<?php

defined('UNLIMITED_ELEMENTS_INC') or die;

class UniteCreatorLayoutPreviewProvider extends UniteCreatorLayoutPreview{


	/**
	 * constructor
	 */
	public function __construct(){

		$this->showHeader = true;
		
		parent::__construct();
				
		$this->display();
	}
	
}