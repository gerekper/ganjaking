<?php

namespace WCML\Compatibility\WcDynamicPricing;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wc_dynamic_pricing_load_modules', [ $this, 'filter_price' ] );
		add_filter( 'woocommerce_dynamic_pricing_get_rule_amount', [ $this, 'woocommerce_dynamic_pricing_get_rule_amount' ], 10, 2 );
		add_filter( 'dynamic_pricing_product_rules', [ $this, 'dynamic_pricing_product_rules' ] );
	}

	/**
	 * @param array $modules
	 *
	 * @return array
	 */
	public function filter_price( $modules ) {

		foreach ( $modules as $mod_key => $module ) {
			if ( isset( $module->available_rulesets ) ) {
				$available_rulesets = $module->available_rulesets;

				foreach ( $available_rulesets as $rule_key => $available_ruleset ) {

					if ( isset( $available_ruleset['rules'] ) && is_array( $available_ruleset['rules'] ) ) {
						$rules = $available_ruleset['rules'];
						foreach ( $rules as $r_key => $rule ) {
							if ( 'fixed_product' === $rule['type'] ) {
								$rules[ $r_key ]['amount'] = apply_filters( 'wcml_raw_price_amount', $rule['amount'] );
							}
						}
						$modules[ $mod_key ]->available_rulesets[ $rule_key ]['rules'] = $rules;

					} elseif ( isset( $available_ruleset['type'] ) && 'fixed_product' === $available_ruleset['type'] ) {
						$modules[ $mod_key ]->available_rulesets[ $rule_key ]['amount'] = apply_filters( 'wcml_raw_price_amount', $available_ruleset['amount'] );
					}
				}
			}
		}

		return $modules;
	}

	/**
	 * @param float  $amount
	 * @param object $rule
	 *
	 * @return float
	 */
	public function woocommerce_dynamic_pricing_get_rule_amount( $amount, $rule ) {

		if ( 'price_discount' === $rule['type'] || 'fixed_price' === $rule['type'] ) {
			$amount = apply_filters( 'wcml_raw_price_amount', $amount );
		}

		return $amount;
	}

	/**
	 * @param array $rules
	 *
	 * @return array
	 */
	public function dynamic_pricing_product_rules( $rules ) {
		if ( is_array( $rules ) ) {
			foreach ( $rules as $r_key => $rule ) {
				foreach ( $rule['rules'] as $key => $product_rule ) {
					if ( 'price_discount' === $product_rule['type'] || 'fixed_price' === $product_rule['type'] ) {
						$rules[ $r_key ]['rules'][ $key ]['amount'] = apply_filters( 'wcml_raw_price_amount', $product_rule['amount'] );
					}
				}
			}
		}
		return $rules;
	}

}
