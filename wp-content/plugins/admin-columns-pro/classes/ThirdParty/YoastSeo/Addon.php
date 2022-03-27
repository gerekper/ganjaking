<?php

namespace ACP\ThirdParty\YoastSeo;

use AC\ListScreen;
use AC\Registrable;

final class Addon implements Registrable {

	/**
	 * @return bool
	 */
	private function is_active() {
		return defined( 'WPSEO_VERSION' );
	}

	public function register() {
		if ( ! $this->is_active() ) {
			return;
		}

		add_action( 'ac/admin_footer', [ $this, 'fix_yoast_heading_tooltips' ] );
		add_action( 'ac/table/list_screen', [ $this, 'remove_link_column_on_ajax' ] );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function remove_link_column_on_ajax( $list_screen ) {
		/**
		 * Quickfix for Yoast SEO Link column, that gives an error on the/our Ajax call
		 * We unset this column on our Ajax Request so
		 */
		add_filter( $list_screen->get_heading_hookname(), function ( $headings ) {
			if ( filter_input( INPUT_POST, 'ac_action' ) && is_array( $headings ) ) {
				$headings = $this->replace_key_maintain_order( $headings, 'wpseo-links', 'wpseo-links_empty' );
				$headings = $this->replace_key_maintain_order( $headings, 'wpseo-linked', 'wpseo-linked_empty' );
			}

			return $headings;
		}, 201 );

	}

	/**
	 * Replace key & Maintain Order
	 *
	 * @param array  $array
	 * @param string $oldkey
	 * @param string $newkey
	 *
	 * @return array
	 */
	private function replace_key_maintain_order( array $array, $oldkey, $newkey ) {
		if ( array_key_exists( $oldkey, $array ) ) {
			$keys = array_keys( $array );
			$keys[ array_search( $oldkey, $keys ) ] = $newkey;

			return array_combine( $keys, $array );
		}

		return $array;
	}

	public function fix_yoast_heading_tooltips() {
		?>
		<style>
			.wp-list-table th > a.yoast-tooltip::before,
			.wp-list-table th > a.yoast-tooltip::after {
				display: none !important;
			}
		</style>
		<?php
	}

}