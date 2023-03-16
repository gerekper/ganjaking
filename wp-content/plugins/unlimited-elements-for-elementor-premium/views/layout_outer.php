<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class BloxViewLayoutOuter{
	
	protected $objPageBuilder;
	protected $objLayout, $objLayouts, $layoutID, $layoutType, $isTemplate;
	
	
	/**
	 * the constructor
	 */
	public function __construct(){
		
		$this->objLayouts = new UniteCreatorLayouts();
		
		$layoutID = UniteFunctionsUC::getGetVar("id", null, UniteFunctionsUC::SANITIZE_ID);
		
		$this->isTemplate = false;
		$this->objLayout = new UniteCreatorLayout();
				
		if(!empty($layoutID)){
			$this->layoutID = $layoutID;
			$this->objLayout->initByID($layoutID);
			$this->layoutType = $this->objLayout->getLayoutType();
						
			
		}else{			//init layout type for new layout
			
			//set layout type
			$layoutType = UniteFunctionsUC::getGetVar("layout_type", null, UniteFunctionsUC::SANITIZE_KEY);
			if(!empty($layoutType)){
				
				$this->objLayouts->validateLayoutType($layoutType);
				$this->layoutType = $layoutType;
				$this->objLayout->setLayoutType($layoutType);
				
			}
			
		}

		if(!empty($this->layoutType))
			$this->isTemplate = true;
		
		
		$this->objPageBuilder = new UniteCreatorPageBuilder();
		$this->objPageBuilder->initOuter($this->objLayout);
		
	}
	
	
	/**
	 * display
	 */
	protected function display(){
							
		$this->objPageBuilder->displayOuter();
						
	}
	
}


$pathProviderLayoutOuter = GlobalsUC::$pathProvider."views/layout_outer.php";

require_once $pathProviderLayoutOuter;

new BloxViewLayoutOuterProvider();
