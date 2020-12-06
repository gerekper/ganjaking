<?php
/**
 * Add-on Name: Swatch Book for WPBakery Page Builder
 * Add-on URI: http://.brainstormforce.com/demos/ultimate/swatch-book
 *
 *  @package Swatch Book
 */

if ( ! class_exists( 'Ultimate_Swatch_Book' ) ) {
	/**
	 * Function that initializes Swatch Book Module
	 *
	 * @class Ultimate_Swatch_Book
	 */
	class Ultimate_Swatch_Book {
		/**
		 * Class instance.
		 *
		 * @access public
		 * @var $swatch_trans_bg_img.
		 */
		public $swatch_trans_bg_img;
		/**
		 * Class instance.
		 *
		 * @access public
		 * @var $swatch_width.
		 */
		public $swatch_width;
		/**
		 * Class instance.
		 *
		 * @access public
		 * @var $swatch_height.
		 */
		public $swatch_height;
		/**
		 * Constructor function that constructs default values for the Swatch Book module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'swatch_book_init' ) );
			}
			add_action( 'wp_enqueue_scripts', array( $this, 'register_swatch_assets' ), 1 );
			if ( function_exists( 'vc_is_inline' ) ) {
				if ( ! vc_is_inline() ) {
					add_shortcode( 'swatch_container', array( $this, 'swatch_container' ) );
					add_shortcode( 'swatch_item', array( $this, 'swatch_item' ) );
				}
			} else {
				add_shortcode( 'swatch_container', array( $this, 'swatch_container' ) );
				add_shortcode( 'swatch_item', array( $this, 'swatch_item' ) );
			}
		}
		/**
		 * Function that register styles and scripts for Swatch Book Module.
		 *
		 * @method register_swatch_assets
		 */
		public function register_swatch_assets() {
			Ultimate_VC_Addons::ultimate_register_script( 'swatchbook-js', 'swatchbook', false, array( 'jquery' ), ULTIMATE_VERSION, false );

			Ultimate_VC_Addons::ultimate_register_style( 'swatchbook-css', 'swatchbook' );
		}
		/**
		 * Function that initializes settings of Swatch Book Module.
		 *
		 * @method swatch_book_init
		 */
		public function swatch_book_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Swatch Book', 'ultimate_vc' ),
						'base'                    => 'swatch_container',
						'class'                   => 'vc_swatch_container',
						'icon'                    => 'vc_swatch_container',
						'category'                => 'Ultimate VC Addons',
						'deprecated'              => '3.13.5',
						'as_parent'               => array( 'only' => 'swatch_item' ),
						'description'             => __( 'Interactive swatch strips.', 'ultimate_vc' ),
						'content_element'         => true,
						'show_settings_on_create' => true,
						'js_view'                 => 'VcColumnView',
						'params'                  => array(
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Swatch Book Style', 'ultimate_vc' ),
								'param_name' => 'swatch_style',
								'value'      => array(
									__( 'Style 1', 'ultimate_vc' ) => 'style-1',
									__( 'Style 2', 'ultimate_vc' ) => 'style-2',
									__( 'Style 3', 'ultimate_vc' ) => 'style-3',
									__( 'Style 4', 'ultimate_vc' ) => 'style-4',
									__( 'Style 5', 'ultimate_vc' ) => 'style-5',
									__( 'Custom Style', 'ultimate_vc' ) => 'custom',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Index of Center Strip', 'ultimate_vc' ),
								'param_name'  => 'swatch_index_center',
								'value'       => 1,
								'min'         => 1,
								'max'         => 100,
								'suffix'      => '',
								'description' => __( 'The index of the “centered” item, the one that will have an angle of 0 degrees when the swatch book is opened', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Space Between Two Swatches', 'ultimate_vc' ),
								'param_name'  => 'swatch_space_degree',
								'value'       => 1,
								'min'         => 1,
								'max'         => 1000,
								'suffix'      => '',
								'description' => __( 'The space between the items (in degrees)', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Transition Speed', 'ultimate_vc' ),
								'param_name'  => 'swatch_trans_speed',
								'value'       => 500,
								'min'         => 1,
								'max'         => 10000,
								'suffix'      => 'ms',
								'description' => __( 'The speed and transition timing functions', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Distance From Open Item To Its Next Sibling', 'ultimate_vc' ),
								'param_name'  => 'swatch_distance_sibling',
								'value'       => 1,
								'min'         => 1,
								'max'         => 10000,
								'suffix'      => '',
								'description' => __( 'Distance From Opened item’s next siblings (neighbor : 4)', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'       => 'Initial Settings',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Swatch book will be initially closed', 'ultimate_vc' ),
								'param_name' => 'swatch_init_closed',
								'value'      => '',
								'options'    => array(
									'closed' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'dependency' => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Index of the item that will be opened initially', 'ultimate_vc' ),
								'param_name' => 'swatch_open_at',
								'value'      => 1,
								'min'        => 1,
								'max'        => 100,
								'suffix'     => '',
								'dependency' => array(
									'element' => 'swatch_style',
									'value'   => 'custom',
								),
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Width', 'ultimate_vc' ),
								'param_name' => 'swatch_width',
								'value'      => 130,
								'min'        => 100,
								'max'        => 1000,
								'suffix'     => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Height', 'ultimate_vc' ),
								'param_name' => 'swatch_height',
								'value'      => 400,
								'min'        => 100,
								'max'        => 1000,
								'suffix'     => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'       => 'ult_img_single',
								'class'      => '',
								'heading'    => __( 'Background Transparent Pattern', 'ultimate_vc' ),
								'param_name' => 'swatch_trans_bg_img',
								'value'      => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Main Strip Title Text', 'ultimate_vc' ),
								'param_name'  => 'swatch_main_strip_text',
								'value'       => '',
								'description' => '',
								'group'       => 'Initial Settings',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Main Strip Highlight Text', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_highlight_text',
								'value'      => '',
								'group'      => 'Initial Settings',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'main_strip_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Advanced Settings',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'main_strip_font_style',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Font Size', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_font_size',
								'value'      => 16,
								'min'        => 1,
								'max'        => 72,
								'suffix'     => 'px',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Font Style', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_font_style',
								'value'      => array(
									__( 'Normal', 'ultimate_vc' ) => 'normal',
									__( 'Bold', 'ultimate_vc' ) => 'bold',
									__( 'Italic', 'ultimate_vc' ) => 'italic',
								),
								'group'      => 'Advanced Settings',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Main Strip Title Color:', 'ultimate_vc' ),
								'param_name'  => 'swatch_main_strip_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Advanced Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Background Color:', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_bg_color',
								'value'      => '',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Highlight Font Size', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_highlight_font_size',
								'value'      => 16,
								'min'        => 1,
								'max'        => 72,
								'suffix'     => 'px',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Highlight Font Weight', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_highlight_font_weight',
								'value'      => array(
									__( 'Normal', 'ultimate_vc' ) => 'normal',
									__( 'Bold', 'ultimate_vc' ) => 'bold',
									__( 'Italic', 'ultimate_vc' ) => 'italic',
								),
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Main Strip Title Highlight Color', 'ultimate_vc' ),
								'param_name' => 'swatch_main_strip_highlight_color',
								'value'      => '',
								'group'      => 'Advanced Settings',
							),
						),
					)
				); // vc_map.

				vc_map(
					array(
						'name'            => __( 'Swatch Book Item', 'ultimate_vc' ),
						'base'            => 'swatch_item',
						'class'           => 'vc_swatch_item',
						'icon'            => 'vc_swatch_item',
						'content_element' => true,
						'as_child'        => array( 'only' => 'swatch_container' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Strip Title Text', 'ultimate_vc' ),
								'param_name' => 'swatch_strip_text',
								'value'      => '',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									'Font Icon Manager' => 'selector',
									'Custom Image Icon' => 'custom',
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
								'type'        => 'ult_img_single',
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
								'min'         => 30,
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
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Strip Title Font Size', 'ultimate_vc' ),
								'param_name' => 'swatch_strip_font_size',
								'value'      => 16,
								'min'        => 1,
								'max'        => 72,
								'suffix'     => 'px',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Strip Title Font Weight', 'ultimate_vc' ),
								'param_name' => 'swatch_strip_font_weight',
								'value'      => array(
									__( 'Normal', 'ultimate_vc' ) => 'normal',
									__( 'Bold', 'ultimate_vc' ) => 'bold',
									__( 'Italic', 'ultimate_vc' ) => 'italic',
								),
								'group'      => 'Advanced Settings',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Strip Title Color:', 'ultimate_vc' ),
								'param_name'  => 'swatch_strip_font_color',
								'value'       => '',
								'description' => '',
								'group'       => 'Advanced Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Strip Title Background Color:', 'ultimate_vc' ),
								'param_name' => 'swatch_strip_title_bg_color',
								'value'      => '',
								'group'      => 'Advanced Settings',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Strip Background Color:', 'ultimate_vc' ),
								'param_name' => 'swatch_strip_bg_color',
								'value'      => '',
								'group'      => 'Advanced Settings',
							),
						),
					)
				); // vc_map.
			}
		}
		/**
		 * Render function for Swatch Book Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function swatch_container( $atts, $content = null ) {
				$ult_swatch_settings = shortcode_atts(
					array(
						'swatch_style'                     => 'style-1',
						'swatch_index_center'              => '1',
						'swatch_space_degree'              => '1',
						'swatch_trans_speed'               => '500',
						'swatch_distance_sibling'          => '1',
						'swatch_init_closed'               => 'on',
						'swatch_open_at'                   => '1',
						'swatch_width'                     => '130',
						'swatch_height'                    => '400',
						'swatch_trans_bg_img'              => '',
						'swatch_main_strip_text'           => '',
						'swatch_main_strip_highlight_text' => '',
						'swatch_main_strip_font_size'      => '16',
						'swatch_main_strip_font_style'     => 'normal',
						'swatch_main_strip_color'          => '',
						'swatch_main_strip_highlight_font_size' => '16',
						'swatch_main_strip_highlight_font_weight' => 'normal',
						'swatch_main_strip_highlight_color' => '',
						'swatch_main_strip_bg_color'       => '',
						'main_strip_font_family'           => '',
						'main_strip_font_style'            => '',
					),
					$atts
				);
			$output                  = '';
			$img                     = '';
			$style                   = '';
			$highlight_style         = '';
			$main_style              = '';
			$uid                     = uniqid();
			if ( '' !== $ult_swatch_settings['swatch_trans_bg_img'] ) {
				$img                       = apply_filters( 'ult_get_img_single', $ult_swatch_settings['swatch_trans_bg_img'], 'url' );
				$this->swatch_trans_bg_img = $ult_swatch_settings['swatch_trans_bg_img'];
				$style                    .= 'background-image: url(' . esc_url( $img ) . ');';
			}
			if ( '' !== $ult_swatch_settings['swatch_width'] ) {
				$style             .= 'width:' . $ult_swatch_settings['swatch_width'] . 'px;';
				$this->swatch_width = $ult_swatch_settings['swatch_width'];
			}
			if ( '' !== $ult_swatch_settings['swatch_height'] ) {
				$style              .= 'height:' . $ult_swatch_settings['swatch_height'] . 'px;';
				$this->swatch_height = $ult_swatch_settings['swatch_height'];
			}

			if ( '' !== $ult_swatch_settings['swatch_main_strip_highlight_font_size'] ) {
				$highlight_style .= 'font-size:' . $ult_swatch_settings['swatch_main_strip_highlight_font_size'] . 'px;';
			}
			if ( '' !== $ult_swatch_settings['swatch_main_strip_highlight_font_weight'] ) {
				$highlight_style .= 'font-weight:' . $ult_swatch_settings['swatch_main_strip_highlight_font_weight'] . ';';
			}
			if ( '' !== $ult_swatch_settings['swatch_main_strip_highlight_color'] ) {
				$highlight_style .= 'color:' . $ult_swatch_settings['swatch_main_strip_highlight_color'] . ';';
			}

			if ( '' != $ult_swatch_settings['main_strip_font_family'] ) {
				$mhfont_family = get_ultimate_font_family( $ult_swatch_settings['main_strip_font_family'] );
				$main_style   .= 'font-family:\'' . $mhfont_family . '\';';
			}
			$main_style .= get_ultimate_font_style( $ult_swatch_settings['main_strip_font_style'] );
			if ( '' !== $ult_swatch_settings['swatch_main_strip_font_size'] ) {
				$main_style .= 'font-size:' . $ult_swatch_settings['swatch_main_strip_font_size'] . 'px;';
			}
			if ( '' !== $ult_swatch_settings['swatch_main_strip_font_style'] ) {
				$main_style .= 'font-weight:' . $ult_swatch_settings['swatch_main_strip_font_style'] . ';';
			}
			if ( '' !== $ult_swatch_settings['swatch_main_strip_color'] ) {
				$main_style .= 'color:' . $ult_swatch_settings['swatch_main_strip_color'] . ';';
			}
			if ( '' !== $ult_swatch_settings['swatch_main_strip_bg_color'] ) {
				$main_style .= 'background:' . $ult_swatch_settings['swatch_main_strip_bg_color'] . ';';
			}

			$output .= '<div id="ulsb-container-' . esc_attr( $uid ) . '" class="ulsb-container ulsb-' . esc_attr( $ult_swatch_settings['swatch_style'] ) . '" style="width:' . esc_attr( $ult_swatch_settings['swatch_width'] ) . 'px; height:' . esc_attr( $ult_swatch_settings['swatch_height'] ) . 'px;">';
			$output .= do_shortcode( $content );
			$output .= '<div class="ulsb-strip highlight-strip" style="' . esc_attr( $style ) . '">';
			$output .= '<h4 class="strip_main_text" style="' . esc_attr( $main_style ) . '"><span>' . $ult_swatch_settings['swatch_main_strip_text'] . '</span></h4>';
			$output .= '<h5 class="strip_highlight_text" style="' . esc_attr( $highlight_style ) . '"><span>' . $ult_swatch_settings['swatch_main_strip_highlight_text'] . '</span></h5>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<script type="text/javascript">
						jQuery(function() {';
			if ( 'style-1' == $ult_swatch_settings['swatch_style'] ) {
					$output .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook();';
			}
			if ( 'style-2' == $ult_swatch_settings['swatch_style'] ) {
					$output .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook( {
									angleInc : -10,
									proximity : -45,
									neighbor : -4,
									closeIdx : 11
								} );';
			}
			if ( 'style-3' == $ult_swatch_settings['swatch_style'] ) {
					$output .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook( {
									angleInc : 15,
									neighbor : 15,
									initclosed : true,
									closeIdx : 11
								} );';
			}
			if ( 'style-4' == $ult_swatch_settings['swatch_style'] ) {
					$output .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook( {
									speed : 500,
									easing : "ease-out",
									center : 7,
									angleInc : 14,
									proximity : 40,
									neighbor : 2
								} );';
			}
			if ( 'style-5' == $ult_swatch_settings['swatch_style'] ) {
					$output .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook( {	openAt : 0	} );';
			}
			if ( 'custom' == $ult_swatch_settings['swatch_style'] ) {
				$swatch_options = '';
				if ( '' !== $ult_swatch_settings['swatch_trans_speed'] ) {
					$swatch_options .= 'speed : ' . esc_attr( $ult_swatch_settings['swatch_trans_speed'] ) . ',';
				}
				if ( '' !== $ult_swatch_settings['swatch_index_center'] ) {
					$swatch_options .= 'center : ' . esc_attr( $ult_swatch_settings['swatch_index_center'] ) . ',';
				}
				if ( '' !== $ult_swatch_settings['swatch_space_degree'] ) {
					$swatch_options .= 'angleInc : ' . esc_attr( $ult_swatch_settings['swatch_space_degree'] ) . ',';
				}
				if ( '' !== $ult_swatch_settings['swatch_distance_sibling'] ) {
					$swatch_options .= 'neighbor : ' . esc_attr( $ult_swatch_settings['swatch_distance_sibling'] ) . ',';
				}
				if ( '' !== $ult_swatch_settings['swatch_open_at'] ) {
					$swatch_options .= 'openAt : ' . esc_attr( $ult_swatch_settings['swatch_open_at'] ) . ',';
				}
				if ( 'on' === $ult_swatch_settings['swatch_init_closed'] ) {
					$ult_swatch_settings['swatch_init_closed'] = 'true';
				} else {
					$ult_swatch_settings['swatch_init_closed'] = 'false';
				}
					$swatch_options .= 'closeIdx : ' . esc_attr( $ult_swatch_settings['swatch_init_closed'] ) . ',';
					$output         .= 'jQuery( "#ulsb-container-' . esc_attr( $uid ) . '" ).swatchbook( {
									' . $swatch_options . '
									easing : "ease-out",
									proximity : 40,
								} );';
			}
			$output .= '});';
			$output .= 'jQuery(document).ready(function(e) {
						var ult_strip = jQuery(".highlight-strip");
						ult_strip.each(function(index, element) {
							var strip_main_text = jQuery(this).children(".strip_main_text").outerHeight();
							var height = ' . esc_attr( $ult_swatch_settings['swatch_height'] ) . '-strip_main_text;
							jQuery(this).children(".strip_highlight_text").css("height",height);
						});
					});';
			$output .= '</script>';
			return $output;
		}
		/**
		 * Render function for Swatch Book Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function swatch_item( $atts, $content = null ) {
				$ult_swatcht_settings = shortcode_atts(
					array(
						'swatch_strip_text'           => '',
						'icon_type'                   => '',
						'icon'                        => '',
						'icon_img'                    => '',
						'img_width'                   => '',
						'icon_size'                   => '',
						'icon_color'                  => '',
						'icon_style'                  => '',
						'icon_color_bg'               => '',
						'icon_color_border'           => '',
						'icon_border_style'           => '',
						'icon_border_size'            => '',
						'icon_border_radius'          => '',
						'icon_border_spacing'         => '',
						'icon_animation'              => '',
						'swatch_strip_font_size'      => '',
						'swatch_strip_font_weight'    => '',
						'swatch_strip_font_color'     => '',
						'swatch_strip_bg_color'       => '',
						'swatch_strip_title_bg_color' => '',
						'el_class'                    => '',
					),
					$atts
				);
			$output                   = '';
			$box_icon                 = do_shortcode( '[just_icon icon_type="' . esc_attr( $ult_swatcht_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_swatcht_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_swatcht_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_swatcht_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_swatcht_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_swatcht_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_swatcht_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_swatcht_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_swatcht_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_swatcht_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_swatcht_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_swatcht_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_swatcht_settings['icon_border_spacing'] ) . '" icon_animation="' . esc_attr( $ult_swatcht_settings['icon_animation'] ) . '"]' );
			$style                    = '';
			if ( '' !== $this->swatch_trans_bg_img ) {
				$img    = apply_filters( 'ult_get_img_single', $this->swatch_trans_bg_img, 'url' );
				$style .= 'background-image: url(' . esc_url( $img ) . ');';
			}
			if ( '' !== $ult_swatcht_settings['swatch_strip_bg_color'] ) {
				$style .= 'background-color: ' . esc_attr( $ult_swatcht_settings['swatch_strip_bg_color'] ) . ';';
			}
			if ( '' !== $this->swatch_width ) {
				$style .= 'width:' . esc_attr( $this->swatch_width ) . 'px;';
			}
			if ( '' !== $this->swatch_height ) {
				$style .= 'height:' . esc_attr( $this->swatch_height ) . 'px;';
			}
			$output .= '<div class="ulsb-strip ' . esc_attr( $ult_swatcht_settings['el_class'] ) . '" style="' . esc_attr( $style ) . '">';
			$output .= '<span class="ulsb-icon">' . $box_icon . '</span>';
			$output .= '<h4 style="color:' . esc_attr( $ult_swatcht_settings['swatch_strip_font_color'] ) . '; background:' . esc_attr( $ult_swatcht_settings['swatch_strip_title_bg_color'] ) . '; font-size:' . esc_attr( $ult_swatcht_settings['swatch_strip_font_size'] ) . 'px; font-style: ' . esc_attr( $ult_swatcht_settings['swatch_strip_font_weight'] ) . ';"><span>' . $ult_swatcht_settings['swatch_strip_text'] . '</span></h4>';
			$output .= '</div>';
			return $output;
		}
	}
}


global $ultimate_swatch_book;
$ultimate_swatch_book = new Ultimate_Swatch_Book();
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	if ( ! class_exists( 'WPBakeryShortCode_Swatch_Container' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Swatch_Container extends WPBakeryShortCodesContainer {
			/**
			 * Extended the class of the WPBakeryShortCodesContainer.
			 *
			 * @param array  $atts represts module attribuits.
			 * @param string $content value has been set to null.
			 * @access public
			 */
			public function content( $atts, $content = null ) {
				global $ultimate_swatch_book;
				return $ultimate_swatch_book->swatch_ocntainer( $atts, $content );
			}
		}
	}
	if ( ! class_exists( 'WPBakeryShortCode_Swatch_Item' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Swatch_Item extends WPBakeryShortCode {
			/**
			 * Extended the class of the WPBakeryShortCodesContainer.
			 *
			 * @param array  $atts represts module attribuits.
			 * @param string $content value has been set to null.
			 * @access public
			 */
			public function content( $atts, $content = null ) {
				global $ultimate_swatch_book;
				return $ultimate_swatch_book->swatch_item( $atts, $content );
			}
		}
	}
}
