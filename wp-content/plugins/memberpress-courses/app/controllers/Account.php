<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class Account extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('mepr_account_nav', array($this, 'my_courses_nav'));
    add_action('mepr_account_nav_content', array($this, 'my_courses_list'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    if(is_admin()) {
      add_action('edit_user_profile', array($this, 'extra_profile_fields'));
    }
  }

  /**
  * Enqueue scripts for account controller
  * @see load_hooks(), add_action('wp_enqueue_scripts')
  */
  public static function enqueue_scripts() {
    global $post;
    $mepr_options = \MeprOptions::fetch();

    if( is_a($post, 'WP_Post') && $post->ID == $mepr_options->account_page_id) {
      \wp_enqueue_style('mpcs-simplegrid', base\CSS_URL . '/simplegrid.css', array(), base\VERSION);
      \wp_enqueue_style('mpcs-progress', base\CSS_URL . '/progress.css', array(), base\VERSION);
      \wp_enqueue_script('mpcs-progress-js', base\JS_URL . '/progress.js', array('jquery'), base\VERSION);
    }
  }

  /**
  * Enqueue scripts for admin user profile
  * @see load_hooks(), add_action('admin_enqueue_scripts')
  * @param string $hook Current admin page
  */
  public static function enqueue_admin_scripts($hook) {
    if($hook === 'user-edit.php') {
      \wp_enqueue_style('mpcs-progress', base\CSS_URL . '/progress.css', array(), base\VERSION);
      \wp_enqueue_script('mpcs-progress-js', base\JS_URL . '/progress.js', array('jquery'), base\VERSION);
    }
  }

  /**
  * Render courses nav
  * @see load_hooks(), add_action('mepr_account_nav')
  * @param MeprUser $current_user logged in MeprUser object
  */
  public static function my_courses_nav($current_user) {
    global $post;
    $account_url = lib\Utils::get_permalink($post->ID);
    $delim = preg_match('#\?#', $account_url) ? '&' : '?';
    ?>
    <span class="mepr-nav-item mepr-courses">
      <a href="<?php echo \apply_filters('mepr-account-nav-courses-link', $account_url . $delim . 'action=courses'); ?>" id="mepr-account-courses">
        <?php echo \apply_filters('mepr-account-nav-courses-label', _e('Courses', 'memberpress-courses')); ?>
      </a>
    </span>
    <?php
  }
  /**
  * Render courses list
  * @see load_hooks(), add_action('mepr_account_nav_content')
  * @param string $action Account page current action
  * @param boolean $show_bookmark Show progress bar
  */
  public static function my_courses_list($action, $show_bookmark = true) {
    global $post;
    $mepr_options = \MeprOptions::fetch();

    if(is_user_logged_in() && $action === 'courses') {
      $my_courses = array();
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $course_query = new \WP_Query(array('post_type' => models\Course::$cpt, 'post_status' => 'publish', 'posts_per_page' => 6, 'paged' => $paged));
      $course_posts = $course_query->get_posts();
      foreach ($course_posts as $course) {
        $current_user = lib\Utils::get_currentuserinfo();
        $mepr_user = new \MeprUser($current_user->ID);
        if(!\MeprRule::is_locked_for_user($mepr_user, $course)) {
          $my_courses[] = new models\Course($course->ID);
        }
      }

      require_once(base\VIEWS_PATH . '/account/course_list.php');
    }
    // Don't render
  }

  /**
  * Render course profile fields
  * @param WP_User Edit user
  */
  public static function extra_profile_fields($user) {
    $my_courses = array();

    $course_posts = \get_posts(array('post_type' => models\Course::$cpt, 'post_status' => 'publish'));
    foreach ($course_posts as $course) {
      $mepr_user = new \MeprUser($user->ID);
      if(!\MeprRule::is_locked_for_user($mepr_user, $course)) {
        $my_courses[] = new models\Course($course->ID);
      }
    }

    require_once(base\VIEWS_PATH . '/admin/users/extra_profile_fields.php');
  }
}
