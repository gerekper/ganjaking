<?php
/**
 * Help Scout Integration.
 *
 * @package WC_Help_Scout_Integration
 *
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Help_Scout_My_Account.
 *
 * @package WC_Help_Scout_My_Account
 *
 * @since 1.3.0
 */
class WC_Help_Scout_My_Account extends WC_Integration {

	/**
	 * Endpoint slug.
	 *
	 * @var string
	 */
	private $_endpoint = 'support-conversations';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->api_url            = 'https://api.helpscout.net/v2/';
		$woocommerce_help_scout_settings = get_option( 'woocommerce_help-scout_settings' );

		// Define user set variables.
		$this->app_key          = $woocommerce_help_scout_settings['app_key'];
		$this->app_secret       = $woocommerce_help_scout_settings['app_secret'];
		$this->mailbox_id       = $woocommerce_help_scout_settings['mailbox_id'];
		$this->assigned_to      = $woocommerce_help_scout_settings['assigned_to'];
		$this->conversation_cc  = $woocommerce_help_scout_settings['conversation_cc'];
		$this->conversation_bcc = $woocommerce_help_scout_settings['conversation_bcc'];
		$this->debug            = $woocommerce_help_scout_settings['debug'];
		$this->hide_conversation = isset( $woocommerce_help_scout_settings['hide_conversation'] ) ? $woocommerce_help_scout_settings['hide_conversation'] : '';
		$this->hide_help_button = isset( $woocommerce_help_scout_settings['hide_help_button'] ) ? $woocommerce_help_scout_settings['hide_help_button'] : '';

		if ( 'yes' == $this->debug ) {
			$this->log = WC_Help_Scout::get_logger();
		}
		$this->allowed_array = array(
			'label' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
				'for' => array(),
			),
			'input' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
				'type' => array(),
				'name' => array(),
				'value' => array(),
			),
			'strong' => array(),
			'small' => array(),
			'ul' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'li' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'form' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
				'type' => array(),
				'name' => array(),
				'action' => array(),
				'method' => array(),
				'enctype' => array(),
			),
			'h3' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'p' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'textarea' => array(
				'value' => array(),
				'name' => array(),
				'cols' => array(),
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'table' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'td' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'th' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'thead' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'tr' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'span' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'tbody' => array(
				'class' => array(),
				'id' => array(),
				'style' => array(),
			),
			'div' => array(
				'class' => array(),
				'id' => array(),
				'style' => array( 'display' => array() ),
			),
			'a' => array(
				'href' => array(),
				'data-conversation-id' => array(),
				'class' => array(),
				'data-subject' => array(),
				'style' => array(),
			),
		);
		// Customer "My Orders" actions.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_create_conversation' ), 40 );
		add_action( 'woocommerce_my_account_my_orders_actions', array( $this, 'orders_actions' ), 10, 2 );
		add_action( 'woocommerce_after_my_account', array( $this, 'my_account_conversations_table' ), 10 );

		// Tabbed My Account in WC 2.6+.
		add_action( 'init', array( $this, 'my_support_conversations_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'my_support_conversations_query_vars' ), 0 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'my_support_conversations_menu_items' ) );
		add_action( 'woocommerce_account_support-conversations_endpoint', array( $this, 'my_support_conversations_content' ) );
		add_filter( 'the_title', array( $this, 'my_support_conversations_title' ) );

		// hooks for update/create customer data at helpscout.
		add_action( 'woocommerce_save_account_details', array( $this, 'update_profile_fields_helpscout' ) );
		add_action( 'woocommerce_customer_save_address', array( $this, 'update_profile_fields_helpscout' ), 10, 2 );
		add_action( 'woocommerce_new_order', array( $this, 'update_profile_fields_helpscout_after_new_order' ), 1, 1 );
		add_action( 'user_register', array( $this, 'update_profile_fields_helpscout_after_user_register' ) );

	}

	/**
	 * Update profile fields on helpscout
	 *
	 * @param int $user_id User ID.
	 */
	public function update_profile_fields_helpscout( $user_id ) {
		if ( isset( $user_id ) && ! empty( $user_id ) ) {
			$customer_id = get_user_meta( $user_id, '_help_scout_customer_id', true );
			$fname = get_user_meta( $user_id, 'first_name', true );
			$lname = get_user_meta( $user_id, 'last_name', true );
			$email = get_user_meta( $user_id, 'billing_email', true );
			$user_info = get_userdata( $user_id );
			$user_email = $user_info->user_email;
			$user_roles = $user_info->roles;
			// Check for user role customer.
			if ( in_array( 'customer', $user_roles ) ) {
				if ( ! empty( $customer_id ) ) {
					// If customer id exist then directely update customer data.
					$this->update_customer( $customer_id, $user_id );
				} else {
					// If customer id not exist then search customer data by using email.
					$customers_url = $this->api_url . 'customers/';
					$params = array(
						'timeout' => 60,
						'headers' => array(
							'Content-Type'  => 'application/json;charset=UTF-8',
							'Authorization' => 'Bearer ' . get_option( 'helpscout_access_token' ),
						),
					);
					$search_customers = wp_safe_remote_get( $customers_url . '?query=(email:' . $user_email . ')', $params );

					if (
					! is_wp_error( $search_customers )
					&& 200 == $search_customers['response']['code']
					&& ( 0 == strcmp( $search_customers['response']['message'], 'OK' ) )
					) {

						$search_customers_data = json_decode( $search_customers['body'], true );

						if (
							isset( $search_customers_data['_embedded']['customers'][0]['id'] )
							&& ! empty( $search_customers_data['_embedded']['customers'][0]['id'] )
						) {
							$customer_id = intval( $search_customers_data['_embedded']['customers'][0]['id'] );
							// If customer id found then update customer data by using customer id.
							update_user_meta( $user_id, '_help_scout_customer_id', $customer_id );
							$this->update_customer( $customer_id, $user_id );
						} else {
							// Create new customer.
							$this->create_customer( $user_id, $user_email );
							if ( 'yes' == $this->debug ) {
								$this->log->add( $this->id, 'update_profile_fields_helpscout => Condition four ' );
							}
						}
					} else {
						// Create new customer.
						$this->create_customer( $user_id, $user_email );
					}
				}
			}
		}
	}
	/**
	 * Update customer profile fields on helpscout
	 *
	 * @param int $customer_id Helpscout Customer ID.
	 * @param int $user_id User ID.
	 */
	public function update_customer( $customer_id, $user_id ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'params' . $customer_id . '-' . $user_id );
		}
		$fname = get_user_meta( $user_id, 'first_name', true );
		$lname = get_user_meta( $user_id, 'last_name', true );
		$billing_first_name = get_user_meta( $user_id, 'billing_first_name', true );
		$billing_last_name = get_user_meta( $user_id, 'billing_last_name', true );
		$billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
		$billing_address_2 = get_user_meta( $user_id, 'billing_address_2', true );
		$billing_city      = get_user_meta( $user_id, 'billing_city', true );
		$billing_state     = get_user_meta( $user_id, 'billing_state', true );
		$billing_postcode  = get_user_meta( $user_id, 'billing_postcode', true );
		$billing_country   = get_user_meta( $user_id, 'billing_country', true );
		$billing_phone   = get_user_meta( $user_id, 'billing_phone', true );

		// Set billing first name if default name is empty.
		if ( empty( $fname ) ) {
			$fname = $billing_first_name;
		}
		// Set billing last name if default name is empty.
		if ( empty( $lname ) ) {
			$lname = $billing_last_name;
		}

		// Set connection params.
		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type'  => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' . get_option( 'helpscout_access_token' ),
			),
		);

		// Search customer by helpscout customer id api url.
		$customers_url_by_id = $this->api_url . 'customers/' . $customer_id;

		$search_customers = wp_safe_remote_get( $customers_url_by_id, $params );
		if ( is_wp_error( $search_customers ) || 200 != $search_customers['response']['code'] ) {
			return $customer_id;
		}
		$search_customers_data = json_decode( $search_customers['body'], true );
		
		// Get Customer phone number id stored in helpscut.
		if ( isset( $search_customers_data['_embedded']['phones'][0]['id'] ) ) {
			$phone = $search_customers_data['_embedded']['phones'][0]['id'];
		} else {
			$phone = '';
		}

		// Make update customer details array to process into api.
		$customer_data = array(
			array(
				'op' => 'replace',
				'path' => '/firstName',
				'value' => $fname,
			),
			array(
				'op' => 'replace',
				'path' => '/lastName',
				'value' => $lname,
			),
			array(
				'op' => 'replace',
				'path' => '/address/city',
				'value' => $billing_city,
			),
			array(
				'op' => 'replace',
				'path' => '/address/country',
				'value' => $billing_country,
			),
			array(
				'op' => 'replace',
				'path' => '/address/lines',
				'value' => array( $billing_address_1, $billing_address_2 ),
			),
			array(
				'op' => 'replace',
				'path' => '/address/postalCode',
				'value' => $billing_postcode,
			),
			array(
				'op' => 'replace',
				'path' => '/address/state',
				'value' => $billing_state,
			),
		);

		// Replace phone number if phone id exist.
		if ( isset( $phone ) && ! empty( $phone ) && ! empty( $billing_phone ) ) {
			$customer_data[] = array(
				'op' => 'replace',
				'path' => '/phones/' . $phone . '/type',
				'value' => 'work',
			);
			$customer_data[] = array(
				'op' => 'replace',
				'path' => '/phones/' . $phone . '/value',
				'value' => $billing_phone,
			);
		} elseif ( ! empty( $billing_phone ) ) {
			// Add new phone number.
			$customer_data[] = array(
				'op' => 'add',
				'path' => '/phones',
				'value' => array(
					'type' => 'work',
					'value' => $billing_phone,
				),
			);
		}
		if ( isset( $email ) ) {

			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			$customer_data = apply_filters( 'woocommerce_help_scout_customer_args', $customer_data, $user_id, $email );
		} else {
			$email = '';

			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			$customer_data = apply_filters( 'woocommerce_help_scout_customer_args', $customer_data, $user_id, $email );
		}

		$params['method'] = 'PATCH';
		$params['body']   = stripslashes( json_encode( $customer_data ) );

		$update_customer = wp_safe_remote_post( $customers_url_by_id, $params );

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'update_customer => Customer ID for the user ' . $user_id . ' is ' . $customer_id );
		}
		return $customer_id;
	}

	/**
	 * Create customer on helpscout
	 *
	 * @param int    $user_id User ID.
	 * @param string $user_email User email ID.
	 */
	public function create_customer( $user_id, $user_email = '' ) {
		$fname = get_user_meta( $user_id, 'first_name', true );
		$lname = get_user_meta( $user_id, 'last_name', true );
		$billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
		$billing_address_2 = get_user_meta( $user_id, 'billing_address_2', true );
		$billing_city      = get_user_meta( $user_id, 'billing_city', true );
		$billing_state     = get_user_meta( $user_id, 'billing_state', true );
		$billing_postcode  = get_user_meta( $user_id, 'billing_postcode', true );
		$billing_country   = get_user_meta( $user_id, 'billing_country', true );
		$billing_phone   = get_user_meta( $user_id, 'billing_phone', true );

		// Customers API.
		$customers_url = $this->api_url . 'customers';

		// Set connection params.
		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type'  => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' . get_option( 'helpscout_access_token' ),
			),
		);
		// Create/update customer.
		$customer_data = array(
			'emails'    => array(
				array(
					'type'  => 'work',
					'value' => $user_email,
				),
			),
		);
		if ( ! empty( $fname ) ) {
			$customer_data['firstName'] = $fname;
		}
		if ( ! empty( $fname ) ) {
			$customer_data['lastName'] = $lname;
		}

		if (
			( $billing_address_1 || $billing_address_2 )
			&& $billing_city
			&& $billing_state
			&& $billing_postcode
			&& $billing_country
		) {
			$customer_data['address'] = array(
				'lines'      => array( $billing_address_1, $billing_address_2 ),
				'city'       => $billing_city,
				'state'      => $billing_state,
				'postalCode' => $billing_postcode,
				'country'    => $billing_country,
			);
		}
		if ( ! empty( $billing_phone ) ) {
			$customer_data['phone'] = $billing_phone;
			$customer_data['phones'] = array(
				array(
					'type' => 'work',
					'value' => $billing_phone,
				),
			);
		}

		/**
		* Action for woocommerce_help_scout_customer_args.
		*
		* @since  1.3.4
		*/
		$customer_data = apply_filters( 'woocommerce_help_scout_customer_args', $customer_data, $user_id, $user_email );
		$params['method'] = 'POST';
		$params['body']   = json_encode( $customer_data );
		$create_customer  = wp_safe_remote_post( $customers_url, $params );

		$customer_id = 0;
		if (
			! is_wp_error( $create_customer )
			&& 201 == $create_customer['response']['code']
			&& ( 0 == strcmp( $create_customer['response']['message'], 'Created' ) )
			&& isset( $create_customer['headers']['location'] )
		) {
			$customer_id = str_replace( array( $this->api_url, 'customers/', '.json' ), '', $create_customer['headers']['location'] );
			$customer_id = intval( $customer_id );

			update_user_meta( $user_id, '_help_scout_customer_id', $customer_id );

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'create_customer => Customer ID for the user ' . $user_id . ' is ' . $customer_id );
			}
		}
		return $customer_id;
	}

	/**
	 * Update customer profile fields on helpscout after new order
	 *
	 * @param int $order_get_id Order ID.
	 */
	public function update_profile_fields_helpscout_after_new_order( $order_get_id ) {
		$user_id = get_post_meta( $order_get_id, '_customer_user', true );
		$this->update_profile_fields_helpscout( $user_id );
	}

	/**
	 * Update customer profile fields on helpscout after new user register
	 *
	 * @param int $user_id User ID.
	 */
	public function update_profile_fields_helpscout_after_user_register( $user_id ) {
		$this->update_profile_fields_helpscout( $user_id );
	}


	/**
	 * Create conversation form.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function view_order_create_conversation( $order_id ) {
		$vars = array(
			'order_id' => $order_id,
		);
		$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';
		wc_get_template( 'order/create-conversation.php', $vars, 'woocommerce-help-scout', $default_path );
	}

	/**
	 * Added support button in order actions.
	 *
	 * @param  array    $actions Order actions.
	 * @param  WC_Order $order   Order data.
	 *
	 * @return array
	 */
	public function orders_actions( $actions, $order ) {
		$order_url = $order->get_view_order_url();
		if ( 'yes' != $this->hide_help_button ) {
			$actions[ $this->id ] = array(
				'url'  => $order_url . '#start-conversation',
				'name' => __( 'Get Help', 'woocommerce-help-scout' ),
			);
		}
		$actions[ $this->id . '_ticket' ] = array(
			'url'  => site_url( 'my-account/support-conversations/' . $order->get_id() . '/#get_Conversation' ),
			'name' => __( 'Ticket/Conversation', 'woocommerce-help-scout' ),
		);
		return $actions;

	}

	/**
	 * Display a table with the user conversations in My Account page.
	 *
	 * Hooked into `woocommerce_after_my_account` to provide backward compatibility
	 * For WC < 2.6. Won't output anything if WC >= 2.6.
	 */
	public function my_account_conversations_table() {
		if ( version_compare( WC()->version, '2.6', '<' ) ) {
			echo wp_kses(
				$this->get_my_conversations(
					array(
						'show_title'   => true,
						'endpoint_url' => esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ),
					)
				),
				$this->allowed_array
			);
		}
	}
	/**
	 * Get my conversations table.
	 *
	 * @param array $args Arguments to alter output.
	 *
	 * @return string HTML of my conversations table
	 */
	public function get_my_conversations( $args = array() ) {
		global $current_user;

		$args = wp_parse_args(
			$args,
			array(
				'show_title'   => false,
				'endpoint_url' => wc_get_endpoint_url( $this->_endpoint ),
			)
		);

		$integration = WC_Help_Scout::get_integration_instance();
		$customer_id = $integration->get_customer_id( $current_user->ID, $current_user->user_email );

		// Navigation.
		$current_page = ( isset( $_GET['support_page'] ) && $_GET['support_page'] > 0 ) ? intval( $_GET['support_page'] ) : 1;
		$next_page    = add_query_arg( array( 'support_page' => $current_page + 1 ), $args['endpoint_url'] );
		$last_page    = add_query_arg( array( 'support_page' => $current_page - 1 ), $args['endpoint_url'] );

		$vars = array_merge(
			$args,
			array(
				'conversations' => $integration->get_customer_conversations( $customer_id, $current_page ),
				'date_format'   => wc_date_format(),
				'integration'   => $integration,
				'current_page'  => $current_page,
				'next_page'     => $next_page,
				'last_page'     => $last_page,
			)
		);

		if ( file_exists( get_stylesheet_directory() . '/woocommerce-help-scout/templates/myaccount/conversations.php' ) ) {
			$default_path = get_stylesheet_directory() . '/woocommerce-help-scout/templates/';
		} else {
			$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';
		}
		return wc_get_template_html( 'myaccount/conversations.php', $vars, 'woocommerce-help-scout', $default_path );

	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function my_support_conversations_endpoint() {
		add_rewrite_endpoint( $this->_endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars Query vars.
	 *
	 * @return array Altered query vars.
	 */
	public function my_support_conversations_query_vars( $vars ) {
		$vars[] = $this->_endpoint;

		return $vars;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items Menu items.
	 *
	 * @return array Altered menu items.
	 */
	public function my_support_conversations_menu_items( $items ) {
		$new_items = array();

		if ( 'yes' != $this->hide_conversation ) {
			$new_items[ $this->_endpoint ] = __( 'Support Conversations', 'woocommerce-help-scout' );
		}

		// Add the new item after `orders`.
		return $this->_insert_menu_items_after( $items, $new_items, 'orders' );
	}

	/**
	 * Custom helper to add new items into an array after a selected item.
	 *
	 * @param array  $items     Original items.
	 * @param array  $new_items New items to add.
	 * @param string $after    Insert new items after this menu item.
	 *
	 * @return array Altered menu items
	 */
	private function _insert_menu_items_after( $items, $new_items, $after ) {
		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $items ) ) + 1;

		// Insert the new item.
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );

		return $array;
	}

	/**
	 * Endpoint HTML content.
	 *
	 * @return void
	 */
	public function my_support_conversations_content() {
		echo wp_kses( $this->get_my_conversations(), $this->allowed_array );
	}

	/**
	 * Change endpoint title.
	 *
	 * @param string $title Original title.
	 *
	 * @return string Altered title.
	 */
	public function my_support_conversations_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ $this->_endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'My Support Conversations', 'woocommerce-help-scout' );

			remove_filter( 'the_title', array( $this, 'my_support_conversations_title' ) );
		}

		return $title;
	}
}
