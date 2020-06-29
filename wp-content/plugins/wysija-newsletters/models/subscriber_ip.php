<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_subscriber_ip extends WYSIJA_model{

    var $pk=array("created_at","ip");
    var $table_name="subscriber_ips";
    var $columns=array(
        'ip' => array('req'=>true,'type'=>'ip'),
        'created_at' => array('auto'=>true,'type'=>'date'),
    );

    function __construct(){
        parent::__construct();
    }


}
