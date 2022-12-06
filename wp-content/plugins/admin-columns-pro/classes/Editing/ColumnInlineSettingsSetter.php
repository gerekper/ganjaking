<?php

namespace ACP\Editing;

use AC\Column;
use ACP\Editing\Model\Disabled;

class ColumnInlineSettingsSetter {

	public function register( Column $column ) {
		if ( ! $column instanceof Editable ) {
			return;
		}

		$service = $column->editing();

		if ( ! $service || $column->get_setting( Settings::NAME ) ) {
			return;
		}

		// legacy
		if ( $service instanceof Disabled ) {
			return;
		}

		$filter = new ApplyFilter\View( $column, Service::CONTEXT_SINGLE, $service );
		$view = $filter->apply_filters( $service->get_view( Service::CONTEXT_SINGLE ) );

		if ( ! $view instanceof View ) {
			return;
		}

		$column->add_setting( new Settings( $column ) );
	}

}