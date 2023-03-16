<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

require	HelperUC::getPathViewObject("settings_view.class");

class UniteCreatorViewLayoutsSettings extends UniteCreatorSettingsView{
	
	
	/**
	 * constructor
	 */
	public function __construct(){

		$this->headerTitle = HelperUC::getText("layouts_global_settings");
		$this->saveAction = "update_global_layout_settings";
		$this->textButton = HelperUC::getText("save_layout_settings");
		
		//set settings object
		$this->objSettings = UniteCreatorLayout::getGlobalSettingsObject();
		
		$this->display();
	}
	
}


new UniteCreatorViewLayoutsSettings();
