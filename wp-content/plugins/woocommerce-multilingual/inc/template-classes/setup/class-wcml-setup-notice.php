<?php

class WCML_Setup_Notice_UI extends WCML_Templates_Factory {

	/**
	 * @return array
	 */
	public function get_model() {

		$model = [
			'text'      => [
				'prepare' => __( 'Make your store multilingual', 'woocommerce-multilingual' ),
				'help'    => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML strong tags */
					esc_html__( 'Set up %1$sWooCommerce Multilingual & Multicurrency%2$s to translate your store and add more currencies.', 'woocommerce-multilingual' ),
					'<strong>',
					'</strong>'
				),
				'start'   => __( 'Start the Setup Wizard', 'woocommerce-multilingual' ),
			],
			'setup_url' => esc_url( admin_url( 'admin.php?page=wcml-setup' ) ),
		];

		return $model;

	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	/**
	 * @return string
	 */
	public function get_template() {
		return '/setup/notice.twig';
	}


}
