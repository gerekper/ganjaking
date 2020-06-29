<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_user_history extends WYSIJA_model{

    var $pk='history_id';
    var $table_name='user_history';
    var $columns=array(
        'history_id'=>array('req'=>true,'type'=>'integer'),
        'email_id'=>array('req'=>true,'type'=>'integer'),
        'user_id'=>array('req'=>true,'type'=>'integer'),
        'executed_at' => array('type'=>'integer'),
        'executed_by' => array('type'=>'integer'),
        'type' => array(),
        'details' => array(),
        'source' => array()
    );



    function __construct(){
        parent::__construct();
    }

    /**
     * overriding the model insert function to be compatible with acymailing way of doing it
     * @param type $subid
     * @param type $action
     * @param string $data
     * @param type $mailid
     * @return type
     */
    function insert($subid,$action=false,$data = array(),$mailid = 0){
            $current_user=WYSIJA::wp_get_userdata();
            /*dbg($current_user,0);
            $current_user=wp_get_current_user();*/
            if(!empty($current_user->ID)){
                $data[] = 'EXECUTED_BY::'.$current_user->ID.' ( '.$current_user->user_login.' )';
            }
            $history = null;
            $history['user_id'] = intval($subid);
            $history['type'] = strip_tags($action);
            $history['details'] = implode("\n",$data);
            $history['executed_at'] = time();
            $history['email_id'] = $mailid;
            $userHelper = WYSIJA::get('user','helper');
            $history['executed_by'] = $userHelper->getIP();
            if(!empty($_SERVER)){
                    $source = array();
                    $vars = array('HTTP_REFERER','HTTP_USER_AGENT','HTTP_HOST','SERVER_ADDR','REMOTE_ADDR','REQUEST_URI','QUERY_STRING');
                    foreach($vars as $oneVar){
                            if(!empty($_SERVER[$oneVar])) $source[] = $oneVar.'::'.strip_tags($_SERVER[$oneVar]);
                    }
                    $history['source'] = implode("\n",$source);
            }
            return parent::insert($history);
    }
}
