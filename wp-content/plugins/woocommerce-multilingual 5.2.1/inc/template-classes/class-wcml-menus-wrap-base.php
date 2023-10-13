<?php

abstract class WCML_Menu_Wrap_Base extends WCML_Templates_Factory {

	/**
	 * @var \woocommerce_wpml $woocommerce_wpml
	 */
	protected $woocommerce_wpml;

	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	/**
	 * @return array
	 */
	public function get_model() {
		return array_merge(
			[
				'can_operate_options' => current_user_can( 'wpml_operate_woocommerce_multilingual' ),
				'rate'                => [
					'on'        => $this->woocommerce_wpml->get_setting( 'rate-block', true ),
					'message'   => sprintf(
						/* translators: %1$s and %3$s are wrappers for html link, and %2$s will be replaced with 5 stars in ASCII. */
						__( 'You can express your love and support by %1$srating our plugin %2$s%3$s and saying that it works for you.', 'woocommerce-multilingual' ),
						'<a href="https://wordpress.org/support/plugin/woocommerce-multilingual/reviews/?filter=5#new-post" class="wpml-external-link" target="_blank">',
						'&#9733;&#9733;&#9733;&#9733;&#9733;', // 5 stars
						'</a>'
					),
					'hide_text' => __( 'Hide', 'woocommerce-multilingual' ),
					'nonce'     => wp_nonce_field( 'wcml_settings', 'wcml_settings_nonce', true, false ),
				],
			],
			$this->get_child_model()
		);
	}

	/**
	 * @return array
	 */
	abstract protected function get_child_model();

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return 'menus-wrap.twig';
	}

}
