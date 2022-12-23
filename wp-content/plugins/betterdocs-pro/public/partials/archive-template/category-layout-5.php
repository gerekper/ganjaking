<?php
/**
 * Template archive docs
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header();

echo '<div class="betterdocs-wraper betterdocs-main-wraper">';
	$live_search = BetterDocs_DB::get_settings('live_search');
	if ($live_search == 1) {
	    echo BetterDocs_Public::search();
	}

    echo '<div class="betterdocs-archive-wrap betterdocs-archive-main">
        <div class="betterdocs-list-popular">
            <div class="betterdocs-archive-list-view">';
                $output = betterdocs_generate_output();
                $terms_orderby = BetterDocs_DB::get_settings('terms_orderby');
                $terms_order   = BetterDocs_DB::get_settings('terms_order');
                if (BetterDocs_DB::get_settings('alphabetically_order_term') == 1) {
                    $terms_orderby = 'name';
                }
                if ( is_tax( 'knowledge_base' ) && BetterDocs_Multiple_Kb::$enable == 1 ) {
                    echo do_shortcode( '[betterdocs_list_view multiple_knowledge_base="true" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'" terms_orderby="'.esc_html($terms_orderby).'" terms_order="'.esc_html($terms_order).'"]' );
                } else {
                    echo do_shortcode( '[betterdocs_list_view title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'" terms_orderby="'.esc_html($terms_orderby).'" terms_order="'.esc_html($terms_order).'"]' );
                }
            echo '</div>';
            if( get_theme_mod('betterdocs_docs_page_popular_docs_switch', true) ) {
                $popular_doc_text       = BetterDocs_DB::get_settings('betterdocs_popular_docs_text');
                $popular_posts_per_page = BetterDocs_DB::get_settings('betterdocs_popular_docs_number');
                echo '<div class="betterdocs-archive-popular">';
                    echo do_shortcode( '[betterdocs_popular_articles title="'.$popular_doc_text.'" post_per_page="'.$popular_posts_per_page.'"]' );
                echo '</div>';
            }
    echo '</div>
    </div>';
    include( BETTERDOCS_PUBLIC_PATH . 'partials/faq.php' );
'</div>';

get_footer();
