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
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters\Product_Item_Event_Data_Adapter;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Events\GA4_Event;
use WC_Product;

defined( 'ABSPATH' ) or exit;

/**
 * The "view item list" event.
 *
 * @since 2.0.0
 */
class View_Item_List_Event extends GA4_Event {


	/** @var string the event ID */
	public const ID = 'view_item_list';

	/** @var WC_Product[] the products in the list to track */
	protected array $products = [];


	/**
	 * @inheritdoc
	 */
	public function get_form_field_title(): string {

		return __( 'View Item List', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_form_field_description(): string {

		return __( 'Triggered when a customer views a list of products, for example the shop page or related products under a single product page.', 'woocommerce-google-analytics-pro' );
	}


	/**
	 * @inheritdoc
	 */
	public function get_default_name(): string {

		return 'view_item_list';
	}


	/**
	 * @inheritdoc
	 */
	public function register_hooks() : void {

		add_action( 'woocommerce_before_shop_loop_item', [ $this, 'remember_product' ] );
		add_action( 'woocommerce_product_loop_end', [ $this, 'trigger_tracking' ] );
	}


	/**
	 * Remember each product that is being looped over inside a product loop.
	 *
	 * This is necessary because at the end of the loop, we no longer have access to the list of products that were looped over.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function remember_product(): void {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$this->products[] = $product;
	}


	/**
	 * Triggers the tracking call at the end of each product loop.
	 *
	 * We're hooking into a filter because there is no equivalent action hook that is guaranteed to exist on all product loops.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $loop_end the loop end HTML
	 * @return string
	 */
	public function trigger_tracking( $loop_end = '' ) : string {

		$this->track();

		return $loop_end;
	}


	/**
	 * @inheritdoc
	 */
	public function track() : void {

		if ( empty( $this->products ) || ! $this->should_track_item_list_view() ) {
			return;
		}

		if ( Tracking::not_page_reload() ) {

			$this->record_via_js( [
				'category'       => 'Products',
				'item_list_name' => Product_Helper::get_list_type(),
				'items'          => array_values( array_map( static function( $product ) {

					return ( new Product_Item_Event_Data_Adapter( $product ) )->convert_from_source();

				}, $this->products ) ),
			] );
		}
	}


	/**
	 * Determines whether we should track the item list view on the current page.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function should_track_item_list_view() : bool {

		$track_on = (array) wc_google_analytics_pro()->get_integration()->get_option( 'track_item_list_views_on', [] );

		// bail if product impression tracking is disabled on product pages, and we're on a product page
		// note: this doesn't account for the [product_page] shortcode unfortunately
		if ( ! in_array( 'single_product_pages', $track_on, true ) && is_product() ) {
			return false;
		}

		// bail if product impression tracking is disabled on product archive pages, and we're on an archive page
		if ( ! in_array( 'archive_pages', $track_on, true ) && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
			return false;
		}

		return true;
	}


}
