<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

abstract class UCAdminNoticeAbstract{

	const LOCATION_EVERYWHERE = 'everywhere';
	const LOCATION_DASHBOARD = 'dashboard';
	const LOCATION_PLUGIN = 'plugin';

	private $location = self::LOCATION_EVERYWHERE;
	private $start = 0;
	private $duration = 0;
	private $freeOnly = false;
	
	/**
	 * get the notice identifier
	 */
	abstract public function getId();

	/**
	 * get the notice html
	 */
	abstract public function getHtml();

	/**
	 * create a new notice instance
	 */
	public function __construct(){

		$this->init();
	}

	/**
	 * check if the notice is in the debug mode
	 */
	public function isDebug(){
		
		if(GlobalsUnlimitedElements::$debugAdminNotices === true)
			return true;

		return false;
	}

	/**
	 * check if the notice should be displayed
	 */
	public function shouldDisplay(){

		$isDebug = $this->isDebug();

		if($isDebug === true)
			return true;

		$isDismissed = $this->isDismissed();

		if($isDismissed === true)
			return false;
		
		$isFreeAllowed = $this->isFreeAllowed();

		if($isFreeAllowed === false)
			return false;

		$isLocationAllowed = $this->isLocationAllowed();

		if($isLocationAllowed === false)
			return false;

		$isConditionAllowed = $this->isConditionAllowed();

		if($isConditionAllowed === false)
			return false;

		$isScheduled = $this->isScheduled();

		if($isScheduled === true)
			return false;

		return true;
	}

	/**
	 * mark the notice as dismissed
	 */
	public function dismiss(){

		$this->setOption('dismissed', true);
	}

	/**
	 * postpone the notice for the given duration (in hours)
	 */
	public function postpone($duration){

		$this->setOption('start_time', time() + $duration * 3600);
		$this->deleteOption('finish_time');
	}

	/**
	 * initialize the notice
	 */
	protected function init(){
		//
	}

	/**
	 * create a new builder instance
	 */
	protected function createBuilder(){

		$id = $this->getId();

		$builder = new UCAdminNoticeBuilder($id);
		$builder = $this->initBuilder($builder);

		return $builder;
	}

	/**
	 * create a new banner builder instance
	 */
	protected function createBannerBuilder(){

		$id = $this->getId();

		$builder = new UCAdminNoticeBannerBuilder($id);
		$builder = $this->initBuilder($builder);

		return $builder;
	}

	/**
	 * check if the notice condition is allowed
	 */
	protected function isConditionAllowed(){

		return true;
	}

	/**
	 * enable the notice free only mode - show only in the free version
	 */
	protected function freeOnly(){

		$this->freeOnly = true;
	}

	/**
	 * set the notice display location
	 */
	protected function setLocation($location){

		$this->location = $location;
	}

	/**
	 * set the notice start offset in hours from the plugin installation
	 */
	protected function setStart($start){

		$this->start = $start;
	}

	/**
	 * set the notice duration offset in hours from the first display
	 */
	protected function setDuration($duration){

		$this->duration = $duration;
	}

	/**
	 * initialize the builder instance
	 */
	private function initBuilder($builder){

		$isDebug = $this->isDebug();

		if($isDebug === true){
			$debugData = $this->getDebugData();

			$builder->debug($debugData);
		}

		return $builder;
	}
	
	
	/**
	 * get the debug data of the notice
	 */
	private function getDebugData(){
		
		$isFreeAllowed = $this->isFreeAllowed();
		
		$id = $this->getId();
		
		if($isFreeAllowed === false)
			return "Notice <b>{$id}</b> hidden - free only";
		
		$isDismissed = $this->isDismissed();
			
		if($isDismissed === true)
			return "Notice <b>{$id}</b> hidden - dismissed";

		$isLocationAllowed = $this->isLocationAllowed();

		if($isLocationAllowed === false)
			return "Notice <b>{$id}</b> hidden - incorrect location";

		$isConditionAllowed = $this->isConditionAllowed();

		if($isConditionAllowed === false)
			return "Notice <b>{$id}</b> hidden - false condition";

		$dateFormat = 'j F Y H:i:s';
		$currentTime = time();
		$startTime = $this->getStartTime();

		if($currentTime < $startTime)
			return "Notice <b>{$id}</b> hidden - scheduled (will be visible on " . date($dateFormat, $startTime) . ")";
			
		$finishTime = $this->getFinishTime();

		if($currentTime <= $finishTime)
			return "Notice <b>{$id}</b> visible (will be hidden on ". date($dateFormat, $finishTime). ")";

		return "Notice <b>{$id}</b> hidden - permanently";
	}

	/**
	 * check if the notice is dismissed
	 */
	private function isDismissed(){

		$isDismissed = $this->getOption('dismissed', false);
		$isDismissed = UniteFunctionsUC::strToBool($isDismissed);

		return $isDismissed;
	}

	/**
	 * check if the notice is allowed for the free version
	 */
	private function isFreeAllowed(){
		
		if($this->freeOnly === true && GlobalsUC::$isProVersion === true)
			return false;

		return true;
	}

	/**
	 * check if the notice location is allowed
	 */
	private function isLocationAllowed(){

		switch($this->location){
			case self::LOCATION_EVERYWHERE:
				return true;
			break;

			case self::LOCATION_DASHBOARD:
				$current_screen = get_current_screen();

				if($current_screen->id === 'dashboard')
					return true;
			break;

			case self::LOCATION_PLUGIN:
				$page = UniteFunctionsUC::getGetVar('page', '', UniteFunctionsUC::SANITIZE_KEY);

				if($page === GlobalsUnlimitedElements::PLUGIN_NAME)
					return true;
			break;
		}

		return false;
	}

	/**
	 * check if the notice is scheduled
	 */
	private function isScheduled(){

		$currentTime = time();
		$startTime = $this->getStartTime();
		$finishTime = $this->getFinishTime();

		if($currentTime >= $startTime && $currentTime <= $finishTime)
			return false;

		return true;
	}

	/**
	 * get the notice start time
	 */
	private function getStartTime(){

		$installTime = intval(UCAdminNoticesOptions::getOption('install_time'));
		$startTime = $this->getOption('start_time');

		if(empty($startTime))
			$startTime = $installTime + $this->start * 3600;

		return $startTime;
	}

	/**
	 * get the notice finish time
	 */
	private function getFinishTime(){

		$currentTime = time();
		$startTime = $this->getStartTime();

		if($currentTime >= $startTime){
			$finishTime = $this->getOption('finish_time');

			if(empty($finishTime)){
				$finishTime = $currentTime + $this->duration * 3600;

				$this->setOption('finish_time', $finishTime);
			}

			return $finishTime;
		}

		return 0;
	}

	/**
	 * get the notice option value
	 */
	private function getOption($key, $fallback = null){

		$value = UCAdminNoticesOptions::getNoticeOption($this->getId(), $key, $fallback);

		return $value;
	}

	/**
	 * set the notice option value
	 */
	private function setOption($key, $value){

		UCAdminNoticesOptions::setNoticeOption($this->getId(), $key, $value);
	}

	/**
	 * delete the notice option value
	 */
	private function deleteOption($key){

		UCAdminNoticesOptions::deleteNoticeOption($this->getId(), $key);
	}

}
