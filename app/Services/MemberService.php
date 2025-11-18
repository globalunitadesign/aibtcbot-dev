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
     * @param int $target_id
     * @param int $max_level
     * @return array
     */
    public function getParentTree(int $target_id, int $max_level = 20)
    {
        $target = Member::find($target_id);
        if (!$target) {
            throw new \Exception("Target member not found");
        }

        $root_id = 5000011;

        $tree = $this->buildMemberTree($root_id);

        $parents = [];
        $this->findParentsInTree($tree, $target_id, $parents);

        return array_slice($parents, 0, $max_level, true);
    }

    /**
     *
     * @param int $root_id
     * @param int $max_level
     * @return array
     */
    public function getChildrenTree(int $root_id, int $max_level = 20)
    {
        $tree = $this->buildMemberTree($root_id);

        $levels = [];
        $queue = [$tree];
        $level = 1;

        while (!empty($queue) && $level <= $max_level) {
            $nextQueue = [];
            $currentLevelMembers = [];

            foreach ($queue as $node) {
                foreach ($node['children'] as $child) {
                    $currentLevelMembers[] = $child['member'];
                    $nextQueue[] = $child;
                }
            }

            if (empty($currentLevelMembers)) break;

            $levels[$level] = collect($currentLevelMembers);
            $queue = $nextQueue;
            $level++;
        }

        return $levels;
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
            'member' => $member,
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
                if ($child['member']->position === 'left') $left = $child;
                if ($child['member']->position === 'right') $right = $child;
            }

            if (!$left) {
                return ['parent_id' => $current['id'], 'position' => 'left', 'level' => $current['member']->level + 1];
            }

            if (!$right) {
                return ['parent_id' => $current['id'], 'position' => 'right', 'level' => $current['member']->level + 1];
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

    private function findParentsInTree($node, $target_id, &$parents, $path = [])
    {
        $path[] = $node['member'];

        if ($node['id'] == $target_id) {
            for ($i = 0; $i < count($path) - 1; $i++) {
                $parents[$i + 1] = $path[$i];
            }

            return true;
        }

        foreach ($node['children'] as $child) {
            if ($this->findParentsInTree($child, $target_id, $parents, $path)) {
                return true;
            }
        }

        return false;
    }
}
