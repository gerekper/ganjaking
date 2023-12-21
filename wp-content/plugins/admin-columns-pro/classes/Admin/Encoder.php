<?php

declare(strict_types=1);

namespace ACP\Admin;

use AC\ListScreen;
use ACP\Type\Url\Preview;
use WP_User;

class Encoder
{

    private $list_screen;

    public function __construct(ListScreen $list_screen)
    {
        $this->list_screen = $list_screen;
    }

    public function encode(): array
    {
        return [
            'id'                     => $this->list_screen->has_id() ? (string)$this->list_screen->get_id() : '',
            'title'                  => $this->list_screen->get_title() ?: $this->list_screen->get_label(),
            'read_only'              => $this->list_screen->is_read_only(),
            'edit_url'               => (string)$this->list_screen->get_editor_url(),
            'preview_url'            => (string)new Preview($this->list_screen->get_table_url()),
            'restricted_description' => $this->get_restricted_description(),
        ];
    }

    private function get_restricted_description(): string
    {
        $description = [];

        $roles = $this->list_screen->get_preference('roles');
        $users = $this->list_screen->get_preference('users');

        if ($roles) {
            if (1 === count($roles)) {
                $role = $roles[0];
                $description[] = get_editable_roles()[$role]['name'] ?? $role;
            } else {
                $description[] = __('Roles', 'codepress-admin-columns');
            }
        }
        if ($users) {
            if (1 === count($users)) {
                $user = get_userdata($users[0]);

                if ($user instanceof WP_User) {
                    $description[] = ucfirst((string)ac_helper()->user->get_display_name($user, 'full_name'))
                        ?: __('User', 'codepress-admin-columns');
                }
            } else {
                $description[] = __('Users');
            }
        }

        return implode(' & ', array_filter($description));
    }

}