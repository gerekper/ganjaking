<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MePdfDB {
  private $tables;

  public static function fetch($force = false) {
    static $mpdf_db;

    if(!isset($mpdf_db) || $force) {
      $mpdf_db = new MePdfDB();
    }

    return $mpdf_db;
  }

  public function __construct() {
    global $wpdb;

    // MemberPress tables
    $this->tables = array(
      'invoice_numbers',
      'credit_notes'
    );
  }

  public function do_upgrade() {
    $old_db_version = get_option('mpdf_db_version', 0);
    return (version_compare(MPDFINVOICE_VERSION, $old_db_version, '>'));
  }

  public function upgrade_multisite() {
    global $wpdb;

    $bids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    // Must not name instance vars blog_id ... reserved by multisite apparently
    foreach($bids AS $bid) {
      switch_to_blog($bid);

      // Ensure MemberPress is active on the current blog?
      if(is_plugin_active(MPCA_PLUGIN_SLUG)) {
        $this->upgrade();
      }

      restore_current_blog();
    }
  }

  /** Will automatically run once when the plugin is upgraded */
  public function upgrade() {
    global $wpdb;

    // This line makes it safe to check this code during admin_init action.
    if($this->do_upgrade()) {
      $old_db_version = get_option('mpdf_db_version', 0);
      $this->before_upgrade($old_db_version);

      // This was introduced in WordPress 3.5
      $charset_collate = '';
      $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

      if(isset($collation->Collation)) {
        $charset = explode('_', $collation->Collation);

        if(is_array($charset) && count($charset) > 1) {
          $charset = $charset[0]; //Get the charset from the collation
          $charset_collate = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
        }
      }

      //Fine we'll try it your way this time
      if(empty($charset_collate)) { $charset_collate = $wpdb->get_charset_collate(); }

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      /* Create/Upgrade Corporate Accounts Table */
      // NOTE: We may want to modify the mpca_user_lookup index to be unique
      // at some point if we decide we need to enforce it at the databse level
      $invoice_numbers =
        "CREATE TABLE {$this->invoice_numbers} (
          id bigint(20) NOT NULL auto_increment,
          invoice_number bigint(20) NOT NULL,
          transaction_id bigint(20) NOT NULL,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY mpdf_invoice_number (invoice_number),
          KEY mpdf_transaction_id (transaction_id),
          KEY created_at (created_at)
        ) {$charset_collate};";

      dbDelta($invoice_numbers);

      $credit_notes =
        "CREATE TABLE {$this->credit_notes} (
          id bigint(20) NOT NULL auto_increment,
          invoice_number bigint(20) NOT NULL,
          created_at datetime NOT NULL,
          PRIMARY KEY  (id),
          KEY mpdf_invoice_number (invoice_number),
          KEY created_at (created_at)
        ) {$charset_collate};";

      dbDelta($credit_notes);

      $this->after_upgrade($old_db_version);

      // Update the version in the DB now that we've run the upgrade
      update_option('mpdf_db_version', MPDFINVOICE_VERSION);
    }
  }

  public function before_upgrade($curr_db_version) {
    //
  }

  public function after_upgrade($curr_db_version) {
    //
  }

  public function __get($name) {
    if(in_array($name,$this->tables)) {
      global $wpdb;
      return "{$wpdb->prefix}mpdf_{$name}";
    }
  }

  public function ts_to_db_date($ts, $format='Y-m-d H:i:s') {
    return gmdate($format, $ts);
  }

  public function now($format='Y-m-d H:i:s') {
    return $this->ts_to_db_date(time(), $format);
  }

  public static function lifetime() {
    return '0000-00-00 00:00:00';
  }

  public function create_record($table, $args, $record_created_at = true) {
    global $wpdb;

    $cols = array();
    $vars = array();
    $values = array();

    $i = 0;

    foreach($args as $key => $value) {
      $cols[$i] = "`$key`";
      if(is_numeric($value) and preg_match('!\.!',$value)) {
        $vars[$i] = '%f';
      }
      else if(is_int($value) or is_bool($value)) {
        $vars[$i] = '%d';
      }
      else {
        $vars[$i] = '%s';
      }

      if(is_bool($value)) {
        $values[$i] = $value ? 1 : 0;
      }
      else if(is_array($value) || is_object($value)) {
        $values[$i] = serialize($value);
      }
      else {
        $values[$i] = $value;
      }

      $i++;
    }

    if($record_created_at) {
      $cols[$i] = 'created_at';
      $vars[$i] = "'".date('c')."'";
    }

    if(empty($cols))
      return false;

    $cols_str = implode(',', $cols);
    $vars_str = implode(',', $vars);

    $query = "INSERT INTO {$table} ({$cols_str}) VALUES ({$vars_str})";
    $query = $wpdb->prepare($query, $values);

    $query_results = $wpdb->query($query);
    if($query_results) {
      return $wpdb->insert_id;
    }
    else {
      return false;
    }
  }


  public function get_one_record($table, $args = array(), $return_type=OBJECT) {
    global $wpdb;

    extract(MeprDb::get_where_clause_and_values($table,$args));
    $query = "SELECT * FROM {$table}{$where} LIMIT 1";

    if(!empty($values)) {
      $query = $wpdb->prepare($query, $values);
    }

    return $wpdb->get_row($query, $return_type);
  }

}
