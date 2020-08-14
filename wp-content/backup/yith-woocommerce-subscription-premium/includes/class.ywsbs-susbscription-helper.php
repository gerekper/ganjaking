<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription_Helper Class
 *
 * @class   YWSBS_Subscription_Helper
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YWSBS_Subscription_Helper' ) ) {

	/**
	 * Class YWSBS_Subscription_Helper
	 */
	class YWSBS_Subscription_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSBS_Subscription_Helper
		 */

		protected static $instance;


		protected $saved_metabox = false;

		/**
		 * Returns single instance of the class
		 *
		 * @access public
		 *
		 * @return \YWSBS_Subscription_Helper
		 * @since  1.0.0
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
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */

		public function __construct() {

			add_action( 'init', array( $this, 'register_subscription_post_type' ) );

			// Add Capabilities to Administrator and Shop Manager
			add_action( 'admin_init', array( $this, 'add_subscription_capabilities' ) );

			add_action( 'add_meta_boxes', array( $this, 'show_info_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_action_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_activity_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_product_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_schedule_subscription' ), 10, 2 );

			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'format_date_fields' ) );
			add_action( 'save_post', array( $this, 'before_data_saving' ), 0, 2 );
			add_action( 'admin_init', array( $this, 'remove_meta_fix' ) );

			add_action( 'ywsbs_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );

		}

		function remove_meta_fix() {
			if ( isset( $_POST['meta'] ) && isset( $_POST['post_type'] ) && YITH_WC_Subscription()->post_name == $_POST['post_type'] ) {
				unset( $_POST['meta'] );
			}
		}

		/**
		 * Register ywsbs_subscription post type
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */

		public function register_subscription_post_type() {

			$supports = false;

			if ( apply_filters( 'ywsbs_test_on', YITH_YWSBS_TEST_ON ) ) {
				$supports = array( 'custom-fields' );
			}

			$labels = array(
				'name'               => _x( 'Subscriptions', 'Post Type General Name', 'yith-woocommerce-subscription' ),
				'singular_name'      => _x( 'Subscription', 'Post Type Singular Name', 'yith-woocommerce-subscription' ),
				'menu_name'          => __( 'Subscription', 'yith-woocommerce-subscription' ),
				'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-subscription' ),
				'all_items'          => __( 'All Subscriptions', 'yith-woocommerce-subscription' ),
				'view_item'          => __( 'View Subscriptions', 'yith-woocommerce-subscription' ),
				'add_new_item'       => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'add_new'            => __( 'Add New Subscription', 'yith-woocommerce-subscription' ),
				'edit_item'          => __( 'Subscription', 'yith-woocommerce-subscription' ),
				'update_item'        => __( 'Update Subscription', 'yith-woocommerce-subscription' ),
				'search_items'       => __( 'Search Subscription', 'yith-woocommerce-subscription' ),
				'not_found'          => __( 'Not found', 'yith-woocommerce-subscription' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-subscription' ),
			);

			$args = array(
				'label'               => __( 'ywsbs_subscription', 'yith-woocommerce-subscription' ),
				'labels'              => $labels,
				'supports'            => $supports,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'ywsbs_sub',
				'capabilities'        => array(
					'create_posts'       => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
					'edit_post'          => 'edit_ywsbs_sub',
					'edit_others_post'   => 'edit_others_ywsbs_subs',
					'delete_post'        => 'delete_ywsbs_sub',
					'delete_others_post' => 'delete_others_ywsbs_subs',
				),
				'map_meta_cap'        => false,
			);

			register_post_type( 'ywsbs_subscription', $args );

			do_action( 'ywsbs_after_register_post_type' );

		}


		/**
		 * Add subscription management capabilities to Admin and Shop Manager
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function add_subscription_capabilities() {

			$capability_type = 'plan';
			$caps            = array(
				'edit_post'          => 'edit_ywsbs_sub',
				'edit_others_post'   => 'edit_others_ywsbs_subs',
				'delete_post'        => 'delete_ywsbs_sub',
				'delete_others_post' => 'delete_others_ywsbs_subs',
			);

			// gets the admin and shop_mamager roles
			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );

			foreach ( $caps as $key => $cap ) {
				$admin && $admin->add_cap( $cap );
				$shop_manager && $shop_manager->add_cap( $cap );
			}
		}


		/**
		 *
		 */
		public function add_metabox() {
			$args = require_once YITH_YWSBS_DIR . 'plugin-options/metabox/ywsbs_metabox.php';
			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once 'plugin-fw/yit-plugin.php';
			}
			$metabox = YIT_Metabox( 'yit-subscription-updates' );
			$metabox->init( $args );
		}

		/**
		 * Format Timestamps into dates
		 *
		 * @access public
		 * @param $args
		 *
		 * @return mixed
		 * @since  1.0.0
		 *
		 */
		public function format_date_fields( $args ) {

			$date_fields = apply_filters( 'ywsbs_date_fields', array( 'start_date', 'payment_due_date', 'expired_date', 'cancelled_date', 'end_date' ) );

			if ( in_array( $args['args']['args']['id'], $date_fields ) ) {

				$args['args']['args']['value'] = ( $time_stamp = $args['args']['args']['value'] ) ? date_i18n( 'Y-m-d', $time_stamp ) : '';

			}

			return $args;

		}


		/**
		 * Save meta data
		 *
		 * @param $post_id
		 *
		 * @param $post
		 *
		 * @return mixed
		 * @throws Exception
		 */
		public function before_data_saving( $post_id, $post ) {

			if ( $post->post_type != YITH_WC_Subscription()->post_name || $this->saved_metabox ) {
				return;
			}

			// Dont' save meta boxes for revisions or autosaves
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Check user has permission to edit
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $post_id );

			// schedule save option ___________________________________________________________________________________//
			if ( isset( $_POST['ywsbs_safe_submit_field'] ) && 'schedule_subscription' == $_POST['ywsbs_safe_submit_field'] ) {

				// Recurring period
				if ( isset( $_REQUEST['ywsbs_price_is_per'] ) && isset( $_REQUEST['ywsbs_price_time_option'] ) &&
					( $_REQUEST['ywsbs_price_is_per'] != $subscription->price_is_per || $_REQUEST['ywsbs_price_time_option'] != $subscription->price_time_option ) ) {
					$recurring_old = ywsbs_get_price_per_string( $subscription->price_is_per, $subscription->price_time_option );
					$subscription->set( 'price_is_per', intval( $_REQUEST['ywsbs_price_is_per'] ) );
					$subscription->set( 'price_time_option', $_REQUEST['ywsbs_price_time_option'] );
					$recurring_new = ywsbs_get_price_per_string( $subscription->price_is_per, $subscription->price_time_option );
					YITH_WC_Activity()->add_activity( $post_id, 'changed', $status = 'success', $order = 0, sprintf( __( 'Changed recurring period from: %1$s to %2$s', 'yith-woocommerce-subscription' ), $recurring_old, $recurring_new ) );
				}

				// Schedule fields
				$schedule_fields = $this->get_schedule_data_subscription_fields();
				foreach ( $schedule_fields as $key => $value ) {
					$field_name = 'ywsbs_' . $key;
					$new_value  = isset( $_REQUEST[ $field_name ] ) ? strtotime( $_REQUEST[ $field_name ] ) : '';
					$old_value  = $subscription->$key;
					if ( $subscription->$key != $new_value ) {
						$subscription->set( $key, $new_value );
						$new_value_formatted = date_i18n( wc_date_format(), $new_value ) . ' ' . date_i18n( __( wc_time_format() ), $new_value );
						$old_value_formatted = date_i18n( wc_date_format(), $old_value ) . ' ' . date_i18n( __( wc_time_format() ), $old_value );
						YITH_WC_Activity()->add_activity( $post_id, 'changed', $status = 'success', $order = 0, sprintf( __( 'Changed %1$s from: %2$s to %3$s', 'yith-woocommerce-subscription' ), $value, $old_value_formatted, $new_value_formatted ) );
					}
				}
			}

			// Save Billing and Shipping Meta if different from parent order __________________________________________//
			$meta                = $meta_billing = $meta_shipping = array();
			$billing_fields      = ywsbs_get_order_fields_to_edit( 'billing' );
			$billing_order_meta  = $subscription->get_address_fields_from_order( 'billing', false, '_' );
			$shipping_fields     = ywsbs_get_order_fields_to_edit( 'shipping' );
			$shipping_order_meta = $subscription->get_address_fields_from_order( 'shipping', false, '_' );

			foreach ( $billing_fields as $key => $billing_field ) {
				$field_id = '_billing_' . $key;
				if ( ! isset( $_POST[ $field_id ] ) ) {
					continue;
				}
				$meta[ $field_id ] = $_POST[ $field_id ];
			}

			foreach ( $shipping_fields as $key => $shipping_field ) {
				$field_id = '_shipping_' . $key;
				if ( ! isset( $_POST[ $field_id ] ) ) {
					continue;
				}
				$meta[ $field_id ] = $_POST[ $field_id ];
			}

			if ( isset( $_POST['customer_note'] ) ) {
				$meta['customer_note'] = $_POST['customer_note'];
			}

			$meta && $subscription->update_subscription_meta( $meta );

			if ( isset( $_POST['ywsbs_subscription_actions'] ) && $_POST['ywsbs_subscription_actions'] != '' ) {

				if ( $_POST['ywsbs_subscription_actions'] == 'renew-order' ) {
					YWSBS_Subscription_Order()->renew_order( $post_id );
				} else {
					$subscription = ywsbs_get_subscription( $post_id );
					$new_status   = $_POST['ywsbs_subscription_actions'];
					YITH_WC_Subscription()->manual_change_status( $new_status, $subscription, 'administrator' );
				}
			}

			$this->saved_metabox = true;

		}

		/**
		 * Add the metabox to show the info of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_info_subscription() {
			add_meta_box(
				'ywsbs-info-subscription',
				__( 'Subscription Info', 'yith-woocommerce-subscription' ),
				array(
					$this,
					'show_subscription_info_metabox',
				),
				YITH_WC_Subscription()->post_name,
				'normal',
				'high'
			);
		}


		/**
		 * Add the metabox to show the info of subscription
		 *
		 * @access public
		 *
		 * @oaram  YWSBS_Subscription
		 * @return void
		 * @since  1.0.0
		 */

		public function show_action_subscription() {
			add_meta_box(
				'ywsbs-action-subscription',
				__( 'Subscription Action', 'yith-woocommerce-subscription' ),
				array(
					$this,
					'show_subscription_action_metabox',
				),
				YITH_WC_Subscription()->post_name,
				'side',
				'high'
			);
		}

		/**
		 * Add the metabox to show the activities of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_activity_subscription() {
			add_meta_box(
				'ywsbs-activity-subscription',
				__( 'Subscription Activities', 'yith-woocommerce-subscription' ),
				array(
					$this,
					'show_subscription_activity_metabox',
				),
				YITH_WC_Subscription()->post_name,
				'side',
				'high'
			);
		}


		/**
		 * Add the metabox to show the product of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_product_subscription() {
			add_meta_box(
				'ywsbs-product-subscription',
				__( 'Subscription Product', 'yith-woocommerce-subscription' ),
				array(
					$this,
					'show_subscription_product_metabox',
				),
				YITH_WC_Subscription()->post_name,
				'normal',
				'high'
			);
		}

		/**
		 * Add the metabox to show the product of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_schedule_subscription( $post_type, $post ) {

			if ( 'ywsbs_subscription' != $post_type ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $post->ID );

			if ( ! $subscription || ! $subscription->can_be_editable( 'payment_date' ) ) {
				return;
			}

			add_meta_box(
				'ywsbs-schedule-subscription',
				__( 'Subscription Schedule', 'yith-woocommerce-subscription' ),
				array(
					$this,
					'show_subscription_schedule_metabox',
				),
				YITH_WC_Subscription()->post_name,
				'normal',
				'high'
			);
		}

		/**
		 * Remove publish box from single page page of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', YITH_WC_Subscription()->post_name, 'side' );
		}


		/**
		 * Metabox to show the info of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_subscription_info_metabox( $post ) {

			$subscription = new YWSBS_Subscription( $post->ID );
			$args         = array(
				'subscription' => $subscription,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_info_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}


		/**
		 * Metabox to show the action of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_subscription_action_metabox( $post ) {

			$subscription = new YWSBS_Subscription( $post->ID );
			$args         = array(
				'subscription' => $subscription,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_action_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}


		/**
		 * Metabox to show the activities of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post
		 *
		 * @return void
		 * @since  1.0.0
		 */

		public function show_subscription_activity_metabox( $post ) {

			$subscription = new YWSBS_Subscription( $post->ID );
			$args         = array(
				'subscription' => $subscription,
				'activities'   => YITH_WC_Activity()->get_activity_by_subscription( $post->ID ),
			);
			wc_get_template( 'admin/metabox/metabox_subscription_activity_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}


		/**
		 * Get all subscriptions of a user
		 *
		 * @access public
		 *
		 * @param int $user_id
		 *
		 * @return array
		 * @since  1.0.0
		 */

		public function get_subscriptions_by_user( $user_id ) {
			$subscriptions = get_posts(
				array(
					'post_type'      => YITH_WC_Subscription()->post_name,
					'posts_per_page' => -1,
					'meta_key'       => 'user_id',
					'meta_value'     => $user_id,
				)
			);

			return $subscriptions;
		}


		/**
		 * Metabox to show the product detail of the current subscription
		 *
		 * @access public
		 *
		 * @param $post object
		 *
		 * @return void
		 *
		 * @since  1.0.0
		 */

		public function show_subscription_product_metabox( $post ) {

			$subscription = ywsbs_get_subscription( $post->ID );
			$product      = wc_get_product( ( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id );
			$args         = array(
				'product'      => $product,
				'subscription' => $subscription,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_product.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Metabox to show the product detail of the current subscription
		 *
		 * @access public
		 *
		 * @param $post object
		 *
		 * @return void
		 *
		 * @since  1.0.0
		 */

		public function show_subscription_schedule_metabox( $post ) {

			$subscription = ywsbs_get_subscription( $post->ID );
			$args         = array(
				'fields'       => $this->get_schedule_data_subscription_fields(),
				'subscription' => $subscription,
				'time_format'  => apply_filters( 'ywsbs_time_format', 'Y-m-d H:i:s' ),
			);
			wc_get_template( 'admin/metabox/metabox_subscription_schedule.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}


		/**
		 * @return mixed|void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_schedule_data_subscription_fields() {
			$fields = array(
				'start_date'       => __( 'Start Date', 'yith-woocommerce-subscription' ),
				'payment_due_date' => __( 'Payment Due Date', 'yith-woocommerce-subscription' ),
				'expired_date'     => __( 'Expired date', 'yith-woocommerce-subscription' ),
			);

			return apply_filters( 'ywsbs_schedule_data_subscription_fields', $fields );
		}

		/**
		 * Change the status of the subscription by administrator
		 *
		 * @access public
		 *
		 * @param int $post_id
		 *
		 * @return int | void
		 * @since  1.0.0
		 */

		public function save_postdata( $post_id ) {

			remove_action( 'save_post', array( $this, 'save_postdata' ) );
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				return $post_id;
			}

			if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != YITH_WC_Subscription()->post_name ) {
				return $post_id;
			}

			if ( isset( $_POST['ywsbs_subscription_actions'] ) && $_POST['ywsbs_subscription_actions'] != '' ) {

				if ( $_POST['ywsbs_subscription_actions'] == 'renew-order' ) {
					YWSBS_Subscription_Order()->renew_order( $post_id );
				} else {
					$subscription = ywsbs_get_subscription( $post_id );
					$new_status   = $_POST['ywsbs_subscription_actions'];
					YITH_WC_Subscription()->manual_change_status( $new_status, $subscription, 'administrator' );
				}
			}
			add_action( 'save_post', array( $this, 'save_postdata' ) );
		}

		/**
		 * Flush rules if the event is queued.
		 *
		 * @since 2.0.0
		 */
		public static function maybe_flush_rewrite_rules() {
			if ( ! get_option( 'ywsbs_queue_flush_rewrite_rules' ) ) {
				update_option( 'ywsbs_queue_flush_rewrite_rules', 'yes' );
				flush_rewrite_rules();
			}
		}

	}

}


/**
 * Unique access to instance of YWSBS_Subscription class
 *
 * @return \YWSBS_Subscription_Helper
 */
function YWSBS_Subscription_Helper() {
	return YWSBS_Subscription_Helper::get_instance();
}
