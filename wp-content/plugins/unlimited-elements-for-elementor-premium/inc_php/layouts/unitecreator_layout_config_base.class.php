<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorLayoutOutputConfigBase{
	
	public $allowAnimations = true;
	
	/**
	 * constructor
	 */
	public function __construct(){
		$this->init();
	}
	
	/**
	 * function for override
	 */
	protected function initChild(){
		dmp("init child - function for override");
	}
	
	/**
	 * init
	 */
	private function init(){
		
		$this->initChild();
	}
	
}