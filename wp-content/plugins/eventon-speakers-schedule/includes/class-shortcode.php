<?php
/**
 * Shortcodes
 * 
 */

class EVOSS_Shortcode{
	function __construct(){
		add_shortcode('add_eventon_speakers', array($this,'speakers_list'));
	}

	function speakers_list($atts){

	}
}