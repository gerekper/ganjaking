<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorMappickerView{
	
	private $settings, $settingsOutput;
	private $defaultAPIKey = "AIzaSyCFgj4Ipg5KSml6cMMHX4S50oRZ9_TIP34";
	private $apiKey = null;
	private $arrLangs = array('[default]'=>"", 'English' => 'en', 'Arabic' => 'ar', 'Basque' => 'eu', 'Bengali' => 'bn', 'Bulgarian' => 'bg', 'Catalan' => 'ca', 'Chinese (Simplified)' => 'zh-CN', 'Chinese (Traditional)' => 'zh-TW', 'Croatian' => 'hr', 'Czech' => 'cs', 'Danish' => 'da', 'Dutch' => 'nl', 'English (Australian)' => 'en-AU', 'English (Great Britain)' => 'en-GB', 'Farsi' => 'fa', 'Filipino' => 'fil', 'Finnish' => 'fi', 'French' => 'fr', 'Galician' => 'gl', 'German' => 'de', 'Greek' => 'el', 'Gujarati' => 'gu', 'Hebrew' => 'iw', 'Hindi' => 'hi', 'Hungarian' => 'hu', 'Indonesian' => 'id', 'Italian' => 'it', 'Japanese' => 'ja', 'Kannada' => 'kn', 'Korean' => 'ko', 'Latvian' => 'lv', 'Lithuanian' => 'lt', 'Malayalam' => 'ml', 'Marathi' => 'mr', 'Norwegian' => 'no', 'Polish' => 'pl', 'Portuguese' => 'pt', 'Portuguese (Brazil)' => 'pt-BR', 'Portuguese (Portugal)' => 'pt-PT', 'Romanian' => 'ro', 'Russian' => 'ru', 'Serbian' => 'sr', 'Slovak' => 'sk', 'Slovenian' => 'sl', 'Spanish' => 'es', 'Swedish' => 'sv', 'Tagalog' => 'tl', 'Tamil' => 'ta', 'Telugu' => 'te', 'Thai' => 'th', 'Turkish' => 'tr', 'Ukrainian' => 'uk', 'Vietnamese' => 'vi');
	private $urlScript, $urlGoogleScript, $mapData, $settingsData;
	private $defaultLocation = array("lat"=>"-33.8688","lng"=>151.2195);
	private $defaultZoom = 13, $isInited = false;
	private $urlStatic;
	private static $serial = 0;
	
	
	/**
	 * the constructor
	 */
	public function __construct(){
		
		$this->apiKey = HelperUC::getGeneralSetting("google_map_key");
		$this->apiKey = trim($this->apiKey);
		if(empty($this->apiKey))
			$this->apiKey = null;
	}
	
	
	/**
	 * get API key, default or private
	 */
	private function getAPIKey(){
		
		if(!empty($this->apiKey))
			return($this->apiKey);
		
		return($this->defaultAPIKey);
	}
	
	
	/**
	 * init scripts url
	 */
	private function initScripts(){
		
		$rand = rand(10,999999);
		
		$this->urlScript = GlobalsUC::$urlPlugin."js/unitecreator_map_picker.js?rand=".$rand;
		
		$apiKey = $this->getAPIKey();
		
		$this->urlGoogleScript = "https://maps.googleapis.com/maps/api/js?key={$apiKey}&libraries=places";
		
		$this->urlStatic = "https://maps.googleapis.com/maps/api/staticmap?key={$apiKey}";
	}
	
	
	/**
	 * init
	 */
	private function initSettings(){
		
		$settings = new UniteCreatorSettings();
		
		$settings->addSap(esc_html__("Size and Location", "unlimited-elements-for-elementor"));
		
		$settings->addTextBox("width","100%",esc_html__("Map Width","unlimited-elements-for-elementor"),array("unit"=>"px or %"));
		$settings->addTextBox("height","300",esc_html__("Map Height","unlimited-elements-for-elementor"),array("unit"=>"px or %"));
		
		$settings->addHr();
		
		$settings->addTextBox("location", "", "Location");
		
		$params = array(UniteSettingsUC::PARAM_ADDTEXT => "<span id='uc_loader_mylocation' class='loader_round' style='display:none'></span>");
		$settings->addButton("button_my_location", esc_html__("Goto My Location", "unlimited-elements-for-elementor"), UniteSettingsUC::PARAM_NOTEXT, $params);
		
		//--------- add marker
		
		$settings->addSap(esc_html__("Marker", "unlimited-elements-for-elementor"));
		
		$arrMarkerType = array();
		$arrMarkerType["Default Marker"] = "default";
		$arrMarkerType["No Marker"] = "no";
		$arrMarkerType["Shape from Icon"] = "icon";
		$arrMarkerType["Shape from Image"] = "image";
		
		$settings->addSelect("marker_type", $arrMarkerType, "Marker Type","default");
		
		$iconParams = array("icons_type"=>"map");
		$settings->addIconPicker("icon","","Marker Icon", $iconParams);
		$settings->addControl("marker_type", "icon", "show", "icon");
		
		$settings->addImage("marker_image","",esc_html__("Marker Image","unlimited-elements-for-elementor"));
		$settings->addControl("marker_type", "marker_image", "show", "image");
		
		
		$settings->addSap(esc_html__("Style", "unlimited-elements-for-elementor"));
		
		$arrStyles = array(
			"[Standard]"=>"",
			"Silver"=>"silver",
			"Retro"=>"retro",
			"Dark"=>"dark",
			"Night"=>"night",
			"Aubegine"=>"aubegine",
			"Custom"=>"custom"
		);
		
		$settings->addSelect("style", $arrStyles, "Map Style");
		
		//add custom style textarea
		$params = array();
		$params[UniteSettingsUC::PARAM_ADDTEXT] = "<div id='uc-mappicker-style-error' class='unite-color-red' style='display:none;'></div>";
		$settings->addTextArea("style_json", "", "Custom Style", $params);
		$settings->addStaticText("Paste here map style from <a href='https://mapstyle.withgoogle.com/' target='_blank'>Google Map Styler</a> or <a href='https://snazzymaps.com/explore' target='_blank'>Snazzy Maps</a>");
		$settings->addControl("style", "style_json", "show", "custom");
		
		
		//add advanced
		$settings->addHr();
		
		//--- map type
		$arrMapType = array(
			"Roadmap"=> "roadmap",
			"Satellite"=> "satellite",
			"Hybrid"=> "hybrid",
			"Terrain"=> "terrain"
		);
		
		$settings->addSelect("map_type", $arrMapType, "Map Type", "roadmap");
		
		$settings->addSap(esc_html__("Language and Controls", "unlimited-elements-for-elementor"));
		
		//--- language
		$settings->addSelect("language", $this->arrLangs, esc_html__("Language", "unlimited-elements-for-elementor"), "");
		
		$settings->addHr();
		
		$settings->addRadioBoolean("zoomControl", "Show Zoom Control");
		$settings->addRadioBoolean("mapTypeControl", "Show Type Control");
		$settings->addRadioBoolean("streetViewControl", "Show Street View Control");
		$settings->addRadioBoolean("fullscreenControl", "Show Full Screen Control");
		
		//set settings data
		if(!empty($this->settingsData))
			$settings->setStoredValues($this->settingsData);
		
		$this->settingsData = $settings->getArrValues();
		
		$this->settings = $settings;
		
		$this->settingsOutput = new UniteSettingsOutputSidebarUC();
		$this->settingsOutput->init($this->settings);
		
	}
	
	
	/**
	 * put parse icons html from google earty icons page
	 */
	private function putParseIconsHtml(){
		?>
			<div id="icons" style="display:none"></div>
			<div id="icons_parsed"></div>
		<?php 
	}
	
	/**
	 * output html
	 */
	private function outputHtml(){
		
		$data = $this->mapData;
		
		$addHtml = "";
		if(!empty($data))
			$addHtml = UniteFunctionsUC::jsonEncodeForHtmlData($data,"mapdata");
		
		$addOverlay = false;
		if(empty($this->apiKey) || $this->apiKey === $this->defaultAPIKey)
			$addOverlay = true;
		
		$urlGeneralSettings = HelperUC::getViewUrl(GlobalsUC::VIEW_SETTINGS);
		$urlGeneralSettings .= "#tab=fields_settings";
		
		$linkGeneralSettings = HelperHtmlUC::getHtmlLink($urlGeneralSettings, esc_html__("General Settings","unlimited-elements-for-elementor"),"","",true);
		
		?>	
			<div class="uc-mappicker-panels-wrapper">
				
				<?php if($addOverlay == true):?>
				<div class="uc-mappicker-overlay-trans"></div>
				<div class="uc-mappicker-overlay-black"></div>
				<div class="uc-mappicker-overlay-text">
					<?php esc_html_e("For edit the map, please enter your google map API Key in", "unlimited-elements-for-elementor")?> <?php echo UniteProviderFunctionsUC::escAddParam($linkGeneralSettings)?>.
					<br>
					<br>
					You can create your API key in <a href="https://developers.google.com/maps/documentation/javascript/" target="_blank">google map developes page</a>
					
				</div>
				
				<?php endif;?>
				
				<div class="unite-panels-wrapper unite-clearfix">
					<div class="unite-left-panel">
						<?php $this->settingsOutput->draw("uc_settings_map", true); ?>
						<br>
					</div>
					
					<div class="unite-right-panel">
						
						<div id="uc_mappicker_mapwrapper" <?php echo UniteProviderFunctionsUC::escAddParam($addHtml)?> class="uc-mappicker-wrapper">
							<div id="uc_mappicker_map" ></div>
						</div>
					</div>
				</div>
			
			</div>
			
		<?php 
	}
	
	
	/**
	 * output scripts
	 */
	private function outputScripts(){
		
		$urlGoogleScript = $this->getUrlScriptAPI();
		
		$apiKey = $this->getAPIKey();
		
		?>
			<script type="text/javascript" src="<?php echo esc_attr($this->urlScript)?>"></script>
			<script id="uc_mappicker_script" src="<?php echo esc_attr($urlGoogleScript)?>"></script>
			
			<script>
				var g_objMapPicker;

				jQuery(document).ready(function(){
					g_objMapPicker = new UniteCreatorMapPicker();
					g_objMapPicker.initMap("<?php echo esc_attr($apiKey)?>");
				});

				
				/**
				* global function for get data
				*/
				document.getIframeData = function(){
						
					var data = g_objMapPicker.getData();
					
					return(data);
				};
				
			</script>
			
		<?php 
	}
	
	/**
	 * get map data
	 */
	private function getMapData(){
		
		$data = $this->mapData;
		if(empty($data))
			$data = array();
		
		if(!isset($data["center"]))
			$data["center"] = $this->defaultLocation;
		
		if(!isset($data["zoom"]))
			$data["zoom"] = $this->defaultZoom;
		
		return($data);
	}
	
	
	/**
	 * get map date for client side
	 */
	private function getMapDataClientSide(){
		
		$data = $this->getMapData();
		
		$mapData = array();
		
		$mapData["center"] = $data["center"];
		$mapData["zoom"] = $data["zoom"];
		
		return($mapData);
	}
	
	
	/**
	 * get default iframe url
	 */
	private function getUrlStaticMap($width, $height){
		
		$this->init();
		
		$url = $this->urlStatic;
		
		$url .= "&size={$width}x{$height}";
		
		$data = $this->getMapData();
		
		//add center
		$location = UniteFunctionsUC::getVal($data, "center");
		if($location){
			$lat = $location["lat"];
			$lng = $location["lng"];
			$url .= "&center={$lat},{$lng}";
		}
		
		//add zoom
		$zoom = UniteFunctionsUC::getVal($data, "zoom");
		if($zoom)
			$url .= "&zoom={$zoom}";
		
		return($url);
	}
	
	
	/**
	 * get script API url
	 */
	private function getUrlScriptAPI($callback = ""){
		
		$url = $this->urlGoogleScript;
		
		if(!empty($callback))
			$url .= "&callback=$callback";
		
		$language = UniteFunctionsUC::getVal($this->mapData, "lang");
		if(!empty($language))
		 	$url .= "&language=$language";
		
		 	
		return($url);
	}
	
	
	/**
	 * set map and settings data
	 */
	public function setData($data){
		
		if(empty($data))
			return(false);
		
		$this->mapData = UniteFunctionsUC::getVal($data, "map");
		$this->settingsData = UniteFunctionsUC::getVal($data, "settings");
		
	}
	
	/**
	 * get map data
	 */
	public function getStoredMapData(){
		return($this->mapData);
	}
	
	/**
	 * init all the objects
	 */
	private function init(){
		
		if($this->isInited == true)
			return(false);
		
		$this->initSettings();
			
		$this->initScripts();
		
		$this->isInited = true;
	}
	
	
	/**
	 * put html of picker input
	 */
	public function putPickerInputHtml(){
		
		$this->init();
		
		$status = esc_html__("No Map Chosen", "unlimited-elements-for-elementor");
		
		$width = 220;
		$height = 100;
		
		$urlImage = $this->getUrlStaticMap($width, $height);
		
		?>
			<div class="unite-mappicker-chooser-wrapper">
				<img class="unite-mappicker-mapimage" src="<?php echo esc_attr($urlImage)?>" width="<?php echo esc_attr($width)?>" height="<?php echo esc_attr($height)?>">
	        	<div class="unite-mappicker-chooser-overlay"></div>
	        	<a href="javascript:void(0)" class="unite-mappicker-button unite-button-secondary unite-center-position" ><?php esc_html_e("Choose Map", "unlimited-elements-for-elementor")?></a>
			</div>
		
		<?php
	}
	
	
	/**
	 * put html
	 */
	public function putHtml(){
		
		$this->init();
		
		$this->outputHtml();
		$this->outputScripts();
	}
	
	/**
	 * get map output id
	 */
	private function getMapOutputID(){
		
		self::$serial++;
		$random = UniteFunctionsUC::getRandomString(5,true);
		$mapID = "uc_googlemap_output_{$random}_".self::$serial;
		
		return($mapID);
	}
	
	/**
	 * get some setting
	 */
	private function getSetting($name){
		
		if(empty($this->settingsData))
			UniteFunctionsUC::throwError("Settigns data is empty");
		
		if(!isset($this->settingsData[$name]))
			UniteFunctionsUC::throwError("Setting with name: $name not found");
		
		$value = $this->settingsData[$name];
		
		return($value);
	}
	
	
	/**
	 * put map client side
	 */
	private function putHtmlClientSide(){
		
		$mapID = $this->getMapOutputID();
		$functionName = "initMap_".$mapID;
		$url = $this->getUrlScriptAPI($functionName);
		
		//set size
		$width = $this->getSetting("width");
		$height = $this->getSetting("height");
		
		$width = UniteFunctionsUC::normalizeSize($width);
		$height = UniteFunctionsUC::normalizeSize($height);
		
		$css = "#{$mapID}{ width:{$width};height:{$height} }";
				
		$mapDataEncode = $this->getMapDataClientSide();
		$jsonOptions = json_encode($mapDataEncode);
		
		$mapData = $this->getMapData();
		
		//marker
		$marker = UniteFunctionsUC::getVal($mapData, "marker");
		$putMarker = true;
		$isMarkerVisible = UniteFunctionsUC::getVal($marker, "isvisible");
		if($isMarkerVisible === false)
			$putMarker = false;
		
		
		if($putMarker){
			
			$markerLat = UniteFunctionsUC::getVal($marker, "lat");
			$markerLng = UniteFunctionsUC::getVal($marker, "lng");
			
			if(empty($markerLat)){
				$markerLat = $this->defaultLocation["lat"];
				$markerLng = $this->defaultLocation["lng"];
			}
			
			$markerIcon = UniteFunctionsUC::getVal($marker, "icon");
			if($markerIcon)
				$markerIcon = HelperUC::URLtoFull($markerIcon);
		}
		
		$style = UniteFunctionsUC::getVal($mapData, "style");
		$style = trim($style);
		$style = str_replace("\n", "", $style);
		$style = str_replace("\r", "", $style);
		
		$mapTypeID = UniteFunctionsUC::getVal($mapData, "maptypeid");
		
		$lang = UniteFunctionsUC::getVal($mapData,"lang");
		
		?>
		
		<style type="text/css">
			<?php echo UniteProviderFunctionsUC::escCombinedHtml($css)?>
		</style>
		<div id="<?php echo esc_attr($mapID)?>"></div>
		
		<script type="text/javascript">
			function <?php echo UniteProviderFunctionsUC::escCombinedHtml($functionName)?>(){

				g_ucGoogleMapLoaded = true;
				
				var strOptions = '<?php echo UniteProviderFunctionsUC::escCombinedHtml($jsonOptions)?>';
				var mapOptions = JSON.parse(strOptions);
				mapOptions.center.lat = Number(mapOptions.center.lat);
				mapOptions.center.lng = Number(mapOptions.center.lng);
				mapOptions.zoom = Number(mapOptions.zoom);
				
				<?php if($style):?>
				var strStyles = '<?php echo UniteProviderFunctionsUC::escAddParam($style)?>';
				mapOptions.styles = JSON.parse(strStyles);
				<?php endif?>

				<?php if($mapTypeID):?>
				mapOptions.mapTypeId = "<?php echo UniteProviderFunctionsUC::escAddParam($mapTypeID)?>";
				<?php endif?>
				
				var map = new google.maps.Map(document.getElementById("<?php echo UniteProviderFunctionsUC::escAddParam($mapID)?>"), mapOptions);

				<?php if($putMarker):?>
				marker = new google.maps.Marker({
					position:{
						lat: <?php echo UniteProviderFunctionsUC::escAddParam($markerLat)?>,
						lng: <?php echo UniteProviderFunctionsUC::escAddParam($markerLng)?>
					},
					map:map
				<?php if($markerIcon):?>,icon:"<?php echo UniteProviderFunctionsUC::escAddParam($markerIcon)?>"<?php endif?>
				});
				<?php endif?>
			}
			
			
			//include api
			if(typeof g_ucGoogleMapLoading == "undefined" && (typeof google == "undefined" || typeof google.maps == "undefined")){

				g_ucGoogleMapLoaded = false;
				
				var tag = document.createElement('script');
				tag.src = "<?php echo UniteProviderFunctionsUC::escAddParam($url)?>";
				var firstScriptTag = document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
				g_ucGoogleMapLoading = true;
				
			}else{	//just run function
				if(typeof g_ucGoogleMapLoaded != "undefined" && g_ucGoogleMapLoaded == true)
					<?php echo UniteProviderFunctionsUC::escAddParam($functionName)?>();
				else
					g_interval_<?php echo UniteProviderFunctionsUC::escAddParam($mapID)?> = setInterval(function(){
						if(typeof g_ucGoogleMapLoaded != "undefined" && g_ucGoogleMapLoaded == true){
							clearInterval(g_interval_<?php echo UniteProviderFunctionsUC::escAddParam($mapID)?>);
							<?php echo UniteProviderFunctionsUC::escAddParam($functionName)?>();
						}
					},200);
				
			}
			
		</script>
		
		<?php 
		
	}
	
	
	/**
	 * 
	 * get client side html
	 */
	public function getHtmlClientSide($data){
		
		$this->init();
		
		ob_start();
			$this->putHtmlClientSide();
			$content = ob_get_contents();
		ob_end_clean();
		
		return($content);
	}
	
}
