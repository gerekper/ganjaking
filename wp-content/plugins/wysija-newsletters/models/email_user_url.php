<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_email_user_url extends WYSIJA_model{
    
    var $pk=array("email_id","user_id","url_id");
    var $table_name="email_user_url";
    var $columns=array(
        'email_id'=>array("req"=>true,"type"=>"integer"),
        'user_id'=>array("req"=>true,"type"=>"integer"),
        'url_id' => array("req"=>true,"type"=>"integer"),
        'clicked_at' => array("type"=>"integer"),
        'number_clicked' => array("type"=>"integer")
    );

    function __construct(){
        parent::__construct();
    }
    

}
