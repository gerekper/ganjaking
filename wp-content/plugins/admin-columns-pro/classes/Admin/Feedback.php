<?php

namespace ACP\Admin;

use AC\Asset\Assets;
use AC\Asset\Enqueueables;
use AC\Asset\Location;
use AC\Asset\Script;
use AC\Asset\Style;
use AC\Renderable;
use AC\View;

class Feedback implements Renderable, Enqueueables {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	public function __construct( Location\Absolute $location ) {
		$this->location = $location;
	}

	public function get_assets() {
		return new Assets( [
			new Style( 'acp-feedback', $this->location->with_suffix( 'assets/core/css/feedback.css' ) ),
			new Script( 'acp-feedback', $this->location->with_suffix( 'assets/core/js/feedback.js' ) ),
		] );
	}

	public function render() {
		$feedback = new View( [
			'nonce' => wp_create_nonce( 'ac-ajax' ),
			'email' => wp_get_current_user()->user_email,
		] );

		return $feedback->set_template( 'admin/modal-feedback' )->render();
	}

}