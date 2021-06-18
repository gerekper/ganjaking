<?php
/**
 * Woocomposer pagination
 *
 * @package Woocomposer pagination.
 */

/**
 * Render function for Ultimate Info List Module.
 *
 * @param string $pages .
 * @param int    $range value .
 * @access public
 */
function woocomposer_pagination( $pages = '', $range = 2 ) {
	ob_start();
	$showitems = ( $range * 2 ) + 1;
	global $paged;
	if ( empty( $paged ) ) {
		$paged = 1; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
	if ( '' == $pages ) {
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if ( ! $pages ) {
			$pages = 1;
		}
	}
	if ( 1 != $pages ) {
		echo "<div class='wcmp-pagination'>";
		if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
			echo "<a href='" . esc_url( get_pagenum_link( 1 ) ) . "'>&laquo;</a>";
		}
		if ( $paged > 1 && $showitems < $pages ) {
			echo "<a href='" . esc_url( get_pagenum_link( $paged - 1 ) ) . "'>&lsaquo;</a>";
		}
		for ( $i = 1; $i <= $pages; $i++ ) {
			if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
				echo ( $paged == $i ) ? "<span class='current'>" . esc_attr( $i ) . '</span>' : "<a href='" . esc_url( get_pagenum_link( $i ) ) . "' class='inactive' >" . esc_attr( $i ) . '</a>';
			}
		}
		if ( $paged < $pages && $showitems < $pages ) {
			echo "<a href='" . esc_url( get_pagenum_link( $paged + 1 ) ) . "'>&rsaquo;</a>";
		}
		if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
			echo "<a href='" . esc_url( get_pagenum_link( $pages ) ) . "'>&raquo;</a>";
		}
		echo "</div>\n";
	}

	return ob_get_clean();
}
