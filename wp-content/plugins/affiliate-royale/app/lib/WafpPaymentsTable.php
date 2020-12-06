<?php
if(!class_exists('WP_List_Table'))
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WafpPaymentsTable extends WP_List_Table {
  public function __construct() {
    parent::__construct( array( 'singular'=> 'wp_list_wafp_payment', //Singular label
                                'plural' => 'wp_list_wafp_payments', //plural label, also this well be one of the table css class
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
      'col_amount'=> __('Amount', 'affiliate-royale', 'easy-affiliate'),
      'col_affiliate'=> __('Affiliate', 'affiliate-royale', 'easy-affiliate')
    );
  }

  public function get_sortable_columns() {
    return $sortable = array(
      'col_created_at' => array('created_at',true),
      'col_amount'=> array('amount',true),
      'col_affiliate'=> array('affiliate',true)
    );
  }

  public function prepare_items() {
    $screen = get_current_screen();

    $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'created_at';
    $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
    $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
    $perpage = !empty($_GET["perpage"]) ? esc_sql($_GET["perpage"]) : 10;
    $search = !empty($_GET["search"]) ? esc_sql($_GET["search"]) : '';

    $list_table = WafpPayment::list_table( $orderby, $order, $paged, $search, $perpage );
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

    require WAFP_VIEWS_PATH . '/payments/row.php';
  }
}
