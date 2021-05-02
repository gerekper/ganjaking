<?php

namespace ACP\Search\Middleware;

class Rules {

	/**
	 * @param string $rules_raw
	 *
	 * @return array
	 */
	public function __invoke( $rules_raw ) {
		$input = json_decode( $rules_raw );

		if ( ! $input || ! $input->rules ) {
			return [];
		}

		$operator = new Mapping\Operator( Mapping::REQUEST );
		$value_type = new Mapping\ValueType( Mapping::REQUEST );

		$rules = [];

		foreach ( $input->rules as $rule ) {
			$rules[] = [
				'name'        => $rule->id,
				'operator'    => $operator->{$rule->operator},
				'value'       => $rule->value,
				'value_type'  => $value_type->{$rule->type},
				'value_label' => isset( $rule->formatted_value ) ? $rule->formatted_value : null,
			];
		}

		return $rules;
	}

}