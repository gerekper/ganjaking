<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: incremental:Support for incremental backups
Description: Allows UpdraftPlus to schedule incremental file backups, which use much less resources
Version: 1.2
Shop: /shop/incremental/
Latest Change: 1.14.5
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_Addons_Incremental;

class UpdraftPlus_Addons_Incremental {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter('updraftplus_incremental_backup_link', array($this, 'incremental_backup_link'), 10, 1);
		// Priority 11 so that it loads after the filter that adds the backup label
		add_filter('updraftplus_showbackup_date', array($this, 'showbackup_date'), 11, 5);
		add_filter('updraftplus_files_altered_since', array($this, 'files_altered_since'), 10, 2);
		add_filter('updraft_backupnow_options', array($this, 'backupnow_options'), 8, 2);
		add_filter('updraftplus_initial_jobdata', array($this, 'initial_jobdata_incremental_jobdata'), 10, 2);
		add_filter('updraftplus_save_backup_history_timestamp', array($this, 'incremental_backup_timestamp'), 10, 1);
		add_filter('updraftplus_base_backup_timestamp', array($this, 'incremental_backup_timestamp'), 10, 1);
		add_filter('updraftplus_merge_backup_history', array($this, 'merge_backup_history'), 10, 2);
		add_filter('updraftplus_include_manifest', array($this, 'incremental_include_manifest'), 10, 1);
		add_filter('updraft_backupnow_modal_afterfileoptions', array($this, 'backupnow_modal_afterfileoptions'), 5, 1);
		add_filter('updraftplus_backupnow_file_entities', array($this, 'get_impossible_incremental_file_options'), 10, 1);
		add_filter('updraftplus_incremental_addon_installed', '__return_true');
		add_filter('updraftplus_prepare_incremental_run', array($this, 'prepare_incremental_run'), 10, 2);
		add_action('updraftplus_incremental_cell', array($this, 'incremental_cell'), 10, 2);
		add_action('updraft_backup_increments', array($this, 'backup_increments'));
		add_action('updraftplus_admin_enqueue_scripts', array($this, 'updraftplus_admin_enqueue_scripts'));
	}

	/**
	 * Runs upon the WP action updraftplus_admin_enqueue_scripts
	 */
	public function updraftplus_admin_enqueue_scripts() {
		add_action('admin_footer', array($this, 'admin_footer_incremental_backups_js'));
	}
	
	/**
	 * This function is called via a filter and will replace the incremental backup link in the free version
	 *
	 * @param string $link - the incremental backup link
	 *
	 * @return string      - the premium backup link
	 */
	public function incremental_backup_link($link) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return '<p><a href="#" id="updraftplus_incremental_backup_link" onclick="updraft_backup_dialog_open(\'incremental\'); return false;" data-incremental="1">' . __('Add changed files (incremental backup) ...', ' updraftplus ') . '</a></p>';
	}

	/**
	 * This function will add to the backup label information on when the last incremental set was created, it will also add to the title the dates for all the incremental sets in this backup.
	 *
	 * @param string  $date          - the date when the backup set was first created
	 * @param array   $backup        - the backup set
	 * @param array   $jobdata       - an array of information relating to the backup job
	 * @param integer $backup_date   - the timestamp of when the backup set was first created
	 * @param boolean $simple_format - a boolean value to indicate if this should be a simple format date
	 *
	 * @return string                - returns a string that is either the original backup date or the string that contains the incremental set data
	 */
	public function showbackup_date($date, $backup, $jobdata, $backup_date, $simple_format) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		$incremental_sets = !empty($backup['incremental_sets']) ? $backup['incremental_sets'] : array();
		
		// Check here that the backup set has the incremental set and that there is more than one set as we don't want the incremental backup UI showing for every user backup
		if (!empty($incremental_sets) && 1 < count($incremental_sets)) {

			// The timestamps here are already in the users local time (they come from the backup filenames) and don't need to be converted again as we will end up displaying the wrong time
			$latest_increment = key(array_slice($incremental_sets, -1, 1, true));

			if ($latest_increment > $backup_date) {
				
				$increment_times = '';

				foreach ($incremental_sets as $inc_time => $entities) {
					if ($increment_times) $increment_times .= '; ';
					$increment_times .= gmdate('M d, Y G:i', $inc_time);
				}

				if ($simple_format) {
					return $date.' '.sprintf(__('(latest increment: %s)', 'updraftplus'), gmdate('M d, Y G:i', $inc_time));
				} else {
					return '<span title="'.sprintf(__('Increments exist at: %s', 'updraftplus'), $increment_times).'">'.$date.'<br>'.sprintf(__('(latest increment: %s)', 'updraftplus'), gmdate('M d, Y G:i', $latest_increment)).'</span>';
				}
			}
		}
		
		return $date;
	}

	/**
	 * This function will get an return the files_enumerated_at array if it is set otherwise returns an empty array
	 *
	 * @param integer $altered_since - integer for files altered since this time default is -1
	 * @param string  $job_type      - a string to indicate the job type
	 *
	 * @return integer|array         - returns the default integer if this is not a backup job other wise returns the files_enumerated_at array or an empty array if not set
	 */
	public function files_altered_since($altered_since, $job_type) {
		global $updraftplus;

		if ('incremental' !== $job_type) return $altered_since;
		
		$backup_history = UpdraftPlus_Backup_History::get_backup_set_by_nonce($updraftplus->file_nonce);
		$files_enumerated_at = isset($backup_history['files_enumerated_at']) ? $backup_history['files_enumerated_at'] : array();
		
		return is_array($files_enumerated_at) ? $files_enumerated_at : array();
	}

	/**
	 * This function will check to see if the incremental option is set and if so adds it to the backup job options
	 *
	 * @param array $options - the backup job options
	 * @param array $request - the backup request array
	 *
	 * @return array         - returns the modified backup job options
	 */
	public function backupnow_options($options, $request) {
		if (!is_array($options)) return $options;
		
		if (!empty($request['incremental'])) {
			$options['incremental'] = $request['incremental'];
			if (!empty($request['backupnow_label'])) unset($request['backupnow_label']);
			// remove from the options array directly as it's already been added before we get here.
			if (!empty($options['always_keep'])) unset($options['always_keep']);
		}
		
		return $options;
	}

	/**
	 * This function will set up the backup job data for when we are starting a incremental backup. It changes the initial jobdata so that UpdraftPlus knows to start a incremental backup job.
	 *
	 * @param array $jobdata - the initial job data that we want to change
	 * @param array $options - options sent from the front end includes backup timestamp and nonce
	 *
	 * @return array         - the modified jobdata
	 */
	public function initial_jobdata_incremental_jobdata($jobdata, $options) {
		
		if (!is_array($jobdata) || empty($options['incremental'])) return $jobdata;
		
		global $updraftplus;
		
		/*
			The initial job data is not set up in a key value array instead it is set up so key "x" is the name of the key and then key "y" is the value.
			e.g array[0] = 'backup_name' array[1] = 'my_backup'

			Note: we use strict comparison here to avoid PHP treating (String)"value" == 1 as true and giving us the wrong keys
		*/
		$jobtype_key = array_search('job_type', $jobdata, true) + 1;
		$job_file_entities_key = array_search('job_file_entities', $jobdata, true) + 1;
		$job_backup_time = array_search('backup_time', $jobdata, true) + 1;
		$backup_database_key = array_search('backup_database', $jobdata, true) + 1;

		$backup_history = UpdraftPlus_Backup_History::get_backup_set_by_nonce($updraftplus->file_nonce);
		$possible_backups = $updraftplus->get_backupable_file_entities(true);

		$job_file_entities = $jobdata[$job_file_entities_key];
		$job_backup_files_array = array();

		foreach ($possible_backups as $youwhat => $whichdir) {
			if (isset($job_file_entities[$youwhat]) && isset($backup_history[$youwhat])) {
				$job_file_entities[$youwhat]['index'] = count($backup_history[$youwhat]);
				$job_backup_files_array[$youwhat] = $backup_history[$youwhat];
				if (isset($backup_history[$youwhat.'-size'])) {
					$job_backup_files_array[$youwhat.'-size'] = $backup_history[$youwhat.'-size'];
				}
			}
		}

		$previous_job_files_array = $job_backup_files_array;

		$db_backups = $jobdata[$backup_database_key];
		
		$db_backup_info = $updraftplus->update_database_jobdata($db_backups, $backup_history);

		$jobdata[$jobtype_key] = 'incremental';
		$backup_time_was = $jobdata[$job_backup_time];
		$jobdata[$job_backup_time] = $backup_history['timestamp'];
		$jobdata[$job_file_entities_key] = $job_file_entities;
		$jobdata[] = 'backup_files_array';
		$jobdata[] = $job_backup_files_array;
		$jobdata[] = 'previous_backup_files_array';
		$jobdata[] = $previous_job_files_array;
		$jobdata[] = 'blog_name';
		$jobdata[] = $db_backup_info['blog_name'];
		$jobdata[$backup_database_key] = $db_backup_info['db_backups'];
		$jobdata[] = 'incremental_run_start';
		$jobdata[] = $backup_time_was;
		
		if (isset($backup_history['morefiles_linked_indexes']) && isset($backup_history['morefiles_more_locations'])) {
			$jobdata[] = 'morefiles_linked_indexes';
			$jobdata[] = $backup_history['morefiles_linked_indexes'];
			$jobdata[] = 'morefiles_more_locations';
			$jobdata[] = $backup_history['morefiles_more_locations'];
		}
		
		return $jobdata;
	}

	/**
	 * This function will merge the incremental backup array with the existing backup history. This is desirable because a manual incremental run (e.g. plugins only) won't contain job-data on the excluded entities; saving the current job-data would therefore result in that data being lost.
	 *
	 * @param array $job_backup_array     - the incremental backup set
	 * @param array $history_backup_array - the full backup history set
	 *
	 * @return array                      - returns the full backup history after the merge
	 */
	public function merge_backup_history($job_backup_array, $history_backup_array) {
		global $updraftplus;

		if ('incremental' != $updraftplus->jobdata_get('job_type')) return $job_backup_array;

		$history_backup_array = $this->recursive_backup_history_merge($history_backup_array, $job_backup_array);

		return $history_backup_array;
	}

	/**
	 * This function will perform a recursive merge on the backup history using the passed in array to merge
	 *
	 * @param array $history_backup_array - the full backup history
	 * @param array $job_backup_array     - the array to merge into the backup history
	 *
	 * @return array                      - the new full backup history
	 */
	private function recursive_backup_history_merge($history_backup_array, $job_backup_array){

		foreach ($job_backup_array as $key => $data) {
			if (is_array($data)) {
				$history_backup_array[$key] = isset($history_backup_array[$key]) ? $this->recursive_backup_history_merge($history_backup_array[$key], $data) : $data;
			} else {
				$history_backup_array[$key] = $data;
			}
		}

		return $history_backup_array;
	}

	/**
	 * This function will filter the passed in timestamp, it will check that this is an incremental run and will return the timestamp from the jobdata so that the increment will be saved in the original backup.
	 *
	 * @param string $timestamp - the backup timestamp
	 *
	 * @return string           - returns the incremental backup timestamp
	 */
	public function incremental_backup_timestamp($timestamp) {
		global $updraftplus;

		if ('incremental' != $updraftplus->jobdata_get('job_type')) return $timestamp;

		$timestamp = $updraftplus->jobdata_get('backup_time');

		return $timestamp;
	}

	/**
	 * This function will filter and return a boolean to indicate if the backup should include a manifest or not
	 *
	 * @param  boolean $include - a boolean to indicate if we should include a manifest in the backup
	 *
	 * @return boolean          - returns a boolean to indicate if we should include a manifest in the backup
	 */
	public function incremental_include_manifest($include) {
		global $updraftplus;

		if ('incremental' != $updraftplus->jobdata_get('job_type')) return $include;
		
		return true;
	}

	/**
	 * This function will add a checkbox to the existing backupnow modal content, which allows the user to specify if this manual backup should be an incremental one or not, if the user does not have an existing backup that is suitable to add increments to then the checkbox will be disabled.
	 *
	 * @param string $ret - the backup now modal content
	 *
	 * @return string     - content to add to the backupnow modal
	 */
	public function backupnow_modal_afterfileoptions($ret) {

		$entities = UpdraftPlus_Backup_History::get_existing_backup_entities();
		
		if (!empty($entities)) {
			$ret .= '<p id="incremental_container" class="incremental-backups-only"><input type="hidden" id="incremental" data-incremental="1" value="1"> <label for="incremental">' . __('Files changed since the last backup will be added as a new increment in that backup set.', 'updraftplus').' '.__('N.B. No backup of your database will be taken in an incremental backup; if you want a database backup as well, then take that separately.', 'updraftplus').'</label></p>';
		} else {
			$ret .= '<p id="incremental_container" class="incremental-backups-only"><input type="hidden" id="incremental" data-incremental="0" value="0"><span> <em>' . __("No incremental backup of your files is possible, as no suitable existing backup was found to add increments to.", 'updraftplus') . '</em></span></p>';
		}

		return $ret;
	}

	/**
	 * This function will return an array of impossible file entities that we cannot add increments to
	 *
	 * @param array $file_entities - an array of file entities
	 *
	 * @return array               - an array of file entities we cannot add an increment to
	 */
	public function get_impossible_incremental_file_options($file_entities) {
		global $updraftplus;

		$entities = UpdraftPlus_Backup_History::get_existing_backup_entities();

		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

		foreach ($backupable_entities as $key => $info) {
			if (!in_array($key, $entities)) $file_entities[] = $key;
		}

		return $file_entities;
	}
	
	/**
	 * Get a list of incremental backup intervals
	 *
	 * @return Array - keys are used as identifiers in the UI drop-down; values are user-displayed text describing the interval
	 */
	private function get_intervals() {
		global $updraftplus;
		if ($updraftplus->is_restricted_hosting('only_one_incremental_per_day')) {
			$intervals = array(
				'none' => __("None", 'updraftplus'),
				'daily' => __("Daily", 'updraftplus'),
				'weekly' => __("Weekly", 'updraftplus'),
				'fortnightly' => __("Fortnightly", 'updraftplus'),
				'monthly' => __("Monthly", 'updraftplus')
			);
		} else {
			$intervals = array(
				'none' => __("None", 'updraftplus'),
				'everyhour' => __("Every hour", 'updraftplus'),
				'every2hours' => sprintf(__("Every %s hours", 'updraftplus'), '2'),
				'every4hours' => sprintf(__("Every %s hours", 'updraftplus'), '4'),
				'every8hours' => sprintf(__("Every %s hours", 'updraftplus'), '8'),
				'twicedaily' => sprintf(__("Every %s hours", 'updraftplus'), '12'),
				'daily' => __("Daily", 'updraftplus'),
				'weekly' => __("Weekly", 'updraftplus'),
				'fortnightly' => __("Fortnightly", 'updraftplus'),
				'monthly' => __("Monthly", 'updraftplus')
			);
		}
		return apply_filters('updraftplus_backup_intervals_increments', $intervals);
	}

	/**
	 * This function is called via the action updraftplus_incremental_cell and will add UI options to schedule incremental backups.
	 *
	 * @param string $selected_interval - the interval that is currently selected
	 *
	 * @return void
	 */
	public function incremental_cell($selected_interval) {
		?>
		<div>
		<?php _e('And then add an incremental backup', 'updraftplus'); ?>
		<select id="updraft_interval_increments" name="updraft_interval_increments">
			<?php
			$intervals = $this->get_intervals();
			$selected_interval = UpdraftPlus_Options::get_updraft_option('updraft_interval_increments', 'none');
			foreach ($intervals as $cronsched => $descrip) {
				echo "<option value=\"$cronsched\" ";
				if ($cronsched == $selected_interval) echo 'selected="selected"';
				echo ">".htmlspecialchars($descrip)."</option>\n";
			}
			?>
		</select>
		<?php echo '<a href="' . apply_filters('updraftplus_com_link', "https://updraftplus.com/support/tell-me-more-about-incremental-backups/") . '" aria-label="'. __('Tell me more about incremental backups', 'updraftplus') .'" target="_blank">' . __('Tell me more', 'updraftplus') . '</a>'; ?>
		</div>
		<?php
	}

	/**
	 * This function will setup and check that an incremental backup can be started. It is called by the WP action updraft_backup_increments (which gets scheduled)
	 */
	public function backup_increments() {
		global $updraftplus;
		
		$selected_interval = UpdraftPlus_Options::get_updraft_option('updraft_interval_increments', 'none');
		
		if ('none' === $selected_interval) {
			// Handle WP-Cron being inconsistent with the saved options
			$updraftplus->log("No incremental backup is configured in the saved settings; will not run");
			return;
		}
		
		$running = $updraftplus->is_backup_running();
		if ($running) {
			$updraftplus->log($running);
			return;
		}
		
		$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);

		$entities = array();

		foreach ($backupable_entities as $key => $info) {
			if (UpdraftPlus_Options::get_updraft_option("updraft_include_$key", false)) {
				$entities[] = $key;
			}
		}

		// No incremental run is possible at this time, so bail out
		if (!$this->prepare_incremental_run(false, $entities)) return;

		// The call to backup_time_nonce() allows us to know the nonce in advance, and return it
		$nonce = $updraftplus->backup_time_nonce();

		$options = array('use_nonce' => $nonce);
		$request = array('incremental' => true);

		$updraftplus->boot_backup(true, false, false, false, false, apply_filters('updraft_backupnow_options', $options, $request));
	}

	/**
	 * This function will prepare the incremental run by setting up the correct backup file nonce to use
	 *
	 * @param boolean $incremental - filter value to decide if we should run an incremental run or not
	 * @param array   $entities    - an array of entities in this backup run
	 *
	 * @return boolean - to indicate whether or not (e.g. no full backup was found) an incremental run can proceed
	 */
	public function prepare_incremental_run($incremental = false, $entities = array()) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		global $updraftplus;

		$nonce = UpdraftPlus_Backup_History::get_latest_backup($entities);
		if (empty($nonce)) return false;

		$updraftplus->file_nonce = $nonce;
		add_filter('updraftplus_incremental_backup_file_nonce', array($updraftplus, 'incremental_backup_file_nonce'));

		return true;
	}

	/**
	 * This function will output any needed js for the incremental backup addon.
	 *
	 * @return void
	 */
	public function admin_footer_incremental_backups_js() {
		?>
		<script>
		jQuery(function() {
			<?php
				$intervals = $this->get_intervals();
				$var_int = '';
				foreach ($intervals as $val => $descript) {
					if ($var_int) $var_int .= ', ';
					$var_int .= "$val: \"".esc_js($descript)."\"";
				}
				echo 'var intervals = {'.$var_int."}\n";
			?>
			function updraft_update_incremental_selector() {
				var fileint = jQuery('#updraft-navtab-settings-content select.updraft_interval').val();
				var prevsel = jQuery('#updraft-navtab-settings-content select#updraft_interval_increments').val();
				
				var newhtml = '';
				var adding = 1;
				for (var key in intervals) {
					if (key == fileint) { adding = 0; }
					if (1 == adding) {
						if ('manual' == fileint && 'none' != key) continue;
						var value = intervals[key];
						var sel = '';
						if (prevsel == key) { sel = 'selected="selected" '; }
						newhtml += '<option '+sel+'value="'+key+'">'+value+'</option>';
					}
				}
				var $increments_selector = jQuery('#updraft-navtab-settings-content select#updraft_interval_increments');
				$increments_selector.attr('disabled', false);
				$increments_selector.html(newhtml)
				if ($increments_selector.find("option").length <= 1) {
					$increments_selector.attr('disabled', true);
				}
			}

			jQuery('#updraft-navtab-settings-content select.updraft_interval, #updraft-navtab-settings-content select#updraft_interval_increments').on('change', function() {
				updraft_update_incremental_selector();
			});
			
			// Set initial values
			updraft_update_incremental_selector();
		});
		</script>
		<?php
	}
}
