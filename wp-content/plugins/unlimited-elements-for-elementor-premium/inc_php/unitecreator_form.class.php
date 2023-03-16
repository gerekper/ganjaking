<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorForm extends HtmlOutputBaseUC{
	
	private static $isFormIncluded = false;	  //indicator that the form included once
	
	private $addon;
	
	
	private function a_INIT(){}
	
	
	/**
	 * validate that addon is inited
	 */
	private function validateAddonInited(){
		
		if(empty($this->addon))
			UniteFunctionsUC::throwError("The addon is not inited");
	}
	
	
	/**
	 * set addon
	 */
	public function setAddon(UniteCreatorAddon $addon){
		$this->addon = $addon;
	}
	
	/**
	 * get form params
	 */
	public function getDialogFormParams(){
		
		$params = array(
			UniteCreatorDialogParam::PARAM_TEXTFIELD,
			UniteCreatorDialogParam::PARAM_TEXTAREA,
			UniteCreatorDialogParam::PARAM_DROPDOWN,
			//self::PARAM_CHECKBOX
		);
		
		
		return($params);
	}
	
	
	private function a_FORM_SETTING(){}
	
	
	/**
	 * add form related settings
	 */
	public function addFormSettings($objSettings, $name, $value, $title, $extra){
		
		$arrValues = UniteFunctionsUC::getVal($value, $name);
		
		
		$adminEmail = helperuc::getGeneralSetting("form_admin_email");
		$adminEmail = trim($adminEmail);
		
		$urlGeneralSettings = HelperUC::getViewUrl(GlobalsUC::VIEW_SETTINGS);
		$linkGeneralSettings = HelperHtmlUC::getHtmlLink($urlGeneralSettings, esc_html__("General Settings","unlimited-elements-for-elementor"),"","",true);
		$urlGeneralSettings .= "#tab=fields_settings";
		
		
		if(empty($adminEmail))
			$objSettings->addStaticText(esc_html__("Please fill admin email in","unlimited-elements-for-elementor")." {$linkGeneralSettings}.");
		else
			$objSettings->addStaticText(esc_html__("Admin Email","unlimited-elements-for-elementor").": <b>$adminEmail</b> (".esc_html__("you can change it in","unlimited-elements-for-elementor")." {$linkGeneralSettings})");
		
		$objSettings->addHr();
		
		$sendTextValue = UniteFunctionsUC::getVal($arrValues, $name."_send_button_text", "Send");
		$objSettings->addTextBox($name."_send_button_text",$sendTextValue,esc_html__("Send Button Text", "unlimited-elements-for-elementor"));
		
		$sendingTextValue = UniteFunctionsUC::getVal($arrValues, $name."_sending_text", "Sending...");
		$objSettings->addTextBox($name."_sending_text",$sendingTextValue,esc_html__("Loading Text", "unlimited-elements-for-elementor"));
		
		$successTextValue = UniteFunctionsUC::getVal($arrValues, $name."_success_text", "Thank you for contacting us");
		$objSettings->addTextBox($name."_success_text",$successTextValue,esc_html__("Thank you text", "unlimited-elements-for-elementor"));
		
	}
	
		
	
	private function a_FORM_OUTPUT(){}
	
	
	/**
	 * add form includes
	 */
	private function addFormIncludes(){
		
		//include common scripts only once
		if(self::$isFormIncluded == false){
			$urlFormJS = GlobalsUC::$url_assets_internal."js/uc_form.js";
			
			$this->addon->addLibraryInclude("jquery");
			$this->addon->addJsInclude($urlFormJS);
			
			$urlAjax = GlobalsUC::$url_ajax_front;
			$this->addon->addToJs('window.g_urlFormAjaxUC = "'.$urlAjax.'";');
		}
		
		self::$isFormIncluded = true;
				
		//include addon scripts
		$script  = "\n\n jQuery(document).ready(function(){\n";
		$script .= "	var objUCForm = new UniteCreatorFormFront();\n";
		$script .= "	objUCForm.init();\n";
		$script .= "});\n";
		
		$this->addon->addToJs($script);
	}
	
	
	/**
	 * get form output data
	 */
	public function getFormOutputData($data, $paramName, $arrValues){
		
		$this->validateAddonInited();
		
		$this->addFormIncludes();
		
		$sendButtonText = UniteFunctionsUC::getVal($arrValues, "{$paramName}_send_button_text", esc_html__("Send", "unlimited-elements-for-elementor"));
		
		$htmlStart = "";
		$htmlStart .= "<form class='uc-form' name='uc_form_{$paramName}'>".self::BR;
		$htmlStart .= self::TAB."<div class='uc-form-content'>".self::BR;
		
		$data["{$paramName}_start"] = $htmlStart;
		
		$textSending =  UniteFunctionsUC::getVal($arrValues, "{$paramName}_sending_text");  
		$textSent =  UniteFunctionsUC::getVal($arrValues, "{$paramName}_success_text");  
		
		$htmlEnd = "";
		$htmlEnd .= self::TAB2."<input type=\"submit\" value=\"{$sendButtonText}\" class=\"uc-submit-button\">".self::BR2;
		$htmlEnd .= self::TAB2."<div class='uc-form-loading' style='display:none'>{$textSending}</div>".self::BR;
		$htmlEnd .= self::TAB2."<div class='uc-form-error' style='display:none'></div>".self::BR;
		
		$htmlEnd .= self::TAB."</div>".self::BR;		//end content
		$htmlEnd .= self::TAB."<div class='uc-form-success' style='display:none'>{$textSent}</div>".self::BR;
		$htmlEnd .= "</form>".self::BR;
		
		$data["{$paramName}_end"] = $htmlEnd;
		
		return($data);
	}
	
	/**
	 * process form items for output
	 */
	public function processFormItemsForOutput($arrItems){
		
		//in case that main values affects the items output
		$arrMainValues = $this->getFormMainValues();
		
		$arrItemsNew = array();
		foreach($arrItems as $key=>$item){
						
			$type = UniteFunctionsUC::getVal($item, "type");
			UniteFunctionsUC::validateNotEmpty($type, "processFormItemsForOutput: Item Type");
			
			unset($item["admin_label"]);
			
			$title = UniteFunctionsUC::getVal($item, "title");
			$name = UniteFunctionsUC::getVal($item, "name");
			$title = htmlspecialchars($title);
			$value = UniteFunctionsUC::getVal($item, "default_value");
			$value = htmlspecialchars($value);
			$isRequired = UniteFunctionsUC::getVal($item, "is_required");
			$isRequired = UniteFunctionsUC::strToBool($isRequired);
			
			
			$htmlParams = "name='{$name}' placeholder='{$title}' data-title='{$title}' class='uc-form-field'";
			
			if($isRequired == true)
				$htmlParams .= " data-required='true'";
			
			switch($type){
				case UniteCreatorDialogParam::PARAM_TEXTFIELD:
					 $htmlField = self::TAB2."<input type='text' {$htmlParams} value='$value'>";
				break;
				case UniteCreatorDialogParam::PARAM_TEXTAREA:
					
					$htmlField = self::TAB2."<textarea {$htmlParams}>{$value}</textarea>";
					
				break;
				case UniteCreatorDialogParam::PARAM_DROPDOWN:
					
					$options = UniteFunctionsUC::getVal($item, "options");
					$options = array_flip($options);
					
					$htmlField = HelperHtmlUC::getHTMLSelect($options, $value, $htmlParams, true);
					
				break;
				case UniteCreatorDialogParam::PARAM_CHECKBOX:
					
					$textNear = UniteFunctionsUC::getVal($item, "text_near");
					$isChecked = UniteFunctionsUC::getVal($item, "is_checked");
					$isChecked = UniteFunctionsUC::strToBool($isChecked);
					
					$htmlChecked = "";
					if($isChecked == true)
						$htmlChecked = "checked='checked'";
					
					$htmlField = self::TAB2."<div class='uc-form-checkbox-wrapper'>".self::BR;
					$htmlField .= self::TAB2."<input type='checkbox' {$htmlParams} {$htmlChecked}>".self::BR;
					$htmlField .= self::TAB2."<span class='uc-form-checkbox-text'>{$textNear}</span>".self::BR;
					$htmlField .= self::TAB2."</div>";
					
				break;
				default:
					$htmlField = "<pre>no output written for form type: $type</pre>";
				break;
			}
			
			$item["form_field"] = $htmlField;
			
			$arrItemsNew[] = array("item"=>$item);
		}
		
		
		return($arrItemsNew);		
	}
	
	/**
	 * get form main values
	 */
	protected function getFormMainValues(){
		
		$param = $this->addon->getParamByType(UniteCreatorDialogParam::PARAM_FORM);
		
		UniteFunctionsUC::validateNotEmpty($param, "Font Param");
		$paramName = $param["name"];
		
		$arrValues = $this->addon->getProcessedMainParamsValues(UniteCreatorParamsProcessor::PROCESS_TYPE_CONFIG);
		
		$formValues = $arrValues[$paramName];
		
		return($formValues);		
	}
	
	private function a_FORM_SEND(){}
	
	
	/**
	 * get message for send
	 */
	private function getFormMessage($arrFormFields){
		
		$message = "";
		
		$emailPrefix = HelperUC::getGeneralSetting("form_admin_email_prefix");
		if(empty($emailPrefix))
			$emailPrefix = "The client info is:";
		
        $emailPrefix = trim($emailPrefix);
        
        if (!empty($emailPrefix))
            $message .= "{$emailPrefix} <br>\n";
        
        foreach ($arrFormFields as $arrField) {
        	
        	$title = UniteFunctionsUC::getVal($arrField, "title");
        	$name = UniteFunctionsUC::getVal($arrField, "name");
        	$value = UniteFunctionsUC::getVal($arrField, "value");
        	
            if (empty($value))
                $value = "[empty text]";
            else
                $value = "<b> {$value} </b>";
            
            $message .= "{$title}: $value <br>\n";
        }
        
        $message = str_replace("::", ":", $message);
        
        return ($message);
	}
	
	
	/**
	 * validate required field
	 */
	private function validateFieldRequired($arrField){

		$required = UniteFunctionsUC::getVal($arrField, "required");
		$required = UniteFunctionsUC::strToBool($required);
			
		if($required == false)
			return(false);
		
		$value = UniteFunctionsUC::getVal($arrField, "value");
		$value = trim($value);
		
		if(!empty($value))
			return(false);
		
		//put error message
		$title = UniteFunctionsUC::getVal($arrField, "title");
		
		$message = esc_html__("Please fill","unlimited-elements-for-elementor"). " <b>".$title. "</b>". esc_html__(" field","unlimited-elements-for-elementor");
		UniteFunctionsUC::throwError($message);
		
		
	}
	
	
	/**
	 * validate form fields
	 */
	private function validateFormFields($formFields){
		
		if(empty($formFields))
			return(false);
		
		foreach($formFields as $field){
		
			//validate required
			$this->validateFieldRequired($field);
		}
		
	}
	
	
	/**
	 * send form
	 */
	public function sendFormFromData($data){
		
		$insideValidation = false;
		
		try{
		
			$arrFormFields = UniteFunctionsUC::getVal($data, "form_data");
			if(empty($arrFormFields))
				UniteFunctionsUC::throwError("No form fields given");
		
			$insideValidation = true;
			
			$this->validateFormFields($arrFormFields);
			
			$insideValidation = false;
			
			$adminEmail = HelperUC::getGeneralSetting("form_admin_email");
			
			$urlViewSettings = HelperUC::getViewUrl(GlobalsUC::VIEW_SETTINGS,"#tab=fields_settings");
			$htmlLink = HelperHtmlUC::getHtmlLink($urlViewSettings, "general settings","","",true);
			
			if(UniteFunctionsUC::isEmailValid($adminEmail) == false)
				UniteFunctionsUC::throwError("Admin email not valid, plase set in {$htmlLink}");
			
			$message = $this->getFormMessage($arrFormFields);
			$subject = "Email from your website";
			
			UniteProviderFunctionsUC::sendEmail($adminEmail, $subject, $message);
			
		}catch(Exception $e){
			
			if($insideValidation == false)
				$errorMessage = "Can't send form: ".$e->getMessage();
			else
				$errorMessage = $e->getMessage();
			
				
			UniteFunctionsUC::throwError($errorMessage);
		}
		
	}
	
	
	
}