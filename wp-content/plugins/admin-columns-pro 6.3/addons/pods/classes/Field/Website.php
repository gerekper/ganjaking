<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Filtering;
use ACP\Search;

class Website extends Field {

	use Editing\DefaultServiceTrait,
		Sorting\DefaultSortingTrait;

	public function get_value( $id ) {
		$field = $this->column->get_pod_field();
		$target = $field['options']['website_new_window'] ? '_blank' : '_self';
		$url = $this->get_raw_value( $id );

		return ac_helper()->html->link( $url, str_replace( [ 'http://', 'https://' ], '', $url ), [ 'target' => $target ] );
	}

	public function filtering() {
		return new Filtering\Model\Meta( $this->column );
	}

	public function search() {
		return new Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

}