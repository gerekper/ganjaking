<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


if($this->showHeader)
	$this->putHeaderHtml();
else
	require HelperUC::getPathTemplate("header_missing");

$slot1AddHtml = "";
if($this->isDataExists == false)
	$slot1AddHtml = "style='display:none'";


$styleShow = "";
$styleHide = "style='display:none'";

$strOptions = UniteFunctionsUC::jsonEncodeForHtmlData($arrOptions);

$urlBack = HelperUC::getViewUrl_Addons($addonType);
if(!empty($objAddonType->addonView_urlBack))
	$urlBack = $objAddonType->addonView_urlBack;

?>

<div id="uc_addondefaults_wrapper" class="uc-addondefaults-wrapper" data-options="<?php echo esc_attr($strOptions)?>">

<?php if($this->showToolbar):?>

<div class="uc-addondefaults-panel">
		
		<div class="uc-panel-save-wrapper">
			<a id="uc_addondefaults_button_save" href="javascript:void(0)" class="unite-button-primary"><?php esc_html_e("Save Defaults", "unlimited-elements-for-elementor")?></a>
			<span id="uc_addondefaults_loader_save" class="loader-text" style="display:none"><?php esc_html_e("saving...")?></span>
		</div>
				
		<a id="uc_button_preview" href="javascript:void(0)" class="unite-button-secondary" <?php echo UniteProviderFunctionsUC::escAddParam($isPreviewMode?$styleHide:$styleShow)?>><?php esc_html_e("To Preview", "unlimited-elements-for-elementor")?></a>
		<a id="uc_button_close_preview" href="javascript:void(0)" class="unite-button-secondary" <?php echo UniteProviderFunctionsUC::escAddParam($isPreviewMode?$styleShow:$styleHide)?>><?php esc_html_e("Hide Preview", "unlimited-elements-for-elementor")?></a>
		<span class="hor_sap10"></span>
				
		<a id="uc_button_preview_tab" href="javascript:void(0)" class="unite-button-secondary uc-button-cat-sap"><?php esc_html_e("Preview New Tab", "unlimited-elements-for-elementor")?></a>
		
		<span class="hor_sap30"></span>
		
		<a href="<?php echo esc_attr($urlEditAddon)?>" class="unite-button-secondary" ><?php esc_html_e("Edit This Widget", "unlimited-elements-for-elementor")?></a>
		<span class="hor_sap15"></span>
		
		<a class="unite-button-secondary uc-button-cat-sap" href="<?php echo esc_attr($urlBack)?>"><?php esc_html_e("Back to Widgets List", "unlimited-elements-for-elementor");?></a>
		
</div>

<?php endif; ?>

<form name="form_addon_defaults">

<?php 

	//put helper editor if needed
	
	if($isNeedHelperEditor)
		UniteProviderFunctionsUC::putInitHelperHtmlEditor();
	
    $addonConfig->putHtmlFrame(); 
?>

</form>

</div>

<script type="text/javascript">

	jQuery(document).ready(function(){
		
		var objAddonDefaultsView = new UniteCreatorAddonDefaultsAdmin();
		objAddonDefaultsView.init();
		
		<?php if($isPreviewMode == true):?>
		jQuery("#uc_button_preview").trigger("click");
		<?php endif?>
		
	});
	
		
</script>

