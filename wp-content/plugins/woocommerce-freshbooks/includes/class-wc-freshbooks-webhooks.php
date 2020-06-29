<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Webhooks class
 *
 * Handles parsing/verifying webhooks from FreshBooks
 *
 * @since 3.0
 */
class WC_FreshBooks_Webhooks {


	/**
	 * Add actions
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// use the WC IPN handler URL
		add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'dispatch' ) );

		// verify callback sent when webhooks are initially setup
		add_action( 'wc_freshbooks_webhook_verify_callback', array( $this, 'verify_callback' ), 10, 2 );
	}


	/**
	 * Dispatches actions for the received webhook after parsing & verification is complete.
	 *
	 * @internal
	 *
	 * @since 3.0
	 */
	public function dispatch() {

		wc_freshbooks()->log( sprintf( 'Webhook data: %s', print_r( $_POST, true ) ) );

		// event name/object ID required
		if ( empty( $_POST['name'] ) || empty( $_POST['object_id'] ) ) {

			$this->dispatch_complete( 'Missing event name or object ID' );
		}

		// split events like 'invoice.create' to noun/verb
		list( $event_name, $event_action ) = explode( '.', $_POST['name'] );

		/**
		 * FreshBooks Webhook Received
		 *
		 * Fired when a webhook is received from FreshBooks, useful for binding
		 * some code to a set of events or actions
		 *
		 * @link see list at http://developers.freshbooks.com/docs/callbacks/
		 *
		 * @since 3.0
		 * @param string $event_name webhook name, e.g. `invoice`
		 * @param string $event_action webhook action, e.g. `create`
		 * @param string $object_id ID for the event object
		 * @param array $data associated data for the event
		 */
		do_action( 'wc_freshbooks_webhook', $event_name, $event_action, $_POST['object_id'], $_POST );

		/**
		 * FreshBooks Event Name Webhook Received
		 *
		 * Fired when a webhook is received from FreshBooks, useful for binding
		 * some code to a set of events or actions
		 *
		 * @link see list at http://developers.freshbooks.com/docs/callbacks/
		 *
		 * @since 3.0
		 * @param string $event_action webhook action, e.g. `create`
		 * @param string $object_id ID for the event object
		 * @param array $data associated data for the event
		 */
		do_action( "wc_freshbooks_webhook_{$event_name}", $event_action, $_POST['object_id'], $_POST );

		/**
		 * FreshBooks Event Name > Event Action Webhook Received
		 *
		 * Fired when a webhook is received from FreshBooks, useful for binding
		 * some code to a specific event, like `invoice.update`
		 *
		 * @link see list at http://developers.freshbooks.com/docs/callbacks/
		 *
		 * @since 3.0
		 * @param string $object_id ID for the event object
		 * @param array $data associated data for the event
		 */
		do_action( "wc_freshbooks_webhook_{$event_action}_{$event_name}", $_POST['object_id'], $_POST );

		$this->dispatch_complete();
	}


	/**
	 * Log any errors in processing and send a 200 OK header back to FreshBooks
	 *
	 * @since 3.0
	 *
	 * @param string $error
	 */
	private function dispatch_complete( $error = '' ) {

		if ( $error ) {

			wc_freshbooks()->log( sprintf( 'Webhook processing failed: %1$s, data: %2$s', $error, print_r( $_POST, true ) ) );
		}

		// everything's under control, situation normal
		status_header( 200 );

		exit;
	}


	/**
	 * Verifies the webhook once it's been added.
	 *
	 * After creating a webhook through the FreshBooks API, it sends a POST to the URL specified, along with a verifier that must then be POST'ed back to verify ownership of the webhook URL.
	 * @link http://developers.freshbooks.com/webhooks/
	 * @internal
	 *
	 * @since 3.0
	 *
	 * @param string $callback_id
	 * @param array $data
	 */
	public function verify_callback( $callback_id, $data ) {

		try {

			wc_freshbooks()->get_api()->verify_webhook( $callback_id, $data['verifier'] );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( sprintf( 'Callback verification failed: %s', $e->getMessage() ) );
		}
	}


}
