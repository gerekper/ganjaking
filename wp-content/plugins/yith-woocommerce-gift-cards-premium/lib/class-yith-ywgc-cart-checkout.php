<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_YWGC_Cart_Checkout' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Cart_Checkout
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Cart_Checkout {

		const ORDER_GIFT_CARDS = '_ywgc_applied_gift_cards';
		const ORDER_GIFT_CARDS_TOTAL = '_ywgc_applied_gift_cards_totals';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
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
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {

			$this->includes();
			$this->init_hooks();
		}

		public function includes() {

		}

		public function init_hooks() {

			/**
			 * set the price when a gift card product is added to the cart
			 */
			add_filter( 'woocommerce_add_cart_item', array(
				$this,
				'set_price_in_cart'
			), 10, 2 );

			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'get_cart_item_from_session'
			), 10, 2 );

			/**
			 *  Let the user to edit the gift card content
			 */
			add_action( 'wp_ajax_edit_gift_card', array(
				$this,
				'edit_gift_card_callback'
			) );

			add_action( 'wp_ajax_no_priv_edit_gift_card', array(
				$this,
				'edit_gift_card_callback'
			) );


			add_action( 'woocommerce_new_order_item', array( $this, 'append_gift_card_data_to_new_order_item' ), 10, 2 );


			/**
			 * Custom add_to_cart handler for gift card product type
			 */
			add_action( 'woocommerce_add_to_cart_handler_gift-card', array(
				$this,
				'add_to_cart_handler'
			) );


			add_filter( 'woocommerce_add_to_cart_validation', array( $this,'prevent_gift_card_and_physical_products_in_the_same_cart' ),10,6 );

			/**
			 * Show gift card details in cart page
			 */

			if (  "yes" == get_option ( 'ywgc_show_recipient_on_cart' , 'no' ) ) {
				add_filter( 'woocommerce_get_item_data', array(
					$this,
					'show_gift_card_details_in_cart'
				), 10, 2 );
			}


			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_apply_gift_card_code', array(
				$this,
				'apply_gift_card_code_callback'
			) );
			add_action( 'wp_ajax_nopriv_ywgc_apply_gift_card_code', array(
				$this,
				'apply_gift_card_code_callback'
			) );

			/* Ajax action for applying a gift card to the cart */
			add_action( 'wp_ajax_ywgc_remove_gift_card_code', array(
				$this,
				'remove_gift_card_code_callback'
			) );
			add_action( 'wp_ajax_nopriv_ywgc_remove_gift_card_code', array(
				$this,
				'remove_gift_card_code_callback'
			) );
			/**
			 * Apply the discount to the cart using the gift cards submitted, is any exists.
			 */
			add_action( 'woocommerce_after_calculate_totals', array(
				$this,
				'apply_gift_cards_discount'
			), 20 );

			/*
			 * Compatibility with YITH WooCommerce Subscription Premium
			 */
			if( defined('YITH_YWSBS_PREMIUM') ){
				add_action( 'ywcsb_after_calculate_totals', array( $this, 'apply_gift_cards_discount_for_subscriptions' ), 10 );
			}

			/**
			 * Show gift card amount usage on cart totals - checkout page
			 */
			add_action( 'woocommerce_review_order_before_order_total', array(
				$this,
				'show_gift_card_amount_on_cart_totals'
			) );

			/**
			 * Show gift card amount usage on cart totals - cart page
			 */
			add_action( 'woocommerce_cart_totals_before_order_total', array(
				$this,
				'show_gift_card_amount_on_cart_totals'
			) );

			add_action( 'woocommerce_new_order', array(
				$this,
				'register_gift_cards_usage'
			) );

			add_filter( 'woocommerce_get_order_item_totals', array(
				$this,
				'show_gift_cards_total_applied_to_order'
			), 10, 2 );

			add_action( 'wp_loaded', array(
				$this,
				'check_email_discount'
			) );

			add_filter( 'woocommerce_is_purchasable', array(
				$this,
				'gift_card_is_purchasable'
			), 10, 2 );

			/**
			 * Show gift card details in cart page
			 */

			add_filter( 'woocommerce_cart_item_thumbnail', array(
				$this,
				'ywgc_custom_cart_product_image'
			), 10, 3 );

			/* Cart and Checkout */
			add_filter( 'woocommerce_cart_item_name', array( $this, 'show_this_product_as_a_gift_card' ), 10, 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'show_this_product_as_a_gift_card' ), 10, 3 );


			if ( get_option( 'ywgc_apply_gift_card_on_coupon_form' , 'no' ) == 'yes' ){
				add_action( 'init', array( $this, 'ywgc_apply_gift_card_on_coupon_form' ) );
			}


			add_action( 'woocommerce_checkout_order_processed', array($this,'add_item_fee'));


		}

		public function ywgc_apply_gift_card_on_coupon_form() {

			add_action('woocommerce_after_calculate_totals', array( $this, 'ywgc_allow_shipping_in_coupons') );

			/**
			 * Verify if a coupon code inserted on cart page or checkout page belong to a valid gift card.
			 * In this case, make the gift card working as a temporary coupon
			 */
			add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'verify_coupon_code' ), 10, 2 );


			add_action( 'woocommerce_new_order_item', array( $this, 'deduct_amount_from_gift_card_wc_3_plus'), 10, 3 );


		}

		/**
		 * Verify the gift card value
		 *
		 * @param array  $return_val the returning value
		 * @param string $code       the gift card code
		 *
		 * @return array
		 * @author Daniel Sanchez
		 * @since  2.0.4
		 */
		public function verify_coupon_code( $return_val, $code ) {

			$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

			if ( apply_filters( 'ywgc_verify_coupon_code_condition', false, $return_val, $code ) ){
				return $return_val;
			}

			if ( $gift_card->exists() && get_option( 'ywgc_apply_gc_code_on_gc_product', 'no' )  == 'yes' && is_cart() ){

				$items = WC()->cart->get_cart();

				foreach ( $items as $cart_item_key => $values ) {
					$product = $values['data'];

					if ( $product->get_type() == 'gift-card' ){
						wc_add_notice( esc_html__( 'It is not possible to add a gift card code when the cart contains a gift card product', 'yith-woocommerce-gift-cards'), 'error' );

						return $return_val;
					}
				}
			}


			if ( ! $gift_card instanceof YWGC_Gift_Card_Premium ) {
				return $return_val;
			}

			$amount = $gift_card->get_balance();

			global $woocommerce_wpml;

			if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency ) {
				$amount = apply_filters( 'wcml_raw_price_amount', $amount );
			}


			if ( $gift_card->ID && $gift_card->get_balance() > 0 && $gift_card->is_enabled() ) {
				$temp_coupon_array = apply_filters( 'ywgc_temp_coupon_array' , array(
					'discount_type' => 'fixed_cart',
					'coupon_amount' => $amount,
					'amount'        => $amount,
					'id'            => true,
				), $gift_card );

				return $temp_coupon_array;
			}

			return $return_val;
		}

		/**
		 * Deduct an amount from the gift card balance
		 *
		 * @param int    $id                  the order id
		 * @param int    $item_id             the item id
		 * @param string $code                the gift card code
		 * @param float  $discount_amount     the amount to deduct
		 * @param float  $discount_amount_tax the tax amount to deduct
		 *
		 * @author Daniel Sanchez
		 * @since  2.0.4
		 */
		public function deduct_amount_from_gift_card( $id, $item_id, $code, $discount_amount, $discount_amount_tax ) {

			$gift = YITH_YWGC()->get_gift_card_by_code( $code );

			$total_discount_amount = $discount_amount + $discount_amount_tax;

			global $woocommerce_wpml;

			if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency ) {
				$total_discount_amount = YWGC_WPML::get_instance()->convert_to_base_currency($total_discount_amount);
			}

			if ( $gift instanceof YWGC_Gift_Card_Premium ) {

				$gift->update_balance( $gift->get_balance() - $total_discount_amount );
				$this->notify_customer_if_gift_cards_used( $gift );

			}

		}

		public function deduct_amount_from_gift_card_wc_3_plus( $item_id, $item, $order_id ) {

			if ( $item instanceof WC_Order_Item_Coupon ) {
				$this->deduct_amount_from_gift_card( $item->get_id(), $item_id, $item->get_code(), $item->get_discount(), $item->get_discount_tax() );
			}

		}


		/**
		 * Deduct an amount from the gift card balance
		 *
		 * @param WC_Cart $cart

		 * @author Fran Mendoza
		 * @since  3.0.0
		 */
		public function ywgc_allow_shipping_in_coupons( $cart ){

			$total_coupons_amount = 0;
			foreach( $cart->get_coupons() as $coupon ){

				$coupon_code = $coupon->get_code();
				$gift = YITH_YWGC()->get_gift_card_by_code( $coupon_code );

				if ($gift->exists())
					$total_coupons_amount += $coupon->get_amount();
			}

			if ( $total_coupons_amount > 0 ){

				$cart_totals = $cart->get_totals();

				$discount_total = $cart_totals['discount_total'] + $cart_totals['discount_tax'];

				$total_to_cover = $cart_totals['total'];

				$coupons_balance = $total_coupons_amount - $discount_total;

				if( $coupons_balance > 0 && $total_to_cover > 0 ){

					if ( $coupons_balance < $total_to_cover ){
						$remaining_amount = $coupons_balance;
					}
					else{
						$remaining_amount = $total_to_cover;
					}

					$cart->discount_cart += $remaining_amount;

					$new_cart_totals = $cart->get_totals();

					$this->ywgc_charge_other_amounts_on_coupons( $remaining_amount );

					$new_total = $new_cart_totals['total'] - $remaining_amount;
					$cart->total = $new_total;

					/////////// tax calculation

					$new_tax_value = 0;
					foreach ( $cart->get_tax_totals() as $tax_object ){

						$rate = WC_Tax::get_rate_percent($tax_object->tax_rate_id);

						$rate_formatted = '1.' . str_replace('%', '', $rate);

						$total_without_tax = (float)$new_total / (float)$rate_formatted;

						$new_tax_value += $new_total - $total_without_tax ;
					}

					///////// Apply all the taxes to the total, and delete it from the shipping and the fees

					foreach ( $cart->get_cart_contents() as $cart_item_key => $values ) {

						$line_tax_data = $values["line_tax_data"];

						$line_tax_data_total = $line_tax_data["total"];

						foreach ( $line_tax_data_total as $line_tax_data_key => $line_tax_data_values ) {

							$cart->set_cart_contents_tax($new_tax_value);
							$cart->set_cart_contents_taxes(array($line_tax_data_key => $new_tax_value));

							$cart->set_total_tax($new_tax_value);
//							$cart->set_subtotal_tax($new_tax_value);

							//Necessary to display a zero tax in the order page
							$cart->cart_contents[ $cart_item_key ]["line_tax_data"]["total"][ $line_tax_data_key ] = $new_tax_value;
							$cart->cart_contents[ $cart_item_key ]["line_tax_data"]["subtotal"][ $line_tax_data_key ] = $new_tax_value;

						}

						foreach ( $cart->get_shipping_taxes() as $cart_shipping_taxes_key => $cart_shipping_taxes_value) {

							$shipping_taxes_key[] = $cart_shipping_taxes_key;

							$shipping_total = $cart->get_shipping_total() + $cart_shipping_taxes_value;
							$cart->set_shipping_total( $shipping_total );

							$cart->set_shipping_tax(0);
							$cart->set_shipping_taxes(array($cart_shipping_taxes_key => 0));

						}

						foreach ( $cart->get_fees() as $key_fee => $value_fee ){
							$cart->set_fee_taxes( array($key_fee => 0));
							$cart->set_fee_tax(0);
						}

					}

				}

			}



		}

		/**
		 * Add the remaining cart amount to the coupon
		 *
		 * @author Fran Mendoza
		 * @since  3.0.0
		 */
		function ywgc_charge_other_amounts_on_coupons( $remaining_amount ) {

			$cart = WC()->cart;

			foreach( $cart->get_coupons() as $coupon ) {

				$coupon_code = $coupon->get_code();

				$coupon_discount_amount = $cart->get_coupon_discount_amount( $coupon_code , true );

				$cart_discount_added_by_this_coupon = isset( $coupon_discount_amount ) ? $coupon_discount_amount : 0;

				if( $cart_discount_added_by_this_coupon < $coupon->get_amount() ) {

					$unused_coupon_amount = $coupon->get_amount() - $cart_discount_added_by_this_coupon;

					if( $remaining_amount <= $unused_coupon_amount ) {

						$cart->coupon_discount_amounts[ $coupon_code ] = (isset( $coupon_discount_amount ) ? $coupon_discount_amount : 0 ) + $remaining_amount;
						$remaining_amount = 0;

					}
					elseif( $remaining_amount > $unused_coupon_amount ) {

						$remaining_amount = $remaining_amount - $unused_coupon_amount;

						$cart->coupon_discount_amounts[ $coupon_code ] += $unused_coupon_amount;
					}
				}

				if( $remaining_amount == 0 ) {
					return;
				}
			}
		}


		/**
		 *
		 * Show the image chosen for a gift card
		 *
		 * @param           $product_image    The product title HTML
		 * @param           $cart_item        The cart item array
		 * @param bool      $cart_item_key    The cart item key
		 *
		 * @since    2.0.1
		 * @author  Daniel Sanchez <daniel.sanchez@yithemes.com>
		 * @return  string  The product title HTML
		 * @use     woocommerce_cart_item_thumbnail hook
		 */
		public function ywgc_custom_cart_product_image( $product_image, $cart_item, $cart_item_key = false ) {

			if ( ! isset( $cart_item[ 'ywgc_amount' ] ) )
				return $product_image;

			$deliminiter1 = apply_filters( 'ywgc_delimiter1_for_cart_image', 'src=' );
			$deliminiter2 = apply_filters( 'ywgc_delimiter2_for_cart_image', '"' );

			if ( ! empty( $cart_item[ 'ywgc_has_custom_design' ] ) ) {

				$design_type = $cart_item[ 'ywgc_design_type' ];

				if ( 'custom' == $design_type ) {

					$image = YITH_YWGC_SAVE_URL . "/" . $cart_item[ 'ywgc_design' ];

					$product_image = '<img width="300" height="300" src="' . $image .'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
            alt="" srcset="' . $image .' 300w, ' . $image .' 600w, ' . $image .' 100w, ' . $image .' 150w, ' . $image .' 768w, ' . $image .' 1024w"
            sizes="(max-width: 300px) 100vw, 300px" />';

				}
				else if ( 'template' == $design_type ) {
					$product_image = wp_get_attachment_image( $cart_item[ 'ywgc_design' ] );

				}
				else if ( 'custom-modal' == $design_type ) {

					$image_url = $cart_item[ 'ywgc_design' ];

					$product_image = '<img width="300" height="300" src="' . $image_url .'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
            alt="" srcset="' . $image_url .' 300w, ' . $image_url .' 600w, ' . $image_url .' 100w, ' . $image_url .' 150w, ' . $image_url .' 768w, ' . $image_url .' 1024w"
            sizes="(max-width: 300px) 100vw, 300px" />';

				}

			}
			else{

				if ( isset( $cart_item[ 'ywgc_product_as_present' ] ) && $cart_item[ 'ywgc_product_as_present' ] ){

					$image = YITH_YWGC()->get_default_header_image();

					$array_product_image = explode( $deliminiter1, $product_image );
					$array_product_image = explode( $deliminiter2, $array_product_image[1] );

					$product_image = str_replace( $array_product_image[1], $image, $product_image );

				}
				else{

					$_product = wc_get_product( $cart_item[ 'product_id' ] );

					if ( get_class( $_product ) == 'WC_Product_Gift_Card' ){

						$image_id = get_post_thumbnail_id( $_product->get_id() );
						$header_image_url = wp_get_attachment_url( $image_id );

						$array_product_image = explode( $deliminiter1, $product_image );
						$array_product_image = explode( $deliminiter2, $array_product_image[ 1 ] );

						$product_image = str_replace( $array_product_image[1], $header_image_url, $product_image );

					}

				}

			}

			return $product_image;
		}

		/**
		 * @param           $product_title    The product title HTML
		 * @param           $cart_item        The cart item array
		 * @param bool|\The $cart_item_key    The cart item key
		 *
		 * @since    2.0.1
		 * @author  Daniel Sanchez <daniel.sanchez@yithemes.com>
		 * @return  string  The product title HTML
		 * @use     woocommerce_cart_item_name hook
		 */
		public function show_this_product_as_a_gift_card( $product_title, $cart_item, $cart_item_key = false ) {

			if ( ! empty( $cart_item[ 'ywgc_product_as_present' ] ) ) {

				$product_id = ( $cart_item[ 'ywgc_present_variation_id' ] ? $cart_item[ 'ywgc_present_variation_id' ] : $cart_item[ 'ywgc_present_product_id' ] );

				$product_title = "<a href='" . get_permalink( $product_id ) . "' >" . wc_get_product( $product_id )->get_name() . "</a> " . apply_filters( 'yith_wc_gift_card_as_a_gift_card', esc_html__( 'as a Gift Card', 'yith-woocommerce-gift-cards' ) );

			}

			return apply_filters( "yith_ywgc_cart_product_title", $product_title, $cart_item, $cart_item_key );
		}

		/**
		 * Allow gift card as a present always, no matter if the gift card is set as purchasable or not
		 *
		 * @param bool       $purchasable
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function gift_card_is_purchasable( $purchasable, $product ) {

			if ( ( $product instanceof WC_Product_Gift_Card ) && ( yit_get_prop( $product, 'id' ) == YITH_YWGC()->default_gift_card_id ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Show gift cards usage on order item totals
		 *
		 * @param array    $total_rows
		 * @param WC_Order $order
		 *
		 * @return array
		 */
		public function show_gift_cards_total_applied_to_order( $total_rows, $order ) {

			$gift_cards = yit_get_prop( $order, self::ORDER_GIFT_CARDS, true );
			if ( $gift_cards ) {
				$row_totals = $total_rows['order_total'];
				unset( $total_rows['order_total'] );

				$gift_cards_message = '';
				foreach ( $gift_cards as $code => $amount ) {
					$amount = apply_filters( 'yith_ywgc_gift_card_coupon_amount', $amount, YITH_YWGC()->get_gift_card_by_code( $code ) );
					$gift_cards_message .= apply_filters('yith_ywgc_gift_card_coupon_message',"-" . wc_price( $amount ) . ' (' . $code . ')', $amount, $code) ;
				}

				$total_rows['gift_cards'] = array(
					'label' => esc_html__( 'Gift cards:', 'yith-woocommerce-gift-cards' ),
					'value' => $gift_cards_message
				);

				$total_rows = apply_filters( 'ywgc_gift_card_thankyou_table_total_rows', $total_rows, $code );

				$total_rows['order_total'] = $row_totals;
			}

			return $total_rows;
		}

		/**
		 * Show gift card amount usage on cart totals
		 */
		public function show_gift_card_amount_on_cart_totals() {

			if ( isset( WC()->cart->applied_gift_cards ) ) {

				foreach ( WC()->cart->applied_gift_cards as $code ) :

					$label = apply_filters( 'yith_ywgc_cart_totals_gift_card_label', esc_html( esc_html__( 'Gift card:', 'yith-woocommerce-gift-cards' ) . ' ' . $code ), $code );
					$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? - WC()->cart->applied_gift_cards_amounts[ $code ] : 0;
					$value = wc_price( $amount ) . ' <a href="' . esc_url( add_query_arg( 'remove_gift_card_code', urlencode( $code ),
							defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url() ) ) .
					         '" class="ywgc-remove-gift-card " data-gift-card-code="' . esc_attr( $code ) . '">' . apply_filters('ywgc_remove_gift_card_text',esc_html__( '[Remove]', 'yith-woocommerce-gift-cards' ) ) . '</a>';
					?>
					<tr class="ywgc-gift-card-applied">
						<th><?php echo $label; ?></th>
						<td><?php echo $value; ?></td>
					</tr>

					<?php do_action('ywgc_gift_card_checkout_cart_table', $code, $amount ); ?>

				<?php endforeach;
			}
		}

		// Comparison function
		function cmp( $a, $b ) {
			if ( $a == $b ) {
				return 0;
			}

			return ( $a < $b ) ? - 1 : 1;
		}

		/**
		 * Apply a gift card discount to current cart
		 *
		 * @param string $code
		 */
		protected function add_gift_card_code_to_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();

			$code = strtoupper($code);

			if ( ! in_array( $code, $applied_gift_cards ) ) {
				$applied_gift_cards[] = $code;
				WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
			}
		}

		/**
		 * Remove a gift card discount from current cart
		 *
		 * @param string $code
		 */
		protected function remove_gift_card_code_from_session( $code ) {
			$applied_gift_cards = $this->get_gift_cards_from_session();

			if ( ( $key = array_search( $code, $applied_gift_cards ) ) !== false ) {
				unset( $applied_gift_cards[ $key ] );
			}

			WC()->session->set( 'applied_gift_cards', $applied_gift_cards );
		}

		private function get_gift_cards_from_session() {
			$value = array();

			if ( isset( WC()->session ) ) {
				$value = WC()->session->get( 'applied_gift_cards', array() );
			}

			return $value;
		}

		private function empty_gift_cards_session() {
			if ( isset( WC()->session ) ) {
				WC()->session->__unset( 'applied_gift_cards' );
			}
		}

		/**
		 * Apply the gift cards discount to the cart
		 *
		 * @param WC_Cart $cart
		 *
		 */
		public function apply_gift_cards_discount( $cart ) {

			$cart->applied_gift_cards         = array();
			$cart->applied_gift_cards_amounts = array();

			$gift_card_codes = $this->get_gift_cards_from_session();
			if ( $gift_card_codes ) {

				$cart_total = $cart->get_total('edit');

				$gift_card_amounts = array();
				foreach ( $gift_card_codes as $code ) {
					/** @var YWGC_Gift_Card_Premium|YITH_YWGC_Gift_Card $gift_card */
					$gift_card = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( YITH_YWGC()->check_gift_card( $gift_card, true ) ) {
						$gift_card_amounts[ $code ] = apply_filters( 'yith_ywgc_gift_card_coupon_amount',
							$gift_card->get_balance(),
							$gift_card );
					} else {
						$this->remove_gift_card_code_from_session( $code );
						wc_print_notices();
					}
				}

				uasort( $gift_card_amounts, array( $this, 'cmp' ) );

				foreach ( $gift_card_amounts as $code => $amount ) {

					$cart->applied_gift_cards[] = $code;

					if ( ( $cart_total + $cart->shipping_total > 0 ) && ( $amount > 0 ) ) {

						$discount = min( $amount, $cart_total );

						$residue = $cart_total - $discount;


						if( $residue > 0 ){
							if( ( $cart->shipping_total - $residue) >= 0 ){
								if( apply_filters('yith_ywgc_detract_residue_to_shipping_total',true) ){

									$cart->set_shipping_total($residue);
								}
							}else{
								$residue = $residue - $cart->shipping_total;
							}

						}

						$cart->applied_gift_cards_amounts[ $code ] = $discount;
						$cart_total -= $discount;
					}
				}

				$discount= isset( $discount ) ? $discount : '';

				$cart->ywgc_original_cart_total = $cart->total;

				do_action( 'yith_ywgc_apply_gift_card_discount_before_cart_total', $cart, $discount );

				$cart->total = abs($cart_total);

				do_action( 'yith_ywgc_apply_gift_card_discount_after_cart_total', $cart, $discount );


				if ( apply_filters( 'yith_ywgc_recalculate_taxes_after_cart_total', false ) ){

					if (  $cart->total  == '0'){
						$cart->set_total_tax(0);
						$cart->set_subtotal_tax(0);
						$cart->set_cart_contents_tax(0);
					}
					else{

						$cart_totals = $cart->get_totals();

						$cart_contents_total = $cart_totals['cart_contents_total'];
						$cart_contents_total_tax = $cart_totals['cart_contents_tax'];

						$new_cart_total =  $cart->total;

						$shiping_total = $cart_totals['shipping_total'];
						$shiping_total_tax = $cart_totals['shipping_tax'];

						$cart_total_aux = $cart_contents_total + $shiping_total;
						$cart_total_tax_aux = $cart_contents_total_tax + $shiping_total_tax;

						$tax_percentage = round(($cart_total_tax_aux * 100 ) / $cart_total_aux );

						$rate_formatted = '1.' . $tax_percentage;

						$amount_to_substract =  ($new_cart_total / $rate_formatted );

						$new_tax = $new_cart_total - $amount_to_substract;

						foreach ( $cart->get_cart_contents() as $cart_item_key => $values ) {

							$line_tax_data = $values["line_tax_data"];
							$line_tax_data_total = $line_tax_data["total"];
							foreach ( $line_tax_data_total as $line_tax_data_key => $line_tax_data_values ) {

								$cart->set_cart_contents_taxes(array($line_tax_data_key => $new_tax));
							}

							foreach ( $cart->get_shipping_taxes() as $cart_shipping_taxes_key => $cart_shipping_taxes_value) {
								$cart->set_shipping_taxes(array($cart_shipping_taxes_key => 0));
							}
						}

						$cart->set_cart_contents_total( $new_cart_total );
						$cart->set_cart_contents_tax($new_tax);
						$cart->set_total_tax($new_tax);

					}
				}



			}

		}


		public function apply_gift_cards_discount_for_subscriptions( $order ){

			$applied_gc = yit_get_prop( $order,'_ywgc_applied_gift_cards',true );

			if ( isset ( $applied_gc ) && !empty( $applied_gc ) ){

				$gift_totals = yit_get_prop( $order, '_ywgc_applied_gift_cards_totals', true );

				$order->set_total( $order->get_total()-$gift_totals );
				$order->save();
			}
		}

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function apply_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );
			$code = sanitize_text_field( $_POST['code'] );

			if ( ! empty( $code ) ) {
				$gift = YITH_YWGC()->get_gift_card_by_code( $code );
				if ( YITH_YWGC()->check_gift_card( $gift )  ) {


					$this->add_gift_card_code_to_session( $code );


					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Check if the gift card code provided is valid and store the amount for
		 * applying the discount to the cart
		 */
		public function remove_gift_card_code_callback() {

			check_ajax_referer( 'apply-gift-card', 'security' );

			$code = sanitize_text_field( $_POST['code'] );

			if ( ! empty( $code ) ) {

				$gift = YITH_YWGC()->get_gift_card_by_code( $code );
				if ( YITH_YWGC()->check_gift_card( $gift, true ) ) {
					$this->remove_gift_card_code_from_session( $code );

					wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_REMOVED ) );
				}
				wc_print_notices();
			}

			die();
		}

		/**
		 * Update the balance for all gift cards applied to an order
		 *
		 * @throws Exception
		 *
		 * @param int $order_id
		 */
		public function register_gift_cards_usage( $order_id ) {

			/**
			 * Adding two race condition fields to the order
			 */
			update_post_meta( $order_id, YWGC_RACE_CONDITION_BLOCKED, 'no' );
			update_post_meta( $order_id, YWGC_RACE_CONDITION_UNIQUID, 'none' );

			$applied_gift_cards = array();
			$applied_discount   = 0.00;

			if ( isset( WC()->cart->applied_gift_cards_amounts ) ) {
				foreach ( WC()->cart->applied_gift_cards_amounts as $code => $amount ) {
					$gift = YITH_YWGC()->get_gift_card_by_code( $code );

					if ( $gift->exists() ) {
						$amount                      = apply_filters( 'yith_ywgc_gift_card_amount_before_deduct', $amount );
						$applied_gift_cards[ $code ] = $amount;
						$applied_discount += $amount;

						$new_balance = apply_filters( 'yith_ywgc_new_balance_before_update_balance', max( 0.00, $gift->get_balance() - $amount ) );

						$gift->update_balance( $new_balance );
						$gift->register_order( $order_id );
					}
				}
			}

			if ( $applied_gift_cards ) {
				$order = wc_get_order( $order_id );
				yit_save_prop( $order, self::ORDER_GIFT_CARDS, $applied_gift_cards );
				yit_save_prop( $order, self::ORDER_GIFT_CARDS_TOTAL, $applied_discount );
				$order->add_order_note( sprintf( esc_html__( 'Order paid with gift cards for a total amount of %s.', 'yith-woocommerce-gift-cards' ), wc_price( $applied_discount ) ) );
			}

			$this->empty_gift_cards_session();
		}

		/**
		 * Notify the customer if a gift cards he bought is used
		 *
		 * @param YITH_YWGC_Gift_Card $gift_card
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function notify_customer_if_gift_cards_used( $gift_card ) {

			if ( "yes" == get_option ( 'ywgc_notify_customer' , 'no' ) ) {

				if ( $gift_card->exists() ) {
					WC()->mailer();
					do_action( 'ywgc-email-notify-customer_notification', $gift_card );
				}
			}
		}

		/**
		 * Add a negative fee when a gift card is added as coupon a remove the coupon
		 */
		public function add_item_fee($order_id) {

			if ( apply_filters( 'ywgc_add_gift_card_coupons_as_negative_fees', true ) ){
				return;
			}


				$order = wc_get_order($order_id);

			$total_coupons_amount = 0;
			$total_coupons_amount_tax = 0;

			foreach( $order->get_coupons() as $coupon ){

				$coupon_code = $coupon->get_code();
				$gift = YITH_YWGC()->get_gift_card_by_code( $coupon_code );

				if ($gift->exists())
					$total_coupons_amount += $coupon->get_discount();
				$total_coupons_amount_tax += $coupon->get_discount_tax();

			}

			if ( $total_coupons_amount > 0 ){
				$total_coupon_value = $total_coupons_amount + $total_coupons_amount_tax;

				foreach( $order->get_items( 'tax' ) as $item_id => $item_tax ){
					$tax_data = $item_tax->get_data();
					$tax_rate = $tax_data['rate_percent'];
				}

				$tax_rate = isset($tax_rate) ? $tax_rate : '0';

				$rate_aux = '0.' . $tax_rate;

				$item = new WC_Order_Item_Fee();

				$amount = -1 * ($total_coupon_value/(1 + $rate_aux) );

				$item->set_props( array(
					'id'      => '_ywgc_fee',
					'name'      => 'Gift Card',
					'total'     => floatval(  $amount ),
					'order_id'  => $order_id,
				) );

				$order->add_item( $item );

				$cart_tax_totals = $order->get_tax_totals();

				$aux_cart_tax = isset($tax_data['rate_code']) ? $cart_tax_totals[$tax_data['rate_code']]->amount : $order->get_cart_tax();

				foreach( $order->get_coupons() as $coupon ){

					$coupon_code = $coupon->get_code();
					$gift = YITH_YWGC()->get_gift_card_by_code( $coupon_code );


					if ($gift->exists()){
						$gift->register_order( $order->get_id() );
						$order->remove_coupon($coupon_code);
					}

				}

				$shipping_aux_total = $order->get_shipping_total() + $order->get_shipping_tax();

				foreach( $order->get_items( 'tax' ) as $item_id => $item_tax ){
					wc_update_order_item_meta( $item_id, 'tax_amount', $aux_cart_tax );
					wc_update_order_item_meta( $item_id, 'shipping_tax_amount', '0' );
				}


				update_post_meta($order->get_id(), '_ywgc_aux_cart_total_tax', $aux_cart_tax );
				update_post_meta($order->get_id(), '_order_tax', $aux_cart_tax );
				update_post_meta($order->get_id(), '_order_shipping_tax', '0' );
				update_post_meta($order->get_id(), '_order_shipping', $shipping_aux_total );


			}


		}

		/**
		 * @param int    $order_id
		 * @param string $code
		 * @param float  $discount
		 *
		 *
		 * @return bool|mixed
		 */
		public function add_order_gift_card( $order_id, $code, $discount ) {

			// Store gift card
			$item_id = wc_add_order_item( $order_id,
				array(
					'order_item_name' => $code,
					'order_item_type' => 'yith-gift-card'
				) );

			if ( ! $item_id ) {
				return false;
			}

			wc_add_order_item_meta( $item_id, 'discount_amount', $discount );

			do_action( 'yith_ywgc_order_add_gift_card', $order_id, $item_id, $code, $discount );

			return $item_id;
		}


		/**
		 * Show gift card details in cart page
		 *
		 * @param array $item_data
		 * @param array $cart_item
		 *
		 * @return array
		 */
		public function show_gift_card_details_in_cart( $item_data, $cart_item ) {

			if ( is_cart() || is_checkout() && isset(  $cart_item['ywgc_product_id'] ) ){

				if ( isset(  $cart_item['ywgc_recipient_name'] ) && ! empty( $cart_item['ywgc_recipient_name'] ) ) {
					echo '<div style="margin-top: 5px">'. esc_html__( 'Recipient\'s name: ', 'yith-woocommerce-gift-cards' ) . $cart_item['ywgc_recipient_name'] .'</div>';
				}
				if ( isset(  $cart_item['ywgc_recipients'] ) && ! empty( $cart_item['ywgc_recipients'] ) ) {

					$value = is_array( $cart_item['ywgc_recipients'] ) ? implode( ', ', $cart_item['ywgc_recipients'] ) : $cart_item['ywgc_recipients'];
					if ( ! $value ) {
						$value = esc_html__( 'Your billing email address', 'yith-woocommerce-gift-cards' );
					}

					echo '<div>'. esc_html__( 'Recipient\'s email: ', 'yith-woocommerce-gift-cards' ) . $value .'</div>';
				}

				if ( isset(  $cart_item['ywgc_sender_name'] ) && ! empty( $cart_item['ywgc_sender_name'] ) ) {
					echo '<div>'. esc_html__( 'Your name: ', 'yith-woocommerce-gift-cards' ) . $cart_item['ywgc_sender_name'] .'</div>';
				}

				if ( isset(  $cart_item['ywgc_message'] ) && ! empty( $cart_item['ywgc_message'] ) ) {
					echo '<div>'. esc_html__( 'Message: ', 'yith-woocommerce-gift-cards' ) . nl2br(stripslashes($cart_item['ywgc_message']))  .'</div>';
				}

				if ( isset( $cart_item['ywgc_delivery_date'] ) && ! empty( $cart_item['ywgc_delivery_date'] ) ) {

					$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

					$date = date_i18n( $date_format,  $cart_item['ywgc_delivery_date']);

					echo '<div>'. esc_html__( 'Delivery date: ', 'yith-woocommerce-gift-cards' ) . $date .'</div>';
				}

			}
			else{
				return array();
			}

			return $item_data;


		}

		/**
		 * Build cart item meta to pass to add_to_cart when adding a gift card to the cart
		 * @since 1.5.0
		 */
		public function build_cart_item_data() {

			$cart_item_data = array();

			$product_as_present = isset( $_POST["ywgc-as-present"] ) && ( 1 == $_POST["ywgc-as-present"] );

			/**
			 * Check if the current gift card has a manually entered amount set
			 */
			$ywgc_is_manual_amount = isset( $_REQUEST['ywgc-manual-amount'] ) && ( floatval( $_REQUEST['ywgc-manual-amount'] ) > 0 ) && ( ! isset( $_REQUEST['gift_amounts'] ) || ( "-1" == $_REQUEST['gift_amounts'] ) );
			$ywgc_is_manual_amount = wc_format_decimal ( $ywgc_is_manual_amount );

			/**
			 * Check if the current gift card has a prefixed amount set
			 */

			$ywgc_is_preset_amount = ! $ywgc_is_manual_amount && isset( $_REQUEST['gift_amounts'] ) && ( floatval( $_REQUEST['gift_amounts'] ) > 0 );
			$ywgc_is_preset_amount = wc_format_decimal ( $ywgc_is_preset_amount );


			/**
			 * Neither manual or fixed? Something wrong happened!
			 */
			if ( ! $product_as_present && ! $ywgc_is_manual_amount && ! $ywgc_is_preset_amount && apply_filters( 'yith_ywgc_allow_zero_gift_cards', true ) ) {
				wp_die( __( 'The gift card has invalid amount', 'yith-woocommerce-gift-cards' ) );
			}

			/**
			 * Check if it is a physical gift card
			 */
			$ywgc_is_physical = isset( $_REQUEST['ywgc-is-physical'] ) && $_REQUEST['ywgc-is-physical'];
			if ( $ywgc_is_physical ) {

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? $_REQUEST['ywgc-sender-name'] : '';

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? $_REQUEST['ywgc-recipient-name'] : '';

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? $_REQUEST['ywgc-edit-message'] : '';

			}

			/**
			 * Check if it is a digital gift card
			 */
			$ywgc_is_digital = isset( $_REQUEST['ywgc-is-digital'] ) && $_REQUEST['ywgc-is-digital'];
			if ( $ywgc_is_digital ) {

				/**
				 * Retrieve gift card recipient
				 */
				$recipients = apply_filters( 'ywgc-recipient-email', isset( $_REQUEST['ywgc-recipient-email'] ) ? $_REQUEST['ywgc-recipient-email'] : '');

				/**
				 * Retrieve sender name
				 */
				$sender_name = isset( $_REQUEST['ywgc-sender-name'] ) ? $_REQUEST['ywgc-sender-name'] : '';

				/**
				 * Recipient name
				 */
				$recipient_name = isset( $_REQUEST['ywgc-recipient-name'] ) ? $_REQUEST['ywgc-recipient-name'] : '';

				/**
				 * Retrieve the sender message
				 */
				$sender_message = isset( $_REQUEST['ywgc-edit-message'] ) ? $_REQUEST['ywgc-edit-message'] : '';

				/**
				 * Gift card should be delivered on a specific date?
				 */

				$delivery_date = isset( $_REQUEST['ywgc-delivery-date'] ) ? $_REQUEST['ywgc-delivery-date'] : '';

				if ( $delivery_date != '' && is_string($delivery_date) && !is_bool( $delivery_date ) ) {

					$saved_format           = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

					if ( $saved_format == 'MM d, yy'){
						$delivery_date = strtotime($delivery_date);
					}
					else{
						$search  = array( '.', ', ', '/', ' ', ',', 'MM', 'yy', 'mm', 'dd' );
						$replace = array( '-', '-', '-', '-', '-', 'M', 'y', 'm', 'd' );

						$date_formatted = str_replace( $search, $replace, $delivery_date );

						$saved_format_formatted = str_replace( $search, $replace, $saved_format );

						$delivery_date = 'mm/dd/yy' !== $saved_format ? date( $saved_format_formatted, strtotime( $date_formatted ) ) : date( $saved_format_formatted, strtotime( $delivery_date ) );

						if ( $delivery_date = DateTime::createFromFormat( $saved_format_formatted, $delivery_date ) ) {
							$delivery_date = $delivery_date->getTimestamp();
						}
					}

				}

				$postdated = $delivery_date != '' ? true : false;

				$gift_card_design = - 1;
				$design_type      = isset( $_POST['ywgc-design-type'] ) ? $_POST['ywgc-design-type'] : 'default';

				if ( 'custom' == $design_type ) {
					/**
					 * The user has uploaded a file
					 */

					if ( isset( $_FILES["ywgc-upload-picture"] ) ) {
						$custom_image = $_FILES["ywgc-upload-picture"];
						if ( isset( $custom_image["tmp_name"] ) && ( 0 == $custom_image["error"] ) ) {
							$gift_card_design = $this->save_uploaded_file( $custom_image );
						}
					}
				}
				else if ( 'template' == $design_type ) {
					if ( isset( $_POST['ywgc-template-design'] ) ) {
						$gift_card_design = $_POST['ywgc-template-design'];
					}
				}
				else if ( 'custom-modal' == $design_type ) {

					if ( isset( $_POST['ywgc-custom-modal-design'] ) ) {
						$gift_card_design = $_POST['ywgc-custom-modal-design'];
					}
				}


			}

			if ( $product_as_present ) {

				$cart_item_data['ywgc_product_id'] = YITH_YWGC()->default_gift_card_id;

				$present_product_id   = $_POST["add-to-cart"];
				$present_variation_id = 0;

				if ( isset( $_POST["variation_id"] ) ) {
					$present_variation_id = $_POST["variation_id"];
				}

				$product = $present_variation_id ? wc_get_product( $present_variation_id ) : wc_get_product( $present_product_id );

				$ywgc_amount = $product->get_price();
				$ywgc_amount = apply_filters( 'yith_ywgc_submitting_as_present_amount', $ywgc_amount );

				$cart_item_data['ywgc_product_as_present']   = $product_as_present;
				$cart_item_data['ywgc_present_product_id']   = $present_product_id;
				$cart_item_data['ywgc_present_variation_id'] = $present_variation_id;
			}
			else {
				if ( isset( $_POST['add-to-cart'] ) ) {
					$cart_item_data['ywgc_product_id'] = absint($_POST['add-to-cart']);
				}
				else if ( isset( $_REQUEST["ywgc_product_id"] ) ) {
					$cart_item_data['ywgc_product_id'] = $_REQUEST["ywgc_product_id"];
				}

				/**
				 * Set the gift card amount
				 */
				$on_sale = get_post_meta( $cart_item_data['ywgc_product_id'], '_ywgc_sale_discount_value', true );

				if ( $ywgc_is_manual_amount ) {

					$ywgc_amount = $_REQUEST['ywgc-manual-amount'];

					if ( apply_filters( 'yith_ywgc_submitting_manual_amount_with_discount', true ) && $on_sale ) {

						//save the real amount of the gift card
						$cart_item_data['ywgc_amount_without_discount'] = $ywgc_amount;

						$discount = apply_filters( 'yith_ywgc_discount_value', ( $ywgc_amount * (int)$on_sale ) / 100, $ywgc_amount, $on_sale );
						$ywgc_amount = $ywgc_amount - $discount ;

					}else{
						$ywgc_amount = apply_filters( 'yith_ywgc_submitting_manual_amount', $ywgc_amount );
					}
				} else {
					$ywgc_amount = $_REQUEST['gift_amounts'];

					if ( $on_sale ) {

						//save the real amount of the gift card
						$cart_item_data['ywgc_amount_without_discount'] = $ywgc_amount;

						$discount = apply_filters( 'yith_ywgc_discount_value', ( $ywgc_amount * (int)$on_sale ) / 100, $ywgc_amount, $on_sale );
						$ywgc_amount = $ywgc_amount - $discount ;
					}
					else{
						$ywgc_amount = apply_filters( 'yith_ywgc_submitting_select_amount', $ywgc_amount );
					}

				}
			}

			$cart_item_data['ywgc_amount']           = $ywgc_amount;
			$cart_item_data['ywgc_is_manual_amount'] = $ywgc_is_manual_amount;
			$cart_item_data['ywgc_is_digital']       = $ywgc_is_digital;
			$cart_item_data['ywgc_is_physical']       = $ywgc_is_physical;

			/**
			 * Retrieve the gift card recipient, if digital
			 */
			if ( $ywgc_is_digital ) {
				$cart_item_data['ywgc_recipients']     = $recipients;
				$cart_item_data['ywgc_sender_name']    = $sender_name;
				$cart_item_data['ywgc_recipient_name'] = $recipient_name;
				$cart_item_data['ywgc_message']        = $sender_message;
				$cart_item_data['ywgc_postdated']      = $postdated;

				if ( $postdated ) {
					$cart_item_data['ywgc_delivery_date'] = $delivery_date;
				}


				$cart_item_data['ywgc_design_type']       = $design_type;
				$cart_item_data['ywgc_has_custom_design'] = $gift_card_design != - 1;
				if ( $gift_card_design ) {
					$cart_item_data['ywgc_design'] = $gift_card_design;
				}

			}

			if ( $ywgc_is_physical ) {
				$cart_item_data['ywgc_recipient_name'] = $recipient_name;
				$cart_item_data['ywgc_sender_name']    = $sender_name;
				$cart_item_data['ywgc_message']        = $sender_message;
			}

			global $woocommerce_wpml;

			if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency ) {

				$currency = $woocommerce_wpml->multi_currency->get_client_currency();

				$cart_item_data['ywgc_currency'] = $currency;

				$cart_item_data['ywgc_default_currency_amount'] = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $ywgc_amount );


			}

			return $cart_item_data;
		}

		/**
		 * Custom add_to_cart handler for gift card product type
		 */
		public function add_to_cart_handler() {

			$item_data  = $this->build_cart_item_data();
			$product_id = $item_data['ywgc_product_id'];
			$adding_to_cart     = wc_get_product( $product_id );

			if ( ! $product_id ) {
				wc_add_notice( esc_html__( 'An error occurred while adding the product to the cart.', 'yith-woocommerce-gift-cards' ), 'error' );

				return false;
			}

			$added_to_cart = false;

			if ( $item_data['ywgc_is_digital'] ) {

				$recipients = $item_data['ywgc_recipients'];
				/**
				 * Check if all mandatory fields are filled or throw an error
				 */
				if ( YITH_YWGC()->mandatory_recipient() && is_array($recipients) && ! count( $recipients ) ) {
					wc_add_notice( esc_html__( 'Add a valid email address for the recipient', 'yith-woocommerce-gift-cards' ), 'error' );

					return false;
				}

				/**
				 * Validate all email addresses submitted
				 */
				$email_error = '';
				if ( YITH_YWGC()->mandatory_recipient() && $recipients ) {
					foreach ( $recipients as $recipient ) {

						if ( YITH_YWGC()->mandatory_recipient() && empty( $recipient ) ) {
							wc_add_notice( esc_html__( 'The recipient(s) email address is mandatory', 'yith-woocommerce-gift-cards' ), 'error' );

							return false;
						}

						if ( $recipient && ! filter_var( $recipient, FILTER_VALIDATE_EMAIL ) ) {
							$email_error .= '<br>' . $recipient;
						}
					}

					if ( $email_error ) {
						wc_add_notice( esc_html__( 'Email address not valid, please check the following: ', 'yith-woocommerce-gift-cards' ) . $email_error, 'error' );

						return false;
					}
				}

				/** The user can purchase 1 gift card with multiple recipient emails or [quantity] gift card for the same user.
				 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
				 * */
				$recipient_count = is_array( $item_data['ywgc_recipients'] ) ? count( $item_data['ywgc_recipients'] ) : 0;
				$quantity        = ( $recipient_count > 1 ) ? $recipient_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );

				if ( $recipient_count > 1 ) {
					$item_data_to_card = $item_data;

					for ( $i = 0; $i < $recipient_count; $i++ ) {

						$item_data_to_card['ywgc_recipients'] = array( $item_data['ywgc_recipients'][$i] );
						$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][$i];

						$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
					}

				} else {
					$item_data['ywgc_recipient_name'] = is_array($item_data['ywgc_recipient_name']) ? $item_data['ywgc_recipient_name'][0] : $item_data['ywgc_recipient_name'];
					$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

				}

			}else if ( $item_data['ywgc_is_physical'] ) {
				/** The user can purchase 1 gift card with multiple recipient names or [quantity] gift card for the same user.
				 * It's not possible to mix both, purchasing multiple instance of gift card with multiple recipients
				 * */

				$recipient_name_count = is_array( $item_data['ywgc_recipient_name'] ) ? count( $item_data['ywgc_recipient_name'] ) : 0;
				$quantity        = ( $recipient_name_count > 1 ) ? $recipient_name_count : ( isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1 );

				if ( $recipient_name_count > 1 ) {
					$item_data_to_card = $item_data;

					for ( $i = 0; $i < $recipient_name_count; $i++ ) {

						$item_data_to_card['ywgc_recipient_name'] = $item_data['ywgc_recipient_name'][$i];

						$added_to_cart = WC()->cart->add_to_cart( $product_id, 1, 0, array(), $item_data_to_card );
					}

				} else {
					$item_data['ywgc_recipient_name'] = is_array($item_data['ywgc_recipient_name']) ? $item_data['ywgc_recipient_name'][0] : $item_data['ywgc_recipient_name'];
					$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );

				}

			}
			else {
				$quantity      = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;
				$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $item_data );
			}

			if ( $added_to_cart ) {
				if ($product_id == get_option(YWGC_PRODUCT_PLACEHOLDER) &&  isset( $item_data['ywgc_present_product_id'] ) ){
					$product_id = $item_data['ywgc_present_product_id'];;
				}
				if ( !isset( $item_data['ywgc_present_product_id'] ) && isset($item_data['ywgc_product_id']) ){
					$product_id = $item_data['ywgc_product_id'];
				}
				$this->show_cart_message_on_added_product( $product_id, $quantity );
			}

			// If we added the product to the cart we can now optionally do a redirect.
			if ( wc_notice_count( 'error' ) === 0 ) {

				$url = '';
				// If has custom URL redirect there
				if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', $url, $adding_to_cart ) ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
					if ( function_exists( 'wc_get_cart_url' ) ) {
						wp_safe_redirect( wc_get_cart_url() );
					} else {
						wp_safe_redirect( WC()->cart->get_cart_url() );
					}
					exit;
				}
			}

		}


		public function prevent_gift_card_and_physical_products_in_the_same_cart( $allow, $product_id, $quantity, $variation_id = '' ){

			if( get_option( 'ywgc_prevent_virtual_gift_card_and_physical_products_in_same_order',false ) == 'yes' ){

				$product_id = $variation_id != '' ? $variation_id : $product_id;

				$added_product = wc_get_product( $product_id );

				$contents = WC()->cart->cart_contents;

				if( !empty( $contents) ) {
					foreach ( $contents as $item_key => $cart_item ) {
						if( (isset($cart_item['ywgc_is_digital']) && $cart_item['ywgc_is_digital'] == true && ! $added_product->is_virtual() )){
							$allow = false;
							break;
						}
					}
				}
				if( $allow == false ){
					wc_add_notice( esc_html__( 'You can\'t purchase a physical product and a digital gift card with the same order', 'yith-woocommerce-gift-cards' ), 'error' );
				}

			}
			return $allow;
		}

		public function show_cart_message_on_added_product( $product_id, $quantity = 1 ) {
			$param  = array( $product_id => $quantity );
			wc_add_to_cart_message( $param, true );
		}

		/**
		 * Set the real amount for the gift card product
		 *
		 * @param array $cart_item
		 *
		 * @since 1.5.0
		 * @return mixed
		 */
		public function set_price_in_cart( $cart_item ) {
			if ( isset( $cart_item['data'] ) ) {
				if ( $cart_item['data'] instanceof WC_Product_Gift_Card && isset($cart_item['ywgc_amount']) ) {

					yit_set_prop( $cart_item['data'], 'price', $cart_item['ywgc_amount'] );
				}
			}

			return $cart_item;
		}

		/**
		 * Update cart item when retrieving cart from session
		 *
		 * @param $session_data mixed Session data to add to cart
		 * @param $values       mixed Values stored in session
		 *
		 * @return mixed Session data
		 * @since 1.5.0
		 */
		public function get_cart_item_from_session( $session_data, $values ) {

			if ( isset( $values['ywgc_product_id'] ) && $values['ywgc_product_id'] ) {

				$session_data['ywgc_product_id']       = isset( $values['ywgc_product_id'] ) ? $values['ywgc_product_id'] : '';
				$session_data['ywgc_amount']           = isset( $values['ywgc_amount'] ) ? $values['ywgc_amount'] : '';
				$session_data['ywgc_amount_without_discount']           = isset( $values['ywgc_amount_without_discount'] ) ? $values['ywgc_amount_without_discount'] : '';
				$session_data['ywgc_is_manual_amount'] = isset( $values['ywgc_is_manual_amount'] ) ? $values['ywgc_is_manual_amount'] : false;
				$session_data['ywgc_is_digital']       = isset( $values['ywgc_is_digital'] ) ? $values['ywgc_is_digital'] : false;
				$session_data['ywgc_currency']       = isset( $values['ywgc_currency'] ) ? $values['ywgc_currency'] : false;
				$session_data['ywgc_default_currency_amount']       = isset( $values['ywgc_default_currency_amount'] ) ? $values['ywgc_default_currency_amount'] : false;

				if ( $session_data['ywgc_is_digital'] ) {
					$session_data['ywgc_recipients']     = isset( $values['ywgc_recipients'] ) ? $values['ywgc_recipients'] : '';
					$session_data['ywgc_sender_name']    = isset( $values['ywgc_sender_name'] ) ? $values['ywgc_sender_name'] : '';
					$session_data['ywgc_recipient_name'] = isset( $values['ywgc_recipient_name'] ) ? $values['ywgc_recipient_name'] : '';
					$session_data['ywgc_message']        = isset( $values['ywgc_message'] ) ? $values['ywgc_message'] : '';

					$session_data['ywgc_has_custom_design'] = isset( $values['ywgc_has_custom_design'] ) ? $values['ywgc_has_custom_design'] : false;
					$session_data['ywgc_design_type']       = isset( $values['ywgc_design_type'] ) ? $values['ywgc_design_type'] : '';
					if ( $session_data['ywgc_has_custom_design'] ) {
						$session_data['ywgc_design'] = isset( $values['ywgc_design'] ) ? $values['ywgc_design'] : '';
					}

					$session_data['ywgc_postdated'] = isset( $values['ywgc_postdated'] ) ? $values['ywgc_postdated'] : false;
					if ( $session_data['ywgc_postdated'] ) {
						$session_data['ywgc_delivery_date'] = isset( $values['ywgc_delivery_date'] ) ? $values['ywgc_delivery_date'] : false;
					}
				}

				if ( isset( $values['ywgc_amount'] ) ) {
					$product_price = apply_filters( 'yith_ywgc_set_cart_item_price', $values['ywgc_amount'], $values );
					yit_set_prop( $session_data['data'], 'price', $product_price );
				}
			}

			return $session_data;
		}

		/**
		 * move an uploaded file into a persistent folder with a unique name
		 *
		 * @param string $image uploaded image
		 *
		 * @return string   real path of the uploaded image
		 */
		public
		function save_uploaded_file(
			$image
		) {
			// Create folders for storing documents
			$date     = getdate();
			$folder   = sprintf( "%s/%s", $date["year"], $date["mon"] );
			$filename = $image["name"];

			while ( true ) {

				$relative_path = sprintf( "%s/%s", $folder, $filename );
				$dir_path      = sprintf( "%s/%s", YITH_YWGC_SAVE_DIR, $folder );
				$full_path     = sprintf( "%s/%s", YITH_YWGC_SAVE_DIR, $relative_path );

				if ( ! file_exists( $full_path ) ) {
					if ( ! file_exists( $dir_path ) ) {
						wp_mkdir_p( $dir_path );
					}

					move_uploaded_file( $image["tmp_name"], $full_path );

					return $relative_path;
				} else {

					$unique_id = rand();

					$name_without_ext = pathinfo( $filename, PATHINFO_FILENAME );
					$ext              = pathinfo( $filename, PATHINFO_EXTENSION );

					$filename = $name_without_ext . $unique_id . '.' . $ext;
				}
			}
		}

		/**
		 * Let the user to edit che gift card content
		 */
		public function edit_gift_card_callback() {

			if ( ! ( "yes" == get_option ( 'ywgc_permit_modification' ) ) ) {
				return;
			}

			$order_item_id = intval( sanitize_text_field( $_POST['item_id'] ) );
			$gift_card_id  = intval( sanitize_text_field( $_POST['gift_card_id'] ) );
			$sender        = sanitize_text_field( $_POST['sender'] );
			$recipient     = sanitize_email( $_POST['recipient'] );
			$message       = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['message'] ) ) );

			/** Retrieve the gift card content.
			 *  If a valid gift card was generated, the content to be edited is a post meta of the gift card.
			 *  In the opposite case all the data are order item meta
			 */
			$item_gift_card_ids = ywgc_get_order_item_giftcards( $order_item_id );

			if ( in_array( $gift_card_id, $item_gift_card_ids ) ) {
				//  The gift card exists, edit it as custom post type
				$curr_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );
				if ( $curr_card->exists() ) {

					//  Update current gift card content without saving, this card will be dismissed leaving a new gift card build as a clone from it
					$clone_it               = $recipient != $curr_card->recipient;
					$curr_card->sender_name = $sender;
					$curr_card->recipient   = $recipient;
					$curr_card->message     = $message;

					//  check if the recipient changes, if so, set_dismissed_status the current gift card and
					//  create a new one
					if ( $clone_it ) {

						//  The gift cards being changed will be closed and a new one will be created
						$new_gift = YITH_YWGC()->clone_gift_card( $curr_card );
						$new_gift->save();

						$curr_card->set_dismissed_status();

						//  assign the new gift card to the order item
						$item_gift_card_ids[] = $new_gift->ID;
						ywgc_set_order_item_giftcards( $order_item_id, $item_gift_card_ids );

						wp_send_json( array(
							"code"   => 2,
							"values" => array(
								"new_id" => $new_gift->ID,
							),
						) );
					} else {

						//  update the current gift card
						$curr_card->save();

						wp_send_json( array(
							"code" => 1,
						) );
					}
				}
			} else {
				//  a gift card custom post type object doesn't exists, edit order item meta values
				$meta = wc_get_order_item_meta( $order_item_id, YWGC_ORDER_ITEM_DATA );

				//edit order item meta
				$meta["sender"]    = $sender;
				$meta["recipient"] = $recipient;
				$meta["message"]   = $message;

				wc_update_order_item_meta( $order_item_id, YWGC_ORDER_ITEM_DATA, $meta );

				wp_send_json( array(
					"code" => 1,
				) );
			}

			wp_send_json( array(
				"code" => - 1,
			) );
		}

		/**
		 * @param                       $item_id
		 * @param WC_Order_Item_Product $item
		 */

		public function append_gift_card_data_to_new_order_item( $item_id, $item ) {

			if ( 'line_item' == $item->get_type() ) {

				if ( isset( $item->legacy_values ) )
					$this->append_gift_card_data_to_order_item( $item_id, $item->legacy_values );
			}
		}

		/**
		 * Append data to order item
		 *
		 * @param int   $item_id
		 * @param array $values
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.5.0
		 */
		public function append_gift_card_data_to_order_item( $item_id, $values ) {

			if ( ! isset( $values['ywgc_product_id'] ) ) {
				return;
			}

			/**
			 * Store all fields related to Gift Cards
			 */

			foreach ( $values as $key => $value ) {
				if ( strpos( $key, 'ywgc_' ) === 0 ) {
					$meta_key = '_' . $key;
					wc_update_order_item_meta( $item_id, $meta_key, $value );
				}
			}

			/**
			 * Store subtotal and subtotal taxes applied to the gift card
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal', $values['line_subtotal'] );
			wc_update_order_item_meta( $item_id, '_ywgc_subtotal_tax', $values['line_subtotal_tax'] );

			/**
			 * Store the plugin version for future use
			 */
			wc_update_order_item_meta( $item_id, '_ywgc_version', YITH_YWGC_VERSION );

		}

		/**
		 * Manage the request from an email for a gift card code to be applied to the cart
		 *
		 */
		public function check_email_discount() {

			$actions = array();

			/**
			 *
			 * Old version support from version 1.8.6 on
			 * after some versions we could remove the following way of retrieving the
			 * 'add-discount' and 'verify-code'
			 *
			 */

			/*************** START OLD VERSION VAR RETRIEVES **************/

			if ( isset( $_GET[ 'add-discount' ] ) &&
			     isset( $_GET[ 'verify-code' ] ) )
				$actions = array(
					'add_discount' => $_GET[ 'add-discount' ],
					'verify_code' => $_GET[ 'verify-code' ],
					'product_id' => ( isset( $_GET[ YWGC_ACTION_PRODUCT_ID ] ) ? $_GET[ YWGC_ACTION_PRODUCT_ID ] : get_option( YWGC_PRODUCT_PLACEHOLDER ) ),
					'gift_this_product' => 'yes',
				);

			/*************** END OLD VERSION VAR RETRIEVES **************/

			if ( isset( $_GET[ YWGC_ACTION_ADD_DISCOUNT_TO_CART ] ) &&
			     isset( $_GET[ YWGC_ACTION_VERIFY_CODE ] )
			)
				$actions = array(
					'add_discount' => $_GET[ YWGC_ACTION_ADD_DISCOUNT_TO_CART ],
					'verify_code' => $_GET[ YWGC_ACTION_VERIFY_CODE ],
					'product_id' => ( isset( $_GET[ YWGC_ACTION_PRODUCT_ID ] ) ? $_GET[ YWGC_ACTION_PRODUCT_ID ] : get_option( YWGC_PRODUCT_PLACEHOLDER ) ),
					'gift_this_product' => ( isset( $_GET[ YWGC_ACTION_GIFT_THIS_PRODUCT ] ) ? $_GET[ YWGC_ACTION_GIFT_THIS_PRODUCT ] : 'no' ),
				);

			if ( is_array( $actions ) && ! empty( $actions ) ) {

				/**
				 *
				 * we add the product to the cart directly so in case the browser of the user never has used the site
				 * we create the cart session. If the admin does not want to add the product directly to the cart
				 * we remove it
				 *
				 */

				if ( get_option ( 'ywgc_gift_this_product_add_to_cart', 'yes' ) == 'yes' && $actions[ 'product_id' ] != get_option( YWGC_PRODUCT_PLACEHOLDER ) && $actions[ 'product_id' ] != '' )
					WC()->cart->add_to_cart( $actions[ 'product_id' ] );

				if ( get_option ( 'ywgc_gift_this_product_add_to_cart', 'yes' ) != 'yes' || $actions[ 'gift_this_product' ] == 'no' ){

					$items = WC()->cart->get_cart();
					foreach ( $items as $cart_item_key => $values ) {
						if ( $values[ 'product_id' ] == $actions[ 'product_id' ] )
							WC()->cart->remove_cart_item( $cart_item_key );

					}

				}

				if ( ( get_option ( 'ywgc_auto_discount' ) != 'no' && $actions[ 'gift_this_product' ] == 'no' ) || ( get_option ( 'ywgc_gift_this_product_apply_gift_card', 'yes' ) != 'no' && $actions[ 'gift_this_product' ] == 'yes' ) ){

					$gift = YITH_YWGC()->get_gift_card_by_code( $actions[ 'add_discount' ] );

					if ( $gift->can_be_used() ) {

						//  Check the hash value and compare with the one provided
						$hash_value = YITH_YWGC()->hash_gift_card( $gift );

						if ( $hash_value == $actions[ 'verify_code' ] ) {
							//  can add the discount to the cart
							if ( YITH_YWGC()->check_gift_card( $gift ) ) {
								$this->add_gift_card_code_to_session( $gift->get_code() );
								wc_add_notice( $gift->get_gift_card_message( YITH_YWGC_Gift_Card::GIFT_CARD_SUCCESS ) );
							}
						}
					}

				}

			}
		}
	}
}

YITH_YWGC_Cart_Checkout::get_instance();
