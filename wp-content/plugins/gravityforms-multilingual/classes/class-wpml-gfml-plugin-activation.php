<?php

/**
 * Class WPML_GFML_Plugin_Activation
 */
class WPML_GFML_Plugin_Activation {

	public function register_callback() {
		register_activation_hook(
			GRAVITYFORMS_MULTILINGUAL_PATH . '/plugin.php',
			[ $this, 'callback' ]
		);
	}

	public function callback() {
		update_option( 'wpml-package-translation-refresh-required', true );
	}
}
