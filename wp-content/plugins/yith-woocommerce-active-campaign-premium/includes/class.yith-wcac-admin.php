<?php
/**
 * Admin class
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

if ( ! class_exists( 'YITH_WCAC_Admin' ) ) {
	/**
	 * WooCommerce Active Campaign Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAC_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Docs url
		 *
		 * @var string Official documentation url
		 * @since 1.0.0
		 */
		public $doc_url = 'https://docs.yithemes.com/yith-active-campaign-for-woocommerce/';

		/**
		 * Premium landing url
		 *
		 * @var string Premium landing url
		 * @since 1.0.0
		 */
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-active-campaign/';

		/**
		 * List of available tab for active campaign panel
		 *
		 * @var array
		 * @access public
		 * @since  1.0.0
		 */
		public $available_tabs = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAC_Admin
		 * @since 1.0.0
		 */
		public function __construct() {

			/* === Register plugin to licence/update system === */
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// sets available tab.
			$this->available_tabs = apply_filters(
				'yith_wcac_available_admin_tabs',
				array(
					'integration' => _x( 'Integration', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'checkout'    => _x( 'Checkout', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'shortcode'   => _x( 'Shortcode', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'widget'      => _x( 'Widget', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'register'    => _x( 'Register', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'tags'        => _x( 'Tags', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'deep-data'   => _x( 'Deep Data', 'Panel tab', 'yith-woocommerce-active-campaign' ),
					'export'      => _x( 'Export', 'Panel tab', 'yith-woocommerce-active-campaign' ),
				)
			);

			// register plugin panel.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'woocommerce_admin_field_yith_wcac_integration_status', array( $this, 'print_custom_yith_wcac_integration_status' ) );
			add_action( 'woocommerce_admin_field_yith_wcac_deep_data_connection_status', array( $this, 'print_custom_yith_wcac_deep_data_connection_status' ) );

			// register plugin links & meta row.
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 5 );
			add_filter( 'plugin_action_links_' . YITH_WCAC_INIT, array( $this, 'action_links' ) );

			// enqueue style.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// update option for older versions.
			add_action( 'admin_init', array( $this, 'upgrade_plugin' ) );

			// register premium options.
			add_action( 'woocommerce_admin_field_yith_wcac_advanced_integration', array( $this, 'print_custom_yith_wcac_advanced_integration' ) );
			add_action( 'woocommerce_admin_field_yith_wcac_custom_fields', array( $this, 'print_custom_yith_wcac_custom_fields' ) );
			add_action( 'woocommerce_admin_field_date_range', array( $this, 'print_custom_date_range' ) );

			// adds dashboard widget.
			add_action( 'admin_init', array( $this, 'refresh_lists_for_widget' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

			// AJAX Handler.
			add_action( 'wp_ajax_wcac_add_advanced_panel_item', array( $this, 'print_advanced_integration_item' ) );
			add_action( 'wp_ajax_wcac_add_advanced_panel_field', array( $this, 'print_advanced_integration_field' ) );
			add_action( 'wp_ajax_wcac_add_advanced_panel_condition', array( $this, 'print_advanced_integration_condition' ) );
			add_action( 'wp_ajax_wcac_add_custom_field', array( $this, 'print_custom_fields_item' ) );
			add_action( 'wp_ajax_wcac_do_request_via_ajax', array( $this, 'do_request_via_ajax' ) );
			add_action( 'wp_ajax_wcac_get_fields_via_ajax', array( $this, 'get_fields_via_ajax' ) );
			add_action( 'wp_ajax_wcac_delete_connection_via_ajax', array( $this, 'disconnect_store_via_ajax' ) );

			// handle exports.
			add_action( 'yit_panel_wc_after_update', array( $this, 'manage_users_export' ) );
			add_action( 'yit_panel_wc_after_update', array( $this, 'manage_csv_download' ) );
			add_filter( 'pre_update_option_yith_wcac_export_list', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_email_type', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_double_optin', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_update_existing', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_user_set', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_users', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_filter_product', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_filter_category', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_export_filter_tag', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_csv_user_set', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_csv_users', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_csv_filter_product', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_csv_filter_category', '__return_empty_string' );
			add_filter( 'pre_update_option_yith_wcac_csv_filter_tag', '__return_empty_string' );

			// register metabox to show user preferences within the order.
			add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ) );

			add_action( 'woocommerce_product_options_advanced', array( $this, 'add_simple_product_field' ) );
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_variable_product_field' ), 10, 3 );
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_simple_product_field' ), 10, 1 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variable_product_field' ), 10, 2 );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCAC_INIT, YITH_WCAC_SECRET_KEY, YITH_WCAC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCAC_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WCAC_SLUG, YITH_WCAC_INIT );
		}

		/* === PLUGIN PANEL METHODS === */

		/**
		 * Register panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Active Campaign', 'yith-woocommerce-active-campaign' ),
				'menu_title'       => __( 'Active Campaign', 'yith-woocommerce-active-campaign' ),
				'capability'       => apply_filters( 'yith_wcac_panel_capability', 'manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => 'yith_wcac_panel',
				'admin-tabs'       => $this->available_tabs,
				'options-path'     => YITH_WCAC_DIR . 'plugin-options',
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCAC_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Output integration status filed
		 *
		 * @param array $value Array representing the field to print.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcac_integration_status( $value ) {

			$result = YITH_WCAC()->do_request( 'users/me' );

			$fname  = false;
			$lname  = false;
			$email  = false;
			$avatar = false;

			if ( isset( $result->user ) ) {
				$fname  = isset( $result->user->firstName ) ? $result->user->firstName : false;
				$lname  = isset( $result->user->lastName ) ? $result->user->lastName : false;
				$email  = isset( $result->user->email ) ? $result->user->email : false;

				if ( $account_name = YITH_WCAC()->calculate_account_name() ) {
					$avatar = isset( $result->user->email ) ? '//' . $account_name . '.activehosted.com/gravatar.php?h=' . $result->user->email : false;
				}
			}

			$username = $fname;
			$name     = $fname . ' ' . $lname;

			$attributes = array(
				'result'   => $result,
				'fname'    => $fname,
				'lname'    => $lname,
				'email'    => $email,
				'username' => $username,
				'name'     => $name,
				'avatar'   => $avatar,
				'value'    => $value,
			);

			yith_wcac_get_template( 'integration-status', $attributes, 'admin/types' );

		}

		/**
		 * Print "yith_wcac_deep_data_connection_status" custom field
		 *
		 * @param $value mixed Args
		 *
		 * @return void
		 */
		public function print_custom_yith_wcac_deep_data_connection_status( $value ) {
			$connection = YITH_WCAC_Deep_Data()->is_store_connected();

			include( YITH_WCAC_DIR . 'templates/admin/types/deep-data-connection-status.php' );
		}

		/* === HANDLE PLUGIN UPDATE === */

		/**
		 * Process update from older version to the latest one
		 *
		 * @return void
		 * @sice 2.0.0
		 */
		public function upgrade_plugin() {
			// first of all, let's check for DB updates.
			$this->_update_db();

			// then proceed updates related to plugin version.
			$this->_update_options();
		}

		/**
		 * Update DB, when required
		 *
		 * @return void
		 */
		private function _update_db() {
			$stored_version = get_option( 'yith_wcac_db_version', '' );

			// if stored version is lower than current version, update.
			if ( version_compare( $stored_version, YITH_WCAC_DB_VERSION, '<' ) ) {
				global $wpdb;

				// assure dbDelta function is defined.
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				// retrieve table charset.
				$charset_collate = $wpdb->get_charset_collate();

				// adds register table.
				$sql = "CREATE TABLE $wpdb->yith_wcac_register (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    item_type varchar(255) NOT NULL,
                    item varchar(255) NOT NULL,
                    ac_id varchar(255) NOT NULL,
                    last_updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                    PRIMARY KEY ID (ID)
				) $charset_collate;";

				dbDelta( $sql );

				// adds waiting orders table.
				$sql = "CREATE TABLE $wpdb->yith_wcac_waiting_orders (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    email varchar(255) NOT NULL,
                    currency char(3) NOT NULL,
                    ts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    cart longtext NOT NULL,
                    PRIMARY KEY ID (ID),
                    INDEX email (email)
				) $charset_collate;";

				dbDelta( $sql );

				// adds background process table.
				$sql = "CREATE TABLE $wpdb->yith_wcac_background_process_batches (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    batch_key char(32) NOT NULL,
                    task_id char(32) DEFAULT NULL,
                    batch longtext NOT NULL,
                    ts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY ID (ID),
                    INDEX task_id (task_id)
				) $charset_collate;";

				dbDelta( $sql );

				do_action( 'yith_wcac_update_db_' . YITH_WCAC_DB_VERSION );
				update_option( 'yith_wcac_db_version', YITH_WCAC_DB_VERSION );
			}
		}

		/**
		 * Update options and other plugin related data, when required
		 *
		 * @return void
		 */
		private function _update_options() {
			$stored_version = get_option( 'yith_wcac_version', '' );

			// process updates for version 2.0.0.
			if ( version_compare( $stored_version, '2.0.0', '<' ) ) {
				$this->_update_200();
			}

			// if stored version is lower than current version, store new version.
			if ( version_compare( $stored_version, YITH_WCAC_VERSION, '<' ) ) {
				update_option( 'yith_wcac_version', YITH_WCAC_VERSION );
			}
		}

		/**
		 * Update to 2.0.0 db version
		 *
		 * @return void
		 */
		private function _update_200() {
			// update all options that store tags, to register ID instead of name.
			$options = [
				'yith_wcac_active_campaign_tags',
				'yith_wcac_shortcode_active-campaign_tags',
				'yith_wcac_shortcode_active-campaign_show_tags',
				'yith_wcac_widget_active-campaign_tags',
				'yith_wcac_widget_active-campaign_show_tags',
				'yith_wcac_register_active-campaign_tags',
				'yith_wcac_register_active-campaign_show_tags',
			];

			// adds options for order status tags.
			$order_statuses = wc_get_order_statuses();
			if ( ! empty( $order_statuses ) ) {
				foreach ( $order_statuses as $status_slug => $status_name ) {
					$options[] = 'yith_wcac_tags_order_' . $status_slug;
				}
			}

			if ( ! empty( $options ) ) {
				foreach ( $options as $option ) {
					$this->_update_100_tag_structure( $option );
				}
			}

			// checkout advanced integration tags require their own handling.
			$this->_update_100_advanced_integration_option();

			// update product metas.
			$this->_update_100_product_meta();
		}

		/**
		 * Update value for advanced integration option, using latest structure of the data
		 *
		 * @return void
		 */
		private function _update_100_advanced_integration_option() {
			$advanced_integration_option = get_option( 'yith_wcac_advanced_integration' );

			if ( ! empty( $advanced_integration_option['show_tags'] ) ) {
				$advanced_integration_option['show_tags'] = $this->_convert_100_tags_value( $advanced_integration_option['show_tags'] );
			}

			if ( ! empty( $advanced_integration_option['items'] ) ) {
				foreach ( $advanced_integration_option['items'] as & $option_set ) {
					if ( ! empty( $option_set['tags'] ) ) {
						$option_set['tags'] = $this->_convert_100_tags_value( $option_set['tags'] );
					}
				}
			}

			update_option( 'yith_wcac_advanced_integration', $advanced_integration_option );
		}

		/**
		 * Convert old post meta structure into new version
		 *
		 * @return void
		 */
		private function _update_100_product_meta() {
			global $wpdb;

			$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", 'yith_wcaf_product_tags' ) );

			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					$value = get_post_meta( $post_id, 'yith_wcaf_product_tags', true );
					$new_value = $this->_convert_100_tags_value( $value );

					update_post_meta( $post_id, 'yith_wcac_product_tags', $new_value );
				}
			}
		}

		/**
		 * Update tags array stored inside plugin option
		 *
		 * Since version 2.0.0 plugin won't store any longer tags name, but tag ids
		 *
		 * @param string $option Option name.
		 * @return void
		 */
		private function _update_100_tag_structure( $option ) {
			$value = get_option( $option );

			// if value is empty, no need to proceed.
			if ( empty( $value ) ) {
				return;
			}

			$converted_value = $this->_convert_100_tags_value( $value );

			// finally update option with new value.
			update_option( $option, $converted_value );
		}

		/**
		 * Retrieve new value for tags option
		 *
		 * Will convert an array of tags name to an array of equivalent tag ids from Active Campaign DB
		 *
		 * @param string|array $value Array of tags or single tag name.
		 * @return string|array|bool Tag id, array of tag ids, or false on failure.
		 */
		private function _convert_100_tags_value( $value ) {
			// if value is empty, no need to proceed.
			if ( empty( $value ) ) {
				return false;
			}

			// retrieve currently existing tags, as id => name array.
			$tags = YITH_WCAC()->retrieve_tags();

			// build new value, with id instead of names (it could be a single value or an array of values).
			if ( is_array( $value ) ) {
				$new_value = [];

				foreach ( $value as $tag ) {
					$id = array_search( $tag, $tags );

					if ( false === $id ) {
						continue;
					}

					$new_value[] = (string) $id;
				}
			} else {
				$new_value = (string) array_search( $value, $tags );
			}

			return $new_value;
		}

		/* === PRINT CUSTOM TYPE FIELDS FOR PANEL === */

		/**
		 * Prints the template for advanced checkout options
		 *
		 * @param array $value Array of field settings.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcac_advanced_integration( $value ) {
			// Session class, handles session data for users - can be overwritten if custom handler is needed.
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

			include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-session.php' );
			include_once( WC()->plugin_path() . '/includes/class-wc-session-handler.php' );

			// Class instances.
			WC()->session  = new $session_class();
			WC()->customer = new WC_Customer();

			$advanced_options            = get_option( 'yith_wcac_advanced_integration', array() );
			$advanced_items              = isset( $advanced_options['items'] ) ? $advanced_options['items'] : array();
			$selected_show_tags          = isset( $advanced_options['show_tags'] ) ? $advanced_options['show_tags'] : array();
			$show_tags_label             = isset( $advanced_options['show_tags_label'] ) ? $advanced_options['show_tags_label'] : '';
			$selected_show_tags_position = isset( $advanced_options['show_tags_position'] ) ? $advanced_options['show_tags_position'] : '';

			$tags = YITH_WCAC()->retrieve_tags();

			$attributes = array(
				'advanced_options'            => $advanced_options,
				'advanced_items'              => $advanced_items,
				'selected_show_tags'          => $selected_show_tags,
				'show_tags_label'             => $show_tags_label,
				'selected_show_tags_position' => $selected_show_tags_position,
				'tags'                        => $tags,
				'value'                       => $value,
			);

			yith_wcac_get_template( 'advanced-integration', $attributes, 'admin/types' );
		}

		/**
		 * Print advanced integration item
		 *
		 * @param array $args An array with options of the item to output (optional).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_item( $args = array() ) {
			$attributes = wp_parse_args(
				$args,
				array(
					'item_id'       => isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0,
					'selected_list' => 0,
					'selected_tags' => array(),
					'fields'        => array(),
					'conditions'    => array(),
					'lists'         => YITH_WCAC()->retrieve_lists(),
					'tags'          => YITH_WCAC()->retrieve_tags(),
				)
			);

			yith_wcac_get_template( 'advanced-integration-item', $attributes, 'admin/types' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Print advanced integration item
		 *
		 * @param array $args An array with options of the item to output (optional).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_field( $args = array() ) {
			$attributes = wp_parse_args(
				$args,
				array(
					'item_id'            => isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0,
					'field_id'           => isset( $_POST['field_id'] ) ? intval( $_POST['field_id'] ) : 0,
					'selected_list'      => isset( $_POST['list_id'] ) ? intval( $_POST['list_id'] ) : 0,
					'selected_checkout'  => '',
					'selected_merge_var' => '',
				)
			);


			$attributes['fields']          = YITH_WCAC()->retrieve_fields();
			$attributes['checkout_fields'] = array();

			// retrieve dynamic checkout fields.
			$checkout_fields_raw = WC()->checkout()->checkout_fields;
			if ( ! empty( $checkout_fields_raw ) ) {
				foreach ( $checkout_fields_raw as $category_id => $category ) {
					if ( ! empty( $category ) ) {
						$attributes['checkout_fields'][ $category_id ]['name']   = ucwords( str_replace( '_', ' ', $category_id ) );
						$attributes['checkout_fields'][ $category_id ]['fields'] = array();

						foreach ( $category as $id => $field ) {
							$attributes['checkout_fields'][ $category_id ]['fields'][ $id ] = isset( $field['label'] ) ? $field['label'] : ucwords( str_replace( '_', ' ', $id ) );
						}
					}
				}
			}

			//unset account fields, if needed.
			$unset_account_fields = apply_filters( 'yith_wcac_unset_account_fields', true );

			if ( $unset_account_fields ) {
				unset( $attributes['checkout_fields']['account'] );
			}

			// adds custom checkout fields.
			$custom_fields = array(
				'shipping_method_title' => __( 'Shipping method name', 'yith-woocommerce-active-campaign' ),
				'payment_method_title'  => __( 'Payment method name', 'yith-woocommerce-active-campaign' ),
				'customer_user'         => __( 'User ID', 'yith-woocommerce-active-campaign' ),
			);

			if ( isset( $attributes['checkout_fields']['custom'] ) ) {
				$attributes['checkout_fields']['custom']['fields'] = array_merge(
					$attributes['checkout_fields']['custom']['fields'],
					$custom_fields
				);
			} else {
				$attributes['checkout_fields']['custom']['name']   = __( 'Custom', 'yith-woocommerce-active-campaign' );
				$attributes['checkout_fields']['custom']['fields'] = $custom_fields;
			}


			yith_wcac_get_template( 'advanced-integration-field', $attributes, 'admin/types' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}

		}

		/**
		 * Print advanced integration item
		 *
		 * @param array $args An array with options of the item to output (optional).
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_advanced_integration_condition( $args = array() ) {
			$attributes = wp_parse_args(
				$args,
				array(
					'item_id'      => isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0,
					'condition_id' => isset( $_POST['condition_id'] ) ? intval( $_POST['condition_id'] ) : 0,
					'condition'    => 'product_in_cart',
					'op_set'       => 'contain',
					'op_number'    => 'less_than',
					'products'     => array(),
					'prod_cats'    => array(),
					'order_total'  => 0,
					'custom_key'   => '',
					'op_mixed'     => 'is',
					'custom_value' => '',
				)
			);

			$attributes['json_ids'] = array();
			if ( ! empty( $attributes['products'] ) ) {
				$product_ids = ! is_array( $attributes['products'] ) ? array_filter( array_map( 'absint', explode( ',', $attributes['products'] ) ) ) : $attributes['products'];

				foreach ( $product_ids as $product_id ) {
					$attributes['json_ids'][ $product_id ] = wp_kses_post( get_the_title( $product_id ) );
				}
			}

			yith_wcac_get_template( 'advanced-integration-condition', $attributes, 'admin/types' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Output custom fields type
		 *
		 * @param array $value Array representing the field to print.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_yith_wcac_custom_fields( $value ) {
			$id = isset( $value['id'] ) ? $value['id'] : false;

			if ( ! $id ) {
				return;
			}

			$fields_options = get_option( $id, array() );
			$list_id        = get_option( str_replace( 'custom_fields', 'active_campaign_list', $id ) );

			$attributes = array_merge(
				$value,
				array(
					'id'             => $id,
					'fields_options' => $fields_options,
					'list_id'        => $list_id,
				)
			);
			yith_wcac_get_template( 'custom-fields', $attributes, 'admin/types' );
		}

		/**
		 * Prints an item for custom fields type
		 *
		 * @param array $args Array with the argument required for the template.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_fields_item( $args ) {
			$attributes = wp_parse_args(
				$args,
				array(
					'id'                 => isset( $_POST['id'] ) ? intval( $_POST['id'] ) : '',
					'item_id'            => isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : '',
					'selected_list'      => isset( $_POST['list_id'] ) ? intval( $_POST['list_id'] ) : 0,
					'selected_name'      => '',
					'selected_merge_var' => '',
					'removable'          => true,
				)
			);

			$attributes['fields'] = YITH_WCAC()->retrieve_fields();
			$tab                  = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : '';
			if ( 'register' === $tab ) {
				// On register tab AC email field will be automatic matched with email field. So is no needed here.
				$attributes['fields'] = array_slice( $attributes['fields'], 1, null, true );
			}

			yith_wcac_get_template( 'custom-fields-item', $attributes, 'admin/types' );

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				die();
			}
		}

		/**
		 * Handles AJAX request, used to call API handles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function do_request_via_ajax() {
			// return if not ajax request.
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// retrieve params for the request.
			$request      = isset( $_REQUEST['request'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['request'] ) ) : false;
			$method       = isset( $_REQUEST['method'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['method'] ) ) : 'GET';
			$args         = isset( $_REQUEST['args'] ) ? wc_clean( $_REQUEST['args'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$body         = isset( $_REQUEST['body'] ) ? wc_clean( $_REQUEST['body'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$force_update = isset( $_REQUEST['force_update'] ) ? boolval( $_REQUEST['force_update'] ) : false;

			// return if required params are missing.
			if ( empty( $request ) || empty( $_REQUEST['yith_wcac_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if nonce check fails.
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith_wcac_ajax_request_nonce'] ) ), 'yith_wcac_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request.
			$result = YITH_WCAC()->do_request( $request, $method, $args, $body, $force_update );

			if ( is_array( $result ) && isset( $result['status'] ) ) {
				wp_send_json( false );
			}

			// return json encoded result.
			wp_send_json( $result );
		}

		/**
		 * Handles AJAX request, used to call API handles
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function get_fields_via_ajax() {
			// return if not ajax request.
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// return if required params are missing.
			if ( empty( $_REQUEST['yith_wcac_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if nonce check fails.
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcac_ajax_request_nonce'], 'yith_wcac_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request.
			$result = YITH_WCAC()->retrieve_fields( true );

			// return json encoded result.
			wp_send_json( $result );
		}

		/**
		 * Disconnect store via ajax call
		 *
		 * @return void
		 * @since 1.1.0
		 */
		public function disconnect_store_via_ajax() {
			if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				wp_send_json( false );
			}

			// return if required params are missing.
			if ( empty( $_REQUEST['yith_wcac_ajax_request_nonce'] ) ) {
				wp_send_json( false );
			}

			// return if non check fails.
			if ( ! wp_verify_nonce( $_REQUEST['yith_wcac_ajax_request_nonce'], 'yith_wcac_ajax_request' ) ) {
				wp_send_json( false );
			}

			// do request.
			$result = YITH_WCAC_Deep_Data()->maybe_disconnect_store();

			// return json encoded result
			wp_send_json( $result );
		}

		/**
		 * Print date range type
		 *
		 * @param array $value Array of options used to print field.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_custom_date_range( $value ) {
			yith_wcac_get_template( 'date-range', array( 'value' => $value ), 'admin/types' );
		}

		/**
		 * Enqueue scripts and stuffs
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
			$screen = get_current_screen();
			$tab    = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : '';

			if ( 'shop_order' == $screen->id ) {
				wp_enqueue_style( 'yith-wcac-admin', YITH_WCAC_URL . '/assets/css/admin/yith-wcac.css', array(), YITH_WCAC_VERSION );
			}

			wp_register_script( 'yith-wcac-admin', YITH_WCAC_URL . '/assets/js/admin' . $path . '/yith-wcac' . $prefix . '.js', array( 'jquery', 'jquery-blockui', 'select2' ), YITH_WCAC::YITH_WCAC_VERSION, true );

			wp_localize_script(
				'yith-wcac-admin',
				'yith_wcac',
				array(
					'labels'             => array(
						'update_list_button'        => _x( 'Update Lists', 'Backend update list button', 'yith-woocommerce-active-campaign' ),
						'update_group_button'       => _x( 'Update Tags', 'Backend update tag button', 'yith-woocommerce-active-campaign' ),
						'update_field_button'       => _x( 'Update Fields', 'Backend update fields button', 'yith-woocommerce-active-campaign' ),
						'connect_store_button'      => _x( 'Connect store', 'Backend button on Deep Data tab', 'yith-woocommerce-active-campaign' ),
						'confirm_connection_delete' => __( "Are you sure you want to delete connection?\nThis will permanently remove from Active Campaign any data registered with this connection.", 'yith-woocommerce-active-campaign' ),
					),
					'actions'            => array(
						'wcac_do_request_via_ajax_action'        => 'wcac_do_request_via_ajax',
						'wcac_get_fields_via_ajax_action'        => 'wcac_get_fields_via_ajax',
						'wcac_delete_connection_via_ajax_action' => 'wcac_delete_connection_via_ajax',
					),
					'tab'                => $tab,
					'texts'              => array(
						'email'      => _x( 'Email Address', 'Email label defined by Active Campaign', 'yith-woocommerce-active-campaign' ),
						'first_name' => _x( 'First name', 'First name label defined by Active Campaign', 'yith-woocommerce-active-campaign' ),
						'last_name'  => _x( 'Last name', 'Last name label defined by Active Campaign', 'yith-woocommerce-active-campaign' ),
					),
					'ajax_request_nonce' => wp_create_nonce( 'yith_wcac_ajax_request' ),
				)
			);

			if ( 'yith-plugins_page_yith_wcac_panel' == $screen->id || 'dashboard' == $screen->id ) {
				wp_enqueue_style( 'yith-wcac-admin', YITH_WCAC_URL . '/assets/css/admin/yith-wcac.css', array(), YITH_WCAC_VERSION );
				wp_enqueue_style( 'owl-carousel', YITH_WCAC_URL . '/assets/css/owl.carousel.css', array(), '1.3.3' );
				wp_enqueue_script( 'owl-carousel', YITH_WCAC_URL . '/assets/js/owl.carousel.min.js', array( 'jquery' ), '1.3.3', true );
				wp_enqueue_script( 'yith-wcac-admin' );

				if ( ! $tab || 'checkout' === $tab ) {
					$items        = get_option( 'yith_wcac_advanced_integration', array() );
					$items_number = empty( $items['items'] ) ? 0 : count( $items['items'] );

					wp_enqueue_script( 'yith-wcac-advanced-panel', YITH_WCAC_URL . 'assets/js/admin' . $path . '/yith-wcac-advanced-panel' . $prefix . '.js', array( 'jquery', 'jquery-blockui' ), YITH_WCAC_VERSION, true );
					wp_enqueue_style( 'yith-wcac-advanced-panel', YITH_WCAC_URL . 'assets/css/admin/yith-wcac-advanced-panel.css', array(), YITH_WCAC_VERSION );

					wp_localize_script(
						'yith-wcac-advanced-panel',
						'yith_wcac_advanced_panel',
						array(
							'actions' => array(
								'wcac_add_advanced_panel_item_action'      => 'wcac_add_advanced_panel_item',
								'wcac_add_advanced_panel_field_action'     => 'wcac_add_advanced_panel_field',
								'wcac_add_advanced_panel_condition_action' => 'wcac_add_advanced_panel_condition',
							),
							'item_id' => ++ $items_number,
						)
					);

				}

				if ( in_array( $tab, array( 'shortcode', 'widget', 'register' ) ) ) {
					$items        = get_option( 'yith_wcac_shortcode_custom_fields', array() );
					$items_number = count( $items );

					wp_enqueue_script( 'yith-wcac-custom-fields', YITH_WCAC_URL . 'assets/js/admin' . $path . '/yith-wcac-custom-fields' . $prefix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable' ), YITH_WCAC_VERSION, true );
					wp_enqueue_style( 'yith-wcac-custom-fields', YITH_WCAC_URL . 'assets/css/admin/yith-wcac-custom-fields.css', array(), YITH_WCAC_VERSION );

					wp_localize_script(
						'yith-wcac-custom-fields',
						'yith_wcac_custom_fields',
						array(
							'actions' => array(
								'wcac_add_custom_field_action' => 'wcac_add_custom_field',
							),
							'item_id' => ++ $items_number,
						)
					);
				}

				if ( 'export' === $tab ) {
					wp_enqueue_script( 'yith-wcac-export-panel', YITH_WCAC_URL . 'assets/js/admin' . $path . '/yith-wcac-export-panel' . $prefix . '.js', array( 'jquery' ), YITH_WCAC_VERSION, true );

					wp_localize_script(
						'yith-wcac-export-panel',
						'yith_wcac_export_panel',
						array(
							'labels' => array(
								'export_users' => __( 'Export Users', 'yith-woocommerce-active-campaign' ),
								'download_csv' => __( 'Download CSV', 'yith-woocommerce-active-campaign' ),
							),
						)
					);
				}
			} elseif ( 'product' == $screen->id ) {
				wp_enqueue_script( 'yith-wcac-admin' );
			}
		}

		/* === PLUGIN LINK METHODS === */

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}

		/**
		 * Adds plugin row meta
		 *
		 * @pagam $new_row_meta_args Meta to filter.
		 * @param array  $new_row_meta_args Array of original plugin meta.
		 * @param array  $plugin_meta       Array of unfiltered plugin meta.
		 * @param string $plugin_file       Plugin base file path.
		 * @param array  $plugin_data       Plugin data.
		 * @param mixed  $status            Plugin status.
		 * @param string $init_file         Plugin init file.
		 *
		 * @return array Filtered array of plugin meta
		 * @since 1.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAC_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WCAC_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Add plugin action links
		 *
		 * @param mixed $links Plugins links array.
		 *
		 * @return array Filtered link array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcac_panel', true );

			return $links;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAC_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/* === HANDLE DASHBOARD WIDGET === */

		/**
		 * Performs a request to active campaign to update lists stats
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function refresh_lists_for_widget() {
			if ( isset( $_GET['refresh_lists_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['refresh_lists_nonce'] ) ), 'refresh_lists_action' ) ) {
				YITH_WCAC()->retrieve_lists();

				wp_redirect( esc_url_raw( remove_query_arg( 'refresh_lists_nonce' ) ) );
			}
		}

		/**
		 * Register dashboard widget
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_dashboard_widget() {
			wp_add_dashboard_widget(
				'yith_wcac_dashboard_widget',
				'Active Campaign Lists Stats',
				array(
					$this,
					'print_dashboard_widget',
				)
			);
		}

		/**
		 * Print dashboard widget
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_dashboard_widget() {
			$fname  = false;
			$lname  = false;
			$email  = false;
			$avatar = false;
			$result = YITH_WCAC()->do_request( 'users/me' );

			if ( isset( $result->user ) ) {
				$fname = isset( $result->user->firstName ) ? $result->user->firstName : false;
				$lname = isset( $result->user->lastNmae ) ? $result->user->lastNmae : false;
				$email = isset( $result->user->email ) ? $result->user->email : false;

				if ( $account_name = YITH_WCAC()->calculate_account_name() ) {
					$avatar = isset( $result->user->email ) ? '//' . $account_name . '.activehosted.com/gravatar.php?h=' . $result->user->email : false;
				}
			}

			$attributes = [
				'email'    => $email,
				'username' => $fname,
				'name'     => $fname . ' ' . $lname,
				'avatar'   => $avatar,
			];

			$attributes['lists'] = YITH_WCAC()->retrieve_full_lists();

			yith_wcac_get_template( 'active-campaign-dashboard-widget', $attributes, 'admin' );
		}

		/* === HANDLE EXPORT === */

		/**
		 * Manage user export, requesting a batch subscribe
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function manage_users_export() {
			if ( isset( $_POST['export_users'] ) && isset( $_POST['yith_wcac_export_list'] ) ) {

				$export_list               = isset( $_POST['yith_wcac_export_list'] ) ? $_POST['yith_wcac_export_list'] : '';
				$export_status             = isset( $_POST['yith_wcac_export_status'] ) ? $_POST['yith_wcac_export_status'] : '';
				$users                     = $this->_retrieve_users_to_export( array(
					'user_set'         => isset( $_POST['yith_wcac_export_user_set'] ) ? $_POST['yith_wcac_export_user_set'] : 'all',
					'users_selected'   => isset( $_POST['yith_wcac_export_users'] ) ? $_POST['yith_wcac_export_users'] : '',
					'product_filter'   => ! empty( $_POST['yith_wcac_export_filter_product'] ) ? array_filter( $_POST['yith_wcac_export_filter_product'], 'intval' ) : array(),
					'category_filter'  => ! empty( $_POST['yith_wcac_export_filter_category'] ) ? array_filter( $_POST['yith_wcac_export_filter_category'], 'intval' ) : array(),
					'tag_filter'       => ! empty( $_POST['yith_wcac_export_filter_tag'] ) ? array_filter( $_POST['yith_wcac_export_filter_tag'], 'intval' ) : array(),
					'from_date_filter' => isset( $_POST['yith_wcac_export_filter_date']['from'] ) ? $_POST['yith_wcac_export_filter_date']['from'] : '',
					'to_date_filter'   => isset( $_POST['yith_wcac_export_filter_date']['to'] ) ? $_POST['yith_wcac_export_filter_date']['to'] : ''
				) );

				$waiting_products_field = ! empty( $_POST['yith_wcac_export_field_waiting_products'] ) ? $_POST['yith_wcac_export_field_waiting_products'] : '';

				if ( ! empty( $users ) ) {
					foreach ( $users as $user ) {
						$args = array();
						if ( ! empty( $user['user_email'] ) ) {
							$args['email'] = $user['user_email'];

							if ( ! empty( $user['user_first_name'] ) ) {
								$args['first_name'] = $user['user_first_name'];
							}
							if ( ! empty( $user['user_last_name'] ) ) {
								$args['last_name'] = $user['user_last_name'];
							}
							if ( ! empty( $export_list ) ) {
								$args['p'] = array( $export_list => $export_list );
							}

							if ( ! empty( $export_status ) ) {
								$args['status'] = array( $export_list => $export_status );
							}
							if ( $waiting_products_field && ! empty( $user['waiting_products'] ) ) {
								$args[ 'field[' . $waiting_products_field . ', 0]' ] = $user['waiting_products'];
							}

							try {
								YITH_WCAC()->synchronize_contact( $args );
							} catch ( Exception $e ) {
								continue;
							}
						}
					}
				}
			}
		}

		/**
		 * Manage CSV download
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function manage_csv_download() {
			if ( isset( $_POST['export_csv'] ) ) {
				$users = $this->_retrieve_users_to_export( array(
					'user_set'         => isset( $_POST['yith_wcac_csv_user_set'] ) ? $_POST['yith_wcac_csv_user_set'] : 'all',
					'users_selected'   => isset( $_POST['yith_wcac_csv_users'] ) ? $_POST['yith_wcac_csv_users'] : '',
					'product_filter'   => ! empty( $_POST['yith_wcac_csv_filter_product'] ) ? array_filter( $_POST['yith_wcac_csv_filter_product'], 'intval' ) : array(),
					'category_filter'  => ! empty( $_POST['yith_wcac_csv_filter_category'] ) ? array_filter( $_POST['yith_wcac_csv_filter_category'], 'intval' ) : array(),
					'tag_filter'       => ! empty( $_POST['yith_wcac_csv_filter_tag'] ) ? array_filter( $_POST['yith_wcac_csv_filter_tag'], 'intval' ) : array(),
					'from_date_filter' => isset( $_POST['yith_wcac_csv_filter_date']['from'] ) ? $_POST['yith_wcac_csv_filter_date']['from'] : '',
					'to_date_filter'   => isset( $_POST['yith_wcac_csv_filter_date']['to'] ) ? $_POST['yith_wcac_csv_filter_date']['to'] : ''
				) );
				if ( ! empty( $users ) ) {
					$csv = '';

					// add csv heading
					$csv .= '"Email Address", first_name, last_name';

					if ( isset( $_POST['yith_wcac_csv_user_set'] ) && $_POST['yith_wcac_csv_user_set'] == 'waiting_lists' ) {
						$csv .= ',"Waiting Products"';
					}

					$csv .= "\n";

					foreach ( $users as $user ) {
						$csv .= '"' . $user['user_email'] . '",';
						$csv .= '"' . $user['user_first_name'] . '",';
						$csv .= '"' . $user['user_last_name'] . '"';

						if ( isset( $_POST['yith_wcac_csv_user_set'] ) && $_POST['yith_wcac_csv_user_set'] == 'waiting_lists' ) {
							$csv .= ',"' . $user['waiting_products'] . '"';
						}

						$csv .= "\n";
					}

					header( 'Content-Type: text/csv; charset=utf-8' );
					header( 'Content-Disposition: attachment; filename=data.csv' );

					echo $csv;
					die();
				}
			}
		}

		/**
		 * Retrieve users to export
		 *
		 * @param array $args Array of parameters to use to filter users.
		 *
		 * @return array Array of users to export
		 * @since 1.0.0
		 */
		protected function _retrieve_users_to_export( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'user_set'         => 'all',
					'users_selected'   => '',
					'product_filter'   => '',
					'category_filter'  => '',
					'tag_filter'       => '',
					'from_date_filter' => '',
					'to_date_filter'   => '',
				)
			);

			/**
			 * @var $user_set
			 * @var $users_selected
			 * @var $product_filter
			 * @var $category_filter
			 * @var $tag_filter
			 * @var $from_date_filter
			 * @var $to_date_filter
			 */
			extract( $args );

			$users_id = array();
			$users    = array();

			switch ( $user_set ) {
				case 'set':
					$users_id = is_serialized( $users_selected ) ? unserialize( $users_selected ) : explode( ',', $users_selected );
					break;
				case 'filter':
					$get_posts_args = array(
						'posts_per_page' => - 1,
						'post_type'      => wc_get_order_types( 'view-orders' ),
						'post_status'    => array( 'wc-processing', 'wc-completed' ),
					);

					if ( ! empty( $from_date_filter ) ) {
						$get_posts_args['meta_query'][] = array(
							'key'     => '_completed_date',
							'value'   => $from_date_filter . ' 00:00:00',
							'compare' => '>=',
						);
					}

					if ( ! empty( $to_date_filter ) ) {
						$get_posts_args['meta_query'][] = array(
							'key'     => '_completed_date',
							'value'   => $to_date_filter . ' 23:59:59',
							'compare' => '<=',
						);
					}

					$query  = new WP_Query( $get_posts_args );
					$orders = $query->get_posts();

					if ( ! empty( $orders ) ) {
						foreach ( $orders as $order ) {
							$order_obj   = wc_get_order( $order->ID );
							$customer    = yit_get_prop( $order_obj, 'customer_user' );
							$order_items = $order_obj->get_items( 'line_item' );

							if ( ( ! empty( $product_filter ) || ! empty( $category_filter ) || ! empty( $tag_filter ) ) && ! empty( $order_items ) ) {
								foreach ( $order_items as $item_id => $item ) {
									$valid        = true;
									$product_id   = $item['product_id'];
									$variation_id = $item['variation_id'];

									// filter for products, if any selected.
									if ( ! empty( $product_filter ) && ! ( in_array( $product_id, $product_filter ) || in_array( $variation_id, $product_filter ) ) ) {
										continue;
									}

									// filter for category, if any selected.
									if ( ! empty( $category_filter ) ) {
										$product_cats    = get_the_terms( $product_id, 'product_cat' );
										$product_cat_ids = array();

										if ( ! empty( $product_cats ) ) {
											foreach ( $product_cats as $term ) {
												$product_cat_ids[] = $term->term_id;
											}
										}

										$cat_intersect = array_intersect( $product_cat_ids, $category_filter );

										if ( empty( $cat_intersect ) ) {
											continue;
										}
									}

									// filter for tag, if any selected.
									if ( ! empty( $tag_filter ) ) {
										$product_tags    = get_the_terms( $product_id, 'product_tag' );
										$product_tag_ids = array();

										if ( ! empty( $product_tags ) ) {
											foreach ( $product_tags as $tag ) {
												$product_tag_ids[] = $tag->term_id;
											}
										}

										$tag_intersect = array_intersect( $product_tag_ids, $tag_filter );

										if ( empty( $tag_intersect ) ) {
											continue;
										}
									}

									if ( ! in_array( $customer, $users_id ) && ! empty( $customer ) ) {
										$users_id[] = $customer;
									}
								}
							} else {
								if ( ! in_array( $customer, $users_id ) ) {
									$users_id[] = $customer;
								}
							}
						}
					}

					break;
				case 'customers':
					$users_selected = get_users(
						array(
							'meta_key'   => 'paying_customer',
							'meta_value' => 1,
							'fields'     => array( 'ID' ),
						)
					);

					if ( ! empty( $users_selected ) ) {
						foreach ( $users_selected as $user_obj ) {
							$users_id[] = $user_obj->ID;
						}
					}
					break;
				case 'waiting_lists':
					global $wpdb;

					$formatted_set = array();
					$waiting_lists = $wpdb->get_results( $wpdb->prepare( "SELECT pm.post_id AS product_id, p.post_name AS product_slug, pm.meta_value AS list FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID WHERE pm.meta_key = %s", '_yith_wcwtl_users_list' ), ARRAY_A );

					if ( ! empty( $waiting_lists ) ) {
						foreach ( $waiting_lists as $record ) {
							$waiting_list = maybe_unserialize( $record['list'] );
							$product_slug = $record['product_slug'];

							if ( ! empty( $waiting_list ) ) {
								foreach ( $waiting_list as $registered_email ) {
									if ( ! in_array( $registered_email, array_keys( $formatted_set ) ) ) {
										$formatted_set[ $registered_email ] = array();
									}

									$formatted_set[ $registered_email ][] = $product_slug;
								}
							}
						}
					}

					if ( ! empty( $formatted_set ) ) {
						foreach ( $formatted_set as $email => $products ) {
							$users[] = array(
								'user_email'       => $email,
								'waiting_products' => implode( ', ', $products ),
								'user_first_name'  => false,
								'user_last_name'   => false,
							);
						}
					}
					break;
				case 'all':
				default:
					$users_selected = get_users(
						array(
							'fields' => array( 'ID' ),
						)
					);

					if ( ! empty( $users_selected ) ) {
						foreach ( $users_selected as $user_obj ) {
							$users_id[] = $user_obj->ID;
						}
					}
					break;
			}

			if ( ! empty( $users_id ) ) {
				foreach ( $users_id as $id ) {
					$user = get_user_by( 'id', $id );

					if ( ! $user ) {
						continue;
					}

					$users[] = array(
						'id'              => $id,
						'user_email'      => $user->user_email,
						'user_first_name' => ! empty( $user->billing_first_name ) ? $user->billing_first_name : $user->first_name,
						'user_last_name'  => ! empty( $user->billing_last_name ) ? $user->billing_last_name : $user->last_name,
					);
				}
			}

			return $users;
		}

		/* === HANDLE METABOX === */

		/**
		 * Add field to Advanced View in product data metabox
		 *
		 * @return void
		 */
		public function add_simple_product_field() {
			global $post;

			$product = wc_get_product( $post->ID );

			if ( ! $product ) {
				return;
			}

			$value        = $product->get_meta( 'yith_wcac_product_tags', true );
			$value        = is_array( $value ) ? $value : array();
			$tags_options = YITH_WCAC()->retrieve_tags();

			?>
			<div class="options_group">
				<p class="form-field">
					<label for="yith_wcac_product_tags"><?php esc_html_e( 'Active Campaign\'s tags', 'yith-woocommerce-active-campaign' ); ?></label>
					<select id="yith_wcac_product_tags" name="yith_wcac_product_tags[]" multiple="multiple" data-allow-clear="true" class="wc-enhanced-select" style="width: 50%">
						<?php
						if ( ! empty( $tags_options ) ) :
							foreach ( $tags_options as $name => $option ) :
								?>
								<option value="<?php echo esc_attr( $name ); ?>" <?php selected( in_array( $name, $value ) ); ?> ><?php echo esc_html( $option ); ?></option>
								<?php
							endforeach;
						endif;
						?>
					</select>
				</p>
			</div>
			<?php
		}

		/**
		 * Add field in variation panel
		 *
		 * @param int      $loop           Variation ordinal number.
		 * @param mixed    $variation_data Variation data.
		 * @param \WP_Post $variation      Variation post.
		 *
		 * @return void
		 */
		public function add_variable_product_field( $loop, $variation_data, $variation ) {
			$variation    = new WC_Product_Variation( $variation->ID );
			$value        = $variation->get_meta( 'yith_wcac_product_tags', true );
			$value        = is_array( $value ) ? $value : array();
			$tags_options = YITH_WCAC()->retrieve_tags();

			?>
			<div>
				<p class="form-field form-row-full form-field">
					<label for="yith_wcaf_variable_product_tags<?php echo esc_attr( $loop ); ?>"><?php esc_html_e( 'Active Campaign\'s tags', 'yith-woocommerce-active-campaign' ); ?></label>
					<select id="yith_wcaf_variable_product_tags<?php echo esc_attr( $loop ); ?>" name="yith_wcaf_variable_product_tags[<?php echo esc_attr( $loop ); ?>][]" multiple="multiple" data-allow-clear="true" class="wc-enhanced-select" style="width: 100%">
						<?php
						if ( ! empty( $tags_options ) ) :
							foreach ( $tags_options as $name => $option ) :
								?>
								<option value="<?php echo esc_attr( $name ); ?>" <?php selected( in_array( $name, $value ) ); ?> ><?php echo esc_html( $option ); ?></option>
								<?php
							endforeach;
						endif;
						?>
					</select>
				</p>
			</div>
			<?php
		}

		/**
		 * Save custom fields for product object
		 *
		 * @param \WC_Product $product Current product.
		 *
		 * @return void
		 */
		public function save_simple_product_field( $product ) {
			if ( ! isset( $_POST['yith_wcac_product_tags'] ) || ! is_array( $_POST['yith_wcac_product_tags'] ) ) { // phpcs:ignore
				return;
			}

			$tags = array_filter( array_map( 'sanitize_text_field', $_POST['yith_wcac_product_tags'] ) ); // phpcs:ignore

			if ( ! empty( $tags ) ) {
				$product->update_meta_data( 'yith_wcac_product_tags', $tags );
				$product->save();
			}
		}

		/**
		 * Save custom  fields for variation object
		 *
		 * @param int $variation_id Variation id.
		 * @param int $loop         Variation ordinal number.
		 *
		 * @return void
		 */
		public function save_variable_product_field( $variation_id, $loop ) {
			if ( ! isset( $_POST['yith_wcaf_variable_product_tags'] ) || ! isset( $_POST['yith_wcaf_variable_product_tags'][ $loop ] ) || ! is_array( $_POST['yith_wcaf_variable_product_tags'][ $loop ] ) ) { // phpcs:ignore
				return;
			}

			$variation = new WC_Product_Variation( $variation_id );
			$tags      = array_filter( array_map( 'sanitize_text_field', $_POST['yith_wcaf_variable_product_tags'][ $loop ] ) ); // phpcs:ignore

			if ( ! $variation ) {
				return;
			}

			if ( ! empty( $tags ) ) {
				$variation->update_meta_data( 'yith_wcac_product_tags', $tags );
				$variation->save();
			}
		}

		/**
		 * Add metabox to order edit page
		 *
		 * @return void
		 * @since 1.1.3
		 */
		public function add_order_metabox() {
			add_meta_box(
				'yith_wcac_user_preferences',
				__( 'Active Campaign status', 'yith-woocommerce-active-campaign' ),
				array( $this, 'print_user_preferences_metabox' ),
				'shop_order',
				'side'
			);
		}

		/**
		 * Print metabox for order edit page
		 *
		 * @param \WP_Post $post Post.
		 *
		 * @return void
		 */
		public function print_user_preferences_metabox( $post ) {
			$order = wc_get_order( $post );

			if ( ! $order ) {
				return;
			}

			$show_checkbox       = yit_get_prop( $order, '_yith_wcac_show_checkbox', true );
			$submitted_value     = yit_get_prop( $order, '_yith_wcac_submitted_value', true );
			$customer_subscribed = yit_get_prop( $order, '_yith_wcac_customer_subscribed', true );
			$personal_data       = yit_get_prop( $order, '_yith_wcac_personal_data', true );

			include( YITH_WCAC_DIR . 'templates/admin/metaboxes/user-preferences-metabox.php' );
		}
	}
}

/**
 * Unique access to instance of YITH_WCAC_Admin class
 *
 * @return \YITH_WCAC_Admin
 * @since 1.0.0
 */
function YITH_WCAC_Admin() {
	$instance = apply_filters( 'yith_wcac_admin_single_instance', null );

	if ( ! $instance ) {
		$instance = YITH_WCAC_Admin::get_instance();
	}

	return $instance;
}
