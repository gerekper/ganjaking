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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Universal_Analytics;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Contracts\Subscription_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integrations\Subscriptions\Events\Traits\Tracks_Subscription_Events;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;
use WC_Subscription;

defined( 'ABSPATH' ) or exit;

/**
 * The "suspended subscription" event.
 *
 * Tracks subscription suspensions (on-hold status).
 *
 * @since 2.0.0
 */
class Suspended_Subscription extends Universal_Analytics_Event implements Subscription_Event {

	use Tracks_Subscription_Events;


	/** @var string the event ID */
	public const ID = 'suspended_subscription';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_subscription_status_on-hold';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Suspended Subscription', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer suspends their subscription.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'suspended subscription';
	}


	/**
	 * @inheritdoc
	 *
	 * @param WC_Subscription $subscription
	 */
	public function track( $subscription = null ): void {

		$this->track_subscription_event_in_ua( $subscription );
	}


}
