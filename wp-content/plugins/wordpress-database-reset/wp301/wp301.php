<?php

/**
 * Campaign for WP 301 Redirects PRO
 * (c) WebFactory Ltd, 2020
 */


if (false == class_exists('wf_wp301')) {
  class wf_wp301
  {
    var $plugin_file = '';
    var $plugin_slug = '';
    var $plugin_screen = '';
    var $options = '';
    var $disable_dashboard = false;


    function __construct($plugin_file, $plugin_screen)
    {
      $this->plugin_file = $plugin_file;
      $this->plugin_slug = basename(dirname($plugin_file));
      $this->plugin_screen = $plugin_screen;
      $this->options = get_option('wp301promo', array());

      if (!is_admin() || !empty($this->options['email_submitted']) || date('Y-m-d') > '2020-11-05') {
        return;
      } else {
        add_action('admin_init', array($this, 'init'));
      }
    } // __construct


    function init()
    {
      add_action('wp_dashboard_setup', array($this, 'add_widget'));
      add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
      add_action('wp_ajax_wp301_promo_submit', array($this, 'ajax_submit'));
      add_action('wp_ajax_wp301_promo_dismiss', array($this, 'ajax_dismiss'));
      add_action('admin_footer', array($this, 'admin_footer'));
    } // init


    function admin_enqueue_scripts()
    {
      $screen = get_current_screen();

      if ($screen->base != 'dashboard' && $screen->id != $this->plugin_screen) {
        return;
      }

      if ($screen->base == 'dashboard' && (!empty($this->options['popup_dismissed_dashboard']) || !empty($this->disable_dashboard))) {
        return;
      }

      if ($screen->id == $this->plugin_screen) {
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-dialog');
      }

      wp_enqueue_script('wp301_promo', plugin_dir_url($this->plugin_file) . 'wp301/wp301.js');

      $js_vars = array(
        'nonce_wp301_promo_submit' => wp_create_nonce('wp301_submit'),
        'nonce_wp301_promo_dismiss' => wp_create_nonce('wp301_dismiss'),
      );

      if (empty($this->options['popup_dismissed_' . $this->plugin_slug])) {
        $js_vars['open_popup'] = true;
      }

      wp_localize_script('wp301_promo', 'wp301_promo', $js_vars);
    } // admin_enqueue_scripts


    function ajax_dismiss()
    {
      if (!wp_verify_nonce(@$_GET['_ajax_nonce'], 'wp301_dismiss')) {
        wp_send_json_error('Something is not right. Please reload the page and try again.');
      }

      $slug = substr(strip_tags(trim(@$_GET['slug'])), 0, 64);

      $this->options['popup_dismissed_' . $slug] = true;
      $tmp = update_option('wp301promo', $this->options);

      if ($tmp) {
        wp_send_json_success();
      } else {
        wp_send_json_error();
      }
    } // ajax_dismiss


    function ajax_submit()
    {
      if (!wp_verify_nonce(@$_GET['_ajax_nonce'], 'wp301_submit')) {
        wp_send_json_error('Something is not right. Please reload the page and try again.');
      }

      $email = strip_tags(trim(@$_GET['email']));
      $name = strip_tags(trim(@$_GET['name']));
      $plugin = strip_tags(trim(@$_GET['plugin'])) . '-' . strip_tags(trim(@$_GET['position']));

      if (!is_email($email)) {
        wp_send_json_error('Please enter a valid email address.');
      }

      $url = add_query_arg(array('name' => $name, 'email' => $email, 'plugin' => $plugin), 'https://wp301redirects.com/subscribe/');

      $response = wp_remote_get($url, array('timeout' => 25));

      if (is_wp_error($response)) {
        wp_send_json_error('Something is not right. Please reload the page and try again.');
      }

      $body = @json_decode(wp_remote_retrieve_body($response), true);
      if (empty($body['success'])) {
        wp_send_json_error('Something is not right. Please reload the page and try again.');
      }

      $this->options['email_submitted'] = true;
      update_option('wp301promo', $this->options);
      wp_send_json_success('Thank you for trusting us with your email! You\'ll hear from us soon 🚀');
    } // ajax_submit


    function add_widget()
    {
      if (!empty($this->options['popup_dismissed_dashboard']) || !empty($this->disable_dashboard)) {
        return;
      }

      add_meta_box('wp301promo_widget', 'Get a WP 301 Redirects PRO license for FREE <del>$158</del>', array($this, 'widget_content'), 'dashboard', 'side', 'high');
    } // add_widget


    function widget_content()
    {
      $out = '';

      $out .= '<style>';
      $out .= '#wp301promo_widget .disabled { pointer-events: none; }';
      $out .= '#wp301promo_widget label { font-weight: normal; display: inline-block; width: 15%; margin-bottom: 10px; }';
      $out .= '#wp301promo_widget input { width: 74%; margin-bottom: 10px; }';
      $out .= '#wp301promo_widget .button-primary { padding: 14px 28px; text-decoration: none; line-height: 1; }';
      $out .= '#wp301promo_dismiss { font-style: italic; display: inline-block; color: #444; text-decoration: none; margin: 8px 0 0 0; }';
      $out .= '#wp301promo_dismiss:hover { text-decoration: underline; }';
      $out .= '#wp301promo_widget, #wp301promo_widget p { font-size: 14px; }';
      $out .= '#wp301promo_widget .title301 { font-weight: 600; margin: 10px 0 -10px 0; }';
      $out .= '#wp301promo_widget img { max-width: 50%; max-height: 80px; }';
      $out .= '#wp301promo_widget .center { text-align: center; }';
      $out .= '#wp301promo_email { margin-bottom: 0 !important; }';
      $out .= '#wp301promo_widget { background-color: #fafafa; }';
      $out .= '#wp301promo_widget li a { text-decoration: underline; }';
      $out .= '#wp301promo_widget .wp301inside { padding: 25px 12px 0px 12px; position: relative; }';
      $out .= '#wp301promo_widget p { margin-top: 14px; line-height: 1.5; }';
      $out .= '#wp301promo_widget small { margin-left: 17%; }';
      $out .= '#wp301promo_widget ul { font-size: 14px; margin: 0 0 20px 0; list-style-type: disc; list-style-position: inside; }';
      $out .= '#wp301promo_widget li { margin-bottom: 3px; }';
      $out .= '#wp301promo_submit span { display: none; text-decoration: none; margin-right: 10px; animation: wf-spin-animation 1.5s infinite linear; }';
      $out .= '#wp301promo_submit.disabled span { display: inline-block; }';
      $out .= '@keyframes wf-spin-animation {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }';
      $out .= '#wp301promo_widget .inside { overflow: hidden; margin: 0; }
      #wp301promo_widget .ribbon { margin: 0; padding: 11px 20px 10px 20px; background: #007cba; color: #FFF; font-weight: 800; position: absolute; top: -17px; right: -17px; transform: translateX(30%) translateY(0%) rotate(45deg); transform-origin: top left; letter-spacing: 1px; }
      #wp301promo_widget .ribbon:before, #wp301promo_widget .ribbon:after { content: ""; position: absolute; top:0; margin: 0 -1px; width: 100%; height: 100%; background: #007cba; }
      #wp301promo_widget .ribbon:before { right:100%; }
      #wp301promo_widget .ribbon:after { left:100%; }';
      $out .= '</style>';

      $plugin_url = plugin_dir_url($this->plugin_file);

      $out .= '<div class="ribbon">FREE</div>';
      $out .= '<div class="wp301inside">';

      $out .= '<div class="center"><a href="https://wp301redirects.com/free-license/?ref=free-' . $this->plugin_slug . '-dashboard" target="_blank"><img src="' . $plugin_url . 'wp301/wp301-logo.png' . '" alt="WP 301 Redirects PRO" title="WP 301 Redirects PRO"></a></div>';

      $out .= '<p>On November 6th <a href="https://www.webfactoryltd.com/" target="_blank">WebFactory Ltd</a> will give away a limited number of lifetime WP 301 Redirect PRO licenses, <b>each for 10 sites</b>. A $158 retail value! 🚀 Leave your email, we\'ll send you a link &amp; download your copy.</p><p style="margin-bottom: 0;"><b>What do I get?</b></p>';

      $out .= '<ul>';
      $out .= '<li>Automatically fix 404 errors &amp; URL typos</li>';
      $out .= '<li>Create advanced redirect rules &amp; control affiliate links</li>';
      $out .= '<li>Detailed log of all redirects &amp; 404s</li>';
      $out .= '<li>Manage all sites\' licenses from a central Dashboard</li>';
      $out .= '<li>Lifetime license for 10 sites. <a href="https://wp301redirects.com/free-license/?ref=free-' . $this->plugin_slug . '-dashboard" target="_blank">See all features</a></li>';
      $out .= '</ul>';

      $out .= '
      <div>
        <label for="wp301promo_name">Name:</label>
        <input type="text" name="wp301promo_name" id="wp301promo_name" placeholder="How shall we call you?"><br>
        <label for="wp301promo_email">Email:</label>
        <input type="text" name="wp301promo_email" id="wp301promo_email" placeholder="Your best email address to get the plugin"><br>
        <small>We hate spam as much as you do and never send it!</small>
        <input type="hidden" id="wp301promo_plugin" value="' . $this->plugin_slug . '">
        <input type="hidden" id="wp301promo_position" value="dashboard">
      </div>

      <p class="center">
        <a id="wp301promo_submit" class="button button-primary" href="#"><span class="dashicons dashicons-update"></span>I Want My FREE License <del>$158</del></a><br>
        <a id="wp301promo_dismiss" class="" href="#" data-plugin-slug="dashboard">I don\'t want a free license; don\'t show this again</a>
      </p>
  </div>';

      echo $out;
    } // widget_content


    function admin_footer()
    {
      $screen = get_current_screen();
      if ($screen->id != $this->plugin_screen) {
        return;
      }

      $out = '';

      $out .= '<style>';
      $out .= '#wp301promo_widget .disabled { pointer-events: none; }';
      $out .= '.wp301-dialog .ui-dialog-titlebar-close { visibility: hidden; }';
      $out .= '#wp301-dialog label { font-weight: normal; display: inline-block; width: 15%; margin-bottom: 10px; }';
      $out .= '#wp301-dialog input { width: 74%; margin-bottom: 10px; }';
      $out .= '#wp301-dialog .button-primary { padding: 14px 28px; text-decoration: none; line-height: 1; }';
      $out .= '#wp301-dialog, #wp301-dialog p { font-size: 14px; }';
      $out .= '#wp301-dialog .title301 { font-size: 1.3em; font-weight: 600; margin: 10px 0 -10px 0; }';
      $out .= '#wp301-dialog img { max-width: 60%; }';
      $out .= '#wp301-dialog li a { text-decoration: underline; }';
      $out .= '#wp301-dialog small { margin-left: 16%; }';
      $out .= '#wp301-dialog .center { text-align: center; }';
      $out .= '#wp301-dialog, .wp301-dialog .ui-dialog-titlebar { background-color: #fafafa; }';
      $out .= '#wp301-dialog .wp301inside { padding: 12px 12px 0px 12px; }';
      $out .= '#wp301-dialog p { margin-top: 14px; line-height: 1.5; }';
      $out .= '#wp301-dialog ul { margin: 0 0 20px 0; list-style-type: disc; list-style-position: inside; }';
      $out .= '#wp301-dialog li { margin-bottom: 3px; }';
      $out .= '#wp301promo_dismiss { display: inline-block; color: #444; text-decoration: none; margin: 8px 0 0 0; }';
      $out .= '#wp301promo_dismiss:hover { text-decoration: underline; }';
      $out .= '#wp301promo_submit span { display: none; text-decoration: none; margin-right: 10px; animation: wf-spin-animation 1.5s infinite linear; }';
      $out .= '#wp301promo_submit.disabled span { display: inline-block; }';
      $out .= '@keyframes wf-spin-animation {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }';
      $out .= '#wp301-dialog .inside { overflow: hidden; margin: 0; }
      #wp301-dialog .ribbon { margin: 0; padding: 11px 20px 10px 20px; background: #007cba; color: #FFF; font-weight: 800; position: absolute; top: -17px; right: -17px; transform: translateX(30%) translateY(0%) rotate(45deg); transform-origin: top left; letter-spacing: 1px; }
      #wp301-dialog .ribbon:before, #wp301-dialog .ribbon:after { content: ""; position: absolute; top:0; margin: 0 -1px; width: 100%; height: 100%; background: #007cba; }
      #wp301-dialog .ribbon:before { right:100%; }
      #wp301-dialog .ribbon:after { left:100%; }';
      $out .= '</style>';

      $out .= '<div id="wp301-dialog" style="display: none;" title="Get a WP 301 Redirects PRO license for FREE"><span class="ui-helper-hidden-accessible"><input type="text"/></span>';

      $plugin_url = plugin_dir_url($this->plugin_file);

      $out .= '<div class="wp301inside">';
      $out .= '<div class="ribbon">FREE</div>';

      $out .= '<div class="center"><a href="https://wp301redirects.com/free-license/?ref=free-' . $this->plugin_slug . '-popup" target="_blank"><img src="' . $plugin_url . 'wp301/wp301-logo.png' . '" alt="WP 301 Redirects PRO" title="WP 301 Redirects PRO"></a></div>';

      $out .= '<p>On November 6th <a href="https://www.webfactoryltd.com/" target="_blank">WebFactory Ltd</a> will give away a limited number of lifetime WP 301 Redirect PRO licenses, <b>each for 10 sites</b>. A $158 retail value! 🚀 Leave your email, we\'ll send you a link &amp; download your copy.</p><p style="margin-bottom: 0;"><b>What do I get?</b></p>';

      $out .= '<ul>';
      $out .= '<li>Automatically fix 404 errors &amp; URL typos</li>';
      $out .= '<li>Create advanced redirect rules &amp; control affiliate links</li>';
      $out .= '<li>Detailed log of all redirects &amp; 404s</li>';
      $out .= '<li>Manage all sites\' licenses from a central Dashboard</li>';
      $out .= '<li>Lifetime license for 10 sites. <a href="https://wp301redirects.com/free-license/?ref=free-' . $this->plugin_slug . '-dashboard" target="_blank">See all features</a></li>';
      $out .= '</ul>';

      $out .= '
      <div>
        <label for="wp301promo_name">Name:</label>
        <input type="text" name="wp301promo_name" id="wp301promo_name" placeholder="How shall we call you?"><br>
        <label for="wp301promo_email">Email:</label>
        <input type="text" name="wp301promo_email" id="wp301promo_email" placeholder="Your best email address to get the plugin"><br>
        <small>We hate spam as much as you do and never send it!</small>
        <input type="hidden" id="wp301promo_plugin" value="' . $this->plugin_slug . '">
        <input type="hidden" id="wp301promo_position" value="popup">
      </div>

      <p class="center">
        <a id="wp301promo_submit" class="button button-primary" href="#"><span class="dashicons dashicons-update"></span>I Want My FREE License <del>$158</del></a><br>
        <a id="wp301promo_dismiss" class="" href="#" data-plugin-slug="' . $this->plugin_slug . '">I don\'t want a free license; don\'t show this again</a>
      </p>

      </div>';

      $out .= '</div>';

      echo $out;
    } // wp_footer
  } // wf_wp301
}
