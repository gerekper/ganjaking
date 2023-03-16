<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class AddonLibraryViewLayout{
	
	protected $showButtons = true;
	protected $isEditMode = false;
	protected $isLiveView = false;
	protected $showHeader = true;
		
	protected $objPageBuilder;
	
	
	/**
	 * the constructor
	 */
	public function __construct(){
		
		$layoutID = UniteFunctionsUC::getGetVar("id", null, UniteFunctionsUC::SANITIZE_ID);
		
		$objLayout = new UniteCreatorLayout();
		
		if($layoutID)
			$objLayout->initByID($layoutID);
		
		$this->objPageBuilder = new UniteCreatorPageBuilder();
		$this->objPageBuilder->initInner($objLayout);
		
	}
	
	
	/**
	 * get header title
	 */
	protected function getHeaderTitle(){
		
		if(empty($this->objLayout)){
			
			$title = HelperUC::getText("new_layout");
			
		}else{
			$title = HelperUC::getText("edit_layout")." - ";
			$title .= $this->objLayout->getTitle();
		}
		
		return($title);
	}
	
	
	
		
	
	/**
	 * display
	 */
	protected function display(){
				
		$this->objPageBuilder->displayInner();		
	}
	
	
}

$pathProviderLayout = GlobalsUC::$pathProvider."views/layout.php";
require_once $pathProviderLayout;
new AddonLibraryViewLayoutProvider();
