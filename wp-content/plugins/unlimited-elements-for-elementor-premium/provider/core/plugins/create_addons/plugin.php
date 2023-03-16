<?php
/**
 * @package Addon Creator for Blox
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2017 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');


class AddonLibraryCreatorPluginUC extends UniteCreatorPluginBase{
	
	protected $extraInitParams = array();
	
	private $version = "1.0.3";
	private $pluginName = "create_addons";
	private $title = "Addon Creator for Blox";
	private $description = "Give the ability to create, duplicate and export custom addons";
	private $objAddonType, $addonType, $textSingle, $textPlural;
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$pathPlugin = dirname(__FILE__)."/";
				
		parent::__construct($pathPlugin);
		
		$this->extraInitParams["silent_mode"] = true;
		
		$this->textSingle = "Addon";
		$this->textPlural = "Addons";
		
		$this->init();
	}
	
	
	/**
	 * add menu items to manager single menu
	 */
	public function addItems_managerMenuSingle($arrMenu){
		
		$arrNewItems = array();
		$arrNewItems[] = array("key"=>"duplicate_item",
							   "text"=>esc_html__("Duplicate","unlimited-elements-for-elementor"),
							   "insert_after"=>"remove_item");
		
		$arrNewItems[] = array("key"=>"export_addon",
							   "text"=>esc_html__("Export ","unlimited-elements-for-elementor").$this->textSingle,
							   "insert_after"=>"test_addon_blank");
		
		$arrMenu = UniteFunctionsUC::insertToAssocArray($arrMenu, $arrNewItems);
		
		
		return($arrMenu);
	}

	/**
	 * add menu items to manager single menu
	 */
	public function addItems_managerMenuMultiple($arrMenu){
	
		$arrNewItems = array();
		$arrNewItems[] = array("key"=>"duplicate_item",
				"text"=>esc_html__("Duplicate","unlimited-elements-for-elementor"),
				"insert_after"=>"bottom");
		
		$arrMenu = UniteFunctionsUC::insertToAssocArray($arrMenu, $arrNewItems);
	
	
		return($arrMenu);
	}
	
	
	/**
	 * add items to menu field
	 */
	public function addItems_managerMenuField($arrMenu){
		
		$arrNewItems[] = array("key"=>"add_addon",
				"text"=>esc_html__("Add Widget","unlimited-elements-for-elementor"),
				"insert_after"=>"top");
		
		$arrMenu = UniteFunctionsUC::insertToAssocArray($arrMenu, $arrNewItems);
		
		return($arrMenu);
	}

	/**
	 * add items to menu field
	 */
	public function addItems_managerMenuCategory($arrMenu){
	
		$arrNewItems[] = array("key"=>"export_cat_addons",
				"text"=>esc_html__("Export Category ","unlimited-elements-for-elementor").$this->textPlural,
				"insert_after"=>"bottom");
		
		$arrMenu = UniteFunctionsUC::insertToAssocArray($arrMenu, $arrNewItems);
		
		return($arrMenu);
	}
	
	
	/**
	 * draw item buttons 1
	 */
	public function drawItemButtons2(){
		?>
		
	 			<a data-action="duplicate_item" type="button" class="unite-button-secondary button-disabled uc-button-item"><?php esc_html_e("Duplicate","unlimited-elements-for-elementor")?></a>
		
		<?php 
	}
	
	/**
	 * draw item buttons 1
	 */
	public function drawItemButtons3(){
		
		$textExport = esc_html__("Export ", "unlimited-elements-for-elementor").$this->textSingle;
		
		?>
	 		
	 		<a data-action="export_addon" type="button" class="unite-button-secondary button-disabled uc-button-item uc-single-item"><?php echo UniteProviderFunctionsUC::escAddParam($textExport)?></a>
		
		<?php 
	}

	
	
	/**
	* edit globals
	*/
	public function editGlobals(){
	
		GlobalsUC::$permisison_add = true;
	
	}
	
	
	/**
	 * on manager init
	 */
	public function onManagerInit($objManager){
		
		$this->objAddonType = $objManager->getObjAddonType();
		
		$this->addonType = $this->objAddonType->typeName;
		
		$this->textSingle = $this->objAddonType->textSingle;
		$this->textPlural = $this->objAddonType->textPlural;
		
	}
	
	
	/**
	 * init the plugin
	 */
	protected function init(){
		
		
		$this->register($this->pluginName, $this->title, $this->version, $this->description, $this->extraInitParams);
		
		$this->addAction(self::ACTION_MODIFY_ADDONS_MANAGER, "onManagerInit");
		
		$this->addFilter(self::FILTER_MANAGER_MENU_SINGLE, "addItems_managerMenuSingle");
		$this->addFilter(self::FILTER_MANAGER_MENU_MULTIPLE, "addItems_managerMenuMultiple");
		$this->addFilter(self::FILTER_MANAGER_MENU_FIELD, "addItems_managerMenuField");
		$this->addFilter(self::FILTER_MANAGER_MENU_CATEGORY, "addItems_managerMenuCategory");
		
		$this->addAction(self::ACTION_MANAGER_ITEM_BUTTONS2, "drawItemButtons2");
		$this->addAction(self::ACTION_MANAGER_ITEM_BUTTONS3, "drawItemButtons3");
		
		
		$this->addAction(self::ACTION_EDIT_GLOBALS, "editGlobals");
	}
	
}

//run the plugin

$filepathProvider = dirname(__FILE__)."/../plugin_provider.php";
if(file_exists($filepathProvider)){
	
	require $filepathProvider;
	new AddonLibraryCreatorPluginProviderUC();
	
}else{
	$objPlugin = new AddonLibraryCreatorPluginUC();
}
		

