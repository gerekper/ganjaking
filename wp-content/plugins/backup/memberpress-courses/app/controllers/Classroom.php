<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\helpers as helpers;
use memberpress\courses\controllers\admin as ctrl;
use memberpress\courses\models as models;

class Classroom extends App {
  public function load_hooks() {
    add_action( 'init',  array($this, 'add_image_sizes') );
    add_filter( 'image_size_names_choose', array($this, 'custom_image_sizes') );
    add_filter( 'excerpt_length', array($this, 'custom_excerpt_length'), 900 );
    add_filter( 'determine_current_user', array($this, 'guest_preview'), 900 );
    add_action( 'wp_head', array($this, 'classroom_theme_customize'));
    add_filter( 'show_admin_bar', array( $this, 'maybe_hide_admin_bar' ));
    add_filter( base\SLUG_KEY . '_customiser_settings', array($this, 'classroom_settings') );
    add_action( 'init', array($this, 'classroom_menu') );
    add_filter( 'nav_menu_link_attributes', array($this, 'add_class_to_menu_anchors'), 10, 3 );
    add_filter( 'wp_nav_menu_objects', array($this, 'filter_classroom_menu'), 10, 2);
    add_filter( base\SLUG_KEY . '_customiser_sections', array($this, 'classroom_section') );
  }

  /**
   * Hide the admin bar when in classroom mode.
   *
   * @param  boolean  $show   Whether to show the admin bar.
   *
   * @return boolean
   */
  public function maybe_hide_admin_bar( $show ) {

    $options = \get_option('mpcs-options');
    $classroom_mode = helpers\Options::val($options,'classroom-mode', 1);

    if ( empty( $classroom_mode ) ) {
      return $show;
    }

    if (
      is_post_type_archive( models\Lesson::$cpt ) || is_singular( models\Lesson::$cpt ) ||
      is_post_type_archive( models\Course::$cpt ) || is_singular( models\Course::$cpt )
    ) {
      $show = false;
    }
    return $show;
  }

  /**
   * Custom image Size for website
   *
   * @return void
   */
  public function add_image_sizes() {
    if ( function_exists( 'add_image_size' ) ) {
      add_image_size( 'mpcs-course-thumbnail', 600, 400, true );
    }
  }

  /**
   * Custom image size name
   *
   * @param  mixed $sizes
   * @return void
   */
  public function custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
      'mpcs-course-thumbnail' => __('MemberPress Courses Thumbnail', 'memberpress-courses')
    ) );
  }

  /**
   * Reduce excerpt to 20 words
   *
   * @param  mixed $length
   * @return void
   */
  function custom_excerpt_length( $length ) {
    return 20;
  }

  /**
   * Preview as logged out user
   *
   * @param  mixed $user_id
   * @return bool
   */
  public function guest_preview($user_id){
    if( isset($_GET['preview']) && 'out' === $_GET['preview'] ){
      return true;
    }

    return $user_id;
  }


  /**
   * Add Classroom Section
   *
   * @param  mixed $sections
   * @return void
   */
  public function classroom_section($sections) {
    $sections[] = array(
      'name' => base\SLUG_KEY . '_classroom',
      'title' => __( 'MemberPress Classroom', 'memberpress-courses' )
    );

    return $sections;
  }


  /**
   * Add Classroom settings to Customizer
   */
  public function classroom_settings( $settings ) {

    $classroom_settings = array(
     /* array(
        'name' => 'mpcs-options[classroom-mode]',
        'label' => esc_html__( 'Classroom Mode', 'memberpress-courses' ),
        'type' => 'checkbox',
        'default' => '0',
        'sanitize_callback' => 'intval',
        'section' => base\SLUG_KEY . '_classroom'
      ),*/
      array(
        'name' => 'mpcs-options[brand-color]',
        'label' => esc_html__( 'Brand Color', 'memberpress-courses' ),
        'type' => 'color',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#2c3637',
        'section' => base\SLUG_KEY . '_classroom'
      ),
      array(
        'name' => 'mpcs-options[accent-color]',
        'label' => esc_html__( 'Accent Color', 'memberpress-courses' ),
        'type' => 'color',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#2c3637',
        'section' => base\SLUG_KEY . '_classroom'
      ),
      array(
        'name' => 'mpcs-options[progress-color]',
        'label' => esc_html__( 'Progress Color', 'memberpress-courses' ),
        'type' => 'color',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#1da69a',
        'section' => base\SLUG_KEY . '_classroom'
      ),
      array(
        'name' => 'mpcs-options[menu-text-color]',
        'label' => esc_html__( 'Menu Text Color', 'memberpress-courses' ),
        'type' => 'color',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => '#ffffff',
        'section' => base\SLUG_KEY . '_classroom'
      ),
      // array(
      //   'name' => 'mpcs-options[link-color]',
      //   'label' => esc_html__( 'Link Color', 'memberpress-courses' ),
      //   'type' => 'color',
      //   'sanitize_callback' => 'sanitize_hex_color',
      //   'default' => '#435253',
      //   'section' => base\SLUG_KEY . '_classroom'
      // ),
      array(
        'name' => 'mpcs-options[classroom-logo]',
        'label' => esc_html__( 'Logo', 'memberpress-courses' ),
        'type' => 'image',
        'sanitize_callback' => 'absint',
        'default' => '',
        'section' => base\SLUG_KEY . '_classroom'
      ),
    );

    $settings = array_merge($settings, $classroom_settings);
    return $settings;

  }


  /**
   * Create Classroom Menu
   *
   * @return void
   */
  public function classroom_menu(){
    // Check if the menu exists
    $menu_name   = 'MemberPress Classroom';
    $menu_exists = wp_get_nav_menu_object( $menu_name );

    // If it doesn't exist, let's create it.
    if ( ! $menu_exists ) {
      $menu_id = wp_create_nav_menu($menu_name);

      // Set up default menu items
      wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'  =>  __( 'My Courses', 'memberpress-courses' ),
        'menu-item-url'    =>  add_query_arg('type', 'mycourses', get_home_url( null, helpers\Courses::get_permalink_base() )),
        'menu-item-status' => 'publish'
      ) );

      wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'   =>  __( 'All Courses', 'memberpress-courses' ),
        'menu-item-url'     => get_home_url( null, helpers\Courses::get_permalink_base() ),
        'menu-item-status'  => 'publish'
      ) );

    }
  }


  /**
   * Add CSS classes to a tag
   *
   * @param  mixed $atts
   * @param  mixed $item
   * @param  mixed $args
   * @return void
   */
  public function add_class_to_menu_anchors($atts, $item, $args){
    if('MemberPress Classroom' !== $args->menu) return $atts;

    if( !isset($args->device) || $args->device != "small" ){
      $atts['class'] = 'btn btn-link';
    }

    return $atts;
  }



  /**
   * Filter Classroom Menu
   *
   * @param  mixed $sorted_menu
   * @param  mixed $args
   * @return void
   */
  public function filter_classroom_menu($sorted_menu, $args){
    if('MemberPress Classroom' !== $args->menu) return $sorted_menu;

    foreach ($sorted_menu as $key => $item) {
      if( false == \in_array($item->post_name, array('all-courses', 'my-courses')) ){
        continue;
      }

      // Show My Courses and All Courses only on archive
      if( false == helpers\Courses::is_course_archive() ){
        unset($sorted_menu[$key]);
      }

      // Show My Courses only if user is logged in
      if( false == \MeprUtils::is_user_logged_in() && 'my-courses' == $item->post_name  ){
        unset($sorted_menu[$key]);
      }
    }

    return $sorted_menu;
  }



  /**
   * Classroom Theme Customizations
   *
   * @return void
   */
  public function classroom_theme_customize(){
    $options = \get_option('mpcs-options');
    $brand_color = implode(', ', helpers\Options::get_rgb($options, 'brand-color') );
    $accent_color = implode(', ', helpers\Options::get_rgb($options, 'accent-color') );
    $progress_color = implode(', ', helpers\Options::get_rgb($options, 'progress-color') );
    $menu_text_color = implode(', ', helpers\Options::get_rgb($options, 'menu-text-color') );
    // $link_color = implode(', ', helpers\Options::get_rgb($options, 'link-color') );
    ?>
    <style type="text/css">

      .mpcs-classroom .nav-back i,
      .mpcs-classroom .navbar-section a.btn,
      .mpcs-classroom .navbar-section a,
      .mpcs-classroom .navbar-section button {
        color: rgba(<?php echo $menu_text_color ?>) !important;
      }

      .mpcs-classroom .navbar-section .dropdown .menu a {
        color: rgba(<?php echo $accent_color ?>) !important;
      }

      .mpcs-classroom .mpcs-progress-ring {
        background-color: rgba(<?php echo $progress_color ?>) !important;
        background-image: linear-gradient(transparent 50%, rgba(<?php echo $progress_color ?>) 50%), linear-gradient(90deg, rgb(204, 204, 204) 50%, transparent 50%) !important;
      }

      .mpcs-classroom .mpcs-course-filter .dropdown .btn span,
      .mpcs-classroom .mpcs-course-filter .dropdown .btn i,
      .mpcs-classroom .mpcs-course-filter .input-group .input-group-btn,
      .mpcs-classroom .mpcs-course-filter .input-group .mpcs-search,
      .mpcs-classroom .mpcs-course-filter .input-group input[type=text],
      .mpcs-classroom .mpcs-course-filter .dropdown a,
      .mpcs-classroom .pagination,
      .mpcs-classroom .pagination i,
      .mpcs-classroom .pagination a {
        color: rgba(<?php echo $accent_color ?>) !important;
        border-color: rgba(<?php echo $accent_color ?>) !important;
      }

      /* body.mpcs-classroom a{
        color: rgba(<?php // echo $link_color ?>);
      } */

      #mpcs-navbar,
      #mpcs-navbar button#previous_lesson_link,
      #mpcs-navbar button#previous_lesson_link:hover {
        background: rgba(<?php echo $brand_color ?>);
      }

      .course-progress .user-progress,
      .btn-green,
      #mpcs-navbar button:not(#previous_lesson_link){
        background: rgba(<?php echo $progress_color ?>, 0.9);
      }

      .btn-green:hover,
      #mpcs-navbar button:not(#previous_lesson_link):focus,
      #mpcs-navbar button:not(#previous_lesson_link):hover{
        background: rgba(<?php echo $progress_color ?>);
      }

      .btn-green{border: rgba(<?php echo $progress_color ?>)}

      .course-progress .progress-text,
      .mpcs-lesson i.mpcs-circle-regular {
        color: rgba(<?php echo $progress_color ?>)
      }

      #mpcs-main #bookmark, .mpcs-lesson.current{background: rgba(<?php echo $progress_color ?>, 0.3)}

      .mpcs-instructor .tile-subtitle{
        color: rgba(<?php echo $progress_color ?>, 1)
      }

    </style>
    <?php
  }




}
