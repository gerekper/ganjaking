<?php
if(!class_exists('WP_List_Table'))
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WafpSubscriptionsTable extends WP_List_Table {
  public function __construct() {
    parent::__construct( array( 'singular'=> 'wp_list_wafp_subscription', //Singular label
                                'plural' => 'wp_list_wafp_subscriptions', //plural label, also this well be one of the table css class
                                'ajax'  => false //We won't support Ajax for this table
                              ) );
  }

  public function extra_tablenav( $which ) {
    if( $which == "top" ) {
      require WAFP_VIEWS_PATH . "/shared/table-controls.php";
    }
    if( $which == "bottom" ) {
      //The code that goes after the table is there
    }
  }

  public function get_columns() {
    return $columns= array(
      'col_post_date' => __('Start Date', 'affiliate-royale', 'easy-affiliate'),
      'col_subscr_id'=> __('Subscription ID', 'affiliate-royale', 'easy-affiliate'),
      'col_affiliate'=> __('Affiliate', 'affiliate-royale', 'easy-affiliate'),
      'col_subscr_type'=> __('Subscription Type', 'affiliate-royale', 'easy-affiliate'),
      'col_del_sub'=> ''
    );
  }

  public function get_sortable_columns() {
    return $sortable = array(
      'col_post_date' => array('post_date',true),
      'col_subscr_id'=> array('subscr_id',true),
      'col_affiliate'=> array('affiliate',true),
      'col_subscr_type'=> array('subscr_type',true)
    );
  }

  public function prepare_items() {
    $screen = get_current_screen();

    $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'post_date';
    $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
    $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
    $perpage = !empty($_GET["perpage"]) ? esc_sql($_GET["perpage"]) : 10;
    $search = !empty($_GET["search"]) ? esc_sql($_GET["search"]) : '';

    $list_table = WafpSubscription::subscr_table( $orderby, $order, $paged, $search, $perpage );
    $totalitems = $list_table['count'];

    //How many pages do we have in total?
    $totalpages = ceil($totalitems/$perpage);

    /* -- Register the pagination -- */
    $this->set_pagination_args( array( "total_items" => $totalitems,
                                       "total_pages" => $totalpages,
                                       "per_page" => $perpage ) );

    /* -- Register the Columns -- */
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);

    /* -- Fetch the items -- */
    $this->items = $list_table['results'];
  }

  public function display_rows() {
    //Get the records registered in the prepare_items method
    $records = $this->items;

    //Get the columns registered in the get_columns and get_sortable_columns methods
    list( $columns, $hidden ) = $this->get_column_info();

    require WAFP_VIEWS_PATH . '/subscriptions/row.php';
  }
}
