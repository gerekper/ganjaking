<?php
declare( strict_types=1 );

namespace ACP\Export\ColumnRepository\Filter;

use AC\Column;
use AC\ColumnRepository\Filter;
use ACP\Export\ApplyFilter;
use ACP\Export\Exportable;
use ACP\Export\Settings;

class ExportableColumns implements Filter {

	public function filter( array $columns ): array {
		return array_filter( $columns, [ $this, 'is_exportable' ] );
	}

	private function is_exportable( Column $column ): bool {
		if ( $column instanceof Exportable && ! $column->export() ) {
			return false;
		}

		$setting = $column->get_setting( 'export' );

		$is_exportable = $setting instanceof Settings\Column
			? $setting->is_active()
			: true;

		return ( new ApplyFilter\ColumnActive( $column ) )->apply_filters( $is_exportable );
	}

}