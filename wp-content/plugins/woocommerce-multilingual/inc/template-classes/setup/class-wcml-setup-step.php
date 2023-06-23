<?php

abstract class WCML_Setup_Step extends WCML_Templates_Factory {

	/** @var string */
	protected $next_step_url;

	/** @var string|null */
	protected $previous_step_url;

	public function __construct( $next_step_url, $previous_step_url = null ) {
		parent::__construct();

		$this->next_step_url     = $next_step_url;
		$this->previous_step_url = $previous_step_url;
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}
}
