<?php
/**
 * Add-on Name: Stats Counter for WPBakery Page Builder
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Stats Counter
 */

if ( ! class_exists( 'Ultimate_VC_Addons_Stats_Counter' ) ) {
	/**
	 * Function that initializes Stats Counter Module
	 *
	 * @class Ultimate_VC_Addons_Stats_Counter
	 */
	class Ultimate_VC_Addons_Stats_Counter {

		/**
		 * Constructor function that constructs default values for the Stats Counter module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'counter_init' ) );
			}
			add_action( 'wp_enqueue_scripts', array( $this, 'register_counter_assets' ), 1 );
			add_shortcode( 'stat_counter', array( $this, 'counter_shortcode' ) );
		}
		/**
		 * Function that register styles and scripts for Stats Counter Module.
		 *
		 * @method register_counter_assets
		 */
		public function register_counter_assets() {
			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-vc-addons-stats-counter-js', 'countUp', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-stats-counter-style', 'stats-counter' );
		}
		/**
		 * Function that initializes settings of Stats Counter Module.
		 *
		 * @method counter_init
		 */
		public function counter_init() {
			if ( function_exists( 'vc_map' ) ) {
				// map with visual.
				vc_map(
					array(
						'name'        => __( 'Counter', 'ultimate_vc' ),
						'base'        => 'stat_counter',
						'class'       => 'vc_stats_counter',
						'icon'        => 'vc_icon_stats',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Your milestones, achievements, etc.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use an existing font icon or upload a custom image.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'icon_manager',
								'class'       => '',
								'heading'     => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name'  => 'icon',
								'value'       => '',
								'description' => __( "Click and select icon of your choice. If you can't find the one that suits for your purpose", 'ultimate_vc' ) . ', ' . __( 'you can', 'ultimate_vc' ) . " <a href='admin.php?page=bsf-font-icon-manager' target='_blank' rel='noopener'>" . __( 'add new here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'attach_image',
								'class'       => '',
								'heading'     => __( 'Upload Image Icon:', 'ultimate_vc' ),
								'param_name'  => 'icon_img',
								'value'       => '',
								'description' => __( 'Upload the custom image icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Image Width', 'ultimate_vc' ),
								'param_name'  => 'img_width',
								'value'       => 48,
								'min'         => 16,
								'max'         => 512,
								'suffix'      => 'px',
								'description' => __( 'Provide image width', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'custom' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size of Icon', 'ultimate_vc' ),
								'param_name'  => 'icon_size',
								'value'       => 32,
								'min'         => 12,
								'max'         => 72,
								'suffix'      => 'px',
								'description' => __( 'How big would you like it?', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color',
								'value'       => '#333333',
								'description' => __( 'Give it a nice paint!', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Style', 'ultimate_vc' ),
								'param_name'  => 'icon_style',
								'value'       => array(
									__( 'Simple', 'ultimate_vc' ) => 'none',
									__( 'Circle Background', 'ultimate_vc' ) => 'circle',
									__( 'Square Background', 'ultimate_vc' ) => 'square',
									__( 'Design your own', 'ultimate_vc' ) => 'advanced',
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '#ffffff',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'circle', 'square', 'advanced' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Border Style', 'ultimate_vc' ),
								'param_name'  => 'icon_border_style',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
								'description' => __( 'Select the border style for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'advanced' ),
								),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_border',
								'value'       => '#333333',
								'description' => __( 'Select border color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Width', 'ultimate_vc' ),
								'param_name'  => 'icon_border_size',
								'value'       => 1,
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'px',
								'description' => __( 'Thickness of the border.', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Border Radius', 'ultimate_vc' ),
								'param_name'  => 'icon_border_radius',
								'value'       => 500,
								'min'         => 1,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => __( '0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 500 pixels).', 'ultimate_vc' ),
								'dependency'  => array(
									'element'   => 'icon_border_style',
									'not_empty' => true,
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Background Size', 'ultimate_vc' ),
								'param_name'  => 'icon_border_spacing',
								'value'       => 50,
								'min'         => 0,
								'max'         => 500,
								'suffix'      => 'px',
								'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_style',
									'value'   => array( 'advanced' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Animation', 'ultimate_vc' ),
								'param_name'  => 'icon_animation',
								'value'       => array(
									__( 'No Animation', 'ultimate_vc' ) => '',
									__( 'Swing', 'ultimate_vc' ) => 'swing',
									__( 'Pulse', 'ultimate_vc' ) => 'pulse',
									__( 'Fade In', 'ultimate_vc' ) => 'fadeIn',
									__( 'Fade In Up', 'ultimate_vc' ) => 'fadeInUp',
									__( 'Fade In Down', 'ultimate_vc' ) => 'fadeInDown',
									__( 'Fade In Left', 'ultimate_vc' ) => 'fadeInLeft',
									__( 'Fade In Right', 'ultimate_vc' ) => 'fadeInRight',
									__( 'Fade In Up Long', 'ultimate_vc' ) => 'fadeInUpBig',
									__( 'Fade In Down Long', 'ultimate_vc' ) => 'fadeInDownBig',
									__( 'Fade In Left Long', 'ultimate_vc' ) => 'fadeInLeftBig',
									__( 'Fade In Right Long', 'ultimate_vc' ) => 'fadeInRightBig',
									__( 'Slide In Down', 'ultimate_vc' ) => 'slideInDown',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Slide In Left', 'ultimate_vc' ) => 'slideInLeft',
									__( 'Bounce In', 'ultimate_vc' ) => 'bounceIn',
									__( 'Bounce In Up', 'ultimate_vc' ) => 'bounceInUp',
									__( 'Bounce In Down', 'ultimate_vc' ) => 'bounceInDown',
									__( 'Bounce In Left', 'ultimate_vc' ) => 'bounceInLeft',
									__( 'Bounce In Right', 'ultimate_vc' ) => 'bounceInRight',
									__( 'Rotate In', 'ultimate_vc' ) => 'rotateIn',
									__( 'Light Speed In', 'ultimate_vc' ) => 'lightSpeedIn',
									__( 'Roll In', 'ultimate_vc' ) => 'rollIn',
								),
								'description' => __( 'Like CSS3 Animations? We have several options for you!', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon Position', 'ultimate_vc' ),
								'param_name'  => 'icon_position',
								'value'       => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Right', 'ultimate_vc' ) => 'right',
									__( 'Left', 'ultimate_vc' ) => 'left',
								),
								'description' => __( 'Enter Position of Icon', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Counter Title ', 'ultimate_vc' ),
								'param_name'  => 'counter_title',
								'admin_label' => true,
								'value'       => '',
								'description' => __( 'Enter title for stats counter block', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Counter Value', 'ultimate_vc' ),
								'param_name'  => 'counter_value',
								'value'       => '1250',
								'description' => __( 'Enter number for counter without any special character. You may enter a decimal number. Eg 12.76', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Thousands Separator', 'ultimate_vc' ),
								'param_name'  => 'counter_sep',
								'value'       => ',',
								'description' => __( "Enter character for thousanda separator. e.g. ',' will separate 125000 into 125,000", 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Replace Decimal Point With', 'ultimate_vc' ),
								'param_name'  => 'counter_decimal',
								'value'       => '.',
								'description' => __( "Did you enter a decimal number (Eg - 12.76) The decimal point '.' will be replaced with value that you will enter above.", 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Counter Value Prefix', 'ultimate_vc' ),
								'param_name'  => 'counter_prefix',
								'value'       => '',
								'description' => __( 'Enter prefix for counter value', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Counter Value Suffix', 'ultimate_vc' ),
								'param_name'  => 'counter_suffix',
								'value'       => '',
								'description' => __( 'Enter suffix for counter value', 'ultimate_vc' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Counter rolling time', 'ultimate_vc' ),
								'param_name'  => 'speed',
								'value'       => 3,
								'min'         => 1,
								'max'         => 10,
								'suffix'      => 'seconds',
								'description' => __( 'How many seconds the counter should roll?', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the icon process, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title_text_typography',
								'heading'          => __( 'Counter Title settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper no-top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'title_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'title_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'title_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'title_font_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'param_name'  => 'counter_color_txt',
								'value'       => '',
								'description' => __( 'Select text color for counter title.', 'ultimate_vc' ),
								'group'       => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'desc_text_typography',
								'heading'          => __( 'Counter Value settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'desc_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'desc_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'desc_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'desc_font_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'colorpicker',
								'param_name'  => 'desc_font_color',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'description' => __( 'Select text color for counter digits.', 'ultimate_vc' ),
								'group'       => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'suf_pref_typography',
								'heading'          => __( 'Counter suffix-prefix Value settings', 'ultimate_vc' ),
								'value'            => '',
								'group'            => 'Typography',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'param_name' => 'suf_pref_font',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'suf_pref_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'suf_pref_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'suf_pref_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'colorpicker',
								'param_name'  => 'suf_pref_font_color',
								'heading'     => __( 'Color', 'ultimate_vc' ),
								'description' => __( 'Select text color for counter prefix and suffix.', 'ultimate_vc' ),
								'group'       => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/t23kn' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_stat_counter',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}
		/**
		 * Render function for Stats Counter Module.
		 *
		 * @param array $atts represts module attribuits.
		 * @access public
		 */
		public function counter_shortcode( $atts ) {
			$counter_font                        = '';
			$title_font_color                    = '';
			$suf_pref_typography                 = '';
				$ult_sc_settings                 = shortcode_atts(
					array(
						'icon_type'              => 'selector',
						'icon'                   => '',
						'icon_img'               => '',
						'img_width'              => '48',
						'icon_size'              => '32',
						'icon_color'             => '#333333',
						'icon_style'             => 'none',
						'icon_color_bg'          => '#ffffff',
						'icon_color_border'      => '#333333',
						'icon_border_style'      => '',
						'icon_border_size'       => '1',
						'icon_border_radius'     => '500',
						'icon_border_spacing'    => '50',
						'icon_link'              => '',
						'icon_animation'         => '',
						'counter_title'          => '',
						'counter_value'          => '1250',
						'counter_sep'            => ',',
						'counter_suffix'         => '',
						'counter_prefix'         => '',
						'counter_decimal'        => '.',
						'icon_position'          => 'top',
						'counter_style'          => '',
						'speed'                  => '3',
						'font_size_title'        => '18',
						'font_size_counter'      => '28',
						'counter_color_txt'      => '',
						'title_font'             => '',
						'title_font_style'       => '',
						'title_font_size'        => '',
						'title_font_line_height' => '',
						'desc_font'              => '',
						'desc_font_style'        => '',
						'desc_font_size'         => '',
						'desc_font_color'        => '',
						'desc_font_line_height'  => '',
						'el_class'               => '',
						'suf_pref_font'          => '',
						'suf_pref_font_color'    => '',
						'suf_pref_font_size'     => '',
						'suf_pref_line_height'   => '',
						'suf_pref_font_style'    => '',
						'css_stat_counter'       => '',
					),
					$atts
				);
			$ult_sc_settings['css_stat_counter'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_sc_settings['css_stat_counter'], ' ' ), 'stat_counter', $atts );
			$ult_sc_settings['css_stat_counter'] = esc_attr( $ult_sc_settings['css_stat_counter'] );
			$class                               = '';
			$style                               = '';
			$title_style                         = '';
			$desc_style                          = '';
			$suf_pref_style                      = '';
			$stats_icon                          = do_shortcode( '[just_icon icon_type="' . esc_attr( $ult_sc_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_sc_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_sc_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_sc_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_sc_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_sc_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_sc_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_sc_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_sc_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_sc_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_sc_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_sc_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_sc_settings['icon_border_spacing'] ) . '" icon_link="' . esc_attr( $ult_sc_settings['icon_link'] ) . '" icon_animation="' . esc_attr( $ult_sc_settings['icon_animation'] ) . '"]' );

			/* title */
			if ( '' != $ult_sc_settings['title_font'] ) {
				$font_family  = get_ultimate_font_family( $ult_sc_settings['title_font'] );
				$title_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_sc_settings['title_font_style'] ) {
				$title_style .= get_ultimate_font_style( $ult_sc_settings['title_font_style'] );
			}
			// Responsive param.
			if ( '' != $ult_sc_settings['title_font_size'] ) {
				$ult_sc_settings['font_size_title'] = '';
			}
			if ( is_numeric( $ult_sc_settings['title_font_size'] ) ) {
				$ult_sc_settings['title_font_size'] = 'desktop:' . $ult_sc_settings['title_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_sc_settings['title_font_line_height'] ) ) {
				$ult_sc_settings['title_font_line_height'] = 'desktop:' . $ult_sc_settings['title_font_line_height'] . 'px;';
			}
			$counter_resp_id         = 'counter-responsv-wrap-' . wp_rand( 1000, 9999 );
			$stats_counter_args      = array(
				'target'      => '#' . $counter_resp_id . ' .stats-text', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_sc_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_sc_settings['title_font_line_height'],
				),
			);
			$stats_counter_data_list = get_ultimate_vc_responsive_media_css( $stats_counter_args );

			/* description */
			if ( '' != $ult_sc_settings['desc_font'] ) {
				$font_family = get_ultimate_font_family( $ult_sc_settings['desc_font'] );
				$desc_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_sc_settings['desc_font_style'] ) {
				$desc_style .= get_ultimate_font_style( $ult_sc_settings['desc_font_style'] );
			}
			// Responsive param.
			if ( '' != $ult_sc_settings['desc_font_size'] || '' != $ult_sc_settings['suf_pref_font_size'] ) {
				$ult_sc_settings['font_size_counter'] = '';
			}

			if ( is_numeric( $ult_sc_settings['desc_font_size'] ) ) {
				$ult_sc_settings['desc_font_size'] = 'desktop:' . $ult_sc_settings['desc_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_sc_settings['desc_font_line_height'] ) ) {
				$ult_sc_settings['desc_font_line_height'] = 'desktop:' . $ult_sc_settings['desc_font_line_height'] . 'px;';
			}
			$stats_counter_val_args      = array(
				'target'      => '#' . $counter_resp_id . ' .stats-number', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_sc_settings['desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $desc_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_sc_settings['desc_font_line_height'],
				),
			);
			$stats_counter_val_data_list = get_ultimate_vc_responsive_media_css( $stats_counter_val_args );

			if ( '' != $ult_sc_settings['desc_font_color'] ) {
				$desc_style .= 'color:' . $ult_sc_settings['desc_font_color'] . ';';
			}

			if ( '' !== $ult_sc_settings['counter_color_txt'] ) {
				$counter_color = 'color:' . $ult_sc_settings['counter_color_txt'] . ';';
			} else {
				$counter_color = '';
			}
			if ( '' != $ult_sc_settings['icon_color'] ) {
				$style .= 'color:' . $ult_sc_settings['icon_color'] . ';';
			}
			if ( 'none' !== $ult_sc_settings['icon_animation'] ) {
				$css_trans = 'data-animation="' . esc_attr( $ult_sc_settings['icon_animation'] ) . '" data-animation-delay="03"';
			}
			if ( '' !== $ult_sc_settings['font_size_counter'] ) {
				$counter_font = 'font-size:' . $ult_sc_settings['font_size_counter'] . 'px;';
			}

			$ult_sc_settings['title_font'] = 'font-size:' . $ult_sc_settings['font_size_title'] . 'px;';

			// Responsive param.

			if ( '' != $ult_sc_settings['suf_pref_font'] ) {
				$font_family     = get_ultimate_font_family( $ult_sc_settings['suf_pref_font'] );
				$suf_pref_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_sc_settings['suf_pref_font_style'] ) {
				$suf_pref_style .= get_ultimate_font_style( $ult_sc_settings['suf_pref_font_style'] );
			}
			// Responsive param.

			if ( is_numeric( $ult_sc_settings['suf_pref_font_size'] ) ) {
				$ult_sc_settings['suf_pref_font_size'] = 'desktop:' . $ult_sc_settings['suf_pref_font_size'] . 'px;';
			}

			if ( is_numeric( $ult_sc_settings['suf_pref_line_height'] ) ) {
				$ult_sc_settings['suf_pref_line_height'] = 'desktop:' . $ult_sc_settings['suf_pref_line_height'] . 'px;';
			}
			$stats_counter_sufpref_args      = array(
				'target'      => '#' . $counter_resp_id . ' .mycust', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_sc_settings['suf_pref_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $desc_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_sc_settings['suf_pref_line_height'],
				),
			);
			$stats_counter_sufpref_data_list = get_ultimate_vc_responsive_media_css( $stats_counter_sufpref_args );

			$suf_pref_style .= 'color:' . $ult_sc_settings['suf_pref_font_color'];

			if ( '' != $ult_sc_settings['counter_style'] ) {
				$class = $ult_sc_settings['counter_style'];
				if ( strpos( $ult_sc_settings['counter_style'], 'no_bg' ) ) {
					$style .= 'border:2px solid ' . $counter_icon_bg_color . ';';
				} elseif ( strpos( $ult_sc_settings['counter_style'], 'with_bg' ) ) {
					if ( '' != $counter_icon_bg_color ) {
						$style .= 'background:' . $counter_icon_bg_color . ';';
					}
				}
			}
			if ( '' != $ult_sc_settings['el_class'] ) {
				$class .= ' ' . $ult_sc_settings['el_class'];
			}
			$ic_position = 'stats-' . $ult_sc_settings['icon_position'];
			$ic_class    = 'aio-icon-' . $ult_sc_settings['icon_position'];
			$output      = '<div class="stats-block ' . esc_attr( $ic_position ) . ' ' . esc_attr( $class ) . ' ' . esc_attr( $ult_sc_settings['css_stat_counter'] ) . '">';
				$id      = 'counter_' . uniqid( wp_rand() );
			if ( '' == $ult_sc_settings['counter_sep'] ) {
				$ult_sc_settings['counter_sep'] = 'none';
			}
			if ( '' == $ult_sc_settings['counter_decimal'] ) {
				$ult_sc_settings['counter_decimal'] = 'none';
			}
			if ( 'right' !== $ult_sc_settings['icon_position'] ) {
				$output .= '<div class="' . esc_attr( $ic_class ) . '">' . $stats_icon . '</div>';
			}
				$output .= '<div class="stats-desc" id="' . esc_attr( $counter_resp_id ) . '">';
			if ( '' !== $ult_sc_settings['counter_prefix'] ) {
				$output .= '<div class="counter_prefix mycust ult-responsive" ' . $stats_counter_sufpref_data_list . ' style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $suf_pref_style ) . '">' . $ult_sc_settings['counter_prefix'] . '</div>';
			}
					$output .= '<div id="' . esc_attr( $id ) . '" data-id="' . esc_attr( $id ) . '" ' . $stats_counter_val_data_list . ' class="stats-number ult-responsive" style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $counter_color ) . ' ' . esc_attr( $desc_style ) . '" data-speed="' . esc_attr( $ult_sc_settings['speed'] ) . '" data-counter-value="' . esc_attr( $ult_sc_settings['counter_value'] ) . '" data-separator="' . esc_attr( $ult_sc_settings['counter_sep'] ) . '" data-decimal="' . esc_attr( $ult_sc_settings['counter_decimal'] ) . '">0</div>';
			if ( '' !== $ult_sc_settings['counter_suffix'] ) {
				$output .= '<div class="counter_suffix mycust ult-responsive" ' . $stats_counter_sufpref_data_list . ' style="' . esc_attr( $counter_font ) . ' ' . esc_attr( $suf_pref_style ) . '">' . $ult_sc_settings['counter_suffix'] . '</div>';
			}
					$output .= '<div id="' . $id . '" ' . $stats_counter_data_list . ' class="stats-text ult-responsive" style="' . esc_attr( $ult_sc_settings['title_font'] ) . ' ' . esc_attr( $counter_color ) . ' ' . esc_attr( $title_style ) . '">' . $ult_sc_settings['counter_title'] . '</div>';
				$output     .= '</div>';
			if ( 'right' == $ult_sc_settings['icon_position'] ) {
				$output .= '<div class="' . esc_attr( $ic_class ) . '">' . $stats_icon . '</div>';
			}
			$output   .= '</div>';
			$is_preset = false; // Display settings for Preset.
			if ( isset( $_GET['preset'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
				$is_preset = true;
			}
			if ( $is_preset ) {
				$text = 'array ( ';
				foreach ( $atts as $key => $att ) {
					$text .= '<br/>	\'' . $key . '\' => \'' . $att . '\',';
				}
				if ( '' != $content ) {
					$text .= '<br/>	\'content\' => \'' . $content . '\',';
				}
				$text   .= '<br/>)';
				$output .= '<pre>';
				$output .= $text;
				$output .= '</pre>';
			}
			return $output;
		}
	}
}
if ( class_exists( 'Ultimate_VC_Addons_Stats_Counter' ) ) {
	$aio_stats_counter = new Ultimate_VC_Addons_Stats_Counter();
}
if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Stat_Counter' ) ) {
	/**
	 * Function that checks if the class is exists or not.
	 */
	class WPBakeryShortCode_Stat_Counter extends WPBakeryShortCode {
	}
}
