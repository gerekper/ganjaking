<?php
defined( 'WYSIJA' ) or die( 'Restricted access' );

class WYSIJA_help_update extends WYSIJA_object {

	function __construct(){
		$this->modelWysija = new WYSIJA_model();

		//IMPORTANT when making db updated or running update processes, add the version and in the big switch below in the runUpdate() method
		$this->updates = array(
			'1.1',
			'2.0','2.1','2.1.6','2.1.7','2.1.8',
			'2.2','2.2.1',
			'2.3.3','2.3.4',
			'2.4', '2.4.1', '2.4.3','2.4.4',
			'2.5','2.5.2','2.5.5','2.5.9.6', '2.5.9.7',
			'2.6', '2.6.0.8', '2.6.15', '2.7.15', '2.11'
		);
	}

	function runUpdate( $version ) {
		//run all the updates missing since the db-version
		//foreach ... $this->updateVersion($version);
		switch($version){
			case '1.1':
				//add column namekey to
				$model_config=WYSIJA::get('config','model');
				if(!$this->modelWysija->query("SHOW COLUMNS FROM `[wysija]list` LIKE 'namekey';")){
					$querys[]='ALTER TABLE `[wysija]list` ADD `namekey` VARCHAR( 255 ) NULL;';
				}

				$querys[]="UPDATE `[wysija]list` SET `namekey` = 'users' WHERE `list_id` =".$model_config->getValue('importwp_list_id').";";
				$errors=$this->runUpdateQueries($querys);

				$importHelp=WYSIJA::get('plugins_import','helper');
				$importHelp->testPlugins();

				// move data
				$installHelper = WYSIJA::get('install', 'helper');
				$installHelper->moveData('dividers');
				$installHelper->moveData('bookmarks');
				$installHelper->moveData('themes');

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;
			case '2.0':
				//add column namekey to
				$model_config=WYSIJA::get('config','model');
				if(!$this->modelWysija->query("SHOW COLUMNS FROM `[wysija]email` LIKE 'modified_at';")){
					$querys[]="ALTER TABLE `[wysija]email` ADD `modified_at` INT UNSIGNED NOT NULL DEFAULT '0';";
				}
				if(!$model_config->getValue('update_error_20')){
					$querys[]="UPDATE `[wysija]email` SET `modified_at` = `sent_at`  WHERE `sent_at`>=0;";
					$querys[]="UPDATE `[wysija]email` SET `modified_at` = `created_at` WHERE `modified_at`='0';";
					$querys[]="UPDATE `[wysija]email` SET `status` = '99' WHERE `status` ='1';";//change sending status from 1 to 99
				}


				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$model_config->save(array('update_error_20'=>true));
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;
			case '2.1':
				$model_config=WYSIJA::get('config','model');
				if(!$model_config->getValue('update_error_21')){
					$modelEmails=WYSIJA::get('email','model');
					$modelEmails->reset();
					$emailsLoaded=$modelEmails->get(array('subject','email_id'),array('status'=>2,'type'=>1));

					//set the default new role caps for super admin and admin
					$wp_tools_helper = WYSIJA::get('wp_tools', 'helper');
					$wp_tools_helper->set_default_rolecaps();

					//based on the config values for role_campaign and role_subscribers
					//let's give the each core roles the right capability

					$minimumroles=array('role_campaign'=>'wysija_newsletters','role_subscribers'=>'wysija_subscribers');

					foreach($minimumroles as $rolename=>$capability){
						$rolesetting=$model_config->getValue($rolename);
						switch($rolesetting){
							case 'switch_themes':
								$keyrole=1;
								break;
							case 'moderate_comments':
								$keyrole=3;
								break;
							case 'upload_files':
								$keyrole=4;
								break;
							case 'edit_posts':
								$keyrole=5;
								break;
							case 'read':
								$keyrole=6;
								break;
							default:
								$keyrole=false;
						}

						if(!$keyrole){
							//add the setting to a custom role
							$role = get_role($rolesetting);
							//added for invalid roles ...
							if($role){
								$role->add_cap( $capability );
							}
						}else{
							//get all the minimum roles getting that capability
							$editable_roles=$wp_tools_helper->wp_get_roles();
							$startcount=1;
							if(!isset($editable_roles[$startcount])) $startcount++;
							for($i = $startcount; $i <= $keyrole; $i++) {
								$rolename=$editable_roles[$i];
								//add the setting to a custom role
								$role = get_role($rolename['key']);
								$role->add_cap( $capability );
							}
						}
					}
					$helper_toolbox = WYSIJA::get('toolbox', 'helper');
					$model_config->save(array('dkim_domain'=>$helper_toolbox->_make_domain_name()));
				}

				if(!$this->modelWysija->query("SHOW COLUMNS FROM `[wysija]list` LIKE 'is_public';")){
					$querys[]="ALTER TABLE `[wysija]list` ADD `is_public` TINYINT UNSIGNED NOT NULL DEFAULT 0;";
					$errors=$this->runUpdateQueries($querys);
					if($errors){
						$model_config->save(array('update_error_21'=>true));
						$this->error(implode($errors,"\n"));
						return false;
					}
				}
				return true;
			break;
			case '2.1.6':
				$querys[]="UPDATE `[wysija]user_list` as A inner join `[wysija]user` as B on (A.user_id= B.user_id) set A.sub_date= B.created_at where A.sub_date=0 and A.unsub_date=0 and B.status>-1;";
				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;
			case '2.1.7':
				$querys[]='UPDATE `[wysija]user_list` as A inner join `[wysija]user` as B on (A.user_id= B.user_id) set A.sub_date= '.time().' where A.sub_date=0 and B.status>-1;';
				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;
		   case '2.1.8':
			   $mConfig=WYSIJA::get('config','model');

			   $querys[]='UPDATE `[wysija]user_list` as A set A.sub_date= '.time().' where A.list_id='.$mConfig->getValue('importwp_list_id').';';
				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;
		   case '2.2':
			   $mConfig=WYSIJA::get('config','model');

			   //let's rename the Synched WordPress list into WordPress Users
			   $mList=WYSIJA::get('list','model');
			   $mList->update(array('name'=>'WordPress Users'),array('list_id'=>$mConfig->getValue('importwp_list_id'), 'namekey'=>'users'));

			   //remove subscribers that should not be in the WordPress Users list
			   $querys[]='DELETE FROM `[wysija]user_list` WHERE `list_id` = '.$mConfig->getValue('importwp_list_id').' AND `user_id` in ( SELECT user_id FROM `[wysija]user` where wpuser_id=0 );';
				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}

				return true;
			   break;
		   case '2.2.1':
				$helperU=WYSIJA::get('user','helper');
				$helperU->cleanWordpressUsersList();

				return true;
			   break;
		   case '2.3.3':
				update_option('wysija_log', '');

				return true;
			   break;


		   case '2.3.4':
				$model_config=WYSIJA::get('config','model');

				$dbl_optin=(int)$model_config->getValue('confirm_dbleoptin');
				//count issue for people who went through all of the versions and now are left with some users in a limbo
				$querys[]='UPDATE `[wysija]user_list` as A inner join `[wysija]user` as B on (A.user_id = B.user_id) set A.sub_date= '.time().' where A.sub_date=0 and A.unsub_date=0 and B.status>='.$dbl_optin.';';
				$errors=$this->runUpdateQueries($querys);

				if($errors){
					$this->error(implode($errors,"\n"));
					return false;
				}
				return true;
				break;

			case '2.4':
				$queries = array();
				$queries[] = 'CREATE TABLE IF NOT EXISTS `[wysija]form` ('.
					'`form_id` INT unsigned AUTO_INCREMENT NOT NULL,'.
					'`name` tinytext COLLATE utf8_bin,'.
					'`data` longtext COLLATE utf8_bin,'.
					'`styles` longtext COLLATE utf8_bin,'.
					'`subscribed` int(10) unsigned NOT NULL DEFAULT "0",'.
					'PRIMARY KEY (`form_id`)'.
				') /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/';

				$errors = $this->runUpdateQueries($queries);

				if($errors) {
					$this->error(implode($errors,"\n"));
					return false;
				} else {
					// the table should be created now. let's make sure:
					if((bool)$this->modelWysija->query('SHOW TABLES LIKE "[wysija]form";') === false) {
						return false;
					} else {
						// the form table has been successfully created, let's convert all previously added widgets
						$widgets_converted = $this->convert_widgets_to_forms();
						if($widgets_converted === 0) {
							$helper_install = WYSIJA::get('install', 'helper');
							$helper_install->create_default_subscription_form();
						}
					}
				}

				return true;
				break;

			case '2.4.1':
				$model_email=WYSIJA::get('email','model');
				$model_email->setConditions(array('type'=>'2'));
				$emails = $model_email->getRows(array('email_id','params'));

				// we don't want parent post notification to send emails
				foreach($emails as $email){
					$model_email->getParams($email);
					if(isset($email['params']) && $email['params']['autonl']['event']=='new-articles'){
						$model_queue=WYSIJA::get('queue','model');
						$model_queue->delete(array('email_id'=>$email['email_id']));
					}
				}
				return true;

			break;

			case '2.4.3':
				// convert data for previously saved forms
				/*
				* 1. get all forms and loop through each form
				* 2. check data['settings']['success_message'] is base64 valid -> decode it
				* 3. loop through data['body'], if type="text" -> is base64 valid -> decode it
				* 4. save form
				*/
				$model_forms = WYSIJA::get('forms', 'model');
				$forms = $model_forms->getRows();
				if(is_array($forms) && count($forms) > 0) {

					foreach ($forms as $i => $form) {
						$requires_update = false;

						// decode form data
						$data = unserialize(base64_decode($form['data']));

						// convert success_message if necessary
						if(strlen($data['settings']['success_message']) % 4 === 0 && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data['settings']['success_message'])) {
							// this is a potential base64 string so decode it
							$data['settings']['success_message'] = base64_decode($data['settings']['success_message']);

							$requires_update = true;
						}

						// loop through each block
						foreach ($data['body'] as $j => $block) {
							// in case we find a text block
							if($block['type'] === 'text') {
								// convert text if necessary
								if(strlen($block['params']['text']) % 4 === 0 && preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $block['params']['text'])) {

									// this is a potential base64 string so decode it
									$data['body'][$j]['params']['text'] = base64_decode($block['params']['text']);
									$requires_update = true;
								}
							}
						}

						// if the form requires update, let's do it
						if($requires_update === true) {
							$model_forms->reset();
							$model_forms->update(array('data' => base64_encode(serialize($data))), array('form_id' => (int)$form['form_id']));
						}
					}
				}

				return true;
			break;
			case '2.4.4':
				// we now have steps in the installation process to make sure we don't rerun two processes
				WYSIJA::update_option('installation_step', '16');
				return true;
				break;

			case '2.5':
				// set the default new role caps for super admin and admin
				$wp_tools_helper = WYSIJA::get('wp_tools', 'helper');
				$wp_tools_helper->set_default_rolecaps();

				// get the main site bounce settings and save them to the global ms settings
				if(is_multisite()){
					$main_blog_id=1;
					switch_to_blog( $main_blog_id );
					$main_site_encoded_option = unserialize(base64_decode(get_option( 'wysija' )));
					restore_current_blog();

					$data_bounce=array();
					foreach($main_site_encoded_option as $key => $val){
						if((strpos($key, 'bounce_')!==false || strpos($key, 'bouncing_')!==false) && !empty($val)) $data_bounce['ms_'.$key]=$val;
					}

					$data_saved_ms_before= unserialize(base64_decode(get_site_option('ms_wysija')));

					// we don't want to run that multiple times
					if(empty($data_saved_ms_before['ms_bounce_host'])){
						if(!empty($data_saved_ms_before))   $data_bounce=array_merge($data_saved_ms_before, $data_bounce);
						update_site_option('ms_wysija',base64_encode(serialize($data_bounce)));
					}

				}

				// Sending method: remove "once a day" & "twice daily",  fallback on "every 2 hours"
				$model_config = WYSIJA::get('config','model');
				$removed_sending_methods = array('twicedaily','daily');
				$target_sending_method = 'two_hours';
				if(in_array($model_config->getValue('sending_emails_each'), $removed_sending_methods))
					$model_config->save(array('sending_emails_each'=>$target_sending_method));
				if(in_array($model_config->getValue('ms_sending_emails_each'), $removed_sending_methods))
					$model_config->save(array('ms_sending_emails_each'=>$target_sending_method));

				return true;
				break;

			case '2.5.2':
				$queries   = array();
				$queries[] = 'UPDATE `[wysija]user_list` AS A JOIN `[wysija]user` AS B ON A.user_id = B.user_id SET A.unsub_date = 0, A.sub_date = '.time().' WHERE STATUS = 1 AND sub_date =0';
				$errors    = $this->runUpdateQueries( $queries );

				if ( $errors ) {
					$this->error( implode( $errors, "\n" ) );
					return false;
				}
				return true;
			break;

			case '2.5.5':
				$model_email  = WYSIJA::get( 'email', 'model', false, 'wysija-newsletters', false );
				$model_config = WYSIJA::get( 'config', 'model' );

				$model_email->update(
					array(
						'replyto_name' => $model_config->getValue( 'replyto_name' ),
						'replyto_email' => $model_config->getValue( 'replyto_email' ),
					),
					array(
						'email_id' => $model_config->getValue( 'confirm_email_id' ),
					)
				);
				return true;
			break;

			case '2.5.9.6':
				$alter_queries   = array();
				$alter_queries[] = 'ALTER TABLE [wysija]user ADD COLUMN `domain` VARCHAR(255);';
				$errors    = $this->run_update_queries( $alter_queries );

				if ( $this->does_column_exist( 'domain', '[wysija]user' ) ) {
					$queries   = array();
					$queries[] = "UPDATE [wysija]user SET `domain` = SUBSTRING(`email`,LOCATE('@',`email`)+1);";
					$errors    = $this->run_update_queries( $queries );
					if ( $errors ) {
						$this->error( implode( $errors, "\n" ) );
						return false;
					}

				} else {
									if(!empty($errors)){
										$this->error( implode( $errors, "\n" ) );
									}
									return false;
				}

				return true;
			break;

			case '2.5.9.7':
				$queries = array();

				//add column namekey to
				$model_config = WYSIJA::get( 'config', 'model' );
				$columns_to_add_to_user_table = array(
					'confirmed_ip' => 'VARCHAR(100) NOT NULL DEFAULT 0',
					'confirmed_at' => 'INT unsigned NULL',
					'last_opened' => 'INT unsigned NULL',
					'last_clicked' => 'INT unsigned NULL',
				);

				foreach ( $columns_to_add_to_user_table as $column_to_add => $sql_definition ){
					if ( ! $this->modelWysija->query( "SHOW COLUMNS FROM `[wysija]user` LIKE '{$column_to_add}';" ) ){
						$queries[] = "ALTER TABLE `[wysija]user` ADD `{$column_to_add}` {$sql_definition};";
					}
				}

				// Custom Fields main table.
				$queries[] = 'CREATE TABLE IF NOT EXISTS `[wysija]custom_field` ('.
					'`id` mediumint(9) NOT NULL AUTO_INCREMENT,'.
					'`name` tinytext NOT NULL,'.
					'`type` tinytext NOT NULL,'.
					'`required` tinyint(1) DEFAULT "0" NOT NULL,'.
					'`settings` text DEFAULT NULL,'.
					'PRIMARY KEY (`id`)'.
					') /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/';

				$errors = $this->runUpdateQueries( $queries );

				if ( $errors ) {
					$this->error( implode( $errors, "\n" ) );
					return false;
				}
				// update the new page_selection options with the default MailPoet's page
				$default_page_id = $model_config->getValue( 'confirm_email_link' );
				$model_config->save(
					array(
						'confirmation_page' => $default_page_id,
						'unsubscribe_page' => $default_page_id,
						'subscriptions_page' => $default_page_id,
					)
				);

				return true;
				break;

			case '2.6':
			case '2.6.0.8':
				$queries = array();

				// Correct links which are missing 'h'
				$queries[] = "UPDATE `[wysija]url` SET `url` = CONCAT('h', `url`) WHERE `url` LIKE 'ttp://%'";
				$errors    = $this->run_update_queries( $queries );

				if ( $errors ) {
					$this->error( implode( $errors, "\n" ) );
					return false;
				}

				return true;
			break;

                        case '2.6.15':
                            global $wpdb;
                            $sql = "SHOW INDEX FROM [wysija]user_list WHERE Key_name = 'user_id'";
                            $result_index = $wpdb->get_results(str_replace('[wysija]',$this->modelWysija->getPrefix(),$sql));

                            $queries = array();
                            //create an INDEX only if it doesn't exist already
                            if( empty( $result_index ) ){
                                $queries[] = 'ALTER TABLE [wysija]user_list ADD INDEX `user_id` ( `user_id` ) ';
                                $errors = $this->run_update_queries( $queries );

                                if( !empty($errors) ){
                                    $this->error( implode( $errors, "\n" ) );
                                }
                            }
                            return true;
                         break;

			case '2.7.15':
				$queries = array();

				// Subscriber IPs table.
				$queries[] = 'CREATE TABLE IF NOT EXISTS `[wysija]subscriber_ips` ('.
          '`ip` varchar(45) NOT NULL,'.
          '`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,'.
          'PRIMARY KEY  (`created_at`, `ip`),'.
          'KEY ip (`ip`)'.
					') /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/';

				$errors = $this->run_update_queries( $queries );

				if ( $errors ) {
					$this->error( implode( $errors, "\n" ) );
					return false;
				}

				return true;
				break;

			case '2.11':
				$alter_queries   = array();
				$alter_queries[] = 'ALTER TABLE [wysija]user ADD COLUMN `count_confirmations` INT unsigned NOT NULL DEFAULT 0;';
				$errors    = $this->run_update_queries( $alter_queries );

				if ( $errors ) {
					$this->error( implode( $errors, "\n" ) );
					return false;
				}

				return true;
        break;

			default:
				return false;
		}
		return false;
	}


	/**
	 *
	 * @return type
	 */
	function check(){

		//we can go there if this is not a new installation and it's an admin
		if(WYSIJA::current_user_can('switch_themes') ){

			// check that the redirection is only processed from wysija's interfaces
			if(isset($_REQUEST['page']) && in_array($_REQUEST['page'], array('wysija_config','wysija_campaigns','wysija_subscribers'))){

				// we are earlier than 1.1  or earlier than the current file version so we can run what's needed to reach the right version number
				$config=WYSIJA::get('config','model');

				if(!$config->getValue('wysija_db_version') || version_compare($config->getValue('wysija_db_version'),WYSIJA::get_version()) < 0){
					$this->update(WYSIJA::get_version());
				}
				// once the update procedure is done we can redirect to the what's new/fixed page
				$noredirect=false;

				//get the right option name based on the type of site we're in
				$whats_new_option='wysija_whats_new';
				$is_multisite=is_multisite();
				$is_network_admin=WYSIJA::current_user_can('manage_network');

				// check that in case of a multisite configuration only the network admin sees that update page
				if($is_multisite){
					if($is_network_admin){
						$whats_new_option='ms_wysija_whats_new';
					}else {
						return;
					}
				}

				// a whats_new option is set and it is less than the current version so that means we need to display it
				if((!$config->getValue($whats_new_option) || version_compare($config->getValue($whats_new_option),WYSIJA::get_version()) < 0)){

					//if there is an action set and it's one of those then we just don't redirect
					if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('whats_new','welcome_new','activate-plugin')))  $noredirect=true;

					if(!$noredirect) {
						$timeInstalled=$config->getValue('installed_time')+3600;
						//if it is a fresh install then it redirects to the welcome screen otherwise to the update one
						if(time()>$timeInstalled){
							WYSIJA::redirect('admin.php?page=wysija_mp3&arg=whats_new');
						}else{
							WYSIJA::redirect('admin.php?page=wysija_campaigns&action=welcome_new');
						}
					}
				}
			}


		}
	}

	/**
	 * this is a central function which will run every update until the current version
	 * @param string $version_current // not used since we use the wysija_db_version
	 * @return boolean
	 */
	function update($version_current){
		$config=WYSIJA::get('config','model');
		$config->getValue('wysija_db_version');

		foreach($this->updates as $version){
			if(version_compare($config->getValue('wysija_db_version'),$version) < 0){
				if(!$this->runUpdate($version)){
					$this->error(sprintf(__('Update procedure to MailPoet version "%1$s" failed!',WYSIJA),$version),true);
					return false;
				}else{
					$config->save(array('wysija_db_version' => $version));
				}
			}
		}

	}

	/**
	 * return the failed queries
	 * @param type $queries
	 * @return type
	 * @deprecated since version 2.6
	 */
	function runUpdateQueries($queries){
	return $this->run_update_queries($queries);
	}

	/**
	 * run a list of specified queries and return a list of failed queries
	 * @param array $queries
	 * <pre>
	 * array(
	 *	0 => full_query_1,
	 *	1 => full_query_2,
	 *	...
	 *	n => full_query_n
	 * )
	 *
	 * @return false or array
	 * <pre>
	 * array(
	 *	0 => full_query_1,
	 *	1 => full_query_2,
	 *	...
	 *	n => full_query_n
	 * )
	 */
	function run_update_queries($queries) {
		$failed = array();

		//we use mysql query instead of wordpress query to make sure we don't miss the sql errors
		global $wpdb;
		foreach($queries as $query){
			$query=str_replace('[wysija]',$this->modelWysija->getPrefix(),$query);
			$last_error = $wpdb->last_error;
                        $wpdb->query($query);

			if( (empty($wpdb->result) || !$wpdb->result) && !empty( $wpdb->last_error ) && $last_error != $wpdb->last_error ){
				$failed[]= $wpdb->last_error." ($query)";
			}
		}
		if($failed) return $failed;
		else return false;
	}

	/**
	 * Detects if a column already exsits in a table
	 * @global wpdb $wpdb
	 * @param string $column_name name of the column
	 * @param string $table_name name of a table which the column should belong to
	 * @return boolean
	 */
	protected function does_column_exist($column_name, $table_name) {
	global $wpdb;
	$sql = "SHOW COLUMNS FROM `$table_name` LIKE '$column_name';";

	$results = $wpdb->get_results(str_replace('[wysija]',$this->modelWysija->getPrefix(),$sql));

	return (!empty($results));
	}


	/**
	 * Version 2.4 introduces the Form Editor
	 * This function converts all previously saved subscription widgets into forms
	 *
	 */
	function convert_widget_to_form($values = array()) {
		// $values needs to be an array
		if(!is_array($values)) return false;

		// make sure we don't convert wysija forms into wysija forms.... that would be silly isn't it?
		if(isset($values['form']) && (int)$values['form'] > 0) return false;

		$settings = $body = array();

		// SETTINGS
		// specify who selects the list (user | admin)
		if($values['autoregister'] === 'not_auto_register') {
			$settings['lists_selected_by'] = 'admin';
		} else {
			// the user will select his own list so let's add the list selection
			$settings['lists_selected_by'] = 'user';
		}

		// lists
		$settings['lists'] = $values['lists'];

		// success message
		$settings['on_success'] = 'message';
		$settings['success_message'] = $values['success'];

		// are the labels in or out?
		if($values['labelswithin'] === 'labels_within') {
			$label_within = true;
		} else {
			$label_within = false;
		}

		// The order of the fields were: firstname, lastname, email, list_selection, submit
		$blocks = array();

		// INSTRUCTIONS
		if(isset($values['instruction']) && strlen(trim($values['instruction'])) > 0) {
			$blocks[] = array(
				'params' => array(
					'text' => base64_encode($values['instruction']),
				),
				'type' => 'text',
				'field' => 'text',
				'name' => __('Random text or instructions', WYSIJA)
			);
		}

		// CUSTOM FIELDS (firstname, lastname, email)
		$has_email_field = false;
		foreach($values['customfields'] as $field => $params) {
			switch($field) {
				case 'firstname':
					$name = __('First name', WYSIJA);
					break;
				case 'lastname':
					$name = __('Last name', WYSIJA);
					break;
				case 'email':
					$has_email_field = true;
					$name = __('Email', WYSIJA);
					break;

			}

			$blocks[] = array(
				'name' => $name,
				'type' => 'input',
				'field' => $field,
				'params' => array(
					'label' => $params['label'],
					'required' => 1,
					'label_within' => (int)$label_within
				)
			);
		}

		// make really sure we have an email field
		if($has_email_field === false) {
			$blocks[] = array(
				'name' => __('Email', WYSIJA),
				'type' => 'input',
				'field' => 'email',
				'params' => array(
					'label' => __('Email', WYSIJA),
					'required' => 1,
					'label_within' => (int)$label_within
				)
			);
		}

		// LIST SELECTION (only if the user can pick his own lists)
		if($settings['lists_selected_by'] === 'user') {

			$list_values = array();

			foreach($settings['lists'] as $list_id) {
				$list_values[] = array(
					'list_id' => $list_id,
					'is_checked' => 1
				);
			}
			$blocks[] = array(
				'name' => __('List selection', WYSIJA),
				'type' => 'list',
				'field' => 'list',
				'params' => array(
					'label' => __('Select list(s):', WYSIJA),
					'values' => $list_values
				)
			);
		}

		// ADD SUBMIT BUTTON
		$submit_label = __('Subscribe!', WYSIJA);
		if(isset($values['submit']) && strlen(trim($values['submit'])) > 0) {
			$submit_label = $values['submit'];
		}
		$blocks[] = array(
			'name' => __('Submit', WYSIJA),
			'type' => 'submit',
			'field' => 'submit',
			'params' => array(
				'label' => $submit_label
			)
		);

		// Format body based on blocks
		for($i = 0, $count = count($blocks); $i < $count; $i++) {
			$body['block-'.($i + 1)] = array_merge($blocks[$i], array('position' => ($i + 1)));
		}

		// set default form name
		$form_name = __('New Form', WYSIJA);

		// check if title exists and override default form name
		if(isset($values['title']) && strlen(trim($values['title'])) > 0) {
			$form_name = $values['title'];
		}

		// form engine helper
		$helper_form_engine = WYSIJA::get('form_engine', 'helper');

		// insert form into db
		$model_forms = WYSIJA::get('forms', 'model');
		$model_forms->reset();
		// get form id back because it's required to generate the html form
		$form_id = $model_forms->insert(array('name' => $form_name));

		if((int)$form_id > 0) {
			$model_forms->reset();
			// set form engine data
			$helper_form_engine->set_data(array(
				'form_id' => (int)$form_id,
				'settings' => $settings,
				'body' => $body
			));

			// update form in database
			$model_forms->update(array('data' => $helper_form_engine->get_encoded('data')), array('form_id' => $form_id));

			return $form_id;
		} else {
			return false;
		}
	}

	/**
	 * very important function to convert subscription widget to our new forms in 2.4
	 * @return int
	 */
	function convert_widgets_to_forms() {
		$widgets_converted = 0;

		// get all wysija widgets
		$widgets = get_option('widget_wysija');

		foreach($widgets as $key => &$values) {
			$form_id = $this->convert_widget_to_form($values);
			if($form_id!==false) {
				$values['default_form'] = $form_id;
				$widgets_converted++;
			}
		}

		update_option('widget_wysija',$widgets);
		return $widgets_converted;
	}

	/**
	 * Deprecated Methods
	 */
	function checkForNewVersion(){
		/*
		$current = get_site_transient( 'update_plugins' );

		if ( !isset( $current->response[ $file ] ) )
			return false;

		$r = $current->response[ $file ];
			$default_headers = array(
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
			'Network' => 'Network',
		);

		$plugin_data = get_file_data( WP_PLUGIN_DIR . DS.$file, $default_headers, 'plugin' );

		$plugins_allowedtags = array('a' => array('href' => array(),'title' => array()),'abbr' => array('title' => array()),'acronym' => array('title' => array()),'code' => array(),'em' => array(),'strong' => array());
		$plugin_name = wp_kses( $plugin_data['Name'], $plugins_allowedtags );

		$details_url = self_admin_url((is_multisite()? 'network/' : '' ).'plugin-install.php?tab=plugin-information&plugin=' . $r->slug . '&section=changelog&TB_iframe=true&width=600&height=400');

		if(((is_multisite() && current_user_can('manage_network') ) || current_user_can('update_plugins') ) && !empty($r->package) ){
			$this->notice(
				sprintf(
					__('Hey! %1$s has an update (version %4$s), <a href="%5$s">click here to update</a>.', WYSIJA)
					, '<strong>'.$plugin_name.'</strong>',
					esc_url($details_url),
					esc_attr($plugin_name),
					$r->new_version,
					wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . $file, 'upgrade-plugin_' . $file) ),true,true);
		}
		*/
	}

		/**
		 * in some cases scenario our update helper can't be run simply because a version is missing
		 */
		function repair_settings(){
			static $is_repairing = FALSE;

			if($is_repairing === FALSE){
				$is_repairing = TRUE;

				// set installed as true
				$values['installed'] = true;
				// set installed_time: minus 7200 on it so that we don't display the welcome page again on the
				// view condition in the check() function above WYSIJA::redirect('admin.php?page=wysija_campaigns&action=welcome_new');
				$values['installed_time'] = time() - 7200;

				// find our current db version
				$values['wysija_db_version'] = $this->_find_db_version();

				// save the missing settings to repair the installation
				$model_config = WYSIJA::get('config','model');
				$model_config->save($values);

			}

		}

		/**
		 * find out what is the db version based on the existing columns of some tables
		 */
		private function _find_db_version(){
			$model_wysija = new WYSIJA_model();

			// test against 2.0 and set it to 1.1 if true
			$test = $model_wysija->query('get_res', "SHOW COLUMNS FROM `[wysija]email` like 'modified_at';" );
			if(empty($test)){
				return '1.1';
			}
			// test against 2.4 and set it to 2.3.4 if true
			$test = $model_wysija->query('get_res', "SHOW COLUMNS FROM `[wysija]form`;" );
			if(empty($test)){
				return '2.3.4';
			}

			// test against 2.5.9.6 and set it to 2.5.5 if true
			$test = $model_wysija->query('get_res', "SHOW COLUMNS FROM `[wysija]user` like 'domain';" );
			if(empty($test)){
				return '2.5.5';
			}
			// test against 2.5.9.7 and set it to 2.5.9.6 if true
			$test = $model_wysija->query('get_res', "SHOW COLUMNS FROM `[wysija]user` like 'last_opened';" );
			if(empty($test)){
				return '2.5.9.6';
			}

			return WYSIJA::get_version();
		}
}
