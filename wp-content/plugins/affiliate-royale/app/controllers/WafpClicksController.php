<?php
class WafpClicksController extends WP_List_Table {
  public static function route() {
    self::display_list();
  }

  public static function display_list() {
    $list_table = new WafpClicksTable();
    $list_table->prepare_items();

    require WAFP_VIEWS_PATH . '/clicks/list.php';
  }
}
