<?php
use Elementor\TemplateLibrary;
use Elementor\Plugin;
use Elementor\Core\Settings\Manager as SettingsManager;


defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class UniteCreatorElementorIntegrate{
	
	const ADDONS_CATEGORY_TITLE = "Unlimited Elements";
	const ADDONS_CATEGORY_NAME = "unlimited_elements";
	const ADDONS_TYPE = "elementor";
	const DEFAULT_ICON = "uc-default-widget-icon ue-wi-svg";
	const TEMPLATE_TYPE_ARCHIVE = "archive";
	const CONTROL_BACKGROUND_TYPE = "uc_background_type";
	
	public static $enableLowMemoryCheck = false;
	
	private $enableImportTemplate = false;
	private $enableExportTemplate = false;
	private $enableBackgroundWidgets = false;
	private $enableDynamicVisibility = false;
	
	public static $isConsolidated = false;
	
	private $pathPlugin;
	private $pathControls;
	private $pathDynamicTags;
	private $arrAddons;
	
	private $arrCatsRecords = array();
	public static $arrAddonsRecords = array();
	public static $arrBGAddonsRecords = array();
	
	private $arrBGAddonsOutput = array();
	public static $isLogMemory;
	public static $counterWidgets=0;
	public static $counterControls=0;
	private static $numRegistered = 0;
	public static $arrCatsCache = array();
	public static $templateType;
	public static $isAjaxAction = false;
	public static $isFrontendEditorMode = false;
	public static $isEditMode = false;
	
	private static $arrPostsWidgetNames = array();
	
	private $isSystemErrorOccured = false;
	private static $objAddons;
	public static $isOutputPage = false;
	private $isPluginFilesIncluded = false;
	private $objBackgroundWidget;
	private $objDynamicVisibility;
	
	public static $enableEditHTMLButton = null;
	
	public static $showWidgetPreviews = true;
	public static $arrWidgetIcons = array();		//fill by the widgets
	public static $isDarkMode = false;
	private $isControlsRegistered = false;
	private $isWidgetsRegistered = false;
	private $isOldElementorVersion = false;
	
	
	/**
	 * init some vars
	 */
	public function __construct(){
		
		$this->pathPlugin = __DIR__."/";
		$this->pathControls = $this->pathPlugin."controls/";
		$this->pathDynamicTags = $this->pathPlugin."dynamic_tags/";
		
		self::$isLogMemory = $this->getIsLogMemory();
		
	}
	
	
	/**
	 * determine if log memory or not
	 */
	private function getIsLogMemory(){
		
		//check general setting		
		if(GlobalsUC::$is_admin == false)
			return(false);
		
		$enableMemoryTest = HelperProviderCoreUC_EL::getGeneralSetting("enable_memory_usage_test");
		$enableMemoryTest = UniteFunctionsUC::strToBool($enableMemoryTest);
				
		if($enableMemoryTest == false)
			return(false);
		
		//filter unnessasery urls
		$url = GlobalsUC::$current_page_url;
		
		if(strpos($url, "js.map") !== false)
			return(false);
		
		return(true);
	}
	
	
	private function z__________TEMP___________(){}
	
	
	/**
	 * run test widget
	 */
	public function runTestWidget(){
		
		require_once $this->pathPlugin."test_widget.class.php";
		
        \Elementor\Plugin::instance()->widgets_manager->register(new ElementorWidgetTest());
	}
	
	
	/**
	 * run second widget
	 */
	public function runTestRealWidget(){
		
		$objAddons = new UniteCreatorAddons();
		$arrAddons = $objAddons->getArrAddons();
		
		$addon = $arrAddons[0];
		
		$widget = new UniteCreatorElementorWidget();
        \Elementor\Plugin::instance()->widgets_manager->register($widget);
		
	}
	
	
	
	private function a___________REGISTER_COMPONENTS__________(){}
	
	
	/**
	 * get arr addons only once
	 */
	public function getArrAddons($getRecordsOnly = false){
		
		if(!empty($this->arrAddons))
			return($this->arrAddons);
		
		if(empty($addonsType))
			$addonsType = self::ADDONS_TYPE;
		
		$objAddons = new UniteCreatorAddons();
		$params = array();
		$params["filter_active"] = "active";
		$params["addontype"] = self::ADDONS_TYPE;
		
		if($getRecordsOnly == true){
			
			$arrAddons = $objAddons->getArrAddonsShort("", $params, self::ADDONS_TYPE);
			
			return($arrAddons);
		}
		else
			$arrAddons = $objAddons->getArrAddons("", $params, self::ADDONS_TYPE);
					
		$this->arrAddons = $arrAddons;
		
		return($arrAddons);
	}
	
	
	/**
	 * register addons
	 */
	private function registerWidgets_addons($arrAddons, $isRecords = false){

		if($this->isWidgetsRegistered == true)
			return(false);
		
		foreach ($arrAddons as $addon) {
			
			self::$counterWidgets++;
			
			$isEnoughtMemory = UniteFunctionsUC::isEnoughtPHPMemory();
			
			self::$numRegistered++;
			
			if($isRecords == true){
				$name = $addon["name"];
				
			}else{
				$name = $addon->getName();
			}
			
			if($isEnoughtMemory == false){
				 
				self::logMemoryUsage("Skip widget register (no memory): ".$name.", counter: ".self::$counterWidgets, true);

				if(self::$enableLowMemoryCheck == true)
					continue;
			}
			
			//some protection
			$isAlphaNumeric = UniteFunctionsUC::isAlphaNumeric($name);
			if($isAlphaNumeric == false)
				return(false);
			
			$className = "UCAddon_".$name;
            
			//if($isEnoughtMemory == false)
				//$className .= "_no_memory";
			
			self::logMemoryUsage("Before Register Widget: ".$name. ", counter: ".self::$counterWidgets);
			
		    $code = "class {$className} extends UniteCreatorElementorWidget{}";
		    eval($code);
            
		    try{
		    	
			    $widget = new $className();
			    
			    $manager = \Elementor\Plugin::instance()->widgets_manager;
			    
			    if(method_exists($manager, "register"))
					$manager->register($widget);
            	else
					$manager->register_widget_type($widget);
            	
					
	             self::logMemoryUsage("Register Widget: ".$name.", counter: ".self::$counterWidgets);
	                    
		    }catch(Exception $e){

				self::logMemoryUsage("Skip widget register (no memory): ".$name.", counter: ".self::$counterWidgets, true);
		   		
		    }
			
			$this->isWidgetsRegistered = true;
			
		}
		
	}
	
	/**
	 * register elementor widget by class name
	 */
	private function registerWidgetByClassName($className){
		
		$code = "class {$className} extends UniteCreatorElementorWidget{}";
	    eval($code);
		
		$widget = new $className();
		
		$manager = \Elementor\Plugin::instance()->widgets_manager;
		
		if(method_exists($manager, "register"))
        	$manager->register($widget);
		else
        	$manager->register_widget_type($widget);
		
	}
	
	
	/**
	 * register consolidated widget
	 */
	private function registerWidgets_consolidated($objCat, $arrCat){
		
		$title = $objCat->getTitle();
		$alias = $objCat->getAlias();
		
		$arrAddons = UniteFunctionsUC::getVal($arrCat, "addons");
		
		if(empty($arrAddons))
			return(false);
		
		self::$arrCatsCache[$alias] = array("title"=>$title, "objcat"=>$objCat,"addons"=>$arrAddons);
		
		$className = "UCAddon_uccat_".$alias;
	    $this->registerWidgetByClassName($className);
		
	}
	
	
	/**
	 * register consolidated category widgets
	 */
	private function registerWidgets_categories(){
		
		$objAddons = new UniteCreatorAddons();
		
		$arrCats = $objAddons->getAddonsWidthCategories(true, false, self::ADDONS_TYPE, array("get_cat_objects"=>true));
    	
		foreach($arrCats as $cat){
			$id = UniteFunctionsUC::getVal($cat, "id");
			
			//uncategorised
			if($id == 0){
				$addons = UniteFunctionsUC::getVal($cat, "addons");
				if(!empty($addons))
					$this->registerWidgets_addons($addons);
				
				continue;
			}
						
			$objCat = UniteFunctionsUC::getVal($cat, "objcat");
			
			//register consolidated widgets
			$this->registerWidgets_consolidated($objCat, $cat);			
		}
		
	}
	
	
	
	/**
	 * register elementor widgets from the library
	 */
	private function registerWidgets(){
		
		if($this->isSystemErrorOccured == true)
			return(false);
			
		self::logMemoryUsage("before widgets registered");
		
		self::$numRegistered = 0;
		
		if(self::$isConsolidated)
			$this->registerWidgets_categories();
		else{
			
			$this->registerWidgets_addons(self::$arrAddonsRecords, true);
		}
		
		self::logMemoryUsage("widgets registered: ".self::$numRegistered, true);
				
	}
	
	/**
	 * get template type
	 */
	public static function getCurrentTemplateType(){
		
		if(!empty(self::$templateType))
			return(self::$templateType);
		
		$post = get_post();
				
		if(empty($post))
			return("");
			
		$postType = $post->post_type;
				
		if($postType == GlobalsUnlimitedElements::POSTTYPE_ELEMENTOR_LIBRARY){
			
			$templateType = get_post_meta($post->ID, GlobalsUnlimitedElements::META_TEMPLATE_TYPE, true);
			return($templateType);		
		}
		
		return("");
	}
	
	
	/**
	 * init other variables like onUpdate yes/no
	 */
	private function initOtherVars(){
		
		$action = UniteFunctionsUC::getPostGetVariable("action", "", UniteFunctionsUC::SANITIZE_KEY);
		if($action == "elementor_ajax")
			self::$isAjaxAction = true;
		
	}
		
	/**
	 * init template vars variables
	 */
	private function initTemplateTypeVars(){
		
		$document = \Elementor\Plugin::$instance->documents->get_current();
		
		if(empty($document)){
			self::$templateType = self::getCurrentTemplateType();
			
			return(false);
		}
		
		if(method_exists($document, "get_template_type"))
			self::$templateType = $document->get_template_type();
		
		HelperUC::addDebug("set template type: ".self::$templateType);
	}
	
	
    /**
     * on widgets registered event
     * register elementor widgets from the library
     */
    public function onWidgetsRegistered() {
		
    	//$this->initTemplateTypeVars();
    	$this->initOtherVars();
    	
    	$this->includePluginFiles();
    	
    	$this->registerWidgets();
    	
    }
	
    
    /**
     * register controls
     */
    public function onRegisterControls($controls_manager) {
		
    	if($this->isControlsRegistered == true)
    		return(false);
    	
    	self::logMemoryUsage("before controls registered");

    	$isOldWay = false;
    	if(method_exists($controls_manager, "register") == false)
    		$isOldWay = true;
    	
    	//add hr control
    	require $this->pathControls."control_hr.php";
        $controls_manager->register(new Elementor\Control_UC_HR());
        
        //add audio control
    	require $this->pathControls."control_audio.php";
    	$controls_manager->register(new Elementor\Control_UC_AUDIO());

        //add select post type control
    	require $this->pathControls."control_select_posttype.php";
        $controls_manager->register(new Elementor\Control_UC_SelectSpecial);
        
        
        self::logMemoryUsage("after controls registered");
        
        $this->isControlsRegistered = true;
        
    }
    
    
	/**
	 * include plugin files that should be included only after elementor include
	 */
	public function includePluginFiles(){
		
		if($this->isPluginFilesIncluded == true)
			return(false);
		
		require_once $this->pathPlugin."elementor_widget.class.php";
		require_once $this->pathPlugin."elementor_background_widget.class.php";
		
		$this->isPluginFilesIncluded = true;
		
	}
    
    
    /**
     * get category alias from id
     */
    public static function getCategoryName($catID){
		$catName = "uc_category_{$catID}";
		
		return($catName);
    }
    
    
    /**
     * add all categories
     */
    private function addUCCategories(){
    	
    	$objElementsManager = \Elementor\Plugin::instance()->elements_manager;
    	
    	//add general category
    	$objElementsManager->add_category(self::ADDONS_CATEGORY_NAME, array("title"=>self::ADDONS_CATEGORY_TITLE,"icon"=>self::DEFAULT_ICON), 2);
    	
    	if(empty($this->arrCatsRecords))
    		return(false);    	
    	
    	foreach($this->arrCatsRecords as $index=>$cat){
    		    		
    		$catID = UniteFunctionsUC::getVal($cat, "id");
    		$catTitle = UniteFunctionsUC::getVal($cat, "title");
    		$catName = self::getCategoryName($catID);
    		
    		$icon = self::DEFAULT_ICON;
    		
    		$objElementsManager->add_category($catName, array("title"=>$catTitle,"icon"=>$icon), 2);
    	}
    	
    }
    
    
    /**
     * run after register controls
     */
    public function onFrontendAfterRegisterControls(){
    	
    	self::logMemoryUsage("End registering widget controls, num registered: ".UniteCreatorElementorIntegrate::$counterControls, true);
    	
    }
	
    /**
     * register dynamic tags
     */
    public function onRegisterDynamicTags($dynamicTags){
    	
    	//add hr control
    	require $this->pathDynamicTags."tag_current_timestamp.php";
    	
    	$dynamicTags->register_tag("UnlimitedElementsDynamicTag_TimeStamp");
    	
    }
    
    /**
     * collect posts widget names by record
     * for later pagination enable check
     */
    private function collectPostsWidgetsByRecord($record){
    	
    	$config = UniteFunctionsUC::getVal($record, "config");
    	if(empty($config))
    		return(false);
    		
    	$arrConfig = UniteFunctionsUC::jsonDecode($config);
    	
    	$params = UniteFunctionsUC::getVal($arrConfig, "params");
    	if(empty($params))
    		return(false);
    		
    	foreach($params as $param){
    		    		
    		$type = UniteFunctionsUC::getVal($param, "type");
    		if($type != UniteCreatorDialogParam::PARAM_POSTS_LIST && $type != UniteCreatorDialogParam::PARAM_LISTING)
    			continue;
    		
    		//post list only
    		$widgetName = UniteFunctionsUC::getVal($record, "alias");
    		
    		self::$arrPostsWidgetNames[$widgetName] = true;
    		
    		return(false);
    	}
    }
    
    
    /**
     * preload all the elementor data
     */
    private function preloadElementorDBData(){
    	
    	//don't let run the function twice
    	if(!empty(self::$arrAddonsRecords))
			return(false);
			    	
    	$db = HelperUC::getDB();
    	
    	$tableCats = GlobalsUC::$table_categories;
    	$tableAddons = GlobalsUC::$table_addons;
    	$addonType = GlobalsUnlimitedElements::ADDONSTYPE_ELEMENTOR;
    	$addonTypeBG = GlobalsUC::ADDON_TYPE_BGADDON;
    	
    	$whereAddonType = "addons.addontype='{$addonType}'";
    	
    	if($this->enableBackgroundWidgets == true)
    		$whereAddonType = "addons.addontype in('{$addonType}','{$addonTypeBG}')";
    	
    	//for output - get without cats
    	if(self::$isOutputPage){
    		
	    	$query = "select * from $tableAddons as addons";
	    	$query .= " where $whereAddonType and addons.is_active=1";
    		
    	}else{		//for editor - get with categories
    	
    		$query = "select addons.*, cats.title as cat_title ,cats.alias as cat_alias, cats.ordering as cat_ordering from $tableAddons as addons";
    		$query .= " left join $tableCats as cats on(addons.catid = cats.id)"; 
    		$query .= " where $whereAddonType and addons.is_active=1 order by cat_ordering";
    	}
    	
    	
    	$arrRecords = $db->fetchSql($query, true);
    	
    	if(empty($arrRecords))
    		return(false);
    	
    	//get cats records
    	foreach($arrRecords as $record){
    		
    		$addonName = UniteFunctionsUC::getVal($record, "name");
    		$recordAddonType = UniteFunctionsUC::getVal($record, "addontype");
			
    		//save bg addon record
    		if($recordAddonType == $addonTypeBG){
    			self::$arrBGAddonsRecords[$addonName] = $record;
    			continue;
    		}
    		
    		$this->collectPostsWidgetsByRecord($record);
    		   
    		self::$arrAddonsRecords[$addonName] = $record;
    		
    		//cache category records
    		if(self::$isOutputPage == true)
    			continue;
    		    		
    		$catID = UniteFunctionsUC::getVal($record, "catid");
    		$catTitle = UniteFunctionsUC::getVal($record, "cat_title");
    		$catAlias = UniteFunctionsUC::getVal($record, "cat_alias");
    		$catOrdering = UniteFunctionsUC::getVal($record, "cat_ordering");
    		
    		if(empty($catAlias))
    			$catAlias = "cat_".$catID;
    		
    		if(isset($this->arrCatsRecords[$catAlias]))
    			continue;
    			
    		$catRecord = array();
    		$catRecord["id"] = $catID;
    		$catRecord["title"] = $catTitle;
    		$catRecord["alias"] = $catAlias;
    		$catRecord["ordering"] = $catOrdering;
    		
    		$this->arrCatsRecords[$catAlias] = $catRecord;
    	}
    	
    }
    
    
    /**
     * on elementor init
     */
    public function onElementorInit(){
    	
    	try{
			
    		$this->preloadElementorDBData();
    		
    		$this->addUCCategories();
    		
    	}catch(Exception $e){
    		
    		$this->isSystemErrorOccured = true;
    	}
    	    	
    }
	
	private function a____________DYNAMIC_VISIBILITY___________(){}
	
	/**
	 * add dynamic visibility controls
	 */
	public function addDynamicVisibilityControls($objControls){
		
		$this->objDynamicVisibility->addVisibilityControls($objControls);
		
	}
	
	
	/**
	 * init dynamic visibility
	 */
	private function initDynamicVisibility(){
				
		$this->enableDynamicVisibility = true;
		$this->objDynamicVisibility = new UniteCreatorDynamicVisibility();
		
		add_action("elementor/element/section/section_advanced/after_section_end", array($this, "addDynamicVisibilityControls"));
		
		//filtering content
		if(self::$isEditMode == true)
			return(false);
		
		add_action("elementor/frontend/section/before_render", array($this->objDynamicVisibility, "onBeforeRenderElement"));
		add_action("elementor/frontend/section/after_render", array($this->objDynamicVisibility, "onAfterRenderElement"));
		
		
		//dmp("init filtering");
        // filter sections
        //$this->loader->addAction( "elementor/frontend/section/before_render", $pluginPublic, 'filterSectionContentBefore', 10, 1 );
        //$this->loader->addAction( "elementor/frontend/section/after_render", $pluginPublic, 'filterSectionContentAfter', 10, 1 );
			
		
	}
	
    
	private function a____________BACKGROUND_WIDGETS___________(){}
    
	
	/**
	 * output background includes
	 */
	private function outputBGIncludes($output){
		
		$arrIncludes = UniteFunctionsUC::getVal($output, "includes");
		
		if(empty($arrIncludes))
			return(false);
		
		foreach($arrIncludes as $include){
			
			$type = UniteFunctionsUC::getVal($include, "type");
			$url = UniteFunctionsUC::getVal($include, "url");
			$handle = UniteFunctionsUC::getVal($include, "handle");
			
			if($type == "css")
				HelperUC::addStyleAbsoluteUrl($url, $handle);
			else
				HelperUC::addScriptAbsoluteUrl($url, $handle);
		}
		
	}
	
	/**
	 * add front end html render
	 */
	private function addFrontRenderBackground($backgroundType, $settings, $objElement){
		
		$sectionName = $objElement->get_name();
		
		$rawData = $objElement->get_raw_data();
		
		$elementID = UniteFunctionsUC::getVal($rawData, "id");
		
		
		try{
			
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByAlias($backgroundType, GlobalsUC::ADDON_TYPE_BGADDON);
			
			if(empty($this->objBackgroundWidget))
				$this->objBackgroundWidget = new UniteCreatorElementorBackgroundWidget();
			
			$arrAddonValues = $this->objBackgroundWidget->getBGSettings($settings, $backgroundType);
						
			if(!empty($arrAddonValues))
				$objAddon = $this->objBackgroundWidget->setAddonSettingsFromElementorSettings($objAddon, $arrAddonValues);
			
			
			if(empty(self::$objAddons))
				self::$objAddons = new UniteCreatorAddons();
			
			$output = self::$objAddons->getAddonOutput($objAddon, true);	//nowrap
			
			if(empty($output))
				return(false);
			
			if(empty($this->arrBGAddonsOutput))
				UniteProviderFunctionsUC::addjQueryInclude();
			
			//put includes
			$this->outputBGIncludes($output);
			
			//add location too
			$location = UniteFunctionsUC::getVal($settings, "uc_background_location");
			
			if(is_array($output))
				$output["location"] = $location;
			
			$this->arrBGAddonsOutput[$elementID] = $output;
			
		}catch(Exception $e){
			//just skip
		}
		
	}
	
	/**
	 * on before render
	 */
	public function onFrontAfterRender($objElement){
		
		$settings = $objElement->get_settings_for_display();
		
		$backgroundType = UniteFunctionsUC::getVal($settings, self::CONTROL_BACKGROUND_TYPE);
		
		if(!empty($backgroundType) && $backgroundType != "__none__")
			$this->addFrontRenderBackground($backgroundType, $settings, $objElement);
		
	}
	
	
	/**
	 * print footer html
	 */
	public function onPrintFooterHtml(){
		
		if(empty($this->arrBGAddonsOutput))
			return(true);
		?>
		<style>
			.unlimited-elements-background-overlay{
				position:absolute;
				top:0px;
				left:0px;
				width:100%;
				height:100%;
				z-index:0;
			}
			
			.unlimited-elements-background-overlay.uc-bg-front{
				z-index:999;
			}
		</style>
		
		<?php 
		
		foreach($this->arrBGAddonsOutput as $elementID => $bgOutput){
			
			$html = UniteFunctionsUC::getVal($bgOutput, "html");
			$location = UniteFunctionsUC::getVal($bgOutput, "location");

			$addClass = "";
			if($location === "front" || $location === "body_front" || $location === "layout_front")
				$addClass = " uc-bg-front";
			
			$addData = "data-location=\"$location\"";
			
			?>
			<div class="unlimited-elements-background-overlay<?php echo $addClass?>" data-forid="<?php echo $elementID?>" <?php echo $addData?> style="display:none">
				<?php echo $html?>
			</div>
			<?php 
		}
		
		?>
		<script type='text/javascript'>
			
			jQuery(document).ready(function(){
				
				function ucBackgroundOverlayPutStart(){
					
					var objBG = jQuery(".unlimited-elements-background-overlay");
					
					if(objBG.length == 0)
						return(false);
										
					objBG.each(function(index, bgElement){

						var objBgElement = jQuery(bgElement);

						var targetID = objBgElement.data("forid");

						var location = objBgElement.data("location");

						switch(location){
							case "body":
							case "body_front":
								var objTarget = jQuery("body");
							break;
							case "layout":
							case "layout_front":
								var objLayout = jQuery("*[data-id=\""+targetID+"\"]");
								var objTarget = objLayout.parents(".elementor");
								if(objTarget.length > 1)
									objTarget = jQuery(objTarget[0]);
							break;
							default:
								var objTarget = jQuery("*[data-id=\""+targetID+"\"]");
							break;
						}
						
						
						if(objTarget.length == 0)
							return(true);

						var objVideoContainer = objTarget.children(".elementor-background-video-container");
						
						if(objVideoContainer.length == 1)
							objBgElement.detach().insertAfter(objVideoContainer).show();
						else
							objBgElement.detach().prependTo(objTarget).show();

						objBgElement.trigger("bg_attached");
						objBgElement.addClass("uc-bg-attached");
						
					});
				}

				ucBackgroundOverlayPutStart();
				
				jQuery( document ).on( 'elementor/popup/show', () => { 
					
					if(jQuery(".unlimited-elements-background-overlay").not(".uc-bg-attached").length) 
						ucBackgroundOverlayPutStart();
					
				});
				
			});

			
		</script>
		<?php 
		
		//empty the bg output
		
		$this->arrBGAddonsOutput = array();
		
	}
	
	
	/**
	 * on page style controls add
	 * from controls-stack.php
	 */
	public function onSectionStyleControlsAdd($objControls, $args){
    	
		$this->includePluginFiles();
		
		//---- set background items
		
		$none = "__none__";
		
		$arrBGItems = array();
		$arrBGItems[$none] = __("[No Background]", "unlimited-elements-for-elementor");
		
		foreach(self::$arrBGAddonsRecords as $addon){
			
			$title = UniteFunctionsUC::getVal($addon, "title");
			$alias = UniteFunctionsUC::getVal($addon, "alias");
			
			$arrBGItems[$alias] = $title;
		}
		
		$default = $none;
		
		//Section background
		$objControls->start_controls_section(
			'section_background_uc',
			[
				'label' => __( 'Unlimited Background', 'unlimited_elements' ),
				'tab' => "style",
			]
		);
		
        $objControls->add_control(
              self::CONTROL_BACKGROUND_TYPE, array(
              'label' => __("Background Type", "unlimited-elements-for-elementor"),
              'type' => \Elementor\Controls_Manager::SELECT,
        	  'default'=> $default,
        	  'options' => $arrBGItems
              )
         );
    	
         //add link to install more
         
        $textInstallMore = __("To install more backgrounds", "unlimited-elements-for-elementor");
        $textClickHere = __("click here","unlimited-elements-for-elementor");
        
        $urlView = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_BACKGROUNDS);
        
        $html = "<i style='font-size:12px;'>{$textInstallMore} <a href=\"{$urlView}\" target=\"_blank\">{$textClickHere}</a> </i>";
        
         foreach(self::$arrBGAddonsRecords as $record){
         	
         	$objAddon = new UniteCreatorAddon();
         	$objAddon->initByDBRecord($record);
         	
         	$objWidget = new UniteCreatorElementorBackgroundWidget();
         	$objWidget->initBGWidget($objAddon, $objControls);
         	
         	$objWidget->registerBGControls();
         }
		
		$objControls->add_control(
			'uc_background_location',
			array(
				'label' => esc_html__( 'Background Location', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'back',
				'options' => array(
					'back'  => esc_html__( 'In Background', 'unlimited-elements-for-elementor' ),
					'front' => esc_html__( 'In Foregroud', 'unlimited-elements-for-elementor' ),
					'body' => esc_html__( 'Site Body Background', 'unlimited-elements-for-elementor' ),
					'body_front' => esc_html__( 'Site Body Foreground', 'unlimited-elements-for-elementor' ),
					'layout' => esc_html__( 'Layout Background', 'unlimited-elements-for-elementor' ),
					'layout_front' => esc_html__( 'Layout Foreground', 'unlimited-elements-for-elementor' )
			),
				"condition" => array(self::CONTROL_BACKGROUND_TYPE."!" => "{$none}")
			)
		);
                  
		$objControls->add_control(
			'html_button_installbg',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'label' => '',
				'separator'=>"before",
				'raw' => $html
			)
		);
         
         $objControls->end_controls_section();
	}
	
	
	
	/**
	 * test background
	 */
	private function initBackgroundWidgets(){
		
		$this->enableBackgroundWidgets = true;
				
		add_action("elementor/element/section/section_background_overlay/after_section_end", array($this, "onSectionStyleControlsAdd"),10, 2);
		add_action("elementor/element/container/section_background_overlay/after_section_end", array($this, "onSectionStyleControlsAdd"),10, 2);
		
		if(self::$isOutputPage == true){
	    	
			add_action('elementor/frontend/section/after_render', array($this, 'onFrontAfterRender'));
			add_action('elementor/frontend/container/after_render', array($this, 'onFrontAfterRender'));
	    	add_action('wp_print_footer_scripts', array($this, 'onPrintFooterHtml'));
		}
    	
	}
	
	
	
    /**
     * get editor page scripts
     */
    private function getEditorPageCustomScripts(){
    	
    	$arrAddons = $this->getArrAddons();
    	
    	$objAddons = new UniteCreatorAddons();
		
    	$urlAssets = GlobalsUC::$url_assets;
    	
    	$script = "";
    	$script .= "\n\n // ----- unlimited elements scripts ------- \n\n";
    	$script .= "var g_ucUrlAssets='{$urlAssets}';\n";
    	
    	if($this->enableBackgroundWidgets == true)
    		$script .= "var g_ucHasBackgrounds=true;\n";
    	
    	$nonce = UniteProviderFunctionsUC::getNonce();
    	
    	$script .= "var g_ucNonce=\"{$nonce}\";\n";
		
    	$urlAdmin = admin_url();
    	
    	$script .= "var g_ucAdminUrl=\"{$urlAdmin}\";\n";

    	
    	return($script);
    }
    
    
    /**
     * register front end scripts
     */
    public function onRegisterFrontScripts(){
    	
    	//background related
    	if(self::$isFrontendEditorMode == true){
    		
	    	HelperUC::addScriptAbsoluteUrl(HelperProviderCoreUC_EL::$urlCore."elementor/assets/uc_front_admin.js", "unlimited_elements_front_admin");
	    	HelperUC::addStyleAbsoluteUrl(HelperProviderCoreUC_EL::$urlCore."elementor/assets/uc_front_admin.css", "unlimited_elements_front_admin_css");
    	}
    	
    	
    }
    
	
	
	private function a____________IMPORT_ADDONS___________(){}
	
    
    /**
     * return if it's elmenetor library page
     */
    private function isElementorLibraryPage(){
		global $current_screen;
		if ( ! $current_screen ) {
			return false;
		}
		
		if($current_screen->base != "edit")
			return(false);
		
		if($current_screen->post_type != GlobalsUnlimitedElements::POSTTYPE_ELEMENTOR_LIBRARY)
			return(false);
		
		return(true);		
    }
    
    
    
   	/**
	* put import vc layout html
	*/
	private function putDialogImportLayoutHtml(){
			
			$dialogTitle = __("Import Unlimited Elements Layout to Elementor",UNLIMITED_ADDONS_TEXTDOMAIN);
			
			
			?>
		<div id="uc_dialog_import_layouts" class="unite-inputs" title="<?php echo esc_attr($dialogTitle)?>" style="display:none;">
			
			<div class="unite-dialog-top"></div>
			
			<div class="unite-inputs-label">
				<?php esc_html_e("Select vc layout export file (zip)", UNLIMITED_ADDONS_TEXTDOMAIN)?>:
			</div>
			
			<div class="unite-inputs-sap-small"></div>
			
			<form id="dialog_import_layouts_form" name="form_import_layouts">
				<input id="dialog_import_layouts_file" type="file" name="import_layout">
				
			</form>	
			
			<div class="unite-inputs-sap-double"></div>
			
			<div class="unite-inputs-label" >
				<label for="dialog_import_layouts_file_overwrite">
					<?php esc_html_e("Overwrite Addons", UNLIMITED_ADDONS_TEXTDOMAIN)?>:
				</label>
				<input type="checkbox" id="dialog_import_layouts_file_overwrite"></input>
			</div>
			
			
			<div class="unite-clear"></div>
			
			<?php 
				$prefix = "uc_dialog_import_layouts";
				$buttonTitle = __("Import VC Layout", UNLIMITED_ADDONS_TEXTDOMAIN);
				$loaderTitle = __("Uploading layout file...", UNLIMITED_ADDONS_TEXTDOMAIN);
				$successTitle = __("Layout Imported Successfully", UNLIMITED_ADDONS_TEXTDOMAIN);
				HelperHtmlUC::putDialogActions($prefix, $buttonTitle, $loaderTitle, $successTitle);
			?>
			
			<div id="div_debug"></div>
			 
		</div>		
		
		<?php 
	}
	
	
	/**
	 * put import layout button
	 */
	private function putImportLayoutButton(){
		
		$nonce = UniteProviderFunctionsUC::getNonce();
		
		dmp("import layout!!!");
		
		?>
		<style>
		
		#uc_import_layout_area{
			margin: 50px 0 30px;
    		text-align: center;			
		}
		
		#uc_form_import_template {
		    background-color: #fff;
		    border: 1px solid #e5e5e5;
		    display: inline-block;
		    margin-top: 30px;
		    padding: 30px 50px;
		}
		
		#uc_import_layout_area_title{
		 	color: #555d66;
    		font-size: 18px;			
		}
    	
    	
		</style>
		
		<div style="display:none">
			
			<a id="uc_button_import_layout" href="javascript:void(0)" class="page-title-action"><?php esc_html_e("Import Template With Images", "unlimited-elements-for-elementor")?></a>
			
			<div id="uc_import_layout_area" style="display:none">
				<div id="uc_import_layout_area_title"><?php _e( 'Choose an Elementor template .zip file, that you exported using "export with images" button'); ?></div>
				<form id="uc_form_import_template" method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>" enctype="multipart/form-data">
					<input type="hidden" name="action" value="unitecreator_elementor_import_template">
					<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce) ?>">
					<fieldset>
						<input type="file" name="file" accept=".json,.zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" required>
						<input type="submit" class="button" value="<?php esc_html_e( 'Import Now', "unlimited-elements-for-elementor"); ?>">
					</fieldset>
				</form>
			</div>
			
		</div>
		<?php 
	}
    
	
    /**
     * Enter description here ...
     */
    public function onAdminFooter(){
		
    	$isTemplatesPage = $this->isElementorLibraryPage();
    	
    	if($isTemplatesPage == false)
    		return(false);
    	
    	$this->putImportLayoutButton();
    	
    }
    
    /**
     * on add scripts to template library page
     */
    public function onAddScripts(){
    	$isTemplatesPage = $this->isElementorLibraryPage();
    	  
    	if($isTemplatesPage == true)
    		HelperUC::addScriptAbsoluteUrl(HelperProviderCoreUC_EL::$urlCore."elementor/assets/template_library_admin.js", "unlimited_addons_template_library_admin");
    }
    
	private function a____________OTHERS___________(){}
    
	/**
	 * get widget icons styles
	 */
	private function getWigetIconsStyles(){
		
		if(empty(self::$arrWidgetIcons))
			return(null);

						
		$styles = "";
		foreach(self::$arrWidgetIcons as $class => $arrWidget){
			
			$urlIcon = UniteFunctionsUC::getVal($arrWidget, "url_icon");
						
			
			$style = "";
			
			$cssAfter = "";
			
			//--- preview related ----
			
			if(self::$showWidgetPreviews == true){
				
				$cssHover = "";
				$urlPreview = UniteFunctionsUC::getVal($arrWidget, "url_preview");
				$text = UniteFunctionsUC::getVal($arrWidget, "text");
				$text = htmlspecialchars($text);
				
				if(!empty($urlPreview)){
					$cssHover .= "background-image:url('{$urlPreview}') !important;";
				
					if(!empty($text))
						$cssHover .= "content:\"{$text}\" !important;";
				}
				
				//constant
				if(!empty($cssHover))
					$style .= ".elementor-element-wrapper:hover .".$class."::after{{$cssHover}}\n";
			}
			
			//--- icon related ---
			
			if(!empty($urlIcon))
				$cssAfter .= "background-image:url('$urlIcon') !important;";
				
			if(!empty($cssAfter))
				$style .= ".".$class."::after{{$cssAfter}}\n";
						
			if(empty($style))
				continue;
			
			$styles .= $style."\n";
		}
		
		
		
		return($styles);
	}
    
	/**
     * on enqueue front end scripts
     */
    public function onEnqueueEditorScripts(){

    	$adminStyleHandle = "unlimited_elements_editor_admin_css";
    	
    	HelperUC::addScriptAbsoluteUrl(HelperProviderCoreUC_EL::$urlCore."elementor/assets/uc_editor_admin.js", "unlimited_elements_editor_admin");
    	HelperUC::addStyleAbsoluteUrl(HelperProviderCoreUC_EL::$urlCore."elementor/assets/uc_editor_admin.css", $adminStyleHandle);
		
    	//select2 sortable
    	HelperUC::addScriptAbsoluteUrl(GlobalsUC::$urlPlugin."js/select2/select2.sortable.js", "select2_sortable_js");
    	HelperUC::addStyleAbsoluteUrl(GlobalsUC::$urlPlugin."js/select2/select2.sortable.css", "select2_sortable_css");
    	
    	
    	$stylesIcons = $this->getWigetIconsStyles();
				
    	if(!empty($stylesIcons))
    		wp_add_inline_style($adminStyleHandle, $stylesIcons);
		
    	//include font awesome
		$urlFontAwesomeCSS = HelperUC::getUrlFontAwesome();
    	
		HelperUC::addStyleAbsoluteUrl($urlFontAwesomeCSS, "font-awesome");
		
    		
    	$script = $this->getEditorPageCustomScripts();
    	UniteProviderFunctionsUC::printCustomScript($script, true);
    }
    
    
    /**
     * on ajax import template
     */
    public function onAjaxImportLayout(){
    	    	
    	try{
    		
	    	$nonce = UniteFunctionsUC::getPostVariable("nonce", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
	    	UniteProviderFunctionsUC::verifyNonce($nonce);
	    	
	    	$arrTempFile = UniteFunctionsUC::getVal($_FILES, "file");
	    	UniteFunctionsUC::validateNotEmpty($arrTempFile,"import file");
	    	
	    	
    		$exporter = new UniteCreatorLayoutsExporterElementor();
	    	$exporter->importElementorTemplateNew($arrTempFile);
	    	
	    	wp_redirect(GlobalsUnlimitedElements::$urlTemplatesList);
	    	exit();
	    	
    	}catch(Exception $e){
    		
    		HelperHtmlUC::outputException($e);
    		exit();
    	}
    	
    	    	
    }
    
    /**
     * log memory usages
     */
    public static function logMemoryUsage($operation, $updateOption=false){
    	
    	if(self::$isLogMemory == false)
    		return(false);
    	
    	HelperUC::logMemoryUsage($operation, $updateOption);
    	
    	$isEnoughtMemory = UniteFunctionsUC::isEnoughtPHPMemory();
    	if($isEnoughtMemory == false)
    		HelperUC::logMemoryUsage("Low Memory!!!", true);
    	
    }
    
    
    
	/**
	 * check the screen
	 */
	private function isTemplatesScreen(){
		global $current_screen;

		if ( ! $current_screen ) {
			return false;
		}
		
		if($current_screen->base == "edit" && $current_screen->post_type == GlobalsUnlimitedElements::POSTTYPE_ELEMENTOR_LIBRARY)
			return(true);
		
		return(false);
	}
    
    
	/**
	 * get export with images link
	 */
	private function getTemplateExportWithAddonsLink($postID){
		
		return add_query_arg(
			[
				'action' => 'unitecreator_elementor_export_template',
				'library_action' => 'export_template_withaddons',
				'source' => 'unlimited-elements',
				'_nonce' => UniteProviderFunctionsUC::getNonce(),
				'template_id' => $postID,
			],
			admin_url( 'admin-ajax.php' )
		);
		
	}
	
	
	/**
	 * return if template support export
	 */
	public function isTemplateSupportExports( $template_id ) {
		$export_support = true;
		
		$export_support = apply_filters( 'elementor/template_library/is_template_supports_export', $export_support, $template_id );
		
		return $export_support;
	}
	
	
	/**
	 * post row actions
	 */
	public function postRowActions($actions, WP_Post $post ){
		
		//-------  validation
		
		$isTemplatesScreen = $this->isTemplatesScreen();
		
		if($isTemplatesScreen == false)
			return($actions);
				
		$postID = $post->ID;
		
		$isSupportExport = $this->isTemplateSupportExports($postID);
		
		if($isSupportExport == false)
			return($actions);
		
		// --------- add action
		
		$actions['export-template-withaddons'] = sprintf( '<a href="%1$s">%2$s</a>', $this->getTemplateExportWithAddonsLink($postID), __( 'Export With Images', "unlimited-elements-for-elementor") );
		
		return($actions);
	}
    
	/**
	 * export template
	 */
	public function onAjaxExportTemplate(){
		
		$nonce = UniteFunctionsUC::getGetVar("_nonce", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		UniteProviderFunctionsUC::verifyNonce($nonce);
		
		$libraryAction = UniteFunctionsUC::getGetVar("library_action", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		if($libraryAction != "export_template_withaddons")
			UniteFunctionsUC::throwError("Wrong action: $libraryAction");
		
		$templateID = UniteFunctionsUC::getGetVar("template_id", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$templateID = (int)$templateID;
		
		$post = get_post($templateID);
		
		if(empty($post))
			UniteFunctionsUC::throwError("template not found");
		
		$postType = $post->post_type;
		if($postType != GlobalsUnlimitedElements::POSTTYPE_ELEMENTOR_LIBRARY)
			UniteFunctionsUC::throwError("wrong post type");
		
		 
		$objExporter = new UniteCreatorLayoutsExporterElementor();
		$objExporter->exportElementorPost($templateID);
		
	}
	
	/**
	 * on the content - clear widget input cache - fix some double render elementor bug
	 */
	public function onTheContent($content){
		
		if(GlobalsProviderUC::$isUnderDynamicTemplateLoop == false)
			UniteCreatorOutput::clearIncludesCache();
		
		return($content);
	}
	
	/**
	 * check allow pagination for single page pagination
	 */
	public function checkAllowWidgetPagination($filterValue, $wp_query){

		$objFilters = new UniteCreatorFiltersProcess();
		$isAjaxRequest = $objFilters->isFrontAjaxRequest();
		
		//get post id from request
		if($isAjaxRequest){
		
			$postID = UniteFunctionsUC::getPostGetVariable("layoutid","",UniteFunctionsUC::SANITIZE_KEY);
			if(empty($postID))
				return($filterValue);
			
			if(is_numeric($postID) == false)
				return(false);
						
		}else{	
			
			//get post id from query
			
			if(is_admin() == true || is_singular() == false)
				return($filterValue);
			
			if(@is_front_page() == true)
				return($filterValue);
			
			if(empty(self::$arrPostsWidgetNames))
				return($filterValue);
			
			$postID = $wp_query->queried_object_id;
			
		}
		
		if(empty($postID))
			return($filterValue);
		
		$document = Plugin::$instance->documents->get( $postID );
		
		if(empty($document))
			return($filterValue);

		$editorData = $document->get_elements_data();				
				
		Plugin::$instance->db->iterate_data($editorData, function( $element ) use ( &$filterValue ) {
							
			$widgetName = UniteFunctionsUC::getVal($element, "widgetType");
			
			if(!empty($widgetName)){
				
				$widgetName = str_replace("ucaddon_", "", $widgetName);
								
				$isPostWidget = isset(self::$arrPostsWidgetNames[$widgetName]);
								
				if($isPostWidget){
					
					$settings = UniteFunctionsUC::getVal($element, "settings");
					
					$paginationType = UniteFunctionsUC::getVal($settings, "pagination_type");
					
					if(!empty($paginationType)){
						$filterValue = true;
						return($filterValue);
					}//if
					
				}//isset 
				
			}//not empty
			
		});//iterate
		
		
		return($filterValue);		
	}
	
	/**
	 * on wpml translation register
	 */
	public function onWpmlTranslateRegister($arrWidgets){
		
    	try{
			
    		$this->preloadElementorDBData();
			
    		$objWpmlIntegrate = new UniteCreatorWpmlIntegrate();
    		
    		$arrUEWidgets = $objWpmlIntegrate->getTranslatableElementorWidgetsFields(self::$arrAddonsRecords);
    		
    		if(!empty($arrUEWidgets))
    			$arrWidgets = array_merge($arrWidgets, $arrUEWidgets);
    			    		
    	}catch(Exception $e){
    		
    	}
		
		
		return($arrWidgets);
	}
	
	/**
	 * get current rendering widget settings
	 */
	public function onGetCurrentRenderingWidgetSettings($settings = array()){
		
		if(empty(GlobalsUnlimitedElements::$currentRenderingWidget))
			return(null);
			
		$widget = GlobalsUnlimitedElements::$currentRenderingWidget;
		
		$arrDynamic = $widget->ueGetDynamicSettingsValues();
		
		return($arrDynamic);
	}
    
	private function a____________INIT_INTEGRATION___________(){}

	/**
	 * check if panel dark mode
	 */
	public static function isElementorPanelDarkMode(){
		
		$uiTheme = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );
		
		if($uiTheme == "dark")
			return(true);

		return(false);
	}
	
	/**
	 * on elementor editor init, set some preferences of the editor
	 */
	public function onEditorInit(){
		
		self::$isDarkMode = self::isElementorPanelDarkMode();
		
    	self::$showWidgetPreviews = HelperProviderCoreUC_EL::getGeneralSetting("enable_panel_previews");
    	self::$showWidgetPreviews = UniteFunctionsUC::strToBool(self::$showWidgetPreviews);
    	
    	self::$enableEditHTMLButton = HelperProviderCoreUC_EL::getGeneralSetting("show_edit_html_button");
    	self::$enableEditHTMLButton = UniteFunctionsUC::strToBool(self::$enableEditHTMLButton);
    	
    	GlobalsProviderUC::$isInsideEditorBackend = true;
    	
	}
	
	
	/**
	 * check and add dynamic loop styles before render
	 */
	public function onBeforeRenderElement($element){
		
		if(!empty(GlobalsUnlimitedElements::$renderingDynamicData)){
			HelperProviderCoreUC_EL::putDynamicLoopElementStyle($element);
		}
	}
	
	/**
	 * on builder content data
	 * check and switch document if available for elementor pro
	 */
	public function onBuilderContentData($document,$second){
		
		if(empty(GlobalsUnlimitedElements::$renderingDynamicData))
			return(false);

		$document = UniteFunctionsUC::getVal(GlobalsUnlimitedElements::$renderingDynamicData, "doc_to_change");
		
		if(empty($document))
			return(false);

		//empty the array for any case, the operation should run once
		
		GlobalsUnlimitedElements::$renderingDynamicData["doc_to_change"] = null;
			
		//check if need to switch or not
		
		$currentDocument = \ElementorPro\Plugin::elementor()->documents->get_current();
		
		if ( $currentDocument instanceof Template_With_Post_Content_interface )
			return(false);
		
		if( $currentDocument instanceof Archive_Template_Interface )
			return(false);

		//if it's a page template - switch to it
		
		\ElementorPro\Plugin::elementor()->documents->switch_to_document( $document );			
			
		
	}
	
	
	
    /**
     * init the elementor integration
     */
    public function initElementorIntegration(){
		
    	$isEnabled = HelperProviderCoreUC_EL::getGeneralSetting("el_enable");
    	$isEnabled = UniteFunctionsUC::strToBool($isEnabled);
    	if($isEnabled == false)
    		return(false);
    	
    	$isPreviewOption = UniteFunctionsUC::getGetVar("elementor-preview", "", UniteFunctionsUC::SANITIZE_KEY);
    	
    	if(!empty($isPreviewOption))
    		self::$isFrontendEditorMode = true;
    	
    	self::$isOutputPage = (GlobalsUC::$is_admin == false);
    	
    	if(self::$isFrontendEditorMode == true)
    		self::$isOutputPage = false;
    	
    	//set if edit mode for widget output
    	self::$isEditMode = HelperUC::isElementorEditMode();
    	
    	GlobalsProviderUC::$isInsideEditor = self::$isEditMode;
    	    	
    	$arrSettingsValues = HelperProviderCoreUC_EL::getGeneralSettingsValues();
    	
    	//detect old elementor version
    	$compare = version_compare(ELEMENTOR_VERSION, '3.7.0');
	    if($compare < 0)
    		$this->isOldElementorVersion = true;
    	
    	//consolidation always false
    	self::$isConsolidated = false;
    			
    	$enableBackgrounds = HelperProviderCoreUC_EL::getGeneralSetting("enable_backgrounds");
    	$enableBackgrounds = UniteFunctionsUC::strToBool($enableBackgrounds);
    	
    	//remove me
    	$enableDynamicVisibility = false;
    	
    	if(GlobalsUC::$inDev == true){	//dynamic visibility
	    	$enableDynamicVisibility = HelperProviderCoreUC_EL::getGeneralSetting("enable_dynamic_visibility");
	    	$enableDynamicVisibility = UniteFunctionsUC::strToBool($enableDynamicVisibility);
    	}
    	    	
    	//disable post_content filtering (in functionsWP)
    	
	    GlobalsProviderUC::$disablePostContentFiltering = HelperProviderCoreUC_EL::getGeneralSetting("disable_post_content_filters");
	    GlobalsProviderUC::$disablePostContentFiltering = UniteFunctionsUC::strToBool(GlobalsProviderUC::$disablePostContentFiltering);

	    
    	add_action('elementor/editor/init', array($this, 'onEditorInit'));
    	    	
    	if($this->isOldElementorVersion == true)
    		add_action('elementor/widgets/widgets_registered', array($this, 'onWidgetsRegistered'));
    	else
    		add_action('elementor/widgets/register', array($this, 'onWidgetsRegistered'));
    	
    	add_action('elementor/frontend/after_register_scripts', array($this, 'onRegisterFrontScripts'), 10);
    	add_action('elementor/editor/after_enqueue_scripts', array($this, 'onEnqueueEditorScripts'), 10);
		
    	if($this->isOldElementorVersion == true)
    		add_action('elementor/controls/controls_registered', array($this, 'onRegisterControls'));
    	else
    		add_action('elementor/controls/register', array($this, 'onRegisterControls'));
    	
    	add_action('elementor/frontend/after_enqueue_scripts', array($this, 'onFrontendAfterRegisterControls'));
    	
    	//add_action("elementor/dynamic_tags/register_tags", array($this, "onRegisterDynamicTags"));
    	
		if($enableBackgrounds == true)
    		$this->initBackgroundWidgets();
    	
    	if($enableDynamicVisibility == true)
    		$this->initDynamicVisibility();
    		
    	add_action('elementor/init', array($this, 'onElementorInit'));
    	
    	//fix some frontend bug with double render
    	add_filter("elementor/frontend/the_content",array($this, "onTheContent"));
		
		add_filter( 'pre_handle_404', array($this, 'checkAllowWidgetPagination' ), 11, 2 );
    	
		//dynamic loop
		add_action( 'elementor/frontend/container/before_render', array($this, "onBeforeRenderElement") );
		add_action( 'elementor/frontend/section/before_render', array($this, "onBeforeRenderElement") );
		add_action( 'elementor/frontend/column/before_render', array($this, 'onBeforeRenderElement') );
		add_action( 'elementor/frontend/widget/before_render', array($this, 'onBeforeRenderElement') );
		
		add_action( 'elementor/frontend/before_get_builder_content', array($this, 'onBuilderContentData'),10,2);
		
		//wpml translation integrattion
		
		add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'onWpmlTranslateRegister' ) );
		
		//get current dynamic settings from loop
		
		add_filter( 'ue_get_current_widget_settings', array( $this, 'onGetCurrentRenderingWidgetSettings' ) );
		
		
    	// ------ admin related only ----------
    	
    	if(is_admin() == false)
    		return(false);
		
    	if($this->enableExportTemplate == true){
		
    		add_filter( 'post_row_actions', array($this, 'postRowActions' ), 20, 2 );
			
    		add_action('wp_ajax_unitecreator_elementor_export_template', array($this, 'onAjaxExportTemplate'));
    		
    	}
    	
    	//import tepmlate
    	if($this->enableImportTemplate == true){
    		
			add_action( 'admin_footer', array($this, 'onAdminFooter') );
			add_action( 'admin_enqueue_scripts', array($this, 'onAddScripts') );
	    	
			add_action('wp_ajax_unitecreator_elementor_import_template', array($this, 'onAjaxImportLayout'));
    	}
    	
    	
    }
    
}
