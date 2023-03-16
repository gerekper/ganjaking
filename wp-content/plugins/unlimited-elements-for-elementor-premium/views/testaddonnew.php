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
		
?>

<h1>Preview Widget - <?php echo $addonTitle?></h1>

<div id="uc_preview_addon_wrapper" class="uc-preview-addon-wrapper" data-addonid="<?php echo esc_attr($addonID)?>">

<div class="uc-preview-addon-left" >

<h2>Settings</h2>

<div id="uc_settings_loader" class="uc-settings-loader" style="display:none">Loading Settings...</div>

<div id="uc_settings_container" class="uc-preview-addons__settings-container"></div>

	
</div>

<div class="uc-preview-addon-right" >

	<h2>Preview</h2>
	
	<div id="uc_preview_loader" class="uc-preview-addon-loader" style="display:none">Loading Preview...</div>
	
	<div id="uc_preview_wrapper"  class="uc-preview-wrapper">
				
	</div>
	
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

