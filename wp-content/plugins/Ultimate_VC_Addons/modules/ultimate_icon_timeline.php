<?php
/**
 * Add-on Name: Ultimate Timeline
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package UAVC Ultimate Timeline
 */

if ( ! class_exists( 'Ultimate_Icon_Timeline' ) ) {
	/**
	 * Function that initializes Ultimate Timeline Module
	 *
	 * @class Ultimate_Icon_Timeline
	 */
	class Ultimate_Icon_Timeline {
		/**
		 * Constructor function that constructs default values for the Ultimate Timeline module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'add_icon_timeline' ) );
			}
			add_shortcode( 'icon_timeline', array( $this, 'icon_timeline' ) );
			add_shortcode( 'icon_timeline_item', array( $this, 'icon_timeline_item' ) );
			add_shortcode( 'icon_timeline_sep', array( $this, 'icon_timeline_sep' ) );
			add_shortcode( 'icon_timeline_feat', array( $this, 'icon_timeline_feat' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_timeline_assets' ), 1 );
		}
		/**
		 * Function to regester assets
		 *
		 * @since ----
		 * @access public
		 */
		public function register_timeline_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-timeline-style', 'timeline' );
		}
		/**
		 * For vc map check
		 *
		 * @since ----
		 * @access public
		 */
		public function add_icon_timeline() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'                    => __( 'Timeline', 'ultimate_vc' ),
						'base'                    => 'icon_timeline',
						'class'                   => 'vc_timeline',
						'icon'                    => 'vc_timeline_icon',
						'category'                => 'Ultimate VC Addons',
						'description'             => __( 'Timeline of old memories and events.', 'smile' ),
						'as_parent'               => array( 'only' => 'icon_timeline_item,icon_timeline_sep,icon_timeline_feat' ),
						'content_element'         => true,
						'show_settings_on_create' => true,
						'front_enqueue_css'       => preg_replace( '/\s/', '%20', UAVC_URL . 'assets/css/advacne_carosal_front.css' ),
						'params'                  => array(
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Height', 'ultimate_vc' ),
								'param_name'  => 'timeline_style',
								'value'       => array(
									__( 'Non-optimized (CSS)', 'ultimate_vc' ) => 'csstime',
									__( 'Optimized with JavaScript', 'ultimate_vc' ) => 'jstime',
								),
								'description' => __( 'How would you like the height of timeline.', 'smile' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Connecter Line Style', 'ultimate_vc' ),
								'param_name'  => 'timeline_line_style',
								'value'       => array(
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
								),
								'description' => __( 'Select the style of line that connects timeline items.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Color of Connecter Line:', 'ultimate_vc' ),
								'param_name'  => 'timeline_line_color',
								'value'       => '',
								'description' => __( 'Select the color for the line that connects timeline items.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background color for timeline item block / container:', 'ultimate_vc' ),
								'param_name'  => 'time_block_bg_color',
								'value'       => '',
								'description' => __( 'Give a background color to the timeline item block.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Select font color of items separator:', 'ultimate_vc' ),
								'param_name'  => 'time_sep_color',
								'value'       => '',
								'description' => __( 'Select font color of items separator.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Select background color for items separator:', 'ultimate_vc' ),
								'param_name'  => 'time_sep_bg_color',
								'value'       => '',
								'description' => __( 'Select the background color for item separator.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Timeline Layout:', 'ultimate_vc' ),
								'param_name'  => 'timeline_layout',
								'value'       => array(
									__( 'Full Width', 'ultimate_vc' ) => '',
									__( 'Custom Width', 'ultimate_vc' ) => 'timeline-custom-width',
								),
								'description' => __( 'Select Layout of Timeline.', 'smile' ),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Custom Width', 'ultimate_vc' ),
								'param_name'  => 'custom_width',
								'value'       => 200,
								'suffix'      => 'px',
								'description' => __( 'Provide custom width for each block', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'timeline_layout',
									'value'   => array( 'timeline-custom-width' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Hover animation:', 'ultimate_vc' ),
								'param_name'  => 'tl_animation',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Slide Out', 'ultimate_vc' ) => 'tl-animation-slide-out',
									__( 'Slide Up', 'ultimate_vc' ) => 'tl-animation-slide-up',
									__( 'Slide Down', 'ultimate_vc' ) => 'tl-animation-slide-down',
									__( 'Shadow', 'ultimate_vc' ) => 'tl-animation-shadow',
								),
								'description' => __( 'Hover animation can be given to the timeline item blocks.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the timeline, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => "<span style='display: block;'><a href='http://bsf.io/agey7' target='_blank' rel='noopener'>" . __( 'Watch Video Tutorial', 'ultimate_vc' ) . " &nbsp; <span class='dashicons dashicons-video-alt3' style='font-size:30px;vertical-align: middle;color: #e52d27;'></span></a></span>",
								'param_name'       => 'notification',
								'edit_field_class' => 'ult-param-important-wrapper ult-dashicon ult-align-right ult-bold-font ult-blue-font vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ultimate_spacing',
								'heading'     => ' Content Margin ',
								'param_name'  => 'timeline_margin',
								'mode'        => 'margin',                    // margin/padding.
								'unit'        => 'px',                        // [required] px,em,%,all     Default all
								'positions'   => array(                   // Also set 'defaults'.
									'Top'    => '',
									'Right'  => '',
									'Bottom' => '',
									'Left'   => '',
								),
								'group'       => __( 'Design ', 'ultimate_vc' ),
								'description' => __( 'Add spacing from outside to content.', 'ultimate_vc' ),
							),
						),
						'js_view'                 => 'VcColumnView',
					)
				);
				vc_map(
					array(
						'name'            => __( 'Items Separator', 'ultimate_vc' ),
						'base'            => 'icon_timeline_sep',
						'class'           => 'vc_timeline_sep',
						'icon'            => 'vc_timeline_sep_icon',
						'category'        => __( 'Timeline', 'ultimate_vc' ),
						'content_element' => true,
						'as_child'        => array( 'only' => 'icon_timeline' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'separator Text', 'ultimate_vc' ),
								'param_name'  => 'time_sep_title',
								'admin_label' => true,
								'value'       => '',
								'description' => __( 'Provide the text for this timeline Separator.', 'ultimate_vc' ),
							),
							array(
								'type'       => 'colorpicker',
								'param_name' => 'time_sep_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Border Style', 'ultimate_vc' ),
								'param_name' => 'line_style',
								'value'      => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Solid', 'ultimate_vc' ) => 'solid',
									__( 'Dashed', 'ultimate_vc' ) => 'dashed',
									__( 'Dotted', 'ultimate_vc' ) => 'dotted',
									__( 'Double', 'ultimate_vc' ) => 'double',
									__( 'Inset', 'ultimate_vc' ) => 'inset',
									__( 'Outset', 'ultimate_vc' ) => 'outset',
								),
							),
							array(
								'type'       => 'colorpicker',
								'param_name' => 'line_color',
								'heading'    => __( 'Border color', 'ultimate_vc' ),
								'dependency' => array(
									'element'   => 'line_style',
									'not_empty' => true,
								),
							),
							array(
								'type'       => 'number',
								'param_name' => 'line_width',
								'heading'    => __( 'Border width', 'ultimate_vc' ),
								'value'      => '1',
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'line_style',
									'not_empty' => true,
								),
							),
							array(
								'type'       => 'number',
								'param_name' => 'line_radius',
								'heading'    => __( 'Border radius', 'ultimate_vc' ),
								'value'      => '5',
								'suffix'     => 'px',
								'dependency' => array(
									'element'   => 'line_style',
									'not_empty' => true,
								),
							),
							array(
								'type'       => 'ultimate_google_fonts',
								'param_name' => 'seperator_title_font',
								'heading'    => __( 'Font Family', 'ultimate_vc' ),
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'seperator_title_font_style',
								'value'      => '',
								'group'      => 'Typography',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => '',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'font_size',
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
								'param_name' => 'seperator_line_ht',
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
							// Customize everything.
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the timeline, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
						),
					)
				);
				vc_map(
					array(
						'name'            => __( 'Timeline Item', 'ultimate_vc' ),
						'base'            => 'icon_timeline_item',
						'class'           => 'vc_timeline_item',
						'icon'            => 'vc_timeline_item_icon',
						'category'        => __( 'Timeline', 'ultimate_vc' ),
						'content_element' => true,
						'as_child'        => array( 'only' => 'icon_timeline' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'             => 'textfield',
								'class'            => '',
								'heading'          => __( 'Title', 'ultimate_vc' ),
								'param_name'       => 'time_title',
								'admin_label'      => true,
								'value'            => '',
								'description'      => __( 'Provide the title for this timeline item.', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-8',
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
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title_text_typography',
								'heading'          => __( 'Title settings', 'ultimate_vc' ),
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
								'param_name' => 'title_line_height',
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
								'type'       => 'colorpicker',
								'param_name' => 'title_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'textarea_html',
								'class'            => '',
								'heading'          => __( 'Description', 'ultimate_vc' ),
								'param_name'       => 'content',
								'admin_label'      => true,
								'value'            => '',
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
								'description'      => __( 'Provide some description.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'desc_text_typography',
								'heading'          => __( 'Description settings', 'ultimate_vc' ),
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
								'param_name' => 'desc_line_height',
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
								'type'       => 'colorpicker',
								'param_name' => 'desc_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'No Icon', 'ultimate_vc' ) => 'noicon',
									__( 'Font Icon Manager', 'ultimate_vc' ) => 'selector',
									__( 'Custom Image Icon', 'ultimate_vc' ) => 'custom',
								),
								'description' => __( 'Use an existing font icon</a> or upload a custom image.', 'ultimate_vc' ),
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
								'value'       => '#DE5034',
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
									__( 'Circle Background', 'ultimate_vc' ) => 'circle',
									__( 'Square Background', 'ultimate_vc' ) => 'square',
									__( 'Design your own', 'ultimate_vc' ) => 'advanced',
								),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '#fff',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
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
								'value'       => '#dbdbdb',
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
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
								),
								'description' => __( 'Like CSS3 Animations? We have several options for you!', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Apply link to:', 'ultimate_vc' ),
								'param_name'  => 'time_link_apply',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Complete box', 'ultimate_vc' ) => 'box',
									__( 'Box Title', 'ultimate_vc' ) => 'title',
									__( 'Display Read More', 'ultimate_vc' ) => 'more',
								),
								'description' => __( 'Select the element for link.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Add Link', 'ultimate_vc' ),
								'param_name'  => 'time_link',
								'value'       => '',
								'dependency'  => array(
									'element' => 'time_link_apply',
									'value'   => array( 'more', 'title', 'box' ),
								),
								'description' => __( 'Provide the link that will be applied to this timeline.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Read More Text', 'ultimate_vc' ),
								'param_name'  => 'time_read_text',
								'value'       => 'Read More',
								'description' => __( 'Customize the read more text.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'time_link_apply',
									'value'   => array( 'more' ),
								),
							),
							// Customize everything.
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the timeline, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
						),
					)
				);
				vc_map(
					array(
						'name'            => __( 'Timeline Featured Item', 'ultimate_vc' ),
						'base'            => 'icon_timeline_feat',
						'class'           => 'vc_timeline_feat',
						'icon'            => 'vc_timeline_feat_icon',
						'category'        => __( 'Timeline', 'ultimate_vc' ),
						'content_element' => true,
						'as_child'        => array( 'only' => 'icon_timeline' ),
						'is_container'    => false,
						'params'          => array(
							array(
								'type'             => 'textfield',
								'class'            => '',
								'heading'          => __( 'Title', 'ultimate_vc' ),
								'param_name'       => 'time_title',
								'admin_label'      => true,
								'value'            => '',
								'description'      => __( 'Provide the title for this timeline item.', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-8',
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
								'edit_field_class' => 'ult-param-padding-remove vc_col-sm-4',
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'title_text_typography',
								'heading'          => __( 'Title settings', 'ultimate_vc' ),
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
								'param_name' => 'title_line_ht',
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
								'type'       => 'colorpicker',
								'param_name' => 'title_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
							array(
								'type'             => 'textarea_html',
								'class'            => '',
								'heading'          => __( 'Description', 'ultimate_vc' ),
								'param_name'       => 'content',
								'admin_label'      => true,
								'value'            => '',
								'edit_field_class' => 'ult_hide_editor_fullscreen vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
								'description'      => __( 'Provide some description.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'param_name'       => 'desc_text_typography',
								'heading'          => __( 'Description settings', 'ultimate_vc' ),
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
								'param_name' => 'desc_line_ht',
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
								'type'       => 'colorpicker',
								'param_name' => 'desc_font_color',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'group'      => 'Typography',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Icon to display:', 'ultimate_vc' ),
								'param_name'  => 'icon_type',
								'value'       => array(
									__( 'No Icon', 'ultimate_vc' ) => 'noicon',
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
								'value'       => '#DE5034',
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
									__( 'Circle Background', 'ultimate_vc' ) => 'circle',
									__( 'Square Background', 'ultimate_vc' ) => 'square',
									__( 'Design your own', 'ultimate_vc' ) => 'advanced',
								),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
								),
								'description' => __( 'We have given three quick preset if you are in a hurry. Otherwise, create your own with various options.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'colorpicker',
								'class'       => '',
								'heading'     => __( 'Background Color', 'ultimate_vc' ),
								'param_name'  => 'icon_color_bg',
								'value'       => '#fff',
								'description' => __( 'Select background color for icon.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
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
								'value'       => '#dbdbdb',
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
								'dependency'  => array(
									'element' => 'icon_type',
									'value'   => array( 'selector', 'custom' ),
								),
								'description' => __( 'Like CSS3 Animations? We have several options for you!', 'ultimate_vc' ),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Apply link to:', 'ultimate_vc' ),
								'param_name'  => 'time_link_apply',
								'value'       => array(
									__( 'None', 'ultimate_vc' ) => '',
									__( 'Complete box', 'ultimate_vc' ) => 'box',
									__( 'Box Title', 'ultimate_vc' ) => 'title',
									__( 'Display Read More', 'ultimate_vc' ) => 'more',
								),
								'description' => __( 'Select the element for link.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'vc_link',
								'class'       => '',
								'heading'     => __( 'Add Link', 'ultimate_vc' ),
								'param_name'  => 'time_link',
								'value'       => '',
								'dependency'  => array(
									'element' => 'time_link_apply',
									'value'   => array( 'more', 'title', 'box' ),
								),
								'description' => __( 'Provide the link that will be applied to this timeline.', 'ultimate_vc' ),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Read More Text', 'ultimate_vc' ),
								'param_name'  => 'time_read_text',
								'value'       => 'Read More',
								'description' => __( 'Customize the read more text.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'time_link_apply',
									'value'   => array( 'more' ),
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => 'Arrow position',
								'param_name' => 'arrow_position',
								'value'      => array(
									__( 'Top', 'ultimate_vc' ) => 'top',
									__( 'Bottom', 'ultimate_vc' ) => 'bottom',
								),
							),
							// Customize everything.
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Extra Class', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'value'       => '',
								'description' => __( 'Add extra class name that will be applied to the timeline, and you can use this class for your customizations.', 'ultimate_vc' ),
							),
						),
					)
				);
			}
		}
		/**
		 * For the icon timeline in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function icon_timeline( $atts, $content = null ) {

			$ult_icon_timeline_settings               = shortcode_atts(
				array(
					'timeline_style'      => 'csstime',
					'timeline_line_color' => '',
					'timeline_line_style' => 'dotted',
					'time_sep_bg_color'   => '',
					'time_sep_color'      => '',
					'time_block_bg_color' => '',
					'timeline_layout'     => '',
					'tl_animation'        => '',
					'custom_width'        => '200',
					'el_class'            => '',
					'timeline_margin'     => '',
				),
				$atts
			);
			$data                                     = '';
			$ult_icon_timeline_settings['line_style'] = '';
			$cw                                       = '';
			$output                                   = '';
			$timeline_design_css                      = '';
			$timeline_design_css                      = $ult_icon_timeline_settings['timeline_margin'];
			if ( 'timeline-custom-width' == $ult_icon_timeline_settings['timeline_layout'] ) {
				$cw = 'data-timeline-cutom-width="' . esc_attr( $ult_icon_timeline_settings['custom_width'] ) . '"';
			}
			if ( '' != $ult_icon_timeline_settings['time_sep_color'] ) {
				$ult_icon_timeline_settings['time_sep_color'] = 'data-time_sep_color="' . esc_attr( $ult_icon_timeline_settings['time_sep_color'] ) . '"';
			}
			if ( '' != $ult_icon_timeline_settings['time_sep_bg_color'] ) {
				$ult_icon_timeline_settings['time_sep_bg_color'] = 'data-time_sep_bg_color="' . esc_attr( $ult_icon_timeline_settings['time_sep_bg_color'] ) . '"';
			}
			if ( '' != $ult_icon_timeline_settings['time_block_bg_color'] ) {
				$ult_icon_timeline_settings['time_block_bg_color'] = 'data-time_block_bg_color="' . esc_attr( $ult_icon_timeline_settings['time_block_bg_color'] ) . '"';
			}
			if ( '' != $ult_icon_timeline_settings['timeline_line_color'] ) {
				$ult_timeline_sep_settings['line_style'] = 'border-right-style:' . $ult_icon_timeline_settings['timeline_line_style'] . ';';
			}
			if ( '' != $ult_icon_timeline_settings['timeline_line_style'] ) {
				$ult_icon_timeline_settings['line_style'] .= 'border-right-color:' . $ult_icon_timeline_settings['timeline_line_color'] . ';';
			}
			if ( 'jstime' == $ult_icon_timeline_settings['timeline_style'] ) {
				$output .= '<div class="' . esc_attr( $ult_icon_timeline_settings['timeline_style'] ) . ' timeline_preloader" style="opacity:0;width:35px;margin:auto;margin-top:30px;"><img style="box-shadow:none;" alt="timeline_pre_loader" src="' . plugin_dir_url( __FILE__ ) . '../assets/img/timeline_pre-loader.gif" /></div>';
				$output .= '<div class="smile-icon-timeline-wrap ' . esc_attr( $ult_icon_timeline_settings['timeline_style'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['el_class'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['timeline_layout'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['tl_animation'] ) . '" ' . $cw . ' ' . $ult_icon_timeline_settings['time_sep_bg_color'] . ' ' . $ult_icon_timeline_settings['time_block_bg_color'] . ' ' . $ult_icon_timeline_settings['time_sep_color'] . ' style="opacity:0; ' . esc_attr( $timeline_design_css ) . '">';
			} else {
				$output .= '<div class="smile-icon-timeline-wrap ' . esc_attr( $ult_icon_timeline_settings['timeline_style'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['el_class'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['timeline_layout'] ) . ' ' . esc_attr( $ult_icon_timeline_settings['tl_animation'] ) . '" ' . $cw . ' ' . $ult_icon_timeline_settings['time_sep_bg_color'] . ' ' . $ult_icon_timeline_settings['time_block_bg_color'] . ' ' . $ult_icon_timeline_settings['time_sep_color'] . ' style="' . esc_attr( $timeline_design_css ) . '">';
			}
			$output .= '<div class="timeline-line " style="' . esc_attr( $ult_icon_timeline_settings['line_style'] ) . '"><span></span></div>';
			$output .= '<div class="timeline-wrapper">';
			$output .= do_shortcode( $content );
			$output .= '</div>';
			$output .= '</div>';
			return $output;
		}
		/**
		 * For the icon timeline in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function icon_timeline_sep( $atts, $content = null ) {
			$animation                 = '';
			$seperator_style           = '';
			$ult_timeline_sep_settings = shortcode_atts(
				array(
					'time_sep_title'             => '. . .',
					'time_sep_color'             => '',
					'time_sep_bg_color'          => '',
					'line_style'                 => '',
					'time_block_bg_color'        => '',
					'line_color'                 => '',
					'line_width'                 => '1',
					'line_radius'                => '5',
					'el_class'                   => '',
					'font_size'                  => '',
					'seperator_line_ht'          => '',
					'seperator_title_font'       => '',
					'seperator_title_font_style' => '',
				),
				$atts
			);

			if ( '' != $ult_timeline_sep_settings['time_sep_color'] ) {
				$seperator_style .= 'color:' . $ult_timeline_sep_settings['time_sep_color'] . ';';
			}
			if ( '' != $ult_timeline_sep_settings['line_style'] ) {
				$seperator_style .= 'border-style:' . $ult_timeline_sep_settings['line_style'] . ';';
			}
			if ( '' != $ult_timeline_sep_settings['line_color'] ) {
				$seperator_style .= 'border-color:' . $ult_timeline_sep_settings['line_color'] . ';';
			}
			if ( '' != $ult_timeline_sep_settings['line_width'] ) {
				$seperator_style .= 'border-width:' . $ult_timeline_sep_settings['line_width'] . 'px;';
			}
			if ( '' != $ult_timeline_sep_settings['line_radius'] ) {
				$seperator_style .= 'border-radius:' . $ult_timeline_sep_settings['line_radius'] . 'px;';
			}

			if ( is_numeric( $ult_timeline_sep_settings['font_size'] ) ) {
				$ult_timeline_sep_settings['font_size'] = 'desktop:' . $ult_timeline_sep_settings['font_size'] . 'px;';
			}
			if ( is_numeric( $ult_timeline_sep_settings['seperator_line_ht'] ) ) {
				$ult_timeline_sep_settings['seperator_line_ht'] = 'desktop:' . $ult_timeline_sep_settings['seperator_line_ht'] . 'px;';
			}
			$timeline_seperator_id   = 'timeline-seperator-' . wp_rand( 1000, 9999 );
			$timeline_seperator_args = array(
				'target'      => '#' . $timeline_seperator_id . ' .sep-text', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_timeline_sep_settings['font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_timeline_sep_settings['seperator_line_ht'],
				),
			);
			$seperator_data_list     = get_ultimate_vc_responsive_media_css( $timeline_seperator_args );
			if ( '' != $ult_timeline_sep_settings['seperator_title_font'] ) {
				$font_family      = get_ultimate_font_family( $ult_timeline_sep_settings['seperator_title_font'] );
				$seperator_style .= 'font-family:\'' . $font_family . '\';';

				if ( '' != $ult_timeline_sep_settings['seperator_title_font_style'] ) {
					$font_style       = get_ultimate_font_style( $ult_timeline_sep_settings['seperator_title_font_style'] );
					$seperator_style .= $font_style;
				}
			}
			$output  = '</div>';
			$output .= '
				<div id="' . esc_attr( $timeline_seperator_id ) . '" class="timeline-separator-text ' . esc_attr( $ult_timeline_sep_settings['el_class'] ) . '" data-sep-col="' . esc_attr( $ult_timeline_sep_settings['time_sep_color'] ) . '" data-sep-bg-col="' . esc_attr( $ult_timeline_sep_settings['time_sep_bg_color'] ) . '"><span class="sep-text ult-responsive" ' . $seperator_data_list . ' style="' . esc_attr( $seperator_style ) . '">' . $ult_timeline_sep_settings['time_sep_title'] . '</span></div><div class="timeline-wrapper ">';
			$style   = '';
			return $output;
		}
		/**
		 * For the icon timeline in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function icon_timeline_feat( $atts, $content = null ) {
			$time_icon          = '';
			$time_icon_color    = '';
			$time_icon_bg_color = '';
			$time_position      = '';
			$animation          = '';
			$border_color       = '';
			$title_style        = '';
			$desc_style         = '';
			$title_line_ht      = '';
			$target             = '';
			$link_title         = '';
			$rel                = '';

			$ult_timeline_feat_settings = shortcode_atts(
				array(
					'icon_type'           => 'noicon',
					'icon'                => '',
					'icon_img'            => '',
					'img_width'           => '',
					'icon_size'           => '',
					'icon_color'          => '',
					'icon_style'          => 'circle',
					'icon_color_bg'       => '',
					'icon_color_border'   => '',
					'icon_border_style'   => '',
					'icon_border_size'    => '',
					'icon_border_radius'  => '',
					'icon_border_spacing' => '',
					'icon_link'           => '',
					'icon_animation'      => '',
					'time_title'          => '',
					'heading_tag'         => 'h3',
					'title_font'          => '',
					'title_font_style'    => '',
					'title_font_size'     => '',
					'title_line_ht'       => '',
					'title_font_color'    => '',
					'desc_font'           => '',
					'desc_font_style'     => '',
					'desc_font_size'      => '',
					'desc_line_ht'        => '',
					'desc_font_color'     => '',
					'time_link'           => '',
					'time_link_apply'     => '',
					'time_read_text'      => 'Read More',
					'el_class'            => '',
					// parent atts.
					'font_size'           => '',
					'line_color'          => '',
					// SEp.
					'time_sep_color'      => '',
					'time_sep_bg_color'   => '',
					'line_style'          => '',
					'time_block_bg_color' => '',
					'line_color'          => '',
					'arrow_position'      => 'top',
				),
				$atts
			);
			$html                       = '';
			$custom_style               = '';
			$bg_cls                     = '';
			$box_icon                   = do_shortcode( '[just_icon icon_type="' . esc_attr( $ult_timeline_feat_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_timeline_feat_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_timeline_feat_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_timeline_feat_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_timeline_feat_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_timeline_feat_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_timeline_feat_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_timeline_feat_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_timeline_feat_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_timeline_feat_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_timeline_feat_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_timeline_feat_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_timeline_feat_settings['icon_border_spacing'] ) . '" icon_link="' . esc_attr( $ult_timeline_feat_settings['icon_link'] ) . '" icon_animation="' . esc_attr( $ult_timeline_feat_settings['icon_animation'] ) . '"]' );
			if ( '' == $ult_timeline_feat_settings['icon_color_bg'] ) {
				$bg_cls .= 'tl-icon-no-bg';
			}
			if ( '' != $ult_timeline_feat_settings['line_color'] ) {
				$ult_timeline_feat_settings['line_style'] = 'border-right-color:' . $ult_timeline_feat_settings['line_color'] . ';';
			}
			if ( '' != $ult_timeline_feat_settings['font_size'] ) {
				$ult_timeline_feat_settings['line_style'] .= 'top:' . ( $ult_timeline_feat_settings['font_size'] * 2 ) . 'px;';
			}
			/* title */
			if ( '' != $ult_timeline_feat_settings['title_font'] ) {
				$font_family  = get_ultimate_font_family( $ult_timeline_feat_settings['title_font'] );
				$title_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_timeline_feat_settings['title_font_style'] ) {
				$title_style .= get_ultimate_font_style( $ult_timeline_feat_settings['title_font_style'] );
			}

			if ( is_numeric( $ult_timeline_feat_settings['title_font_size'] ) ) {
				$ult_timeline_feat_settings['title_font_size'] = 'desktop:' . $ult_timeline_feat_settings['title_font_size'] . 'px;';
			}
			if ( is_numeric( $title_line_ht ) ) {
				$title_line_ht = 'desktop:' . $title_line_ht . 'px;';
			}
			$timeline_featured_id         = 'timeline-featured-' . wp_rand( 1000, 9999 );
			$timeline_featured_title_args = array(
				'target'      => '#' . $timeline_featured_id . ' .ult-timeline-title', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_timeline_feat_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $title_line_ht,
				),
			);
			$data_list                    = get_ultimate_vc_responsive_media_css( $timeline_featured_title_args );

			if ( '' != $ult_timeline_feat_settings['title_font_color'] ) {
				$title_style .= 'color:' . $ult_timeline_feat_settings['title_font_color'] . ';';
			}
			/* description */
			if ( '' != $ult_timeline_feat_settings['desc_font'] ) {
				$font_family = get_ultimate_font_family( $ult_timeline_item_settings['desc_font'] );
				$desc_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_timeline_feat_settings['desc_font_style'] ) {
				$desc_style .= get_ultimate_font_style( $ult_timeline_item_settings['desc_font_style'] );
			}
			if ( is_numeric( $ult_timeline_feat_settings['desc_font_size'] ) ) {
				$ult_timeline_feat_settings['desc_font_size'] = 'desktop:' . $ult_timeline_feat_settings['desc_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_timeline_feat_settings['desc_line_ht'] ) ) {
				$ult_timeline_feat_settings['desc_line_ht'] = 'desktop:' . $ult_timeline_feat_settings['desc_line_ht'] . 'px;';
			}
			$timeline_featured_title_args = array(
				'target'      => '#' . $timeline_featured_id . ' .custom-lht', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_timeline_feat_settings['desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_timeline_feat_settings['desc_line_ht'],
				),
			);
			$data_list_desc               = get_ultimate_vc_responsive_media_css( $timeline_featured_title_args );
			if ( '' != $ult_timeline_feat_settings['desc_font_color'] ) {
				$desc_style .= 'color:' . $ult_timeline_feat_settings['desc_font_color'] . ';';
			}
			$li_prefix                          = '<div class="timeline-block ' . esc_attr( $ult_timeline_feat_settings['el_class'] ) . '"><div class="timeline-dot"></div><div class="ult-timeline-arrow"><s></s><l></l></div>';
			$li_suffix                          = '</div>';
			$style                              = ( '' !== $time_icon_color ) ? ' color:' . $time_icon_color . ';' : ' ';
			$style                             .= ( '' !== $time_icon_bg_color ) ? ' background:' . $time_icon_bg_color . ';' : ' ';
			$style                             .= ( '' !== $ult_timeline_feat_settings['font_size'] ) ? ' font-size:' . $ult_timeline_feat_settings['font_size'] . 'px;' : ' ';
			$icon_pad                           = '';
			$header_block_style                 = '';
			$ult_timeline_feat_settings['icon'] = '<div class="timeline-icon-block"' . $icon_pad . '><div class="ult-timeline-icon ' . esc_attr( $bg_cls ) . '" style="' . esc_attr( $style ) . '">';
			if ( 'noicon' != $ult_timeline_feat_settings['icon_type'] ) {
				$ult_timeline_feat_settings['icon'] .= $box_icon;
			}
			$ult_timeline_feat_settings['icon'] .= '</div> <!-- icon --></div>';
			$link_sufix                          = '';
			$link_prefix                         = '';

			$vv_link = '';
			if ( '' != $ult_timeline_feat_settings['time_link'] ) {
				$href = vc_build_link( $ult_timeline_feat_settings['time_link'] );

				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

				$link_prefix = '<a class="tl-desc-a" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '>';
				$link_sufix  = '</a>';
			}
			$header  = '';
			$header .= '<div class="timeline-header-block" ' . $header_block_style . '>
							<div class="timeline-header" id="' . esc_attr( $timeline_featured_id ) . '"  style="">';
			$header .= '<' . $ult_timeline_feat_settings['heading_tag'] . '
			 class="ult-timeline-title ult-responsive"  ' . $data_list . ' style="' . esc_attr( $title_style ) . '">' . $ult_timeline_feat_settings['time_title'] . '</' . $ult_timeline_feat_settings['heading_tag'] . '>';
			if ( '' != $ult_timeline_feat_settings['time_link_apply'] && 'title' == $ult_timeline_feat_settings['time_link_apply'] ) {
				$header = $link_prefix . $header . $link_sufix;
			}
			$header .= '<div class="ult-responsive custom-lht" ' . $data_list_desc . ' style="' . esc_attr( $desc_style ) . '">' . do_shortcode( $content ) . '</div>';
			if ( '' != $ult_timeline_feat_settings['time_link_apply'] && 'more' == $ult_timeline_feat_settings['time_link_apply'] ) {
				$header = $header . '<p>' . $link_prefix . $ult_timeline_feat_settings['time_read_text'] . $link_sufix . '</p>';
			}
			$header .= '</div> <!-- header --></div>';
			$contt   = '';
			if ( '' != $ult_timeline_feat_settings['time_link_apply'] && 'box' == $ult_timeline_feat_settings['time_link_apply'] ) {
				$contt .= '<a class="link-box ult-link-box" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '></a>';
			}
			$icon_wrap_preffix = '<div class="timeline-icon-block">';
			$icon_wrap_suffix  = '</div>';
			$heading_preffix   = '<div class="timeline-header-block">';
			$heading_suffix    = '</div>';
			$html              = $ult_timeline_feat_settings['icon'] . $header;
			$feat_spl          = '</div>';
			if ( 'bottom' == $ult_timeline_feat_settings['arrow_position'] ) { // featured item at top.
				$ext_class = 'feat-top';
			} else {
				$ext_class = '';
			}
			$feat_spl .= '<div class="timeline-feature-item feat-item ' . esc_attr( $ext_class ) . ' ' . esc_attr( $ult_timeline_feat_settings['el_class'] ) . '">';
			$contt    .= '<div class="feat-dot ' . esc_attr( $ext_class ) . '"><div class="timeline-dot"></div></div><div class="ult-timeline-arrow ' . esc_attr( $ext_class ) . '"><s></s><l></l></div>' . $html;
			$contt    .= '</div><div class="timeline-wrapper ">';
			$feat_spl .= $contt;
			return $feat_spl;
		}
		/**
		 * For the icon timeline in the module
		 *
		 * @since ----
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function icon_timeline_item( $atts, $content = null ) {
			$time_icon_color    = '';
			$time_icon_bg_color = '';
			$time_position      = '';
			$animation          = '';
			$border_color       = '';
			$title_style        = '';
			$desc_style         = '';
			$target             = '';
			$link_title         = '';
			$rel                = '';

			$ult_timeline_item_settings = shortcode_atts(
				array(
					'icon_type'           => 'noicon',
					'icon'                => '',
					'icon_img'            => '',
					'img_width'           => '',
					'icon_size'           => '',
					'icon_color'          => '',
					'icon_style'          => 'circle',
					'icon_color_bg'       => '',
					'icon_color_border'   => '',
					'icon_border_style'   => '',
					'icon_border_size'    => '',
					'icon_border_radius'  => '',
					'icon_border_spacing' => '',
					'icon_link'           => '',
					'icon_animation'      => '',
					'time_title'          => '',
					'heading_tag'         => 'h3',
					'title_font'          => '',
					'title_font_style'    => '',
					'title_font_size'     => '',
					'title_line_height'   => '',
					'title_font_color'    => '',
					'desc_font'           => '',
					'desc_font_style'     => '',
					'desc_font_size'      => '',
					'desc_line_height'    => '',
					'desc_font_color'     => '',
					'time_link'           => '',
					'time_link_apply'     => '',
					'time_read_text'      => 'Read More',
					'el_class'            => '',
					'font_size'           => '',
					'line_color'          => '',
				),
				$atts
			);
			$html                       = '';
			$custom_style               = '';
			$bg_cls                     = '';
			$box_icon                   = do_shortcode( '[just_icon icon_type="' . esc_attr( $ult_timeline_item_settings['icon_type'] ) . '" icon="' . esc_attr( $ult_timeline_item_settings['icon'] ) . '" icon_img="' . esc_attr( $ult_timeline_item_settings['icon_img'] ) . '" img_width="' . esc_attr( $ult_timeline_item_settings['img_width'] ) . '" icon_size="' . esc_attr( $ult_timeline_item_settings['icon_size'] ) . '" icon_color="' . esc_attr( $ult_timeline_item_settings['icon_color'] ) . '" icon_style="' . esc_attr( $ult_timeline_item_settings['icon_style'] ) . '" icon_color_bg="' . esc_attr( $ult_timeline_item_settings['icon_color_bg'] ) . '" icon_color_border="' . esc_attr( $ult_timeline_item_settings['icon_color_border'] ) . '"  icon_border_style="' . esc_attr( $ult_timeline_item_settings['icon_border_style'] ) . '" icon_border_size="' . esc_attr( $ult_timeline_item_settings['icon_border_size'] ) . '" icon_border_radius="' . esc_attr( $ult_timeline_item_settings['icon_border_radius'] ) . '" icon_border_spacing="' . esc_attr( $ult_timeline_item_settings['icon_border_spacing'] ) . '" icon_link="' . esc_attr( $ult_timeline_item_settings['icon_link'] ) . '" icon_animation="' . esc_attr( $ult_timeline_item_settings['icon_animation'] ) . '"]' );
			if ( '' == $ult_timeline_item_settings['icon_color_bg'] ) {
				$bg_cls .= 'tl-icon-no-bg';
			}
			if ( '' != $ult_timeline_item_settings['line_color'] ) {
				$ult_timeline_feat_settings['line_style'] = 'border-right-color:' . $ult_timeline_item_settings['line_color'] . ';';
			}
			if ( '' != $ult_timeline_item_settings['font_size'] ) {
				$ult_timeline_feat_settings['line_style'] .= 'top:' . ( $ult_timeline_item_settings['font_size'] * 2 ) . 'px;';
			}
			/* title */
			if ( '' != $ult_timeline_item_settings['title_font'] ) {
				$font_family  = get_ultimate_font_family( $ult_timeline_item_settings['title_font'] );
				$title_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_timeline_item_settings['title_font_style'] ) {
				$title_style .= get_ultimate_font_style( $ult_timeline_item_settings['title_font_style'] );
			}

			if ( is_numeric( $ult_timeline_item_settings['title_font_size'] ) ) {
				$ult_timeline_item_settings['title_font_size'] = 'desktop:' . $ult_timeline_item_settings['title_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_timeline_item_settings['title_line_height'] ) ) {
				$ult_timeline_item_settings['title_line_height'] = 'desktop:' . $ult_timeline_item_settings['title_line_height'] . 'px;';
			}
			$timeline_item_id   = 'timeline-item-' . wp_rand( 1000, 9999 );
			$timeline_item_args = array(
				'target'      => '#' . $timeline_item_id . ' .ult-timeline-title', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_timeline_item_settings['title_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_timeline_item_settings['title_line_height'],
				),
			);
			$Item_data_list     = get_ultimate_vc_responsive_media_css( $timeline_item_args );// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( '' != $ult_timeline_item_settings['title_font_color'] ) {
				$title_style .= 'color:' . $ult_timeline_item_settings['title_font_color'] . ';';
			}
			/* description */
			if ( '' != $ult_timeline_item_settings['desc_font'] ) {
				$font_family = get_ultimate_font_family( $ult_timeline_item_settings['desc_font'] );
				$desc_style .= 'font-family:\'' . $font_family . '\';';
			}
			if ( '' != $ult_timeline_item_settings['desc_font_style'] ) {
				$desc_style .= get_ultimate_font_style( $ult_timeline_item_settings['desc_font_style'] );
			}

			if ( is_numeric( $ult_timeline_item_settings['desc_font_size'] ) ) {
				$ult_timeline_item_settings['desc_font_size'] = 'desktop:' . $ult_timeline_item_settings['desc_font_size'] . 'px;';
			}
			if ( is_numeric( $ult_timeline_item_settings['desc_line_height'] ) ) {
				$ult_timeline_item_settings['desc_line_height'] = 'desktop:' . $ult_timeline_item_settings['desc_line_height'] . 'px;';
			}
			$timeline_item_args_desc = array(
				'target'      => '#' . $timeline_item_id . ' .timeline-item-spt', // set targeted element e.g. unique class/id etc.
				'media_sizes' => array(
					'font-size'   => $ult_timeline_item_settings['desc_font_size'], // set 'css property' & 'ultimate_responsive' sizes. Here $title_responsive_font_size holds responsive font sizes from user input.
					'line-height' => $ult_timeline_item_settings['desc_line_height'],
				),
			);
			$Item_desc_data_list     = get_ultimate_vc_responsive_media_css( $timeline_item_args_desc );// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( '' != $ult_timeline_item_settings['desc_font_color'] ) {
				$desc_style .= 'color:' . $ult_timeline_item_settings['desc_font_color'] . ';';
			}
			$li_prefix                          = '<div class="timeline-block ' . esc_attr( $ult_timeline_item_settings['el_class'] ) . '"><div class="timeline-dot"></div><div class="ult-timeline-arrow"><s></s><l></l></div>';
			$li_suffix                          = '</div>';
			$style                              = ( '' !== $time_icon_color ) ? ' color:' . $time_icon_color . ';' : ' ';
			$style                             .= ( '' !== $time_icon_bg_color ) ? ' background:' . $time_icon_bg_color . ';' : ' ';
			$style                             .= ( '' !== $ult_timeline_item_settings['font_size'] ) ? ' font-size:' . $ult_timeline_item_settings['font_size'] . 'px;' : ' ';
			$icon_pad                           = '';
			$header_block_style                 = '';
			$ult_timeline_item_settings['icon'] = '<div class="timeline-icon-block"><div class="ult-timeline-icon ' . esc_attr( $bg_cls ) . '" style="' . esc_attr( $style ) . '">';
			if ( 'noicon' != $ult_timeline_item_settings['icon_type'] ) {
				$ult_timeline_item_settings['icon'] .= $box_icon;
			}
			$ult_timeline_item_settings['icon'] .= '</div> <!-- icon --></div>';
			$link_sufix                          = '';
			$link_prefix                         = '';
			$vv_link                             = '';
			if ( '' != $ult_timeline_item_settings['time_link'] ) {
				$href       = vc_build_link( $ult_timeline_item_settings['time_link'] );
				$url        = ( isset( $href['url'] ) && '' !== $href['url'] ) ? $href['url'] : '';
				$target     = ( isset( $href['target'] ) && '' !== $href['target'] ) ? esc_attr( trim( $href['target'] ) ) : '';
				$link_title = ( isset( $href['title'] ) && '' !== $href['title'] ) ? esc_attr( $href['title'] ) : '';
				$rel        = ( isset( $href['rel'] ) && '' !== $href['rel'] ) ? esc_attr( $href['rel'] ) : '';

				$link_prefix = '<a class="tl-desc-a" ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . '>';
				$link_sufix  = '</a>';
			}
			$header             = '';
			$header_link_prefix = '';
			$header_link_suffix = '';
			$header            .= '<div class="timeline-header-block" ' . $header_block_style . '>
							<div id="' . esc_attr( $timeline_item_id ) . '" class="timeline-header" style="">';
			if ( '' != $ult_timeline_item_settings['time_link_apply'] && 'title' == $ult_timeline_item_settings['time_link_apply'] ) {
				$header_link_prefix = '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' class="link-title">';
				$header_link_suffix = '</a>';
			}
			$header .= '<' . $ult_timeline_item_settings['heading_tag'] . ' class="ult-timeline-title ult-responsive" ' . $Item_data_list . ' style="' . esc_attr( $title_style ) . '">' . $header_link_prefix . $ult_timeline_item_settings['time_title'] . $header_link_suffix . '</' . $ult_timeline_item_settings['heading_tag'] . '>';// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$header .= '<div class="ult-responsive timeline-item-spt" ' . $Item_desc_data_list . ' style="' . esc_attr( $desc_style ) . '">' . do_shortcode( $content ) . '</div>';// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( '' != $ult_timeline_item_settings['time_link_apply'] && 'more' == $ult_timeline_item_settings['time_link_apply'] ) {
				$header = $header . '<p>' . $link_prefix . $ult_timeline_item_settings['time_read_text'] . $link_sufix . '</p>';
			}
			$header .= '</div> <!-- header --></div>';
			if ( '' != $ult_timeline_item_settings['time_link_apply'] && 'box' == $ult_timeline_item_settings['time_link_apply'] ) {
				$header .= '<a ' . Ultimate_VC_Addons::uavc_link_init( $url, $target, $link_title, $rel ) . ' class="link-box ult-link-box"></a>';
			}
			$icon_wrap_preffix = '<div class="timeline-icon-block">';
			$icon_wrap_suffix  = '</div>';
			$heading_preffix   = '<div class="timeline-header-block">';
			$heading_suffix    = '</div>';
			$html              = $li_prefix . $ult_timeline_item_settings['icon'] . $header . $li_suffix;
			return $html;
		}
	}
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Icon_Timeline' ) ) {
	/**
	 * Function that initializes Ultimate Timeline Module
	 *
	 * @class WPBakeryShortCode_Icon_Timeline
	 */
	class WPBakeryShortCode_Icon_Timeline extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Icon_Timeline_Item' ) ) {
	/**
	 * Function that initializes Ultimate Timeline Module
	 *
	 * @class WPBakeryShortCode_Icon_Timeline_Item
	 */
	class WPBakeryShortCode_Icon_Timeline_Item extends WPBakeryShortCode {
	}
}
if ( class_exists( 'Ultimate_Icon_Timeline' ) ) {
	$Ultimate_Icon_Timeline = new Ultimate_Icon_Timeline();// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
}
