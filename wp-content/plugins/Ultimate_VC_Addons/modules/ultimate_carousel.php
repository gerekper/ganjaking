<?php
/**
 *  UAVC Ultimate Carousel module file
 *
 *  @package Ultimate_Carousel
 */

if ( ! class_exists( 'Ultimate_Carousel' ) ) {
	/**
	 * Constructor function that constructs default values for the Ultimate Carousel module.
	 *
	 * @method __construct
	 */
	class Ultimate_Carousel {
		/**
		 * Constructor function that constructs default values for the Ultimate Carousel module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'init_carousel_addon' ) );
			}
			add_shortcode( 'ultimate_carousel', array( $this, 'ultimate_carousel_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ultimate_front_scripts' ), 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'custom_param_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'ultimate_admin_scripts' ), 100 );
		}
		/**
		 * Custom css style
		 *
		 * @since ----
		 * @access public
		 */
		public function custom_param_styles() {
			echo '<style type="text/css">
					.items_to_show.vc_shortcode-param {
						background: #E6E6E6;
						padding-bottom: 10px;
					}
					.items_to_show.ult_margin_bottom{
						margin-bottom: 15px;
					}
					.items_to_show.ult_margin_top{
						margin-top: 15px;
					}
				</style>';
		}
		/**
		 * Function for carousel admin script
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_front_scripts() {

			Ultimate_VC_Addons::ultimate_register_script( 'ult-slick', 'slick', false, array( 'jquery' ), ULTIMATE_VERSION, false );
			Ultimate_VC_Addons::ultimate_register_script( 'ult-slick-custom', 'slick-custom', false, array( 'jquery', 'ult-slick' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'ult-slick', 'slick' );
			Ultimate_VC_Addons::ultimate_register_style( 'ult-icons', UAVC_URL . 'assets/css/icons.css', true );
		}
		/**
		 * Function for button admin script
		 *
		 * @since ----
		 * @param mixed $hook for the script.
		 * @access public
		 */
		public function ultimate_admin_scripts( $hook ) {
			if ( 'post.php' == $hook || 'post-new.php' == $hook ) {
				wp_enqueue_style( 'ult-icons', UAVC_URL . 'assets/css/icons.css', null, ULTIMATE_VERSION, 'all' );
			}
		}
		/**
		 * Function to intialize the carusole module
		 *
		 * @since ----
		 * @access public
		 */
		public function init_carousel_addon() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Advanced Carousel', 'ultimate_vc' ),
						'base'                    => 'ultimate_carousel',
						'icon'                    => 'ultimate_carousel',
						'class'                   => 'ultimate_carousel',
						'as_parent'               => array( 'except' => 'ultimate_carousel' ),
						'content_element'         => true,
						'controls'                => 'full',
						'show_settings_on_create' => true,
						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Carousel anything.', 'ultimate_vc' ),
						'params'                  => array(
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Slider Type', 'ultimate_vc' ),
								'param_name' => 'slider_type',
								'value'      => array(
									'Horizontal' => 'horizontal',
									'Vertical'   => 'vertical',
									'Horizontal Full Width' => 'full_width',
								),

								'group'      => 'General',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Slides to Scroll', 'ultimate_vc' ),
								'param_name' => 'slide_to_scroll',
								'value'      => array(
									'All visible'   => 'all',
									'One at a Time' => 'single',
								),

								'group'      => 'General',
							),
							array(
								'type'             => 'text',
								'param_name'       => 'title_text_typography',
								'heading'          => '<p>' . __( 'Items to Show‏ -', 'ultimate_vc' ) . '</p>',
								'value'            => '',
								'edit_field_class' => 'vc_col-sm-12 items_to_show ult_margin_top',
								'group'            => 'General',
							),
							array(
								'type'             => 'number',
								'class'            => '',
								'edit_field_class' => 'vc_col-sm-4 items_to_show ult_margin_bottom',
								'heading'          => __( 'On Desktop', 'ultimate_vc' ),
								'param_name'       => 'slides_on_desk',
								'value'            => '5',
								'min'              => '1',
								'max'              => '25',
								'step'             => '1',

								'group'            => 'General',
							),
							array(
								'type'             => 'number',
								'class'            => '',
								'edit_field_class' => 'vc_col-sm-4 items_to_show ult_margin_bottom',
								'heading'          => __( 'On Tabs', 'ultimate_vc' ),
								'param_name'       => 'slides_on_tabs',
								'value'            => '3',
								'min'              => '1',
								'max'              => '25',
								'step'             => '1',

								'group'            => 'General',
							),
							array(
								'type'             => 'number',
								'class'            => '',
								'edit_field_class' => 'vc_col-sm-4 items_to_show ult_margin_bottom',
								'heading'          => __( 'On Mobile', 'ultimate_vc' ),
								'param_name'       => 'slides_on_mob',
								'value'            => '2',
								'min'              => '1',
								'max'              => '25',
								'step'             => '1',

								'group'            => 'General',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Infinite loop', 'ultimate_vc' ),
								'param_name'  => 'infinite_loop',

								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => __( 'Restart the slider automatically as it passes the last slide.', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency'  => '',
								'default_set' => true,
								'group'       => 'General',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Transition speed', 'ultimate_vc' ),
								'param_name'  => 'speed',
								'value'       => '300',
								'min'         => '100',
								'max'         => '10000',
								'step'        => '100',
								'suffix'      => 'ms',
								'description' => __( 'Speed at which next slide comes.', 'ultimate_vc' ),
								'group'       => 'General',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Autoplay Slides‏', 'ultimate_vc' ),
								'param_name'  => 'autoplay',

								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => __( 'Enable Autoplay', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency'  => '',
								'default_set' => true,
								'group'       => 'General',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Autoplay Speed', 'ultimate_vc' ),
								'param_name' => 'autoplay_speed',
								'value'      => '5000',
								'min'        => '100',
								'max'        => '10000',
								'step'       => '10',
								'suffix'     => 'ms',

								'dependency' => array(
									'element' => 'autoplay',
									'value'   => array( 'on' ),
								),
								'group'      => 'General',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Extra Class', 'ultimate_vc' ),
								'param_name' => 'el_class',
								'value'      => '',

								'group'      => 'General',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Navigation Arrows', 'ultimate_vc' ),
								'param_name'  => 'arrows',

								'value'       => 'show',
								'options'     => array(
									'show' => array(
										'label' => __( 'Display next / previous navigation arrows', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency'  => '',
								'default_set' => true,
								'group'       => 'Navigation',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Arrow Style', 'ultimate_vc' ),
								'param_name' => 'arrow_style',
								'value'      => array(
									'Default'           => 'default',
									'Circle Background' => 'circle-bg',
									'Square Background' => 'square-bg',
									'Circle Border'     => 'circle-border',
									'Square Border'     => 'square-border',
								),

								'dependency' => array(
									'element' => 'arrows',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'arrow_bg_color',
								'value'      => '',

								'dependency' => array(
									'element' => 'arrow_style',
									'value'   => array( 'circle-bg', 'square-bg' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Border Color', 'ultimate_vc' ),
								'param_name' => 'arrow_border_color',
								'value'      => '',

								'dependency' => array(
									'element' => 'arrow_style',
									'value'   => array( 'circle-border', 'square-border' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Size', 'ultimate_vc' ),
								'param_name' => 'border_size',
								'value'      => '2',
								'min'        => '1',
								'max'        => '100',
								'step'       => '1',
								'suffix'     => 'px',

								'dependency' => array(
									'element' => 'arrow_style',
									'value'   => array( 'circle-border', 'square-border' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Arrow Color', 'ultimate_vc' ),
								'param_name' => 'arrow_color',
								'value'      => '#333333',

								'dependency' => array(
									'element' => 'arrows',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Arrow Size', 'ultimate_vc' ),
								'param_name' => 'arrow_size',
								'value'      => '24',
								'min'        => '10',
								'max'        => '75',
								'step'       => '1',
								'suffix'     => 'px',

								'dependency' => array(
									'element' => 'arrows',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'ultimate_navigation',
								'class'      => '',
								'heading'    => __( "Select icon for 'Next Arrow'", 'ultimate_vc' ),
								'param_name' => 'next_icon',
								'value'      => 'ultsl-arrow-right4',

								'dependency' => array(
									'element' => 'arrows',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'ultimate_navigation',
								'class'      => '',
								'heading'    => __( "Select icon for 'Previous Arrow'", 'ultimate_vc' ),
								'param_name' => 'prev_icon',
								'value'      => 'ultsl-arrow-left4',

								'dependency' => array(
									'element' => 'arrows',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Dots Navigation', 'ultimate_vc' ),
								'param_name'  => 'dots',

								'value'       => 'show',
								'options'     => array(
									'show' => array(
										'label' => __( 'Display dot navigation', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency'  => '',
								'default_set' => true,
								'group'       => 'Navigation',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Color of dots', 'ultimate_vc' ),
								'param_name' => 'dots_color',
								'value'      => '#333333',

								'dependency' => array(
									'element' => 'dots',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'ultimate_navigation',
								'class'      => '',
								'heading'    => __( "Select icon for 'Navigation Dots'", 'ultimate_vc' ),
								'param_name' => 'dots_icon',
								'value'      => 'ultsl-record',

								'dependency' => array(
									'element' => 'dots',
									'value'   => array( 'show' ),
								),
								'group'      => 'Navigation',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Item Animation', 'ultimate_vc' ),
								'param_name' => 'item_animation',
								'value'      => array(
									__( 'No Animation', 'ultimate_vc' )       => '',
									__( 'Swing', 'ultimate_vc' )              => 'swing',
									__( 'Pulse', 'ultimate_vc' )              => 'pulse',
									__( 'Fade In', 'ultimate_vc' )            => 'fadeIn',
									__( 'Fade In Up', 'ultimate_vc' )         => 'fadeInUp',
									__( 'Fade In Down', 'ultimate_vc' )       => 'fadeInDown',
									__( 'Fade In Left', 'ultimate_vc' )       => 'fadeInLeft',
									__( 'Fade In Right', 'ultimate_vc' )      => 'fadeInRight',
									__( 'Fade In Up Long', 'ultimate_vc' )    => 'fadeInUpBig',
									__( 'Fade In Down Long', 'ultimate_vc' )  => 'fadeInDownBig',
									__( 'Fade In Left Long', 'ultimate_vc' )  => 'fadeInLeftBig',
									__( 'Fade In Right Long', 'ultimate_vc' ) => 'fadeInRightBig',
									__( 'Slide In Down', 'ultimate_vc' )      => 'slideInDown',
									__( 'Slide In Left', 'ultimate_vc' )      => 'slideInLeft',
									__( 'Slide In Left', 'ultimate_vc' )      => 'slideInLeft',
									__( 'Bounce In', 'ultimate_vc' )          => 'bounceIn',
									__( 'Bounce In Up', 'ultimate_vc' )       => 'bounceInUp',
									__( 'Bounce In Down', 'ultimate_vc' )     => 'bounceInDown',
									__( 'Bounce In Left', 'ultimate_vc' )     => 'bounceInLeft',
									__( 'Bounce In Right', 'ultimate_vc' )    => 'bounceInRight',
									__( 'Rotate In', 'ultimate_vc' )          => 'rotateIn',
									__( 'Light Speed In', 'ultimate_vc' )     => 'lightSpeedIn',
									__( 'Roll In', 'ultimate_vc' )            => 'rollIn',
								),

								'group'      => 'Animation',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Draggable Effect', 'ultimate_vc' ),
								'param_name'  => 'draggable',
								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => __( 'Allow slides to be draggable', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'dependency'  => '',
								'default_set' => true,
								'group'       => 'Advanced',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Touch Move', 'ultimate_vc' ),
								'param_name'  => 'touch_move',

								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => __( 'Enable slide moving with touch', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency'  => array(
									'element' => 'draggable',
									'value'   => array( 'on' ),
								),
								'default_set' => true,
								'group'       => 'Advanced',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'RTL Mode', 'ultimate_vc' ),
								'param_name' => 'rtl',

								'value'      => '',
								'options'    => array(
									'on' => array(
										'label' => __( 'Turn on RTL mode', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency' => '',
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Adaptive Height', 'ultimate_vc' ),
								'param_name' => 'adaptive_height',

								'value'      => '',
								'options'    => array(
									'on' => array(
										'label' => __( 'Turn on Adaptive Height', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),

								'dependency' => '',
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Pause on hover', 'ultimate_vc' ),
								'param_name' => 'pauseohover',

								'value'      => 'on',
								'options'    => array(
									'on' => array(
										'label' => __( 'Pause the slider on hover', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'dependency' => array(
									'element' => 'autoplay',
									'value'   => 'on',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Center mode', 'ultimate_vc' ),
								'param_name'  => 'centermode',

								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => __( 'Enable center mode of the carousel', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'description' => __( "Enables centered view with partial prev/next slides. <br>Animations do not work with center mode.<br>Slides to scroll -> 'All Visible' do not work with center mode.", 'ultimate_vc' ),
								'dependency'  => '',
								'group'       => 'Advanced',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Focus on select', 'ultimate_vc' ),
								'param_name' => 'focusonselect',

								'value'      => 'off',
								'options'    => array(
									'on' => array(
										'label' => __( 'Middle the slide on select', 'ultimate_vc' ),
										'on'    => 'Yes',
										'off'   => 'No',
									),
								),
								'dependency' => array(
									'element' => 'centermode',
									'value'   => 'on',
								),
								'group'      => 'Advanced',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Space between two items', 'ultimate_vc' ),
								'param_name' => 'item_space',
								'value'      => '15',
								'min'        => '0',
								'max'        => '1000',
								'step'       => '1',
								'suffix'     => 'px',

								'group'      => 'Advanced',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/bzyci' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
								'group'            => 'General',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_ad_caraousel',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				); // vc_map.
			}
		}
		/**
		 * Shortcode handler function for  icon block.
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content module content.
		 * @access public
		 */
		public function ultimate_carousel_shortcode( $atts, $content ) {

				$ult_uc_settings = shortcode_atts(
					array(
						'slider_type'        => 'horizontal',
						'slides_on_desk'     => '5',
						'slides_on_tabs'     => '3',
						'slides_on_mob'      => '2',
						'slide_to_scroll'    => 'all',
						'speed'              => '300',
						'infinite_loop'      => 'on',
						'autoplay'           => 'on',
						'autoplay_speed'     => '5000',
						'lazyload'           => '',
						'arrows'             => 'show',
						'dots'               => 'show',
						'dots_icon'          => 'ultsl-record',
						'next_icon'          => 'ultsl-arrow-right4',
						'prev_icon'          => 'ultsl-arrow-left4',
						'dots_color'         => '#333333',
						'arrow_color'        => '#333333',
						'arrow_size'         => '20',
						'arrow_style'        => 'default',
						'arrow_bg_color'     => '',
						'arrow_border_color' => '',
						'border_size'        => '1.5',
						'draggable'          => 'on',
						'swipe'              => 'true',
						'touch_move'         => 'on',
						'rtl'                => '',
						'item_space'         => '15',
						'el_class'           => '',
						'item_animation'     => '',
						'adaptive_height'    => '',
						'css_ad_caraousel'   => '',
						'pauseohover'        => 'on',
						'focusonselect'      => 'off',
						'centermode'         => 'off',
					),
					$atts
				);
			$uid                 = uniqid( wp_rand() );

			$settings     = '';
			$responsive   = '';
			$infinite     = '';
			$dot_display  = '';
			$custom_dots  = '';
			$arr_style    = '';
			$wrap_data    = '';
			$design_style = '';

			$desing_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_uc_settings['css_ad_caraousel'], ' ' ), 'ultimate_carousel', $atts );
			$desing_style = esc_attr( $desing_style );
			if ( 'all' == $ult_uc_settings['slide_to_scroll'] ) {
				$ult_uc_settings['slide_to_scroll'] = $ult_uc_settings['slides_on_desk'];
				$slide_to_tab                       = $ult_uc_settings['slides_on_tabs'];
				$slide_to_mob                       = $ult_uc_settings['slides_on_mob'];
			} else {
				$ult_uc_settings['slide_to_scroll'] = 1;
				$slide_to_tab                       = 1;
				$slide_to_mob                       = 1;
			}

			$arr_style .= 'color:' . $ult_uc_settings['arrow_color'] . '; font-size:' . $ult_uc_settings['arrow_size'] . 'px;';
			if ( 'circle-bg' == $ult_uc_settings['arrow_style'] || 'square-bg' == $ult_uc_settings['arrow_style'] ) {
				$arr_style .= 'background:' . $ult_uc_settings['arrow_bg_color'] . ';';
			} elseif ( 'circle-border' == $ult_uc_settings['arrow_style'] || 'square-border' == $ult_uc_settings['arrow_style'] ) {
				$arr_style .= 'border:' . $ult_uc_settings['border_size'] . 'px solid ' . $ult_uc_settings['arrow_border_color'] . ';';
			}

			if ( 'off' !== $ult_uc_settings['dots'] && '' !== $ult_uc_settings['dots'] ) {
				$settings .= 'dots: true,';
			} else {
				$settings .= 'dots: false,';
			}
			if ( 'off' !== $ult_uc_settings['autoplay'] && '' !== $ult_uc_settings['autoplay'] ) {
				$settings .= 'autoplay: true,';
			}
			if ( '' !== $ult_uc_settings['autoplay_speed'] ) {
				$settings .= 'autoplaySpeed: ' . $ult_uc_settings['autoplay_speed'] . ',';
			}
			if ( '' !== $ult_uc_settings['speed'] ) {
				$settings .= 'speed: ' . $ult_uc_settings['speed'] . ',';
			}
			if ( 'off' !== $ult_uc_settings['infinite_loop'] && '' !== $ult_uc_settings['infinite_loop'] ) {
				$settings .= 'infinite: true,';
			} else {
				$settings .= 'infinite: false,';
			}
			if ( 'off' !== $ult_uc_settings['lazyload'] && '' !== $ult_uc_settings['lazyload'] ) {
				$settings .= 'lazyLoad: true,';
			}

			if ( is_rtl() ) {
				if ( 'off' !== $ult_uc_settings['arrows'] && '' !== $ult_uc_settings['arrows'] ) {
					$settings .= 'arrows: true,';
					$settings .= 'nextArrow: \'<button type="button" role="button" aria-label="Next" style="' . esc_attr( $arr_style ) . '" class="slick-next ' . esc_attr( $ult_uc_settings['arrow_style'] ) . '"><i class="' . esc_attr( $ult_uc_settings['prev_icon'] ) . '"></i></button>\',';
					$settings .= 'prevArrow: \'<button type="button" role="button" aria-label="Previous" style="' . esc_attr( $arr_style ) . '" class="slick-prev ' . esc_attr( $ult_uc_settings['arrow_style'] ) . '"><i class="' . esc_attr( $ult_uc_settings['next_icon'] ) . '"></i></button>\',';
				} else {
					$settings .= 'arrows: false,';
				}
			} else {
				if ( 'off' !== $ult_uc_settings['arrows'] && '' !== $ult_uc_settings['arrows'] ) {
					$settings .= 'arrows: true,';
					$settings .= 'nextArrow: \'<button type="button" role="button" aria-label="Next" style="' . esc_attr( $arr_style ) . '" class="slick-next ' . esc_attr( $ult_uc_settings['arrow_style'] ) . '"><i class="' . esc_attr( $ult_uc_settings['next_icon'] ) . '"></i></button>\',';
					$settings .= 'prevArrow: \'<button type="button" role="button" aria-label="Previous" style="' . esc_attr( $arr_style ) . '" class="slick-prev ' . esc_attr( $ult_uc_settings['arrow_style'] ) . '"><i class="' . esc_attr( $ult_uc_settings['prev_icon'] ) . '"></i></button>\',';
				} else {
					$settings .= 'arrows: false,';
				}
			}

			if ( '' !== $ult_uc_settings['slide_to_scroll'] ) {
				$settings .= 'slidesToScroll:' . $ult_uc_settings['slide_to_scroll'] . ',';
			}
			if ( '' !== $ult_uc_settings['slides_on_desk'] ) {
				$settings .= 'slidesToShow:' . $ult_uc_settings['slides_on_desk'] . ',';
			}
			if ( '' == $ult_uc_settings['slides_on_mob'] ) {
				$ult_uc_settings['slides_on_mob'] = $ult_uc_settings['slides_on_desk'];
				$slide_to_tab                     = $ult_uc_settings['slide_to_scroll'];
			}
			if ( '' == $ult_uc_settings['slides_on_tabs'] ) {
				$ult_uc_settings['slides_on_tabs'] = $ult_uc_settings['slides_on_desk'];
				$slide_to_mob                      = $ult_uc_settings['slide_to_scroll'];
			}
			if ( 'off' !== $ult_uc_settings['draggable'] && '' !== $ult_uc_settings['draggable'] ) {
				$settings .= 'swipe: true,';
				$settings .= 'draggable: true,';
			} else {
				$settings .= 'swipe: false,';
				$settings .= 'draggable: false,';
			}

			if ( 'on' == $ult_uc_settings['touch_move'] ) {
				$settings .= 'touchMove: true,';
			} else {
				$settings .= 'touchMove: false,';
			}

			if ( 'off' !== $ult_uc_settings['rtl'] && '' !== $ult_uc_settings['rtl'] ) {
				$settings .= 'rtl: true,';
				$wrap_data = 'dir="rtl"';
			}

			if ( 'vertical' == $ult_uc_settings['slider_type'] ) {
				$settings .= 'vertical: true,';
			}

			$site_rtl = 'false';
			if ( is_rtl() ) {
				$site_rtl = 'true';
			}
			if ( 'false' === $site_rtl || false === $site_rtl ) {
				$ultimate_rtl_support = get_option( 'ultimate_rtl_support' );
				if ( 'enable' == $ultimate_rtl_support ) {
					$site_rtl = 'true';
				}
			}

			if ( is_rtl() ) {
				$settings .= 'rtl: true,';
			}

			if ( 'on' == $ult_uc_settings['pauseohover'] ) {
				$settings .= 'pauseOnHover: true,';
			} else {
				$settings .= 'pauseOnHover: false,';
			}
			$settings .= 'pauseOnFocus: false,';

			if ( 'on' == $ult_uc_settings['centermode'] ) {
				$settings .= 'centerMode: true,';
			}

			if ( 'on' == $ult_uc_settings['focusonselect'] && 'on' == $ult_uc_settings['centermode'] ) {
				$settings .= 'focusOnSelect: true,';
			}

			if ( 'on' === $ult_uc_settings['adaptive_height'] ) {
				$settings .= 'adaptiveHeight: true,';
			}

			$settings .= 'responsive: [
							{
							  breakpoint: 1026,
							  settings: {
								slidesToShow: ' . $ult_uc_settings['slides_on_desk'] . ',
								slidesToScroll: ' . $ult_uc_settings['slide_to_scroll'] . ', ' . $infinite . ' ' . $dot_display . '
							  }
							},
							{
							  breakpoint: 1025,
							  settings: {
								slidesToShow: ' . $ult_uc_settings['slides_on_tabs'] . ',
								slidesToScroll: ' . $slide_to_tab . '
							  }
							},
							{
							  breakpoint: 760,
							  settings: {
								slidesToShow: ' . $ult_uc_settings['slides_on_mob'] . ',
								slidesToScroll: ' . $slide_to_mob . '
							  }
							}
						],';
			$settings .= 'pauseOnDotsHover: true,';
			if ( 'off' !== $ult_uc_settings['dots_icon'] && '' !== $ult_uc_settings['dots_icon'] ) {
				if ( 'off' !== $ult_uc_settings['dots_color'] && '' !== $ult_uc_settings['dots_color'] ) {
					$custom_dots = 'style= "color:' . esc_attr( $ult_uc_settings['dots_color'] ) . ';"';
				}
				$settings .= 'customPaging: function(slider, i) {
                   return \'<i type="button" ' . $custom_dots . ' class="' . esc_attr( $ult_uc_settings['dots_icon'] ) . '" data-role="none"></i>\';
                },';
			}

			if ( '' == $ult_uc_settings['item_animation'] ) {
				$ult_uc_settings['item_animation'] = 'no-animation';
			}
			ob_start();
			$uniqid = uniqid( wp_rand() );

			echo '<div id="ult-carousel-' . esc_attr( $uniqid ) . '" class="ult-carousel-wrapper ' . esc_attr( $desing_style ) . ' ' . esc_attr( $ult_uc_settings['el_class'] ) . ' ult_' . esc_attr( $ult_uc_settings['slider_type'] ) . '" data-gutter="' . esc_attr( $ult_uc_settings['item_space'] ) . '" data-rtl="' . esc_attr( $site_rtl ) . '" >';
			echo '<div class="ult-carousel-' . esc_attr( $uid ) . ' " ' . esc_attr( $wrap_data ) . '>';
			ultimate_override_shortcodes( $ult_uc_settings );
			echo do_shortcode( $content );
			ultimate_restore_shortcodes();
			echo '</div>';
			echo '</div>';
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					if( typeof jQuery('.ult-carousel-<?php echo esc_attr( $uid ); ?>').slick == "function"){
						$('.ult-carousel-<?php echo esc_attr( $uid ); ?>').slick({<?php echo $settings; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>});
					}
				});
			</script>
			<?php
			return ob_get_clean();
		}
	}

	new Ultimate_Carousel();
	if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Carousel' ) ) {
		/**
		 * Function that initializes Ultimate Animation Module
		 *
		 * @class WPBakeryShortCode_Ultimate_Carousel
		 */
		class WPBakeryShortCode_Ultimate_Carousel extends WPBakeryShortCodesContainer {
		}
	}
}
if ( ! function_exists( 'ultimate_override_shortcodes' ) ) {
	/**
	 * For vc map check
	 *
	 * @since ----
	 * @param mixed $ult_uc_settings composer settings.
	 * @access public
	 */
	function ultimate_override_shortcodes( $ult_uc_settings ) {
		global $shortcode_tags, $_shortcode_tags;
		// Let's make a back-up of the shortcodes.
		$_shortcode_tags = $shortcode_tags;
		// Add any shortcode tags that we shouldn't touch here.
		$disabled_tags = array( '' );
		foreach ( $shortcode_tags as $tag => $cb ) {
			if ( in_array( $tag, $disabled_tags, true ) ) {
				continue;
			}
			// Overwrite the callback function.
			$shortcode_tags[ $tag ]            = 'ultimate_wrap_shortcode_in_div'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$_shortcode_tags['ult_item_space'] = $ult_uc_settings['item_space'];
			$_shortcode_tags['item_animation'] = $ult_uc_settings['item_animation'];
		}
	}
}
// Wrap the output of a shortcode in a div with class "ult-item-wrap".
// The original callback is called from the $_shortcode_tags array.
if ( ! function_exists( 'ultimate_wrap_shortcode_in_div' ) ) {
	/**
	 * Wrap the output of a shortcode in a div with class "ult-item-wrap".
	 *
	 * @since ----
	 * @param array  $attr represts module attribuits.
	 * @param string $content value has been set to null.
	 * @param mixed  $tag fuction call.
	 * @access public
	 */
	function ultimate_wrap_shortcode_in_div( $attr, $content = null, $tag ) {
		global $_shortcode_tags;

		return '<div class="ult-item-wrap" data-animation="animated ' . esc_attr( $_shortcode_tags['item_animation'] ) . '">' . call_user_func( $_shortcode_tags[ $tag ], $attr, $content, $tag ) . '</div>';
	}
}
if ( ! function_exists( 'ultimate_restore_shortcodes' ) ) {
	/**
	 * Restore the original callbacks.
	 *
	 * @since ----

	 * @access public
	 */
	function ultimate_restore_shortcodes() {
		global $shortcode_tags, $_shortcode_tags;
		// Restore the original callbacks.
		if ( isset( $_shortcode_tags ) ) {
			$shortcode_tags = $_shortcode_tags; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}
}
