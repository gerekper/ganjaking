<?php

defined('UNLIMITED_ELEMENTS_INC') or die;

class AddonLibraryViewLayoutProvider extends AddonLibraryViewLayout{
	
	
	/**
	 * add toolbar
	 */
	function __construct(){
		parent::__construct();
		
		$this->shortcodeWrappers = "wp";
		$this->shortcode = "blox_layout";
				
		$this->display();
	}
	
	
}