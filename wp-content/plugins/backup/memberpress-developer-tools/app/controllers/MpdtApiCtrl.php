<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtApiCtrl extends MpdtBaseCtrl {
  public function load_hooks() {
    add_action('rest_api_init', array($this, 'api_init'));
    add_action('admin_init', array($this, 'maybe_generate_api_key'));
    add_action('wp_ajax_mpdt_api_data',array($this,'ajax_api_data'));
    add_action('wp_ajax_mpdt_regen_api_key', array($this, 'regenerate_api_key'));
  }

  // Build Routes
  public function routes() {
    $whk_ctrl = new MpdtWebhooksCtrl();
    $registered_events = $whk_ctrl->real_events(true); // Used in general.php
    $api_routes = require(MPDT_DOCS_PATH . "/routes/general.php"); // Non-Model API Endpoints
    $utils_files = @glob(MPDT_UTILS_PATH . '/Mpdt*Utils.php', GLOB_NOSORT);
    foreach($utils_files as $utils_file) {
      $class = preg_replace('#\.php#', '', basename($utils_file));

      $r = new ReflectionClass($class);
      $obj = $r->newInstanceArgs(array());

      $api_routes = array_merge($api_routes, $obj->endpoints);
    }

    return $api_routes;
  }

  public function api_init() {
    $api_files = @glob(MPDT_API_PATH . '/Mpdt*Api.php', GLOB_NOSORT);
    foreach($api_files as $api_file) {
      $class = preg_replace('#\.php#', '', basename($api_file));

      $r = new ReflectionClass($class);
      $obj = $r->newInstanceArgs(array());
      $obj->register_routes();
    }
  }

  /**
  * Used to regenerate a new API Key via ajax
  * @see add_action('wp_ajax_mpdt_regen_api_key')
  * @return string JSON response containing api_key
  */
  public function regenerate_api_key() {
    check_ajax_referer('api_key', 'regen_api_key_nonce');
    $api_key = $this->maybe_generate_api_key(true);

    wp_send_json_success(array('api_key' => $api_key));
  }

  /**
  * Used to store the mpdt_api_key option
  * @see add_action('admin_init')
  * @param bool $regenerate Force the key to be regenerated
  * @return string $api_key
  */
  public function maybe_generate_api_key($regenerate = false) {
    $api_key = get_option('mpdt_api_key', '');
    if(empty($api_key) || $regenerate === true) {
      $api_key = $this->generate_api_key();
      update_option('mpdt_api_key', $api_key);
    }

    return $api_key;
  }

  /**
  * Generates a random alphanumeric string
  * @return string Random 10 character alphanumeric [a-zA-Z0-9]
  */
  private function generate_api_key() {
    $chars = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));

    return substr(str_shuffle(implode('', $chars)), 0, 10);
  }

  public function ajax_api_data() {
    if(!MeprUtils::is_mepr_admin()) {
      header('HTTP/1.1 401 Unauthorized', true, 401);
      exit(__('Error: You are unauthorized.', 'memberpress-developer-tools'));
    }

    if(!isset($_REQUEST['endpoint'])) {
      header('HTTP/1.1 400 Bad Request', true, 400);
      exit(__('Error: No event was specified.', 'memberpress-developer-tools'));
    }

    $endpoint = $_REQUEST['endpoint'];
    $routes = $this->routes();

    if(!isset($routes[$endpoint])) {
      header('HTTP/1.1 400 Bad Request', true, 400);
      exit(__('Error: Invalid event.', 'memberpress-developer-tools'));
    }

    $route = $routes[$endpoint];

    $data = array();
    if(isset($route->sample_request)) {
      $data = array($route->sample_request);
    }

    if(is_object($route->resp)) {
      $utils = MpdtUtilsFactory::fetch($route->resp->utils_class);
      $data  = $utils->get_data(array('per_page',$route->resp->count), true);

      // *** Get Example Response CURL command line
      if($route->resp->single_result) {
        $response = $data[0];
      }
      else {
        $response = $data;
      }

      if(isset($route->resp->custom_response)) {
        $custom = $route->resp->custom_response;
        $custom['data'] = $response;
        $response = $custom;
      }
    }
    else {
      $response = $route->resp;
    }

    // *** Get Example Request CURL command line
    $method = strtolower($route->method);
    $url = $route->url;

    // Replace url variables with actual data
    preg_match_all('/:([a-z]+)/i', $url, $m);

    if(isset($m[1])) {
      foreach($m[1] as $mkey => $mval) {
        if(isset($data[0][$mval])) {
          $url = preg_replace("/:{$mval}/", $data[0][$mval], $url);
        }
      }
    }

    $params = array();
    if(is_array($route->search_args)) {
      $params = array('page'=>2,'per_page'=>10);
    }
    elseif(is_array($route->update_args)) {
      $params = $data[0];
      $args = array_keys($route->update_args);
      foreach($params as $pkey => $pval) {
        if(!in_array($pkey, $args)) {
          unset($params[$pkey]);
        }
      }
    }

    ob_start();
    require(MPDT_VIEWS_PATH . '/request.php');
    $request = ob_get_clean();

    exit(json_encode(compact('request','response')));
  }

}
