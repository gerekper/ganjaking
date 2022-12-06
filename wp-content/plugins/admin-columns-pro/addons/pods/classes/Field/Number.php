<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACP;
use ACP\Search;
use ACP\Sorting\Type\DataType;

class Number extends Field {

	use Editing\DefaultServiceTrait;

	public function filtering() {
		return new Filtering\Number( $this->column );
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key(), new DataType( DataType::NUMERIC ) );
	}

	public function search() {
		return new Search\Comparison\Meta\Number( $this->column->get_meta_key(), $this->column->get_meta_type() );
	}

}