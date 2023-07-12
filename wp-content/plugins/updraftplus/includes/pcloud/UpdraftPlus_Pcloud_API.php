<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed');

/**
 * pCloud API implementation for UpdraftPlus
 *
 * @package PCloud
 */
class UpdraftPlus_Pcloud_API {

	/**
	 * Authentication token key
	 *
	 * @var string $auth Token key
	 */
	private $auth = '';

	/**
	 * Api Endpoint hostname
	 *
	 * @var string $apiep Api Endpoint
	 */
	private $apiep = '';

	/**
	 * Backup folder location
	 *
	 * @var string $folder Backup folder
	 */
	private $folder = '';

	/**
	 * The size in bytes of each uploaded/downloaded chunk
	 *
	 * @var int $part_size
	 */
	private $part_size = 2097152;

	/**
	 * Set authentication key
	 *
	 * @param string $auth Authentication token.
	 *
	 * @return void
	 */
	public function set_auth($auth = '') {
		$this->auth = $auth;
	}

	/**
	 * Set location ID
	 *
	 * @param int|string $location Location ID.
	 *
	 * @return void
	 */
	public function set_location($location = 1) {
		if (1 === $location) {
			$this->apiep = 'https://api.pcloud.com';
		} else {
			$this->apiep = 'https://eapi.pcloud.com';
		}
	}

	/**
	 * Set backup folder
	 *
	 * @param string $folder - the backup folder
	 *
	 * @return void
	 */
	public function set_folder($folder) {
		$this->folder = $folder;
	}

	/**
	 * Retrieves information about the user's account
	 *
	 * @return array|WP_Error - returns the response array or a WP_Error
	 */
	public function account_info() {
		
		$params = array(
			'access_token' => $this->auth
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('userinfo', $params, $args);

		if (is_wp_error($response)) return $response;

		if (is_array($response) && isset($response['quota'])) {
			return $response;
		} else {
			return new WP_Error('unexpected_response', 'pCloud userinfo request returned an unexpected result: '.json_encode($response));
		}
	}

	/**
	 * Create base remote directory
	 *
	 * @param string $dir_name - the directory name.
	 *
	 * @return int|WP_Error - returns the int result or a WP_Error
	 */
	public function make_directory($dir_name) {

		$folder_id = 0;
		$folders = explode("/", $dir_name);

		foreach ($folders as $folder) {
			$params = array(
				'name' => untrailingslashit($folder),
				'folderid' => $folder_id,
				'access_token' => $this->auth,
			);
	
			$args = array(
				'method' => 'GET',
			);
	
			$response = $this->make_request('createfolderifnotexists', $params, $args);
	
			if (is_wp_error($response)) return $response;
	
			if (is_array($response) && isset($response['metadata']) && isset($response['metadata']['folderid'])) {
				$folder_id = intval($response['metadata']['folderid']);
			} else {
				return new WP_Error('unexpected_response', 'pCloud createfolderifnotexists request returned an unexpected result: '.json_encode($response));
			}
		}

		return $folder_id;
	}

	/**
	 * Get upload Dir ID
	 *
	 * @return int|WP_Error - returns upload folder ID or a WP_Error
	 */
	public function get_upload_dir_id() {

		$backup_dir = $this->get_backup_dir();

		$params = array(
			'path' => '/' . $backup_dir,
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('listfolder', $params, $args);

		if (is_wp_error($response)) return $response;

		if (!isset($response['result']) || 2005 === $response['result']) {
			return $this->make_directory($backup_dir);
		} else {
			if (isset($response['metadata'])) {
				return $response['metadata']['folderid'];
			}
		}

		return 0;
	}

	/**
	 * Prepare to initiate Upload process
	 *
	 * @return array|WP_Error - returns response array or a WP_Error
	 */
	public function create_upload() {
		
		$params = array(
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('upload_create', $params, $args);

		if (is_wp_error($response)) return $response;

		return $response;
	}

	/**
	 * Chunked upload
	 *
	 * @param string $path         - path/file to be uploaded.
	 * @param int    $upload_id    - pCloud Upload ID.
	 * @param int    $uploadoffset - file offset.
	 *
	 * @return int|WP_Error returns the new offset OR -2 when end of file reached or WP_Error
	 */
	public function chunked_upload($path, $upload_id = 0, $uploadoffset = 0) {

		if (!file_exists($path) || !is_file($path) || !is_readable($path)) {
			return new WP_Error('invalid_file', 'pCloud chunked upload: Invalid file provided: '.$path);
		}

		$filesize = abs(filesize($path));

		if ($uploadoffset >= $filesize) {
			return -2;
		}

		$file = fopen($path, 'r');

		if (0 < $uploadoffset) {
			fseek($file, $uploadoffset);
		}

		$params = array(
			'uploadid'     => $upload_id,
			'uploadoffset' => $uploadoffset,
		);

		$content = fread($file, $this->part_size);

		if (!empty($content)) {

			$result = $this->write($content, $params);

			if (is_wp_error($result)) {
				fclose($file);
				return $result;
			}

			$uploadoffset += $this->part_size;
		}

		fclose($file);

		if ($uploadoffset >= $filesize) {
			return -2;
		}

		return $uploadoffset;
	}

	/**
	 * Save the uploaded file
	 *
	 * @param int    $upload_id - pCloud Upload ID.
	 * @param string $path      - File Path.
	 * @param int    $folder_id - pCloud Folder ID.
	 *
	 * @return array|WP_Error - returns response array or a WP_Error
	 */
	public function save($upload_id, $path, $folder_id) {

		$path = str_replace(array('\\'), "/", $path);
		$parts = explode("/", $path);
		$filename = end($parts);

		$params = array(
			'uploadid' => intval($upload_id),
			'name' => rawurlencode($filename),
			'folderid' => intval($folder_id),
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('upload_save', $params, $args);

		if (is_wp_error($response)) return $response;

		return $response;
	}

	/**
	 * Collecting existing backups on pCloud servers
	 *
	 * @return array|WP_Error - returns response array or a WP_Error
	 */
	public function list_backups() {

		$backup_dir = $this->get_backup_dir();

		$params = array(
			'path' => '/' . $backup_dir,
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('listfolder', $params, $args);

		if (is_wp_error($response)) return $response;

		if (is_bool($response) || !isset($response['metadata']) || !isset($response['metadata']['contents'])) {
			return new WP_Error('unexpected_response', 'pCloud listfolder: returned an unexpected result: '.json_encode($response));
		}

		return $response['metadata']['contents'];
	}

	/**
	 * Chunked downloads a file.
	 *
	 * @param int      $file_id      - pCloud File ID.
	 * @param resource $archive_file - the local file handle.
	 * @param array    $options      - any extra options to be passed e.g. headers.
	 * @param array    $offset       - you can create real chunked download using this offset param.
	 *
	 * @return int
	 * @throws Exception Throws standard exception.
	 */
	public function download($file_id, $archive_file = null, $options = array(), $offset = 0) {

		$chunksize = 3 * (1024 * 1024);

		if (isset($options['Range'])) { // Disabled !
			$range_tmp = str_replace('bytes=', '', $options['Range']);
			$range_arr = explode('-', $range_tmp);
			if (is_array($range_arr)) {
				if (isset($range_arr[0]) && is_numeric($range_arr[0]) && isset($range_arr[1]) && is_numeric($range_arr[1])) {
					$offset = intval($range_arr[0]);
					$chunksize = intval($range_arr[1]) - $offset;
				}
			}
		}

		$dwl_url = '';

		$params = array(
			'fileid' => $file_id,
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('getfilelink', $params, $args);

		if (is_wp_error($response)) return $response;
		
		if (isset($response['hosts']) && isset($response['hosts'][0])) {
			$dwl_url = $response['hosts'][0] . $response['path'];
		}

		if (empty($dwl_url)) {
			return new WP_Error('unexpected_response', 'pCloud download: Failed to get file download link.');
		}

		$errstr = '';

		$args = array(
			'headers' => array(
				'Range' => 'bytes=' . $offset . '-' . ($offset + ($chunksize - 1)),
			),
		);

		$content = false;
		$api_response = wp_remote_get('https://' . $dwl_url, $args);
		if (is_array($api_response) && !is_wp_error($api_response)) {
			$response_body = wp_remote_retrieve_body($api_response);
			if (is_string($response_body)) {
				$content = $response_body;
			}
		} else {
			$errstr = $api_response->get_error_message();
		}

		if (!$content) {
			return new WP_Error('unexpected_response', 'pCloud download: Failed to open connection to the backup file: ' . $dwl_url . ' error:' . $errstr);
		} else {
			if (!fwrite($archive_file, $content)) {
				return new WP_Error('unexpected_response', 'pCloud download: Failed to write content to output file.');
			} else {
				$offset += $chunksize;
			}
		}

		return $offset;
	}

	/**
	 * Deletes remote file
	 *
	 * @param string $path - The path to the file to be deleted.
	 *
	 * @return array|WP_Error - returns response array or a WP_Error
	 */
	public function delete($path) {

		$params = array(
			'path' => $path,
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('deletefile', $params, $args);

		if (is_wp_error($response)) return $response;

		if (is_bool($response)) {
			return new WP_Error('unexpected_response', 'pCloud deletefile: returned an unexpected result: '.json_encode($response));
		}

		return $response;
	}

	/**
	 * Get File info from
	 *
	 * @param string $file - basename, needed file.
	 *
	 * @return array|WP_Error - returns response array or a WP_Error
	 */
	public function get_file_info($file) {

		$path = "/" . $this->get_backup_dir() . "/" . $file;

		$params = array(
			'path' => $path,
			'access_token' => $this->auth,
		);

		$args = array(
			'method' => 'GET',
		);

		$response = $this->make_request('checksumfile', $params, $args);

		if (is_wp_error($response)) return $response;

		if (is_bool($response)) {
			return new WP_Error('unexpected_response', 'pCloud checksumfile: returned an unexpected result: '.json_encode($response));
		}

		$response = $response['metadata'];

		return $response;
	}

	/**
	 * Get Backup directory
	 *
	 * @return string - the backup directory
	 */
	public function get_backup_dir() {
		return untrailingslashit(apply_filters('updraftplus_pcloud_backup_dir', 'UpdraftPlus').'/'.$this->folder);
	}

	/**
	 * Upload - write content chunk
	 *
	 * @param string $content - String content to be written.
	 * @param array  $params  - Additional request params.
	 *
	 * @return WP_Error - returns a WP_Error if something goes wrong
	 */
	private function write($content, $params) {

		$params['access_token'] = $this->auth;

		$args = array(
			'method'      => 'PUT',
			'redirection' => 5,
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $content,
		);

		$response = $this->make_request('upload_write', $params, $args);

		if (is_wp_error($response)) return $response;
	}

	/**
	 * This function will make a API request andcheck the response. Returns the response or a WordPress error.
	 *
	 * @param string $endpoint - the API endpoint
	 * @param array  $params   - the API request parameters
	 * @param array  $args     - an array of request options
	 *
	 * @return array|WP_Error - returns an array response or WP_Error
	 */
	private function make_request($endpoint, $params, $args) {
		$api_response = wp_remote_request($this->apiep . '/'. $endpoint . '?' . http_build_query($params), $args);

		if (is_wp_error($api_response)) {
			return $api_response;
		}

		$response_code = wp_remote_retrieve_response_code($api_response);
		if ($response_code < 200 || $response_code >= 300) {
			return new WP_Error('unexpected_response_code', 'pCloud ' . $endpoint . ': returned an unexpected response code: '. $response_code . ' response: '.json_encode($api_response));
		}

		$response_body = wp_remote_retrieve_body($api_response);
		if (empty($response_body) || !is_string($response_body)) {
			return new WP_Error('no_response_body', 'pCloud ' . $endpoint . ': returned no response body: ' . json_encode($api_response));
		}
		
		$response_array = json_decode($response_body, true);
		if (!is_array($response_array)) {
			return new WP_Error('json_decode_failed', 'pCloud ' . $endpoint . ': Failed to json decode: ' . $response_body);
		}

		return $response_array;
	}
}
