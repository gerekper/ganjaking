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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Order_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Order_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "refund" event.
 *
 * @since 2.0.0
 */
class Refund_Event extends GA4_Event
{


	/** @var string the event ID */
	public const ID = 'refund';

	/** @var bool whether this is an admin event */
	protected bool $admin_event = true;

	/** @var bool whether this is a GA4 recommended event */
	protected bool $recommended_event = true;


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Refund', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when an order is refunded.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'refund';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks(): void {

		add_action( 'woocommerce_order_partially_refunded', [ $this, 'track' ], 10, 2 );
		add_action( 'woocommerce_order_fully_refunded', [ $this, 'track' ], 10, 2 );
	}


	/**
	 * @inheritdoc
	 *
	 * @param int $order_id the order ID
	 * @param int $refund_id the refund ID
	 */
	public function track( $order_id = null, $refund_id = null ): void {

		// don't track if the refund is already tracked
		if ( Order_Helper::is_order_tracked( $refund_id ) ) {
			return;
		}

		$order  = wc_get_order( $order_id );
		$refund = wc_get_order( $refund_id );

		$properties = array_merge( ['category' => 'Orders'], ( new Order_Event_Data_Adapter( $refund ) )->convert_from_source() );
		$identities = Order_Helper::get_order_identities( $order );

		if ( $this->record_via_api( $properties, $identities ) ) {

			// mark refund as tracked
			Order_Helper::set_order_tracked( $refund_id );
		}
	}


}
