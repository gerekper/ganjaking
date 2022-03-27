<?php

namespace ACP\Service;

use AC\Registrable;

class Templates implements Registrable {

	/**
	 * @var string
	 */
	private $dir;

	public function __construct( $dir ) {
		$this->dir = (string) $dir;
	}

	public function register() {
		add_filter( 'ac/view/templates', [ $this, 'templates' ] );
	}

	/**
	 * @param array $templates
	 *
	 * @return array
	 */
	public function templates( $templates ) {
		$templates[] = $this->dir . 'templates';

		return $templates;
	}

}