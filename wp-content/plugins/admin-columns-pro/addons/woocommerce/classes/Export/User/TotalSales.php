<?php

namespace ACA\WC\Export\User;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\User\TotalSales $column
 * @since 2.2.1
 */
class TotalSales extends ACP\Export\Model {

	public function get_value( $id ) {
		$totals = $this->column->get_raw_value( $id );

		if ( ! $totals ) {
			return false;
		}

		$values = [];

		foreach ( $totals as $currency => $amount ) {
			$values[] = get_woocommerce_currency_symbol( $currency ) . $amount;
		}

		return implode( ', ', $values );
	}

}