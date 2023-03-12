<?php

namespace ACA\WC\Asset\Script;

use AC;

class TableVariation extends AC\Asset\Script {

	private const TABLE = 'product_variation';

	public function __construct( string $handle, AC\Asset\Location\Absolute $location ) {
		parent::__construct( $handle, $location->with_suffix( 'assets/js/table-variation.js' ), [ 'jquery' ] );
	}

	public function register(): void {
		parent::register();

		wp_localize_script( $this->handle, 'aca_wc_table_variation', [
			'button_back_label' => __( 'Back to products', 'codepress-admin-columns' ),
			'button_back_link'  => $this->get_referer_link(),
		] );
	}

	private function get_referer_link(): string {
		$preference = new AC\Preferences\Site( 'referer' );

		$referer = $this->check_referer( 'product' );

		if ( $referer ) {
			$preference->set( self::TABLE, $referer );
		} else if ( ! $this->check_referer( self::TABLE ) ) {

			// Remove preference link when referer is neither from product or product_variation
			$preference->delete( self::TABLE );
		}

		$link = $preference->get( self::TABLE );

		if ( ! $link ) {
			$link = add_query_arg( [ 'post_type' => 'product' ], admin_url( 'edit.php' ) );
		}

		return $link;
	}

	/**
	 * Checks if the referer came from another list table
	 *
	 * @param string $post_type
	 *
	 * @return false|string Return referer link
	 */
	private function check_referer( $post_type ) {
		$referer = wp_get_referer();

		if ( ! $referer ) {
			return false;
		}

		if ( false === strpos( $referer, admin_url( 'edit.php' ) ) ) {
			return false;
		}

		$parts = parse_url( $referer );

		if ( ! isset( $parts['query'] ) ) {
			return false;
		}

		parse_str( $parts['query'], $query );

		if ( ! isset( $query['post_type'] ) || $post_type !== $query['post_type'] ) {
			return false;
		}

		return $referer;
	}

}