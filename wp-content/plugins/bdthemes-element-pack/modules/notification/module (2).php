<?php

namespace ElementPack\Modules\Notification;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();

		add_action( "wp_ajax_ep_connect_confetti", array( $this, "ep_connect_confetti" ) );
		add_action( "wp_ajax_nopriv_ep_connect_confetti", array( $this, "ep_connect_confetti" ) );
	}

	public function get_name() {
		return 'notification';
	}

	public function get_widgets() {
		$widgets = [ 
			'Notification',
		];

		return $widgets;
	}

	public function ep_connect_confetti() {
		return true;
	}
}
