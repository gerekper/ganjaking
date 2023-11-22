<?php
namespace ElementPack\Modules\Countdown;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();

		add_action('wp_ajax_element_pack_countdown_end', [$this, 'countdown_end']);
		add_action('wp_ajax_nopriv_element_pack_countdown_end', [$this, 'countdown_end']);
		
	}

	public function get_name() {
		return 'countdown';
	}

	public function get_widgets() {

		$widgets = [
			'Countdown',
		];

		return $widgets;
	}


	public function countdown_end(){ 
		$wp_current_time = current_time( 'timestamp' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$end_time = (int) $_POST['endTime'];

		if( $wp_current_time > $end_time ){
			echo "ended";
		}
		wp_die();

	}
 
}
 