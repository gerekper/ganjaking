<?php
/**
 * Add shortcodes for single product page
 *
 * @author   Porto Themes
 * @category Library
 * @since    5.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PortoCustomProduct {

	protected $shortcodes = array(
		'image',
		'title',
		'rating',
		'actions',
		'price',
		'excerpt',
		'description',
		'add_to_cart',
		'meta',
		'tabs',
		'upsell',
		'related',
		'next_prev_nav',
	);

	protected $display_product_page_elements = false;

	protected $edit_post = null;

	protected $edit_product = null;

	protected $is_product = false;

	protected static $instance = null;

	public function __construct() {
		$this->init();
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function init() {
		remove_action( 'porto_after_content_bottom', 'porto_woocommerce_output_related_products', 10 );

		if ( defined( 'WPB_VC_VERSION' ) || defined( 'VCV_VERSION' ) ) {
			add_action(
				'template_redirect',
				function () {
					$should_add_shortcodes = false;
					if ( ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'product' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
						$should_add_shortcodes = true;
					} else {
						global $porto_settings;
						if ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'product' ) ) {
							$should_add_shortcodes = true;
						} elseif ( is_singular( 'product' ) && isset( $porto_settings['product-single-content-layout'] ) && 'builder' == $porto_settings['product-single-content-layout'] && ! empty( $porto_settings['product-single-content-builder'] ) ) {
							$should_add_shortcodes = true;
						}
					}

					if ( $should_add_shortcodes ) {
						foreach ( $this->shortcodes as $shortcode ) {
							add_shortcode( 'porto_single_product_' . $shortcode, array( $this, 'shortcode_single_product_' . $shortcode ) );
						}
					}
				}
			);

			add_action(
				'admin_init',
				function () {
					$should_add_shortcodes = false;
					if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vc_save' == $_REQUEST['action'] ) {
						$should_add_shortcodes = true;
					} elseif ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_POST['post_type'] ) {
						$should_add_shortcodes = true;
					}

					if ( $should_add_shortcodes ) {
						foreach ( $this->shortcodes as $shortcode ) {
							add_shortcode( 'porto_single_product_' . $shortcode, array( $this, 'shortcode_single_product_' . $shortcode ) );
						}
					}
				}
			);
		}

		add_action( 'save_post', array( $this, 'add_shortcodes_css' ), 100, 2 );

		if ( defined( 'WPB_VC_VERSION' ) ) {
			if ( is_admin() || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				add_action( 'vc_after_init', array( $this, 'load_custom_product_shortcodes' ) );
			}
		}

		add_action(
			'wp',
			function () {
				if ( is_singular( PortoBuilders::BUILDER_SLUG ) ) {
					$terms = wp_get_post_terms( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, array( 'fields' => 'names' ) );
					if ( ! empty( $terms ) && 'product' == $terms[0] ) {
						add_filter( 'body_class', array( $this, 'filter_body_class' ) );
						add_filter( 'porto_is_product', '__return_true' );
					}
				}
			}
		);

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			add_filter( 'porto_is_product', array( $this, 'filter_is_product' ), 20 );
		} elseif ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( is_admin() && isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
				add_action(
					'elementor/elements/categories_registered',
					function ( $self ) {
						$self->add_category(
							'custom-product',
							array(
								'title'  => __( 'Porto Single Product', 'porto-functionality' ),
								'active' => false,
							)
						);
					}
				);
			}

			add_action( 'elementor/widgets/widgets_registered', array( $this, 'elementor_custom_product_shortcodes' ), 10, 1 );
		}
		if ( defined( 'VCV_VERSION' ) ) {
			add_action( 'vcv:api', array( $this, 'vc_custom_product_shortcodes' ), 9 );
		}

		/**
		 * Enqueue styles and scripts for single product gutenberg blocks
		 *
		 * @since 6.1
		 */
		if ( is_admin() ) {
			$load_blocks = false;
			if ( ( PortoBuilders::BUILDER_SLUG ) && isset( $_REQUEST['post'] ) && 'product' == get_post_meta( $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				$load_blocks = true;
			}
			if ( $load_blocks ) {
				add_action(
					'enqueue_block_editor_assets',
					function () {
						wp_enqueue_script( 'porto_single_product_blocks', PORTO_FUNC_URL . 'builders/elements/product/gutenberg/blocks.min.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-data'/*, 'wp-editor'*/ ), PORTO_VERSION, true );
					},
					1000
				);
				add_filter(
					'block_categories_all',
					function ( $categories ) {
						return array_merge(
							$categories,
							array(
								array(
									'slug'  => 'porto-single-product',
									'title' => __( 'Porto Single Product Blocks', 'porto-functionality' ),
									'icon'  => '',
								),
							)
						);
					},
					11,
					1
				);
			}
		}

		$gutenberg_attr = array(
			'image'         => array(
				'style' => array(
					'type' => 'string',
				),
			),
			'title'         => array(
				'font_family'    => array(
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
				'el_class'       => array(
					'type' => 'string',
				),
			),
			'excerpt'       => array(
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
					'type' => 'string',
				),
			),
			'price'         => array(
				'font_family'         => array(
					'type' => 'string',
				),
				'font_size'           => array(
					'type' => 'string',
				),
				'font_weight'         => array(
					'type' => 'integer',
				),
				'text_transform'      => array(
					'type' => 'string',
				),
				'line_height'         => array(
					'type' => 'string',
				),
				'letter_spacing'      => array(
					'type' => 'string',
				),
				'color'               => array(
					'type' => 'string',
				),
				'sale_font_family'    => array(
					'type' => 'string',
				),
				'sale_font_size'      => array(
					'type' => 'string',
				),
				'sale_font_weight'    => array(
					'type' => 'integer',
				),
				'sale_text_transform' => array(
					'type' => 'string',
				),
				'sale_line_height'    => array(
					'type' => 'string',
				),
				'sale_letter_spacing' => array(
					'type' => 'string',
				),
				'sale_color'          => array(
					'type' => 'string',
				),
			),
			'rating'        => array(
				'font_size' => array(
					'type' => 'string',
				),
				'bgcolor'   => array(
					'type' => 'string',
				),
				'color'     => array(
					'type' => 'string',
				),
			),
			'actions'       => array(
				'action' => array(
					'type' => 'string',
				),
			),
			'add_to_cart'   => array(),
			'meta'          => array(),
			'next_prev_nav' => array(),
			'description'   => array(),
			'tabs'          => array(
				'style' => array(
					'type' => 'string',
				),
			),
			'upsell'        => array(
				'title'              => array(
					'type' => 'string',
				),
				'title_border_style' => array(
					'type' => 'string',
				),
				'view'               => array(
					'type'    => 'string',
					'default' => 'grid',
				),
				'status'             => array(
					'type' => 'string',
				),
				'count'              => array(
					'type' => 'integer',
				),
				'orderby'            => array(
					'type'    => 'string',
					'default' => 'title',
				),
				'order'              => array(
					'type'    => 'string',
					'default' => 'asc',
				),
				'columns'            => array(
					'type' => 'integer',
				),
				'columns_mobile'     => array(
					'type' => 'string',
				),
				'column_width'       => array(
					'type' => 'string',
				),
				'grid_layout'        => array(
					'type' => 'integer',
				),
				'grid_height'        => array(
					'type' => 'string',
				),
				'spacing'            => array(
					'type' => 'integer',
				),
				'addlinks_pos'       => array(
					'type' => 'string',
				),
				'navigation'         => array(
					'type' => 'boolean',
				),
				'show_nav_hover'     => array(
					'type' => 'boolean',
				),
				'pagination'         => array(
					'type' => 'boolean',
				),
				'nav_pos'            => array(
					'type' => 'string',
				),
				'nav_pos2'           => array(
					'type' => 'string',
				),
				'nav_type'           => array(
					'type' => 'string',
				),
				'dots_pos'           => array(
					'type' => 'string',
				),
				'category_filter'    => array(
					'type' => 'boolean',
				),
				'pagination_style'   => array(
					'type' => 'string',
				),
			),
			'related'       => array(
				'title'              => array(
					'type' => 'string',
				),
				'title_border_style' => array(
					'type' => 'string',
				),
				'view'               => array(
					'type'    => 'string',
					'default' => 'grid',
				),
				'status'             => array(
					'type' => 'string',
				),
				'count'              => array(
					'type' => 'integer',
				),
				'orderby'            => array(
					'type'    => 'string',
					'default' => 'title',
				),
				'order'              => array(
					'type'    => 'string',
					'default' => 'asc',
				),
				'columns'            => array(
					'type' => 'integer',
				),
				'columns_mobile'     => array(
					'type' => 'string',
				),
				'column_width'       => array(
					'type' => 'string',
				),
				'grid_layout'        => array(
					'type' => 'integer',
				),
				'grid_height'        => array(
					'type' => 'string',
				),
				'spacing'            => array(
					'type' => 'integer',
				),
				'addlinks_pos'       => array(
					'type' => 'string',
				),
				'navigation'         => array(
					'type' => 'boolean',
				),
				'show_nav_hover'     => array(
					'type' => 'boolean',
				),
				'pagination'         => array(
					'type' => 'boolean',
				),
				'nav_pos'            => array(
					'type' => 'string',
				),
				'nav_pos2'           => array(
					'type' => 'string',
				),
				'nav_type'           => array(
					'type' => 'string',
				),
				'dots_pos'           => array(
					'type' => 'string',
				),
				'category_filter'    => array(
					'type' => 'boolean',
				),
				'pagination_style'   => array(
					'type' => 'string',
				),
			),
		);

		foreach ( $gutenberg_attr as $shortcode => $attr ) {
			register_block_type(
				'porto-single-product/porto-sp-' . str_replace( '_', '-', $shortcode ),
				array(
					'editor_script'   => 'porto_single_product_blocks',
					'render_callback' => array( $this, 'shortcode_single_product_' . $shortcode ),
					'attributes'      => $attr,
				)
			);
		}

		/* init google structured data */
		add_action(
			'init',
			function() {
				remove_action( 'woocommerce_single_product_summary', array( WC()->structured_data, 'generate_product_data' ), 60 );
				add_action( 'woocommerce_after_single_product', array( WC()->structured_data, 'generate_product_data' ), 60 );
			}
		);
	}

	public function filter_body_class( $classes ) {
		global $post;
		if ( $post && PortoBuilders::BUILDER_SLUG == $post->post_type ) {
			$classes[] = 'single-product';
		}
		return $classes;
	}

	public function filter_is_product( $is_product ) {
		if ( $this->is_product ) {
			return true;
		}
		$post_id = (int) vc_get_param( 'vc_post_id' );
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && PortoBuilders::BUILDER_SLUG == $post->post_type ) {
				$this->is_product = true;
				return true;
			}
		}
		return $is_product;
	}

	private function restore_global_product_variable() {
		if ( ! $this->edit_product && ( is_singular( PortoBuilders::BUILDER_SLUG ) || ( isset( $_REQUEST['context'] ) && 'edit' == $_REQUEST['context'] ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) ) ) {

			$query = new WP_Query(
				array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'posts_per_page'      => 1,
					'numberposts'         => 1,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $query->have_posts() ) {
				$the_post           = $query->next_post();
				$this->edit_post    = $the_post;
				$this->edit_product = wc_get_product( $the_post );
			}
		}
		if ( $this->edit_product ) {
			global $post, $product;
			$post = $this->edit_post;
			setup_postdata( $this->edit_post );
			$product = $this->edit_product;
			return true;
		}
		return false;
	}

	private function reset_global_product_variable() {
		if ( $this->edit_product ) {
			wp_reset_postdata();
		}
	}

	public function shortcode_single_product_image( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract(
			shortcode_atts(
				array(
					'style' => '',
				),
				$atts
			)
		);

		if ( 'transparent' == $style ) {
			wp_enqueue_script( 'jquery-slick' );
		}
		if ( ! $style ) {
			$style = 'default';
		}
		ob_start();
		echo '<div class="product-layout-image' . ( $style ? ' product-layout-' . esc_attr( $style ) : '' ) . '">';
		echo '<div class="summary-before">';
		woocommerce_show_product_sale_flash();
		echo '</div>';
		global $porto_product_layout;
		$porto_product_layout = $style;

		if ( defined( 'ELEMENTOR_VERSION' ) && ! has_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails' ) ) {
			add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		}
		wc_get_template_part( 'single-product/product-image' );
		echo '</div>';
		$porto_product_layout = 'builder';

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_title( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		$result = '';
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$result .= '<style>';
			ob_start();
			include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-title.php';
			$result .= ob_get_clean();
			$result .= '</style>';
		}

		extract(
			shortcode_atts(
				array(
					'font_family'    => '',
					'font_size'      => '',
					'font_weight'    => '',
					'text_transform' => '',
					'line_height'    => '',
					'letter_spacing' => '',
					'color'          => '',
					'el_class'       => '',
				),
				$atts
			)
		);

		global $porto_settings;
		$el_class = ! empty( $atts['className'] ) ? $atts['className'] : $el_class;
		$result  .= '<h2 class="product_title entry-title' . ( ! $porto_settings['product-nav'] ? '' : ' show-product-nav' ) . ( $el_class ? ' ' . esc_attr( trim( $el_class ) ) : '' ) . '">';
		$result  .= esc_html( get_the_title() );
		$result  .= '</h2>';

		$this->reset_global_product_variable();

		return $result;
	}

	public function shortcode_single_product_rating( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			echo '<style>';
			include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-rating.php';
			echo '</style>';
		}
		woocommerce_template_single_rating();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_actions( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract(
			shortcode_atts(
				array(
					'action' => 'woocommerce_single_product_summary',
				),
				$atts
			)
		);
		if ( 'woocommerce_single_product_summary' == $action && defined( 'ELEMENTOR_VERSION' ) ) {
			if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title' ) ) {
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			}
			if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating' ) ) {
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			}
			if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ) ) {
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			}
			if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt' ) ) {
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			}
			if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing' ) ) {
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			}
		}

		ob_start();
		do_action( $action );

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_price( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}
		if ( ! has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' ) ) {
			return null;
		}

		ob_start();
		if ( ! empty( $atts ) ) {
			if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
				echo '<style>';
				include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-price.php';
				echo '</style>';
			}
			echo '<div class="single-product-price">';
		}
		woocommerce_template_single_price();
		if ( ! empty( $atts ) ) {
			echo '</div>';
		}

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_excerpt( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			echo '<style>';
			include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-excerpt.php';
			echo '</style>';
		}

		woocommerce_template_single_excerpt();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_description( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		the_content();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_add_to_cart( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		global $porto_settings;
		if ( isset( $porto_settings['product-show-price-role'] ) && ! empty( $porto_settings['product-show-price-role'] ) ) {
			$hide_price = false;
			if ( ! is_user_logged_in() ) {
				$hide_price = true;
			} else {
				foreach ( wp_get_current_user()->roles as $role => $val ) {
					if ( ! in_array( $val, $porto_settings['product-show-price-role'] ) ) {
						$hide_price = true;
						break;
					}
				}
			}
			if ( $hide_price ) {
				return null;
			}
		}

		ob_start();
		echo '<div class="product-summary-wrap">';
		if ( ( defined( 'ELEMENTOR_VERSION' ) || function_exists( 'register_block_type' ) ) && ! wp_doing_ajax() ) {
			if ( ! has_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart' ) ) {
				add_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			}
			if ( ! has_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart' ) ) {
				add_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			}
			if ( ! has_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart' ) ) {
				add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			}
			if ( ! has_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart' ) ) {
				add_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
			}
		}
		woocommerce_template_single_add_to_cart();

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			echo '<script>theme.WooQtyField.initialize();</script>';
		}
		echo '</div>';

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_meta( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		woocommerce_template_single_meta();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_tabs( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		extract(
			shortcode_atts(
				array(
					'style' => '', // tabs or accordion
				),
				$atts
			)
		);

		ob_start();
		if ( 'vertical' == $style ) {
			echo '<style>.woocommerce-tabs .resp-tabs-list { display: none; }
.woocommerce-tabs h2.resp-accordion { display: block; }
.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -4px; }
.woocommerce-tabs .tab-content { border-top: none; padding-' . ( is_rtl() ? 'right' : 'left' ) . ': 20px; }</style>';
		}

		if ( defined( 'ELEMENTOR_VERSION' ) && ! wp_doing_ajax() ) {
			if ( ! has_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' ) ) {
				add_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );
			}
			if ( ! has_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs' ) ) {
				add_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );
			}
		}
		wc_get_template_part( 'single-product/tabs/tabs' );

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_upsell( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		global $product, $porto_settings;
		if ( ! $porto_settings['product-upsells'] ) {
			return;
		}
		$upsells = $product->get_upsell_ids();
		if ( sizeof( $upsells ) === 0 ) {
			return;
		}
		if ( in_array( $product->get_id(), $upsells ) ) {
			$upsells = array_diff( $upsells, array( $product->get_id() ) );
		}

		if ( ! empty( $atts['columns'] ) ) {
			$columns = $atts['columns'];
		} else {
			$columns = isset( $porto_settings['product-upsells-cols'] ) ? $porto_settings['product-upsells-cols'] : $porto_settings['product-cols'];
		}
		if ( ! $columns ) {
			$columns = 4;
		}
		$args = array(
			'posts_per_page' => empty( $atts['count'] ) ? $porto_settings['product-upsells-count'] : $atts['count'],
			'columns'        => $columns,
	  		'orderby'        => empty( $atts['orderby'] ) ? 'rand' : $atts['orderby'], // @codingStandardsIgnoreLine.
		);

		$args     = apply_filters( 'porto_woocommerce_upsell_display_args', $args );
		$str_atts = 'ids="' . esc_attr( implode( ',', $upsells ) ) . '" count="' . intval( $args['posts_per_page'] ) . '" columns="' . intval( $args['columns'] ) . '" orderby="' . esc_attr( $args['orderby'] ) . '" pagination="1" navigation="" dots_pos="show-dots-title-right"';
		if ( is_array( $atts ) ) {
			foreach ( $atts as $key => $val ) {
				if ( in_array( $key, array( 'count', 'columns', 'orderby' ) ) || 0 === strpos( $key, '_' ) ) {
					continue;
				}
				$str_atts .= ' ' . esc_html( $key ) . '="' . esc_attr( $val ) . '"';
			}
		}
		if ( empty( $atts['view'] ) ) {
			$str_atts .= ' view="products-slider"';
		}

		ob_start();

		echo '<div class="upsells products">';
		echo '<h2 class="slider-title"><span class="inline-title">' . esc_html__( 'You may also like&hellip;', 'woocommerce' ) . '</span><span class="line"></span></h2>';
		echo do_shortcode( '[porto_products ' . $str_atts . ']' );
		echo '</div>';

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_related( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}
		global $product, $porto_settings;
		$related = wc_get_related_products( $product->get_id(), $porto_settings['product-related-count'] );
		if ( sizeof( $related ) === 0 || ! $porto_settings['product-related'] ) {
			return;
		}
		if ( in_array( $product->get_id(), $related ) ) {
			$related = array_diff( $related, array( $product->get_id() ) );
		}

		if ( ! empty( $atts['columns'] ) ) {
			$columns = $atts['columns'];
		} else {
			$columns = isset( $porto_settings['product-related-cols'] ) ? $porto_settings['product-related-cols'] : $porto_settings['product-cols'];
		}
		if ( ! $columns ) {
			$columns = 4;
		}
		$args = array(
			'posts_per_page' => empty( $atts['count'] ) ? $porto_settings['product-related-count'] : $atts['count'],
			'columns'        => $columns,
	  'orderby'        => empty( $atts['orderby'] ) ? 'rand' : $atts['orderby'], // @codingStandardsIgnoreLine.
		);

		$args     = apply_filters( 'woocommerce_related_products_args', $args );
		$str_atts = 'ids="' . esc_attr( implode( ',', $related ) ) . '" count="' . intval( $args['posts_per_page'] ) . '" columns="' . intval( $args['columns'] ) . '" orderby="' . esc_attr( $args['orderby'] ) . '" pagination="1" navigation="" dots_pos="show-dots-title-right"';
		if ( is_array( $atts ) ) {
			foreach ( $atts as $key => $val ) {
				if ( in_array( $key, array( 'count', 'columns', 'orderby' ) ) || 0 === strpos( $key, '_' ) ) {
					continue;
				}
				$str_atts .= ' ' . esc_html( $key ) . '="' . esc_attr( $val ) . '"';
			}
		}
		if ( empty( $atts['view'] ) ) {
			$str_atts .= ' view="products-slider"';
		}

		ob_start();

		echo '<div class="related products">';
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );
		if ( $heading ) {
			echo '<h2 class="slider-title">' . esc_html( $heading ) . '</h2>';
		}
		echo do_shortcode( '[porto_products ' . $str_atts . ']' );
		echo '</div>';

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	public function shortcode_single_product_next_prev_nav( $atts ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() ) {
			return null;
		}

		ob_start();
		add_filter( 'porto_is_product', '__return_true' );
		porto_woocommerce_product_nav();

		$this->reset_global_product_variable();

		return ob_get_clean();
	}

	function load_custom_product_shortcodes() {
		if ( ! $this->display_product_page_elements ) {
			$this->display_product_page_elements = PortoBuilders::check_load_wpb_elements( 'product' );
		}

		if ( ! $this->display_product_page_elements ) {
			return;
		}

		$order_by_values  = porto_vc_woo_order_by();
		$order_way_values = porto_vc_woo_order_way();
		$custom_class     = porto_vc_custom_class();
		$products_args    = array(
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'View mode', 'porto-functionality' ),
				'param_name'  => 'view',
				'value'       => porto_sh_commons( 'products_view_mode' ),
				'std'         => 'products-slider',
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
					'1'                                    => '1',
					'2'                                    => '2',
					'3'                                    => '3',
				),
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Pagination Style', 'porto-functionality' ),
				'param_name' => 'pagination_style',
				'dependency' => array(
					'element' => 'view',
					'value'   => array( 'list', 'grid', 'divider' ),
				),
				'std'        => '',
				'value'      => array(
					__( 'No pagination', 'porto-functionality' ) => '',
					__( 'Default' )   => 'default',
					__( 'Load more' ) => 'load_more',
				),
			),
			array(
				'type'        => 'number',
				'heading'     => __( 'Number of Products per page', 'porto-functionality' ),
				'description' => __( 'Leave blank if you use default value.', 'porto-functionality' ),
				'param_name'  => 'count',
				'admin_label' => true,
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Order by', 'js_composer' ),
				'param_name'  => 'orderby',
				'value'       => $order_by_values,
				/* translators: %s: Wordpress codex page */
				'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Order way', 'js_composer' ),
				'param_name'  => 'order',
				'value'       => $order_way_values,
				/* translators: %s: Wordpress codex page */
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Product Layout', 'porto-functionality' ),
				'description' => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto-functionality' ),
				'param_name'  => 'addlinks_pos',
				'value'       => porto_sh_commons( 'products_addlinks_pos' ),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Use simple layout?', 'porto-functionality' ),
				'description' => __( 'If you check this option, it will display product title and price only.', 'porto-functionality' ),
				'param_name'  => 'use_simple',
				'std'         => 'no',
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
		);

		vc_map(
			array(
				'name'     => __( 'Product Image', 'porto-functionality' ),
				'base'     => 'porto_single_product_image',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Style', 'porto-functionality' ),
						'param_name'  => 'style',
						'value'       => array(
							__( 'Default', 'porto-functionality' ) => '',
							__( 'Extended', 'porto-functionality' ) => 'extended',
							__( 'Grid Images', 'porto-functionality' ) => 'grid',
							__( 'Thumbs on Image', 'porto-functionality' ) => 'full_width',
							__( 'List Images', 'porto-functionality' ) => 'sticky_info',
							__( 'Left Thumbs 1', 'porto-functionality' ) => 'transparent',
							__( 'Left Thumbs 2', 'porto-functionality' ) => 'centered_vertical_zoom',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Title', 'porto-functionality' ),
				'base'     => 'porto_single_product_title',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
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
				'name'                    => __( 'Product Description', 'porto-functionality' ),
				'base'                    => 'porto_single_product_description',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Rating', 'porto-functionality' ),
				'base'     => 'porto_single_product_rating',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Font Size', 'porto-functionality' ),
						'param_name'  => 'font_size',
						'admin_label' => true,
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Background Star Color', 'porto-functionality' ),
						'param_name' => 'bgcolor',
						'value'      => '',
					),
					array(
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Active Color', 'porto-functionality' ),
						'param_name' => 'color',
						'value'      => '',
					),
				),
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Hooks', 'porto-functionality' ),
				'base'     => 'porto_single_product_actions',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'action', 'porto-functionality' ),
						'param_name'  => 'action',
						'value'       => array(
							'woocommerce_before_single_product_summary' => 'woocommerce_before_single_product_summary',
							'woocommerce_single_product_summary' => 'woocommerce_single_product_summary',
							'woocommerce_after_single_product_summary' => 'woocommerce_after_single_product_summary',
							'porto_woocommerce_before_single_product_summary' => 'porto_woocommerce_before_single_product_summary',
							'porto_woocommerce_single_product_summary2' => 'porto_woocommerce_single_product_summary2',
							'woocommerce_share' => 'woocommerce_share',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Price', 'porto-functionality' ),
				'base'                    => 'porto_single_product_price',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
				'params'                  => array(
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
						'type'       => 'colorpicker',
						'class'      => '',
						'heading'    => __( 'Color', 'porto-functionality' ),
						'param_name' => 'color',
						'value'      => '',
					),
				),
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Excerpt', 'porto-functionality' ),
				'base'     => 'porto_single_product_excerpt',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
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
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Add To Cart', 'porto-functionality' ),
				'base'                    => 'porto_single_product_add_to_cart',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Product Meta', 'porto-functionality' ),
				'base'                    => 'porto_single_product_meta',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
		vc_map(
			array(
				'name'     => __( 'Product Tabs', 'porto-functionality' ),
				'base'     => 'porto_single_product_tabs',
				'icon'     => 'fas fa-cart-arrow-down',
				'category' => __( 'Product Page', 'porto-functionality' ),
				'params'   => array(
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Style', 'porto-functionality' ),
						'param_name'  => 'style',
						'value'       => array(
							__( 'Default', 'porto-functionality' ) => '',
							__( 'Vetical', 'porto-functionality' ) => 'vertical',
						),
						'admin_label' => true,
					),
				),
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Upsells', 'porto-functionality' ),
				'base'                    => 'porto_single_product_upsell',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
				'params'                  => $products_args,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Related Products', 'porto-functionality' ),
				'base'                    => 'porto_single_product_related',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
				'params'                  => $products_args,
			)
		);
		vc_map(
			array(
				'name'                    => __( 'Prev and Next Navigation', 'porto-functionality' ),
				'base'                    => 'porto_single_product_next_prev_nav',
				'icon'                    => 'fas fa-cart-arrow-down',
				'category'                => __( 'Product Page', 'porto-functionality' ),
				'show_settings_on_create' => false,
			)
		);
	}
	public function add_shortcodes_css( $post_id, $post ) {
		if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'product' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'WPB_VC_VERSION' ) && false !== strpos( $post->post_content, '[porto_single_product_' ) ) {
			ob_start();
			$css = '';
			preg_match_all( '/' . get_shortcode_regex( array( 'porto_single_product_title', 'porto_single_product_price', 'porto_single_product_excerpt', 'porto_single_product_rating' ) ) . '/', $post->post_content, $shortcodes );
			foreach ( $shortcodes[2] as $index => $tag ) {
				$atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
				include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-' . str_replace( array( 'porto_single_product_', '_' ), array( '', '-' ), $tag ) . '.php';
			}
			$css = ob_get_clean();
			if ( $css ) {
				update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
			} else {
				delete_post_meta( $post_id, 'porto_builder_css' );
			}
		} elseif ( false !== strpos( $post->post_content, '<!-- wp:porto-single-product' ) ) { // Gutenberg editor
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
			if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-single-product/porto-sp-title', 'porto-single-product/porto-sp-price', 'porto-single-product/porto-sp-excerpt', 'porto-single-product/porto-sp-rating' ) ) ) {
				$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];
				include PORTO_BUILDERS_PATH . '/elements/product/wpb/style-' . str_replace( 'porto-single-product/porto-sp-', '', $block['blockName'] ) . '.php';
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->include_style( $block['innerBlocks'] );
			}
		}
	}

	public function elementor_custom_product_shortcodes( $self ) {
		$load_widgets = false;
		if ( is_singular( 'product' ) ) {
			$load_widgets = true;
		} elseif ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'product' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
			$load_widgets = true;
		} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
			$load_widgets = true;
		}
		if ( $load_widgets ) {
			foreach ( $this->shortcodes as $shortcode ) {
				include_once PORTO_BUILDERS_PATH . '/elements/product/elementor/' . $shortcode . '.php';
				$class_name = 'Porto_Elementor_CP_' . ucfirst( $shortcode ) . '_Widget';
				if ( class_exists( $class_name ) ) {
					$self->register_widget_type( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
				}
			}
		}
	}
	public function vc_custom_product_shortcodes( $api ) {
		$load_widgets = false;

		if ( is_singular( 'product' ) ) {
			$load_widgets = true;
		} elseif ( ! empty( $_REQUEST['post'] ) && PortoBuilders::BUILDER_SLUG == get_post_type( $_REQUEST['post'] ) && 'product' == get_post_meta( (int) $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
			$load_widgets = true;
		}
		if ( $load_widgets ) {
			$base_url     = rtrim( plugins_url( basename( dirname( PORTO_FUNC_FILE ) ) ), '\\/' ) . '/builders/elements/product';
			$elements_api = $api->elements;
			foreach ( $this->shortcodes as $shortcode ) {
				$manifest_path = __DIR__ . '/vc/' . $shortcode . '/manifest.json';
				$element_url   = $base_url . '/vc/' . $shortcode;
				$elements_api->add( $manifest_path, $element_url );
			}
		}
	}
}

PortoCustomProduct::get_instance();
