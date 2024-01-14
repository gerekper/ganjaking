<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class GlobalsUnlimitedElements{

	public static $enableDashboard = true;

	public static $enableForms = true;

	public static $enableGutenbergSupport = false;

	public static $showAdminNotices = false;
	public static $debugAdminNotices = false;

	public static $enableApiIntegrations = true;
	
	public static $enableGoogleAPI = true;
	public static $enableWeatherAPI = false;
	public static $enableCurrencyAPI = true;

	public static $enableGoogleCalendarScopes = false;
	public static $enableGoogleYoutubeScopes = false;

	public static $enableInsideNotification = true;

	public static $enableInstagramErrorMessage = true;
	
	//public static $insideNotificationText = "BLACK FRIDAY SALE STARTS NOW! <br> Grab the PRO version for 50% off. <br> <a href='https://unlimited-elements.com/pricing/' target='_blank'>Get It Now</a> ";
	//public static $insideNotificationText = "Unlimited Elements Birthday Sale!!! <br> 50% OFF - all plans! <br> <a style='text-decoration:underline;' href='https://unlimited-elements.com/pricing/' target='_blank'>Get It Now!</a> ";
	public static $insideNotificationText = "Unlock Access To All PRO Widgets and Features.  <a href='https://unlimited-elements.com/pricing/' target='_blank'>Upgrade Now</a> ";
	public static $insideNotificationUrl = "https://unlimited-elements.com/pricing/";

	const PLUGIN_NAME = "unlimitedelements";
	const VIEW_DASHBOARD = "dashboard";
	const VIEW_ADDONS_ELEMENTOR = "addons_elementor";
	const VIEW_LICENSE_ELEMENTOR = "licenseelementor";
	const VIEW_SETTINGS_ELEMENTOR = "settingselementor";
	const VIEW_TEMPLATES_ELEMENTOR = "templates_elementor";
	const VIEW_SECTIONS_ELEMENTOR = "sections_elementor";
	const VIEW_CUSTOM_POST_TYPES = "custom_posttypes";
	const VIEW_ICONS = "svg_shapes";
	const VIEW_BACKGROUNDS = "backgrounds";
	const VIEW_FORM_ENTRIES = "form_entries";
	const VIEW_CHANGELOG = "changelog";

	const LINK_BUY = "https://unlimited-elements.com/pricing/";

	const SLUG_BUY_BROWSER = "page=unlimitedelements-pricing";

	const GENERAL_SETTINGS_KEY = "unlimited_elements_general_settings";
	const ADDONSTYPE_ELEMENTOR = "elementor";
	const ADDONSTYPE_ELEMENTOR_TEMPLATE = "elementor_template";
	const ADDONSTYPE_CUSTOM_POSTTYPES = "posttype";

	const PLUGIN_TITLE = "Unlimited Elements";
	const POSTTYPE_ELEMENTOR_LIBRARY = "elementor_library";
	const META_TEMPLATE_TYPE = '_elementor_template_type';
	const META_TEMPLATE_SOURCE = "_unlimited_template_source";  //the value is unlimited
	const META_TEMPLATE_SOURCE_NAME = "_unlimited_template_sourceid";

	const POSTTYPE_UNLIMITED_ELEMENS_LIBRARY = "unelements_library";

	const ALLOW_FEEDBACK_ONUNINSTALL = false;
	const EMAIL_FEEDBACK = "support@unitecms.net";

	const FREEMIUS_PLUGIN_ID = "4036";
	
	const GOOGLE_CONNECTION_URL = "https://unlimited-elements.com/google-connect/connect.php";
	const GOOGLE_CONNECTION_CLIENTID = "916742274008-sji12chck4ahgqf7c292nfg2ofp10qeo.apps.googleusercontent.com";
	
	const LINK_HELP_POSTSLIST = "https://unlimited-elements.helpscoutdocs.com/article/69-post-list-query-usage";

	const PREFIX_TEMPLATE_PERMALINK = "unlimited-";

	public static $enableCPT = false;
	public static $urlTemplatesList;
	public static $urlAccount;
	public static $renderingDynamicData;
	public static $currentRenderingWidget;


	/**
	 * init globals
	 */
	public static function initGlobals(){

		//remove me
		//if(GlobalsUC::$inDev === true)
		//self::$showAdminNotices = true;

		self::$urlTemplatesList = admin_url("edit.php?post_type=elementor_library&tabs_group=library");

		self::$urlAccount = admin_url("admin.php?page=unlimitedelements-account");

		UniteProviderFunctionsUC::addAction('admin_init', array("GlobalsUnlimitedElements", 'initAdminNotices'));

		if(self::$enableGutenbergSupport == true)
			self::initGutenbergIntegration();
	
		if(GlobalsUC::$is_admin == true && HelperUC::hasPermissionsFromQuery("showadminnotices"))
			self::$debugAdminNotices = true;
			
	}

	/**
	 * init the admin notices
	 */
	public static function initAdminNotices(){

		if(GlobalsUnlimitedElements::$showAdminNotices === false)
			return;

		UCAdminNotices::init(array(
//			new UCAdminNoticeBanner(),
//			new UCAdminNoticeSimpleExample(),
//			new UCAdminNoticeDoubly(),
//			new UCAdminNoticeRating(),
		));

	}

	/**
	 * init the Gutenberg integration
	 */
	private static function initGutenbergIntegration(){

		$gutenbergIntegrate = UniteCreatorGutenbergIntegrate::getInstance();
		$gutenbergIntegrate->init();
	}

}

GlobalsUnlimitedElements::initGlobals();
