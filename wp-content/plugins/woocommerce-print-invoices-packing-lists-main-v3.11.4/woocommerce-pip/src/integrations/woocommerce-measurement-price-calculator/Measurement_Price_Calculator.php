<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\PIP\Integration;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Measurement Price Calculator integration.
 *
 * @since 3.9.2
 */
class Measurement_Price_Calculator {


	/**
	 * Hooks into plugin.
	 *
	 * @since 3.9.2
	 */
	public function __construct() {

		// calculate table item correct weight row cell
		add_filter( 'wc_pip_document_table_row_cells', [ $this, 'calculated_item_weight_cell' ], 10, 5 );

		// calculate order item weight number
		add_filter( 'wc_pip_order_item_weight', [ $this, 'calculated_item_weight_number' ], 10, 3 );
	}


	/**
	 * Calculates the package MPC item's correct weight amount.
	 *
	 * @internal
	 *
	 * @since 3.9.2
	 *
	 * @param float $item_weight order item weight
	 * @param array|int|string $item item data
	 * @param \WC_Product|\WC_Order_Item_Product $product product object
	 * @return float
	 */
	public function calculated_item_weight_number( $item_weight, $item, $product ) {

		$item_product = $this->get_order_item_product( $product );

		if ( ! $item_product || false === $this->has_product_pricing_calculated_weight_enabled( $item_product ) ) {

			// bail if product doesn't have calculated weight enabled
			return $item_weight;
		}

		$item_weight_data = $this->get_order_item_weight( $item );

		if ( is_array( $item_weight_data ) && isset( $item_weight_data['value'] ) ) {
			$item_weight = (float) $item_weight_data['value'];
		}

		return $item_weight;
	}


	/**
	 * Calculates the package MPC item's correct weight cell output.
	 *
	 * @internal
	 *
	 * @since 3.9.2
	 *
	 * @param array $item_data document item row data
	 * @param string $document_type document type
	 * @param string $item_id item ID
	 * @param array $item item data
	 * @param \WC_Product $product product object
	 * @return array
	 */
	public function calculated_item_weight_cell( $item_data, $document_type, $item_id, $item, $product ) {

		if ( false === $this->is_packing_list_document( $document_type ) ) {

			// bail if document isn't a packing list
			return $item_data;
		}

		$item_product = $this->get_order_item_product( $product );

		if ( ! $item_product || false === $this->has_product_pricing_calculated_weight_enabled( $product ) ) {

			// bail if product doesn't have calculated weight enabled
			return $item_data;
		}

		$item_weight_data = $this->get_order_item_weight( $item_id );

		if ( is_array( $item_weight_data ) && isset( $item_weight_data['value'], $item_weight_data['unit'] ) ) {
			$item_data['weight'] = $item_weight_data['value'] . ' ' . $item_weight_data['unit'];
		}

		return $item_data;
	}


	/**
	 * Gets the order item's calculated weight data.
	 *
	 * @since 3.9.2
	 *
	 * @param string|array $item_id product item
	 * @return array|null
	 */
	private function get_order_item_weight( $item_id ) {

		$weight_data = null;

		try {

			if ( is_string( $item_id ) || is_numeric( $item_id ) ) {

				// get data via meta query
				$measurement_data = wc_get_order_item_meta( $item_id, '_measurement_data' );

			} else {

				// get data via array accessor
				$measurement_data = $item_id['_measurement_data'];
			}

		} catch ( \Exception $e ) {

			return null;
		}

		if ( is_array( $measurement_data ) && isset( $measurement_data['weight'] ) ) {

			$weight_data = $measurement_data['weight'];
		}

		return $weight_data;
	}


	/**
	 * Gets the WooCommerce product instance.
	 *
	 * @since 3.9.2
	 *
	 * @param \WC_Product|\WC_Order_Item_Product $object product object
	 * @return \WC_Product|null
	 */
	private function get_order_item_product( $object ) {

		if ( $object instanceof \WC_Product ) {
			return $object;
		}

		if ( $object instanceof \WC_Order_Item_Product ) {

			$product = $object->get_product();

			if ( $product instanceof \WC_Product ) {
				return $product;
			}
		}

		return null;
	}


	/**
	 * Checks if given document type is packing list.
	 *
	 * @since 3.9.2
	 *
	 * @param string $document_type document type
	 * @return bool
	 */
	private function is_packing_list_document( $document_type ) {

		return 'packing-list' === $document_type;
	}


	/**
	 * Checks if the given product object has pricing weight calculation enabled or not.
	 *
	 * @since 3.9.2
	 *
	 * @param \WC_Product $product product object
	 * @return bool
	 */
	private function has_product_pricing_calculated_weight_enabled( \WC_Product $product ) {

		return \WC_Price_Calculator_Product::pricing_calculated_weight_enabled( $product );
	}


}
