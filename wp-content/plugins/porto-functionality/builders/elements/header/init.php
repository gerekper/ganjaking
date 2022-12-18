<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header Builder
 */

if ( ! class_exists( 'PortoBuildersHeader' ) ) :
	class PortoBuildersHeader {

		private $display_wpb_elements = false;

		public static $elements = array(
			'logo',
			'menu',
			'switcher',
			'search-form',
			'mini-cart',
			'social',
			'menu-icon',
			'divider',
		);

		public static $woo_elements = array(
			'myaccount',
			'wishlist',
			'compare',
		);

		/**
		 * Global Instance Objects
		 *
		 * @var array $instances
		 * @since 2.4.0
		 * @access private
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				if ( is_admin() ) {
					add_action(
						'elementor/elements/categories_registered',
						function( $self ) {
							$self->add_category(
								'porto-hb',
								array(
									'title'  => __( 'Porto Header Builder', 'porto-functionality' ),
									'active' => false,
								)
							);
						}
					);
				}

				add_action( 'elementor/widgets/register', array( $this, 'add_elementor_elements' ), 10, 1 );
			}

			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'vc_after_init', array( $this, 'load_wpb_map_elements' ) );
			}
			if ( is_admin() ) {
				add_action( 'save_post', array( $this, 'add_internal_dynamic_css' ), 100, 2 );
			}

			if ( defined( 'WPB_VC_VERSION' ) || defined( 'VCV_VERSION' ) ) {
				add_action(
					'template_redirect',
					function() {
						$should_add_shortcodes = false;
						if ( is_singular( PortoBuilders::BUILDER_SLUG ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
							$should_add_shortcodes = true;
						} elseif ( function_exists( 'porto_check_builder_condition' ) ) {
							global $porto_settings;
							$builder_id = porto_check_builder_condition( 'header' );
							if ( isset( $porto_settings['header-type-select'] ) && 'header_builder_p' == $porto_settings['header-type-select'] && $builder_id ) {
								$should_add_shortcodes = true;
							}
						}

						if ( $should_add_shortcodes ) {
							$this->add_shortcodes();
						} else {
							$this->add_shortcodes( array( 'social' ) );
						}
					}
				);

				add_action(
					'admin_init',
					function() {
						$should_add_shortcodes = false;
						if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vc_save' == $_REQUEST['action'] ) {
							$should_add_shortcodes = true;
						} elseif ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_POST['post_type'] ) {
							$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							$this->add_shortcodes();
						}
					}
				);
			}

			$this->add_gutenberg_elements();
		}

		public function add_elementor_elements( $self ) {
			$load_widgets = false;
			if ( is_admin() ) {
				$load_widgets = true;
			} else {
				global $porto_settings;
				if ( isset( $porto_settings['header-type-select'] ) && 'header_builder_p' == $porto_settings['header-type-select'] ) {
					$load_widgets = true;
				}
			}
			if ( $load_widgets ) {
				foreach ( $this::$elements as $element ) {
					include_once PORTO_BUILDERS_PATH . '/elements/header/elementor/' . $element . '.php';
					$class_name = 'Porto_Elementor_HB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
				if ( class_exists( 'Woocommerce' ) ) {
					foreach ( $this::$woo_elements as $element ) {
						include_once PORTO_BUILDERS_PATH . '/elements/header/elementor/' . $element . '.php';
						$class_name = 'Porto_Elementor_HB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
						if ( class_exists( $class_name ) ) {
							$self->register( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
						}
					}
				}
			}
		}

		private function add_shortcodes( $global_shortcodes = array() ) {
			$shortcodes = $this::$elements;
			if ( class_exists( 'Woocommerce' ) ) {
				$shortcodes = array_merge( $shortcodes, $this::$woo_elements );
			}
			foreach ( $shortcodes as $tag ) {
				if ( /*in_array( $tag, array( 'menu-icon', 'divider', 'myaccount' ) ) || */ ! function_exists( 'porto_header_elements' ) ) {
					continue;
				}
				if ( ! empty( $global_shortcodes ) && ! in_array( $tag, $global_shortcodes ) ) {
					continue;
				}
				$shortcode_name = str_replace( '-', '_', $tag );
				add_shortcode(
					'porto_hb_' . $shortcode_name,
					function( $atts, $content = null ) use ( $tag ) {
						ob_start();
						if ( ! $atts ) {
							$atts = array();
						}
						$el_class = isset( $atts['el_class'] ) ? trim( $atts['el_class'] ) : '';

						if ( in_array( $tag, array( 'menu', 'search-form', 'mini-cart', 'social', 'menu-icon', 'switcher' ) ) ) {
							if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
								ob_start();
								echo '<style>';
								include PORTO_BUILDERS_PATH . '/elements/header/wpb/style-' . $tag . '.php';
								echo '</style>';
								porto_filter_inline_css( ob_get_clean() );
							}
						}

						if ( 'menu' == $tag ) {
							$this->gutenberg_hb_menu( $atts, true );
						} elseif ( 'switcher' == $tag ) {
							if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
								$shortcode_name = 'porto_hb_switcher';
								// Shortcode class
								$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
									$atts,
									$shortcode_name,
									array(
										array(
											'param_name' => 'dropdown_font',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dropdown_item_padding',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dropdown_padding',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dropdown_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dropdown_hover_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dropdown_hover_bg',
											'selectors'  => true,
										),
									)
								);
								$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
							}
							if ( ! empty( $shortcode_class ) ) {
								$shortcode_class .= $el_class ? ' ' . $el_class : '';
							}
							if ( ! empty( $internal_css ) ) {
								// only wpbakery frontend editor
								echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
							}
							isset( $atts['type'] ) && porto_header_elements( array( (object) array( $atts['type'] => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class );
						} elseif ( 'search-form' == $tag ) {
							$this->gutenberg_hb_search_form( $atts, true );
						} elseif ( 'mini-cart' == $tag ) {
							$this->gutenberg_hb_mini_cart( $atts, true );
						} elseif ( 'menu-icon' == $tag ) {
							$this->gutenberg_hb_menu_icon( $atts, true );
						} elseif ( 'divider' == $tag ) {
							$this->gutenberg_hb_divider( $atts, true );
						} elseif ( 'myaccount' == $tag ) {
							$this->gutenberg_hb_myaccount( $atts, true );
						} elseif ( 'wishlist' == $tag ) {
							$this->gutenberg_hb_wishlist( $atts, true );
						} elseif ( 'compare' == $tag ) {
							$this->gutenberg_hb_compare( $atts, true );
						} elseif ( 'social' == $tag ) {
							$this->gutenberg_hb_social( $atts, true );
						} else {
							porto_header_elements( array( (object) array( $tag => '' ) ), $el_class );
						}
						return ob_get_clean();
					}
				);
			}
		}

		/**
		 * Add WPBakery Page Builder header elements
		 */
		public function load_wpb_map_elements() {
			if ( ! $this->display_wpb_elements ) {
				$this->display_wpb_elements = PortoBuilders::check_load_wpb_elements( 'header' );
			}
			if ( ! $this->display_wpb_elements ) {
				return;
			}

			$custom_class = porto_vc_custom_class();
			$right        = is_rtl() ? 'left' : 'right';
			vc_map(
				array(
					'name'        => __( 'Logo', 'porto-functionality' ),
					'description' => __( 'Show Site logo.', 'porto-functionality' ),
					'base'        => 'porto_hb_logo',
					'icon'        => 'far fa-circle',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_logo',
							'text'       => esc_html__( 'Please see \'Theme Options -> Logo\' panel.', 'porto-functionality' ),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Menu', 'porto-functionality' ),
					'description' => __( 'Show Navigation Menu.', 'porto-functionality' ),
					'base'        => 'porto_hb_menu',
					'icon'        => 'far fa-list-alt',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_menu',
							'text'       => esc_html__( 'Please see \'Theme Options -> Menu\'.', 'porto-functionality' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Location', 'porto-functionality' ),
							'param_name'  => 'location',
							'value'       => array(
								__( 'Select a Location', 'porto-functionality' ) => '',
								__( 'Main Menu', 'porto-functionality' )         => 'main-menu',
								__( 'Secondary Menu', 'porto-functionality' )    => 'secondary-menu',
								__( 'Main Toggle Menu', 'porto-functionality' )  => 'main-toggle-menu',
								__( 'Top Navigation', 'porto-functionality' )    => 'nav-top',
							),
							'admin_label' => true,
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Menu Title', 'porto-functionality' ),
							'param_name' => 'title',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Font Size', 'porto-functionality' ),
							'param_name' => 'font_size',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Font Weight', 'porto-functionality' ),
							'param_name' => 'font_weight',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Text Transform', 'porto-functionality' ),
							'param_name' => 'text_transform',
							'value'      => array(
								__( 'Default', 'porto-functionality' )    => '',
								__( 'None', 'porto-functionality' )       => 'none',
								__( 'Uppercase', 'porto-functionality' )  => 'uppercase',
								__( 'Capitalize', 'porto-functionality' ) => 'capitalize',
								__( 'Lowercase', 'porto-functionality' )  => 'lowercase',
							),
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Line Height', 'porto-functionality' ),
							'param_name' => 'line_height',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Letter Spacing', 'porto-functionality' ),
							'param_name' => 'letter_spacing',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Size', 'porto-functionality' ),
							'param_name'  => 'tg_icon_sz',
							'units'       => array( 'px', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}}#main-toggle-menu .toggle' => "font-size: {{VALUE}}{{UNIT}};vertical-align: middle;",
							),
							'dependency'  => array(
								'element' => 'location',
								'value'   => 'main-toggle-menu',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Between Spacing', 'porto-functionality' ),
							'description' => __( 'Controls the spacing.', 'porto-functionality' ),
							'param_name'  => 'between_spacing',
							'units'       => array( 'px', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}}#main-toggle-menu .menu-title .toggle' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'qa_selector' => '#main-toggle-menu .menu-title .toggle',
							'dependency'  => array(
								'element' => 'location',
								'value'   => 'main-toggle-menu',
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Left / Right Padding', 'porto-functionality' ),
							'param_name' => 'padding',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Top / Bottom Padding', 'porto-functionality' ),
							'param_name' => 'padding3',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}}#main-toggle-menu .menu-title' => 'padding-top: {{VALUE}}{{UNIT}};padding-bottom: {{VALUE}}{{UNIT}};',
							),
							'dependency' => array(
								'element' => 'location',
								'value'   => 'main-toggle-menu',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'param_name' => 'bgcolor',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_color',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
							'param_name' => 'hover_bgcolor',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Popup Width', 'porto-functionality' ),
							'param_name' => 'popup_width',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu' ),
							),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Show Narrow', 'porto-functionality' ),
							'param_name'  => 'show_narrow',
							'description' => __( 'Turn on to show the narrow.', 'porto-functionality' ),
							'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
							'selectors'   => array(
								'{{WRAPPER}} .menu-title:after' => 'content:"\\\e81c";' . "position:absolute;font-family:'porto';{$right}: 1.4rem;",
							),
							'dependency' => array(
								'element' => 'location',
								'value'   => 'main-toggle-menu',
							),
							'group'      => __( 'Toggle Narrow', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Position', 'porto-functionality' ),
							'param_name' => 'narrow_pos',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .menu-title:after' => "{$right}: {{VALUE}}{{UNIT}};",
							),
							'dependency' => array(
								'element' => 'show_narrow',
								'value'   => 'yes',
							),
							'group'      => __( 'Toggle Narrow', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Size', 'porto-functionality' ),
							'param_name' => 'narrow_sz',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .menu-title:after' => 'font-size: {{VALUE}}{{UNIT}};',
							),
							'dependency' => array(
								'element' => 'show_narrow',
								'value'   => 'yes',
							),
							'group'      => __( 'Toggle Narrow', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Top Level Typography', 'porto-functionality' ),
							'param_name' => 'top_level_font',
							'selectors'  => array(
								'#header {{WRAPPER}}.main-menu > li.menu-item > a, #header {{WRAPPER}} .menu-custom-block span, #header {{WRAPPER}} .menu-custom-block a, {{WRAPPER}} .sidebar-menu > li.menu-item > a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item > a',
							),
							'dependency' => array(
								'element'            => 'location',
								'value_not_equal_to' => 'nav-top',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'top_level_link_color',
							'heading'    => __( 'Link Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.main-menu > li.menu-item > a, {{WRAPPER}} .sidebar-menu > li.menu-item > a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item > a' => 'color: {{VALUE}};',
							),
							'dependency' => array(
								'element'            => 'location',
								'value_not_equal_to' => 'nav-top',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'top_level_link_bg_color',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links > li.menu-item > a, #header {{WRAPPER}}.main-menu > li.menu-item > a, {{WRAPPER}} .sidebar-menu > li.menu-item, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item > a' => 'background-color: {{VALUE}};',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'top_level_link_hover_color',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.main-menu > li.menu-item.active:hover > a, #header {{WRAPPER}}.main-menu > li.menu-item:hover > a, {{WRAPPER}} .sidebar-menu > li.menu-item:hover > a, {{WRAPPER}} .sidebar-menu > li.menu-item.active > a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item.active:hover > a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item:hover > a' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
							'dependency' => array(
								'element'            => 'location',
								'value_not_equal_to' => 'nav-top',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'top_level_link_hover_bg_color',
							'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links > li.menu-item:hover > a, #header {{WRAPPER}}.top-links > li.menu-item.has-sub:hover > a, #header {{WRAPPER}}.main-menu > li.menu-item.active:hover > a, #header {{WRAPPER}}.main-menu > li.menu-item:hover > a, {{WRAPPER}} .sidebar-menu > li.menu-item:hover, {{WRAPPER}} .sidebar-menu > li.menu-item.active, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item.active:hover > a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item:hover > a' => 'background-color: {{VALUE}};',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_dimension',
							'heading'    => __( 'Menu Item Padding', 'porto-functionality' ),
							'param_name' => 'top_level_padding',
							'responsive' => true,
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links > li.menu-item > a, #header {{WRAPPER}}.main-menu > li.menu-item > a, #header {{WRAPPER}} .menu-custom-block a, #header {{WRAPPER}} .menu-custom-block span, {{WRAPPER}} .sidebar-menu>li.menu-item>a, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item > a' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'group'       => __( 'Top Level Menu', 'porto-functionality' ),
							'qa_selector' => '.top-links > li:nth-child(2) > a, .main-menu > li:nth-child(2) > a',
						),
						array(
							'type'       => 'porto_dimension',
							'heading'    => __( 'Menu Item Spacing', 'porto-functionality' ),
							'param_name' => 'top_level_margin',
							'responsive' => true,
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links > li.menu-item, #header {{WRAPPER}}.main-menu > li.menu-item, #header {{WRAPPER}} .menu-custom-block, #header {{WRAPPER}}.porto-popup-menu .main-menu > li.menu-item' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'group'      => __( 'Top Level Menu', 'porto-functionality' ),
							'dependency' => array(
								'element'            => 'location',
								'value_not_equal_to' => 'main-toggle-menu',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Size', 'porto-functionality' ),
							'description' => __( 'Controls the size of menu icon.', 'porto-functionality' ),
							'param_name'  => 'top_level_icon_sz',
							'units'       => array( 'px', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} li.menu-item>a>[class*=" fa-"]' => 'width: {{VALUE}}{{UNIT}};',
								'{{WRAPPER}} li.menu-item>a>i' => 'font-size: {{VALUE}}{{UNIT}};',
							),
							'qa_selector' => 'li.menu-item>a>i',
							'group'       => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Spacing', 'porto-functionality' ),
							'description' => __( 'Controls the spacing between icon and label.', 'porto-functionality' ),
							'param_name'  => 'top_level_icon_spacing',
							'units'       => array( 'px', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} li.menu-item>a>.avatar, {{WRAPPER}} li.menu-item>a>i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'group'       => __( 'Top Level Menu', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Popup Typography', 'porto-functionality' ),
							'param_name' => 'submenu_font',
							'selectors'  => array(
								'#header {{WRAPPER}}.main-menu .popup a, {{WRAPPER}} .sidebar-menu .popup, {{WRAPPER}}.porto-popup-menu .sub-menu, #header {{WRAPPER}}.top-links .narrow li.menu-item>a',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'submenu_link_color',
							'heading'    => __( 'Link Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links .narrow li.menu-item > a, #header {{WRAPPER}}.main-menu .wide li.sub li.menu-item > a, #header {{WRAPPER}}.main-menu .narrow li.menu-item > a, {{WRAPPER}} .sidebar-menu .wide li.menu-item li.menu-item > a, {{WRAPPER}} .sidebar-menu .wide li.sub li.menu-item > a, {{WRAPPER}} .sidebar-menu .narrow li.menu-item > a, {{WRAPPER}}.porto-popup-menu .sub-menu a' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'submenu_link_bg_color',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links .narrow ul.sub-menu, #header {{WRAPPER}}.main-menu .wide .popup > .inner, #header {{WRAPPER}}.main-menu .narrow ul.sub-menu, {{WRAPPER}} .sidebar-menu .wide .popup > .inner, {{WRAPPER}} .sidebar-menu .narrow ul.sub-menu, {{WRAPPER}}.porto-popup-menu .sub-menu a' => 'background-color: {{VALUE}};',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'submenu_link_hover_color',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links .narrow li.menu-item:hover > a, #header {{WRAPPER}}.main-menu .wide li.menu-item li.menu-item>a:hover, #header {{WRAPPER}}.main-menu .narrow li.menu-item:hover > a, {{WRAPPER}}.porto-popup-menu .sub-menu a:hover, {{WRAPPER}} .sidebar-menu .narrow li.menu-item:hover > a, {{WRAPPER}} .sidebar-menu .wide li.menu-item li.menu-item > a:hover' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'submenu_link_hover_bg_color',
							'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.top-links .narrow li.menu-item:hover > a, #header {{WRAPPER}} .sidebar-menu .narrow .menu-item:hover > a, #header {{WRAPPER}}.main-menu .narrow li.menu-item:hover > a, #header {{WRAPPER}}.main-menu .wide li.menu-item li.menu-item>a:hover, {{WRAPPER}} .sidebar-menu .wide li.menu-item li.menu-item > a:hover, {{WRAPPER}}.porto-popup-menu .sub-menu a:hover' => 'background-color: {{VALUE}};',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Item Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of sub menu item.', 'porto-functionality' ),
							'param_name'  => 'submenu_item_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .narrow li.menu-item>a, {{WRAPPER}} .wide li.sub li.menu-item>a, {{WRAPPER}}.porto-popup-menu .sub-menu li.menu-item>a' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'qa_selector' => '.narrow li:first-child>a, .wide li.sub li:first-child>a, .porto-popup-menu .sub-menu li:first-child>a',
							'group'       => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'SubMenu Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of sub menu.', 'porto-functionality' ),
							'param_name'  => 'submenu_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .narrow ul.sub-menu, {{WRAPPER}} .wide .popup>.inner, {{WRAPPER}}.porto-popup-menu .sub-menu' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'qa_selector' => '.narrow ul.sub-menu, .wide .popup>.inner',
							'group'       => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'submenu_narrow_border_color',
							'heading'    => __( 'Item Border Color on Narrow Menu', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .narrow li.menu-item>a' => 'border-bottom-color: {{VALUE}};',
							),
							'qa_selector' => '.narrow li:nth-child(2)>a',
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_wide_subheading',
							'text'       => esc_html__( 'Sub Heading on Mega Menu', 'porto-functionality' ),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'mega_title_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.main-menu .wide li.sub > a, {{WRAPPER}} .sidebar-menu .wide li.sub > a' => 'color: {{VALUE}};',
								'#header {{WRAPPER}} .wide li.side-menu-sub-title > a' => 'color: {{VALUE}} !important;',
							),
							'qa_selector' => '.wide li.sub > a',
							'group'       => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'mega_title_font',
							'selectors'  => array(
								'#header {{WRAPPER}} .wide li.side-menu-sub-title > a, #header {{WRAPPER}}.main-menu .wide li.sub > a, {{WRAPPER}} .sidebar-menu .wide li.sub > a',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of sub heading.', 'porto-functionality' ),
							'param_name'  => 'mega_title_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .wide li.side-menu-sub-title > a, #header {{WRAPPER}}.main-menu .wide li.sub > a, {{WRAPPER}} .sidebar-menu .wide li.sub > a' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'group'      => __( 'Menu Popup', 'porto-functionality' ),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'View Switcher', 'porto-functionality' ),
					'description' => __( 'Language, Currency Switcher', 'porto-functionality' ),
					'base'        => 'porto_hb_switcher',
					'icon'        => 'fas fa-language',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_switcher',
							'text'       => esc_html__( 'Please see \'Theme Options -> Header -> Language, Currency Switcher\'.', 'porto-functionality' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Type', 'porto-functionality' ),
							'param_name'  => 'type',
							'value'       => array(
								__( 'Select...', 'porto-functionality' ) => '',
								__( 'Language Switcher', 'porto-functionality' ) => 'language-switcher',
								__( 'Currency Switcher', 'porto-functionality' ) => 'currency-switcher',
							),
							'admin_label' => true,
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Font Size', 'porto-functionality' ),
							'param_name' => 'font_size',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Font Weight', 'porto-functionality' ),
							'param_name' => 'font_weight',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Top Level Text Transform', 'porto-functionality' ),
							'param_name' => 'text_transform',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'None', 'porto-functionality' ) => 'none',
								__( 'Uppercase', 'porto-functionality' ) => 'uppercase',
								__( 'Capitalize', 'porto-functionality' ) => 'capitalize',
								__( 'Lowercase', 'porto-functionality' ) => 'lowercase',
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Line Height', 'porto-functionality' ),
							'param_name' => 'line_height',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Letter Spacing', 'porto-functionality' ),
							'param_name' => 'letter_spacing',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_color',
							'value'      => '',
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Dropdown Label Font', 'porto-functionality' ),
							'param_name' => 'dropdown_font',
							'selectors'  => array(
								'#header {{WRAPPER}} .narrow li.menu-item>a',
							),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Dropdown Label Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of dropdown label.', 'porto-functionality' ),
							'param_name'  => 'dropdown_item_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .narrow li.menu-item>a' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Dropdown Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of dropdown.', 'porto-functionality' ),
							'param_name'  => 'dropdown_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .narrow ul.sub-menu' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'dropdown_color',
							'heading'    => __( 'Dropdown Label Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .narrow li.menu-item > a' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'dropdown_hover_color',
							'heading'    => __( 'Dropdown Label Hover Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .narrow li.menu-item:hover > a' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'dropdown_hover_bg',
							'heading'    => __( 'Dropdown Label Hover Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .narrow li.menu-item:hover > a, #header {{WRAPPER}} .narrow li.menu-item > a.active' => 'background-color: {{VALUE}};',
							),
						),
						$custom_class,
					),
				)
			);

			$border_radius_selectors = array(
				'#header {{WRAPPER}} .searchform'        => 'border-radius: {{VALUE}};',
				'#header {{WRAPPER}}.search-popup .searchform-fields' => 'border-radius: {{VALUE}};',
				'#header {{WRAPPER}} .searchform input'  => 'border-radius: {{VALUE}} 0 0 {{VALUE}};',
				'#header {{WRAPPER}} .searchform button' => 'border-radius: 0 max( 0px, calc({{VALUE}} - 5px)) max( 0px, calc({{VALUE}} - 5px)) 0;',
			);
			if ( is_rtl() ) {
				$border_radius_selectors = array(
					'#header {{WRAPPER}} .searchform' => 'border-radius: {{VALUE}};',
					'#header {{WRAPPER}}.search-popup .searchform-fields' => 'border-radius: {{VALUE}};',
					'#header {{WRAPPER}} .searchform input' => 'border-radius: 0 {{VALUE}} {{VALUE}} 0;',
					'#header {{WRAPPER}} .searchform button' => 'border-radius: max( 0px, calc({{VALUE}} - 5px)) 0 0 max( 0px, calc({{VALUE}} - 5px));',
				);
			}

			vc_map(
				array(
					'name'        => __( 'Search Form', 'porto-functionality' ),
					'description' => __( 'Show Search Form.', 'porto-functionality' ),
					'base'        => 'porto_hb_search_form',
					'icon'        => 'fas fa-search',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_search',
							'text'       => esc_html__( 'Please see \'Theme Options -> Header -> Search Form\' panel.', 'porto-functionality' ),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Placeholder Text', 'porto-functionality' ),
							'param_name' => 'placeholder_text',
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Show Category filter', 'porto-functionality' ),
							'param_name'  => 'category_filter',
							'value'       => array(
								__( 'Theme Options', 'porto-functionality' ) => '',
								__( 'Yes', 'porto-functionality' ) => 'yes',
								__( 'No', 'porto-functionality' ) => 'no',
							),
							'std'         => '',
							'admin_label' => true,
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Show Sub Categories', 'porto-functionality' ),
							'description' => __( 'Show categories including subcategory.', 'porto-functionality' ),
							'param_name'  => 'sub_cats',
							'dependency'  => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Show Categories on Mobile', 'porto-functionality' ),
							'param_name'  => 'category_filter_mobile',
							'value'       => array(
								__( 'Theme Options', 'porto-functionality' ) => '',
								__( 'Yes', 'porto-functionality' ) => 'yes',
								__( 'No', 'porto-functionality' ) => 'no',
							),
							'std'         => '',
							'admin_label' => true,
							'dependency'  => array(
								'element' => 'category_filter',
								'value'   => array( 'yes' ),
							),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Popup Position', 'porto-functionality' ),
							'description' => __( 'This works for only "Popup 1" and "Popup 2" and "Form" search layout on mobile. You can change search layout using Porto -> Theme Options -> Header -> Search Form -> Search Layout.', 'porto-functionality' ),
							'param_name'  => 'popup_pos',
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Left', 'porto-functionality' ) => 'left',
								__( 'Center', 'porto-functionality' ) => 'center',
								__( 'Right', 'porto-functionality' ) => 'right',
							),
							'std'         => '',
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Toggle Icon Size', 'porto-functionality' ),
							'description' => __( 'Input units together. e.g: 16px', 'porto-functionality' ),
							'param_name'  => 'toggle_size',
							'selectors'   => array(
								'#header {{WRAPPER}} .search-toggle' => 'font-size: {{VALUE}};',
							),
							'group'       => __( 'Toggle Icon Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Toggle Icon Color', 'porto-functionality' ),
							'param_name' => 'toggle_color',
							'selectors'  => array(
								'#header {{WRAPPER}} .search-toggle' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Toggle Icon Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Toggle Icon Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_toggle_color',
							'selectors'  => array(
								'#header.sticky-header {{WRAPPER}} .search-toggle' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Toggle Icon Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_toggle_color',
							'selectors'  => array(
								'#header {{WRAPPER}} .search-toggle:hover' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Toggle Icon Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_hover_toggle_color',
							'selectors'  => array(
								'#header.sticky-header {{WRAPPER}} .search-toggle:hover' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Toggle Icon Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Search Form Max Width', 'porto-functionality' ),
							'param_name' => 'search_width',
							'units'      => array( 'px', 'em', '%' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .searchform' => 'max-width: {{VALUE}}{{UNIT}}; width: 100%;',
								'{{WRAPPER}}.searchform-popup' => 'width: 100%;',
								'#header {{WRAPPER}} input' => 'max-width: 100%;',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Height', 'porto-functionality' ),
							'description' => __( 'Controls the height of search form.', 'porto-functionality' ),
							'param_name'  => 'height',
							'selectors'   => array(
								'#header {{WRAPPER}} input, #header {{WRAPPER}} select, #header {{WRAPPER}} .selectric .label, #header {{WRAPPER}} .selectric, #header {{WRAPPER}} button' => 'height: {{VALUE}};line-height: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Border Width', 'porto-functionality' ),
							'description' => __( 'Controls the border width of search form.', 'porto-functionality' ),
							'param_name'  => 'border_width',
							'selectors'   => array(
								'#header {{WRAPPER}} .searchform' => 'border-width: {{VALUE}};',
								'#header {{WRAPPER}}.search-popup .searchform-fields' => 'border-width: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Border Color', 'porto-functionality' ),
							'description' => __( 'Controls the border color of search form.', 'porto-functionality' ),
							'param_name'  => 'border_color',
							'value'       => '',
							'selectors'   => array(
								'#header {{WRAPPER}} .searchform' => 'border-color: {{VALUE}};',
								'#header {{WRAPPER}}.search-popup .searchform-fields' => 'border-color: {{VALUE}};',
								'#header {{WRAPPER}}.searchform-popup .search-toggle:after' => 'border-bottom-color: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Border Radius', 'porto-functionality' ),
							'description' => __( 'Controls the border radius of search form.', 'porto-functionality' ),
							'param_name'  => 'border_radius',
							'selectors'   => $border_radius_selectors,
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'form_bg_color',
							'heading'     => __( 'Form Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the background color of search form.', 'porto-functionality' ),
							'selectors'   => array(
								'#header {{WRAPPER}} .searchform, .fixed-header #header.sticky-header {{WRAPPER}} .searchform' => 'background-color: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Input Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of Input.', 'porto-functionality' ),
							'param_name'  => 'input_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} .searchform input' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'input_placeholder_color',
							'heading'     => __( 'Input Box Placeholder Color', 'porto-functionality' ),
							'description' => __( 'Controls placeholder color of the input box.', 'porto-functionality' ),
							'selectors'   => array(
								'#header {{WRAPPER}} input::placeholder' => 'color: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Input Box Width', 'porto-functionality' ),
							'description' => __( 'Controls the width of input box in form.', 'porto-functionality' ),
							'param_name'  => 'input_size',
							'selectors'   => array(
								'#header {{WRAPPER}} .text, #header {{WRAPPER}} input, #header {{WRAPPER}} .searchform-cats input' => 'width: {{VALUE}};',
								'#header {{WRAPPER}} input' => 'max-width: {{VALUE}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_search_form_icon',
							'text'       => __( 'Search Icon', 'porto-functionality' ),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Search Icon Size', 'porto-functionality' ),
							'param_name' => 'form_icon_size',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'#header {{WRAPPER}} button' => 'font-size: {{VALUE}}{{UNIT}};',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'form_icon_color',
							'heading'    => __( 'Search Icon Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} button' => 'color: {{VALUE}};',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'form_icon_bg_color',
							'heading'    => __( 'Search Icon Background Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} button' => 'background-color: {{VALUE}};',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Search Icon Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of search icon.', 'porto-functionality' ),
							'param_name'  => 'form_icon_padding',
							'selectors'   => array(
								'#header {{WRAPPER}} button' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'group'       => __( 'Search Form Style', 'porto-functionality' ),
						),

						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Separator Color', 'porto-functionality' ),
							'param_name' => 'divider_color',
							'value'      => '',
							'selectors'  => array(
								'#header {{WRAPPER}} input, #header {{WRAPPER}} select, #header {{WRAPPER}} .selectric, #header {{WRAPPER}} .selectric-hover .selectric, #header {{WRAPPER}} .selectric-open .selectric, #header {{WRAPPER}} .autocomplete-suggestions, #header {{WRAPPER}} .selectric-items' => 'border-color: {{VALUE}};',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_search_category',
							'text'       => __( 'Category', 'porto-functionality' ),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Separator Width', 'porto-functionality' ),
							'param_name' => 'category_inner_width',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .selectric, #header {{WRAPPER}}.simple-popup input, #header {{WRAPPER}} select' => 'border-right-width: {{VALUE}}{{UNIT}};',
								'#header {{WRAPPER}} select, #header {{WRAPPER}} .selectric' => 'border-left-width: {{VALUE}}{{UNIT}};',
								'#header {{WRAPPER}}.simple-popup select, #header {{WRAPPER}}.simple-popup .selectric' => 'border-left-width: 0;',
							),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Category Width', 'porto-functionality' ),
							'param_name' => 'category_width',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .selectric-cat, #header {{WRAPPER}} select' => 'width: {{VALUE}}{{UNIT}};',
							),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_dimension',
							'heading'    => __( 'Category Padding', 'porto-functionality' ),
							'param_name' => 'category_padding',
							'selectors'  => array(
								'#header {{WRAPPER}} .selectric .label, #header {{WRAPPER}} select' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Category Typography', 'porto-functionality' ),
							'param_name' => 'category_font',
							'selectors'  => array(
								'{{WRAPPER}} .selectric-cat, #header {{WRAPPER}} select',
							),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'category_color',
							'heading'    => __( 'Category Color', 'porto-functionality' ),
							'selectors'  => array(
								'#header {{WRAPPER}} .selectric .label, #header {{WRAPPER}} select' => 'color: {{VALUE}};',
							),
							'dependency' => array(
								'element' => 'category_filter',
								'value'   => 'yes',
							),
							'group'      => __( 'Search Form Style', 'porto-functionality' ),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Mini Cart', 'porto-functionality' ),
					'description' => __( 'Displays the mini cart.', 'porto-functionality' ),
					'base'        => 'porto_hb_mini_cart',
					'icon'        => 'fas fa-shopping-cart',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Type', 'porto-functionality' ),
							'description' => __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> WooCommerce.', 'porto-functionality' ),
							'param_name'  => 'type',
							'value'       => array(
								__( 'None', 'porto-functionality' ) => 'none',
								__( 'Simple', 'porto-functionality' ) => 'simple',
								__( 'Arrow Alt', 'porto-functionality' ) => 'minicart-arrow-alt',
								__( 'Text', 'porto-functionality' ) => 'minicart-inline',
								__( 'Icon & Text', 'porto-functionality' ) => 'minicart-text',
							),
							'std'         => 'simple',
							'admin_label' => true,
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Content Type', 'porto-functionality' ),
							'param_name'  => 'content_type',
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Off Canvas', 'porto-functionality' ) => 'offcanvas',
							),
							'std'         => '',
							'admin_label' => true,
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Custom Icon Class', 'porto-functionality' ),
							'param_name' => 'icon_cl',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Size', 'porto-functionality' ),
							'param_name' => 'icon_size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Color', 'porto-functionality' ),
							'param_name' => 'icon_color',
							'selectors'   => array(
								'{{WRAPPER}}#mini-cart .minicart-icon, {{WRAPPER}}#mini-cart.minicart-arrow-alt .cart-head:after' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_icon_color',
							'selectors'   => array(
								'.sticky-header {{WRAPPER}}#mini-cart .minicart-icon, .sticky-header {{WRAPPER}}#mini-cart.minicart-arrow-alt .cart-head:after' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Icon Color', 'porto-functionality' ),
							'param_name' => 'hover_icon_color',
							'selectors'   => array(
								'{{WRAPPER}}#mini-cart:hover .minicart-icon, {{WRAPPER}}#mini-cart.minicart-arrow-alt:hover .cart-head:after' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Hover Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_hover_icon_color',
							'selectors'   => array(
								'.sticky-header {{WRAPPER}}#mini-cart:hover .minicart-icon, .sticky-header {{WRAPPER}}#mini-cart.minicart-arrow-alt:hover .cart-head:after' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'icon_item_color',
							'heading'     => __( 'Badge Color', 'porto-functionality' ),
							'description' => __( 'Controls the the color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'#mini-cart .cart-items' => 'color: {{VALUE}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'icon_item_bg_color',
							'heading'     => __( 'Badge Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the the background color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'#mini-cart .cart-items' => 'background-color: {{VALUE}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Size', 'porto-functionality' ),
							'param_name' => 'icon_item_size',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}}#mini-cart .cart-items' => 'font-size: {{VALUE}}{{UNIT}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Background Size', 'porto-functionality' ),
							'param_name' => 'icon_item_bg_size',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}}#mini-cart .cart-items' => '--porto-badge-size: {{VALUE}}{{UNIT}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Badge Right Position', 'porto-functionality' ),
							'param_name' => 'icon_item_right',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}}#mini-cart .cart-items' => ( is_rtl() ? 'left' : 'right' ) . ':{{VALUE}}{{UNIT}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Badge Top Position', 'porto-functionality' ),
							'param_name' => 'icon_item_top',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}}#mini-cart .cart-items' => 'top:{{VALUE}}{{UNIT}};',
							),
							'group'       => __( 'Cart Badge', 'porto-functionality' ),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Margin Left', 'porto-functionality' ),
							'param_name' => 'icon_ml',
							'value'      => '',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Margin Right', 'porto-functionality' ),
							'param_name' => 'icon_mr',
							'value'      => '',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Text Font Size', 'porto-functionality' ),
							'param_name' => 'text_font_size',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Text Font Weight', 'porto-functionality' ),
							'param_name' => 'text_font_weight',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								'200' => '200',
								'300' => '300',
								'400' => '400',
								'500' => '500',
								'600' => '600',
								'700' => '700',
								'800' => '800',
							),
							'std'        => '',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Text Transform', 'porto-functionality' ),
							'param_name' => 'text_transform',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'None', 'porto-functionality' ) => 'none',
								__( 'Uppercase', 'porto-functionality' ) => 'uppercase',
								__( 'Capitalize', 'porto-functionality' ) => 'capitalize',
								__( 'Lowercase', 'porto-functionality' ) => 'lowercase',
							),
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Text Line Height', 'porto-functionality' ),
							'param_name' => 'text_line_height',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Text Letter Spacing', 'porto-functionality' ),
							'param_name' => 'text_ls',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Text Color', 'porto-functionality' ),
							'param_name' => 'text_color',
							'value'      => '',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Price Font Size', 'porto-functionality' ),
							'param_name' => 'price_font_size',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Price Font Weight', 'porto-functionality' ),
							'param_name' => 'price_font_weight',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								'200' => '200',
								'300' => '300',
								'400' => '400',
								'500' => '500',
								'600' => '600',
								'700' => '700',
								'800' => '800',
							),
							'std'        => '',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Price Line Height', 'porto-functionality' ),
							'param_name' => 'price_line_height',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Price Letter Spacing', 'porto-functionality' ),
							'param_name' => 'price_ls',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Price Color', 'porto-functionality' ),
							'param_name' => 'price_color',
							'value'      => '',
							'dependency' => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Header Social Icons', 'porto-functionality' ),
					'description' => __( 'Displays the social icons.', 'porto-functionality' ),
					'base'        => 'porto_hb_social',
					'icon'        => 'fab fa-facebook-f',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_social_icons',
							'text'       => esc_html__( 'Please see \'Theme Options -> Header -> Social Links\' panel.', 'porto-functionality' ),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Size', 'porto-functionality' ),
							'param_name' => 'icon_size',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Background Size', 'porto-functionality' ),
							'param_name' => 'icon_border_spacing',
						),
						array(
							'type'       => 'porto_dimension',
							'heading'    => __( 'Icon Margin', 'porto-functionality' ),
							'param_name' => 'icon_spacing',
							'selectors'  => array(
								'#header {{WRAPPER}} a' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'icon_color',
							'value'      => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'param_name' => 'icon_hover_color',
							'value'      => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Background Color', 'porto-functionality' ),
							'param_name' => 'icon_color_bg',
							'value'      => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Hover Background Color', 'porto-functionality' ),
							'param_name' => 'icon_hover_color_bg',
							'value'      => '',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Icon Border Style', 'porto-functionality' ),
							'param_name' => 'icon_border_style',
							'value'      => array(
								__( 'None', 'porto-functionality' ) => '',
								__( 'Solid', 'porto-functionality' ) => 'solid',
								__( 'Dashed', 'porto-functionality' ) => 'dashed',
								__( 'Dotted', 'porto-functionality' ) => 'dotted',
								__( 'Double', 'porto-functionality' ) => 'double',
								__( 'Inset', 'porto-functionality' ) => 'inset',
								__( 'Outset', 'porto-functionality' ) => 'outset',
							),
							'std'        => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Border Color', 'porto-functionality' ),
							'param_name' => 'icon_color_border',
							'dependency' => array(
								'element' => 'icon_border_style',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Hover Border Color', 'porto-functionality' ),
							'param_name' => 'icon_hover_color_border',
							'dependency' => array(
								'element' => 'icon_border_style',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Border Width', 'porto-functionality' ),
							'param_name' => 'icon_border_size',
							'dependency' => array(
								'element' => 'icon_border_style',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Border Radius', 'porto-functionality' ),
							'param_name' => 'icon_border_radius',
						),

						array(
							'type'       => 'porto_boxshadow',
							'heading'    => __( 'Box Shadow', 'porto-functionality' ),
							'param_name' => 'box_shadow',
							'unit'       => 'px',
							'positions'  => array(
								__( 'Horizontal', 'porto-functionality' ) => '',
								__( 'Vertical', 'porto-functionality' ) => '',
								__( 'Blur', 'porto-functionality' )   => '',
								__( 'Spread', 'porto-functionality' ) => '',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Mobile Menu Icon', 'porto-functionality' ),
					'description' => __( 'Show Mobile Toggle Icon.', 'porto-functionality' ),
					'base'        => 'porto_hb_menu_icon',
					'icon'        => 'fas fa-bars',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_mobile_toggle',
							'text'       => esc_html__( 'Please see \'Theme Options -> Menu -> Mobile Menu\'.', 'porto-functionality' ),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Custom Icon Class', 'porto-functionality' ),
							'param_name' => 'icon_cl',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Size', 'porto-functionality' ),
							'param_name' => 'size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Background Color', 'porto-functionality' ),
							'param_name' => 'bg_color',
							'value'      => '',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Icon Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding value of mobile icon.', 'porto-functionality' ),
							'param_name'  => 'icon_padding',
							'selectors'   => array(
								'{{WRAPPER}}.mobile-toggle' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}} !important;',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Vertical Divider', 'porto-functionality' ),
					'description' => __( 'Vertical Separator Line.', 'porto-functionality' ),
					'base'        => 'porto_hb_divider',
					'icon'        => 'fas fa-grip-lines-vertical',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Width', 'porto-functionality' ),
							'param_name' => 'width',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Height', 'porto-functionality' ),
							'param_name' => 'height',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'My Account Icon', 'porto-functionality' ),
					'description' => __( 'Show Account Icon.', 'porto-functionality' ),
					'base'        => 'porto_hb_myaccount',
					'icon'        => 'far fa-user',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Custom Icon Class', 'porto-functionality' ),
							'param_name' => 'icon_cl',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Font Size', 'porto-functionality' ),
							'param_name' => 'size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'color',
							'selectors'   => array(
								'#header {{WRAPPER}}' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_color',
							'selectors'   => array(
								'#header.sticky-header {{WRAPPER}}' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_color',
							'selectors'   => array(
								'#header {{WRAPPER}}:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_hover_color',
							'selectors'   => array(
								'#header.sticky-header {{WRAPPER}}:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Show Account Dropdown', 'porto-functionality' ),
							'description' => __( 'When user is logged in, Menu that is located in Account Menu will be shown.', 'porto-functionality' ),
							'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
							'param_name'  => 'account_dropdown',
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Between Spacing', 'porto-functionality' ),
							'param_name' => 'spacing',
							'units'      => array( 'px', 'em' ),
							'selectors'  => array(
								'#header {{WRAPPER}}.account-dropdown > li.menu-item > a > i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
								'{{WRAPPER}}.account-dropdown > li.has-sub > a::after' => 'font-size: 12px;vertical-align: middle;',
							),
							'dependency' => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Account Dropdown Font', 'porto-functionality' ),
							'param_name' => 'account_menu_font',
							'selectors'  => array(
								'{{WRAPPER}} .sub-menu li.menu-item > a',
							),
							'dependency' => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'account_dropdown_bgc',
							'heading'     => __( 'Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the background color for account dropdown.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}}.account-dropdown .narrow ul.sub-menu' => 'background-color: {{VALUE}};',
							),
							'dependency'  => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'account_dropdown_hbgc',
							'heading'     => __( 'Hover Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the background color for account dropdown item on hover.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}}.account-dropdown .sub-menu li.menu-item:hover > a, {{WRAPPER}}.account-dropdown .sub-menu li.menu-item.active > a, {{WRAPPER}}.account-dropdown .sub-menu li.menu-item.is-active > a' => 'background-color: {{VALUE}};',
							),
							'dependency'  => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'account_dropdown_lc',
							'heading'     => __( 'Link Color', 'porto-functionality' ),
							'description' => __( 'Controls the link color for account dropdown.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .sub-menu li.menu-item:before, {{WRAPPER}} .sub-menu li.menu-item > a' => 'color: {{VALUE}};',
							),
							'dependency'  => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'account_dropdown_hlc',
							'heading'     => __( 'Link Hover Color', 'porto-functionality' ),
							'description' => __( 'Controls the link hover color for account dropdown.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}}.account-dropdown .sub-menu li.menu-item:hover > a, {{WRAPPER}}.account-dropdown .sub-menu li.menu-item.active > a, {{WRAPPER}}.account-dropdown .sub-menu li.menu-item.is-active > a' => 'color: {{VALUE}};',
							),
							'dependency'  => array(
								'element' => 'account_dropdown',
								'value'   => 'yes',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Wishlist Icon', 'porto-functionality' ),
					'description' => __( 'Show YITH Wishlist Icon.', 'porto-functionality' ),
					'base'        => 'porto_hb_wishlist',
					'icon'        => 'far fa-heart',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Custom Icon Class', 'porto-functionality' ),
							'param_name' => 'icon_cl',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Font Size', 'porto-functionality' ),
							'param_name' => 'size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'color',
							'selectors'   => array(
								'#header {{WRAPPER}}' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_color',
							'selectors'   => array(
								'#header.sticky-header {{WRAPPER}}' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_color',
							'selectors'   => array(
								'#header {{WRAPPER}}:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Hover Color In Sticky', 'porto-functionality' ),
							'param_name' => 'sticky_hover_color',
							'selectors'   => array(
								'#header.sticky-header {{WRAPPER}}:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'badge_color',
							'heading'     => __( 'Badge Color', 'porto-functionality' ),
							'description' => __( 'Controls the the color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .wishlist-count' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'badge_bg_color',
							'heading'     => __( 'Badge Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the the background color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .wishlist-count' => 'background-color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Off Canvas ?', 'porto-functionality' ),
							'description' => __( 'Controls to show the wishlist dropdown as off canvas.', 'porto-functionality' ),
							'param_name'  => 'offcanvas',
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Compare Icon', 'porto-functionality' ),
					'description' => __( 'Show YITH Compare Icon.', 'porto-functionality' ),
					'base'        => 'porto_hb_compare',
					'icon'        => 'porto-icon-compare-link',
					'category'    => __( 'Header Builder', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Custom Icon Class', 'porto-functionality' ),
							'param_name' => 'icon_cl',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Font Size', 'porto-functionality' ),
							'param_name' => 'size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'badge_color',
							'heading'     => __( 'Badge Color', 'porto-functionality' ),
							'description' => __( 'Controls the the color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .compare-count' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'badge_bg_color',
							'heading'     => __( 'Badge Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the the background color of badge.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .compare-count' => 'background-color: {{VALUE}};',
							),
						),
						$custom_class,
					),
				)
			);
		}

		/**
		 * Save shortcode css to post meta in gutenberg editor
		 *
		 * @since 6.1.0
		 */
		public function add_internal_dynamic_css( $post_id, $post ) {
			
			if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'header' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			if ( defined( 'WPB_VC_VERSION' ) && false !== strpos( $post->post_content, '[porto_hb_' ) ) {
				ob_start();
				$css = '';
				preg_match_all( '/' . get_shortcode_regex( array( 'porto_hb_menu', 'porto_hb_search_form', 'porto_hb_mini_cart', 'porto_hb_social', 'porto_hb_menu_icon', 'porto_hb_switcher' ) ) . '/', $post->post_content, $shortcodes );
				foreach ( $shortcodes[2] as $index => $tag ) {
					$atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
					include PORTO_BUILDERS_PATH . '/elements/header/wpb/style-' . str_replace( array( 'porto_hb_', '_' ), array( '', '-' ), $tag ) . '.php';
				}
				$css = ob_get_clean();
				if ( $css ) {
					$result = update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
				} else {
					delete_post_meta( $post_id, 'porto_builder_css' );
				}
			} elseif ( false !== strpos( $post->post_content, '<!-- wp:porto-hb' ) ) { // Gutenberg editor
				
				$blocks = parse_blocks( $post->post_content );
				if ( ! empty( $blocks ) ) {
					ob_start();
					$css = '';
					$this->include_style( $blocks );
					$css = ob_get_clean();
					if ( $css ) {
						update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
					} else {
						delete_post_meta( $post_id, 'porto_builder_css' );
					}
				}
			}
		}

		public function include_style( $blocks ) {
			if ( empty( $blocks ) ) {
				return;
			}
			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-hb/porto-menu', 'porto-hb/porto-switcher', 'porto-hb/porto-search-form', 'porto-hb/porto-mini-cart', 'porto-hb/porto-social', 'porto-hb/porto-menu-icon' ) ) ) {
					$atts                 = empty( $block['attrs'] ) ? array() : $block['attrs'];
					$atts['page_builder'] = 'gutenberg';
					include PORTO_BUILDERS_PATH . '/elements/header/wpb/style-' . str_replace( 'porto-hb/porto-', '', $block['blockName'] ) . '.php';
				}
				if ( ! empty( $block['innerBlocks'] ) ) {
					$this->include_style( $block['innerBlocks'] );
				}
			}
		}

		/**
		 * Load gutenberg header builder blocks
		 *
		 * @since 6.1.0
		 */
		private function add_gutenberg_elements() {

			$load_blocks = false;
			if ( is_admin() ) {
				// Header Builder
				if ( ( PortoBuilders::BUILDER_SLUG ) && isset( $_REQUEST['post'] ) && 'header' == get_post_meta( $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$load_blocks = true;
				}

				// Gutenberg Full Site Editing
				global $porto_settings;
				if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && ! empty( $porto_settings['enable-gfse'] ) ) {
					$load_blocks = true;
				}
			}

			if ( $load_blocks ) {
				add_action(
					'enqueue_block_editor_assets',
					function () {
						wp_enqueue_script( 'porto-hb-blocks', PORTO_FUNC_URL . 'builders/elements/header/gutenberg/blocks.min.js', array( 'porto_blocks' ), PORTO_SHORTCODES_VERSION, true );
					},
					999
				);
				add_filter(
					'block_categories_all',
					function ( $categories ) {
						return array_merge(
							$categories,
							array(
								array(
									'slug'  => 'porto-hb',
									'title' => __( 'Porto Header Blocks', 'porto-functionality' ),
									'icon'  => '',
								),
							)
						);
					},
					11,
					1
				);
			}

			register_block_type(
				'porto-hb/porto-logo',
				array(
					'attributes'      => array(
						'className'    => array(
							'type' => 'string',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_logo',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-menu',
				array(
					'attributes'      => array(
						'location'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'title'          => array(
							'type' => 'string',
						),
						'font_size'      => array(
							'type' => 'string',
						),
						'font_weight'    => array(
							'type' => 'integer',
						),
						'text_transform' => array(
							'type' => 'string',
						),
						'line_height'    => array(
							'type' => 'string',
						),
						'letter_spacing' => array(
							'type' => 'string',
						),
						'color'          => array(
							'type' => 'string',
						),
						'padding'        => array(
							'type' => 'string',
						),
						'bgcolor'        => array(
							'type' => 'string',
						),
						'hover_color'    => array(
							'type' => 'string',
						),
						'hover_bgcolor'  => array(
							'type' => 'string',
						),
						'popup_width'    => array(
							'type' => 'string',
						),
						'page_builder'   => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_menu',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-switcher',
				array(
					'attributes'      => array(
						'type'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'font_size'      => array(
							'type' => 'string',
						),
						'font_weight'    => array(
							'type' => 'integer',
						),
						'text_transform' => array(
							'type' => 'string',
						),
						'line_height'    => array(
							'type' => 'string',
						),
						'letter_spacing' => array(
							'type' => 'string',
						),
						'color'          => array(
							'type' => 'string',
						),
						'hover_color'    => array(
							'type' => 'string',
						),
						'page_builder'   => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_switcher',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-search-form',
				array(
					'attributes'      => array(
						'placeholder_text'       => array(
							'type' => 'string',
						),
						'category_filter'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'category_filter_mobile' => array(
							'type'    => 'string',
							'default' => '',
						),
						'popup_pos'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'toggle_size'            => array(
							'type' => 'string',
						),
						'toggle_color'           => array(
							'type' => 'string',
						),
						'input_size'             => array(
							'type' => 'string',
						),
						'height'                 => array(
							'type' => 'string',
						),
						'border_width'           => array(
							'type' => 'integer',
						),
						'border_color'           => array(
							'type' => 'string',
						),
						'border_radius'          => array(
							'type' => 'string',
						),
						'page_builder'           => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_search_form',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-mini-cart',
				array(
					'attributes'      => array(
						'type'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'content_type'      => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_cl'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_size'         => array(
							'type' => 'string',
						),
						'icon_color'        => array(
							'type' => 'string',
						),
						'icon_margin_left'  => array(
							'type' => 'string',
						),
						'icon_margin_right' => array(
							'type' => 'string',
						),
						'text_font_size'    => array(
							'type' => 'string',
						),
						'text_font_weight'  => array(
							'type' => 'integer',
						),
						'text_transform'    => array(
							'type' => 'string',
						),
						'text_line_height'  => array(
							'type' => 'string',
						),
						'text_ls'           => array(
							'type' => 'string',
						),
						'text_color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'price_font_size'   => array(
							'type' => 'string',
						),
						'price_font_weight' => array(
							'type' => 'integer',
						),
						'price_line_height' => array(
							'type' => 'string',
						),
						'price_ls'          => array(
							'type' => 'string',
						),
						'price_color'       => array(
							'type' => 'string',
						),
						'page_builder'      => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_mini_cart',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-social',
				array(
					'attributes'      => array(
						'icon_size'           => array(
							'type' => 'string',
						),
						'icon_color'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_hover_color'    => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_color_bg'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_hover_color_bg' => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_border_style'   => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_color_border'   => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_border_size'    => array(
							'type' => 'integer',
						),
						'icon_border_radius'  => array(
							'type' => 'string',
						),
						'icon_border_spacing' => array(
							'type' => 'string',
						),
						'spacing'             => array(
							'type' => 'string',
						),
						'className'           => array(
							'type' => 'string',
						),
						'page_builder'        => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_social',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-menu-icon',
				array(
					'attributes'      => array(
						'icon_cl'      => array(
							'type' => 'string',
						),
						'size'         => array(
							'type' => 'string',
						),
						'bg_color'     => array(
							'type'    => 'string',
							'default' => '',
						),
						'color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_menu_icon',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-divider',
				array(
					'attributes'      => array(
						'width'        => array(
							'type' => 'string',
						),
						'height'       => array(
							'type' => 'string',
						),
						'color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_divider',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-myaccount',
				array(
					'attributes'      => array(
						'icon_cl'      => array(
							'type' => 'string',
						),
						'size'         => array(
							'type' => 'string',
						),
						'color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_myaccount',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-wishlist',
				array(
					'attributes'      => array(
						'icon_cl'      => array(
							'type' => 'string',
						),
						'size'         => array(
							'type' => 'string',
						),
						'color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_wishlist',
					),
				)
			);

			register_block_type(
				'porto-hb/porto-compare',
				array(
					'attributes'      => array(
						'icon_cl'      => array(
							'type' => 'string',
						),
						'size'         => array(
							'type' => 'string',
						),
						'color'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'page_builder' => array(
							'type'    => 'string',
							'default' => 'gutenberg',
						),
					),
					'editor_script'   => 'porto-hb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_hb_compare',
					),
				)
			);
		}

		/**
		 * display logo in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_logo( $atts ) {
			return porto_logo( false, isset( $atts['className'] ) ? $atts['className'] : '' );
		}

		/**
		 * display menu in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_menu( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_menu';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'tg_icon_sz',
							'selectors'  => true,
						),
						array(
							'param_name' => 'between_spacing',
							'selectors'  => true,
						),
						array(
							'param_name' => 'padding3',
							'selectors'  => true,
						),
						array(
							'param_name' => 'show_narrow',
							'selectors'  => true,
						),
						array(
							'param_name' => 'narrow_pos',
							'selectors'  => true,
						),
						array(
							'param_name' => 'narrow_sz',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_font',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_link_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_link_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_link_hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_link_hover_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_margin',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_icon_sz',
							'selectors'  => true,
						),
						array(
							'param_name' => 'top_level_icon_spacing',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_font',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_link_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_link_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_link_hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_link_hover_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_item_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'submenu_narrow_border_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'mega_title_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'mega_title_font',
							'selectors'  => true,
						),
						array(
							'param_name' => 'mega_title_padding',
							'selectors'  => true,
						)
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}

			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}

			if ( ! empty( $shortcode_class ) ) {
				$shortcode_class .= $el_class ? ' ' . $el_class : '';
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( stripslashes( $internal_css ) ) . '</style>';
			}

			if ( ! empty( $atts['location'] ) ) {
				global $porto_settings;
				if ( 'main-toggle-menu' == $atts['location'] && ! empty( $atts['title'] ) && ! empty( $porto_settings ) ) {
					$settings_backup              = $porto_settings['menu-title'];
					$porto_settings['menu-title'] = $atts['title'];
					porto_header_elements( array( (object) array( $atts['location'] => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class );
					$porto_settings['menu-title'] = $settings_backup;
				} else {
					porto_header_elements( array( (object) array( $atts['location'] => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class );
				}
			}
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display language/currency swticher in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_switcher( $atts ) {

			ob_start();

			$el_class = isset( $atts['className'] ) ? trim( $atts['className'] ) : '';
			isset( $atts['type'] ) && porto_header_elements( array( (object) array( $atts['type'] => '' ) ), $el_class );

			return ob_get_clean();
		}

		/**
		 * display search form in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_search_form( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_search_form';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'toggle_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'toggle_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_toggle_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'hover_toggle_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_hover_toggle_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'search_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'height',
							'selectors'  => true,
						),
						array(
							'param_name' => 'border_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'border_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'border_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'input_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'input_placeholder_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'input_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_icon_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_icon_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_icon_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_icon_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'divider_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'category_inner_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'category_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'category_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'category_font',
							'selectors'  => true,
						),
						array(
							'param_name' => 'category_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}

			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			global $porto_settings;
			if ( isset( $porto_settings['search-cats'] ) ) {
				$backup_cat_filter        = $porto_settings['search-cats'];
				$backup_cat_filter_mobile = $porto_settings['search-cats-mobile'];
				$backup_cat_sub           = $porto_settings['search-sub-cats'];
			}
			if ( ! empty( $settings['placeholder_text'] ) ) {
				$backup_placeholder = $porto_settings['search-placeholder'];
			}
			if ( ! empty( $atts['category_filter'] ) ) {
				$porto_settings['search-cats'] = 'yes' == $atts['category_filter'] ? true : false;
			} else {
				$porto_settings['search-cats'] = false;
			}
			if ( ! empty( $atts['category_filter_mobile'] ) ) {
				$porto_settings['search-cats-mobile'] = 'yes' == $atts['category_filter_mobile'] ? true : false;
			} else {
				$porto_settings['search-cats-mobile'] = false;
			}
			if ( ! empty( $atts['sub_cats'] ) ) {
				$porto_settings['search-sub-cats'] = 'yes' == $atts['sub_cats'] ? true : false;
			} else {
				$porto_settings['search-sub-cats'] = false;
			}

			if ( ! empty( $atts['placeholder_text'] ) ) {
				$porto_settings['search-placeholder'] = $atts['placeholder_text'];
			}
			if ( ! empty( $atts['popup_pos'] ) ) {
				$el_class .= ' search-popup-' . $atts['popup_pos'];
			}
			if ( 'simple' == $porto_settings['search-layout'] ) {
				$el_class .= ' simple-popup ';
			}
			porto_header_elements( array( (object) array( 'search-form' => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class . ' ' . $el_class );
			if ( isset( $backup_cat_filter ) ) {
				$porto_settings['search-cats']        = $backup_cat_filter;
				$porto_settings['search-cats-mobile'] = $backup_cat_filter_mobile;
				$porto_settings['search-sub-cats']    = $backup_cat_sub;
			}
			if ( isset( $backup_placeholder ) ) {
				$porto_settings['search-placeholder'] = $backup_placeholder;
			}
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display mini cart in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_mini_cart( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_mini_cart';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'icon_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_icon_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'hover_icon_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_hover_icon_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_bg_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_right',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_item_top',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$shortcode_class .= ( $el_class ? ' ' . $el_class : '' );
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			global $porto_settings;
			if ( ! empty( $atts['type'] ) ) {
				if ( isset( $porto_settings['minicart-type'] ) ) {
					$backup_type = $porto_settings['minicart-type'];
				}
				$porto_settings['minicart-type'] = $atts['type'];
			}
			if ( ! empty( $atts['icon_cl'] ) ) {
				if ( isset( $porto_settings['minicart-icon'] ) ) {
					$backup_icon = $porto_settings['minicart-icon'];
				}
				$porto_settings['minicart-icon'] = $atts['icon_cl'];
			}
			if ( ! isset( $atts['content_type'] ) ) {
				$atts['content_type'] = '';
			}
			if ( isset( $porto_settings['minicart-content'] ) ) {
				$backup_content_type = $porto_settings['minicart-content'];
			}
			$porto_settings['minicart-content'] = $atts['content_type'];

			porto_header_elements( array( (object) array( 'mini-cart' => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class );

			if ( isset( $backup_type ) ) {
				$porto_settings['minicart-type'] = $backup_type;
			}
			if ( isset( $backup_icon ) ) {
				$porto_settings['minicart-icon'] = $backup_icon;
			}
			if ( isset( $backup_content_type ) ) {
				$porto_settings['minicart-content'] = $backup_content_type;
			}
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display social icons in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_social( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_social';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'icon_spacing',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$shortcode_class .= ( $el_class ? ' ' . $el_class : '' );
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			porto_header_elements( array( (object) array( 'social' => '' ) ), empty( $shortcode_class ) ? $el_class : $shortcode_class );

			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display mobile menu toggle in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_menu_icon( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_menu_icon';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'icon_padding',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$el_class .= ' ' . $shortcode_class;
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			global $porto_settings;
			$custom_icon = 'fas fa-bars';
			if ( ! empty( $atts['icon_cl'] ) ) {
				$custom_icon = $atts['icon_cl'];
			}
			echo apply_filters( 'porto_header_builder_mobile_toggle', '<a aria-label="Mobile Menu" href="#" class="mobile-toggle' . ( empty( $atts['bg_color'] ) && ( ! isset( $porto_settings['mobile-menu-toggle-bg-color'] ) || 'transparent' == $porto_settings['mobile-menu-toggle-bg-color'] ) ? ' ps-0' : '' ) . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"><i class="' . esc_attr( $custom_icon ) . '"></i></a>' );

			if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && ! empty( $porto_settings['enable-gfse'] ) && isset( $porto_settings['mobile-panel-type'] ) ) {
				if ( 'side' === $porto_settings['mobile-panel-type'] ) {
					add_action( 'wp_footer', function(){
						get_template_part( 'panel' );
					} );
				} elseif ( '' === $porto_settings['mobile-panel-type'] ){
					get_template_part( 'header/mobile_menu' );
				}
			}
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display vertical divider in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_divider( $atts, $echo = false ) {
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			$inline_style = '';
			if ( ! empty( $atts['width'] ) ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $atts['width'] ) );
				if ( ! $unit ) {
					$atts['width'] .= 'px';
				}
				$inline_style .= 'border-left-width:' . esc_attr( $atts['width'] ) . ';';
			}
			if ( ! empty( $atts['height'] ) ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $atts['height'] ) );
				if ( ! $unit ) {
					$atts['height'] .= 'px';
				}
				$inline_style .= 'height:' . esc_attr( $atts['height'] ) . ';';
			}
			if ( ! empty( $atts['color'] ) ) {
				$inline_style .= 'border-left-color:' . esc_attr( $atts['color'] );
			}
			if ( $inline_style ) {
				$inline_style = ' style="' . $inline_style . '"';
			}
			echo '<span class="separator' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '></span>';
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display my account icon in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_myaccount( $atts, $echo = false ) {
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_myaccount';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'spacing',
							'selectors'  => true,
						),
						array(
							'param_name' => 'account_menu_font',
							'selectors'  => true,
						),
						array(
							'param_name' => 'account_dropdown_bgc',
							'selectors'  => true,
						),
						array(
							'param_name' => 'account_dropdown_hbgc',
							'selectors'  => true,
						),
						array(
							'param_name' => 'account_dropdown_lc',
							'selectors'  => true,
						),
						array(
							'param_name' => 'account_dropdown_hlc',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$el_class .= ' ' . $shortcode_class;
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			$icon_cl = 'porto-icon-user-2';
			if ( ! empty( $atts['icon_cl'] ) ) {
				$icon_cl = trim( $atts['icon_cl'] );
			}
			global $porto_settings;
			if ( isset( $porto_settings['show-account-dropdown'] ) ) {
				$backup_account = $porto_settings['show-account-dropdown'];
			}
			$porto_settings['show-account-dropdown'] = ! empty( $atts['account_dropdown'] ) ? true : false;

			$inline_style = '';
			if ( ! empty( $atts['size'] ) ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $atts['size'] ) );
				if ( ! $unit ) {
					$atts['size'] .= 'px';
				}
				$inline_style .= 'font-size:' . esc_attr( $atts['size'] ) . ';';
			}
			if ( $inline_style ) {
				$inline_style = ' style="' . $inline_style . '"';
			}

			if ( function_exists( 'porto_account_menu' ) ) {
				porto_account_menu( $el_class, $icon_cl, $inline_style );
			} else {
				if ( ! is_user_logged_in() && empty( $porto_settings['woo-account-login-style'] ) ) {
					$el_class .= ' porto-link-login';
				}
				echo '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"' . ' title="' . esc_attr__( 'My Account', 'porto' ) . '" class="my-account' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '><i class="' . esc_attr( $icon_cl ) . '"></i></a>';
			}
			if ( isset( $backup_account ) ) {
				$porto_settings['show-account-dropdown'] = $backup_account;
			}
			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display wishlist icon in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_wishlist( $atts, $echo = false ) {
			if ( ! defined( 'YITH_WCWL' ) ) {
				return;
			}
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_wishlist';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'sticky_hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'badge_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'badge_bg_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$el_class .= ' ' . $shortcode_class;
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			$icon_cl = 'porto-icon-wishlist-2';
			if ( ! empty( $atts['icon_cl'] ) ) {
				$icon_cl = trim( $atts['icon_cl'] );
			}
			global $porto_settings;
			if ( isset( $porto_settings['wl-offcanvas'] ) ) {
				$backup_offcanvas = $porto_settings['wl-offcanvas'];
			}
			$porto_settings['wl-offcanvas'] = ! empty( $atts['offcanvas'] ) ? true : false;

			$inline_style = '';
			if ( ! empty( $atts['size'] ) ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $atts['size'] ) );
				if ( ! $unit ) {
					$atts['size'] .= 'px';
				}
				$inline_style .= 'font-size:' . esc_attr( $atts['size'] ) . ';';
			}

			if ( function_exists( 'porto_wishlist' ) ) {
				echo porto_wishlist( $el_class, $icon_cl, $inline_style );
			} else {
				if ( $inline_style ) {
					$inline_style = ' style="' . $inline_style . '"';
				}
				$wc_count = yith_wcwl_count_products();
				echo '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '"' . ' title="' . esc_attr__( 'Wishlist', 'porto' ) . '" class="my-wishlist' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '><i class="' . esc_attr( $icon_cl ) . '"></i><span class="wishlist-count">' . intval( $wc_count ) . '</span></a>';
			}
			if ( isset( $backup_offcanvas ) ) {
				$porto_settings['wl-offcanvas'] = $backup_offcanvas;
			}

			if ( ! $echo ) {
				return ob_get_clean();
			}
		}

		/**
		 * display product compare icon in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_compare( $atts, $echo = false ) {
			if ( ! defined( 'YITH_WOOCOMPARE' ) || ! class_exists( 'YITH_Woocompare' ) ) {
				return;
			}
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				$shortcode_name = 'porto_hb_compare';
				// Shortcode class
				$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					$shortcode_name,
					array(
						array(
							'param_name' => 'badge_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'badge_bg_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );
			}
			if ( ! $echo ) {
				ob_start();
			}
			if ( isset( $atts['el_class'] ) ) {
				$el_class = trim( $atts['el_class'] );
			} elseif ( isset( $atts['className'] ) ) {
				$el_class = trim( $atts['className'] );
			} else {
				$el_class = '';
			}
			if ( ! empty( $shortcode_class ) ) {
				$el_class .= ' ' . $shortcode_class;
			}
			if ( ! empty( $internal_css ) ) {
				// only wpbakery frontend editor
				echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
			}

			global $yith_woocompare;
			$icon_cl = 'porto-icon-compare-link';
			if ( ! empty( $atts['icon_cl'] ) ) {
				$icon_cl = trim( $atts['icon_cl'] );
			}

			$inline_style = '';
			if ( ! empty( $atts['size'] ) ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $atts['size'] ) );
				if ( ! $unit ) {
					$atts['size'] .= 'px';
				}
				$inline_style .= 'font-size:' . esc_attr( $atts['size'] ) . ';';
			}
			if ( ! empty( $atts['color'] ) ) {
				$inline_style .= 'color:' . esc_attr( $atts['color'] );
			}
			if ( $inline_style ) {
				$inline_style = ' style="' . $inline_style . '"';
			}

			$compare_count = isset( $yith_woocompare->obj->products_list ) ? sizeof( $yith_woocompare->obj->products_list ) : 0;

			echo '<a href="#" title="' . esc_attr__( 'Compare', 'porto' ) . '" class="yith-woocompare-open' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '><i class="' . esc_attr( $icon_cl ) . '"></i><span class="compare-count">' . intval( $compare_count ) . '</span></a>';

			if ( ! $echo ) {
				return ob_get_clean();
			}
		}
	}
endif;

PortoBuildersHeader::get_instance();
