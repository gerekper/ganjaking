<?php
defined('WYSIJA') or die('Restricted access');


class WYSIJA_control_front_confirm extends WYSIJA_control_front{
    var $model='user';
    var $view='confirm';

    function __construct(){
        parent::__construct();
    }

    function _testKeyuser(){
        $this->helperUser=WYSIJA::get('user','helper');

        $this->userData=$this->helperUser->checkUserKey();
        add_action('init',array($this,'testsession'));

        if(!$this->userData){
            $this->title=__('Page does not exist.',WYSIJA);
            $this->subtitle=__('Please verify your link to this page.',WYSIJA);
            return false;
        }
        return true;
    }

    /**
     * confirm subscription page
     * return boolean
     */
    function subscribe(){
        $helper_user = WYSIJA::get('user','helper');
        if(!isset($_REQUEST['demo'])){
            $helper_user->confirm_user();

            if(!empty($helper_user->title))    $this->title = $helper_user->title;
            if(!empty($helper_user->subtitle))    $this->optional_subtitle = $helper_user->subtitle;
        }else{
            $model_config=WYSIJA::get('config','model');

            // we need to call the translation otherwise it will not be loaded and translated
            $model_config->add_translated_default();

            $this->title = sprintf($model_config->getValue('subscribed_title'), 'demo');
            $this->optional_subtitle=$model_config->getValue('subscribed_subtitle');
        }

        return true;
    }

    function unsubscribe(){
        remove_action( 'bp_get_request_unsubscribe', 'bp_email_unsubscribe_handler' );
        $model_config=WYSIJA::get('config','model');

        // we need to call the translation otherwise it will not be loaded and translated
        $model_config->add_translated_default();

        $this->title = $model_config->getValue('unsubscribed_title');
        if(!isset($model_config->values['unsubscribed_title'])) $this->title = __("You've unsubscribed!",WYSIJA);

        $this->optional_subtitle = $model_config->getValue('unsubscribed_subtitle');
        if(!isset($model_config->values['unsubscribed_subtitle'])) $this->optional_subtitle = __("Great, you'll never hear from us again!",WYSIJA);

        $wysija_key = '';
        if(isset( $_GET['wysija-key'] )){
            $wysija_key =  filter_var($_GET['wysija-key'], FILTER_SANITIZE_STRING);
        }
        $undo_paramsurl = array(
             'wysija-page' => 1,
             'controller' => 'confirm',
             'action' => 'undounsubscribe',
             'wysija-key' => $wysija_key
             );

        if(! isset($_GET['demo']) ){
            if($this->_testKeyuser()){
                $statusint=(int)$this->userData['details']['status'];
                if( ($model_config->getValue('confirm_dbleoptin') && $statusint>0) || (!$model_config->getValue('confirm_dbleoptin') && $statusint>=0)){
                    $listids=$this->helperUser->unsubscribe($this->userData['details']['user_id']);
                    $this->helperUser->uid=$this->userData['details']['user_id'];
                    if($model_config->getValue('emails_notified') && $model_config->getValue('emails_notified_when_unsub'))    $this->helperUser->_notify($this->userData['details']['email'],false,$listids);
                }else{
                    $this->title=__('You are already unsubscribed.',WYSIJA);
                    return false;
                }
            }
        }else{
            $undo_paramsurl['demo'] = 1;
        }

        $link_undo = WYSIJA::get_permalink($model_config->getValue('unsubscribe_page'),$undo_paramsurl);


        $this->undo_unsubscribe = str_replace(
                array('[link]','[/link]'),
                array('<a href="'.$link_undo.'">','</a>'),
                '<p><b>'.__('You made a mistake? [link]Undo unsubscribe[/link].',WYSIJA)).'</b></p>';
        return true;
    }

    function undounsubscribe(){
        $model_config=WYSIJA::get('config','model');

        // we need to call the translation otherwise it will not be loaded and translated
        $model_config->add_translated_default();

        $this->title =__("You've been subscribed!",WYSIJA);
        $user_object = false;
        if(!isset($_REQUEST['demo'])){
            if($this->_testKeyuser()){
                $user_object = (object)$this->userData['details'];
                $this->helperUser->undounsubscribe($this->userData['details']['user_id']);
            }
        }

        //manage subcription link
        if(($model_config->getValue('manage_subscriptions'))){
            $helper_user = WYSIJA::get('config','helper');
            $model_user = WYSIJA::get('user','model');
            $editsubscriptiontxt = $model_config->getValue('manage_subscriptions_linkname');
            if(empty($editsubscriptiontxt)) $editsubscriptiontxt =__('Edit your subscription',WYSIJA);
            $this->subtitle = '<p>'.$model_user->getEditsubLink($user_object,false,'').'.</p>';
        }
        return true;
    }

    function subscriptions(){
        $data=array();

        //get the user_id out of the params passed
        if($this->_testKeyuser()){

            $data['user']=$this->userData;
            //get the list of user
            $model_list=WYSIJA::get('list','model');
            $model_list->orderBy('ordering','ASC');
            $data['list']=$model_list->get(array('list_id','name','description'),array('is_enabled'=>true,'is_public'=>true));

            $this->title=sprintf(__('Edit your subscriber profile: %1$s',WYSIJA),$data['user']['details']['email']);

            $this->subtitle=$this->viewObj->subscriptions($data);

            return true;
        }
    }

    function resend(){
        $this->title = $this->subtitle = 'The link you clicked has expired';
    }

    function save(){

        //get the user_id out of the params passed */
        if($this->_testKeyuser()){
            //update the general details */
            if(! is_array($_REQUEST['wysija']) || !is_array($_REQUEST['wysija']['user'])){
                return false;
            }
            $userid = $this->userData['details']['user_id'];
            unset($_REQUEST['wysija']['user']['user_id']);
            $model_config=WYSIJA::get('config','model');
            // we need to call the translation otherwise it will not be loaded and translated
            $model_config->add_translated_default();
            $this->helperUser->uid=$userid;

            // Prevent changing email address
            if($this->userData['details']['email'] != $_REQUEST['wysija']['user']['email']) {
                $this->error(__('Email cannot be changed. Please subscribe again.',WYSIJA),1);
                unset($_REQUEST['wysija']['user']['email']);
            }

            //if the status changed we might need to send notifications */
            if((int)$_REQUEST['wysija']['user']['status'] !=(int)$this->userData['details']['status']){
                if($_REQUEST['wysija']['user']['status']>0){
                    if($model_config->getValue('emails_notified_when_sub'))    $this->helperUser->_notify($this->userData['details']['email']);
                }else{
                    if($model_config->getValue('emails_notified_when_unsub'))    $this->helperUser->_notify($this->userData['details']['email'],false);
                }
            }

            //check whether the email address has changed if so then we should make sure that the new address doesnt exists already
            if(isset($_REQUEST['wysija']['user']['email'])){
                $_REQUEST['wysija']['user']['email']=trim($_REQUEST['wysija']['user']['email']);
                if($this->userData['details']['email']!=$_REQUEST['wysija']['user']['email']){
                    $this->modelObj->reset();
                    $result=$this->modelObj->getOne(false,array('email'=>$_REQUEST['wysija']['user']['email']));
                    if($result){
                        $this->error(sprintf(__('Email %1$s already exists.',WYSIJA),$_REQUEST['wysija']['user']['email']),1);
                        unset($_REQUEST['wysija']['user']['email']);
                    }
                }
            }

            $this->modelObj->update($_REQUEST['wysija']['user'],array('user_id'=>$userid));
            $id=$userid;

            $hUser=WYSIJA::get('user','helper');
            //update the list subscriptions */
           //run the unsubscribe process if needed
            if((int)$_REQUEST['wysija']['user']['status']==-1){
                $hUser->unsubscribe($id);
            }

            //update subscriptions */
            $modelUL=WYSIJA::get('user_list','model');
            $modelUL->backSave=true;
            /* list of core list */
            $modelLIST=WYSIJA::get('list','model');

	    // Using "like" condition in order to force sql query to OR (instead of AND). It'll be incorrct if status contains other values than 0/1.
            $results=$modelLIST->get(array('list_id'),array('like' => array('is_enabled'=>0, 'is_public' => 0)));
            $static_listids=array();
            foreach($results as $res){
                $static_listids[]=$res['list_id'];
            }

            //0 - get current lists of the user
            $userlists=$modelUL->get(array('list_id','unsub_date'),array('user_id'=>$id));

            $oldlistids=$new_list_ids=array();
            foreach($userlists as $listdata)    $oldlistids[$listdata['list_id']]=$listdata['unsub_date'];

            $config=WYSIJA::get('config','model');
            $dbloptin=$config->getValue('confirm_dbleoptin');
            //1 - insert new user_list
            if(!empty($_POST['wysija']['user_list']['list_id']) && is_array($_POST['wysija']['user_list']['list_id'])){
                $modelUL->reset();
                $modelUL->update(array('sub_date'=>time()),array('user_id'=>$id));
                foreach($_POST['wysija']['user_list']['list_id'] as $list_id){
                    //if the list is not already recorded for the user then we will need to insert it
                    if(!isset($oldlistids[$list_id])){
                        $modelUL->reset();
                        $new_list_ids[]=$list_id;
                        $dataul=array('user_id'=>$id,'list_id'=>$list_id,'sub_date'=>time());
                        //if double optin is on then we want to send a confirmation email for newly added subscription
                        if($dbloptin){
                            unset($dataul['sub_date']);
                            $modelUL->nohook=true;
                        }
                        $modelUL->insert($dataul);
                    //if the list is recorded already then let's check the status, if it is an unsubed one then we update it
                    }else{
                        if($oldlistids[$list_id]>0){
                            $modelUL->reset();
                            $modelUL->update(array('unsub_date'=>0,'sub_date'=>time()),array('user_id'=>$id,'list_id'=>$list_id));
                        }
                        //$alreadysubscribelistids[]=$list_id;
                    }
                }
            }




            //if a confirmation email needs to be sent then we send it

            if($dbloptin && !empty($new_list_ids)){
                $send_confirmation = true;
                $send_confirmation = apply_filters('mpoet_confirm_new_list_subscriptions_page', $send_confirmation);

                if($send_confirmation === true){
                    $hUser->sendConfirmationEmail($id,true,$new_list_ids);
                }else{
                    // this case has been made so that when subscribers add themselves to a
                    // list through the edit your subscription form they don't receive a confirmation email they already confirmed.
                    // so they receive also the autorespo,nders correspondign to that list immediately.
                    $helper_user = WYSIJA::get('user','helper');
                    $_REQUEST['wysiconf'] = base64_encode(json_encode($new_list_ids));
                    $helper_user->confirm_user();
                }
            }

            // list ids
            $list_ids = !empty($_POST['wysija']['user_list']['list_id']) ? $_POST['wysija']['user_list']['list_id'] : array();
            if(is_array($list_ids) === false) $list_ids = array();

            $notEqual = array_merge($static_listids, $list_ids);

            //delete the lists from which you've removed yourself
            $condiFirst = array('notequal'=>array('list_id'=> $notEqual), 'equal' => array('user_id' => $id, 'unsub_date' => 0));
            $modelUL=WYSIJA::get('user_list','model');
            $modelUL->update(array('unsub_date'=>time()),$condiFirst);
            $modelUL->reset();

            /*
            Custom Fields.
            */
            if (isset($_POST['wysija']['field'])) {
              WJ_FieldHandler::handle_all(
                $_POST['wysija']['field'], $id
              );
            }

            $this->notice(__('Newsletter profile has been updated.',WYSIJA));
            $this->subscriptions();

            //reset post otherwise wordpress will not recognise the post !!!
            $_POST=array();
        }
        return true;
    }
}
