<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_user_list extends WYSIJA_model{

    var $pk=array("list_id","user_id");
    var $table_name="user_list";
    var $columns=array(
        'list_id'=>array("req"=>true,"type"=>"integer"),
        'user_id'=>array("req"=>true,"type"=>"integer"),
        'sub_date' => array("type"=>"integer"),
        'unsub_date' => array("type"=>"integer")
    );

    function __construct(){
        parent::__construct();
    }

    function hook_subscriber_to_list( $details ) {

        $config=WYSIJA::get('config','model');
        $modelUser=WYSIJA::get('user','model');
        $userdata=$modelUser->getOne(false,array('user_id'=>$details['user_id']));
        $confirmed=true;

        /* do not send email if user is not confirmed*/
        /*if($config->getValue('confirm_dbleoptin') && (int)$userdata['status']!=1)   $confirmed=false;

        if($confirmed){
            $helperU=WYSIJA::get('user','helper');
            $helperU->sendAutoNl($details['user_id'],array(0=>$details));
        }*/
        $dbloptin=$config->getValue('confirm_dbleoptin');
        /*only if dbleoptin has been deactivated we send immediately the post notification*/

        if(!$dbloptin || ($dbloptin && (int)$userdata['status']>0)){
            /*check for auto nl and send if needed*/
            $helperU=WYSIJA::get('user','helper');
            if(isset($this->backSave) && $this->backSave){
                $helperU->sendAutoNl($details['user_id'],array(0=>$details),'subs-2-nl',true);
            }else{
                $helperU->sendAutoNl($details['user_id'],array(0=>$details));
            }
        }

        return true;
    }

    function afterInsert($resultSaveID) {
        if(!isset($this->nohook)){
            add_action('wysija_subscribed_to', array($this, 'hook_subscriber_to_list'), 1);
        }

        do_action('wysija_subscribed_to',$this->values);
    }

	/**
	 * Get public lists of users
	 * @param array $user_ids list of user ids
	 * @return array
	 * <pre>
	 * array(
	 *	int => array(int, int, ..., int),// user_id => array(list_id, list_id, ..., list_id)
	 *	int => array(),
	 *	...
	 *	int => array()
	 * )
	 */
	public function get_lists(Array $user_ids = array()) {
		$user_lists = array();
		if (!empty($user_ids)) {
			$query = '
				SELECT
					`user_id`,
					GROUP_CONCAT(ul.`list_id`) AS `lists`
				FROM
					`[wysija]user_list` ul
				JOIN
					`[wysija]list` l
				ON
					l.`list_id` = ul.`list_id`
					AND ul.`user_id` IN ('.implode(', ', $user_ids).')
				WHERE
					l.`is_enabled` = 1
				GROUP BY
					`user_id`';
			$result = $this->get_results($query);
			if (!empty($result)) {
				foreach ($result as $record) {
					$user_lists[$record['user_id']] = explode(',', $record['lists']);
				}
			}
		}
		return $user_lists;
	}

}
