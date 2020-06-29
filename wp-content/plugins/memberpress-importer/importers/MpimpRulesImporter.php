<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpimpRulesImporter extends MpimpBaseImporter {
  private $legacy_import = false;
  public function form() { }

  public function import($row,$args) {
    $required = array('type','target');
    if(array_key_exists('product_id_0', $row)) {
      $this->fail_if_empty('product_id_0', $row['product_id_0']);
      $required = array_merge($required, array('product_id_0'));
      $this->legacy_import = true;
      $mepr_access = array();
    }
    $this->check_required('rules', array_keys($row), $required);

    // Merge in default values where applicable
    $row = array_merge( array( 'regexp' => 0,
                               'drip' => 0,
                               'drip_duration' => 0,
                               'drip_type' => 'days',
                               'drip_trigger' => 'registers',
                               'expire' => 0,
                               'expire_duration' => 0,
                               'expire_type' => 'days',
                               'expire_trigger' => 'registers' ), $row );

    $rule = new MeprRule();
    $rule->post_status='publish';

    $valid_types = array_keys(MeprRule::get_types());
    $valid_drip_expire_types = array('days','weeks','months','years');

    foreach( $row as $col => $cell ) {
      if( $this->legacy_import && preg_match( '#^product_id_.*$#', $col ) ) {
        if(!empty($cell)) {
          $this->fail_if_not_valid_product_id($cell);
          $mepr_access[] = $cell;
        }
      }
      else {
        switch( $col ) {
          case "type":
            $this->fail_if_empty($col, $cell);
            $this->fail_if_not_in_enum($col,$cell,$valid_types);
            $rule->mepr_type = $cell;
            break;
          case "target":
            $this->fail_if_empty($col, $cell);
            $rule->mepr_content = $cell;
            // TODO: Validate each rule type?
            break;
          case "regexp":
            $cell = empty($cell)?0:$cell;
            $this->fail_if_not_bool($col,$cell);
            $rule->is_mepr_content_regexp = ((int)$cell==1);
            break;
          case "drip":
          case "expire":
            $cell = empty($cell)?0:$cell;
            $this->fail_if_not_bool($col,$cell);
            $varname = "{$col}_enabled";
            $rule->{$varname} = $cell;
            break;
          case "drip_type":
          case "expire_type":
            $cell = empty($cell)?'days':$cell;
            $this->fail_if_not_in_enum($col,$cell,$valid_drip_expire_types);
            $varname = preg_replace(array('#^expire#','#_type$#'),array('expires','_unit'),$col);
            $rule->{$varname} = $cell;
            break;
          case "drip_duration":
          case "expire_duration":
            $cell = empty($cell)?0:$cell;
            $this->fail_if_not_number($col,$cell);
            $varname = preg_replace(array('#^expire#','#_duration$#'),array('expires','_amount'),$col);
            $rule->{$varname} = $cell;
            break;
          case "drip_trigger":
          case "expire_trigger":
            $cell = empty($cell)?'registers':$cell;
            if( is_numeric($cell) )
              $this->fail_if_not_valid_product_id($cell);
            else {
              if($cell != 'registers' && $cell != 'fixed' && $cell != 'rule-products')
                throw new Exception( sprintf( __('%1$s must be the word "registers", "fixed", "rule-products" or a valid product_id'), $col ) );
            }

            $varname = preg_replace(array('#^expire#','#_trigger$#'),array('expires','_after'),$col);
            $rule->{$varname} = $cell;
            break;
          case "drip_after_date":
          case "expire_after_date":
            $varname = preg_replace(array('#^expire#','#_date$#'),array('expires','_fixed'), $col);
            $rule->{$varname} = $cell;
            break;
        }
      }
    }

    $mepr_types = MeprRule::get_types();
    $rule->post_title = $mepr_types[$rule->mepr_type] . ": " . ucwords( $rule->mepr_content );
    if($this->legacy_import && !empty($mepr_access))
      $rule->mepr_access = $mepr_access;

    if( $rule_id = $rule->store() ) {
      $success_msg = sprintf(__('Rule (ID = %d) was created successfully.'), $rule_id);
      if(!$this->legacy_import)
        $success_msg .= __(" Don't forget to import or create AccessConditions");
      return $success_msg;
    }
    else
      throw new Exception(__('Rule failed to be created'));
  }
}
