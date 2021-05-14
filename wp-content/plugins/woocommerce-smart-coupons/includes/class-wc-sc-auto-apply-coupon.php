<?php
/**
 * Auto apply coupon
 *
 * @author      StoreApps
 * @since       4.6.0
 * @version     1.3.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Auto_Apply_Coupon' ) ) {

	/**
	 * Class for handling coupons applied via URL
	 */
	class WC_SC_Auto_Apply_Coupon {

		/**
		 * Variable to hold instance of WC_SC_Auto_Apply_Coupon
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Variable to hold coupon notices
		 *
		 * @var $coupon_notices
		 */
		private $coupon_notices = array();

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'woocommerce_coupon_options', array( $this, 'usage_restriction' ), 10, 2 );
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'process_coupon_meta_value_for_import' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );

			// Action to auto apply coupons.
			add_action( 'wp_loaded', array( $this, 'auto_apply_coupons' ) );
		}

		/**
		 * Get single instance of WC_SC_Auto_Apply_Coupon
		 *
		 * @return WC_SC_Auto_Apply_Coupon Singleton object of WC_SC_Auto_Apply_Coupon
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
		 * Display field for auto apply coupon
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {
			?>
			<script type="text/javascript">
				jQuery(function() {
					let show_hide_auto_apply_field = function() {
						let discount_type = jQuery('select#discount_type').val();
						if ('smart_coupon' === discount_type) {
							jQuery('.wc_sc_auto_apply_coupon_field').hide();
						} else {
							jQuery('.wc_sc_auto_apply_coupon_field').show();
						}
					}
					show_hide_auto_apply_field();
					jQuery('select#discount_type').on('change', function() {
						show_hide_auto_apply_field();
					});
				});
			</script>
			<div class="options_group smart-coupons-field">
			<?php
				woocommerce_wp_checkbox(
					array(
						'id'          => 'wc_sc_auto_apply_coupon',
						'label'       => __( 'Auto apply?', 'woocommerce-smart-coupons' ),
						'description' => __( 'When checked, this coupon will be applied automatically, if it is valid. If enabled in more than 5 coupons, only 5 coupons will be applied automatically, rest will be ignored.', 'woocommerce-smart-coupons' ),
					)
				);
			?>
			</div>
			<?php
		}

		/**
		 * Save auto apply coupon in meta
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

			// Get list of ids of coupons to auto apply.
			$auto_apply_coupon_ids = get_option( 'wc_sc_auto_apply_coupon_ids', array() );
			$auto_apply_coupon_ids = array_map( 'absint', $auto_apply_coupon_ids );
			$post_id               = absint( $post_id );
			if ( isset( $_POST['wc_sc_auto_apply_coupon'] ) ) { // phpcs:ignore
				$auto_apply_coupon = wc_clean( wp_unslash( $_POST['wc_sc_auto_apply_coupon'] ) ); // phpcs:ignore
				update_post_meta( $post_id, 'wc_sc_auto_apply_coupon', $auto_apply_coupon );
				// Add coupon id to auto apply coupon list if haven't added already.
				if ( is_array( $auto_apply_coupon_ids ) && ! in_array( $post_id, $auto_apply_coupon_ids, true ) ) {
					$auto_apply_coupon_ids[] = $post_id;
				}
			} else {
				update_post_meta( $post_id, 'wc_sc_auto_apply_coupon', 'no' );
				// Remove coupon id from auto apply coupon list if auto apply is disabled.
				if ( is_array( $auto_apply_coupon_ids ) && in_array( $post_id, $auto_apply_coupon_ids, true ) ) {
					$auto_apply_coupon_ids = array_diff( $auto_apply_coupon_ids, array( $post_id ) );
				}
			}
			update_option( 'wc_sc_auto_apply_coupon_ids', $auto_apply_coupon_ids, 'no' );
		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$headers['wc_sc_auto_apply_coupon'] = __( 'Auto apply?', 'woocommerce-smart-coupons' );

			return $headers;
		}

		/**
		 * Post meta defaults for auto apply coupon meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array $defaults Modified postmeta defaults
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_auto_apply_coupon'] = '';

			return $defaults;
		}

		/**
		 * Add auto apply coupon's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array $data Modified row data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$data['wc_sc_auto_apply_coupon'] = ( isset( $post['wc_sc_auto_apply_coupon'] ) ) ? $post['wc_sc_auto_apply_coupon'] : '';

			return $data;
		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function process_coupon_meta_value_for_import( $meta_value = null, $args = array() ) {

			if ( ! empty( $args['meta_key'] ) && 'wc_sc_auto_apply_coupon' === $args['meta_key'] ) {
				$auto_apply_coupon = $meta_value;
				if ( 'yes' === $auto_apply_coupon ) {
					$auto_apply_coupon_ids = get_option( 'wc_sc_auto_apply_coupon_ids', array() );
					$auto_apply_coupon_ids = array_map( 'absint', $auto_apply_coupon_ids );
					$coupon_id             = ( isset( $args['post']['post_id'] ) ) ? absint( $args['post']['post_id'] ) : 0;
					if ( ! empty( $coupon_id ) && ! in_array( $coupon_id, $auto_apply_coupon_ids, true ) ) {
						$auto_apply_coupon_ids[] = $coupon_id;
						update_option( 'wc_sc_auto_apply_coupon_ids', $auto_apply_coupon_ids, 'no' );
					}
				}
			}

			return $meta_value;
		}

		/**
		 * Make meta data of auto apply coupon meta protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			if ( 'wc_sc_auto_apply_coupon' === $meta_key ) {
				return true;
			}

			return $protected;
		}

		/**
		 * Function to apply coupons automatically.
		 */
		public function auto_apply_coupons() {

			$cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_object( $cart ) && is_callable( array( $cart, 'is_empty' ) ) && ! $cart->is_empty() ) {
				$auto_apply_coupon_ids = get_option( 'wc_sc_auto_apply_coupon_ids', array() );
				if ( ! empty( $auto_apply_coupon_ids ) && is_array( $auto_apply_coupon_ids ) ) {
					$valid_coupon_counter         = 0;
					$max_auto_apply_coupons_limit = apply_filters( 'wc_sc_max_auto_apply_coupons_limit', 5, array( 'source' => $this ) );
					foreach ( $auto_apply_coupon_ids as $apply_coupon_id ) {
						// Process only five coupons.
						if ( absint( $max_auto_apply_coupons_limit ) === $valid_coupon_counter ) {
							break;
						}
						$coupon_status = get_post_status( $apply_coupon_id );
						if ( 'publish' !== $coupon_status ) {
							continue;
						}
						$coupon = new WC_Coupon( absint( $apply_coupon_id ) );
						if ( $this->is_wc_gte_30() ) {
							$coupon_id = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
						} else {
							$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						}
						// Check if it is a valid coupon object.
						if ( $apply_coupon_id === $coupon_id ) {
							if ( $this->is_wc_gte_30() ) {
								$coupon_code   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
								$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
							} else {
								$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
								$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							}
							if ( ! empty( $coupon_code ) && 'smart_coupon' !== $discount_type && $coupon->is_valid() ) {
								$cart_total    = ( $this->is_wc_greater_than( '3.1.2' ) ) ? $cart->get_cart_contents_total() : $cart->cart_contents_total;
								$is_auto_apply = apply_filters(
									'wc_sc_is_auto_apply',
									( $cart_total > 0 ),
									array(
										'source'     => $this,
										'cart_obj'   => $cart,
										'coupon_obj' => $coupon,
										'cart_total' => $cart_total,
									)
								);
								// Check if cart still requires a coupon discount and does not have coupon already applied.
								if ( true === $is_auto_apply && ! $cart->has_discount( $coupon_code ) ) {
									$cart->add_discount( $coupon_code );
								}
								$valid_coupon_counter++;
							}
						}
					}
				}
			}
		}

	}

}

WC_SC_Auto_Apply_Coupon::get_instance();
