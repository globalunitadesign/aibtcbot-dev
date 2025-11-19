<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Asset;
use App\Models\AssetTransfer;

class ChartService
{
    public array $data = [];
    public ?string $mode = null;
    public int $is_admin = 0;

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function setIsAdmin(int $isAdmin): self
    {
        $this->is_admin = $isAdmin;
        return $this;
    }

    public function getChartData(int $member_id): array
    {
        $root = Member::with('user', 'avatar', 'children')->find($member_id);

        if (!$root) {
            return [];
        }

        $this->data[] = $this->writeNodeData($root);

        $this->addChildrenData($root);

        return $this->data;
    }

    protected function addChildrenData(Member $parentMember): void
    {
        $children = $parentMember->children()
            ->with('user', 'avatar', 'children')
            ->get();

        if ($children->isEmpty()) return;

        foreach ($children as $child) {
            $this->data[] = $this->writeNodeData($child, $parentMember->id);

            if ($this->mode === 'aff') {
                $this->addChildrenData($child);
            }
        }
    }

    protected function writeNodeData(Member $member, $parent = null): array
    {
        if ($member->user) {
            $info = "<i>U{$member->user->id}</i> <br> <i>{$member->user->name}</i>";
        } elseif ($member->avatar) {
            $info = "<i>A{$member->avatar->id}</i> <br> <i>{$member->avatar->name}</i>";
        } else {
            $info = "Unknown <br>";
        }

        /*
        if ($member->user) {
            $assets = Asset::where('member_id', $member->id)
                ->whereHas('coin', fn($q) => $q->where('is_active', 'y'))
                ->get();

            foreach ($assets as $asset) {
                $sales = AssetTransfer::where('asset_id', $asset->id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

                $info .= "{$asset->coin->code}: {$sales} <br>";
            }
        }
        */

        return [
            'id'     => (string) $member->id,
            'parent' => $parent ? (string) $parent : null,
            'info'   => $info,
        ];
    }
}
