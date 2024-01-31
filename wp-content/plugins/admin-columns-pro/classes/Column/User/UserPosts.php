<?php

namespace ACP\Column\User;

use AC;
use AC\View;
use ACP;
use ACP\Export\Exportable;
use ACP\Search;
use ACP\Sorting;
use ACP\Sorting\Sortable;

class UserPosts extends AC\Column implements Sortable, AC\Column\AjaxValue, Exportable, Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-user_posts')
             ->set_label(__('Posts by Author', 'codepress-admin-columns'));
    }

    public function get_value($id)
    {
        $id = (int)$id;

        $count = $this->get_post_count($id);

        if ($count < 1) {
            return $this->get_empty_char();
        }

        return ac_helper()->html->get_ajax_modal_link(
            number_format_i18n($count),
            [
                'title' => ac_helper()->user->get_display_name($id),
                'id'    => $id,
                'class' => '-w-large',
            ]
        );
    }

    private function get_post_count(int $user_id): int
    {
        return ac_helper()->post->count_user_posts(
            $user_id,
            $this->get_selected_post_types(),
            $this->get_selected_post_status()
        );
    }

    public function get_ajax_value($user_id)
    {
        $user_id = (int)$user_id;

        $count = $this->get_post_count($user_id);

        if ($count < 1) {
            return __('No items', 'codepress-admin-columns');
        }

        $posts = [];

        $limit = 30;

        foreach ($this->get_recent_posts($user_id, $limit) as $post) {
            $post_title = strip_tags($post->post_title) ?: $post->ID;
            $edit_link = get_edit_post_link($post->ID);

            if ($edit_link) {
                $post_title = sprintf(
                    '<a href="%s">%s</a>',
                    $edit_link,
                    $post_title
                );
            }

            $post_type = get_post_type_object($post->post_type);

            if ($post_type) {
                $post_type = $post_type->labels->singular_name;
            }

            $posts[] = [
                'id'         => $post->ID,
                'post_type'  => $post_type,
                'post_title' => $post_title,
                'post_date'  => ac_helper()->date->date($post->post_date),
            ];
        }

        $view = new View([
            'title'      => __('Recent items', 'codepress-admin-columns'),
            'posts'      => $posts,
            'post_types' => $this->get_post_count_per_post_type($user_id),
        ]);

        return $view->set_template('modal-value/user-posts')
                    ->render();
    }

    private function get_post_count_per_post_type(int $user_id): array
    {
        $post_types = [];

        foreach ($this->get_selected_post_types() as $post_type) {
            $count = ac_helper()->post->count_user_posts($user_id, [$post_type], $this->get_selected_post_status());

            if ($count > 0) {
                $post_types[] = [
                    'link'      => $this->get_post_table_link($user_id, $post_type),
                    'post_type' => get_post_type_object($post_type)->labels->singular_name,
                    'count'     => number_format_i18n($count),
                ];
            }
        }

        return $post_types;
    }

    private function get_post_table_link(int $user_id, string $post_type): string
    {
        return add_query_arg(
            [
                'post_type' => $post_type,
                'author'    => $user_id,
            ],
            admin_url('edit.php')
        );
    }

    private function get_selected_post_types(): array
    {
        $post_type = (string)$this->get_setting('post_type')->get_post_type();

        if ('any' === $post_type) {
            // All post types, including the ones that are marked "exclude from search"
            return get_post_types(['show_ui' => true]);
        }

        if (post_type_exists($post_type)) {
            return [$post_type];
        }

        return [];
    }

    private function get_recent_posts(int $user_id, int $limit = null): array
    {
        return get_posts([
            'author'         => $user_id,
            'post_type'      => $this->get_selected_post_types(),
            'posts_per_page' => $limit ?: -1,
            'post_status'    => $this->get_selected_post_status(),
        ]);
    }

    protected function get_selected_post_status(): array
    {
        $post_status = $this->get_setting('post_status')->get_value();

        if ('' === $post_status) {
            return get_post_stati(['internal' => 0]);
        }

        return $post_status;
    }

    public function get_raw_value($user_id)
    {
        return $this->get_post_count($user_id);
    }

    public function export()
    {
        return new ACP\Export\Model\User\UserPosts($this->get_selected_post_types(), $this->get_selected_post_status());
    }

    public function sorting()
    {
        return new Sorting\Model\User\PostCount($this->get_selected_post_types(), $this->get_selected_post_status());
    }

    public function search()
    {
        return new ACP\Search\Comparison\User\UserPosts(
            $this->get_selected_post_types(),
            $this->get_selected_post_status()
        );
    }

    protected function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\PostType($this, true));
        $this->add_setting(new AC\Settings\Column\PostStatus($this));
    }

}