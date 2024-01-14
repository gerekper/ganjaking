<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCChangelogView extends WP_List_Table{

	const ACTION_EDIT = "edit";
	const ACTION_DELETE = "delete";
	const ACTION_EXPORT = "export";

	const FILTER_ID = "id";
	const FILTER_ADDON = "addon";
	const FILTER_VERSION = "version";

	const EDIT_FIELD_TYPE = "type";
	const EDIT_FIELD_TEXT = "text";

	private $service;

	/**
	 * Constructor.
	 *
	 * @param array|string $args
	 *
	 * @return void
	 */
	public function __construct($args = array()){

		$this->service = new UniteCreatorAddonChangelog();

		parent::__construct($args);
	}

	/**
	 * Process the action.
	 *
	 * @return void
	 */
	public function processAction(){

		$generalQueryArgs = array("_wp_http_referer", "_wpnonce", "ucwindow");
		$actionQueryArgs = array("action", "action2", self::FILTER_ID, self::EDIT_FIELD_TYPE, self::EDIT_FIELD_TEXT);

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
				case self::ACTION_EDIT:
					$result = $this->processEditAction($ids);
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
			"addon" => __("Widget", "unlimited-elements-for-elementor"),
			"type" => __("Type", "unlimited-elements-for-elementor"),
			"text" => __("Text", "unlimited-elements-for-elementor"),
			"version" => __("Version", "unlimited-elements-for-elementor"),
			"user" => __("Author", "unlimited-elements-for-elementor"),
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
	 * Message to be displayed when there are no items.
	 *
	 * @return void
	 */
	public function no_items(){

		echo __("No changelogs found.", "unlimited-elements-for-elementor");
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
		<form class="unite-inputs" method="get">
			<?php $this->displayHiddenFields(); ?>
			<?php $this->views(); ?>
			<?php parent::display(); ?>
			<?php $this->displayFilterScript(); ?>
			<?php $this->displayEditTemplate(); ?>
			<?php $this->displayEditStyle(); ?>
			<?php $this->displayEditScript(); ?>
		</form>
		<?php
	}

	/**
	 * Get a list of bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions(){

		$actions = array(
			self::ACTION_DELETE => __("Delete Permanently", "unlimited-elements-for-elementor"),
		);

		return $actions;
	}

	/**
	 * Generates content for a single row of the table.
	 *
	 * @param array $item
	 *
	 * @return void
	 */
	public function single_row($item){

		echo '<tr data-log="' . esc_attr(json_encode($item)) . '">';
		$this->single_row_columns($item);
		echo '</tr>';
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
	protected function column_addon($item){

		$id = $item["id"];
		$content = $item["addon_title"];

		$actions = array(
			self::ACTION_EDIT => $this->getActionLink(self::ACTION_EDIT, $id, __("Edit", "unlimited-elements-for-elementor")),
			self::ACTION_DELETE => $this->getActionLink(self::ACTION_DELETE, $id, __("Delete Permanently", "unlimited-elements-for-elementor")),
		);

		return $content . '<div style="font-weight: normal">' . $this->row_actions($actions) . '</div>';
	}

	/**
	 * Renders the type column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_type($item){

		return $item["type_title"];
	}

	/**
	 * Renders the text column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_text($item){

		return $item["text_html"];
	}

	/**
	 * Renders the version column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_version($item){

		return $item["plugin_version"];
	}

	/**
	 * Renders the user column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_user($item){

		return $item["user_username"];
	}

	/**
	 * Renders the date column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_date($item){

		return $item["created_date"];
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

		$addons = $this->getAddonsFilter();
		$versions = $this->getVersionsFilter();

		$selectedAddon = $this->getFilter(self::FILTER_ADDON);
		$selectedVersion = $this->getFilter(self::FILTER_VERSION);

		?>
		<div class="alignleft actions">
			<?php $this->displayFilterSelect(self::FILTER_ADDON, __("Filter by Widget", "unlimited-elements-for-elementor"), __("All Widgets", "unlimited-elements-for-elementor"), $addons, $selectedAddon); ?>
			<?php $this->displayFilterSelect(self::FILTER_VERSION, __("Filter by Version", "unlimited-elements-for-elementor"), __("All Versions", "unlimited-elements-for-elementor"), $versions, $selectedVersion); ?>
			<?php submit_button(__("Filter", "unlimited-elements-for-elementor"), "", "", false, array("id" => "filter-submit")); ?>
		</div>
		<div class="alignright" style="margin-left: 8px;">
			<?php submit_button(__("Export", "unlimited-elements-for-elementor"), "primary", self::ACTION_EXPORT, false, array("id" => "export-submit")); ?>
		</div>
		<?php
	}

	/**
	 * Process the edit action.
	 *
	 * @param array $ids
	 *
	 * @return int
	 */
	private function processEditAction($ids){

		$type = UniteFunctionsUC::getGetVar(self::EDIT_FIELD_TYPE, null, UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		$text = UniteFunctionsUC::getGetVar(self::EDIT_FIELD_TEXT, null, UniteFunctionsUC::SANITIZE_TEXT_FIELD);

		UniteFunctionsUC::validateNotEmpty($type, "type");
		UniteFunctionsUC::validateNotEmpty($text, "text");

		$result = $this->service->updateChangelog($ids, array(
			"type" => $type,
			"text" => $text,
		));

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

		$result = $this->service->deleteChangelog($ids);

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
		";

		$ids = $wpdb->get_col($sql);
		$items = $this->service->findChangelog($ids);

		$lines = array();

		foreach($items as $item){
			$lines[] = implode(" - ", array(
				$item["addon_title"],
				$item["type_title"],
				$item["text"],
			));
		}

		$filename = "changelog-" . current_time("mysql") . ".txt";
		$content = implode("\n", $lines);

		UniteFunctionsUC::downloadTxt($filename, $content);
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
			LIMIT {$this->getLimit()}
			OFFSET {$this->getOffset()}
		";

		$ids = $wpdb->get_col($sql);
		$items = $this->service->findChangelog($ids);

		$data = array(
			"items" => $items,
			"total" => $total,
		);

		return $data;
	}

	/**
	 * Get the list of addons for the filter.
	 *
	 * @return array
	 */
	private function getAddonsFilter(){

		global $wpdb;

		$sql = "
			SELECT addon_id
			FROM {$this->service->getTable()}
			GROUP BY addon_id
			ORDER BY addon_id
		";

		$results = $wpdb->get_results($sql);
		$addon = new UniteCreatorAddon();
		$items = array();

		foreach($results as $result){
			try {
				$addon->initByID($result->addon_id);

				$result->addon_title = $addon->getTitle();
			} catch(Exception $exception) {
				$result->addon_title = sprintf(__("#%s (not found)", "unlimited-elements-for-elementor"), $result->addon_id);
			}

			$items[$result->addon_id] = $result->addon_title;
		}

		return $items;
	}

	/**
	 * Get the list of versions for the filter.
	 *
	 * @return array
	 */
	private function getVersionsFilter(){

		global $wpdb;

		$sql = "
			SELECT plugin_version
			FROM {$this->service->getTable()}
			GROUP BY plugin_version
			ORDER BY plugin_version
		";

		$results = $wpdb->get_results($sql);
		$items = array();

		foreach($results as $result){
			$items[$result->plugin_version] = $result->plugin_version;
		}

		return $items;
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
		$url["ucwindow"] = "blank";
		$url[self::FILTER_ID] = $id;

		if(empty($_REQUEST["view"]) === false)
			$url["view"] = $_REQUEST["view"];

		return '<a href="?' . http_build_query($url) . '" data-action="' . esc_attr($action) . '">' . esc_html($label) . '</a>';
	}

	/**
	 * Get the filter values.
	 *
	 * @return array
	 */
	private function getFilters(){

		$keys = array(
			self::FILTER_ID,
			self::FILTER_ADDON,
			self::FILTER_VERSION,
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
			case self::FILTER_ADDON:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_ADDON, null, UniteFunctionsUC::SANITIZE_ID);
			break;
			case self::FILTER_VERSION:
				$value = UniteFunctionsUC::getGetVar(self::FILTER_VERSION, null, UniteFunctionsUC::SANITIZE_NOTHING);
			break;
		}

		return $value;
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

		$addon = UniteFunctionsUC::getVal($filters, self::FILTER_ADDON, null);

		if(empty($addon) === false)
			$where .= $wpdb->prepare(" AND addon_id = %d", array($addon));

		$version = UniteFunctionsUC::getVal($filters, self::FILTER_VERSION, null);

		if(empty($version) === false)
			$where .= $wpdb->prepare(" AND plugin_version = %s", array($version));

		return $where;
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

		$headerTitle = __("Changelog", "unlimited-elements-for-elementor");

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

		echo '<input type="hidden" name="ucwindow" value="blank" />';
	}

	/**
	 * Display the filter select.
	 *
	 * @param string $name
	 * @param string $label
	 * @param string $allLabel
	 * @param array $options
	 * @param mixed $selectedValue
	 *
	 * @return void
	 */
	private function displayFilterSelect($name, $label, $allLabel, $options, $selectedValue = null){

		$id = "filter-$name";

		?>
		<label class="screen-reader-text" for="<?php esc_attr_e($id); ?>"><?php esc_html_e($label); ?></label>
		<select id="<?php esc_attr_e($id); ?>" name="<?php esc_attr_e($name); ?>">
			<option value=""><?php esc_html_e($allLabel); ?></option>
			<?php foreach($options as $value => $label): ?>
				<option
					value="<?php esc_attr_e($value); ?>"
					<?php echo $value === $selectedValue ? "selected" : ""; ?>
				>
					<?php esc_html_e($label); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Display the filter script.
	 *
	 * @return void
	 */
	private function displayFilterScript(){

		HelperUC::addScript("select2.full.min", "select2_js","js/select2");
		HelperUC::addStyle("select2", "select2_css","js/select2");

		?>
		<script>
			jQuery(document).ready(function () {
				jQuery(".tablenav select").each(function () {
					var objSelect = jQuery(this);

					objSelect.select2({
						dropdownParent: objSelect.closest(".actions"),
						minimumResultsForSearch: 10,
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Display the edit template.
	 *
	 * @return void
	 */
	private function displayEditTemplate(){

		$types = $this->service->getTypes();

		?>
		<script id="uc-inline-edit-template" type="text/html">
			<form class="uc-inline-edit-form" method="get">
				<div class="uc-inline-edit-form-error">
					<div class="uc-inline-edit-form-error-title"></div>
					<div class="uc-inline-edit-form-error-content"></div>
				</div>
				<?php $this->displayHiddenFields(); ?>
				<input type="hidden" name="action" value="<?php esc_attr_e(self::ACTION_EDIT); ?>" />
				<input type="hidden" name="id" />
				<select name="<?php esc_attr_e(self::EDIT_FIELD_TYPE); ?>">
					<option value="" selected disabled>
						<?php esc_html_e("Select type", "unlimited-elements-for-elementor"); ?>
					</option>
					<?php foreach($types as $value => $label): ?>
						<option value="<?php esc_attr_e($value); ?>"><?php esc_html_e($label); ?></option>
					<?php endforeach; ?>
				</select>
				<textarea name="<?php esc_attr_e(self::EDIT_FIELD_TEXT); ?>"
					placeholder="<?php esc_attr_e("Enter text", "unlimited-elements-for-elementor"); ?>"></textarea>
				<div class="uc-inline-edit-form-actions">
					<button class="uc-inline-edit-form-submit unite-button-primary" type="submit">
						<?php esc_html_e("Save", "unlimited-elements-for-elementor"); ?>
					</button>
					<button class="uc-inline-edit-form-cancel unite-button-secondary" type="button">
						<?php esc_html_e("Cancel", "unlimited-elements-for-elementor"); ?>
					</button>
				</div>
			</form>
		</script>
		<?php
	}

	/**
	 * Display the edit style.
	 *
	 * @return void
	 */
	private function displayEditStyle(){

		?>
		<style>
			.uc-inline-edit-form {
				display: flex;
				flex-direction: column;
				max-width: 320px;
			}

			.uc-inline-edit-form select,
			.uc-inline-edit-form textarea {
				margin-bottom: 8px;
			}

			.uc-inline-edit-form-error {
				display: none;
				font-size: var(--ue-font-size);
				line-height: var(--ue-line-height);
				color: var(--ue-color-danger);
				margin-bottom: 8px;
			}

			.uc-inline-edit-form-error-title {
				font-weight: var(--ue-font-weight-bold);
			}

			.uc-inline-edit-form-actions {
				display: flex;
				align-items: center;
				justify-content: flex-start;
			}

			.uc-inline-edit-form-actions button + button {
				margin-left: 8px;
			}
		</style>
		<?php
	}

	/**
	 * Display the edit script.
	 *
	 * @return void
	 */
	private function displayEditScript(){

		?>
		<script>
			jQuery(document).ready(function () {
				jQuery(document).on("click", ".uc-inline-edit-form-cancel", function (event) {
					event.preventDefault();

					clearEdit();
				});

				jQuery(document).on("click", ".uc-inline-edit-form-submit", function (event) {
					var objForm = jQuery(this).closest(".uc-inline-edit-form");
					var objFormError = objForm.find(".uc-inline-edit-form-error");
					var objFormErrorTitle = objForm.find(".uc-inline-edit-form-error-title");
					var objFormErrorContent = objForm.find(".uc-inline-edit-form-error-content");
					var type = objForm.find("[name='type']").val();
					var text = objForm.find("[name='text']").val().trim();
					var errors = [];

					if (!type)
						errors.push("Type is empty.");

					if (!text)
						errors.push("Text is empty.");

					if (errors.length > 0) {
						event.preventDefault();

						objFormError.show();
						objFormErrorTitle.html("Validation errors:");
						objFormErrorContent.html("- " + errors.join("<br />- "));
					}
				});

				jQuery(document).on("click", ".row-actions [data-action='edit']", function (event) {
					event.preventDefault();

					var objRow = jQuery(this).closest("tr");
					var objTemplate = jQuery(jQuery("#uc-inline-edit-template").html());
					var colspan = objRow.children().length;
					var log = objRow.data("log");

					var objEmptyRow = jQuery("<tr class='uc-inline-edit-row hidden'><td colspan='" + colspan + "'></td></tr>");
					var objEditRow = jQuery("<tr class='uc-inline-edit-row'><td colspan='" + colspan + "'></td></tr>");

					clearEdit();

					objTemplate.find("[name='id']").val(log.id);
					objTemplate.find("[name='type']").val(log.type);
					objTemplate.find("[name='text']").val(log.text);

					objEditRow.find("td").append(objTemplate);

					objRow
						.hide()
						.after(objEditRow)
						.after(objEmptyRow);
				});

				function clearEdit() {
					var objTable = jQuery("table.wp-list-table");

					objTable.find("tr.uc-inline-edit-row").remove();
					objTable.find("tr").show();
				}
			});
		</script>
		<?php
	}

}

$changelog = new UCChangelogView();
$changelog->display();
