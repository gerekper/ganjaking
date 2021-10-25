<?php

/**
 * PAPRO White Label Branding.
 */
namespace PremiumAddonsPro\Includes\White_Label;

// Premium Addons Classes
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PA_White_Label' ) ) {

	final class Branding {

		public static function init() {

			add_action( 'after_setup_theme', __CLASS__ . '::pa_init_hooks' );

		}

		public static function pa_init_hooks() {

			add_filter( 'all_plugins', __CLASS__ . '::add_plugin_info' );

			add_filter( 'plugin_row_meta', __CLASS__ . '::pa_meta_data', 10, 2 );

		}

		/**
		 * PA Meta Data
		 *
		 * Add meta data to the free/pro versions
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array  $plugin_meta
		 * @param string $plugin_file
		 */
		public static function pa_meta_data( $plugin_meta, $plugin_file ) {

			$settings = Helper::get_white_labeling_settings();

			$url = $settings['premium-wht-lbl-url'];

			if ( $plugin_file === PREMIUM_ADDONS_BASENAME ) {
				if ( ! empty( $url ) ) {
					$plugin_meta[2] = '';

					$row_meta = array(
						'uri' => '<a href="' . $url . '" title="' . esc_attr( __( 'Visit Plugin Site', 'premium-addons-pro' ) ) . '" target="_blank">' . __( 'Visit Plugin Site', 'premium-addons-pro' ) . '</a>',
					);

					$plugin_meta = array_merge( $plugin_meta, $row_meta );
				}

				return $plugin_meta;
			}

			if ( $plugin_file === PREMIUM_PRO_ADDONS_BASENAME ) {

				if ( ! Helper::is_hide_changelog() ) {

					$theme = Helper_Functions::get_installed_theme();

					$link = sprintf( 'https://premiumaddons.com/change-log/?utm_source=plugins-page&utm_medium=wp-dash&utm_campaign=changelog-link&utm_term=%s', $theme );

					$row_meta = array(
						'changelog' => '<a href="' . esc_attr( $link ) . '" aria-label="' . esc_attr( __( 'View Premium Addons Pro Changelog', 'premium-addons-pro' ) ) . '" target="_blank">' . __( 'Changelog', 'premium-addons-pro' ) . '</a>',
					);

					$plugin_meta = array_merge( $plugin_meta, $row_meta );
				}

				return $plugin_meta;
			}

			return $plugin_meta;

		}

		/**
		 * Add Plugins Info
		 *
		 * @param array $plugins
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public static function add_plugin_info( $plugins ) {

			$settings = Helper::get_white_labeling_settings();

			$basename_free = plugin_basename( PREMIUM_ADDONS_PATH . 'premium-addons-for-elementor.php' );

			$basename_pro = plugin_basename( PREMIUM_PRO_ADDONS_PATH . 'premium-addons-pro-for-elementor.php' );

			if ( isset( $plugins[ $basename_pro ] ) && isset( $plugins[ $basename_free ] ) && is_array( $settings ) ) {

				$plugin_name = isset( $settings['premium-wht-lbl-plugin-name'] ) ? $settings['premium-wht-lbl-plugin-name'] : '';
				$plugin_desc = isset( $settings['premium-wht-lbl-desc'] ) ? $settings['premium-wht-lbl-desc'] : '';

				$author_name     = isset( $settings['premium-wht-lbl-name'] ) ? $settings['premium-wht-lbl-name'] : '';
				$author_url      = isset( $settings['premium-wht-lbl-url'] ) ? $settings['premium-wht-lbl-url'] : '';
				$plugin_name_pro = isset( $settings['premium-wht-lbl-plugin-name-pro'] ) ? $settings['premium-wht-lbl-plugin-name-pro'] : '';

				$plugin_desc_pro = isset( $settings['premium-wht-lbl-desc-pro'] ) ? $settings['premium-wht-lbl-desc-pro'] : '';

				$author_name_pro = isset( $settings['premium-wht-lbl-name-pro'] ) ? $settings['premium-wht-lbl-name-pro'] : '';
				$author_url_pro  = isset( $settings['premium-wht-lbl-url-pro'] ) ? $settings['premium-wht-lbl-url-pro'] : '';

				if ( '' != $plugin_name ) {
					$plugins[ $basename_free ]['Name']  = $plugin_name;
					$plugins[ $basename_free ]['Title'] = $plugin_name;
				}

				if ( '' != $plugin_desc ) {
					$plugins[ $basename_free ]['Description'] = $plugin_desc;
				}

				if ( '' != $author_name ) {
					$plugins[ $basename_free ]['Author']     = $author_name;
					$plugins[ $basename_free ]['AuthorName'] = $author_name;
				}

				if ( '' != $author_url ) {
					$plugins[ $basename_free ]['AuthorURI'] = $author_url;
					$plugins[ $basename_free ]['PluginURI'] = $author_url;
				}

				if ( '' != $plugin_name_pro ) {
					$plugins[ $basename_pro ]['Name']  = $plugin_name_pro;
					$plugins[ $basename_pro ]['Title'] = $plugin_name_pro;
				}

				if ( '' != $plugin_desc_pro ) {
					$plugins[ $basename_pro ]['Description'] = $plugin_desc_pro;
				}

				if ( '' != $author_name_pro ) {
					$plugins[ $basename_pro ]['Author']     = $author_name_pro;
					$plugins[ $basename_pro ]['AuthorName'] = $author_name_pro;
				}

				if ( '' != $author_url_pro ) {
					$plugins[ $basename_pro ]['AuthorURI'] = $author_url_pro;
					$plugins[ $basename_pro ]['PluginURI'] = $author_url_pro;
				}
			}

			return $plugins;
		}

	}

}
