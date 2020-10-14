<?php
namespace memberpress\downloads\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\controllers\admin as ctrl,
    memberpress\downloads\models as models;

class App extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('admin_init', array($this,'install')); // DB upgrade is handled automatically here now
    add_action('custom_menu_order', array($this,'admin_menu_order'));
    add_action('menu_order', array($this,'admin_menu_order'));
    add_action('menu_order', array($this,'admin_submenu_order'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
  }

  public static function load_language() {
    $path_from_plugins_folder = base\PLUGIN_NAME . '/i18n/';
    load_plugin_textdomain(base\PLUGIN_NAME, false, $path_from_plugins_folder);
  }

  public static function setup_menus() {
    $app = App::fetch();
    add_action('admin_menu', array($app,'menu'));
  }

  /**
  * Register custom post type for all CPTs
  * Called from activation.php
  * Hook: register_activation_hook
  */
  public function register_all_cpts() {
    $downloads_ctrl = ctrl\Files::fetch();
    $downloads_ctrl->register_post_type();
  }

  /**
  * Creates and protects the MPDL uploads dir
  * Called from activation.php
  * Hook: register_activation_hook
  */
  public function init_uploads_dir() {
    $wp_upload_dir = wp_upload_dir();
    $access_type = get_filesystem_method();
    if($access_type === 'direct') {
      $creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, array());
      if (!WP_Filesystem($creds)) { return false; }
      global $wp_filesystem; // We can now use WP_Filesystem

      if(!empty($wp_upload_dir['basedir'])) {
        $mpdl_upload_dir = trailingslashit(trailingslashit($wp_upload_dir['basedir']) . base\SLUG_KEY);
        $mpdl_thumb_dir = trailingslashit($mpdl_upload_dir . 'thumbnail');
        if(!$wp_filesystem->is_dir($mpdl_upload_dir)) {
          // Create base upload dir
          $wp_filesystem->mkdir($mpdl_upload_dir, FS_CHMOD_DIR);
        }
        if(!$wp_filesystem->is_dir($mpdl_thumb_dir)) {
          // Create thumbnail dir
          $wp_filesystem->mkdir($mpdl_thumb_dir, FS_CHMOD_DIR);
        }
        $mpdl_htaccess = $mpdl_upload_dir . '.htaccess';
        $mpdl_thumb_htaccess = $mpdl_thumb_dir . '.htaccess';
        if(!$wp_filesystem->exists($mpdl_htaccess)) {
          // Create htaccess for apache file protection
          $wp_filesystem->put_contents(
            $mpdl_htaccess,
            <<<EOD
Options -Indexes
deny from all
EOD
,
            FS_CHMOD_FILE
          );
        }
        if(!$wp_filesystem->exists($mpdl_thumb_htaccess)) {
          // Create htaccess for apache file protection
          $wp_filesystem->put_contents(
            $mpdl_thumb_htaccess,
            <<<EOD
Options -Indexes
allow from all
EOD
,
            FS_CHMOD_FILE
          );
        }
        $mpdl_index = $mpdl_upload_dir . 'index.php';
        if(!$wp_filesystem->exists($mpdl_index)) {
          // Create index file to prevent browsing
          $wp_filesystem->put_contents(
            $mpdl_index,
            '<?php /* Silence will fall. */ ?>',
            FS_CHMOD_FILE
          );
        }
      }
    }
  }

  public function toplevel_menu_route() {
    $downloads_ctrl = ctrl\Downloads::fetch();

    ?>
    <script>
      window.location.href="<?php echo $downloads_ctrl->cpt_admin_url(); ?>";
    </script>
    <?php
  }

  public function menu() {
    self::admin_separator();

    $downloads_menu_hook = add_menu_page(
      __('MP Downloads', 'memberpress-downloads'),
      __('MP Downloads', 'memberpress-downloads'),
      'administrator',
      base\PLUGIN_NAME,
      array($this, 'toplevel_menu_route'),
      'dashicons-download',
      12097
    );
    // $options_ctrl = ctrl\Options::fetch();
    // add_submenu_page(
    //   base\PLUGIN_NAME,
    //   __('MP Downloads | Downloads', 'memberpress-downloads'),
    //   __('Options', 'memberpress-downloads'),
    //   'administrator',
    //   base\PLUGIN_NAME . '-options',
    //   array($options_ctrl, 'route')
    // );

    do_action(base\SLUG_KEY . '_menu');
  }

  /********* INSTALL PLUGIN ***********/
  public function install() {
    $db = lib\Db::fetch();
    $db->upgrade();
  }

  /**
   * Add a separator to the WordPress admin menus
   */
  public static function admin_separator() {
    global $menu;

    // Prevent duplicate separators when no core menu items exist
    if(!lib\Utils::is_user_admin()) { return; }

    $menu[] = array('', 'read', 'separator-' . base\PLUGIN_NAME, '', 'wp-menu-separator ' . base\PLUGIN_NAME);
  }

  /*
   * Move our custom separator above our admin menu
   *
   * @param array $menu_order Menu Order
   * @return array Modified menu order
   */
  public static function admin_menu_order($menu_order) {
    if(!$menu_order) {
      return true;
    }

    if(!is_array($menu_order)) {
      return $menu_order;
    }

    // Initialize our custom order array
    $new_menu_order = array();

    // Menu values
    $second_sep   = 'separator2';
    $custom_menus = array('separator-' . base\PLUGIN_NAME, base\PLUGIN_NAME);

    // Loop through menu order and do some rearranging
    foreach($menu_order as $item) {
      // Position MemberPress Downloads menu above appearance
      if($second_sep == $item) {
        // Add our custom menus
        foreach($custom_menus as $custom_menu) {
          if(array_search($custom_menu, $menu_order)) {
            $new_menu_order[] = $custom_menu;
          }
        }

        // Add the appearance separator
        $new_menu_order[] = $second_sep;

      // Skip our menu items down below
      }
      elseif(!in_array($item, $custom_menus)) {
        $new_menu_order[] = $item;
      }
    }

    // Return our custom order
    return $new_menu_order;
  }

  //Organize the CPT's in our submenu
  public static function admin_submenu_order($menu_order) {
    global $submenu;

    static $run = false;

    //no sense in running this everytime the hook gets called
    if($run) { return $menu_order; }

    //just return if there's no memberpress-downloads menu available for the current screen
    if(!isset($submenu[base\PLUGIN_NAME])) { return $menu_order; }

    $run = true;
    $new_order = array();
    $i = 2;

    foreach($submenu[base\PLUGIN_NAME] as $sub) {
      if($sub[0] == __('Downloads', 'memberpress-downloads')) {
        $new_order[0] = $sub;
      }
      else {
        $new_order[$i++] = $sub;
      }
    }

    ksort($new_order);

    $submenu[base\PLUGIN_NAME] = $new_order;

    return $menu_order;
  }

  public function enqueue_admin_scripts($hook) {
    wp_enqueue_style('mpdl-simplegrid', base\CSS_URL . '/simplegrid.css', null, base\VERSION);
    wp_enqueue_style('jquery-magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css');
    wp_enqueue_style('mpdl-admin-shared', base\CSS_URL . '/admin_shared.css', array('wp-pointer','jquery-magnific-popup','mpdl-simplegrid'), base\VERSION);
    wp_enqueue_style('mpdl-fontello-styles', base\FONTS_URL.'/fontello/css/fontello.css', array(), base\VERSION);
    wp_register_script('jquery-magnific-popup', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js', array('jquery'));
    wp_enqueue_script('mpdl-tooltip', base\JS_URL . '/tooltip.js', array('jquery','wp-pointer','jquery-magnific-popup'), base\VERSION);
    if(strstr($hook, base\PLUGIN_NAME . '-options') !== false) {
      wp_enqueue_style('mpdl-settings-table', base\CSS_URL . '/settings_table.css', null, base\VERSION);
      wp_enqueue_script('mpdl-settings-table', base\JS_URL . '/settings_table.js', array('jquery'), base\VERSION);
    }
  }

}
