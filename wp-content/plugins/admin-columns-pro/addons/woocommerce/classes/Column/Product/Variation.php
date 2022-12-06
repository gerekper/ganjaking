<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Export;
use ACA\WC\Sorting;
use ACP;
use WC_Product_Variable;
use WC_Product_Variation;

/**
 * @since 1.3
 */
class Variation extends AC\Column
	implements AC\Column\AjaxValue, ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-variation' )
		     ->set_label( __( 'Variations', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$variations = $this->get_raw_value( $id );

		if ( ! $variations ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', count( $variations ) ), count( $variations ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	public function get_raw_value( $post_id ) {
		return $this->get_variations( $post_id );
	}

	public function get_ajax_value( $id ) {
		$value = false;

		$variations = $this->get_variations( $id );

		if ( $variations ) {
			$values = [];

			foreach ( $variations as $variation ) {

				$html = $this->get_variation_label( $variation );
				$html .= $this->get_variation_stock_status( $variation );
				$html .= $this->get_variation_price( $variation );

				$values[] = '<div class="variation">' . $html . '</div>';
			}

			$value = implode( $values );
		}

		return $value;
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_variation_label( WC_Product_Variation $variation ) {
		$label = $variation->get_id();

		$attributes = $variation->get_variation_attributes();

		if ( $attributes ) {
			$label = implode( ' | ', array_filter( $attributes ) );
		}

		return '<span class="label" ' . ac_helper()->html->get_tooltip_attr( $this->get_tooltip_variation( $variation ) ) . '">' . $label . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_variation_stock_status( WC_Product_Variation $variation ) {
		if ( ! $variation->is_in_stock() ) {
			return '<span class="stock outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</span>';
		}

		$stock = __( 'In stock', 'woocommerce' );

		$qty = $variation->get_stock_quantity();

		if ( $qty ) {
			$stock .= ' <span class="qty">' . $qty . '</span>';
		}

		return '<span class="stock instock">' . $stock . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return bool|string
	 */
	protected function get_variation_price( WC_Product_Variation $variation ) {
		$price = $variation->get_price_html();

		if ( ! $price ) {
			return false;
		}

		return '<span class="price">' . $variation->get_price_html() . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_tooltip_variation( $variation ) {
		$tooltip = [];

		if ( $variation->get_sku() ) {
			$tooltip[] = __( 'SKU', 'woocommerce' ) . ' ' . $variation->get_sku();
		}

		if ( $variation->get_weight() ) {
			$tooltip[] = (float) $variation->get_weight() . get_option( 'woocommerce_weight_unit' );
		}

		$tooltip[] = $this->get_dimensions( $variation );
		$tooltip[] = $variation->get_shipping_class();

		$tooltip[] = '#' . $variation->get_id();

		return implode( ' | ', array_filter( $tooltip ) );
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return bool|string
	 */
	protected function get_dimensions( $variation ) {
		$dimensions = [
			'length' => $variation->get_length(),
			'width'  => $variation->get_width(),
			'height' => $variation->get_height(),
		];

		if ( count( array_filter( $dimensions ) ) <= 0 ) {
			return false;
		}

		return implode( ' x ', $dimensions ) . ' ' . get_option( 'woocommerce_dimension_unit' );
	}

	/**
	 * @param $product_id
	 *
	 * @return array
	 */
	public function get_variation_ids( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product_Variable ) {
			return [];
		}

		return $product->get_children();
	}

	/**
	 * @param int $product_id
	 *
	 * @return WC_Product_Variation[]
	 */
	protected function get_variations( $product_id ) {
		$variations = [];

		foreach ( $this->get_variation_ids( $product_id ) as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( $variation->exists() ) {
				$variations[] = $variation;
			}
		}

		return $variations;
	}

	public function sorting() {
		return new Sorting\Product\Variation();
	}

	public function export() {
		return new Export\Product\Variation( $this );
	}

}