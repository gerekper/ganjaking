<?php
/**
 * Template multiple docs
 *
 * @link       https://wpdeveloper.com
 * @since      
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header();

echo '<div class="betterdocs-wraper betterdocs-mkb-wraper">';
    $live_search = BetterDocs_DB::get_settings('live_search');
    if ( $live_search == 1 && method_exists('BetterDocs_Public','search') ) {
        echo BetterDocs_Public::search();
    }
	
	echo '<div class="betterdocs-archive-wrap betterdocs-archive-mkb">';
	    $output = betterdocs_generate_output_pro();
	    echo do_shortcode( '[betterdocs_multiple_kb_2 title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_mkb_title_tag']).'"]' );
	echo '</div>';
    
    include( BETTERDOCS_PUBLIC_PATH . 'partials/faq-mkb.php' );

echo '</div>';

get_footer();
