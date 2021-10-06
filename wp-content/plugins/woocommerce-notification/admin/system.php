<?php

/*
Class Name: VI_WNOTIFICATION_Admin_System
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016-2019 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VI_WNOTIFICATION_Admin_System {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
	}

	public function page_callback() { ?>
		<h2><?php esc_html_e( 'System Status', 'woocommerce-notification' ) ?></h2>
		<table cellspacing="0" id="status" class="widefat">
			<tbody>

			<tr>
				<td data-export-label="<?php esc_html_e( 'Log Directory Writable', 'woocommerce-notification' ) ?>"><?php esc_html_e( 'Log Directory Writable', 'woocommerce-notification' ) ?></td>
				<td>
					<?php
					if ( @fopen( VI_WNOTIFICATION_CACHE . 'test-log.log', 'a' ) ) {
						echo '<mark class="yes">&#10004; <code class="private">' . VI_WNOTIFICATION_CACHE . '</code></mark> ';
					} else {
						echo sprintf( '<mark class="error">&#10005; ' . __( 'To allow logging, make <code>%s</code> writable or define a custom <code>VI_WNOTIFICATION_CACHE</code>.', 'woocommerce-notification' ) . '</mark>', VI_WNOTIFICATION_CACHE );
					}
					?>

				</td>
			</tr>
			<tr>
				<td data-export-label="file_get_contents">file_get_contents</td>
				<td>
					<?php
					if ( function_exists( 'file_get_contents' ) ) {
						echo '<mark class="yes">&#10004; <code class="private"></code></mark> ';
					} else {
						echo '<mark class="error">&#10005; </mark>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td data-export-label="file_put_contents">file_put_contents</td>
				<td>
					<?php
					if ( function_exists( 'file_put_contents' ) ) {
						echo '<mark class="yes">&#10004; <code class="private"></code></mark> ';
					} else {
						echo '<mark class="error">&#10005; </mark>';
					}
					?>

				</td>
			</tr>
			<tr>
				<td data-export-label="mkdir">mkdir</td>
				<td>
					<?php
					if ( function_exists( 'mkdir' ) ) {
						echo '<mark class="yes">&#10004; <code class="private"></code></mark> ';
					} else {
						echo '<mark class="error">&#10005; </mark>';
					}
					?>

				</td>
			</tr>
			<tr>
				<td data-export-label="<?php esc_html_e( 'PHP Time Limit', 'woocommerce-notification' ) ?>"><?php esc_html_e( 'PHP Time Limit', 'woocommerce-notification' ) ?></td>
				<td><?php echo ini_get( 'max_execution_time' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="<?php esc_html_e( 'PHP Max Input Vars', 'woocommerce-notification' ) ?>"><?php esc_html_e( 'PHP Max Input Vars', 'woocommerce-notification' ) ?></td>

				<td><?php echo ini_get( 'max_input_vars' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="<?php esc_html_e( 'Memory Limit', 'woocommerce-notification' ) ?>"><?php esc_html_e( 'Memory Limit', 'woocommerce-notification' ) ?></td>

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
			'woocommerce-notification',
			esc_html__( 'System Status', 'woocommerce-notification' ),
			esc_html__( 'System Status', 'woocommerce-notification' ),
			'manage_options',
			'woocommerce-notification-status',
			array( $this, 'page_callback' )
		);

	}
}
