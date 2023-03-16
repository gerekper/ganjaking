<?php

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

require HelperUC::getPathViewObject("addons_view.class");


class UniteCreatorAddonsBackgroundsView extends UniteCreatorAddonsView{
	
	protected $showButtons = true;
	protected $showHeader = true;
	protected $pluginTitle = null;
	
	
	/**
	 * get header text
	 * @return unknown
	 */
	protected function getHeaderText(){
		
		$headerTitle = esc_html__("Manage Background Widgets", "unlimited-elements-for-elementor");
		
		return($headerTitle);
	}
	
	
	/**
	 * addons view provider
	 */
	public function __construct(){
		
		$this->addonType = GlobalsUC::ADDON_TYPE_BGADDON;
		$this->product = GlobalsUnlimitedElements::PLUGIN_NAME;
		$this->pluginTitle = GlobalsUnlimitedElements::PLUGIN_TITLE;
		
		
		parent::__construct();
	}
	
	
}


new UniteCreatorAddonsBackgroundsView();
