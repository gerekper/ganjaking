<?php
/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Icon_Column' ) ) {
	class MPC_Icon_Column {
		public $shortcode = 'mpc_icon_column';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'mpc_icon_column', array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$this->parts = array(
				'section_begin' => '',
				'section_end'   => '',
				'icon'          => '',
				'divider'       => '',
				'title'         => '',
				'description'   => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_icon_column-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_icon_column/css/mpc_icon_column.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_icon_column-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/mpc_icon_column/js/mpc_icon_column' . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build shortcode layout */
		function shortcode_layout( $style, $parts ) {
			$content = '';

			$layouts = array(
				'style_1' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_2' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_3' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_4' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_5' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_6' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_7' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
				'style_8' => array( 'icon', 'section_begin', 'title', 'divider', 'description', 'section_end' ),
			);

			if( ! isset( $layouts[ $style ] ) )
				return;

			foreach( $layouts[ $style ] as $part ) {
				$content .= $parts[ $part ];
			}

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $MPC_Icon, $MPC_Divider, $mpc_circle_icons_wrap, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'                       => '',
				'preset'                      => '',
				'layout' => 'style_1',

				'alignment' => 'center',
				'url'       => '',

				'title_font_preset'      => '',
				'title_font_color'       => '',
				'title_font_size'        => '',
				'title_font_line_height' => '',
				'title_font_align'       => '',
				'title_font_transform'   => '',
				'title'                  => '',
				'title_margin_css'       => '',

				'content_font_preset'      => '',
				'content_font_color'       => '',
				'content_font_size'        => '',
				'content_font_line_height' => '',
				'content_font_align'       => '',
				'content_font_transform'   => '',
				'content_margin_css'       => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_border_css' => '',

				'hover_title_color'    => '',
				'hover_content_color'  => '',

				/* Icon */
				'mpc_icon__class'      => '',
				'mpc_icon__disable'    => '',
				'mpc_icon__preset'     => 'default',
				'mpc_icon__url'        => '',
				'mpc_icon__transition' => 'none',

				'mpc_icon__padding_css' => '',
				'mpc_icon__margin_css'  => '',

				'mpc_icon__border_css' => '',

				'mpc_icon__icon_type'       => 'icon',
				'mpc_icon__icon'            => '',
				'mpc_icon__icon_character'  => '',
				'mpc_icon__icon_image'      => '',
				'mpc_icon__icon_image_size' => 'thumbnail',
				'mpc_icon__icon_preset'     => '',
				'mpc_icon__icon_size'       => '',
				'mpc_icon__icon_color'      => '',

				'mpc_icon__background_type'       => 'color',
				'mpc_icon__background_color'      => '',
				'mpc_icon__background_image'      => '',
				'mpc_icon__background_image_size' => 'large',
				'mpc_icon__background_repeat'     => 'no-repeat',
				'mpc_icon__background_size'       => 'initial',
				'mpc_icon__background_position'   => 'middle-center',
				'mpc_icon__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__hover_border_css' => '',

				'mpc_icon__hover_icon_type'       => 'icon',
				'mpc_icon__hover_icon'            => '',
				'mpc_icon__hover_icon_character'  => '',
				'mpc_icon__hover_icon_image'      => '',
				'mpc_icon__hover_icon_image_size' => 'thumbnail',
				'mpc_icon__hover_icon_preset'     => '',
				'mpc_icon__hover_icon_color'      => '',

				'mpc_icon__hover_background_type'       => 'color',
				'mpc_icon__hover_background_color'      => '',
				'mpc_icon__hover_background_image'      => '',
				'mpc_icon__hover_background_image_size' => 'large',
				'mpc_icon__hover_background_repeat'     => 'no-repeat',
				'mpc_icon__hover_background_size'       => 'initial',
				'mpc_icon__hover_background_position'   => 'middle-center',
				'mpc_icon__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_icon__animation_loop_type'     => 'none',
				'mpc_icon__animation_loop_duration' => '1000',
				'mpc_icon__animation_loop_delay'    => '1000',
				'mpc_icon__animation_loop_hover'    => '',

				/* Divider */
				'mpc_divider__disable'              => '',
				'mpc_divider__align'                => 'center',
				'mpc_divider__width'                => '100',

				'mpc_divider__content_type'     => 'none',
				'mpc_divider__content_position' => '50',

				'mpc_divider__lines_number' => '1',
				'mpc_divider__lines_style'  => 'solid',
				'mpc_divider__lines_color'  => '',
				'mpc_divider__lines_gap'    => '1',
				'mpc_divider__lines_weight' => '1',

				'mpc_divider__title'            => '',
				'mpc_divider__font_preset'      => '',
				'mpc_divider__font_color'       => '#333333',
				'mpc_divider__font_size'        => '18',
				'mpc_divider__font_line_height' => '',
				'mpc_divider__font_align'       => '',
				'mpc_divider__font_transform'   => '',

				'mpc_divider__icon_type'       => 'icon',
				'mpc_divider__icon'            => '',
				'mpc_divider__icon_character'  => '',
				'mpc_divider__icon_image'      => '',
				'mpc_divider__icon_image_size' => 'thumbnail',
				'mpc_divider__icon_preset'     => '',
				'mpc_divider__icon_size'       => '',
				'mpc_divider__icon_color'      => '#333333',

				'mpc_divider__padding_css' => '',
				'mpc_divider__margin_css'  => '',
			), $atts );

			if ( $mpc_circle_icons_wrap ) {
				$atts[ 'layout' ] = 'style_1';
			}

			/* Prepare */
			$url_settings = MPC_Parser::url( $atts[ 'url' ] );
			$wrapper      = $url_settings != '' ? 'a' : 'div';

			$animation    = MPC_Parser::animation( $atts );
			$atts_icon    = MPC_Parser::shortcode( $atts, 'mpc_icon_' );
			$atts_divider = MPC_Parser::shortcode( $atts, 'mpc_divider_' );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-init mpc-parent-hover mpc-transition';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'layout' ] != '' ? ' mpc-icon-column--' . esc_attr( $atts[ 'layout' ] ) : '';
			$classes .= $atts[ 'alignment' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'alignment' ] ) : ' mpc-align--center';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_title   = $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'title_font_preset' ] ) : '';
			$classes_title   .= ' mpc-transition';
			$classes_content = $atts[ 'content_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'content_font_preset' ] ) : '';
			$classes_content .= ' mpc-transition';

			/* Layout parts */
			$this->parts[ 'section_begin' ] = '<div class="mpc-icon-column__content-wrap"><div class="mpc-icon-column__content">';
			$this->parts[ 'section_end' ]   = '</div></div>';
			$this->parts[ 'icon' ]          = $atts[ 'mpc_icon__disable' ] == '' ? $MPC_Icon->shortcode_template( $atts_icon ) : '';
			$this->parts[ 'title' ]         = $atts[ 'title' ] != '' ? '<h3 class="mpc-icon-column__heading' . $classes_title . '">' . $atts[ 'title' ] . '</h3>' : '';
			$this->parts[ 'divider' ]       = $atts[ 'mpc_divider__disable' ] == '' ? $MPC_Divider->shortcode_template( $atts_divider ) : '';
			$this->parts[ 'description' ]   = $description != '' ? '<div class="mpc-icon-column__description' . $classes_content . '">' . wpb_js_remove_wpautop( $description, true ) . '</div>' : '';

			/* Shortcode Output */
			$return = '<' . $wrapper . $url_settings . ' data-id="' . $css_id . '" class="mpc-icon-column' . $classes . '" ' . $animation . '>';
				$return .= $this->shortcode_layout( $atts[ 'layout' ], $this->parts );
			$return .= '</' . $wrapper . '>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_icon_column-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ]  = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'content_font_size' ]  = $styles[ 'content_font_size' ] != '' ? $styles[ 'content_font_size' ] . ( is_numeric( $styles[ 'content_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-circle-icons .mpc-icon-column[data-id="' . $css_id . '"] .mpc-icon-column__content-wrap,';
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_border_css' ] ) { $inner_styles[] = $styles[ 'hover_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-circle-icons .mpc-icon-column[data-id="' . $css_id . '"]:hover .mpc-icon-column__content-wrap,';
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"]:hover {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'hover_title_color' ] ) {
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"]:hover .mpc-icon-column__heading {';
					$style .= 'color: ' . $styles[ 'hover_title_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_content_color' ] ) {
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"]:hover .mpc-icon-column__description {';
					$style .= 'color: ' . $styles[ 'hover_content_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0  ) {
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"] .mpc-icon-column__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'content_margin_css' ] ) { $inner_styles[] = $styles[ 'content_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0  ) {
				$style .= '.mpc-icon-column[data-id="' . $css_id . '"] .mpc-icon-column__description {';
					$style .= join( '', $inner_styles );
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

			$base = array(
				array(
					'type'        => 'mpc_preset',
					'heading'     => __( 'Main Preset', 'mpc' ),
					'param_name'  => 'preset',
					'tooltip'     => MPC_Helper::style_presets_desc(),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'             => 'mpc_layout_select',
					'heading'          => __( 'Layout Select', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => 'style_1',
					'columns'          => '4',
					'layouts'          => array(
						'style_1' => '4',
						'style_2' => '3',
						'style_3' => '2',
						'style_4' => '2',
						'style_5' => '2',
						'style_6' => '2',
					),
					'std'              => 'style_1',
					'shortcode'        => $this->shortcode,
					'description'      => __( 'Choose layout style.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'mpc_align',
					'heading'          => __( 'Content Alignment', 'mpc' ),
					'param_name'       => 'alignment',
					'tooltip'          => __( 'Choose content alignment.', 'mpc' ),
					'value'            => 'center',
					'grid_size'        => 'small',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => __( 'URL', 'mpc' ),
					'param_name'       => 'url',
					'admin_label'      => true,
					'tooltip'          => __( 'Choose target link for the whole icon column.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
				),
			);

			/* SECTION TITLE */
			$title = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'title',
					'admin_label'      => true,
					'value'            => '',
					'group'            => __( 'Title', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			);

			/* SECTION DESCRIPTION */
			$description = array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Description', 'mpc' ),
					'param_name'  => 'content',
					'holder'      => 'div',
					'tooltip'     => __( 'Define content. Thanks to default WordPress WYSIWYG editor you can easily format the content.', 'mpc' ),
					'value'       => '',
					'group'       => __( 'Content', 'mpc' ),
				),
			);

			/* HOVER TITLE */
			$hover_title = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover - Title', 'mpc' ),
					'param_name' => 'hover_title_divider',
					'group'      => __( 'Title', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'hover_title_color',
					'tooltip'          => __( 'If you want to change the title color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Title', 'mpc' ),
				),
			);

			/* HOVER DESCRIPTION */
			$hover_description = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover - Description', 'mpc' ),
					'param_name' => 'hover_title_divider',
					'group'      => __( 'Content', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Color', 'mpc' ),
					'param_name'       => 'hover_content_color',
					'tooltip'          => __( 'If you want to change the content color after hover choose a different one from the color picker below.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Content', 'mpc' ),
				),
			);


			/* Integrate Icon */
			$icon_exclude = array( 'exclude_regex' => '/animation_in(.*)|url|tooltip_(.*)/', );
			$integrate_icon = vc_map_integrate_shortcode( 'mpc_icon', 'mpc_icon__', __( 'Icon', 'mpc' ), $icon_exclude );
			$disable_icon   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Icon', 'mpc' ),
					'param_name'       => 'mpc_icon__disable',
					'tooltip'          => __( 'Check to disable icon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_icon = array_merge( $disable_icon, $integrate_icon );

			/* Integrate Divider */
			$divider_exclude = array( 'exclude_regex' => '/animation_(.*)/', );
			$integrate_divider = vc_map_integrate_shortcode( 'mpc_divider', 'mpc_divider__', __( 'Divider', 'mpc' ), $divider_exclude );
			$disable_divider   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Divider', 'mpc' ),
					'param_name'       => 'mpc_divider__disable',
					'tooltip'          => __( 'Check to disable divider.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Divider', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_divider = array_merge( $disable_divider, $integrate_divider );

			$title_font       = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Title', 'mpc' ), ) );
			$description_font = MPC_Snippets::vc_font( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ), ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$title_margin   = MPC_Snippets::vc_margin( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Title', 'mpc' ), ) );
			$content_margin = MPC_Snippets::vc_margin( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ), ) );

			$hover_atts = array( 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ) );

			$hover_background = MPC_Snippets::vc_background( $hover_atts );
			$hover_border     = MPC_Snippets::vc_border( $hover_atts );

			$animation = MPC_Snippets::vc_animation();
			$class     = MPC_Snippets::vc_class();

			$params = array_merge(
				$base,
				$background,
				$border,
				$padding,
				$margin,
				$title_font,
				$title,
				$title_margin,
				$description_font,
				$description,
				$content_margin,
				$hover_background,
				$hover_border,
				$hover_title,
				$hover_description,
				$integrate_icon,
				$integrate_divider,
				$animation,
				$class
			);

			return array(
				'name'        => __( 'Info Box', 'mpc' ),
				'description' => __( 'Text block with icon', 'mpc' ),
				'base'        => 'mpc_icon_column',
				'class'       => '',
//				'icon'        => mpc_get_plugin_path( __FILE__ ) . '/assets/images/icons/mpc-icon-column.png',
				'icon'        => 'mpc-shicon-icon-column',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Icon_Column' ) ) {
	global $MPC_Icon_Column;
	$MPC_Icon_Column = new MPC_Icon_Column;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_icon_column' ) ) {
	class WPBakeryShortCode_mpc_icon_column extends MPCShortCode_Base {}
}
