<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteSettingsOutputSidebarUC extends UniteCreatorSettingsOutput{

    private $isAccordion = true;
    private $accordionItemsSpaceBetween = 0;	//space between accoridion items
    private $showTips = true;
    private $showSapTitle = true;


    /**
     * constuct function
     */
    public function __construct(){
        $this->isParent = true;
        self::$serial++;

        $this->wrapperID = "unite_settings_sidebar_output_".self::$serial;
        $this->settingsMainClass = "unite-settings-sidebar";

        $this->showDescAsTips = false;
        $this->setShowSaps(true, self::SAPS_TYPE_ACCORDION);

        $this->isSidebar = true;

    }



		/**
		 * draw wrapper end after settings
		 */
		protected function drawSettingsAfter(){

			?></ul><?php

			parent::drawSettingsAfter();
		}

		/**
		 * get options override
		 * add accordion space
		 */
		protected function getOptions(){

			$arrOptions = parent::getOptions();
			$arrOptions["accordion_sap"] = $this->accordionItemsSpaceBetween;

			return($arrOptions);
		}

		/**
		 * set draw options before draw
		 */
		protected function setDrawOptions(){

			$numSaps = $this->settings->getNumSaps();
			if($numSaps <= 1)
				$this->showSapTitle = false;

		}


		/**
		 * draw before settings row
		 */
		protected function drawSettings_before(){

			parent::drawSettings_before();

		  ?>
		  	  <ul class="unite-list-settings">
		  <?php

		}

		/**
		 * draw responsive picker
		 */
		private function drawResponsivePicker($selectedType){

			$devices = array(
				"desktop" => array(
					"title" => __("Desktop", "unlimited-elements-for-elementor"),
					"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 10"><path d="M3.5 10.5h5M6 7.5v3M11.5.5H.5v7h11v-7Z" /></svg>',
				),
				"tablet" => array(
					"title" => __("Tablet", "unlimited-elements-for-elementor"),
					"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 12"><path d="M2.5 9.5h5M8.5.5h-7a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1Z" /></svg>',
				),
				"mobile" => array(
					"title" => __("Mobile", "unlimited-elements-for-elementor"),
					"icon" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 12"><path d="M2.5 9.5h3M6.5.5h-5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1Z" /></svg>',
				),
			);

			?>
			<select class="unite-responsive-picker">
				<?php foreach($devices as $type => $device): ?>
					<option
						value="<?php esc_attr_e($type); ?>"
						data-content="<?php esc_attr_e('<div class="unite-responsive-picker-item uc-tip" title="' . esc_attr($device["title"]) . '" data-tipsy-gravity="w">' . $device["icon"] . '</div>'); ?>"
						<?php echo $type === $selectedType ? "selected" : ""; ?>
					>
						<?php esc_html_e($device["title"]); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
		}

		/**
		 * draw units picker
		 */
		private function drawUnitsPicker($units, $selectedUnit){

			$defaultUnit = reset($units);
			$initialUnit = $selectedUnit ?: $defaultUnit;

			?>
			<select
				class="unite-units-picker <?php echo count($units) < 2 ? 'unite-hidden' : ''; ?>"
				data-default="<?php esc_attr_e($defaultUnit); ?>"
				data-initval="<?php esc_attr_e($initialUnit); ?>"
			>
				<?php foreach($units as $unit): ?>
					<option
						value="<?php esc_attr_e($unit); ?>"
						data-content="<?php esc_attr_e('<div class="unite-units-picker-item">' . $unit . '</div>'); ?>"
						<?php echo $unit === $selectedUnit ? "selected" : ""; ?>
					>
						<?php esc_html_e($unit); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
		}

		/**
		 * draw settings row
		 * @param array $setting
		 * @param string $mode
		 */
		protected function drawSettingRow($setting, $mode = ""){

			$addAttr = "";
			$baseClass = "unite-setting-row";

			$id = UniteFunctionsUC::getVal($setting, "id");
			$type = UniteFunctionsUC::getVal($setting, "type");
			$text = UniteFunctionsUC::getVal($setting, "text");
			$description = UniteFunctionsUC::getVal($setting, "description");

			$toDrawText = true;

			$attribsText = UniteFunctionsUC::getVal($setting, "attrib_text");
			if(empty($attribsText) && empty($text))
				$toDrawText = false;

			$labelBlock = UniteFunctionsUC::getVal($setting, "label_block");
			$labelBlock = UniteFunctionsUC::strToBool($labelBlock);

			if($labelBlock === false)
				$baseClass .= " uc-inline";

			$responsiveId = UniteFunctionsUC::getVal($setting, "responsive_id");
			$responsiveType = UniteFunctionsUC::getVal($setting, "responsive_type");
			$isResponsive = UniteFunctionsUC::getVal($setting, "is_responsive");
			$isResponsive = UniteFunctionsUC::strToBool($isResponsive);

			if($isResponsive === true)
				$addAttr .= " data-responsive-id=\"$responsiveId\" data-responsive-device=\"$responsiveType\"";

			$tabsId = UniteFunctionsUC::getVal($setting, "tabs_id");
			$tabsValue = UniteFunctionsUC::getVal($setting, "tabs_value");

			if (empty($tabsId) === false && empty($tabsValue) === false)
				$addAttr .= " data-tabs-id=\"$tabsId\" data-tabs-value=\"$tabsValue\"";

			$units = UniteFunctionsUC::getVal($setting, "units");
			$unitsSelected = UniteFunctionsUC::getVal($setting, "units_selected");

			$rowClass = $this->drawSettingRow_getRowClass($setting, $baseClass);

			?>
			<li
				id="<?php esc_attr_e($id); ?>_row"
				<?php echo UniteProviderFunctionsUC::escAddParam($rowClass); ?>
				<?php echo UniteProviderFunctionsUC::escAddParam($addAttr); ?>
				data-type="<?php esc_attr_e($type); ?>"
			>

				<div class="unite-setting-field">

					<?php if($toDrawText === true): ?>
						<div class="unite-setting-text-wrapper">
							<div id="<?php echo esc_attr($id); ?>_text" class='unite-setting-text' <?php echo UniteProviderFunctionsUC::escAddParam($attribsText); ?>>
								<?php echo esc_html($text); ?>
							</div>
							<?php if($isResponsive === true): ?>
								<?php $this->drawResponsivePicker($responsiveType); ?>
							<?php endif; ?>
							<?php if(empty($units) === false): ?>
								<?php $this->drawUnitsPicker($units, $unitsSelected); ?>
							<?php endif; ?>
						</div>
					<?php endif ?>

					<?php if(!empty($addHtmlBefore)): ?>
						<div class="unite-setting-addhtmlbefore"><?php echo UniteProviderFunctionsUC::escAddParam($addHtmlBefore); ?></div>
					<?php endif; ?>

					<div class="unite-setting-input">
						<?php $this->drawInputs($setting); ?>
					</div>

				</div>

				<?php if(!empty($description)): ?>
					<div class="unite-setting-helper">
						<?php echo $description; ?>
					</div>
				<?php endif; ?>

			</li>

			<?php
		}


		/**
		 * draw text row
		 * @param unknown_type $setting
		 */
		protected function drawTextRow($setting){

		    //set cell style
		    $cellStyle = "";
		    if(isset($setting["padding"]))
		        $cellStyle .= "padding-left:".$setting["padding"].";";

	        if(!empty($cellStyle))
	            $cellStyle="style='$cellStyle'";

            //set style
            $label = UniteFunctionsUC::getVal($setting, "label");

             $rowClass = $this->drawSettingRow_getRowClass($setting);

             $classAdd = UniteFunctionsUC::getVal($setting, UniteSettingsUC::PARAM_CLASSADD);

             if(!empty($classHidden))
				$classAdd .= $classHidden;

			$isHeading = UniteFunctionsUC::getVal($setting, "is_heading");
			$isHeading = UniteFunctionsUC::strToBool($isHeading);

			if($isHeading == true){
				$classAdd .= " unite-settings-static-text__heading";
			}

             if(!empty($classAdd))

                 	$classAdd = " ".$classAdd;
                    $settingID = $setting["id"];

			?>
    			  	<li id="<?php echo esc_attr($settingID)?>_row" <?php echo UniteProviderFunctionsUC::escAddParam($rowClass)?>>

    					<?php if(!empty($label)):?>
    					<span class="unite-settings-text-label">
    						<?php echo esc_html($label)?>
    					</span>
    					<?php endif?>

    					<span class="unite-settings-static-text<?php echo esc_attr($classAdd)?>"><?php echo esc_html($setting["text"])?></span>

    				</li>

			<?php
		}


		/**
		 * draw sap before override
		 * @param  $sap
		 */
		protected function drawSapBefore($sap, $key){

		    //set class
		    $class = "unite-postbox";
		    if(!empty($this->addClass))
		        $class .= " ".$this->addClass;

		    $tab = UniteFunctionsUC::getVal($sap, "tab");

		    if(empty($tab))
		    	$tab = UniteSettingsUC::TAB_CONTENT;

				if($this->isAccordion == false){
					$class .= " unite-no-accordion";
				}

				//set text and icon class
				$text = UniteFunctionsUC::getVal($sap, "text");
				$classIcon = UniteFunctionsUC::getVal($sap, "icon");
				$text = esc_html__($text,"unlimited-elements-for-elementor");

				$classIcon = null;	//disable icons for now

				//title style
				$styleTitle = "";

				if(!empty($styleTitle)){
					$styleTitle = esc_attr($styleTitle);
					$styleTitle = " style='$styleTitle'";
				}

				$isHidden = UniteFunctionsUC::getVal($sap, "hidden");
				$isHidden = UniteFunctionsUC::strToBool($isHidden);

				if($isHidden == true)
					$class .= " unite-setting-hidden";

				$name = UniteFunctionsUC::getVal($sap, "name");

				//generate name if missing
				if(empty($name))
					$name = "unnamed_".UniteFunctionsUC::getRandomString();


				$sapID = $this->idPrefix."ucsap_{$name}";

			?>
			<div id="<?php esc_attr_e($sapID)?>" class="<?php esc_attr_e($class); ?>" data-tab="<?php esc_attr_e($tab); ?>">

				<?php if($this->showSapTitle == true): ?>

					<div class="unite-postbox-title" <?php echo UniteProviderFunctionsUC::escAddParam($styleTitle); ?>>

					<?php if(!empty($classIcon)):?>
						<i class="unite-postbox-icon <?php echo esc_attr($classIcon); ?>"></i>
					<?php endif; ?>

						<span><?php echo esc_html($text); ?></span>

						<?php if($this->isAccordion == true): ?>
							<div class="unite-postbox-arrow"></div>
						<?php endif; ?>

					</div>
				<?php endif; ?>

				<div class="unite-postbox-inside">
			<?php

		}


		/**
		 * draw sap after
		 */
		protected function drawSapAfter(){

		?>
			</div>
		</div>
		<?php
		}


		/**
		 * draw hr row
		 */
		protected function drawHrRow($setting){

             $rowClass = $this->drawSettingRow_getRowClass($setting);

             $settingID = $setting["id"];

			?>
    			  	<li id="<?php echo esc_attr($settingID)?>_row" <?php echo UniteProviderFunctionsUC::escAddParam($rowClass)?>>

    					<hr id="<?php echo esc_attr($settingID)?>">

    				</li>
			<?php
		}


	}
?>
