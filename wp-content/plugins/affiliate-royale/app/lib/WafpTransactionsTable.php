<?php
if(!class_exists('WP_List_Table'))
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WafpTransactionsTable extends WP_List_Table {
  public function __construct() {
    parent::__construct( array( 'singular'=> 'wp_list_wafp_transaction', //Singular label
                                'plural' => 'wp_list_wafp_transactions', //plural label, also this well be one of the table css class
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
      'col_created_at' => __('Time', 'affiliate-royale', 'easy-affiliate'),
      'col_user_login' => __('Affiliate', 'affiliate-royale', 'easy-affiliate'),
      'col_trans_num' => __('Invoice', 'affiliate-royale', 'easy-affiliate'),
      'col_item_name' => __('Product', 'affiliate-royale', 'easy-affiliate'),
      //'col_sale_amount' => __('Amount', 'affiliate-royale'),
      //'col_refund_amount' => __('Refund', 'affiliate-royale'),
      'col_total_amount' => __('Total', 'affiliate-royale', 'easy-affiliate'),
      'col_commission_amount' => __('Commission', 'affiliate-royale', 'easy-affiliate'),
      'col_referring_page' => __('Referrer', 'affiliate-royale', 'easy-affiliate'),
      'col_actions' => __('Actions', 'affiliate-royale', 'easy-affiliate')
    );
  }

  public function get_sortable_columns() {
    return $sortable = array(
      'col_created_at' => array('created_at',true),
      'col_user_login' => array('user_login',true),
      'col_item_name' => array('item_name',true),
      'col_trans_num' => array('trans_num',true),
      'col_sale_amount' => array('sale_amount',true),
      'col_refund_amount' => array('refund_amount',true),
      'col_total_amount' => array('total_amount',true),
      'col_commission_amount' => array('commission_amount',true)
    );
  }

  public function prepare_items() {
    $screen = get_current_screen();

    $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'created_at';
    $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
    $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
    $perpage = !empty($_GET["perpage"]) ? esc_sql($_GET["perpage"]) : 10;
    $search = !empty($_GET["search"]) ? esc_sql($_GET["search"]) : '';

    $list_table = WafpTransaction::list_table( $orderby, $order, $paged, $search, $perpage );
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
    global $wafp_options;
    //Get the records registered in the prepare_items method
    $records = $this->items;

    //Get the columns registered in the get_columns and get_sortable_columns methods
    list( $columns, $hidden ) = $this->get_column_info();

    require WAFP_VIEWS_PATH . '/transactions/row.php';
  }
}
