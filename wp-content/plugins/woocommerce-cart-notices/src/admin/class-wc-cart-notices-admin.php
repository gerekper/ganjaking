<?php
/**
 * WooCommerce Cart Notices
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cart Notices to newer
 * versions in the future. If you wish to customize WooCommerce Cart Notices for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-cart-notices/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\Admin\Features\Navigation\Menu as Enhanced_Navigation_Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen as Enhanced_Navigation_Screen;
use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Cart Notices Admin Class.
 *
 * @since 1.0.7
 */
class WC_Cart_Notices_Admin {


	/** @var Framework\SV_WP_Admin_Message_Handler message handler instance */
	public $message_handler;

	/** @var array notice types to display name */
	public $notice_types;


	/**
	 * Construct and initialize the admin class
	 *
	 * @since 1.0.7
	 */
	public function __construct() {

		$this->notice_types = $this->get_notice_types();

		// load WC styles / scripts
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_styles_scripts' ) );

		// add menu item to WooCommerce menu
		add_action( 'admin_menu', [ $this, 'add_menu_item' ] );
		// add menu item to WooCommerce enhanced navigation menu
		add_action( 'admin_menu', [ $this, 'add_enhanced_navigation_menu_items' ] );
		// remove the menu item migrated by WooCommerce admin to prevent a conflict
		add_action( 'woocommerce_navigation_menu_items', [ $this, 'remove_enhanced_navigation_menu_item' ] );

		// handle the create/edit actions
		add_action( 'admin_post_cart_notice_new', array( $this, 'create_cart_notice' ) );
		add_action( 'admin_post_cart_notice_edit', array( $this, 'update_cart_notice' ) );

		// Handle the enable/disable/delete actions.
		add_action( 'admin_init', array( $this, 'toggle_cart_notice' ) );

		// add uninstall option
		add_filter( 'woocommerce_general_settings', array( $this, 'add_global_settings' ) );
	}


	/**
	 * Gets the notice types.
	 *
	 * @since 1.1.1-dev.1
	 *
	 * @return array
	 */
	public function get_notice_types() {

		return [
			'minimum_amount' => __( 'Minimum Amount', 'woocommerce-cart-notices' ),
			'deadline'       => __( 'Deadline', 'woocommerce-cart-notices' ),
			'referer'        => __( 'Referer', 'woocommerce-cart-notices' ),
			'products'       => __( 'Products in Cart', 'woocommerce-cart-notices' ),
			'categories'     => __( 'Categories in Cart', 'woocommerce-cart-notices' ),
		];
	}


	/**
	 * Add notice screen ID to the list of pages for WC to load its CSS/JS on
	 *
	 * @since 1.0.7
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_styles_scripts( $screen_ids ) {

		$screen_ids[] = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc-cart-notices' );

		return $screen_ids;
	}


	/**
	 * Validate options, called after cart notice create/edit
	 *
	 * @since 1.0.7
	 */
	private function validate_options() {
		global $wpdb;

		// new cart notice must have a valid notice type selected
		if ( 'cart_notice_new' === $_POST['action'] ) {

			$notice_types = $this->get_notice_types();

			if ( ! $_POST['notice_type'] || ! isset( $notice_types[ $_POST['notice_type'] ] ) ) {
				$this->message_handler->add_error( __( 'You must choose a Notice Type', 'woocommerce-cart-notices' ) );
			}
		}

		// notice name is required
		if ( ! $_POST['notice_name'] ) {
			$this->message_handler->add_error( __( 'You must provide a Notice Name', 'woocommerce-cart-notices' ) );
		}

		// notice name already in use?
		if ( 'cart_notice_new' === $_POST['action'] ) {
			$name_exists_query = $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cart_notices WHERE name = %s", $this->get_requested_value( 'notice_name' ) );
		} elseif ( 'cart_notice_edit' === $_POST['action'] ) {
			$name_exists_query = $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}cart_notices WHERE name = %s and id != %d",
			                                     $this->get_requested_value( 'notice_name' ), $this->get_requested_value( 'id' ) );
		}
		if ( $wpdb->get_var( $name_exists_query ) ) {
			$this->message_handler->add_error( __( 'That name is already in use', 'woocommerce-cart-notices' ) );
		}

		// validate target amount, if set
		if ( $_POST['minimum_order_amount'] && ( ! is_numeric( $_POST['minimum_order_amount'] ) || (float) $_POST['minimum_order_amount'] < 0 ) ) {
			$this->message_handler->add_error( __( 'Target amount must be positive number, or empty', 'woocommerce-cart-notices' ) );
		}

		// validate threshold amount, if set
		if ( $_POST['threshold_order_amount'] && ( ! is_numeric( $_POST['threshold_order_amount'] ) || (float) $_POST['threshold_order_amount'] < 0 ) ) {
			$this->message_handler->add_error( __( 'Threshold amount must be positive number, or empty', 'woocommerce-cart-notices' ) );
		}

		// validate deadline hour, if set
		if ( $_POST['deadline_hour'] && ( ! is_numeric( $_POST['deadline_hour'] ) || (int) $_POST['deadline_hour'] < 1 || (int) $_POST['deadline_hour'] > 24 ) ) {
			$this->message_handler->add_error( __( 'Deadline hour must be in 24-hour format, between 1 to 24', 'woocommerce-cart-notices' ) );
		}

		/**
		 * Fires after notice options have been validated.
		 * Notify Diego Z if this changes {BR 2016-11-28}
		 *
		 * @since 1.6.1
		 * @param array $_POST notice options data
		 * @param \WC_Cart_Notices_Admin $admin_instance the admin class instance
		 */
		do_action( 'wc_cart_notices_validate_options', $_POST, $this );
	}


	/**
	 * Action to create a new cart notice
	 *
	 * @since 1.0.7
	 */
	public function create_cart_notice() {

		$this->handle_create_update_cart_notice( 'create' );
	}


	/**
	 * Action to update an existing cart notice
	 *
	 * @since 1.0.7
	 */
	public function update_cart_notice() {

		$this->handle_create_update_cart_notice( 'update' );
	}


	/**
	 * Helper function to perform the create/update cart notice actions
	 *
	 * @since 1.0.7
	 * @param string $action one of 'create' or 'update'
	 * @return bool
	 */
	private function handle_create_update_cart_notice( $action ) {
		global $wpdb;

		$this->validate_options();

		if ( $this->message_handler->error_count() > 0 ) {

			// If there are validation errors, send the data back to the create notice page so the user can fix the issue
			// note that we have to serialize the arrayed values because wordpress redirect messes with the normal way of sending arrays through URL parameters
			$query_params = array(
				'page'                   => wc_cart_notices()->id,
				'tab'                    => 'create' === $action ? 'new' : 'edit',
				'notice_name'            => urlencode( $this->get_requested_value( 'notice_name' ) ),
				'notice_enabled'         => urlencode( $this->get_requested_value( 'notice_enabled' ) ),
				'notice_message'         => urlencode( $this->get_requested_value( 'notice_message' ) ),
				'call_to_action'         => urlencode( $this->get_requested_value( 'call_to_action' ) ),
				'call_to_action_url'     => urlencode( $this->get_requested_value( 'call_to_action_url' ) ),
				'minimum_order_amount'   => urlencode( $this->get_requested_value( 'minimum_order_amount' ) ),
				'threshold_order_amount' => urlencode( $this->get_requested_value( 'threshold_order_amount' ) ),
				'deadline_days'          => urlencode( serialize( $this->get_requested_value( 'deadline_days' ) ) ),
				'deadline_hour'          => urlencode( $this->get_requested_value( 'deadline_hour' ) ),
				'referer'                => urlencode( $this->get_requested_value( 'referer' ) ),
				'product_ids'            => urlencode( serialize( $this->get_requested_value( 'product_ids' ) ) ),
				'hide_product_ids'       => urlencode( serialize( $this->get_requested_value( 'hide_product_ids' ) ) ),
				'shipping_countries'     => urlencode( serialize( $this->get_requested_value( 'shipping_countries' ) ) ),
				'minimum_quantity'       => urlencode( $this->get_requested_value( 'minimum_quantity' ) ),
				'maximum_quantity'       => urlencode( $this->get_requested_value( 'maximum_quantity' ) ),
				'category_ids'           => urlencode( serialize( $this->get_requested_value( 'category_ids' ) ) ),
				'hide_category_ids'      => urlencode( serialize( $this->get_requested_value( 'hide_category_ids' ) ) ),
			);

			if ( 'create' === $action ) {
				$query_params['notice_type'] = urlencode( $this->get_requested_value( 'notice_type' ) );
			} elseif ( 'update' === $action ) {
				$query_params['id'] = $this->get_requested_value( 'id' );
			}

			/**
			 * Filter the query parameters so custom validations errors can be shown on Cart Notices admin pages.
			 * Notify Diego Z if this changes {BR 2016-11-28}
			 *
			 * @since 1.6.1
			 * @param array $query_params
			 * @param \WC_Cart_Notices_Admin $admin_instance admin class instance
			 */
			$query_params = apply_filters( 'wc_cart_notices_validate_error_query_params', $query_params, $this );

			return wp_redirect( esc_url_raw( add_query_arg( $query_params, "admin.php" ) ) );
		}

		// data common to an insert or update
		$fields = array(
			'name'       => trim( $this->get_requested_value( 'notice_name' ) ),
			'enabled'    => $this->get_requested_value( 'notice_enabled' ) ? 1 : 0,
			'message'    => trim( $this->get_requested_value( 'notice_message' ) ),
			'action'     => trim( $this->get_requested_value( 'call_to_action' ) ),
			'action_url' => trim( $this->get_requested_value( 'call_to_action_url' ) ),
			'date_added' => date("Y-m-d H:i:s")
		);

		// get the notice type, depending on whether we're creating or updating
		if ( 'create' === $action ) {
			$notice_type    = $this->get_requested_value( 'notice_type' );
			$fields['type'] = $notice_type;
		} elseif ( 'update' === $action ) {
			// load the immutable notice type from the database
			$notice_type = $wpdb->get_var( $wpdb->prepare( "SELECT type FROM {$wpdb->prefix}cart_notices WHERE id = %d", $this->get_requested_value( 'id' ) ) );
		}

		// set any missing defaults (ie, unchecked check boxes)
		if ( 'deadline' === $notice_type ) {

			$deadline_days = $this->get_requested_value( 'deadline_days' );

			for ( $i = 0; $i < 6; $i++ ) {

				if ( ! isset( $deadline_days[ $i ] ) ) {
					$deadline_days[ $i ] = 0;
				}
			}
		}

		// handle the type-dependent data field
		switch ( $notice_type ) {

			case 'minimum_amount':

				$fields['data']['minimum_order_amount']   = trim( $this->get_requested_value( 'minimum_order_amount' ) );
				$fields['data']['threshold_order_amount'] = trim( $this->get_requested_value( 'threshold_order_amount' ) );

			break;

			case 'deadline':

				$fields['data'] = array(
					'deadline_hour' => trim( $this->get_requested_value( 'deadline_hour' ) ),
					'deadline_days' => $deadline_days,
				);

			break;

			case 'referer':
				$fields['data']['referer'] = trim( $this->get_requested_value( 'referer' ) );
			break;

			case 'products':

				$fields['data']['product_ids']        = $this->get_requested_value( 'product_ids' );
				$fields['data']['hide_product_ids']   = $this->get_requested_value( 'hide_product_ids' );
				$fields['data']['shipping_countries'] = $this->get_requested_value( 'shipping_countries' );
				$fields['data']['minimum_quantity']   = $this->get_requested_value( 'minimum_quantity' );
				$fields['data']['maximum_quantity']   = $this->get_requested_value( 'maximum_quantity' );

			break;

			case 'categories':

				$fields['data']['category_ids']      = $this->get_requested_value( 'category_ids' );
				$fields['data']['hide_category_ids'] = $this->get_requested_value( 'hide_category_ids' );

			break;

		}

		/**
		 * Filters the fields for the notice.
		 * Notify Diego Z if this changes {BR 2016-11-28}
		 *
		 * @since 1.6.1
		 * @param array $fields notice fields
		 */
		$fields = apply_filters( 'wc_cart_notices_update_fields', $fields );

		$fields['data'] = maybe_serialize( $fields['data'] );

		// perform the insert or update
		if ( 'create' === $action ) {

			$wpdb->insert( "{$wpdb->prefix}cart_notices", $fields );

			return wp_redirect( esc_url_raw( add_query_arg( array( "page" => wc_cart_notices()->id, 'tab' => 'list', "result" => "created" ), 'admin.php' ) ) );

		} elseif ( 'update' === $action ) {

			$id = $this->get_requested_value( 'id' );

			$wpdb->update( "{$wpdb->prefix}cart_notices", $fields, array( 'id' => $id ) );

			return wp_redirect( esc_url_raw( add_query_arg( array( "page" => wc_cart_notices()->id, 'id' => $id, 'tab' => 'edit', "result" => "updated" ), 'admin.php' ) ) );
		}
	}


	/**
	 * Adds a Cart Notices menu item in the standard WooCommerce menu category.
	 *
	 * @internal
	 *
	 * @since 1.0.7
	 */
	public function add_menu_item() {

		add_submenu_page( 'woocommerce',                                  // parent menu
			__( 'WooCommerce Cart Notices', 'woocommerce-cart-notices' ), // page title
			__( 'Cart Notices', 'woocommerce-cart-notices' ),             // menu title
			'manage_woocommerce',                                         // capability
			wc_cart_notices()->id,                                        // unique menu slug
			array( $this, 'wc_cart_notices_options' )                     // callback
		);
	}


	/**
	 * Adds support for the WooCommerce enhanced navigation admin.
	 *
	 * @internal
	 *
	 * @since 1.13.1
	 */
	public function add_enhanced_navigation_menu_items() {

		if ( ! Framework\SV_WC_Helper::is_wc_navigation_enabled() ) {
			return;
		}

		Enhanced_Navigation_Menu::add_plugin_category( [
			'id'      => 'woocommerce-cart-notices',
			'title'   => __( 'Cart Notices', 'woocommerce-cart-notices' ),
			'parent'  => 'woocommerce',
		] );

		Enhanced_Navigation_Menu::add_plugin_item( [
			'id'      => 'woocommerce-cart-notices-view-all-notices',
			'parent'  => 'woocommerce-cart-notices',
			'title'   => __( 'All Notices', 'woocommerce-cart-notices' ),
			'url'     => 'wc-cart-notices',
			'order'   => 1,
		] );

		Enhanced_Navigation_Menu::add_plugin_item( [
			'id'     => 'woocommerce-cart-notices-add-new-notice',
			'parent' => 'woocommerce-cart-notices',
			'title'  => __( 'Add New Notice', 'woocommerce-cart-notices' ),
			'url'    => 'wc-cart-notices&tab=new',
			'order'  => 2,
		] );
	}


	/**
	 * Handles conflicts in the enhanced navigation menu items.
	 *
	 * WooCommerce Admin will automatically try to migrate the standard WordPress
	 * submenu item added in {@see \WC_Cart_Notices_Admin::add_menu_item()}
	 * but this will conflict with the menu item added in
	 * {@see \WC_Cart_Notices_Admin::add_enhanced_navigation_menu_items()},
	 * thus we remove that to ensure that the navigation isn't broken.
	 *
	 * @internal
	 *
	 * @since 1.13.1
	 *
	 * @param array $items the list of current menu items
	 * @return array a filtered menu item list
	 */
	public function remove_enhanced_navigation_menu_item( $items ) {

		foreach ( array_keys( (array) $items ) as $item ) {
			if ( 'cart-notices' === $item ) {
				unset( $items[ $item ] );
				break;
			}
		}

		return $items;
	}


	/**
	 * Handle the enable/disable/delete actions.
	 *
	 * @since 1.4.0
	 */
	public function toggle_cart_notice() {
		global $wpdb;

		// If on the WC Cart Notices screen & the current user can manage WooCommerce, continue.
		if ( isset( $_GET['page'] ) && wc_cart_notices()->id === $_GET['page'] && current_user_can( 'manage_woocommerce' ) ) {

			$action = isset( $_GET['action'] ) ? $_GET['action'] : false;

			// If no action or cart notice ID are set, bail.
			if ( ! $action || ! isset( $_GET['id'] ) ) {
				return;
			}

			$id = (int) $_GET['id'];

			if ( 'enable' === $action ) {

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cart_notices SET enabled=true WHERE id = %d", $id ) );

				wp_redirect( esc_url_raw( add_query_arg( array( 'page' => wc_cart_notices()->id, 'result' => 'enabled' ), 'admin.php' ) ) );
				exit;

			} elseif ( 'disable' === $action ) {

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}cart_notices SET enabled=false WHERE id = %d", $id ) );

				wp_redirect( esc_url_raw( add_query_arg( array( 'page' => wc_cart_notices()->id, 'result' => 'disabled' ), 'admin.php' ) ) );
				exit;

			} elseif ( 'delete' === $action ) {

				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}cart_notices WHERE id = %d", $id ) );

				wp_redirect( esc_url_raw( add_query_arg( array( 'page' => wc_cart_notices()->id, 'result' => 'deleted' ), 'admin.php' ) ) );
				exit;
			}
		}
	}


	/**
	 * Render the plugin options page,
	 * and handle the enable/disable/delete tab actions
	 *
	 * @since 1.0.7
	 */
	public function wc_cart_notices_options() {
		global $wpdb;

		// Check the user capabilities
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-cart-notices' ) );
		}

		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'list';

		if ( 'list' === $tab ) {

			$notices = wc_cart_notices()->get_notices();

			// load product names, category names, and deadline day names, as needed
			foreach ( $notices as $key => $notice ) {

				if ( 'products' === $notice->type ) {
					$notices[ $key ] = $this->load_product_data( $notice );
				} elseif ( 'categories' === $notice->type ) {
					$notices[ $key ] = $this->load_category_data( $notice );
				} elseif ( 'deadline' === $notice->type ) {
					$notices[ $key ] = $this->load_deadline_data( $notice );
				}
			}

		} elseif ( 'new' === $tab ) {

			// create a new dummy object, loading any request data if there was a validation error
			$notice = $this->load_notice_from_request();

			if ( 'products' === $notice->type ) {
				$this->load_product_data( $notice );
			} elseif ( 'categories' === $notice->type ) {
				$this->load_category_data( $notice );
			}

		} elseif ( 'edit' === $tab ) {

			$id = $_GET['id'];

			$notice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cart_notices WHERE id = %d", $id ) );
			$notice->data = maybe_unserialize( $notice->data );

			if ( ! $notice ) {
				wp_die( 'The requested data could not be found!', 'woocommerce-cart-notices' );
			}

			if ( isset( $_REQUEST['notice_name'] ) ) {

				// edit page error request, get the submitted data from the request so error messages can be displayed and the user can fix as necessary
				$notice_type = $notice->type;
				$notice = $this->load_notice_from_request();
				$notice->id = $id;
				$notice->type = $notice_type;
			}

			if ( 'products' === $notice->type ) {
				$this->load_product_data( $notice );
			} elseif ( 'categories' === $notice->type ) {
				$this->load_category_data( $notice );
			}
		}

		require_once( wc_cart_notices()->get_plugin_path() . '/src/admin/views/admin-options.php' );
	}


	/**
	 * Inject global settings into the Settings > General page
	 *
	 * @since 1.2.3
	 * @param array $settings array of WooCommerce settings
	 * @return array WooCommerce settings
	 */
	public function add_global_settings( $settings ) {

		/**
		 * Filter Cart Notices settings
		 *
		 * @since 1.2.3
		 * @param array $settings array of Cart Notices settings
		 */
		$cart_notices_settings = apply_filters( 'wc_cart_notices_global_settings', array(

			array( 'title' => __( 'Cart Notices', 'woocommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'cart_notices' ),

			array(
				'title'   => __( 'Delete data', 'woocommerce-cart-notices' ),
				'desc'    => __( 'Delete all Cart Notices data on uninstall', 'woocommerce-cart-notices' ),
				'id'      => 'wc_cart_notices_uninstall_data',
				'default' => 'no',
				'type'    => 'checkbox'
			),

			array( 'type' => 'sectionend', 'id' => 'cart_notices' ),
		) );

		return array_merge( $settings, $cart_notices_settings );
	}


	/** Helper methods ******************************************************/


	/**
	 * Helper function to create and load a notice object from the client request
	 *
	 * @since 1.0.7
	 * @return stdClass notice settings object with data loaded from the current request
	 */
	private function load_notice_from_request() {

		$notice = (object) array(
			'name'       => $this->get_requested_value( 'notice_name' ),
			'enabled'    => $this->get_requested_value( 'notice_enabled' ),
			'type'       => $this->get_requested_value( 'notice_type' ),
			'message'    => $this->get_requested_value( 'notice_message' ),
			'action'     => $this->get_requested_value( 'call_to_action' ),
			'action_url' => $this->get_requested_value( 'call_to_action_url' ),
			'data'       => array(
				'minimum_order_amount' => $this->get_requested_value( 'minimum_order_amount' ),
				'threshold_order_amount' => $this->get_requested_value( 'threshold_order_amount' ),
				'deadline_hour'        => $this->get_requested_value( 'deadline_hour' ),
				'deadline_days'        => unserialize( $this->get_requested_value( 'deadline_days' ) ),
				'referer'              => $this->get_requested_value( 'referer' ),
				'product_ids'          => unserialize( $this->get_requested_value( 'product_ids' ) ),
				'hide_product_ids'     => unserialize( $this->get_requested_value( 'hide_product_ids' ) ),
				'shipping_countries'   => unserialize( $this->get_requested_value( 'shipping_countries' ) ),
				'minimum_quantity'     => $this->get_requested_value( 'minimum_quantity' ),
				'maximum_quantity'     => $this->get_requested_value( 'maximum_quantity' ),
				'category_ids'         => unserialize( $this->get_requested_value( 'category_ids' ) ),
				'hide_category_ids'    => unserialize( $this->get_requested_value( 'hide_category_ids' ) ),
			),
		);

		/**
		 * Allow actors to modify the notice loaded from $_REQUEST arguments.
		 * Notify Diego Z if this changes {BR 2016-12-16}
		 *
		 * @since 1.6.1
		 * @param stdClass $notice notice settings object
		 * @param \WC_Cart_Notices_Admin $admin admin class instance
		 */
		return apply_filters( 'wc_cart_notices_load_notice_from_request', $notice, $this );
	}


	/**
	 * Safely get value from the REQUEST object
	 *
	 * @since 1.0.7
	 * @param string $name
	 * @return string|null value if it exists, null otherwise
	 */
	private function get_requested_value( $name ) {

		if ( isset( $_REQUEST[ $name ] ) ) {

			if ( is_string( $_REQUEST[ $name ] ) ) {
				return stripslashes( $_REQUEST[ $name ] );
			} else {
				return $_REQUEST[ $name ];
			}
		}

		return null;
	}


	/**
	 * Helper function to load the product data for the given notice
	 *
	 * @since 1.0.7
	 * @param stdClass $notice notice settings object
	 * @return stdClass notice settings object with products loaded
	 */
	private function load_product_data( $notice ) {

		$products = $hide_products = array();

		// get any products for the autocompleting search box
		if ( isset( $notice->data['product_ids'] ) && is_array( $notice->data['product_ids'] ) ) {

			foreach ( $notice->data['product_ids'] as $product_id ) {

				if ( $product = wc_get_product( $product_id ) ) {
					$products[ $product_id ] = $product->get_formatted_name();
				}
			}
		}

		// get any products for the autocompleting search box
		if ( isset( $notice->data['hide_product_ids'] ) && is_array( $notice->data['hide_product_ids'] ) ) {

			foreach ( $notice->data['hide_product_ids'] as $product_id ) {

				if ( $product = wc_get_product( $product_id ) ) {
					$hide_products[ $product_id ] = $product->get_formatted_name();
				}
			}
		}

		$notice->data['products']      = $products;
		$notice->data['hide_products'] = $hide_products;

		return $notice;
	}


	/**
	 * Helper function to load the category data for the given notice
	 *
	 * @since 1.0.7
	 * @param stdClass $notice notice settings object
	 * @return stdClass notice settings object with categories loaded
	 */
	private function load_category_data( $notice ) {

		$categories = $hide_categories = array();

		// get any product categories for the autocompleting search box
		if ( isset( $notice->data['category_ids'] ) && is_array( $notice->data['category_ids'] ) ) {

			foreach ( $notice->data['category_ids'] as $category_id ) {

				$category = get_term( $category_id, 'product_cat' );

				if ( ! $category ) {
					continue;
				}

				$categories[ $category_id ] = $category->name;
			}
		}

		// get any product categories for the autocompleting search box
		if ( isset( $notice->data['hide_category_ids'] ) && is_array( $notice->data['hide_category_ids'] ) ) {

			foreach ( $notice->data['hide_category_ids'] as $category_id ) {

				$category = get_term( $category_id, 'product_cat' );

				if ( ! $category ) {
					continue;
				}

				$hide_categories[ $category_id ] = $category->name;
			}
		}

		$notice->data['categories']      = $categories;
		$notice->data['hide_categories'] = $hide_categories;

		return $notice;
	}


	/**
	 * Helper function to load displayable deadline days data for this notice
	 *
	 * @since 1.0.7
	 * @param stdClass $notice notice settings object
	 * @return stdClass notice settings object with deadline days formatted
	 */
	private function load_deadline_data( $notice ) {

		$days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat' );

		$active_days = array();

		foreach ( $days as $key => $name ) {

			if ( isset( $notice->data['deadline_days'][ $key ] ) && $notice->data['deadline_days'][ $key ] ) {
				$active_days[] = $name;
			}
		}

		$notice->data['deadline_days_names'] = $active_days;

		return $notice;
	}


}
