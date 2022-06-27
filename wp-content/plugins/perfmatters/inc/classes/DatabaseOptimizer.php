<?php
namespace Perfmatters;

class DatabaseOptimizer {

	//declare our optimizer
	protected $optimizer;

	//actions + filters
	public function __construct() {

		//initialize optimizer
		$this->optimizer = new DatabaseOptimizationProcess();

		add_filter('pre_update_option_perfmatters_tools', array($this, 'perfmatters_database_optimization_action'), 10, 2);
		add_action('admin_notices', array($this, 'perfmatters_database_optimization_notices'));
		add_filter('cron_schedules', array($this, 'perfmatters_add_database_optimization_cron_schedule'));
		add_action('init', array($this, 'perfmatters_schedule_database_optimization'));
		add_action('perfmatters_database_optimization', array($this, 'perfmatters_run_scheduled_database_optimization'));
	}

	//run the background process
	public function process_handler($items) {

		//push the requested items to the queue
		array_map(array($this->optimizer, 'push_to_queue'), $items);

		//run the process
		$this->optimizer->save()->dispatch();
	}

	//watch and respond to the optimize button
	public function perfmatters_database_optimization_action($new_value, $old_value) {

		//optimize button was pressed
		if(!empty($new_value['database']['optimize_database'])) {

			//stop and show error if process is already running
			$working = get_transient('perfmatters_database_optimization_process');
			if($working !== false) {
				add_settings_error('perfmatters', 'perfmatters-database-optimization-running', __('There is already an existing database optimization process running.', 'perfmatters'), 'error');
				return $old_value;
			}

			//get available options array
			$optimize_options = array_keys($this->perfmatters_get_database_options());

			//build array of requested items
			$items = array();
			foreach($optimize_options as $item) {
				if(!empty($new_value['database'][$item])) {
					$items[] = $item;
				}
			}

			//run process handler
			if(!empty($items)) {
				$this->process_handler($items);
			}

			//add hidden notice to prevent save message
			add_settings_error('perfmatters', 'perfmatters-hidden-notice', '', 'success');

			return $old_value;
		}

		$new_optimize_schedule = isset($new_value['database']['optimize_schedule']) ? $new_value['database']['optimize_schedule'] : '';
		$old_optimize_schedule = isset($old_value['database']['optimize_schedule']) ? $old_value['database']['optimize_schedule'] : '';

		//optimize schedule was changed
		if($new_optimize_schedule !== $old_optimize_schedule) {
			if(wp_next_scheduled('perfmatters_database_optimization')) {
				wp_clear_scheduled_hook('perfmatters_database_optimization');
			}
		}

		return $new_value;
	}

	//display notices for database optimization process
	public function perfmatters_database_optimization_notices() {
	
		//permissions check
		if(!current_user_can('manage_options')) {
			return;
		}

		//make sure were on our settings page
		if(empty($_GET['page']) || $_GET['page'] !== 'perfmatters') {
			return;
		}

		//get working transient
		$working = get_transient('perfmatters_database_optimization_process');

		if($working !== false) {

			$notice_type = "info";

			$message = __('Database optimization is running in the background.', 'perfmatters');
		}
		else {

			//get completed optimized transient
			$completed = get_transient('perfmatters_database_optimization_process_complete');

			if($completed === false) {
				return;
			}

			$notice_type = "success";

			//get db options array
			$database_options = $this->perfmatters_get_database_options();

			//build admin notice message
			if(!empty($completed)) {
				$message = __('Database optimization completed. The following items were removed:', 'perfmatters');

				$message.= "<ul style='margin: 0px; padding: 0px 2px;'>";
				foreach($completed as $key => $count) {
					$message.= "<li>";
						$message.= "<strong>" . $database_options[$key] . ':</strong> ' . $count;
					$message.= "</li>";
				}
				$message.= "</ul>";
			}
			else {
				$message = __('Database optimization completed. No optimizations found.', 'perfmatters');
			}

			//delete our completed transient
			delete_transient('perfmatters_database_optimization_process_complete');
		}

		//display admin notice
		if(!empty($message)) {
			echo "<div class='notice notice-" . $notice_type . " is-dismissible'>";
	        	echo "<p>" . $message . "</p>";
	   		echo"</div>";
		}
	}

	//add cron schedule
	public function perfmatters_add_database_optimization_cron_schedule($schedules) {

		$perfmatters_tools = get_option('perfmatters_tools');
		if(empty($perfmatters_tools['database']['optimize_schedule']) || $perfmatters_tools['database']['optimize_schedule'] == 'daily') {
			return $schedules;
		}

		switch($perfmatters_tools['database']['optimize_schedule']) {
			case 'weekly' :
				$schedules['weekly'] = array(
					'interval' => 604800,
					'display'  => __('Once Weekly', 'perfmatters'),
				);
				break;

			case 'monthly' :
				$schedules['monthly'] = array(
					'interval' => 2592000,
					'display'  => __('Once Monthly', 'perfmatters'),
				);
				break;

			default :
				break;
		}

		return $schedules;
	}

	//create database optimization scheduled event
	public function perfmatters_schedule_database_optimization() {
		$perfmatters_tools = get_option('perfmatters_tools');
		if(!empty($perfmatters_tools['database']['optimize_schedule']) && !wp_next_scheduled('perfmatters_database_optimization')) {
			wp_schedule_event(time(), $perfmatters_tools['database']['optimize_schedule'], 'perfmatters_database_optimization');
		}
	}

	//scheduled event action
	public function perfmatters_run_scheduled_database_optimization() {

		$perfmatters_tools = get_option('perfmatters_tools');

		$optimize_options = array_keys($this->perfmatters_get_database_options());

		//build array of set items
		$items = array();
		foreach($optimize_options as $item) {
			if(!empty($perfmatters_tools['database'][$item])) {
				 $items[] = $item;
			}
		}

		//run process handler
		if(!empty($items)) {
			$this->process_handler($items);
		}
	}

	//return array of database options
	protected function perfmatters_get_database_options() {
		return array(
			'post_revisions'     => __('Revisions', 'perfmatters'),
			'post_auto_drafts'   => __('Auto Drafts', 'perfmatters'),
			'trashed_posts'       => __('Trashed Posts', 'perfmatters'),
			'spam_comments'      => __('Spam Comments', 'perfmatters'),
			'trashed_comments'   => __('Trashed Comments', 'perfmatters'),
			'expired_transients' => __('Expired transients', 'perfmatters'),
			'all_transients'     => __('Transients', 'perfmatters'),
			'tables'             => __('Tables', 'perfmatters')
		);
	}
}