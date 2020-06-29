<?php
/**
 * WebFactory Licensing Manager
 * (c) WebFactory Ltd
 * www.webfactoryltd.com
 */


if (false === class_exists('WF_Licensing')) {
  class WF_Licensing
  {
    public $prefix = '';
    private $licensing_servers = array();
    private $version = '';
    private $slug = '';
    private $basename = '';
    private $plugin_file = '';
    private $js_folder = '';
    protected $api_ver = 'v1/';
    protected $valid_forever = '2035-01-01';
    protected $unlimited_installs = 99999;
    public $debug = false;


    /**
     * Init licensing by setting up various params and hooking into actions.
     *
     * @param array $params Prefix, licensing_servers, version, plugin_file, skip_hooks
     *
     * @return void
     */
    function __construct($params)
    {
      $this->prefix = trim($params['prefix']);
      $this->licensing_servers = $params['licensing_servers'];
      $this->version = trim($params['version']);
      $this->slug = dirname(plugin_basename(trim($params['plugin_file'])));
      $this->basename = plugin_basename(trim($params['plugin_file']));
      $this->plugin_file = $params['plugin_file'];
      $this->debug = !empty($params['debug']);

      if ($params['js_folder']) {
        $this->js_folder = trim($params['js_folder']);
      } else {
        $this->js_folder = plugin_dir_url($this->plugin_file) . 'js/';
      }

      if (empty($params['skip_hooks'])) {
        register_activation_hook($this->plugin_file, array($this, 'activate_plugin'));
        register_deactivation_hook($this->plugin_file, array($this, 'deactivate_plugin'));

        add_filter('pre_set_site_transient_update_plugins', array($this, 'update_filter'));
        add_filter('plugins_api', array($this, 'update_details'), 100, 3);

        add_action('init', array($this, 'init'));

        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_validate', array($this, 'validate_ajax'));
        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_save', array($this, 'save_ajax'));
        add_action('wp_ajax_wf_licensing_' . $this->prefix . '_deactivate', array($this, 'deactivate_ajax'));
      }

      $this->log('__construct', $params, get_object_vars($this));
    } // __construct


    /**
     * Actions performed on WP init action.
     *
     * @return void
     */
    function init()
    {
      if (is_admin()) {
        $vars = array(
          'prefix' => $this->prefix,
          'debug' => $this->debug,
          'nonce' => wp_create_nonce('wf_licensing_' . $this->prefix),
          'licensing_endpoint' => $this->licensing_servers[0] . $this->api_ver,
          'request_data' => array(
            'action' => 'validate_license',
            'license_key' => '',
            'rand' => rand(1000, 9999),
            'version' => $this->version,
            'wp_version' => get_bloginfo('version'),
            'site_url' => get_home_url(),
            'site_title' => get_bloginfo('name'),
            'meta' => array()
          )
        );

        wp_enqueue_script('wf_licensing', $this->js_folder . 'wf-licensing.js', array(), 1.0, true);
        wp_localize_script('wf_licensing', 'wf_licensing_' . $this->prefix, $vars);
      }
    } // init


    /**
     * Log message if debugging is enabled.
     * Log file: /wp-content/wf-licensing.log
     *
     * @param string $message Message to write to log.
     * @param mixed $data Optional, extra data to write to debug log.
     *
     * @return void
     */
    function log($message, ...$data)
    {
      if (!$this->debug) {
        return;
      }

      $log_file = trailingslashit(WP_CONTENT_DIR) . 'wf-licensing.log';
      $fp = fopen($log_file, 'a+');

      fputs($fp, '[' . date('r') . '] ' . $this->prefix . ': ');
      fputs($fp, (string) $message . PHP_EOL);
      foreach ($data as $tmp) {
        fputs($fp, var_export($tmp, true) . PHP_EOL);
      }

      fputs($fp, PHP_EOL);
      fclose($fp);
    } // log


    /**
     * Fetches license details from the database.
     *
     * @param string $key If set returns only requested options key.
     *
     * @return string
     */
    function get_license($key = '')
    {
      $default = array(
        'license_key' => '',
        'error' => '',
        'valid_until' => '',
        'last_check' => 0,
        'name' => '',
        'meta' => array()
      );

      $options = get_option('wf_licensing_' . $this->prefix, array());
      $options = array_merge($default, $options);

      if (!empty($key)) {
        return $options[$key];
      } else {
        return $options;
      }
    } // get_license


    function get_license_formatted($key = '')
    {
      $license = $this->get_license();
      $out = array(
        'name' => '',
        'name_long' => '',
        'valid_until' => '',
        'expires' => '',
        'license_key' => '',
        'license_key_hidden' => '',
        'recurring' => false,
        'keyless' => false,
      );

      if (!$this->is_active()) {
        return $out;
      }
      $license['valid_until'] = $license['valid_until'];

      $out['name'] = $license['name'];
      $out['name_long'] = $license['name'];
      if ($license['meta']) {
        $tmp = '';
        foreach ($license['meta'] as $meta => $meta_value) {
    
          if ($meta[0] == '_' || filter_var($meta_value, FILTER_VALIDATE_BOOLEAN) != true) {
            continue;
          }
          $meta = str_replace('_', ' ', $meta);
          $meta = ucwords($meta);
          $meta = str_ireplace('wpr ', 'WPR ', $meta);
          $tmp .= $meta . ', ';
        }
        $tmp = trim($tmp, ', ');
        if ($tmp) {
          $out['name_long'] .= ' with ' . $tmp;
        }
      }

      if ($license['valid_until'] == $this->valid_forever) {
        $out['valid_until'] = 'forever';
        $out['recurring'] = false;
      } else {
        $out['valid_until'] = 'until ' . date(get_option('date_format'), strtotime($license['valid_until']));
        $out['recurring'] = true;
      }

      if (date('Y-m-d') == $license['valid_until']) {
        $out['expires'] = 'today';
      } elseif (date('Y-m-d', time() + 30 * DAY_IN_SECONDS) > $license['valid_until']) {
        $tmp = (strtotime($license['valid_until'] . date(' G:i:s')) - time()) / DAY_IN_SECONDS;
        $out['expires'] = 'in ' . round($tmp) . ' days';
      } else {
        $out['expires'] = 'in more than 30 days';
      }

      if (empty($license['license_key']) || $license['license_key'] == 'keyless') {
        $out['keyless'] = true;
      } else {
        $out['keyless'] = false;
        $out['license_key'] = $license['license_key'];
        $tmp = strlen($license['license_key']);
        $dash = false;
        $new = '';
        for ($i = $tmp - 1; $i >= 0; $i--) {
          if ($dash == false || $out['license_key'][$i] == '-') {
            $new = $out['license_key'][$i] . $new;
          } else {
            $new = '*' . $new;
          }
          if ($out['license_key'][$i] == '-') {
            $dash = true;
          }
        }
        $out['license_key_hidden'] = $new;
      }

      $out = apply_filters('wf_licensing_license_formatted_' . $this->prefix, $out);

      if (!empty($key)) {
        return $out[$key];
      } else {
        return $out;
      }
    } // get_license_formatted


    /**
     * Updates license details in the database.
     *
     * @param string $data License data to save; or empty to delete license
     *
     * @return bool
     */
    function update_license($data = false)
    {
      if (false === $data) {
        $tmp = delete_option('wf_licensing_' . $this->prefix);
      } else {
        $tmp = update_option('wf_licensing_' . $this->prefix, $data);
      }

      return $tmp;
    } // update_license


    /**
     * Check if license is valid
     *
     * @param string $feature If set it checks for a specific feature.
     * @param bool $force_check Forces license recheck on server instead of just cached values.
     *
     * @return boolean
     */
    function is_active($feature = '', $force_check = false)
    {
      $last_check = $this->get_license('last_check');
      if ($force_check || ($last_check && ($last_check + HOUR_IN_SECONDS * 8) < time())) {
        $this->log('auto recheck license');
        $this->validate();
      }

      $license = $this->get_license();
      
      if (
        !empty($license['license_key']) && !empty($license['name']) &&
        !empty($license['valid_until']) && $license['valid_until'] >= date('Y-m-d')
      ) {
        if (!empty($feature)) {
          if (!empty($license['meta'][$feature]) && filter_var($license['meta'][$feature], FILTER_VALIDATE_BOOLEAN) == true) {
            return true;
          } else {
            return false;
          }
        } else {
          return true;
        }
      } else {
        return false;
      }
    } // is_active


    /**
     * Hook to plugin activation action.
     * If there's a license key, try to activate & write response.
     *
     * @return void
     */
    function activate_plugin()
    {
      $license = $this->get_license();
      if ($this->is_active() || empty($license['license_key'])) {
        return false;
      }

      $tmp = $this->validate();
      if ($tmp) {
        $this->log('activating plugin, license activated');
        return true;
      } else {
        $this->log('activating plugin, unable to activate license');
        return false;
      }
    } // activate_plugin


    /**
     * Hook to plugin deactivation action.
     * If there's a license key, try to deactivate & write response.
     *
     * @return void
     */
    function deactivate_plugin()
    {
      if (!$this->is_active()) {
        return false;
      }

      $license = $this->get_license();
      $result = $this->query_licensing_server('deactivate_license');

      if (is_wp_error($result) || !is_array($result) || !isset($result['success']) || $result['success'] == false) {
        $this->log('unable to deactivate license');

        return false;
      } else {
        $license['error'] = '';
        $license['name'] = '';
        $license['valid_until'] = '';
        $license['meta'] = '';
        $license['last_check'] = 0;
        $this->update_license($license);
        $this->log('license deactivated');

        return true;
      }
    } // deactivate_plugin


    /**
     * Use when uninstalling (deleting) the plugin to clean up.
     *
     * @param string $prefix Same prefix as used when initialising the class.
     * @return bool
     */
    static function uninstall_plugin($prefix)
    {
      $tmp = delete_option('wf_licensing_' . $prefix);

      return $tmp;
    } // uninstall_plugin


    /**
     * Deletes license locally and send deactivate ping to licensing server
     *
     * @return void
     */
    function deactivate() {
      $result = $this->query_licensing_server('deactivate_license', array());
      $this->update_license(false);

      return $result;
    } // deactivate


    /**
     * Validate license key on server and save response.
     *
     * @param string $license_key License key, or leave empty to pull from saved.
     *
     * @return void
     */
    function validate($license_key = '')
    {
      $license = $this->get_license();
      if (empty($license_key)) {
        $license_key = $license['license_key'];
      }

      $out = array(
        'license_key' => $license_key,
        'error' => '',
        'name' => '',
        'last_check' => 0,
        'valid_until' => '',
        'meta' => array()
      );

      $result = $this->query_licensing_server('validate_license', array('license_key' => $license_key));

      if (is_wp_error($result)) {
        $out['error'] = 'Error querying licensing server. ' .  $result->get_error_message() . ' Please try again in a few moments.';
        $this->update_license($out);

        return false;
      } elseif (!is_array($result) || !isset($result['success'])) {
        $out['error'] = 'Invalid response from licensing server. Please try again in a few moments.';
        $this->update_license($out);

        return false;
      } elseif ($result['success'] == false) {
        $out['error'] = $result['data'];
        $this->update_license($out);

        return true;
      } else {
        $out['error'] = $result['data']['error'];
        $out['name'] = $result['data']['name'];
        $out['valid_until'] = $result['data']['valid_until'];
        $out['meta'] = $result['data']['meta'];
        $out['last_check'] = time();
        $this->update_license($out);

        return true;
      }
    } // validate


    function validate_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);

      $license_key = trim($_REQUEST['license_key']);
      if (empty($license_key)) {
        $this->update_license(false);
        do_action('wf_licensing_' . $this->prefix . '_validate_ajax', $license_key, false);

        wp_send_json_success();
      } else {
        $result = $this->validate($license_key);
        $license = $this->get_license();
        do_action('wf_licensing_' . $this->prefix . '_validate_ajax', $license_key, $result);

        if ($result == true) {
          set_site_transient('update_plugins', null);
          wp_send_json_success($result);
        } else {
          wp_send_json_error($license);
        }
      }
    } // validate_ajax


    function deactivate_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);

      $old_license = $this->get_license();
      $result = $this->deactivate();
      do_action('wf_licensing_' . $this->prefix . '_deactivate_ajax', $old_license, $result);
      wp_send_json_success($result);
    } // deactivate_ajax


    function save_ajax()
    {
      check_ajax_referer('wf_licensing_' . $this->prefix);

      $out['license_key'] = trim($_POST['license_key']);

      if ($_POST['success'] == 'true') {
        $out['error'] = trim($_POST['data']['error']);
        $out['name'] = trim($_POST['data']['name']);
        $out['valid_until'] = trim($_POST['data']['valid_until']);
        $out['meta'] = $_POST['data']['meta'];
      } else {
        $out['error'] = trim($_POST['data']);
        $out['name'] = '';
        $out['valid_until'] = '';
        $out['meta'] = array();
      }
      $out['last_check'] = time();

      $this->update_license($out);
      do_action('wf_licensing_' . $this->prefix . '_save_ajax', $out);

      wp_send_json_success();
    } // save_ajax


    /**
     * Run license server query.
     *
     * @param string $action
     * @param array $data
     *
     * @return string response|bool
     */
    function query_licensing_server($action, $data = array())
    {
      $license = $this->get_license();

      $request_params = array('sslverify' => false, 'timeout' => 25, 'redirection' => 2);
      $default_data = array(
        'action' => '',
        'license_key' => $license['license_key'],
        'rand' => rand(1000, 9999),
        'version' => $this->version,
        'wp_version' => get_bloginfo('version'),
        'site_url' => get_home_url(),
        'site_title' => get_bloginfo('name'),
        'meta' => array()
      );

      $request_data = array_merge($default_data, $data, array('action' => $action));
      $request_data = apply_filters('wf_licensing_query_server_data', $request_data);
      array_walk_recursive($request_data, function (&$val, $ind) {
        $val = rawurlencode($val);
      });
      
      $this->log('query licensing server', $request_data);

      $url = rtrim(add_query_arg($request_data, trailingslashit($this->licensing_servers[0] . $this->api_ver)), '&');
      $response = wp_remote_get($url, $request_params);
      $body = wp_remote_retrieve_body($response);
      $result = @json_decode($body, true);

      $this->log('licensing server response', $response);
     
      if (is_wp_error($response) || empty($body) || !is_array($result) || !isset($result['success'])) {
        if (is_wp_error($response)) {
          return $response;
        } else {
          return new WP_Error(1, 'Invalid server response format.');
        }
      } else {
        return $result;
      }
    } // query_licensing_server


    /**
     * Plugin info lightbox
     *
     * @param object $return
     * @param string $action
     * @param object $args
     *
     * @return object
     */
    function update_details($return, $action, $args)
    {
      if (!$this->is_active()) {
        return $return;
      }

      static $response = false;

      if ($action != 'plugin_information' || empty($args->slug) || $args->slug != $this->slug) {
        return $return;
      }

      if (empty($response) || is_wp_error($response)) {
        $response = $this->query_licensing_server('plugin_information', array('request_details' => serialize($args)));
      }

      if (is_wp_error($response)) {
        $res = new WP_Error('plugins_api_failed', 'xAn unexpected HTTP error occurred during the API request.', $response->get_error_message());
      } elseif ($response['success'] != true) {
        $res = new WP_Error('plugins_api_failed', 'Invalid response data received during the API request.', $response['data']);
      } else {
        $res = (object) $response['data'];
        $res->sections = (array) $res->sections;
        $res->banners = (array) $res->banners;
        $res->icons = (array) $res->icons;
      }

      return $res;
    } // update_details


    /**
     * Get info on new plugin version if one exists
     *
     * @param object current plugin info
     *
     * @return object update info
     */
    function update_filter($current)
    {   
      if (!$this->is_active()) {
        return $current;
      }

      static $response = false;

      $response = get_transient('wf_plugin_update_' . $this->prefix);
      
      if (empty($response)) {
        $response = $this->query_licensing_server('update_info');
        set_transient('wf_plugin_update_' . $this->prefix, $response, 120);
      }

      if (!is_wp_error($response) && $response['success'] == true) {
        $data = (object)$response['data'];

        if (empty($current)) {
          $current = new stdClass();
        }
        if (empty($current->response)) {
          $current->response = array();
        }
        if (!empty($data) && is_object($data) && version_compare($data->new_version, $this->version) === 1) {
          $data->icons = (array) $data->icons;
          $data->banners = (array) $data->banners;
          $current->response[$this->basename] = $data;
        }
      }

      return $current;
    } // update_filter
  } // WF_Licensing
} // if WF_Licensing
