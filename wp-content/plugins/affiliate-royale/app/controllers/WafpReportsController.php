<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpReportsController
{
  public static function overview() {
    $title = __('Reports', 'affiliate-royale', 'easy-affiliate');
    require( WAFP_VIEWS_PATH . "/reports/overview.php" );
  }

  public static function admin_affiliate_stats($period='current') {
    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    $stats = WafpReport::affiliate_stats( $period );
    require( WAFP_VIEWS_PATH . "/reports/stats.php" );
  }

  public static function admin_affiliate_clicks() {
    $list_table = new WafpClicksTable();
    $list_table->prepare_items();

    require WAFP_VIEWS_PATH . '/clicks/list.php';
  }

  public static function admin_affiliate_top($period='current', $page=1, $page_size=25 ) {
    if( $period=='current' or empty($period) )
      $period = mktime(0, 0, 0, date('n'), 1, date('Y'));

    if( !isset($page) or empty($page) )
      $page=1;

    $top_affiliates = WafpReport::top_referring_affiliates( $period, $page, $page_size );
    $aff_count = WafpReport::get_user_count();
    $num_pages = $aff_count / $page_size;

    $prev_page = false;
    $next_page = false;

    if($page > 1)
      $prev_page = $page - 1;

    if($page < $num_pages)
      $next_page = $page + 1;

    require( WAFP_VIEWS_PATH . "/reports/top.php" );
  }
}
