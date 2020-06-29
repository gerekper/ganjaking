<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_notifications extends WYSIJA_object{

    /**
     * is this time to send the report yet?
     * @return boolean
     */
    private function _can_send_daily_report(){
        $model_config = WYSIJA::get('config' , 'model');
        $last_daily_report = $model_config->getValue('last_daily_report');

        // make sure the last report was not sent within the last 23 hours
        if(time() - $last_daily_report < (3600*23)) return false;

        // if the scheduled time is passed let's send the email
        // we don't compare to the time recorded but to the offset time which is the time set by the user in his time
        $helper_toolbox = WYSIJA::get('toolbox','helper');
        $unix_scheduled_time = strtotime('Today, 23:00:00');

        if($helper_toolbox->localtime_to_servertime($unix_scheduled_time) < time()){
            return true;
        }
        return false;
    }


   /**
    * send an email summary of the email activity
    * @return type
    */
   public function send_daily_report(){
       //make sure tody's report hasn't been sent yet
       if(!$this->_can_send_daily_report()) return false;

        //know everything that happened in the last 24 hours
        $one_day_ago = time()-(3600*24);

        $model_email_user_stat = WYSIJA::get('email_user_stat','model');
        $query='SELECT COUNT('.$model_email_user_stat->getPk().') as count, status FROM `[wysija]'.$model_email_user_stat->table_name.'`
            WHERE sent_at>'.$one_day_ago.'
                GROUP BY status';
        $status_count = $model_email_user_stat->query('get_res',$query);

        $query = 'SELECT B.user_id,B.email FROM `[wysija]'.$model_email_user_stat->table_name.'` as A JOIN `[wysija]user` as B on A.user_id=B.user_id
            WHERE A.sent_at>'.$one_day_ago." AND A.status='-1'";
        $details = $model_email_user_stat->query('get_res',$query);

        $total = 0;
        foreach($status_count as &$count){
            switch($count['status']){
                case '-2':
                    $count['status']=__('undelivered',WYSIJA);
                    break;
                case '-1':
                    $count['status']=__('bounced',WYSIJA);
                    break;
                case '0':
                    $count['status']=__('unopened',WYSIJA);
                    break;
                case '1':
                    $count['status']=__('opened',WYSIJA);
                    break;
                case '2':
                    $count['status']=__('clicked',WYSIJA);
                    break;
                case '3':
                    $count['status']=__('unsubscribed',WYSIJA);
                    break;
            }
            $total = $total + $count['count'];
        }

        if((int)$total<=0) return;

        $html = '<h2>'.__('Today\'s statistics',WYSIJA).'</h2>';
        $html .= '<h3>'.sprintf(__('Today you have sent %1$s emails',WYSIJA),$total);
        foreach($status_count as $counting){
            $html .= sprintf(__(', %1$s of which were %2$s',WYSIJA),$counting['count'],$counting['status']);
        }
        $html .= '.</h3>';
        if(count($details)>0){
            $html .= '<h2>'.sprintf(__('Here is the list of bounced emails.',WYSIJA),$total).'</h2>';

            foreach($details as $email){
                $html .= '<p>'.$email['email'].'</p>';
            }
        }
        $html .= '<p>'.__('Cheers, your MailPoet Newsletter Plugin',WYSIJA).'</p>';

        $model_config = WYSIJA::get('config','model');
        $helper_mailer = WYSIJA::get('mailer','helper');
        $helper_mailer->testemail=true;

        $emails_notifieds = $model_config->getValue('emails_notified');
        $emails_notifieds = explode(',' , $emails_notifieds);


        foreach($emails_notifieds as $receiver){
            $helper_mailer->sendSimple( trim($receiver) , __('Your daily newsletter stats',WYSIJA),$html);
        }

        // keep track of the time we sent the daily report to make sure we don't send it many times per day but just once
        $model_config->save( array('last_daily_report' => time() ));

   }

}