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
			$product_id = $this->get_product_id();
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
		protected function get_product_id() {
			if ( isset ( $_REQUEST['post'] ) ) {
				$product_id = absint( $_REQUEST['post'] );
			} elseif ( isset ( $_REQUEST['post_ID'] ) ) {
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
				echo "</p></div>";
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

		public function enqueue_styles() {
			wp_enqueue_style( 'wcwl_admin', WCWL_ENQUEUE_PATH . '/includes/css/src/wcwl_admin.min.css', array(), WCWL_VERSION );
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
			if ( WCWL_SLUG . '_count' == $orderby ) {
				$query->set( 'meta_key', '_' . WCWL_SLUG . '_count' );
				$query->set( 'orderby', 'meta_value_num' );
			}
		}

		/**
		 * Alerts user of moved waitlists at 1.0.4 upgrade
		 *
		 * @access public
		 * @return void
		 */
		public function alert_user_of_moved_waitlists_at_1_0_4_upgrade() {
			$options = get_option( WCWL_SLUG, true );
			if ( isset( $options['moved_waitlists_at_1_0_4_upgrade'] ) && is_array( $options['moved_waitlists_at_1_0_4_upgrade'] ) && ! empty( $options['moved_waitlists_at_1_0_4_upgrade'] ) ) {
				echo '<div class="updated"><p>';
				echo apply_filters( 'wcwl_moved_waitlists_at_1_0_4_upgrade_text', sprintf( $this->moved_waitlists_at_1_0_4_upgrade_text, WCWL_VERSION ) );
				echo '</p><ul>';
				foreach ( $options['moved_waitlists_at_1_0_4_upgrade'] as $waitlist ) {
					echo '<li>';
					printf( esc_html__( 'Waitlist for product %s has been moved to %s (User IDs: %s)', 'woocommerce-waitlist' ), '<strong>' . get_the_title( $waitlist['origin'] ) . '</strong>', '<strong>' . get_the_title( $waitlist['target'] ) . '</strong>', implode( ', ', $waitlist['user_ids'] ) );
					echo ' - <a href="' . esc_url( admin_url( 'post.php?post=' . $waitlist['origin'] . '&action=edit' ) ) . '">' . __( 'Edit Product', 'woocommerce-waitlist' ) . '</a></li>';
				}
				echo '</ul></div>';
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
			$this->general_settings_option_group_title            = __( "Out-of-stock Waitlist", 'woocommerce-waitlist' );
			$this->general_settings_option_group_description      = __( "The following options control the behaviour of the waitlist for out-of-stock products.", 'woocommerce-waitlist' );
			$this->general_settings_registration_option_heading   = __( "Registration", 'woocommerce-waitlist' );
			$this->general_settings_registration_option_one_label = __( "Enable guest waitlist registration (no account required)", 'woocommerce-waitlist' );
			$this->hide_out_of_stock_products_nag                 = __( 'The WooCommerce Waitlist extension is active but you have the "Hide out of stock items from the catalog" option switched on. Please %schange your settings%s for WooCommerce Waitlist to function correctly.', 'woocommerce-waitlist' );
			$this->update_waitlist_counts_nag                     = __( 'Your WooCommerce Waitlist counts may be inaccurate. Please %shead to the settings page%s and click "Update Counts" to do this now.', 'woocommerce-waitlist' );
			$this->corrupt_waitlist_data_nag                      = __( 'WooCommerce Waitlist has discovered waitlist entries on translation products. Please %scontact support%s to get help with resolving this issue.', 'woocommerce-waitlist' );
			$this->metadata_update_nag                            = __( 'WooCommerce Waitlist needs to update your database entries to fully function. Please %shead to the settings page%s and click "Update Metadata" to do this now', 'woocommerce-waitlist' );
			$this->version_2_nag                                  = __( 'Thank you for updating WooCommerce Waitlist to version 2. If you have any issues please %srefer to the documentation%s to review the changes made with this update', 'woocommerce-waitlist' );
			$this->dismiss_nag_text                               = __( "Stop nagging me", 'woocommerce-waitlist' );
			$this->moved_waitlists_at_1_0_4_upgrade_text          = __( 'In order to support waitlists for product variations in WooCommerce Waitlist version %s, the waitlists for the following variable products have been moved to the corresponding product variations:', 'woocommerce-waitlist' );
			$this->original_variable_product                      = __( 'Original variable product', 'woocommerce-waitlist' );
			$this->new_product_variation                          = __( 'New product variation', 'woocommerce-waitlist' );
			$this->list_of_user_ids                               = __( 'List of user IDs', 'woocommerce-waitlist' );
		}
	}
}
