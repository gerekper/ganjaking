<?php
/*----------------------------------------------------------------------------*\
	ADD TO CART SHORTCODE
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_WC_Add_To_Cart' ) ) {
	class MPC_WC_Add_To_Cart {
		public $shortcode = 'mpc_wc_add_to_cart';
		public $style = '';
		private $defaults = array();
		private $atts = array();

		function __construct() {
			add_shortcode( $this->shortcode, array( $this, 'shortcode_template' ) );

			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( $this->shortcode, array( $this, 'shortcode_map' ) );
			} else {
				add_action( 'init', array( $this, 'shortcode_map_fallback' ) );
			}

			/* Autocomplete */
			add_filter( 'vc_autocomplete_mpc_wc_add_to_cart_item_id_callback', 'MPC_Autocompleter::suggest_wc_product', 10, 1 );
			add_filter( 'vc_autocomplete_mpc_wc_add_to_cart_item_id_render', 'MPC_Autocompleter::render_wc_product', 10, 1 );

			/* AJAX page load */
			add_action( 'wp_ajax_mpc_get_variations', array( $this, 'get_variations_options' ) );

			add_action( 'wp_ajax_mpc_wc_add_to_cart', array( $this, 'add_to_cart' ) );
			add_action( 'wp_ajax_nopriv_mpc_wc_add_to_cart', array( $this, 'add_to_cart' ) );
		}

		function shortcode_map_fallback() {
			vc_map( $this->shortcode_map() );
		}

		function shortcode_defaults() {
			$this->defaults = array(
				'preset'       => '',
				'item_id'      => '',
				'variation_id' => 'none',
				'title_type'   => 'price',
				'title'        => '',
				'block'        => '',
				'auto_size'    => '',

				'font_preset'      => '',
				'font_color'       => '',
				'font_size'        => '',
				'font_line_height' => '',
				'font_align'       => '',
				'font_transform'   => '',

				'padding_css' => '',
				'margin_css'  => '',
				'border_css'  => '',

				'icon_type'       => 'icon',
				'icon'            => '',
				'icon_character'  => '',
				'icon_image'      => '',
				'icon_image_size' => 'thumbnail',
				'icon_preset'     => '',
				'icon_color'      => '#333333',
				'icon_size'       => '',
				'icon_gap'        => '',
				'icon_position'   => 'left',

				'hover_effect' => 'fade-in',

				'background_type'       => 'color',
				'background_color'      => '',
				'background_image'      => '',
				'background_image_size' => 'large',
				'background_repeat'     => 'no-repeat',
				'background_size'       => 'initial',
				'background_position'   => 'middle-center',
				'background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'hover_background_effect' => 'fade-in',
				'hover_background_offset' => '',

				'hover_border_css' => '',

				'hover_title_type' => 'none',
				'hover_none_icon_color' => '',
				'hover_title'      => '',
				'hover_font_size'  => '',
				'hover_font_color' => '',
				'hover_font_align' => '',

				'hover_icon_type'       => 'icon',
				'hover_icon'            => '',
				'hover_icon_character'  => '',
				'hover_icon_image'      => '',
				'hover_icon_image_size' => 'thumbnail',
				'hover_icon_preset'     => '',
				'hover_icon_color'      => '#333333',
				'hover_icon_size'       => '',
				'hover_icon_gap'        => '',
				'hover_icon_position'   => 'left',

				'hover_background_type'       => 'color',
				'hover_background_color'      => '',
				'hover_background_image'      => '',
				'hover_background_image_size' => 'large',
				'hover_background_repeat'     => 'no-repeat',
				'hover_background_size'       => 'initial',
				'hover_background_position'   => 'middle-center',
				'hover_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',

				'notices_effect'         => 'fade-in',
				'loader_icon_type'       => 'icon',
				'loader_icon'            => '',
				'loader_icon_character'  => '',
				'loader_icon_image'      => '',
				'loader_icon_image_size' => 'thumbnail',
				'loader_icon_preset'     => '',
				'loader_icon_color'      => '#333333',
				'loader_icon_size'       => '',
				'loader_icon_spin'       => 'rotate',
				'loader_position'        => 'inside',
				'loader_align'           => 'left',
				'loader_gap'             => '',

				'loader_background_type'       => 'color',
				'loader_background_color'      => '',
				'loader_background_image'      => '',
				'loader_background_image_size' => 'large',
				'loader_background_repeat'     => 'no-repeat',
				'loader_background_size'       => 'initial',
				'loader_background_position'   => 'middle-center',
				'loader_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'loader_border'                => '',

				'success_icon_type'       => 'icon',
				'success_icon'            => '',
				'success_icon_character'  => '',
				'success_icon_image'      => '',
				'success_icon_image_size' => 'thumbnail',
				'success_icon_preset'     => '',
				'success_icon_color'      => '#333333',
				'success_icon_size'       => '',

				'success_background_type'       => 'color',
				'success_background_color'      => '',
				'success_background_image'      => '',
				'success_background_image_size' => 'large',
				'success_background_repeat'     => 'no-repeat',
				'success_background_size'       => 'initial',
				'success_background_position'   => 'middle-center',
				'success_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'success_border'                => '',

				'error_icon_type'       => 'icon',
				'error_icon'            => '',
				'error_icon_character'  => '',
				'error_icon_image'      => '',
				'error_icon_image_size' => 'thumbnail',
				'error_icon_preset'     => '',
				'error_icon_color'      => '#333333',
				'error_icon_size'       => '',

				'error_background_type'       => 'color',
				'error_background_color'      => '',
				'error_background_image'      => '',
				'error_background_image_size' => 'large',
				'error_background_repeat'     => 'no-repeat',
				'error_background_size'       => 'initial',
				'error_background_position'   => 'middle-center',
				'error_background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
				'error_border'                => '',

				'animation_in_type'     => 'none',
				'animation_in_duration' => '300',
				'animation_in_delay'    => '0',
				'animation_in_offset'   => '100',

				'animation_loop_type'     => 'none',
				'animation_loop_duration' => '1000',
				'animation_loop_delay'    => '1000',
				'animation_loop_hover'    => '',

				/* Tooltip */
				'mpc_tooltip__disable'    => '',

				'mpc_tooltip__preset'        => '',
				'mpc_tooltip__text'          => '',
				'mpc_tooltip__position'      => 'top',
				'mpc_tooltip__trigger'       => 'hover',
				'mpc_tooltip__show_effect'   => '',
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

				'mpc_tooltip__background_type'       => 'color',
				'mpc_tooltip__background_color'      => '',
				'mpc_tooltip__background_image'      => '',
				'mpc_tooltip__background_image_size' => 'large',
				'mpc_tooltip__background_repeat'     => 'no-repeat',
				'mpc_tooltip__background_size'       => 'initial',
				'mpc_tooltip__background_position'   => 'middle-center',
				'mpc_tooltip__background_gradient'   => '#83bae3||#80e0d4||0;100||180||linear',
			);
		}

		/* Handle add to cart */
		function add_to_cart() {
			if( !class_exists( 'WooCommerce' ) ) {
				die();
			}

			$product_id   = isset( $_POST[ 'product_id' ] ) ? absint( $_POST[ 'product_id' ] ) : false;			
			$variation_id = isset( $_POST[ 'variation_id' ] ) ? absint( $_POST[ 'variation_id' ] ) : false;

			if( $product_id && $variation_id ) {
				$variation = wc_get_product_variation_attributes( $variation_id );
				$result = WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation );

				WC_AJAX::get_refreshed_fragments();
			} else if( $product_id ) {
				$result = WC()->cart->add_to_cart( $product_id, 1 );

				WC_AJAX::get_refreshed_fragments();
			} else {
				$result = false;
			}

			echo $result;
			die();
		}

		/* Enqueue all styles/scripts required by shortcode */
		function enqueue_shortcode_scripts() {
			wp_enqueue_style( 'mpc_wc_add_to_cart-css', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/css/' . $this->shortcode . '.css', array(), MPC_MASSIVE_VERSION );
			wp_enqueue_script( 'mpc_wc_add_to_cart-js', mpc_get_plugin_path( __FILE__ ) . '/shortcodes/' . $this->shortcode . '/js/' . $this->shortcode . MPC_MASSIVE_MIN . '.js', array( 'jquery' ), MPC_MASSIVE_VERSION );
		}

		/* Retrieve Button Title Content */
		function get_title( $product, $type, $state = '' ) {
			switch( $type ) {
				case 'icon' : return ''; break;
				case 'price' : return apply_filters( 'ma/atc/get/price', $product->get_price_html(), 'atc_price' ); break;
				case 'title' : return apply_filters( 'ma/atc/get/title', $product->get_title(), 'atc_title' ); break;
				case 'custom' : return $this->atts[ $state . 'title' ]; break;
				default: return ''; break;
			}
		}

		/* Retrieve Button Icon */
		function get_icon( $icon = array(), $classes_icon ) {
			if( empty( $icon ) ) return '';

			return '<i class="mpc-atc__icon mpc-transition ' . $icon[ 'class' ] . $classes_icon . '">' . $icon[ 'content' ] . '</i>';
		}

		/* Output Title Markup */
		function get_title_markup( $product, $icon, $state = '' ) {
			$classes_icon = $this->atts[ $state . 'icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			$icon  = $this->get_icon( $icon, $classes_icon );
			$title = '<span>' . $this->get_title( $product, $this->atts[ $state . 'title_type' ], $state ) . '</span>';

			if( $this->atts[ $state . 'icon_position' ] == 'left' ) {
				return $icon . $title;
			} else {
				return $title . $icon;
			}
		}

		/* Output Notices */
		function prepare_notices_atts() {
			$icons_defaults = array(
				'loader' => array(
					'loader_icon' => 'fa fa-refresh',
					'loader_icon_color' => '#ffffff',
				),
				'success' => array(
					'success_icon' => 'fa fa-check',
					'success_icon_color' => '#ffffff',
				),
				'error' => array(
					'error_icon' => 'fa fa-exclamation-triangle',
					'error_icon_color' => '#ffffff',
				),
			);

			foreach( $icons_defaults as $notice => $values ) {
				if( $this->atts[ $notice . '_icon_type' ] == 'icon' && $this->atts[ $notice . '_icon' ] == '' ) {
					$this->atts[ $notice . '_icon' ] = $values[ $notice . '_icon' ];
					$this->atts[ $notice . '_icon_color' ] = $values[ $notice . '_icon_color' ];
				}
			}
		}
		function loader_notice() {
			$loader_icon  = MPC_Parser::icon( $this->atts, 'loader' );
			$classes_loader  = $this->atts[ 'loader_icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			if( $this->atts[ 'loader_position' ] == 'inside' ) {
				return '<div class="mpc-atc__notice mpc--loader">' . $this->get_icon( $loader_icon, $classes_loader ) . '</div>';
			}

			$attr = $this->atts[ 'loader_icon_spin' ] != 'none' ? ' data-spinner="' . esc_attr( $this->atts[ 'loader_icon_spin' ] ) . '"' : '';
			$attr .= $this->atts[ 'notices_effect' ] != '' ? ' data-effect="' . esc_attr( $this->atts[ 'notices_effect' ] ) . '"' : '';
			$attr .= $this->atts[ 'loader_align' ] != '' ? ' data-side="' . esc_attr( $this->atts[ 'loader_align' ] ) . '"' : '';

			return '<div class="mpc-atc__notice mpc--loader mpc-atc--outside mpc-transition"' . $attr . '>' . $this->get_icon( $loader_icon, $classes_loader ) . '</div>';
		}
		function notices_markup() {
			$success_icon = MPC_Parser::icon( $this->atts, 'success' );
			$error_icon   = MPC_Parser::icon( $this->atts, 'error' );

			$classes_success = $this->atts[ 'success_icon_type' ] == 'image' ? ' mpc-icon--image' : '';
			$classes_error   = $this->atts[ 'error_icon_type' ] == 'image' ? ' mpc-icon--image' : '';

			$attr = $this->atts[ 'notices_effect' ] != '' ? ' data-effect="' . esc_attr( $this->atts[ 'notices_effect' ] ) . '"' : '';
			$attr .= $this->atts[ 'loader_position' ] == 'inside' && $this->atts[ 'loader_icon_spin' ] != 'none' ? ' data-spinner="' . esc_attr( $this->atts[ 'loader_icon_spin' ] ) . '"' : '';

			$return = '<div class="mpc-atc__notices mpc-transition"' . $attr . '>';
				$return .= $this->atts[ 'loader_position' ] == 'inside' ? $this->loader_notice() : '';
				$return .= '<div class="mpc-atc__notice mpc--success"><a href="' . get_permalink( wc_get_page_id( 'cart' ) ) .'">' . $this->get_icon( $success_icon, $classes_success ) . '</a></div>';
				$return .= '<div class="mpc-atc__notice mpc--error">' . $this->get_icon( $error_icon, $classes_error ) . '</div>';
			$return .= '</div>';

			return $return;
		}

		/* Return shortcode markup for display */
		function shortcode_template( $atts, $content = null, $shortcode = null, $parent_css = null ) {
			if( !class_exists( 'WooCommerce' ) ) {
				return '';
			}

			global $MPC_Tooltip, $mpc_ma_options, $mpc_can_link, $mpc_button_separator;
			if ( ! defined( 'MPC_MASSIVE_FULL' ) || ( defined( 'MPC_MASSIVE_FULL' ) && $mpc_ma_options[ 'single_js_css' ] !== '1' ) ) {
				$this->enqueue_shortcode_scripts();
			}

			$this->shortcode_defaults();
			$this->atts = shortcode_atts( $this->defaults, $atts );
			$this->prepare_notices_atts();

			if( isset( $this->atts[ 'item_id' ] )  && (int) $this->atts[ 'item_id' ]) {
				$this->atts[ 'item_id' ] = (int) $this->atts[ 'item_id' ];
				$product = wc_get_product( $this->atts[ 'item_id' ] );

				if( !$product || !$product->is_visible() || !$product->is_purchasable() ) return '';

				$this->atts[ 'item_id' ] = apply_filters( 'ma/atc/id', $this->atts[ 'item_id' ], 'id' );
				$product_type   = property_exists( $product, 'product_type' ) ? $product->product_type : $product->get_type();

				if( $product_type  == 'variation' ) {
					$data_cart = ' data-cart=\'' . json_encode( array( 'product_id' => $this->atts[ 'item_id' ], 'variation_id' => $product->get_id() ) ) . '\'';
				} else if( !in_array( $product_type, array( 'external', 'variable' ) ) ) {
					$data_cart = ' data-cart=\'' . json_encode( array( 'product_id' => $this->atts[ 'item_id' ] ) ) . '\'';
				} else {
					$data_cart = '';
				}
			} else {
				return '';
			}

			if( $data_cart == '' ) {
				$url_settings =  $mpc_can_link ? MPC_Parser::url( 'url:' . urlencode( $product->add_to_cart_url() ). '|title:' . $product->add_to_cart_text() . '|' ) : '';
				$wrapper = $url_settings != '' ? 'a' : 'div';
			} else {
				$wrapper = 'div';
				$url_settings = '';
			}

			$icon       = MPC_Parser::icon( $this->atts );
			$hover_icon = MPC_Parser::icon( $this->atts, 'hover' );

			$hover_effect = explode( '-', $this->atts[ 'hover_effect' ] );
			if ( count( $hover_effect ) == 2 ) {
				$hover_effect_class = $hover_effect[ 0 ] != '' ? ' mpc-effect-type--' . $hover_effect[ 0 ] : '';
				$hover_effect_class .= $hover_effect[ 1 ] != '' ? ' mpc-effect-side--' . $hover_effect[ 1 ] : '';

			} else {
				$hover_effect_class = '';
			}

			$background_effect = explode( '-', $this->atts[ 'hover_background_effect' ] );
			if ( ! count( $background_effect ) == 2 ) {
				$background_effect = array( '', '' );
			}
			$background_effect_type = $background_effect[ 0 ] != '' ? 'mpc-effect-type--' . $background_effect[ 0 ] : '';
			$background_effect_side = $background_effect[ 1 ] != '' ? 'mpc-effect-side--' . $background_effect[ 1 ] : '';

			$styles = $this->shortcode_styles( $this->atts, $parent_css );
			$css_id = $styles[ 'id' ];
			$css_id = ! empty( $parent_css ) ? '' : ' data-id="' . $css_id . '"';

			$atts_tooltip = MPC_Parser::shortcode( $this->atts, 'mpc_tooltip_' );
			$tooltip      = $this->atts[ 'mpc_tooltip__disable' ] == '' ? $MPC_Tooltip->shortcode_template( $atts_tooltip ) : '';

			$animation = MPC_Parser::animation( $this->atts );
			$classes   = ' mpc-init mpc-transition';
			$classes   .= $animation != '' ? ' mpc-animation' : '';
			$classes   .= $this->atts[ 'font_preset' ] != '' ? ' mpc-typography--' . $this->atts[ 'font_preset' ] : '';
			$classes   .= $this->atts[ 'loader_position' ] == 'outside' ? ' mpc-loader--outside' : '';
			$classes   .= $this->atts[ 'auto_size' ] != '' ? ' mpc-auto-size' : '';

			$classes   .= $tooltip != '' && $this->atts[ 'loader_position' ] == 'inside' ? ' mpc-tooltip-target' : '';
			$classes_wrap = $tooltip != '' && $this->atts[ 'loader_position' ] == 'outside' ? ' mpc-tooltip-target' : '';
			$classes_wrap .= $this->atts[ 'block' ] != '' ? ' mpc-display--block' : '';

			$return = $tooltip != '' ? '<div class="mpc-tooltip-wrap ' . $classes_wrap .'"' . $css_id . '>' : '';
			$return .= '<div class="mpc-wc-add_to_cart-wrap' . $classes_wrap .'"' . $css_id . '>';
				$return .= '<' . $wrapper . $url_settings . $data_cart . ' class="mpc-wc-add_to_cart' . $classes . '" ' . $animation . '>';
					$return .= '<div class="mpc-atc__content ' . $hover_effect_class . '">';

						$return .= '<div class="mpc-atc__title mpc-transition">' . $this->get_title_markup( $product, $icon ) . '</div>';
						$return .= '<div class="mpc-atc__background mpc-transition ' . $background_effect_type . ' ' . $background_effect_side . '"></div>';

						if( $this->atts[ 'hover_title_type' ] == 'none' ) {
							$return .= '<div class="mpc-atc__title-hover mpc-transition">' . $this->get_title_markup( $product, $icon ) . '</div>';
						} else {
							$return .= '<div class="mpc-atc__title-hover mpc-transition">' . $this->get_title_markup( $product, $hover_icon, 'hover_' ). '</div>';
						}

						$return .= $url_settings == '' ? $this->notices_markup() : '';

					$return .= '</div>';
					$return .= '</' . $wrapper . '>';
				$return .= $this->atts[ 'loader_position' ] == 'outside' ? $this->loader_notice() : '';
			$return .= '</div>';
			$return .= $tooltip;
			$return .= $tooltip != '' ? '</div>' : '';

			if ( isset( $mpc_button_separator ) ) {
				$return .= $mpc_button_separator;
			}

			global $mpc_frontend;
			if ( $mpc_frontend ) {
				$return .= '<style>' . $styles[ 'css' ] . '</style>';
			}

			return $return;
		}

		/* Generate shortcode styles */
		function shortcode_styles( $styles, $parent_css = '' ) {
			global $mpc_massive_styles;
			$css_id       = uniqid( 'mpc_add_to_cart-' . rand( 1, 100 ) );
			$css_selector = '[data-id="' . $css_id . '"]';
			$style        = '';

			if ( is_array( $parent_css ) ) {
				$css_id       = $parent_css[ 'id' ];
				$css_selector = $parent_css[ 'selector' ];
			}

			if( $this->style === $css_id ) {
				return array(
					'id'  => $css_id,
					'css' => $this->style,
				);
			}

			$disabled_tooltip = $styles[ 'mpc_tooltip__disable' ] != '' || ( $styles[ 'mpc_tooltip__disable' ] == '' && $styles[ 'mpc_tooltip__text' ] == '' );

			// Add 'px'
			$styles[ 'font_size' ]         = $styles[ 'font_size' ] != '' ? $styles[ 'font_size' ] . ( is_numeric( $styles[ 'font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_font_size' ]   = $styles[ 'hover_font_size' ] != '' ? $styles[ 'hover_font_size' ] . ( is_numeric( $styles[ 'hover_font_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_size' ]         = $styles[ 'icon_size' ] != '' ? $styles[ 'icon_size' ] . ( is_numeric( $styles[ 'icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'icon_gap' ]          = $styles[ 'icon_gap' ] != '' ? $styles[ 'icon_gap' ] . ( is_numeric( $styles[ 'icon_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_icon_size' ]   = $styles[ 'hover_icon_size' ] != '' ? $styles[ 'hover_icon_size' ] . ( is_numeric( $styles[ 'hover_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'hover_icon_gap' ]    = $styles[ 'hover_icon_gap' ] != '' ? $styles[ 'hover_icon_gap' ] . ( is_numeric( $styles[ 'hover_icon_gap' ] ) ? 'px' : '' ) : '';
			$styles[ 'success_icon_size' ] = $styles[ 'success_icon_size' ] != '' ? $styles[ 'success_icon_size' ] . ( is_numeric( $styles[ 'success_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'loader_icon_size' ]  = $styles[ 'loader_icon_size' ] != '' ? $styles[ 'loader_icon_size' ] . ( is_numeric( $styles[ 'loader_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'error_icon_size' ]   = $styles[ 'error_icon_size' ] != '' ? $styles[ 'error_icon_size' ] . ( is_numeric( $styles[ 'error_icon_size' ] ) ? 'px' : '' ) : '';
			$styles[ 'loader_gap' ]        = $styles[ 'loader_gap' ] != '' ? $styles[ 'loader_gap' ] . ( is_numeric( $styles[ 'loader_gap' ] ) ? 'px' : '' ) : '';

			// Add '%'
			$styles[ 'hover_background_offset' ] = $styles[ 'hover_background_offset' ] != '' ? $styles[ 'hover_background_offset' ] . ( is_numeric( $styles[ 'hover_background_offset' ] ) ? '%' : '' ) : '';

			// Regular
			$inner_styles = array();
			if ( $styles[ 'border_css' ] ) { $inner_styles[] = $styles[ 'border_css' ]; }
			if ( $temp_style = MPC_CSS::background( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::font( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-wc-add_to_cart {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && $disabled_tooltip ) {
				$inner_styles[] = $styles[ 'margin_css' ];
				$style .= $css_selector . ' {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			if ( $styles[ 'margin_css' ] && ! $disabled_tooltip ) {
				$style .= '.mpc-tooltip-wrap[data-id="' . $css_id . '"] {';
					$style .= $styles[ 'margin_css' ];
				$style .= '}';
			}

			$inner_styles = array();
			if ( $styles[ 'icon_gap' ] && $styles[ 'icon_position' ] == 'left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'icon_gap' ] . ' !important;'; }
			if ( $styles[ 'icon_gap' ] && $styles[ 'icon_position' ] == 'right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'icon_gap' ] . ' !important;'; }
			if ( $temp_style = MPC_CSS::icon( $styles ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $styles[ 'hover_title_type' ] == 'none' ? $css_selector . ' .mpc-atc__title-hover .mpc-atc__icon,' : '';
				$style .= $css_selector . ' .mpc-atc__title .mpc-atc__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'padding_css' ] ) {
				$style .= $css_selector . ' .mpc-atc__title,';
				$style .= $css_selector . ' .mpc-atc__title-hover {';
					$style .= $styles[ 'padding_css' ];
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::background( $styles, 'hover' ) ) {
				$style .= $css_selector . ' .mpc-atc__background {';
					$style .= $temp_style;
				$style .= '}';
			}

			// Hover
			$inner_styles = array();
			if ( $styles[ 'hover_icon_gap' ] && $styles[ 'hover_icon_position' ] == 'left' ) { $inner_styles[] = 'padding-right:' . $styles[ 'hover_icon_gap' ] . ' !important;'; }
			if ( $styles[ 'hover_icon_gap' ] && $styles[ 'hover_icon_position' ] == 'right' ) { $inner_styles[] = 'padding-left:' . $styles[ 'hover_icon_gap' ] . ' !important;'; }
			if ( $temp_style = MPC_CSS::icon( $styles, 'hover' ) ) { $inner_styles[] = $temp_style; }
			if ( $styles[ 'hover_title_type' ] != 'none' && count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc-atc__title-hover .mpc-atc__icon {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'hover_border_css' ] ) {
				$style .= $css_selector . ':hover .mpc-wc-add_to_cart {';
					$style .= $styles[ 'hover_border_css' ];
				$style .= '}';
			}

			if ( $temp_style = MPC_CSS::font( $styles, 'hover' ) ) {
				$style .= $css_selector . ' .mpc-atc__title-hover {';
					$style .= $temp_style;
				$style .= '}';
			}
			if ( $styles[ 'hover_title_type' ] == 'none' && $styles[ 'hover_none_icon_color' ] ) {
				$style .= $css_selector . ' .mpc-atc__title-hover .mpc-atc__icon {';
					$style .= 'color:' . $styles[ 'hover_none_icon_color' ] . ';';
				$style .= '}';
			}

			if ( $styles[ 'hover_background_offset' ] ) {
				$temp_style = '';

				if ( $styles[ 'hover_background_effect' ] == 'expand-horizontal' ) {
					$temp_style = 'left:' . $styles[ 'hover_background_offset' ] . '  !important;right:' . $styles[ 'hover_background_offset' ] . '  !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-vertical' ) {
					$temp_style = 'top:' . $styles[ 'hover_background_offset' ] . '  !important;bottom:' . $styles[ 'hover_background_offset' ] . '  !important;';
				} elseif ( $styles[ 'hover_background_effect' ] == 'expand-diagonal_left' || $styles[ 'hover_background_effect' ] == 'expand-diagonal_right' ) {
					$temp_style = 'top:-' . $styles[ 'hover_background_offset' ] . '  !important;bottom:-' . $styles[ 'hover_background_offset' ] . '  !important;';
				}

				if ( $temp_style ) {
					$style .= $css_selector . ':hover .mpc-atc__background {';
						$style .= $temp_style;
					$style .= '}';
				}
			}

			/* Notices */
			$inner_styles = array();
			if ( $temp_style = MPC_CSS::icon( $styles, 'loader' ) ) { $inner_styles[] = $temp_style; }
			if ( $this->atts[ 'loader_position' ] == 'inside' && $temp_style = MPC_CSS::background( $styles, 'loader' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ( $this->atts[ 'loader_position' ] == 'inside' ? ' .mpc--loader' : ' .mpc-wc-add_to_cart + .mpc--loader' ) . ' {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $temp_style = MPC_CSS::icon( $styles, 'success' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles, 'success' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc--success {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			$inner_styles = array();
			if ( $temp_style = MPC_CSS::icon( $styles, 'error' ) ) { $inner_styles[] = $temp_style; }
			if ( $temp_style = MPC_CSS::background( $styles, 'error' ) ) { $inner_styles[] = $temp_style; }
			if ( count( $inner_styles ) > 0 ) {
				$style .= $css_selector . ' .mpc--error {';
					$style .= join( '', $inner_styles );
				$style .= '}';
			}

			if ( $styles[ 'loader_border' ] && $this->atts[ 'loader_position' ] == 'inside' ) {
				$style .= $css_selector . ' [data-notice$="loader"] {';
					$style .= 'border-color:' . $styles[ 'loader_border' ] . ';';
				$style .= '}';
			}
			if ( $styles[ 'loader_border' ] && $this->atts[ 'loader_position' ] == 'outside' ) {
				$style .= $css_selector . ' [data-notice$="loader"] + .mpc--loader {';
					$style .= $styles[ 'border_css' ] ? $styles[ 'border_css' ] : '';
					$style .= 'border-color:' . $styles[ 'loader_border' ] . ';';
					$style .= 'margin: 0 ' . $styles[ 'loader_gap' ] . ';';
				$style .= '}';
			}
			if ( $styles[ 'success_border' ] ) {
				$style .= $css_selector . ' [data-notice$="success"] {';
					$style .= 'border-color:' . $styles[ 'success_border' ] . ';';
				$style .= '}';
			}
			if ( $styles[ 'error_border' ] ) {
				$style .= $css_selector . ' [data-notice$="error"] {';
					$style .= 'border-color:' . $styles[ 'error_border' ] . ';';
				$style .= '}';
			}

			$mpc_massive_styles .= $style;
			$this->style = $css_id;

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
					'tooltip'     => __( 'Presets are used to easily configure your shortcode. You can choose one of the premade presets or create your own. After choosing a preset click the load button to use it.', 'mpc' ),
					'value'       => '',
					'shortcode'   => $this->shortcode,
					'description' => __( 'Choose preset or create new one.', 'mpc' ),
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Product', 'mpc' ),
					'param_name'  => 'item_id',
					'settings'    => array(
						'multiple'      => false,
						'sortable'      => false,
						'unique_values' => true,
					),
					'std'         => '',
					'description' => __( 'Select product to display.', 'mpc' ),
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Full Width', 'mpc' ),
					'param_name'       => 'block',
					'tooltip'          => __( 'Display button at 100% width of its container.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Display as full width.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Auto Size', 'mpc' ),
					'param_name'       => 'auto_size',
					'tooltip'          => __( 'Enable to adjust the button size if hover content is bigger than button.', 'mpc' ),
					'value'            => array( __( 'Enable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Auto adjust the button size.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency'       => array( 'element' => 'block', 'value_not_equal_to' => 'true' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Title Type', 'mpc' ),
					'param_name'       => 'title_type',
					'tooltip'          => __( 'Select which title type you want to use. Please notice that you can disable Icon in each of them by not selecting it.', 'mpc' ),
					'value'            => array(
						__( 'Only Icon', 'mpc' )            => 'icon',
						__( 'Icon + Product Price', 'mpc' ) => 'price',
						__( 'Icon + Product Title', 'mpc' ) => 'title',
						__( 'Icon + Custom Title', 'mpc' )  => 'custom',
					),
					'std'              => 'price',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
			);

			$text = array(
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Title', 'mpc' ),
					'param_name'       => 'title',
					'tooltip'          => __( 'Text displayed on the button.', 'mpc' ),
					'value'            => '',
//					'description'      => __( 'Specify text for this button.', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'title_type', 'value' => 'custom' ),
				),
			);

			$icon_settings = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Position', 'mpc' ),
					'param_name'       => 'icon_position',
					'value'            => array(
						__( 'Left', 'mpc' )  => 'left',
						__( 'Right', 'mpc' ) => 'right',
					),
					'std'              => 'left',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'title_type', 'value_not_equal_to' => array( 'icon' ) ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Custom Icon Gap', 'mpc' ),
					'param_name'       => 'icon_gap',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-validate-int',
					'dependency'       => array( 'element' => 'title_type', 'value_not_equal_to' => array( 'icon' ) ),
				),
			);

			$hover = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Hover', 'mpc' ),
					'subtitle'   => __( 'Setup button hover.', 'mpc' ),
					'param_name' => 'hover_divider',
					'group'      => __( 'Hover', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Title Type', 'mpc' ),
					'param_name'       => 'hover_title_type',
					'tooltip'          => __( 'Select which title type you want to use. Please notice that you can disable Icon in each of them by not selecting it.', 'mpc' ),
					'value'            => array(
						__( 'None (Use the regular state content)', 'mpc' ) => 'none',
						__( 'Only Icon', 'mpc' )                            => 'icon',
						__( 'Icon + Product Price', 'mpc' )                 => 'price',
						__( 'Icon + Product Title', 'mpc' )                 => 'title',
						__( 'Icon + Custom Title', 'mpc' )                  => 'custom',
					),
					'std'              => 'none',
					'group'            => __( 'Hover', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Icon Color', 'mpc' ),
					'param_name'       => 'hover_none_icon_color',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Hover', 'mpc' ),
					'dependency'       => array( 'element' => 'hover_title_type', 'value_not_equal_to' => array( 'icon', 'price', 'title', 'custom' ) ),
//					'dependency'       => array( 'element' => 'hover_title_type', 'value' => 'none' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Hover Title', 'mpc' ),
					'param_name'       => 'hover_title',
					'tooltip'          => __( 'Text displayed on the button in hover state.', 'mpc' ),
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Hover', 'mpc' ),
					'dependency'       => array( 'element' => 'hover_title_type', 'value' => 'custom' ),
				),

				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'hover_effect',
					'tooltip'          => __( 'Choose hover state animation style.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )                   => 'fade-in',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
						__( 'Push Out - from Top', 'mpc' )    => 'push_out-top',
						__( 'Push Out - from Right', 'mpc' )  => 'push_out-right',
						__( 'Push Out - from Bottom', 'mpc' ) => 'push_out-bottom',
						__( 'Push Out - from Left', 'mpc' )   => 'push_out-left',
					),
					'std'           => 'fade-in',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'         => __( 'Hover', 'mpc' ),
					'dependency'    => array( 'element' => 'hover_title_type', 'value_not_equal_to' => 'none' ),
				),
			);

			$hover_icon_settings = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Icon Position', 'mpc' ),
					'param_name'       => 'hover_icon_position',
					'value'            => array(
						__( 'Left', 'mpc' )  => 'left',
						__( 'Right', 'mpc' ) => 'right',
					),
					'std'              => 'left',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency'       => array( 'element' => 'hover_title_type', 'value_not_equal_to' => array( 'none', 'icon' ) ),
					'group'            => __( 'Hover', 'mpc' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Custom Icon Gap', 'mpc' ),
					'param_name'       => 'hover_icon_gap',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-validate-int',
					'dependency'       => array( 'element' => 'hover_title_type', 'value_not_equal_to' => array( 'none', 'icon' ) ),
					'group'            => __( 'Hover', 'mpc' ),
				),
			);

			$background_effect = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Hover Effect', 'mpc' ),
					'param_name'       => 'hover_background_effect',
					'tooltip'          => __( 'Choose background hover animation style.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )                    => 'fade-in',
						__( 'Slide In - from Top', 'mpc' )     => 'slide-top',
						__( 'Slide In - from Right', 'mpc' )   => 'slide-right',
						__( 'Slide In - from Bottom', 'mpc' )  => 'slide-bottom',
						__( 'Slide In - from Left', 'mpc' )    => 'slide-left',
						__( 'Expand - Horizontal', 'mpc' )     => 'expand-horizontal',
						__( 'Expand - Vertical', 'mpc' )       => 'expand-vertical',
						__( 'Expand - Diagonal Left', 'mpc' )  => 'expand-diagonal_left',
						__( 'Expand - Diagonal Right', 'mpc' ) => 'expand-diagonal_right',
					),
					'std'              => 'fade-in',
					'group'            => __( 'Hover', 'mpc' ),
				),
				array(
					'type'             => 'textfield',
					'heading'          => __( 'Custom Offset', 'mpc' ),
					'param_name'       => 'hover_background_offset',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-validate-int',
					'group'            => __( 'Hover', 'mpc' ),
					'dependency'       => array( 'element' => 'hover_background_effect', 'value' => array( 'expand-horizontal', 'expand-vertical', 'expand-diagonal_left', 'expand-diagonal_right' ) ),
				),
			);

			$notices = array(
				array(
					'type'       => 'mpc_divider',
					'title'      => __( 'Notices', 'mpc' ),
					'subtitle'   => __( 'Setup notices for Add To Cart functionality.', 'mpc' ),
					'param_name' => 'notice_divider',
					'group'      => __( 'Notices', 'mpc' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Appear Effect', 'mpc' ),
					'param_name'       => 'notices_effect',
					'tooltip'          => __( 'Choose notices animation style.', 'mpc' ),
					'value'            => array(
						__( 'Fade', 'mpc' )                  => 'fade-in',
						__( 'Slide In - from Left', 'mpc' )   => 'slide-left',
						__( 'Slide In - from Right', 'mpc' )  => 'slide-right',
					),
					'std'           => 'fade-in',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'         => __( 'Notices', 'mpc' ),
				),
			);
			$icon_loader_spin = array(
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Loader Position', 'mpc' ),
					'param_name'       => 'loader_position',
					'description'      => __( 'Select loader notice position.', 'mpc' ),
					'value'            => array(
						__( 'Inside', 'mpc' )  => 'inside',
						__( 'Outside', 'mpc' ) => 'outside',
					),
					'std'              => 'inside',
					'group'            => __( 'Notices', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
						'type'             => 'dropdown',
						'heading'          => __( 'Spinner Effect', 'mpc' ),
						'param_name'       => 'loader_icon_spin',
						'description'      => __( 'Select spin effect for loader icon.', 'mpc' ),
						'value'            => array(
								__( 'None', 'mpc' )                   => 'none',
								__( 'Rotate 2D', 'mpc' )              => 'rotate',
								__( 'Rotate 3D - horizontal', 'mpc' ) => 'rotate3d-horizontal',
								__( 'Rotate 3D - vertical', 'mpc' )   => 'rotate3d-vertical',
						),
						'std'              => 'rotate',
						'group'            => __( 'Notices', 'mpc' ),
						'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => __( 'Loader Alignment', 'mpc' ),
					'param_name'       => 'loader_align',
					'description'      => __( 'Select loader notice alignemnt.', 'mpc' ),
					'value'            => array(
						__( 'Left', 'mpc' )  => 'left',
						__( 'Right', 'mpc' ) => 'right',
					),
					'std'              => 'left',
					'group'            => __( 'Notices', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'loader_position', 'value' => array( 'outside' ) ),
				),
				array(
					'type'             => 'mpc_text',
					'heading'          => __( 'Custom Gap', 'mpc' ),
					'param_name'       => 'loader_gap',
					'tooltip'          => __( 'Define gap between loader icon and Add To Cart button.', 'mpc' ),
					'value'            => '',
					'addon'            => array(
						'icon'  => 'dashicons dashicons-leftright',
						'align' => 'prepend',
					),
					'label'            => 'px',
					'validate'         => true,
					'group'            => __( 'Notices', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column mpc-advanced-field',
					'dependency'       => array( 'element' => 'loader_position', 'value' => array( 'outside' ) ),
				),
			);

			$border_loader = array( array(
				'type'             => 'colorpicker',
				'heading'          => __( 'Border Color', 'mpc' ),
				'param_name'       => 'loader_border',
				'value'            => '',
				'edit_field_class' => 'vc_col-sm-6 vc_column',
				'group'            => __( 'Notices', 'mpc' ),
				'dependency'       => array( 'element' => 'loader_position', 'value' => 'inside' ),
			) );
			$border_success = array( array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'success_border',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Notices', 'mpc' ),
			) );
			$border_error = array( array(
					'type'             => 'colorpicker',
					'heading'          => __( 'Border Color', 'mpc' ),
					'param_name'       => 'error_border',
					'value'            => '',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'group'            => __( 'Notices', 'mpc' ),
			) );

			$icon_loader = MPC_Snippets::vc_icon( array( 'prefix' => 'loader', 'subtitle' => __( 'Loading', 'mpc' ), 'tooltip' => __( 'To create icon use the section bellow to configure it. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ) ) );
			$bg_loader   = MPC_Snippets::vc_background( array( 'prefix' => 'loader', 'subtitle' => __( 'Loading', 'mpc' ), 'tooltip' => __( 'Setup buttons background after hover. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ), 'dependency' => array( 'element' => 'loader_position', 'value' => 'inside' ), ) );
			$icon_success = MPC_Snippets::vc_icon( array( 'prefix' => 'success', 'subtitle' => __( 'Success', 'mpc' ), 'tooltip' => __( 'To create icon use the section bellow to configure it. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ) ) );
			$bg_success   = MPC_Snippets::vc_background( array( 'prefix' => 'success', 'subtitle' => __( 'Success', 'mpc' ), 'tooltip' => __( 'Setup buttons background after hover. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ) ) );
			$icon_error = MPC_Snippets::vc_icon( array( 'prefix' => 'error', 'subtitle' => __( 'Error', 'mpc' ), 'tooltip' => __( 'To create icon use the section bellow to configure it. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ) ) );
			$bg_error   = MPC_Snippets::vc_background( array( 'prefix' => 'error', 'subtitle' => __( 'Error', 'mpc' ), 'tooltip' => __( 'Setup buttons background after hover. You can leave it unchanged to use default.', 'mpc' ), 'group' => __( 'Notices', 'mpc' ) ) );

			$integrate_tooltip = vc_map_integrate_shortcode( 'mpc_tooltip', 'mpc_tooltip__', __( 'Tooltip', 'mpc' ) );
			$disable_tooltip   = array(
				array(
					'type'             => 'checkbox',
					'heading'          => __( 'Disable Tooltip', 'mpc' ),
					'param_name'       => 'mpc_tooltip__disable',
					'value'            => array( __( 'Disable', 'mpc' ) => 'true' ),
					'std'              => '',
					'description'      => __( 'Switch to disable tooltip display.', 'mpc' ),
					'group'            => __( 'Tooltip', 'mpc' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column mpc-section-disabler',
				),
			);
			$integrate_tooltip = array_merge( $disable_tooltip, $integrate_tooltip );

			$font       = MPC_Snippets::vc_font( array( 'subtitle' => __( 'Content', 'mpc' ) ) );
			$hover_font = MPC_Snippets::vc_font_simple( array( 'prefix' => 'hover', 'group' => __( 'Hover', 'mpc' ), 'subtitle' => __( 'Hover', 'mpc' ), 'tooltip' => __( 'Specify font settings for button hover state.', 'mpc' ), 'dependency' => array( 'element' => 'hover_title_type', 'value_not_equal_to' => 'icon' ) ) );
			$background = MPC_Snippets::vc_background();
			$border     = MPC_Snippets::vc_border();
			$padding    = MPC_Snippets::vc_padding();
			$margin     = MPC_Snippets::vc_margin();

			$icon = MPC_Snippets::vc_icon( array( 'tooltip' => __( 'To create a button with an icon use the section bellow to configure it.', 'mpc' ), ) );
			$hover_icon = MPC_Snippets::vc_icon( array( 'prefix' => 'hover', 'group' => __( 'Hover', 'mpc' ), 'subtitle' => __('Hover','mpc'), 'tooltip' => __( 'To create a button with an icon use the section bellow to configure it.', 'mpc' ), 'dependency' => array( 'element' => 'hover_title_type', 'value_not_equal_to' => 'none' ) ) );

			$hover_background = MPC_Snippets::vc_background( array( 'group' => __( 'Hover', 'mpc' ), 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ), 'tooltip' => __( 'Setup buttons background after hover. You can leave it unchanged.', 'mpc' ) ) );
			$hover_border     = MPC_Snippets::vc_border( array( 'group' => __( 'Hover', 'mpc' ), 'prefix' => 'hover', 'subtitle' => __( 'Hover', 'mpc' ), 'tooltip' => __( 'Setup buttons border after hover. You can leave it unchanged.', 'mpc' ) ) );

			$animation  = MPC_Snippets::vc_animation();

			$params = array_merge(
				$base,
				$font,
				$text,
				$icon,
				$icon_settings,
				$background,
				$border,
				$padding,
				$margin,

				$hover,
				$hover_font,
				$hover_icon,
				$hover_icon_settings,
				$hover_background,
				$background_effect,
				$hover_border,

				$notices,
				$icon_loader,
				$icon_loader_spin,
				$bg_loader,
				$border_loader,
				$icon_success,
				$bg_success,
				$border_success,
				$icon_error,
				$bg_error,
				$border_error,

				$integrate_tooltip,
				$animation
			);

			if( !class_exists( 'WooCommerce' ) ) {
				$params = array(
					array(
						'type'             => 'custom_markup',
						'param_name'       => 'woocommerce_notice',
						'value'            => '<p class="mpc-warning mpc-active"><i class="dashicons dashicons-warning"></i>' . __( 'Please install and activate <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugin in order to use this shortcode! :)', 'mpc' ) . '</p>',
						'edit_field_class' => 'vc_col-sm-12 vc_column mpc-woocommerce-field',
					),
				);
			}

			return array(
				'name'          => __( 'Add To Cart', 'mpc' ),
				'description'   => __( 'Sell your products anywhere', 'mpc' ),
				'base'          => 'mpc_wc_add_to_cart',
				'class'         => '',
				'icon'          => 'mpc-shicon-wc-add_to_cart',
				'category'      => __( 'Massive', 'mpc' ),
				'params'        => $params,
			);
		}
	}
}
if ( class_exists( 'MPC_WC_Add_To_Cart' ) ) {
	global $MPC_WC_Add_To_Cart;
	$MPC_WC_Add_To_Cart = new MPC_WC_Add_To_Cart;
}
