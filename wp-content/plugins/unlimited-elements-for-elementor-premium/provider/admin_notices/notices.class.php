<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

abstract class UCAdminNotices{

	const NOTICES_DISPLAY_LIMIT = 2;

	private static $initialized = false;

	/**
	 * init
	 */
	public static function init($notices){

		$shouldInitialize = self::shouldInitialize();

		if($shouldInitialize === false)
			return;

		self::initializeOptions();

		self::registerNotices($notices);
		self::registerHooks();

		self::checkDismissAction();

		self::$initialized = true;
	}

	/**
	 * display notices
	 */
	public static function displayNotices(){

		$notices = UCAdminNoticesManager::getNotices();
		$displayedCount = 0;

		foreach($notices as $notice){
			$isDebug = $notice->isDebug();

			if($isDebug === true){
				$noticeHtml = $notice->getHtml();

				echo $noticeHtml;

				continue;
			}

			if($displayedCount >= self::NOTICES_DISPLAY_LIMIT)
				return;

			$isDisplayable = $notice->shouldDisplay();

			if($isDisplayable === false)
				continue;

			$displayedCount++;

			$noticeHtml = $notice->getHtml();

			echo $noticeHtml;
		}
	}

	/**
	 * enqueue assets
	 */
	public static function enqueueAssets(){

		HelperUC::addStyleAbsoluteUrl(GlobalsUC::$url_provider . 'assets/admin_notices.css', 'uc_admin_notices');
		HelperUC::addScriptAbsoluteUrl(GlobalsUC::$url_provider . 'assets/admin_notices.js', 'uc_admin_notices');
	}

	/**
	 * check if notices need to be initialized
	 */
	private static function shouldInitialize(){

		if(self::$initialized === true)
			return false;

		if(GlobalsUC::$is_admin === false)
			return false;

		if(current_user_can('administrator') === false)
			return false;

		return true;
	}

	/**
	 * initialize options
	 */
	private static function initializeOptions(){

		// Set plugin installation time
		$installTime = UCAdminNoticesOptions::getOption('install_time');

		if(empty($installTime))
			UCAdminNoticesOptions::setOption('install_time', time());
	}

	/**
	 * check for dismiss action
	 */
	private static function checkDismissAction(){

		$id = UniteFunctionsUC::getPostGetVariable('uc_dismiss_notice', '', UniteFunctionsUC::SANITIZE_KEY);

		if(empty($id))
			return;

		UCAdminNoticesManager::dismissNotice($id);
	}

	/**
	 * register notices
	 */
	private static function registerNotices($notices){

		foreach($notices as $notice){
			UCAdminNoticesManager::addNotice($notice);
		}
	}

	/**
	 * register hooks
	 */
	private static function registerHooks(){

		UniteProviderFunctionsUC::addFilter('admin_notices', array(self::class, 'displayNotices'), 10, 3);
		UniteProviderFunctionsUC::addAction('admin_enqueue_scripts', array(self::class, 'enqueueAssets'));
	}

}
