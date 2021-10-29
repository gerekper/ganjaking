<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MePdfInvoiceNumber extends MeprBaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'invoice_number'  => 0,
        'transaction_id'  => 0,
        'created_at'  => null
      ),
      $obj
    );
  }

  public static function get_invoice_num($transaction_id){
    $db = MePdfDB::fetch();
    $invoice = $db->get_one_record($db->invoice_numbers, array('transaction_id' => $transaction_id));
    if($invoice){
      return $invoice->invoice_number;
    }
    return false;
  }

  public static function get_starting_number(){
    $mepr_options = MeprOptions::fetch();
    $starting_no = $mepr_options->attr( 'inv_starting_number' );
    return $starting_no;
  }

  public static function next_invoice_num() {
    $starting_num = MePdfInvoiceNumber::get_starting_number();
    $last_invoice_num = MePdfInvoiceNumber::get_last_invoice_num();
    $invoice_num = ($last_invoice_num == NULL || $last_invoice_num <= 0 || $last_invoice_num < $starting_num) ? absint($starting_num) : absint($last_invoice_num) + 1 ;
    return $invoice_num;
  }

  public static function get_last_invoice_num() {
    global $wpdb;
    $db = MePdfDB::fetch();
    $last_invoice_no = $wpdb->get_var(
      "SELECT `invoice_number` FROM {$db->invoice_numbers} ORDER BY `transaction_id` DESC LIMIT 1"
    );

    return $last_invoice_no;
  }

  public static function find_invoice_num($invoice_num){
    global $wpdb;
    $db = MePdfDB::fetch();
    $invoice_no = $wpdb->get_var(
      "SELECT `id` FROM {$db->invoice_numbers} WHERE `invoice_number`={$invoice_num} LIMIT 1"
    );

    return $invoice_no;
  }

  public static function find_invoice_num_by_txn_id($txn_id){
    global $wpdb;
    $db = MePdfDB::fetch();
    $invoice_no = $wpdb->get_var(
      "SELECT `invoice_number` FROM {$db->invoice_numbers} WHERE `transaction_id`={$txn_id} LIMIT 1"
    );

    return $invoice_no;
  }

  public static function get_last_transaction() {
    global $wpdb;

    $mepr_db = new MeprDb();
    $query = "SELECT id FROM {$mepr_db->transactions} ORDER BY id DESC LIMIT 0,1";
    return $wpdb->get_var($query);
  }

  public static function completed_refunded_transactions() {
    global $wpdb;
    $mepr_db = new MeprDb();

    $res = $wpdb->get_var("SELECT id FROM {$mepr_db->transactions} ORDER BY id DESC LIMIT 1");

    if(empty($res)) { $res = 0; }

    return $res;
  }

  public function store(){
    $db = MePdfDB::fetch();

    if(absint($this->invoice_number) <= 0 || absint($this->transaction_id) <= 0){
      return;
    }

    if(is_null($this->created_at) || empty($this->created_at)) {
      $this->created_at = MeprUtils::ts_to_mysql_date(time());
    }

    $args = (array)$this->get_values();
    return $db->create_record($db->invoice_numbers, $args, false);
  }

  public function destroy (){}

}
