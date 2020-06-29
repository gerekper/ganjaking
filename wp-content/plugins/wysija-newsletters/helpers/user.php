<?php

defined('WYSIJA') or die('Restricted access');

class WYSIJA_help_user extends WYSIJA_object {

    var $no_confirmation_email = false;

    /**
     * Used whend confirming email with dbl optin
     * @param type $user_id
     * @param type $status
     * @param type $auto
     */
    function subscribe($user_id, $status = true, $auto = false, $listids = array()) {
        $time = time();
        $cols = false;
        // get the enabled lists
        $model_list = WYSIJA::get('list', 'model');
        $listsdata = $model_list->get(array('list_id'), array('is_enabled' => '1'));
        $enabled_list_ids = array();
        foreach ($listsdata as $listdt) {
            $enabled_list_ids[] = $listdt['list_id'];
        }

        // get list_id from user
        $model_user_list = WYSIJA::get('user_list', 'model');
        $listidsfromuser = $model_user_list->get(array('list_id'), array('user_id' => $user_id, 'list_id' => $enabled_list_ids));

        $listidsenableduser = array();
        foreach ($listidsfromuser as $listdt) {
            $listidsenableduser[] = $listdt['list_id'];
        }

        if ($status) {
            $status = 1;

            // if undo unsubscribe then we re-subscribe all the previously unsubscribed lists based on the unsub_date value
            if( !empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'undounsubscribe' ){
                // get latest time when users unsubcribe from a list
                $model_user_list->columns['MAX(`unsub_date`) as `max_unsub_date`']=array("type"=>"integer");
                $max_unsub_date = $model_user_list->get(array('MAX(`unsub_date`) as `max_unsub_date`'), array('user_id' => $user_id));
                $max_unsub_date = $max_unsub_date[0]['max_unsub_date'];
                // when somebody undo - unsubscribe, let's reset the unsub_date of all of the latest lists which users belong to
                $model_user_list->update(array('unsub_date' => 0, 'sub_date' => time()), array('user_id' => $user_id, 'unsub_date' => $max_unsub_date));
            }


            // the data  we update on the user row
            $user_updated_data = array( 'status' => $status , 'confirmed_ip' => $this->getIP() , 'confirmed_at' => time() );
        } else {
            // when we unsubscribe somebody automatically(through the bounce) we set the status to -2 instead of -1 to make the difference
            $status = -1;
            $modelU = WYSIJA::get('queue', 'model');
            $modelU->delete(array('user_id' => $user_id));

            // when somebody unsubscribe, let's set the unsub_date and reset the sub_date of all the lists which users belong to
            $model_user_list->update(array('unsub_date' => time(), 'sub_date' => 0), array('user_id' => $user_id, 'unsub_date' => 0));

            // the data  we update on the user row
            $user_updated_data = array('status' => $status, 'count_confirmations' => 0);
        }

        $modelUser = WYSIJA::get('user', 'model');
        $modelUser->update($user_updated_data, array('user_id' => $user_id));

        // if the status is subscribed then we modify the user_list status and process the autonewsletter
        if ($status) {
            // update the sub_date col in the user_list table
            if (!$auto) {
                $model_user_list = WYSIJA::get('user_list', 'model');
                $cols = array('sub_date' => $time, 'unsub_date' => 0);
                $model_user_list->update($cols, array('user_id' => $user_id, 'list_id' => $listids));
            }

            // process the auto newsletter once the status changed
            $lists = $this->getUserLists($user_id, $listids);
            $this->sendAutoNl($user_id, $lists);
        }

        return $listidsenableduser;
    }

    /**
     * check that a bot didn't stick his hand in our honey pot
     * @param type $data
     * @return boolean
     */
    function checkData(&$data) {
        if (isset($data['user']['abs'])) {
            foreach ($data['user']['abs'] as $honeyKey => $honeyVal) {
                //if honey val then robotty is out !
                if ($honeyVal)
                    return false;
            }
            unset($data['user']['abs']);
        }
        $user_keys = array('email','firstname','lastname');
        foreach($data['user'] as $keyi => $val){
            if(!in_array($keyi, $user_keys)){
                unset($data['user'][$keyi]);
            }
        }

        return true;
    }

    function isCaptchaEnabled() {
      $config = WYSIJA::get('config', 'model');
      return $config->getValue('recaptcha');
    }

    function verifyCaptcha($data) {
      $config = WYSIJA::get('config', 'model');

      if(!$this->isCaptchaEnabled()) {
        return true;
      }

      if(isset($data['g-recaptcha-response'])) {
        $response = json_decode(
          wp_remote_retrieve_body(
            wp_remote_post(
              'https://www.google.com/recaptcha/api/siteverify',
              array(
                'body' => array(
                  'secret' => $config->getValue('recaptcha_secret'),
                  'response' => $data['g-recaptcha-response']
                )
              )
            )
          ),
          true
        );

        if(!empty($response["success"])) {
          return true;
        }
      }

      $this->error( __( 'Please enter CAPTCHA correctly.' , WYSIJA ) , true);
      return false;
    }

    /**
     * function to insert subscribers into wysija
     * data parameter is a multidimensional array
     * @param array  $data=array(
     *       'user'=>array('email'=>$myuserEMAIL,'firstname'=>$myuserFIRSTNAME),
     *       'user_lists'=>array('list_ids'=>array($listid1,$listid2))
     *       );
     * @param boolean $subscribing_from_backend
     * @return type
     */
    function addSubscriber( $data , $subscribing_from_backend = false) {

        $has_error = false;
        // 0 - action before any processing call third party services WangGuard for instance
        if ( !$subscribing_from_backend ) {
            $valid_email = apply_filters('wysija_beforeAddSubscriber', true, $data['user']['email']);
            if ( !$valid_email ) {
                $this->error( __( 'The email is not valid!' , WYSIJA ), true );
                $has_error = true;
            }
        }

        // 1-check if email is valid
        if ( !$this->validEmail( $data['user']['email'] ) ) {
            $this->error( __( 'The email is not valid!' , WYSIJA ), true );
            $has_error = true;
        }

        // check if lists are specified?
        if ( empty( $data['user_list']['list_ids'] ) ) {
            $this->error( __( 'You need to select at least one list.' , WYSIJA ) , true);
            $has_error = true;
        }

        // make sure we get all the errors in one shot
        if ($has_error === true) {
            return false;
        }

        // 2-check if email doesn't exists already
        $model_user = WYSIJA::get('user', 'model');
        $subscriber_exists_already = $model_user->getOne(false, array('email' => trim($data['user']['email'])));

        $model_config = WYSIJA::get('config', 'model');
        $confirm_dbloptin = $model_config->getValue('confirm_dbleoptin');

        // message success escaping, striping, setting
        $message_success = '';
        if ( isset( $data['message_success'] ) ) {
            $message_success = strip_tags( $data['message_success'] , '<p><em><span><b><strong><i><h1><h2><h3><a><ul><ol><li><br>');
        } else if ( isset( $data['success_message'] ) ) {
            $message_success = strip_tags( nl2br(base64_decode( $data['success_message'] ) ) , '<p><em><span><b><strong><i><h1><h2><h3><a><ul><ol><li><br>');
        } else if ( isset( $data['form_id'] ) ) {
            // we have a form_id parameter so let's fetch the form success message
            $model_forms = WYSIJA::get('forms', 'model');
            $form = $model_forms->getOne( array( 'data' ) , array( 'form_id' => (int) $data['form_id'] ) );
            $form_data = unserialize( base64_decode( $form['data'] ) );

            // if the on_success action is 'message', display message
            if ( $form_data['settings']['on_success'] === 'message') {
                $message_success = nl2br( $form_data['settings']['success_message'] );
            }
        }

        if ( $subscriber_exists_already ) {
            // show a message for that case scenario in the admin panel
            if ( $subscribing_from_backend ) {
                $this->error(str_replace(array('[link]', '[/link]'), array('<a href="admin.php?page=wysija_subscribers&action=edit&id=' . $subscriber_exists_already['user_id'] . '" >', "</a>"), __('Subscriber already exists. [link]Click to edit[/link].', WYSIJA)), true);
                return false;
            }


            $model_user_list = WYSIJA::get( 'user_list' , 'model' );
            $subscribed_but_require_confirmation = false;
            if( $subscriber_exists_already['status'] == 1 ){
                $unsubscribed_lists = $model_user_list->get( array( 'list_id' ) , array( 'greater' => array( 'unsub_date' => 0 ), 'equal' => array( 'user_id' => $subscriber_exists_already['user_id'] ) ) );
                $already_unsubscribed_list_ids_formatted = array();

                foreach ($unsubscribed_lists as $user_list_detail) {
                    $already_unsubscribed_list_ids_formatted[] = $user_list_detail['list_id'];
                }

                foreach ($data['user_list']['list_ids'] as $list_id) {
                    if ( in_array( $list_id, $already_unsubscribed_list_ids_formatted ) ) {
                        $subscribed_but_require_confirmation = true;
                    }
                }
            }


            // if the status of the user is either unsubscribed or not confirmed
            // or he is unsubscribed from one of the list he is trying to subscribe again
            // we resend him the activation email
            if ( (int) $subscriber_exists_already['status'] < 1 || $subscribed_but_require_confirmation ) {
                $model_user->reset();
                $model_user->update( array( 'status' => 0 ), array( 'user_id' => $subscriber_exists_already['user_id'] ) );

                $subscribe_to_list = 0;
                if ( !$confirm_dbloptin ){
                    $subscribe_to_list = time();
                }

                $this->addToLists( $data['user_list']['list_ids'], $subscriber_exists_already['user_id'], $subscribe_to_list);

                // this is the double optin case, where we simply send the signup confirmation
                if ( $confirm_dbloptin ) {
                    $this->sendConfirmationEmail( (object) $subscriber_exists_already, true, $data['user_list']['list_ids']);
                } else {
                    // this is the single optin case, where we fire the autoresponders directly
                    $lists = $this->getUserLists( $subscriber_exists_already['user_id'], $data['user_list']['list_ids'] );
                    $this->sendAutoNl( $subscriber_exists_already['user_id'], $lists );

                    if ( $model_config->getValue( 'emails_notified' ) && $model_config->getValue( 'emails_notified_when_sub' ) ) {
                        // notify the administrators of a new subscribption
                        $this->uid = $subscriber_exists_already['user_id'];
                        if ( !$subscribing_from_backend )
                            $this->_notify( $data['user']['email'], true, $data['user_list']['list_ids'] );
                    }
                }


                if ( !empty( $message_success ) ){
                    $this->notice( $message_success );
                }

                return true;
            }

            $model_user_list = WYSIJA::get('user_list', 'model');
            $already_subscribed_list_ids = $model_user_list->get(array('list_id'), array('greater' => array('sub_date' => 0), 'equal' => array( 'user_id' => $subscriber_exists_already['user_id'] ) ) );
            $already_subscribed_list_ids_formatted = array();

            foreach ($already_subscribed_list_ids as $user_list_detail) {
                $already_subscribed_list_ids_formatted[] = $user_list_detail['list_id'];
            }

            // a confirmation needs to be resend for those lists
            $list_ids_require_confirmation = array();
            foreach ( $data['user_list']['list_ids'] as $list_id ) {
                // only the list for which the subscribe request is made and is not already subscribed too willr equire confirmation
                if ( !in_array($list_id, $already_subscribed_list_ids_formatted ) ) {
                    $list_ids_require_confirmation[] = $list_id;
                }
            }

            // this process require either a confirmation email to be sent or
            // the autoresponders to be triggerred
            if ( !empty( $list_ids_require_confirmation ) ) {
                $subscribe_to_list = $subscriber_status = 0;

                if ( isset( $data['user']['status'] ) ){
                    $subscriber_status = $data['user']['status'];
                }

                // if double optin is activated and the subscriber status is 1 (subscribed)
                // or this is single optin, then we directly subscribe the user to the list
                if ( ( $confirm_dbloptin && $subscriber_status ) || !$confirm_dbloptin ){
                    $subscribe_to_list = time();
                }

                // we can add the subscribers to the lists passed
                $this->addToLists( $data['user_list']['list_ids'] , $subscriber_exists_already['user_id'] , $subscribe_to_list );

                // send a confirmation message when double optin is on
                if ( $confirm_dbloptin ) {
                    //if we have lists that are going to be added we send a confirmation email for double optin
                    $this->sendConfirmationEmail( (object) $subscriber_exists_already, true, $list_ids_require_confirmation );
                }else{
                    // send auto nl to single optin which have lists added
                    if ( !empty( $list_ids_require_confirmation ) ) {
                        $lists = $this->getUserLists( $subscriber_exists_already['user_id'] , $data['user_list']['list_ids'] );
                        $this->sendAutoNl( $subscriber_exists_already['user_id'] , $lists );
                    }
                }
                if ( !empty( $message_success ) ){
                    $this->notice( $message_success );
                }

            } else {
                //no lists need to be added so we can simply return the message
                $this->notice(__("Oops! You're already subscribed.", WYSIJA));
                return true;
            }

            return true;
        }

        // 3-insert the subscriber with the right status based on optin status

        $subscriber_data = $data['user'];
        $subscriber_data['ip'] = $this->getIP();

        $model_user->reset();
        $user_id = $model_user->insert($subscriber_data);


        if ( (int)$user_id > 0 ) {
            // if a form id is specified, let's increment its "subscribed count"
            if (isset($data['form_id']) && (int) $data['form_id'] > 0) {
                // check if the form exists
                $model_forms = WYSIJA::get('forms', 'model');
                $form = $model_forms->getOne( array('form_id', 'subscribed') , array('form_id' => (int) $data['form_id']) );
                if ( isset( $form['form_id'] ) && (int) $form['form_id'] === (int) $data['form_id'] ) {
                    // the form exists so let's increment the "subscribed" count
                    $model_forms->update( array(
                        'subscribed' => $form['subscribed'] + 1
                            ), array(
                        'form_id' => (int) $form['form_id']
                    ));
                }
            }

            // set user profile data
            if( !empty( $data['user_field'] ) ){
                WJ_FieldHandler::handle_all( $data['user_field'], $user_id );
            }

            // display success message
            if ( !empty( $message_success ) ){
                $this->notice( $message_success );
            }

        }else {
            if ($subscribing_from_backend) {
                $this->notice(__('Subscriber has not been saved.', WYSIJA));
            } else {
                $this->notice(__('Oops! We could not add you!', WYSIJA));
            }
            return false;
        }

        $subscribe_to_list = $subscriber_status = 0;
        if ( isset( $data['user']['status'] ) ){
            $subscriber_status = $data['user']['status'];
        }

        if ( ( $confirm_dbloptin && $subscriber_status ) || !$confirm_dbloptin ){
            $subscribe_to_list = time();
        }

        //4-we add the user to the lists
        $this->addToLists( $data['user_list']['list_ids'], $user_id, $subscribe_to_list );

        // 5-send a confirmation email or add the user to the lists depending on the status
        $can_send_autoresponders = false;
        if ( $subscriber_status > -1 ) {
            if ( $confirm_dbloptin ) {
                if ( $subscriber_status == 0 ) {
                    $model_user->reset();
                    $model_user->getFormat = OBJECT;
                    $receiver = $model_user->getOne( false , array('email' => trim($data['user']['email']) ) );
                    $this->sendConfirmationEmail( $receiver, true, $data['user_list']['list_ids'] );
                } else {
                    //the subscriber status is set to subscribed so we send the auto nl straight away
                    $can_send_autoresponders = true;
                }
            } else {
                // single optin - we send a notification to the admin if settings are set
                $can_send_autoresponders = true;

                if ($model_config->getValue( 'emails_notified' ) && $model_config->getValue( 'emails_notified_when_sub' ) ) {
                    $this->uid = $user_id;
                    if (!$subscribing_from_backend){
                        $this->_notify( $data['user']['email'] , true, $data['user_list']['list_ids'] );
                    }

                }
            }

            // let's send the autoresponders
            if ( $can_send_autoresponders ) {
                $lists = $this->getUserLists( $user_id, $data['user_list']['list_ids'] );
                $this->sendAutoNl( $user_id, $lists, 'subs-2-nl', $subscribing_from_backend );
            }
        }

        return $user_id;
    }

    function throttleRepeatedSubscriptions() {
      $model_subscriber_ip = WYSIJA::get('subscriber_ip', 'model');

      $subscription_limit_enabled = apply_filters('wysija_subscription_limit_enabled', true);

      $subscription_limit_window = apply_filters('wysija_subscription_limit_window', DAY_IN_SECONDS);
      $subscription_limit_base = apply_filters('wysija_subscription_limit_base', MINUTE_IN_SECONDS);

      $subscriber_ip = $this->getIPForThrottling();

      if ($subscription_limit_enabled && !is_user_logged_in()) {
        if (!empty($subscriber_ip)) {
          $subscription_count = $model_subscriber_ip->query(
            'get_row',
            'SELECT COUNT(*) as row_count FROM ' . $model_subscriber_ip->getSelectTableName() . '
             WHERE `ip` = "' . $subscriber_ip . '" AND `created_at` >= NOW() - INTERVAL ' . (int) $subscription_limit_window . ' SECOND'
          );

          if (isset($subscription_count['row_count']) && $subscription_count['row_count'] > 0) {
            $timeout = $subscription_limit_base * pow(2, $subscription_count['row_count'] - 1);
            $existing_user = $model_subscriber_ip->query(
              'get_row',
              'SELECT COUNT(*) as row_count
               FROM ' . $model_subscriber_ip->getSelectTableName() . '
               WHERE `ip` = "' . $subscriber_ip . '" AND `created_at` >= NOW() - INTERVAL ' . (int) $timeout . ' SECOND LIMIT 1'
            );
            if (!empty($existing_user['row_count'])) {
              $this->error( sprintf(__( 'You need to wait %s seconds before subscribing again.' , WYSIJA ), $timeout) , true);
              return false;
            }
          }
        }
      }

      // Purge old IP addresses
      $model_subscriber_ip->query(
        'DELETE FROM ' . $model_subscriber_ip->getSelectTableName() . '
        WHERE `created_at` < NOW() - INTERVAL ' . (int) $subscription_limit_window . ' SECOND'
      );

      return true;
    }

    function storeSubscriberIP() {
      global $wpdb;

      $model_subscriber_ip = WYSIJA::get('subscriber_ip', 'model');
      $subscriber_ip = $this->getIPForThrottling();

      if (!empty($subscriber_ip)) {
        $wpdb->insert(
          $model_subscriber_ip->getSelectTableName(),
          array('ip' => $subscriber_ip)
        );
      }
    }

    /**
     * send auto nl based on the params passed
     * @staticvar type $emails
     * @param int $user_id
     * @param mixed $params_based_on_type the params which are needed for this type of newsletter
     * @param string $process_type the type of auto newsletter that needs to be processed
     * @param boolean $added_by_admin to make the distinction between subscribers added through the admin interface or not
     * @return boolean
     */
    function sendAutoNl($user_id, $params_based_on_type = false, $process_type = 'subs-2-nl', $added_by_admin = false) {

        // check that the user we're trying to send the newsletter to has the right status
        $modelUser=WYSIJA::get('user','model');
        $modelC=WYSIJA::get('config','model');
        $dbloptin=(int)$modelC->getValue('confirm_dbleoptin');

        // we send an autonl if the subscriber is not added to the list by the admin and the user's status is greater or equal to 0 or 1 (if a user switch from double optin to single optin the greater or equal suddenly make sense)
        if(!$added_by_admin && !$modelUser->exists( array( 'equal' => array( 'user_id' => $user_id ), 'greater_eq' => array( 'status' => $dbloptin )))){
            return false;
        }

        $userListss = array();
        if($dbloptin && !$added_by_admin){
            $modelUserList=WYSIJA::get('user_list','model');
            $userListssres=$modelUserList->get(array('list_id'),array('equal'=>array('user_id'=>$user_id),'greater'=>array('sub_date'=>0)));
            foreach($userListssres as $res){
                $userListss[]=$res['list_id'];
            }
        }elseif(!$dbloptin){
            // in the case where double optin is off we're filling an array of lists from which the subscriber could receive autoresponders
            $modelUserList = WYSIJA::get('user_list','model');
            $userListssres = $modelUserList->get(array('list_id'),array('equal'=>array('user_id'=>$user_id)));
            foreach($userListssres as $res){
                $userListss[] = $res['list_id'];
            }
        }

        // loading active automatic emails
        static $emails;
        if (empty($emails)) {
            $modelEmail = WYSIJA::get('email', 'model');
            $modelEmail->reset();
            $emails = $modelEmail->get(false, array('type' => 2, 'status' => array(1, 3, 99)));
            if (is_object($emails)) {
                $emailarr = null;
                foreach ($emails as $keyob => $valobj) {
                    $emailarr[$keyob] = $valobj;
                }
                $emails = $emailarr;
            }
            if (is_array($emails) && isset($emails['body']))
                $emails = array($emails);
        }


        foreach ($emails as $key => $email) {
            if ($email['params']['autonl']['event'] != $process_type)
                continue;
            switch ($process_type) {
                case 'subs-2-nl':
                    // extra params in that case is an array of lists
                    foreach ($params_based_on_type as $details) {

                        if (isset($email['params']['autonl']['subscribetolist']) && isset($details['list_id']) && $email['params']['autonl']['subscribetolist'] == $details['list_id']) {
                            $ok = true;
                            if (!$added_by_admin && $dbloptin && !in_array($details['list_id'], $userListss)) {
                                // make sure the list has sub_date
                                $ok = false;
                            }

                            if ($ok)
                                $this->insertAutoQueue($user_id, $email['email_id'], $email['params']['autonl']);
                        }
                    }

                    break;
                case 'new-user':
                    // extra params in that case is a user object
                    $okInsert = false;
                    switch ($email['params']['autonl']['roles']) {
                        case 'any':
                            $okInsert = true;
                            break;
                        default:
                            foreach ($params_based_on_type->roles as $rolename) {
                                if ($rolename == $email['params']['autonl']['roles'])
                                    $okInsert = true;
                            }

                            break;
                    }
                    if ($okInsert)
                        $this->insertAutoQueue($user_id, $email['email_id'], $email['params']['autonl']);
                    break;
            }
        }
    }

    /**
     * this is used when we want to send the email immediately after inserting it into the queue
     * @param int $user_id
     * @param int $email_id
     * @param array $email_params_autonl
     * @return void
     */
    function insertAutoQueue($user_id, $email_id, $email_params_autonl) {
        $model_queue = WYSIJA::get('queue', 'model');
        $queueData = array('priority' => '-1', 'email_id' => $email_id, 'user_id' => $user_id);

        $delay = $model_queue->calculate_delay($email_params_autonl);

        $queueData['send_at'] = time() + $delay;

        // if the email is already queued for that autoresponder, we don't queue it
        if(!$model_queue->exists(array('email_id'=>$email_id,'user_id'=>$user_id))){
            // if the email has already been sent and is now in the stat table, we don't queue it either
            $modelEUS = WYSIJA::get('email_user_stat','model');
            if(!$modelEUS->exists(array('email_id'=>$email_id,'user_id'=>$user_id))){
                $model_queue->insert($queueData,true);
            }

        }

        //if delay is = to 0 then we need to try to send the email straight away
        if ($delay == 0) {
            $queueH = WYSIJA::get('queue', 'helper');
            $queueH->report = false;
            WYSIJA::log('insertAutoQueue queue process', array('email_id' => $email_id, 'user_id' => $user_id), 'queue_process');
            $queueH->process($email_id, $user_id);
        }
    }

    /**
     * unsubscribe a user
     * @param type $user_id
     * @param type $auto
     * @return type
     */
    function unsubscribe($user_id, $auto = false) {
        return $this->subscribe($user_id, false, $auto);
    }

    /**
     * Undo the action "unsubscribe" by a mistake
     * @param type $user_id
     * @param type $auto
     */
    function undounsubscribe($user_id, $auto = false) {
        return $this->subscribe($user_id, true, $auto);
    }

    /**
     * send confirmation email
     * @param type $user_ids
     * @param type $sendone
     * @param type $listids
     * @return boolean
     */
    function sendConfirmationEmail($user_ids, $sendone = false, $listids = array()) {
        if ($this->no_confirmation_email === true)
            return;

        if ($sendone || is_object($user_ids)) {
            /* in this case user_ids is just one user object */
            $users = array($user_ids);
        } else {
            if (!is_array($user_ids)) {
                $user_ids = (array) $user_ids;
            }
            /* get users objects */
            $modelU = WYSIJA::get('user', 'model');
            $modelU->getFormat = OBJECT_K;
            $users = $modelU->get(false, array('equal' => array('user_id' => $user_ids, 'status' => 0)));
        }

        $config = WYSIJA::get('config', 'model');
        $mailer = WYSIJA::get('mailer', 'helper');

        //check if there are lists
        if ($listids) {
            $mailer->listids = $listids;
            $mList = WYSIJA::get('list', 'model');
            $listnamesarray = $mList->get(array('name'), array('list_id' => $listids));
            $arrayNames = array();
            foreach ($listnamesarray as $detailname) {
                $arrayNames[] = $detailname['name'];
            }
            $mailer->listnames = $arrayNames;
        }

        //load confirmation email, and if it doesn't exists create a new one
        $mEmail = WYSIJA::get('email', 'model');
        $mEmail->getFormat = OBJECT;
        $emailConfirmationData = $mEmail->getOne(false, array('email_id' => $config->getValue('confirm_email_id')));

        if (empty($emailConfirmationData)) {
            //somehow the confirmation email has been lost so we need to create a new one
            $dataEmailCon = array('from_name' => $config->getValue('from_name'), 'from_email' => $config->getValue('from_email'),
                'replyto_name' => $config->getValue('replyto_name'), 'replyto_email' => $config->getValue('replyto_email'),
                'subject' => $config->getValue('confirm_email_title'), 'body' => $config->getValue('confirm_email_body'), 'type' => '0', 'status' => '99');

            $confemailid = $mEmail->insert($dataEmailCon);
            if ($confemailid)
                $config->save(array('confirm_email_id' => $confemailid));

            $mEmail->reset();

            $mEmail->getFormat = OBJECT;
            $emailConfirmationData = $mEmail->getOne(false, array('email_id' => $config->getValue('confirm_email_id')));
        }

		$result_send = false;
        foreach ($users as $userObj) {
            $result_send = $mailer->sendOne($emailConfirmationData, $userObj, true);
        }

        if (!$sendone) {
			if (count($users) <= 0) {
				$this->notice(__('No email sent.',WYSIJA));
			} else {
                                $this->notice( _n( 'One email has been sent.', '%d emails have been sent to unconfirmed subscribers.', count($users), WYSIJA ) );
            }
			return true;
		}
        else
            return $result_send;
    }

    /**
     * Get all unconfirmed subscribers.
     * @param
     * @return Array $unconfirmed_ids Array with all the IDs of unconfirmed subscribers.
     */
    function get_unconfirmed_subscribers() {

        // Get all unconfirmed users ids.
        $model_user = WYSIJA::get('user', 'model');
        $query = 'SELECT user_id
                  FROM ' . '[wysija]' . $model_user->table_name . '
                  WHERE  status = 0';
        $result = $model_user->query('get_res', $query);

        // Convert the array to a simple ids array.
        $unconfirmed_subscribers = array();
        foreach ($result as $unconfirmed_user) {
            $unconfirmed_subscribers[] = $unconfirmed_user['user_id'];
        }

        return $unconfirmed_subscribers;
    }

    /**
     * bulk delete users
     * @param type $user_ids
     * @param type $sendone
     * @return boolean
     */
    function delete($user_ids, $sendone = false, $is_batch_select = false) {
        $list_user_ids = null; // list of user ids to query, in a list or sub-query
        $user_model = WYSIJA::get('user', 'model');
        if ($sendone) {
            // actually, this case is not implemeted yet
            // in this case user_ids is just one user object
            // $users=array($user_ids);
            exit('Not implemented! Please contact mailpoet.com');
        } elseif ($is_batch_select) {
            // in this case $users_ids = an associated array('query', 'from', 'select', ...etc)
            $list_user_ids = $user_ids['query'];
        } else {
            if (!is_array($user_ids)) {
                $user_ids = (array) $user_ids;
            }
            $list_user_ids = implode(',', $user_ids);
        }
        // table queue to delete
        $tables = array(
            'user_history',
            'email_user_url',
            'email_user_stat',
            'user_list',
            'queue',
            'user'
        );
        try {
            foreach ($tables as $table_name) {
                $query = 'DELETE FROM [wysija]' . $table_name . ' WHERE user_id IN (' . $list_user_ids . ')';
                $user_model->query($query);
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * add many subsribers to one list
     * @param int $list_id
     * @param int $user_ids
     * @return boolean
     */
    function addToList($list_id, $user_ids, $is_batch_select = false) {
        $model_user = WYSIJA::get('user', 'model');
        $sub_date = time();
        if (!$is_batch_select) {
            $total = count($user_ids);
            $list_user_ids = implode(',', $user_ids);
            $query = 'REPLACE INTO `[wysija]user_list` (`list_id`,`user_id`,`sub_date`) VALUES ';
            foreach ($user_ids as $key => $uid) {
                $query .= '(' . (int) $list_id . ',' . (int) $uid . ',' . $sub_date . ")\n";
                if ($total > ($key + 1))
                    $query.=',';
            }
        }else {
            $list_user_ids = $user_ids['query'];
            $query = 'REPLACE INTO `[wysija]user_list` (`list_id`,`user_id`,`sub_date`)
                            SELECT ' . $list_id . ' AS `list_id`, `user_id` AS `user_id`, ' . $sub_date . ' AS `sub_date` ' . $user_ids['from'];
        }
        return $model_user->query($query);
    }

    /**
     * add one subscribers to some lists
     * @param array $list_ids
     * @param int $user_id
     * @param int $subscribed_at
     * @return boolean
     */
    function addToLists($list_ids, $user_id, $subscribed_at = 0) {

        if (empty($list_ids)) {
            // don't add subscriber to lists if no list ids specified
            return false;
        }

        $modelUser = WYSIJA::get('user', 'model');
        $extraFieldsName = $extraFieldsVal = '';
        if ($subscribed_at) {
            $extraFieldsName = ',`sub_date`';
            $extraFieldsVal = ',' . $subscribed_at;
        }
        $query = 'INSERT IGNORE INTO `[wysija]user_list` (`list_id`,`user_id`' . $extraFieldsName . ')';
        $query.=' VALUES ';
        $total = count($list_ids);
        foreach ($list_ids as $key => $listid) {
            $query.='(' . (int) $listid . ',' . (int) $user_id . $extraFieldsVal . ")\n";
            if ($total > ($key + 1))
                $query.=',';
        }

        return $modelUser->query($query);
    }

    /**
     * Move bulk users to a list
     * @param int $listid
     * @param array $userids = array(int,int,int,...) OR array('where'=>'', 'from'=>'', 'select'=>'', 'query'=>'', 'row_counts_query'=>'')
     * @return type
     */
    function moveToList($listid, $userids, $is_batch_select = false) {
        //1. Remove from all existing list
        $frozen_list = $this->_getHiddenLists();
        if (empty($frozen_list))
            $frozen_list_where = '';
        else
            $frozen_list_where = ' AND `list_id` NOT IN (' . implode(',', $frozen_list) . ')';
        if (!$is_batch_select)
            $userids_query = implode(',', $userids);
        else
            $userids_query = $userids['query'];
        $query1 = 'DELETE FROM `[wysija]user_list` WHERE `user_id` IN (' . $userids_query . ')' . $frozen_list_where;

        //2. Add to the new list
        $sub_date = time();
        if (!$is_batch_select) {
            $records = array();
            foreach ($userids as $key => $uid) {
                $records[] = implode(',', array($listid, $uid, $sub_date));
            }
            $values = '(' . implode('),(', $records) . ');';
            $query2 = 'INSERT INTO `[wysija]user_list` (`list_id`,`user_id`,`sub_date`) VALUES ' . $values;
        } else {
            $query2 = 'INSERT INTO `[wysija]user_list` (`list_id`,`user_id`,`sub_date`)
                            SELECT ' . $listid . ' AS `list_id`, `user_id` AS `user_id`, ' . $sub_date . ' AS `sub_date` ' . $userids['from'];
        }

        //3. Execute
        $model_user = WYSIJA::get('user', 'model');
        $model_user->query($query1);
        $model_user->query($query2);
        return true;
    }

    /**
     * Remove user(s) from list(s)
     * @param array $listids
     * @param array $userids
     * @return boolean
     */
    function removeFromLists($listids = array(), $userids = array(), $is_batch_select = false) {
        if (empty($userids) || (empty($listids) && empty($userids)))
            return true;
        if ($is_batch_select)
            $list_userids = $userids['query'];
        else
            $list_userids = implode(',', $userids);

        $frozen_list = $this->_getHiddenLists();
        if (empty($frozen_list)) {
            $frozen_list_where = '';
        } else {
            $frozen_list_where = ' AND `list_id` NOT IN (' . implode(',', $frozen_list) . ')';
        }

        if (empty($listids)) {//remove from all lists
            $query = 'DELETE FROM `[wysija]user_list` WHERE `user_id` IN (' . $list_userids . ')';
        } else { // remove from specific lists
            $list_listids = implode(',', $listids);
            $query = 'DELETE FROM `[wysija]user_list` WHERE `user_id` IN (' . $list_userids . ') AND `list_id` IN (' . $list_listids . ')';
        }
        $query .= $frozen_list_where;

        //Execute
        $model_user = WYSIJA::get('user', 'model');
        return $model_user->query($query);
    }

    /**
     * Bulk confirm user(s)
     * @param array $userids
     * @return boolean
     */
    function confirmUsers($userids = array(), $is_batch_select = false) {
        if (empty($userids))
            return true;

        $model_user = WYSIJA::get('user', 'model');
        if ($is_batch_select) {
            $row_count = $userids['count'];
            $list_userids = $userids['query'];
        } else {
            $list_userids = implode(',', $userids);
            $row_count = !empty($_REQUEST['wysija']['user']['user_id']) ? count($_REQUEST['wysija']['user']['user_id']) : 0;
        }
        $query1 = 'UPDATE `[wysija]user` SET status = 1 WHERE `user_id` IN (' . $list_userids . ')';
        $query2 = 'UPDATE `[wysija]user_list` SET sub_date  = ' . time() . ', unsub_date = 0  WHERE `user_id` IN (' . $list_userids . ')';

        //Execute
        $model_user->query($query1);
        $model_user->query($query2);
        return $row_count;
    }

    /**
     * Get hidden lists (typically Wordpress user list)
     * @return array(int, int)
     */
    function _getHiddenLists() {
        $result = WYSIJA::get('user', 'model')->getResults('SELECT GROUP_CONCAT(`list_id`) as ids FROM `[wysija]list` WHERE `is_enabled` = 0');
        return $result[0]['ids'] ? explode(',', $result[0]['ids']) : array();
    }

    /**
     * send Admin notification about new subscriber or new unsubscribed user
     * @param type $email
     * @param type $subscribed
     * @param type $list_ids
     */
    function _notify($email, $subscribed = true, $list_ids = false) {
        // get the public list to which user is subscribed
        $model_user = WYSIJA::get('user_list', 'model');

        if ($list_ids) {
            $query = "Select B.name from `[wysija]list` as B where B.list_id IN ('" . implode("','", $list_ids) . "') and B.is_enabled>0";
        } else {
            $query = 'Select B.name from `[wysija]user_list` as A join `[wysija]list` as B on A.list_id=B.list_id where A.user_id=' . $this->uid . ' and B.is_enabled>0';
        }

        $result = $model_user->query('get_res', $query);
        $list_names = array();
        foreach ($result as $arra) {
            $list_names[] = $arra['name'];
        }

        if ($subscribed) {
            $title = sprintf(__('New subscriber to %1$s', WYSIJA), implode(',', $list_names));
            $body = sprintf(__("Howdy,\n\n The subscriber %1\$s has just subscribed to your list '%2\$s' \n\n Cheers,\n\n The MailPoet Plugin", WYSIJA), "<strong>" . $email . "</strong>", "<strong>" . implode(',', $list_names) . "</strong>");
        } else {
            $title = sprintf(__('One less subscriber to %1$s', WYSIJA), implode(',', $list_names));
            $body = sprintf(__("Howdy,\n\n The subscriber %1\$s has just unsubscribed to your list '%2\$s' \n\n Cheers,\n\n The MailPoet Plugin", WYSIJA), "<strong>" . $email . "</strong>", "<strong>" . implode(',', $list_names) . "</strong>");
        }

        $model_config = WYSIJA::get('config', 'model');
        $mailer = WYSIJA::get('mailer', 'helper');
        $notifieds = $model_config->getValue('emails_notified');
        //$mailer->report=false;
        $notifieds = explode(',', $notifieds);
        $mailer->testemail = true;
        $body = nl2br($body);
        foreach ($notifieds as $receiver) {
            $mailer->sendSimple(trim($receiver), $title, $body);
        }
    }

    /**
     * refresh all related info of users
     * @return boolean
     */
    function refreshUsers() {
        $model_user = WYSIJA::get('user', 'model');
        $model_user->reset();
        $model_config = WYSIJA::get('config', 'model');
        if ($model_config->getValue('confirm_dbleoptin')) {
            $model_user->setConditions(array('greater' => array('status' => 0)));
        } else {
            $model_user->setConditions(array('greater' => array('status' => -1)));
        }

        $count = $model_user->count();
        $model_config->save(array('total_subscribers' => $count));
        return true;
    }

    /**
     * Regenerate domains based on users' emails
     */
    public function generate_domains() {
        $query = "UPDATE [wysija]user SET `domain` = SUBSTRING(`email`,LOCATE('@',`email`)+1) WHERE ISNULL(`domain`)";
        $model_user = WYSIJA::get('user', 'model');
        return $model_user->query($query);
    }

    /**
     * function used to update the synchronisation with a plugin list or WP's one table
     * @global object $wpdb
     * @param int $list_id
     * @param boolean $synch_all_wp_user_base means complete synch of the whole user base, it's used for multisite and the WordPress users list
     * @return type
     */
    function synchList($list_id, $synch_all_wp_user_base = false) {
        $model = WYSIJA::get('list', 'model');
        $data = $model->getOne(false, array('list_id' => (int) $list_id, 'is_enabled' => '0'));

        if ($data) {

            if (strpos($data['namekey'], '-listimported-') !== false) {
                // plugins list table synch
                $model->reset();

                // import synch list
                $listdata = explode('-listimported-', $data['namekey']);
                $dataMainList = $model->getOne(false, array('namekey' => $listdata[0], 'is_enabled' => '0'));

                $importHelper = WYSIJA::get('plugins_import', 'helper');
                $connection_info = $importHelper->getPluginsInfo($listdata[0]);
                $lists_ids = array(
                    'wysija_list_main_id' => $dataMainList['list_id'],
                    'wysija_list_id' => $data['list_id'],
                    'plug_list_id' => $listdata[1]
                );
                $importHelper->import($listdata[0], $connection_info, false, false, $lists_ids);
            } elseif ($data['namekey'] == 'users') {
                // wordpress user table synch
                //importwp
                $ismainsite = true;
                if (is_multisite()) {
                    global $wpdb;
                    if ($wpdb->prefix != $wpdb->base_prefix) {
                        $ismainsite = false;
                    }
                }

                $connection_info = array('name' => 'WordPress',
                    'pk' => 'ID',
                    'matches' => array('ID' => 'wpuser_id', 'user_email' => 'email', 'first_name' => 'firstname', 'last_name' => 'lastname'),
                    'matchesvar' => array('status' => 1));

                $importHelper = WYSIJA::get('plugins_import', 'helper');
                $lists_ids = array(
                    'wysija_list_main_id' => $data['list_id']
                );

                $importHelper->import($data['namekey'], $connection_info, false, $synch_all_wp_user_base, $lists_ids);

                $this->cleanWordpressUsersList();
            } elseif (strpos($data['namekey'], 'query-') !== false) {
                // special query look for an action to run the refresh
                //TODO maybe we don't need to create a list that we sync but simply need a way to make a filter
                $queryUid = apply_filters('wysija_synch_' . str_replace('-', '_', $data['namekey']));
                $importHelper = WYSIJA::get('plugins_import', 'helper');
                $queryUid = str_replace(array('[list_id]', '[created_at]'), array($data['list_id'], time()), $queryUid);
                $importHelper->insertUserList(0, 0, 0, $queryUid);
            } else {
                // plugins table synch

                $config = WYSIJA::get('config', 'model');
                $importables = $config->getValue('pluginsImportableEgg');

                if (in_array($data['namekey'], array_keys($importables))) {
                    $importHelper = WYSIJA::get('plugins_import', 'helper');
                    $dataMainList = $model->getOne(false, array('namekey' => $data['namekey'], 'is_enabled' => '0'));

                    $importHelper->import($data['namekey'], $importHelper->getPluginsInfo($data['namekey']), false, false, array('wysija_list_main_id' => $dataMainList['list_id']));
                }
            }
            $this->notice(sprintf(__('List "%1$s" has been synchronised.', WYSIJA), $data['name']));
            return true;
        } else {
            $this->error(__('The list does not exists or cannot be synched.', WYSIJA), true);
            return false;
        }
    }

    /**
     * function used to cleanup the lost wpuser_id
     * @return boolean
     */
    function cleanWordpressUsersList() {
        // update the screwed up wpuser_id
        $model_list = WYSIJA::get('list', 'model');
        $query = 'UPDATE [wysija]user as A JOIN [wp]users as B on A.email=B.user_email SET A.wpuser_id = B.ID WHERE A.wpuser_id = 0';
        $model_list->query($query);

        // get all the wysija user with a wpuser_id >0 and insert them into the WordPress user list
        $model_config = WYSIJA::get('config', 'model');
        $selectuserCreated = 'SELECT [wysija]user.user_id, ' . $model_config->getValue('importwp_list_id') . ', ' . time() . ' FROM [wysija]user WHERE wpuser_id > 0';
        $query = 'INSERT IGNORE INTO `[wysija]user_list` (`user_id`,`list_id`,`sub_date`) ' . $selectuserCreated;
        $model_list->reset();
        $model_list->query($query);
        return true;
    }

    /**
     * get an IP for the current visitor/user
     * @return string IP address
     */
    function getIP() {
        $ip = '';

        // cloudFlare IP check
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = strip_tags($_SERVER['HTTP_CF_CONNECTING_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) AND strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) AND strlen($_SERVER['HTTP_CLIENT_IP']) > 6) {
            $ip = strip_tags($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR']) > 6) {
            $ip = strip_tags($_SERVER['REMOTE_ADDR']);
        }//endif
        if (empty($ip))
            $ip = '127.0.0.1';
        return strip_tags($ip);
    }

    // More secure IP check
    function getIPForThrottling() {
        $ip = '';

        if (!empty($_SERVER['REMOTE_ADDR']) AND strlen($_SERVER['REMOTE_ADDR']) > 6) {
            $ip = strip_tags($_SERVER['REMOTE_ADDR']);
        }//endif

        return $ip;
    }

    /**
     * verify that an email string is valid
     * @param string $email
     * @return boolean
     */
    function validEmail($email) {
        //1 - check if the email parameter looks like an email
        if (empty($email) || !is_string($email) || strpos($email, '@') === false)
            return false;

        //2 - pregmatch the email
        $check_domain = false;
        $preg_check = true;
        $string_special_chars = '\æ\ø\å\ä\ö\ü';
        $email_pattern = '/^([a-z0-9_\'&\.\-\+' . $string_special_chars . '])+\@(([a-z0-9\-' . $string_special_chars . '])+\.)+([a-z0-9\-' . $string_special_chars . ']{2,10})+$/i';
        //$string_special_chars = '';
        $model_config = WYSIJA::get('config', 'model');
        if ($model_config->getValue('email_cyrillic')) {
            // check domain
            $preg_check = false;
        }

        if ($model_config->getValue('check_domain')) {
            // check domain
            $check_domain = true;
        }

        if ($preg_check) {
            if (!preg_match($email_pattern, $email))
                return false;
        }


        if ($check_domain) {
            //3 - make sure the domain of the email exists
            $helper_toolbox = WYSIJA::get('toolbox', 'helper');

            if (!$helper_toolbox->check_email_domain($email))
                return false;
        }
        return true;
    }

    /**
     * make sure that the request wysija-key parameter correspond to a user in the db
     * @return boolean
     */
    function checkUserKey($user_id = false) {

        // the !is numeric condition is here because if we input wysija-key=3 in the url
        // our model will search keyuser=3 which will return the result of the user with key user 3b61ac508456ab6ee7594c47cddb86a5
        if((!empty($_REQUEST['wysija-key']) && !is_numeric($_REQUEST['wysija-key'])) || $user_id !==false){
            $modelUser=WYSIJA::get('user','model');

            if ($user_id === false) {
                $where_condition = array('keyuser' => $_REQUEST['wysija-key']);
            } else {
                $where_condition = array('user_id' => $user_id);
            }

            $result = $modelUser->getDetails($where_condition);
            if ($result) {
                return $result;
            } else {
                $this->error(__('Page is not accessible.', WYSIJA), true);
                return false;
            }
        } else {
            $this->error(__('Page is not accessible.', WYSIJA), true);
            return false;
        }
    }

    /**
     * get all of the lists a user has subscribed to
     * @param int $user_id
     * @param array $list_ids
     * @return array results
     */
    function getUserLists($user_id, $list_ids = array()) {
        $model_user = WYSIJA::get('user', 'model');
        $list_id_in = '';
        $clean_ids = array();
        foreach ($list_ids as $id) {
          $clean_ids[] = (int)$id;
        }
        if (!empty($clean_ids)) {
          $list_id_in = "AND A.list_id IN(" . implode(",", $clean_ids) . ")";
        }
        $query = 'SELECT A.* FROM [wysija]user_list as A LEFT JOIN [wysija]list as B on A.list_id=B.list_id WHERE A.user_id=' . (int)$user_id . ' AND B.is_enabled=1 ' . $list_id_in;
        return $model_user->getResults($query);
    }

    /**
     * if we confirm the user manually we pass it a user_id otherwise it works with a global key
     * @param type $user_id
     * @return boolean
     */
    function confirm_user($user_id = false) {
        $model_config = WYSIJA::get('config', 'model');
        // we need to call the translation otherwise it will not be loaded and translated
        $model_config->add_translated_default();

        $list_ids = array();
        if ( isset( $_REQUEST['wysiconf'] ) ){
            $list_ids = json_decode( base64_decode( $_REQUEST['wysiconf'] ), true );
        }

        if(empty( $list_ids ) || !is_array( $list_ids )){
            $this->title = __('Your confirmation link expired, please subscribe again.', WYSIJA);
            return;
        }

        // START part linked to the view
        $this->title = $model_config->getValue( 'subscribed_title' );
        if ( !empty( $list_ids ) && is_array( $list_ids ) ) {
            $model_list = WYSIJA::get('list', 'model');
            $lists_names_res = $model_list->get(array('name'), array('list_id' => $list_ids));
            $names = array();
            foreach ($lists_names_res as $nameob){
                $names[] = $nameob['name'];
            }


            if (!isset($model_config->values['subscribed_title'])){
                $this->title = __('You\'ve subscribed to: %1$s', WYSIJA);
            }

            $this->title = sprintf($this->title, implode(', ', $names));
        }

        $this->subtitle = $model_config->getValue('subscribed_subtitle');
        if ( !isset( $model_config->values['subscribed_subtitle'] ) ){
            $this->subtitle = __("Yup, we've added you to our list. You'll hear from us shortly.", WYSIJA);
        }
        // END part linked to the view

        $user_data = $this->checkUserKey( $user_id );

        if ( $user_data ) {
            //user is not confirmed yet
            $model_config = WYSIJA::get('config', 'model');

            if ( (int) $user_data['details']['status'] < 1) {
                // let's confirm the subscriber
                $this->subscribe( $user_data['details']['user_id'], true, false, $list_ids );
                $this->uid = $user_data['details']['user_id'];

                // send a notification to the email specified in the settings if required to
                if ( $model_config->getValue( 'emails_notified' ) && $model_config->getValue( 'emails_notified_when_sub' ) ) {
                    $this->_notify( $user_data['details']['email'] );
                }
                return true;
            } else {
                if ( isset( $_REQUEST['wysiconf'] ) ) {
                    $needs_subscription = false;
                    foreach ( $user_data['lists'] as $list ) {
                        if ( in_array( $list['list_id'], $list_ids ) && (int) $list['sub_date'] < 1) {
                            $needs_subscription = true;
                        }
                    }

                    if ( $needs_subscription ) {

                        $this->subscribe( $user_data['details']['user_id'] , true, false, $list_ids );
                        $this->title = sprintf( $model_config->getValue('subscribed_title') , implode( ', ', $names ) );
                        $this->subtitle = $model_config->getValue( 'subscribed_subtitle' );
                        // send a notification to the email specified in the settings if required to
                        if ( $model_config->getValue( 'emails_notified' ) && $model_config->getValue( 'emails_notified_when_sub' ) ) {
                            $this->_notify( $user_data['details']['email'] , true , $list_ids );
                        }
                    } else {
                        $this->title = sprintf( __('You are already subscribed to : %1$s' , WYSIJA ) , implode( ', ', $names ) );
                    }
                } else {
                    $this->title = __( 'You are already subscribed.' , WYSIJA );
                }
                return true;
            }
        }
    }
}
