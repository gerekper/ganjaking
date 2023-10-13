<?php

class WCML_Setup_Introduction_UI extends WCML_Setup_Step {

	const SLUG = 'introduction';

	public function get_model() {
		return [
			'strings'      => [
				'step_id'     => 'introduction_step',
				'heading'     => __( "Let's make your WooCommerce store multilingual", 'woocommerce-multilingual' ),
				'description' => [

					'title' => __( 'To get started, we need to set up the following:', 'woocommerce-multilingual' ),
					'step1' => __( "Create store pages in all your site's languages", 'woocommerce-multilingual' ),
					'step2' => __( 'Choose which product attributes you want to translate', 'woocommerce-multilingual' ),
					'step3' => __( 'Set your translation options', 'woocommerce-multilingual' ),
					'step4' => __( 'Decide if you want to add multiple currencies to your store', 'woocommerce-multilingual' ),

				],
				'continue'    => __( "Let's continue", 'woocommerce-multilingual' ),
				'later'       => __( "I'll do the setup later", 'woocommerce-multilingual' ),
			],
			'later_url'    => admin_url( 'admin.php?page=wpml-wcml&src=setup_later' ),
			'continue_url' => $this->next_step_url,
		];
	}

	public function get_template() {
		return '/setup/introduction.twig';
	}
}
