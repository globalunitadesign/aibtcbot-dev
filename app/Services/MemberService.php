<?php

namespace App\Services;


use App\Models\Asset;
use App\Models\Coin;
use App\Models\Income;
use App\Models\Member;
use App\Models\User;
use App\Models\Avatar;

class MemberService
{
    /**
     *
     * @param int $id
     * @param string $type 'user' or 'avatar'
     * @param int|null $root_id
     * @return Member
     * @throws \Exception
     */
    public function addMember(int $id, string $type = 'user', int $root_id = null)
    {
        $root_id = $root_id ?? 1;
        $root_node = $this->buildMemberTree($root_id);
        $position_data = $this->findAvailableParentInTree($root_node);

        if (!$position_data) {
            throw new \Exception('No available position found in the tree.');
        }

        $member = Member::create([
            'user_id'   => $type === 'user' ? $id : null,
            'avatar_id' => $type === 'avatar' ? $id : null,
            'parent_id' => $position_data['parent_id'],
            'position'  => $position_data['position'],
            'referrer_id' => $root_id,
            'level'     => $position_data['level'],
            'is_valid' => $type === 'user' ? 'n' : 'y',
        ]);

        $coins = Coin::pluck('id');

        foreach($coins as $id) {
            Asset::create([
                'member_id' => $member->id,
                'coin_id' => $id,
            ]);

            Income::create([
                'member_id' => $member->id,
                'coin_id' => $id,
            ]);
        }
    }

    /**
     *
     * @param User $root
     * @return Member
     */
    public function addAvatar(User $root)
    {
        $avatar_count = Avatar::where('owner_id', $root->id)->count() + 1;

        $avatar = Avatar::create([
            'owner_id' => $root->id,
            'name'     => $root->id . '_' .$avatar_count,
        ]);

        return $avatar;
    }

    /**
     *
     * @param int $onwer_id
     * @param int $target_id
     * @return bool
     */
    public function hasMemberInTree(int $owner_id, int $target_id)
    {
        $tree = $this->buildMemberTree($owner_id);
        return $this->searchTree($tree, $target_id);
    }

    /**
     * Parse Member code.
     *
     *
     * @return Array
     */
    public function memberParseCode(string $code)
    {
        $prefix = mb_substr($code, 0, 1);
        $number = mb_substr($code, 1);

        if ($prefix === 'A') {
            $type = 'avatar';
        } else {
            $type = 'user';
        }

        return ['type' => $type, 'id' => $number];
    }

    /**
     *
     * @param int $root_id
     * @return array
     */
    private function buildMemberTree(int $root_id)
    {
        $member = Member::with('children')->find($root_id);

        if (!$member) {
            throw new \Exception("Member not found");
        }

        $tree = [
            'id' => $member->id,
            'parent_id' => $member->parent_id,
            'position' => $member->position,
            'level' => $member->level,
            'children' => [],
        ];

        foreach ($member->children as $child) {
            $tree['children'][] = $this->buildMemberTree($child->id);
        }

        return $tree;
    }

    /**
     *
     * @param array $rootNode
     * @return array|null
     */
    private function findAvailableParentInTree(array $root_node)
    {
        $queue = [$root_node];

        while (!empty($queue)) {
            $current = array_shift($queue);

            $left = null;
            $right = null;

            foreach ($current['children'] as $child) {
                if ($child['position'] === 'left') $left = $child;
                if ($child['position'] === 'right') $right = $child;
            }

            if (!$left) {
                return ['parent_id' => $current['id'], 'position' => 'left', 'level' => $current['level'] + 1];
            }

            if (!$right) {
                return ['parent_id' => $current['id'], 'position' => 'right', 'level' => $current['level'] + 1];
            }

            foreach ($current['children'] as $child) {
                $queue[] = $child;
            }
        }

        return null;
    }

    /**
     *
     * @param array $tree
     * @param int $target_id
     * @return bool
     */
    private function searchTree(array $tree, int $target_id)
    {
        if ($tree['id'] === $target_id) {
            return true;
        }

        foreach ($tree['children'] as $child) {
            if ($this->searchTree($child, $target_id)) {
                return true;
            }
        }

        return false;
    }
}
