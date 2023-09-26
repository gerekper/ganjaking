<?php

namespace ACA\WC\Asset\Script;

use AC;

class Admin extends AC\Asset\Script {

	/**
	 * @var string
	 */
	private $assets_url;

	public function __construct( string $handle, AC\Asset\Location\Absolute $location ) {
		parent::__construct( $handle, $location->with_suffix( 'assets/js/admin.js' ) );

		$this->assets_url = $location->with_suffix( 'assets/' )->get_url();
	}

	public function register(): void {
		parent::register();

		$this->add_inline_variable( 'aca_wc_admin', [
			'assets' => $this->assets_url . '/',
		] );
	}

}