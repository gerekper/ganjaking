<?php
/**
 * 
 * Event Reviewer front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-reviewer/classes
 * @version     0.6
 */
class evore_front{
	private $currentlang;

	public $lang = 'L1';

	function __construct(){
		global $eventon_re;

		include_once('class-functions.php');
		$this->functions = new evo_re_functions();

		add_filter('eventon_eventCard_evore', array($this, 'frontend_box'), 10, 2);
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	

		$this->opt = $eventon_re->opt;
		$this->opt2 = $eventon_re->opt2;

		add_filter('evo_frontend_lightbox', array($this, 'lightbox'),10,1);

		add_action('evo_addon_styles', array($this, 'styles') );
	}

	// STYLES: for the tab page 
		public function register_styles_scripts(){
			if(is_admin()) return false;
			
			$evOpt = evo_get_options('1');
			if( evo_settings_val('evcal_concat_styles',$evOpt, true))
				wp_register_style( 'evo_RE_styles',EVORE()->assets_path.'RE_styles.css');

			wp_register_script('evo_RE_script',EVORE()->assets_path.'RE_script.js', array('jquery'), EVORE()->version, true );
			wp_localize_script( 
				'evo_RE_script', 
				'evore_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evore_nonce' )
				)
			);
			
			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){
			wp_enqueue_script('evo_RE_ease');	
			//wp_enqueue_script('evo_RS_mobile');	
			wp_enqueue_script('evo_RE_script');	
		}
		function print_styles(){
			wp_enqueue_style( 'evo_RE_styles');	
		}
		function styles(){
			global $eventon_re;
			ob_start();
			include_once($eventon_re->plugin_path.'/assets/RE_styles.css');
			echo ob_get_clean();
		}


	// EventON lightbox Call
		function lightbox($array){
			$array['evorev_lightbox']= array(
				'id'=>'evorev_lightbox',
				'CLclosebtn'=> 'evorev_lightbox',
				'CLin'=> 'evorev_lightbox_body',
			);
			return $array;
		}


	// Review EVENTCARD form HTML
		// add Review box to front end
			function frontend_box($object, $helpers){
				global $eventon_re, $eventon;
				$event_pmv = get_post_custom($object->event_id);

				// loggedin user
					$currentUserID = 	$this->functions->get_current_userid();	
					
				// Review enabled for this event
					if( !evo_check_yn($event_pmv,'event_review') ) return;

				// Get the language passed via shortcode or from single events page URL
				$lang = !empty($eventon->evo_generator->shortcode_args['lang'])? 
					$eventon->evo_generator->shortcode_args['lang']:
					(!empty($_GET['l'])? $_GET['l']:'L1');

				
				ob_start();

				echo  "<div class='evorow evcal_evdata_row bordb evcal_evrow_sm evo_metarow_review".$helpers['end_row_class']."' data-eid='".$object->event_id."' data-ri='{$object->__repeatInterval}' data-lang='{$lang}'>
							<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__evore_001', 'fa-star',$helpers['evOPT'] )."'></i></span>
							<div class='evcal_evdata_cell'>							
								<h3 class='evo_h3'>".evo_lang('Event Reviews',$lang,$this->opt2)."</h3>";

					$current_average_rating = $this->functions->get_average_rating($object->event_id,$event_pmv, $object->__repeatInterval );

				echo "<div class='evore_row_inside'>";

					// if this event have a current rating and average
					if($current_average_rating):
						echo "<h3 class='evo_h3 orating'>".evo_lang('Overall Rating:',$lang,$this->opt2)." <span class='orating_stars' title='{$current_average_rating}'>".$this->functions->get_star_rating_html($current_average_rating)."</span> <span class='orating_data'>".$this->functions->get_rating_count($object->event_id,$event_pmv, $object->__repeatInterval)." ".evo_lang('Ratings',$lang,$this->opt2)."</span>";
							echo ((!empty($event_pmv['_rating_data']) && $event_pmv['_rating_data'][0]!='yes') || empty($event_pmv['_rating_data']))? "<span class='extra_data' style='margin-left:5px;'>".evo_lang('Data',$lang,$this->opt2)."</span>":'';
						echo "</h3>";

						// additional rating data
						if( !evo_check_yn($event_pmv, '_rating_data') ):
							echo "<div class='rating_data' style='display:none'>";
							$rate_count = $this->functions->get_rating_ind_counts($object->event_id, $object->__repeatInterval,$event_pmv);
							$rate_sum = ($rate_count && is_array($rate_count))? array_sum($rate_count):0;
							
							for($x=5; $x>0; $x--){
								$width_percentage = round(($rate_count[$x]/$rate_sum)*100);
								echo "<p><span class='rating'>".$this->functions->get_star_rating_html($x)."</span>
									<span class='bar'><em title='{$width_percentage}%' style='width:".($width_percentage)."%'></em></span>
									<span class='count'>".$rate_count[$x]."</span>
								</p>";
							}
							echo "</div>";
						endif;

						// all reviews list
						$reviews_array =  $this->functions->get_all_reviews_for_event($object->event_id, $object->__repeatInterval);
						if(!empty($reviews_array) && count($reviews_array)>0):
							echo "<div class='review_list ". (evo_check_yn($event_pmv,'_all_reviews')?'alllist':'') ."'>";							
								$count = '';
									$count = 1;
									foreach($reviews_array as $review){
										echo "<p class='review ".($count==1?'show':'')."'>
											<span class='rating'>".$this->functions->get_star_rating_html($review['rating'])."</span>";
										echo "<span class='description'>".$review['review']."</span>";
										echo "<span class='reviewer'>".(!empty($review['reviewer'])? $review['reviewer']:'')." on ".$review['date']."</span></p>";
										$count++;
									}					
							echo "</div>";

							// review scrolling controllers
							if($count>2 && !evo_check_yn($event_pmv,'_all_reviews')){
								echo "<div class='review_list_control' data-revs='{$count}'><span class='fa fa-chevron-circle-left' data-dir='prev'></span><span class='fa fa-chevron-circle-right' data-dir='next'></span></div>";
							}

						endif;
					else: // there are no reviews for this event yet
						echo "<h3 class='evo_h3 orating'>".evo_lang('There are no reviews for this event',$lang,$this->opt2)."</h3>";
					endif;

					// write a review button
					if(empty($this->opt['evore_only_logged']) || ($this->opt['evore_only_logged']=='yes' && is_user_logged_in()) || $this->opt['evore_only_logged']=='no'){
						$user_ID = get_current_user_id();
						$user_name = $user_email ='';
						if(!empty($user_ID) && $user_ID && !empty($this->opt['evore_prefil']) && $this->opt['evore_prefil']=='yes' ){
							$user_info = get_userdata($user_ID);
							$user_name = $user_info->display_name;
							$user_email = $user_info->user_email;
						}
						echo "<div class='review_actions'><a class='evcal_btn new_review_btn' data-username='{$user_name}' data-useremail='{$user_email}' data-uid='{$user_ID}' data-eventname='".get_the_title($object->event_id)."'>".evo_lang('Write a Review',$lang,$this->opt2)."</a></div>";
					}


				echo "</div>";

				echo "</div>".$helpers['end'];
				echo "</div>";
				return ob_get_clean();
			}
			
		// save a cookie for Review
			function set_user_cookie($args){
				//$ip =$this->get_client_ip();
				$cookie_name = 'evore_'.$args['email'].'_'.$args['e_id'].'_'.$args['repeat_interval'];
				$cookie_value = 'rated';
				setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
			}
			function check_user_cookie($userid, $eventid){
				$cookie_name = 'evore_'.$eventid.'_'.$userid;
				if(!empty($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]=='rated'){
					return true;
				}else{
					return false;
				}
			}
		// get form messages html
			function get_form_message($code='', $lang=''){
				$array =  array(
					'err'=>evo_lang('Required fields missing',$lang,$this->opt2),
					'err1'=>evo_lang('Invalid nonce, try again later!',$lang,$this->opt2),
					'err2'=>evo_lang('Invalid email address',$lang,$this->opt2),
					'err6'=>evo_lang('Invalid Validation code.',$lang,$this->opt2),
					'err7'=>evo_lang('Could not save review please try later.',$lang,$this->opt2),
					'err8'=>evo_lang('You can only submit once for this event.',$lang,$this->opt2),
					'succ'=>evo_lang('Thank you for submitting your review',$lang,$this->opt2),
				);				
				return (!empty($code))? $array[$code]: $array;
			}
			function get_form_msg($opt, $lang){
				$str='';
				$ar = array('codes'=> $this->get_form_message('' , $lang));
				return "<div class='evore_msg_' style='display:none'>". json_encode($ar)."</div>";
			}
				
		// add eventon review event card field to filter
			function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
				$array['evore']= array(
					'event_id' => $eventid,
					'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
				);
				return $array;
			}
			function eventcard_adds($array){
				$array[] = 'evore';
				return $array;
			}

	// SAVE new Review
		function _form_save_review($args){
			global $eventon_re;
			$status = 0;
			
			// add new review
			if($created_review_id = $this->create_post() ){
				
				// save review data								
				if(!empty($args['name']))
					$this->create_custom_fields($created_review_id, 'name', $args['name']);

				if(!empty($args['email']))
					$this->create_custom_fields($created_review_id, 'email', $args['email']);

				if(!empty($args['review']))		
					$this->create_custom_fields($created_review_id, 'review', $args['review']);		

				$this->create_custom_fields($created_review_id, 'rating', $args['rating']); 
				$this->create_custom_fields($created_review_id, 'e_id', $args['e_id']);

				$__repeat_interval = (!empty($args['repeat_interval']))? $args['repeat_interval']: '0';
				$this->create_custom_fields($created_review_id, 'repeat_interval', $__repeat_interval);
				
				// save loggedin user ID if prefill fields for loggedin enabled
					$prefill_enabled = (!empty($this->opt['evore_prefil']) && $this->opt['evore_prefil']=='yes')? true:false;
					$CURRENT_user_id = $this->functions->get_current_userid();
					if( ($CURRENT_user_id && $prefill_enabled) || !empty($args['uid'])){
						// user ID if provided or find loggedin user id
						$CURRENT_user_id = !empty($args['uid'])? $args['uid']: $CURRENT_user_id;
						$this->create_custom_fields($created_review_id, 'userid',$CURRENT_user_id);						
					}

				$args['review_id'] = $created_review_id;

				if(!empty($this->opt['evore_draft']) && $this->opt['evore_draft']=='yes'){}else{
					// SYNC event's rating value
					$this->functions->add_new_rating($args['rating'], $args['e_id'], $__repeat_interval);
					$this->functions->sync_ratings($args['e_id']);
				}
				
				// send email notification
				$this->send_email_notif($args);
				$status = $created_review_id;

			}else{	$status = 7; // new rsvp post was not created
			}
		
			return $status;
		}

	// EMAIL function 		
		public function _event_date($pmv, $repeat_interval){
			$datetime = new evo_datetime();
			$eventtime = $datetime->get_correct_formatted_event_repeat_time($pmv, $repeat_interval);	
			return $eventtime;	
		}
		// send email confirmation of Review  to submitter
			function get_email_data($args){
				$this->evore_args = $args;

				$email_data = array();

				$from_email = $this->get_from_email();

				$__to_email = (!empty($this->opt['evore_notfiemailto']) )?
					htmlspecialchars_decode ($this->opt['evore_notfiemailto'])
					:get_bloginfo('admin_email');

				$email_data['to'] = $__to_email;			

				if(!empty($email_data['to'])){
					$email_data['subject'] =((!empty($this->opt['evore_notfiesubjest']))? $this->opt['evore_notfiesubjest']: __('New Review Notification','eventon'));
					$filename = 'notification_email';
					$headers = 'From: '.$from_email. "\r\n";
					$headers .= 'Reply-To: '.$args['email']. "\r\n";
					
					$email_data['message'] = $this->_get_email_body($args, $filename);
					$email_data['header'] = $headers;	
					$email_data['from'] = $from_email;	
				}
				return $email_data;
			}

			// notify admin
			function send_email_notif($args){				
				if(!empty($this->opt['evore_notif']) && $this->opt['evore_notif']=='yes'){
					
					$args['html']= 'yes';

					$helper = new evo_helper();

					return $helper->send_email(
						$this->get_email_data($args)
					);
				}
			}
			// return proper from email with name
				function get_from_email(){
					$__from_email = (!empty($this->opt['evore_notfiemailfrom']) )?
						htmlspecialchars_decode ($this->opt['evore_notfiemailfrom'])
						:get_bloginfo('admin_email');
					$__from_email_name = (!empty($this->opt['evore_notfiemailfromN']) )?
						($this->opt['evore_notfiemailfromN'])
						:get_bloginfo('name');
						$from_email = (!empty($__from_email_name))? 
							$__from_email_name.' <'.$__from_email.'>' : $__from_email;
					return $from_email;
				}

		// email body from template file
			function _get_email_body($evore_args, $file){
				
				ob_start();
				$args = $evore_args;
				$file_location = EVO()->template_locator(
					$file.'.php', 
					EVORE()->addon_data['plugin_path']."/templates/", 
					'templates/email/reviewer/'
				);
				include($file_location);
				return ob_get_clean();
			}
			
			// this will return eventon email template driven email body
			// need to update this after evo 2.3.8 release
			function get_evo_email_body($message){
				global $eventon;
				// /echo $eventon->get_email_part('footer');
				ob_start();
				$wrapper = "
					background-color: #e6e7e8;
					-webkit-text-size-adjust:none !important;
					margin:0;
					padding: 25px 25px 25px 25px;
				";
				$innner = "
					background-color: #ffffff;
					-webkit-text-size-adjust:none !important;
					margin:0;
					border-radius:5px;
				";
				?>
				<!DOCTYPE html>
				<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
				<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
					<div style="<?php echo $wrapper; ?>">
						<div style="<?php echo $innner;?>"><?php
				echo $message;
				echo $eventon->get_email_part('footer');
				return ob_get_clean();
			}

	// SHOW all Reviews
		function show_all_reviews_html($atts){
			$reviews = $this->functions->get_all_reviews();

			$defaults = array(
				'header'=> evo_lang('All Reviews'),
				'count'=>0,
				'ratingtype'=>'all'
			);
			$args = !empty($atts)? array_merge($defaults, $atts): $defaults;

			
			ob_start();
			echo "<div class='evore_all_reviews'>";
			if($reviews){

				?>
				<p class='all_reviews_header'><?php echo $args['header'];?></p>
				<?php

				$count =1;
				foreach($reviews as $review):

					// rating restriction
						if( $args['ratingtype'] != 'all' && (int)$review['rating'] < (int)$args['ratingtype']){
							continue;
						}

					// count restriction
						if($args['count'] != '0'|| $args['count']  != 'all'){
							if( $count > (int)$args['count'] ) break;
						}


					?> 
					<p class='review'>
						<span class='rating'><?php echo $this->functions->get_star_rating_html($review['rating']);?></span>
						<?php if(isset($review['review'])):?>
							<span class='description'><?php echo $review['review'];?></span>
						<?php endif;?>
						<span class='reviewer'><?php echo (!empty($review['reviewer'])? $review['reviewer']:'')." on ".$review['date'];?></span>
					</p>
					<?php
					$count++;
				endforeach;

			}else{
				?>
				<p><?php evo_lang_e('There are no reviews at the moment!');?></p>
				<?php
			}
			echo "</div>";

			return ob_get_clean();

		}

	// user review manager
	// not used
		function user_review_manager(){
			global $eventon_re, $eventon;

			$this->register_styles_scripts();
			
			// intial variables
			$current_user = get_user_by( 'id', get_current_user_id() );
			$USERID = is_user_logged_in()? get_current_user_id(): false;
			$current_page_link = get_page_link();

			// loading child templates
				$file_name = 'user_review_manager.php';
				$paths = array(
					0=> TEMPLATEPATH.'/'.$eventon->template_url.'rsvp/',
					1=> $eventon_re->plugin_path.'/templates/',
				);

				foreach($paths as $path){	
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}
				}
			require_once($template);
		}
	
	// SUPPORT functions	
		// RETURN: language
			function lang($variable, $default_text, $lang=''){
				$lang = empty($lang)? $this->lang: $lang;
				global $eventon_re;
				return eventon_get_custom_language($eventon_re->opt2, $variable, $default_text, $lang);
			}
		// function replace event name from string
			function replace_en($string, $replacewith){
				return str_replace('[event-name]', $replacewith, $string);
			}		
		
		function create_post() {			
			$type = 'evo-review';
	        $valid_type = (function_exists('post_type_exists') &&  post_type_exists($type));

	        if (!$valid_type) {
	            $this->log['error']["type-{$type}"] = sprintf(
	                'Unknown post type "%s".', $type);
	        }
	       
	        $title = 'REVIEW '.date('M d Y @ h:i:sa', time());
	        $author = ($this->get_author_id())? $this->get_author_id(): 1;
	        $post_status = (!empty($this->opt['evore_draft']) && $this->opt['evore_draft']=='yes')? 'draft':'publish';

	        $new_post = array(
	            'post_title'   => $title,	            
	            'post_type'    => $type,
	            'post_status'  => $post_status,
	            'post_name'    => sanitize_title($title),
	            'post_author'  => $author,
	        );
	       
	        // create!
	        $id = wp_insert_post($new_post);
	       
	        return $id;
	    }
		function create_custom_fields($post_id, $field, $value) {       
	        add_post_meta($post_id, $field, $value);
	    }
	    function update_custom_fields($post_id, $field, $value) {       
	        update_post_meta($post_id, $field, $value);
	    }
    	function get_author_id() {
			$current_user = wp_get_current_user();
	        return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
	    }	
	    function get_event_post_date() {
	        return date('Y-m-d H:i:s', time());        
	    }
	    // return sanitized additional rsvp field option values
	    function get_additional_field_options($val){
	    	$OPTIONS = stripslashes($val);
			$OPTIONS = str_replace(', ', ',', $OPTIONS);
			$OPTIONS = explode(',', $OPTIONS);
			$output = false;
			foreach($OPTIONS as $option){
				$slug = str_replace(' ', '-', $option);
				$output[$slug]= $option;
			}
			return $output;
	    }
}
