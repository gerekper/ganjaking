<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class GlobalsUnlimitedElements{
	
	public static $enableInsideNotification = true;
	
	//public static $insideNotificationText = "BLACK FRIDAY SALE STARTS NOW! <br> Grab the PRO version for 50% off. <br> <a href='https://unlimited-elements.com/pricing/' target='_blank'>Get It Now</a> ";
	//public static $insideNotificationText = "BLACK FRIDAY DEAL ENDS SOON! <br> Last chance to get 50% off. <br> <a href='https://unlimited-elements.com/pricing/' target='_blank'>Get It Now</a> ";
	public static $insideNotificationText = "Unlock Access To All PRO Widgets and Features.  <a href='https://unlimited-elements.com/pricing/' target='_blank'>Upgrade Now</a> ";
	public static $insideNotificationUrl = "https://unlimited-elements.com/pricing/";
	
	public static $showAdminNotice = false;
	
	public static $arrAdminNotice = array(
		"id"=>"black_friday_22_last",
		"text"=>"temp text",		//real text goes from event
		"banner"=>"ue-black-friday-banner.jpg",
		//"type"=>"simple",
		"color"=>"info",		//info , error, doubly, warning
		"type"=>"banner",	//advanced,banner
		//"button_text"=>"Show Me More",
		"button_link"=>"https://unlimited-elements.com/pricing/",
		"expire"=>"",
		"free_only"=>true,
		//"condition"=>"no_doubly",
		"internal_only"=>true,		//show only in ue page
		"no-notice-wrap"=>false
	);
	
	/*
	public static $arrAdminNotice = array(
		"id"=>"birthday-3-sale",
		//"text"=>"Birthday Sale IS Here!",
		//"banner"=>"birthday-3-banner.png",
		"type"=>"advanced",
		//"type"=>"banner",	//advanced,banner
		//"button_text"=>"Show Me More",
		"button_link"=>"https://unlimited-elements.com/pricing/",
		"expire"=>"",
		"free_only"=>true,
		"no-notice-wrap"=>true
	);
	*/
	
	const PLUGIN_NAME = "unlimitedelements";
   	const VIEW_ADDONS_ELEMENTOR = "addons_elementor";	
   	const VIEW_LICENSE_ELEMENTOR = "licenseelementor";
   	const VIEW_SETTINGS_ELEMENTOR = "settingselementor";
   	const VIEW_TEMPLATES_ELEMENTOR = "templates_elementor";
   	const VIEW_SECTIONS_ELEMENTOR = "sections_elementor";
   	const VIEW_CUSTOM_POST_TYPES = "custom_posttypes";
   	const VIEW_ICONS = "svg_shapes";
   	const VIEW_BACKGROUNDS = "backgrounds";
   	
   	const LINK_BUY = "https://unlimited-elements.com/pricing/";
   	
   	const SLUG_BUY_BROWSER = "page=unlimitedelements-pricing";
   	
   	const GENERAL_SETTINGS_KEY = "unlimited_elements_general_settings";
   	const ADDONSTYPE_ELEMENTOR = "elementor";
   	const ADDONSTYPE_ELEMENTOR_TEMPLATE = "elementor_template";
   	const ADDONSTYPE_CUSTOM_POSTTYPES = "posttype";
   	
	const PLUGIN_TITLE = "Unlimited Elements";
   	const POSTTYPE_ELEMENTOR_LIBRARY = "elementor_library";
   	const META_TEMPLATE_TYPE = '_elementor_template_type';
   	const META_TEMPLATE_SOURCE = "_unlimited_template_source";	//the value is unlimited
   	const META_TEMPLATE_SOURCE_NAME = "_unlimited_template_sourceid";
   	
	const POSTTYPE_UNLIMITED_ELEMENS_LIBRARY = "unelements_library";
	
   	const ALLOW_FEEDBACK_ONUNINSTALL = false;
   	const EMAIL_FEEDBACK = "support@unitecms.net";
   	
   	const FREEMIUS_PLUGIN_ID = "4036";
   	
   	const LINK_HELP_POSTSLIST = "https://unlimited-elements.helpscoutdocs.com/article/69-post-list-query-usage";
   	
   	const PREFIX_TEMPLATE_PERMALINK = "unlimited-";

   	public static $enableCPT = false;
   	public static $urlTemplatesList;
   	public static $renderingDynamicData;
   	
   	
   	/**
   	 * set doubly notice text
   	 */
   	private static function setDoublyNoticeText(){
   		
   		$urlInstallDoubly = UniteFunctionsWPUC::getInstallPluginLink("doubly");
   				
   		$urlImage = GlobalsUC::$urlPluginImages."logo-circle.svg";
   		
		$textDoubly = "
			<div class='uc-notice-wrapper'>
			<div class='uc-notice-left'>
				<img src='{$urlImage}' width='100'>
			</div>
			<div class='uc-notice-right'>
				<div class='uc-notice-header'>Live Copy Paste from Unlimited Elements</div>
				
				<div class='uc-notice-middle'>
					Did you know that now you can copy fully designed sections from Unlimited Elements to your website for FREE? <br> 
					If you want to try then install our new plugin called Doubly. <br>
				</div>
				
				<a class='uc-notice-button button button-primary' href='{$urlInstallDoubly}'>Install Doubly Now</a>
			</div>
			</div>
		";
   		
   		self::$arrAdminNotice["text"] = $textDoubly;
   		
   	}
   	
   	
   	/**
   	 * on admin init
   	 */
   	public static function onAdminInit(){
		
   		if(self::$showAdminNotice == false)
   			return(false);
   		
   		self::setDoublyNoticeText();
   			
   	}
   	
   	
   	/**
   	 * init globals
   	 */
   	public static function initGlobals(){
   		
   		//remove me
   		//if(GlobalsUC::$inDev == true)
   			//self::$showAdminNotice = true;
   		
   		self::$urlTemplatesList = admin_url("edit.php?post_type=elementor_library&tabs_group=library");
		
   		add_action("init",array("GlobalsUnlimitedElements", "onAdminInit"));
   		
   		
   	}
   	
}


GlobalsUnlimitedElements::initGlobals();

