<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_help_bounce extends WYSIJA_help {

    var $report = false;
    var $config;
    var $mailer;
    var $mailbox;
    var $_message;
    var $listsubClass;
    var $subClass;
    var $db;
    var $deletedUsers = array();
    var $unsubscribedUsers = array();
    var $addtolistUsers = array();
    var $bounceMessages = array();
    var $listdetails = array();
    var $usepear = false;
    var $detectEmail = '/[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@([a-z0-9\-]+\.)+[a-z0-9]{2,8}/i';
    var $messages = array();
    var $record_ms_bounce = false; // only used in multisite scenario

    function __construct() {
        $this->config = WYSIJA::get('config', 'model');
        $this->mailer = WYSIJA::get('mailer', 'helper');
        $this->rulesClass = WYSIJA::get('rules', 'helper');
        $this->mailer->report = false;
        $this->subClass = WYSIJA::get('user', 'model'); //acymailing_get('class.subscriber');
        $this->listsubClass = WYSIJA::get('user_list', 'model'); //acymailing_get('class.listsub');
        $this->listsubClass->checkAccess = false;
        $this->listsubClass->sendNotif = false;
        $this->listsubClass->sendConf = false;
        $this->historyClass = WYSIJA::get('user_history', 'model');
    }

    function init($config = false) {
        if ($config) {
            //unset($this->config->values);
            foreach ($config as $key => $val)
                $this->config->values[$key] = $val;
        }
        if ($this->config->getValue('bounce_connection_method') == 'pear') {
            $this->usepear = true;
            include_once(WYSIJA_INC . 'pear' . DS . 'pop3.php');
            return true;
        }

        if (extension_loaded('imap') OR function_exists('imap_open'))
            return true;

        $prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
        $EXTENSION = $prefix . 'imap.' . PHP_SHLIB_SUFFIX;

        if (function_exists('dl')) {
            //We will try to load it on the fly
            $fatalMessage = 'The system tried to load dynamically the ' . $EXTENSION . ' extension';
            $fatalMessage .= '<br/>If you see this message, that means the system could not load this PHP extension';
            $fatalMessage .= '<br/>Please enable the PHP Extension ' . $EXTENSION;
            ob_start();
            echo $fatalMessage;
            //This method could cause a fatal error, but we will still display some messages in that case.
            dl($EXTENSION);
            $warnings = str_replace($fatalMessage, '', ob_get_clean());
            if (extension_loaded('imap') OR function_exists('imap_open'))
                return true;
        }

        if ($this->report) {
            $this->error('The extension "' . $EXTENSION . '" could not be loaded, please change your PHP configuration to enable it or use the pop3 method without imap extension', true);
            if (!empty($warnings))
                $this->error($warnings, true);
        }

        return false;
    }

    function connect() {
        if ($this->usepear)
            return $this->_connectpear();
        return $this->_connectimap();
    }

    function _connectpear() {
        ob_start();
        $this->mailbox = new Net_POP3();

        $timeout = $this->config->getValue('bounce_timeout');
        if (!empty($timeout))
            $this->mailbox->setTimeOut($timeout);

        $port = intval($this->config->getValue('bounce_port', ''));
        if (empty($port))
            $port = '110/pop3/notls';

        $serverName = $this->config->getValue('bounce_host');
        $secure = $this->config->getValue('bounce_connection_secure', '');
        //We don't add back the ssl:// or tls:// if it's already there
        if (!empty($secure) AND !strpos($serverName, '://'))
            $serverName = $secure . '://' . $serverName;

        if (!$this->mailbox->connect($serverName, $port)) {
            $warnings = ob_get_clean();
            if ($this->report) {
                $this->error('Error connecting to the server ' . $this->config->getValue('bounce_host') . ' : ' . $port, true);
                return false;
            }
            if (!empty($warnings) AND $this->report)
                $this->error($warnings, true);
            return false;
        }

        $login = $this->mailbox->login(trim($this->config->getValue('bounce_login')), trim($this->config->getValue('bounce_password')), 'USER');
        if (empty($login) OR isset($login->code)) {
            $warnings = ob_get_clean();
            if ($this->report) {
                $this->error('Identication error ' . $this->config->getValue('bounce_login') . ':' . $this->config->getValue('bounce_password'), true);
                return false;
            }
            if (!empty($warnings) AND $this->report)
                $this->error($warnings, true);
            return false;
        }

        ob_clean();

        return true;
    }

    function _connectimap() {
        ob_start();
        //First we reset the buffer or errors and warnings
        $buff = imap_alerts();
        $buff = imap_errors();

        $timeout = $this->config->getValue('bounce_timeout');
        if (!empty($timeout))
            imap_timeout(IMAP_OPENTIMEOUT, $timeout);

        $port = $this->config->getValue('bounce_port', '');
        $secure = $this->config->getValue('bounce_connection_secure', '');
        $protocol = $this->config->getValue('bounce_connection_method', '');
        $serverName = '{' . $this->config->getValue('bounce_host');
        if (empty($port)) {
            if ($secure == 'ssl' && $protocol == 'imap')
                $port = '993';
            elseif ($protocol == 'imap')
                $port = '143';
            elseif ($protocol == 'pop3')
                $port = '110';
        }


        if (!empty($port))
            $serverName .= ':' . $port;
        //Add the secure protocol (TLS or SSL)
        if (!empty($secure))
            $serverName .= '/' . $secure;
        if ($this->config->getValue('bounce_selfsigned', false))
            $serverName .= '/novalidate-cert';
        //Add the method (imap by default) ex : pop3
        if (!empty($protocol))
            $serverName .='/service=' . $protocol;
        $serverName .= '}';
        $this->mailbox = imap_open($serverName, trim($this->config->getValue('bounce_login')), trim($this->config->getValue('bounce_password')));
        $warnings = ob_get_clean();

        if ($this->report) {
            if (!$this->mailbox) {
                $this->error('Error connecting to ' . $serverName, true);
            }
            if (!empty($warnings)) {
                $this->error($warnings, true);
            }
        }


        return $this->mailbox ? true : false;
    }

    function getNBMessages() {
        if ($this->usepear) {
            $this->nbMessages = $this->mailbox->numMsg();
        } else {
            $this->nbMessages = imap_num_msg($this->mailbox);
        }

        return $this->nbMessages;
    }

    function getMessage($msgNB) {
        if ($this->usepear) {
            $message = new \stdClass;
            $message->headerString = $this->mailbox->getRawHeaders($msgNB);
            if (empty($message->headerString))
                return false;
        }else {
            $message = imap_headerinfo($this->mailbox, $msgNB);
        }

        return $message;
    }

    function deleteMessage($msgNB) {
        if ($this->usepear) {
            $this->mailbox->deleteMsg($msgNB);
        } else {
            imap_delete($this->mailbox, $msgNB);
            imap_expunge($this->mailbox);
        }
    }

    function close() {
        if ($this->usepear) {
            $this->mailbox->disconnect();
        } else {
            imap_close($this->mailbox);
        }
    }

    function decodeMessage() {
        if ($this->usepear) {
            return $this->_decodeMessagepear();
        } else {
            return $this->_decodeMessageimap();
        }
    }

    function _decodeMessagepear() {
        $this->_message->headerinfo = $this->mailbox->getParsedHeaders($this->_message->messageNB);

        if (empty($this->_message->headerinfo['subject']))
            return false;
        $this->_message->text = '';
        $this->_message->html = $this->mailbox->getBody($this->_message->messageNB);
        $this->_message->subject = $this->_decodeHeader($this->_message->headerinfo['subject']);
        $this->_message->header->sender_email = (isset($this->_message->headerinfo['return-path'])) ? $this->_message->headerinfo['return-path'] : '';
        if (is_array($this->_message->header->sender_email))
          $this->_message->header->sender_email = reset($this->_message->header->sender_email);
        if (preg_match($this->detectEmail, $this->_message->header->sender_email, $results)) {
          $this->_message->header->sender_email = $results[0];
        }
        $this->_message->header->sender_name = (isset($this->_message->headerinfo['from'])) ?  strip_tags(@$this->_message->headerinfo['from']) : '';
        $this->_message->header->reply_to_email = $this->_message->header->sender_email;
        $this->_message->header->reply_to_name = (property_exists($this->_message->header, 'sender_name')) ? $this->_message->header->sender_name : '';
        $this->_message->header->from_email = $this->_message->header->sender_email;
        $this->_message->header->from_name = $this->_message->header->reply_to_name;

        return true;
    }

    function _decodeMessageimap() {
        $this->_message->structure = imap_fetchstructure($this->mailbox, $this->_message->messageNB);
        if (empty($this->_message->structure))
            return false;
        $this->_message->headerinfo = imap_fetchheader($this->mailbox, $this->_message->messageNB);
        $this->_message->html = '';
        $this->_message->text = '';

        //Multipart message : type == 1
        if ($this->_message->structure->type == 1) {
            $this->_message->contentType = 2;
            $allParts = $this->_explodeBody($this->_message->structure);

            $this->_message->text = '';
            foreach ($allParts as $num => $onePart) {
                $charset = $this->_getMailParam($onePart, 'charset');
                if ($onePart->subtype == 'HTML') {
                    $this->_message->html = $this->_decodeContent(imap_fetchbody($this->mailbox, $this->_message->messageNB, $num), $onePart);
                } else {
                    $this->_message->text .= $this->_decodeContent(imap_fetchbody($this->mailbox, $this->_message->messageNB, $num), $onePart) . "\n\n- - -\n\n";
                }
            }
        } else {
            $charset = $this->_getMailParam($this->_message->structure, 'charset');
            if ($this->_message->structure->subtype == 'HTML') {
                $this->_message->contentType = 1;
                $this->_message->html = $this->_decodeContent(imap_body($this->mailbox, $this->_message->messageNB), $this->_message->structure);
            } else {
                $this->_message->contentType = 0;
                $this->_message->text = $this->_decodeContent(imap_body($this->mailbox, $this->_message->messageNB), $this->_message->structure);
            }
        }

        //Decode the subject
        $this->_message->subject = $this->_decodeHeader($this->_message->subject);

        $this->_decodeAddressimap('sender');
        $this->_decodeAddressimap('from');
        $this->_decodeAddressimap('reply_to');
        $this->_decodeAddressimap('to');
        return true;
    }

    function handleMessages() {
        $model_list = WYSIJA::get('list', 'model');

        $listdetails = $model_list->getRows(array('name', 'list_id'));

        foreach ($listdetails as $listinfo) {
            $this->listdetails[$listinfo['list_id']] = $listinfo['name'];
        }
        $maxMessages = min($this->nbMessages, $this->config->getValue('bounce_max', 100));
        if (empty($maxMessages))
            $maxMessages = $this->nbMessages;

        // we need a report when we are handling the bounce manually through the settings
        if ($this->report) {
            // If we display informations, we directy flush so that we can display in real time!
            if (!headers_sent() AND ob_get_level() > 0) {
                ob_end_flush();
            }

            // We prepare the area where we will add informations...
            $disp = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';
            $disp .= '<title>' . addslashes(__('Bounce Handling', WYSIJA)) . '</title>';
            $disp .= '<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;} strong{color: black;}</style></head><body>';
            $disp .= "<div style='position:relative; top:3px;left:3px;'>";
            $disp .= __("Bounce Handling", WYSIJA);
            $disp .= ':  <span id="counter"/>0</span> / ' . $maxMessages;
            $disp .= '</div>';
            $disp .= '<br/>';
            $disp .= '<script type="text/javascript" language="javascript">';
            $disp .= 'var mycounter = document.getElementById("counter");';
            $disp .= 'function setCounter(val){ mycounter.innerHTML=val;}';
            $disp .= '</script>';
            echo $disp;
            if (function_exists('ob_flush'))
                @ob_flush();
            @flush();
        }

        //We load all published the rules
        $rules = $this->rulesClass->getRules();

        $msgNB = $maxMessages;
        $listClass = WYSIJA::get('list', 'model');
        $this->allLists = $listClass->getRows();

        while (($msgNB > 0) && ($this->_message = $this->getMessage($msgNB))) {
            if ($this->report) {
                echo '<script type="text/javascript" language="javascript">setCounter(' . ($maxMessages - $msgNB + 1) . ')</script>';
                if (function_exists('ob_flush'))
                    @ob_flush();
                @flush();
            }
            $this->_message->messageNB = $msgNB;
            $this->decodeMessage();

            $msgNB--;
            if (empty($this->_message->subject))
                continue;

            $this->_message->analyseText = $this->_message->html . ' ' . $this->_message->text;
            $this->_display('<strong>' . __('Subject', WYSIJA) . ' : ' . strip_tags($this->_message->subject) . '</strong>', false, $maxMessages - $this->_message->messageNB + 1);

            // Identify the user and the e-mail... there is not the same info when it is a multisite or not
            $email_identifiers = array();
            if ($this->record_ms_bounce) {
                preg_match('#WY([0-9]+)SI([0-9]+)JA([0-9]+)MS#i', $this->_message->analyseText, $email_identifiers);
                if (!empty($email_identifiers[1]))
                    $this->_message->user_id = $email_identifiers[1];
                if (!empty($email_identifiers[2]))
                    $this->_message->email_id = $email_identifiers[2];
                if (!empty($email_identifiers[3]))
                    $this->_message->site_id = $email_identifiers[3];
            }else {
                preg_match('#WY([0-9]+)SI([0-9]+)JA#i', $this->_message->analyseText, $email_identifiers);
                if (!empty($email_identifiers[1]))
                    $this->_message->user_id = $email_identifiers[1];
                if (!empty($email_identifiers[2]))
                    $this->_message->email_id = $email_identifiers[2];
            }

            // if we don't have the user_id set then we need to find the user_id differently
            if (empty($this->_message->user_id)) {
                // We will need the e-mail itself in that case... :p
                $emails_detected_in_the_email = array();
                preg_match_all($this->detectEmail, $this->_message->analyseText, $emails_detected_in_the_email);
                $reply_email = $this->config->getValue('reply_email');
                $from_email = $this->config->getValue('from_email');
                $bounce_email = $this->config->getValue('bounce_email');
                $remove_emails = '#(' . str_replace(array('%'), array('@'), $this->config->getValue('bounce_login'));
                if (!empty($bounce_email))
                    $remove_emails .= '|' . $bounce_email;
                if (!empty($from_email))
                    $remove_emails .= '|' . $from_email;
                if (!empty($reply_email))
                    $remove_emails .= '|' . $reply_email;
                    $remove_emails .= ')#i';
                if (!empty($emails_detected_in_the_email[0])) {
                    $email_already_checked = array();
                    foreach ($emails_detected_in_the_email[0] as $detected_email) {
                        // We will find the e-mail if it's not in the list of incorrect e-mail addresses
                        if (!preg_match($remove_emails, $detected_email)) {
                            // We will keep this one, so we make sure it's strtolower
                            $this->_message->subemail = strtolower($detected_email);
                            // We already checked this e-mail address... no need to try it a second time
                            if (!empty($email_already_checked[$this->_message->subemail]))
                                continue;
                            $this->subClass->getFormat = OBJECT;
                            $result = $this->subClass->getOne(array('user_id'), array('email' => $this->_message->subemail));

                            $this->_message->user_id = $result->user_id;
                            $email_already_checked[$this->_message->subemail] = true;
                            if (!empty($this->_message->user_id))
                                break;
                        }
                    }
                }
            }

            // get the email_id if it is not set and the user_id has been found
            if (empty($this->_message->email_id) && !empty($this->_message->user_id)) {
                // We can check if we have a user and only one e-mail sent for this user, it's obviously the e-mail we just sent!!
                $modelEUS = WYSIJA::get('email_user_stat', 'model');
                $emailres = $modelEUS->query('get_row', 'SELECT `email_id` FROM [wysija]' . $modelEUS->table_name . ' WHERE `user_id` = ' . (int) $this->_message->user_id . ' ORDER BY `sent_at` DESC LIMIT 1');
                $this->_message->email_id = $emailres['email_id'];
                //$this->_message->email_id = $this->db->loadResult();
            }

            foreach ($rules as $one_rule) {
                //We stop as soon as we find a good rule...
                if ($this->_handleRule($one_rule))
                    break;
            }


            if ($msgNB % 50 == 0){
                if(!$this->record_ms_bounce) $this->_sub_actions();
            }
        }

        if(!$this->record_ms_bounce) $this->_sub_actions();

        if ($this->report) {
            //We need to finish the current page properly
            echo '</body></html>';
        }
    }


    /**
     * take action on the subscribers based on what data we gathered in the processing part earlier
     */
    function _sub_actions() {

        // the action is about deleting users
        if (!empty($this->deletedUsers)) {
            $this->subClass->testdelete = true;
            $helper_user = WYSIJA::get('user','helper');
            $helper_user->delete($this->deletedUsers);
            $this->deletedUsers = array();
        }

        if (!empty($this->unsubscribedUsers)) {
            //unsubscribe user
            $user_helper = WYSIJA::get('user', 'helper');
            if (!is_array($this->unsubscribedUsers)) $this->unsubscribedUsers=array($this->unsubscribedUsers);

            foreach ($this->unsubscribedUsers as $unsub_user_id) {
                $user_helper->unsubscribe($unsub_user_id, true);
            }

            $this->unsubscribedUsers = array();
        }

        if (!empty($this->addtolistUsers)) {
            //unsubscribe user
            $user_helper = WYSIJA::get('user', 'helper');
            foreach ($this->addtolistUsers as $listid => $user_ids) {
                $user_helper->addToList($listid, $user_ids);
            }

            $this->addtolistUsers = array();
        }

        if (!empty($this->bounceMessages)) {
            foreach ($this->bounceMessages as $email_id => $bouncedata) {
                if (!empty($bouncedata['user_id'])) {
                    //flag email has bounced
                    $modelEUS = WYSIJA::get('email_user_stat', 'model');
                    $modelEUS->update(array('status' => -1), array('user_id' => $bouncedata['user_id'], 'email_id' => (int) $email_id));
                    /* $this->db->setQuery('UPDATE '.acymailing_table('userstats').' SET `bounce` = `bounce` + 1 WHERE `user_id` IN ('.implode(',',$bouncedata['user_id']).') AND `email_id` = '.(int) $email_id);
                      $this->db->query(); */
                }
            }
            $this->bounceMessages = array();
        }
    }




    function _handleRule(&$one_rule) {

        $regex = $one_rule['regex'];
        if (empty($regex))
            return false;

        //Do it based on the config of the rule...

        $analyse_text = '';
        if (isset($one_rule['executed_on']['senderinfo']))
            $analyse_text .= ' ' . $this->_message->header->sender_name . $this->_message->header->sender_email;
        if (isset($one_rule['executed_on']['subject']))
            $analyse_text .= ' ' . $this->_message->subject;
        if (isset($one_rule['executed_on']['body'])) {
            if (!empty($this->_message->html))
                $analyse_text .= ' ' . $this->_message->html;
            if (!empty($this->_message->text))
                $analyse_text .= ' ' . $this->_message->text;
        }

        //regex multilines
        if (!preg_match('#' . $regex . '#is', $analyse_text))
            return false;

        $message = $one_rule['name'];

        if($this->record_ms_bounce) {
            // no need for user action in multisite because we'll do it per site in a second process
            $message .= $this->_action_message_ms($one_rule);
        }else{
            $message .= $this->_action_user($one_rule);
            $message .= $this->_action_message($one_rule);
        }


        $this->_display($message, true);

        return true;
    }

    function _action_user(&$one_rule) {
        $message = '';

        if (empty($this->_message->user_id)) {
            $message .= 'user not identified';
            if (!empty($this->_message->subemail))
                $message .= ' ( ' . $this->_message->subemail . ' ) ';
            return $message;
        }

        if (isset($one_rule['action_user']) && in_array($one_rule['action_user'], array('unsub'))) {
            if (empty($this->_message->subemail)) {
                $currentUser = $this->subClass->getObject($this->_message->user_id);
                if (!empty($currentUser->email))
                    $this->_message->subemail = $currentUser->email;
            }
        }

        if (empty($this->_message->subemail))
            $this->_message->subemail = $this->_message->user_id;

        //let's handle some actions on the subscriber first
        if (isset($one_rule['action_user_stats'])) {
            //handle this rule in the stats
            if (!empty($this->_message->email_id)) {
                if (empty($this->bounceMessages[$this->_message->email_id]['nbbounces'])) {
                    $this->bounceMessages[$this->_message->email_id] = array();
                    $this->bounceMessages[$this->_message->email_id]['nbbounces'] = 1;
                } else {
                    $this->bounceMessages[$this->_message->email_id]['nbbounces']++;
                }

                if (!empty($this->_message->user_id) AND ((isset($one_rule['action_user']) && $one_rule['action_user'] != 'delete') || !isset($one_rule['action_user']) )) {
                    //Increment the bounce number in the user stat table but only if we don't delete the subscriber
                    $this->bounceMessages[$this->_message->email_id]['user_id'][] = intval($this->_message->user_id);
                }
            }
        }


        //Make sure we have enough messages to really execute this
        if (!empty($one_rule['action_user_min']) && $one_rule['action_user_min'] > 1) {
            //Let's load the number of bounces the user has and then exit or not...
            $modelEUS = WYSIJA::get('email_user_stat', 'model');
            $res = $modelEUS->query('get_row', 'SELECT COUNT(email_id) as count FROM [wysija]' . $modelEUS->table_name . ' WHERE status = -1 AND user_id = ' . $this->_message->user_id);
            $nb = intval($res['count']) + 1;

            if ($nb < $one_rule['action_user_min']) {
                $message .= ', ' . sprintf(__('We received %1$s messages from the user %2$s', WYSIJA), $nb, $this->_message->subemail) . ', ' . sprintf(__('Actions will be executed after %1$s messages', WYSIJA), $one_rule['action_user_min']);
                return $message;
            }
        }

        //IN WYSIJA THERE ARE 3 POSSIBILITIES
        //1-Delete user
        //2-Unsubscribe user
        //3-Unsubscribe and attach to list "xxx"
        if (isset($one_rule['action_user'])) {
            switch ($one_rule['action_user']) {
                case 'delete'://1 -Delete user
                    $message .= ', user ' . $this->_message->subemail . ' deleted';
                    $this->deletedUsers[] = intval($this->_message->user_id);

                    break;
                case 'unsub'://2-Unsubscribe user
                    //when we unsubscribe somebody automatically we set the status to -2 instead of -1 to make the difference
                    $message .= ', user ' . $this->_message->subemail . ' unsubscribed';
                    $this->unsubscribedUsers[] = $this->_message->user_id;

                    break;
                default:
                    //3 - Unsubscribe user and add to list
                    if (strpos($one_rule['action_user'], 'unsub_') !== false) {
                        $listid = (int) str_replace('unsub_', '', $one_rule['action_user']);
                        $message .= ', user ' . $this->_message->subemail . ' unsubscribed';
                        $this->unsubscribedUsers[] = $this->_message->user_id;
                        $this->addtolistUsers[$listid][] = $this->_message->user_id;
                        $message .= ', user ' . $this->_message->subemail . ' added to list "' . $this->listdetails[$listid] . '"';
                    }
            }
        }





        return $message;
    }

    function _action_message(&$one_rule) {

        $message = '';

        //Handle actions on the message itself

        if (isset($one_rule['action_message']['save']) && !empty($this->_message->user_id)) {
            //We have a user_id, should we save the message in the database?
            $data = array();
            $data[] = 'SUBJECT::' . $this->_message->subject;

            if (!empty($this->_message->html))
                $data[] = 'HTML_VERSION::' . htmlentities($this->_message->html);
            if (!empty($this->_message->text))
                $data[] = 'TEXT_VERSION::' . nl2br(htmlentities($this->_message->text));
            $this->_message->header->reply_to_name = (property_exists($this->_message->header, 'reply_to_name')) ? $this->_message->header->reply_to_name : '';
            $this->_message->header->from_name = (property_exists($this->_message->header, 'from_name')) ? $this->_message->header->from_name : '';
            $data[] = 'REPLYTO_ADDRESS::' . $this->_message->header->reply_to_name . ' ( ' . $this->_message->header->reply_to_email . ' )';
            $data[] = 'FROM_ADDRESS::' . $this->_message->header->from_name . ' ( ' . $this->_message->header->from_email . ' )';
            $data[] = print_r($this->_message->headerinfo, true);
            $this->historyClass->insert($this->_message->user_id, 'bounce', $data, @$this->_message->email_id);
            $message .= ', message saved (user ' . $this->_message->user_id . ')';
        }

        if (isset($one_rule['forward'])) {
            if (isset($one_rule['action_message_forwardto']) && !empty($one_rule['action_message_forwardto']) && trim($one_rule['action_message_forwardto']) != trim($this->config->getValue('bounce_email'))) {
                //Get the forward address :
                $this->mailer->clearAll();
                $this->mailer->Subject = 'BOUNCE FORWARD : ' . $this->_message->subject;
                $this->mailer->AddAddress($one_rule['action_message_forwardto']);
                if (!empty($this->_message->html)) {
                    $this->mailer->IsHTML(true);
                    $this->mailer->Body = $this->_message->html;
                    if (!empty($this->_message->text))
                        $this->mailer->Body .= '<br/><br/>-------<br/>' . nl2br($this->_message->text);
                }else {
                    $this->mailer->IsHTML(false);
                    $this->mailer->Body = $this->_message->text;
                }

                //We add all other extra information just in case of we could use them...
                //original-rcpt-to ?   http://tools.ietf.org/html/rfc5965
                $this->mailer->Body .= print_r($this->_message->headerinfo, true);
                $this->_message->header->reply_to_name = (property_exists($this->_message->header, 'reply_to_name')) ? $this->_message->header->reply_to_name : '';
                $this->_message->header->from_name = (property_exists($this->_message->header, 'from_name')) ? $this->_message->header->from_name : '';
                $this->mailer->AddReplyTo($this->_message->header->reply_to_email, $this->_message->header->reply_to_name);
                $this->mailer->setFrom($this->_message->header->from_email, $this->_message->header->from_name);
                if ($this->mailer->send()) {
                    $message .= ', forwarded to ' . $one_rule['action_message_forwardto'];
                } else {
                    $message .= ', error forwarding to ' . $one_rule['action_message_forwardto'];
                }

            } else {
                //we dont delete the email if the forward email is not specified or if the bounce email is the same as the forward email
                unset($one_rule['action_message']['delete']);
            }
        }

        if (isset($one_rule['action_message']['delete'])) {
            $message .= ', message deleted';
            $this->deleteMessage($this->_message->messageNB);
        }

        return $message;
    }

    function _decodeAddressimap($type) {
        $address = $type . 'address';
        $name = $type . '_name';
        $email = $type . '_email';
        if (empty($this->_message->$type))
            return false;

        $var = $this->_message->$type;

        if (!empty($this->_message->$address)) {
            if(!isset($this->_message->header) || is_null($this->_message->header)){
                $this->_message->header = new stdClass();
            }
        } else {
            $this->_message->header->$name = $var[0]->personal;
        }

        $this->_message->header->$email = $var[0]->mailbox . '@' . @$var[0]->host;
        return true;
    }

    /**
     * If num is empty then it's a message otherwise it's a send statrus
     */
    function _display($message, $status = '', $num = '') {
        $this->messages[] = $message;

        if (!$this->report)
            return;

        $color = $status ? 'black' : 'blue';
        if (!empty($num))
            echo '<br/>' . $num . ' : ';
        else
            echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

        echo '<font style="font-family: Arial;" color="' . $color . '">' . $message . '</font>';
        if (function_exists('ob_flush'))
            @ob_flush();
        @flush();
    }

    function _decodeHeader($input) {
        // Remove white space between encoded-words
        $input = preg_replace('/(=\?[^?]+\?(q|b)\?[^?]*\?=)(\s)+=\?/i', '\1=?', $input);
        $this->charset = false;

        // For each encoded-word...
        while (preg_match('/(=\?([^?]+)\?(q|b)\?([^?]*)\?=)/i', $input, $matches)) {

            $encoded = $matches[1];
            $charset = $matches[2];
            $encoding = $matches[3];
            $text = $matches[4];

            switch (strtolower($encoding)) {
                case 'b':
                    $text = base64_decode($text);
                    break;

                case 'q':
                    $text = str_replace('_', ' ', $text);
                    preg_match_all('/=([a-f0-9]{2})/i', $text, $matches);
                    foreach ($matches[1] as $value)
                        $text = str_replace('=' . $value, chr(hexdec($value)), $text);
                    break;
            }
            $this->charset = $charset;
            $input = str_replace($encoded, $text, $input);
        }

        return $input;
    }

    function _explodeBody($struct, $path = "0", $inline = 0) {
        $allParts = array();

        if (empty($struct->parts))
            return $allParts;

        $c = 0; //counts real content
        foreach ($struct->parts as $part) {
            if ($part->type == 1) {
                //There are more parts....:
                if ($part->subtype == "MIXED") { //Mixed:
                    $path = $this->_incPath($path, 1); //refreshing current path
                    $newpath = $path . ".0"; //create a new path-id (ex.:2.0)
                    $allParts = array_merge($this->_explodeBody($part, $newpath), $allParts); //fetch new parts
                } else { //Alternativ / rfc / signed
                    $newpath = $this->_incPath($path, 1);
                    $path = $this->_incPath($path, 1);
                    $allParts = array_merge($this->_explodeBody($part, $newpath, 1), $allParts);
                }
            } else {
                $c++;
                //creating new tree if this is part of a alternativ or rfc message:
                if ($c == 1 && $inline) {
                    $path = $path . ".0";
                }
                //saving content:
                $path = $this->_incPath($path, 1);
                //print "<br>  Content ".$path."<br>";        //debug information
                $allParts[$path] = $part;
            }
        }

        return $allParts;
    }

    //Increases the Path to the parts:
    function _incPath($path, $inc) {
        $newpath = "";
        $path_elements = explode(".", $path);
        $limit = count($path_elements);
        for ($i = 0; $i < $limit; $i++) {
            if ($i == $limit - 1) { //last element
                $newpath .= $path_elements[$i] + $inc; // new Part-Number
            } else {
                $newpath .= $path_elements[$i] . "."; //rebuild "1.2.2"-Chronology
            }
        }
        return $newpath;
    }

    function _decodeContent($content, $structure) {
        $encoding = $structure->encoding;

        //First we decode the content properly
        if ($encoding == 2)
            $content = imap_binary($content);
        elseif ($encoding == 3)
            $content = imap_base64($content);
        elseif ($encoding == 4)
            $content = imap_qprint($content);
        //Other cases??
        //added for a client who had issue when message was base64
        if(base64_decode($content,true)!==FALSE)
            $content = base64_decode($content);

        //Now we convert into utf-8!
        //$charset = $this->_getMailParam($structure,'charset');
        // removes attachment to prevent bounce handling timeout
        // 100 000 characters is plenty
        return substr($content, 0, 100000);
    }

    function _getMailParam($params, $name) {
        $searchIn = array();

        if ($params->ifparameters)
            $searchIn = array_merge($searchIn, $params->parameters);
        if ($params->ifdparameters)
            $searchIn = array_merge($searchIn, $params->dparameters);

        if (empty($searchIn))
            return false;

        foreach ($searchIn as $num => $values) {
            if (strtolower($values->attribute) == $name) {
                return $values->value;
            }
        }
    }

    function getErrors() {
        $return = array();
        if ($this->usepear) {
//TODO : get some errors from the pear interface?
        } else {
            $alerts = imap_alerts();
            $errors = imap_errors();
            if (!empty($alerts))
                $return = array_merge($return, $alerts);
            if (!empty($errors))
                $return = array_merge($return, $errors);
        }

        return $return;
    }



    /**
     * simple function that launch a bounce process
     * @return boolean
     */
    function process_bounce() {

        @ini_set('max_execution_time', 0);

        $model_config = WYSIJA::get('config', 'model');

        $this->report = true;
        if (!$this->init()) {
            $res['result'] = false;
            return $res;
        }
        if (!$this->connect()) {
            $this->error($this->getErrors());
            $res['result'] = false;
            return $res;
        }
        $this->notice(sprintf(__('Successfully connected to %1$s'), $model_config->getValue('bounce_login')));
        $nbMessages = $this->getNBMessages();


        if (empty($nbMessages)) {
            $this->error(__('There are no messages'), true);
            $res['result'] = false;
            return $res;
        } else {
            $this->notice(sprintf(__('There are %1$s messages in your mailbox'), $nbMessages));
        }

        $this->handleMessages();

        $this->close();

        $res['result'] = true;

        return $res;
    }


    /**
     * record the bounce into the bounce table on a multisite
     * @return type
     */
    function record_bounce_ms() {
        // make sure that the bounce recording is not already being processed on another child site
        if (get_site_option('wysija_bounce_being_recorded'))
            return;

        // flag the current recording
        WYSIJA::update_option('wysija_bounce_being_recorded', true);

        // set the flag to indicate we are processing the bounce in a multisite manner right now
        $this->record_ms_bounce = true;

        // will record the bounce in the ms table
        $result = $this->process_bounce();

        // lower the flag we can process the bounce again
        WYSIJA::update_option('wysija_bounce_being_recorded', false);

        return $result;
    }
    /**
     * base on the records we have in the bounce table we will take action
     * @return boolean
     */
    function process_bounce_ms() {

        @ini_set('max_execution_time', 0);
        global $blog_id;
        $main_site_prefix = $this->subClass->get_site_prefix();
        // make a query that will handle the bounce delete for all of the emails recorded in the bounce table for that site
        // join the table bounce and user to make sure the ID's exist get 200 of them

        // we will delete one by one all of the data from the users that need to be removed
        $tables = array(
            'user_history',
            'email_user_url',
            'email_user_stat',
            'user_list',
            'queue',
        );

        // central query to fetch the id of the bounced emails of the delete action
        $query_join_bounce = 'SELECT B.user_id from '.$main_site_prefix.'bounce as A JOIN [wysija]user as B on A.email = B.email WHERE A.action_taken = "delete"';

        try{
            foreach ($tables as $table_name){
                $query_delete = 'DELETE FROM [wysija]' . $table_name . ' WHERE user_id IN (' . $query_join_bounce . ')';
                $this->subClass->query($query_delete);
            }
        }catch(Exception $e){
            return false;
        }

        // delete process from  the  user table needs to be made through a join since we cannot nest select from the same table we delete from
        $query_delete_user = 'DELETE A.* FROM [wysija]user as A JOIN '.$main_site_prefix.'bounce as B on A.email = B.email WHERE B.action_taken = "delete"';
        $this->subClass->query($query_delete_user);

        // central query to fetch the id of the bounced emails of the unsubscribe action
        $query_join_bounce = 'SELECT B.user_id from '.$main_site_prefix.'bounce as A JOIN [wysija]user as B on A.email = B.email WHERE A.action_taken = "unsubscribe"';

        // query to update the status to unsubscribe
        $query_update_user = 'UPDATE [wysija]user as A JOIN '.$main_site_prefix.'bounce as B on A.email = B.email SET A.`status` = -1 WHERE A.`status` != -1';
        //$query_update_user = 'UPDATE [wysija]user SET `status` = -1  WHERE user_id IN (' . $query_join_bounce . ')';

        $query_update_user_list = 'UPDATE [wysija]user_list as A JOIN [wysija]user as B on A.user_id = B.user_id JOIN '.$main_site_prefix.'bounce as C on B.email = C.email  SET A.`unsub_date` = '.time().' , A.`sub_date` = 0  WHERE B.`status` != -1';
        //$query_update_user_list = 'UPDATE [wysija]user_list SET `unsub_date` = '.time().' , `sub_date` = 0  WHERE user_id IN (' . $query_join_bounce . ')';

        $this->subClass->getResults($query_update_user);
        $this->subClass->getResults($query_update_user_list);

        // delete what's in the queue for those subscribers
        $query_delete = 'DELETE FROM [wysija]queue WHERE user_id IN (' . $query_join_bounce . ')';

        $this->subClass->getResults($query_delete);

        // query to set the boucne value in the email table
        $query_update_email_user_status = 'UPDATE [wysija]email_user_stat as A JOIN '.$main_site_prefix.'bounce as C on (A.user_id = C.user_id AND A.email_id = C.email_id)  SET A.`status` = -1 WHERE C.site_id='.$blog_id;
        $this->subClass->getResults($query_update_email_user_status);

        $res['result'] = true;

        return $res;
    }

    /**
     * take action on the subscribers for multisites processing we don't delete or unsubscribe the users, we just record the action to be taken in the db
     */
    function _sub_actions_ms() {

        // the action is about deleting users
        if (!empty($this->deletedUsers)) {
            $this->subClass->testdelete = true;

            $this->deletedUsers = array();
        }

        if (!empty($this->unsubscribedUsers)) {

            $this->unsubscribedUsers = array();
        }
    }

    function _action_message_ms(&$one_rule) {

        $email_copy = $message = '';

        //Handle actions on the message itself

        if (isset($one_rule['action_message']['save']) && !empty($this->_message->user_id)) {
            //We have a user_id, should we save the message in the database?
            $email_saved_as_array = array();
            $email_saved_as_array[] = 'SUBJECT::' . $this->_message->subject;

            if (!empty($this->_message->html))
                $email_saved_as_array[] = 'HTML_VERSION::' . htmlentities($this->_message->html);
            if (!empty($this->_message->text))
                $email_saved_as_array[] = 'TEXT_VERSION::' . nl2br(htmlentities($this->_message->text));
            $this->_message->header->reply_to_name = (property_exists($this->_message->header, 'reply_to_name')) ? $this->_message->header->reply_to_name : '';
            $this->_message->header->from_name = (property_exists($this->_message->header, 'from_name')) ? $this->_message->header->from_name : '';
            $email_saved_as_array[] = 'REPLYTO_ADDRESS::' . $this->_message->header->reply_to_name . ' ( ' . $this->_message->header->reply_to_email . ' )';
            $email_saved_as_array[] = 'FROM_ADDRESS::' . $this->_message->header->from_name . ' ( ' . $this->_message->header->from_email . ' )';
            $email_saved_as_array[] = print_r($this->_message->headerinfo, true);
            $email_copy = implode("\n",$email_saved_as_array);

            $message .= ', message saved (user ' . $this->_message->user_id . ')';

        }

        if (isset($one_rule['forward'])) {
            if (isset($one_rule['action_message_forwardto']) && !empty($one_rule['action_message_forwardto']) && trim($one_rule['action_message_forwardto']) != trim($this->config->getValue('bounce_email'))) {
                //Get the forward address :
                $this->mailer->clearAll();
                $this->mailer->Subject = 'BOUNCE FORWARD : ' . $this->_message->subject;
                $this->mailer->AddAddress($one_rule['action_message_forwardto']);
                if (!empty($this->_message->html)) {
                    $this->mailer->IsHTML(true);
                    $this->mailer->Body = $this->_message->html;
                    if (!empty($this->_message->text))
                        $this->mailer->Body .= '<br/><br/>-------<br/>' . nl2br($this->_message->text);
                }else {
                    $this->mailer->IsHTML(false);
                    $this->mailer->Body = $this->_message->text;
                }

                //We add all other extra information just in case of we could use them...
                //original-rcpt-to ?   http://tools.ietf.org/html/rfc5965
                $this->mailer->Body .= print_r($this->_message->headerinfo, true);
                $this->_message->header->reply_to_name = (property_exists($this->_message->header, 'reply_to_name')) ? $this->_message->header->reply_to_name : '';
                $this->_message->header->from_name = (property_exists($this->_message->header, 'from_name')) ? $this->_message->header->from_name : '';
                $this->mailer->AddReplyTo($this->_message->header->reply_to_email, $this->_message->header->reply_to_name);
                $this->mailer->setFrom($this->_message->header->from_email, $this->_message->header->from_name);
                if ($this->mailer->send()) {
                    $message .= ', forwarded to ' . $one_rule['action_message_forwardto'];
                } else {
                    $message .= ', error forwarding to ' . $one_rule['action_message_forwardto'];
                }

            } else {
                //we dont delete the email if the forward email is not specified or if the bounce email is the same as the forward email
                unset($one_rule['action_message']['delete']);
            }
        }

        //  insert the message or just the action taken on that message in the main bounce table

        // get the prefix of the current child site
        $bounced_site_prefix = $this->subClass->get_site_prefix($this->_message->site_id);
        // get the email of the bounced message based on the user_id and the site_id
        $query = 'SELECT email FROM `'.$bounced_site_prefix.'user` WHERE user_id='.(int)$this->_message->user_id.' LIMIT 0 , 1';
        $result_subscriber = $this->subClass->query('get_res',$query,OBJECT);

        $this->subClass->reset();

        if(strpos($one_rule['action_user'], 'unsub')!==false) $action_taken='unsubscribe';
        elseif($one_rule['action_user']!='') $action_taken='delete';

        if(!empty($result_subscriber[0]->email)){
            $main_site_prefix = $this->subClass->get_site_prefix();
            $query_insert_bounce_ms = 'INSERT IGNORE INTO `'.$main_site_prefix.'bounce` (`email`,`site_id`,`user_id`,`email_id`,`action_taken`,`case`,`message`,`created_at`)';
            $query_insert_bounce_ms .= " VALUES ('".$result_subscriber[0]->email."','".(int)$this->_message->site_id."','".(int)$this->_message->user_id."','".(int)$this->_message->email_id."','".$action_taken."', '".$one_rule['key']."', '". esc_sql($email_copy)."', '".time()."')";

            $this->subClass->query($query_insert_bounce_ms);
        }

        if (isset($one_rule['action_message']['delete'])) {
            $message .= ', message deleted';
            $this->deleteMessage($this->_message->messageNB);
        }

        return $message;
    }

}