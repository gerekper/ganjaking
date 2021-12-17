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
        <div class="betterdocs-archive-tab-grid">';
            $output = betterdocs_generate_output();
            echo do_shortcode( '[betterdocs_multiple_kb_tab_grid]' );
        echo '</div>
    </div>
</div>';

get_footer();
