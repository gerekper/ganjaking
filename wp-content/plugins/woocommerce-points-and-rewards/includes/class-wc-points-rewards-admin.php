<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class
 *
 * Load / saves admin settings
 *
 * @since 1.3.1
 */
class WC_Points_Rewards_Admin {

	/** @var string settings page ID */
	private $page_id;

	/** @var array points & rewards manage/actions tabs */
	private $tabs;

	/* @var \WC_Points_Rewards_Manage_Points_List_Table the manage points list table object */
	private $manage_points_list_table;

	/* @var \WC_Points_Rewards_Points_Log_List_Table The points log list table object */
	private $points_log_list_table;


	/**
	 * Setup admin class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->tabs = array(
			'manage'   => __( 'Manage Points', 'woocommerce-points-and-rewards' ),
			'log'      => __( 'Points Log', 'woocommerce-points-and-rewards' ),
			'settings' => __( 'Settings', 'woocommerce-points-and-rewards' )
		);

		/** General admin hooks */

		// Load WC styles / scripts.
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );

		// add 'Points & Rewards' link under WooCommerce menu.
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// enqueue assets.
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

		// manage points / points log list table settings.
		add_action( 'in_admin_header',   array( $this, 'load_list_tables' ) );
		add_filter( 'set-screen-option', array( $this, 'set_list_table_options' ), 10, 3 );
		add_filter( 'manage_woocommerce_page_WC_Points_Rewards_columns', array( $this, 'manage_columns' ) );

		// warn that points won't be able to be redeemed if coupons are disabled.
		add_action( 'admin_notices', array( $this, 'verify_coupons_enabled' ) );

		// save settings.
		add_action( 'admin_post_save_points_rewards_settings', array( $this, 'save_settings' ) );

		// Add a custom field types.
		add_action( 'woocommerce_admin_field_conversion_ratio', array( $this, 'render_conversion_ratio_field' ) );
		add_action( 'woocommerce_admin_field_singular_plural',  array( $this, 'render_singular_plural_field' ) );
		add_action( 'woocommerce_admin_field_points_expiry', array( $this, 'render_points_expiry' ) );

		// save custom field types.
		add_action( 'init', array( $this, 'save_custom_field_types' ) );

		// Add a apply points woocommerce_admin_fields() field type.
		add_action( 'woocommerce_admin_field_apply_points', array( $this, 'render_apply_points_section' ) );

		// handle any settings page actions (apply points to previous orders).
		add_action( 'woocommerce_admin_field_apply_points', array( $this, 'handle_settings_actions' ) );

		/** Order hooks */

		// Add the points earned/redeemed for a discount to the edit order page.
		add_action( 'woocommerce_admin_order_totals_after_shipping', array( $this, 'render_points_earned_redeemed_info' ) );

		/** Coupon hooks */

		// Add coupon points modifier field.
		add_action( 'woocommerce_coupon_options', array( $this, 'render_coupon_points_modifier_field' ) );

		// Save coupon points modifier field.
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'save_coupon_points_modifier_field' ) );

		// Sync variation prices.
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			add_action( 'woocommerce_variable_product_sync', array( $this, 'variable_product_sync' ), 10, 2 );
		} else {
			add_action( 'woocommerce_variable_product_sync_data', array( $this, 'variable_product_sync' ), 10 );
		}

		// Tool to clear points.
		add_filter( 'woocommerce_debug_tools', array( $this, 'woocommerce_debug_tools' ) );
	}

	public function assets() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

	}

	/**
	 * Verify that coupouns are enabled and render an annoying warning in the
	 * admin if they are not
	 *
	 * @since 1.0
	 */
	public function verify_coupons_enabled() {

		$coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;

		if ( ! $coupons_enabled ) {
			$message = sprintf(
				__( 'WooCommerce Points and Rewards requires coupons to be %senabled%s in order to function properly and allow customers to redeem points during checkout.', 'woocommerce-points-and-rewards' ),
				'<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">',
				'</a>'
			);

			echo '<div class="error"><p>' . $message . '</p></div>';
		}
	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its JS on
	 *
	 * @since 1.0
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );

		// sub-menu page.
		$screen_ids[] = $wc_screen_id . '_page_woocommerce-points-and-rewards';

		// add/edit product category page.
		$screen_ids[] = 'edit-product_cat';

		return $screen_ids;
	}


	/** 'Points & Rewards' sub-menu methods ******************************************************/


	/**
	 * Add 'Points & Rewards' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @since 1.0
	 */
	public function add_menu_link() {

		$this->page_id = add_submenu_page(
			'woocommerce',
			__( 'Points & Rewards', 'woocommerce-points-and-rewards' ),
			__( 'Points & Rewards', 'woocommerce-points-and-rewards' ),
			'manage_woocommerce',
			'woocommerce-points-and-rewards',
			array( $this, 'show_sub_menu_page' )
		);

		// add the Manage Points/Points log list table Screen Options.
		add_action( 'load-' . $this->page_id, array( $this, 'add_list_table_options' ) );
	}


	/**
	 * Save our list table options
	 *
	 * @since 1.0
	 * @param string $status unknown.
	 * @param string $option the option name.
	 * @param mixed $value the option value.
	 * @return mixed
	 */
	public function set_list_table_options( $status, $option, $value ) {
		if ( 'wc_points_rewards_manage_points_customers_per_page' == $option || 'wc_points_rewards_points_log_per_page' == $option )
			return $value;

		return $status;
	}


	/**
	 * Add list table Screen Options
	 *
	 * @since 1.0
	 */
	public function add_list_table_options() {

		if ( isset( $_GET['tab'] ) && 'log' === $_GET['tab'] ) {
			$args = array(
				'label' => __( 'Points Log', 'woocommerce-points-and-rewards' ),
				'default' => 20,
				'option' => 'wc_points_rewards_points_log_per_page',
			);
		} else {
			$args = array(
				'label' => __( 'Manage Points', 'woocommerce-points-and-rewards' ),
				'default' => 20,
				'option' => 'wc_points_rewards_manage_points_customers_per_page',
			);
		}

		add_screen_option( 'per_page', $args );
	}


	/**
	 * Loads the list tables so the columns can be hidden/shown from
	 * the page Screen Options dropdown (this must be done prior to Screen Options
	 * being rendered)
	 *
	 * @since 1.0
	 */
	public function load_list_tables() {

		if ( isset( $_GET['page'] ) && 'woocommerce-points-and-rewards' == $_GET['page'] ) {
			if ( isset( $_GET['tab'] ) && 'log' == $_GET['tab'] )
				$this->get_points_log_list_table();
			else
				$this->get_manage_points_list_table();
		}
	}


	/**
	 * Returns the list table columns so they can be managed from the screen
	 * options pulldown.  Normally this would happen automatically based on the
	 * screen id, but since we have two distinct list tables sharing one screen
	 * we had to generate unique id's in the two list table constructors, which
	 * means that the core manage_{screen_id}_columns filters don't get called,
	 * so we hook on the screen-based filter and then call our two custom-screen
	 * based filters to get the columns based on the current tab.
	 *
	 * Unfortunately the settings still seem to be saved to the common screen id
	 * so hiding a column in one list table hides a column of the same name in
	 * the other
	 *
	 * @since 1.0
	 * @param $columns array array of column definitions
	 * @return array of column definitions
	 */
	public function manage_columns( $columns ) {
		if ( isset( $_GET['page'] ) && 'woocommerce-points-and-rewards' == $_GET['page'] ) {
			if ( isset( $_GET['tab'] ) && 'log' == $_GET['tab'] )
				$columns = apply_filters( 'manage_woocommerce_page_WC_Points_Rewards_points_log_columns', $columns );
			else
				$columns = apply_filters( 'manage_woocommerce_page_WC_Points_Rewards_manage_points_columns', $columns );
		}

		return $columns;
	}


	/**
	 * Show Points & Rewards Manage/Log page content
	 *
	 * @since 1.0
	 */
	public function show_sub_menu_page() {

		$current_tab = ( empty( $_GET['tab'] ) ) ? 'manage' : urldecode( $_GET['tab'] );

		?>
		<div class="wrap woocommerce">
			<div id="icon-woocommerce" class="icon32-woocommerce-users icon32"><br /></div>
			<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">

			<?php

				// display tabs.
				foreach ( $this->tabs as $tab_id => $tab_title ) {

					$class = ( $tab_id == $current_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
					$url   = add_query_arg( 'tab', $tab_id, admin_url( 'admin.php?page=woocommerce-points-and-rewards' ) );

					printf( '<a href="%s" class="%s">%s</a>', $url, $class, $tab_title );
				}

			?> </h2> <?php


			// display tab content, default to 'Manage' tab.
			if ( 'log' === $current_tab )
				$this->show_log_tab();
			elseif ( 'settings' === $current_tab )
				$this->show_settings_tab();
			elseif ( 'manage' === $current_tab )
				$this->show_manage_tab();

		?></div> <?php
	}


	/**
	 * Show the Points & Rewards > Manage tab content
	 *
	 * @since 1.0
	 */
	private function show_manage_tab() {

		// setup 'Manage Points' list table and prepare the data.
		$manage_table = $this->get_manage_points_list_table();
		$manage_table->prepare_items();

		?><form method="post" id="mainform" action="" enctype="multipart/form-data"><?php

		// title/search result string.
		echo '<h2>' . __( 'Manage Customer Points', 'woocommerce-points-and-rewards' ) . '</h2>';

		// display any action messages.
		$manage_table->render_messages();

		echo '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';

		// display the list table.
		$manage_table->display();
		?></form><?php
	}


	/**
	 * Show the Points & Rewards > Log tab content
	 *
	 * @since 1.0
	 */
	private function show_log_tab() {

		// setup 'Points Log' list table and prepare the data.
		$log_table = $this->get_points_log_list_table();
		$log_table->prepare_items();

		?><form method="get" id="mainform" action="" enctype="multipart/form-data"><?php

		// title/search result string.
		echo '<h2>' . __( 'Points Log', 'woocommerce-points-and-rewards' ) . '</h2>';

		echo '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';
		echo '<input type="hidden" name="tab" value="' . esc_attr( $_REQUEST['tab'] ) . '" />';

		// display the list table.
		$log_table->display();
		?></form><?php
	}

	/**
	 * Show the Points & Rewards > Settings tab content
	 *
	 * @since 1.4.2
	 */
	private function show_settings_tab() {
		?>
		<form method="post" action="admin-post.php" enctype="multipart/form-data">
			<input type="hidden" name="action" value="save_points_rewards_settings" />
			<?php
				wp_nonce_field( 'points-rewards-save-settings-verify' );
				$this->render_settings();
			?>
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'woocommerce' ) ?>" />
		</form>
		<?php
	}


	/**
	 * Gets the manage points list table object
	 *
	 * @since 1.0
	 * @return \WC_Points_Rewards_Manage_Points_List_Table the points & rewards manage points list table object
	 */
	private function get_manage_points_list_table() {
		global $wc_points_rewards;

		if ( ! is_object( $this->manage_points_list_table ) ) {

			$class_name = apply_filters( 'wc_points_rewards_manage_points_list_table_class_name', 'wc_points_rewards_Manage_Points_List_Table' );

			require( $wc_points_rewards->get_plugin_path() . '/includes/class-wc-points-rewards-manage-points-list-table.php' );

			$this->manage_points_list_table = new $class_name();
		}

		return $this->manage_points_list_table;
	}


	/**
	 * Gets the points log list table object
	 *
	 * @since 1.0
	 * @return \WC_Points_Rewards_Points_Log_List_Table the points & rewards points log list table object
	 */
	private function get_points_log_list_table() {
		global $wc_points_rewards;

		if ( ! is_object( $this->points_log_list_table ) ) {

			$class_name = apply_filters( 'wc_points_rewards_points_log_list_table_class_name', 'wc_points_rewards_Points_Log_List_Table' );

			require( $wc_points_rewards->get_plugin_path() . '/includes/class-wc-points-rewards-points-log-list-table.php' );

			$this->points_log_list_table = new $class_name();
		}

		return $this->points_log_list_table;
	}


	/**
	 * Render the 'Points & Rewards' settings page
	 *
	 * @since 1.0
	 */
	public function render_settings() {
		woocommerce_admin_fields( $this->get_settings() );

		$confirm_message = __( 'Are you sure you want to apply points to all previous orders that have not already had points generated? This cannot be reversed! Note that this can take some time in shops with a large number of orders, if an error occurs, simply Apply Points again to continue the process.', 'woocommerce-points-and-rewards' );

		wc_enqueue_js( "
			// confirm admin wants to apply points to all previous orders
			$( '#wc_points_rewards_apply_points_to_previous_orders' ).click( function( e ) {
				if ( ! confirm( '" . esc_js( $confirm_message ) . "' ) ) {
					e.preventDefault();
				}
			} );
			$( '.date-picker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				showOn: 'button',
				buttonImage: '" . WC()->plugin_url() . "/assets/images/calendar.png',
				buttonImageOnly: true
			});
			var _href = $('a#wc_points_rewards_apply_points_to_previous_orders').attr('href');
			$('.apply_points_until_field input.date-picker').change(function() {
				var date_value = $( this ).val();
				$('a#wc_points_rewards_apply_points_to_previous_orders').attr('href', _href + '&date=' + date_value);
			}).change();
		" );
	}


	/**
	 * Save the 'Points & Rewards' settings page
	 *
	 * @since 1.0
	 */
	public function save_settings() {

		// Check the nonce.
		check_admin_referer( 'points-rewards-save-settings-verify' );

		// Save the settings.
		woocommerce_update_options( $this->get_settings() );

		// Go back to the settings page.
		wp_redirect( admin_url( 'admin.php?page=woocommerce-points-and-rewards&tab=settings' ) );

		exit;
	}

	/**
	 * Filters the save custom field type functions so they get sanitized correctly
	 */
	public function save_custom_field_types() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_points_rewards_earn_points_ratio', array( $this, 'save_conversion_ratio_field' ), 10, 3 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_points_rewards_redeem_points_ratio', array( $this, 'save_conversion_ratio_field' ), 10, 3 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_points_rewards_points_label', array( $this, 'save_singular_plural_field' ), 10, 3 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_wc_points_rewards_points_expiry', array( $this, 'save_points_expiry' ), 10, 3 );
		} else {
			add_action( 'woocommerce_update_option_conversion_ratio', array( $this, '_deprecated_save_conversion_ratio_field' ) );
			add_action( 'woocommerce_update_option_singular_plural', array( $this, '_deprecated_save_singular_plural_field' ) );
			add_action( 'woocommerce_update_option_points_expiry', array( $this, '_deprecated_save_points_expiry' ) );
		}
	}

	/**
	 * Returns settings array for use by render/save/install default settings methods
	 *
	 * @since 1.0
	 * @return array settings
	 */
	public static function get_settings() {

		$settings = array(

			array(
				'title' => __( 'Points Settings', 'woocommerce-points-and-rewards' ),
				'type'  => 'title',
				'id'    => 'wc_points_rewards_points_settings_start'
			),

			// earn points conversion.
			array(
				'title'    => __( 'Earn Points Conversion Rate', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the number of points awarded based on the product price.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_earn_points_ratio',
				'default'  => '1:1',
				'type'     => 'conversion_ratio'
			),

			// earn points conversion.
			array(
				'title'    => __( 'Earn Points Rounding Mode', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set how points should be rounded.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_earn_points_rounding',
				'default'  => 'round',
				'options'  => array(
					'round' => 'Round to nearest integer',
					'floor' => 'Always round down',
					'ceil'  => 'Always round up',
				),
				'type'     => 'select'
			),

			// redeem points conversion.
			array(
				'title'    => __( 'Redemption Conversion Rate', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the value of points redeemed for a discount.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_redeem_points_ratio',
				'default'  => '100:1',
				'type'     => 'conversion_ratio'
			),

			// redeem points conversion.
			array(
				'title'    => __( 'Partial Redemption', 'woocommerce-points-and-rewards' ),
				'desc'     => __( 'Enable partial redemption', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Lets users enter how many points they wish to redeem during cart/checkout.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_partial_redemption_enabled',
				'default'  => 'no',
				'type'     => 'checkbox'
			),

			// Minimum points discount.
			array(
				'title'    => __( 'Minimum Points Discount', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the minimum amount a user\'s points must add up to in order to redeem points. Use a fixed monetary amount or leave blank to disable.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_cart_min_discount',
				'default'  => '',
				'type'     => 'text',
			),

			// maximum points discount available.
			array(
				'title'    => __( 'Maximum Points Discount', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the maximum product discount allowed for the cart when redeeming points. Use either a fixed monetary amount or a percentage based on the product price. Leave blank to disable.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_cart_max_discount',
				'default'  => '',
				'type'     => 'text',
			),

			// maximum points discount available.
			array(
				'title'    => __( 'Maximum Product Points Discount', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the maximum product discount allowed when redeeming points per-product. Use either a fixed monetary amount or a percentage based on the product price. Leave blank to disable. This can be overridden at the category and product level.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_max_discount',
				'default'  => '',
				'type'     => 'text',
			),

			// Tax settings.
			array(
				'title'    => __( 'Tax Setting', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Whether or not points should apply to prices inclusive of tax.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_points_tax_application',
				'default'  => wc_prices_include_tax() ? 'inclusive' : 'exclusive',
				'options'  => array(
					'inclusive' => 'Apply points to price inclusive of taxes.',
					'exclusive' => 'Apply points to price exclusive of taxes.',
				),
				'type'     => 'select',
			),

			// points label.
			array(
				'title'    => __( 'Points Label', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'The label used to refer to points on the frontend, singular and plural.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_points_label',
				'default'  => sprintf( '%s:%s', __( 'Point', 'woocommerce-points-and-rewards' ), __( 'Points', 'woocommerce-points-and-rewards' ) ),
				'type'     => 'singular_plural',
			),

			// Expire Points.
			array(
				'title'    => __( 'Points Expire After', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Set the period after which points expire once granted to a user', 'woocommerce-points-and-rewards' ),
				'type'     => 'points_expiry',
				'id'       => 'wc_points_rewards_points_expiry'
			),

			array( 'type' => 'sectionend', 'id' => 'wc_points_rewards_points_settings_end' ),

			array(
				'title' => __( 'Product / Cart / Checkout Messages', 'woocommerce-points-and-rewards' ),
				'desc'  => sprintf( __( 'Adjust the message by using %1$s{points}%2$s and %1$s{points_label}%2$s to represent the points earned / available for redemption and the label set for points.', 'woocommerce-points-and-rewards' ), '<code>', '</code>' ),
				'type'  => 'title',
				'id'    => 'wc_points_rewards_messages_start'
			),

			// single product page message.
			array(
				'title'    => __( 'Single Product Page Message', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Add an optional message to the single product page below the price. Customize the message using {points} and {points_label}. Limited HTML is allowed. Leave blank to disable.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_single_product_message',
				'css'      => 'min-width: 400px;',
				'default'  => sprintf( __( 'Purchase this product now and earn %s!', 'woocommerce-points-and-rewards' ), '<strong>{points}</strong> {points_label}' ),
				'type'     => 'textarea',
			),

			// variable product page message.
			array(
				'title'    => __( 'Variable Product Page Message', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Add an optional message to the variable product page below the price. Customize the message using {points} and {points_label}. Limited HTML is allowed. Leave blank to disable.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_variable_product_message',
				'css'      => 'min-width: 400px;',
				'default'  => sprintf( __( 'Earn up to %s.', 'woocommerce-points-and-rewards' ), '<strong>{points}</strong> {points_label}' ),
				'type'     => 'textarea',
			),

			// earn points cart/checkout page message.
			array(
				'title'    => __( 'Earn Points Cart/Checkout Page Message', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Displayed on the cart and checkout page when points are earned. Customize the message using {points} and {points_label}. Limited HTML is allowed.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_earn_points_message',
				'css'      => 'min-width: 400px;',
				'default'  => sprintf( __( 'Complete your order and earn %s for a discount on a future purchase', 'woocommerce-points-and-rewards' ), '<strong>{points}</strong> {points_label}' ),
				'type'     => 'textarea',
			),

			// redeem points cart/checkout page message.
			array(
				'title'    => __( 'Redeem Points Cart/Checkout Page Message', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Displayed on the cart and checkout page when points are available for redemption. Customize the message using {points}, {points_value}, and {points_label}. Limited HTML is allowed.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_redeem_points_message',
				'css'      => 'min-width: 400px;',
				'default'  => sprintf( __( 'Use %s for a %s discount on this order!', 'woocommerce-points-and-rewards' ), '<strong>{points}</strong> {points_label}', '<strong>{points_value}</strong>' ),
				'type'     => 'textarea',
			),

			// earned points thank you / order received page message.
			array(
				'title'    => __( 'Thank You / Order Received Page Message', 'woocommerce-points-and-rewards' ),
				'desc_tip' => __( 'Displayed on the thank you / order received page when points were earned. Customize the message using {points}, {total_points}, {points_label}, and {total_points_label}. Limited HTML is allowed.', 'woocommerce-points-and-rewards' ),
				'id'       => 'wc_points_rewards_thank_you_message',
				'css'      => 'min-width: 400px;min-height: 75px;',
				'default'  => sprintf( __( 'You have earned %s for this order. You have a total of %s.', 'woocommerce-points-and-rewards' ), '<strong>{points}</strong> {points_label}', '<strong>{total_points}</strong> {total_points_label}' ),
				'type'     => 'textarea',
			),

			array( 'type' => 'sectionend', 'id' => 'wc_points_rewards_messages_end' ),

			array(
				'title' => __( 'Points Earned for Actions', 'woocommerce-points-and-rewards' ),
				'desc'  => __( 'Customers can also earn points for actions like creating an account or writing a product review. You can enter the amount of points the customer will earn for each action in this section.', 'woocommerce-points-and-rewards' ),
				'type'  => 'title',
				'id'    => 'wc_points_rewards_earn_points_for_actions_settings_start'
			),

			array( 'type' => 'sectionend', 'id' => 'wc_points_rewards_earn_points_for_actions_settings_end' ),

			array(
				'type'  => 'title',
				'title' => __( 'Actions', 'woocommerce-points-and-rewards' ),
				'id'    => 'wc_points_rewards_points_actions_start',
			),

			array(
				'title'       => __( 'Apply Points to Previous Orders', 'woocommerce-points-and-rewards' ),
				'desc_tip'    => __( 'This will apply points to all previous orders (processing and completed) and cannot be reversed.', 'woocommerce-points-and-rewards' ),
				'button_text' => __( 'Apply Points', 'woocommerce-points-and-rewards' ),
				'type'        => 'apply_points',
				'id'          => 'wc_points_rewards_apply_points_to_previous_orders',
				'class'       => 'wc-points-rewards-apply-button',
			),

			array( 'type' => 'sectionend', 'id' => 'wc_points_rewards_points_actions_end' ),

		);

		$integration_settings = apply_filters( 'wc_points_rewards_action_settings', array() );

		if ( $integration_settings ) {

			// set defaults.
			foreach ( array_keys( $integration_settings ) as $key ) {
				if ( ! isset( $integration_settings[ $key ]['css'] ) )  $integration_settings[ $key ]['css']  = 'max-width: 50px;';
				if ( ! isset( $integration_settings[ $key ]['type'] ) ) $integration_settings[ $key ]['type'] = 'text';
			}

			// find the start of the Points Earned for Actions settings to splice into.
			$index = -1;
			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && 'wc_points_rewards_earn_points_for_actions_settings_start' == $setting['id'] )
					break;
			}

			array_splice( $settings, $index + 1, 0, $integration_settings );
		}

		return apply_filters( 'wc_points_rewards_settings', $settings );
	}


	/**
	 * Render the Earn Points/Redeem Points conversion ratio section
	 *
	 * @since 1.0
	 * @param array $field associative array of field parameters
	 */
	public function render_conversion_ratio_field( $field ) {
		if ( isset( $field['title'] ) && isset( $field['id'] ) ) :

			$ratio = get_option( $field['id'], $field['default'] );

			list( $points, $monetary_value ) = explode( ':', $ratio );

			$monetary_value = str_replace( '.', wc_get_price_decimal_separator(), $monetary_value );

			?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for=""><?php echo wp_kses_post( $field['title'] ); ?></label>
						<img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( $field['desc_tip'] ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" height="16" width="16" />
					</th>
					<td class="forminp forminp-text">
						<fieldset>
							<input name="<?php echo esc_attr( $field['id'] . '_points' ); ?>" id="<?php echo esc_attr( $field['id'] . '_points' ); ?>" type="number" style="max-width: 70px;" value="<?php echo esc_attr( $points ); ?>" min="0" step="0.01" />&nbsp;<?php esc_html_e( 'Points', 'woocommerce-points-and-rewards' ); ?>
							<span>&nbsp;&#61;&nbsp;</span>&nbsp;<?php echo get_woocommerce_currency_symbol(); ?>
							<input class="wc_input_price" name="<?php echo esc_attr( $field['id'] . '_monetary_value' ); ?>" id="<?php echo esc_attr( $field['id'] . '_monetary_value' ); ?>" type="number" style="max-width: 70px;" value="<?php echo esc_attr( $monetary_value ); ?>" min="0" step="0.01" />
						</fieldset>
					</td>
				</tr>
			<?php

		endif;
	}


	/**
	 * Render a singular-plural text field
	 *
	 * @since 0.1
	 * @param array $field associative array of field parameters
	 */
	public function render_singular_plural_field( $field ) {
		if ( isset( $field['title'] ) && isset( $field['id'] ) ) :

			$value = get_option( $field['id'], $field['default'] );

			list( $singular, $plural ) = explode( ':', $value );

			?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for=""><?php echo wp_kses_post( $field['title'] ); ?></label>
						<img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( $field['desc_tip'] ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" height="16" width="16" />
					</th>
					<td class="forminp forminp-text">
						<fieldset>
							<input name="<?php echo esc_attr( $field['id'] . '_singular' ); ?>" id="<?php echo esc_attr( $field['id'] . '_singular' ); ?>" type="text" style="max-width: 75px;" value="<?php echo esc_attr( $singular ); ?>" />
							<input name="<?php echo esc_attr( $field['id'] . '_plural' ); ?>" id="<?php echo esc_attr( $field['id'] . '_plural' ); ?>" type="text" style="max-width: 75px;" value="<?php echo esc_attr( $plural ); ?>" />
						</fieldset>
					</td>
				</tr>
			<?php

		endif;
	}

	/**
	 * Save the points expiry field
	 *
	 * @since 1.4.2
	 * @param array $field
	 */
	public function save_points_expiry( $value, $option, $raw_value ) {

		if ( isset( $_POST[ $option['id'] . '_number' ] ) && isset( $_POST[ $option['id'] . '_period' ] ) ) {
			if ( is_numeric( $_POST[ $option['id'] . '_number' ] ) && in_array( $_POST[ $option['id'] . '_period' ], array( 'DAY', 'WEEK', 'MONTH', 'YEAR' ) ) ) {

				// Check if expire points since has been set
				if ( isset( $_POST[ 'expire_points_since' ] ) && DateTime::createFromFormat( 'Y-m-d', $_POST[ 'expire_points_since' ] ) ) {
					update_option( 'wc_points_rewards_points_expire_points_since', wc_clean( $_POST[ 'expire_points_since' ] ) );
				}
				
				return wc_clean( $_POST[ $option['id'] . '_number' ] ) . ':' . wc_clean( $_POST[ $option['id'] . '_period' ] );
			}
			else {
				update_option( 'wc_points_rewards_points_expire_points_since', '' );
				return '';
			}
		}
	}
	
	/**
	 * Backward compatible function to deal with deprecated actions in 2.4
	 * @since 2.4
	 */
	public function _deprecated_save_points_expiry( $option ) {
		$value = $this->save_points_expiry( null, $option, null );
		update_option( $option['id'], $value );
	}
	
	/**
	 * Save the Earn Points/Redeem Points Conversion Ratio field
	 *
	 * @since 1.0
	 * @param array $field
	 */
	public function save_conversion_ratio_field( $value, $option, $raw_value ) {

		if ( isset( $_POST[ $option['id'] . '_points' ] ) && ! empty( $_POST[ $option['id'] . '_monetary_value' ] ) )
			$points         = wc_clean( $_POST[ $option['id'] . '_points' ] );
			$monetary_value = wc_clean( $_POST[ $option['id'] . '_monetary_value' ] );
			$monetary_value = str_replace( wc_get_price_decimal_separator(), '.', $monetary_value );

			return $points . ':' . $monetary_value;
	}

	/**
	 * Backward compatible function to deal with deprecated actions in 2.4
	 * @since 2.4
	 */
	public function _deprecated_save_conversion_ratio_field( $option ) {
		$value = $this->save_conversion_ratio_field( null, $option, null );
		update_option( $option['id'], $value );
	}

	/**
	 * Save the singular-plural text fields
	 *
	 * @since 0.1
	 * @param array $field
	 */
	public function save_singular_plural_field( $value, $option, $raw_value ) {

		if ( ! empty( $_POST[ $option['id'] . '_singular' ] ) && ! empty( $_POST[ $option['id'] . '_plural' ] ) )
			return wc_clean( $_POST[ $option['id'] . '_singular' ] ) . ':' . wc_clean( $_POST[ $option['id'] . '_plural' ] );
	}

	/**
	 * Backward compatible function to deal with deprecated actions in 2.4
	 * @since 2.4
	 */
	public function _deprecated_save_singular_plural_field( $option ) {
		$value = $this->save_singular_plural_field( null, $option, null );
		update_option( $option['id'], $value );
	}


	/**
	 * Render the 'Points Expry' section
	 *
	 * @since 1.4.2
	 * @param array $field associative array of field parameters
	 */
	public function render_points_expiry( $field ) {

		if ( isset( $field['title'] ) && isset( $field['id'] ) ) :	

			$expiry = get_option( $field['id'] );
			
			if ( ! $expiry ) {
				$number = '';
				$period = '';
			}
			else {
				list( $number, $period ) = explode( ':', $expiry );
			}
			
			$periods = array(
				'DAY'   => 'Day(s)',
				'WEEK'  => 'Week(s)',
				'MONTH' => 'Month(s)',
				'YEAR'  => 'Year(s)'
			);
			
			$expire_since = get_option( 'wc_points_rewards_points_expire_points_since', '' );
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="expire_points"><?php echo wp_kses_post( $field['title'] ); ?></label>
					<img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( $field['desc_tip'] ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" height="16" width="16" />
				</th>
				<td class="forminp forminp-text" style="width: 50%; float: left;">
					<fieldset id="expire_points">
						<select name="<?php echo esc_attr( $field[ 'id' ] . '_number' ); ?>" id="<?php echo esc_attr( $field[ 'id' ] ); ?>_number">
							<option value=""></option>
							<?php
								for ( $num = 1; $num < 100; $num++ ) :
									$selected = '';
									if ( $num == $number ) {
										$selected = ' selected="selected" ';
									}
							?>
								<option value="<?php echo esc_attr( $num ); ?>" <?php echo $selected; ?>><?php echo $num; ?></option> 
							<?php endfor; ?>
						</select>
						<select name="<?php echo esc_attr( $field[ 'id' ] . '_period' ); ?>" id="<?php echo esc_attr( $field[ 'id' ] ); ?>_period">
							<option value=""></option>
							<?php
								foreach ( $periods as $period_id => $period_text ) :
									$selected = '';
									if ( $period_id == $period ) {
										$selected = ' selected="selected" ';
									}
							?>
								<option value="<?php echo esc_attr( $period_id ); ?>" <?php echo $selected; ?>><?php _e( $period_text, 'woocommerce-points-and-rewards' ); ?></option>
							<?php endforeach; ?>
						</select>

						<fieldset>
							<p class="form-field expire-points-since">
								<label for="expire_points_since"><?php printf( __( '%sOnly apply to points earned since%s - %sOptional%s', 'woocommerce-points-and-rewards' ), '<strong>', '</strong>', '<em>', '</em>' ); ?></label>
								<input type="text" class="date-picker" style="width: 200px;" name="expire_points_since" id="expire_points_since" value="<?php echo esc_attr( $expire_since ); ?>" placeholder="<?php echo _x( 'YYYY-MM-DD', 'placeholder', 'woocommerce-points-and-rewards' ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
								<p class="description"><?php _e( 'Leave blank to apply to all points', 'woocommerce-points-and-rewards' ); ?></p>
							</p>
						</fieldset>

					</fieldset>
				</td>
			</tr>
		<?php
		endif;
	}
	
	/**
	 * Render the 'Apply Points to all previous orders' section
	 *
	 * @since 1.0
	 * @param array $field associative array of field parameters
	 */
	public function render_apply_points_section( $field ) {
		if ( isset( $field['title'] ) && isset( $field['button_text'] ) && isset( $field['id'] ) ) :
		?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="apply_points"><?php echo wp_kses_post( $field['title'] ); ?></label>
					<img class="help_tip" data-tip="<?php echo wc_sanitize_tooltip( $field['desc_tip'] ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" height="16" width="16" />
				</th>
				<td class="forminp forminp-text" style="width:15%;">
					<fieldset>
						<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'apply_points' ) ) ); ?>" class="button" id="<?php echo $field['id'];?>"><?php echo esc_html( $field['button_text'] ); ?></a>
					</fieldset>
				</td>
				<td class="forminp forminp-text" style="width: 50%; float: left;">
					<fieldset>
						<p class="form-field apply_points_until_field">
							<label for="apply_points_until_field"><?php printf( __( '%sUp Until%s - %sOptional%s: Leave blank to apply to all orders', 'woocommerce-points-and-rewards' ), '<strong>', '</strong>', '<em>', '</em>' ); ?></label>
							<input type="text" class="date-picker" name="apply_points_until" id="apply_points_until" value="" placeholder="<?php echo _x( 'YYYY-MM-DD', 'placeholder', 'woocommerce-points-and-rewards' ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
						</p>
					</fieldset>
				</td>
			</tr>
		<?php

		endif;
	}


	/**
	 * Handles any points & rewards setting page actions.  The only available
	 * action is to apply points to previous orders, useful when the plugin
	 * is first installed
	 *
	 * @since 1.0
	 */
	public function handle_settings_actions() {

		global $wc_points_rewards;

		$current_tab     = ( empty( $_GET['tab'] ) )         ? null : sanitize_text_field( urldecode( $_GET['tab'] ) );
		$current_action  = ( empty( $_REQUEST['action'] ) )  ? null : sanitize_text_field( urldecode( $_REQUEST['action'] ) );
		$date 			 = ( empty( $_REQUEST['date'] ) )	 ? null : sanitize_text_field( urldecode( $_REQUEST['date'] ) );
		$date 			 = strtotime( $date );

		if ( 'settings' == $current_tab ) {

			if ( 'apply_points' == $current_action ) {

				// try and avoid timeouts as best we can
				@set_time_limit( 0 );

				// perform the action in manageable chunks
				$success_count  = 0;
				$offset         = 0;
				$posts_per_page = 500;

				do {

					$args = array(
						'post_type'      => 'shop_order',
						'fields'         => 'ids',
						'offset'         => $offset,
						'posts_per_page' => $posts_per_page,
						'meta_query' => array(
							array(
								'key'     => '_wc_points_earned',
								'compare' => 'NOT EXISTS'
							),
						)
					);

					// if date has been chosen, query orders only after that date
					if ( $date ) {
						$args['date_query']	= array(
							array(
								'after' => array(
									'year' 	=> date( 'Y', $date ),
									'month'	=> date( 'n', $date ),
									'day'	=> date( 'j', $date ),
								),
								'inclusive'	=> true,
							),
						);
					}

					// WC 2.2+
					if ( version_compare( WC_VERSION, '2.2.0', '>=' ) ) {
						$args['post_status'] = array( 'wc-processing', 'wc-completed' );
					}

					// grab a set of order ids for existing orders with no earned points set
					$order_ids = get_posts( $args );

					// some sort of database error
					if ( is_wp_error( $order_ids ) ) {
						$wc_points_rewards->admin_message_handler->add_error( __( 'Database error while applying user points.', 'woocommerce-points-and-rewards' ) );

						return;
					}

					// otherwise go through the results and set the order numbers
					if ( is_array( $order_ids ) ) {
						foreach( $order_ids as $order_id ) {

							$order = new WC_Order( $order_id );

							// only add points to processing or completed orders
							if ( 'processing' === $order->status || 'completed' === $order->status ) {

								$wc_points_rewards->order->add_points_earned( $order );

								$success_count++;
							}
						}
					}

					// increment offset
					$offset += $posts_per_page;

				} while( count( $order_ids ) == $posts_per_page );  // while full set of results returned  (meaning there may be more results still to retrieve)

				// success message
				$wc_points_rewards->admin_message_handler->add_message( sprintf( _n( '%d order updated.', '%s orders updated.', $success_count, 'woocommerce-points-and-rewards' ), $success_count ) );

			}

			if ( $wc_points_rewards->admin_message_handler->message_count() > 0 || $wc_points_rewards->admin_message_handler->error_count() > 0 ) {
				// display the result

				if ( $wc_points_rewards->admin_message_handler->error_count() > 0 )
					echo '<div id="message" class="error fade"><p><strong>' . esc_html( $wc_points_rewards->admin_message_handler->get_error( 0 ) ) . '</strong></p></div>';

				if ( $wc_points_rewards->admin_message_handler->message_count() > 0 )
					echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $wc_points_rewards->admin_message_handler->get_message( 0 ) ) . '</strong></p></div>';

			}
		}

	}


	/**
	 * Render the points earned / redeemed on the Edit Order totals section
	 *
	 * @since 1.0
	 * @param int $order_id the WC_Order ID
	 */
	public function render_points_earned_redeemed_info( $order_id ) {

		$points_earned   = get_post_meta( $order_id, '_wc_points_earned', true );
		$points_redeemed = get_post_meta( $order_id, '_wc_points_redeemed', true );

		?>
			<h4><?php _e( 'Points', 'woocommerce-points-and-rewards' ); ?></h4>
			<ul class="totals">
				<li class="left">
					<label><?php _e( 'Earned:', 'woocommerce-points-and-rewards' ); ?></label>
					<input type="number" disabled="disabled" id="_wc_points_earned" name="_wc_points_earned" placeholder="<?php _e( 'None', 'woocommerce-points-and-rewards' ); ?>" value="<?php if ( ! empty( $points_earned ) ) echo esc_attr( $points_earned ); ?>" class="first" />
				</li>
				<li class="right">
					<label><?php _e( 'Redeemed:', 'woocommerce-points-and-rewards' ); ?></label>
					<input type="number" disabled="disabled" id="_wc_points_redeemed" name="_wc_points_redeemed" placeholder="<?php _e( 'None', 'woocommerce-points-and-rewards' ); ?>" value="<?php if ( ! empty( $points_redeemed ) ) echo esc_attr( $points_redeemed ); ?>" class="first" />
				</li>
			</ul>
			<div class="clear"></div>
		<?php
	}


	/**
	 * Render the points modifier field on the create/edit coupon page
	 *
	 * TODO: an even better action implementation would be ajax calls with a progress or activity indicator
	 *
	 * @since 1.0
	 */
	public function render_coupon_points_modifier_field() {

		// Unique URL
		woocommerce_wp_text_input(
			array(
				'id'          => '_wc_points_modifier',
				'label'       => __( 'Points Modifier', 'woocommerce-points-and-rewards' ),
				'description' => __( 'Enter a percentage which modifies how points are earned when this coupon is applied. For example, enter 200% to double the amount of points typically earned when the coupon is applied.', 'woocommerce-points-and-rewards' ),
				'desc_tip'    => true,
			)
		);
	}


	/**
	 * Save the points modifier field on the create/edit coupon page
	 *
	 * @since 1.0
	 * @param int $post_id the coupon post ID
	 */
	public function save_coupon_points_modifier_field( $post_id ) {

		if ( ! empty( $_POST['_wc_points_modifier'] ) )
			update_post_meta( $post_id, '_wc_points_modifier', stripslashes( $_POST['_wc_points_modifier'] ) );
		else
			delete_post_meta( $post_id, '_wc_points_modifier' );

	}

	/**
	 * Go through variations and store the max and min points.
	 *
	 * @since 1.0.0
	 * @version 1.6.5
	 * @param object $product In < WC3.0 this is the variation ID.
	 * @param array $children In WC3.0+ this is not passed.
	 */
	public function variable_product_sync( $product, $children = array() ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$variation_id = $product;
		} else {
			$variation_id = $product->get_id();
			$children     = $product->get_children();
		}

		$wc_max_points_earned = '';
		$wc_min_points_earned = '';
		
		$variable_points = array();

		foreach ( $children as $child ) {
			$earned = get_post_meta( $child, '_wc_points_earned', true );
			if ( $earned !== '' ) {
				$variable_points[] = $earned;
			}
		}
		
		if ( count( $variable_points ) > 0 ) {
			$wc_max_points_earned = max( $variable_points );
			$wc_min_points_earned = min( $variable_points );
		}
		
		update_post_meta( $variation_id, '_wc_max_points_earned', $wc_max_points_earned );
		update_post_meta( $variation_id, '_wc_min_points_earned', $wc_min_points_earned );
	}

	/**
	 * Reset points tool
	 * @param  array $tools
	 * @return array
	 */
	public function woocommerce_debug_tools( $tools ) {
		$tools['reset_points_rewards'] = array(
			'name'     => __( 'Reset Points and Rewards', 'woocommerce-points-and-rewards' ),
			'button'   => __( 'Delete all points and rewards and clear the logs.', 'woocommerce-points-and-rewards' ),
			'desc'     => __( '<strong class="red">Note:</strong> This action will remove all customer points and cannot be undone.', 'woocommerce-points-and-rewards' ),
			'callback' => array( $this, 'reset_points_rewards' )
		);
		return $tools;
	}

	/**
	 * Clear all points and the logs
	 */
	public function reset_points_rewards() {
		global $wpdb, $wc_points_rewards;

		$wpdb->query( "TRUNCATE " . $wc_points_rewards->user_points_log_db_tablename );
		$wpdb->query( "TRUNCATE " . $wc_points_rewards->user_points_db_tablename  );

		echo '<div class="updated"><p>' . __( 'All points and rewards successfully deleted', 'woocommerce-points-and-rewards' ) . '</p></div>';
	}

} // end \WC_Points_Rewards_Admin class
