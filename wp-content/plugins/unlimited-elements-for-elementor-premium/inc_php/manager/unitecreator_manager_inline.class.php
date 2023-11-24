<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorManagerInline extends UniteCreatorManager{

	private $startAddon;
	private $itemsType;
	private $source = "";
	
	
	/**
	 * construct the manager
	 */
	public function __construct(){
		
		$this->type = self::TYPE_ITEMS_INLINE;
		
		$this->init();
	}
	
	/**
	 * set source
	 */
	public function setSource($source){
		
		$this->source = $source;
		$this->arrOptions["source"] = $source;
	}
	
	
	/**
	 * validate that the start addon exists
	 */
	private function validateStartAddon(){
		
		if(empty($this->startAddon))
			UniteFunctionsUC::throwError("The start addon not given");
		
	}
	
	
	/**
	 * init the data from start addon
	 */
	private function initStartAddonData(){
		
		$this->itemsType = $this->startAddon->getItemsType();
		
		//set init data
		$arrItems = $this->startAddon->getArrItemsForConfig();
		
		$strItems = "";
		if(!empty($arrItems)){
			$strItems = json_encode($arrItems);
			$strItems = htmlspecialchars($strItems);
		}
		
		$addHtml = " data-init-items=\"{$strItems}\" ";
		
		$this->setManagerAddHtml($addHtml);
		
	}
	
	
	/**
	 * set start addon
	 */
	public function setStartAddon($addon){
		$this->startAddon = new UniteCreatorAddon();	//just for code completion
		$this->startAddon = $addon;
		
		$this->initStartAddonData();
				
	}
	
	/**
	 * get category list
	 */
	protected function getCatList($selectCatID = null, $arrCats = null, $params = array()){
		
		$htmlCatList = "";
		
		return($htmlCatList);
	}
	
	
	/**
	 * get single item menu
	 */
	protected function getMenuSingleItem(){
		
		$arrMenuItem = array();
		$arrMenuItem["edit_item"] = esc_html__("Edit Item","unlimited-elements-for-elementor");
		$arrMenuItem["remove_items"] = esc_html__("Delete","unlimited-elements-for-elementor");
		$arrMenuItem["duplicate_items"] = esc_html__("Duplicate","unlimited-elements-for-elementor");
		
		return($arrMenuItem);
	}

	/**
	 * get multiple items menu
	 */
	protected function getMenuMulitipleItems(){
		$arrMenuItemMultiple = array();
		$arrMenuItemMultiple["remove_items"] = esc_html__("Delete","unlimited-elements-for-elementor");
		$arrMenuItemMultiple["duplicate_items"] = esc_html__("Duplicate","unlimited-elements-for-elementor");
		return($arrMenuItemMultiple);
	}
	
	
	/**
	 * get item field menu
	 */
	protected function getMenuField(){
		$arrMenuField = array();
		$arrMenuField["add_item"] = esc_html__("Add Item","unlimited-elements-for-elementor");
		$arrMenuField["select_all"] = esc_html__("Select All","unlimited-elements-for-elementor");
		
		return($arrMenuField);
	}
	
	
	/**
	 * put items buttons
	 */
	protected function putItemsButtons(){
		
		$this->validateStartAddon();
		
		$itemType = $this->startAddon->getItemsType();
		
		$buttonClass = "unite-button-primary button-disabled uc-button-item uc-button-add";
		
		//put add item button according the type
		switch($itemType){
			default:
			case UniteCreatorAddon::ITEMS_TYPE_DEFAULT:
			?>
 				<a data-action="add_item" type="button" class="<?php echo esc_attr($buttonClass)?>"><?php esc_html_e("Add Item","unlimited-elements-for-elementor")?></a>
			<?php 
			break;
			case UniteCreatorAddon::ITEMS_TYPE_IMAGE:
			?>
 				<a data-action="add_images" type="button" class="<?php echo esc_attr($buttonClass)?>"><?php esc_html_e("Add Images","unlimited-elements-for-elementor")?></a>
			<?php 
			break;
			case UniteCreatorAddon::ITEMS_TYPE_FORM:
				?>
 				<a data-action="add_form_item" type="button" class="<?php echo esc_attr($buttonClass)?>"><?php esc_html_e("Add Form Item","unlimited-elements-for-elementor")?></a>
 				<?php
			break;
		}
		
		?>
	 		<a data-action="select_all_items" type="button" class="unite-button-secondary button-disabled uc-button-item uc-button-select" data-textselect="<?php esc_html_e("Select All","unlimited-elements-for-elementor")?>" data-textunselect="<?php esc_html_e("Unselect All","unlimited-elements-for-elementor")?>"><?php esc_html_e("Select All","unlimited-elements-for-elementor")?></a>
	 		<a data-action="duplicate_items" type="button" class="unite-button-secondary button-disabled uc-button-item"><?php esc_html_e("Duplicate","unlimited-elements-for-elementor")?></a>
	 		<a data-action="remove_items" type="button" class="unite-button-secondary button-disabled uc-button-item"><?php esc_html_e("Delete","unlimited-elements-for-elementor")?></a>
	 		<a data-action="edit_item" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php esc_html_e("Edit Item","unlimited-elements-for-elementor")?> </a>
		<?php 
	}
	
	
	/**
	 * put add edit item dialog
	 */
	private function putAddEditDialog(){
		
		$isLoadByAjax = $this->startAddon->isEditorItemsAttributeExists();
		
		
		$addHtml = "";
		if($isLoadByAjax == true){
			
			$addonID = $this->startAddon->getID();
			$addonID = esc_attr($addonID);
			$addHtml = "data-initbyaddon=\"{$addonID}\"";
		}
		
		?>
			<div title="<?php esc_html_e("Edit Item","unlimited-elements-for-elementor")?>" class="uc-dialog-edit-item" style="display:none">
				<div class="uc-item-config-settings" autofocus="true" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml);?>>
					
					<?php if($isLoadByAjax == false): 
						
						if($this->startAddon)
						$this->startAddon->putHtmlItemConfig();
					?>
					<?php else:	 //load by ajax?>
						
						<div class="unite-dialog-loader-wrapper">
							<div class="unite-dialog-loader"><?php esc_html_e("Loading Settings", "unlimited-elements-for-elementor")?>...</div>
						</div>
						
					<?php endif?>
					
				</div>
			</div>
		<?php 
	}
	
	
	/**
	 * put form dialog
	 */
	protected function putFormItemsDialog(){
		
		$objDialogParam = new UniteCreatorDialogParam();
		$objDialogParam->init(UniteCreatorDialogParam::TYPE_FORM_ITEM, $this->startAddon);
		$objDialogParam->outputHtml();
		
	}
	
	
	/**
	 * put additional html here
	 */
	protected function putAddHtml(){
				
		if($this->itemsType == UniteCreatorAddon::ITEMS_TYPE_FORM)
			$this->putFormItemsDialog();
		else
			$this->putAddEditDialog();
		
	}
	
	/**
	 * before init
	 */
	protected function beforeInit($addonType){
		$this->hasCats = false;
	}
	
	
	
}