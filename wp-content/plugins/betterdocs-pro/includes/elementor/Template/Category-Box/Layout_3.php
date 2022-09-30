<?php

/**
 * Template Name: Layout 3
 *
 */

$term_permalink =  BetterDocs_Helper::term_permalink('doc_category', $term->slug);
echo '<a href="' . esc_url($term_permalink) . '" class="docs-single-cat-wrap">';
$cat_icon_id = get_term_meta($term->term_id, 'doc_category_image-id', true);
if($settings['show_icon']){
    if ($cat_icon_id) {
        echo wp_get_attachment_image($cat_icon_id, 'thumbnail');
    } else {
        echo '<img class="docs-cat-icon" src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="">';
    }
}
    echo '<div class="title-count">';
if( $settings['show_title'] ) {
    echo '<'.BetterDocs_Elementor::elbd_validate_html_tag($settings['title_tag']).' class="docs-cat-title">' . $term->name . '</'.BetterDocs_Elementor::elbd_validate_html_tag($settings['title_tag']).'>';
}

if ($settings['listview-show-description'] == true) {
    echo '<p class="cat-description">' . $term->description . '</p>';
}
if ($settings['show_count'] == true) {
    if ($term->count == 1) {
        echo wp_sprintf('<span>%s %s</span>', $term->count, __('article', 'betterdocs-pro'));
    } else {
        echo wp_sprintf('<span>%s %s</span>', $term->count, __('articles', 'betterdocs-pro'));
    }
}
echo '</div>
</a>';