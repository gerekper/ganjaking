<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorDialogParamWork{
	
	const TYPE_MAIN = "main";
	const TYPE_ITEM_VARIABLE = "variable_item";
	const TYPE_MAIN_VARIABLE = "variable_main";
	const TYPE_FORM_ITEM = "form_item";
	
	const PARAM_EDITOR = "uc_editor";
	const PARAM_TEXTFIELD = "uc_textfield";
	const PARAM_TEXTAREA = "uc_textarea";
	const PARAM_NUMBER = "uc_number";
	const PARAM_RADIOBOOLEAN = "uc_radioboolean";
	const PARAM_DROPDOWN = "uc_dropdown";
	const PARAM_MULTIPLE_SELECT = "uc_multiple_select";
	const PARAM_HR = "uc_hr";	
	const PARAM_HEADING = "uc_heading";	
	const PARAM_CONTENT = "uc_content";	
	const PARAM_POST = "uc_post";
	const PARAM_DATASET = "uc_dataset";
	const PARAM_POSTS_LIST = "uc_posts_list";
	const PARAM_POST_TERMS = "uc_post_terms";
	const PARAM_WOO_CATS = "uc_woo_categories";
	const PARAM_LISTING = "uc_listing";
	
	const PARAM_USERS = "uc_users";
	const PARAM_TEMPLATE = "uc_template";
	const PARAM_INSTAGRAM = "uc_instagram";
	
	const PARAM_MENU = "uc_menu";
	const PARAM_COLORPICKER = "uc_colorpicker";
	const PARAM_LINK = "uc_link";
	const PARAM_CHECKBOX = "uc_checkbox";
	const PARAM_AUDIO = "uc_mp3";
	const PARAM_FONT_OVERRIDE = "uc_font_override";
	const PARAM_ICON = "uc_icon";
	const PARAM_ICON_LIBRARY = "uc_icon_library";
	const PARAM_SHAPE = "uc_shape";
	const PARAM_IMAGE = "uc_image";
	const PARAM_MAP = "uc_map";
	const PARAM_FORM = "uc_form";
	const PARAM_ADDONPICKER = "uc_addonpicker";
	const PARAM_TYPOGRAPHY = "uc_typography";
	const PARAM_HIDDEN = "hidden";
	const PARAM_STATIC_TEXT = "static_text";
	const PARAM_MARGINS = "uc_margins";
	const PARAM_PADDING = "uc_padding";	
	const PARAM_SLIDER = "uc_slider";
	const PARAM_GALLERY = "uc_gallery";
	
	const PARAM_BACKGROUND = "uc_background";
	const PARAM_BORDER = "uc_border";
	const PARAM_DATETIME = "uc_datetime";
	const PARAM_TEXTSHADOW = "uc_textshadow";
	const PARAM_BOXSHADOW = "uc_boxshadow";
	
	const PARAM_BORDER_DIMENTIONS = "uc_border_dimentions";
	const PARAM_CSS_FILTERS = "uc_css_filters";
	const PARAM_HOVER_ANIMATIONS = "uc_hover_animations";
	const PARAM_SPECIAL = "uc_special";
	const PARAM_POST_SELECT = "uc_post_select";
	const PARAM_TERM_SELECT = "uc_term_select";
	
	const PARAM_VAR_GET = "uc_var_get";
	const PARAM_VAR_FILTER = "uc_var_filter";
	
	protected $addon, $objSettings, $objDatasets, $addonType;
	private $type;
	private $arrContentIDs = array();
	private $arrParamsTypes = array();
	protected $arrParams = array();
	protected $arrParamsItems = array();
	protected $arrProParams = array();
	
	protected  $option_putTitle = true;
	protected  $option_putAdminLabel = true;
	protected  $option_arrTexts = array();
	protected  $option_putDecsription = true;
	protected  $option_allowFontEditCheckbox = true;
	protected  $option_putCondition = true;
	
	
	/**
	 * get instance of this object by addon type
	 */
	public static function getInstance($addonType){
		
		switch($addonType){
			case GlobalsUC::ADDON_TYPE_BGADDON:
			case "elementor":
				$classExists = class_exists("UniteCreatorDialogParamElementor");
				if($classExists == false)
					UniteFunctionsUC::throwError("class: UniteCreatorDialogParamElementor not exists");
				
				$objDialog = new UniteCreatorDialogParamElementor();
			break;
			default:
				$objDialog = new UniteCreatorDialogParam();
			break;
		}
		
		
		return($objDialog);
	}
	
	
	/**
	 * init all params
	 */
	public function __construct(){
		
		$this->initParamTypes();
		$this->initProParams();	
	}
	
	/**
	 * modify param text, function for override
	 */
	protected function modifyParamText($paramType, $paramText){
		
		return($paramText);
	}
	
	
	/**
	 * add param to the list
	 */
	protected function addParam($paramType, $paramText){
		
		$paramText = $this->modifyParamText($paramType, $paramText);
		
		$this->arrParamsTypes[$paramType] = $paramText;
	}
	
	/**
	 * init pro params
	 */
	protected function initProParams(){
		
		$this->arrProParams = array();
		$this->arrProParams[self::PARAM_TEMPLATE] = true;
		$this->arrProParams[self::PARAM_USERS] = true;
		$this->arrProParams[self::PARAM_MENU] = true;
		$this->arrProParams[self::PARAM_POST_TERMS] = true;
		$this->arrProParams[self::PARAM_WOO_CATS] = true;
		$this->arrProParams[self::PARAM_PADDING] = true;
		$this->arrProParams[self::PARAM_MARGINS] = true;
		$this->arrProParams[self::PARAM_INSTAGRAM] = true;
		$this->arrProParams[self::PARAM_POSTS_LIST] = true;
		$this->arrProParams[self::PARAM_BACKGROUND] = true;
		$this->arrProParams[self::PARAM_BORDER] = true;
		$this->arrProParams[self::PARAM_SLIDER] = true;
		$this->arrProParams[self::PARAM_LISTING] = true;
		
	}
	
	
	/**
	 * set the param types
	 */
	protected function initParamTypes(){
		
		$this->addParam("uc_textfield", esc_html__("Text Field", "unlimited-elements-for-elementor"));
		$this->addParam("uc_number", esc_html__("Number", "unlimited-elements-for-elementor"));
		$this->addParam("uc_radioboolean", esc_html__("Radio Boolean", "unlimited-elements-for-elementor"));
		$this->addParam("uc_textarea", esc_html__("Text Area", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_EDITOR, esc_html__("Editor", "unlimited-elements-for-elementor"));
		$this->addParam("uc_checkbox", esc_html__("Checkbox", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_DROPDOWN, esc_html__("Dropdown", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_MULTIPLE_SELECT, esc_html__("Multiple Select", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_COLORPICKER, esc_html__("Color Picker", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_LINK, esc_html__("Link", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_IMAGE, esc_html__("Image (media)", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_HR, esc_html__("HR Line", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_HEADING, esc_html__("Heading", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_FONT_OVERRIDE, esc_html__("Font Override", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_ADDONPICKER, esc_html__("Addon Picker", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_AUDIO, esc_html__("Audio", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_ICON, esc_html__("Icon (deprecated)", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_ICON_LIBRARY, esc_html__("Icon Library", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_SHAPE, esc_html__("Shape", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_CONTENT, esc_html__("Content", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_POST, esc_html__("Post", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_POSTS_LIST, esc_html__("Posts List", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_POST_TERMS, esc_html__("Posts Terms", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_WOO_CATS, esc_html__("WooCommerce Categories", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_LISTING, esc_html__("Dynamic Grouped Settings", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_USERS, esc_html__("Users List", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_TEMPLATE, esc_html__("Elementor Template", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_MENU, esc_html__("Menu", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_FORM, esc_html__("Form", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_INSTAGRAM, esc_html__("Instagram", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_MAP, esc_html__("Google Map", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_DATASET, esc_html__("Dataset", "unlimited-elements-for-elementor"));
		
		//variables
		$this->addParam("uc_varitem_simple", esc_html__("Simple Variable", "unlimited-elements-for-elementor"));
		$this->addParam("uc_var_paramrelated", esc_html__("Attribute Related", "unlimited-elements-for-elementor"));
		$this->addParam("uc_var_paramitemrelated", esc_html__("Item Attribute Related", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_VAR_FILTER, esc_html__("By Filter Hook", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_VAR_GET, esc_html__("GET Param", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_TYPOGRAPHY, esc_html__("Typography", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_MARGINS, esc_html__("Margins", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_PADDING, esc_html__("Padding", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_BACKGROUND, esc_html__("Background", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_BORDER, esc_html__("Border", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_BOXSHADOW, esc_html__("Box Shadow", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_TEXTSHADOW, esc_html__("Text Shadow", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_SLIDER, esc_html__("Slider", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_DATETIME, esc_html__("Date Time", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_BORDER_DIMENTIONS, esc_html__("Border Radius", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_CSS_FILTERS, esc_html__("Css Filters", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_HOVER_ANIMATIONS, esc_html__("Hover Animations", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_POST_SELECT, esc_html__("Post Select", "unlimited-elements-for-elementor"));
		$this->addParam(self::PARAM_TERM_SELECT, esc_html__("Term Select", "unlimited-elements-for-elementor"));
		
		$this->addParam(self::PARAM_SPECIAL, esc_html__("Special Attribute", "unlimited-elements-for-elementor"));
		
	}
	
		
	/**
	 * validate that the dialog inited
	 */
	private function validateInited(){
		if(empty($this->type))
			UniteFunctionsUC::throwError("Empty params dialog");
	}

	/**
	 * return if some param is pro
	 */
	protected function isProParam($paramType){
		
		if(GlobalsUC::$isProVersion == true)
			return(false);
		
		if(isset($this->arrProParams[$paramType]) == true)
			return(true);
			
		return(false);
	}
	
	
	
	private function a________MAIN_PARAMS___________(){}
	
	
	/**
	 * put instagram param
	 */
	private function putInstagramParam(){
		?>
			<div class="unite-inputs-label">
				<?php esc_html_e("Max Items", "unlimited-elements-for-elementor")?>
			</div>
			
			<input type="text" name="max_items" class="unite-input-number" value="">
			
			<div class="unite-inputs-description">
				* <?php esc_html_e("Put number of items (1-12), or empty for all the items (12)", "unlimited-elements-for-elementor")?>
			</div>
			
			<br>
			
		<?php 
		
		$this->putStyleCheckbox();
	}
	
	
	/**
	 * put google map param
	 */
	private function putGoogleMapParam(){
		?>
			<div class="unite-inputs-label">
				<?php esc_html_e("Defaults for google map", "unlimited-elements-for-elementor")?>
			</div>
			
		<?php 
	}
	
	
	/**
	 * put form param
	 */
	private function putFormParam(){
		?>
			<div class="unite-inputs-label">
				<?php esc_html_e("Form Params Goes Here", "unlimited-elements-for-elementor")?>
			</div>
		<?php 
	}
	
	/**
	 * put no default value text
	 */
	protected function putNoDefaultValueText($text = "", $addStyleCheckbox = false){
		
		if(empty($text))
			esc_html_e("No default value for this attribute", "unlimited-elements-for-elementor");
		else
			echo esc_html($text);
			
		if($addStyleCheckbox == true)
			$this->putStyleCheckbox();
	}
	
	/**
	 * put checkbox input
	 */
	private function putCheckbox($name, $text){
		?>
			<label class="unite-inputs-label-inline-free">
					<?php echo esc_html($text)?>:
				 	<input type="checkbox" onfocus="this.blur()" name="<?php echo $name?>">
			</label>
		
		<?php 
	}
	
	/**
	 * put style checkbox
	 */
	private function putStyleCheckbox(){
		?>
				<div class='uc-dialog-param-style-checkbox-wrapper'>
					<div class="unite-inputs-sap"></div>
					<label class="unite-inputs-label-inline-free">
							<?php esc_html_e("Allow Font Edit", "unlimited-elements-for-elementor")?>:
						 	<input type="checkbox" onfocus="this.blur()" name="font_editable">
					</label>
					<div class="unite-dialog-description-left"><?php esc_html_e("Allow edit font for this field in font style tab. Must be put with the {{fieldname|raw}} in html", "unlimited-elements-for-elementor")?></div>
				</div>
		<?php 
	}
	
	/**
	 * put items available only for the form
	 */
	private function putFormItemInputs(){
		
		$id = "required_checkbox_".UniteFunctionsUC::getRandomString();
		
		?>
		
		<div class="vert_sap20"></div>
			
		<div class="unite-inputs-label">
			
			<label for="<?php echo esc_attr($id)?>">
			<?php esc_html_e("Field Required", "unlimited-elements-for-elementor") ?>:
			</label> 
			
			<input id="<?php echo esc_attr($id)?>" type="checkbox" name="is_required">
			
		</div>
		
		<?php 
		
	}
	
	
	/**
	 * put default value param in params dialog
	 */
	protected function putDefaultValueParam($isTextarea = false, $class="", $addStyleChekbox = false, $useFor = ""){
		
		$addDynamic = false;
		$addPlaceholder = false;
		
		switch($useFor){
			case "textbox":
				$addDynamic = true;
				$addPlaceholder = true;
			break;
		}
				
		//disable in form item mode
		$putTextareaText = true;
		
		if($this->option_allowFontEditCheckbox == false){
			$addStyleChekbox = false;
			$putTextareaText = false;
		}

		$text = __("Default Value", "unlimited-elements-for-elementor");
		
		if($useFor == "heading"){
			$putTextareaText = false;
			$text = __("Enter Text", "unlimited-elements-for-elementor");
		}
		
		$strClass = "";
		if(!empty($class))
			$strClass = "class='{$class}'";
		
		?>
				<div class="unite-inputs-label">
					<?php echo $text?>:
				</div>
				
				<?php if($isTextarea == false):?>
				
				<input type="text" name="default_value" <?php echo UniteProviderFunctionsUC::escAddParam($strClass)?> value="">
				
				<?php else: ?>
				
				<textarea name="default_value" <?php echo UniteProviderFunctionsUC::escAddParam($strClass)?>> </textarea>
				
					<?php if($putTextareaText == true):?>
					
						<br><br>
						
						* <?php esc_html_e("To allow html tags, use","unlimited-elements-for-elementor")?> <b>|raw</b> <?php esc_html_e("filter", "unlimited-elements-for-elementor") ?> <br><br>
						&nbsp;&nbsp;&nbsp; <?php esc_html_e("example","unlimited-elements-for-elementor")?> : {{myfield|raw}}
						
					<?php endif?>
				
				<?php endif?>
		
				<?php if($addStyleChekbox == true):
					
					$this->putStyleCheckbox();
				
				endif?>
				
				<?php 
				if($this->type == self::TYPE_FORM_ITEM)
					$this->putFormItemInputs();
				?>
				
				<?php if($addDynamic == true || $addPlaceholder == true):?>
				
					<div class="unite-inputs-sap"></div>
						<hr>
				
				<?php endif?>
								
			
			<?php if($addPlaceholder):?>
					<div class="unite-inputs-sap"></div>
						
				<div class="unite-inputs-label">
					<?php _e("Placeholder Text","unlimited-elements-for-elementor")?>:
				</div>
				
				<input type="text" name="placeholder" <?php echo $strClass?> value="">
			
			<?php endif?>
			
			<?php if($addDynamic == true):?>
					<div class="unite-inputs-sap"></div>
				
				<?php $this->putCheckbox("disable_dynamic", __("Disable Dynamic Icon","unlimited-elements-for-elementor"))?>
				
			<?php endif?>
			
			
		<?php 
	}
	
	
	
	/**
	 * put font override param
	 */
	private function putFontOverrideParam(){
		?>
				
				* <?php esc_html_e("Use this font override in css tab using special function","unlimited-elements-for-elementor")?> 
				
		<?php 
	}
	
	/**
	 * put color picker default value
	 */
	protected function putColorPickerDefault(){
		
		dmp("putColorPickerDefault: option for override");		
	}
	
	

	/**
	 * put number param field
	 */
	protected function putNumberParam(){
		
		dmp("putNumberParam: option for override");
		
	}
	
	/**
	 * put radio yes no option
	 */
	private function putRadioYesNo($name, $text = null, $defaultTrue = false, $yesText = "Yes", $noText="No", $isTextNear = false){
	
		if($defaultTrue == true){
			$trueChecked = " checked ";
			$falseChecked = "";
			$defaultValue = "true";
		}else{
			$defaultValue = "false";
			$trueChecked = "";
			$falseChecked = " checked ";
		}
		
		//make not repeated id's
		$idPrefix = "uc_param_radio_".$this->type."_".$name;
		
		$idYes = $idPrefix."_yes";
		$idNo = $idPrefix."_no";
		
		?>
			<div class='uc-radioset-wrapper' data-defaultchecked="<?php echo esc_attr($defaultValue)?>">
			
			<?php if(!empty($text)): ?>
				<span class="uc-radioset-title">
				<?php esc_html_e($text, "unlimited-elements-for-elementor")?>:
				</span>
			<?php endif?>
			
				<input id="<?php echo esc_attr($idYes)?>" type="radio" name="<?php echo esc_attr($name)?>" value="true" <?php echo esc_attr($trueChecked)?>>
				<label for="<?php echo esc_attr($idYes)?>"><?php _e($yesText, "unlimited-elements-for-elementor")?></label>
				
				<input id="<?php echo esc_attr($idNo)?>" type="radio" name="<?php echo esc_attr($name)?>" value="false" <?php echo esc_attr($falseChecked)?>>
				<label for="<?php echo esc_attr($idNo)?>"><?php _e($noText, "unlimited-elements-for-elementor")?></label>
				
				<?php if($isTextNear == true):?>
					<input type="text" name="text_near" class="unite-input-medium">
					<?php esc_html_e("(text near)", "unlimited-elements-for-elementor")?>
					
				<?php endif?>
			</div>
			
		
		<?php 
	}
	
	
	/**
	 * put radio boolean param
	 */
	protected function putRadioBooleanParam(){
		
		dmp("function for override");
		 
	}
	
	
	/**
	 * add checkbox section param to image param type
	 */
	private function putImageParam_addThumbSection($thumbName, $text, $addSuffix){
		$IDprefix = "uc_param_image_".$this->type."_";
		
		$checkID = $IDprefix.$thumbName;
		$inputID = $IDprefix.$thumbName."_input";
		
		?>
			<label for="<?php echo esc_attr($checkID)?>">
				<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-image-checkbox uc-control" data-controlled-selector="#<?php echo esc_attr($inputID)?>" name="<?php echo esc_attr($thumbName)?>">
				<?php _e($text, "unlimited-elements-for-elementor")?>
			</label>
			<input id="<?php echo esc_attr($inputID)?>" type="text" data-addsuffix="<?php echo esc_attr($addSuffix)?>" style="display:none" disabled class="mleft_5 unite-input-alias uc-param-image-thumbname">
			
		<?php 
	}
	
	
	/**
	 * put image param settings
	 */
	private function putImageParam(){
		
		$arrTypes = array();
		$arrTypes["image"] = "Image";
		$arrTypes["json"] = "Json (lottie)";
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrTypes,"image", "name='media_type' class='uc-control' data-controlled-selector='.uc-media-param-image-attributes'",true);
		
		?>
			<?php _e("Media Type","unlimited-elements-for-elementor") ?>: 
			
			<div class="unite-inputs-sap"></div>
			
			<?php echo $htmlSelect?>
			
			<div class="unite-inputs-sap-double"></div>
			
			<div class="uc-media-param-image-attributes" data-control="image">
			
				
				<?php $this->putImageSelectInput("default_value",esc_html__("Default Image","unlimited-elements-for-elementor")); ?>
			
				<div class="unite-inputs-sap-double"></div>
				
				<?php $this->putCheckbox("add_image_sizes", __("Add Image Size Select","unlimited-elements-for-elementor"))?>
			
			</div>
			
			<div class="uc-media-param-image-attributes" data-control="json">
			
				<div class="unite-inputs-label">
					<?php esc_html_e("Default Json File", "unlimited-elements-for-elementor")?>
				</div>
				
				<input type="text" name="default_value_json" value="">
				
				<div class="unite-inputs-description">
					* <?php esc_html_e("Write a json file, from assets folder. Important to specify it that the widget will not look empty.", "unlimited-elements-for-elementor")?>
				</div>
			
			</div>
			
			
			
		<?php
	}
	
	
	/**
	 * put single setting input
	 */
	private function putSingleSettingInput($name, $text, $type){
		
		?>			
			<div class="unite-inputs-label"><?php echo esc_html($text)?>:</div>
		<?php 
		
		$objSettings = new UniteCreatorSettings();
		$objSettings->setCurrentAddon($this->addon);
		
		switch($type){
			case "image":
				$objSettings->addImage($name, "", $text, array("source"=>"addon"));
			break;
			case "mp3":
				$objSettings->addMp3($name, "", $text, array("source"=>"addon"));
			break;
			default:
				UniteFunctionsUC::throwError("Wrong seting type: $type");
			break;
		}
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		$objOutput->drawSingleSetting($name);
		
	}
	
	
	/**
	 * put image select input
	 */
	protected function putImageSelectInput($name, $text){
		
		$this->putSingleSettingInput($name, $text, "image");
	}
	
	
	/**
	 * put mp3 select input
	 */
	private function putMp3SelectInput($name, $text){
		
		$this->putSingleSettingInput($name, $text, "mp3");
		
	}

	
	/**
	 * put mp3 param
	 */
	private function putMp3Param(){
	
		$this->putMp3SelectInput("default_value",esc_html__("Default Audio File Url","unlimited-elements-for-elementor"));
	}
	
	/**
	 * put menu param
	 */
	protected function putMenuParam(){
		//function for override n
	}

	
	/**
	 * put menu param
	 */
	private function putDatasetParam(){
				
		$arrDatasetsNames = $this->objDatasets->getDatasetTypeNames();
		$settings = new UniteCreatorSettings();
		
		if(empty($arrDatasetsNames))
			$settings->addStaticText("No dataset types found");
		else{
			
			$firstType = UniteFunctionsUC::getFirstNotEmptyKey($arrDatasetsNames);
			$arrDatasetsNames = array_flip($arrDatasetsNames);
			
			$settings->addSelect("dataset_type", $arrDatasetsNames, esc_html__("Choose Dataset Type", "unlimited-elements-for-elementor"), $firstType ,array("description"=>"select the datase type"));
			
			//put queries
			$arrDatasetObjects = $this->objDatasets->getDatasetTypes();
			
			foreach($arrDatasetObjects as $type=>$dataset){
								
				$queries = UniteFunctionsUC::getVal($dataset, "queries");
				
				if(empty($queries))
					continue;
				
				$firstQuery = UniteFunctionsUC::getFirstNotEmptyKey($queries);
				$queries = array_flip($queries);
				
				$queries["---Not Selected---"] = "";
				
				$settingName = "dataset_{$type}_query";
				$settings->addSelect($settingName, $queries, esc_html__("Choose Query", "unlimited-elements-for-elementor"), $firstQuery ,array("description"=>"select the dataset query"));
				$settings->addControl("dataset_type", $settingName, "show", $type);
			}
			
		}
		
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($settings);
		$objOutput->draw("dataset_param_settings", false);
	}
	
	
	/**
	 * put addonpicker addon
	 */
	private function putAddonPickerParam(){
		
		$arrTypes = UniteCreatorAddonType::getAddonTypesForAddonPicker();
		$firstType = UniteFunctionsUC::getFirstNotEmptyKey($arrTypes);
		$arrTypes = array_flip($arrTypes);
		
		$settings = new UniteCreatorSettings();

		$settings->addSelect("addon_type", $arrTypes, esc_html__("Choose Addon Type", "unlimited-elements-for-elementor"), $firstType ,array("description"=>"select the addon type"));
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($settings);
		$objOutput->draw("addonpicker_param_settings", false);
				
	}
	
	
	/**
	 * put users param
	 */
	protected function putUsersParam(){
		dmp("function for override");
	}
	
	
	/**
	 * put template param
	 */
	protected function putTemplateParam(){
		dmp("function for overrie");
	}
	
	
	/**
	 * put post terms param
	 */
	private function putPostTermsParam(){
		
		esc_html_e("Post terms are post categories / tags and other custom types. Also called as taxonomies ", "unlimited-elements-for-elementor");
		
		$arrFilterType = array();
		$arrFilterType["none"] = __("No","unlimited-elements-for-elementor");
		$arrFilterType["filter_option"] = __("Has Filter Option","unlimited-elements-for-elementor");
		
		$selectFilter = HelperHtmlUC::getHTMLSelect($arrFilterType,"none","name='filter_type'", true);
		
		?>
		<br>
		<br>
		
		<div class="vert_sap10"></div>
		
		<?php 
		$this->putCheckbox("use_custom_fields", __("Use Custom Fields", "unlimited-elements-for-elementor"));
		?>
		<div class="vert_sap20"></div>
		
		<?php 
		$this->putCheckbox("for_woocommerce", __("For WooCommerce Terms", "unlimited-elements-for-elementor"));
		?>
		
		<div class="vert_sap20"></div>
		
		<label class="unite-inputs-label-inline-free">
				<?php _e("Posts Filter Options")?> :
		</label>
		<?php echo $selectFilter?>
		
		<br><br>
		
		<hr>
		
		<?php 
		
		$this->putStyleCheckbox();
	}
	
	/**
	 * put woo cats param
	 */
	private function putWooCatsParam(){
		
		$this->putPostTermsParam();
		
	}
	
	/**
	 * put listing param
	 */
	protected function putListingParam(){
		
		$arrItems = array(
			"template"=>__("Template Loop","unlimited-elements-for-elementor"),
			"gallery"=>__("Gallery","unlimited-elements-for-elementor"),
			"remote"=>__("Remote","unlimited-elements-for-elementor"),
			"items"=>__("Items","unlimited-elements-for-elementor")
		);
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrItems,"template","name='use_for' class='unite-inputs-select uc-control' data-controlled-selector='.uc-listing-param-options'", true);
		
		$arrRemoteItems = array(
			"parent"=>__("Remote Parent","unlimited-elements-for-elementor"),
			"controller"=>__("Remote Controller","unlimited-elements-for-elementor"),
			"background"=>__("Remote Background","unlimited-elements-for-elementor")
		);
		
		$htmlSelectRemote = HelperHtmlUC::getHTMLSelect($arrRemoteItems,"parent","name='remote_type' class='unite-inputs-select'", true);
		
		?>
		
		<?php _e("Use For","unlimited-elements-for-elementor")?>:
		
		<?php echo $htmlSelect?>
		
		<div class="unite-inputs-sap-double"></div>
		
		<!-- Gallery  -->
		
		<div class="uc-listing-param-options" data-control="gallery">
		<?php 
		
			$this->putCheckbox("gallery_enable_video", __("Enable Video Items", "unlimited-elements-for-elementor"));
			
		?>
		
		<div class="unite-inputs-sap"></div>
		
		</div>

		<!-- Template  -->
		
		<div class="uc-listing-param-options" data-control="template,gallery,items">
		<?php 
			$this->putCheckbox("enable_pagination", __("Add Pagination", "unlimited-elements-for-elementor"));
		?>
		
		<div class="unite-inputs-sap"></div>
		
		<?php 
			$this->putCheckbox("enable_ajax", __("Add Filtering", "unlimited-elements-for-elementor"));
		?>
		
		</div>
		
		<!-- Items  -->
		<div class="uc-listing-param-options" data-control="items">
		
			<div class="unite-inputs-sap-double"></div>
				
			<label class="unite-inputs-label">
				<?php _e("Included Attributes", "unlimited-elements-for-elementor")?>: 
			</label>
			
			<div class="unite-inputs-sap"></div>
						
			<input type="text" name="multisource_included_attributes" value="" class="unite-input-link">
					
			<div class="unite-dialog-description-left">
			
			* <?php esc_html_e("list here all the fields that will be included in the multisource comma saparated like ","unlimited-elements-for-elementor")?> 
				<b>title,image,other</b>		
			</div>
			
			<div class="unite-inputs-sap-double"></div>
			
			<label class="unite-inputs-label">
				<?php _e("Default Values", "unlimited-elements-for-elementor")?>: 
			</label>
						
			<div class="unite-inputs-sap"></div>
			
			<input type="text" name="multisource_attributes_defaults" value="" class="unite-input-link">
			
			<div class="unite-dialog-description-left">
			
			* <?php esc_html_e("comma saparated defalut values of the items fields. exampe:field=value,field2=value2","unlimited-elements-for-elementor")?> 
			</div>
			
		</div>
		
		
		<!-- Remote  -->
		
		<div class="uc-listing-param-options" data-control="remote">
		
			<?php 
			
			_e("Widget Type","unlimited-elements-for-elementor");?>:
			
			<?php echo $htmlSelectRemote; ?>
			
			<div class="unite-inputs-sap"></div>
			
			<?php 
				$this->putCheckbox("controller_more_parents", __("Add More Parent Connect (for controller only)", "unlimited-elements-for-elementor"));
			?>
			
		</div>
		
		<?php 
		
	}
	
	
	/**
	 * put post list param
	 */
	private function putPostListParam(){
				
		$settings = new UniteCreatorSettings();

		$params = array();
		$params["description"] = __("Choose some post for the custom fields to appear in attributes list in the right", "unlimited-elements-for-elementor");
		
		$settings->addPostPicker("post_example", "", __("Post Example For Custom Fields", "unlimited-elements-for-elementor") );
				
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($settings);
		$objOutput->draw("postpicker_param_settings", false);
		
		$this->putCheckbox("use_custom_fields", __("Use Custom Fields", "unlimited-elements-for-elementor"));
		
		?>
		<div class="vert_sap10"></div>
		<?php 
		$this->putCheckbox("use_category", __("Use Post Category", "unlimited-elements-for-elementor"));
		
		?>
		<div class="vert_sap10"></div>
		
		<hr>
		
		<div class="vert_sap10"></div>
		
		<label class="unite-inputs-label">
			<?php _e("Default Max Posts", "unlimited-elements-for-elementor")?>: 
		</label>
		
		<input type="text" name="default_max_posts" value="" class="unite-input-number" placeholder="10">
		
		<div class="vert_sap10"></div>
		
		<?php 
			$this->putCheckbox("for_woocommerce_products", __("For WooCommerce Products", "unlimited-elements-for-elementor"));
		?>
		<div class="vert_sap10"></div>
		
		<?php 
			$this->putCheckbox("show_image_sizes", __("Show Image Sizes Select", "unlimited-elements-for-elementor"));
		?>
		
		<div class="vert_sap10"></div>
		
		<?php 
			$this->putCheckbox("enable_ajax", __("Enable Ajax / Filters Options", "unlimited-elements-for-elementor"));
		?>
		
		<div class="vert_sap10"></div>
		
		<?php 
			$this->putCheckbox("disable_pagination", __("Disable Pagination Pane", "unlimited-elements-for-elementor"));
		?>
		
		<div class="vert_sap10"></div>
		
		<hr>
		
		<div class="vert_sap10"></div>
		<?php 
		
		$this->putStyleCheckbox();
		
	}
	
	private function a___________FOR_OVERRIDE________(){}
	
	/**
	 * function for override
	 */
	protected function putDimentionsParam($type = ""){
		dmp("putDimentionsParam: function for override");
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putSliderParam(){
		dmp("putSliderParam: function for override");
		UniteFunctionsUC::showTrace();
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putBackgroundParam(){
		dmp("putBackgroundParam: function for override");
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putBorderParam(){
		dmp("putBorderParam: function for override");
		exit();
	}

	/**
	 * function for override
	 */
	protected function putDateTimeParam(){
		dmp("putDateTimeParam: function for override");
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putTextShadowParam(){
		dmp("putTextShadowParam: function for override");
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putBoxShadowParam(){
		dmp("putTextShadowParam: function for override");
		exit();
	}
	

	/**
	 * function for override
	 */
	protected function putCssFiltersParam(){
		dmp("putCssFiltersParam: function for override");
		exit();
	}
	
	/**
	 * function for override
	 */
	protected function putHoverAnimations(){
		dmp("putHoverAnimations: function for override");
		exit();
	}
	
	/**
	 * 
	 * function for override
	 */
	protected function putSpecialAttribute(){
		
		
		dmp("putSpecialAttribute: function for override");
		exit();
		
	}
		
	/**
	 * 
	 * function for override
	 */
	protected function putPostSelectAttribute(){
		
		dmp("putPostSelectAttribute: function for override");
		exit();
		
	}

	/**
	 * function for override
	 */
	protected function putTermSelectAttribute(){
		
		dmp("putTermSelectAttribute: function for override");
		exit();
		
	}
	
	
	private function a___________DROPDOWN_PARAM________(){}
	
	/**
	 * php filter options
	 */
	protected function addPHPFilterOptions($type){
		?>
		
		<div class="unite-inputs-sap"></div>
		
		<hr>
		
		<div class="unite-inputs-sap"></div>

		<div class="unite-inputs-label">
			<?php esc_html_e("PHP Filter Name", "unlimited-elements-for-elementor")?>
		</div>
		
		<input type="text" name="php_filter_name" class="input-regular" value="">
		
		<div class="unite-dialog-description-left">
			<?php _e("* With this setting you can set or modify dropdown items in php.", "unlimited-elements-for-elementor")?>
			
			<a href="https://unlimited-elements.com/docs/modify-dropdown-items-with-php/" target="_blank"><?php _e("instructions","unlimited-elements-for-elementor")?></a>
			
		</div>
		
		
		<?php 
	}
	
	
	/**
	 * put drop down param
	 */
	protected function putDropDownParam(){
		
		dmp("function for overwrite");
		
	}
	
	/**
	 * put multiple select param
	 */
	private function putMultipleSelectParam(){
		
		$this->putDropdownItems(true);
		
		$this->addPHPFilterOptions("dropdown");
		
	}
	
	/**
	 * put dropdown items table
	 */
	protected function putDropdownItems($isMultiple = false){
		
		$addParams = "";
		if($isMultiple == true)
			$addParams = "data-ismultiple=\"true\"";
		
		?>
				<table data-inputtype="table_dropdown" <?php echo $addParams?> class='uc-table-dropdown-items uc-table-dropdown-full'>
					<thead>
						<tr>
							<th></th>
							<th width="100px"><?php esc_html_e("Item Text", "unlimited-elements-for-elementor")?></th>
							<th width="100px"><?php esc_html_e("Item Value", "unlimited-elements-for-elementor")?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><div class='uc-dropdown-item-handle'></div></td>
							<td><input type="text" value="" class='uc-dropdown-item-name'></td>
							<td><input type="text" value="" class='uc-dropdown-item-value'></td>
							<td>
								<div class='uc-dropdown-icon uc-dropdown-item-delete' title="<?php esc_html_e("Delete Item", "unlimited-elements-for-elementor")?>"></div>
								<div class='uc-dropdown-icon uc-dropdown-item-add' title="<?php esc_html_e("Add Item", "unlimited-elements-for-elementor")?>"></div>
								<div class='uc-dropdown-icon uc-dropdown-item-default uc-selected' title="<?php esc_html_e("Default Item", "unlimited-elements-for-elementor")?>"></div>
							</td>
						</tr>
					</tbody>
				</table>
		
		<?php 
	}
	
	
	/**
	 * put select related dropdown
	 */
	private function putDropdownSelectRelated($selectSelector, $valueText = null, $putText = null){
		
		$valueTextOutput = esc_html__("Attribute Value", "unlimited-elements-for-elementor");
		$putTextOutput = esc_html__("Html Output", "unlimited-elements-for-elementor");
		
		if(!empty($valueText))
			$valueTextOutput = $valueText;
		
		if(!empty($putText))
			$putTextOutput = $putText;
		
		?>
				<table data-inputtype="table_select_related" class='uc-table-dropdown-items uc-table-dropdown-simple uc-table-select-related' data-relateto="<?php echo esc_attr($selectSelector)?>">
					<thead>
						<tr>
							<th><?php echo esc_html($valueTextOutput)?></th>
							<th><?php echo esc_html($putTextOutput)?></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
		<?php 
	}
	
	
	private function a___________VARIABLE_PARAMS_________(){}
	
	
	/**
	 * put item variable fields
	 */
	private function putVarItemSimpleFields(){
		
		$checkboxFirstID = "uc_check_first_varitem_".$this->type;
		$checkboxLastID = "uc_check_last_varitem_".$this->type;
		
		?>
			
			<div class="unite-inputs-label">
				<?php esc_html_e("Default Value", "unlimited-elements-for-elementor")?>:
			</div>
			
			<input type="text" name="default_value" value="" class="uc_default_value">
			
			<a class="uc-link-add" data-addto-selector=".uc_default_value" data-addtext="%numitem%" href="javascript:void(0)"><?php esc_html_e("Add Numitem", "unlimited-elements-for-elementor")?></a>
			
			<div class="unite-inputs-label mtop_5 mbottom_5">
				
				<input id="<?php echo esc_attr($checkboxFirstID)?>" type="checkbox" name="enable_first_item" class="uc-control" data-controlled-selector=".uc_section_first">
				
				<label for="<?php echo esc_attr($checkboxFirstID)?>">
				<?php esc_html_e("Value for First Item", "unlimited-elements-for-elementor")?>:
				</label>
			</div>
			
			<div class="uc_section_first" style="display:none">
				
				<input type="text" name="first_item_value" value="" class="uc_first_item_value">
				
				<a class="uc-link-add" data-addto-selector=".uc_first_item_value" data-addtext="%numitem%" href="javascript:void(0)"><?php esc_html_e("Add Numitem", "unlimited-elements-for-elementor")?></a>
				
			</div>
			
			<div class="unite-inputs-label mtop_5 mbottom_5">
				
				<input id="<?php echo esc_attr($checkboxLastID)?>" type="checkbox" name="enable_last_item" class="uc-control" data-controlled-selector=".uc_section_last">
				
				<label for="<?php echo esc_attr($checkboxLastID)?>">
				<?php esc_html_e("Value for Last Item", "unlimited-elements-for-elementor")?>:
				</label>
			</div>
			
			<div class="uc_section_last" style="display:none">
				
				<input type="text" name="last_item_value" value="" class="uc_last_item_value" >
				
				<a class="uc-link-add" data-addto-selector=".uc_last_item_value" data-addtext="%numitem%" href="javascript:void(0)"><?php esc_html_e("Add Numitem", "unlimited-elements-for-elementor")?></a>
							
			</div>
			
			<div class="unite-dialog-description-right">
				* <?php esc_html_e("The %numitem% is 1,2,3,4... numbers serials", "unlimited-elements-for-elementor")?>
			</div>
			
		<?php
	}
	
	
	/**
	 * put fields of item params related variable
	 * type: item / main
	 */
	private function putParamsRelatedFields($type = "main"){
		
		$title = esc_html__("Select Main Attribute", "unlimited-elements-for-elementor");
		$source = "main";
		
		if($type == "item"){
			$title = esc_html__("Select Item Attribute", "unlimited-elements-for-elementor");
			$source = "item";
		}
		
		?>
		
		<div class="unite-inputs-label-inline-free ptop_5" >
			<?php echo esc_html($title)?>:
		</div>
		
		<select class="uc-select-param uc_select_param_name" data-source="<?php echo esc_attr($source)?>" name="param_name"></select>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="uc-dialog-param-min-height">
		
		<?php $this->putDropdownSelectRelated(".uc_select_param_name");?>
		
		</div>
		
		<?php HelperHtmlUC::putDialogControlFieldsNotice() ?>
		
		<?php
		
	}
	
	/**
	 * put GET query string params
	 */
	private function putGetParamFields(){
		
		$text = esc_html__("This parameter will go from GET query string", "unlimited-elements-for-elementor");
		
		?>			
			<div class="unite-inputs-label"><?php echo esc_html($text)?>:</div>
		<?php 
		
		$objSettings = new UniteCreatorSettings();
		
		$arrSanitize = UniteFunctionsUC::getArrSanitizeTypes();		
		$firstType = UniteFunctionsUC::getFirstNotEmptyKey($arrSanitize);
		$arrSanitize = array_flip($arrSanitize);
		
		$objSettings->addSelect("sanitize_type", $arrSanitize, esc_html__("Sanitize Type", "unlimited-elements-for-elementor"), $firstType);
		$objSettings->addTextBox("default_value", "", esc_html__("Default Value", "unlimited-elements-for-elementor"));
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		$objOutput->draw("get_param_settings", false);
		
	}
	
	
	private function a___________OUTPUT_________(){}
	
	
	/**
	 * put tab html
	 */
	private function putTab($paramType, $isSelected = false, $isSelect = false){
		
		$tabPrefix = "uc_tabparam_".$this->type."_";
		$contentID = $tabPrefix.$paramType;
		
		$isProParam = $this->isProParam($paramType);
				
		//check for duplicates
		if(isset($this->arrContentIDs[$paramType]))
			UniteFunctionsUC::throwError("dialog param error: duplicate tab type: $paramType");
		
		//save content id
		$this->arrContentIDs[$paramType] = $contentID;
		
		$title = UniteFunctionsUC::getVal($this->arrParamsTypes, $paramType);
		if(empty($title))
			UniteFunctionsUC::throwError("Attribute: {$paramType} is not found in param list.");
		
		$addHtml = "";
		if($isProParam == true){
			$title .= " (pro)";
			$addHtml .= " data-ispro='true'";
		}
		
		//put tab content
		$class = "uc-tab";
		$selectHtml = "";
		if($isSelected == true){
			$class = "uc-tab uc-tab-selected";
			$selectHtml .= " selected='selected' ";
		}
		
		if($this->type == self::TYPE_MAIN && isset($this->arrParamsItems[$paramType]) == false)
			$selectHtml .= " class='uc-hide-when-item'";
		
		if($isSelect == true):
		?>
			<option <?php echo UniteProviderFunctionsUC::escAddParam($selectHtml)?> data-type="<?php echo esc_attr($paramType)?>" value="<?php echo esc_attr($contentID)?>" <?php echo $addHtml?> >
				<?php _e($title, "unlimited-elements-for-elementor")?>
			</option>
		<?php
		else:
		?>
			<a href="javascript:void(0)" data-type="<?php echo esc_attr($paramType)?>" data-contentid="<?php echo esc_attr($contentID)?>" class="<?php echo esc_attr($class)?>" <?php echo $addHtml?>>
				<?php _e($title, "unlimited-elements-for-elementor")?>
			</a>
		<?php
		endif;
		
	}
	
	/**
	 * put filter param
	 */
	private function putVarFilter(){
		?>
			<div class="unite-inputs-label">
				<?php esc_html_e("Filter Name", "unlimited-elements-for-elementor")?>:
			</div>
			
			<input type="text" name="filter_name" value="" class="uc_default_value">
			
						
			<div class="unite-inputs-sap"></div>
			
			<div class="unite-inputs-label">
				<?php esc_html_e("Filter Parameter", "unlimited-elements-for-elementor")?>:
			</div>
			
			<input type="text" name="filter_param" value="" >
			
			<div class="unite-dialog-description-right">
				* <?php esc_html_e("Write every filter name that could be run by apply_filters php function", "unlimited-elements-for-elementor")?>
			</div>
			
		<?php
	}
	
	/**
	 * put param content
	 */
	protected function putParamFields($paramType){
		
		switch($paramType){
			case "uc_textfield":
				$this->putDefaultValueParam(false, "", true, "textbox");
			break;
			case "uc_number":
				$this->putNumberParam();
			break;
			case "uc_radioboolean":
				$this->putRadioBooleanParam();
			break;
			case "uc_textarea":
				$this->putDefaultValueParam(true,"",true);
			break;
			case self::PARAM_EDITOR:
				$this->putDefaultValueParam(true);
			break;
			case "uc_checkbox":
				$this->putRadioYesNo("is_checked", esc_html__("Checked By Default", "unlimited-elements-for-elementor"), false, "Yes", "No", true);
			break;
			case self::PARAM_DROPDOWN:
				$this->putDropDownParam();
			break;
			case self::PARAM_MULTIPLE_SELECT:
				$this->putMultipleSelectParam();
			break;
			case self::PARAM_LINK:
				$this->putDefaultValueParam(false, "", false);
			break;
			case self::PARAM_COLORPICKER:
				$this->putColorPickerDefault();
			break;
			case self::PARAM_IMAGE:
				$this->putImageParam();
			break;
			case "uc_mp3":
				$this->putMp3Param();
			break;
			case self::PARAM_ICON:
				$this->putDefaultValueParam();
			break;
			case self::PARAM_ICON_LIBRARY:
				$this->putIconLibraryParam();
			break;
			case self::PARAM_SHAPE:
				$this->putNoDefaultValueText();
			break;
			case self::PARAM_CONTENT:
				$this->putDefaultValueParam(true,"");
			break;
			case self::PARAM_POSTS_LIST:
				$this->putPostListParam();
			break;
			case self::PARAM_USERS:
				$this->putUsersParam();
			break;
			case self::PARAM_TEMPLATE:
				$this->putTemplateParam();
			break;
			case self::PARAM_POST_TERMS:
				$this->putPostTermsParam();
			break;
			case self::PARAM_WOO_CATS:
				$this->putWooCatsParam();
			break;
			case self::PARAM_LISTING:
				$this->putListingParam();
			break;
			case self::PARAM_FORM:
				$this->putFormParam();
			break;
			case self::PARAM_INSTAGRAM:
				$this->putInstagramParam();
			break;
			case self::PARAM_MAP:
				$this->putGoogleMapParam();
			break;
			case self::PARAM_HR:
				$this->putNoDefaultValueText();
			break;
			case self::PARAM_HEADING:
				$this->putDefaultValueParam(true,"",false,"heading");
			break;
			case self::PARAM_FONT_OVERRIDE:
				$text = esc_html__("Use this font override in css tab using special function", "unlimited-elements-for-elementor");
				$this->putNoDefaultValueText($text);
			break;
			//variable params
			case "uc_varitem_simple":
				$this->putVarItemSimpleFields();
			break;
			case "uc_var_paramrelated":
				$this->putParamsRelatedFields("main");
			break;
			case "uc_var_paramitemrelated":
				$this->putParamsRelatedFields("item");
			break;
			case self::PARAM_VAR_FILTER:
				$this->putVarFilter();
			break;
			case self::PARAM_MENU:
				$this->putMenuParam();
			break;
			case self::PARAM_DATASET:
				$this->putDatasetParam();
			break;
			case self::PARAM_ADDONPICKER:
				$this->putAddonPickerParam();
			break;
			case self::PARAM_MARGINS:
				$this->putDimentionsParam("margin");
			break;
			case self::PARAM_PADDING:
				$this->putDimentionsParam("padding");
			break;
			case self::PARAM_BORDER_DIMENTIONS:
				$this->putDimentionsParam("border");
			break;
			case self::PARAM_SLIDER:
				$this->putSliderParam();
			break;
			case self::PARAM_BACKGROUND:
				$this->putBackgroundParam();
			break;
			case self::PARAM_BORDER:
				$this->putBorderParam();
			break;
			case self::PARAM_DATETIME:
				$this->putDateTimeParam();
			break;
			case self::PARAM_TEXTSHADOW:
				$this->putTextShadowParam();
			break;
			case self::PARAM_BOXSHADOW:
				$this->putBoxShadowParam();
			break;
			case self::PARAM_CSS_FILTERS:
				$this->putCssFiltersParam();
			break;
			case self::PARAM_HOVER_ANIMATIONS:
				$this->putHoverAnimations();
			break;
			case self::PARAM_SPECIAL:
				$this->putSpecialAttribute();
			break;
			case self::PARAM_POST_SELECT:
				$this->putPostSelectAttribute();
			break;
			case self::PARAM_TERM_SELECT:
				$this->putTermSelectAttribute();
			break;
			case self::PARAM_VAR_GET:
				$this->putGetParamFields();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong param type, fields not found: $paramType");
			break;
		}
		
	}
	
	
	/**
	 * get texts array
	 */
	private function getArrTexts(){
		
		$arrTexts = array();
		
		switch($this->type){
			case self::TYPE_FORM_ITEM:
				$arrTexts["add_title"] = esc_html__("Add Form Item","unlimited-elements-for-elementor");
				$arrTexts["add_button"] = esc_html__("Add Form Item","unlimited-elements-for-elementor");
				$arrTexts["edit_title"] = esc_html__("Edit Form Item","unlimited-elements-for-elementor");
				$arrTexts["update_button"] = esc_html__("Update Form Item","unlimited-elements-for-elementor");				
			break;
			default:
				$arrTexts["add_title"] = esc_html__("Add Attribute","unlimited-elements-for-elementor");
				$arrTexts["add_button"] = esc_html__("Add Attribute","unlimited-elements-for-elementor");
				$arrTexts["edit_title"] = esc_html__("Edit Attribute","unlimited-elements-for-elementor");
				$arrTexts["update_button"] = esc_html__("Update Attribute","unlimited-elements-for-elementor");				
			break;
		}
		
		$arrTexts = array_merge($arrTexts, $this->option_arrTexts);
		
		return($arrTexts);
	}
	
	
	/**
	 * put dialog tabs
	 */
	private function putTabs(){
		?>
		<div class="uc-tabs uc-tabs-paramdialog">
			<?php 
			
			$firstParam = true;
			foreach($this->arrParams as $paramType){
			
				$this->putTab($paramType, $firstParam);
				$firstParam = false;
			}
			
			?>			
		</div>
		
		<div class="unite-clear"></div>
		
		<?php 
	}
	
	/**
	 * put tabs as dropdown
	 */
	private function putTabsDropdown(){
		?>
		
		<?php esc_html_e("Attribute Type: " , "unlimited-elements-for-elementor")?>
		
		<select class="uc-paramdialog-select-type">
			
			<?php
				$firstParam = true;
				foreach($this->arrParams as $paramType){
					$this->putTab($paramType, $firstParam, true);
					$firstParam = false;
				}
			?>
		</select>
		<?php
		
	}

	/**
	 * put condition
	 */
	private function putHtmlConditionLeft(){
			
		HelperHtmlUC::putHtmlConditions($this->type);
	}
	
	
	/**
	 * output html
	 */
	public function outputHtml(){
		
		$this->validateInited();
		$type = $this->type;
		$dialogID = "uc_dialog_param_".$type;
		
		//fill texts
		$arrTexts = $this->getArrTexts();
		$dataTexts = UniteFunctionsUC::jsonEncodeForHtmlData($arrTexts);
		
		$linkDownloadPro = HelperHtmlUC::getHtmlLink(GlobalsUC::URL_DOWNLOAD_PRO, __("client panel","unlimited-elements-for-elementor"),"","",true);
		$linkBuyPro = HelperHtmlUC::getHtmlLink(GlobalsUC::URL_BUY, __("PRO version","unlimited-elements-for-elementor"),"","",true);
		
		//put items param types
		$addParams = "";
		
		$checkboxBlockLabelID = "uc_dialog_left_blocklabel_".$this->type;
		
		?>
			
			<!-- Dialog Param: <?php echo esc_html($type)?> -->
			
			<div id="<?php echo esc_attr($dialogID)?>" class="uc-dialog-param uc-dialog-param-<?php echo esc_attr($type)?>" data-texts="<?php echo esc_attr($dataTexts)?>" <?php echo $addParams?> style="display:none">
				
				<div class="dialog-param-wrapper unite-inputs">
					
					<?php 
						$this->putTabsDropdown();
					?>
					
					<div class="uc-tabsparams-content-wrapper">
					
						<div class="dialog-param-left">
							
							<?php if($this->option_putTitle == true): ?>
							
								<div class="unite-inputs-label">
								<?php esc_html_e("Title")?>:
								</div>
								
								<input type="text" class="uc-param-title" name="title" value="">
								
								<div class="unite-inputs-sap"></div>
							
							<?php endif?>
							
							<div class="unite-inputs-label">
							<?php esc_html_e("Name", "unlimited-elements-for-elementor")?>:
							</div>
							<input type="text" class="uc-param-name" name="name" value="">
							
							<?php if($this->option_putDecsription == true):?>
							<div class="unite-inputs-sap"></div>
							
							<div class="unite-inputs-label">
							<?php esc_html_e("Description", "unlimited-elements-for-elementor")?>:
							</div>
							
							<textarea name="description"></textarea>
							
							<?php endif?>
							
							<?php if($this->option_putCondition == true):
								$this->putHtmlConditionLeft();
							?>
								
							<?php endif?>
							
							<div class="unite-inputs-sap"></div>
							
							<label for="<?php echo $checkboxBlockLabelID?>" class="unite-inputs-label-inline-free">
									<?php esc_html_e("Label Block", "unlimited-elements-for-elementor")?>:
							</label>
							<input id="<?php echo $checkboxBlockLabelID?>" type="checkbox" name="label_block">
							
							<div class="unite-inputs-sap"></div>
								
							<label class="uc-dialog-label-inline">
									<?php esc_html_e("Tab", "unlimited-elements-for-elementor")?>:
							</label>
							
							<input type="text" name="tabname" class="unite-input-medium">
							
							
							<?php if($this->option_putAdminLabel == true):?>
							<div class='uc-dialog-param-admin-label-wrapper'>
								<div class="unite-inputs-sap"></div>
								
								<div class="unite-inputs-label-inline-free">
										<?php esc_html_e("Admin Label", "unlimited-elements-for-elementor")?>:
								</div>
								<input type="checkbox" name="admin_label">
								<div class="unite-dialog-description-left"><?php esc_html_e("Show attribute content on admin side", "unlimited-elements-for-elementor")?></div>
							</div>
							<?php endif?>
							
							<?php if(GlobalsUC::$isProVersion == false):?>
							
							<div class='uc-dialog-param-pro-message'>
								<?php _e("This attribute is available only in the .","unlimited-elements-for-elementor"); 
								echo $linkBuyPro;
								?>
								<br>
								<?php _e("The PRO version (unlimited-elements-pro) is available for download in the ","unlimited-elements-for-elementor");?>
								<?php echo $linkDownloadPro?>
								<?php _e(" under \"downloads\" section.","unlimited-elements-for-elementor")?>
								 
							</div>
							<?php endif?>
							
						</div>
						
						
						<div class="dialog-param-right">
							
							<?php 
							
							$firstParam = true;
							foreach($this->arrParams as $paramType):
								
								$tabContentID = UniteFunctionsUC::getVal($this->arrContentIDs, $paramType);
								if(empty($tabContentID))
									UniteFunctionsUC::throwError("No content ID found for param: {$paramType} ");
								
								$addHTML = "";
								$addClass = "uc-content-selected";
								if($firstParam == false){
									$addHTML = " style='display:none'";
									$addClass = "";
								}
								
								$firstParam = false;
								
								//is pro param
								$isProParam = $this->isProParam($paramType);
													
								if($isProParam == true)
									$addClass .= " uc-pro-param";
								
								?>
								
								<!-- <?php echo esc_html($paramType)?> fields -->
								
								<div id="<?php echo esc_attr($tabContentID)?>" class="uc-tab-content <?php echo esc_attr($addClass)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHTML)?> >
									
									<?php 
									
										$this->putParamFields($paramType);
										
									?>
									
								</div>
								
								<?php 								
								
							endforeach;
							?>
							
							
						</div>
						
						<div class="unite-clear"></div>
					
					</div>	<!-- end uc-tabs-content-wrapper -->
					
					<div class="uc-dialog-param-error unite-color-red" style="display:none"></div>
					
				</div>
				
					
			</div>		
		
		
		<?php 
	}
	
	
	private function a______INIT______(){}
	
	
	/**
	 * init main dialog params
	 */
	public function initMainParams(){
		
		$this->arrParams = array(
			self::PARAM_TEXTFIELD,
			self::PARAM_NUMBER,
			self::PARAM_RADIOBOOLEAN,
			self::PARAM_TEXTAREA,
			self::PARAM_CHECKBOX,
			self::PARAM_DROPDOWN,
			self::PARAM_MULTIPLE_SELECT,
			self::PARAM_SLIDER,			
			self::PARAM_COLORPICKER,
			self::PARAM_LINK,
			self::PARAM_EDITOR,
			self::PARAM_HR,
			self::PARAM_HEADING,
			self::PARAM_IMAGE,
			self::PARAM_AUDIO,
			self::PARAM_ICON,
			self::PARAM_ICON_LIBRARY,
			//self::PARAM_SHAPE,
			//self::PARAM_FONT_OVERRIDE,
			self::PARAM_INSTAGRAM,
		);
		
		
		//add dataset
		$arrDatasets = $this->objDatasets->getDatasetTypeNames();
		if(!empty($arrDatasets))
			$this->arrParams[] = self::PARAM_DATASET;
		
	}
	
	/**
	 * init item params inside repeater
	 */
	public function initItemParams(){
		
		$this->arrParamsItems = array(
			self::PARAM_TEXTFIELD,
			self::PARAM_NUMBER,
			self::PARAM_RADIOBOOLEAN,
			self::PARAM_TEXTAREA,
			self::PARAM_CHECKBOX,
			self::PARAM_DROPDOWN,
			self::PARAM_MULTIPLE_SELECT,
			self::PARAM_COLORPICKER,
			self::PARAM_SLIDER,
			self::PARAM_TEMPLATE,
			self::PARAM_LINK,
			self::PARAM_EDITOR,
			self::PARAM_HR,
			self::PARAM_HEADING,
			self::PARAM_IMAGE,
			self::PARAM_AUDIO,
			self::PARAM_ICON,
			self::PARAM_ICON_LIBRARY,
			self::PARAM_MARGINS,
			self::PARAM_PADDING
		);
		
		
		$this->arrParamsItems = UniteFunctionsUC::arrayToAssoc($this->arrParamsItems);
	}
	
	/**
	 * init common variable dialogs
	 */
	private function initVariableCommon(){
		
		$this->option_putAdminLabel = false;
		$this->option_putTitle = false;
		$this->option_arrTexts["add_title"] = esc_html__("Add Variable","unlimited-elements-for-elementor");
		$this->option_arrTexts["add_button"] = esc_html__("Add Variable","unlimited-elements-for-elementor");
		$this->option_arrTexts["update_button"] = esc_html__("Update Variable","unlimited-elements-for-elementor");
		$this->option_arrTexts["edit_title"] = esc_html__("Edit Variable","unlimited-elements-for-elementor");
		
	}
		
	
	/**
	 * init variable params
	 */
	private function initVariableMainParams(){
	
		$this->initVariableCommon();
		
		$this->arrParams = array(
				"uc_varitem_simple",
				"uc_var_paramrelated",
				self::PARAM_VAR_GET,
				self::PARAM_VAR_FILTER
		);
	
	}
	
	
	/**
	 * init variable item params
	 */
	private function initVariableItemParams(){
	
		$this->initVariableCommon();
		
		$this->arrParams = array(
				"uc_varitem_simple",
				"uc_var_paramrelated",
				"uc_var_paramitemrelated"
		);
		
	}
	
	
	/**
	 * init form item params
	 */
	private function initFormItemParams(){
		
		$objForm = new UniteCreatorForm();
		$this->arrParams = $objForm->getDialogFormParams();
		
		$this->option_putDecsription = false;
		$this->option_allowFontEditCheckbox = false;
	}
	
	/**
	 * init by addon type
	 * function for override
	 */
	protected function initByAddonType($addonType){
	}
	
	
	/**
	 * sort main params
	 */
	private function sortMainParams(){
		
		
		$arrParams = array();
		
		foreach($this->arrParams as $type){
			$text = UniteFunctionsUC::getVal($this->arrParamsTypes, $type);
			$arrParams[$type] = $text;
		}
		
		asort($arrParams);
		
		$this->arrParams = array_keys($arrParams);
		
	}
	
	
	/**
	 * init the params dialog
	 */
	public function init($type, $addon){
		
		$this->type = $type;
				
		if(empty($addon))
			UniteFunctionsUC::throwError("you must pass addon");
		
		$this->addon = $addon;
		$this->addonType = $addon->getType();
		
		$this->initByAddonType($this->addonType);
		
		$this->objSettings = new UniteCreatorSettings();
		$this->objDatasets = new UniteCreatorDataset();
				
		switch($this->type){
			case self::TYPE_MAIN:
				
				$this->initMainParams();
				$this->initItemParams(); 
			break;
			case self::TYPE_ITEM_VARIABLE:
				$this->initVariableItemParams();
			break;
			case self::TYPE_MAIN_VARIABLE:
				$this->initVariableMainParams();
			break;
			case self::TYPE_FORM_ITEM:
				$this->initFormItemParams();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong param dialog type: $type");
			break;
		}
		
    	$isSortParams = HelperProviderCoreUC_EL::getGeneralSetting("alphabetic_attributes");
		$isSortParams = UniteFunctionsUC::strToBool($isSortParams);
    	
		if($isSortParams == true)
			$this->sortMainParams();
		
	}
	
	
	
}
