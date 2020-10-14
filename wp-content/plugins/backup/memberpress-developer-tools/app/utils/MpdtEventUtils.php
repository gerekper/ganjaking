<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtEventUtils extends MpdtBaseUtils {
  public $model_class = 'MeprEvent';

  public function __construct() {
    $this->map  = array(
    );

    parent::__construct();
  }

  protected function extend_obj(Array $evt) {
    $e = new MeprEvent($evt['id']);
    $obj = $e->get_data();

    $events = require(MPDT_DOCS_PATH . '/webhooks/events.php');
    $info = $events[$e->event];
    $data = $e->get_data();

    $utils = MpdtUtilsFactory::fetch($info->type);
    $evt['data'] = $utils->map_vars((array)$data->rec);
    $evt['data'] = $utils->trim_obj($evt['data']);

    return $evt;
  }

  protected function get_data_query(Array $args, $count=false) {
    global $wpdb;

    $mpdt_db = new MeprDb();
    $tablename = $mpdt_db->events;

    $rc = new ReflectionClass($this->model_class);

    $id_clause='';
    if(!empty($args['id'])) {
      $id_clause = $wpdb->prepare("
         WHERE e.id = %d
      ",
      $args['id']);
    }

    $search_clause='';
    if(!empty($args['search'])) {
      $where = (empty($id_clause) ? 'WHERE' : 'AND');
      $search_clause = $wpdb->prepare("
        {$where} ( e.event LIKE %s
                 OR e.args LIKE %s
                 OR e.evt_id_type LIKE %s )
      ",
      '%'.$args['search'].'%',
      '%'.$args['search'].'%',
      '%'.$args['search'].'%');
    }

    $limit_statement='';
    if(!$count && (int)$args['per_page'] !== -1) {
      $limit_statement = $wpdb->prepare("
        LIMIT %d OFFSET %d
      ",
      (int)$args['per_page'],
      (((int)$args['page']-1) * (int)$args['per_page']));
    }

    $args['order'] = strtolower($args['order']);

    $order_statement = ($count ? '' : "ORDER BY e.{$args['order']} {$args['order_dir']}");

    $select_vars = ($count ? 'COUNT(*)' : 'e.id');

    $q = "
      SELECT {$select_vars}
        FROM {$tablename} AS e
      {$id_clause}
      {$search_clause}
      {$order_statement}
      {$limit_statement}
    ";

    return $q;
  }
}

