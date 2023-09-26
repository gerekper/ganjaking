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

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking\Adapters;

use Automattic\WooCommerce\Utilities\NumberUtil;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Product_Helper;
use WC_Product;

defined( 'ABSPATH' ) or exit;

/**
 * The Product Item Event Data Adapter class.
 *
 * @since 2.0.0
 */
class Product_Item_Event_Data_Adapter extends Event_Data_Adapter {


	/** @var WC_Product the source product */
	protected WC_Product $product;


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Product $product
	 */
	public function __construct( WC_Product $product) {

		$this->product = $product;
	}


	/**
	 * Converts the source into an array.
	 *
	 * @since 2.0.0
	 *
	 * @param float|int $quantity
	 * @param array<string, mixed> $variation
	 * @return array<string, mixed>
	 */
	public function convert_from_source( $quantity = 1, array $variation = [] ) : array {

		$product = $this->product;

		$data = [
			'item_id'      => Product_Helper::get_product_identifier( $product ),
			'item_name'    => $product->get_name(),
			'item_variant' => Product_Helper::get_product_variation_attributes( $product, $variation ),
			// we should always use per-unit prices, hence using qty=1 below when getting the price
			'price'        => NumberUtil::round( wc_get_price_excluding_tax( $product,  [ 'qty' => 1, 'price' => $product->get_price() ] ), wc_get_price_decimals() ),
			'quantity'     => $quantity,
		];

		$index = '';

		/** @link https://developers.google.com/analytics/devguides/collection/protocol/ga4/reference/events#view_item_item */
		foreach ( Product_Helper::get_hierarchical_categories( $product ) as $category ) {

			$data[ 'item_category' . $index ] = $category->name;

			$index ? $index++ : $index = 2; // index only appended starting from 2nd category

			// GA supports up to 5 categories
			if ( $index > 5 ) {
				break;
			}
		}

		return $data;
	}


}
