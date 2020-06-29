<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_url extends WYSIJA_model{
    
    var $pk="url_id";
    var $table_name="url";
    var $columns=array(
        'url_id'=>array("req"=>true,"type"=>"integer"),
        'name'=>array(),
        'url'=>array("req"=>true,"type"=>"url")
    );

    function __construct(){
        parent::__construct();
    }
    

}
