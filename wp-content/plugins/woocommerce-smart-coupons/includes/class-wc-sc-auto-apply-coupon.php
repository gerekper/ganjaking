<?php
/**
 * Auto apply coupon
 *
 * @author      StoreApps
 * @since       4.6.0
 * @version     1.6.0
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
			add_action( 'woocommerce_cart_emptied', array( $this, 'reset_auto_applied_coupons_session' ) );

			add_action( 'woocommerce_removed_coupon', array( $this, 'wc_sc_removed_coupon' ) );
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
			if ( isset( $_POST['wc_sc_auto_apply_coupon'] ) && isset( $_POST['discount_type'] ) && 'smart_coupon' !==  wc_clean( wp_unslash( $_POST['discount_type'] ) ) ) { // phpcs:ignore
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

			if ( isset( $post['discount_type'] ) && 'smart_coupon' !== $post['discount_type'] ) {
				$data['wc_sc_auto_apply_coupon'] = ( isset( $post['wc_sc_auto_apply_coupon'] ) ) ? $post['wc_sc_auto_apply_coupon'] : '';
			}

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

			$discount_type = isset( $args['discount_type'] ) ? $args['discount_type'] : '';
			if ( 'smart_coupon' !== $discount_type && ! empty( $args['meta_key'] ) && 'wc_sc_auto_apply_coupon' === $args['meta_key'] ) {
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
		 * Get auto applied coupons
		 *
		 * @since 4.27.0
		 * @return array
		 */
		public function get_auto_applied_coupons() {
			$coupons = ( is_object( WC()->session ) && is_callable( array( WC()->session, 'get' ) ) ) ? WC()->session->get( 'wc_sc_auto_applied_coupons' ) : array();
			return apply_filters( 'wc_sc_' . __FUNCTION__, ( ! empty( $coupons ) && is_array( $coupons ) ? $coupons : array() ), array( 'source' => $this ) );
		}

		/**
		 * Add auto applied coupon to WC session
		 *
		 * @since 4.27.0
		 * @param string $coupon_code Coupon Code.
		 */
		public function set_auto_applied_coupon( $coupon_code = '' ) {
			if ( ! empty( $coupon_code ) ) {
				$coupons = $this->get_auto_applied_coupons();
				// Check if auto applied coupons are not empty.
				if ( ! empty( $coupons ) && is_array( $coupons ) ) {
					$coupons[] = $coupon_code;
				} else {
					$coupons = array( $coupon_code );
				}
				if ( is_object( WC()->session ) && is_callable( array( WC()->session, 'set' ) ) ) {
					WC()->session->set( 'wc_sc_auto_applied_coupons', $coupons );
				}
			}
		}

		/**
		 * Remove an auto applied coupon from WC session
		 *
		 * @since 4.31.0
		 * @param string $coupon_code Coupon Code.
		 */
		public function unset_auto_applied_coupon( $coupon_code = '' ) {
			if ( ! empty( $coupon_code ) ) {
				$update  = false;
				$coupons = $this->get_auto_applied_coupons();
				// Check if auto applied coupons are not empty.
				if ( ! empty( $coupons ) && in_array( $coupon_code, $coupons, true ) ) {
					$coupons = array_diff( $coupons, array( $coupon_code ) );
					$update  = true;
				}
				if ( true === $update && is_object( WC()->session ) && is_callable( array( WC()->session, 'set' ) ) ) {
					$coupons = array_values( array_filter( $coupons ) );
					WC()->session->set( 'wc_sc_auto_applied_coupons', $coupons );
				}
			}
		}

		/**
		 * Reset cart session data.
		 *
		 * @since 4.27.0
		 */
		public function reset_auto_applied_coupons_session() {
			if ( is_object( WC()->session ) && is_callable( array( WC()->session, 'set' ) ) ) {
				WC()->session->set( 'wc_sc_auto_applied_coupons', null );
			}
		}

		/**
		 * Runs after a coupon is removed
		 *
		 * @since 4.31.0
		 * @param string $coupon_code The coupon code.
		 * @return void
		 */
		public function wc_sc_removed_coupon( $coupon_code = '' ) {
			$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ); // phpcs:ignore
			if ( ! empty( $coupon_code ) && ! empty( $backtrace ) ) {
				foreach ( $backtrace as $trace ) {
					if ( ! empty( $trace['function'] ) && 'check_cart_coupons' === $trace['function'] && ! empty( $trace['class'] ) && 'WC_Cart' === $trace['class'] ) { // This condition will make sure that the coupon is removed automatically.
						$this->unset_auto_applied_coupon( $coupon_code );
					}
				}
			}
		}

		/**
		 * Check if auto apply coupon allowed in the cart
		 *
		 * @since 4.27.0
		 * @return bool.
		 */
		public function is_allow_auto_apply_coupons() {
			$auto_applied_coupons         = $this->get_auto_applied_coupons();
			$auto_applied_coupons_count   = ! empty( $auto_applied_coupons ) && is_array( $auto_applied_coupons ) ? count( $auto_applied_coupons ) : 0;
			$max_auto_apply_coupons_limit = apply_filters( 'wc_sc_max_auto_apply_coupons_limit', get_option( 'wc_sc_max_auto_apply_coupons_limit', 5 ), array( 'source' => $this ) );

			return apply_filters(
				'wc_sc_' . __FUNCTION__,
				$auto_applied_coupons_count < $max_auto_apply_coupons_limit,
				array(
					'source'               => $this,
					'auto_applied_coupons' => $auto_applied_coupons,
				)
			);
		}

		/**
		 * Check if the auto apply removable
		 *
		 * @since 4.27.0
		 * @param string $coupon_code Coupon Code.
		 * @return bool.
		 */
		public function is_auto_apply_coupon_removable( $coupon_code = '' ) {

			return apply_filters(
				'wc_sc_' . __FUNCTION__,
				get_option( 'wc_sc_auto_apply_coupon_removable', 'yes' ),
				array(
					'source'      => $this,
					'coupon_code' => $coupon_code,
				)
			);
		}

		/**
		 * Check if the coupon is applied through auto apply
		 *
		 * @since 4.27.0
		 * @param string $coupon_code Coupon Code.
		 * @return bool.
		 */
		public function is_coupon_applied_by_auto_apply( $coupon_code = '' ) {
			if ( ! empty( $coupon_code ) ) {
				$applied_coupons = $this->get_auto_applied_coupons();
				if ( ! empty( $applied_coupons ) && is_array( $applied_coupons ) && in_array( $coupon_code, $applied_coupons, true ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Check if coupon is applicable for auto apply
		 *
		 * @since 4.26.0
		 * @param WC_Coupon $coupon WooCommerce coupon object.
		 * @return bool
		 */
		public function is_coupon_valid_for_auto_apply( $coupon = null ) {

			$valid = false;
			if ( is_object( $coupon ) && $coupon instanceof WC_Coupon ) {

				if ( $this->is_wc_gte_30() ) {
					$coupon_code               = is_callable( array( $coupon, 'get_code' ) ) ? $coupon->get_code() : '';
					$discount_type             = is_callable( array( $coupon, 'get_discount_type' ) ) ? $coupon->get_discount_type() : '';
					$is_auto_generate_coupon   = is_callable( array( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'auto_generate_coupon' ) : 'no';
					$is_disable_email_restrict = is_callable( array( $coupon, 'get_meta' ) ) ? $coupon->get_meta( 'sc_disable_email_restriction' ) : 'no';
				} else {
					$coupon_id                 = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$coupon_code               = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					$discount_type             = get_post_meta( $coupon_id, 'discount_type', true );
					$is_auto_generate_coupon   = get_post_meta( $coupon_id, 'auto_generate_coupon', true );
					$is_disable_email_restrict = get_post_meta( $coupon_id, 'sc_disable_email_restriction', true );
				}

				$is_removable    = $this->is_auto_apply_coupon_removable( $coupon_code );
				$is_auto_applied = $this->is_coupon_applied_by_auto_apply( $coupon_code );

				/**
				 * Validate coupon for auto apply if
				 *
				 * Discount type is not smart_coupon.
				 * Auto generate is not enabled.
				 * Disable email restriction is not enabled.
				 * Coupon should not be auto applied OR auto applied coupon should not be removable.
				 * Coupon code is valid.
				 */
				$valid = 'smart_coupon' !== $discount_type
							&& 'yes' !== $is_auto_generate_coupon
							&& 'yes' !== $is_disable_email_restrict
							&& ( ! $is_auto_applied || 'yes' !== $is_removable )
							&& $coupon->is_valid();
			}

			return apply_filters(
				'wc_sc_' . __FUNCTION__,
				$valid,
				array(
					'coupon_obj' => $coupon,
					'source'     => $this,
				)
			);
		}

		/**
		 * Function to apply coupons automatically.
		 *
		 * TODO: IF we need another variable for removed coupons;
		 * There will be 2 session variables: wc_sc_auto_applied_coupons and wc_sc_removed_auto_applied_coupons.
		 * Whenever a coupon will be auto-applied, it'll be stored in wc_sc_auto_applied_coupons.
		 * Whenever a coupon will be removed, it'll be moved from wc_sc_auto_applied_coupons to wc_sc_removed_auto_applied_coupons.
		 * And before applying an auto-apply coupon, it'll be made sure that the coupon doesn't exist in wc_sc_removed_auto_applied_coupons
		 * And sum of counts of both session variable will be considered before auto applying coupons. It will be made sure that the sum of counts in not exceeding option `wc_sc_max_auto_apply_coupons_limit`
		 * Reference: issues/234#note_27085
		 */
		public function auto_apply_coupons() {
			$cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_object( $cart ) && is_callable( array( $cart, 'is_empty' ) ) && ! $cart->is_empty() && $this->is_allow_auto_apply_coupons() ) {
				global $wpdb;
				$user_role = '';
				$email     = '';
				if ( ! is_admin() ) {
					$current_user = wp_get_current_user();
					if ( ! empty( $current_user->ID ) ) {
						$user_role = ( ! empty( $current_user->roles[0] ) ) ? $current_user->roles[0] : '';
						$email     = get_user_meta( $current_user->ID, 'billing_email', true );
						$email     = ( ! empty( $email ) ) ? $email : $current_user->user_email;
					}
				}
				$query = $wpdb->prepare(
					"SELECT DISTINCT p.ID
						FROM {$wpdb->posts} AS p
							JOIN {$wpdb->postmeta} AS pm1
								ON (p.ID = pm1.post_id
									AND p.post_type = %s
									AND p.post_status = %s
									AND pm1.meta_key = %s
									AND pm1.meta_value = %s)
							JOIN {$wpdb->postmeta} AS pm2
								ON (p.ID = pm2.post_id
									AND pm2.meta_key IN ('wc_sc_user_role_ids', 'customer_email')
									AND (pm2.meta_value = ''
											OR pm2.meta_value = 'a:0:{}'",
					'shop_coupon',
					'publish',
					'wc_sc_auto_apply_coupon',
					'yes'
				);
				if ( ! empty( $user_role ) ) {
					$query .= $wpdb->prepare(
						' OR pm2.meta_value LIKE %s',
						'%' . $wpdb->esc_like( $user_role ) . '%'
					);
				}
				if ( ! empty( $email ) ) {
					$query .= $wpdb->prepare(
						' OR pm2.meta_value LIKE %s',
						'%' . $wpdb->esc_like( $email ) . '%'
					);
				}
				$query                .= '))';
				$auto_apply_coupon_ids = $wpdb->get_col( $query ); // phpcs:ignore
				$auto_apply_coupon_ids = array_filter( array_map( 'absint', $auto_apply_coupon_ids ) );
				if ( ! empty( $auto_apply_coupon_ids ) && is_array( $auto_apply_coupon_ids ) ) {
					$valid_coupon_counter         = 0;
					$max_auto_apply_coupons_limit = apply_filters( 'wc_sc_max_auto_apply_coupons_limit', get_option( 'wc_sc_max_auto_apply_coupons_limit', 5 ), array( 'source' => $this ) );
					foreach ( $auto_apply_coupon_ids as $apply_coupon_id ) {
						// Process only five coupons.
						if ( absint( $max_auto_apply_coupons_limit ) === $valid_coupon_counter ) {
							break;
						}
						$coupon = new WC_Coupon( absint( $apply_coupon_id ) );
						if ( $this->is_wc_gte_30() ) {
							$coupon_id   = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
							$coupon_code = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
						} else {
							$coupon_id   = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
						}
						// Check if it is a valid coupon object.
						if ( $apply_coupon_id === $coupon_id && ! empty( $coupon_code ) && $this->is_coupon_valid_for_auto_apply( $coupon ) ) {
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
								$this->set_auto_applied_coupon( $coupon_code );
							}
							$valid_coupon_counter++;
						} // End if to check valid coupon.
					}
				}
			}
		}

	}

}

WC_SC_Auto_Apply_Coupon::get_instance();
