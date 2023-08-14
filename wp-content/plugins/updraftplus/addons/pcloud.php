<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: pcloud:pCloud Support
Description: pCloud Support
Version: 1.0
Shop: /shop/pcloud/
Include: includes/pcloud
IncludePHP: methods/backup-module.php
Latest Change: 1.22.23
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_BackupModule')) updraft_try_include_file('methods/backup-module.php', 'require_once');

/**
 * pCloud Backup module class
 */
class UpdraftPlus_Addons_RemoteStorage_pcloud extends UpdraftPlus_BackupModule {

	private $client_id = '';

	private $callback_url = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->client_id = defined('UPDRAFTPLUS_PCLOUD_CLIENT_ID') ? UPDRAFTPLUS_PCLOUD_CLIENT_ID : 'zrkDNwnlAGj';
		$this->callback_url = defined('UPDRAFTPLUS_PCLOUD_CALLBACK_URL') ? UPDRAFTPLUS_PCLOUD_CALLBACK_URL : 'https://auth.updraftplus.com/auth/pcloud';
	}

	/**
	 * Supported features.
	 *
	 * @return Array
	 */
	public function get_supported_features() {
		// These options format is handled via only accessing options via $this->get_options().
		return array(
			'multi_options',
			'config_templates',
			'multi_storage',
			'conditional_logic',
			'manual_authentication',
		);
	}

	/**
	 * Default options
	 *
	 * @return Array
	 */
	public function get_default_options() {
		return array(
			'pclauth'     => '',
			'pcllocation' => '1',
			'folderid'    => 0,
			'uploadid'    => 0,
			'folder'      => ''
		);
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options.
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['pclauth'])) {
			return true;
		}
		return false;
	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param Array $pcloud - An array of pCloud options.
	 *
	 * @return Array - the returned array can either be the set of updated pCloud settings or a WordPress error array
	 */
	public function options_filter($pcloud) {
	
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('pcloud');
		
		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: pCloud: $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $pcloud;
		}
		
		if (!is_array($pcloud)) return $opts;

		// Remove instances that no longer exist
		foreach ($opts['settings'] as $instance_id => $storage_options) {
			if (!isset($pcloud['settings'][$instance_id])) unset($opts['settings'][$instance_id]);
		}
		
		if (empty($pcloud['settings'])) return $opts;

		foreach ($pcloud['settings'] as $instance_id => $storage_options) {
			// Now loop over the new options, and replace old options with them
			foreach ($storage_options as $key => $value) {
				if (null === $value) {
					unset($opts['settings'][$instance_id][$key]);
				} else {
					if (!isset($opts['settings'][$instance_id])) $opts['settings'][$instance_id] = array();
					$opts['settings'][$instance_id][$key] = $value;
				}
			}
		}
		return $opts;
	}

	/**
	 * Proceed with the backup
	 *
	 * @param Array $backup_array - Array of files to be backed up.
	 *
	 * @return false|void|null
	 */
	public function backup($backup_array) {

		global $updraftplus;

		try {
			$pcloud = $this->bootstrap();
			$info = $pcloud->account_info();

			if (is_wp_error($info)) {
				$this->log('pCloud ('.$info->get_error_code().'): '.$info->get_error_message(), 'error');
				$space_available = 0;
			} else {
				$space_available = $info['quota'] - $info['usedquota'];
			}
		} catch (Exception $e) {
			$this->log('Exception ('.get_class($e).') when trying to backup: ' . $e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log(sprintf(__('error: %s (see log file for more)', 'updraftplus'), $e->getMessage()), 'error');

			return false;
		}

		$updraft_dir = $updraftplus->backups_dir_location();
		$opts = $this->get_options();

		foreach ($backup_array as $file) {

			$hash         = md5($file);
			$file_success = false;

			if (!file_exists($updraft_dir . '/' . $file)) {
				$file_success = true;
			}

			$filesize      = filesize($updraft_dir . '/' . $file);
			$microtime     = microtime(true);
			$pcl_upload_id = $this->jobdata_get('upload_pclid_' . $hash, 'None');

			if ('None' === $pcl_upload_id) {

				$upload = $pcloud->create_upload();
				
				if (is_wp_error($upload)) {
					$this->log('pCloud ('.$upload->get_error_code().'): '.$upload->get_error_message(), 'error');
					return false;
				}
				
				$pcl_upload_id = $upload['uploadid'];

				$this->jobdata_set('upload_pclid_' . $hash, $pcl_upload_id);
			} else {
				$pcl_upload_id = intval($pcl_upload_id);
			}

			if ('None' !== $this->jobdata_get('upload_id_' . $hash, 'None')) {
				// Resume.
				$offset = $this->jobdata_get('upload_offset_' . $hash, 0);
				if ($offset) {
					$this->log("This is a resumption: $offset bytes had already been uploaded");
				}

				$offset = intval($offset);
			} else {
				$offset = 0;
			}

			// We don't actually abort now - there's no harm in letting it try and then fail.
			if ($space_available < ($filesize - $offset)) {
				$this->log('File upload expected to fail: file data remaining to upload ($file) size is ' . (($filesize - $offset) / 1024) . ' Kb (overall file size; .' . $filesize . " b), whereas available quota is only $space_available b");
			}

			$ufile = $file;

			$this->log("Attempt to upload: $file to: $ufile");

			$upload_tick = microtime(true);

			$retries = 0;

			if (false === $file_success) {

				while (true) {

					$prev_offset = $offset;

					try {
						$new_offset = $pcloud->chunked_upload($updraft_dir . '/' . $file, $pcl_upload_id, $offset);
						
						if (is_wp_error($new_offset)) {
							throw new Exception($new_offset->get_error_message());
						}
						
						$offset = $new_offset;

						if ($prev_offset === $offset) { // Failed, will retry.
							$retries++;
						}
						if (-2 === $offset) { // Success.
							$file_success = true;
							break;
						}

						$this->jobdata_set('upload_offset_' . $hash, $offset);

					} catch (Exception $e) {

						$this->log('chunked upload exception (' . get_class($e) . '): ' . $e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
						
						if ($upload_tick > 0 && time() - $upload_tick > 800) {

							UpdraftPlus_Job_Scheduler::reschedule(60);
							
							$this->log('Select/poll returned after a long time: scheduling a resumption and terminating for now');
							UpdraftPlus_Job_Scheduler::record_still_alive();

							$result = $pcloud->save($pcl_upload_id, $updraft_dir . '/' . $file, $opts['folderid']);
							if (is_wp_error($result)) $this->log('pCloud ('.$result->get_error_code().'): '.$result->get_error_message(), 'error');

							die;
						}

						$retries++;
					}

					if (5 < $retries) {
						$this->log('chunked upload failed: too many failures.');
						$this->log(__('Chunked upload failed', 'updraftplus'), 'error');
						break;
					}
				}
			}

			if ($file_success) {

				$updraftplus->uploaded_file($file);
				$microtime_elapsed = microtime(true) - $microtime;
				$speedps           = ($microtime_elapsed > 0) ? $filesize / $microtime_elapsed : 0;
				$speed             = sprintf('%.2d', ($filesize / 1024)) . ' KB in ' . sprintf('%.2d', $microtime_elapsed) . 's (' . sprintf('%.2d', $speedps) . ' KB/s)';

				$this->log('File upload success (' . $file . "): $speed");
				$this->jobdata_delete('upload_id_' . $hash);
				$this->jobdata_delete('upload_pclid_' . $hash);
				$this->jobdata_delete('upload_offset_' . $hash);

				$result = $pcloud->save($pcl_upload_id, $updraft_dir . '/' . $file, $opts['folderid']);
				if (is_wp_error($result)) $this->log('pCloud ('.$result->get_error_code().'): '.$result->get_error_message(), 'error');
			}
		}

		return null;
	}

	/**
	 * This method gets a list of files from the remote storage that match the string passed in and returns an array of backups
	 *
	 * @param String $match a substring to require (tested via strpos() !== false).
	 *
	 * @return Array|WP_Error
	 */
	public function listfiles($match = 'backup_') {

		try {
			$opts = $this->get_options();
			if (!$this->options_exist($opts)) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->description));
			$pcloud = $this->bootstrap();
		} catch (Exception $e) {
			$this->log($e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log(__('Listing the files failed:', 'updraftplus').' '.$e->getMessage(), 'warning');
			return new WP_Error('listfiles', $e->getMessage());
		}

		$results = array();

		$backups = $pcloud->list_backups();
		if (is_wp_error($backups)) {
			$this->log('pCloud ('.$backups->get_error_code().'): '.$backups->get_error_message(), 'error');
			return $backups;
		}
		foreach ($backups as $backup) {
			$regex = str_replace('/', '\/', $match);
			if (empty($match) || preg_match('/' . $regex . '/', $backup['path'])) {
				$results[] = array(
					'name' => $backup['name'],
					'size' => $backup['size'],
				);
			}
		}

		return $results;
	}

	/**
	 * Delete files from the service using the pCloud API
	 *
	 * @param Array $files - array of filenames to delete.
	 *
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete($files) {

		if (is_string($files)) {
			$files = array($files);
		}

		try {
			$pcloud = $this->bootstrap();
		} catch (Exception $e) {

			$this->log($e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log(sprintf(__('Failed to access %s when deleting (see log file for more)', 'updraftplus'), 'pCloud'), 'warning');

			return 'service_unavailable';
		}

		$any_failures = false;

		foreach ($files as $file) {

			$fullpath = '/' . $pcloud->get_backup_dir() . '/' . $file;

			$this->log("request deletion: $file");

			$result = $pcloud->delete($fullpath);
			if (is_wp_error($result)) {
				$this->log('pCloud ('.$result->get_error_code().'): '.$result->get_error_message(), 'error');
			} else {
				$file_success = 1;
			}

			if (!isset($file_success)) $any_failures = true;
		}

		return $any_failures ? 'file_delete_error' : true;

	}

	/**
	 * Download method
	 *
	 * @param string $file File to be downloaded.
	 *
	 * @return false
	 */
	public function download($file) {

		global $updraftplus;

		$opts    = $this->get_options();
		$pclauth = !empty($opts['pclauth']) ? $opts['pclauth'] : '';

		if (20 > strlen($pclauth)) {

			$this->log('You are not authenticated with pCloud');
			$this->log(__('You are not authenticated with pCloud', 'updraftplus'), 'error');

			return false;
		}

		try {
			$pcloud = $this->bootstrap();
		} catch (Exception $e) {

			$this->log($e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log($e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')', 'error');

			return false;
		}
		if (!$pcloud) {
			return false;
		}

		$remote_files = $this->listfiles($file);

		foreach ($remote_files as $file_info) {
			if (basename($file_info['name']) === basename($file)) {

				$fname = basename($file_info['name']);

				return $updraftplus->chunked_download($fname, $this, $file_info['size'], apply_filters('updraftplus_pcloud_downloads_manually_break_up', false), null, 2 * 1048576);
			}
		}

		$this->log("$file: file not found in listing of remote directory");

		return false;
	}

	/**
	 * Callback used by chunked downloading API
	 *
	 * @param string   $file    - the file (basename) to be downloaded.
	 * @param array    $headers - supplied headers.
	 * @param mixed    $data    - pass-back from our call to the API (which we don't use).
	 * @param resource $fh      - the local file handle.
	 *
	 * @return bool - the data downloaded
	 */
	public function chunked_download($file, $headers, $data, $fh) {

		try {
			$pcloud = $this->bootstrap();
			$needed_file = $pcloud->get_file_info(basename($file));

			if (is_wp_error($needed_file)) {
				$this->log('pCloud ('.$needed_file->get_error_code().'): '.$needed_file->get_error_message(), 'error');
				return false;
			}
		} catch (Exception $e) {

			$this->log($e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log(sprintf(__('Failed to access %s when deleting (see log file for more)', 'updraftplus'), 'pCloud'), 'warning');

			return false;
		}

		if (count($needed_file) < 2 || !isset($needed_file['fileid'])) {
			$this->log('The requested file is no longer in the pCloud backup folder.');
			return false;
		}

		$offset  = 0;
		$retries = 0;

		while (true) {

			try {
				$offset = $pcloud->download($needed_file['fileid'], $fh, $headers, $offset);

				if (is_wp_error($offset)) throw new Exception($offset->get_error_message());

				if ($offset >= ($needed_file['size'] - 1)) {
					fclose($fh);
					$get = true;
					break;
				}
			} catch (Exception $e) {

				$this->log($e);
				$this->log($e->getMessage(), 'error');
				$get = false;

				$retries++;
				if (40 < $retries) {
					break;
				}

				// Sometimes the server can not deliver the file content for some many reasons, we can wait a little and try again.
				sleep(2);
			}
		}
		return $get;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		?>
			<tr class="{{get_template_css_classes false}} {{method_id}}_pre_config_container">
				<td colspan="2">
					<img alt="{{storage_image_title}}" src="{{storage_image_url}}" width="250px">
					<br>
					<p>
						{{{storage_long_description}}}
					</p>
				</td>
			</tr>
		<?php
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		ob_start();
		?>
			<tr class="{{get_template_css_classes true}}">
				<th><?php _e('Store at', 'updraftplus');?>:</th>
				<td>
					{{folder_path}}<input type="text" style="width: 292px" id="{{get_template_input_attribute_value "id" "folder"}}" name="{{get_template_input_attribute_value "name" "folder"}}" value="{{folder}}">
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{authentication_label}}:</th>
				<td>
					{{#if is_authenticated}}
					<p>
						<strong>{{already_authenticated_label}}</strong>
						<a class="updraft_deauthlink" href="{{admin_page_url}}?action=updraftmethod-{{method_id}}-auth&page=updraftplus&updraftplus_{{method_id}}auth=deauth&nonce={{deauthentication_nonce}}&updraftplus_instance={{instance_id}}" data-instance_id="{{instance_id}}" data-remote_method="{{method_id}}">{{deauthentication_link_text}}</a>
					</p>
					{{/if}}
					{{#if ownername_sentence}}
						<br>
						{{ownername_sentence}}
					{{/if}}
					<p><a class="updraft_authlink" href="{{admin_page_url}}?&action=updraftmethod-{{method_id}}-auth&page=updraftplus&updraftplus_{{method_id}}auth=doit&nonce={{storage_auth_nonce}}&updraftplus_instance={{instance_id}}" data-instance_id="{{instance_id}}" data-remote_method="{{method_id}}">{{{authentication_link_text}}}</a></p>
				</td>
				<input type="hidden" id="{{get_template_input_attribute_value "id" "pcllocation"}}" name="{{get_template_input_attribute_value "name" "pcllocation"}}" value="{{pcllocation}}">
			</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Retrieve a list of template properties by taking all the persistent variables and methods of the parent class and combining them with the ones that are unique to this module, also the necessary HTML element attributes and texts which are also unique only to this backup module
	 * NOTE: Please sanitise all strings that are required to be shown as HTML content on the frontend side (i.e. wp_kses()), or any other technique to prevent XSS attacks that could come via WP hooks
	 *
	 * @return Array an associative array keyed by names that describe themselves as they are
	 */
	public function get_template_properties() {
		global $updraftplus;
		$properties = array(
			'storage_image_url' => UPDRAFTPLUS_URL.'/images/pcloud-logo.png',
			'storage_image_title' => __(sprintf(__('%s logo', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()])),
			'storage_long_description' => wp_kses(sprintf(__('Please read %s for use of our %s authorization app (none of your backup data is sent to us).', 'updraftplus'), '<a target="_blank" href="https://updraftplus.com/faqs/what-is-your-privacy-policy-for-the-use-of-your-pcloud-app/">'.__('this privacy policy', 'updraftplus').'</a>', $updraftplus->backup_methods[$this->get_id()]), $this->allowed_html_for_content_sanitisation()),
			'authentication_label' => sprintf(__('Authenticate with %s', 'updraftplus'),  $updraftplus->backup_methods[$this->get_id()]),
			'already_authenticated_label' => __('(You are already authenticated).', 'updraftplus'),
			'deauthentication_link_text' => sprintf(__("Follow this link to remove these settings for %s.", 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
			'authentication_link_text' => wp_kses(sprintf(__("<strong>After</strong> you have saved your settings (by clicking 'Save Changes' below), then come back here and follow this link to complete authentication with %s.", 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]), $this->allowed_html_for_content_sanitisation()),
			'deauthentication_nonce' => wp_create_nonce($this->get_id().'_deauth_nonce'),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}

	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		if (!empty($opts['pclauth'])) {
			$opts['ownername'] = empty($opts['ownername']) ? '' : $opts['ownername'];
			if ($opts['ownername']) {
				$opts['ownername_sentence'] = sprintf(__("Account holder's name: %s.", 'updraftplus'), $opts['ownername']).' ';
			}
			$opts['is_authenticated'] = true;
		}
		$opts['folder_path'] = apply_filters('updraftplus_pcloud_backup_dir', 'UpdraftPlus').'/';
		$opts = apply_filters("updraftplus_options_pcloud_options", $opts);
		return $opts;
	}

	/**
	 * Gives settings keys which values should not passed to handlebarsjs context.
	 * The settings stored in UD in the database sometimes also include internal information that it would be best not to send to the front-end (so that it can't be stolen by a man-in-the-middle attacker)
	 *
	 * @return Array - Settings array keys which should be filtered
	 */
	public function filter_frontend_settings_keys() {
		return array(
			'ownername',
			'pclauth',
		);
	}

	/**
	 * Over-rides the parent to allow this method to output extra information about using the correct account for OAuth authentication
	 *
	 * @return false
	 */
	public function output_account_warning() {
		return false;
	}

	/**
	 * Handles various URL actions, as indicated by the updraftplus_pcloudauth URL parameter
	 *
	 * @return null
	 */
	public function action_auth() {
		if (isset($_GET['updraftplus_pcloudauth'])) {
			if ('doit' == stripslashes($_GET['updraftplus_pcloudauth'])) {
				$this->action_authenticate_storage();
				return;
			} elseif ('deauth' == stripslashes($_GET['updraftplus_pcloudauth'])) {
				$this->action_deauthenticate_storage();
				return;
			}
		} elseif (isset($_REQUEST['state'])) {

			$parts = explode(':', stripslashes($_GET['state']));
			$state = $parts[0];

			if ('success' == $state) {
				$raw_state = stripslashes($_GET['state']);
				if (isset($_GET['code'])) $raw_code = urldecode(stripslashes($_GET['code']));

				$this->do_complete_authentication($raw_state, $raw_code);
			}
		}
	}

	/**
	 * Acquire single-use authorization code from pCloud via OAuth 2.0
	 *
	 * @param  String $instance_id - the instance id of the settings we want to authenticate
	 */
	public function do_authenticate_storage($instance_id) {
		$opts = $this->get_options();

		// Set a flag so we know this authentication is in progress
		$opts['auth_in_progress'] = true;
		$this->set_options($opts, true);

		$prefixed_instance_id = ':' . $instance_id;
		$token = 'token'.$prefixed_instance_id.UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-pcloud-auth';

		$params = array(
			'response_type' => 'code',
			'client_id' => $this->client_id,
			'redirect_uri' => $this->callback_url,
			'state' => $token,
			'access_type' => 'offline',
			'force_reapprove' => 'true',
			'returnqueryparams' => 1
		);

		if (headers_sent()) {
			$this->log(sprintf(__('The %s authentication could not go ahead, because something else on your site is breaking it.', 'updraftplus'), 'pCloud').' '.__('Try disabling your other plugins and switching to a default theme.', 'updraftplus').' ('.__('Specifically, you are looking for the component that sends output (most likely PHP warnings/errors) before the page begins.', 'updraftplus').' '.__('Turning off any debugging settings may also help).', 'updraftplus').')', 'error');
		} else {
			header('Location: https://my.pcloud.com/oauth2/authorize?'.http_build_query($params, '', '&'));
		}
	}

	/**
	 * This function will complete the oAuth flow, if return_instead_of_echo is true then add the action to display the authed admin notice, otherwise echo this notice to page.
	 *
	 * @param string  $state                  - the state
	 * @param string  $code                   - the oauth code
	 * @param boolean $return_instead_of_echo - a boolean to indicate if we should return the result or echo it
	 *
	 * @return void|string - returns the authentication message if return_instead_of_echo is true
	 */
	public function do_complete_authentication($state, $code, $return_instead_of_echo = false) {

		$code = json_decode(base64_decode($code), true);

		if (!is_bool($code) && isset($code['access_token']) && 30 < strlen($code['access_token']) && isset($code['locationid'])) {
			$opts = $this->get_options();
			$opts['pclauth']     = $code['access_token'];
			$opts['pcllocation'] = $code['locationid'];
			// remove our flag so we know this authentication is complete
			if (isset($opts['auth_in_progress'])) unset($opts['auth_in_progress']);
			$this->set_options($opts, true);
		}

		if ($return_instead_of_echo) {
			return $this->show_authed_admin_success($return_instead_of_echo);
		} else {
			add_action('all_admin_notices', array($this, 'show_authed_admin_success'));
		}
	}

	/**
	 * This method will setup the authenticated admin notice, it can either return this or echo it
	 *
	 * @param boolean $return_instead_of_echo - a boolean to indicate if we should return the result or echo it
	 *
	 * @return void|string - returns the authentication message if return_instead_of_echo is true
	 */
	public function show_authed_admin_success($return_instead_of_echo) {
		global $updraftplus_admin;

		try {
			$pcloud = $this->bootstrap();
			$info = $pcloud->account_info();
			if (is_wp_error($info)) {
				$this->log('pCloud ('.$info->get_error_code().'): '.$info->get_error_message(), 'error');
			}
		} catch (Exception $e) {
			$accountinfo_err = sprintf(__("%s error: %s", 'updraftplus'), 'pCloud', $e->getMessage()).' ('.$e->getCode().')';
			$this->log('pCloud error: ' . $e->getMessage() . ' (line: ' . $e->getLine() . ', file: ' . $e->getFile() . ')');
			$this->log(sprintf(__('error: %s (see log file for more)', 'updraftplus'), $e->getMessage()), 'error');
		}

		$message = "<strong>".__('Success:', 'updraftplus').'</strong> '.sprintf(__('you have authenticated your %s account', 'updraftplus'), 'pCloud');

		if (isset($info['quota']) || isset($info['usedquota']) || isset($info['email'])) {
			$available_quota = $info['quota'] - $info['usedquota'];
			$used_perc = round($info['usedquota']*100/$info['quota'], 1);

			$opts = $this->get_options();
			$opts['ownername'] = $info['email'];
			$this->set_options($opts, true);

			$message .= ". <br>".sprintf(__('Your %s account name: %s', 'updraftplus'), 'pCloud', htmlspecialchars($info['email']));

			$message .= ' <br>'.sprintf(__('Your %s quota usage: %s %% used, %s available', 'updraftplus'), 'pCloud', $used_perc, round($available_quota/1048576, 1).' MB');
		} else {
			$message .= " (".__('though part of the returned information was not as expected - whether this indicates a real problem cannot be determined', 'updraftplus').")";
			if (!empty($accountinfo_err)) $message .= "<br>".htmlspecialchars($accountinfo_err);
		}

		if ($return_instead_of_echo) {
			return "<div class='updraftmessage updated'><p>{$message}</p></div>";
		} else {
			$updraftplus_admin->show_admin_warning($message);
		}

	}

	/**
	 * This basically reproduces the relevant bits of bootstrap.php from the SDK
	 *
	 * @return object
	 * @throws Exception Throws standard exception.
	 */
	public function bootstrap() {

		if (!class_exists('UpdraftPlus_Pcloud_API')) {
			include_once UPDRAFTPLUS_DIR . '/includes/pcloud/UpdraftPlus_Pcloud_API.php';
		}

		// if (false === $opts) $opts = $this->options;
		// $opts = $this->get_options();

		$storage = $this->get_storage();
		if (!empty($storage) && !is_wp_error($storage)) {
			return $storage;
		}

		$opts        = $this->get_options();
		$pclauth     = !empty($opts['pclauth']) ? $opts['pclauth'] : '';
		$pcllocation = !empty($opts['pcllocation']) ? intval($opts['pcllocation']) : '';

		if (empty($pclauth) || 20 > strlen($pclauth)) {
			throw new Exception('You are not logged in.');
		}

		$storage = new UpdraftPlus_Pcloud_API();
		$storage->set_auth($pclauth);
		$storage->set_location($pcllocation);
		$storage->set_folder($opts['folder']);

		if (empty($opts['folderid'])) {

			$folder_id = $storage->get_upload_dir_id();
			if (is_wp_error($folder_id)) $this->log('pCloud ('.$folder_id->get_error_code().'): '.$folder_id->get_error_message(), 'error');
			if (is_numeric($folder_id) && 0 < $folder_id) {
				$opts['folderid'] = $folder_id;
			}

			$this->set_options($opts, true);

		} else {
			$folder_id = intval($opts['folderid']);
		}

		if (empty($folder_id) || 10 > $folder_id) $this->log('pCloud error: Failed to get folder id; backup will be uploaded to the root directory');

		$this->set_storage($storage);

		return $storage;
	}
}
