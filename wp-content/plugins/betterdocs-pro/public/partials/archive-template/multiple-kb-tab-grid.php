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
                $terms_orderby = BetterDocs_DB::get_settings('terms_orderby');
                $terms_order   = BetterDocs_DB::get_settings('terms_order');
                if (BetterDocs_DB::get_settings('alphabetically_order_term') == 1) {
                    $terms_orderby = 'name';
                }
                echo do_shortcode( '[betterdocs_multiple_kb_tab_grid terms_order="'.esc_html($terms_order).'" terms_orderby="'.esc_html($terms_orderby).'"]' );
            echo '</div>';
    echo  '</div>';

    include( BETTERDOCS_PUBLIC_PATH . 'partials/faq-mkb.php' );

echo '</div>';

get_footer();
