<?php

namespace ACP\Editing\Service\User;

use AC\Helper\Select\Options;
use ACP\Editing\RemoteOptions;
use ACP\Editing\Service\BasicStorage;
use ACP\Editing\Storage;
use ACP\Editing\View;

class DisplayName extends BasicStorage implements RemoteOptions
{

    public function __construct()
    {
        parent::__construct(new Storage\User\DisplayName());
    }

    public function get_view(string $context): ?View
    {
        if ($context === self::CONTEXT_BULK) {
            return null;
        }

        return new View\RemoteSelect();
    }

    public function get_remote_options(int $id = null): Options
    {
        $user = get_userdata($id);

        $options = [
            $user->nickname,
            $user->user_login,
        ];

        if ($user->first_name) {
            $options[] = $user->first_name;
        }

        if ($user->last_name) {
            $options[] = $user->last_name;
        }

        if ($user->first_name && $user->last_name) {
            $options[] = sprintf(
                '%s %s',
                $user->first_name,
                $user->last_name
            );
            $options[] = sprintf(
                '%s %s',
                $user->last_name,
                $user->first_name
            );
        }

        if ( ! in_array($user->display_name, $options, true)) {
            $options[] = $user->display_name;
        }

        $options = array_combine($options, $options);

        return Options::create_from_array($options);
    }

}