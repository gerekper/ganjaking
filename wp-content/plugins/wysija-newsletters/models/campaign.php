<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_campaign extends WYSIJA_model{

    var $pk='campaign_id';
    var $table_name='campaign';
    var $columns=array(
        'campaign_id'=>array('type'=>'integer'),
        'name'=>array('req'=>true),
        'description' => array(),
    );
    var $escapeFields=array('name','description');
    var $escapingOn=true;

    function __construct(){
        parent::__construct();
    }

    function getDetails($email_id=false){
        if(!$email_id) $email_id=$_REQUEST['id'];

        $where_condition=array('email_id'=>$email_id);

        $data=array();
        $model_email=WYSIJA::get('email','model');
        $data['email']=$model_email->getOne(false,$where_condition);

        $data['campaign']=$this->getOne(false,array('campaign_id'=>$data['email']['campaign_id']));


        $model_campaign_list=WYSIJA::get('campaign_list','model');
        $data['campaign']['lists']['full']=$model_campaign_list->get(array('list_id','filter'),array('campaign_id'=>$data['email']['campaign_id']));

        foreach($data['campaign']['lists']['full'] as $list){
            $data['campaign']['lists']['ids'][]=$list['list_id'];
        }

        return $data;
    }
}
