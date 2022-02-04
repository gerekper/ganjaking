<?php

namespace memberpress\courses\lib;

use memberpress\courses as base;
use memberpress\courses\models as models;

class AttemptsTable extends \WP_List_Table {
  public function __construct() {
    parent::__construct([
      'singular' => 'wp_list_mpcs_quiz_attempt',
      'plural' => 'wp_list_mpcs_quiz_attempts',
      'ajax' => false,
    ]);
  }

  public function get_columns() {
    return [
      'cb' => '<input type="checkbox">',
      'col_name' => esc_html__('Name', 'memberpress-courses'),
      'col_score' => esc_html__('Score', 'memberpress-courses'),
      'col_finished_at' => esc_html__('Finished', 'memberpress-courses')
    ];
  }

  public function get_sortable_columns() {
    return [
      'col_name' => ['name', true],
      'col_score' => ['score', true],
      'col_finished_at' => ['finished_at', true],
    ];
  }

  protected function get_bulk_actions() {
    return [
      'delete' => __('Delete permanently', 'memberpress-courses')
    ];
  }

  public function prepare_items() {
    $valid_orderby = ['name', 'score', 'finished_at'];
    $orderby = isset($_GET['orderby']) && is_string($_GET['orderby']) && in_array($_GET['orderby'], $valid_orderby) ? $_GET['orderby'] : 'finished_at';
    $order = isset($_GET['order']) && is_string($_GET['order']) && strtoupper($_GET['order']) == 'DESC' ? 'DESC' : 'ASC';
    $paged = isset($_GET['paged']) && is_numeric($_GET['paged']) ? max((int) $_GET['paged'], 1) : 1;
    $search = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $perpage = Utils::get_per_page_screen_option('mpcs_attempts_per_page');
    $quiz_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $list_table = models\Attempt::list_table($orderby, $order, $paged, $search, $perpage, $quiz_id);

    $totalitems = $list_table['count'];

    // How many pages do we have in total?
    $totalpages = ceil($totalitems / $perpage);

    // Register the pagination
    $this->set_pagination_args([
      'total_items' => $totalitems,
      'total_pages' => $totalpages,
      'per_page' => $perpage
    ]);

    // Register the columns
    $columns = $this->get_columns();
    $hidden = [];
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = [$columns, $hidden, $sortable];

    // Fetch the items
    $this->items = $list_table['results'];
  }

  public function display_rows() {
    $rows = $this->items;
    list($columns, $hidden, , $primary) = $this->get_column_info();

    require base\VIEWS_PATH . '/admin/attempts/rows.php';
  }
}
