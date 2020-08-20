<?php
/*----------------------------------------------------------------------------*\
	ICON LIST SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Icon_List' ) ) {
	class MPC_Icon_List {
		public $shortcode = 'mpc_icon_list';

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
			wp_enqueue_style( $this->shortcode . '-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css ', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( $this->shortcode . '-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null ) {
			global $mpc_ma_options, $MPC_Icon;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$atts = shortcode_atts( array(
				'class'  => '',
				'preset' => '',

				'list' => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'border_css'  => '',
				'padding_css' => '',
				'margin_css'  => '',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'list_margin_css'  => '',
				'list_padding_css' => '',
				'list_border_css'  => '',

				/* Icon */
				'icon_position'        => 'left',
				'mpc_icon__disable'    => '',
				'mpc_icon__preset'     => 'default',
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

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',
			), $atts );

			$list_items = vc_param_group_parse_atts( $atts[ 'list' ] );

			if( !is_array( $list_items ) ) {
				return null;
			}

			$styles = $this->shortcode_styles( $atts, $list_items );
			$css_id = $styles[ 'id' ];

			$animation = MPC_Parser::animation( $atts );

			$atts_icon = MPC_Parser::shortcode( $atts, 'mpc_icon_' );
			$icon = $atts[ 'mpc_icon__disable' ] == '' ? '<div class="mpc-list__icon">' . $MPC_Icon->shortcode_template( $atts_icon ) . '</div>' : '';

			$classes = ' mpc-init';
			$classes .= $animation != '' ? ' mpc-animation' : '';
			$classes .= $atts[ 'icon_position' ] != '' ? ' mpc-icon--' . esc_attr( $atts[ 'icon_position' ] ) : '';
			$classes .= $atts[ 'font_preset' ] != '' ? ' mpc-typography--' . esc_attr( $atts[ 'font_preset' ] ) : '';
			$classes .= ' ' . esc_attr( $atts[ 'class' ] );

			$return = '<div id="' . $css_id . '" class="mpc-icon-list' . $classes . '"' . $animation . '>';
				$return .= '<ul class="mpc-list__ul">';

			for( $i=0; $i < count( $list_items ); $i++ ) {
				$item = $list_items[ $i ];

				$return .= '<li class="mpc-list__item mpc-parent-hover mpc-transition" data-index="' . $i . '">';
					$return .= $icon;
					$return .= isset( $item[ 'title' ] ) && $item[ 'title' ] != '' ? '<span class="mpc-list__title">' . apply_filters( 'ma/mpc_icon_list/item/content', $item[ 'title' ] ) . '</span>' : '';
				$return .= '</li>';
			}

				$return .= '</ul>';
			$return .= '</div>';

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $list_items ) {
			global $mpc_massive_styles;
			$css_id = uniqid( 'mpc_icon_list-' . rand( 1, 100 ) );
			$style = '';

			// Add 'px'
			$styles[ 'font_size' ] = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';

			$inner_styles = array();
			if ( $styles[ 'padding_css' ] ) { $inner_styles[] = $styles[ 'padding_css' ]; }
			if ( $styles[ 'margin_css' ] ) { $inner_styles[] = $styles[ 'margin_css' ]; }
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $temp = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp; }
			if ( $temp = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-icon-list[id="' . $css_id . '"] {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// List items
			$inner_styles = array();
			if ( $styles[ 'list_padding_css' ] ) { $inner_styles[] = $styles[ 'list_padding_css' ]; }
			if ( $styles[ 'list_margin_css' ] ) { $inner_styles[] = $styles[ 'list_margin_css' ]; }
			if ( $styles[ 'list_border_css' ] ) { $inner_styles[] = $styles[ 'list_border_css' ]; }

			if ( count( $inner_styles ) > 0 ) {
				$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			// Colors per line
			for( $i=0; $i < count( $list_items ); $i++ ) {
				$item = $list_items[ $i ];

				$inner_styles = array();
				if( isset( $item[ 'text_color' ] ) ) { $inner_styles[] = 'color:' . $item[ 'text_color' ] . ';'; }
				if( isset( $item[ 'bg_color' ] ) ) { $inner_styles[] = 'background:' . $item[ 'bg_color' ] . ';'; }

				if( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item[data-index="' . $i . '"] {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				$inner_styles = array();
				if( isset( $item[ 'hover_text_color' ] ) ) { $inner_styles[] = 'color:' . $item[ 'hover_text_color' ] . ';'; }
				if( isset( $item[ 'hover_bg_color' ] ) ) { $inner_styles[] = 'background:' . $item[ 'hover_bg_color' ] . ';'; }

				if( count( $inner_styles ) > 0 ) {
					$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item[data-index="' . $i . '"]:hover {';
						$style .= join( '', $inner_styles );
					$style .= '}';
				}

				if( isset( $item[ 'icon_color' ] ) ) {
					$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item[data-index="' . $i . '"] i {';
						$style .= 'color:' . $item[ 'icon_color' ] . ' !important;';
					$style .= '}';
				}

				if( isset( $item[ 'hover_icon_color' ] ) ) {
					$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item[data-index="' . $i . '"] i.mpc-hover,';
					$style .= '.mpc-icon-list[id="' . $css_id . '"] .mpc-list__item[data-index="' . $i . '"]:hover i {';
						$style .= 'color:' . $item[ 'hover_icon_color' ] . ' !important;';
					$style .= '}';
				}
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

			/* Integrate Icon */
			$icon_exclude = array( 'exclude_regex' => '/animation_(.*)|url|tooltip_(.*)/', );
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
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-section-disabler',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Position', 'mpc' ),
					'param_name'       => 'icon_position',
					'tooltip'          => __( 'Select icon position inside list.', 'mpc' ),
					'value'            => array(
						__( 'Left', 'mpc' )  => 'left',
						__( 'Right', 'mpc' ) => 'right',
					),
					'std'              => 'left',
					'group'            => __( 'Icon', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-clear mpc-first-row',
					'dependency'       => array( 'element' => 'mpc_icon__disable', 'value_not_equal_to' => 'true' ),
				),
			);
			$integrate_icon = array_merge( $disable_icon, $integrate_icon );

			$list_border  = MPC_Snippets::vc_border( array( 'prefix' => 'list', 'subtitle' => __( 'Items', 'mpc' ), 'group' => __( 'List', 'mpc' ) ) );
			$list_margin  = MPC_Snippets::vc_margin( array( 'prefix' => 'list', 'subtitle' => __( 'Items', 'mpc' ), 'group' => __( 'List', 'mpc' ) ) );
			$list_padding = MPC_Snippets::vc_padding( array( 'prefix' => 'list', 'subtitle' => __( 'Items', 'mpc' ), 'group' => __( 'List', 'mpc' ) ) );

			$font       = MPC_Snippets::vc_font();
			$border     = MPC_Snippets::vc_border();
			$margin     = MPC_Snippets::vc_margin();
			$padding    = MPC_Snippets::vc_padding();
			$background = MPC_Snippets::vc_background();

			$animation = MPC_Snippets::vc_animation_basic();
			$class     = MPC_Snippets::vc_class();

			$list = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'List items', 'mpc' ),
					'param_name' => 'list_divider',
					'std'        => '',
					'group'      => __( 'List', 'mpc' ),
				),
				array(
					'type'       => 'param_group',
					'value'      => '',
					'param_name' => 'list',
					'group'      => __( 'List', 'mpc' ),
					'params'     => self::vc_list_params(),
				),
			);

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
			);

			return array(
				'name'        => __( 'Icon List', 'mpc' ),
				'description' => __( 'Display a list with icon.', 'mpc' ),
				'base'        => 'mpc_icon_list',
				'class'       => '',
				'icon'        => 'mpc-shicon-icon-list',
				'category'    => __( 'Massive', 'mpc' ),
				'params'      => array_merge( $base, $integrate_icon, $font, $background, $border, $padding, $margin, $list, $list_border, $list_padding, $list_margin, $animation, $class ),
			);
		}

		static function vc_list_params() {
			$return = array(
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Icon Color', 'mpc' ),
					'param_name'       => 'icon_color',
					'tooltip'          => __( 'Choose icon color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field mpc-color-picker type--icon type--character',
					'group'            => __( 'List', 'mpc' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon', 'character' )
					),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Icon Color', 'mpc' ),
					'param_name'       => 'hover_icon_color',
					'tooltip'          => __( 'Choose hover icon color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field mpc-color-picker type--icon type--character',
					'group'            => __( 'List', 'mpc' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon', 'character' )
					),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Text Color', 'mpc' ),
					'param_name'       => 'text_color',
					'tooltip'          => __( 'Choose text color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field mpc-color-picker',
					'group'            => __( 'List', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Text Color', 'mpc' ),
					'param_name'       => 'hover_text_color',
					'tooltip'          => __( 'Choose text color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column mpc-advanced-field mpc-clear mpc-color-picker',
					'group'            => __( 'List', 'mpc' ),
				),
				array(
					'type'             => 'textarea',
					'heading'          => __( 'Content', 'mpc' ),
					'param_name'       => 'title',
					'tooltip'          => __( 'Define item content. <br/>HTML markup is supported.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Background Color', 'mpc' ),
					'param_name'       => 'bg_color',
					'tooltip'          => __( 'Choose background color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field  mpc-color-picker',
					'group'            => __( 'List', 'mpc' ),
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Hover Background Color', 'mpc' ),
					'param_name'       => 'hover_bg_color',
					'tooltip'          => __( 'Choose background color.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-4 vc_column mpc-advanced-field  mpc-color-picker',
					'group'            => __( 'List', 'mpc' ),
				),
				array(
					'type'             => 'hidden',
					'param_name'       => 'icon_type',
					'std'              => 'icon',
				),
			);

			return $return;
		}
	}
}

if ( class_exists( 'MPC_Icon_List' ) ) {
	global $MPC_Icon_List;
	$MPC_Icon_List = new MPC_Icon_List;
}
