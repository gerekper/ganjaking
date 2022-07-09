<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: sftp:SFTP, SCP and FTPS Support
Description: Allows UpdraftPlus to backup to SFTP, SSH and encrypted FTP servers
Version: 2.8
Shop: /shop/sftp/
Latest Change: 1.16.58
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

/*
This file contains the classes:

- UpdraftPlus_Addons_RemoteStorage_sftp_helper
- UpdraftPlus_Addons_RemoteStorage_sftp (extends UpdraftPlus_RemoteStorage_Addons_Base_v2)
- UpdraftPlus_ftp_wrapper
*/

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

// Do not instantiate the storage object (as that is instantiated on demand), but only the helper
new UpdraftPlus_Addons_RemoteStorage_sftp_helper;

class UpdraftPlus_Addons_RemoteStorage_sftp_helper {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter('updraft_sftp_ftps_notice', array($this, 'ftps_notice'));
		add_filter('updraftplus_ftp_possible', array($this, 'updraftplus_ftp_possible'));
	}

	/**
	 * Called by the WP filter updraftplus_ftp_possible
	 *
	 * @param Array $funcs_disabled
	 *
	 * @return Array
	 */
	public function updraftplus_ftp_possible($funcs_disabled) {
		if (!is_array($funcs_disabled)) return $funcs_disabled;
		foreach (array('ftp_ssl_connect', 'ftp_login') as $func) {
			if (!function_exists($func)) $funcs_disabled['ftpsslexplicit'][] = $func;
		}
		foreach (array('curl_exec') as $func) {
			if (!function_exists($func)) $funcs_disabled['ftpsslimplicit'][] = $func;
		}
		return $funcs_disabled;
	}

	/**
	 * Called by the WP filter updraft_sftp_ftps_notice
	 *
	 * @return String
	 */
	public function ftps_notice() {
		return __("Encrypted FTP is available, and will be automatically tried first (before falling back to non-encrypted if it is not successful), unless you disable it using the expert options. The 'Test FTP Login' button will tell you what type of connection is in use.", 'updraftplus').' '.__('Some servers advertise encrypted FTP as available, but then time-out (after a long time) when you attempt to use it. If you find this happening, then go into the "Expert Options" (below) and turn off SSL there.', 'updraftplus').' '.__('Explicit encryption is used by default. To force implicit encryption (port 990), add :990 to your FTP server below.', ' updraftplus');
	}
}

class UpdraftPlus_Addons_RemoteStorage_sftp extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	private $last_logged_at = 0;

	/**
	 * Set up the connection, change directory to the configured directory, and return a connection object
	 *
	 * @return WP_Error|Net_SSH2|Net_SCP
	 */
	private function do_connect_and_chdir() {
	
		$options = $this->get_options();
		
		if (!array($options)) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), 'SCP/SFTP'));
		
		if (empty($options['host'])) return new WP_Error('no_settings', sprintf(__('No %s found', 'updraftplus'), __('SCP/SFTP host setting', 'updraftplus')));
		if (empty($options['user'])) return new WP_Error('no_settings', sprintf(__('No %s found', 'updraftplus'), __('SCP/SFTP user setting', 'updraftplus')));
		if (empty($options['pass']) && empty($options['key'])) return new WP_Error('no_settings', sprintf(__('No %s found', 'updraftplus'), __('SCP/SFTP password/key', 'updraftplus')));
		$host = $options['host'];
		$user = $options['user'];
		$pass = empty($options['pass']) ? '' : $options['pass'];
		$key = empty($options['key']) ? '' : $options['key'];
		$port = empty($options['port']) ? 22 : (int) $options['port'];
		$fingerprint = empty($options['fingerprint']) ? '' : $options['fingerprint'];
		$path = empty($options['path']) ? '' : $options['path'];
		$scp = !empty($options['scp']);

		$this->path = $path;

		$sftp = $this->connect($host, $port, $fingerprint, $user, $pass, $key, $scp);
		if (is_wp_error($sftp)) return $sftp;

		// So far, so good
		if ($path) {
			if ($scp) {
				// May fail - e.g. if directory already exists, or if the remote shell is restricted
				@$this->ssh->exec('mkdir '.$this->possibly_escapeshellarg($path));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// N.B. - have not changed directory (since cd may not be an available command)
			} else {
				@$sftp->mkdir($path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// See if the directory now exists
				if (!$sftp->chdir($path)) {
					@$sftp->disconnect();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					return new WP_Error('nochdir', __("Check your file permissions: Could not successfully create and enter directory:", 'updraftplus')." $path");
				}
			}
		}
		if (!empty($fingerprint)) {
			$match_fingerprint = $this->validate_fingerprint($fingerprint);
			if (!$match_fingerprint) {
				return new WP_Error('invalid_fingerprint', __("Fingerprints don't match.", 'updraftplus'));
			}
		}

		return $sftp;

	}

	/**
	 * Pass through to escapeshellarg() if the function is present or if the argument needs escaping. The purpose of this is that some hosts have pointlessly disabled escapeshellarg(); so we avoid the error that comes from calling it if it is not going to do anything non-trivial.
	 *
	 * @param String $arg
	 *
	 * @return String
	 */
	private function possibly_escapeshellarg($arg) {
		
		if (function_exists('escapeshellarg')) return escapeshellarg($arg);
		
		// If there is nothing to escape, then just add the quotes; see: https://www.php.net/manual/en/function.escapeshellarg.php . Note that whether we are running on Windows or not is irrelevant, since the command is being passed to the remote shell
		if (!preg_match('#[\'"\%\!\\\\]#', $arg)) {
			return "'".$arg."'";
		}
		
		// Since the function does not exist, and since escaping was needed, this will now report the necessary error to the user.
		return escapeshellarg($arg);
		
	}
	
	public function upload_files($ret, $backup_array) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;
		$sftp = $this->do_connect_and_chdir();
		if (is_wp_error($sftp)) {
			foreach ($sftp->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		if (empty($this->scp)) {
			$this->log("Successfully logged in");
		} else {
			$this->log("SCP: Successfully logged in");
		}

		$any_failures = false;

		$updraft_dir = $updraftplus->backups_dir_location().'/';

		foreach ($backup_array as $file) {
			$this->log("upload: attempt: $file");
			
			$this->sftp_path = $updraft_dir.'/'.$file;
			$this->sftp_size = max(filesize($updraft_dir.'/'.$file), 1);

			$this->last_logged_at = 0;

			if (empty($this->scp)) {

				// SFTP
				
				$this->sftp_began_at = 0;

				try {
					$remote_stat = $sftp->stat($file);
					$current_remote_size = (is_array($remote_stat) && isset($remote_stat['size']) && $remote_stat['size'] > 0) ? $remote_stat['size'] : 0;
					if ($current_remote_size > 0) {
						$this->sftp_began_at = $current_remote_size;
						$this->log('File exists remotely; upload will resume; remote size is: '.round($current_remote_size/1024, 2).' KB');
					}
				} catch (Exception $e) {
					$this->log('Exception when stating remote file ('.get_class($e).'): '.$e->getMessage());
					$current_remote_size = 0;
				}

				if ($current_remote_size >= $this->sftp_size || $sftp->put($file, $updraft_dir.'/'.$file, NET_SFTP_LOCAL_FILE, $current_remote_size, $current_remote_size, array($this, 'sftp_progress_callback'))) {
					$updraftplus->uploaded_file($file);
				} else {
					$any_failures = true;
					$this->log('ERROR: SFTP: Failed to upload file: '.$file);
					$this->log(__('Error: Failed to upload', 'updraftplus').": $file", 'error');
				}
			} else {
			
				// SCP
			
				$rfile = empty($this->path) ? $file : trailingslashit($this->path).$file;
				if ($sftp->put($rfile, $updraft_dir.'/'.$file, NET_SCP_LOCAL_FILE, array($this, 'sftp_progress_callback'))) {
					$updraftplus->uploaded_file($file);
				} else {
					$any_failures = true;
					$this->log('ERROR: SCP: Failed to upload file: '.$file);
					$this->log(sprintf(__('%s Error: Failed to upload', 'updraftplus'), 'SCP').": $file", 'error');
				}
			}
		}

		// In the array we used to pass (before 1.16.58) 'sftp_object' => $sftp; but this was not multi-instance compatible
		return $any_failures ? null : array();
		
	}

	public function sftp_progress_callback($sent) {
		global $updraftplus;
		$bytes_sent = empty($this->sftp_began_at) ? $sent : $this->sftp_began_at + $sent;
		if ($bytes_sent > $this->last_logged_at + 1048576) {
			$perc = empty($this->sftp_size) ? 0 : round(100*$bytes_sent / $this->sftp_size, 1);
			$updraftplus->record_uploaded_chunk($perc, '', $this->sftp_path);
			$this->last_logged_at = $bytes_sent;
		}
	}

	public function delete_files($ret, $files, $sftp_arr = false) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (is_string($files)) $files = array($files);

		if ($sftp_arr && isset($sftp_arr['sftp_object'])) {
			$sftp = $sftp_arr['sftp_object'];
		} else {
			$sftp = $this->do_connect_and_chdir();
			if (is_wp_error($sftp)) {
				foreach ($sftp->get_error_messages() as $msg) {
					$this->log($msg);
					$this->log($msg, 'error');
				}
				return false;
			}
		}

		$some_success = false;

		foreach ($files as $file) {

			if (empty($this->scp)) {
				$this->log("Delete remote: $file");
			} else {
				$this->log("SCP: Delete remote: $file");
			}

			if (empty($this->scp)) {
				if (!$sftp->delete($file, false)) {
					$this->log("Delete failed: $file");
				} else {
					$some_success = true;
				}
			} else {
				$rfile = empty($this->path) ? $file : trailingslashit($this->path).$file;
				if (!$this->ssh->exec('rm -f '.$this->possibly_escapeshellarg($rfile))) {
					$this->log("SCP: Delete failed: $rfile");
				} else {
					$some_success = true;
				}
			}
		}

		return $some_success;

	}

	public function listfiles($match = 'backup_') {
		$sftp = $this->do_connect_and_chdir();
		if (is_wp_error($sftp)) return $sftp;

		$results = array();

		if ($this->scp) {

			$cdcom = empty($this->path) ? '' : "cd ".trailingslashit($this->path)." && ";
			
			$nosizes = false;

			if (false == ($exec = $this->ssh->exec($cdcom."ls -l ${match}*"))) {
				$nosizes = true;
				$exec = $this->ssh->exec($cdcom."ls -1 ${match}*");
			}
			if (false != $exec) {
				foreach (explode("\n", $exec) as $str) {
					if ($nosizes) {
						if (0 === strpos($str, $match)) $results[] = array('name' => $str);
					} elseif (!$nosizes && preg_match('/^[^dls].*\s(\d+)\s+\S+\s+\d+\s+([:0-9]+)\s+'.$match.'(.*)$/', $str, $matches)) {
						$results[] = array('name' => $match.$matches[3], 'size' => $matches[1]);
					}
				}
			}

		} else {
			$dirlist = $sftp->rawlist();
			if (!is_array($dirlist)) return array();

			foreach ($dirlist as $path => $stat) {
				if (0 === strpos($path, $match)) $results[] = array('name' => $path, 'size' => $stat['size']);
				unset($dirlist[$path]);
			}

		}

		return $results;
	}

	public function download_file($ret, $file) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;

		$sftp = $this->do_connect_and_chdir();
		if (is_wp_error($sftp)) {
			foreach ($sftp->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		$fullpath = $updraftplus->backups_dir_location().'/'.$file;

		$rfile = (empty($this->scp) || empty($this->path)) ? $file : trailingslashit($this->path).$file;
		if (!$sftp->get($rfile, $fullpath)) {
			$this->log("Error: Failed to download: $rfile");
			$this->log(__('Error: Failed to download', 'updraftplus').": $rfile", 'error');
			return false;
		}
		return true;
	}

	/**
	 * Open a connection to the SSH server
	 *
	 * @param String  $host        - SSH server hostname
	 * @param Integer $port        - TCP port to connect to
	 * @param String  $fingerprint - fingerprint to check (not currently implemented)
	 * @param String  $user        - login username
	 * @param String  $pass        - login password
	 * @param String  $key         - RSA private key to use for logging in (an alternative to a password)
	 * @param Boolean $scp         - if set, then SCP will be used; otherwise SFTP
	 * @param Boolean $debug       - debugging mode: will ask phpseclib to log (which, being controlled by constants, may not be possible if they are already set)
	 *
	 * @return WP_Error|Net_SSH2|Net_SCP
	 */
	private function connect($host, $port = 22, $fingerprint = '', $user = '', $pass = '', $key = '', $scp = false, $debug = false) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;
		
		$this->scp = $scp;

		$timeout = (defined('UPDRAFTPLUS_SFTP_TIMEOUT') && is_numeric(UPDRAFTPLUS_SFTP_TIMEOUT)) ? UPDRAFTPLUS_SFTP_TIMEOUT : 15;

		if ($scp) {
			$ensure_phpseclib = $updraftplus->ensure_phpseclib('Net_SSH2');
			$updraftplus->ensure_phpseclib('Net_SCP');
		} else {
			$ensure_phpseclib = $updraftplus->ensure_phpseclib('Net_SFTP');
		}
		
		if (is_wp_error($ensure_phpseclib)) return $ensure_phpseclib;
		
		// N.B. The same NET_SFTP_* constants exist; but as this point, we're only testing login, so will stick with SSH2
		if ($debug) {
			if (!defined('NET_SSH2_LOGGING')) {
				// Alternative: NET_SFTP_LOG_SIMPLE. phpseclib source says that NET_SSH2_LOG_COMPLEX is most useful for SSH2
				define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
			} elseif (NET_SSH2_LOGGING != NET_SSH2_LOG_COMPLEX) {
				$this->log("NET_SSH2_LOGGING: constant was already set; value not as desired (value=".NET_SSH2_LOGGING.", desired=".NET_SSH2_LOG_COMPLEX.")");
			}
		}
		
		$connection_class = $scp ? 'Net_SSH2' : 'Net_SFTP';
		
		$this->ssh = new $connection_class($host, $port, $timeout);

		if (!empty($key)) {
			$updraftplus->ensure_phpseclib('Crypt_RSA');
			$updraftplus->ensure_phpseclib('Math_BigInteger');
			$rsa = new Crypt_RSA();
			if (false === $rsa->loadKey($key)) {
				if (preg_match('/Encryption: (.+)/i', $key, $matches)) {
					$encryption = trim($matches[1]);
					if ('none' !== $encryption) return new WP_Error('no_key_passphrase', __("The key provided is encrypted. You need to provide the unencrypted key (see: https://updraftplus.com/faqs/why-must-i-use-a-non-encrypted-sftp-key/).", 'updraftplus'));
				}
				if (empty($pass)) return new WP_Error('no_load_key', __('The key provided was not in a valid format, or was corrupt.', 'updraftplus'));
			} else {
				$pass = $rsa;
			}
		}

		// See: https://github.com/phpseclib/phpseclib/issues/1271#issuecomment-390417276 . Default is 10s.
		$this->ssh->setTimeout(35);

		// Ensure phpseclib Crypt_Blowfish is loaded, over PEAR's
		$updraftplus->ensure_phpseclib('Crypt_Blowfish');

		if (!$this->ssh->login($user, $pass)) {
			$error_data = null;
			$message = 'SSH 2 login failed';
			if ($debug) {
				$error_data = array("UpdraftPlus debug mode is on: detailed debugging data follows (some data may be base-64 encoded)\n");
				$errors = $this->ssh->getErrors();
				
				if (is_array($errors)) {
					foreach ($errors as $err) {
						// Sending raw data to the browser makes JSON-decoding on the browser unhappy
						$error_data[] = base64_encode($err);
					}
				}
				
				// "Returns a string if NET_SSH2_LOGGING == NET_SSH2_LOG_COMPLEX, an array if NET_SSH2_LOGGING == NET_SSH2_LOG_SIMPLE and false if !defined('NET_SSH2_LOGGING')"
				$ssh_log = $this->ssh->getLog();
				if (is_string($ssh_log)) {
					$error_data[] = $ssh_log;
				} elseif (is_array($ssh_log)) {
					$error_data = array_merge($error_data, $ssh_log);
				}
			}

			return new WP_Error('ssh2_nologin', $message, $error_data);
		}

		// if ($fingerprint) {
		// $fingerprint = str_replace(':', '', $fingerprint);
		// Fingerprint checking not yet supported by phpseclib
		// return new WP_Error('debug', "Remove fingerprint: $remote_finger");
		// }

		return $scp ? new Net_SCP($this->ssh) : $this->ssh;

	}

	/**
	 * This method overrides the parent method and lists the supported features of  this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	public function get_default_options() {
		return array(
			'host' => '',
			'port' => '22',
			'user' => '',
			'pass' => '',
			'key' => '',
			'path' => '',
			'scp' => 0,
		);
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		?>
		<tr class="{{get_template_css_classes false}} sftp_pre_config_container">
			<td colspan="2">
				<h3>{{method_display_name}}</h3>
				<p><em>{{description_label}}</em></p>
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
				<th>{{input_host_label}}:</th>
				<td>
					<input type="text" class="updraft_input--wide udc-wd-600" data-updraft_settings_test="host" id="{{get_template_input_attribute_value "id" "host"}}" name="{{get_template_input_attribute_value "name" "host"}}" value="{{host}}" />
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_port_label}}:</th>
				<td>
					<input type="text" class="updraft_input--wide udc-wd-600" data-updraft_settings_test="port" id="{{get_template_input_attribute_value "id" "port"}}" name="{{get_template_input_attribute_value "name" "port"}}" value="{{port}}" />
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_username_label}}:</th>
				<td>
					<input type="text" autocomplete="off" class="updraft_input--wide udc-wd-600" data-updraft_settings_test="user" id="{{get_template_input_attribute_value "id" "user"}}" name="{{get_template_input_attribute_value "name" "user"}}" value="{{user}}" />
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_password_label}}:</th>
				<td>
					<input title="{{input_password_title}}" data-updraft_settings_test="pass" type="{{input_password_type}}" autocomplete="off" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "pass"}}" name="{{get_template_input_attribute_value "name" "pass"}}" value="{{pass}}" />
					<br><em>{{input_password_title}}</em>
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_key_label}}:</th>
				<td>
					<textarea title="{{input_key_title}}" class="updraft_input--wide udc-wd-600" rows="4" data-updraft_settings_test="key" id="{{get_template_input_attribute_value "id" "key"}}" name="{{get_template_input_attribute_value "name" "key"}}">{{key}}</textarea>
					<br><em>{{input_key_title}}</em>
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_rsa_fingerprint_label}}:</th>
				<td>
					<input title="{{input_rsa_fingerprint_plain_label}}" data-updraft_settings_test="fingerprint" type="text" autocomplete="on" class="updraft_input--wide udc-wd-600" id="{{get_template_input_attribute_value "id" "fingerprint"}}" name="{{get_template_input_attribute_value "name" "fingerprint"}}" value="{{fingerprint}}" />
					<p class="udc-wd-600"><em>{{{input_rsa_fingerprint_html_label}}}</em></p>
				</td>
			</tr>
			
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_directory_path_label}}:</th>
				<td>
					<input title="{{input_directory_path_title}}" type="text" class="updraft_input--wide udc-wd-600" data-updraft_settings_test="path" id="{{get_template_input_attribute_value "id" "path"}}" name="{{get_template_input_attribute_value "name" "path"}}" value="{{path}}" /><br><em>{{input_directory_path_title}}</em>
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>SCP:</th>
				<td>
					<input type="checkbox" data-updraft_settings_test="scp" id="{{get_template_input_attribute_value "id" "scp"}}" name="{{get_template_input_attribute_value "name" "scp"}}" value="1" {{#ifeq '1' scp}} checked="checked"{{/ifeq}}> <label for="{{get_template_input_attribute_value "id" "scp"}}">{{input_scp_label}}</label>
				</td>
			</tr>

			{{{get_template_test_button_html "SFTP/SCP"}}}
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Modifies handlebar template options
	 *
	 * @param Array $opts
	 *
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		$opts['port'] = isset($opts['port']) ? $opts['port'] : 22;
		return $opts;
	}

	/**
	 * Retrieve a list of template properties by taking all the persistent variables and methods of the parent class and combining them with the ones that are unique to this module, also the necessary HTML element attributes and texts which are also unique only to this backup module
	 * NOTE: Please sanitise all strings that are required to be shown as HTML content on the frontend side (i.e. wp_kses()), or any other technique to prevent XSS attacks that could come via WP hooks
	 *
	 * @return Array an associative array keyed by names that describe themselves as they are
	 */
	public function get_template_properties() {
		global $updraftplus;
		$rsa_fingerprint_tooltip = __('MD5 (128-bit) fingerprint, in hex format - should have the same length and general appearance as this (colons optional): 73:51:43:b1:b5:fc:8b:b7:0a:3a:a9:b1:0f:69:73:a8. Using a fingerprint is not essential, but you are not secure against %s if you do not use one', 'updraftplus');
		$properties = array(
			'description_label' => __('Resuming partial uploads is supported for SFTP, but not for SCP. Thus, if using SCP then you will need to ensure that your webserver allows PHP processes to run long enough to upload your largest backup file.', 'updraftplus'),
			'input_host_label' => __('Host', 'updraftplus'),
			'input_port_label' => __('Port', 'updraftplus'),
			'input_username_label' => __('Username', 'updraftplus'),
			'input_password_label' => __('Password', 'updraftplus'),
			'input_key_label' => __('Key', 'updraftplus'),
			'input_rsa_fingerprint_label' => __('RSA fingerprint', 'updraftplus'),
			'input_directory_path_label' => __('Directory path', 'updraftplus'),
			'input_password_title' => __('Your login may be either password or key-based - you only need to enter one, not both.', 'updraftplus'),
			'input_password_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_key_title' => __('PKCS1 (PEM header: BEGIN RSA PRIVATE KEY), XML and PuTTY format keys are accepted.', 'Do not translate BEGIN RSA PRIVATE KEY. PCKS1, XML, PEM and PuTTY are also technical acronyms which should not be translated.', 'updraftplus'),
			'input_rsa_fingerprint_plain_label' => sprintf($rsa_fingerprint_tooltip, __('MITM attacks', 'updraftplus')),
			'input_rsa_fingerprint_html_label' => sprintf($rsa_fingerprint_tooltip, '<a href="http://en.wikipedia.org/wiki/Man-in-the-middle_attack" target="_blank">'.__('MITM attacks', 'updraftplus').'</a>'),
			'input_directory_path_title' => __('Where to change directory to after logging in - often this is relative to your home directory.', 'updraftplus'),
			'input_scp_label' => __('Use SCP instead of SFTP', 'updraftplus'),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()])
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}

	/**
	 * Test the supplied credentials. Output to display to the user should be echoed.
	 *
	 * @param Array $posted_settings - the settings to test (including meta such as debug mode)
	 *
	 * @return Mixed|Void - any data to return with the test results (may be logged for debugging purposes)
	 */
	public function credentials_test($posted_settings) {
	
		if (empty($posted_settings['host'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('host name', 'updraftplus'));
			return;
		}
		if (empty($posted_settings['user'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('username', 'updraftplus'));
			return;
		}
		if (empty($posted_settings['pass']) && empty($posted_settings['key'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('password/key', 'updraftplus'));
			return;
		}
		$port = empty($posted_settings['port']) ? 22 : $posted_settings['port'];
		if (!is_numeric($port)) {
			_e("Failure: Port must be an integer.", 'updraftplus');
			return;
		}
		$path = empty($posted_settings['path']) ? '' : $posted_settings['path'];

		$fingerprint = empty($posted_settings['fingerprint']) ? '' : $posted_settings['fingerprint'];

		$scp = !empty($posted_settings['scp']);

		$host = $posted_settings['host'];
		$user = $posted_settings['user'];
		$pass = empty($posted_settings['pass']) ? '' : $posted_settings['pass'];
		$key = empty($posted_settings['key']) ? '' : $posted_settings['key'];
		$debug_mode = empty($posted_settings['debug_mode']) ? false : true;

		$sftp = $this->connect($host, $port, $fingerprint, $user, $pass, $key, $scp, $debug_mode);

		if (is_wp_error($sftp)) {
			echo __("Failed", 'updraftplus').": ";
			foreach ($sftp->get_error_messages() as $key => $msg) {
				echo "$msg\n";
			}
			$error_data = $sftp->get_error_data();
			return is_array($error_data) ? $error_data : null;
		}
		
		// So far, so good
		if (empty($scp)) {
			if ($path) {
				@$sftp->mkdir($path);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				// See if the directory now exists
				if (!$sftp->chdir($path)) {
					echo __('Check your file permissions: Could not successfully create and enter:', 'updraftplus')." (".htmlspecialchars($path).")";
					@$sftp->disconnect();// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					return;
				}
			}
		} elseif ($path) {
			$this->ssh->exec('mkdir '.$this->possibly_escapeshellarg($path));
		}

		$testfile = md5(time().rand());
		if (!empty($scp) && !empty($path)) $testfile = trailingslashit($path).$testfile;
		// Now test uploading a file
		$putfile = $sftp->put($testfile, 'test');
		if (empty($scp)) {
			$sftp->delete($testfile);
		} else {
			$this->ssh->exec('rm -f '.$this->possibly_escapeshellarg($testfile));
		}
		
		$ret_arr = array();
		if ($putfile) {
			$valid_fingerprints = $this->get_fingerprints($this->ssh);
			if (empty($fingerprint)) {
				$ret_arr['valid_md5_fingerprint'] = $valid_fingerprints['md5'];
				_e('Success', 'updraftplus');
			} else {
				$match_fingerprint = $this->validate_fingerprint($fingerprint, $valid_fingerprints);
				if ($match_fingerprint) {
					_e('Success', 'updraftplus');
				} else {
					echo __("Failed: We are unable to match the fingerprint. However, we were able to log in and move to the indicated directory and successfully create a file in that location.", 'updraftplus');
				}
			}

			printf(' '.__("The server's RSA key %s fingerprint: %s.", 'updraftplus').' ', 'MD5', $valid_fingerprints['md5']);
			printf(__("The server's RSA key %s fingerprint: %s.", 'updraftplus'), 'SHA256', $valid_fingerprints['sha256']);
		} else {
			if (empty($scp)) {
				echo __("Failed: We were able to log in and move to the indicated directory, but failed to successfully create a file in that location.", 'updraftplus');
			} else {
				_e("Failed: We were able to log in, but failed to successfully create a file in that location.", 'updraftplus');
			}
		}

		if ($this->scp) {
			$this->ssh->disconnect();
		} else {
			$sftp->disconnect();
		}
		return $ret_arr;
	}
	
	/**
	 * Get both md5 and sha256 fingerprints
	 *
	 * @param Object|String $ssh Net_SSH2 or it's subclass instace. If this is empty, $this->ssh will be $ssh
	 *
	 * @return Array An associative array has md5 and sha256 fingerprint
	 */
	private function get_fingerprints($ssh = '') {
		global $updraftplus;
		
		if (empty($ssh)) $ssh = $this->ssh;

		$host_key = $ssh->getServerPublicHostKey();
		$updraftplus->ensure_phpseclib('Crypt_RSA');
		
		$host_rsa = new Crypt_RSA();
		$host_rsa->loadKey($host_key);
		return array(
			'md5' => $host_rsa->getPublicKeyFingerprint('md5'),
			'sha256' => $host_rsa->getPublicKeyFingerprint('sha256'),
		);
	}
	
	/**
	 * Get both md5 and sha256 fingerprints
	 *
	 * @param String $fingerprint        A fingerprint which need to be validated
	 * @param Array  $valid_fingerprints Host's valid fingerprints
	 *
	 * @return Boolean Whether the given fingerprint is matched or not
	 */
	private function validate_fingerprint($fingerprint, $valid_fingerprints = array()) {
		if (empty($valid_fingerprints)) $valid_fingerprints = $this->get_fingerprints($this->ssh);

		foreach ($valid_fingerprints as $valid_fingerprint) {
			if ($fingerprint == $valid_fingerprint)	{
				return true;
			}
		}
		return false;
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['host']) && isset($opts['user']) && '' != $opts['user']) return true;
		return false;
	}
}
	
/**
 * Adapted from http://www.solutionbot.com/2009/01/02/php-ftp-class/
 *
 * Our main tweaks to this class are to enable SSL with fallback for explicit encryption, and to provide rudimentary implicit support (the support for implicit is via Curl (since PHP's functions do not support it), and only extends to methods that we know we use).
 *
 * We somewhat crudely detect the request for implicit via use of port 990. But in the real world, it's unlikely we'll come across anything else - if we do, we can abstract a little more.
 */
class UpdraftPlus_ftp_wrapper {

	private $conn_id;

	private $host;

	private $username;

	private $password;

	private $port;

	public $timeout = 60;

	public $passive = true;

	// Whether to *allow* (not necessarily require) SSL
	public $ssl = true;

	public $system_type = '';

	public $login_type = 'non-encrypted';

	public $use_server_certs = false;

	public $disable_verify = true;

	public $curl_handle;
 
	public function __construct($host, $username, $password, $port = 21) {
		$this->host     = $host;
		$this->username = $username;
		$this->password = $password;
		$this->port     = $port;
	}
 
	public function connect() {

		// Implicit SSL - not handled via PHP's native ftp_ functions, so we use curl instead
		if (990 == $this->port || (defined('UPDRAFTPLUS_FTP_USECURL') && UPDRAFTPLUS_FTP_USECURL)) {
			if (false == $this->ssl) {
				$this->port = 21;
			} else {
				$this->curl_handle = curl_init();
				if (!$this->curl_handle) {
					$this->port = 21;
				} else {
					$options = array(
						CURLOPT_USERPWD        => $this->username . ':' . $this->password,
						CURLOPT_PORT           => $this->port,
						CURLOPT_CONNECTTIMEOUT => 20,
						// CURLOPT_TIMEOUT timeout is not just a "no-activity" timeout, but a total time limit on any Curl operation - undesirable
						// CURLOPT_TIMEOUT        => 20,
						CURLOPT_FTP_CREATE_MISSING_DIRS => true
					);
					$options[CURLOPT_FTP_SSL] = CURLFTPSSL_TRY; // CURLFTPSSL_ALL, // require SSL For both control and data connections
					if (990 == $this->port) {
						$options[CURLOPT_FTPSSLAUTH] = CURLFTPAUTH_SSL; // CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
					} else {
						$options[CURLOPT_FTPSSLAUTH] = CURLFTPAUTH_DEFAULT; // let cURL choose the FTP authentication method (either SSL or TLS)
					}
					// Prints to STDERR by default - noisy
					if (defined('WP_DEBUG') && WP_DEBUG && UpdraftPlus_Options::get_updraft_option('updraft_debug_mode')) {
						$options[CURLOPT_VERBOSE] = true;
					}
					
					// Provided for people who explicitly set the option to support their broken server
					if ($this->disable_verify) {
						$options[CURLOPT_SSL_VERIFYPEER] = false;
						$options[CURLOPT_SSL_VERIFYHOST] = 0;
					} else {
						$options[CURLOPT_SSL_VERIFYPEER] = true;
					}
					
					if (!$this->use_server_certs) {
						$options[CURLOPT_CAINFO] = UPDRAFTPLUS_DIR.'/includes/cacert.pem';
					}
					
					if (true != $this->passive) $options[CURLOPT_FTPPORT] = '-';
					foreach ($options as $option_name => $option_value) {
						if (!curl_setopt($this->curl_handle, $option_name, $option_value)) {
							global $updraftplus;
							if (is_a($updraftplus, 'UpdraftPlus')) {
								$updraftplus->log("Curl exception: will revert to normal FTP");
							}
							$this->port = 21;
							$this->curl_handle = false;
						}
					}
				}
				// All done - leave
				if ($this->curl_handle) {
					$this->login_type = 'encrypted (implicit, port 990)';
					return true;
				}
			}
		}

		$time_start = time();
		if (function_exists('ftp_ssl_connect') && false !== $this->ssl) {
			$this->conn_id = ftp_ssl_connect($this->host, $this->port, 15);
			$attempting_ssl = true;
		}

		if ($this->conn_id) {
			$this->login_type = 'encrypted';
			$this->ssl = true;
		} else {
			$this->conn_id = ftp_connect($this->host, $this->port, 15);
		}

		if ($this->conn_id) $result = ftp_login($this->conn_id, $this->username, $this->password);

		if (!empty($result)) {
			ftp_set_option($this->conn_id, FTP_TIMEOUT_SEC, $this->timeout);
			ftp_pasv($this->conn_id, $this->passive);
			$this->system_type = ftp_systype($this->conn_id);
			return true;
		} elseif (!empty($attempting_ssl)) {
			global $updraftplus_admin;
			if (isset($updraftplus_admin->logged) && is_array($updraftplus_admin->logged)) {
				// Clear the previous PHP messages, so that we only show the user messages from the method that worked (or from both if both fail)
				$save_array = $updraftplus_admin->logged;
				$updraftplus_admin->logged = array();
				// trigger_error(__('Encrypted login failed; trying non-encrypted', 'updraftplus'), E_USER_NOTICE);
			}
			$this->ssl = false;
			$this->login_type = 'non-encrypted';
			$time_start = time();
			$this->conn_id = ftp_connect($this->host, $this->port, 15);
			if ($this->conn_id) $result = ftp_login($this->conn_id, $this->username, $this->password);
			if (!empty($result)) {
				ftp_set_option($this->conn_id, FTP_TIMEOUT_SEC, $this->timeout);
				ftp_pasv($this->conn_id, $this->passive);
				$this->system_type = ftp_systype($this->conn_id);
				return true;
			} else {
				// Add back the previous PHP messages
				if (isset($save_array)) $updraftplus_admin->logged = array_merge($save_array, $updraftplus_admin->logged);
			}
		}

		// If we got here, then we failed
		if (time() - $time_start > 14) {
			global $updraftplus_admin;
			if (isset($updraftplus_admin->logged) && is_array($updraftplus_admin->logged)) {
				$updraftplus_admin->logged[] = sprintf(__('The %s connection timed out; if you entered the server correctly, then this is usually caused by a firewall blocking the connection - you should check with your web hosting company.', 'updraftplus'), 'FTP');
			} else {
				global $updraftplus;
				$updraftplus->log(sprintf(__('The %s connection timed out; if you entered the server correctly, then this is usually caused by a firewall blocking the connection - you should check with your web hosting company.', 'updraftplus'), 'FTP'), 'error');
			}
		}

		return false;

	}
 
	public function curl_progress_function($download_size, $downloaded_size, $upload_size, $uploaded_size) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if ($uploaded_size<1) return;

		global $updraftplus;

		$percent = 100*($uploaded_size+$this->upload_from)/$this->upload_size;

		// Log every megabyte or at least every 20%
		if ($percent > $this->upload_last_recorded_percent + 20 || $uploaded_size > $this->uploaded_bytes + 1048576) {
			$updraftplus->record_uploaded_chunk(round($percent, 1), '', $this->upload_local_path);
			$this->upload_last_recorded_percent=floor($percent);
			$this->uploaded_bytes = $uploaded_size;
		}

	}

	public function put($local_file_path, $remote_file_path, $mode = FTP_BINARY, $resume = false, $updraftplus = false) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		$file_size = filesize($local_file_path);

		$existing_size = 0;
		if ($resume) {

			if ($this->curl_handle) {
				if (true === $this->curl_handle) $this->connect();
				curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);
				curl_setopt($this->curl_handle, CURLOPT_NOBODY, true);
				curl_setopt($this->curl_handle, CURLOPT_HEADER, false);

				// curl_setopt($this->curl_handle, CURLOPT_FORBID_REUSE, true);

				$getsize = curl_exec($this->curl_handle);
				if ($getsize) {
					$sizeinfo = curl_getinfo($this->curl_handle);
					$existing_size = $sizeinfo['download_content_length'];
				} else {
					if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("Curl: upload error: ".curl_error($this->curl_handle));
				}
			} else {
				$existing_size = ftp_size($this->conn_id, $remote_file_path);
			}
			// In fact curl can return -1 as the value, for a non-existant file
			if ($existing_size <=0) {
				$resume = false;
				$existing_size = 0;
			} else {
				if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("File already exists at remote site: size $existing_size. Will attempt resumption.");
				if ($existing_size >= $file_size) {
					if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("File is apparently already completely uploaded");
					return true;
				}
			}
		}

		// From here on, $file_size is only used for logging calculations. We want to avoid divsion by zero.
		$file_size = max($file_size, 1);

		if (!$fh = fopen($local_file_path, 'rb')) return false;
		if ($existing_size) fseek($fh, $existing_size);

		// FTPS (i.e. implicit encryption)
		if ($this->curl_handle) {
			// Reset the curl object (because otherwise we get errors that make no sense)
			$this->connect();
			if (version_compare(phpversion(), '5.3.0', '>=')) {
				// @codingStandardsIgnoreLine
				curl_setopt($this->curl_handle, CURLOPT_PROGRESSFUNCTION, array($this, 'curl_progress_function'));
				curl_setopt($this->curl_handle, CURLOPT_NOPROGRESS, false);
			}
			$this->upload_local_path = $local_file_path;
			$this->upload_last_recorded_percent = 0;
			$this->upload_size = max($file_size, 1);
			$this->upload_from = $existing_size;
			$this->uploaded_bytes = $existing_size;
			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);
			if ($existing_size) curl_setopt($this->curl_handle, CURLOPT_FTPAPPEND, true);

			// DOn't set CURLOPT_UPLOAD=true before doing the size check - it results in a bizarre error
			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, true);
			curl_setopt($this->curl_handle, CURLOPT_INFILE, $fh);
			$output = curl_exec($this->curl_handle);
			fclose($fh);
			if (is_a($updraftplus, 'UpdraftPlus') && !$output) {
				$updraftplus->log("FTPS: error: ".curl_error($this->curl_handle));
			} elseif (true === $updraftplus && !$output) {
				echo __('Error:', 'updraftplus').' '.curl_error($this->curl_handle)."\n";
			}
			// Mark as used
			$this->curl_handle = true;
			return $output;
		}

		$ret = ftp_nb_fput($this->conn_id, $remote_file_path, $fh, FTP_BINARY, $existing_size);

		// $existing_size can now be re-purposed

		while (FTP_MOREDATA == $ret) {
			if (is_a($updraftplus, 'UpdraftPlus')) {
				$new_size = ftell($fh);
				if ($new_size - $existing_size > 524288) {
					$existing_size = $new_size;
					$percent = round(100*$new_size/$file_size, 1);
					$updraftplus->record_uploaded_chunk($percent, '', $local_file_path);
				}
			}
			// Continue upload
			$ret = ftp_nb_continue($this->conn_id);
		}

		fclose($fh);

		if (FTP_FINISHED != $ret) {
			if (is_a($updraftplus, 'UpdraftPlus')) $updraftplus->log("FTP upload: error ($ret)");
			return false;
		}

		return true;

	}
 
	public function get($local_file_path, $remote_file_path, $mode = FTP_BINARY, $resume = false, $updraftplus = false) {

		$file_last_size = 0;

		if ($resume) {
			if (!$fh = fopen($local_file_path, 'ab')) return false;
			// @codingStandardsIgnoreLine
			clearstatcache($local_file_path);
			$file_last_size = filesize($local_file_path);
		} else {
			if (!$fh = fopen($local_file_path, 'wb')) return false;
		}

		// Implicit FTP, for which we use curl (since PHP's native FTP functions don't handle implicit FTP)
		if ($this->curl_handle) {
			if ($resume) curl_setopt($this->curl_handle, CURLOPT_RESUME_FROM, $resume);
			curl_setopt($this->curl_handle, CURLOPT_NOBODY, false);
			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);
			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, false);
			curl_setopt($this->curl_handle, CURLOPT_FILE, $fh);
			$output = curl_exec($this->curl_handle);
			if ($output) {
				if ($updraftplus) $updraftplus->log("FTP fetch: fetch complete");
			} else {
				if ($updraftplus) $updraftplus->log("FTP fetch: fetch failed");
			}
			return $output;
		}

		$ret = ftp_nb_fget($this->conn_id, $fh, $remote_file_path, $mode, $file_last_size);

		if (false == $ret) return false;

		while (FTP_MOREDATA == $ret) {

			if ($updraftplus) {
				$file_now_size = filesize($local_file_path);
				if ($file_now_size - $file_last_size > 524288) {
					$updraftplus->log("FTP fetch: file size is now: ".sprintf("%0.2f", filesize($local_file_path)/1048576)." MB");
					$file_last_size = $file_now_size;
				}
				clearstatcache();
			}

			$ret = ftp_nb_continue($this->conn_id);
		}

		fclose($fh);

		if (FTP_FINISHED == $ret) {
			if ($updraftplus) $updraftplus->log("FTP fetch: fetch complete");
			return true;
		} else {
			if ($updraftplus) $updraftplus->log("FTP fetch: fetch failed");
			return false;
		}

	}

	public function chmod($permissions, $remote_filename) {
		if ($this->is_octal($permissions)) {
			$result = ftp_chmod($this->conn_id, $permissions, $remote_filename);
			return ($result) ? true : false;
		} else {
			throw new Exception('$permissions must be an octal number');
		}
	}
 
	public function chdir($directory) {
		ftp_chdir($this->conn_id, $directory);
	}
 
	public function delete($remote_file_path) {

		if ($this->curl_handle) {
			if (true === $this->curl_handle) $this->connect();
			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.$remote_file_path);
			curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl_handle, CURLOPT_QUOTE, array('DELE '.$remote_file_path));
			// Unset some (possibly) previously-set options
			curl_setopt($this->curl_handle, CURLOPT_UPLOAD, false);
			curl_setopt($this->curl_handle, CURLOPT_INFILE, STDIN);
			$output = curl_exec($this->curl_handle);
			return $output;
		}

		return (ftp_delete($this->conn_id, $remote_file_path)) ? true : false;

	}
 
	public function make_dir($directory) {
		return ftp_mkdir($this->conn_id, $directory) ? true : false;
	}
 
	public function rename($old_name, $new_name) {
		return ftp_rename($this->conn_id, $old_name, $new_name) ? true : false;
	}
 
	public function remove_dir($directory) {
		return ftp_rmdir($this->conn_id, $directory);
	}
 
	public function dir_list($directory) {
		if ($this->curl_handle) {
			// Can't get this to work - it might just be the vsftpd server I am testing on; it hangs strangely. But this means I can't test it.
			return new WP_Error('unsupported_op', sprintf(__('The UpdraftPlus module for this file access method (%s) does not support listing files', 'updraftplus'), 'FTP (SSL/Implicit)'));
			if (true === $this->curl_handle) $this->connect();
			curl_setopt($this->curl_handle, CURLOPT_URL, 'ftps://'.$this->host.'/'.trailingslashit($directory));
			curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl_handle, CURLOPT_TIMEOUT, 10);
			$output = curl_exec($this->curl_handle);
			return $output;
		}

		return ftp_nlist($this->conn_id, $directory);
	}
 
	public function cdup() {
		return ftp_cdup($this->conn_id);
	}
 
	public function size($f) {
		return $this->curl_handle ? false : ftp_size($this->conn_id, $f);
	}

	public function current_dir() {
		return ftp_pwd($this->conn_id);
	}
 
	private function is_octal($i) {
		return decoct(octdec($i)) == $i;
	}
 
	public function __destruct() {
		if ($this->conn_id) ftp_close($this->conn_id);
	}
}
