<?php

namespace ACP\Editing;

use AC\Column;
use ACP\Editing\Model\Disabled;

class ColumnBulkSettingsSetter {

	public function register( Column $column ) {
		if ( ! $column instanceof Editable ) {
			return;
		}

		$service = $column->editing();

		if ( ! $service || $column->get_setting( Settings\BulkEditing::NAME ) ) {
			return;
		}

		// legacy
		if ( $service instanceof Disabled ) {
			return;
		}

		$view = ( new ApplyFilter\View( $column, Service::CONTEXT_BULK, $service ) )->apply_filters( $service->get_view( Service::CONTEXT_SINGLE ) );

		if ( $view === false ) {
			return;
		}

		$column->add_setting( new Settings\BulkEditing( $column ) );
	}

}