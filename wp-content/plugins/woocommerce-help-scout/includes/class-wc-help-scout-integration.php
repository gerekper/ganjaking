<?php
/**
 * Help Scout Integration.
 *
 * @package  WC_Help_Scout_Integration
 * @category Integration
 * @author   WooThemes
 */

class WC_Help_Scout_Integration extends WC_Integration {

	protected $api_url;
	protected $admin_url;
	protected $app_key;
	protected $app_secret;
	protected $mailbox_id;
	protected $assigned_to;
	protected $conversation_cc;
	protected $conversation_bcc;
	protected $debug;
	protected $log;

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		$this->id                 = 'help-scout';
		$this->method_title       = __( 'Help Scout', 'woocommerce-help-scout' );
		$this->method_description = __( 'Help Scout is a scalable customer support, no help desk headaches.<br/><br/>Redirect URL:   '.$this->help_scout_redirect_uri(), 'woocommerce-help-scout' );

		// API.
		$this->api_url            = 'https://api.helpscout.net/v2/';
		$this->admin_url          = 'https://secure.helpscout.net/';

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->app_key          = $this->get_option( 'app_key' );
		$this->app_secret       = $this->get_option( 'app_secret' );
		$this->mailbox_id       = $this->get_option( 'mailbox_id' );
		$this->assigned_to      = $this->get_option( 'assigned_to' );
		$this->conversation_cc  = $this->get_option( 'conversation_cc' );
		$this->conversation_bcc = $this->get_option( 'conversation_bcc' );
		$this->debug            = $this->get_option( 'debug' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ),10 ,1 );

		// Scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		//wp_schedule_event( time(), 'hourly', array($this,'check_authorization_still_valid') );
		//add_action('init',array($this,'check_authorization_still_valid') );

		/*if (!wp_next_scheduled('my_task_hook')) {
			wp_schedule_event( time(), '60', 'my_task_hook' );
		}
		add_action ( 'my_task_hook', array($this,'check_authorization_still_valid') ); */


		// add_action( 'wp', array( $this,'check_authorization_still_valid' ));

		if ( is_admin() ) {
			// Comments.
			add_action( 'admin_init', array( $this, 'comment_to_conversation' ) );
			add_filter( 'manage_edit-comments_columns', array( $this, 'comments_columns' ) );
			add_action( 'manage_comments_custom_column', array( $this, 'custom_comment_column' ), 10, 2 );

			// Orders.
			add_action( 'add_meta_boxes', array( $this, 'add_order_conversation_metabox' ) );

			// General.
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			// Scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		}

		// Active logs.
		if ( 'yes' == $this->debug ) {
			$this->log = WC_Help_Scout::get_logger();
		}
	}

	/**
	 * Admin scripts.
	 *
	 * @return void
	 */
	public function admin_scripts() {
		$screen = get_current_screen();

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( 'edit-comments' === $screen->id ) {
			wp_enqueue_style( $this->id . '-comments-screen', plugins_url( 'assets/css/admin/comments-screen' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );
		}

		if('woocommerce_page_wc-settings'===$screen->id){
			wp_enqueue_style( $this->id . '-integration-screen', plugins_url( 'assets/css/admin/integration-screen.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );

			wp_enqueue_script( $this->id . '-integration-screen', plugins_url( 'assets/js/admin/integration-screen.js', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, true );
		}

		if ( 'shop_order' === $screen->id ) {
			wp_enqueue_style( $this->id . '-order-screen', plugins_url( 'assets/css/admin/order-screen' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );
			wp_enqueue_script( $this->id . '-order-screen', plugins_url( 'assets/js/admin/order-screen' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, true );
			wp_localize_script(
				$this->id . '-order-screen',
				'woocommerce_help_scout_shop_order_params',
				array(
					'success'   => __( 'Conversation created successfully.', 'woocommerce-help-scout' ),
					'view'      => __( 'View conversation.', 'woocommerce-help-scout' ),
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'security'  => wp_create_nonce( 'woocommerce_help_scout_ajax' ),
					'error'     => __( 'Failed to create the conversation, please try again.', 'woocommerce-help-scout' ),
					'admin_url' => $this->admin_url
				)
			);
		}
	}

	/**
	 * Check if client has defined Scout API Credentials.
	 *
	 * @return boolean
	 */
	public function are_credentials_defined() {
		if(!empty($this->app_key) && !empty($this->app_secret) && !empty($this->app_secret)) {
			return true;
		}
		return false;
	}

	/**
	 * Front-end scripts.
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		global $post;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'help-scout-form', plugins_url( 'assets/js/frontend/conversation-form' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'jquery-blockui' ), WC_HELP_SCOUT_VERSION, true );

		if ( is_account_page() || is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'woocommerce_order_tracking' ) ) {
			wp_enqueue_style( 'help-scout-myaccount-styles', plugins_url( 'assets/css/frontend/myaccount-page' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );

			wp_enqueue_script( 'help-scout-myaccount-scripts', plugins_url( 'assets/js/frontend/myaccount-page' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'help-scout-form' ), WC_HELP_SCOUT_VERSION, true );


			wp_localize_script(
				'help-scout-myaccount-scripts',
				'woocommerce_help_scout_myaccount_params',
				array(
					'processing'           => __( 'Please, wait a few moments, sending your request...', 'woocommerce-help-scout' ),
					'success'              => __( 'Thank you for your contact, we will respond as soon as possible.', 'woocommerce-help-scout' ),
					'ajax_url'             => admin_url( 'admin-ajax.php' ),
					'security'             => wp_create_nonce( 'woocommerce_help_scout_ajax' ),
					'error'                => __( 'There was an error in the request, please try again or contact us for assistance.', 'woocommerce-help-scout' ),
					'getting_conversation' => __( 'Please, wait a few moments, retrieving the conversation data...', 'woocommerce-help-scout' ),
					'reply'                => __( 'Reply', 'woocommerce-help-scout' ),
					'reply_to'             => __( 'Reply to', 'woocommerce-help-scout' ),
					'message'              => __( 'Message', 'woocommerce-help-scout' ),
					'send'                 => __( 'Send', 'woocommerce-help-scout' ),
					'user_id'              => get_current_user_id()
				)
			);
		}

		wp_localize_script(
			'help-scout-form',
			'woocommerce_help_scout_form_params',
			array(
				'processing'           => __( 'Please, wait a few moments, sending your request...', 'woocommerce-help-scout' ),
				'success'              => __( 'Thank you for your contact, we will respond as soon as possible.', 'woocommerce-help-scout' ),
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'security'             => wp_create_nonce( 'woocommerce_help_scout_ajax' ),
				'error'                => __( 'There was an error in the request, please try again or contact us for assistance.', 'woocommerce-help-scout' )
			)
		);

		wp_enqueue_style( 'hs-jquery-ui', plugins_url( 'assets/css/frontend/jquery-ui' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );
		wp_enqueue_style( 'hs-jquery-ui-plupload', plugins_url( 'assets/css/frontend/jquery.ui.plupload' . $suffix . '.css', plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, 'all' );

		//wp_enqueue_script( 'hs-jquery-min', plugins_url( 'assets/js/frontend/jquery' . $suffix . '.js',plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, false );
		wp_enqueue_script( 'hs-jquery-ui-min', plugins_url( 'assets/js/frontend/jquery-ui' . $suffix . '.js',plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, false );
		wp_enqueue_script( 'hs-plupload-full-min', plugins_url( 'assets/js/frontend/plupload.full' . $suffix . '.js',plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, false );
		wp_enqueue_script( 'hs-jquery-ui-plupload', plugins_url( 'assets/js/frontend/jquery.ui.plupload.js',plugin_dir_path( __FILE__ ) ), array(), WC_HELP_SCOUT_VERSION, false );
	}

	/**
     * Helpscout redirect uri.
    */

	public function help_scout_redirect_uri(){

		return WC_HELP_SCOUT_PLUGINURL . 'allow-access.php';
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$debug_label       = __( 'Enable Logging %s', 'woocommerce-help-scout' );
		$debug_description = __( 'Log Help Scout events, such as API requests.', 'woocommerce-help-scout' );

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' ) ) {
			$debug_label = sprintf( $debug_label, ' | ' . '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'View Log', 'woocommerce-help-scout' ) . '</a>' );
		} else {
			$debug_label = sprintf( $debug_label, ' | ' . __( 'View Log', 'woocommerce-help-scout' ) . ': <code>woocommerce/logs/' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' );
		}

		$this->form_fields = array(
			'app_key' => array(
				'title'       => __( 'APP Key', 'woocommerce-help-scout' ),
				'type'        => 'text',
				'description' => __( 'Enter your Scout APP API Key. Create one by navigating to Your Profile > My apps and click Create My App', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'app_secret' => array(
				'title'       => __( 'APP Secret', 'woocommerce-help-scout' ),
				'type'        => 'text',
				'description' => __( 'Enter your Help Scout APP Secret Key.', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'mailbox_id' => array(
				'title'       => __( 'Mailbox ID', 'woocommerce-help-scout' ),
				'type'        => 'text',
				'description' => __( 'Enter with your Help Scout Mailbox ID. Find this in your Help Scout Mailbox > Mailbox Settings > Edit Mailbox, example: <code>https://secure.helpscout.net/settings/mailbox/ID-HERE/</code>.', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'assigned_to' => array(
				'title'       => __( 'Assigned To (optional)', 'woocommerce-help-scout' ),
				'type'        => 'text',
				'description' => __( 'Enter the user ID responsible for conversations. Find this in the "Your Profile" page URL, example: <code>https://secure.helpscout.net/users/profile/USER-ID-HERE/</code>.', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'conversation_cc' => array(
				'title'       => __( 'Conversation CC (optional)', 'woocommerce-help-scout' ),
				'type'        => 'textarea',
				'description' => __( 'Enter a list of users emails that will receive copies of the all conversations, separate each email address with a comma.', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'conversation_bcc' => array(
				'title'       => __( 'Conversation BCC (optional)', 'woocommerce-help-scout' ),
				'type'        => 'textarea',
				'description' => __( 'Enter a list of users emails that will receive hidden copies of the all conversations, separate each email address with a comma.', 'woocommerce-help-scout' ),
				'desc_tip'    => false,
				'default'     => ''
			),
			'debug'   => array(
				'title'       => __( 'Debug Log', 'woocommerce-help-scout' ),
				'type'        => 'checkbox',
				'label'       => $debug_label,
				'default'     => 'no',
				'description' => $debug_description
			),
			'access_url' => array(
				'title'       => __( '', 'woocommerce-help-scout' ),
				'type'        => 'title',
				'description' => '<a href="#" id="allow_access_url">Allow Access</a>',
			)
		);

	}

	/**
	 * Get Customer ID in Help Scout.
	 *
	 * @param  int    $user_id    WP user ID.
	 * @param  string $user_email User email.
	 * @param  string $first_name User first name (optional).
	 * @param  string $last_name  User last name (optional).
	 *
	 * @return int                Help Scout customer ID.
	 */
	public function get_customer_id($user_id, $user_email, $first_name = null, $last_name = null) {
        /* print_R($user_id);die; */
        // Gets the Help Scout customer ID.
        $customer_id = get_user_meta($user_id, '_help_scout_customer_id', true);

        if ('yes' == $this->debug) {
            $this->log->add($this->id, 'Getting Help Scout customer ID for the user ' . $user_id . '...');
        }

        if ($customer_id) {
            if ('yes' == $this->debug) {
                $this->log->add($this->id, 'Customer ID for the user ' . $user_id . ' is ' . $customer_id);
            }

            return $customer_id;
        }

        // Customers API.
        $customers_url = $this->api_url . 'customers';

        // Set connection params.
        $params = array(
            'timeout' => 60,
            'headers' => array(
                'Content-Type' => 'application/json;charset=UTF-8',
                'Authorization' => 'Bearer ' . get_option('helpscout_access_token')
            )
        );

        // First name fallback.
        if (null === $first_name) {
            $first_name = get_user_meta($user_id, 'first_name', true);
        }

        // Last name fallback.
        if (null === $last_name) {
            $last_name = get_user_meta($user_id, 'last_name', true);
        }

        // Create/update customer.
        $customer_data = array(
            'firstName' => $first_name,
            'lastName' => $last_name,
            'emails' => array(
                array(
                    'type' => 'work',
                    'value' => $user_email
                )
            )
        );

        // Customer address.
        $billing_address_1 = get_user_meta($user_id, 'billing_address_1', true);
        $billing_address_2 = get_user_meta($user_id, 'billing_address_2', true);
        $billing_city = get_user_meta($user_id, 'billing_city', true);
        $billing_state = get_user_meta($user_id, 'billing_state', true);
        $billing_postcode = get_user_meta($user_id, 'billing_postcode', true);
        $billing_country = get_user_meta($user_id, 'billing_country', true);

        if (
                ( $billing_address_1 || $billing_address_2 ) && $billing_city && $billing_state && $billing_postcode && $billing_country
        ) {
            $customer_data['address'] = array(
                'lines' => array($billing_address_1, $billing_address_2),
                'city' => $billing_city,
                'state' => $billing_state,
                'postalCode' => $billing_postcode,
                'country' => $billing_country
            );

            $search_customers_by_id = wp_safe_remote_get($customers_url, $params);
            //echo '<pre>'; print_r($search_customers_by_id); //exit;

            if ('yes' == $this->debug) {
                $this->log->add($this->id, 'search_customers_by_id...' . print_r($search_customers_by_id, true));
            }
            if (!is_wp_error($search_customers_by_id)) {
                if (404 == $search_customers_by_id['response']['code']) {
                    // Customers API.
                    $customers_url = $this->api_url . 'customers';

                    // Set connection params.
                    $params = array(
                        'timeout' => 60,
                        'headers' => array(
                            'Content-Type' => 'application/json;charset=UTF-8',
                            'Authorization' => 'Bearer ' . get_option('helpscout_access_token')
                        )
                    );

                    // First name fallback.
                    if (null === $first_name) {
                        $first_name = get_user_meta($user_id, 'first_name', true);
                    }

                    $customer_data = apply_filters('woocommerce_help_scout_customer_args', $customer_data, $user_id, $user_email);



                    // Searches for an existing client.
                    if ('yes' == $this->debug) {
                        $this->log->add($this->id, 'Searching customer in Help Scout...');
                    }

                    $search_customers = wp_safe_remote_get($customers_url . '?query=(email:' . $user_email . ')', $params);

                    if (
                            !is_wp_error($search_customers) && 200 == $search_customers['response']['code'] && ( 0 == strcmp($search_customers['response']['message'], 'OK') )
                    ) {

                        $search_customers_data = json_decode($search_customers['body'], true);

                        if (
                                isset($search_customers_data['_embedded']['customers'][0]['id']) && !empty($search_customers_data['_embedded']['customers'][0]['id'])
                        ) {
                            $customer_id = intval($search_customers_data['_embedded']['customers'][0]['id']);

                            update_user_meta($user_id, '_help_scout_customer_id', $customer_id);

                            if ('yes' == $this->debug) {
                                $this->log->add($this->id, 'Customer successfully found');
                            }

                            // Update the customer data.
                            $params['method'] = 'PUT';
                            $params['body'] = json_encode($customer_data);

                            if ('yes' == $this->debug) {
                                $this->log->add($this->id, 'Updating customer in Help Scout...');
                            }

                            $update_customer = wp_safe_remote_post($this->api_url . 'customers/' . $customer_id, $params);

                            if (
                                    !is_wp_error($update_customer) && 200 == $update_customer['response']['code'] && ( 0 == strcmp($update_customer['response']['message'], 'OK') )
                            ) {

                                if ('yes' == $this->debug) {
                                    $this->log->add($this->id, 'Customer successfully updated');
                                }
                            } else {
                                if ('yes' == $this->debug) {
                                    $this->log->add($this->id, 'Error updating customer data: ' . print_r($update_customer, true));
                                }
                            }
                        } elseif (200 == $search_customers_by_id['response']['code']) {
                            return $customer_id;
                        }
                    }
                }
            }
        }

        // Create a new customer in Help Scout.
        if ('yes' == $this->debug) {
            $this->log->add($this->id, 'No customer was found');
            $this->log->add($this->id, 'Creating a new customer in Help Scout...');
        }
        $customers_url = $this->api_url . 'customers';
        // Set connection params.
        $params = array(
            'timeout' => 60,
            'headers' => array(
                'Content-Type' => 'application/json;charset=UTF-8',
                'Authorization' => 'Bearer ' . get_option('helpscout_access_token')
            )
        );
        $search_customers = wp_safe_remote_get($customers_url . '?query=(email:' . $user_email . ')', $params);
        //echo '<pre>'; print_r($search_customers);
        if (
                !is_wp_error($search_customers) && 200 == $search_customers['response']['code'] && ( 0 == strcmp($search_customers['response']['message'], 'OK') )
        ) {

            $search_customers_data = json_decode($search_customers['body'], true);

            if (
                    isset($search_customers_data['_embedded']['customers'][0]['id']) && !empty($search_customers_data['_embedded']['customers'][0]['id'])
            ) {
                return $customer_id = intval($search_customers_data['_embedded']['customers'][0]['id']);
            }
        }

        // Create customer.
        $customer_data = array(
            'firstName' => $first_name,
            'lastName' => $last_name,
            'emails' => array(
                array(
                    'type' => 'work',
                    'value' => $user_email
                )
            )
        );

        $params['method'] = 'POST';
        $params['body'] = json_encode($customer_data);
        $create_customer = wp_safe_remote_post($customers_url, $params);
        //echo '<pre>'; print_r($create_customer); exit;
        if (
                !is_wp_error($create_customer) && 201 == $create_customer['response']['code'] && ( 0 == strcmp($create_customer['response']['message'], 'Created') ) && isset($create_customer['headers']['location'])
        ) {
            $customer_id = str_replace(array($this->api_url, 'customers/', '.json'), '', $create_customer['headers']['location']);
            $customer_id = intval($customer_id);
            if (!empty($user_id)) {
                update_user_meta($user_id, '_help_scout_customer_id', $customer_id);
            }

            if ('yes' == $this->debug) {
                $this->log->add($this->id, 'Customer ID for the user ' . $user_id . ' is ' . $customer_id);
            }

            return $customer_id;
        }

        if ('yes' == $this->debug) {
            $this->log->add($this->id, 'Error creating customer: ' . print_r($create_customer, true));
            $this->log->add($this->id, 'Unable to obtain the customer ID');
        }

        return 0;
    }

    /**
	 * Get customer conversations.
	 *
	 * @param  int    $customer_id Help Scout customer ID.
	 * @param  int    $page        List page.
	 * @param  string $status      Conversation status (all, active and pending).
	 *
	 * @return array               Customer conversatios.
	 */
	public function get_customer_conversations( $customer_id, $page = 1, $status = 'all' ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Getting conversations customer ID: ' . $customer_id );
		}

		$url = $this->api_url .'conversations?mailbox' . $this->mailbox_id .'&query=(customerIds:'.$customer_id.')&page=' . $page . '&status=' . $status;

		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$response = wp_safe_remote_get( $url, $params );

		if (
			! is_wp_error( $response )
			&& 200 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'OK' ) )
		) {
			$conversations = json_decode( $response['body'], true );

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'conversations successfully retrieved for the customer ID: ' . $customer_id );
			}

			return wp_unslash( $conversations );
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Error while try to get the conversations for customer ID: ' . $customer_id );
		}

		return array(
			'page'  => 1,
			'pages' => 0,
			'count' => 0,
			'items' => array()
		);
	}

	/**
	 * Get conversation details.
	 *
	 * @param  int    $conversation_id Conversation ID.
	 *
	 * @return array                   Conversation details.
	 */
	public function get_conversation( $conversation_id ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Getting conversation by ID: ' . $conversation_id );
		}

		$url = $this->api_url . 'conversations/' . $conversation_id .'/threads' ;
		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$response = wp_safe_remote_get( $url, $params );

		if (
			! is_wp_error( $response )
			&& 200 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'OK' ) )
		) {
			$items = json_decode( $response['body'], true );

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Conversation successfully retrieved by ID: ' . $conversation_id );
			}

			return wp_unslash( $items );
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Error while try to get the conversation by ID: ' . $conversation_id );
		}

		return array(
			'items' => array()
		);
	}

	/**
	 * Get conversation status.
	 *
	 * @param  string $slug
	 *
	 * @return string
	 */
	public function get_conversation_status( $slug ) {
		$status = array(
			'active'  => __( 'Active', 'woocommerce-help-scout' ),
			'pending' => __( 'Pending', 'woocommerce-help-scout' ),
			'closed'  => __( 'Closed', 'woocommerce-help-scout' ),
			'spam'    => __( 'Spam', 'woocommerce-help-scout' ),
		);

		if ( ! isset( $status[ $slug ] ) ) {
			return ucfirst( $slug );
		}

		return $status[ $slug ];
	}

	/**
	 * Create Conversations on Help Scout.
	 *
	 * @param  string $subject        Conversation subject.
	 * @param  string $message        Conversation body.
	 * @param  int    $customer_id    Customer ID.
	 * @param  string $customer_email Customer email.
	 * @param  string $fileData attachment files.
	 * @param  int $user_id to get billing first name & last name using $user_id in case customer id is not matched to the configured helpscoout account
	 *
	 * @return string                 Conversation URL.
	 */
	public function create_conversation( $subject, $message, $customer_id = 0, $customer_email = '', $fileData = '', $user_id = 0 ) {

		$data = array(
			'customer' => array(
				'email' => $customer_email
			),
			'subject'  => $subject,
			'mailboxId'=> $this->mailbox_id,
			'status'   => 'active',
			'type'     => 'email',
			'threads'  => array(
				array(
					'type'      => 'customer',
					'createdBy' => array(
						'email' => $customer_email,
						'type'  => 'customer'
					),
					'text'   => strip_tags( $message ),
					'status' => 'active'
				)
			)
		);

		if ( 0 < $customer_id ) {
			$data['customer']['id'] = $customer_id;
			$data['threads'][0]['createdBy']['id'] = $customer_id;
			$data['threads'][0]['customer']['id'] = $customer_id;
		}

		if ( ! empty( $this->assigned_to ) ) {
			$data['assignTo'] = $this->assigned_to;
		}

		if ( ! empty( $this->conversation_cc ) ) {
			$cc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_cc ) ) );
			if ( is_array( $cc ) && ! empty( $cc ) ) {
				$data['threads'][0]['cc'] = $cc;
			}
		}

		if ( ! empty( $this->conversation_bcc ) ) {
			$bcc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_bcc ) ) );
			if ( is_array( $bcc ) && ! empty( $bcc ) ) {
				$data['threads'][0]['bcc'] = $bcc;
			}
		}

		// Add filter to customize the conversation arguments.
		$data = apply_filters( 'woocommerce_help_scout_conversation_args', $data );
		$data = wp_unslash( $data );
		$url  = $this->api_url . 'conversations';

		$request_query = array(
			'autoReply' => 'true',
		);

		/**
		 * Filter the request query URL to create conversation.
		 *
		 * @since 1.3.0
		 *
		 * @param array $request_query Request query
		 */
		$request_query = apply_filters( 'woocommerce_help_scout_create_conversation_request_query', $request_query );
		$url = add_query_arg( $request_query, $url );

		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$post_params = $params;

		$post_params['body'] = json_encode( $data );
		$response = wp_safe_remote_post( $url, $post_params );
		//echo '<pre>';
		//print_r($response);
		//die;

		//Here we check for error message if customer id is not matched to the configured helpscoout account
		$arrError = json_decode($response['body']);
		$errorMsgCustomer = '';
		if(isset($arrError->_embedded->errors[0]->message)){
		    $errorMsgCustomer = $arrError->_embedded->errors[0]->message;
		}

		//Check for the condition if customer id is not matched to the configured helpscoout account
        if($errorMsgCustomer=='Customer must belong to the company'){
            //Here we call create_conversation_by_email function to create coonversation by using email id
            $response = $this->create_conversation_by_email( $subject, $message, $customer_id, $customer_email, $fileData, $user_id );
        }

		if (! is_wp_error( $response )
			&& 201 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'Created' ) )
			&& isset( $response['headers']['location'] )
		) {
			$conversion_api = esc_url( $response['headers']['location'] );
    		$conversion = wp_safe_remote_get( $conversion_api, $params );

    		if (
    			! is_wp_error( $conversion )
    			&& 200 == $conversion['response']['code']
    			&& ( 0 == strcmp( $conversion['response']['message'], 'OK' ) )
    		) {
    			$conversion_data = json_decode( $conversion['body'], true );
    			$id              = intval( $conversion_data['id'] );
    			$number          = intval( $conversion_data['number'] );

    			if(!empty($fileData[0]['name'])){
    				//$this->conversations_attachment($id, $number, $fileData);
    				$this->create_thread_with_attachment($id, $number, $customer_id, $fileData);
    			}
    			if ( 'yes' == $this->debug ) {
    				$this->log->add( $this->id, 'Conversation created successfully! ID: ' . $id . ', Number: ' . $number );
    			}

    			return array(
    				'id'     => $id,
    				'number' => $number,
    				'status' => 1
    			);
    		}

    		if ( 'yes' == $this->debug ) {
    			$this->log->add( $this->id, 'Failed to get the conversation: ' . print_r( $conversion, true ) );
    		}
    	}

    	if ( 'yes' == $this->debug ) {
    		$this->log->add( $this->id, 'Failed to create the conversation: ' . print_r( $response, true ) );
    	}

    	return array(
    		'id'     => 0,
    		'number' => 0,
    		'status' => 0
    	);


}

	/**
	 * Create Conversations using email if customer id not matched in helpscout configured account.
	 *
	 * @param  string $subject        Conversation subject.
	 * @param  string $message        Conversation body.
	 * @param  int    $customer_id    Customer ID.
	 * @param  string $customer_email Customer email.
	 * @param  string $fileData attachment files.
	 * @param  int $user_id to get billing first name & last name using $user_id
	 *
	 * @return string                 Conversation URL.
	 */
function create_conversation_by_email( $subject, $message, $customer_id = 0, $customer_email, $fileData = '', $user_id = 0 ){
    $billing_fname = get_user_meta( $user_id, 'billing_first_name', true );
    $billing_lname = get_user_meta( $user_id, 'billing_last_name', true );
    $data = array(
			'customer' => array(
				'email' => $customer_email,
				'firstName' => $billing_fname,
				'lastName' => $billing_lname
			),
			'subject'  => $subject,
			'mailboxId'=> $this->mailbox_id,
			'status'   => 'active',
			'type'     => 'email',
			'threads'  => array(
				array(
					'type'      => 'customer',
					'createdBy' => array(
						'email' => $customer_email,
						'type'  => 'customer'
					),
					'text'   => strip_tags( $message ),
					'status' => 'active'
				)
			)
		);
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'create_conversation_by_email #' . print_r( $customer_id, true ) );
		}
		if ( 0 < $customer_id ) {
			//$data['customer']['id'] = $customer_id;
			//$data['threads'][0]['createdBy']['id'] = $customer_id;
			$data['threads'][0]['customer']['email'] = $customer_email;
			$data['threads'][0]['customer']['firstName'] = $billing_fname;
			$data['threads'][0]['customer']['lastName'] = $billing_lname;
		}

		if ( ! empty( $this->assigned_to ) ) {
			$data['assignTo'] = $this->assigned_to;
		}

		if ( ! empty( $this->conversation_cc ) ) {
			$cc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_cc ) ) );
			if ( is_array( $cc ) && ! empty( $cc ) ) {
				$data['threads'][0]['cc'] = $cc;
			}
		}

		if ( ! empty( $this->conversation_bcc ) ) {
			$bcc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_bcc ) ) );
			if ( is_array( $bcc ) && ! empty( $bcc ) ) {
				$data['threads'][0]['bcc'] = $bcc;
			}
		}

		// Add filter to customize the conversation arguments.
		$data = apply_filters( 'woocommerce_help_scout_conversation_args', $data );
		$data = wp_unslash( $data );
		$url  = $this->api_url . 'conversations';

		$request_query = array(
			'autoReply' => 'true',
		);

		/**
		 * Filter the request query URL to create conversation.
		 *
		 * @since 1.3.0
		 *
		 * @param array $request_query Request query
		 */
		$request_query = apply_filters( 'woocommerce_help_scout_create_conversation_request_query', $request_query );
		$url = add_query_arg( $request_query, $url );

		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$post_params = $params;

		$post_params['body'] = json_encode( $data );
		$response = wp_safe_remote_post( $url, $post_params );
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'create_conversation_by_email response #' . print_r( $response, true ) );
		}
		return $response;

}

	/**
	 * Create attachments in a conversation on Help Scout.
	 *
	 * @param  string $conversation_id  Conversation ID.
	 * @param  string $thread_id        Thread ID.
	 * @param  string $files   Attachment files.
	 *
	 * @return bool.
	 */

	public function conversations_attachment($conversation_id, $thread_id, $files) {

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Creating a thread in the conversation #' . $conversation_id . '...' );
		}
		/*if(!empty($files[0]['name'])){
			foreach($files as $singleAttach){
				$data['attachments'][] = array('fileName'=>$singleAttach['name'],'mimeType'=>$singleAttach['type'],'data'=>$singleAttach['data']);
			}
		}*/
		$data['attachments'] = array('fileName'=>$files[0]['name'],'mimeType'=>$files[0]['type'],'data'=>$files[0]['data']);
		// Add filter to customize the conversation arguments.
		$data = apply_filters( 'woocommerce_help_scout_thread_args', $data );
		$data = wp_unslash( $data );
		$url  = $this->api_url . 'conversations/' . $conversation_id.'/threads/'.$thread_id.'/attachments';

		$params = array(
			'body'    => json_encode( $data ),
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$response = wp_safe_remote_post( $url, $params );
		//echo '<pre>';
		//print_r($response); exit;
		if (
			! is_wp_error( $response )
			&& 201 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'Created' ) )
		) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Thread created successfully in the conversation #' . $conversation_id . '!' );
			}

			return true;
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Failed to create the thread in the conversation #' . $conversation_id . ': ' . print_r( $response, true ) );
		}

		return false;
	}

	/**
	 * Create threads with attachments in a conversation on Help Scout.
	 *
	 * @param  string $conversation_id  Conversation ID.
	 * @param  string $thread_id        Thread ID.
	 * @param  int    $customer_id      Customer ID.
	 * @param  string $files   Attachment files.
	 *
	 * @return bool.
	 */
	public function create_thread_with_attachment($conversation_id, $thread_id, $customer_id, $files) {

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Creating a thread in the conversation #' . $conversation_id . '...' );
		}

		$data['customer']['id'] = $customer_id;
		$data['text'] = 'Attachments';
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'create_thread_with_attachment #' . print_r( $data, true ) );
		}
		$data['attachments'] = [];
		if(!empty($files[0]['name'])){
			foreach($files as $singleAttach){
				$data['attachments'][] = array('fileName'=>$singleAttach['name'],'mimeType'=>$singleAttach['type'],'data'=>$singleAttach['data']);
			}
		}
		//print_r($data['attachments']); exit;
		// Add filter to customize the conversation arguments.
		$data = apply_filters( 'woocommerce_help_scout_thread_args', $data );
		$data = wp_unslash( $data );
		//$url  = $this->api_url . 'conversations/' . $conversation_id.'/threads/'.$thread_id.'/attachments' ;
		$url  = $this->api_url . 'conversations/' . $conversation_id.'/chats';

		$params = array(
			'body'    => json_encode( $data ),
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);



		$response = wp_safe_remote_post( $url, $params );
		//echo '<pre>';
		//print_r($response); exit;
		if (
			! is_wp_error( $response )
			&& 201 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'Created' ) )
		) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Thread created successfully in the conversation #' . $conversation_id . '!' );
			}

			return true;
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Failed to create the thread in the conversation #' . $conversation_id . ': ' . print_r( $response, true ) );
		}

		return false;
	}

	/**
	 * Create threads in a conversation on Help Scout.
	 *
	 * @param  string $conversation_id  Conversation ID.
	 * @param  string $message          Thread message.
	 * @param  int    $customer_id      Customer ID.
	 * @param  string $customer_email   Customer email.
	 *
	 * @return bool.
	 */
	public function create_thread( $conversation_id, $message, $customer_id = 0, $customer_email = '', $files = '' ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Creating a thread in the conversation #' . $conversation_id . '...' );
		}

		$data = array(
			'createdBy' => array(
				'email' => $customer_email,
				'type'  => 'customer'
			),
			'type'      => 'customer',
			'text'      => strip_tags( $message ),
			'status'    => 'active'
		);

		if ( 0 < $customer_id ) {
			$data['createdBy']['id'] = $customer_id;
			$data['customer']['id']  = $customer_id;
		}

		$data['attachments'] = [];
		if(!empty($files[0]['name'])){
			foreach($files as $singleAttach){
				$data['attachments'][] = array('fileName'=>$singleAttach['name'],'mimeType'=>$singleAttach['type'],'data'=>$singleAttach['data']);
			}
		}

		if ( ! empty( $this->assigned_to ) ) {
			$data['assignedTo'] = array( 'id' => $this->assigned_to );
		}

		if ( ! empty( $this->conversation_cc ) ) {
			$cc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_cc ) ) );
			if ( is_array( $cc ) && ! empty( $cc ) ) {
				$data['cc'] = $cc;
			}
		}

		if ( ! empty( $this->conversation_bcc ) ) {
			$bcc = array_map( 'sanitize_text_field', array_filter( explode( ',', $this->conversation_bcc ) ) );
			if ( is_array( $bcc ) && ! empty( $bcc ) ) {
				$data['bcc'] = $bcc;
			}
		}

		// Add filter to customize the conversation arguments.
		$data = apply_filters( 'woocommerce_help_scout_thread_args', $data );
		$data = wp_unslash( $data );
		$url  = $this->api_url . 'conversations/' . $conversation_id.'/customer' ;

		$params = array(
			'body'    => json_encode( $data ),
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$response = wp_safe_remote_post( $url, $params );

		if (
			! is_wp_error( $response )
			&& 201 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'Created' ) )
		) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Thread created successfully in the conversation #' . $conversation_id . '!' );
			}

			return true;
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Failed to create the thread in the conversation #' . $conversation_id . ': ' . print_r( $response, true ) );
		}

		return false;
	}

	/**
	 * Generate the order data.
	 *
	 * @param  WC_Order $order Order object.
	 *
	 * @return string          Order data.
	 */
	public function generate_order_data( $order ) {
		ob_start();

		// Break two lines to distance the user message.
		echo PHP_EOL . PHP_EOL;
		echo '****************************************************';
		echo PHP_EOL . PHP_EOL;
		echo __( 'Order data:', 'woocommerce-help-scout' );
		echo PHP_EOL . PHP_EOL;

		do_action( 'woocommerce_help_scout_conversation_order_data_before', $order );

		// Order meta.
		echo sprintf( __( 'Order number: %s', 'woocommerce-help-scout' ), $order->get_order_number() );
		echo PHP_EOL;
		$order_date = version_compare( WC_VERSION, '3.0', '<' ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' );
		echo sprintf( __( 'Order date: %s', 'woocommerce-help-scout' ), date_i18n( wc_date_format(), strtotime( $order_date ) ) );
		echo PHP_EOL . PHP_EOL;

		do_action( 'woocommerce_help_scout_conversation_order_data_meta', $order );

		// Products list. (Always plain text)
		wc_get_template( 'emails/plain/email-order-items.php', array(
			'order' => $order,
			'items' => $order->get_items(),
			'show_download_links' => false,
			'show_sku' => true,
			'show_purchase_note' => false,
			'show_image' => false,
			'plain_text' => true,
			'image_size' => ''
		) );

		echo '----------' . PHP_EOL . PHP_EOL;

		if ( $totals = $order->get_order_item_totals() ) {
			foreach ( $totals as $total ) {
				echo sanitize_text_field( $total['label'] . "\t " . $total['value'] ) . PHP_EOL;
			}
		}

		do_action( 'woocommerce_help_scout_conversation_order_data_after', $order );

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Retrieves the last order of a customer from a comment.
	 *
	 * @param  string $customer_email Customer email.
	 * @param  int    $user_id        User ID.
	 * @param  int    $product_id     Product ID.
	 *
	 * @return int                    Last order ID.
	 */
	protected function get_order_from_comment( $customer_email, $user_id, $product_id ) {
		global $wpdb;

		$emails = array();

		if ( $user_id ) {
			$user     = get_user_by( 'id', $user_id );
			$emails[] = $user->user_email;
		}

		if ( is_email( $customer_email ) ) {
			$emails[] = $customer_email;
		}

		if ( sizeof( $emails ) == 0 ) {
			return 0;
		}

		return $wpdb->get_var(
			$wpdb->prepare( "
				SELECT order_items.order_id
				FROM {$wpdb->prefix}woocommerce_order_items as order_items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
				LEFT JOIN {$wpdb->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
				WHERE
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
				postmeta.meta_value  IN ( '" . implode( "','", array_unique( $emails ) ) . "' ) OR
				(
				postmeta.meta_value = %s AND
				postmeta.meta_value > 0
				)
				)
				ORDER BY order_items.order_id DESC
				LIMIT 1
				", $product_id, $user_id
			)
		);
	}

	/**
	 * Comments to conversation.
	 *
	 * @return void
	 */
	public function comment_to_conversation() {
		if ( ! isset( $_GET['comment_to_conversation'] ) ) {
			return '';
		}

		global $wpdb;
		$comment_id = intval( $_GET['comment_to_conversation'] );

		// Get the post ID from the comment ID.
		$post_id = $wpdb->get_var(
			$wpdb->prepare( "
				SELECT comment_post_ID
				FROM $wpdb->comments
				WHERE comment_ID = %d
				", $comment_id )
		);

		// Stops if don't find a post id.
		if ( empty( $post_id ) ) {
			return '';
		}

		// Get the post type.
		$post_type = get_post_type( $post_id );

		// Sets the conversation data.
		$comment     = get_comment( $comment_id );
		$email       = $comment->comment_author_email;
		$subject     = sprintf( __( 'Comment in %s at %s', 'woocommerce-help-scout' ), get_the_title( $post_id ), $comment->comment_date );
		$subject     = apply_filters( 'woocommerce_help_scout_conversation_from_comment_subject', $subject, $comment );
		$description = $comment->comment_content;

		// Test the email.
		if ( ! is_email( $email ) ) {
			wp_redirect( esc_url_raw( add_query_arg( array( 'conversation_status' => 2 ), esc_url( admin_url( 'edit-comments.php' ) ) ) ) );
			exit;
		}

		// Test the content.
		if ( empty( $description ) ) {
			wp_redirect( esc_url_raw( add_query_arg( array( 'conversation_status' => 3 ), esc_url( admin_url( 'edit-comments.php' ) ) ) ) );
			exit;
		}

		// Get post data.
		if ( 'product' == $post_type ) {
			$order_id = $this->get_order_from_comment( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID );
			if ( $order_id ) {
				$order = new WC_Order( $order_id );
				$description .= $this->generate_order_data( $order );
			}
		}

		$author_last_name  = '';
		$comment_author    = explode( ' ', $comment->comment_author );

		if ( 1 == count( $comment_author ) ) {
			$author_first_name = $comment->comment_author;
		} else {
			$author_first_name = $comment_author[0];
			unset( $comment_author[0] );
			$author_last_name  = implode( ' ', $comment_author );
		}

		$customer_id = $this->get_customer_id( $comment->user_id, $email, $author_first_name, $author_last_name );
		$response    = $this->create_conversation( $subject, $description, $customer_id, $email );

		// Redirects to avoid reloads and displaying the success message.
		$args = array(
			'conversation_id'     => $response['id'],
			'conversation_number' => $response['number'],
			'conversation_status' => $response['status']
		);

		wp_redirect( esc_url_raw( add_query_arg( $args, admin_url( 'edit-comments.php' ) ) ) );
		exit;
	}

	/**
	 * Add actions item in comments columns.
	 *
	 * @param  array $columns Default columns.
	 *
	 * @return array          Add actions column.
	 */
	public function comments_columns( $columns ) {
		$columns[ $this->id . '_comment_actions' ] = __( 'Actions', 'woocommerce-help-scout' );

		return $columns;
	}

	/**
	 * Add actions content to comments columns.
	 *
	 * @param  string $column     Column ID.
	 * @param  int    $comment_id Comment ID.
	 *
	 * @return string             Column content.
	 */
	public function custom_comment_column( $column, $comment_id ) {
		if ( $this->id . '_comment_actions' == $column ) {
			echo '<p><a class="button" href="' . esc_url( admin_url( 'edit-comments.php' ) ) . '?comment_to_conversation=' . intval( $comment_id ) . '">' . __( 'Create a Conversation', 'woocommerce-help-scout' ) . '</a></p>';
		}
	}

	/**
	 * Displays notices in admin.
	 *
	 * @return string Error notices.
	 */
	public function admin_notices() {
		global $post;

		$screen = get_current_screen();

		if ( 'edit-comments' === $screen->id && isset( $_GET['conversation_status'] ) ) {

			switch ( intval( $_GET['conversation_status'] ) ) {
				case 0:
				echo '<div class="error"><p><strong>' . __( 'Help Scout Error', 'woocommerce-help-scout' ) . ':</strong> ' . __( 'Failed to create the conversation, please try again.', 'woocommerce-help-scout' ) . '</p></div>';
				break;
				case 1:
				$conversation_id  = isset( $_GET['conversation_id'] ) ? intval( $_GET['conversation_id'] ) : 0;
				$conversation_number  = isset( $_GET['conversation_number'] ) ? intval( $_GET['conversation_number'] ) : 0;
				$conversation_url = ( $conversation_id > 0 ) ? ' <a href="' . esc_url( $this->admin_url . 'conversation/' . $conversation_id . '/' . $conversation_number ) . '/">' . __( 'View conversation.', 'woocommerce-help-scout' ) . '</a>' : '';

				echo '<div class="updated"><p><strong>' . __( 'Help Scout', 'woocommerce-help-scout' ) . ':</strong> ' . __( 'Conversation created successfully.', 'woocommerce-help-scout' ) . $conversation_url . '</p></div>';
				break;
				case 2:
				echo '<div class="error"><p><strong>' . __( 'Help Scout Error', 'woocommerce-help-scout' ) . ':</strong> ' . __( 'This comment has not a valid email address.', 'woocommerce-help-scout' ) . '</p></div>';
				break;
				case 3:
				echo '<div class="error"><p><strong>' . __( 'Help Scout Error', 'woocommerce-help-scout' ) . ':</strong> ' . __( 'This review/comment is empty, needs some content to create the conversation!', 'woocommerce-help-scout' ) . '</p></div>';
				break;

				default:
				break;
			}
		}

	} 

	/**
	 * Generate a URL to our specific settings screen.
	 * @access public
	 * @since  1.3.4
	 * @return string Generated URL.
	 */
	public function get_settings_url () {
		return add_query_arg(
			array(
				'page'    => 'wc-settings',
				'tab'     => 'integration',
				'section' => 'help-scout',
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Adds a metabox for create conversations for orders.
	 *
	 * @return void
	 */
	public function add_order_conversation_metabox() {
		add_meta_box(
			$this->id . '-conversation',
			__( 'Report an issue', 'woocommerce-help-scout' ),
			array( $this, 'order_conversation_metabox_content' ),
			'shop_order',
			'side',
			'low'
		);
	}

	/**
	 * Order conversations metabox content.
	 *
	 * @param  int    $post_id Current order/post ID.
	 *
	 * @return string          Conversation fields.
	 */
	public function order_conversation_metabox_content( $post_id ) {
		include_once( 'views/html-admin-order-create-conversation.php' );
	}
	/**
	 * Order conversations auth token validation.
	 *
	 * @param  int    $mailbox_id.
	 *
	 * @return string auth token.
	 */
	public function check_authorization_still_valid() {


		$current_timestamp = time();
		$expire_timestamp =  get_option('helpscout_expires_in');
		if(!empty($expire_timestamp)) {
			//include a 5 minute buffer for long API calls.
			$expire_timestamp_with_buffer = $expire_timestamp - 300;

			//regenerate token if current token has expired
			if($expire_timestamp_with_buffer <= $current_timestamp) {

				return $this->regenrate_credentials();

			} else {
				return true;
			}
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, "Please try to generate credenials from Plugin's settings page.");
			}
			return false;
		}

	}

	/**
	 * Regenrate API credentials from Refresh Token
	 *
	 * @return boolean
	 */

	public function regenrate_credentials(){

		$url  = $this->api_url .'oauth2/token';

		$params = array(
			'client_id'				=>	$this->app_key,
			'client_secret'			=>	$this->app_secret,
			'grant_type'			=>	'refresh_token',
			'refresh_token'			=>	get_option('helpscout_access_refresh_token'),
		);
		$params = http_build_query($params);
			// generate token post through api
		$response  = wp_safe_remote_post( $url , array(
			'body' => $params
		));

		if($response['response']['code']=="200"){
			$tokenData = json_decode($response['body']);
			$expire_timestamp = time() + $tokenData->expires_in;
				/*echo "<b>Token Generated:-</b>";
				echo "</br>----------------------------------------</br>";
				echo $tokenData->access_token;
				echo "</br>----------------------------------------</br>";*/

				// update token related data in option table
				update_option('helpscout_access_token',$tokenData->access_token);
				update_option('helpscout_access_refresh_token',$tokenData->refresh_token);
				update_option('helpscout_access_token_type',$tokenData->token_type);
				update_option('helpscout_expires_in', $expire_timestamp);
				return true;
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, "Problem with generating API Access Token. Please verify credentials or Retry to generate credenials from Plugin's settings page.");
				return false;
			}

		}
	}

	/**
	 * Get conversation Subject.
	 *
	 * @param  int    $conversation_id Conversation ID.
	 *
	 * @return array Conversation Subject.
	*/
	public function get_conversation_thread_subject( $conversation_id ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Getting Thread Subject by ID: ' . $conversation_id );
		}

		$url = $this->api_url . 'conversations/' . $conversation_id;
		$params = array(
			'timeout' => 60,
			'headers' => array(
				'Content-Type' => 'application/json;charset=UTF-8',
				'Authorization' => 'Bearer ' .get_option('helpscout_access_token')
			)
		);

		$response = wp_safe_remote_get( $url, $params );

		if (
			! is_wp_error( $response )
			&& 200 == $response['response']['code']
			&& ( 0 == strcmp( $response['response']['message'], 'OK' ) )
		) {
			$items = json_decode( $response['body'], true );

			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, 'Conversation successfully retrieved by ID: ' . $conversation_id );
			}

			return wp_unslash( $items );
		}

		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, 'Error while try to get the conversation by ID: ' . $conversation_id );
		}

		return array(
			'items' => array()
		);
	}
}
