<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Traits;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_AJAX_Event;

defined( 'ABSPATH' ) or exit;

/**
 * AJAX trigger trait for tracking events.
 *
 * Provides base functionality for triggering events via AJAX calls.
 *
 * @see Deferred_AJAX_Event
 *
 * @since 2.0.0
 */
trait Has_Deferred_AJAX_Trigger {


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		if ( ! $this->trigger_hook || ! $this->ajax_action ) {
			return;
		}

		add_action( $this->trigger_hook, [ $this, 'trigger_via_ajax' ] );

		add_action( "wp_ajax_{$this->ajax_action}", [ $this, 'track' ] );
		add_action( "wp_ajax_nopriv_{$this->ajax_action}", [ $this, 'track' ] );
	}


	/**
	 * @inheritdoc
	 */
	public function trigger_via_ajax(): void {

		$action = esc_js( $this->ajax_action );
		$nonce  = esc_js( wp_create_nonce( $action ) );

		$ajax_call = <<<JS
			((data) => {
				$.ajax({
					type: 'POST',
					url:  wc_ga_pro.ajax_url,
					data: Object.assign({
						action:   '{$action}',
						security: '{$nonce}'
					}, data)
				})
			})
		JS;

		$ajax_call = str_replace( '"__INSERT_AJAX_CALL_HERE__"', $ajax_call, $this->get_trigger_js() );

		wc_google_analytics_pro()->get_tracking_instance()->get_frontend_handler_instance()->enqueue_tracking_call( $ajax_call );
	}


}
