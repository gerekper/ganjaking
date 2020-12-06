<?php
if(!class_exists('WP_List_Table'))
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WafpAffiliatesTable extends WP_List_Table {
  public function __construct() {
    parent::__construct( array( 'singular'=> 'wp_list_wafp_affiliate', //Singular label
                                'plural' => 'wp_list_wafp_affiliates', //plural label, also this well be one of the table css class
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
      'col_username' => __('Username', 'affiliate-royale', 'easy-affiliate'),
      'col_first_name' => __('First Name', 'affiliate-royale', 'easy-affiliate'),
      'col_last_name' => __('Last Name', 'affiliate-royale', 'easy-affiliate'),
      'col_ID' => __('ID', 'affiliate-royale', 'easy-affiliate'),
      'col_mtd_clicks' => __('MTD Clicks', 'affiliate-royale', 'easy-affiliate'),
      'col_ytd_clicks' => __('YTD Clicks', 'affiliate-royale', 'easy-affiliate'),
      'col_mtd_commissions' => __('MTD Commissions', 'affiliate-royale', 'easy-affiliate'),
      'col_ytd_commissions' => __('YTD Commissions', 'affiliate-royale', 'easy-affiliate'),
      'col_signup_date' => __('Signup Date', 'affiliate-royale', 'easy-affiliate'),
      'col_parent_name' => __('Referrer', 'affiliate-royale', 'easy-affiliate')
    );
  }

  public function get_sortable_columns() {
    return $sortable = array(
      'col_signup_date' => array('signup_date', true),
      'col_username' => array('username', true),
      'col_first_name' => array('first_name', true),
      'col_last_name' => array('last_name', true),
      'col_ID' => array('ID', true),
      'col_mtd_clicks' => array('mtd_clicks', true),
      'col_ytd_clicks' => array('ytd_clicks', true),
      'col_mtd_commissions' => array('mtd_commissions', true),
      'col_ytd_commissions' => array('ytd_commissions', true),
      'col_parent_name' => array('parent_name', true)
    );
  }

  public function prepare_items() {
    $screen = get_current_screen();

    $orderby = !empty($_GET["orderby"]) ? esc_sql($_GET["orderby"]) : 'signup_date';
    $order = !empty($_GET["order"]) ? esc_sql($_GET["order"]) : 'DESC';
    $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
    $perpage = !empty($_GET["perpage"]) ? esc_sql($_GET["perpage"]) : 10;
    $search = !empty($_GET["search"]) ? esc_sql($_GET["search"]) : '';

    $list_table = WafpUser::affiliate_datatable( false, $orderby, $order, $paged, $search, $perpage );
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

    require WAFP_VIEWS_PATH . '/affiliates/row.php';
  }
}
