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
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "started checkout" event.
 *
 * @since 2.0.0
 */
class Started_Checkout_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'started_checkout';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_after_checkout_form';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Started Checkout', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer starts the checkout.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'started checkout';
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		// bail if tracking is disabled
		if ( Tracking::do_not_track() ) {
			return;
		}

		if ( Tracking::not_page_reload() ) {

			$frontend = $this->get_frontend_handler_instance();

			// enhanced Ecommerce tracking
			$js = '';

			foreach ( WC()->cart->get_cart() as $item ) {

				// JS add product
				$js .= $frontend->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
			}

			// JS checkout action
			$args = array(
				'step'   => 1,
				'option' => is_user_logged_in() ? __( 'Registered User', 'woocommerce-google-analytics-pro' ) : __( 'Guest', 'woocommerce-google-analytics-pro' ),
			);

			$js .= $frontend->get_ec_action_js( 'checkout', $args );

			// enqueue JS
			$frontend->enqueue_js( 'event', $js );

			$frontend->js_record_event( $this->get_name(), [
				'eventCategory'  => 'Checkout',
				'nonInteraction' => true,
			] );
		}
	}


}
