<?php

namespace ACA\Pods\Setting;

use AC;
use ACA\Pods\Column;
use PodsField_Date;
use PodsForm;

/**
 * @property Column $column
 */
class Date extends AC\Settings\Column\Date {

	public function __construct( Column $column ) {
		parent::__construct( $column );

		switch ( $column->get_field()->get( 'type' ) ) {
			case 'datetime':
				$date_format = $this->format_pods_date( $column->get_field()->get_option( 'datetime_format' ) ) . ' H:i';
				break;
			default:
				$date_format = $this->format_pods_date( $column->get_field()->get_option( 'date_format' ) );
		}

		$this->set_default( $date_format );
	}

	private function format_pods_date( $date_format ) {
		/* @var PodsField_Date $pods_field */
		$pods_field = PodsForm::field_loader( $this->column->get_field()->get( 'type' ) );
		$pods_date_formats = $pods_field->get_date_formats();

		if ( $pods_date_formats[ $date_format ] ) {
			$date_format = $pods_date_formats[ $date_format ];
		}

		return $date_format;
	}

}