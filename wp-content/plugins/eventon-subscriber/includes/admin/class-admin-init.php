<?php
/**
 * Admin class for subscriber plugin
 *
 * @author  	AJDE
 * @version 	1.2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosb_admin{
	function __construct(){
		
		include_once('evo-subscriber_meta_boxes.php');
		include_once('evo-subscriber.php');


		// icon in eventon settings
		add_filter( 'eventon_custom_icons',array($this,'custom_icons') , 10, 1);

		// appearance
		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);

		// language
		add_filter('eventon_settings_lang_tab_content', array($this,'language_additions'), 10, 1);

		// settings
		add_filter('eventon_settings_tabs',array($this,'tab_array') ,10, 1);
		add_action('eventon_settings_tabs_evcal_sb',array($this,'tab_content') );	

		add_action( 'admin_menu', array( $this, 'menu' ),9);
		
		add_action( 'init', array( $this, 'init' ));		

		// print settings styles
		if((!empty($_GET['page']) && $_GET['page']=='eventon') || (!empty($_GET['post_type']) && $_GET['post_type']=='evo-subscriber') )
			add_action( 'admin_init', array($this, 'admin_styles'));

		// event email notifications
		add_action('eventon_save_meta',array($this,'event_emailing'), 10, 2);
		
		// event meta box
		add_action( 'add_meta_boxes', array($this,'meta_box') );
		//add_action('eventon_add_meta_boxes',array($this, 'meta_box'));

		// shortcode inclusions
		add_filter('eventon_shortcode_popup',array($this, 'add_shortcode_options'), 10, 1);

		// delete subscriber post
		add_action('wp_trash_post',array($this,'trash_subscriber_post'),1,1);

		// Exclude event fields for duplication
		add_filter('eventon_duplicate_event_exclude_meta', array($this, 'exclude_from_duplication'), 10, 1);
	}

	// INIT
		public function init(){	
			EVOSB()->frontend->functions->subscription_page();
		}

	// Exclude from duplication
		function exclude_from_duplication($array){
			$array[] = '_evosb_send_mail';
			$array[] = '_evosb_email_sent';
			return $array;
		}

	// event meta box
		function meta_box(){
			add_meta_box('ajdeevcal_evosb',__('Event Subscription','eventon_sb'), array($this,'meta_box_content'),'ajde_events', 'side', 'high');
		}
		function meta_box_content(){
			global $post;

			$event_pmv = (!empty($post))? get_post_custom($post->ID):null;
			


			$is_repeating_event = (!empty($event_pmv['evcal_repeat']) && $event_pmv['evcal_repeat'][0]=='yes')? true:false;
			$_evosb_send_mail = (!empty($event_pmv['_evosb_send_mail']))?
				$event_pmv['_evosb_send_mail'][0]:null;
			$_evosb_email_sent = (!empty($event_pmv['_evosb_email_sent']))?
				$event_pmv['_evosb_email_sent'][0]:null;

			ob_start();

			if($is_repeating_event){
				echo "<p style='  text-align: center;opacity: 0.5;padding: 5px;background-color: #F0F0F0;border-radius:6px'>".__('Repeating events are not supported with subscriber emails!','eventon_sb')."</p>";
				return;
			}

			// if already emailed to subscribers for this event
			if($_evosb_email_sent=='yes'):
				echo "<p style='  text-align: center;opacity: 0.5;padding: 5px;background-color: #F0F0F0;border-radius:6px'>".__('Already Emailed Subscribers!','eventon_sb')."</p>";
			else:

				$evosb_autosend = (!empty(EVOSB()->frontend->evoOpt_sb['evosb_autosend']) && EVOSB()->frontend->evoOpt_sb['evosb_autosend']=='yes')? 'yes':'no';
			?>
			<p class='yesno_leg_line' style='padding-top:0px'>
				<?php 	echo eventon_html_yesnobtn(
					array(
						'id'=>'_evosb_send_mail', 
						'var'=>$_evosb_send_mail,
						'input'=>true,
						'default'=>$evosb_autosend,
						'label'=>__('Email to Subscribers','eventon_sb'),
						'guide'=>__('Setting this to yes will send new event email to subscribers.','eventon_sb'),
						'guide_position'=>'L'
					));
				?>	
			</p>
			<?php


			// cancellation notice
				$evosb_cancel_notif = (!empty(EVOSB()->frontend->evoOpt_sb['evosb_cancel_notif']) && EVOSB()->frontend->evoOpt_sb['evosb_cancel_notif']=='yes')? true: false;

				if($evosb_cancel_notif){
					echo "<p style='  text-align: center;opacity: 0.5;padding: 5px;background-color: #F0F0F0;border-radius:6px'>".__('If event cancelled, subscribers are set to be notified via email.','eventon_sb')."</p>";
				}

			// subscriber count
				$__emails_list = $this->_get_subscribers_for_event($post->ID);	

				//print_r($__emails_list);
				if($__emails_list){
					echo "<p style='  text-align: center;opacity: 0.5;padding: 5px;background-color: #F0F0F0;border-radius:6px'>". count($__emails_list) .' '.__('Subscriber(s) for this event','eventon_sb')."</p>";
				}else{
					echo "<p style='  text-align: center;opacity: 0.5;padding: 5px;background-color: #F0F0F0;border-radius:6px'>".__('No subscribers for this event!','eventon_sb')."</p>";
				}

			//echo "<a id='evosb_manual_email_btn' class='button' href='".get_admin_url()."post.php?post={$post->ID}&action=edit&evosb_emailing=true'>".__('Manually Email to Subscribers','eventon_sb')."</a>";

			endif;

			echo ob_get_clean();
		}
	
	// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Subscriber', __('Subscriber','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_sb', '' );
		}

	// TABS SETTINGS
		function tab_array($evcal_tabs){
			$evcal_tabs['evcal_sb']='Subscriber';		
			return $evcal_tabs;
		}

		function tab_content(){
			global $eventon;

			$eventon->load_ajde_backender();
			?>
			<form method="post" action=""><?php settings_fields('evosb_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_sb" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php
				$site_name = get_bloginfo('name');
				$site_email = get_bloginfo('admin_email');

				$customization_pg_array = array(					
					array(
						'id'=>'evosb','display'=>'show',
						'name'=>'General Subscriber Settings',
						'tab_name'=>'General',
						'fields'=>array(	
							array('id'=>'evosb_2_002','type'=>'yesno',
								'name'=> __('Process as NO event types when no event types for an event is selected','evosb'),
								'legend'=>'When sending new event emails- if the event does not have any event type categories selected, process this as no categories selected as oppose to ALL Selected. Subscribers who have subscribed to either none or all will get email in respective case.',),

							array('id'=>'evosb_draft','type'=>'yesno','name'=>'Save subscribers as draft','legend'=>'This will save susbcribers as draft posts and need to be published to send emails.',),

							array('id'=>'evosb_only_logged','type'=>'yesno','name'=>'Only loggedin users can subscribe','legend'=>'Subscription button will show only if the user is registered and logged into your site'),

							array('id'=>'evosb_del_sub','type'=>'dropdown','name'=>'How to handle unsubscriptions','legend'=>'How should the unsubcriptions be handled in backend','options'=>array('def'=>'Change status to inactive','del'=>'Trash subscriber from system') ),

							array('id'=>'evosb_1_003','type'=>'text','name'=>'Link to Privacy Policy page','legend'=>'This will be shown as a link below subscribe button in the form'),
							array('id'=>'evosb_1_004','type'=>'text','name'=>'Link to Terms of Use Page','legend'=>'This will be shown as a link below subscribe button in the form')
							,							

							array('id'=>'evcal_sub','type'=>'subheader','name'=>'Appearance and Language Settings'),
							array('id'=>'evcal_sub','type'=>'note',
							'name'=>'Appearance and styles for the subscription form can be editted from <b><u>myEventon > Settings > Appearance</u></b> '),
							array('id'=>'evcal_sub','type'=>'note',
							'name'=>'Text for the form can be edited from <b><u>myEventon > Language</u></b> '),
							array('id'=>'evosb_csv','type'=>'customcode','name'=>'Preview Emails','code'=>$this->__evosb_settings_part_csv()
							),	
							
					)),array(
						'id'=>'evosb2',
						'name'=>'Subscriber Form Field Settings',
						'tab_name'=>'Subscriber Form',
						'icon'=>'list-alt',
						'fields'=>array(
							/*array('id'=>'evosb_001','type'=>'yesno','name'=>'Inherit calendar event type filter values for subscription form','legend'=>'If the calendar has certain event type events showing, using this option will allow users to subscribe only to those events instead of having to choose -- and these event types will be set when form opens at first.',),*/

							array('id'=>'evosb_002', 'type'=>'checkboxes','name'=>'Additional form fields to show: <i>(NOTE: <b>Email Address</b> is required field. Select below additional fields to show on subscription form.)<br/><br/>Selecting none of the categories below will subscribe a subscriber to all event categories.</i>',
								'options'=> $this->__evosb_settings_part_fields(),
							),
							array('id'=>'evcal_sub','type'=>'subheader','name'=>'Category Settings'),
							array('id'=>'evosb_show_cat','type'=>'yesno',
								'name'=>'Show all event type category terms'
							),
							array(
								'id'=>'evosb_show_loc','type'=>'yesno',
								'name'=>'Show all locations items'
							),
							array(
								'id'=>'evosb_show_org','type'=>'yesno',
								'name'=>'Show all organizers items'
							),
							
					)),array(
						'id'=>'evosb3',
						'name'=>'Email Settings for Subscriber',
						'tab_name'=>'Email Settings','icon'=>'inbox',
						'fields'=>array(
							array('id'=>'evcal_sub','type'=>'subheader','name'=>'New Event Email Settings'),
								array('id'=>'evosb_autosend','type'=>'yesno',
									'name'=>'Auto notify subscribers of new event, when the event is published',
									'legend'=>'This will auto send new event email when event is published without having to set individually on event edit page. This can be overridden by event edit page and disabling email subscribers button.',),
								array('id'=>'evosb_more_link','type'=>'text','name'=>'Custom "More Information" link for new event email. Use complete url with http://','legend'=>'You can use this to put a link to a different page that would be used for "More Information" button in the new event email'),

								array('id'=>'evosb_cancel_notif','type'=>'yesno',
									'name'=>'Auto notify subscribers of event cancellation',
									'legend'=>'This will auto send event cancellation notice email when event is set as cancelled event. The reason you set for cancellation will be in the notification email (set via event edit page)',),
							
							array('id'=>'evosb_3_000','type'=>'subheader','name'=>'All email sent information'),

							array('id'=>'evosb_1_001','type'=>'yesno','name'=>'Subscriber must verify email before receving emails','legend'=>'This will send the subscriber a verification email to very their email address before receiving event update emails',),
							array('id'=>'evosb_1_002','type'=>'yesno','name'=>'Send out confirmation email upon new subscriptions - to subscriber'),
							
							array('id'=>'evosb_1_005','type'=>'yesno','name'=>'Receive notification email upon new subscriptions','afterstatement'=>'evosb_1_005'),
								array('id'=>'evosb_1_005','type'=>'begin_afterstatement'),
								array('id'=>'evosb_3_notif_email','type'=>'text','name'=>'Email address(s) to receive notification email. Multiple email addresses seperate by commas' ,'default'=>$site_email),
								array('id'=>'evosb_3_006','type'=>'text','name'=>'Subject Line: Notification email','default'=>'You have a new subscriber!'),
								array('id'=>'evosb_1_005','type'=>'end_afterstatement'),

							array('id'=>'evosb_3_001','type'=>'text','name'=>'"From" Name','default'=>$site_name),
							array('id'=>'evosb_3_002','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
							
							array('id'=>'evosb_3_003','type'=>'text','name'=>'Subject Line: Subscription Confirmation','default'=>'Thank you for subscribing to our site!'),

							array('id'=>'evosb_3_004','type'=>'text','name'=>'Subject Line: Verify Subscription','default'=>'Verify your subscription'),

							array('id'=>'evosb_3_005','type'=>'text','name'=>'Subject Line: New Event','default'=>'New Event: {event-name}'),
							array('id'=>'evosb_3_cancel','type'=>'text','name'=>'Subject Line: Cancel Event','default'=>'New Event: {event-name}'),
							
							array('id'=>'evosb_3_unsubcribe','type'=>'text','name'=>'Subject Line: Unsubscription','default'=>'Unsubscribe Confirmation'),


							array('id'=>'evosb_3_000','type'=>'subheader','name'=>'HTML Template'),
							

							array('id'=>'evosb_3_000','type'=>'note','name'=>'To override and edit the email template copy the email PHP file from <code>../eventon-subscriber/templates/</code> to  <code>../yourtheme/eventon/subscriber/</code> folder.'),

							array('id'=>'evosb_3_000','type'=>'subheader','name'=>'Preview email templates'),
							array('id'=>'evosb_3_000','type'=>'customcode','name'=>'Preview Emails','code'=>$this->__evosb_settings_part_preview_email()
							),	
					)),array(
						'id'=>'evosb4',
						'name'=>'Third Party Plugin for Subscriber',
						'tab_name'=>'Third Party','icon'=>'plug',
						'fields'=>array(
							array('id'=>'evosb_4_000','type'=>'subheader','name'=>'You can connect to other third party supported programs in here'),
							array('id'=>'evosb4_mailchimp','type'=>'yesno','name'=>'Activate MainChimp for subscriber addon','afterstatement'=>'evosb4_mailchimp'),
								array('id'=>'evosb4_mailchimp','type'=>'begin_afterstatement'),
								array('id'=>'evosb4_mailchimp_api','type'=>'text','name'=>'MailChimp API key',),
								array('id'=>'evosb4_003a','type'=>'note','name'=>'NOTE: Once you enter API key, Click save changes then you will see available email lists to choose to add subscribers to.',),
								array('id'=>'evosb4_forms','type'=>'customcode','name'=>'MailChimp Forms','code'=>$this->__evosb_mailchimpforms()
								),	
								array('id'=>'evosb4_003a','type'=>'note','name'=>'<a target="_blank" href="http://kb.mailchimp.com/accounts/management/about-api-keys">How to find API Key for MailChimp</a></br><a href="http://kb.mailchimp.com/lists/growth/create-a-new-list" target="_blank">How to create a new Email List in Mailchimp</a>.',),

								array('id'=>'evosb4_mailchimp','type'=>'end_afterstatement'),
					))
				);
						
				$eventon->load_ajde_backender();						
				$evcal_opt = get_option('evcal_options_evcal_sb');
				print_ajde_customization_form($customization_pg_array, $evcal_opt);
												
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
			</div>
			
			</form>	
			<?php
		}

		function __evosb_mailchimpforms(){
			$evcal_opt = get_option('evcal_options_evcal_sb');


			$APIKEY = !empty($evcal_opt['evosb4_mailchimp_api'])?$evcal_opt['evosb4_mailchimp_api']: false;
			if(!$APIKEY) return;



			$MailchimpList = !empty($evcal_opt['evosb4_mailchimp_list'])? $evcal_opt['evosb4_mailchimp_list']: false;

			require_once(EVOSB()->plugin_path.'/includes/lib/mailchimp/MailChimp.php');

			if(!class_exists('MailChimp')) return false;


			$MAILCHIMP = new MailChimp( $APIKEY);
			$LIST = $MAILCHIMP->get('lists');

			
			$noListMsg = "<p>".__('We could not find any lists in your mailChimp account','eventon')."</p>";
			$opt = array();

			// if there are lists
			if(isset($LIST) && !empty($LIST)){
				foreach($LIST['lists'] as $list){
					$opt[$list['id']] = $list['name'].' (Subscriber Count: '.(!empty($list['stats']['member_count'])? $list['stats']['member_count']:'0').')';
				}
				if(sizeof($opt) > 0){
					$opt = array_merge(array('none'=>'None'), $opt);
					$output = "<p>".__('Select email list to add all new subscribers','eventon')."</p>";

					$output .= "<p><select name='evosb4_mailchimp_list'>";
					foreach($opt as $ID=>$list){
						$selected = ($MailchimpList && $MailchimpList == $ID)?'selected="selected"':'';
						$output .= "<option {$selected} value='{$ID}'>{$list}</option>";
					}
					$output .= "</select></p>";

					return $output;
					
				}else{	return $noListMsg;	}
			}else{	return $noListMsg;	}	
		}

		function __evosb_settings_part_csv(){
			$exportURL = add_query_arg(array(
			    'action' => 'evosb_generate_csv',
			), admin_url('admin-ajax.php'));

			return "<span>".__('Download all the verified subscribers information as CSV file.','eventon_sb')."</span><br/><a style='margin-top:10px' class='evo_admin_btn btn_secondary' href=". $exportURL .">".__('Download (CSV)','eventon_sb')."</a>";
		}

		function __evosb_settings_part_fields(){
			$evoOpt = get_option('evcal_options_evcal_1');

			$_add_tax_count = evo_get_ett_count($evoOpt);
			$_tax_names_array = evo_get_ettNames($evoOpt);

			
			$arr = array();

			$arr['name']=__('Your Name','eventon');

			// additional taxonomies
			for($n=1; $n<= $_add_tax_count; $n++){
				$ab = $n==1?'':'_'.$n;
				$__tax_fields = 'event_type'.$ab;
				$__tax_name = $_tax_names_array[$n];
				$arr[$__tax_fields]=__($__tax_name.' (Category #'.$n.')','eventon');
			}

			$arr['location']=__('Event Location','eventon');
			$arr['organizer']=__('Event Organizer','eventon');
			

			return $arr;
		}
		function __evosb_settings_part_preview_email(){
			ob_start();
			echo "<a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_sb&action=verification#evosb3' class='evo_admin_btn btn_triad'>Verification Email</a> <a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_sb&action=confirmation#evosb3' class='evo_admin_btn btn_triad'>Confirmation Email</a> <a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_sb&action=notification#evosb3' class='evo_admin_btn btn_triad'>Notification Email</a>  <a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_sb&action=newevent#evosb3' class='evo_admin_btn btn_triad'>New Event Email</a>  <a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_sb&action=cancelevent#evosb3' class='evo_admin_btn btn_triad'>Cancel Event Email</a>";
			
			if(!empty($_GET['action'])){
				echo $this->get_email_preview('test@msn.com',$_GET['action']);
			}
			return ob_get_clean();
		}
	
	// when event saved, Event email notifications
		function event_emailing($fields='', $post_id){

			$post = get_post($post_id);
			if($post->post_type != 'ajde_events')	return;

			if(empty($_POST['post_status']) || (!empty($_POST['post_status']) && $_POST['post_status']!='publish'))
				return;

			if($post->post_status != 'publish') return;


			$EVENT = new EVO_Event($post_id);

			// send new event email
				$send_new_event_email = true;

				// check if set to send email via event edit page
				if((!empty($_REQUEST['_evosb_send_mail']) && $_REQUEST['_evosb_send_mail']=='no')) $send_new_event_email = false;
				
				/*
				$evosb_autosend = (!empty(EVOSB()->frontend->evoOpt_sb['evosb_autosend']) && EVOSB()->frontend->evoOpt_sb['evosb_autosend']=='yes')? true: false;
				if($evosb_autosend) $send_new_event_email = true;
				*/

				if($EVENT->check_yn('_evosb_email_sent')) 	$send_new_event_email = false;

				// if new event email already sent
				if( $send_new_event_email){

					$__emails_list = $this->_get_subscribers_for_event($EVENT->ID);		
					if(empty($__emails_list)) 	return;

					// mark on event that a email is sent
					update_post_meta($post_id, '_evosb_email_sent','yes');

					// actually sending new event email
					$email_status = EVOSB()->frontend->send_email(array(
						'to'=>$__emails_list, 
						'type'=>'newevent', 
						'output'=>'send', 
						'args'=>	array(
								'event-name'=>$EVENT->get_title(),
								'e_id'=>$post_id,
							)
						)
					);
				}

			// cancel event notification
				$send_cancel_event_email = true;
				
				// if cancel emails not set to be sent via settings
				$evosb_cancel_notif = (!empty(EVOSB()->frontend->evoOpt_sb['evosb_cancel_notif']) && EVOSB()->frontend->evoOpt_sb['evosb_cancel_notif']=='yes')? true: false;
				
				if(!$evosb_cancel_notif) $send_cancel_event_email = false;
				
				// if event is not cancelled
				if($send_cancel_event_email){
					$send_cancel_event_email = (isset($_REQUEST['_cancel']) && $_REQUEST['_cancel'] == 'yes')? true: false;

					// cancelled using event status in eventon @v2.8.10
					if(isset($_REQUEST['_status']) && $_REQUEST['_status'] == 'cancelled') 
						$send_cancel_event_email = true;
				}

				if($send_cancel_event_email){
					$__emails_list = $this->_get_subscribers_for_event($EVENT->ID);		
					if(empty($__emails_list)) 	return;

					// mark on event that a email is sent
					update_post_meta($post_id, '_evosb_email_sent','yes');

					// actually sending new event email
					$email_status = EVOSB()->frontend->send_email(array(
						'to'=>$__emails_list, 
						'type'=>'cancelevent', 
						'output'=>'send', 
						'args'=>	array(
								'event-name'=>$EVENT->get_title(),
								'e_id'=>$post_id,
								'EVENT'=> $EVENT
							)
						)
					);
				}

		}

	// subscriber list
		function _get_subscribers_for_event($event_id){
			// get emails list to send email to
				$opt = get_option('evcal_options_evcal_1');				
				$term_count = evo_get_ett_count($opt);

				// all or none values for event types
				$opt_sb = get_option('evcal_options_evcal_sb');
				$all_none = (!empty($opt_sb['evosb_2_002']) && $opt_sb['evosb_2_002']=='yes')?'no':'all';
					

			// ALL EVENT's TAX
				$all_terms = array();

				// event type taxonomies
				for($x=1; $x<=evo_get_ett_count($opt); $x++ ){
					$taxonomy = ($x==1)? 'event_type':'event_type_'.$x;
					$terms = get_the_terms($event_id, $taxonomy);
					// if there are terms set for event

					if($terms && ! is_wp_error( $terms )){
						foreach ( $terms as $term ) {
							$all_terms[$taxonomy][] = $term->term_id;
						}
					}else{
						$all_terms[$taxonomy][]= $all_none;
					}
				}

				// for location and organizer
				foreach(array('event_location', 'event_organizer') as $tax){
					$terms = get_the_terms($event_id, $tax);
					if($terms && ! is_wp_error( $terms )){
						foreach ( $terms as $term ) {
							$all_terms[$tax][] = $term->term_id;
						}
					}else{
						$all_terms[$tax][]= $all_none;
					}
				}

				//print_r($all_terms);

			// get emails
				$__emails_list = array();
				$subscribers = new WP_Query(array(
					'post_type'=>'evo-subscriber',
					'posts_per_page'=>-1,
				));

				if($subscribers->have_posts()):
					//print_r($subscribers->posts);
					
					// EACH subscriber email
					foreach($subscribers->posts as $SUB){
					
						$pmv = get_post_custom($SUB->ID);
						
						// make sure verification is passed
						$verify_req = (!empty($pmv['verification_required']) && $pmv['verification_required'][0]=='yes')? true:false;
						$verified = (!empty($pmv['verified']) && $pmv['verified'][0]=='yes')? true:false;
						$status = (!empty($pmv['status']) && $pmv['status'][0]=='yes')? true:false;

						if(!$status) continue;

						if($verify_req && !$verified )	continue;


						// EACH event taxonomy
						foreach($all_terms as $_tax=>$_terms){
							
							// check if email in list and tax value is not empty for subscriber
							if( isset($pmv['email'][0]) && in_array($pmv['email'][0], $__emails_list) 	|| empty($pmv[$_tax]) )
								continue;							

							// event term for this tax
							$subscriber_term = (!empty($pmv[$_tax]) )? 	$pmv[$_tax][0]:'none';					
							$subscriber_term = explode(',', $subscriber_term);
							$subscriber_term = array_filter($subscriber_term); // remove empty

							// check if subscribed to this event tax terms
							$intersect = array_intersect($subscriber_term, $_terms);

							//echo $pmv['email'][0]. '=>'.$_tax.' '.$all_terms[$_tax][0].' ='.$subscriber_term[0].' '.count($intersect).'<br/>';
							//print_r($subscriber_term);

							if( 
								(!empty( $intersect)&& count($intersect)>0 )	
								|| (isset($subscriber_term[0]) && $subscriber_term[0]=='all' && isset($all_terms[$_tax][0]) && $all_terms[$_tax][0] != 'no' )
								|| $all_terms[$_tax][0] =='all'
							){
								$__emails_list[] = urldecode($pmv['email'][0]);
								continue;
							}
						}
					}
					
				endif;
			return $__emails_list;
		}

	// get event data for emails
		function get_event_data($event_id, $event_pmv){

			$EVENT = new EVO_Event($event_id, $event_pmv);

			// location
			$LD = $EVENT->get_location_data();
			$location = '';			
			if($LD){
				if( isset($LD['location_name'])) $location = $LD['location_name'];
				if( isset($LD['location_address'])) $location .= ': '. $LD['location_address'];
			}

			// organizer
			$organizer = '';
			$OD = $EVENT->get_organizer_data();
			if($OD){
				$organizer = $OD['organizer_name'];
			}
			
			
			// Date and time
				$datetime = new evo_datetime();
				$correct_unix = $datetime->get_correct_event_repeat_time($event_pmv, 0);
				$time_string = $datetime->get_formatted_smart_time($correct_unix['start'], $correct_unix['end'],$event_pmv);

			
			//content
			$content = (!empty($_POST['content']))? $_POST['content']:'';
			if(!empty($content)){				
				$content = str_replace(']]>', ']]&gt;', $content);
				$content = stripslashes($content);
				$content = substr($content, 0, 1000).' [..]';
			}

			// cancellation reason
				$_cancel_reason = '';
				if(isset($_POST['_cancel_reason'])) $_cancel_reason = $_POST['_cancel_reason'];

			// event image
				$image_src = '';
				if(!empty($event_id)){
					$img_id =get_post_thumbnail_id($event_id);
					if($img_id!=''){
						$img_src = wp_get_attachment_image_src($img_id,'thumbnail');
						$image_src = $img_src[0];
					}
				}

			return array(
				'location'=>$location,
				'time_string'=>$time_string,
				'organizer'=>$organizer,
				'content'=>$content,
				'imagesrc'=>$image_src,
				'_cancel_reason'=> $_cancel_reason
			);
		}

	// icons
		function custom_icons($array){
			$array[] = array('id'=>'evcal__evosub_001','type'=>'icon','name'=>'Subscriber Icon','default'=>'fa-envelope-o');
			return $array;
		}
	// Appearnace section
		function appearance_settings($array){			
			$new[] = array('id'=>'evosub','type'=>'hiddensection_open','name'=>'Subscriber Styles' ,'display'=>'none');
			$new[] = array('id'=>'evosub','type'=>'fontation','name'=>'Subscribe Button',
				'variations'=>array(
					array('id'=>'evosub_1', 'name'=>'Text Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evosub_2', 'name'=>'Background Color','type'=>'color', 'default'=>'78aabc'),					
				)
			);

			$new[] = array('id'=>'evotx','type'=>'hiddensection_close',);
			return array_merge($array, $new);
		}
		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.ajde_evcal_calendar .evosub_subscriber_btn.evcal_btn, #evoSUB_form .form .formIn button#evosub_submit_button, .evosub_subscriber_btn.evcal_btn',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evosub_2',	'default'=>'78aabc'),
						array('css'=>'color:#$', 'var'=>'evosub_1',	'default'=>'ffffff'),
					)
				)		
			);

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
	// language settings additinos
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: SUBSCRIBER'),
					array('label'=>'Subscribe to this calendar','name'=>'evoSUB_001',),
					array('label'=>'Please subscribe to receive email updates about our awesome events!','name'=>'evoSUB_002',),

					array('label'=>'By subscribing you agree with','name'=>'evoSUB_002e',),
					array('label'=>'Terms of Use','name'=>'evoSUB_002f',),
					array('label'=>'Privacy Policy','name'=>'evoSUB_002g',),

					array('label'=>'Your Name','name'=>'evoSUB_002a',),
					array('label'=>'Your Email Address','name'=>'evoSUB_002b',),				
					array('label'=>'Required field missing!','name'=>'evoSUB_003',),
					array('label'=>'Invalid Email address','name'=>'evoSUB_004',),
					array('label'=>'Email Address Exists Already','name'=>'evoSUB_004a',),
					array('label'=>'Could not create subscriber, try later!','name'=>'evoSUB_004b',),
					
					array('label'=>'Subscribe','name'=>'evoSUB_005',),

					array('label'=>'Thank you for subscribing to our calendar!','name'=>'evoSUB_006',),
					array('label'=>'We have sent you a verification email, please verify your email address','name'=>'evoSUB_007',),

					array('type'=>'subheader','label'=>'Subscriber Page Text'),
						array('label'=>'Subscriber Manager','var'=>'1'),

						array('label'=>'Successfully verified your subscription!','var'=>'1'),
						array('label'=>'Successfully subscribed back to system!','var'=>'1'),
						array('label'=>'Subscription Already Verified!','var'=>'1',),
						array('label'=>'Email address does not have a match in our database!','var'=>'1',),
						array('label'=>'Verification code did not match to database for the subscription!','var'=>'1',),
						array('label'=>'Successfully unsubscribed from the system!','var'=>'1',),
						array('label'=>'Could not find the email address in our system!','var'=>'1',),
						array('label'=>'Unsubscribe from our calendar','var'=>'1',),
						array('label'=>'Confirm Unsubscription','var'=>'1',),
						array('label'=>'Manage your subscription to our calendar','var'=>'1',),
						array('label'=>'Unsubscribe','var'=>'1',),
						
						array('label'=>'Update Subscription','var'=>'1',),
						array('label'=>'You are subscribed to below event categories','var'=>'1',),
						array('label'=>'Could not find your email in our system!','var'=>'1',),
						array('label'=>'Type your email address','var'=>'1',),
						array('label'=>'Access Subscription System','var'=>'1',),
						array('label'=>'You are subscribed in our system!','var'=>'1'),
						array('label'=>'Subscription Information Updated!','var'=>'1'),
						array('label'=>'Please type in your email address to unsubscribe!','var'=>'1'),
						array('label'=>'Incorrect Email Address!','var'=>'1'),

						array('label'=>'Your subscription information','var'=>'1'),
						array('label'=>'Email Address','var'=>'1'),
						array('label'=>'Subscription Status','var'=>'1'),
						array('label'=>'Not Subscribed','var'=>1),
						array('label'=>'Subscribed','var'=>1),

						array('label'=>'Subscribe back to our calendar','var'=>'1'),
						array('label'=>'Event Subscription System','var'=>'1'),
					array('type'=>'togend'),

					array('type'=>'subheader','label'=>'Subscription Confirmation Email'),
						array('label'=>'Thank you for subscribing to our calendar events!','var'=>'1'),
						array('label'=>'You can manage your subscription settings from the below link.','var'=>'1'),
						array('label'=>'NOTE: If clicking on the link does not work, please copy the link and paste it in your browser window to verify your email address.','var'=>'1'),
						
					array('type'=>'togend'),
					array('type'=>'subheader','label'=>'Unsubscription Confirmation Email'),
						array('label'=>'You have been successfully unsubscribed from our site','var'=>'1'),
						array('label'=>'Thank you!','var'=>'1'),
					array('type'=>'togend'),

					array('type'=>'subheader','label'=>'Verify Subscription Email'),
						array('label'=>'Thank you for subscribing to our calendar events!','var'=>'1'),
						array('label'=>'Please click the link below to verify your email address.','var'=>'1'),
						array('label'=>'Verify your email address','var'=>'1'),
						array('label'=>'NOTE: If clicking on the link does not work, please copy the link and paste it in your browser window to verify your email address.','var'=>'1'),
					array('type'=>'togend'),

					array('type'=>'subheader','label'=>'New Event Email'),
						array('label'=>'New Event','var'=>1),
						array('label'=>'Event Time','var'=>1),
						array('label'=>'Event Details','var'=>1),
						array('label'=>'Location','var'=>1),
						array('label'=>'Organizer','var'=>1),
						array('label'=>'More Information','var'=>1),
						array('label'=>'Add to calendar','var'=>1),
						array('label'=>'Manage your subscription settings','var'=>1),
						array('label'=>'Unsubscribe','var'=>1),
					array('type'=>'togend'),
					array('type'=>'subheader','label'=>'Cancel Event Email'),
						array('label'=>'This Event is Cancelled','var'=>1),
						array('label'=>'Cancellation Reason','var'=>1),
					array('type'=>'togend'),

				array('type'=>'togend'),

				
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	// eventon settings only styles
		function admin_styles(){
			wp_enqueue_style( 'evosb_admin',EVOSB()->plugin_url.'/assets/admin.css');
		}

	// preview of emails that are sent out
		function get_email_preview($to, $type){
			$email = EVOSB()->frontend->send_email(array(
				'to'=>$to, 'type'=>$type, 'key'=>md5(time()), 'output'=>'echo')
			);

			
			return EVOSB()->helper->get_html('email_preview',array(
				'headers'=>$email['headers'],
				'to'=>$email['to'],
				'subject'=>$email['subject'],
				'message'=>$email['message'],
			));
		}

	// Shortcode data
		function add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_SB',
					'name'=>'Subscriber Standalone Button',
					'code'=>'evo_subscribe_btn',
					'variables'=>array(
						array(
							'name'=>'<i>NOTE: This standalone subscriber button can be placed anywhere in your website to prompt subscribe to calendar lightbox form.</i>',
							'type'=>'note',
						),
						array(
							'name'=>'Button Text',
							'placeholder'=>'eg. Subscribe To This Calendar',
							'type'=>'text',
							'var'=>'btn_txt','default'=>'0',
						)
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}

	// if the subscriber post was deleted
		public function trash_subscriber_post($post_id){
			if ( 'evo-subscriber' != get_post_type( $post_id )) return;

			$PMV = get_post_custom($post_id);

       		// check if exist in mailchimp list 
       		if(!empty($PMV['_mailchimp']) && !empty($PMV['email']) && $PMV['_mailchimp'][0]=='added'){
       			// delete subscriber from mailchimp list
       			EVOSB()->frontend->functions->unsubscribe_mailchimp_email($PMV['email'][0], $post_id, true);
       		}
		}

}
