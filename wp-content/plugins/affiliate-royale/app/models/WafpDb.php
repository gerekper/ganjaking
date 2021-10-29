<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
class WafpDb
{
  public $links;
  public $clicks;
  public $transactions;
  public $payments;
  public $commissions;

  public function __construct()
  {
    global $wpdb;

    $this->links        = "{$wpdb->prefix}wafp_links";
    $this->clicks       = "{$wpdb->prefix}wafp_clicks";
    $this->transactions = "{$wpdb->prefix}wafp_transactions";
    $this->payments     = "{$wpdb->prefix}wafp_payments";
    $this->commissions  = "{$wpdb->prefix}wafp_commissions";
    $this->responses    = "{$wpdb->prefix}wafp_responses";
  }

  public function upgrade()
  {
    global $wpdb, $wafp_db_version;

    $old_db_version = get_option('wafp_db_version');

    if($wafp_db_version > $old_db_version)
    {
      $this->before_upgrade($old_db_version);

      // This was introduced in WordPress 3.5
      // $char_col = $wpdb->get_charset_collate(); //This doesn't work for most non english setups
      $char_col = "";
      $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

      if(isset($collation->Collation)) {
        $charset = explode('_', $collation->Collation);

        if(is_array($charset) && count($charset) > 1) {
          $charset = $charset[0]; //Get the charset from the collation
          $char_col = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
        }
      }

      //Fine we'll try it your way this time
      if(empty($char_col)) { $char_col = $wpdb->get_charset_collate(); }

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      /* Create/Upgrade Clicks Table */
      $sql = "CREATE TABLE {$this->clicks} (
                id int(11) NOT NULL auto_increment,
                ip varchar(255) default NULL,
                browser varchar(255) default NULL,
                referrer varchar(255) default NULL,
                uri varchar(255) default NULL,
                robot tinyint default 0,
                first_click tinyint default 0,
                created_at datetime NOT NULL,
                link_id int(11) default NULL,
                affiliate_id int(11) default NULL,
                PRIMARY KEY  (id),
                KEY link_id (link_id),
                KEY ip (ip),
                KEY browser (browser),
                KEY referrer (referrer),
                KEY uri (uri),
                KEY robot (robot),
                KEY first_click (first_click),
                KEY created_at (created_at),
                KEY affiliate_id (affiliate_id)
              ) {$char_col};";

      dbDelta($sql);

      /* Create/Upgrade Links Table */
      $sql = "CREATE TABLE {$this->links} (
                id int(11) NOT NULL auto_increment,
                target_url text NOT NULL,
                slug varchar(255) DEFAULT NULL,
                description varchar(255) NOT NULL,
                info text default NULL,
                image text default NULL,
                width int(11) default NULL,
                height int(11) default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY slug (slug),
                KEY width (width),
                KEY height (height)
              ) {$char_col};";

      dbDelta($sql);

      /* Create/Upgrade Transactions Table */
      $sql = "CREATE TABLE {$this->transactions} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                item_name varchar(255) DEFAULT NULL,
                sale_amount float(9,2) NOT NULL,
                commission_amount float(9,2) NOT NULL,
                refund_amount float(9,2) DEFAULT 0.00,
                correction_amount float(9,2) DEFAULT 0.00,
                commission_percentage float(9,2) DEFAULT 0.00,
                commission_type varchar(255) DEFAULT 'percentage',
                subscr_id varchar(255) DEFAULT NULL,
                subscr_paynum int(11) DEFAULT 0,
                ip_addr varchar(255) DEFAULT NULL,
                cust_email varchar(255) DEFAULT NULL,
                cust_name varchar(255) DEFAULT NULL,
                trans_num varchar(255) DEFAULT NULL,
                type varchar(255) DEFAULT NULL,
                status varchar(255) DEFAULT NULL,
                response text DEFAULT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY affiliate_id (affiliate_id),
                KEY item_name (item_name),
                KEY sale_amount (sale_amount),
                KEY commission_amount (commission_amount),
                KEY refund_amount (refund_amount),
                KEY correction_amount (correction_amount),
                KEY commission_percentage (commission_percentage),
                KEY commission_type (commission_type),
                KEY subscr_id (subscr_id),
                KEY subscr_paynum (subscr_paynum),
                KEY ip_addr (ip_addr),
                KEY cust_email (cust_email),
                KEY cust_name (cust_name),
                KEY trans_num (trans_num),
                KEY type (type),
                KEY status (status),
                KEY created_at (created_at)
              ) {$char_col};";

      dbDelta($sql);

      /* Create/Upgrade Payments Table */
      $sql = "CREATE TABLE {$this->payments} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                amount float(9,2) NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY created_at (created_at),
                KEY amount (amount),
                KEY affiliate_id (affiliate_id)
              ) {$char_col};";

      dbDelta($sql);

      /* Create/Upgrade Commissions Table */
      $sql = "CREATE TABLE {$this->commissions} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                transaction_id int(11) NOT NULL,
                commission_level int(11) DEFAULT 0,
                commission_percentage float(9,2) NOT NULL,
                commission_type varchar(255) DEFAULT 'percentage',
                commission_amount float(9,2) NOT NULL,
                correction_amount float(9,2) DEFAULT 0.00,
                payment_id int(11) DEFAULT 0,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY created_at (created_at),
                KEY transaction_id (transaction_id),
                KEY commission_level (commission_level),
                KEY commission_percentage (commission_percentage),
                KEY commission_type (commission_type),
                KEY commission_amount (commission_amount),
                KEY correction_amount (correction_amount),
                KEY payment_id (payment_id),
                KEY affiliate_id (affiliate_id)
              ) {$char_col};";

      dbDelta($sql);

      /* Create/Upgrade Gateway Responses Table */
      $sql = "CREATE TABLE {$this->responses} (
                id int(11) NOT NULL auto_increment,
                response text NOT NULL,
                type varchar(255) DEFAULT NULL,
                status varchar(255) DEFAULT 'pending',
                created_ts bigint NOT NULL,
                PRIMARY KEY  (id),
                KEY created_ts (created_ts),
                KEY status (status),
                KEY type (type)
              ) {$char_col};";

      dbDelta($sql);

      $this->after_upgrade($old_db_version);

      /***** SAVE DB VERSION *****/
      //Let's only run this query if we're actually updating
      update_option('wafp_db_version', $wafp_db_version);
    }
  }

  public function before_upgrade($curr_db_version)
  {
    // Nothing here yet
  }

  public function after_upgrade($curr_db_version)
  {
    global $wpdb, $wafp_db;

    if((int)$curr_db_version < 11)
    {
      $transactions = $wpdb->get_results("SELECT * FROM {$wafp_db->transactions}");

      foreach($transactions as $transaction)
      {
        if( !empty($transaction->affiliate_id) and
            $transaction->commission_amount > 0.00 )
        {
          $commission_id = WafpCommission::create( $transaction->affiliate_id, $transaction->id, 0, $transaction->commission_percentage, $transaction->commission_amount, $transaction->payment_id, $transaction->correction_amount );

          // Manually update the timestamp
          $query = $wpdb->prepare("UPDATE {$wafp_db->commissions} SET created_at=%s WHERE id=%d", $transaction->created_at, $commission_id);
          $wpdb->query($query);
        }
      }

      $wpdb->query("ALTER TABLE {$wafp_db->transactions} MODIFY commission_percentage float(9,2) DEFAULT 0.00");
      $wpdb->query("ALTER TABLE {$wafp_db->transactions} MODIFY subscr_id varchar(255) DEFAULT NULL");
    }

    if((int)$curr_db_version < 13)
    {
      $links = WafpLink::get_all_objects('image, id');
      foreach ($links as $link)
       {
          if (!empty($link->rec->image))
          {
             // Convert to new format
             WafpLink::update_image($link->rec->id, $link->image_url(), $link->rec->width, $link->rec->height);
          }
       }
    }

  }

  public function create_record($table, $args, $record_created_at=true, $output=false, $record_created_ts=false)
  {
    global $wpdb;

    $cols = array();
    $vars = array();
    $values = array();

    $i = 0;
    foreach($args as $key => $value)
    {
      $cols[$i] = $key;
      if(is_numeric($value) and preg_match('!\.!',$value))
        $vars[$i] = '%f';
      else if(is_int($value) or is_numeric($value) or is_bool($value))
        $vars[$i] = '%d';
      else
        $vars[$i] = '%s';

      if(is_bool($value))
        $values[$i] = $value ? 1 : 0;
      else
        $values[$i] = $value;
      $i++;
    }

    if($record_created_at)
    {
      $cols[$i] = 'created_at';
      $vars[$i] = 'NOW()';
      $i++;
    }

    if($record_created_ts)
    {
    $cols[$i] = 'created_ts';
      $vars[$i] = time();
    }

    if(empty($cols))
      return false;

    $cols_str = implode(',',$cols);
    $vars_str = implode(',',$vars);

    $query = "INSERT INTO {$table} ( {$cols_str} ) VALUES ( {$vars_str} )";
    if(empty($values)) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    if($output)
      echo $query . "\n";

    $query_results = $wpdb->query($query);

    if($query_results)
      return $wpdb->insert_id;
    else
      return false;
  }

  public function update_record( $table, $id, $args )
  {
    global $wpdb;

    if(empty($args) or empty($id))
      return false;

    $set = '';
    $values = array();
    foreach($args as $key => $value)
    {
      if(empty($set))
        $set .= ' SET';
      else
        $set .= ',';

      $set .= " {$key}=";

      if(is_numeric($value) and preg_match('!\.!',$value))
        $set .= "%f";
      else if(is_int($value) or is_numeric($value) or is_bool($value))
        $set .= "%d";
      else
        $set .= "%s";

      if(is_bool($value))
        $values[] = $value ? 1 : 0;
      else
        $values[] = $value;
    }

    $values[] = $id;
    $query = "UPDATE {$table}{$set} WHERE id=%d";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->query($query);
  }

  public function delete_records($table, $args)
  {
    global $wpdb;
    extract(WafpDb::get_where_clause_and_values( $args ));

    $query = "DELETE FROM {$table}{$where}";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->query($query);
  }

  public function get_count($table, $args=array())
  {
    global $wpdb;
    extract(WafpDb::get_where_clause_and_values( $args ));

    $query = "SELECT COUNT(*) FROM {$table}{$where}";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->get_var($query);
  }

  public function get_where_clause_and_values( $args )
  {
    $where = '';
    $values = array();
    foreach($args as $key => $value)
    {
      if(!empty($where))
        $where .= ' AND';
      else
        $where .= ' WHERE';

      $where .= " {$key}=";

      if(is_numeric($value) and preg_match('!\.!',$value))
        $where .= "%f";
      else if(is_int($value) or is_numeric($value) or is_bool($value))
        $where .= "%d";
      else
        $where .= "%s";

      if(is_bool($value))
        $values[] = $value ? 1 : 0;
      else
        $values[] = $value;
    }

    return compact('where','values');
  }

  public function get_one_record($table, $args=array())
  {
    global $wpdb;

    extract(WafpDb::get_where_clause_and_values( $args ));

    $query = "SELECT * FROM {$table}{$where} LIMIT 1";

    if( empty($values) ) {
      $query = esc_sql( $query );
    }
    else {
      $query = $wpdb->prepare( $query, $values );
    }

    return $wpdb->get_row($query);
  }

  public function get_records($table, $args=array(), $order_by='', $limit='', $joins=array())
  {
    global $wpdb;

    extract(WafpDb::get_where_clause_and_values( $args ));
    $join = '';

    if(!empty($order_by))
      $order_by = " ORDER BY {$order_by}";

    if(!empty($limit))
      $limit = " LIMIT {$limit}";

    if(!empty($joins)) {
      foreach($joins as $join_clause) {
        $join .= " {$join_clause}";
      }
    }

    $query = "SELECT * FROM {$table}{$join}{$where}{$order_by}{$limit}";

    if(empty($values)) {
      $query = esc_sql($query);
    }
    else {
      $query = $wpdb->prepare($query, $values);
    }

    return $wpdb->get_results($query);
  }

  /** Built to work with the datatables plugin for jQuery */
  public function datatable($cols, $from, $order_by='', $limit='', $joins=array(), $args=array())
  {
    global $wpdb;

    # defaults
    $col_str_array = array();
    foreach( $cols as $col => $code )
      $col_str_array[] = "{$code} AS {$col}";

    $col_names = array_keys($cols);

    $col_str = implode(", ",$col_str_array);

    if(!empty($order_by))
      $order_by = " ORDER BY {$order_by}";

    if(!empty($limit))
      $limit = " LIMIT {$limit}";

    if(!empty($joins))
      $join_str = " " . implode( " ", $joins );

    $args_str = implode(' AND ', $args);

    # Paging
    if( isset($_REQUEST['iDisplayStart']) and
        isset($_REQUEST['iDisplayLength']) and
        $_REQUEST['iDisplayLength'] != '-1' ) {
      $limit = " LIMIT {$_REQUEST['iDisplayStart']},{$_REQUEST['iDisplayLength']}";
    }

    # Ordering
    if(isset($_REQUEST['iSortCol_0'])) {
      $orders = array();
      for($i=0; $i < $_REQUEST['iSortingCols']; $i++) {
        if( $_REQUEST['bSortable_' . $_REQUEST['iSortCol_' . $i]] == "true" ) {
          $col = $col_names[ $_REQUEST['iSortCol_' . $i] ];
          $orders[] = "{$col} {$_REQUEST['sSortDir_' . $i]}";
        }
      }

      if(!empty($orders))
        $order_by = " ORDER BY " . implode(", ", $orders);
    }

    # Searching
    $search_str = "";
    $searches = array();
    if(isset($_REQUEST['sSearch']) and !empty($_REQUEST['sSearch'])) {
      foreach($cols as $col => $code) {
        $searches[] = "{$code} LIKE '%{$_REQUEST['sSearch']}%'";
      }

      if(!empty($searches))
        $search_str = implode(' OR ', $searches);
    }

    # Filtering
    $filter_str = "";
    $filters = array();
    $i=0;
    foreach($cols as $col => $code) {
      if( $_REQUEST['bSearchable_' . $i] == "true" and !empty($_REQUEST['sSearch_' . $i]) ) {
        $filters[] = "{$code} LIKE '%{$_REQUEST['sSearch_' . $i]}%'";
      }
      $i++;
    }

    if(!empty($filters))
      $filter_str = implode(' AND ', $filters );

    $conditions = "";

    # Pull Searching & Filtering into where

    if(!empty($args))
    {
      if(!empty($searches) and !empty($filters))
        $conditions = " WHERE $args_str AND ({$search_str}) AND {$filter_str}";
      elseif(!empty($searches))
        $conditions = " WHERE $args_str AND ({$search_str})";
      elseif(!empty($filters))
        $conditions = " WHERE $args_str AND {$filter_str}";
      else
        $conditions = " WHERE $args_str";
    }
    else {
      if(!empty($searches) and !empty($filters))
        $conditions = " WHERE ({$search_str}) AND {$filter_str}";
      elseif(!empty($searches))
        $conditions = " WHERE {$search_str}";
      elseif(!empty($filters))
        $conditions = " WHERE {$filter_str}";
    }

    $query = "SELECT {$col_str} FROM {$from}{$join_str}{$conditions}{$order_by}{$limit}";
    $total_query = "SELECT COUNT(*) FROM {$from}{$join_str}{$conditions}";
    $results = $wpdb->get_results($query, ARRAY_N);
    $total = $wpdb->get_var($total_query);

    // Datatables needs the aaData thing here to work
    $json = json_encode(array("sEcho" => $_REQUEST['sEcho'],
                              "iTotalRecords" => (int)$total,
                              "iTotalDisplayRecords" => (int)$total,
                              "aaData" => $results));

    return $json;
    //return $query;
  }

  /* Built to work with WordPress' built in WP_List_Table class */
  public static function list_table( $cols,
                              $from,
                              $joins=array(),
                              $args=array(),
                              $order_by='',
                              $order='',
                              $paged='',
                              $search='',
                              $perpage=10,
                              $countonly=false ) {
    global $wpdb;

    // Setup selects
    $col_str_array = array();
    foreach( $cols as $col => $code )
      $col_str_array[] = "{$code} AS {$col}";

    $col_str = implode(", ",$col_str_array);

    // Setup Joins
    if(!empty($joins))
      $join_str = " " . implode( " ", $joins );
    else
      $join_str = '';

    $args_str = implode(' AND ', $args);

    /* -- Ordering parameters -- */
    //Parameters that are going to be used to order the result
    $order_by = (!empty($order_by) and !empty($order)) ? ( $order_by = ' ORDER BY ' . $order_by . ' ' . $order ) : '';

    //Page Number
    if(empty($paged) or !is_numeric($paged) or $paged<=0 ){ $paged=1; }

    $limit = '';
    //adjust the query to take pagination into account
    if(!empty($paged) and !empty($perpage)) {
      $offset=($paged-1)*$perpage;
      $limit = ' LIMIT '.(int)$offset.','.(int)$perpage;
    }

    // Searching
    $search_str = "";
    $searches = array();
    if(!empty($search)) {
      foreach($cols as $col => $code)
        $searches[] = "{$code} LIKE '%{$search}%'";

      if(!empty($searches))
        $search_str = implode(' OR ', $searches);
    }

    $conditions = "";

    // Pull Searching into where
    if(!empty($args)) {
      if(!empty($searches))
        $conditions = " WHERE $args_str AND ({$search_str})";
      else
        $conditions = " WHERE $args_str";
    }
    else {
      if(!empty($searches))
        $conditions = " WHERE {$search_str}";
    }

    $query = "SELECT {$col_str} FROM {$from}{$join_str}{$conditions}{$order_by}{$limit}";
    $total_query = "SELECT COUNT(*) FROM {$from}{$join_str}{$conditions}";

    //Allows us to run the bazillion JOINS we use on the list tables
    $wpdb->query("SET SQL_BIG_SELECTS=1");

    $results = $wpdb->get_results($query);
    $count = $wpdb->get_var($total_query);

    return array( 'results' => $results, 'count' => $count );
  }
}
