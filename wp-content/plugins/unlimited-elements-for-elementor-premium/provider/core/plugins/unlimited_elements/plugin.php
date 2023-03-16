<?php
/**
 * @package unlimited elements plugin
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2017 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UnlimitedElementsPluginUC extends UniteCreatorPluginBase{
	
	protected $extraInitParams = array();
	
	private $version = "1.0";
	private $pluginName = "unlimited_elements_plugin";
	private $title;
	private $description;

	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$pathPlugin = dirname(__FILE__)."/";
		
		parent::__construct($pathPlugin);
		
		$this->title = esc_html__("Unlimited Elements for Elementor", "unlimited-elements-for-elementor");
		$this->description = "Create and use widgets for Elementor Page Builder";
						
		$this->init();
	}
	
	/**
	 * run admin
	 */
	public function runAdmin(){
		
		$this->includeCommonFiles();
		$this->runCommonActions();
		
		require_once GlobalsUC::$pathPlugin . "unitecreator_admin.php";
		require_once GlobalsUC::$pathProvider . "provider_admin.class.php";
		require_once $this->pathPlugin . "provider_core_admin.class.php";
		require_once $this->pathPlugin . "feedback.class.php";
		require_once $this->pathPlugin . "dialog_param_elementor.class.php";
		
		$mainFilepath = GlobalsUC::$pathPlugin."unlimited_elements.php";
		
		new UniteProviderCoreAdminUC_Elementor($mainFilepath);
		
	}

	
	/**
	 * run front 
	 */
	public function runFront(){
				
		$this->includeCommonFiles();
		$this->runCommonActions();
				
		require_once GlobalsUC::$pathProvider . "provider_front.class.php";
		require_once $this->pathPlugin . "provider_core_front.class.php";
		
		$mainFilepath = GlobalsUC::$pathPlugin."unlimited_elements.php";
						
		new UniteProviderCoreFrontUC_Elementor($mainFilepath);
		
	}
	
	
	/**
	 * include files
	 */
	protected function includeCommonFiles(){
		
		require_once $this->pathPlugin . 'globals.class.php';
		require_once $this->pathPlugin . 'addontype_elementor.class.php';
		require_once $this->pathPlugin . 'addontype_elementor_template.class.php';
		require_once $this->pathPlugin . 'addontype_cpt.class.php';
		require_once $this->pathPlugin . 'helper_provider_core.class.php';
		require_once $this->pathPlugin . 'elementor/elementor_integrate.class.php';
		require_once $this->pathPlugin . 'elementor/pagination.class.php';
		require_once $this->pathPlugin . "elementor/elementor_dynamic_visibility.class.php";
		require_once $this->pathPlugin . "elementor/elementor_controls.class.php";
		
		if(is_admin()){
			require_once $this->pathPlugin . 'elementor/elementor_layout_exporter.class.php';
		}
		
		//require_once $this->pathPlugin . 'elementor/elementor_base_override.class.php';
		
	}

	
	/**
	 * on plugins loaded
	 */
	public function onPluginsLoaded(){
		
		//register elementor template addon type
		
		$objAddonTypeElementorTempalte = new UniteCreatorAddonType_Elementor_Template();
		$this->registerAddonType(GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR_TEMPLATE, $objAddonTypeElementorTempalte);
		
		if(defined("UC_BOTH_VERSIONS_ACTIVE")){
			HelperUC::addAdminNotice("Both unlimited elements plugins, FREE and PRO are active! Please uninstall FREE version.");
		}
		
		// Notice if the Elementor is not active
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}
				
		$objIntegrate = new UniteCreatorElementorIntegrate();
		$objIntegrate->initElementorIntegration();
		
	}
	
	
	/**
	 * modify plugin variables if the plugin is not default (blox)
	 */
	private function modifyPluginVariables(){
				
		$pluginName = GlobalsUnlimitedElements::PLUGIN_NAME;
		
		GlobalsUC::$url_component_admin = admin_url()."admin.php?page={$pluginName}";
		GlobalsUC::$url_component_client = GlobalsUC::$url_component_admin;
		GlobalsUC::$url_component_admin_nowindow = GlobalsUC::$url_component_admin."&ucwindow=blank";
	}
	
	
	/**
	 * run common actions
	 */
	protected function runCommonActions(){
		
		$this->addAction("plugins_loaded", "onPluginsLoaded");
		$this->modifyPluginVariables();
		
		//register elementor addon type
		
		$objAddonTypeElementor = new UniteCreatorAddonType_Elementor();
		$this->registerAddonType(GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR, $objAddonTypeElementor);
				
		$objAddonTypeCustomPostTypes = new UniteCreatorAddonType_CustomPostType();
		$this->registerAddonType(GlobalsUnlimitedElements::ADDONSTYPE_CUSTOM_POSTTYPES, $objAddonTypeCustomPostTypes);
		
		
	}
	
	
	/**
	 * init the plugin
	 */
	protected function init(){
		
		$this->register($this->pluginName, $this->title, $this->version, $this->description, $this->extraInitParams);
		
	}
	
}


//run the plugin
new UnlimitedElementsPluginUC();
		
