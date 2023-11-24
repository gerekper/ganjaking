<?php

class UnlimitedAddonsMigraterUC{
	
	private static $arrMenuPages = array();
	private static $arrSubMenuPages = array();
	private static $capability = "manage_options";
	private static $urlProvider;
	private static $pathImporter;
	private static $urlComponent;
	
	private static $db;
	private static $tableCreatorAddons;
	private static $tableLibraryAddons;
	private static $tableLibraryCategories;
	private static $status;
	
	const PLUGIN_NAME = "unlimitedelements";
	const MENU_NAME = "unlimitedelements";
	
	const ACTION_ADMIN_MENU = "admin_menu";
	const ACTION_ADMIN_INIT = "admin_init";
	const ACTION_ADD_SCRIPTS = "admin_enqueue_scripts";
	
	private static $t;
	
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		self::$t = $this;
		
		$this->init();
	}
	
	/**
	 * get value from array. if not - return alternative
	 */
	public static function getVal($arr,$key,$altVal=""){
	
		if(isset($arr[$key]))
			return($arr[$key]);
	
		return($altVal);
	}
	
	
	/**
	 * init variables
	 */
	private function initVars(){
		$pathBase = ABSPATH;
		$pathPlugin = realpath(dirname(__FILE__)."/../../")."/";
		$pathProvider = $pathPlugin."provider/";
		$pathImporter = $pathProvider."ac_importer/";
		
		if(class_exists("GlobalsUC") && isset(GlobalsUC::$urlPlugin))
			$urlPlugin = GlobalsUC::$urlPlugin;
		else
			$urlPlugin = plugins_url(self::PLUGIN_NAME)."/";
			
		$urlProvider = $urlPlugin."provider/";
		
		self::$urlComponent = admin_url()."admin.php?page=".self::MENU_NAME;
		
		self::$urlProvider = $urlProvider;
		self::$pathImporter = $pathImporter;

		
	}
	
	/**
	 * init vars only in admin pages
	 */
	public static function initAdminPagesVars(){
		
	}
	
	
	
	/**
	 *
	 * add menu page
	 */
	protected static function addMenuPage($title,$pageFunctionName,$icon=null){
				
		self::$arrMenuPages[] = array("title"=>$title,"pageFunction"=>$pageFunctionName,"icon"=>$icon);
	}
	
	/**
	 *
	 * add sub menu page
	 */
	protected static function addSubMenuPage($slug,$title,$pageFunctionName){
		self::$arrSubMenuPages[] = array("slug"=>$slug,"title"=>$title,"pageFunction"=>$pageFunctionName);
	}
	
	/**
	 * add admin menus from the list.
	 */
	public static function addAdminMenu(){
	
		foreach(self::$arrMenuPages as $menu){
			$title = $menu["title"];
			$pageFunctionName = $menu["pageFunction"];
			$icon = self::getVal($menu, "icon");
	
			add_menu_page( $title, $title, self::$capability, self::MENU_NAME, array(self::$t, $pageFunctionName), $icon );
		}
	
		foreach(self::$arrSubMenuPages as $key=>$submenu){
	
			$title = $submenu["title"];
			$pageFunctionName = $submenu["pageFunction"];
	
			$slug = self::MENU_NAME."_".$submenu["slug"];
			
			if($key == 0)
				$slug = self::MENU_NAME;
	
			add_submenu_page(self::MENU_NAME, $title, $title, 'manage_options', $slug, array(self::$t, $pageFunctionName) );
		}
	
	}
	
	
	/**
	 *
	 * tells if the the current plugin opened is this plugin or not
	 * in the admin side.
	 */
	private function isInsidePlugin(){
		$page = self::getGetVar("page","",UniteFunctionsUC::SANITIZE_KEY);
		
		if($page == self::MENU_NAME || strpos($page, self::MENU_NAME."_") !== false)
			return(true);
	
		return(false);
	}
	
	/**
	 *
	 * add some wordpress action
	 */
	protected static function addAction($action,$eventFunction){
	
		add_action( $action, array(self::$t, $eventFunction) );
	}
	
	public static function a_PUT_HTML(){}
	
	/**
	 * put style
	 */
	public static function putHtmlStyle(){
		?>
		<style>
			
			.uc-importer-text{
				font-size:18px;
			}
			
		</style>
		
		<?php 
	}
	
		
	
	/**
	 * put html start import
	 */
	public static function putHtmlStart(){
		
		global $ucStandAloneErrorMessage;
		
		$urlUninstall = admin_url()."plugins.php";
		
		?>
		<div class="uc-compatability-message">
			
			<h1 class='uc-importer-header'>Unlimited Elements</h1>
			
			<div class="uc-importer-text">
				
				<br><br>
				
				<h3>Plugin Startup Error</h3>
				<br><br>
				
				<?php echo $ucStandAloneErrorMessage?>
				
			</div>
			
		</div>
		
		<?php 
	}
	
	
	
	/**
	 *
	 * admin main page function.
	 */
	public static function adminPages(){
		
		self::initAdminPagesVars();
						
		self::putHtmlStyle();
		
		self::putHtmlStart();
		
	}
	
	/**
	 * init function
	 */
	private function init(){
				
		$this->initVars();				
		$urlMenuIcon = self::$urlProvider."assets/images/icon_menu.png";
		
		self::addMenuPage('Unlimited Elements', "adminPages", $urlMenuIcon);
		
		//add internal hook for adding a menu in arrMenus
		self::addAction(self::ACTION_ADMIN_MENU, "addAdminMenu");
		
	}
	
}

new UnlimitedAddonsMigraterUC();

