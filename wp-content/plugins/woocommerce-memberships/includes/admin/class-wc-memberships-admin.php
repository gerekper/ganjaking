<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\Memberships\Admin\Views\Modals\Profile_Field\Confirm_Deletion;
use SkyVerge\WooCommerce\Memberships\Admin\Views\Modals\User_Membership\Confirm_Edit_Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields;
use SkyVerge\WooCommerce\Memberships\Profile_Fields as Profile_Fields_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Main admin class.
 *
 * @since 1.0.0
 */
class WC_Memberships_Admin {


	/** @var Framework\SV_WP_Admin_Message_Handler instance */
	public $message_handler; // this is passed from \WC_Memberships and can't be protected

	/** @var \WC_Memberships_Admin_Import_Export_Handler instance */
	protected $import_export;

	/** @var Profile_Fields instance */
	private $profile_fields;

	/** @var \WC_Memberships_Admin_User_Memberships instance */
	protected $user_memberships;

	/** @var \WC_Memberships_Admin_Membership_Plans instance */
	protected $membership_plans;

	/** @var \WC_Memberships_Admin_Users instance */
	protected $users;

	/** @var \WC_Memberships_Admin_Orders instance */
	protected $orders;

	/** @var \WC_Memberships_Admin_Products instance */
	protected $products;

	/** @var stdClass container of modals instances */
	protected $modals;

	/** @var stdClass container of meta boxes instances */
	protected $meta_boxes;


	/**
	 * Init Memberships admin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load rules admin helper static class
		require_once( wc_memberships()->get_plugin_path() . '/includes/admin/class-wc-memberships-admin-membership-plan-rules.php' );

		// display admin messages
		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );

		// init settings page
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
		// email settings hooks
		add_action( 'woocommerce_email_settings_after', array( $this, 'handle_email_settings_pages' ) );
		// render Memberships admin tabs for pages with Memberships' own custom post types
		add_action( 'all_admin_notices', array( $this, 'render_tabs' ), 5 );
		// init content in Memberships tabbed admin pages
		add_action( 'current_screen', array( $this, 'init' ) );

		// init import/export page
		add_action( 'admin_menu', [ $this, 'add_import_export_admin_page' ] );
		// init profile fields page
		add_action( 'admin_menu', [ $this, 'add_profile_fields_admin_page' ] );

		// add additional bulk actions to memberships-restrictable post types
		// TODO when WordPress 4.7 is the minimum required version, this may be updated to use new hooks {FN 2018-11-05}
		add_action( 'admin_footer-edit.php', array( $this, 'add_restrictable_post_types_bulk_actions' ), 100 );
		add_action( 'load-edit.php',         array( $this, 'process_restrictable_post_types_bulk_actions' ), 100 );
		add_action( 'admin_notices',         array( $this, 'display_restrictable_post_types_bulk_actions_notices' ), 100 );

		// conditionally remove duplicate submenu link
		add_action( 'admin_menu', array( $this, 'remove_submenu_link' ) );
		// remove "New User Membership" item from Admin bar
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 9999 );

		// enqueue admin scripts & styles
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts_and_styles' ] );
		// load admin scripts & styles
		add_filter( 'woocommerce_screen_ids', [ $this, 'load_wc_scripts' ] );

		// add system status report data
		add_action( 'woocommerce_system_status_report', array( $this, 'add_system_status_report_block' ), 9 );
	}


	/**
	 * Gets the Message Handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return Framework\SV_WP_Admin_Message_Handler
	 */
	public function get_message_handler() {

		// note: this property is public since it needs to be passed from the main class
		return $this->message_handler;
	}


	/**
	 * Gets the Users admin handler instance.
	 *
	 * @since 1.7.4
	 *
	 * @return \WC_Memberships_Admin_Users
	 */
	public function get_users_instance() {

		return $this->users;
	}


	/**
	 * Gets the User Memberships admin handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Admin_User_Memberships
	 */
	public function get_user_memberships_instance() {

		return $this->user_memberships;
	}


	/**
	 * Gets the User Memberships admin handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Admin_Membership_Plans
	 */
	public function get_membership_plans_instance() {

		return $this->membership_plans;
	}


	/**
	 * Gets the Import / Export Handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Admin_Import_Export_Handler
	 */
	public function get_import_export_handler_instance() {

		return $this->import_export;
	}


	/**
	 * Gets the Profile Fields Handler instance.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Fields
	 */
	public function get_profile_fields_instance() {

		return $this->profile_fields;
	}


	/**
	 * Returns the products admin handler instance.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Memberships_Admin_Products
	 */
	public function get_products_instance() {
		return $this->products;
	}


	/**
	 * Returns the orders admin handler instance.
	 *
	 * @since 1.9.0
	 *
	 * @return \WC_Memberships_Admin_Orders
	 */
	public function get_orders_instance() {
		return $this->orders;
	}


	/**
	 * Returns Memberships admin screen IDs.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $context when a context is specified, screens for a particular group are displayed (default null: display all)
	 * @return string[] list of admin screen IDs where Memberships does something
	 */
	public function get_screen_ids( $context = null ) {

		$settings_page_id = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id();

		$tabs_screens = [
			// User Membership screens:
			'wc_user_membership',
			'edit-wc_user_membership',
			// Membership Plan screens:
			'wc_membership_plan',
			'edit-wc_membership_plan',
			// User Memberships Import/Export screens:
			'wc_memberships_import_export',
			'admin_page_wc_memberships_import_export',
			// Profile Fields screens:
			'admin_page_wc_memberships_profile_fields',
		];

		$modal_screens = [
			// User Membership screens:
			'wc_user_membership',
			'edit-wc_user_membership',
			// Membership Plan screens:
			'wc_membership_plan',
			'edit-wc_membership_plan',
			// WooCommerce Settings page tab
			$settings_page_id,
			// User Memberships Import/Export screens:
			'wc_memberships_import_export',
			'admin_page_wc_memberships_import_export',
			// Profile Fields screens:
			'admin_page_wc_memberships_profile_fields',
		];

		$scripts_screens = [
			// User screens:
			'users',
			'user-edit',
			'profile',
			// WooCommerce Settings page tab
			$settings_page_id,
			// WooCommerce system status
			'woocommerce_page_wc-status',
		];

		$meta_boxes_screens = [
			// User Membership screens:
			'wc_user_membership',
			'edit-wc_user_membership',
			// Membership Plan screens:
			'wc_membership_plan',
			'edit-wc_membership_plan',
		];

		if ( class_exists( 'WC_Memberships_Admin_Membership_Plan_Rules' ) ) {
			// post types edit screens, including products, where plan rules are applicable
			foreach ( array_keys( WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules( false ) ) as $post_type ) {
				$meta_boxes_screens[] = $post_type;
				$meta_boxes_screens[] = "edit-{$post_type}";
			}
		}

		/**
		 * Filters Memberships admin screen IDs.
		 *
		 * @since 1.9.0
		 *
		 * @param array $screen_ids associative array organized by context
		 */
		$screen_ids = (array) apply_filters( 'wc_memberships_admin_screen_ids', [
			'meta_boxes' => $meta_boxes_screens,
			'modals'     => $modal_screens,
			'scripts'    => array_merge( $tabs_screens, $scripts_screens, $meta_boxes_screens, $modal_screens ),
			'tabs'       => $tabs_screens,
		] );

		// return all screens or screens belonging to a particular group
		if ( null !== $context && isset( $screen_ids[ $context ] ) ) {
			$screen_ids = array_unique( $screen_ids[ $context ] );
		} else {
			$screens = [];
			foreach ( $screen_ids as $group => $ids ) {
				$screens += $ids;
			}
			$screen_ids = array_unique( $screens );
		}

		return $screen_ids;
	}


	/**
	 * Checks if we are on a Memberships admin screen.
	 *
	 * @since 1.6.0
	 *
	 * @param string $screen_id a screen ID to check - default blank, will try to determine the current admin screen
	 * @param string|string[] $which check for a specific screen type (or array of types) or leave 'any' to check if the current screen is one of the memberships screens
	 * @param bool $exclude_content if set to false (default) the check will exclude Memberships restrictable post types edit screens
	 * @return bool
	 */
	public function is_memberships_admin_screen( $screen_id = '', $which = 'any', $exclude_content = false ) {
		global $current_screen;

		$screen = empty( $screen_id ) ? $current_screen : $screen_id;

		if ( $screen instanceof \WP_Screen ) {
			$screen_id = $screen->id;
		}

		$is_screen = false;

		if ( is_string( $screen_id ) ) {

			$screen_ids = $this->get_screen_ids();

			if ( true === $exclude_content ) {
				unset( $screen_ids['content'] );
			}

			$is_screen = $screen_id && in_array( $screen_id, $screen_ids, true );

			if ( 'any' !== $which ) {
				if ( is_array( $which ) ) {
					$is_screen = in_array( $screen_id, $which, true );
				} else {
					$is_screen = $is_screen && $screen_id === $which;
				}
			}
		}

		return $is_screen;
	}


	/**
	 * Checks if the current screen is a Memberships import or export page.
	 *
	 * @since 1.9.0
	 *
	 * @param null|\WP_Screen|string $screen optional, defaults to current screen global
	 * @return bool
	 */
	public function is_memberships_import_export_admin_screen( $screen = null ) {

		return $this->is_memberships_admin_screen( $screen, 'admin_page_wc_memberships_import_export', true );
	}


	/**
	 * Checks if the current screen is a Memberships profile fields admin page.
	 *
	 * @since 1.19.0
	 *
	 * @param null|\WP_Screen|string $screen optional, defaults to current screen global
	 * @return bool
	 */
	public function is_memberships_profile_fields_admin_screen( $screen = null ) {

		return $this->is_memberships_admin_screen( $screen, 'admin_page_wc_memberships_profile_fields', true );
	}


	/**
	 * Checks if the current screen is a screen that contains a membership modal.
	 *
	 * @since 1.9.0
	 *
	 * @param null|\WP_Screen|string $screen optional, defaults to current screen global
	 * @return bool
	 */
	public function is_memberships_modal_admin_screen( $screen = null ) {

		return $this->is_memberships_admin_screen( $screen, $this->get_screen_ids( 'modals' ) );
	}


	/**
	 * Adds Memberships settings page to WooCommerce settings.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @return array
	 */
	public function add_settings_page( $settings ) {

		$settings[] = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-settings.php', 'WC_Settings_Memberships' );

		return $settings;
	}


	/**
	 * Ensures that there are no ongoing batch jobs when opening the emails settings pages.
	 *
	 * There can be only one batch job at any time so if a job was abandoned, but for some reason it wasn't cancelled, it can be cancelled before a new modal is opened.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function handle_email_settings_pages() {

		if ( isset( $_GET['tab'], $_GET['section'] ) && 'email' === $_GET['tab'] && in_array( $_GET['section'], array( 'wc_memberships_user_membership_ending_soon_email', 'wc_memberships_user_membership_renewal_reminder_email' ), true ) ) {

			$handler = wc_memberships()->get_utilities_instance()->get_user_memberships_reschedule_events_instance();

			if ( $current_job = $handler->get_job() ) {
				$handler->delete_job( $current_job->id );
			}
		}
	}


	/**
	 * Adds Import / Export page for Memberships admin page.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function add_import_export_admin_page() {

		/**
		 * Set minimum capability to use Import / Export features.
		 *
		 * @since 1.6.0
		 *
		 * @param string $capability defaults to Shop Managers with 'manage_woocommerce'
		 */
		$capability = (string) apply_filters( 'woocommerce_memberships_can_import_export', 'manage_woocommerce' );

		add_submenu_page(
			'',
			__( 'Import / Export', 'woocommerce-memberships' ),
			__( 'Import / Export', 'woocommerce-memberships' ),
			$capability,
			'wc_memberships_import_export',
			array( $this, 'render_import_export_admin_page' )
		);
	}


	/**
	 * Adds the Profile Fields page for Memberships admin page.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function add_profile_fields_admin_page() {

		/**
		 * Set minimum capability to use Profile Fields features.
		 *
		 * @since 1.19.0
		 *
		 * @param string $capability defaults to Shop Managers with 'manage_woocommerce'
		 */
		$capability = (string) apply_filters( 'woocommerce_memberships_can_manage_profile_fields', 'manage_woocommerce' );

		add_submenu_page(
			'',
			__( 'Profile Fields', 'woocommerce-memberships' ),
			__( 'Profile Fields', 'woocommerce-memberships' ),
			$capability,
			'wc_memberships_profile_fields',
			[ $this, 'render_profile_fields_page' ]
		);
	}


	/**
	 * Renders the Import / Export admin page.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function render_import_export_admin_page() {

		/**
		 * Outputs the Import / Export admin page.
		 *
		 * @since 1.6.0
		 */
		do_action( 'wc_memberships_render_import_export_page' );
	}


	/**
	 * Renders the Profile Fields admin page.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function render_profile_fields_page() {

		/**
		 * Outputs the Profile Fields admin page.
		 *
		 * @since 1.19.0
		 */
		do_action( 'wc_memberships_render_profile_fields_page' );
	}


	/**
	 * Gets a list of membership-related bulk actions applicable to restrictable post types.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $with_labels whether to return only ID keys (false) or include labels (true)
	 * @return string[]|array list of IDs or associative array of IDs and labels
	 */
	private function get_restrictable_post_types_bulk_actions( $with_labels = false ) {

		$bulk_actions = array(
			'wc_memberships_force_content_public'      => __( 'Disallow restrictions rules', 'woocommerce-memberships' ),
			'wc_memberships_dont_force_content_public' => __( 'Allow restriction rules', 'woocommerce-memberships' ),
		);

		return true === $with_labels ? $bulk_actions : array_keys( $bulk_actions );
	}


	/**
	 * Adds bulk actions to restrictable post types.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_restrictable_post_types_bulk_actions() {
		global $post_type;

		if ( $post_type && current_user_can( 'manage_woocommerce' ) ) :

			$post_type_name = $post_type instanceof \WP_Post_Type ? $post_type->name : $post_type;

			if ( array_key_exists( $post_type_name, WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules( true ) ) ) :

				?>
				<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						<?php foreach ( $this->get_restrictable_post_types_bulk_actions( true ) as $id => $label ) : ?>
							$( '<option>' ).val( '<?php echo esc_js( $id ); ?>' ).text( '<?php echo esc_js( $label ); ?>' ).appendTo( 'select[name="action"]' );
							$( '<option>' ).val( '<?php echo esc_js( $id ); ?>' ).text( '<?php echo esc_js( $label ); ?>' ).appendTo( 'select[name="action2"]' );
						<?php endforeach; ?>
					} );
				</script>
				<?php

			endif;

		endif;
	}


	/**
	 * Processes membership-related bulk actions for restrictable post types.
	 *
	 * TODO update this deprecated handling when WordPress 4.7 is the minimum required version {FN 2018-11-05}
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function process_restrictable_post_types_bulk_actions() {
		global $post_type;

		if ( $wp_list_table = _get_list_table( 'WP_Posts_List_Table' ) ) {

			$action       = $wp_list_table->current_action();
			$bulk_actions = $this->get_restrictable_post_types_bulk_actions();

			if ( $action && current_user_can( 'manage_woocommerce' ) && in_array( $action, $bulk_actions, true ) ) {

				$content_ids = isset( $_REQUEST['post'] ) ? array_map( 'intval', (array) $_REQUEST['post'] ) : array();
				$handled     = true;

				switch ( $action ) {
					case 'wc_memberships_force_content_public' :
						$processed = wc_memberships()->get_restrictions_instance()->set_content_public( $content_ids );
					break;
					case 'wc_memberships_dont_force_content_public' :
						$processed = wc_memberships()->get_restrictions_instance()->unset_content_public( $content_ids );
					break;
					default :
						$processed = 0;
						$handled   = false;
					break;
				}

				if ( $handled ) {

					// remove bulk actions set on the request URL
					$clean_original_url = remove_query_arg( array_merge( $bulk_actions, array( 'untrashed', 'deleted', 'ids', 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view' ) ), wp_get_referer() );

					if ( $clean_original_url ) {
						$processed_url = $clean_original_url;
					} else {
						$post_type_name = $post_type instanceof \WP_Post_Type ? $post_type->name : $post_type;
						$processed_url  = empty( $post_type_name ) ? admin_url( 'edit.php' ) : admin_url( "edit.php?post_type={$post_type_name}" );
					}

					if ( $processed_url ) {

						// re-add the processed bulk action and pagination information
						$redirect_url = add_query_arg( array(
							$action => $processed,
							'paged' => $wp_list_table->get_pagenum(),
						), $processed_url );

						// redirect to the products edit screen carrying bulk action results
						wp_redirect( $redirect_url );
						exit;
					}
				}
			}
		}
	}


	/**
	 * Displays an admin notice after a membership-related bulk action has been processed.
	 *
	 * TODO update this deprecated handling when WordPress 4.7 is the minimum required version {FN 2018-11-05}
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function display_restrictable_post_types_bulk_actions_notices() {
		global $post_type, $pagenow;

		if ( $post_type && 'edit.php' === $pagenow ) {

			$content_type            = $post_type instanceof \WP_Post_Type ? $post_type->name : $post_type;
			$restrictable_post_types = WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules( true );

			if ( array_key_exists( $content_type, $restrictable_post_types ) ) {

				$bulk_actions  = $this->get_restrictable_post_types_bulk_actions();
				$name_singular = $restrictable_post_types[ $content_type ]->labels->singular_name;
				$name_plural   = $restrictable_post_types[ $content_type ]->labels->name;
				$message       = '';

				foreach ( $bulk_actions as $bulk_action ) {

					if ( isset( $_GET[ $bulk_action ] ) ) {

						$processed = is_numeric( $_GET[ $bulk_action ] ) ? max( 0, (int) $_GET[ $bulk_action ] ) : 0;

						if ( 0 === $processed ) {

							switch ( $bulk_action ) {
								case 'wc_memberships_force_content_public' :
									/* translators: Placeholder: %s - post type name (plural) */
									$message .= sprintf( __( 'No %s have been marked as public.', 'woocommerce-memberships' ), $name_plural );
								break;
								case 'wc_memberships_dont_force_content_public' :
									/* translators: Placeholder: %s - post type name (plural) */
									$message .= sprintf( __( 'No %s have been unmarked as public.', 'woocommerce-memberships' ), $name_plural );
								break;
							}

						} else {

							switch ( $bulk_action ) {
								case 'wc_memberships_force_content_public' :
									/* translators: Placeholder: %1$s - processed items (number), %2$s - post type name (singular), %3$s - post type name (plural) */
									$message .= sprintf( _n( '%1$s %2$s has been marked as public and excluded from memberships restriction rules.', '%1$s %3$s have been marked as public and excluded from memberships restriction rules.', $processed, 'woocommerce-memberships' ), $processed, $name_singular, $name_plural );
								break;
								case 'wc_memberships_dont_force_content_public' :
									/* translators: Placeholder: %1$s - processed items (number), %2$s - post type name (singular), %3$s - post type name (plural) */
									$message .= sprintf( _n( '%1$s %2$s has been unmarked as public and will now follow any membership plan rules that may affect it.', '%1$s %3$s have been unmarked as public and will now follow any membership plan rules that may affect them.', $processed, 'woocommerce-memberships' ), $processed, $name_singular, $name_plural );
								break;
							}
						}
					}
				}

				if ( '' !== $message ) {
					// duplicate %1$s is intended, to have notice-warning to work properly
					printf( '<div class="notice notice-%1$s %1$s"><p>%2$s</p></div>', ! empty( $processed ) ? 'updated' : 'warning', esc_html( $message ) );
				}
			}
		}
	}


	/**
	 * Initializes the main admin screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( $screen = get_current_screen() ) {

			switch ( $screen->id ) {

				// subscriptions are correctly handled here as orders subclasses
				case 'shop_order' :
				case 'edit-shop_order' :
				case 'shop_subscription' :
				case 'edit-shop_subscription' :
					$this->orders = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-admin-orders.php', 'WC_Memberships_Admin_Orders' );
				break;

				case 'product' :
				case 'edit-product' :
					$this->products = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-admin-products.php', 'WC_Memberships_Admin_Products' );
				break;

				case 'wc_membership_plan' :
				case 'edit-wc_membership_plan' :
					$this->membership_plans = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-admin-membership-plans.php',  'WC_Memberships_Admin_Membership_Plans');
				break;

				case 'wc_user_membership' :
				case 'edit-wc_user_membership' :
					$this->user_memberships = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-admin-user-memberships.php',  'WC_Memberships_Admin_User_Memberships' );
					// the import / export handler runs bulk export on User Memberships screen
					$this->import_export = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-import-export-handler.php', 'WC_Memberships_Admin_Import_Export_Handler' );
				break;

				case 'admin_page_wc_memberships_import_export' :
					$this->import_export = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-import-export-handler.php', 'WC_Memberships_Admin_Import_Export_Handler' );
				break;

				case 'admin_page_wc_memberships_profile_fields' :
					$this->profile_fields = wc_memberships()->load_class( '/includes/admin/Profile_Fields.php', 'SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields' );
				break;

				case 'users' :
				case 'user-edit' :
				case 'profile' :
					$this->users = wc_memberships()->load_class( '/includes/admin/class-wc-memberships-admin-users.php', 'WC_Memberships_Admin_Users' );
				break;
			}

			// init modals in screens where they could be opened
			if ( in_array( $screen->id, $this->get_screen_ids( 'modals' ), true ) ) {
				$this->init_modals();
			}

			// init meta boxes on restrictable post types edit screens
			if ( in_array( $screen->id, $this->get_screen_ids( 'meta_boxes' ), true ) ) {
				$this->init_meta_boxes();
			}
		}
	}


	/**
	 * Loads and instantiates classes helper.
	 *
	 * @since 1.9.0
	 *
	 * @param string $prefix prefix of each class name
	 * @param array $files associative array of object names and relative file paths (to includes/admin)
	 * @return array
	 */
	private function init_objects( $prefix, array $files ) {

		$objects = [];

		foreach ( $files as $class => $path ) {

			// handle namespaced classes and their paths
			if ( Framework\SV_WC_Helper::str_starts_with( $class, '\\' ) ) {
				$class_parts = explode( '\\', $class );
				$file_name   = array_pop( $class_parts ) . '.php';
			} else {
				$file_name = 'class-'. strtolower( str_replace( '_', '-', $class ) ) . '.php';
			}

			$file_path = wc_memberships()->get_plugin_path() . $path . $file_name;

			if ( is_readable( $file_path ) && ! class_exists( $class ) ) {

				require_once( $file_path );

				if ( class_exists( $class ) ) {

					// handle namespaced class names
					if ( Framework\SV_WC_Helper::str_starts_with( $class, '\\' ) ) {
						$object_name = str_replace( '\\', '-', str_replace( '\\SkyVerge\\WooCommerce\\Memberships\\', '', $class ) );
					} else {
						$object_name = strtolower( str_replace( $prefix . '_', '', $class ) );
					}

					$objects[ $object_name ] = new $class();
				}
			}
		}

		return $objects;
	}


	/**
	 * Loads modal templates.
	 *
	 * @since 1.9.0
	 */
	private function init_modals() {

		if ( $screen = get_current_screen() ) {

			// load abstracts
			require_once( wc_memberships()->get_plugin_path() . '/includes/admin/modals/abstract-wc-memberships-modal.php' );
			require_once( wc_memberships()->get_plugin_path() . '/includes/admin/modals/abstract-wc-memberships-member-modal.php' );
			require_once( wc_memberships()->get_plugin_path() . '/includes/admin/modals/abstract-wc-memberships-batch-job-modal.php' );

			$this->modals   = new stdClass();
			$modals_classes = [];

			// new user membership screen
			if ( 'edit-wc_user_membership' === $screen->id ) {
				$modals_classes['WC_Memberships_Modal_Add_User_Membership'] = '/includes/admin/modals/';
				$modals_classes['WC_Memberships_Modal_Import_Export_User_Memberships'] = '/includes/admin/modals/';
			// edit user membership screen
			} elseif ( 'wc_user_membership' === $screen->id ) {
				$modals_classes['WC_Memberships_Modal_Add_User_Membership'] = '/includes/admin/modals/';
				$modals_classes['WC_Memberships_Modal_Transfer_User_Membership'] = '/includes/admin/modals/';
				$modals_classes['WC_Memberships_Modal_Import_Export_User_Memberships'] = '/includes/admin/modals/';
				$modals_classes[ '\\' . Confirm_Edit_Profile_Fields::class ] = '/includes/admin/Views/Modals/User_Membership/';
			// membership plan screens
			} elseif ( in_array( $screen->id, array( 'wc_membership_plan', 'edit-wc-membership-plan' ), true ) ) {
				$modals_classes['WC_Memberships_Modal_Grant_Access_Membership_Plan'] = '/includes/admin/modals/';
			// user memberships import/export screens
			} elseif ( 'admin_page_wc_memberships_import_export' === $screen->id ) {
				$modals_classes['WC_Memberships_Modal_Import_Export_User_Memberships'] = '/includes/admin/modals/';
			// email settings screens
			} elseif ( isset( $_GET['tab'], $_GET['section'] ) && 'email' === $_GET['tab'] && in_array( $_GET['section'], array( 'wc_memberships_user_membership_ending_soon_email', 'wc_memberships_user_membership_renewal_reminder_email' ), true ) && Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id() === $screen->id ) {
				$modals_classes['WC_Memberships_Modal_Reschedule_User_Memberships_Events'] = '/includes/admin/modals/';

			// profile fields screens
			} elseif ( $this->get_profile_fields_instance() && $this->get_profile_fields_instance()->is_profile_fields_screen( [ Profile_Fields::SCREEN_ACTION_NEW, Profile_Fields::SCREEN_ACTION_EDIT ] ) ) {

				$modals_classes[ '\\' . Confirm_Deletion::class ] = '/includes/admin/Views/Modals/Profile_Field/';
			}

			// load and instantiate objects
			$modals = $this->init_objects( 'WC_Memberships_Modal', $modals_classes );

			/**
			 * Filter Memberships admin modals.
			 *
			 * @since 1.9.0
			 *
			 * @param \WC_Memberships_Modal[] $modals an associative array of modals names and instances
			 * @param \WP_Screen $screen the current screen
			 */
			$modals = apply_filters( 'wc_memberships_modals', $modals, $screen );

			foreach ( $modals as $modal_name => $modal_object ) {
				if ( ! empty( $modal_name ) ) {
					$this->modals->$modal_name = $modal_object;
				}
			}
		}
	}


	/**
	 * Loads meta boxes.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	private function init_meta_boxes() {
		global $pagenow;

		$screen = get_current_screen();

		// bail out if not on a new post / edit post screen
		if ( ! $screen || ( 'post-new.php' !== $pagenow && 'post.php' !== $pagenow ) ) {
			return;
		}

		$meta_box_classes = [];

		// load meta boxes abstract class
		if ( ! class_exists( 'WC_Memberships_Meta_Box' ) ) {
			require_once( wc_memberships()->get_plugin_path() . '/includes/admin/meta-boxes/abstract-wc-memberships-meta-box.php' );
		}

		$this->meta_boxes = new stdClass();

		// load restriction meta boxes on post screen only
		$meta_box_classes[ 'WC_Memberships_Meta_Box_Post_Memberships_Data'] = '/includes/admin/meta-boxes/';

		// product-specific meta boxes
		if ( 'product' === $screen->id ) {
			$meta_box_classes['WC_Memberships_Meta_Box_Product_Memberships_Data'] = '/includes/admin/meta-boxes/';
		}

		// load user membership meta boxes on user membership screen only
		if ( 'wc_membership_plan' === $screen->id ) {
			$meta_box_classes['WC_Memberships_Meta_Box_Membership_Plan_Data'] = '/includes/admin/meta-boxes/';
			$meta_box_classes['WC_Memberships_Meta_Box_Membership_Plan_Email_Content_Merge_Tags'] = '/includes/admin/meta-boxes/';
		}

		// load user membership meta boxes on user membership screen only
		if ( 'wc_user_membership' === $screen->id ) {
			$meta_box_classes['WC_Memberships_Meta_Box_User_Membership_Data'] = '/includes/admin/meta-boxes/';
			$meta_box_classes['WC_Memberships_Meta_Box_User_Membership_Notes'] = '/includes/admin/meta-boxes/';
			$meta_box_classes['WC_Memberships_Meta_Box_User_Membership_Member_Details'] = '/includes/admin/meta-boxes/';
			$meta_box_classes['\\SkyVerge\\WooCommerce\\Memberships\\Admin\\Views\\Meta_Boxes\\User_Membership\\Profile_Fields'] = '/includes/admin/Views/Meta_Boxes/User_Membership/';
			$meta_box_classes['WC_Memberships_Meta_Box_User_Membership_Recent_Activity' ] = '/includes/admin/meta-boxes/';
		}

		// load and instantiate objects
		$meta_boxes = $this->init_objects( 'WC_Memberships_Meta_Box', $meta_box_classes );

		/**
		 * Filter Memberships admin meta boxes.
		 *
		 * @since 1.9.0
		 *
		 * @param \WC_Memberships_Meta_Box[] $meta_boxes an associative array of meta boxes names and instances
		 * @param \WP_Screen $screen the current screen
		 */
		$meta_boxes = apply_filters( 'wc_memberships_meta_boxes', $meta_boxes, $screen );

		foreach ( $meta_boxes as $meta_box_name => $meta_box_object ) {
			$this->meta_boxes->$meta_box_name = $meta_box_object;
		}
	}


	/**
	 * Returns meta boxes instances.
	 *
	 * @since 1.0.0
	 *
	 * @return \stdClass object containing \WC_Memberships_Meta_Box instances for properties
	 */
	public function get_meta_boxes() {
		return $this->meta_boxes;
	}


	/**
	 * Returns the admin meta box IDs.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] array of meta box IDs
	 */
	public function get_meta_box_ids() {

		$ids = array();

		foreach ( (array) $this->get_meta_boxes() as $meta_box ) {
			$ids[] = $meta_box->get_id();
		}

		return $ids;
	}


	/**
	 * Returns modals instances.
	 *
	 * @since 1.9.0
	 *
	 * @return \stdClass object containing instances of \WC_Memberships_Modal for properties
	 */
	public function get_modals() {
		return $this->modals;
	}


	/**
	 * Enqueues admin scripts & styles conditionally.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts_and_styles() {

		$screen = get_current_screen();

		if ( $screen && in_array( $screen->id, $this->get_screen_ids( 'scripts' ), true ) ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();
		}
	}


	/**
	 * Enqueues admin styles.
	 *
	 * @since 1.8.0
	 */
	private function enqueue_styles() {

		$path = wc_memberships()->get_plugin_url() . '/assets/css/admin/';
		$deps = [];

		wp_register_style( 'wc-memberships-user-memberships', $path . 'wc-memberships-user-memberships.min.css', [], \WC_Memberships::VERSION );
		wp_register_style( 'wc-memberships-profile-fields', $path . 'wc-memberships-profile-fields.min.css', [], \WC_Memberships::VERSION );

		if ( $this->is_memberships_profile_fields_admin_screen() ) {
			$deps[] = 'wc-memberships-profile-fields';
		}

		if ( $this->is_memberships_admin_screen( 'edit-wc_user_membership' ) || $this->is_memberships_admin_screen( 'wc_user_membership' ) ) {
			$deps[] = 'wc-memberships-user-memberships';
		}

		wp_enqueue_style( 'wc-memberships-admin', $path . 'wc-memberships-admin.min.css', $deps, \WC_Memberships::VERSION );
	}


	/**
	 * Enqueues admin scripts conditionally.
	 *
	 * @since 1.8.0
	 */
	private function enqueue_scripts() {
		global $pagenow, $typenow;

		$screen   = get_current_screen();
		$path     = wc_memberships()->get_plugin_url() . '/assets/js/admin/';
		$ver      = \WC_Memberships::VERSION;

		// base scripts
		wp_register_script( 'wc-memberships-enhanced-select', $path . 'wc-memberships-enhanced-select.min.js', [ 'jquery', 'select2' ], $ver );
		wp_register_script( 'wc-memberships-rules',           $path . 'wc-memberships-rules.min.js',           [ 'wc-memberships-enhanced-select' ], $ver );
		wp_register_script( 'wc-memberships-modal',           $path . 'wc-memberships-modal.min.js',           [ 'jquery', 'backbone', 'wc-backbone-modal'], $ver );
		wp_register_script( 'wc-memberships-modals',          $path . 'wc-memberships-member-modals.min.js',   [ 'wc-memberships-modal', 'wc-memberships-enhanced-select' ], $ver );
		wp_register_script( 'wc-memberships-profile-fields',  $path . 'wc-memberships-profile-fields.min.js',  [ 'wc-memberships-modals', 'jquery-ui-sortable' ], $ver );
		wp_enqueue_script(  'wc-memberships-admin',           $path . 'wc-memberships-admin.min.js',           [ 'wc-memberships-rules' ], $ver );

		// plans edit screens
		if ( $screen && in_array( $screen->id, [ 'wc_membership_plan', 'edit-wc_membership_plan' ], false ) ) {

			wp_enqueue_script( 'wc-memberships-membership-plans', $path . 'wc-memberships-plans.min.js', array( 'wc-memberships-admin', 'wc-memberships-modal', 'jquery-ui-datepicker' ), $ver );

		// user memberships screens and import export screens
		} elseif ( $screen && in_array( $screen->id, [ 'admin_page_wc_memberships_import_export', 'wc_user_membership', 'edit-wc_user_membership' ], false ) ) {

			// user memberships screens only
			if ( in_array( $screen->id, [ 'wc_user_membership', 'edit-wc_user_membership' ], false ) ) {

				wp_enqueue_script( 'wc-memberships-user-memberships', $path . 'wc-memberships-user-memberships.min.js', [ 'wc-memberships-modals', 'jquery-ui-datepicker' ], $ver );
			}

			// export scripts are also loaded on the memberships edit screen for bulk exports
			wp_enqueue_script( 'wc-memberships-import-export', $path . 'wc-memberships-import-export.min.js', [ 'wc-memberships-modal', 'wc-memberships-enhanced-select', 'jquery-ui-datepicker' ], $ver );

		// product screens
		} elseif ( $screen && in_array( $screen->id, [ 'product', 'edit-product' ], true ) ) {

			wp_enqueue_script( 'wc-memberships-modals' );


		// profile fields screens
		} elseif ( $this->is_memberships_profile_fields_admin_screen() ) {

			if ( $profile_fields = $this->get_profile_fields_instance() ) {
				$profile_field_definition = $profile_fields->get_admin_screen_profile_field_definition();
				$profile_field_in_use     = $profile_field_definition && $profile_field_definition->is_in_use();
			}

			wp_enqueue_script( 'wc-memberships-profile-fields' );

		// settings pages, including memberships emails settings
		} elseif ( wc_memberships()->is_plugin_settings() ) {

			wp_enqueue_script( 'wc-memberships-settings', $path . 'wc-memberships-settings.min.js', [ 'wc-memberships-modal', 'wc-memberships-enhanced-select', 'jquery-ui-datepicker' ], $ver );
		}

		$profile_field_definition_options = [];

		if ( 'edit.php' === $pagenow && 'wc_user_membership' === $typenow ) {

			foreach ( Profile_Fields_Handler::get_profile_field_definitions() as $profile_field_definition ) {

				if ( ! $profile_field_definition->has_options() ) {
					continue;
				}

				// converts the slug into a format that can be used as a javascript property
				$slug = str_replace( '-', '_', $profile_field_definition->get_slug( 'edit' ) );

				// maps the profile field options
				$profile_field_definition_options[ $slug ] = array_values( $profile_field_definition->get_options( 'edit' ) );
			}
		}

		// localize the main admin script to add variable properties and localization strings.
		wp_localize_script( 'wc-memberships-admin', 'wc_memberships_admin', [

			// add any config/state properties here, for example:
			// 'is_user_logged_in' => is_user_logged_in()

			'ajax_url'                                  => admin_url( 'admin-ajax.php' ),
			'new_membership_url'                        => admin_url( 'post-new.php?post_type=wc_user_membership' ),
			'wc_plugin_url'                             => WC()->plugin_url(),
			'calendar_image'                            => WC()->plugin_url() . '/assets/images/calendar.png',
			'user_membership_url'                       => admin_url( 'edit.php?post_type=wc_user_membership' ),
			'new_user_membership_url'                   => admin_url( 'post-new.php?post_type=wc_user_membership' ),
			'restrictable_post_types'                   => array_keys( WC_Memberships_Admin_Membership_Plan_Rules::get_valid_post_types_for_content_restriction_rules( false ) ),
			'profile_fields_visibility_options'         => Profile_Fields_Handler::get_profile_fields_visibility_options( true ),
			'profile_field_is_in_use'                   => ! empty( $profile_field_in_use ),
			'profile_field_definitions_options'         => $profile_field_definition_options,
			'search_products_nonce'                     => wp_create_nonce( 'search-products' ),
			'search_posts_nonce'                        => wp_create_nonce( 'search-posts' ),
			'search_terms_nonce'                        => wp_create_nonce( 'search-terms' ),
			'get_membership_date_nonce'                 => wp_create_nonce( 'get-membership-date' ),
			'search_customers_nonce'                    => wp_create_nonce( 'search-customers' ),
			'add_user_membership_note_nonce'            => wp_create_nonce( 'add-user-membership-note' ),
			'create_user_for_membership_nonce'          => wp_create_nonce( 'create-user-for-membership' ),
			'transfer_user_membership_nonce'            => wp_create_nonce( 'transfer-user-membership' ),
			'toggle_profile_field_editable_by_nonce'    => wp_create_nonce( 'toggle-profile-field-editable-by' ),
			'save_profile_fields_nonce'                 => wp_create_nonce( 'save-profile-fields' ),
			'sort_profile_fields_nonce'                 => wp_create_nonce( 'sort-profile-fields' ),
			'delete_user_membership_note_nonce'         => wp_create_nonce( 'delete-user-membership-note' ),
			'delete_user_membership_subscription_nonce' => wp_create_nonce( 'delete-user-membership-with-subscription' ),
			'get_memberships_batch_job_nonce'           => wp_create_nonce( 'get-memberships-batch-job' ),
			'remove_memberships_batch_job_nonce'        => wp_create_nonce( 'remove-memberships-batch-job' ),
			'grant_retroactive_access_nonce'            => wp_create_nonce( 'grant-retroactive-access' ),
			'reschedule_user_memberships_events_nonce'  => wp_create_nonce( 'reschedule-user-memberships-events' ),
			'export_user_memberships_nonce'             => wp_create_nonce( 'export-user-memberships' ),
			'import_user_memberships_nonce'             => wp_create_nonce( 'import-user-memberships' ),

			'i18n' => [

				// add i18n strings here, for example:
				// 'log_in' => __( 'Log In', 'woocommerce-memberships' )

				'delete_membership_confirm'                 => __( 'Are you sure that you want to permanently delete this membership?', 'woocommerce-memberships' ),
				'delete_memberships_confirm'                => __( 'Are you sure that you want to permanently delete these memberships?', 'woocommerce-memberships' ),
				'please_select_user'                        => __( 'Please select a user.', 'woocommerce-memberships' ),
				'reschedule'                                => __( 'Reschedule', 'woocommerce-memberships' ),
				'export_user_memberships'                   => __( 'Export User Memberships', 'woocommerce-memberships' ),
				'import_file_missing'                       => __( 'Please upload a file to import memberships from.', 'woocommerce-memberships' ),
				'confirm_export_cancel'                     => __( 'Are you sure you want to cancel this export?', 'woocommerce-memberships' ),
				'confirm_import_cancel'                     => __( 'Are you sure you want to cancel this import?', 'woocommerce-memberships' ),
				'confirm_stop_batch_job'                    => __( 'Are you sure you want to stop the current batch process?', 'woocommerce-memberships' ),
				'blanket_rule_warning'                      => __( 'One or more of your rules uses a blank "Title" field - blank rules apply the rule to all content. This may restrict all content or products, or offer discounts on all products.', 'woocommerce-memberships' ),
				'blanket_content_restriction_rule'          => __( 'On the Restrict Content tab, all content under the specified post type or taxonomy will be restricted to plan members', 'woocommerce-memberships' ),
				'blanket_product_restriction_rule'          => __( 'On the Restrict Products tab, all products will be restricted to plan members', 'woocommerce-memberships' ),
				'blanket_product_discount_rule'             => __( 'On the Purchasing Discounts tab, all products will have a discount for plan members', 'woocommerce-memberships' ),
				'blanket_rule_confirmation'                 => __( 'Please confirm to save the rules or cancel to review without saving.', 'woocommerce-memberships' ),
				'profile_field_no_visibility'               => __( 'The profile field should have visibility preferences if editable by a member.', 'woocommerce-memberships' ),
				'profile_field_filter_comparators'          => [
					'is'             => _x( 'is', 'Comparator: <value> is <something>', 'woocommerce-memberships' ),
					'is_not'         => _x( 'is not', 'Comparator: <value> is not <something>', 'woocommerce-memberships' ),
					'includes'       => _x( 'includes', 'Comparator: <value> includes <something>',  'woocommerce-memberships' ),
					'doesnt_include' => _x( "doesn't include", "Comparator: <value> doesn't include <something>", 'woocommerce-memberships' ),
					'is_empty'       => _x( 'is empty', 'Comparator: <value> is empty', 'woocommerce-memberships' ),
				],
				'profile_field_filter_checkbox'             => [
					'yes'            => _x( 'selected', 'Checkbox field status', 'woocommerce-memberships' ),
					'no'             => _x( 'unselected', 'Checkbox field status', 'woocommerce-memberships' ),
				],
				'profile_field_filter_placeholder_single'   => __( 'Select one option', 'woocommerce-memberships' ),
				'profile_field_filter_placeholder_multiple' => __( 'Select one or more options', 'woocommerce-memberships' ),

			],
		] );
	}


	/**
	 * Adds settings/export screen ID to the list of pages for WC to load its JS on.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {
		return array_merge( $screen_ids, $this->get_screen_ids( 'scripts' ) );
	}


	/**
	 * Adds a system status report block to the WooCommerce status page.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_system_status_report_block() {

		?>
		<table id="wc-memberships" class="wc_status_table widefat" cellspacing="0">

			<thead>
				<tr>
					<th colspan="3" data-export-label="Memberships">
						<h2><?php esc_html_e( 'Memberships', 'woocommerce-memberships' ); ?> <?php echo wc_help_tip( __( 'This section shows troubleshooting information for WooCommerce Memberships.', 'woocommerce-memberships' ) ); ?></h2>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php

				foreach ( \SkyVerge\WooCommerce\Memberships\System_Status_Report::get_system_status_report_data() as $export_key => $data ) :

					if ( ! empty( $data['label'] ) && ! empty( $data['html'] ) ) :

						?>
						<tr data-export-label="<?php echo esc_html( $export_key ); ?>">
							<td><?php echo esc_html( $data['label'] ); ?>:</td>
							<td class="help"><?php echo ! empty( $data['help'] ) ? wc_help_tip( $data['help'] ) : '&nbsp;' ?></td>
							<td><?php echo wp_kses_post( $data['html'] ); ?></td>
						</tr>
						<?php

					endif;

				endforeach;

				?>
			</tbody>

		</table>
		<?php
	}


	/**
	 * Removes the duplicate submenu link for Memberships custom post type that is not being viewed.
	 *
	 * It's easier to add both submenu links via register_post_type() and conditionally remove them here than it is try to add them both correctly.
	 *
	 * @internal
	 *
	 * @since 1.2.0
	 */
	public function remove_submenu_link() {
		global $pagenow, $typenow;

		$submenu_slug = 'edit.php?post_type=wc_membership_plan';

		// remove user membership submenu page when viewing or editing membership plans
		if (    ( 'edit.php' === $pagenow && 'wc_membership_plan' === $typenow )
		     || ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'wc_membership_plan' === get_post_type( $_GET['post'] ) ) ) {

			$submenu_slug = 'edit.php?post_type=wc_user_membership';
		}

		remove_submenu_page( 'woocommerce', $submenu_slug );
	}


	/**
	 * Returns the current admin tab.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $current_tab current tab slug, defaults to user memberships
	 * @return string
	 */
	public function get_current_tab( $current_tab = 'members' ) {

		if ( $screen = get_current_screen() ) {
			if ( in_array( $screen->id, array( 'wc_membership_plan', 'edit-wc_membership_plan' ), true ) ) {
				$current_tab = 'memberships';
			} elseif ( in_array( $screen->id, array( 'wc_user_membership', 'edit-wc_user_membership' ), true ) ) {
				$current_tab = 'members';
			} elseif ( $this->is_memberships_import_export_admin_screen() ) {
				$current_tab = 'import-export';
			} elseif( $this->is_memberships_profile_fields_admin_screen() ) {
				$current_tab = 'profile-fields';
			}
		}

		/**
		 * Filters the current Memberships Admin tab.
		 *
		 * @since 1.0.0
		 *
		 * @param string $current_tab the current tab (defaults to 'members')
		 * @param \WP_Screen $screen the current screen
		 */
		return (string) apply_filters( 'wc_memberships_admin_current_tab', $current_tab, $screen );
	}


	/**
	 * Renders tabs on our custom post types pages.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function render_tabs() {
		global $post, $typenow;

		$screen = get_current_screen();

		// handle tabs on the relevant WooCommerce pages
		if ( $screen && in_array( $screen->id, $this->get_screen_ids( 'tabs' ), true ) ) :

			$tabs = apply_filters( 'wc_memberships_admin_tabs', [
				'members'        => [
					'title' => __( 'Members', 'woocommerce-memberships' ),
					'url'   => admin_url( 'edit.php?post_type=wc_user_membership' ),
				],
				'memberships'    => [
					'title' => wp_is_mobile() ? __( 'Plans', 'woocommerce-memberships' ) : __( 'Membership Plans', 'woocommerce-memberships' ),
					'url'   => admin_url( 'edit.php?post_type=wc_membership_plan' ),
				],
				'import-export'  => [
					'title' => __( 'Import / Export', 'woocommerce-memberships' ),
					'url'   => admin_url( 'admin.php?page=wc_memberships_import_export' ),
				],
				'profile-fields' => [
					'title' => __( 'Profile Fields', 'woocommerce-memberships' ),
					'url'   => admin_url( 'admin.php?page=wc_memberships_profile_fields' ),
				],
			] );

			if ( is_array( $tabs ) ) :

				?>
				<div class="wrap woocommerce">
					<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
						<?php $current_tab = $this->get_current_tab(); ?>
						<?php $current_tab = 'members' === $current_tab && 'admin_page_wc_memberships_import_export' === $screen->id ? 'import-export' : $current_tab; ?>
						<?php foreach ( $tabs as $tab_id => $tab ) : ?>
							<?php $class = $tab_id === $current_tab ? array( 'nav-tab', 'nav-tab-active' ) : array( 'nav-tab' ); ?>
							<?php printf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $tab['url'] ), implode( ' ', array_map( 'sanitize_html_class', $class ) ), esc_html( $tab['title'] ) ); ?>
						<?php endforeach; ?>
					</h2>
				</div>
				<?php

			endif;

		// warn users against the usage of 'woocommerce_my_account' deprecated shortcode attributes as these could conflict with Memberships and trigger a server error in the Members Area
		elseif ( 'page' === $typenow && ( $post && ( (int) $post->ID === (int) wc_get_page_id( 'myaccount' ) || has_shortcode( $post->post_content, 'woocommerce_my_account' ) ) ) ) :

			preg_match_all('/' . get_shortcode_regex() .'/s', $post->post_content, $matches );

			if ( isset( $matches[2], $matches[3] ) && ( is_array( $matches[2] ) && is_array( $matches[3] ) ) ) {

				$position = null;

				foreach ( $matches[2] as $key => $found_shortcode ) {
					if ( 'woocommerce_my_account' === $found_shortcode ) {
						$position = $key;
						break;
					}
				}

				if ( null !== $position && ! empty( $matches[3][ $position ] ) ) {

					$has_atts = trim( $matches[3][ $position ] );

					if ( ! empty( $has_atts ) ) {

						?>
						<div class="notice notice-warning">
							<p><?php
								/* translators: Placeholders: %1$s - the 'woocommerce_my_account' shortcode, %2$s - the 'order_count' shortcode attribute */
								printf( __( 'It looks like you might be using the %1$s shortcode with deprecated attributes, such as %2$s. These attributes have been deprecated since WooCommerce 2.6 and may no longer have any effect on the shortcode output. Furthermore, they might cause a server error when visiting the Members Area while WooCommerce Memberships is active.', 'woocommerce-memberships' ), '<code>woocommerce_my_account</code>', '<code>order_count</code>' );
								?></p>
						</div>
						<?php
					}
				}
			}

		endif;
	}


	/**
	 * Displays admin messages.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function show_admin_messages() {
		global $current_screen;

		// maybe add an informational notice about Jilt member emails
		if (
			     $current_screen
			&&   isset( $_GET['tab'], $_GET['section'] )
			&&   $_GET['tab'] === 'email'
			&&   $current_screen->id === Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id()
			&& ! wc_memberships()->get_integrations_instance()->is_jilt_active()
		) {

			foreach ( wc_memberships()->get_emails_instance()->get_email_class_names() as $membership_email_cass ) {

				$membership_email_id = strtolower( $membership_email_cass );

				if ( $membership_email_id === $_GET['section'] ) {

					wc_memberships()->get_admin_notice_handler()->add_admin_notice(
						sprintf(
							/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
							__( 'Send more member emails, including welcome series, using the Jilt integration for Memberships. %1$sLearn more &raquo;%2$s.', 'woocommerce-memberships' ),
							'<a href="https://jilt.com/go/memberships-email-notice">', '</a>'
						),
						'wc-memberships-emails-jilt-member-emails',
						[
							'notice_class'            => 'notice-info',
							'dismissible'             => true,
							'always_show_on_settings' => false,
						]
					);

					break;
				}
			}
		}

		$this->message_handler->show_messages();
	}


	/**
	 * Removes New User Membership menu option from Admin Bar.
	 *
	 * @internal
	 *
	 * @since 1.3.0
	 *
	 * @param \WP_Admin_Bar $admin_bar WP_Admin_Bar instance, passed by reference
	 */
	public function admin_bar_menu( $admin_bar ) {

		$admin_bar->remove_menu( 'new-wc_user_membership' );
	}


}
