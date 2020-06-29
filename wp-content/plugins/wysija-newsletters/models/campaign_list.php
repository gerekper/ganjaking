<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_campaign_list extends WYSIJA_model{

    var $pk=array("list_id","campaign_id");
    var $table_name="campaign_list";
    var $columns=array(
        'list_id'=>array("req"=>true,"type"=>"integer"),
        'campaign_id'=>array("req"=>true,"type"=>"integer"),
        'filter' => array("req"=>true),
    );

    function __construct(){
        parent::__construct();
    }

    function getReceivers($mailid,$total = true,$onlypublished = true){
            $query = 'SELECT a.name,a.description,a.published,a.color,b.listid,b.mailid FROM [wysija]campaign_list as b LEFT JOIN [wysija]list as a on a.list_id = b.list_id WHERE b.campaign_id = '.intval($mailid);
            if($onlypublished) $query .= ' AND a.published = 1';
            $lists=$this->query("get_res",$query,OBJECT_K);
            //$lists  = $this->database->loadObjectList('listid');
            if(empty($lists) OR !$total) return $lists;
            $config = WYSIJA::get('config','model');
            $confirmed = $config->getValue('confirm_dbleoptin') ? 'b.status = 1 AND' : '';
            $countQuery = 'SELECT a.listid, count(b.subid) as nbsub FROM `[wysija]user_list` as a LEFT JOIN `[wysija]user` as b ON a.user_id = b.user_id WHERE '.$confirmed.'  a.`list_id` IN ('.implode(',',array_keys($lists)).') GROUP BY a.`list_id`';
            //$this->database->setQuery($countQuery);
            //$countResult = $this->database->loadObjectList('listid');
            $countResult=$this->query('get_res',$countQuery,OBJECT_K);
            foreach($lists as $listid => $count){
                    $lists[$listid]->nbsub = empty($countResult[$listid]->nbsub) ? 0 : $countResult[$listid]->nbsub;
            }
            return $lists;
    }


}
