<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Admin {


	/** @var Framework\SV_WP_Admin_Message_Handler instance **/
	private $message_handler;

	/** @var \WC_Order_Status_Manager_Admin_Orders instance **/
	private $admin_orders;

	/** @var \WC_Order_Status_Manager_Admin_Order_Statuses|\WC_Order_Status_Manager_Admin_Order_Status_Emails instance **/
	private $admin_order_statuses;


	/**
	 * Setup admin class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// ensure that message handler loaded
		$this->message_handler = wc_order_status_manager()->get_message_handler();

		// load admin classes and admin messages
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this->message_handler, 'load_messages' ) );

		// add styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// remove bulk actions
		add_filter( 'bulk_actions-edit-wc_order_status', '__return_empty_array' );
		add_filter( 'bulk_actions-edit-wc_order_email',  '__return_empty_array' );

		// remove date filter from list screen
		add_filter( 'months_dropdown_results', array( $this, 'remove_months_dropdown' ), 10, 2 );

		// normalize columns
		add_filter( 'manage_edit-wc_order_status_columns', array( $this, 'normalize_columns' ) );
		add_filter( 'manage_edit-wc_order_email_columns',  array( $this, 'normalize_columns' ) );

		// normalize row actions
		add_filter( 'post_row_actions', array( $this, 'normalize_row_actions' ), 100, 2 );

		// force-delete statuses and emails instead of trashing
		add_action( 'load-edit.php', array( $this, 'force_delete' ) );

		// hide title field and default publishing box
		add_action( 'post_submitbox_misc_actions', array( $this, 'normalize_edit_screen' ) );

		// hide search & filter on list screen
		add_action( 'restrict_manage_posts', array( $this, 'normalize_list_screen' ) );

		// save meta boxes
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// disable autosave for the out post types
		add_action( 'admin_footer', array( $this, 'disable_autosave' ) );

		// add Order Statuses tab to settings
		add_action( 'woocommerce_settings_tabs', array( $this, 'print_settings_tabs'), 1 );

		// display WooCommerce settings tabs on order status & emails pages
		add_action( 'all_admin_notices', array( $this, 'print_woocommerce_settings_tabs' ), 1 );
		add_action( 'all_admin_notices', array( $this->message_handler, 'show_messages' ) );
		add_action( 'all_admin_notices', array( $this, 'output_sections' ) );

		// highlight WooCommerce -> Settings when on order status manager pages
		add_filter( 'parent_file', array( $this, 'highlight_admin_menu' ) );
	}


	/**
	 * Add the Order Statuses tab to WooCommerce settings
	 *
	 * @since 1.0.0
	 */
	public function print_settings_tabs() {
		global $typenow;

		?>
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_order_status' ) ); ?>" class="nav-tab <?php echo ( 'wc_order_status' === $typenow ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Order Statuses', 'woocommerce-order-status-manager' ); ?></a>
		<?php
	}


	/**
	 * Print WooCommerce settings tabs on order status manager screens
	 *
	 * Simulates a simplified version of WC_Admin_Settings::output and
	 * `html-admin-settings.php` from WC core
	 *
	 * @since 1.0.0
	 */
	public function print_woocommerce_settings_tabs() {

		if ( ! $this->is_order_status_manager_screen() ) {
			return;
		}

		WC_Admin_Settings::get_settings_pages();

		// get tabs for the settings page
		$tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );

		?>
		<div class="wrap woocommerce">
			<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
			<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				<?php foreach ( $tabs as $name => $label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . $name ) ); ?>" class="nav-tab"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
				<?php do_action( 'woocommerce_settings_tabs' ); ?>
			</h2>
		</div>
		<?php
	}


	/**
	 * Output Order Status Manager sections
	 *
	 * Simulates WC_Settings_Page->output_sections
	 *
	 * @since 1.0.0
	 */
	public function output_sections() {

		if ( ! $this->is_order_status_manager_screen() ) {
			return;
		}

		global $typenow;
		?>
		<ul class="subsubsub">
			<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_order_status' ) ); ?>" class="<?php echo ( 'wc_order_status' === $typenow ? 'current' : '' ); ?>"><?php esc_html_e( 'Statuses', 'woocommerce-order-status-manager' ); ?></a> | </li>
			<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wc_order_email' ) ); ?>" class="<?php echo ( 'wc_order_email' === $typenow ? 'current' : '' ); ?>"><?php esc_html_e( 'Emails', 'woocommerce-order-status-manager' ); ?></a></li>
		</ul>
		<br class="clear" />
		<?php
	}


	/**
	 * Show Messages
	 *
	 * @since 1.0.0
	 */
	public function show_messages() {

		wc_order_status_manager()->get_message_handler()->show_messages();
	}


	/**
	 * Highlight WooCommerce -> Settings admin menu item when editing an order
	 * status or order status email
	 *
	 * Besides modifying the filterable $parent_file, this function modifies the
	 * global $submenu_file variable.
	 *
	 * @since 1.0.0
	 * @param string $parent_file
	 * @return string $parent_file
	 */
	public function highlight_admin_menu( $parent_file ) {
		global $submenu_file;

		if ( $this->is_order_status_manager_screen() ) {

			$parent_file  = 'woocommerce';
			$submenu_file = 'wc-settings';
		}

		return $parent_file;
	}


	/**
	 * Initialize the admin, adding actions to properly display and handle
	 * the Order Status and Email custom post type add/edit pages
	 *
	 * @since 1.0.0
	 */
	public function init() {
		global $pagenow, $typenow;

		if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow || 'edit.php' === $pagenow ) {

			if ( 'wc_order_status' === $typenow || ( isset( $_GET['post'] ) && 'wc_order_status' === get_post_type( $_GET['post'] ) ) ) {
				require_once( wc_order_status_manager()->get_plugin_path() . '/includes/admin/class-wc-order-status-manager-admin-order-statuses.php' );
				$this->admin_order_statuses = new WC_Order_Status_Manager_Admin_Order_Statuses();
			}

			if ( 'wc_order_email' === $typenow || ( isset( $_GET['post'] ) && 'wc_order_email' === get_post_type( $_GET['post'] ) ) ) {
				require_once( wc_order_status_manager()->get_plugin_path() . '/includes/admin/class-wc-order-status-manager-admin-order-status-emails.php' );
				$this->admin_order_statuses = new WC_Order_Status_Manager_Admin_Order_Status_Emails();
			}

			if ( 'shop_order' === $typenow || ( isset( $_GET['post'] ) && 'shop_order' === get_post_type( $_GET['post'] ) ) ) {
				require_once( wc_order_status_manager()->get_plugin_path() . '/includes/admin/class-wc-order-status-manager-admin-orders.php' );
				$this->admin_orders = new WC_Order_Status_Manager_Admin_Orders();
			}
		}
	}

	/**
	 * Load admin js/css
	 *
	 * @since 1.0.0
	 */
	public function load_styles_scripts() {

		// get admin screen id
		$screen = get_current_screen();

		// order status edit screen specific styles & scripts
		if ( $screen && 'wc_order_status' === $screen->id ) {

			// color picker script/styles
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			// jquery fonticonpicker
			wp_enqueue_script( 'jquery-fonticonpicker', wc_order_status_manager()->get_plugin_url() . '/assets/js/vendor/jquery.fonticonpicker.min.js', array( 'jquery' ), WC_Order_Status_Manager::VERSION );

			wp_enqueue_style( 'wc-order-status-manager-jquery-fonticonpicker', wc_order_status_manager()->get_plugin_url() . '/assets/css/admin/wc-order-status-manager-jquery-fonticonpicker.min.css', null, WC_Order_Status_Manager::VERSION );

			wp_enqueue_media();
		}

		if ( $screen && 'edit-shop_order' === $screen->id ) {

			// Font Awesome font & classes
			wp_enqueue_style( 'font-awesome', wc_order_status_manager()->get_plugin_url() . '/assets/css/font-awesome.min.css', null, WC_Order_Status_Manager::VERSION );
		}

		// Load styles and scripts on order status screens
		// Also load on the plugin management screen so the deactivate link can
		// be modified to trigger a JavaScript notice to the user
		if ( ( $screen && 'plugins' === $screen->id ) || $this->is_order_status_manager_screen() ) {

			// load WC admin CSS
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );

			// Admin CSS
			wp_enqueue_style( 'wc-order-status-manager-admin', wc_order_status_manager()->get_plugin_url() . '/assets/css/admin/wc-order-status-manager-admin.min.css', array( 'woocommerce_admin_styles' ), WC_Order_Status_Manager::VERSION );

			// WooCommerce font class declarations
			wp_enqueue_style( 'woocommerce-font-classes', wc_order_status_manager()->get_plugin_url() . '/assets/css/woocommerce-font-classes.min.css', array( 'woocommerce_admin_styles' ), WC_Order_Status_Manager::VERSION );

			// Font Awesome font & classes
			wp_enqueue_style( 'font-awesome', wc_order_status_manager()->get_plugin_url() . '/assets/css/font-awesome.min.css', null, WC_Order_Status_Manager::VERSION );

			// Magnific Popup CSS
			wp_enqueue_style( 'magnific-popup', wc_order_status_manager()->get_plugin_url() . '/assets/css/vendor/magnific-popup.css' );

			// Magnific Popup JS
			wp_register_script( 'magnific-popup', wc_order_status_manager()->get_plugin_url() . '/assets/js/vendor/jquery.magnific-popup.min.js', array( 'jquery' ), WC_Order_Status_Manager::VERSION, true );

			// WC Order Status Manager Admin JS
			wp_enqueue_script(
				'wc-order-status-manager-admin',
				wc_order_status_manager()->get_plugin_url() . '/assets/js/admin/wc-order-status-manager-admin.min.js',
				array(
					'jquery',
					'jquery-tiptip',
					'jquery-ui-sortable',
					'magnific-popup',
					'select2',
				),
				\WC_Order_Status_Manager::VERSION
			);

			// localize admin JS
			$order_statuses = array();
			foreach ( wc_get_order_statuses() as $slug => $name ) {
				$order_statuses[ str_replace( 'wc-', '', $slug ) ] = $name;
			}

			$script_data = array(

				'ajax_url'                            => admin_url( 'admin-ajax.php' ),
				'sort_order_statuses_nonce'           => wp_create_nonce( 'sort-order-statuses' ),
				'import_custom_order_statuses_nonce'  => wp_create_nonce( 'import-custom-order-statuses' ),
				'delete_order_status_nonce'           => wp_create_nonce( 'delete-order-status' ),
				'bulk_reassign_order_status_nonce'    => wp_create_nonce( 'bulk-reassign-order-status' ),
				'set_deactivation_confirmation_state' => wp_create_nonce( 'set-deactivation-confirmation-state' ),
				'order_statuses'                      => $order_statuses,

				'i18n' => array(
					'remove_this_condition' => __( 'Remove this condition?', 'woocommerce-order-status-manager' ),
					'from_status'           => __( 'From Status', 'woocommerce-order-status-manager' ),
					'to_status'             => __( 'To Status', 'woocommerce-order-status-manager' ),
					'remove'                => __( 'Remove', 'woocommerce-order-status-manager' ),
					'any'                   => __( 'Any', 'woocommerce-order-status-manager' ),
					'remove_icon'           => __( 'Remove Icon', 'woocommerce-order-status-manager' ),
					'select_icon'           => __( 'Select Icon', 'woocommerce-order-status-manager' ),
					'all_icon_packages'     => __( 'All icon packages', 'woocommerce-order-status-manager' ),
					'search_icons'          => __( 'Search Icons', 'woocommerce-order-status-manager' ),
					'choose_file'           => __( 'Choose a file', 'woocommerce-order-status-manager' ),
					'close'                 => __( 'Close', 'woocommerce-order-status-manager' ),
					'confirm_any_status'    => __( 'Emails will not be sent for dispatch conditions where the To and From statuses are both \'Any\'. Continue anyway?', 'woocommerce-order-status-manager' ),
					'confirm_deactivate'    => __( 'Please be sure to remove custom statuses from orders before deactivating the plugin. Continue?', 'woocommerce-order-status-manager' ),
					'wc_prefix_disallowed'  => __( 'Sorry, slugs can\'t begin with "wc-".', 'woocommerce-order-status-manager' ),
				),
			);

			if ( 'wc_order_status' === $screen->id ) {

				// Create a flat list of icon classes for icon options, we
				// do not need the glyphs there
				$icon_options = array();
				foreach ( wc_order_status_manager()->get_icons_instance()->get_icon_options() as $package => $icons ) {
					$icon_options[ $package ] = array_keys( $icons );
				}

				$script_data['icon_options'] = $icon_options;
			}

			wp_localize_script( 'wc-order-status-manager-admin', 'wc_order_status_manager', $script_data );
		}
	}


	/**
	 * Removes Months dropdown from list screen by returning an empty array of months.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $months the months drop-down query results.
	 * @param string $post_type the post type.
	 * @return array
	 */
	public function remove_months_dropdown( $months, $post_type ) {

		return $this->is_order_status_manager_post_type( $post_type ) ? [] : $months;
	}


	/**
	 * Force-delete any trashed order statuses or emails
	 *
	 * @since 1.0.0
	 */
	public function force_delete() {
		global $typenow;

		if ( $this->is_order_status_manager_post_type( $typenow ) && isset( $_REQUEST['action'] ) && 'trash' === $_REQUEST['action'] ) {
			$_REQUEST['action'] = 'delete';
		}
	}


	/**
	 * Normalize order status / email columns
	 *
	 * @since 1.0.0
	 * @param array $columns
	 * @return array
	 */
	public function normalize_columns( $columns ) {

		// change the title column name
		$columns['title'] = __( 'Name', 'woocommerce-order-status-manager' );

		// remove checkbox and date columns
		unset( $columns['cb'], $columns['date'] );

		return $columns;
	}


	/**
	 * Order status & email row actions
	 *
	 * @since 1.0.0
	 * @param array $actions
	 * @param \WP_Post $post
	 * @return array
	 */
	public function normalize_row_actions( $actions, WP_Post $post ) {

		if ( $this->is_order_status_manager_post_type( get_post_type( $post->ID ) ) ) {

			unset( $actions['inline hide-if-no-js'], $actions['trash'] );

			$order_status = new WC_Order_Status_Manager_Order_Status( $post->ID );

			if ( current_user_can( 'delete_post', $post->ID ) && ! $order_status->is_core_status() ) {

				$actions['delete'] = sprintf(
					'<a class="submitdelete" title="%1$s" href="%2$s">%3$s</a>',
					esc_attr__( 'Delete this item permanently', 'woocommerce-order-status-manager' ),
					get_delete_post_link( $post->ID, '', true ),
					__( 'Delete Permanently', 'woocommerce-order-status-manager' )
				);
			}
		}

		return $actions;
	}


	/**
	 * Hide title field and default publishing box
	 *
	 * @since 1.0.0
	 */
	public function normalize_edit_screen() {
		global $post;

		if ( $this->is_order_status_manager_post_type( get_post_type( $post->ID ) ) ) {

			?>
			<style type="text/css">
				#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv, #woocommerce-order-status-data .handlediv, #woocommerce-order-status-data h3.hndle, #woocommerce-order-status-email-data .handlediv, #woocommerce-order-status-email-data h3.hndle { display:none }
			</style>
			<?php
		}
	}


	/**
	 * Hide title field and default publishing box
	 *
	 * @since 1.0.0
	 */
	public function normalize_list_screen() {
		global $typenow;

		if ( $this->is_order_status_manager_post_type( $typenow ) ) {

			?>
			<style type="text/css">
				#posts-filter .search-box, #posts-filter .actions, #posts-filter .view-switch { display:none }
			</style>
			<?php
		}
	}


	/**
	 * Check if we're saving, then trigger an action based on the post type
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save_meta_boxes( $post_id, $post ) {

		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// check the nonce
		if ( empty( $_POST['wc_order_status_manager_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wc_order_status_manager_meta_nonce'], 'wc_order_status_manager_save_data' ) ) {
			return;
		}

		// check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || (int) $_POST['post_ID'] !== (int) $post_id ) {
			return;
		}

		// check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( $this->is_order_status_manager_post_type( $post->post_type ) ) {

			// prefixing a status slug with "wc-" would result in double prefixing under the hood and open potential issues with WooCommerce handling of custom statuses
			if ( Framework\SV_WC_Helper::str_starts_with( $post->post_name, 'wc-' ) ) {

				// trims the prefix iteratively, e.g. "wc-wc-custom-status" still becomes "custom-status" rather than "wc-custom-status"
				$slug = ltrim( $post->post_name, 'wc-' );
				// accounts for a very rare possibility where the name or the slug is exactly "wc-"
				$slug = '' === $slug ? 'custom-wc' : $slug;

				$new_post_id = wp_update_post( [
					'ID'        => $post->ID,
					'post_name' => $slug,
				] );

				if ( is_numeric( $new_post_id ) ) {

					$post    = get_post( $new_post_id );
					$post_id = $post->ID;

					wc_order_status_manager()->get_admin_instance()->message_handler->add_warning( sprintf(
						/* translators: Placeholder: %s - custom order status slug */
						__( 'Sorry, slugs can\'t begin with "wc-". Order status has been renamed "%s".', 'woocommerce-order-status-manager' ),
						$post->post_name
					) );
				}
			}

			/**
			 * Process order status / email meta
			 *
			 * @param int $post_id
			 * @param \WP_Post $post
			 */
			do_action( "wc_order_status_manager_process_{$post->post_type}_meta", $post_id, $post );
		}
	}


	/**
	 * Disable autosave for the Order Status Manager post types
	 *
	 * @since 1.0.0
	 */
	public function disable_autosave() {
		global $typenow;

		if ( $this->is_order_status_manager_post_type( $typenow ) ) {
			wp_dequeue_script( 'autosave' );
		}
	}


	/**
	 * Check if the post type is either order status or order status email
	 *
	 * @since 1.0.0
	 * @param string $post_type
	 * @return bool
	 */
	private function is_order_status_manager_post_type( $post_type ) {
		return in_array( $post_type, array( 'wc_order_status', 'wc_order_email' ), true );
	}


	/**
	 * Check if the current screen is one of order status manager screens
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	private function is_order_status_manager_screen() {

		if ( ! function_exists( 'get_current_screen') ) {
			return false;
		}

		$screen = get_current_screen();

		return $screen && in_array( $screen->id, array(
			'wc_order_status',
			'edit-wc_order_status',
			'wc_order_email',
			'edit-wc_order_email',
		), true );
	}


}
