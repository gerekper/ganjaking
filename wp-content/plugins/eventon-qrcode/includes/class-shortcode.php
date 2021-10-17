<?php
/**
 * Checking Events shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-QR/Functions/shortcode
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_qr_shortcode{

	static $add_script;

	function __construct(){		
		add_shortcode('evo_checking_page',array($this, 'evo_checkin_page'));
		add_shortcode('add_eventon_checkin',array($this, 'evo_checkin_page'));
	}

	function evo_checkin_page($atts){
		global $eventon_qr;

		wp_enqueue_style( 'evo_checkin');

		ob_start();
		echo '<div class="evochecking_inpage">';
		$eventon_qr->checkin->checkin_page_content($atts);
		echo "</div>";
		return ob_get_clean();
	}
	
}
?>