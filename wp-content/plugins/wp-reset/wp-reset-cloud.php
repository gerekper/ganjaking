<?php

/**
 * WP Reset PRO
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2015 - 2021
 */

// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}
class WP_Reset_Cloud
{
    public $cloud_url = 'http://dashboard.wpreset.com/cloud/v1/';
    public $cloud_timeout = 60;
    public $stream_size = 2500000;
    public $cloud_service = 'wpreset';
    public $snapshots;
    private $dropbox = false;
    private $pcloud = false;
    private $pcloudeu = false;
    private $gdrive = false;
    public $icedrive = false;
    public function __construct()
    {
        global $wp_reset;
        $options = $wp_reset->get_options();
        $this->cloud_service = $options['cloud_service'];
    }


    /**
     * Returns all cloud snapshots from DB
     *
     * @return array
     */
    public function get_cloud_snapshots()
    {
        if (!empty($this->snapshots)) {
            return $this->snapshots;
        }

        $this->snapshots = get_option('wp-reset-cloud-snapshots', array());

        if (!is_array($this->snapshots)) {
            return array();
        }

        foreach ($this->snapshots as $uid => $snapshot) {
            $this->snapshots[$uid]['name'] = stripslashes($this->snapshots[$uid]['name']);
        }

        return $this->snapshots;
    } // get_cloud_snapshots


    /**
     * Run cloud action from AJAX request
     *
     * @param string|array $extra_data depending on action
     * 
     * @return array|WP_Error
     */
    public function cloud_action($extra_data)
    {
        global $wp_reset;

        switch ($extra_data['action']) {
            case 'cloud_authorize_get_url':
                return $this->cloud_authorize_get_url($extra_data['service']);
                break;
            case 'cloud_authorize_auth':
                return $this->cloud_authorize_auth($extra_data);
                break;
            case 'cloud_switch_service':
                $options = $wp_reset->get_options();
                $options['cloud_service'] = $extra_data['service'];
                $this->cloud_service = $extra_data['service'];
                $wp_reset->update_options('options', $options);
                $this->cloud_snapshot_refresh();
                return true;
                break;
            case 'snapshot_upload':
                $uid = $extra_data['parameters'];

                $snapshots = $wp_reset->get_snapshots();

                if (!array_key_exists($uid, $snapshots)) {
                    return new WP_Error('1', 'Snapshot not found.');
                }

                if (array_key_exists('cloud', $snapshots[$uid]) && $snapshots[$uid]['cloud'] == true) {
                    return new WP_Error('1', 'Snapshot already exists in cloud.');
                }

                $snapshot_export_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');
                if (!file_exists($snapshot_export_file)) {
                    $res = $wp_reset->do_export_snapshot($uid);
                    if (is_wp_error($res)) {
                        return $res;
                    }
                }

                if (is_wp_error($snapshot_export_file)) {
                    return $snapshot_export_file;
                }

                return $this->cloud_snapshot_upload($snapshots[$uid], $snapshot_export_file);
                break;
            case 'upload_part':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_upload_part($uid, $extra_data['position']);
                break;
            case 'check_snapshot':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_check($uid);
                break;
            case 'snapshot_download':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_download($uid);
                break;
            case 'snapshot_download_check':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_download_check($uid);
                break;
            case 'download_part':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_download_part($uid, $extra_data['position']);
                break;
            case 'snapshot_delete':
                $uid = $extra_data['parameters'];
                return $this->cloud_snapshot_delete($uid);
                break;
            case 'snapshot_delete_local':
                $uid = $extra_data['parameters'];
                $delete = $wp_reset->do_delete_snapshot($uid);
                if (is_wp_error($delete)) {
                    wp_send_json_error($delete->get_error_message());
                } else {
                    return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
                }
                break;
            case 'snapshots_refresh':
                $wp_reset->log('info', 'Cloud: Refreshing snapshots');
                return $this->cloud_snapshot_refresh();
                break;
            default:
                return new WP_Error('1', 'Unknown cloud action.');
                break;
        }

        return true;
    } // cloud_action


    /**
     * Query Cloud Endpoint
     *
     * @param array $parameters, send via request headers
     * @param string $body, send as raw data, if not false, Content-Type header will be set to application/octet-stream
     * 
     * @return string authorize URL 
     */
    function query_cloud_server($parameters, $body = false, $return_raw = false)
    {
        global $wp_reset, $wp_reset_licensing;

        $license = $wp_reset_licensing->get_license();

        $headers = array(
            'license_key' => $license['license_key'],
            'version' => $wp_reset->version,
            'wp_version' => get_bloginfo('version'),
            'site_url' => get_home_url(),
            'site_title' => preg_replace('/[[:^print:]]/', '', get_bloginfo('name')),
            'access_key' => $license['access_key'],
            'meta' => 'Array',
        );

        $headers = array_merge($headers, $parameters);

        if ($body !== false) {
            $headers['content-type'] = 'application/octet-stream';
        }

        $args = array(
            'timeout'     => $this->cloud_timeout,
            'sslverify' => false,
            'headers' => $headers,
            'body' => $body
        );

        $response = wp_remote_post($this->cloud_url, $args);

        $wp_reset->log('info', 'Cloud request: ' . serialize($headers));
        if(is_wp_error($response)){
            $wp_reset->log('info', 'Cloud response: ' . $response->get_error_message());
        } else {
            $wp_reset->log('info', 'Cloud response: ' . $response['body']);
        }
        

        if ($return_raw) {
            return $response;
        }

        if (is_wp_error($response)) {
            return $response;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Get authorize URL for cloud services
     *
     * @param string $service e.g. dropbox, gdrive
     * 
     * @return string authorize URL 
     */
    public function cloud_authorize_get_url($service)
    {
        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'cloud_authorize_get_url',
                'service' => $service,
                'redirect_url' => admin_url('tools.php?page=wp-reset&authorize_cloud=' . $service)
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            return @$response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the authorize URL.');
        }
    } // cloud_authorize_get_url


    /**
     * Save authentication details for cloud services using username and password
     *
     * @param array containing service, user and pass
     * 
     * @return string|object WP_Error or success message
     */
    function cloud_authorize_auth($extra_data)
    {
        global $wp_reset;
        $options = $wp_reset->get_options();

        if ($extra_data['service'] == 'icedrive') {
            if (!array_key_exists('user', $extra_data) || !array_key_exists('pass', $extra_data) || strlen($extra_data['user']) < 4 || strlen($extra_data['pass']) < 4) {
                return new WP_Error('1', 'Failed to connect to ' . $wp_reset->cloud_services[$extra_data['service']] . ': Invalid username or password.');
            }

            $options['cloud_data']['icedrive']['user'] = $extra_data['user'];
            $options['cloud_data']['icedrive']['pass'] = $extra_data['pass'];
            $wp_reset->update_options('options', $options);

            $this->icedrive = $this->get_icedrive_client();
            if (is_wp_error($this->icedrive)) {
                return $this->icedrive;
            }
        }

        return $wp_reset->cloud_services[$extra_data['service']] . ' connected succesfully!';
    }


    /**
     * Get authorize URL for cloud services
     *
     * @param string $service e.g. dropbox, gdrive
     * 
     * @return string authorize URL 
     */
    public function cloud_authorize_get_token($service, $code)
    {
        global $wp_reset;

        $options = $wp_reset->get_options();

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'cloud_authorize_get_token',
                'code' => $code,
                'service' => $service
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $options['cloud_data'][$service]['token'] = json_encode($response['data']);
            $options['cloud_service'] = $service;
            $wp_reset->update_options('options', $options);
            $this->cloud_service = $service;
            $this->cloud_snapshot_refresh();
            return true;
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the ' . $service . ' token.');
        }

        return true;
    } // cloud_authorize_get_token


    /**
     * Begin collection item upload
     *
     * @return string cloud service
     */
    public function upload_collection_item($collection_id, $item_id, $item_file)
    {

        switch ($this->cloud_service) {
            case 'dropbox':
                $result = $this->dropbox_collection_upload($item_id, $item_file);
                break;
            case 'gdrive':
                $result = $this->gdrive_collection_upload($item_id, $item_file);
                break;
            case 'pcloud':
            case 'pcloudeu':
                $result = $this->pcloud_collection_upload($collection_id, $item_id, $item_file);
                break;
            case 'icedrive':
                $result = $this->icedrive_collection_upload($collection_id, $item_id, $item_file);
                break;
            case 'wpreset':
                $result = $this->wpreset_collection_upload($item_id, $item_file);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // upload_collection_item


    /**
     * Delete collection item from cloud
     *    
     * @param int collection_id
     * @param int collection_item_id
     *
     * @return array next ajax step
     */
    public function cloud_collection_item_delete($location, $collection_id, $collection_item_id)
    {
        $result = new WP_Error('1', 'The collection item has been deleted but the file still remains ');

        switch ($location) {
            case 'dropbox':
                if ($this->cloud_service != 'gdrive') {
                    return new WP_Error('1', 'The collection item has been deleted but the file still remains in your Dropbox because it is not your currently active cloud service');
                }
                $result = $this->dropbox_collection_item_delete($collection_id, $collection_item_id);
                break;
            case 'gdrive':
                if ($this->cloud_service != 'gdrive') {
                    return new WP_Error('1', 'The collection item has been deleted but the file still remains in your Google Drive because it is not your currently active cloud service');
                }
                $result = $this->gdrive_collection_item_delete($collection_id, $collection_item_id);
                break;
            case 'pcloud':
            case 'pcloudeu':
                if ($this->cloud_service != 'pcloud' && $this->cloud_service != 'pcloudeu') {
                    return new WP_Error('1', 'The collection item has been deleted but the file still remains in your pCloud because it is not your currently active cloud service');
                }
                $result = $this->pcloud_collection_item_delete($collection_id, $collection_item_id);
                break;
            case 'icedrive':
                if ($this->cloud_service != 'icedrive') {
                    return new WP_Error('1', 'The collection item has been deleted but the file still remains in your icedrive because it is not your currently active cloud service');
                }
                $result = $this->icedrive_collection_item_delete($collection_id, $collection_item_id);
                break;
            case 'wpreset':
                $result = $this->wpreset_collection_item_delete($collection_item_id);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_collection_item_delete


    /**
     * Begin snapshot download
     *
     * @return string cloud service
     */
    public function cloud_snapshot_download($snapshot)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                $result = $this->dropbox_snapshot_download($snapshot);
                break;
            case 'gdrive':
                $result = $this->gdrive_snapshot_download($snapshot);
                break;
            case 'pcloud':
            case 'pcloudeu':
                $result = $this->pcloud_snapshot_download($snapshot);
                break;
            case 'icedrive':
                $result = $this->icedrive_snapshot_download($snapshot);
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_download($snapshot);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_download


    /**
     * Download snapshot part
     *
     * @param string snapshot uid
     * @param int position to download from
     * 
     * @return array next ajax step
     */
    public function cloud_snapshot_download_part($uid, $position)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                break;
            case 'gdrive':
                break;
            case 'pcloud':
            case 'pcloudeu':
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_download_part($uid, $position);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_download_part


    /**
     * Check downloaded snapshot
     *
     * @param string snapshot data
     * 
     * @return array next ajax step
     */
    public function cloud_snapshot_download_check($snapshot)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                $result = $this->dropbox_snapshot_download_check($snapshot);
                break;
            case 'gdrive':
                $result = $this->gdrive_snapshot_download_check($snapshot);
                break;
            case 'pcloud':
            case 'pcloudeu':
                $result = $this->pcloud_snapshot_download_check($snapshot);
                break;
            case 'icedrive':
                $result = $this->icedrive_snapshot_download_check($snapshot);
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_download_check($snapshot);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_download_check


    /**
     * Register snapshot to cloud and begin uploading
     *
     * @param string snapshot data
     * 
     * @return array next ajax step
     */
    public function cloud_snapshot_upload($snapshot)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                $result = $this->dropbox_snapshot_upload($snapshot);
                break;
            case 'gdrive':
                $result = $this->gdrive_snapshot_upload($snapshot);
                break;
            case 'pcloud':
            case 'pcloudeu':
                $result = $this->pcloud_snapshot_upload($snapshot);
                break;
            case 'icedrive':
                $result = $this->icedrive_snapshot_upload($snapshot);
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_upload($snapshot);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_upload


    /**
     * Upload snapshot part to the cloud
     *
     * @param string snapshot uid
     * @param int position to upload from
     * 
     * @return array next ajax step
     */
    public function cloud_snapshot_upload_part($uid, $position)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                break;
            case 'gdrive':
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_upload_part($uid, $position);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_upload_part


    /**
     * Check snapshot upload
     *
     * @param string snapshot uid
     * 
     * @return array next ajax step
     */
    public function cloud_snapshot_check($uid)
    {
        switch ($this->cloud_service) {
            case 'dropbox':
                break;
            case 'gdrive':
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_check($uid);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_check


    /**
     * Delete snapshot from cloud
     *    
     * @param string snapshot uid
     *
     * @return array next ajax step
     */
    public function cloud_snapshot_delete($uid)
    {
        global $wp_reset;
        $snapshots = $wp_reset->get_snapshots();
        unset($snapshots[$uid]['autoupload']);
        update_option('wp-reset-snapshots', $snapshots);

        switch ($this->cloud_service) {
            case 'dropbox':
                $result = $this->dropbox_snapshot_delete($uid);
                break;
            case 'gdrive':
                $result = $this->gdrive_snapshot_delete($uid);
                break;
            case 'pcloud':
            case 'pcloudeu':
                $result = $this->pcloud_snapshot_delete($uid);
                break;
            case 'icedrive':
                $result = $this->icedrive_snapshot_delete($uid);
                break;
            case 'wpreset':
                $result = $this->wpreset_snapshot_delete($uid);
                break;
            default:
                $result = new WP_Error('1', 'No cloud service is enabled');
        }

        return $result;
    } // cloud_snapshot_delete


    /**
     * Refresh cloud snapshots array
     *
     * @return array next ajax step
     */
    public function cloud_snapshot_refresh()
    {
        switch ($this->cloud_service) {
            case 'dropbox':
            case 'gdrive':
            case 'wpreset':
            case 'pcloud':
            case 'pcloudeu':
            case 'icedrive':
                $result = $this->wpreset_snapshot_refresh();
                break;
            default:
                update_option('wp-reset-cloud-snapshots', array());
                $result = false;
                break;
        }

        return $result;
    } // cloud_snapshot_refresh


    /** 
   #####################
   WP Reset Cloud
   #####################
     */


    /**
     * Get snapshot information to begin downloading from WP Reset Cloud
     *
     * @param array snapshot data
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_download($snapshot)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot . '.zip');
        if (file_exists($snapshot_file)) {
            unlink($snapshot_file);
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download',
                'snapshot_uid' => $snapshot
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            return array('parameters' => $snapshot, 'action' => 'download_part', 'continue' => 1, 'position' => 0, 'message' => 'Downloading snapshot');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the snapshot data from the cloud.');
        }
    } // wpreset_snapshot_download


    /**
     * Download snapshot part from WP Reset Cloud
     *
     * @param string snapshot uid
     * @param int position in file to start download from
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_download_part($uid, $position)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        $file = fopen($snapshot_file, 'a');
        fseek($file, $position);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download_part',
                'position' => $position,
                'snapshot_uid' => $uid,
                'stream_size' => $this->stream_size
            ),
            false,
            true
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $headers = wp_remote_retrieve_headers($response);
        $part = wp_remote_retrieve_body($response);

        if (trim($headers['part_checksum']) == md5($part)) {
            fwrite($file, $part);
            fclose($file);

            if (isset($headers['last_part'])) {
                return array('parameters' => $uid, 'action' => 'snapshot_download_check', 'continue' => 1, 'message' => 'Checking downloaded snapshot');
            } else {
                return array('parameters' => $uid, 'action' => 'download_part', 'continue' => 1, 'position' => $position + $this->stream_size, 'message' => 'Downloaded ' . WP_Reset_Utility::format_size($position) . ' of ' . WP_Reset_Utility::format_size($headers['file_size']));
            }
        } else {
            unlink($snapshot_file);

            if (isset($response['data'])) {
                return new WP_Error('1', $response['data']);
            } else {
                return new WP_Error('1', 'An error occurred downloading the snapshot from the cloud.');
            }
        }
    } // wpreset_snapshot_download_part


    /**
     * Check downloaded snapshot from WP Reset Cloud
     *
     * @param string snapshot uid
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_download_check($uid)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        $file_checksum = $this->file_checksum($snapshot_file);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download_check',
                'snapshot_uid' => $uid,
                'checksum' => $file_checksum
            )
        );

        if ($response['success'] == true) {
            $steps = $wp_reset->do_import_snapshot($snapshot_file, true);
            if (is_wp_error($steps)) {
                return $steps;
            }

            return array('parameters' => $uid, 'action' => 'import_snapshot_steps', 'steps' => $steps); // AJAX response, set do_import_snapshot $ajax to true
        } else {
            unlink($snapshot_file);

            if (isset($response['data'])) {
                return new WP_Error('1', $response['data']);
            } else {
                return new WP_Error('1', 'An error occurred verifying the snapshot from the cloud.');
            }
        }
    } // wpreset_snapshot_download_check


    /**
     * Register snapshot to WP Reset Cloud
     *
     * @param array snapshot data
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_upload($snapshot)
    {
        global $wp_reset;
        $wp_reset->log('info', 'Cloud: Uploading snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' to WP Reset Cloud.');

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot['uid'] . '.zip');
        if (!file_exists($snapshot_file)) {
            $res = $wp_reset->do_export_snapshot($snapshot['uid']);
            if (is_wp_error($res)) {
                return $res;
            }
        }

        $wp_reset->log('info', 'Cloud: Snapshot data: ' . serialize($snapshot));

        $checksum = $this->file_checksum($snapshot_file);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'register_snapshot',
                'location' => 'wpreset',
                'checksum' => $checksum,
                'filesize' => filesize($snapshot_file),
                'snapshot_uid' => $snapshot['uid'],
                'snapshot_data' => serialize($snapshot)
            )
        );

        $wp_reset->log('info', 'Cloud: Registering snapshot ' . $snapshot['uid'] . ' ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' for upload to WP Reset Cloud.');
        $wp_reset->log('info', 'Cloud: snapshot ' . $snapshot['uid'] . ' file size is ' . filesize($snapshot_file));
        $wp_reset->log('info', 'Cloud: snapshot ' . $snapshot['uid'] . ' checksum is ' . $checksum);


        if ($response['success'] == true) {
            $wp_reset->log('success', 'Snapshot registered succesfully');
            return array('parameters' => $snapshot['uid'], 'action' => 'upload_part', 'continue' => 1, 'position' => 0);
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Snapshot registration failed: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Snapshot registration failed: Unknown error');
            return new WP_Error('1', 'An error occurred registering the snapshot on the cloud.');
        }
    } // wpreset_snapshot_upload


    /**
     * Upload collection item in WP Reset Cloud
     *
     * @param int item id
     * @param string collection item file path
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_collection_upload($item_id, $item_file)
    {

        $file = fopen($item_file, 'r');
        if ($file === false) {
            return new WP_Error('1', 'Failed to read collection item ZIP file.');
        }

        $file_contents = fread($file, filesize($item_file));

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_upload',
                'location' => 'wpreset',
                'part_checksum' => md5($file_contents),
                'item_id' => $item_id
            ),
            $file_contents
        );

        if ($response['success'] == true) {
            if (!empty($response['data'])) {
                return $response['data'];
            }
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred uploading the item ZIP to the cloud.');
        }

        return true;
    } // wpreset_collection_upload


    /**
     * Delete collection item from WP Reset Cloud
     *
     * @param int item id
     *
     * @return array|WP_Error array with response or WP_Error
     */
    public function wpreset_collection_item_delete($item_id)
    {
        global $wp_reset;

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_delete',
                'location' => 'wpreset',
                'item_id' => $item_id['id']
            )
        );

        if ($response['success'] == true) {
            return $response['data'];
        } else if (isset($response['data'])) {
            $wp_reset->log('error', $response['data']);
        } else {
            $wp_reset->log('error', 'An error occurred deleting the collection item from the cloud.');
        }
    } // wpreset_collection_upload


    /**
     * Upload snapshot part to WP Reset Cloud
     *
     * @param string snapshot uid
     * @param int position in bytes to start reading file contents
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_upload_part($uid, $position)
    {
        global $wp_reset;
        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');
        $wp_reset->log('info', 'Cloud: Uploading part for ' . $uid . ' from ' . WP_Reset_Utility::format_size($position) . ' to ' . WP_Reset_Utility::format_size($this->stream_size) . ' of ' . WP_Reset_Utility::format_size(filesize($snapshot_file)));
        $file = fopen($snapshot_file, 'r');
        if ($file === false) {
            return new WP_Error('1', 'Failed to read local snapshot file.');
        }

        fseek($file, $position);

        $part = fread($file, $this->stream_size);
        $file_size = filesize($snapshot_file);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'put_part',
                'part_checksum' => md5($part),
                'snapshot_uid' => $uid
            ),
            $part
        );

        if ($response['success'] != true) {
            if (isset($response['data'])) {
                $wp_reset->log('error', 'Cloud: Error uploading part for ' . $uid . ' from ' . WP_Reset_Utility::format_size($position) . ' to ' . WP_Reset_Utility::format_size($this->stream_size) . ': ' . $response['data']);
                return new WP_Error('1', $response['data']);
            } else {
                $wp_reset->log('error', 'Cloud: Error uploading part for ' . $uid . ' from ' . WP_Reset_Utility::format_size($position) . ' to ' . WP_Reset_Utility::format_size($this->stream_size) . ': unknown error');
                return new WP_Error('1', 'An error occurred transferring the snapshot part to the cloud.');
            }
        }

        $wp_reset->log('info', 'Cloud: ' . $response['data']);

        if (!feof($file)) {
            $wp_reset->log('info', 'Cloud: Successfully uploaded part for ' . $uid . ' from ' . WP_Reset_Utility::format_size($position) . ' to ' . WP_Reset_Utility::format_size($this->stream_size) . ', continue with next part from ' . WP_Reset_Utility::format_size($position + $this->stream_size));
            return array('parameters' => $uid, 'action' => 'upload_part', 'continue' => 1, 'position' => ($position + $this->stream_size), 'message' => 'Uploaded ' . WP_Reset_Utility::format_size($position + $this->stream_size) . ' of ' . WP_Reset_Utility::format_size($file_size));
        } else {
            $wp_reset->log('info', 'Cloud: Successfully uploaded part for ' . $uid . ' from ' . WP_Reset_Utility::format_size($position) . ' to ' . WP_Reset_Utility::format_size($this->stream_size));
            $wp_reset->log('info', 'Cloud: No more parts left for ' . $uid);
            return array('parameters' => $uid, 'action' => 'check_snapshot', 'continue' => 1, 'message' => 'Verifying snapshot');
        }
    } // wpreset_snapshot_upload_part


    /**
     * Check if snapshot has been uploaded succesfully
     *
     * @param string snapshot uid
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_check($uid)
    {
        global $wp_reset;

        $options = $wp_reset->get_options();
        $wp_reset->log('info', 'Cloud: Checking upload result for snapshot ' . $uid);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'check_snapshot',
                'snapshot_uid' => $uid
            )
        );

        if ($response['success'] == true) {
            $wp_reset->log('info', 'Cloud: Snapshot ' . $uid . ' upload has been uploaded succesfully!');
            if ($options['snapshots_upload_delete']) {
                $res = $wp_reset->do_delete_snapshot($uid);
                if (is_wp_error($res)) {
                    $wp_reset->log('error', 'Failed to delete the snapshot ' . $uid . ' from local website: ' . $res->get_error_message());
                } else {
                    $wp_reset->log('success', 'Deleted snapshot ' . $uid . ' from local website');
                }
                return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
            } else {
                return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
            }
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Cloud: An error occured checking uploaded snapshot ' . $uid . ': ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Cloud: An error occured checking uploaded snapshot ' . $uid . ': unknown error');
            return new WP_Error('1', 'An error occurred transferring the snapshot to the cloud.');
        }

        return true;
    } // wpreset_snapshot_check


    /**
     * Refresh Snapshots From WP Reset Cloud
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_refresh()
    {
        global $wp_reset;
        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshots_list',
                'location' => $this->cloud_service
            )
        );

        if ($response['success'] == true) {
            $this->snapshots = $response['data'];
            $autouploader = $wp_reset->get_autouploader();
            foreach ($this->snapshots as $snapshot) {
                if (array_key_exists('snapshots', $autouploader) && array_key_exists($snapshot['uid'], $autouploader['snapshots'])) {
                    unset($autouploader['snapshots'][$snapshot['uid']]);
                }
            }

            if (array_key_exists('snapshots', $autouploader)) {
                foreach ($autouploader['snapshots'] as $uid => $snapshot) {
                    if ($snapshot['status'] == 'error') {
                        $autouploader['snapshots'][$uid] = array(
                            'steps' => array(),
                            'status' => 'pending',
                            'message' => 'Pending upload'
                        );
                    }
                }
            }
            $wp_reset->update_options('autouploader', $autouploader);

            update_option('wp-reset-cloud-snapshots', $this->snapshots);
            $wp_reset->log('info', 'Cloud: Snapshots Refreshed successfully');
            $wp_reset->log('info', serialize($this->snapshots));
            return array('message' => 'Snapshots Refreshed');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Cloud: An error occurred refreshing the snapshots list from the cloud: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Cloud: An error occurred refreshing the snapshots list from the cloud.');
            return new WP_Error('1', 'An error occurred refreshing the snapshots list from the cloud.');
        }
    } // wpreset_snapshot_refresh


    /**
     * Delete Snapshot From WP Reset Cloud
     *
     * @param string snapshot uid
     *
     * @return array|WP_Error array of next AJAX step or WP_Error
     */
    public function wpreset_snapshot_delete($snapshot_uid)
    {
        global $wp_reset;
        $wp_reset->log('info', 'Cloud: Deleteting snapshot ' . $snapshot_uid);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'delete_snapshot',
                'snapshot_uid' => $snapshot_uid
            )
        );

        if ($response['success'] == true) {
            $wp_reset->log('info', 'Cloud: Snapshot ' . $snapshot_uid . ' deleted successfully!');
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Cloud: Error deleteting snapshot ' . $response['data']);
        } else {
            $wp_reset->log('error', 'Cloud: Error deleteting snapshot: unknown error');
        }
    } // wpreset_snapshot_delete


    /** 
   #####################
   Google Drive
   #####################
     */


    /**
     * Get Google Drive client
     * This also refreshes the token if needed and checks and creates required folders on every call as users might change them at any time so it is unreliable to assume they exists
     *
     * @return object Google Drive client
     */
    public function get_gdrive_client()
    {
        global $wp_reset;

        $options = $wp_reset->get_options();

        require_once $wp_reset->plugin_dir . 'libs/vendor/autoload.php';

        if ($this->gdrive == false) {
            $google_client = new Google_Client();
            $google_client->addScope('https://www.googleapis.com/auth/drive.file');

            try {
                $google_client->setAccessToken($options['cloud_data']['gdrive']['token']);
            } catch (exception $e) {
                $options['cloud_data']['gdrive']['token'] = false;
                $options['cloud_service'] = false;
                $wp_reset->update_options('options', $options);
                $wp_reset->log('error', 'An error occurred connecting to Google client: ' . $e->getMessage());
                return new WP_Error(1, 'An error occurred connecting to Google client: ' . $e->getMessage());
            }

            if ($google_client->isAccessTokenExpired()) {
                $new_token = $this->cloud_gdrive_refresh_token($options['cloud_data']['gdrive']['token']);
                if (is_wp_error($new_token)) {
                    return $new_token;
                }

                $old_token = json_decode($options['cloud_data']['gdrive']['token'], true);
                $new_token['refresh_token'] = $old_token['refresh_token'];
                $options['cloud_data']['gdrive']['token'] = json_encode($new_token);

                $google_client->setAccessToken($options['cloud_data']['gdrive']['token']);
                if ($google_client->isAccessTokenExpired()) {
                    $wp_reset->log('error', 'New Gooogle Drive token is not valid!');
                    return new WP_Error(1, 'New Gooogle Drive token is not valid!');
                }

                $wp_reset->update_options('options', $options);
            }
            $this->gdrive = new Google_Service_Drive($google_client);
        }

        $website_folder_name = $this->get_website_folder_from_url();
        $options['cloud_data']['gdrive']['folders']['wpr'] = $this->cloud_gdrive_get_folder('WP Reset');
        $options['cloud_data']['gdrive']['folders']['collections'] = $this->cloud_gdrive_get_folder('collections', $options['cloud_data']['gdrive']['folders']['wpr']);
        $options['cloud_data']['gdrive']['folders']['snapshots'] = $this->cloud_gdrive_get_folder('snapshots', $options['cloud_data']['gdrive']['folders']['wpr']);
        $options['cloud_data']['gdrive']['folders']['website'] = $this->cloud_gdrive_get_folder($website_folder_name, $options['cloud_data']['gdrive']['folders']['snapshots']);

        $wp_reset->update_options('options', $options);

        return $this->gdrive;
    } // get_gdrive_client


    /**
     * Get Google Drive folder object, create it if it does not exist
     *
     * @param string $folder name
     * @param string|object $parent folder ID or Google_Service_Drive_DriveFile
     * 
     * @return object Google_Service_Drive_DriveFile
     */
    public function cloud_gdrive_get_folder($name = false, $parent = false)
    {
        if ($this->gdrive == false) {
            $this->gdrive = $this->get_gdrive_client();
        }

        $name_query = $parent_query = '';

        if ($name !== false) {
            $name_query = " and name = '" . $name . "' ";
        }

        if ($parent !== false) {
            if (is_a($parent, 'Google_Service_Drive_DriveFile')) {
                $parent_id = $parent->id;
            } else {
                $parent_id = $parent;
            }
            $parent_query = " and '" . $parent_id . "' in parents ";
        }

        $response = $this->gdrive->files->listFiles(array(
            'spaces' => 'drive',
            'q' => "mimeType='application/vnd.google-apps.folder' and trashed = false " . $name_query . $parent_query,
            'fields' => 'nextPageToken, files(id, name)',
            'pageSize' => 10
        ));

        $results = $response->getFiles();

        if (empty($results) || !is_array($results)) {
            $folder = $this->cloud_gdrive_create_folder($name, $parent);
            return $folder->id;
        }

        return $results[0]->id;
    } // cloud_gdrive_get_folder


    /**
     * Create Google Drive folder
     *
     * @param string $folder name
     * @param string|object $parent folder ID or Google_Service_Drive_DriveFile
     * 
     * @return object Google_Service_Drive_DriveFile
     */
    public function cloud_gdrive_create_folder($name, $parent)
    {
        if ($this->gdrive == false) {
            $this->gdrive = $this->get_gdrive_client();
        }

        $file = new Google_Service_Drive_DriveFile();
        $file->setName($name);
        $file->setMimeType("application/vnd.google-apps.folder");

        if ($parent !== false) {
            if (is_a($parent, 'Google_Service_Drive_DriveFile')) {
                $parent_id = $parent->id;
            } else {
                $parent_id = $parent;
            }
            $file->setParents(array($parent_id));
        }

        $result = $this->gdrive->files->create($file);

        return $result;
    } // cloud_gdrive_create_folder


    /**
     * Refresh Google Drive token
     *
     * @param string $access_token JSON
     * 
     * @return array|WP_Error new $access_token returned by cloud or WP_Error
     */
    public function cloud_gdrive_refresh_token($access_token)
    {

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'cloud_gdrive_refresh_token',
                'access_token' => $access_token
            )
        );

        if ($response['success'] == true) {
            return $response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred refreshing the Google Drive token.');
        }

        return true;
    } // cloud_gdrive_refresh_token


    /**
     * Google Drive Upload collection item
     *
     * @param int $item_id ID of collection item
     * @param string $item_file path of collection item
     * 
     * @return string|WP_Error we check for WP_Error, otherwise it was uploaded successfully
     */
    public function gdrive_collection_upload($item_id, $item_file)
    {
        global $wp_reset;

        if ($this->gdrive == false) {
            $this->gdrive = $this->get_gdrive_client();
        }

        $options = $wp_reset->get_options();

        $remote_filename = $item_id . '_' . basename($item_file);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $remote_filename,
            'parents' => array($options['cloud_data']['gdrive']['folders']['collections'])
        ));
        $content = file_get_contents($item_file);
        $upload_result = $this->gdrive->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => mime_content_type($item_file),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));

        if (is_array($upload_result) && array_key_exists('error', $upload_result)) {
            wp_send_json_error();
            return new WP_Error(1, 'An error occurred uploading the file to Google Drive: ' . $upload_result['error_description'] . '(' . $upload_result['error'] . ')');
        }

        $userPermission = new Google_Service_Drive_Permission(array(
            'type' => 'anyone',
            'role' => 'reader',
        ));
        $enable_link_result = $this->gdrive->permissions->create($upload_result->id, $userPermission, array('fields' => 'id'));

        if (is_array($enable_link_result) && array_key_exists('error', $enable_link_result)) {
            wp_send_json_error();
            return new WP_Error(1, 'An error occurred creating the file link on Google Drive: ' . $enable_link_result['error_description'] . '(' . $enable_link_result['error'] . ')');
        }

        $zip_url = 'https://drive.google.com/uc?id=' . $upload_result->id . '&export=download';

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_upload',
                'location' => 'gdrive',
                'zip_filename' => $upload_result->id,
                'zip_url' => $zip_url,
                'filesize' => filesize($item_file),
                'item_id' => $item_id
            )
        );

        if ($response['success'] == true) {
            return @$response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred registering the item ZIP to the cloud.');
        }
    } // gdrive_collection_upload


    /**
     * Google Drive Upload snapshot to cloud
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function gdrive_snapshot_upload($snapshot)
    {
        global $wp_reset;

        $wp_reset->log('info', 'Cloud: Uploading snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' to Google Drive.');
        $wp_reset->log('info', 'Cloud: Snapshot data: ' . serialize($snapshot));

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot['uid'] . '.zip');
        if (!file_exists($snapshot_file)) {
            $res = $wp_reset->do_export_snapshot($snapshot['uid']);
            if (is_wp_error($res)) {
                return $res;
            }
        }

        if ($this->gdrive == false) {
            $this->gdrive = $this->get_gdrive_client();
            if (is_wp_error($this->gdrive)) {
                return $this->gdrive;
            }
        }

        $options = $wp_reset->get_options();

        $remote_filename = basename($snapshot_file);

        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $remote_filename,
            'parents' => array($options['cloud_data']['gdrive']['folders']['website'])
        ));

        $content = file_get_contents($snapshot_file);
        $upload_result = $this->gdrive->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => mime_content_type($snapshot_file),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));

        if (is_array($upload_result) && array_key_exists('error', $upload_result)) {
            $wp_reset->log('error', 'Cloud: Snapshot upload to Google Drive as ' . $remote_filename . ':' . $upload_result['error_description'] . '(' . $upload_result['error'] . ')');
            return new WP_Error(1, 'An error occurred uploading the snapshot to Google Drive: ' . $upload_result['error_description'] . '(' . $upload_result['error'] . ')');
        }

        $wp_reset->log('success', 'Cloud: Snapshot upload to Google Drive was successfully');
        $wp_reset->log('info', 'Cloud: Cloud: Registering snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' in WP Reset Cloud.');

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'register_snapshot',
                'snapshot_uid' => $snapshot['uid'],
                'snapshot_data' => serialize($snapshot),
                'location' => 'gdrive',
                'filesize' => filesize($snapshot_file),
                'filename' => $upload_result->id
            )
        );

        if ($response['success'] == true) {
            $wp_reset->log('success', 'Snapshot uploaded succesfully');
            if ($options['snapshots_upload_delete']) {
                $res = $wp_reset->do_delete_snapshot($snapshot['uid']);
                if (is_wp_error($res)) {
                    $wp_reset->log('error', 'Failed to delete the snapshot from local website: ' . $res->get_error_message());
                } else {
                    $wp_reset->log('success', 'Deleted snapshot from local website');
                }
            }
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Snapshot registration failed: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Snapshot registration failed: Unknown error');
            return new WP_Error('1', 'An error occurred registering the snapshot on the cloud.');
        }
    } // gdrive_snapshot_upload


    /**
     * Google Drive download snapshot from cloud
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function gdrive_snapshot_download($snapshot)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot . '.zip');
        if (file_exists($snapshot_file)) {
            unlink($snapshot_file);
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download',
                'snapshot_uid' => $snapshot
            )
        );

        if ($response['success'] == true) {
            if ($this->gdrive == false) {
                $this->gdrive = $this->get_gdrive_client();
                if (is_wp_error($this->gdrive)) {
                    return $this->gdrive;
                }
            }

            $cloud_snapshots = $this->get_cloud_snapshots();

            try {
                $content = $this->gdrive->files->get($cloud_snapshots[$snapshot]['cloud_path'], array("alt" => "media"));
                $snapshot_file_handle = fopen($snapshot_file, "w+");
                fwrite($snapshot_file_handle, $content->getBody());
                fclose($snapshot_file_handle);
            } catch (Exception $e) {
                $error = json_decode($e->getMessage());
                return new WP_Error(1, 'An error occurred downloading the snapshot file from Google Drive: ' . $error->error->message);
            }

            return array('parameters' => $snapshot, 'action' => 'snapshot_download_check', 'continue' => 1, 'message' => 'Checking downloaded snapshot');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the snapshot data from the cloud.');
        }
    } // gdrive_snapshot_download


    /**
     * Google Drive check download and initiate import of snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function gdrive_snapshot_download_check($uid)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        if (file_exists($snapshot_file)) {
            $steps = $wp_reset->do_import_snapshot($snapshot_file, true);
            if (is_wp_error($steps)) {
                return $steps;
            }

            return array('parameters' => $uid, 'action' => 'import_snapshot_steps', 'steps' => $steps); // AJAX response, set do_import_snapshot $ajax to true
        } else {
            unlink($snapshot_file);
            return new WP_Error('1', 'An error occurred verifying the snapshot from the cloud.');
        }
    } // gdrive_snapshot_download_check


    /**
     * Google Drive delete snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function gdrive_snapshot_delete($snapshot_uid)
    {

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'delete_snapshot',
                'snapshot_uid' => $snapshot_uid
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            if ($this->gdrive == false) {
                $this->gdrive = $this->get_gdrive_client();
            }

            $cloud_snapshots = $this->get_cloud_snapshots();

            try {
                $this->gdrive->files->delete($cloud_snapshots[$snapshot_uid]['cloud_path']);
            } catch (Exception $e) {
                $error = json_decode($e->getMessage());
                return new WP_Error('1', 'An error occurred deleting the snapshot from cloud: ' . $error->error->errors[0]->message);
            }

            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred deleting the snapshot from the cloud.');
        }
    } // gdrive_snapshot_delete


    /**
     * Google Drive delete collection item
     *
     * @param int collection_id
     * @param int collection_item_data
     * 
     * @return bool|WP_Error true or WP_Error
     */
    public function gdrive_collection_item_delete($collection_id, $collection_item_data)
    {
        if ($this->gdrive == false) {
            $this->gdrive = $this->get_gdrive_client();
        }

        try {
            $this->gdrive->files->delete($collection_item_data['zip_filename']);
        } catch (Exception $e) {
            return new WP_Error('1', 'An error occurred deleting the collection item from cloud: ' . $e->getMessage());
        }

        return true;
    } // gdrive_collection_item_delete


    /** 
   #####################
   Dropbox
   #####################
     */


    /**
     * Get Dropbox client
     *
     * @return object Dropbox client
     */
    public function get_dropbox_client($auth = false)
    {
        global $wp_reset;

        $options = $wp_reset->get_options();

        require_once $wp_reset->plugin_dir . 'libs/dropbox.php';

        if (!isset($options['cloud_data'])) {
            $token = false;
        } else {
            $token = unserialize(stripslashes(json_decode($options['cloud_data']['dropbox']['token'])));
        }

        if (empty($token) || !is_array($token)) {
            return new WP_Error(1, 'Dropbox authentication token is not valid.');
        }

        if ($this->dropbox == false) {
            try {
                $this->dropbox = new WPRDropboxClient($token);
            } catch (WPRDropboxException $e) {
                $options['cloud_data']['dropbox'] = false;
                $options['cloud_service'] = 'none';
                $wp_reset->update_options('options', $options);
                return new WP_Error(1, 'An error occurred connecting to Dropbox client: ' . $e->getMessage());
            }
        }

        return $this->dropbox;
    } // get_dropbox_client


    /**
     * Dropbox upload collection
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_collection_upload($item_id, $item_file)
    {

        $this->dropbox = $this->get_dropbox_client(true);

        if (is_wp_error($this->dropbox)) {
            return $this->dropbox;
        }

        $remote_filename = 'collections/' . $item_id . '_' . basename($item_file);

        if (empty($this->dropbox->UploadFile($item_file, $remote_filename))) {
            return new WP_Error(1, 'An error occurred uploading the file to Dropbox.');
        }

        $zip_url = str_replace('dl=0', 'dl=1', $this->dropbox->GetLink($remote_filename, true));

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_upload',
                'location' => 'dropbox',
                'zip_filename' => $remote_filename,
                'zip_url' => $zip_url,
                'filesize' => filesize($item_file),
                'item_id' => $item_id
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            return @$response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred registering the item ZIP to the cloud.');
        }
    } // dropbox_collection_upload


    /**
     * Dropbox upload snapshot
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_snapshot_upload($snapshot)
    {
        global $wp_reset;
        $options = $wp_reset->get_options();
        $wp_reset->log('info', 'Cloud: Uploading snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' to Dropbox.');
        $wp_reset->log('info', 'Cloud: Snapshot data: ' . serialize($snapshot));

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot['uid'] . '.zip');
        if (!file_exists($snapshot_file)) {
            $res = $wp_reset->do_export_snapshot($snapshot['uid']);
            if (is_wp_error($res)) {
                return $res;
            }
        }

        $this->dropbox = $this->get_dropbox_client(true);

        if (is_wp_error($this->dropbox)) {
            return $this->dropbox;
        }

        $remote_filename = 'snapshots/' . sanitize_title_with_dashes(get_home_url()) . '_' . $snapshot['uid'] . '_' . basename($snapshot_file);

        if (empty($this->dropbox->UploadFile($snapshot_file, $remote_filename))) {
            $wp_reset->log('error', 'Snapshot upload to Dropbox as ' . $remote_filename . ' failed: Unknown error');
            return new WP_Error(1, 'An error occurred uploading the file to Dropbox.');
        }

        $wp_reset->log('success', 'Snapshot upload to Dropbox was successfull');
        $wp_reset->log('info', 'Cloud: Registering snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' in WP Reset Cloud.');

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'register_snapshot',
                'snapshot_uid' => $snapshot['uid'],
                'snapshot_data' => serialize($snapshot),
                'location' => 'dropbox',
                'filesize' => filesize($snapshot_file),
                'filename' => $remote_filename
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $wp_reset->log('success', 'Snapshot registered succesfully');
            if ($options['snapshots_upload_delete']) {
                $res = $wp_reset->do_delete_snapshot($snapshot['uid']);
                if (is_wp_error($res)) {
                    $wp_reset->log('error', 'Failed to delete the snapshot from local website: ' . $res->get_error_message());
                } else {
                    $wp_reset->log('success', 'Deleted snapshot from local website');
                }
            }
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Snapshot registration failed: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Snapshot registration failed: Unknown error');
            return new WP_Error('1', 'An error occurred registering the snapshot on the cloud.');
        }
    } // dropbox_snapshot_upload


    /**
     * Dropbox download snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_snapshot_download($snapshot)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot . '.zip');
        if (file_exists($snapshot_file)) {
            unlink($snapshot_file);
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download',
                'snapshot_uid' => $snapshot
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->dropbox = $this->get_dropbox_client(true);

            if (is_wp_error($this->dropbox)) {
                return $this->dropbox;
            }

            $cloud_snapshots = $this->get_cloud_snapshots();

            if (empty($this->dropbox->DownloadFile('/' . $cloud_snapshots[$snapshot]['cloud_path'], $snapshot_file))) {
                return new WP_Error(1, 'An error occurred downloading the file from Dropbox.');
            }

            return array('parameters' => $snapshot, 'action' => 'snapshot_download_check', 'continue' => 1, 'message' => 'Checking downloaded snapshot');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the snapshot data from the cloud.');
        }
    } // dropbox_snapshot_download


    /**
     * Dropbox check download and initiate import of snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_snapshot_download_check($uid)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        if (file_exists($snapshot_file)) {
            $steps = $wp_reset->do_import_snapshot($snapshot_file, true);
            if (is_wp_error($steps)) {
                return $steps;
            }

            return array('parameters' => $uid, 'action' => 'import_snapshot_steps', 'steps' => $steps); // AJAX response, set do_import_snapshot $ajax to true
        } else {
            unlink($snapshot_file);
            return new WP_Error('1', 'An error occurred verifying the snapshot from the cloud.');
        }
    } // dropbox_snapshot_download_check


    /**
     * Dropbox delete snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_snapshot_delete($snapshot_uid)
    {
        global $wp_reset;
        $wp_reset->log('info', 'Cloud: Deleting snapshot ' . $wp_reset->log_format_snapshot_name($snapshot_uid) . ' from Dropbox.');

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'delete_snapshot',
                'snapshot_uid' => $snapshot_uid
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->dropbox = $this->get_dropbox_client(true);
            $cloud_snapshots = $this->get_cloud_snapshots();
            if (is_wp_error($this->dropbox)) {
                return $this->dropbox;
            }

            try {
                $this->dropbox->Delete('/' . $cloud_snapshots[$snapshot_uid]['cloud_path']);
            } catch (WPRDropboxException $e) {
                $wp_reset->log('error', 'Deleting snapshot ' . $wp_reset->log_format_snapshot_name($snapshot_uid) . ' from Dropbox failed: ' . $e->getMessage());
            }

            $wp_reset->log('success', 'Deleted snapshot ' . $wp_reset->log_format_snapshot_name($snapshot_uid) . ' from Dropbox successfully');
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Deleting snapshot ' . $wp_reset->log_format_snapshot_name($snapshot_uid) . ' from Dropbox failed: ' . $response['data']);            
        } else {
            $wp_reset->log('error', 'Deleting snapshot ' . $wp_reset->log_format_snapshot_name($snapshot_uid) . ' from Dropbox failed: Unknown error');            
        }
    } // dropbox_snapshot_delete


    /**
     * Dropbox collection item delete
     *
     * @param int collection_id
     * @param array collection item data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function dropbox_collection_item_delete($collection_id, $collection_item_data)
    {
        global $wp_reset;
        
        $this->dropbox = $this->get_dropbox_client(true);

        if (is_wp_error($this->dropbox)) {
            return $this->dropbox;
        }

        try {
            $this->dropbox->Delete('/' . $collection_item_data['zip_filename']);
        } catch (WPRDropboxException $e) {
            $wp_reset->log('error', 'An error occurred deleting the collection item from Dropbox: ' . $e->getMessage());
        }

        return true;
    } // pcloud_snapshot_delete

    /** 
   #####################
   pCloud
   #####################
     */


    /**
     * Get pCloud client
     *
     * @return object pCloud client
     */
    public function get_pcloud_client()
    {
        global $wp_reset;

        $options = $wp_reset->get_options();

        try {
            if ($this->cloud_service == 'pcloud') {
                require_once $wp_reset->plugin_dir . 'libs/pCloud/autoload.php';
            } else {
                require_once $wp_reset->plugin_dir . 'libs/pCloud-eu/autoload.php';
            }

            $website_folder_name = $this->get_website_folder_from_url();

            $collections_folder = $this->cloud_pcloud_get_folder('collections');
            if (is_wp_error($collections_folder)) {
                return $collections_folder;
            } else {
                $options['cloud_data'][$this->cloud_service]['folders']['collections'] = $this->cloud_pcloud_get_folder('collections');
            }

            $snapshots_folder = $this->cloud_pcloud_get_folder('snapshots');
            if (is_wp_error($snapshots_folder)) {
                return $snapshots_folder;
            } else {
                $options['cloud_data'][$this->cloud_service]['folders']['snapshots'] = $snapshots_folder;
            }

            $website_folder = $this->cloud_pcloud_get_folder($website_folder_name, $options['cloud_data'][$this->cloud_service]['folders']['snapshots']);
            if (is_wp_error($website_folder)) {
                return $website_folder;
            } else {
                $options['cloud_data'][$this->cloud_service]['folders']['website'] = $website_folder;
            }

            $wp_reset->update_options('options', $options);
        } catch (Exception $e) {
            if ($e->getMessage() == 'Invalid \'access_token\' provided.') {
                return new WP_Error(1, 'pCloud Token Expired<br /><a href="' . $this->cloud_authorize_get_url($this->cloud_service) . '">Click here to reauthorize pCloud</a>');
            }
            return new WP_Error(1, 'An error occurred connecting to pCloud: ' . $e->getMessage() . '|' . $e->getCode());
        }

        return true;
    } // get_pcloud_client


    /**
     * Get pCloud folder id, create it if it does not exist. Folders are created in WP Reset application folder
     *
     * @param string $folder name
     * @param string|object optional $parent folder object
     * 
     * @return int folder ID
     */
    public function cloud_pcloud_get_folder($name = false, $parent = 0)
    {
        if ($parent !== 0) {
            if (!is_object($parent)) {
                return false;
            }

            if (empty($parent->metadata->path)) {
                return new WP_Error(1, 'Could not find pCloud parent folder path');
            }
            $path = $parent->metadata->path . '/' . $name . '/*';
            $parent_folder = $parent->metadata->folderid;
        } else {
            $path = '/' . $name . '/*';
            $parent_folder = 0;
        }

        try {
            $pfolder = new pCloud\Folder();
            $folder = $pfolder->search($path);
        } catch (Exception $e) {
            $pcloudFolder = new pCloud\Folder();
            $folder = $pcloudFolder->create($name, $parent_folder);

            if (!is_object($folder) || !isset($folder->metadata)) {
                return new WP_Error(1, 'Could not create pCloud folder ' . $name . ': ' . $e->getMessage());
            }
        }

        if (!isset($folder->metadata)) {
            return new WP_Error(1, 'Could not create pCloud folder ' . $name);
        }

        return $folder;
    } // cloud_pcloud_get_folder


    /**
     * pCloud upload collection
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_collection_upload($collection_id, $item_id, $item_file)
    {
        global $wp_reset;

        $this->pcloud = $this->get_pcloud_client();

        $options = $wp_reset->get_options();

        if (is_wp_error($this->pcloud)) {
            return $this->pcloud;
        }

        $this->pcloud = $this->get_pcloud_client();

        $upload_checksum = $this->file_checksum($item_file);

        try {
            $pcloudFile = new pCloud\File();
            $fileMetadata = $pcloudFile->upload($item_file, $options['cloud_data'][$this->cloud_service]['folders']['collections']->metadata->folderid);
        } catch (Exception $e) {
            return new WP_Error(1, 'An error occurred uploading the collection item to pCloud: ' . $e->getMessage() . ' ' . $e->getCode());
        }

        try {
            $pcloudFile = new pCloud\File();
            $pcloudFile->download($fileMetadata->metadata->fileid, $wp_reset->export_dir_path());
            $download_checksum = $this->file_checksum($wp_reset->export_dir_path(basename($item_file)));
            if ($download_checksum != $upload_checksum) {
                throw new Exception('Upload Failed');
            }
        } catch (Exception $e) {
            $this->query_cloud_server(
                array(
                    'cloud_action' => 'collection_item_remove',
                    'collection_id' => $collection_id,
                    'collection_item_id' => $item_id
                )
            );
            
            return new WP_Error(1, 'An error occurred verifying the uploaded file.');
        }

        $zip_url = $pcloudFile->getLink($fileMetadata->metadata->fileid);

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_upload',
                'location' => $this->cloud_service,
                'zip_filename' => $fileMetadata->metadata->fileid,
                'zip_url' => $zip_url,
                'filesize' => filesize($item_file),
                'item_id' => $item_id
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            return @$response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred registering the item ZIP to the cloud.');
        }
    } // pcloud_collection_upload


    /**
     * pCloud upload snapshot
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_snapshot_upload($snapshot)
    {
        global $wp_reset;
        $wp_reset->log('info', 'Cloud: Uploading snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' to pCloud.');
        $wp_reset->log('info', 'Cloud: Snapshot data: ' . serialize($snapshot));

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot['uid'] . '.zip');
        if (!file_exists($snapshot_file)) {
            $res = $wp_reset->do_export_snapshot($snapshot['uid']);
            if (is_wp_error($res)) {
                return $res;
            }
        }


        $this->pcloud = $this->get_pcloud_client();
        $options = $wp_reset->get_options();


        if (is_wp_error($this->pcloud)) {
            return $this->pcloud;
        }

        $upload_checksum = $this->file_checksum($snapshot_file);

        try {
            $pcloudFile = new pCloud\File();
            $fileMetadata = $pcloudFile->upload($snapshot_file, $options['cloud_data'][$this->cloud_service]['folders']['website']->metadata->folderid);
        } catch (Exception $e) {
            $wp_reset->log('error', 'Snapshot upload to pCloud as ' . basename($snapshot_file) . ' failed: ' . $e->getMessage() . ' ' . $e->getCode());
            return new WP_Error(1, 'An error occurred uploading the snapshot to pCloud: ' . $e->getMessage() . ' ' . $e->getCode());
        }

        try {
            $pcloudFile = new pCloud\File();
            $pcloudFile->download($fileMetadata->metadata->fileid, $wp_reset->export_dir_path());
            $download_checksum = $this->file_checksum($wp_reset->export_dir_path(basename($snapshot_file)));
            if ($download_checksum != $upload_checksum) {
                throw new Exception('Upload Failed');
            }
        } catch (Exception $e) {
            $response = $this->query_cloud_server(
                array(
                    'cloud_action' => 'delete_snapshot',
                    'snapshot_uid' => $snapshot['uid']
                )
            );
            $wp_reset->log('error', 'Snapshot upload to pCloud failed verification');
            return new WP_Error(1, 'An error occurred verifying the uploaded snapshot.');
        }

        $wp_reset->log('success', 'Snapshot upload to pCloud was successfull');
        $wp_reset->log('info', 'Cloud: Registering snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' in WP Reset Cloud.');

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'register_snapshot',
                'snapshot_uid' => $snapshot['uid'],
                'snapshot_data' => serialize($snapshot),
                'location' => $this->cloud_service,
                'filesize' => filesize($snapshot_file),
                'filename' => $fileMetadata->metadata->fileid
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $wp_reset->log('success', 'Snapshot registered in WP Reset Cloud succesfully');
            if ($options['snapshots_upload_delete']) {
                $res = $wp_reset->do_delete_snapshot($snapshot['uid']);
                if (is_wp_error($res)) {
                    $wp_reset->log('error', 'Failed to delete the snapshot from local website: ' . $res->get_error_message());
                } else {
                    $wp_reset->log('success', 'Deleted snapshot from local website');
                }
            }
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Snapshot registration failed: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Snapshot registration failed: Unknown error');
            return new WP_Error('1', 'An error occurred registering the snapshot on the cloud.');
        }
    } // pcloud_snapshot_upload


    /**
     * pCloud download snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_snapshot_download($snapshot)
    {
        global $wp_reset;

        $license = $wp_reset->get_license();

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot . '.zip');
        if (file_exists($snapshot_file)) {
            unlink($snapshot_file);
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download',
                'snapshot_uid' => $snapshot
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->pcloud = $this->get_pcloud_client();

            if (is_wp_error($this->pcloud)) {
                return $this->pcloud;
            }

            $cloud_snapshots = $this->get_cloud_snapshots();

            $pcloudFile = new pCloud\File();
            $download_result = $pcloudFile->download((int)$cloud_snapshots[$snapshot]['cloud_path'], dirname($snapshot_file));

            if (!$download_result) {
                return new WP_Error(1, 'An error occurred downloading the file from pCloud.');
            }

            return array('parameters' => $snapshot, 'action' => 'snapshot_download_check', 'continue' => 1, 'message' => 'Checking downloaded snapshot');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the snapshot data from the cloud.');
        }
    } // pcloud_snapshot_download


    /**
     * pCloud check download and initiate import of snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_snapshot_download_check($uid)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        if (file_exists($snapshot_file)) {
            $steps = $wp_reset->do_import_snapshot($snapshot_file, true);
            if (is_wp_error($steps)) {
                return $steps;
            }

            return array('parameters' => $uid, 'action' => 'import_snapshot_steps', 'steps' => $steps); // AJAX response, set do_import_snapshot $ajax to true
        } else {
            unlink($snapshot_file);
            return new WP_Error('1', 'An error occurred verifying the snapshot from the cloud.');
        }
    } // pcloud_snapshot_download_check


    /**
     * pCloud delete snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_snapshot_delete($snapshot_uid)
    {
        global $wp_reset;
        
        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'delete_snapshot',
                'snapshot_uid' => $snapshot_uid
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->pcloud = $this->get_pcloud_client(true);
            $cloud_snapshots = $this->get_cloud_snapshots();
            if (is_wp_error($this->pcloud)) {
                return $this->pcloud;
            }

            try {
                $pcloudFile = new pCloud\File();
                $pcloudFile->delete((int)$cloud_snapshots[$snapshot_uid]['cloud_path']);
            } catch (Exception $e) {
                $wp_reset->log('error', 'An error occurred deleting the snapshot from pCloud: ' . $e->getMessage());
            }

            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', $response['data']);
        } else {
            $wp_reset->log('error', 'An error occurred deleting the snapshot from the cloud.');
        }
    } // pcloud_snapshot_delete


    /**
     * pCloud collection item delete
     *
     * @param int collection_id
     * @param array collection item data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function pcloud_collection_item_delete($collection_id, $collection_item_data)
    {
        global $wp_reset;
        
        $this->pcloud = $this->get_pcloud_client(true);

        if (is_wp_error($this->pcloud)) {
            return $this->pcloud;
        }

        try {
            $pcloudFile = new pCloud\File();
            $pcloudFile->delete((int)$collection_item_data['zip_filename']);
        } catch (Exception $e) {
            $wp_reset->log('error', 'An error occurred deleting the collection item from pCloud: ' . $e->getMessage());
        }

        return true;
    } // pcloud_snapshot_delete


    /** 
   #####################
   Icedrive
   #####################
     */


    /**
     * Get Icedrive client
     * This also creates required folders on every call as users might change them at any time so it is unreliable to assume they exists
     *
     * @return object Icedrive client
     */
    public function get_icedrive_client()
    {
        global $wp_reset;

        $options = $wp_reset->get_options();

        require_once $wp_reset->plugin_dir . 'libs/vendor/autoload.php';

        if ($this->icedrive == false) {
            $id_settings = array(
                'baseUri' => 'https://webdav.icedrive.io',
                'userName' => $options['cloud_data']['icedrive']['user'],
                'password' => $options['cloud_data']['icedrive']['pass']
            );

            $this->icedrive = new Sabre\DAV\Client($id_settings);
            $features = $this->icedrive->options();

            if (empty($features)) {
                $wp_reset->log('error', 'Icedrive: Failed to connect. Disabling Icedrive cloud service.');
                $options['cloud_data']['icedrive'] = array();
                $options['cloud_service'] = 'none';
                $wp_reset->update_options('options', $options);
                return new WP_Error(1, 'An error occurred connecting to Icedrive: Invalid username or WebDav Access key');
            } else if ($options['cloud_service'] != 'icedrive') {
                $options['cloud_service'] = 'icedrive';
                $wp_reset->update_options('options', $options);
            }

            if (!$this->icedrive_file_exists('/', 'wpreset/')) {
                $wp_reset->log('info', 'Icedrive: /wpreset/ directory doesn\'t exist. Attempting to create it.');
                $create_wpreset_directory = $this->icedrive_create_directory('wpreset/');
                if (is_wp_error($create_wpreset_directory)) {
                    $wp_reset->log('error', 'Icedrive: Failed to create /wpreset/ directory.');
                    return $create_wpreset_directory;
                }
                $wp_reset->log('info', 'Icedrive: /wpreset/ directory created.');
            }

            if (!$this->icedrive_file_exists('/wpreset/', 'snapshots/')) {
                $wp_reset->log('info', 'Icedrive: /wpreset/snapshots/ directory doesn\'t exist. Attempting to create it.');
                $create_snapshots_directory = $this->icedrive_create_directory('wpreset/snapshots/');
                if (is_wp_error($create_snapshots_directory)) {
                    $wp_reset->log('error', 'Icedrive: Failed to create /wpreset/snapshots/ directory.');
                    return $create_snapshots_directory;
                }
                $wp_reset->log('info', 'Icedrive: /wpreset/snapshots/ directory created.');
            }

            if (!$this->icedrive_file_exists('/wpreset/', 'collections/')) {
                $wp_reset->log('info', 'Icedrive: /wpreset/collections/ directory doesn\'t exist. Attempting to create it.');
                $create_snapshots_directory = $this->icedrive_create_directory('wpreset/collections/');
                if (is_wp_error($create_snapshots_directory)) {
                    $wp_reset->log('error', 'Icedrive: Failed to create /wpreset/collections/ directory.');
                    return $create_snapshots_directory;
                }
                $wp_reset->log('info', 'Icedrive: /wpreset/collections/ directory created.');
            }
        }

        $wp_reset->update_options('options', $options);

        return $this->icedrive;
    } // get_icedrive_client


    /**
     * Convert Icedrive XML response into an array
     *
     * @param string $path to look into
     * 
     * @return array of files found
     */
    function icedrive_response_to_array($response)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string(str_replace(':', '', $response));
        $xml = json_encode($xml);
        $xml = json_decode($xml, true);
        return $xml;
    }


    /**
     * Icedrive get list of files in $path
     *
     * @param string $path to look into
     * 
     * @return array of files found
     */
    function icedrive_get_files($path)
    {
        $files = array();
        $response = $this->icedrive->request('PROPFIND', $path);
        $response = $this->icedrive_response_to_array($response['body']);
        foreach ($response['dresponse'] as $file) {
            $files[] = $file['dhref'];
        }
        return $files;
    }


    /**
     * Icedrive check if file/folder exists
     *
     * @param string $path to look into
     * @param string $file name
     * 
     * @return bool
     */
    function icedrive_file_exists($path, $file)
    {
        $files = array();
        $response = $this->icedrive->request('PROPFIND', $path);
        $response = $this->icedrive_response_to_array($response['body']);

        if (empty($response['dresponse'])) {
            return false;
        }

        foreach ($response['dresponse'] as $f) {
            if (is_array($f) && array_key_exists('dhref', $f)) {
                $files[] = $f['dhref'];
            }
        }
        if (in_array($path . $file, $files)) {
            return true;
        }
        return false;
    }


    /**
     * Icedrive create directory
     *
     * @param string directory name
     * 
     * @return bool|WP_Error true on success WP_Error on error
     */
    function icedrive_create_directory($name)
    {
        try {
            $result = $this->icedrive->request('MKCOL', $name);
            if ($result['statusCode'] == 201) {
                return true;
            } else {
                $response = $this->icedrive_response_to_array($result['body']);
                if (array_key_exists('smessage', $response)) {
                    return new WP_Error(1, 'An error occured creating the <strong>' . $name . '</strong> directory: ' . $response['smessage']);
                } else {
                    return new WP_Error(1, 'An undocumented error occured creating the <strong>' . $name . '</strong> directory');
                }
            }
        } catch (Exception $e) {
            return new WP_Error(1, 'An error occured creating the <strong>' . $name . '</strong> directory: ' . $e->getMessage());
        }
        return true;
    }

    /**
     * Icedrive upload collection
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_collection_upload($collection_id, $item_id, $item_file)
    {
        global $wp_reset;

        $this->icedrive = $this->get_icedrive_client();

        if (is_wp_error($this->icedrive)) {
            return $this->icedrive;
        }

        $upload_checksum = $this->file_checksum($item_file);

        try {
            $this->icedrive->request('DELETE', '/wpreset/collections/' . basename($item_file));
            $response = $this->icedrive->request('PUT', '/wpreset/collections/' . basename($item_file), file_get_contents($item_file));
        } catch (Exception $e) {
            $wp_reset->log('error', 'Collection item upload to Icedrive as ' . basename($item_file) . ' failed: ' . $e->getMessage() . ' ' . $e->getCode());
            return new WP_Error(1, 'An error occurred uploading the collection item to Icedrive: ' . $e->getMessage() . ' ' . $e->getCode());
        }

        try {
            $icedriveFile = $this->icedrive->request('GET', '/wpreset/collections/' . basename($item_file));
            file_put_contents($wp_reset->export_dir_path(basename($item_file)), $icedriveFile['body']);

            $download_checksum = $this->file_checksum($wp_reset->export_dir_path(basename($item_file)));
            if ($download_checksum != $upload_checksum) {
                throw new Exception('Upload to Icedrive Failed');
            }
        } catch (Exception $e) {
            $this->query_cloud_server(
                array(
                    'cloud_action' => 'collection_item_remove',
                    'collection_id' => $collection_id,
                    'collection_item_id' => $item_id
                )
            );
            $wp_reset->log('error', 'Collection item upload to Icedrive failed verification:' . $e->getMessage());
            return new WP_Error(1, 'An error occurred verifying the uploaded collection item.');
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_upload',
                'location' => 'icedrive',
                'zip_filename' => '/wpreset/collections/' . basename($item_file),
                'zip_url' => '',
                'filesize' => filesize($item_file),
                'item_id' => $item_id
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            return @$response['data'];
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred registering the item ZIP to the cloud.');
        }
    } // icedrive_collection_upload


    /**
     * Icedrive upload snapshot
     *
     * @param array snapshot data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_snapshot_upload($snapshot)
    {
        global $wp_reset;
        $options = $wp_reset->get_options();

        $wp_reset->log('info', 'Cloud: Uploading snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' to Icedrive.');
        $wp_reset->log('info', 'Cloud: Snapshot data: ' . serialize($snapshot));

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot['uid'] . '.zip');
        if (!file_exists($snapshot_file)) {
            $res = $wp_reset->do_export_snapshot($snapshot['uid']);
            if (is_wp_error($res)) {
                return $res;
            }
        }

        $this->icedrive = $this->get_icedrive_client();

        if (is_wp_error($this->icedrive)) {
            return $this->icedrive;
        }

        $upload_checksum = $this->file_checksum($snapshot_file);

        try {
            $this->icedrive->request('DELETE', '/wpreset/snapshots/' . basename($snapshot_file));
            $response = $this->icedrive->request('PUT', '/wpreset/snapshots/' . basename($snapshot_file), file_get_contents($snapshot_file));
        } catch (Exception $e) {
            $wp_reset->log('error', 'Snapshot upload to Icedrive as ' . basename($snapshot_file) . ' failed: ' . $e->getMessage() . ' ' . $e->getCode());
            return new WP_Error(1, 'An error occurred uploading the snapshot to Icedrive: ' . $e->getMessage() . ' ' . $e->getCode());
        }

        try {
            $icedriveFile = $this->icedrive->request('GET', '/wpreset/snapshots/' . basename($snapshot_file));
            file_put_contents($wp_reset->export_dir_path(basename($snapshot_file)), $icedriveFile['body']);

            $download_checksum = $this->file_checksum($wp_reset->export_dir_path(basename($snapshot_file)));
            if ($download_checksum != $upload_checksum) {
                throw new Exception('Upload to Icedrive Failed');
            }
        } catch (Exception $e) {
            $response = $this->query_cloud_server(
                array(
                    'cloud_action' => 'delete_snapshot',
                    'snapshot_uid' => $snapshot['uid']
                )
            );
            $wp_reset->log('error', 'Snapshot upload to Icedrive failed verification:' . $e->getMessage());
            return new WP_Error(1, 'An error occurred verifying the uploaded snapshot.');
        }

        $wp_reset->log('success', 'Snapshot upload to Icedrive was successfull');
        $wp_reset->log('info', 'Cloud: Registering snapshot ' . $wp_reset->log_format_snapshot_name($snapshot['uid']) . ' in WP Reset Cloud.');

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'register_snapshot',
                'snapshot_uid' => $snapshot['uid'],
                'snapshot_data' => serialize($snapshot),
                'location' => 'icedrive',
                'filesize' => filesize($snapshot_file),
                'filename' => '/wpreset/snapshots/' . basename($snapshot_file)
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $wp_reset->log('success', 'Snapshot registered in WP Reset Cloud succesfully');
            if ($options['snapshots_upload_delete']) {
                $res = $wp_reset->do_delete_snapshot($snapshot['uid']);
                if (is_wp_error($res)) {
                    $wp_reset->log('error', 'Failed to delete the snapshot from local website: ' . $res->get_error_message());
                } else {
                    $wp_reset->log('success', 'Deleted snapshot from local website');
                }
            }
            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', 'Snapshot registration failed: ' . $response['data']);
            return new WP_Error('1', $response['data']);
        } else {
            $wp_reset->log('error', 'Snapshot registration failed: Unknown error');
            return new WP_Error('1', 'An error occurred registering the snapshot on the cloud.');
        }
    } // icedrive_snapshot_upload


    /**
     * Icedrive download snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_snapshot_download($snapshot)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $snapshot . '.zip');
        if (file_exists($snapshot_file)) {
            unlink($snapshot_file);
        }

        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'snapshot_download',
                'snapshot_uid' => $snapshot
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->icedrive = $this->get_icedrive_client();

            if (is_wp_error($this->icedrive)) {
                return $this->icedrive;
            }

            $icedriveFile = $this->icedrive->request('GET', '/wpreset/snapshots/' . basename($snapshot_file));
            file_put_contents($wp_reset->export_dir_path(basename($snapshot_file)), $icedriveFile['body']);

            return array('parameters' => $snapshot, 'action' => 'snapshot_download_check', 'continue' => 1, 'message' => 'Checking downloaded snapshot');
        } else if (isset($response['data'])) {
            return new WP_Error('1', $response['data']);
        } else {
            return new WP_Error('1', 'An error occurred retrieving the snapshot data from the cloud.');
        }
    } // icedrive_snapshot_download


    /**
     * Icedrive check download and initiate import of snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_snapshot_download_check($uid)
    {
        global $wp_reset;

        $snapshot_file = $wp_reset->export_dir_path('wp-reset-snapshot-' . $uid . '.zip');

        if (file_exists($snapshot_file)) {
            $steps = $wp_reset->do_import_snapshot($snapshot_file, true);
            if (is_wp_error($steps)) {
                return $steps;
            }

            return array('parameters' => $uid, 'action' => 'import_snapshot_steps', 'steps' => $steps); // AJAX response, set do_import_snapshot $ajax to true
        } else {
            unlink($snapshot_file);
            return new WP_Error('1', 'An error occurred verifying the snapshot from the cloud.');
        }
    } // icedrive_snapshot_download_check


    /**
     * Icedrive delete snapshot
     *
     * @param string snapshot uid
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_snapshot_delete($snapshot_uid)
    {
        global $wp_reset;
        
        $response = $this->query_cloud_server(
            array(
                'cloud_action' => 'delete_snapshot',
                'snapshot_uid' => $snapshot_uid
            )
        );

        if(is_wp_error($response)){
            return $response; 
        }

        if ($response['success'] == true) {
            $this->icedrive = $this->get_icedrive_client(true);
            $cloud_snapshots = $this->get_cloud_snapshots();
            if (is_wp_error($this->icedrive)) {
                return $this->icedrive;
            }

            try {
                $this->icedrive->request('DELETE', $cloud_snapshots[$snapshot_uid]['cloud_path']);
            } catch (Exception $e) {
                $wp_reset->log('error', 'An error occurred deleting the snapshot from Icedrive: ' . $e->getMessage());
            }

            return array('action' => 'snapshots_refresh', 'continue' => 1, 'message' => 'Refreshing cloud snapshots');
        } else if (isset($response['data'])) {
            $wp_reset->log('error', $response['data']);
        } else {
            $wp_reset->log('error', 'An error occurred deleting the snapshot from the cloud.');
        }
    } // icedrive_snapshot_delete


    /**
     * Icedrive collection item delete
     *
     * @param int collection_id
     * @param array collection item data
     * 
     * @return array|WP_Error next action for AJAX to call or WP_Error
     */
    public function icedrive_collection_item_delete($collection_id, $collection_item_data)
    {
        global $wp_reset;
        
        $this->icedrive = $this->get_icedrive_client(true);

        if (is_wp_error($this->icedrive)) {
            return $this->icedrive;
        }

        try {
            $this->icedrive->request('DELETE', $collection_item_data['zip_filename']);
        } catch (Exception $e) {
            $wp_reset->log('error', 'An error occurred deleting the collection item from Icedrive: ' . $e->getMessage());
        }

        return true;
    } // icedrive_collection_item_delete

    /** 
   #####################
   Utility
   #####################
     */


    /**
     * Get folder name based on current WordPress home_url
     *
     * @return string folder name
     */
    public function get_website_folder_from_url()
    {
        $url = parse_url(get_home_url());
        return sanitize_title_with_dashes($url['host']);
    } // get_website_folder_from_url


    public function file_checksum($path)
    {
        return md5_file($path);
    }
} // WP_Reset_Cloud
