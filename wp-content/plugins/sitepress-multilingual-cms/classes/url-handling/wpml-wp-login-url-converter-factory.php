<?php

namespace WPML\UrlHandling;

class WPLoginUrlConverterFactory implements \IWPML_Frontend_Action_Loader, \IWPML_Backend_Action_Loader {

	/**
	 * @return WPLoginUrlConverter|null
	 */
	public function create() {
		/** @var \WPML_URL_Converter $wpml_url_converter */
		global $wpml_url_converter, $sitepress;


		return get_option( WPLoginUrlConverter::SETTINGS_KEY, false )
			? $wpml_url_converter->get_wp_login_url_converter( $sitepress )
			: null;
	}
}