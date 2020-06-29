<?php
defined('WYSIJA') or die('Restricted access');
include(dirname(dirname(__FILE__)).DS.'front.php');
class WYSIJA_control_back_subscribers extends WYSIJA_control_front{
    var $model='user';
    var $view='';

    function __construct(){
        parent::__construct();
        $data=array();
        foreach($_REQUEST['data'] as $vals){
            $data[esc_sql($vals['name'])]=esc_sql($vals['value']);
        }
        if(isset($data['message_success'])){
            $this->messages['insert'][true]=$data['message_success'];
        }else{
            $this->messages['insert'][true]=__('User has been inserted.',WYSIJA);
        }

        $this->messages['insert'][false]=__('User has not been inserted.',WYSIJA);
        $this->messages['update'][true]=__('User has been updated.',WYSIJA);
        $this->messages['update'][false]=__('User has not been updated.',WYSIJA);
    }

    function save(){
        $datarequested=array();
        $i=0;
        foreach($_REQUEST['data'] as $vals){
            if($vals['name']=='wysija[user_list][list_id][]'){
                $datarequested[str_replace('wysija[user_list][list_id][]', 'wysija[user_list][list_id]['.$i.']', esc_sql($vals['name']))]=esc_sql($vals['value']);
                $i++;
            }else   $datarequested[esc_sql($vals['name'])]=esc_sql($vals['value']);
        }

        $data=$this->convertUserData($datarequested);

        $helperUser=WYSIJA::get('user','helper');
        if(!$helperUser->checkData($data))return false;
        if(!$helperUser->verifyCaptcha($data))return false;
        if(!$helperUser->throttleRepeatedSubscriptions($data))return false;
        if($helperUser->addSubscriber($data)) {
          $helperUser->storeSubscriberIP();
        }

        return true;
    }

    // REFACTOR: Insanely complicated and inefficient
    function convertUserData($datarequested){
        $data=array();

        //get the lists
        if(isset($datarequested['wysija[user_list][list_ids]'])){
            $listids=explode(',',$datarequested['wysija[user_list][list_ids]']);
            $subdate=time();
            unset($datarequested['wysija[user_list][list_ids]']);
        }else{
            $i=0;
            $listids=array();
            for ($i = 0; $i <= 25; $i++) {
                $testkey='wysija[user_list][list_id]['.$i.']';
                if(isset($datarequested[$testkey])) $listids[]=$datarequested[$testkey];
                unset($datarequested[$testkey]);
            }
        }
        $data['user_list']['list_ids']=$listids;

        // define array for custom user fields
        $data['user_field'] = array();

        //get the user info and the rest of the data posted
        foreach($datarequested as $key => $val){
            if(strpos($key, 'wysija[user]')!== false) {
                $keymodified=str_replace(array('wysija[','][',']'),array('','#',''),$key);
                $keystabcol=explode('#',$keymodified);
                switch(count($keystabcol)){
                    case 2:
                        $data[$keystabcol[0]][$keystabcol[1]]=$val;
                    break;
                    case 3:
                        $data[$keystabcol[0]][$keystabcol[1]][$keystabcol[2]]=$val;
                    break;
                }
            } else if(strpos($key, 'wysija[field]')!== false){
                $keymodified=str_replace(array('wysija[','][',']'),array('','#',''),$key);
                $keystabcol=explode('#',$keymodified);

                switch(count($keystabcol)){
                    case 2:
                        $data['user_field'][$keystabcol[1]] = $val;
                    break;
                    case 3:
                        $data['user_field'][$keystabcol[1]][$keystabcol[2]] = $val;
                    break;
                }
            } else {
                if(!isset($data[$key])) $data[$key]=$val;
            }
        }

        return $data;
    }

    function registerToLists($data,$uid){
        $model=WYSIJA::get('user_list','model');
        if(isset($data['wysija[user_list][list_ids]'])){
            $listids=explode(',',$data['wysija[user_list][list_ids]']);

            $subdate=time();


        }else{
            $i=0;
            $listids=array();
            for ($i = 0; $i <= 25; $i++) {
                $testkey='wysija[user_list][list_id]['.$i.']';
                if(isset($data[$testkey])) $listids[]=$data[$testkey];
            }

            //$listids=$data['wysija[user_list][list_id]'];
        }
        foreach($listids as $listid){
            $model->replace(array('list_id'=>$listid,'user_id'=>$uid,'sub_date'=>$subdate));
            $model->reset();
        }

    }
}