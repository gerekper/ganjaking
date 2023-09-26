<?php

namespace ACA\WC\Editing\ShopOrder;

use ACP;
use ACP\Editing\Service;
use ACP\Editing\View;

class Status implements Service {

	public function get_view( string $context ): ?View {
		return new ACP\Editing\View\Select( wc_get_order_statuses() );
	}

	public function get_value( int $id ) {
		$order = wc_get_order( $id );

		if ( ! $order ) {
			return null;
		}

		$status = $order->get_status();

		if ( strpos( $status, 'wc-' ) !== 0 ) {
			$status = 'wc-' . $status;
		}

		return $status;
	}

	public function update( int $id, $data ): void {
		$order = wc_get_order( $id );
		if ( $order ) {
			$order->update_status( $data );
		}
	}

}
