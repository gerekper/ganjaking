<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
ep_add_shortcode([
    'id'             => 'breadcrumbs',
    'callback'       => 'ep_shortcode_breadcrumbs',
    'name'           => __('Breadcrumbs', 'bdthemes-element-pack'),
    'type'           => 'single',
    'atts'           => [],
    'desc'           => __('Show Breadcrumbs', 'bdthemes-element-pack'),
]);

function ep_shortcode_breadcrumbs($align = 'left') {

    $showOnHome  = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter   = '/'; // delimiter between crumbs
    $home        = get_bloginfo('name'); // text for the 'Home' link
    $blog        = get_theme_mod('rooten_blog_title', 'Blog');
    $shop        = get_theme_mod('rooten_woocommerce_title', 'Shop');
    $forums      = get_theme_mod('rooten_bbpress_title', 'Forum');
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $before      = '<li><span href="#">'; // tag before the current crumb
    $after       = '</span></li>'; // tag after the current crumb
    $output      = array();
    $class     = ['bdt-breadcrumb', 'bdt-margin-remove'];
    $class[]     = ($align == 'center') ? 'bdt-flex-center' : null;
    $class       = implode(' ', $class);

    global $post;
    $homeLink = home_url('/');

    global $woocommerce;
    if ($woocommerce) {
        $shopLink = get_permalink(wc_get_page_id('shop'));
    }

    $forumLink = get_post_type_archive_link('forum');

    if (is_home() || is_front_page()) {
        if ($showOnHome == 1) {
            $output[] = '<ul id="epsc-breadcrumb" class="' . $class . '">
        <li><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a></li><li><span> ' . esc_html($blog) . '</span></li></ul>';
        }
    } else {
        $output[] = '<ul id="epsc-breadcrumb" class="' . $class . '"><li><a href="' . esc_url($homeLink) . '">' . esc_html($home) . '</a></li>';

        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $output[] = get_category_parents($thisCat->parent, TRUE, ' ') . '';
            }
            $output[] = $before . esc_html__('Category', 'bdthemes-element-pack') . ': ' . esc_html(single_cat_title('', false)) . '' . $after;
        } elseif (is_search()) {
            $output[] = $before . esc_html__('Search', 'bdthemes-element-pack') . $after;
        } elseif (is_day()) {
            $output[] = '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a></li>';
            $output[] = '<li><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a></li>';
            $output[] = $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            $output[] = '<li><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a></li>';
            $output[] = $before . esc_html(get_the_time('F')) . $after;
        } elseif (is_year()) {
            $output[] = $before . esc_html(get_the_time('Y')) . $after;
        } elseif (class_exists('Woocommerce') && is_shop()) {
            $output[] = '<li><a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a></li>';
        } elseif (class_exists('Woocommerce') && is_product()) {
            $output[] = '<li><a href="' . esc_url($shopLink) . '">' . esc_html($shop) . '</a></li> ' . $before . esc_html(get_the_title()) . $after;
        } elseif (class_exists('bbPress') && is_bbpress()) {
            $output[] = '<li><a href="' . esc_url($forumLink) . '">' . esc_html($forums) . '</a></li> ' . $before . esc_html(get_the_title()) . $after . '</a></li>';
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                if ($showCurrent == 1) {
                    $output[] = ' ' . $before . get_the_title() . $after;
                }
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ');
                if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                $output[] = '<li>' . $cats . '</li>'; // No need to escape here
                if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            $output[] = $before . wp_kses_post($post_type->labels->singular_name) . $after;
        } elseif (is_attachment()) {
            if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
        } elseif (is_page() && $post->post_parent) {
            $parent_id  = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a></li>';
                $parent_id  = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                $output[] = $breadcrumbs[$i]; // No need to escape here
                //if ($i != count($breadcrumbs)-1) $output[] = ' ' . esc_html($delimiter) . ' ';
            }
            if ($showCurrent == 1) $output[] = $before . get_the_title() . $after;
        } elseif (is_tag()) {
            $output[] = $before . esc_html__('Tag', 'bdthemes-element-pack') . ': ' . esc_html(single_tag_title('', false)) . $after;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            $output[] = $before . esc_html__('Articles by', 'bdthemes-element-pack') . ' ' . esc_html($userdata->display_name) . $after;
        } elseif (is_404()) {
            $output[] = $before . esc_html__('Error 404', 'bdthemes-element-pack') . $after;
        }
        if (get_query_var('paged')) {
            $output[] = ' (' . esc_html__('Page', 'bdthemes-element-pack') . ' ' . esc_html(get_query_var('paged')) . ')';
        }
        $output[] = '</ul>';
    }

    $output = implode("\n", $output);

    echo wp_kses_post($output);
}
