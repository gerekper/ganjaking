<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtAdminCtrl extends MpdtBaseCtrl {

  public function load_hooks() {
    add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'));
    add_action('mepr_menu', array($this,'menu'));
    add_action('plugins_loaded', array($this,'load_language'));
  }

  public function menu() {
    add_submenu_page(
      'memberpress',
      __('Developer', 'memberpress-developer-tools'),
      __('Developer', 'memberpress-developer-tools'),
      'administrator',
      MPDT_PLUGIN_NAME,
      array($this,'route')
    );
  }

  public function load_language() {
    $path_from_plugins_folder = MPDT_PLUGIN_NAME . '/i18n';
    load_plugin_textdomain( MPDT_PLUGIN_NAME, false, $path_from_plugins_folder );
    load_plugin_textdomain( MPDT_PLUGIN_NAME, false, '/mepr-i18n' );
  }

  public function admin_enqueue_scripts($hook) {
    if(strstr($hook, MPDT_PLUGIN_NAME) !== false) {
      $pattern = '/(\s{2}|\n)/';

      ob_start();
      self::webhook_row();
      $row_html = preg_replace($pattern, '', ob_get_clean());

      ob_start();
      require(MPDT_VIEWS_PATH.'/route.php');
      $route_html = preg_replace($pattern, '', ob_get_clean());

      ob_start();
      require(MPDT_VIEWS_PATH.'/route-input.php');
      $route_input_html = preg_replace($pattern, '', ob_get_clean());

      ob_start();
      require(MPDT_VIEWS_PATH.'/route-search.php');
      $route_search_html = preg_replace($pattern, '', ob_get_clean());

      $api = MpdtCtrlFactory::fetch('api');
      $routes = $api->routes();
      $events = require(MPDT_DOCS_PATH.'/webhooks/events.php');

      $data = array(
        'webhooks' => array(
          'row_html' => $row_html,
          'events' => $events
        ),
        'api' => array(
          'routes' => $routes,
          'route_html' => $route_html,
          'route_input_html' => $route_input_html,
          'route_search_html' => $route_search_html,
        ),
        'str' => array(
          'default_value' => __('default value:', 'memberpress-developer-tools'),
        ),
        'regen_api_key_nonce' => wp_create_nonce('api_key')
      );

      wp_register_style('mpdt_highlightjs', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/styles/sunburst.min.css', array(), '8.7');
      wp_register_style('mpdt_simplegrid', MPDT_CSS_URL.'/simplegrid.css');
      wp_register_style('mepr_settings_table', MEPR_CSS_URL.'/settings_table.css');
      wp_enqueue_style('mpdt_css', MPDT_CSS_URL.'/admin.css', array('mpdt_highlightjs','mepr_settings_table','mpdt_simplegrid'));
      wp_register_style('tooltipster-borderless-theme', MPDT_CSS_URL . '/vendor/tooltipster-sideTip-borderless.min.css', array());
      wp_enqueue_style('tooltipster-css', MPDT_CSS_URL . '/vendor/tooltipster.bundle.min.css', array('tooltipster-borderless-theme'));

      wp_register_script('mpdt_highlightjs', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.7/highlight.min.js', array(), '8.7');
      wp_register_script('mepr_settings_table', MEPR_JS_URL.'/settings_table.js', array('jquery'));
      wp_register_script('mpdt-tooltipster-js', MPDT_JS_URL . '/vendor/tooltipster.bundle.min.js', array('jquery'));
      wp_enqueue_script('mpdt-clipboard-js', MPDT_JS_URL . '/vendor/clipboard.min.js', array('mpdt-tooltipster-js'));
      wp_enqueue_script('mpdt_js', MPDT_JS_URL.'/admin.js', array('jquery','mpdt_highlightjs','mepr_settings_table'));

      wp_localize_script('mpdt_js', 'MPDT', $data);
    }
  }

  public function route() {
    if(MeprUtils::is_post_request()) {
      $this->save_options();
      $message = __('Your Webhooks have been saved.', 'memberpress-developer-tools');
    }

    $webhooks = get_option(MPDT_WEBHOOKS_KEY, false);
    $api_key  = get_option('mpdt_api_key', '');

    require(MPDT_VIEWS_PATH.'/admin.php');
  }

  public function save_options() {
    if(isset($_POST[MPDT_WEBHOOKS_KEY])) {
      update_option(MPDT_WEBHOOKS_KEY, $_POST[MPDT_WEBHOOKS_KEY]);
    }
  }

  public function webhook_row($count='{{id}}', $webhook=array()) {
    $whk = MpdtCtrlFactory::fetch('webhooks');
    $events = $whk->events;

    if(!is_array($webhook) || empty($webhook) || !isset($webhook['url'])) {
      $webhook = array('url' => '');
    }

    if(!isset($webhook['events']) || !is_array($webhook['events']) || empty($webhook['events'])) {
      $webhook['events'] = array('all' => 'on');
    }

    $delim = MeprUtils::get_delim($_SERVER['REQUEST_URI']);
    $title = __('Select which events should be pushed to this Webhook', 'memberpress-developer-tools');

    require(MPDT_VIEWS_PATH.'/webhook-row.php');
  }

} //End class
