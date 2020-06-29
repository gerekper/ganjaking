<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_email extends WYSIJA_object{

    function __construct(){
        parent::__construct();
    }

    /**
     * used to strip the unsubscribe links and the view in browser links from an email html
     * @param type $content
     * @return type
     */
    function stripPersonalLinks($content){

        //delete the view in browser span
        $content = preg_replace('#<td id="wysija_viewbrowser_content"[^>]*>(.*)</td>#Uis','',$content);
        $content = preg_replace('#<p *class="wysija_viewbrowser_container"[^>]*>(.*)</p>#Uis','',$content);

        //delete the unsubscribe td
        $content = preg_replace('#<td id="wysija_unsubscribe_content"[^>]*>(.*)</td>#Uis','',$content);
        $content = preg_replace('#<p *class="wysija_unsubscribe_container"[^>]*>(.*)</p>#Uis','',$content);

        return $content;

    }

    /**
     *
     * @param array $values configuration form's current values
     * @param boolean $testMultisite test the multisite configuration
     * @return boolean
     */
    function send_test_mail($values,$testMultisite=false){
        $content_email=__('Yup, it works. You can start blasting away emails to the moon.',WYSIJA);

        $options=array(
            'sending_method'=>'sending_method',
            'sending_emails_site_method'=>'sending_emails_site_method',
            'sendmail_path'=>'sendmail_path',
            'smtp_rest'=>'smtp_rest',
            'smtp_host'=>'smtp_host',
            'smtp_port'=>'smtp_port',
            'smtp_secure'=>'smtp_secure',
            'smtp_auth'=>'smtp_auth',
        );

        if($testMultisite){
            $is_multisite=is_multisite();

            //$is_multisite=true;//PROD comment that line
            if(!$is_multisite) return false;
        }

        switch($values[$options['sending_method']]){
            case 'network':
            case 'site':
                if($values[$options['sending_emails_site_method']]=='phpmail'){
                    $send_method='PHP Mail';
                }else{
                    $send_method='Sendmail';
                    $sendmail_path=$_POST['data']['wysija[config]['.$options['sendmail_path'].']'];
                }
                break;
            case 'smtp':
                $smtp=array();
                $send_method='SMTP';
                $config=WYSIJA::get('config','model');
                if(!isset($values[$options['smtp_rest']])) unset($config->values[$options['smtp_rest']]);
                break;
            case 'gmail':
                $send_method='Gmail';

                $values[$options['smtp_host']]='smtp.gmail.com';
                $values[$options['smtp_port']]='465';
                $values[$options['smtp_secure']]='ssl';
                $values[$options['smtp_auth']]=true;

                $content_email=__('You\'re all setup! You\'ve successfully sent with Gmail.',WYSIJA).'<br/><br/>';
                $content_email.=str_replace(
                        array('[link]','[/link]'),
                        array('<a href="http://support.mailpoet.com/knowledgebase/send-with-smtp-when-using-a-professional-sending-provider/?utm_source=wpadmin&utm_campaign=test_email_result" target="_blank" title="SendGrid partnership">','</a>'),
                        __('Looking for a faster method to send? [link]Read more[/link] on sending with a professional SMTP.',WYSIJA));
                break;
        }

        $mailer=WYSIJA::get('mailer','helper');
        $mailer->__construct('',$values,$testMultisite);

        $current_user=WYSIJA::wp_get_userdata();
        $mailer->testemail=true;
        $mailer->wp_user=&$current_user->data;
		// If users enter a custom email, let's send to that address
		if (isset($values['test_mails']) && trim($values['test_mails']) != '') {
			$current_user->data->user_email = $values['test_mails'];
		}

        $res=$mailer->sendSimple($current_user->data->user_email,str_replace('[send_method]',$send_method,__('[send_method] works with MailPoet',WYSIJA)),$content_email);

        if($res){
            $this->notice(sprintf(__('Test email successfully sent to %s',WYSIJA),'<b><i>'.$current_user->data->user_email.'</i></b>'));
            return true;
        }else{
            $config=WYSIJA::get('config','model');
            $bounce = $config->getValue('bounce_email');
            if(in_array($config->getValue('sending_method'),array('smtp','gmail')) && $config->getValue('smtp_secure')=='ssl' && !function_exists('openssl_sign')){
                $this->error(__('The PHP Extension openssl is not enabled on your server. Ask your host to enable it if you want to use an SSL connection.',WYSIJA));
            }elseif(!empty($bounce) AND !in_array($config->getValue('sending_method'),array('smtp_com','elasticemail'))){
                $this->error(sprintf(__('The bounce email address "%1$s" might actually cause the problem. Leave the field empty and try again.',WYSIJA),$bounce));
            //Case 2 : you are using SMTP but you didn't add an authentication
            }elseif(in_array($config->getValue('sending_method'),array('smtp','gmail')) AND !$config->getValue('smtp_auth') AND strlen($config->getValue('smtp_password')) > 1){
                $this->error(__('You specified an SMTP password but you don\'t require an authentication, you might want to turn the SMTP authentication ON.',WYSIJA));
            //Case 3 : you are on localhost!
            }elseif((strpos(WYSIJA_URL,'localhost') || strpos(WYSIJA_URL,'127.0.0.1')) && in_array($config->getValue('sending_method'),array('sendmail','qmail','mail'))){
                $this->error(__('Your localhost may not have a mail server. To verify, please log out and click on the "Lost your password?" link on the login page. Do you receive the reset password email from your WordPress?',WYSIJA));
            }

            $this->error($mailer->reportMessage);
            return false;
        }
    }

    /**
     * get view in browser link
     * @param array/stdClass $data_email
     * @return string url
     */
    function getVIB($data_email){
        if (!is_array($data_email) && is_object($data_email))
            $data_email = (array) $data_email;
        if(false && isset($data_email['params']['vib_id'])) return WYSIJA::get_permalink($data_email['params']['vib_id'],false);
        else{
           $paramsurl=array(
                'wysija-page'=>1,
                'controller'=>'email',
                'action'=>'view',
                'email_id'=>$data_email['email_id']
                );

            $modelConf=WYSIJA::get('config','model');
            return WYSIJA::get_permalink($modelConf->getValue('confirm_email_link'),$paramsurl);
        }
    }

    /**
     * return an array of active follow_ups
     * @param type $data
     * @param boolean $delay calculate delay and add it to the result
     */
    function get_active_follow_ups($data=array('subject','params'),$delay=false){
        if($delay)  $model_queue=WYSIJA::get('queue','model');

        $model_email=WYSIJA::get('email','model');
        $model_email->setConditions(array('type'=>2,'status'=>99));
        $automatic_emails=$model_email->getRows($data);

        $follow_ups_per_list=array();
        foreach($automatic_emails as &$auto_email){
            $model_email->getParams($auto_email);
            if($delay)  $auto_email['delay']=$model_queue->calculate_delay($auto_email['params']['autonl']);
            if(isset($auto_email['params']['autonl']['event']) && $auto_email['params']['autonl']['event']=='subs-2-nl'){
                if(!isset($follow_ups_per_list[$auto_email['params']['autonl']['subscribetolist']]))    $follow_ups_per_list[$auto_email['params']['autonl']['subscribetolist']]=array();
                $follow_ups_per_list[$auto_email['params']['autonl']['subscribetolist']][]=$auto_email;
            }
        }

        return $follow_ups_per_list;
    }

}