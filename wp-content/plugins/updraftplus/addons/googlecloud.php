<?php
// @codingStandardsIgnoreStart
/*
UpdraftPlus Addon: googlecloud:Google Cloud Support
Description: Google Cloud Support
Version: 1.5
Shop: /shop/googlecloud/
Include: includes/googlecloud
IncludePHP: methods/addon-base-v2.php
RequiresPHP: 5.2.4
*/
// @codingStandardsIgnoreEnd

/*
Potential enhancements:
- Implement the permission to not use SSL (we currently always use SSL).
*/

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_RemoteStorage_Addons_Base_v2')) require_once(UPDRAFTPLUS_DIR.'/methods/addon-base-v2.php');

class UpdraftPlus_Addons_RemoteStorage_googlecloud extends UpdraftPlus_RemoteStorage_Addons_Base_v2 {

	private $client;

	private $chunk_size = 2097152;

	private $storage_classes;

	private $bucket_locations;

	// This can get over-ridden by a user-defined constant
	private $client_id = '916618189494-u3ehb1fl7u3meb63nb2b4fqi0r9pcfe2.apps.googleusercontent.com';

	// This can get over-ridden by a user-defined constant
	private $callback_url = 'https://auth.updraftplus.com/auth/googlecloud';

	public function __construct() {
		// 3rd parameter: chunking? 4th: Test button?

		$this->storage_classes = array(
			'STANDARD' => __('Standard', 'updraftplus'),
			'DURABLE_REDUCED_AVAILABILITY' => __('Durable reduced availability', 'updraftplus'),
			'NEARLINE' => __('Nearline', 'updraftplus'),
		);

		$this->bucket_locations = array(
			'us' => __('United States', 'updraftplus').' ('.__('multi-region location', 'updraftplus').')',
			'asia' => __('Asia Pacific', 'updraftplus').' ('.__('multi-region location', 'updraftplus').')',
			'eu' => __('European Union', 'updraftplus').' ('.__('multi-region location', 'updraftplus').')',
			'us-central1' => __('Central United States', 'updraftplus').' (1, '.__('Iowa', 'updraftplus').')',
			'us-east1' => __('Eastern United States', 'updraftplus').' (1, '.__('South Carolina', 'updraftplus').')',
			'us-east4' => __('Eastern United States', 'updraftplus').' (4, '.__('North Virginia', 'updraftplus').')',
			'us-west1' => __('Western United States', 'updraftplus').' (1, '.__('Oregon', 'updraftplus').')',
			'asia-east1' => __('Eastern Asia-Pacific', 'updraftplus').' (1, '.__('Taiwan', 'updraftplus').')',
			'asia-northeast1' => __('North-east Asia', 'updraftplus').' (1, '.__('Tokyo', 'updraftplus').')',
			'asia-southeast1' => __('South-east Asia', 'updraftplus').' (1, '.__('Singapore', 'updraftplus').')',
			'australia-southeast1' => __('South-east Australia', 'updraftplus').' (1, '.__('Sydney', 'updraftplus').')',
			'europe-west1' => __('Western Europe', 'updraftplus').' (1, '.__('Belgium', 'updraftplus').')',
			'europe-west2' => __('Western Europe', 'updraftplus').' (2, '.__('London', 'updraftplus').')',
			'europe-west3' => __('Western Europe', 'updraftplus').' (3, '.__('Frankfurt', 'updraftplus').')',
		);

		parent::__construct('googlecloud', 'Google Cloud Storage', true, true);

		if (defined('UPDRAFTPLUS_UPLOAD_CHUNKSIZE') && UPDRAFTPLUS_UPLOAD_CHUNKSIZE>0) $this->chunk_size = max(UPDRAFTPLUS_UPLOAD_CHUNKSIZE, 512*1024);

		if (defined('UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID')) $this->client_id = UPDRAFTPLUS_GOOGLECLOUD_CLIENT_ID;

		if (defined('UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL')) $this->callback_url = UPDRAFTPLUS_GOOGLECLOUD_CALLBACK_URL;
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
			'clientid' => '',
			'secret' => '',
			'project_id' => '',
			'bucket_path' => '',
			'storage_class' => '',
			'bucket_location' => '',
		);
	}

	/**
	 * This method checks what app we are on and then checks the relevant options to see if they exist
	 *
	 * @param  Array $opts - the array of options for Google Cloud
	 * @return Boolean     - returns a boolean to indicate if the options are found or not
	 */
	public function options_exist($opts) {
		$use_master = $this->use_master($opts);

		if ($use_master) {
			if (is_array($opts) && !empty($opts['user_id']) || !empty($opts['token'])) return true;
		} else {
			if (is_array($opts) && !empty($opts['clientid']) && !empty($opts['secret']) && !empty($opts['bucket_path'])) return true;
		}

		return false;
	}

	public function do_upload($file, $from) {
		return $this->do_upload_engine($file, $from, true);
	}

	/**
	 * The code in this method is basically copied and slightly adjusted from our Google Drive module
	 *
	 * @param  string  $basename
	 * @param  string  $from
	 * @param  boolean $try_again
	 * @return array
	 */
	private function do_upload_engine($basename, $from, $try_again = true) {
		global $updraftplus;
		
		$opts = $this->options;
		$storage = $this->get_storage();

		list ($bucket_name, $path) = $this->split_bucket_path($opts['bucket_path']);
		
		if (empty($bucket_name)) {
			_e("Failure: No bucket details were given.", 'updraftplus');
			return;
		}

		$bucket = $this->create_bucket_if_not_existing($bucket_name);
		if (is_wp_error($bucket)) throw new Exception("Google Cloud Storage: Error accessing or creating bucket: ".$bucket->get_error_message());


		$storage_object = new UDP_Google_Service_Storage_StorageObject();
		$storage_object->setName($path.$basename);
		$storage_object->setBucket($bucket_name);

		// In Google Cloud Storage, the storage class is a property of buckets, not objects - i.e. objects simply inherit from the bucket
// $storage_class = (!empty($opts['storage_class']) && isset($this->storage_classes[$opts['storage_class']])) ? $opts['storage_class'] : 'STANDARD';
// $storage_object->setStorageClass($storage_class);
		
		$this->client->setDefer(true);

		$request = $storage->objects->insert($bucket_name, $storage_object, array(
			'mimeType' => UpdraftPlus_Manipulation_Functions::get_mime_type_from_filename($basename),
			'uploadType' => 'media'
		));

		$hash = md5($basename);
		$transkey = 'resume_'.$hash;
		// This is unset upon completion, so if it is set then we are resuming
		$possible_location = $this->jobdata_get($transkey, false, 'gc'.$transkey);

		$size = 0;
		$local_size = filesize($from);

		if (is_array($possible_location)) {

			$headers = array('content-range' => "bytes */".$local_size);

			$http_request = new UDP_Google_Http_Request(
				$possible_location[0],
				'PUT',
				$headers,
				''
			);
			$response = $this->client->getIo()->makeRequest($http_request);
			$can_resume = false;
			if (308 == $response->getResponseHttpCode()) {
				$range = $response->getResponseHeader('range');
				if (!empty($range) && preg_match('/bytes=0-(\d+)$/', $range, $matches)) {
					$can_resume = true;
					$possible_location[1] = $matches[1]+1;
					$this->log("$basename: upload already began; attempting to resume from byte ".$matches[1]);
				}
			}
			if (!$can_resume) {
				$this->log("$basename: upload already began; attempt to resume did not succeed (HTTP code: ".$response->getResponseHttpCode().")");
			}
		}

		if (!class_exists('UpdraftPlus_Google_Http_MediaFileUpload')) {
			include_once(UPDRAFTPLUS_DIR.'/includes/google-extensions.php');
		}
		
// (('.zip' == substr($basename, -4, 4)) ? 'application/zip' : 'application/octet-stream'),
		$media = new UpdraftPlus_Google_Http_MediaFileUpload(
			$this->client,
			$request,
			UpdraftPlus_Manipulation_Functions::get_mime_type_from_filename($basename),
			null,
			true,
			$this->chunk_size
		);
		$media->setFileSize($local_size);

		if (!empty($possible_location)) {
// $media->resumeUri = $possible_location[0];
// $media->progress = $possible_location[1];
			$media->updraftplus_setResumeUri($possible_location[0]);
			$media->updraftplus_setProgress($possible_location[1]);
			$size = $possible_location[1];
		}
		if ($size >= $local_size) return true;

		$status = false;
		if (false == ($handle = fopen($from, 'rb'))) {
			$this->log("failed to open file: $basename");
			$this->log("$basename: ".__('Error: Failed to open local file', 'updraftplus'), 'error');
			return false;
		}
		if ($size > 0 && 0 != fseek($handle, $size)) {
			$this->log("failed to fseek file: $basename, $size");
			$this->log("$basename (fseek): ".__('Error: Failed to open local file', 'updraftplus'), 'error');
			return false;
		}

		$pointer = $size;

		try {
			while (!$status && !feof($handle)) {
				$chunk = fread($handle, $this->chunk_size);
				// Error handling??
				$pointer += strlen($chunk);
				
				$start_time = microtime(true);
				$status = $media->nextChunk($chunk);
				
				unset($chunk);

				$extra_log = $media->getProgress();
				
				if (!$status && $this->chunk_size < 67108864 && microtime(true) - $start_time < 2.5 && !feof($handle) && $updraftplus->verify_free_memory($this->chunk_size * 4)) {
					$memory_usage = round(@memory_get_usage(false)/1048576, 1);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$memory_usage2 = round(@memory_get_usage(true)/1048576, 1);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					$this->chunk_size = $this->chunk_size * 2;
					$extra_log .= ' - increasing chunk size to '.round($this->chunk_size/1024).' KB';
					$extra_log .= " - memory usage: $memory_usage / $memory_usage2";
				}
				
				$updraftplus->jobdata_set($transkey, array($media->updraftplus_getResumeUri(), $media->getProgress()));
				$updraftplus->record_uploaded_chunk(round(100*$pointer/$local_size, 1), $extra_log, $from);
			}
		} catch (UDP_Google_Service_Exception $e) {
			return $this->catch_upload_engine_exceptions($e, $handle, $try_again, $basename, $from);
		} catch (UDP_Google_IO_Exception $e) {
			return $this->catch_upload_engine_exceptions($e, $handle, $try_again, $basename, $from);
		}

		// N.B. $status is not used in our current code from this point

		fclose($handle);
		$this->client->setDefer(false);
		$this->jobdata_delete($transkey);

		return true;
		
	}

	/**
	 * This function catches exceptions that can rise from uploading files to Google Cloud, it will log the exception, clean up and try the upload again.
	 *
	 * @param Exception $e         - the Google exception we caught
	 * @param Resource  $handle    - a file handler object that needs closing
	 * @param Boolean   $try_again - indicates if we should try again
	 * @param String    $basename  - the file basename
	 * @param String    $from      - the file path
	 *
	 * @return Boolean
	 */
	private function catch_upload_engine_exceptions($e, $handle, $try_again, $basename, $from) {
		global $updraftplus;
		
		$this->log("ERROR: upload error (".get_class($e)."): ".$e->getMessage().' (line: '.$e->getLine().', file: '.$e->getFile().')');
		$this->client->setDefer(false);
		fclose($handle);
		$this->jobdata_delete('resume_'.md5($basename));
		if (false == $try_again) throw($e);
		// Reset this counter to prevent the something_useful_happened condition's possibility being sent into the far future and potentially missed
		if ($updraftplus->current_resumption > 9) $updraftplus->jobdata_set('uploaded_lastreset', $updraftplus->current_resumption);
		return $this->do_upload_engine($basename, $from, false);
	}

	public function do_download($file, $fullpath) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- need $fullpath as its called in functions.php

		global $updraftplus;

		$storage = $this->get_storage();

		list ($bucket, $path) = $this->split_bucket_path($this->options['bucket_path']);
		
		try {
			$objects = $storage->objects->listObjects($bucket, array('prefix' => $path.$file));
		} catch (UDP_Google_Service_Exception $e) {
			return new WP_Error('google_service_exception', sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('You do not have access to this bucket.', 'updraftplus'));
		}
		
		foreach ($objects['items'] as $item) {

			if (preg_match("#^/*([^/]+)/(.*)$#", $item['name'], $bmatches)) {
				$name = $bmatches[2];
			} else {
				$name = $item['name'];
			}

			if ($name !== $file) continue;
			
			$remote_size = $item['size'];
			// Not selfLink
			$link = $item['mediaLink'];

		}

		if (empty($link)) return false;

		return $updraftplus->chunked_download($file, $this, $remote_size, true, $link, $this->chunk_size);
		
	}
	
	public function chunked_download($file, $headers, $link) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found

		$request = $this->client->getAuth()->sign(new UDP_Google_Http_Request($link, 'GET', $headers, null));

		$http_request = $this->client->getIo()->makeRequest($request);

		$http_response_code = $http_request->getResponseHttpCode();

		if (200 == $http_response_code || 206 == $http_response_code) {
			return $http_request->getResponseBody();
		} else {
			$this->log("download: failed: unexpected HTTP response code: ".$http_response_code);
			$this->log(__("download: failed: file not found", 'updraftplus'), 'error');
			return false;
		}

	}

	public function do_delete($file) {

		list ($bucket, $path) = $this->split_bucket_path($this->options['bucket_path']);
		
		$storage = $this->get_storage();

		try {
			$storage->objects->delete($bucket, $path.$file);
			return true;
		} catch (UDP_Google_Service_Exception $e) {
			return new WP_Error('google_service_exception',  sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('You do not have access to this bucket', 'updraftplus'));
		}
		
	}

	public function do_listfiles($match = 'backup_') {
		$opts = $this->options;

		$use_master = $this->use_master($opts);

		if (!$use_master) {
			if (empty($opts['secret']) || empty($opts['clientid']) || empty($opts['project_id']) || empty($opts['bucket_path'])) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), __('Google Cloud', 'updraftplus')));
		} else {
			if (empty($opts['user_id']) || empty($opts['tmp_access_token']) || empty($opts['project_id']) || empty($opts['bucket_path'])) return new WP_Error('no_settings', sprintf(__('No %s settings were found', 'updraftplus'), __('Google Cloud', 'updraftplus')));
		}

		$storage = $this->get_storage();
		if (is_wp_error($storage) || false == $storage) return $storage;
		
		list ($bucket, $path) = $this->split_bucket_path($opts['bucket_path']);
		
		try {
			$objects = $storage->objects->listObjects($bucket, array('prefix' => $path.$match));
		} catch (UDP_Google_Service_Exception $e) {
			return new WP_Error('google_service_exception', sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('You do not have access to this bucket.', 'updraftplus'));
		}
		
		$results = array();
		
		foreach ($objects['items'] as $item) {
			if (preg_match("#^/*([^/]+)/(.*)$#", $item['name'], $bmatches)) {
				$name = $bmatches[2];
			} else {
				$name = $item['name'];
			}
			
			$results[] = array('name' => $name, 'size' => $item['size']);
		}
		
		return $results;
	}
	
	/**
	 * Revoke a Google account refresh token
	 *
	 * @param  boolean $unsetopt - indicates if the options should be unset or not
	 */
	public function gcloud_auth_revoke($unsetopt = true) {
		$opts = $this->get_options();
		$result = wp_remote_get('https://accounts.google.com/o/oauth2/revoke?token='.$opts['token']);

		// If the call to revoke the token fails, we try again one more time
		if (is_wp_error($result) || 200 != wp_remote_retrieve_response_code($result)) {
			wp_remote_get('https://accounts.google.com/o/oauth2/revoke?token='.$opts['token']);
		}
		
		if ($unsetopt) {
			$opts['token'] = '';
			unset($opts['ownername']);
			$this->set_options($opts, true);
		}
	}
	
	/**
	 * Acquire single-use authorization code from Google OAuth 2.0
	 *
	 * @param  String $instance_id - the instance id of the settings we want to authenticate
	 */
	public function do_authenticate_storage($instance_id) {
		$opts = $this->get_options();

		$use_master = $this->use_master($opts);

		// First revoke any existing token, since Google doesn't appear to like issuing new ones
		if (!empty($opts['token']) && !$use_master) $this->gcloud_auth_revoke();

		// We use 'force' here for the approval_prompt, not 'auto', as that deals better with messy situations where the user authenticated, then changed settings

		// Check if there is a client ID set if there is use it otherwise use ours
		if ($use_master) {
			$client_id = $this->client_id;
			$token = 'token'.$this->redirect_uri();
		} else {
			$client_id = $opts['clientid'];
			$token = 'token'.(empty($instance_id) ? ':' . $instance_id : '');
		}

		$params = array(
			'response_type' => 'code',
			'client_id' => $client_id,
			'redirect_uri' => $this->redirect_uri($use_master),
			'scope' => apply_filters('updraft_googlecloud_scope', 'https://www.googleapis.com/auth/devstorage.read_write https://www.googleapis.com/auth/userinfo.profile'),
			'state' => $token,
			'access_type' => 'offline',
			'approval_prompt' => 'force'
		);
		if (headers_sent()) {
			$this->log(__('Authentication could not go ahead, because something else on your site is breaking it. Try disabling your other plugins and switching to a default theme. (Specifically, you are looking for the component that sends output (most likely PHP warnings/errors) before the page begins. Turning off any debugging settings may also help).', 'updraftplus'), 'error');
		} else {
			header('Location: https://accounts.google.com/o/oauth2/auth?'.http_build_query($params, null, '&'));
		}
	}
	
	/**
	 * Get a Google account refresh token using the code received from do_authenticate_storage
	 */
	public function gcloud_auth_token() {
		$opts = $this->get_options();
		if (isset($_GET['code'])) {
			$post_vars = array(
				'code' => $_GET['code'],
				'client_id' => $opts['clientid'],
				'client_secret' => $opts['secret'],
				'redirect_uri' => UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-googlecloud-auth',
				'grant_type' => 'authorization_code'
			);

			$result = wp_remote_post('https://accounts.google.com/o/oauth2/token', array('timeout' => 25, 'method' => 'POST', 'body' => $post_vars));

			if (is_wp_error($result)) {
				$add_to_url = "Bad response when contacting Google: ";
				foreach ($result->get_error_messages() as $message) {
					$this->log("authentication error: ".$message);
					$add_to_url .= $message.". ";
				}
				header('Location: '.UpdraftPlus_Options::admin_page_url().'?page=updraftplus&error='.urlencode($add_to_url));
			} else {
				$json_values = json_decode(wp_remote_retrieve_body($result), true);
				if (isset($json_values['refresh_token'])) {

					 // Save token
					$opts['token'] = $json_values['refresh_token'];
					$this->set_options($opts, true);

					if (isset($json_values['access_token'])) {
						$opts['tmp_access_token'] = $json_values['access_token'];
						$this->set_options($opts, true);
						// We do this to clear the GET parameters, otherwise WordPress sticks them in the _wp_referer in the form and brings them back, leading to confusion + errors
						header('Location: '.UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-googlecloud-auth&page=updraftplus&state=success:'.urlencode($this->get_instance_id()));
					}

				} else {

					$msg = __('No refresh token was received from Google. This often means that you entered your client secret wrongly, or that you have not yet re-authenticated (below) since correcting it. Re-check it, then follow the link to authenticate again. Finally, if that does not work, then use expert mode to wipe all your settings, create a new Google client ID/secret, and start again.', 'updraftplus');

					if (isset($json_values['error'])) $msg .= ' '.sprintf(__('Error: %s', 'updraftplus'), $json_values['error']);

					header('Location: '.UpdraftPlus_Options::admin_page_url().'?page=updraftplus&error='.urlencode($msg));
				}
			}
		} else {
			header('Location: '.UpdraftPlus_Options::admin_page_url().'?page=updraftplus&error='.urlencode(__('Authorization failed', 'updraftplus')));
		}
	}
	
	/**
	 * This method will return a redirect URL depending on the parameter passed. It will either return the redirect for the users site or the auth server.
	 *
	 * @param  Boolean $master - a Bool value to indicate if we want the master redirect URL
	 * @return String          - A redirect URL
	 */
	private function redirect_uri($master = false) {
		if ($master) {
			return $this->callback_url;
		} else {
			return UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-googlecloud-auth';
		}
	}
	
	/**
	 * Get a Google account access token using the refresh token
	 *
	 * @param  string $refresh_token
	 * @param  string $client_id
	 * @param  string $client_secret
	 */
	private function access_token($refresh_token, $client_id, $client_secret) {
		$this->log("requesting access token: client_id=$client_id");

		$query_body = array(
			'refresh_token' => $refresh_token,
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'refresh_token'
		);

		$result = wp_remote_post('https://accounts.google.com/o/oauth2/token',
			array(
				'timeout' => '15',
				'method' => 'POST',
				'body' => $query_body
			)
		);

		if (is_wp_error($result)) {
			$this->log("error when requesting access token");
			foreach ($result->get_error_messages() as $msg) $this->log("Error message: $msg");
			return false;
		} else {
			$json_values = json_decode($result['body'], true);
			if (isset($json_values['access_token'])) {
				$this->log("successfully obtained access token");
				return $json_values['access_token'];
			} else {
				$this->log("error when requesting access token: response does not contain access_token. Response: ".(is_string($result['body']) ? str_replace("\n", '', $result['body']) : json_encode($result['body'])));
				return false;
			}
		}
	}
	
	public function do_bootstrap($opts) {
		$storage = $this->get_storage();

		if (!empty($storage) && is_object($storage) && is_a($storage, 'UDP_Google_Service_Storage')) return $storage;
		
		if (empty($opts)) $opts = $this->get_options();

		$use_master = $this->use_master($opts);

		if (!$use_master) {

			if (empty($opts['token']) || empty($opts['clientid']) || empty($opts['secret'])) {
				$this->log('this account is not authorised');
				$this->log(__('Account is not authorized.', 'updraftplus'), 'error', 'googlecloudnotauthed');
				return new WP_Error('not_authorized', __('Account is not authorized.', 'updraftplus').' (Google Cloud)');
			}

			$access_token = $this->access_token($opts['token'], $opts['clientid'], $opts['secret']);
		} else {

			if (!isset($opts['expires_in']) || $opts['expires_in'] < time()) {

				$user_id = empty($opts['user_id']) ? '' : $opts['user_id'];

				$args = array(
					'code' => 'ud_googlecloud_code',
					'user_id' => $user_id,
				);
				
				$result = wp_remote_post($this->callback_url, array(
					'timeout' => 60,
					'headers' => apply_filters('updraftplus_auth_headers', ''),
					'body' => $args
				));

				if (is_wp_error($result)) {
				
					$body = array('result' => 'error', 'error' => $result->get_error_code(), 'error_description' => $result->get_error_message());
				
				} else {
				
					$body_json = wp_remote_retrieve_body($result);

					$body = json_decode($body_json, true);
					
				}

				if (!empty($body['result']) && 'error' == $body['result']) {
				
					$access_token = new WP_Error($body['error'], empty($body['error_description']) ? __('Have not yet obtained an access token from Google - you need to authorise or re-authorise your connection to Google Cloud.', 'updraftplus') : $body['error_description']);
				
				} else {

					$result_body_json = base64_decode($body[0]);
					$result_body = json_decode($result_body_json);

					if (isset($result_body->access_token)) {
						$access_token = array(
							'access_token' => $result_body->access_token,
							'created' => time(),
							'expires_in' => $result_body->expires_in,
							'id_token' => $result_body->id_token,
							'refresh_token' => ''
						);

						$opts['tmp_access_token'] = $access_token;
						$opts['expires_in'] = $access_token['created'] + $access_token['expires_in'] - 30;
						$this->set_options($opts, true);
					} else {
						$access_token = '';
					}
				}
			} else {
				$access_token = $opts['tmp_access_token'];
			}
		}
		
		$spl = spl_autoload_functions();
		if (is_array($spl)) {
			// Workaround for Google Cloud CDN plugin's autoloader
			if (in_array('wpbgdc_autoloader', $spl)) spl_autoload_unregister('wpbgdc_autoloader');
			// http://www.wpdownloadmanager.com/download/google-drive-explorer/ - but also others, since this is the default function name used by the Google SDK
			if (in_array('google_api_php_client_autoload', $spl)) spl_autoload_unregister('google_api_php_client_autoload');
		}

		if ((!class_exists('UDP_Google_Config') || !class_exists('UDP_Google_Client') || !class_exists('UDP_Google_Service_Storage') || !class_exists('UDP_Google_Http_Request')) && !function_exists('google_api_php_client_autoload_updraftplus')) {
			include_once(UPDRAFTPLUS_DIR.'/includes/Google/autoload.php');
		}

		$config = new UDP_Google_Config();
		$config->setClassConfig('UDP_Google_IO_Abstract', 'request_timeout_seconds', 60);
		// In our testing, $storage->about->get() fails if gzip is not disabled when using the stream wrapper
		if (!function_exists('curl_version') || !function_exists('curl_exec') || (defined('UPDRAFTPLUS_GOOGLECLOUD_DISABLEGZIP') && UPDRAFTPLUS_GOOGLECLOUD_DISABLEGZIP)) {
			$config->setClassConfig('UDP_Google_Http_Request', 'disable_gzip', true);
		}

		if (!$use_master) {
			$client_id = $opts['clientid'];
			$client_secret = $opts['secret'];
			$refresh_token = $opts['token'];
		} else {
			$client_id = $this->client_id;
			$client_secret = '';
			$refresh_token = '';
		}

		$client = new UDP_Google_Client($config);
		$client->setClientId($client_id);
		$client->setClientSecret($client_secret);
		$client->setApplicationName("UpdraftPlus WordPress Backups");
		$client->setRedirectUri($this->redirect_uri());
		$client->setScopes('https://www.googleapis.com/auth/devstorage.read_write');

		// Do we have an access token?
		if (empty($access_token) || is_wp_error($access_token)) {
			$this->log('ERROR: Have not yet obtained an access token from Google (has the user authorised?)');
			$this->log(__('Have not yet obtained an access token from Google - you need to authorize or re-authorize your connection to Google Cloud.', 'updraftplus'), 'error');
			return $access_token;
		}

		if (!$use_master) {
			$client->setAccessToken(json_encode(array(
				'access_token' => $access_token,
				'refresh_token' => $refresh_token
			)));
		} else {
			$client->setAccessToken(json_encode($access_token));
		}

		$io = $client->getIo();
		$setopts = array();

		$ssl_disableverify = isset($opts['ssl_disableverify']) ? $opts['ssl_disableverify'] : UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify');
		$ssl_useservercerts = isset($opts['ssl_useservercerts']) ? $opts['ssl_useservercerts'] : UpdraftPlus_Options::get_updraft_option('updraft_ssl_useservercerts');

		if (is_a($io, 'UDP_Google_IO_Curl')) {

			$setopts[CURLOPT_SSL_VERIFYPEER] = $ssl_disableverify ? false : true;
			if (!$ssl_useservercerts) $setopts[CURLOPT_CAINFO] = UPDRAFTPLUS_DIR.'/includes/cacert.pem';
			// Raise the timeout from the default of 15
			$setopts[CURLOPT_TIMEOUT] = 60;
			$setopts[CURLOPT_CONNECTTIMEOUT] = 15;
			if (defined('UPDRAFTPLUS_IPV4_ONLY') && UPDRAFTPLUS_IPV4_ONLY) $setopts[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
		} elseif (is_a($io, 'UDP_Google_IO_Stream')) {
			$setopts['timeout'] = 60;
			// We had to modify the SDK to support this
			// https://wiki.php.net/rfc/tls-peer-verification - before PHP 5.6, there is no default CA file
			if (!$ssl_useservercerts || (version_compare(PHP_VERSION, '5.6.0', '<'))) $setopts['cafile'] = UPDRAFTPLUS_DIR.'/includes/cacert.pem';
			if ($ssl_disableverify) $setopts['disable_verify_peer'] = true;
		}

		$io->setOptions($setopts);

		$storage = new UDP_Google_Service_Storage($client);
		$this->client = $client;
		$this->set_storage($storage);

		return $storage;

	}

	/**
	 * Acts as a WordPress options filter
	 *
	 * @param  Array $google - An array of Google Cloud options
	 * @return Array - the returned array can either be the set of updated Google Cloud settings or a WordPress error array
	 */
	public function options_filter($google) {

		global $updraftplus;
	
		// Get the current options (and possibly update them to the new format)
		$opts = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format('googlecloud');
		
		if (is_wp_error($opts)) {
			if ('recursion' !== $opts->get_error_code()) {
				$msg = "(".$opts->get_error_code()."): ".$opts->get_error_message();
				$this->log($msg);
				error_log("UpdraftPlus: $msg");
			}
			// The saved options had a problem; so, return the new ones
			return $google;
		}

		if (!is_array($google)) return $opts;

		if (!empty($opts['settings']) && is_array($opts['settings'])) {
			// Remove instances that no longer exist
			foreach ($opts['settings'] as $instance_id => $storage_options) {
				if (!isset($google['settings'][$instance_id])) unset($opts['settings'][$instance_id]);
			}
		}

		if (empty($google['settings'])) return $opts;
		
		foreach ($google['settings'] as $instance_id => $storage_options) {
			$old_token = (empty($opts['settings'][$instance_id]['token'])) ? '' : $opts['settings'][$instance_id]['token'];
			$old_client_id = (empty($opts['settings'][$instance_id]['clientid'])) ? '' : $opts['settings'][$instance_id]['clientid'];
			$old_client_secret = (empty($opts['settings'][$instance_id]['secret'])) ? '' : $opts['settings'][$instance_id]['secret'];
			
			if (isset($google['settings'][$instance_id]['clientid']) && $old_client_id == $google['settings'][$instance_id]['clientid'] && $old_client_secret == $google['settings'][$instance_id]['secret']) {
				$google['settings'][$instance_id]['token'] = $old_token;
			}
			if (!empty($opts['settings'][$instance_id]['token']) && $old_client_id != $google['settings'][$instance_id]['clientid']) {
				include_once(UPDRAFTPLUS_DIR.'/methods/googlecloud.php');
				$updraftplus->register_wp_http_option_hooks();
				$googlecloud = new UpdraftPlus_BackupModule_googlecloud();
				$googlecloud->gcloud_auth_revoke(false);
				$updraftplus->register_wp_http_option_hooks(false);
				$opts['settings'][$instance_id]['token'] = '';
				unset($opts['settings'][$instance_id]['ownername']);
			}
			foreach ($storage_options as $key => $value) {
				// Trim spaces - I got support requests from users who didn't spot the spaces they introduced when copy/pasting
				$opts['settings'][$instance_id][$key] = ('clientid' == $key || 'secret' == $key) ? trim($value) : $value;
				if ('bucket_location' == $key) $opts['settings'][$instance_id][$key] = trim(strtolower($value));
			}
		}
		
		return $opts;
	}

	/**
	 * This function checks if the user has any options for Google Cloud saved or if they have defined to use a custom app and if they have we will not use the master Google Cloud app and allow them to enter and use their own client ID and secret
	 *
	 * @param  Array $opts - the Google Cloud options array
	 * @return Bool        - a bool value to indicate if we should use the master app or not
	 */
	protected function use_master($opts) {
		if ((!empty($opts['clientid']) && !empty($opts['secret'])) || (defined('UPDRAFTPLUS_CUSTOM_GOOGLECLOUD_APP') && UPDRAFTPLUS_CUSTOM_GOOGLECLOUD_APP)) return false;
		return true;
	}

	/**
	 * Is a multipurpose function for getting request
	 * Is called by the authenticate link and calls auth_request or auth_token
	 */
	public function action_auth() {
		if (isset($_GET['state'])) {

			$parts = explode(':', $_GET['state']);
			$state = $parts[0];

			if ('success' == $state) {
				// If these are set then this is a request from the our master app and the auth server has returned these to be saved.
				if (isset($_GET['user_id']) && isset($_GET['access_token'])) {
					$opts = $this->get_options();
					$opts['user_id'] = base64_decode($_GET['user_id']);
					$opts['tmp_access_token'] = base64_decode($_GET['access_token']);
					// Unset this value if it is set as this is a fresh auth we will set this value in the next step
					if (isset($opts['expires_in'])) unset($opts['expires_in']);
					$this->set_options($opts, true);
				}
				add_action('all_admin_notices', array($this, 'show_authed_admin_success'));
			} elseif ('token' == $state) $this->gcloud_auth_token();
			elseif ('revoke' == $state) $this->gcloud_auth_revoke();
		} elseif (isset($_GET['updraftplus_googlecloudauth'])) {
			if ('doit' == $_GET['updraftplus_googlecloudauth']) {
				$this->action_authenticate_storage();
			} elseif ('deauth' == $_GET['updraftplus_googlecloudauth']) {
				$this->action_deauthenticate_storage();
			}
		}
	}

	public function show_authed_admin_success() {

		global $updraftplus_admin;

		$opts = $this->get_options();

		$use_master = $this->use_master($opts);

		if (empty($opts['tmp_access_token'])) return;
		$tmp_opts = $opts;
		$tmp_opts['token'] = $opts['tmp_access_token'];

		$message = '';
		try {
			$this->bootstrap($tmp_opts, false);

			if (false != $this->client && !is_wp_error($this->client)) {
				$oauth2 = new UDP_Google_Service_Oauth2($this->client);
			}

			if (false != $oauth2 && !empty($oauth2->userinfo)) {
				$userinfo = $oauth2->userinfo->get();
				$username = $userinfo->name;
				$opts['ownername'] = $username;
			}
			if (!$this->options_exist($opts)) {
				if (!empty($opts['clientid']) && !empty($opts['secret']) && empty($opts['bucket_path'])) {
					$message .= sprintf(__('But no bucket was defined, so backups may not complete. Please enter a bucket name in the %s settings and save settings.', 'updraftplus'), $this->description);
				} else { // If clientid or secret or both are empty, below message appears. But Authentication success or failure action occurs if user has filled both clientid and secret. In conclusion, Execution control never runs below line of code logically.
					$message .= sprintf(__('But no %s settings were found. Please complete all fields in %s settings and save the settings.', 'updraftplus'), $this->description);
				}
			} else {
				if ($use_master && empty($opts['bucket_path'])) $message .= sprintf(__('But no bucket was defined, so backups may not complete. Please enter a bucket name in the %s settings and save settings.', 'updraftplus'), $this->description);
			}
		} catch (Exception $e) {
			if (is_a($e, 'UDP_Google_Service_Exception')) {
				$errs = $e->getErrors();
				$message .= __('However, subsequent access attempts failed:', 'updraftplus');
				if (is_array($errs)) {
					$message .= '<ul style="list-style: disc inside;">';
					foreach ($errs as $err) {
						$message .= '<li>';
						if (!empty($err['reason'])) $message .= '<strong>'.htmlspecialchars($err['reason']).':</strong> ';
						if (!empty($err['message'])) {
							$message .= htmlspecialchars($err['message']);
						} else {
							$message .= htmlspecialchars(serialize($err));
						}
						$message .= '</li>';
					}
					$message .= '</ul>';
				} else {
					$message .= htmlspecialchars(serialize($errs));
				}
			}
		}

		$updraftplus_admin->show_admin_warning(__('Success', 'updraftplus').': '.sprintf(__('you have authenticated your %s account.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.((!empty($username)) ? sprintf(__('Name: %s.', 'updraftplus'), $username).' ' : '').$message);

		unset($opts['tmp_access_token']);
		$this->set_options($opts, true);

	}
	
	/**
	 * Google require lower-case only; that's not such a hugely obvious one, so we automatically munge it. We also trim slashes.
	 *
	 * @param  string $bucket_path
	 * @return array
	 */
	private function split_bucket_path($bucket_path) {
		if (preg_match("#^/*([^/]+)/(.*)$#", $bucket_path, $bmatches)) {
			$bucket = $bmatches[1];
			$path = trailingslashit($bmatches[2]);
		} else {
			$bucket = trim($bucket_path, " /");
			$path = "";
		}
		
		return array(strtolower($bucket), $path);
	}
	
	public function credentials_test($posted_settings) {
		return $this->credentials_test_engine($posted_settings);
	}
	
	/**
	 * This method will take the passed in credentials and try and connect and write data to the remote storage option
	 *
	 * @param  Array $posted_settings - an array of settings
	 * @return Null - returns if there is an error
	 */
	public function credentials_test_engine($posted_settings) {

		$this->options = $this->get_options();
		$opts = $this->options;

		$use_master = $this->use_master($opts);

		if (!$use_master) {

			if (empty($opts['token']) || empty($posted_settings['clientid']) || empty($posted_settings['secret']) || $posted_settings['clientid'] != $opts['clientid'] || $posted_settings['secret'] != $opts['secret']) {
				_e("You must save and authenticate before you can test your settings.", 'updraftplus');
				return;
			}
		}

		$ssl_useservercerts = (bool) $posted_settings['useservercerts'];
		$ssl_disableverify = (bool) $posted_settings['disableverify'];

		// Not currently used: we always do SSL.
		// Without SSL is possible: https://cloud.google.com/storage/docs/reference-uris?hl=en
// $nossl = $posted_settings['nossl'];
		
		$opts['ssl_useservercerts'] = $ssl_useservercerts;
		$opts['ssl_disableverify'] = $ssl_disableverify;

		$storage = $this->bootstrap($opts);

		if (is_wp_error($storage)) {
			echo __("Failed", 'updraftplus').". ";
			foreach ($storage->get_error_messages() as $msg) {
				echo "$msg\n";
			}
			return;
		}

		$storage_class = (!empty($posted_settings['storage_class']) && isset($this->storage_classes[$posted_settings['storage_class']])) ? $posted_settings['storage_class'] : 'STANDARD';

		$bucket_location = (!empty($posted_settings['bucket_location']) && isset($this->bucket_locations[$posted_settings['bucket_location']])) ? $posted_settings['bucket_location'] : 'US';

		$opts['bucket_location'] = $bucket_location;
		$opts['storage_class'] = $storage_class;

		list ($bucket_name, $path) = $this->split_bucket_path($posted_settings['bucket_path']);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		
		if (empty($bucket_name)) {
			_e("Failure: No bucket details were given.", 'updraftplus');
			return;
		}

		// Project ID only needed if creating a bucket
		if (isset($posted_settings['project_id'])) {
			$project_id = (string) $posted_settings['project_id'];
			$opts['project_id'] = $project_id;
		}

		// Save them so that create_bucket_if_not_existing uses them instead of the saved ones
		$this->options = $opts;
		$this->set_storage($storage);

		$bucket = $this->create_bucket_if_not_existing($bucket_name, $storage_class, $bucket_location);
	
		if (is_wp_error($bucket)) {
			echo __("Failed", 'updraftplus').". ";
			foreach ($bucket->get_error_messages() as $msg) {
				echo "$msg\n";
			}
			return;
		} elseif (!is_a($bucket, 'UDP_Google_Service_Storage_Bucket')) {
			echo __("Failed", 'updraftplus').". (".serialize($bucket).")";
			return;
		}

		$random_file_name = md5(rand()).'.tmp';

		$storage_object = new UDP_Google_Service_Storage_StorageObject();
		$storage_object->setName($random_file_name);
		$storage_object->setBucket($bucket_name);
		
		try {
			$result = $storage->objects->insert($bucket_name, $storage_object, array(
				'data' => 'UpdraftPlus connection test temporary file - you can delete this',
				'mimeType' => 'text/plain',
				'uploadType' => 'media'
			));
		} catch (Exception $e) {
			echo __('Failure', 'updraftplus').": ".__('We successfully accessed the bucket, but the attempt to create a file in it failed.', 'updraftplus')."\n";
			echo $e->getMessage();
			return;
		}

		if (is_a($result, 'UDP_Google_Service_Storage_StorageObject')) {
			echo __('Success', 'updraftplus').": ".__('We accessed the bucket, and were able to create files within it.', 'updraftplus')."\n";
			try {
				$storage->objects->delete($bucket_name, $random_file_name);
			} catch (Exception $e) {
				echo ' '.__('Delete failed:', 'updraftplus').' '.$e->getMessage();
			}
			return;
		} else {
			echo __('Failure', 'updraftplus').": ".__('We successfully accessed the bucket, but the attempt to create a file in it failed.', 'updraftplus').' ('.get_class($result).')';
		}

	}

	/**
	 * Requires project ID to actually create
	 * Returns a UDP_Google_Service_Storage_Bucket if successful
	 * Defaults to STANDARD / US, if the options are not passed and if nothing is in the saved settings
	 *
	 * @param  string  $bucket_name
	 * @param  boolean $storage_class
	 * @param  boolean $location
	 * @return array
	 */
	private function create_bucket_if_not_existing($bucket_name, $storage_class = false, $location = false) {

		$opts = empty($this->options) ? $this->get_options() : $this->options;

		$use_master = $this->use_master($opts);

		if (!$use_master) {
			if (!is_array($opts) || empty($opts['token'])) return new WP_Error('googlecloud_not_authorised', __('Account is not authorized.', 'updraftplus'));
		} else {
			if (!is_array($opts) || empty($opts['user_id'])) return new WP_Error('googlecloud_not_authorised', __('Account is not authorized.', 'updraftplus'));
		}

		$storage = $this->get_storage();

		$storage = empty($storage) ? $this->bootstrap() : $storage;

		if (is_wp_error($storage)) return $storage;

		try {
			$buckets = $storage->buckets;
			if (is_object($buckets) && is_callable(array($buckets, 'get'))) {
				$bucket_object = $buckets->get($bucket_name);
				if (is_a($bucket_object, 'UDP_Google_Service_Storage_Bucket') && isset($bucket_object->name)) return $bucket_object;
			}
			return new WP_Error('googlecloud_unexpected_exception', 'Google Cloud Access Error (unknown response): '.serialize($bucket_object));
		} catch (UDP_Google_Service_Exception $e) {
			$errors = $e->getErrors();
			$codes = '';
			if (is_array($errors)) {
				foreach ($errors as $err) {
					// One other possibility is 'forbidden'
					if (is_array($err) && isset($err['reason'])) {
						if ('notFound' == $err['reason']) {
							$not_found = true;
						} else {
							$codes .= ($codes) ? ', '.$err['reason'] : $err['reason'];
						}
					}
				}
			}
			if (empty($not_found)) {
				return new WP_Error('google_service_exception_'.$codes, sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('You do not have access to this bucket.', 'updraftplus').' ('.$codes.')');
			}
		} catch (Exception $e) {
			return new WP_Error('google_misc_exception', 'Google Cloud Access Error ('.get_class($e).'): '.$e->getMessage(), 'updraftplus');
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			return new WP_Error('google_misc_error', 'Google Cloud Access Error ('.get_class($e).'): '.$e->getMessage(), 'updraftplus');
		}
		
		// If we get here, then the result is 'not found'. Try to create.

		if (empty($opts['project_id'])) return new WP_Error('googlecloud_no_project_id', __('You must enter a project ID in order to be able to create a new bucket.', 'updraftplus'));

		$bucket = new UDP_Google_Service_Storage_Bucket();
		$bucket->setName($bucket_name);

		if (false === $location) {
			$location = (isset($opts['bucket_location']) && isset($this->bucket_locations[$opts['bucket_location']])) ? $opts['bucket_location'] : 'US';
		}

		if (false === $storage_class) {
			$storage_class = (isset($opts['storage_class']) && isset($this->storage_classes[$opts['storage_class']])) ? $opts['storage_class'] : 'STANDARD';
		}

		// global $updraftplus;
		$this->log("Attempting to create bucket $bucket_name with $location/$storage_class");

		$bucket->setLocation($location);
		$bucket->setStorageClass($storage_class);

		try {
			$bucket_object = $storage->buckets->insert($opts['project_id'], $bucket);
			if (is_a($bucket_object, 'UDP_Google_Service_Storage_Bucket') && isset($bucket_object->name)) return $bucket_object;
			return new WP_Error('googlecloud_unexpected_exception', 'Google Cloud Access Error (unknown response/2): '.serialize($bucket_object));
		} catch (UDP_Google_Service_Exception $e) {
			$errors = $e->getErrors();
			$codes = '';
			if (is_array($errors)) {
				foreach ($errors as $err) {
					// One other possibility is 'forbidden'
					if (is_array($err) && isset($err['reason'])) {
						if ('notFound' == $err['reason']) {
							$not_found = true;
						} else {
							$codes .= $codes ? ', '.$err['reason'] : $err['reason'];
						}
					}
				}
			}
			if (empty($not_found)) {
				return new WP_Error('google_service_exception_'.$codes, sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('You do not have access to this bucket.', 'updraftplus').' ('.$codes.')');
			} else {
				return new WP_Error('google_service_exception_not_found', sprintf(__('%s Service Exception.', 'updraftplus'), __('Google Cloud', 'updraftplus')).' '.__('The specified bucket was not found.', 'updraftplus'));
			}
		} catch (Exception $e) {
			return new WP_Error('google_misc_exception', 'Google Cloud Access Error ('.get_class($e).'): '.$e->getMessage(), 'updraftplus');
		}

		// There's nothing that can reach here; but a default doesn't hurt

		return new WP_Error('googlecloud_unexpected_exception', 'Google Cloud Access Error (unknown response/3)');

	}
	
	/**
	 * This function returns a boolean value to indicate if a test button should be printed or not.
	 *
	 * @return Boolean - boolean value to indicate if a test button should be printed or not.
	 */
	public function should_print_test_button() {
		$opts = $this->get_options();
		if (!is_array($opts) || (empty($opts['token']) && empty($opts['user_id']))) return false;
		return true;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {

		$classes = $this->get_css_classes(false);
		$opts = empty($this->options) ? $this->get_options() : $this->options;
		$use_master = $this->use_master($opts);
		
		?>
		<tr class="<?php echo $classes . ' ' . 'googlecloud_pre_config_container';?>">
			<td colspan="2">
				<img alt="<?php echo esc_attr(sprintf(__('%s logo', 'updraftplus'), 'Google Cloud')); ?>" src="<?php echo esc_attr(UPDRAFTPLUS_URL.'/images/googlecloud.png'); ?>"><br>
				<p><?php printf(__('Do not confuse %s with %s - they are separate things.', 'updraftplus'), '<a href="https://cloud.google.com/storage" target="_blank">Google Cloud</a>', '<a href="https://drive.google.com" target="_blank">Google Drive</a>'); ?></p>

			<?php
				$admin_page_url = UpdraftPlus_Options::admin_page_url();
				// This is advisory - so the fact it doesn't match IPv6 addresses isn't important
				if (preg_match('#^(https?://(\d+)\.(\d+)\.(\d+)\.(\d+))/#', $admin_page_url, $matches) && !$use_master) {
				echo '<p><strong>'.htmlspecialchars(sprintf(__("%s does not allow authorization of sites hosted on direct IP addresses. You will need to change your site's address (%s) before you can use %s for storage.", 'updraftplus'), __('Google Cloud', 'updraftplus'), $matches[1], __('Google Cloud', 'updraftplus'))).'</strong></p>';
				} else {
					// If we are not using the master app then show them the instructions for manual setup
					if (!$use_master) {
				?>

				<p><a href="https://updraftplus.com/support/configuring-google-cloud-api-access-updraftplus/" target="_blank"><strong><?php _e('For longer help, including screenshots, follow this link. The description below is sufficient for more expert users.', 'updraftplus');?></strong></a></p>

				<p><a href="https://console.developers.google.com" target="_blank"><?php _e('Follow this link to your Google API Console, and there activate the Storage API and create a Client ID in the API Access section.', 'updraftplus');?></a> <?php _e("Select 'Web Application' as the application type.", 'updraftplus');?></p><p><?php echo htmlspecialchars(__('You must add the following as the authorized redirect URI (under "More Options") when asked', 'updraftplus'));?>: <kbd><?php echo UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-googlecloud-auth'; ?></kbd>
				</p>
				<?php
					}
				}
			?>
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
		$classes = $this->get_css_classes();
		$opts = empty($this->options) ? $this->get_options() : $this->options;
		$use_master = $this->use_master($opts);

		// If we are not using the master app then show them the Client ID and Secret
		if (!$use_master) {
		?>
		
		<tr class="<?php echo $classes; ?>">
			<th><?php echo __('Google Cloud', 'updraftplus').' '.__('Client ID', 'updraftplus'); ?>:</th>
			<td>
				<input title="<?php esc_attr_e('If Google later shows you the message "invalid_client", then you did not enter a valid client ID here.', 'updraftplus');?>" type="text" data-updraft_settings_test="clientid" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('clientid');?> value="{{clientid}}" />
				<br><em><?php _e('If Google later shows you the message "invalid_client", then you did not enter a valid client ID here.', 'updraftplus');?></em>
			</td>
		</tr>
		
		<tr class="<?php echo $classes; ?>">
			<th><?php echo __('Google Cloud', 'updraftplus').' '.__('Client Secret', 'updraftplus'); ?>:</th>
			<td><input data-updraft_settings_test="secret" type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('secret');?> value="{{secret}}" /></td>
		</tr>
		<?php
		}
		?>

		<tr class="<?php echo $classes;?>">
			<th><?php echo 'Google Cloud '.__('Project ID', 'updraftplus').':';?></th>
			<td>
				<input data-updraft_settings_test="project_id" title="<?php echo esc_attr(sprintf(__('Enter the ID of the %s project you wish to use here.', 'updraftplus'), 'Google Cloud')).' '.__('N.B. This is only needed if you have not already created the bucket, and you wish UpdraftPlus to create it for you.', 'updraftplus').' '.__('Otherwise, you can leave it blank.', 'updraftplus');?>" type="text" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('project_id');?> value="{{project_id}}"><br><em><?php echo __('N.B. This is only needed if you have not already created the bucket, and you wish UpdraftPlus to create it for you.', 'updraftplus').' '.__('Otherwise, you can leave it blank.', 'updraftplus');?> <a href="https://updraftplus.com/faqs/where-do-i-find-my-google-project-id/" target="_blank"><?php echo __('Go here for more information.', 'updraftplus');?></a></em>
			</td>
		</tr>
		<tr class="<?php echo $classes;?>">
			<th><?php echo 'Google Cloud '.__('Bucket', 'updraftplus').':';?></th>
			<td><input data-updraft_settings_test="bucket_path" title="<?php echo esc_attr(sprintf(__('Enter the name of the %s bucket you wish to use here.', 'updraftplus'), 'Google Cloud').' '.__('Bucket names have to be globally unique. If the bucket does not already exist, then it will be created.').' '.sprintf(__('e.g. %s', 'updraftplus'), 'mybackups/workwebsite.'));?>" type="text" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('bucket_path');?> value="{{bucket_path}}"><br><a href="https://cloud.google.com/storage/docs/bucket-naming?hl=en" target="_blank"><em><?php echo __("See Google's guidelines on bucket naming by following this link.", 'updraftplus');?></a> <?php echo sprintf(__('You must use a bucket name that is unique, for all %s users.', 'updraftplus'), __('Google Cloud', 'updraftplus'));?></em></td>
		</tr>
		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Storage class', 'updraftplus');?>:<br><a title="<?php _e('Read more about storage classes', 'updraftplus'); ?>" href="https://cloud.google.com/storage/docs/storage-classes" target="_blank"><em><?php _e('(Read more)', 'updraftplus');?></em></a></th>
			<td>
				<select title="<?php echo __('This setting applies only when a new bucket is being created.', 'updraftplus').' '.__('Note that Google do not support every storage class in every location - you should read their documentation to learn about current availability.', 'updraftplus');?>" data-updraft_settings_test="storage_class" <?php $this->output_settings_field_name_and_id('storage_class');?>>
					{{#each storage_classes as |description id|}}
						<option value="{{id}}" {{#ifeq ../storage_class id}}selected="selected"{{/ifeq}}>{{description}}</option>
					{{/each}}
				</select>
				<br>
				<em><?php echo __('This setting applies only when a new bucket is being created.', 'updraftplus').' '.__('Note that Google do not support every storage class in every location - you should read their documentation to learn about current availability.', 'updraftplus');?></em>
			</td>
		</tr>
		
		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Bucket location', 'updraftplus');?>:<br><a title="<?php _e('Read more about bucket locations', 'updraftplus'); ?>" href="https://cloud.google.com/storage/docs/bucket-locations" target="_blank"><em><?php _e('(Read more)', 'updraftplus');?></em></a></th>
			<td>				
				<select title="<?php echo __('This setting applies only when a new bucket is being created.', 'updraftplus');?>" data-updraft_settings_test="bucket_location" <?php $this->output_settings_field_name_and_id('bucket_location');?>>
					{{#each bucket_locations as |description id|}}
						<option value="{{id}}" {{#ifeq ../bucket_location id}}selected="selected"{{/ifeq}}>{{description}}</option>
					{{/each}}					
				</select>
				<br>
				<em><?php echo __('This setting applies only when a new bucket is being created.', 'updraftplus');?></em>

			</td>
		</tr>
		
		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Authenticate with Google');?>:</th>
			<td>
				<p>
					{{#if is_already_authenticated}}
						<?php
							echo __("<strong>(You appear to be already authenticated,</strong> though you can authenticate again to refresh your access if you've had a problem).", 'updraftplus');
							$this->get_deauthentication_link();
						?>
					{{/if}}
					{{#if ownername_sentence}}
						<br>
						{{ownername_sentence}}
					{{/if}}
				</p>
				<?php
					$this->get_authentication_link();
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
	public function transform_options_for_template($opts) {
		$opts['bucket_path'] = empty($opts['bucket_path']) ? '' : untrailingslashit($opts['bucket_path']);
		$opts['storage_class'] = empty($opts['storage_class']) ? 'STANDARD' : $opts['storage_class'];
		$opts['bucket_location'] = empty($opts['bucket_location']) ? 'us' : $opts['bucket_location'];
		$opts['storage_classes'] = $this->storage_classes;
		$opts['bucket_locations'] = $this->bucket_locations;
		$opts['is_already_authenticated'] = (!empty($opts['token']));
		if (!empty($opts['token']) && !empty($opts['ownername'])) {
			$opts['ownername_sentence'] = sprintf(__("Account holder's name: %s.", 'updraftplus'), $opts['ownername']).' ';
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
			'ownername',
			'tmp_access_token',
			'token',
		);
	}

	/**
	 * This function will build and return the authentication link
	 *
	 * @param String $instance_id - the instance id
	 * @param String $text        - the link text
	 *
	 * @return String - the authentication link
	 */
	public function build_authentication_link($instance_id, $text) {
		
		$id = $this->get_id();

		return '<p>'. $text .'</p><br><a data-pretext="'.$text.'" class="button-ud-google updraft_authlink" href="'.UpdraftPlus_Options::admin_page_url().'?&action=updraftmethod-'.$id.'-auth&page=updraftplus&updraftplus_'.$id.'auth=doit&updraftplus_instance='.$instance_id.'" data-instance_id="'.$instance_id.'" data-remote_method="'.$id.'">'.sprintf(__('Sign in with %s', 'updraftplus'), 'Google').'</a>';
	}
}

// Do *not* instantiate here; it is a storage module, so is instantiated on-demand
// $updraftplus_addons_googlecloud = new UpdraftPlus_Addons_RemoteStorage_googlecloud;
