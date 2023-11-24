<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticesManager{

	private static $notices = array();

	/**
	 * get all notices
	 */
	public static function getNotices(){

		return self::$notices;
	}

	/**
	 * add a notice instance
	 */
	public static function addNotice($notice){

		self::$notices[$notice->getId()] = $notice;
	}

	/**
	 * mark the notice as dismissed
	 */
	public static function dismissNotice($id){

		$notice = self::getNotice($id);

		if($notice === null)
			return;

		$notice->dismiss();
	}

	/**
	 * postpone the notice for the given duration (in hours)
	 */
	public static function postponeNotice($id, $duration){

		$notice = self::getNotice($id);

		if($notice === null)
			return;

		$notice->postpone($duration);
	}

	/**
	 * get the notice by identifier
	 */
	private static function getNotice($id){

		if(empty(self::$notices[$id]))
			return null;

		return self::$notices[$id];
	}

}
