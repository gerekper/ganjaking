<?php

namespace ACP\Column\Post;

use AC;
use AC\View;
use ACP\Sorting;

class Revisions extends AC\Column
    implements AC\Column\AjaxValue, Sorting\Sortable
{

    public function __construct()
    {
        $this->set_type('column-revisions');
        $this->set_label(__('Revisions', 'codepress-admin-columns'));
    }

    public function get_value($post_id)
    {
        $post_id = (int)$post_id;

        $count = $this->get_revision_count($post_id);

        if ($count < 1) {
            return $this->get_empty_char();
        }

        return ac_helper()->html->get_ajax_modal_link(
            sprintf(_n('%d revision', '%d revisions', $count, 'codepress-admin-columns'), $count),
            [
                'title'     => get_the_title($post_id),
                'edit_link' => get_edit_post_link($post_id),
                'id'        => $post_id,
            ]
        );
    }

    public function get_raw_value($post_id)
    {
        return $this->get_revision_count((int)$post_id);
    }

    private function get_revision_count(int $post_id): int
    {
        return count(wp_get_post_revisions($post_id, ['posts_per_page' => -1, 'fields' => 'ids']));
    }

    private function get_revisions(int $post_id): array
    {
        return wp_get_post_revisions($post_id, ['posts_per_page' => 30]);
    }

    public function get_ajax_value($post_id): string
    {
        $post_id = (int)$post_id;

        $count = $this->get_revision_count($post_id);

        $view = new View([
            'title'     => sprintf(_n('%d revision', '%d revisions', $count, 'codepress-admin-columns'), $count),
            'revisions' => $this->get_revisions($post_id),
        ]);

        return $view->set_template('modal-value/revisions')
                    ->render();
    }

    public function is_valid(): bool
    {
        return post_type_supports($this->get_post_type(), 'revisions');
    }

    public function sorting()
    {
        return new Sorting\Model\Post\Revisions();
    }

}