<?php

/*
Class Name: VI_WBOOSTSALES_Admin_System
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2015 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VI_WBOOSTSALES_Admin_ZSystem {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
	}

	public function page_callback() { ?>
		<h2><?php esc_html_e( 'System Status', 'woocommerce-boost-sales' ) ?></h2>
		<table cellspacing="0" id="status" class="widefat">
			<tbody>
			<tr>
				<td data-export-label="<?php esc_html_e( 'PHP Time Limit', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'PHP Time Limit', 'woocommerce-boost-sales' ) ?></td>
				<td><?php echo ini_get( 'max_execution_time' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="<?php esc_html_e( 'PHP Max Input Vars', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'PHP Max Input Vars', 'woocommerce-boost-sales' ) ?></td>

				<td><?php echo ini_get( 'max_input_vars' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="<?php esc_html_e( 'Memory Limit', 'woocommerce-boost-sales' ) ?>"><?php esc_html_e( 'Memory Limit', 'woocommerce-boost-sales' ) ?></td>

				<td><?php echo ini_get( 'memory_limit' ); ?></td>
			</tr>

			</tbody>
		</table>
	<?php }

	/**
	 * Register a custom menu page.
	 */
	public function menu_page() {
		add_submenu_page(
			'woocommerce-boost-sales',
			esc_html__( 'System Status', 'woocommerce-boost-sales' ),
			esc_html__( 'System Status', 'woocommerce-boost-sales' ),
			'manage_options',
			'woocommerce-boost-sales-status',
			array( $this, 'page_callback' )
		);

	}
}

?>