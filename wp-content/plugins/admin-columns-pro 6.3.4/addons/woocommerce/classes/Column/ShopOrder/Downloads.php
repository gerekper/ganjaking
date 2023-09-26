<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACA\WC\Export;
use ACP;
use WC_DateTime;

class Downloads extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'column-wc-order_downloads' )
		     ->set_label( 'Downloads' )
		     ->set_group( 'woocommerce' );
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$downloadables = $this->get_raw_value( $id );

		if ( ! $downloadables ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $downloadables as $download ) {
			$product = wc_get_product( $download['product_id'] );

			$download_url = $product->get_file_download_path( $download['download_id'] );
			$label = ac_helper()->html->link( $download_url, $download['download_name'] ?: $download['product_name'] );

			$values[] = ac_helper()->html->tooltip( $label, $this->get_description( $download ) );
		}

		return implode( ', ', $values );
	}

	/**
	 * @param $download
	 *
	 * @return string
	 */
	private function get_description( $download ) {
		$product = wc_get_product( $download['product_id'] );

		$description = [
			wc_get_filename_from_url( $product->get_file_download_path( $download['download_id'] ) ),
		];

		if ( ! empty( $download['downloads_remaining'] ) ) {
			$description[] = __( 'Downloads remaining', 'woocommerce' ) . ': ' . $download['downloads_remaining'];
		}

		if ( ! empty( $download['access_expires'] ) ) {
			/* @var WC_DateTime $date */
			$date = $download['access_expires'];

			if ( $date->getTimestamp() > time() ) {
				$description[] = __( 'Access expires', 'woocommerce' ) . ': ' . human_time_diff( $date->getTimestamp() );
			}
		}

		return implode( '<br/>', $description );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order->get_downloadable_items();
	}

	public function export() {
		return new Export\ShopOrder\Downloads( $this );
	}

}