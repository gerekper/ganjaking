<?php

namespace ACA\ACF\Settings\Column;

use AC;

class TermLink extends AC\Settings\Column\TermLink {

	protected function get_link_options() {
		$options = parent::get_link_options();

		unset( $options['filter'] );

		return $options;
	}

}