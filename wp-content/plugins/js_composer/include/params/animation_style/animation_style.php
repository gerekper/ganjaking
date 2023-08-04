<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_ParamAnimation
 *
 * For working with animations
 * array(
 *        'type' => 'animation_style',
 *        'heading' => esc_html__( 'Animation', 'js_composer' ),
 *        'param_name' => 'animation',
 * ),
 * Preview in https://daneden.github.io/animate.css/
 * @since 4.4
 */
class Vc_ParamAnimation {
	/**
	 * @since 4.4
	 * @var array $settings parameter settings from vc_map
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var string $value parameter value
	 */
	protected $value;

	/**
	 * Define available animation effects
	 * @return array
	 * @since 4.4
	 * vc_filter: vc_param_animation_style_list - to override animation styles
	 *     array
	 */
	protected function animationStyles() {
		$styles = array(
			array(
				'values' => array(
					esc_html__( 'None', 'js_composer' ) => 'none',
				),
			),
			array(
				'label' => esc_html__( 'Attention Seekers', 'js_composer' ),
				'values' => array(
					// text to display => value
					esc_html__( 'bounce', 'js_composer' ) => array(
						'value' => 'bounce',
						'type' => 'other',
					),
					esc_html__( 'flash', 'js_composer' ) => array(
						'value' => 'flash',
						'type' => 'other',
					),
					esc_html__( 'pulse', 'js_composer' ) => array(
						'value' => 'pulse',
						'type' => 'other',
					),
					esc_html__( 'rubberBand', 'js_composer' ) => array(
						'value' => 'rubberBand',
						'type' => 'other',
					),
					esc_html__( 'shake', 'js_composer' ) => array(
						'value' => 'shake',
						'type' => 'other',
					),
					esc_html__( 'swing', 'js_composer' ) => array(
						'value' => 'swing',
						'type' => 'other',
					),
					esc_html__( 'tada', 'js_composer' ) => array(
						'value' => 'tada',
						'type' => 'other',
					),
					esc_html__( 'wobble', 'js_composer' ) => array(
						'value' => 'wobble',
						'type' => 'other',
					),
				),
			),
			array(
				'label' => esc_html__( 'Bouncing Entrances', 'js_composer' ),
				'values' => array(
					// text to display => value
					esc_html__( 'bounceIn', 'js_composer' ) => array(
						'value' => 'bounceIn',
						'type' => 'in',
					),
					esc_html__( 'bounceInDown', 'js_composer' ) => array(
						'value' => 'bounceInDown',
						'type' => 'in',
					),
					esc_html__( 'bounceInLeft', 'js_composer' ) => array(
						'value' => 'bounceInLeft',
						'type' => 'in',
					),
					esc_html__( 'bounceInRight', 'js_composer' ) => array(
						'value' => 'bounceInRight',
						'type' => 'in',
					),
					esc_html__( 'bounceInUp', 'js_composer' ) => array(
						'value' => 'bounceInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => esc_html__( 'Bouncing Exits', 'js_composer' ),
				'values' => array(
					// text to display => value
					esc_html__( 'bounceOut', 'js_composer' ) => array(
						'value' => 'bounceOut',
						'type' => 'out',
					),
					esc_html__( 'bounceOutDown', 'js_composer' ) => array(
						'value' => 'bounceOutDown',
						'type' => 'out',
					),
					esc_html__( 'bounceOutLeft', 'js_composer' ) => array(
						'value' => 'bounceOutLeft',
						'type' => 'out',
					),
					esc_html__( 'bounceOutRight', 'js_composer' ) => array(
						'value' => 'bounceOutRight',
						'type' => 'out',
					),
					esc_html__( 'bounceOutUp', 'js_composer' ) => array(
						'value' => 'bounceOutUp',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Fading Entrances', 'js_composer' ),
				'values' => array(
					// text to display => value
					esc_html__( 'fadeIn', 'js_composer' ) => array(
						'value' => 'fadeIn',
						'type' => 'in',
					),
					esc_html__( 'fadeInDown', 'js_composer' ) => array(
						'value' => 'fadeInDown',
						'type' => 'in',
					),
					esc_html__( 'fadeInDownBig', 'js_composer' ) => array(
						'value' => 'fadeInDownBig',
						'type' => 'in',
					),
					esc_html__( 'fadeInLeft', 'js_composer' ) => array(
						'value' => 'fadeInLeft',
						'type' => 'in',
					),
					esc_html__( 'fadeInLeftBig', 'js_composer' ) => array(
						'value' => 'fadeInLeftBig',
						'type' => 'in',
					),
					esc_html__( 'fadeInRight', 'js_composer' ) => array(
						'value' => 'fadeInRight',
						'type' => 'in',
					),
					esc_html__( 'fadeInRightBig', 'js_composer' ) => array(
						'value' => 'fadeInRightBig',
						'type' => 'in',
					),
					esc_html__( 'fadeInUp', 'js_composer' ) => array(
						'value' => 'fadeInUp',
						'type' => 'in',
					),
					esc_html__( 'fadeInUpBig', 'js_composer' ) => array(
						'value' => 'fadeInUpBig',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => esc_html__( 'Fading Exits', 'js_composer' ),
				'values' => array(
					esc_html__( 'fadeOut', 'js_composer' ) => array(
						'value' => 'fadeOut',
						'type' => 'out',
					),
					esc_html__( 'fadeOutDown', 'js_composer' ) => array(
						'value' => 'fadeOutDown',
						'type' => 'out',
					),
					esc_html__( 'fadeOutDownBig', 'js_composer' ) => array(
						'value' => 'fadeOutDownBig',
						'type' => 'out',
					),
					esc_html__( 'fadeOutLeft', 'js_composer' ) => array(
						'value' => 'fadeOutLeft',
						'type' => 'out',
					),
					esc_html__( 'fadeOutLeftBig', 'js_composer' ) => array(
						'value' => 'fadeOutLeftBig',
						'type' => 'out',
					),
					esc_html__( 'fadeOutRight', 'js_composer' ) => array(
						'value' => 'fadeOutRight',
						'type' => 'out',
					),
					esc_html__( 'fadeOutRightBig', 'js_composer' ) => array(
						'value' => 'fadeOutRightBig',
						'type' => 'out',
					),
					esc_html__( 'fadeOutUp', 'js_composer' ) => array(
						'value' => 'fadeOutUp',
						'type' => 'out',
					),
					esc_html__( 'fadeOutUpBig', 'js_composer' ) => array(
						'value' => 'fadeOutUpBig',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Flippers', 'js_composer' ),
				'values' => array(
					esc_html__( 'flip', 'js_composer' ) => array(
						'value' => 'flip',
						'type' => 'other',
					),
					esc_html__( 'flipInX', 'js_composer' ) => array(
						'value' => 'flipInX',
						'type' => 'in',
					),
					esc_html__( 'flipInY', 'js_composer' ) => array(
						'value' => 'flipInY',
						'type' => 'in',
					),
					esc_html__( 'flipOutX', 'js_composer' ) => array(
						'value' => 'flipOutX',
						'type' => 'out',
					),
					esc_html__( 'flipOutY', 'js_composer' ) => array(
						'value' => 'flipOutY',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Lightspeed', 'js_composer' ),
				'values' => array(
					esc_html__( 'lightSpeedIn', 'js_composer' ) => array(
						'value' => 'lightSpeedIn',
						'type' => 'in',
					),
					esc_html__( 'lightSpeedOut', 'js_composer' ) => array(
						'value' => 'lightSpeedOut',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Rotating Entrances', 'js_composer' ),
				'values' => array(
					esc_html__( 'rotateIn', 'js_composer' ) => array(
						'value' => 'rotateIn',
						'type' => 'in',
					),
					esc_html__( 'rotateInDownLeft', 'js_composer' ) => array(
						'value' => 'rotateInDownLeft',
						'type' => 'in',
					),
					esc_html__( 'rotateInDownRight', 'js_composer' ) => array(
						'value' => 'rotateInDownRight',
						'type' => 'in',
					),
					esc_html__( 'rotateInUpLeft', 'js_composer' ) => array(
						'value' => 'rotateInUpLeft',
						'type' => 'in',
					),
					esc_html__( 'rotateInUpRight', 'js_composer' ) => array(
						'value' => 'rotateInUpRight',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => esc_html__( 'Rotating Exits', 'js_composer' ),
				'values' => array(
					esc_html__( 'rotateOut', 'js_composer' ) => array(
						'value' => 'rotateOut',
						'type' => 'out',
					),
					esc_html__( 'rotateOutDownLeft', 'js_composer' ) => array(
						'value' => 'rotateOutDownLeft',
						'type' => 'out',
					),
					esc_html__( 'rotateOutDownRight', 'js_composer' ) => array(
						'value' => 'rotateOutDownRight',
						'type' => 'out',
					),
					esc_html__( 'rotateOutUpLeft', 'js_composer' ) => array(
						'value' => 'rotateOutUpLeft',
						'type' => 'out',
					),
					esc_html__( 'rotateOutUpRight', 'js_composer' ) => array(
						'value' => 'rotateOutUpRight',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Specials', 'js_composer' ),
				'values' => array(
					esc_html__( 'hinge', 'js_composer' ) => array(
						'value' => 'hinge',
						'type' => 'out',
					),
					esc_html__( 'rollIn', 'js_composer' ) => array(
						'value' => 'rollIn',
						'type' => 'in',
					),
					esc_html__( 'rollOut', 'js_composer' ) => array(
						'value' => 'rollOut',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Zoom Entrances', 'js_composer' ),
				'values' => array(
					esc_html__( 'zoomIn', 'js_composer' ) => array(
						'value' => 'zoomIn',
						'type' => 'in',
					),
					esc_html__( 'zoomInDown', 'js_composer' ) => array(
						'value' => 'zoomInDown',
						'type' => 'in',
					),
					esc_html__( 'zoomInLeft', 'js_composer' ) => array(
						'value' => 'zoomInLeft',
						'type' => 'in',
					),
					esc_html__( 'zoomInRight', 'js_composer' ) => array(
						'value' => 'zoomInRight',
						'type' => 'in',
					),
					esc_html__( 'zoomInUp', 'js_composer' ) => array(
						'value' => 'zoomInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => esc_html__( 'Zoom Exits', 'js_composer' ),
				'values' => array(
					esc_html__( 'zoomOut', 'js_composer' ) => array(
						'value' => 'zoomOut',
						'type' => 'out',
					),
					esc_html__( 'zoomOutDown', 'js_composer' ) => array(
						'value' => 'zoomOutDown',
						'type' => 'out',
					),
					esc_html__( 'zoomOutLeft', 'js_composer' ) => array(
						'value' => 'zoomOutLeft',
						'type' => 'out',
					),
					esc_html__( 'zoomOutRight', 'js_composer' ) => array(
						'value' => 'zoomOutRight',
						'type' => 'out',
					),
					esc_html__( 'zoomOutUp', 'js_composer' ) => array(
						'value' => 'zoomOutUp',
						'type' => 'out',
					),
				),
			),
			array(
				'label' => esc_html__( 'Slide Entrances', 'js_composer' ),
				'values' => array(
					esc_html__( 'slideInDown', 'js_composer' ) => array(
						'value' => 'slideInDown',
						'type' => 'in',
					),
					esc_html__( 'slideInLeft', 'js_composer' ) => array(
						'value' => 'slideInLeft',
						'type' => 'in',
					),
					esc_html__( 'slideInRight', 'js_composer' ) => array(
						'value' => 'slideInRight',
						'type' => 'in',
					),
					esc_html__( 'slideInUp', 'js_composer' ) => array(
						'value' => 'slideInUp',
						'type' => 'in',
					),
				),
			),
			array(
				'label' => esc_html__( 'Slide Exits', 'js_composer' ),
				'values' => array(
					esc_html__( 'slideOutDown', 'js_composer' ) => array(
						'value' => 'slideOutDown',
						'type' => 'out',
					),
					esc_html__( 'slideOutLeft', 'js_composer' ) => array(
						'value' => 'slideOutLeft',
						'type' => 'out',
					),
					esc_html__( 'slideOutRight', 'js_composer' ) => array(
						'value' => 'slideOutRight',
						'type' => 'out',
					),
					esc_html__( 'slideOutUp', 'js_composer' ) => array(
						'value' => 'slideOutUp',
						'type' => 'out',
					),
				),
			),
		);

		/**
		 * Used to override animation style list
		 * @since 4.4
		 */

		return apply_filters( 'vc_param_animation_style_list', $styles );
	}

	/**
	 * @param array $styles - array of styles to group
	 * @param string|array $type - what type to return
	 *
	 * @return array
	 * @since 4.4
	 */
	public function groupStyleByType( $styles, $type ) {
		$grouped = array();
		foreach ( $styles as $group ) {
			$inner_group = array( 'values' => array() );
			if ( isset( $group['label'] ) ) {
				$inner_group['label'] = $group['label'];
			}
			foreach ( $group['values'] as $key => $value ) {
				if ( ( is_array( $value ) && isset( $value['type'] ) && ( ( is_string( $type ) && $value['type'] === $type ) || is_array( $type ) && in_array( $value['type'], $type, true ) ) ) || ! is_array( $value ) || ! isset( $value['type'] ) ) {
					$inner_group['values'][ $key ] = $value;
				}
			}
			if ( ! empty( $inner_group['values'] ) ) {
				$grouped[] = $inner_group;
			}
		}

		return $grouped;
	}

	/**
	 * Set variables and register animate-css asset
	 * @param $settings
	 * @param $value
	 * @since 4.4
	 *
	 */
	public function __construct( $settings, $value ) {
		$this->settings = $settings;
		$this->value = $value;
		wp_register_style( 'vc_animate-css', vc_asset_url( 'lib/bower/animate-css/animate.min.css' ), array(), WPB_VC_VERSION );
	}

	/**
	 * Render edit form output
	 * @return string
	 * @since 4.4
	 */
	public function render() {
		$output = '<div class="vc_row">';
		wp_enqueue_style( 'vc_animate-css' );

		$styles = $this->animationStyles();
		if ( isset( $this->settings['settings']['type'] ) ) {
			$styles = $this->groupStyleByType( $styles, $this->settings['settings']['type'] );
		}
		if ( isset( $this->settings['settings']['custom'] ) && is_array( $this->settings['settings']['custom'] ) ) {
			$styles = array_merge( $styles, $this->settings['settings']['custom'] );
		}

		if ( is_array( $styles ) && ! empty( $styles ) ) {
			$left_side = '<div class="vc_col-sm-6">';
			$build_style_select = '<select class="vc_param-animation-style">';
			foreach ( $styles as $style ) {
				$build_style_select .= '<optgroup ' . ( isset( $style['label'] ) ? 'label="' . esc_attr( $style['label'] ) . '"' : '' ) . '>';
				if ( is_array( $style['values'] ) && ! empty( $style['values'] ) ) {
					foreach ( $style['values'] as $key => $value ) {
						$selected = '';
						$option_value = is_array( $value ) ? $value['value'] : $value;
						if ( $option_value === $this->value ) {
							$selected = 'selected="selected"';
						}
						$build_style_select .= '<option value="' . ( $option_value ) . '" ' . $selected . '>' . esc_html( $key ) . '</option>';
					}
				}
				$build_style_select .= '</optgroup>';
			}
			$build_style_select .= '</select>';
			$left_side .= $build_style_select;
			$left_side .= '</div>';
			$output .= $left_side;

			$right_side = '<div class="vc_col-sm-6">';
			$right_side .= '<div class="vc_param-animation-style-preview"><button class="vc_btn-grey vc_general vc_param-animation-style-trigger vc_ui-button vc_ui-button-shape-rounded">' . esc_html__( 'Animate it', 'js_composer' ) . '</button></div>';
			$right_side .= '</div>';
			$output .= $right_side;
		}

		$output .= '</div>'; // Close Row
		$output .= sprintf( '<input name="%s" class="wpb_vc_param_value  %s %s_field" type="hidden" value="%s"  />', esc_attr( $this->settings['param_name'] ), esc_attr( $this->settings['param_name'] ), esc_attr( $this->settings['type'] ), $this->value );

		return $output;
	}
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered 'values'.
 *
 * @param array $settings - parameter settings in vc_map
 * @param string $value - parameter value
 * @param string $tag - shortcode tag
 *
 * vc_filter: vc_animation_style_render_filter - filter to override editor form
 *     field output
 *
 * @return mixed rendered template for params in edit form
 *
 * @since 4.4
 */
function vc_animation_style_form_field( $settings, $value, $tag ) {

	$field = new Vc_ParamAnimation( $settings, $value );

	/**
	 * Filter used to override full output of edit form field animation style
	 * @since 4.4
	 */

	return apply_filters( 'vc_animation_style_render_filter', $field->render(), $settings, $value, $tag );
}

