<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCBM_WPML_Compatibility
 * @package YITH WooCommerce Badge Management Premium
 * @since   1.4.0
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 */
class YITH_WCBM_WPML_Compatibility {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCBM_WPML_Compatibility
	 */
	protected static $_instance;


	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCBM_WPML_Compatibility
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * YITH_WCBM_WPML_Compatibility constructor.
	 */
	public function __construct() {
		add_filter( 'yith_wcbm_print_badges_select_badge_options', array( $this, 'badges_in_default_language_in_general_settings_panel' ) );
	}

	/**
	 * Show badges in default language for General Settings panel
	 *
	 * @param array $badge_options
	 *
	 * @return array
	 */
	public function badges_in_default_language_in_general_settings_panel( $badge_options ) {
		global $sitepress;
		if ( ! $badge_options && yith_wcbm_is_settings_panel() && $sitepress ) {
			$badge_options = array(
				'none' => __( 'None', 'yith-woocommerce-badges-management' ),
			);

			$default_language = $sitepress->get_default_language();
			$current_language = $sitepress->get_current_language();

			if ( isset( $_GET['tab'] ) && 'settings' !== $_GET['tab'] && $default_language !== $current_language ) {
				$current_language_badge_options = array();
				$badge_ids                      = yith_wcbm_get_badges( array( 'suppress_filters' => false ) );
				foreach ( $badge_ids as $badge_id ) {
					$current_language_badge_options[ $badge_id ] = get_the_title( $badge_id );
				}

				$default_language_badges = $this->get_badges_array_in_default_language();

				if ( $default_language_badges ) {
					$badge_options['default_language'] = array(
						'label'   => sprintf( __( 'Default Language: %s', 'yith-woocommerce-badges-management' ), $default_language ),
						'options' => $this->get_badges_array_in_default_language(),
					);
				}

				if ( $current_language_badge_options ) {
					$badge_options['current_language'] = array(
						'label'   => sprintf( __( 'Current Language: %s', 'yith-woocommerce-badges-management' ), $current_language ),
						'options' => $current_language_badge_options,
					);
				}
			} else {
				$badge_options = $badge_options + $this->get_badges_array_in_default_language();
			}
		}

		return $badge_options;
	}

	/**
	 * get the array of badges in default language
	 *
	 * @return array
	 */
	public function get_badges_array_in_default_language() {
		global $sitepress;
		$default_language_badge_options = array();

		$current_language = '';
		if ( isset( $sitepress ) ) {
			$current_language   = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$sitepress->switch_lang( $default_language );
		}

		$badge_ids = yith_wcbm_get_badges( array( 'suppress_filters' => false ) );
		foreach ( $badge_ids as $badge_id ) {
			$default_language_badge_options[ $badge_id ] = get_the_title( $badge_id );
		}

		if ( isset( $sitepress ) ) {
			$sitepress->switch_lang( $current_language );
		}

		return $default_language_badge_options;
	}

}