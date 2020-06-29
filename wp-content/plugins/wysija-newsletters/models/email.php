<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_model_email extends WYSIJA_model{

    var $pk='email_id';
    var $table_name='email';
    var $columns=array(
        'email_id'=>array('auto'=>true),
        'campaign_id' => array('type'=>'integer'),
        'subject' => array('req'=>true),
        'body' => array('req'=>true,'html'=>1),
        'from_email' => array('req'=>true),
        'from_name' => array('req'=>true),
        'replyto_email' => array(),
        'replyto_name' => array(),
        'attachments' => array(),
        'status' => array('type'=>'integer'),
        /*draft :0
          sending:1&3
          sent:2
          paused:-1*/
        'type' => array('type'=>'integer'),
        /*
         * 0 : confirmation email
         * 1 : standard nl
         * 2 : autonewsletter
         */
        'number_sent'=>array('type'=>'integer'),
        'number_opened'=>array('type'=>'integer'),
        'number_clicked'=>array('type'=>'integer'),
        'number_unsub'=>array('type'=>'integer'),
        'number_bounce'=>array('type'=>'integer'),
        'number_forward'=>array('type'=>'integer'),
        'sent_at' => array('type'=>'date'),
        'created_at' => array('type'=>'date','autoins'=>1),
        'modified_at' => array('type'=>'date'),
        'params' => array(),
        'wj_data' => array(),
        'wj_styles' => array()
    );

    var $retro_active_autoresponders=true;

    function __construct(){
        parent::__construct();
    }

    /**
     * validation before insertion
     * @return type
     */
    function beforeInsert(){
        $this->checkParams();
        $model_config = WYSIJA::get('config','model');
        if(!isset($this->values['from_email'])) $this->values['from_email'] = $model_config->getValue('from_email');
        if(!isset($this->values['from_name'])) $this->values['from_name'] = $model_config->getValue('from_name');
        if(!isset($this->values['replyto_email'])) $this->values['replyto_email'] = $model_config->getValue('replyto_email');
        if(!isset($this->values['replyto_name'])) $this->values['replyto_name'] = $model_config->getValue('replyto_name');
        if(!isset($this->values['modified_at'])) $this->values['modified_at'] = time();

        return true;
    }

    /**
     * validation before deletion
     * @param type $conditions
     * @return type
     */
    function beforeDelete($conditions){
        if(!isset($conditions['email_id'])){
            return true;
        }else $emailid=$conditions['email_id'];

        $modelQ=WYSIJA::get('queue','model');
        $modelQ->delete(array('email_id'=>$conditions['email_id']));
        return true;
    }

    /**
     * validation before updating the data
     * @return boolean
     */
    function beforeUpdate($id = null){

        if(isset($this->values['params']) && is_array($this->values['params'])){

            //update the nextSend value
            if(!isset($this->values['params']['autonl']['nextSend']) && isset($this->values['type']) && $this->values['type']=='2'){
                $auton=WYSIJA::get('autonews','helper');
                $this->values['params']['autonl']['nextSend']=$auton->getNextSend($this->values);
            }
        }

        //get the params from the db and update them
        $this->checkParams();


        return true;
    }

    /**
     * trigger on Update
     */
    function afterUpdate($result_save_id){
        //First reply-to address (name + email) is a default value for next newsletter
        $model_config=WYSIJA::get('config','model');
        if(!$model_config->getValue('replyto_name') || !$model_config->getValue('replyto_email')){
            $email = $this->getOne(false,array('email_id'=>$result_save_id));
            $model_config->save(array(
                'replyto_name' => $email['replyto_name'],
                'replyto_email' => $email['replyto_email']
            ));
        }
        return true;
    }


    /**
     * return the online version link of a newsletter
     * @param int $email_id
     * @param string $text
     * @param string $urlOnly
     * @return string
     */
    function getPreviewLink($email_id,$text=false,$urlOnly=true){
        if(!$text) $text=__('View',WYSIJA);

        $this->reset();
        $modelConf=WYSIJA::get('config','model');

        $params=array(
            'wysija-page'=>1,
            'controller'=>'email',
            'action'=>'view',
            'email_id'=>$email_id,
            );


        $fullurl=WYSIJA::get_permalink($modelConf->getValue('confirm_email_link'),$params);
        if($urlOnly) return $fullurl;
        return '<a href="'.$fullurl.'" target="_blank">'.$text.'</a>';

    }


    /**
     * what to do when starting to send a newsletter based on the type and other parameters
     * @param mixed $email
     * @return boolean
     */
    function send_activate($email){
        if(!is_array($email)){
            if(is_numeric($email)){
                $email=$this->getOne(false,array('email_id'=>$email));
            }else return false;
        }

        // we go through that queuing function which will check if it is necessary to queue the email
        // depending on the type of email we're dealing with there will be no queuing
        if($this->retro_active_autoresponders){
            $model_queue = WYSIJA::get('queue','model');
            $emails_have_been_queued = $model_queue->queue_email($email);
        }

        //set the email status based on parameters and also return a message
        $email_status=99;
        $sent_status = array();

        if((int)$email['type']===1)  {
            if(isset($email['params']['schedule']['isscheduled']) && !$emails_have_been_queued){
                $email_status=4;
                $this->notice(__('Newsletter has been scheduled.',WYSIJA));
            } else $this->notice(__('Your latest newsletter is being sent.',WYSIJA));
            $sent_status['sent_at'] = time();
        } else $this->notice(__('Your auto newsletter has been activated.',WYSIJA));
        $sent_status['status'] = $email_status;


        $this->reset();
        $this->update($sent_status,array('email_id'=>$email['email_id']));
        return true;
    }


    /**
     * used in post notification scheme, it uses the model email to create a child to send when ready
     * @param array $email
     * @param boolean $immediatePostNotif
     * @return int next send value
     */
    function give_birth($email, $immediatePostNotif=false){
        WYSIJA::log('give_birth_starts', $email['params']['autonl']['nextSend'], 'post_notif');
        //duplicate email with the right body and title set it as type 1
        if(isset($email['params']) && !is_array($email['params']))  $this->getParams($email);
        $emailChild=$email;
        $paramsVal=$email['params'];

        if(!isset($paramsVal['autonl']['total_child'])) $paramsVal['autonl']['total_child'] = 0;
        $paramsVal['autonl']['total_child']++;

        unset($emailChild['email_id']);
        unset($emailChild['created_at']);
        $emailChild['type']=1;
        $emailChild['status']=99;
        $emailChild['sent_at']=time();

        $this->reset();
        unset($emailChild['params']['autonl']);

        // get articles ids used in previously sent childs
        $ids = (!empty($paramsVal['autonl']['articles']['ids'])) ? $paramsVal['autonl']['articles']['ids'] : array();

        // build autonl articles params for child
        $emailChild['params']['autonl']['articles'] = array('ids' => $ids, 'count' => 0, 'first_subject' => '');
        if(isset($email['params']['autonl']['firstSend']))  $emailChild['params']['autonl']['firstSend'] = $email['params']['autonl']['firstSend'];
        if(isset($email['params']['autonl']['lastSend']))  $emailChild['params']['autonl']['lastSend'] = $email['params']['autonl']['lastSend'];

        //if it's an immediate post notif let know the render email
        if($immediatePostNotif) {
            $emailChild['params']['autonl']['articles']['immediatepostid'] = (int)$immediatePostNotif;
            //if this article is already set there is no reason to give birth to a child email
            if(isset($email['params']['autonl']['articles']['ids']) && in_array($immediatePostNotif, $email['params']['autonl']['articles']['ids'])) return false;
        }

        $wjEngine = WYSIJA::get('wj_engine', 'helper');
        // set data & styles
        if(isset($emailChild['wj_data'])) { $wjEngine->setData($emailChild['wj_data'], true); } else { $wjEngine->setData(); }
        if(isset($emailChild['wj_styles'])) { $wjEngine->setStyles($emailChild['wj_styles'], true); } else { $wjEngine->setStyles(); }

        // generate email html body
        $body = $wjEngine->renderEmail($emailChild);

        // get back email data as it will be updated during the rendering (articles ids + articles count)
        $emailChild = $wjEngine->getEmailData();
        $emailChild['body'] = $body;

        // update parent email articles' ids to reflect the ones added in the child email
        $paramsVal['autonl']['articles']['ids'] = $emailChild['params']['autonl']['articles']['ids'];

        // added new_ids parameter in order to know which articles are being used in that newsletter for instance with shortcodes
        if(!empty($email['params']['autonl']['articles']['ids'])) {
            $emailChild['params']['autonl']['articles']['new_ids'] = array_diff($emailChild['params']['autonl']['articles']['ids'], $email['params']['autonl']['articles']['ids']);
        } else {
            $emailChild['params']['autonl']['articles']['new_ids'] = $emailChild['params']['autonl']['articles']['ids'];
        }

        $donotsend=false;
        // if there's no article, do not send
        if((int)$emailChild['params']['autonl']['articles']['count'] === 0) {
            $donotsend = true;
        }

        // we send if not told to not do it
        if(!$donotsend){

            // Parse old subject shortcodes.
            $emailChild['subject'] = str_replace(
                    array('[total]','[number]','[post_title]'),
                    array((int)$emailChild['params']['autonl']['articles']['count'],
                        (int)$paramsVal['autonl']['total_child'],
                        $emailChild['params']['autonl']['articles']['first_subject']),
                    $emailChild['subject']);
            $emailChild['params']['autonl']['total_child']=(int)$paramsVal['autonl']['total_child'];
            // Get the email object, needed for the shortcode class.
            $current_email_object = (object) $emailChild;

            // Parse subject shortcodes.
            $helper_mailer = WYSIJA::get('mailer','helper');
            $helper_mailer->parseSubjectUserTags($current_email_object);

            // Replace subject shortcodes.
            $helper_shortcodes = WYSIJA::get('shortcodes','helper');
            $emailChild['subject'] = $helper_shortcodes->replace_subject($current_email_object);

            // save the child email
            $emailChild['params']['autonl']['parent']=$email['email_id'];

            $this->dbg=false;//this line is to correct the crazy color so that it doesn't use the keepQry function.
            $emailChild['email_id']=$this->insert($emailChild);
            $this->reset();
            WYSIJA::log('check_post_notif_give_birth_before_send', $emailChild, 'post_notif');
            $this->send_activate($emailChild);
        }
        WYSIJA::log('prev_send_value_give_birth', $email['params']['autonl']['nextSend'], 'post_notif');
        // update the parent with the new nextSend date
        $auton=WYSIJA::get('autonews','helper');
        $nextSendValue=$auton->getNextSend($email);

        WYSIJA::log('next_send_value_give_birth', $nextSendValue, 'post_notif');
        //update the parent email only it has been  sent
        if(!$donotsend){
            $paramsVal['autonl']['nextSend']=$nextSendValue;
            if(isset($paramsVal['autonl']['late_send'])) unset($paramsVal['autonl']['late_send']);
            $this->reset();

            //we use to have a filter compared to the first send date, but we should have a filter to the lastSend date instead
            if(!isset($email['params']['autonl']['firstSend'])) $paramsVal['autonl']['firstSend']=time();
            $paramsVal['autonl']['lastSend']=time();


            $this->update(array('params'=>$paramsVal), array('email_id'=>$email['email_id']));
        }

        return $nextSendValue;
    }

    /**
     * special get overriding the model class in order to load the params of the email
     * @param array $columns
     * @param array $conditions
     * @return type
     */
    function get($columns=array(),$conditions=array()){

        $results=parent::get($columns,$conditions);

        if(is_array($results) && (!isset($results['params']))){
            foreach($results as &$result){
                $this->getParams($result);
            }
        }else $this->getParams($results);

        return $results;
    }

    /**
     * convert the base64 serialized param into an array directly on  email load
     * @param type $object
     */
    function getParams(&$object=false){
        if(!$object) $object=&$this->values;

        if(is_array($object)){
            if(isset($object['params']) && is_string($object['params'])){
                $object['params']=unserialize(base64_decode($object['params']));
            }
        }else{
            if(isset($object->params) && is_string($object->params)){
                $object->params=unserialize(base64_decode($object->params));
            }
        }
    }



    /**
     * encode the param array before saving the email data
     * @param object reference $object
     */
    function checkParams(&$object=false){

        if(!$object) $object=&$this->values;
        //else    dbg($object,0);
        if(is_array($object)){

            if(isset($object['params']) && is_array($object['params'])){
                 if(isset($object['email_id'])){
                    $newEmailModel=new WYSIJA_model_email();
                    $recentData=$newEmailModel->getOne(false,array('email_id'=>$object['email_id']));
                    if(is_string($recentData['params']))    $recentData['params']=unserialize(base64_decode($recentData['params']));

                }

                foreach($object['params'] as $pk => $pv){
                    if($pk=='autonl'){
                        foreach($pv as $pvk => $pvv){
                            $recentData['params'][$pk][$pvk]=$pvv;
                        }
                    }else   $recentData['params'][$pk]=$pv;
                }

                $object['params']=base64_encode(serialize($recentData['params']));
            }
        }else{
            if(isset($object->params) && is_array($object->params)){
                 if(isset($object->email_id)){
                    $newEmailModel=new WYSIJA_model_email();
                    $recentData=$newEmailModel->getOne(false,array('email_id'=>$object->email_id));
                    $recentData['params']=unserialize(base64_decode($recentData['params']));
                }

                foreach($object->params as $pk => $pv){
                    if($pk=='autonl'){
                        foreach($pv as $pvk => $pvv){
                            $recentData['params'][$pk][$pvk]=$pvv;
                        }
                    }else   $recentData['params'][$pk]=$pv;
                }

                $object->params=base64_encode(serialize($recentData['params']));
            }
        }
    }
}
