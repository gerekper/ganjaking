<?php
namespace memberpress\downloads\controllers\admin;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models;

class FileCategories extends lib\BaseCtaxCtrl {
  public function load_hooks() {
    $this->cpts = models\File::$cpt;
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
      'label' => __('File Categories', 'memberpress-downloads'),
      'labels' => array(
        'name' => __('File Categories', 'memberpress-downloads'),
        'singular_name' => __('File Category', 'memberpress-downloads'),
      ),
      'public' => true,
      'hierarchical' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => true,
      'rewrite' => array(
        'slug' => 'file-category',
        'with_front' => true,
      ),
      'show_admin_column' => true,
      'show_in_rest' => false,
      'rest_base' => '',
      'show_in_quick_edit' => true,
    );

    register_taxonomy(models\File::$file_category_ctax, $this->cpts, $this->ctax);
  }

  /**
  * Add sub menus
  * @see add_action('admin_menu')
  * @return void
  */
  public function add_sub_menu() {
    $file_category_ctax = models\File::$file_category_ctax;
    add_submenu_page(
      base\PLUGIN_NAME,
      __('File Categories', 'memberpress-downloads'),
      __('Categories', 'memberpress-downloads'),
      'manage_options',
      "edit-tags.php?taxonomy={$file_category_ctax}&post_type={$this->cpts}",
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

    if($current_screen->taxonomy === models\File::$file_category_ctax) {
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
    $file_category_ctax = models\File::$file_category_ctax;

    if($current_screen->taxonomy === $file_category_ctax) {
      $submenu_file = "edit-tags.php?taxonomy={$file_category_ctax}&post_type={$this->cpts}";
    }

    return $submenu_file;
  }
}
