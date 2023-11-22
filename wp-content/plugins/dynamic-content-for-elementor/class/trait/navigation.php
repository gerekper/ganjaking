<?php

namespace DynamicContentForElementor;

use Elementor\Icons_Manager;
trait Navigation
{
    public static function numeric_posts_nav()
    {
        if (is_singular()) {
            return;
        }
        global $wp_query;
        /** Stop execution if there's only 1 page */
        if ($wp_query->max_num_pages <= 1) {
            return;
        }
        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
        $max = \intval($wp_query->max_num_pages);
        $links = [];
        //phpstan
        $prev_arrow = is_rtl() ? 'fa fa-angle-right' : 'fa fa-angle-left';
        $next_arrow = is_rtl() ? 'fa fa-angle-left' : 'fa fa-angle-right';
        /** Add current page to the array */
        if ($paged >= 1) {
            $links[] = $paged;
        }
        /** Add the pages around the current page to the array */
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }
        if ($paged + 2 <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }
        echo '<div class="navigation posts-navigation"><ul class="page-numbers">' . "\n";
        /** Previous Post Link */
        if (get_previous_posts_link()) {
            \printf('<li>%s</li>' . "\n", get_previous_posts_link());
        }
        /** Link to first page, plus ellipses if necessary */
        if (!\in_array(1, $links)) {
            $class = 1 == $paged ? ' class="current"' : '';
            \printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');
            if (!\in_array(2, $links)) {
                echo '<li>…</li>';
            }
        }
        /** Link to current page, plus 2 pages in either direction if necessary */
        \sort($links);
        foreach ((array) $links as $link) {
            $class = $paged == $link ? ' class="current"' : '';
            \printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
        }
        /** Link to last page, plus ellipses if necessary */
        if (!\in_array($max, $links)) {
            if (!\in_array($max - 1, $links)) {
                echo '<li>…</li>' . "\n";
            }
            $class = $paged == $max ? ' class="current"' : '';
            \printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
        }
        /** Next Post Link */
        if (get_next_posts_link()) {
            \printf('<li>%s</li>' . "\n", get_next_posts_link());
        }
        echo '</ul></div>' . "\n";
    }
    /* -------------------- */
    // Search and Filter Pro - Navigation
    public static function get_wp_link_page_sf($i)
    {
        return get_pagenum_link($i);
    }
    public static function get_wp_link_page($i)
    {
        if (!is_singular() || is_front_page()) {
            return get_pagenum_link($i);
        }
        // Based on wp-includes/post-template.php:957 `_wp_link_page`.
        global $wp_rewrite;
        $id_page = \DynamicContentForElementor\Helper::get_the_id();
        $post = get_post();
        $query_args = [];
        $url = get_permalink($id_page);
        if ($i > 1) {
            if ('' === get_option('permalink_structure') || \in_array($post->post_status, ['draft', 'pending'])) {
                $url = add_query_arg('page', $i, $url);
            } elseif (get_option('show_on_front') === 'page' && (int) get_option('page_on_front') === $post->ID) {
                $url = trailingslashit($url) . user_trailingslashit("{$wp_rewrite->pagination_base}/" . $i, 'single_paged');
            } else {
                $url = trailingslashit($url) . user_trailingslashit($i, 'single_paged');
            }
        }
        if (is_preview()) {
            if ('draft' !== $post->post_status && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
                $query_args['preview_id'] = sanitize_text_field(wp_unslash($_GET['preview_id']));
                $query_args['preview_nonce'] = sanitize_text_field(wp_unslash($_GET['preview_nonce']));
            }
            $url = get_preview_post_link($post, $query_args, $url);
        }
        return $url;
    }
    public static function get_next_pagination()
    {
        $paged = \max(1, get_query_var('paged'), get_query_var('page'));
        if (empty($paged)) {
            $paged = 1;
        }
        $link_next = self::get_wp_link_page($paged + 1);
        return $link_next;
    }
    // Next Pagination for Search&Filter Pro
    public static function get_next_pagination_sf()
    {
        $paged = \max(1, get_query_var('paged'));
        if (empty($paged)) {
            $paged = 1;
        }
        $link_next = self::get_wp_link_page_sf($paged + 1);
        return $link_next;
    }
    /**
     * @param array<string,mixed> $settings
     * @param string $key
     * @return array{left: string|false, right: string|false}
     */
    public static function get_leftright_icon($settings, $key)
    {
        $old_key = $key;
        $new_key = 'selected_' . $key;
        $migration_allowed = Icons_Manager::is_migration_allowed();
        // old default
        if (!isset($settings[$old_key]) && !$migration_allowed) {
            $settings[$old_key] = 'fa fa-long-arrow-right';
        }
        $migrated = isset($settings['__fa4_migrated'][$new_key]);
        $is_new = empty($settings[$old_key]) && $migration_allowed;
        $icon = ['right' => '', 'left' => ''];
        if ($migrated || $is_new) {
            \ob_start();
            Icons_Manager::render_icon($settings[$new_key] ?? '', ['aria-hidden' => 'true']);
            $icon['right'] = \ob_get_clean();
            $left = \str_replace('right', 'left', $settings[$new_key] ?? '');
            \ob_start();
            Icons_Manager::render_icon($left, ['aria-hidden' => 'true']);
            $icon['left'] = \ob_get_clean();
        } else {
            $prefix = \str_replace('right', '', $settings[$old_key]);
            $icon['left'] = "<i class='{$prefix}left'></i>";
            $icon['right'] = "<i class='{$prefix}right'></i>";
        }
        return $icon;
    }
    /**
     *  Numeric Query Pagination
     *
     * @param int|string $pages
     * @param array<mixed> $settings
     * @param string $class
     * @param bool $rtl
     * @return void
     */
    public static function numeric_query_pagination($pages, $settings, $class = '', $rtl = \false)
    {
        $search_filter_query = \false;
        if (isset($settings['query_type']) && $settings['query_type'] === 'search_filter') {
            $search_filter_query = \true;
        }
        $icon_prevnext = self::get_leftright_icon($settings, 'pagination_icon_prevnext');
        $icon_firstlast = self::get_leftright_icon($settings, 'pagination_icon_firstlast');
        $range = (int) $settings['pagination_range'] - 1;
        // The numbers displayed at a time
        $showitems = $range * 2 + 1;
        $paged = \max(1, get_query_var('paged'), get_query_var('page'));
        if (empty($paged)) {
            $paged = 1;
        }
        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }
        if ($pages !== 1) {
            if ($class) {
                if (!$rtl) {
                    echo '<div class="dce-pagination ' . $class . '">';
                } else {
                    echo '<div class="dce-pagination ' . $class . '" dir="rtl">';
                }
            } else {
                if (!$rtl) {
                    echo '<div class="dce-pagination">';
                } else {
                    echo '<div class="dce-pagination" dir="rtl">';
                }
            }
            // Progression
            if ($settings['pagination_show_progression']) {
                echo '<span class="progression">' . $paged . ' / ' . $pages . '</span>';
            }
            // First
            if ($settings['pagination_show_firstlast']) {
                if ($paged > 2 && $paged > $range + 1 && $showitems < $pages) {
                    $link = $search_filter_query ? self::get_wp_link_page_sf(1) : self::get_wp_link_page(1);
                    echo '<a href="' . $link . '" class="pagefirst">' . $icon_firstlast['left'] . ' ' . wp_kses_post($settings['pagination_first_label']) . '</a>';
                }
            }
            // Prev
            if ($settings['pagination_show_prevnext']) {
                if ($paged > 1 && $showitems < $pages) {
                    $link = $search_filter_query ? self::get_wp_link_page_sf($paged - 1) : self::get_wp_link_page($paged - 1);
                    echo '<a href="' . $link . '" class="pageprev">' . $icon_prevnext['left'] . ' ' . wp_kses_post($settings['pagination_prev_label']) . '</a>';
                }
            }
            // Numbers
            if ($settings['pagination_show_numbers']) {
                for ($i = 1; $i <= $pages; $i++) {
                    if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                        $link = $search_filter_query ? self::get_wp_link_page_sf($i) : self::get_wp_link_page($i);
                        echo $paged == $i ? '<span class="current">' . $i . '</span>' : "<a href='" . $link . "' class=\"inactive\">" . $i . '</a>';
                    }
                }
            }
            // Next
            if ($settings['pagination_show_prevnext']) {
                if ($paged < $pages && $showitems < $pages) {
                    $link = $search_filter_query ? self::get_wp_link_page_sf($paged + 1) : self::get_wp_link_page($paged + 1);
                    echo '<a href="' . $link . '" class="pagenext">' . wp_kses_post($settings['pagination_next_label']) . ' ' . $icon_prevnext['right'] . '</a>';
                }
            }
            // Last
            if ($settings['pagination_show_firstlast']) {
                if ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) {
                    $link = $search_filter_query ? self::get_wp_link_page_sf($pages) : self::get_wp_link_page($pages);
                    echo '<a href="' . $link . '" class="pagelast">' . wp_kses_post($settings['pagination_last_label']) . ' ' . $icon_firstlast['right'] . '</a>';
                }
            }
            echo '</div>';
        }
    }
}
