<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpReportsHelper
{
  public static function periods_dropdown($field_name, $curr_period, $onchange='')
  {
    $field_value = (isset($_POST[$field_name])?$_POST[$field_name]:'');

    $periods = WafpReportsHelper::get_periods();

    rsort($periods);
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" onchange="<?php echo $onchange; ?>" class="wafp-dropdown wafp-periods-dropdown">
      <?php
        foreach($periods as $period)
        {
          $period_time = $period['time'];
          $period_label = $period['label'];
          ?>
          <option value="<?php echo $period_time; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_period) or (!isset($_POST[$field_name]) and $period_time == $curr_period))?' selected="selected"':''); ?>><?php echo $period_label; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }

  public static function get_periods()
  {
    $first_click = WafpClick::get_first_click();
    $first_timestamp = ($first_click?$first_click->created_at_ts:time());

    $period = array(date('Y', $first_timestamp),date('n', $first_timestamp));
    $timestamp = mktime(0,0,0,$period[1],1,$period[0]);

    $curr_timestamp = time();
    $curr_period = array(date('Y'),date('n'));

    $periods = array();

    while( $curr_timestamp >= $timestamp ) {
      $periods[] = array( 'time' => $timestamp, 'label' => date('F 01-t, Y', $timestamp));

      if($period[1]==12) {
        $period[1] = 1;
        $period[0]++;
      }
      else
        $period[1]++;

      $timestamp = mktime(0,0,0,$period[1],1,$period[0]);
    }

    return $periods;
  }

  public static function admin_affiliate_transactions($page=1, $page_size=25, $search='')
  {
    if( $page=='current' or empty($page) )
      $page=1;

    $transactions = WafpReport::affiliate_transactions( $page, $page_size, $search );
    $transaction_count = WafpTransaction::get_count($search);
    $num_pages   = $transaction_count / $page_size;

    $prev_page = false;
    $next_page = false;

    if($page > 1)
      $prev_page = $page - 1;

    if($page < $num_pages)
      $next_page = $page + 1;

    require( WAFP_VIEWS_PATH . "/reports/transactions.php" );
  }
}
