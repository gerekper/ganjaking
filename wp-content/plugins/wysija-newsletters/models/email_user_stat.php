<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_email_user_stat extends WYSIJA_model{

    var $pk=array('email_id','user_id');
    var $table_name='email_user_stat';
    var $columns=array(
        'email_id'=>array('req'=>true,'type'=>'integer'),
        'user_id'=>array('req'=>true,'type'=>'integer'),
        'sent_at' => array('req'=>true,'type'=>'integer'),
        'opened_at' => array('req'=>true,'type'=>'integer'),
        'status' => array('req'=>true,'type'=>'integer')
    );

    function __construct(){
        parent::__construct();
    }
}
