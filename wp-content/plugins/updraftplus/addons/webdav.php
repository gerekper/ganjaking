<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: webdav:WebDAV Support
Description: Allows UpdraftPlus to backup to WebDAV servers
Version: 3.0
Shop: /shop/webdav/
Include: includes/PEAR
*/
// @codingStandardsIgnoreEnd

/*
To look at:
http://sabre.io/dav/http-patch/
http://sabre.io/dav/davclient/
https://blog.sphere.chronosempire.org.uk/2012/11/21/webdav-and-the-http-patch-nightmare
*/

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

// In PHP 5.2, the instantiation of the class has to be after it is defined, if the class is extending a class from another file. Hence, that has been moved to the end of this file.

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

class UpdraftPlus_Addons_RemoteStorage_webdav extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {
	
	public $upload_stream_chunk_size = 2097152;

	public $download_stream_chunk_size = 5242880;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->is_supress_initial_remote_404_log = true;
		$this->method = 'webdav';
		$this->desc = 'WebDAV';
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'url' => ''
		);
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['url'])) {
			$url = parse_url($opts['url']);
			if (!is_array($url)) return false;
			if ("" !== $url['host'] && "" !== $url['user'] && "" !== $url['pass']) return true;
		}
		return false;
	}

	/**
	 * This function sets up the remote storage object
	 *
	 * @param Boolean $opts    - unused boolean
	 * @param Boolean $connect - unused boolean
	 *
	 * @return Boolean - returns true on success
	 */
	public function bootstrap($opts = false, $connect = true) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if (!class_exists('HTTP_WebDAV_Client_Stream')) {
			// Needed in the include path because PEAR modules (including the file immediately required) will themselves require based on the relative path only
			set_include_path(UPDRAFTPLUS_DIR.'/includes/PEAR'.PATH_SEPARATOR.get_include_path());
			include_once(UPDRAFTPLUS_DIR.'/includes/PEAR/HTTP/WebDAV/Client.php');
		}
		return true;
	}
	
	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $webdav - An array of WebDAV options
	 *
	 * @return Array - the returned array can either be the set of updated WebDAV settings or a WordPress error array
	 */
	public function options_filter($webdav) {
	
		// Get the current options (and possibly update them to the new format)
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('webdav');

		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: WebDAV $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $webdav;
		}

		// If the input is not as expected, then return the current options
		if (!is_array($webdav)) return $opts;

		// Remove instances that no longer exist
		if (!empty($opts['settings']) && is_array($opts['settings'])) {
			foreach ($opts['settings'] as $instance_id => $storage_options) {
				if (!isset($webdav['settings'][$instance_id])) unset($opts['settings'][$instance_id]);
			}
		}

		// WebDAV has a special case where the settings could be empty so we should check for this before proceeding
		if (!empty($webdav['settings'])) {
			
			foreach ($webdav['settings'] as $instance_id => $storage_options) {
				if (isset($storage_options['webdav'])) {
			
					$slash = "/";
					$host = "";
					$colon = "";
					$port_colon = "";
					
					if ((80 == $storage_options['port'] && 'webdav' == $storage_options['webdav']) || (443 == $storage_options['port'] && 'webdavs' == $storage_options['webdav'])) {
						$storage_options['port'] = '';
					}
					
					if ('/' == substr($storage_options['path'], 0, 1)) {
						$slash = "";
					}
					
					if (false === strpos($storage_options['host'], "@")) {
						$host = "@";
					}
					
					if ('' != $storage_options['user'] && '' != $storage_options['pass']) {
						$colon = ":";
					}
					
					if ('' != $storage_options['host'] && '' != $storage_options['port']) {
						$port_colon = ":";
					}

					if (!empty($storage_options['url']) && 'http' == strtolower(substr($storage_options['url'], 0, 4))) {
						$storage_options['url'] = 'webdav'.substr($storage_options['url'], 4);
					} elseif ('' != $storage_options['user'] && '' != $storage_options['pass']) {
						$storage_options['url'] = $storage_options['webdav'].urlencode($storage_options['user']).$colon.urlencode($storage_options['pass']).$host.urlencode($storage_options['host']).$port_colon.$storage_options['port'].$slash.$storage_options['path'];
					} else {
						$storage_options['url'] = $storage_options['webdav'].urlencode($storage_options['host']).$port_colon.$storage_options['port'].$slash.$storage_options['path'];
					}

					$opts['settings'][$instance_id]['url'] = $storage_options['url'];

					// Now we have constructed the URL we should loop over the options and save any extras, but we should ignore the options used to create the URL as they are no longer needed.
					$skip_keys = array("url", "webdav", "user", "pass", "host", "port", "path");

					foreach ($storage_options as $key => $value) {
						if (!in_array($key, $skip_keys)) {
							$opts['settings'][$instance_id][$key] = $storage_options[$key];
						}
					}
				}
			}
		}
		
		return $opts;
	}
	
	/**
	 * Get the pre configuration template (directly output)
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		$classes = $this->get_css_classes(false);
		
		?>
		<tr class="<?php echo $classes . ' ' . $this->method . '_pre_config_container';?>">
			<td colspan="2">
				<h3><?php echo $this->desc; ?></h3>
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
		$template_str = $this->get_configuration_middlesection_template();
		$template_str .= $this->get_test_button_html($this->desc);
		return $template_str;
	}

	/**
	 * Get configuration template of middle section
	 *
	 * @return String - the partial template, ready for substitutions to be carried out
	 */
	public function get_configuration_middlesection_template() {
		ob_start();
		$classes = $this->get_css_classes();
		?>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('WebDAV URL', 'updraftplus');?>:</th>
				<td>
					<input data-updraft_settings_test="url" type="hidden" <?php $this->output_settings_field_name_and_id('url');?> value="{{url}}" />
					<input id="<?php echo $this->get_css_id('masked_url'); ?>" title="<?php _e('This WebDAV URL is generated by filling in the options below. If you do not know the details, then you will need to ask your WebDAV provider.', 'updraftplus');?>" type="text" class="updraft_input--wide" value="{{#if is_webdavs_protocol}}webdavs://{{else}}webdav://{{/if}}{{user}}{{#if pass}}:{{maskPassword pass}}{{/if}}{{#if host}}@{{encodeURIComponent host}}{{/if}}{{#if port}}:{{port}}{{/if}}{{path}}" readonly />
					<p>
						<em><?php _e('This WebDAV URL is generated by filling in the options below. If you do not know the details, then you will need to ask your WebDAV provider.', 'updraftplus');?></em>
					</p>
				</td>
			</tr>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Protocol (SSL or not)', 'updraftplus');?>:</th>
				<td>
					<select <?php $this->output_settings_field_name_and_id('webdav');?> class="updraft_webdav_settings" >
						<option value="webdav://" {{#if is_webdav_protocol}}selected="selected"{{/if}}>webdav://</option>
						<option value="webdavs://" {{#if is_webdavs_protocol}}selected="selected"{{/if}}>webdavs://</option>
					</select>
				</td>
			</tr>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Username', 'updraftplus');?>:</th>
				<td>
					<input type="text" <?php $this->output_settings_field_name_and_id('user');?> class="updraft_webdav_settings updraft_input--wide" value="{{user}}"/>
				</td>
			</tr>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Password', 'updraftplus');?>:</th>
				<td>
					<input type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" <?php $this->output_settings_field_name_and_id('pass');?> class="updraft_webdav_settings updraft_input--wide" value="{{pass}}" />
				</td>
			</tr>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Host', 'updraftplus');?>:</th>
				<td>
					<input type="text" <?php $this->output_settings_field_name_and_id('host');?> class="updraft_webdav_settings updraft_input--wide" value="{{host}}"/>
					<br>
					<em id="updraft_webdav_host_error" style="display: none;"><?php echo __('Error:', 'updraftplus').' '.__('A host name cannot contain a slash.', 'updraftplus').' '.__('Enter any path in the field below.', 'updraftplus'); ?></em>
				</td>
			</tr>
			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Port', 'updraftplus');?>:</th>
				<td>
					<input title="<?php _e('Leave this blank to use the default (80 for webdav, 443 for webdavs)', 'updraftplus');?>" type="number" step="1" min="1" max="65535" <?php $this->output_settings_field_name_and_id('port');?> class="updraft_webdav_settings updraft_input--wide" value="{{port}}" />
					<br>
					<em><?php _e('Leave this blank to use the default (80 for webdav, 443 for webdavs)', 'updraftplus');?></em>
				</td>
			</tr>

			<tr class="<?php echo $classes; ?>">
				<th><?php _e('Path', 'updraftplus');?>:</th>
				<td>
					<input type="text" <?php $this->output_settings_field_name_and_id('path');?> class="updraft_webdav_settings updraft_input--wide" value="{{path}}"/>
				</td>
			</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 *
	 * @return array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		$url = isset($opts['url']) ? $opts['url'] : '';
		$parse_url = @parse_url($url);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if (false === $parse_url) $url = '';
		$opts['url'] = $url;
		$url_scheme = @parse_url($url, PHP_URL_SCHEME);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ('webdav' == $url_scheme) {
			$opts['is_webdav_protocol'] = true;
		} elseif ('webdavs' == $url_scheme) {
			$opts['is_webdavs_protocol'] = true;
		}
		$opts['user'] = urldecode(@parse_url($url, PHP_URL_USER));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$opts['pass'] = urldecode(@parse_url($url, PHP_URL_PASS));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$opts['host'] = urldecode(@parse_url($url, PHP_URL_HOST));// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$opts['port'] = @parse_url($url, PHP_URL_PORT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		$opts['path'] = @parse_url($url, PHP_URL_PATH);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		return $opts;
	}

	/**
	 * This method will take the passed in credentials and try and connect and write data to the remote storage option
	 *
	 * @param  Array $posted_settings - an array of settings
	 *
	 * @return Void - result is echoed to page
	 */
	public function credentials_test($posted_settings) {
	
		if (empty($posted_settings['url'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), 'URL');
			return;
		}

		$url = preg_replace('/^http/i', 'webdav', untrailingslashit($posted_settings['url']));
		
		$storage = $this->bootstrap();

		if (is_wp_error($storage) || true !== $storage) {
			echo __("Failed", 'updraftplus').": ";
			foreach ($storage->get_error_messages() as $msg) {
				echo htmlspecialchars("$msg\n");
			}
			return;
		}

		@mkdir($url);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
		// $updraftplus_webdav_filepath shold have readable file path when file is being send on the webdav filesystem
		if ('webdav' == $this->method) {
			global $updraftplus, $updraftplus_webdav_filepath;
			$updraftplus_webdav_filepath = $updraftplus->backups_dir_location().'/index.html';
		}
		$testfile = $url.'/'.md5(time().rand());
		if (file_put_contents($testfile, 'test')) {
			_e("Success", 'updraftplus');
			@unlink($testfile);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} else {
			_e("Failed: We were not able to place a file in that directory - please check your credentials.", 'updraftplus');
		}
	}

	/**
	 * Delete a single file from the service
	 *
	 * @param Boolean      $ret         - value to return
	 * @param Array|String $files       - array of file names to delete
	 * @param Array        $storage_arr - service details
	 *
	 * @return Boolean|String - either a boolean true or an error code string
	 */
	public function delete_files($ret, $files, $storage_arr = false) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		if (is_string($files)) $files = array($files);

		if ($storage_arr) {
			$url = $storage_arr['url'];
		} else {
			$this->bootstrap();
			$options = $this->get_options();
			if (!array($options) || !isset($options['url'])) {
				$this->log('No '.$this->desc.' settings were found');
				$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
				return 'authentication_fail';
			}
			$url = untrailingslashit($options['url']);
		}

		$logurl = preg_replace('/:([^\@:]*)\@/', ':(password)@', $url);
		
		$ret = true;
		
		foreach ($files as $file) {
			$this->log("Delete remote: $logurl/$file");
			if (!unlink("$url/$file")) {
				$this->log("Delete failed");
				$ret = 'file_delete_error';
			}
		}
		return $ret;
	}

	/**
	 * Uploads a single file in chunks to the service
	 *
	 * @param String $file - the file to upload
	 * @param String $url  - the upload destination
	 *
	 * @return Boolean - returns true on success or false on failure
	 */
	public function chunked_upload($file, $url) {

		global $updraftplus;

		$orig_file_size = filesize($file);

		$start_offset = 0;
		$GLOBALS['updraftplus_404_should_be_logged'] = false;
		if (is_file($url)) {
			$url_size = filesize($url);
			if ($url_size == $orig_file_size) {
				$this->log("This file has already been successfully uploaded");
				return true;
			} elseif ($url_size > $orig_file_size) {
				$this->log("A larger file than expected ($url_size > $orig_file_size) already exists");
				return false;
			}
			$this->log("$url_size bytes already uploaded; resuming");
			$start_offset = $url_size;
		}
		
		$GLOBALS['updraftplus_404_should_be_logged'] = true;
		
		$chunks = floor($orig_file_size / 2097152);
		// There will be a remnant unless the file size was exactly on a 5MB boundary
		if ($orig_file_size % 2097152 > 0) $chunks++;

		if (!$fh = fopen($url, 'a')) {
			$this->log('Failed to open remote file');
			return false;
		}
		if (!$rh = fopen($file, 'rb')) {
			$this->log('Failed to open local file');
			return false;
		}

		// A hack, to pass information to a modified version of the PEAR library
		global $updraftplus_webdav_filepath;
		$updraftplus_webdav_filepath = $file;
		
		/*
		 * This is used for increase chunk size for webdav stream wrapper. WebDav stream wrapper chunk size is 8kb by default. This chunk size impacts on speed of upload
		 */
		$read_buffer_size = 131072;
		if (isset($this->upload_stream_chunk_size) && function_exists('stream_set_chunk_size')) {
			// stream_set_chunk_size() exists in PHP 5.4+
			// @codingStandardsIgnoreLine
			$ret_set_chunk_size = stream_set_chunk_size($fh, $this->upload_stream_chunk_size);
			if (false === $ret_set_chunk_size) {
				$this->log(sprintf("Upload chunk size: failed to change to %d bytes", $this->upload_stream_chunk_size));
			} else {
				$read_buffer_size = min($this->upload_stream_chunk_size, 1048576);
				$this->log(sprintf("Upload chunk size: successfully changed to %d bytes", $this->upload_stream_chunk_size));
			}
		}

		$last_time = time();
		for ($i = 1; $i <= $chunks; $i++) {

			$chunk_start = ($i-1)*2097152;
			$chunk_end = min($i*2097152-1, $orig_file_size);

			if ($start_offset > $chunk_end) {
				$this->log("Chunk $i: Already uploaded");
			} else {

				fseek($fh, $chunk_start);
				fseek($rh, $chunk_start);

				$bytes_left = $chunk_end - $chunk_start;
				while ($bytes_left > 0) {
					if ($buf = fread($rh, $read_buffer_size)) {
						if (fwrite($fh, $buf, strlen($buf))) {
							$bytes_left = $bytes_left - strlen($buf);
							if (time()-$last_time > 15) {
								$last_time = time();
								touch($file);
							}
						} else {
							$this->log(sprintf(__("Chunk %s: A %s error occurred", 'updraftplus'), $i, 'write'), 'error');
							return false;
						}
					} else {
						$this->log(sprintf(__("Chunk %s: A %s error occurred", 'updraftplus'), $i, 'read'), 'error');
						return false;
					}
				}
			}

			$updraftplus->record_uploaded_chunk(round(100*$i/$chunks, 1), "$i", $file);

		}

		// N.B. fclose() always returns true for stream wrappers - stream wrappers' return values are ignored - http://php.net/manual/en/streamwrapper.stream-close.php (29-Jan-2015)
		try {
			if (!fclose($fh)) {
				$this->log('Upload failed (fclose error)');
				$this->log(__('Upload failed', 'updraftplus'), 'error');
				return false;
			}
		} catch (Exception $e) {
			$this->log('Upload failed (fclose exception; class='.get_class($e).'): '.$e->getMessage());
			$this->log(__('Upload failed', 'updraftplus'), 'error');
			return false;
		}
		fclose($rh);

		return true;

	}

	/**
	 * Lists files found at the service which match the passed in string
	 *
	 * @param String $match - the string we want to match when searching for files
	 *
	 * @return Array - an array of files found
	 */
	public function listfiles($match = 'backup_') {

		$storage = $this->bootstrap();
		if (is_wp_error($storage)) return $storage;

		$options = $this->get_options();
		if (!array($options) || empty($options['url'])) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));

		$url = trailingslashit($options['url']);

		// A change to how WebDAV settings are saved resulted in near-empty URLs being saved, like webdav:/// . Detect 'empty URLs'.
		if (preg_match('/^[a-z]+:$/', untrailingslashit($url))) {
			return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));
		}
		
		if (false == ($handle = opendir($url))) return new WP_Error('no_access', sprintf('Failed to gain %s access', $this->desc));

		$results = array();

		while (false !== ($entry = readdir($handle))) {
			if (is_file($url.$entry) && 0 === strpos($entry, $match)) {
				$results[] = array('name' => $entry, 'size' => filesize($url.$entry));
			}
		}

		return $results;

	}

	/**
	 * Uploads a list of files to the service
	 *
	 * @param Boolean $ret          - a boolean
	 * @param Array   $backup_array - an array of files to upload
	 *
	 * @return Array|Boolean - returns an array on success or boolean false on failure
	 */
	public function upload_files($ret, $backup_array) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;

		$storage = $this->bootstrap();

		if (is_wp_error($storage)) {
			foreach ($storage->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		$options = $this->get_options();
		if (!array($options) || !isset($options['url'])) {
			$this->log('No '.$this->desc.' settings were found');
			$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
			return false;
		}

		$any_failures = false;

		$updraft_dir = untrailingslashit($updraftplus->backups_dir_location());
		$url = untrailingslashit($options['url']);

		foreach ($backup_array as $file) {
			$this->log("upload: attempt: $file");
			if ($this->chunked_upload($updraft_dir.'/'.$file, $url.'/'.$file)) {
				$updraftplus->uploaded_file($file);
			} else {
				$any_failures = true;
				$this->log('ERROR: '.$this->desc.': Failed to upload file: '.$file);
				$this->log(__('Error', 'updraftplus').': '.$this->desc.': '.sprintf(__('Failed to upload to %s', 'updraftplus'), $file), 'error');
			}
		}

		return ($any_failures) ? null : array('url' => $url);

	}

	/**
	 * Downloads a list of files from the service
	 *
	 * @param Boolean $ret   - a boolean
	 * @param Array   $files - an array of files to download
	 *
	 * @return Boolean - returns false on failure and true on success
	 */
	public function download_file($ret, $files) {

		global $updraftplus;

		if (is_string($files)) $files = array($files);

		$storage = $this->bootstrap();
		if (is_wp_error($storage)) {
			foreach ($storage->get_error_messages() as $msg) {
				$this->log($msg);
				$this->log($msg, 'error');
			}
			return false;
		}

		$options = $this->get_options();

		if (!array($options) || !isset($options['url'])) {
			$this->log('No '.$this->desc.' settings were found');
			$this->log(sprintf(__('No %s settings were found', 'updraftplus'), $this->desc), 'error');
			return false;
		}

		$ret = true;
		foreach ($files as $file) {

			$fullpath = $updraftplus->backups_dir_location().'/'.$file;
			$url = untrailingslashit($options['url']).'/'.$file;

			$start_offset = (file_exists($fullpath)) ? filesize($fullpath) : 0;

			if (@filesize($url) == $start_offset) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$ret = false;
				continue;
			}

			if (!$fh = fopen($fullpath, 'a')) {
				$this->log("Error opening local file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening local file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}

			if (!$rh = fopen($url, 'rb')) {
				$this->log("Error opening remote file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening remote file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}
			
			$read_buffer_size = 262144;
			
			/*
			 * This is used for increase chunk size for webdav stream wrapper. WebDav stream wrapper chunk size is 8kb by default. This chunk size impacts on speed of download
			 */
			 
			// stream_set_chunk_size function exist in >= 5.4.0 Php version
			if (isset($this->download_stream_chunk_size) && function_exists('stream_set_chunk_size')) {
				// @codingStandardsIgnoreLine
				$ret_set_chunk_size = stream_set_chunk_size($rh, $this->download_stream_chunk_size);
				if (false === $ret_set_chunk_size) {
					$this->log(sprintf(__("Download chunk size failed to change to %d", 'updraftplus'), $this->download_stream_chunk_size));
				} else {
					$read_buffer_size = $this->download_stream_chunk_size;
					$this->log(sprintf(__("Download chunk size successfully changed to %d", 'updraftplus'), $this->download_stream_chunk_size));
				}
			}

			if ($start_offset) {
				fseek($fh, $start_offset);
				fseek($rh, $start_offset);
			}

			while (!feof($rh) && $buf = fread($rh, $read_buffer_size)) {
				if (!fwrite($fh, $buf, strlen($buf))) {
					$this->log("Error: Local write failed: Failed to download: $file");
					$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Local write failed: Failed to download', 'updraftplus'), 'error');
					$ret = false;
					continue;
				}
			}
		}

		return $ret;

	}
}

// Do *not* instantiate here; it is a storage module, so is instantiated on-demand
// $updraftplus_addons_webdav = new UpdraftPlus_Addons_RemoteStorage_webdav;
