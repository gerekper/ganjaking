<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: onedrive:Microsoft OneDrive Support
Description: Microsoft OneDrive Support
Version: 1.9
Shop: /shop/onedrive/
Include: includes/onedrive
IncludePHP: methods/addon-base-v2.php
RequiresPHP: 5.3.3
Latest Change: 1.14.13
*/
// @codingStandardsIgnoreEnd

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

// Converted to multi-options (Feb 2017-) and previous options conversion removed: Yes

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');
if (!class_exists('UpdraftPlus_OneDrive_Account')) require_once(UPDRAFTPLUS_DIR.'/includes/class-onedrive-account.php');

class UpdraftPlus_Addons_RemoteStorage_onedrive extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	// https://dev.onedrive.com/items/upload_large_files.htm says "Use a fragment size that is a multiple of 320 KB"
	private $chunk_size = 3276800;

	private $the_callback;

	private $the_client_id;

	/**
	 * German Azure Active Directory client id
	 *
	 * @var string
	 */
	private $the_germany_client_id;

	/**
	 * Constructor
	 */
	public function __construct() {
	
		$this->the_client_id = defined('UPDRAFTPLUS_ONEDRIVE_CLIENT_ID') ? UPDRAFTPLUS_ONEDRIVE_CLIENT_ID : '276d9423-7d0c-41be-a3e1-4cdad89dc36f';
		// To do: Add Germany AAD app client id here
		$this->the_germany_client_id = defined('UPDRAFTPLUS_ONEDRIVE_GERMANY_CLIENT_ID') ? UPDRAFTPLUS_ONEDRIVE_GERMANY_CLIENT_ID : '7cc3beb4-daab-4a59-b091-c4c2319d8d2d';
		$this->the_callback = defined('UPDRAFTPLUS_ONEDRIVE_CALLBACK_URL') ? UPDRAFTPLUS_ONEDRIVE_CALLBACK_URL : 'https://auth.updraftplus.com/auth/onedrive';
	
		// 3rd parameter: chunking? 4th: Test button?
		parent::__construct('onedrive', 'OneDrive', false, false);

		if (defined('UPDRAFTPLUS_UPLOAD_CHUNKSIZE') && UPDRAFTPLUS_UPLOAD_CHUNKSIZE>0) $this->chunk_size = max(UPDRAFTPLUS_UPLOAD_CHUNKSIZE, 320*1024);
	}
	
	public function do_upload($file, $from) {

		global $updraftplus;
		$opts = $this->get_options();
		
		$message = " did not return the expected data";
		
		if (!function_exists("curl_init") || !function_exists('curl_exec')) {
			$this->log('The required Curl PHP module is not installed. This upload will abort');
			$this->log(sprintf(__('The required %s PHP module is not installed - ask your web hosting company to enable it.', 'updraftplus'), 'Curl'), 'error');
			return false;
		}

		$endpoint_name = isset($opts['endpoint_tld']) && 'de' == $opts['endpoint_tld'] ? 'OneDrive Germany' : 'OneDrive International';
		
		if ($this->use_msgraph_api($opts)) {
			$this->log("begin cloud upload to {$endpoint_name} (using Microsoft Graph API)");
		} else {
			$this->log("begin cloud upload {$endpoint_name} (using Live SDK API)");
		}
		
		// If the user is using OneDrive for Germany option
		if (isset($opts['endpoint_tld']) && 'de' === $opts['endpoint_tld']) {
			$odg_warning = sprintf(__('Due to the shutdown of the %1$s endpoint, support for %1$s will be ending soon. You will need to migrate to the Global endpoint in your UpdraftPlus settings. For more information, please see: %2$s', 'updraftplus'), 'OneDrive Germany', 'https://www.microsoft.com/en-us/cloud-platform/germany-cloud-regions');
			// We only want to log this once per backup job
			$this->log($odg_warning, 'warning', 'onedrive_de_migrate');
		}

		try {
			$storage = $this->bootstrap();
			if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
			if (!is_object($storage)) throw new Exception("OneDrive service error");
		} catch (Exception $e) {
			$message = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			$this->log("service error: ".$message);
			$this->log($message, 'error');
			return false;
		}
		
		$folder = empty($opts['folder']) ? '' : $opts['folder'];

		$filesize = filesize($from);
		$this->onedrive_file_size = $filesize;

		try {
			// Check if enough storage space in quota
			$quota = $storage->fetchQuota();
			
			if (!is_object($quota)) {
			
				$this->log("quota fetching failed; object returned was a: ".gettype($quota));
			
			} else {
			
				$total = $quota->total;
				$available = $quota->remaining;

				if (is_numeric($total) && is_numeric($available)) {
					$used = $total - $available;
					$used_perc = $total ? round($used*100/$total, 1) : 'n/a';
					$message = sprintf('Your %s quota usage: %s %% used, %s available', 'OneDrive', $used_perc, round($available/1048576, 1).' MB');
				}

				if (isset($available) && -1 != $available && $available < $filesize) {
					$this->log("File upload expected to fail: file data remaining to upload ($file) size is ".($filesize)." b (overall file size; $filesize b), whereas available quota is only $available b");
					$this->log(sprintf(__("Account full: your %s account has only %d bytes left, but the file to be uploaded has %d bytes remaining (total size: %d bytes)", 'updraftplus'), 'OneDrive', $available, $filesize, $filesize), 'warning', 'onedrive_expect_to_fail');
				}
			}
			
		} catch (Exception $e) {
			$message .= " ".get_class($e).": ".$e->getMessage();
		}

		$this->log($message.'. Upload folder: '.$folder);
		
		// Ensure directory exists
		$pointer = $this->get_pointer($folder, $storage);

		// Perhaps it already exists? (if we didn't get the final confirmation)
		try {
			$items = $storage->fetchObjects($pointer);
			foreach ($items as $item) {
				if ($file == $item->getName()) {
					if ($item->getSize() >= $filesize) {
						$this->log("$file: already uploaded");
						return true;
					} else {
						$this->log("$file: partially uploaded (".$item->getSize()." < $filesize)");
					}
				}
			}
		} catch (Exception $e) {
		
			$file_check_msg = "file check: exception: ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')';
			
			/*
			02/08/2018: There's no obvious documentation, but this error code appears to be a token expiry: https://github.com/microsoftgraph/python-sample-auth/issues/10 .
			24/06/2020: "Access token has expired." has been returned on a customer site
			It would be nicer to request a new token immediately, but a swift resumption is not bad.
			*/
			if ((false !== stripos($file_check_msg, 'token') && false !== strpos($file_check_msg, '80049228')) || (false !== stripos($file_check_msg, 'Access token has expired.'))) {
				$this->log($file_check_msg.' - matches a token expiry pattern; will schedule a resumption and terminate for now');
				UpdraftPlus_Job_Scheduler::reschedule(60);
				UpdraftPlus_Job_Scheduler::record_still_alive();
				die;
			}
		
			$this->log($file_check_msg);
			
		}

		try {
			if (false != ($handle = fopen($from, 'rb'))) {
				if ($filesize < $this->chunk_size) {
					$storage->createFile($file, $pointer, $handle);
					fclose($handle);
				} else {
					// https://dev.onedrive.com/items/upload_large_files.htm
					$path = $folder ? $folder.'/'.$file : $file;
					
					// This is only used in a corner-case
					$this->onedrive_folder = $folder;
					
					$session_key = "sess_".md5($path);

					$possible_session = $this->jobdata_get($session_key, false, '1d_'.$session_key);
					
					if (is_object($possible_session) && !empty($possible_session->uploadUrl)) {
						$this->log("chunked upload: session appears to be underway/resumable; will attempt resumption");
						$session = $possible_session;
						
						$state = $storage->getState();
						$upload_status = $storage->apiGet($possible_session->uploadUrl, array());

						if (!is_object($upload_status) || empty($upload_status->nextExpectedRanges)) {
							// One retry
							$this->log("Failed to get upload status; making second attempt to request prior to re-starting");
							$upload_status = $storage->apiGet($possible_session->uploadUrl, array());
						}
						
						if (is_object($upload_status) && !empty($upload_status->nextExpectedRanges)) {
							if (is_array($upload_status->nextExpectedRanges)) {
								$next_expected = $upload_status->nextExpectedRanges[0];
							} else {
								$next_expected = $upload_status->nextExpectedRanges;
							}

							if (preg_match('/^(\d+)/', $next_expected, $matches)) {
								$uploaded_size = $matches[1];
								$this->log("Resuming OneDrive upload session from byte: $uploaded_size (".serialize($upload_status->nextExpectedRanges).")");
							} else {
								$this->log("Could not parse next expected range: ".serialize($upload_status->nextExpectedRanges));
							}
						} else {
							$clean_state = $state;
							if (is_object($state) && !empty($state->token->data->access_token)) $clean_state->token->data->access_token = substr($state->token->data->access_token, 0, 3).'...';
							$this->log("Failed to get upload status - will re-start this upload: service_state=".serialize($clean_state).",  upload_status=".serialize($upload_status));
							$this->jobdata_delete($session_key);
						}
					}

					if (!isset($uploaded_size)) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
						$uploaded_size = 0;
						if ($this->use_msgraph_api($opts)) {
							$endpoint_tld = isset($opts['endpoint_tld']) ? $opts['endpoint_tld'] : 'com';
							$graph_url = \Onedrive\UpdraftPlus_OneDrive_Account::$types[$endpoint_tld]['graph_url'];
							$session = $storage->apiPost($graph_url."/v1.0/me/drive/root:/". rawurlencode($path).':/createUploadSession');
						} else {
							$session = $storage->apiPost("https://api.onedrive.com/v1.0/drive/root:/". rawurlencode($path).':/upload.createSession');
						}
						if (!is_object($session) || empty($session->uploadUrl)) {
							throw new Exception("Failed to create upload session (".serialize($session).")");
						}
						$this->jobdata_set($session_key, $session);
					}

					$this->onedrive_session = $session;

					$this->onedrive_uploaded_size = $uploaded_size;

					$ret = $updraftplus->chunked_upload($this, $file, $this->method."://".$folder."/".$file, $this->description, $this->chunk_size, $uploaded_size, false);
					fclose($handle);
					
					// If chunked upload appears successful, clear the 'onedrive_expect_to_fail' warning
					if (true === $ret) {
						$updraftplus->log_remove_warning('onedrive_expect_to_fail');
					} else {
						$this->jobdata_delete($session_key);
					}
					return $ret;
				}
				
			} else {
				throw new Exception("Failed to open file for reading: $from");
			}
		} catch (Exception $e) {
			$this->log($this->description." upload: error: ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
			return false;
		}
		
		// At this point, the upload has suceeded despite expectation of failure
		$updraftplus->log_remove_warning('onedrive_expect_to_fail');
		return true;
	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $onedrive - An array of OneDrive options
	 * @return Array - the returned array can either be the set of updated OneDrive settings or a WordPress error array
	 */
	public function options_filter($onedrive) {

		// Get the current options (and possibly update them to the new format)
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('onedrive');

		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $onedrive;
		}

		if (!is_array($onedrive)) return $opts;

		// Remove instances that no longer exist
		if (!empty($opts['settings']) && is_array($opts['settings'])) {
			foreach ($opts['settings'] as $instance_id => $storage_options) {
				if (!isset($onedrive['settings'][$instance_id])) unset($opts['settings'][$instance_id]);
			}
		}

		if (empty($onedrive['settings'])) return $opts;
		
		foreach ($onedrive['settings'] as $instance_id => $storage_options) {
			$old_client_id = empty($opts['settings'][$instance_id]['clientid']) ? '' : $opts['settings'][$instance_id]['clientid'];
			$now_client_id = empty($storage_options['clientid']) ? '' : $storage_options['clientid'];
			if (!empty($opts['settings'][$instance_id]['refresh_token']) && $old_client_id != $now_client_id) {
				unset($opts['settings'][$instance_id]['refresh_token']);
				unset($opts['settings'][$instance_id]['tokensecret']);
				unset($opts['settings'][$instance_id]['ownername']);
			}
			
			foreach ($storage_options as $key => $value) {
				if ('folder' == $key) $value = trim(str_replace('\\', '/', $value), '/');
				$opts['settings'][$instance_id][$key] = ('clientid' == $key || 'secret' == $key) ? trim($value) : $value;
			}
		}
		return $opts;
	}
	
	public function chunked_upload($file, $fp, $chunk_index, $upload_size, $upload_start, $upload_end) {

		// Already done?
		if ($upload_start < $this->onedrive_uploaded_size) return 1;

		$storage = $this->get_storage();
		$opts = $this->get_options();
		
		$headers = array(
			"Content-Length: $upload_size",
			"Content-Range: bytes $upload_start-$upload_end/".$this->onedrive_file_size,
		);
		
		$empty_object = new stdClass;

		try {
			$put_chunk = $storage->apiPut($this->onedrive_session->uploadUrl, $fp, null, $upload_size, $headers);
		} catch (Exception $e) {
			$this->log($this->description." upload: exception (".get_class($e)."): ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
			
			// See HS#6320 and https://github.com/OneDrive/onedrive-api-docs/blob/master/items/upload_large_files.md#handle-commit-errors
			if (false !== strpos($e->getMessage(), 'Optimistic concurrency failure during fragmented upload') && 0 == $this->onedrive_uploaded_size) {
			
				try {
				
					// It can be the case that the item was completely uploaded, but that $this->onedrive_uploaded_size was zero
				
					$already_there_perhaps = $this->do_listfiles($file);
					foreach ($already_there_perhaps as $file_object) {
						// This test is quite conservative - there are other things we could do (if there's ever a need)
						if ($file_object['name'] == $file && !empty($file_object['size']) && $file_object['size'] > $this->onedrive_uploaded_size) {
							$this->onedrive_uploaded_size = $file_object['size'];
							if ($upload_start < $this->onedrive_uploaded_size) {
								$this->log("More of file ($upload_start) is uploaded than previous API call indicated ");
								return 1;
							}
						}
					}
				
					// Tried this with just $file - which is what the doc suggests - but OneDrive returned an error with: The name in the provided oneDrive.item does not match the name in the URL
					// Update: turned out that OneDrive's error was bogus; the upload was already complete
// $name = $this->onedrive_folder ? $this->onedrive_folder.'/'.$file : $file;
					$name = $file;
				
					$put_url = 'https://api.onedrive.'.$opts['endpoint_tld'].'/v1.0/drive/root:/'. urlencode($this->onedrive_folder);
				
					$this->log("Trying to PUT probably-completed upload to OneDrive: name=$name, PUT to: $put_url");
				
					$commit_body = json_encode(array(
						'name' => $name,
						'description' => null,
						'@name.conflictBehavior' => 'replace',
						'@content.sourceUrl' => $this->onedrive_session->uploadUrl
					));
					
					$commit_headers = array(
						"Content-Type: application/json",
						"Content-Length: ".strlen($commit_body),
					);
					
					$fp = fopen('php://temp', 'rw+b');

					if (!$fp) {
						$this->log('Trying to PUT probably-completed upload to OneDrive: failed to open php://temp');
						return false;
					}

					fwrite($fp, $commit_body);
					fseek($fp, 0);
					
					$commit = $storage->apiPut($put_url, $fp, null, strlen($commit_body), $commit_headers);
				
					if (is_object($commit) && (!empty($commit->expirationDateTime) || !empty($commit->id) || $commit === $empty_object)) {
						$this->log('Trying to PUT probably-completed upload to OneDrive: success');
						return true;
					}
					$this->log('Trying to PUT probably-completed upload to OneDrive: appears to have failed ('.gettype($commit).')');
					
				} catch (Exception $e) {
					$this->log('upload commit: exception ('.get_class($e)."): ($file) (".$e->getMessage().") (line: ".$e->getLine().', file: '.$e->getFile().')');
				}
			
			}
			
			return false;
		}

		// It seems we get an empty response object (but success - i.e. no exception thrown above) when a chunk was already previously uploaded
		if (is_object($put_chunk) && (!empty($put_chunk->expirationDateTime) || !empty($put_chunk->id) || $put_chunk === $empty_object)) return true;

		$this->log("Unexpected response when putting chunk $chunk_index: ".serialize($put_chunk));
		return false;

	}

	/**
	 * Get the OneDrive internal pointer for an indicated folder path
	 *
	 * @param String $folder  - folder path
	 * @param Object $storage - storage object that API calls can be made upon
	 *
	 * @return String - the pointer
	 */
	private function get_pointer($folder, $storage) {
		
		$pointer = null;
		try {
			$folder_array = explode('/', $folder);
			
			// Check if folder exists
			foreach ($folder_array as $val) {
				if ('' == $val) break; // If value is root break;
				
				$new_pointer = $pointer;
				
				// Fetch objects in dir
				$dirs = $storage->fetchObjects($pointer);
				foreach ($dirs as $dir) {
					$dirname = $dir->getName();
					if (strtolower($dirname) == strtolower($val) && $dir->isFolder()) {
						$new_pointer = $dir->getId();
						break; // This folder exists, we want to select this
					}
				}
				
				// If new_pointer is same, path doesn't exist, so create it
				if ($pointer == $new_pointer) {
					$newdir = $storage->createFolder($val, $pointer);
					$new_pointer = $newdir->getId();
				}
				$pointer = $new_pointer;
				
			}
			return $pointer;
		} catch (Exception $e) {
			$this->log("get_pointer($folder) exception: backup may not go into desired folder: ".$e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')');
			return $pointer;
		}
	}

	public function do_download($file) {

		global $updraftplus;
		$opts = $this->get_options();
		
		$message = " did not return the expected data";
		
		try {
			$storage = $this->bootstrap();
			if (!is_object($storage)) throw new Exception('OneDrive service error');
		} catch (Exception $e) {
			$message = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			$this->log($message);
			$this->log($message, 'error');
			return false;
		}
		
		$folder = $opts['folder'];
		$pointer = $this->get_pointer($folder, $storage);

		$objs = $storage->fetchObjects($pointer);
		foreach ($objs as $obj) {
			$obj_name = $obj->getName();
			if ($obj_name == $file && !$obj->isFolder()) {
				return $updraftplus->chunked_download($file, $this, $obj->getSize(), true, array($storage, $obj));
			}
		}

		$this->log("$file: ".sprintf("%s download: failed: file not found", 'OneDrive'));
		$this->log("$file: ".sprintf(__("%s download: failed: file not found", 'updraftplus'), 'OneDrive'), 'error');
		return false;

	}
	
	public function chunked_download($file, $headers, $data) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$file_obj = $data[1];

		$options = array();

		if (is_array($headers) && !empty($headers['Range']) && preg_match('/bytes=(.*)$/', $headers['Range'], $matches)) {
			$options[CURLOPT_RANGE] = $matches[1];
		}

		return $file_obj->fetchContent($options);

	}

	/**
	 * This function will use the OneDrive SDK to perform the deletion of the files passed in
	 *
	 * @param array $files - an array of files to delete
	 *
	 * @return boolean|array - if there is an error returns false else returns true when deleting a single file or a response array when deleting multiple
	 */
	public function do_delete($files) {
		$opts = $this->get_options();
		
		try {
			$storage = $this->bootstrap();
		} catch (Exception $e) {
			$storage = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
		}
		
		if (is_object($storage) && !is_wp_error($storage)) {
			// Get the folder from options
			$folder = $opts['folder'];
			$folder_array = explode('/', $folder);
			
			$pointer = null;
			// Check if folder exists
			foreach ($folder_array as $val) {
				if ('' == $val) break; // If value is root break;
				
				$new_pointer = $pointer;
				
				// Fetch objects in dir
				$dirs = $storage->fetchObjects($pointer);
				foreach ($dirs as $dir) {
					$dirname = $dir->getName();
					if ($dirname == $val && $dir->isFolder()) {
						$new_pointer = $dir->getId();
						break; // This folder exists, we want to select this
					}
				}
				
				// If new_pointer is same, path doesn't exist, so can't delete
				if ($pointer == $new_pointer) {
					$this->log("folder does not exist");
					return 'container_access_error';
				}
				$pointer = $new_pointer;
				
			} // End foreach().
			
			$objs = $storage->fetchObjects($pointer);
			$objectids = array();
			foreach ($objs as $obj) {
				$obj_name = $obj->getName();
				if (in_array($obj_name, $files) && !$obj->isFolder()) $objectids[] = $obj->getID();
			}

			if (!empty($objectids)) {
				if (1 == count($objectids)) {
					$storage->deleteObject($objectids[0]);
					return true;
				} else {
					return $storage->deleteObjectMulti($objectids);
				}
			}
			
			$this->log("file does not exist");
			return 'file_access_error';
		}

		if (is_wp_error($storage)) {
			$this->log("service was not available (".$storage->get_error_message().")");
			return 'service_unavailable';
		}

		$this->log('delete error');
		return false;
	}

	public function do_listfiles($match = 'backup_') {
	
		$opts = $this->get_options();
		
		try {
			$storage = $this->bootstrap();
			if (!is_object($storage)) throw new Exception('OneDrive service error');
		} catch (Exception $e) {
			$storage = $e->getMessage().' ('.get_class($e).') (line: '.$e->getLine().', file: '.$e->getFile().')';
			return array();
		}
		
		// https://dev.onedrive.com/items/list.htm
		// OneDrive doesn't (currently - 07-Jul-2016) allow filtering in the request; that has to be done client-side (i.e. here). So, we cache the result (because there are code paths in UD in which we call this multiple times).
		
		static $last_folder = null;
		static $fetched_results = null;
		
		// Get the folder from options
		$folder = $opts['folder'];

		if ($folder != $last_folder || empty($fetched_results)) {
			$pointer = $this->get_pointer($folder, $storage);
			$fetched_results = $storage->fetchObjects($pointer);
			$last_folder = $folder;
		}
		
		$results = array();

		foreach ($fetched_results as $obj) {
			if (!$obj->isFolder()) {
				$res = array(
					'name' => $obj->getName(),
					'size' => $obj->getSize()
				);
				if (!$match || 0 === strpos($res['name'], $match)) $results[] = $res;
			}
		}
		
		return $results;
		
	}

	/**
	 * Move the remote post to the redirect URL
	 *
	 * @param  array $opts
	 * @return array
	 */
	public function do_bootstrap($opts) {
	
		include_once(UPDRAFTPLUS_DIR.'/includes/onedrive/onedrive.php');
		global $updraftplus;
		
		$opts = $this->get_options();
		
		$use_master = $this->use_master($opts);
		
		/*
			The redirect URI has been taken out of the below so that it no longer stores within OPTS
			This check if this is the master (local call) or Auth call and sets URI's appropriately
		*/
		if ($this->use_msgraph_api($opts)) {
			$redirect_uri = $this->the_callback;
		} elseif ($use_master) { // For live sdk compatibility
			$redirect_uri = $this->the_callback.'?ud_source_url='.urlencode(UpdraftPlus_Options::admin_page_url());
		} else {
			$redirect_uri = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth';
		}
		
		/*
			To save calls to the AUth server, Checking from the saved details (in $opts) from the last OneDrive call
			and to see if there needs to be a new call or to re-use the values.  This also double Checks to see
			if the access_token_timeout is set. if this is a new setup, this would never be set and therfore
			initial a first request in order to be saved back to $opts for future calls.
		*/
		if (!isset($opts['access_token_timeout']) || time() > $opts['access_token_timeout']) {
			if ($use_master) { // use_master app
			
				$endpoint_tld = isset($opts['endpoint_tld']) ? $opts['endpoint_tld'] : 'com';
				$client_id = \Onedrive\UpdraftPlus_OneDrive_Account::get_client_id($endpoint_tld);
				$refresh_token = empty($opts['refresh_token']) ? '' : $opts['refresh_token'];
				
				$args = array(
					'code' => 'ud_onedrive_bootstrap',
					'refresh_token' => $refresh_token,
					'endpoint_tld' => $opts['endpoint_tld'],
				);
				// For live sdk compatibility
				if ($use_master && !$this->use_msgraph_api($opts)) {
					$args['ud_source_url'] = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth';
				}
				$result = wp_remote_post($this->the_callback, array(
					'timeout' => 60,
					'headers' => apply_filters('updraftplus_auth_headers', ''),
					'body' => $args
				));
				
				$body = wp_remote_retrieve_body($result);
				$result_body_json = base64_decode($body);
				$result_body = json_decode($result_body_json);
				
			} else { // using own app
			
				$client_id = $opts['clientid'];
				
				// Obtain new token using refresh token
				$args = array(
					'timeout' => 60,
					'body' => array(
						'client_id' => $client_id,
						'redirect_uri' => $redirect_uri,
						'client_secret' => $opts['secret'],
						'refresh_token' => empty($opts['refresh_token']) ? '' : $opts['refresh_token'],
						'grant_type' => 'refresh_token'
					)
				);
				if ($this->use_msgraph_api($opts)) {
					$token_url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
				} else {
					$token_url = 'https://login.live.com/oauth20_token.srf';
				}
				$result = wp_remote_post($token_url, $args);
				
				$body = wp_remote_retrieve_body($result);
				
				$result_body = json_decode($body);
			}
			/**
			 * Before proceeding, check to make sure no errors returned from OneDrive or CloudFlare.
			 * If no refresh_token returned, disply Errrors
			 */
			$http_code = wp_remote_retrieve_response_code($result);
			if ($http_code >= 400) {
			
				$code = 'not_authed';

				$message = __('An error response was received; HTTP code:', 'updraftplus').' '.$http_code;
				
				$headers = wp_remote_retrieve_headers($result);
				
				if (!empty($headers['cf-ray'])) {
					$message .= ' CF-Ray: '.$headers['cf-ray'];
				}
				
				if (403 == $http_code) {
					$ip_addr = $updraftplus->get_outgoing_ip_address();
					if (false !== $ip_addr && false !== filter_var($ip_addr, FILTER_VALIDATE_IP)) {
						$message .= '  IP: '.htmlspecialchars($ip_addr);
						
						$message .= '<br>'.__('This most likely means that you share a webserver with a hacked website that has been used in previous attacks.', 'updraftplus').'<br> <a href="https://updraftplus.com/unblock-ip-address/" target="_blank">'.__('To remove any block, please go here.', 'updraftplus').'</a> '.__('Your IP address:', 'updraftplus').' '.htmlspecialchars($ip_addr);

						$code = 'cloudflare_block';
					}
				}
				
				$message .= ' ('.$code.')';

				return new WP_Error($code, $message, $result_body);
			}
			
			
			if (empty($result_body->refresh_token)) {
				global $updraftplus;
				
				if (is_string($result_body)) {
					if (preg_match('/(?:has banned your IP address \(([\.:0-9a-f]+)\))|(?:Why do I have to complete a CAPTCHA\?)/', $result_body, $matches)) {
						if (empty($matches[1])) {
							$ip_addr = $updraftplus->get_outgoing_ip_address();
							if (false !== $ip_addr && false !== filter_var($ip_addr, FILTER_VALIDATE_IP)) {
								$matches[1] = $ip_addr;
							}
						}
						return new WP_Error('banned_ip', sprintf(__("UpdraftPlus.com has responded with 'Access Denied'.", 'updraftplus').'<br>'.__("It appears that your web server's IP Address (%s) is blocked.", 'updraftplus').' '.__('This most likely means that you share a webserver with a hacked website that has been used in previous attacks.', 'updraftplus').'<br> <a href="'.apply_filters("updraftplus_com_link", "https://updraftplus.com/unblock-ip-address/").'" target="_blank">'.__('To remove the block, please go here.', 'updraftplus').'</a> ', $matches[1]));
					}
				}
				
				$error_log = "Data: ".json_encode($body);
	
				$error_code = 'no_refresh_token';
				$error_message = sprintf(__('Please re-authorize the connection to your %s account.', 'updraftplus'), 'OneDrive');
				
				if (isset($result_body->error)) {
					$error_code = $result_body->error;
					if (isset($result_body->error_description)) $error_message = $result_body->error_description;
				}
	
				$this->log("no refresh token found: $error_code - $error_log");
	
				return new WP_Error('no_refresh_token', 'OneDrive: '.sprintf(__('Account is not authorized (%s).', 'updraftplus'), $error_code).' '.$error_message);
			}
			
			/*
				If no errors returned, setup opts values extra details 				to be saved in $opts for less calls to Auth server
			*/
			
			/*
				Adding the expires_in value returned from OneDrive to the current time to to get the expired time
				If no expires_in value returned, set to current time so it can bypass the IF check on access_token_timeout
			*/
			$opts['access_token_timeout'] = (isset($result_body->expires_in) ? (time()+$result_body->expires_in) : time());
			$opts['access_token'] = $result_body->access_token;
			$opts['expires_in'] = $result_body->expires_in;
			$opts['refresh_token'] = $result_body->refresh_token;

			// save details back to $opts
			$this->set_options($opts, true);
		}
		
		// setup array to be sent to oneDrive

		$client_id = (isset($opts['endpoint_tld']) && 'de' == $opts['endpoint_tld']) ? $this->the_germany_client_id : $this->the_client_id;
		$onedrive_options = array(
			'client_id' => (empty($opts['clientid']) ? $client_id : $opts['clientid']),
			'state' => (object) array(
				'redirect_uri' => $redirect_uri,
				'token' => (object) array(
					'data' => (object) array(
						'obtained_at' => time(),
						'expires_in' => $opts['expires_in'],
						'access_token' => $opts['access_token']
					)
				)
			),
			'ssl_verify' => true,
			'use_msgraph_api' => $this->use_msgraph_api($opts),
			'endpoint_tld'	=> $opts['endpoint_tld'],
		);

		if (UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify')) $onedrive_options['ssl_verify'] = false;
		if (!UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts')) $onedrive_options['ssl_capath'] = UPDRAFTPLUS_DIR.'/includes/cacert.pem';

		$storage = new \Onedrive\Client($onedrive_options);

		$this->set_storage($storage);
		
		return $storage;
	}
	
	/**
	 * Whether or not to use the master app
	 *
	 * @param Array $opts - options
	 *
	 * @return Boolean
	 */
	protected function use_master($opts) {
		if ((!empty($opts['clientid']) && ($opts['clientid'] != $this->the_client_id || $opts['clientid'] != $this->the_germany_client_id)) || (defined('UPDRAFTPLUS_CUSTOM_ONEDRIVE_APP') && UPDRAFTPLUS_CUSTOM_ONEDRIVE_APP)) return false;
		return true;
	}
	
	/**
	 * Whether or not to use msgraph_api
	 *
	 * @param Array $opts - options
	 *
	 * @return Boolean
	 */
	protected function use_msgraph_api($opts) {
		if ($this->use_master($opts) && isset($opts['use_msgraph_api']) && $opts['use_msgraph_api']) {
			return true;
		}
		return false;
	}
	
	/**
	 * Whether or not options exist
	 *
	 * @param Array $opts - options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if ((is_array($opts) && !empty($opts['clientid']) && !empty($opts['secret'])) || ($this->use_master($opts) && !empty($opts['refresh_token']))) return true;
		return false;
	}

	/**
	 * Is called by the authenticate link and calls auth_request or auth_token
	 * Is a multipurpose function for getting request
	 */
	public function action_auth() {
		if (isset($_GET['code'])) {
			// Shouldn't need to change this for user_master, as should never arrive here is that is set
			$this->auth_token($_GET['code']);
		} elseif (isset($_GET['state'])) {
			$parts = explode(':', $_GET['state']);
			$state = $parts[0];
			if ('success' == $state) {
				add_action('all_admin_notices', array($this, 'show_authed_admin_warning'));
			} elseif ('token' == $state) {
				// For when master OneDrive app used
				$encoded_token = stripslashes($_GET['token']);
				$token = json_decode($encoded_token);
				$this->do_complete_authentication($state, $token, false);
			}
		} elseif (isset($_GET['updraftplus_onedriveauth'])) {
			if ('doit' == $_GET['updraftplus_onedriveauth']) {
				$this->action_authenticate_storage();
			} elseif ('deauth' == $_GET['updraftplus_onedriveauth']) {
				$this->action_deauthenticate_storage();
			}
		}
	}

	/**
	 * This method will reset any saved options and start the bootstrap process for an authentication
	 *
	 * @param  String $instance_id - the instance id of the settings we want to authenticate
	 */
	public function do_authenticate_storage($instance_id) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- This is called from backup-module and think this can stay
		// Clear out the existing credentials
		$opts = $this->get_options();
		$opts['refresh_token'] = '';
		// Set a flag so we know this authentication is in progress
		$opts['auth_in_progress'] = true;
		$this->set_options($opts, true);
		try {
			$this->auth_request();
		} catch (Exception $e) {
			$this->log(sprintf(__("%s error: %s", 'updraftplus'), __("Authentication", 'updraftplus'), $e->getMessage()), 'error');
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
		$opts = $this->get_options();
		return $this->auth_token_stage2($code, $opts, $return_instead_of_echo);
	}

	public function show_authed_admin_warning($return_instead_of_echo = false) {
		global $updraftplus_admin;

		$opts = $this->get_options();

		if (empty($opts['refresh_token'])) return;
		// $updraftplus_refresh_token = $opts['refresh_token'];

		$message = '';
		$warning_class = 'updated';
		try {
			// Remove existing object
			$this->set_storage(null);

			$storage = $this->bootstrap($opts);

			if (false != $storage && !is_wp_error($storage)) {

				$quota = $storage->fetchQuota();
				$total = $quota->total;
				$available = $quota->remaining;

				if (is_numeric($total) && is_numeric($available)) {
					$used = $total - $available;
					$used_perc = $total ? round($used*100/$total, 1) : 'n/a';
					$message .= sprintf(__('Your %s quota usage: %s %% used, %s available', 'updraftplus'), 'OneDrive', $used_perc, round($available/1048576, 1).' MB');
				}

				$account_info = $storage->fetchAccountInfo();

				$opts['ownername'] = '';
				if (!empty($account_info->user)) {
					$opts['ownername'] = $account_info->user->displayName;
					$message .= ". <br>".sprintf(__('Your %s account name: %s', 'updraftplus'), 'OneDrive', htmlspecialchars($account_info->user->displayName));
				}
				$this->set_options($opts, true);

			} else {
				if (is_wp_error($storage) && ('cloudflare_block' == $storage->get_error_code() || 'not_authed' == $storage->get_error_code())) {
					$message .= __('However, subsequent access attempts failed:', 'updraftplus');
					$message .= '<br>'.$storage->get_error_message();
					$warning_class = 'error';
				} else {
					if (is_wp_error($storage)) throw new Exception($storage->get_error_message());
					if (!is_object($storage)) throw new Exception("OneDrive service error");
				}
			}
		} catch (Exception $e) {
// $errs = $e->getErrors();
			$errs = array(array('reason' => $e->getCode(), 'message' => $e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')'));
			$message .= __('However, subsequent access attempts failed:', 'updraftplus');
			if (is_array($errs)) {
				$message .= '<ul style="list-style: disc inside;">';
				foreach ($errs as $err) {
					$message .= '<li>';
					if (!empty($err['reason'])) $message .= '<strong>'.htmlspecialchars($err['reason']).':</strong> ';
					if (!empty($err['message'])) {
						if ('cloudflare_block' == $err['reason'] || 'not_authed' == $err['reason']) $message .= $err['message'];
						else $message .= htmlspecialchars($err['message']);
					} else {
						$message .= htmlspecialchars(serialize($err));
					}
					$message .= '</li>';
				}
				$message .= '</ul>';
			} else {
				$message .= htmlspecialchars(serialize($errs));
			}
			$warning_class = 'error';
		}
		
		$final_message = __('Success', 'updraftplus').': '.sprintf(__('you have authenticated your %s account.', 'updraftplus'), __('OneDrive', 'updraftplus')).' '.$message;

		try {
			if ($return_instead_of_echo) {
				return "<div class='updraftmessage {$warning_class}'><p>{$final_message}</p></div>";
			} else {
				$updraftplus_admin->show_admin_warning($final_message, $warning_class);
			}
		} catch (Exception $e) {
			if ($return_instead_of_echo) {
				return "<div class='updraftmessage {$warning_class}'><p>{$e->getMessage()}</p></div>";
			} else {
				$updraftplus_admin->show_admin_warning($e->getMessage());
			}
		}
	}

	private function get_onedrive_perms() {
		return json_encode(array(
			'profile' => array('read' => true),
			'filesystem' => array('read' => true, 'write' => true)
		));
	}

	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'multi_delete', 'conditional_logic', 'manual_authentication');
	}

	public function get_default_options() {
		return array(
			'clientid' => '',
			'secret' => '',
			'url' => '',
			'folder' => '',
			'endpoint_tld' => 'com',
		);
	}

	/**
	 * Over-rides the parent to allow this method to output extra information about using the correct account for OAuth authentication
	 *
	 * @return Boolean - return false so that no extra information is output
	 */
	public function output_account_warning() {
		return true;
	}

	/**
	 * Directs users to the login/authentication page
	 */
	private function auth_request() {

		include_once(UPDRAFTPLUS_DIR.'/includes/onedrive/onedrive.php');
	
		$opts = $this->get_options();
		$use_master = $this->use_master($opts);
		
		// Get the client id
		if ($use_master) {
			$client_id = (isset($opts['endpoint_tld']) && 'de' == $opts['endpoint_tld']) ? $this->the_germany_client_id : $this->the_client_id;
		} else {
			$client_id = empty($opts['clientid']) ? '' : $opts['clientid'];
		}
		
		$instance_id = isset($_GET['updraftplus_instance']) ? $_GET['updraftplus_instance'] : '';

		if (!$use_master) {
			$redirect_uri = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth';
			$callback_uri = '';
		} else {
			$redirect_uri = $this->the_callback;
			$callback_uri = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth&endpoint-tld='.$opts['endpoint_tld'];
		}

		// For all permissions https://developer.microsoft.com/en-us/graph/docs/concepts/permissions_reference
		if ($use_master) {
			$scope = array(
				'openid',
				// 'wl.basic',
				// 'wl.contacts_skydrive',
				// 'wl.skydrive_update',
				// 'wl.offline_access',
				'offline_access',
				// 'onedrive.readwrite',
				'files.readwrite.all',
			);
		} else {
			$scope = array(
				'wl.signin',
				// 'wl.basic',
				// 'wl.contacts_skydrive',
				// 'wl.skydrive_update',
				'wl.offline_access',
				'onedrive.readwrite',
			);
		}
		
		// Instantiate OneDrive client
		$onedrive = new \Onedrive\Client(array(
			'client_id' => $client_id,
			'use_msgraph_api' => $use_master,
			'endpoint_tld' => $opts['endpoint_tld'],
		));

		$url = $onedrive->getLogInUrl($scope, $redirect_uri, array(), $instance_id, $callback_uri);

		if (headers_sent()) {
			$this->log(sprintf(__('The %s authentication could not go ahead, because something else on your site is breaking it. Try disabling your other plugins and switching to a default theme. (Specifically, you are looking for the component that sends output (most likely PHP warnings/errors) before the page begins. Turning off any debugging settings may also help).', 'updraftplus'), 'OneDrive'), 'error');
		} else {
			header('Location: '.esc_url_raw($url));
		}
	}
	
	private function auth_token($code) {

		$opts = $this->get_options();
		$use_master = $this->use_master($opts);

		$secret = (empty($opts['secret'])) ? '' : $opts['secret'];
		
		if ($use_master) {
			$client_id = (isset($opts['endpoint_tld']) && 'de' == $opts['endpoint_tld']) ? $this->the_germany_client_id : $this->the_client_id;
		} else {
			$client_id = (empty($opts['clientid'])) ? '' : $opts['clientid'];
		}
	
		include_once(UPDRAFTPLUS_DIR.'/includes/onedrive/onedrive.php');
		
		if (!$use_master) {
			$callback = UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth';
		} else {
			$callback = $this->the_callback;
		}
		
		$onedrive = new \Onedrive\Client(array(
			'client_id' => $client_id,
			'state' => (object) array('redirect_uri' => $callback),
			// Control comes in this functions, if udp uses custom onedrive app, So it is false
			'use_msgraph_api' => false,
		));

		$onedrive->obtainAccessToken($secret, $code);
		$token = $onedrive->getState();

		$this->auth_token_stage2($token, $opts, false);
	}
	
	/**
	 * Split off so can be accessed directly when using master UDP OneDrive app
	 *
	 * @param string  $token                  - the oauth token array
	 * @param string  $opts                   - the remote storage options
	 * @param boolean $return_instead_of_echo - a boolean to indicate if we should return the result or echo it
	 *
	 * @return void
	 */
	private function auth_token_stage2($token, $opts, $return_instead_of_echo = false) {

		if (!empty($token->token->data->refresh_token)) {
			$opts['use_msgraph_api'] = true;
			$opts['refresh_token'] = $token->token->data->refresh_token;
			// remove our flag so we know this authentication is complete
			if (isset($opts['auth_in_progress'])) unset($opts['auth_in_progress']);
			$this->set_options($opts, true);

			if ($return_instead_of_echo) {
				return $this->show_authed_admin_warning($return_instead_of_echo);
			} else {
				header('Location: '.UpdraftPlus_Options::admin_page_url().'?page=updraftplus&action=updraftmethod-onedrive-auth&state=success');
			}

		} else {
			if (!empty($token->token->data->error)) {
				$this->log(__('authorization failed:', 'updraftplus').' '.$token->token->data->error_description, 'error');
			} else {
				$this->log(__('authorization failed:', 'updraftplus').' '."OneDrive service error: ".serialize($token), 'error');
			}
		}
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		global $updraftplus_admin;

		$classes = $this->get_css_classes(false);
		
		?>
		<tr class="<?php echo $classes . ' ' . 'onedrive_pre_config_container';?>">
			<td colspan="2">
				<img src="<?php echo UPDRAFTPLUS_URL;?>/images/onedrive.png">
				
				{{#unless use_master}}
				{{#if is_ip_host}}
				{{!-- Of course, there are other things that are effectively 127.0.0.1. This is just to help. --}}
				<p>
					<strong>
						<?php
						echo htmlspecialchars(sprintf(__('This site uses a URL which is either non-HTTPS, or is localhost or 127.0.0.1 URL. As such, you must use the main %s %s App to authenticate with your account.', 'updraftplus'), 'UpdraftPlus', 'OneDrive'));
						?>
					</strong>
				</p>
				<br>
				{{else}}
				<p>
					<?php
					echo htmlspecialchars(__('You must add the following as the authorized redirect URI in your OneDrive console (under "API Settings") when asked', 'updraftplus')).': <kbd>'.UpdraftPlus_Options::admin_page_url().'</kbd>';
					?>
				</p>
				<br>
				{{/if}}
				<p>
					<a href="https://account.live.com/developers/applications/create" target="_blank">
						<?php
						_e('Create OneDrive credentials in your OneDrive developer console.', 'updraftplus');
						?>
					</a>
				</p>
				<p>
					<a href="https://updraftplus.com/microsoft-onedrive-setup-guide/" target="_blank">
						<?php
						_e('For longer help, including screenshots, follow this link.', 'updraftplus');
						?>
					</a>
				</p>
				<br>
				{{/unless}}
			
				<?php $updraftplus_admin->curl_check('OneDrive', true, 'onedrive', true); ?>
				<p>
					<?php echo sprintf(__('Please read %s for use of our %s authorization app (none of your backup data is sent to us).', 'updraftplus'), '<a target="_blank" href="https://updraftplus.com/faqs/what-is-your-privacy-policy-for-the-use-of-your-microsoft-onedrive-app/">'.__('this privacy policy', 'updraftplus').'</a>', 'OneDrive');?>
				</p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Get the partial configuration template
	 *
	 * @return String - the partial template, ready for substitutions to be carried out which is appended before test button in template
	 */
	public function do_get_configuration_template() {
		$classes = $this->get_css_classes();
		ob_start();
		?>
		{{#unless use_master}}
		<tr class="<?php echo $classes; ?>">
			<th><?php echo __('OneDrive', 'updraftplus').' '.__('Client ID', 'updraftplus'); ?>:</th>
			<td><input type="text" autocomplete="off" <?php $this->output_settings_field_name_and_id('clientid');?> value="{{clientid}}" class="updraft_input--wide" /><br><em><?php echo htmlspecialchars(__('If OneDrive later shows you the message "unauthorized_client", then you did not enter a valid client ID here.', 'updraftplus'));?></em></td>
		</tr>
		<tr class="<?php echo $classes; ?>">
			<th><?php echo __('OneDrive', 'updraftplus').' '.__('Client Secret', 'updraftplus'); ?>:</th>
			<td><input type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" <?php $this->output_settings_field_name_and_id('secret');?> value="{{secret}}" class="updraft_input--wide" /></td>
		</tr>
		{{/unless}}
		<tr class="<?php echo $classes;?>">
			<th><?php echo 'OneDrive '.__('folder', 'updraftplus');?></th>
			<td>
				<input title="<?php echo esc_attr(sprintf(__('Enter the path of the %s folder you wish to use here.', 'updraftplus'), 'OneDrive').' '.__('If the folder does not already exist, then it will be created.').' '.sprintf(__('e.g. %s', 'updraftplus'), 'MyBackups/WorkWebsite.').' '.sprintf(__('If you leave it blank, then the backup will be placed in the root of your %s', 'updraftplus'), 'OneDrive account').' '.sprintf(__('N.B. %s is not case-sensitive.', 'updraftplus'), 'OneDrive'));?>" type="text" <?php $this->output_settings_field_name_and_id('folder');?> value="{{folder}}" class="updraft_input--wide updraftplus_onedrive_folder_input">
			</td>
		</tr>
		{{#if use_master}}
		<tr class="<?php echo $classes;?>">
			<th><?php _e('Account type', 'updraftplus');?></th>
			<td>
				<select style="width: 180px" <?php $this->output_settings_field_name_and_id('endpoint_tld');?> >
					<option {{#ifeq "com" endpoint_tld}}selected="selected"{{/ifeq}} value="com"><?php echo __('OneDrive International', 'updraftplus');?></option>
					<option {{#ifeq "de" endpoint_tld}}selected="selected"{{/ifeq}} value="de"><?php echo __('OneDrive Germany', 'updraftplus');?></option>
				</select>
			</td>
		</tr>
		{{/if}}
		<tr class="<?php echo $classes;?>">
			<th>
				<?php
				echo sprintf(__('Authenticate with %s', 'updraftplus'), 'OneDrive')
				?>
			</th>
			<td>
				<p>
					{{#if is_already_authenticated}}
					<?php
						echo "<strong>".__('(You appear to be already authenticated).', 'updraftplus').'</strong>';
						$this->get_deauthentication_link();
					?>
					{{/if}}
					{{#if ownername_sentence}}
						{{ownername_sentence}}
					{{/if}}
				</p>
				<?php
					echo '<p>';
					$this->get_authentication_link();
					echo '</p>';
				?>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 * @return array - Modified handerbar template options
	 */
	protected function do_transform_options_for_template($opts) {
		$opts['use_master'] = $this->use_master($opts);
		$site_host = parse_url(network_site_url(), PHP_URL_HOST);
		$site_scheme = parse_url(network_site_url(), PHP_URL_SCHEME);
		$opts['is_ip_host'] = ('127.0.0.1' == $site_host || '::1' == $site_host || 'localhost' == $site_host || 'https' != $site_scheme);
		$opts['folder'] = (empty($opts['folder'])) ? '' : untrailingslashit($opts['folder']);
		$opts['endpoint_tld'] = (empty($opts['endpoint_tld'])) ? 'com' : untrailingslashit($opts['endpoint_tld']);
		$opts['clientid'] = (empty($opts['clientid']) || $opts['use_master']) ? '' : $opts['clientid'];
		$opts['secret'] = (empty($opts['secret']) || $opts['use_master']) ? '' : $opts['secret'];
		$opts['is_already_authenticated'] = (!empty($opts['refresh_token']));
		if (!empty($opts['refresh_token']) && !empty($opts['ownername'])) {
			$opts['ownername_sentence']	= sprintf(__("Account holder's name: %s.", 'updraftplus'), $opts['ownername']);
		}
		return $opts;
	}
	
	/**
	 * Gives settings keys which values should not passed to handlebarsjs context.
	 * The settings stored in UD in the database sometimes also include internal information that it would be best not to send to the front-end (so that it can't be stolen by a man-in-the-middle attacker)
	 *
	 * @return array - Settings array keys which should be filtered
	 */
	public function filter_frontend_settings_keys() {
		return array(
			'access_token',
			'refresh_token',
			'access_token_timeout',
		);
	}
}

// Do *not* instantiate here; it is a storage module, so is instantiated on-demand
// $updraftplus_addons_onedrive = new UpdraftPlus_Addons_RemoteStorage_onedrive;
