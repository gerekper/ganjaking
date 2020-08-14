<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
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

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC' ) ) {
	/**
	 * WooCommerce Active Campaign
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC {
		/**
		 * Plugin version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCAC_VERSION = '1.0.0';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAC
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Active Campaign API wrapper class
		 *
		 * @var \YITH_WCAC_API
		 * @since 1.0.0
		 */
		protected $active_campaign = null;

		/**
		 * Cachable requests
		 *
		 * @var array
		 * @since 1.0.0
		 */
		public $cachable_requests = array();

		/**
		 * Logger instance
		 *
		 * @var \WC_Logger
		 */
		protected $_log;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCAC
		 * @since 1.0.0
		 */
		public function __construct() {
			do_action( 'yith_wcac_startup' );

			// init plugin.
			add_action( 'init', array( $this, 'install' ), 5 );

			// load plugin-fw.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			// init api key.
			add_action( 'update_option_yith_wcac_active_campaign_api_url', array( $this, 'init_api' ) );
			add_action( 'update_option_yith_wcac_active_campaign_api_key', array( $this, 'init_api' ) );
			$this->init_api();

			// update checkout page.
			add_action( 'init', array( $this, 'add_subscription_checkout' ) );

			// update register page.
			add_action( 'woocommerce_register_form', array( $this, 'add_subscription_register' ) );

			// register subscription functions.
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'adds_order_meta' ), 10, 1 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'subscribe_on_checkout' ), 10, 1 );
			add_action( 'woocommerce_order_status_completed', array( $this, 'subscribe_on_completed' ), 15, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'add_order_tags' ), 10, 3 );

			// Shortcode.
			add_action( 'init', array( $this, 'register_shortcode' ) );
			add_action( 'init', array( $this, 'post_form_subscribe' ) );
			add_action( 'wp_ajax_yith_wcac_subscribe', array( $this, 'ajax_form_subscribe' ) );
			add_action( 'wp_ajax_nopriv_yith_wcac_subscribe', array( $this, 'ajax_form_subscribe' ) );

			// Inits widget.
			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			// Register page.
			add_action( 'user_register', array( $this, 'register_subscribe' ), 10, 1 );

		}

		/* === INSTALL METHODS === */

		/**
		 * Run pre-mission checklist
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install() {
			// install db.
			$this->install_db();

			// install logger.
			$this->install_log();

			// install cachable API.
			$this->install_cachable_requests();

			// checklist completed; stand by.
			do_action( 'yith_wcac_standby' );
		}

		/**
		 * Install db tables when updating to new version of db structure
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_db() {
			global $wpdb;

			// adds tables name in global $wpdb.
			$wpdb->yith_wcac_register = $wpdb->prefix . 'yith_wcac_register';
			$wpdb->yith_wcac_waiting_orders = $wpdb->prefix . 'yith_wcac_waiting_orders';
			$wpdb->yith_wcac_background_process_batches = $wpdb->prefix . 'yith_wcac_background_process_batches';
		}

		/**
		 * Install logger
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_log() {
			$this->_log = new WC_Logger();
		}

		/**
		 * Init cachable requests array
		 *
		 * @return void
		 * @since 1.1.2
		 */
		public function install_cachable_requests() {
			$this->cachable_requests = apply_filters(
				'yith_wcac_cachable_requests',
				array(
					'lists',
					'tags',
					'fields',
					'contacts',
					'connections',
					'users/me',
				)
			);
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/* === PRIVACY LOADER === */

		/**
		 * Loads privacy class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function privacy_loader() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCAC_INC . 'class.yith-wcac-privacy.php' );
				new YITH_WCAC_Privacy();
			}
		}

		/* === ENQUEUE SCRIPTS === */

		/**
		 * Enqueue scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			wp_enqueue_script( 'yith-wcac', YITH_WCAC_URL . 'assets/js' . $path . '/yith-wcac' . $prefix . '.js', array( 'jquery', 'jquery-blockui' ), YITH_WCAC_VERSION, true );

			wp_localize_script(
				'yith-wcac',
				'yith_wcac',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'actions'  => array(
						'subscribe_action' => 'yith_wcac_subscribe',
						'register_session_billing_email_action' => 'yith_wcac_register_session_billing_email',
					),
					'nonce' => array(
						'register_session_billing_email' => wp_create_nonce( 'register_session_billing_email' ),
					),
					'is_user_logged_in' => is_user_logged_in(),
					'abandoned_cart_enable_guest' => 'yes' === get_option( 'yith_wcac_store_integration_abandoned_cart_enable_guest', 'no' ),
					'abandoned_cart_enable_guest_after_tc' => 'yes' === get_option( 'yith_wcac_store_integration_abandoned_cart_enable_guest_after_tc', 'yes' ),
				)
			);
		}

		/**
		 * Enqueue scripts required by subscription form
		 *
		 * @return void
		 */
		public function enqueue_form() {
			global $wp_scripts, $woocommerce;

			// Enqueue scripts to form.
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

			wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
			wp_register_style( 'wc_select2', WC()->plugin_url() . '/assets/css/select2.css', array(), WC()->version );
			wp_register_style( 'yith-wcac-subscription-form-style', YITH_WCAC_URL . '/assets/css/frontend/yith-wcac-subscription-form.css', array( 'jquery-ui-style', 'wc_select2' ), self::YITH_WCAC_VERSION );

			wp_enqueue_style( 'yith-wcac-subscription-form-style' );

			wp_register_script( 'yith-wcac-subscription-form-script', YITH_WCAC_URL . '/assets/js/frontend' . $path . '/yith-wcac-subscription-form' . $prefix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-blockui', 'select2' ), self::YITH_WCAC_VERSION, true );
			wp_enqueue_script( 'yith-wcac-subscription-form-script' );

			do_action( 'before_newsletter_subscription_form' );
		}

		/* === HANDLE REQUEST TO ACTIVE CAMPAIGN === */

		/**
		 * Init Api class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_api() {
			$api_url = get_option( 'yith_wcac_active_campaign_api_url' );
			$api_key = get_option( 'yith_wcac_active_campaign_api_key' );
			if ( ! empty( $api_url ) && ! empty( $api_key ) ) {
				set_time_limit( 0 );
				$this->active_campaign = new YITH_WCAC_API( $api_url, $api_key );
			} else {
				$this->active_campaign = null;
			}
		}

		/**
		 * Retrieve lists registered for current API Key
		 *
		 * @return array Array of available list, in id -> name format
		 * @since 1.0.0
		 */
		public function retrieve_lists() {
			$results      = $this->do_request( 'lists' );
			$list_options = array();

			if ( isset( $results->lists ) ) {
				foreach ( $results->lists as $list ) {
					if ( ! isset( $list->id ) || ! isset( $list->name ) ) {
						continue;
					}
					$list_options[ $list->id ] = $list->name;
				}
			}

			return $list_options;
		}

		/**
		 * Retrieve lists registered for current API Key with all data
		 *
		 * @return array Array of available list with all data
		 * @since 1.0.0
		 */
		public function retrieve_full_lists() {
			$results      = $this->do_request( 'lists' );
			$list_options = array();

			if ( isset( $results->lists ) ) {
				foreach ( $results->lists as $list ) {
					if ( ! isset( $list->id ) || ! isset( $list->name ) ) {
						continue;
					}

					$list_options[ $list->id ] = [
						'name'             => $list->name,
						'subscriber_count' => $this->count_list_contacts( $list->id ),
					];
				}
			}

			return $list_options;
		}

		/**
		 * Retrieve tags registered for current API Key
		 *
		 * @param array $filter_tags Array of tag ids to retrieve.
		 *
		 * @return array Array of available tags
		 * @since 1.0.0
		 */
		public function retrieve_tags( $filter_tags = [] ) {
			$tags_options = array();
			$results      = $this->do_request( 'tags' );
			$tags         = isset( $results->tags ) ? $results->tags : array();

			if ( ! empty( $tags ) ) {
				foreach ( $tags as $tag ) {
					if ( ! isset( $tag->id ) || ! isset( $tag->tag ) ) {
						continue;
					}

					if ( ! empty( $filter_tags ) && ! in_array( $tag->id, $filter_tags ) ) {
						continue;
					}

					$tags_options[ $tag->id ] = $tag->tag;
				}
			}

			return $tags_options;
		}

		/**
		 * Retrieve fields registered for current API Key
		 *
		 * @param bool $force_update Whether to force update of cached informations.
		 *
		 * @return array Array of available fields
		 * @since 1.0.0
		 */
		public function retrieve_fields( $force_update = false ) {
			$fields         = [];
			$default_fields = array(
				'email'      => array(
					'title' => __( 'Email Address', 'yith-woocommerce-active-campaign' ),
					'type'  => 'email',
				),
				'first_name' => array(
					'title' => __( 'First name', 'yith-woocommerce-active-campaign' ),
					'type'  => 'text',
				),
				'last_name'  => array(
					'title' => __( 'Last name', 'yith-woocommerce-active-campaign' ),
					'type'  => 'text',
				),
				'telephone'  => array(
					'title' => __( 'Telephone', 'yith-woocommerce-active-campaign' ),
					'type'  => 'text',
				),
			);

			$results = $this->do_request( 'fields', 'GET', [], [], $force_update );

			if ( isset( $results->fields ) ) {
				foreach ( $results->fields as $field ) {
					if ( ! isset( $field->id ) ) {
						continue;
					}

					$fields[ $field->id ] = array(
						'title' => $field->title,
						'type'  => $field->type,
					);

					if ( 'dropdown' == $field->type || 'listbox' == $field->type || 'radio' == $field->type || 'checkbox' == $field->type ) {

						// if no option found, skip this field.
						if ( ! isset( $results->fieldOptions ) ) {
							unset( $fields[ $field->id ] );
							continue;
						}

						$field_option_ids = isset( $field->options ) ? $field->options : [];
						$option_ids = wp_list_pluck( $results->fieldOptions, 'id' );
						$options = [];

						foreach ( $field_option_ids as $option_id ) {
							if( ! in_array( $option_id, $option_ids ) ){
								continue;
							}

							$option_index = array_search( $option_id, $option_ids );
							$options[ $option_id ] = $results->fieldOptions[ $option_index ];
						}

						$fields[ $field->id ]['options'] = $options;
					}
				}
			}

			ksort( $fields );

			$fields = $default_fields + $fields;

			return $fields;
		}

		/**
		 * Returns ID field for the first contact that matches email address passed as argument
		 *
		 * @param string $email_address Email address to search.
		 * @return object|bool Contact record on Active Campaign DB, or false on failure
		 */
		public function retrieve_contact_by_email( $email_address ) {
			if ( ! is_email( $email_address ) ) {
				return false;
			}

			// retrieve contacts with a specific email address.
			$results = $this->do_request( 'contacts', 'GET', [ 'email' => $email_address ] );

			if ( ! isset( $results->contacts ) ) {
				return false;
			}

			// by default, retrieve only first occurrence.
			$contact = array_shift( $results->contacts );

			// double check that contact exists.
			if ( ! isset( $contact->id ) ) {
				return false;
			}

			// finally return contact.
			return $contact;
		}

		/**
		 * Retrieve contactTags for a contact on ActiveCampaign
		 *
		 * @param int   $contact_id ID of the contact on Active Campaign.
		 * @param array $filter_tags Array of tags slugs to retrieve.
		 * @return array Array of contactTags
		 */
		public function retrieve_contact_tags( $contact_id, $filter_tags = [] ) {
			if ( ! $contact_id ) {
				return [];
			}

			$results = $this->do_request( "contacts/{$contact_id}/contactTags" );

			if ( ! isset( $results->contactTags ) ) {
				return [];
			}

			$contact_tags = $results->contactTags;

			// if filter param isset, filter just required tags.
			if ( ! empty( $filter_tags ) ) {
				foreach ( $contact_tags as $index => $contact_tag ) {
					if ( ! isset( $contact_tag->tag ) || ! in_array( $contact_tag->tag, $filter_tags ) ) {
						unset( $contact_tags[ $index ] );
					}
				}
			}

			// return contactTags for the contact.
			return $contact_tags;
		}

		/**
		 * Counts contact in a give list
		 *
		 * @param int $list_id List id.
		 * @return int Contact list
		 */
		public function count_list_contacts( $list_id ) {
			$results = $this->do_request(
				'contacts',
				'GET',
				[
					'listid' => $list_id,
				]
			);

			if ( ! isset( $results->meta ) ) {
				return 0;
			}

			return $results->meta->total;
		}

		/**
		 * Register a new contact, or updates his/her basic informations
		 *
		 * @param array $args Array of parameters for api call; it should be formatted as follows
		 * [
		 *   'email'     => '',
		 *   'firstName' => '',
		 *   'lastName'  => '',
		 *   'phone'     => '',
		 * ].
		 *
		 * @return bool|object Contact object, or false on failure
		 */
		public function update_contact( $args ) {
			$args = $this->_translate_old_subscription_params( $args );

			$defaults = [
				'email'     => '',
				'firstName' => '',
				'lastName'  => '',
				'phone'     => '',
			];

			$args = shortcode_atts( $defaults, $args );

			if ( empty( $args['email'] ) ) {
				return false;
			}

			$api_params = [
				'contact' => $args,
			];

			$results = $this->do_request( 'contact/sync', 'POST', array(), apply_filters( 'yith_wcac_subscribe_args', $api_params ) );


			if ( ! isset( $results->contact ) ) {
				return false;
			}

			return $results->contact;
		}

		/**
		 * Update contact list status
		 *
		 * @param object $contact Contact object.
		 * @param array  $args    Array of parameters for api call; it should be formatted as follows
		 * [
		 *   'list'    => '',
		 *   'status' => '',
		 * ].
		 *
		 * @return bool|object Contact object, or false on failure
		 */
		public function update_contact_list( $contact, $args ) {
			if ( ! isset( $contact->id ) ) {
				return false;
			}

			$args = $this->_translate_old_subscription_params( $args );

			$defaults = [
				'list'    => '',
				'contact' => $contact->id,
				'status' => '',
			];

			$args = shortcode_atts( $defaults, $args );

			$api_params = [
				'contactList' => $args,
			];

			$results = $this->do_request( 'contactLists', 'POST', array(), apply_filters( 'yith_wcac_subscribe_args', $api_params ) );

			if ( ! isset( $results->contacts ) || ! isset( $results->contacts[0] ) ) {
				return false;
			}

			return $results->contacts[0];
		}

		/**
		 * Update or create values of the fields for a contact
		 *
		 * @param object $contact Customer object.
		 * @param array  $args    Array of parameters for the API call; it should be formatted as follows
		 * [
		 *   'fieldValues' => [
		 *     [
		 *       'field' => '',
		 *       'value' => '',
		 *     ],
		 *     ...
		 *   ]
		 * ].
		 *
		 * @return bool Status of the operation
		 */
		public function update_field_values( $contact, $args ) {
			if ( ! isset( $contact->id ) ) {
				return false;
			}

			$args = $this->_translate_old_subscription_params( $args );

			if ( empty( $args['fieldValues'] ) ) {
				return false;
			}

			foreach ( $args['fieldValues'] as $field_value ) {
				$defaults = [
					'field'    => '',
					'contact' => $contact->id,
					'value' => '',
				];

				$field_value = shortcode_atts( $defaults, $field_value );

				$api_params = [
					'fieldValue' => $field_value,
				];

				$this->do_request( 'fieldValues', 'POST', array(), $api_params );
			}

			return true;
		}

		/**
		 * Subscribe contact to a list of tags
		 *
		 * @param object $contact Customer object.
		 * @param array  $args    Array of parameters for the API call; it should be formatted as follows
		 * [
		 *   'contactTags' => [
		 *     [
		 *       'tag' => '',
		 *     ],
		 *     ...
		 *   ]
		 * ].
		 *
		 * @return bool Status of the operation
		 */
		public function update_contact_tags( $contact, $args ) {
			if ( ! isset( $contact->id ) ) {
				return false;
			}

			$args = $this->_translate_old_subscription_params( $args );

			if ( empty( $args['contactTags'] ) ) {
				return false;
			}

			foreach ( $args['contactTags'] as $contact_tag ) {
				$defaults = [
					'contact' => $contact->id,
					'tag'     => '',
				];

				$contact_tag = shortcode_atts( $defaults, $contact_tag );

				$api_params = [
					'contactTag' => $contact_tag,
				];

				$this->do_request( 'contactTags', 'POST', array(), $api_params );
			}

			return true;
		}

		/**
		 * Synchronize contact defined by args
		 *
		 * @param array $args the arguments that needed on Active Campaign to make synchronization.
		 *
		 * @return mixed API response array with the result request.
		 * @throws Exception Object describing error occurred during sync procedure.
		 * @since 1.0.0
		 */
		public function synchronize_contact( $args ) {
			// check if email address is correctly set.
			if ( ! isset( $args['email'] ) || ! is_email( $args['email'] ) ) {
				throw new Exception( __( 'Email address is missing or malformed', 'yith-woocommerce-active-campaign' ) );
			}

			// register contact if needed.
			if ( ! $contact = $this->update_contact( $args ) ) {
				throw new Exception( __( 'An error occurred while trying to subscribe user; please, try again later', 'yith-woocommerce-active-campaign' ) );
			}

			// register contact to list.
			if ( ! $contact = $this->update_contact_list( $contact, $args ) ) {
				throw new Exception( __( 'An error occurred while subscribing user to the list; please, try again later', 'yith-woocommerce-active-campaign' ) );
			}

			// register fields for contact.
			$this->update_field_values( $contact, $args );

			// register tags for contact.
			$this->update_contact_tags( $contact, $args );

			return true;
		}

		/**
		 * Unsubscribe user by email
		 *
		 * @param string $list_id List from which user should be unsubscribed.
		 * @param string $email   Email of the contact that should be unsubscribed.
		 *
		 * @return mixed API response array with the result request.
		 * @since 1.0.0
		 */
		public function unsubscribe( $list_id, $email ) {
			$contact = $this->retrieve_contact_by_email( $email );

			if ( ! $contact || ! isset( $contact->ID ) ) {
				return false;
			}

			$res = $this->do_request(
				'contactLists',
				'POST',
				[],
				[
					'contactList' => [
						'list'    => $list_id,
						'contact' => $contact->ID,
						'status'  => 2,
					],
				]
			);

			return $res;
		}

		/**
		 * Send a request to active campaign servers
		 *
		 * @param string $request      API handle to call (e.g. 'lists/list').
		 * @param string $method       HTTP method for the request.
		 * @param array  $query        Associative array of params sent as query string of the request.
		 * @param array  $body         Associative array of params sent as body of the request (will be json_encoded).
		 * @param bool   $force_update Whether to force update of values in cache.
		 * @param array  $args         Array of parameters used to alter request behaviour.
		 *
		 * @return mixed API response (as an associative array)
		 * @since 1.0.0
		 */
		public function do_request( $request, $method = 'GET', $query = array(), $body = array(), $force_update = false, $args = array() ) {
			if ( is_null( $this->active_campaign ) ) {
				return false;
			}

			$api_url        = get_option( 'yith_wcac_active_campaign_api_url' );
			$api_key        = get_option( 'yith_wcac_active_campaign_api_key' );
			$transient_name = 'yith_wcac_' . md5( $api_url . $api_key );
			$data           = get_transient( $transient_name );
			$args_index     = md5( http_build_query( array_merge( $body, $query ) ) );

			if ( 'GET' === $method && in_array( $request, $this->cachable_requests ) && ! $force_update && ! empty( $data ) && isset( $data[ $request ] ) && isset( $data[ $request ][ $args_index ] ) ) {
				return $data[ $request ][ $args_index ];
			}

			try {
				$result = $this->active_campaign->call( $method, $request, $body, $query, $args );

				if ( in_array( $request, $this->cachable_requests ) ) {
					$data[ $request ][ $args_index ] = $result;

					set_transient( $transient_name, $data, apply_filters( 'yith_wcac_transient_expiration', DAY_IN_SECONDS ) );
				}

				return $result;
			} catch ( GuzzleHttp\Exception\RequestException $e ) {
				$code = $e->getCode();

				$response = array(
					'code'   => $code,
					'status' => false,
				);

				switch ( $code ) {
					case '400':
						$response['message'] = _x( 'The request could not be validated, as it probably contains malformed data.', 'API error message', 'yith-woocommerce-active-campaign' );
						break;
					case '403':
						$response['message'] = _x( 'The request could not be authenticated or the authenticated user is not authorized to access the requested resource.', 'API error message', 'yith-woocommerce-active-campaign' );
						break;
					case '404':
						$response['message'] = _x( 'The requested resource does not exist.', 'API error message', 'yith-woocommerce-active-campaign' );
						break;
					case '429':
						$response['message'] = _x( 'You\'re being rate limited! Please, wait a couple of minutes before submitting further requests to ActiveCampaign API', 'API error message', 'yith-woocommerce-active-campaign' );
						break;
					case '422':
						$body = @json_decode( (string) $e->getResponse()->getBody(), true );

						if ( isset( $body['errors'] ) ) {
							$errors_list = wp_list_pluck( $body['errors'], 'title' );

							// translators: 1. Error list.
							$response['message'] = sprintf( _x( 'The following errors occurred while processing API request: %s', 'API error message', 'yith-woocommerce-active-campaign' ), implode( ' | ', $errors_list ) );
							break;
						}

						// fails back to default when no errors array is provided in answer body.
					default:
						$response['message'] = $e->getMessage();
				}

				return $response;
			} catch ( Throwable $e ) {
				return array(
					'status'  => false,
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
				);
			}
		}

		/**
		 * Map params for subscription request formatted for v1 api into v3 structure
		 *
		 * @param array $args Array of parameters for api v1
		 * @param array Array of parameters for api v3
		 */
		private function _translate_old_subscription_params( $args ) {
			$output = [];
			$map = [
				'email'      => 'email',
				'first_name' => 'firstName',
				'last_name'  => 'lastName',
				'phone'      => 'phone',
			];

			// map old params to new ones.
			foreach ( $map as $old_param => $new_param ) {
				if ( ! isset( $args[ $old_param ] ) ) {
					continue;
				}

				$output[ $new_param ] = $args[ $old_param ];
			}

			// process list parameters.
			if ( isset( $args['p'] ) ) {
				// retrieve list id.
				$lists = array_values( $args['p'] );
				$list_id = array_pop( $lists );

				// retrieve status.
				$status = ( isset( $args['status'] ) && isset( $args['status'][ $list_id ] ) ) ? $args['status'][ $list_id ] : 1;

				$output = array_merge(
					$output,
					[
						'list'   => $list_id,
						'status' => $status,
					]
				);
			}

			// process fields.
			foreach ( $args as $key => $value ) {
				if ( ! preg_match( '/field\[([0-9]+),0]/', $key, $matches ) ) {
					continue;
				}

				$field_id = $matches[1];
				$field_value = $value;

				if ( ! isset( $output['fieldValues'] ) ) {
					$output['fieldValues'] = [];
				}

				$output['fieldValues'][] = [
					'field' => $field_id,
					'value' => $field_value,
				];
			}

			// process tags.
			if ( isset( $args['tags'] ) ) {
				$tags = is_array( $args['tags'] ) ? $args['tags'] : explode( ',', $args['tags'] );
				$tags = array_filter( $tags );

				if ( ! isset( $output['fieldValues'] ) ) {
					$output['contactTags'] = [];
				}

				foreach ( $tags as $tag_id ) {
					$output['contactTags'][] = [
						'tag' => $tag_id,
					];
				}
			}

			return $output;
		}

		/* === ADDS FRONTEND CHECKBOX === */

		/**
		 * Register action to print subscription items on Checkbox page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_subscription_checkout() {
			$positions_hook_relation = apply_filters(
				'yith_wcac_checkbox_position_hook',
				array(
					'above_customer'    => 'woocommerce_checkout_before_customer_details',
					'below_customer'    => 'woocommerce_checkout_after_customer_details',
					'above_place_order' => 'woocommerce_review_order_before_submit',
					'below_place_order' => 'woocommerce_review_order_after_submit',
					'above_total'       => 'woocommerce_review_order_before_order_total',
					'above_billing'     => 'woocommerce_checkout_billing',
					'below_billing'     => 'woocommerce_after_checkout_billing_form',
					'above_shipping'    => 'woocommerce_checkout_shipping',
				)
			);

			$trigger           = get_option( 'yith_wcac_checkout_trigger', 'never' );
			$show_checkbox     = 'yes' == get_option( 'yith_wcac_checkout_subscription_checkbox' );
			$checkbox_position = get_option( 'yith_wcac_checkout_subscription_checkbox_position' );

			// Print Checkbox Subscription on Checkout page.
			if ( 'never' != $trigger && $show_checkbox ) {
				if ( ! in_array( $checkbox_position, array_keys( $positions_hook_relation ) ) ) {
					$checkbox_position = 'below_customer';
				}

				$hook = $positions_hook_relation[ $checkbox_position ];
				add_action( $hook, array( $this, 'print_subscription_checkbox' ) );
			}

			$advanced_options            = get_option( 'yith_wcac_advanced_integration', array() );
			$selected_show_tags_position = isset( $advanced_options['show_tags_position'] ) ? $advanced_options['show_tags_position'] : 'below_customer';
			$integration_mode            = get_option( 'yith_wcac_active_campaign_integration_mode', false );

			// Print Checks Tags Subscriptions on Checkout page.
			if ( 'never' != $trigger && ! empty( $advanced_options['show_tags'] ) && 'advanced' == $integration_mode ) {

				$hook = $positions_hook_relation[ $selected_show_tags_position ];

				add_action( $hook, array( $this, 'print_subscription_tags' ) );
			}
		}

		/**
		 * Prints subscription checkbox
		 *
		 * @param string $context Context for subscription form (checkout/registration).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_subscription_checkbox( $context = 'checkout' ) {
			$label_option   = 'yith_wcac_checkout_subscription_checkbox_label';
			$checked_option = 'yith_wcac_checkout_subscription_checkbox_default';

			if ( 'register' == $context ) {
				$label_option   = 'yith_wcac_register_subscription_checkbox_label';
				$checked_option = 'yith_wcac_register_subscription_checkbox_default';
			}

			$checkbox_label   = get_option( $label_option );
			$checkbox_checked = 'yes' == get_option( $checked_option );

			if ( function_exists( 'wc_privacy_policy_page_id' ) ) {
				$privacy_link   = sprintf( '<a href="%s">%s</a>', get_the_permalink( wc_privacy_policy_page_id() ), apply_filters( 'yith_wcac_privacy_policy_page_label', __( 'Privacy Policy', 'yith-woocommerce-active-campaign' ) ) );
				$checkbox_label = str_replace( '%privacy_policy%', $privacy_link, $checkbox_label );
			}

			$attributes = array(
				'checkbox_label'   => $checkbox_label,
				'checkbox_checked' => $checkbox_checked,
				'context'          => $context,
			);

			yith_wcac_get_template( 'active-campaign-subscription-checkbox', $attributes );
		}

		/**
		 * Prints subscription tags
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_subscription_tags() {
			$advanced_options            = get_option( 'yith_wcac_advanced_integration', array() );
			$selected_show_tags          = isset( $advanced_options['show_tags'] ) ? $advanced_options['show_tags'] : array();
			$show_tags_label             = isset( $advanced_options['show_tags_label'] ) ? $advanced_options['show_tags_label'] : '';
			$selected_show_tags_position = isset( $advanced_options['show_tags_position'] ) ? $advanced_options['show_tags_position'] : '';

			if ( empty( $selected_show_tags ) ) {
				return;
			}

			// retrieve tags from api.
			$selected_show_tags = $this->retrieve_tags( $selected_show_tags );

			$attributes = array(
				'advanced_options'            => $advanced_options,
				'selected_show_tags'          => $selected_show_tags,
				'show_tags_label'             => $show_tags_label,
				'selected_show_tags_position' => $selected_show_tags_position,
			);

			yith_wcac_get_template( 'active-campaign-subscription-tags', $attributes );
		}

		/* === HANDLES ORDER SUBSCRIPTION === */

		/**
		 * Adds metas to order post, saving active campaign informations
		 *
		 * @param int $order_id Order id.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function adds_order_meta( $order_id ) {
			$show_checkbox   = 'yes' == get_option( 'yith_wcac_checkout_subscription_checkbox' );
			$submitted_value = isset( $_POST['yith_wcac_subscribe_me'] ) ? 'yes' : 'no'; // phpcs:ignore
			$subscribe_tags  = isset( $_POST['yith_wcac_subscribe_tags'] ) ? $_POST['yith_wcac_subscribe_tags'] : 'no'; // phpcs:ignore

			update_post_meta( $order_id, '_yith_wcac_show_checkbox', $show_checkbox );
			update_post_meta( $order_id, '_yith_wcac_submitted_value', $submitted_value );
			update_post_meta( $order_id, '_yith_wcac_subscribe_tags', $subscribe_tags );
		}

		/**
		 * Subscribe user to newsletter (called on order placed)
		 *
		 * @param int $order_id Order id.
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function subscribe_on_checkout( $order_id ) {
			$trigger         = get_option( 'yith_wcac_checkout_trigger' );
			$show_checkbox   = get_post_meta( $order_id, '_yith_wcac_show_checkbox', true );
			$submitted_value = get_post_meta( $order_id, '_yith_wcac_submitted_value', true );

			// return if admin don't want to subscribe users at this point.
			if ( 'created' != $trigger ) {
				return false;
			}

			// return if subscription checkbox is printed, but not submitted.
			if ( $show_checkbox && 'no' == $submitted_value ) {
				return false;
			}

			return $this->order_subscribe( $order_id );
		}

		/**
		 * Subscribe user to newsletter (called on order completed)
		 *
		 * @param int $order_id Order id.
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function subscribe_on_completed( $order_id ) {


			$trigger         = get_option( 'yith_wcac_checkout_trigger' );
			$show_checkbox   = get_post_meta( $order_id, '_yith_wcac_show_checkbox', true );
			$submitted_value = get_post_meta( $order_id, '_yith_wcac_submitted_value', true );

			// return if admin don't want to subscribe users at this point.
			if ( 'completed' != $trigger ) {
				return false;
			}

			// return if subscription checkbox is printed, but not submitted.
			if ( $show_checkbox && 'no' == $submitted_value ) {
				return false;
			}

			return $this->order_subscribe( $order_id );
		}

		/**
		 * Call subscribe API handle, to register user to a specific list
		 *
		 * @param int   $order_id Order id.
		 * @param array $args     Array of arguments.
		 *
		 * @return bool status of the operation
		 */
		public function order_subscribe( $order_id, $args = array() ) {
			$order = wc_get_order( $order_id );
			$res   = false;

			$list_id            = get_option( 'yith_wcac_active_campaign_list' );
			$contact_status     = get_option( 'yith_wcac_contact_status' );
			$integration_mode   = get_option( 'yith_wcac_active_campaign_integration_mode', 'simple' );
			$email              = yit_get_prop( $order, 'billing_email', true );
			$first_name         = yit_get_prop( $order, 'billing_first_name', true );
			$last_name          = yit_get_prop( $order, 'billing_last_name', true );
			$user_id            = yit_get_prop( $order, 'customer_user', true );

			if ( empty( $list_id ) ) {
				return false;
			}
			if ( 'simple' == $integration_mode ) {
				$selected_tags = get_option( 'yith_wcac_active_campaign_tags', array() );

				$args = array_merge(
					array(
						'email'        => yit_get_prop( $order, 'billing_email' ),
						'first_name'   => yit_get_prop( $order, 'billing_first_name' ),
						'last_name'    => yit_get_prop( $order, 'billing_last_name' ),
						'tags'         => $selected_tags,
						'p'            => array(
							$list_id => $list_id
						),
						'status'       => array(
							$list_id => $contact_status
						),
					),
					$args
				);

				$res = $this->synchronize_order_contact( $order_id, $args );

				if ( $res ) {
					if ( isset( $args['p'] ) ) {
						$list_id = array_values( $args['p'] );
						$list_id = array_pop( $list_id );
					}

					$this->_register_customer_subscribed_lists( $list_id, $email, $user_id, $order_id );

					// register personal data
					$personal_data = apply_filters( 'yith_wcac_customer_personal_data', array(
						'billing_first_name' => array(
							'label' => __( 'First name', 'yith-woocommerce-active-campaign' ),
							'value' => $first_name
						),
						'billing_last_name'  => array(
							'label' => __( 'Last name', 'yith-woocommerce-active-campaign' ),
							'value' => $last_name
						),
						'billing_email'      => array(
							'label' => __( 'Email', 'yith-woocommerce-active-campaign' ),
							'value' => $email
						)
					), $args, $order_id );

					if ( ! empty( $personal_data ) ) {
						$this->_register_customer_personal_data( $order_id, $personal_data );
					}

					yit_save_prop( $order, '_yith_wcac_customer_subscribed', true );
				}
			} else {
				$res              = true;
				$advanced_options = get_option( 'yith_wcac_advanced_integration', array() );
				$advanced_items   = isset( $advanced_options['items'] ) ? $advanced_options['items'] : array();
				$subscribe_tags   = get_post_meta( $order_id, '_yith_wcac_subscribe_tags', true );
				$subscribe_tags   = is_array( $subscribe_tags ) ? array_keys( $subscribe_tags ) : array();

				$args['tags']         = ! empty( $subscribe_tags ) ? $subscribe_tags : array();
				$args['email']        = $email;
				$args['first_name']   = $first_name;
				$args['last_name']    = $last_name;
				$args['status']       = array(
					$list_id => $contact_status
				);
				$args['p']            = array(
					$list_id => $list_id
				);
				if ( ! empty( $advanced_items ) ) {
					foreach ( $advanced_items as $option ) {

						// Checks conditions
						$selected_conditions = isset( $option['conditions'] ) ? $option['conditions'] : array();
						if ( ! empty( $selected_conditions ) ) {
							if ( ! $this->_check_conditions( $selected_conditions, $order_id ) ) {
								continue;
							}
						}

						$args['tags']         = isset( $option['tags'] ) ? array_merge( $args['tags'], $option['tags'] ) : $args['tags'];
						$args['status']       = array(
							$option['list'] => $contact_status
						);
						// set list id to current section
						$args['p'] = array(
							$option['list'] => $option['list']
						);

						// manage fields
						$selected_fields = isset( $option['fields'] ) ? $option['fields'] : array();
						$field_structure = $this->_create_field_structure( $selected_fields, $order_id );
						if ( ! empty( $field_structure ) ) {
							foreach ( $field_structure as $key => $field_item ) {
								if ( 'first_name' == $key ) {
									$args['first_name'] = $field_item;
								} elseif ( 'last_name' == $key ) {
									$args['last_name'] = $field_item;
								} elseif ( 'email' == $key ) {
									$args['email'] = $field_item;
								} else {
									$args[ 'field[' . $key . ',0]' ] = $field_item;
								}
							}
						}

						$res = $this->synchronize_order_contact( $order_id, $args );

						if ( $res ) {
							$lists = isset( $args['p'] ) ? array_values( $args['p'] ) : array();
							$list  = ! empty( $lists ) ? array_pop( $lists ) : $list_id;
							$this->_register_customer_subscribed_lists( $list, $email, $user_id, $order_id );
						}
					}
				} else {

					$res = $this->synchronize_order_contact( $order_id, $args );

					if ( $res ) {
						$this->_register_customer_subscribed_lists( isset( $args['p'] ) ? array_pop( array_values( $args['p'] ) ) : $list_id, $email, $user_id, $order_id );
					}
				}
			}

			return $res;
		}

		/**
		 * Synchronize contact with Active Campaign access, add or edit the contact
		 *
		 * @param int   $order_id Order id.
		 * @param array $args     The array arguments..
		 *
		 * @return boolean
		 * @since 1.0.0
		 */
		public function synchronize_order_contact( $order_id, $args ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return false;
			}

			do_action( 'yith_wcac_user_subscribing', $order_id );

			try {
				$status = $this->synchronize_contact( apply_filters( 'yith_wcac_synchronize_order_contact_params', $args, $order_id ) );
				// translators: 1. Subscribed email.
				$message = apply_filters( 'yith_wcac_subscribed_message', sprintf( __( 'Correctly subscribed %1$s.', 'yith-woocommerce-active-campaign' ), $args['email'] ), null, $args['email'] );
			} catch ( Exception $e ) {
				$status = false;
				$message = $e->getMessage();
			}

			$order->add_order_note( $message );

			do_action( 'yith_wcac_user_subscribed', $order_id );

			return $status;

		}

		/**
		 * Add tags to subscriber as a consequence of specific status change of the order
		 *
		 * @param int    $order_id   Order id.
		 * @param string $old_status Old status.
		 * @param string $new_status New status.
		 *
		 * @return void
		 */
		public function add_order_tags( $order_id, $old_status, $new_status ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$customer_email = $order->get_billing_email();

			// retrieve contact id.
			$contact = $this->retrieve_contact_by_email( $customer_email );

			if ( ! $contact ) {
				return;
			}

			// remove old tags.
			$old_status  = str_replace( 'wc-', '', $old_status );
			$status_tags = get_option( 'yith_wcac_tags_order_' . $old_status, array() );
			$contact_tags = $this->retrieve_contact_tags( $contact->id, $status_tags );

			if ( ! empty( $contact_tags ) ) {
				foreach ( $contact_tags as $tag ) {
					if ( ! isset( $tag->id ) ) {
						continue;
					}

					// delete each tag registered for old order status.
					$this->do_request(
						"contactTags/{$tag->id}",
						'DELETE'
					);
				}
			}

			// add new tags.
			$new_status  = str_replace( 'wc-', '', $new_status );
			$tags_to_add = get_option( 'yith_wcac_tags_order_' . $new_status, array() );

			// add product-related tags.
			if ( in_array( $new_status, array( 'completed', 'processing' ) ) && ! in_array( $old_status, array( 'completed', 'processing' ) ) ) {
				$items = $order->get_items();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						/**
						 * @var $item \WC_Order_Item_Product
						 */
						$product_id   = $item->get_product_id();
						$variation_id = $item->get_variation_id();

						$product = wc_get_product( $product_id );

						if ( $product ) {
							$product_meta = $product->get_meta( 'yith_wcac_product_tags', true );

							if ( ! empty( $product_meta ) ) {
								$tags_to_add = array_merge( $tags_to_add, $product_meta );
							}
						}

						if ( $variation_id ) {
							$variation = wc_get_product( $variation_id );

							if ( $variation ) {
								$variation_meta = $variation->get_meta( 'yith_wcac_product_tags', true );

								if ( ! empty( $variation_meta ) ) {
									$tags_to_add = array_merge( $tags_to_add, $variation_meta );
								}
							}
						}
					}
				}
			}

			if ( ! empty( $tags_to_add ) ) {
				$args['contactTags'] = [];

				foreach ( $tags_to_add as $tag ) {
					$args['contactTags'][] = [
						'tag' => $tag,
					];
				}

				$this->update_contact_tags( $contact, $args );
			}
		}

		/**
		 * Create structure to register fields to a specific user
		 *
		 * @param array $selected_fields Array of selected fields to register.
		 * @param int   $order_id        Order id.
		 *
		 * @return array A valid array to use in subscription request
		 * @since 1.0.0
		 */
		protected function _create_field_structure( $selected_fields, $order_id ) {
			if ( empty( $selected_fields ) ) {
				return array();
			}

			$order = wc_get_order( $order_id );

			if ( empty( $order ) ) {
				return array();
			}

			// populate customer when missing.
			if ( is_admin() ) {
				WC()->customer = new WC_Customer( $order->get_customer_id() );
			}

			if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ){
				// Session class, handles session data for users - can be overwritten if custom handler is needed.
				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

				include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-session.php' );
				include_once( WC()->plugin_path() . '/includes/class-wc-session-handler.php' );
				// Class instances.
				WC()->session  = new $session_class();
				WC()->customer = new WC_Customer();
			}

			$field_structure = array();

			foreach ( $selected_fields as $field ) {

				$field_value                            = yit_get_prop( $order, $field['checkout'], true );
				$field_structure[ $field['merge_var'] ] = $field_value;


				$checkout_fields = apply_filters( 'yith_wcac_checkout_fields', WC()->checkout()->get_checkout_fields() );

				foreach ( $checkout_fields as $group => $fields ) {
					if ( isset( $fields[ $field['checkout'] ] ) ) {
						$label = $fields[ $field['checkout'] ]['label'];
						break;
					}
				}

				if ( empty( $label ) ) {
					$label = $field['checkout'];
				}
				$this->_register_customer_personal_data( $order_id, $field['checkout'], $label, $field_value );
			}

			return $field_structure;
		}

		/**
		 * Check if selected conditions are matched
		 *
		 * @param array $selected_conditions Array of selected conditions to match.
		 * @param int   $order_id            Order id.
		 *
		 * @return boolean True, if all conditions are matched; false otherwise
		 * @since 1.0.0
		 */
		protected function _check_conditions( $selected_conditions, $order_id ) {
			$order            = wc_get_order( $order_id );
			$condition_result = true;

			if ( empty( $selected_conditions ) ) {
				return true;
			}

			foreach ( $selected_conditions as $condition ) {
				$condition_type = $condition['condition'];
				switch ( $condition_type ) {
					case 'product_in_cart':
						$set_operator      = $condition['op_set'];
						$selected_products = ! is_array( $condition['products'] ) ? explode( ',', $condition['products'] ) : $condition['products'];
						$items             = $order->get_items( 'line_item' );
						$products_in_cart  = array();

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								if ( is_object( $item ) ) {
									/**
									 * @var $item \WC_Order_Item_Product
									 */
									$products_in_cart[] = $item->get_product_id();
								} else {
									if ( ! empty( $item['product_id'] ) && ! in_array( $item['product_id'], $products_in_cart ) ) {
										$products_in_cart[] = $item['product_id'];
									}

									if ( ! empty( $item['variation_id'] ) && ! in_array( $item['variation_id'], $products_in_cart ) ) {
										$products_in_cart[] = $item['variation_id'];
									}
								}
							}
							$products_in_cart = array_unique( $products_in_cart );
						}

						switch ( $set_operator ) {
							case 'contains_one':
								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									$found = false;
									foreach ( (array) $selected_products as $product ) {
										if ( in_array( $product, $products_in_cart ) ) {
											$found = true;
											break;
										}
									}

									if ( ! $found ) {
										$condition_result = false;
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
							case 'contains_all':
								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									foreach ( (array) $selected_products as $product ) {
										if ( ! in_array( $product, $products_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
							case 'not_contain':
								if ( ! empty( $selected_products ) && ! empty( $products_in_cart ) ) {
									foreach ( (array) $selected_products as $product ) {
										if ( in_array( $product, $products_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_products ) ) {
									$condition_result = false;
								}

								break;
						}

						break;
					case 'product_cat_in_cart':
						$set_operator  = $condition['op_set'];
						$selected_cats = $condition['prod_cats'];
						$items         = $order->get_items( 'line_item' );
						$cats_in_cart  = array();

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								/**
								 * @var $item array|\WC_Order_Item_Product
								 */
								$product_id = is_object( $item ) ? $item->get_product_id() : $item['product_id'];
								$item_terms = get_the_terms( $product_id, 'product_cat' );

								if ( ! empty( $item_terms ) ) {
									foreach ( $item_terms as $term ) {
										if ( ! in_array( $term->term_id, $cats_in_cart ) ) {
											$cats_in_cart[] = $term->term_id;
										}
									}
								}
							}
						}

						switch ( $set_operator ) {
							case 'contains_one':
								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									$found = false;
									foreach ( (array) $selected_cats as $cat ) {
										if ( in_array( $cat, $cats_in_cart ) ) {
											$found = true;
											break;
										}
									}

									if ( ! $found ) {
										$condition_result = false;
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
							case 'contains_all':
								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									foreach ( (array) $selected_cats as $cat ) {
										if ( ! in_array( $cat, $cats_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
							case 'not_contain':
								if ( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ) {
									foreach ( (array) $selected_cats as $cat ) {
										if ( in_array( $cat, $cats_in_cart ) ) {
											$condition_result = false;
											break;
										}
									}
								} elseif ( ! empty( $selected_cats ) ) {
									$condition_result = false;
								}

								break;
						}

						break;
					case 'order_total':
						$number_operator = $condition['op_number'];
						$threshold       = $condition['order_total'];
						$order_total     = $order->get_total();

						switch ( $number_operator ) {
							case 'less_than':
								if ( ! ( $order_total < $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'less_or_equal':
								if ( ! ( $order_total <= $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'equal':
								if ( ! ( $order_total == $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_or_equal':
								if ( ! ( $order_total >= $threshold ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_than':
								if ( ! ( $order_total > $threshold ) ) {
									$condition_result = false;
								}
								break;
						}

						break;
					case 'custom':
						$operator       = $condition['op_mixed'];
						$field_key      = $condition['custom_key'];
						$expected_value = $condition['custom_value'];

						// retrieve field value (first check in post meta).
						$field = yit_get_prop( $order, $field_key, true );

						// retrieve field value (then check in $_REQUEST superglobal).
						if ( empty( $field ) ) {
							$field = isset( $_REQUEST[ $field_key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $field_key ] ) ) : '';
						}

						// nothing found? condition failed.
						if ( empty( $field ) ) {
							$condition_result = false;
							break;
						}

						switch ( $operator ) {
							case 'is':
								if ( ! ( strcmp( $field, $expected_value ) == 0 ) ) {
									$condition_result = false;
								}
								break;
							case 'not_is':
								if ( ! ( strcmp( $field, $expected_value ) != 0 ) ) {
									$condition_result = false;
								}
								break;
							case 'contains':
								if ( ! ( strpos( $field, $expected_value ) !== false ) ) {
									$condition_result = false;
								}
								break;
							case 'not_contains':
								if ( ! ( strpos( $field, $expected_value ) === false ) ) {
									$condition_result = false;
								}
								break;
							case 'less_than':
								if ( ! ( $field < $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'less_or_equal':
								if ( ! ( $field <= $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'equal':
								if ( ! ( $field == $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_or_equal':
								if ( ! ( $field >= $expected_value ) ) {
									$condition_result = false;
								}
								break;
							case 'greater_than':
								if ( ! ( $field > $expected_value ) ) {
									$condition_result = false;
								}
								break;
						}

						break;
				}

				if ( ! $condition_result ) {
					break;
				}
			}

			return $condition_result;
		}

		/* === HANDLE SHORTCODE === */

		/**
		 * Register newsletter subscription form shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_shortcode() {
			add_shortcode( 'yith_wcac_subscription_form', array( $this, 'print_subscription_form' ) );
		}

		/**
		 * Retrieve default attributes from options, in order to print subscription form
		 *
		 * @param string $context Current context (shortcode/widget)
		 *
		 * @eturn array Array of default args
		 */
		public function get_default_attributes( $context ) {

			// Generate default attributes.
			// Array defaults with general attributes.
			$defaults = array(
				'list'                         => get_option( 'yith_wcac_' . $context . '_active-campaign_list' ),
				'title'                        => get_option( 'yith_wcac_' . $context . '_title' ),
				'submit_label'                 => get_option( 'yith_wcac_' . $context . '_submit_button_label' ),
				'success_message'              => get_option( 'yith_wcac_' . $context . '_success_message' ),
				'show_privacy_field'           => get_option( 'yith_wcac_' . $context . '_show_privacy_field' ),
				'privacy_label'                => get_option( 'yith_wcac_' . $context . '_privacy_label' ),
				'hide_form_after_registration' => get_option( 'yith_wcac_' . $context . '_hide_after_registration' ),
				'status'                       => get_option( 'yith_wcac_' . $context . '_status' ),
				'show_tags'                    => get_option( 'yith_wcac_' . $context . '_active-campaign_show_tags' ),
				'tags_label'                   => get_option( 'yith_wcac_' . $context . '_active-campaign_tags_label' ),
				'widget'                       => ( 'widget' != $context ) ? 'no' : 'yes',
			);

			// Set privacy options.

			$defaults['show_privacy_field'] = 'yes' == $defaults['show_privacy_field'];

			if ( function_exists( 'wc_privacy_policy_page_id' ) ) {
				$privacy_link              = sprintf( '<a href="%s">%s</a>', get_the_permalink( wc_privacy_policy_page_id() ), apply_filters( 'yith_wcac_privacy_policy_page_label', __( 'Privacy Policy', 'yith-woocommerce-active-campaign' ) ) );
				$defaults['privacy_label'] = str_replace( '%privacy_policy%', $privacy_link, $defaults['privacy_label'] );
			}

			// Set default fields attributes.

			$selected_fields = get_option( 'yith_wcac_' . $context . '_custom_fields' );
			$def_fields      = array();

			// Loop selected fields defined from options and implement format on $def_fields.
			if ( ! empty( $selected_fields ) ) {
				foreach ( $selected_fields as $field ) {
					$def_fields[ $field['merge_var'] ] = array(
						'name'      => $field['name'],
						'merge_var' => $field['merge_var'],
					);
				}
			}

			// Set default tags attributes.
			$selected_tags = get_option( 'yith_wcac_' . $context . '_active-campaign_tags' );
			$def_tags      = ! empty( $selected_tags ) ? implode( ',', $selected_tags ) : '';

			// Set default show_tags attributes.
			$selected_show_tags = get_option( 'yith_wcac_' . $context . '_active-campaign_show_tags' );
			$def_show_tags      = ! empty( $selected_show_tags ) ? implode( ',', $selected_show_tags ) : '';

			// Set default styles attributes.
			// Array with general defaults attributes...
			$style_defaults = array(
				'enable_style'           => get_option( 'yith_wcac_' . $context . '_style_enable' ),
				'round_corners'          => get_option( 'yith_wcac_' . $context . '_subscribe_button_round_corners', 'no' ),
				'background_color'       => get_option( 'yith_wcac_' . $context . '_subscribe_button_background_color' ),
				'text_color'             => get_option( 'yith_wcac_' . $context . '_subscribe_button_color' ),
				'border_color'           => get_option( 'yith_wcac_' . $context . '_subscribe_button_border_color' ),
				'background_hover_color' => get_option( 'yith_wcac_' . $context . '_subscribe_button_background_hover_color' ),
				'text_hover_color'       => get_option( 'yith_wcac_' . $context . '_subscribe_button_hover_color' ),
				'border_hover_color'     => get_option( 'yith_wcac_' . $context . '_subscribe_button_border_hover_color' ),
				'custom_css'             => get_option( 'yith_wcac_' . $context . '_custom_css' ),
			);


			// Now merge all defaults attributes on the same array.
			$defaults = array_merge(
				$defaults,
				array(
					'fields'    => $def_fields,
					'tags'      => $def_tags,
					'show_tags' => $def_show_tags,
				),
				$style_defaults
			);

			return $defaults;
		}

		/**
		 * Print newsletter subscription form shortcode
		 *
		 * @param array  $attributes Array of attributes passed to shortcode.
		 * @param string $content    Shortcode content.
		 *
		 * @return string Shortcode template
		 * @since 1.0.0
		 */
		public function print_subscription_form( $attributes, $content = '' ) {
			// generate unique shortcode id.
			$unique_id = mt_rand();

			$defaults = $this->get_default_attributes( 'shortcode' );

			// Merge the attributes defined by options with $attributes.
			$attributes              = shortcode_atts( $defaults, $attributes );
			$attributes['unique_id'] = $unique_id;

			// generate structure for fields.
			if ( ! is_array( $attributes['fields'] ) ) {
				$fields_chunk    = array();
				$fields_subchunk = array_filter( explode( '|', $attributes['fields'] ) );
				if ( ! empty( $fields_subchunk ) ) {
					foreach ( $fields_subchunk as $subchunk ) {
						if ( strpos( $subchunk, ',' ) === false ) {
							continue;
						}

						list( $name, $merge_var ) = explode( ',', $subchunk );
						$fields_chunk[ $merge_var ] = array(
							'name' => $name,
							'merge_var' => $merge_var,
						);
					}
				}
				$attributes['fields'] = $fields_chunk;
			}

			// generate structure for tags.
			if ( ! is_array( $attributes['show_tags'] ) ) {
				$tags = explode( ',', $attributes['show_tags'] );

				if ( ! empty( $tags ) ) {
					$attributes['show_tags'] = $this->retrieve_tags( $tags );
				}
			}

			// define context.
			$attributes['context'] = ( isset( $attributes['widget'] ) && 'yes' == $attributes['widget'] ) ? 'widget' : 'shortcode';

			// replace "yes"/"no" values with true/false.
			$attributes['enable_style']  = ( 'yes' == $attributes['enable_style'] );
			$attributes['round_corners'] = ( 'yes' == $attributes['round_corners'] );

			if ( empty( $attributes['list'] ) ) {
				return '';
			}

			// retrieve fields informations from active-campaign.
			$attributes['fields_data'] = $this->retrieve_fields();

			if ( empty( $attributes['fields_data'] ) ) {
				return '';
			}

			// retrieve style information for template.
			$attributes['style'] = '';
			if ( $attributes['enable_style'] ) {
				$attributes['style'] = sprintf(
					'#subscription_form_%d input[type="submit"]{
					    color: %s;
					    border: 1px solid %s;
					    border-radius: %dpx;
					    background: %s;
					}
					#subscription_form_%d input[type="submit"]:hover{
					    color: %s;
					    border: 1px solid %s;
					    background: %s;
					}
					%s',
					$unique_id,
					$attributes['text_color'],
					$attributes['border_color'],
					( $attributes['round_corners'] ) ? 5 : 0,
					$attributes['background_color'],
					$unique_id,
					$attributes['text_hover_color'],
					$attributes['border_hover_color'],
					$attributes['background_hover_color'],
					$attributes['custom_css']
				);
			}

			$attributes['use_placeholders'] = apply_filters( 'yith_wcac_use_placeholders_instead_of_labels', false );

			// retrieve template for the subscription form.

			$this->enqueue_form();

			ob_start();
			yith_wcac_get_template( 'active-campaign-subscription-form', $attributes );

			return ob_get_clean();
		}

		/**
		 * Print single subscription form field
		 *
		 * @param int    $id                   Unique id of the shortcode.
		 * @param array  $panel_options        Array of options setted in settings panel.
		 * @param array  $active_campaign_data Array of data retreieved from active campaign server.
		 * @param string $context              Context for the field (shortcode/widget/etc).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_field( $id, $panel_options, $active_campaign_data, $context = 'shortcode' ) {
			if ( empty( $active_campaign_data ) ) {
				return;
			}

			$use_placeholders = apply_filters( 'yith_wcac_use_placeholders_instead_of_labels', false );
			$placeholder      = ! empty( $panel_options['name'] ) && $use_placeholders ? $panel_options['name'] : '';

			$attributes = array(
				'id'                   => $id,
				'panel_options'        => $panel_options,
				'active_campaign_data' => $active_campaign_data,
				'context'              => $context,
				'use_placeholders'     => $use_placeholders,
				'placeholder'          => $placeholder,
			);

			yith_wcac_get_template( $active_campaign_data['type'], $attributes, 'types' );

		}

		/* === HANDLE WIDGET === */

		/**
		 * Registers widget used to show subscription form
		 *
		 * @return void
		 * @since1.0.0
		 */
		public function register_widget() {
			register_widget( 'YITH_WCAC_Widget' );
		}

		/* === HANDLE FORM SUBSCRIPTION === */

		/**
		 * Register a user using form fields
		 *
		 * @param string $context Context for the form.
		 * @param int    $user_id User id.
		 *
		 * @return array Array with status code and messages
		 * @since 1.0.0
		 */
		public function form_subscribe( $context = 'shortcode', $user_id = null ) {
			// phpcs:disable WordPress.Security.NonceVerification
			$args                      = array();
			$yith_wcac_shortcode_items = isset( $_POST['yith_wcac_shortcode_items'] ) ? wc_clean( $_POST['yith_wcac_shortcode_items'] ) : array(); // phpcs:ignore

			// Set default args that has nor been setted like fields from AC.
			if ( in_array( $context, array( 'shortcode', 'widget' ) ) ) {
				$email = isset( $yith_wcac_shortcode_items['default']['email'] ) ? $yith_wcac_shortcode_items['default']['email'] : '';
			} elseif ( 'register' == $context ) {
				$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
			}

			if ( ! empty( $user_id ) && 'register' != $context ) {
				$user = get_user_by( 'ID', $user_id );

				$first_name = $user->first_name;
				$last_name  = $user->last_name;
			} else {
				$first_name = isset( $yith_wcac_shortcode_items['default']['first_name'] ) ? $yith_wcac_shortcode_items['default']['first_name'] : '';
				$last_name  = isset( $yith_wcac_shortcode_items['default']['last_name'] ) ? $yith_wcac_shortcode_items['default']['last_name'] : '';
				$telephone  = isset( $yith_wcac_shortcode_items['default']['telephone'] ) ? $yith_wcac_shortcode_items['default']['telephone'] : '';
			}

			$ac_tags = get_option( 'yith_wcac_' . $context . '_active-campaign_tags' );

			$list               = isset( $yith_wcac_shortcode_items['hidden']['list'] ) ? $yith_wcac_shortcode_items['hidden']['list'] : '';
			$status             = ! empty( $list ) ? isset( $yith_wcac_shortcode_items['hidden']['status'] ) ? $yith_wcac_shortcode_items['hidden']['status'] : '' : '';
			$show_tags          = isset( $yith_wcac_shortcode_items['default']['show_tags'] ) ? implode( ',', $yith_wcac_shortcode_items['default']['show_tags'] ) : '';
			$tags               = implode( ',', (array) $ac_tags );
			$success_message    = ! empty( $_POST['success_message'] ) ? wp_kses_post( wp_unslash( $_POST['success_message'] ) ) : __( 'Great! You\'re now subscribed to our newsletter', 'yith-woocommerce-active-campaign' );
			$show_privacy_field = isset( $_POST['show_privacy_field'] ) ? 'yes' == $_POST['show_privacy_field'] : false;
			$privacy_agreement  = isset( $_POST['privacy_agreement'] );

			// phpcs:enable WordPress.Security.NonceVerification

			if ( ! empty( $email ) ) {
				$args['email'] = $email;
			} else {
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcac_missing_required_arguments_error_message', __( 'Email is required', 'yith-woocommerce-active-campaign' ) ),
				);
			}
			if ( ! empty( $first_name ) ) {
				$args['first_name'] = $first_name;
			}
			if ( ! empty( $telephone ) ) {
				$args['phone'] = $telephone;
			}
			if ( ! empty( $last_name ) ) {
				$args['last_name'] = $last_name;
			}
			if ( ! empty( $list ) ) {
				$args['p'] = array( $list => $list );
			}
			if ( $show_privacy_field && ! $privacy_agreement ) {
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcac_privacy_error_message', __( 'You must agree privacy agreement', 'yith-woocommerce-active-campaign' ) ),
				);
			}
			if( ! apply_filters( 'yith_wcac_form_validated', true, $email, $_POST ) ){
				return array(
					'status'  => false,
					'code'    => false,
					'message' => apply_filters( 'yith_wcac_form_validation_error', __( 'There is an error with your data; try again later.', 'yith-woocommerce-active-campaign' ) ),
				);
			}

			if ( ! empty( $status ) ) {
				$args['status'] = array( $list => $status );
			}
			if ( ! empty( $show_tags ) | ! empty( $tags ) ) {
				$args['tags'] = $show_tags . ',' . $tags;
			}

			// Set fields args defined by AC.
			$fields = isset( $yith_wcac_shortcode_items['fields'] ) ? $yith_wcac_shortcode_items['fields'] : array();
			foreach ( $fields as $key => $field_item ) {
				$args[ 'field[' . $key . ',0]' ] = is_array( $field_item ) ? '||' . implode( '||', $field_item ) . '||' : $field_item;
			}

			try {
				$status = $this->synchronize_contact( $args );
				// translators: 1. Subscribed email 2. List id.
				$message = apply_filters( 'yith_wcac_subscribed_message', sprintf( __( 'Correctly subscribed %1$s.', 'yith-woocommerce-active-campaign' ), $email ), $list, $email );
			} catch ( Exception $e ) {
				$status = false;
				$message = $e->getMessage();
			}

			$res = array(
				'status' => $status,
				'message' => $message,
			);

			if ( $status && is_user_logged_in() ) {
				// register subscribed list.
				$this->_register_customer_subscribed_lists( $list, $email, get_current_user_id() );
			}

			return $res;
		}

		/**
		 * Calls form_subscribe(), when posting form data, and adds woocommerce notice with result of the operation
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function post_form_subscribe() {

			if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['yith_wcac_subscribe_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcac_subscribe_nonce'] ) ), 'yith_wcac_subscribe' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

				$res = $this->form_subscribe( 'shortcode' );
				wc_add_notice( $res['message'], ( $res['status'] ) ? 'yith-wcac-success' : 'yith-wcac-error' );
			}
		}

		/* === FRONTEND REGISTER === */

		/**
		 * Add checkbox to subscribe to the newsletter on registration form
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_subscription_register() {
			$print_sub_register_enable = get_option( 'yith_wcac_register_subscription_checkbox_enable' );
			if ( 'yes' != $print_sub_register_enable ) {
				return;
			}

			// generate basic default array.
			$attributes = $this->get_default_attributes( 'register' );

			// define context.
			$attributes['context'] = 'register';

			// replace "yes"/"no" values with true/false.
			$attributes['enable_style'] = ( 'yes' == $attributes['enable_style'] );

			// retrieve fields informations from active-campaign.
			$attributes['fields_data'] = $this->retrieve_fields();

			if ( empty( $attributes['fields_data'] ) ) {
				return;
			}

			$attributes['style'] = '';

			if ( $attributes['enable_style'] ) {
				$attributes['style'] = sprintf( '%s', $attributes['custom_css'] );
			}

			$attributes['use_placeholders'] = apply_filters( 'yith_wcac_use_placeholders_instead_of_labels', false );

			$this->enqueue_form();

			yith_wcac_get_template( 'active-campaign-subscription-form-content', $attributes );

		}

		/**
		 * Subscribe after registration
		 *
		 * @param \WP_User $user User just registered.
		 * @return void
		 */
		public function register_subscribe( $user = null ) {
			// phpcs:disable WordPress.Security.NonceVerification

			$subscribe_user = true;
			if ( isset( $_POST['yith_wcac_subscribe_me_enabled'] ) ) {
				if ( ! isset( $_POST['yith_wcac_subscribe_me'] ) ) {
					$subscribe_user = false;
				}
			}

			if ( $subscribe_user && ! is_admin() ) {
				$res = $this->form_subscribe( 'register', $user );
				wc_add_notice( $res['message'], ( $res['status'] ) ? 'yith-wcac-success' : 'yith-wcac-error' );
			}

			// phpcs:enable WordPress.Security.NonceVerification
		}

		/* === HANDLES AJAX REQUESTS === */

		/**
		 * Calls form_subscribe(), from an AJAX request, and print JSON encoded version of its result
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_form_subscribe() {
			$context = 'shortcode';

			// phpcs:disable WordPress.Security.NonceVerification

			if (
				isset( $_POST['yith_wcac_shortcode_items'] ) &&
				isset( $_POST['yith_wcac_shortcode_items']['hidden'] ) &&
				isset( $_POST['yith_wcac_shortcode_items']['hidden']['context'] ) &&
				in_array(
					$_POST['yith_wcac_shortcode_items']['hidden']['context'],
					array(
						'shortcode',
						'widget',
						'register',
					)
				)
			) {
				$context = sanitize_text_field( wp_unslash( $_POST['yith_wcac_shortcode_items']['hidden']['context'] ) );
			}

			// phpcs:enable WordPress.Security.NonceVerification

			wp_send_json( $this->form_subscribe( $context ) );
		}

		/* === LOG METHODS === */

		/**
		 * Return single instance of logger class
		 *
		 * @return \WC_Logger
		 * @since 2.0.0
		 */
		public function get_logger() {
			return $this->_log;
		}

		/**
		 * Log messages to system logger
		 *
		 * @param string $message Log message.
		 * @param string $level   Type of log (emergency|alert|critical|error|warning|notice|info|debug).
		 */
		public function log( $message, $level = 'info' ) {
			$message = '(' . microtime() . ') ' . $message;

			$this->_log->add( 'yith_wcac', $message, $level );
		}

		/* === UTILITY METHODS === */

		/**
		 * Returns account name for currently set API key
		 *
		 * @return string|bool Account name, or false on failure
		 */
		public function calculate_account_name() {
			if ( is_null( $this->active_campaign ) ) {
				return false;
			}

			return $this->active_campaign->get_account_name();
		}

		/**
		 * Clear cached data in the transient
		 *
		 * @param string $endpoint Endpoint that needs to be flushed.
		 * @param array  $body     Array of query argoments, used to clear specific results.
		 * @param array  $query    Array of body arguments, used to clear specific results.
		 * @return void
		 */
		public function clear_cached_data( $endpoint, $body = [], $query = [] ) {
			$api_url        = get_option( 'yith_wcac_active_campaign_api_url' );
			$api_key        = get_option( 'yith_wcac_active_campaign_api_key' );
			$transient_name = 'yith_wcac_' . md5( $api_url . $api_key );
			$data           = get_transient( $transient_name );

			if ( ! in_array( $endpoint, $this->cachable_requests ) || ! isset( $data[ $endpoint ] ) ) {
				return;
			}

			if ( ! empty( $body ) || ! empty( $query ) ) {
				$args_index = md5( http_build_query( array_merge( $body, $query ) ) );

				if ( isset( $data[ $endpoint ][ $args_index ] ) ) {
					unset( $data[ $endpoint ][ $args_index ] );
				}
			} else {
				unset( $data[ $endpoint ] );
			}

			set_transient( $transient_name, $data, apply_filters( 'yith_wcac_transient_expiration', DAY_IN_SECONDS ) );
		}

		/**
		 * Register personal data sent to Active Campaign servers, within order meta
		 *
		 * @param int          $order_id Order id.
		 * @param string|array $arg1     ID of the data to save or array of arrays containing label and value to save.
		 * @param string|bool  $arg2     When $arg1 is an ID, this param will be used as label.
		 * @param string|bool  $arg3     When $arg1 is an ID, this param will be used as value.
		 *
		 * @return void
		 */
		protected function _register_customer_personal_data( $order_id, $arg1, $arg2 = false, $arg3 = false ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$data = is_array( $arg1 ) ? $arg1 : array(
				$arg1 => array(
					'label' => $arg2,
					'value' => $arg3,
				),
			);

			$previous_personal_data = yit_get_prop( $order, '_yith_wcac_personal_data', true );
			$previous_personal_data = ! empty( $previous_personal_data ) ? $previous_personal_data : array();
			$new_personal_data      = array_merge( $previous_personal_data, $data );

			yit_save_prop( $order, '_yith_wcac_personal_data', $new_personal_data );
		}

		/**
		 * Register subscribed lists, in order to easily unsubscribe user later
		 *
		 * @param string   $list_id  List id.
		 * @param string   $email    Email being subscribed.
		 * @param int      $user_id  User id.
		 * @param int|bool $order_id Order id.
		 *
		 * @return void
		 */
		protected function _register_customer_subscribed_lists( $list_id, $email, $user_id, $order_id = false ) {
			// register list within the customer.
			if ( $user_id ) {
				$order_subscribed_lists = get_user_meta( $user_id, '_yith_wcac_subscribed_lists', true );
				$order_subscribed_lists = ! empty( $order_subscribed_lists ) ? $order_subscribed_lists : array();

				if ( ! array_key_exists( $list_id, $order_subscribed_lists ) ) {
					$order_subscribed_lists[ $list_id ] = array();
				}

				if ( ! in_array( $email, $order_subscribed_lists[ $list_id ] ) ) {
					$order_subscribed_lists[ $list_id ][] = $email;

					update_user_meta( $user_id, '_yith_wcac_subscribed_lists', $order_subscribed_lists );
				}
			}

			// eventually register list within the order.
			if ( $order_id ) {
				$order = wc_get_order( $order_id );

				if ( $order ) {
					$order_subscribed_lists = yit_get_prop( $order, '_yith_wcac_subscribed_lists', true );
					$order_subscribed_lists = ! empty( $order_subscribed_lists ) ? $order_subscribed_lists : array();

					if ( ! array_key_exists( $list_id, $order_subscribed_lists ) ) {
						$order_subscribed_lists[ $list_id ] = array();
					}

					if ( ! in_array( $email, $order_subscribed_lists[ $list_id ] ) ) {
						$order_subscribed_lists[ $list_id ][] = $email;
						yit_save_prop( $order, '_yith_wcac_subscribed_lists', $order_subscribed_lists );
					}
				}
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAC
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	}
}
/**
 * Unique access to instance of YITH_WCAC class
 *
 * @return \YITH_WCAC
 * @since 1.0.0
 */
function YITH_WCAC() {
	return YITH_WCAC::get_instance();
}