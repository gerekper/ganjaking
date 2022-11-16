<?php
$customizer_settings        = function_exists('betterdocs_generate_output_pro') ? betterdocs_generate_output_pro() : '';
$live_search                = BetterDocs_DB::get_settings('live_search') != 'off' ? true : false;
$betterdocs_breadcrumb      = BetterDocs_DB::get_settings('enable_breadcrumb') != 'off' ? true : false;
$live_search_markup         = $live_search ? BetterDocs_Public::search() : '';
$current_category           = get_queried_object() != null ? get_queried_object() : '';
$nested_subcategory         = BetterDocs_DB::get_settings('nested_subcategory') != 'off' ? true : false;
$multiple_kb		        = BetterDocs_DB::get_settings('multiple_kb') != 'off' ? true : false;
$term_count	                = isset( $current_category->count ) ? $current_category->count : '';
$term_doc_count	            = betterdocs_get_postcount( $term_count, $current_category->term_id, $nested_subcategory );
$term_doc_count	            = apply_filters('betterdocs_postcount', $term_doc_count, $multiple_kb, $current_category->term_id, $current_category->slug, $term_count, $nested_subcategory);
$list_args 		            = apply_filters( 'betterdocs_articles_args', BetterDocs_Helper::list_query_arg('docs', $multiple_kb, $current_category->slug, -1, 'name', 'asc', ''), $current_category->term_id );
$current_category_icon_id 	= get_term_meta( $current_category->term_id, 'doc_category_thumb-id', true ) ?  get_term_meta( $current_category->term_id, 'doc_category_thumb-id', true ) : '';
$current_category_icon_url  = $current_category_icon_id ? wp_get_attachment_image( $current_category_icon_id, 'medium' ) : '<img src="' . BETTERDOCS_PRO_URL. 'admin/assets/img/cat-grid-3.png">';
$heading                    = isset( $customizer_settings['betterdocs_archive_other_categories_heading_text'] ) ?  $customizer_settings['betterdocs_archive_other_categories_heading_text'] : '';
$load_more_text             = isset( $customizer_settings['betterdocs_archive_other_categories_load_more_text'] ) ? $customizer_settings['betterdocs_archive_other_categories_load_more_text'] : '';
$category_related_docs      = shortcode_exists('betterdocs_related_categories') ? do_shortcode('[betterdocs_related_categories heading="'.$heading.'" load_more_text="'.$load_more_text.'"]') : '';
$doc_query		            = new WP_Query( $list_args );
$doc_list                   = '';

while( $doc_query->have_posts() ) {
    $doc_query->the_post();
    $excerpt   = get_the_content() != '' ? '<p class="betterdocs-excerpt">'.wp_trim_words( get_the_content(), 20 ).'</p>' : '';
    $doc_list .= '<li id="'.get_the_ID().'"><a class="betterdocs-article" href="'.esc_url( get_the_permalink() ).'"><p>'.wp_kses(get_the_title(), BETTERDOCS_PRO_KSES_ALLOWED_HTML).'</p><svg class="archive-doc-arrow" width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.85848 5.53176L1.51833 0.191706C1.39482 0.068097 1.22994 0 1.05414 0C0.878334 0 0.713458 0.068097 0.589947 0.191706L0.196681 0.584873C-0.059219 0.841066 -0.059219 1.25745 0.196681 1.51326L4.68094 5.99751L0.191706 10.4867C0.0681946 10.6104 0 10.7751 0 10.9508C0 11.1267 0.0681946 11.2915 0.191706 11.4152L0.584971 11.8083C0.70858 11.9319 0.873359 12 1.04916 12C1.22497 12 1.38984 11.9319 1.51335 11.8083L6.85848 6.46336C6.98229 6.33936 7.05028 6.1738 7.04989 5.9978C7.05028 5.82112 6.98229 5.65566 6.85848 5.53176Z" fill="#15063F"/></svg></a>'.$excerpt.'</li>';
}

get_header();

echo '<div class="betterdocs-category-wraper betterdocs-single-wraper">';
    echo $live_search_markup;
    echo '<div class="betterdocs-content-area betterdocs-doc-category-full-width">';
        echo '<div id="main" class="docs-listing-main">';
            echo '<div class="docs-category-listing">';
                echo '<div class="betterdocs-doc-category-layout-2-container">';
                    echo '<div class="betterdocs-doc-category-layout-2">';
                            echo '<div class="betterdocs-doc-category-term-img">';
                                echo $current_category_icon_url;
                            echo '</div>';
                            echo '<div class="betterdocs-doc-category-term-info">';
                                echo '<div class="betterdocs-doc-category-term-title-count">';
                                    echo '<'.$customizer_settings['betterdocs_archive_title_tag_layout2'].' class="betterdocs-doc-category-term-title">'.esc_html( $current_category->name ).'</'.$customizer_settings['betterdocs_archive_title_tag_layout2'].'>';
                                    echo '<span class="betterdocs-doc-category-term-count">'.$term_doc_count.'</span>';
                                echo '</div>';
                                betterdocs_breadcrumbs();
                            echo '</div>';
                    echo '</div>';
                    echo '<div class="betterdocs-doc-category-term-description"><p>'.$current_category->description.'</p></div>';
                    echo '<ul class="betterdocs-doc-category-layout-2-list">';
                    echo $doc_list;
                    echo '</ul>';
                    echo $category_related_docs;
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';

echo '</div>';

get_footer();