<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager
 *
 * @since       2.8
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager
 */

/**
 * WC_AM_Menus object.
 *
 * @since 2.8
 */
class WC_AM_Menus {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menus' ), 9 );
	}

	/**
	 * Customers Menu.
	 *
	 * @since 2.8
	 */
	public function menus() {
		$api_manager_customers = add_menu_page( __( 'API Manager', 'woocommerce-api-manager' ), __( 'API Manager', 'woocommerce-api-manager' ), 'manage_options', 'wcam_api_manager_customers', null, 'dashicons-admin-network', '58.0' );

		add_submenu_page( 'wcam_api_manager_customers', __( 'API Customers', 'woocommerce-api-manager' ), __( 'API Customers', 'woocommerce-api-manager' ), 'manage_options', 'wcam_api_manager_customers', array(
			$this,
			'customers_page'
		) );

		add_action( "load-$api_manager_customers", array( $this, 'add_screen_options' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Customers Menu.
	 *
	 * @since 2.8
	 */
	public function customers_page() {
		global $wc_am_customers_table_list;
		?>
        <div class="wrap">
            <h2><?php _e( 'API Customers', 'woocommerce-api-manager' ); ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=api_manager' ) ); ?>"
                                                                             class="page-title-action"><?php esc_html_e( 'Settings', 'woocommerce-api-manager' ); ?></a></h2>
			<?php
			$wc_am_customers_table_list->prepare_items();
			// Search box form in display_tablenav()
			$wc_am_customers_table_list->display();
			?>
        </div>
		<?php
	}

	/**
	 * Add screen options to the top of the List Table.
	 *
	 * @since 2.8
	 */
	public function add_screen_options() {
		global $wc_am_customers_table_list;

		$screen = get_current_screen();

		if ( ! is_object( $screen ) || $screen->id != 'toplevel_page_wcam_api_manager_customers' ) {
			return;
		}

		$args = array(
			'label'   => esc_html__( 'Number of items per page', 'woocommerce-api-manager' ),
			'default' => 20,
			'option'  => 'wc_am_customers_per_page',
		);

		add_screen_option( 'per_page', $args );

		$wc_am_customers_table_list = new WC_AM_Customers_Table_List();
	}

	/**
	 * Validate screen options on update.
	 *
	 * Filters a screen option value before it is set.
	 *
	 * The filter can also be used to modify non-standard [items]_per_page
	 * settings. See the parent function for a full list of standard options.
	 *
	 * Returning false from the filter will skip saving the current option.
	 *
	 * Only applied to options ending with '_page', or the 'layout_columns' option.
	 *
	 * @see   set_screen_options()
	 *
	 * @since 2.8
	 *
	 * @param bool|int $screen_option The value to save instead of the option value.
	 *                                Default false (to skip saving the current option).
	 * @param string   $option        The option name.
	 * @param int      $value         The option value. The number of rows to display.
	 */
	public function set_screen_option( $screen_option, $option, $value ) {
		if ( $option === 'wc_am_customers_per_page' ) {
			return $value;
		}

		return $screen_option;
	}
}