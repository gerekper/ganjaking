<?php

namespace ACP\Filtering\Settings;

use ACP\Filtering\Settings;

class Delegated extends Settings {

	public function define_options() {
		$options = parent::define_options();

		// Default is on
		$options['filter'] = 'on';

		return $options;
	}

	public function create_view() {
		$view = parent::create_view();

		// Remove Top Label
		$view->set( 'sections', null );

		return $view;
	}

}