<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// Files can easily get too big for this method

if (!class_exists('UpdraftPlus_BackupModule')) updraft_try_include_file('methods/backup-module.php', 'require_once');

class UpdraftPlus_BackupModule_email extends UpdraftPlus_BackupModule {

	public function backup($backup_array) {

		global $updraftplus;

		$updraft_dir = trailingslashit($updraftplus->backups_dir_location());

		$email = $updraftplus->just_one_email(UpdraftPlus_Options::get_updraft_option('updraft_email'), true);
		
		if (!is_array($email)) $email = array_filter(array($email));
		
		foreach ($backup_array as $type => $file) {

			$descrip_type = preg_match('/^(.*)\d+$/', $type, $matches) ? $matches[1] : $type;

			$fullpath = $updraft_dir.$file;

			if (file_exists($fullpath) && filesize($fullpath) > UPDRAFTPLUS_WARN_EMAIL_SIZE) {
				$size_in_mb_of_big_file = round(filesize($fullpath)/1048576, 1);
				$toobig_hash = md5($file);
				$this->log($file.': '.sprintf(__('This backup archive is %s MB in size - the attempt to send this via email is likely to fail (few email servers allow attachments of this size).', 'updraftplus'), $size_in_mb_of_big_file).' '.__('If so, you should switch to using a different remote storage method.', 'updraftplus'), 'warning', 'toobigforemail_'.$toobig_hash);
			}

			$any_attempted = false;
			$any_sent = false;
			$any_skip = false;
			foreach ($email as $ind => $addr) {

				if (apply_filters('updraftplus_email_backup', true, $addr, $ind, $type)) {
					foreach (explode(',', $addr) as $sendmail_addr) {
						if (!preg_match('/^https?:\/\//i', $sendmail_addr)) {
							$send_short = (strlen($sendmail_addr)>5) ? substr($sendmail_addr, 0, 5).'...' : $sendmail_addr;
							$this->log("$file: email to: $send_short");
							$any_attempted = true;
							$headers = array();
		
							$subject = __("WordPress Backup", 'updraftplus').': '.get_bloginfo('name').' (UpdraftPlus '.$updraftplus->version.') '.get_date_from_gmt(gmdate('Y-m-d H:i:s', $updraftplus->backup_time), 'Y-m-d H:i');
							$from_email = apply_filters('updraftplus_email_from_header', $updraftplus->get_email_from_header());
							$from_name = apply_filters('updraftplus_email_from_name_header', $updraftplus->get_email_from_name_header());
							$use_wp_from_name_filter = '' === $from_email;
							// Notice that we don't use the 'wp_mail_from' filter, but only the 'From:' header to set sender name and sender email address, the reason behind it is that some SMTP plugins override the "wp_mail()" function and they do anything they want inside their own "wp_mail()" function, including not to call the php_mailer filter nor the wp_mail_from and wp_mail_from_name filters, but since the function signature remain the same as the WP one, so they may evaluate and do something with the header parameter
							if (!$use_wp_from_name_filter) {
								$headers[] = sprintf('From: %s <%s>', $from_name, $from_email);
							} else {
								add_filter('wp_mail_from_name', array($updraftplus, 'get_email_from_name_header'), 9);
							}
							add_action('wp_mail_failed', array($updraftplus, 'log_email_delivery_failure'));
							$sent = wp_mail(trim($sendmail_addr), $subject, sprintf(__("Backup is of: %s.", 'updraftplus'), site_url().' ('.$descrip_type.')'), $headers, array($fullpath));
							remove_action('wp_mail_failed', array($updraftplus, 'log_email_delivery_failure'));
							if ($use_wp_from_name_filter) remove_filter('wp_mail_from_name', array($this, 'get_email_from_name_header'), 9);
							if ($sent) $any_sent = true;
						}
					}
				} else {
					$log_message = apply_filters('updraftplus_email_backup_skip_log_message', '', $addr, $ind, $descrip_type);
					if (!empty($log_message)) {
						$this->log($log_message);
					}
					$any_skip = true;
				}
			}
			if ($any_sent) {
				if (isset($toobig_hash)) {
					$updraftplus->log_remove_warning('toobigforemail_'.$toobig_hash);
					// Don't leave it still set for the next archive
					unset($toobig_hash);
				}
				$updraftplus->uploaded_file($file);
			} elseif ($any_attempted) {
				$this->log('Mails were not sent successfully');
				$this->log(__('The attempt to send the backup via email failed (probably the backup was too large for this method)', 'updraftplus'), 'error');
			} elseif ($any_skip) {
				$this->log('No email addresses were configured to send email to '.$descrip_type);
			} else {
				$this->log('No email addresses were configured to send to');
			}
		}
		return null;
	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $options - An array of options
	 *
	 * @return Array - the returned array can either be the set of updated settings or a WordPress error array
	 */
	public function options_filter($options) {
		global $updraftplus;
		return $updraftplus->just_one_email($options);
	}
	
	public function config_print() {
		global $updraftplus;
		?>
		<tr class="updraftplusmethod email">
			<th><?php _e('Note:', 'updraftplus');?></th>
			<td><?php

				$used = apply_filters('updraftplus_email_whichaddresses',
					sprintf(__("Your site's admin email address (%s) will be used.", 'updraftplus'), get_bloginfo('admin_email').' - <a href="'.esc_attr(admin_url('options-general.php')).'">'.__("configure it here", 'updraftplus').'</a>').
					' <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('For more options, use Premium', 'updraftplus').'</a>'
				);

				echo $used.' '.sprintf(__('Be aware that mail servers tend to have size limits; typically around %s MB; backups larger than any limits will likely not arrive.', 'updraftplus'), '10-20');
				?>
			</td>
		</tr>
		<?php
	}

	public function delete($files, $data = null, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused parameter is present because the caller uses 3 arguments.
		return true;
	}
}
