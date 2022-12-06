<?php

namespace ACA\WC\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\WC\ListScreen\Product;
use ACP\ListScreen\Post;

class Table implements Registerable {

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'fix_manual_product_sort' ], 12 ); // After Sorting is applied
		add_filter( 'acp/sorting/remember_last_sorting_preference', [ $this, 'disable_product_sorting_mode_preference' ], 10, 2 );
		add_filter( 'acp/sticky_header/enable', [ $this, 'disable_sticky_headers' ] );
	}

	public function fix_manual_product_sort( ListScreen $list_screen ) {
		if (
			isset( $_GET['orderby'] ) &&
			$list_screen instanceof Post &&
			$list_screen->get_post_type() === 'product' &&
			strpos( $_GET['orderby'], 'menu_order' ) !== false &&
			! filter_input( INPUT_GET, 'orderby' )
		) {
			unset( $_GET['orderby'] );
		}
	}

	public function disable_sticky_headers( $enabled ) {
		return 'product' === filter_input( INPUT_GET, 'post_type' ) && 'menu_order title' === filter_input( INPUT_GET, 'orderby' )
			? false
			: $enabled;
	}

	public function disable_product_sorting_mode_preference( $enabled, ListScreen $list_screen ) {
		if ( $list_screen instanceof Product && 'menu_order title' === filter_input( INPUT_GET, 'orderby' ) ) {
			return false;
		}

		return $enabled;
	}

}