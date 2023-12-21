<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCFormEntryView{

	private $service;
	private $entry;

	/**
	 * Create a new view instance.
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function __construct($id){

		$this->service = new UCFormEntryService();
		$this->entry = $this->getEntry($id);

		$this->service->readEntry($id);
	}

	/**
	 * Displays the view.
	 *
	 * @return void
	 */
	public function display(){

		$this->displayHeader();
		$this->displayContent();
		$this->displayFooter();
	}

	/**
	 * Get the entry data.
	 *
	 * @param int $id
	 *
	 * @return array
	 * @throws Exception
	 */
	private function getEntry($id){

		global $wpdb;

		$sql = "
			SELECT *
			FROM {$this->service->getTable()}
			WHERE id = %d
			LIMIT 1
		";

		$sql = $wpdb->prepare($sql, array($id));
		$entry = $wpdb->get_row($sql, ARRAY_A);

		if(empty($entry) === true)
			UniteFunctionsUC::throwError("Entry with ID {$id} not found.");

		$sql = "
			SELECT *
			FROM {$this->service->getFieldsTable()}
			WHERE entry_id = %d
		";

		$sql = $wpdb->prepare($sql, array($id));
		$entry["fields"] = $wpdb->get_results($sql, ARRAY_A);

		return $entry;
	}

	/**
	 * Display the header.
	 *
	 * @return void
	 */
	private function displayHeader(){

		$headerTitle = sprintf(__("Form Entry (ID %d)", "unlimited-elements-for-elementor"), $this->entry["id"]);

		require HelperUC::getPathTemplate("header");
	}

	/**
	 * Display the content.
	 *
	 * @return void
	 */
	private function displayContent(){

		$asides = array(
			__("Entry Information", "unlimited-elements-for-elementor") => array(
				__("Entry ID", "unlimited-elements-for-elementor") => $this->entry["id"],
				__("Form", "unlimited-elements-for-elementor") => $this->entry["form_name"],
				__("Page", "unlimited-elements-for-elementor") => $this->entry["post_title"],
				__("Date", "unlimited-elements-for-elementor") => $this->service->formatEntryDate($this->entry["created_at"]),
			),
			__("User Information", "unlimited-elements-for-elementor") => array(
				__("User ID", "unlimited-elements-for-elementor") => $this->entry["user_id"],
				__("User IP", "unlimited-elements-for-elementor") => $this->entry["user_ip"],
				__("User Agent", "unlimited-elements-for-elementor") => $this->entry["user_agent"],
			),
		);

		?>
		<div id="poststuff">
			<div id="post-body" class="columns-2">

				<div id="post-body-content">
					<div class="postbox">
						<div class="postbox-header">
							<h2><?php echo esc_html__("Entry Fields", "unlimited-elements-for-elementor"); ?></h2>
						</div>
						<div class="inside">
							<table class="wp-list-table widefat">
								<tbody>
									<?php foreach($this->entry["fields"] as $field): ?>
										<tr>
											<td><?php echo esc_html($field["title"]); ?></td>
											<td>
												<?php

												switch($field["type"]){
													case UniteCreatorForm::TYPE_FILES:
														$form = new UniteCreatorForm();
														echo $form->getFilesFieldLinksHtml($field["value"], "<br />", true);
													break;

													default:
														echo esc_html($field["value"]);
												}

												?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="postbox-container-1">
					<?php foreach($asides as $title => $fields): ?>
						<div class="postbox">
							<div class="postbox-header">
								<h2><?php echo esc_html($title); ?></h2>
							</div>
							<div class="inside">
								<div id="misc-publishing-actions">
									<?php foreach($fields as $label => $value): ?>
										<?php if(isset($value) === true): ?>
											<div class="misc-pub-section">
												<?php echo esc_html($label); ?>: <b><?php echo esc_html($value); ?></b>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

			</div>
			<br class="clear" />
		</div>

		<style>
			#post-body-content .postbox .postbox-header {
				border-bottom: none;
			}

			#post-body-content .postbox .inside {
				margin: 0;
				padding: 0;
			}

			#post-body-content .postbox .wp-list-table {
				border: none;
				border-collapse: collapse;
			}

			#post-body-content .postbox .wp-list-table td {
				border-top: 1px solid #c3c4c7;
			}

			#post-body-content .postbox .wp-list-table td:first-child {
				width: 150px;
				background: #f6f7f7;
				font-weight: bold;
			}
		</style>
		<?php
	}

	/**
	 * Display the footer.
	 *
	 * @return void
	 */
	private function displayFooter(){

		$url = wp_get_referer() ?: "?page={$_REQUEST["page"]}";

		?>
		<div>
			<a class="button" href="<?php echo esc_attr($url); ?>">
				<?php echo esc_html__("Back to Form Entries", "unlimited-elements-for-elementor"); ?>
			</a>
		</div>
		<?php
	}

}
