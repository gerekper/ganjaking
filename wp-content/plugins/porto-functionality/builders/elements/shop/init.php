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

		/**
		 * Is legacy mode?
		 *
		 * @access protected
		 * @since 2.3.0
		 */
		protected $legacy_mode = true;

		/**
		 * Is shop builder related pages?
		 *
		 * @since 2.4.0
		 */
		protected $is_shop_builder_layout = false;

		public static $elements = array(
			'archives',
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
			$this->legacy_mode = apply_filters( 'porto_legacy_mode', true );
			if ( ! $this->legacy_mode ) { // if soft mode
				self::$elements = array_diff( self::$elements, array( 'products' ) );
			}
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

				add_action( 'elementor/widgets/register', array( $this, 'add_elementor_elements' ), 10, 1 );
			}

			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'vc_after_init', array( $this, 'load_wpb_map_elements' ) );
			}
			if ( is_admin() ) {
				add_action( 'save_post', array( $this, 'add_shortcodes_css' ), 99, 2 );
			}

			if ( defined( 'WPB_VC_VERSION' ) || defined( 'VCV_VERSION' ) ) {
				add_action(
					'template_redirect',
					function() {
						if ( ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'shop' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
							$this->is_shop_builder_layout = true;
						} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'shop' ) ) {
							$this->is_shop_builder_layout = true;
						}

						if ( $this->is_shop_builder_layout ) {
							$this->add_shortcodes();
						}
					}
				);

				add_action(
					'admin_init',
					function() {
						$this->is_shop_builder_layout = false;
						if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vc_save' == $_REQUEST['action'] ) {
							$this->is_shop_builder_layout = true;
						} elseif ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_POST['post_type'] ) {
							$this->is_shop_builder_layout = true;
						}

						if ( $this->is_shop_builder_layout ) {
							$this->add_shortcodes();
						}
					}
				);
			}

			$this->add_gutenberg_elements();

			add_filter( 'porto_shop_builder_set_preview', array( $this, 'set_preview' ) );
			add_action( 'porto_shop_builder_unset_preview', array( $this, 'unset_preview' ) );
		}

		public function add_elementor_elements( $self ) {
			if ( is_admin() ) {
				if ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'shop' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$this->is_shop_builder_layout = true;
				} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
					$this->is_shop_builder_layout = true;
				}
			} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'shop' ) ) {
				$this->is_shop_builder_layout = true;
			}
			if ( $this->is_shop_builder_layout ) {
				foreach ( $this::$elements as $element ) {
					if ( 'toolbox' == $element ) {
						continue;
					}
					include_once PORTO_BUILDERS_PATH . 'elements/shop/elementor/' . $element . '.php';
					$class_name = 'Porto_Elementor_SB_' . ucfirst( str_replace( '-', '_', $element ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
			}
		}

		/**
		 * Set preview for editor and template view
		 *
		 * @since 2.4.0
		 */
		public function set_preview() {
			if ( ! is_archive() && $this->is_shop_builder_layout ) {
				global $wp_query, $post, $product;

				$this->original = array(
					'wp_query' => $wp_query,
					'post'     => $post,
					'product'  => empty( $product ) ? '' : $product,
				);

				// Get current options
				$posts = new WP_Query;
				$posts->query(
					array(
						'post_type'           => 'product',
						'post_status'         => 'publish',
						'posts_per_page'      => apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page', 12 ) ),
						'ignore_sticky_posts' => true,
					)
				);
				$wp_query = $posts;
				WC()->query->product_query( $wp_query );

				wc_setup_loop();

				return true;
			}

			return false;
		}

		/**
		 * Unset preview for editor and template view
		 *
		 * @since 2.4.0
		 */
		public function unset_preview() {
			global $wp_query;
			if ( ! empty( $this->original ) && $this->original['wp_query'] !== $wp_query ) {
				global $post, $product;

				$wp_query = $this->original['wp_query'];
				if ( ! empty( $this->original['post'] ) ) {
					$post = $this->original['post'];
				}
				if ( ! empty( $this->original['product'] ) ) {
					$product = $this->original['product'];
				}
				unset( $this->original );
			}
		}

		private function add_shortcodes() {
			$shortcodes = $this::$elements;
			foreach ( $shortcodes as $tag ) {
				if ( 'archives' == $tag ) {
					continue;
				}
				$shortcode_name = str_replace( '-', '_', $tag );
				add_shortcode(
					'porto_sb_' . $shortcode_name,
					function( $atts, $content = null ) use ( $tag ) {
						ob_start();
						$shortcode_name = 'porto_sb_' . str_replace( '-', '_', $tag );
						$el_class       = isset( $atts['el_class'] ) ? trim( $atts['el_class'] ) : '';
						$internal_css   = '';

						if ( 'products' == $tag && is_singular( PortoBuilders::BUILDER_SLUG ) ) {
							if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
								$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
									$atts,
									'porto_sb_products',
									array(
										array(
											'param_name' => 'dots_pos_top',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_pos_bottom',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_pos_left',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_pos_right',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_br_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_abr_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_bg_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'dots_abg_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_fs',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_width',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_height',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_br',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_h_pos',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_v_pos',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_h_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_bg_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_h_bg_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_br_color',
											'selectors'  => true,
										),
										array(
											'param_name' => 'nav_h_br_color',
											'selectors'  => true,
										),
									)
								);
								$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_sb_products', $atts );
								echo '<div class="archive-products">';
								include $template;
								echo '</div>';
							}
						} else {

							// Shortcode class
							if ( 'toggle' == $tag ) {
								$params_keys = array( 'fs', 'spacing', 'clr', 'active_clr', 'w', 'h', 'bs', 'bw', 'bc', 'bc_active' );
							} elseif ( 'result' == $tag || 'description' == $tag ) {
								$params_keys = array( 'tg', 'clr' );
							} elseif ( 'sort' == $tag || 'count' == $tag ) {
								$params_keys = array( 'label_hide', 'label_color', 'label_typography', 'select_color', 'select_typography', 'select_padding', 'spacing' );
							}
							$params = array();
							if ( ! empty( $params_keys ) ) {
								foreach ( $params_keys as $k ) {
									$params[] = array(
										'param_name' => $k,
										'selectors'  => true,
									);
								}
							}

							if ( ! empty( $params ) ) {
								$shortcode_class = 'wpb_custom_' . PortoShortcodesClass::get_global_hashcode( $atts, $shortcode_name, $params );
							}

							$internal_css = PortoShortcodesClass::generate_wpb_css( $shortcode_name, $atts );

							include PORTO_BUILDERS_PATH . '/elements/shop/wpb/' . $tag . '.php';
						}

						$result = ob_get_clean();
						if ( $result && $internal_css ) {
							$first_tag_index = strpos( $result, '>' );
							if ( $first_tag_index ) {
								$internal_css = porto_filter_inline_css( $internal_css, false );
								if ( $internal_css ) {
									$result = substr( $result, 0, $first_tag_index + 1 ) . '<style>' . wp_strip_all_tags( $internal_css ) . '</style>' . substr( $result, $first_tag_index + 1 );
								}
							}
						}

						return $result;
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

			do_action( 'porto_archive_builder_add_wpb_elements', 'shop', $custom_class );

			if ( $this->legacy_mode ) {
				vc_map(
					array(
						'name'     => __( 'Shop Products', 'porto-functionality' ),
						'base'     => 'porto_sb_products',
						'icon'     => 'fas fa-cart-arrow-down',
						'category' => __( 'Shop Builder', 'porto-functionality' ),
						'params'   => array_merge(
							array(
								array(
									'type'       => 'porto_param_heading',
									'param_name' => 'notice_wrong_data',
									'text'       => __( 'This element was deprecated in 6.3.0. Please use Type Builder Archives instead.', 'porto-functionality' ),
								),
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
									'selectors'   => array(
										'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
									),
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
									'group'      => __( 'Style', 'porto-functionality' ),
								),
								array(
									'type'       => 'checkbox',
									'heading'    => __( 'Use theme default font family?', 'porto-functionality' ),
									'param_name' => 'title_use_theme_fonts',
									'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
									'std'        => 'yes',
									'group'      => __( 'Style', 'porto-functionality' ),
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
										'element' => 'title_use_theme_fonts',
										'value_not_equal_to' => 'yes',
									),
									'group'      => __( 'Style', 'porto-functionality' ),
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
									'group'      => __( 'Style', 'porto-functionality' ),
								),
							),
							porto_vc_product_slider_fields()
						),
					)
				);
			}

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
					'name'        => __( 'Sort By', 'porto-functionality' ),
					'base'        => 'porto_sb_sort',
					'icon'        => 'fas fa-sort-alpha-down',
					'category'    => __( 'Shop Builder', 'porto-functionality' ),
					'description' => __( 'Displays a select box which allows to sort products by popularity, price, rating, etc.', 'porto-functionality' ),
					'as_child'    => array( 'only' => 'porto_sb_toolbox' ),
					'params'      => array(
						array(
							'type'       => 'dropdown',
							'heading'    => esc_html__( 'Label Visibility', 'porto-functionality' ),
							'param_name' => 'label_hide',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Hide', 'porto-functionality' )    => 'none',
							),
							'selectors'  => array(
								'{{WRAPPER}} label' => 'display: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => esc_html__( 'Label Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls color of label.', 'porto-functionality' ),
							'param_name'  => 'label_color',
							'selectors'   => array(
								'{{WRAPPER}} label' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => esc_html__( 'Label Typography', 'porto-functionality' ),
							'param_name' => 'label_typography',
							'selectors'  => array(
								'{{WRAPPER}} label',
							),
						),

						array(
							'heading'     => esc_html__( 'Select box Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls color of select box.', 'porto-functionality' ),
							'type'        => 'colorpicker',
							'param_name'  => 'select_color',
							'selectors'   => array(
								'{{WRAPPER}} select' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_typography',
							'param_name' => 'select_typography',
							'heading'    => esc_html__( 'Select box Typography', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} select',
							),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => esc_html__( 'Select box Padding', 'porto-functionality' ),
							'description' => esc_html__( 'Controls padding of select box.', 'porto-functionality' ),
							'param_name'  => 'select_padding',
							'selectors'   => array(
								'{{WRAPPER}} select' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'heading'     => esc_html__( 'Spacing', 'porto-functionality' ),
							'type'        => 'number',
							'param_name'  => 'spacing',
							'description' => esc_html__( 'Controls spacing between label and select box.', 'porto-functionality' ),
							'min'         => 0,
							'max'         => 20,
							'suffix'      => 'px',
							'selectors'   => array(
								'{{WRAPPER}} label' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{VALUE}}px',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Products Result Count', 'porto-functionality' ),
					'base'        => 'porto_sb_result',
					'icon'        => 'fas fa-text-width',
					'category'    => __( 'Shop Builder', 'porto-functionality' ),
					'description' => __( 'Displays the products result count.', 'porto-functionality' ),
					'as_child'    => array( 'only' => 'porto_sb_toolbox' ),
					'params'      => array(
						array(
							'type'       => 'porto_typography',
							'heading'    => esc_html__( 'Typography', 'porto-functionality' ),
							'param_name' => 'tg',
							'selectors'  => array(
								'{{WRAPPER}} .woocommerce-result-count',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => esc_html__( 'Color', 'porto-functionality' ),
							'param_name' => 'clr',
							'selectors'  => array(
								'{{WRAPPER}} .woocommerce-result-count' => 'color: {{VALUE}};',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Count Per Page', 'porto-functionality' ),
					'base'        => 'porto_sb_count',
					'icon'        => 'fas fa-list-ul',
					'category'    => __( 'Shop Builder', 'porto-functionality' ),
					'as_child'    => array( 'only' => 'porto_sb_toolbox' ),
					'description' => __( 'You can set these values using WooCommerce -> Product Archives -> Products per Page in Theme Options. This displays pagination together when pagination is disabled in Type Builder Archives element.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'dropdown',
							'heading'    => esc_html__( 'Label Visibility', 'porto-functionality' ),
							'param_name' => 'label_hide',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Hide', 'porto-functionality' )    => 'none',
							),
							'selectors'  => array(
								'{{WRAPPER}} label' => 'display: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => esc_html__( 'Label Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls color of label.', 'porto-functionality' ),
							'param_name'  => 'label_color',
							'selectors'   => array(
								'{{WRAPPER}} label' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => esc_html__( 'Label Typography', 'porto-functionality' ),
							'param_name' => 'label_typography',
							'selectors'  => array(
								'{{WRAPPER}} label',
							),
						),

						array(
							'heading'     => esc_html__( 'Select box Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls color of select box.', 'porto-functionality' ),
							'type'        => 'colorpicker',
							'param_name'  => 'select_color',
							'selectors'   => array(
								'{{WRAPPER}} select' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_typography',
							'param_name' => 'select_typography',
							'heading'    => esc_html__( 'Select box Typography', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} select',
							),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => esc_html__( 'Select box Padding', 'porto-functionality' ),
							'description' => esc_html__( 'Controls padding of select box.', 'porto-functionality' ),
							'param_name'  => 'select_padding',
							'selectors'   => array(
								'{{WRAPPER}} select' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'heading'     => esc_html__( 'Spacing', 'porto-functionality' ),
							'type'        => 'number',
							'param_name'  => 'spacing',
							'description' => esc_html__( 'Controls spacing between label and select box.', 'porto-functionality' ),
							'min'         => 0,
							'max'         => 20,
							'suffix'      => 'px',
							'selectors'   => array(
								'{{WRAPPER}} label' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{VALUE}}px',
							),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'        => esc_html__( 'Grid / List Toggle', 'porto-functionality' ),
					'base'        => 'porto_sb_toggle',
					'icon'        => 'fas fa-th-list',
					'category'    => esc_html__( 'Shop Builder', 'porto-functionality' ),
					'as_child'    => array( 'only' => 'porto_sb_toolbox' ),
					'description' => esc_html__( 'Displays the toggle buttons to switch products layout in grid and list view.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'dropdown',
							'heading'    => esc_html__( 'Grid Icon Type', 'porto-functionality' ),
							'param_name' => 'icon_grid_type',
							'value'      => array(
								__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
								__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
								__( 'Porto Icon', 'porto-functionality' ) => 'porto',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'Grid Icon', 'porto-functionality' ),
							'param_name' => 'icon_grid',
							'dependency' => array(
								'element' => 'icon_grid_type',
								'value'   => 'fontawesome',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'Grid Icon', 'porto-functionality' ),
							'param_name' => 'icon_grid_simpleline',
							'settings'   => array(
								'type'         => 'simpleline',
								'iconsPerPage' => 4000,
							),
							'dependency' => array(
								'element' => 'icon_grid_type',
								'value'   => 'simpleline',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'Grid Icon', 'porto-functionality' ),
							'param_name' => 'icon_grid_porto',
							'settings'   => array(
								'type'         => 'porto',
								'iconsPerPage' => 4000,
							),
							'dependency' => array(
								'element' => 'icon_grid_type',
								'value'   => 'porto',
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => esc_html__( 'List Icon Type', 'porto-functionality' ),
							'param_name' => 'icon_list_type',
							'value'      => array(
								esc_html__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
								esc_html__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
								esc_html__( 'Porto Icon', 'porto-functionality' ) => 'porto',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'List Icon', 'porto-functionality' ),
							'param_name' => 'icon_list',
							'dependency' => array(
								'element' => 'icon_list_type',
								'value'   => 'fontawesome',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'List Icon', 'porto-functionality' ),
							'param_name' => 'icon_list_simpleline',
							'settings'   => array(
								'type'         => 'simpleline',
								'iconsPerPage' => 4000,
							),
							'dependency' => array(
								'element' => 'icon_list_type',
								'value'   => 'simpleline',
							),
						),
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'List Icon', 'porto-functionality' ),
							'param_name' => 'icon_list_porto',
							'settings'   => array(
								'type'         => 'porto',
								'iconsPerPage' => 4000,
							),
							'dependency' => array(
								'element' => 'icon_list_type',
								'value'   => 'porto',
							),
						),
						array(
							'type'       => 'number',
							'heading'    => esc_html__( 'Icon Size (px)', 'porto-functionality' ),
							'param_name' => 'fs',
							'min'        => 0,
							'max'        => 50,
							'suffix'     => 'px',
							'selectors'  => array(
								'{{WRAPPER}} > a' => 'font-size: {{VALUE}}px;',
							),
						),
						array(
							'type'        => 'number',
							'heading'     => esc_html__( 'Item Spacing (px)', 'porto-functionality' ),
							'description' => esc_html__( 'Adjust spacing between toggle buttons.', 'porto-functionality' ),
							'param_name'  => 'spacing',
							'min'         => 0,
							'max'         => 20,
							'suffix'      => 'px',
							'selectors'   => array(
								'{{WRAPPER}} #grid' => 'margin-' . ( is_rtl() ? 'left' : 'right' ) . ': {{VALUE}}px;',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => esc_html__( 'Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls the color of the button.', 'porto-functionality' ),
							'param_name'  => 'clr',
							'selectors'   => array(
								'{{WRAPPER}} a:not(.active)' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => esc_html__( 'Active Color', 'porto-functionality' ),
							'description' => esc_html__( 'Controls the active color of the button.', 'porto-functionality' ),
							'param_name'  => 'active_clr',
							'selectors'   => array(
								'{{WRAPPER}} .active' => 'color: {{VALUE}};',
							),
						),

						array(
							'type'       => 'number',
							'heading'    => esc_html__( 'Width (px)', 'porto-functionality' ),
							'param_name' => 'w',
							'min'        => 0,
							'max'        => 100,
							'suffix'     => 'px',
							'selectors'  => array(
								'{{WRAPPER}} > a' => 'width: {{VALUE}}px;',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'number',
							'heading'    => esc_html__( 'Height (px)', 'porto-functionality' ),
							'param_name' => 'h',
							'min'        => 0,
							'max'        => 100,
							'suffix'     => 'px',
							'selectors'  => array(
								'{{WRAPPER}} > a' => 'height: {{VALUE}}px;',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Border Style', 'porto-functionality' ),
							'param_name' => 'bs',
							'std'        => '',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'None', 'porto-functionality' )   => 'none',
								__( 'Solid', 'porto-functionality' )  => 'solid',
								__( 'Dashed', 'porto-functionality' ) => 'dashed',
								__( 'Dotted', 'porto-functionality' ) => 'dotted',
								__( 'Double', 'porto-functionality' ) => 'double',
								__( 'Inset', 'porto-functionality' )  => 'inset',
								__( 'Outset', 'porto-functionality' ) => 'outset',
							),
							'selectors'  => array(
								'{{WRAPPER}} > a' => 'border-style: {{VALUE}};',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_number',
							'heading'    => __( 'Border Width', 'porto-functionality' ),
							'param_name' => 'bw',
							'units'      => array( 'px' ),
							'dependency' => array(
								'element'            => 'bs',
								'value_not_equal_to' => array( 'none' ),
							),
							'selectors'  => array(
								'{{WRAPPER}} > a' => 'border-width: {{VALUE}}{{UNIT}};',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Border Color', 'porto-functionality' ),
							'param_name' => 'bc',
							'dependency' => array(
								'element'            => 'bs',
								'value_not_equal_to' => array( 'none' ),
							),
							'selectors'  => array(
								'{{WRAPPER}}.gridlist-toggle > a' => 'border-color: {{VALUE}};',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Active Border Color', 'porto-functionality' ),
							'param_name' => 'bc_active',
							'dependency' => array(
								'element'            => 'bs',
								'value_not_equal_to' => array( 'none' ),
							),
							'selectors'  => array(
								'{{WRAPPER}} > a.active' => 'border-color: {{VALUE}};',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						$custom_class,
					),
				)
			);

			vc_map(
				array(
					'name'                    => __( 'Filter Toggle', 'porto-functionality' ),
					'base'                    => 'porto_sb_filter',
					'icon'                    => 'fas fa-toggle-off',
					'category'                => __( 'Shop Builder', 'porto-functionality' ),
					'description'             => __( 'Displays a toggle button or filtering widgets according to "Filter Layout" in Theme Options.', 'porto-functionality' ),
					'as_child'                => array( 'only' => 'porto_sb_toolbox' ),
					'show_settings_on_create' => false,
					'params'                  => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'desc',
							'text'       => __( 'Displays a toggle button or filtering widgets according to "Filter Layout" in Theme Options.', 'porto-functionality' ),
						),
					),
				)
			);

			vc_map(
				array(
					'name'        => __( 'Shop Hooks', 'porto-functionality' ),
					'base'        => 'porto_sb_actions',
					'icon'        => 'fas fa-cart-arrow-down',
					'category'    => __( 'Shop Builder', 'porto-functionality' ),
					'description' => __( 'Renders WooCommerce supported WordPress actions.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'action', 'porto-functionality' ),
							'param_name'  => 'action',
							'value'       => array(
								''                             => '',
								'woocommerce_before_shop_loop' => 'woocommerce_before_shop_loop',
								'woocommerce_after_shop_loop'  => 'woocommerce_after_shop_loop',
							),
							'admin_label' => true,
						),
						$custom_class,
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
							'type'       => 'porto_typography',
							'heading'    => esc_html__( 'Typography', 'porto-functionality' ),
							'param_name' => 'tg',
							'selectors'  => array(
								'{{WRAPPER}}',
							),
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => esc_html__( 'Color', 'porto-functionality' ),
							'param_name' => 'clr',
							'selectors'  => array(
								'{{WRAPPER}}' => 'color: {{VALUE}};',
							),
						),
						$custom_class,
					),
				)
			);
		}

		/**
		 * Save shortcode css to post meta in WPBakery & Gutenberg editor
		 *
		 * @since 6.1.0
		 */
		public function add_shortcodes_css( $post_id, $post ) {
			if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'shop' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( defined( 'WPB_VC_VERSION' ) && false !== strpos( $post->post_content, '[porto_sb_' ) ) {
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
			} elseif ( false !== strpos( $post->post_content, '<!-- wp:porto-sb' ) ) { // Gutenberg editor
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
				if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-sb/porto-products', 'porto-sb/porto-description' ) ) ) {
					$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];
					include PORTO_BUILDERS_PATH . '/elements/shop/wpb/style-' . str_replace( 'porto-sb/porto-', '', $block['blockName'] ) . '.php';
				}
				if ( ! empty( $block['innerBlocks'] ) ) {
					$this->include_style( $block['innerBlocks'] );
				}
			}
		}

		/**
		 * Load gutenberg shop builder blocks
		 *
		 * @since 6.1.0
		 */
		private function add_gutenberg_elements() {

			$load_blocks = false;
			if ( is_admin() ) {
				if ( ( PortoBuilders::BUILDER_SLUG ) && isset( $_REQUEST['post'] ) && 'shop' == get_post_meta( $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					$load_blocks = true;
				}
			}

			if ( $load_blocks ) {
				add_action(
					'enqueue_block_editor_assets',
					function () {
						wp_enqueue_script( 'porto-sb-blocks', PORTO_FUNC_URL . 'builders/elements/shop/gutenberg/blocks.min.js', array( 'porto_blocks' ), PORTO_SHORTCODES_VERSION, true );
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
									'slug'  => 'porto-sb',
									'title' => __( 'Porto Shop Blocks', 'porto-functionality' ),
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
				'porto-sb/porto-products',
				array(
					'attributes'      => array(
						'view'                 => array(
							'type'    => 'string',
							'default' => 'grid',
						),
						'grid_layout'          => array(
							'type'    => 'integer',
							'default' => 1,
						),
						'grid_height'          => array(
							'type'    => 'string',
							'default' => '600px',
						),
						'spacing'              => array(
							'type' => 'integer',
						),
						'columns'              => array(
							'type'    => 'integer',
							'default' => 4,
						),
						'columns_mobile'       => array(
							'type' => 'integer',
						),
						'addlinks_pos'         => array(
							'type' => 'string',
						),
						'overlay_bg_opacity'   => array(
							'type'    => 'integer',
							'default' => 30,
						),
						'image_size'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'navigation'           => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'nav_pos'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'nav_pos2'             => array(
							'type' => 'string',
						),
						'nav_type'             => array(
							'type' => 'string',
						),
						'show_nav_hover'       => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'pagination'           => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'dots_pos'             => array(
							'type' => 'string',
						),
						'dots_style'           => array(
							'type' => 'string',
						),
						'autoplay'             => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'autoplay_timeout'     => array(
							'type'    => 'integer',
							'default' => 5000,
						),
						'title_google_font'    => array(
							'type' => 'string',
						),
						'title_font_size'      => array(
							'type' => 'string',
						),
						'title_font_weight'    => array(
							'type' => 'integer',
						),
						'title_text_transform' => array(
							'type' => 'string',
						),
						'title_line_height'    => array(
							'type' => 'string',
						),
						'title_ls'             => array(
							'type' => 'string',
						),
						'title_color'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'price_font_size'      => array(
							'type' => 'string',
						),
						'price_font_weight'    => array(
							'type' => 'integer',
						),
						'price_line_height'    => array(
							'type' => 'string',
						),
						'price_ls'             => array(
							'type' => 'string',
						),
						'price_color'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-sb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_sb_products',
					),
				)
			);

			$shop_blocks = array(
				'toolbox',
				'sort',
				'count',
				'toggle',
				'filter',
			);
			foreach ( $shop_blocks as $block ) {
				register_block_type(
					'porto-sb/porto-' . $block,
					array(
						'attributes'      => array(),
						'editor_script'   => 'porto-sb-blocks',
						'render_callback' => array(
							$this,
							'gutenberg_sb_' . $block,
						),
					)
				);
			}

			register_block_type(
				'porto-sb/porto-actions',
				array(
					'attributes'      => array(
						'action' => array(
							'type'    => 'string',
							'default' => 'woocommerce_before_shop_loop',
						),
					),
					'editor_script'   => 'porto-sb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_sb_actions',
					),
				)
			);

			register_block_type(
				'porto-sb/porto-title',
				array(
					'attributes'      => array(
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
						'ls'             => array(
							'type' => 'string',
						),
						'color'          => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'editor_script'   => 'porto-sb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_sb_title',
					),
				)
			);

			register_block_type(
				'porto-sb/porto-description',
				array(
					'attributes'      => array(
						'font_size'   => array(
							'type' => 'string',
						),
						'font_weight' => array(
							'type' => 'integer',
						),
						'line_height' => array(
							'type' => 'string',
						),
						'ls'          => array(
							'type' => 'string',
						),
						'color'       => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'editor_script'   => 'porto-sb-blocks',
					'render_callback' => array(
						$this,
						'gutenberg_sb_description',
					),
				)
			);
		}

		/**
		 * display products in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_products( $atts ) {
			ob_start();
			$el_class = isset( $atts['className'] ) ? trim( $atts['className'] ) : '';
			if ( wp_is_json_request() && isset( $_REQUEST['context'] ) && 'edit' == $_REQUEST['context'] ) {
				if ( $template = porto_shortcode_woo_template( 'porto_products' ) ) {
					echo '<div class="archive-products">';
					include $template;
					echo '</div>';
				}
			} else {
				include PORTO_BUILDERS_PATH . '/elements/shop/wpb/products.php';
			}
			return ob_get_clean();
		}

		/**
		 * display tool box in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_toolbox( $atts, $content = null ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/toolbox.php';
			return ob_get_clean();
		}

		/**
		 * display sort by in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_sort( $atts ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/sort.php';
			return ob_get_clean();
		}

		/**
		 * display count per page in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_count( $atts ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/count.php';
			return ob_get_clean();
		}

		/**
		 * display grid/list toggle in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_toggle( $atts ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/toggle.php';
			return ob_get_clean();
		}

		/**
		 * display filter toggle in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_filter( $atts ) {
			ob_start();
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/filter.php';
			return ob_get_clean();
		}

		/**
		 * display hooks in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_actions( $atts ) {
			ob_start();
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/actions.php';
			return ob_get_clean();
		}

		/**
		 * display archive title in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_title( $atts ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/title.php';
			return ob_get_clean();
		}

		/**
		 * display archive description in gutenberg shop builder
		 *
		 * @since 6.1.0
		 */
		public function gutenberg_sb_description( $atts ) {
			ob_start();
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
				unset( $atts['className'] );
			}
			include PORTO_BUILDERS_PATH . '/elements/shop/wpb/description.php';
			return ob_get_clean();
		}
	}
endif;

new PortoBuildersShop;

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_porto_sb_toolbox extends WPBakeryShortCodesContainer {
	}
}
