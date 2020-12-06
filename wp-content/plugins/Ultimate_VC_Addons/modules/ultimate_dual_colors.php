<?php
/**
 * Add-on Name: Ultimate Dual Color
 * Add-on URI: http://dev.brainstormforce.com
 *
 * @package Ultimate_Dual_Colors.
 */

if ( ! class_exists( 'Ultimate_Dual_Colors' ) ) {
	/**
	 * Ultimate_Dual_Colors.
	 *
	 * @class Ultimate_Dual_Colors.
	 */
	class Ultimate_Dual_Colors {
		/**
		 * Constructor function that constructs default values for the Ultimate_List_Icon.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_dual_colors_module_init' ) );
			}
			add_shortcode( 'ultimate_dual_color', array( $this, 'ultimate_dual_colors_module_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_dual_colors_module_assets' ), 1 );
		}//end __construct()

		/**
		 *  Function Dual color Heading assets.
		 *
		 * @method register_dual_colors_module_assets
		 */
		public function register_dual_colors_module_assets() {

			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-dual-colors-style', 'dual_color' );
		}//end register_dual_colors_module_assets()


		/**
		 *  Function Init.
		 *
		 * @method ultimate_dual_colors_module_init
		 */
		public function ultimate_dual_colors_module_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Dual Color Heading', 'ultimate_vc' ),
						'base'        => 'ultimate_dual_color',
						'class'       => 'vc_ultimate_dual_color',
						'icon'        => 'vc_ultimate_dual_color',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Style your heading.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Heading Text', 'ultimate_vc' ),
								'param_name'       => 'dual_main_heading',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12 no-top-margin',
								'group'            => 'General',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Before Text', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'dual_before_txt',
								'value'       => 'I Love',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link', 'ultimate_vc' ),
								'param_name'  => 'dual_before_link',
								'value'       => '',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Highlighted Text', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'dual_high_txt',
								'value'       => 'this website',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link', 'ultimate_vc' ),
								'param_name'  => 'dual_high_link',
								'value'       => '',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'After Text', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'dual_after_txt',
								'value'       => '',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Link', 'ultimate_vc' ),
								'param_name'  => 'dual_after_link',
								'value'       => '',
								'description' => '',
								'group'       => 'General',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
								'group'       => 'General',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Style', 'ultimate_vc' ),
								'param_name'       => 'dual_main_style',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12 no-top-margin',
								'group'            => 'Style',
							),
							array(
								'type'             => 'dropdown',
								'class'            => '',
								'heading'          => __( 'Alignment', 'ultimate_vc' ),
								'param_name'       => 'dual_color_align',
								'value'            => array(
									'Center Align' => 'center',
									'Left Align'   => 'left',
									'Right Align'  => 'right',
								),
								'description'      => __( 'Alignment option for heading.', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-8',
								'group'            => 'Style',
							),
							array(
								'type'             => 'dropdown',
								'heading'          => __( 'Tag', 'ultimate_vc' ),
								'param_name'       => 'heading_tag',
								'value'            => array(
									__( 'Default', 'ultimate_vc' ) => 'h3',
									__( 'H1', 'ultimate_vc' ) => 'h1',
									__( 'H2', 'ultimate_vc' ) => 'h2',
									__( 'H4', 'ultimate_vc' ) => 'h4',
									__( 'H5', 'ultimate_vc' ) => 'h5',
									__( 'H6', 'ultimate_vc' ) => 'h6',
									__( 'Div', 'ultimate_vc' ) => 'div',
									__( 'p', 'ultimate_vc' )  => 'p',
									__( 'span', 'ultimate_vc' ) => 'span',
								),
								'description'      => __( 'Default is H3', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-4',
								'group'            => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Layout', 'ultimate_vc' ),
								'param_name' => 'dual_color_layout',
								'value'      => array(
									'Inline' => 'inline',
									'Stack'  => 'stack',
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Spacing Between Headings', 'ultimate_vc' ),
								'param_name' => 'dual_color_spacing',
								'value'      => array(
									'No'  => 'no',
									'Yes' => 'yes',
								),
								'dependency' => array(
									'element' => 'dual_color_layout',
									'value'   => 'inline',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Spacing Width', 'ultimate_vc' ),
								'param_name'  => 'dual_color_width',
								'value'       => '',
								'min'         => 10,
								'max'         => 100,
								'suffix'      => 'px',
								'description' => '',
								'dependency'  => array(
									'element' => 'dual_color_spacing',
									'value'   => 'yes',
								),
								'group'       => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Stack on', 'ultimate_vc' ),
								'param_name' => 'dual_color_stack',
								'value'      =>
								array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Desktop', 'ultimate_vc' ) => 'desktop',
									__( 'Tablet', 'ultimate_vc' ) => 'tablet',
									__( 'Mobile', 'ultimate_vc' ) => 'mobile',
								),
								'dependency' => array(
									'element' => 'dual_color_layout',
									'value'   => 'stack',
								),
								'group'      => 'Style',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Before & After settings', 'ultimate_vc' ),
								'param_name'       => 'dual_main_style',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Before & After Text Color', 'ultimate_vc' ),
								'param_name'  => 'dual_ba_color',
								'description' => '',
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Before & After Background Color', 'ultimate_vc' ),
								'param_name'  => 'dual_ba_back_color',
								'description' => '',
								'group'       => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Before & After Border Style', 'ultimate_vc' ),
								'param_name' => 'dual_ba_border',
								'value'      => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'dual_ba_bcolor',
								'value'       => '',
								'description' => '',
								'dependency'  => array(
									'element'   => 'dual_ba_border',
									'not_empty' => true,
								),
								'group'       => 'Style',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Width', 'ultimate_vc' ),
								'param_name' => 'dual_ba_bstyle',
								'value'      => 1,
								'min'        => 1,
								'max'        => 10,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'dual_ba_border',
									'not_empty' => true,
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Radius', 'ultimate_vc' ),
								'param_name' => 'dual_ba_bradius',
								'value'      => 3,
								'min'        => 0,
								'max'        => 500,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'dual_ba_border',
									'not_empty' => true,
								),
								'group'      => 'Style',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Highlighted settings', 'ultimate_vc' ),
								'param_name'       => 'dual_main_style',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Highlighted Text Color', 'ultimate_vc' ),
								'param_name'  => 'dual_high_color',
								'description' => '',
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Highlighted Text Background Color', 'ultimate_vc' ),
								'param_name'  => 'dual_high_back_color',
								'description' => '',
								'group'       => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Highlighted Border Style', 'ultimate_vc' ),
								'param_name' => 'dual_high_border',
								'value'      => array(
									'None'   => '',
									'Solid'  => 'solid',
									'Dashed' => 'dashed',
									'Dotted' => 'dotted',
									'Double' => 'double',
									'Inset'  => 'inset',
									'Outset' => 'outset',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Border Color', 'ultimate_vc' ),
								'param_name'  => 'dual_high_bcolor',
								'value'       => '',
								'description' => '',
								'dependency'  => array(
									'element'   => 'dual_high_border',
									'not_empty' => true,
								),
								'group'       => 'Style',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Width', 'ultimate_vc' ),
								'param_name' => 'dual_high_bstyle',
								'value'      => 1,
								'min'        => 1,
								'max'        => 10,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'dual_high_border',
									'not_empty' => true,
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'number',
								'class'      => '',
								'heading'    => __( 'Border Radius', 'ultimate_vc' ),
								'param_name' => 'dual_high_bradius',
								'value'      => 3,
								'min'        => 0,
								'max'        => 500,
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'dual_high_border',
									'not_empty' => true,
								),
								'group'      => 'Style',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Before & After Text Settings', 'ultimate_vc' ),
								'param_name'       => 'before_typo',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12 no-top-margin',
								'group'            => 'Typography',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'dual_color_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'dual_color_font_style',
								'group'      => 'Typography',
							),

							// Responsive Param.
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'dual_color_font_size',
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
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'dual_color_line_height',
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
								'type'             => 'number',
								'class'            => '',
								'heading'          => __( 'Letter Spacing', 'ultimate_vc' ),
								'param_name'       => 'dual_ba_letter_space',
								'value'            => '',
								'min'              => 1,
								'max'              => 15,
								'suffix'           => 'px',
								'edit_field_class' => 'vc_col-sm-4',
								'group'            => 'Typography',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'dual_ba_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Transform', 'ultimate_vc' ),
								'param_name' => 'dual_ba_transform',
								'value'      => array(
									__( 'Default', 'ultimate_vc' ) => 'unset',
									__( 'UPPERCASE', 'ultimate_vc' ) => 'uppercase',
									__( 'lowercase', 'ultimate_vc' ) => 'lowercase',
									__( 'Capitalize', 'ultimate_vc' ) => 'capitalize',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Highlighted Text Settings', 'ultimate_vc' ),
								'param_name'       => 'high_before_typo',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Typography',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'dual_color_high_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'group'       => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'dual_color_high_font_style',
								'group'      => 'Typography',
							),

							// Responsive Param.
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'dual_color_high_font_size',
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
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'dual_color_high_line_height',
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
								'type'             => 'number',
								'class'            => '',
								'heading'          => __( 'Letter Spacing', 'ultimate_vc' ),
								'param_name'       => 'dual_high_letter_space',
								'value'            => '',
								'min'              => 1,
								'max'              => 15,
								'suffix'           => 'px',
								'edit_field_class' => 'vc_col-sm-4',
								'group'            => 'Typography',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'dual_high_padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'group'      => 'Typography',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Transform', 'ultimate_vc' ),
								'param_name' => 'dual_high_transform',
								'value'      => array(
									__( 'Default', 'ultimate_vc' ) => 'unset',
									__( 'UPPERCASE', 'ultimate_vc' ) => 'uppercase',
									__( 'lowercase', 'ultimate_vc' ) => 'lowercase',
									__( 'Capitalize', 'ultimate_vc' ) => 'capitalize',
								),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_dual_color_design',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}//end ultimate_dual_colors_module_init()

		/**
		 *  Function Dual color Heading shotcode.
		 *
		 *  @param array  $atts Attributes.
		 *  @param string $content Content.
		 * @method ultimate_dual_colors_module_shortcode
		 */
		public function ultimate_dual_colors_module_shortcode( $atts, $content = null ) {
			$dual_align                   = '';
			$dual_ba_inline               = '';
			$dual_high_inline             = '';
			$dual_inlinel_width           = '';
			$dual_inliner_width           = '';
			$href                         = '';
			$target                       = '';
			$link_title                   = '';
			$rel                          = '';
			$url                          = '';
			$blink_prefix                 = '';
			$blink_sufix                  = '';
			$hlink_prefix                 = '';
			$hlink_sufix                  = '';
			$alink_prefix                 = '';
			$alink_sufix                  = '';
			$dcfont_family                = '';
			$dchfont_family               = '';
			$main_dual_color_style_inline = '';
			$main_dual_high_style_inline  = '';
			$main_dual_color_responsive   = '';
			$main_dual_high_responsive    = '';
			$dual_ba_back_inline          = '';
			$dual_high_back_inline        = '';
			$dual_inline_width            = '';
			$dual_ba_trans_inline         = '';
			$dual_high_trans_inline       = '';
			$dual_ba_ls_inline            = '';
			$dual_high_ls_inline          = '';
			$dual_ba_padding_inline       = '';
			$dual_high_padding_inline     = '';
			$dual_high_bcolor             = '';
			$dual_ba_border_inline        = '';
			$dual_high_border_inline      = '';
			$dual_design_style_css        = '';
			$output                       = '';
			$ult_dual_color_settings      = shortcode_atts(
				array(
					'dual_before_txt'             => '',
					'dual_high_txt'               => '',
					'dual_after_txt'              => '',
					'dual_before_link'            => '',
					'dual_high_link'              => '',
					'dual_after_link'             => '',
					'dual_color_align'            => 'center',
					'heading_tag'                 => '',
					'dual_ba_color'               => '',
					'dual_high_color'             => '',
					'dual_color_spacing'          => 'no',
					'dual_color_width'            => '',
					'dual_color_font_family'      => '',
					'dual_color_font_style'       => '',
					'dual_color_font_size'        => '',
					'dual_color_line_height'      => '',
					'dual_color_high_font_family' => '',
					'dual_color_high_font_style'  => '',
					'dual_color_high_font_size'   => '',
					'dual_color_high_line_height' => '',
					'dual_ba_back_color'          => '',
					'dual_high_back_color'        => '',
					'dual_ba_transform'           => '',
					'dual_high_transform'         => '',
					'dual_ba_letter_space'        => '',
					'dual_high_letter_space'      => '',
					'dual_ba_padding'             => '',
					'dual_high_padding'           => '',
					'dual_ba_border'              => '',
					'dual_ba_bcolor'              => '',
					'dual_ba_bstyle'              => '',
					'dual_ba_bradius'             => '',
					'dual_high_border'            => '',
					'dual_high_bcolor'            => '',
					'dual_high_bstyle'            => '',
					'dual_high_bradius'           => '',
					'dual_color_layout'           => 'inline',
					'dual_color_stack'            => 'none',
					'el_class'                    => '',
					'css_dual_color_design'       => '',
				),
				$atts
			);
			$vc_version                   = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus                = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			// Default Design Editor.
			$dual_design_style_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $ult_dual_color_settings['css_dual_color_design'], ' ' ), 'ultimate_ribbons', $atts );

			$dual_design_style_css = esc_attr( $dual_design_style_css );

				$uid = 'ultv-' . wp_rand( 0000, 9999 );

			if ( '' == $ult_dual_color_settings['dual_before_txt'] ) {
				$ult_dual_color_settings['dual_before_txt'] = 'I Love';
			}

			if ( '' == $ult_dual_color_settings['dual_high_txt'] ) {
					$ult_dual_color_settings['dual_high_txt'] = 'this website';
			}
				// Dual-Color Alignment.
			if ( '' != $ult_dual_color_settings['dual_color_align'] ) {
				$dual_align = 'text-align:' . $ult_dual_color_settings['dual_color_align'] . ';';
			}

				// Dual-color Heading tag.
			if ( '' == $ult_dual_color_settings['heading_tag'] ) {
				$ult_dual_color_settings['heading_tag'] = 'h3';
			}

				// Before & after Text color for dual-color.
			if ( '' != $ult_dual_color_settings['dual_ba_color'] ) {
				$dual_ba_inline = 'color:' . $ult_dual_color_settings['dual_ba_color'] . ';';
			}
				// Highlighted text color for dual-color.
			if ( '' != $ult_dual_color_settings['dual_high_color'] ) {
				$dual_high_inline = 'color:' . $ult_dual_color_settings['dual_high_color'] . ';';
			}
				// Before & after Background color for dual-color.
			if ( '' != $ult_dual_color_settings['dual_ba_back_color'] ) {
				$dual_ba_back_inline = 'background-color:' . $ult_dual_color_settings['dual_ba_back_color'] . ';';
			}
				// Highlighted background color for dual-color.
			if ( '' != $ult_dual_color_settings['dual_high_back_color'] ) {
				$dual_high_back_inline = 'background-color:' . $ult_dual_color_settings['dual_high_back_color'] . ';';
			}

				// Text -Transform Property for Before & After heading.
			if ( '' != $ult_dual_color_settings['dual_ba_transform'] ) {
				$dual_ba_trans_inline = 'text-transform: ' . $ult_dual_color_settings['dual_ba_transform'] . ';';
			}

				// Text -Transform Property for Highlighted heading.
			if ( '' != $ult_dual_color_settings['dual_high_transform'] ) {
				$dual_high_trans_inline = 'text-transform: ' . $ult_dual_color_settings['dual_high_transform'] . ';';
			}

				// Letter spacing for Before & After heading.
			if ( '' !== $ult_dual_color_settings['dual_ba_letter_space'] ) {
				$dual_ba_ls_inline = 'letter-spacing:' . $ult_dual_color_settings['dual_ba_letter_space'] . 'px;';
			}

				// Letter spacing for Highlighted heading.
			if ( '' !== $ult_dual_color_settings['dual_high_letter_space'] ) {
				$dual_high_ls_inline = 'letter-spacing:' . $ult_dual_color_settings['dual_high_letter_space'] . 'px;';
			}

				/* Before and after padding */
			if ( '' != $ult_dual_color_settings['dual_ba_padding'] ) {
				$dual_ba_padding_inline = $ult_dual_color_settings['dual_ba_padding'];
			}

				/* Highlighted padding */
			if ( '' != $ult_dual_color_settings['dual_high_padding'] ) {
				$dual_high_padding_inline = $ult_dual_color_settings['dual_high_padding'];
			}
				// Border style for Before & after headings.
			if ( '' !== $ult_dual_color_settings['dual_ba_border'] ) {
				$dual_ba_border_inline .= 'border-radius:' . $ult_dual_color_settings['dual_ba_bradius'] . 'px;';
				$dual_ba_border_inline .= 'border-width:' . $ult_dual_color_settings['dual_ba_bstyle'] . 'px;';
				$dual_ba_border_inline .= 'border-color:' . $ult_dual_color_settings['dual_ba_bcolor'] . ';';
				$dual_ba_border_inline .= 'border-style:' . $ult_dual_color_settings['dual_ba_border'] . ';';
			} else {
				$dual_ba_border_inline .= 'border:none;';
			}

				// Border style for Highlighted headings.
			if ( '' !== $ult_dual_color_settings['dual_high_border'] ) {
				$dual_high_border_inline .= 'border-radius:' . $ult_dual_color_settings['dual_high_bradius'] . 'px;';
				$dual_high_border_inline .= 'border-width:' . $ult_dual_color_settings['dual_high_bstyle'] . 'px;';
				$dual_high_border_inline .= 'border-color:' . $dual_high_bcolor . ';';
				$dual_high_border_inline .= 'border-style:' . $ult_dual_color_settings['dual_high_border'] . ';';
			} else {
				$dual_high_border_inline .= 'border:none;';
			}

			if ( 'yes' == $ult_dual_color_settings['dual_color_spacing'] ) {
				if ( '' != $ult_dual_color_settings['dual_color_width'] ) {
						$dual_inline_width  = 'margin-left:' . $ult_dual_color_settings['dual_color_width'] . 'px;';
						$dual_inline_width .= 'margin-right: ' . $ult_dual_color_settings['dual_color_width'] . 'px;';
				}
			}
			// Link for the Before Text.
			if ( '' !== $ult_dual_color_settings['dual_before_link'] ) {
				$href = vc_build_link( $ult_dual_color_settings['dual_before_link'] );
				if ( '' !== $href['url'] ) {
					$url          = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target       = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$link_title   = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel          = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
					$blink_prefix = '<a class="dual-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' >';
					$blink_sufix  = '</a>';
				}
			}
			// Link for the Highlighted Text.
			if ( '' !== $ult_dual_color_settings['dual_high_link'] ) {
				$href = vc_build_link( $ult_dual_color_settings['dual_high_link'] );
				if ( '' !== $href['url'] ) {
					$url          = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target       = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$link_title   = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel          = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
					$hlink_prefix = '<a class="dual-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' >';
					$hlink_sufix  = '</a>';
				}
			}
			// Link for the after Text.
			if ( '' !== $ult_dual_color_settings['dual_after_link'] ) {
				$href = vc_build_link( $ult_dual_color_settings['dual_after_link'] );
				if ( '' !== $href['url'] ) {
					$url          = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
					$target       = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
					$link_title   = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
					$rel          = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';
					$alink_prefix = '<a class="dual-link" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' >';
					$alink_sufix  = '</a>';
				}
			}

			/* ---- Before and after heading styles ---- */
			if ( '' != $ult_dual_color_settings['dual_color_font_family'] ) {
				$dcfont_family = get_ultimate_font_family( $ult_dual_color_settings['dual_color_font_family'] );
				if ( $dcfont_family ) {
					$main_dual_color_style_inline .= 'font-family:\'' . $dcfont_family . '\';';
				}
			}
			/* ----  Before and after font style---- */
			$main_dual_color_style_inline .= get_ultimate_font_style( $ult_dual_color_settings['dual_color_font_style'] );

			// FIX: Before and after font size before implementing responsive param.
			if ( is_numeric( $ult_dual_color_settings['dual_color_font_size'] ) ) {
				$ult_dual_color_settings['dual_color_font_size'] = 'desktop:' . $ult_dual_color_settings['dual_color_font_size'] . 'px;';     }
			if ( is_numeric( $ult_dual_color_settings['dual_color_line_height'] ) ) {
				$ult_dual_color_settings['dual_color_line_height'] = 'desktop:' . $ult_dual_color_settings['dual_color_line_height'] . 'px;';     }
			// Before and after responsive {main} video styles.
			$args                       = array(
				'target'      => '.ult-dual-color.' . $uid . ' .ult-dual-heading-text',
				'media_sizes' => array(
					'font-size'   => $ult_dual_color_settings['dual_color_font_size'],
					'line-height' => $ult_dual_color_settings['dual_color_line_height'],
				),
			);
			$main_dual_color_responsive = get_ultimate_vc_responsive_media_css( $args );

			/* ---- Highlighted heading styles ---- */
			if ( '' != $ult_dual_color_settings['dual_color_high_font_family'] ) {
				$dchfont_family = get_ultimate_font_family( $ult_dual_color_settings['dual_color_high_font_family'] );
				if ( $dchfont_family ) {
					$main_dual_high_style_inline .= 'font-family:\'' . $dchfont_family . '\';';
				}
			}
			/* ----  Highlighted font style---- */
			$main_dual_high_style_inline .= get_ultimate_font_style( $ult_dual_color_settings['dual_color_high_font_style'] );
			// FIX: Highlighted font size before implementing responsive param.
			if ( is_numeric( $ult_dual_color_settings['dual_color_high_font_size'] ) ) {
				$ult_dual_color_settings['dual_color_high_font_size'] = 'desktop:' . $ult_dual_color_settings['dual_color_high_font_size'] . 'px;';       }
			if ( is_numeric( $ult_dual_color_settings['dual_color_high_line_height'] ) ) {
				$ult_dual_color_settings['dual_color_high_line_height'] = 'desktop:' . $ult_dual_color_settings['dual_color_high_line_height'] . 'px;';       }
			// Highlighted responsive {main} video styles.
			$args                      = array(
				'target'      => '.ult-dual-color.' . $uid . ' .ult-highlight-text',
				'media_sizes' => array(
					'font-size'   => $ult_dual_color_settings['dual_color_high_font_size'],
					'line-height' => $ult_dual_color_settings['dual_color_high_line_height'],
				),
			);
			$main_dual_high_responsive = get_ultimate_vc_responsive_media_css( $args );

			$output      = '<div id="' . esc_attr( $uid ) . '" class="ult-dual-color ' . esc_attr( $is_vc_49_plus ) . ' ult-dual-color-responsive-' . esc_attr( $ult_dual_color_settings['dual_color_stack'] ) . ' ' . esc_attr( $uid ) . ' ' . esc_attr( $ult_dual_color_settings['el_class'] ) . ' ' . esc_attr( $dual_design_style_css ) . '">';
				$output .= '<div class="ult-module-content ult-dual-color-heading" style="' . esc_attr( $dual_align ) . '">
						<' . $ult_dual_color_settings['heading_tag'] . '>';
			if ( '' !== $blink_prefix ) {
				$output .= $blink_prefix;
			}
						$output .= '<span class="ult-before-heading">
									<span class="ult-dual-heading-text ult-first-text ult-responsive" ' . $main_dual_color_responsive . ' style="' . esc_attr( $dual_ba_inline ) . ' ' . esc_attr( $dual_ba_back_inline ) . ' ' . esc_attr( $dual_ba_trans_inline ) . ' ' . esc_attr( $dual_ba_ls_inline ) . ' ' . esc_attr( $dual_ba_padding_inline ) . ' ' . esc_attr( $dual_ba_border_inline ) . ' ' . esc_attr( $main_dual_color_style_inline ) . '">' . esc_attr( $ult_dual_color_settings['dual_before_txt'] ) . '</span>
								</span>';
			if ( '' !== $blink_sufix ) {
				$output .= $blink_sufix;
			}
			if ( '' !== $hlink_prefix ) {
				$output .= $hlink_prefix;
			}
						$output .= '<span class="ult-adv-heading" style="' . esc_attr( $dual_inline_width ) . '">
									<span class="ult-dual-adv-heading-text ult-highlight-text ult-responsive" ' . $main_dual_high_responsive . ' style="' . esc_attr( $dual_high_inline ) . ' ' . esc_attr( $dual_high_back_inline ) . ' ' . esc_attr( $dual_high_trans_inline ) . ' ' . esc_attr( $dual_high_ls_inline ) . ' ' . esc_attr( $dual_high_padding_inline ) . ' ' . esc_attr( $dual_high_border_inline ) . ' ' . esc_attr( $main_dual_high_style_inline ) . '">' . esc_attr( $ult_dual_color_settings['dual_high_txt'] ) . '</span>
								</span>';
			if ( '' !== $hlink_sufix ) {
					$output .= $hlink_sufix;
			}
			if ( '' != $ult_dual_color_settings['dual_after_txt'] ) {
				if ( '' !== $alink_prefix ) {
					$output .= $alink_prefix;
				}
				$output .= '<span class="ult-after-heading">
									<span class="ult-dual-heading-text ult-third-text" ' . $main_dual_color_responsive . ' style="' . esc_attr( $dual_ba_inline ) . ' ' . esc_attr( $dual_ba_back_inline ) . ' ' . esc_attr( $dual_ba_trans_inline ) . ' ' . esc_attr( $dual_ba_ls_inline ) . ' ' . esc_attr( $dual_ba_padding_inline ) . ' ' . esc_attr( $dual_ba_border_inline ) . ' ' . esc_attr( $main_dual_color_style_inline ) . '">' . esc_attr( $ult_dual_color_settings['dual_after_txt'] ) . '</span>
								</span>';
				if ( '' !== $alink_sufix ) {
						$output .= $alink_sufix;
				}
			}
				$output .= '</' . $ult_dual_color_settings['heading_tag'] . '> </div>';
			$output     .= '</div>';
			return $output;
		}//end ultimate_dual_colors_module_shortcode()
	}//end class
	new Ultimate_Dual_Colors();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Dual_Color' ) ) {
		/**
		 * Class WPBakeryShortCode_Ultimate_Dual_Color
		 */
		class WPBakeryShortCode_Ultimate_Dual_Color extends WPBakeryShortCode {
		}
	}
}
