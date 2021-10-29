<?php
/** 
 * Frontend Class for Subscriber
 *
 * @author 		AJDE
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosb_front{
	public $print_scripts_on;
	public $evoOpt;
	public $lang;
	
	function __construct(){
		
		EVO()->cal->load_more('evcal_sb');

		$this->evoOpt = EVO()->cal->get_op('evcal_1', 'evcal_options_');
		$this->evoOpt2 = EVO()->cal->get_op('evcal_1', 'evcal_options_');
		$this->evoOpt_sb = EVO()->cal->get_op('evcal_sb', 'evcal_options_');

		// functions
		include_once('class-functions.php');
		$this->functions = new evosb_functions($this->evoOpt_sb);

		add_action( 'init', array( $this, 'register_frontend_scripts' ) ,15);
		add_action( 'eventon_enqueue_scripts', array( $this, 'enque_script' ) ,15);

		// include subscriber in calendar
		add_action('evo_cal_after_footer', array($this, 'sub_to_footer'), 10, 1);

		add_action( 'wp_footer', array( $this, 'print_scripts' ) ,15);

		add_filter( 'template_include', array( $this, 'template_loader' ) , 99);

		// shortcode to throw subscription button anywhere
		add_shortcode('evo_subscribe_btn', array($this,'subscription_button'));

		add_action('evo_addon_styles', array($this, 'styles') );
	}

	

	// template loading
		public function template_loader( $template='', $file='') {
			global $eventon_sb, $post, $eventon;
			
			// Paths to check
			$paths = apply_filters('eventon_template_paths', array(
				0=>TEMPLATEPATH.'/',
				1=>TEMPLATEPATH.'/'.$eventon->template_url.'subscriber/', 
				2=>STYLESHEETPATH.'/'.$eventon->template_url.'subscriber/', 
				// eg. .../wp-content/themes/yourtheme/eventon/subscriber/
				3=>$eventon_sb->addon_data['plugin_path']  . '/templates/',
			));
			
			$eventon_subscription_page_id = get_option('eventon_subscription_page_id');

			// if no file was suggested
			if(empty($file)){
				// subscription manager page
				if( !empty($post) && !empty($eventon_subscription_page_id) &&  $post->ID == $eventon_subscription_page_id ) {
					$file 	= 'subscription.php';

					// load styles for this page
					wp_enqueue_style( 'evosb_page_styles', EVOSB()->assets_path.'evosb_subscriber_page.css','', EVOSB()->version);
					wp_enqueue_script( 'evosb_page_script', EVOSB()->assets_path.'evosb_subscriber_page.js',array('jquery'), EVOSB()->version);
				}
			}

			// FILE Exist
			if ( $file ) {				
				// each path
				foreach($paths as $path){	
					//echo $path.$file.'<br/>';			
					if(file_exists($path.$file) ){	
						$template = $path.$file;
						break;
					}
				}
					
				if ( ! $template ) { 
					$template = $eventon_sb->addon_data['plugin_path'] . '/templates/' . $file;				
				}
			}
			
			return $template;
		}
	
		// parse default email arguments with passed values
			function parse_email_atts($data){
				return array_merge(array(
					'to'=>'','key'=>'','subscriber_id'=>'','lang'=>'L1'
				),$data);
			}
		// send subscription verification email
			function email_them($data){
				//verification email

				$data = $this->parse_email_atts($data);

				$this->send_verification_email($data);
				$this->send_notification_email($data);
				$this->send_confirmation_email($data,false);
			}
			function send_verification_email($data){
				if(!empty($this->evoOpt_sb['evosb_1_001']) && $this->evoOpt_sb['evosb_1_001']=='yes'){
					$data['type']= 'verification';
					return $this->send_email($data);
				}else{
					return false;
				}
			}
			function send_notification_email($data){
				if(!empty($this->evoOpt_sb['evosb_1_005']) && $this->evoOpt_sb['evosb_1_005']=='yes'){

					$to = (!empty($this->evoOpt_sb['evosb_3_notif_email']))?
						$this->evoOpt_sb['evosb_3_notif_email']: get_bloginfo('admin_email');
					if(!empty($to)){
						$data['to']= $to;
						$data['type']= 'notification';
						$data['output']= 'send';
						return $this->send_email($data);
					}
				}else{
					return false;
				}
			}
			function send_confirmation_email($data, $force=false){
				// if send out confirmation is set
				
				$confirmation_emails_active = (!empty($this->evoOpt_sb['evosb_1_002']) 
					&& $this->evoOpt_sb['evosb_1_002']=='yes')? true: false;
				$must_verify_first = (!empty($this->evoOpt_sb['evosb_1_001']) && $this->evoOpt_sb['evosb_1_001']=='yes')? 'yes':'no';

				if(!$confirmation_emails_active)
					return false;

				if(	$must_verify_first=='no' || $force){
					$data['type']= 'confirmation';
					return $this->send_email($data);
				}
			}
	// actual sending of the email
		function send_email($args=''){

			$default = array(
				'to'=>'',
				'type'=>'confirmation',
				'key'=>'',
				'output'=>'send',
				'subscriber_id'=>'',
				'lang'=>'L1'
			);
			$args = array_merge($default, $args);
				
			global $eventon_sb;

			$_link = ''; $evolayout = false;

			switch ($args['type']){
				case 'confirmation':
					$evolayout = true;
					$_link = $this->subscriber_url(array(
						'email'=>urlencode($args['to']), 
						'action'=>'manage',
						'subscriber'=>$args['subscriber_id'],
						'lang'=>$args['lang'],
						));
					$subject = ((!empty($this->evoOpt_sb['evosb_3_003']))? 
							$this->evoOpt_sb['evosb_3_003']: __('Thank you for subscribing to our site!','eventon'));
					$file = 'email-subscription_confirmation';
				break;
				case 'verification':
					$evolayout = true;
					$_link = $this->subscriber_url(array(
						'key'=>$args['key'], 
						'email'=>urlencode($args['to']), 
						'action'=>'verify',
						'subscriber'=>$args['subscriber_id'],
						'lang'=>$args['lang'],
					));
					$subject = ((!empty($this->evoOpt_sb['evosb_3_004']))? 
							$this->evoOpt_sb['evosb_3_004']: __('Verify your subscription','eventon'));
					$file = 'email-verify_subscription';
				break;
				case 'notification':
					$evolayout = true;
					$subject = ((!empty($this->evoOpt_sb['evosb_3_006']))? 
						$this->evoOpt_sb['evosb_3_006']: __('You have a new subscriber!','eventon'));
					$file = 'email-notification';
				break;
				case 'unsubscribed':
					$evolayout = true;
					$subject = ((!empty($this->evoOpt_sb['evosb_3_unsubcribe']))? 
						$this->evoOpt_sb['evosb_3_unsubcribe']: __('Unsubscribe Confirmation','eventon'));
					$file = 'email-unsubscribed';
				break;
				case 'newevent':
					// for preview purposes
					if($args['output']=='echo'){
						$args['args'] = array(
							'event-name'=>'Super fun Event',	'e_id'=>'100'
						);
					}

					$file = 'email_new_event';
					$subject = ((!empty($this->evoOpt_sb['evosb_3_005']))? 
							$this->evoOpt_sb['evosb_3_005']: __('New Event: {event-name}','eventon'));
					// replace with actual event name
					if(strpos($subject, '{event-name}')!==false){
						$subject = str_replace('{event-name}', html_entity_decode($args['args']['event-name']), $subject);
					}
				break;
				case 'cancelevent':
					// for preview purposes
					if($args['output']=='echo'){
						$args['args'] = array(	'event-name'=>'Super fun Event',	'e_id'=>'100');
					}

					$file = 'email_cancel_event';
					$subject = ((!empty($this->evoOpt_sb['evosb_3_cancel']))? 
							$this->evoOpt_sb['evosb_3_cancel']: __('Cancel Event: {event-name}','eventon'));
					// replace with actual event name
					if(strpos($subject, '{event-name}')!==false){
						$subject = str_replace('{event-name}', html_entity_decode($args['args']['event-name']), $subject);
					}
				break;
			}

			//connect to eventon helper to send email					
				if(empty($args['to']))
					return;			
				if(!empty($_link))
					$args['link'] = $_link;

				$args_ = array(
					'to'=>$args['to'],
					'subject'=>$subject,
					'message'=>stripslashes($this->emailpart_message($file, $args, $evolayout)),
					'from'=>$this->emailpart_from_info(),
					'type'=> ( in_array($args['type'], array('cancelevent','newevent'))? 'bcc':'normal')
				);

			// just for previewing emails
				if($args['output'] == 'echo'){
					$args_['preview']='yes';
				}

				return $eventon_sb->helper->send_email($args_);
		}

		// email parts
			function backup_email($args){
				$headers[] = 'From: '.$args['from'];

				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type'));
			
				$emailed =  wp_mail($args['to'], $args['subject'],$args['message'], $headers);

				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
				return $emailed;
			}
			function emailpart_from_info(){
				$__from_email = (!empty($this->evoOpt_sb['evosb_3_002']) )?
						htmlspecialchars_decode ($this->evoOpt_sb['evosb_3_002'])
						:get_bloginfo('admin_email');
				$__from_email_name = (!empty($this->evoOpt_sb['evosb_3_001']) )?
						($this->evoOpt_sb['evosb_3_001'])
						:get_bloginfo('name');
				return (!empty($__from_email_name))? 
							$__from_email_name.' <'.$__from_email.'>' : $__from_email;
			}

		// email body content for the email
			function emailpart_message($filename, $args='', $evolayout= false){
				global $eventon;
				$args = $args;
				$_link = !empty($args['link'])? $args['link']:'';

				ob_start();

					// eventon email header
					if($evolayout){
						echo $eventon->get_email_part('header');
						echo '<div style="padding:20px; font-family:\'open sans\'">';
					}  

					$file_ = $this->template_loader('', $filename.'.php');
					include_once($file_);

					// eventon email footer
					if($evolayout)echo '</div>'.$eventon->get_email_part('footer');

				return ob_get_clean();	
			}
			function set_html_content_type() {	return 'text/html';	}

	// Subscriber page
		// global language for subscriber page
			public function set_language(){
				
				$lang = !empty($_REQUEST['lang'])? $_REQUEST['lang']: 'L1';
				$this->lang = EVO()->lang = $lang; // set the language for this class as well

				return $lang;
			}
		// VERIFY subscription
			function verify_subscription($email, $key){
				$args = array(
					'post_type'=>'evo-subscriber',
					'meta_query'=>array(
						array(
							'key'=>'verification_key',
							'value'=>$key,
						)
					)
				);
				$subscriber = new WP_Query($args);
				// if there is a matching subscriber post with the same key
				if($subscriber->have_posts()){
					while($subscriber->have_posts()): $subscriber->the_post();
						$post_title = ($subscriber->post->post_title == urldecode($email))? 'true':'false';

						// check if post title match to email
						if($post_title){
							// get verification status
							$verification_status = get_post_meta($subscriber->post->ID, 'verified',true);

							// already verified
							if(!empty($verification_status) && $verification_status=='yes'){
								return evo_lang('Subscription Already Verified!');
							}else{
								update_post_meta($subscriber->post->ID, 'verified', 'yes');
								// send confirmation email
								$this->send_confirmation_email(
									array(
										'to'=>urldecode($email),
										'subscriber_id'=>$subscriber->post->ID,
										'lang'=>$this->lang
									), true
								);
								return evo_lang('Successfully verified your subscription!');
							}
						}else{
							return evo_lang('Email address does not have a match in our database!');
						}
						
					endwhile;
					wp_reset_postdata();
				}else{
					return evo_lang('Verification code did not match to database for the subscription!');
				}
			}
		// UNsubscribe a email 
			function unsubscribe($args){
				// check if email exists
				$email = urldecode($args['email']);
								
				if($subscription_id = $this->email_exist($email)){

					// send unsubscription confirmation email
					$att = array(
						'to'=>$email,
						'message'=>$this->emailpart_message('email-unsubscribed'),
						'subject'=> ((!empty($this->evoOpt_sb['evosb_3_unsubcribe']))? 
						$this->evoOpt_sb['evosb_3_unsubcribe']: __('Unsubscribe Confirmation','eventon') ),
						'from'=>$this->emailpart_from_info()
					);
					$emailed = $this->backup_email($att);

					//print_r($att);
					//echo $emailed?'emailed':'notemailed';					

					// Delete subscriber from the system
					if(!empty($this->evoOpt_sb['evosb_del_sub']) && $this->evoOpt_sb['evosb_del_sub']=='del'){
						$trashed = wp_trash_post($subscription_id);

						// unsubscribe from mailchimp & delete
							$this->functions->unsubscribe_mailchimp_email($email, $subscription_id, true);

						return evo_lang('Successfully unsubscribed from the system!');
					
					// unsubscribe only from the system
					}else{
						update_post_meta($subscription_id, 'status', 'no');

						// unsubscribe from mailchimp
							$this->functions->unsubscribe_mailchimp_email($email, $subscription_id);

						return evo_lang('Successfully unsubscribed from the system!');
					}					
				}else{
					return evo_lang('Could not find the email address in our system!');
				}
			}
		// check of email is subscribed currently
			function is_currently_subscribed($subscriber_id){
				$status =  get_post_meta($subscriber_id, 'status', true);
				return (!empty($status) && $status=='yes')? true:false;
			}
			function do_subscribe_back($args){
				// check if email exists
				$email = urldecode($args['email']);
				if($subscription_id = $this->email_exist($email)){
					update_post_meta($subscription_id, 'status', 'yes');

					// subscribe back in mailchimp
					$this->functions->subscribe_back_mailchimp_email($email, $subscription_id);

					return evo_lang('Successfully subscribed back to system!');
				}else{
					return evo_lang('Could not find the email address in our system!');
				}
			}
		// get subscriber page url
			function subscriber_url($args){				
				$eventon_subscription_page_id = get_option('eventon_subscription_page_id');

				// if the subscription page doesnt exist create
				if(empty($eventon_subscription_page_id)){
					$eventon_subscription_page_id = $this->functions->subscription_page();
				}

				$link =  get_permalink($eventon_subscription_page_id);

				$_append = (strpos($link, '?')!== false)?'&':'?';

				$_args = '';
				foreach($args as $ff=>$vv){
					$_args .= $ff.'='.$vv.'&';
				}
				return $link . $_append . $_args;
				
			}
		// manage subsctiption categories
			function manage_subscribe_categories(){

				ob_start();

				$subscriber_id = !empty($_REQUEST['subscriber'])? $_REQUEST['subscriber']:'';
				$subscriber_email = !empty($_REQUEST['email'])? $_REQUEST['email']:'';

				if(empty($subscriber_email)){
					echo "<p>".evo_lang('Access not granted!')."</p>";
					return;
				}

				// if subscription id is not present
				$s_id = $this->email_exist($subscriber_email);
				if($s_id){
					$subscriber_id = $s_id;
				}
				if(empty($subscriber_id)){
					echo "<p>".evo_lang('Could not find your email in our system!')."</p>";
					return;
				}

				$_saved_fields = !empty($this->evoOpt_sb['evosb_002'])? $this->evoOpt_sb['evosb_002']: false;
				if($_saved_fields){	

					if(empty($subscriber_id)) return;

					$show_success_msg = false;
					
					// update subscription options from form
					if(!empty($_POST['evosb_action']) && $_POST['evosb_action']=='true'){

						foreach($_saved_fields as $field){
							if(!empty($_POST[$field])){
								update_post_meta($subscriber_id, $field, $_POST[$field]);
							}
						}

						$show_success_msg = true;

					}

					$subscriber_pmv = get_post_custom($subscriber_id);

					$myurl = strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);

					echo "<div class='evosb_categories_section'>";
					echo "<h4>".evo_lang('You are subscribed to below event categories')."</h4>
						<form id='evosb_subscriber_page' action='' method='post'>
						<input type='hidden' name='evosb_action' value='true'/>
						<input type='hidden' name='email' value='".(isset($_POST['email'])? $_POST['email']:'') ."'/>
						<input type='hidden' name='subscriber' value='".$subscriber_id."'/>
					";

					$event_type_names = evo_get_ettNames($this->evoOpt);
					
					// event type tax
						for($x=1; $x<evo_get_ett_count($this->evoOpt)+1; $x++){
							$ab = ($x==1)? '':'_'.$x;							
							if(in_array('event_type'.$ab, $_saved_fields)){
								$tax_name = $this->lang_get('evcal_lang_et'.$x,$event_type_names[$x]);
								echo $this->_html_tax_section_subcriber_page($subscriber_pmv, 'event_type'.$ab, $tax_name);
							}
						}

					// location 
						if(in_array('location', $_saved_fields)){
							$tax_name = $this->lang_get('evcal_lang_evloc','Event Location');
							echo $this->_html_tax_section_subcriber_page($subscriber_pmv, 'event_location',$tax_name);
						}
					// Organizer 
						if(in_array('organizer', $_saved_fields)){
							$tax_name = $this->lang_get('evcal_lang_evorg','Event Organizer');
							echo $this->_html_tax_section_subcriber_page($subscriber_pmv, 'event_organizer',$tax_name);
						}

					echo "</div>";

					// successfully updated message
						if($show_success_msg) echo "<p style='padding: 20px 0;background-color: #b4eaa2;'>".evo_lang('Subscription Information Updated!')."</p>";

					echo "<button type='submit' class='evosb_btn'>".evo_lang('Update Subscription')."</button></form>";

					echo $this->_html_section_unsubscribe_btn();
					
				}
				// there are no fields set for the form
				else{
					echo "<p>".evo_lang('You are subscribed in our system!')."</p>";
					echo $this->_html_section_unsubscribe_btn();
				}

				echo ob_get_clean();
			}
				function _html_section_unsubscribe_btn(){
					$_link =$this->subscriber_url(array('action'=>'unsubscribe', 'email'=>urlencode($_REQUEST['email'])) );

					return "<p><a class='evosb_btn evosb_btn_two' href='".$_link."'>".evo_lang('Unsubscribe')."</a></p>";
				}
				function _html_tax_section_subcriber_page($pmv, $tax, $taxname){

					$thisterms = '';
					$_term_checked = 'none';
					if($pmv && !empty($pmv[$tax])){
						$_terms = $pmv[$tax][0];
						
						if($_terms =='all'){
							$_term_checked = 'all';
						}elseif($_terms !='-'){
							$_term_checked = $_terms;
							$thisterms = explode(',', $_terms);
						}
					}
					
					$terms = $this->get_tax_terms($tax);

					if($terms):
					ob_start();

						$lang = !empty($_REQUEST['lang'])? $_REQUEST['lang']: 'L1';

						$_text_all = $this->lang_get('evcal_lang_all','All', $lang);


						//pretty terms view
						$pretty_terms = $_term_checked;
						if($_term_checked!='all' && $_term_checked!='none' && !empty($thisterms)){
							$pretty_terms='';
							foreach($terms as $term){
								// find actual term name for saved values
								if(in_array($term->term_id,$thisterms)){
									$term_name = $this->lang_get('evolang_'.$tax.'_'.$term->term_id, $term->name);
									$pretty_terms.= $term_name.', ';
								}
							}
						}

						if( $_term_checked == 'all') $pretty_terms = $_text_all;

					?>
						<div class="evoETT_section">
							<p class='categories' data-name='<?php echo $tax;?>'><?php echo $taxname;?> <span value='<?php echo $_term_checked;?>' data-name='<?php echo $tax;?>' class='field'><?php echo $pretty_terms;?></span>
								<input type="hidden" name='<?php echo $tax;?>' value='<?php echo $_term_checked;?>'/>
							</p>
							<p class="cat_selection" style='display:none'>
								<span class='cat_sel_in'>
									<span><input name='all' type='checkbox' data-id='all' data-name='<?php echo $_text_all;?>' <?php echo ($_term_checked=='all')?"checked='checked'":'';?>/><?php echo $_text_all;?></span>
								<?php
									foreach($terms as $term):
										$checked = (
											(!empty($thisterms) && 
											in_array($term->term_id, $thisterms)) || 
											$_term_checked=='all'
										)? 'checked="checked"':'';
										$term_name = $this->lang_get( 'evolang_'.$tax.'_'.$term->term_id,$term->name, $lang);

								?>
									<span><input data-id='<?php echo $term->term_id;?>' data-name='<?php echo $term_name;?>' type="checkbox" <?php echo $checked;?>/><?php echo $term_name;?></span>
								<?php endforeach;?>	
								</span>
							</p>
						</div>
					<?php

					return ob_get_clean();

					endif;
				}
		
	// get language text translated
		function lang_get($var, $default, $lang=''){
			$lang = !empty($this->lang)? $this->lang: 
				(!empty($lang)? $lang: 'L1');

			return evo_lang_get($var, $default, $lang );
		}
	// Subscriber on front-end calendar footer
		function sub_to_footer($args){			
			// shortcode variable rss passed as yes
			if(!empty($args['subscriber']) && $args['subscriber']=='yes'){	

				// if only got loggedin users
				$only_logged = (!empty($this->evoOpt_sb['evosb_only_logged']) && $this->evoOpt_sb['evosb_only_logged']=='yes')? true: false;	

				if(!$only_logged || ($only_logged && is_user_logged_in())):

					$this->lang = (!empty($args['lang']))? $args['lang']:'L1';

					$this->print_scripts_on=true;				
					echo '<a class="evosub_subscriber_btn evcal_btn"><em class="fa fa-envelope-o"></em> '. $this->lang_get('evoSUB_001','Subscribe to this calendar').'</a>';
				endif;
			}
		}

	// subscription button
		function subscription_button($atts){
			// if only got loggedin users
			$only_logged = (!empty($this->evoOpt_sb['evosb_only_logged']) && $this->evoOpt_sb['evosb_only_logged']=='yes')? true: false;	

			if(!$only_logged || ($only_logged && is_user_logged_in())):

				$this->lang = (!empty($atts['lang']))? $atts['lang']:'L1';
				$buttonText = !empty($atts['btn_txt'])? $atts['btn_txt']: 
					$this->lang_get('evoSUB_001','Subscribe to this calendar');

				$this->print_scripts_on=true;	
				$this->enque_script();			
				return '<a class="evosub_subscriber_btn evcal_btn" data-role="none"><em class="fa fa-envelope-o"></em> '. $buttonText.'</a>';
			endif;
		}

	// subscription form HTML into the page
		function sub_form_html(){
			
			$only_logged = (!empty($this->evoOpt_sb['evosb_only_logged']) && $this->evoOpt_sb['evosb_only_logged']=='yes')? true: false;

			if(!$only_logged || ($only_logged && is_user_logged_in())):


			?>
			<div id='evoSUB_form' style='opacity:0;display:none;top:50px'>
				<div class="form">
				<a id="evoSUB_close">X</a>
					<div class='formIn '>						
						<h3><?php echo $this->lang_get('evoSUB_001','Subscribe to this calendar');?></h3>
						<p><?php echo $this->lang_get('evoSUB_002','Please subscribe to receive email updates about our awesome events!');?></p>							
						<?php 
							// show all taxonomy types that are active
							$this->_html_additional_form_fields();
						?>
						<p><button id='evosub_submit_button'><?php echo $this->lang_get('evoSUB_005','Subscribe');?></button></p>
					</div>
					<div class="formMsg" style='display:none' ><p><b></b><?php echo $this->lang_get('evoSUB_006','Thank you for subscribing to our calendar!');?></p>
					<?php
						// verification message if needed via settings
						if(!empty($this->evoOpt_sb['evosb_003']) && $this->evoOpt_sb['evosb_003']=='yes'){
							echo "<p>".$this->lang_get('evoSUB_007','We have sent you a verification email, please verify your email address.')."</p>";
						}
					?>
					</div>
					<?php if(!empty($this->evoOpt_sb['evosb_1_003']) || !empty($this->evoOpt_sb['evosb_1_004'])):
						$_a_target= 'target="_blank"';
					?>
					<div class='form_footer'>
						<p><?php echo $this->lang_get('evoSUB_002e','By subscribing, you agree with');?> <?php if(!empty($this->evoOpt_sb['evosb_1_004'])):?><a <?php echo $_a_target;?> href='<?php echo $this->evoOpt_sb['evosb_1_004'];?>'><?php echo $this->lang_get('evoSUB_002f','Terms of Use');?></a><?php endif; if(!empty($this->evoOpt_sb['evosb_1_003'])):?> <a <?php echo $_a_target;?> href="<?php echo $this->evoOpt_sb['evosb_1_003'];?>"><?php echo $this->lang_get('evoSUB_002g','Privacy Policy');?></a><?php endif;?></p>
					</div>
					<?php endif;?>
					<div id='form_text' style='display:none' data-all='<?php echo $this->lang_get('evcal_lang_all','All');?>'></div>
				</div>
			</div>
			<div id="evoSUB_bg" style='display:none'></div>
			<?php
			endif; // logedin check
		}

	// ECHO form fields if set via settings
		function _html_additional_form_fields(){
			$_saved_fields = !empty($this->evoOpt_sb['evosb_002'])? $this->evoOpt_sb['evosb_002']: false;

			ob_start();

			echo "<div class='evosub_form' >";

			// input as lang field
				if($this->lang!='L1')
					echo "<input class='evo_lang' type='hidden' name='evo_lang' value='{$this->lang}'/>";

			if($_saved_fields){				
				$event_type_names = evo_get_localized_ettNames($this->lang, $this->evoOpt,$this->evoOpt2);
				$lang = !empty($this->lang)? $this->lang: 'L1';
							
				// name
					if(in_array('name', $_saved_fields))
						echo "<p><input class='field' data-name='name' type='text' placeholder='".$this->lang_get('evoSUB_002a','Your Name')."'/></p>";

				// email -- required field
					echo "<p><input type='text' data-name='email' class='field req' placeholder='".$this->lang_get('evoSUB_002b','Your Email Address')."'/></p>";

				// event type tax
					for($x=1; $x<evo_get_ett_count($this->evoOpt)+1; $x++){
						$ab = ($x==1)? '':'_'.$x;						
						if(in_array('event_type'.$ab, $_saved_fields)){
							echo $this->_html_taxonomy_section('event_type'.$ab,$event_type_names[$x]);
						}
					}

				// location 
					if(in_array('location', $_saved_fields)){
						echo $this->_html_taxonomy_section('event_location',$this->lang_get('evcal_lang_evloc','Event Location'));
					}
				// Organizer 
					if(in_array('organizer', $_saved_fields)){
						echo $this->_html_taxonomy_section('event_organizer',$this->lang_get('evcal_lang_evorg','Event Organizer'));
					}

			}else{
				echo "<p><input type='text' data-name='email' class='field' placeholder='".$this->lang_get('evoSUB_002b','Your Email Address')."'/></p>";
			}

			echo "<p style='display:none' class='evosub_msg' data-str1='".$this->lang_get('evoSUB_003','Required field missing!')."' data-str2='".$this->lang_get('evoSUB_004','Invalid Email address')."' data-email_exist='".$this->lang_get('evoSUB_004a','Email Address Exists Already!')."' data-no_cpt='".$this->lang_get('evoSUB_004b','Could not create subscriber, try later!')."'>Error</p>";
			echo "</div>";

			echo ob_get_clean();

		}
	
	// get event taxonomy terms
		function _html_taxonomy_section($tax, $tax_name){

			$terms = $this->get_tax_terms($tax);

			
			if($terms):
			ob_start();
				$_text_all = $this->lang_get('evcal_lang_all','All');

			?>
				<div class="evoETT_section">
					<p class='categories' data-name='<?php echo $tax;?>'><?php echo $tax_name;?><span value='all' data-name='<?php echo $tax;?>' class=''><?php echo $_text_all;?></span>
						<input class='evosub_cat_vals field' type="hidden" name='<?php echo $tax;?>' data-name='<?php echo $tax;?>' value='all'/>
					</p>
					<p class="cat_selection" style='display:none'>
						<span class='cat_sel_in'>
							<span><input name='<?php echo $_text_all;?>' type='checkbox' data-id='all' data-name='<?php echo $_text_all;?>' checked='checked'/><?php echo $_text_all;?></span>
						<?php
							// each tax term
							foreach($terms as $term):	
								$term_name = $this->lang_get('evolang_'.$tax.'_'.$term->term_id, $term->name);
						?>
							<span><input data-id='<?php echo $term->term_id;?>' data-name='<?php echo $term_name;?>' type="checkbox" checked="checked"/><?php echo $term_name;?></span>
						<?php endforeach;?>	
						</span>
					</p>
				</div>
			<?php

			return ob_get_clean();

			endif;
		}

		function get_tax_terms($tax){


			EVO()->cal->set_cur('evcal_sb');

			$hide_empty = true;
			
			// enable show all terms if selected via settings
			if(
				( $tax == 'event_location' && EVO()->cal->check_yn('evosb_show_loc') ) ||
				( $tax == 'event_organizer' && EVO()->cal->check_yn('evosb_show_org') ) ||
				( strpos($tax, 'event_type') !== false && EVO()->cal->check_yn('evosb_show_cat') ) 
			){
				$hide_empty = false;
			}


			$terms = get_terms( array(
				'taxonomy'=> $tax,
				'orderby'=>'name',
				'hide_empty'=> $hide_empty,
			));

			return !empty($terms)? $terms: false;
		}
	
	// GET subsctiption form fields as an array
		function get_form_fields(){
							
			$event_type_names = evo_get_ettNames($this->evoOpt);

			$fields = array();

			$fields['email']=__('Email Address','eventon');

			$fields['name']=__('Subscriber Name','eventon');
			$fields['status']=__('Subscription Status','eventon');
			$fields['verified']=__('Email Verified Status','eventon');
			$fields['subtitle']=__('Event Categories subscribed to','eventon');

			// taxonomies
			for($x=1; $x<evo_get_ett_count($this->evoOpt)+1; $x++){
				$ab = $x==1?'':'_'.$x;
				$fields['event_type'.$ab] = $event_type_names[$x];
			}

			// location
			$fields['event_location']=__('Event Location','eventon');
			$fields['event_organizer']=__('Event Organizer','eventon');

			return $fields;
		}
	
	// check if the form include any event category fields for selection
		public function has_category_fields_selected(){
			$output = false;
			$_saved_fields = !empty($this->evoOpt_sb['evosb_002'])? $this->evoOpt_sb['evosb_002']: false;

			if(!$_saved_fields) return false;

			if(in_array('location', $_saved_fields)) return true;
			if(in_array('organizer', $_saved_fields)) return true;

			for($x=1; $x<evo_get_ett_count($this->evoOpt)+1; $x++){
				if(in_array('event_type_'.$x, $_saved_fields)){
					return true;
					exit;
				}
			}
			return false;
		}

	// check if email address exists
		function email_exist($email){
			global $wpdb;
			$val = $wpdb->get_row("SELECT ID FROM " . $wpdb->prefix.'posts' . " WHERE post_type='evo-subscriber' AND post_status='publish' AND post_title = '" . $email . "'", 'ARRAY_N');			
			return (null !== $val)? $val[0]: false;
		}

	// front end styles and scripts
		function register_frontend_scripts(){
			
			if( evo_settings_val('evcal_concat_styles',$this->evoOpt, true))
				wp_register_style( 'evo_sb_styles',EVOSB()->assets_path.'evosub_styles.css');
			
			wp_register_script( 'evo_sb_script',EVOSB()->assets_path.'evosub_script.js');
			wp_localize_script( 
				'evo_sb_script', 
				'evosub_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonsub_nonce' )
				)
			);
		}
		function print_scripts(){

			if(!$this->print_scripts_on) return;

			$this->sub_form_html();
			$this->print_front_end_scripts();
		}
		function print_front_end_scripts(){
			wp_enqueue_style('evo_sb_styles');			
		}
		function enque_script(){
			wp_enqueue_script('evo_sb_script');
		}
		function styles(){
			ob_start();
			include_once(EVOSB()->plugin_path.'/assets/evosub_styles.css');
			echo ob_get_clean();
		}

}