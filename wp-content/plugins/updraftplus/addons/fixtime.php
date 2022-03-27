<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: fixtime:Time and Scheduling
Description: Allows you to specify the exact time at which backups will run, and create more complex retention rules
Version: 2.1
Shop: /shop/fix-time/
Latest Change: 1.12.3
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_AddOn_FixTime;

class UpdraftPlus_AddOn_FixTime {

	public function __construct() {
		add_filter('updraftplus_schedule_firsttime_files', array($this, 'starttime_files'));
		add_filter('updraftplus_schedule_firsttime_db', array($this, 'starttime_db'));
		add_filter('updraftplus_schedule_showfileopts', array($this, 'schedule_showfileopts'), 10, 2);
		add_filter('updraftplus_schedule_showdbopts', array($this, 'schedule_showdbopts'), 10, 2);
		add_filter('updraftplus_fixtime_ftinfo', array($this, 'return_empty_string'));
		add_filter('updraftplus_schedule_sametimemsg', array($this, 'schedule_sametimemsg'));

		// Retention rules
		add_filter('updraftplus_group_backups_for_pruning', array($this, 'group_backups_for_pruning'), 10, 3);
		add_action('updraftplus_after_filesconfig', array($this, 'after_filesconfig'));
		add_action('updraftplus_after_dbconfig', array($this, 'after_dbconfig'));
		add_filter('updraftplus_prune_or_not', array($this, 'prune_or_not'), 10, 7);
		add_filter('updraftplus_get_settings_meta', array($this, 'get_settings_meta'));
	}

	/**
	 * Purpose of this function: place the backups into groups, where each backup in the same group is governed by the same pruning rule
	 *
	 * @param  String $groups
	 * @param  Array  $backup_history must be already sorted in date order (most recent first)
	 * @param  String $type           'db'|'files'
	 * @return Array
	 */
	public function group_backups_for_pruning($groups, $backup_history, $type) {

		if (is_array($groups)) return $groups;
		$groups = array();
		
		if (empty($backup_history)) return $groups;
		
		global $updraftplus;

		$wp_cron_unreliability_margin = (defined('UPDRAFTPLUS_PRUNE_MARGIN') && is_numeric(UPDRAFTPLUS_PRUNE_MARGIN)) ? UPDRAFTPLUS_PRUNE_MARGIN : 900;
		
		$retain_extrarules = UpdraftPlus_Options::get_updraft_option('updraft_retain_extrarules');
		
		if (!is_array($retain_extrarules)) $retain_extrarules = array();
		if (!isset($retain_extrarules['db'])) $retain_extrarules['db'] = array();
		if (!isset($retain_extrarules['files'])) $retain_extrarules['files'] = array();

		$extra_rules = $retain_extrarules[$type];

		uasort($extra_rules, array($this, 'soonest_first'));

		// For each backup set in the history, we go through it, and work out which is the 'latest' rule to apply to it - and put it in the corresponding group. Then, return the groups.
		
		$backup_run_time = empty($updraftplus->backup_time) ? time() : $updraftplus->backup_time;

		// We add on 15 minutes so that the vagaries of WP's cron system are less likely to intervene - backups that ran up to 10 minutes later than the exact time will be included
		
		$last_rule = empty($extra_rules) ? false : max(array_keys($extra_rules));

		$there_are_some_multiple_periods_in = false;
		
		foreach ($backup_history as $backup_datestamp => $backup_to_examine) {

			$backup_age = $backup_run_time - $backup_datestamp + $wp_cron_unreliability_margin;
		
			// Find the relevant rule at this stage
			$latest_relevant_index = false;
			foreach ($extra_rules as $i => $rule) {
			
				$rule_interpreted = $this->interpret_rule($rule);
				if (!is_array($rule_interpreted)) continue;
			
				list ($after_howmany, $after_period, $every_howmany, $every_period) = $rule_interpreted;

				// Get the times in seconds
				$after_time = $after_howmany * $after_period;
				if ($backup_age > $after_time) {
					$latest_relevant_index = $i;
				}
			}

			$group_number = (false === $latest_relevant_index) ? 0 : $latest_relevant_index+1;
			
			$process_order = 'keep_newest';
			
			if (false !== $latest_relevant_index) {
			
				// The last set needs splitting up into further sets - one set for each period specified in the rule. Only the final set-within-the-last-set should be keep_newest - that gets set later, once we know how many sets there actually are
				$process_order = 'keep_oldest';
			
				if ($latest_relevant_index == $last_rule) {
					
					$rule = $extra_rules[$latest_relevant_index];
					
					$rule_interpreted = $this->interpret_rule($rule);
					if (is_array($rule_interpreted)) {
						list ($after_howmany, $after_period, $every_howmany, $every_period) = $rule_interpreted;
						// Get the times in seconds
						$after_time = $after_howmany * $after_period;
						$one_every = $every_howmany * $every_period;
				
						$how_far_into_period = $backup_age - $after_time;
						$how_many_periods_in = floor($how_far_into_period / $one_every);
				
						if ($how_many_periods_in > 0) {
							$there_are_some_multiple_periods_in = true;
							$group_number += $how_many_periods_in;
						}
				
					}
				}

			}

			if (!isset($groups[$group_number])) $groups[$group_number] = array(
				'rule' => (false === $latest_relevant_index) ? null : $extra_rules[$latest_relevant_index],
				'process_order' => $process_order,
				'sets' => array(),
			);
			$groups[$group_number]['sets'][$backup_datestamp] = $backup_to_examine;
			
		}
		
		// If multiple rules exist, and if the final group got split, then in that group, the newest (not oldest) should be kept
		$highest_group_number = max(array_keys($groups));
		if ($highest_group_number > 0 && $there_are_some_multiple_periods_in) {
			$groups[$highest_group_number]['process_order'] = 'keep_newest';
		}
		
		return $groups;
	}
	
	private function interpret_rule($rule) {
		if (!is_array($rule) || !isset($rule['after-howmany']) || !isset($rule['after-period']) || !isset($rule['every-howmany']) || !isset($rule['every-period'])) return false;
		$after_howmany = $rule['after-howmany'];
		$after_period = $rule['after-period'];
		if (!is_numeric($after_howmany) || $after_howmany < 0) return false;
		// Fix historic bug - 'week' got saved as the number of seconds in 4 weeks instead...
		if (2419200 == $after_period) $after_period = 604800;
		if ($after_period < 3600) $after_period = 3600;
		$every_howmany = $rule['every-howmany'];
		$every_period = $rule['every-period'];
		// Fix historic bug - 'week' got saved as the number of seconds in 4 weeks instead...
		if (!is_numeric($every_howmany) || $every_howmany < 1) return false;
		if (2419200 == $every_period) $every_period = 604800;
		if ($every_period < 3600) $every_period = 3600;
		return array($after_howmany, $after_period, $every_howmany, $every_period);
	}
	
	/**
	 * TODO: Sort this doc block out
	 * Backup sets will get run through this filter in "keep" order (i.e. within the same group, they are sent through in order of "keep first") - which is assumed (in the function below) to be by time, either ascending or descending
	 * $type is 'files' or 'db', not to be confused with entity (plugins/themes/db/db1 etc.)
	 *
	 * @param  string $prune_it
	 * @param  string $type
	 * @param  string $backup_datestamp
	 * @param  string $entity
	 * @param  string $entity_how_many
	 * @param  string $rule
	 * @param  string $group_id
	 * @return boolean
	 */
	public function prune_or_not($prune_it, $type, $backup_datestamp, $entity, $entity_how_many, $rule, $group_id) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		$debug = UpdraftPlus_Options::get_updraft_option('updraft_debug_mode');

		static $last_backup_seen_at = array();
		static $last_relevant_backup_kept_at = array();

		if (!isset($last_backup_seen_at[$group_id])) $last_backup_seen_at[$group_id] = array();
		if (!isset($last_relevant_backup_kept_at[$group_id])) $last_relevant_backup_kept_at[$group_id] = array();

		if (!isset($last_backup_seen_at[$group_id][$entity])) $last_backup_seen_at[$group_id][$entity] = false;
		if (!isset($last_relevant_backup_kept_at[$group_id][$entity])) $last_relevant_backup_kept_at[$group_id][$entity] = false;

		global $updraftplus;

		$wp_cron_unreliability_margin = (defined('UPDRAFTPLUS_PRUNE_MARGIN') && is_numeric(UPDRAFTPLUS_PRUNE_MARGIN)) ? UPDRAFTPLUS_PRUNE_MARGIN : 900;

		if ($debug) $updraftplus->log("dbprune examine: $backup_datestamp, type=$type, entity=$entity, entry_prune_it=$prune_it, last_relevant_backup_kept_at=".$last_relevant_backup_kept_at[$group_id][$entity].", last_backup_seen_at=".$last_backup_seen_at[$group_id][$entity].", rule=".json_encode($rule), 'debug');
		
		// If it's already being pruned, then we have nothing to do
		if ($prune_it) {
			$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;
			return $prune_it;
		}

/*
		$backup_run_time = $updraftplus->backup_time;

		static $retain_extrarules = false;
		if (!is_array($retain_extrarules)) {
			$retain_extrarules = UpdraftPlus_Options::get_updraft_option('updraft_retain_extrarules');
			if (!is_array($retain_extrarules)) $retain_extrarules = array();
			if (!isset($retain_extrarules['db'])) $retain_extrarules['db'] = array();
			if (!isset($retain_extrarules['files'])) $retain_extrarules['files'] = array();
			$db = $retain_extrarules['db'];
			$files = $retain_extrarules['files'];
			uasort($db, array($this, 'soonest_first'));
			uasort($files, array($this, 'soonest_first'));
			$retain_extrarules['db'] = $db;
			$retain_extrarules['files'] = $files;
		}

		$extra_rules = (is_array($retain_extrarules) && isset($retain_extrarules[$type])) ? $retain_extrarules[$type] : array();

		// We add on 15 minutes so that the vagaries of WP's cron system are less likely to intervene - backups that ran up to 10 minutes later than the exact time will be included
		$backup_age = $backup_run_time - $backup_datestamp + $wp_cron_unreliability_margin;

		// Find the relevant rule at this stage
		$latest_relevant_index = false;
		foreach ($extra_rules as $i => $rule) {
			// Drop broken rules
			if (!is_array($rule) || !isset($rule['after-howmany']) || !isset($rule['after-period']) || !isset($rule['every-howmany']) || !isset($rule['every-period'])) continue;
			$after_howmany = $rule['after-howmany'];
			$after_period = $rule['after-period'];
			if (!is_numeric($after_howmany) || $after_howmany < 0) continue;
			// Fix historic bug - 'week' got saved as the number of seconds in 4 weeks instead...
			if ($after_period == 2419200) $after_period = 604800;
			if ($after_period < 3600) $after_period = 3600;
			$every_howmany = $rule['every-howmany'];
			$every_period = $rule['every-period'];
			// Fix historic bug - 'week' got saved as the number of seconds in 4 weeks instead...
			if (!is_numeric($every_howmany) || $every_howmany < 1) continue;
			if ($every_period == 2419200) $every_period = 604800;
			if ($every_period < 3600) $every_period = 3600;
			// Finally, get the times in seconds
			$after_time = $after_howmany * $after_period;
			if ($backup_age > $after_time) {
				$latest_relevant_index = $i;
			}
		}

		if ($debug) $updraftplus->log("backup_age=$backup_age, type=$type, entity=$entity, latest_relevant_index: ".serialize($latest_relevant_index), 'debug');

		if (false === $latest_relevant_index) {
			// There are no rules which apply to this backup (it's not old enough)
			$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;
			return false;
		}
		$rule = $extra_rules[$latest_relevant_index];
*/
		if (empty($rule)) {
			// There are no rules which apply to this backup (which would usually mean, it's not old enough)
			$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;
			return false;
		}

// if ($debug) $updraftplus->log("last_relevant_backup_kept_at=$last_relevant_backup_kept_at[$group_id][$entity], last_backup_seen_at=".$last_backup_seen_at[$group_id][$entity].", rule=".serialize($rule), 'debug');

		// Is this the first relevant (i.e. old enough) backup we've come across?
		if (!$last_backup_seen_at[$group_id][$entity] || !$last_relevant_backup_kept_at[$group_id][$entity]) {
			$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;
			$last_relevant_backup_kept_at[$group_id][$entity] = $backup_datestamp;
			if ($debug) $updraftplus->log("Keeping this backup, as it is the first relevant (i.e. old enough) backup we've come across for the current rule");
			return false;
		}

		$every_time = $rule['every-howmany'] * $rule['every-period'];

		// At this stage, we know that the backup's age is relevant to the rule, and that a previous old-enough backup has been kept. Now we just need to kept the time between them.
		// We want an unsigned result, as potentially the backups may be being fed through in either forward or reverse order
		$time_from_backup_to_last_kept = $last_relevant_backup_kept_at[$group_id][$entity] - $backup_datestamp;
		$time_from_backup_to_last_kept_abs = absint($time_from_backup_to_last_kept);

		// Again, apply a 15-minute margin
		if ($time_from_backup_to_last_kept_abs > $every_time - $wp_cron_unreliability_margin) {
			// Keep it - enough time has passed
			$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;
			$last_relevant_backup_kept_at[$group_id][$entity] = $backup_datestamp;
			if ($debug) $updraftplus->log("Will keep - enough time different to the last backup. time_from_backup_to_last_kept=$time_from_backup_to_last_kept, every_time=$every_time", 'debug');
			return false;
		}

		if ($debug) $updraftplus->log("Will prune ($entity): backup is older than ".$rule['after-howmany']." periods of ".$rule['after-period']." s, and a backup ".$time_from_backup_to_last_kept." s more recent was kept (which is within the configured ".$rule['every-howmany']." periods of ".$rule['every-period']." s = ".$every_time." s)", 'debug');

		$last_backup_seen_at[$group_id][$entity] = $backup_datestamp;

		return true;

	}

	/**
	 * WP 3.7+ has __return_empty_string() - but we support 3.2+
	 *
	 * @return String
	 */
	public function return_empty_string() {
		return '';
	}

	public function after_dbconfig() {
		echo '<div id="updraft_retain_db_rules"></div><div><a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" id="updraft_retain_db_addnew" class="updraft_icon_link" aria-label="'.__('Add an additional database retention rule', 'updraftplus').'"><span class="dashicons dashicons-plus"></span>'.__('Add an additional retention rule...', 'updraftplus').'</a></div>';
	}

	public function after_filesconfig() {
		add_action('admin_footer', array($this, 'admin_footer_extraretain_js'));
		echo '<div id="updraft_retain_files_rules"></div><div><a href="'.esc_url(UpdraftPlus::get_current_clean_url()).'" id="updraft_retain_files_addnew" class="updraft_icon_link" aria-label="'.__('Add an additional file retention rule', 'updraftplus').'"><span class="dashicons dashicons-plus"></span>'.__('Add an additional retention rule...', 'updraftplus').'</a></div>';
	}

	public function soonest_first($a, $b) {
		if (!is_array($a)) {
			if (!is_array($b)) return 0;
			return 1;
		} elseif (!is_array($b)) {
			return -1;
		}
		$after_howmany_a = isset($a['after-howmany']) ? absint($a['after-howmany']) : 0;
		$after_howmany_b = isset($b['after-howmany']) ? absint($b['after-howmany']) : 0;
		$after_period_a = isset($a['after-period']) ? absint($a['after-period']) : 0;
		$after_period_b = isset($b['after-period']) ? absint($b['after-period']) : 0;
		$after_a = $after_howmany_a * $after_period_a;
		$after_b = $after_howmany_b * $after_period_b;
		if ($after_a == $after_b) return 0;
		return ($after_a < $after_b) ? -1 : 1;
	}

	public function get_settings_meta($meta) {
		if (!is_array($meta)) return $meta;
		$meta['retain_rules'] = array(
			'files' => $this->javascript_retain_rules('files', 'return'),
			'db' => $this->javascript_retain_rules('db', 'return'),
		);
		return $meta;
	}
	
	public function admin_footer_extraretain_js() {
		?>
		<script>
		jQuery(function($) {
			<?php
				$this->javascript_retain_rules('files');
				$this->javascript_retain_rules('db');
			?>
			var db_index = 0;
			var files_index = 0;
			$.each(retain_rules_files, function(index, rule) {
				add_rule('files', rule.after_howmany, rule.after_period, rule.every_howmany, rule.every_period);
			});
			$.each(retain_rules_db, function(index, rule) {
				add_rule('db', rule.after_howmany, rule.after_period, rule.every_howmany, rule.every_period);
			});
					
			$('#updraft_retain_db_addnew').on('click', function(e) {
				e.preventDefault();
				add_rule('db', 12, 604800, 1, 604800);
			});
			$('#updraft_retain_files_addnew').on('click', function(e) {
				e.preventDefault();
				add_rule('files', 12, 604800, 1, 604800);
			});
			$('#updraft_retain_db_rules, #updraft_retain_files_rules').on('click', '.updraft_retain_rules_delete', function(e) {
				e.preventDefault();
				$(this).parent('.updraft_retain_rules').slideUp(function() {$(this).remove();});
			});
			function add_rule(type, howmany_after, period_after, howmany_every, period_every) {
				var selector = 'updraft_retain_'+type+'_rules';
				var index;
				if ('db' == type) {
					db_index++;
					index = db_index;
				} else {
					files_index++;
					index = files_index;
				}
				$('#'+selector).append(
					'<div class="updraft_retain_rules '+selector+'_entry">'+
					updraftlion.forbackupsolderthan+' '+rule_period_selector(type, index, 'after', howmany_after, period_after)+' keep no more than 1 backup every '+rule_period_selector(type, index, 'every', howmany_every, period_every)+
					' <a href="#" title="'+updraftlion.deletebutton+'" class="updraft_retain_rules_delete"><span class="dashicons dashicons-no"></span></a></div>'
				)
			}
			function rule_period_selector(type, index, which, howmany_value, period) {
				var nameprefix = "updraft_retain_extrarules["+type+"]["+index+"]["+which+"-";
				var ret = '<input type="number" min="1" step="1" class="additional-rule-width" name="'+nameprefix+'howmany]" value="'+howmany_value+'"> \
				<select name="'+nameprefix+'period]">\
				<option value="3600"';
				if (period == 3600) { ret += ' selected="selected"'; }
				ret += '>'+updraftlion.hours+'</option>\
				<option value="86400"';
				if (period == 86400) { ret += ' selected="selected"'; }
				ret += '>'+updraftlion.days+'</option>\
				<option value="604800"';
				if (period == 604800) { ret += ' selected="selected"'; }
				ret += '>'+updraftlion.weeks+'</option>\
				</select>';
				return ret;
			}
		});
		</script>
		<?php
	}

	public function javascript_retain_rules($type, $format = 'printjs') {
	
		$extra_rules = UpdraftPlus_Options::get_updraft_option('updraft_retain_extrarules');
		if (!is_array($extra_rules)) $extra_rules = array();
		$extra_rules = empty($extra_rules[$type]) ? array() : $extra_rules[$type];
	
		uasort($extra_rules, array($this, 'soonest_first'));
		$processed_rules = array();
		foreach ($extra_rules as $i => $rule) {
			if (!is_array($rule) || !isset($rule['after-howmany']) || !isset($rule['after-period']) || !isset($rule['every-howmany']) || !isset($rule['every-period'])) continue;
			$after_howmany = $rule['after-howmany'];
			$after_period = $rule['after-period'];
			
			// Fix historic bug - stored the value of 28 days' worth of seconds, instead of 7
			if (2419200 == $after_period) $after_period = 604800;
			
			// Best not to just drop the rule if it is invalid
			if (!is_numeric($after_howmany) || $after_howmany < 0) continue;
			if ($after_period <3600) $after_period = 3600;
			if (3600 != $after_period && 86400 != $after_period && 604800 != $after_period) continue;
			$every_howmany = $rule['every-howmany'];
			$every_period = $rule['every-period'];
			
			// Fix historic bug - stored the value of 28 days' worth of seconds, instead of 7
			if (2419200 == $every_period) $every_period = 604800;

			// Best not to just drop the rule if it is invalid
			if (!is_numeric($every_howmany) || $every_howmany < 1) continue;
			if ($every_period <3600) $every_period = 3600;
			if (3600 != $every_period && 86400 != $every_period && 604800 != $every_period) continue;

			$processed_rules[] = array('index' => $i, 'after_howmany' => $after_howmany, 'after_period' => $after_period, 'every_howmany' => $every_howmany, 'every_period' => $every_period);
			// echo "add_rule('$type', $i, $after_howmany, $after_period, $every_howmany, $every_period);\n";
		}
		if ('return' == $format) {
			return $processed_rules;
		} else {
			echo "var retain_rules_$type = ".json_encode($processed_rules).";\n";
		}
	}

	public function schedule_sametimemsg() {
		return htmlspecialchars(__('(at same time as files backup)', 'updraftplus'));
	}

	public function starttime_files() {
		return $this->compute('files');
	}

	public function starttime_db() {
		return $this->compute('db');
	}

	private function parse($start_time) {
		preg_match("/^(\d+):(\d+)$/", $start_time, $matches);
		if (empty($matches[1]) || !is_numeric($matches[1]) || $matches[1]>23) {
			$start_hour = 0;
		} else {
			$start_hour = (int) $matches[1];
		}
		if (empty($matches[2]) || !is_numeric($matches[2]) || $matches[1]>59) {
			$start_minute = 5;
			if ($start_minute>60) {
				$start_minute = $start_minute-60;
				$start_hour++;
				if ($start_hour>23) $start_hour =0;
			}
		} else {
			$start_minute = (int) $matches[2];
		}
		return array($start_hour, $start_minute);
	}

	private function compute($whichtime) {
		// Returned value should be in UNIX time.

		$unixtime_now = time();
		// Convert to date
		$now_timestring_gmt = gmdate('Y-m-d H:i:s', $unixtime_now);

		// Convert to blog's timezone
		$now_timestring_blogzone = get_date_from_gmt($now_timestring_gmt, 'Y-m-d H:i:s');

		$int_key = ('db' == $whichtime) ? '_database' : '';
		$sched = (isset($_POST['updraft_interval'.$int_key])) ? $_POST['updraft_interval'.$int_key] : 'manual';

		// HH:MM, in blog time zone
		// This function is only called from the options validator, so we don't read the current option
		// $start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_'.$whichtime);
		$start_time = (isset($_POST['updraft_starttime_'.$whichtime])) ? $_POST['updraft_starttime_'.$whichtime] : '00:00';

		list ($start_hour, $start_minute) = $this->parse($start_time);

		// Was a particular week-day specified?
		if (isset($_POST['updraft_startday_'.$whichtime]) && ('weekly' == $sched || 'monthly' == $sched || 'fortnightly' == $sched)) {
			// All the monthly stuff is done here, since it has different logic
			if ('monthly' == $sched) {
				// Get specified day of the month in range 1-28
				$startday = min(absint($_POST['updraft_startday_'.$whichtime]), 28);
				if ($startday < 1) $startday = 1;
				// Get today's day of month in range 1-31
// $day_today_blogzone = get_date_from_gmt($now_timestring_gmt, 'j');

				$thismonth_timestring = 'Y-m-'.sprintf("%02d", $startday).' '.sprintf("%02d:%02d", $start_hour, $start_minute).':00';

				$thismonth_time = get_date_from_gmt($now_timestring_gmt, $thismonth_timestring);
				$thismonth_unixtime = get_gmt_from_date($thismonth_time, 'U');

				// Is that in the past? If so, then wind on a month.
				if ($thismonth_unixtime < $unixtime_now) {
					return strtotime("@".$thismonth_unixtime." + 1 month");
				} else {
					return $thismonth_unixtime;
				}
			} else {
				// Get specified day of week in range 0-6
				$startday = min(absint($_POST['updraft_startday_'.$whichtime]), 6);
				// Get today's day of week in range 0-6
				$day_today_blogzone = get_date_from_gmt($now_timestring_gmt, 'w');
				if ($day_today_blogzone != $startday) {
					if ($startday<$day_today_blogzone) $startday +=7;
					$new_startdate_unix = $unixtime_now + ($startday-$day_today_blogzone)*86400;
					$now_timestring_blogzone = get_date_from_gmt(gmdate('Y-m-d H:i:s', $new_startdate_unix), 'Y-m-d H:i:s');
				}
			}
		}

		// Now, convert the start time HH:MM from blog time to UNIX time
		$start_time_unix = get_gmt_from_date(substr($now_timestring_blogzone, 0, 11).sprintf('%02d', $start_hour).':'.sprintf('%02d', $start_minute).':00', 'U');

		// That may have already passed for today
		if ($start_time_unix<time()) {
			if ('weekly' == $sched || 'fortnightly' == $sched) {
				$start_time_unix = $start_time_unix + 86400*7;
			} elseif ('monthly' == $sched) {
				error_log("This code path is impossible, or so it was thought!");
			} else {
				$start_time_unix =$start_time_unix+86400;
			}
		}

		return $start_time_unix;
	}

	private function day_selector($id, $selected_interval = 'manual') {
		global $wp_locale;

		$day_selector = '<select title="'.__('Day to run backups', 'updraftplus').'" name="'.$id.'" id="'.$id.'">';

		$opt = UpdraftPlus_Options::get_updraft_option($id, 0);

		$start_from = ('monthly' == $selected_interval) ? 1 : 0;
		$go_to = ('monthly' == $selected_interval) ? 28 : 6;

		for ($day_index = $start_from; $day_index <= $go_to; $day_index++) :
			$selected = ($opt == $day_index) ? 'selected="selected"' : '';
			$day_selector .= "\n\t<option value='" . $day_index . "' $selected>";
			$day_selector .= ('monthly' == $selected_interval) ? $day_index : $wp_locale->get_weekday($day_index);
			$day_selector .= '</option>';
		endfor;
		$day_selector .= '</select>';
		return $day_selector;
	}

	public function starting_widget($start_hour, $start_minute, $day_selector_id, $time_selector_id, $selected_interval = 'manual') {
		return __('starting from next time it is', 'updraftplus').' '.$this->day_selector($day_selector_id, $selected_interval).'<input title="'.__('Start time', 'updraftplus').__('Enter in format HH:MM (e.g. 14:22).', 'updraftplus').' '.htmlspecialchars(__('The time zone used is that from your WordPress settings, in Settings -> General.', 'updraftplus')).'" type="text" class="fix-time" maxlength="5" name="'.$time_selector_id.'" value="'.sprintf('%02d', $start_hour).':'.sprintf('%02d', $start_minute).'">';
	}

	public function schedule_showdbopts($disp, $selected_interval) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_db');
		list ($start_hour, $start_minute) = $this->parse($start_time);
		return $this->starting_widget($start_hour, $start_minute, 'updraft_startday_db', 'updraft_starttime_db', $selected_interval);
	}

	public function schedule_showfileopts($disp, $selected_interval) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$start_time = UpdraftPlus_Options::get_updraft_option('updraft_starttime_files');
		list ($start_hour, $start_minute) = $this->parse($start_time);
		return $this->starting_widget($start_hour, $start_minute, 'updraft_startday_files', 'updraft_starttime_files', $selected_interval);
	}
}
