<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class MantAppCtrl {
  public function __construct() {
    $this->load_hooks();
  }

  public function load_hooks() {
    // Back end stuff
    // add_action('mepr_menu',                     array($this, 'menu'), 1000);
    add_action('admin_menu',                    array($this, 'menu'));
    add_action('admin_enqueue_scripts',         array($this, 'load_scripts'));
    add_action('admin_init',                    array($this, 'save_admin_page'));
    add_action('wp_ajax_mant_get_blank_form',   array($this, 'get_blank_form'));
    
    // Front end stuff
    add_action('mepr_account_nav',              array($this, 'add_nav_tabs'));
    add_action('mepr_account_nav_content',      array($this, 'add_nav_content'));
    add_action('bp_setup_nav',                  array($this, 'setup_bp_nav'), 11);
    add_action('plugins_loaded',                array($this, 'maybe_redirect_bp_tabs'));
  }

  public function load_scripts($hook) {
    // var_dump($hook);
    if(strpos($hook, 'mepr-toolbox') !== false) {
      wp_enqueue_style('mant-admin-page-css', MANTSCRIPTSURL . '/css/admin_page.css', array());
      wp_enqueue_script('mant-admin-page-js', MANTSCRIPTSURL . '/js/admin_page.js', array('jquery'));
    }
  }

  public function menu() {
    $page_title = 'MeprToolbox';
    $exists = $this->toplevel_menu_exists($page_title);

    if(!$exists) {
      add_menu_page(
        $page_title . ' - Account Navigation Tabs',
        $page_title,
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page'),
        'dashicons-hammer'
      );
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Account Navigation Tabs',
        'Account Nav Tabs',
        'manage_options',
        'mepr-toolbox',
        array($this, 'admin_page')
      );
    }
    else {
      add_submenu_page(
        'mepr-toolbox',
        $page_title . ' - Account Navigation Tabs',
        'Account Nav Tabs',
        'manage_options',
        'mepr-toolbox-nav-tabs',
        array($this, 'admin_page')
      );
    }
  }

  public function toplevel_menu_exists($title) {
    global $menu;
    foreach($menu as $item) {
      if(strtolower($item[0]) == strtolower($title)) {
        return true;
      }
    }
    return false;
  }

  public function admin_page() {
    include(MANTVIEWSPATH . '/admin/admin_page.php');
  }

  public function save_admin_page() {
    if(!isset($_POST['mant_admin_page_save']) || !isset($_POST['mant-tab'])) { return; }

    if(empty($_POST['mant-tab'])) { return; }

    $tabs = array(); // an array of objects

    foreach($_POST['mant-tab'] as $tab) {
      if($tab['type'] == 'url' && empty($tab['url'])) { continue; }
      if($tab['type'] == 'content' && empty($tab['content'])) { continue; }

      $tabs[] = array(
        'name'    => stripslashes($tab['name']),
        'type'    => stripslashes($tab['type']),
        'url'     => stripslashes($tab['url']),
        'new_tab' => (isset($tab['new_tab'])) ? 1 : 0,
        'content' => stripslashes($tab['content'])
      );
    }

    if(!empty($tabs)) {
      update_option('mant_tabs', $tabs);
    }
  }

  // Stored as array of arrays for WPML/Polylang admin texts compatibility
  // Convert back to array of objects on retrieval though
  public function get_tabs() {
    $array_of_objects = array();
    $array_of_arrays = get_option('mant_tabs', false);
    if($array_of_arrays === false) { return false; }
    foreach($array_of_arrays as $tab) {
      if(!is_object($tab)) {
        $array_of_objects[] = (object)$tab;
      }
      else {
        $array_of_objects[] = $tab;
      }
    }
    return $array_of_objects;
  }

  public function get_blank_form() {
    ob_start();
    $random_id = (int)rand(100, 100000);
    MantAppHelper::render_admin_page_tab($random_id, '', 'content', '', '', '');
    $form = ob_get_clean();
    die(trim($form));
  }

  public function add_nav_tabs($user) {
    $tabs = $this->get_tabs();

    if(empty($tabs)) { return; }

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);

    foreach($tabs as $i => $tab) {
      $new_tab = ($tab->new_tab) ? 'target="_blank"' : '';
      $active = (isset($_GET['action']) && $_GET['action'] == 'tab' . $i) ? 'mepr-active-nav-tab' : '';

      if($tab->type == 'content') {
        ?>
          <span class="mepr-nav-item <?php echo $active; ?>">
            <a href="<?php echo $uri_parts[0]; ?>?action=tab<?php echo $i; ?>"><?php echo stripslashes($tab->name); ?></a>
          </span>
        <?php
      }
      elseif($tab->type == 'url') {
        ?>
          <span class="mepr-nav-item">
            <a href="<?php echo stripslashes($tab->url); ?>" <?php echo $new_tab; ?>><?php echo stripslashes($tab->name); ?></a>
          </span>
        <?php
      }
    }
  }

  public function add_nav_content($action) {
    $tabs = $this->get_tabs();

    if(empty($tabs)) { return; }

    foreach($tabs as $i => $tab) {
      if($action == 'tab' . $i) {
        ?>
          <div id="mant-content-<?php echo $i; ?>">
            <?php echo do_shortcode(wpautop(stripslashes($tab->content))); ?>
          </div>
        <?php
      }
    }
  }

  public function maybe_redirect_bp_tabs() {
    $this->bp_nav_manager(true); // redirect = true
  }

  public function setup_bp_nav() {
    global $bp;

    $main_slug  = 'mp-membership';
    $tabs       = $this->get_tabs();
    $position   = 100;

    if(empty($tabs)) { return; }

    foreach($tabs as $i => $tab) {
      //Payments Sub Menu
      bp_core_new_subnav_item(
        array(
          'name' => stripslashes($tab->name),
          'slug' => 'mp-tab-' . $i,
          'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
          'parent_slug' => $main_slug,
          'screen_function' => array($this, 'bp_screen_function'),
          'position' => $position++,
          'user_has_access' => bp_is_my_profile(),
          'site_admin_only' => false,
          'item_css_id' => 'mepr-bp-tab-' . $i
        )
      );
    } //end foreach
  }

  public function bp_screen_function() {
    add_action('bp_template_content', array($this, 'bp_nav_manager'));

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function bp_nav_manager($redirect = false) {
    $tabs = $this->get_tabs();

    if(empty($tabs)) { return; }

    foreach($tabs as $i => $tab) {
      if(strpos($_SERVER['REQUEST_URI'], 'mp-tab-' . $i) !== false) {
        if(!$redirect) {
          if($tab->type == 'content') {
            ?>
              <div id="mant-content-<?php echo $i; ?>">
                <?php echo do_shortcode(wpautop(stripslashes($tab->content))); ?>
              </div>
            <?php
          }
          elseif($tab->type == 'url') { // Meta refresh redirect (This is only here in case the "plugins_loaded" redirect doesn't happen)
            ?>
              <div id="mant-content-<?php echo $i; ?>">
                <p><?php _e('Please wait while you are being redirected...', 'mant'); ?><p>
                <meta http-equiv="refresh" content="0; url=<?php echo stripslashes($tab->url); ?>">
              </div>
            <?php
          }
        }
        elseif($tab->type == 'url') { // Runs only on plugins_loaded hook
          MeprUtils::wp_redirect(stripslashes($tab->url));
          die();
        }
      }
    }
  }
} //end class
