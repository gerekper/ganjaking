<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * RevSlider loader.
 * @since 4.3
 */
class Vc_Vendor_Revslider {
	/**
	 * @since 4.3
	 * @var int - index of revslider
	 */
	protected static $instanceIndex = 1;

	/**
	 * Add shortcode to WPBakery Page Builder also add fix for frontend to regenerate id of revslider.
	 * @since 4.3
	 */
	public function load() {
		add_action( 'vc_after_mapping', array(
			$this,
			'buildShortcode',
		) );

	}

	/**
	 * @since 4.3
	 */
	public function buildShortcode() {
		if ( class_exists( 'RevSlider' ) ) {
			vc_lean_map( 'rev_slider_vc', array(
				$this,
				'addShortcodeSettings',
			) );
			if ( vc_is_frontend_ajax() || vc_is_frontend_editor() ) {
				add_filter( 'vc_revslider_shortcode', array(
					$this,
					'setId',
				) );
			}
		}
	}

	/**
	 * @param array $revsliders
	 *
	 * @since 4.4
	 *
	 * @deprecated 4.9
	 */
	public function mapShortcode( $revsliders = array() ) {
		vc_map( array(
			'base' => 'rev_slider_vc',
			'name' => esc_html__( 'Revolution Slider', 'js_composer' ),
			'icon' => 'icon-wpb-revslider',
			'category' => esc_html__( 'Content', 'js_composer' ),
			'description' => esc_html__( 'Place Revolution slider', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Widget title', 'js_composer' ),
					'param_name' => 'title',
					'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Revolution Slider', 'js_composer' ),
					'param_name' => 'alias',
					'admin_label' => true,
					'value' => $revsliders,
					'save_always' => true,
					'description' => esc_html__( 'Select your Revolution Slider.', 'js_composer' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'js_composer' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
				),
			),
		) );
	}

	/**
	 * Replaces id of revslider for frontend editor.
	 * @param $output
	 *
	 * @return string
	 * @since 4.3
	 *
	 */
	public function setId( $output ) {
		return preg_replace( '/rev_slider_(\d+)_(\d+)/', 'rev_slider_$1_$2' . time() . '_' . self::$instanceIndex ++, $output );
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
		/** @noinspection PhpUndefinedClassInspection */
		$slider = new RevSlider();
		$arrSliders = $slider->getArrSliders();

		$revsliders = array();
		if ( $arrSliders ) {
			foreach ( $arrSliders as $slider ) {
				/** @noinspection PhpUndefinedClassInspection */
				/** @var RevSlider $slider */
				$revsliders[ $slider->getTitle() ] = $slider->getAlias();
			}
		} else {
			$revsliders[ esc_html__( 'No sliders found', 'js_composer' ) ] = 0;
		}

		// Add fixes for frontend editor to regenerate id
		return array(
			'base' => $tag,
			'name' => esc_html__( 'Revolution Slider', 'js_composer' ),
			'icon' => 'icon-wpb-revslider',
			'category' => esc_html__( 'Content', 'js_composer' ),
			'description' => esc_html__( 'Place Revolution slider', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Widget title', 'js_composer' ),
					'param_name' => 'title',
					'description' => esc_html__( 'Enter text used as widget title (Note: located above content element).', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Revolution Slider', 'js_composer' ),
					'param_name' => 'alias',
					'admin_label' => true,
					'value' => $revsliders,
					'save_always' => true,
					'description' => esc_html__( 'Select your Revolution Slider.', 'js_composer' ),
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
