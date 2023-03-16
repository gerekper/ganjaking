<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

$bottomLineClass = "";
if($view == "layout")
    $bottomLineClass = " unite-position-right";

 ob_start();
 
 self::requireView($view);
 
 $htmlView = ob_get_contents();
 
 ob_end_clean();
    
 $htmlClassAdd = "";
 if(!empty($view)){
 	$htmlClassAdd = " unite-view-{$view}";
 	$bottomLineClass .= " unite-view-{$view}";
 }
 
?>

<?php HelperHtmlUC::putGlobalsHtmlOutput(); ?>

	<script type="text/javascript">
		var g_view = "<?php echo self::$view?>";
	</script>

<?php HelperHtmlUC::putInternalAdminNotices()?>


<div id="viewWrapper" class="unite-view-wrapper unite-admin unite-inputs <?php echo $htmlClassAdd?>">

<?php
	echo UniteProviderFunctionsUC::escCombinedHtml($htmlView);
	
	//include provider view if exists
	$filenameProviderView = GlobalsUC::$pathProviderViews.$view.".php";
	if(file_exists($filenameProviderView))
		require_once($filenameProviderView);
?>

</div>

<?php 
	$filepathProviderMasterView = GlobalsUC::$pathProviderViews."master_view.php";
	if(file_exists($filepathProviderMasterView))
		require_once $filepathProviderMasterView;
		
?>

<?php if(GlobalsUC::$blankWindowMode == false):?>

<?php HelperHtmlUC::putFooterAdminNotices() ?>


<div id="uc_dialog_version" title="<?php esc_html_e("Version Release Log. Current Version: ".UNLIMITED_ELEMENTS_VERSION." ", "unlimited-elements-for-elementor")?>" style="display:none;">
	<div class="unite-dialog-inside">
		<div id="uc_dialog_version_content" class="unite-dialog-version-content">
			<div id="uc_dialog_loader" class="loader_text"><?php esc_html_e("Loading...", "unlimited-elements-for-elementor")?></div>
		</div>
	</div>
</div>

<div class="unite-clear"></div>

<div class="unite-plugin-version-line unite-admin <?php echo esc_attr($bottomLineClass)?>">
	<?php UniteProviderFunctionsUC::putFooterTextLine() ?>
	<?php esc_html_e("Plugin version", "unlimited-elements-for-elementor")?> <?php echo UNLIMITED_ELEMENTS_VERSION?>
	<?php if(defined("UNLIMITED_ELEMENTS_UPRESS_VERSION"))
				esc_html_e("upress", "unlimited-elements-for-elementor")
	?>
	,
	<a id="uc_version_link" href="javascript:void(0)" class="unite-version-link">
		<?php esc_html_e("view change log", "unlimited-elements-for-elementor")?>
	</a>
	
	
	<?php UniteProviderFunctionsUC::doAction(UniteCreatorFilters::ACTION_BOTTOM_PLUGIN_VERSION)?>
	
</div>

<?php endif?>
