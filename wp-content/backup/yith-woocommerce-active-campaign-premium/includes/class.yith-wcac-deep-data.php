<?php
/**
 * DeepData integration class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC_Deep_Data' ) ) {
	/**
	 * Active Campaign DeepData integration class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Deep_Data {
		/**
		 * Background processed
		 *
		 * @var \YITH_WCAC_Background_Process
		 * @since 1.0.0
		 */
		protected $_background_process;

		/**
		 * Processed items register
		 *
		 * @var \YITH_WCAC_Deep_Data_Register
		 * @since 1.0.0
		 */
		protected $_register;

		/**
		 * Carts waiting queue
		 *
		 * @var \YITH_WCAC_Carts_Waiting_Queue
		 * @since 1.0.0
		 */
		protected $_carts_waiting_queue;

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAC_Deep_Data
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAC_Deep_Data
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// setup background process.
			$this->_background_process  = new YITH_WCAC_Background_Process();
			$this->_register            = new YITH_WCAC_Deep_Data_Register();
			$this->_carts_waiting_queue = new YITH_WCAC_Carts_Waiting_Queue();

			// register store.
			add_action( 'yit_panel_wc_after_update', array( $this, 'create_connection' ) );

			// register products /variations / coupons.
			add_action( 'save_post', array( $this, 'process_item' ), 20, 1 );
			add_action( 'untrashed_post', array( $this, 'process_item' ), 20, 1 );

			// clean orders
			add_action( 'trashed_post', array( $this, 'clean_item' ), 20, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'clean_item' ), 20, 3 );

			// register carts for Abandoned Cart automations.
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_add_to_cart', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_applied_coupon', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_removed_coupon', array( $this, 'process_cart' ) );
			add_action( 'woocommerce_calculated_shipping', array( $this, 'process_cart' ) );
			add_action( 'yith_wcac_enqueue_abandoned_carts', array( $this, 'schedule_carts' ) );

			// set correct currency for BP session.
			add_action( 'yith_wcac_before_cart_processing', array( $this, 'filter_currency_for_cart' ), 10, 3 );

			// register carts for Abandoned Cart automations (guest users).
			add_action( 'wp_ajax_yith_wcac_register_session_billing_email', array(
				$this,
				'register_session_billing_email'
			) );
			add_action( 'wp_ajax_nopriv_yith_wcac_register_session_billing_email', array(
				$this,
				'register_session_billing_email'
			) );

			// sync cron handling.
			add_action( 'admin_notices', array( $this, 'print_admin_notices' ) );
			add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
			add_action( 'yith_wcac_sync_cron', array( $this, 'sync_step' ) );
			add_action( 'admin_action_yith_wcac_sync_start', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcac_sync_restart', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcac_sync_resume', array( $this, 'sync_action' ) );
			add_action( 'admin_action_yith_wcac_sync_stop', array( $this, 'sync_action' ) );
		}

		/**
		 * Add schedule specific of YITH WCAC DeepData Integration crons
		 *
		 * @param array $schedules Array of existing schedules.
		 *
		 * @return array Array of filtered schedules
		 */
		public function add_cron_schedule( $schedules ) {
			$interval = apply_filters( 'yith_wcac_cron_schedule', 2 );

			$schedules['yith_wcac_cron_schedule'] = array(
				'interval' => MINUTE_IN_SECONDS * $interval,

				// translators: 1. Number of minutes.
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
			return apply_filters( 'yith_wcac_store_uniqid', md5( site_url() . ( function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : 1 ) ) );
		}

		/**
		 * Generate unique id for each object in the store
		 *
		 * @param string $object_type Post type.
		 * @param int    $id          Post id.
		 * @param mixed  $object      Object being processed.
		 *
		 * @return string Unique object id
		 * @filter yith_wcac_store_{$object_type}_uniqid
		 */
		public function get_object_uniqid( $object_type, $id, $object = null ) {
			$unique = 'cart' === $object_type ? md5( $id . rand() ) : md5( $id );

			return apply_filters( "yith_wcac_store_{$object_type}_uniqid", $unique, $object );
		}

		/**
		 * Check whether current store is connected
		 *
		 * @param bool $force_update Whether to force update of API results, to retrieve fresh data.
		 *
		 * @return object|bool Connection object, or false if store isn't connected
		 */
		public function is_store_connected( $force_update = false ) {
			$store_id = $this->get_store_uniqid();

			$results = YITH_WCAC()->do_request(
				'connections',
				'GET',
				[
					'filters' => [
						'externalid' => $store_id,
						'service'    => 'YITH Active Campaign for WooCommerce',
					]
				],
				[],
				$force_update
			);

			if ( ! isset( $results->connections ) || empty( $results->connections ) ) {
				return false;
			}

			$connection = array_shift( $results->connections );

			return $connection;
		}

		/* === STORE CONNECTION PROCESS === */

		/**
		 * Register store on Active Campaign
		 *
		 * @return void
		 * @use \YITH_WCAC_Deep_Data::maybe_connect_store
		 */
		public function create_connection() {
			if ( ! ( isset( $_GET['page'] ) && 'yith_wcac_panel' === $_GET['page'] && isset( $_GET['tab'] ) && 'deep-data' === $_GET['tab'] ) ) {
				return;
			}

			$store_name = get_option( 'yith_wcac_store_connection_name' );
			$store_logo = get_option( 'yith_wcac_store_connection_logo' );

			if ( ! $store_name || ! $store_logo ) {
				return;
			}

			$is_store_connected = $this->is_store_connected( false );

			if ( $is_store_connected ) {
				return;
			}

			$args = [
				'name'    => $store_name,
				'logoUrl' => esc_url( $store_logo ),
			];

			$id = $this->maybe_connect_store( $args );

			if ( $id ) {
				wp_redirect(
					add_query_arg(
						array(
							'page' => 'yith_wcac_panel',
							'tab'  => 'deep-data',
						),
						admin_url( 'admin.php' )
					)
				);
			}
		}

		/**
		 * Execute API call to connect store to a ActiveCampaign, if store is not connected already
		 *
		 * @param array $args Array of arguments to use for API call
		 *
		 * @return mixed Result of operation
		 */
		public function maybe_connect_store( $args = array() ) {
			$is_store_connected = $this->is_store_connected( true );

			if ( $is_store_connected ) {
				return $this->get_store_uniqid();
			}

			return $this->_create_store( $args );
		}

		/**
		 * Execute API call to disconnect store from Active Campaign, if store is connected
		 *
		 * @return mixed Result of operation
		 */
		public function maybe_disconnect_store() {
			$is_store_connected = $this->is_store_connected( true );

			if ( ! $is_store_connected ) {
				return true;
			}

			return $this->_delete_store();
		}

		/**
		 * Creates a store on Active Campaign account for the list_id
		 *
		 * @param array $args Array of parameters to use on API call.
		 *
		 * @return string Store id
		 *
		 * @since 1.1.0
		 */
		protected function _create_store( $args = array() ) {
			YITH_WCAC()->log( _x( 'Attempting to connect store', 'log message', 'yith-woocommerce-active-campaign' ) );

			$defaults = array(
				'service'    => 'YITH Active Campaign for WooCommerce',
				'externalid' => $this->get_store_uniqid(),
				'name'       => get_bloginfo( 'name' ),
				'logoUrl'    => function_exists( 'yith_plugin_fw_get_default_logo' ) ? yith_plugin_fw_get_default_logo() : '',
				'linkUrl'    => esc_url(
					add_query_arg(
						[
							'page' => 'yith_wcac_panel',
							'tab'  => 'deep-data',
						],
						admin_url( 'admin.php' )
					)
				),
			);

			$args       = wp_parse_args( $args, $defaults );
			$api_params = [
				'connection' => $args,
			];

			$results = YITH_WCAC()->do_request( 'connections', 'POST', [], $api_params );

			if ( ! isset( $results->connection ) || empty( $results->connection ) ) {
				return false;
			}

			YITH_WCAC()->clear_cached_data( 'connections' );
			YITH_WCAC()->log( _x( 'Store connected!', 'log message', 'yith-woocommerce-active-campaign' ) );

			return $results->connection->id;
		}

		/**
		 * Delete a store from Active Campaign
		 *
		 * @return bool Status of the operation
		 */
		protected function _delete_store() {
			$connection = $this->is_store_connected();

			if ( ! $connection ) {
				return true;
			}

			$connection_id = $connection->id;

			YITH_WCAC()->log( _x( 'Attempting to disconnect store', 'log message', 'yith-woocommerce-active-campaign' ) );

			YITH_WCAC()->do_request( "connections/{$connection_id}", 'DELETE' );

			if ( ! $this->is_store_connected( true ) ) {
				delete_option( 'yith_wcac_store_connection_name' );
				delete_option( 'yith_wcac_store_connection_logo' );

				$this->_register->truncate();

				YITH_WCAC()->log( _x( 'Store disconnected!', 'log message', 'yith-woocommerce-active-campaign' ) );

				return true;
			}

			return false;
		}

		/* === SYNC STORE === */

		/**
		 * Print admin notices in Store option tab of the plugin
		 *
		 * @return void
		 */
		public function print_admin_notices() {
			$current_screen = get_current_screen();

			if ( 'yith-plugins_page_yith_wcac_panel' != $current_screen->id || ! isset( $_GET['tab'] ) || 'deep-data' != $_GET['tab'] ) {
				return;
			}

			if ( $this->is_store_syncing() ) {
				$status_message = $this->get_sync_status_message();

				?>
				<div class="notice notice-success">
					<p>
						<?php esc_html_e( 'Your store is currently being synced! This may take a while, but won\'t affect performance of your store. You can keep track of the status of the operation in this page', 'yith-woocommerce-active-campaign' ); ?>
					</p>
					<p>
						<?php echo wp_kses_post( $status_message ); ?>
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
			$sync_info = $this->get_sync_details();
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
						// translators: 1. Post type name (Orders). 2. Processed items. 3. Total items. 4. Percentage of processed.
						__( '<b>%1$s</b> %2$s/%3$s (%4$s%%)', 'yith-woocommerce-active-campaign' ),
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
				return __( 'Synchronization complete', 'yith-woocommerce-active-campaign' );
			}

			$status_message = __( 'Current synchronization status:', 'yith-woocommerce-active-campaign' );
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
				$action = str_replace( 'yith_wcac_', '', $action );

				YITH_WCAC()->log( sprintf( _x( 'Attempting sync action: %s', 'log message', 'yith-woocommerce-active-campaign' ), $action ) );

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
						update_option( 'yith_wcac_is_store_synced', false );
						update_option( 'yith_wcac_processed_items', array() );

						$this->sync();
						break;
				}

				YITH_WCAC()->log( sprintf( _x( 'Sync action performed: %s', 'log message', 'yith-woocommerce-active-campaign' ), $action ) );
			}

			wp_redirect( add_query_arg( array(
				'page' => 'yith_wcac_panel',
				'tab'  => 'deep-data'
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

			if ( ! wp_next_scheduled( 'yith_wcac_sync_cron' ) ) {
				wp_schedule_event( time(), 'yith_wcac_cron_schedule', 'yith_wcac_sync_cron' );
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
			wp_clear_scheduled_hook( 'yith_wcac_sync_cron' );

			$this->_sync_paused();
		}

		/**
		 * Process next step of the synchronization process
		 *
		 * @return void
		 */
		public function sync_step() {
			if ( ! $this->is_store_syncing() ) {
				wp_clear_scheduled_hook( 'yith_wcac_sync_cron' );

				return;
			}

			$items_to_process = $this->_get_next_items_to_sync();

			if ( ! empty( $items_to_process ) ) {
				foreach ( $items_to_process as $item ) {
					$this->_background_process->push_to_queue( $item );
				}

				$this->_background_process->save();

				// translators: 1. Print out of items to process within this step.
				YITH_WCAC()->log( sprintf( _x( "New sync step: %s\n", 'log message', 'yith-woocommerce-active-campaign' ), print_r( $items_to_process, 1 ) ) );
			} else {
				wp_clear_scheduled_hook( 'yith_wcac_sync_cron' );
				$this->_sync_completed();

				YITH_WCAC()->log( _x( 'Sync completed!', 'log message', 'yith-woocommerce-active-campaign' ) );
			}
		}

		/**
		 * Check whether store is currently syncing
		 *
		 * @return bool Whether store is syncing
		 */
		public function is_store_syncing() {
			$is_store_syncing = get_option( 'yith_wcac_is_store_syncing', false );

			return (bool) $is_store_syncing && ! $this->is_store_synced();
		}

		/**
		 * Check whether store is synced
		 *
		 * @return bool Whether store sync is completed
		 */
		public function is_store_synced() {
			$is_store_synced = get_option( 'yith_wcac_is_store_synced', false );

			return (bool) $is_store_synced;
		}

		/**
		 * Check whether synchronization was paused
		 *
		 * @return bool Whether store sync was paused
		 */
		public function is_sync_paused() {
			$is_store_synced = get_option( 'yith_wcac_is_store_sync_paused', false );

			return (bool) $is_store_synced;
		}

		/**
		 * Get information about current status of the sync
		 *
		 * @return array Each item of the array refers to a post_type and contain overall count and processed count
		 */
		public function get_sync_details() {
			global $wpdb;

			$where = call_user_func_array( array( $wpdb, 'prepare' ), apply_filters( 'yith_wcac_get_next_items_to_synch_where', array( '%d=%d', array( 1, 1 ) ) ) );

			$post_type_count = $wpdb->get_results( "SELECT post_type, COUNT(ID) AS count FROM {$wpdb->posts} WHERE post_status IN ( 'publish', 'wc-completed', 'wc-processing' ) AND {$where} GROUP BY post_type", ARRAY_A ); // @codingStandardsIgnoreLine.

			if ( ! empty( $post_type_count ) ) {
				$post_counts = array_combine( wp_list_pluck( $post_type_count, 'post_type' ), wp_list_pluck( $post_type_count, 'count' ) );
			}

			$processed_posts     = get_option( 'yith_wcac_processed_items', array() );
			$supported_post_type = $this->_get_post_types_to_sync();
			$info                = array();

			foreach ( $supported_post_type as $post_type ) {
				$info[ $post_type ] = array(
					'count'     => isset( $post_counts[ $post_type ] ) ? $post_counts[ $post_type ] : 0,
					'processed' => isset( $processed_posts[ $post_type ] ) ? min( $processed_posts[ $post_type ], isset( $post_counts[ $post_type ] ) ? $post_counts[ $post_type ] : 0 ) : 0,
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
			$processed_posts = get_option( 'yith_wcac_processed_items', array() );

			if ( isset( $processed_posts[ $post_type ] ) ) {
				$processed_posts[ $post_type ] += $new_count;
			} else {
				$processed_posts[ $post_type ] = $new_count;
			}

			update_option( 'yith_wcac_processed_items', $processed_posts );
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

				YITH_WCAC()->log( sprintf( _x( "New sync step: %s\n", 'log message', 'yith-woocommerce-active-campaign' ), print_r( $items_to_process, 1 ) ) );
			}
		}

		/**
		 * Register start of the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_started() {
			if ( ! $connection = $this->is_store_connected() ) {
				return;
			}

			$args = array(
				'syncStatus' => 1,
			);

			YITH_WCAC()->do_request( "connections/{$connection->id}", 'PUT', [], $args );

			update_option( 'yith_wcac_is_store_syncing', true );
			update_option( 'yith_wcac_is_store_sync_paused', false );
		}

		/**
		 * Register pause for the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_paused() {
			if ( ! $connection = $this->is_store_connected() ) {
				return;
			}

			$args = array(
				'syncStatus' => 0,
			);

			YITH_WCAC()->do_request( "connections/{$connection->id}", 'PUT', [], $args );

			update_option( 'yith_wcac_is_store_syncing', false );
			update_option( 'yith_wcac_is_store_sync_paused', true );
		}

		/**
		 * Register completion of the synchronization process
		 *
		 * @return void
		 */
		protected function _sync_completed() {
			if ( ! $connection = $this->is_store_connected() ) {
				return;
			}

			$args = array(
				'syncStatus' => 0,
			);

			YITH_WCAC()->do_request( "connections/{$connection->id}", 'PUT', [], $args );

			update_option( 'yith_wcac_is_store_syncing', false );
			update_option( 'yith_wcac_is_store_synced', true );
		}

		/**
		 * Returns post types to sync
		 *
		 * @return array Post types to sync
		 * @since 2.1.5
		 */
		protected function _get_post_types_to_sync() {
			return apply_filters( 'yith_wcac_supported_post_type_to_sync', array( 'shop_order' ) );
		}

		/**
		 * Retrieve next items to be queued for processing
		 *
		 * @return array Item to be queued for background processing
		 */
		protected function _get_next_items_to_sync() {
			$sync_info = $this->get_sync_details();

			if ( $sync_info['shop_order']['processed'] < $sync_info['shop_order']['count'] ) {
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
		 * @param string $post_type Post type.
		 * @param int    $offset    Offset to be used as starting point.
		 *
		 * @return array Posts to be processed
		 */
		protected function _get_next_items( $post_type, $offset ) {
			global $wpdb;

			$batch_count = apply_filters( 'yith_wcac_batch_count', 100 );
			$where       = call_user_func_array( array( $wpdb, 'prepare' ), apply_filters( 'yith_wcac_get_next_items_to_synch_where', array( '%d=%d', array( 1, 1 ) ) ) );

			$res   = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ( 'publish', 'wc-processing', 'wc-completed' ) AND {$where} ORDER BY post_date DESC LIMIT %d, %d", $post_type, $offset, $batch_count ) ); // @codingStandardsIgnoreLine.
			$items = array();

			switch ( $post_type ) {
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
						'id'   => $item,
					);
				}
			}

			return $items;
		}

		/* === REGISTER ITEMS INTO THE STORE === */

		/**
		 * Process single item, and register it in Active Campaign
		 *
		 * @param int $item_id ID of the item to process.
		 *
		 * @return void
		 */
		public function process_item( $item_id ) {
			$post_type   = get_post_type( $item_id );
			$post_status = get_post_status( $item_id );

			if ( ! apply_filters( 'yith_wcac_process_item', true, $item_id, $post_type, $post_status ) ) {
				return;
			}

			switch ( $post_type ) {
				case 'shop_order':
					if ( in_array( $post_status, array( 'wc-completed', 'wc-processing' ) ) ) {
						$this->maybe_process_order( $item_id );
					}
					break;
			}
		}

		/**
		 * Cleant single item, and removes it from Active Campaign
		 *
		 * @param int   $item_id ID of the item to process.
		 * @param mixed $add_1   Additional param, depending on the action; usually old order status.
		 * @param mixed $add_2   Additional param, depending on the action; usually new order status.
		 *
		 * @return void
		 */
		public function clean_item( $item_id, $add_1 = null, $add_2 = null ) {
			if ( doing_action( 'woocommerce_order_status_changed' ) ) {
				$old_status = $add_1;
				$new_status = $add_2;

				if ( in_array( $old_status, array( 'completed', 'processing' ) ) && ! in_array( $new_status, array( 'completed', 'processing' ) ) ) {
					$this->maybe_delete_order( $item_id );
				}
			} elseif ( doing_action( 'trashed_post' ) ) {
				$post_type   = get_post_type( $item_id );
				$post_status = get_post_status( $item_id );

				if ( ! apply_filters( 'yith_wcac_process_item', true, $item_id, $post_type, $post_status ) ) {
					return;
				}

				switch ( $post_type ) {
					case 'shop_order':
						$this->maybe_delete_order( $item_id );
						break;
				}
			}
		}

		/**
		 * Process cart item, and schedule it for registration
		 *
		 * @param mixed $value Used as return value, when method is hooked to a filter.
		 *
		 * @return mixed Returns $value as it is
		 */
		public function process_cart( $value = null ) {
			$abandoned_cart_enabled       = get_option( 'yith_wcac_store_integration_abandoned_cart_enable', 'no' );
			$abandoned_cart_enabled_guest = get_option( 'yith_wcac_store_integration_abandoned_cart_enable_guest', 'no' );

			if ( 'yes' !== $abandoned_cart_enabled ) {
				return $value;
			}

			$customer_email = false;

			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();

				if ( ! $user || is_wp_error( $user ) ) {
					return $value;
				}

				$customer_email = ! empty( $user->billing_email ) ? $user->billing_email : $user->user_email;
			} elseif ( 'yes' == $abandoned_cart_enabled_guest ) {
				$customer_email = WC()->session->get( 'yith_wcac_guest_billing_email' );
			}

			// if we don't have a customer email, return.
			if ( ! $customer_email ) {
				return $value;
			}

			if ( WC()->cart->is_empty() ) {
				// if empty cart, remove it from the queue (no reason to process).
				$this->_pop_cart_from_waiting_queue( $customer_email );
			} else {
				// if cart has items, register it on the queue, or update already existing item.
				$cart = $this->_prepare_cart_for_storage( WC()->cart );

				if ( ! $cart ) {
					return $value;
				}

				$this->_push_cart_to_waiting_queue( $customer_email, $cart, get_woocommerce_currency() );
			}

			// return sent value, just to avoid causing issues when method is hooked to filters.
			return $value;
		}

		/**
		 * Process carts awaiting delay queue, and register items to Background Process handling when ready
		 *
		 * @return void
		 */
		public function schedule_carts() {
			$delay               = get_option( 'yith_wcac_store_integration_abandoned_cart_delay', 1 );
			$timestamp_threshold = time() - $delay * HOUR_IN_SECONDS;

			$items = $this->_carts_waiting_queue->pop_expired_items( $timestamp_threshold );

			if ( $this->_carts_waiting_queue->is_empty() && wp_next_scheduled( 'yith_wcac_enqueue_abandoned_carts' ) ) {
				wp_clear_scheduled_hook( 'yith_wcac_enqueue_abandoned_carts' );
			}

			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {
					$this->_background_process->push_to_queue(
						array(
							'type'      => 'cart',
							'id'        => $item['email'],
							'item'      => $item['cart'],
							'currency'  => $item['currency'],
							'timestamp' => strtotime( $item['ts'] ),
						)
					);
				}

				$this->_background_process->save();
			}
		}

		/**
		 * Register order if store is connected
		 *
		 * @param \WC_Order|int $order Order object or order id.
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_order( $order ) {
			if ( ! $connection = $this->is_store_connected() ) {
				return false;
			}

			$order = $order instanceof WC_Order ? $order : wc_get_order( $order );

			if ( ! $order ) {
				return false;
			}

			// first of all, let's process customer if needed.
			$customer_email = $order->get_billing_email();

			if ( ! $customer = $this->maybe_process_customer( $customer_email, $order ) ) {
				// translators: 1. Order id. 2. Customer email.
				YITH_WCAC()->log( sprintf( _x( 'Order %1$s couldn\'t process because we weren\'t able to update customer %2$s', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id(), $customer_email ) );

				return false;
			}

			// allow third party code to execute actions before processing.
			do_action( 'yith_wcac_before_order_processing', $customer_email, $order );

			// translators: 1. Order id.
			YITH_WCAC()->log( sprintf( _x( 'Attempting to process order %s', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id() ) );

			// check whether order is already registered on AC.
			$order_processed = $this->_register->is_item_processed( $order->get_id(), 'order' );

			// if no cart was registered with this id, search for cart registered for the customer.
			if ( ! $order_processed ) {
				$order_processed = $this->_register->is_item_processed( $customer_email, 'cart' );
			}

			$order_created_date = $order->get_date_created();
			$order_updated_date = $order->get_date_modified();
			$order_currency     = $order->get_currency();
			$order_tax_total    = 0;

			if ( $taxes = $order->get_tax_totals() ) {
				$order_tax_total = array_sum( wp_list_pluck( $taxes, 'amount' ) );
			}

			$args = [
				'ecomOrder' => [
					'externalid'          => $this->get_object_uniqid( 'order', $order->get_id() ),
					'source'              => apply_filters( 'yith_wcac_order_source', 1, $order, $customer_email ),
					'email'               => $customer_email,
					'orderProducts'       => $this->_get_order_products( $order, $order_currency ),
					'totalPrice'          => $this->_get_item_price( $order->get_total(), $order_currency ),
					'shippingAmount'      => $this->_get_item_price( $order->get_shipping_total(), $order_currency ),
					'taxAmount'           => $this->_get_item_price( $order_tax_total, $order_currency ),
					'currency'            => $order_currency,
					'connectionid'        => $connection->id,
					'customerid'          => $customer->id,
					'orderUrl'            => $order->get_view_order_url(),
					'externalCreatedDate' => $order_created_date ? $order_created_date->date( 'Y-m-d H:i:s' ) : '',
					'externalUpdatedDate' => $order_updated_date ? $order_updated_date->date( 'Y-m-d H:i:s' ) : '',
					'shippingMethod'      => $order->get_shipping_method(),
					'orderNumber'         => $order->get_order_number(),
					'orderDiscounts'      => $this->_get_order_discounts( $order, $order_currency ),
				]
			];

			$external_id = isset( $order_processed['ac_id'] ) ? $order_processed['ac_id'] : false;
			$method      = $order_processed ? 'PUT' : 'POST';
			$request     = $order_processed ? "ecomOrders/{$external_id}" : 'ecomOrders';

			if ( ! $args = apply_filters( 'yith_wcac_process_order_args', $args, $order ) ) {
				// translators: 1. Order id.
				YITH_WCAC()->log( sprintf( _x( 'Order %s skipped', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id() ) );

				return false;
			}

			$res = YITH_WCAC()->do_request( $request, $method, [], $args );

			if ( isset( $res->ecomOrder ) || yith_wcac_doing_task() ) {
				if ( isset( $res->ecomOrder ) ) {
					$this->_register->maybe_add_item( $order->get_id(), 'order', $res->ecomOrder->id );
					$this->_register->remove_item( $customer_email, 'cart' );
					$this->_pop_cart_from_waiting_queue( $customer_email );
				}

				// translators: 1. Order id
				YITH_WCAC()->log( sprintf( _x( 'Order %s processed', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id() ) );
			}

			if ( ! isset( $res->ecomOrder ) ){
				// translators: 1. Order id. 2. Answer body.
				YITH_WCAC()->log( sprintf( _x( 'Order %1$s wasn\'t processed due to an error: %2$s', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id(), print_r( $res, 1 ) ) );
			}

			return isset( $res->ecomOrder ) ? $res->ecomOrder : false;
		}

		/**
		 * Delete order if store is connected
		 *
		 * @param \WC_Order|int $order Order object or order id.
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_order( $order ) {
			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$order = $order instanceof WC_Order ? $order : wc_get_order( $order );

			if ( ! $order ) {
				return false;
			}

			// translators: 1. Order id.
			YITH_WCAC()->log( sprintf( _x( 'Attempting to delete order %s', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id() ) );

			$order_processed = $this->_register->is_item_processed( $order->get_id(), 'order' );

			if ( ! $order_processed ) {
				return false;
			}

			$external_id = $order_processed['ac_id'];

			$res = YITH_WCAC()->do_request( "ecomOrders/{$external_id}", 'DELETE' );

			$this->_register->remove_item( $order->get_id(), 'order' );

			// translators: 1. Order id.
			YITH_WCAC()->log( sprintf( _x( 'Order %s deleted', 'log message', 'yith-woocommerce-active-campaign' ), $order->get_id() ) );

			return $res;
		}

		/**
		 * Register cart if store is connected
		 *
		 * @param string        $customer_email User email.
		 * @param WC_Cart|array $cart           Cart object.
		 * @param string|bool   $currency       Currency at the time of cart registration.
		 * @param int           $abandoned_ts   Timestamp of last cart update.
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_cart( $customer_email, $cart = null, $currency = false, $abandoned_ts = null ) {
			// include required cart functions.
			include_once WC_ABSPATH . 'includes/wc-cart-functions.php';

			if ( ! $connection = $this->is_store_connected() ) {
				return false;
			}

			if ( ! $cart instanceof WC_Cart ) {
				$cart = $this->_prepare_cart_from_storage( $cart );
			}

			if ( ! $cart instanceof WC_Cart ) {
				return false;
			}

			// include required session dependencies.
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

			include_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-session.php' );
			include_once( WC()->plugin_path() . '/includes/class-wc-session-handler.php' );

			// Class instances.
			WC()->cart     = $cart;
			WC()->session  = new $session_class();
			WC()->customer = new WC_Customer();

			if ( ! $customer = $this->maybe_process_customer( $customer_email ) ) {
				// translators: 1. Customer email.
				YITH_WCAC()->log( sprintf( _x( 'Cart for user %s couldn\'t process because we weren\'t able to update customer object', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );

				return false;
			}

			// allow third party code to execute actions before processing.
			do_action( 'yith_wcac_before_cart_processing', $customer_email, $cart, $currency, $abandoned_ts );

			// translators: 1. Order id.
			YITH_WCAC()->log( sprintf( _x( 'Attempting to process cart for user %s', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );

			$cart_processed  = $this->_register->is_item_processed( $customer_email, 'cart' );
			$order_tax_total = 0;

			// update totals before creating args array.
			$cart->calculate_totals();

			if ( $taxes = $cart->get_tax_totals() ) {
				$order_tax_total = array_sum( wp_list_pluck( $taxes, 'amount' ) );
			}

			$args = [
				'ecomOrder' => [
					'externalcheckoutid'  => $this->get_object_uniqid( 'cart', $customer_email ),
					'source'              => apply_filters( 'yith_wcac_order_source', 1, null, $customer_email ),
					'email'               => $customer_email,
					'orderProducts'       => $this->_get_order_products( $cart, $currency ),
					'totalPrice'          => $this->_get_item_price( $cart->get_total( 'edit' ), $currency ),
					'shippingAmount'      => $this->_get_item_price( $cart->get_shipping_total(), $currency ),
					'taxAmount'           => $this->_get_item_price( $order_tax_total, $currency ),
					'currency'            => $currency,
					'connectionid'        => $connection->id,
					'customerid'          => $customer->id,
					'orderUrl'            => wc_get_checkout_url(),
					'externalCreatedDate' => gmdate( 'Y-m-d H:i:s', $abandoned_ts ),
					'externalUpdatedDate' => gmdate( 'Y-m-d H:i:s', $abandoned_ts ),
					'abandoned_date'      => gmdate( 'Y-m-d H:i:s', time() ),
					'orderDiscounts'      => $this->_get_order_discounts( $cart, $currency ),
				]
			];

			$external_id = isset( $cart_processed['ac_id'] ) ? $cart_processed['ac_id'] : false;
			$method      = $cart_processed ? 'PUT' : 'POST';
			$request     = $cart_processed ? "ecomOrders/{$external_id}" : 'ecomOrders';

			if ( ! $args = apply_filters( 'yith_wcac_process_cart_args', $args, $cart ) ) {
				// translators: 1. Order id.
				YITH_WCAC()->log( sprintf( _x( 'Cart for user %s skipped', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );

				return false;
			}

			$res = YITH_WCAC()->do_request( $request, $method, [], $args );

			if ( isset( $res->ecomOrder ) || yith_wcac_doing_task() ) {
				if ( isset( $res->ecomOrder ) ) {
					$this->_register->maybe_add_item( $customer_email, 'cart', $res->ecomOrder->id );
					$this->_pop_cart_from_waiting_queue( $customer_email );
				}

				// translators: 1. Order id.
				YITH_WCAC()->log( sprintf( _x( 'Cart for user %s processed', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );
			}

			if ( ! isset( $res->ecomOrder ) ){
				// translators: 1. Order id. 2. Answer body.
				YITH_WCAC()->log( sprintf( _x( 'Cart for user %1$s wasn\'t processed due to an error: %2$s', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email, print_r( $res, 1 ) ) );
			}

			return isset( $res->ecomOrder ) ? $res->ecomOrder : false;
		}

		/**
		 * Delete cart if store is connected
		 *
		 * @param string $customer_email Email of the user that created cart.
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_delete_cart( $customer_email ) {
			if ( ! $this->is_store_connected() ) {
				return false;
			}

			$cart_processed = $this->_register->is_item_processed( $customer_email, 'cart' );

			if ( ! $cart_processed ) {
				return false;
			}

			$external_id = $cart_processed['ac_id'];
			$request     = "ecomOrders/{$external_id}";

			$res = YITH_WCAC()->do_request( $request, 'DELETE' );

			$this->_register->remove_item( $customer_email, 'order' );
			$this->_pop_cart_from_waiting_queue( $customer_email );

			// translators: 1. Customer email.
			YITH_WCAC()->log( sprintf( _x( 'Cart for customer %s deleted', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );

			return $res;
		}

		/**
		 * Register customer if store is connected
		 *
		 * @param string   $customer_email Customer email.
		 * @param WC_Order $order          Order for the customer, if exists.
		 *
		 * @return mixed Status of the operation
		 */
		public function maybe_process_customer( $customer_email, $order = null ) {
			if ( ! $connection = $this->is_store_connected() ) {
				return false;
			}

			$customer_processed = $this->_register->is_item_processed( $customer_email, 'customer' );

			if ( ! is_null( $order ) ) {
				$show_checkbox   = yit_get_prop( $order, '_yith_wcac_show_checkbox', true );
				$submitted_value = yit_get_prop( $order, '_yith_wcac_submitted_value', true );

				$accepts_marketing = ( yith_wcac_doing_task() && apply_filters( 'yith_wcac_opt_in_status_during_sync', true ) ) || ! $show_checkbox || 'yes' === $submitted_value;
			} else {
				$accepts_marketing = apply_filters( 'yith_wcac_default_opt_in_status', false );
			}

			$args = [
				'ecomCustomer' => [
					'connectionid'     => $connection->id,
					'externalid'       => $this->get_object_uniqid( 'customer', $customer_email ),
					'email'            => $customer_email,
					'acceptsMarketing' => $accepts_marketing ? '1' : '0',
				]
			];

			$external_id = isset( $customer_processed['ac_id'] ) ? $customer_processed['ac_id'] : false;
			$method      = $customer_processed ? 'PUT' : 'POST';
			$request     = $customer_processed ? "ecomCustomers/{$external_id}" : 'ecomCustomers';

			if ( ! $args = apply_filters( 'yith_wcac_process_customer_args', $args, $customer_email ) ) {
				// translators: 1. Customer email.
				YITH_WCAC()->log( sprintf( _x( 'Customer %s skipped', 'log message', 'yith-woocommerce-active-campaign' ), $customer_processed ) );

				return false;
			}

			$res = YITH_WCAC()->do_request( $request, $method, [], $args );

			if ( isset( $res->ecomCustomer ) || yith_wcac_doing_task() ) {
				if( ! is_null( $order ) ) {
					YITH_WCAC()->do_request( 'contact/sync', 'POST', [], [
						'contact' => [
							'email' => $customer_email,
							'firstName' => $order->get_billing_first_name(),
							'lastName' => $order->get_billing_last_name()
						]
					] );
				}

				$this->_register->maybe_add_item( $customer_email, 'customer', $res->ecomCustomer->id );

				// translators: 1. Customer email.
				YITH_WCAC()->log( sprintf( _x( 'Customer %s processed', 'log message', 'yith-woocommerce-active-campaign' ), $customer_email ) );
			}

			return isset( $res->ecomCustomer ) ? $res->ecomCustomer : false;
		}

		/* === AJAX CALL HANDLING === */

		/**
		 * Register in the session email entered by customer at checkout
		 *
		 * @return void
		 */
		public function register_session_billing_email() {
			if ( ! isset( $_REQUEST['security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'register_session_billing_email' ) ) {
				die;
			}

			if ( is_user_logged_in() ) {
				die;
			}

			if ( 'yes' !== get_option( 'yith_wcac_store_integration_abandoned_cart_enable_guest' ) ) {
				die;
			}

			$customer_email = isset( $_REQUEST['email'] ) ? sanitize_email( wp_unslash( $_REQUEST['email'] ) ) : false;

			if ( ! $customer_email ) {
				die;
			}

			// first check if we enqueued another cart for current session.
			$previous_registered_email = WC()->session->get( 'yith_wcac_guest_billing_email' );

			if ( $customer_email === $previous_registered_email ) {
				// if email was already used to schedule cart, do don't need to do anything.
				die;
			}

			// if email changed, remove previously enqueue cart.
			if ( $previous_registered_email ) {
				$this->_pop_cart_from_waiting_queue( $previous_registered_email );
			}

			// register new billing email for current session.
			WC()->session->set( 'yith_wcac_guest_billing_email', $customer_email );

			// enqueue back cart.
			$this->process_cart();

			die;
		}

		/* === CART QUEUE UTILITIES === */

		/**
		 * Enqueue cart to waiting list
		 *
		 * @param string   $customer_email Customer email.
		 * @param \WC_Cart $cart           Cart to register.
		 * @param string   $currency       Current store currency.
		 */
		private function _push_cart_to_waiting_queue( $customer_email, $cart, $currency ) {
			$this->_carts_waiting_queue->maybe_add_item( $customer_email, $cart, $currency );

			if ( ! $this->_carts_waiting_queue->is_empty() && ! wp_next_scheduled( 'yith_wcac_enqueue_abandoned_carts' ) ) {
				wp_schedule_event( time() + 30, 'hourly', 'yith_wcac_enqueue_abandoned_carts' );
			}
		}

		/**
		 * Removes a cart from the waiting queue
		 *
		 * @param string $customer_email Customer email.
		 *
		 * @return array Item found, if any; null otherwise
		 */
		private function _pop_cart_from_waiting_queue( $customer_email ) {
			$item = $this->_carts_waiting_queue->remove_item( $customer_email );

			if ( $this->_carts_waiting_queue->is_empty() && wp_next_scheduled( 'yith_wcac_enqueue_abandoned_carts' ) ) {
				wp_clear_scheduled_hook( 'yith_wcac_enqueue_abandoned_carts' );
			}

			return $item;
		}

		/* === ORDER / CART UTILITIES === */

		/**
		 * When called, filters current WooCommerce currency, to change it while processing Deep Data sync
		 *
		 * @param string   $customer_email Customer email; not used.
		 * @param \WC_Cart $cart           Cart being processed; not used.
		 * @param string   $currency       Currency for current cart.
		 *
		 * @return void
		 */
		public function filter_currency_for_cart( $customer_email, $cart, $currency ) {
			if ( $currency === get_woocommerce_currency() ) {
				return;
			}

			$this->current_currency = $currency;

			apply_filters( 'woocommerce_currency', array( $this, 'set_currency_for_cart' ) );
		}

		/**
		 * Actually filters WooCommerce currecny
		 *
		 * @param string $currency Currency currently set.
		 *
		 * @return string Currency to use
		 */
		public function set_currency_for_cart( $currency ) {
			if ( empty( $this->current_currency ) ) {
				return $currency;
			}

			return $this->current_currency;
		}

		/**
		 * Accepts order object or persistent cart, and returns items array
		 *
		 * @param WC_Order|WC_Cart $object   Order or Cart.
		 * @param string|bool      $currency Order currency, or customer one at the moment of abandoning the cart.
		 *
		 * @return array Array of items formatted to be used in API call.
		 */
		private function _get_order_products( $object, $currency = false ) {
			$order_products = [];

			if ( ! $currency ) {
				$currency = $object instanceof WC_Order ? $object->get_currency() : get_woocommerce_currency();
			}

			if ( $object instanceof WC_Order ) {
				$items = $object->get_items();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						/**
						 * @var $item \WC_Order_Item_Product
						 */
						$product = $item->get_product();

						if ( ! $product ) {
							continue;
						}

						$categories      = wc_get_product_terms( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(), 'product_cat' );
						$categories_list = [];

						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$categories_list[] = $category->name;
							}
						}

						$order_products[] = apply_filters( 'yith_wcac_order_product', [
							'externalid'  => $this->get_object_uniqid( 'product', $product->get_id() ),
							'name'        => $product->get_title(),
							'price'       => $this->_get_item_price( $item->get_total() / $item->get_quantity(), $currency ),
							'quantity'    => $item->get_quantity(),
							'category'    => ! empty( $categories_list ) ? implode( ' | ', $categories_list ) : '',
							'sku'         => $product->get_sku(),
							'description' => $product->get_description(),
							'imageUrl'    => $this->_get_order_product_image( $product ),
							'productUrl'  => $product->get_permalink(),
						], $item, $product, $object );
					}
				}
			} elseif ( $object instanceof WC_Cart ) {
				$items = $object->get_cart_contents();

				if ( ! empty( $items ) ) {
					foreach ( $items as $key => $item ) {
						$product = $item['data'];

						if ( ! $product || ! $product instanceof WC_Product ) {
							continue;
						}

						// retrieve product category list.
						$categories      = wc_get_product_terms( $product->get_id(), 'product_cat' );
						$categories_list = [];

						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								$categories_list[] = $category->name;
							}
						}

						// retrieve product unit price.
						if ( $object->display_prices_including_tax() ) {
							$product_price = wc_get_price_including_tax( $product );
						} else {
							$product_price = wc_get_price_excluding_tax( $product );
						}

						$order_products[] = apply_filters( 'yith_wcac_order_product', [
							'externalid'  => $this->get_object_uniqid( 'product', $product->get_id() ),
							'name'        => $product->get_title(),
							'price'       => $this->_get_item_price( $product_price, $currency ),
							'quantity'    => $item['quantity'],
							'category'    => ! empty( $categories_list ) ? implode( ' | ', $categories_list ) : '',
							'sku'         => $product->get_sku(),
							'description' => $product->get_description(),
							'imageUrl'    => $this->_get_order_product_image( $product ),
							'productUrl'  => $product->get_permalink(),
						], $item, $product, $object );
					}
				}
			}

			return $order_products;
		}

		/**
		 * Accepts order object or persistent cart, and returns items array
		 *
		 * @param WC_Order|WC_Cart $object   Order or Cart.
		 * @param string|bool      $currency Order currency, or customer one at the moment of abandoning the cart.
		 *
		 * @return array Array of items formatted to be used in API call.
		 */
		private function _get_order_discounts( $object, $currency = false ) {
			$order_discounts = [];

			if ( ! $currency ) {
				$currency = $object instanceof WC_Order ? $object->get_currency() : get_woocommerce_currency();
			}

			if ( $object instanceof WC_Order ) {
				$items = $object->get_coupons();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						$order_discounts[] = [
							'name'           => $item->get_name(),
							'type'           => 'order',
							'discountAmount' => $this->_get_item_price( $item->get_discount(), $currency ),
						];
					}
				}
			} elseif ( $object instanceof WC_Cart ) {
				$items = $object->get_coupons();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item ) {
						$order_discounts[] = [
							'name'           => $item->get_code(),
							'type'           => 'order',
							'discountAmount' => $this->_get_item_price( $object->get_coupon_discount_amount( $item->get_code(), $object->display_cart_ex_tax ), $currency ),
						];
					}
				}
			}

			return $order_discounts;
		}

		/**
		 * Returns image url for a product
		 *
		 * @param \WC_Product $product Product.
		 * @return string Url of the image, or empty on failure.
		 */
		private function _get_order_product_image( $product ) {
			$product_image_id = $product->get_image_id();

			if ( ! $product_image_id && $parent_id = $product->get_parent_id() ) {
				$parent_product   = wc_get_product( $parent_id );
				$product_image_id = $parent_product->get_image_id();
			}

			$product_image = $product_image_id ? wp_get_attachment_image_url( $product_image_id, 'woocommerce_thumbnail' ) : '';

			return $product_image;
		}

		/**
		 * Returns formatted price for items (in cents)
		 *
		 * @param float  $price    Item original price.
		 * @param string $currency Order/Cart currency.
		 *
		 * @return int Formatted price
		 */
		private function _get_item_price( $price, $currency ) {
			return apply_filters( 'yith_wcac_item_price', intval( $price * 100 ), $currency );
		}

		/**
		 * Returns array to save in database, containing cart information
		 *
		 * @param \WC_Cart $cart Cart to convert.
		 *
		 * @return array|bool Array to store; false on failure.
		 */
		private function _prepare_cart_for_storage( $cart ) {
			try {
				$session     = new WC_Cart_Session( $cart );
				$stored_cart = array(
					'cart'                       => $session->get_cart_for_session(),
					'cart_totals'                => $cart->get_totals(),
					'applied_coupons'            => $cart->get_applied_coupons(),
					'coupon_discount_totals'     => $cart->get_coupon_discount_totals(),
					'coupon_discount_tax_totals' => $cart->get_coupon_discount_tax_totals(),
					'removed_cart_contents'      => $cart->get_removed_cart_contents(),
				);

				return $stored_cart;
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Return cart object from stored value
		 *
		 * @param array|string $stored_cart Array of items describing cart.
		 *
		 * @return \WC_Cart Cart object generated.
		 */
		private function _prepare_cart_from_storage( $stored_cart ) {
			$cart = new WC_Cart();
			$cart_contents = $stored_cart['cart'];

			// fool WooCommerce into thinking we already read cart from session.
			do_action( 'woocommerce_load_cart_from_session' );

			// restore data.
			foreach ( $cart_contents as $key => $values ) {
				$product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );
				$cart_contents[ $key ]['data'] = $product;
			}

			$cart->set_cart_contents( $cart_contents );
			$cart->set_totals( $stored_cart['cart_totals'] );
			$cart->set_applied_coupons( $stored_cart['applied_coupons'] );
			$cart->set_coupon_discount_totals( $stored_cart['coupon_discount_totals'] );
			$cart->set_coupon_discount_tax_totals( $stored_cart['coupon_discount_tax_totals'] );
			$cart->set_removed_cart_contents( $stored_cart['removed_cart_contents'] );

			return $cart;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAC_Deep_Data class
 *
 * @return \YITH_WCAC_Deep_Data
 * @since 1.0.0
 */
function YITH_WCAC_Deep_Data() {
	return YITH_WCAC_Deep_Data::get_instance();
}

