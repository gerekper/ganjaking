<?php
declare( strict_types=1 );

namespace ACA\WC\ConditionalFormat\Formatter\User;

use AC\Column;
use ACP\ConditionalFormat\Formatter;

class TotalSalesFormatter extends Formatter\FloatFormatter {

	public function get_type(): string {
		return self::FLOAT;
	}

	public function format( string $value, int $id, Column $column, string $operator_group ): string {
		$totals = $column->get_raw_value( $id );

		return (string) reset( $totals );
	}

}