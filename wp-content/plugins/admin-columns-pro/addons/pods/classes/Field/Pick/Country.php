<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Search;
use ACP;

class Country extends Field\Pick {

	use Editing\DefaultServiceTrait;

	public function get_options() {
		return $this->get_pick_field()->data_countries();
	}

	public function sorting() {
		$options = $this->get_options();

		return ( new ACP\Sorting\Model\MetaMappingFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), array_keys( $options ) );
	}

	public function filtering() {
		return new Filtering\Pick( $this->column() );
	}

	public function search() {
		return new Search\Pick( $this->column()->get_meta_key(), $this->column()->get_meta_type(), $this->get_options() );
	}

}