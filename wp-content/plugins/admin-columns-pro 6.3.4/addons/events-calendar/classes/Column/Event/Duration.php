<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Service\ColumnGroups;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;
use ACP\Sorting\Model\Post\Meta;
use ACP\Sorting\Sortable;
use ACP\Sorting\Type\DataType;

class Duration extends AC\Column\Meta
	implements Sortable, Formattable {

	use ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-ec-event_duration' )
		     ->set_label( __( 'Duration', 'codepress-admin-columns' ) )
		     ->set_group( ColumnGroups::EVENTS_CALENDAR );
	}

	public function get_meta_key() {
		return '_EventDuration';
	}

	public function sorting() {
		return new Meta( $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function get_value( $id ) {
		$value = human_time_diff( 0, $this->get_raw_value( $id ) );

		/**
		 * If it is an all day event, the raw value may be not correct
		 */
		if ( 'yes' === get_post_meta( $id, '_EventAllDay', true ) ) {
			$start_date = strtotime( get_post_meta( $id, '_EventStartDate', true ) );
			$end_date = strtotime( get_post_meta( $id, '_EventEndDate', true ) ) + 1;

			$value = human_time_diff( $start_date, $end_date );
		}

		return $value;
	}

}