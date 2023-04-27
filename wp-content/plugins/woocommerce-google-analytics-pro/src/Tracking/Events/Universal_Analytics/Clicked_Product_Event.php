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

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Product_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Universal_Analytics_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "clicked product" event.
 *
 * @since 2.0.0
 */
class Clicked_Product_Event extends Universal_Analytics_Event {


	/** @var string the event ID */
	public const ID = 'clicked_product';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_before_shop_loop_item';


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Clicked Product', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer clicks a product in listing, such as search results or related products.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'clicked product';
	}


	/**
	 * @inheritdoc
	 */
	public function track(): void {

		if ( Tracking::do_not_track() ) {
			return;
		}

		$frontend = $this->get_frontend_handler_instance();

		global $product;

		$properties = [
			'eventCategory' => 'Products',
			'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
		];

		if ($parent_id = $product->get_parent_id()) {
			$product_id = $parent_id;
		} else {
			$product_id = $product->get_id();
		}

		$js =
			"document.querySelector( '.products .post-{$product_id} a' ).addEventListener('click', function(event) {
				if ( event.target.classList.contains( 'add_to_cart_button' ) ) {
					return;
				}
				" . $frontend->get_ec_add_product_js( $product_id ) . $frontend->get_ec_action_js( 'click', ['list' => Product_Helper::get_list_type()] ) . $frontend->get_event_tracking_js( $this->get_name(), $properties ) . '
			} );';

		$frontend->enqueue_js( 'event', $js );
	}


}
