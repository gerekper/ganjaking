<?php
/**
 * Helper function for YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Helper function for YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Dynamic_Pricing_Helper' ) ) {

	/**
	 * Class YITH_WC_Dynamic_Pricing_Helper
	 */
	class YITH_WC_Dynamic_Pricing_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WC_Dynamic_Pricing_Helper
		 */
		protected static $instance;
		/**
		 * Product counters.
		 *
		 * @var array
		 */
		public $product_counters = array();
		/**
		 * Variation counters.
		 *
		 * @var array
		 */
		public $variation_counters = array();
		/**
		 * Categories counter.
		 *
		 * @var array
		 */
		public $categories_counter = array();
		/**
		 * Categories on cart.
		 *
		 * @var array
		 */
		public $cart_categories = array();
		/**
		 * Tags counters.
		 *
		 * @var array
		 */
		public $tags_counter = array();
		/**
		 * Tags on cart.
		 *
		 * @var array
		 */
		public $cart_tags = array();
		/**
		 * Discount to apply.
		 *
		 * @var array
		 */
		public $discounts_to_apply = array();
		/**
		 * Product to apply.
		 *
		 * @var array
		 */
		private $valid_product_to_apply = array();
		/**
		 * Gift product to apply.
		 *
		 * @var array
		 */
		private $valid_gift_product_to_apply = array();
		/**
		 * Valid product to adjustment.
		 *
		 * @var array
		 */
		private $valid_product_to_adjustment = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Dynamic_Pricing_Helper
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'load_counters' ), 98 );
			add_action( 'yith_wacp_before_popup_content', array( $this, 'load_counters' ), 5 );
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		/**
		 * Register discount post type.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function register_post_type() {

			$labels = array(
				'name'               => _x( 'Discount Rules', 'Post Type General Name', 'ywdpd' ),
				'singular_name'      => _x( 'Discount Rule', 'Post Type Singular Name', 'ywdpd' ),
				'menu_name'          => __( 'Discount Rule', 'ywdpd' ),
				'parent_item_colon'  => __( 'Parent Item:', 'ywdpd' ),
				'all_items'          => __( 'All Discount Rules', 'ywdpd' ),
				'view_item'          => __( 'View Discount Rules', 'ywdpd' ),
				'add_new_item'       => __( 'Add New Discount Rule', 'ywdpd' ),
				'add_new'            => __( 'Add New Discount Rule', 'ywdpd' ),
				'edit_item'          => __( 'Discount Rule', 'ywdpd' ),
				'update_item'        => __( 'Update Discount Rule', 'ywdpd' ),
				'search_items'       => __( 'Search Discount Rule', 'ywdpd' ),
				'not_found'          => __( 'Not found', 'ywdpd' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'ywdpd' ),
			);

			$args = array(
				'label'               => __( 'ywdpd_discount', 'ywdpd' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'post',
			);

			register_post_type( 'ywdpd_discount', $args );

		}


		/**
		 * Load all the counters
		 *
		 * @return void
		 */
		public function load_counters() {
			if ( empty( WC()->cart->cart_contents ) ) {
				return;
			}

			$this->reset_counters();

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				$product_id   = $cart_item['product_id'];
				$variation_id = ( isset( $cart_item['variation_id'] ) && '' != $cart_item['variation_id'] ) ? $cart_item['variation_id'] : false;
				$quantity     = $cart_item['quantity'];

				if ( $variation_id ) {
					$this->product_counters[ $product_id ] = isset( $this->product_counters[ $product_id ] ) ?
						$this->product_counters[ $product_id ] + $quantity : $quantity;

					$this->variation_counters[ $variation_id ] = isset( $this->variation_counters[ $variation_id ] ) ?
						$this->variation_counters[ $variation_id ] + $quantity : $quantity;
				} else {
					$this->product_counters[ $product_id ] = isset( $this->product_counters[ $product_id ] ) ?
						$this->product_counters[ $product_id ] + $quantity : $quantity;
				}

				$categories = wp_get_post_terms( $product_id, 'product_cat' );
				foreach ( $categories as $category ) {
					$this->categories_counter[ $category->term_id ] = isset( $this->categories_counter[ $category->term_id ] ) ?
						$this->categories_counter[ $category->term_id ] + $quantity : $quantity;

					$this->cart_categories[] = $category->term_id;
				}

				$tags = wp_get_post_terms( $product_id, 'product_tag' );
				foreach ( $tags as $tag ) {
					$this->tags_counter[ $tag->term_id ] = isset( $this->tags_counter[ $tag->term_id ] ) ?
						$this->tags_counter[ $tag->term_id ] + $quantity : $quantity;

					$this->cart_tags[] = $tag->term_id;
				}
			}
		}

		/**
		 * Reset all counters
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		private function reset_counters() {
			$this->categories_counter = array();
			$this->cart_categories    = array();
			$this->tags_counter       = array();
			$this->cart_tags          = array();
			$this->product_counters   = array();
			$this->variation_counters = array();
		}

		/**
		 * Get all user role list for select field
		 *
		 * @access public
		 * @return array
		 */
		public function get_roles() {
			global $wp_roles;
			$roles = array();

			foreach ( $wp_roles->get_names() as $key => $role ) {
				$roles[ $key ] = translate_user_role( $role );
			}

			return array_merge(
				array(
					''      => __( 'All', 'ywdpd' ),
					'guest' => __(
						'Guest',
						'ywdpd'
					),
				),
				$roles
			);
		}

		/**
		 * Validate date
		 *
		 * @access public
		 *
		 * @param string $from From date.
		 * @param string $to To date.
		 *
		 * @return mixed
		 */
		public function validate_schedule( $from, $to ) {

			if ( '' === $from && '' === $to ) {
				return true;
			}

			try {

				$return     = true;
				$timezone   = get_option( 'timezone_string' );
				$zone       = '' !== $timezone ? new DateTimeZone( $timezone ) : '';
				$gmt_offset = get_option( 'gmt_offset' );
				$ve         = $gmt_offset > 0 ? '+' : '-';

				if ( '' !== $zone ) {
					$today_dt = new DateTime( 'now', $zone );
				} else {
					$today_dt = new DateTime( '@' . strtotime( 'now ' . $ve . absint( $gmt_offset ) . ' HOURS' ) );
				}

				if ( '' !== $from ) {

					if ( '' !== $zone ) {
						$from_dt = new DateTime( $from, $zone );
					} else {
						$from_dt = new DateTime( '@' . strtotime( $from . ' ' . $ve . absint( $gmt_offset ) . ' HOURS' ) );
					}

					if ( $today_dt < $from_dt ) {
						$return = false;
					}
				}

				if ( $return && '' !== $to ) {

					if ( '' !== $zone ) {
						$to_dt = new DateTime( $to, $zone );
					} else {
						$to_dt = new DateTime( '@' . strtotime( $to . ' ' . $ve . absint( $gmt_offset ) . ' HOURS' ) );
					}

					if ( $today_dt > $to_dt ) {
						$return = false;
					}
				}
			} catch ( Exception $e ) {
				return false;
			}

			return apply_filters( 'ywsbs_validate_schedule', $return, $from, $to );

		}

		/**
		 * Validate user
		 *
		 * @access public
		 *
		 * @param string $type Type of validation.
		 * @param array $users_list User list.
		 *
		 * @return mixed
		 */
		public function validate_user( $type, $users_list ) {

			$to_return = false;

			if ( ! is_array( $users_list ) ) {
				return $to_return;
			}
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$intersect    = array_intersect( $current_user->roles, $users_list );

				switch ( $type ) {
					case 'role_list':
						if ( ! empty( $current_user->roles ) && is_array( $current_user->roles ) && ! empty( $intersect ) ) {
							$to_return = true;
						}
						break;
					case 'role_list_excluded':
						if ( ! empty( $current_user->roles ) && is_array( $current_user->roles ) && empty( $intersect ) ) {
							$to_return = true;
						}
						break;
					case 'customers_list':
						if ( in_array( $current_user->ID, $users_list ) ) {
							$to_return = true;
						}
						break;
					case 'customers_list_excluded':
						if ( ! in_array( $current_user->ID, $users_list ) ) {
							$to_return = true;
						}
						break;
					default:
				}
			} else {
				switch ( $type ) {
					case 'role_list':
						if ( in_array( 'guest', $users_list ) ) {
							$to_return = true;
						}
						break;
					case 'role_list_excluded':
						if ( ! in_array( 'guest', $users_list ) ) {
							$to_return = true;
						}
						break;

					default:
				}
			}

			return apply_filters( 'yit_ywdpd_validate_user', $to_return, $type, $users_list );
		}


		/**
		 * Check if the cart item has the bulk applied.
		 *
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return bool
		 */
		public function has_a_bulk_applied( $cart_item_key ) {

			if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'] ) ) {
				return false;
			}

			$ywdpd_discounts = WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'];
			foreach ( $ywdpd_discounts as $ywdpd_discount ) {
				if ( isset( $ywdpd_discount['discount_mode'] ) && 'bulk' === $ywdpd_discount['discount_mode'] ) {
					return true;
				}
			}

			return false;

		}

		/**
		 * Validate product apply_to
		 *
		 * @access public
		 *
		 * @param string $key_rule Key rule.
		 * @param array $rule Rule.
		 * @param string $cart_item_key Cart item key.
		 * @param array $cart_item Cart item.
		 *
		 * @return mixed
		 */
		public function validate_apply_to( $key_rule, $rule, $cart_item_key, $cart_item ) {

			$is_valid = false;

			if ( $this->is_in_exclusion_rule( $cart_item ) || ( $this->has_a_bulk_applied( $cart_item_key ) && 'bulk' === $rule['discount_mode'] ) ) {
				return false;
			}

			switch ( $rule['apply_to'] ) {
				case 'all_products':
					$is_valid = true;
					break;
				case 'products_list':
					if ( isset( $rule['apply_to_products_list'] ) ) {
						$product_list = $rule['apply_to_products_list'];
						if ( is_array( $product_list ) && $this->product_in_list( $cart_item, $product_list ) ) {
							$is_valid = true;
						}
					}
					break;
				case 'products_list_excluded':
					if ( isset( $rule['apply_to_products_list_excluded'] ) ) {
						$product_list = $rule['apply_to_products_list_excluded'];
						if ( is_array( $product_list ) && ! $this->product_in_list( $cart_item, $product_list ) ) {
							$is_valid = true;
						}
					}
					break;
				case 'categories_list':
					if ( isset( $rule['apply_to_categories_list'] ) ) {
						$is_valid = $this->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_cat' );
					}
					break;
				case 'categories_list_excluded':
					if ( isset( $rule['apply_to_categories_list_excluded'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_to_categories_list_excluded'], $cart_item['product_id'], 'product_cat', false );
						break;
					}
					break;
				case 'tags_list':
					if ( isset( $rule['apply_to_tags_list'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_to_tags_list'], $cart_item['product_id'], 'product_tag' );
					}
					break;
				case 'tags_list_excluded':
					if ( isset( $rule['apply_to_tags_list_excluded'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_to_tags_list_excluded'], $cart_item['product_id'], 'product_tag', false );
					}
					break;

				case 'vendor_list':
					if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_to_vendors_list'] ) ) {
						break;
					}
					$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list'] );
					$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $vendor_of_item, $vendor_list );
					if ( ! empty( $intersect ) ) {
						$is_valid = true;
					}
					break;
				case 'vendor_list_excluded':
					if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_to_vendors_list_excluded'] ) ) {
						break;
					}
					$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list_excluded'] );
					$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $vendor_of_item, $vendor_list );
					if ( empty( $intersect ) ) {
						$is_valid = true;
					}
					break;

				default:
					$is_valid = apply_filters( 'ywdpd_validate_apply_to', $is_valid, $rule['apply_to'], $cart_item['product_id'], $rule, $cart_item );
			}

			if ( $is_valid ) {

				$discount = array();
				$quantity = $this->check_quantity( $rule, $cart_item );

				$tt                                       = 0;
				$is_valid_also_for_adjust                 = $this->valid_product_to_adjust( $rule, $cart_item );
				$num_valid_product_to_apply_in_cart_clean = $this->num_valid_product_to_apply_in_cart( $rule, $cart_item, true );
				$num_valid_product_to_apply_in_cart_mix   = $this->num_valid_product_to_apply_in_cart( $rule, $cart_item, false );

				$product_id = ( isset( $cart_item['variation_id'] ) && '' != $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
				$product    = wc_get_product( $product_id );

				if ( ! $product ) {
					return false;
				}

				$discount['key']       = $key_rule;
				$discount['status']    = 'processing';
				$discount['exclusive'] = isset( $rule['apply_with_other_rules'] ) && ! ywdpd_is_true( $rule['apply_with_other_rules'] );

				$discount['onsale'] = isset( $rule['apply_on_sale'] ) && ywdpd_is_true( $rule['apply_on_sale'] );

				remove_filter( 'woocommerce_product_get_price', array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price'
				) );
				remove_filter( 'woocommerce_product_variation_get_price', array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price'
				) );

				$discount['default_price'] = ( WC()->cart->tax_display_cart === 'excl' ) ? yit_get_price_excluding_tax( $product ) : yit_get_price_including_tax( $product );

				add_filter( 'woocommerce_product_get_price', array(
					YITH_WC_Dynamic_Pricing_Frontend(),
					'get_price'
				), 10, 2 );

				$discount = apply_filters( 'ywdpd_validate_apply_to_discount', $discount, $product, $key_rule, $rule, $cart_item_key, $cart_item );

				if ( 'bulk' === $rule['discount_mode'] ) {
					$discount['discount_mode'] = 'bulk';
					foreach ( $rule['rules'] as $index => $r ) {

						if ( ( $quantity >= $r['min_quantity'] && '*' === $r['max_quantity'] ) || ( $quantity <= $r['max_quantity'] && $quantity >= $r['min_quantity'] ) ) {
							$discount['discount_amount'] = array(
								'type'   => $r['type_discount'],
								'amount' => $r['discount_amount'],
							);
							break;
						}
					}
				} elseif ( 'special_offer' === $rule['discount_mode'] ) {
					$discount['discount_mode'] = 'special_offer';

					if ( isset( $rule['so-rule']['repeat'] ) ) {
						$repetitions = floor( ( $num_valid_product_to_apply_in_cart_clean + $num_valid_product_to_apply_in_cart_mix ) / $rule['so-rule']['purchase'] );
					} else {
						$repetitions = 1;
					}

					$rcq          = $num_valid_product_to_apply_in_cart_clean; // remaining clean quantity.
					$rmq          = $num_valid_product_to_apply_in_cart_mix; // remaining mixed quantity.
					$tot_apply_to = $rmq + $rcq;

					$tt = 0;
					if ( $rcq || $rmq ) {
						for ( $x = 1; $x <= $repetitions; $x ++ ) {
							if ( $tot_apply_to - $rule['so-rule']['purchase'] >= 0 ) {
								$tot_apply_to -= $rule['so-rule']['purchase'];
								$tt           += isset( $rule['so-rule']['receive'] ) ? intval( $rule['so-rule']['receive'] ) : 0;
							}
						}
					}

					$discount['discount_amount'] = array(
						'type'           => $rule['so-rule']['type_discount'],
						'amount'         => $rule['so-rule']['discount_amount'],
						'purchase'       => $rule['so-rule']['purchase'],
						'receive'        => $rule['so-rule']['receive'],
						'quantity_based' => $rule['quantity_based'],
						'total_target'   => $tt,
						'same_product'   => 0,
					);

				}

				if ( ! isset( $discount['discount_amount'] ) ) {
					return false;
				}

				// check if the rule can be applied to current cart item.
				if ( 'same_product' === $rule['apply_adjustment'] || 'all_products' === $rule['apply_adjustment'] || $is_valid_also_for_adjust ) {
					WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $key_rule ]                                    = $discount;
					WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $key_rule ]['discount_amount']['same_product'] = 1;
				}

				if ( 'same_product' !== $rule['apply_adjustment'] ) {
					foreach ( WC()->cart->cart_contents as $cart_item_key_adj => $cart_item_adj ) {
						if ( 'special_offer' === $discount['discount_mode'] && $this->valid_product_to_adjust( $rule, $cart_item_adj ) ) {
							$discount['discount_amount']['total_target'] = $tt;
						}

						$this->process_rule_adjustment( $rule, $key_rule, $cart_item_key_adj, $cart_item_adj, $discount );
					}
				}
			}

			return $is_valid;

		}

		/**
		 * Check if the product in cart_item is in a exclusion rule
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return bool
		 */
		public function is_in_exclusion_rule( $cart_item ) {

			$exclusion_rules = YITH_WC_Dynamic_Pricing()->get_exclusion_rules();
			$excluded        = false;

			foreach ( $exclusion_rules as $rule ) {

				switch ( $rule['apply_to'] ) {
					case 'all_products':
						return true;
					case 'products_list':
						$product_list = $rule['apply_to_products_list'];
						if ( is_array( $product_list ) && $this->product_in_list( $cart_item, $product_list ) ) {
							$excluded = true;
						}
						break;
					case 'products_list_excluded':
						$product_list = $rule['apply_to_products_list_excluded'];
						if ( is_array( $product_list ) && ! $this->product_in_list( $cart_item, $product_list ) ) {
							$excluded = true;
						}
						break;
					case 'categories_list':
						$excluded = $this->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_cat' );
						break;
					case 'categories_list_excluded':
						$excluded = $this->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_cat', false );
						break;
					case 'tags_list':
						$excluded = $this->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_tag' );
						break;
					case 'tags_list_excluded':
						$excluded = $this->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_tag', false );
						break;
					case 'vendor_list':
						if ( ! class_exists( 'YITH_Vendors' ) ) {
							break;
						}

						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list'] );
						$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( ! empty( $intersect ) ) {
							$excluded = true;
						}
						break;
					case 'vendor_list_excluded':
						if ( ! class_exists( 'YITH_Vendors' ) ) {
							break;
						}
						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list_excluded'] );
						$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( empty( $intersect ) ) {
							$excluded = true;
						}
						break;
					default:
						$excluded = apply_filters( 'ywdpd_is_in_exclusion_rule', $excluded, $rule['apply_to'], $cart_item['product_id'], $rule, $cart_item );
				}

				if ( $excluded ) {
					return true;
				}
			}

			return $excluded;
		}

		/**
		 * Assign the discount to a cart item if is a valid product to adjust
		 *
		 * @param array $rule Rule.
		 * @param string $key_rule Key rule.
		 * @param string $cart_item_key Cart item key.
		 * @param array $cart_item Cart item.
		 * @param mixed $discount Discount.
		 *
		 * @return mixed
		 */
		public function process_rule_adjustment( $rule, $key_rule, $cart_item_key, $cart_item, $discount ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $key_rule ] ) ) {
				return;
			}

			if ( $this->valid_product_to_adjust( $rule, $cart_item ) ) {
				WC()->cart->cart_contents[ $cart_item_key ]['ywdpd_discounts'][ $key_rule ] = $discount;
			}

			return;
		}

		/**
		 * Check the quantity of a cart_item based on the rule quantity_based
		 *
		 * @param array $rule Rule.
		 * @param array $cart_item Cart item.
		 *
		 * @return int|mixed
		 */
		public function check_quantity( $rule, $cart_item ) {

			$quantity = $cart_item['quantity'];

			if ( 'bulk' === $rule['discount_mode'] || 'special_offer' === $rule['discount_mode'] ) {
				switch ( $rule['quantity_based'] ) {
					case 'cart_line':
						break;
					case 'single_product':
						if ( isset( $this->product_counters[ $cart_item['product_id'] ] ) ) {
							$quantity = $this->product_counters[ $cart_item['product_id'] ];
						}
						break;
					case 'single_variation_product':
						if ( isset( $cart_item['variation_id'] ) && '' != $cart_item['variation_id'] && isset( $this->variation_counters[ $cart_item['variation_id'] ] ) ) {
							$quantity = $this->variation_counters[ $cart_item['variation_id'] ];
						}
						break;
					case 'cumulative':
						$quantity = $this->get_cumulative_quantity( $rule );
						break;
					default:
				}
			}

			return $quantity;
		}

		/**
		 * Get the cumulative quantity in the cart contents
		 *
		 * @param array $rule Rule.
		 *
		 * @return int
		 */
		public function get_cumulative_quantity( $rule ) {
			$quantity = 0;
			switch ( $rule['apply_to'] ) {
				case 'all_products':
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$quantity += $cart_item['quantity'];
					}
					break;
				case 'products_list':
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						if ( apply_filters( 'ywdpd_get_cumulative_quantity_product_list', false, $cart_item, $quantity, $rule ) ) {
							continue;
						}
						$product_list = $rule['apply_to_products_list'];
						if ( $this->product_in_list( $cart_item, $product_list ) ) {
							$quantity += $cart_item['quantity'];
						}
					}
					break;
				case 'products_list_excluded':
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$product_list = $rule['apply_to_products_list_excluded'];
						if ( ! $this->product_in_list( $cart_item, $product_list ) ) {
							$quantity += $cart_item['quantity'];
						}
					}
					break;
				case 'categories_list':
					$quantity = $this->check_taxonomy_quantity( $rule['apply_to_categories_list'], 'product_cat' );
					break;
				case 'categories_list_excluded':
					$quantity = $this->check_taxonomy_quantity( $rule['apply_to_categories_list_excluded'], 'product_cat', false );
					break;
				case 'tags_list':
					$quantity = $this->check_taxonomy_quantity( $rule['apply_to_tags_list'], 'product_tag' );
					break;
				case 'tags_list_excluded':
					$quantity = $this->check_taxonomy_quantity( $rule['apply_to_tags_list_excluded'], 'product_tag', false );
					break;
				case 'vendor_list':
					if ( ! class_exists( 'YITH_Vendors' ) ) {
						break;
					}
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list'] );
						$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( ! empty( $intersect ) ) {
							$quantity += $cart_item['quantity'];
						}
					}
					break;
				case 'vendor_list_excluded':
					if ( ! class_exists( 'YITH_Vendors' ) ) {
						break;
					}
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list_excluded'] );
						$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( empty( $intersect ) ) {
							$quantity += $cart_item['quantity'];
						}
					}
					break;
				default:
					$quantity = apply_filters( 'ywdpd_get_cumulative_quantity', $quantity, $rule['apply_to'], $rule );
			}

			return $quantity;
		}

		/**
		 * Check if the product in cart item is a valid product to adjust the rule
		 *
		 * @param array $rule Rule.
		 * @param array $cart_item Cart item.
		 *
		 * @return bool
		 */
		public function valid_product_to_adjust( $rule, $cart_item ) {
			$is_valid = false;

			switch ( $rule['apply_adjustment'] ) {
				case 'same_product':
					$is_valid = $this->valid_product_to_apply( $rule, wc_get_product( $cart_item['product_id'] ), true );
					break;
				case 'all_products':
					$is_valid = true;
					break;
				case 'products_list':
					$product_list = $rule['apply_adjustment_products_list'];
					if ( $this->product_in_list( $cart_item, $product_list ) ) {
						$is_valid = true;
					}
					break;
				case 'products_list_excluded':
					$product_list = $rule['apply_adjustment_products_list_excluded'];
					if ( ! $this->product_in_list( $cart_item, $product_list ) ) {
						$is_valid = true;
					}
					break;
				case 'categories_list':
					if ( isset( $rule['apply_adjustment_categories_list'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_adjustment_categories_list'], $cart_item['product_id'], 'product_cat' );
					}
					break;
				case 'categories_list_excluded':
					if ( isset( $rule['apply_adjustment_categories_list_excluded'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_adjustment_categories_list_excluded'], $cart_item['product_id'], 'product_cat', false );
					}
					break;
				case 'tags_list':
					if ( isset( $rule['apply_adjustment_tags_list'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_adjustment_tags_list'], $cart_item['product_id'], 'product_tag' );
					}
					break;
				case 'tags_list_excluded':
					if ( isset( $rule['apply_adjustment_tags_list_excluded'] ) ) {
						$is_valid = $this->check_taxonomy( $rule['apply_adjustment_tags_list_excluded'], $cart_item['product_id'], 'product_tag', false );
					}
					break;
				case 'vendor_list':
					if ( ! class_exists( 'YITH_Vendors' ) ) {
						break;
					}
					$vendor_list    = array_map( 'intval', $rule['apply_adjustment_vendor_list'] );
					$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $vendor_of_item, $vendor_list );
					if ( ! empty( $intersect ) ) {
						$is_valid = true;
					}
					break;
				case 'vendor_list_excluded':
					if ( ! class_exists( 'YITH_Vendors' ) ) {
						break;
					}
					$vendor_list    = array_map( 'intval', $rule['apply_adjustment_vendor_list_excluded'] );
					$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
					$intersect      = array_intersect( $vendor_of_item, $vendor_list );
					if ( empty( $intersect ) ) {
						$is_valid = true;
					}
					break;
				default:
					$is_valid = apply_filters( 'ywdpd_valid_product_to_adjust', $is_valid, $rule['apply_adjustment'], $cart_item['product_id'], $rule, $cart_item );
			}

			return $is_valid;
		}

		/**
		 * Check valid product to adjustment.
		 *
		 * @param array $rule Rule.
		 * @param WC_Product $product Product.
		 * @param bool $other_variations Variations.
		 *
		 * @return bool
		 */
		public function valid_product_to_adjustment( $rule, $product, $other_variations = false ) {

			$key_check = $rule['key'] . '_' . $product->get_id() . '_' . ( $other_variations ? 1 : 0 );

			if ( isset( $this->valid_product_to_adjustment[ $key_check ] ) ) {
				return $this->valid_product_to_adjustment[ $key_check ];
			}

			$is_valid = false;

			$even_onsale = isset( $rule['apply_on_sale'] ) && ywdpd_is_true( $rule['apply_on_sale'] );
			$sale_price  = yit_get_prop( $product, 'sale_price', true, 'edit' );
			$main_id     = $product->is_type( 'variation' ) ? yit_get_base_product_id( $product ) : $product->get_id();
			$search_in   = array( $main_id, $product->get_id() );
			$sale_price  = apply_filters( 'ywcdp_product_is_on_sale', '' !== $sale_price, $product, $rule );

			if ( $sale_price && ! $even_onsale ) {
				return false;
			}

			if ( $other_variations ) {
				if ( $product->is_type( 'variation' ) ) {
					$parent    = wc_get_product( yit_get_base_product_id( $product ) );
					$search_in = array_merge( $parent->get_children(), $search_in );
				} elseif ( $product->is_type( 'variable' ) ) {
					$search_in = array_merge( $product->get_children(), $search_in );
				}
			}

			if ( isset( $rule['apply_adjustment'] ) ) {
				switch ( $rule['apply_adjustment'] ) {
					case 'all_products':
						$is_valid = true;
						break;
					case 'products_list':
						if ( isset( $rule['apply_adjustment_products_list'] ) ) {
							$product_list = $rule['apply_adjustment_products_list'];
							$intersect    = array_intersect( $search_in, $product_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'products_list_excluded':
						if ( isset( $rule['apply_adjustment_products_list_excluded'] ) ) {
							$product_list = $rule['apply_adjustment_products_list_excluded'];
							$intersect    = array_intersect( $search_in, $product_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'categories_list':
						if ( isset( $rule['apply_adjustment_categories_list'] ) ) {
							$categories_list    = $rule['apply_adjustment_categories_list'];
							$get_by             = is_numeric( $categories_list[0] ) ? 'ids' : 'slugs';
							$categories_of_item = wc_get_product_terms( $main_id, 'product_cat', array( 'fields' => $get_by ) );
							$categories_of_item = apply_filters( 'ywdpd_dynamic_category_list', $categories_of_item, $main_id, $rule );
							$intersect          = array_intersect( $categories_of_item, $categories_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}

						break;
					case 'categories_list_excluded':
						if ( isset( $rule['apply_adjustment_categories_list_excluded'] ) ) {
							$categories_list    = $rule['apply_adjustment_categories_list_excluded'];
							$get_by             = is_numeric( $categories_list[0] ) ? 'ids' : 'slugs';
							$categories_of_item = wc_get_product_terms( $main_id, 'product_cat', array( 'fields' => $get_by ) );
							$categories_of_item = apply_filters( 'ywdpd_dynamic_exclude_category_list', $categories_of_item, $main_id, $rule );
							$intersect          = array_intersect( $categories_of_item, $categories_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'tags_list':
						if ( isset( $rule['apply_adjustment_tags_list'] ) ) {
							$tags_list    = $rule['apply_adjustment_tags_list'];
							$get_by       = is_numeric( $tags_list[0] ) ? 'ids' : 'slugs';
							$tags_of_item = wc_get_product_terms( $main_id, 'product_tag', array( 'fields' => $get_by ) );
							$intersect    = array_intersect( $tags_of_item, $tags_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'tags_list_excluded':
						if ( isset( $rule['apply_adjustment_tags_list_excluded'] ) ) {
							$tags_list    = $rule['apply_adjustment_tags_list_excluded'];
							$get_by       = is_numeric( $tags_list[0] ) ? 'ids' : 'slugs';
							$tags_of_item = wc_get_product_terms( $main_id, 'product_tag', array( 'fields' => $get_by ) );
							$intersect    = array_intersect( $tags_of_item, $tags_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;

					case 'vendor_list':
						if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_adjustment_vendor_list'] ) ) {
							break;
						}
						$vendor_list    = array_map( 'intval', $rule['apply_adjustment_vendor_list'] );
						$vendor_of_item = wc_get_product_terms( $main_id, YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = true;
						}
						break;
					case 'vendor_list_excluded':
						if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_adjustment_vendor_list_excluded'] ) ) {
							break;
						}
						$vendor_list    = array_map( 'intval', $rule['apply_adjustment_vendor_list_excluded'] );
						$vendor_of_item = wc_get_product_terms( $main_id, YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( empty( $intersect ) ) {
							$is_valid = true;
						}
						break;
					default:
						$is_valid = apply_filters( 'ywdpd_valid_product_to_adjustment_bulk', true, $rule['apply_adjustment'], $main_id, $rule, $product );

				}
			}

			$this->valid_product_to_adjustment[ $key_check ] = $is_valid;

			return $is_valid;
		}

		/**
		 * Check if the product is a valid product to apply the rule
		 *
		 * @param array $rule Rule.
		 * @param WC_Product|WC_Product_Variable $product Product.
		 * @param bool $other_variations Variations.
		 *
		 * @return bool
		 */
		public function valid_product_to_apply( $rule, $product, $other_variations = false ) {

			if ( ! $product ) {
				return false;
			}

			$key_check = $rule['key'] . '_' . $product->get_id() . '_' . ( $other_variations ? 1 : 0 );

			if ( isset( $this->valid_product_to_apply[ $key_check ] ) ) {
				return $this->valid_product_to_apply[ $key_check ];
			}

			$is_valid = false;

			$even_onsale = isset( $rule['apply_on_sale'] ) && ywdpd_is_true( $rule['apply_on_sale'] );
			$sale_price  = yit_get_prop( $product, 'sale_price', true, 'edit' );
			$main_id     = $product->is_type( 'variation' ) ? yit_get_base_product_id( $product ) : $product->get_id();
			$search_in   = array( $main_id, $product->get_id() );
			$sale_price  = apply_filters( 'ywcdp_product_is_on_sale', '' !== $sale_price, $product, $rule );

			if ( $sale_price && ! $even_onsale ) {
				return false;
			}

			if ( $other_variations && apply_filters( 'yith_ywdpd_valid_product_to_apply_other_variations', true, $rule, $product, $other_variations ) ) {
				if ( $product->is_type( 'variation' ) ) {
					$parent = wc_get_product( yit_get_base_product_id( $product ) );
					if ( $parent instanceof WC_Product_Variable ) {
						$search_in = array_merge( $parent->get_children(), $search_in );
					}
				} elseif ( $product->is_type( 'variable' ) ) {
					$search_in = array_merge( $product->get_children(), $search_in );
				}
			}

			if ( isset( $rule['apply_to'] ) ) {
				switch ( $rule['apply_to'] ) {
					case 'all_products':
						$is_valid = true;
						break;
					case 'products_list':
						if ( isset( $rule['apply_to_products_list'] ) ) {
							$product_list = $rule['apply_to_products_list'];
							$intersect    = array_intersect( $search_in, $product_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'products_list_excluded':
						if ( isset( $rule['apply_to_products_list_excluded'] ) ) {
							$product_list = $rule['apply_to_products_list_excluded'];
							$intersect    = array_intersect( $search_in, $product_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'categories_list':
						if ( isset( $rule['apply_to_categories_list'] ) ) {
							$categories_list    = $rule['apply_to_categories_list'];
							$get_by             = is_numeric( $categories_list[0] ) ? 'ids' : 'slugs';
							$categories_of_item = wc_get_product_terms( $main_id, 'product_cat', array( 'fields' => $get_by ) );
							$categories_of_item = apply_filters( 'ywdpd_dynamic_category_list', $categories_of_item, $main_id, $rule );
							$intersect          = array_intersect( $categories_of_item, $categories_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}

						break;
					case 'categories_list_excluded':
						if ( isset( $rule['apply_to_categories_list_excluded'] ) ) {
							$categories_list    = $rule['apply_to_categories_list_excluded'];
							$get_by             = is_numeric( $categories_list[0] ) ? 'ids' : 'slugs';
							$categories_of_item = wc_get_product_terms( $main_id, 'product_cat', array( 'fields' => $get_by ) );
							$categories_of_item = apply_filters( 'ywdpd_dynamic_exclude_category_list', $categories_of_item, $main_id, $rule );
							$intersect          = array_intersect( $categories_of_item, $categories_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'tags_list':
						if ( isset( $rule['apply_to_tags_list'] ) ) {
							$tags_list    = $rule['apply_to_tags_list'];
							$get_by       = is_numeric( $tags_list[0] ) ? 'ids' : 'slugs';
							$tags_of_item = wc_get_product_terms( $main_id, 'product_tag', array( 'fields' => $get_by ) );
							$intersect    = array_intersect( $tags_of_item, $tags_list );
							if ( ! empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'tags_list_excluded':
						if ( isset( $rule['apply_to_tags_list_excluded'] ) ) {
							$tags_list    = $rule['apply_to_tags_list_excluded'];
							$get_by       = is_numeric( $tags_list[0] ) ? 'ids' : 'slugs';
							$tags_of_item = wc_get_product_terms( $main_id, 'product_tag', array( 'fields' => $get_by ) );
							$intersect    = array_intersect( $tags_of_item, $tags_list );
							if ( empty( $intersect ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'vendor_list':
						if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_to_vendors_list'] ) ) {
							break;
						}
						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list'] );
						$vendor_of_item = wc_get_product_terms( $main_id, YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = true;
						}
						break;
					case 'vendor_list_excluded':
						if ( ! class_exists( 'YITH_Vendors' ) || ! isset( $rule['apply_to_vendors_list_excluded'] ) ) {
							break;
						}
						$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list_excluded'] );
						$vendor_of_item = wc_get_product_terms( $main_id, YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
						$intersect      = array_intersect( $vendor_of_item, $vendor_list );
						if ( empty( $intersect ) ) {
							$is_valid = true;
						}
						break;
					default:
						$is_valid = apply_filters( 'ywdpd_valid_product_to_apply_bulk', $is_valid, $rule['apply_to'], $main_id, $rule, $product );
				}
			}

			$this->valid_product_to_apply[ $key_check ] = $is_valid;

			return $is_valid;
		}

		/**
		 * Check if the product is a valid product to apply the bulk rule
		 *
		 * @param array $rule Rule.
		 * @param WC_Product $product Product.
		 * @param bool $other_variations Variations.
		 *
		 * @return bool
		 */
		public function valid_product_to_apply_bulk( $rule, $product, $other_variations = false ) {
			return isset( $rule['discount_mode'] ) && $rule['discount_mode'] == 'bulk' && $this->valid_product_to_apply( $rule, $product, $other_variations );
		}

		/**
		 * Check if the product is a valid product to adjustment the bulk rule
		 *
		 * @param array $rule Rule.
		 * @param WC_Product $product Product.
		 * @param bool $other_variations Variations.
		 *
		 * @return bool
		 */
		public function valid_product_to_adjustment_bulk( $rule, $product, $other_variations = false ) {
			return isset( $rule['discount_mode'] ) && $rule['discount_mode'] == 'bulk' && $this->valid_product_to_adjustment( $rule, $product, $other_variations );
		}

		/**
		 * Check if the user has made $num order
		 *
		 * @param integer $num Number of orders.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_num_of_orders( $num, $rule ) {

			$orders = array();

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$orders = wc_get_orders(
						array(
							'status'   => apply_filters( 'yith_ywdpd_valid_num_of_orders_status', 'wc-completed', $num, $rule, $current_user ),
							'limit'    => - 1,
							'customer' => $current_user->ID,
						)
					);

				} else {
					$args   = array(
						'numberposts' => - 1,
						'post_type'   => 'shop_order',
						'post_status' => apply_filters( 'yith_ywdpd_valid_num_of_orders_status', 'wc-completed', $num, $rule, $current_user ),
						'meta_key'    => '_customer_user',
						'meta_value'  => $current_user->ID,
					);
					$orders = get_posts( $args );

				}
			}

			return count( $orders ) >= $num;
		}

		/**
		 * Check if the user has made max $num order
		 *
		 * @param integer $num Number of orders.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_max_num_of_orders( $num, $rule ) {

			$orders = array();

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$orders = wc_get_orders(
						array(
							'status'   => apply_filters( 'yith_ywdpd_valid_max_num_of_orders_status', 'wc-completed', $num, $rule, $current_user ),
							'limit'    => - 1,
							'customer' => $current_user->ID,
						)
					);

				} else {
					$args   = array(
						'numberposts' => - 1,
						'post_type'   => 'shop_order',
						'post_status' => apply_filters( 'yith_ywdpd_valid_max_num_of_orders_status', 'wc-completed', $num, $rule, $current_user ),
						'meta_key'    => '_customer_user',
						'meta_value'  => $current_user->ID,
					);
					$orders = get_posts( $args );

				}
			}

			return count( $orders ) < $num;
		}

		/**
		 * Check if the user has spent $limit amount
		 *
		 * @param float $limit Amount limit.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_amount_spent( $limit, $rule ) {

			$is_valid = false;
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$orders = wc_get_orders(
						array(
							'status'   => 'wc-completed',
							'limit'    => - 1,
							'customer' => $current_user->ID,
						)
					);

					$amount = 0;
					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {

							if ( apply_filters( 'ywdpd_include_shipping_on_totals', true ) ) {
								$amount += $order->get_total();
							} else {
								$amount += $order->get_subtotal() + ( $order->get_total_tax() - $order->get_shipping_tax() );
							}

							if ( $amount >= $limit ) {
								$is_valid = true;
							}
						}
					}
				} else {
					$args   = array(
						'numberposts' => - 1,
						'post_type'   => 'shop_order',
						'post_status' => 'wc-completed',
						'meta_key'    => '_customer_user',
						'meta_value'  => $current_user->ID,
					);
					$orders = get_posts( $args );
					$amount = 0;
					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {
							$order_obj = wc_get_order( $order->ID );
							$amount    += $order_obj->get_total();
							if ( $amount >= $limit ) {
								$is_valid = true;
							}
						}
					}
				}
			}

			return $is_valid;
		}

		/**
		 * Check if the user has spent $limit amount
		 *
		 * @param float $limit Limit max amount.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_max_amount_spent( $limit, $rule ) {

			$is_valid = false;
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$orders = wc_get_orders(
						array(
							'status'   => 'wc-completed',
							'limit'    => - 1,
							'customer' => $current_user->ID,
						)
					);

					$amount = 0;
					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {

							if ( apply_filters( 'ywdpd_include_shipping_on_totals', true ) ) {
								$amount += $order->get_total();
							} else {
								$amount += $order->get_subtotal() + ( $order->get_total_tax() - $order->get_shipping_tax() );
							}

							if ( $amount < $limit ) {
								$is_valid = true;
							}
						}
					}
				} else {
					$args   = array(
						'numberposts' => - 1,
						'post_type'   => 'shop_order',
						'post_status' => 'wc-completed',
						'meta_key'    => '_customer_user',
						'meta_value'  => $current_user->ID,
					);
					$orders = get_posts( $args );
					$amount = 0;
					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {
							$order_obj = wc_get_order( $order->ID );
							$amount    += $order_obj->get_total();
							if ( $amount < $limit ) {
								$is_valid = true;
							}
						}
					}
				}
			}

			return $is_valid;
		}

		/**
		 * Check if in the cart there are $quantity items
		 *
		 * @param integer $quantity Quantity.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_sum_item_quantity( $quantity, $rule ) {
			$num_items = WC()->cart->get_cart_contents_count();

			$num_items = apply_filters( 'yith_dynamic_valid_sum_item_quantity', $num_items, $rule );
			if ( $num_items >= $quantity ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if in the cart there are less of $quantity items
		 *
		 * @param integer $quantity Quantity.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_sum_item_quantity_less( $quantity, $rule ) {
			$num_items = WC()->cart->get_cart_contents_count();
			$num_items = apply_filters( 'yith_dynamic_valid_sum_item_quantity_less', $num_items, $rule );

			if ( $num_items <= $quantity ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if in the cart there are $quantity item quantities
		 *
		 * @param integer $quantity Quantity.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_count_cart_items_less( $quantity, $rule ) {
			$item_quantity = apply_filters( 'ywdpd_get_cart_item_quantities', WC()->cart->get_cart_item_quantities() );
			if ( is_array( $item_quantity ) ) {
				$item_quantity = array_sum( $item_quantity );
			}

			if ( $item_quantity <= $quantity ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if in the cart there are at least $quantity items
		 *
		 * @param integer $quantity Quantity.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_count_cart_items_at_least( $quantity, $rule ) {
			$item_quantity = apply_filters( 'ywdpd_get_cart_item_quantities', WC()->cart->get_cart_item_quantities() );

			if ( is_array( $item_quantity ) ) {
				$item_quantity = array_sum( $item_quantity );
			}
			if ( $item_quantity >= $quantity ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if the subtotal at least is equal to $limit
		 *
		 * @param float $limit Limit subtotal amount.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_subtotal_at_least( $limit, $rule ) {
			// $subtotal = apply_filters( 'ywdpd_subtotal_at_least', YITH_WC_Dynamic_Pricing()->get_option( 'calculate_discounts_tax' ) == 'tax_excluded' ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal );
			$subtotal = YITH_WC_Dynamic_Discounts()->get_cart_subtotal_to_discount( $rule );
			if ( $subtotal >= $limit ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if the subtotal is less of $limit
		 *
		 * @param float $limit Limit subtotal amount.
		 * @param array $rule Rule.
		 *
		 * @return bool
		 */
		public function valid_subtotal_less( $limit, $rule ) {
			$subtotal = YITH_WC_Dynamic_Discounts()->get_cart_subtotal_to_discount( $rule );
			if ( $subtotal <= $limit ) {
				return true;
			}

			return false;
		}

		/**
		 * Validate product in cart
		 *
		 * @param string $type Product list condition.
		 * @param array $product_list Product list.
		 *
		 * @return bool
		 */
		public function validate_product_in_cart( $type, $product_list ) {
			$is_valid = false;

			if ( ! $product_list || ! is_array( $product_list ) ) {
				return $is_valid;
			}

			$get_by    = is_numeric( $product_list[0] ) ? 'ids' : 'slugs';
			$search_by = is_numeric( $product_list[0] ) ? 'id' : 'slug';

			switch ( $type ) {
				case 'products_list':
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						if ( $this->product_in_list( $cart_item, $product_list ) ) {
							$is_valid = true;
						}
					}
					break;
				case 'products_list_and':
					foreach ( $product_list as $pl ) {
						if ( $this->find_product_in_cart( $pl ) !== '' ) {
							$is_valid = true;
						} else {
							$is_valid = false;
							break;
						}
					}
					break;
				case 'products_list_excluded':
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						if ( ! $this->product_in_list( $cart_item, $product_list ) ) {
							$is_valid = true;
						} else {
							$is_valid = false;
							break;
						}
					}
					break;
				case 'categories_list':
					$categories_list = $product_list;
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$categories_of_item = wc_get_product_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => $get_by ) );
						$intersect          = array_intersect( $categories_of_item, $categories_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = true;
						}
					}

					break;
				case 'categories_list_and':
					$categories_list = $product_list;

					foreach ( $categories_list as $category_id ) {
						$term = get_term_by( $search_by, $category_id, 'product_cat' );
						if ( is_a( $term, 'WP_Term' ) && $this->find_taxonomy_in_cart( $term->term_id, 'product_cat' ) !== '' ) {
							$is_valid = true;
						} else {
							$is_valid = false;
							break;
						}
					}
					break;
				case 'categories_list_excluded':
					$is_valid        = true;
					$categories_list = $product_list;
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$categories_of_item = wc_get_product_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => $get_by ) );
						$intersect          = array_intersect( $categories_of_item, $categories_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = false;
						}
					}

					break;
				case 'tags_list':
					$tags_list = $product_list;
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$tags_of_item = wc_get_product_terms( $cart_item['product_id'], 'product_tag', array( 'fields' => $get_by ) );
						$intersect    = array_intersect( $tags_of_item, $tags_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = true;
						}
					}
					break;
				case 'tags_list_and':
					$tags_list = $product_list;
					foreach ( $tags_list as $tag_id ) {
						$term = get_term_by( $search_by, $tag_id, 'product_tag' );
						if ( is_a( $term, 'WP_Term' ) && $this->find_taxonomy_in_cart( $term->term_id, 'product_tag' ) !== '' ) {
							$is_valid = true;
						} else {
							$is_valid = false;
							break;
						}
					}
					break;
				case 'tags_list_excluded':
					$is_valid  = true;
					$tags_list = $product_list;
					foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
						$tags_of_item = wc_get_product_terms( $cart_item['product_id'], 'product_tag', array( 'fields' => $get_by ) );
						$intersect    = array_intersect( $tags_of_item, $tags_list );
						if ( ! empty( $intersect ) ) {
							$is_valid = false;
						}
					}
					break;
				default:
					$is_valid = apply_filters( 'ywdpd_validate_product_in_cart', $is_valid, $type, $product_list );
			}

			return $is_valid;
		}

		/**
		 * Check if in the cart there a taxonomy
		 *
		 * @param integer $taxonomy_id Taxonomy id.
		 * @param string $taxonomy Taxonomy.
		 *
		 * @return int|string
		 */
		public function find_taxonomy_in_cart( $taxonomy_id, $taxonomy ) {
			$is_in_cart = '';
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				$taxonomy_of_item = wc_get_product_terms( $cart_item['product_id'], $taxonomy, array( 'fields' => 'ids' ) );

				if ( ! empty( $taxonomy_of_item ) && in_array( $taxonomy_id, $taxonomy_of_item ) ) {
					$is_in_cart = $cart_item_key;
				}
			}

			return $is_in_cart;

		}

		/**
		 * Check if a product is in cart
		 *
		 * @param integer $product_id Product id.
		 *
		 * @return int|string
		 */
		public function find_product_in_cart( $product_id ) {
			$is_in_cart = '';

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				if ( ( isset( $cart_item['variation_id'] ) && $product_id == $cart_item['variation_id'] ) || $product_id == $cart_item['product_id'] ) {
					$is_in_cart = $cart_item_key;
				}
			}

			return $is_in_cart;
		}

		/**
		 * Return the number of valid product to adjust
		 *
		 * @param array $rule Rule.
		 * @param array $cart_item Cart Item.
		 * @param bool $clean Bool.
		 *
		 * @return int|string
		 * @since 1.1.0
		 */
		public function num_valid_product_to_adjust_in_cart( $rule, $cart_item, $clean = false ) {
			$num        = 0;
			$product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
			$product    = wc_get_product( $product_id );

			if ( in_array( $rule['quantity_based'], array( 'cart_line', 'single_variation_product' ) )
			     || ( 'single_product' === $rule['quantity_based'] && ! $product->is_type( 'variation' ) )
			) {
				if ( $clean ) {
					if ( $this->valid_product_to_apply( $rule, $cart_item['data'], true ) && ! $this->valid_product_to_adjust( $rule, $cart_item ) ) {
						$num = $cart_item['available_quantity'];
					}
				} else {
					if ( $this->valid_product_to_apply( $rule, $cart_item['data'], true ) && $this->valid_product_to_adjust( $rule, $cart_item ) ) {
						$num = isset( $cart_item['available_quantity'] ) ? $cart_item['available_quantity'] : 1;
					}
				}
			} elseif ( 'single_product' === $rule['quantity_based'] && $product->is_type( 'variation' ) ) {
				$parent_id = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : $product->post->ID;

				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_it ) {
					if ( $cart_it['variation_id'] && $parent_id === $cart_it['product_id'] ) {
						if ( $clean ) {
							if ( $this->valid_product_to_adjust( $rule, $cart_it ) && ! $this->valid_product_to_apply( $rule, $cart_it['data'], true ) ) {
								$num += $cart_it['quantity'];
							}
						} else {
							if ( $this->valid_product_to_adjust( $rule, $cart_it ) && $this->valid_product_to_apply( $rule, $cart_it['data'], true ) ) {
								$num += $cart_it['quantity'];
							}
						}
					}
				}
			} else {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_it ) {
					if ( $clean ) {
						if ( $this->valid_product_to_adjust( $rule, $cart_it ) && ! $this->valid_product_to_apply( $rule, $cart_it['data'], true ) ) {
							$num += $cart_it['quantity'];
						}
					} else {
						if ( $this->valid_product_to_adjust( $rule, $cart_it ) && $this->valid_product_to_apply( $rule, $cart_it['data'], true ) ) {
							$num += $cart_it['quantity'];
						}
					}
				}
			}

			return $num;
		}

		/**
		 * Return the number of valid product to adjust
		 *
		 * @param array $rule Rule.
		 * @param array $cart_item Cart Item.
		 * @param bool $clean Bool.
		 *
		 * @return int|string
		 * @since 1.1.0
		 */
		public function num_valid_product_to_apply_in_cart( $rule, $cart_item, $clean = false ) {
			$num        = 0;
			$product_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
			$product    = wc_get_product( $product_id );

			if ( in_array( $rule['quantity_based'], array( 'cart_line', 'single_variation_product' ) )
			     || ( 'single_product' === $rule['quantity_based'] && ! $product->is_type( 'variation' ) )
			) {
				if ( $clean ) {
					if ( $this->valid_product_to_apply( $rule, $cart_item['data'], true ) && ! $this->valid_product_to_adjust( $rule, $cart_item ) ) {
						$num = $cart_item['available_quantity'];
					}
				} else {
					if ( $this->valid_product_to_apply( $rule, $cart_item['data'], true ) && $this->valid_product_to_adjust( $rule, $cart_item ) ) {
						$num = isset( $cart_item['available_quantity'] ) ? $cart_item['available_quantity'] : 1;
					}
				}
			} elseif ( 'single_product' === $rule['quantity_based'] && $product->is_type( 'variation' ) ) {

				$parent_id = yit_get_base_product_id( $product );

				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_it ) {

					if ( $cart_it['variation_id'] && $parent_id === $cart_it['product_id'] ) {
						if ( $clean ) {
							if ( $this->valid_product_to_apply( $rule, $cart_it['data'], true ) && ! $this->valid_product_to_adjust( $rule, $cart_it ) ) {
								$num += $cart_it['quantity'];
							}
						} else {
							if ( $this->valid_product_to_apply( $rule, $cart_it['data'], true ) && $this->valid_product_to_adjust( $rule, $cart_it ) ) {
								$num += $cart_it['quantity'];
							}
						}
					}
				}
			} else {
				foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_it ) {
					if ( $clean ) {
						if ( $this->valid_product_to_apply( $rule, $cart_it['data'], true ) && ! $this->valid_product_to_adjust( $rule, $cart_it ) ) {
							$num += $cart_it['available_quantity'];
						}
					} else {
						if ( $this->valid_product_to_apply( $rule, $cart_it['data'], true ) && $this->valid_product_to_adjust( $rule, $cart_it ) ) {
							$num += $cart_it['available_quantity'];
						}
					}
				}
			}

			return $num;
		}

		/**
		 * Check if the product of the cart item is in a list
		 *
		 * @param array $cart_item Cart Item.
		 * @param array $product_list Product list.
		 *
		 * @return bool
		 * @since 1.1.0
		 */
		public function product_in_list( $cart_item, $product_list ) {
			return ( ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] && in_array( $cart_item['variation_id'], $product_list ) ) || in_array( $cart_item['product_id'], $product_list ) );
		}

		/**
		 * Sorting.
		 *
		 * @param array $cart_item_a Cart item.
		 * @param array $cart_item_b Cart item.
		 *
		 * @return bool
		 */
		public static function sort_by_price( $cart_item_a, $cart_item_b ) {
			return $cart_item_a['data']->get_price() > $cart_item_b['data']->get_price();
		}

		/**
		 * Sorting descendant.
		 *
		 * @param array $cart_item_a Cart item.
		 * @param array $cart_item_b Cart item.
		 *
		 * @return bool
		 */
		public static function sort_by_price_desc( $cart_item_a, $cart_item_b ) {
			return $cart_item_a['data']->get_price() < $cart_item_b['data']->get_price();
		}

		/**
		 * Check taxonomy.
		 *
		 * @param array $list List.
		 * @param string $item Term.
		 * @param string $taxonomy_name Taxonomy.
		 * @param bool $in Bool.
		 *
		 * @return bool
		 */
		public function check_taxonomy( $list, $item, $taxonomy_name, $in = true ) {
			$excluded     = false;
			$get_by       = is_numeric( $list[0] ) ? 'ids' : 'slugs';
			$list_of_item = wc_get_product_terms( $item, $taxonomy_name, array( 'fields' => $get_by ) );
			$intersect    = array_intersect( $list_of_item, $list );
			if ( ! empty( $intersect ) ) {
				$excluded = true;
			}

			return $in ? $excluded : ! $excluded;
		}

		/**
		 * Check the quantity products of a specific taxonomy.
		 *
		 * @param array $list List.
		 * @param string $taxonomy_name Taxonomy
		 * @param bool $in Bool.
		 *
		 * @return int
		 */
		public function check_taxonomy_quantity( $list, $taxonomy_name, $in = true ) {

			$quantity = 0;
			$get_by   = is_numeric( $list[0] ) ? 'ids' : 'slugs';
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				$list_of_item = wc_get_product_terms( $cart_item['product_id'], $taxonomy_name, array( 'fields' => $get_by ) );
				$intersect    = array_intersect( $list_of_item, $list );
				$check        = $in ? ! empty( $intersect ) : empty( $intersect );
				if ( $check ) {
					$quantity += $cart_item['quantity'];
				}
			}

			return $quantity;
		}

		/**
		 * Check cart item exclusion.
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return mixed
		 */
		public function check_cart_item_filter_exclusion( $cart_item ) {
			if ( isset( $cart_item['product_id'] ) ) {
				$product = wc_get_product( $cart_item['product_id'] );

				return apply_filters( 'ywdpd_exclude_products_from_discount', false, $product );
			}
		}


		/**
		 * WPML adjust.
		 *
		 * @param array $list List.
		 * @param string $type_of_list Type of list.
		 *
		 * @return mixed
		 */
		public function wpml_product_list_adjust( $list, $type_of_list ) {
			if ( $list ) {
				$trans_products = $list;
				$object_type    = false;
				switch ( $type_of_list ) {
					case 'all_products':
					case 'products_list':
					case 'products_list_and':
					case 'products_list_excluded':
						$object_type = 'product';
						break;
					case 'categories_list':
					case 'categories_list_excluded':
					case 'categories_list_and':
						$object_type = 'product_cat';
						break;
					case 'tags_list':
					case 'tags_list_and':
					case 'tags_list_excluded':
						$object_type = 'product_tag';
						break;
					default:
						apply_filters( 'wpml_product_list_adjust_type_of_list', $object_type, $type_of_list );
				}

				if ( $object_type ) {
					foreach ( $list as $product_id ) {
						$p = wpml_object_id_filter( $product_id, $object_type, true, wpml_get_current_language() );

						if ( ! in_array( $p, $trans_products ) ) {
							$trans_products[] = $p;
						}
					}
					$list = $list + $trans_products;
				}
			}

			return $list;
		}

	}
}


/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Helper class
 *
 * @return YITH_WC_Dynamic_Pricing_Helper
 */
function YITH_WC_Dynamic_Pricing_Helper() {
	return YITH_WC_Dynamic_Pricing_Helper::get_instance();
}

YITH_WC_Dynamic_Pricing_Helper();
