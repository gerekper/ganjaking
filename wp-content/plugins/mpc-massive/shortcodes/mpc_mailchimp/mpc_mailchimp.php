<?php
/*----------------------------------------------------------------------------*\
	MAILCHIMP SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Mailchimp' ) ) {
	class MPC_Mailchimp {
		public $shortcode = 'mpc_mailchimp';

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'   => '',
				'preset'  => '',
				'form_id' => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'border_css'  => '',
				'padding_css' => '',

				'section_padding_css' => '',

				'label_font_preset'      => '',
				'label_font_color'       => '',
				'label_font_size'        => '',
				'label_font_line_height' => '',
				'label_font_align'       => '',
				'label_font_transform'   => '',

				'label_margin_css'  => '',

				'input_background_color' => '',

				'input_font_preset'      => '',
				'input_font_color'       => '',
				'input_font_size'        => '',
				'input_font_line_height' => '',
				'input_font_align'       => '',
				'input_font_transform'   => '',

				'input_placeholder_color' => '',

				'input_border_css'  => '',
				'input_padding_css' => '',

				'input_active_color'            => '',
				'input_active_background_color' => '',
				'input_active_border_color'     => '',

				'radio_font_preset'      => '',
				'radio_font_color'       => '',
				'radio_font_size'        => '',
				'radio_font_line_height' => '',
				'radio_font_align'       => '',
				'radio_font_transform'   => '',

				'submit_font_preset'      => '',
				'submit_font_color'       => '',
				'submit_font_size'        => '',
				'submit_font_line_height' => '',
				'submit_font_align'       => '',
				'submit_font_transform'   => '',

				'submit_border_css'  => '',
				'submit_margin_css'  => '',
				'submit_padding_css' => '',

				'submit_fullwidth' => '',
				'submit_align'     => 'left',

				'submit_background_type'       => 'color',
				'submit_background_color'      => '',
				'submit_background_image'      => '',
				'submit_background_image_size' => 'large',
				'submit_background_repeat'     => 'no-repeat',
				'submit_background_size'       => 'initial',
				'submit_background_position'   => 'middle-center',
				'submit_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'submit_active_color'        => '',
				'submit_active_border_color' => '',

				'submit_active_background_type'       => 'color',
				'submit_active_background_color'      => '',
				'submit_active_background_image'      => '',
				'submit_active_background_image_size' => 'large',
				'submit_active_background_repeat'     => 'no-repeat',
				'submit_active_background_size'       => 'initial',
				'submit_active_background_position'   => 'middle-center',
				'submit_active_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'notice_font_preset'      => '',
				'notice_font_color'       => '',
				'notice_font_size'        => '',
				'notice_font_line_height' => '',
				'notice_font_align'       => '',
				'notice_font_transform'   => '',

				'notice_success_color' => '',
				'notice_error_color'   => '',
			), $atts );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			$classes = ' mpc-init mpc-transition';
//			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'submit_fullwidth' ] == '' ? ' mpc-submit--small' : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$typography_data = $atts[ 'label_font_preset' ] != '' ? ' data-typo-label="mpc-typography--' . esc_attr( $atts[ 'label_font_preset' ] ) . '"' : '';
			$typography_data .= $atts[ 'input_font_preset' ] != '' ? ' data-typo-input="mpc-typography--' . esc_attr( $atts[ 'input_font_preset' ] ) . '"' : '';
			$typography_data .= $atts[ 'radio_font_preset' ] != '' ? ' data-typo-radio="mpc-typography--' . esc_attr( $atts[ 'radio_font_preset' ] ) . '"' : '';
			$typography_data .= $atts[ 'submit_font_preset' ] != '' ? ' data-typo-submit="mpc-typography--' . esc_attr( $atts[ 'submit_font_preset' ] ) . '"' : '';

			$submit_align = $atts[ 'submit_fullwidth' ] == '' ? ' data-align="' . esc_attr( $atts[ 'submit_align' ] ) . '"' : '';

			$return = '<div data-id="' . $css_id . '" class="mpc-mailchimp' . $classes . '"' . $typography_data . $submit_align . '>';
				$return .= do_shortcode( '[mc4wp_form id="' . esc_attr( $atts[ 'form_id' ] ) . '"]' );
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_mailchimp-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'label_font_size' ] = $styles[ 'label_font_size' ] != '' ? $styles[ 'label_font_size' ] . ( is_numeric( $styles[ 'label_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'input_font_size' ] = $styles[ 'input_font_size' ] != '' ? $styles[ 'input_font_size' ] . ( is_numeric( $styles[ 'input_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'radio_font_size' ] = $styles[ 'radio_font_size' ] != '' ? $styles[ 'radio_font_size' ] . ( is_numeric( $styles[ 'radio_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'submit_font_size' ] = $styles[ 'submit_font_size' ] != '' ? $styles[ 'submit_font_size' ] . ( is_numeric( $styles[ 'submit_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'notice_font_size' ] = $styles[ 'notice_font_size' ] != '' ? $styles[ 'notice_font_size' ] . ( is_numeric( $styles[ 'notice_font_size' ] ) ? 'px' : '' ) : '';

			// Form
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'section_padding_css' ] ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] .mc4wp-form-fields > p,';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] .mc4wp-response {';
					$style .= $styles[ 'section_padding_css' ];
				$style .= '}';
			}

			// Label
			$inner_styles = array();
			if ( $styles[ 'label_margin_css' ] ) { $inner_styles[] = $styles[ 'label_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'label' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] label:not(.mpc-input-wrap) {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Input
			$inner_styles = array();
			if ( $styles[ 'input_background_color' ] ) { $inner_styles[] = 'background-color:' . $styles[ 'input_background_color' ] . ';'; }
			if ( $styles[ 'input_border_css' ] ) { $inner_styles[] = $styles[ 'input_border_css' ]; }
			if ( $styles[ 'input_padding_css' ] ) { $inner_styles[] = $styles[ 'input_padding_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'input' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input:not([type="submit"]),';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] select {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'input_placeholder_color' ] ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] ::-webkit-input-placeholder {color:' . $styles[ 'input_placeholder_color' ] . ';}';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] ::-moz-placeholder {color:' . $styles[ 'input_placeholder_color' ] . ';}';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] :-ms-input-placeholder {color:' . $styles[ 'input_placeholder_color' ] . ';}';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] ::-ms-input-placeholder {color:' . $styles[ 'input_placeholder_color' ] . ';}';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] :placeholder-shown {color:' . $styles[ 'input_placeholder_color' ] . ';}';
			}

			// Input - Active / Hover
			$inner_styles = array();
			if ( $styles[ 'input_active_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'input_active_color' ] . ';'; }
			if ( $styles[ 'input_active_background_color' ] ) { $inner_styles[] = 'background-color:' . $styles[ 'input_active_background_color' ] . ';'; }
			if ( $styles[ 'input_active_border_color' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'input_active_border_color' ] . ';'; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input:not([type="submit"]):focus,';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input:not([type="submit"]):hover,';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] select:focus,';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] select:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Radio
			$inner_styles = array();
			if ( $temp_style = MPC_CSS::font( $styles, 'radio' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] label.mpc-input-wrap {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Submit
			$inner_styles = array();
			if ( $styles[ 'submit_border_css' ] ) { $inner_styles[] = $styles[ 'submit_border_css' ]; }
			if ( $styles[ 'submit_margin_css' ] ) { $inner_styles[] = $styles[ 'submit_margin_css' ]; }
			if ( $styles[ 'submit_padding_css' ] ) { $inner_styles[] = $styles[ 'submit_padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'submit' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles, 'submit' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input[type="submit"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Submit - Active / Hover
			$inner_styles = array();
			if ( $styles[ 'submit_active_color' ] ) { $inner_styles[] = 'color:' . $styles[ 'submit_active_color' ] . ';'; }
			if ( $styles[ 'submit_active_border_color' ] ) { $inner_styles[] = 'border-color:' . $styles[ 'submit_active_border_color' ] . ';'; }
			if ( $temp_style = MPC_CSS::background( $styles, 'submit_active' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input[type="submit"]:focus,';
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] input[type="submit"]:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Notice
			$inner_styles = array();
			if ( $temp_style = MPC_CSS::font( $styles, 'notice' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] .mc4wp-response {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'notice_success_color' ] ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] .mc4wp-response .mc4wp-success {';
					$style .= 'color:' . $styles[ 'notice_success_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'notice_error_color' ] ) {
				$style .= '.mpc-mailchimp[data-id="' . $css_id . '"] .mc4wp-response .mc4wp-error {';
					$style .= 'color:' . $styles[ 'notice_error_color' ] . ';';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;

			return array(
				'id'  => $css_id,
				'css' => $style,
			);
		}

		/* Map all shortcode options to Visual Composer popup */
		function shortcode_map() {
			if ( ! function_exists( 'vc_map' ) ) {
				return '';
			}

			$forms_list = array( '' => '', );

			if ( function_exists( 'mc4wp_get_forms' ) ) {
				$forms = mc4wp_get_forms();

				if ( ! empty( $forms ) ) {
					foreach ( $forms as $form ) {
						if ( isset( $form->name ) && isset( $form->ID ) ) {
							$forms_list[ $form->name ] = $form->ID;
						}
					}
				}
			}

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Main Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'wide_modal'  => true,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Form', 'mpc' ),
					'param_name'  => 'form_id',
					'admin_label' => true,
					'tooltip'     => __( 'Select form you want to style and display.', 'mpc' ),
					'value'       => $forms_list,
					'std'         => '',
					'description' => __( 'Make sure you are using <a href="https://wordpress.org/plugins/mailchimp-for-wp/" target="_blank">MailChimp for WordPress</a>.', 'mpc' ),
				),
			);

			$input = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background Color', 'mpc' ),
					'param_name'       => 'input_background_color',
					'tooltip'          => __( 'Choose input placeholder color.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Inputs', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
				),
			);

			$input_placeholder = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Placeholder Color', 'mpc' ),
					'param_name'       => 'input_placeholder_color',
					'tooltip'          => __( 'Choose input placeholder color.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Inputs', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$input_active = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Active / Hover', 'mpc' ),
					'param_name'       => 'input_active_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'group'            => __( 'Inputs', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Font Color', 'mpc' ),
					'param_name'       => 'input_active_color',
					'tooltip'          => __( 'If you want to change the color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Inputs', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background Color', 'mpc' ),
					'param_name'       => 'input_active_background_color',
					'tooltip'          => __( 'If you want to change the background color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Inputs', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-color-picker',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'input_active_border_color',
					'tooltip'          => __( 'If you want to change the border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Inputs', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$submit = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Full width', 'mpc' ),
					'param_name'       => 'submit_fullwidth',
					'tooltip'          => __( 'Display submit button at full width.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Submit', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Button Alignment', 'mpc' ),
					'param_name'       => 'submit_align',
					'tooltip'          => __( 'Choose submit button alignment.', 'mpc' ),
					'grid_size'        => 'small',
					'value'            => 'left',
					'group'            => __( 'Submit', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field mpc-first-row',
					'dependency'       => array( 'element' => 'submit_fullwidth', 'value_not_equal_to' => 'true' ),
				),
			);

			$submit_active = array(
				array(
					'type'             => 'mpc_divider',
					'title'            => __( 'Active / Hover', 'mpc' ),
					'param_name'       => 'submit_active_divider',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'group'            => __( 'Submit', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'submit_active_color',
					'tooltip'          => __( 'If you want to change the color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Submit', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'submit_active_border_color',
					'tooltip'          => __( 'If you want to change the border color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Submit', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$notice = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Success Color', 'mpc' ),
					'param_name'       => 'notice_success_color',
					'tooltip'          => __( 'Choose success notice color.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Notice', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Error Color', 'mpc' ),
					'param_name'       => 'notice_error_color',
					'tooltip'          => __( 'Choose error notice color.', 'mpc' ),
					'value'            => '',
					'group'            => __( 'Notice', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();

			$section_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'section', 'subtitle' => __( 'Sections', 'mpc' ) ) );

			$label_font   = MPC_Snippets::vc_font( array( 'prefix' => 'label', 'group' => __( 'Labels', 'mpc' ) ) );
			$label_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'label', 'group' => __( 'Labels', 'mpc' ) ) );

			$input_font       = MPC_Snippets::vc_font( array( 'prefix' => 'input', 'group' => __( 'Inputs', 'mpc' ) ) );
			$input_border     = MPC_Snippets::vc_border( array( 'prefix' => 'input', 'group' => __( 'Inputs', 'mpc' ) ) );
			$input_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'input', 'group' => __( 'Inputs', 'mpc' ) ) );

			$radio_font = MPC_Snippets::vc_font( array( 'prefix' => 'radio', 'group' => __( 'Radios', 'mpc' ) ) );

			$submit_font       = MPC_Snippets::vc_font( array( 'prefix' => 'submit', 'group' => __( 'Submit', 'mpc' ) ) );
			$submit_background = MPC_Snippets::vc_background( array( 'prefix' => 'submit', 'group' => __( 'Submit', 'mpc' ) ) );
			$submit_border     = MPC_Snippets::vc_border( array( 'prefix' => 'submit', 'group' => __( 'Submit', 'mpc' ) ) );
			$submit_margin     = MPC_Snippets::vc_margin( array( 'prefix' => 'submit', 'group' => __( 'Submit', 'mpc' ) ) );
			$submit_padding    = MPC_Snippets::vc_padding( array( 'prefix' => 'submit', 'group' => __( 'Submit', 'mpc' ) ) );

			$submit_active_background = MPC_Snippets::vc_background( array( 'prefix' => 'submit_active', 'group' => __( 'Submit', 'mpc' ), 'subtitle' => __( 'Active / Hover', 'mpc' ) ) );

			$notice_font = MPC_Snippets::vc_font( array( 'prefix' => 'notice', 'group' => __( 'Notice', 'mpc' ) ) );

			$class = MPC_Snippets::vc_class();

			$params = array_merge( $base, $background, $border, $padding, $section_padding, $label_font, $label_margin, $input, $input_font, $input_placeholder, $input_active, $input_border, $input_padding, $radio_font, $submit, $submit_font, $submit_active, $submit_background, $submit_active_background, $submit_border, $submit_padding, $submit_margin, $notice_font, $notice, $class );

			return array(
				'name'        => __( 'MailChimp', 'mpc' ),
				'description' => __( 'MailChimp forms integration', 'mpc' ),
				'base'        => 'mpc_mailchimp',
				'class'       => '',
				'icon'        => 'mpc-shicon-mailchimp',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Mailchimp' ) ) {
	global $MPC_Mailchimp;
	$MPC_Mailchimp = new MPC_Mailchimp;
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_mpc_mailchimp' ) ) {
	class WPBakeryShortCode_mpc_mailchimp extends WPBakeryShortCode {}
}
