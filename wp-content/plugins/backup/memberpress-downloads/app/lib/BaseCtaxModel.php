<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

/** Specific base class for CPT Style models */
abstract class CtaxModel extends BuiltinModel {
  //All inheriting classes should set -- public static $ctax (custom taxonomy)
  public static $ctax = '';

  /** This should only be used if the model is using a custom post type **/
  protected function initialize_new_ctax() {
    if(!isset($this->attrs) || !is_array($this->attrs)) {
      $this->attrs = array();
    }

    $tax_slug = $this->get_taxonomy_slug();

    $this->attrs = array_merge(
      array(
        'term_id'          => array( 'default' => null,      'type' => 'int' ),
        'name'             => array( 'default' => '',        'type' => 'string' ),
        'slug'             => array( 'default' => '',        'type' => 'string' ),
        'taxonomy'         => array( 'default' => $tax_slug, 'type' => 'string' ),
        'description'      => array( 'default' => '',        'type' => 'string' ),
        'parent'           => array( 'default' => '',        'type' => 'int' ),
        'count'            => array( 'default' => 0,         'type' => 'int' ),
        'term_group'       => array( 'default' => 0,         'type' => 'string' ),
        'term_taxonomy_id' => array( 'default' => 0,         'type' => 'string' ),
      ),
      $this->attrs
    );

    $this->rec = $this->get_defaults_from_attrs($this->attrs);

    return $this->rec;
  }

  /** Requires defaults to be set */
  protected function load_ctax($id, $ctax, $attrs) {
    $this->attrs = $this->meta_attrs = $attrs;

    // Short Circuit if id is null/empty/non-numeric
    if(empty($id) || !is_numeric($id)) {
      return $this->initialize_new_ctax();
    }

    $term = (array)get_term($id);
    if( null === $term ||
        (isset($term['taxonomy']) && ($term['taxonomy'] != $ctax)) ||
        isset($term['errors']) ) {
      $this->initialize_new_ctax();
    }
    else {
      if(isset($term['meta'])) {
        unset($term['meta']);
      }

      $this->rec = (object)$term;
      $this->load_meta($id);
    }
  }

  /** Requires defaults to be set */
  protected function load_meta($id) {
    $metas = get_term_meta($id);

    $rec = array();

    // Unserialize and set appropriately
    foreach( $this->attrs as $attr => $config ) {
      $key = $this->get_attr_key($attr);
      if(isset($metas[$key])) {
        if(count($metas[$key]) > 1) {
          $rec[$attr] = array();
          foreach($metas[$key] as $sub_key => $sub_val) {
            $rec[$attr][$sub_key] = maybe_unserialize($sub_val);
          }
        }
        else {
          $meta_val = $metas[$key][0];
          if($meta_val==='' && strpos($config['type'],'bool')===0) {
            $rec[$attr] = false;
          }
          else {
            $rec[$attr] = maybe_unserialize($meta_val);
          }
        }
      }
    }

    $defaults = (array)$this->get_defaults_from_attrs($this->attrs);
    $this->rec = (object)array_merge((array)$this->rec,$defaults,$rec);
  }

  public function store($validate = true) {
    // Term meta errors
    if($validate) {
      try {
        $this->validate();
      }
      catch(Exception $e) {
        return new WP_Error(
          get_class($e),
          sprintf(
            __('%s was unable to be saved: %s', 'memberpress-downloads'),
            get_class($this),
            $e->getMessage()
          )
        );
      }
    }

    $ctax = $this->get_taxonomy_slug();

    if(isset($this->term_id) and !is_null($this->term_id)) {
      $id = wp_update_term($this->term_id, $ctax, (array)$this->rec);
    }
    else {
      $id = wp_insert_term($this->name, $ctax, (array)$this->rec);
    }

    // Builtin Error
    if(is_wp_error($id)) {
      return new WP_Error(
        'CreateException',
        sprintf(
          __('%s was unable to be saved: %s', 'memberpress-downloads'),
          get_class($this),
          $id->get_error_message()
        )
      );
    }
    else {
      if(is_numeric($id)) {
        $this->term_id = $id;
      }
      else if(is_array($id) && isset($id['term_id'])) {
        $this->term_id = $id['term_id'];
      }
    }

    $this->store_meta();

    return $id;
  }

  public function store_meta() {
    $attrs = (array)$this->get_meta_values();

    $id = $this->rec->term_id;

    foreach($attrs as $attr => $attr_value) {
      $attr_key = $this->get_attr_key($attr);
      update_term_meta($id, $attr_key, $this->cast_attr($attr,$attr_value));
    }
  }

  public function destroy() {
    $res = wp_delete_term($this->term_id, $this->get_taxonomy_slug());

    if(false===$res) {
      throw new CreateException(sprintf(__( 'This was unable to be deleted.', 'memberpress-downloads')));
    }

    return $res;
  }

  //Should probabaly add a delim char check to add before the args
  //similar to how I did it in Options
  public function url($args = '') {
    $link = get_term_link($this->term_id, $this->get_taxonomy_slug());
    return apply_filters( base\SLUG_KEY.'_ctax_model_url', "{$link}{$args}", $this );
  }

  protected static function get_one($args) {
    global $wpdb;

    if(!is_numeric($args) && !is_array($args) && !is_object($args)) {
      return false;
    }

    // Get the sub class ... only works in PHP 5.3 or higher
    $class = get_called_class();

    if(is_numeric($args)) {
      $args = array($wpdb->prepare('t.term_id=%d',$args));
    }

    $data = self::get_all_data(
      $class,
      ARRAY_A,     // $type
      't.term_id', // $orderby
      'ASC',       // $order
      1,           // $limit
      0,           // $offset
      array(),     // $selects
      array(),     // $joins
      $args        // $wheres
    );

    if(!empty($data) && is_array($data) &&
       !empty($data[0]) && is_array($data[0])) {
      $obj = new $class();
      $obj->load_from_array((array)$data[0]);
      return $obj;
    }

    return false;
  }

  protected static function get_all($order_by='',$limit='',$args=array()) {
    $class = get_called_class();

    if(is_numeric($args)) {
      $args = array('term_id' => $args);
    }

    $order = 'ASC';

    if(!empty($order_by)) {
      $orders=explode(' ', $order_by);
      if(isset($orders[0])) {
        $order_by = $orders[0];
      }
      if(isset($orders[1])) {
        $order = $orders[1];
      }
    }

    if(empty($order_by)) {
      $order_by = 't.term_id';
    }

    $data = self::get_all_data(
      $class,
      ARRAY_A,    // $type
      $order_by, // $orderby
      $order,    // $order
      $limit,    // $limit
      0,         // $offset
      array(),   // $selects
      array(),   // $joins
      $args      // $wheres
    );

    $objs = false;
    if(!empty($data) && is_array($data)) {
      $objs = array();
      foreach($data as $row) {
        $obj = new $class();
        $obj->load_from_array((array)$row);
        $objs[] = $obj;
      }
    }

    return $objs;
  }

  protected static function get_count($args=array()) {
    global $wpdb;
    $db = Db::fetch();

    // Get the sub class
    $class = get_called_class();

    $r = new ReflectionClass($class);
    $ctax = $r->getStaticPropertyValue('ctax');

    return $db->get_count($wpdb->terms,array('taxonomy'=>$ctax));
  }

  private static function get_all_data( $class, // get_class relies on $this so we have to pass the name in
                                        $type=ARRAY_A,
                                        $orderby='t.term_id',
                                        $order='ASC',
                                        $limit=100,
                                        $offset=0,
                                        $selects=array(),
                                        $joins=array(),
                                        $wheres=array() ) {
    global $wpdb;

    $rc = new ReflectionClass($class);
    $obj = $rc->newInstance();
    $ctax = $rc->getStaticPropertyValue('ctax');

    $attrs = $obj->get_meta_attrs();
    $meta_keys = array_keys($attrs);

    array_unshift(
      $wheres,
      $wpdb->prepare('tx.taxonomy=%s', $ctax)
    );

    if(empty($selects)) {
      $selects = array(
        '`t`.`term_id` AS `term_id`',
        '`t`.`name` AS `name`',
        '`t`.`slug` AS `slug`',
        '`tx`.`taxonomy` AS `taxonomy`',
        '`tx`.`description` AS `description`',
        '`tx`.`parent` AS `parent`',
        '`tx`.`count` AS `count`',
        '`t`.`term_group` AS `term_group`',
        '`tx`.`term_taxonomy_id` AS `term_taxonomy_id`',
      );
      $fill_selects = true;
    }
    else {
      $fill_selects = false;
    }

    foreach($meta_keys as $meta_key) {
      $meta_key_getter = "{$meta_key}_str";
      $meta_key_str = $obj->{$meta_key_getter};

      if($fill_selects) {
        $selects[] = "tm_{$meta_key}.meta_value AS {$meta_key}";
      }

      $joins[] = $wpdb->prepare( "
          LEFT JOIN {$wpdb->termmeta} AS tm_{$meta_key}
            ON tm_{$meta_key}.term_id=t.term_id
           AND tm_{$meta_key}.meta_key=%s
        ",
        $meta_key_str
      );
    }

    array_unshift(
      $joins,
      "
      INNER JOIN {$wpdb->term_taxonomy} AS tx
         ON tx.term_id=t.term_id
      "
    );

    $selects_str = join(",\n             ", $selects);
    $joins_str = join("\n", $joins);
    $wheres_str = join( ' AND ', $wheres );

    if(empty($limit)) {
      $limit_str = '';
    }
    else {
      $limit_str = "
        LIMIT {$limit}
      ";
    }

    if(empty($offset)) {
      $offset_str = '';
    }
    else {
      $offset_str = "
        OFFSET {$limit}
      ";
    }

    $q = "
      SELECT {$selects_str}
        FROM {$wpdb->terms} AS t {$joins_str}
       WHERE {$wheres_str}
       ORDER BY {$orderby} {$order}
       {$limit_str}
       {$offset_str}
    ";

    $res = $wpdb->get_results($q,$type);

    // two layer maybe_unserialize
    for( $i=0; $i<count($res); $i++ ) {
      foreach( $res[$i] as $k => $val ) {
        $res[$i][$k] = maybe_unserialize($val);
      }
    }

    return $res;
  }

  public function get_taxonomy_slug() {
    $class = get_class($this);
    return Utils::get_property($class, 'ctax');
  }
}

