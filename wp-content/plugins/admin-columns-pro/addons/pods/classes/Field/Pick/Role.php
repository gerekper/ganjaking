<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Search;
use ACP;
use PodsField_Pick;

class Role extends Field\Pick {

	use Editing\DefaultServiceTrait;

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() );
	}

	public function filtering() {
		return new Filtering\Pick( $this->column() );
	}

	public function search() {
		return new Search\Pick( $this->column()->get_meta_key(), $this->column()->get_meta_type(), $this->get_options() );
	}

	public function get_options() {
		if ( ! class_exists( 'PodsField_Pick' ) ) {
			return [];
		}

		$pod = new PodsField_Pick();

		return $pod->data_roles();
	}

}