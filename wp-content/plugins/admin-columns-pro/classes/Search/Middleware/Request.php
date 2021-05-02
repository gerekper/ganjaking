<?php

namespace ACP\Search\Middleware;

use AC;

class Request implements AC\Middleware {

	/**
	 * @param AC\Request $request
	 */
	public function handle( AC\Request $request ) {
		$rules_key = 'rules';

		if ( $request->get_method() === AC\Request::METHOD_GET ) {
			$rules_key = 'ac-' . $rules_key;
		}

		$input_raw = $request->get( $rules_key );

		$rule_mapper = new Rules();
		$rules = $rule_mapper( $input_raw );

		if ( ! $rules ) {
			return;
		}

		$request->get_parameters()->merge( [
			$rules_key          => $rules,
			$rules_key . '-raw' => $input_raw,
		] );
	}

}