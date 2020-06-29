<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_url_mail extends WYSIJA_model{

    var $pk=array('url_id','email_id');
    var $table_name='url_mail';
    var $columns=array(
        'url_id'=>array('req'=>true,'type'=>'integer'),
        'email_id'=>array('req'=>true,'type'=>'integer'),
        'unique_clicked'=>array('type'=>'integer'),
        'total_clicked'=>array('type'=>'integer')
    );

    function __construct(){
        parent::__construct();
    }

}
