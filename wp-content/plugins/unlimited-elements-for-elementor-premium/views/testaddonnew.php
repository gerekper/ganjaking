<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorTestAddonNewView{


	/**
	 * constructor
	 */
	public function __construct(){

		$this->putHtml();
	}


	/**
	 * put html
	 */
	private function putHtml(){

		$addonID = UniteFunctionsUC::getGetVar("id","",UniteFunctionsUC::SANITIZE_ID);

		$addon = new UniteCreatorAddon();
		$addon->initByID($addonID);

		$objAddons = new UniteCreatorAddons();

		$addonTitle = $addon->getTitle();

		$isTestData1 = $addon->isTestDataExists(1);

		$slot1AddHtml = "";
		if($isTestData1 == false)
			$slot1AddHtml = "style='display:none'";

?>

<h1>Preview Widget - <?php echo $addonTitle?></h1>

<div class="uc-testaddon-new-panel">

	<a id="uc_testaddon_button_save" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Save", "unlimited-elements-for-elementor")?></a>
	<span id="uc_testaddon_loader_save" class="loader-text" style="display:none"><?php esc_html_e("saving...")?></span>

	<span id="uc_testaddon_slot1" class="uc-testaddon-slot" <?php echo UniteProviderFunctionsUC::escAddParam($slot1AddHtml)?>>
		<a id="uc_testaddon_button_restore" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Restore", "unlimited-elements-for-elementor")?></a>
		<span id="uc_testaddon_loader_restore" class="loader-text" style="display:none"><?php esc_html_e("loading...")?></span>

		<a id="uc_testaddon_button_delete" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Delete", "unlimited-elements-for-elementor")?></a>
		<span id="uc_testaddon_loader_delete" class="loader-text" style="display:none"><?php esc_html_e("deleting...")?></span>
	</span>

	|
	&nbsp;&nbsp;

	<a id="uc_testaddon_button_clear" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Clear", "unlimited-elements-for-elementor")?></a>

	<a id="uc_testaddon_button_check" href="javascript:void(0)" class="unite-button-secondary"><?php esc_html_e("Check", "unlimited-elements-for-elementor")?></a>

</div>

<div id="uc_preview_addon_wrapper" class="uc-preview-addon-wrapper" data-addonid="<?php echo esc_attr($addonID) ?>">

	<?php UniteProviderFunctionsUC::putInitHelperHtmlEditor(); ?>

	<div class="uc-preview-addon-left">
		<div id="uc_settings_loader" class="uc-settings-loader" style="display:none"><?php esc_html_e("Loading settings...", "unlimited-elements-for-elementor"); ?></div>
		<div id="uc_settings_wrapper" class="uc-settings-wrapper"></div>
	</div>

	<div class="uc-preview-addon-right">
		<div id="uc_preview_loader" class="uc-preview-loader" style="display:none"><?php esc_html_e("Loading preview...", "unlimited-elements-for-elementor"); ?></div>
		<div id="uc_preview_wrapper" class="uc-preview-wrapper"></div>
	</div>

</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		var objView = new UniteCreatorTestAddonNew();
		objView.init();
	});
</script>

		<?php

	}

}

new UniteCreatorTestAddonNewView();
