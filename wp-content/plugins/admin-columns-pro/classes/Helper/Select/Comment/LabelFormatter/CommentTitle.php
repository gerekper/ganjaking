<?php

declare(strict_types=1);

namespace ACP\Helper\Select\Comment\LabelFormatter;

use ACP\Helper\Select\Comment\LabelFormatter;
use DateTime;
use WP_Comment;

class CommentTitle implements LabelFormatter
{

    public function format_label(WP_Comment $comment): string
    {
        $date = new DateTime($comment->comment_date);

        $value = array_filter([
            $comment->comment_author_email,
            $date->format('M j, Y H:i'),
        ]);

        return sprintf(
            '#%s %s',
            $comment->comment_ID,
            implode(' / ', $value)
        );
    }

    public function format_label_unique(WP_Comment $comment): string
    {
        return sprintf('%s (%s)', $this->format_label($comment), $comment->comment_ID);
    }

}