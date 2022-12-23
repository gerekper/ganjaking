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
    if ( $live_search == 1 && method_exists('BetterDocs_Public','search') ) {
        echo BetterDocs_Public::search();
    }

    $terms_orderby = BetterDocs_DB::get_settings('terms_orderby');
    $terms_order   = BetterDocs_DB::get_settings('terms_order');
    if (BetterDocs_DB::get_settings('alphabetically_order_term') == 1) {
        $terms_orderby = 'name';
    }
	echo '<div class="betterdocs-archive-wrap betterdocs-archive-category-box betterdocs-archive-category-box-2 betterdocs-archive-main">';
        $output = betterdocs_generate_output();
		if ( is_tax( 'knowledge_base' ) && BetterDocs_Multiple_Kb::$enable == 1 ) {
			echo do_shortcode( '[betterdocs_category_box_2 multiple_knowledge_base="true" terms_order="'.esc_html($terms_order).'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
		} else {
			echo do_shortcode( '[betterdocs_category_box_2 terms_order="'.esc_html($terms_order).'" terms_orderby="'.esc_html($terms_orderby).'" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
		}
	echo '</div>';

    include( BETTERDOCS_PUBLIC_PATH . 'partials/faq.php' );
    
'</div>';

get_footer();