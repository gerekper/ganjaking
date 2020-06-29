<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * LayerSlider loader.
 * Adds layerSlider shortcode to WPBakery Page Builder and fixes issue in frontend editor
 *
 * @since 4.3
 */
class Vc_Vendor_Layerslider {
	/**
	 * @var int - used to detect id for layerslider in frontend
	 * @deprecated
	 */
	protected static $instanceIndex = 1;

	/**
	 * Add layerslayer shortcode to WPBakery Page Builder, and add fix for ID in frontend editor
	 * @since 4.3
	 */
	public function load() {
		add_action( 'vc_after_mapping', array(
			$this,
			'buildShortcode',
		) );

	}

	/**
	 * Add shortcode and filters for layerslider id
	 * @since 4.3
	 */
	public function buildShortcode() {

		vc_lean_map( 'layerslider_vc', array(
			$this,
			'addShortcodeSettings',
		) );

		if ( vc_is_page_editable() ) {
			add_filter( 'layerslider_slider_init', array(
				$this,
				'setMarkupId',
			), 10, 3 );
			add_filter( 'layerslider_slider_markup', array(
				$this,
				'setMarkupId',
			), 10, 3 );
		}
	}

	/**
	 * @param $output
	 *
	 * @return string
	 * @since 4.3
	 *
	 */
	public function setId( $output ) {
		return preg_replace( '/(layerslider_\d+)/', '$1_' . $_SERVER['REQUEST_TIME'], $output );
	}

	/**
	 * @param $markup
	 * @param $slider
	 * @param $id
	 * @return string
	 * @deprecated 5.2
	 * @since 4.3
	 */
	public function setMarkupId( $markup, $slider, $id ) {
		return str_replace( $id, $id . '_' . $_SERVER['REQUEST_TIME'], $markup );
	}

	/**
	 * Mapping settings for lean method.
	 *
	 * @param $tag
	 *
	 * @return array
	 * @since 4.9
	 *
	 */
	public function addShortcodeSettings( $tag ) {
		$use_old = class_exists( 'LS_Sliders' );
		if ( ! class_exists( 'LS_Sliders' ) && defined( 'LS_ROOT_PATH' ) && false === strpos( LS_ROOT_PATH, '.php' ) ) {
			include_once LS_ROOT_PATH . '/classes/class.ls.sliders.php';
			$use_old = false;
		}
		if ( ! class_exists( 'LS_Sliders' ) ) {
			// again check is needed if some problem inside file "class.ls.sliders.php
			$use_old = true;
		}
		/**
		 * Filter to use old type of layerslider vendor.
		 * @since 4.4.2
		 */
		$use_old = apply_filters( 'vc_vendor_layerslider_old', $use_old ); // @since 4.4.2 hook to use old style return true.
		if ( $use_old ) {
			global $wpdb;
			$ls = wp_cache_get( 'vc_vendor_layerslider_list' );

			if ( empty( $ls ) ) {
				// @codingStandardsIgnoreLine
				$ls = $wpdb->get_results( '
  SELECT id, name, date_c
  FROM ' . $wpdb->prefix . "layerslider
  WHERE flag_hidden = '0' AND flag_deleted = '0'
  ORDER BY date_c ASC LIMIT 999
  " );
				wp_cache_add( 'vc_vendor_layerslider_list', $ls );
			}

			$layer_sliders = array();
			if ( ! empty( $ls ) ) {
				foreach ( $ls as $slider ) {
					$layer_sliders[ $slider->name ] = $slider->id;
				}
			} else {
				$layer_sliders[ esc_html__( 'No sliders found', 'js_composer' ) ] = 0;
			}
		} else {
			/** @noinspection PhpUndefinedClassInspection */
			$ls = LS_Sliders::find( array(
				'limit' => 999,
				'order' => 'ASC',
			) );
			$layer_sliders = array();
			if ( ! empty( $ls ) ) {
				foreach ( $ls as $slider ) {
					$layer_sliders[ $slider['name'] ] = $slider['id'];
				}
			} else {
				$layer_sliders[ esc_html__( 'No sliders found', 'js_composer' ) ] = 0;
			}
		}

		return array(
			'base' => $tag,
			'name' => esc_html__( 'Layer Slider', 'js_composer' ),
			'icon' => 'icon-wpb-layerslider',
			'category' => esc_html__( 'Content', 'js_composer' ),
			'description' => esc_html__( 'Place LayerSlider', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Widget title', 'js_composer' ),
					'param_name' => 'title',
					'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'LayerSlider ID', 'js_composer' ),
					'param_name' => 'id',
					'admin_label' => true,
					'value' => $layer_sliders,
					'save_always' => true,
					'description' => esc_html__( 'Select your LayerSlider.', 'js_composer' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'js_composer' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
				),
			),
		);
	}
}
