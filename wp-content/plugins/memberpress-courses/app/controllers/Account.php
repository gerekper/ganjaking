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
  *
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
      $current_user = lib\Utils::get_currentuserinfo();
      $mepr_user = new \MeprUser($current_user->ID);
      $options = \get_option('mpcs-options');

      $courses = get_posts(array('post_type' => models\Course::$cpt, 'post_status' => 'publish', 'posts_per_page' => '-1', 'orderby'=> 'title', 'order' => 'ASC'));

      if(false == \MeprUtils::is_logged_in_and_an_admin()){
        $courses = array_filter($courses, function($course) use ( $mepr_user ) {
          return false == \MeprRule::is_locked_for_user($mepr_user, $course);
        });
      }

      $courses_ids = array_column( $courses, 'ID' );
      $per_page = apply_filters('mpcs_courses_per_page', 6);

      $course_query = new \WP_Query(array('post_type' => models\Course::$cpt, 'post_status' => 'publish', 'posts_per_page' => $per_page, 'paged' => $paged, 'orderby'=> 'post__in', 'order' => 'ASC', 'post__in' => $courses_ids));
      $course_posts = $course_query->get_posts();

      foreach ($course_posts as $course) {
        $my_courses[] = new models\Course($course->ID);
      }

      \MeprView::render('/account/courses_list', get_defined_vars());
    }
  }
}
