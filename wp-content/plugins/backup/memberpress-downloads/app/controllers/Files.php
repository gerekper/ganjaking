<?php
namespace memberpress\downloads\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models,
    memberpress\downloads\helpers as helpers;

class Files extends lib\BaseCtrl {
  public function load_hooks() {
    add_action('generate_rewrite_rules', array($this, 'add_download_endpoint'));
    add_action('template_redirect', array($this, 'download_file'));
    add_filter('query_vars', array($this, 'append_rewrite_query_var'));
    add_shortcode('mpdl-file-links', array($this, 'render_file_links'));
    add_shortcode('mpdl-file-link', array($this, 'render_file_link'));
  }

  /**
  * Register the file download endpoint with the rewrite rules
  * @param WP_Rewrite $wp_rewrite
  * @return array Array of modified rewrite rules
  * @see add_action('generate_rewrite_rules')
  */
  public static function add_download_endpoint($wp_rewrite) {
    $mpdl_rules = array('.*' . models\File::$permalink_slug . '/([^/]+)/?$' => 'index.php?plugin='. base\SLUG_KEY .'&file=$matches[1]&action=download');
    $wp_rewrite->rules = $mpdl_rules + $wp_rewrite->rules;
    return $wp_rewrite->rules;
  }

  /**
  * Handle the download file request
  * @see add_action('template_redirect')
  */
  public static function download_file() {
    $permalink_type = \get_option('permalink_structure');
    $file = \get_query_var(models\File::$cpt, false);
    if(empty($permalink_type) && !empty($file)) {
      $plugin = base\SLUG_KEY;
      $action = 'download';
    }
    else {
      $plugin = \get_query_var('plugin', false);
      $action = \get_query_var('action', false);
      $file   = \get_query_var('file', false);
    }
    if(\strtolower($_SERVER['REQUEST_METHOD']) === 'get' && $plugin === base\SLUG_KEY && $action === 'download' && !empty($file)) {
      $file_parts = \pathinfo($file);
      $file_post = \get_page_by_path($file_parts['filename'], OBJECT, models\File::$cpt);
      $download_file = new models\File($file_post->ID);
      if($file_post) {
        if(\is_plugin_active('memberpress/memberpress.php')) {
          $current_user_id = \get_current_user_id();
          $mepr_user = new \MeprUser($current_user_id);
          if(!\MeprUtils::is_mepr_admin() && \MeprRule::is_locked_for_user($mepr_user, $file_post)) {
            \MeprRulesCtrl::redirect_unauthorized($file_post);
          }
        }
        $download_file->send_download($file);
      }
      else {
        // Redirect here?
      }
    }
  }

  /**
  * Append file to the allowed query vars
  * @param array $vars Current allowed query vars
  * @return array Modified allowed query vars
  */
  public static function append_rewrite_query_var($vars) {
    $slug = models\File::$permalink_slug;
    if(\strtolower($_SERVER['REQUEST_METHOD']) === 'get' && \preg_match("/\/{$slug}\//", $_SERVER['REQUEST_URI'])) {
      $vars = array_merge($vars, array('plugin', 'file', 'action'));
    }
    return $vars;
  }

  /**
  * File links short code
  * @see add_shortcode('mpdl-file-links')
  * @param array $attrs Shortcode attributes category|tag
  * @return string File links HTML
  */
  public static function render_file_links($attrs) {
    $files = array();

    $post_args = array(
      'post_type' => models\File::$cpt,
      'post_status' => 'publish',
      'posts_per_page' => isset( $attrs['limit'] ) && 0 < $attrs['limit'] ? $attrs['limit']  : -1
    );

    if(isset($attrs['category']) && !empty($attrs['category'])) {
      $relation = isset( $attrs['category-relation'] ) && in_array( strtoupper( $attrs['category-relation'] ), array( 'IN', 'AND') )
        ? strtoupper( $attrs['category-relation'] )
        : 'IN';
      if ( strpos( $attrs['category'], ',' ) !== false ) {
        $terms = explode( ',', trim( $attrs['category'] ) );
      } else {
        $terms = trim( $attrs['category'] );
      }
      if(!is_numeric($attrs['category'])) { // Category is a slug or name
        $post_args = \array_merge(
          array(
            'tax_query' => array(
              'relation' => 'OR',
              array(
                'taxonomy' => models\File::$file_category_ctax,
                'field'    => 'name',
                'terms'    => $terms,
                'operator' => $relation
              ),
              array(
                'taxonomy' => models\File::$file_category_ctax,
                'field'    => 'slug',
                'terms'    => $terms,
                'operator' => $relation
              )
            )
          ),
          $post_args
        );
      }
      else { // Category is an ID
        $post_args = \array_merge(
          array(
            'tax_query' => array(
              array(
                'taxonomy' => models\File::$file_category_ctax,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator' => $relation
              )
            )
          ),
          $post_args
        );
      }
    }
    elseif(isset($attrs['tag']) && !empty($attrs['tag'])) {
      $relation = isset( $attrs['tag-relation'] ) && in_array( strtoupper( $attrs['tag-relation'] ), array( 'IN', 'AND' ) )
        ? strtoupper( $attrs['tag-relation'] )
        : 'IN';
      if ( strpos( $attrs['tag'], ',' ) !== false ) {
        $terms = explode( ',', trim( $attrs['tag'] ) );
      } else {
        $terms = trim( $attrs['tag'] );
      }
      if(!is_numeric($attrs['tag'])) {
        $post_args = \array_merge( // Tag is slug or name
          array(
            'tax_query' => array(
              'relation' => 'OR',
              array(
                'taxonomy' => models\File::$file_tag_ctax,
                'field'    => 'name',
                'terms'    => $terms,
                'operator' => $relation
              ),
              array(
                'taxonomy' => models\File::$file_tag_ctax,
                'field'    => 'slug',
                'terms'    => $terms,
                'operator' => $relation
              )
            )
          ),
          $post_args
        );
      }
      else { // Tag is an ID
        $post_args = \array_merge(
          array(
            'tax_query' => array(
              array(
                'taxonomy' => models\File::$file_tag_ctax,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator' => $relation
              )
            )
          ),
          $post_args
        );
      }
    }

    if(isset($attrs['class'])) {
      $link_class = $attrs['class'];
    }

    $file_posts = \get_posts($post_args);

    foreach($file_posts as $post) {
      $files[] = new models\File($post->ID);
    }

    \ob_start();
    require(base\VIEWS_PATH . '/files/file_links.php');
    return \ob_get_clean();
  }

  /**
  * File link short code
  * @see add_shortcode('mpdl-file-link')
  * @param array $attrs Shortcode attributes file_id|id|class
  * @return string File link HTML
  */
  public static function render_file_link($attrs) {
    $files = array();
    if(isset($attrs['file_id'])) {
      $file = new models\File($attrs['file_id']);
      if($file->ID > 0) {
        $files[] = $file;
        if(isset($attrs['id'])) {
          $link_id = $attrs['id'];
        }
        if(isset($attrs['class'])) {
          $link_class = $attrs['class'];
        }
      }
    }

    \ob_start();
      require(base\VIEWS_PATH . '/files/file_links.php');
    return \ob_get_clean();
  }
}
