<?php
defined('WYSIJA') or die('Restricted access');

class WYSIJA_help_rules extends WYSIJA_help{
	var $tables = array('rules');
	var $pkey = 'ruleid';
	var $errors = array();
        var $defaultrules=array();
        function __construct(){
            $forwardEmail="";
            $forwardEmail=count(str_split($forwardEmail)).':"'.$forwardEmail.'"';
            /*
            $this->defaultrules[]=array("name"=>__('Feedback loop',WYSIJA),
                                        "title"=>__('When feedback loop',WYSIJA),
                                        "regex"=>'feedback|staff@hotmail.com',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "senderinfo"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1,
                                            "save"=>1
                                            ),
                                        "action_user"=>"unsub",
                                         "action_user_min"=>0
                                        );*/

            $this->defaultrules[]=array("order_display"=>0,"key"=>"mailbox_full","name"=>__('Mailbox Full',WYSIJA),
                                        "title"=>__('When mailbox is full',WYSIJA),
                                        "regex"=>'((mailbox|mailfolder|storage|quota|space) *(is)? *(over)? *(exceeded|size|storage|allocation|full|quota|maxi))|((over|exceeded|full) *(mail|storage|quota))',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1,
                                            "save"=>1
                                            ),
                                        "action_user_min"=>3,
                                         "action_user_stats"=>1);
            $this->defaultrules[]=array("order_display"=>1,"key"=>"mailbox_na","name"=>__('Mailbox not available',WYSIJA),
                                        "title"=>__('When mailbox is not available',WYSIJA),
                                        "regex"=>'(Invalid|no such|unknown|bad|des?activated|undelivered|inactive|unrouteable|delivery|mail ID|failed to|may not|no known user|email account) *(mail|destination|recipient|user|address|person|failure|has failed|does not exist|deliver to|exist|with this email|is closed)|RecipNotFound|status(-code)? *(:|=)? *5\.(1\.[1-6]|0\.0|4\.[0123467])|(user|mailbox|address|recipients?|host|account|domain) *(is|has been)? *(error|disabled|failed|unknown|unavailable|not *(found|available)|.{1,30}inactiv)|recipient *address *rejected|does *not *like *recipient|no *mailbox *here|user does.?n.t have.{0,20}account',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1,
                                            "save"=>1
                                            ),
                                        "action_user_min"=>0,
                                         "action_user_stats"=>1
                                            );

            $this->defaultrules[]=array("order_display"=>5,"behave"=>"mailbox_na","key"=>"message_delayed","name"=>__('Message delayed',WYSIJA),
                                        "title"=>__('When message is delayed',WYSIJA),
                                        "regex"=>'possible *mail *loop|too *many *hops|Action: *delayed|has.*been.*delayed|delayed *mail|temporary *failure',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1,
                                            "save"=>1
                                            ),
                                        "action_user_min"=>3,
                                         "action_user_stats"=>1);



            $this->defaultrules[]=array("order_display"=>6,"behave"=>"mailbox_na","key"=>"failed_permanent","name"=>__('Failed Permanently',WYSIJA),
                                        "title"=>__('When failed permanently',WYSIJA),
                                        "regex"=>'failed *permanently|permanent *(fatal)? *(failure|error)|Unrouteable *address|not *accepting *(any)? *mail',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1,
                                            "save"=>1
                                            ),
                                        "action_user_min"=>0,
                                         "action_user_stats"=>1
                                            );

           /* $this->defaultrules[]=array("order_display"=>2,
                                        "name"=>__('Out of office',WYSIJA),
                                        "title"=>__('When out of office detected',WYSIJA),
                                        "key"=>"is_out_office",
                                        "regex"=>'(out|away|on) .*(of|from|leave)|office|vacation|holiday|absen|congÃˆs|recept|acknowledg|thank you for',
                                         "executed_on"=>array(
                                            "subject"=>1,
                                             "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1
                                            ),
                                        //"action_user_min"=>0
                );*/

            $this->defaultrules[]=array("order_display"=>3,"key"=>"action_required","name"=>__('Action Required',WYSIJA),
                                        "title"=>__('When you need to confirm you\'re a human being, forward to:',WYSIJA),
                                        "regex"=>'action *required|verif',
                                        "forward"=>1,
                                         "executed_on"=>array(
                                            "subject"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1
                                            ),
                                        "action_user_min"=>0);

            $this->defaultrules[]=array("order_display"=>4,"key"=>"blocked_ip","name"=>__('Blocked IP',WYSIJA),
                                        "forward"=>1,
                                        "title"=>__('When you are flagged as a spammer forward the bounced message to',WYSIJA),
                                        "regex"=>'is *(currently)? *blocked *by|block *list|spam *detected|(unacceptable|banned|offensive|filtered|blocked) *(content|message|e-?mail)|administratively *denied',
                                         "executed_on"=>array(
                                            "body"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1
                                            ),
                                        "action_user_min"=>0);



            $this->defaultrules[]=array("order_display"=>7,"key"=>"nohandle","name"=>'Final Rule',
                                        "title"=>__('When the bounce is weird and we\'re not sure what to do, forward to:',WYSIJA),
                                        "forward"=>1,
                                        "regex"=>'.',
                                         "executed_on"=>array(
                                             "senderinfo"=>1,
                                             "subject"=>1
                                            ),
                                        "action_message"=>array(
                                            "delete"=>1
                                            ),
                                        "action_user_min"=>0,
                                        "action_user_stats"=>1);

            $model_config=WYSIJA::get('config','model');
            $prefix_ms='';
            if(is_multisite()) $prefix_ms='ms_';
            foreach($this->defaultrules as $ki =>$vi){
               //if a rule is defined
               if(isset($model_config->values[$prefix_ms.'bounce_rule_'.$vi['key']])){
                   if($model_config->values[$prefix_ms.'bounce_rule_'.$vi['key']]!=''){
                       $this->defaultrules[$ki]['action_user']=$model_config->values[$prefix_ms.'bounce_rule_'.$vi['key']];
                   }
               }

               //if a forwarded message is detected
               if(isset($model_config->values[$prefix_ms.'bounce_rule_'.$vi['key'].'_forwardto'])){
                   if($model_config->values[$prefix_ms.'bounce_rule_'.$vi['key'].'_forwardto']!=''){
                       $this->defaultrules[$ki]['action_message_forwardto']=$model_config->values[$prefix_ms.'bounce_rule_'.$vi['key'].'_forwardto'];
                   }
               }
            }

        }

	function getRules($single=false,$display=false){
		$rules = $this->defaultrules;
                if($single){
                    foreach($rules as $id => $rule){
			if($rule['key']==$single) return $this->_prepareRule($rule,$id);
                    }
                }else{
                    if($display){
                        $newrules=array();
                        foreach($rules as $id => $rule){

                            if(isset($rule['order_display']))   $newrules[$rule['order_display']] = $this->_prepareRule($rule,$id);
                            else $newrules[rand(99,130)] = $this->_prepareRule($rule,$id);
                        }
                        $rules=$newrules;
                        ksort($rules);
                    }else{
                      foreach($rules as $id => $rule){
                            $rules[$id] = $this->_prepareRule($rule,$id);
                        }
                    }

                    return $rules;
                }

	}

	function _prepareRule($rule,$id){
		$vals = array('executed_on','action_message','action_user','action_user_min','action_user_stats','action_user_block');

		foreach($vals as $oneVal){
                    if(!empty($rule[$oneVal])) {
                        $rule[$oneVal] = $rule[$oneVal];
                    }
		}
                $rule['id']=$id;
		return $rule;
	}

}
