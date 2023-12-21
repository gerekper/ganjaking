<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCFormEntriesView extends WP_List_Table{

	const ACTION_VIEW = "view";
	const ACTION_READ = "read";
	const ACTION_UNREAD = "unread";
	const ACTION_TRASH = "trash";
	const ACTION_UNTRASH = "untrash";
	const ACTION_DELETE = "delete";
	const ACTION_EXPORT = "export";

	const FILTER_ID = "entry";
	const FILTER_STATUS = "status";
	const FILTER_SEARCH = "filter_search";
	const FILTER_FORM = "filter_form";
	const FILTER_PAGE = "filter_page";
	const FILTER_DATE = "filter_date";

	const STATUS_ALL = "all";
	const STATUS_UNREAD = "unread";
	const STATUS_READ = "read";
	const STATUS_TRASH = "trash";

	const DATE_TODAY = "today";
	const DATE_YESTERDAY = "yesterday";
	const DATE_LAST_7 = "last_7";
	const DATE_LAST_30 = "last_30";

	private $service;

	/**
	 * Constructor.
	 *
	 * @param array|string $args
	 *
	 * @return void
	 */
	public function __construct($args = array()){

		$this->service = new UCFormEntryService();

		parent::__construct($args);
	}

	/**
	 * Process the action.
	 *
	 * @return void
	 */
	public function processAction(){

		$generalQueryArgs = array("_wp_http_referer", "_wpnonce", "ucwindow");
		$actionQueryArgs = array("action", "action2", self::FILTER_ID);

		$action = $this->current_action();

		if($action === self::ACTION_EXPORT){
			$this->processExportAction();
			exit;
		}

		$ids = $this->getFilter(self::FILTER_ID);

		if(empty($ids) === false){
			$ids = is_array($ids) ? $ids : array($ids);
			$result = false;

			switch($action){
				case self::ACTION_READ:
					$result = $this->processReadAction($ids);
				break;
				case self::ACTION_UNREAD:
					$result = $this->procesUnreadAction($ids);
				break;
				case self::ACTION_TRASH:
					$result = $this->processTrashAction($ids);
				break;
				case self::ACTION_UNTRASH:
					$result = $this->processUntrashAction($ids);
				break;
				case self::ACTION_DELETE:
					$result = $this->processDeleteAction($ids);
				break;
			}

			if(empty($result) === false){
				$url = wp_get_referer();
				$url = remove_query_arg($actionQueryArgs, $url);

				wp_redirect($url);
				exit;
			}
		}

		$containedQueryArgs = array_intersect($generalQueryArgs, array_keys($_REQUEST));

		if(empty($containedQueryArgs) === false){
			$url = wp_unslash($_SERVER["REQUEST_URI"]);
			$url = remove_query_arg(array_merge($generalQueryArgs, $actionQueryArgs), $url);

			wp_redirect($url);
			exit;
		}
	}

	/**
	 * Gets the current action.
	 *
	 * @return string|false
	 */
	public function current_action(){

		if(isset($_REQUEST[self::ACTION_EXPORT]))
			return self::ACTION_EXPORT;

		return parent::current_action();
	}

	/**
	 * Gets a list of columns.
	 *
	 * @return array
	 */
	public function get_columns(){

		$columns = array(
			"cb" => '<input type="checkbox" />',
			"main" => __("Main", "unlimited-elements-for-elementor"),
			"form" => __("Form", "unlimited-elements-for-elementor"),
			"page" => __("Page", "unlimited-elements-for-elementor"),
			"id" => __("ID", "unlimited-elements-for-elementor"),
			"date" => __("Date", "unlimited-elements-for-elementor"),
		);

		return $columns;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items(){

		$this->processAction();

		// prepare columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		// prepare items and pagination
		$data = $this->prepareData();
		$limit = $this->getLimit();

		$this->items = $data["items"];

		$this->set_pagination_args([
			"total_items" => $data["total"],
			"per_page" => $limit,
		]);
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @return void
	 */
	public function no_items(){

		echo __("No entries found.", "unlimited-elements-for-elementor");
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @param array $item
	 *
	 * @return void
	 */
	public function single_row($item){

		echo '<tr style="font-weight: ' . ($item["is_read"] === true ? "inherit" : "bold") . '">';
		$this->single_row_columns($item);
		echo '</tr>';
	}

	/**
	 * Displays the table.
	 *
	 * @return void
	 */
	public function display(){

		$this->prepare_items();

		$this->displayHeader();

		?>
		<form method="get">
			<?php $this->displayHiddenFields(); ?>
			<?php $this->displaySearchBox(); ?>
			<?php $this->views(); ?>
			<?php parent::display(); ?>
		</form>
		<?php
	}

	/**
	 * Gets the list of views available on this table.
	 *
	 * @return array
	 */
	protected function get_views(){

		$statuses = array(
			self::STATUS_ALL => __("All", "unlimited-elements-for-elementor"),
			self::STATUS_UNREAD => __("Unread", "unlimited-elements-for-elementor"),
			self::STATUS_READ => __("Read", "unlimited-elements-for-elementor"),
			self::STATUS_TRASH => __("Trash", "unlimited-elements-for-elementor"),
		);

		$links = array();
		$counts = $this->countStatuses();
		$selectedStatus = $this->getFilter(self::FILTER_STATUS);

		foreach($statuses as $status => $label){
			$count = $counts[$status];

			if($status !== self::STATUS_ALL && $count === 0)
				continue;

			$links[$status] = array(
				"url" => add_query_arg("status", $status),
				"label" => $label . ' <span class="count">(' . $count . ')</span>',
				"current" => $status === $selectedStatus,
			);
		}

		return $this->get_views_links($links);
	}

	/**
	 * Get a list of bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions(){

		$actions = array(
			self::ACTION_READ => __("Mark as Read", "unlimited-elements-for-elementor"),
			self::ACTION_UNREAD => __("Mark as Unread", "unlimited-elements-for-elementor"),
			self::ACTION_TRASH => __("Move to Trash", "unlimited-elements-for-elementor"),
		);

		return $actions;
	}

	/**
	 * Gets a list of sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns(){

		$columns = array(
			"id" => array("id", "desc"),
			"date" => array("date", "desc"),
		);

		return $columns;
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_cb($item){

		return '<input type="checkbox" name="' . self::FILTER_ID . '[]" value="' . esc_attr($item["id"]) . '" />';
	}

	/**
	 * Renders the id column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_id($item){

		return $item["id"];
	}

	/**
	 * Renders the main column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_main($item){

		$id = $item["id"];
		$label = $item["main"]["value"];
		$status = $this->getFilter(self::FILTER_STATUS);

		$form = new UniteCreatorForm();
		$files = array();

		foreach($item["fields"] as $field){
			if($field["type"] === UniteCreatorForm::TYPE_FILES && empty($field["value"]) === false){
				$urls = $form->decodeFilesFieldValue($field["value"]);
				$names = array_map("basename", $urls);

				$files = array_merge($files, $names);
			}
		}

		if($status === self::STATUS_TRASH){
			$content = $label;

			$actions = array(
				self::ACTION_UNTRASH => $this->getActionLink(self::ACTION_UNTRASH, $id, __("Restore", "unlimited-elements-for-elementor")),
				self::ACTION_DELETE => $this->getActionLink(self::ACTION_DELETE, $id, __("Delete Permanently", "unlimited-elements-for-elementor")),
			);
		}else{
			$content = $this->getActionLink(self::ACTION_VIEW, $id, $label);

			$actions = array(
				self::ACTION_VIEW => $this->getActionLink(self::ACTION_VIEW, $id, __("View", "unlimited-elements-for-elementor")),
				self::ACTION_TRASH => $this->getActionLink(self::ACTION_TRASH, $id, __("Trash", "unlimited-elements-for-elementor")),
			);

			if($item["is_read"] === true)
				$actions[self::ACTION_UNREAD] = $this->getActionLink(self::ACTION_UNREAD, $id, __("Mark as Unread", "unlimited-elements-for-elementor"));
			else
				$actions[self::ACTION_READ] = $this->getActionLink(self::ACTION_READ, $id, __("Mark as Read", "unlimited-elements-for-elementor"));
		}

		if(empty($files) === false)
			$content = '<span class="dashicons dashicons-paperclip" title="' . esc_attr(implode(", ", $files)) . '" style="width: 1em; height: 1em; font-size: 1em; vertical-align: middle"></span> ' . $content;

		return $content . '<div style="font-weight: normal">' . $this->row_actions($actions) . '</div>';
	}

	/**
	 * Renders the form column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_form($item){

		return $item["form_name"];
	}

	/**
	 * Renders the page column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_page($item){

		return '<a href="' . esc_attr($item["post_url"]) . '" target="_blank">' . esc_html($item["post_title"]) . '</a>';
	}

	/**
	 * Renders the date column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_date($item){

		return $this->service->formatEntryDate($item["created_at"]);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which
	 *
	 * return void
	 */
	protected function extra_tablenav($which){

		if($which !== "top")
			return;

		$forms = $this->getFormsFilter();
		$posts = $this->getPostsFilter();
		$dates = $this->getDatesFilter();

		$selectedForm = $this->getFilter(self::FILTER_FORM);
		$selectedPost = $this->getFilter(self::FILTER_PAGE);
		$selectedDate = $this->getFilter(self::FILTER_DATE);

		?>
		<div class="alignleft actions">

			<label class="screen-reader-text" for="filter-form">
				<?php echo esc_html__("Filter by form", "unlimited-elements-for-elementor"); ?>
			</label>
			<select id="filter-form" name="<?php echo esc_attr(self::FILTER_FORM); ?>">
				<option value=""><?php echo esc_html__("All Forms", "unlimited-elements-for-elementor"); ?></option>
				<?php foreach($forms as $value => $label): ?>
					<option
						value="<?php echo esc_attr($value); ?>"
						<?php echo $value === $selectedForm ? "selected" : ""; ?>
					>
						<?php echo esc_html($label); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label class="screen-reader-text" for="filter-page">
				<?php echo esc_html__("Filter by page", "unlimited-elements-for-elementor"); ?>
			</label>
			<select id="filter-page" name="<?php echo esc_attr(self::FILTER_PAGE); ?>">
				<option value=""><?php echo esc_html__("All Pages", "unlimited-elements-for-elementor"); ?></option>
				<?php foreach($posts as $value => $label): ?>
					<option
						value="<?php echo esc_attr($value); ?>"
						<?php echo $value === $selectedPost ? "selected" : ""; ?>
					>
						<?php echo esc_html($label); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<label class="screen-reader-text" for="filter-date">
				<?php echo esc_html__("Filter by date", "unlimited-elements-for-elementor"); ?>
			</label>
			<select id="filter-date" name="<?php echo esc_attr(self::FILTER_DATE); ?>">
				<option value=""><?php echo esc_html__("All Time", "unlimited-elements-for-elementor"); ?></option>
				<?php foreach($dates as $value => $label): ?>
					<option
						value="<?php echo esc_attr($value); ?>"
						<?php echo $value === $selectedDate ? "selected" : ""; ?>
					>
						<?php echo esc_html($label); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<?php submit_button(__("Filter", "unlimited-elements-for-elementor"), "", "", false, array("id" => "filter-submit")); ?>

		</div>
		<div class="alignright" style="margin-left: 8px;">
			<?php submit_button(__("Export to CSV", "unlimited-elements-for-elementor"), "primary", self::ACTION_EXPORT, false, array("id" => "export-submit")); ?>
		</div>
		<?php
	}

	/**
	 * Process the read action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function processReadAction($ids){

		$result = $this->service->readEntry($ids);

		return $result;
	}

	/**
	 * Process the unread action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function procesUnreadAction($ids){

		$result = $this->service->unreadEntry($ids);

		return $result;
	}

	/**
	 * Process the trash action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function processTrashAction($ids){

		$result = $this->service->trashEntry($ids);

		return $result;
	}

	/**
	 * Process the untrash action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function processUntrashAction($ids){

		$result = $this->service->untrashEntry($ids);

		return $result;
	}

	/**
	 * Process the delete action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function processDeleteAction($ids){

		$result = $this->service->deleteEntry($ids);

		return $result;
	}

	/**
	 * Process the export action.
	 *
	 * @return void
	 */
	private function processExportAction(){

		global $wpdb;

		$filters = $this->getFilters();

		$sql = "
			SELECT id
			FROM {$this->service->getTable()}
			WHERE {$this->getWhere($filters)}
			ORDER BY {$this->getOrderBy()}
		";

		$ids = $wpdb->get_col($sql);
		$entries = $this->service->findEntry($ids);

		$entryHeaders = array(
			"id" => __("Entry ID", "unlimited-elements-for-elementor"),
			"form_name" => __("Form", "unlimited-elements-for-elementor"),
			"post_title" => __("Page", "unlimited-elements-for-elementor"),
			"post_url" => __("Page URL", "unlimited-elements-for-elementor"),
			"created_at" => __("Date", "unlimited-elements-for-elementor"),
			"user_id" => __("User ID", "unlimited-elements-for-elementor"),
			"user_ip" => __("User IP", "unlimited-elements-for-elementor"),
			"user_agent" => __("User Agent", "unlimited-elements-for-elementor"),
		);

		$fieldHeaders = array();
		$rows = array();

		foreach($entries as $entry){
			$row = $entry;

			foreach($entry["fields"] as $field){
				$key = "field:{$field["title"]}";

				$fieldHeaders[$key] = $field["title"];
				$row[$key] = $field["value"];
			}

			$rows[] = $row;
		}

		$filename = "unlimited-elements-export-" . current_time("mysql");
		$headers = array_merge($fieldHeaders, $entryHeaders);

		UniteFunctionsUC::downloadCsv($filename, $headers, $rows);
	}

	/**
	 * Prepares the list of items.
	 *
	 * @return array
	 */
	private function prepareData(){

		global $wpdb;

		$filters = $this->getFilters();
		$table = $this->service->getTable();
		$where = $this->getWhere($filters);

		$sql = "
			SELECT COUNT(*)
			FROM $table
			WHERE $where
		";

		$total = $wpdb->get_var($sql);

		$sql = "
			SELECT id
			FROM $table
			WHERE $where
			ORDER BY {$this->getOrderBy()}
			LIMIT {$this->getLimit()}
			OFFSET {$this->getOffset()}
		";

		$ids = $wpdb->get_col($sql);
		$items = $this->service->findEntry($ids);

		$data = array(
			"items" => $items,
			"total" => $total,
		);

		return $data;
	}

	/**
	 * Get the list of forms for the filter.
	 *
	 * @return array
	 */
	private function getFormsFilter(){

		global $wpdb;

		$sql = "
			SELECT form_name
			FROM {$this->service->getTable()}
			GROUP BY form_name
			ORDER BY form_name
		";

		$results = $wpdb->get_results($sql);
		$items = array();

		foreach($results as $result){
			$items[$result->form_name] = $result->form_name;
		}

		return $items;
	}

	/**
	 * Get the list of posts for the filter.
	 *
	 * @return array
	 */
	private function getPostsFilter(){

		global $wpdb;

		$sql = "
			SELECT post_title
			FROM {$this->service->getTable()}
			GROUP BY post_title
			ORDER BY post_title
		";

		$results = $wpdb->get_results($sql);
		$items = array();

		foreach($results as $result){
			$items[$result->post_title] = $result->post_title;
		}

		return $items;
	}

	/**
	 * Get the list of dates for the filter.
	 *
	 * @return array
	 */
	private function getDatesFilter(){

		$items = array(
			self::DATE_TODAY => __("Today", "unlimited-elements-for-elementor"),
			self::DATE_YESTERDAY => __("Yesterday", "unlimited-elements-for-elementor"),
			self::DATE_LAST_7 => __("Last 7 days", "unlimited-elements-for-elementor"),
			self::DATE_LAST_30 => __("Last 30 days", "unlimited-elements-for-elementor"),
		);

		return $items;
	}

	/**
	 * Get the named dates range.
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	private function getDatesRange($key){

		$currentTime = current_time("timestamp");
		$startTime = strtotime("today", $currentTime);
		$endTime = strtotime("tomorrow", $startTime) - 1;

		switch($key){
			case self::DATE_YESTERDAY:
				$startTime = strtotime("yesterday", $currentTime);
				$endTime = strtotime("tomorrow", $startTime) - 1;
			break;
			case self::DATE_LAST_7:
				$startTime = strtotime("-6 days", $startTime);
			break;
			case self::DATE_LAST_30:
				$startTime = strtotime("-29 days", $startTime);
			break;
		}

		$range = array(
			"start" => date("Y-m-d H:i:s", $startTime),
			"end" => date("Y-m-d H:i:s", $endTime),
		);

		return $range;
	}

	/**
	 * Get the action link.
	 *
	 * @param string $action
	 * @param int $id
	 * @param string $label
	 *
	 * @return string
	 */
	private function getActionLink($action, $id, $label){

		$url = array();
		$url["page"] = $_REQUEST["page"];
		$url["action"] = $action;
		$url[self::FILTER_ID] = $id;

		if(empty($_REQUEST["view"]) === false)
			$url["view"] = $_REQUEST["view"];

		if($action !== self::ACTION_VIEW)
			$url["ucwindow"] = "blank";

		return '<a href="?' . http_build_query($url) . '">' . esc_html($label) . '</a>';
	}

	/**
	 * Get the filter values.
	 *
	 * @return array
	 */
	private function getFilters(){

		$keys = array(
			self::FILTER_ID,
			self::FILTER_STATUS,
			self::FILTER_SEARCH,
			self::FILTER_FORM,
			self::FILTER_PAGE,
			self::FILTER_DATE,
		);

		$filters = array();

		foreach($keys as $key){
			$filters[$key] = $this->getFilter($key);
		}

		return $filters;
	}

	/**
	 * Get the filter value.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	private function getFilter($key){

		$value = null;

		switch($key){
			case self::FILTER_ID:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_ID, null, UniteFunctionsUC::SANITIZE_NOTHING);
			break;
			case self::FILTER_STATUS:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_STATUS, self::STATUS_ALL, UniteFunctionsUC::SANITIZE_KEY);
			break;
			case self::FILTER_SEARCH:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_SEARCH, null, UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			break;
			case self::FILTER_FORM:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_FORM, null, UniteFunctionsUC::SANITIZE_NOTHING);
			break;
			case self::FILTER_PAGE:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_PAGE, null, UniteFunctionsUC::SANITIZE_NOTHING);
			break;
			case self::FILTER_DATE:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_DATE, null, UniteFunctionsUC::SANITIZE_KEY);
			break;
		}

		return $value;
	}

	/**
	 * Get the statuses count.
	 *
	 * @return array
	 */
	private function countStatuses(){

		global $wpdb;

		$filters = $this->getFilters();

		// should not filter by status
		unset($filters[self::FILTER_STATUS]);

		$sql = "
			SELECT
			    SUM(IF({$this->getStatusWhere(self::STATUS_ALL)}, 1, 0)) AS '" . self::STATUS_ALL . "',
			    SUM(IF({$this->getStatusWhere(self::STATUS_UNREAD)}, 1, 0)) AS '" . self::STATUS_UNREAD . "',
			    SUM(IF({$this->getStatusWhere(self::STATUS_READ)}, 1, 0)) AS '" . self::STATUS_READ . "',
			    SUM(IF({$this->getStatusWhere(self::STATUS_TRASH)}, 1, 0)) AS '" . self::STATUS_TRASH . "'
			FROM {$this->service->getTable()}
			WHERE {$this->getWhere($filters)}
		";

		$results = $wpdb->get_row($sql);
		$counts = array();

		foreach($results as $status => $count){
			$counts[$status] = intval($count);
		}

		return $counts;
	}

	/**
	 * Get the where clause.
	 *
	 * @param array $filters
	 *
	 * @return string
	 */
	private function getWhere(array $filters){

		global $wpdb;

		$where = "1 = 1";

		$id = UniteFunctionsUC::getVal($filters, self::FILTER_ID, null);

		if(empty($id) === false){
			$ids = is_array($id) ? $id : array($id);
			$placeholders = UniteFunctionsWPUC::getDBPlaceholders($ids, "%d");
			$where .= $wpdb->prepare(" AND id IN($placeholders)", $ids);
		}

		$status = UniteFunctionsUC::getVal($filters, self::FILTER_STATUS, null);

		if(empty($status) === false)
			$where .= " AND {$this->getStatusWhere($status)}";

		$search = UniteFunctionsUC::getVal($filters, self::FILTER_SEARCH, null);

		if(empty($search) === false){
			$search = "%{$wpdb->esc_like($search)}%";
			$fieldsTable = $this->service->getFieldsTable();

			$where .= $wpdb->prepare(
				" AND (
						form_name LIKE %s
						OR post_title LIKE %s
						OR (
							SELECT GROUP_CONCAT($fieldsTable.value)
							FROM $fieldsTable
							WHERE $fieldsTable.entry_id = {$this->service->getTable()}.id
							GROUP BY $fieldsTable.entry_id
						) LIKE %s
					)",
				array($search, $search, $search)
			);
		}

		$form = UniteFunctionsUC::getVal($filters, self::FILTER_FORM, null);

		if(empty($form) === false)
			$where .= $wpdb->prepare(" AND form_name = %s", array($form));

		$post = UniteFunctionsUC::getVal($filters, self::FILTER_PAGE, null);

		if(empty($post) === false)
			$where .= $wpdb->prepare(" AND post_title = %s", array($post));

		$date = UniteFunctionsUC::getVal($filters, self::FILTER_DATE, null);

		if(empty($date) === false){
			$range = $this->getDatesRange($date);
			$where .= $wpdb->prepare(" AND created_at >= %s AND created_at <= %s", array($range["start"], $range["end"]));
		}

		return $where;
	}

	/**
	 * Get the where clause for the status.
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	private function getStatusWhere($status){

		switch($status){
			case self::STATUS_UNREAD:
				$where = "seen_at IS NULL AND deleted_at IS NULL";
			break;
			case self::STATUS_READ:
				$where = "seen_at IS NOT NULL AND deleted_at IS NULL";
			break;
			case self::STATUS_TRASH:
				$where = "deleted_at IS NOT NULL";
			break;
			default:
				$where = "deleted_at IS NULL";
			break;
		}

		return $where;
	}

	/**
	 * Get the sorting clause.
	 *
	 * @return string
	 */
	private function getOrderBy(){

		$orderBy = "{$this->getSortingField()} {$this->getSortingOrder()}";

		return $orderBy;
	}

	/**
	 * Get the sorting field.
	 *
	 * @return string
	 */
	private function getSortingField(){

		$fields = array(
			"id" => "id",
			"date" => "created_at",
		);

		$field = UniteFunctionsUC::getGetVar("orderby", null, UniteFunctionsUC::SANITIZE_KEY);
		$field = UniteFunctionsUC::getVal($fields, $field, "id");

		return $field;
	}

	/**
	 * Get the sorting order.
	 *
	 * @return string
	 */
	private function getSortingOrder(){

		$orders = array(
			"asc" => "ASC",
			"desc" => "DESC",
		);

		$order = UniteFunctionsUC::getGetVar("order", null, UniteFunctionsUC::SANITIZE_KEY);
		$order = UniteFunctionsUC::getVal($orders, $order, "DESC");

		return $order;
	}

	/**
	 * Get the limit.
	 *
	 * @return int
	 */
	private function getLimit(){

		return 20;
	}

	/**
	 * Get the offset.
	 *
	 * @return int
	 */
	private function getOffset(){

		$page = $this->get_pagenum();
		$limit = $this->getLimit();
		$offset = ($page - 1) * $limit;

		return $offset;
	}

	/**
	 * Display the header.
	 *
	 * @return void
	 */
	private function displayHeader(){

		$headerTitle = __("Form Entries", "unlimited-elements-for-elementor");

		require HelperUC::getPathTemplate("header");
	}

	/**
	 * Display the hidden fields.
	 *
	 * @return void
	 */
	private function displayHiddenFields(){

		echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST["page"]) . '" />';

		if(empty($_REQUEST["view"]) === false)
			echo '<input type="hidden" name="view" value="' . esc_attr($_REQUEST["view"]) . '" />';

		if(empty($_REQUEST["orderby"]) === false)
			echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST["orderby"]) . '" />';

		if(empty($_REQUEST["order"]) === false)
			echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST["order"]) . '" />';

		if(empty($_REQUEST["status"]) === false)
			echo '<input type="hidden" name="status" value="' . esc_attr($_REQUEST["status"]) . '" />';

		echo '<input type="hidden" name="ucwindow" value="blank" />';
	}

	/**
	 * Display the search box.
	 *
	 * @return void
	 */
	private function displaySearchBox(){

		?>
		<p class="search-box">
			<label class="screen-reader-text" for="filter-search">
				<?php echo esc_html__("Search", "unlimited-elements-for-elementor"); ?>
			</label>
			<input
				id="filter-search"
				type="search"
				name="<?php echo esc_attr(self::FILTER_SEARCH); ?>"
				placeholder="<?php echo esc_attr__("Search...", "unlimited-elements-for-elementor"); ?>"
				value="<?php echo esc_attr($this->getFilter(self::FILTER_SEARCH)); ?>"
			/>
			<?php submit_button(__("Search", "unlimited-elements-for-elementor"), "", "", false, array("id" => "search-submit")); ?>
		</p>
		<?php
	}

}
