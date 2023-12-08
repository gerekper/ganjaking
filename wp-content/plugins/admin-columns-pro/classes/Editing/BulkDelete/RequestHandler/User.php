<?php

namespace ACP\Editing\BulkDelete\RequestHandler;

use ACP\Editing\BulkDelete\RequestHandler;
use RuntimeException;

class User extends RequestHandler
{

    protected function delete($id, array $args = []): void
    {
        $id = (int)$id;

        if ( ! current_user_can('delete_users') || ! current_user_can('delete_user', $id)) {
            throw new RuntimeException(
                __('You do not have permissions to delete this user.', 'codepress-admin-columns')
            );
        }

        if (get_current_user_id() === $id) {
            throw new RuntimeException(__('The current user can not be deleted.', 'codepress-admin-columns'));
        }

        $reassign_user = isset($args['reassign_user']) && is_numeric($args['reassign_user'])
            ? (int)$args['reassign_user']
            : null;

        if ($reassign_user === $id) {
            throw new RuntimeException(__('The assigned user can not be deleted.', 'codepress-admin-columns'));
        }

        $result = wp_delete_user($id, $reassign_user);

        if (false === $result) {
            throw new RuntimeException(__('User does not exists.', 'codepress-admin-columns'));
        }
    }

}