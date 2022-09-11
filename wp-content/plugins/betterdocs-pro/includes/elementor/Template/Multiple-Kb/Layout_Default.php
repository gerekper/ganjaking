<?php
/**
 * Template Name: Default
 *
 */


echo '<a href="' . get_term_link($term->slug, 'knowledge_base') . '" class="el-betterdocs-category-box-post">
    <div class="el-betterdocs-cb-inner">';

    if ($settings['show_icon']) {
        $cat_icon_id = get_term_meta($term->term_id, 'knowledge_base_image-id', true);
        if ($cat_icon_id) {
            $cat_icon = wp_get_attachment_image($cat_icon_id, 'thumbnail');
            // $cat_icon = wp_get_attachment_image($cat_icon_id, 'thumbnail', ['alt' => esc_attr(get_post_meta($cat_icon_id, '_wp_attachment_image_alt', true))]);
        } else {
            $cat_icon = '<img src="' . BETTERDOCS_ADMIN_URL . 'assets/img/betterdocs-cat-icon.svg" alt="betterdocs-category-box-icon">';
        }

        echo '<div class="el-betterdocs-cb-cat-icon">' . $cat_icon . '</div>';
    }

    if ($settings['show_title']) {
        echo '<' . BetterDocs_Elementor::elbd_validate_html_tag($settings['title_tag']) . ' class="el-betterdocs-cb-cat-title">' . $term->name . '</' . BetterDocs_Elementor::elbd_validate_html_tag($settings['title_tag']) . '>';
    }

    if ($settings['show_count']) {
        if($term->count == 1) {
            printf('<div class="el-betterdocs-cb-cat-count"><span class="count-prefix">%s</span>%s<span class="count-suffix">%s</span></div>', esc_html($settings['count_prefix']), $term->count, esc_html($settings['count_suffix_singular']));
        } else {
            printf('<div class="el-betterdocs-cb-cat-count"><span class="count-prefix">%s</span>%s<span class="count-suffix">%s</span></div>', esc_html($settings['count_prefix']), $term->count, esc_html($settings['count_suffix']));
        }
    }

    echo '</div>
</a>';
