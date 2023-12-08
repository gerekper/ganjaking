<?php

namespace ACA\BP\Field\Profile;

use ACA\BP\Editing;
use ACA\BP\Field\Profile;
use ACA\BP\Filtering;
use ACA\BP\Search;
use ACA\BP\Sorting;
use ACP;
use ACP\Editing\Service\Basic;
use ACP\Sorting\Type\DataType;

class Number extends Profile {

	public function editing() {
		return new Basic(
			( new ACP\Editing\View\Number() )->set_clear_button( true ),
			new Editing\Storage\Profile( $this->column->get_buddypress_field_id() )
		);
	}

	public function search() {
		return new Search\Profile\Number( $this->column->get_buddypress_field_id() );
	}

	public function sorting() {
		return new Sorting\Profile( $this->column, new DataType( DataType::NUMERIC ) );
	}

}