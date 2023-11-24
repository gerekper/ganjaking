<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

	
	$sapIDPrefix = "uc_tab_";
	
?>
<div class="content_wrapper unite-content-wrapper">	

	<div id="uc_tabs" class="uc-tabs">
		<?php 
			$isFirstTab = true;
			foreach($arrSaps as $sap):

				$isHidden = UniteFunctionsUC::getVal($sap, "hidden");
				$isHidden = UniteFunctionsUC::strToBool($isHidden);
							
				$sapName = $sap["name"];
				$sapID = $sapIDPrefix.$sapName;
				$class = "";
				if($isFirstTab == true)
					$class = "uc-tab-selected";
				
				$text = $sap["text"];
				
				$isFirstTab = false;
				
				$style = "";
				
				if($isHidden == true)
					$style = "style='display:none'";
					
		?>
		
			<a id="<?php echo esc_attr($sapID)?>_tablink" data-name="<?php echo esc_attr($sapName)?>" data-contentid="<?php echo esc_attr($sapID)?>" class="<?php echo esc_attr($class)?>" href="javascript:void(0)" onfocus="this.blur()" <?php echo $style?>> <?php echo esc_html($text)?></a>
			
		<?php endforeach?>
		
		<?php $this->drawAdditionalTabs(); ?>
		
		<div class="unite-clear"></div>
	</div>
	
	<div id="uc_tab_contents" class="uc-tabs-content-wrapper">
		
		<?php $objOutput->drawWrapperStart()?>
		
		<form name="<?php echo esc_attr($formID)?>" id="<?php echo esc_attr($formID)?>">
		
			<?php 
			$isFirstTab = true;
			
			foreach($arrSaps as $sapKey=>$sap):

			    $sapName = $sap["name"];
				
				$sapID = $sapIDPrefix.$sapName;
				
				$style = "style='display:none'";
				if($isFirstTab == true)
					$style = "";
				
				$isFirstTab = false;
				
			?>
			
			<div id="<?php echo esc_attr($sapID)?>" class="uc-tab-content" <?php echo UniteProviderFunctionsUC::escAddParam($style)?> >
				<?php
				
				$objOutput->drawSettings($sapKey);
				
				$this->drawSaveSettingsButton($sapID)?>
				
			</div>
			
			
			<?php endforeach?>
			
		</form>
		
		<?php $objOutput->drawWrapperEnd()?>
		
		
		<?php $this->drawAdditionalTabsContent() ?>
		
	</div>
	
</div>	

<script type="text/javascript">

	jQuery(document).ready(function(){
		
		var objAdmin = new UniteCreatorAdmin_GeneralSettings();
		objAdmin.initView("<?php echo UniteProviderFunctionsUC::escAddParam($this->saveAction)?>");
		
	});

</script>


