<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

	class UniteSettingsOutputUCWork extends HtmlOutputBaseUC{
		
		protected static $arrIDs = array();
		
		protected $arrSettings = array(); 
		protected $settings;
		protected $formID;
		
		protected static $serial = 0;
		
		protected $showDescAsTips = false;
		protected $wrapperID = "";
		protected $addCss = "";
		protected $settingsMainClass = "";
		protected $isParent = false;		//variable that this class is parent
		protected $isSidebar = false;
		
		const INPUT_CLASS_NORMAL = "unite-input-regular";
		const INPUT_CLASS_NUMBER = "unite-input-number";
		const INPUT_CLASS_ALIAS = "unite-input-alias";
		const INPUT_CLASS_LONG = "unite-input-long";
		const INPUT_CLASS_SMALL = "unite-input-small";
		
		//saps related variables
		
		protected $showSaps = false;
		protected $sapsType = null;
		protected $activeSap = 0;		
		
		const SAPS_TYPE_INLINE = "saps_type_inline";	//inline sapts type
		const SAPS_TYPE_CUSTOM = "saps_type_custom";	//custom saps tyle
	    const SAPS_TYPE_ACCORDION = "saps_type_accordion";
		
	    
		/**
		 * 
		 * init the output settings
		 */
		public function init(UniteSettingsUC $settings){
			
			if($this->isParent == false)
				UniteFunctionsUC::throwError("The output class must be parent of some other class.");
			
			$this->settings = new UniteSettingsUC();
			$this->settings = $settings;
		}
		
		
		/**
		 * validate that the output class is inited with settings
		 */
		protected function validateInited(){
			if(empty($this->settings))
				UniteFunctionsUC::throwError("The output class not inited. Please call init() function with some settings class");
		}
		
		
		/**
		 * set add css. work with placeholder
		 * [wrapperid]
		 */
		public function setAddCss($css){
		
			$replace = "#".$this->wrapperID;
			$this->addCss = str_replace("[wrapperid]", $replace, $css);
		}
		
		/**
		 *
		 * set show descriptions as tips true / false
		 */
		public function setShowDescAsTips($show){
			$this->showDescAsTips = $show;
		}
		
		
		/**
		 *
		 * show saps true / false
		 */
		public function setShowSaps($show = true, $type = null){
		        
			if($type === null)
				$type = self::SAPS_TYPE_INLINE;
			
			$this->showSaps = $show;
						
			
			switch($type){
				case self::SAPS_TYPE_CUSTOM:
				case self::SAPS_TYPE_INLINE:
				case self::SAPS_TYPE_ACCORDION:
				break;
				default:
					UniteFunctionsUC::throwError("Wrong saps type: $type ");
				break;
			}
			
			$this->sapsType = $type;
			
		}
		
		
		/**
		 * get default value add html
		 * @param $setting
		 */
		protected function getDefaultAddHtml($setting, $implodeArray = false){
			
			$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
			if(is_array($defaultValue))
				$defaultValue = json_encode($defaultValue);
			
			$defaultValue = htmlspecialchars($defaultValue);
			
			//UniteFunctionsUC::showTrace();exit();
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			if(is_array($value) || is_object($value)){
				if($implodeArray == false)
					return("");
				else
					$value = implode(",", $value);
			}
						
			$value = htmlspecialchars($value);
			
			$addHtml = " data-default=\"{$defaultValue}\" data-initval=\"{$value}\" ";
			
			$addParams = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDPARAMS);
			if(!empty($addParams))
				$addHtml .= " ".$addParams;
			
			return($addHtml);
		}
		
		
		/**
		 * prepare draw setting text
		 */
		protected function drawSettingRow_getText($setting){
		
			//modify text:
			$text = UniteFunctionsUC::getVal($setting, "text", "");
			
			if(empty($text))
				return("");
				
			// prevent line break (convert spaces to nbsp)
			$text = str_replace(" ","&nbsp;",$text);
		
			switch($setting["type"]){
				case UniteSettingsUC::TYPE_CHECKBOX:
					$text = "<label for='".$setting["id"]."' style='cursor:pointer;'>$text</label>";
					break;
			}
		
			return($text);
		}
		
		
		/**
		 *
		 * get text style
		 */
		protected function drawSettingRow_getTextStyle($setting){
		
			//set text style:
			$textStyle = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_TEXTSTYLE);
		
			if($textStyle != "")
				$textStyle = "style='".$textStyle."'";
		
			return($textStyle);
		}
		
		
		/**
		 * get row style
		 */
		protected function drawSettingRow_getRowHiddenClass($setting){
			
			//set hidden			
			$isHidden = isset($setting["hidden"]);
			
			if($isHidden == true && $setting["hidden"] === "false")
				$isHidden = false;
			
			//operate saps
			if($this->showSaps == true && $this->sapsType == self::SAPS_TYPE_INLINE){
				
				$sap = UniteFunctionsUC::getVal($setting, "sap");
				$sap = (int)$sap;
				
				if($sap != $this->activeSap)
					$isHidden = true;
			}

			$class = "";
			if($isHidden == true)
				$class = "unite-setting-hidden";
			
			return($class);
		}
		
		
		/**
		 *
		 * get row class
		 */
		protected function drawSettingRow_getRowClass($setting, $basClass = ""){
						
			//set text class:
			$class = $basClass;
			
			if(isset($setting["disabled"])){
				if(!empty($class))
					$class .= " ";
				
				$class .= "setting-disabled";
			}
			
			//add saps class
			if($this->showSaps && $this->sapsType == self::SAPS_TYPE_INLINE){
				
				$sap = UniteFunctionsUC::getVal($setting, "sap");
				$sap = (int)$sap;
				$sapClass = "unite-sap-element unite-sap-".$sap;
				
				if(!empty($class))
					$class .= " ";
				
				$class .= $sapClass;
			}
			
			$showin = UniteFunctionsUC::getVal($setting, "showin");
			if(!empty($showin)){
				if(!empty($class))
					$class .= " ";
				
				$class .= "uc-showin-{$showin}";
			}
				
			$classHidden = $this->drawSettingRow_getRowHiddenClass($setting);
			if(!empty($classHidden)){
				
				if(!empty($class))
					$class .= " ";
				
				$class .= $classHidden;
			}
			
			if(!empty($class))
				$class = "class='{$class}'";
			
				
			return($class);
		}
		
		
		
		
		/**
		* draw after body additional settings accesories
		*/
		public function drawAfterBody(){
			$arrTypes = $this->settings->getArrTypes();
			foreach($arrTypes as $type){
				switch($type){
					case self::TYPE_COLOR:
						?>
							<div id='divPickerWrapper' style='position:absolute;display:none;'><div id='divColorPicker'></div></div>
						<?php
					break;
				}
			}
		}
				
		
		/**
		 * 
		 * do some operation before drawing the settings.
		 */
		protected function prepareToDraw(){
			
			$this->settings->setSettingsStateByControls();
			$this->settings->setPairedSettings();
		}


		/**
		 * get setting class attribute
		 */
		protected function getInputClassAttr($setting, $defaultClass="", $addClassParam="", $wrapClass = true){
						
			$class = UniteFunctionsUC::getVal($setting, "class", $defaultClass);
			$classAdd = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_CLASSADD);
			
			switch($class){
				case "alias":
					$class = self::INPUT_CLASS_ALIAS;
				break;
				case "long":
					$class = self::INPUT_CLASS_LONG;
				break;
				case "normal":
					$class = self::INPUT_CLASS_NORMAL;
				break;
				case "number":
					$class = self::INPUT_CLASS_NUMBER;
				break;
				case "small":
					$class = self::INPUT_CLASS_SMALL;
				break;
				case "nothing":
					$class = "";
				break;
			}
			
			if(!empty($classAdd)){
				if(!empty($class))
					$class .= " ";
				$class .= $classAdd;
			}
			
			if(!empty($addClassParam)){
				if(!empty($class))
					$class .= " ";
				$class .= $addClassParam;
			}
			
			$isTransparent = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_MODE_TRANSPARENT);
			if(!empty($isTransparent)){
				if(!empty($class))
					$class .= " ";
				$class .= "unite-setting-transparent";
			}
			
			if(!empty($class) && $wrapClass == true)
				$class = "class='$class'";
			
			return($class);
		}
		
		
		
		
		/**
		 * modify image setting values
		 */
		protected function modifyImageSetting($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			$value = trim($value);
			
			$urlBase = UniteFunctionsUC::getVal($setting, "url_base", null);
			
			if(!empty($value) && is_numeric($value) == false)
				$value = HelperUC::URLtoFull($value, $urlBase);
			
			$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
			$defaultValue = trim($defaultValue);
			
			if(!empty($defaultValue) && is_numeric($defaultValue) == false)
				$defaultValue = HelperUC::URLtoFull($defaultValue, $urlBase);
			
			$setting["value"] = $value;
			$setting["default_value"] = $defaultValue;
			
			
			return($setting);
		}
	
		
		/**
		 * 
		 * draw imaeg input:
		 * @param $setting
		 */
		protected function drawImageInput($setting){
			
			$previewStyle = "display:none";
			
			$setting = $this->modifyImageSetting($setting);
						
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			$imageID = null;
			$urlImage = $value;
			$urlThumb = $value;
			
			if(!empty($value) && is_numeric($value)){
				$imageID = $value;
				$urlImage = UniteProviderFunctionsUC::getImageUrlFromImageID($imageID);
				$urlThumb = UniteProviderFunctionsUC::getThumbUrlFromImageID($imageID);
				
				$urlImage = HelperUC::URLtoFull($urlImage);
				$urlThumb = HelperUC::URLtoFull($urlThumb);
				
				$setting["value"] = $urlImage;		//for initval
			}
			
			//try create thumb image
			if(empty($urlThumb) && !empty($urlImage)){
				
					try{
						$operations = new UCOperations();
						$urlThumb = $operations->getThumbURLFromImageUrl($value);
						$urlThumb = HelperUC::URLtoFull($urlThumb);
						
					}catch(Exception $e){
						$urlThumb = $urlImage;
					}
								
			}
			
			//get url preview
			$urlPreview = "";
			if(!empty($urlThumb))
				$urlPreview = $urlThumb;
			
			//get preview style
			if(empty($urlPreview) && !empty($urlImage))
				$urlPreview = $urlImage;
			
			$previewStyle = "";
			
			if(!empty($urlPreview))
				$previewStyle .= "background-image:url('{$urlPreview}');";
			
			$clearStyle = "style='display:none'";
			if(!empty($previewStyle)){
				$previewStyle = "style=\"{$previewStyle}\"";
				$clearStyle = "";
			}
			
			$class = $this->getInputClassAttr($setting, "", "unite-setting-image-input unite-input-image");
			
			$addHtml = $this->getDefaultAddHtml($setting);
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
			
			if(!empty($imageID))
					$addHtml .= " data-imageid='{$imageID}'";
			
			
			$textPlaceholder = __("Image Url");
			
			$addClass = "";
			if(!empty($urlImage)){
				$addClass = "unite-image-exists";
			}
			
			 
			?>
				<div class="unite-setting-image <?php echo esc_attr($addClass)?>"> 
					
					<div class='unite-setting-image-preview' <?php echo UniteProviderFunctionsUC::escAddParam($previewStyle)?>>
						
						<div class="unite-no-image">
					        <i class="fa fa-plus-circle"></i>
					        <br>
					        <?php esc_html_e("Select Image", "unlimited-elements-for-elementor")?>
					     </div>
					     
					    <div class="unite-image-actions">
					      <span class="unite-button-clear"><?php esc_html_e("Clear", "unlimited-elements-for-elementor")?></span>
					      <span class="unite-button-choose"><?php esc_html_e("Change", "unlimited-elements-for-elementor")?></span>
					    </div>
      					
					</div>
				
					<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>"  <?php echo UniteProviderFunctionsUC::escAddParam($class)?> value="<?php echo esc_attr($urlImage)?>" placeholder="<?php echo esc_attr($textPlaceholder)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
															
				</div>
			<?php
		}

		
		/**
		 *
		 * draw image input:
		 * @param $setting
		 */
		protected function drawMp3Input($setting){
			
			$previewStyle = "display:none";
		
			$setting = $this->modifyImageSetting($setting);
			
			$value = UniteFunctionsUC::getVal($setting, "value");
		
			$class = $this->getInputClassAttr($setting, "", "unite-setting-mp3-input unite-input-image");
			
			$addHtml = $this->getDefaultAddHtml($setting);
		
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
		
			?>
				<div class="unite-setting-mp3">
					<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
					<a href="javascript:void(0)" class="unite-button-secondary unite-button-choose"><?php esc_html_e("Choose", "unlimited-elements-for-elementor")?></a>
				</div>
			<?php
		}
		
		/**
		 *
		 * draw icon picker input:
		 * @param $setting
		 */
		protected function drawIconPickerInput($setting){
			 
			$previewStyle = "display:none";
			$iconsType = UniteFunctionsUC::getVal($setting, "icons_type");
				
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			if(empty($iconsType) || $iconsType == "fa"){
				$setting["value"] = UniteFontManagerUC::fa_convertIcon($value);
				$value = $setting["value"];
			}
			
			$class = $this->getInputClassAttr($setting, "", "unite-iconpicker-input");
			$addHtml = $this->getDefaultAddHtml($setting);
			
			$addClassWrapper = "";
			if($iconsType){
				$addHtml .= " data-icons_type='$iconsType'";
				$addClassWrapper = " unite-icon-type-".$iconsType;
			}
			
			?>
		      <div class="unite-settings-iconpicker<?php echo esc_attr($addClassWrapper)?>">
					<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
		        	<span class="unite-iconpicker-button"></span>
		        	<div class="unite-iconpicker-title"></div>
			  </div>
			<?php
		}
		
		
		/**
		 * draw addon picker input
		 */
		protected function drawAddonPickerInput($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			$value = trim($value);
			
			$addonType = UniteFunctionsUC::getVal($setting, "addontype");
			$objAddonType = UniteCreatorAddonType::getAddonTypeObject($addonType);
			if(empty($addonType))
				$addonType = GlobalsUC::ADDON_TYPE_REGULAR_ADDON;
			
			$isSVG = $objAddonType->isSVG;
			
			$addClass = "";
			if($isSVG == true)
				$addClass = " unite-addonpicker-icon-svg";
			
			$addClass .= " uc-addon-type-".$addonType;
				
			$styleButton = "";
			$title = "";
			
			//get all the addon data
			if(!empty($value)){
				try{
					
					$objAddon = new UniteCreatorAddon();
					$objAddon->initByMixed($value, $addonType);
					
					$urlPreview = $objAddon->getUrlPreview();
					if($urlPreview)
						$styleButton = "background-image:url('{$urlPreview}')";

					$title = $objAddon->getTitle(true);
					
				}catch(Exception $e){
					$value = "";
				}
				
			}

			if(!empty($styleButton))
				$styleButton = "style=\"{$styleButton}\"";
			
			if(empty($value))
				$addClass .= " unite-empty-content";
			
			$addHtml = $this->getDefaultAddHtml($setting);
			$addHtml .= " data-addontype=\"{$addonType}\" style='display:none'";
			
			$textSelect = __("Select ").$objAddonType->textSingle;
			
			$showTitle = true;
			if(isset($setting["noaddontitle"]))
				$showTitle = false;

			//add data holder
			$addDataHolder = UniteFunctionsUC::getVal($setting, "add_data_holder");
			$addDataHolder = UniteFunctionsUC::strToBool($addDataHolder);
			
			$addClearButton = UniteFunctionsUC::getVal($setting, "add_clear_button");
			$addClearButton = UniteFunctionsUC::strToBool($addClearButton);
			
			$addConfigureButton = UniteFunctionsUC::getVal($setting, "add_configure_button");
			$addConfigureButton = UniteFunctionsUC::strToBool($addConfigureButton);
			
			$configureButtonAction = UniteFunctionsUC::getVal($setting, "configure_button_action");
			
			
			?>
		      <div class="unite-settings-addonpicker-wrapper <?php echo esc_attr($addClass)?>">
					<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" class="unite-setting-addonpicker" value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
		        	
					<?php if($addDataHolder == true):?>
					<input type="hidden" id="<?php echo esc_attr($setting["id"])?>_data" name="<?php echo esc_attr($setting["name"])?>_data" value="" />
		        	<?php endif?>
		        	
		        	<div class="unite-addonpicker-button" <?php echo UniteProviderFunctionsUC::escAddParam($styleButton)?>>
		        		<div class="unite-addonpicker-empty-container">
		        			<?php echo esc_html($textSelect)?>
		        		</div>
		        	</div>
		        	
		        	<?php if($showTitle == true):?>
		        	<div class="unite-addonpicker-title"><?php echo esc_html($title)?></div>
			  		<?php endif?>
			  		
			  		<?php if($addClearButton == true):?>
			  		<a href="javascript:void(0)" class="unite-button-secondary uc-action-button" data-action="clear" ><?php esc_html_e("Clear", "unlimited-elements-for-elementor")?></a>
			  		<?php endif?>
			  		
			  		<?php if($addConfigureButton == true):?>
			  		<a href="javascript:void(0)" class="unite-button-secondary uc-action-button" data-action="configure" data-configureaction="<?php echo esc_attr($configureButtonAction)?>" ><?php esc_html_e("Configure", "unlimited-elements-for-elementor")?></a>
			  		<?php endif?>
			  		
			  </div>
			<?php
			
		}
		
		
		/**
		 * special inputs
		 */
		private function a______SPECIAL_INPUTS_____(){}
		
		
		/**
		 * draw galler setting
		 */
		protected function drawGallerySetting($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			?>
		      <div id="<?php echo esc_attr($setting["id"])?>" data-settingtype="gallery" class="unite-settings-gallery unite-setting-input-object" data-name="<?php echo esc_attr($setting["name"])?>" >
		      		<b>
		      			Gallery Picker Goes Here
		      		</b>
			  </div>
			<?php
						
		}
		
		
		/**
		 * draw icon picker input:
		 * @param $setting
		 */
		protected function drawMapPickerInput($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
						
			$dialogTitle = esc_html__("Select Map","unlimited-elements-for-elementor");
			
			$filepathPickerObject = GlobalsUC::$pathViewsObjects."mappicker_view.class.php";
			require_once $filepathPickerObject;
			
			$objPicker = new UniteCreatorMappickerView();
			$objPicker->setData($value);
			
			$strMapData = UniteFunctionsUC::jsonEncodeForHtmlData($value, "mapdata");
			
			?>
		      <div id="<?php echo esc_attr($setting["id"])?>" data-settingtype="map" <?php echo UniteProviderFunctionsUC::escAddParam($strMapData)?> class="unite-settings-mappicker unite-setting-input-object" data-name="<?php echo esc_attr($setting["name"])?>" data-dialogtitle="<?php echo esc_attr($dialogTitle)?>" >
		      	 <?php $objPicker->putPickerInputHtml()?>
			  </div>
			<?php
		}
		
		
		/**
		 * draw icon picker input:
		 * @param $setting
		 */
		protected function drawPostPickerInput($setting){
			dmp("drawPostPickerInput: function for override");
			exit();
		}
		
				
		/**
		 * draw module picker input:
		 * @param $setting
		 */
		protected function drawModulePickerInput($setting){
			dmp("drawModulePickerInput: function for override");
			exit();
		}
		
		
		/**
		 * draw color picker
		 * @param $setting
		 */
		protected function drawColorPickerInput($setting){	
			
			$disabled = "";
			if(isset($setting["disabled"])){
				$color = "";
				$disabled = 'disabled="disabled"';
			}
			
			$pickerType = HelperUC::getGeneralSetting("color_picker_type");
			
			$bgcolor = $setting["value"];
			$bgcolor = str_replace("0x","#",$bgcolor);			
			
			$style = "";
			if($pickerType == "farbtastic"){
				
				
				// set the forent color (by black and white value)
				$rgb = UniteFunctionsUC::html2rgb($bgcolor);
				$bw = UniteFunctionsUC::yiq($rgb[0],$rgb[1],$rgb[2]);
				
				$color = "#000000";
				if($bw<128) 
					$color = "#ffffff";
				
				$style="style='background-color:$bgcolor;color:$color'";
			}
			
			$addHtml = $this->getDefaultAddHtml($setting);
			
			$class = $this->getInputClassAttr($setting, "", "unite-color-picker");
			
			?>
				<div class="unite-color-picker-wrapper">
				
					<input type="text" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> id="<?php echo esc_attr($setting["id"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($style)?> name="<?php echo esc_attr($setting["name"])?>" value="<?php echo esc_attr($bgcolor)?>" <?php echo UniteProviderFunctionsUC::escAddParam($disabled)?> <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?>></input>
				
				</div>
			<?php
		}
		
		
		/**
		 * draw the editor by provider
		 */
		protected function drawEditorInput($setting){
			
			dmp("provider settings output - function to override");
			exit();
		}
		
		/**
		 * draw fonts panel - function for override
		 */
		protected function drawFontsPanel($setting){
			
			dmp("draw fonts panel - function for override");
			exit();
		}
		
		/**
		 * draw fonts panel - function for override
		 */
		protected function drawItemsPanel($setting){
			
			dmp("draw items panel - function for override");
			exit();
		}
		
		
		/**
		 * draw setting input by type
		 */
		protected function drawInputs($setting){
			
			switch($setting["type"]){
				case UniteSettingsUC::TYPE_TEXT:
					$this->drawTextInput($setting);
				break;
				case UniteSettingsUC::TYPE_COLOR:
					$this->drawColorPickerInput($setting);
				break;
				case UniteSettingsUC::TYPE_SELECT:
					$this->drawSelectInput($setting);
				break;
				case UniteSettingsUC::TYPE_MULTISELECT:
					$this->drawMultiSelectInput($setting);
				break;
				case UniteSettingsUC::TYPE_CHECKBOX:
					$this->drawCheckboxInput($setting);
				break;
				case UniteSettingsUC::TYPE_RADIO:
					$this->drawRadioInput($setting);
				break;
				case UniteSettingsUC::TYPE_TEXTAREA:
					$this->drawTextAreaInput($setting);
				break;
				case UniteSettingsUC::TYPE_IMAGE:
					$this->drawImageInput($setting);
				break;
				case UniteSettingsUC::TYPE_MP3:
					$this->drawMp3Input($setting);
				break;
				case UniteSettingsUC::TYPE_ICON:
					$this->drawIconPickerInput($setting);
				break;
				case UniteSettingsUC::TYPE_ADDON:
					$this->drawAddonPickerInput($setting);
				break;
				case UniteSettingsUC::TYPE_MAP:
					$this->drawMapPickerInput($setting);
				break;
				case UniteSettingsUC::TYPE_POST:
					$this->drawPostPickerInput($setting);
				break;
				case UniteSettingsUC::TYPE_EDITOR:
					$this->drawEditorInput($setting);
				break;
				case UniteCreatorSettings::TYPE_FONT_PANEL:
					$this->drawFontsPanel($setting);
				break;
				case UniteCreatorSettings::TYPE_ITEMS:
					$this->drawItemsPanel($setting);
				break;
				case UniteCreatorSettings::TYPE_BUTTON:
					$this->drawButtonInput($setting);
				break;
				case UniteCreatorSettings::TYPE_RANGE:
					$this->drawRangeSliderInput($setting);
				break;
				case UniteCreatorSettings::TYPE_HIDDEN:
					$this->drawHiddenInput($setting);
				break;
				case UniteCreatorSettings::TYPE_REPEATER:
					
					$this->drawRepeaterInput($setting);
					
				break;
				case UniteCreatorSettings::TYPE_TYPOGRAPHY:
					
					$this->drawTypographySetting($setting);
					
				break;
				case UniteCreatorSettings::TYPE_DIMENTIONS:
					
					$this->drawDimentionsSetting($setting);
					
				break;
				case UniteCreatorSettings::TYPE_GALLERY:
					
					$this->drawGallerySetting($setting);
					
				break;
				case UniteSettingsUC::TYPE_CUSTOM:
					if(method_exists($this,"drawCustomInputs") == false){
						UniteFunctionsUC::throwError("Method don't exists: drawCustomInputs, please override the class");
					}
					$this->drawCustomInputs($setting);
				break;
				default:
					throw new Exception("drawInputs error: wrong setting type - ".$setting["type"]);
				break;
			}
			
		}		
		
		
		/**
		 * draw text input
		 * @param $setting
		 */
		protected function drawRangeSliderInput($setting) {
			
			
			$setting[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-range";
			$setting["class"] = "nothing";
			$setting["type_number"] = true;
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			$min = UniteFunctionsUC::getVal($setting, "min");
			$max = UniteFunctionsUC::getVal($setting, "max");
			$step = UniteFunctionsUC::getVal($setting, "step");
			
			if(empty($step))
				$step = 1;
			
			if($min === "" || is_numeric($min) == false)
				UniteFunctionsUC::throwError("range error: should be min value");
			
			if($max === "" || is_numeric($max) == false)
				UniteFunctionsUC::throwError("range error: should be max value");
			
			$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
			
			$unit = UniteFunctionsUC::getVal($setting, "range_unit");
			
			if($unit == "__hide__")
				$unit = null;
			
			?>
			<div class="unite-setting-range-wrapper">
				
				<input type="range" min="<?php echo esc_attr($min)?>" max="<?php echo esc_attr($max)?>" step="<?php echo esc_attr($step)?>" value="<?php echo esc_attr($value)?>" >
			<?php 
					
				$this->drawTextInput($setting);
				
				if(!empty($unit)):
				?>
				<span class="setting_unit"><?php echo esc_html($unit)?></span>
				<?php 
				endif;
			?>
				
			</div>
			<?php
		}
		
		
		/**
		 * draw repeater input
		 */
		protected function drawRepeaterInput($setting){
			
			$itemsValues = UniteFunctionsUC::getVal($setting, "items_values");
						
			$strData = UniteFunctionsUC::jsonEncodeForHtmlData($itemsValues, "itemvalues");
			
			$addItemText = UniteFunctionsUC::getVal($setting, "add_button_text");
			if(empty($addItemText))
				$addItemText = esc_html__("Add Item", "unlimited-elements-for-elementor");
			
			//get empty text
			$emptyText = UniteFunctionsUC::getVal($setting, "empty_text");
			
			if(empty($emptyText))
				$emptyText = esc_html__("No Items Found", "unlimited-elements-for-elementor");
			
			$objSettingsItems = UniteFunctionsUC::getVal($setting, "settings_items");
			UniteFunctionsUC::validateNotEmpty($objSettingsItems, "settings items");
			
			$emptyTextAddHtml = "";
			if(!empty($value))
				$emptyTextAddHtml = "style='display:none'";
			
			if($this->isSidebar == true){
				$output = new UniteSettingsOutputSidebarUC();
				$output->setShowSaps(false);
			}
			else
				$output = new UniteSettingsOutputWideUC();
			
			
			$output->init($objSettingsItems);
			
			//get item title
			$itemTitle = UniteFunctionsUC::getVal($setting, "item_title");
			if(empty($itemTitle))
				$itemTitle = esc_html__("Item", "unlimited-elements-for-elementor");
				
			$itemTitle = htmlspecialchars($itemTitle);
			
			//delete button text
			$deleteButtonText = UniteFunctionsUC::getVal($setting, "delete_button_text");
			if(empty($deleteButtonText))
				$deleteButtonText = esc_html__("Delete Item","unlimited-elements-for-elementor");
			
			$duplicateButtonText = UniteFunctionsUC::getVal($setting, "duplicate_button_text");
			if(empty($duplicateButtonText))
				$duplicateButtonText = esc_html__("Duplicate Item","unlimited-elements-for-elementor");
			
			$deleteButtonText = htmlspecialchars($deleteButtonText);
			$duplicateButtonText = htmlspecialchars($duplicateButtonText);
			
			
			?>
		      <div id="<?php echo esc_attr($setting["id"])?>" data-settingtype="repeater" <?php echo UniteProviderFunctionsUC::escAddParam($strData)?> class="unite-settings-repeater unite-setting-input-object" data-name="<?php echo esc_attr($setting["name"])?>" data-itemtitle='<?php echo esc_attr($itemTitle)?>' data-deletetext="<?php echo esc_attr($deleteButtonText)?>" data-duplicatext="<?php echo esc_attr($duplicateButtonText)?>" >
		      	 
		      	 <div class="unite-repeater-emptytext" <?php echo UniteProviderFunctionsUC::escAddParam($emptyTextAddHtml)?>>
		      	 	<?php echo esc_html($emptyText)?>
		      	 </div>
		      	 
		      	 <div class="unite-repeater-template" style="display:none">
		      	 	
		      	 		<?php $output->draw("settings_item_repeater", false); ?>
		      	 		
		      	 </div>
		      	 
		      	 <div class="unite-repeater-items"></div>
		      	 
		      	 <a class="unite-button-secondary unite-repeater-buttonadd" ><?php echo UniteProviderFunctionsUC::escAddParam($addItemText)?></a>
		      	 
			  </div>
			  			  
			<?php
			
		}
		
		
		/**
		 * special inputs
		 */
		private function a______REGULAR_INPUTS______(){}
		
		
		/**
		 * draw text input
		 * @param $setting
		 */
		protected function drawTextInput($setting) {
						
			$disabled = "";
			$style="";
			$readonly = "";
			
			if(isset($setting["style"])) 
				$style = "style='".$setting["style"]."'";
			if(isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';
				
			if(isset($setting["readonly"])){
				$readonly = "readonly='readonly'";
			}
			
			$defaultClass = self::INPUT_CLASS_NORMAL;
			
			$typeNumber = UniteFunctionsUC::getVal($setting, "type_number");
			$typeNumber = UniteFunctionsUC::strToBool($typeNumber);
			
			$unit = UniteFunctionsUC::getVal($setting, "unit");
			if(!empty($unit)){
				$defaultClass = self::INPUT_CLASS_NUMBER;
				if($unit == "px")
					$typeNumber = true;
			}
			
			$class = $this->getInputClassAttr($setting, $defaultClass);
			
			$addHtml = $this->getDefaultAddHtml($setting);
						
			$placeholder = UniteFunctionsUC::getVal($setting, "placeholder", null);
			
			if($placeholder !== null){
				$placeholder = htmlspecialchars($placeholder);
				$addHtml .= " placeholder=\"$placeholder\"";
			}
			
			$value = $setting["value"];
			$value = htmlspecialchars($value);
						
			$typePass = UniteFunctionsUC::getVal($setting, "ispassword");
			$typePass = UniteFunctionsUC::strToBool($typePass);
			
			//set input type
			
			$inputType = "text";
			if($typeNumber == true){
				$inputType = "number";
				$step = UniteFunctionsUC::getVal($setting, "step");
				if(!empty($step) && is_numeric($step))
					$addHtml .= " step=\"{$step}\"";
			}
			
			if($typePass === true){
				$inputType = "password";
			}
			
			?>
				<input type="<?php echo esc_attr($inputType)?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> <?php echo UniteProviderFunctionsUC::escAddParam($style)?> <?php echo UniteProviderFunctionsUC::escAddParam($disabled)?><?php echo UniteProviderFunctionsUC::escAddParam($readonly)?> id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
			<?php
		}
		
		
		/**
		 * draw hidden input
		 */
		protected function drawHiddenInput($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			$value = htmlspecialchars($value);
			$addHtml = $this->getDefaultAddHtml($setting);
			
			?>
				<input type="hidden" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
			<?php 
		}
		
		
		
		/**
		 * draw button input
		 */
		protected function drawButtonInput($setting){
			
			$name = $setting["name"];
			$id = $setting["id"];
			$value = $setting["value"];
			$href = "javascript:void(0)";
			$gotoView = UniteFunctionsUC::getVal($setting, "gotoview");
			
			if(!empty($gotoView))
				$href = HelperUC::getViewUrl($gotoView);
			
			?>
			<a id="<?php echo esc_attr($id)?>" href="<?php echo esc_attr($href)?>" name="<?php echo esc_attr($name)?>" class="unite-button-secondary"><?php echo esc_html($value)?></a>
			<?php 
			
		}
		
		
		/**
		 * draw text area input
		 */
		protected function drawTextAreaInput($setting){
			
			$disabled = "";
			if (isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';
			
			$style = "";
			if(isset($setting["style"]))
				$style = "style='".$setting["style"]."'";
			
			$rows = UniteFunctionsUC::getVal($setting, "rows");
			if(!empty($rows))
				$rows = "rows='$rows'";
			
			$cols = UniteFunctionsUC::getVal($setting, "cols");
			if(!empty($cols))
				$cols = "cols='$cols'";
			
			$addHtml = $this->getDefaultAddHtml($setting);
			
			$class = $this->getInputClassAttr($setting);
			
			$value = $setting["value"];
			$value = htmlspecialchars($value);
			
			?>
				<textarea id="<?php echo esc_attr($setting["id"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($style)?> <?php echo UniteProviderFunctionsUC::escAddParam($disabled)?> <?php echo UniteProviderFunctionsUC::escAddParam($rows)?> <?php echo UniteProviderFunctionsUC::escAddParam($cols)?> <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> ><?php echo UniteProviderFunctionsUC::escAddParam($value)?></textarea>
			<?php
			if(!empty($cols))
				echo "<br>";	//break line on big textareas.
		}		
		
		
		/**
		 * draw radio input
		 */
		protected function drawRadioInput($setting){
			
			$items = $setting["items"];
			$counter = 0;
			$settingID = $setting["id"];
			$isDisabled = UniteFunctionsUC::getVal($setting, "disabled");
			$isDisabled = UniteFunctionsUC::strToBool($isDisabled);
			$settingName = $setting["name"];
			$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
			$settingValue = UniteFunctionsUC::getVal($setting, "value");
			
			$class = $this->getInputClassAttr($setting);
			
			$specialDesign = UniteFunctionsUC::getVal($setting, "special_design");
			$specialDesign = UniteFunctionsUC::strToBool($specialDesign);
			
			if($this->isSidebar == false)
				$specialDesign = false;
			
			$addClass = "";
			if($specialDesign == true){
				$addClass = " unite-radio-special";
				$numItems = count($items);
				switch($numItems){
					case 2:
						$addClass .= " split-two-columns";
					break;
					case 3:
						$addClass .= " split-three-columns";
					break;
					case 4:
						$addClass .= " split-four-columns";
					break;
					default:
						$addClass = "";
					break;
				}
				
				$designColor = UniteFunctionsUC::getVal($setting, "special_design_color");
				if(!empty($designColor))
					$addClass .= " unite-radio-color-$designColor";
			
			}
			
			?>
			<span id="<?php echo esc_attr($settingID) ?>" class="radio_wrapper<?php echo esc_attr($addClass)?>">
			
			<?php 
			
			foreach($items as $text=>$value):
				$counter++;
				$radioID = $settingID."_".$counter;
				
				$classLabel = "unite-radio-item-label-$counter";
				
				$strChecked = "";				
				if($value == $settingValue) 
					$strChecked = " checked";
				
				$strDisabled = "";
				if($isDisabled)
					$strDisabled = 'disabled = "disabled"';
				
				$addHtml = "";
				if($value == $defaultValue)
					$addHtml .= " data-defaultchecked=\"true\"";
				
				if($value == $settingValue){
					$addHtml .= " data-initchecked=\"true\"";
				}
				
				$props = "style=\"cursor:pointer;\" {$strChecked} {$strDisabled} {$addHtml} {$class}";
				
				?>					
					<input type="radio" id="<?php echo esc_attr($radioID)?>" value="<?php echo esc_attr($value)?>" name="<?php echo esc_attr($settingName)?>" <?php echo UniteProviderFunctionsUC::escAddParam($props)?>/>
					<label class="<?php echo esc_attr($classLabel)?>" for="<?php echo esc_attr($radioID)?>" ><?php echo UniteProviderFunctionsUC::escAddParam($text)?></label>
					
					<?php if($specialDesign == false):?>
					&nbsp; &nbsp;
					<?php endif?>
				<?php				
			endforeach;
			
			?>
			</span>
			<?php 
		}
		
		
		/**
		 * draw checkbox
		 */
		protected function drawCheckboxInput($setting){
			
			$checked = "";
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			$value = UniteFunctionsUC::strToBool($value);
			
			if($value == true) 
				$checked = 'checked="checked"';
			
				$textNear = UniteFunctionsUC::getVal($setting, "text_near");
			
			$settingID = $setting["id"];
			
			if(!empty($textNear)){
				$textNearAddHtml = "";
				if($this->showDescAsTips == true){
					$description = UniteFunctionsUC::getVal($setting, "description");
					$description = htmlspecialchars($description);
					$textNearAddHtml = " title='$description' class='uc-tip'";
				}
				
				$textNear = "<label for=\"{$settingID}\"{$textNearAddHtml}>$textNear</label>";
			}
			
			$defaultValue = UniteFunctionsUC::getVal($setting, "default_value");
			$defaultValue = UniteFunctionsUC::strToBool($defaultValue);
			
			$addHtml = "";
			if($defaultValue == true)
				$addHtml .= " data-defaultchecked=\"true\"";
			
			if($value)
				$addHtml .= " data-initchecked=\"true\"";
			
			$class = $this->getInputClassAttr($setting);
			
			?>
				<input type="checkbox" id="<?php echo esc_attr($settingID)?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($checked)?> <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?>/>
			<?php
			if(!empty($textNear))
				echo $textNear;
		}
		
		
		/**
		 * draw select input
		 */
		protected function drawSelectInput($setting){
						
			$type = UniteFunctionsUC::getVal($setting, "type");
			
			$name = UniteFunctionsUC::getVal($setting, "name");
		
			$isMultiple = false;
			if($type == "multiselect")
				$isMultiple = true;
			
			$disabled = "";
			if(isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';
			
			$args = UniteFunctionsUC::getVal($setting, "args");
			
			$settingValue = $setting["value"];
						
			if(is_array($settingValue) == false && strpos($settingValue,",") !== false)
				$settingValue = explode(",", $settingValue);
						
			$addHtml = $this->getDefaultAddHtml($setting, true);
			
			if($isMultiple == true){
				$addHtml .= " multiple";
			}
			
			//set this flag for true to enable select2
			
			$setSelect2 = false;
			if($isMultiple == true)
				$setSelect2 = true;
			
			$isPostSelect = UniteFunctionsUC::getVal($setting, "post_select");
			$isPostSelect = UniteFunctionsUC::strToBool($isPostSelect);
			
			if($isPostSelect == true){
				$setSelect2 = true;
				
				$postSelectType = UniteFunctionsUC::getVal($setting, "post_select_type");
				
				$addHtml .= " data-settingtype='post_select' data-postselecttype='$postSelectType'";
				
			}
			
			$defaultClass = "";
				
			if($setSelect2 == true){
			
					$defaultClass = "select2";
			}
				
			$class = $this->getInputClassAttr($setting, $defaultClass);
			
			$arrItems = UniteFunctionsUC::getVal($setting, "items",array());
			if(empty($arrItems))
				$arrItems = array();
			
				
			?>
			<select id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($disabled)?> <?php echo UniteProviderFunctionsUC::escAddParam($class)?> <?php echo UniteProviderFunctionsUC::escAddParam($args)?> <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?>>
			<?php
			foreach($arrItems as $text=>$value):
				
				//set selected
				$selected = "";
				$addition = "";
								
				if(is_array($settingValue)){
					if(array_search($value, $settingValue) !== false) 
						$selected = 'selected="selected"';
				}else{
					if($value == $settingValue) 
						$selected = 'selected="selected"';
				}
				
				?>
					<option <?php echo $addition?> value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($selected)?>><?php echo UniteProviderFunctionsUC::escAddParam($text)?></option>
				<?php
			endforeach
			?>
			</select>
			<?php
		}

		
		/**
		 * draw select input
		 */
		protected function drawMultiSelectInput($setting){
			
			$this->drawSelectInput($setting);
			
		}
		
		/**
		 * draw text row
		 * @param unknown_type $setting
		 */
		protected function drawTextRow($setting){
			echo "draw text row - override this function";
		}

		
		/**
		 * draw hr row - override
		 */
		protected function drawHrRow($setting){
			echo "draw hr row - override this function";
		}
		
		
		/**
		 * draw typography setting
		 */
		protected function drawTypographySetting($setting){
			?>
			<?php _e("The typography setting will be visible in Elementor Page Builder","unlimited-elements-for-elementor");?>
			<?php 
		}
		
		/**
		 * draw dimentions setting
		 */
		protected function drawDimentionsSetting($setting){
			
			dmp("draw dimentions setting - function for override");
			// function for override
			
		}
		
		
		
		/**
		 * draw input additinos like unit / description etc
		 */
		protected function drawInputAdditions($setting,$showDescription = true){
			
			$description = UniteFunctionsUC::getVal($setting, "description");
			if($showDescription === false)
				$description = "";
			$unit = UniteFunctionsUC::getVal($setting, "unit");
			$required = UniteFunctionsUC::getVal($setting, "required");
			$addHtml = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_ADDTEXT);
			
			?>
			
			<?php if(!empty($unit)):?>
			<span class='setting_unit'><?php echo esc_html($unit)?></span>
			<?php endif?>
			<?php if(!empty($required)):?>
			<span class='setting_required'>*</span>
			<?php endif?>
			<?php if(!empty($addHtml)):?>
			<span class="settings_addhtml"><?php echo esc_html($addHtml)?></span>
			<?php endif?>					
			<?php if(!empty($description) && $this->showDescAsTips == false):?>
			<span class="description"><?php echo $description?></span>
			<?php endif?>
			
			<?php 
		}
		
				
		
		/**
		 * get options
		 */
		protected function getOptions(){
			
			$idPrefix = $this->settings->getIDPrefix();
			
			$options = array();
			$options["show_saps"] = $this->showSaps;
			$options["saps_type"] = $this->sapsType;
			$options["id_prefix"] = $idPrefix;
			
			return($options);
		}
		
		
		/**
		* set form id
		 */
		public function setFormID($formID){
			
			if(isset(self::$arrIDs[$formID]))
				UniteFunctionsUC::throwError("Can't output settings with the same ID: $formID");
			
			self::$arrIDs[$formID] = true;
			
			UniteFunctionsUC::validateNotEmpty($formID, "formID");
			
			$this->formID = $formID;
			
		}
		
		
		/**
		 *
		 * insert settings into saps array
		 */
		private function groupSettingsIntoSaps(){
		    
		    $arrSaps = $this->settings->getArrSaps();
		    $arrSettings = $this->settings->getArrSettings();
		    
		    //group settings by saps
		    foreach($arrSettings as $key=>$setting){
		        
		        $sapID = $setting["sap"];
		        
		        if(isset($arrSaps[$sapID]["settings"]))
		            $arrSaps[$sapID]["settings"][] = $setting;
		            else
		                $arrSaps[$sapID]["settings"] = array($setting);
		    }
		    		    
		    return($arrSaps);
		}
		
		
		private function a______DRAW_GENENRAL_____(){}
		
		
		/**
		 * get controls for client side
		 * eliminate only one setting in children
		 */
		private function getControlsForJS(){
			
			$controls = $this->settings->getArrControls(true);
			$arrChildren = $controls["children"];
			
			if(empty($arrChildren))
				return($controls);
			
			$arrChildrenNew = array();
			
			foreach($arrChildren as $name=>$arrChild){
				if(count($arrChild)>1)
					$arrChildrenNew[$name] = $arrChild;
			}
			
			$controls["children"] = $arrChildrenNew;
			
			return($controls);
		}
		
		
		/**
		 * draw wrapper start
		 */
		public function drawWrapperStart(){
			
			UniteFunctionsUC::validateNotEmpty($this->settingsMainClass, "settings main class not found, please use wide, inline or sidebar output");
			
			//get options
			$options = $this->getOptions();
			$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($options);
			
			//get controls
			$controls = $this->getControlsForJS();
			
			/*
			if(!empty($controls["children"])){
				dmp($controls);exit();
			}
			*/
			
			$addHtml = "";
			if(!empty($controls)){
				$strControls = UniteFunctionsUC::jsonEncodeForHtmlData($controls);
				$addHtml = " data-controls=\"{$strControls}\"";
			}
			
			
			if(!empty($this->addCss)):
			?>
				<!-- settings add css -->
				<style type="text/css">
					<?php echo UniteProviderFunctionsUC::escAddParam($this->addCss)?>
				</style>
			<?php
			endif;
			
			?>
			<div id="<?php echo esc_attr($this->wrapperID)?>" data-options="<?php echo esc_attr($strOptions)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> autofocus="true" class="unite_settings_wrapper <?php echo UniteProviderFunctionsUC::escAddParam($this->settingsMainClass)?> unite-settings unite-inputs">
			
			<?php
		}
		
		
		/**
		 * draw wrapper end
		 */
		public function drawWrapperEnd(){
			
			?>
			
			</div>
			<?php 
		}
		
		
		/**
		 * function for override
		 */
		protected function setDrawOptions(){}
		
		/**
		 * 
		 * draw settings function
		 * @param $drawForm draw the form yes / no
		 * if filter sapid present, will be printed only current sap settings
		 */
		public function draw($formID, $drawForm = false){
			
			if(empty($this->settings))
				UniteFunctionsUC::throwError("No settings are inited. Please init the settings in output class");
			
			$this->setDrawOptions();
				
			$this->setFormID($formID);
			
			$this->drawWrapperStart();
			
			
			if($this->showSaps == true){
			     
			     switch($this->sapsType){
			         case self::SAPS_TYPE_INLINE:
			             $this->drawSapsTabs();
			         break;
			         case self::SAPS_TYPE_CUSTOM:
			             $this->drawSaps();
			         break;
			     }  
			     
			}
			
			
			if($drawForm == true){
				
				if(empty($formID))
					UniteFunctionsUC::throwError("The form ID can't be empty. you must provide it");
				
				?>
				<form name="<?php echo esc_attr($formID)?>" id="<?php echo esc_attr($formID)?>">
					<?php $this->drawSettings() ?>
				</form>
				<?php 				
			}else
				$this->drawSettings();
			
			?>
			
			<?php 
			
			$this->drawWrapperEnd();
			
		}

		
		/**
		 * draw wrapper before settings
		 */
		protected function drawSettings_before(){
		}
		
		
		/**
		* draw wrapper end after settings
		*/
		protected function drawSettingsAfter(){
		}
		

		/**
		 * draw single setting
		 */
		public function drawSingleSetting($name){
			
			$arrSetting = $this->settings->getSettingByName($name);
			
			$this->drawInputs($arrSetting);
			$this->drawInputAdditions($arrSetting);
		}
		
		
		/**
		 * function for override
		 */
		protected function drawSaps(){}
		
		
		/**
		 * draw saps tabs
		 */
		protected function drawSapsTabs(){
			
			$arrSaps = $this->settings->getArrSaps();
			
			?>
			<div class="unite-settings-tabs">
				
				<?php foreach($arrSaps as $key=>$sap){
					$text = $sap["text"];
					UniteFunctionsUC::validateNotEmpty($text,"sap $key text");
					
					$class = "";
					if($key == $this->activeSap)
						$class = "class='unite-tab-selected'";
					
					?>
					<a href="javascript:void(0)" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> data-sapnum="<?php echo esc_attr($key)?>" onfocus="this.blur()"><?php echo esc_html($text)?></a>
					<?php 
					
				}
				?>
				
			</div>
			<?php 
			
		}
		
		/**
		 * draw setting row by type
		 *
		 */
		private function drawSettingsRowByType($setting, $mode){
		    		    
		    switch($setting["type"]){
		        case UniteSettingsUC::TYPE_HR:
		            $this->drawHrRow($setting);
		            break;
		        case UniteSettingsUC::TYPE_STATIC_TEXT:
		            $this->drawTextRow($setting);
		            break;
		        default:
		            $this->drawSettingRow($setting, $mode);
		        break;
		    }
		    
		}
		
		
		/**
		 * draw settings - all together
		 */
		private function drawSettings_settings($filterSapID = null, $mode=null, $arrSettings = null){
		    
			if(is_null($arrSettings))
				$arrSettings = $this->arrSettings;
			
		    $this->drawSettings_before();
		    
		    foreach($arrSettings as $key=>$setting){
		            
		            if(isset($setting[UniteSettingsUC::PARAM_NODRAW]))
		                continue;
		                
		                if($filterSapID !== null){
		                    $sapID = UniteFunctionsUC::getVal($setting, "sap");
		                    if($sapID != $filterSapID)
		                        continue;
		                }
		                
		                $this->drawSettingsRowByType($setting, $mode);
		                
		        }
		        
		        $this->drawSettingsAfter();
		     
		}
		
		
		/**
		 * draw sap before override
		 * @param unknown $sap
		 */
		protected function drawSapBefore($sap, $key){
		    dmp("function for override");
		    
		}
		
		protected function drawSapAfter(){
		    dmp("function for override");
		}
		
		
		/**
		 * draw settings - all together
		 */
		private function drawSettings_saps($filterSapID = null, $mode=null){
		    
		    $arrSaps = $this->groupSettingsIntoSaps();
		    
		        //draw settings - advanced - with sections
		        foreach($arrSaps as $key=>$sap):
		        		
		        		$arrSettings = $sap["settings"];
		        		
		        		$nodraw = UniteFunctionsUC::getVal($sap, "nodraw");
		        		if($nodraw === true)
		        			continue;
		        		
		                $this->drawSapBefore($sap, $key);
						
						$this->drawSettings_settings($filterSapID, $mode, $arrSettings);
						
						$this->drawSapAfter();
						
		        
		        endforeach;
		    
		}
		
		
		
		/**
		 * draw all settings
		 */
		public function drawSettings($filterSapID = null){
			
			$this->prepareToDraw();
			
			$arrSettings = $this->settings->getArrSettings();
			if(empty($arrSettings))
			    $arrSettings = array();
			    
			$this->arrSettings = $arrSettings;

			//set special mode
			$mode = "";
			if(count($arrSettings) == 1 && $arrSettings[0]["type"] == UniteSettingsUC::TYPE_EDITOR)
			    $mode = "single_editor";
			
			
			if($this->showSaps == true && $this->sapsType == self::SAPS_TYPE_ACCORDION)
			    $this->drawSettings_saps($filterSapID, $mode);
			else			     
			    $this->drawSettings_settings($filterSapID, $mode);
			
		  
		}
		
		
		
	}

?>