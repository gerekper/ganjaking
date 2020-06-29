<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_help_queue extends WYSIJA_object{
	var $email_id = 0;
	var $report = true;
	var $send_limit = 0;
	var $finish = false;
	var $error = false;
	var $nbprocess = 0;
	var $start = 0;
	var $stoptime = 0;
	var $successSend =0;
	var $errorSend=0;
	var $consecutiveError=0;
	var $messages = array();
	var $pause = 0;
	var $config;
 	var $listsubClass;
  	var $subClass;

        /**
         *
         */
	function __construct(){
            $this->config = WYSIJA::get('config','model');
            $this->subClass = WYSIJA::get('user','model');//acymailing_get('class.sub');
            $this->listsubClass = WYSIJA::get('user_list','model');//acymailing_get('class.listsub');
            $this->listsubClass->checkAccess = false;
            $this->listsubClass->sendNotif = false;
            $this->listsubClass->sendConf = false;

            $is_multisite=is_multisite();

            //$is_multisite=true;//PROD comment that line

            // get the right sending limit based on the setup we are on,
            // if we are on a multisite we need to see which sending method is used to determine that
            if($is_multisite && $this->config->getValue('sending_method')=='network'){
                $this->send_limit=(int)$this->config->getValue('ms_sending_emails_number');
            }else{
                $this->send_limit=(int)$this->config->getValue('sending_emails_number');
            }

            if(isset($_REQUEST['totalsend'])){
                $this->send_limit = (int) $_REQUEST['totalsend']-$_REQUEST['alreadysent'];
            }

            @ini_set('max_execution_time',0);
            @ini_set('default_socket_timeout',10);
            @ignore_user_abort(true);

            //we set a stoppage time to avoid broken process
            $time_limit = ini_get('max_execution_time');
            if(!empty($time_limit)){
                    $this->stoptime = time()+$time_limit-4;
            }
	}

        /**
         *
         * @param int $email_id
         * @param int $user_id we can specify that parameter if we want to process the emails of only one specific user
         * @return boolean
         */
	function process($email_id=false,$user_id=false){
                if($email_id)    $this->email_id=(int)$email_id;
                $model_queue = WYSIJA::get('queue','model');
		$queue_elements = $model_queue->getReady($this->send_limit,$this->email_id,$user_id);

                $this->total=count($queue_elements);
                $this->start=0;

                // there is no elements queued
		if(empty($queue_elements)){
			// there might be some delaied emails let's list those ones
                        $queue_elements_delayed = $model_queue->getDelayed($this->email_id);

                        // let's see if there are any delayed emails so that we display something about it
                        if(empty($queue_elements_delayed)){
                            $this->clear();
                        }else{
                            // report is on let's print the details about those delayed elements
                            if($this->report){
                                $html_response = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';
                                $html_response .= '<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;}</style></head><body>';
                                $html_response.= '<div style="background-color : white;border : 1px solid grey; padding : 3px;font-size:14px">';
                                $html_response.= '<span id="divpauseinfo" style="padding:10px;margin:5px;font-size:16px;font-weight:bold;display:none;background-color:black;color:white;"> </span>';
                                $helper_toolbox=WYSIJA::get('toolbox','helper');

                                $html_response.= sprintf(__('There are %1$s pending email(s)',WYSIJA),count($queue_elements_delayed));
                                $html_response.= ' - <small>'.date_i18n(get_option('date_format').' '.get_option('time_format'),$helper_toolbox->servertime_to_localtime(time())).'</small>';
                                $html_response.= '</div>';

                                // for each elements let's calculate and print when the email is supposed to be sent
                                foreach($queue_elements_delayed as $element){
                                    $html_response.= '<div id="divinfo">'.sprintf(__('Email will be sent to %1$s at %2$s',WYSIJA),'<b>'.$element['email'].'</b>','<em>'.date_i18n(get_option('date_format').' '.get_option('time_format'),$helper_toolbox->servertime_to_localtime($element['send_at'])).'</em>').' </div>';
                                }

                                echo $html_response;
                            }
                        }

                        $this->finish = true;
			return true;
		}

                // report is on let's print the details about those delayed elements
		if($this->report){
			if(!headers_sent() AND ob_get_level() > 0){
				ob_end_flush();
			}
			$html_response = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';
			$html_response .= '<title>'.addslashes(__('Send Process',WYSIJA)).'</title>';
			$html_response .= '<style>body{font-size:12px;font-family: Arial,Helvetica,sans-serif;}</style></head><body>';
			$html_response.= "<div style='padding: 3px;'>";
			$html_response.= "<span id='divpauseinfo' style='padding:10px;margin:5px;font-size:16px;font-weight:bold;display:none;background-color:black;color:white;'> </span>";
			$html_response.= __('Total of batch',WYSIJA).': <span id="counter"/>'.$this->start.'</span> / '. $this->total;
			$html_response.= '</div>';
			$html_response.= "<div id='divinfo' style='display:none; position:fixed; bottom:3px;left:3px;background-color : white; border : 1px solid grey; padding : 3px;'> </div>";

                        $url = 'admin.php?page=wysija_campaigns&action=manual_send&emailid='.$this->email_id.'&_wpnonce='.WYSIJA_view::secure(array('action' => 'manual_send'), true).'&totalsend='.$this->total.'&alreadysent=';

			$html_response.= '<script type="text/javascript" language="javascript">';
			$html_response.= 'var mycounter = document.getElementById("counter");';
			$html_response.= 'var divinfo = document.getElementById("divinfo");
					var divpauseinfo = document.getElementById("divpauseinfo");
					function setInfo(message){ divinfo.style.display = \'block\';divinfo.innerHTML=message; }
					function setPauseInfo(nbpause){ divpauseinfo.style.display = \'\';divpauseinfo.innerHTML=nbpause;}
					function setCounter(val){ mycounter.innerHTML=val;}
					var scriptpause = '.intval($this->pause).';
					function handlePause(){
						setPauseInfo(scriptpause);
						if(scriptpause > 0){
							scriptpause = scriptpause - 1;
							setTimeout(\'handlePause()\',1000);
						}else{
							document.location.href=\''.$url.'\'+mycounter.innerHTML;
						}
					}
					</script>';
			echo $html_response;
			if(function_exists('ob_flush')) @ob_flush();
			@flush();
		}//endifreport

                $helper_mailer=WYSIJA::get('mailer','helper');
		$helper_mailer->report = false;
		if($this->config->getValue('smtp_keepalive',1)) $helper_mailer->SMTPKeepAlive = true;
                else $helper_mailer->SMTPKeepAlive = false;
		$queue_delete = array();
		$queue_update = array();
		$stats_add = array();
		$action_subscriber = array();
		$max_try = (int) $this->config->getValue('queue_try',3);
		$current_mail = $this->start;
		$this->nbprocess = 0;
		if(count($queue_elements) < $this->send_limit){
			$this->finish = true;
		}
                WYSIJA::log('helpers -> Queue ->process',$queue_elements,'queue_process');

                // go through each queued element and process it
		foreach($queue_elements as $queue_item){
			$current_mail++; $this->nbprocess++;
			if($this->report){
				echo '<script type="text/javascript" language="javascript">setCounter('. $current_mail .')</script>';
				if(function_exists('ob_flush')) @ob_flush();
				@flush();
			}
                        WYSIJA::log('helpers -> Queue ->process ->sendOne',array('email_id'=>$queue_item->email_id,'oneQueue'=>$queue_item),'queue_process');
			$result = $helper_mailer->sendOne($queue_item->email_id,$queue_item);

			$queue_delete_ok = true;
			$other_message = '';

                        // email has been sent ok let's make the process resulting after that
			if($result){
				$this->successSend ++;
				$this->consecutiveError = 0;
				$queue_delete[$queue_item->email_id][] = $queue_item->user_id;
				$stats_add[$queue_item->email_id][1][(int)$helper_mailer->sendHTML][] = $queue_item->user_id;
				$queue_delete_ok = $this->_deleteQueue($queue_delete);
                                WYSIJA::log('helpers -> Queue ->process ->sendOne resultOK(queue delete)',$queue_delete,'queue_process');
				$queue_delete = array();

                                // each 10 elements processed we record the statistics for that email and update the queue
				if($this->nbprocess%10 == 0){
					$this->_statsAdd($stats_add);
					$this->_queueUpdate($queue_update);
					$stats_add = array();
					$queue_update = array();
				}

			}else{
                                // email has not been sent and the mailer class returned an error
                                $this->errorSend ++;
				$new_try = false;
				if(in_array($helper_mailer->errorNumber,$helper_mailer->errorNewTry)){
                                    // we check if that's still possible to have a new try at sending the email
                                    if(empty($max_try) OR $queue_item->number_try < $max_try-1){
                                        $new_try = true;
                                        $other_message = sprintf(__('Next try in %s minutes.',WYSIJA),round($this->config->getValue('queue_delay')/60));
                                    }
                                    if($helper_mailer->errorNumber == 1) $this->consecutiveError ++;
                                    if($this->consecutiveError == 2) sleep(1);
				}

                                //if it's not possible to have a new try at sending the email then we delete the email from the queue and we add some stats
				if(!$new_try){
                                    $queue_delete[$queue_item->email_id][] = $queue_item->user_id;
                                    $stats_add[$queue_item->email_id][0][(int)@$helper_mailer->sendHTML][] = $queue_item->user_id;
                                    if($helper_mailer->errorNumber == 1 AND $this->config->getValue('bounce_action_maxtry')){
                                        $queue_delete_ok = $this->_deleteQueue($queue_delete);
                                        $queue_delete = array();

                                        // ADDED to add the stats immediately instead of waiting
                                        $this->_statsAdd($stats_add);
					$stats_add = array();

                                        // this is not used actually it would be used if we had a rule for the bounce max try case scenario
                                        // this is used in acy not in wysija
                                        //$other_message .= $this->_subscriberAction($queue_item->user_id);

                                        $other_message .='';
                                    }
				}else{
                                    $queue_update[$queue_item->email_id][] = $queue_item->user_id;
				}
                                WYSIJA::log('helpers -> Queue ->process ->sendOne resultFAILED(queue update)',$queue_update,'queue_process');
			}
			//$messageOnScreen = '['.$oneQueue->email_id.'] '.$mailHelper->reportMessage;
                        $message_on_screen = $helper_mailer->reportMessage;
			if(!empty($other_message)) $message_on_screen .= ' => '.$other_message;
			$this->_display($message_on_screen,$result,$current_mail);
			if(!$queue_delete_ok){
                            $this->finish = true;
                            break;
			}

                        // when we reach the execution time limit we just refresh the screen
			if(!empty($this->stoptime) AND $this->stoptime < time()){
                            $this->_display(__('Process refreshed to avoid a time limit.',WYSIJA));
                            if($this->nbprocess < count($queue_elements)) $this->finish = false;
                            break;
			}

                        // too many consecutive errors compared to th enumber of success
			if($this->consecutiveError > 2 AND $this->successSend>3){
                            $this->_display(__('Process refreshed to avoid a possible loss of connection.',WYSIJA));
                            break;
			}

                        // if there are too many consecutive errors or the connection has been aborted by the client we finish the request
			if($this->consecutiveError > 5 OR connection_aborted()){
                            $this->finish = true;
                            break;
			}
		}

                //delete the elements from the queue and add statistics and update the queue
		$this->_deleteQueue($queue_delete);
		$this->_statsAdd($stats_add);
		$this->_queueUpdate($queue_update);
		if($this->config->getValue('smtp_keepalive',1)) $helper_mailer->SmtpClose();

		if(!empty($this->total) AND $current_mail >= $this->total){
			$this->finish = true;
		}
		if($this->consecutiveError>5){
			$this->_handleError();
			return false;
		}
		if($this->report && !$this->finish){
			echo '<script type="text/javascript" language="javascript">handlePause();</script>';


		}
		if($this->report){
			echo '</body></html>';
                        exit;
		}
		return true;
	}

        /**
         * erase an array of queue elements from the database
         * @param array $queue_delete
         * @return boolean
         */
	function _deleteQueue($queue_delete){
            if(empty($queue_delete)) return true;
            $status = true;
            $modelQ=WYSIJA::get('queue','model');
            foreach($queue_delete as $email_id => $subscribers){
                $nbsub = count($subscribers);
                //$res=$modelQ->delete(array("email_id"=>intval($email_id),"user_id"=>$subscribers));
                //$realquery='DELETE a.* FROM `[wysija]queue` as a LEFT JOIN `[wysija]email` as b on a.email_id = b.email_id WHERE b.email_id IS NULL';

                $real_query='DELETE FROM `[wysija]queue` WHERE email_id = '.intval($email_id).' AND user_id IN ('.implode(',',$subscribers).') LIMIT '.$nbsub;
                WYSIJA::log('helpers -> Queue ->process ->deleteQueue',$real_query,'queue_process');
                $res=$modelQ->query($real_query);

                if(!$res){
                    $status = false;
                    WYSIJA::log('helpers -> Queue ->process ->deleteQueue failed',true,'queue_process');
                    //$this->_display($this->db->getErrorNum.' : '.$this->db->getErrorMsg());
                }else{
                    WYSIJA::log('deleting queue ok',array('email_id'=>$email_id,'subscribers'=>$subscribers),'queue_process');
                    $nbdeleted = $modelQ->getAffectedRows();
                    if($nbdeleted != $nbsub){
                            $status = false;

                            $this->_display(__('Newsletters are already being sent. Your latest newsletter will be sent afterwards.',WYSIJA));

                    }
                }
            }


            return $status;
	}

        /**
         * insert statistics entries in the database
         * @param array $stats_add
         * @return boolean
         */
	function _statsAdd($stats_add){
            $time = time();
            if(empty($stats_add)) return true;
            $modelEUS=WYSIJA::get('email_user_stat','model');

            foreach($stats_add as $email_id => $infos){
                $email_id = intval($email_id);

                //Email.sent_at should be the last sending time
                $query = 'UPDATE `[wysija]email` SET sent_at = '.$time.' WHERE email_id = '.$email_id;
                $modelEUS->query($query);

                //User stat
                foreach($infos as $status => $infosSub){
                    foreach($infosSub as $html => $subscribers){

                        // if not status that means it failed sending so we record it as -2 otherwise it sent properly we record as 0 and will increment step by step
                        if(!$status) $status=-2;
                        else $status=0;

                        $query = 'INSERT IGNORE INTO `[wysija]email_user_stat` (email_id,user_id,status,sent_at) VALUES ('.$email_id.','.implode(','.$status.','.$time.'),('.$email_id.',',$subscribers).','.$status.','.$time.')';
                        $modelEUS->query($query);
                    }
                }
            }
	}


        /**
         * update the queued elements which failed and need more tries
         * @param array $queue_update
         * @return boolean
         */
	function _queueUpdate($queue_update){
		if(empty($queue_update)) return true;
		$delay = $this->config->getValue('queue_delay',3600);
                $model_queue=WYSIJA::get('queue','model');
		foreach($queue_update as $email_id => $subscribers){
			$query = 'UPDATE `[wysija]queue` SET send_at = send_at + '.$delay.', number_try = number_try +1 WHERE email_id = '.$email_id.' AND user_id IN ('.implode(',',$subscribers).')';
			$model_queue->query($query);
		}
	}

        /**
         *
         */
	function _handleError(){
            $this->finish = true;
            $message = __('The Send Process stopped because there are too many errors.',WYSIJA);
            $message .= '<br/>';
            $message .= __('We kept all non delivered emails in the queue, so you will be able to resume the send process later.',WYSIJA);
            $message .= '<br/>';
            if($this->report){
                if(empty($this->successSend) AND empty($this->start)){
                    $message .= __('Please verify your mail configuration and make sure you can send a test of this email.',WYSIJA);
                    $message .= '<br/>';
                    $message .= __('If you recently, successfully, sent a lot of emails, those errors may also be due to your server limitations.',WYSIJA);
                }else{
                    $message .= __('Your server apparently refuses to send more emails.',WYSIJA);
                    $message .= '<br/>';
                }
            }
            $this->_display($message);
	}


        /**
         *
         * @param type $message
         * @param type $status
         * @param type $num
         * @return type
         */
	function _display($message,$status = '',$num = ''){
            $this->messages[] = strip_tags($message);
            if(!$this->report) return;
            if(!empty($num)){
                $color = $status ? 'black' : 'red';
                echo '<br/>'.$num.' : <font color="'.$color.'">'.$message.'</font>';
            }else{
                echo '<script type="text/javascript" language="javascript">setInfo(\''. addslashes($message) .'\')</script>';
            }
            if(function_exists('ob_flush')) @ob_flush();
            @flush();
	}

        /**
         * clear the queue from fucked up things
         * @return boolean
         */
        function clear(){

            $model_config = WYSIJA::get('config','model');
            $model_queue = WYSIJA::get('queue','model');

            //remove queued emails of unsubscribed users
            $real_query = 'DELETE a.* FROM `[wysija]queue` as a LEFT JOIN `[wysija]user` as b on a.user_id = b.user_id WHERE b.status< '.$model_config->getValue('confirm_dbleoptin');
            $model_queue->query($real_query);

            //remove queued emails of deleted emails
            $real_query = 'DELETE a.* FROM `[wysija]queue` as a LEFT JOIN `[wysija]email` as b on a.email_id = b.email_id WHERE b.email_id IS NULL';
            $model_queue->query($real_query);

            //remove queued emails of deleted users
            $real_query = 'DELETE a.* FROM `[wysija]queue` as a LEFT JOIN `[wysija]user` as b on a.user_id = b.user_id WHERE b.user_id IS NULL';
            $model_queue->query($real_query);

            //finally check if there are any queued emails left that stays unsent from 2 days ago
            $conditions = array();
            $conditions['less'] = array( 'send_at' => time() - (3600*48) );

            // replaced the exists function with a count
            $model_queue->setConditions( $conditions );
            $count_late_in_queue = $model_queue->count();

            if( $count_late_in_queue > 1000 ){
                //send message your queue cannot send very fast
                $model_config->save( array( 'queue_sends_slow' => 1 ) );
            }

            return true;
        }

        /**
         * function not used in wysija but in acy to handle the bounce max try case scenario
         * @param type $subid
         * @return string
         *
	function _subscriberAction($subid){
            return '';
                // we don't have a rule bounce_action_maxtry
                if($this->config->getValue('bounce_action_maxtry') == 'delete'){
			$this->subClass->delete($subid);
			return ' user '.$subid.' deleted';
		}
                $listId = 0;
                if(in_array($this->config->getValue('bounce_action_maxtry'),array('sub','remove','unsub'))){
                        $status = $this->subClass->getSubscriptionStatus($subid);
                }
                $message = '';
                $modelU=WYSIJA::get('user','model');
		switch($this->config->getValue('bounce_action_maxtry')){
			case 'sub' :
				$listId = $this->config->getValue('bounce_action_lists_maxtry');
				if(!empty($listId)){
					$message .= ' user '.$subid.' subscribed to '.$listId;
		            if(empty($status[$listId])){
						$this->listsubClass->addSubscription($subid,array('1' => array($listId)));
		            }elseif($status[$listId]->status != 1){
					 	$this->listsubClass->updateSubscription($subid,array('1' => array($listId)));
		            }
				}
			case 'remove' :
				$unsubLists = array_diff(array_keys($status),array($listId));
				if(!empty($unsubLists)){
					$message .= ' | user '.$subid.' removed from lists '.implode(',',$unsubLists);
					$this->listsubClass->removeSubscription($subid,$unsubLists);
				}else{
					$message .= ' | user '.$subid.' not subscribed';
				}
				break;
			case 'unsub' :
				$unsubLists = array_diff(array_keys($status),array($listId));
				if(!empty($unsubLists)){
					$message .= ' | user '.$subid.' unsubscribed from lists '.implode(',',$unsubLists);
					$this->listsubClass->updateSubscription($subid,array('-1' => $unsubLists));
				}else{
					$message .= ' | user '.$subid.' not subscribed';
				}
				break;
			case 'delete' :
				$message .= ' | user '.$subid.' deleted';
				//$this->subClass->delete($subid);
                                $modelU->delete($subid);
                                $modelU->reset();
				break;
			case 'block' :
				$message .= ' | user '.$subid.' blocked';
				$modelU->query('UPDATE `[wysija]user` SET `enabled` = 0 WHERE `user_id` = '.intval($subid));
				$modelU->query('DELETE FROM `[wysija]queue` WHERE `user_id` = '.intval($subid));
				break;
	      }
		return $message;
	}*/

}
