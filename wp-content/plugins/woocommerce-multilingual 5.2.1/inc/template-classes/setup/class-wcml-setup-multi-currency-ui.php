<?php

class WCML_Setup_Multi_Currency_UI extends WCML_Setup_Step {

	const SLUG = 'multi-currency';

	public function get_model() {
		return [
			'strings'           => [
				'step_id'     => 'currency_step',
				'heading'     => __( 'Do you want to add more currencies to your store?', 'woocommerce-multilingual' ),
				'description' => __( 'You will be able to:', 'woocommerce-multilingual' ),
				'bullet1'     => __( 'Add a currency switcher to your store', 'woocommerce-multilingual' ),
				'bullet2'     => __( 'Set exchange rates', 'woocommerce-multilingual' ),
				'bullet3'     => __( 'Create custom pricing in each currency', 'woocommerce-multilingual' ),
				'bullet4'     => __( 'And more!', 'woocommerce-multilingual' ),
				'enable'      => __( 'Yes, enable multicurrency mode', 'woocommerce-multilingual' ),
				'continue'    => __( 'No, use only one currency', 'woocommerce-multilingual' ),
				'go_back'     => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'documentation_url' => WCML_Tracking_Link::getWcmlMultiCurrencyDoc( false, [ 'utm_term' => WCML_Tracking_Link::UTM_TERM_WIZARD ] ),
			'multi_currency_on' => wcml_is_multi_currency_on(),
			'continue_url'      => $this->next_step_url,
			'go_back_url'       => $this->previous_step_url,
		];
	}

	public function get_template() {
		return '/setup/multi-currency.twig';
	}
}
