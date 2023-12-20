<?php
if ( $category_search == true && $category_search !== 'false' ) {
    echo '<select class="betterdocs-search-category">
        <option value="">' . esc_html__( 'All Categories', 'betterdocs-pro' ) . '</option>
        '.betterdocs()->template_helper->term_options('doc_category', '', betterdocs()->settings->get('child_category_exclude')).'
    </select>';
}

if ( $search_button == true && $search_button !== 'false' ) {
    echo '<input class="search-submit" type="submit" value="' . esc_html($search_button_text) . '">';
}

if ( betterdocs()->settings->get('multiple_kb') == 1 && betterdocs()->settings->get('kb_based_search') == 1) {
    $kb_slug = betterdocs_pro()->multiple_kb->get_kb_slug();
    echo '<input type="hidden" value="' . esc_attr($kb_slug) . '" class="betterdocs-search-kbslug betterdocs-search-submit">';
}
