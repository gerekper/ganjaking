<?php
namespace memberpress\downloads\models;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

use memberpress\downloads as base,
    memberpress\downloads\lib as lib,
    memberpress\downloads\models as models;

class FileStat extends lib\BaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'ID'             => array('default' => 0, 'type' => 'integer'),
        'file_id'        => array('default' => 0, 'type' => 'integer'),
        'user_id'        => array('default' => get_current_user_id(), 'type' => 'integer'),
        'ip_address'     => array('default' => ip2long(lib\Utils::get_ip_address()), 'type' => 'integer'), // use long2ip() to format to IP
        'created_at'     => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'),
        'updated_at'     => array('default' => lib\Utils::ts_to_mysql_date(time()), 'type' => 'datetime'),
      ),
      $obj
    );
  }

  /**
  * Used to validate the file download object
  * @return true|null ValidationException raised on failure
  */
  public function validate() {
    lib\Validate::not_empty($this->file_id, 'file_id');
    lib\Validate::not_empty($this->created_at, 'created_at');

    return true;
  }

  /**
  * Used to create the file download record
  * @param File $file
  * @return integer id
  */
  public static function create($file_id) {
    $file_stats = new self(array('file_id' => $file_id));
    $file_stats->store();
  }


  /**
  * Used to update the file download record
  * @return integer id
  */
  private function update() {
    $db = new lib\Db;
    $attrs = $this->get_values();
    $attrs = \array_merge($attrs, array('updated_at' => lib\Utils::ts_to_mysql_date(time())));

    return $db->update_record($db->file_stats, $this->id, $attrs);
  }

  /**
  * Destroy the file download stat
  * @return integer|false Returns number of rows affected or false
  */
  public function destroy() {
    $db = new lib\Db;

    return $db->delete_records($db->file_stats, array('id' => $this->id));
  }


  /**
  * Used to create or update the file download record
  * @param boolean $validate default true
  * @return integer id
  */
  public function store($validate = true) {
    if($validate) {
      $this->validate();
    }
    $db = new lib\Db;
    $attrs = apply_filters(base\SLUG_KEY.'_stats_attrs', $this->get_values()); ;
    return $db->create_record($db->file_stats, $attrs, false);
  }


  public static function list_table( $order_by = '',
                                     $order = '',
                                     $paged = '',
                                     $search = '',
                                     $search_field = 'any',
                                     $perpage = 10,
                                     $params = null,
                                     $include_fields = false ) {
    global $wpdb;
    $db = new lib\Db;

    if(is_null($params)) { $params=$_GET; }

    $mepr_options = \MeprOptions::fetch();

    if(empty($order_by)) {
      $order_by = 'created_at';
      $order = 'DESC';
    }

    $cols = array(
      'ID'          => 'fs.ID',
      'user_id'     => 'fs.user_id',
      'user_email'  => 'u.user_email',
      'file_id'     => 'fs.file_id',
      'file_name'   => 'p.post_title',
      'ip_address'  => 'IFNULL(INET_NTOA(fs.ip_address),NULL)',
      'created_at'  => 'IFNULL(fs.created_at,NULL)',
    );

    $args = array();

    if(isset($params['file_name']) && !empty($params['file_name']) && $params['file_name'] != 'all') {
      $args[] = $wpdb->prepare('p.post_title LIKE "%%%s%%"', $params['file_name']);
    }

    if( isset($params['start_date'], $params['end_date'])
        && !in_array('all', array($params['start_date'], $params['end_date']))
        && !empty($params['start_date'])
        && !empty($params['start_date'])){
          $start_date = date('Y-m-d H:i:s', strtotime($params['start_date']));
          $end_date = date('Y-m-d H:i:s', strtotime($params['end_date']));
          $args[] = $wpdb->prepare('fs.created_at between %s AND %s', $start_date, $end_date);
    }
    else{
      if(isset($params['start_date']) && !empty($params['start_date']) && $params['start_date'] != 'all') {
        $start_date = date('Y-m-d H:i:s', strtotime($params['start_date']));
        $args[] = $wpdb->prepare('fs.created_at > %s', $start_date);
      }

      if(isset($params['end_date']) && !empty($params['end_date']) && $params['end_date'] != 'all') {
        $end_date = date('Y-m-d H:i:s', strtotime($params['end_date']));
        $args[] = $wpdb->prepare('fs.created_at < %s', $end_date);
      }
    }

    $joins = array(
      "/* IMPORTANT */ LEFT JOIN {$wpdb->prefix}users AS u ON fs.user_id=u.ID",
      "/* IMPORTANT */ LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID=fs.file_id",
    );

    return \MeprDb::list_table($cols, "{$db->file_stats} AS fs", $joins, $args, $order_by, $order, $paged, $search, $search_field, $perpage); //, false, true);
  }

  public static function download_count($file){

    // Get existing download count
    $download_count = $file->download_count > 0 ? $file->download_count : 0;

    // Get stats download count
    $stats_count = absint( self::get_count(array('file_id' => $file->ID)) );

    // Return addition of the two
    return $download_count + $stats_count;
  }
}
