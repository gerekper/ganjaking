<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Admin_Init' ) ) {
	/**
	 * The Admin User Interface
	 *
	 * @package WooCommerce Waitlist
	 */
	class Pie_WCWL_Admin_Init {

		/**
		 * Initialise Admin class
		 */
		public function init() {
			$this->load_files();
			$this->load_hooks();
			$this->setup_text_strings();
		}

		/**
		 * Load required files
		 */
		protected function load_files() {
			require_once 'class-pie-wcwl-waitlist-settings.php';
			$settings = new Pie_WCWL_Waitlist_Settings();
			$settings->init();
			require_once 'class-pie-wcwl-admin-ajax.php';
			$ajax = new Pie_WCWL_Admin_Ajax();
			$ajax->init();
			require_once 'class-pie-wcwl-exporter.php';
			$exporter = new Pie_WCWL_Exporter();
			$exporter->init();
		}

		/**
		 * Add hooks
		 */
		protected function load_hooks() {
			// Init
			add_action( 'init', array( $this, 'load_waitlist' ), 20 );
			add_action( 'wc_bulk_stock_before_process_qty', array( $this, 'load_waitlist_from_product_id' ), 5 );
			add_action( 'admin_notices', array( $this, 'set_up_admin_nags' ), 15 );
			add_action( 'admin_init', array( $this, 'ignore_admin_nags' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			// Columns
			add_filter( 'manage_edit-product_columns', array( $this, 'add_waitlist_column_header' ), 11 );
			add_action( 'manage_product_posts_custom_column', array( $this, 'add_waitlist_column_content' ), 10, 2 );
			add_filter( 'manage_edit-product_sortable_columns', array( $this, 'register_waitlist_column_as_sortable' ) );
			add_action( 'pre_get_posts', array( $this, 'sort_by_waitlist_column' ), 10, 1 );
			// Events
			add_action( 'tribe_events_tickets_metabox_edit_accordion_content', array( $this, 'add_waitlist_for_tickets' ), 99, 2 );
		}

		/**
		 * Sets up the waitlist and calls product tab function if required
		 *
		 * @hooked action init
		 * @access public
		 * @return void
		 * @since  1.0.1
		 */
		public function load_waitlist() {
			$product_id = $this->get_post_id();
			if ( 'product' !== get_post_type( $product_id ) ) {
				return;
			}
			$this->load_waitlist_from_product_id( $product_id );
		}

		/**
		 * Get post ID
		 *
		 * @return string
		 */
		protected function get_post_id() {
			if ( isset( $_REQUEST['post'] ) ) {
				$product_id = absint( $_REQUEST['post'] );
			} elseif ( isset( $_REQUEST['post_ID'] ) ) {
				$product_id = absint( $_REQUEST['post_ID'] );
			} else {
				$product_id = '';
			}

			return $product_id;
		}

		/**
		 * Sets up the waitlist from the post id and calls product tab function if required
		 *
		 * We don't want the waitlist tab to appear for grouped products as each linked product will have it's own waitlist
		 *
		 * @param  int $product_id id of the post
		 *
		 * @access public
		 * @return void
		 */
		public function load_waitlist_from_product_id( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product && 'grouped' != $product->get_type() && array_key_exists( $product->get_type(), WooCommerce_Waitlist_Plugin::$allowed_product_types ) ) {
				require_once 'product-tab/class-pie-wcwl-custom-admin-tab.php';
				$tab = new Pie_WCWL_Custom_Tab( $product );
				$tab->init();
			}
		}

		/**
		 * Hook up admin nags as and when required
		 */
		public function set_up_admin_nags() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}
			if ( 'no' !== get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$this->set_up_nag( 'updated', 'hide_out_of_stock_products_nag', $this->get_settings_url( 'inventory' ) );
			}
			if ( ! get_option( '_' . WCWL_SLUG . '_counts_updated' ) ) {
				$this->set_up_nag( 'updated', 'update_waitlist_counts_nag', self::get_settings_url( 'waitlist' ) );
			}
			if ( get_option( '_' . WCWL_SLUG . '_corrupt_data' ) ) {
				$this->set_up_nag( 'updated', 'corrupt_waitlist_data_nag', 'https://woocommerce.com/my-account/create-a-ticket/' );
			}
			if ( ! get_option( '_' . WCWL_SLUG . '_metadata_updated' ) ) {
				$this->set_up_nag( 'updated', 'metadata_update_nag', self::get_settings_url( 'waitlist' ) );
			}
			if ( ! get_option( '_' . WCWL_SLUG . '_version_2_warning' ) ) {
				$this->set_up_nag( 'updated', 'version_2_nag', 'https://docs.woocommerce.com/document/woocommerce-waitlist/#section-10' );
			}
		}

		/**
		 * Add all nag notices in using a particular format
		 *
		 * @param $status string type of notice to be used
		 * @param $type   string type of nag that we are outputting
		 * @param $link   string link to be used in our string to aid the user in fixing the issue
		 */
		protected function set_up_nag( $status, $type, $link ) {
			global $current_user;
			$usermeta = get_user_meta( $current_user->ID, '_' . WCWL_SLUG, true );
			if ( ! isset( $usermeta[ 'ignore_' . $type ] ) || ! $usermeta[ 'ignore_' . $type ] ) {
				echo '<div class="' . $status . '"><p>';
				echo apply_filters( 'wcwl_{$type}_nag_text', sprintf( $this->{$type}, '<a href="' . $link . '">', '</a>' ) ) . ' | <a href="' . esc_url( add_query_arg( 'ignore_' . $type, true ) ) . '">' . apply_filters( 'wcwl_dismiss_nag_text', $this->dismiss_nag_text ) . '</a>';
				echo '</p></div>';
			}
		}

		/**
		 * When a user selects the option to ignore a nag add this to their usermeta so we don't display it again
		 */
		public function ignore_admin_nags() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}
			if ( isset( $_GET['ignore_hide_out_of_stock_products_nag'] ) && $_GET['ignore_hide_out_of_stock_products_nag'] ) {
				$this->ignore_nag( 'ignore_hide_out_of_stock_products_nag' );
			}
			if ( isset( $_GET['ignore_update_waitlist_counts_nag'] ) && $_GET['ignore_update_waitlist_counts_nag'] ) {
				$this->ignore_nag( 'ignore_update_waitlist_counts_nag' );
			}
			if ( isset( $_GET['ignore_corrupt_waitlist_data_nag'] ) && $_GET['ignore_corrupt_waitlist_data_nag'] ) {
				$this->ignore_nag( 'ignore_corrupt_waitlist_data_nag' );
			}
			if ( isset( $_GET['ignore_metadata_update_nag'] ) && $_GET['ignore_metadata_update_nag'] ) {
				$this->ignore_nag( 'ignore_metadata_update_nag' );
			}
			if ( isset( $_GET['ignore_version_2_nag'] ) && $_GET['ignore_version_2_nag'] ) {
				$this->ignore_nag( 'ignore_version_2_nag' );
			}
		}

		/**
		 * Ignore selected nags by user
		 *
		 * @param $type string type of nag the user has selected to ignore
		 */
		protected function ignore_nag( $type ) {
			global $current_user;
			$usermeta = get_user_meta( $current_user->ID, '_' . WCWL_SLUG, true );
			if ( ! is_array( $usermeta ) ) {
				$usermeta = array();
			}
			$usermeta[ $type ] = true;
			update_user_meta( $current_user->ID, '_' . WCWL_SLUG, $usermeta );
		}

		/**
		 * Function to get the URL of of the inventory settings page. Settings URLs were refactored in 2.1 with no API
		 * provided to retrieve them
		 *
		 * @access public
		 *
		 * @param $section
		 *
		 * @return string
		 * @since  1.8.0
		 */
		public static function get_settings_url( $section ) {
			return admin_url( 'admin.php?page=wc-settings&tab=products&section=' . $section );
		}

		/**
		 * Enqueue styles
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'wcwl_admin', WCWL_ENQUEUE_PATH . '/includes/css/src/wcwl_admin.min.css', array(), WCWL_VERSION );
			wp_enqueue_script( 'wcwl_admin_custom_tab', WCWL_ENQUEUE_PATH . '/includes/js/src/wcwl_admin_custom_tab.min.js', array(), WCWL_VERSION, true );
			$data = $this->get_data_required_for_js();
			wp_localize_script( 'wcwl_admin_custom_tab', 'wcwl_tab', $data );
		}

		/**
		 * Setup data for JS
		 *
		 * @return array
		 */
		protected function get_data_required_for_js() {
			return array(
				'admin_email'            => get_option( 'woocommerce_email_from_address' ),
				'invalid_email'          => __( 'One or more emails entered appear to be invalid', 'woocommerce-waitlist' ),
				'add_text'               => __( 'Add', 'woocommerce-waitlist' ),
				'no_users_text'          => __( 'No users selected', 'woocommerce-waitlist' ),
				'no_action_text'         => __( 'No action selected', 'woocommerce-waitlist' ),
				'view_profile_text'      => __( 'View User Profile', 'woocommerce-waitlist' ),
				'go_text'                => __( 'Go', 'woocommerce-waitlist' ),
				'update_button_text'     => __( 'Update Options', 'woocommerce-waitlist' ),
				'update_waitlist_notice' => __( 'Waitlists may be appear inaccurate due to an update to variations. Please update the product or refresh the page to update waitlists', 'woocommerce-waitlist' ),
				'current_user'           => get_current_user_id(),
			);
		}

		/**
		 * Appends the element needed to create a custom admin column to an array
		 *
		 * @hooked filter manage_edit-product_columns
		 *
		 * @param array $defaults the array to append
		 *
		 * @access public
		 * @return array The $defaults array with custom column values appended
		 * @since  1.0
		 */
		public function add_waitlist_column_header( $defaults ) {
			$defaults[ WCWL_SLUG . '_count' ] = $this->column_title;

			return $defaults;
		}

		/**
		 * Outputs total waitlist members for a given post ID if $column_name is our custom column
		 *
		 * @hooked action manage_product_posts_custom_column
		 *
		 * @param string $column_name name of the column for which we are outputting data
		 * @param mixed  $post_ID     ID of the post for which we are outputting data
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function add_waitlist_column_content( $column_name, $post_ID ) {
			if ( WCWL_SLUG . '_count' != $column_name ) {
				return;
			}
			$content = get_post_meta( $post_ID, '_' . WCWL_SLUG . '_count', true );
			echo empty( $content ) ? '<span class="na">–</span>' : $content;
		}

		/**
		 * Appends our column ID to an array
		 *
		 * @hooked filter manage_edit-product_sortable_columns
		 *
		 * @param array $columns The WP admin sortable columns array.
		 *
		 * @access public
		 * @return array
		 * @since  1.0
		 */
		public function register_waitlist_column_as_sortable( $columns ) {
			$columns[ WCWL_SLUG . '_count' ] = WCWL_SLUG . '_count';

			return $columns;
		}

		/**
		 * Sort columns by waitlist count when required
		 *
		 * @param $query
		 */
		public function sort_by_waitlist_column( $query ) {
			if ( ! is_admin() ) {
				return;
			}
			$orderby = $query->get( 'orderby' );
			if ( WCWL_SLUG . '_count' === $orderby ) {
				$query->set( 'meta_key', '_' . WCWL_SLUG . '_count' );
				$query->set( 'orderby', 'meta_value_num' );
			}
		}

		/**
		 * Include HTML for waitlists on the event page
		 *
		 * @param int $post_id event ID.
		 * @param int $ticket_id ticket ID.
		 * @return void
		 */
		public function add_waitlist_for_tickets( $post_id, $ticket_id ) {
			if ( ! $ticket_id ) {
				return;
			}
			$product = wc_get_product( $ticket_id );
			if ( $product ) {
				require_once 'product-tab/class-pie-wcwl-custom-admin-tab.php';
				$tab = new Pie_WCWL_Custom_Tab( $product );
				$tab->init();
				$this->product = $product;
				include apply_filters( 'wcwl_include_path_admin_panel_event', plugin_dir_path( __FILE__ ) . 'product-tab/components/panel-event.php' );
			}
		}

		/**
		 * Sets up the text strings required by the admin UI
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function setup_text_strings() {
			$this->column_title                                   = __( 'Waitlist', 'woocommerce-waitlist' );
			$this->hide_out_of_stock_products_nag                 = __( 'The WooCommerce Waitlist extension is active but you have the "Hide out of stock items from the catalog" option switched on. Please %1$schange your settings%2$s for WooCommerce Waitlist to function correctly.', 'woocommerce-waitlist' );
			$this->update_waitlist_counts_nag                     = __( 'Your WooCommerce Waitlist counts may be inaccurate. Please %1$shead to the settings page%2$s and click "Update Counts" to do this now.', 'woocommerce-waitlist' );
			$this->corrupt_waitlist_data_nag                      = __( 'WooCommerce Waitlist has discovered waitlist entries on translation products. Please %1$scontact support%2$s to get help with resolving this issue.', 'woocommerce-waitlist' );
			$this->metadata_update_nag                            = __( 'WooCommerce Waitlist needs to update your database entries to fully function. Please %1$shead to the settings page%2$s and click "Update Metadata" to do this now', 'woocommerce-waitlist' );
			$this->version_2_nag                                  = __( 'Thank you for updating WooCommerce Waitlist to version 2. If you have any issues please %1$srefer to the documentation%2$s to review the changes made with this update', 'woocommerce-waitlist' );
			$this->dismiss_nag_text                               = __( 'Stop nagging me', 'woocommerce-waitlist' );
		}
	}
}
