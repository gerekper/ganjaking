<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpSubscription
{
  static public function register()
  {
    add_action( 'init', 'WafpSubscription::register_post_type', 0 );
  }

  static public function register_post_type()
  {
    register_post_type( 'wafp-subscriptions',
      array(
        'labels' => array(
          'name' => __( 'Subscriptions' , 'affiliate-royale', 'easy-affiliate'),
          'singular_name' => __( 'Subscription' , 'affiliate-royale', 'easy-affiliate'),
          'add_new_item' => __('Add New Subscription', 'affiliate-royale', 'easy-affiliate'),
          'edit_item' => __('Edit Subscription', 'affiliate-royale', 'easy-affiliate'),
          'new_item' => __('New Subscription', 'affiliate-royale', 'easy-affiliate'),
          'view_item' => __('View Subscription', 'affiliate-royale', 'easy-affiliate'),
          'search_items' => __('Search Subscription', 'affiliate-royale', 'easy-affiliate'),
          'not_found' => __('No Subscription found', 'affiliate-royale', 'easy-affiliate'),
          'not_found_in_trash' => __('No Subscription found in Trash', 'affiliate-royale', 'easy-affiliate'),
          'parent_item_colon' => __('Parent Subscription:', 'affiliate-royale', 'easy-affiliate')
        ),
        'public' => false,
        'show_ui' => false,
        'capability_type' => 'post',
        'hierarchical' => true,
        'supports' => array('none')
      )
    );
  }

  static public function create($subscr_id, $subscr_type="generic", $affiliate_id=0, $title="Subscription", $ip_addr="")
  {
    if( $subscr = self::subscription_exists($subscr_id) )
      return $subscr->subscription->ID;

    $post_id = wp_insert_post(array('post_title' => $title, 'post_type' => 'wafp-subscriptions', 'post_status' => 'publish', 'comment_status' => 'closed'));

    add_post_meta( $post_id, 'wafp_subscr_id',   $subscr_id );
    add_post_meta( $post_id, 'wafp_subscr_type', $subscr_type );
    add_post_meta( $post_id, 'wafp_ip_addr',     $ip_addr );

    if($affiliate_id)
      add_post_meta( $post_id, 'wafp_affiliate_id', $affiliate_id );

    return $post_id;
  }

  static public function get_one_by_subscr_id($subscr_id)
  {
    global $wpdb;

    $sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s and meta_value=%s";
    $sql = $wpdb->prepare($sql, 'wafp_subscr_id', $subscr_id);
    $post_id = $wpdb->get_var($sql);

    if($post_id)
      return new WafpSubscription($post_id);
    else
      return false;
  }

  static public function get_all()
  {
    global $wpdb;

    $sql = "SELECT Meta.meta_value FROM {$wpdb->posts} Post, {$wpdb->postmeta} Meta WHERE Post.ID = Meta.post_id AND Post.post_type = 'wafp-subscriptions' AND Meta.meta_key = 'wafp_subscr_id'";
    return $wpdb->get_col($sql);
  }

  static public function subscription_exists($subscr_id)
  {
    return self::get_one_by_subscr_id($subscr_id);
  }

  public static function subscr_table( $order_by='',
                                       $order='',
                                       $paged='',
                                       $search='',
                                       $perpage=10 ) {
    global $wafp_db, $wpdb, $wafp_options;

    $year = date('Y');
    $month = date('m');

    $cols = array(
      'post_date' => 'pst.post_date',
      'subscr_id' => 'pm_subscr_id.meta_value',
      'affiliate_id' => 'pm_affiliate_id.meta_value',
      'subscr_type' => 'pm_subscr_type.meta_value',
      'affiliate' => 'u.user_login'
    );

    $args = array( "pst.post_type='wafp-subscriptions'" );

    $joins = array(
      "LEFT OUTER JOIN {$wpdb->postmeta} AS pm_affiliate_id ON pm_affiliate_id.post_id=pst.ID AND pm_affiliate_id.meta_key='wafp_affiliate_id'",
      "LEFT OUTER JOIN {$wpdb->postmeta} AS pm_subscr_id ON pm_subscr_id.post_id=pst.ID AND pm_subscr_id.meta_key='wafp_subscr_id'",
      "LEFT OUTER JOIN {$wpdb->postmeta} AS pm_subscr_type ON pm_subscr_type.post_id=pst.ID AND pm_subscr_type.meta_key='wafp_subscr_type'",
      "LEFT OUTER JOIN {$wpdb->postmeta} AS pm_ip_addr ON pm_ip_addr.post_id=pst.ID AND pm_ip_addr.meta_key='wafp_ip_addr'",
      "LEFT OUTER JOIN {$wpdb->users} AS u ON u.ID=pm_affiliate_id.meta_value"
    );

    return WafpDb::list_table($cols, "{$wpdb->posts} AS pst", $joins, $args, $order_by, $order, $paged, $search, $perpage);
  }

  /** Instance Variables & Methods **/
  public $subscription;
  public $subscr_id;
  public $subscr_type;
  public $affiliate_id;
  public $ip_addr;

  public function __construct($id)
  {
    $this->subscription = get_post($id);
    $this->subscr_id    = get_post_meta( $id, 'wafp_subscr_id',    true );
    $this->subscr_type  = get_post_meta( $id, 'wafp_subscr_type',  true );
    $this->affiliate_id = get_post_meta( $id, 'wafp_affiliate_id', true );
    $this->ip_addr      = get_post_meta( $id, 'wafp_ip_addr',      true );
  }

  public function update()
  {
    $this->subscr_id    = get_post_meta( $this->subscription->ID, 'wafp_subscr_id',    $this->subscr_id );
    $this->subscr_type  = get_post_meta( $this->subscription->ID, 'wafp_subscr_type',  $this->subscr_type );
    $this->affiliate_id = get_post_meta( $this->subscription->ID, 'wafp_affiliate_id', $this->affiliate_id );
    $this->ip_addr      = get_post_meta( $this->subscription->ID, 'wafp_ip_addr',      $this->ip_addr );
  }

  //Deletes this Subscr and it's associated txns/commissions
  public function destroy()
  {
    $txns = WafpTransaction::get_all_by_subscription_id($this->subscription->ID);

    if(!empty($txns))
      foreach($txns as $txn)
        WafpTransaction::destroy($txn->id);

    wp_delete_post($this->subscription->ID, true);
  }
} //End class
