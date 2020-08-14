<?php
/**
 * Affiliate Handler class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Affiliate_Handler' ) ) {
	/**
	 * WooCommerce Affiliate Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Affiliate_Handler {

		/**
		 * Role name for affiliates
		 * @since 1.2.0
		 */
		protected static $_role_name = 'yith_affiliate';

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Affiliate_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Affiliate_Handler
		 * @since 1.0.0
		 */
		public function __construct() {
			// register affiliate panel
			add_action( 'yith_wcaf_affiliate_panel', array( $this, 'print_affiliate_panel' ) );
			add_action( 'admin_init', array( $this, 'add_affiliate' ) );
			add_action( 'admin_action_yith_wcaf_change_status', array( $this, 'handle_switch_status_panel_actions' ) );
			add_action( 'admin_action_yith_wcaf_process_dangling_commissions', array(
				$this,
				'handle_process_dangling_commissions_panel_action'
			) );
			add_action( 'current_screen', array( $this, 'add_screen_option' ) );
			add_filter( 'manage_yith-plugins_page_yith_wcaf_panel_columns', array( $this, 'add_screen_columns' ) );
			add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'load-yith-plugins_page_yith_wcaf_panel', array( $this, 'process_bulk_actions' ) );

			// handle affiliate registration
			add_filter( 'woocommerce_process_registration_errors', array( $this, 'check_affiliate' ) );
			add_action( 'woocommerce_created_customer', array( $this, 'register_affiliate' ), 5, 1 );
			add_action( 'woocommerce_register_form_start', array( $this, 'print_affiliate_fields' ), 10 );
			add_action( 'woocommerce_register_form', array( $this, 'print_affiliate_fields' ), 10 );
			add_action( 'wp_loaded', array( $this, 'become_an_affiliate' ) );
			add_action( 'yith_wcaf_process_become_an_affiliate_errors', array( $this, 'check_affiliate' ) );

			// profile screen update methods
			add_action( 'show_user_profile', array( $this, 'render_affiliate_extra_fields' ), 20 );
			add_action( 'edit_user_profile', array( $this, 'render_affiliate_extra_fields' ), 20 );
			add_action( 'personal_options_update', array( $this, 'save_affiliate_extra_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_affiliate_extra_fields' ) );

			// affiliate dashboard actions
			add_action( 'yith_wcaf_before_dashboard_section', array( $this, 'print_ban_message' ) );
			add_action( 'yith_wcaf_before_dashboard_summary', array( $this, 'print_ban_message' ) );

			// handle notifications
			add_action( 'yith_wcaf_new_affiliate', array( WC(), 'mailer' ), 5 );
			add_action( 'yith_wcaf_affiliate_status_updated', array( WC(), 'mailer' ), 5 );
			add_action( 'yith_wcaf_affiliate_banned', array( WC(), 'mailer' ), 5 );

			// handle ajax actions
			add_action( 'wp_ajax_json_search_affiliates', array( $this, 'get_affiliates_via_ajax' ) );
		}

		/* === AFFILIATE HANDLING METHODS === */

		/**
		 * Add an item to affiliate table
		 *
		 * @param $affiliate_args mixed<br/>
		 *                        [<br/>
		 *                        'token' => '',        // affiliate token<br/>
		 *                        'user_id' => 0,       // affiliate related user id<br/>
		 *                        'enabled' => 1,       // affiliate enabled (0/1/-1)<br/>
		 *                        'rate' => 'NULL',     // affiliate rate (float; leave empty if there is no specific rate for this affiliate)<br/>
		 *                        'earnings' => 0,      // affiliate earnings (float)<br/>
		 *                        'refunds' => 0,       // affiliate refunds (float)<br/>
		 *                        'paid' => 0,          // affiliate paid (float)<br/>
		 *                        'click' => 0,         // affiliate clicks (int)<br/>
		 *                        'conversion' => 0,    // affiliate conversions (int)<br/>
		 *                        'banned' => 0,        // affiliates banned (bool)<br/>
		 *                        'payment_email' => '' // affiliate payment email (string)<br/>
		 *                        ]
		 *
		 * @return int Inserted row ID
		 * @since 1.0.0
		 */
		public function add( $affiliate_args ) {
			global $wpdb;

			$defaults = array(
				'token'         => '',
				'user_id'       => 0,
				'enabled'       => 1,
				'rate'          => 'NULL',
				'earnings'      => 0,
				'refunds'       => 0,
				'paid'          => 0,
				'click'         => 0,
				'conversion'    => 0,
				'banned'        => 0,
				'payment_email' => ''
			);

			$args = wp_parse_args( $affiliate_args, $defaults );

			if ( $args['rate'] == 'NULL' ) {
				unset( $args['rate'] );
			}

			$res = $wpdb->insert( $wpdb->yith_affiliates, $args );

			if ( ! $res ) {
				return false;
			}

			$affiliate_id = $wpdb->insert_id;

			$this->add_role( $affiliate_id );

			$process_dangling_commissions = get_option( 'yith_wcaf_referral_registration_process_dangling_commissions', 'no' );

			if ( 'yes' == $process_dangling_commissions ) {
				$this->process_dangling_commissions( $affiliate_id, $args['token'] );
			}

			return $affiliate_id;
		}

		/**
		 * Update an item of affiliate table
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $args         mixed<br/>
		 *                      [<br/>
		 *                      'token' => '',        // affiliate token<br/>
		 *                      'user_id' => 0,       // affiliate related user id<br/>
		 *                      'enabled' => 1,       // affiliate enabled (0/1/-1)<br/>
		 *                      'rate' => 'NULL',     // affiliate rate (float; leave empty if there is no specific rate for this affiliate)<br/>
		 *                      'earnings' => 0,      // affiliate earnings (float)<br/>
		 *                      'refunds' => 0,       // affiliate refunds (float)<br/>
		 *                      'paid' => 0,          // affiliate paid (float)<br/>
		 *                      'click' => 0,         // affiliate clicks (int)<br/>
		 *                      'conversion' => 0,    // affiliate conversions (int)<br/>
		 *                      'banned' => 0,        // affiliates banned (bool)<br/>
		 *                      'payment_email' => '' // affiliate payment email (string)<br/>
		 *                      ]
		 *
		 * @return int|bool False on failure; number of updated rows on success (usually 1)
		 * @since 1.0.0
		 */
		public function update( $affiliate_id, $args ) {
			global $wpdb;

			// retrieve old instance of affiliate object, to check what is changing
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			// executes update
			$result = $wpdb->update( $wpdb->yith_affiliates, $args, array( 'ID' => $affiliate_id ) );

			if ( isset( $args['enabled'] ) && $args['enabled'] != $affiliate['enabled'] ) {
				/**
				 * Triggers yith_wcaf_affiliate_status_updated action whenever affiliate's status changes
				 * Used to send email and perform other kind of operations
				 *
				 * See also: yith_wcaf_affiliate_enabled / yith_wcaf_affiliate_disabled
				 *
				 * @since 1.1.1
				 */
				do_action( 'yith_wcaf_affiliate_status_updated', $affiliate_id, $args['enabled'], $affiliate['enabled'] );
			}

			if ( isset( $args['payment_email'] ) && $args['payment_email'] != $affiliate['payment_email'] ) {
				/**
				 * Triggers yith_wcaf_affiliate_payment_email_updated action whenever affiliate's payment email changes
				 * Used to complete payment data, for payments without payment_email
				 *
				 * @since 1.1.1
				 */
				do_action( 'yith_wcaf_affiliate_payment_email_updated', $affiliate_id, $args['payment_email'], $affiliate['payment_email'] );
			}

			return $result;
		}

		/**
		 * Delete an item from affiliates table
		 *
		 * @param $affiliate_id int Affiliate id
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function delete( $affiliate_id ) {
			global $wpdb;

			$this->remove_role( $affiliate_id );

			return $wpdb->delete( $wpdb->yith_affiliates, array( 'ID' => $affiliate_id ) );
		}

		/**
		 * Register a user as an enabled affiliate (admin panel action handling)
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_affiliate() {
			if ( ! isset( $_REQUEST['yith_new_affiliate'] ) ) {
				return;
			}

			$user_id = isset( $_REQUEST['yith_new_affiliate'] ) ? intval( $_REQUEST['yith_new_affiliate'] ) : 0;

			if ( empty( $user_id ) ) {
				return;
			}

			$return_url = esc_url_raw( remove_query_arg( 'yith_new_affiliate' ) );

			if ( $this->is_user_affiliate( $user_id ) ) {
				$return_url = add_query_arg( 'affiliate_added', 0, $return_url );

				wp_redirect( $return_url );
				die;
			}

			$token = $this->get_default_user_token( $user_id );
			$res   = $this->add( array( 'user_id' => $user_id, 'token' => $token ) );

			$return_url = add_query_arg( 'affiliate_added', (bool) $res, $return_url );

			wp_redirect( $return_url );
			die;
		}

		/**
		 * Add role to enabled affiliates
		 *
		 * @return void
		 * @since 1.2.0
		 */
		public function add_role( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );
			$user      = get_user_by( 'id', $affiliate['user_id'] );

			if ( ! $user || is_wp_error( $user ) || ! apply_filters( 'yith_wcaf_add_affiliate_role', true, $affiliate ) ) {
				return;
			}

			$user->add_role( self::$_role_name );
		}

		/**
		 * Remove role from enabled affiliates
		 *
		 * @return void
		 * @since 1.2.0
		 */
		public function remove_role( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );
			$user      = get_user_by( 'id', $affiliate['user_id'] );

			if ( ! $user || is_wp_error( $user ) ) {
				return;
			}

			$user->remove_role( self::$_role_name );
		}

		/**
		 * Process dangling commissions, assigning all old commissions registered for current affiliate token
		 * to affiliate_id
		 *
		 * Note: this will only work if commissions exists in commission table, and affiliate do not exists anymore
		 *
		 * @param $current_affiliate_id int Current ID of the affiliate
		 * @param $affiliate_token      string Affiliate token (should be the same for the same user)
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function process_dangling_commissions( $current_affiliate_id, $affiliate_token ) {
			global $wpdb;

			$query      = "SELECT im.meta_value 
			              FROM {$wpdb->prefix}woocommerce_order_itemmeta as im 
			              LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS i USING( order_item_id )
			              LEFT JOIN {$wpdb->postmeta} AS pm ON i.order_id = pm.post_id
			              WHERE im.meta_key = %s AND pm.meta_key = %s AND pm.meta_value = %s";
			$query_args = array(
				'_yith_wcaf_commission_id',
				'_yith_wcaf_referral',
				$affiliate_token
			);

			$dangling_commissions = $wpdb->get_col( $wpdb->prepare( $query, $query_args ) );

			if ( ! empty( $dangling_commissions ) ) {
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
					'include' => $dangling_commissions
				) );

				if ( ! empty( $commissions ) ) {
					foreach ( $commissions as $commission ) {
						$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $commission['affiliate_id'] );

						if ( ! empty( $affiliate ) ) {
							continue;
						}

						/**
						 * @since 1.2.4
						 */
						YITH_WCAF_Commission_Handler()->change_commission_affiliate(
							$commission['ID'],
							$current_affiliate_id,
							sprintf(
								__( 'Commission assigned to affiliate #%d (previously was assigned to #%d)', 'yith-woocommerce-affiliate' ),
								$current_affiliate_id,
								$commission['affiliate_id']
							)
						);
					}
				}
			}
		}

		/* === FORM HANDLER METHODS === */

		/**
		 * Flag a registered user as an affiliates
		 *
		 * @return void
		 * @since 1.0.9
		 */
		public function become_an_affiliate() {
			if ( isset( $_REQUEST['become_an_affiliate'] ) && $_REQUEST['become_an_affiliate'] == 1 ) {
				if ( is_user_logged_in() ) {
					$customer_id = get_current_user_id();
					$affiliates  = $this->get_affiliates( array( 'user_id' => $customer_id ) );
					$affiliate   = isset( $affiliates[0] ) ? $affiliates[0] : false;

					$show_additional_fields = get_option( 'yith_wcaf_referral_show_fields_on_become_an_affiliate', 'no' );

					if ( ! $affiliate ) {
						$validation_error = new WP_Error();
						$validation_error = apply_filters( 'yith_wcaf_process_become_an_affiliate_errors', $validation_error, $customer_id );

						if ( $validation_error->get_error_code() ) {
							wc_add_notice( $validation_error->get_error_message(), 'error' );
						} else {
							$id = $this->add( array(
								'user_id' => $customer_id,
								'enabled' => false,
								'token'   => $this->get_default_user_token( $customer_id )
							) );

							if ( $id ) {
								// set up payment email address
								if ( 'yes' == $show_additional_fields ) {
									$payment_email = apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_POST['payment_email'] ), $_REQUEST['payment_email'] );
									YITH_WCAF_Affiliate_Handler()->update( $id, array( 'payment_email' => $payment_email ) );
								}

								wc_add_notice( __( 'Your request has been processed correctly', 'yith-woocommerce-affiliates' ) );

								// trigger new affiliate action
								do_action( 'yith_wcaf_new_affiliate', $id );
							} else {
								wc_add_notice( __( 'An error occurred while trying to create the affiliate; try later.', 'yith-woocommerce-affiliates' ), 'error' );
							}
						}
					} else {
						wc_add_notice( __( 'You have already affiliated with us!', 'yith-woocommerce-affiliates' ), 'error' );
					}
				}

				wp_redirect( esc_url( apply_filters( 'yith_wcaf_become_an_affiliate_redirection', remove_query_arg( 'become_an_affiliate' ) ) ) );
				die();
			}
		}

		/**
		 * Register a user as an affiliate (register form action handling)
		 *
		 * @param $customer_id int Customer ID
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_affiliate( $customer_id ) {
			// retrieve options
			$enabled_form = get_option( 'yith_wcaf_referral_registration_form_options' );

			// retrieve post data
			$first_name    = ! empty( $_POST['first_name'] ) ? sanitize_text_field( trim( $_POST['first_name'] ) ) : false;
			$last_name     = ! empty( $_POST['last_name'] ) ? sanitize_text_field( trim( $_POST['last_name'] ) ) : false;
			$payment_email = ! empty( $_POST['payment_email'] ) ? apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_POST['payment_email'] ), $_POST['payment_email'] ) : false;

			if (
				( ! empty( $_POST['register_affiliate'] ) && isset( $_POST['register_affiliate'] ) && wp_verify_nonce( $_POST['register_affiliate'], 'yith-wcaf-register-affiliate' ) ) ||
				( ! empty( $_POST['register'] ) && isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register' ) && $enabled_form == 'any' )
			) {
				$id = $this->add( array(
					'user_id'       => $customer_id,
					'enabled'       => false,
					'payment_email' => $payment_email,
					'token'         => $this->get_default_user_token( $customer_id )
				) );

				if ( $first_name || $last_name ) {
					wp_update_user( array_merge(
						array( 'ID' => $customer_id ),
						( $first_name ) ? array( 'first_name' => $first_name ) : array(),
						( $last_name ) ? array( 'last_name' => $last_name ) : array()
					) );
				}

				// trigger new affiliate action
				do_action( 'yith_wcaf_new_affiliate', $id );
			}
		}

		/**
		 * Check affiliate additional data
		 *
		 * @param $validation_error \WP_Error Registration errors object
		 *
		 * @return \WP_Error
		 * @since 1.0.0
		 */
		public function check_affiliate( $validation_error ) {
			$enabled_form                       = get_option( 'yith_wcaf_referral_registration_form_options' );
			$show_name_field                    = get_option( 'yith_wcaf_referral_registration_show_name_field', 'no' );
			$show_surname_field                 = get_option( 'yith_wcaf_referral_registration_show_surname_field', 'no' );
			$show_fields_on_become_an_affiliate = get_option( 'yith_wcaf_referral_show_fields_on_become_an_affiliate', 'no' );
			$val_error                          = array();
			if (
				( ! empty( $_POST['register_affiliate'] ) && wp_verify_nonce( $_POST['register_affiliate'], 'yith-wcaf-register-affiliate' ) ) ||
				( ! empty( $_POST['register'] ) && isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register' ) && $enabled_form == 'any' ) ||
				( isset( $_GET['become_an_affiliate'] ) && 'yes' == $show_fields_on_become_an_affiliate )
			) {
				if ( ( empty( $_POST['payment_email'] ) || ! apply_filters( 'yith_wcaf_is_payment_email', is_email( $_POST['payment_email'] ), $_POST['payment_email'] ) ) && apply_filters( 'yith_wcaf_payment_email_required', true ) ) {
					$val_error['no_payment_email'] = __( 'Please, submit a valid email address where we can send PayPal payments', 'yith-woocommerce-affiliates' );
				}

				if (
					$show_name_field && (
						( apply_filters( 'yith_wcaf_first_name_required', false ) && empty( $_POST['first_name'] ) ) ||
						( ! empty( $_POST['first_name'] ) && ! sanitize_text_field( $_POST['first_name'] ) )
					)
				) {
					$val_error['invalid_name'] = __( 'Please, enter a valid first name', 'yith-woocommerce-affiliates' );
				}

				if (
					$show_surname_field && (
						( apply_filters( 'yith_wcaf_last_name_required', false ) && empty( $_POST['last_name'] ) ) ||
						( ! empty( $_POST['last_name'] ) && ! sanitize_text_field( $_POST['last_name'] ) )
					)
				) {
					$val_error['invalid_surname'] = __( 'Please, enter a valid last name', 'yith-woocommerce-affiliates' );
				}
			}

			if ( ! empty( $val_error ) ) {
				$val_error = apply_filters( 'yith_wcaf_check_affiliate_val_error', $val_error );
				foreach ( $val_error as $error_key => $error_message ) {
					$validation_error->add( $error_key, $error_message );
				}
			}

			return $validation_error;
		}

		/**
		 * Print affiliates additional fields on my-account screen
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_affiliate_fields() {
			global $wp_current_filter;
			$enabled_form = get_option( 'yith_wcaf_referral_registration_form_options' );

			if ( YITH_WCAF_Shortcode::$is_affiliate_dashboard || YITH_WCAF_Shortcode::$is_registration_form ) {
				return;
			}

			if ( 'any' == $enabled_form && in_array( 'woocommerce_register_form_start', $wp_current_filter ) ):
				$this->print_top_fields();
			endif;

			if ( 'any' == $enabled_form && in_array( 'woocommerce_register_form', $wp_current_filter ) ):
				$this->print_bottom_fields();
			endif;
		}

		/**
		 * Prints top part of Affiliate registration fields
		 *
		 * @return void
		 * @since 1.2.5
		 */
		public function print_top_fields( $show_required = true ) {
			$show_name_field    = get_option( 'yith_wcaf_referral_registration_show_name_field' );
			$show_surname_field = get_option( 'yith_wcaf_referral_registration_show_surname_field' );

			if ( 'yes' == $show_name_field ):
				$label = apply_filters( 'yith_wcaf_first_name_label', __( 'First name', 'yith-woocommerce-affiliates' ) );
				$required       = $show_required ? apply_filters( 'yith_wcaf_first_name_required', false ) : false;
				?>
				<p class="form-row form-row-wide">
					<label for="first_name"><?php echo $label; ?><?php echo $required ? ' <span class="required">*</span>' : '' ?></label>
					<input type="text" class="input-text" name="first_name" id="first_name" value="<?php if ( ! empty( $_POST['first_name'] ) ) {
						echo esc_attr( $_POST['first_name'] );
					} ?>"/>
				</p>
			<?php
			endif;

			if ( 'yes' == $show_surname_field ):
				$label = apply_filters( 'yith_wcaf_last_name_label', __( 'Last name', 'yith-woocommerce-affiliates' ) );
				$required       = $show_required ? apply_filters( 'yith_wcaf_last_name_required', false ) : false;
				?>
				<p class="form-row form-row-wide">
					<label for="last_name"><?php echo $label; ?><?php echo $required ? ' <span class="required">*</span>' : '' ?></label>
					<input type="text" class="input-text" name="last_name" id="last_name" value="<?php if ( ! empty( $_POST['last_name'] ) ) {
						echo esc_attr( $_POST['last_name'] );
					} ?>"/>
				</p>
			<?php
			endif;
		}

		/**
		 * Print lower part of Affiliate registration form
		 *
		 * @return void
		 * @since 1.2.5
		 */
		public function print_bottom_fields() {
			if ( apply_filters( 'yith_wcaf_payment_email_required', true ) ):
				?>
				<p class="form-row form-row-wide">
					<label for="payment_email"><?php _e( 'Payment email address', 'yith-woocommerce-affiliates' ); ?>
						<span class="required">*</span></label>
					<input type="email" class="input-text" name="payment_email" id="payment_email" value="<?php if ( ! empty( $_POST['payment_email'] ) ) {
						echo esc_attr( $_POST['payment_email'] );
					} ?>"/>
				</p>
			<?php
			endif;
		}

		/* === HELPER METHODS === */

		/**
		 * Return a human friendly version of a affiliate status
		 *
		 * @param $status int Status to convert to human friendly form
		 *
		 * @return string Human friendly status
		 * @since 1.3.0
		 */
		public function get_readable_status( $status ) {
			switch ( $status ) {
				case 0:
					$label = __( 'New request', 'yith-woocommerce-affiliates' );
					break;
				case 1:
					$label = __( 'Accepted and Enabled', 'yith-woocommerce-affiliates' );
					break;
				case - 1:
					$label = __( 'Rejected', 'yith-woocommerce-affiliates' );
					break;
			}

			return apply_filters( "yith_wcaf_affiliate_status_name", $label, $status );
		}

		/**
		 * Return current ref variable name
		 *
		 * @return string Ref variable name
		 * @since 1.0.0
		 */
		public function get_ref_name() {
			return get_option( 'yith_wcaf_referral_var_name', 'ref' );
		}

		/**
		 * Return number of affiliates matching filtering criteria
		 *
		 * @param $args mixed Filtering criteria<br/>
		 *              [<br/>
		 *              'user_id' => false,              // affiliate related user id (int)<br/>
		 *              'user_login' => false,           // affiliate related user login, or part of it (string)<br/>
		 *              'user_email' => false,           // affiliate related user EMAIL, or part of it (string)<br/>
		 *              'payment_email' => false,        // affiliate payment email, or part of it (string)<br/>
		 *              'rate' => false,                 // affiliate rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'earnings' => false,             // affiliate earnings range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'paid' => false,                 // affiliate paid range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'balance' => false,              // affiliate balance range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'clicks' => false,               // affiliate clicks range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'conversions' => false,          // affiliate conversions range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'conv_rate' => false,            // affiliate conversion rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'status' => false,               // affiliate status (enabled/disabled)<br/>
		 *              'banned' => false,               // affiliates ban status (banned/unbanned)<br/>
		 *              's' => false                     // search string (string)<br/>
		 *              ]
		 *
		 * @return int Number of counted affiliates
		 * @use   YITH_WCAF_Affiliate_Handler::get_affiliates()
		 * @since 1.0.0
		 */
		public function count_affiliates( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'user_id'       => false,
				'user_login'    => false,
				'user_email'    => false,
				'payment_email' => false,
				'rate'          => false,
				'earnings'      => false,
				'paid'          => false,
				'balance'       => false,
				'clicks'        => false,
				'conversions'   => false,
				'conv_rate'     => false,
				'enabled'       => false,
				'banned'        => false,
				's'             => false
			);

			$args = wp_parse_args( $args, $defaults );

			return count( $this->get_affiliates( $args ) );
		}

		/**
		 * Return affiliates matching filtering criteria
		 *
		 * @param $args mixed Filtering criteria<br/>
		 *              [<br/>
		 *              'user_id' => false,              // affiliate related user id (int)<br/>
		 *              'user_login' => false,           // affiliate related user login, or part of it (string)<br/>
		 *              'user_email' => false,           // affiliate related user EMAIL, or part of it (string)<br/>
		 *              'payment_email' => false,        // affiliate payment email, or part of it (string)<br/>
		 *              'rate' => false,                 // affiliate rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'earnings' => false,             // affiliate earnings range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'paid' => false,                 // affiliate paid range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'balance' => false,              // affiliate balance range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'clicks' => false,               // affiliate clicks range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'conversions' => false,          // affiliate conversions range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'conv_rate' => false,            // affiliate conversion rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'enabled' => false,              // affiliate status (new/enabled/disabled)<br/>
		 *              'banned' => false,               // affiliates ban status (banned/unbanned)<br/>
		 *              's' => false                     // search string (string)<br/>
		 *              'order' => 'DESC',               // sorting direction (ASC/DESC)<br/>
		 *              'orderby' => 'ID',               // sorting column (any table valid column)<br/>
		 *              'limit' => 0,                    // limit (int)<br/>
		 *              'offset' => 0                    // offset (int)<br/>
		 *              ]
		 *
		 * @return mixed Matching affiliates
		 * @since 1.0.0
		 */
		public function get_affiliates( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'user_id'       => false,
				'user_login'    => false,
				'user_email'    => false,
				'payment_email' => false,
				'rate'          => false,
				'earnings'      => false,
				'paid'          => false,
				'balance'       => false,
				'clicks'        => false,
				'conversions'   => false,
				'conv_rate'     => false,
				'enabled'       => false,
				'banned'        => false,
				's'             => false,
				'order'         => 'DESC',
				'orderby'       => 'ID',
				'limit'         => 0,
				'offset'        => 0
			);

			$args = wp_parse_args( $args, $defaults );

			$query     = "SELECT
                       ya.*,
                       ( ya.earnings + ya.refunds ) AS totals,
                       ( ya.earnings - ya.paid ) AS balance,
                       ( ya.conversion / ya.click * 100 ) AS conv_rate,
                       u.user_login,
                       u.user_email,
                       u.display_name,
                       u.user_nicename
                      FROM {$wpdb->yith_affiliates} AS ya
                      LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
                      WHERE 1 = 1";
			$query_arg = array();

			if ( ! empty( $args['user_id'] ) ) {
				$query       .= ' AND ya.user_id = %d';
				$query_arg[] = $args['user_id'];
			}

			if ( ! empty( $args['user_login'] ) ) {
				$query       .= ' AND u.user_login LIKE %s';
				$query_arg[] = '%' . $args['user_login'] . '%';
			}

			if ( ! empty( $args['user_email'] ) ) {
				$query       .= ' AND u.user_email LIKE %s';
				$query_arg[] = '%' . $args['user_email'] . '%';
			}

			if ( ! empty( $args['payment_email'] ) ) {
				$query       .= ' AND ya.payment_email LIKE %s';
				$query_arg[] = '%' . $args['payment_email'] . '%';
			}

			if ( ! empty( $args['rate'] ) ) {
				if ( is_array( $args['rate'] ) && ( isset( $args['rate']['min'] ) || isset( $args['rate']['max'] ) ) ) {
					if ( ! empty( $args['rate']['min'] ) ) {
						$query       .= ' AND ya.rate >= %f';
						$query_arg[] = $args['rate']['min'];
					}

					if ( ! empty( $args['rate']['max'] ) ) {
						$query       .= ' AND ya.rate <= %f';
						$query_arg[] = $args['rate']['max'];
					}
				} elseif ( $args['rate'] == 'NULL' ) {
					$query .= ' AND ya.rate IS NULL';
				} elseif ( $args['rate'] == 'NOT NULL' ) {
					$query .= ' AND ya.rate IS NOT NULL';
				}
			}

			if ( ! empty( $args['earnings'] ) && is_array( $args['earnings'] ) && ( isset( $args['earnings']['min'] ) || isset( $args['earnings']['max'] ) ) ) {
				if ( ! empty( $args['earnings']['min'] ) ) {
					$query       .= ' AND ( ya.earnings + ya.refunds ) >= %f';
					$query_arg[] = $args['earnings']['min'];
				}

				if ( ! empty( $args['earnings']['max'] ) ) {
					$query       .= ' AND ( ya.earnings + ya.refunds ) <= %f';
					$query_arg[] = $args['earnings']['max'];
				}
			}

			if ( ! empty( $args['paid'] ) && is_array( $args['paid'] ) && ( isset( $args['paid']['min'] ) || isset( $args['paid']['max'] ) ) ) {
				if ( ! empty( $args['paid']['min'] ) ) {
					$query       .= ' AND ya.paid >= %f';
					$query_arg[] = $args['paid']['min'];
				}

				if ( ! empty( $args['paid']['max'] ) ) {
					$query       .= ' AND ya.paid <= %f';
					$query_arg[] = $args['paid']['max'];
				}
			}

			if ( ! empty( $args['balance'] ) && is_array( $args['balance'] ) && ( isset( $args['balance']['min'] ) || isset( $args['balance']['max'] ) ) ) {
				if ( ! empty( $args['balance']['min'] ) ) {
					$query       .= ' AND ( ya.earnings - ya.paid ) >= %f';
					$query_arg[] = $args['balance']['min'];
				}

				if ( ! empty( $args['balance']['max'] ) ) {
					$query       .= ' AND ( ya.earnings - ya.paid ) <= %f';
					$query_arg[] = $args['balance']['max'];
				}
			}

			if ( ! empty( $args['click'] ) && is_array( $args['click'] ) && ( isset( $args['click']['min'] ) || isset( $args['click']['max'] ) ) ) {
				if ( ! empty( $args['click']['min'] ) ) {
					$query       .= ' AND ya.click >= %f';
					$query_arg[] = $args['click']['min'];
				}

				if ( ! empty( $args['click']['max'] ) ) {
					$query       .= ' AND ya.click <= %f';
					$query_arg[] = $args['click']['max'];
				}
			}

			if ( ! empty( $args['conversion'] ) && is_array( $args['conversion'] ) && ( isset( $args['conversion']['min'] ) || isset( $args['conversion']['max'] ) ) ) {
				if ( ! empty( $args['conversion']['min'] ) ) {
					$query       .= ' AND ya.conversion >= %f';
					$query_arg[] = $args['conversion']['min'];
				}

				if ( ! empty( $args['conversion']['max'] ) ) {
					$query       .= ' AND ya.conversion <= %f';
					$query_arg[] = $args['conversion']['max'];
				}
			}

			if ( ! empty( $args['conv_rate'] ) && is_array( $args['conv_rate'] ) && ( isset( $args['conv_rate']['min'] ) || isset( $args['conv_rate']['max'] ) ) ) {
				if ( ! empty( $args['conv_rate']['min'] ) ) {
					$query       .= ' AND ( ya.conversion / ya.click * 100 ) >= %f';
					$query_arg[] = $args['conv_rate']['min'];
				}

				if ( ! empty( $args['conv_rate']['max'] ) ) {
					$query       .= ' AND ( ya.conversion / ya.click * 100 ) <= %f';
					$query_arg[] = $args['conv_rate']['max'];
				}
			}

			if ( ! empty( $args['enabled'] ) ) {
				$query .= ' AND ya.enabled = %d';
				switch ( $args['enabled'] ) {
					case 'new':
						$query_arg[] = 0;
						break;
					case 'disabled':
						$query_arg[] = - 1;
						break;
					case 'enabled':
					default:
						$query_arg[] = 1;
						break;
				}
			}

			if ( ! empty( $args['banned'] ) ) {
				$query       .= ' AND ya.banned = %d';
				$query_arg[] = ( $args['banned'] == 'banned' ) ? 1 : 0;
			}

			if ( ! empty( $args['s'] ) ) {
				$query         .= ' AND ( u.user_login LIKE %s OR u.user_email LIKE %s OR ya.token LIKE %s OR ya.payment_email LIKE %s )';
				$search_string = '%' . $args['s'] . '%';

				$query_arg = array_merge( $query_arg, array(
					$search_string,
					$search_string,
					$search_string,
					$search_string
				) );
			}

			if ( ! empty( $args['orderby'] ) ) {
				$query .= sprintf( ' ORDER BY %s %s', $args['orderby'], $args['order'] );
			}

			if ( ! empty( $args['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $args['offset'] ) ? $args['offset'] : 0, $args['limit'] );
			}

			if ( ! empty( $query_arg ) ) {
				$query = $wpdb->prepare( $query, $query_arg );
			}

			$res = $wpdb->get_results( $query, ARRAY_A );

			return $res;
		}

		/**
		 * Print json encoded list of affiliate matching filter (param $term in request used to filter)
		 * Array is formatted as affiliate_id => Verbose affiliate description
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function get_affiliates_via_ajax() {
			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			if ( ! current_user_can( apply_filters( 'yith_wcaf_panel_capability', 'manage_woocommerce' ) ) ) {
				die( - 1 );
			}

			$term = wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}

			$found_affiliates     = array();
			$found_affiliates_raw = array_merge( $this->get_affiliates( array( 'user_login' => $term ) ), $this->get_affiliates( array( 'user_email' => $term ) ) );

			if ( ! empty( $found_affiliates_raw ) ) {
				foreach ( $found_affiliates_raw as $affiliate ) {
					$user = get_user_by( 'id', $affiliate['user_id'] );

					$username = '';
					if ( $user->first_name || $user->last_name ) {
						$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
					} else {
						$username .= esc_html( ucfirst( $user->display_name ) );
					}

					$found_affiliates[ $affiliate['ID'] ] = $username . ' (#' . $user->ID . ' &ndash; ' . sanitize_email( $user->user_email ) . ')';
				}
			}

			wp_send_json( $found_affiliates );
		}

		/**
		 * Return affiliate matching passed token
		 *
		 * @param $token          string Affiliate token to find
		 * @param $enabled        string Whether to find all affiliate whatever the state (all), or only enabled (true) or disabled (false) ones
		 * @param $exclude_banned bool Whether to exclude from current selection banned affiliates (default to false)
		 *
		 * @return mixed Result
		 * @since 1.0.0
		 */
		public function get_affiliate_by_token( $token, $enabled = 'all', $exclude_banned = false ) {
			global $wpdb;

			$query = "SELECT
                       ya.*,
                       ( ya.earnings + ya.refunds ) AS totals,
                       ( ya.earnings - ya.paid ) AS balance,
                       ( ya.conversion / ya.click * 100 ) AS conv_rate,
                       u.user_login,
                       u.user_email,
                       u.display_name,
                       u.user_nicename
			          FROM {$wpdb->yith_affiliates} AS ya
			          LEFT JOIN {$wpdb->users} AS u ON ya.user_id = u.ID
			          WHERE ya.token = %s";

			$query_args = array(
				$token
			);

			if ( isset( $enabled ) && is_bool( $enabled ) ) {
				$query        .= ' AND ya.enabled = %d';
				$query_args[] = $enabled ? 1 : - 1;
			}

			if ( $exclude_banned ) {
				$query        .= ' AND ya.banned = %d';
				$query_args[] = 0;
			}

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Return affiliate matching passed id
		 *
		 * @param $id             int Affiliate id to find
		 * @param $enabled        string Whether to find all affiliate whatever the state (all), or only enabled (true) or disabled (false) ones
		 * @param $exclude_banned bool Whether to exclude from current selection banned affiliates (default to false)
		 *
		 * @return mixed Result
		 * @since 1.0.0
		 */
		public function get_affiliate_by_id( $affiliate_id, $enabled = 'all', $exclude_banned = false ) {
			global $wpdb;

			$query = "SELECT
                       ya.*,
                       ( ya.earnings + ya.refunds ) AS totals,
                       ( ya.earnings - ya.paid ) AS balance,
                       ( ya.conversion / ya.click * 100 ) AS conv_rate,
                       u.user_login,
                       u.user_email,
                       u.display_name,
                       u.user_nicename
			          FROM {$wpdb->yith_affiliates} AS ya
			          LEFT JOIN {$wpdb->users} AS u ON ya.user_id = u.ID
			          WHERE ya.ID = %d";

			$query_args = array(
				$affiliate_id
			);

			if ( isset( $enabled ) && is_bool( $enabled ) ) {
				$query        .= ' AND ya.enabled = %d';
				$query_args[] = $enabled ? 1 : - 1;
			}

			if ( $exclude_banned ) {
				$query        .= ' AND ya.banned = %d';
				$query_args[] = 0;
			}

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Return affiliate matching passed user id
		 *
		 * @param $id             int User id to find
		 * @param $enabled        string Whether to find all affiliate whatever the state (all), or only enabled (true) or disabled (false) ones
		 * @param $exclude_banned bool Whether to exclude from current selection banned affiliates (default to false)
		 *
		 * @return mixed Result
		 * @since 1.0.0
		 */
		public function get_affiliate_by_user_id( $user_id, $enabled = 'all', $exclude_banned = false ) {
			global $wpdb;

			$query = "SELECT
                       ya.*,
                       ( ya.earnings + ya.refunds ) AS earnings,
                       ( ya.earnings - ya.paid ) AS balance,
                       ( ya.conversion / ya.click * 100 ) AS conv_rate,
                       u.user_login,
                       u.user_email,
                       u.display_name,
                       u.user_nicename
			          FROM {$wpdb->yith_affiliates} AS ya
			          LEFT JOIN {$wpdb->users} AS u ON ya.user_id = u.ID
			          WHERE ya.user_id = %d";

			$query_args = array(
				$user_id
			);

			if ( isset( $enabled ) && is_bool( $enabled ) ) {
				$query        .= ' AND ya.enabled = %d';
				$query_args[] = $enabled ? 1 : - 1;
			}

			if ( $exclude_banned ) {
				$query        .= ' AND ya.banned = %d';
				$query_args[] = 0;
			}

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Return affiliate rate for a specific affiliate id
		 *
		 * @param $affiliate_id int Affiliate ID
		 *
		 * @return float Affiliate rate
		 * @since 1.0.0
		 */
		public function get_affiliate_rate( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return false;
			}

			return (float) $affiliate['rate'];
		}

		/**
		 * Update affiliate rate for a specific affiliate id (set it null if no rate is passed)
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $rate         float New affiliate rate
		 *
		 * @return int Operation result
		 * @since 1.0.0
		 */
		public function update_affiliate_rate( $affiliate_id, $rate = false ) {
			global $wpdb;

			if ( $rate === false ) {
				$res = $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->yith_affiliates} SET rate = NULL WHERE ID = %d", $affiliate_id ) );
			} else {
				$res = $this->update( $affiliate_id, array( 'rate' => $rate ) );
			}

			return $res;
		}

		/**
		 * Return affiliate earnings for a specific affiliate id
		 *
		 * @param $affiliate_id int Affiliate ID
		 *
		 * @return float Affiliate earnings
		 * @since 1.0.0
		 */
		public function get_affiliate_total( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return 0;
			}

			return (float) $affiliate['earnings'];
		}

		/**
		 * Update affiliate total for a specific affiliate id (sum amount passed to total)
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $amount       float Amount to sum to old total
		 *
		 * @return int Operation result
		 * @since 1.0.0
		 */
		public function update_affiliate_total( $affiliate_id, $amount ) {
			$total_user_commissions = $this->get_affiliate_total( $affiliate_id );
			$total_user_commissions += (float) $amount;
			$total_user_commissions = $total_user_commissions > 0 ? $total_user_commissions : 0;

			$this->update( $affiliate_id, array( 'earnings' => $total_user_commissions ) );
		}

		/**
		 * Return affiliate refunds for a specific affiliate id
		 *
		 * @param $affiliate_id int Affiliate ID
		 *
		 * @return float Affiliate refunds
		 * @since 1.0.0
		 */
		public function get_affiliate_refunds( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return 0;
			}

			return (float) $affiliate['refunds'];
		}

		/**
		 * Update affiliate refunds for a specific affiliate id (sum amount passed to total)
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $amount       float Amount to sum to old total
		 *
		 * @return int Operation result
		 * @since 1.0.0
		 */
		public function update_affiliate_refunds( $affiliate_id, $amount ) {
			$total_user_refunds = $this->get_affiliate_refunds( $affiliate_id );
			$total_user_refunds += (float) $amount;
			$total_user_refunds = $total_user_refunds > 0 ? $total_user_refunds : 0;

			// check if total refund exceed for some reason earning amount
			$total_user_earnings = $this->get_affiliate_total( $affiliate_id );
			$total_user_refunds  = $total_user_refunds < $total_user_earnings ? $total_user_refunds : $total_user_earnings;

			$this->update( $affiliate_id, array( 'refunds' => $total_user_refunds ) );
		}

		/**
		 * Return affiliate total payments for a specific affiliate id
		 *
		 * @param $affiliate_id int Affiliate ID
		 *
		 * @return float Affiliate refunds
		 * @since 1.0.0
		 */
		public function get_affiliate_payments( $affiliate_id ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return 0;
			}

			return (float) $affiliate['paid'];
		}

		/**
		 * Update affiliate total payments for a specific affiliate id (sum amount passed to total)
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $amount       float Amount to sum to old total
		 *
		 * @return int Operation result
		 * @since 1.0.0
		 */
		public function update_affiliate_payments( $affiliate_id, $amount ) {
			$total_user_payments = $this->get_affiliate_payments( $affiliate_id );
			$total_user_payments += (float) $amount;
			$total_user_payments = $total_user_payments > 0 ? $total_user_payments : 0;

			// check if total paid exceed for some reason earning amount
			$total_user_earnings = $this->get_affiliate_total( $affiliate_id );
			$total_user_payments = $total_user_payments < $total_user_earnings ? $total_user_payments : $total_user_earnings;

			$this->update( $affiliate_id, array( 'paid' => $total_user_payments ) );
		}

		/**
		 * Return affiliate balance for a specific affiliate id
		 *
		 * @param $affiliate_id int Affiliate ID
		 * @param $stype        string Stored or Actual, depending on how to calculate balance
		 *
		 * @return float Affiliate refunds
		 * @since 1.0.0
		 */
		public function get_affiliate_balance( $affiliate_id, $type = 'stored' ) {
			$affiliate = $this->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return 0;
			}

			if ( $type == 'actual' ) {
				$balance     = 0;
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
					'status' => 'pending'
				) );

				if ( ! empty( $commissions ) ) {
					$balance = array_sum( wp_list_pluck( $commissions, 'amount' ) );
				}
			} else {
				$balance = (float) $affiliate['earnings'] - (float) $affiliate['paid'];
				$balance = $balance > 0 ? $balance : 0;
			}

			return $balance;
		}

		/**
		 * Return default token for a specific user id
		 *
		 * @param $user_id int User id
		 *
		 * @return string User default token
		 * @since 1.0.0
		 */
		public function get_default_user_token( $user_id ) {
			$default_token = $user_id;

			return apply_filters( 'yith_wcaf_affiliate_token', $default_token, $user_id );
		}

		/**
		 * Return user object for the given token
		 *
		 * @param $token string Token to use to retrieve user
		 *
		 * @return \WP_User|bool User object, or false if token doesn't match any user
		 * @since 1.0.0
		 */
		public function get_user_by_token( $token ) {
			if ( ! empty( $token ) ) {
				$affiliate = $this->get_affiliate_by_token( $token, true );

				if ( ! $affiliate ) {
					return false;
				}

				$user = get_user_by( 'id', $affiliate['user_id'] );

				if ( $user ) {
					return $user;
				}
			}

			return false;
		}

		/**
		 * Check if given string is a valid affiliate token
		 *
		 * @param $token string Token to check
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function is_valid_token( $token ) {
			$user = $this->get_user_by_token( $token );

			if ( ! $user ) {
				return false;
			}

			if ( ! $this->is_user_valid_affiliate( $user->ID ) ) {
				return false;
			}

			$current_user_id        = get_current_user_id();
			$avoid_auto_commissions = get_option( 'yith_wcaf_commission_avoid_auto_referral', 'yes' );

			if ( $avoid_auto_commissions == 'yes' && is_user_logged_in() && $user->ID == $current_user_id ) {
				return false;
			}

			return apply_filters( 'yith_wcaf_is_valid_token', true, $token );
		}

		/**
		 * Returns true if user is an affiliate
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is an affiliate or not
		 * @since 1.0.0
		 */
		public function is_user_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliates = $this->get_affiliates( array( 'user_id' => $user_id ) );

			return apply_filters( 'yith_wcaf_is_user_affiliate', ! empty( $affiliates ), $user_id );
		}

		/**
		 * Returns true if user is an enabled affiliate (enabled = 1)
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is an enabled affiliate or not
		 * @since 1.0.0
		 */
		public function is_user_enabled_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliates = $this->get_affiliates( array( 'user_id' => $user_id, 'enabled' => 'enabled' ) );

			return apply_filters( 'yith_wcaf_is_user_enabled_affiliate', ! empty( $affiliates ), $user_id );
		}

		/**
		 * Returns true if user is a pending affiliate (enabled = 0)
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is an enabled affiliate or not
		 * @since 1.0.0
		 */
		public function is_user_pending_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliates = $this->get_affiliates( array( 'user_id' => $user_id, 'enabled' => 'new' ) );

			return apply_filters( 'yith_wcaf_is_user_pending_affiliate', ! empty( $affiliates ), $user_id );
		}

		/**
		 * Returns true if user is a rejected affiliate (enabled = -1)
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is an enabled affiliate or not
		 * @since 1.0.0
		 */
		public function is_user_rejected_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliates = $this->get_affiliates( array( 'user_id' => $user_id, 'enabled' => 'disabled' ) );

			return apply_filters( 'yith_wcaf_is_user_rejected_affiliate', ! empty( $affiliates ), $user_id );
		}

		/**
		 * Checks whether current affiliate is valid (enabled and not banned)
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is a valid affiliate or not
		 * @since 1.2.5
		 */
		public function is_user_valid_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliates = $this->get_affiliates( array(
				'user_id' => $user_id,
				'enabled' => 'enabled',
				'banned'  => 'unbanned'
			) );

			return apply_filters( 'yith_wcaf_is_user_valid_affiliate', ! empty( $affiliates ), $user_id );
		}

		/**
		 * Checks whether current affiliate is banned
		 *
		 * @param $user_id int|bool Id of the user to check; false if currently logged in user should be considered
		 *
		 * @return bool Whether user is a banned affiliate or not
		 * @since 1.2.5
		 */
		public function is_user_banned_affiliate( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id ) {
				return false;
			}

			$affiliate = $this->get_affiliate_by_user_id( $user_id );
			$banned    = $affiliate && isset( $affiliate['banned'] ) ? (bool) $affiliate['banned'] : false;

			return apply_filters( 'yith_wcaf_is_user_banned_affiliate', $banned, $user_id );
		}

		/**
		 * Check if user can see a specific section of the Affiliate Dashboard
		 *
		 * @param $user_id int|bool User id; false to use current user id
		 * @param $section string Section id
		 * @param $nopriv  bool Whether section should be visible by unauthenticated users or not
		 *
		 * @return bool Whether user can see section or not
		 *
		 * @since 1.2.5
		 */
		public function can_user_see_section( $user_id = false, $section = 'summary', $nopriv = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( ! $user_id && ! $nopriv ) {
				return false;
			}

			$return = true;

			return apply_filters( 'yith_wcaf_can_user_see_section', $return, $user_id, $section );
		}

		/**
		 * Returns count of affiliate, grouped by status
		 *
		 * @param $status string Specific status to count, or all to obtain a global statistic
		 * @param $args   mixed Array of arguments to filter status query<br/>
		 *                [<br/>
		 *                's' => false                     // search string (string)<br/>
		 *                ]
		 *
		 * @return int|mixed Count per state, or array indexed by status, with status count
		 * @since 1.0.0
		 */
		public function per_status_count( $status = 'all', $args = array() ) {
			global $wpdb;

			$res = wp_cache_get( 'per_status_count', 'yith_wcaf_affiliates' );

			if ( ! $res ) {
				$query     = "SELECT ya.enabled, ya.banned, COUNT( ya.enabled ) AS status_count 
                      FROM {$wpdb->yith_affiliates} AS ya 
                      LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
                      WHERE 1 = 1";
				$query_arg = array();

				if ( ! empty( $args['s'] ) ) {
					$query         .= ' AND ( u.user_login LIKE %s OR u.user_email LIKE %s OR ya.token LIKE %s OR ya.payment_email LIKE %s )';
					$search_string = '%' . $args['s'] . '%';

					$query_arg = array_merge( $query_arg, array(
						$search_string,
						$search_string,
						$search_string,
						$search_string
					) );
				}

				$query .= " GROUP BY enabled, banned";

				if ( ! empty( $query_arg ) ) {
					$query = $wpdb->prepare( $query, $query_arg );
				}

				$res = $wpdb->get_results( $query, ARRAY_A );

				$banned   = 0;
				$statuses = array();
				$counts   = array();
				if ( ! empty( $res ) ) {
					foreach ( $res as $row ) {
						if ( $row['banned'] == 1 ) {
							$banned += $row['status_count'];
						} else {
							$statuses[] = $row['enabled'];
							$counts[]   = $row['status_count'];
						}
					}
				}

				$res = array(
					'banned'   => $banned,
					'counts'   => $counts,
					'statuses' => $statuses
				);

				wp_cache_set( 'per_status_count', $res, 'yith_wcaf_affiliates' );
			}

			extract( $res );

			if ( $status == 'all' ) {
				return array_sum( $counts ) + $banned;
			} elseif ( $status == 'banned' ) {
				return $banned;
			} else {
				switch ( $status ) {
					case 'new':
						$status = 0;
						break;
					case 'disabled':
						$status = - 1;
						break;
					case 'enabled':
					default:
						$status = 1;
				}

				if ( in_array( $status, $statuses ) ) {
					$index = array_search( $status, $statuses );

					if ( $index === false ) {
						return 0;
					} else {
						return $counts[ $index ];
					}
				} else {
					return 0;
				}
			}
		}

		/**
		 * Returns true if affiliate has some unpaid commissions
		 *
		 * @param $affiliate_id int Affiliate id
		 *
		 * @return bool Whether affiliate has unpaid commissions or not
		 * @since 1.0.10
		 */
		public function has_unpaid_commissions( $affiliate_id ) {
			$unpaid_commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
				'affiliate_id' => $affiliate_id,
				'status'       => 'pending'
			) );

			return apply_filters( 'yith_wcaf_affiliate_has_unpaid_commissions', ! empty( $unpaid_commissions ), $affiliate_id, $unpaid_commissions );
		}

		/**
		 * Returns role name
		 *
		 * @return string Role name
		 * @since 1.2.0
		 */
		public function get_role_name() {
			return self::$_role_name;
		}

		/* === PANEL HANDLING METHODS === */

		/**
		 * Print Affiliate panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_affiliate_panel() {
			// define variables to be used in template
			$affiliates_table = new YITH_WCAF_Affiliates_Table();
			$affiliates_table->prepare_items();

			include( YITH_WCAF_DIR . 'templates/admin/affiliate-panel.php' );
		}

		/**
		 * Add Screen option
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_screen_option() {
			if ( 'yith-plugins_page_yith_wcaf_panel' == get_current_screen()->id && isset( $_GET['tab'] ) && $_GET['tab'] == 'affiliates' ) {
				add_screen_option( 'per_page', array(
					'label'   => __( 'Affiliates', 'yith-woocommerce-affiliates' ),
					'default' => 20,
					'option'  => 'edit_affiliates_per_page'
				) );

			}
		}

		/**
		 * Save custom screen options
		 *
		 * @param $set    bool Value to filter (default to false)
		 * @param $option string Custom screen option key
		 * @param $value  mixed Custom screen option value
		 *
		 * @return mixed Value to be saved as user meta; false if no value should be saved
		 */
		public function set_screen_option( $set, $option, $value ) {
			return ( isset( $_GET['tab'] ) && 'affiliates' == $_GET['tab'] && 'edit_affiliates_per_page' == $option ) ? $value : $set;
		}

		/**
		 * Add columns filters to commissions page
		 *
		 * @param $columns mixed Available columns
		 *
		 * @return mixed The columns array to print
		 * @since 1.0.0
		 */
		public function add_screen_columns( $columns ) {
			if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'affiliates' ) {
				$columns = array_merge(
					$columns,
					array(
						'id'         => __( 'ID', 'yith-woocommerce-affiliates' ),
						'token'      => __( 'Token', 'yith-woocommerce-affiliates' ),
						'status'     => __( 'Status', 'yith-woocommerce-affiliates' ),
						'affiliate'  => __( 'Affiliate', 'yith-woocommerce-affiliates' ),
						'rate'       => __( 'Rate', 'yith-woocommerce-affiliates' ),
						'earnings'   => __( 'Earnings', 'yith-woocommerce-affiliates' ),
						'refunds'    => __( 'Refunds', 'yith-woocommerce-affiliates' ),
						'paid'       => __( 'Paid', 'yith-woocommerce-affiliates' ),
						'balance'    => __( 'Balance', 'yith-woocommerce-affiliates' ),
						'click'      => __( 'Click', 'yith-woocommerce-affiliates' ),
						'conversion' => __( 'Conversion', 'yith-woocommerce-affiliates' ),
						'conv_rate'  => __( 'Conv. Rate', 'yith-woocommerce-affiliates' ),
						'actions'    => __( 'Action', 'yith-woocommerce-affiliates' )
					)
				);
			}

			return $columns;
		}

		/**
		 * Handle affiliate user status change
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function handle_switch_status_panel_actions() {
			$affiliate_id = isset( $_REQUEST['affiliate_id'] ) ? $_REQUEST['affiliate_id'] : 0;
			$new_status   = isset( $_REQUEST['status'] ) && in_array( $_REQUEST['status'], array(
				'enabled',
				'disabled',
				'banned',
				'unbanned'
			) ) ? $_REQUEST['status'] : '';

			if ( ! $affiliate_id || ! $new_status ) {
				return;
			}

			// ban/unban affiliates
			if ( in_array( $_REQUEST['status'], array( 'banned', 'unbanned' ) ) ) {
				$banned = $_REQUEST['status'] == 'banned' ? 1 : 0;

				$res = $this->update( $affiliate_id, array( 'banned' => $banned ) );

				/**
				 * Let third party execute actions when affiliate is enabled or disabled
				 *
				 * @param $affiliate_id int Id of the affiliate tha have been enabled/disabled
				 *
				 * @since 1.2.5
				 */
				if ( $res ) {
					$affiliate = $this->get_affiliate_by_id( $affiliate_id );

					if ( $banned ) {
						if ( isset( $_REQUEST['message'] ) ) {
							$ban_message = wp_kses_post( $_REQUEST['message'] );

							update_user_meta( $affiliate['user_id'], '_yith_wcaf_ban_message', $ban_message );
						}

						do_action( 'yith_wcaf_affiliate_banned', $affiliate_id );
					} else {
						update_user_meta( $affiliate['user_id'], '_yith_wcaf_ban_message', '' );

						do_action( 'yith_wcaf_affiliate_unbanned', $affiliate_id );
					}
				}
			}

			// enable/disable affiliates
			if ( in_array( $_REQUEST['status'], array( 'enabled', 'disabled' ) ) ) {
				$enabled = $_REQUEST['status'] == 'enabled' ? 1 : - 1;

				$affiliate = $this->get_affiliate_by_id( $affiliate_id );

				if ( isset( $_REQUEST['message'] ) ) {
					$reject_message = wp_kses_post( $_REQUEST['message'] );

					update_user_meta( $affiliate['user_id'], '_yith_wcaf_reject_message', $reject_message );
				}

				$res = $this->update( $affiliate_id, array( 'enabled' => $enabled ) );

				/**
				 * Let third party execute actions when affiliate is enabled or disabled
				 *
				 * @param $affiliate_id int Id of the affiliate tha have been enabled/disabled
				 *
				 * @since 1.1.0
				 */
				if ( $res ) {
					if ( $enabled == 1 ) {
						do_action( 'yith_wcaf_affiliate_enabled', $affiliate_id );
					} else {
						do_action( 'yith_wcaf_affiliate_disabled', $affiliate_id );
					}
				}
			}

			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page'                     => 'yith_wcaf_panel',
				'tab'                      => 'affiliates',
				'commission_status_change' => $res
			), admin_url( 'admin.php' ) ) );

			wp_redirect( $redirect_to );
			die();
		}

		/**
		 * Handle "Process dangling commissions" action from panel actions
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function handle_process_dangling_commissions_panel_action() {
			$affiliate_id = isset( $_REQUEST['affiliate_id'] ) ? $_REQUEST['affiliate_id'] : 0;
			$res          = true;

			if ( ! $affiliate_id ) {
				$res = false;
			} else {
				$affiliate = $this->get_affiliate_by_id( $affiliate_id );

				if ( ! $affiliate ) {
					$res = false;
				} else {
					$this->process_dangling_commissions( $affiliate_id, $affiliate['token'] );
				}
			}

			$redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : esc_url_raw( add_query_arg( array(
				'page'               => 'yith_wcaf_panel',
				'tab'                => 'affiliates',
				'processed_dangling' => $res
			), admin_url( 'admin.php' ) ) );

			wp_redirect( $redirect_to );
			die();
		}

		/**
		 * Process bulk action for current view
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_bulk_actions() {
			$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
			$current_action = ( empty( $current_action ) && isset( $_REQUEST['action2'] ) ) ? $_REQUEST['action2'] : $current_action;

			if ( ! empty( $_REQUEST['affiliates'] ) && ! empty( $current_action ) ) {
				$redirect = esc_url_raw( remove_query_arg( array( 'action', 'action2', 'affiliates' ) ) );

				switch ( $current_action ) {
					case 'delete':
						foreach ( $_REQUEST['affiliates'] as $affiliate_id ) {
							YITH_WCAF_Affiliate_Handler()->delete( $affiliate_id );
						}
						break;
					case 'enable':
						foreach ( $_REQUEST['affiliates'] as $affiliate_id ) {
							YITH_WCAF_Affiliate_Handler()->update( $affiliate_id, array( 'enabled' => 1 ) );
							do_action( 'yith_wcaf_affiliate_enabled', $affiliate_id );
						}
						break;
					case 'disable':
						$reject_message = isset( $_REQUEST['message'] ) ? wp_kses_post( $_REQUEST['message'] ) : false;

						foreach ( $_REQUEST['affiliates'] as $affiliate_id ) {
							if ( $reject_message ) {
								$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

								if ( $affiliate ) {
									update_user_meta( $affiliate['user_id'], '_yith_wcaf_reject_message', $reject_message );
								}
							}

							YITH_WCAF_Affiliate_Handler()->update( $affiliate_id, array( 'enabled' => - 1 ) );

							do_action( 'yith_wcaf_affiliate_disabled', $affiliate_id );
						}
						break;
					case 'unban':
						foreach ( $_REQUEST['affiliates'] as $affiliate_id ) {
							YITH_WCAF_Affiliate_Handler()->update( $affiliate_id, array( 'banned' => 0 ) );
							do_action( 'yith_wcaf_affiliate_unbanned', $affiliate_id );
						}
						break;
					case 'ban':
						$ban_message = isset( $_REQUEST['message'] ) ? wp_kses_post( $_REQUEST['message'] ) : false;

						foreach ( $_REQUEST['affiliates'] as $affiliate_id ) {
							YITH_WCAF_Affiliate_Handler()->update( $affiliate_id, array( 'banned' => 1 ) );

							if ( $ban_message ) {
								$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

								if ( $affiliate ) {
									update_user_meta( $affiliate['user_id'], '_yith_wcaf_ban_message', $ban_message );
								}
							}

							do_action( 'yith_wcaf_affiliate_banned', $affiliate_id );
						}
						break;
				}

				wp_redirect( $redirect );
				die();
			}

		}

		/* === EDIT PROFILE METHODS === */

		/**
		 * Render affiliate fields
		 *
		 * @param $user \WP_User User object
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function render_affiliate_extra_fields( $user ) {
			$affiliate = false;

			if ( isset( $user->ID ) ) {
				$affiliates = $this->get_affiliates( array( 'user_id' => $user->ID ) );
				$affiliate  = isset( $affiliates[0] ) ? $affiliates[0] : false;
			}

			if ( ! current_user_can( apply_filters( 'yith_wcaf_panel_capability', 'manage_woocommerce' ) ) ) {
				return;
			}

			$is_affiliate   = $affiliate ? true : false;
			$is_enabled     = isset( $affiliate['enabled'] ) ? $affiliate['enabled'] : 0;
			$reject_message = $user ? get_user_meta( $user->ID, '_yith_wcaf_reject_message', true ) : false;
			$is_banned      = isset( $affiliate['banned'] ) ? $affiliate['banned'] : 0;
			$ban_message    = $user ? get_user_meta( $user->ID, '_yith_wcaf_ban_message', true ) : false;
			$token          = isset( $affiliate['token'] ) ? $affiliate['token'] : '';
			$token          = ( empty( $token ) && isset( $user->ID ) ) ? $this->get_default_user_token( $user->ID ) : $token;
			$rate           = isset( $affiliate['rate'] ) ? $affiliate['rate'] : '';
			$payment_email  = isset( $affiliate['payment_email'] ) ? $affiliate['payment_email'] : '';

			?>
			<hr/>
			<h3><?php _e( 'Affiliate details', 'yith-woocommerce-affiliates' ) ?></h3>
			<table class="form-table">
				<tr>
					<th><label for="affiliate"><?php _e( 'Affiliate', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="checkbox" name="affiliate" id="affiliate" value="1" <?php checked( $is_affiliate, true ) ?> />
						<span class="description"><?php _e( 'Check if this user is an affiliate', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="enabled"><?php _e( 'Enabled', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<select name="enabled" id="enabled">
							<option value="0" <?php selected( $is_enabled == 0 ) ?> ><?php _e( 'New', 'yith-woocommerce-affiliates' ) ?></option>
							<option value="1" <?php selected( $is_enabled == 1 ) ?> ><?php _e( 'Accepted', 'yith-woocommerce-affiliates' ) ?></option>
							<option value="-1" <?php selected( $is_enabled == - 1 ) ?> ><?php _e( 'Rejected', 'yith-woocommerce-affiliates' ) ?></option>
						</select>
						<span class="description"><?php _e( 'If this user is an affiliate, you can choose to enable or disable it', 'yith-wcaf' ) ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="reject_message"><?php _e( 'Reject Message', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<textarea name="reject_message" id="reject_message" cols="50" rows="10"><?php echo $reject_message ?></textarea>
						<p class="description"><?php _e( 'Optionally you can show affiliate a message, explaining why her/his account was rejected', 'yith-wcaf' ) ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="banned"><?php _e( 'Banned', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="checkbox" name="banned" id="banned" value="1" <?php checked( $is_banned, true ) ?> />
						<span class="description"><?php _e( 'If this user is an affiliate, you can choose to ban or unban it', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="ban_message"><?php _e( 'Ban Message', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<textarea name="ban_message" id="ban_message" cols="50" rows="10"><?php echo $ban_message ?></textarea>
						<p class="description"><?php _e( 'Optionally you can show affiliate a message, explaining why her/his account was banned', 'yith-woocommerce-affiliates' ) ?></p>
					</td>
				</tr>
				<tr>
					<th><label for="token"><?php _e( 'Token', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="text" name="token" id="token" value="<?php echo esc_attr( $token ) ?>" class="regular-text"/>
						<span class="description"><?php _e( 'Token for the user (default to user ID)', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="rate"><?php _e( 'Rate', 'yith-woocommerce-affiliates' ) ?></label></th>
					<td>
						<input type="number" min="0" max="100" step="any" name="rate" id="rate" value="<?php echo esc_attr( $rate ) ?>"/>
						<span class="description"><?php _e( 'User-specific rate to apply, if any (general rates will be applied if left empty)', 'yith-wcaf' ) ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="payment_email"><?php _e( 'Payment email', 'yith-woocommerce-affiliates' ) ?></label>
					</th>
					<td>
						<input type="email" name="payment_email" id="payment_email" value="<?php echo esc_attr( $payment_email ) ?>" class="regular-text"/>
						<span class="description"><?php _e( 'Address email where affiliate wants to receive PayPal payments', 'yith-woocommerce-affiliates' ) ?></span>
					</td>
				</tr>

			</table>
			<?php
		}

		/**
		 * Save affiliate fields
		 *
		 * @param $user_id int User id
		 *
		 * @return bool Whether method actually saved option or not
		 * @since  1.0.0
		 */
		public function save_affiliate_extra_fields( $user_id ) {
			if ( ! current_user_can( apply_filters( 'yith_wcaf_panel_capability', 'manage_woocommerce' ) ) ) {
				return;
			}

			$affiliates = $this->get_affiliates( array( 'user_id' => $user_id ) );
			$affiliate  = isset( $affiliates[0] ) ? $affiliates[0] : false;

			$is_affiliate  = isset( $_POST['affiliate'] ) ? $_POST['affiliate'] : false;
			$is_enabled    = isset( $_POST['enabled'] ) && in_array( $_POST['enabled'], array(
				0,
				1,
				- 1
			) ) ? $_POST['enabled'] : 0;
			$is_banned     = isset( $_POST['banned'] ) ? $_POST['banned'] : 0;
			$token         = ( isset( $_POST['token'] ) && $_POST['token'] != '' ) ? trim( $_POST['token'] ) : $this->get_default_user_token( $user_id );
			$rate          = ( isset( $_POST['rate'] ) && $_POST['rate'] != '' ) ? floatval( $_POST['rate'] ) : false;
			$payment_email = isset( $_POST['payment_email'] ) ? trim( $_POST['payment_email'] ) : '';

			if ( $is_affiliate && ! $affiliate ) {
				$this->add(
					array_merge(
						array(
							'user_id'       => $user_id,
							'token'         => $token,
							'enabled'       => $is_enabled,
							'banned'        => $is_banned,
							'payment_email' => $payment_email
						),
						$rate !== false ? array( 'rate' => $rate ) : array()
					)
				);
			} elseif ( $is_affiliate && $affiliate ) {
				$this->update(
					$affiliate['ID'],
					array(
						'token'         => $token,
						'enabled'       => $is_enabled,
						'banned'        => $is_banned,
						'payment_email' => $payment_email
					)
				);

				$this->update_affiliate_rate( $affiliate['ID'], $rate );

				update_user_meta( $user_id, '_yith_wcaf_ban_message', wp_kses_post( $_POST['ban_message'] ) );
				update_user_meta( $user_id, '_yith_wcaf_reject_message', wp_kses_post( $_POST['reject_message'] ) );

				/**
				 * Let third party execute actions when affiliate is enabled or disabled
				 *
				 * @param $affiliate_id int Id of the affiliate tha have been enabled/disabled
				 *
				 * @since 1.1.0
				 */
				if ( $is_enabled ) {
					do_action( 'yith_wcaf_affiliate_enabled', $affiliate['ID'] );
				} else {
					do_action( 'yith_wcaf_affiliate_disabled', $affiliate['ID'] );
				}

				/**
				 * Let third party execute actions when affiliate is banned or unbanned
				 *
				 * @param $affiliate_id int Id of the affiliate tha have been banned/unbanned
				 *
				 * @since 1.2.5
				 */
				if ( $is_banned ) {
					do_action( 'yith_wcaf_affiliate_banned', $affiliate['ID'] );
				} else {
					do_action( 'yith_wcaf_affiliate_unbanned', $affiliate['ID'] );
				}
			} elseif ( ! $is_affiliate && $affiliate ) {
				$this->delete( $affiliate['ID'] );
			}

		}

		/* === AFFILIATE DASHBOARD METHODS === */

		/**
		 * Print ban message is affiliate is banned and message not empty
		 *
		 * @return void
		 * @since 1.2.5
		 */
		public function print_ban_message() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$user_id = get_current_user_id();

			if ( $this->is_user_affiliate( $user_id ) && $this->is_user_banned_affiliate( $user_id ) ) {
				$ban_message = get_user_meta( $user_id, '_yith_wcaf_ban_message', true );

				if ( $ban_message ) {
					wc_print_notice( nl2br( $ban_message ), 'error' );
				}
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Affiliate_Handler
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Affiliate_Handler_Premium' ) ) {
				return YITH_WCAF_Affiliate_Handler_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF_Affiliate_Handler::$instance ) ) {
					YITH_WCAF_Affiliate_Handler::$instance = new YITH_WCAF_Affiliate_Handler;
				}

				return YITH_WCAF_Affiliate_Handler::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Affiliate_Handler class
 *
 * @return \YITH_WCAF_Affiliate_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Affiliate_Handler() {
	return YITH_WCAF_Affiliate_Handler::get_instance();
}