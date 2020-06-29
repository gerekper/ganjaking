<?php

namespace ACP\Search\Middleware;

use AC;
use AC\Middleware;

class Request
	implements Middleware {

	/**
	 * @param AC\Request $request
	 */
	public function handle( AC\Request $request ) {
		$rules_key = 'rules';

		if ( $request->get_method() === AC\Request::METHOD_GET ) {
			$rules_key = 'ac-' . $rules_key;
		}

		$input_raw = $request->get( $rules_key );
		$input = json_decode( $input_raw );

		if ( ! $input || ! $input->rules ) {
			return;
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

		$request->get_parameters()->merge( [
			$rules_key          => $rules,
			$rules_key . '-raw' => $input_raw,
		] );
	}

}