<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

use \DrewM\MailChimp\MailChimp;

if ( ! class_exists( 'YITH_WCMC' ) ) {
	/**
	 * WooCommerce Mailchimp
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC {
		/**
		 * Current version of the API
		 *
		 * @var string
		 * @since 2.0.0
		 */
		const API_VERSION = '3.0';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMC
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Mailchimp API wrapper class
		 *
		 * @var \Mailchimp
		 * @since 1.0.0
		 */
		protected $mailchimp = null;

		/**
		 * A list of the operations executed during last batch session
		 *
		 * System collects them instead of performing direct calls, to process a single batch call
		 * System consider current execution as a batch session if defined( 'YITH_WCMC_DOING_BATCH' ) && YITH_WCMC_DOING_BATCH
		 *
		 * @var array
		 * @since 2.0.0
		 */
		protected $batch_ops = array();

		/**
		 * Logger instance
		 *
		 * @var \WC_Logger
		 */
		protected $_log;

		/**
		 * Cachable requests
		 *
		 * @var array
		 * @since 1.0.0
		 */
		public $cachable_requests = array();

		/**
		 * Available MailChimp API REST methods
		 *
		 * @var array
		 * @since 1.1
		 */
		public static $available_api_methods = array( 'get', 'post', 'delete', 'put', 'patch' );

		/**
		 * Call timeout (seconds)
		 *
		 * @var int
		 * @since 1.1
		 */
		public static $timeout = 600;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMC
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCMC_Premium' ) ) {
				return YITH_WCMC_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCMC::$instance ) ) {
					YITH_WCMC::$instance = new YITH_WCMC;
				}

				return YITH_WCMC::$instance;
			}
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCMC
		 * @since 1.0.0
		 */
		public function __construct() {
			// init plugin
			add_action( 'init', array( $this, 'install' ), 5 );

			// load plugin-fw
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			// init api key when updating license key
			add_action( 'update_option_yith_wcmc_mailchimp_api_key', array( $this, 'init_api' ) );

			// handle ajax requests
			add_action( 'wp_ajax_do_request_via_ajax', array( $this, 'do_request_via_ajax' ) );
			add_action( 'wp_ajax_retrieve_lists_via_ajax', array( $this, 'retrieve_lists_via_ajax' ) );
			add_action( 'wp_ajax_retrieve_groups_via_ajax', array( $this, 'retrieve_groups_via_ajax' ) );
			add_action( 'wp_ajax_retrieve_fields_via_ajax', array( $this, 'retrieve_fields_via_ajax' ) );

			// update checkout page
			add_action( 'init', array( $this, 'add_subscription_checkbox' ) );

			// register subscription functions
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'adds_order_meta' ), 10, 1 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'subscribe_on_checkout' ), 10, 1 );
			add_action( 'woocommerce_order_status_completed', array( $this, 'subscribe_on_completed' ), 15, 1 );
		}

		/**
		 * Run pre-mission checklist
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install() {
			// install api
			$this->install_api();

			// install db
			$this->install_db();

			// install logger
			$this->install_log();

			// install cachable API
			$this->install_cachable_requests();

			do_action( 'yith_wcmc_standby' );
		}

		/**
		 * Install API for the plugin
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_api() {
			$previous_version = str_replace( '.', '', get_option( 'yith_wcmc_api_version', '' ) );
			$current_version  = str_replace( '.', '', self::API_VERSION );

			$this->init_api();

			if ( $previous_version != $current_version ) {
				if ( $previous_version ) {
					$action = "yith_wcmc_api_{$previous_version}_to_{$current_version}";
				} else {
					$action = "yith_wcmc_api_{$current_version}";
				}

				do_action( $action );

				update_option( 'yith_wcmc_api_version', self::API_VERSION );
			}
		}

		/**
		 * Install db tables when updating to new version of db structure
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_db() {
			global $wpdb;

			// adds tables name in global $wpdb
			$wpdb->yith_wcmc_register = $wpdb->prefix . 'yith_wcmc_register';

			$current_db_version = get_option( 'yith_wcmc_db_version', '' );
			if ( version_compare( $current_db_version, YITH_WCMC_DB_VERSION, '>=' ) ) {
				return;
			}

			// perform related operation for specific db update
			add_action( 'yith_wcmc_update_db_2.0.0', array( $this, 'install_update_200' ) );

			do_action( 'yith_wcmc_update_db_' . YITH_WCMC_DB_VERSION );
			update_option( 'yith_wcmc_db_version', YITH_WCMC_DB_VERSION );
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
			$this->cachable_requests = apply_filters( 'yith_wcmc_cachable_requests', array(
				'lists',
				'lists\/(.*)',
				'users\/profile',
				'ecommerce\/stores\/(.*)'
			) );
		}

		/**
		 * Updated options when moving from 1.x to 2.x version
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function install_update_200() {
			$options_to_update = array(
				array(
					'name'        => 'yith_wcmc_mailchimp_groups',
					'index'       => '',
					'set'         => false,
					'list_option' => 'yith_wcmc_mailchimp_list',
					'list_index'  => ''
				),
				array(
					'name'        => 'yith_wcmc_advanced_integration',
					'index'       => 'groups',
					'set'         => true,
					'list_option' => '',
					'list_index'  => 'list'
				),
				array(
					'name'        => 'yith_wcmc_shortcode_mailchimp_groups',
					'index'       => '',
					'set'         => false,
					'list_option' => 'yith_wcmc_shortcode_mailchimp_list',
					'list_index'  => ''
				),
				array(
					'name'        => 'yith_wcmc_shortcode_mailchimp_groups_selectable',
					'index'       => '',
					'set'         => false,
					'list_option' => 'yith_wcmc_shortcode_mailchimp_list',
					'list_index'  => ''
				),
				array(
					'name'        => 'yith_wcmc_widget_mailchimp_groups',
					'index'       => '',
					'set'         => false,
					'list_option' => 'yith_wcmc_widget_mailchimp_list',
					'list_index'  => ''
				),
				array(
					'name'        => 'yith_wcmc_widget_mailchimp_groups_selectable',
					'index'       => '',
					'set'         => false,
					'list_option' => 'yith_wcmc_widget_mailchimp_list',
					'list_index'  => ''
				)
			);

			foreach ( $options_to_update as $option ) {
				$option_value = get_option( $option['name'], array() );

				if ( ! $option_value ) {
					continue;
				}

				if ( ! empty( $option['list_option'] ) ) {
					$list = get_option( $option['list_option'] );
				}

				if ( $option['set'] && ! empty( $option_value ) ) {
					foreach ( $option_value as $id => $subset ) {
						if ( ! empty( $option['index'] ) && isset( $subset[ $option['index'] ] ) ) {
							if ( empty( $option['list_option'] ) && isset( $option['list_index'] ) && ! empty( $subset[ $option['list_index'] ] ) ) {
								$list = $subset[ $option['list_index'] ];
							}

							if ( $list ) {
								$option_value[ $id ][ $option['index'] ] = $this->_update_groups_to_200_format( $subset[ $option['index'] ], $list );
							}
						} elseif ( empty( $option['index'] ) && ! empty( $option['list_option'] ) ) {
							$option_value[ $id ] = $this->_update_groups_to_200_format( $subset, $list );
						}
					}
				} else {
					if ( ! empty( $option['index'] ) && isset( $option_value[ $option['index'] ] ) ) {
						if ( empty( $option['list_option'] ) && isset( $option['list_index'] ) && ! empty( $option_value[ $option['list_index'] ] ) ) {
							$list = $option_value[ $option['list_index'] ];
						}

						if ( $list ) {
							$option_value[ $option['index'] ] = $this->_update_groups_to_200_format( $option_value[ $option['index'] ], $list );
						}
					} elseif ( empty( $option['index'] ) && ! empty( $option['list_option'] ) ) {
						$option_value = $this->_update_groups_to_200_format( $option_value, $list );
					}
				}

				update_option( $option['name'], $option_value );
			}
		}

		/**
		 * Update groups to new format
		 *
		 * @param $group_options array Group array formatted in old style groupID-interestName
		 * @param $list          string Related list
		 *
		 * @return array Array of groups formatted in new style groupID-interestID
		 * @since 2.0.0
		 */
		protected function _update_groups_to_200_format( $group_options, $list ) {
			$new_option_value = array();
			$api_key          = get_option( 'yith_wcmc_mailchimp_api_key' );

			if ( ! empty( $group_options ) && ! empty( $list ) && $api_key ) {
				$legacy_groups = yith_wcmc_retrieve_legacy_groups( $list );
				$list_groups   = $this->retrieve_groups( $list );

				foreach ( $group_options as $option ) {
					list( $group_id, $interest_name ) = explode( '-', $option );

					if ( ! isset( $legacy_groups[ $group_id ] ) ) {
						continue;
					}

					$group_name = $legacy_groups[ $group_id ]['name'];

					/**
					 * It seems that previous group ID are no longer valid on API 3.0
					 * A complete new ID is show through API
					 *
					 * We need to rely just on interest and group name, but this could lead to unexpected behaviour
					 *
					 * In this  case first encountered will be considered as the current option, and this may lead to major issues
					 *
					 * Old format: group_id-Group Name
					 * New format: interest_category_id-interest_id
					 */
					foreach ( $list_groups as $new_option => $name ) {
						if ( $name == "{$group_name} - {$interest_name}" ) {
							$new_option_value[] = $new_option;
						}
					}
				}
			}

			return $new_option_value;
		}

		/* === PLUGIN FW LOADER === */

		/**
		 * Loads plugin fw, if not yet created
		 *
		 * @return void
		 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		public function privacy_loader() {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCMC_INC . 'class.yith-wcmc-privacy.php' );
				new YITH_WCMC_Privacy();
			}
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
		 * @param $message string Log message
		 * @param $level   string Type of log (emergency|alert|critical|error|warning|notice|info|debug)
		 */
		public function log( $message, $level = 'info' ) {
			$message = '(' . microtime() . ') ' . $message;

			$this->_log->add( 'yith_wcmc', $message, $level );
		}

		/* === HANDLE REQUEST TO MAILCHIMP === */

		/**
		 * Init Api class
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_api() {
			$api_key = get_option( 'yith_wcmc_mailchimp_api_key' );

			if ( ! empty( $api_key ) ) {
				try {
					$this->mailchimp = new Mailchimp( $api_key );

					// Disable verify peer when not under ssl
					if ( ! is_ssl() ) {
						$this->mailchimp->verify_ssl = false;
					}
				} catch ( Exception $e ) {
					$this->mailchimp = null;
				}
			} else {
				$this->mailchimp = null;
			}
		}

		/**
		 * Retrieve lists registered for current API Key
		 *
		 * @param $force_update bool Whether to force update of cache
		 *
		 * @return array Array of available list, in id -> name format
		 * @since 1.0.0
		 */
		public function retrieve_lists( $force_update = false ) {
			$lists = $this->do_request( 'get', 'lists', array(), $force_update );

			$list_options = array();
			if ( ! empty( $lists['lists'] ) ) {
				foreach ( $lists['lists'] as $list ) {
					$list_options[ $list['id'] ] = $list['name'];
				}
			}

			return $list_options;
		}

		/**
		 * Retrieve interest groups registered for passed list
		 *
		 * @param string $list         Id of the list, used to retrieve groups
		 * @param        $force_update bool Whether to force update of cache
		 *
		 * @return array Array of available groups, formatted as ( group_id - interest_name ) -> ( group_name - interest_name ) format
		 * @since 1.0.0
		 */
		public function retrieve_groups( $list, $force_update = false ) {
			$groups_options = array();
			if ( ! empty( $list ) ) {
				$interests_categories = $this->do_request( 'get', "lists/{$list}/interest-categories", array(), $force_update );

				if ( ! empty( $interests_categories['categories'] ) && is_array( $interests_categories['categories'] ) ) {
					foreach ( $interests_categories['categories'] as $interests_category ) {
						$category_id = $interests_category['id'];
						$interests   = $this->do_request( 'get', "lists/{$list}/interest-categories/{$category_id}/interests", array(), $force_update );

						if ( ! empty( $interests['interests'] ) && is_array( $interests['interests'] ) ) {
							foreach ( $interests['interests'] as $interest ) {
								$groups_options[ $interests_category['id'] . '-' . $interest['id'] ] = $interests_category['title'] . ' - ' . $interest['name'];
							}
						}
					}
				}
			}

			return $groups_options;
		}

		/**
		 * Retrieve specific interests registered for passed list
		 *
		 * @param string $list            Id of the list, used to retrieve groups
		 * @param        $subscriber_hash hash of the subscriber to Mailchimp
		 * @param bool   $force_update    bool Whether to force update of cache
		 *
		 * @return array Array of available interests, formatted as ( category_id - interest_id ) -> ( interest_category - interest_name ) format
		 */
		public function retrieve_specific_interests_by_user( $list, $subscriber_hash, $force_update = false ) {
			$groups_options = array();
			if ( ! empty( $list ) ) {
				$user_info = $this->do_request( 'get', "/lists/{$list}/members/{$subscriber_hash}", array(), $force_update );
				if ( ! empty( $user_info['interests'] ) && is_array( $user_info['interests'] ) ) {
					foreach ( $user_info['interests'] as $interest_id => $is_interest_suscribed ) {
						if ( ! empty( $is_interest_suscribed ) ) {
							$interests_categories = $this->do_request( 'get', "lists/{$list}/interest-categories", array(), $force_update );
							foreach ( $interests_categories['categories'] as $interests_category ) {
								$category_id                                         = $interests_category['id'];
								$interests                                           = $this->do_request( 'get', "/lists/{$list}/interest-categories/{$category_id}/interests/{$interest_id}", array(), $force_update );
								$groups_options[ $category_id . '-' . $interest_id ] = $interests_category['title'] . '-' . $interests['name'];
							}
						}
					}
				}

			}

			return $groups_options;
		}

		/**
		 * Retrieve merge fields for passed list
		 *
		 * @param string $list         Id of the list, used to retrieve groups
		 * @param        $force_update bool Whether to force update of cache
		 *
		 * @return array Array of available merge vars, formatted as tag -> name format
		 * @since 1.0.0
		 */
		public function retrieve_fields( $list, $force_update = false ) {
			$fields = array();

			if ( ! empty( $list ) ) {
				$response = $this->do_request( 'get', "lists/{$list}/merge-fields", array(), $force_update );

				if ( ! empty( $response['merge_fields'] ) ) {
					$merge_fields = $response['merge_fields'];

					foreach ( $merge_fields as $field ) {
						$fields[ $field['tag'] ] = $field['name'];
					}
				}

				// add email, since API 3.0 won't return it
				$fields['EMAIL'] = _x( 'Email Address', 'Default email field name (backend)', 'yith-woocommerce-mailchimp' );
			}

			return $fields;
		}

		/**
		 * Subscribe an email to a specific list
		 *
		 * @param $list  string List id
		 * @param $email string Email address to subscribe
		 * @param $args  array Array of additional args to use for the API call
		 *
		 * @return array|bool Request response; false on invalid list
		 */
		public function subscribe( $list, $email, $args = array() ) {
			if ( ! $list ) {
				return false;
			}

			$args = apply_filters( 'yith_wcmc_subscribe_args', array_merge( array(
				'id'            => $list,
				'email_address' => $email,
				'email_type'    => 'html',
				'status'        => 'subscribed'
			), $args ) );

			$member_hash = md5( strtolower( $args['email_address'] ) );

			if ( ! isset( $args['update_existing'] ) || $args['update_existing'] ) {
				$method = 'put';
				$path   = "lists/{$list}/members/{$member_hash}";
			} else {
				$method = 'post';
				$path   = "lists/{$list}/members";
			}

			if ( isset( $args['update_existing'] ) ) {
				unset( $args['update_existing'] );
			}

			$res = $this->do_request( $method, $path, $args );

			return $res;
		}

		/**
		 * Unsubscribe an email to a specific list
		 *
		 * @param $list  string List id
		 * @param $email string Email address to subscribe
		 * @param $args  array Array of additional args to use for the API call
		 *
		 * @return array|bool Request response; false on invalid list
		 */
		public function unsubscribe( $list, $email, $args = array() ) {
			if ( ! $list ) {
				return false;
			}

			$member_hash = md5( strtolower( $email ) );
			$request     = "lists/{$list}/members/{$member_hash}";

			if ( isset( $args['delete_member'] ) && $args['delete_member'] ) {
				$method         = 'delete';
				$request_params = array();
			} else {
				$method         = 'patch';
				$request_params = array(
					'status' => 'unsubscribed'
				);
			}

			if ( isset( $args['delete_member'] ) ) {
				unset( $args['delete_member'] );
			}

			$args = apply_filters( 'yith_wcmc_unsubscribe_args', array_merge(
				$request_params,
				$args
			) );

			$res = $this->do_request( $method, $request, $args );

			return $res;
		}

		/**
		 * Send a request to mailchimp servers
		 *
		 * @param $request      string API handle to call (e.g. 'lists/list')
		 * @param $args         array Associative array of params to use in the request (default to empty array)
		 * @param $force_update boolean Whether or not to update cached data with a fresh request (applied only for requests in $cachable_requests, default to false)
		 *
		 * @return mixed API response (as an associative array)
		 * @since 1.0.0
		 */
		public function do_request( $method, $request = '', $args = array(), $force_update = false ) {
			if ( is_null( $this->mailchimp ) ) {
				return false;
			}

			if ( ! in_array( $method, self::$available_api_methods ) ) {
				return false;
			}

			if ( yith_wcmc_doing_batch() && $method != 'get' && $request != 'batches' ) {
				$this->batch_ops[] = array(
					'method' => $method,
					'path'   => $request,
					'body'   => json_encode( $args )
				);

				return false;
			}

			$api_key        = get_option( 'yith_wcmc_mailchimp_api_key' );
			$transient_name = 'yith_wcmc_' . md5( $api_key );
			$transient_key  = $request . '_' . md5( json_encode( $args ) );
			$data           = get_transient( $transient_name );

			// check if request is could be stored in cache
			$cachable = false;
			if ( ! empty( $this->cachable_requests ) ) {
				foreach ( $this->cachable_requests as $cachable_request ) {
					if ( preg_match( '/^' . $cachable_request . '$/', $request ) ) {
						$cachable = true;
						break;
					}
				}
			}

			// retrieve result from cache when possible
			if ( 'get' == $method && $cachable && ! $force_update && ! empty( $data ) && isset( $data[ $transient_key ] ) ) {
				return $data[ $transient_key ];
			}

			// cache miss; let's proceed with API call
			try {
				$result = null;

				// execute API call
				$result = $this->_call_rest_api( $method, $request, $args );

				// caching results
				if ( 'get' == $method && $cachable ) {
					$data[ $transient_key ] = $result;
					set_transient( $transient_name, $data, apply_filters( 'yith_wcmc_transient_expiration', DAY_IN_SECONDS ) );
				}

				return $result;
			} catch ( YITH_WCMC_API_Exception $e ) {
				return array(
					'status'  => false,
					'code'    => $e->getCode(),
					'message' => $e->getLocalizedMessage()
				);
			} catch ( Exception $e ) {
				return array(
					'status'  => false,
					'code'    => $e->getCode(),
					'message' => $e->getMessage()
				);
			}
		}

		/**
		 * Delete data in the cache for a specific request
		 *
		 * @param $request string
		 *
		 * @return void
		 *
		 * @since 2.0.0
		 */
		public function delete_cached_data( $request ) {
			$api_key        = get_option( 'yith_wcmc_mailchimp_api_key' );
			$transient_name = 'yith_wcmc_' . md5( $api_key );
			$data           = get_transient( $transient_name );

			if ( isset( $data[ $request ] ) ) {
				unset( $data[ $request ] );
			}

			update_option( '_transient_' . $transient_name, $data );
		}

		/**
		 * Return currently registered batch operations
		 *
		 * @param $empty_list bool Whether to empty list after read
		 *
		 * @return array Batch operations
		 *
		 * @since 2.0.0
		 */
		public function get_batch_ops( $empty_list = false ) {
			$res = $this->batch_ops;

			if ( $empty_list ) {
				$this->batch_ops = array();
			}

			return $res;
		}

		/**
		 * Perform api call to MailChimp
		 * When required, iterate to retrieve all items in the set
		 *
		 * @param $method    string HTTP method to call
		 * @param $reques    string Path to call
		 * @param $args      array Array of additional args to send with API call
		 * @param $iteration bool Reserved; set to true when iterating to get all page of the set
		 *
		 * @return array Result set
		 * @throws YITH_WCMC_API_Exception Throws exception when API call fails
		 * @throws Exception Throws exception when connection issue occurs
		 */
		protected function _call_rest_api( $method, $request, $args, $iteration = false ) {
			$count             = isset( $args['count'] ) ? $args['count'] : 10;
			$offset            = isset( $args['offset'] ) ? $args['offset'] : 0;
			$manual_pagination = isset( $args['count'] ) || isset( $args['offset'] );

			$timeout = apply_filters( 'yith_wcmc_request_timeout', self::$timeout, $method, $request );

			$result = $this->mailchimp->$method( $request, $args, $timeout );

			// error handling
			if ( empty( $result ) ) {
				throw new Exception( _x( 'Server returned an unexpected response; please, try again later', 'Generic API error message', 'yith-woocommerce-mailchimp' ) );
			} elseif ( isset( $result['status'] ) && is_int( $result['status'] ) && $result['status'] != '200' ) {
				throw new YITH_WCMC_API_Exception( $result['detail'], $result['title'], $result['status'] );
			}

			// check if iteration is required
			if ( $method == 'get' && ( ! $manual_pagination || $iteration ) && isset( $result['total_items'] ) && $result['total_items'] > ( $offset + $count ) ) {
				$args['count']  = $count;
				$args['offset'] = $offset + $count;

				$next_page = $this->_call_rest_api( $method, $request, $args, true );

				$path_exploded = explode( '/', $request );
				$items_index   = str_replace( '-', '_', array_pop( $path_exploded ) );

				if ( ! isset( $result[ $items_index ] ) || ! isset( $next_page[ $items_index ] ) ) {
					return $result;
				}

				$result[ $items_index ] = array_merge( $result[ $items_index ], $next_page[ $items_index ] );
			}

			// our system expect boolean status; error are already handled by exceptions, so we just need to send true
			if ( is_array( $result ) && ( ! isset( $result['status'] ) || $result['status'] == '200' ) ) {
				$result['status'] = true;
			}

			return $result;
		}

		/* === AJAX CALLS === */

		/**
		 * Handles AJAX request, used to call API handles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function do_request_via_ajax() {
			// return if not ajax request
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// retrieve params for the request
			$method       = isset( $_REQUEST['method'] ) ? trim( $_REQUEST['method'] ) : false;
			$request      = isset( $_REQUEST['request'] ) ? trim( $_REQUEST['request'] ) : false;
			$args         = isset( $_REQUEST['args'] ) ? $_REQUEST['args'] : array();
			$force_update = isset( $_REQUEST['force_update'] ) ? $_REQUEST['force_update'] : false;

			// return if required params are missing
			if ( empty( $method ) || empty( $request ) || empty( $_REQUEST['yith_wcmc_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcmc_ajax_request_nonce'], 'yith_wcmc_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request
			$result = $this->do_request( $method, $request, $args, $force_update );

			// send empty response, if there was an error
			if ( isset( $result['status'] ) && ! $result['status'] ) {
				wp_send_json( false );
			}

			// return json encoded result
			wp_send_json( $result );
		}

		/**
		 * Retrieve lists via ajax call
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function retrieve_lists_via_ajax() {
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// retrieve params for the request
			$force_update = isset( $_REQUEST['force_update'] ) ? $_REQUEST['force_update'] : false;

			// return if required params are missing
			if ( empty( $_REQUEST['yith_wcmc_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcmc_ajax_request_nonce'], 'yith_wcmc_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request
			$result = $this->retrieve_lists( $force_update );

			// send empty response, if there was an error
			if ( isset( $result['status'] ) && ! $result['status'] ) {
				wp_send_json( false );
			}

			// return json encoded result
			wp_send_json( $result );
		}

		/**
		 * Retrieve groups via ajax call
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function retrieve_groups_via_ajax() {
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// retrieve params for the request
			$list         = isset( $_REQUEST['list'] ) ? trim( $_REQUEST['list'] ) : false;
			$force_update = isset( $_REQUEST['force_update'] ) ? $_REQUEST['force_update'] : false;

			// return if required params are missing
			if ( empty( $list ) || empty( $_REQUEST['yith_wcmc_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcmc_ajax_request_nonce'], 'yith_wcmc_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request
			$result = $this->retrieve_groups( $list, $force_update );

			// send empty response, if there was an error
			if ( isset( $result['status'] ) && ! $result['status'] ) {
				wp_send_json( false );
			}

			// return json encoded result
			wp_send_json( $result );
		}

		/**
		 * Retrieve fields via ajax call
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function retrieve_fields_via_ajax() {
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// retrieve params for the request
			$list         = isset( $_REQUEST['list'] ) ? trim( $_REQUEST['list'] ) : false;
			$force_update = isset( $_REQUEST['force_update'] ) ? $_REQUEST['force_update'] : false;

			// return if required params are missing
			if ( empty( $list ) || empty( $_REQUEST['yith_wcmc_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcmc_ajax_request_nonce'], 'yith_wcmc_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request
			$result = $this->retrieve_fields( $list, $force_update );

			// send empty response, if there was an error
			if ( isset( $result['status'] ) && ! $result['status'] ) {
				wp_send_json( false );
			}

			// return json encoded result
			wp_send_json( $result );
		}

		/* === ADDS FRONTEND CHECKBOX === */

		/**
		 * Register action to print subscription checkbox
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_subscription_checkbox() {
			$trigger           = get_option( 'yith_wcmc_checkout_trigger', 'never' );
			$show_checkbox     = 'yes' == get_option( 'yith_wcmc_subscription_checkbox' );
			$checkbox_position = get_option( 'yith_wcmc_subscription_checkbox_position' );

			if ( $trigger != 'never' && $show_checkbox ) {
				$positions_hook_relation = apply_filters( 'yith_wcmc_checkbox_position_hook', array(
					'above_customer'    => 'woocommerce_checkout_before_customer_details',
					'below_customer'    => 'woocommerce_checkout_after_customer_details',
					'above_place_order' => 'woocommerce_review_order_before_submit',
					'below_place_order' => 'woocommerce_review_order_after_submit',
					'above_total'       => 'woocommerce_checkout_before_order_review',
					'above_billing'     => 'woocommerce_checkout_billing',
					'below_billing'     => 'woocommerce_after_checkout_billing_form',
					'above_shipping'    => 'woocommerce_checkout_shipping'
				) );

				if ( ! in_array( $checkbox_position, array_keys( $positions_hook_relation ) ) ) {
					$checkbox_position = 'below_customer';
				}

				$hook = $positions_hook_relation[ $checkbox_position ];

				add_action( $hook, array( $this, 'print_subscription_checkbox' ) );
			}
		}

		/**
		 * Prints subscription checkbox
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_subscription_checkbox() {
			$checkbox_label   = get_option( 'yith_wcmc_subscription_checkbox_label' );
			$checkbox_checked = 'yes' === get_option( 'yith_wcmc_subscription_checkbox_default' );

			if ( function_exists( 'wc_privacy_policy_page_id' ) ) {
				$privacy_link   = sprintf( '<a href="%s">%s</a>', get_the_permalink( wc_privacy_policy_page_id() ), apply_filters( 'yith_wcmc_privacy_policy_page_label', __( 'Privacy Policy', 'yith-woocommerce-mailchimp' ) ) );
				$checkbox_label = str_replace( '%privacy_policy%', $privacy_link, $checkbox_label );
			}

			$template_name = 'mailchimp-subscription-checkbox.php';
			$located       = locate_template( array(
				trailingslashit( WC()->template_path() ) . 'wcmc/' . $template_name,
				trailingslashit( WC()->template_path() ) . $template_name,
				'wcmc/' . $template_name,
				$template_name
			) );

			if ( ! $located ) {
				$located = YITH_WCMC_DIR . 'templates/' . $template_name;
			}

			include_once( $located );
		}

		/* === HANDLES ORDER SUBSCRIPTION === */

		/**
		 * Adds metas to order post, saving mailchimp informations
		 *
		 * @param $order_id int Order id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function adds_order_meta( $order_id ) {
			$show_checkbox   = 'yes' == get_option( 'yith_wcmc_subscription_checkbox' );
			$submitted_value = isset( $_POST['yith_wcmc_subscribe_me'] ) ? 'yes' : 'no';
			$order           = wc_get_order( $order_id );

			yit_save_prop( $order, '_yith_wcmc_show_checkbox', $show_checkbox );
			yit_save_prop( $order, '_yith_wcmc_submitted_value', $submitted_value );
		}

		/**
		 * Subscribe user to newsletter (called on order placed)
		 *
		 * @param $order_id int Order id
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function subscribe_on_checkout( $order_id ) {
			$order           = wc_get_order( $order_id );
			$trigger         = get_option( 'yith_wcmc_checkout_trigger' );
			$show_checkbox   = yit_get_prop( $order, '_yith_wcmc_show_checkbox', true );
			$submitted_value = yit_get_prop( $order, '_yith_wcmc_submitted_value', true );

			// return if admin don't want to subscribe users at this point
			if ( $trigger != 'created' ) {
				return false;
			}

			// return if subscription checkbox is printed, but not submitted
			if ( $show_checkbox && $submitted_value == 'no' ) {
				return false;
			}

			return $this->order_subscribe( $order_id );
		}

		/**
		 * Subscribe user to newsletter (called on order completed)
		 *
		 * @param $order_id int Order id
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function subscribe_on_completed( $order_id ) {
			$order           = wc_get_order( $order_id );
			$trigger         = get_option( 'yith_wcmc_checkout_trigger' );
			$show_checkbox   = yit_get_prop( $order, '_yith_wcmc_show_checkbox', true );
			$submitted_value = yit_get_prop( $order, '_yith_wcmc_submitted_value', true );

			// return if admin don't want to subscribe users at this point
			if ( $trigger != 'completed' ) {
				return false;
			}

			// return if subscription checkbox is printed, but not submitted
			if ( apply_filters( 'yith_wcmc_subscribe_on_completed', $show_checkbox && $submitted_value == 'no', $order_id ) ) {
				return false;
			}

			return $this->order_subscribe( $order_id );
		}

		/**
		 * Call subscribe API handle, to register user to a specific list
		 *
		 * @param $order_id int Order id
		 *
		 * @return bool status of the operation
		 */
		public function order_subscribe( $order_id, $args = array() ) {
			$order = wc_get_order( $order_id );

			$list_id         = get_option( 'yith_wcmc_mailchimp_list' );
			$email_type      = get_option( 'yith_wcmc_email_type' );
			$double_optin    = 'yes' == get_option( 'yith_wcmc_double_optin' );
			$update_existing = 'yes' == get_option( 'yith_wcmc_update_existing' );

			if ( empty( $list_id ) ) {
				return false;
			}

			$email      = yit_get_prop( $order, 'billing_email', true );
			$first_name = yit_get_prop( $order, 'billing_first_name', true );
			$last_name  = yit_get_prop( $order, 'billing_last_name', true );
			$user_id    = yit_get_prop( $order, 'customer_user', true );

			$merge_fields        = new stdClass();
			$merge_fields->FNAME = $first_name;
			$merge_fields->LNAME = $last_name;

			$args = array_merge( array(
				'email_address'   => $email,
				'merge_fields'    => apply_filters( 'yith_wcmc_subscribe_merge_vars', $merge_fields ),
				'email_type'      => $email_type,
				'status'          => $double_optin ? 'pending' : 'subscribed',
				'update_existing' => $update_existing
			), $args );

			if ( isset( $args['id'] ) ) {
				$list_id = $args['id'];
				unset( $args['id'] );
			}

			do_action( 'yith_wcmc_user_subscribing', $order_id );

			$res = $this->subscribe( $list_id, $email, $args );

			if ( isset( $res['status'] ) && ! $res['status'] ) {
				$order->add_order_note( sprintf( __( 'MAILCHIMP ERROR: (%s) %s', 'yith-woocommerce-mailchimp' ), $res['code'], $res['message'] ) );

				return $res;
			}

			// register subscribed list
			$this->_register_customer_subscribed_lists( isset( $args['id'] ) ? $args['id'] : $list_id, $email, $user_id, $order_id );

			// register personal data
			$personal_data = apply_filters( 'yith_wcmc_customer_personal_data', array(
				'billing_first_name' => array(
					'label' => __( 'First name', 'yith-woocommerce-mailchimp' ),
					'value' => $first_name
				),
				'billing_last_name'  => array(
					'label' => __( 'Last name', 'yith-woocommerce-mailchimp' ),
					'value' => $last_name
				),
				'billing_email'      => array(
					'label' => __( 'Email', 'yith-woocommerce-mailchimp' ),
					'value' => $email
				)
			), $args, $order_id );

			if ( ! empty( $personal_data ) ) {
				$this->_register_customer_personal_data( $order_id, $personal_data );
			}

			yit_save_prop( $order, '_yith_wcmc_customer_subscribed', true );

			do_action( 'yith_wcmc_user_subscribed', $order_id );

			return $res;
		}

		/**
		 * Register personal data sent to MailChimp servers, within order meta
		 *
		 * @param $order_id int Order id
		 * @param $arg1     string|array ID of the data to save or array of arrays containing label and value to save
		 * @param $arg2     string|bool When $arg1 is an ID, this param will be used as label
		 * @param $arg3     string|bool When $arg1 is an ID, this param will be used as value
		 *
		 * @return void
		 */
		protected function _register_customer_personal_data( $order_id, $arg1, $arg2 = false, $arg3 = false ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$data = is_array( $arg1 ) ? $arg1 : array( $arg1 => array( 'label' => $arg2, 'value' => $arg3 ) );

			$previous_personal_data = yit_get_prop( $order, '_yith_wcmc_personal_data', true );
			$previous_personal_data = ! empty( $previous_personal_data ) ? $previous_personal_data : array();
			$new_personal_data      = array_merge( $previous_personal_data, $data );

			yit_save_prop( $order, '_yith_wcmc_personal_data', $new_personal_data );
		}

		/**
		 * Register subscribed lists, in order to easily unsubscribe user later
		 *
		 * @param $list_id  string List id
		 * @param $email    string Email being subscribed
		 * @param $user_id  int User id
		 * @param $order_id int|bool Order id
		 *
		 * @return void
		 */
		protected function _register_customer_subscribed_lists( $list_id, $email, $user_id, $order_id = false ) {
			// register list within the customer
			if ( $user_id ) {
				$order_subscribed_lists = get_user_meta( $user_id, '_yith_wcmc_subscribed_lists', true );
				$order_subscribed_lists = ! empty( $order_subscribed_lists ) ? $order_subscribed_lists : array();

				if ( ! array_key_exists( $list_id, $order_subscribed_lists ) ) {
					$order_subscribed_lists[ $list_id ] = array();
				}

				if ( ! in_array( $email, $order_subscribed_lists[ $list_id ] ) ) {
					$order_subscribed_lists[ $list_id ][] = $email;

					update_user_meta( $user_id, '_yith_wcmc_subscribed_lists', $order_subscribed_lists );

					do_action( 'yith_wcmc_register_customer_subscribed_list', $list_id, $email, $user_id, $order_id );
				}
			}

			// eventually register list within the order
			if ( $order_id ) {
				$order = wc_get_order( $order_id );

				if ( $order ) {
					$order_subscribed_lists = yit_get_prop( $order, '_yith_wcmc_subscribed_lists', true );
					$order_subscribed_lists = ! empty( $order_subscribed_lists ) ? $order_subscribed_lists : array();

					if ( ! array_key_exists( $list_id, $order_subscribed_lists ) ) {
						$order_subscribed_lists[ $list_id ] = array();
					}

					if ( ! in_array( $email, $order_subscribed_lists[ $list_id ] ) ) {
						$order_subscribed_lists[ $list_id ][] = $email;
						yit_save_prop( $order, '_yith_wcmc_subscribed_lists', $order_subscribed_lists );
					}
				}
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCMC class
 *
 * @return \YITH_WCMC
 * @since 1.0.0
 */
function YITH_WCMC() {
	return YITH_WCMC::get_instance();
}