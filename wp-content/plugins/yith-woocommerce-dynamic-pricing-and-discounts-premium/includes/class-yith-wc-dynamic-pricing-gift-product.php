<?php
/**
 * Gift products class
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WC_Dynamic_Pricing_Gift_Product' ) ) {

	/**
	 * Class YITH_WC_Dynamic_Pricing_Gift_Product
	 */
	class YITH_WC_Dynamic_Pricing_Gift_Product {

		/**
		 * Instance.
		 * @var YITH_WC_Dynamic_Pricing_Gift_Product
		 */
		protected static $instance;

		/**
		 * List of gift rules.
		 * @var array
		 */
		private $gift_rules = array();

		/**
		 * List of rules to apply.
		 * @var array
		 */
		private $gift_rules_to_apply = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'load_gift_rules' ), 20 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'check_if_apply_rules' ), 25 );
			add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'check_if_apply_rules' ), 25 );
			add_action( 'woocommerce_after_cart', array( $this, 'print_gift_product' ), 25 );
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'update_gift_products' ), 20, 1 );
			add_action( 'ywdpd_before_cart_process_discounts', array(
				$this,
				'update_gift_products_before_cart_process_discount'
			), 20 );

			add_action( 'wp_loaded', array( $this, 'add_to_cart_gift_product' ), 25 );

			add_filter( 'woocommerce_add_cart_item', array( $this, 'change_price_gift_product' ), 20, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'change_price_gift_product' ), 30, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_popup_scripts' ), 20 );

			add_action( 'wp_ajax_show_second_step', array( $this, 'show_second_step' ), 20 );
			add_action( 'wp_ajax_nopriv_show_second_step', array( $this, 'show_second_step' ), 20 );

			add_action( 'wp_ajax_add_gift_to_cart', array( $this, 'add_gift_to_cart' ) );
			add_action( 'wp_ajax_nopriv_add_gift_to_cart', array( $this, 'add_gift_to_cart' ) );

			add_action( 'wp_ajax_check_variable', array( $this, 'check_variable' ) );
			add_action( 'wp_ajax_nopriv_check_variable', array( $this, 'check_variable' ) );

			add_filter( 'ywdpd_show_note_apply_to', array( $this, 'show_notes_on_product' ), 10, 3 );
			add_filter( 'ywdpd_show_note_on_sale', array( $this, 'show_also_on_sale' ), 10, 2 );


			add_filter( 'yith_dynamic_valid_sum_item_quantity', array(
				$this,
				'valid_sum_item_quantity_not_gifts'
			), 10, 1 );
			add_filter( 'yith_dynamic_valid_sum_item_quantity_less', array(
				$this,
				'valid_sum_item_quantity_not_gifts'
			), 10, 1 );
			add_filter( 'ywdpd_get_cart_item_quantities', array( $this, 'valid_sum_item_quantity_not_gifts' ), 10, 1 );

		}

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WC_Dynamic_Pricing_Gift_Product
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Load the gift rules
		 */
		public function load_gift_rules() {
			$this->gift_rules = YITH_WC_Dynamic_Pricing()->get_gift_rules();
		}

		/**
		 * Check if the rule can be applied.
		 */
		public function check_if_apply_rules() {

			if ( ! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) {

				$no_gift_products = $this->get_cart_products();

				if ( count( $no_gift_products ) > 0 ) {
					foreach ( $no_gift_products as $cart_item_key => $cart_item ) {
						foreach ( $this->gift_rules as $key => $rule ) {

							if ( $this->apply_to_is_valid( $rule, $cart_item ) ) {

								$this->gift_rules_to_apply[ $key ] = $rule;
							}
						}
					}
				} else {
					// remove all gift products.
					$this->remove_all_gift_product();
				}
			}
		}

		/**
		 * Remove all gift product in the cart
		 */
		public function remove_all_gift_product() {

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );
				if ( $is_gift_product ) {

					/**
					 * Product.
					 * @var WC_Product $product
					 */
					$product = $cart_item['data'];
					WC()->cart->remove_cart_item( $cart_item_key );
					/* translators: name of product */
					wc_add_notice( sprintf( __( 'Gift %s removed properly', 'ywdpd' ), $product->get_formatted_name() ) );
				}
			}
		}

		/**
		 * Remove gift product from cart.
		 *
		 * @param integer $rule_id Rule id.
		 */
		public function remove_gift_product( $rule_id ) {

			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );
				$cart_rule_id    = isset( $cart_item['ywdpd_rule_id'] ) ? $cart_item['ywdpd_rule_id'] : false;

				if ( $is_gift_product && $cart_rule_id == $rule_id ) {
					$product = $cart_item['data'];
					WC()->cart->remove_cart_item( $cart_item_key );
					/* translators: name of product */
					wc_add_notice( sprintf( __( 'Gift %s removed properly', 'ywdpd' ), $product->get_formatted_name() ) );
				}
			}
		}

		/**
		 * Calculate how many gift are on cart.
		 *
		 * @param integer $rule_id Rule id.
		 *
		 * @return int|mixed
		 */
		public function get_total_gift_product_by_rule( $rule_id ) {
			$total = 0;
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );
				$cart_rule_id    = isset( $cart_item['ywdpd_rule_id'] ) ? $cart_item['ywdpd_rule_id'] : false;

				if ( $is_gift_product && $cart_rule_id == $rule_id ) {

					$total += $cart_item['quantity'];
				}
			}

			return $total;
		}

		/**
		 * Check if on cart there's a gift
		 *
		 * @param integer $rule_id Rule id.
		 * @param int $product_id Product id.
		 *
		 * @return bool
		 */
		public function is_gift_product_in_cart( $rule_id, $product_id ) {

			$found = false;
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );
				$cart_rule_id    = isset( $cart_item['ywdpd_rule_id'] ) ? $cart_item['ywdpd_rule_id'] : false;
				if ( $is_gift_product && $cart_rule_id == $rule_id ) {

					$data_product_id = $cart_item['data']->get_id();

					if ( $data_product_id == $product_id ) {
						return true;
					}
				}
			}

			return $found;
		}

		/**
		 * Get the product on cart that aren't gift.
		 *
		 * @return array
		 */
		public function get_cart_products() {

			$products_in_cart = array();
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );

				if ( ! $is_gift_product ) {

					$products_in_cart[ $cart_item_key ] = $cart_item;
				}
			}

			return $products_in_cart;
		}

		/**
		 * Get total item on cart.
		 *
		 * @param array $rule Rule.
		 *
		 * @return int
		 */
		public function get_total_item_in_cart( $rule ) {

			$cart_item_total = 0;
			foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );

				if ( ! $is_gift_product ) {
					switch ( $rule['apply_to'] ) {
						case 'all_products':
							$cart_item_total += $cart_item['quantity'];
							break;
						case 'products_list':
							$product_list = $rule['apply_to_products_list'];
							if ( YITH_WC_Dynamic_Pricing_Helper()->product_in_list( $cart_item, $product_list ) ) {
								$cart_item_total += $cart_item['quantity'];
							}
							break;
						case 'products_list_excluded':
							$product_list = $rule['apply_to_products_list_excluded'];
							if ( ! YITH_WC_Dynamic_Pricing_Helper()->product_in_list( $cart_item, $product_list ) ) {
								$cart_item_total += $cart_item['quantity'];
							}
							break;
						case 'categories_list':
							$cart_item_total = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_categories_list'], 'product_cat' );
							break;
						case 'categories_list_excluded':
							$cart_item_total = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_categories_list_excluded'], 'product_cat', false );
							break;
						case 'tags_list':
							$cart_item_total = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_tags_list'], 'product_tag' );
							break;
						case 'tags_list_excluded':
							$cart_item_total = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy_quantity( $rule['apply_to_tags_list_excluded'], 'product_tag', false );
							break;
						case 'vendor_list':
							if ( ! class_exists( 'YITH_Vendors' ) ) {
								break;
							}
							$vendor_list    = array_map( 'intval', $rule['apply_to_vendors_list'] );
							$vendor_of_item = wc_get_product_terms( $cart_item['product_id'], YITH_Vendors()->get_taxonomy_name(), array( 'fields' => 'ids' ) );
							$intersect      = array_intersect( $vendor_of_item, $vendor_list );
							if ( ! empty( $intersect ) ) {
								$cart_item_total += $cart_item['quantity'];
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
								$cart_item_total += $cart_item['quantity'];
							}

							break;

					}
				}
			}

			return $cart_item_total;
		}

		/**
		 * Apply the rule if it is valid.
		 *
		 * @param array $rule Rule.
		 * @param array $cart_item Cart item.
		 *
		 * @return boolean
		 */
		public function apply_to_is_valid( $rule, $cart_item ) {

			$is_valid = 'all_products' == $rule['apply_to'];

			if ( ! $is_valid ) {

				switch ( $rule['apply_to'] ) {

					case 'products_list':
						if ( isset( $rule['apply_to_products_list'] ) ) {

							$product_list = $rule['apply_to_products_list'];

							if ( is_array( $product_list ) && YITH_WC_Dynamic_Pricing_Helper()->product_in_list( $cart_item, $product_list ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'products_list_excluded':
						if ( isset( $rule['apply_to_products_list_excluded'] ) ) {
							$product_list = $rule['apply_to_products_list_excluded'];
							if ( is_array( $product_list ) && ! YITH_WC_Dynamic_Pricing_Helper()->product_in_list( $cart_item, $product_list ) ) {
								$is_valid = true;
							}
						}
						break;
					case 'categories_list':
						if ( isset( $rule['apply_to_categories_list'] ) ) {
							$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule[ 'apply_to_' . $rule['apply_to'] ], $cart_item['product_id'], 'product_cat' );
						}
						break;
					case 'categories_list_excluded':
						if ( isset( $rule['apply_to_categories_list_excluded'] ) ) {
							$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_categories_list_excluded'], $cart_item['product_id'], 'product_cat', false );
							break;
						}
						break;
					case 'tags_list':
						if ( isset( $rule['apply_to_tags_list'] ) ) {
							$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_tags_list'], $cart_item['product_id'], 'product_tag' );
						}
						break;
					case 'tags_list_excluded':
						if ( isset( $rule['apply_to_tags_list_excluded'] ) ) {
							$is_valid = YITH_WC_Dynamic_Pricing_Helper()->check_taxonomy( $rule['apply_to_tags_list_excluded'], $cart_item['product_id'], 'product_tag', false );
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

				// check how many product are in the cart.

			}
			if ( $is_valid ) {

				$need_items_in_cart = $rule['n_items_in_cart']['n_items'];
				$criteria           = $rule['n_items_in_cart']['condition'];
				$items_in_cart      = $this->get_total_item_in_cart( $rule );

				switch ( $criteria ) {
					case '>':
						$is_valid = $items_in_cart > $need_items_in_cart;
						break;
					case '<':
						$is_valid = $items_in_cart < $need_items_in_cart;
						break;
					case '==':
						$is_valid = $items_in_cart == $need_items_in_cart;
						break;
					default:
						$is_valid = $items_in_cart != $need_items_in_cart;
						break;

				}
			}

			return apply_filters( 'ywdpd_apply_to_is_valid', $is_valid );
		}

		/**
		 * Print the gift product.
		 */
		public function print_gift_product() {

			if ( count( $this->gift_rules_to_apply ) > 0 ) {

				$gift_rules_valid = array();

				foreach ( $this->gift_rules_to_apply as $key => $rule ) {
					$items_in_cart = $this->get_total_gift_product_by_rule( $key );
					$allowed_item  = $rule['amount_gift_product_allowed'];

					if ( $items_in_cart < $allowed_item ) {
						$gift_rules_valid[ $key ] = $rule;
					}
				}

				if ( count( $gift_rules_valid ) > 0 ) {
					wc_get_template( 'yith_ywdpd_popup.php', array( 'gift_rules_to_apply' => $gift_rules_valid ), YITH_YWDPD_TEMPLATE_PATH, YITH_YWDPD_TEMPLATE_PATH );
				}
			}
		}

		/**
		 * Add to cart the gift product.
		 *
		 * @throws Exception Return an error.
		 */
		public function add_to_cart_gift_product() {

			$posted = $_REQUEST;

			if ( isset( $posted['ywdpd_add_to_cart_gift'] ) ) {

				$product = wc_get_product( $posted['ywdpd_add_to_cart_gift'] );
				$rule_id = isset( $posted['ywdpd_rule_id'] ) ? $posted['ywdpd_rule_id'] : false;

				$total_items_in_cart = $this->get_total_gift_product_by_rule( $rule_id );

				$allowed_items = $this->gift_rules_to_apply[ $rule_id ]['amount_gift_product_allowed'];

				if ( $rule_id && isset( $this->gift_rules_to_apply[ $rule_id ] ) && $total_items_in_cart + 1 <= $allowed_items ) {

					if ( $product->is_type( 'variation' ) ) {
						$product_id   = $product->get_parent_id();
						$variation_id = $product->get_id();
					} else {
						$product_id   = $product->get_id();
						$variation_id = 0;
					}

					WC()->cart->add_to_cart(
						$product_id,
						1,
						$variation_id,
						array(),
						array(
							'ywdpd_is_gift_product' => true,
							'ywdpd_rule_id'         => $rule_id,
						)
					);
				}
			}
		}

		/**
		 * Add gift to cart.
		 *
		 * @throws Exception Get the error.
		 */
		public function add_gift_to_cart() {

			$posted = $_REQUEST;

			if ( isset( $posted['rule_id'] ) ) {

				$this->load_gift_rules();
				$this->check_if_apply_rules();

				$rule_id             = $posted['rule_id'];
				$variations          = array();
				$variation_id        = 0;
				$product_id          = $posted['product_id'];
				$product             = wc_get_product( $product_id );
				$total_items_in_cart = $this->get_total_gift_product_by_rule( $rule_id );

				$allowed_items = $this->gift_rules_to_apply[ $rule_id ]['amount_gift_product_allowed'];

				if ( $rule_id && isset( $this->gift_rules_to_apply[ $rule_id ] ) && $total_items_in_cart + 1 <= $allowed_items ) {

					if ( isset( $posted['variations'] ) && ( isset( $posted['variation_id'] ) && $posted['variation_id'] > 0 ) ) {
						$variations   = $posted['variations'];
						$variation_id = $posted['variation_id'];
						$product      = wc_get_product( $variation_id );
					}

					WC()->cart->add_to_cart(
						$product_id,
						1,
						$variation_id,
						$variations,
						array(
							'ywdpd_is_gift_product' => true,
							'ywdpd_rule_id'         => $rule_id,
							'ywdpd_time'            => time(),
						)
					);
					/* translators: name of product */
					wc_add_notice( sprintf( __( 'Gift %s added properly', 'ywdpd' ), $product->get_formatted_name() ) );

				}
			}
		}

		/**
		 * Change gift product price.
		 *
		 * @param WC_Product $cart_item_data Cart item data.
		 * @param string $cart_item_key Cart item key.
		 *
		 * @return mixed
		 */
		public function change_price_gift_product( $cart_item_data, $cart_item_key ) {

			if ( isset( $cart_item_data['ywdpd_is_gift_product'] ) ) {

				$cart_item_data['data']->set_price( 0 );
				$cart_item_data['data']->set_sold_individually( true );
				$cart_item_data['data']->update_meta_data( 'has_dynamic_price', true );

			}

			return $cart_item_data;
		}

		/**
		 * Enqueue scripts and style
		 */
		public function enqueue_popup_scripts() {

			wp_register_script( 'ywdpd_popup', YITH_YWDPD_ASSETS_URL . '/js/' . yit_load_js_file( 'ywdpd-gift-popup.js' ), array( 'jquery' ), YITH_YWDPD_VERSION, true );
			wp_register_script( 'ywdpd_owl', YITH_YWDPD_ASSETS_URL . '/js/owl/owl.carousel.min.js', array( 'jquery' ), YITH_YWDPD_VERSION, true );
			wp_register_style( 'ywdpd_owl', YITH_YWDPD_ASSETS_URL . '/css/owl/owl.carousel.min.css', array(), YITH_YWDPD_VERSION );
			wp_register_style( 'ywdpd_owl_theme', YITH_YWDPD_ASSETS_URL . '/css/owl/owl.carousel.min.css', array(), YITH_YWDPD_VERSION );

			$args = array(
				'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
				'actions'  => array(
					'add_gift_to_cart' => 'add_gift_to_cart',
					'show_second_step' => 'show_second_step',
					'check_variable'   => 'check_variable',
				),
			);
			wp_localize_script( 'ywdpd_popup', 'ywdpd_popup_args', $args );

			if ( is_cart() && count( $this->gift_rules ) > 0 ) {

				wp_enqueue_script( 'ywdpd_popup' );
				$params = array(
					'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
					'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
					'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
				);

				wp_enqueue_script(
					'wc-add-to-cart-variation',
					WC()->plugin_url() . 'assets/js/frontend/add-to-cart-variation.min.js',
					array(
						'jquery',
						'wp-util',
						'jquery-blockui',
					),
					WC()->version
				);

				wp_localize_script( 'wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', $params );

				wp_enqueue_script( 'ywdpd_owl' );
				wp_enqueue_style( 'ywdpd_owl_theme' );
				wp_enqueue_style( 'ywdpd_owl' );

			}
		}

		/**
		 * Second step.
		 */
		public function show_second_step() {

			$posted = $_REQUEST;
			if ( isset( $posted['product_id'] ) ) {

				$args = array(
					'product_id' => $posted['product_id'],
					'rule_id'    => $posted['rule_id'],
				);

				ob_start();
				wc_get_template( 'yith_ywdpd_popup_single_product.php', $args, YITH_YWDPD_TEMPLATE_PATH, YITH_YWDPD_TEMPLATE_PATH );
				$template = ob_get_contents();
				ob_end_clean();

				wp_send_json( array( 'template' => $template ) );
			}
		}

		/**
		 * Update gift product.
		 *
		 * @param bool $updated Bool.
		 *
		 * @return mixed
		 */
		public function update_gift_products( $updated ) {

			$rule_to_remove = array();
			foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) {

				$rule_id = isset( $cart_item['ywdpd_rule_id'] ) ? $cart_item['ywdpd_rule_id'] : false;

				if ( $rule_id && ! in_array( $rule_id, $rule_to_remove ) ) {

					$rule = isset( $this->gift_rules_to_apply[ $rule_id ] ) ? $this->gift_rules_to_apply[ $rule_id ] : false;

					if ( ! $rule || ! $this->check_valid_single_rule( $rule ) ) {
						$rule_to_remove[] = $rule_id;
						$this->remove_gift_product( $rule_id );
					}
				}
			}

			return $updated;
		}

		/**
		 * Update gift product before process the discount.
		 */
		public function update_gift_products_before_cart_process_discount() {

			$posted = $_REQUEST;

			if ( ! empty( $posted['remove_item'] ) ) {

				$cart_item_key = $posted['remove_item'];

				$cart_item = WC()->cart->get_cart_item( $cart_item_key );

				$is_gift_product = isset( $cart_item['ywdpd_is_gift_product'] );

				if ( ! $is_gift_product ) {

					foreach ( $this->gift_rules as $key => $rule ) {

						if ( $this->apply_to_is_valid( $rule, $cart_item ) ) {

							$this->remove_gift_product( $key );
						}
					}
				}
			}
		}

		/**
		 * Check if a rule is valid
		 *
		 * @param array $rule Rule.
		 *
		 * @return bool
		 * @author YITH
		 * @since 1.6.0
		 */
		public function check_valid_single_rule( $rule ) {

			$products_in_cart = $this->get_cart_products();

			$valid = false;
			foreach ( $products_in_cart as $cart_item_key => $cart_item ) {

				if ( $this->apply_to_is_valid( $rule, $cart_item ) ) {
					return true;
				}
			}

			return $valid;
		}

		/**
		 * Check if a variation is already added as gift for a rule
		 *
		 * @author YITH
		 * @since 1.6.0
		 */
		public function check_variable() {
			$posted = $_REQUEST;
			if ( isset( $posted['ywdp_check_rule_id'] ) && isset( $posted['product_id'] ) ) {

				$rule_id    = $posted['ywdp_check_rule_id'];
				$product_id = $posted['product_id'];
				$find       = false;
				if ( ! is_null( WC()->cart ) ) {

					foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) {

						$cart_rule_id = isset( $cart_item['ywdpd_rule_id'] ) ? $cart_item['ywdpd_rule_id'] : false;

						if ( $cart_rule_id && $rule_id == $cart_rule_id && $product_id == $cart_item['variation_id'] ) {
							$find = true;
							break;
						}
					}
				}

				wp_send_json( array( 'variation_found' => $find ) );
			}

		}

		/**
		 * Check if is possibles show  notes on the product
		 *
		 * @param boolean $show Boolean.
		 * @param array $rule Rule.
		 * @param WC_Product $product Product.
		 *
		 * @return boolean
		 * @author YITH
		 * @since 1.6.0
		 */
		public function show_notes_on_product( $show, $rule, $product ) {

			if ( 'gift_products' == $rule['discount_mode'] && YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply( $rule, $product ) ) {
				$show = true;
			}

			return $show;
		}

		/**
		 * Show also on sale.
		 *
		 * @param boolean $show Boolean.
		 * @param array $rule Rule.
		 *
		 * @return boolean
		 * @since 1.6.0
		 * @author YITH
		 */
		public function show_also_on_sale( $show, $rule ) {
			if ( 'gift_products' == $rule['discount_mode'] ) {
				$show = true;
			}

			return $show;
		}

		/**
		 * @param int $num_items
		 *
		 * @return int
		 */
		public function valid_sum_item_quantity_not_gifts( $num_items ) {
			$gift_qty = 0;

			foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) {

				if ( isset( $cart_item['ywdpd_is_gift_product'] ) ) {
					$gift_qty += 1;
				}
			}

			if ( is_array( $num_items ) ) {
				$num_items = array_sum( $num_items );
			}

			return $num_items - $gift_qty;
		}
	}

}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Gift_Product class
 *
 * @return YITH_WC_Dynamic_Pricing_Gift_Product
 */
function YITH_WC_Dynamic_Pricing_Gift_Product() {
	return YITH_WC_Dynamic_Pricing_Gift_Product::get_instance();
}

YITH_WC_Dynamic_Pricing_Gift_Product();
