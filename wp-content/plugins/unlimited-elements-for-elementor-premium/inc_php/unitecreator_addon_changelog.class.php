<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorAddonChangelog{

	const TYPE_CHANGE = "change";
	const TYPE_FEATURE = "feature";
	const TYPE_FIX = "fix";
	const TYPE_OTHER = "other";

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	public function getTable(){

		$table = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_CHANGELOG_NAME);

		return $table;
	}

	/**
	 * Get the list of types.
	 *
	 * @return array
	 */
	public function getTypes(){

		$types = array(
			self::TYPE_CHANGE => $this->getTypeTitle(self::TYPE_CHANGE),
			self::TYPE_FEATURE => $this->getTypeTitle(self::TYPE_FEATURE),
			self::TYPE_FIX => $this->getTypeTitle(self::TYPE_FIX),
			self::TYPE_OTHER => $this->getTypeTitle(self::TYPE_OTHER),
		);

		return $types;
	}

	/**
	 * Get the title of the given addon.
	 *
	 * @param int $addonId
	 * @param string $fallback
	 *
	 * @return string
	 */
	public function getAddonTitle($addonId, $fallback){

		try{
			$addon = new UniteCreatorAddon();
			$addon->initByID($addonId);

			return $addon->getTitle();
		}catch(Exception $exception){
			return sprintf(__("%s (deleted)", "unlimited-elements-for-elementor"), $fallback);
		}
	}

	/**
	 * Get the version of the given addon.
	 *
	 * @param int $addonId
	 *
	 * @return string|null
	 */
	public function getAddonVersion($addonId){

		try{
			$addon = new UniteCreatorAddon();
			$addon->initByID($addonId);

			$options = $addon->getOptions();

			if(isset($options["is_free_addon"]) === false)
				return null;

			$isFree = UniteFunctionsUC::getVal($options, "is_free_addon");
			$isFree = UniteFunctionsUC::strToBool($isFree);

			if($isFree === true)
				return __("Free", "unlimited-elements-for-elementor");

			return __("Pro", "unlimited-elements-for-elementor");
		}catch(Exception $exception){
			return null;
		}
	}

	/**
	 * Get a list of changelogs for the given addon.
	 *
	 * @param int $addonId
	 * @param array $filters
	 *
	 * @return array
	 */
	public function getAddonChangelogs($addonId, $filters = array()){

		global $wpdb;

		$sql = "
			SELECT *
			FROM {$this->getTable()}
			WHERE addon_id = %d
			ORDER BY created_at DESC
		";

		$limit = UniteFunctionsUC::getVal($filters, "limit", null);

		if($limit !== null){
			$sql .= " LIMIT " . intval($limit);
		}

		$sql = $wpdb->prepare($sql, array($addonId));
		$results = $wpdb->get_results($sql, ARRAY_A);
		$changelogs = $this->prepareChangelogs($results);

		return $changelogs;
	}

	/**
	 * Find the changelog by the identifier.
	 *
	 * @param int|int[] $id
	 *
	 * @return array
	 */
	public function findChangelog($id){

		global $wpdb;

		$ids = is_array($id) ? $id : array($id);
		$idPlaceholders = UniteFunctionsWPUC::getDBPlaceholders($ids, "%d");

		if(empty($ids) === true)
			return array();

		$sql = "
			SELECT *
			FROM {$this->getTable()}
			WHERE id IN($idPlaceholders)
			ORDER BY created_at DESC
		";

		$sql = $wpdb->prepare($sql, $ids);
		$results = $wpdb->get_results($sql, ARRAY_A);
		$items = $this->prepareChangelogs($results);

		if(is_array($id) === true)
			return $items;

		return reset($items);
	}

	/**
	 * Add a new changelog.
	 *
	 * @param int $addonId
	 * @param string $type
	 * @param string $text
	 *
	 * @return int
	 */
	public function addChangelog($addonId, $type, $text){

		global $wpdb;

		$addon = new UniteCreatorAddon();
		$addon->initByID($addonId);

		$data = array(
			"addon_id" => $addon->getID(),
			"addon_title" => $addon->getTitle(),
			"user_id" => get_current_user_id(),
			"type" => $type,
			"text" => $text,
			"plugin_version" => UNLIMITED_ELEMENTS_VERSION,
			"created_at" => current_time("mysql"),
		);

		$result = $wpdb->insert($this->getTable(), $data);

		return $result;
	}

	/**
	 * Update the given changelog.
	 *
	 * @param int|int[] $id
	 * @param array $data
	 *
	 * @return int
	 * @throws Exception
	 */
	public function updateChangelog($id, $data){

		$result = UniteFunctionsWPUC::processDBTransaction(function() use ($id, $data){

			global $wpdb;

			$ids = is_array($id) ? $id : array($id);
			$table = $this->getTable();
			$result = 0;

			foreach($ids as $id){
				$where = array("id" => $id);
				$result += $wpdb->update($table, $data, $where);
			}

			return $result;
		});

		return $result;
	}

	/**
	 * Delete the changelog permanently.
	 *
	 * @param int|int[] $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function deleteChangelog($id){

		$result = UniteFunctionsWPUC::processDBTransaction(function() use ($id){

			global $wpdb;

			$ids = is_array($id) ? $id : array($id);
			$table = $this->getTable();
			$result = 0;

			foreach($ids as $id){
				$where = array("id" => $id);
				$result += $wpdb->delete($table, $where);
			}

			return $result;
		});

		return $result;
	}

	/**
	 * Get the title of the given type.
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	private function getTypeTitle($type){

		$titles = array(
			self::TYPE_CHANGE => __("Change", "unlimited-elements-for-elementor"),
			self::TYPE_FEATURE => __("Feature", "unlimited-elements-for-elementor"),
			self::TYPE_FIX => __("Fix", "unlimited-elements-for-elementor"),
			self::TYPE_OTHER => __("Other", "unlimited-elements-for-elementor"),
		);

		return UniteFunctionsUC::getVal($titles, $type, $type);
	}

	/**
	 * Prepare changelogs from the given results.
	 *
	 * @param array $results
	 *
	 * @return array
	 */
	private function prepareChangelogs($results){

		$changelogs = array();

		foreach($results as $result){
			$user = get_user_by("id", $result["user_id"]);

			$textHtml = esc_html($result["text"]);
			$textHtml = nl2br($textHtml);

			$changelogs[] = array_merge($result, array(
				"user_username" => $user ? $user->user_login : __("(deleted)", "unlimited-elements-for-elementor"),
				"addon_title" => $this->getAddonTitle($result["addon_id"], $result["addon_title"]),
				"addon_version" => $this->getAddonVersion($result["addon_id"]),
				"type_title" => $this->getTypeTitle($result["type"]),
				"text_html" => $textHtml,
				"created_date" => mysql2date("j F Y H:i:s", $result["created_at"]),
				"created_time" => strtotime($result["created_at"]),
			));
		}

		return $changelogs;
	}

}
