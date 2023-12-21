<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorDialogParamElementor extends UniteCreatorDialogParam{

	
	private function ____HELPERS____(){}

	
	
	/**
	 * add selector html to the params
	 */
	private function addHtmlSelector(){
		
		?>		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("CSS Selector", "unlimited-elements-for-elementor")?>:
		</div>
				
		<input type="text" name="selector"  value="" placeholder="<?php _e("Example","unlimited-elements-for-elementor")?> .my-price">
		
		<div class="unite-inputs-sap"></div>
		
		<i><?php _e("* This attribute generate css only within the css selectors, it don't have placeholder in the widget editor","unlimited-elements-for-elementor")?></i>
		
		
		<?php
	}

	
	/**
	 * add selector html to the params
	 */
	private function addHtmlSelectorNameValue($selectorPlaceholder = "", $selectorValuePlaceholder = "", $value = "", $bottomTextUnits = ""){
		
		if(empty($selectorPlaceholder))
			$selectorPlaceholder = __("Example .my-price", "unlimited-elements-for-elementor");
		
		if(empty($bottomTextUnits))
			$bottomTextUnits = "{{SIZE}} &nbsp; {{UNIT}} &nbsp; {{CURRENT_ITEM}}";
		
		if($bottomTextUnits == "value_unit")
			$bottomTextUnits = "{{VALUE}} &nbsp; {{CURRENT_ITEM}}";
		
		$bottomText = __("* You can use those placeholders: ", "unlimited-elements-for-elementor") . $bottomTextUnits;
		
		$checkID = "check_".UniteFunctionsUC::getRandomString();
		
		
		?>		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("CSS Selector", "unlimited-elements-for-elementor")?>:
		</div>
				
		<input type="text" name="selector"  value="" placeholder="<?php echo $selectorPlaceholder?>">
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("CSS Selector Value", "unlimited-elements-for-elementor")?>:
		</div>
		
		<input type="text" name="selector_value" data-initval="<?php echo $value?>" value="<?php echo $value?>" placeholder="<?php echo $selectorValuePlaceholder?>">
		
		<div class="unite-inputs-sap"></div>
		
		<label for="<?php echo esc_attr($checkID)?>">
			<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox uc-control" data-controlled-selector=".uc-more-selectors" name="show_more_selectors">
			<?php _e("Show More Selectors", "unlimited-elements-for-elementor")?>
		</label>
		
		<div class="uc-more-selectors" style="display:none">
				
				<div class="unite-inputs-sap-double"></div>
			
				<div class="unite-inputs-label">
					<?php esc_html_e("CSS Selector 2", "unlimited-elements-for-elementor")?>:
				</div>
						
				<input type="text" name="selector2"  value="" placeholder="<?php echo $selectorPlaceholder?>">
				
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php esc_html_e("CSS Selector 2 Value", "unlimited-elements-for-elementor")?>:
				</div>
				
				<input type="text" name="selector2_value" data-initval="<?php echo $value?>" value="<?php echo $value?>" placeholder="<?php echo $selectorValuePlaceholder?>">
				
				
				<div class="unite-inputs-sap-double"></div>
				
				
				<div class="unite-inputs-label">
					<?php esc_html_e("CSS Selector 3", "unlimited-elements-for-elementor")?>:
				</div>
						
				<input type="text" name="selector3"  value="" placeholder="<?php echo $selectorPlaceholder?>">
				
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php esc_html_e("CSS Selector 3 Value", "unlimited-elements-for-elementor")?>:
				</div>
				
				<input type="text" name="selector3_value" data-initval="<?php echo $value?>" value="<?php echo $value?>" placeholder="<?php echo $selectorValuePlaceholder?>">
										
		</div>
		
		<div class="unite-inputs-sap"></div>
		
		<i>
		<?php echo $bottomText?>
		</i>
		
		<?php
	}
	
		
	
	
	/**
	 * put responsive controls
	 * Enter description here ...
	 */
	private function addResponsiveInputs($type){
		
		$checkID = "check_{$type}_param_responsive";
		
		?>
		
		<hr>
		
		<div class="unite-inputs-sap"></div>
		
		<label for="<?php echo esc_attr($checkID)?>">
			<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox uc-control" data-controlled-selector=".uc-responsive-controls" name="is_responsive">
			<?php _e("Responsive Control", "unlimited-elements-for-elementor")?>
		</label>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="uc-responsive-controls" style="display:none">
			
			<div class="unite-inputs-sap"></div>
		
			<div class="params-dialog-table">
			
				<div class="params-table-item">
					<div class="unite-inputs-label">
						<?php esc_html_e("Default Value - Tablet", "unlimited-elements-for-elementor")?>
					</div>
					
					<input type="text" name="default_value_tablet" class="input-small" value="">
				</div>
				<div class="params-table-item">
					<div class="unite-inputs-label">
						<?php esc_html_e("Default Value - Mobile", "unlimited-elements-for-elementor")?>
					</div>
					
					<input type="text" name="default_value_mobile" class="input-small" value="">
				</div>
				
			</div>
			
			<div class="unite-inputs-sap"></div>
						
		</div>
			
		<hr>
		
		<?php 
	}
	
	private function ____SOME_PARAMS____(){}
	
	/**
	 * put menu param
	 */
	protected function putMenuParam(){
		
		$settings = new UniteCreatorSettings();
		
		$settings->addTextBox("menu_class","uc-list-menu","Menu Class",array("description"=>"The class on menu ul element","unlimited-elements-for-elementor"));
		$settings->addTextBox("before","","Html Before Link",array("description"=>"The html that are put before link if needed","unlimited-elements-for-elementor"));
		$settings->addTextBox("after","","Html After Link",array("description"=>"The html that are put after the link if needed","unlimited-elements-for-elementor"));
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($settings);
		$objOutput->draw("menu_param_settings", false);
		
		?>
		<div class="unite-inputs-sap-double"></div>
		
		<i> 
			* <?php _e("Information about menu classes you can find here", "unlimited-elements-for-elementor")?>: 
			
			<a href="https://developer.wordpress.org/reference/functions/wp_nav_menu/" target="_blank"><?php _e("WP Menu Reference","unlimited-elements-for-elementor")?></a>
			
		</i>
		<?php 
	}
	
	/**
	 * 
	 * put template param
	 */
	protected function putTemplateParam(){
		
		$this->putNoDefaultValueText();
		
	}
	
	/**
	 * put users param
	 */
	protected function putUsersParam(){
		
		$checkID = "get_meta";
		$checkIDAvatar = "get_avatar";
		
		esc_html_e("Use this attribute to get the WP Users List", "unlimited-elements-for-elementor");
		
		?>
		<br>
		<br>
		
		<label for="<?php echo esc_attr($checkID)?>" >
			<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox"  name="<?php echo $checkID?>">
			<?php _e("Include User Meta Data", "unlimited-elements-for-elementor")?>
		</label>
		
		<div class="unite-inputs-sap"></div>
		
		<label for="<?php echo esc_attr($checkIDAvatar)?>" >
			<input id="<?php echo esc_attr($checkIDAvatar)?>" type="checkbox" class="uc-param-checkbox"  name="<?php echo $checkIDAvatar?>">
			<?php _e("Include Avatar", "unlimited-elements-for-elementor")?>
		</label>
		
		<?php 
		
	}
	
	
	/**
	 * put icon library parameter
	 */
	protected function putIconLibraryParam(){
		
		$checkID = "check_put_svg";
		$putSVGID = "put_svg_fields";
		
		?>
		<div class="unite-inputs-label">
			<?php esc_html_e("Default Value", "unlimited-elements-for-elementor")?>:
		</div>
		
		
		<input type="text" name="default_value"  value="">
		
		<div class="unite-inputs-sap-double"></div>
		
		<label for="<?php echo esc_attr($checkID)?>" >
			<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox uc-control" data-controlled-selector=".uc-put-svg-fields" name="enable_svg">
			<?php _e("Enable SVG Select", "unlimited-elements-for-elementor")?>
		</label>
		
		<div class="unite-inputs-sap"></div>
	    
		<label class="uc-put-svg-fields">
		
			<?php _e("Put SVG as", "unlimited-elements-for-elementor")?>
			
			<select name="put_svg_as">
				<option value="image" selected><?php _e("Image","unlimited-elements-for-elementor")?></option>
				<option value="svg"><?php _e("SVG Output","unlimited-elements-for-elementor")?></option>
			</select>
			
		</label>
		
		<?php 
	}
	
	/**
	 * function for override
	 */
	protected function putBorderParam(){
		
		//---- border type
		
		$arrType = array();
		$arrType["none"] = "No Border";
		$arrType["solid"] = "Solid";
		$arrType["double"] = "Double";
		$arrType["dotted"] = "Dotted";
		$arrType["groove"] = "Groove";
		
		$arrType = array_flip($arrType);
		
		$objSettings = new UniteCreatorSettings();
		
		$objSettings->addSelect("border_type", $arrType, __("Border Type", "unlimited-elements-for-elementor"), "none");
		
		$params = array();
		$params[UniteSettingsUC::PARAM_CLASSADD] = "uc-text-colorpicker";
		
		$objSettings->addColorPicker("border_color", "", "Solid Color", $params);
		
		$extra = array();
		$extra["output_names"] = true;
		$extra["no_units"] = true;
		
		$objSettings->addDimentionsSetting("width_desktop", "", "Border Width", $extra);
		$objSettings->addDimentionsSetting("width_tablet", "", "Border Width - Tablet", $extra);
		$objSettings->addDimentionsSetting("width_mobile", "", "Border Width - Mobile", $extra);
		
		//------
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		
		?>
		<div class="unite-inputs-label">
			<?php esc_html_e("Border Type", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php $objOutput->drawSingleSetting("border_type"); ?>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("Border Color", "unlimited-elements-for-elementor")?>:
		</div>

		<?php $objOutput->drawSingleSetting("border_color"); ?>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("Border Width", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php $objOutput->drawSingleSetting("width_desktop"); ?>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("Border Width - Tablet", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php $objOutput->drawSingleSetting("width_tablet"); ?>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("Border Width - Mobile", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php $objOutput->drawSingleSetting("width_mobile"); ?>
		
		<hr>		
		
		<?php 
		$this->addHtmlSelector();
		
		
	}
	
	
	/**
	 * slider param
	 */
	protected function putSliderParam(){
		
		$arrUnits = array();
		$arrUnits["px"] = "PX";
		$arrUnits["%"] = "%";
		$arrUnits["em"] = "EM";
		$arrUnits["vh"] = __("VH","unlimited-elements-for-elementor");
		$arrUnits["vw"] = __("VW","unlimited-elements-for-elementor");
		$arrUnits["percent_px"] = __("%, PX","unlimited-elements-for-elementor");		
		$arrUnits["px_percent"] = __("PX, %","unlimited-elements-for-elementor");
		$arrUnits["px_percent_em"] = __("PX, %, EM","unlimited-elements-for-elementor");
		$arrUnits["vh_px"] = __("VH, PX, REM","unlimited-elements-for-elementor");
		$arrUnits["px_vh"] = __("PX, VH, REM","unlimited-elements-for-elementor");
		$arrUnits["px_vh_percent"] = __("PX, VH, %","unlimited-elements-for-elementor");
		$arrUnits["vw_px"] = __("VW, PX","unlimited-elements-for-elementor");
		$arrUnits["px_vw"] = __("PX, VW","unlimited-elements-for-elementor");
		$arrUnits["px_vw_percent"] = __("PX, VW, %","unlimited-elements-for-elementor");
		
		
		$arrUnits = array_flip($arrUnits);
		
		$objSettings = new UniteCreatorSettings();
		
		$params = array();
		$params["class"] = "number";
		
		$objSettings->addTextBox("default_value","20",__("Default Value","unlimited-elements-for-elementor"),$params);
		$objSettings->addTextBox("min","1",__("Min","unlimited-elements-for-elementor"),$params);
		$objSettings->addTextBox("max","100",__("Max","unlimited-elements-for-elementor"),$params);
		$objSettings->addTextBox("step","1",__("Step","unlimited-elements-for-elementor"),$params);
		
		$objSettings->addSelect("units", $arrUnits, __("Units", "unlimited-elements-for-elementor"),"px");

		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		
		?>
		
		<div class="unite-inputs-label">
			<?php esc_html_e("Default Value", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php $objOutput->drawSingleSetting("default_value"); ?>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="params-dialog-table">
		
			<!-- Min -->
			<div class="params-table-item">
				<div class="unite-inputs-label">
					<?php esc_html_e("Min", "unlimited-elements-for-elementor")?>:
				</div>
				
				<?php $objOutput->drawSingleSetting("min"); ?>
			</div>
			
			<!-- Max -->
			<div class="params-table-item">
	
				<div class="unite-inputs-label">
					<?php esc_html_e("Max", "unlimited-elements-for-elementor")?>:
				</div>
			
			<?php $objOutput->drawSingleSetting("max"); ?>
			</div>
			
			<!-- Step -->
			<div class="params-table-item">
				
				<div class="unite-inputs-label">
					<?php esc_html_e("Step", "unlimited-elements-for-elementor")?>:
				</div>
			
			<?php $objOutput->drawSingleSetting("step"); ?>
			</div>

			<div class="params-table-item">
			
				<div class="unite-inputs-label">
					<?php esc_html_e("Units", "unlimited-elements-for-elementor")?>:
				</div>
				
				<?php $objOutput->drawSingleSetting("units"); ?>
				
			</div>
		
		</div>
		<div class="unite-dialog-description-left">
			<?php _e("* In case of multiple units, the min, max apply to px unit only.", "unlimited-elements-for-elementor")?>
		</div>
		
		<?php 
		
		$this->addResponsiveInputs("slider");
		
		$this->addHtmlSelectorNameValue("Example .box", "example - width: {{SIZE}}{{UNIT}};", "width: {{SIZE}}{{UNIT}};")
		
		?>
				
		
		<?php
	}
	
	/**
	 * put radio boolean param
	 */
	protected function putRadioBooleanParam(){
		?>
			<table data-inputtype="radio_boolean"  class='uc-table-dropdown-items uc-table-dropdown-full'>
				<thead>
					<tr>
						<th width="100px"><?php esc_html_e("Item Text", "unlimited-elements-for-elementor")?></th>
						<th width="100px"><?php esc_html_e("Item Value", "unlimited-elements-for-elementor")?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="text" name="true_name" value="Yes" data-initval="Yes" class='uc-dropdown-item-name'></td>
						<td><input type="text" name="true_value" value="true" data-initval="true" class='uc-dropdown-item-value'></td>
						<td>
							<div class='uc-dropdown-icon uc-dropdown-item-default uc-selected' title="<?php esc_html_e("Default Item", "unlimited-elements-for-elementor")?>"></div>
						</td>
					</tr>
					<tr>
						<td><input type="text" name="false_name" value="No" data-initval="No" class='uc-dropdown-item-name'></td>
						<td><input type="text" name="false_value" value="false" data-initval="false" class='uc-dropdown-item-value'></td>
						<td>
							<div class='uc-dropdown-icon uc-dropdown-item-default' title="<?php esc_html_e("Default Item", "unlimited-elements-for-elementor")?>"></div>
						</td>
					</tr>
					
				</tbody>
			</table>
		<?php 
		
		$this->addResponsiveInputs("radio_boolean");
		
			
	}
	
	
	/**
	 * background param
	 */
	protected function putBackgroundParam(){
		
		$arrSelect = array();
		$objSettings = new UniteCreatorSettings();
		
		$params = array();
		$params[UniteSettingsUC::PARAM_CLASSADD] = "uc-text-colorpicker";
		
		$objSettings->addColorPicker("solid_color", "", "Solid Color", $params);
		
		$objSettings->addColorPicker("gradient_color1", "", "Gradient Color 1", $params);
		$objSettings->addColorPicker("gradient_color2", "", "Gradient Color 2", $params);
		
		//---- image bg position 
		
		$arrPosition = array();
		$arrPosition[""] = "Default";
		$arrPosition["center center"] = "Center Center";
		$arrPosition["center left"] = "Center Left";
		$arrPosition["center right"] = "Center Right";
		$arrPosition["top center"] = "Top Center";
		$arrPosition["top left"] = "Top Left";
		$arrPosition["top right"] = "Top Right";
		$arrPosition["bottom center"] = "Bottom Center";
		$arrPosition["bottom left"] = "Bottom Left";
		$arrPosition["bottom right"] = "Bottom Right";
		
		$arrPosition = array_flip($arrPosition);
		
		$objSettings->addSelect("solid_bg_image_position", $arrPosition, __("Background Image Position", "unlimited-elements-for-elementor"));
		
		
		//---- image bg repeat
		
		$arrRepeat = array();
		$arrRepeat[""] = "Default";
		$arrRepeat["no-repeat"] = "No-repeat";
		$arrRepeat["repeat"] = "Repeat";
		$arrRepeat["repeat-x"] = "Repeat-x";
		$arrRepeat["repeat-y"] = "Repeat-y";
		
		$arrRepeat = array_flip($arrRepeat);
		
		$objSettings->addSelect("solid_bg_image_repeat", $arrRepeat, __("Background Image Repeat", "unlimited-elements-for-elementor"));
		
		
		//---- image bg size
		
		$arrSize = array();
		$arrSize[""] = "Default";
		$arrSize["auto"] = "Auto";
		$arrSize["cover"] = "Cover";
		$arrSize["contain"] = "Contain";
		
		$arrSize = array_flip($arrSize);
		
		$objSettings->addSelect("solid_bg_image_size", $arrSize, __("Background Image Size", "unlimited-elements-for-elementor"));
		
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		
		?>
			<div class='uc-paramsdialog-radio-chooser uc-radioset-wrapper' data-defaultchecked="none">
				
				<label>
					<?php _e("None","unlimited-elements-for-elementor")?>:
					<input type="radio" name="background_type" class="uc-param-radio uc-control" data-controlled-selector=".uc-controls-none" value="none" >
				</label>
				
				<label>
					<?php _e("Solid","unlimited-elements-for-elementor")?>:
					<input type="radio" name="background_type" class="uc-param-radio uc-control" data-controlled-selector=".uc-controls-solid" value="solid" >
				</label>
				
				<label>
					<?php _e("Gradient","unlimited-elements-for-elementor")?>
					<input type="radio" name="background_type" class="uc-param-radio uc-control" data-controlled-selector=".uc-controls-gradient" value="gradient">
				</label>
				
			</div>

			<div class="uc-controls-none">
															
			</div>

			
			<div class="uc-controls-solid" style="display:none">
				
				<div class="vert_sap5"></div>
				
				<label><?php _e("Solid Color","unlimited-elements-for-elementor")?></label>
				
				<?php 
				$objOutput->drawSingleSetting("solid_color");
				?>
				
				<div class="vert_sap10"></div>
				
				<hr>
				
				<div class="vert_sap10"></div>
				
				<?php $this->putImageSelectInput("solid_bg_image", esc_html__("Background Image","unlimited-elements-for-elementor"))?>
				
				<div class="vert_sap10"></div>
				
				<label><?php _e("Image Position","unlimited-elements-for-elementor")?></label>
				
				<?php 
				$objOutput->drawSingleSetting("solid_bg_image_position");
				?>

				<div class="vert_sap10"></div>
				<label><?php _e("Image Repeat","unlimited-elements-for-elementor")?></label>
				
				<?php 
				$objOutput->drawSingleSetting("solid_bg_image_repeat");
				?>

				<div class="vert_sap10"></div>
				<label><?php _e("Image Size","unlimited-elements-for-elementor")?></label>
				
				<?php 
				$objOutput->drawSingleSetting("solid_bg_image_size");
				?>

				
			</div>
			
			<div class="vert_sap5"></div>
			
			<div class="uc-dialogparam-horlist uc-controls-gradient" style="display:none">
				
				<label>
					<?php _e("Gradient Color1", "unlimited-elements-for-elementor")?>
					
					<?php $objOutput->drawSingleSetting("gradient_color1"); ?>
					
				</label>
				<label>
					<?php _e("Gradient Color2", "unlimited-elements-for-elementor")?>
				
					<?php $objOutput->drawSingleSetting("gradient_color2");?>
				
				</label>
			
			</div>
			
			<div class="vert_sap20"></div>
			<hr>
		<?php 
		
		$this->addHtmlSelector();
		
	}
	
	
	
	/**
	 * function for override
	 */
	protected function putDateTimeParam(){
		
		
		$arrModes = array();
		$arrModes["date"] = "Date Only";
		$arrModes["date_time"] = "Date And Time";
		$arrModes["time"] = "Time Only";
		
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrModes, "", "name='date_time_mode'", true, "date");
		
		
		?>
		
		<div class="unite-inputs-label">
			<?php echo __("Date Time", "unlimited-elements-for-elementor")?>:
		</div>
		
		<input type="text" name="default" value="" placeholder="YYYY-mm-dd HH:ii">
		
		<div class="unite-inputs-sap"></div>
		
		<i><?php _e("* The default value can be empty as well","unlimited-elements-for-elementor")?></i>

		<div class="unite-inputs-sap-double"></div>
		
		<div class="unite-inputs-label">
			<?php echo __("Date / Time Mode", "unlimited-elements-for-elementor")?>:
			
			<?php echo $htmlSelect?>
			
		</div>
			
				
		
		<?php 	
	}
	
	/**
	 * add color picker setting
	 */
	private function putColorPickerSetting($name, $text, $color){
		?>
			<?php $text?>:
			
 		    <input type="text" name="<?php echo $name?>" class="uc-text-colorpicker" value="<?php echo $color?>" data-initval="<?php echo $color?>">
			<div class='unite-color-picker-element'></div>
		<?php 
	}
	
	/**
	 * function for override
	 */
	protected function putTextShadowParam(){

		$this->addHtmlSelector();
		
		return(false);
		
		/*
		$params = array();
		$params["class"] = "number";
		
		$objSettings = new UniteCreatorSettings();
		
		$objSettings->addTextBox("blur", "20", __("Default Value","unlimited-elements-for-elementor"),$params);
		$objSettings->addTextBox("horizontal", "1", __("Min","unlimited-elements-for-elementor"),$params);
		$objSettings->addTextBox("vertical", "100", __("Max","unlimited-elements-for-elementor"),$params);
		
		$textColor = __("Default Color","unlimited-elements-for-elementor");
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
		
		$checkID = "text_shadow_enable_default_values";
		
		?>			
															
			<label for="<?php echo esc_attr($checkID)?>">
				<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox uc-control" data-controlled-selector=".uc-default-settings" name="enable_default_values">
				<?php _e("Enable Default Values", "unlimited-elements-for-elementor")?>
			</label>
			
			<div class="unite-inputs-sap"></div>
			
			<div class="uc-default-settings">
				
					<hr>
							
					<!-- color setting -->
			
					<div class="unite-inputs-label">
						<?php esc_html_e("Color", "unlimited-elements-for-elementor")?>:
					</div>
								
					<?php 
						$this->putColorPickerSetting("color", $textColor, "rgba(0,0,0,0.5)");
					?>
					
					<div class="unite-inputs-sap"></div>
					
					
					<!-- blur setting -->
					
					<div class="unite-inputs-label">
						<?php esc_html_e("Blur", "unlimited-elements-for-elementor")?>:
					</div>
								
					<?php 
						$objOutput->drawSingleSetting("blur");
					?>
					
					<div class="unite-inputs-sap"></div>
					
					
					<!-- horizontal setting -->
					
					<div class="unite-inputs-label">
						<?php esc_html_e("Horizontal", "unlimited-elements-for-elementor")?>:
					</div>
								
					<?php 
						$objOutput->drawSingleSetting("horizontal");
					?>
					
					<div class="unite-inputs-sap"></div>
					
					
					
					<!-- vertical setting -->
					
					<div class="unite-inputs-label">
						<?php esc_html_e("Vertical", "unlimited-elements-for-elementor")?>:
					</div>
								
					<?php 
						$objOutput->drawSingleSetting("vertical");
					?>
					
					<div class="unite-inputs-sap"></div>
					
					
			</div>
			
			<hr>
			
			<?php 
				$this->addHtmlSelector();
			?>
			
		<?php 
		
		*/	
	}
	
	/**
	 * put box shadow param
	 */
	protected function putBoxShadowParam(){
		
		$this->addHtmlSelector();
				
	}


	/**
	 * put css filter param
	 */
	protected function putCssFiltersParam(){
		
		$this->addHtmlSelector();
				
	}

	
	/**
	 * put hover animations param
	 */
	protected function putHoverAnimations(){
		
		esc_html_e("Default Value", "unlimited-elements-for-elementor");

		$arrAnimations = HelperProviderCoreUC_EL::getHoverAnimationClasses();
		
		$htmlSelect = HelperHtmlUC::getHTMLSelect($arrAnimations, "", "name='default_value'", true, "not_chosen");
		
		?>
			<div class="vert_sap5"></div>
			
			<?php echo $htmlSelect?>
			
			<div class="vert_sap10"></div>
 		    
 		    <p>
 		    <?php _e("Add the value of this attribute to the class list of your html element","unlimited-elements-for-elementor")?>
 		    </p>
		<?php 
		
	}
	
	/**
	 * 
	 * function for override
	 */
	protected function putSpecialAttribute(){
		
		$arrTypes = array();
		$arrTypes["none"] = __("[Select Type]","unlimited-elements-for-elementor");
		$arrTypes["entrance_animation"] = __("Entrance Animation","unlimited-elements-for-elementor");
		$arrTypes["items_image_size"] = __("Items Image Size","unlimited-elements-for-elementor");
		$arrTypes["schema"] = __("Schema","unlimited-elements-for-elementor");
		$arrTypes["dynamic_popup"] = __("Dynamic Popup","unlimited-elements-for-elementor");
		$arrTypes["contact_form7"] = __("Contact Form 7","unlimited-elements-for-elementor");
		$arrTypes["ucform_conditions"] = __("Unite Form Conditions","unlimited-elements-for-elementor");
		$arrTypes["sort_filter_fields"] = __("Sort Filter Fields","unlimited-elements-for-elementor");
		$arrTypes["currency_api"] = __("Currency API Fields","unlimited-elements-for-elementor");
		
		$optionsClass = "uc-special-attribute-options";
		
		$htmlSelectTypes = HelperHtmlUC::getHTMLSelect($arrTypes, "", "name='attribute_type' class='uc-control' data-controlled-selector='.{$optionsClass}'", true, "refresh");
		
		?>
		
			<?php esc_html_e("Special Attribute Type", "unlimited-elements-for-elementor"); ?>
			
			<div class="vert_sap5"></div>
			
			<?php echo $htmlSelectTypes?>
			
			<div class="vert_sap30"></div>
 		    
 		    <!-- animation -->
 		    
 		    <div class="<?php echo $optionsClass?>" data-control="entrance_animation" style="display:none">
				
				<div class="unite-inputs-label">
			 		    <?php _e("Item Class","unlimited-elements-for-elementor")?>		
				</div>
 		    		 		    
 		    	<input type="text" name="entrance_animation_item_class" value="" placeholder="Example: ue-item">
	 		    
			</div>
			
			<!-- schema -->
			
 		    <div class="<?php echo $optionsClass?>" data-control="schema" style="display:none">
				
				<div class="unite-inputs-label">
			 		    <?php _e("Items Attribute - Title","unlimited-elements-for-elementor")?>		
				</div>
 		    		 		    
 		    	<input type="text" name="schema_title_name" value="title" placeholder="example: title">
				
				<div class="vert_sap20"></div>
				
				<div class="unite-inputs-label">
			 		    <?php _e("Items Attribute - Content","unlimited-elements-for-elementor")?>		
				</div>
 		    	
 		    	<input type="text" name="schema_content_name" value="content" placeholder="example: content">

			</div>

<!-- image size -->
			
 		    <div class="<?php echo $optionsClass?>" data-control="items_image_size" style="display:none">
				
				<div class="unite-inputs-label">
			 		    <?php _e("Items Attribute Name","unlimited-elements-for-elementor")?>		
				</div>
 		    		 		    
 		    	<input type="text" name="image_size_param_name" value="" placeholder="Example: image1">
	 		    
				<div class="unite-dialog-description-left">
					<?php _e("* If leave empty, then the image size chooser will affect the first item image attribute.", "unlimited-elements-for-elementor")?>
				</div>
	 		    
	 		    
			</div>

 		    <div class="<?php echo $optionsClass?>" data-control="dynamic_popup" style="display:none">
				
				<div class="unite-inputs-label">
			 		    <?php _e("Attribute Suffix","unlimited-elements-for-elementor")?>		
				</div>
 		    	
 		    	<input type="text" name="dynamic_popup_suffix" value="" placeholder="Example: title">
	 		    
				<div class="unite-dialog-description-left">
					<?php _e("For the button leave it empty.", "unlimited-elements-for-elementor")?>
				</div>
	 		    
	 		    
			</div>

			
		<?php 
	}
	
	
	/**
	 * put color picker default value
	 */
	protected function putColorPickerDefault(){
				
		?>
			<?php esc_html_e("Default Value", "unlimited-elements-for-elementor")?>:
			
			<div class="vert_sap5"></div>
 		    <input type="text" name="default_value" class="uc-text-colorpicker" value="#ffffff" data-initval="#ffffff">
			<div class='unite-color-picker-element'></div>
			
			
			<?php 
				
			$placeholder = __("Example: .my-box", "unlimited-elements-for-elementor");
				
			$this->addHtmlSelectorNameValue($placeholder, "", "", "value_unit")?>
			
		<?php 
	}
	
	/**
	 * put drop down param
	 */
	protected function putDropDownParam(){
		
		$this->putDropdownItems();
		
		?>
		
		<div class="vert_sap10"></div>
		<hr>
		<div class="vert_sap5"></div>
		
		<?php 
		
		$placeholder = __("Example: .my-box", "unlimited-elements-for-elementor");
			
		$this->addHtmlSelectorNameValue($placeholder, "", "", "value_unit");
				
		$this->addResponsiveInputs("dropdown");
		
		$this->addPHPFilterOptions("dropdown");
		
	}
	
	/**
	 * 
	 * function for override
	 */
	protected function putPostSelectAttribute(){
		
		$this->putNoDefaultValueText();
				
	}

	/**
	 * function for override
	 */
	protected function putTermSelectAttribute(){
		
		$this->putNoDefaultValueText();
	}
	
	
	private function ____NUMBER____(){}
	
	/**
	 * put number unit select
	 */
	protected function putNumberUnitSelect(){
		?>
				<div class="unite-inputs-label-inline-suffix">
					<?php esc_html_e("Suffix")?>:
				</div>
				
				<select name="unit" class='uc-select-unit' data-initval="px">
					<option value="px">px</option>
					<option value="ms">ms</option>
					<option value="%">%</option>
					<option value="em">em</option>
					<option value="">[none]</option>
					<option value="other">[custom]</option>
				</select>
				
				<input type="text" class='uc-text-unit-custom input-small' name="unit_custom" style="display:none">
		<?php
	}
	
	/**
	 * put number max and min values
	 */
	private function putNumberMaxMinInputs(){
		
		?>
		
		<div class="unite-inputs-sap"></div>
		
		<hr>
		
		<span class="unite-inputs-label"><?php _e("Min Value", "unlimited-elements-for-elementor")?></span>: 
		<input type="text" name="min_value" class="input-small">
		
		&nbsp;&nbsp;
		
		<span class="unite-inputs-label"><?php _e("Max Value", "unlimited-elements-for-elementor")?></span>: 
		<input type="text" name="max_value" class="input-small">
		
		&nbsp;&nbsp;
		
		<span class="unite-inputs-label"><?php _e("Step", "unlimited-elements-for-elementor")?></span>: 
		<input type="text" name="step" class="input-small" placeholder="1">
		
		<div class="unite-dialog-description-left">
			<?php _e("* Those settings are for the attribute up and down arrows only.", "unlimited-elements-for-elementor")?>
		</div>
						
		<?php 
		
	}
	
	/**
	 * put number param field
	 */
	protected function putNumberParam(){
		
		$this->putDefaultValueParam(false, "input-small");
		
		$this->putNumberUnitSelect();
				
		$this->putNumberMaxMinInputs();
		
		$this->addResponsiveInputs("number");
		
		$bottomText = __("* You can use those placeholders: {{VALUE}} &nbsp; {{CURRENT_ITEM}}", "unlimited-elements-for-elementor");
		
		$this->addHtmlSelectorNameValue("","","",$bottomText);
		
				
	}
		
	
	
	/**
	 * put dimentions param
	 * type can be padding or margin
	 */
	protected function putDimentionsParam($type = ""){
		
		$title = __("Margins","unlimited-elements-for-elementor");
		if($type == "padding")
			$title = "Padding";
			
		if($type == "border")
			$title = "Border Width";
		
		$extra = array();
		$extra["output_names"] = true;
		
		$objSettings = new UniteCreatorSettings();
		
		$objSettings->addDimentionsSetting("desktop", "", "Dimentions", $extra);
		$objSettings->addDimentionsSetting("tablet", "", "Tablet", $extra);
		$objSettings->addDimentionsSetting("mobile", "", "Mobile", $extra);
		
		$objOutput = new UniteSettingsOutputWideUC();
		$objOutput->init($objSettings);
			
		$checkID = "check_dimentions_{$type}_is_responsive";
		
		?>
		
		<label for="<?php echo esc_attr($checkID)?>">
			<input id="<?php echo esc_attr($checkID)?>" type="checkbox" class="uc-param-checkbox uc-control" data-controlled-selector=".uc-responsive-controls,.uc-label-desktop" name="is_responsive">
			<?php _e("Responsive Control", "unlimited-elements-for-elementor")?>
		</label>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			<?php echo $title.__(" Default Values", "unlimited-elements-for-elementor")?>:
		</div>
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label uc-label-desktop" style="display:none">
			<?php esc_html_e("Desktop", "unlimited-elements-for-elementor")?>:
		</div>
		
		<?php 
		$objOutput->drawSingleSetting("desktop");
		?>
				
		
		<div class="uc-responsive-controls" style="display:none">
				
				<div class="unite-inputs-sap"></div>
		
				<div class="unite-inputs-label">
					<?php esc_html_e("Tablet", "unlimited-elements-for-elementor")?>:
				</div>
				
				<?php 
				$objOutput->drawSingleSetting("tablet");
				?>
				
				<div class="unite-inputs-sap"></div>
				
				<div class="unite-inputs-label">
					<?php esc_html_e("Mobile", "unlimited-elements-for-elementor")?>:
				</div>
								
				<?php 
				$objOutput->drawSingleSetting("mobile");
				?>
		</div>
		
		<?php $this->addHtmlSelector()?>
		
		<!--  additional units -->
		
		<div class="unite-inputs-sap-double"></div>
		
		<div class="unite-inputs-label">
			
			<?php esc_html_e("Additional Units", "unlimited-elements-for-elementor")?>:
		</div>		
		
		<input type="text" name="add_units" value="">
		
		<div class="unite-dialog-description-left">
			* <?php esc_html_e("Here you can specify additional units comma separated like vw, vh etc.", "unlimited-elements-for-elementor")?>
		</div>
		
		
		<?php
	}	
	
	
	/**
	 * put elementor typography param field
	 */
	protected function putTypographyParamField(){
		?>
		
		<!-- selector 1 -->
		
		<div class="unite-inputs-label">
			
			<?php esc_html_e("CSS Selector", "unlimited-elements-for-elementor")?>:
		</div>		
		
		<input type="text" name="selector1" value="">
		
		<!-- selector 2 -->
		
		<div class="unite-inputs-sap"></div>
						
		<div class="unite-inputs-label">
			
			<?php esc_html_e("CSS Selector 2 (optional)", "unlimited-elements-for-elementor")?>:
		</div>		
		
		<input type="text" name="selector2" value="">
		
		<!-- selector 3 -->
		
		<div class="unite-inputs-sap"></div>
		
		<div class="unite-inputs-label">
			
			<?php esc_html_e("CSS Selector 3 (optional)", "unlimited-elements-for-elementor")?>:
		</div>		
		
		<input type="text" name="selector3" value="">
		
		<div class="unite-dialog-description-right">
			* <?php esc_html_e("The selector that the typography field will be related to. Can be related to several html tags.", "unlimited-elements-for-elementor")?>
		</div>
		
		<?php 
	}
	
	
	
	/**
	 * put param content
	 */
	protected function putParamFields($paramType){
	
		switch($paramType){
			
			case self::PARAM_TYPOGRAPHY:
				$this->putTypographyParamField();
			break;
			default:
				parent::putParamFields($paramType);
			break;
		}
	
	}
	
	
	/**
	 * init by addon type
	 * function for override
	 */
	protected function initByAddonType($addonType){
		
		if($addonType != GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR)
			return(false);
		
		$this->option_putAdminLabel = false;
				
	}
	
	
}