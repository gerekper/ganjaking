<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: backblaze:Backblaze Support
Description: Backblaze Support
Version: 1.3
Shop: /shop/backblaze/
Include: includes/backblaze
IncludePHP: methods/addon-base-v2.php
RequiresPHP: 5.3.3
Latest Change: 1.15.3
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');
/**
 * Possible enhancements:
 * - Investigate porting to WP HTTP API so that curl is not required
 */
class UpdraftPlus_Addons_RemoteStorage_backblaze extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	private $_large_file_id;
	
	private $_sha1_of_parts;
	
	private $_uploaded_size;
	
	private $chunk_size = 5242880;

	/**
	 * Constructor
	 */
	public function __construct() {
		// 3rd parameter: chunking? 4th: Test button?
		parent::__construct('backblaze', 'Backblaze B2', true, true);
		// Set it any lower, any you will get an error when calling /b2_finish_large_file upon finishing: 400, Message: Part number 1 is smaller than 5000000 bytes"
		if (defined('UPDRAFTPLUS_UPLOAD_CHUNKSIZE') && UPDRAFTPLUS_UPLOAD_CHUNKSIZE > 0) $this->chunk_size = max(UPDRAFTPLUS_UPLOAD_CHUNKSIZE, 5000000);
	}
	
	/**
	 * Upload a single file
	 *
	 * @param String $file		 - the basename of the file to upload
	 * @param String $local_path - the full path of the file
	 *
	 * @return Boolean - success status. Failures can also be thrown as exceptions.
	 */
	public function do_upload($file, $local_path) {
	
		global $updraftplus;

		$opts = $this->options;
		$storage = $this->get_storage();

		if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
		if (!is_object($storage)) throw new Exception("Backblaze service error (got a ".gettype($storage).")");
		
		$backup_path = empty($opts['backup_path']) ? '' : trailingslashit($opts['backup_path']);
		$remote_path = $backup_path.$file;
		
		$file_hash = md5($file);
		$this->_uploaded_size = $this->jobdata_get('total_bytes_sent_'.$file_hash, 0);
		
		if (!file_exists($local_path) || !is_readable($local_path)) throw new Exception("Could not read file: $local_path");
		
		$bucket_name = $opts['bucket_name'];
		// Create bucket if bucket doesn't exists
		if (!isset($this->is_upload_bucket_exist) && $this->is_valid_bucket_name($bucket_name)) {
			$buckets = $this->get_bucket_names_array();
			if (!in_array($bucket_name, $buckets)) {
				$new_bucket_created = $storage->createPrivateBucket($bucket_name);
				if ($new_bucket_created) {
					$this->is_upload_bucket_exist = true;
					$this->log("bucket was not found, but a new private bucket has now been created: ".$bucket_name);
				} else {
					$this->log("bucket was not found, and creation of a new private bucket failed: ".$bucket_name);
				}
			} else {
				$this->is_upload_bucket_exist = true;
			}
		}
		
		if (1 === ($ret = $updraftplus->chunked_upload($this, $file, "backblaze://".trailingslashit($bucket_name).$backup_path.$file, 'Backblaze', $this->chunk_size, $this->_uploaded_size))) {
		
			$result = $storage->upload(array(
				'BucketName' => $opts['bucket_name'],
				'FileName'   => $remote_path,
				'Body'	   => file_get_contents($local_path),
			));
			
			if (is_object($result) && is_callable(array($result, 'getSize')) && $result->getSize() > 1) {
				$ret = true;
			} else {
				$ret = false;
				$this->log("all-in-one upload fail: ".serialize($result));
			}
			
		}
		
		return $ret;

	}

	/**
	 * N.B. If we ever use varying-size chunks, we must be careful as to what we do with $chunk_index
	 *
	 * @param String		  $file 		   - Basename for the file being uploaded
	 * @param Resource|String $fp 			   - Data to send, or a file handle to read upload data from
	 * @param Integer		  $chunk_index 	   - Index of chunked upload
	 * @param Integer		  $upload_size 	   - Size of the upload, in bytes (this and the next are only used if a resource was given for $fp)
	 * @param Integer		  $upload_start	   - How many bytes into the file the upload process has got
	 * @param Integer		  $upload_end	   - How many bytes into the file we will be after this chunk is uploaded (not currently used)
	 * @param Integer		  $total_file_size - Total file size (not currently used)
	 *
	 * @return Boolean|WP_Error
	 */
	public function chunked_upload($file, $fp, $chunk_index, $upload_size = 0, $upload_start = 0, $upload_end = 0, $total_file_size = 0) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
	
		// Already done? This is not checked if we are sent data directly, as that implies forcing.
		if (is_resource($fp) && $upload_start < $this->_uploaded_size) return 1;

		$storage = $this->get_storage();
		if (is_wp_error($storage)) return $storage;
		if (!is_object($storage)) return new WP_Error('no_backblaze_service', "Backblaze service error (got a ".gettype($storage).")");
		
		$file_hash = md5($file);

		$upload_state = $this->jobdata_get('upload_state_'.$file_hash, array());
		// An upload URL is valid for 24 hours. But, we'll only use them for 1 hour, in case something else happens to invalidate it (we don't want to wait a whole day before getting a new one).
		if (!empty($upload_state['saved_at']) && $upload_state['saved_at'] < time() - 3600) $upload_state = array();
		
		$large_file_id = empty($upload_state['large_file_id']) ? false : $upload_state['large_file_id'];
		$upload_url = empty($upload_state['upload_url']) ? false : $upload_state['upload_url'];
		$auth_token = empty($upload_state['auth_token']) ? false : $upload_state['auth_token'];
		$need_new_state = ($large_file_id && $upload_url && $auth_token) ? false : true;
		
		$opts = $this->options;
		$backup_path = empty($opts['backup_path']) ? '' : trailingslashit($opts['backup_path']);
		$remote_path = $backup_path.$file;

		if (!$large_file_id) {
			$this->log("initiating multi-part upload");
			try {
				$response = $storage->uploadLargeStart(array(
					'FileName'   => $remote_path,
					'BucketName' => $opts['bucket_name'],
				));

				if (empty($response['fileId'])) {
					$this->log('Unexpected response to uploadLargeStart: '.serialize($response));
					return false;
				}

			} catch (Exception $e) {
				$this->log('Unexpected chunk uploading exception ('.get_class($e).'): '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
				return false;
			}

			$large_file_id = $response['fileId'];

		}

		$this->_large_file_id = $large_file_id;
		
		if (!$upload_url || !$auth_token) {
			try {
				$this->log("requesting multi-part file upload url (id $large_file_id)");
				$response = $storage->uploadLargeUrl(array(
					'FileId' => $large_file_id,
				));
				if (empty($response['authorizationToken']) || empty($response['uploadUrl'])) {
					$this->log('Unexpected response to uploadLargeUrl: '.serialize($response));
					return false;
				}

			} catch (Exception $e) {
				$this->log('Unexpected error when getting upload URL ('.get_class($e).'): '.$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
				return false;
			}
			$auth_token = $response['authorizationToken'];
			$upload_url = $response['uploadUrl'];
		}
		
		if ($need_new_state) {
			$this->jobdata_set('upload_state_'.$file_hash, array(
				'large_file_id' => $large_file_id,
				'upload_url' => $upload_url,
				'auth_token' => $auth_token,
				// N.B. An upload URL is valid for 24 hours
				'saved_at' => time()
			));
		}
		
		if (is_resource($fp)) {
			if (false === ($data = fread($fp, $upload_size))) {
				$this->log(__('Error: unexpected file read fail', 'updraftplus'), 'error');
				$this->log("File read fail (fread() returned false)");
				return false;
			}
		} elseif (is_string($fp)) {
			$data = $fp;
		} else {
			return new WP_Error('backblaze_chunk_data_error', __('Error:', 'updraftplus')." backblaze::chunked_upload() received invalid input");
		}
		
		$sha1_of_parts = $this->jobdata_get('sha1_of_parts_'.$file_hash, array());
		$sha1_of_parts[$chunk_index - 1] = sha1($data);

		try {
			$response = $storage->uploadLargePart(array(
				'AuthorizationToken' => $auth_token,
				'FilePartNo' => $chunk_index,
				'UploadUrl' => $upload_url,
				'Body' => $data,
			));
			if (!is_array($response) || !isset($response['partNumber'])) {
				$this->log("Unexpected response to uploadLargePart: ".serialize($response));
				return false;
			}
		} catch (Exception $e) {
			if ($e->getCode() >= 500 && $e->getCode() <= 599) {
				$this->jobdata_set('upload_state_'.$file_hash, array(
					'large_file_id' => $large_file_id,
					'upload_url' => '',
					'auth_token' => '',
				));
			}
			return new WP_Error('backblaze_chunk_upload_error', __('Error:', 'updraftplus')." {$e->getCode()}, Message: {$e->getMessage()}");
		}
		
		$this->_sha1_of_parts = $sha1_of_parts;
		$this->jobdata_set('sha1_of_parts_'.$file_hash, $sha1_of_parts);

		$this->jobdata_set('total_bytes_sent_'.$file_hash, $upload_end + 1);
		
		return true;
	}

	/**
	 * Called when all chunks have been uploaded, to allow any required finishing actions to be carried out
	 *
	 * @param String $file - the basename of the file being uploaded
	 *
	 * @return Integer|Boolean - success or failure state of any finishing actions
	 */
	public function chunked_upload_finish($file) {

		$file_hash = md5($file);
		
		$storage = $this->get_storage();
		
		// This happens if chunked_upload_finish is called without chunked_upload having been called
		if (empty($this->_large_file_id)) {
		
			$upload_state = $this->jobdata_get('upload_state_'.$file_hash, array());
			
			// An upload URL is valid for 24 hours. But, we'll only use them for 1 hour, in case something else happens to invalidate it (we don't want to wait a whole day before getting a new one).
			if (!empty($upload_state['saved_at']) && $upload_state['saved_at'] < time() - 3600) $upload_state = array();
			
			$this->_large_file_id = empty($upload_state['large_file_id']) ? false : $upload_state['large_file_id'];
			
			$this->_sha1_of_parts = $this->jobdata_get('sha1_of_parts_'.$file_hash, array());
			
		}

		try {
			$response = $storage->uploadLargeFinish(array(
				'FileId' => $this->_large_file_id,
				'FilePartSha1Array' => $this->_sha1_of_parts,
			));
		} catch (Exception $e) {
			global $updraftplus;
			
			if (preg_match('/No active upload for: .*/', $e->getMessage())) {
				$this->log("upload: b2_finish_large_file has already been called ('".$e->getMessage()."')");
				return 1;
			} elseif (preg_match('/Part number (\d+) has not been uploaded/i', $e->getMessage(), $matches)) {
				$missing_chunk_index = $matches[1];
				$this->log("Exception in uploadLargeFinish(); will retry part $missing_chunk_index: {$e->getCode()}, Message: {$e->getMessage()} (line: {$e->getLine()}, file: {$e->getFile()})");
				$updraft_dir = $updraftplus->backups_dir_location();
				
				// If more than this are needed, they will happen on the next resumption
				static $retries = 12;
				
				if (false === ($data = file_get_contents($updraft_dir.'/'.$file, false, null, ($missing_chunk_index - 1 ) * $this->chunk_size, $this->chunk_size))) {
					$retry_part = new WP_Error('file_read_failed', "Could not read: $file");
				} elseif ($retries > 0) {
					$retries--;
					$retry_part = $this->chunked_upload($file, $data, $missing_chunk_index);
					// Missing part was uploaded; try the whole again
					if (true === $retry_part) {
						return $this->chunked_upload_finish($file);
					}
					// N.B. chunked_upload() does its own logging when returning false
				}
				
				if (is_wp_error($retry_part)) {
					$this->log("Failed ".$retry_part->get_error_code().": ".$retry_part->get_error_message());
				}
			} else {
				$this->log("Exception in uploadLargeFinish(): {$e->getCode()}, Message: {$e->getMessage()} (line: {$e->getLine()}, file: {$e->getFile()})");
			}
			return false;
		}
		
		global $updraftplus;
		$this->log('upload: success (b2_finish_large_file called successfully; chunks='.count($this->_sha1_of_parts).', file ID returned='.$response->getId().', size='.$response->getSize().')');

		// Clean-up
		$this->jobdata_delete('upload_state_'.$file_hash);
		$this->jobdata_delete('sha1_of_parts_'.$file_hash);

		// (int)1 means 'we already logged', as opposed to (boolean)true which does not
		return 1;
	}

	/**
	 * Perform a download of the requested file
	 *
	 * @param String  $file	  		- the file (basename) to download
	 * @param String  $fullpath		- the full path to download it too
	 * @param Integer $start_offset - byte marker to begin at (starting from 0)
	 *
	 * @return Boolean|Integer - success/failure, or a byte counter of how much has been downloaded. Exceptions can also be thrown for errors.
	 */
	public function do_download($file, $fullpath) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		global $updraftplus;

		$remote_files = $this->do_listfiles($file);

		if (is_wp_error($remote_files)) {
			throw new Exception('Download error ('.$remote_files->get_error_code().'): '.$remote_files->get_error_message());
		}

		foreach ($remote_files as $file_info) {
			if ($file_info['name'] == $file) {
				return $updraftplus->chunked_download($file, $this, $file_info['size'], true, null, 2*1048576);
			}
		}
		
		$this->log("$file: file not found in listing of remote directory");
		
		return false;

	}
	
	/**
	 * Callback used by by chunked downloading API
	 *
	 * @param String $file	  - the file (basename) to be downloaded
	 * @param Array	 $headers - supplied headers
	 * @return String - the data downloaded
	 */
	public function chunked_download($file, $headers) {

		// $curl_options = array();
		// if (is_array($headers) && !empty($headers['Range']) && preg_match('/bytes=(.*)$/', $headers['Range'], $matches)) {
		// $curl_options[CURLOPT_RANGE] = $matches[1];

		$opts = $this->options;
		$storage = $this->get_storage();

		$backup_path = empty($opts['backup_path']) ? '' : trailingslashit($opts['backup_path']);
		
		$options = array(
			'BucketName' => $opts['bucket_name'],
			'FileName'   => $backup_path.$file,
		);
		
		if (!empty($headers)) $options['headers'] = $headers;
		
		$remote_file = $storage->download($options);

		return is_string($remote_file) ? $remote_file : false;

	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param Array $settings - pre-filtered settings
	 *
	 * @return Array filtered settings
	 */
	public function options_filter($settings) {
		if (is_array($settings) && !empty($settings['version']) && !empty($settings['settings'])) {
			foreach ($settings['settings'] as $instance_id => $instance_settings) {
				if (!empty($instance_settings['backup_path'])) {
					$settings['settings'][$instance_id]['backup_path'] = trim($instance_settings['backup_path'], "/ \t\n\r\0\x0B");
				}
			}
		}
		return $settings;
	}
	
	/**
	 * Delete an indicated file from remote storage
	 *
	 * @param Array $files - the files (basename) to delete
	 *
	 * @return Boolean|Array - success/failure status of the delete operation. Throwing exception is also permitted.
	 */
	public function do_delete($files) {
	
		$opts = $this->options;

		$storage = $this->get_storage();

		$backup_path = empty($opts['backup_path']) ? '' : trailingslashit($opts['backup_path']);
		
		try {
			if (count($files) > 1) {
				$multipleFiles = array();
				foreach ($files as $file) {
					$multipleFiles[] = array(
						'FileName'   => $backup_path.$file,
						'BucketName' => $opts['bucket_name']
					);
				}
				$result = $storage->deleteMultipleFiles($multipleFiles, $opts['bucket_name'], $backup_path);
			} else {
				$fileName = $files[0];
				$result = $storage->deleteFile(array(
					'FileName'   => $backup_path.$fileName,
					'BucketName' => $opts['bucket_name'],
				));
			}
		} catch (UpdraftPlus_Backblaze_NotFoundException $e) {
			// This exception should only be possible on the single file delete path
			$this->log("$fileName: file not found (so likely already deleted)");
			return true;
		}

		return $result;
		
	}

	/**
	 * This method is used to get a list of backup files for the remote storage option
	 *
	 * @param  String $match - a string to match when looking for files
	 *
	 * @return Array|WP_Error - returns an array of files (arrays with keys 'name' (basename) and (optional) 'size' (in bytes)) or a WordPress error. Throwing an exception is also allowed.
	 */
	public function do_listfiles($match = 'backup_') {
		$opts = $this->get_options();
		$storage = $this->get_storage();
		
		// When listing, paths in the root must not begin with a slash
		$backup_path = empty($opts['backup_path']) ? '' : trailingslashit($opts['backup_path']);

		try {
			$remote_files = $storage->listFiles(array(
				'BucketName' => $opts['bucket_name'],
				'Prefix' => $backup_path.$match
			));
		} catch (Exception $e) {
			return new WP_Error('backblaze_list_error', $e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
		}

		if (is_wp_error($remote_files)) return $remote_files;
		
		$files = array();

		foreach ($remote_files as $file) {
			$file_name = $file->getName();
			if ($backup_path && 0 !== strpos($file_name, $backup_path)) continue;
			$files[] = array(
				'name' => substr($file_name, strlen($backup_path)),
				'size' => $file->getSize(),
				// 'fid'  => $file->getId(),
			);
		}

		return $files;
		
	}

	/**
	 * Get a list of parameters required to be present for a credential tests, plus descriptions
	 *
	 * @return Array
	 */
	public function get_credentials_test_required_parameters() {
		return array(
			'account_id' => __('Account ID', 'updraftplus'),
			'key' => __('Account Key', 'updraftplus')
		);
	}
	
	/**
	 * Run a credentials test. Output can be echoed.
	 *
	 * @param String $testfile		  - basename to use for the test
	 * @param Array  $posted_settings - settings to use
	 *
	 * @return Array - 'result' indicating a success/failure status, and 'data' with returned data
	 */
	protected function do_credentials_test($testfile, $posted_settings = array()) {

		$bucket_name = $posted_settings['bucket_name'];
		
		$result = false;
		$data = null;
		$storage = $this->get_storage();
		
		try {
			if (!$this->is_valid_bucket_name($bucket_name)) {
				echo __('Invalid bucket name', 'updraftplus')."\n";
			} else {
				$buckets = $this->get_bucket_names_array();
				$new_bucket_created = false;
				if (!in_array($bucket_name, $buckets)) {
					 $new_bucket_created = $storage->createPrivateBucket($bucket_name);
				}
				
				if (in_array($bucket_name, $buckets) || $new_bucket_created) {
					$backup_path = empty($posted_settings['backup_path']) ? '' : trailingslashit($posted_settings['backup_path']);
	
					// Now try to write
					$result = $storage->upload(array(
						'BucketName' => $bucket_name,
						'FileName'   => $backup_path.$testfile,
						'Body'	   => 'This is a test file resulting from pressing the "Test" button in UpdraftPlus, https://updraftplus.com. If it is still here afterwards, then something went wrong deleting it - you should delete it manually.',
					));
					
					if (is_object($result) && is_callable(array($result, 'getSize')) && $result->getSize() > 1) {
						$result = true;
					}
				} elseif (!$new_bucket_created) {
					printf(__("Failure: We could not successfully access or create such a bucket. Please check your access credentials, and if those are correct then try another bucket name (as another %s user may already have taken your name).", 'updraftplus'), 'Backblaze');
				}
			}
		} catch (Exception $e) {
			echo get_class($e).': '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().")\n";
		}

		return array('result' => $result, 'data' => $data);
		
	}
	
	/**
	 * Delete a temporary file use for a credentials test. Output can be echo-ed.
	 *
	 * @param String $testfile		  - the basename of the file to delete
	 * @param Array  $posted_settings - the settings to use
	 *
	 * @return void
	 */
	protected function do_credentials_test_deletefile($testfile, $posted_settings) {
		
		try {
			$backup_path = empty($posted_settings['backup_path']) ? '' : trailingslashit($posted_settings['backup_path']);
			$storage = $this->get_storage();
		
			$storage->deleteFile(array(
				'FileName'   => $backup_path.$testfile,
				'BucketName' => $posted_settings['bucket_name'],
			));

		} catch (Exception $e) {
			echo __('Delete failed:', 'updraftplus').' '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
		}
	}

	/**
	 * Retrieve a list of supported features for this storage method
	 * This method should be over-ridden by methods supporting new
	 * features.
	 *
	 * @see UpdraftPlus_BackupModule::get_supported_features()
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic', 'multi_delete');
	}
	
	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'account_id' => '',
			'key' => '',
			'bucket_name' => '',
			'backup_path' => '',
			'single_bucket_key_id' => '',
		);
	}
	
	/**
	 * Perform any boot-strapping functions, and return a client instance
	 *
	 * @param Array	  $opts	   - instance options
	 * @param Boolean $connect - whether to also set up a connection (if supported by this method)
	 *
	 * @return UpdraftPlus_Backblaze_CurlClient|WP_Error - the storage object. It should also be stored as $this->storage.
	 */
	public function do_bootstrap($opts) {
		$storage = $this->get_storage();

		if (!empty($storage) && !is_wp_error($storage)) return $storage;
		
		try {

			if (!is_array($opts)) $opts = $this->get_options();
	
			if (!class_exists('UpdraftPlus_Backblaze_CurlClient')) include_once UPDRAFTPLUS_DIR.'/includes/Backblaze/CurlClient.php';

			if (empty($opts['account_id']) || empty($opts['key'])) return new WP_Error('no_settings', __('No settings were found', 'updraftplus').' (Backblaze)');
			
			$backblaze_options = array(
				'ssl_verify' => empty($opts['disableverify']),
				'ssl_ca_certs' => empty($opts['useservercerts']) ? UPDRAFTPLUS_DIR.'/includes/cacert.pem' : false
			);
			
			$storage = new UpdraftPlus_Backblaze_CurlClient($opts['account_id'], $opts['key'], $opts['single_bucket_key_id'], $backblaze_options);

			$this->set_storage($storage);
			
		} catch (Exception $e) {
			return new WP_Error('blob_service_failed', 'Error when attempting to setup Backblaze access (please check your credentials): '.$e->getMessage().' ('.$e->getCode().', '.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')');
		}

		return $storage;

	}
	
	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && !empty($opts['account_id']) && !empty($opts['key'])) return true;
		return false;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		?>
		<tr class="{{get_template_css_classes false}} backblaze_pre_config_container">
			<td colspan="2">
				<img width="434" src="{{storage_image_url}}"><br>
				{{{curl_existence_label}}}
				<p><a href="https://updraftplus.com/support/configuring-backblaze-cloud-storage-access-in-updraftplus/" target="_blank"><strong>{{configuration_helper_link_text}}</strong></a></p>
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
			<th>{{input_key_id_label}}:</th>
			<td><input type="text" size="40" data-updraft_settings_test="account_id" id="{{get_template_input_attribute_value "id" "account_id"}}" name="{{get_template_input_attribute_value "name" "account_id"}}" value="{{account_id}}"><br>
			<em>{{{input_key_id_title}}}</em><br>
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_application_key_label}}:</th>
			<td><input type="{{input_application_key_type}}" size="40" data-updraft_settings_test="key" id="{{get_template_input_attribute_value "id" "key"}}" name="{{get_template_input_attribute_value "name" "key"}}" value="{{key}}" /></td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_bucket_key_id_label}}:</th>
			<td><input title="{{input_bucket_key_id_title}}" type="text" size="40" data-updraft_settings_test="single_bucket_key_id" id="{{get_template_input_attribute_value "id" "single_bucket_key_id"}}" name="{{get_template_input_attribute_value "name" "single_bucket_key_id"}}" value="{{single_bucket_key_id}}"><br>
			<em>{{input_bucket_key_id_title}}</em></a><br>
			</td>
		</tr>

		<tr class="{{get_template_css_classes true}}">
			<th>{{input_backup_path_label}}:</th>
			<td>/<input type="text" size="19" maxlength="50" placeholder="{{input_backup_path_name_placeholder}}" data-updraft_settings_test="bucket_name" id="{{get_template_input_attribute_value "id" "bucket_name"}}" name="{{get_template_input_attribute_value "name" "bucket_name"}}" value="{{bucket_name}}" />/<input type="text" size="19" maxlength="200" placeholder="{{input_backup_path_some_path_placeholder}}" data-updraft_settings_test="backup_path" id="{{get_template_input_attribute_value "id" "backup_path"}}" name="{{get_template_input_attribute_value "name" "backup_path"}}" value="{{backup_path}}" /><br>
			<em>{{{input_backup_path_title}}}</em><br>
			</td>
		</tr>
		
		{{{get_template_test_button_html "Backblaze"}}}
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
		global $updraftplus, $updraftplus_admin;
		$properties = array(
			'storage_image_url' => UPDRAFTPLUS_URL.'/images/backblaze.png',
			'curl_existence_label' => wp_kses($updraftplus_admin->curl_check('Backblaze B2', false, 'backblaze hidden-in-updraftcentral', false), $this->allowed_html_for_content_sanitisation()),
			'configuration_helper_link_text' => sprintf(__('For help configuring %s, including screenshots, follow this link.', 'updraftplus'), 'Backblaze'),
			'input_key_id_label' => __('Master Application Key ID', 'updraftplus'),
			'input_key_id_title' => sprintf(__('Get these settings from %s, or sign up %s.', 'updraftplus'), '<a aria-label="secure.backblaze.com/b2_buckets.htm" target="_blank" href="https://secure.backblaze.com/b2_buckets.htm">'.__('here', 'updraftplus').'</a>', '<a aria-label="www.backblaze.com/b2/" target="_blank" href="https://www.backblaze.com/b2/">'.__('here', 'updraftplus').'</a>'),
			'input_application_key_label' => __('Application key', 'updraftplus'),
			'input_application_key_type' => apply_filters('updraftplus_admin_secret_field_type', 'password'),
			'input_bucket_key_id_label' => __('Bucket application key ID', 'updraftplus'),
			'input_bucket_key_id_title' => __('This is needed if, and only if, your application key was a bucket-specific application key (not a master key)', 'updraftplus'),
			'input_backup_path_label' => __('Backup path', 'updraftplus'),
			'input_backup_path_name_placeholder' => __('Bucket name', 'updraftplus'),
			'input_backup_path_title' => '<a target="_blank" href="https://help.backblaze.com/hc/en-us/articles/217666908-What-you-need-to-know-about-B2-Bucket-names">'.__('There are limits upon which path-names are valid. Spaces are not allowed.', 'updraftplus').'</a>',
			'input_backup_path_some_path_placeholder' => __('some/path', 'updraftplus'),
			'input_test_label' => sprintf(__('Test %s Settings', 'updraftplus'), $updraftplus->backup_methods[$this->get_id()]),
		);
		return wp_parse_args($properties, $this->get_persistent_variables_and_methods());
	}
	
	/**
	 * Get bucket name list array for current storage instance
	 *
	 * @return array Which contains bucket names as element values
	 */
	protected function get_bucket_names_array() {
		$bucket_names = array();
		$storage = $this->get_storage();
		$buckets = $storage->listBuckets();
		if (is_array($buckets)) {
			foreach ($buckets as $bucket) {
				$bucket_names[] = $bucket->getName();
			}
		}
		return $bucket_names;
	}
	
	/**
	 * Checks whether bucket name is valid as per backblaze standards
	 *
	 * @param string $bucket_name Backblaze bucket name
	 * @return boolean If bucket name is valid, it returns true. Otherwise false
	 */
	protected function is_valid_bucket_name($bucket_name) {
		return preg_match('/^(?!b2-)[-0-9a-z]{6,50}$/i', $bucket_name);
	}
}
