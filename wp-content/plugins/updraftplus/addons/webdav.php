<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: webdav:WebDAV Support
Description: Allows UpdraftPlus to backup to WebDAV servers
Version: 3.0
Shop: /shop/webdav/
Include: includes/PEAR
RequiresPHP: 5.5
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
if (!defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) define('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT', 33); // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ConstantNotUpperCase

class UpdraftPlus_Addons_RemoteStorage_webdav extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {
	
	/**
	 * The size of chunk upload
	 *
	 * @access private
	 * @var    integer
	 */
	private $upload_chunk_size = 2097152;

	/**
	 * The size of chunk download
	 *
	 * @access private
	 * @var    integer
	 */
	private $download_chunk_size = 5242880;

	/**
	 * User-Agent: header string
	 *
	 * @access private
	 * @var    string
	 */
	private $userAgent;

	/**
	 * Content-type: header string
	 *
	 * @access private
	 * @var    string
	 */
	private $contentType = "application/octet-stream";

	/**
	 * The http or https resource URL
	 *
	 * @access private
	 * @var    string  url
	 */
	private $url = false;

	/**
	 * The resource URL path
	 *
	 * @access private
	 * @var    string  path
	 */
	private $path = false;

	/**
	 * File position indicator
	 *
	 * @access private
	 * @var    int     offset in bytes
	 */
	private $position = 0;

	/**
	 * File status information cache
	 *
	 * @access private
	 * @var    array   stat information
	 */
	private $stat = array();

	/**
	 * User name for authentication
	 *
	 * @access private
	 * @var    string  name
	 */
	private $user = false;

	/**
	 * Password for authentication
	 *
	 * @access private
	 * @var    string  password
	 */
	private $pass = false;

	/**
	 * WebDAV protocol levels supported by the server
	 *
	 * @access private
	 * @var    array   level entries
	 */
	private $dav_level = array();

	/**
	 * HTTP methods supported by the server
	 *
	 * @access private
	 * @var    array   method entries
	 */
	private $dav_allow = array();

	/**
	 * Directory content cache
	 *
	 * @access private
	 * @var    array   filename entries
	 */
	private $dirfiles = false;

	/**
	 * Current readdir() position
	 *
	 * @access private
	 * @var    int
	 */
	private $dirpos = 0;

	/**
	 * Remember if end of file was reached
	 *
	 * @access private
	 * @var    bool
	 */
	private $eof = false;

	/**
	 * Lock token
	 *
	 * @access private
	 * @var    string
	 */
	private $locktoken = false;

	private $write_returned_recoverable_error = false;

	private $write_final = false;
	
	private $write_returned_for_final_write = false;

	private $error_404_should_be_logged = false;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		global $updraftplus;

		$this->is_supress_initial_remote_404_log = true;
		$this->method = 'webdav';
		$this->desc = 'WebDAV';
		$this->userAgent = 'UpdraftPlus/'.$updraftplus->version;
	}

	/**
	 * Load required libraries
	 */
	public function load_libraries() {
		set_include_path(UPDRAFTPLUS_DIR.'/includes/PEAR'.PATH_SEPARATOR.get_include_path());
		require_once UPDRAFTPLUS_DIR.'/includes/PEAR/HTTP/Request2.php';
		require_once UPDRAFTPLUS_DIR.'/includes/PEAR/HTTP/WebDAV/Tools/_parse_propfind_response.php';
		require_once UPDRAFTPLUS_DIR.'/includes/PEAR/HTTP/WebDAV/Tools/_parse_lock_response.php';
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
		?>
		<tr class="{{get_template_css_classes false}} {{method_id}}_pre_config_container">
			<td colspan="2">
				<h3>{{method_display_name}}</h3>
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
		return $this->get_configuration_middlesection_template();
	}

	/**
	 * Get configuration template of middle section
	 *
	 * @return String - the partial template, ready for substitutions to be carried out
	 */
	public function get_configuration_middlesection_template() {
		ob_start();
		?>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_url_label}}:</th>
				<td>
					<input data-updraft_settings_test="url" type="hidden" id="{{get_template_input_attribute_value "id" "url"}}" name="{{get_template_input_attribute_value "name" "url"}}" value="{{url}}" />
					<input id="{{get_template_input_attribute_value "id" "masked_url"}}" title="{{input_url_title}}" type="text" class="updraft_input--wide udc-wd-600" value="{{#if is_webdavs_protocol}}webdavs://{{else}}webdav://{{/if}}{{user}}{{#if pass}}:{{maskPassword pass}}{{/if}}{{#if host}}@{{encodeURIComponent host}}{{/if}}{{#if port}}:{{port}}{{/if}}{{path}}" readonly />
					<p class="udc-wd-600">
						<em>{{input_url_title}}</em>
					</p>
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_protocol_label}}:</th>
				<td>
					<select id="{{get_template_input_attribute_value "id" "webdav"}}" name="{{get_template_input_attribute_value "name" "webdav"}}" class="updraft_webdav_settings udc-wd-600" >
						<option value="webdav://" {{#if is_webdav_protocol}}selected="selected"{{/if}}>webdav://</option>
						<option value="webdavs://" {{#if is_webdavs_protocol}}selected="selected"{{/if}}>webdavs://</option>
					</select>
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_username_label}}:</th>
				<td>
					<input type="text" id="{{get_template_input_attribute_value "id" "user"}}" name="{{get_template_input_attribute_value "name" "user"}}" class="updraft_webdav_settings updraft_input--wide udc-wd-600" value="{{user}}"/>
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_password_label}}:</th>
				<td>
					<input type="{{input_password_type}}" id="{{get_template_input_attribute_value "id" "pass"}}" name="{{get_template_input_attribute_value "name" "pass"}}" class="updraft_webdav_settings updraft_input--wide udc-wd-600" value="{{pass}}" />
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_host_label}}:</th>
				<td>
					<input type="text" id="{{get_template_input_attribute_value "id" "host"}}" name="{{get_template_input_attribute_value "name" "host"}}" class="updraft_webdav_settings updraft_input--wide udc-wd-600" value="{{host}}"/>
					<br>
					<em class="updraft_webdav_host_error" style="display: none;">{{hostname_error_label}}</em>
				</td>
			</tr>
			<tr class="{{get_template_css_classes true}}">
				<th>{{input_port_label}}:</th>
				<td>
					<input title="{{input_port_title}}" type="number" step="1" min="1" max="65535" id="{{get_template_input_attribute_value "id" "port"}}" name="{{get_template_input_attribute_value "name" "port"}}" class="updraft_webdav_settings updraft_input--wide udc-wd-600 udc-ta-left" value="{{port}}" />
					<br>
					<em>{{input_port_title}}</em>
				</td>
			</tr>

			<tr class="{{get_template_css_classes true}}">
				<th>{{input_path_title}}:</th>
				<td>
					<input type="text" id="{{get_template_input_attribute_value "id" "path"}}" name="{{get_template_input_attribute_value "name" "path"}}" class="updraft_webdav_settings updraft_input--wide udc-wd-600" value="{{path}}"/>
				</td>
			</tr>
			{{{get_template_test_button_html "WebDav"}}}
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
			'input_url_label' => __('WebDAV URL', 'updraftplus'),
			'input_url_title' => __('This WebDAV URL is generated by filling in the options below. If you do not know the details, then you will need to ask your WebDAV provider.', 'updraftplus'),
			'input_protocol_label' => __('Protocol (SSL or not)', 'updraftplus'),
			'input_username_label' => __('Username', 'updraftplus'),
			'input_password_label' => __('Password', 'updraftplus'),
			'input_password_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_host_label' => __('Host', 'updraftplus'),
			'hostname_error_label' => __('Error:', 'updraftplus').' '.__('A host name cannot contain a slash.', 'updraftplus').' '.__('Enter any path in the field below.', 'updraftplus'),
			'input_port_label' => __('Port', 'updraftplus'),
			'input_port_title' => __('Leave this blank to use the default (80 for webdav, 443 for webdavs)', 'updraftplus'),
			'input_path_title' => __('Path', 'updraftplus'),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
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
		
		@$this->mkdir($url);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		
		// $updraftplus_webdav_filepath shold have readable file path when file is being send on the webdav filesystem
		if ('webdav' == $this->method) {
			global $updraftplus, $updraftplus_webdav_filepath;
			$updraftplus_webdav_filepath = $updraftplus->backups_dir_location().'/index.html';
		}
		$testfile = $url.'/'.md5(time().rand());
		$this->_parse_url($testfile);
		if ($this->write('test')) {
			_e("Success", 'updraftplus');
			$this->unlink($testfile);
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
			if (!$this->unlink("$url/$file")) {
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
		$this->error_404_should_be_logged = false;
		$url_size = $this->filesize($url);
		
		if ($url_size) {
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
		
		$this->error_404_should_be_logged = true;
		
		$chunks = floor($orig_file_size / 2097152);
		// There will be a remnant unless the file size was exactly on a 5MB boundary
		if ($orig_file_size % 2097152 > 0) $chunks++;

		if (!$rh = fopen($file, 'rb')) {
			$this->log('Failed to open local file');
			return false;
		}

		// A hack, to pass information to a modified version of the PEAR library
		global $updraftplus_webdav_filepath;
		$updraftplus_webdav_filepath = $file;
		
		$read_buffer_size = 131072;

		if (isset($this->upload_chunk_size)) {
			// @codingStandardsIgnoreLine
			$read_buffer_size = min($this->upload_chunk_size, 1048576);
			$this->log(sprintf("Upload chunk size: successfully changed to %d bytes", $this->upload_chunk_size));
		}

		$last_time = time();
		for ($i = 1; $i <= $chunks; $i++) {

			$chunk_start = ($i-1)*2097152;
			$chunk_end = min($i*2097152-1, $orig_file_size);

			if ($start_offset > $chunk_end) {
				$this->log("Chunk $i: Already uploaded");
			} else {

				$this->seek($chunk_start, SEEK_SET);
				fseek($rh, $chunk_start);

				$bytes_left = $chunk_end - $chunk_start;
				while ($bytes_left > 0) {
					if ($buf = fread($rh, $read_buffer_size)) {
						// if (fwrite($fh, $buf, strlen($buf))) {
						if ($this->write($buf)) {
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

		try {
			if (!$this->connection_close()) {
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

		$options = $this->get_options();
		if (!array($options) || empty($options['url'])) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));

		$url = trailingslashit($options['url']);

		// A change to how WebDAV settings are saved resulted in near-empty URLs being saved, like webdav:/// . Detect 'empty URLs'.
		if (preg_match('/^[a-z]+:$/', untrailingslashit($url))) {
			return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), $this->desc));
		}
		
		if (false == ($handle = $this->opendir($url))) return new WP_Error('no_access', sprintf('Failed to gain %s access', $this->desc));

		$results = array();

		while (false !== ($entry = $this->readdir())) {
			if ($this->filesize($url.$entry) && 0 === strpos($entry, $match)) {
				$results[] = array('name' => $entry, 'size' => $this->filesize($url.$entry));
			}
		}

		return $results;

	}

	/**
	 * Uploads a list of files to the service
	 *
	 * @param Boolean $ret          a boolean
	 * @param Array   $backup_array an array of files to upload
	 *
	 * @return Array|Boolean - returns an array on success or boolean false on failure
	 */
	public function upload_files($ret, $backup_array) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		global $updraftplus;

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
			$url_size = $this->filesize($url);
			if ($url_size == $start_offset) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				$ret = false;
				continue;
			}

			if (!$fh = fopen($fullpath, 'a')) {
				$this->log("Error opening local file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening local file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}

			if (!$this->connection_open($url, 'rb')) {
				$this->log("Error opening remote file: Failed to download: $file");
				$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Error opening remote file: Failed to download', 'updraftplus'), 'error');
				$ret = false;
				continue;
			}

			$read_buffer_size = 262144;
			
			if (isset($this->download_chunk_size)) {
				// @codingStandardsIgnoreLine
				$read_buffer_size = $this->download_chunk_size;
				$this->log(sprintf(__("Download chunk size successfully changed to %d", 'updraftplus'), $this->download_chunk_size));
			}

			if ($start_offset) {
				fseek($fh, $start_offset);
				$this->seek($start_offset, SEEK_SET);
			}

			while (!$this->eof() && $buf = $this->read($read_buffer_size)) {
				if (!fwrite($fh, $buf, strlen($buf))) {
					$this->log("Error: Local write failed: Failed to download: $file");
					$this->log("$file: ".sprintf(__("%s Error", 'updraftplus'), $this->desc).": ".__('Local write failed: Failed to download', 'updraftplus'), 'error');
					$ret = false;
					continue;
				}
			}

			$this->connection_close();
		}

		return $ret;

	}

	/**
	 * Method for open connection
	 *
	 * @access public
	 * @param  string $path resource URL
	 * @param  string $mode flags
	 * @return bool   true on success
	 */
	public function connection_open($path, $mode = null) {
		global $updraftplus;

		// rewrite the request URL
		if (!$this->_parse_url($path)) return false;

		$writing = preg_match('|[aw\+]|', $mode);
		
		// query server for WebDAV options
		if (!$this->_check_options()) {
			if ($writing) {
				// Retry on the directory instead of on the file itself
				$old_url = $this->url;
				$this->url = dirname($this->url);
				if (!$this->_check_options()) {
					$this->url = $old_url;
					$updraftplus->log('Failed to check WebDAV server options');
					return false;
				}
				$this->url = $old_url;
			} else {
				$updraftplus->log('Failed to check WebDAV server options');
				return false;
			}
		}

		try {
			// now get the file metadata
			// we only need type, size, creation and modification date
			$req = $this->_startRequest('PROPFIND');
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			$req->setHeader('Depth', "0");
			$req->setHeader('Content-type', 'text/xml');
			$req->setBody('<?xml version="1.0" encoding="utf-8"?>
			<propfind xmlns="DAV:">
			<prop>
			<resourcetype/>
			<getcontentlength/>
			<getlastmodified />
			<creationdate/>
			</prop>
			</propfind>
			');
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}

		// check the response code, anything but 207 indicates a problem
		switch ($result->getStatus()) {
			case 207:
				// OK
				// now we have to parse the result to get the status info items
				$propinfo = new HTTP_WebDAV_Client_parse_propfind_response($result->getBody());
				$this->stat = $propinfo->stat();
				unset($propinfo);
				break;

			case 404:
				// not found is ok in write modes
				if (preg_match('|[aw\+]|', $mode)) {
					break; // write
				}
				$this->eof = true;
				// else fallthru
			
			/*
			case 405: // method disabled. In write mode, try to carry on.
				if (preg_match('|[aw\+]|', $mode)) {
					break; // write
				}
				$this->eof = true;
			*/
			// N.B. Some 404s drop also through to here
			default:
				// Log only if the condition was not expected
				if ($this->error_404_should_be_logged) {
					trigger_error("file not found: ".UpdraftPlus_HTTP_Error_Descriptions::get_http_status_code_description(404));
				}
				return false;
		}
		
		// 'w' -> open for writing, truncate existing files
		if (strpos($mode, "w") !== false) {
			try {
				$req = $this->_startRequest(HTTP_Request2::METHOD_PUT);

				$req->setHeader('Content-length', 0);
	
				if (is_string($this->user)) {
					$req->setAuth($this->user, $this->pass);
				}
	
				$req->send();
			} catch (Exception $e) {
				if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
					return $this->_check_options();
				}
				throw $e;
			}
		}

		// we are done :)
		return true;
	}


	/**
	 * Method for close connection
	 *
	 * @access public
	 */
	public function connection_close() {
		global $updraftplus, $updraftplus_webdav_filepath;
		if ((defined('UPDRAFTPLUS_WEBDAV_NEVER_CHUNK') && UPDRAFTPLUS_WEBDAV_NEVER_CHUNK && true === $this->write_returned_for_final_write) || $this->write_returned_recoverable_error) {
			if (!empty($updraftplus_webdav_filepath) && is_readable($updraftplus_webdav_filepath)) {
				$this->position = 0;
				$this->write_returned_for_final_write = false;
				$this->write_final = true;
				if (false === $this->write(file_get_contents($updraftplus_webdav_filepath))) {
					$this->write_final = false;
					$updraftplus->log('WebDAV: All-in-one write failed');
					// The return result is ignored; so, we throw an exception instead
					throw new Exception('WebDAV: All-in-one write failed');
					return false;
				} else {
					$updraftplus->log('WebDAV: All-in-one write succeeded');
				}
				$this->write_final = false;
			} else {
				$updraftplus->log("File not readable: $updraftplus_webdav_filepath");
				throw new Exception("File not readable: $updraftplus_webdav_filepath");
				return false;
			}
		}

		// unlock?
		if ($this->locktoken) {
			$this->lock(LOCK_UN);
		}

		// closing is simple as HTTP is stateless
		$this->url = $this->eof = false;
		$this->position = 0;
		
		return true;
	}

	/**
	 * Method for retrieving information about a file resource
	 *
	 * @access public
	 * @return array  stat entries
	 */
	public function stat() {
		// we already have collected the needed information
		// in connection_open() :)
		return $this->stat;
	}

	/**
	 * Method for reading a file resource
	 *
	 * @access public
	 * @param  int $count requested byte count
	 * @return string read data
	 */
	public function read($count) {
		// do some math
		$start = $this->position;
		$end   = $start + $count - 1;

		try {
			// create a GET request with a range
			$req = $this->_startRequest(HTTP_Request2::METHOD_GET);
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			$req->setHeader("Range", "bytes=$start-$end");

			// go! go! go!
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}
		$data = $result->getBody();
		$len  = strlen($data);

		// lets see what happened
		switch ($result->getStatus()) {
			case 200:
				// server doesn't support range requests
				// TODO we should add some sort of cacheing here - inherited from initial commit
				$data = substr($data, $start, $count);
				break;

			case 206:
				// server supports range requests
				break;

			case 416:
				// reading beyond end of file is not an error
				$data = "";
				$len  = 0;
				break;

			default:
				return false;
		}

		// no data indicates end of file
		if (!$len) {
			$this->eof = true;
		}

		// update position
		$this->position += $len;

		// thats it!
		return $data;
	}

	/**
	 * Method for writing a file resource
	 *
	 * @access public
	 * @param  string $buffer data to write
	 * @return int    number of bytes actually written
	 */
	public function write($buffer) {
		// do some math
		$start = $this->position;
		$end   = $this->position + strlen($buffer) - 1;
		
		if (((defined('UPDRAFTPLUS_WEBDAV_NEVER_CHUNK') && UPDRAFTPLUS_WEBDAV_NEVER_CHUNK) || $this->write_returned_recoverable_error) && !$this->write_final) {
			$this->position += strlen($buffer);
			return 1 + $end - $start;
		}

		$method = ($start > 0 && defined('UPDRAFTPLUS_WEBDAV_USE_SABRE_APPEND') && UPDRAFTPLUS_WEBDAV_USE_SABRE_APPEND) ? 'PATCH' : HTTP_Request2::METHOD_PUT;

		try {
			// create a partial PUT request
			$req = $this->_startRequest($method);
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}

			if (defined('UPDRAFTPLUS_WEBDAV_USE_SABRE_APPEND') && UPDRAFTPLUS_WEBDAV_USE_SABRE_APPEND) {

			if ($start>0) {
				$req->setHeader('Content-Type', 'application/x-sabredav-partialupdate');
				$req->setHeader("X-Update-Range", "append");
			}
			} else {
			// Special hack to drop Content-Range header for the test file
			if (($start>0 || $end>8) && !$this->write_final) $req->setHeader("Content-Range", "bytes $start-$end/*");
			}
			if ($this->locktoken) {
				$req->setHeader("If", "(<{$this->locktoken}>)");
			}
			$req->setBody($buffer);

			// go! go! go!
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}

		// check result
		switch ($result->getStatus()) {
			case 200:
			case 201:
			case 204:
				$this->position += strlen($buffer);
				return 1 + $end - $start;

			// New in UD 1.11.13 for ownCloud 8.1.? (strictly, the version of SabreDav in it)

			/*
			<?xml version="1.0" encoding="utf-8"?>
	<d:error xmlns:d="DAV:" xmlns:s="http://sabredav.org/ns">
	<s:exception>Sabre\DAV\Exception\BadRequest</s:exception>
	<s:message>Content-Range on PUT requests are forbidden.</s:message>
	</d:error>
			*/
			case 400:
				global $updraftplus, $updraftplus_webdav_filepath;
				if (false !== strpos($result->getBody(), 'Content-Range') && !empty($updraftplus_webdav_filepath)) {
					$updraftplus->log('WebDAV server returned 400 due to Content-Range issue; will try all-at-once method');
					$this->write_returned_recoverable_error = true;
					// You lie!
					return 1 + $end - $start;
				} else {
					trigger_error("Unexpected HTTP response code: ".UpdraftPlus_HTTP_Error_Descriptions::get_http_status_code_description($result->getStatus()));
					return false;
				}
				

			case 501:
				global $updraftplus, $updraftplus_webdav_filepath;
				if (!empty($updraftplus_webdav_filepath)) {
					$updraftplus->log('WebDAV server returned 501; probably does not support Content-Range; will try all-at-once method');
					$this->write_returned_recoverable_error = true;
					// You lie!
					return 1 + $end - $start;
				} else {
					return false;
				}
				
			default:
				trigger_error("Unexpected HTTP response code: ".UpdraftPlus_HTTP_Error_Descriptions::get_http_status_code_description($result->getStatus()));
				return false;
		}

		/*
		We do not cope with servers that do not support partial PUTs!
		And we do assume that a server does conform to the following
		rule from RFC 2616 Section 9.6:

		"The recipient of the entity MUST NOT ignore any Content-*
		(e.g. Content-Range) headers that it does not understand or
		implement and MUST return a 501 (Not Implemented) response
		in such cases."

		So the worst case scenario with a compliant server not
		implementing partial PUTs should be a failed request. A
		server simply ignoring "Content-Range" would replace
		file contents with the request body instead of putting
		the data at the requested place but we can blame it
		for not being compliant in this case ;)

		(TODO: maybe we should do a HTTP version check first?) - inherited from initial commit

		we *could* emulate partial PUT support by adding local
		cacheing but for now we don't want to as it adds a lot
		of complexity and storage overhead to the client ...
		*/

		return true;
	}

	/**
	 * Method for returning an end-of-file on a file pointer
	 *
	 * @access public
	 * @return bool   true if end of file was reached
	 */
	public function eof() {
		// another simple one
		return $this->eof;
	}

	/**
	 * Method for seeking to specific location of file resource
	 *
	 * @access public
	 * @param  int $pos    position to seek to
	 * @param  int $whence seek mode
	 * @return bool   true on success
	 */
	public function seek($pos, $whence) {
		switch ($whence) {
			case SEEK_SET:
				// absolute position
				$this->position = $pos;
				break;
			case SEEK_CUR:
				// relative position
				$this->position += $pos;
				break;
			case SEEK_END:
				// relative position form end
				$this->position = $this->stat['size'] + $pos;
				break;
			default:
				return false;
		}

		// TODO: this is rather naive (check how libc handles this) - inherited from initial commit
		$this->eof = false;

		return true;
	}

	/**
	 * Method for reading directory
	 *
	 * @access public
	 * @param  string $path directory resource URL
	 * @return bool   true on success
	 */
	public function opendir($path) {
		// rewrite the request URL
		if (!$this->_parse_url($path)) return false;

		// query server for WebDAV options
		if (!$this->_check_options())  return false;

		if (!isset($this->dav_allow['PROPFIND'])) {
			return false;
		}

		try {
			// now read the directory
			$req = $this->_startRequest('PROPFIND');
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			$req->setHeader("Depth", "1");
			$req->setHeader("Content-Type", "text/xml");
			$req->setBody('<?xml version="1.0" encoding="utf-8"?>
			<propfind xmlns="DAV:">
			<prop>
			<resourcetype/>
			<getcontentlength/>
			<creationdate/>
			<getlastmodified/>
			</prop>
			</propfind>
			');
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}

		switch ($result->getStatus()) {
			case 207:
				// multistatus content
				$this->dirfiles = array();
				$this->dirpos = 0;
				
				// for all returned resource entries
				foreach (explode("\n", $result->getBody()) as $line) {
					// Preg_match_all if the whole response is one line!
					if (preg_match_all("/href>([^<]*)/", $line, $matches)) {
						// skip the directory itself
						foreach ($matches[1] as $match) {
							// Compare to $this->url too
							if ("" == $match || $match == $this->path || $match == $this->url) {
								continue;
							}
							// just remember the basenames to return them later with readdir()
							$this->dirfiles[] = basename($match);
						}
					}
				}
				return true;
			default:
				// any other response state indicates an error
				if ($this->error_404_should_be_logged) {
					trigger_error("file not found: ".UpdraftPlus_HTTP_Error_Descriptions::get_http_status_code_description(404));
				}
				return false;
		}
	}


	/**
	 * Method for reading directory
	 *
	 * @access public
	 * @return string filename
	 */
	public function readdir() {
		// bailout if directory is empty
		if (!is_array($this->dirfiles)) {
			return false;
		}
		
		// bailout if we already reached end of dir
		if ($this->dirpos >= count($this->dirfiles)) {
			return false;
		}

		// return an entry and move on
		return $this->dirfiles[$this->dirpos++];
	}

	/**
	 * Method for creating a new directory
	 *
	 * @access public
	 * @param string $path collection URL to be created
	 * @return bool   true on access
	 */
	public function mkdir($path) {
		// rewrite the request URL
		if (!$this->_parse_url($path)) return false;

		// query server for WebDAV options
		if (!$this->_check_options())  return false;

		try {
			$req = $this->_startRequest('MKCOL');
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			if ($this->locktoken) {
				$req->setHeader("If", "(<{$this->locktoken}>)");
			}
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}
		
		// check the response code, anything but 201 indicates a problem
		$stat = $result->getStatus();
		switch ($stat) {
			case 201:
			case 405: // directory already created
				return true;
			default:
				trigger_error("mkdir failed - ". $stat);
				return false;
		}
	}

	/**
	 * Method for removing a file
	 *
	 * @access public
	 * @param string $path resource URL to be removed
	 * @return bool   true on success
	 */
	public function unlink($path) {
		// rewrite the request URL
		if (!$this->_parse_url($path)) return false;

		// query server for WebDAV options
		if (!$this->_check_options())  return false;

		// is DELETE supported?
		if (!isset($this->dav_allow[HTTP_Request2::METHOD_DELETE])) {
			return false;
		}

		try {
			$req = $this->_startRequest(HTTP_Request2::METHOD_DELETE);
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			if ($this->locktoken) {
				$req->setHeader("If", "(<{$this->locktoken}>)");
			}
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}

		switch ($result->getStatus()) {
			case 204:
				// ok
				return true;
			default:
				return false;
		}
	}
		


	/**
	 * Helper function for URL analysis
	 *
	 * @access private
	 * @param string $path original request URL
	 * @return bool true on success else false
	 */
	private function _parse_url($path) {
		// rewrite the WebDAV url as a plain HTTP url
		$url = parse_url($path);

		// detect whether plain or SSL-encrypted transfer is requested
		$scheme = $url['scheme'];
		switch ($scheme) {
			case "webdav":
				$url['scheme'] = "http";
				break;
			case "webdavs":
				$url['scheme'] = "https";
				break;
			default:
				trigger_error("only 'webdav:' and 'webdavs:' are supported, not '$url[scheme]:'");
				return false;
		}

		// if a TCP port is specified we have to add it after the host
		if (isset($url['port'])) {
			$url['host'] .= ":$url[port]";
		}

		// store the plain path for possible later use
		$this->path = $url["path"];

		// now we can put together the new URL
		$this->url = "$url[scheme]://$url[host]$url[path]";

		// extract authentication information
		if (isset($url['user'])) {
			$this->user = urldecode($url['user']);
		}
		if (isset($url['pass'])) {
			$this->pass = urldecode($url['pass']);
		}

		return true;
	}

	/**
	 * Helper function for WebDAV OPTIONS detection
	 *
	 * @access private
	 * @return bool    true on success else false
	 */
	private function _check_options() {
		$this->load_libraries();
		
		try {
			// now check OPTIONS reply for WebDAV response headers
			$req = $this->_startRequest(HTTP_Request2::METHOD_OPTIONS);
			if (is_string($this->user)) {
				$req->setAuth($this->user, $this->pass);
			}
			$result = $req->send();
		} catch (Exception $e) {
			if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
				return $this->_check_options();
			}
			throw $e;
		}

		if ($result->getStatus() != 200) {
			// If the status is 301 we want to return false so the calling code can deal with it but not trigger any errors on the front end
			if ($result->getStatus() != 301) trigger_error(UpdraftPlus_HTTP_Error_Descriptions::get_http_status_code_description($result->getStatus()) . ' returned when checking WebDAV server options using URL: ' . $this->url . ' response: ' . json_encode($result->getBody()));
			return false;
		}

		// get the supported DAV levels and extensions
		$dav = $result->getHeader("DAV");
		$this->dav_level = array();
		foreach (explode(",", $dav) as $level) {
			$this->dav_level[trim($level)] = true;
		}
		if (!isset($this->dav_level["1"])) {
			// we need at least DAV Level 1 conformance
			trigger_error('WebDAV server must be at least DAV level 1 conformance');
			return false;
		}
		
		// get the supported HTTP methods
		// TODO these are not checked for WebDAV compliance yet - inherited from initial commit
		$allow = $result->getHeader("Allow");
		$this->dav_allow = array();
		foreach (explode(",", $allow) as $method) {
			$this->dav_allow[trim($method)] = true;
		}

		// TODO check for required WebDAV methods - inherited from initial commit

		return true;
	}


	/**
	 * Method for locking a file resource
	 *
	 * @access private
	 * @param string $mode lock mode
	 * @return bool true on success else false
	 */
	private function lock($mode) {
		/* TODO:
		- think over how to refresh locks - inherited from initial commit
		*/
		
		$ret = false;

		// LOCK is only supported by DAV Level 2
		if (!isset($this->dav_level["2"])) {
			return false;
		}

		switch ($mode & ~LOCK_NB) {
			case LOCK_UN:
				if ($this->locktoken) {
					try {
						$req = $this->_startRequest('UNLOCK');
						if (is_string($this->user)) {
							$req->setAuth($this->user, $this->pass);
						}
						$req->setHeader("Lock-Token", "<{$this->locktoken}>");
						$result = $req->send();
					} catch (Exception $e) {
						if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
							return $this->_check_options();
						}
						throw $e;
					}

					$ret = $result->getStatus() == 204;
				}
				break;

			case LOCK_SH:
			case LOCK_EX:
				$body = sprintf('<?xml version="1.0" encoding="utf-8" ?> 
<D:lockinfo xmlns:D="DAV:"> 
<D:lockscope><D:%s/></D:lockscope> 
<D:locktype><D:write/></D:locktype> 
<D:owner>%s</D:owner> 
</D:lockinfo>', ($mode & LOCK_SH) ? "shared" : "exclusive", get_class($this)); // TODO better owner string - inherited from initial commit
				try {
					$req = $this->_startRequest('LOCK');
					if (is_string($this->user)) {
						$req->setAuth($this->user, $this->pass);
					}
					if ($this->locktoken) { // needed for refreshing a lock
						$req->setHeader("Lock-Token", "<{$this->locktoken}>");
					}
					$req->setHeader("Timeout", "Infinite, Second-4100000000");
					$req->setHeader("Content-Type", 'text/xml; charset="utf-8"');
					$req->setBody($body);
					$result = $req->send();
				} catch (Exception $e) {
					if (preg_match("/Malformed response: /i", $e->getMessage(), $matches)) {
						return $this->_check_options();
					}
					throw $e;
				}

				$ret = $result->getStatus() == 200;

				if ($ret) {
					$propinfo = new HTTP_WebDAV_Client_parse_lock_response($result->getBody());
					$this->locktoken = $propinfo->locktoken;
					// TODO deal with timeout - inherited from initial commit
				}
				break;
				
			default:
				break;
		}

		return $ret;
	}

	private function _startRequest($method) {
		$req = new HTTP_Request2($this->url);

		// We need to set this to fix a bug in an old nginx as  it sends a response body for HEAD requests which is a violation of RFC 2616 and fixed in newer nginx versions (https://pear.php.net/bugs/bug.php?id=20227)
		$req->setHeader('Accept-Encoding', 'identity');
		$req->setHeader('User-agent', $this->userAgent);
		$req->setHeader('Content-type', $this->contentType);

		$req->setMethod($method);

		return $req;
	}

	private function filesize($url) {
		$this->connection_open($url);

		if (isset($this->stat['size'])) {
			return intval($this->stat['size']);
		}
	}
}

// Do *not* instantiate here; it is a storage module, so is instantiated on-demand
// $updraftplus_addons_webdav = new UpdraftPlus_Addons_RemoteStorage_webdav;
