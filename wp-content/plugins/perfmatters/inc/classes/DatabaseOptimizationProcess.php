<?php
namespace Perfmatters;

class DatabaseOptimizationProcess extends \WP_Background_Process {

	//prefix for process
	protected $prefix = 'perfmatters';

	//action name for process
	protected $action = 'database_optimization';

	//totals of removed items
	protected $counts = array();

	//run on each queue item
	protected function task($item) {

		global $wpdb;

		switch($item) {
			case 'post_revisions' :
				$query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'revision'");
				if($query) {
					$count = 0;
					foreach($query as $id) {
						$count += wp_delete_post_revision(intval($id)) instanceof \WP_Post ? 1 : 0;
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'post_auto_drafts' :
				$query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft'");
				if($query) {
					$count = 0;
					foreach($query as $id) {
						$count += wp_delete_post(intval($id), true) instanceof \WP_Post ? 1 : 0;
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'trashed_posts' :
				$query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'trash'");
				if($query) {
					$count = 0;
					foreach($query as $id) {
						$count += wp_delete_post($id, true) instanceof \WP_Post ? 1 : 0;
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'spam_comments' :
				$query = $wpdb->get_col("SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = 'spam'");
				if($query) {
					$count = 0;
					foreach($query as $id) {
						$count += (int) wp_delete_comment(intval($id), true);
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'trashed_comments' :
				$query = $wpdb->get_col("SELECT comment_ID FROM $wpdb->comments WHERE (comment_approved = 'trash' OR comment_approved = 'post-trashed')");
				if($query) {
					$count = 0;
					foreach($query as $id) {
						$count += (int) wp_delete_comment(intval($id), true);
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'expired_transients' :
				$time  = isset($_SERVER['REQUEST_TIME']) ? (int) $_SERVER['REQUEST_TIME'] : time();
				$query = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d", $wpdb->esc_like('_transient_timeout') . '%', $time));
				if($query) {
					$count = 0;
					foreach($query as $transient) {
						$key = str_replace('_transient_timeout_', '', $transient);
						$count += (int) delete_transient($key);
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'all_transients' :
				$query = $wpdb->get_col($wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s", $wpdb->esc_like('_transient_') . '%', $wpdb->esc_like('_site_transient_') . '%'));
				if($query) {
					$count = 0;
					foreach($query as $transient) {
						if(strpos($transient, '_site_transient_') !== false) {
							$count += (int) delete_site_transient(str_replace('_site_transient_', '', $transient));
						} else {
							$count += (int) delete_transient(str_replace('_transient_', '', $transient));
						}
					}
					$this->counts[$item] = $count;
				}
				break;

			case 'tables' :
				$query = $wpdb->get_results("SELECT table_name, data_free FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' and Engine <> 'InnoDB' and data_free > 0");
				if($query) {
					$count = 0;
					foreach($query as $table) {
						$count += (int) $wpdb->query("OPTIMIZE TABLE $table->table_name");
					}
					$this->counts[$item] = $count;
				}
				break;
		}

		return false;
	}

	//run background process on queue
	public function dispatch() {

		//set our working transient
		set_transient('perfmatters_database_optimization_process', 'working', HOUR_IN_SECONDS);

		//run parent dispatch
		return parent::dispatch();
	}

	//run when background process is complete
	protected function complete() {

		//delete our working transient
		delete_transient('perfmatters_database_optimization_process');

		//set complete transient
		set_transient('perfmatters_database_optimization_process_complete', $this->counts);

		//run parent complete
		parent::complete();
	}
}