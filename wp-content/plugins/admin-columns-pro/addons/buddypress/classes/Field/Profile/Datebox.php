<?php

namespace ACA\BP\Field\Profile;

use ACP;
use ACA\BP\Editing;
use ACA\BP\Field\Profile;
use ACA\BP\Filtering;
use ACA\BP\Search;
use ACA\BP\Sorting;
use ACP\Sorting\Type\DataType;

class Datebox extends Profile {

	public function editing() {
		return new ACP\Editing\Service\Date(
			( new ACP\Editing\View\Date() )->set_clear_button( true ),
			new Editing\Storage\Profile( $this->column->get_buddypress_field_id() ),
			'Y-m-d 00:00:00'
		);
	}

	public function filtering() {
		return new Filtering\Profile\Date( $this->column );
	}

	public function search() {
		return new Search\Profile\Date( $this->column->get_buddypress_field_id() );
	}

	public function sorting() {
		return new Sorting\Profile( $this->column, new DataType( DataType::DATETIME ) );
	}

}