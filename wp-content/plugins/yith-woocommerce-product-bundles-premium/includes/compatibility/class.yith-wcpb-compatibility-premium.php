<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Compatibility Class
 *
 * @class   YITH_WCPB_Compatibility_Premium
 * @package Yithemes
 * @since   1.1.15
 * @author  Yithemes
 */
class YITH_WCPB_Compatibility_Premium extends YITH_WCPB_Compatibility {

	/** @var \YITH_WCPB_Compatibility_Premium
	 */
	protected static $_instance;

	/** @var YITH_WCPB_Wpml_Compatibility_Premium */
	public $wpml;

	/**
	 * set the plugins
	 */
	protected function _set_plugins() {
		$this->_plugins = array(
			'wpml'                    => array(
				'always_enabled' => true,
			),
			'dynamic'                 => array(
				'always_enabled' => true,
			),
			'pdf-invoice'             => array(
				'always_enabled' => true,
			),
			'aelia-currency-switcher' => array(),
			'role-based'              => array(),
			'request-a-quote'         => array(),
			'catalog-mode'            => array(),
			'multi-vendor'            => array(),
			'name-your-price'         => array(),
			'waiting-list'            => array(),
		);
	}

	/**
	 * Check if user has plugin
	 *
	 * @param string $plugin_name
	 *
	 * @return bool
	 * @since   1.1.15
	 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	static function has_plugin( $plugin_name ) {
		switch ( $plugin_name ) {
			case 'catalog-mode':
				return defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM && defined( 'YWCTM_VERSION' ) && version_compare( YWCTM_VERSION, '1.4.8', '>=' );
			case 'role-based':
				return defined( 'YWCRBP_PREMIUM' ) && YWCRBP_PREMIUM && defined( 'YWCRBP_VERSION' ) && version_compare( YWCRBP_VERSION, '1.0.9', '>=' );
			case 'request-a-quote':
				return defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM && defined( 'YITH_YWRAQ_VERSION' ) && version_compare( YITH_YWRAQ_VERSION, '1.5.7', '>=' );
			case 'multi-vendor':
				return defined( 'YITH_WPV_INIT' ) && YITH_WPV_INIT;
			case 'name-your-price':
				return defined( 'YWCNP_PREMIUM' ) && YWCNP_PREMIUM && defined( 'YWCNP_VERSION' ) && version_compare( YWCNP_VERSION, '1.1.5', '>=' );
			case 'waiting-list':
				return defined( 'YITH_WCWTL_PREMIUM' ) && YITH_WCWTL_PREMIUM;
			case 'aelia-currency-switcher':
				return isset( $GLOBALS['woocommerce-aelia-currencyswitcher'] );
			default:
				return false;
		}
	}
}