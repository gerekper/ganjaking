<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Update extends Mfn_API {

	protected $code = '';

	/**
	 * Mfn_Update constructor
	 */

	public function __construct(){

		$this->code = mfn_get_purchase_code();

		// It runs when wordpress check for updates
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );

	}

	/**
	 * Filter WP Update transient
	 *
	 * @param unknown $transient
	 * @return unknown
	 */

	public function pre_set_site_transient_update_themes( $transient ) {

		if( ! mfn_is_registered() ){
			return $transient;
		}

		$new_version = $this->remote_get_version();
		$theme_template = get_template();

		if( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

			$args = array(
				'code' => $this->code,
			);

			if( mfn_is_hosted() ){
				$args[ 'ish' ] = mfn_get_ish();
			}

			$transient->response[ $theme_template ] = array(
				'theme' => $theme_template,
				'new_version' => $new_version,
				'url' => $this->get_url( 'changelog' ),
				'package' => add_query_arg( $args, $this->get_url( 'theme_download' ) ),
			);

		}

		return $transient;
	}

}

$mfn_update = new Mfn_Update();
