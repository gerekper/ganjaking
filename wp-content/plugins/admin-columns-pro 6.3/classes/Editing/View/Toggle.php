<?php

namespace ACP\Editing\View;

use AC\Type\ToggleOptions;
use ACP\Editing\View;

class Toggle extends View {

	use OptionsTrait;

	public function __construct( ToggleOptions $options ) {
		parent::__construct( 'togglable' );

		$this->set_options( [
			$options->get_disabled()->get_value() => $options->get_disabled()->get_label(),
			$options->get_enabled()->get_value()  => $options->get_enabled()->get_label(),
		] );
	}

}