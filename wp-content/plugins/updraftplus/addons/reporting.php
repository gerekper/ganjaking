<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: reporting:Sophisticated reporting options
Description: Provides various new reporting capabilities
Version: 2.6
Shop: /shop/reporting/
Latest Change: 2.15.8
*/
// @codingStandardsIgnoreEnd

// Future possibility: more reporting options; e.g. HTTP ping; tweet, etc.

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

new UpdraftPlus_Addon_Reporting;

class UpdraftPlus_Addon_Reporting {

	private $emails;

	private $warningsonly;

	private $history;

	private $syslog;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter('updraftplus_showbackup_date', array($this, 'showbackup_date'), 10, 5);
		add_filter('updraft_backupnow_options', array($this, 'backupnow_options'), 10, 2);
		add_filter('updraftplus_report_form', array($this, 'updraftplus_report_form'));
		add_action('updraftplus_configprint_expertoptions', array($this, 'configprint_expertoptions'), 9);
		add_filter('updraftplus_saveemails', array($this, 'saveemails'), 10, 2);
		add_filter('updraft_report_sendto', array($this, 'updraft_report_sendto'), 10, 5);
		add_filter('updraftplus_email_whichaddresses', array($this, 'email_whichaddresses'));
		add_filter('updraftplus_email_backup', array($this, 'email_backup'), 10, 4);
		add_filter('updraft_report_subject', array($this, 'updraft_report_subject'), 10, 3);
		add_filter('updraft_report_body', array($this, 'updraft_report_body'), 10, 6);
		add_filter('updraft_report_attachments', array($this, 'updraft_report_attachments'));
		add_filter('updraftplus_email_backup_skip_log_message', array($this, 'backup_skip_log_message'), 10, 4);
		add_filter('updraft_backupnow_modal_afteroptions', array($this, 'backupnow_modal_afteroptions'), 10, 2);
		add_filter('updraft_report_downloadable_file_link', array($this, 'generate_downloadable_file_link'), 10, 4);
		add_action('updraft_final_backup_history', array($this, 'final_backup_history'));
		add_action('updraft_report_finished', array($this, 'report_finished'));
		add_action('init', array($this, 'init'));
		$this->log_ident = (defined('UPDRAFTPLUS_LOG_IDENT')) ? UPDRAFTPLUS_LOG_IDENT : 'updraftplus';
		$this->log_facility = (defined('UPDRAFTPLUS_LOG_FACILITY')) ? UPDRAFTPLUS_LOG_FACILITY : LOG_USER;
	}

	/**
	 * Runs upon the WordPress action 'init'
	 */
	public function init() {
		if (!class_exists('UpdraftPlus_Options')) return;
		if (!UpdraftPlus_Options::get_updraft_option('updraft_log_syslog', false) || !function_exists('openlog') || !function_exists('syslog')) return;
		if (false !== ($this->syslog = openlog($this->log_ident, LOG_ODELAY|LOG_PID, $this->log_facility))) add_filter('updraftplus_logline', array($this, 'logline'), 10, 3);
	}

	public function showbackup_date($date, $backup, $jobdata, $key, $simple_format) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if (!is_array($backup) || empty($backup['label'])) return $date;
		if ($simple_format) {
			return $date.' - '.htmlspecialchars($backup['label']);
		} else {
			return $date.'<span class="updraft-backup-label">'.htmlspecialchars($backup['label']).'</span>';
		}
	}

	/**
	 * Runs upon the WP filter updraft_backupnow_modal_afteroptions
	 *
	 * @param String $ret	 - unfiltered value to return
	 * @param String $prefix - prefix to use
	 *
	 * @return String
	 */
	public function backupnow_modal_afteroptions($ret, $prefix) {

		$ret .= '<p id="'.$prefix.'backupnow_label_container" class="new-backups-only">
			<label for="'.$prefix.'backupnow_label">'.__('Your label for this backup (optional)', 'updraftplus').':</label> <input type="text" id="'.$prefix.'backupnow_label" name="label" size="40" maxlength="40" value="';

		if ('remotesend_' == $prefix) {
			$label = preg_replace('#^https?://#i', '', network_site_url());
			
			$backup_of = __('Backup of:', 'updraftplus').' ';

			if (strlen($backup_of.$label) <= 40) $label = $backup_of.$label;

			$ret .= esc_attr($label);
		}

		$ret .= '"></p>';

		return $ret;
	}

	/**
	 * Adjust the backup-now options based on the incoming request
	 *
	 * @param Array $options - the current options
	 * @param Array $request - the incoming request
	 *
	 * @return Array - the filtered options
	 */
	public function backupnow_options($options, $request) {
		if (!is_array($options)) return $options;
		// See: https://trello.com/c/NH83ZCnj/494
		if (!empty($request['backupnow_label']) && is_string($request['backupnow_label']))
		$options['label'] = substr($request['backupnow_label'], 0, 40);
		return $options;
	}

	public function logline($line, $nonce, $level) {
		// See https://php.net/manual/en/function.syslog.php for descriptions of the log level meanings
		if ('error' == $level) {
			$pri = LOG_WARNING;
		} elseif ('warning' == $level) {
			$pri = LOG_NOTICE;
		} else {
			$pri = LOG_INFO;
		}
		@syslog($pri, "($nonce) $line");// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		return $line;
	}

	public function final_backup_history($history) {
		$this->history = $history;
	}

	public function updraft_report_attachments($attachments) {
		// Always attach the log file
		global $updraftplus;
		$attachments[0] = $updraftplus->logfile_name;
		return $attachments;
	}

	/**
	 * TODO: Jobdata is passed in, rather than live, because the live jobdata may have moved on from the time which the point should reflectg (e.g. an incremental backup was subsequently started)
	 *
	 * @param  string $report
	 * @param  string $final_message
	 * @param  string $contains
	 * @param  string $errors
	 * @param  string $warnings
	 * @param  array  $jobdata
	 * @return string
	 */
	public function updraft_report_body($report, $final_message, $contains, $errors, $warnings, $jobdata) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	
		global $updraftplus;

		$error_count = 0;
		foreach ($errors as $err) {
			if ((is_string($err) || is_wp_error($err)) || (is_array($err) && 'error' == $err['level'])) {
				$error_count++;
			}
		}
		$warning_count = count($warnings);

		$history = $this->history;
		$debug = UpdraftPlus_Options::get_updraft_option('updraft_debug_mode');

		$errors_and_warns = sprintf(__('%d errors, %d warnings', 'updraftplus'), $error_count, $warning_count);

		$file_entities = $updraftplus->get_backupable_file_entities(true, true);

		$backup_time = empty($jobdata['incremental_run_start']) ? $jobdata['backup_time'] : $jobdata['incremental_run_start'];
		
		$date = get_date_from_gmt(gmdate('Y-m-d H:i:s', $backup_time), 'Y-m-d H:i');

		$time_taken = time() - $backup_time;
		$hrs = floor($time_taken/3600);
		$mins = floor(($time_taken-3600*$hrs)/60);
		$secs = $time_taken - 3600*$hrs - 60*$mins;

		$services = empty($jobdata['service']) ? array('none') : $jobdata['service'];
		if (!is_array($services)) $services = array('none');

		$time_taken = sprintf(__("%d hours, %d minutes, %d seconds", 'updraftplus'), $hrs, $mins, $secs);

		ob_start();
		?>
<style type="text/css">h1, h2, h3, p, pre, ul { clear: both; margin: 0; padding: 15px 0 0;} h1, h3, ul { margin-top: 2px; margin-bottom: 0; }</style>
<h1><?php echo get_bloginfo('name').': '.__('Backup Report', 'updraftplus');?></h1>
<p style="float: left; clear: left; margin: 0 0 8px;"><em><?php printf(__('Backup made by %s', 'updraftplus'), '<a href="https://updraftplus.com" target="_blank">UpdraftPlus '.$updraftplus->version); ?></a></em></p>
<?php
	if (!class_exists('UpdraftPlus_Notices')) include_once(UPDRAFTPLUS_DIR.'/includes/updraftplus-notices.php');
	global $updraftplus_notices;
	$ws_advert = $updraftplus_notices->do_notice(false, 'report', true);
	if ($ws_advert) {
	echo '<div style="max-width: 700px; border: 1px solid; border-radius: 4px; font-size:110%; line-height: 110%; padding:8px; margin: 6px 0 12px; clear:left;">'.$ws_advert.'</div>';
	}
?>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Backup of:', 'updraftplus'); ?></div> <div style="float: left;"><a href="<?php echo esc_attr(site_url()); ?>"><?php echo site_url();?></a></div></div>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Latest status:', 'updraftplus');?></div> <div style="float: left;"><?php echo $final_message; ?></div></div>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Backup began:', 'updraftplus');?></div> <div style="float: left;"><?php echo $date; ?></div></div>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Contains:', 'updraftplus');?></div> <div style="float: left;"><?php echo $contains; ?></div></div>
<?php
	$extra_messages = apply_filters('updraftplus_report_extramessages', array());
	$extra_msg = '';
	if (is_array($extra_messages)) {
	foreach ($extra_messages as $msg) {
		$extra_msg .= '<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;">'.htmlspecialchars($msg['key']).'</div> <div style="float: left;">'.htmlspecialchars($msg['val']).'</div></div>';
	}
	}
	echo $extra_msg;
?>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Errors / warnings:', 'updraftplus');?></div> <div style="float: left;"><?php echo $errors_and_warns; ?></div></div>
<?php
		if ($updraftplus->error_count() > 0) {
	echo '<h2>'.__('Errors', 'updraftplus')."</h2>\n<ul>";
	foreach ($updraftplus->errors as $err) {
		if (is_wp_error($err)) {
			foreach ($err->get_error_messages() as $msg) {
				echo "<li>".htmlspecialchars(rtrim($msg))."</li>\n";
			}
		} elseif (is_array($err) && 'error' == $err['level']) {
			echo "<li>".htmlspecialchars(rtrim($err['message']))."</li>\n";
		} elseif (is_string($err)) {
			echo "<li>".htmlspecialchars(rtrim($err))."</li>\n";
		}
	}
	echo "</ul>\n";
		}
		if (is_array($warnings) && count($warnings) >0) {
	echo '<h2>'.__('Warnings', 'updraftplus')."</h2>\n<ul>";
	foreach ($warnings as $err) {
		echo "<li>".rtrim($err)."</li>\n";
	}
	echo "</ul>\n";
	echo '<p><em>'.__('Note that warning messages are advisory - the backup process does not stop for them. Instead, they provide information that you might find useful, or that may indicate the source of a problem if the backup did not succeed.', 'updraftplus').'</em></p>';
		}
		?>
<p>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Time taken:', 'updraftplus');?></div> <div style="float: left;"><?php echo $time_taken;?></div></div>
<div style="width: 100%; display: table; margin-bottom: 5px;"><div style="font-weight: bold; width: 200px; float: left;"><?php echo __('Uploaded to:', 'updraftplus');?></div> <div style="float: left;"><?php

			$show_services = '';
			foreach ($services as $serv) {
	if ('none' == $serv || '' == $serv) {
		$add_none = true;
	} else {
				
		if (isset($updraftplus->backup_methods[$serv])) {
			$show_services .= ($show_services) ? ', '.$updraftplus->backup_methods[$serv] : $updraftplus->backup_methods[$serv];
		} else {
			$show_services .= ($show_services) ? ', '.$serv : $serv;
		}
					
		if (isset($jobdata['remotestorage_extrainfo']) && !empty($jobdata['remotestorage_extrainfo'][$serv])) {
					
			$show_services .= ' ('.$jobdata['remotestorage_extrainfo'][$serv]['pretty'].')';
					
		}
					
	}
			}
			if ('' == $show_services && $add_none) $show_services .= __('None', 'updraftplus');

			echo $show_services."</div></div></p>\n\n";

			$checksums = $updraftplus->which_checksums();

			if (!empty($file_entities)) {
	foreach ($file_entities as $entity => $info) {
		echo $updraftplus->printfile($info['description'], $history, $entity, $checksums, $jobdata);
	}
			}

			if (!empty($history)) {
	foreach ($history as $key => $val) {
		if ('db' == strtolower(substr($key, 0, 2)) && '-size' != substr($key, -5, 5)) {
			echo $updraftplus->printfile(__('Database', 'updraftplus'), $history, $key, $checksums, $jobdata);
		}
	}
			}

			echo '<p>'.__('The log file has been attached to this email.', 'updraftplus')."</p>\n\n";

			if ($debug) {
	echo '<h2>'.__('Debugging information', 'updraftplus')."</h2>\n<pre>";
	print chunk_split(base64_encode(serialize($jobdata)), 76, "\n");
	print "\n";
	print chunk_split(base64_encode(serialize($history)), 76, "\n");
	echo "</pre>";
			}

		$this->html = ob_get_contents();
		ob_end_clean();

		// Lower priority: get there before other plugins which apply templates
		add_filter('wp_mail_content_type', array($this, 'wp_mail_content_type'), 8);

		$report_body = $this->html;
		
		
		return str_replace("\n", "\r\n", strip_tags(preg_replace('#\<style([^\>]*)\>.*\</style\>#', '', $report_body)));

	}

	public function wp_mail_content_type($content_type) {
		// Only convert if the message is text/plain and the template is ok
		if ('text/plain' == $content_type && !empty($this->html)) {
			if (empty($this->added_phpmailer_init_action)) {
				$this->added_phpmailer_init_action = true;
				add_action('phpmailer_init', array($this, 'phpmailer_init'));
			}
			return 'text/html';
		}
		return $content_type;
	}

	public function phpmailer_init($phpmailer) {
		if (empty($this->html)) return;
		$phpmailer->AltBody = wp_specialchars_decode($phpmailer->Body, ENT_QUOTES);
		$phpmailer->Body = $this->html;
	}

	public function report_finished() {
		remove_filter('wp_mail_content_type', array($this, 'wp_mail_content_type'), 8);
		remove_action('phpmail_init', array($this, 'phpmailer_init'));
		if (empty($this->html)) return;
		global $phpmailer;
		if (is_object($phpmailer) && is_a($phpmailer, 'PHPMailer')) {
// $phpmailer->AltBody = '';
// $phpmailer->Body = '';
// $phpmailer->ContentType = 'text/plain';
			// Best just to force WP to get the whole thing again from the beginning
			$phpmailer = null;
		}
		unset($this->html);
	}

	public function updraft_report_subject($subject, $error_count, $warning_count) {
		if ($error_count > 0) {
			$subject .= sprintf(__(' (with errors (%s))'), $error_count);
		} elseif ($warning_count >0) {
			$subject .= sprintf(__(' (with warnings (%s))'), $warning_count);
		}
		return $subject;
	}

	public function updraft_report_sendto($send, $addr, $error_count, $warning_count, $ind) {
		if (null === $this->emails) {
			$this->emails = UpdraftPlus_Options::get_updraft_option('updraft_email', array());
			if (is_string($this->emails)) $this->emails = array($this->emails);
		}
		if (null === $this->warningsonly) {
			$this->warningsonly = UpdraftPlus_Options::get_updraft_option('updraft_report_warningsonly', array());
			if (!is_array($this->warningsonly)) $this->warningsonly = array();
		}

		if (0 == $error_count + $warning_count && isset($this->emails[$ind]) && !empty($this->warningsonly[$ind])) {
			$send = false;
			global $updraftplus;
			$updraftplus->log("No report will be sent to this address, as it is configured to receive them only when there are errors or warnings: ".substr($addr, 0, 5).'...');
		}
		return $send;
	}

	/**
	 * Function for filter updraftplus_email_backup
	 *
	 * @param boolean $doit filter value of updraftplus_email_backup
	 * @param string  $addr email address
	 * @param integer $ind  index of report box
	 * @param string  $type backup entity types
	 * @return boolean filtered value
	 */
	public function email_backup($doit, $addr, $ind, $type) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$wholebackup = UpdraftPlus_Options::get_updraft_option('updraft_report_wholebackup', null);
		$dbbackup = UpdraftPlus_Options::get_updraft_option('updraft_report_dbbackup', null);
		if (is_array($wholebackup) && !empty($wholebackup[$ind]) && empty($dbbackup[$ind])) {
			return true;
		}
		if ('db' == strtolower(substr($type, 0, 2)) && is_array($dbbackup) && !empty($dbbackup[$ind])) {
			return true;
		}
		return false;
	}
	
	/**
	 * Function for filter updraftplus_backup_skip_log_message
	 *
	 * @param string  $log_message  default log message of updraftplus_backup_skip_log_message filter
	 * @param string  $addr         email address
	 * @param integer $ind          index of report box
	 * @param string  $descrip_type backup entity types
	 * @return string log message
	 */
	public function backup_skip_log_message($log_message, $addr, $ind, $descrip_type) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$wholebackup = UpdraftPlus_Options::get_updraft_option('updraft_report_wholebackup', null);
		if (!is_array($wholebackup) || empty($wholebackup[$ind])) {
			return 'You have chosen to not send the backup via the email remote storage option for '.$addr.'. '.$descrip_type.' will not be sent.';
		} else {
			return 'You have chosen to only send the database via the email remote storage option for '.$addr.'. '.$descrip_type.' will not be sent.';
		}
	}

	public function email_whichaddresses() {
		return __('Use the "Reporting" section to configure the email addresses to be used.', 'updraftplus');
	}

	public function admin_footer() {
		?>
		<script>
			jQuery(function($){
			
				var reportbox_index = $('#updraft_report_cell .updraft_reportbox').length + 2;
				
				$('#updraft_report_cell').on('click', '.updraft_reportbox .updraft_reportbox_delete', function() {
					$(this).closest('.updraft_reportbox').fadeOut('medium', function() { $(this).remove(); });
				});
				
				$('#updraft-navtab-settings-content .updraft_report_another').on('click', function(e) {
					e.preventDefault();

					$('#updraft-navtab-settings-content .updraft_report_another_p').before('<div id="updraft_reportbox_'+reportbox_index+'" class="updraft_reportbox updraft-hidden" style="display:none;"><button class="updraft_reportbox_delete" reportbox_index="'+reportbox_index+'" type="button"><span class="dashicons dashicons-no"></span></button>\
<input type="text" title="'+updraftlion.enteremailhere+'" class="updraft_report_email" name="updraft_email['+reportbox_index+']" value="" />\
<label for="updraft_report_warningsonly_'+reportbox_index+'" class="updraft_checkbox"><input class="updraft_report_checkbox" type="checkbox" id="updraft_report_warningsonly_'+reportbox_index+'" name="updraft_report_warningsonly['+reportbox_index+']"> '+updraftlion.sendonlyonwarnings+'</label><div class="updraft_report_wholebackup">\
<label for="updraft_report_wholebackup_'+reportbox_index+'" title="'+updraftlion.emailsizelimits+'" class="updraft_checkbox"><input class="updraft_report_checkbox" type="checkbox" id="updraft_report_wholebackup_'+reportbox_index+'" name="updraft_report_wholebackup['+reportbox_index+']" title="'+updraftlion.emailsizelimits+'"> '+updraftlion.wholebackup+'</label></div><div class="updraft_report_dbbackup updraft_report_disabled">\
<label for="updraft_report_dbbackup_'+reportbox_index+'" title="'+updraftlion.emailsizelimits+'" class="updraft_checkbox"><input class="updraft_report_checkbox" type="checkbox" id="updraft_report_dbbackup_'+reportbox_index+'" disabled name="updraft_report_dbbackup['+reportbox_index+']" title="'+updraftlion.emailsizelimits+'"> '+updraftlion.dbbackup+'</label></div></div>');
					$('#updraft_reportbox_'+reportbox_index).fadeIn();

					reportbox_index++;

				});
				$('#updraft_report_row').on('change', '.updraft_report_wholebackup .updraft_report_checkbox', function() {
					var reportbox = $(this).closest('.updraft_reportbox').find('.updraft_report_dbbackup');
					if ($(this).is(':checked')) {
						reportbox.removeClass('updraft_report_disabled').find('.updraft_report_checkbox').prop('disabled', false);
					} else {
						reportbox.find('.updraft_report_checkbox').prop('checked', false);
						reportbox.addClass('updraft_report_disabled').find('.updraft_report_checkbox').prop('disabled', true);
					}
				});
			});
		</script>
		<?php
	}

	public function updraftplus_report_form() {

		add_action('admin_footer', array($this, 'admin_footer'));

		// Columns: Email address | only send if no errors/warnings

		$out = '<tr id="updraft_report_row">
				<th>'.__('Send reports', 'updraftplus').':</th>
				<td id="updraft_report_cell">';

		// Could be multiple (separated by commas)
		$updraft_email = UpdraftPlus_Options::get_updraft_option('updraft_email');
		$updraft_report_warningsonly = UpdraftPlus_Options::get_updraft_option('updraft_report_warningsonly');
		$updraft_report_wholebackup = UpdraftPlus_Options::get_updraft_option('updraft_report_wholebackup');
		$updraft_report_dbbackup = UpdraftPlus_Options::get_updraft_option('updraft_report_dbbackup');

		if (is_string($updraft_email)) {
			$utmp = $updraft_email;
			$updraft_email = array();
			$updraft_report_warningsonly = array();
			$updraft_report_wholebackup = array();
			foreach (explode(',', $utmp) as $email) {
				// Whole backup only takes effect if 'Email' is chosen as a storage option
				$updraft_email[] = $email;
				$updraft_report_warningsonly[] = false;
				$updraft_report_wholebackup[] = true;
			}
		} elseif (!is_array($updraft_email)) {
			$updraft_email = array();
			$updraft_report_warningsonly = array();
			$updraft_report_wholebackup = array();
		}

		$out .= '<p>'.__('Enter addresses here to have a report sent to them when a backup job finishes.', 'updraftplus').'</p>';

		$ind = 0;
		foreach ($updraft_email as $ikey => $destination) {
			$warningsonly = empty($updraft_report_warningsonly[$ikey]) ? false : true;
			$wholebackup = empty($updraft_report_wholebackup[$ikey]) ? false : true;
			$dbbackup = empty($updraft_report_dbbackup[$ikey]) ? false : true;
			if (!empty($destination)) {
				$ind++;
				$out .= $this->report_box_generator($destination, $ind, $warningsonly, $wholebackup, $dbbackup);
			}
		}

		if (0 === $ind) $out .= $this->report_box_generator('', 0, false, false, false);

		$out .= '<p class="updraft_report_another_p"><a class="updraft_report_another updraft_icon_link" href="'.esc_url(UpdraftPlus::get_current_clean_url()).'#updraft_report_row"><span class="dashicons dashicons-plus"></span>'.__('Add another address...', 'updraftplus').'</a></p>';

		$out .= '</td>
			</tr>';

		return $out;
	}
	
	/**
	 * Renders reporting expert settings
	 */
	public function configprint_expertoptions() {
		?>
		<tr class="expertmode updraft-hidden" style="display:none;">
			<th><?php _e('Log all messages to syslog', 'updraftplus');?>:</th>
			<td><input type="checkbox" id="updraft_log_syslog" name="updraft_log_syslog" value="1" <?php if (UpdraftPlus_Options::get_updraft_option('updraft_log_syslog')) echo 'checked="checked"'; ?>> <br><label for="updraft_log_syslog"><?php _e("Log all messages to syslog (only server admins are likely to want this)", 'updraftplus'); ?></label></td>
		</tr>
		<?php
	}

	public function saveemails($rinput, $input) {
		return $input;
	}

	/**
	 * Generate Email Report Box
	 *
	 * @param string  $addr         email address
	 * @param integer $ind          index of email report
	 * @param boolean $warningsonly boolean boolean Whether send email of warnings
	 * @param boolean $wholebackup  boolean Whether email whole backup checkbox is checked or not
	 * @param boolean $dbbackup     boolean Whether email database backup checkbox is checked or not
	 * @param string  $out          html
	 */
	private function report_box_generator($addr, $ind, $warningsonly, $wholebackup, $dbbackup) {

		$out = '';

		$out .='<div id="updraft_reportbox_'.$ind.'" class="updraft_reportbox">';

		$out .= '<button class="updraft_reportbox_delete" type="button"><span title="'.__('Remove', 'updraftplus').'" class="dashicons dashicons-no"></span></button>';

		$out .= '<input type="text" title="'.esc_attr(__('To send to more than one address, separate each address with a comma.', 'updraftplus')).'" class="updraft_report_email" name="updraft_email['.$ind.']" value="'.esc_attr($addr).'" />';

		$out .= '<label for="updraft_report_warningsonly_'.$ind.'" class="updraft_checkbox"><input '.(($warningsonly) ? 'checked="checked" ' : '').' id="updraft_report_warningsonly_'.$ind.'" class="updraft_report_checkbox" type="checkbox"  name="updraft_report_warningsonly['.$ind.']"> '.__('Send a report only when there are warnings/errors', 'updraftplus').'</label>';

		$out .= '<div class="updraft_report_wholebackup"><label for="updraft_report_wholebackup_'.$ind.'" title="'.esc_attr(sprintf(__('Be aware that mail servers tend to have size limits; typically around %s MB; backups larger than any limits will likely not arrive.', 'updraftplus'), '10-20')).'" class="updraft_checkbox"><input '.(($wholebackup) ? 'checked="checked" ' : '').'class="updraft_report_checkbox" type="checkbox" id="updraft_report_wholebackup_'.$ind.'" name="updraft_report_wholebackup['.$ind.']" title="'.esc_attr(sprintf(__('Be aware that mail servers tend to have size limits; typically around %s MB; backups larger than any limits will likely not arrive.', 'updraftplus'), '10-20')).'"> '.__('When email storage method is enabled, and an email address is entered, also send the backup', 'updraftplus').'</label></div>';

		$out .= '<div class="updraft_report_dbbackup'.((!$wholebackup) ? ' updraft_report_disabled' : '').'"><label for="updraft_report_dbbackup_'.$ind.'" title="'.esc_attr(sprintf(__('Be aware that mail servers tend to have size limits; typically around %s MB; backups larger than any limits will likely not arrive as a result UpdraftPlus will only send Database backups to email.', 'updraftplus'), '10-20')).'" class="updraft_checkbox"><input '.(($dbbackup) ? 'checked="checked" ' : '').'class="updraft_report_checkbox" type="checkbox" '.((!$wholebackup) ? 'disabled ' : '').'id="updraft_report_dbbackup_'.$ind.'" name="updraft_report_dbbackup['.$ind.']" title="'.esc_attr(sprintf(__('Be aware that mail servers tend to have size limits; typically around %s MB; backups larger than any limits will likely not arrive.', 'updraftplus').' '.__('Use this option to only send database backups when sending to email, and skip other components.', 'updraftplus'), '10-20')).'"> '.__('Only email the database backup', 'updraftplus').'</label></div>';
		
		$out .= '</div>';

		return $out;

	}

	/**
	 * Generate a downloadable backup link
	 *
	 * @param String  $link    the unfiltered backup file name in plain text format
	 * @param String  $entity  the backup entity (db, uploads, plugins, etc..)
	 * @param Integer $index   the index number of the backup file
	 * @param Array   $jobdata the jobdata for the currently running backup
	 * @return String the filtered backup file name with its HTML link text attached
	 */
	public function generate_downloadable_file_link($link, $entity, $index, $jobdata) {

		global $updraftplus;

		$jobdata['service'] = empty($jobdata['service']) ? array() : $updraftplus->get_canonical_service_list($jobdata['service']);

		// I was thinking not to check the the nonce fisrt, but at this point I believe we should only generate a valid link
		$download_link = is_array($jobdata) && !empty($jobdata['backup_time']) && !empty($this->file_nonce) && empty($jobdata['service']);

		if ($download_link) {
			$link = '<a href="'.esc_url(add_query_arg(array(
				'page' => 'updraftplus',
				'type' => $entity,
				'timestamp' => $jobdata['backup_time'],
				'nonce' => $this->file_nonce,
				'findex' => $index,
				'action' => 'updraft_download_backup',
			), UpdraftPlus_Options::admin_page_url())).'">'.$link.'</a>';
		}

		return $link;
	}
}
