<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCAdminNoticesOptions{

	const OPTIONS_KEY = 'unlimited_elements_notices';

	private static $optionsCache = array();

	/**
	 * get the option value
	 */
	public static function getOption($key, $fallback = null){

		$options = self::getOptions();
		$value = UniteFunctionsUC::getVal($options['options'], $key, $fallback);

		return $value;
	}

	/**
	 * set the option value
	 */
	public static function setOption($key, $value){

		$options = self::getOptions();
		$options['options'][$key] = $value;

		self::setOptions($options);
	}

	/**
	 * get the notice all options
	 */
	public static function getNoticeOptions($id){

		$options = self::getOptions();
		$noticeOptions = UniteFunctionsUC::getVal($options['notices'], $id, array());

		return $noticeOptions;
	}

	/**
	 * get the notice option value
	 */
	public static function getNoticeOption($id, $key, $fallback = null){

		$options = self::getOptions();
		$noticeOptions = self::getNoticeOptions($id);

		$value = UniteFunctionsUC::getVal($noticeOptions, $key, $fallback);

		return $value;
	}

	/**
	 * set the notice option value
	 */
	public static function setNoticeOption($id, $key, $value){

		$options = self::getOptions();
		$noticeOptions = self::getNoticeOptions($id);

		$noticeOptions[$key] = $value;

		$options['notices'][$id] = $noticeOptions;

		self::setOptions($options);
	}

	/**
	 * delete the notice option value
	 */
	public static function deleteNoticeOption($id, $key){

		$options = self::getOptions();
		$noticeOptions = self::getNoticeOptions($id);

		unset($noticeOptions[$key]);

		$options['notices'][$id] = $noticeOptions;

		self::setOptions($options);
	}

	/**
	 * get all options
	 */
	private static function getOptions(){

		if(empty(self::$optionsCache)){
			self::$optionsCache = get_option(self::OPTIONS_KEY, array(
				'options' => array(),
				'notices' => array(),
			));
		}

		return self::$optionsCache;
	}

	/**
	 * set all options
	 */
	private static function setOptions($options){

		self::$optionsCache = $options;

		update_option(self::OPTIONS_KEY, $options);
	}

}
