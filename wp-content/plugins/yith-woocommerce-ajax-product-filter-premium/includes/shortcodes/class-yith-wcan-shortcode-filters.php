<?php
/**
 * Filters Shortcodes
 *
 * Defines shortcode that output Filters Preset
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Shortcodes
 * @version 4.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Shortcode_Filters' ) ) {
	/**
	 * Shortcodes classes
	 */
	class YITH_WCAN_Shortcode_Filters {
		/**
		 * Render shortcode, given attributes
		 *
		 * @param array $atts Shortcode attributes.
		 * @return string Shortcode output
		 */
		public static function render( $atts = array() ) {
			$defaults = array(
				'slug'     => '',
				'selector' => '',
			);
			$atts     = shortcode_atts( $defaults, $atts );

			if ( ! $atts['slug'] ) {
				return '';
			}

			// retrieve preset.
			$preset = YITH_WCAN_Preset_Factory::get_preset( $atts['slug'] );

			if ( ! $preset || ! $preset->is_enabled() || ! $preset->get_filters() ) {
				return false;
			}

			$atts['preset'] = $preset;

			// include template.
			return yith_wcan_get_template( 'shortcodes/filters.php', $atts, false );
		}

		/**
		 * Returns array of configuration for Gutenberg block fo this shortcode
		 *
		 * @return array Array of configuration.
		 */
		public static function get_gutenberg_config() {
			$presets         = YITH_WCAN_Preset_Factory::list_presets();
			$presets_options = array_merge(
				array(
					'' => _x( 'Choose an option', '[ELEMENTOR] Default preset option', 'yith-woocommerce-ajax-navigation' ),
				),
				$presets
			);

			$blocks = array(
				'yith-wcan-ajax-filters-preset' => array(
					'style'          => 'yith-wcan-shortcodes',
					'script'         => 'yith-wcan-shortcodes',
					'title'          => _x( 'YITH AJAX Filters Preset', '[GUTENBERG]: block name', 'yith-woocommerce-ajax-navigation' ),
					'description'    => _x( 'Show filters from a preset', '[GUTENBERG]: block description', 'yith-woocommerce-ajax-navigation' ),
					'shortcode_name' => 'yith_wcan_filters',
					'empty_message'  => _x( 'Please, choose the preset to render', '[GUTENBERG]: block empty message', 'yith-woocommerce-ajax-navigation' ),
					'attributes'     => array(
						'slug' => array(
							'type'    => 'select',
							'label'   => _x( 'Preset', '[GUTENBERG]: attribute description', 'yith-woocommerce-ajax-navigation' ),
							'options' => $presets_options,
							'default' => '',
						),
					),
				),
			);

			return $blocks;
		}
	}
}
