<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/Admin
 * @author    WooThemes
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre-Orders Admin Pre Orders class.
 */
class WC_Pre_Orders_Admin_Pre_Orders {

	/**
	 * The pre-orders list table object.
	 *
	 * @var WC_Pre_Orders_List_Table
	 */
	private $pre_orders_list_table;

	/**
	 * Mensage transient prefix.
	 *
	 * @var string
	 */
	private $message_transient_prefix = '_wc_pre_orders_messages_';

	/**
	 * Initialize the admin settings actions.
	 */
	public function __construct() {
		// Add 'Pre-Orders' link under WooCommerce menu.
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// Pre-Orders list table settings
		add_action( 'in_admin_header',   array( $this, 'load_pre_orders_list_table' ) );
		add_filter( 'set-screen-option', array( $this, 'set_pre_orders_list_option' ), 10, 3 );
	}

	/**
	 * Get pre-orders tabs.
	 *
	 * @return array
	 */
	protected function get_tabs() {
		return array(
			'manage'  => __( 'Manage', 'wc-pre-orders' ),
			'actions' => __( 'Actions', 'wc-pre-orders' ),
		);
	}

	/**
	 * Add 'Pre-Orders' sub-menu link under 'WooCommerce' top level menu.
	 */
	public function add_menu_link() {

		$hook = add_submenu_page(
			'woocommerce',
			__( 'Pre-Orders', 'wc-pre-orders' ),
			__( 'Pre-Orders', 'wc-pre-orders' ),
			'manage_woocommerce',
			'wc_pre_orders',
			array( $this, 'show_sub_menu_page' )
		);

		// add the Pre-Orders list Screen Options
		add_action( 'load-woocommerce_page_wc_pre_orders', array( $this, 'add_pre_orders_list_options' ) );
		add_action( 'load-' . $hook, array( $this, 'process_actions' ) );
	}

	/**
	 * Show Pre-Orders Manage/Actions page content.
	 */
	public function show_sub_menu_page() {

		$current_tab = ( empty( $_GET['tab'] ) ) ? 'manage' : urldecode( $_GET['tab'] );

		echo '<div class="wrap woocommerce">';
		echo '<div id="icon-woocommerce" class="icon32-woocommerce-users icon32"><br /></div>';
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';

		// Display tabs.
		foreach ( $this->get_tabs() as $tab_id => $tab_title ) {

			$class = ( $tab_id == $current_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
			$url   = add_query_arg( 'tab', $tab_id, admin_url( 'admin.php?page=wc_pre_orders' ) );

			printf( '<a href="%s" class="%s">%s</a>', $url, $class, $tab_title );
		}

		echo '</h2>';

		// Show any messages.
		if ( ! empty( $_GET['success'] ) ) {

			switch ( $_GET['success'] )  {

				case 'email':
					$message = __( 'Pre-Order customers emailed successfully', 'wc-pre-orders' );
					break;

				case 'change-date':
					$message = __( 'Pre-Order Date Changed', 'wc-pre-orders' );
					break;

				case 'complete':
					$message = __( 'Pre-Orders completed', 'wc-pre-orders' );
					break;

				case 'cancel':
					$message = __( 'Pre-Orders cancelled', 'wc-pre-orders' );
					break;

				default:
					$message = '';
					break;
			}

			if ( $message ) {
				echo '<div id="message" class="updated fade"><p><strong>' . $message . '</strong></p></div>';
			}
		}

		// Display tab content, default to 'Manage' tab.
		if ( 'actions' === $current_tab ) {
			$this->show_actions_tab();
		} else {
			$this->show_manage_tab();
		}

		echo '</div>';
	}

	/**
	 * Add the Pre-Orders list table Screen Options.
	 */
	public function add_pre_orders_list_options() {
		$args = array(
			'label'   => __( 'Pre-Orders', 'wc-pre-orders' ),
			'default' => 20,
			'option'  => 'wc_pre_orders_edit_pre_orders_per_page',
		);

		add_screen_option( 'per_page', $args );
	}

	/**
	 * Processes the cancelling of individual pre-order.
	 *
	 * @since 1.4.6
	 * @version 1.4.7
	 * @return bool
	 */
	public function process_cancel_pre_order_action() {
		if ( empty( $_GET['action'] ) || 'cancel_pre_order' !== $_GET['action'] ) {
			return;
		}

		if ( ! empty( $_GET['cancel_pre_order_nonce'] ) && ! wp_verify_nonce( $_GET['cancel_pre_order_nonce'], 'cancel_pre_order' ) ) {
			wp_die( __( 'Action failed. Please refresh the page and retry.', 'wc-pre-orders' ) );
		}

		// User check.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have the correct permissions to do this.', 'wc-pre-orders' ) );
		}

		$order_id = absint( $_GET['order_id'] );

		WC_Pre_Orders_Manager::cancel_pre_order( $order_id );

		$this->_redirect_with_notice( sprintf( __( 'Pre-order #%s cancelled.', 'wc-pre-orders' ), $order_id ) );
	}

	/**
	 * Process the actions from the 'Actions' tab.
	 */
	public function process_actions_tab() {
		global $wc_pre_orders;

		if ( empty( $_POST['wc_pre_orders_action'] ) ) {
			return;
		}

		// Security check.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wc-pre-orders-process-actions' ) ) {
			wp_die( __( 'Action failed. Please refresh the page and retry.', 'wc-pre-orders' ) );
		}

		// User check.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have the correct permissions to do this.', 'wc-pre-orders' ) );
		}

		// Get parameters.
		$action                = ( in_array( $_POST['wc_pre_orders_action'], array( 'email', 'change-date', 'complete', 'cancel' ), true ) ) ? $_POST['wc_pre_orders_action'] : '';
		$product_id            = ( ! empty( $_POST['wc_pre_orders_action_product'] ) ) ? absint( $_POST['wc_pre_orders_action_product'] ) : '';
		$send_email            = ( isset( $_POST['wc_pre_orders_action_enable_email_notification'] ) && '1' === $_POST['wc_pre_orders_action_enable_email_notification'] ) ? true : false;
		$email_message         = ( ! empty( $_POST['wc_pre_orders_action_email_message'] ) ) ? wp_filter_kses( $_POST['wc_pre_orders_action_email_message'] ) : '';
		$new_availability_date = ( ! empty( $_POST['wc_pre_orders_action_new_availability_date'] ) ) ? $_POST['wc_pre_orders_action_new_availability_date'] : '';

		if ( ! $action || ! $product_id ) {
			return;
		}

		switch ( $action ) {

			// Email all pre-ordered customers.
			case 'email':

				WC_Pre_Orders_Manager::email_all_pre_order_customers( $product_id, $email_message );

				break;

			// Change the release date for all pre-orders.
			case 'change-date':

				// Remove email notification if disabled.
				if ( ! $send_email ) {
					remove_action( 'wc_pre_orders_pre_order_date_changed', array( $wc_pre_orders, 'send_transactional_email' ), 10 );
				}

				WC_Pre_Orders_Manager::change_release_date_for_all_pre_orders( $product_id, $new_availability_date, $email_message );

				break;

			// Complete all pre-orders.
			case 'complete' :

				// Remove email notification if disabled.
				if ( ! $send_email ) {
					remove_action( 'wc_pre_order_status_completed', array( $wc_pre_orders, 'send_transactional_email' ), 10 );
				}

				WC_Pre_Orders_Manager::complete_all_pre_orders( $product_id, $email_message );

				break;

			// Cancel all pre-orders.
			case 'cancel' :

				// Remove email notification if disabled.
				if ( ! $send_email ) {
					remove_action( 'wc_pre_order_status_active_to_cancelled', array( $wc_pre_orders, 'send_transactional_email' ), 10 );
				}

				WC_Pre_Orders_Manager::cancel_all_pre_orders( $product_id, $email_message );

				break;

			default :
				break;
		}

		wp_safe_redirect( esc_url_raw( add_query_arg( 'success', $_POST['wc_pre_orders_action'] ) ) );
		exit;
	}

	/**
	 * Process the actions from the 'Manage' tab.
	 */
	public function process_manage_tab() {
		// Get the current action (if any).
		$action = $this->current_action();

		// Cancellation of individual pre-order should be handled by
		// self::process_cancel_pre_order_action.
		if ( 'cancel_pre_order' === $action ) {
			return;
		}

		// Get the set of orders to operate on.
		$order_ids = isset( $_REQUEST['order_id'] ) ? array_map( 'absint', $_REQUEST['order_id'] ): array();

		$message = $this->get_current_customer_message();

		// No action, or invalid action.
		if ( isset( $_GET['page'] ) && 'wc_pre_orders' == $_GET['page'] ) {

			if ( false === $action || empty( $order_ids ) ) {
				if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
					// remove _wp_http_referer/_wp_nonce/action params
					wp_redirect( esc_url_raw( remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) );
					exit;
				}
				return;
			}

			$success_count = $error_count = 0;

			// Process the orders
			foreach ( $order_ids as $order_id ) {

				$order = new WC_Order( $order_id );

				// Perform the action.
				switch ( $action ) {
					case 'cancel':
						if ( WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
							$success_count++;
							WC_Pre_Orders_Manager::cancel_pre_order( $order, $message );
						} else {
							$error_count++;
						}
					break;

					case 'complete':
						if ( WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'completed', $order ) ) {
							$success_count++;
							WC_Pre_Orders_Manager::complete_pre_order( $order, $message );
						} else {
							$error_count++;
						}
					break;

					case 'message':
						WC_Pre_Orders_Manager::email_pre_order_customer( $order_id, $message );
					break;
				}
			}

			$messages = array();

			switch ( $action ) {
				case 'cancel':
					if ( $success_count > 0 ) {
						$messages[] = sprintf( _n( '%d pre-order cancelled.', '%s pre-orders cancelled.', $success_count, 'wc-pre-orders' ), $success_count );
					}
					if ( $error_count > 0 ) {
						$messages[] = sprintf( _n( '%d pre-order could not be cancelled.', '%s pre-orders could not be cancelled.', $error_count, 'wc-pre-orders' ), $error_count );
					}
				break;

				case 'complete':
					if ( $success_count > 0 ) {
						$messages[] = sprintf( _n( '%d pre-order completed.', '%s pre-orders completed.', $success_count, 'wc-pre-orders' ), $success_count );
					}
					if ( $error_count > 0 ) {
						$messages[] = sprintf( _n( '%d pre-order could not be completed.', '%s pre-orders could not be completed.', $error_count, 'wc-pre-orders' ), $error_count );
					}
				break;

				case 'message':
					$messages[] = sprintf( _n( '%d email dispatched.', '%s emails dispatched.', count( $order_ids ), 'wc-pre-orders' ), count( $order_ids ) );
				break;
			}

			$this->_redirect_with_notice( implode( '  ', $messages ) );
		}
	}

	/**
	 * Get the current action selected from the bulk actions dropdown, verifying
	 * that it's a valid action to perform.
	 *
	 * @see WP_List_Table::current_action()
	 *
	 * @return string|bool The action name or False if no action was selected.
	 */
	public function current_action() {
		$current_action = false;

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
			$current_action = $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
			$current_action = $_REQUEST['action2'];
		}

		$valid_actions   = array_keys( $this->get_bulk_actions() );
		$valid_actions[] = 'cancel_pre_order';

		if ( $current_action && ! in_array( $current_action, $valid_actions ) ) {
			return false;
		}

		return $current_action;
	}

	/**
	 * Dispatch actions from Manage tab and Actions tab.
	 *
	 * @since 1.0
	 */
	public function process_actions() {
		$this->process_actions_tab();
		$this->process_manage_tab();
		$this->process_cancel_pre_order_action();
	}

	/**
	 * Gets the bulk actions available for pre-orders: complete, cancel or message.
	 *
	 * @see WP_List_Table::get_bulk_actions()
	 *
	 * @return array associative array of action_slug => action_title.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'cancel'   => __( 'Cancel', 'wc-pre-orders' ),
			'complete' => __( 'Complete', 'wc-pre-orders' ),
			'message'  => __( 'Customer Message', 'wc-pre-orders' ),
		);

		return $actions;
	}

	/**
	 * Gets the current customer message which is used for bulk actions.
	 *
	 * @return string the current customer message.
	 */
	public function get_current_customer_message() {
		if ( isset( $_REQUEST['customer_message'] ) && $_REQUEST['customer_message'] ) {
			return $_REQUEST['customer_message'];
		}

		if ( isset( $_REQUEST['customer_message2'] ) && $_REQUEST['customer_message2'] ) {
			return $_REQUEST['customer_message2'];
		}

		return null;
	}

	/**
	 * Loads the pre-orders list table so the columns can be hidden/shown from
	 * the page Screen Options dropdown (this must be done prior to Screen Options
	 * being rendered).
	 */
	public function load_pre_orders_list_table() {
		if ( isset( $_GET['page'] ) && 'wc_pre_orders' == $_GET['page'] ) {
			$this->get_pre_orders_list_table();
		}
	}

	/**
	 * Gets the pre-orders list table object.
	 *
	 * @return WC_Pre_Orders_List_Table the pre-orders list table object
	 */
	private function get_pre_orders_list_table() {
		global $wc_pre_orders;

		if ( ! isset( $this->pre_orders_list_table ) ) {

			$class_name = apply_filters( 'wc_pre_orders_list_table_class_name', 'WC_Pre_Orders_List_Table' );

			require( $wc_pre_orders->get_plugin_path() . '/includes/class-wc-pre-orders-list-table.php' );
			$this->pre_orders_list_table = new $class_name();
		}

		return $this->pre_orders_list_table;
	}

	/**
	 * Show the Pre-Orders > Actions tab content.
	 */
	private function show_actions_tab() {
		global $woocommerce;

		// Load file for woocommerce_admin_fields() usage.
		if ( file_exists ( $woocommerce->plugin_path() . '/includes/admin/wc-admin-functions.php' ) ) {
			require_once( $woocommerce->plugin_path() . '/includes/admin/wc-admin-functions.php' );
		} else {
			require_once( $woocommerce->plugin_path() . '/admin/woocommerce-admin-settings.php' );
		}

		// TODO: cache this results? this will be called again when form is rendered.
		$pre_order_products = WC_Pre_Orders_Manager::get_all_pre_order_enabled_products();
		if ( empty( $pre_order_products ) ) {
			?>
			<div class="notice notice-warning">
				<p><?php _e( 'There is no pre-order product currently. List of pre-order products will appear in the drop-down Product below.', 'wc-pre-orders' ); ?></p>
			</div>
			<?php
		}

		// Add 'submit_button' woocommerce_admin_fields() field type.
		add_action( 'woocommerce_admin_field_submit_button', array( $this, 'generate_submit_button' ) );

		$current_section = ( empty( $_REQUEST['section'] ) ) ? 'email' : sanitize_text_field( urldecode( $_REQUEST['section'] ) );

		$actions = array(
			'email'       => __( 'Email', 'wc-pre-orders' ),
			'change-date' => __( 'Change Release Date', 'wc-pre-orders' ),
			'complete'    => __( 'Complete', 'wc-pre-orders' ),
			'cancel'      => __( 'Cancel', 'wc-pre-orders' ),
		);

		foreach ( $actions as $action_id => $action_title ) {
			$current = ( $action_id == $current_section ) ? ' class="current"' : '';

			$links[] = sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'section' => $action_id ), admin_url( 'admin.php?page=wc_pre_orders&tab=actions' ) ), $current, $action_title );
		}

		echo '<ul class="subsubsub"><li>' . implode( ' | </li><li>', $links ) . '</li></ul><br class="clear" />';
		echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">';
		woocommerce_admin_fields( $this->get_action_fields( $current_section ) );
		wp_nonce_field( 'wc-pre-orders-process-actions' );
		echo '<input type="hidden" name="wc_pre_orders_action" value="' . esc_attr( $current_section ) . '" /></form>';
	}

	/**
	 * Show the Pre-Orders > Manage tab content.
	 */
	private function show_manage_tab() {
		// Setup 'Manage Pre-Orders' list table and prepare the data.
		$manage_table = $this->get_pre_orders_list_table();
		$manage_table->prepare_items();

		echo '<form method="get" id="mainform" action="" enctype="multipart/form-data">';
		// title/search result string
		echo '<h2>' . __( 'Manage Pre-Orders', 'wc-pre-orders' );
		if ( isset( $_GET['s'] ) && $_GET['s'] ) {
			echo '<span class="subtitle">' . sprintf( __( 'Search results for "%s"', 'wc-pre-orders' ), $_GET['s'] ) . '</span>';
		}
		echo '</h2>';

		// display any action messages
		$manage_table->render_messages();

		// Display the views
		$manage_table->views();
		$manage_table->search_box( __( 'Search Pre-Orders', 'wc-pre-orders' ), 'pre_order' );

		if ( ! empty( $_REQUEST['pre_order_status'] ) ) {
			echo '<input type="hidden" name="pre_order_status" value="' . esc_attr( $_REQUEST['pre_order_status'] ) . '" />';
		}

		echo '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';

		// display the list table
		$manage_table->display();
		echo '</form>';
	}

	/**
	 * Get the fields to display for the selected action, in the format required by woocommerce_admin_fields().
	 *
	 * @param  string $section The current section to get fields for.
	 *
	 * @return array
	 */
	private function get_action_fields( $section ) {

		$products = array( '' => __( 'Select a Product', 'wc-pre-orders' ) );

		foreach ( WC_Pre_Orders_Manager::get_all_pre_order_enabled_products() as $product ) {
			$products[ $product->get_id() ] = $product->get_formatted_name();
		}

		$fields = array(

			'email' => array(

				array(
					'name' => __( 'Email Pre-Order Customers', 'wc-pre-orders' ),
					'desc' => sprintf( __( 'You may send an email message to all customers who have pre-ordered a specific product. This will use the default template specified for the %sCustomer Note%s Email.', 'wc-pre-orders' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_note' ) . '">', '</a>' ),
					'type' => 'title'
				),

				array(
					'id'                => 'wc_pre_orders_action_product',
					'name'              => __( 'Product', 'wc-pre-orders' ),
					'desc_tip'          => __( 'Select which product to email all pre-ordered customers.', 'wc-pre-orders' ),
					'default'           => ' ',
					'options'           => $products,
					'type'              => 'select',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'                => 'wc_pre_orders_action_email_message',
					'name'              => __( 'Message', 'wc-pre-orders' ),
					'desc_tip'          => __( 'Enter a message to include in the email notification to customer. Limited HTML allowed.', 'wc-pre-orders' ),
					'css'               => 'min-width: 300px;',
					'default'           => '',
					'type'              => 'textarea',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Send Emails', 'wc-pre-orders' ),
					'type' => 'submit_button',
				),
			),

			'change-date' => array(

				array(
					'name' => __( 'Change the Pre-Order Release Date', 'wc-pre-orders' ),
					'desc' => __( 'You may change the release date for all pre-orders of a specific product. This will send an email notification to each customer informing them that the pre-order release date was changed, along with the new release date.', 'wc-pre-orders' ),
					'type' => 'title'
				),

				array(
					'id'                => 'wc_pre_orders_action_product',
					'name'              => __( 'Product', 'wc-pre-orders' ),
					'desc_tip'          => __( 'Select which product to change the release date for.', 'wc-pre-orders' ),
					'default'           => ' ',
					'options'           => $products,
					'type'              => 'select',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'                => 'wc_pre_orders_action_new_availability_date',
					'name'              => __( 'New Availability Date', 'wc-pre-orders' ),
					'desc_tip'          => __( 'The new availability date for the product. This must be later than the current availability date.', 'wc-pre-orders' ),
					'default'           => '',
					'type'              => 'text',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'      => 'wc_pre_orders_action_enable_email_notification',
					'name'    => __( 'Send Email Notification', 'wc-pre-orders' ),
					'desc'    => __( 'Uncheck this to prevent email notifications from being sent to customers.', 'wc-pre-orders' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'id'       => 'wc_pre_orders_action_email_message',
					'name'     => __( 'Message', 'wc-pre-orders' ),
					'desc_tip' => __( 'Enter a message to include in the email notification to customer.', 'wc-pre-orders' ),
					'default'  => '',
					'css'      => 'min-width: 300px;',
					'type'     => 'textarea',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Change Release Date', 'wc-pre-orders' ),
					'type' => 'submit_button',
				),
			),

			'complete' => array(

				array(
					'name' => __( 'Complete Pre-Orders', 'wc-pre-orders' ),
					'desc' => __( 'You may complete all pre-orders for a specific product. This will charge the customer\'s card the pre-ordered amount, change their order status to completed, and send them an email notification.', 'wc-pre-orders' ),
					'type' => 'title'
				),

				array(
					'id'                => 'wc_pre_orders_action_product',
					'name'              => __( 'Product', 'wc-pre-orders' ),
					'desc_tip'          => __( 'Select which product to complete all pre-orders for.', 'wc-pre-orders' ),
					'default'           => ' ',
					'options'           => $products,
					'type'              => 'select',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'      => 'wc_pre_orders_action_enable_email_notification',
					'name'    => __( 'Send Email Notification', 'wc-pre-orders' ),
					'desc'    => __( 'Uncheck this to prevent email notifications from being sent to customers.', 'wc-pre-orders' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'id'       => 'wc_pre_orders_action_email_message',
					'name'     => __( 'Message', 'wc-pre-orders' ),
					'desc_tip' => __( 'Enter a message to include in the email notification to customer.', 'wc-pre-orders' ),
					'default'  => '',
					'css'      => 'min-width: 300px;',
					'type'     => 'textarea',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Complete Pre-Orders', 'wc-pre-orders' ),
					'type' => 'submit_button',
				),
			),

			'cancel' => array(
				array(
					'name' => __( 'Cancel Pre-Orders', 'wc-pre-orders' ),
					'desc' => __( 'You may cancel all pre-orders for a specific product. This will mark the order as cancelled and send the customer an email notification. If pre-orders were charged upfront, you must manually refund the orders.', 'wc-pre-orders' ),
					'type' => 'title'
				),

				array(
					'id'                => 'wc_pre_orders_action_product',
					'name'              => __( 'Product', 'wc-pre-orders' ),
					'desc_tip'          => __( 'Select which product to cancel all pre-orders for.', 'wc-pre-orders' ),
					'default'           => ' ',
					'options'           => $products,
					'type'              => 'select',
					'custom_attributes' => array(
						'required' => 'required',
					),
				),

				array(
					'id'      => 'wc_pre_orders_action_enable_email_notification',
					'name'    => __( 'Send Email Notification', 'wc-pre-orders' ),
					'desc'    => __( 'Uncheck this to prevent email notifications from being sent to customers.', 'wc-pre-orders' ),
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'id'       => 'wc_pre_orders_action_email_message',
					'name'     => __( 'Message', 'wc-pre-orders' ),
					'desc_tip' => __( 'Enter a message to include in the email notification to customer.', 'wc-pre-orders' ),
					'default'  => '',
					'css'      => 'min-width: 300px;',
					'type'     => 'textarea',
				),

				array( 'type' => 'sectionend' ),

				array(
					'name' => __( 'Cancel Pre-Orders', 'wc-pre-orders' ),
					'type' => 'submit_button',
				),
			),
		);

		return ( isset( $fields[ $section ] ) ) ? $fields[ $section ] : array();
	}

	/**
	 * Generate a submit button, called via a do_action() inside woocommerce_admin_fields() for non-default field types.
	 *
	 * @param array $field The field info.
	 */
	public function generate_submit_button( $field ) {
		submit_button( $field['name'] );
	}

	/**
	 * Save our list option.
	 *
	 * @param  string $status unknown.
	 * @param  string $option the option name.
	 * @param  string $value the option value.
	 *
	 * @return string
	 */
	public function set_pre_orders_list_option( $status, $option, $value ) {
		if ( 'wc_pre_orders_edit_pre_orders_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Redirect with message notice.
	 *
	 * @since 1.4.7
	 *
	 * @param string $message Message to display
	 */
	protected function _redirect_with_notice( $message ) {
		$message_nonce = wp_create_nonce( __FILE__ );

		set_transient( $this->message_transient_prefix . $message_nonce, array( 'messages' => $message ), 60 * 60 );

		// Get our next destination, stripping out all actions and other unneeded parameters.
		if ( isset( $_REQUEST['_wp_http_referer'] ) ) {
			$redirect_url = $_REQUEST['_wp_http_referer'];
		} else {
			$redirect_url = remove_query_arg( array( '_wp_http_referer', '_wpnonce', 'action', 'action2', 'order_id', 'customer_message', 'customer_message2' ), stripslashes( $_SERVER['REQUEST_URI'] ) );
		}

		wp_redirect( esc_url_raw( add_query_arg( 'message', $message_nonce, $redirect_url ) ) );
		exit;
	}
}

new WC_Pre_Orders_Admin_Pre_Orders();
