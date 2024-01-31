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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Contracts\Subscription_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Traits\Tracks_Subscription_Events;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use WC_Subscriptions;

defined( 'ABSPATH' ) or exit;

/**
 * The "subscription free trial end" event.
 *
 * @since 2.0.0
 */
class Subscription_Free_Trial_End extends GA4_Event implements Subscription_Event {

	use Tracks_Subscription_Events;


	/** @var string the event ID */
	public const ID = 'subscription_free_trial_end';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_scheduled_subscription_trial_end';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Subscription Free Trial End', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when the free trial ends for a subscription.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'subscription_free_trial_end';
	}


	/**
	 * @inheritdoc
	 *
	 * @param int|string $subscription_id
	 */
	public function track( $subscription_id = null ): void {

		$subscription  = wcs_get_subscription( $subscription_id );
		$payment_count = version_compare( WC_Subscriptions::$version, '2.6', '>=' ) ? $subscription->get_payment_count() : $subscription->get_completed_payment_count();

		$this->track_subscription_event( $subscription, [], [
			// handle trial conversions - if a subscription has more than a single completed payment, assume it converted
			'conversion_to_paid' => $payment_count > 1,
		] );
	}


}
