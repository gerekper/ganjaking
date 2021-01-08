<?php
namespace memberpress\courses\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses as base;
use memberpress\courses\lib as lib;
use memberpress\courses\models as models;

class CourseCategories extends lib\BaseCtaxCtrl  {
  public static $tax = 'mpcs-course-categories';

  public function load_hooks() {
    $this->cpts = models\Course::$cpt;
    add_action('admin_menu', array($this, 'add_sub_menu'));
    add_action('parent_file', array($this, 'parent_menu_expand'));
    add_action('submenu_file', array($this, 'submenu_highlight'));
  }

  /**
  * Register custom taxonomy
  * @see BaseCtaxCtrl add_action('init')
  * @return void
  */
  public function register_taxonomy() {
    $this->ctax = array(
      'label' => __('Course Categories', 'memberpress-courses'),
      'labels' => array(
        'name' => __('Course Categories', 'memberpress-courses'),
        'singular_name' => __('Course Categories', 'memberpress-courses'),
        'add_new_item' => __('Add New Course Category', 'memberpress-courses'),
        'search_items' => __('Search Categories', 'memberpress-courses'),
        'edit_item' => __('Edit Category', 'memberpress-courses'),
        'view_item' => __('View Category', 'memberpress-courses'),
        'update_item' => __('Update Category', 'memberpress-courses'),
        'back_to_items' => __('Back to Categories', 'memberpress-courses')
      ),
      'public' => true,
      'hierarchical' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => true,
      'rewrite' => array(
        'slug' => 'course-category',
        'with_front' => true,
      ),
      'show_admin_column' => true,
      'show_in_rest' => true,
      'rest_base' => '',
      'show_in_quick_edit' => true,
    );
    register_taxonomy('mpcs-course-categories', $this->cpts, $this->ctax);
  }
  /**
  * Add sub menus
  * @see add_action('admin_menu')
  * @return void
  */
  public function add_sub_menu() {
    add_submenu_page(
      base\PLUGIN_NAME,
      __('Course Categories', 'memberpress-courses'),
      __('Categories', 'memberpress-courses'),
      'manage_options',
      "edit-tags.php?taxonomy=mpcs-course-categories&post_type={$this->cpts}",
      false
    );
  }
  /**
  * Expand the parent menu
  * @see add_action('parent_file')
  * @param string $parent_file Name of the current parent menu
  * @return void
  */
  public function parent_menu_expand($parent_file) {
    global $current_screen;
    if($current_screen->taxonomy === 'mpcs-course-categories') {
      $parent_file = base\PLUGIN_NAME;
    }
    return $parent_file;
  }
  /**
  * Highlight the current selected submenu item
  * @see add_action('submenu_file')
  * @param string $submenu_file Current submenu
  * @return void
  */
  public function submenu_highlight($submenu_file) {
    global $current_screen;
    if($current_screen->taxonomy === 'mpcs-course-categories') {
      $submenu_file = "edit-tags.php?taxonomy=mpcs-course-categories&post_type={$this->cpts}";
    }
    return $submenu_file;
  }
}
