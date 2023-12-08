<?php

namespace ACP\Search\Middleware;

class Rules {

	/**
	 * @param string $rules_raw
	 *
	 * @return array
	 */
	public function __invoke( string $rules_raw ) {
		$input = json_decode( $rules_raw );

		if ( ! $input || ! $input->rules ) {
			return [];
		}

		$operator = new Mapping\Operator( Mapping::REQUEST );

		$rules = [];

		foreach ( $input->rules as $rule ) {
			$rules[] = [
				'name'     => $rule->id,
				'operator' => $operator->{$rule->operator},
				'value'    => $rule->value,
			];
		}

		return $rules;
	}

}