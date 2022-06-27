<?php

class WC_Dynamic_Pricing_Counter {

	public $taxonomy_counts = array();
	public $product_counts = array();
	public $variation_counts = array();
	public $category_counts = array();
	private $categories_in_cart = array();
	private $taxonomies_in_cart = array();
	private $tracked_cart_items = array();

	/**
	 * @var WC_Dynamic_Pricing_Counter
	 */
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Counter();
		}
	}

	/**
	 * @return WC_Dynamic_Pricing_Counter
	 */
	public static function instance() {
		self::register();

		return self::instance();
	}

	public function __construct() {
		add_action( 'woocommerce_before_calculate_totals', array( &$this, 'reset_counter' ), 80, 1 );

		add_action( 'woocommerce_cart_emptied', array( $this, 'empty_counter' ), 0 );

		//Share Cart
		// Ajax - Send Cart
		add_action( 'wp_ajax_send_cart_email_ajax', array( $this, 'empty_counter' ), 0 );
		add_action( 'wp_ajax_nopriv_send_cart_email_ajax', array( $this, 'empty_counter' ), 0 );


		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 100, 3 );

		//Add action to reset counters when product added to cart
		add_action( 'woocommerce_add_to_cart', array( $this, 'on_add_to_cart' ), 100, 6 );


		add_action( 'woocommerce_after_cart_item_quantity_update', [$this, 'on_after_cart_item_quantity_update'], 10, 4 );
	}

	public function empty_counter() {
		$this->taxonomy_counts    = array();
		$this->taxonomies_in_cart = array();
		$this->product_counts     = array();
		$this->variation_counts   = array();
		$this->category_counts    = array();
		$this->categories_in_cart = array();
		$this->tracked_cart_items = array();
	}

	public function reset_counter( $cart ) {

		$this->product_counts     = array();
		$this->variation_counts   = array();
		$this->category_counts    = array();
		$this->categories_in_cart = array();
		$this->taxonomies_in_cart = array();
		$this->taxonomy_counts    = array();
		$this->tracked_cart_items = array();

		if ( sizeof( $cart->cart_contents ) > 0 ) {
			foreach ( $cart->cart_contents as $cart_item_key => $values ) {

				if ( isset( $this->tracked_cart_items[ $cart_item_key ] ) ) {
					continue;
				} else {
					$this->tracked_cart_items[ $cart_item_key ] = true;
				}

				$quantity = isset( $values['quantity'] ) ? (int) $values['quantity'] : 0;

				$product_id   = $values['product_id'];
				$variation_id = isset( $values['variation_id'] ) ? $values['variation_id'] : false;

				//Store product counts
				$this->product_counts[ $product_id ] = isset( $this->product_counts[ $product_id ] ) ? $this->product_counts[ $product_id ] + $quantity : $quantity;

				//Gather product variation id counts
				if ( ! empty( $variation_id ) ) {
					$this->variation_counts[ $variation_id ] = isset( $this->variation_counts[ $variation_id ] ) ?
						$this->variation_counts[ $variation_id ] + $quantity : $quantity;
				}

				//Gather product category counts
				$product            = wc_get_product( $product_id );
				$product_categories = WC_Dynamic_Pricing_Compatibility::get_product_category_ids( $product );

				foreach ( $product_categories as $category ) {
					$this->category_counts[ $category ] = isset( $this->category_counts[ $category ] ) ?
						$this->category_counts[ $category ] + $quantity : $quantity;

					$this->categories_in_cart[] = $category;
				}

				$additional_taxonomies = apply_filters( 'wc_dynamic_pricing_get_discount_taxonomies', array( 'product_brand' ) );
				//Gather additional taxonomy counts.

				$additional_taxonomies = array_unique( $additional_taxonomies );

				foreach ( $additional_taxonomies as $additional_taxonomy ) {
					if ( ! taxonomy_exists( $additional_taxonomy ) ) {
						continue;
					}

					if ( ! isset( $this->taxonomy_counts[ $additional_taxonomy ] ) ) {
						$this->taxonomy_counts[ $additional_taxonomy ] = array();
					}

					if ( ! isset( $this->taxonomies_in_cart[ $additional_taxonomy ] ) ) {
						$this->taxonomies_in_cart[ $additional_taxonomy ] = array();
					}

					$product_categories = wp_get_post_terms( $product_id, $additional_taxonomy );
					foreach ( $product_categories as $category ) {
						$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] = isset( $this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] ) ?
							$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] + $quantity : $quantity;

						$this->taxonomies_in_cart[ $additional_taxonomy ][] = $category->term_id;
					}
				}


			}
		}

		do_action( 'wc_dynamic_pricing_counter_updated' );
	}

	public function on_after_cart_item_quantity_update($cart_item_key, $quantity, $old_quantity, $cart) {
		$cart_item = WC()->cart->get_cart_item($cart_item_key);
		$variation_id = 0;
		$product_id = $cart_item['data']->get_id();
		if ($cart_item['data']->is_type('variation')) {
			$variation_id = $cart_item['data']->get_id();
			$product_id = $cart_item['data']->get_parent_id();
		}

		$this->on_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, null, null);
	}

	public function on_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( isset( $this->tracked_cart_items[ $cart_item_key ] ) ) {
			return;
		} else {
			$this->tracked_cart_items[ $cart_item_key ] = true;
		}

		//Store product counts
		$this->product_counts[ $product_id ] = isset( $this->product_counts[ $product_id ] ) ?
			$this->product_counts[ $product_id ] + $quantity : $quantity;

		//Gather product variation id counts
		if ( isset( $variation_id ) && ! empty( $variation_id ) ) {
			$this->variation_counts[ $variation_id ] = isset( $this->variation_counts[ $variation_id ] ) ?
				$this->variation_counts[ $variation_id ] + $quantity : $quantity;
		}

		//Gather product category counts
		$product            = wc_get_product( $product_id );
		$product_categories = WC_Dynamic_Pricing_Compatibility::get_product_category_ids( $product );

		foreach ( $product_categories as $category ) {
			$this->category_counts[ $category ] = isset( $this->category_counts[ $category ] ) ?
				$this->category_counts[ $category ] + $quantity : $quantity;

			$this->categories_in_cart[] = $category;
		}

		$additional_taxonomies = apply_filters( 'wc_dynamic_pricing_get_discount_taxonomies', array( 'product_brand' ) );
		//Gather additional taxonomy counts.

		$additional_taxonomies = array_unique( $additional_taxonomies );

		foreach ( $additional_taxonomies as $additional_taxonomy ) {
			if ( ! taxonomy_exists( $additional_taxonomy ) ) {
				continue;
			}

			if ( ! isset( $this->taxonomy_counts[ $additional_taxonomy ] ) ) {
				$this->taxonomy_counts[ $additional_taxonomy ] = array();
			}

			if ( ! isset( $this->taxonomies_in_cart[ $additional_taxonomy ] ) ) {
				$this->taxonomies_in_cart[ $additional_taxonomy ] = array();
			}

			$product_categories = wp_get_post_terms( $product_id, $additional_taxonomy );
			foreach ( $product_categories as $category ) {
				$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] = isset( $this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] ) ?
					$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] + $quantity : $quantity;

				$this->taxonomies_in_cart[ $additional_taxonomy ][] = $category->term_id;
			}
		}

		do_action( 'wc_dynamic_pricing_counter_updated' );
	}

	public function get_cart_item_from_session( $cart_item, $values, $cart_item_key ) {
		if ( empty( $cart_item ) ) {
			return $cart_item;
		}

		if ( isset( $this->tracked_cart_items[ $cart_item_key ] ) ) {
			return $cart_item;
		} else {
			$this->tracked_cart_items[ $cart_item_key ] = true;
		}

		$product = $cart_item['data'];

		//Store product counts
		$this->product_counts[ $product->get_id() ] = isset( $this->product_counts[ $product->get_id() ] ) ?
			$this->product_counts[ $product->get_id() ] + $cart_item['quantity'] : $cart_item['quantity'];


		if ( $product->get_type() == 'variation' ) {
			$id                          = WC_Dynamic_Pricing_Compatibility::get_parent_id( $cart_item['data'] );
			$this->product_counts[ $id ] = isset( $this->product_counts[ $id ] ) ?
				$this->product_counts[ $id ] + $cart_item['quantity'] : $cart_item['quantity'];
		}

		//Gather product variation id counts
		if ( isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ) {
			$this->variation_counts[ $cart_item['variation_id'] ] = isset( $this->variation_counts[ $cart_item['variation_id'] ] ) ?
				$this->variation_counts[ $cart_item['variation_id'] ] + $cart_item['quantity'] : $cart_item['quantity'];
		}

		//Gather product category counts
		$product_categories = WC_Dynamic_Pricing_Compatibility::get_product_category_ids( $product );
		foreach ( $product_categories as $category ) {
			$this->category_counts[ $category ] = isset( $this->category_counts[ $category ] ) ?
				$this->category_counts[ $category ] + $cart_item['quantity'] : $cart_item['quantity'];

			$this->categories_in_cart[] = $category;
		}

		$additional_taxonomies = apply_filters( 'wc_dynamic_pricing_get_discount_taxonomies', array( 'product_brand' ) );
		//Gather additional taxonomy counts.

		$additional_taxonomies = array_unique( $additional_taxonomies );

		foreach ( $additional_taxonomies as $additional_taxonomy ) {
			if ( ! taxonomy_exists( $additional_taxonomy ) ) {
				continue;
			}

			if ( ! isset( $this->taxonomy_counts[ $additional_taxonomy ] ) ) {
				$this->taxonomy_counts[ $additional_taxonomy ] = array();
			}

			if ( ! isset( $this->taxonomies_in_cart[ $additional_taxonomy ] ) ) {
				$this->taxonomies_in_cart[ $additional_taxonomy ] = array();
			}

			$product_categories = wp_get_post_terms( $product->get_id(), $additional_taxonomy );
			foreach ( $product_categories as $category ) {
				$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] = isset( $this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] ) ?
					$this->taxonomy_counts[ $additional_taxonomy ][ $category->term_id ] + $cart_item['quantity'] : $cart_item['quantity'];

				$this->taxonomies_in_cart[ $additional_taxonomy ][] = $category->term_id;
			}
		}


		do_action( 'wc_dynamic_pricing_counter_updated' );

		return $cart_item;
	}

	/** Static Access Methods * */
	public static function get_product_count( $product_id ) {
		return isset( self::$instance->product_counts[ $product_id ] ) ? self::$instance->product_counts[ $product_id ] : 0;
	}

	public static function get_variation_count( $variation_id ) {
		return isset( self::$instance->variation_counts[ $variation_id ] ) ? self::$instance->variation_counts[ $variation_id ] : 0;
	}

	public static function get_category_count( $category_id ) {
		return isset( self::$instance->category_counts[ $category_id ] ) ? self::$instance->category_counts[ $category_id ] : 0;
	}

	public static function categories_in_cart( $categories ) {
		if ( ! is_array( $categories ) ) {
			$categories = array( $categories );
		}

		return count( array_intersect( self::$instance->categories_in_cart, $categories ) ) > 0;
	}


	public static function get_taxonomy_count( $category_id, $taxonomy ) {
		if ( ! isset( self::$instance->taxonomy_counts[ $taxonomy ] ) ) {
			return 0;
		}

		return isset( self::$instance->taxonomy_counts[ $taxonomy ][ $category_id ] ) ? self::$instance->taxonomy_counts[ $taxonomy ][ $category_id ] : 0;
	}

	public static function taxonomies_in_cart( $categories, $taxonomy ) {
		if ( ! isset( self::$instance->taxonomies_in_cart[ $taxonomy ] ) ) {
			return false;
		}

		if ( ! is_array( $categories ) ) {
			$categories = array( $categories );
		}


		return count( array_intersect( self::$instance->taxonomies_in_cart[ $taxonomy ], $categories ) ) > 0;
	}

}
