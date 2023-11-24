<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorDynamicVisibility{
	
	const PREFIX = "uc_dynamic_visibility_";
	private $arrSettings;
	private $arrHiddenIDs = array();
	private $showDebug = false;
	private $arrDebug;
	
	
	/**
	 * get setting by key
	 */
	private function getSetting($key){
				
		$value =  UniteFunctionsUC::getVal($this->arrSettings, self::PREFIX.$key);
		
		return($value);
	}
	
	/**
	 * add debug string
	 */
	private function addDebug($strDebug, $strDebug2 = null){
		
		$this->arrDebug[] = $strDebug;

		if(!empty($strDebug))
			$this->arrDebug[] = $strDebug2;
	}
	
	
	private function addDebugOptions($arrOptions){
		
		$this->arrDebug[] = array(
			"debug_type"=>"options",
			"options"=>$arrOptions
		);
		
		return(false);
	}
	
	/**
	 * print debug
	 */
	private function printDebug($element){
		
		//return(false);
		
		dmp($this->arrDebug);
		
	}
	
	
	/**
	 * is hide by archive term ids
	 */
	private function isShowElement_archiveTermIDs($condition){
				
		$termIDs = UniteFunctionsUC::getVal($condition, "term_ids");
		
		$isIncludeChildren = UniteFunctionsUC::getVal($condition,  "include_children");
		$isIncludeChildren = UniteFunctionsUC::strToBool($isIncludeChildren);

		$showType = UniteFunctionsUC::getVal($condition, "terms_show");
			
		$isShow = ($showType !== "hide");
		
		$showValue = true;
		$hideValue = false;
				
		if($isShow == false){
			$showValue = false;
			$hideValue = true;
		}
		
		if($this->showDebug == true){
			
			$this->addDebug("Show by archive term IDs.");
			
			$this->addDebugOptions(array(
				"include children"=>$isIncludeChildren,
				"show"=>$isShow,
				"terms ids"=>$termIDs
			));
		
		}
		
		if(empty($termIDs)){
			
			if($this->showDebug == true)
				$this->addDebug("empty term ids, hide element");
			
			return(false);		//if not given term ids - always show
		}
		
		//not archive - hide
		if(is_archive() == false){
			
			if($this->showDebug == true)
				$this->addDebug("the page is not archive, hide element");
			
			return(false);
		}
		
		$objTerm = get_queried_object();
		
		//not term current obj - hide
		if($objTerm instanceof WP_Term == false){
			
			if($this->showDebug == true)
				$this->addDebug("Not a term related page, hide element");
			
			return(false);
		}
		
		//not term - hide
		if(isset($objTerm->term_id) == false){
			
			if($this->showDebug == true)
				$this->addDebug("no term id found, wrong term loaded, hide element");
			
			return(false);
		}
		
		//check current term
		if($isIncludeChildren == true)
			$arrCurrentTermIDs = UniteFunctionsWPUC::getTermParentIDs($objTerm);
		else
			$arrCurrentTermIDs = array($objTerm->term_id);
			
		if($this->showDebug == true)
			$this->addDebug("current term ids:", $arrCurrentTermIDs);
		
		//if current or parent of the current found between the given, don't hide
		foreach($arrCurrentTermIDs as $currentID){
			
			$isFound = (array_search($currentID, $termIDs) !== false);
			
			if($isFound == true){
				
			if($this->showDebug == true)
				$this->addDebug("term found", $showValue?"show element":"hide element");
				
				return($showValue);
			}
			
		}
		
		if($this->showDebug == true)
			$this->addDebug("term not found", $hideValue?"hide element":"show element");
		
		return($hideValue);	//hide in other cases
	}
	
	/**
	 * return if show element by condition
	 */
	private function isShowElementByCondition($condition){
		
		if(empty($condition))
			return(true);
		
		$type = UniteFunctionsUC::getVal($condition, "condition_type");
				
		switch($type){
			case "archive_terms":
				
				$isShow = $this->isShowElement_archiveTermIDs($condition);
							
				return($isShow);
			break;
			default:
				return(true);
			break;
		}
		
		return(true);
	}
	
	
	/**
	 * check if hide element or not
	 */
	private function isShowElement($type){
		
		$arrConditions = $this->getSetting("conditions");
		
		if(empty($arrConditions) || is_array($arrConditions) == false)
			return(true);
		
		$isShowTotal = true;
		
		foreach($arrConditions as $conditionOptions){
			
			$isShow = $this->isShowElementByCondition($conditionOptions);
					
			if($isShow == false)
				$isShowTotal = false;
		}
				
		return($isShowTotal);
	}
	
	
	/**
	 * get all relevant settings
	 */
	private function getRelevantSettings(){
		
		$arrSettingKeys = array(
			self::PREFIX."show_debug",
			self::PREFIX."conditions"
		);
		
		$output = array();
		foreach($this->arrSettings as $key=>$setting){
			
			if(array_search($key, $arrSettingKeys) === false)
				continue;
			
			$output[$key] = $setting;
		}
		
		return($output);
	}
	
	
	/**
	 * print debug settings
	 */
	private function printSettingsDebug(){
		
		dmp("UE Visibility Conditions Settings");
		$arrSettings = $this->getRelevantSettings();
		
		dmp($arrSettings);
		
	}
	
	
	
	/**
	 * on before render
	 */
	public function onBeforeRenderElement($element){
		
		$this->arrSettings = $element->get_settings_for_display();		
		
		$conditions = $this->getSetting("conditions");
		if(empty($conditions))
			return(true);
		
			
		$this->arrDebug = array();
		$this->showDebug = false;
		
		$showDebug = $this->getSetting("show_debug");
		$this->showDebug = ($showDebug == "yes");
		
		if($this->showDebug){
			$this->printSettingsDebug();
		}
		
		$isShow = $this->isShowElement($conditions);
		
		if($this->showDebug == true && !empty($this->arrDebug))
			$this->printDebug($element);
		
		if($isShow == true)
			return(false);
		
		$elementID = $element->get_id();

		$this->arrHiddenIDs[$elementID] = true;
		
		//start hiding
		
		ob_start();
	}
	
	
	/**
	 * on after render element
	 */
	public function onAfterRenderElement($element){
		
		$elementID = $element->get_id();
		
		$isHidden = isset($this->arrHiddenIDs[$elementID]);
		
		if($isHidden == false)
			return(true);

		//finish hiding
		
		ob_end_clean();
	}
	
	private function ___________ADD_CONTROLS_____________(){}
	
	
	/**
	 * add visibility controls section
	 */
	public function addVisibilityControls($objControls){
		
		$objControls->start_controls_section(
			'section_visibility_uc',
			[
				'label' => __( 'UE Visibility Conditions', 'unlimited_elements' ),
				'tab' => "advanced",
			]
		);
		
						
		//condition type
		
		$prefix = self::PREFIX;
		
		$objControls->add_control(
			'condition_type',
				array(
					'label' => __( 'Enable Visibility Conditions', 'unlimited-elements-for-elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
					'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
					'return_value'=>"yes",
					'separator'=>"after"
				)
	      );
		
	    $objControls->add_control("hideby",[
				'label' => __( 'Hide By', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'hide_by_default'  => __( 'Hide By Default', 'unlimited-elements-for-elementor' ),
					'archive_terms'  => __( 'Archive Terms', 'unlimited-elements-for-elementor' ),
				],
				'default' => [ ],					    	
		]);
	   	
		//------ hide by default
		
		$conditionHideDefalut = array("hideby"=>"hide_by_default");
		
		$objControls->add_control(
			'hide_by_default_text',
			[
				'label' => __( 'This section will be hidden by default if no other condition is chosen', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'label_block'=>true,
				'condition' => $conditionHideDefalut
			]
		);		
		
		
		//------ hide by terms text
		
		$conditionTerms = array("hideby"=>"archive_terms");
		
	    $objControls->add_control(
			'by_terms_heading',
			array(
				'label' => __( 'This hide by archive terms works only when current page is ARCHIVE type and current the term match the seleced', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => $conditionTerms,
				'separator' => 'before'
			)
		);
		
		//------ terms to hide
		
	    $arrControl = HelperProviderCoreUC_EL::getElementorControl_TermsPickerControl(__("Hide When Has Those Terms", "unlimited-elements-for-elementor"));
	    $arrControl["condition"] = $conditionTerms;
	    
		$objControls->add_control(
			'term_ids_hide',
			$arrControl
		);
		
		//------ terms to hide - children
		
		$objControls->add_control(
			'terms_hide_include_children',
			array(
				'label' => __( 'Or Their Children', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
				'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'=>$conditionTerms
			)
		);
		
		
		//------ terms to show
		
	    $arrControl = HelperProviderCoreUC_EL::getElementorControl_TermsPickerControl(__("Show When Has Those Terms", "unlimited-elements-for-elementor"));
	    $arrControl["condition"] = $conditionTerms;
	    
		$objControls->add_control(
			'term_ids_show',
			$arrControl
		);
		
		//------ terms to show - children
		
		$objControls->add_control(
			'terms_show_include_children',
			array(
				'label' => __( 'Or Their Children', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
				'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'=>$conditionTerms
			)
		);
		
		
	    /*
	    //--term heading
	   	
	    $objControls->add_control(
			'by_terms_heading',
			array(
				'label' => __( 'When current page is archive and current term match the seleced', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => $conditionTerms
			)
		);
	    
		
		
	    //--term ids
		
	    $arrControl = HelperProviderCoreUC_EL::getElementorControl_TermsPickerControl(__("Select Term", "unlimited-elements-for-elementor"));
	    $arrControl["condition"] = $conditionTerms;
	    
		$objControls->add_control(
			'term_ids',
			$arrControl
		);

		$objControls->add_control(
			'terms_include_children',
			array(
				'label' => __( 'Or Their Children', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
				'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition'=>$conditionTerms
			)
		);
		*/
		
		//------------- DEBUG --------------
		
		$objControls->add_control(
			$prefix.'show_debug',
			array(
				'label' => __( 'Show Debug Text', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'unlimited-elements-for-elementor' ),
				'label_off' => __( 'No', 'unlimited-elements-for-elementor' ),
				'return_value' => 'yes',
				'default' => '',
				'separator'=>"before"
			)
		);
		
		
        $objControls->end_controls_section();
	}
	
	
	
}