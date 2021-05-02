<?php
/**
 * Template archive docs
 *
 * @link       https://wpdeveloper.net
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

get_header(); 

echo '<div class="betterdocs-wraper betterdocs-main-wraper">';
	$live_search = BetterDocs_DB::get_settings('live_search');
	if ($live_search == 1) {
        echo '<div class="betterdocs-search-form-wrap cat-layout-4">'. do_shortcode( '[betterdocs_search_form]' ) .'</div>';
	}

    $output = betterdocs_generate_output();
	echo '<div class="betterdocs-archive-wrap betterdocs-archive-main cat-layout-4">';
        if ( is_tax( 'knowledge_base' ) && BetterDocs_Multiple_Kb::$enable == 1 ) {
            echo do_shortcode( '[betterdocs_category_grid_2 multiple_knowledge_base="true" title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
        } else {
            echo do_shortcode( '[betterdocs_category_grid_2 title_tag="'.BetterDocs_Helper::html_tag($output['betterdocs_category_title_tag']).'"]' );
        }
	echo '</div>';
echo '</div>';

get_footer();
