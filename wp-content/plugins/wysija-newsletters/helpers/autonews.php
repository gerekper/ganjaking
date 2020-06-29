<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_autonews  extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    function events($key=false,$get=true,$value_set=array()){
        static $events=array();
        if($get){
            if(!$key){
                return $events;
            }else{
                if(isset($events[$key])) return $events[$key];
                return false;
            }

        }else{
            if(isset($events[$key])) return false;
            $events[$key]=$value_set;
        }
    }

    function register($key_event,$event=array()){
        $this->events($key_event,false,$event);
    }

    function get($fieldKey){
         return $this->events($fieldKey);
    }

    /**
     * I'm not sure if this function is useful anymore
     * @param type $email
     * @return type
     */
    function _deprecated_nextSend($email=false){
        if(!$email) return;
        $model_email=WYSIJA::get('email','model');

        if(is_array($email)){
            $email_data=$model_email->getOne(false,array('email_id'=>$email['email_id']));
        }else{
            $email_data=$model_email->getOne(false,array('email_id'=>$email));
        }
        return $model_email->give_birth($email_data);
    }

    /**
     *
     * @param type $email
     * @return type
     */
    function getNextSend($email) {
        $schedule_at = -1;

        //this condition makes sure that our $email is a post notification
        if((int)$email['type'] === 2 && isset($email['params']['autonl']['event']) && $email['params']['autonl']['event'] === 'new-articles') {

            $helper_toolbox = WYSIJA::get('toolbox','helper');
            // get current time
            $now = time();


            //local time = site's time set by the administrator of the site in WP's settings
            //server time = time return by PHP functions such as time()
            //nextSend is the time scheduled in the future of the local time
            //so if we compare the current time to the next send value we need to offset only one value and that would be the nextSend
            //this way both values are on server time
            if(!isset($email['params']['autonl']['nextSend']) || $now > $helper_toolbox->localtime_to_servertime($email['params']['autonl']['nextSend'])) {
                switch($email['params']['autonl']['when-article']) {
                    case 'immediate':
                        break;
                    case 'daily':
                        // get timestamp of when the newsletter is supposed to be sent
                        $schedule_at = strtotime($email['params']['autonl']['time']);

                        // check if the scheduled at time has already passed
                        if($helper_toolbox->localtime_to_servertime($schedule_at) < $now) {
                            // schedule it for tomorrow
                            $schedule_at = strtotime('tomorrow '.$email['params']['autonl']['time']);
                        }
                        break;
                    case 'weekly':
                        // get timestamp of when the newsletter is supposed to be sent
                        $schedule_at = strtotime(ucfirst($email['params']['autonl']['dayname']).' '.$email['params']['autonl']['time']);

                        // check if the scheduled at time has already passed
                        if($helper_toolbox->localtime_to_servertime($schedule_at) < $now) {
                            // schedule it for next week
                            $schedule_at = strtotime('next '.ucfirst($email['params']['autonl']['dayname']).' '.$email['params']['autonl']['time']);
                        }
                        break;
                    case 'monthly':
                        $time_current_day=date('d',$now);
                        $time_current_month=date('m',$now);
                        $time_current_year=date('y',$now);

                        // we increment the next date to next months in two cases
                        // 1 - if we're setting the next date using the interface in step 1 or step 3 of the newsletter edition and the current day is greater to the selected day
                        if(isset( $_POST['save-reactivate'] ) || isset( $_POST['submit-send'] ) ){
                            //trigger has to be next month
                            if($time_current_day > $email['params']['autonl']['daynumber']) {

                                if((int)$time_current_month === 12) {
                                   //year +1
                                   $time_current_month=1;
                                   $time_current_year++;
                                }else{
                                   //current year
                                    $time_current_month++;
                                }
                            }
                        // 2 - if we're setting the next date automatically and the date is already passed
                        }else{
                            if($helper_toolbox->localtime_to_servertime($schedule_at) < $now) {

                                if((int)$time_current_month === 12) {
                                   //year +1
                                   $time_current_month=1;
                                   $time_current_year++;
                                }else{
                                   //current year
                                    $time_current_month++;
                                }
                            }
                        }

                        //3 - otherwise we stay in the same month


                        $schedule_at=strtotime($time_current_month.'/'.$email['params']['autonl']['daynumber'].'/'.$time_current_year.' '.$email['params']['autonl']['time']);
                        break;
                    case 'monthlyevery': // monthly every X Day of the week
                        $current_day = date('d', $now);
                        $current_month = date('m', $now);
                        $current_year = date('y', $now);

                        // calculate the timestamp of the Xth day of the week of the current month
                        // strtotime('02/01/13 1 Monday 20:00:00') -> this will return the timestamp of the 1st monday of the current month
                        $schedule_at = strtotime(
                            sprintf('%02d/01/%02d %d %s %s',
                            $current_month,
                            $current_year,
                            $email['params']['autonl']['dayevery'],
                            ucfirst($email['params']['autonl']['dayname']),
                            $email['params']['autonl']['time']
                        ));

                        if($helper_toolbox->localtime_to_servertime($schedule_at) < $now) {
                            // get first day timestamp of next month
                            $first_day_of_next_month = $this->get_first_day_of_month($schedule_at, 1);

                            // get next month's Xth day of the week
                            $schedule_at = strtotime(
                                sprintf('%02d/01/%02d %d %s %s',
                                    date('m', $first_day_of_next_month),
                                    date('y', $first_day_of_next_month),
                                    $email['params']['autonl']['dayevery'],
                                    ucfirst($email['params']['autonl']['dayname']),
                                    $email['params']['autonl']['time']
                                )
                            );
                        }
                        break;
                }
            }
        }
        return $schedule_at;
    }

    function get_first_day_of_month($time_stamp, $months_to_add = 0) {
        // You can add as many months as you want. mktime will accumulate to the next year.
        $date = getdate($time_stamp); // Covert to Array
        // add number of months
        $date['mon'] = $date['mon'] + (int)$months_to_add;
        // set day to 1
        $date['mday'] = 1;
        // return timestamp
        return mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);
    }

    /**
     * get the time of the n dayname of the month
     * @param type $first_day_of_month
     * @param type $day_name
     * @param type $which_number
     * @param type $time_now
     * @return type
     */
    function getNextDay($first_day_of_month,$day_name,$which_number,$time_now){
        $name_first_day = strtolower(date('l', $first_day_of_month));

        if($name_first_day == strtolower($day_name)) $which_number--;
        for($i=0; $i < $which_number;$i++){
            $first_day_of_month = strtotime('next '.ucfirst($day_name), $first_day_of_month);
        }
        return $first_day_of_month;
    }


    /**
     * check if there is post notification needing a child email
     */
    function checkPostNotif(){
        // flag security to make sure that there can't be two checks of the post notif in the same minute
        $current_check = (float)get_option('wysija_check_pn');

        // there is a check that has been starting to run less than 60 seconds ago
        if(microtime(true) < ($current_check+60)){
            WYSIJA::log('already_running_checkPN', $current_check, 'post_notif');
            return false;
        }

        // flag is down we process our post notification check and set the start time of the current check
        $current_check=microtime(true);
        WYSIJA::update_option('wysija_check_pn',$current_check);

        // let's check when do we come here
        WYSIJA::log('check_post_notif_starts', $current_check , 'post_notif');

        $model_email=WYSIJA::get('email','model');
        $model_email->reset();
        $all_emails=$model_email->get(false,array('type'=>'2','status'=>array('1','3','99')));

        if($all_emails){
            $helper_toolbox=WYSIJA::get('toolbox','helper');
            foreach($all_emails as $email){
                //post notification make a child newsletter when the timing is immediate otherwise let the cron take care of it
                if($email['params']['autonl']['event']=='new-articles' && $email['params']['autonl']['when-article']!='immediate'){
                    //check if the next sending is passed if so then we give birth to one child email
                    //IMPORTANT WE COMPARE TO THE OFFSET TIME (time set by the administrator)
                    //if the nextSend doesn't exist then we set it
                    if(!isset($email['params']['autonl']['nextSend'])){
                        WYSIJA::log('check_post_notif_next_send_not_set', $current_check , 'post_notif');
                    }else {
                        //if the next send is passed we should trigger it
                        $time_now_server=time();
                        if($time_now_server > $helper_toolbox->localtime_to_servertime($email['params']['autonl']['nextSend'])){
                            $how_late=$time_now_server-$helper_toolbox->localtime_to_servertime($email['params']['autonl']['nextSend']);
                            //check how late was the previous notification,
                            //if it has been more than two hours late then cancel it and change it to the next day
                            if(!$this->cancel_late_post_notification($email,$how_late)){
                                 WYSIJA::log('check_post_notif_before_give_birth', $current_check, 'post_notif');
                                //it is not cancel so we can give birth toa a child newsletter
                                $model_email->give_birth($email);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * email has a late post notification which needs to be cancelled and postponed
     * @param array $email
     * @param int $how_late
     * @return boolean
     */
    function cancel_late_post_notification($email,$how_late){
        $cancel_it=false;
        switch($email['params']['autonl']['when-article']) {
            case 'daily':
                //cancel a daily notification with more than two hours delay
                if($how_late>(2*3600)){
                    $cancel_it=true;
                }
                break;
            case 'weekly':
                //cancel a weekly notification with more than half a day delay
                if($how_late>(12*3600)){
                    $cancel_it=true;
                }
                break;
            case 'monthly':
                //cancel a monthly notification with more than a day delay
                if($how_late>(24*3600)){
                    $cancel_it=true;
                }
                break;
            case 'monthlyevery':
                //cancel a monthly notification with more than a day delay
                if($how_late>(24*3600)){
                    $cancel_it=true;
                }
                break;
        }
        //if the notification is being cancelled then we store the value of that late notification and update the nextSend
        if($cancel_it){

            $late_send=$email['params']['autonl']['nextSend'];
            WYSIJA::log('cancel_late_post_notification_late_send', $late_send, 'post_notif');
            $next_send=$this->getNextSend($email);
            $email['params']['autonl']['nextSend']=$next_send;
            $email['params']['autonl']['late_send']=$late_send;

            $model_email=WYSIJA::get('email','model');
            $model_email->reset();
            $model_email->update(array('params'=>$email['params']), array('email_id' => $email['email_id']));

            return true;
        }

        return false;
    }

    /**
     * check if there are any scheduled email not sent yet
     */
    function checkScheduled(){
        $model_email = WYSIJA::get('email','model');
        $helper_toolbox = WYSIJA::get('toolbox','helper');
        $model_email->reset();

        // select the scheduled emails
        $all_emails = $model_email->get(false,array('type'=>'1','status'=>'4'));

        if($all_emails){

            foreach($all_emails as $email){

                // check if the email is scheduled
                if(isset($email['params']['schedule']['isscheduled'])){

                    $schedule_date = $email['params']['schedule']['day'] . ' ' . $email['params']['schedule']['time'];
                    $unix_scheduled_time = strtotime($schedule_date);

                    // if the scheduled time is passed let's send the email
                    // we don't compare to the time recorded but to the offset time which is the time set by the user in his time
                    if($helper_toolbox->localtime_to_servertime($unix_scheduled_time) < time()){
                        $model_email->reset();
                        $model_email->send_activate($email);
                    }
                }
            }
        }
    }

    function refresh_automatic_content($email_ids = array()) {
        // TO OPTIMIZE: add a boolean flag for ALP widget being present or not (this way we filter out "static" auto nl)
        $model_email = WYSIJA::get('email', 'model');

        $conditions = array('type' => 2, 'status' => array(1, 3, 99));
        if(!empty($email_ids)) {
            $conditions['email_id'] = $email_ids;
        }
        // get only the data needed to update an auto nl so we save some resources
        $data_needed = array('campaign_id','email_id','params','wj_styles','wj_data');
        $emails = $model_email->get( $data_needed, $conditions );

        foreach($emails as $key => $email) {
            if(is_array($email) && isset($email['params']['autonl']['event']) ) {

                $wj_data = unserialize(base64_decode($email['wj_data']));
                $reload_auto_content = false;
                foreach($wj_data['body'] as $block) {
                    if(isset($block['type']) && $block['type'] === 'auto-post') {
                        $reload_auto_content = true;
                    }
                }
                if(!$reload_auto_content) continue;

                // we have to regenerate the html rendering of each auto newsletter
                $helper_wj_engine = WYSIJA::get('wj_engine', 'helper');
                $helper_wj_engine->setStyles($email['wj_styles'], true);
                $helper_wj_engine->setData($email['wj_data'], true);

                // update email data
                $values = array(
                    'email_id' => (int)$email['email_id'],
                    'wj_data' => $helper_wj_engine->getEncoded('data'),
                    'body' => $helper_wj_engine->renderEmail($email)
                );

                // make sure the modified_at columns get updated
                $model_email->columns['modified_at']['autoup'] = 1;

                // update data in DB
                $model_email->update($values, array('email_id' => (int)$email['email_id']));
            }
        }
    }

    // removes any auto-post block
    function remove_autopost_blocks($data = array()) {
        if(empty($data)) {
            return false;
        }

        // decode data
        $wj_data = unserialize(base64_decode($data));

        // init updated_wj_data variable with initial wj_data
        $updated_wj_data = $wj_data;

        foreach($wj_data['body'] as $key => $block) {
            // if we detect an auto-post block, we need to remove it
            if(isset($block['type']) && $block['type'] === 'auto-post') {
                // remove block from updated data
                unset($updated_wj_data['body'][$key]);
            }
        }
        // if the wj_data has not changed, return false
        if($updated_wj_data === $wj_data) {
            return false;
        } else {
            // otherwise return encoded version of updated_wj_data
            return base64_encode(serialize($updated_wj_data));
        }
    }
}
