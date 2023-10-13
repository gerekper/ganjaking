<?php
/**
 * Class to handle feature Coupons By User Role
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_User_Role' ) ) {

	/**
	 * Class WC_SC_Coupons_By_User_Role
	 */
	class WC_SC_Coupons_By_User_Role {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;


		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'usage_restriction' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'process_meta' ), 10, 2 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 3 );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_after_checkout' ), 99, 2 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'wc_sc_export_coupon_meta', array( $this, 'export_coupon_meta_data' ), 10, 2 );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'process_coupon_meta_value_for_import' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );

			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_user_role_meta' ) );
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
		 * Display field for coupon by user role
		 *
		 * @param integer   $coupon_id The coupon id.
		 * @param WC_Coupon $coupon    The coupon object.
		 */
		public function usage_restriction( $coupon_id = 0, $coupon = null ) {

			$user_role_ids         = array();
			$exclude_user_role_ids = array();
			if ( ! empty( $coupon_id ) ) {
				if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
					$coupon = new WC_Coupon( $coupon_id );
				}

				$is_callable_coupon_get_meta = $this->is_callable( $coupon, 'get_meta' );

				$user_role_ids = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_user_role_ids' ) : get_post_meta( $coupon_id, 'wc_sc_user_role_ids', true );
				if ( empty( $user_role_ids ) || ! is_array( $user_role_ids ) ) {
					$user_role_ids = array();
				}
				$exclude_user_role_ids = ( true === $is_callable_coupon_get_meta ) ? $coupon->get_meta( 'wc_sc_exclude_user_role_ids' ) : get_post_meta( $coupon_id, 'wc_sc_exclude_user_role_ids', true );
				if ( empty( $exclude_user_role_ids ) || ! is_array( $exclude_user_role_ids ) ) {
					$exclude_user_role_ids = array();
				}
			}
			$available_user_roles = $this->get_available_user_roles();
			?>
			<div class="options_group smart-coupons-field">
				<p class="form-field">
					<label for="wc_sc_user_role_ids"><?php echo esc_html__( 'Allowed user roles', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_user_role_ids" name="wc_sc_user_role_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No user roles', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( ! empty( $available_user_roles ) && is_array( $available_user_roles ) ) {
							foreach ( $available_user_roles as $role_id => $role ) {
								$role_name = translate_user_role( $role['name'] );
								echo '<option value="' . esc_attr( $role_id ) . '"' . esc_attr( selected( in_array( $role_id, $user_role_ids, true ), true, false ) ) . '>' . esc_html( $role_name ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					$tooltip_text = esc_html__( 'Role of the users for whom this coupon is valid. Keep empty if you want this coupon to be valid for users with any role.', 'woocommerce-smart-coupons' );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
				<p class="form-field">
					<label for="wc_sc_exclude_user_role_ids"><?php echo esc_html__( 'Exclude user roles', 'woocommerce-smart-coupons' ); ?></label>
					<select id="wc_sc_exclude_user_role_ids" name="wc_sc_exclude_user_role_ids[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No user roles', 'woocommerce-smart-coupons' ); ?>">
						<?php
						if ( ! empty( $available_user_roles ) && is_array( $available_user_roles ) ) {
							foreach ( $available_user_roles as $role_id => $role ) {
								$role_name = translate_user_role( $role['name'] );
								echo '<option value="' . esc_attr( $role_id ) . '"' . esc_attr( selected( in_array( $role_id, $exclude_user_role_ids, true ), true, false ) ) . '>' . esc_html( $role_name ) . '</option>';
							}
						}
						?>
					</select>
					<?php
					$tooltip_text = esc_html__( 'Role of the users for whom this coupon is not valid. Keep empty if you want this coupon to be valid for users with any role.', 'woocommerce-smart-coupons' );
					echo wc_help_tip( $tooltip_text ); // phpcs:ignore
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Save coupon by user role data in meta
		 *
		 * @param  Integer   $post_id The coupon post ID.
		 * @param  WC_Coupon $coupon    The coupon object.
		 */
		public function process_meta( $post_id = 0, $coupon = null ) {
			if ( empty( $post_id ) ) {
				return;
			}

			$coupon = new WC_Coupon( $coupon );

			$user_role_ids = ( isset( $_POST['wc_sc_user_role_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_user_role_ids'] ) ) : array(); // phpcs:ignore
			$exclude_user_role_ids = ( isset( $_POST['wc_sc_exclude_user_role_ids'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_exclude_user_role_ids'] ) ) : array(); // phpcs:ignore

			if ( $this->is_callable( $coupon, 'update_meta_data' ) && $this->is_callable( $coupon, 'save' ) ) {
				$coupon->update_meta_data( 'wc_sc_user_role_ids', $user_role_ids );
				$coupon->update_meta_data( 'wc_sc_exclude_user_role_ids', $exclude_user_role_ids );
				$coupon->save();
			} else {
				update_post_meta( $post_id, 'wc_sc_user_role_ids', $user_role_ids );
				update_post_meta( $post_id, 'wc_sc_exclude_user_role_ids', $exclude_user_role_ids );
			}

		}

		/**
		 * Validate the coupon based on user role
		 *
		 * @param  boolean      $valid  Is valid or not.
		 * @param  WC_Coupon    $coupon The coupon object.
		 * @param  WC_Discounts $discounts The discount object.
		 *
		 * @throws Exception If the coupon is invalid.
		 * @return boolean           Is valid or not
		 */
		public function validate( $valid = false, $coupon = object, $discounts = null ) {

			// If coupon is invalid already, no need for further checks.
			if ( false === $valid ) {
				return $valid;
			}

			$coupon_id = ( $this->is_wc_gte_30() ) ? $coupon->get_id() : $coupon->id;
			if ( ! is_a( $coupon, 'WC_Coupon' ) ) {
				$coupon = new WC_Coupon( $coupon_id );
			}
			if ( $this->is_callable( $coupon, 'get_meta' ) ) {
				$user_role_ids         = $coupon->get_meta( 'wc_sc_user_role_ids' );
				$exclude_user_role_ids = $coupon->get_meta( 'wc_sc_exclude_user_role_ids' );
			} else {
				$user_role_ids         = get_post_meta( $coupon_id, 'wc_sc_user_role_ids', true );
				$exclude_user_role_ids = get_post_meta( $coupon_id, 'wc_sc_exclude_user_role_ids', true );
			}

			$current_user = wp_get_current_user();

			$post_action = ( ! empty( $_POST['action'] ) ) ? wc_clean( wp_unslash( $_POST['action'] ) ) : ''; // phpcs:ignore

			if ( is_admin() && wp_doing_ajax() && 'woocommerce_add_coupon_discount' === $post_action ) { // This condition will allow the addition of coupon from admin side, in the order even if the user role is not matching.
				return true;
			}

			if ( is_array( $user_role_ids ) && ! empty( $user_role_ids ) ) {
				// Check if current user's role is allowed.
				if ( ! array_intersect( $current_user->roles, $user_role_ids ) ) {
					throw new Exception( __( 'This coupon is not valid for you.', 'woocommerce-smart-coupons' ) );
				}
			}

			if ( is_array( $exclude_user_role_ids ) && ! empty( $exclude_user_role_ids ) ) {
				// Check if current user's role is excluded.
				if ( array_intersect( $current_user->roles, $exclude_user_role_ids ) ) {
					throw new Exception( __( 'This coupon is not valid for you.', 'woocommerce-smart-coupons' ) );
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

			$headers['wc_sc_user_role_ids']         = __( 'User Role', 'woocommerce-smart-coupons' );
			$headers['wc_sc_exclude_user_role_ids'] = __( 'Exclude User Role', 'woocommerce-smart-coupons' );

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

			if ( ! empty( $args['meta_key'] ) ) {
				if ( 'wc_sc_user_role_ids' === $args['meta_key'] ) {
					if ( isset( $args['meta_value'] ) && ! empty( $args['meta_value'] ) ) {
						$user_role_ids = maybe_unserialize( stripslashes( $args['meta_value'] ) );
						if ( is_array( $user_role_ids ) && ! empty( $user_role_ids ) ) {
							$user_role_names = $this->get_user_role_names_by_ids( $user_role_ids );
							if ( is_array( $user_role_names ) && ! empty( $user_role_names ) ) {
								$meta_value = implode( '|', wc_clean( wp_unslash( $user_role_names ) ) );  // Replace user role ids with their respective role name.
							}
						}
					}
				} elseif ( 'wc_sc_exclude_user_role_ids' === $args['meta_key'] ) {
					if ( isset( $args['meta_value'] ) && ! empty( $args['meta_value'] ) ) {
						$exclude_user_role_ids = maybe_unserialize( stripslashes( $args['meta_value'] ) );
						if ( is_array( $exclude_user_role_ids ) && ! empty( $exclude_user_role_ids ) ) {
							$exclude_user_role_names = $this->get_user_role_names_by_ids( $exclude_user_role_ids );
							if ( is_array( $exclude_user_role_names ) && ! empty( $exclude_user_role_names ) ) {
								$meta_value = implode( '|', wc_clean( wp_unslash( $exclude_user_role_names ) ) );  // Replace user role ids with their respective role name.
							}
						}
					}
				}
			}

			return $meta_value;

		}

		/**
		 * Post meta defaults for user role ids meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_user_role_ids']         = '';
			$defaults['wc_sc_exclude_user_role_ids'] = '';

			return $defaults;
		}

		/**
		 * Add user role's meta with value in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

			$user_role_names         = '';
			$exclude_user_role_names = '';

			if ( ! empty( $post['wc_sc_user_role_ids'] ) && is_array( $post['wc_sc_user_role_ids'] ) ) {
				$user_role_names = $this->get_user_role_names_by_ids( $post['wc_sc_user_role_ids'] );
				if ( is_array( $user_role_names ) && ! empty( $user_role_names ) ) {
					$user_role_names = implode( '|', wc_clean( wp_unslash( $user_role_names ) ) );
				}
			}

			if ( ! empty( $post['wc_sc_exclude_user_role_ids'] ) && is_array( $post['wc_sc_exclude_user_role_ids'] ) ) {
				$exclude_user_role_names = $this->get_user_role_names_by_ids( $post['wc_sc_exclude_user_role_ids'] );
				if ( is_array( $exclude_user_role_names ) && ! empty( $exclude_user_role_names ) ) {
					$exclude_user_role_names = implode( '|', wc_clean( wp_unslash( $exclude_user_role_names ) ) );
				}
			}

			$data['wc_sc_user_role_ids']         = $user_role_names; // Replace user role ids with their respective role name.
			$data['wc_sc_exclude_user_role_ids'] = $exclude_user_role_names; // Replace user role ids with their respective role name.

			return $data;
		}

		/**
		 * Function to get user role titles for given user role ids
		 *
		 * @param  array $user_role_ids ids of user roles.
		 * @return array $user_role_names titles of user roles
		 */
		public function get_user_role_names_by_ids( $user_role_ids = array() ) {

			$user_role_names = array();

			if ( is_array( $user_role_ids ) && ! empty( $user_role_ids ) ) {
				$available_user_roles = $this->get_available_user_roles();
				foreach ( $user_role_ids as $index => $user_role_id ) {
					$user_role = ( isset( $available_user_roles[ $user_role_id ] ) && ! empty( $available_user_roles[ $user_role_id ] ) ) ? $available_user_roles[ $user_role_id ] : '';
					if ( is_array( $user_role ) && ! empty( $user_role ) ) {
						$user_role_name = ! empty( $user_role['name'] ) ? $user_role['name'] : '';
						if ( ! empty( $user_role_name ) ) {
							$user_role_names[ $index ] = $user_role_name; // Replace user role id with it's repective name.
						} else {
							$user_role_names[ $index ] = $user_role_id; // In case of empty user role name replace it with role id.
						}
					}
				}
			}

			return $user_role_names;
		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function process_coupon_meta_value_for_import( $meta_value = null, $args = array() ) {

			if ( ! empty( $args['meta_key'] ) ) {
				$available_user_roles = $this->get_available_user_roles();
				if ( 'wc_sc_user_role_ids' === $args['meta_key'] ) {
					$meta_value = ( ! empty( $args['postmeta']['wc_sc_user_role_ids'] ) ) ? explode( '|', wc_clean( wp_unslash( $args['postmeta']['wc_sc_user_role_ids'] ) ) ) : array();
					if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
						if ( is_array( $available_user_roles ) && ! empty( $available_user_roles ) ) {
							foreach ( $meta_value as $index => $user_role_name ) {
								foreach ( $available_user_roles as $role_id => $user_role ) {
									$role_name = isset( $user_role['name'] ) ? $user_role['name'] : '';
									if ( $role_name === $user_role_name ) {
										$meta_value[ $index ] = $role_id; // Replace user role title with it's repective id.
									}
								}
							}
						}
					}
				} elseif ( 'wc_sc_exclude_user_role_ids' === $args['meta_key'] ) {
					$meta_value = ( ! empty( $args['postmeta']['wc_sc_exclude_user_role_ids'] ) ) ? explode( '|', wc_clean( wp_unslash( $args['postmeta']['wc_sc_exclude_user_role_ids'] ) ) ) : array();
					if ( is_array( $meta_value ) && ! empty( $meta_value ) ) {
						if ( is_array( $available_user_roles ) && ! empty( $available_user_roles ) ) {
							foreach ( $meta_value as $index => $user_role_name ) {
								foreach ( $available_user_roles as $role_id => $user_role ) {
									$role_name = isset( $user_role['name'] ) ? $user_role['name'] : '';
									if ( $role_name === $user_role_name ) {
										$meta_value[ $index ] = $role_id; // Replace user role title with it's repective id.
									}
								}
							}
						}
					}
				}
			}

			return $meta_value;
		}

		/**
		 * Make meta data of user role ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			if ( in_array( $meta_key, array( 'wc_sc_user_role_ids', 'wc_sc_exclude_user_role_ids' ), true ) ) {
				return true;
			}
			return $protected;
		}


		/**
		 * Function to get available user roles which current user use.
		 *
		 * @return array $available_user_roles Available user roles
		 */
		public function get_available_user_roles() {
			$available_user_roles = array();

			if ( ! function_exists( 'get_editable_roles' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}

			if ( function_exists( 'get_editable_roles' ) ) {
				$available_user_roles = get_editable_roles();
			}

			return $available_user_roles;
		}

		/**
		 * Function to copy user role restriction meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_user_role_meta( $args = array() ) {

			// Copy meta data to new coupon.
			$this->copy_coupon_meta_data(
				$args,
				array( 'wc_sc_user_role_ids', 'wc_sc_exclude_user_role_ids' )
			);

		}

		/**
		 * Validate user role after checkout.
		 *
		 * @param array    $posted Post data.
		 * @param WP_Error $errors Validation errors.
		 * @return void
		 */
		public function validate_after_checkout( $posted = array(), $errors = object ) {

			$current_user_id = get_current_user_id();

			if ( ! empty( $current_user_id ) ) {
				return;
			}

			$billing_email = ! empty( $posted['billing_email'] ) ? $posted['billing_email'] : '';

			if ( empty( $posted['billing_email'] ) ) {
				return;
			}

			$cart = ( function_exists( 'WC' ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_a( $cart, 'WC_Cart' ) ) {
				$is_cart_empty = is_callable( array( $cart, 'is_empty' ) ) && $cart->is_empty();
				if ( false === $is_cart_empty ) {
					$applied_coupons = ( is_callable( array( $cart, 'get_applied_coupons' ) ) ) ? $cart->get_applied_coupons() : array();
					if ( ! empty( $applied_coupons ) ) {
						foreach ( $applied_coupons as $code ) {
							$coupon = new WC_Coupon( $code );
							if ( ! is_object( $coupon ) ) {
								continue;
							}

							if ( is_callable( array( $coupon, 'get_meta' ) ) ) {
								$user_role_ids         = $coupon->get_meta( 'wc_sc_user_role_ids' );
								$exclude_user_role_ids = $coupon->get_meta( 'wc_sc_exclude_user_role_ids' );
							} else {
								if ( is_callable( array( $coupon, 'get_id' ) ) ) {
									$coupon_id = $coupon->get_id();
								} else {
									$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
								}
								if ( empty( $coupon_id ) ) {
									continue;
								}
								$user_role_ids         = get_post_meta( $coupon_id, 'wc_sc_user_role_ids', true );
								$exclude_user_role_ids = get_post_meta( $coupon_id, 'wc_sc_exclude_user_role_ids', true );
							}

							if ( empty( $exclude_user_role_ids ) && empty( $user_role_ids ) ) {
								continue;
							}

							$current_user       = get_user_by( 'email', $billing_email );
							$current_user_roles = ! empty( $current_user->roles ) ? $current_user->roles : array();

							$is_message = is_callable( array( $coupon, 'add_coupon_message' ) );
							$is_remove  = is_callable( array( $cart, 'remove_coupon' ) );

							if ( is_array( $user_role_ids ) && ! empty( $user_role_ids ) ) {
								// Check if current user's role is allowed.
								if ( ! array_intersect( $current_user_roles, $user_role_ids ) ) {
									if ( true === $is_message ) {
										$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED );
									}
									if ( true === $is_remove ) {
										$cart->remove_coupon( $code );
									}
								}
							}

							if ( is_array( $exclude_user_role_ids ) && ! empty( $exclude_user_role_ids ) ) {
								// Check if current user's role is excluded.
								if ( array_intersect( $current_user_roles, $exclude_user_role_ids ) ) {
									if ( true === $is_message ) {
										$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED );
									}
									if ( true === $is_remove ) {
										$cart->remove_coupon( $code );
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

WC_SC_Coupons_By_User_Role::get_instance();
