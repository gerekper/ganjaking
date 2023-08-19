<?php

namespace ACP\Editing\ApplyFilter;

class ReassignUser
{

    /**
     * Reassign posts and links to new User ID.
     */
    public function apply_filters(?int $user_id = null): ?int
    {
        $user_id = apply_filters('acp/delete/reassign_user', $user_id);

        return $user_id && is_numeric($user_id)
            ? (int)$user_id
            : null;
    }

}