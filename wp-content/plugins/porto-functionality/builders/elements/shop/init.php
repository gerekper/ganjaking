<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Shop Builder
 */

if ( ! class_exists( 'PortoBuildersShop' ) ) :
	class PortoBuildersShop {

		private $display_wpb_elements = false;

		private $elements = array(
			'products',
			'toolbox',
			'sort',
			'count',
			'result',
			'toggle',
			'filter',
			'actions',
			'title',
			'description',
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
								'porto-sb',
								array(
									'title'  => __( 'Porto Shop Builder', 'porto-functionality' ),
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

				if ( is_admin() ) {
					add_action( 'save_post', array( $this, 'add_wpb_shortcodes_css' ), 99, 2 );
				}
			}

			if ( defined( 'WPB_VC_VERSION' ) || defined( 'VCV_VERSION' ) ) {
				add_action(
					'template_redirect',
					function() {
						$should_add_shortcodes = false;
						if ( ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'shop' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
							$should_add_shortcodes = true;
						} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'shop' ) ) {
							$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							$this->add_shortcodes();
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
							if ( function_exists( 'porto_is_vc_preview' ) && porto_is_vc_preview() ) {
								$post_id = $_GET['vcv-source-id'];
								$terms   = wp_get_post_terms( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, array( 'fields' => 'names' ) );
								if ( isset( $terms[0] ) && 'shop' == $terms[0] ) {
									$base_url = rtrim( plugins_url( basename( dirname( PORTO_FUNC_FILE ) ) ), '\\/' ) . '/builders/elements/shop/vc';

									/**
									 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
									*/
									$elements_api = $api->elements;

									foreach ( $this->elements as $tag ) {
										$tag           = 'portoShop' . ucfirst( str_replace( '-', '', $tag ) );
										$manifest_path = __DIR__ . '/vc/' . $tag . '/manifest.json';
										$element_url   = $base_url . '/' . $tag;
										$elements_api->add( $manifest_path, $element_url );
									}
								}
							}
						},
						10
					);
				}
			}
		}

		public function add_elementor_elements( $self ) {
			$load_widgets = false;
			if ( is_admin() ) {
				if ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'shop' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$load_widgets = true;
				} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
					$load_widgets = true;
				}
			}
			if ( $load_widgets ) {
				foreach ( $this->elements as $element ) {
					if ( 'toolbox' == $element ) {
						continue;
					}
					include_once PORTO_BUILDERS_PATH . 'elements/shop/elementor/' . $element . '.php';
					$class_name = 'Porto_Elementor_SB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
			}
		}

		private function add_shortcodes() {
			$shortcodes = $this->elements;
			foreach ( $shortcodes as $tag ) {
				$shortcode_name = str_replace( '-', '_', $tag );
				add_shortcode(
					'porto_sb_' . $shortcode_name,
					function( $atts, $content = null ) use ( $tag ) {
						ob_start();
						$el_class = isset( $atts['el_class'] ) ? trim( $atts['el_class'] ) : '';

						if ( 'products' == $tag && is_singular( PortoBuilders::BUILDER_SLUG ) ) {
							if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
								echo '<div class="archive-products">';
								include $template;
								echo '</div>';
							}
						} else {
							include PORTO_BUILDERS_PATH . '/elements/shop/wpb/' . $tag . '.php';
						}

						return ob_get_clean();
					}
				);
			}
		}

		/**
		 * Add WPBakery Page Builder shop elements
		 */
		public function load_wpb_map_elements() {
			if ( ! $this->display_wpb_elements ) {
				$this->display_wpb_elements = PortoBuilders::check_load_wpb_elements( 'shop' );
			}
			if ( ! $this->display_wpb_elements ) {
				return;
			}

			$custom_class = porto_vc_custom_class();

			vc_map(
				array(
					'name'     => __( 'Shop Products', 'porto-functionality' ),
					'base'     => 'porto_sb_products',
					'icon'     => 'fas fa-cart-arrow-down',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'params'   => array_merge(
						array(
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'View mode', 'porto-functionality' ),
								'param_name'  => 'view',
								'value'       => porto_sh_commons( 'products_view_mode' ),
								'admin_label' => true,
							),
							array(
								'type'       => 'porto_image_select',
								'heading'    => __( 'Grid Layout', 'porto-functionality' ),
								'param_name' => 'grid_layout',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'std'        => '1',
								'value'      => porto_sh_commons( 'masonry_layouts' ),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
								'param_name' => 'grid_height',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'suffix'     => 'px',
								'std'        => 600,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
								'param_name'  => 'spacing',
								'dependency'  => array(
									'element' => 'view',
									'value'   => array( 'grid', 'creative', 'products-slider' ),
								),
								'suffix'      => 'px',
								'std'         => '',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns', 'porto-functionality' ),
								'param_name' => 'columns',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'products-slider', 'grid', 'divider' ),
								),
								'std'        => '4',
								'value'      => porto_sh_commons( 'products_columns' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
								'param_name' => 'columns_mobile',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'products-slider', 'grid', 'divider', 'list' ),
								),
								'std'        => '',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									'1' => '1',
									'2' => '2',
									'3' => '3',
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Product Layout', 'porto-functionality' ),
								'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
								'param_name'  => 'addlinks_pos',
								'value'       => porto_sh_commons( 'products_addlinks_pos' ),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Overlay Background Opacity (%)', 'porto-functionality' ),
								'param_name' => 'overlay_bg_opacity',
								'dependency' => array(
									'element' => 'addlinks_pos',
									'value'   => array( 'onimage2', 'onimage3' ),
								),
								'suffix'     => '%',
								'std'        => '30',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Image Size', 'porto-functionality' ),
								'param_name' => 'image_size',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'products-slider', 'grid', 'divider', 'list' ),
								),
								'value'      => porto_sh_commons( 'image_sizes' ),
								'std'        => '',
							),
							$custom_class,
							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'title_text_typography',
								'text'       => __( 'Product Title settings', 'porto-functionality' ),
								'group'      => 'Style',
							),
							array(
								'type'       => 'checkbox',
								'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
								'param_name' => 'title_use_theme_fonts',
								'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
								'std'        => 'yes',
								'group'      => 'Style',
							),
							array(
								'type'       => 'google_fonts',
								'param_name' => 'title_google_font',
								'settings'   => array(
									'fields' => array(
										'font_family_description' => __( 'Select Font Family.', 'porto-functionality' ),
										'font_style_description'  => __( 'Select Font Style.', 'porto-functionality' ),
									),
								),
								'dependency' => array(
									'element'            => 'title_use_theme_fonts',
									'value_not_equal_to' => 'yes',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Font Size', 'porto-functionality' ),
								'param_name'  => 'title_font_size',
								'admin_label' => true,
								'group'       => 'Style',
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Font Weight', 'porto-functionality' ),
								'param_name'  => 'title_font_weight',
								'value'       => array(
									__( 'Default', 'porto-functionality' ) => '',
									'100' => '100',
									'200' => '200',
									'300' => '300',
									'400' => '400',
									'500' => '500',
									'600' => '600',
									'700' => '700',
									'800' => '800',
									'900' => '900',
								),
								'admin_label' => true,
								'group'       => 'Style',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Line Height', 'porto-functionality' ),
								'param_name'  => 'title_line_height',
								'admin_label' => true,
								'group'       => 'Style',
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Letter Spacing', 'porto-functionality' ),
								'param_name'  => 'title_ls',
								'admin_label' => true,
								'group'       => 'Style',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Color', 'porto-functionality' ),
								'param_name' => 'title_color',
								'value'      => '',
								'group'      => 'Style',
							),
						),
						porto_vc_product_slider_fields()
					),
				)
			);

			vc_map(
				array(
					'name'         => __( 'Tool Box', 'porto-functionality' ),
					'description'  => __( 'Tools box is a container which contains "Sort By", "Display Count", "Grid/List Toggle", etc.', 'porto-functionality' ),
					'base'         => 'porto_sb_toolbox',
					'icon'         => 'fas fa-toolbox',
					'category'     => __( 'Shop Builder', 'porto-functionality' ),
					'as_parent'    => array( 'only' => 'porto_sb_sort,porto_sb_count,porto_sb_result,porto_sb_toggle,porto_sb_filter' ),
					'is_container' => true,
					'js_view'      => 'VcColumnView',
					'params'       => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Sort By', 'porto-functionality' ),
					'base'     => 'porto_sb_sort',
					'icon'     => 'fas fa-sort-alpha-down',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'as_child' => array( 'only' => 'porto_sb_toolbox' ),
					'params'   => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Products Result Count', 'porto-functionality' ),
					'base'     => 'porto_sb_result',
					'icon'     => 'fas fa-text-width',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'as_child' => array( 'only' => 'porto_sb_toolbox' ),
					'params'   => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Count Per Page', 'porto-functionality' ),
					'base'     => 'porto_sb_count',
					'icon'     => 'fas fa-list-ul',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'as_child' => array( 'only' => 'porto_sb_toolbox' ),
					'params'   => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Grid / List Toggle', 'porto-functionality' ),
					'base'     => 'porto_sb_toggle',
					'icon'     => 'fas fa-th-list',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'as_child' => array( 'only' => 'porto_sb_toolbox' ),
					'params'   => array(
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Filter Toggle', 'porto-functionality' ),
					'base'     => 'porto_sb_filter',
					'icon'     => 'fas fa-toggle-off',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'as_child' => array( 'only' => 'porto_sb_toolbox' ),
					'params'   => array(),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Shop Hooks', 'porto-functionality' ),
					'base'     => 'porto_sb_actions',
					'icon'     => 'fas fa-cart-arrow-down',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'action', 'porto-functionality' ),
							'param_name'  => 'action',
							'value'       => array(
								'woocommerce_before_shop_loop' => 'woocommerce_before_shop_loop',
								'woocommerce_after_shop_loop' => 'woocommerce_after_shop_loop',
							),
							'admin_label' => true,
						),
					),
				)
			);

			vc_map(
				array(
					'name'     => __( 'Archive Title', 'porto-functionality' ),
					'base'     => 'porto_sb_title',
					'icon'     => 'fas fa-heading',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Font Size', 'porto-functionality' ),
							'param_name'  => 'font_size',
							'admin_label' => true,
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Font Weight', 'porto-functionality' ),
							'param_name'  => 'font_weight',
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								'100' => '100',
								'200' => '200',
								'300' => '300',
								'400' => '400',
								'500' => '500',
								'600' => '600',
								'700' => '700',
								'800' => '800',
								'900' => '900',
							),
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Line Height', 'porto-functionality' ),
							'param_name'  => 'line_height',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Letter Spacing', 'porto-functionality' ),
							'param_name'  => 'ls',
							'admin_label' => true,
						),
						array(
							'type'       => 'colorpicker',
							'class'      => '',
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
					'name'     => __( 'Archive Description', 'porto-functionality' ),
					'base'     => 'porto_sb_description',
					'icon'     => 'far fa-file-alt',
					'category' => __( 'Shop Builder', 'porto-functionality' ),
					'params'   => array(
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Font Size', 'porto-functionality' ),
							'param_name'  => 'font_size',
							'admin_label' => true,
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Font Weight', 'porto-functionality' ),
							'param_name'  => 'font_weight',
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								'100' => '100',
								'200' => '200',
								'300' => '300',
								'400' => '400',
								'500' => '500',
								'600' => '600',
								'700' => '700',
								'800' => '800',
								'900' => '900',
							),
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Line Height', 'porto-functionality' ),
							'param_name'  => 'line_height',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => __( 'Letter Spacing', 'porto-functionality' ),
							'param_name'  => 'ls',
							'admin_label' => true,
						),
						array(
							'type'       => 'colorpicker',
							'class'      => '',
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
		 * Save shortcode css to post meta
		 */
		public function add_wpb_shortcodes_css( $post_id, $post ) {
			if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'shop' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			ob_start();
			$css = '';
			preg_match_all( '/' . get_shortcode_regex( array( 'porto_sb_description', 'porto_sb_products' ) ) . '/', $post->post_content, $shortcodes );
			foreach ( $shortcodes[2] as $index => $tag ) {
				$atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
				include PORTO_BUILDERS_PATH . '/elements/shop/wpb/style-' . str_replace( array( 'porto_sb_', '_' ), array( '', '-' ), $tag ) . '.php';
			}
			$css = ob_get_clean();
			if ( $css ) {
				update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
			} else {
				delete_post_meta( $post_id, 'porto_builder_css' );
			}
		}
	}
endif;

new PortoBuildersShop;

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_porto_sb_toolbox extends WPBakeryShortCodesContainer {
	}
}
