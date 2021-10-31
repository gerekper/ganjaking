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

		private $elements = array(
			'logo',
			'menu',
			'switcher',
			'search-form',
			'mini-cart',
			'social',
			'menu-icon',
			'divider',
		);

		private $woo_elements = array(
			'myaccount',
			'wishlist',
			'compare',
		);

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				if ( is_admin() && isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
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

				add_action( 'elementor/widgets/widgets_registered', array( $this, 'add_elementor_elements' ), 10, 1 );
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

			if ( defined( 'VCV_VERSION' ) ) {
				if ( is_admin() ) {
					add_action(
						'vcv:api',
						function( $api ) {
							if ( function_exists( 'porto_is_vc_preview' ) && porto_is_vc_preview() && isset( $_GET['vcv-source-id'] ) ) {
								$post_id = $_GET['vcv-source-id'];
								if ( 'header' == get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
									$base_url = rtrim( plugins_url( basename( dirname( PORTO_FUNC_FILE ) ) ), '\\/' ) . '/builders/elements/header/vc';

									/**
									 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
									*/
									$elements_api = $api->elements;

									foreach ( $this->elements as $tag ) {
										$tag           = 'portoHeader' . ucfirst( str_replace( '-', '', $tag ) );
										$manifest_path = __DIR__ . '/vc/' . $tag . '/manifest.json';
										$element_url   = $base_url . '/' . $tag;
										$elements_api->add( $manifest_path, $element_url );
									}

									if ( class_exists( 'Woocommerce' ) ) {
										foreach ( $this->woo_elements as $tag ) {
											$tag           = 'portoHeader' . ucfirst( str_replace( '-', '', $tag ) );
											$manifest_path = __DIR__ . '/vc/' . $tag . '/manifest.json';
											$element_url   = $base_url . '/' . $tag;
											$elements_api->add( $manifest_path, $element_url );
										}
									}
								}
							}
						},
						10
					);
				}
			}

			$this->add_gutenberg_elements();
		}

		public function add_elementor_elements( $self ) {
			$load_widgets = false;
			if ( is_admin() ) {
				if ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'header' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$load_widgets = true;
				} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
					$load_widgets = true;
				}
			} else {
				global $porto_settings;
				if ( isset( $porto_settings['header-type-select'] ) && 'header_builder_p' == $porto_settings['header-type-select'] ) {
					$load_widgets = true;
				}
			}
			if ( $load_widgets ) {
				foreach ( $this->elements as $element ) {
					include_once PORTO_BUILDERS_PATH . '/elements/header/elementor/' . $element . '.php';
					$class_name = 'Porto_Elementor_HB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
				if ( class_exists( 'Woocommerce' ) ) {
					foreach ( $this->woo_elements as $element ) {
						include_once PORTO_BUILDERS_PATH . '/elements/header/elementor/' . $element . '.php';
						$class_name = 'Porto_Elementor_HB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
						if ( class_exists( $class_name ) ) {
							$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
						}
					}
				}
			}
		}

		private function add_shortcodes( $global_shortcodes = array() ) {
			$shortcodes = $this->elements;
			if ( class_exists( 'Woocommerce' ) ) {
				$shortcodes = array_merge( $shortcodes, $this->woo_elements );
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

						if ( in_array( $tag, array( 'menu', 'search-form', 'mini-cart', 'social', 'menu-con', 'switcher' ) ) ) {
							if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
								echo '<style>';
								include PORTO_BUILDERS_PATH . '/elements/header/wpb/style-' . $tag . '.php';
								echo '</style>';
							}
						}

						if ( 'menu' == $tag ) {
							$this->gutenberg_hb_menu( $atts, true );
						} elseif ( 'switcher' == $tag ) {
							isset( $atts['type'] ) && porto_header_elements( array( (object) array( $atts['type'] => '' ) ), $el_class );
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

			vc_map(
				array(
					'name'     => __( 'Logo', 'porto-functionality' ),
					'base'     => 'porto_hb_logo',
					'icon'     => 'far fa-circle',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Menu', 'porto-functionality' ),
					'base'     => 'porto_hb_menu',
					'icon'     => 'far fa-list-alt',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Location', 'porto-functionality' ),
							'param_name'  => 'location',
							'value'       => array(
								__( 'Select a Location', 'porto-functionality' ) => '',
								__( 'Main Menu', 'porto-functionality' ) => 'main-menu',
								__( 'Secondary Menu', 'porto-functionality' ) => 'secondary-menu',
								__( 'Main Toggle Menu', 'porto-functionality' ) => 'main-toggle-menu',
								__( 'Top Navigation', 'porto-functionality' ) => 'nav-top',
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
							'heading'    => __( 'Top Level Font Size', 'porto-functionality' ),
							'param_name' => 'font_size',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Font Weight', 'porto-functionality' ),
							'param_name' => 'font_weight',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
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
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Line Height', 'porto-functionality' ),
							'param_name' => 'line_height',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Letter Spacing', 'porto-functionality' ),
							'param_name' => 'letter_spacing',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Top Level Left/Right Padding', 'porto-functionality' ),
							'param_name' => 'padding',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Color', 'porto-functionality' ),
							'param_name' => 'color',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Background Color', 'porto-functionality' ),
							'param_name' => 'bgcolor',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Hover Color', 'porto-functionality' ),
							'param_name' => 'hover_color',
							'value'      => '',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'main-toggle-menu', 'nav-top' ),
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Top Level Hover Background Color', 'porto-functionality' ),
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
							'type'       => 'textfield',
							'heading'    => __( 'Custom CSS Class', 'porto-functionality' ),
							'param_name' => 'el_class',
							'dependency' => array(
								'element' => 'location',
								'value'   => array( 'nav-top' ),
							),
						),
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'View Switcher', 'porto-functionality' ),
					'base'     => 'porto_hb_switcher',
					'icon'     => 'fas fa-language',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Search Form', 'porto-functionality' ),
					'base'     => 'porto_hb_search_form',
					'icon'     => 'fas fa-search',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
							'type'       => 'textfield',
							'heading'    => __( 'Search Icon Size', 'porto-functionality' ),
							'param_name' => 'toggle_size',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Search Icon Color', 'porto-functionality' ),
							'param_name' => 'toggle_color',
							'value'      => '',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Input Box Width', 'porto-functionality' ),
							'param_name' => 'input_size',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Height', 'porto-functionality' ),
							'param_name' => 'height',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Border Width', 'porto-functionality' ),
							'param_name' => 'border_width',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Border Color', 'porto-functionality' ),
							'param_name' => 'border_color',
							'value'      => '',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Border Radius', 'porto-functionality' ),
							'param_name' => 'border_radius',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Separator Color', 'porto-functionality' ),
							'param_name' => 'divider_color',
							'value'      => '',
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Mini Cart', 'porto-functionality' ),
					'base'     => 'porto_hb_mini_cart',
					'icon'     => 'fas fa-shopping-cart',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Type', 'porto-functionality' ),
							'description' => __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> Mini Cart Type instead.', 'porto-functionality' ),
							'param_name'  => 'type',
							'value'       => array(
								__( 'Theme Options', 'porto-functionality' ) => '',
								__( 'Simple', 'porto-functionality' ) => 'simple',
								__( 'Arrow Alt', 'porto-functionality' ) => 'minicart-arrow-alt',
								__( 'Text', 'porto-functionality' ) => 'minicart-inline',
								__( 'Icon & Text', 'porto-functionality' ) => 'minicart-text',
							),
							'std'         => '',
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
							'type'        => 'textfield',
							'heading'     => __( 'Mini Cart Text', 'porto-functionality' ),
							'description' => __( 'If you have any trouble with this setting, please use Porto -> Theme Options -> Header -> Mini Cart Text instead.', 'porto-functionality' ),
							'param_name'  => 'cart_text',
							'dependency'  => array(
								'element' => 'type',
								'value'   => array( 'minicart-inline', 'minicart-text' ),
							),
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
							'value'      => '',
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
							'type'        => 'dropdown',
							'heading'     => __( 'Text Font Weight', 'porto-functionality' ),
							'param_name'  => 'text_font_weight',
							'value'       => array(
								__( 'Default' ) => '',
								'200'           => '200',
								'300'           => '300',
								'400'           => '400',
								'500'           => '500',
								'600'           => '600',
								'700'           => '700',
								'800'           => '800',
							),
							'admin_label' => true,
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
							'type'        => 'dropdown',
							'heading'     => __( 'Price Font Weight', 'porto-functionality' ),
							'param_name'  => 'price_font_weight',
							'value'       => array(
								__( 'Default' ) => '',
								'200'           => '200',
								'300'           => '300',
								'400'           => '400',
								'500'           => '500',
								'600'           => '600',
								'700'           => '700',
								'800'           => '800',
							),
							'admin_label' => true,
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
					'name'     => __( 'Social Icons', 'porto-functionality' ),
					'base'     => 'porto_hb_social',
					'icon'     => 'fab fa-facebook-f',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Font Size', 'porto-functionality' ),
							'param_name' => 'icon_size',
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
							'value'      => '',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Border Width', 'porto-functionality' ),
							'param_name' => 'icon_border_size',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Border Radius', 'porto-functionality' ),
							'param_name' => 'icon_border_radius',
						),
						array(
							'type'       => 'textfield',
							'heading'    => __( 'Icon Size', 'porto-functionality' ),
							'param_name' => 'icon_border_spacing',
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
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Mobile Menu Icon', 'porto-functionality' ),
					'base'     => 'porto_hb_menu_icon',
					'icon'     => 'fas fa-bars',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Vertical Divider', 'porto-functionality' ),
					'base'     => 'porto_hb_divider',
					'icon'     => 'fas fa-grip-lines-vertical',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
					'name'     => __( 'My Account Icon', 'porto-functionality' ),
					'base'     => 'porto_hb_myaccount',
					'icon'     => 'far fa-user',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Wishlist Icon', 'porto-functionality' ),
					'base'     => 'porto_hb_wishlist',
					'icon'     => 'far fa-heart',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Compare Icon', 'porto-functionality' ),
					'base'     => 'porto_hb_compare',
					'icon'     => 'porto-icon-compare-link',
					'category' => __( 'Header Builder', 'porto-functionality' ),
					'params'   => array(
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

		private function include_style( $blocks ) {
			if ( empty( $blocks ) ) {
				return;
			}
			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-hb/porto-menu', 'porto-hb/porto-switcher', 'porto-hb/porto-search-form', 'porto-hb/porto-mini-cart', 'porto-hb/porto-social', 'porto-hb/porto-menu-icon' ) ) ) {
					$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];
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
				if ( ( PortoBuilders::BUILDER_SLUG ) && isset( $_REQUEST['post'] ) && 'header' == get_post_meta( $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
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
						'className' => array(
							'type' => 'string',
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
							'default' => 'minicart-arrow-alt',
						),
						'content_type'      => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon_cl'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'cart_text'         => array(
							'type' => 'string',
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
						'icon_cl'  => array(
							'type' => 'string',
						),
						'size'     => array(
							'type' => 'string',
						),
						'bg_color' => array(
							'type'    => 'string',
							'default' => '',
						),
						'color'    => array(
							'type'    => 'string',
							'default' => '',
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
						'width'    => array(
							'type' => 'string',
						),
						'height'   => array(
							'type' => 'string',
						),
						'color'    => array(
							'type'    => 'string',
							'default' => '',
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
						'icon_cl'  => array(
							'type' => 'string',
						),
						'size'     => array(
							'type' => 'string',
						),
						'color'    => array(
							'type'    => 'string',
							'default' => '',
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
						'icon_cl' => array(
							'type' => 'string',
						),
						'size'    => array(
							'type' => 'string',
						),
						'color'   => array(
							'type'    => 'string',
							'default' => '',
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
						'icon_cl' => array(
							'type' => 'string',
						),
						'size'    => array(
							'type' => 'string',
						),
						'color'   => array(
							'type'    => 'string',
							'default' => '',
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
			if ( ! empty( $atts['location'] ) ) {
				global $porto_settings;
				if ( 'main-toggle-menu' == $atts['location'] && ! empty( $atts['title'] ) && ! empty( $porto_settings ) ) {
					$settings_backup              = $porto_settings['menu-title'];
					$porto_settings['menu-title'] = $atts['title'];
					porto_header_elements( array( (object) array( $atts['location'] => '' ) ), $el_class );
					$porto_settings['menu-title'] = $settings_backup;
				} else {
					porto_header_elements( array( (object) array( $atts['location'] => '' ) ), $el_class );
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
			global $porto_settings;
			if ( ! empty( $atts['category_filter'] ) ) {
				$backup_cat_filter             = $porto_settings['search-cats'];
				$porto_settings['search-cats'] = 'yes' == $atts['category_filter'] ? true : false;
			}
			if ( ! empty( $atts['category_filter_mobile'] ) ) {
				$backup_cat_filter_mobile             = $porto_settings['search-cats-mobile'];
				$porto_settings['search-cats-mobile'] = 'yes' == $atts['category_filter_mobile'] ? true : false;
			}
			if ( ! empty( $atts['placeholder_text'] ) ) {
				$backup_placeholder                   = $porto_settings['search-placeholder'];
				$porto_settings['search-placeholder'] = $atts['placeholder_text'];
			}
			if ( ! empty( $atts['popup_pos'] ) ) {
				$el_class .= ( $el_class ? ' ' : '' ) . 'search-popup-' . $atts['popup_pos'];
			}
			porto_header_elements( array( (object) array( 'search-form' => '' ) ), $el_class );
			if ( ! empty( $atts['category_filter'] ) ) {
				$porto_settings['search-cats'] = $backup_cat_filter;
			}
			if ( ! empty( $atts['category_filter_mobile'] ) ) {
				$porto_settings['search-cats-mobile'] = $backup_cat_filter_mobile;
			}
			if ( ! empty( $atts['placeholder_text'] ) ) {
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
			global $porto_settings;
			if ( ! empty( $atts['type'] ) ) {
				$backup_type                     = $porto_settings['minicart-type'];
				$porto_settings['minicart-type'] = $atts['type'];
			}
			if ( ! empty( $atts['icon_cl'] ) ) {
				$backup_icon                     = $porto_settings['minicart-icon'];
				$porto_settings['minicart-icon'] = $atts['icon_cl'];
			}
			if ( ! isset( $atts['content_type'] ) ) {
				$atts['content_type'] = '';
			}
			if ( ! empty( $atts['cart_text'] ) ) {
				$backup_cart_text = false;
				if ( isset( $porto_settings['minicart-text'] ) ) {
					$backup_cart_text = $porto_settings['minicart-text'];
				}
				$porto_settings['minicart-text'] = $atts['cart_text'];
			}
			$backup_content_type                = $porto_settings['minicart-content'];
			$porto_settings['minicart-content'] = $atts['content_type'];

			porto_header_elements( array( (object) array( 'mini-cart' => '' ) ), $el_class );

			if ( ! empty( $atts['type'] ) ) {
				$porto_settings['minicart-type'] = $backup_type;
			}
			if ( ! empty( $atts['icon_cl'] ) ) {
				$porto_settings['minicart-icon'] = $backup_icon;
			}
			$porto_settings['minicart-content'] = $backup_content_type;
			if ( ! empty( $atts['cart_text'] ) && false !== $backup_cart_text ) {
				$porto_settings['minicart-text'] = $backup_cart_text;
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
		public function gutenberg_hb_social( $atts ) {
			ob_start();
			$el_class = isset( $atts['className'] ) ? trim( $atts['className'] ) : '';
			porto_header_elements( array( (object) array( 'social' => '' ) ), $el_class );
			return ob_get_clean();
		}

		/**
		 * display mobile menu toggle in gutenberg header builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_hb_menu_icon( $atts, $echo = false ) {
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
			global $porto_settings;
			$custom_icon = 'fas fa-bars';
			if ( ! empty( $atts['icon_cl'] ) ) {
				$custom_icon = $atts['icon_cl'];
			}
			echo apply_filters( 'porto_header_builder_mobile_toggle', '<a class="mobile-toggle' . ( empty( $atts['bg_color'] ) && ( ! isset( $porto_settings['mobile-menu-toggle-bg-color'] ) || 'transparent' == $porto_settings['mobile-menu-toggle-bg-color'] ) ? ' ps-0' : '' ) . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"><i class="' . esc_attr( $custom_icon ) . '"></i></a>' );
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
			$icon_cl  = 'porto-icon-user-2';
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
			echo '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '"' . ' title="' . esc_attr__( 'My Account', 'porto' ) . '" class="my-account' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '><i class="' . esc_attr( $icon_cl ) . '"></i></a>';
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
			$icon_cl  = 'porto-icon-wishlist-2';
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

			if ( function_exists( 'porto_wishlist' ) ) {
				echo porto_wishlist( $el_class, $icon_cl, $inline_style );
			} else {
				if ( $inline_style ) {
					$inline_style = ' style="' . $inline_style . '"';
				}
				$wc_count = yith_wcwl_count_products();
				echo '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '"' . ' title="' . esc_attr__( 'Wishlist', 'porto' ) . '" class="my-wishlist' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '"' . $inline_style . '><i class="' . esc_attr( $icon_cl ) . '"></i><span class="wishlist-count">' . intval( $wc_count ) . '</span></a>';
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

new PortoBuildersHeader;
