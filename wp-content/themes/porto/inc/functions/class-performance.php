<?php
/**
 * Porto Performance
 *
 * @author     Porto Themes
 * @since      6.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Performance' ) ) :
	class Porto_Performance {
		public function __construct() {
			// image quality
			add_filter( 'jpeg_quality', array( $this, 'modify_jpg_quality' ) );
			add_filter( 'wp_editor_set_quality', array( $this, 'modify_jpg_quality' ) );
			add_filter( 'big_image_size_threshold', array( $this, 'modify_image_size_threshold' ) );

			// remove emojis script
			add_action( 'init', array( $this, 'remove_emojis' ) );

			// disable jQuery migrate
			add_action( 'wp_default_scripts', array( $this, 'disable_jquery_migrate' ) );
		}

		/**
		 * Modify the image quality
		 *
		 * @since 6.2.0
		 */
		public function modify_jpg_quality( $quality ) {
			global $porto_settings_optimize;

			if ( ! empty( $porto_settings_optimize['jpg_quality'] ) ) {
				return (int) $porto_settings_optimize['jpg_quality'];
			}

			return $quality;
		}

		/**
		 * Modify WordPress Max image size
		 *
		 * @since 6.2.0
		 */
		public function modify_image_size_threshold( $threshold ) {
			global $porto_settings_optimize;
			if ( isset( $porto_settings_optimize['max_image_size'] ) && '' !== (string) $porto_settings_optimize['max_image_size'] ) {
				if ( 0 === (int) $porto_settings_optimize['max_image_size'] ) {
					return false;
				}
				return (int) $porto_settings_optimize['max_image_size'];
			}

			return $threshold;
		}

		/**
		 * Removes emojis.
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis() {

			global $porto_settings_optimize;
			if ( empty( $porto_settings_optimize['optimize_emojis'] ) ) {
				return;
			}

			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'tiny_mce_plugins', [ $this, 'remove_emojis_tinymce' ] );
			add_filter( 'wp_resource_hints', [ $this, 'remove_emojis_dns_prefetch' ], 10, 2 );

			if ( '1' === get_option( 'use_smilies' ) ) {
				update_option( 'use_smilies', '0' );
			}
		}

		/**
		 * Disable jQuery Migrate.
		 *
		 * @since 6.2.0
		 */
		public function disable_jquery_migrate( $scripts ) {

			global $porto_settings_optimize;
			if ( empty( $porto_settings_optimize['optimize_migrate'] ) ) {
				return;
			}

			if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
				$script = $scripts->registered['jquery'];

				if ( $script->deps ) {
					$script->deps = array_diff( $script->deps, [ 'jquery-migrate' ] );
				}
			}
		}

		/**
		 * Remove tinymce emoji plugin
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis_tinymce( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			}

			return array();
		}

		/**
		 * Remove emoji CDN hostname from DNS prefetching hints
		 *
		 * @since 6.2.0
		 */
		public function remove_emojis_dns_prefetch( $urls, $relation_type ) {

			if ( 'dns-prefetch' === $relation_type ) {
				$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/11/svg/' );
				$urls          = array_diff( $urls, array( $emoji_svg_url ) );
			}

			return $urls;
		}
	}

	new Porto_Performance;
endif;
