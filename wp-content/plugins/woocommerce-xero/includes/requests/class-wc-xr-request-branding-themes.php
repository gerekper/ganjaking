<?php
/**
 *  API calls for Branding themes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Request_Branding_Themes extends WC_XR_Request {

	public function __construct( WC_XR_Settings $settings, $branding_theme_ID = '' ) {
		parent::__construct( $settings );

		$this->set_method( 'GET' );

		$end_point = 'BrandingThemes';
		if ( ! empty( $branding_theme_ID ) ) {
			$end_point .= "/$branding_theme_ID";
		}
		$this->set_endpoint( $end_point );
	}
}
