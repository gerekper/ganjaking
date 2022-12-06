<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use AC\Collection;
use AC\Settings\Column\NumberOfItems;
use AC\Settings\Column\Separator;
use ACA\WC\Filtering;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.3.1
 */
class Product extends AC\Column implements ACP\Filtering\Filterable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_group( 'woocommerce' )
		     ->set_type( 'column-wc-product' )
		     ->set_label( __( 'Product', 'woocommerce' ) );
	}

	private function get_product_or_variation_ids_by_order( $order_id ) {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT om.order_item_id as oid, om.meta_value as product_id, om2.meta_value as variation_id
				FROM {$wpdb->prefix}woocommerce_order_items AS oi
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON ( oi.order_item_id = om.order_item_id )
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om2 ON ( oi.order_item_id = om2.order_item_id )
				WHERE om.meta_key = '_product_id' 
				AND om2.meta_key ='_variation_id'
				AND oi.order_id = %d
				",
				(int) $order_id
			)
		);

		$product_ids = [];

		foreach ( $results as $result ) {
			$product_ids[] = $result->variation_id ?: $result->product_id;
		}

		return $product_ids;
	}

	public function get_raw_value( $order_id ) {
		return new Collection( $this->get_product_or_variation_ids_by_order( $order_id ) );
	}

	public function get_value( $id ) {
		$values = [];

		foreach ( $this->get_raw_value( $id ) as $product_id ) {
			$values[] = $this->get_formatted_value( $product_id, $product_id );
		}

		$values = array_filter( $values );

		if ( empty( $values ) ) {
			return $this->get_empty_char();
		}

		return ac_helper()->html->more( $values, $this->get_items_limit(), $this->get_separator() );
	}

	/**
	 * @return string
	 */
	public function get_separator() {
		$setting = $this->get_setting( 'separator' );

		return $setting instanceof Separator
			? $setting->get_separator_formatted()
			: parent::get_separator();
	}

	/**
	 * @return int
	 */
	private function get_items_limit() {
		$setting_limit = $this->get_setting( NumberOfItems::NAME );

		return $setting_limit instanceof NumberOfItems
			? $setting_limit->get_number_of_items()
			: 0;
	}

	public function filtering() {
		if ( in_array( $this->get_product_property(), [ Settings\ShopOrder\Product::PROPERTY_TITLE, Settings\ShopOrder\Product::TYPE_SKU ], true ) ) {
			return new Filtering\ShopOrder\Product( $this );
		}

		return new ACP\Filtering\Model\Disabled( $this );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

	public function search() {
		return new Search\ShopOrder\Product( $this->get_post_type() );
	}

	public function register_settings() {
		$this->add_setting( new Settings\ShopOrder\Product( $this ) )
		     ->add_setting( new AC\Settings\Column\NumberOfItems( $this ) )
		     ->add_setting( new AC\Settings\Column\Separator( $this ) );
	}

	public function get_product_property() {
		return $this->get_setting( Settings\ShopOrder\Product::NAME )->get_value();
	}

}