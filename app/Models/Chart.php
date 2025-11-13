<?php

namespace App\Models;

use App\Models\Member;
use App\Models\User;
use App\Models\Avatar;
use App\Models\Asset;
use App\Models\AssetTransfer;
use Illuminate\Support\Facades\DB;

class Chart
{
    public $data = [];
    public $mode;
    public $is_admin = 0;


    public function getChartData($member_id)
    {
        $root = Member::with('user', 'avatar', 'children')->find($member_id);

        if (!$root) {
            return [];
        }

        $this->data[] = $this->writeNodeData($root);

        $this->addChildrenData($root);

        return $this->data;
    }


    protected function addChildrenData(Member $parentMember)
    {

        $children = $parentMember->children()->with('user', 'avatar', 'children')->get();

        if ($children->isEmpty()) return;

        foreach ($children as $child) {
            $this->data[] = $this->writeNodeData($child, $parentMember->id);

            if ($this->mode == 'aff') {
                $this->addChildrenData($child);
            }
        }
    }


    protected function writeNodeData(Member $member, $parent = null)
    {
        $userOrAvatar = $member->user ?? $member->avatar;

        if ($member->user) {
            $info = "<i>U{$member->user->id}</i> <br> <i>{$member->user->name}</i>";
        } else if ($member->avatar) {
            $info = "<i>A{$member->avatar->id}</i> <br> <i>{$member->avatar->name}</i>";
        } else {
            $info = "Unknown <br>";
        }

        if ($member->user) {
            $assets = Asset::where('member_id', $member->id)
                ->whereHas('coin', fn($q) => $q->where('is_active', 'y'))
                ->get();

            /*
            foreach ($assets as $asset) {
                $sales = AssetTransfer::where('asset_id', $asset->id)
                    ->whereIn('type', ['deposit', 'internal', 'manual_deposit'])
                    ->where('status', 'completed')
                    ->sum(fn($deposit) => (float) $deposit->getAmountInUsdt());

                $info .= "{$asset->coin->code}: {$sales} <br>";
            }
            */
        }

        return [
            'id' => strval($member->id),
            'parent' => strval($parent),
            'info' => $info,
        ];
    }
}
