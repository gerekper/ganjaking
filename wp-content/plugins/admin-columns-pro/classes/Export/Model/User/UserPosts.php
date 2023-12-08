<?php

declare(strict_types=1);

namespace ACP\Export\Model\User;

use ACP;

class UserPosts implements ACP\Export\Service
{

    private $post_types;

    private $post_stati;

    public function __construct(array $post_types, array $post_stati)
    {
        $this->post_types = $post_types;
        $this->post_stati = $post_stati;
    }

    public function get_value($id)
    {
        return ac_helper()->post->count_user_posts((int)$id, $this->post_types, $this->post_stati);
    }

}