<?php
namespace memberpress\courses\controllers;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\courses\lib as lib;
use memberpress\courses\models as models;
use memberpress\courses\helpers as helpers;

class CoursesApi extends lib\BaseCtrl {
  public static $namespace_str = 'mpcs';
  public static $resource_name_str = 'courses';

  // Here initialize our namespace and resource name.
  public function __construct() {
    parent::__construct();
  }

  public function load_hooks() {
    add_action('rest_api_init', array($this, 'register_routes'));
  }

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    register_rest_route( self::$namespace_str, '/' . self::$resource_name_str .'/lessons', array(
      array(
        'methods'             => \WP_REST_Server::READABLE,
        'callback'            => array( $this, 'fetch_lessons' ),
        'permission_callback' => array( $this, 'fetch_lessons_permissions_check' ),
      ),
    ) );

    register_rest_route( self::$namespace_str, '/' . self::$resource_name_str .'/lessons' . '/(?P<id>[\d]+)', array(
      array(
        'methods'             => \WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'duplicate_lesson' ),
        'permission_callback' => array( $this, 'create_item_permissions_check' ),
      ),
    ) );

    register_rest_route( self::$namespace_str, '/' . self::$resource_name_str .'/curriculum'. '/(?P<id>[\d]+)', array(
      array(
        'methods'             => \WP_REST_Server::READABLE,
        'callback'            => array( $this, 'get_curriculum' ),
        'permission_callback' => array( $this, 'fetch_lessons_permissions_check' ),
      ),
    ) );
  }

  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function fetch_lessons( $request ) {
    $params = $request->get_params();
    $args = array(
      'post_type' => 'mpcs-lesson',
      'fields' => 'ids'
    );

    $args = \array_merge($params, $args);
    $args['per_page'] =

    $query = ( new \WP_Query($args) );
    $lesson_ids = $query->get_posts();
    $data = array();

    foreach( $lesson_ids as $lesson_id ) {
      $itemdata = $this->prepare_item_for_response( $lesson_id );
      $data['lessons'][] = $itemdata;
    }

    $data['meta']['total'] = $query->found_posts;
    $data['meta']['max'] = $query->max_num_pages;
    $data['meta']['count'] = $query->post_count;

    return new \WP_REST_Response( $data, 200 );
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function fetch_lessons_permissions_check( $request ) {
    return current_user_can( 'read' );
  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function duplicate_lesson( $request ) {
    global $wpdb;

    $post_id = absint( $request->get_param( 'id' ) );
    $post = get_post( $post_id );

    if(NULL == $post ) return array();

    // args for new post
    $args = array(
      'comment_status' => $post->comment_status,
      'ping_status'    => $post->ping_status,
      'post_author'    => $post->post_author,
      'post_content'   => $post->post_content,
      'post_excerpt'   => $post->post_excerpt,
      'post_name'      => $post->post_name,
      'post_parent'    => $post->post_parent,
      'post_password'  => $post->post_password,
      'post_status'    => $post->post_status,
      'post_title'     => $post->post_title,
      'post_type'      => $post->post_type,
      'to_ping'        => $post->to_ping,
      'menu_order'     => $post->menu_order
    );

    // insert the new post
    $new_post_id = wp_insert_post( $args );

    // add taxonomy terms to the new post
    $taxonomies = get_object_taxonomies( $post->post_type );
    foreach ( $taxonomies as $taxonomy ) {
      $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
      wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
    }

    $new_post = new models\Lesson($new_post_id);

    if($new_post){
      return new \WP_REST_Response( $new_post->rec, 200 );
    }

    return new WP_Error( 'cant-create', __( 'message', 'memberpress-courses' ), array( 'status' => 500 ) );
  }


  /**
   * Fetches updated curriculum
   *
   * @param  mixed $request
   * @return mixed
   */
  public function get_curriculum($request){
    $post_id = absint( $request->get_param( 'id' ) );
    $curriculum = helpers\Courses::course_curriculum($post_id);
    return new \WP_REST_Response( $curriculum, 200 );
  }

  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_item_permissions_check( $request ) {
    return current_user_can( 'edit_pages' );
  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response( $lesson_id ) {
    $lesson = new models\Lesson($lesson_id);
    $course = $lesson->course();

    return array(
      'ID' => $lesson->ID,
      'title' => $lesson->post_title,
      'permalink' => get_permalink($lesson->ID),
      'courseID' => $course ? $course->ID : '',
      'courseTitle' => $course ? $course->post_title : '',
    );
  }

}