<?php
/*----------------------------------------------------------------------------*\
	ICON COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Timeline_Item' ) ) {
	class MPC_Timeline_Item {
		public $shortcode = 'mpc_timeline_item';
		private $parts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			$this->parts = array(
				'icon'          => '',
				'divider'       => '',
				'title'         => '',
				'content'       => '',
			);
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Build layout */
		function layout( $layout ) {
			$content = '';

			if( $layout == '' )
				return '';

			$parts = explode( ',', $layout );

			if( ! isset( $parts ) )
				return '';

			foreach( $parts as $part ) {
				$content .= $this->parts[ $part ];
			}

			return $content;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $description = null ) {
			global $MPC_Icon, $MPC_Divider, $MPC_Tooltip, $mpc_ma_options;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'layout' => 'icon,divider,title,content',

				'alignment' => 'center',

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
				'background_overlay'    => '',

				'padding_css' => '',
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
				'hover_background_overlay'    => '',

				'hover_border_css' => '',

				'hover_title_color'    => '',
				'hover_content_color'  => '',

				/* Icon */
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

				/* Divider */
				'mpc_divider__align' => 'center',
				'mpc_divider__width' => '100',

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

				'mpc_divider__padding_css'  => '',
				'mpc_divider__margin_css'   => '',

				/* Timestamp Icon & Pointer */
				'pointer_disable'     => '',
				'pointer_color'       => '',
				'pointer_hover_color' => '',
				'stamp__disable'      => '',
				'stamp__label'        => '',
				'stamp__transition'   => 'none',

				'stamp__padding_css' => '',
				'stamp__border_css'  => '',

				'stamp__icon_type'       => 'icon',
				'stamp__icon'            => '',
				'stamp__icon_character'  => '',
				'stamp__icon_image'      => '',
				'stamp__icon_image_size' => 'thumbnail',
				'stamp__icon_preset'     => '',
				'stamp__icon_size'       => '',
				'stamp__icon_color'      => '',

				'stamp__background_type'       => 'color',
				'stamp__background_color'      => '',
				'stamp__background_image'      => '',
				'stamp__background_image_size' => 'large',
				'stamp__background_repeat'     => 'no-repeat',
				'stamp__background_size'       => 'initial',
				'stamp__background_position'   => 'middle-center',
				'stamp__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'stamp__hover_border_css' => '',

				'stamp__hover_icon_type'       => 'icon',
				'stamp__hover_icon'            => '',
				'stamp__hover_icon_character'  => '',
				'stamp__hover_icon_image'      => '',
				'stamp__hover_icon_image_size' => 'thumbnail',
				'stamp__hover_icon_preset'     => '',
				'stamp__hover_icon_color'      => '',

				'stamp__hover_background_type'       => 'color',
				'stamp__hover_background_color'      => '',
				'stamp__hover_background_image'      => '',
				'stamp__hover_background_image_size' => 'large',
				'stamp__hover_background_repeat'     => 'no-repeat',
				'stamp__hover_background_size'       => 'initial',
				'stamp__hover_background_position'   => 'middle-center',
				'stamp__hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'mpc_tooltip__disable'       => '',
				'mpc_tooltip__visible'       => '',
				'mpc_tooltip__text'          => '',
				'mpc_tooltip__trigger'       => 'hover',
				'mpc_tooltip__position'      => 'top',
				'mpc_tooltip__show_effect'   => 'fade',
				'mpc_tooltip__disable_arrow' => '',
				'mpc_tooltip__disable_hover' => '',
				'mpc_tooltip__always_visible' => '',
				'mpc_tooltip__enable_wide'   => '',

				'mpc_tooltip__font_preset'      => '',
				'mpc_tooltip__font_color'       => '',
				'mpc_tooltip__font_size'        => '',
				'mpc_tooltip__font_line_height' => '',
				'mpc_tooltip__font_align'       => '',
				'mpc_tooltip__font_transform'   => '',

				'mpc_tooltip__padding_css' => '',
				'mpc_tooltip__border_css'  => '',

				'mpc_tooltip__background_color'      => '',
				'mpc_tooltip__background_image'      => '',
				'mpc_tooltip__background_image_size' => 'large',
				'mpc_tooltip__background_repeat'     => 'no-repeat',
				'mpc_tooltip__background_size'       => 'initial',
				'mpc_tooltip__background_position'   => 'middle-center',
				'mpc_tooltip__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			), $atts );

			/* Prepare */
			$animation    = MPC_Parser::animation( $atts );
			$atts_pointer = MPC_Parser::shortcode( $atts, 'stamp_' );
			$atts_tooltip = MPC_Parser::shortcode( $atts, 'mpc_tooltip_' );
			$atts_icon    = MPC_Parser::shortcode( $atts, 'mpc_icon_' );
			$atts_divider = MPC_Parser::shortcode( $atts, 'mpc_divider_' );

			foreach ( $atts_tooltip as $key => $value ){
				$atts_tooltip[ 'mpc_tooltip__' . $key ] = $value;
				unset( $atts_tooltip[ $key ] );
			}
			$atts_pointer = array_merge( $atts_pointer, $atts_tooltip );

			$styles = $this->shortcode_styles( $atts );
			$css_id = $styles[ 'id' ];

			/* Shortcode classes | Animation | Layout */
			$classes = ' mpc-transition';
			$classes .= $atts[ 'alignment' ] != '' ? ' mpc-align--' . esc_attr( $atts[ 'alignment' ] ) : ' mpc-align--center';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );
			$classes_wrap = ' mpc-init mpc-parent-hover';
			$classes_wrap .= $animation != '' ? ' mpc-animation' : '';
			$classes_wrap .= $atts[ 'mpc_tooltip__visible' ] == '' ? ' mpc-tooltip--always' : '';
			$classes_title   = $atts[ 'title_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'title_font_preset' ] ) : '';
			$classes_title   .= ' mpc-transition';
			$classes_content = $atts[ 'content_font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'content_font_preset' ] ) : '';

			/* Layout parts */
			$enabled_elements = explode( ',', $atts[ 'layout' ] );
			$this->parts[ 'stamp' ]   = $atts[ 'stamp__disable' ] == '' ? '<div class="mpc-tl-icon">' . $MPC_Icon->shortcode_template( $atts_pointer ) . '</div>' : '';
			$this->parts[ 'icon' ]    = in_array( 'icon', $enabled_elements ) ? $MPC_Icon->shortcode_template( $atts_icon ) : '';
			$this->parts[ 'title' ]   = in_array( 'title', $enabled_elements ) && $atts[ 'title' ] != '' ? '<h3 class="mpc-timeline-item__heading' . $classes_title . '">' . $atts[ 'title' ] . '</h3>' : '';
			$this->parts[ 'divider' ] = in_array( 'divider', $enabled_elements ) ? $MPC_Divider->shortcode_template( $atts_divider ) : '';
			$this->parts[ 'content' ] = in_array( 'content', $enabled_elements ) && $description != '' ? '<div class="mpc-timeline-item__description' . $classes_content . '">' . wpb_js_remove_wpautop( $description, true ) . '</div>' : '';

			/* Shortcode Output */
			$return = '<div class="mpc-timeline-item__wrap' . $classes_wrap . '" ' . $animation . '>';
				$return .= '<div data-id="' . $css_id . '" class="mpc-timeline-item' . $classes . '">';
					$return .= '<div class="mpc-tl-before"></div>';
					$return .= $this->layout( $atts[ 'layout' ] );
				$return .= '</div>';
				$return .= $this->parts[ 'stamp' ];
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
			$css_id = uniqid( $this->shortcode . '-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'title_font_size' ]  = $styles[ 'title_font_size' ] != '' ? $styles[ 'title_font_size' ] . ( is_numeric( $styles[ 'title_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'content_font_size' ]  = $styles[ 'content_font_size' ] != '' ? $styles[ 'content_font_size' ] . ( is_numeric( $styles[ 'content_font_size' ] ) ? 'px' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if( $styles[ 'background_overlay' ] != '' ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"]:before {';
					$style .= 'background-color: ' . $styles[ 'background_overlay' ] . ';';
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_border_css' ] ) { $inner_styles[] = $styles[ 'hover_border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-timeline-item__wrap:hover .mpc-timeline-item[data-id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if( $styles[ 'hover_background_overlay' ] != '' ) {
				$style .= '.mpc-timeline-item__wrap:hover [data-id="' . $css_id . '"]:before {';
					$style .= 'background-color: ' . $styles[ 'hover_background_overlay' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_title_color' ] ) {
				$style .= '.mpc-timeline-item__wrap:hover .mpc-timeline-item[data-id="' . $css_id . '"] .mpc-timeline-item__heading {';
					$style .= 'color: ' . $styles[ 'hover_title_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_content_color' ] ) {
				$style .= '.mpc-timeline-item__wrap:hover .mpc-timeline-item[data-id="' . $css_id . '"] .mpc-timeline-item__description {';
					$style .= 'color: ' . $styles[ 'hover_content_color' ] . ';';
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'title_margin_css' ] ) { $inner_styles[] = $styles[ 'title_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'title' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0  ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"] .mpc-timeline-item__heading {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'content_margin_css' ] ) { $inner_styles[] = $styles[ 'content_margin_css' ]; }
			if ( $temp_style = MPC_CSS::font( $styles, 'content' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0  ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"] .mpc-timeline-item__description {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Triangle
			if( $styles[ 'pointer_disable' ] != '' || $styles[ 'stamp__disable' ] != '' ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"] .mpc-tl-before {';
					$style .= 'border-color: transparent !important;';
				$style .= '}';
			}
			if( $styles[ 'pointer_color' ] != '' ) {
				$style .= '.mpc-timeline-item[data-id="' . $css_id . '"] .mpc-tl-before {';
					$style .= 'border-color: ' . $styles[ 'pointer_color' ] . ';';
				$style .= '}';
			}
			if( $styles[ 'pointer_hover_color' ] != '' ) {
				$style .= '.mpc-timeline-item__wrap:hover .mpc-timeline-item[data-id="' . $css_id . '"] .mpc-tl-before {';
					$style .= 'border-color: ' . $styles[ 'pointer_hover_color' ] . ';';
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
					'type'             => 'mpc_list',
					'heading'          => __( 'Elements Order', 'mpc' ),
					'param_name'       => 'layout',
					'tooltip'          => __( 'Layout styles let you choose the target layout after you define the shortcode options.', 'mpc' ),
					'value'            => 'icon,divider,title,content',
					'options' => array(
						'icon'        => __( 'Icon', 'mpc' ),
						'title'       => __( 'Title', 'mpc' ),
						'divider'     => __( 'Divider', 'mpc' ),
						'content'     => __( 'Content', 'mpc' ),
					),
					'edit_field_class' => 'vc_col-sm-8 vc_column mpc-advanced-field',
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
					'heading'     => __( 'Content', 'mpc' ),
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
			$icon_exclude = array( 'exclude_regex' => '/animation(.*)|url|class|tooltip_(.*)/', );
			$integrate_icon = vc_map_integrate_shortcode( 'mpc_icon', 'mpc_icon__', __( 'Icon', 'mpc' ), $icon_exclude );

			/* Integrate Divider */
			$divider_exclude = array( 'exclude_regex' => '/animation_(.*)|class/', );
			$integrate_divider = vc_map_integrate_shortcode( 'mpc_divider', 'mpc_divider__', __( 'Divider', 'mpc' ), $divider_exclude );

			/* Integrate Icon */
			$pointer_exclude = array( 'exclude_regex' => '/animation(.*)|preset|class|margin(.*)|tooltip(.*)|url/', );
			$integrate_pointer = vc_map_integrate_shortcode( 'mpc_icon', 'stamp__', __( 'Timestamp', 'mpc' ), $pointer_exclude );
			$disable_pointer   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Pointer', 'mpc' ),
					'param_name'       => 'pointer_disable',
					'tooltip'          => __( 'Check to disable pointer.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Pointer', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Pointer Color', 'mpc' ),
					'param_name'       => 'pointer_color',
					'tooltip'          => __( 'If empty, the default pointer color will be same as Timeline item border.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Pointer', 'mpc' ),
					'dependency'       => array( 'element' => 'pointer_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Pointer Hover Color', 'mpc' ),
					'param_name'       => 'pointer_hover_color',
					'tooltip'          => __( 'If empty, the default pointer color will be same as Timeline item hover border.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'group'            => __( 'Pointer', 'mpc' ),
					'dependency'       => array( 'element' => 'pointer_disable', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Timestamp', 'mpc' ),
					'param_name'       => 'stamp__disable',
					'tooltip'          => __( 'Check to disable timestamp icon.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Timestamp', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_pointer = array_merge( $disable_pointer, $integrate_pointer );

			/* Integrate Tooltip */
			$tooltip_exclude = array( 'exclude_regex' => '/class|visible/', );
			$integrate_tooltip = vc_map_integrate_shortcode( 'mpc_tooltip', 'mpc_tooltip__', __( 'Label', 'mpc' ), $tooltip_exclude );
			$disable_tooltip   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Tooltip', 'mpc' ),
					'param_name'       => 'mpc_tooltip__disable',
					'tooltip'          => __( 'Check to disable timestamp tooltip.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Label', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Always Visible', 'mpc' ),
					'param_name'       => 'mpc_tooltip__visible',
					'tooltip'          => __( 'Check to show tooltip on hover only.', 'mpc' ),
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'group'            => __( 'Label', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
			);
			$integrate_tooltip = array_merge( $disable_tooltip, $integrate_tooltip );

			$background_overlay = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Overlay', 'mpc' ),
					'param_name'       => 'background_overlay',
					'tooltip'          => __( 'Adds a color overlay to image background.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker mpc-clear--both',
//					'dependency'       => array( 'element' => 'background_type', 'value' => 'image' ),
				),
			);
			$hover_background_overlay = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Overlay', 'mpc' ),
					'param_name'       => 'hover_background_overlay',
					'tooltip'          => __( 'Adds a color overlay to image background.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-color-picker mpc-clear--both',
//					'dependency'       => array( 'element' => 'hover_background_type', 'value' => 'image' ),
				),
			);

			$title_font       = MPC_Snippets::vc_font( array( 'prefix' => 'title', 'subtitle' => __( 'Title', 'mpc' ), 'group' => __( 'Title', 'mpc' ), ) );
			$description_font = MPC_Snippets::vc_font( array( 'prefix' => 'content', 'subtitle' => __( 'Content', 'mpc' ), 'group' => __( 'Content', 'mpc' ), ) );

			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();

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
				$background_overlay,
				$border,
				$padding,
				$title_font,
				$title,
				$title_margin,
				$description_font,
				$description,
				$content_margin,
				$hover_background,
				$hover_background_overlay,
				$hover_border,
				$hover_title,
				$hover_description,
				$integrate_icon,
				$integrate_divider,
				$integrate_pointer,
				$integrate_tooltip,
				$animation,
				$class
			);

			return array(
				'name'            => __( 'Timeline Item', 'mpc' ),
				'description'     => __( 'Adds new item to timeline.', 'mpc' ),
				'base'            => $this->shortcode,
				'class'           => '',
				'icon'            => 'mpc-shicon-timeline-item',
				'as_child'        => array( 'only' => 'mpc_timeline_basic' ),
				'content_element' => true,
				'category'        => __( 'Massive', 'mpc' ),
				'params'          => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_Timeline_Item' ) ) {
	global $MPC_Timeline_Item;
	$MPC_Timeline_Item = new MPC_Timeline_Item;
}

if ( class_exists( 'MPCShortCode_Base' ) && ! class_exists( 'WPBakeryShortCode_mpc_timeline_item' ) ) {
	class WPBakeryShortCode_mpc_timeline_item extends MPCShortCode_Base {}
}
