<?php

/**
 * Class VI_WBOOSTSALES_Frontend_Notify
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Frontend_Archive_Upsells {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
		if ( $this->settings->enable() && $this->settings->get_option( 'enable_upsell' ) ) {
			add_action( 'wp_footer', array( $this, 'init_boost_sales' ) );
			add_action( 'wp_ajax_wbs_get_product', array( $this, 'product_html' ) );
			add_action( 'wp_ajax_nopriv_wbs_get_product', array( $this, 'product_html' ) );
		}
	}

	/**
	 * Show HTML on front end
	 */
	public function product_html() {
		$enable                  = $this->settings->get_option( 'enable' );
		$upsell_exclude_products = $this->settings->get_option( 'upsell_exclude_products' );
		$product_id              = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
		$upsells_html            = '';
		if ( $enable && $product_id && ! in_array( $product_id, $upsell_exclude_products ) ) {
			$upsells_html = $this->show_product( $product_id );
		}
		$discount_bar = new VI_WBOOSTSALES_Discount_Bar();
		wp_send_json( array(
			'upsells_html'      => $upsells_html,
			'discount_bar_html' => $discount_bar->show_html(),
		) );
	}


	/**
	 * Show HTML code
	 */
	public function init_boost_sales() {
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		if ( is_cart() && $this->settings->get_option( 'hide_on_cart_page' ) ) {
			return;
		}
		if ( is_checkout() && $this->settings->get_option( 'hide_on_checkout_page' ) ) {
			return;
		}
		if ( ! is_single() ) {
			echo $this->show_product();
		}
	}

	/**
	 * @param $product WC_Product
	 *
	 * @return array
	 */
	protected function get_product_in_category( $product ) {
		$only_sub_category         = $this->settings->get_option( 'show_with_subcategory' );
		$exclude_categories        = $this->settings->get_option( 'exclude_categories' );
		$upsell_exclude_categories = $this->settings->get_option( 'upsell_exclude_categories' );
		$products                  = array();
		$category_ids              = $product->get_category_ids();
		if ( count( array_intersect( $category_ids, $upsell_exclude_categories ) ) ) {
			return $products;
		}
		if ( count( $category_ids ) ) {
			$categories = $category_ids;
			if ( $only_sub_category ) {
				$count      = count( get_ancestors( $category_ids[0], 'product_cat', 'taxonomy' ) );
				$cates_temp = array( $category_ids[0] );
				foreach ( $category_ids as $cate ) {
					$parents = get_ancestors( $cate, 'product_cat', 'taxonomy' );
					if ( $count < count( $parents ) ) {
						$count      = count( $parents );
						$cates_temp = array( $cate );
					} elseif ( $count == count( $parents ) ) {
						$cates_temp[] = $cate;
					}
				};
				$categories = $cates_temp;
			}
			$categories = array_diff( $categories, $exclude_categories );
			$u_args     = array(
				'post_status'    => 'publish',
				'post_type'      => 'product',
				'posts_per_page' => 50,
				'tax_query'      => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'ID',
						'terms'    => $categories,
						'operator' => 'IN'
					),
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'wbs_bundle',
						'operator' => 'NOT IN'
					),
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array(
							'simple',
							'variable',
							'external',
							'subscription',
							'variable-subscription',
							'member'
						),
						'operator' => 'IN'
					),
				),
			);
			switch ( $this->settings->get_option( 'sort_product' ) ) {
				case 1:
					$u_args['orderby'] = 'title';
					$u_args['order']   = 'desc';
					break;
				case 2;
					$u_args['orderby']  = 'meta_value_num';
					$u_args['meta_key'] = '_price';
					$u_args['order']    = 'desc';
					break;
				case 3;
					$u_args['orderby']  = 'meta_value_num';
					$u_args['meta_key'] = '_price';
					$u_args['order']    = 'asc';
					break;
				case 4;
					$u_args['orderby'] = 'rand';
					break;
				case 5;
					$u_args['orderby']  = 'meta_value_num';
					$u_args['meta_key'] = 'total_sales';
					$u_args['order']    = 'desc';
					break;
				default;
					$u_args['orderby'] = 'title';
					$u_args['order']   = 'asc';
			}
			$the_query = new WP_Query( $u_args );

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$products[] = get_the_ID();
				}
			}
			wp_reset_postdata();
		}

		return $products;
	}

	/**
	 * @param null $product_id
	 *
	 * @return false|string
	 */
	protected function show_product( $product_id = null ) {
		if ( ! $product_id ) {
			ob_start();
			?>
            <div id="wbs-content-upsells"
                 class="woocommerce-boost-sales wbs-content-up-sell wbs-archive-page" style="display: none;">
            </div>
			<?php
			return ob_get_clean();
		} else {
			$item_limit         = $this->settings->get_option( 'limit' );
			$show_with_category = $this->settings->get_option( 'show_with_category' );
			$product            = wc_get_product( $product_id );
			if ( ! $product ) {
				return '';
			}
			/*Get Recently Viewed Products*/
			if ( $this->settings->get_option( 'show_recently_viewed_products' ) ) {
				$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array(); // @codingStandardsIgnoreLine
				$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
			} else {
				$viewed_products = array();
			}
			/*Get product in cart*/
			$products_added = array();
			if ( $this->settings->get_option( 'hide_products_added' ) ) {
				$cart_items = WC()->cart->get_cart();
				if ( is_array( $cart_items ) && count( $cart_items ) ) {
					foreach ( $cart_items as $cart_item ) {
						$products_added[] = $cart_item['product_id'];
					}
				}
			}
			$viewed_products = array_diff( $viewed_products, $products_added );
			if ( $this->settings->get_option( 'show_recently_viewed_products' ) && count( $viewed_products ) ) {
				$upsells = array_values( $viewed_products );
				if ( $item_limit ) {
					$upsells = array_slice( $upsells, 0, $item_limit );
				}
			} elseif ( $show_with_category ) {
				if ( get_transient( 'vi_woocommerce_boost_sales_product_in_category_ids_' . $product_id ) ) {
					$upsells = get_transient( 'vi_woocommerce_boost_sales_product_in_category_ids_' . $product_id );
				} else {
					$upsells = $this->get_product_in_category( $product );
					set_transient( 'vi_woocommerce_boost_sales_product_in_category_ids_' . $product_id, $upsells, DAY_IN_SECONDS );
				}
				$exclude_product   = $this->settings->get_option( 'exclude_product' );
				$exclude_product[] = $product_id;
				$exclude_product   = array_merge( $exclude_product, $products_added );
				$upsells           = array_diff( $upsells, $exclude_product );
				if ( $item_limit ) {
					$upsells = array_slice( $upsells, 0, $item_limit );
				}
			} else {
				$upsells = get_post_meta( $product_id, '_wbs_upsells', true );
				if ( ! is_array( $upsells ) ) {
					$upsells = array();
				}
				$upsells_categories = get_post_meta( $product_id, '_wbs_upsells_categories', true );
				$upsells            = array_merge( $upsells, VI_WBOOSTSALES_Frontend_Upsells::get_products_from_categories( $upsells_categories ) );
				if ( count( array_filter( $upsells ) ) ) {
					$upsells = array_diff( $upsells, array( $product_id ) );
					$upsells = array_diff( $upsells, $products_added );
					$upsells = array_unique( $upsells );
				}
			}

			$quantity = filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT );

			$obj_upsell = new VI_WBOOSTSALES_Upsells( $product->get_id(), $quantity, $upsells, $product_id );
			ob_start();
			echo $obj_upsell->show_html();

			return ob_get_clean();
		}
	}
}