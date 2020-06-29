<?php

/**
 * Class VI_WBOOSTSALES_Frontend_Upsells
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WBOOSTSALES_Frontend_Upsells {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WBOOSTSALES_Data();
		/*Add to cart template*/
		add_action( 'woocommerce_boost_sales_single_product_summary', array( $this, 'add_to_cart_template' ) );
		add_action( 'woocommerce_boost_sales_single_product_summary_mobile', array(
			$this,
			'add_to_cart_template_mobile'
		) );
		add_action( 'woocommerce_boost_sales_simple_add_to_cart', array(
			$this,
			'woocommerce_boost_sales_simple_add_to_cart'
		) );
		add_action( 'woocommerce_boost_sales_variable_add_to_cart', array(
			$this,
			'woocommerce_boost_sales_variable_add_to_cart'
		) );
		add_action( 'woocommerce_boost_sales_single_variation', array(
			$this,
			'woocommerce_boost_sales_single_variation'
		) );

		add_action( 'woocommerce_boost_sales_simple_add_to_cart_mobile', array(
			$this,
			'woocommerce_boost_sales_simple_add_to_cart_mobile'
		) );
		add_action( 'woocommerce_boost_sales_variable_add_to_cart_mobile', array(
			$this,
			'woocommerce_boost_sales_variable_add_to_cart_mobile'
		) );
		add_action( 'woocommerce_boost_sales_single_variation_mobile', array(
			$this,
			'woocommerce_boost_sales_single_variation_mobile'
		) );


		add_action( 'woocommerce_boost_sales_single_product_summary', array( $this, 'product_link' ) );
		add_action( 'woocommerce_boost_sales_single_product_summary_mobile', array( $this, 'product_link' ) );
		/**
		 * woocommerce_before_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		add_action( 'woocommerce_boost_sales_before_shop_loop_item_title', array(
			$this,
			'woocommerce_template_loop_product_thumbnail'
		) );
		/**
		 * woocommerce_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		add_action( 'woocommerce_boost_sales_shop_loop_item_title', array(
			$this,
			'woocommerce_template_loop_product_title'
		) );

		/**
		 * woocommerce_after_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		add_action( 'woocommerce_boost_sales_after_shop_loop_item_title', array( $this, 'product_rate' ), 5 );
		add_action( 'woocommerce_boost_sales_after_shop_loop_item_title', array( $this, 'product_price' ), 10 );

	}

	/**
	 * @param $product WC_Product
	 */
	public function woocommerce_template_loop_product_title( $product ) {
		echo '<span class="woocommerce-loop-product__title">' . $product->get_title() . '</span>';
	}

	/**
	 * @param $product WC_Product
	 */
	public function woocommerce_template_loop_product_thumbnail( $product ) {
		echo VI_WBOOSTSALES_Upsells::get_product_image( $product ); // WPCS: XSS ok.
	}

	/**
	 * @param $product
	 */
	public function woocommerce_boost_sales_single_variation( $product ) {
		echo '<div class="woocommerce-variation single_variation"></div>';
		wbs_get_template( 'single-product/add-to-cart/variation-add-to-cart-button.php', array( 'product' => $product ), '', VI_WBOOSTSALES_TEMPLATES );
	}

	public function woocommerce_boost_sales_single_variation_mobile( $product ) {
		echo '<div class="woocommerce-variation single_variation"></div>';
		wbs_get_template( 'single-product/add-to-cart/variation-add-to-cart-button-mobile.php', array( 'product' => $product ), '', VI_WBOOSTSALES_TEMPLATES );
	}

	/**
	 * @param $product WC_Product_Variable
	 */
	public function woocommerce_boost_sales_variable_add_to_cart( $product ) {
		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		// Load the template.
		wbs_get_template(
			'single-product/add-to-cart/variable.php', array(
			'available_variations' => $get_variations ? $product->get_available_variations() : false,
			'attributes'           => $product->get_variation_attributes(),
			'selected_attributes'  => $product->get_default_attributes(),
			'product'              => $product,
		), '', VI_WBOOSTSALES_TEMPLATES
		);
	}

	/**
	 * @param $product WC_Product_Variable
	 */
	public function woocommerce_boost_sales_variable_add_to_cart_mobile( $product ) {
		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		// Load the template.
		wbs_get_template(
			'single-product/add-to-cart/variable-mobile.php', array(
			'available_variations' => $get_variations ? $product->get_available_variations() : false,
			'attributes'           => $product->get_variation_attributes(),
			'selected_attributes'  => $product->get_default_attributes(),
			'product'              => $product,
		), '', VI_WBOOSTSALES_TEMPLATES
		);
	}

	/**
	 * @param $product
	 */
	public function woocommerce_boost_sales_simple_add_to_cart( $product ) {
		wbs_get_template( 'single-product/add-to-cart/simple.php', array( 'product' => $product ), '', VI_WBOOSTSALES_TEMPLATES );
	}

	public function woocommerce_boost_sales_simple_add_to_cart_mobile( $product ) {
		wbs_get_template( 'single-product/add-to-cart/simple-mobile.php', array( 'product' => $product ), '', VI_WBOOSTSALES_TEMPLATES );
	}

	/**
	 * @param $product WC_Product
	 */
	public function add_to_cart_template( $product ) {
		$required_addon = false;
		if ( class_exists( 'WC_Product_Addons_Helper' ) ) {
			$addons = WC_Product_Addons_Helper::get_product_addons( $product->get_id(), false, false, true );
			if ( $addons && ! empty( $addons ) ) {
				foreach ( $addons as $addon ) {
					if ( '1' == $addon['required'] ) {
						$required_addon = true;
						break;
					}
				}
			}
		}
		if ( ! $required_addon ) {
			do_action( 'woocommerce_boost_sales_' . $product->get_type() . '_add_to_cart', $product );
		} elseif ( ! $this->settings->get_option( 'hide_view_more_button' ) ) {
			?>
            <a href="<?php echo $product->get_permalink() ?>"
               class="wbs-product-link"><?php esc_html_e( 'View more', 'woocommerce-boost-sales' ) ?></a>
			<?php
		}
	}

	/**
	 * @param $product WC_Product
	 */
	public function add_to_cart_template_mobile( $product ) {
		$required_addon = false;
		if ( class_exists( 'WC_Product_Addons_Helper' ) ) {
			$addons = WC_Product_Addons_Helper::get_product_addons( $product->get_id(), false, false, true );
			if ( $addons && ! empty( $addons ) ) {
				foreach ( $addons as $addon ) {
					if ( '1' == $addon['required'] ) {
						$required_addon = true;
						break;
					}
				}
			}
		}
		if ( ! $required_addon ) {
			do_action( 'woocommerce_boost_sales_' . $product->get_type() . '_add_to_cart_mobile', $product );
		} elseif ( ! $this->settings->get_option( 'hide_view_more_button' ) ) {
			?>
            <a href="<?php echo $product->get_permalink() ?>"
               class="wbs-product-link"><?php esc_html_e( 'View more', 'woocommerce-boost-sales' ) ?></a>
			<?php
		}
	}

	/**
	 * @param $product WC_Product
	 */
	public function product_price( $product ) {
		if ( $price_html = $product->get_price_html() ) {
			?>
            <span class="price"><?php echo $price_html; ?></span>
			<?php
		}
	}

	/**
	 * @param $product WC_Product
	 */
	public function product_rate( $product ) {
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			return;
		}
		$rating = $product->get_average_rating();
		if ( $rating > 0 ) {
			echo wc_get_rating_html( $rating );
		}
	}

	/**
	 * @param $product WC_Product
	 */
	public function product_link( $product ) {
		if ( ! $this->settings->get_option( 'hide_view_more_button' ) ) {
			?>
            <a href="<?php echo $product->get_permalink() ?>"
               class="wbs-product-link"><?php esc_html_e( 'View more', 'woocommerce-boost-sales' ) ?></a>
			<?php
		}
	}

	/**
	 * @param $categories
	 *
	 * @return array
	 */
	public static function get_products_from_categories( $categories ) {
		$products = array();
		if ( is_array( $categories ) && count( $categories ) ) {
			$args      = array(
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
						'terms'    => array(
							'simple',
							'variable',
							'external',
							'subscription',
							'variable-subscription',
							'member',
							'woosb',
							'redq_rental',
						),
						'operator' => 'IN'
					),
				),
			);
			$the_query = new WP_Query( $args );

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

}