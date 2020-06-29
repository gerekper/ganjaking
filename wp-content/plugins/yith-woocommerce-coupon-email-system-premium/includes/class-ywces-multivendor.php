<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCES_MultiVendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWCES_MultiVendor
	 * @package Yithemes
	 * @since   1.0.5
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCES_MultiVendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCES_MultiVendor
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCES_MultiVendor
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

		/**
		 * @var YITH_Vendor current vendor
		 */
		protected $vendor;

		/**
		 * @var YITH_Vendor active vendors
		 */
		protected $active_vendors;

		/**
		 * @var string Yith WooCommerce Coupon Email System vendor panel page
		 */
		protected $_panel_page = 'yith_vendor_ces_settings';

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->vendor         = yith_get_vendor( 'current', 'user' );
			$this->active_vendors = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );

			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() && $this->vendors_coupon_active() && $this->check_active_coupon_events() ) {

				add_action( 'admin_menu', array( $this, 'add_ywces_vendor' ), 5 );

			}

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
			add_action( 'woocommerce_created_customer', array( $this, 'ywces_user_registration_vendor' ), 11, 2 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'ywces_user_purchase_vendor' ), 10, 3 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'admin_notices', array( $this, 'check_active_options' ) );
			add_action( 'ywces_daily_send_mail_job', array( $this, 'ywces_daily_send_mail_job_vendors' ) );
			add_action( 'admin_notices', array( $this, 'ywces_daily_send_mail_job_vendors' ) );

			add_filter( 'ywces_get_vendor_name', array( $this, 'get_vendor_name' ), 10, 2 );
			add_filter( 'ywces_set_vendor_id', array( $this, 'set_vendor_id' ), 10, 3 );
			add_filter( 'ywces_set_coupon_author', array( $this, 'set_vendor_owner' ), 10, 2 );
			add_filter( 'ywces_multivendor_coupon_active_notice', array( $this, 'coupon_active_notice' ), 10, 1 );

		}

		/**
		 * Add Coupon Email System panel for vendors
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_ywces_vendor() {

			if ( ! empty( $this->_vendor_panel ) ) {
				return;
			}

			$tabs = array(
				'vendor' => esc_html__( 'Settings', 'yith-woocommerce-coupon-email-system' ),
				'howto'  => esc_html__( 'How To', 'yith-woocommerce-coupon-email-system' )
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => esc_html__( 'Coupon Email System', 'yith-woocommerce-coupon-email-system' ),
				'menu_title'       => 'Coupon Email System',
				'capability'       => 'manage_vendor_store',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWCES_DIR . 'plugin-options/vendor',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99
			);

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @since   1.0.5
		 *
		 * @param   $screen_ids
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = $this->_panel_page;

			return $screen_ids;

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] == $this->_panel_page ) ) {

				wp_register_style( 'yit-plugin-style', YIT_CORE_PLUGIN_URL . '/assets/css/yit-plugin-panel.css' );
				wp_enqueue_style( 'yit-plugin-style' );
				wp_enqueue_script( 'selectWoo' );
				wp_enqueue_script( 'wc-enhanced-select' );

			}

		}

		/**
		 * Check if active options have a coupon assigned
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function check_active_options() {

			$messages = array();

			if ( isset( $_POST[ 'ywces_enable_register_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_register_' . $this->vendor->id ] ) {

				if ( $_POST[ 'ywces_coupon_register_' . $this->vendor->id ] == '' ) {

					$messages[] = esc_html__( 'You need to select a coupon to send one for a new user registration.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST[ 'ywces_enable_first_purchase_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_first_purchase_' . $this->vendor->id ] ) {

				if ( $_POST[ 'ywces_coupon_first_purchase_' . $this->vendor->id ] == '' ) {

					$messages[] = esc_html__( 'You need to select a coupon to send one for a new first purchase.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST[ 'ywces_enable_purchases_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_purchases_' . $this->vendor->id ] ) {

				if ( ! isset( $_POST[ 'ywces_thresholds_purchases_' . $this->vendor->id ] ) ) {

					$messages[] = esc_html__( 'You need to set a threshold to send a coupon once a user reaches a specific number of purchases.', 'yith-woocommerce-coupon-email-system' );

					update_option( 'ywces_thresholds_purchases_' . $this->vendor->id, '' );

				} else {

					$count = 0;

					foreach ( maybe_unserialize( $_POST[ 'ywces_thresholds_purchases_' . $this->vendor->id ] ) as $threshold ) {

						if ( $threshold['coupon'] == '' ) {

							$count ++;

						}

					}

					if ( $count > 0 ) {

						$messages[] = esc_html__( 'You need to set a coupon for each threshold to send one when users reach a specific number of purchases.', 'yith-woocommerce-coupon-email-system' );

					}

				}

			}

			if ( isset( $_POST[ 'ywces_enable_spending_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_spending_' . $this->vendor->id ] ) {

				if ( ! isset( $_POST[ 'ywces_thresholds_spending_' . $this->vendor->id ] ) ) {

					$messages[] = esc_html__( 'You need to set a threshold to send a coupon once a user reaches a specific spent amount.', 'yith-woocommerce-coupon-email-system' );

					update_option( 'ywces_thresholds_spending_' . $this->vendor->id, '' );

				} else {

					$count = 0;

					foreach ( maybe_unserialize( $_POST[ 'ywces_thresholds_spending_' . $this->vendor->id ] ) as $threshold ) {

						if ( $threshold['coupon'] == '' ) {

							$count ++;

						}

					}

					if ( $count > 0 ) {

						$messages[] = esc_html__( 'You need to set a coupon for each threshold to send one when users reach a specific spent amount.', 'yith-woocommerce-coupon-email-system' );

					}

				}

			}

			if ( isset( $_POST[ 'ywces_enable_product_purchasing_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_product_purchasing_' . $this->vendor->id ] ) {

				if ( ! isset( $_POST[ 'ywces_targets_product_purchasing_' . $this->vendor->id ] ) || $_POST[ 'ywces_targets_product_purchasing_' . $this->vendor->id ] == '' ) {

					$messages[] = esc_html__( 'You need to select at least one product to send a coupon once purchased.', 'yith-woocommerce-coupon-email-system' );

				}

				$coupon = maybe_unserialize( $_POST[ 'ywces_coupon_product_purchasing_' . $this->vendor->id ] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it for the purchase of a specific product.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST[ 'ywces_enable_birthday_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_birthday_' . $this->vendor->id ] ) {

				$coupon = maybe_unserialize( $_POST[ 'ywces_coupon_birthday_' . $this->vendor->id ] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it for the birthday of a user.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( isset( $_POST[ 'ywces_enable_last_purchase_' . $this->vendor->id ] ) && '1' == $_POST[ 'ywces_enable_last_purchase_' . $this->vendor->id ] ) {

				$coupon = maybe_unserialize( $_POST[ 'ywces_coupon_birthday_' . $this->vendor->id ] );

				if ( $coupon['coupon_amount'] == '' ) {

					$messages[] = esc_html__( 'You need to select at least the amount/percentage of a coupon to send it after a specific number of days following the last order.', 'yith-woocommerce-coupon-email-system' );

				}

			}

			if ( ! empty( $messages ) ) :

				?>
                <div class="error">
                    <ul>
						<?php foreach ( $messages as $message ): ?>

                            <li><?php echo $message ?></li>

						<?php endforeach; ?>
                    </ul>
                </div>
			<?php

			endif;

		}

		/**
		 * Check if there is at least a coupon event allowed
		 *
		 * @since   1.0.5
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function check_active_coupon_events() {

			if ( get_option( 'ywces_enable_register_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_first_purchase_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_purchases_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_spending_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_product_purchasing_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_birthday_vendor' ) == 'yes' ) {
				return true;
			}

			if ( get_option( 'ywces_enable_last_purchase_vendor' ) == 'yes' ) {
				return true;
			}

			return false;

		}

		/**
		 * Get vendor's name
		 *
		 * @since   1.0.5
		 *
		 * @param   $value
		 * @param   $vendor_id
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_name( $value, $vendor_id ) {

			$vendor = yith_get_vendor( $vendor_id, 'vendor' );

			if ( $vendor->is_valid() ) {

				$value = $vendor->term->name;

			}

			return $value;

		}

		/**
		 * Get vendor's name
		 *
		 * @since   1.0.5
		 *
		 * @param   $value
		 * @param   $vendor_id
		 * @param   $label
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function set_vendor_id( $value, $vendor_id, $label = false ) {

			if ( $vendor_id != '' ) {

				$value = '_';

				if ( $label ) {
					$value .= 'vendor_';
				}

				$value .= $vendor_id;

			}

			return $value;

		}

		/**
		 * Check if there is at least a coupon event allowed
		 *
		 * @since   1.0.5
		 *
		 * @param   $vendor_id
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_coupons( $vendor_id = false ) {

			if ( $vendor_id ) {

				$vendor = yith_get_vendor( $vendor_id, 'vendor' );

			} else {

				$vendor = yith_get_vendor( 'current', 'user' );

			}

			$posts = get_posts(
				array(
					'post_type'   => 'shop_coupon',
					'post_status' => 'publish',
					'numberposts' => - 1,
					'author__in'  => $vendor->admins,
				)
			);

			$array = array();

			foreach ( $posts as $post ) {

				$array[ $post->post_title ] = $post->post_title;

			}

			return $array;

		}

		/**
		 * Set coupon owner
		 *
		 * @since   1.0.5
		 *
		 * @param   $value
		 * @param   $vendor_id
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function set_vendor_owner( $value, $vendor_id ) {

			$vendor = yith_get_vendor( $vendor_id, 'vendor' );

			if ( $vendor->is_valid() ) {

				$value = $vendor->get_owner();

			}

			return $value;

		}

		/**
		 * Check if YITH WooCommerce Multi Vendor and coupon management of YITH WooCommerce Multi Vendor is active to show notifications
		 *
		 * @since   1.0.5
		 *
		 * @param   $value
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function coupon_active_notice( $value = true ) {

			if ( $this->vendor->is_valid() && $this->vendors_coupon_active() ) {

				if ( ! $this->check_active_coupon_events() ) {
					$value = false;
				}

			} else {
				$value = false;
			}

			return $value;

		}

		/**
		 * Check if coupon management of YITH WooCommerce Multi Vendor is active
		 *
		 * @since   1.0.5
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function vendors_coupon_active() {

			return ( get_option( 'yith_wpv_vendors_option_coupon_management' ) == 'yes' ? true : false );

		}

		/**
		 * VENDORS COUPON METHODS
		 */

		/**
		 * Trigger coupon on user registration
		 *
		 * @since   1.0.0
		 *
		 * @param    $customer_id
		 * @param    $new_customer_data
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function ywces_user_registration_vendor( $customer_id, $new_customer_data ) {

			if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
				return;
			}

			foreach ( $this->active_vendors as $curr_vendor ) {

				$coupon_code = get_option( 'ywces_coupon_register_' . $curr_vendor->id );

				if ( get_option( 'ywces_enable_register_' . $curr_vendor->id ) == 'yes' && count( $this->get_vendor_coupons( $curr_vendor->id ) ) > 0 && YITH_WCES()->check_if_coupon_exists( $coupon_code ) && get_option( 'ywces_enable_register_vendor' ) == 'yes' ) {

					YITH_WCES()->bind_coupon( $coupon_code, $new_customer_data['user_email'] );

					$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'register', $coupon_code, array(), false, false, $curr_vendor->id );

					if ( ! $email_result ) {
						YITH_WCES()->write_log( array(
							                        'coupon_code' => $coupon_code,
							                        'type'        => 'register'
						                        ) );
					}

				}

			}

		}

		/**
		 * Trigger coupons on user purchase
		 *
		 * @since   1.0.0
		 *
		 * @param   $parent_order_id
		 * @param   $old_status
		 * @param   $new_status
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function ywces_user_purchase_vendor( $parent_order_id, $old_status, $new_status ) {

			if ( $new_status != 'completed' ) {
				return;
			}

			$parent_order = wc_get_order( $parent_order_id );
			$suborders    = YITH_Orders::get_suborder( $parent_order_id );
			foreach ( $suborders as $suborder_id ) {
				$vendor      = yith_get_vendor( get_post_field( 'post_author', $suborder_id ), 'user' );
				$customer_id = yit_get_prop( $parent_order, 'customer_user' );
				$order_count = ywces_order_count( $customer_id, get_post_field( 'post_author', $suborder_id ) );
				$money_spent = ywces_total_spent( $customer_id, get_post_field( 'post_author', $suborder_id ) );

				$this->process_vendor_coupon( $vendor->id, $parent_order, $customer_id, $order_count, $money_spent );

			}

		}

		/**
		 * Process coupon for each vendor
		 *
		 * @since   1.0.5
		 *
		 * @param   $vendor_id
		 * @param   $order
		 * @param   $customer_id
		 * @param   $order_count
		 * @param   $money_spent
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function process_vendor_coupon( $vendor_id, WC_Order $order, $customer_id, $order_count, $money_spent ) {

			if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
				return;
			}

			//Set the user to receive again a coupon after XX days from his last purchase
			update_user_meta( $customer_id, '_last_purchase_coupon_sent_vendor_' . $vendor_id, 'no' );

			$order_date    = date( 'Y-m-d', yit_datetime_to_timestamp( yit_get_prop( $order, 'order_date' ) ) );
			$billing_email = yit_get_prop( $order, 'billing_email' );

			if ( count( $this->get_vendor_coupons( $vendor_id ) ) > 0 ) {

				//Check if is user first purchase
				if ( get_option( 'ywces_enable_first_purchase_' . $vendor_id ) == 'yes' && get_option( 'ywces_enable_first_purchase_vendor' ) == 'yes' ) {

					if ( $order_count == 1 ) {

						$coupon_code = get_option( 'ywces_coupon_first_purchase_' . $vendor_id );

						if ( YITH_WCES()->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
							);

							YITH_WCES()->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'first_purchase', $coupon_code, $args, false, false, $vendor_id );

							if ( ! $email_result ) {
								YITH_WCES()->write_log( array(
									                        'coupon_code' => $coupon_code,
									                        'type'        => 'first_purchase'
								                        ) );
							}
						}

						return;

					}

				}

				//check if uses has reached an order threshold
				if ( get_option( 'ywces_enable_purchases_' . $vendor_id ) == 'yes' && get_option( 'ywces_enable_purchases_vendor' ) == 'yes' ) {

					$purchase_threshold = YITH_WCES()->check_threshold( $order_count, 'purchases', $customer_id, $vendor_id );

					if ( ! empty( $purchase_threshold ) ) {

						$coupon_code = $purchase_threshold['coupon_id'];

						if ( YITH_WCES()->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
								'threshold'  => $purchase_threshold['threshold'],
							);

							YITH_WCES()->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'purchases', $coupon_code, $args, false, false, $vendor_id );

							if ( ! $email_result ) {
								YITH_WCES()->write_log( array(
									                        'coupon_code' => $coupon_code,
									                        'type'        => 'purchases'
								                        ) );
							}

						}

						return;

					}

				}

				//check if uses has reached a spending threshold
				if ( get_option( 'ywces_enable_spending_' . $vendor_id ) == 'yes' && get_option( 'ywces_enable_spending_vendor' ) == 'yes' ) {

					$spending_threshold = YITH_WCES()->check_threshold( $money_spent, 'spending', $customer_id, $vendor_id );

					if ( ! empty( $spending_threshold ) ) {

						$coupon_code = $spending_threshold['coupon_id'];

						if ( YITH_WCES()->check_if_coupon_exists( $coupon_code ) ) {

							$args = array(
								'order_date' => $order_date,
								'threshold'  => $spending_threshold['threshold'],
								'expense'    => $money_spent,
							);

							YITH_WCES()->bind_coupon( $coupon_code, $billing_email );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'spending', $coupon_code, $args, false, false, $vendor_id );

							if ( ! $email_result ) {
								YITH_WCES()->write_log( array(
									                        'coupon_code' => $coupon_code,
									                        'type'        => 'spending'
								                        ) );
							}

						}

						return;

					}

				}

			}

			if ( get_option( 'ywces_enable_product_purchasing_' . $vendor_id ) == 'yes' && get_option( 'ywces_targets_product_purchasing_' . $vendor_id ) != '' && get_option( 'ywces_enable_product_purchasing_vendor' ) == 'yes' ) {

				$is_deposits = yit_get_prop( $order, '_created_via' ) == 'yith_wcdp_balance_order';

				if ( ! $is_deposits ) {

					$target_products = get_option( 'ywces_targets_product_purchasing_' . $vendor_id );
					$target_products = is_array( $target_products ) ? $target_products : explode( ',', $target_products );
					$order_items     = $order->get_items();
					$found_product   = '';
					foreach ( $order_items as $item ) {

						//$product_id = ( $item['variation_id'] != '0' ? $item['variation_id'] : $item['product_id'] );
						$product_id = $item['product_id'];

						if ( in_array( $product_id, $target_products ) && $found_product == '' ) {

							$found_product = $product_id;
						}
					}

					if ( $found_product != '' ) {

						$coupon_code = YITH_WCES()->create_coupon( $customer_id, 'product_purchasing', array(), $vendor_id );
						$args        = array(
							'order_date' => $order_date,
							'product'    => $found_product,
						);

						$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'product_purchasing', $coupon_code, $args, false, false, $vendor_id );

						if ( ! $email_result ) {
							YITH_WCES()->write_log( array(
								                        'coupon_code' => $coupon_code,
								                        'type'        => 'product_purchasing'
							                        ) );
						}

					}

				}

			}

		}

		/**
		 * Daily cron job for each vendor
		 *
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function ywces_daily_send_mail_job_vendors() {

			foreach ( $this->active_vendors as $vendor ) {

				if ( get_option( 'ywces_enable_last_purchase_' . $vendor->id ) == 'yes' && get_option( 'ywces_enable_last_purchase_vendor' ) == 'yes' ) {

					$users = YITH_WCES()->get_customers_id_by_last_purchase( $vendor->id );

					if ( ! empty( $users ) ) {

						foreach ( $users as $customer_id ) {

							if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
								continue;
							}

							$coupon_code = YITH_WCES()->create_coupon( $customer_id, 'last_purchase', array(), $vendor->id );

							$args = array(
								'days_ago' => get_option( 'ywces_days_last_purchase_' . $vendor->id )
							);

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'last_purchase', $coupon_code, $args, false, false, $vendor->id );

							if ( ! $email_result ) {
								YITH_WCES()->write_log( array(
									                        'coupon_code' => $coupon_code,
									                        'type'        => 'last_purchase'
								                        ) );
							} else {
								//Set the user to not receive another coupon until he does a new purchase
								update_user_meta( $customer_id, '_last_purchase_coupon_sent_vendor_' . $vendor->id, 'yes' );

							}
						}

					}

				}

				if ( get_option( 'ywces_enable_birthday_' . $vendor->id ) == 'yes' && get_option( 'ywces_enable_birthday_vendor' ) == 'yes' ) {

					$users = YITH_WCES()->get_customers_id_by_birthdate();

					if ( ! empty( $users ) ) {

						foreach ( $users as $customer_id ) {

							if ( get_option( 'ywces_refuse_coupon' ) == 'yes' && get_user_meta( $customer_id, 'ywces_receive_coupons', true ) == 'yes' ) {
								continue;
							}

							$coupon_code = YITH_WCES()->create_coupon( $customer_id, 'birthday', array(), $vendor->id );

							$email_result = YWCES_Emails()->prepare_coupon_mail( $customer_id, 'birthday', $coupon_code, array(), false, false, $vendor->id );

							if ( ! $email_result ) {
								YITH_WCES()->write_log( array(
									                        'coupon_code' => $coupon_code,
									                        'type'        => 'birthday'
								                        ) );
							}

						}

					}

				}

			}


		}

	}

	/**
	 * Unique access to instance of YWCES_MultiVendor class
	 *
	 * @return \YWCES_MultiVendor
	 */
	function YWCES_MultiVendor() {

		return YWCES_MultiVendor::get_instance();

	}

	YWCES_MultiVendor();

}