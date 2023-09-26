<?php

declare(strict_types=1);

namespace ACP\Sorting\Strategy;

trait PostResultsTrait
{

    /**
     * For backwards compatibility we need the method `get_results()`
     * @depecated 6.3
     */
    public function get_results(array $args = []): array
    {
        _deprecated_function(__METHOD__, '6.3');

        global $wp_query;

        if ( ! $wp_query) {
            return [];
        }

        $vars = $wp_query->vars ?? [];

        if (empty($vars['post_status'])) {
            $vars['post_status'] = ['any'];
        }

        if (isset($vars['orderby'])) {
            $vars['orderby'] = false;
        }

        $vars['post_status'] = apply_filters('acp/sorting/post_status', $vars['post_status'], $this);
        $vars['no_found_rows'] = 1;
        $vars['fields'] = 'ids';
        $vars['posts_per_page'] = -1;
        $vars['order'] = 'ASC';
        $vars['posts_per_archive_page'] = '';
        $vars['nopaging'] = true;

        $args = array_merge($args, $vars);

        return get_posts($args);
    }

}