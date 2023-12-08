<?php

declare(strict_types=1);

namespace ACP\Helper\Select\PostType\GroupFormatter;

use ACP\Helper\Select\PostType\GroupFormatter;
use WP_Post_Type;

class Type implements GroupFormatter
{

    public function format(WP_Post_Type $post_type): string
    {
        if ($post_type->show_ui || $post_type->show_in_menu || $post_type->show_in_admin_bar) {
            return _x('Public', 'post_types', 'codepress-admin-columns');
        }

        return _x('Hidden', 'post_types', 'codepress-admin-columns');
    }

}