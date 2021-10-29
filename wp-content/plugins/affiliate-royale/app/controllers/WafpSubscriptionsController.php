<?php
class WafpSubscriptionsController extends WP_List_Table {
  public static function route() {
    self::display_list();
  }

  public static function display_list() {
    $sub_table = new WafpSubscriptionsTable();
    $sub_table->prepare_items();

    require WAFP_VIEWS_PATH . '/subscriptions/list.php';
  }

  public static function delete_subscription()
  {
    if(!is_super_admin())
      die(__('You do not have access.', 'affiliate-royale', 'easy-affiliate'));

    if(!isset($_POST['subscr_id']) || empty($_POST['subscr_id']))
      die(__('Could not delete subscription', 'affiliate-royale', 'easy-affiliate'));

    $subscription = WafpSubscription::get_one_by_subscr_id($_POST['subscr_id']);

    if($subscription)
    {
      $subscription->destroy();
      die('true'); //don't localize this string
    }

    die(__('This Subscription cannot be deleted.', 'affiliate-royale', 'easy-affiliate'));
  }
} //End class
