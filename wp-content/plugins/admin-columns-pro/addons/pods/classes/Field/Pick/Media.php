<?php

namespace ACA\Pods\Field\Pick;

use AC\Collection;
use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Export;
use ACA\Pods\Field;
use ACP;
use ACP\Search\Comparison;

class Media extends Field\Pick {

	use Editing\DefaultServiceTrait;

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function get_value( $id ) {
		return $this->column->get_formatted_value( new Collection( $this->get_raw_value( $id ) ) );
	}

	public function get_raw_value( $id ) {
		return (array) $this->get_ids_from_array( parent::get_raw_value( $id ) );
	}

	public function export() {
		return new Export\File( $this->column() );
	}

	public function get_dependent_settings() {
		return [
			new Settings\Column\Image( $this->column() ),
		];
	}

}