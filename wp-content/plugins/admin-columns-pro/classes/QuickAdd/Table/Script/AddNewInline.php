<?php

namespace ACP\QuickAdd\Table\Script;

use AC\Asset\Location;
use AC\Asset\Script;

class AddNewInline extends Script {

	/**
	 * @var string
	 */
	private $label;

	public function __construct( $label, $handle, Location $location = null, array $dependencies = [] ) {
		parent::__construct( $handle, $location, $dependencies );

		$this->label = $label;
	}

	public function register() {
		parent::register();

		wp_localize_script( $this->get_handle(), 'ACP_ADD_NEW_INLINE', [
				'i18n' => [
					'add_new' => $this->label,
				],
			]
		);
	}

}