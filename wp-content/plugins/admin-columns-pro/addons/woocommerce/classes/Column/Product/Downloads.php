<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Export;
use ACP;
use WC_Product_Download;

/**
 * @since 1.0
 */
class Downloads extends AC\Column
	implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-product_downloads' )
		     ->set_label( __( 'Downloads', 'woocommerce' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$values = [];

		$description = $this->get_description( $id );

		foreach ( $this->get_raw_value( $id ) as $product_id => $download ) {

			$label = ac_helper()->html->link( $download->get_file(), $download->get_name() );
			$tooltip = array_merge( [ wc_get_filename_from_url( $download->get_file() ) ], $description );

			$values[] = ac_helper()->html->tooltip( $label, implode( '<br/>', $tooltip ) );
		}

		if ( ! $values ) {
			return $this->get_empty_char();
		}

		return implode( $this->get_separator(), $values );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	private function get_description( $id ) {
		$description = [];

		$product = wc_get_product( $id );

		if ( ( $limit = $product->get_download_limit() ) > 0 ) {
			$description[] = __( 'Download limit', 'woocommerce' ) . ': ' . $limit;
		}

		if ( ( $days = $product->get_download_expiry() ) > 0 ) {
			$description[] = __( 'Download expiry', 'woocommerce' ) . ': ' . sprintf( _n( '%s day', '%s days', $days ), $days );
		}

		return $description;
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Product_Download[]
	 */
	public function get_raw_value( $id ) {
		return wc_get_product( $id )->get_downloads();
	}

	public function export() {
		return new Export\Product\Downloads( $this );
	}

}