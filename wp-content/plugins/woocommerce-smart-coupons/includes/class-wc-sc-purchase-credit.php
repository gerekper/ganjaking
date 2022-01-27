<?php
/**
 * Purchase Credit Features
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.9.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Purchase_Credit' ) ) {

	/**
	 * Class for handling Purchase credit feature
	 */
	class WC_SC_Purchase_Credit {

		/**
		 * Variable to hold instance of WC_SC_Purchase_Credit
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'call_for_credit_form' ), 20 );
			add_filter( 'woocommerce_call_for_credit_form_template', array( $this, 'woocommerce_call_for_credit_form_template_path' ) );

			add_filter( 'woocommerce_is_purchasable', array( $this, 'make_product_purchasable' ), 10, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'price_html_for_purchasing_credit' ), 10, 2 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'override_price_before_calculate_totals' ) );

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-gateway-paypal-express/woocommerce-gateway-paypal-express.php' ) ) {
				add_action( 'woocommerce_ppe_checkout_order_review', array( $this, 'gift_certificate_receiver_detail_form' ) );
				add_action( 'woocommerce_ppe_do_payaction', array( $this, 'ppe_save_called_credit_details_in_order' ) );
			}

			add_filter( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price_html' ), 10, 3 );
			add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'gift_certificate_receiver_detail_form' ) );

			add_action( 'wp_loaded', array( $this, 'purchase_credit_hooks' ) );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_called_credit_details_in_order' ), 10, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'call_for_credit_cart_item_data' ), 10, 3 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'save_called_credit_in_session' ), 10, 6 );

			add_filter( 'woocommerce_product_get_price', array( $this, 'override_voucher_product_empty_price' ), 9, 2 );

			add_action( 'wp_footer', array( $this, 'enqueue_styles_scripts' ) );

		}

		/**
		 * Get single instance of WC_SC_Purchase_Credit
		 *
		 * @return WC_SC_Purchase_Credit Singleton object of WC_SC_Purchase_Credit
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Display form to enter value of the store credit to be purchased
		 */
		public function call_for_credit_form() {
			global $product, $store_credit_label;

			if ( $product instanceof WC_Product_Variation ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				if ( ! is_object( $product ) || ! is_callable( array( $product, 'get_id' ) ) ) {
					return;
				}
				$product_id = $product->get_id();
				if ( empty( $product_id ) ) {
					return;
				}
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$product_price = $product->get_price();

			// MADE CHANGES IN THE CONDITION TO SHOW INPUT FIELD FOR PRICE ONLY FOR COUPON AS A PRODUCT.
			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ( ! ( ! empty( $product_price ) || ( is_plugin_active( 'woocommerce-name-your-price/woocommerce-name-your-price.php' ) && ( get_post_meta( $product_id, '_nyp', true ) === 'yes' ) ) ) ) ) {

				$js = "
							const minCreditAmount      = parseFloat( jQuery('input#credit_called').attr('min') );
							const maxCreditAmount      = parseFloat( jQuery('input#credit_called').attr('max') );
							var   validateCreditCalled = function(){
								var enteredCreditAmount  = parseFloat( jQuery('input#credit_called').val() );
								if ( isNaN(enteredCreditAmount) || enteredCreditAmount < minCreditAmount || ( maxCreditAmount > 0 && enteredCreditAmount > maxCreditAmount ) ) {
									var creditErrorMsg = '" . __( 'Invalid amount.', 'woocommerce-smart-coupons' ) . "';
									if ( isNaN(enteredCreditAmount) ) {
										creditErrorMsg += ' " . __( 'Enter a numeric value.', 'woocommerce-smart-coupons' ) . "';
									}
									if ( enteredCreditAmount < minCreditAmount ) {
										creditErrorMsg += ' " . __( 'The value should not be less than', 'woocommerce-smart-coupons' ) . " ' + minCreditAmount;
									} else if ( enteredCreditAmount > maxCreditAmount ) {
										creditErrorMsg += ' " . __( 'The value should not be greater than', 'woocommerce-smart-coupons' ) . " ' + maxCreditAmount;
									}
									jQuery('#error_message').text(creditErrorMsg);
									jQuery('input#credit_called').css('border-color', 'red');
									return false;
								} else {
									jQuery('form.cart').unbind('submit');
									jQuery('#error_message').text('');
									jQuery('input#credit_called').css('border-color', '');
									return true;
								}
							};

							jQuery('input#credit_called').bind('change keyup', function(){
								validateCreditCalled();
								jQuery('input#hidden_credit').remove();
								if ( jQuery('input[name=quantity]').length ) {
									jQuery('input[name=quantity]').append('<input type=\"hidden\" id=\"hidden_credit\" name=\"credit_called[" . absint( $product_id ) . "]\" value=\"'+jQuery('input#credit_called').val()+'\" />');
								} else {
									jQuery('input[name=\"add-to-cart\"]').after('<input type=\"hidden\" id=\"hidden_credit\" name=\"credit_called[" . absint( $product_id ) . "]\" value=\"'+jQuery('input#credit_called').val()+'\" />');
								}
							});

							jQuery('button.single_add_to_cart_button').on('click', function(e) {
								if ( validateCreditCalled() == false ) {
									e.preventDefault();
								}
							});

							jQuery('input#credit_called').on( 'keypress', function (e) {
								if (e.which == 13) {
									jQuery('form.cart').find('button[name=\"add-to-cart\"]').trigger('click');
								}
							});

							// To handle, if the call for credit form is included twice.
							jQuery.each( jQuery('body').find('div#call_for_credit'), function(){
								let current_element = jQuery(this);
								let is_visible = current_element.is(':visible');
								if ( false === is_visible ) {
									current_element.remove();
								}
							});

						";

				// To handle, if the call for credit form is included twice.
				if ( did_action( 'wc_sc_call_for_credit_script_enqueued' ) <= 0 ) {
					wc_enqueue_js( $js );
					do_action( 'wc_sc_call_for_credit_script_enqueued', array( 'source' => $this ) );
				}

				$smart_coupon_store_gift_page_text = get_option( 'smart_coupon_store_gift_page_text' );

				/* translators: %s: singular name for store credit */
				$smart_coupon_store_gift_page_text = ( ! empty( $smart_coupon_store_gift_page_text ) ) ? $smart_coupon_store_gift_page_text . ' ' : ( ! empty( $store_credit_label['singular'] ) ? sprintf( __( 'Purchase %s worth', 'woocommerce-smart-coupons' ), ucwords( $store_credit_label['singular'] ) ) : __( 'Purchase credit worth', 'woocommerce-smart-coupons' ) ) . ' ';

				$custom_classes = array(
					'container' => '',
					'row'       => '',
					'label'     => '',
					'input'     => '',
					'error'     => '',
				);

				$custom_classes = apply_filters( 'wc_sc_call_for_credit_template_custom_classes', $custom_classes );

				$currency_symbol = get_woocommerce_currency_symbol();

				$input = array(
					'type'         => 'number',
					'autocomplete' => 'off',
					'autofocus'    => 'autofocus',
					'height'       => '',
					'max'          => '',
					'maxlength'    => '',
					'min'          => '1',
					'minlength'    => '',
					'name'         => '',
					'pattern'      => '',
					'placeholder'  => '',
					'required'     => 'required',
					'size'         => '',
					'step'         => 'any',
					'width'        => '',
				);

				$input = apply_filters( 'wc_sc_call_for_credit_template_input', $input, array( 'source' => $this ) );

				$input = array_filter( $input );

				$input['id']    = 'credit_called';
				$input['name']  = $input['id'];
				$input['value'] = '';

				$allowed_html = wp_kses_allowed_html( 'post' );

				$allowed_html['input'] = array(
					'aria-describedby' => true,
					'aria-details'     => true,
					'aria-label'       => true,
					'aria-labelledby'  => true,
					'aria-hidden'      => true,
				);
				$input_element         = '<input ';
				foreach ( $input as $attribute => $value ) {
					$input_element                      .= $attribute . '="' . esc_attr( $value ) . '" ';
					$allowed_html['input'][ $attribute ] = true;
				}
				$input_element .= ' />';

				if ( function_exists( 'wc_get_template' ) ) {
					$args = array(
						'custom_classes'  => $custom_classes,
						'currency_symbol' => $currency_symbol,
						'smart_coupon_store_gift_page_text' => $smart_coupon_store_gift_page_text,
						'allowed_html'    => $allowed_html,
						'input'           => $input,
						'input_element'   => $input_element,
					);
					wc_get_template( 'call-for-credit-form.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' );
				} else {
					include apply_filters( 'woocommerce_call_for_credit_form_template', 'templates/call-for-credit-form.php' );
				}
			}
		}

		/**
		 * Allow overridding of Smart Coupon's template for credit of any amount
		 *
		 * @param string $template The template name.
		 * @return mixed $template
		 */
		public function woocommerce_call_for_credit_form_template_path( $template ) {

			$template_name = 'call-for-credit-form.php';

			$template = $this->locate_template_for_smart_coupons( $template_name, $template );

			// Return what we found.
			return $template;
		}

		/**
		 * Make product whose price is set as zero but is for purchasing credit, purchasable
		 *
		 * @param boolean    $purchasable Is purchasable.
		 * @param WC_Product $product The product.
		 * @return boolean   $purchasable
		 */
		public function make_product_purchasable( $purchasable, $product ) {

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}
			$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );

			if ( ! empty( $coupons ) && $product instanceof WC_Product && $product->get_price() === '' && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product->get_price() > 0 ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Remove price html for product which is selling any amount of storecredit
		 *
		 * @param string     $price Current price HTML.
		 * @param WC_Product $product The product object.
		 * @return string $price
		 */
		public function price_html_for_purchasing_credit( $price = null, $product = null ) {

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}
			$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );

			$is_product            = is_a( $product, 'WC_Product' );
			$is_purchasable_credit = $this->is_coupon_amount_pick_from_product_price( $coupons );
			$product_price         = $product->get_price();

			if ( ! empty( $coupons ) && true === $is_product && true === $is_purchasable_credit && ( ! ( $product_price > 0 ) || empty( $product_price ) ) ) {
				return '';
			}

			return $price;
		}

		/**
		 * Set price for store credit to be purchased before calculating total in cart
		 *
		 * @param WC_Cart $cart_object The cart object.
		 */
		public function override_price_before_calculate_totals( $cart_object ) {

			$credit_called = WC()->session->get( 'credit_called' );

			foreach ( $cart_object->cart_contents as $key => $value ) {

				$product       = $value['data'];
				$credit_amount = ( ! empty( $value['credit_amount'] ) ) ? $value['credit_amount'] : 0;

				if ( $this->is_wc_gte_30() ) {
					$product_type  = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
					$product_id    = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ), true ) ) ? ( ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0 ) : ( ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0 );
					$product_price = ( ! empty( $product ) && is_callable( array( $product, 'get_price' ) ) ) ? $product->get_price() : 0;
				} else {
					$product_id    = ( ! empty( $product->id ) ) ? $product->id : 0;
					$product_price = ( ! empty( $product->price ) ) ? $product->price : 0;
				}

				$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );

				if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product_price > 0 ) ) {

					$price = ( ! empty( $credit_called[ $key ] ) ) ? $credit_called[ $key ] : $credit_amount;

					if ( $price <= 0 ) {
						WC()->cart->set_quantity( $key, 0 );    // Remove product from cart if price is not found either in session or in product.
						continue;
					}

					if ( $this->is_wc_gte_30() ) {
						$cart_object->cart_contents[ $key ]['data']->set_price( $price );
					} else {
						$cart_object->cart_contents[ $key ]['data']->price = $price;
					}
				}
			}

		}

		/**
		 * Override empty price of voucher product by 0 to avoid 'non numeric value' warning for product price being triggered on coupon validation.
		 *
		 * @param string     $price product price.
		 * @param WC_Product $product WooCommerce product.
		 * @return integer $price new product price
		 */
		public function override_voucher_product_empty_price( $price, $product ) {

			if ( ! doing_action( 'woocommerce_single_product_summary' ) ) {

				if ( $this->is_wc_gte_30() ) {
					$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
					$product_id   = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ), true ) ) ? ( ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0 ) : ( ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0 );
				} else {
					$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
				}

				$coupons = $this->get_coupon_titles( array( 'product_object' => $product ) );

				// Override product price only if product contains coupon and price is already an empty string.
				if ( ! empty( $coupons ) && '' === $price ) {
					$price = 0;
				}
			}

			return $price;

		}

		/**
		 * Display store credit's value as cart item's price
		 *
		 * @param string $product_price The product price HTML.
		 * @param array  $cart_item Associative array of cart item.
		 * @param string $cart_item_key The cart item key.
		 * @return string product's price with currency symbol
		 */
		public function woocommerce_cart_item_price_html( $product_price, $cart_item, $cart_item_key ) {

			$gift_certificate = ( is_object( WC()->session ) && is_callable( array( WC()->session, 'get' ) ) ) ? WC()->session->get( 'credit_called' ) : array();

			if ( ! empty( $gift_certificate ) && isset( $gift_certificate[ $cart_item_key ] ) && ! empty( $gift_certificate[ $cart_item_key ] ) ) {
				$price = $gift_certificate[ $cart_item_key ];
				// Hook for 3rd party plugin to modify value of the credit.
				$price = apply_filters(
					'wc_sc_credit_called_price_cart',
					$price,
					array(
						'source'        => $this,
						'cart_item_key' => $cart_item_key,
						'cart_item'     => $cart_item,
						'credit_called' => $gift_certificate,
					)
				);
				return wc_price( $price );
			} elseif ( ! empty( $cart_item['credit_amount'] ) ) {
				$price = $cart_item['credit_amount'];
				// Hook for 3rd party plugin to modify value of the credit.
				$price = apply_filters(
					'wc_sc_credit_called_price_cart',
					$price,
					array(
						'source'        => $this,
						'cart_item_key' => $cart_item_key,
						'cart_item'     => $cart_item,
						'credit_called' => $gift_certificate,
					)
				);
				return wc_price( $price );
			}

			return $product_price;

		}

		/**
		 * Receiver Detail Form Styles And Scripts
		 */
		public function receiver_detail_form_styles_and_scripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<!-- WooCommerce Smart Coupons Styles And Scripts Start -->
			<style type="text/css" media="screen">

				.wc-sc-toggle-check-input {
					width: 0;
					height: 0;
					visibility: hidden;
				}

				.wc-sc-toggle-check-text {
					display: inline-block;
					position: relative;
					text-transform: uppercase;
					font-size: small;
					background: #71b02f;
					padding: 0 .8rem 0 1.5rem;
					border-radius: 1rem;
					color: #fff;
					cursor: pointer;
					transition: background-color 0.15s;
				}

				.wc-sc-toggle-check-text:after {
					content: ' ';
					display: block;
					background: #fff;
					width: 0.8rem;
					height: 0.8rem;
					border-radius: 0.8rem;
					position: absolute;
					left: 0.3rem;
					top: 0.25rem;
					transition: left 0.15s, margin-left 0.15s;
				}

				.wc-sc-toggle-check-text:before {
					content: '<?php echo esc_attr__( 'Now', 'woocommerce-smart-coupons' ); ?>';
				}

				.wc-sc-toggle-check-input:checked ~ .wc-sc-toggle-check-text {
					background: #96588a;
					padding-left: .5rem;
					padding-right: 1.4rem;
				}

				.wc-sc-toggle-check-input:checked ~ .wc-sc-toggle-check-text:before {
					content: '<?php echo esc_attr__( 'Later', 'woocommerce-smart-coupons' ); ?>';
				}

				.wc-sc-toggle-check-input:checked ~ .wc-sc-toggle-check-text:after {
					left: 100%;
					margin-left: -1.1rem;
				}

			</style>
			<script type="text/javascript">
				jQuery(function(){
					var is_multi_form = function() {
						var creditCount = jQuery('div#gift-certificate-receiver-form-multi div.form_table').length;

						if ( creditCount <= 1 ) {
							return false;
						} else {
							return true;
						}
					};

					jQuery('form.woocommerce-checkout').on('click', 'input#show_form', function(){
						if ( is_multi_form() ) {
							jQuery('ul.single_multi_list').slideDown();
						}
						jQuery('div.gift-certificate-receiver-detail-form').slideDown();
						jQuery('.wc_sc_schedule_gift_sending_wrapper').addClass('show');
					});
					jQuery('form.woocommerce-checkout').on('click', 'input#hide_form', function(){
						if ( is_multi_form() ) {
							jQuery('ul.single_multi_list').slideUp();
						}
						jQuery('div.gift-certificate-receiver-detail-form').slideUp();
						jQuery('.wc_sc_schedule_gift_sending_wrapper').removeClass('show');
					});
					jQuery('form.woocommerce-checkout').on('change', 'input[name=sc_send_to]', function(){
						jQuery('div#gift-certificate-receiver-form-single').slideToggle(1);
						jQuery('div#gift-certificate-receiver-form-multi').slideToggle(1);
					});
					jQuery('form.woocommerce-checkout').on('change', 'input#wc_sc_schedule_gift_sending', function(){
						if( jQuery(this).is(':checked') ) {
							jQuery('.email_sending_date_time_wrapper').addClass('show');
						} else {
							jQuery('.email_sending_date_time_wrapper').removeClass('show');
						}
					});
					jQuery('form.woocommerce-checkout').on('change', 'input#wc_sc_schedule_gift_sending', function(){
						if ( jQuery(this).is(':checked') && false === jQuery('.gift_sending_date_time').hasClass('hasDatepicker') ) {
							setTimeout(function(){
								jQuery('input.gift_sending_date_time')
								.datetimepicker({ 
													dateFormat: 'dd-mm-yy', 
													minDate: 0,
													inline: true,
													changeMonth: true, 
													changeYear: true,
													timeInput: true,
												});
							}, 10);
						}
					});
					jQuery('form.woocommerce-checkout').on('change', 'input.gift_sending_date_time', function(e){
						let date_time_wrapper           = jQuery(this).closest('.email_sending_date_time_wrapper');
						let gift_sending_date_time      = jQuery(date_time_wrapper).find('.gift_sending_date_time').val();
						if( '' === gift_sending_date_time ) {
							return;
						}
						gift_sending_date_time          = gift_sending_date_time.split(' ');
						let gift_sending_date           = gift_sending_date_time[0];
						let gift_sending_time           = gift_sending_date_time[1];
						gift_sending_date               = gift_sending_date.split('-');
						gift_sending_date               = gift_sending_date[2] + '-' + gift_sending_date[1] + '-' + gift_sending_date[0]; // Convert date from dd-mm-yy format to yy-mm-dd format
						let gift_sending_date_time_time = gift_sending_date + ' ' + gift_sending_time;
						let gift_sending_utc_date_time  = new Date(gift_sending_date_time_time).toUTCString();
						let utc_date_time               = new Date(gift_sending_utc_date_time);
						let gift_sending_timestamp      = Math.floor(utc_date_time.getTime()/1000); // Remove milliseconds from timestamp.
						jQuery(date_time_wrapper).find('.gift_sending_timestamp').val(gift_sending_timestamp);
					});
				});
			</script>
			<!-- WooCommerce Smart Coupons Styles And Scripts End -->
			<?php
		}

		/**
		 * Function to display form for entering details of the gift certificate's receiver
		 */
		public function gift_certificate_receiver_detail_form() {
			global $total_coupon_amount;

			$is_show = apply_filters( 'is_show_gift_certificate_receiver_detail_form', true, array() );

			if ( ! $is_show ) {
				return;
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			if ( ! wp_doing_ajax() ) {
				add_action( 'wp_footer', array( $this, 'receiver_detail_form_styles_and_scripts' ) );
			}

			$form_started = false;

			$all_discount_types = wc_get_coupon_types();

			$schedule_store_credit = get_option( 'smart_coupons_schedule_store_credit' );

			foreach ( WC()->cart->cart_contents as $product ) {

				if ( ! empty( $product['variation_id'] ) ) {
					$_product = wc_get_product( $product['variation_id'] );
				} elseif ( ! empty( $product['product_id'] ) ) {
					$_product = wc_get_product( $product['product_id'] );
				} else {
					continue;
				}

				$coupon_titles = $this->get_coupon_titles( array( 'product_object' => $_product ) );

				$price = $_product->get_price();

				if ( $coupon_titles ) {

					foreach ( $coupon_titles as $coupon_title ) {

						$coupon = new WC_Coupon( $coupon_title );
						if ( $this->is_wc_gte_30() ) {
							if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
								continue;
							}
							$coupon_id = $coupon->get_id();
							if ( empty( $coupon_id ) ) {
								continue;
							}
							$discount_type = $coupon->get_discount_type();
							$coupon_amount = $coupon->get_amount();
						} else {
							$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						}

						$pick_price_of_prod                              = get_post_meta( $coupon_id, 'is_pick_price_of_product', true );
						$smart_coupon_gift_certificate_form_page_text    = get_option( 'smart_coupon_gift_certificate_form_page_text' );
						$smart_coupon_gift_certificate_form_page_text    = ( ! empty( $smart_coupon_gift_certificate_form_page_text ) ) ? $smart_coupon_gift_certificate_form_page_text : __( 'Send Coupons to...', 'woocommerce-smart-coupons' );
						$smart_coupon_gift_certificate_form_details_text = get_option( 'smart_coupon_gift_certificate_form_details_text' );
						$smart_coupon_gift_certificate_form_details_text = ( ! empty( $smart_coupon_gift_certificate_form_details_text ) ) ? $smart_coupon_gift_certificate_form_details_text : '';     // Enter email address and optional message for Gift Card receiver.

						// MADE CHANGES IN THE CONDITION TO SHOW FORM.
						if ( array_key_exists( $discount_type, $all_discount_types ) || ( 'yes' === $pick_price_of_prod && '' === $price ) || ( 'yes' === $pick_price_of_prod && '' !== $price && $coupon_amount > 0 ) ) {

							if ( ! $form_started ) {
								$is_show_coupon_receiver_form = get_option( 'smart_coupons_display_coupon_receiver_details_form', 'yes' );
								if ( 'no' === $is_show_coupon_receiver_form ) {
									?>
									<div class="gift-certificate sc_info_box">
										<p><?php echo esc_html__( 'Your order contains coupons. You will receive them after completion of this order.', 'woocommerce-smart-coupons' ); ?></p>
									</div>
									<?php
								}
								?>
								<div class="gift-certificate sc_info_box" <?php echo ( 'no' === $is_show_coupon_receiver_form ) ? 'style="' . esc_attr( 'display: none;' ) . '"' : ''; ?>>
									<h3><?php echo esc_html( stripslashes( $smart_coupon_gift_certificate_form_page_text ) ); ?></h3>
										<?php if ( ! empty( $smart_coupon_gift_certificate_form_details_text ) ) { ?>
										<p><?php echo esc_html( stripslashes( $smart_coupon_gift_certificate_form_details_text ) ); ?></p>
										<?php } ?>
										<div class="gift-certificate-show-form">
											<p><?php echo esc_html__( 'Your order contains coupons. What would you like to do?', 'woocommerce-smart-coupons' ); ?></p>
											<ul class="show_hide_list" style="list-style-type: none;">
												<li><input type="radio" id="hide_form" name="is_gift" value="no" checked="checked" /> <label for="hide_form"><?php echo esc_html__( 'Send to me', 'woocommerce-smart-coupons' ); ?></label></li>
												<li>
												<input type="radio" id="show_form" name="is_gift" value="yes" /> <label for="show_form"><?php echo esc_html__( 'Gift to someone else', 'woocommerce-smart-coupons' ); ?></label>
												<ul class="single_multi_list" style="list-style-type: none;">
												<li><input type="radio" id="send_to_one" name="sc_send_to" value="one" checked="checked" /> <label for="send_to_one"><?php echo esc_html__( 'Send to one person', 'woocommerce-smart-coupons' ); ?></label></li>
												<li><input type="radio" id="send_to_many" name="sc_send_to" value="many" /> <label for="send_to_many"><?php echo esc_html__( 'Send to different people', 'woocommerce-smart-coupons' ); ?></label></li>
												</ul>
												<?php if ( 'yes' === $schedule_store_credit ) { ?>
													<li class="wc_sc_schedule_gift_sending_wrapper">
														<?php echo esc_html__( 'Deliver coupon', 'woocommerce-smart-coupons' ); ?>
														<label class="wc-sc-toggle-check">
															<input type="checkbox" class="wc-sc-toggle-check-input" id="wc_sc_schedule_gift_sending" name="wc_sc_schedule_gift_sending" value="yes" />
															<span class="wc-sc-toggle-check-text"></span>
														</label>
													</li>
												<?php } ?>
												</li>
											</ul>
										</div>
								<div class="gift-certificate-receiver-detail-form">
								<div class="clear"></div>
								<div id="gift-certificate-receiver-form-multi">
								<?php

								$form_started = true;

							}

							$this->add_text_field_for_email( $coupon, $product );

						}
					}
				}
			}

			if ( $form_started ) {
				?>
				</div>
				<div id="gift-certificate-receiver-form-single">
					<div class="form_table">
						<div class="email_amount">
							<div class="amount"></div>
							<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php echo esc_attr__( 'Enter recipient e-mail address', 'woocommerce-smart-coupons' ); ?>..." name="gift_receiver_email[0][0]" value="" /></div>
						</div>
						<div class="email_sending_date_time_wrapper">
								<input class="gift_sending_date_time" type="text" placeholder="<?php echo esc_attr__( 'Pick a delivery date & time', 'woocommerce-smart-coupons' ); ?>..." name="gift_sending_date_time[0][0]" value="" autocomplete="off"/>
								<input class="gift_sending_timestamp" type="hidden" name="gift_sending_timestamp[0][0]" value=""/>
						</div>
						<div class="message_row">
							<div class="message"><textarea placeholder="<?php echo esc_attr__( 'Write a message', 'woocommerce-smart-coupons' ); ?>..." class="gift_receiver_message" name="gift_receiver_message[0][0]" cols="50" rows="5"></textarea></div>
						</div>
					</div>
				</div>
				</div></div>
				<?php
				do_action( 'wc_sc_gift_certificate_form_shown' );
			}

		}

		/**
		 * Function to add hooks based on conditions
		 */
		public function purchase_credit_hooks() {

			if ( $this->is_wc_gte_30() ) {
				add_action( 'woocommerce_new_order_item', array( $this, 'save_called_credit_details_in_order_item' ), 10, 3 );
			} else {
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'save_called_credit_details_in_order_item_meta' ), 10, 2 );
			}

		}

		/**
		 * Display form to enter receiver's details on checkout page
		 *
		 * @param WC_Coupon $coupon The coupon object.
		 * @param array     $product The product object.
		 */
		public function add_text_field_for_email( $coupon = '', $product = '' ) {
			global $total_coupon_amount;

			if ( empty( $coupon ) ) {
				return;
			}

			$sell_sc_at_less_price = get_option( 'smart_coupons_sell_store_credit_at_less_price', 'no' );

			$coupon_data = $this->get_coupon_meta_data( $coupon );

			if ( $this->is_wc_gte_30() ) {
				$coupon_id        = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : '';
				$coupon_code      = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
				$product_price    = ( is_object( $product['data'] ) && is_callable( array( $product['data'], 'get_price' ) ) ) ? $product['data']->get_price() : 0;
				$coupon_amount    = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
				$is_free_shipping = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? ( ( $coupon->get_free_shipping() ) ? 'yes' : 'no' ) : '';
				$discount_type    = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				$product_price    = ( ! empty( $product['data']->price ) ) ? $product['data']->price : 0;
				$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
				$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
				$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			for ( $i = 1; $i <= $product['quantity']; $i++ ) {

				if ( $this->is_coupon_amount_pick_from_product_price( array( $coupon_code ) ) ) {
					if ( 'yes' === $sell_sc_at_less_price ) {
						$_coupon_amount = ( is_object( $product['data'] ) && is_callable( array( $product['data'], 'get_regular_price' ) ) ) ? $product['data']->get_regular_price() : 0;
						if ( empty( $_coupon_amount ) && ! empty( $product_price ) ) {
							$_coupon_amount = $product_price;
						}
					} else {
						$_coupon_amount = $product_price;
					}
				} else {
					$_coupon_amount = $coupon_amount;
				}

				// NEWLY ADDED CONDITION TO NOT TO SHOW TEXTFIELD IF COUPON AMOUNT IS "0".
				// TODO: Free Gift Coupon: Due to coupon amount as zero, following condition is not showing multiple form to send coupon to different people.
				if ( '' !== $_coupon_amount || $_coupon_amount > 0 || $coupon_amount > 0 || 'yes' === $is_free_shipping ) {

					$total_coupon_amount += $_coupon_amount;

					$formatted_coupon_text   = '';
					$formatted_coupon_amount = 0;
					if ( ! empty( $_coupon_amount ) || ! empty( $coupon_amount ) ) {
						$formatted_coupon_amount = ( $coupon_amount <= 0 ) ? wc_price( $_coupon_amount ) : $coupon_data['coupon_amount'];
						$formatted_coupon_text  .= $coupon_data['coupon_type'];
						if ( 'yes' === $is_free_shipping ) {
							$formatted_coupon_text .= ' &amp; ';
						}
					}
					if ( 'yes' === $is_free_shipping ) {
						$formatted_coupon_text .= __( 'Free Shipping coupon', 'woocommerce-smart-coupons' );
					}
					if ( 'smart_coupon' !== $discount_type && strpos( $formatted_coupon_text, 'coupon' ) === false ) {
						$formatted_coupon_text .= ' ' . __( 'coupon', 'woocommerce-smart-coupons' );
					}
					?>
					<div class="form_table">
						<div class="email_amount">
							<?php /* translators: 1. Coupon type 2. Coupon amount */ ?>
							<div class="amount"><?php echo esc_html__( 'Send', 'woocommerce-smart-coupons' ) . ' ' . esc_html( $formatted_coupon_text ) . ' ' . esc_html__( 'of', 'woocommerce-smart-coupons' ) . ' ' . $formatted_coupon_amount; // phpcs:ignore ?></div>
							<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php echo esc_attr__( 'Enter recipient e-mail address', 'woocommerce-smart-coupons' ); ?>..." name="gift_receiver_email[<?php echo esc_attr( $coupon_id ); ?>][]" value="" /></div>
						</div>
						<div class="email_sending_date_time_wrapper">
							<div class="sending_date_time"></div>
							<div class="email">
								<input class="gift_sending_date_time" type="text" placeholder="<?php echo esc_attr__( 'Pick a delivery date & time', 'woocommerce-smart-coupons' ); ?>..." name="gift_sending_date_time[<?php echo esc_attr( $coupon_id ); ?>][]" value="" autocomplete="off"/>
								<input class="gift_sending_timestamp" type="hidden" name="gift_sending_timestamp[<?php echo esc_attr( $coupon_id ); ?>][]" value=""/>
							</div>
						</div>

						<div class="message_row">
							<div class="sc_message"><textarea placeholder="<?php echo esc_attr__( 'Write a message', 'woocommerce-smart-coupons' ); ?>..." class="gift_receiver_message" name="gift_receiver_message[<?php echo esc_attr( $coupon_id ); ?>][]" cols="50" rows="5"></textarea></div>
						</div>
					</div>
					<?php
				}
			}

		}

		/**
		 * Save entered credit value by customer in order for further processing
		 *
		 * @param int   $order_id The order id.
		 * @param array $posted Associative array of posted data.
		 */
		public function save_called_credit_details_in_order( $order_id, $posted ) {

			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items();

			$sc_called_credit = array();
			$update           = false;

			$prices_include_tax = ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' ) ? true : false;

			foreach ( $order_items as $item_id => $order_item ) {

				$item_sc_called_credit = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_meta' ) ) ) ? $order_item->get_meta( 'sc_called_credit' ) : ( ( ! empty( $order_item['sc_called_credit'] ) ) ? $order_item['sc_called_credit'] : 0 );

				if ( ! empty( $item_sc_called_credit ) ) {

					$product = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_product' ) ) ) ? $order_item->get_product() : $order->get_product_from_item( $order_item );

					if ( ! is_object( $product ) ) {
						continue;
					}

					if ( $this->is_wc_gte_30() ) {
						$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
						$product_id   = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ), true ) ) ? ( ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0 ) : ( ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0 );
						$item_qty     = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_quantity' ) ) ) ? $order_item->get_quantity() : 1;
						$item_tax     = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_subtotal_tax' ) ) ) ? $order_item->get_subtotal_tax() : 0;
					} else {
						$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
						$item_qty   = ( ! empty( $order_item['qty'] ) ) ? $order_item['qty'] : 1;
						$item_tax   = ( ! empty( $order_item['line_subtotal_tax'] ) ) ? $order_item['line_subtotal_tax'] : 0;
					}

					if ( true === $prices_include_tax && ! $this->is_generated_store_credit_includes_tax() ) {
						$item_total = $item_sc_called_credit - $item_tax;
					} elseif ( false === $prices_include_tax && $this->is_generated_store_credit_includes_tax() ) {
						$item_total = $item_sc_called_credit + $item_tax;
					} else {
						$item_total = $item_sc_called_credit;
					}

					if ( ! empty( $item_qty ) ) {
						$qty = $item_qty;
					} else {
						$qty = 1;
					}

					$coupon_titles = $this->get_coupon_titles( array( 'product_object' => $product ) );

					if ( $coupon_titles ) {

						foreach ( $coupon_titles as $coupon_title ) {

							$coupon = new WC_Coupon( $coupon_title );

							if ( $this->is_wc_gte_30() ) {
								if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
									continue;
								}
								$coupon_id = $coupon->get_id();
								if ( empty( $coupon_id ) ) {
									continue;
								}
								$coupon_amount = $coupon->get_amount();
							} else {
								$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
								$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							}

							if ( $this->is_coupon_amount_pick_from_product_price( array( $coupon_title ) ) ) {
								$products_price               = ( ! $prices_include_tax ) ? $item_total : $item_total + $item_tax;
								$amount                       = $products_price / $qty;
								$sc_called_credit[ $item_id ] = wc_round_discount( $amount, 2 );
								$update                       = true;
							}
						}
					}

					if ( $this->is_wc_gte_30() ) {
						wc_delete_order_item_meta( $item_id, 'sc_called_credit' );
					} else {
						woocommerce_delete_order_item_meta( $item_id, 'sc_called_credit' );
					}
				}
			}
			if ( $update ) {
				update_post_meta( $order_id, 'sc_called_credit_details', $sc_called_credit );
			}

			if ( isset( WC()->session->credit_called ) ) {
				unset( WC()->session->credit_called );
			}

		}

		/**
		 * Save entered credit value by customer in order item meta
		 *
		 * @param int   $item_id The order item id.
		 * @param array $values Associative array containing item's details.
		 */
		public function save_called_credit_details_in_order_item_meta( $item_id = 0, $values = array() ) {

			if ( empty( $item_id ) || empty( $values ) ) {
				return;
			}
			$sell_sc_at_less_price = get_option( 'smart_coupons_sell_store_credit_at_less_price', 'no' );
			if ( $this->is_wc_gte_30() ) {
				if ( ! $values instanceof WC_Order_Item_Product ) {
					return;
				}
				$product = $values->get_product();
				if ( ! is_object( $product ) || ! is_a( $product, 'WC_Product' ) ) {
					return;
				}
				$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
				$product_id   = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ), true ) ) ? ( ( is_object( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0 ) : ( ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0 );
				$qty          = ( is_callable( array( $values, 'get_quantity' ) ) ) ? $values->get_quantity() : 1;
				$qty          = ( ! empty( $qty ) ) ? $qty : 1;

				// Check if selling store credit at less price is enabled.
				if ( 'yes' === $sell_sc_at_less_price ) {
					// For gift certificate of any amount.
					if ( isset( $values->legacy_values['credit_amount'] ) && ! empty( $values->legacy_values['credit_amount'] ) ) {
						$product_price = $values->legacy_values['credit_amount'] * $qty;
					} else {
						// For gift certificate of fixed price.
						$product_price = ( is_callable( array( $product, 'get_regular_price' ) ) ) ? $product->get_regular_price() * $qty : 0;
					}
				} else {
					$subtotal      = ( is_callable( array( $values, 'get_subtotal' ) ) ) ? $values->get_subtotal() : 0;
					$product_price = $subtotal;
				}
			} else {
				if ( empty( $values['data'] ) ) {
					return;
				}
				$product       = $values['data'];
				$product_id    = ( ! empty( $values['product_id'] ) ) ? $values['product_id'] : 0;
				$product_price = ( ! empty( $values['data']->price ) ) ? $values['data']->price : 0;
			}

			if ( empty( $product_id ) ) {
				return;
			}

			$coupon_titles = $this->get_coupon_titles( array( 'product_object' => $product ) );

			if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product_price > 0 ) {
				if ( $this->is_wc_gte_30() ) {
					wc_add_order_item_meta( $item_id, 'sc_called_credit', $product_price );
				} else {
					woocommerce_add_order_item_meta( $item_id, 'sc_called_credit', $product_price );
				}
			}
		}

		/**
		 * Save entered credit value by customer in order item
		 *
		 * @param int   $item_id The order item id.
		 * @param array $values Associative array containing item's details.
		 * @param int   $order_id The order id.
		 */
		public function save_called_credit_details_in_order_item( $item_id = 0, $values = array(), $order_id = 0 ) {

			$this->save_called_credit_details_in_order_item_meta( $item_id, $values );

		}

		/**
		 * Save entered credit value by customer in order for PayPal Express Checkout
		 *
		 * @param WC_Order $order The order object.
		 */
		public function ppe_save_called_credit_details_in_order( $order ) {
			if ( $this->is_wc_gte_30() ) {
				$order_id = ( is_object( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
			} else {
				$order_id = ( ! empty( $order->id ) ) ? $order->id : 0;
			}
			$this->save_called_credit_details_in_order( $order_id, null );
		}

		/**
		 * Save entered credit value by customer in cart item data
		 *
		 * This function is only for simple products.
		 *
		 * @param array $cart_item_data The cart item data.
		 * @param int   $product_id The product id.
		 * @param int   $variation_id The variation id.
		 * @return array $cart_item_data
		 */
		public function call_for_credit_cart_item_data( $cart_item_data = array(), $product_id = '', $variation_id = '' ) {

			if ( ! empty( $variation_id ) && $variation_id > 0 || empty( $product_id ) ) {
				return $cart_item_data;
			}

			$_product = wc_get_product( $product_id );

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $_product->get_price() > 0 ) ) {
				$request_credit_called           = ( ! empty( $_REQUEST['credit_called'] ) ) ? wc_clean( wp_unslash( $_REQUEST['credit_called'] ) ) : array(); // phpcs:ignore
				$request_add_to_cart             = ( ! empty( $_REQUEST['add-to-cart'] ) ) ? wc_clean( wp_unslash( $_REQUEST['add-to-cart'] ) ) : 0; // phpcs:ignore
				$product_id                      = apply_filters(
					'wc_sc_call_for_credit_product_id',
					$request_add_to_cart,
					array(
						'cart_item_data' => $cart_item_data,
						'product_id'     => $product_id,
						'variation_id'   => $variation_id,
						'source'         => $this,
					)
				);
				$cart_item_data['credit_amount'] = ( ! empty( $request_credit_called ) && ! empty( $product_id ) && ! empty( $request_credit_called[ $product_id ] ) ) ? $request_credit_called[ $product_id ] : 0;
				return $cart_item_data;
			}

			return $cart_item_data;
		}

		/**
		 * Save entered credit value by customer in session
		 *
		 * This function is only for simple products.
		 *
		 * @param string $cart_item_key The cart item key.
		 * @param int    $product_id The product id.
		 * @param int    $quantity The product quantity.
		 * @param int    $variation_id The variation id.
		 * @param array  $variation The variation data.
		 * @param array  $cart_item_data The cart item data.
		 */
		public function save_called_credit_in_session( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			if ( ! empty( $variation_id ) && $variation_id > 0 ) {
				return;
			}
			if ( ! isset( $cart_item_data['credit_amount'] ) || empty( $cart_item_data['credit_amount'] ) ) {
				return;
			}

			$_product = wc_get_product( $product_id );

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $_product->get_price() > 0 ) ) {
				$credit_called = WC()->session->get( 'credit_called' );
				if ( empty( $credit_called ) || ! is_array( $credit_called ) ) {
					$credit_called = array();
				}
				$credit_called[ $cart_item_key ] = $cart_item_data['credit_amount'];
				WC()->session->set( 'credit_called', $credit_called );
			}

		}

		/**
		 * Enqueue required styles/scripts for store credit frontend form
		 */
		public function enqueue_styles_scripts() {

			// Return if gift certificate form is not shown.
			if ( ! did_action( 'wc_sc_gift_certificate_form_shown' ) ) {
				return;
			}

			$this->enqueue_timepicker();

		}

		/**
		 * Add timepicker
		 */
		public function enqueue_timepicker() {

			if ( is_callable( 'WC_Smart_Coupons::get_smart_coupons_plugin_data' ) ) {
				$plugin_data = WC_Smart_Coupons::get_smart_coupons_plugin_data();
				$version     = $plugin_data['Version'];
			} else {
				$version = '';
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( ! wp_style_is( 'jquery-ui-style', 'registered' ) ) {
				wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui' . $suffix . '.css', array(), WC()->version );
			}

			if ( ! wp_style_is( 'jquery-ui-timepicker', 'registered' ) ) {
				wp_register_style( 'jquery-ui-timepicker', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/css/jquery-ui-timepicker-addon' . $suffix . '.css', array( 'jquery-ui-style' ), $version );
			}

			if ( ! wp_style_is( 'jquery-ui-timepicker' ) ) {
				wp_enqueue_style( 'jquery-ui-timepicker' );
			}

			if ( ! wp_script_is( 'jquery-ui-timepicker', 'registered' ) ) {
				wp_register_script( 'jquery-ui-timepicker', untrailingslashit( plugins_url( '/', WC_SC_PLUGIN_FILE ) ) . '/assets/js/jquery-ui-timepicker-addon' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ), $version, true );
			}

			if ( ! wp_script_is( 'jquery-ui-timepicker' ) ) {
				wp_enqueue_script( 'jquery-ui-timepicker' );
			}

		}

	}

}

WC_SC_Purchase_Credit::get_instance();
