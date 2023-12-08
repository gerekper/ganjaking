<?php

namespace ACP\Editing\Storage\User;

use ACP\Editing\Storage;

class DisplayName implements Storage
{

    public function get($id): string
    {
        return (string)ac_helper()->user->get_user_field('display_name', $id);
    }

    public function update(int $id, $data): bool
    {
        global $wpdb;

        $data = sanitize_user($data, true);

        $result = $wpdb->update(
            $wpdb->users,
            ['display_name' => $data],
            ['ID' => $id],
            ['%s'],
            ['%d']
        );

        clean_user_cache($id);

        return $result !== false;
    }

}