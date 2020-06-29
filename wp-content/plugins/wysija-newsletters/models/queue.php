<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_queue extends WYSIJA_model{

    var $pk=array('email_id','user_id');
    var $table_name='queue';
    var $columns=array(
        'email_id'=>array('type'=>'integer'),
        'user_id'=>array('type'=>'integer'),
        'send_at' => array('req'=>true,'type'=>'integer'),
        'priority' => array('type'=>'integer'),
        'number_try' => array('type'=>'integer')
    );

    function __construct(){
        parent::__construct();
    }

    /**
     * used to put emails in the queue when starting to send or adding user to a follow_up email
     * @param array $email
     * @return boolean
     */
    function queue_email($email){
        // make sure that the email array has at least those 3 important parameters set
        if(!isset($email['email_id']) || !isset($email['type']) || !isset($email['params'])) {
            $this->error('Missing data to queue email');
            return false;
        }

        $follow_up = $emails_need_to_be_queued = false;
        // we cannot queue all kinds of emails make sure this email can be queued
        if((int)$email['type'] === 2){
            // if we are in a subscriber follow-up case then we can queue emails of the list
            // only if it's the first time that we hit the send button
            if(isset($email['params']) && $email['params']['autonl']['event']=='subs-2-nl' && (int)$email['sent_at']===0){
                $emails_need_to_be_queued=true;
                $follow_up=true;
            }

        }else{
            // queue emails only if it is not a scheduled email
            if(!isset($email['params']['schedule']['isscheduled'])){
                $emails_need_to_be_queued = true;
            }else{
                $helper_toolbox = WYSIJA::get('toolbox','helper');
                $schedule_date = $email['params']['schedule']['day'].' '.$email['params']['schedule']['time'];
                $unix_scheduled_time = strtotime($schedule_date);

                // if the scheduled time is passed let's send the email
                // we don't compare to the time recorded but to the offset time which is the time set by the user in his time
                if($helper_toolbox->localtime_to_servertime($unix_scheduled_time) < time()){
                    $emails_need_to_be_queued = true;
                }
            }
        }

        // we're not supposed to queue any email here probably because it is an automatic newsletter or a scheduled email
        if(!$emails_need_to_be_queued) return false;


        // if it is a standard email we get the campaign list
        if(!$follow_up){
            $model_campaign = WYSIJA::get('campaign','model');
            $data = $model_campaign->getDetails($email['email_id']);

            $lists_to_send_to = $data['campaign']['lists']['ids'];
            $to_be_sent=time();
        }else{
            // if it is a follow up we get the campaign list
            $lists_to_send_to = array($email['params']['autonl']['subscribetolist']);
            $delay = $this->calculate_delay($email['params']['autonl']);
            $to_be_sent = '(A.sub_date) + '.$delay;
        }

        // get the minimum status to queue emails based on the double optin config
        $model_config=WYSIJA::get('config','model');
        if($model_config->getValue('confirm_dbleoptin')) $status_min=0;
        else $status_min=-1;

        if(empty($lists_to_send_to)){
            $this->error(__('There are no list to send to.',WYSIJA),1);
            return false;
        }
        // insert into the queue
        $query='INSERT IGNORE INTO [wysija]queue (`email_id` ,`user_id`,`send_at`) ';
        $query.='SELECT '.$email['email_id'].', A.user_id,'.$to_be_sent.'
            FROM [wysija]user_list as A
            JOIN [wysija]user as B on A.user_id=B.user_id
            WHERE B.status>'.$status_min.'
                AND A.list_id IN ('.implode(',',$lists_to_send_to).')
                AND A.unsub_date=0';

        // if some emails have already been sent on that newsletter, make sure we don't re enqueue the same emails again
        $query_count = 'SELECT count(user_id) as count FROM [wysija]email_user_stat WHERE email_id = '.$email['email_id'];
        if( $this->count( $query_count) > 0){
            $query .= ' AND A.user_id NOT IN (SELECT user_id FROM [wysija]email_user_stat WHERE email_id = '.$email['email_id'].')';
        }

        $this->query($query);

        if($this->sql_error){

            $this->error($this->sql_error);
            $this->error('Full query : '.$query);
            return false;
        }else{
            // rows were inserted
            $nb_emails=$this->getAffectedRows();
            if((int)$nb_emails  > 0){
                //$this->notice(sprintf(__('%1$s email(s) queued', WYSIJA),$nb_emails),false);
                return true;
            }
        }
        return true;
    }


    /**
     * get a list of the delaied queued emails
     * @param type $mailid
     * @return type
     */
    function getDelayed($mailid=0){
        if(!$mailid) return array();
        $query = 'SELECT c.*,a.* FROM [wysija]queue as a';
        $query .= ' JOIN [wysija]email as b on a.`email_id` = b.`email_id` ';
        $query .= ' JOIN [wysija]user as c on a.`user_id` = c.`user_id` ';
        $query .= ' WHERE  b.`status` IN (1,3,99)';
        if(!empty($mailid)) $query .= ' AND a.`email_id` = '.$mailid;
        $query .= ' ORDER BY a.`priority` ASC, a.`send_at` ASC, a.`user_id` ASC';

        $results=$this->query('get_res',$query);


        return $results;
    }

    /**
     * get a list of the emails ready to be sent
     * @param string $sql_limit
     * @param int $email_id
     * @param int $user_id
     * @return type
     */
    function getReady($sql_limit,$email_id = 0,$user_id=false){
        // in some cases of a large database, with millions of entries recorded in the queue table, the triple joins was
        // very slow and crashing the request.
        $model_config = WYSIJA::get('config', 'model');
        if ((int) $model_config->getValue('total_subscribers') > 1000000){
            return $this->get_ready_large_db($sql_limit,$email_id = 0,$user_id=false);
        }

        $query = 'SELECT c.*,a.* FROM [wysija]queue as a';
        $query .= ' JOIN [wysija]email as b on a.`email_id` = b.`email_id` ';
        $query .= ' JOIN [wysija]user as c on a.`user_id` = c.`user_id` ';
        $query .= ' WHERE a.`send_at` <= '.time().' AND b.`status` IN (1,3,99)';
        if(!empty($email_id)) $query .= ' AND a.`email_id` = '.$email_id;
        if($user_id) $query .= ' AND a.`user_id` = '.$user_id;
        $query .= ' ORDER BY a.`priority` ASC, a.`send_at` ASC, a.`user_id` ASC';
        if(!empty($sql_limit)) $query .= ' LIMIT '.$sql_limit;

        $results=$this->query('get_res',$query,OBJECT_K);
        if($results === null){
            $this->query('REPAIR TABLE [wysija]queue, [wysija]user, [wysija]email');
        }

        if(!empty($results)){
                $first_element_queued = reset($results);
                $this->query('UPDATE [wysija]queue SET send_at = send_at + 1 WHERE email_id = '.$first_element_queued->email_id.' AND user_id = '.$first_element_queued->user_id.' LIMIT 1');
        }
        return $results;
    }

    /**
     * this function is here to make sure that if the subscribers has a large db, and the queue has millions of entry, the
     * selection request will still work
     * @param type $sql_limit
     * @param type $email_id
     * @param type $user_id
     * @return type
     */
    function get_ready_large_db($sql_limit,$email_id = 0,$user_id=false){

        $query = 'SELECT A.user_id, A.email_id
            FROM [wysija]queue AS A
            JOIN [wysija]email AS B ON A.`email_id` = B.`email_id`
            WHERE A.`send_at` <= '.time().' AND B.status IN ( 1, 3, 99 ) ';
        if($user_id) $query .= ' AND A.`user_id` = '.$user_id;
        if(!empty($email_id)) $query .= ' AND A.`email_id` = '.$email_id;
        $query .= ' ORDER BY A.`priority` ASC , A.`send_at` ASC , A.`user_id` ASC';

        if(!empty($sql_limit)) $query .= ' LIMIT '.$sql_limit;

        // the first request is looking for data between the queue table and the email table
        $results = $this->query('get_res',$query,OBJECT_K);
        if(empty($results)) return $result;
        $user_ids = $email_ids = '';
        $email_ids = array();

        foreach($results as $ids){
            $user_ids .= $ids->user_id. ',';
            $email_ids[$ids->email_id] = $ids->email_id ;
        }

        $user_ids = substr($user_ids, 0, -1);

        $email_ids_string = implode(',',  $email_ids);

        if(substr($email_ids_string, 0, -1) == ',') $email_ids_string = substr($email_ids_string, 0, -1);


        $query = 'SELECT c.*,a.*
            FROM [wysija]queue as a
            JOIN [wysija]user as c on a.`user_id` = c.`user_id`
            WHERE a.user_id IN('.$user_ids.') and a.email_id IN('.$email_ids_string.') ORDER BY a.`priority` ASC, a.`send_at` ASC, a.`user_id`';

        $results=$this->query('get_res',$query,OBJECT_K);
        if($results === null){
            $this->query('REPAIR TABLE [wysija]queue, [wysija]user, [wysija]email');
        }

        if(!empty($results)){
                $first_element_queued = reset($results);
                $this->query('UPDATE [wysija]queue SET send_at = send_at + 1 WHERE email_id = '.$first_element_queued->email_id.' AND user_id = '.$first_element_queued->user_id.' LIMIT 1');
        }
        return $results;
    }

    /**
     * calculate the delay of the follow up based on the email parameters that have been setup
     * @param array $email_params_autonl
     * @return int
     */
    function calculate_delay($email_params_autonl){
        $delay=0;

        //check if there is a delay, if so we just set a send_at params
        if(isset($email_params_autonl['numberafter']) && (int)$email_params_autonl['numberafter']>0){
            switch($email_params_autonl['numberofwhat']){
                case 'immediate':
                    $delay=0;
                    break;
                case 'hours':
                    $delay=(int)$email_params_autonl['numberafter']*3600;
                    break;
                case 'days':
                    $delay=(int)$email_params_autonl['numberafter']*3600*24;
                    break;
                case 'weeks':
                    $delay=(int)$email_params_autonl['numberafter']*3600*24*7;
                    break;
            }
        }
        return $delay;
    }

}
