<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Db {
  private $tables;

  public static function fetch($force = false) {
    static $mpca_db;

    if(!isset($mpca_db) || $force) {
      $mpca_db = new MPCA_Db();
    }

    return $mpca_db;
  }

  public function __construct() {
    global $wpdb;

    // MemberPress tables
    $this->tables = array(
      'corporate_accounts'
    );
  }

  public function do_upgrade() {
    $old_db_version = get_option('mpca_db_version', 0);
    return (version_compare(MPCA_VERSION, $old_db_version, '>'));
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
      $old_db_version = get_option('mpca_db_version', 0);
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
      $corporate_accounts =
        "CREATE TABLE {$this->corporate_accounts} (
          id bigint(20) NOT NULL auto_increment,
          user_id bigint(20) NOT NULL,
          obj_id bigint(20) NOT NULL,
          obj_type varchar(25) NOT NULL,
          num_sub_accounts int(11) NOT NULL,
          status varchar(25) DEFAULT 'enabled',
          uuid varchar(60) NOT NULL,
          PRIMARY KEY  (id),
          KEY mpca_user_id (user_id),
          KEY mpca_obj_id (obj_id),
          KEY mpca_obj_type (obj_type),
          KEY mpca_num_sub_accounts (num_sub_accounts),
          KEY mpca_status (status),
          KEY mpca_user_lookup (user_id,obj_id,obj_type),
          KEY mpca_object_lookup (obj_id,obj_type),
          KEY mpca_uuid (uuid)
        ) {$charset_collate};";

      dbDelta($corporate_accounts);

      $this->after_upgrade($old_db_version);

      // Update the version in the DB now that we've run the upgrade
      update_option('mpca_db_version', MPCA_VERSION);
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
      return "{$wpdb->prefix}mepr_{$name}";
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
}
