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

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Product_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Product_Item_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\Contracts\Deferred_Event;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;

defined( 'ABSPATH' ) or exit;

/**
 * The "select item" event.
 *
 * This event may be registered multiple times on a single page.
 *
 * @since 2.0.0
 */
class Select_Item_Event extends GA4_Event implements Deferred_Event {


	/** @var string the event ID */
	public const ID = 'select_item';

	/** @var string the event trigger action hook  */
	protected string $trigger_hook = 'woocommerce_before_shop_loop_item';

	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'Select Item', 'woocommerce-google-analytics-pro' );
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

		return 'select_item';
	}


	/**
	 * @inheritdoc
	 *
	 * This method will be called once per each product listed on a page.
	 */
	public function track() : void {

		global $product;

		$this->record_via_js( [
			'category'       => 'Products',
			'item_list_name' => Product_Helper::get_list_type(),
			'items'          => [ ( new Product_Item_Event_Data_Adapter( $product ) )->convert_from_source() ],
		] );
	}


	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public function get_trigger_js(): string {

		global $product;

		$product_id = esc_js( $product->get_id() );

		return <<<JS
		document.querySelector( '.products .post-{$product_id} a' ).addEventListener('click', function(event) {
			if ( ! event.target.classList.contains( 'add_to_cart_button' ) ) {
				"__INSERT_TRACKING_CALL_HERE__";
			}
		} );
		JS;
	}


}
