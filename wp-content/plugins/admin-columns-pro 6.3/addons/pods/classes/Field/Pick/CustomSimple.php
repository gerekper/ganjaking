<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Search;
use ACP;
use PodsField_Pick;
use PodsForm;

class CustomSimple extends Field\Pick {

	use Editing\DefaultServiceTrait;

	public function sorting() {
		$options = $this->get_options();
		natcasesort( $options );

		return ( new ACP\Sorting\Model\MetaMappingFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), array_keys( $options ) );
	}

	public function filtering() {
		return new Filtering\PickCustom( $this->column() );
	}

	public function search() {
		return new Search\Pick( $this->column()->get_meta_key(), $this->column()->get_meta_type(), $this->get_options() );
	}

	public function get_options() {
		$_field = PodsForm::field_loader( $this->get( 'type' ) );

		if ( ! $_field instanceof PodsField_Pick ) {
			return [];
		}

		return $_field->get_field_data( $this->column->get_pod_field() );
	}

}