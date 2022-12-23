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

echo '<div class="betterdocs-wraper betterdocs-mkb-wraper">';
	$live_search = BetterDocs_DB::get_settings('live_search');
	if ($live_search == 1) {
	    echo BetterDocs_Public::search();
	}

    echo '<div class="betterdocs-archive-wrap betterdocs-archive-mkb">
        <div class="betterdocs-list-popular">
            <div class="betterdocs-archive-list-view">';
                $output                 = betterdocs_generate_output();
                if ( is_tax( 'knowledge_base' ) && BetterDocs_Multiple_Kb::$enable == 1 ) {
                    echo do_shortcode( '[betterdocs_multiple_kb_list multiple_knowledge_base="true" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
                } else {
                    echo do_shortcode( '[betterdocs_multiple_kb_list title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
                }
            echo '</div>';
            if( get_theme_mod('betterdocs_mkb_popular_docs_switch', true) ) {
                $popular_doc_text       = BetterDocs_DB::get_settings('betterdocs_popular_docs_text');
                $popular_posts_per_page = BetterDocs_DB::get_settings('betterdocs_popular_docs_number');
                echo '<div class="betterdocs-archive-popular">';
                    echo do_shortcode( '[betterdocs_popular_articles multiple_knowledge_base=true title="'.$popular_doc_text.'" post_per_page="'.$popular_posts_per_page.'"]' );
                echo '</div>';
            }
  echo '</div>
    </div>';
    
    include( BETTERDOCS_PUBLIC_PATH . 'partials/faq-mkb.php' );

echo '</div>';

get_footer();
