<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** Utilities to be used with APIs, Webhooks and the like **/
abstract class MpdtBaseUtils {
  public $search_fields, $accept_fields, $endpoints, $map,
         $model_class, $class_info, $namespace;

  // API VERSION
  public $version = '1';

  public function __construct() {
    // The map array holds all of the mapping of
    // variable names from the model to api interface
    if(!isset($this->map) || !is_array($this->map)) {
      $this->map = array();
    }

    $this->namespace = "mp/v{$this->version}";
    $this->class_info = $this->get_class_info();
    $this->model_class = $this->class_info->model_class;
    $this->base = $this->class_info->plural;

    // We don't always want all of the model's fields updateable so
    // accept_fields is an array the holds all of the updateable fields
    //if(!isset($this->accept_fields) || !is_array($this->accept_fields)) {
    //  $this->accept_fields = array();
    //}

    // If this file doesn't exist we've got bigger issues than a file not found error
    $this->accept_fields = require(MPDT_DOCS_PATH . "/update_args/{$this->class_info->plural}.php");

    // Figure out search fields
    $base_search = require(MPDT_DOCS_PATH . "/search_args/base.php");
    $custom_search_file = MPDT_DOCS_PATH . "/search_args/{$this->class_info->plural}.php";
    if(file_exists($custom_search_file)) {
      $custom_search = require($custom_search_file);
      $this->search_fields = array_merge($base_search, $custom_search);
    }
    else {
      $this->search_fields = $base_routes;
    }

    // Figure out routes
    $this->endpoints = array(); // default

    // No use for endpoints if we don't have the REST API Plugin installed
    if( mpdt_rest_api_available() ) {
      $base_routes = require(MPDT_DOCS_PATH . "/routes/base.php");
      $custom_routes_file = MPDT_DOCS_PATH . "/routes/{$this->class_info->plural}.php";
      if(file_exists($custom_routes_file)) {
        $custom_routes = require($custom_routes_file);
        $this->endpoints = array_merge($base_routes, $custom_routes);
      }
      else {
        $this->endpoints = $base_routes;
      }
    }
  }

  // Override this in sub-classes when they need more extending happening
  protected function extend_obj(Array $_post) {
    return $_post;
  }

  // Optional method that can be used to trim
  // elements of the results we don't want to show
  public function trim_obj(Array $_post) {
    return $_post;
  }

  public function prepare_obj(Array $_post) {
    $_post = $this->map_vars($_post);

    $member_utils = MpdtUtilsFactory::fetch('member');
    if(isset($_post['author']) && is_numeric($_post['author']) && (int)$_post['author'] > 0) {
      $user = new MeprUser($_post['author']);
      $_post['author'] = $member_utils->map_vars((array)$user->rec);
    }

    $_post = $this->trim_obj($_post);
    $_post = $this->extend_obj($_post);

    return apply_filters('mpdt-prepare-obj-'.$this->class_info->singular, $_post);
  }

  public function get_class_info() {
    $base_class = get_class();
    $utils_class = get_class($this);

    preg_match('/^Mpdt(.*)Utils$/', $utils_class, $m);

    // Not sure how $m[1] wouldn't match but hey
    $singular = (isset($m[1]) ? $m[1] : $m[0]);
    $singular = MpdtInflector::tableize($singular);
    $plural = MpdtInflector::pluralize($singular);
    $api_class = 'Mpdt'.MpdtInflector::classify($plural).'Api';
    $model_class = (isset($this->model_class) && !empty($this->model_class)) ? $this->model_class : 'Mepr'.MpdtInflector::classify($singular);

    return (object)compact('base_class', 'utils_class', 'api_class', 'model_class', 'singular', 'plural');
  }

  public function get_api_info() {
    $cinfo = $this->get_class_info();

    $namespace = "mp/v{$this->version}";
    $base = $cinfo->plural;

    return (object)compact('namespace', 'base');
  }

  public function map_vars($res, $reverse=false) {
    $map = $this->map;

    if($reverse) {
      foreach($map as $k => $v) {
        if(!is_string($v) && !is_integer($v)) {
          unset($map[$k]);
        }
      }

      $map = array_flip($map);
    }

    // use this to prepend values
    $mapped = array();

    foreach($map as $k => $v) {
      if(array_key_exists($k, $res)) {
        if(false!==$map[$k]) {
          $mapped[$map[$k]] = $res[$k];
        }

        unset($res[$k]);
      }
    }

    $res = array_merge($mapped, $res);

    return $res;
  }

  public function separate_post_fields(Array $data, MeprBaseModel $model_obj) {
    $model_data = $post_data = $data;
    $attrs = array_keys($model_obj->attrs);

    foreach($data as $k => $v) {
      if(in_array($k, $attrs)) {
        unset($post_data[$k]); // Get rid of MemberPress fields from post data
      }
      else {
        unset($model_data[$k]); // Get rid of wp fields from cpt data
      }
    }

    return array( $model_data, $post_data );
  }

  public function prepare_data_args(Array $args) {
    return array_merge(array(
      'page' => 1,
      'per_page' => 10,
      'search' => '',
      'order' => 'ID',
      'order_dir' => 'DESC'
    ), $args);
  }

  // Used to implement custom search args
  protected function get_data_query_custom_clauses(Array $args) {
    return '';
  }

  /** Define and retrieve a data query */
  protected function get_data_query(Array $args, $count=false) {
    global $wpdb;

    $rc = new ReflectionClass($this->model_class);

    try {
      $cpt = $rc->getStaticPropertyValue('cpt');
      $post_type_clause = $wpdb->prepare("
        AND p.post_type=%s
      ", $cpt);

    }
    catch(ReflectionException $e) {
      // That property must not exist so let's just blank it out
      $post_type_clause = '';
    }

    $id_clause='';
    if(!empty($args['id'])) {
      $id_clause = $wpdb->prepare("
         AND p.ID = %d
      ",
      $args['id']);
    }

    // Since most data we'll return is CPT related the default
    // will be to pull directly from the posts table, etc
    $search_clause='';
    if(!empty($args['search'])) {
      $search_clause = $wpdb->prepare("
         AND ( p.post_title LIKE %s
               OR p.post_content LIKE %s )
      ",
      '%'.$args['search'].'%',
      '%'.$args['search'].'%');
    }

    $custom_clauses = $this->get_data_query_custom_clauses($args);

    $limit_statement='';
    if(!$count && (int)$args['per_page'] !== -1) {
      $limit_statement = $wpdb->prepare("
        LIMIT %d OFFSET %d
      ",
      (int)$args['per_page'],
      (((int)$args['page']-1) * (int)$args['per_page']));
    }

    $order_statement = "ORDER BY p.{$args['order']} {$args['order_dir']}";

    $select_vars = ($count ? 'COUNT(*)' : 'p.ID');

    $q = "
      SELECT {$select_vars}
        FROM {$wpdb->posts} AS p
       WHERE p.post_status='publish'
         {$post_type_clause}
         {$id_clause}
         {$search_clause}
         {$custom_clauses}
       {$order_statement}
       {$limit_statement}
    ";

    return $q;
  }

  public function get_count($args=array()) {
    return $this->get_data($args, false, true);
  }

  public function get_data($args=array(), $test_data=false, $count=false) {
    global $wpdb;

    $rc = new ReflectionClass($this->model_class);

    $args = $this->prepare_data_args($args);
    $q = $this->get_data_query($args,$count);

    if($count) { return $wpdb->get_var($q); }

    $ids = $wpdb->get_col($q);

    $data = array();
    if($test_data && empty($ids)) {
      for($i=0; $i<$count; $i++) {
        $obj = $rc->newInstanceArgs(array());
        $data[$i] = $this->prepare_obj((array)$obj->rec);
      }
    }
    else {
      foreach($ids as $i => $id) {
        $obj = $rc->newInstanceArgs(array($id));
        $data[$i] = $this->prepare_obj((array)$obj->rec);
      }
    }

    return $data;
  }

  public function get_json() {
    $data = $this->get_data();
    return json_encode($data);
  }

  public function get_event_json($event) {
    $evt_data = $this->get_event_data($event);
    return json_encode($evt_data);
  }

  public function get_event_data($event) {
    $whk = MpdtCtrlFactory::fetch('webhooks');
    $evt_obj = $whk->events[$event];
    $type = $evt_obj->type;

    // If this event has been recently sent then let's use it
    if(($evt = MeprEvent::latest($event))) {
      $events = require(MPDT_DOCS_PATH . '/webhooks/events.php');
      $obj = $evt->get_data();
      $info = $events[$event];

      $utils = MpdtUtilsFactory::fetch($info->type);

      $data = (array)$obj->rec;
      $data = $utils->prepare_obj($data);
    }
    else {
      $data = $this->get_data();
      $data = $data[0];
    }

    return compact('event', 'type', 'data');
  }

  protected function get_where_operator($clauses) {
    return (empty($clauses) ? 'WHERE' : 'AND');
  }

  public static function table_exists($table) {
    global $wpdb;
    $q = $wpdb->prepare('SHOW TABLES LIKE %s', $table);
    $table_res = $wpdb->get_var($q);
    return ($table_res == $table);
  }
}

