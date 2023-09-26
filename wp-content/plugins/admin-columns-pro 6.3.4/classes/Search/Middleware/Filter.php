<?php

namespace ACP\Search\Middleware;

use AC;
use ACP\Search;
use ACP\Search\Comparison;

class Filter extends Search\Filter {

	private function get_labels() {
		$mapping = new Mapping\Operator( Mapping::RESPONSE );
		$labels = [];

		foreach ( $this->comparison->get_labels() as $operator => $label ) {
			$labels[ $mapping->$operator ] = $label;
		}

		return $labels;
	}

	private function get_value_type() {
		$mapping = new Mapping\ValueType( Mapping::RESPONSE );
		$value_type = $this->comparison->get_value_type();

		return $mapping->$value_type;
	}

	public function __invoke() {
		$comparison = $this->comparison;
		$labels = $this->get_labels();

		$filter = [
			'id'              => $this->name,
			'type'            => $this->get_value_type(),
			'operators'       => array_keys( $labels ),
			'operator_labels' => $labels,
			'label'           => $this->label,
			'values'          => false,
			'use_ajax'        => false,
			'use_pagination'  => false,
		];

		switch ( true ) {
			case $comparison instanceof Comparison\Values :

				$values = [];

				/** @var AC\Helper\Select\Option $value */
				foreach ( $comparison->get_values() as $value ) {
					$values[ $value->get_value() ] = $value->get_label();
				}

				$filter['values'] = (object) $values;

				break;
			case $comparison instanceof Comparison\SearchableValues :
				$filter['use_ajax'] = true;
				$filter['use_pagination'] = true;

				break;
			case $comparison instanceof Comparison\RemoteValues :
				$filter['use_ajax'] = true;
				$filter['use_pagination'] = false;

				break;
		}

		return $filter;
	}

}