<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');
	
	class UniteCreatorSettingsOutput extends UniteSettingsOutputUC{
		
		private static $counter = 1;
		
		private function a_______COLS_LAYOUT_________(){}
		
		
		/**
		 * draw columns layout output
		 */
		protected function drawColsLayoutInput($setting){
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			$id = UniteFunctionsUC::getVal($setting, "id");
			$name = UniteFunctionsUC::getVal($setting, "name");
			
			?>
				
				<div id="<?php echo esc_attr($id)?>" data-name="<?php echo esc_attr($name)?>" data-settingtype="col_layout" class="uc-setting-cols-layout unite-setting-input-object">
				        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_1" title="100%">
                            <div class="uc-layout-col uc-colsize-1_1 unite-clear"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_2-1_2" title="50% 50%">
                             <div class="uc-layout-col uc-colsize-1_2"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_2"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_4-1_4-1_4-1_4" title="25% 25% 25% 25%">
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_3-1_3-1_3" title="33% 33% 33%">
                            <div class="uc-layout-col uc-colsize-1_3"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_3"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_3"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_4-3_4" title="25% 75%">
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-3_4"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_4-1_4-1_2" title="25% 25% 50%">
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_2"><span></span></div>
                        </div>
                                                                    
                        <div class='uc-layout-row unite-clear' data-layout-type="2_3-1_3" title="66% 33%">
                            <div class="uc-layout-col uc-colsize-2_3"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_3"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_3-2_3" title="33% 66%">
                            <div class="uc-layout-col uc-colsize-1_3"><span></span></div>
                            <div class="uc-layout-col uc-colsize-2_3"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="3_4-1_4" title="75% 25%">
                            <div class="uc-layout-col uc-colsize-3_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_4-1_2-1_4" title="25% 50% 25%">
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_2"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                        </div>
                                                               
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_2-1_4-1_4" title="50% 25% 25%">
                            <div class="uc-layout-col uc-colsize-1_2"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_4"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="2_5-1_5-1_5-1_5" title="40% 20% 20% 20%">
                            <div class="uc-layout-col uc-colsize-2_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                        </div>
                        
                        <div class='uc-layout-row unite-clear' data-layout-type="1_5-1_5-1_5-2_5" title="20% 20% 20% 40%">
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-1_5"><span></span></div>
                            <div class="uc-layout-col uc-colsize-2_5"><span></span></div>
                        </div>
                        
				</div>
			
			<?php
		}
		
		private function a________SAVE_GRID_PANEL________(){}
		
		
		/**
		 * draw save grid panel
		 */
		private function drawSaveGridPanelButton($setting){
			
			$id = UniteFunctionsUC::getVal($setting, "id");
			$name = UniteFunctionsUC::getVal($setting, "name");
			
			$prefix = $id;
			
			?>
			<div id="<?php echo esc_attr($id)?>" data-name="<?php echo esc_attr($name)?>" data-settingtype="save_section_tolibrary" class="uc-setting-save-panel-wrapper unite-setting-input-object">
				
				<?php 
				$buttonTitle = esc_html__("Save Section", "unlimited-elements-for-elementor");
				$loaderTitle = esc_html__("Saving...", "unlimited-elements-for-elementor");
				$successTitle = esc_html__("Section Saved", "unlimited-elements-for-elementor");
				HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
				?>
				
				
			</div>
			<?php
		}
		
		private function a_______GRID_PANEL_BUTTON_____(){}
		
		
		/**
		 * draw save grid panel
		 */
		private function drawGridPanelButton($setting){
			
			$id = UniteFunctionsUC::getVal($setting, "id");
			$name = UniteFunctionsUC::getVal($setting, "name");
			$class = UniteFunctionsUC::getVal($setting, "class");
			if(empty($class))
				$class = "unite-button-secondary";
			
			$prefix = $id;
			
			$label = UniteFunctionsUC::getVal($setting, "button_text");
			if(empty($label))
				$label = esc_html__("Click Me", "unlimited-elements-for-elementor");
			
			$label = UniteFunctionsUC::sanitizeAttr($label);
						
			$action = UniteFunctionsUC::getVal($setting, "action", "no_action");
			$action = UniteFunctionsUC::sanitizeAttr($action);
			
			$actionParam = UniteFunctionsUC::getVal($setting, "action_param");
			
			$addHtml = "";
			if(!empty($actionParam)){
				$actionParam = UniteFunctionsUC::sanitizeAttr($actionParam);
				$addHtml = "data-actionparam=\"$actionParam\"";
			}
			
			?>
			<div id="<?php echo esc_attr($id)?>" data-settingtype="grid_panel_button" class="unite-setting-input-object uc-grid-panel-button-wrapper">
				
				<a id="<?php echo esc_attr($id)?>_button" data-action="<?php echo esc_attr($action)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> href="javascript:void(0)"  class="uc-grid-panel-button <?php echo esc_attr($class)?>"><?php echo esc_html($label)?></a>
			
			</div>
			<?php
		}
		
		
		private function a_______SIZE_RELATED_LAYOUT_____(){}
		
		
		/**
		 * draw size input label
		 */
		protected function drawSizeInput_label($setting, $size){
						
			$keyLabel = "label_".$size;
			$label = UniteFunctionsUC::getVal($setting, $keyLabel);
			$label = htmlspecialchars($label);
			
			
			$keyDesc = "description_".$size;
			$description = UniteFunctionsUC::getVal($setting, $keyDesc);
			$description = htmlspecialchars($description);
			
			$addClass = "uc-showin-".$size;
						
			if(empty($label))
				return(false);
			
			?>
			
			<div class="unite-setting-text uc-tip <?php echo esc_attr($addClass)?>" title="<?php echo esc_attr($description)?>">
				<?php echo esc_html($label)?>
			</div>
			
			<?php 
		}
		
		
		/**
		 * draw the four input for perticular size
		 */
		protected function drawFourInputsInput_size($setting, $baseName, $size, $arrSuffix, $arrTitles){
			
			$arrObjSettings = array();
			foreach($arrSuffix as $suffix){
				
				$settingName = $baseName."_".$suffix;
				
				if(!empty($size) && $size != "desktop")
					$settingName .= "_".$size;
				
				$objSettings = $this->settings->getSettingByName($settingName);
				$objSettings["type_number"] = true;
				unset($objSettings["unit"]);
				$objSettings["class"] = "nothing";
				
				$arrObjSettings[$settingName] = $objSettings;
			}
			
			$index = 0;
			
			$this->drawSizeInput_label($setting, $size);
			
			$addClass = "uc-showin-".$size;
			
			?>
			
			<div class="unite-setting-paddingline unite-clear <?php echo esc_attr($addClass)?>">
				<?php 
				foreach($arrObjSettings as $setting):
					
					$title = $arrTitles[$index];
					
					?>
					<div class="unite-setting-paddingline-item">
						<?php $this->drawTextInput($setting);?>
						<label><?php echo esc_html($title)?></label>
					</div>
					<?php
					$index++;
				endforeach;
				
				?>
			</div>
			<?php 
				
		}
		
		/**
		 * get the sizes array from size related draw setting
		 */
		protected function getSizesFromCustomSetting($setting){
			$arrSizes = array("desktop");
			$sizes = UniteFunctionsUC::getVal($setting, "sizes");
			
			if($sizes == "all")
				$arrSizes = array_merge($arrSizes, GlobalsUC::$arrSizes);
			
			return($arrSizes);
		}
		
		
		/**
		 * draw four inputs input, for padding and margin
		 * check that the settings are there
		 */
		protected function drawFourInputsInput($setting){
			
			$baseName = UniteFunctionsUC::getVal($setting, "name");
			$prefix = UniteFunctionsUC::getVal($setting, "prefix");
			$prefixMobile = UniteFunctionsUC::getVal($setting, "prefixmobile"); 
			$onlyTopBottom = UniteFunctionsUC::getVal($setting, "onlytopbottom"); 
			$onlyTopBottom = UniteFunctionsUC::strToBool($onlyTopBottom);
			
			if(!empty($prefix))
				$baseName = $prefix;
			
			$arrSizes = $this->getSizesFromCustomSetting($setting);
			
			$arrSuffix = array("top", "right", "bottom", "left");
			$arrTitles = array(
					esc_html__("Top","unlimited-elements-for-elementor"),
					esc_html__("Right","unlimited-elements-for-elementor"), 
					esc_html__("Bottom","unlimited-elements-for-elementor"),
					esc_html__("Left","unlimited-elements-for-elementor")
			);
			
			//put only top and bottom
			if($onlyTopBottom == true){
				
				$arrSuffix = array("top", "bottom");
				$arrTitles = array(
						esc_html__("Top","unlimited-elements-for-elementor"),
						esc_html__("Bottom","unlimited-elements-for-elementor"),
				);
				
			}
			
			foreach($arrSizes as $size){
				
				if(!empty($prefixMobile) && $size != "desktop")
					$baseName = $prefixMobile;
				
				$this->drawFourInputsInput_size($setting, $baseName, $size, $arrSuffix, $arrTitles);
			}
			
		}
		
		
		/**
		 * draw input with sizes
		 */
		protected function drawInputWithSizes($setting){
			
			$baseName = UniteFunctionsUC::getVal($setting, "prefix");
			$arrSizes = $this->getSizesFromCustomSetting($setting);
			
			
			foreach($arrSizes as $size){
				
				$settingName = $baseName;
				
				if(!empty($size) && $size != "desktop")
					$settingName .= "_".$size;
				
				$objSettings = $this->settings->getSettingByName($settingName);
				
				$this->drawSizeInput_label($setting, $size);
				
				$type = UniteFunctionsUC::getVal($objSettings, "type");
				
				if($type == "custom")
					UniteFunctionsUC::throwError("the input should not be custom here!");
			
				$showinClass = "uc-showin-".$size;
				
				$unit = UniteFunctionsUC::getVal($objSettings, "unit");
				?>
				
				<div class="<?php echo esc_attr($showinClass)?>">
				
					<?php $this->drawInputs($objSettings); ?>
					<?php if(!empty($unit)):?>
						<span class="setting_unit"><?php echo esc_html($unit)?></span>
					<?php endif?>
					
				</div>
				
				<?php 
			}
			
			
		}
		
		/**
		 * draw connect with instagram button
		 */
		private function drawConnectWithInstagramButton($setting){
						
			$objServices = new UniteServicesUC();
			$objServices->includeInstagramAPI();
						
			HelperInstaUC::putConnectWithInstagramButton();			
						
		}
		
		/**
		 * draw widget svg text
		 */
		private function drawWidgetSvg($setting){
			
			?>
			<div id="uc_widget_svg_holder" class="uc-wiget-svg-holder" style="display:none"></div>
			
			<span class="description">
				<?php _e("For the preview svg icon put preview_icon.svg file in the assets folder", "unlimited-elements-for-elementor")?>
			</span>
			
			<?php 
		}
		
		/**
		 * draw custom inputs
		 */
		protected function drawCustomInputs($setting){
			
			$customType = UniteFunctionsUC::getVal($setting, "custom_type");
			
			switch($customType){
				case "cols-layout":
					$this->drawColsLayoutInput($setting);
				break;
				case "fourinputs":
					$this->drawFourInputsInput($setting);
				break;
				case "inputwithsize":
					$this->drawInputWithSizes($setting);
				break;
				case "save_settings_tolibrary":
					$this->drawSaveGridPanelButton($setting);
				break;
				case "grid_panel_button":
					$this->drawGridPanelButton($setting);
				break;
				case "widget_svg_icon":
					$this->drawWidgetSvg($setting);
				break;
				case "instagram_connect":
					$this->drawConnectWithInstagramButton($setting);
				break;
			}
		}
		
		
		
		private function a_______IMAGE_AND_MP3______(){}
		
		/**
		 * 
		 * draw imaeg input:
		 * @param $setting
		 */
		protected function drawImageAddonInput($setting){
			
			$previewStyle = "";
			
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			$urlBase = UniteFunctionsUC::getVal($setting, "url_base");
			$isError = false;
			
			if(empty($urlBase)){
				$isError = true;
				$value = "";
				$setting["value"] = "";
			}
			
			$urlImage = "";
					
			if(!empty($value)){
								
				$urlFull = $urlBase.$value;
				
				$urlImage = $urlFull;
				
				$previewStyle = "";
			
				$operations = new UCOperations();
				try{
					
					$urlThumb = $operations->createThumbs($urlFull);
					
				}catch(Exception $e){
					$urlThumb = $value;
				}
			
				$urlThumbFull = HelperUC::URLtoFull($urlThumb);
				if(!empty($previewStyle))
					$previewStyle .= ";";
				
				$previewStyle .= "background-image:url('{$urlThumbFull}');";
			}
			
			if(!empty($previewStyle))
				$previewStyle = "style=\"{$previewStyle}\"";
			
			
			$class = $this->getInputClassAttr($setting, "", "unite-setting-image-input unite-input-image");
			
			$addHtml = $this->getDefaultAddHtml($setting);
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
			
			//set error related
			$addClass = "";
				
			$errorStyle = "style='display:none'";
			if($isError == true){		//set disabled
				$errorStyle = "";
				$addClass .= " unite-disabled";
				$previewStyle = "";
			}
			
			if(!empty($urlImage)){
				$addClass = "unite-image-exists";
			}
			
			$textPlaceholder = esc_html__("Image Url");
			
			
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
					
					<div class='unite-setting-image-error' <?php echo UniteProviderFunctionsUC::escAddParam($errorStyle)?>><?php esc_html_e("Please select assets path", "unlimited-elements-for-elementor")?></div>
															
				</div>
			<?php
		}
				
		
		/**
		 *
		 * draw imaeg input:
		 * @param $setting
		 */
		protected function drawMp3AddonInput($setting){
		
			$previewStyle = "display:none";
			
			$setting = $this->modifyImageSetting($setting);
		
			$value = UniteFunctionsUC::getVal($setting, "value");
			
			$urlBase = UniteFunctionsUC::getVal($setting, "url_base");
			
			$isError = false;
						
			if(empty($urlBase)){
				$isError = true;
				$value = "";
				$setting["value"] = "";
			}
			
			$class = $this->getInputClassAttr($setting, "", "unite-setting-mp3-input unite-input-image");
		
			$addHtml = $this->getDefaultAddHtml($setting);
		
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if(!empty($source))
				$addHtml .= " data-source='{$source}'";
			
			$buttonAddClass = "";
			$errorStyle = "style='display:none'";
			if($isError == true){
				$buttonAddClass = " button-disabled";
				$errorStyle = "'";
			}
			
			?>
				<div class="unite-setting-mp3">
					<input type="text" id="<?php echo esc_attr($setting["id"])?>" name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> value="<?php echo esc_attr($value)?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> />
					<a href="javascript:void(0)" class="unite-button-secondary unite-button-choose <?php echo esc_attr($buttonAddClass)?>"><?php esc_html_e("Choose", "unlimited-elements-for-elementor")?></a>
					<div class='unite-setting-mp3-error' <?php echo UniteProviderFunctionsUC::escAddParam($errorStyle)?>><?php esc_html_e("Please select assets path", "unlimited-elements-for-elementor")?></div>
				</div>
			<?php
		}
		
		
		/**
		 * override setting
		 */
		protected function drawImageInput($setting){
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if($source == "addon")
				$this->drawImageAddonInput($setting);
			else
				parent::drawImageInput($setting);
			
		}
		
		
		/**
		 * draw mp3 input
		 */
		protected function drawMp3Input($setting){
			
			//add source param
			$source = UniteFunctionsUC::getVal($setting, "source");
			if($source == "addon")
				$this->drawMp3AddonInput($setting);
			else
				parent::drawMp3Input($setting);
		}
		
		
		private function a________DRAW_DIMENTIONS_SETTING_______(){}
		
		
		/**
		 * draw row of dimention setting
		 */
		private function drawDimentionsSetting_drawRow($setting, $arrValues, $prefix = "", $prefixTitle=""){
			
			$name = UniteFunctionsUC::getVal($setting, "name");
			$isNoUnits = UniteFunctionsUC::getVal($setting, "no_units");
			$isNoUnits = UniteFunctionsUC::strToBool($isNoUnits);
			
			$top = UniteFunctionsUC::getVal($arrValues, $prefix."top");
			$bottom = UniteFunctionsUC::getVal($arrValues, $prefix."bottom");
			$left = UniteFunctionsUC::getVal($arrValues, $prefix."left");
			$right = UniteFunctionsUC::getVal($arrValues, $prefix."right");
			
			$units = UniteFunctionsUC::getVal($arrValues, "units");
			
			$optionUnitsPX = "selected";
			$optionPercent = "";
			
			if($units == "%"){
				$optionUnitsPX = "";
				$optionPercent = "selected";				
			}
			
			$isOutputNames = UniteFunctionsUC::getVal($setting, "output_names");
			$isOutputNames = UniteFunctionsUC::strToBool($isOutputNames);
			
			$nameTop = "";
			$nameBottom = "";
			$nameLeft = "";
			$nameRight = "";
			
			$posPrefix = $prefix;
			$drawUnits = false;
			
			if(empty($prefix)){
				$posPrefix = "";
				$prefix = "_";
				$drawUnits = true;
			} 
			
			if($isNoUnits === true)
				$drawUnits = false;
			
			if($isOutputNames === true){
				
				$nameTop = $name.$prefix."top";
				$nameBottom = $name.$prefix."bottom";
				$nameLeft = $name.$prefix."left";
				$nameRight = $name.$prefix."right";
			}
			
			if(!empty($prefixTitle))
				$prefixTitle .= " - ";
			
			
			
			?>
					<tr>
						<th>
							<?php echo $prefixTitle.__("Top","unlimited-elements-for-elementor")?>
						</th>
						<th>
							<?php echo $prefixTitle.__("Right","unlimited-elements-for-elementor")?>
						</th>
						<th>
							<?php echo $prefixTitle.__("Bottom","unlimited-elements-for-elementor")?>
						</th>
						<th>
							<?php echo $prefixTitle.__("Left","unlimited-elements-for-elementor")?>
						</th>
						<th>
							<?php if($drawUnits == true):?>
						
							<?php echo $prefixTitle.__("Units","unlimited-elements-for-elementor")?>
							
							<?php endif?>
						</th>
					</tr>
					<tr>
						<td>
							<input data-pos="<?php echo $posPrefix?>top" type="text" value="<?php echo $top?>" name="<?php echo $nameTop?>" class="unite-input-dimentions unite-input-dimentions-top">
						</td>
						<td>
							<input data-pos="<?php echo $posPrefix?>right" type="text" value="<?php echo $right?>" name="<?php echo $nameRight?>" class="unite-input-dimentions unite-input-dimentions-right">				
						</td>
						<td>
							<input data-pos="<?php echo $posPrefix?>bottom" type="text" value="<?php echo $bottom?>" name="<?php echo $nameBottom?>" class="unite-input-dimentions unite-input-dimentions-bottom">
						</td>
						<td>
							<input data-pos="<?php echo $posPrefix?>left" type="text" value="<?php echo $left?>" name="<?php echo $nameLeft?>" class="unite-input-dimentions unite-input-dimentions-left">
						</td>
						<td>
							<?php if($drawUnits == true):?>
							
							<select class="unite-setting-dimentions-select-units unite-input-dimentions" >
								<option value="px" <?php echo $optionUnitsPX?>>PX</option>
								<option value="%" <?php echo $optionPercent?>>%</option>
								<option value="em" >em</option>
								<option value="rem" >rem</option>
							</select>
							
							<?php endif?>
						</td>
					</tr>
				
				
			<?php 
		}
		
		/**
		 * draw dimentions setting
		 */
		protected function drawDimentionsSetting($setting){
			
			$arrValues = UniteFunctionsUC::getVal($setting, "value");
			$id = UniteFunctionsUC::getVal($setting, "id");
			$name = UniteFunctionsUC::getVal($setting, "name");

			//clear values
			$arrNames = array(
				"top","bottom","left","right","units",
				"tablet_top","tablet_bottom","tablet_left","tablet_right",
				"mobile_top","mobile_bottom","mobile_left","mobile_right"
			);
			
			foreach($arrNames as $key){
				
				if(isset($arrValues[$key]) == false)
					continue;
					
				$arrValues[$key] = trim($arrValues[$key]);
				$arrValues[$key] = htmlspecialchars($arrValues[$key]);
			}
			
			
			$isResponsive = UniteFunctionsUC::getVal($arrValues, "is_responsive");
			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);
						
			$setting["is_responsive"] = $isResponsive;
			if($isResponsive == true)
				$setting["responsive_type"] = "desktop";
			
			?>
			<div class="unite-setting-input-object" data-name="<?php echo $name?>" data-settingtype="dimentions">
				
				<table class="unite-settings-table-dimentions ">
					
					<?php $this->drawDimentionsSetting_drawRow($setting,$arrValues); ?>
					<?php 
						if($isResponsive == true){
							
							$setting["responsive_type"] = "tablet";
							
							$this->drawDimentionsSetting_drawRow($setting, $arrValues, "tablet_", "Tablet");
							
							$setting["responsive_type"] = "mobile";
							
							$this->drawDimentionsSetting_drawRow($setting, $arrValues, "mobile_", "Mobile");
						}
					?>
					
				</table>
				
			</div>
			
			<?php 
		}
		
		
		private function a________DRAW_FONTS_PANEL_______(){}
		
		
		/**
		 * get mobile placeholders
		 */
		protected function getMobilePlaceholders($valDesktop, $valTablet, $valMobile){
			
			$arrSizeValues = array();
			$arrSizeValues["tablet"] = $valTablet;
			$arrSizeValues["mobile"] = $valMobile;
			
			$parentValue = $valDesktop;
			
			foreach(GlobalsUC::$arrSizes as $size){
				
				$sizeValue = UniteFunctionsUC::getVal($arrSizeValues, $size);
				if($sizeValue === "")
					$sizeValue = $parentValue;
				
				$parentValue = $sizeValue;
				
				$arrSizeValues[$size] = $sizeValue;
			}
			
			return($arrSizeValues);
		}
		
		
		/**
		 * get slider font panel html
		 */
		private function getFontsPanelHtmlFields_slider($name, $text, $value, $placeholder = "", $inputID = null, $arrPlaceholderGroup = null, $addClass=""){
			 			
			 $classSection = "uc-fontspanel-details";			 
		     $classInput = "uc-fontspanel-field";
			 $br = "\n";
		     
		     $valueSlider = $value;
		     if($value === "" && !empty($placeholder))
		     	$valueSlider = $placeholder;
			 
		     $addParams = "";
		     if(!empty($arrPlaceholderGroup)){
		     	$jsonChildren = UniteFunctionsUC::jsonEncodeForHtmlData($arrPlaceholderGroup);
		     	$addParams = " data-placeholder_group='$jsonChildren'";
		     }
		     
		     if(!empty($placeholder)){
		     	$placeholder = htmlspecialchars($placeholder);
		     	$addParams .= " placeholder=\"{$placeholder}\"";
		     }
		     
		     if(!empty($inputID))
		     	$addParams .= " id=\"{$inputID}\"";
		     
		     
			 $html = "";
		     $html .= "<span class=\"{$classSection} uc-details-font-size {$addClass}\">".$br;
		     $html .= "			".$text."<br>".$br;
		     $html .= "<div class=\"unite-setting-range-wrapper\">".$br;
		     $html .= "		<input type=\"range\" min=\"8\" max=\"76\" step=\"1\" value=\"{$valueSlider}\">".$br;
	      	 $html .= "	<input type=\"text\" data-fieldname='{$name}' {$addParams} value=\"{$value}\" class=\"unite-setting-range {$classInput}\">	".$br;
	      	 $html .= "</div>".$br;
	      	 $html .= "</span>".$br;
			 
	      	 return($html);
		}
		
		
		/**
		 * get fonts panel html fields
		 */
		private function getFontsPanelHtmlFields($arrParams, $arrFontsData, $addTemplate = false){
			
			$arrData = HelperUC::getFontPanelData($addTemplate);
			
			if($addTemplate == true)
				$arrFontsTemplate = UniteCreatorPageBuilder::getPageFontNames(true);
			
			//get last param name
			end($arrParams);
			$lastName = key($arrParams);
						
			$html = "<div class='uc-fontspanel'>";
			
			$counter = 0;
			$random = UniteFunctionsUC::getRandomString(5);
			
			$br = "\n";
			foreach ($arrParams as $name => $title):
				
				 $counter++;
				 $IDSuffix = "{$random}_{$counter}";
			     $sectionID = "uc_fontspanel_section_{$IDSuffix}";
				 
			     $fontData = UniteFunctionsUC::getVal($arrFontsData, $name);
				 $isDataExists = !empty($fontData);
				 
				 if($addTemplate == true)
				 	$fontTemplate = UniteFunctionsUC::getVal($fontData, "template");
				 
				 $fontFamily = UniteFunctionsUC::getVal($fontData, "font-family");
				 $fontWeight = UniteFunctionsUC::getVal($fontData, "font-weight");
				 
				 //get size related fields
				 
				 $fontSize = UniteFunctionsUC::getVal($fontData, "font-size");
				 $fontSize = UniteFunctionsUC::getNumberFromString($fontSize);
				 
				 $fontSizeTablet = UniteFunctionsUC::getVal($fontData, "font-size-tablet");
				 $fontSizeTablet = UniteFunctionsUC::getNumberFromString($fontSizeTablet);
				 
				 $fontSizeMobile = UniteFunctionsUC::getVal($fontData, "font-size-mobile");
				 if(empty($fontSizeMobile))
				 	$fontSizeMobile = UniteFunctionsUC::getVal($fontData, "mobile-size");	//old way
				 
				 $fontSizeMobile = UniteFunctionsUC::getNumberFromString($fontSizeMobile);

				 $arrPlaceholders = $this->getMobilePlaceholders($fontSize, $fontSizeTablet, $fontSizeMobile);
				 
				 $placeholderSizeTablet = UniteFunctionsUC::getVal($arrPlaceholders, "tablet");
				 $placeholderSizeMobile = UniteFunctionsUC::getVal($arrPlaceholders, "mobile");
			     
				 
				 //---------
				 
				 $lineHeight = UniteFunctionsUC::getVal($fontData, "line-height");
				 $textDecoration = UniteFunctionsUC::getVal($fontData, "text-decoration");
				 $fontStyle = UniteFunctionsUC::getVal($fontData, "font-style");
				 
				 $color = UniteFunctionsUC::getVal($fontData, "color");
				 $color = htmlspecialchars($color);
				 
				 $customStyles = UniteFunctionsUC::getVal($fontData, "custom");
				 $customStyles = htmlspecialchars($customStyles);
				 
				 $classInput = "uc-fontspanel-field";
				 
				 if($addTemplate == true)
				 	$selectFontTemplate = HelperHtmlUC::getHTMLSelect($arrFontsTemplate, $fontTemplate,"data-fieldname='template' class='{$classInput}'", true, "not_chosen", esc_html__("---- Select Page Font----", "unlimited-elements-for-elementor"));
				 
				 $selectFontFamily = HelperHtmlUC::getHTMLSelect($arrData["arrFontFamily"],$fontFamily,"data-fieldname='font-family' class='{$classInput}'", true, "not_chosen", esc_html__("Select Font Family", "unlimited-elements-for-elementor"));
				 
				 $selectFontWeight = HelperHtmlUC::getHTMLSelect($arrData["arrFontWeight"],$fontWeight,"data-fieldname='font-weight' class='{$classInput}'", false, "not_chosen", esc_html__("Select", "unlimited-elements-for-elementor"));
				 
				 $selectLineHeight = HelperHtmlUC::getHTMLSelect($arrData["arrLineHeight"],$lineHeight,"data-fieldname='line-height' class='{$classInput}'", false, "not_chosen", esc_html__("Select", "unlimited-elements-for-elementor"));
				 $selectTextDecoration = HelperHtmlUC::getHTMLSelect($arrData["arrTextDecoration"],$textDecoration,"data-fieldname='text-decoration' class='{$classInput}'", false, "not_chosen", esc_html__("Select Text Decoration", "unlimited-elements-for-elementor"));
				 
				 $selectFontStyle = HelperHtmlUC::getHTMLSelect($arrData["arrFontStyle"],$fontStyle,"data-fieldname='font-style' class='{$classInput}'", false, "not_chosen", esc_html__("Select", "unlimited-elements-for-elementor"));
				 
				 $classSection = "uc-fontspanel-details";			 
				 
				 $htmlChecked = "";
				 $contentAddHtml = "style='display:none'";
				 
				 if($isDataExists == true){
				 	$htmlChecked = "checked ";
				 	$contentAddHtml = "";
				 }
				 
				 $html .= "<label class=\"uc-fontspanel-title\">".$br;
				 $html .=    "<input data-target=\"{$sectionID}\" {$htmlChecked}data-sectionname=\"{$name}\" type=\"checkbox\" onfocus='this.blur()' class='uc-fontspanel-toggle uc-fontspanel-toggle-{$name}' /> {$title}".$br;
				 $html .= " </label>";
				 
			     $html .= "<div id='{$sectionID}' class='uc-fontspanel-section' {$contentAddHtml}>	".$br;
			    	
			     $html .= "<div class=\"uc-fontspanel-line\">".$br;
			     
			     if($addTemplate == true){
			     	
				     $html .= "<span class=\"{$classSection} uc-details-font-select\">".$br;
				     $html .= " 			".esc_html__("From Page Font", "unlimited-elements-for-elementor")."<br>".$br;
				     $html .= "		".$selectFontTemplate.$br;
				     $html .= "</span>".$br;
			     }
			     
			     $html .= "<span class=\"{$classSection} uc-details-font-family\">".$br;
			     $html .= " 			".esc_html__("Font Family", "unlimited-elements-for-elementor")."<br>".$br;
			     $html .= "		".$selectFontFamily.$br;
			     $html .= "</span>".$br;
			     
			     $html .= "<span class=\"{$classSection} uc-details-font-weight\">".$br;
			     $html .= "			".esc_html__("Font Weight", "unlimited-elements-for-elementor")."<br>".$br;
			     $html .= "		".$selectFontWeight.$br;
			     $html .= "</span>".$br;
			     
			     			     
			     //put size related
			     $idFontSize = "fontfield_font_size_".$IDSuffix;
			     $idFontSizeTablet = "fontfield_font_size_tablet_".$IDSuffix;
			     $idFontSizeMobile = "fontfield_font_size_mobile_".$IDSuffix;
			     
			     $arrPlaceholdersGroup = array($idFontSize, $idFontSizeTablet, $idFontSizeMobile);
			     
			     $text = esc_html__("Font Size (px)", "unlimited-elements-for-elementor");
			     $html .= $this->getFontsPanelHtmlFields_slider("font-size", $text, $fontSize, "", $idFontSize, $arrPlaceholdersGroup, "uc-showin-desktop");
				
			     $text = esc_html__("Font Size - Tablet (px)", "unlimited-elements-for-elementor");
			     $html .= $this->getFontsPanelHtmlFields_slider("font-size-tablet", $text, $fontSizeTablet, $placeholderSizeTablet, $idFontSizeTablet, $arrPlaceholdersGroup, "uc-showin-tablet");
			     
			     $text = esc_html__("Font Size - Mobile (px)", "unlimited-elements-for-elementor");
			     $html .= $this->getFontsPanelHtmlFields_slider("font-size-mobile", $text, $fontSizeMobile, $placeholderSizeMobile, $idFontSizeMobile,null,"uc-showin-mobile");
			     
			     // --------- 
		      	 		      	 
			     $html .= "<span class=\"{$classSection} uc-details-line-height\">".$br;
			     $html .= "		".esc_html__("Line Height", "unlimited-elements-for-elementor")."<br>".$br;
			     $html .= "		".$selectLineHeight.$br;
			     $html .= "</span>".$br;
			     
			     $html .= "</div>".$br;	//line
			     
			     $html .= "<div class=\"uc-fontspanel-line\">".$br;
			     		      			      		
		      	 $html .= "<span class=\"{$classSection} uc-details-text-decoration\">".$br;
		      	 $html .= "	".esc_html__("Text Decoration", "unlimited-elements-for-elementor")."<br>".$br;
		      	 $html .= $selectTextDecoration;
		      	 $html .= "</span>".$br;
			     
		      	 $html .= "<span class=\"{$classSection} uc-details-color\">".$br;
		      	 $html .= "	".esc_html__("Color", "unlimited-elements-for-elementor")."<br>".$br;
		      	 $html .= "<div class='unite-color-picker-wrapper'>".$br;
		      	 $html .= "	<input type=\"text\" data-fieldname='color' value=\"{$color}\" class=\"unite-color-picker {$classInput}\">	".$br;
		      	 $html .= "</div>".$br;
		      	 $html .= "</span>".$br;
			     
		      	 /*
		      	 $html .= "<span class=\"{$classSection} uc-details-mobile-size\">".$br;
		      	 $html .= "	".esc_html__("Mobile Font Size", "unlimited-elements-for-elementor")."<br>".$br;
		      	 $html .= "	".$selectMobileSize.$br;
		      	 $html .= "</span>".$br;
		      	 */
		      	 
		      	 $html .= "<span class=\"{$classSection} uc-details-font-style\">".$br;
		      	 $html .= "	".esc_html__("Font Style", "unlimited-elements-for-elementor")."<br>".$br;
		      	 $html .= $selectFontStyle;
		      	 $html .= "</span>".$br;
		      	 
		      	 $html .= "<span class=\"{$classSection} uc-details-custom-styles\">".$br;
		      	 $html .= "	".esc_html__("Custom Styles", "unlimited-elements-for-elementor")."<br>".$br;
		      	 $html .= "	<input type=\"text\" data-fieldname='custom' value=\"{$customStyles}\" class=\"{$classInput}\">	".$br;
		      	 $html .= "</span>".$br;
		      	 
			     $html .= "	</div>".$br;    				      
			     $html .= "</div>".$br;
			    
			    if($name != $lastName) 
			    	$html .= "<div class='uc-fontspanel-sap'></div>";
			    
			    $html .= "<div class='unite-clear'></div>".$br;
			    
			endforeach;
					
			$html .= "</div>".$br;
			
			$html .= "<div class='unite-clear'></div>".$br;
			
			return($html);
		}


		/**
		 * get param array
		 */
		private function getFontsParams_getArrParam($type, $fieldName, $name, $title, $value, $options = null, $addParams = null){
			
			$paramName = "ucfont_{$name}__".$fieldName;
			
			$param = array();
			$param["name"] = $paramName;
			$param["type"] = $type;
			$param["title"] = $title;
			$param["value"] = $value;
			
			if(!empty($options)){
				$options = array_flip($options);
				$param["options"] = $options;
			}
			
			if(!empty($addParams))
				$param = array_merge($param, $addParams);
			
			return($param);
		}
		
				
		
		/**
		 * get fonts params
		 */
		public function getFontsParams($arrFontNames, $arrFontsData, $addonType = null, $addonName = null){
			
			$isElementor = false;
			if($addonType == "elementor")
				$isElementor = true;
			
			
			$arrData = HelperUC::getFontPanelData();
			$valueNotChosen = "not_chosen";
			
			
			if($isElementor == true){
				$arrFontStyle = array();
				$arrFontWeight = array();
				$arrFontSize = array();
				$arrMobileSize = array();
				$arrLineHeight = array();
				$arrTextDecoration = array();
				$arrFontFamily = array();
				$arrTabletSize = array();
			}else{
				
				$arrFontStyle = UniteFunctionsUC::arrayToAssoc($arrData["arrFontStyle"]);
				$arrFontWeight = UniteFunctionsUC::arrayToAssoc($arrData["arrFontWeight"]);
				$arrFontSize = UniteFunctionsUC::arrayToAssoc($arrData["arrFontSize"]);
				$arrMobileSize = UniteFunctionsUC::arrayToAssoc($arrData["arrMobileSize"]);
				$arrLineHeight = UniteFunctionsUC::arrayToAssoc($arrData["arrLineHeight"]);
				$arrTextDecoration = UniteFunctionsUC::arrayToAssoc($arrData["arrTextDecoration"]);
				
				
				$arrFontFamily = UniteFunctionsUC::addArrFirstValue($arrData["arrFontFamily"], "[Select Font Family]",$valueNotChosen);
				$arrFontStyle = UniteFunctionsUC::addArrFirstValue($arrFontStyle, "[Select Style]",$valueNotChosen);
				$arrFontWeight = UniteFunctionsUC::addArrFirstValue($arrFontWeight, "[Select Font Weight]",$valueNotChosen);
				$arrFontSize = UniteFunctionsUC::addArrFirstValue($arrFontSize, "[Select Font Size]",$valueNotChosen);
				$arrMobileSize = UniteFunctionsUC::addArrFirstValue($arrMobileSize, "[Select Mobile Size]",$valueNotChosen);			
				$arrTabletSize = UniteFunctionsUC::addArrFirstValue($arrMobileSize, "[Select Tablet Size]",$valueNotChosen);			
				$arrLineHeight = UniteFunctionsUC::addArrFirstValue($arrLineHeight, "[Select Line Height]",$valueNotChosen);
				$arrTextDecoration = UniteFunctionsUC::addArrFirstValue($arrTextDecoration, "[Select Text Decoration]",$valueNotChosen);
				
			}
			
			
			$arrParams = array();
			
			foreach($arrFontNames as $name => $title){

				$fontData = UniteFunctionsUC::getVal($arrFontsData, $name);
				$isDataExists = !empty($fontData);
								
				$fontFamily = UniteFunctionsUC::getVal($fontData, "font-family",$valueNotChosen);
				$fontWeight = UniteFunctionsUC::getVal($fontData, "font-weight",$valueNotChosen);
				$fontSize = UniteFunctionsUC::getVal($fontData, "font-size",$valueNotChosen);
				$lineHeight = UniteFunctionsUC::getVal($fontData, "line-height",$valueNotChosen);
				$textDecoration = UniteFunctionsUC::getVal($fontData, "text-decoration",$valueNotChosen);
				$mobileSize = UniteFunctionsUC::getVal($fontData, "mobile-size",$valueNotChosen);
				$fontStyle = UniteFunctionsUC::getVal($fontData, "font-style",$valueNotChosen);
				$color = UniteFunctionsUC::getVal($fontData, "color");
				$customStyles = UniteFunctionsUC::getVal($fontData, "custom");
				
				
				$paramType = UniteCreatorDialogParam::PARAM_CHECKBOX;
				if($isElementor == true)
					$paramType = UniteCreatorDialogParam::PARAM_HIDDEN;
				
				$arrFields = array();
				
				if($isElementor == true){
					
					$styleSelector = "uc-style-{$addonName}-{$name}";
					
					$styleSelector = HelperUC::convertTitleToHandle($styleSelector);
					
					$arrFields[] = $this->getFontsParams_getArrParam(UniteCreatorDialogParam::PARAM_HIDDEN, "style-selector", $name, "Style Selector", $styleSelector);
				}
				
				$fieldEnable = $this->getFontsParams_getArrParam(UniteCreatorDialogParam::PARAM_CHECKBOX, "fonts-enabled", $name, __("Enable Styles", "unlimited-elements-for-elementor"),null, null, array("is_checked"=>$isDataExists));
				
				$arrFields[] = $fieldEnable;
				
				//add typography field
				if($isElementor == true){
					$arrTypography = array();
					$arrTypography["selector1"] = ".".$styleSelector;
					
					$arrFields[] = $this->getFontsParams_getArrParam(UniteCreatorDialogParam::PARAM_TYPOGRAPHY, "typography", $name, "Typography", "", null, $arrTypography);
				}
				
				$nameEnabled = $fieldEnable["name"];

				$addParams = array();
				
				// 		conditions
				
				/*
					$condition = array();
					$condition[$nameEnabled] = "no";
					
					$addParams["elementor_condition"] = $condition;
				*/
				
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "font-family", 	$name, "Font Family", $fontFamily, $arrFontFamily);
				$arrFields[] = $this->getFontsParams_getArrParam(UniteCreatorDialogParam::PARAM_COLORPICKER, "color", $name, "Color", $color, null, $addParams);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "font-style", 	$name, "Font Style", $fontStyle, $arrFontStyle);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "font-weight", 	$name, "Font Weight", $fontWeight, $arrFontWeight);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "font-size", 	$name, "Font Size", $fontSize, $arrFontSize);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "mobile-size", 	$name, "Mobile Size", $mobileSize, $arrMobileSize);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "font-size-tablet", 	$name, "Tablet Size", $mobileSize, $arrTabletSize);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "line-height", 	$name, "Line Height", $lineHeight, $arrLineHeight);
				$arrFields[] = $this->getFontsParams_getArrParam($paramType, "text-decoration", 	$name, "Text Decoraiton", $textDecoration, $arrTextDecoration);
				$arrFields[] = $this->getFontsParams_getArrParam(UniteCreatorDialogParam::PARAM_TEXTAREA, "custom", 	$name, __("Custom Styles", "unlimited-elements-for-elementor"), $customStyles);
								
				
				$arrParams[$name] = $arrFields;
			}
			
			return($arrParams);
		}
		
		
		/**
		 * draw fonts panel - function for override
		 */
		protected function drawFontsPanel($setting){
			
			$name = $setting["name"];
			$id = $setting["id"];
			
			$arrParamsNames = $setting["font_param_names"];
			$arrFontsData = $setting["value"];
			
			$html = "<div id='{$id}' class='uc-setting-fonts-panel' data-name='{$name}'>";
			
			if(empty($arrParamsNames)){
				
				$html .= "<div class='uc-fontspanel-message'>";
				$html .= "Font overrides are disabled for this addon. If you would like to enable them please contact our support at <a href='https://unitecms.ticksy.com' target='_blank'>unitecms.ticksy.com</a>";
				$html .= "</div>";
				
			}else{
							
				$html .= self::TAB3."<div class='uc-addon-config-fonts'>".self::BR;
				$html .= "<h2>".esc_html__("Edit Fonts", "unlimited-elements-for-elementor")."</h2>";
				
				$isInsideGrid = UniteFunctionsUC::getVal($setting, "inside_grid");
				$addGridTemplate = UniteFunctionsUC::strToBool($isInsideGrid);
				
				$html .= $this->getFontsPanelHtmlFields($arrParamsNames, $arrFontsData, $addGridTemplate);
								
				$html .= self::TAB3."</div>";
			}
			
			$html .= "</div>";
			
			echo UniteProviderFunctionsUC::escCombinedHtml($html);
		}
		
		
		private function a_______DRAW_ITEMS_PANEL_______(){}
		
		
		/**
		 * draw fonts panel - function for override
		 */
		protected function drawItemsPanel($setting){
			
			$name = $setting["name"];
			$id = $setting["id"];
			$value = UniteFunctionsUC::getVal($setting, "value");
			$idDialog = $id."_dialog";
			
			$objManager = $setting["items_manager"];

			$source = UniteFunctionsUC::getVal($setting, "source");
			
			if(!empty($source))
				$objManager->setSource($source);
			
			?>
			<div id="<?php echo esc_attr($id)?>" class='uc-setting-items-panel'  data-name='<?php echo esc_attr($name)?>'>
			<?php 
				
				if($this->isSidebar == true): ?>
					<a href="javascript:void(0)" class="unite-button-secondary uc-setting-items-panel-button"><?php esc_html_e("Edit Widget Items", "unlimited-elements-for-elementor")?></a>
					
					<div id='<?php echo esc_attr($idDialog)?>' class='uc-settings-items-panel-dialog' title="<?php esc_html_e("Edit Addon Items", "unlimited-elements-for-elementor")?>" style='display:none'>
				<?php endif;
				
				$objManager->outputHtml();
				
				if($this->isSidebar == true):?>
					</div>
				<?php endif;
				
			?>
			</div>
			<?php 
		}
		
		
	}

