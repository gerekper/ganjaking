<?php
/**
 * Store integration class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Store' ) ) {
	/**
	 * WooCommerce MailChimp Store integration
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Store {
		/**
		 * Background processed
		 *
		 * @var \YITH_WCMC_Background_Process
		 * @since 1.0.0
		 */
		protected $_background_process;

		/**
		 * Processed items register
		 *
		 * @var \YITH_WCMC_Store_Register
		 * @since 1.0.0
		 */
		protected $_register;

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMC_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMC_Store
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCMC_Store
		 * @since 1.0.0
		 */
		public function __construct() {
			// setup background process
			$this->_background_process = new YITH_WCMC_Background_Process();
			$this->_register           = new YITH_WCMC_Store_Register();

			// register store
			add_action( 'yit_panel_wc_after_update', array( $this, 'execute_store_creation' ) );

			// register products /variations / coupons
			add_action( 'save_post', array( $this, 'process_item' ), 20, 1 );
			add_action( 'trashed_post', array( $this, 'process_item' ), 20, 1 );
			add_action( 'untrashed_post', array( $this, 'process_item' ), 20, 1 );

			// register carts for Abandoned Cart automations
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_add_to_cart', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'process_cart' ) );

			// sync cron handling
			add_action( 'admin_notices', array( $this, 'print_admin_notices' ) );
			add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
			add_action( 'yith_wcmc_sync_cron', array( $this, 'sync_step' ) );
			add_action( 'admin_action_yith_wcmc_sync_start', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcmc_sync_restart', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcmc_sync_resume', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcmc_sync_stop', array( $this, 'sync_action' ) );
		}

		/**
		 * Add schedule specific of YITH WCMC Store crons
		 *
		 * @param $schedules array Array of existing schedules
		 *
		 * @return array Array of filtered schedules
		 */
		public function add_cron_schedule( $schedules ) {
			$interval = apply_filters( 'yith_wcmc_cron_schedule', 2 );

			$schedules['yith_wcmc_cron_schedule'] = array(
				'interval' => MINUTE_IN_SECONDS * $interval,
				'display'  => sprintf( __( 'Every %d Minutes' ), $interval ),
			);

			return $schedules;
		}

		/* === UTILS METHODS === */

		/**
		 * Generates unique store's id
		 *
		 * @return string Unique store id
		 */
		public function get_store_uniqid() {
			return apply_filters( 'yith_wcmc_store_uniqid', md5( site_url() . ( function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 1 ) ) );
		}

		/**
		 * Generate unique id for each object in the store
		 *
		 * @param $object_type string Post type
		 * @param $id          int Post id
		 * @param $object      mixed Object being processed
		 *
		 * @return string Unique object id
		 * @filter yith_wcmc_store_{$object_type}_uniqid
		 */
		public function get_object_uniqid( $object_type, $id, $object = null ) {
			return apply_filters( "yith_wcmc_store_{$object_type}_uniqid", md5( $id ), $object );
		}

		/**
		 * Returns details of the store, retrieved from MailChimp
		 *
		 * @return array|bool Array of information, or false on failure
		 */
		public function get_store_info() {
			$store_id = $this->get_store_uniqid();

			$result = YITH_WCMC()->do_request( 'get', "ecommerce/stores/{$store_id}", array() );

			if ( isset( $result['status'] ) && ( ! $result['status'] || $result['status'] === 404 ) ) {
				return false;
			}

			return $result;
		}

		/**
		 * Returns customers details, to attach them in customer profile on MailChimp
		 *
		 * @param $customer_email string Customer email
		 *
		 * @return array Array with the following indexes: orders_count/total_spent
		 */
		public function get_customer_stats( $customer_email ) {
			$customer_orders = wc_get_orders( array(
				'billing_email' => $customer_email,
				'status'        => array( 'wc-completed', 'wc-processing' ),
				'limit'         => - 1
			) );

			$orders_count = count( $customer_orders );
			$total_spent  = 0;

			if ( ! empty( $customer_orders ) ) {
				foreach ( $customer_orders as $customer_order ) {
					$total_spent += $customer_order->get_total( 'edit' );
				}
			}

			return array(
				'orders_count' => $orders_count,
				'total_spent'  => $total_spent
			);
		}

		/**
		 * Check whether current store is connected
		 *
		 * @param $list_id      string|bool List id store should be connected to; false if no check should be performed over list
		 * @param $force_update bool Whether to force update of API results, to retrieve fresh data
		 *
		 * @return bool Whether current store is connected
		 */
		public function is_store_connected( $list_id = false, $force_update = false ) {
			$store_id = $this->get_store_uniqid();

			$result = YITH_WCMC()->do_request( 'get', "ecommerce/stores/{$store_id}", array(), $force_update );

			if ( isset( $result['status'] ) && ( ! $result['status'] || $result['status'] === 404 ) ) {
				return false;
			}

			if ( ! empty( $list_id ) && ( ! isset( $result['list_id'] ) || $result['list_id'] != $list_id ) ) {
				return false;
			}

			return $result['id'];
		}

		/* === STORE CONNECTION PROCESS === */

		/**
		 * Register store on MailChimp
		 *
		 * @return void
		 * @use \YITH_WCMC_Store::maybe_connect_store
		 */
		public function execute_store_creation() {
			if ( ! ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcmc_panel' && isset( $_GET['tab'] ) && $_GET['tab'] == 'store' ) ) {
				return;
			}

			$list_id = get_option( 'yith_wcmc_store_integration_list' );

			if ( ! $list_id ) {
				return;
			}

			$is_store_connected = $this->is_store_connected( false, true );

			if ( $is_store_connected ) {
				return;
			}

			$store_name           = get_option( 'yith_wcmc_store_integration_name' );
			$store_address_line_1 = get_option( 'yith_wcmc_store_address_line_1' );
			$store_address_line_2 = get_option( 'yith_wcmc_store_address_line_2' );
			$store_city           = get_option( 'yith_wcmc_store_city' );
			$store_postcode       = get_option( 'yith_wcmc_store_postcode' );
			$store_state          = get_option( 'yith_wcmc_store_state' );
			$store_country        = get_option( 'yith_wcmc_store_country' );

			$args = array(
				'name'    => $store_name,
				'address' => array(
					'address1'     => $store_address_line_1,
					'address2'     => $store_address_line_2,
					'city'         => $store_city,
					'postal_code'  => $store_postcode,
					'province'     => $store_state,
					'country_code' => $store_country,
				)
			);

			$id = $this->maybe_connect_store( $list_id, $args );

			if ( $id ) {
				wp_redirect( add_query_arg( array(
					'page' => 'yith_wcmc_panel',
					'tab'  => 'store'
				), admin_url( 'admin.php' ) ) );
			}
		}

		/**
		 * Execute API call to connect store to a MailChimp list, if store is not connected already
		 *
		 * @param $list_id string List id to connect to
		 * @param $args    array Array of arguments to use for API call
		 *
		 * @return mixed Result of operation
		 */
		public function maybe_connect_store( $list_id, $args = array() ) {
			$is_store_connected = $this->is_store_connected( $list_id, true );

			if ( $is_store_connected ) {
				return $this->get_store_uniqid();
			}

			return $this->_create_store( $list_id, $args );
		}

		/**
		 * Execute API call to disconnect store from MailChimp, if store is connected
		 *
		 * @return mixed Result of operation
		 */
		public function maybe_disconnect_store() {
			$is_store_connected = $this->is_store_connected( false, true );

			if ( ! $is_store_connected ) {
				return true;
			}

			return $this->_delete_store();
		}

		/**
		 * Creates a store on MailChimp account for the list_id
		 *
		 * @param $list_id string List id
		 *
		 * @return string Store id
		 *
		 * @since 1.1.0
		 */
		protected function _create_store( $list_id, $args = array() ) {
			$domain   = apply_filters( 'yith_wcmc_store_domain', site_url() );
			$store_id = $this->get_store_uniqid();

			YITH_WCMC_Premium()->log( _x( 'Attempting to connect store', 'log message', 'yith-woocommerce-mailchimp' ) );

			$defaults = array(
				'id'            => $store_id,
				'list_id'       => apply_filters( 'yith_wcmc_store_list', $list_id ),
				'name'          => apply_filters( 'yith_wcmc_store_name', sprintf( '%s - %s', get_option( 'blogname' ), get_option( 'blogdescription' ) ) ),
				'platform'      => 'woocommerce',
				//sprintf( '%s %s', __( 'YITH WooCommerce MailChimp', 'yith-woocommerce-mailchimp' ), YITH_WCMC_VERSION ),
				'currency_code' => get_woocommerce_currency(),
				'domain'        => $domain,
				'address'       => array(
					'address1'     => WC()->countries->get_base_address(),
					'address2'     => WC()->countries->get_base_address_2(),
					'city'         => WC()->countries->get_base_city(),
					'postal_code'  => WC()->countries->get_base_postcode(),
					'province'     => WC()->countries->get_base_state(),
					'country_code' => WC()->countries->get_base_country(),
				)
			);

			$args = wp_parse_args( $args, $defaults );

			$result = YITH_WCMC()->do_request( 'post', 'ecommerce/stores', $args );

			if ( isset( $result['status'] ) && ! $result['status'] ) {
				return false;
			}

			YITH_WCMC_Premium()->delete_cached_data( "ecommerce/stores/{$store_id}" );
			YITH_WCMC_Premium()->log( _x( 'Store connected!', 'log message', 'yith-woocommerce-mailchimp' ) );

			return $result['id'];
		}

		/**
		 * Delete a store from MailChimp
		 *
		 * @return bool Status of the operation
		 */
		protected function _delete_store() {
			$store_id = $this->get_store_uniqid();

			YITH_WCMC_Premium()->log( _x( 'Attempting to disconnect store', 'log message', 'yith-woocommerce-mailchimp' ) );

			$result = YITH_WCMC()->do_request( 'delete', "ecommerce/stores/{$store_id}" );

			if ( isset( $result['status'] ) && ! $result['status'] ) {
				/**
				 * Delete cached data even if delete operation failed
				 * This way we force system to check again if store is actually still connected on next page loading
				 */
				YITH_WCMC_Premium()->delete_cached_data( "ecommerce/stores/{$store_id}" );

				return false;
			}

			delete_option( 'yith_wcmc_store_integration_list' );
			delete_option( 'yith_wcmc_processed_items' );
			delete_option( 'yith_wcmc_is_store_syncing' );
			delete_option( 'yith_wcmc_is_store_synced' );
			delete_option( 'yith_wcmc_is_store_sync_paused' );

			YITH_WCMC_Premium()->delete_cached_data( "ecommerce/stores/{$store_id}" );
			YITH_WCMC_Premium()->log( _x( 'Store disconnected!', 'log message', 'yith-woocommerce-mailchimp' ) );

			$this->_register->truncate();

			return true;
		}

		/* === SYNC STORE === */

		/**
		 * Print admin notices in Store option tab of the plugin
		 *
		 * @return void
		 */
		public function print_admin_notices() {
			$current_screen = get_current_screen();

			if ( 'yith-plugins_page_yith_wcmc_panel' != $current_screen->id || ! isset( $_GET['tab'] ) || 'store' != $_GET['tab'] ) {
				return;
			}

			if ( $this->is_store_syncing() ) {
				$status_message = $this->get_sync_status_message();

				?>
				<div class="notice notice-success">
					<p>
						<?php _e( 'Your store is currently being synced! This may take a while, but won\'t affect performance of your store. You can keep track of the status of the operation in this page', 'yith-woocommerce-mailchimp' ) ?>
					</p>
					<p>
						<?php echo $status_message ?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Returns a message that describes current status of the synchronization
		 *
		 * @return string Status message
		 */
		public function get_sync_status_message() {
			$sync_info = YITH_WCMC_Store()->get_sync_details();
			$complete  = true;

			if ( ! empty( $sync_info ) ) {
				$textual_counts = array();

				foreach ( $sync_info as $post_type_slug => $counts ) {
					if ( $counts['processed'] != $counts['count'] ) {
						$complete = false;
					}

					$post_type = get_post_type_object( $post_type_slug );

					if ( ! $post_type ) {
						continue;
					}

					$post_type_name   = $post_type->label;
					$textual_counts[] = sprintf(
					/**
					 * @since 2.1.5
					 */
						__( '<b>%s</b> %s/%s (%s%%)', 'yith-woocommerce-mailchimp' ),
						$post_type_name,
						$counts['processed'],
						$counts['count'],
						$counts['count'] ? floor( $counts['processed'] / $counts['count'] * 100 ) : 100
					);
				}
			}

			if ( $complete ) {
				/**
				 * @since 2.1.5
				 */
				return __( 'Synchronization complete', 'yith-woocommerce-mailchimp' );
			}

			$status_message = __( 'Current synchronization status:', 'yith-woocommerce-mailchimp' );
			$status_message .= " ";
			$status_message .= implode( ' | ', $textual_counts );

			return $status_message;
		}

		/**
		 * Handle sync buttons
		 *
		 * @return void
		 */
		public function sync_action() {
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

			if ( $action ) {
				$action = str_replace( 'yith_wcmc_', '', $action );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting sync action: %s', 'log message', 'yith-woocommerce-mailchimp' ), $action ) );

				switch ( $action ) {
					case 'sync_start':
						$this->sync();
						break;
					case 'sync_resume':
						$this->resume_syncing();
						break;
					case 'sync_stop':
						$this->stop_syncing();
						break;
					case 'sync_restart':
						update_option( 'yith_wcmc_is_store_synced', false );
						update_option( 'yith_wcmc_processed_items', array() );

						$this->sync();
						break;
				}

				YITH_WCMC_Premium()->log( sprintf( _x( 'Sync action performed: %s', 'log message', 'yith-woocommerce-mailchimp' ), $action ) );
			}

			wp_redirect( add_query_arg( array(
				'page' => 'yith_wcmc_panel',
				'tab'  => 'store'
			), admin_url( 'admin.php' ) ) );
			die;
		}

		/**
		 * Start synchronization for store items
		 *
		 * @return void
		 */
		public function sync() {
			$this->_sync_started();

			if ( ! wp_next_scheduled( 'yith_wcmc_sync_cron' ) ) {
				wp_schedule_event( time(), 'yith_wcmc_cron_schedule', 'yith_wcmc_sync_cron' );
			}
		}

		/**
		 * Resume paused syncing
		 *
		 * @return void
		 */
		public function resume_syncing() {
			$this->sync();
		}

		/**
		 * Pause syncing
		 *
		 * @return void
		 */
		public function stop_syncing() {
			wp_clear_scheduled_hook( 'yith_wcmc_sync_cron' );

			$this->_sync_paused();
		}

		/**
		 * Process next step of the synchronization process
		 *
		 * @return void
		 */
		public function sync_step() {
			if ( ! $this->is_store_syncing() ) {
				wp_clear_scheduled_hook( 'yith_wcmc_sync_cron' );

				return;
			}

			$items_to_process = $this->_get_next_items_to_sync();

			if ( ! empty( $items_to_process ) ) {
				foreach ( $items_to_process as $item ) {
					$this->_background_process->push_to_queue( $item );
				}

				$this->_background_process->save();

				YITH_WCMC_Premium()->log( sprintf( _x( "New sync step: %s\n", 'log message', 'yith-woocommerce-mailchimp' ), print_r( $items_to_process, 1 ) ) );
			} else {
				wp_clear_scheduled_hook( 'yith_wcmc_sync_cron' );
				$this->_sync_completed();

				YITH_WCMC_Premium()->log( _x( 'Sync completed!', 'log message', 'yith-woocommerce-mailchimp' ) );
			}
		}

		/**
		 * Check whether store is currently syncing
		 *
		 * @return bool Whether store is syncing
		 */
		public function is_store_syncing() {
			$is_store_syncing = get_option( 'yith_wcmc_is_store_syncing', false );

			return (bool) $is_store_syncing && ! $this->is_store_synced();
		}

		/**
		 * Check whether store is synced
		 *
		 * @return bool Whether store sync is completed
		 */
		public function is_store_synced() {
			$is_store_synced = get_option( 'yith_wcmc_is_store_synced', false );

			return (bool) $is_store_synced;
		}

		/**
		 * Check whether synchronization was paused
		 *
		 * @return bool Whether store sync was paused
		 */
		public function is_sync_paused() {
			$is_store_synced = get_option( 'yith_wcmc_is_store_sync_paused', false );

			return (bool) $is_store_synced;
		}

		/**
		 * Get information about current status of the sync
		 *
		 * @return array Each item of the array refers to a post_type and contain overall count and processed count
		 */
		public function get_sync_details() {
			global $wpdb;

			$where           = call_user_func_array( array(
				$wpdb,
				'prepare'
			), apply_filters( 'yith_wcmc_get_next_items_to_synch_where', array( '%d=%d', array( 1, 1 ) ) ) );
			$post_type_count = $wpdb->get_results( "SELECT post_type, COUNT(ID) AS count FROM {$wpdb->posts} WHERE post_status IN ( 'publish', 'wc-completed', 'wc-processing' ) AND {$where} GROUP BY post_type", ARRAY_A );

			if ( ! empty( $post_type_count ) ) {
				$post_counts = array_combine( wp_list_pluck( $post_type_count, 'post_type' ), wp_list_pluck( $post_type_count, 'count' ) );
			}

			$processed_posts     = get_option( 'yith_wcmc_processed_items', array() );
			$supported_post_type = $this->_get_post_types_to_sync();
			$info                = array();

			foreach ( $supported_post_type as $post_type ) {
				$info[ $post_type ] = array(
					'count'     => isset( $post_counts[ $post_type ] ) ? $post_counts[ $post_type ] : 0,
					'processed' => isset( $processed_posts[ $post_type ] ) ? min( $processed_posts[ $post_type ], isset( $post_counts[ $post_type ] ) ? $post_counts[ $post_type ] : 0 ) : 0
				);
			}

			return $info;
		}

		/**
		 * Update processed count for a single post type
		 *
		 * @param $post_type string Post type to update
		 * @param $new_count int New count of processed items for the post type
		 *
		 * @return void
		 */
		public function update_processed_count( $post_type, $new_count ) {
			$processed_posts = get_option( 'yith_wcmc_processed_items', array() );

			if ( isset( $processed_posts[ $post_type ] ) ) {
				$processed_posts[ $post_type ] += $new_count;
			} else {
				$processed_posts[ $post_type ] = $new_count;
			}

			update_option( 'yith_wcmc_processed_items', $processed_posts );
		}

		/**
		 * Allow third party developers to manually sync items
		 *
		 * @param $items_to_process array Array of items to process; each item is an array ( type => 'item_type', 'id' => 'item_id' )
		 *
		 * @return void
		 */
		public function manual_syncing( $items_to_process ) {
			if ( ! empty( $items_to_process ) ) {
				foreach ( $items_to_process as $item ) {
					$this->_background_process->push_to_queue( $item );
				}

				$this->_background_process->save();

				YITH_WCMC_Premium()->log( sprintf( _x( "New sync step: %s\n", 'log message', 'yith-woocommerce-mailchimp' ), print_r( $items_to_process, 1 ) ) );
			}
		}

		/**
		 * Register start of the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_started() {
			$store_id = $this->get_store_uniqid();

			$args = array(
				'is_syncing' => true
			);

			YITH_WCMC_Premium()->do_request( 'patch', "ecommerce/stores/{$store_id}", $args );

			update_option( 'yith_wcmc_is_store_syncing', true );
			update_option( 'yith_wcmc_is_store_sync_paused', false );
		}

		/**
		 * Register pause for the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_paused() {
			$store_id = $this->get_store_uniqid();

			$args = array(
				'is_syncing' => false
			);

			YITH_WCMC_Premium()->do_request( 'patch', "ecommerce/stores/{$store_id}", $args );

			update_option( 'yith_wcmc_is_store_syncing', false );
			update_option( 'yith_wcmc_is_store_sync_paused', true );
		}

		/**
		 * Register completion of the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_completed() {
			$store_id = $this->get_store_uniqid();

			$args = array(
				'is_syncing' => false
			);

			YITH_WCMC_Premium()->do_request( 'patch', "ecommerce/stores/{$store_id}", $args );

			update_option( 'yith_wcmc_is_store_syncing', false );
			update_option( 'yith_wcmc_is_store_synced', true );
		}

		/**
		 * Returns post types to sync
		 *
		 * @return array Post types to sync
		 * @since 2.1.5
		 */
		protected function _get_post_types_to_sync() {
			return apply_filters( 'yith_wcmc_supported_post_type_to_sync', array(
				'product',
				'shop_coupon',
				'shop_order'
			) );
		}

		/**
		 * Retrieve next items to be queued for processing
		 *
		 * @return array Item to be queued for background processing
		 */
		protected function _get_next_items_to_sync() {
			$sync_info = $this->get_sync_details();

			// first check if there are products to retrieve
			if ( $sync_info['product']['processed'] < $sync_info['product']['count'] ) {
				$post_type = 'product';
			} // first check if there are products to retrieve
			elseif ( $sync_info['shop_coupon']['processed'] < $sync_info['shop_coupon']['count'] ) {
				$post_type = 'shop_coupon';
			} // first check if there are products to retrieve
			elseif ( $sync_info['shop_order']['processed'] < $sync_info['shop_order']['count'] ) {
				$post_type = 'shop_order';
			}

			if ( ! isset( $post_type ) ) {
				return array();
			}

			$items = $this->_get_next_items( $post_type, $sync_info[ $post_type ]['processed'] );

			if ( empty( $items ) ) {
				$sync_info[ $post_type ]['processed'] = $sync_info[ $post_type ]['count'];

				$this->update_processed_count( $post_type, $sync_info[ $post_type ]['count'] );
				$items = $this->_get_next_items_to_sync();
			} else {
				$this->update_processed_count( $post_type, count( $items ) );
			}

			return $items;
		}

		/**
		 * Retrieve next posts of a specific post type, to be queued for processing
		 *
		 * @param $post_type string Post type
		 * @param $offset    int Offset to be used as starting point
		 *
		 * @return array Posts to be processed
		 */
		protected function _get_next_items( $post_type, $offset ) {
			global $wpdb;

			$batch_count = apply_filters( 'yith_wcmc_batch_count', 100 );
			$where       = call_user_func_array( array(
				$wpdb,
				'prepare'
			), apply_filters( 'yith_wcmc_get_next_items_to_synch_where', array( '%d=%d', array( 1, 1 ) ) ) );

			$res   = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ( 'publish', 'wc-processing', 'wc-completed' ) AND {$where} ORDER BY post_date DESC LIMIT %d, %d", $post_type, $offset, $batch_count ) );
			$items = array();

			switch ( $post_type ) {
				case 'shop_coupon':
					$type = 'promo_rule';
					break;
				case 'shop_order':
					$type = 'order';
					break;
				default:
					$type = $post_type;
			}

			if ( ! empty( $res ) ) {
				foreach ( $res as $item ) {
					$items[] = array(
						'type' => $type,
						'id'   => $item
					);
				}
			}

			return $items;
		}

		/* === REGISTER ITEMS INTO THE STORE === */

		/**
		 * Process single item, and register it in MailChimp
		 *
		 * @param $item_id int ID of the item to process
		 *
		 * @return void
		 */
		public function process_item( $item_id ) {
			$post_type   = get_post_type( $item_id );
			$post_status = get_post_status( $item_id );
			$is_publish  = 'publish' == $post_status;

			if ( ! apply_filters( 'yith_wcmc_process_item', true, $item_id, $post_type, $post_status ) ) {
				return;
			}

			switch ( $post_type ) {
				case 'product':
					if ( $is_publish ) {
						$this->maybe_process_product( $item_id );
					} else {
						$this->maybe_delete_product( $item_id );
					}
					break;
				case 'product_variation':
					$product_id = wp_get_post_parent_id( $item_id );

					if ( $is_publish ) {
						$this->maybe_process_product_variant( $item_id, $product_id );
					} else {
						$this->maybe_delete_product_variant( $item_id, $product_id );
					}
					break;
				case 'shop_coupon':

					if ( $is_publish ) {
						$this->maybe_process_promo_rule( $item_id );
					} else {
						$this->maybe_delete_promo_rule( $item_id );
					}
					break;
				case 'shop_order':
					if ( in_array( $post_status, array( 'wc-completed', 'wc-processing' ) ) ) {
						$this->maybe_process_order( $item_id );
					} else {
						$this->maybe_delete_order( $item_id );
					}
					break;
			}
		}

		/**
		 * Process cart item, and schedule it for registration
		 *
		 * @param $value mixed Used as return value, when method is hooked to a filter
		 *
		 * @return mixed Returns $value as it is
		 */
		public function process_cart( $value = null ) {
			if ( is_user_logged_in() ) {
				//$this->maybe_process_cart( get_current_user_id() );

				$this->_background_process->push_to_queue( array(
					'type' => 'cart',
					'id'   => get_current_user_id()
				) )->save();
			}

			// return sent value, just to avoid causing issues when method is hooked to filters
			return $value;
		}

		/**
		 * Register order if store is connected
		 *
		 * @param $order \WC_Order
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_order( $order ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$order = $order instanceof WC_Order ? $order : wc_get_order( $order );

			if ( ! $order ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to process order %s', 'log message', 'yith-woocommerce-mailchip' ), $order->get_id() ) );

			$order_processed = $this->_register->is_item_processed( $order->get_id(), 'order' );

			$customer_id = $order->get_customer_id();

			$show_checkbox   = yit_get_prop( $order, '_yith_wcmc_show_checkbox', true );
			$submitted_value = yit_get_prop( $order, '_yith_wcmc_submitted_value', true );

			// this could be a very resource-demanding process, and it is not required when syncing the entire shop, as
			// MailChimp will create its stats on the go
			// for this reason we include  yith_wcmc_process_customer_history filter to let third party dev skip this
			if ( $customer_id && apply_filters( 'yith_wcmc_process_customer_history', true ) ) {
				$customer_stats = $this->get_customer_stats( $order->get_billing_email() );
			}

			$campaign_data      = $order->get_meta( '_yith_wcmc_ecommerce_360_data', true );
			$campaign_processed = $order->get_meta( '_yith_wcmc_ecommerce_360_processed', true );

			$financial_status   = 'paid';
			$fulfillment_status = $order->has_status( 'completed' ) ? 'fulfilled' : '';

			$refunds_total = $order->get_total_refunded();
			$order_total   = $order->get_total();

			if ( $refunds_total && $refunds_total < $order_total ) {
				$financial_status = 'partially_refunded';
			} elseif ( $refunds_total == $order_total ) {
				$financial_status = 'refunded';
			}

			if ( $order->get_refunds() ) {
				$promos = array();
			}

			if ( $coupons = $order->get_items( 'coupon' ) ) {
				foreach ( $coupons as $coupon_item ) {
					/**
					 * @var $coupon      \WC_Coupon
					 * @var $coupon_item \WC_Order_Item_Coupon
					 */
					$coupon = new WC_Coupon( $coupon_item->get_code() );

					$coupon_type = $coupon->get_discount_type() == 'percent' ? 'percentage' : 'fixed';

					$promos[] = array(
						'code'              => $coupon_item->get_code(),
						'amount_discounted' => $coupon_item->get_discount(),
						'type'              => $coupon_type
					);
				}
			}

			$lines = array();

			if ( $items = $order->get_items( 'line_item' ) ) {
				foreach ( $items as $item_id => $item ) {
					/**
					 * @var $item \WC_Order_Item_Product
					 */
					$product = $item->get_product();

					if ( ! $product ) {
						continue;
					}

					$product_processed = $this->_register->is_item_processed( ( $product instanceof WC_Product_Variation ) ? $product->get_parent_id() : $product->get_id(), 'product' );

					if ( ! $product_processed ) {
						$this->maybe_process_product( $product );
					}

					$lines[] = array(
						'id'                 => $this->get_object_uniqid( 'order_item', $item_id, $item ),
						'product_id'         => $this->get_object_uniqid( 'product', $item->get_product_id(), $product ),
						'product_title'      => $product->get_title() . ( $product instanceof WC_Product_Variation ? ' - ' . wc_get_formatted_variation( $product, true ) : '' ),
						'product_variant_id' => $this->get_object_uniqid( 'variant', $product->get_id(), $product ),
						'image_url'          => $product->get_image_id() ? wp_get_attachment_url( $product->get_image_id() ) : false,
						'quantity'           => $item->get_quantity(),
						'price'              => $item->get_total(),
						'discount'           => $item->get_subtotal() - $item->get_total()
					);
				}
			}

			$order_id       = $this->get_object_uniqid( 'order', $order->get_id(), $order );
			$completed_date = $order->get_date_completed();

			$args = array_merge(
				array(
					'id'                   => $order_id,
					'customer'             => array_merge(
						array(
							'id'            => $this->get_object_uniqid( 'customer', $order->get_billing_email(), $order->get_user() ),
							'email_address' => $order->get_billing_email(),
							'opt_in_status' => ( yith_wcmc_doing_batch() && apply_filters( 'yith_wcmc_opt_in_status_during_sync', true ) ) || ! $show_checkbox || $submitted_value == 'yes',
							'company'       => $order->get_billing_company(),
							'first_name'    => $order->get_billing_first_name(),
							'last_name'     => $order->get_billing_last_name(),
							'address'       => array(
								'address1'     => $order->get_billing_address_1(),
								'address2'     => $order->get_billing_address_2(),
								'city'         => $order->get_billing_city(),
								'province'     => $order->get_billing_state(),
								'postal_code'  => $order->get_billing_postcode(),
								'country_code' => $order->get_billing_country()
							)
						),
						isset( $customer_stats ) ? $customer_stats : array()
					),
					'financial_status'     => $financial_status,
					'fulfillment_status'   => $fulfillment_status,
					'currency_code'        => $order->get_currency(),
					'order_total'          => $order_total,
					'order_url'            => $order->get_edit_order_url(),
					'discount_total'       => $order->get_discount_total() ? $order->get_discount_total() : 0,
					'tax_total'            => array_sum( wp_list_pluck( $order->get_tax_totals(), 'amount' ) ),
					'processed_at_foreign' => $completed_date ? $completed_date->date( 'Y-m-d H:m:i' ) : date( 'Y-m-d H:m:i' ),
					'shipping_total'       => $order->get_shipping_total() ? $order->get_shipping_total() : 0,
					'shipping_address'     => array(
						'name'         => sprintf( '%s %s', $order->get_shipping_first_name(), $order->get_shipping_last_name() ),
						'address1'     => $order->get_shipping_address_1(),
						'address2'     => $order->get_shipping_address_2(),
						'city'         => $order->get_shipping_city(),
						'province'     => $order->get_shipping_state(),
						'postal_code'  => $order->get_shipping_postcode(),
						'country_code' => $order->get_shipping_country(),
					),
					'billing_address'      => array(
						'name'         => sprintf( '%s %s', $order->get_billing_first_name(), $order->get_billing_last_name() ),
						'address1'     => $order->get_billing_address_1(),
						'address2'     => $order->get_billing_address_2(),
						'city'         => $order->get_billing_city(),
						'province'     => $order->get_billing_state(),
						'postal_code'  => $order->get_billing_postcode(),
						'country_code' => $order->get_billing_country(),
					),
				),

				! empty( $promos ) ? array(
					'promos' => $promos
				) : array(),

				! empty( $lines ) ? array(
					'lines' => $lines
				) : array(),

				( $campaign_processed != 'yes' && isset( $campaign_data['cid'] ) ) ? array(
					'campaign_id' => isset( $campaign_data['cid'] ) ? $campaign_data['cid'] : false,
				) : array(),

				( $campaign_processed != 'yes' && isset( $campaign_data['tc'] ) ) ? array(
					'tracking_code' => isset( $campaign_data['tc'] ) ? $campaign_data['tc'] : '',
				) : array()
			);

			$method  = $order_processed ? 'patch' : 'post';
			$request = "ecommerce/stores/{$store_id}/orders" . ( $order_processed ? "/{$order_id}" : '' );

			if ( ! $args = apply_filters( 'yith_wcmc_process_order_args', $args, $order ) ) {
				YITH_WCMC_Premium()->log( sprintf( _x( 'Order %s skipped', 'log message', 'yith-woocommerce-mailchip' ), $order->get_id() ) );

				return false;
			}

			$res = YITH_WCMC_Premium()->do_request( $method, $request, $args );

			if ( isset( $res['id'] ) || yith_wcmc_doing_batch() ) {
				if ( $customer_id ) {
					$this->maybe_delete_cart( $customer_id );
				}

				$this->_register->maybe_add_item( $order->get_id(), 'order' );

				$order->update_meta_data( '_yith_wcmc_ecommerce_360_processed', 'yes' );
				$order->save();

				YITH_WCMC_Premium()->log( sprintf( _x( 'Order %s processed', 'log message', 'yith-woocommerce-mailchip' ), $order->get_id() ) );
			}

			return $res;
		}

		/**
		 * Delete order if store is connected
		 *
		 * @param $order \WC_Order
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_order( $order ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return;
			}

			$order = $order instanceof WC_Order ? $order : wc_get_order( $order );

			if ( ! $order ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to delete order %s', 'log message', 'yith-woocommerce-mailchip' ), $order->get_id() ) );

			$order_processed = $this->_register->is_item_processed( $order->get_id(), 'order' );

			if ( ! $order_processed ) {
				return false;
			}

			$order_id = $this->get_object_uniqid( 'order', $order->get_id(), $order );

			$res = YITH_WCMC_Premium()->do_request( 'delete', "ecommerce/stores/{$store_id}/orders/{$order_id}" );

			if ( $res ) {
				$this->_register->remove_item( $order->get_id(), 'order' );

				$order->update_meta_data( '_yith_wcmc_ecommerce_360_processed', 'no' );
				$order->save();

				YITH_WCMC_Premium()->log( sprintf( _x( 'Order %s deleted', 'log message', 'yith-woocommerce-mailchip' ), $order->get_id() ) );
			}

			return $res;
		}

		/**
		 * Register product if store is connected
		 *
		 * @param $product \WC_Product
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_product( $product ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$product = $product instanceof WC_Product ? $product : wc_get_product( $product );

			if ( ! $product ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to process product %s', 'log message', 'yith-woocommerce-mailchip' ), $product->get_id() ) );

			$product_processed = $this->_register->is_item_processed( ( $product instanceof WC_Product_Variation ) ? $product->get_parent_id() : $product->get_id(), 'product' );

			$main_product_category = '';

			if ( $categories = $product->get_category_ids() ) {
				$main_product_category_id   = array_shift( $categories );
				$main_product_category_term = get_term_by( 'id', $main_product_category_id, 'product_cat' );

				if ( $main_product_category_term && ! is_wp_error( $main_product_category_term ) ) {
					$main_product_category = $main_product_category_term->name;
				}
			}

			$variants = array();

			if ( $product instanceof WC_Product_Variation ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			if ( $product instanceof WC_Product_Variable ) {
				$variations = $product->get_children();

				if ( ! empty( $variations ) ) {
					foreach ( $variations as $variation_id ) {
						/**
						 * @var $variation \WC_Product_Variation
						 */
						$variation = wc_get_product( $variation_id );
						$price     = $variation->get_regular_price();

						$variants[] = array(
							'id'                 => $this->get_object_uniqid( 'variant', $variation->get_id(), $variation ),
							'title'              => $variation->get_title() . ' - ' . wc_get_formatted_variation( $variation, true ),
							'url'                => $variation->get_permalink(),
							'sku'                => $variation->get_sku(),
							'price'              => $price ? $price : 0,
							'inventory_quantity' => $variation->managing_stock() ? $variation->get_stock_quantity() : 99999999,
							// use a random high number when not managing stock
							'image_url'          => $variation->get_image_id() ? wp_get_attachment_url( $variation->get_image_id() ) : '',
							'backorders'         => $variation->backorders_allowed() ? 'true' : 'false',
							'visibility'         => $variation->is_visible() ? 'visible' : ''
						);
					}
				}
			} else {
				$price      = $product->get_regular_price();
				$variants[] = array(
					'id'                 => $this->get_object_uniqid( 'variant', $product->get_id(), $product ),
					'title'              => $product->get_title(),
					'url'                => $product->get_permalink(),
					'sku'                => $product->get_sku(),
					'price'              => $price ? $price : 0,
					'inventory_quantity' => $product->managing_stock() ? $product->get_stock_quantity() : 99999999,
					// use a random high number when not managing stock
					'image_url'          => $product->get_image_id() ? wp_get_attachment_url( $product->get_image_id() ) : '',
					'backorders'         => $product->backorders_allowed() ? 'true' : 'false',
					'visibility'         => $product->is_visible() ? 'visible' : ''
				);
			}

			$product_id   = $this->get_object_uniqid( 'product', $product->get_id(), $product );
			$date_created = $product->get_date_created();

			$args = array(
				'id'                   => $product_id,
				'title'                => $product->get_title(),
				'handle'               => $product->get_name(),
				'url'                  => $product->get_permalink(),
				'description'          => $product->get_description(),
				'vendor'               => $main_product_category,
				'image_url'            => $product->get_image_id() ? wp_get_attachment_url( $product->get_image_id() ) : '',
				'variants'             => $variants,
				'published_at_foreign' => $date_created ? $date_created->date( 'Y-m-d H:m:i' ) : date( 'Y-m-d H:m:i' )
			);

			$method  = $product_processed ? 'patch' : 'post';
			$request = "ecommerce/stores/{$store_id}/products" . ( $product_processed ? "/{$product_id}" : '' );

			if ( ! $args = apply_filters( 'yith_wcmc_process_product_args', $args, $product ) ) {
				YITH_WCMC_Premium()->log( sprintf( _x( 'Product %s skipped', 'log message', 'yith-woocommerce-mailchip' ), $product->get_id() ) );

				return false;
			}

			$res = YITH_WCMC_Premium()->do_request( $method, $request, $args );

			if ( isset( $res['id'] ) || yith_wcmc_doing_batch() ) {
				$this->_register->maybe_add_item( $product->get_id(), 'product' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Product %s processed', 'log message', 'yith-woocommerce-mailchip' ), $product->get_id() ) );
			}

			return $res;
		}

		/**
		 * Delete product if store is connected
		 *
		 * @param $product \WC_Product
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_product( $product ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$product = $product instanceof WC_Product ? $product : wc_get_product( $product );

			if ( ! $product ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to delete product %s', 'log message', 'yith-woocommerce-mailchip' ), $product->get_id() ) );

			$product_processed = $this->_register->is_item_processed( ( $product instanceof WC_Product_Variation ) ? $product->get_parent_id() : $product->get_id(), 'product' );

			if ( ! $product_processed ) {
				return false;
			}

			if ( $product instanceof WC_Product_Variation ) {
				$product = wc_get_product( $product->get_parent_id() );
			}

			$product_id = $this->get_object_uniqid( 'order', $product->get_id(), $product );

			$res = YITH_WCMC_Premium()->do_request( 'delete', "ecommerce/stores/{$store_id}/products/{$product_id}" );

			if ( $res ) {
				$this->_register->remove_item( $product->get_id(), 'product' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Product %s deleted', 'log message', 'yith-woocommerce-mailchip' ), $product->get_id() ) );
			}

			return $res;
		}

		/**
		 * Register product variant if store is connected
		 *
		 * @param $variation \WC_Product_Variation
		 * @param $product   \WC_Product
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_product_variant( $variation, $product ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$product = $product instanceof WC_Product ? $product : wc_get_product( $product );

			if ( ! $product ) {
				return false;
			}

			$variation = $variation instanceof WC_Product ? $variation : wc_get_product( $variation );

			if ( ! $variation ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to process variant %s (%s)', 'log message', 'yith-woocommerce-mailchip' ), $variation->get_id(), $product->get_id() ) );

			$product_processed   = $this->_register->is_item_processed( $product->get_id(), 'product' );
			$variation_processed = $this->_register->is_item_processed( $variation->get_id(), 'variant' );
			$variation_processed = $product_processed && $variation_processed;

			$product_id = $this->get_object_uniqid( 'product', $product->get_id(), $product );
			$variant_id = $this->get_object_uniqid( 'variant', $variation->get_id(), $variation );

			$args = array(
				'id'                 => $variant_id,
				'title'              => $variation->get_title() . ' - ' . wc_get_formatted_variation( $variation, true ),
				'url'                => $variation->get_permalink(),
				'sku'                => $variation->get_sku(),
				'price'              => $variation->get_regular_price(),
				'inventory_quantity' => $variation->managing_stock() ? $variation->get_stock_quantity() : 99999999,
				// use a random high number when not managing stock
				'image_url'          => $variation->get_image_id() ? wp_get_attachment_url( $variation->get_image_id() ) : '',
				'backorders'         => $variation->backorders_allowed() ? 'true' : 'false',
				'visibility'         => $variation->is_visible() ? 'visible' : ''
			);

			$method  = $variation_processed ? 'patch' : 'post';
			$request = "/ecommerce/stores/{$store_id}/products/{$product_id}/variants" . ( $variation_processed ? "/{$variant_id}" : '' );

			if ( ! $args = apply_filters( 'yith_wcmc_process_product_variant_args', $args, $variation ) ) {
				YITH_WCMC_Premium()->log( sprintf( _x( 'Variant %s (%s) skipped', 'log message', 'yith-woocommerce-mailchip' ), $variation->get_id(), $product->get_id() ) );

				return false;
			}

			$res = YITH_WCMC_Premium()->do_request( $method, $request, $args );

			if ( $res['id'] ) {
				$this->_register->maybe_add_item( $variation->get_id(), 'variant' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Variant %s (%s) processed', 'log message', 'yith-woocommerce-mailchip' ), $variation->get_id(), $product->get_id() ) );
			}

			return $res;
		}

		/**
		 * Delete product variant if store is connected
		 *
		 * @param $variation \WC_Product_Variation
		 * @param $product   \WC_Product
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_product_variant( $variation, $product ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$product = $product instanceof WC_Product ? $product : wc_get_product( $product );

			if ( ! $product ) {
				return false;
			}

			$variation = $variation instanceof WC_Product ? $variation : wc_get_product( $variation );

			if ( ! $variation ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to delete variant %s (%s)', 'log message', 'yith-woocommerce-mailchip' ), $variation->get_id(), $product->get_id() ) );

			$product_processed   = $this->_register->is_item_processed( $product->get_id(), 'product' );
			$variation_processed = $this->_register->is_item_processed( $variation->get_id(), 'variant' );
			$variation_processed = $product_processed && $variation_processed;

			if ( ! $variation_processed ) {
				return false;
			}

			$product_id   = $this->get_object_uniqid( 'product', $product->get_id(), $product );
			$variation_id = $this->get_object_uniqid( 'variant', $variation->get_id(), $variation );

			$res = YITH_WCMC_Premium()->do_request( 'delete', "ecommerce/stores/{$store_id}/products/{$product_id}/variants/{$variation_id}" );

			if ( $res ) {
				$this->_register->remove_item( $variation->get_id(), 'variant' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Variant %s (%s) deleted', 'log message', 'yith-woocommerce-mailchip' ), $variation->get_id(), $product->get_id() ) );
			}

			return $res;
		}

		/**
		 * Register coupon if store is connected
		 *
		 * @param $coupon \WC_Coupon
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_promo_rule( $coupon ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$coupon = $coupon instanceof WC_Coupon ? $coupon : new WC_Coupon( $coupon );

			if ( ! $coupon ) {
				return;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to process promo code %s', 'log message', 'yith-woocommerce-mailchip' ), $coupon->get_code() ) );

			$coupon_processed = $this->_register->is_item_processed( $coupon->get_id(), 'promo_code' );

			$promo_id = $this->get_object_uniqid( 'promo_rule', $coupon->get_id(), $coupon );
			$amount   = $coupon->get_amount();
			$type     = $coupon->get_discount_type();

			if ( 'percent' == $type ) {
				$type   = 'percentage';
				$amount = $amount / 100;
				$target = 'total';
			} elseif ( 'fixed_product' ) {
				$type   = 'fixed';
				$target = 'per_item';
			} else {
				$type   = 'fixed';
				$target = 'total';
			}

			$expiration_time = $coupon->get_date_expires();
			$description     = $coupon->get_description();
			$enabled         = $expiration_time ? $expiration_time->getTimestamp() > time() : true;

			$args = array_merge(
				array(
					'id'          => $promo_id,
					'title'       => $coupon->get_code(),
					'description' => $description ? $description : $coupon->get_code(),
					'enabled'     => $enabled,
					'type'        => $type,
					'amount'      => $amount,
					'target'      => $target,
				),
				$expiration_time ? array(
					'ends_at' => $expiration_time->date( 'Y-m-d' )
				) : array()
			);

			$method  = $coupon_processed ? 'patch' : 'post';
			$request = "ecommerce/stores/{$store_id}/promo-rules" . ( $coupon_processed ? "/{$promo_id}" : '' );

			$res = YITH_WCMC_Premium()->do_request( $method, $request, $args );

			if ( ! isset( $res['id'] ) ) {
				return $res;
			}

			$promo_code_id = $this->get_object_uniqid( 'promo_code', $coupon->get_code(), $coupon );

			$args = array(
				'id'             => $promo_code_id,
				'code'           => $coupon->get_code(),
				'redemption_url' => get_home_url(),
				'enabled'        => $enabled,
				'usage_count'    => $coupon->get_usage_count(),
			);

			if ( ! $args = apply_filters( 'yith_wcmc_process_promo_rule_args', $args, $coupon ) ) {
				YITH_WCMC_Premium()->log( sprintf( _x( 'Promo code %s skipped', 'log message', 'yith-woocommerce-mailchip' ), $coupon->get_code() ) );

				return false;
			}

			$request = "ecommerce/stores/{$store_id}/promo-rules/{$promo_id}/promo-codes" . ( $coupon_processed ? "/{$promo_code_id}" : '' );

			$res = YITH_WCMC_Premium()->do_request( $method, $request, $args );

			if ( isset( $res['id'] ) || yith_wcmc_doing_batch() ) {
				$this->_register->maybe_add_item( $coupon->get_id(), 'promo_code' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Promo code %s processed', 'log message', 'yith-woocommerce-mailchip' ), $coupon->get_code() ) );
			}

			return $res;
		}

		/**
		 * Delete coupon if store is connected
		 *
		 * @param $coupon \WC_Coupon
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_promo_rule( $coupon ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return;
			}

			$coupon = $coupon instanceof WC_Coupon ? $coupon : new WC_Coupon( $coupon );

			if ( ! $coupon ) {
				return;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to delete promo code %s', 'log message', 'yith-woocommerce-mailchip' ), $coupon->get_code() ) );

			$coupon_processed = $this->_register->is_item_processed( $coupon->get_id(), 'promo_code' );

			if ( ! $coupon_processed ) {
				return false;
			}

			$promo_id = $this->get_object_uniqid( 'promo_rule', $coupon->get_id(), $coupon );

			$res = YITH_WCMC_Premium()->do_request( 'delete', "ecommerce/stores/{$store_id}/promo-rules/{$promo_id}" );

			if ( $res ) {
				$this->_register->remove_item( $coupon->get_id(), 'promo_code' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Promo code %s deleted', 'log message', 'yith-woocommerce-mailchip' ), $coupon->get_code() ) );
			}

			return $res;
		}

		/**
		 * Register cart if store is connected
		 *
		 * @param $user_id int
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_cart( $user_id ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			if ( ! apply_filters( 'woocommerce_persistent_cart_enabled', true ) ) {
				return false;
			}

			$saved_cart_meta = get_user_meta( $user_id, '_woocommerce_persistent_cart_' . get_current_blog_id(), true );

			if ( isset( $saved_cart_meta['cart'] ) ) {
				$saved_cart = array_filter( (array) $saved_cart_meta['cart'] );
			}

			if ( ! isset( $saved_cart ) || ! $saved_cart ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to process cart for customer %s', 'log message', 'yith-woocommerce-mailchip' ), $user_id ) );

			$customer = get_user_by( 'id', $user_id );

			if ( ! $customer ) {
				return false;
			}

			$cart_processed = $this->_register->is_item_processed( $user_id, 'cart' );

			if ( $cart_processed ) {
				$this->maybe_delete_cart( $user_id );
			}

			// this could be a very resource-demanding process, and it is not required when syncing the entire shop, as
			// MailChimp will create its stats on the go
			// for this reason we include  yith_wcmc_process_customer_history filter to let third party dev skip this
			if ( apply_filters( 'yith_wcmc_process_customer_history', true ) ) {
				$customer_stats = $this->get_customer_stats( $customer->billing_email );
			}

			$lines = array();

			if ( $items = $saved_cart ) {
				foreach ( $items as $item_id => $item ) {
					if ( ! isset( $item['product_id'] ) || ! isset( $item['variation_id'] ) || ! isset( $item['quantity'] ) || ! isset( $item['line_total'] ) || ! isset( $item['line_subtotal'] ) ) {
						continue;
					}

					/**
					 * @var $product \WC_Product
					 */
					$product_id        = $item['product_id'];
					$variation_id      = $item['variation_id'];
					$product           = wc_get_product( $variation_id ? $variation_id : $product_id );
					$product_processed = $this->_register->is_item_processed( ( $product instanceof WC_Product_Variation ) ? $product->get_parent_id() : $product->get_id(), 'product' );

					if ( ! $product_processed ) {
						$this->maybe_process_product( $product );
					}

					$lines[] = array(
						'id'                 => $this->get_object_uniqid( 'cart_item', $item_id, $item ),
						'product_id'         => $this->get_object_uniqid( 'product', $product_id, $product ),
						'product_title'      => $product->get_title(),
						'product_variant_id' => $this->get_object_uniqid( 'variant', $variation_id ? $variation_id : $product_id, $product ),
						'image_url'          => $product->get_image_id() ? wp_get_attachment_url( $product->get_image_id() ) : '',
						'quantity'           => $item['quantity'],
						'price'              => $item['line_total'],
						'discount'           => $item['line_subtotal'] - $item['line_total']
					);
				}
			}

			$cart_id = $this->get_object_uniqid( 'cart', $user_id, $saved_cart_meta );

			$args = array_merge(
				array(
					'id'            => $cart_id,
					'customer'      => array_merge(
						array(
							'id'            => $this->get_object_uniqid( 'customer', $customer->billing_email, $customer ),
							'email_address' => $customer->billing_email ? $customer->billing_email : $customer->user_email,
							'opt_in_status' => apply_filters( 'yith_wcmc_opt_in_status_abandoned_cart', true ),
							'company'       => $customer->billing_company,
							'first_name'    => $customer->billing_first_name,
							'last_name'     => $customer->billing_last_name,
							'address'       => array(
								'address1'     => $customer->billing_address_1,
								'address2'     => $customer->billing_address_2,
								'city'         => $customer->billing_city,
								'province'     => $customer->billing_state,
								'postal_code'  => $customer->billing_postcode,
								'country_code' => $customer->billing_country
							)
						),
						isset( $customer_stats ) ? $customer_stats : array()
					),
					'checkout_url'  => wc_get_checkout_url(),
					'currency_code' => get_woocommerce_currency(),
					'order_total'   => $saved_cart ? array_sum( wp_list_pluck( $saved_cart, 'line_total' ) ) : 0,
					'tax_total'     => $saved_cart ? array_sum( wp_list_pluck( $saved_cart, 'line_tax' ) ) : 0
				),
				$lines ? array(
					'lines' => $lines
				) : array()
			);

			if ( ! $args = apply_filters( 'yith_wcmc_process_cart_args', $args, $saved_cart ) ) {
				YITH_WCMC_Premium()->log( sprintf( _x( 'Cart for customer %s skipped', 'log message', 'yith-woocommerce-mailchip' ), $user_id ) );

				return false;
			}

			$res = YITH_WCMC_Premium()->do_request( 'post', "ecommerce/stores/{$store_id}/carts", $args );

			if ( isset( $res['id'] ) || yith_wcmc_doing_batch() ) {
				$this->_register->maybe_add_item( $user_id, 'cart' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Cart for customer %s processed', 'log message', 'yith-woocommerce-mailchip' ), $user_id ) );
			}

			return $res;
		}

		/**
		 * Delete cart if store is connected
		 *
		 * @param $user_id int
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_cart( $user_id ) {
			$store_id = $this->get_store_uniqid();

			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$cart_processed = $this->_register->is_item_processed( $user_id, 'cart' );

			if ( ! $cart_processed ) {
				return false;
			}

			YITH_WCMC_Premium()->log( sprintf( _x( 'Attempting to delete cart for customer %s', 'log message', 'yith-woocommerce-mailchip' ), $user_id ) );

			$cart_id = $this->get_object_uniqid( 'cart', $user_id, get_user_by( 'id', $user_id ) );

			$res = YITH_WCMC_Premium()->do_request( 'delete', "ecommerce/stores/{$store_id}/carts/{$cart_id}" );

			if ( $res ) {
				$this->_register->remove_item( $user_id, 'cart' );

				YITH_WCMC_Premium()->log( sprintf( _x( 'Cart for customer %s deleted', 'log message', 'yith-woocommerce-mailchip' ), $user_id ) );
			}

			return $res;
		}
	}
}

/**
 * Unique access to instance of YITH_WCMC_Store class
 *
 * @return \YITH_WCMC_Store
 * @since 1.0.0
 */
function YITH_WCMC_Store() {
	return YITH_WCMC_Store::get_instance();
}

