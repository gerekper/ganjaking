<?php
/**
 * Class to handle feature Coupons By Shipping Method
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Shipping_Method' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Shipping_Method
	 */
	class WC_SC_Coupons_By_Shipping_Method {

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
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 2 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'wc_sc_export_coupon_meta', array( $this, 'export_coupon_meta_data' ), 10, 2 );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'process_coupon_meta_value_for_import' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_shipping_method_meta' ) );
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
		 * Display field for coupon by shipping method
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {

			$shipping_method_ids = array();
			if ( ! empty( $coupon_id ) ) {
				$shipping_method_ids = get_post_meta( $coupon_id, 'wc_sc_shipping_method_ids', true );
				if ( empty( $shipping_method_ids ) || ! is_array( $shipping_method_ids ) ) {
					$shipping_method_ids = array();
				}
			}
			$available_shipping_methods = WC()->shipping->get_shipping_methods();
			?>
			<div class="options_group smart-coupons-field">
				<p class="form-field">
					<label for="wc_sc_shipping_method_ids"><?php echo esc_html__( 'Shipping methods', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_shipping_method_ids" name="wc_sc_shipping_method_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No shipping methods', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( is_array( $available_shipping_methods ) && ! empty( $available_shipping_methods ) ) {
							foreach ( $available_shipping_methods as $shipping_method ) {
								echo '<option value="' . esc_attr( $shipping_method->id ) . '"' . esc_attr( selected( in_array( $shipping_method->id, $shipping_method_ids, true ), true, false ) ) . '>' . esc_html( $shipping_method->get_method_title() ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					$tooltip_text = esc_html__( 'Shipping methods that must be selected during checkout for this coupon to be valid.', 'woocommerce-smart-coupons' );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Save coupon by shipping method data in meta
		 *
		 * @param  Integer $post_id The coupon post ID.
		 * @param  WP_Post $post    The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$shipping_method_ids = ( isset( $_POST['wc_sc_shipping_method_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_shipping_method_ids'] ) ) : array(); // phpcs:ignore

			update_post_meta( $post_id, 'wc_sc_shipping_method_ids', $shipping_method_ids );
		}

		/**
		 * Validate the coupon based on shipping method
		 *
		 * @param  boolean   $valid  Is valid or not.
		 * @param  WC_Coupon $coupon The coupon object.
		 *
		 * @throws Exception If the coupon is invalid.
		 * @return boolean           Is valid or not
		 */
		public function validate( $valid = false, $coupon = object ) {

			// If coupon is already invalid, no need for further checks.
			if ( false === $valid ) {
				return $valid;
			}

			$coupon_id           = ( $this->is_wc_gte_30() ) ? $coupon->get_id() : $coupon->id;
			$shipping_method_ids = get_post_meta( $coupon_id, 'wc_sc_shipping_method_ids', true );

			if ( is_array( $shipping_method_ids ) && ! empty( $shipping_method_ids ) ) {
				$chosen_shipping_method_data   = WC()->session->__isset( 'chosen_shipping_methods' ) ? WC()->session->get( 'chosen_shipping_methods' ) : '';
				$chosen_shipping_method_string = is_array( $chosen_shipping_method_data ) && ! empty( $chosen_shipping_method_data ) ? $chosen_shipping_method_data[0] : '';
				if ( ! empty( $chosen_shipping_method_string ) ) {
					$chosen_shipping_method_string = explode( ':', $chosen_shipping_method_string );
					$chosen_shipping_method_id     = $chosen_shipping_method_string[0];
					if ( ! in_array( $chosen_shipping_method_id, $shipping_method_ids, true ) ) {
						$wc_shipping_packages = ( is_callable( array( WC()->shipping, 'get_packages' ) ) ) ? WC()->shipping->get_packages() : null;
						if ( empty( $wc_shipping_packages ) && is_callable( array( WC()->cart, 'calculate_shipping' ) ) ) {
							WC()->cart->calculate_shipping();
						}
						$chosen_shipping_method_rate_id = is_array( $chosen_shipping_method_data ) && ! empty( $chosen_shipping_method_data ) ? $chosen_shipping_method_data[0] : '';
						$shipping_method_id             = '';
						$available_shipping_packages    = ( is_callable( array( WC()->shipping, 'get_packages' ) ) ) ? WC()->shipping->get_packages() : '';

						if ( ! empty( $available_shipping_packages ) ) {
							foreach ( $available_shipping_packages as $key => $package ) {
								if ( ! empty( $shipping_method_id ) ) {
									break;
								}
								// Loop through Shipping rates.
								if ( isset( $package['rates'] ) && ! empty( $package['rates'] ) ) {
									foreach ( $package['rates'] as $rate_id => $rate ) {
										if ( $chosen_shipping_method_rate_id === $rate_id ) {
											$shipping_method_id = ( is_callable( array( $rate, 'get_method_id' ) ) ) ? $rate->get_method_id() : '';
											break;
										}
									}
								}
							}
							if ( ! in_array( $shipping_method_id, $shipping_method_ids, true ) ) {
								if ( ! apply_filters( 'wc_sc_coupon_validate_shipping_method', false, $chosen_shipping_method_id, $shipping_method_ids ) ) {
									throw new Exception( __( 'This coupon is not valid for selected shipping method.', 'woocommerce-smart-coupons' ) );
								}
							}
						} else {
							if ( ! apply_filters( 'wc_sc_coupon_validate_shipping_method', false, $chosen_shipping_method_id, $shipping_method_ids ) ) {
								throw new Exception( __( 'This coupon is not valid for selected shipping method.', 'woocommerce-smart-coupons' ) );
							}
						}
					}
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

			$headers['wc_sc_shipping_method_ids'] = __( 'Shipping methods', 'woocommerce-smart-coupons' );

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

			if ( ! empty( $args['meta_key'] ) && 'wc_sc_shipping_method_ids' === $args['meta_key'] ) {
				if ( isset( $args['meta_value'] ) && ! empty( $args['meta_value'] ) ) {
					$shipping_method_ids = maybe_unserialize( stripslashes( $args['meta_value'] ) );
					if ( is_array( $shipping_method_ids ) && ! empty( $shipping_method_ids ) ) {
						$shipping_method_titles = $this->get_shipping_method_titles_by_ids( $shipping_method_ids );
						if ( is_array( $shipping_method_titles ) && ! empty( $shipping_method_titles ) ) {
							$meta_value = implode( '|', wc_clean( wp_unslash( $shipping_method_titles ) ) );  // Replace shipping method ids with their respective method titles.
						}
					}
				}
			}

			return $meta_value;

		}

		/**
		 * Post meta defaults for shipping method ids meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array $defaults Modified postmeta defaults
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_shipping_method_ids'] = '';

			return $defaults;
		}

		/**
		 * Add shipping method's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array $data Modified row data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$shipping_method_titles = '';

			if ( ! empty( $post['wc_sc_shipping_method_ids'] ) && is_array( $post['wc_sc_shipping_method_ids'] ) ) {
				$shipping_method_titles = $this->get_shipping_method_titles_by_ids( $post['wc_sc_shipping_method_ids'] );
				if ( is_array( $shipping_method_titles ) && ! empty( $shipping_method_titles ) ) {
					$shipping_method_titles = implode( '|', wc_clean( wp_unslash( $shipping_method_titles ) ) );
				}
			}

			$data['wc_sc_shipping_method_ids'] = $shipping_method_titles; // Replace shipping method ids with their respective method titles.

			return $data;
		}

		/**
		 * Function to get shipping method titles for given shipping method ids
		 *
		 * @param  array $shipping_method_ids ids of shipping methods.
		 * @return array $shipping_method_titles titles of shipping methods
		 */
		public function get_shipping_method_titles_by_ids( $shipping_method_ids = array() ) {

			$shipping_method_titles = array();

			if ( is_array( $shipping_method_ids ) && ! empty( $shipping_method_ids ) ) {
				$available_shipping_methods = WC()->shipping->load_shipping_methods();
				foreach ( $shipping_method_ids as $index => $shipping_method_id ) {
					$shipping_method = ( isset( $available_shipping_methods[ $shipping_method_id ] ) && ! empty( $available_shipping_methods[ $shipping_method_id ] ) ) ? $available_shipping_methods[ $shipping_method_id ] : '';
					if ( ! empty( $shipping_method ) && is_a( $shipping_method, 'WC_Shipping_Method' ) ) {
						$shipping_method_title = is_callable( array( $shipping_method, 'get_method_title' ) ) ? $shipping_method->get_method_title() : '';
						if ( ! empty( $shipping_method_title ) ) {
							$shipping_method_titles[ $index ] = $shipping_method_title; // Replace shipping method id with it's repective title.
						} else {
							$shipping_method_titles[ $index ] = $shipping_method->id; // In case of empty shipping method title replace it with method id.
						}
					}
				}
			}

			return $shipping_method_titles;
		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function process_coupon_meta_value_for_import( $meta_value = null, $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && 'wc_sc_shipping_method_ids' === $args['meta_key'] ) {

				$meta_value = ( ! empty( $args['postmeta']['wc_sc_shipping_method_ids'] ) ) ? explode( '|', wc_clean( wp_unslash( $args['postmeta']['wc_sc_shipping_method_ids'] ) ) ) : array();
				if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
					$available_shipping_methods = WC()->shipping->load_shipping_methods();
					if ( is_array( $available_shipping_methods ) && ! empty( $available_shipping_methods ) ) {
						foreach ( $meta_value as $index => $shipping_method_title ) {
							foreach ( $available_shipping_methods as $shipping_method ) {
								$method_title = is_callable( array( $shipping_method, 'get_method_title' ) ) ? $shipping_method->get_method_title() : '';
								if ( $method_title === $shipping_method_title && ! empty( $shipping_method->id ) ) {
									$meta_value[ $index ] = $shipping_method->id; // Replace shipping method title with it's repective id.
								}
							}
						}
					}
				}
			}

			return $meta_value;
		}

		/**
		 * Function to copy shipping method restriction meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_shipping_method_meta( $args = array() ) {

			// Copy meta data to new coupon.
			$this->copy_coupon_meta_data(
				$args,
				array( 'wc_sc_shipping_method_ids' )
			);

		}

		/**
		 * Make meta data of shipping method ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			if ( 'wc_sc_shipping_method_ids' === $meta_key ) {
				return true;
			}
			return $protected;
		}
	}
}

WC_SC_Coupons_By_Shipping_Method::get_instance();
