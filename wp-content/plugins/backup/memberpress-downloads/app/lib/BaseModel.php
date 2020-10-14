<?php
namespace memberpress\downloads\lib;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base;

abstract class BaseModel {
  protected $rec, $attrs, $defaults, $custom_attr_keys;

  public function __get($name) {
    $value = null;

    if($this->magic_method_handler_exists($name)) {
      $value = $this->call_magic_method_handler('get',$name);
    }

    $object_vars = array_keys(get_object_vars($this));
    $rec_array = (array)$this->rec;

    if(in_array($name, $object_vars)) {
      $value = $this->$name;
    }
    else if(array_key_exists($name, $rec_array)) {
      if(is_array($this->rec)) {
        $value = $this->rec[$name];
      }
      else {
        $value = $this->rec->$name;
      }
    }

    // _str appended to the end of an attribute will return it's key
    if(preg_match('/(.*)_str$/', $name, $m)) {
      $attr = $m[1];
      $attr_key = $this->get_attr_key($attr);

      if(!empty($attr_key)) {
        $value = $attr_key;
      }
    }

    // Alternative way to filter results through an sub class method
    $extend_fn = "__extend_model_get_{$name}";
    if(method_exists($this,$extend_fn)) {
      $value = call_user_func(array($this,$extend_fn), $value);
    }

    // A way to extend all get handlers in a special sub class method
    $extend_fn = "__extend_model_get";
    if(method_exists($this,$extend_fn)) {
      $value = call_user_func(array($this,$extend_fn), $name, $value);
    }

    return apply_filters(base\SLUG_KEY . '_get_model_attribute_'.$name, $this->cast_attr($name,$value), $this);
  }

  public function __set($name, $value) {
    $value = apply_filters(base\SLUG_KEY . '_set_model_attribute_'.$name, $value, $this);

    // Alternative way to filter results through an sub class method
    $extend_fn = "__extend_model_set_{$name}";
    if(method_exists($this,$extend_fn)) {
      $value = call_user_func(array($this,$extend_fn), $value);
    }

    // A way to extend all set handlers in a special sub class method
    $extend_fn = "__extend_model_set";
    if(method_exists($this,$extend_fn)) {
      $value = call_user_func(array($this,$extend_fn), $name, $value);
    }

    if($this->magic_method_handler_exists($name)) {
      return $this->call_magic_method_handler('set', $name, $value);
    }

    $object_vars = array_keys(get_object_vars($this));
    $rec_array = (array)$this->rec;

    if(in_array($name,$object_vars)) {
      $this->$name = $this->cast_attr($name,$value);
    }
    else if(array_key_exists($name, $rec_array)) {
      if(is_array($this->rec)) {
        $this->rec[$name] = $this->cast_attr($name,$value);
      }
      else {
        $this->rec->$name = $this->cast_attr($name,$value);
      }
    }
    else {
      $this->$name = $this->cast_attr($name,$value);
    }
  }

  public function __isset($name) {
    if($this->magic_method_handler_exists($name)) {
      return $this->call_magic_method_handler('isset', $name);
    }

    if(is_array($this->rec)) {
      return isset($this->rec[$name]);
    }
    else if(is_object($this->rec)) {
      return isset($this->rec->$name);
    }
    else {
      return false;
    }
  }

  public function __unset($name) {
    if($this->magic_method_handler_exists($name)) {
      return $this->call_magic_method_handler('unset', $name);
    }

    if(is_array($this->rec)) {
      unset($this->rec[$name]);
    }
    else if(is_object($this->rec)) {
      unset($this->rec->$name);
    }
  }

  /** We just return a JSON encoding of the attributes in the model when we
    * try to get a string for the model. */
  public function __toString() {
    return json_encode((array)$this->rec);
  }

  protected function get_defaults_from_attrs($attrs) {
    $defaults = array();
    foreach($attrs as $attr => $value) {
      $defaults[$attr] = $this->cast_attr($attr,$value['default']);
    }

    return (object)$defaults;
  }

  /** Initializes the model. This method *requires* that you have the static method get_one implemented */
  public function initialize($attrs, $obj=null) {
    $this->rec = $this->get_defaults_from_attrs($attrs);
    $this->attrs = $attrs;

    if(!is_null($obj)) {
      if(is_numeric($obj) && $obj > 0) {
        $class = get_class($this);

        //$obj = $class::get_one((int)$obj);
        //$obj = call_user_func("{$class}::get_one",(int)$obj);

        // ReflectionMethod is less error prone than the other two methods above
        $rm = new \ReflectionMethod($class, 'get_one');
        $obj = $rm->invoke(null, (int)$obj);
      }

      if(is_object($obj) && $obj instanceof BaseModel) {
        $this->load_from_array((array)$obj->get_values());
      }
      else if(is_array($obj) || is_object($obj)) {
        $this->load_from_array((array)$obj);
      }
    }
  }

  /** Get only the data that is specified as attributes */
  public function get_values() {
    return Utils::filter_array_keys((array)$this->rec,array_keys((array)$this->attrs));
  }

  /** Get all the data as an array */
  public function get_record() {
    return (array)$this->rec;
  }

  /** Get all the attributes and default values */
  public function get_attrs() {
    return (array)$this->attrs;
  }

  // create a duplicate model without an id
  public function duplicate() {
    $values = (array)$this->rec;

    if(isset($values['id'])) { unset($values['id']); }
    if(isset($values['ID'])) { unset($values['ID']); }

    $class = get_class($this);

    $r = new ReflectionClass($class);
    $obj = $r->newInstance();

    $obj->load_from_array($values);

    return $obj;
  }

  public function load_from_array($values) {
    $unserialized_values = array();
    $values = (array)$values;
    $attrs = (array)$this->attrs;

    foreach($values as $key => $value) {
      // Try to detect the type appropriately
      if(isset($attrs[$key])) {
        if(in_array($attrs[$key]['type'],array('bool','boolean'))) {
          $value = (bool)$value;
        }
        else if($attrs[$key]['type']=='float') {
          $value = (float)$value;
        }
        else if($attrs[$key]['type']=='integer') {
          $value = (integer)$value;
        }
      }
      $unserialized_values[$key] = maybe_unserialize($value);
    }

    $this->rec = (object)array_merge((array)$this->rec,(array)$unserialized_values);
  }

  public function load_from_get($unset_bools=false) {
    return $this->load_from_request('get',$unset_bools);
  }

  public function load_from_post($unset_bools=false) {
    return $this->load_from_request('post',$unset_bools);
  }

  /** This function assumes that in your web form you've used the attribute keys by
    * utilizing the $obj->{attribute_name}_str convention.
    */
  public function load_from_request($type='request',$unset_bools=false) {
    $type = strtolower($type);

    if($type=='post') {
      $request = $_POST;
    }
    else if($type=='get') {
      $request = $_GET;
    }
    else {
      $request = $_REQUEST;
    }

    $attr_keys = $this->get_attr_keys();
    $request = Utils::filter_array_keys($request,$attr_keys);

    $load_array = array();
    foreach($request AS $key => $value) {
      $attr = $this->get_attr_from_key($key);

      if(!empty($attr)) {
        $load_array[$attr] = $this->cast_attr($attr,$value);
      }
    }

    // Maybe unset bools
    if($unset_bools) {
      $attrs = $this->get_attrs();
      foreach($attrs as $attr => $config) {
        $key = $this->get_attr_key($attr);
        if(0 === strpos($config['type'],'bool') && !isset($request[$key])) {
          $load_array[$attr] = false;
        }
      }
    }

    $this->load_from_array($load_array);
    $this->sanitize();
  }

  // Alias just for convenience
  public function load_by_array($values) {
    $this->load_from_array($values);
  }

  // Alias just for convenience
  public function load_data($values) {
    $this->load_from_array($values);
  }

  /* Ensure that the object validates. */
  public function validate() {
    return true;
  }

  /* Sanitize data input by users. */
  public function sanitize() {
    return true;
  }

  /* Store the object in the database */
  abstract public function store($validate=true);

  abstract public function destroy();

  // If this function exists it will override the default behavior of looking in the rec object
  protected function magic_method_handler_exists($name) {
    return in_array("mgm_{$name}", get_class_methods($this));
  }

  protected function call_magic_method_handler($mgm, $name, $value='') {
    return call_user_func_array( array($this, "mgm_{$name}"), array( $mgm, $value ) );
  }

  protected function validate_not_null($var, $field='') {
    if(is_null($var)) {
      throw new CreateException(sprintf(__('%s must not be empty', 'memberpress-downloads'),$field));
    }
  }

  protected function validate_not_empty($var, $field='') {
    if($var === '' || $var === '0' || $var === 0 || $var === false) {
      throw new CreateException(sprintf(__('%s must not be empty', 'memberpress-downloads'),$field));
    }
  }

  protected function validate_is_bool($var, $field='') {
    if(!is_bool($var) && $var!=0 && $var!=1) {
      throw new CreateException(sprintf(__('%s must be true or false', 'memberpress-downloads'),$field));
    }
  }

  protected function validate_is_array($var, $field='') {
    if(!is_array($var)) {
      throw new CreateException(sprintf(__('%s must be an array', 'memberpress-downloads'),$field));
    }
  }

  protected function validate_is_in_array($var, $lookup, $field='') {
    if(is_array($lookup) && !in_array($var, $lookup)) {
      throw new CreateException(sprintf(__('%1$s must be %2$s NOT %3$s', 'memberpress-downloads'),$field, implode( ' '.__('or', 'memberpress-downloads').' ', $lookup), $var));
    }
  }

  protected function validate_is_url($var, $field='') {
    if(!Utils::is_url($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid url', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_currency($var, $min=0.00, $max=null, $field='') {
    if(!is_numeric($var) || $var < $min || (!is_null($max) && $var > $max)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid representation of currency', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_numeric($var, $min=0, $max=null, $field='') {
    if(!is_numeric($var) || $var < $min || (!is_null($max) && $var > $max)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid number', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_email($var, $field='') {
    if(!Utils::is_email($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid email', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_phone($var, $field='') {
    if(!Utils::is_phone($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid phone number', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_ip_addr($var, $field='') {
    if(!Utils::is_ip($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid IP Address', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_is_date($var, $field='') {
    if(!Utils::is_date($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid date', 'memberpress-downloads'),$field,$var));
    }
  }

  // Pretty much all we can do here is make sure it's a number and not empty
  protected function validate_is_timestamp($var, $field='') {
    if(empty($var) || !is_numeric($var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must be a valid timestamp', 'memberpress-downloads'),$field,$var));
    }
  }

  protected function validate_regex($pattern, $var, $field='') {
    if(!preg_match($pattern, $var)) {
      throw new CreateException(sprintf(__('%1$s (%2$s) must match the regex pattern: %3$s', 'memberpress-downloads'),$field,$var,$pattern));
    }
  }

  public static function find($id) {
    return self::get_one($id);
  }

  public static function get_one($args) {
    if(!is_numeric($args) && !is_array($args) && !is_object($args)) {
      return false;
    }

    $db = Db::fetch();

    $class = get_called_class();

    if(is_numeric($args)) {
      $args = array('id' => $args);
      if($class=='User') {
        $args = array('ID' => $args);
      }
    }

    return $db->get_one_model($class, $args);
  }

  public static function get_all($order_by='',$limit='',$args=array()) {
    $db = Db::fetch();
    $class = get_called_class();
    return $db->get_models($class, $order_by, $limit, $args);
  }

  public static function get_count($args=array()) {
    $db = Db::fetch();
    $class = get_called_class();

    // Get the right table for this model
    $table = $db->get_table_for_model($class);

    return $db->get_count($table,$args);
  }

  public function get_attr_keys() {
    $attrs = $this->get_attrs();
    $class = get_class($this);

    $attr_keys = array();
    foreach($attrs as $attr => $attr_val) {
      $attr_keys[$attr] = $this->get_attr_key($attr);
    }

    return $attr_keys;
  }

  public function get_attr_key($attr) {
    if(($custom_key = $this->get_custom_attr_key($attr))) {
      return $custom_key;
    }

    $attrs = $this->get_attrs();

    if(array_key_exists($attr, $attrs)) {
      $class = get_class($this);
      $model_str = wp_unslash(strtolower(preg_replace('/^' . wp_slash(base\MODELS_NAMESPACE) . '(.*)/', '$1', $class)));
      return "_".base\SLUG_KEY."_{$model_str}_{$attr}";
    }

    return '';
  }

  public function get_attr_from_key($key) {
    if(($custom_key_attr = $this->get_attr_from_custom_key($key))) {
      return $custom_key_attr;
    }

    $class = get_class($this);
    $model_str = strtolower(preg_replace('/^'.base\ROOT_NAMESPACE.'(.*)/', '$1', $class));
    $attrs = $this->get_attrs();

    if( preg_match("/^_".base\SLUG_KEY."_{$model_str}_(.*)$/", $key, $m) &&
        array_key_exists($m[1], $attrs) ) {
      return $m[1];
    }

    return '';
  }

  protected function get_custom_attr_key($attr) {
    if( isset($this->custom_attr_keys) &&
        is_array($this->custom_attr_keys) &&
        isset($this->custom_attr_keys[$attr]) ) {
      return $this->custom_attr_keys[$attr];
    }

    return false;
  }

  protected function get_attr_from_custom_key($key) {
    if( isset($this->custom_attr_keys) &&
        is_array($this->custom_attr_keys) ) {
      $flipped = array_flip($this->custom_attr_keys);

      if(isset($flipped[$key])) {
        return $flipped[$key];
      }
    }

    return false;
  }

  protected function cast_attr($name, $value) {
    $attrs = $this->get_attrs();

    if(is_object($value) || is_array($value)) {
      // TODO: Not sure what to do here? This should probably be an error condition.
      return $value;
    }

    if(isset($attrs[$name]) && isset($attrs[$name]['type'])) {
      switch($attrs[$name]['type']) {
        case 'string':
        case 'datetime':
        case 'date':
          return (string)$value;
        case 'integer':
        case 'int':
          return (int)$value;
        case 'float':
          return (float)$value;
        case 'boolean':
        case 'bool':
          return (bool)$value;
        case 'array':
        case 'object':
        case 'obj':
          return serialize($value);
      }
    }

    // default don't cast at all
    return $value;
  }
}
