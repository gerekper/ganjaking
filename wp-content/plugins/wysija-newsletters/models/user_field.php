<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_user_field extends WYSIJA_model{

    var $pk="field_id";
    var $table_name="user_field";
    var $columns=array(
        'field_id'=>array("req"=>true,"type"=>"integer"),
        'name'=>array("req"=>true),
        'column_name' => array("req"=>true),
        'type' => array("req"=>true,"type"=>"integer"),
        'values' => array("req"=>true),
        'default' => array("req"=>true),
        'is_required' => array("req"=>true,"type"=>"integer"),
        'error_message' => array("req"=>true)
    );

    function __construct(){
        $this->defaults=array('email'=>__('Email',WYSIJA),
            'firstname'=>__('First name',WYSIJA),
            'lastname'=>__('Last name',WYSIJA),
            'ip'=>__('IP address',WYSIJA),
            'status'=>__('Status',WYSIJA),
            'created_at'=>__('Subscription date',WYSIJA));
        parent::__construct();
    }

    function getFields(){
        $fields = array();

        // add custom fields
        $WJ_Field = new WJ_Field();
        $custom_fields = $WJ_Field->get_all();
        if(!empty($custom_fields)){
            foreach($custom_fields as $row){
                $fields['cf_'.$row->id] = $row->name;
            }
        }


        return array_merge($this->defaults,$fields);
    }
}
