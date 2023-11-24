<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonRevisioner{

	const FOLDER_NAME = 'unlimited_elements_revisions';
	const MAX_REVISIONS = 10;
	const MAX_TODAY_REVISIONS = 5;

	private static $initialized = false;

	/**
	 * init
	 */
	public static function init(){

		$shouldInitialize = self::shouldInitialize();

		if($shouldInitialize === false)
			return;

		self::registerHooks();

		self::$initialized = true;
	}

	/**
	 * get a list of revision for the addon
	 */
	public function getAddonRevisions($addon){

		$files = $this->getRevisions($addon);
		$files = array_reverse($files); // new at the top
		$revisions = array();

		foreach($files as $file){
			$revisions[] = $this->prepareRevision($file);
		}

		return $revisions;
	}

	/**
	 * create a new revision for the addon
	 */
	public function createAddonRevision($addon){

		$this->createRevision($addon);
		$this->purgeRevisions($addon);
	}

	/**
	 * restore the addon revision
	 */
	public function restoreAddonRevision($addon, $revisionId){

		$revision = $this->getAddonRevision($addon, $revisionId);
		$filePath = $revision['path'];
		$categoryId = null; // autodetect

		// create a new revision before restoring
		$this->createRevision($addon);

		$exporter = new UniteCreatorExporter();
		$exporter->initByAddon($addon);
		$exporter->setMustImportAddonType($addon->getType());
		$log = $exporter->import($categoryId, $filePath, true, false);

		$this->purgeRevisions($addon);

		return $log;
	}

	/**
	 * download the addon revision
	 */
	public function downloadAddonRevision($addon, $revisionId){

		$revision = $this->getAddonRevision($addon, $revisionId);
		$filePath = $revision['path'];
		$fileName = $addon->getName() . ' - ' . $revision['date'] . '.' . $revision['extension'];

		UniteFunctionsUC::downloadFile($filePath, $fileName);
	}

	/**
	 * delete all revisions of the addon
	 */
	public function deleteAddonRevisions($addon){

		$folderPath = $this->getFolderPath($addon);

		UniteFunctionsUC::deleteDir($folderPath);
	}

	/**
	 * check if revisions need to be initialized
	 */
	private static function shouldInitialize(){

		if(self::$initialized === true)
			return false;

		if(GlobalsUC::$is_admin === false)
			return false;

		$isRevisionsEnabled = HelperProviderUC::isAddonRevisionsEnabled();

		if($isRevisionsEnabled === false)
			return false;

		return true;
	}

	/**
	 * register hooks
	 */
	private static function registerHooks(){

		$instance = new self();

		UniteProviderFunctionsUC::addAction(UniteCreatorFilters::ACTION_ADDON_AFTER_UPDATE, array($instance, 'createAddonRevision'));
		UniteProviderFunctionsUC::addAction(UniteCreatorFilters::ACTION_ADDON_AFTER_DELETE, array($instance, 'deleteAddonRevisions'));
	}

	/**
	 * get the revision folder path
	 */
	private function getFolderPath($addon){

		$id = $addon->getID();

		$path = GlobalsUC::$path_images
			. self::FOLDER_NAME . '/'
			. $id . '/';

		return $path;
	}

	/**
	 * generate the revision file name
	 */
	private function generateFileName(){

		$user = wp_get_current_user();
		$time = current_time('timestamp');
		$name = $time . '-' . $user->user_login;

		return $name;
	}

	/**
	 * get a list of revisions
	 */
	private function getRevisions($addon){

		$path = $this->getFolderPath($addon);
		$files = list_files($path, 1);

		sort($files);

		return $files;
	}

	/**
	 * delete previous revisions
	 */
	private function purgeRevisions($addon){

		$files = $this->getRevisions($addon);
		$todayFiles = array();
		$pastFiles = array();

		foreach($files as $file){
			$revision = $this->prepareRevision($file);

			if($revision['is_today'])
				$todayFiles[] = $file;
			else
				$pastFiles[] = $file;
		}

		$maxTodayFiles = max(self::MAX_TODAY_REVISIONS, self::MAX_REVISIONS - count($pastFiles));
		$deleteTodayFiles = array_slice($todayFiles, 0, -$maxTodayFiles);
		$leftFiles = array_diff($files, $deleteTodayFiles);
		$deleteLeftFiles = array_slice($leftFiles, 0, -self::MAX_REVISIONS);
		$deleteFiles = array_merge($deleteTodayFiles, $deleteLeftFiles);

		UniteFunctionsUC::deleteListOfFiles($deleteFiles);
	}

	/**
	 * create a new revision
	 */
	private function createRevision($addon){

		// create revisions folder
		$revisionsPath = GlobalsUC::$path_images . self::FOLDER_NAME . "/";

		UniteFunctionsUC::checkCreateDir($revisionsPath);

		if(is_dir($revisionsPath) === false)
			return;

		// create addon folder
		$folderPath = $this->getFolderPath($addon);

		UniteFunctionsUC::checkCreateDir($folderPath);

		if(is_dir($folderPath) === false)
			return;

		$fileName = $this->generateFileName();

		$exporter = new UniteCreatorExporter();
		$exporter->initByAddon($addon);
		$exporter->setFilename($fileName);
		$exporter->export($folderPath);
	}

	/**
	 * prepare revision data
	 */
	private function prepareRevision($file){

		$info = pathinfo($file);
		$name = explode('-', $info['filename'], 2);
		$time = $name[0];
		$username = $name[1];

		$currentTime = current_time('timestamp');
		$todayStartTime = strtotime('today', $currentTime);
		$todayEndTime = strtotime('tomorrow', $todayStartTime) - 1;

		$date = date('Y-m-d H:i:s', $time);
		$isToday = ($time >= $todayStartTime && $time <= $todayEndTime);

		$revision = array(
			'id' => $info['basename'],
			'path' => $file,
			'time' => $time,
			'date' => $date,
			'extension' => $info['extension'],
			'username' => $username,
			'is_today' => $isToday,
		);

		return $revision;
	}

	/**
	 * get the addon revision
	 */
	private function getAddonRevision($addon, $revisionId){

		$files = $this->getRevisions($addon);
		$revision = null;

		foreach($files as $file){
			if(strpos($file, $revisionId) !== false){
				$revision = $this->prepareRevision($file);

				break;
			}
		}

		return $revision;
	}

}
