<?php

namespace ACP\Export\Strategy;

use AC\ListScreen;
use AC\ListTable;
use AC\ListTableFactory;
use ACP\Export\Strategy;
use WP_Comment_Query;

class Comment extends Strategy
{

    public function __construct(ListScreen\Comment $list_screen)
    {
        parent::__construct($list_screen);
    }

    protected function get_list_table(): ?ListTable
    {
        return (new ListTableFactory())->create_from_globals();
    }

    protected function ajax_export(): void
    {
        add_action('parse_comment_query', [$this, 'comments_query'], PHP_INT_MAX - 100);
    }

    /**
     * Catch the comments query and run it with altered parameters for pagination. This should be
     * attached to the parse_comment_query hook when an AJAX request is sent
     *
     * @param $query
     *
     * @see   action:pre_get_posts
     * @since 1.0
     */
    public function comments_query($query): void
    {
        if ($query->query_vars['count']) {
            return;
        }

        remove_action('parse_comment_query', [$this, __FUNCTION__], PHP_INT_MAX - 100);

        $per_page = $this->get_num_items_per_iteration();

        $query->query_vars['offset'] = $this->get_export_counter() * $per_page;
        $query->query_vars['number'] = $per_page;
        $query->query_vars['fields'] = 'ids';

        $ids = $this->get_requested_ids();

        if ($ids) {
            $query->query_vars['comment__in'] = isset($query->query_vars['comment__in'])
                ? array_merge($ids, (array)$query->query_vars['comment__in'])
                : $ids;
        }

        $modified_query = new WP_Comment_Query($query->query_vars);
        $comments = $modified_query->get_comments();

        $this->export($comments);
    }

}