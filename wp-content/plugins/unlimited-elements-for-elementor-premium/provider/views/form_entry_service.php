<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCFormEntryService{

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	public function getTable(){

		$table = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRIES_NAME);

		return $table;
	}

	/**
	 * Get the fields table name.
	 *
	 * @return string
	 */
	public function getFieldsTable(){

		$table = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRY_FIELDS_NAME);

		return $table;
	}

	/**
	 * Format the entry date.
	 *
	 * @return string
	 */
	public function formatEntryDate($date){

		$date = mysql2date("j F Y H:i:s", $date);

		return $date;
	}

	/**
	 * Find the entry by the identifier.
	 *
	 * @param int|array $id
	 *
	 * @return array
	 */
	public function findEntry($id){

		global $wpdb;

		$ids = is_array($id) ? $id : array($id);
		$idPlaceholders = UniteFunctionsWPUC::getDBPlaceholders($ids, "%d");

		if(empty($ids) === true)
			return array();

		// Get fields
		$sql = "
			SELECT *
			FROM {$this->getFieldsTable()}
			WHERE entry_id IN($idPlaceholders)
			ORDER BY id ASC
		";

		$sql = $wpdb->prepare($sql, $ids);
		$results = $wpdb->get_results($sql, ARRAY_A);
		$fields = array();

		foreach($results as $result){
			$key = $result["entry_id"];

			if(empty($fields[$key]) === true){
				$fields[$key] = array();
			}

			$fields[$key][] = $result;
		}

		// Get entries
		$sql = "
			SELECT *
			FROM {$this->getTable()}
			WHERE id IN($idPlaceholders)
			ORDER BY FIELD(id, $idPlaceholders)
		";

		$sql = $wpdb->prepare($sql, array_merge($ids, $ids));
		$results = $wpdb->get_results($sql, ARRAY_A);
		$entries = array();

		foreach($results as $result){
			$entryFields = $fields[$result["id"]];
			$entryMain = $this->getEntryMainField($entryFields);
			$entryRead = $result["seen_at"] !== null;

			$entries[] = array_merge($result, array(
				"main" => $entryMain,
				"is_read" => $entryRead,
				"fields" => $entryFields,
			));
		}

		if(is_array($id) === true)
			return $entries;

		return reset($entries);
	}

	/**
	 * Mark the entry as read.
	 *
	 * @param int|array $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function readEntry($id){

		$data = array("seen_at" => current_time("mysql"));
		$result = $this->updateEntry($id, $data);

		return $result;
	}

	/**
	 * Mark the entry as unread.
	 *
	 * @param int|array $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function unreadEntry($id){

		$data = array("seen_at" => null);
		$result = $this->updateEntry($id, $data);

		return $result;
	}

	/**
	 * Put the entry to trash.
	 *
	 * @param int|array $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function trashEntry($id){

		$data = array("deleted_at" => current_time("mysql"));
		$result = $this->updateEntry($id, $data);

		return $result;
	}

	/**
	 * Restore the entry from trash.
	 *
	 * @param int|array $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function untrashEntry($id){

		$data = array("deleted_at" => null);
		$result = $this->updateEntry($id, $data);

		return $result;
	}

	/**
	 * Delete the entry permanently.
	 *
	 * @param int|array $id
	 *
	 * @return int
	 * @throws Exception
	 */
	public function deleteEntry($id){

		$result = UniteFunctionsWPUC::processDBTransaction(function() use ($id){

			global $wpdb;

			$ids = is_array($id) ? $id : array($id);
			$table = $this->getTable();
			$fieldsTable = $this->getFieldsTable();
			$result = 0;

			foreach($ids as $id){
				$where = array("entry_id" => $id);
				$wpdb->delete($fieldsTable, $where);

				$where = array("id" => $id);
				$result += $wpdb->delete($table, $where);
			}

			return $result;
		});

		return $result;
	}

	/**
	 * Update the given entry.
	 *
	 * @param int|array $id
	 * @param array $data
	 *
	 * @return int
	 * @throws Exception
	 */
	private function updateEntry($id, $data){

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
	 * Get the entry's main field.
	 *
	 * @param array $fields
	 *
	 * @return string
	 */
	private function getEntryMainField($fields){

		$main = null;

		foreach($fields as $field){
			$isEmailField = strtolower($field["name"]) === "email";

			if($isEmailField === true){
				$main = $field;

				break;
			}

			$isValidEmail = UniteFunctionsUC::isEmailValid($field["value"]);

			if($isValidEmail === true){
				$main = $field;

				break;
			}
		}

		if($main === null)
			$main = reset($fields); // fallback to the first field

		if($main["value"] === "")
			$main["value"] = __("(empty)", "unlimited-elements-for-elementor");

		return $main;
	}

}
