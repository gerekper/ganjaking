<?php
namespace memberpress\downloads\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models,
    memberpress\downloads\helpers as helpers;

class FileStats extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('mepr_menu', array($this, 'menu'));
    add_action('mepr_table_controls_search', array($this, 'table_search_box'));
    add_action('wp_ajax_mpdl_file_search', array($this, 'file_search'));

    // Screen Options
    $hook = 'mp-downloads_page_mpdl_stats';
    add_action( "load-{$hook}", array($this,'add_screen_options') );
    add_filter( "manage_{$hook}_columns", array($this, 'get_columns'), 0 );
    add_filter( 'set_screen_option_mpdl_stats_perpage', array($this,'setup_screen_options'), 10, 3 );
  }

  public function add_screen_options() {
    add_screen_option( 'layout_columns' );

    $option = 'per_page';

    $args = array(
      'label' => __('Stats', 'memberpress', 'memberpress-downloads'),
      'default' => 10,
      'option' => base\SLUG_KEY.'_stats_perpage'
    );

    add_screen_option( $option, $args );
  }

  /* This is here to use wherever we want. */
  public function get_columns() {
    $cols = array(
      'col_id'              => __('ID', 'memberpress', 'memberpress-downloads'),
      'col_file_id'         => __('File', 'memberpress', 'memberpress-downloads'),
      'col_user_id'         => __('User', 'memberpress', 'memberpress-downloads'),
      'col_ip_address'      => __('IP Address', 'memberpress', 'memberpress-downloads'),
      'col_created_at'      => __('Date', 'memberpress', 'memberpress-downloads'),
    );

    return apply_filters(base\SLUG_KEY.'_stats_cols', $cols);
  }

  public function setup_screen_options($status, $option, $value) {
    if ( 'mpdl_stats_perpage' === $option ) { return $value; }
    return $status;
  }


  public static function admin_enqueue_scripts() {
    global $current_screen;

    if($current_screen->id === 'mp-downloads_page_mpdl_stats') {
      wp_enqueue_script('mpdl-file-stats-js', base\JS_URL . '/admin_stats.js', array('jquery', 'suggest', 'jquery-ui-datepicker'), base\VERSION);
      wp_enqueue_script('mpdl-table-control-js', base\JS_URL . '/table-controls.js', array(), base\VERSION);
      wp_enqueue_style('mpcs-files', base\CSS_URL . '/admin_files.css', array(), base\VERSION);
      wp_enqueue_style('jquery-ui-datepicker-style','https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css',false,"1.9.0",false);
    }
  }

  /**
   * Adds a submenu page under the custom post type parent.
   */
  public function menu(){
    global $menu, $submenu;

    $capability = \MeprUtils::get_mepr_admin_capability();

    add_submenu_page('memberpress-downloads', __('Stats', 'memberpress', 'memberpress-downloads'), __('Stats', 'memberpress', 'memberpress-downloads'), $capability, base\SLUG_KEY . '_stats', array( $this, 'render_stats' ));
  }


  public function table_search_box() {
    if(isset($_REQUEST['page']) && $_REQUEST['page']=='mpdl_stats') {
      $mepr_options = \MeprOptions::fetch();

      $file_name = (isset($_REQUEST['file_name'])?$_REQUEST['file_name']:false);
      $start_date = (isset($_REQUEST['start_date'])?$_REQUEST['start_date']:false);
      $end_date = (isset($_REQUEST['end_date'])?$_REQUEST['end_date']:false);

      require_once(base\VIEWS_PATH . '/admin/files/stats/search_box.php');
    }
  }

  /**
   * Display callback for the submenu page.
   */
  public function render_stats() {
    $screen = get_current_screen();
    $list_table = new lib\FileStatsTable( $screen, $this->get_columns() );

    $list_table->prepare_items();
    require_once(base\VIEWS_PATH . '/admin/files/stats/list.php');
  }

  public function file_search() {
    if(!lib\Utils::is_admin()) {
      die('-1');
    }

    // jQuery suggest plugin has already trimmed and escaped user input (\ becomes \\)
    // so we just need to sanitize the username
    $s = sanitize_user($_GET['q']);

    if(strlen($s) < 2) {
      die; // require 2 chars for matching
    }

    $files = get_posts(array('post_type' => models\File::$cpt, 's' => $s));
    require_once(base\VIEWS_PATH . '/admin/files/stats/search.php');
    die();
  }

}
