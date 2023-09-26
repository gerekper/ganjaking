<?php

namespace ACA\WC\Column\Order;

use AC;
use ACP;
use WC_DateTime;

class Downloads extends AC\Column implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

	public function __construct() {
		$this->set_type( 'column-order_downloads' )
		     ->set_label( __( 'Downloads', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		$downloads = $order ? $order->get_downloadable_items() : [];

		if ( empty( $downloads ) ) {
			return $this->get_empty_char();
		}

		$values = [];

		foreach ( $downloads as $download ) {
			$product = wc_get_product( $download['product_id'] );

			$download_url = $product->get_file_download_path( $download['download_id'] );
			$label = ac_helper()->html->link( $download_url, $download['download_name'] ?: $download['product_name'] );

			$values[] = ac_helper()->html->tooltip( $label, $this->get_description( $download ) );
		}

		return implode( ', ', $values );
	}

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

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}