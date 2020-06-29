<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @since 1.4.0
 */
class WC_Tab_Manager_Ajax_Events {


	/**
	 * Constructor function.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		// Set up AJAX action callbacks.
		add_action( 'wp_ajax_wc_tab_manager_get_editor', array( $this, 'wc_tab_manager_get_editor' ) );
		add_action( 'wp_ajax_wc_tab_manager_batch_update_products', array( $this, 'ajax_batch_update_products' ) );
	}


	/**
	 * Returns the main WC_Tab_Manager. @see wc_tab_manager()
	 *
	 * @since 1.4.0
	 */
	public function get_plugin() {
		return wc_tab_manager();
	}


	/**
	 * Gets a quicktags editor
	 *
	 * @access public
	 */
	public function wc_tab_manager_get_editor() {
		ob_start();

		check_ajax_referer( 'get-editor', 'security' );

		$size = esc_attr( $_POST['size'] );

		// Call `wp_editor` twice to get rid of `$editor_buttons_css`.
		ob_start();

		wp_editor( '', 'producttabcontent' . $size, array( 'textarea_name' => 'product_tab_content[' . $size . ']', 'tinymce' => false, 'textarea_rows' => 10 ) );

		ob_clean();

		wp_editor( '', 'producttabcontent' . $size, array( 'textarea_name' => 'product_tab_content[' . $size . ']', 'tinymce' => false, 'textarea_rows' => 10 ) );

		$content = ob_get_contents();

		ob_end_clean();

		echo $content;

		// Quit out.
		exit();
	}


	/**
	 * Processes a batch of products via AJAX.
	 *
	 * @since  1.4.0
	 */
	public function ajax_batch_update_products() {

		check_ajax_referer( 'wc_tab_manager_nonce', 'nonce' );

		ignore_user_abort( true );

		@set_time_limit( 0 );

		$step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 0;

		$args = array(
			'step' => $step,
		);

		$response = $this->get_plugin()->batch_update_products( $args );

		wp_send_json_success( $response );
	}


}
