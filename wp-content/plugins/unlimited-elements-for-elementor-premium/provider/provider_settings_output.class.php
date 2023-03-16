<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

class UniteSettingsOutputUC extends UniteSettingsOutputUCWork{

	
	/**
	 * draw editor input
	 */
	protected function drawEditorInput($setting){
		
		$settingsID = UniteFunctionsUC::getVal($setting, "id");
		$name = UniteFunctionsUC::getVal($setting, "name");
		$class = self::getInputClassAttr($setting,"","",false);
		
		$editorParams = array();
		$editorParams['media_buttons'] = true;
		$editorParams['wpautop'] = false;
		$editorParams['editor_height'] = 200;
		$editorParams['textarea_name'] = $name;
		
		if(!empty($class))
			$editorParams['editor_class'] = $class;
		
		$addHtml = $this->getDefaultAddHtml($setting);
		
		$class = $this->getInputClassAttr($setting);
		
		$value = UniteFunctionsUC::getVal($setting, "value");
		
		?>
		<div class="unite-editor-setting-wrapper unite-editor-wp" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?>>
		<?php 
			wp_editor($value, $settingsID, $editorParams);
		?>
		</div>
		<?php 
	}
	
	
	/**
	 * draw icon picker input:
	 * @param $setting
	 */
	protected function drawPostPickerInput($setting){
		
		$value = UniteFunctionsUC::getVal($setting, "value");
		
		if(!empty($value))
			$value = (int)$value;
		
		$postTitle = "";
		if(!empty($value)){
			
			$post = get_post($value);
			
			if(!empty($post))
				$postTitle = $post->post_title;
			else{
				$value = "";
			}
		}
		
		$class = $this->getInputClassAttr($setting, "", "unite-setting-post-picker");
		
		$placeholder = UniteFunctionsUC::getVal($setting, "placeholder");
		if(empty($placeholder))
			$placeholder = __("Please type post title", "unlimited-elements-for-elementor");
		
		
		$addHtml = $this->getDefaultAddHtml($setting);
		
		?>
			<div class="unite-settings-postpicker-wrapper unite-setting-input-object" data-settingtype="post" id="<?php echo esc_attr($setting["id"])?>"  name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?>>
				<select type="text" data-name="<?php echo esc_attr($setting["name"])?>" <?php echo UniteProviderFunctionsUC::escAddParam($class)?> data-placeholder="<?php echo esc_attr($placeholder)?>" data-postid="<?php echo esc_attr($value)?>" data-posttitle="<?php echo esc_attr($postTitle)?>"  style="width:220px;" ></select>				
			</div>
		<?php
		
	}
	
	
}