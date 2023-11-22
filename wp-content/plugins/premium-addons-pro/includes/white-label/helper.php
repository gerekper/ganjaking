<?php

namespace PremiumAddonsPro\Includes\White_Label;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Helper
 */
class Helper {

	/**
	 * White Label Options
	 *
	 * @var white_label
	 */
	public static $white_label = null;

	/**
	 * Return plugin pro version author name
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function author_pro() {

		if ( isset( get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-name-pro'] ) ) {

			$author_pro = get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-name-pro'];

		}

		return ( isset( $author_pro ) && '' != $author_pro ) ? $author_pro : 'Leap13';
	}

	/**
	 * Return plugin pro version name
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function name_pro() {

		if ( isset( get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-plugin-name-pro'] ) ) {

			$name_pro = get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-plugin-name-pro'];

		}

		return ( isset( $name_pro ) && '' != $name_pro ) ? $name_pro : 'Premium Addons PRO for Elementor';
	}

	/**
	 * Check if license if valid
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function is_lic_act() {

		$license_status = get_option( 'papro_license_status' );

		return ( 'valid' === $license_status ) ? true : false;

	}

	/**
	 * Check if hide plugin changelog link is enabled
	 *
	 * @since 1.7.7
	 * @access public
	 *
	 * @return boolean
	 */
	public static function is_hide_changelog() {

		if ( isset( get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-changelog'] ) ) {

			$hide_changelog = get_option( 'pa_wht_lbl_save_settings' )['premium-wht-lbl-changelog'];

		}

		return isset( $hide_changelog ) ? $hide_changelog : false;
	}

	/**
	 * Get White Label Settings
	 *
	 * @since 2.0.7
	 * @access public
	 *
	 * @return array $options white label options
	 */
	public static function get_white_label_options() {

		if ( null === self::$white_label ) {

			self::$white_label = array(
				'premium-wht-lbl-name',
				'premium-wht-lbl-url',
				'premium-wht-lbl-plugin-name',
				'premium-wht-lbl-short-name',
				'premium-wht-lbl-desc',
				'premium-wht-lbl-row',
				'premium-wht-lbl-name-pro',
				'premium-wht-lbl-url-pro',
				'premium-wht-lbl-plugin-name-pro',
				'premium-wht-lbl-desc-pro',
				'premium-wht-lbl-changelog',
				'premium-wht-lbl-option',
				'premium-wht-lbl-rate',
				'premium-wht-lbl-about',
				'premium-wht-lbl-license',
				'premium-wht-lbl-not',
				'premium-wht-lbl-logo',
				'premium-wht-lbl-version',
				'premium-wht-lbl-prefix',
				'premium-wht-lbl-badge',
			);

		}

		return self::$white_label;

	}

	/**
	 * Get White Labeling Defaults
	 *
	 * @since 2.0.7
	 * @access public
	 *
	 * @return array $defaults white labeling defaults
	 */
	public static function get_white_labeling_defaults() {

		$keys = self::get_white_label_options();

		$defaults = array_fill_keys( $keys, '' );

		return $defaults;

	}

	/**
	 * Get White Labeling Settings
	 *
	 * @since 2.0.7
	 * @access public
	 *
	 * @return array $settings white labeling settings
	 */
	public static function get_white_labeling_settings() {

		$settings = get_option( 'pa_wht_lbl_save_settings', self::get_white_labeling_defaults() );

		return $settings;

	}

}
