<?php
/**
 * Class to handle feature Coupons By Payment Method
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Payment_Method' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Payment_Method
	 */
	class WC_SC_Coupons_By_Payment_Method {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;


		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 3 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'wc_sc_export_coupon_meta', array( $this, 'export_coupon_meta_data' ), 10, 2 );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'process_coupon_meta_value_for_import' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_payment_method_meta' ) );
			add_action( 'wp_footer', array( $this, 'styles_and_scripts' ) );
		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
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
		public function __call( $function_name = '', $arguments = array() ) {

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
		 * Display field for coupon by payment method
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {

			$payment_method_ids = array();
			if ( ! empty( $coupon_id ) ) {
				$payment_method_ids = $this->get_post_meta( $coupon_id, 'wc_sc_payment_method_ids', true );
				if ( empty( $payment_method_ids ) || ! is_array( $payment_method_ids ) ) {
					$payment_method_ids = array();
				}
			}
			$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
			?>
			<div class="options_group smart-coupons-field">
				<p class="form-field">
					<label for="wc_sc_payment_method_ids"><?php echo esc_html__( 'Payment methods', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_payment_method_ids" name="wc_sc_payment_method_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No payment methods', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( is_array( $available_payment_methods ) && ! empty( $available_payment_methods ) ) {
							foreach ( $available_payment_methods as $payment_method ) {
								echo '<option value="' . esc_attr( $payment_method->id ) . '"' . esc_attr( selected( in_array( $payment_method->id, $payment_method_ids, true ), true, false ) ) . '>' . esc_html( $payment_method->get_title() ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					$tooltip_text = esc_html__( 'Payment methods that must be selected during checkout for this coupon to be valid.', 'woocommerce-smart-coupons' );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Save coupon by payment method data in meta
		 *
		 * @param  Integer   $post_id The coupon post ID.
		 * @param  WC_Coupon $coupon    The coupon object.
		 */
		public function process_meta( $post_id = 0, $coupon = null ) {
			if ( empty( $post_id ) ) {
				return;
			}

			$coupon = new WC_Coupon( $coupon );

			$payment_method_ids = ( isset( $_POST['wc_sc_payment_method_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_payment_method_ids'] ) ) : array(); // phpcs:ignore

			if ( $this->is_callable( $coupon, 'update_meta_data' ) && $this->is_callable( $coupon, 'save' ) ) {
				$coupon->update_meta_data( 'wc_sc_payment_method_ids', $payment_method_ids );
				$coupon->save();
			} else {
				$this->update_post_meta( $post_id, 'wc_sc_payment_method_ids', $payment_method_ids );
			}
		}

		/**
		 * Validate the coupon based on payment method
		 *
		 * @param  boolean      $valid  Is valid or not.
		 * @param  WC_Coupon    $coupon The coupon object.
		 * @param  WC_Discounts $discounts The discount object.
		 *
		 * @throws Exception If the coupon is invalid.
		 * @return boolean           Is valid or not
		 */
		public function validate( $valid = false, $coupon = object, $discounts = object ) {

			// If coupon is already invalid, no need for further checks.
			if ( false === $valid ) {
				return $valid;
			}

			if ( ! $coupon instanceof WC_Coupon ) {
				return $valid;
			}

			if ( ! $discounts instanceof WC_Discounts ) {
				return $valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$coupon_id   = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$coupon_code = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$coupon_id   = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			if ( empty( $coupon_id ) ) {
				return $valid;
			}

			$payment_method_ids   = $this->get_post_meta( $coupon_id, 'wc_sc_payment_method_ids', true );
			$cart_or_order_object = is_callable( array( $discounts, 'get_object' ) ) ? $discounts->get_object() : null;
			$is_wc_session        = is_a( $cart_or_order_object, 'WC_Cart' ) && function_exists( 'WC' ) && isset( WC()->session ) && is_object( WC()->session );
			$needs_payment        = ( $this->is_callable( $cart_or_order_object, 'needs_payment' ) ) ? $cart_or_order_object->needs_payment() : true;
			$posted_data          = array();
			$post_data            = isset( $_POST['post_data'] ) && ! empty( $_POST['post_data'] ) ? wc_clean( wp_unslash( $_POST['post_data'] ) ) : ''; // phpcs:ignore
			wp_parse_str( $post_data, $posted_data );

			if ( true === $needs_payment && is_array( $payment_method_ids ) && ! empty( $payment_method_ids ) ) {
				$payment_titles        = $this->get_payment_method_titles_by_ids( $payment_method_ids );
				$chosen_payment_method = '';
				if ( is_a( $cart_or_order_object, 'WC_Order' ) ) {
					$chosen_payment_method = is_callable( array( $cart_or_order_object, 'get_payment_method' ) ) ? $cart_or_order_object->get_payment_method() : '';
				} elseif ( ! empty( $posted_data ) && isset( $posted_data['payment_method'] ) ) {
					$chosen_payment_method = $posted_data['payment_method'];
				} elseif ( true === $is_wc_session ) {
					$chosen_payment_method = ( WC()->session->__isset( 'chosen_payment_method' ) ) ? WC()->session->get( 'chosen_payment_method' ) : '';
				}
				if ( ! in_array( $chosen_payment_method, $payment_method_ids, true ) ) {
					if ( true === $is_wc_session && is_callable( array( WC()->session, 'set' ) ) ) {
						WC()->session->set( 'wc_sc_reload_payment_method', 'yes' );
					}

					$applied_coupons = ( WC()->cart instanceof WC_Cart && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();
					if ( ! empty( $applied_coupons ) && in_array( $coupon_code, $applied_coupons, true ) ) {
						WC()->cart->remove_coupon( $coupon_code );
						/* translators: 1. The coupon code 2. The text 'payment method/s' 3. List of payment method names 4. Link to the checkout page */
						wc_add_notice( sprintf( __( 'Coupon code %1$s has been removed. It is valid only for %2$s: %3$s. You can change the payment method from the %4$s page.', 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>', _n( 'payment method', 'payment methods', count( $payment_titles ), 'woocommerce-smart-coupons' ), '<strong>"' . implode( '", "', $payment_titles ) . '"</strong>', '<a href="' . esc_url( wc_get_checkout_url() ) . '"><strong>' . __( 'Checkout', 'woocommerce-smart-coupons' ) . '</strong></a>' ), 'error' );
					}

					/* translators: 1. The coupon code 2. The text 'payment method/s' 3. List of payment method names 4. Link to the checkout page */
					throw new Exception( sprintf( __( 'Coupon code %1$s is valid only for %2$s: %3$s. You can change payment method from the %4$s page.', 'woocommerce-smart-coupons' ), '<code>' . $coupon_code . '</code>', _n( 'payment method', 'payment methods', count( $payment_titles ), 'woocommerce-smart-coupons' ), '<strong>"' . implode( '", "', $payment_titles ) . '"</strong>', '<a href="' . esc_url( wc_get_checkout_url() ) . '"><strong>' . __( 'Checkout', 'woocommerce-smart-coupons' ) . '</strong></a>' ) );
				}
				if ( true === $is_wc_session && is_callable( array( WC()->session, 'set' ) ) ) {
					WC()->session->set( 'wc_sc_reload_payment_method', 'no' );
				}
			}

			return $valid;

		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$headers['wc_sc_payment_method_ids'] = __( 'Payment methods', 'woocommerce-smart-coupons' );

			return $headers;

		}

		/**
		 * Function to handle coupon meta data during export of existing coupons
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional arguments.
		 * @return string Processed meta value
		 */
		public function export_coupon_meta_data( $meta_value = '', $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && 'wc_sc_payment_method_ids' === $args['meta_key'] ) {
				if ( isset( $args['meta_value'] ) && ! empty( $args['meta_value'] ) ) {
					$payment_method_ids = maybe_unserialize( stripslashes( $args['meta_value'] ) );
					if ( is_array( $payment_method_ids ) && ! empty( $payment_method_ids ) ) {
						$payment_method_titles = $this->get_payment_method_titles_by_ids( $payment_method_ids );
						if ( is_array( $payment_method_titles ) && ! empty( $payment_method_titles ) ) {
							$meta_value = implode( '|', wc_clean( wp_unslash( $payment_method_titles ) ) );  // Replace payment method ids with their respective method titles.
						}
					}
				}
			}

			return $meta_value;

		}

		/**
		 * Post meta defaults for payment method ids meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array $defaults Modified postmeta defaults
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_payment_method_ids'] = '';

			return $defaults;
		}

		/**
		 * Add payment method's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array $data Modified row data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$payment_method_titles = '';

			if ( ! empty( $post['wc_sc_payment_method_ids'] ) && is_array( $post['wc_sc_payment_method_ids'] ) ) {
				$payment_method_titles = $this->get_payment_method_titles_by_ids( $post['wc_sc_payment_method_ids'] );
				if ( is_array( $payment_method_titles ) && ! empty( $payment_method_titles ) ) {
					$payment_method_titles = implode( '|', wc_clean( wp_unslash( $payment_method_titles ) ) );
				}
			}

			$data['wc_sc_payment_method_ids'] = $payment_method_titles; // Replace payment method ids with their respective method titles.

			return $data;
		}

		/**
		 * Function to get payment method titles for given payment method ids
		 *
		 * @param  array $payment_method_ids ids of payment methods.
		 * @return array $payment_method_titles titles of payment methods
		 */
		public function get_payment_method_titles_by_ids( $payment_method_ids = array() ) {

			$payment_method_titles = array();

			if ( is_array( $payment_method_ids ) && ! empty( $payment_method_ids ) ) {
				$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
				foreach ( $payment_method_ids as $index => $payment_method_id ) {
					$payment_method = ( isset( $available_payment_methods[ $payment_method_id ] ) && ! empty( $available_payment_methods[ $payment_method_id ] ) ) ? $available_payment_methods[ $payment_method_id ] : '';
					if ( ! empty( $payment_method ) && is_a( $payment_method, 'WC_Payment_Gateway' ) ) {
						$payment_method_title = is_callable( array( $payment_method, 'get_title' ) ) ? $payment_method->get_title() : '';
						if ( ! empty( $payment_method_title ) ) {
							$payment_method_titles[ $index ] = $payment_method_title; // Replace payment method id with it's repective title.
						} else {
							$payment_method_titles[ $index ] = $payment_method->id; // In case of empty payment method title replace it with method id.
						}
					}
				}
			}

			return $payment_method_titles;
		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function process_coupon_meta_value_for_import( $meta_value = null, $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && 'wc_sc_payment_method_ids' === $args['meta_key'] ) {

				$meta_value = ( ! empty( $args['postmeta']['wc_sc_payment_method_ids'] ) ) ? explode( '|', wc_clean( wp_unslash( $args['postmeta']['wc_sc_payment_method_ids'] ) ) ) : array();
				if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
					$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
					if ( is_array( $available_payment_methods ) && ! empty( $available_payment_methods ) ) {
						foreach ( $meta_value as $index => $payment_method_title ) {
							foreach ( $available_payment_methods as $payment_method ) {
								$method_title = is_callable( array( $payment_method, 'get_title' ) ) ? $payment_method->get_title() : '';
								if ( $method_title === $payment_method_title && ! empty( $payment_method->id ) ) {
									$meta_value[ $index ] = $payment_method->id; // Replace payment method title with it's respective id.
								}
							}
						}
					}
				}
			}

			return $meta_value;
		}

		/**
		 * Function to copy payment method restriction meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_payment_method_meta( $args = array() ) {

			// Copy meta data to new coupon.
			$this->copy_coupon_meta_data(
				$args,
				array( 'wc_sc_payment_method_ids' )
			);

		}

		/**
		 * Make meta data of payment method ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			if ( 'wc_sc_payment_method_ids' === $meta_key ) {
				return true;
			}
			return $protected;
		}

		/**
		 * Function to add styles & scripts
		 */
		public function styles_and_scripts() {

			$js = "jQuery('form.checkout').on('change', 'input[name=payment_method]', function(){
					jQuery(document.body).trigger('update_checkout');
				})";
			wc_enqueue_js( $js );

		}
	}
}

WC_SC_Coupons_By_Payment_Method::get_instance();
