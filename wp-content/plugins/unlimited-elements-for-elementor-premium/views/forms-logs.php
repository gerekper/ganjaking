<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCFormsDebugLogView extends WP_List_Table{

	/**
	 * Gets a list of columns.
	 *
	 * @return array
	 */
	public function get_columns(){

		$columns = array(
			"form" => __("Form", "unlimited-elements-for-elementor"),
			"message" => __("Message", "unlimited-elements-for-elementor"),
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

		// prepare columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		// prepare items
		$this->items = $this->prepareData();
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @return void
	 */
	public function no_items(){

		echo __("No logs found.", "unlimited-elements-for-elementor");
	}

	/**
	 * Displays the table.
	 *
	 * @return void
	 */
	public function display(){

		$this->prepare_items();

		$this->displayHeader();

		parent::display();

		$this->displayFooter();
	}

	/**
	 * Renders the form column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_form($item){

		return $item["form"];
	}

	/**
	 * Renders the message column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_message($item){

		return $item["message"];
	}

	/**
	 * Renders the date column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function column_date($item){

		return mysql2date("j F Y H:i:s", $item["date"]);
	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @param string $which
	 *
	 * return void
	 */
	protected function display_tablenav($which){
		// hide navigation
	}

	/**
	 * Prepares the list of items.
	 *
	 * @return array
	 */
	private function prepareData(){

		$items = UniteCreatorForm::getFormLogs();
		$items = array_reverse($items);

		return $items;
	}

	/**
	 * Display the header.
	 *
	 * @return void
	 */
	private function displayHeader(){

		$headerTitle = __("Form Logs", "unlimited-elements-for-elementor");

		require HelperUC::getPathTemplate("header");
	}

	/**
	 * Display the footer.
	 *
	 * @return void
	 */
	private function displayFooter(){

		$url = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR, "#tab=forms");

		?>
		<div style="margin-top: 20px;">
			<a class="button" href="<?php echo esc_attr($url); ?>">
				<?php echo esc_html__("Back to Settings", "unlimited-elements-for-elementor"); ?>
			</a>
		</div>
		<?php
	}

}

$debugLog = new UCFormsDebugLogView();
$debugLog->display();
