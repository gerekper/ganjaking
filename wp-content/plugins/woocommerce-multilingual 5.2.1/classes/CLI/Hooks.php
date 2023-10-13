<?php

namespace WCML\CLI;

class Hooks implements \IWPML_CLI_Action {

	public function add_hooks() {
		add_action( 'shutdown', [ $this, 'preventWcWizardRedirection' ] );
	}

	public function preventWcWizardRedirection() {
		delete_transient( '_wc_activation_redirect' );
	}
}
