<?php
/**
 * UAEL Base Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Woocommerce\TemplateBlocks;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Base
 */
abstract class Skin_Style {


	/**
	 * Query object
	 *
	 * @since 1.5.0
	 * @var object $query
	 */
	public static $query;

	/**
	 * Query object
	 *
	 * @since 1.5.0
	 * @var object $query_obj
	 */
	public static $query_obj;

	/**
	 * Settings
	 *
	 * @since 1.5.0
	 * @var object $settings
	 */
	public static $settings;

	/**
	 * Skin
	 *
	 * @since 1.5.0
	 * @var object $skin
	 */
	public static $skin;

	/**
	 * Node ID of element
	 *
	 * @since 1.5.0
	 * @var object $node_id
	 */
	public static $node_id;

	/**
	 * Rendered Settings
	 *
	 * @since 1.5.0
	 * @var object $_render_attributes
	 */
	public $_render_attributes; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @param string $located location.
	 * @param string $template_name template name.
	 * @param array  $args arguments.
	 * @param string $template_path path.
	 * @param string $default_path default path.
	 * @return string template location
	 */
	public function woo_pagination_template( $located, $template_name, $args, $template_path, $default_path ) {

		if ( 'loop/pagination.php' === $template_name ) {
			$located = UAEL_MODULES_DIR . 'woocommerce/templates/loop/pagination.php';
		}

		return $located;
	}

	/**
	 * Change pagination arguments based on settings.
	 *
	 * @since 0.0.1
	 * @access protected
	 * @param array $args pagination args.
	 * @return array
	 */
	public function woo_pagination_options( $args ) {

		$settings = self::$settings;

		$pagination_arrow = false;

		if ( 'numbers_arrow' === $settings['pagination_type'] ) {
			$pagination_arrow = true;
		}

		$args['prev_next'] = $pagination_arrow;

		if ( '' !== $settings['pagination_prev_label'] ) {
			$args['prev_text'] = $settings['pagination_prev_label'];
		}

		if ( '' !== $settings['pagination_next_label'] ) {
			$args['next_text'] = $settings['pagination_next_label'];
		}

		return $args;
	}

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function set_slider_attr() {

		$settings = self::$settings;

		if ( 'slider' !== $settings['products_layout_type'] ) {
			return;
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 4,
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
			'prevArrow'      => '<button type="button" data-role="none" class="slick-prev slick-arrow fa fa-angle-left" aria-label="Previous" role="button"></button>',
			'nextArrow'      => '<button type="button" data-role="none" class="slick-next slick-arrow fa fa-angle-right" aria-label="Next" role="button"></button>',
		);

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {

			$slick_options['responsive'] = array();

			if ( ! empty( $settings['slides_to_show_tablet'] ) ) {

				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( ! empty( $settings['slides_to_scroll_tablet'] ) ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( ! empty( $settings['slides_to_show_mobile'] ) ) {

				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( ! empty( $settings['slides_to_scroll_mobile'] ) ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'data-woo_slider' => wp_json_encode( $slick_options ),
			)
		);
	}

	/**
	 * Render Query.
	 *
	 * @since 1.1.0
	 */
	public function render_query() {

		$this->query_posts();
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function query_posts() {

		$settings = self::$settings;

		if ( 'main' === $settings['query_type'] ) {

			global $wp_query;

			$main_query = clone $wp_query;

			self::$query = $main_query;

		} elseif ( 'related' === $settings['query_type'] ) {

			if ( is_product() ) {

				global $product;

				$product_id                  = $product->get_id();
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__not_in'   => array(),
				);

				if ( 'grid' === $settings['products_layout_type'] ) {

					if ( $settings['products_per_page'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_per_page'];
					}

					if ( '' !== $settings['pagination_type'] ) {

						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

						if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-product-nonce' ) ) {
							if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
								$paged = sanitize_text_field( $_POST['page_number'] );
							}
						}

						$query_args['paged'] = $paged;
					}
				} else {

					if ( $settings['slider_products_per_page'] > 0 ) {
						$query_args['posts_per_page'] = $settings['slider_products_per_page'];
					}
				}

				// Get current post categories and pass to filter.
				$product_cat = array();

				$product_categories = wp_get_post_terms( $product_id, 'product_cat' );

				if ( ! empty( $product_categories ) ) {

					foreach ( $product_categories as $key => $category ) {

						$product_cat[] = $category->slug;
					}
				}

				if ( ! empty( $product_cat ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_cat,
						'operator' => 'IN',
					);
				}

				// Exclude current product.
				$query_args['post__not_in'][] = $product_id;

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				// Default ordering args.
				$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

				$query_args['orderby'] = $ordering_args['orderby'];
				$query_args['order']   = $ordering_args['order'];

				$query_args = apply_filters( 'uael_woo_product_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$query_args = apply_filters( 'uael_woo_product_query_args', $query_args, $settings );

				self::$query = new \WP_Query( $query_args );
			}
		} else {

			global $post;
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$query_args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__not_in'   => array(),
			);

			if ( 'grid' === $settings['products_layout_type'] ) {

				if ( $settings['products_per_page'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_per_page'];
				}

				if ( '' !== $settings['pagination_type'] ) {

					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

					if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-product-nonce' ) ) {

						if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
							$paged = sanitize_text_field( $_POST['page_number'] );
						}
					}

					$query_args['paged'] = $paged;
				}
			} else {

				if ( $settings['slider_products_per_page'] > 0 ) {
					$query_args['posts_per_page'] = $settings['slider_products_per_page'];
				}
			}

			if ( 'price' === $settings['orderby'] || 'popularity' === $settings['orderby'] || 'rating' === $settings['orderby'] ) {
				if ( 'price' === $settings['orderby'] ) {
					$query_args['meta_key'] = '_price';
				} elseif ( 'popularity' === $settings['orderby'] ) {
					$query_args['meta_key'] = 'total_sales';
				} elseif ( 'rating' === $settings['orderby'] ) {
					$query_args['meta_key'] = '_wc_average_rating';
				}

				$query_args['orderby'] = 'meta_value_num';
				$query_args['order']   = $settings['order'];
			} else {
				$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

				$query_args['orderby'] = $ordering_args['orderby'];
				$query_args['order']   = $ordering_args['order'];
			}

			if ( 'sale' === $settings['filter_by'] ) {

				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			} elseif ( 'featured' === $settings['filter_by'] ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				);
			}

			if ( 'custom' === $settings['query_type'] ) {

				if ( ! empty( $settings['category_filter'] ) ) {

					$cat_operator = $settings['category_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $settings['category_filter'],
						'operator' => $cat_operator,
					);
				}

				if ( ! empty( $settings['tag_filter'] ) ) {

					$tag_operator = $settings['tag_filter_rule'];

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_tag',
						'field'    => 'slug',
						'terms'    => $settings['tag_filter'],
						'operator' => $tag_operator,
					);
				}

				if ( 0 < $settings['offset'] ) {

					/**
					 * Offset break the pagination. Using WordPress's work around
					 *
					 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
					 */
					$query_args['offset_to_fix'] = $settings['offset'];
				}
			}

			if ( 'manual' === $settings['query_type'] ) {

				$manual_ids = $settings['query_manual_ids'];

				$query_args['post__in'] = $manual_ids;
			}

			if ( 'manual' !== $settings['query_type'] && 'main' !== $settings['query_type'] ) {

				if ( '' !== $settings['query_exclude_ids'] ) {

					$exclude_ids = $settings['query_exclude_ids'];

					$query_args['post__not_in'] = $exclude_ids;

					// Exclude from on sale products.
					if ( 'sale' === $settings['filter_by'] ) {

						$query_args['post__in'] = array_diff( $query_args['post__in'], $query_args['post__not_in'] );
					}
				}

				if ( 'yes' === $settings['query_exclude_current'] ) {

					$query_args['post__not_in'][] = $post->ID;
				}
			}

			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				);
			}

			if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
					'operator' => 'NOT IN',
				);
			}

			$query_args = apply_filters( 'uael_woo_product_query_args', $query_args, $settings );

			self::$query = new \WP_Query( $query_args );
		}
	}

	/**
	 * Render loop required arguments.
	 *
	 * @since 1.1.0
	 */
	public function render_loop_args() {

		$query = $this->get_query();

		global $woocommerce_loop;

		$settings = self::$settings;

		if ( 'grid' === $settings['products_layout_type'] ) {
			$woocommerce_loop['columns'] = (int) $settings['products_columns'];

			if ( 'main' !== $settings['query_type'] ) {
				if ( 0 < $settings['products_per_page'] && '' !== $settings['pagination_type'] ) {
					/* Pagination */
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

					if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-product-nonce' ) ) {
						if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
							$paged = sanitize_text_field( $_POST['page_number'] );
						}
					}

					$woocommerce_loop['paged']        = $paged;
					$woocommerce_loop['total']        = $query->found_posts;
					$woocommerce_loop['post_count']   = $query->post_count;
					$woocommerce_loop['per_page']     = $settings['products_per_page'];
					$woocommerce_loop['total_pages']  = ceil( $query->found_posts / $settings['products_per_page'] );
					$woocommerce_loop['current_page'] = $paged;
				}
			}

			$this->add_render_attribute(
				'inner',
				array(
					'class' => array(
						' columns-' . $woocommerce_loop['columns'],
					),
				)
			);
		} else {
			if ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) ) {

				$this->add_render_attribute(
					'inner',
					array(
						'class' => array(
							'uael-slick-dotted',
						),
					)
				);
			}
		}
	}

	/**
	 * Pagination Structure.
	 *
	 * @since 1.1.0
	 */
	public function render_pagination_structure() {

		$settings = self::$settings;

		if ( '' !== $settings['pagination_type'] ) {
			add_filter( 'wc_get_template', array( $this, 'woo_pagination_template' ), 10, 5 );
			add_filter( 'uael_woocommerce_pagination_args', array( $this, 'woo_pagination_options' ) );
			woocommerce_pagination();
			remove_filter( 'uael_woocommerce_pagination_args', array( $this, 'woo_pagination_options' ) );
			remove_filter( 'wc_get_template', array( $this, 'woo_pagination_template' ), 10, 5 );
		}
	}

	/**
	 * Render wrapper start.
	 *
	 * @since 1.1.0
	 */
	public function render_wrapper_start() {

		$settings = self::$settings;

		$skin_slug = str_replace( '_', '-', self::$skin );

		$page_id = 0;

		if ( null !== \Elementor\Plugin::$instance->documents->get_current() ) {
			$page_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		$this->set_slider_attr();

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'     => array(
					'uael-woocommerce',
					'uael-woo-products-' . $settings['products_layout_type'],
					'uael-woo-skin-' . $skin_slug,
					'uael-woo-query-' . $settings['query_type'],
				),
				'data-page' => $page_id,
				'data-skin' => self::$skin,
			)
		);

		echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ) . '>';
	}

	/**
	 * Render wrapper end.
	 *
	 * @since 1.1.0
	 */
	public function render_wrapper_end() {
		echo '</div>';
	}

	/**
	 * Render inner container start.
	 *
	 * @since 1.1.0
	 */
	public function render_inner_start() {

		$settings = self::$settings;

		$product_column_tablet = isset( $settings['products_columns_tablet'] ) ? $settings['products_columns_tablet'] : '';
		$product_column_mobile = isset( $settings['products_columns_mobile'] ) ? $settings['products_columns_mobile'] : '';

		$this->add_render_attribute(
			'inner',
			array(
				'class' => array(
					'uael-woo-products-inner',
					'uael-woo-product__column-' . $settings['products_columns'],
					'uael-woo-product__column-tablet-' . $product_column_tablet,
					'uael-woo-product__column-mobile-' . $product_column_mobile,
				),
			)
		);

		if ( '' !== $settings['products_hover_style'] ) {
			$this->add_render_attribute(
				'inner',
				array(
					'class' => array(
						'uael-woo-product__hover-' . $settings['products_hover_style'],
					),
				)
			);
		}

		echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'inner' ) ) . '>';
	}

	/**
	 * Render inner container end.
	 *
	 * @since 1.1.0
	 */
	public function render_inner_end() {
		echo '</div>';
	}

	/**
	 * Render woo loop start.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_start() {
		do_action( 'uael_before_product_loop_start' );
		woocommerce_product_loop_start();
	}

	/**
	 * Render woo loop.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop() {

		$query = $this->get_query();

		while ( $query->have_posts() ) :
			$query->the_post();
			$this->render_woo_loop_template();
		endwhile;
	}

	/**
	 * Render woo default template.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_template() {

		$settings = self::$settings;

		include UAEL_MODULES_DIR . 'woocommerce/templates/content-product-default.php';
	}
	/**
	 * Render woo loop end.
	 *
	 * @since 1.1.0
	 */
	public function render_woo_loop_end() {
		woocommerce_product_loop_end();
	}

	/**
	 * Render reset loop.
	 *
	 * @since 1.1.0
	 */
	public function render_reset_loop() {

		woocommerce_reset_loop();

		wp_reset_postdata();
	}

	/**
	 * Quick View.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function quick_view_modal() {

		$quick_view_type = $this->get_instance_value( 'quick_view_type' );

		if ( '' !== $quick_view_type ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'flexslider' );

			$widget_id = self::$node_id;

			include UAEL_MODULES_DIR . 'woocommerce/templates/quick-view-modal.php';
		}
	}

	/**
	 * Register Get Query.
	 *
	 * @since 0.0.1
	 * @access protected
	 */
	public function get_query() {
		return self::$query;
	}

	/**
	 * Get empty products found message.
	 *
	 * Returns the no products found message HTML.
	 *
	 * @since 1.10.0
	 * @access public
	 */
	public function render_empty() {
		$settings = self::$settings;
		?>
		<div class="uael-wooproducts-empty">
			<p><?php echo wp_kses_post( $settings['no_results_text'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $style Skin ID.
	 * @param array  $settings Settings Object.
	 * @param string $node_id Node ID.
	 * @since 1.5.0
	 * @access public
	 */
	public function render( $style, $settings, $node_id ) {
		self::$settings = $settings;
		self::$skin     = str_replace( '-', '_', $style );
		self::$node_id  = $node_id;

		$this->render_query();

		$query = self::$query;

		if ( ! $query->have_posts() ) {
			$this->render_empty();
			return;
		}

		$this->render_loop_args();
		$this->render_wrapper_start();
			$this->render_inner_start();
				$this->render_woo_loop_start();
					$this->render_woo_loop();
				$this->render_woo_loop_end();
				$this->render_pagination_structure();
				$this->render_reset_loop();
			$this->render_inner_end();
		$this->render_wrapper_end();

		$this->quick_view_modal();
	}

	/**
	 * Render settings array for selected skin
	 *
	 * @since 1.5.0
	 * @param string $control_base_id Skin ID.
	 * @access public
	 */
	public function get_instance_value( $control_base_id ) {
		if ( isset( self::$settings[ self::$skin . '_' . $control_base_id ] ) ) {
			return self::$settings[ self::$skin . '_' . $control_base_id ];
		} else {
			return null;
		}
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->_render_attributes[ $element ][ $key ] ) ) {
			$this->_render_attributes[ $element ][ $key ] = array();
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->_render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->_render_attributes[ $element ][ $key ] = array_merge( $this->_render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {
		if ( empty( $this->_render_attributes[ $element ] ) ) {
			return '';
		}

		$render_attributes = $this->_render_attributes[ $element ];

		$attributes = array();

		foreach ( $render_attributes as $attribute_key => $attribute_values ) {
			$attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * Render post HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 1.5.0
	 * @access public
	 */
	public function inner_render( $style_id, $widget ) {

		ob_start();

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-product-nonce' ) ) {
			$category = ( isset( $_POST['category'] ) ) ? sanitize_text_field( $_POST['category'] ) : '';
		}

		self::$settings = $widget->get_settings();
		self::$skin     = $style_id;
		$this->render_query();
		$query    = self::$query;
		$settings = self::$settings;

		$this->render_loop_args();

		$this->render_woo_loop_start();
			$this->render_woo_loop();
		$this->render_woo_loop_end();

		return ob_get_clean();
	}

	/**
	 * Render post pagination HTML via AJAX call.
	 *
	 * @param array|string $style_id  The style ID.
	 * @param array|string $widget    Widget object.
	 * @since 1.5.0
	 * @access public
	 */
	public function page_render( $style_id, $widget ) {

		ob_start();

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'uael-product-nonce' ) ) {
			$category = ( isset( $_POST['category'] ) ) ? sanitize_text_field( $_POST['category'] ) : '';
		}

		self::$settings = $widget->get_settings();
		self::$skin     = $style_id;
		$this->render_query();
		$query       = self::$query;
		$settings    = self::$settings;
		$is_featured = false;

		$this->render_pagination_structure();

		return ob_get_clean();
	}
}
