<?php

namespace ACA\Types\Field;

use ACA\Types\Field;
use ACA\Types\Filtering;
use ACA\Types\Search;
use ACP;

class Skype extends Field {

	public function is_serialized() {
		return true;
	}

	public function get_value( $id ) {
		return $this->format( $this->get_raw_value( $id ) );
	}

	public function filtering() {
		return new Filtering\Skype( $this->column );
	}

	public function search() {
		return new Search\Skype( $this->column->get_meta_key(), $this->column->get_meta_type() );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this->column );
	}

	/**
	 * @param array $skype
	 *
	 * @return string
	 */
	protected function format( $skype ) {
		if ( empty( $skype['skypename'] ) ) {
			return false;
		}

		return ac_helper()->html->link( 'skype:' . $skype['skypename'] . '?' . $skype['action'], $skype['skypename'] );
	}

}