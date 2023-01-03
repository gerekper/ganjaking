<?php 
/*
Plugin Name: Extendons: WooCommerce FAQ Plugin - Store + Product FAQs
Plugin URI: http://extendons.com
Description:WooCommerce FAQs plugin enables your customers to ask product related questions exactly from product pages. This plugin creates an FAQ tab under each product page as well as a separate faqs page for store questions.
Author: Extendons
Version: 1.0.6
Developed By: Extendons
Author URI: http://extendons.com/
Support: http://support@extendons.com
textdomain: extendons_faq_domain
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


//If not user for security purpose
if ( ! defined( 'ABSPATH' ) ) exit; 

	//Exit if woocommerce not installed
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function extendons_check_woocommerce() {

			// Deactivate the plugin
		deactivate_plugins(__FILE__);
		$error_message = __('<div class="error notice"><p>This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be installed and active!</p></div>', 'extendons_faq_domain');
		die($error_message);
	}

	add_action( 'extendons_check_woocommerce', 'my_admin_notice' );

}


//Extends main class
class EXTENDONS_FAQ_MAIN_CLASS {
	
	//constructor main class
	public function __construct() {
		
		$this->module_constant();

		if(is_admin()) {

			require_once( product_question_plguin_dir.'extendons-faq-admin.php');

			require_once( product_question_plguin_dir.'extendons-faq-setting.php');

			require_once( product_question_plguin_dir.'extendons-faq-store.php');

		} else {
			require_once( product_question_plguin_dir.'extendons-faq-store.php');

			require_once( product_question_plguin_dir.'extendons-faq-front.php');
		}

		add_action( 'init', array($this,'extendons_post_type_faq' ));
		
		add_action( 'admin_menu', array($this,'extendons_product_review_post_setting_option'));
		
		add_action( 'admin_init', array($this,'extendons_display_faqsetting_fields'));		
		
		add_action( 'wp_loaded', array( $this,'extendons_scripts_style_textdomain_init'));
		
		add_action( 'wp_loaded', array( $this,'extendons_settings'));

		add_action( 'wp_ajax_addnewquestion', array($this,'sumit_question_front' ));
		add_action( 'wp_ajax_nopriv_addnewquestion', array($this,'sumit_question_front' ));

		add_action( 'wp_ajax_sorting_questions', array($this,'sorting_thequestion' ));
		add_action( 'wp_ajax_nopriv_sorting_questions', array($this,'sorting_thequestion' ));

		add_action( 'wp_ajax_form_comments', array($this,'inserting_commentdate' ));
		add_action( 'wp_ajax_nopriv_form_comments', array($this,'inserting_commentdate' ));

		add_action( 'wp_ajax_question_like', array($this,'question_likedb' ));
		add_action( 'wp_ajax_nopriv_question_like', array($this,'question_likedb' ));

		add_action( 'wp_ajax_question_dislike', array($this,'question_dlikedb' ));
		add_action( 'wp_ajax_nopriv_question_dislike', array($this,'question_dlikedb' ));

		add_action( 'wp_ajax_support_extend_contact', array($this,'support_extendon_callback' ));
		add_action( 'wp_ajax_nopriv_support_extend_contact', array($this,'support_extendon_callback' ));
		
		add_action( 'wp_ajax_extendon_settingopt', array($this,'extendon_settingopt_callback' ));
		add_action( 'wp_ajax_nopriv_extendon_settingopt', array($this,'extendon_settingopt_callback' ));
		register_deactivation_hook( __FILE__,array( $this,  'myplugin_deactivate' ));
		register_activation_hook( __FILE__, array( $this, 'install_module' ) );

		add_filter('login_message', array($this, 'custom_login_message'));

	}

	function custom_login_message() {

		$message = "<div style='text-align:center'><b>Username: </b> <i>demo</i> <br> <b>Password: </b> <i>demo</i></div>";
		return $message;
	}

	public function install_module() {


		$curr_post = $this->the_slug_exists('ext_faq');

		if (empty($curr_post) || $curr_post->post_name != 'ext_faq') {
            // Create post object
			$my_post = array(
				'post_title'    => 'FAQ',
				'post_name'    => 'ext_faq',
				'post_content'  => '[faq]',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'     => 'page',
			);

			
			wp_insert_post( $my_post );
		}

			// flush_rewrite_rules();
	}
	public function myplugin_deactivate() {

		$page = get_page_by_path('ext_faq');
		$id = $page->ID;
		if($id){
			wp_delete_post( $id , true );
		}
		
	}

	public function the_slug_exists($post_name) {
		global $wpdb;

		$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name = '".$post_name."'");
		return $result;
	}
	//extednon saving setting option
	function extendon_settingopt_callback() {
		
		if(isset($_POST['condition']) && $_POST['condition'] == "setting_extend") {

			update_option( 'page_title_setting', $_POST['extend_blocktitle'], null );
			update_option( 'extendons_singlepage_tab', $_POST['single_tab_title'], null );
			update_option( 'post_perpage_setting', $_POST['single_q_limit'], null );
			update_option( 'qustiondisabele_enabled', $_POST['faqpublishpending'], null );
			update_option( 'ask_queston', $_POST['faq_permission'], null );
			update_option( 'likes_queston', $_POST['likes_enable'], null );
			update_option( 'myaccount_perpage_setting', $_POST['questionmyaccount'], null );
			update_option( 'myaccountans_perpage_setting', $_POST['answermyaccont'], null );
			update_option( 'comment_approv', $_POST['commentapproval'], null );
			update_option( 'commentcloseopen', $_POST['commentcloseopen'], null );
			update_option( 'ext_haveaquest', $_POST['ext_haveaquest'], null );
			update_option( 'ext_haveafaqdisable', $_POST['ext_haveafaqdisable'], null );

			update_option( 'extendons_myaccount_blocktitle', $_POST['myaccountbtitle'], null );
			update_option( 'extendons_myaccount_blocktab', $_POST['myaccounttabtitle'], null );

			update_option( 'email_sender_setting', $_POST['sender_email'], null );
			update_option( 'notificationclient_email', $_POST['email_notification'], null );
			update_option( 'notificationclient_comme', $_POST['cemail_notification'], null );
			update_option( 'emailclient_subject', $_POST['email_subject'], null );
			update_option( 'email_content_publish', $_POST['email_content'], null );

			update_option( 'your_secret_key', $_POST['google_secret_key'], null );
			update_option( 'your_site_key', $_POST['google_site_key'], null );

		}

		die();
	}


	//dislike counter
	function question_dlikedb(){
		global $wpdb, $post;

		if(isset($_POST['condition']) && $_POST['condition'] == "dislikecount") {

			$faq_did = $_POST['questionid'];

			$dlike = $_POST['dlike'];

			$dislikecount = get_post_meta( $faq_did, '_faq_dislike', true);	

			$current_ip = $_SERVER['REMOTE_ADDR'];

			$already_exist_ip = get_post_meta($faq_did, '_faq_dlikeip', false);

			if(in_array($current_ip, $already_exist_ip)) {

			} else {

				$likecountupdate = (int)$dislikecount + 1 ;

				update_post_meta( $faq_did, '_faq_dislike', $likecountupdate);

				add_post_meta( $faq_did, '_faq_dlikeip', $current_ip);				
			}

		}

		$dislikecount = get_post_meta( $faq_did, '_faq_dislike', true);

		echo '<span id="discount'.$faq_did .'">('.$dislikecount.')</span>';


		die();
	}


	//like counter
	function question_likedb() {	
		global $wpdb, $post;
		
		if(isset($_POST['condition']) && $_POST['condition'] == "likecount") {
			
			$faq_id = $_POST['questionid'];
			
			$like = $_POST['like'];

			$likecount = get_post_meta( $faq_id, '_faq_likes', true);
			
			$current_ip = $_SERVER['REMOTE_ADDR'];

			$already_exist_ip = get_post_meta($faq_id, '_faq_likeip', false);

			if(in_array($current_ip, $already_exist_ip)) {

			} else {

				$likecountupdate = (int)$likecount + 1 ;

				update_post_meta( $faq_id, '_faq_likes', $likecountupdate);

				add_post_meta( $faq_id, '_faq_likeip', $current_ip);

			}

		}

		$likecount = get_post_meta( $faq_id, '_faq_likes', true);

		echo '<span id="likecount'.$faq_id .'">('.$likecount.')</span>';
		

		die();	

	}

	//main all settings
	function extendons_settings() {

	 	//blocktitle
		$blocktitle = get_option('page_title_setting');	
		if($blocktitle !='') {
			$blocktitle = get_option('page_title_setting');	
		}else {
			$blocktitle = __('Extendons Product Questions (FAQ)', 'extendons_faq_domain'); 
		}

	 	//question per page 
		$faq_perpage = get_option('post_perpage_setting');	
		if($faq_perpage !='') {
			$faq_perpage = get_option('post_perpage_setting');	
		}else {
			$faq_perpage = 10; 
		}

	 	//question approved unapproved
		$faqapprovedun = get_option('qustiondisabele_enabled');	
		if($faqapprovedun !='') {
			$faqapprovedun = get_option('qustiondisabele_enabled');	
		}else {
			$faqapprovedun = "publish"; 
		}

	 	//question approved unapproved
		$commentapp = get_option('comment_approv');	
		if($commentapp !='') {
			$commentapp = get_option('comment_approv');	
		}else {
			$commentapp = "1"; 
		}

	 	//question approved unapproved
		$ask_queston = get_option('ask_queston');	
		if($ask_queston !='') {
			$ask_queston = get_option('ask_queston');	
		}else {
			$ask_queston = "enabled"; 
		}

		$likes_endis = get_option('likes_queston');	
		if($likes_endis !='') {
			$likes_endis = get_option('likes_queston');	
		}else {
			$likes_endis = "enabled"; 
		}

		$captcha_sitek = get_option('your_site_key');	
		if($captcha_sitek !='') {
			$captcha_sitek = get_option('your_site_key');	
		}else {
			$captcha_sitek = "6LfkFykUAAAAANRL3x837zrvDflc8niwan3X-If-"; 
		}

		$captcha_secretk = get_option('your_secret_key');	
		if($captcha_secretk !='') {
			$captcha_secretk = get_option('your_secret_key');	
		}else {
			$captcha_secretk = "6LfkFykUAAAAAJaez_yn2HPmcarBPAT-mGOjInfS"; 
		}


		$emailsender = get_option('email_sender_setting');	
		if($emailsender !='') {
			$emailsender = get_option('email_sender_setting');	
		}else {
			$emailsender = get_option('admin_email'); 
		}

		$emailclient_subject = get_option('emailclient_subject');	
		if($emailclient_subject !='') {
			$emailclient_subject = get_option('emailclient_subject');	
		}else {
			$emailclient_subject = __('Extendons Product Question', 'extendons_faq_domain'); 
		}

		$email_content_publish = get_option('email_content_publish');	
		if($email_content_publish !='') {
			$email_content_publish = get_option('email_content_publish');	
		}else {
			$email_content_publish = __('Dear User your Question was send successuflly, Thanks', 'extendons_faq_domain'); 
		}

		$email_notify = get_option('notificationclient_email');	
		if($email_notify !='') {
			$email_notify = get_option('notificationclient_email');	
		}else {
			$email_notify = "yes"; 
		}

		$commen_notify = get_option('notificationclient_comme');	
		if($commen_notify !='') {
			$commen_notify = get_option('notificationclient_comme');	
		}else {
			$commen_notify = "yes"; 
		}

		$myaccountT = get_option('extendons_myaccount_blocktitle');	
		if($myaccountT !='') {
			$myaccountT = get_option('extendons_myaccount_blocktitle');	
		}else {
			$myaccountT = __('My Q/A', 'extendons_faq_domain'); 
		}

		$myaccounttab = get_option('extendons_myaccount_blocktab');	
		if($myaccounttab !='') {
			$myaccounttab = get_option('extendons_myaccount_blocktab');	
		}else {
			$myaccounttab = __('My Q/A', 'extendons_faq_domain'); 
		}

		$singlepagetabtitle = get_option('extendons_singlepage_tab');	
		if($singlepagetabtitle !='') {
			$singlepagetabtitle = get_option('extendons_singlepage_tab');	
		}else {
			$singlepagetabtitle = __('Extendons Products Questions Tab','extendons_faq_domain'); 
		}

		$commentopencc = get_option('commentcloseopen');	
		if($commentopencc !='') {
			$commentopencc = get_option('commentcloseopen');	
		}else {
			$commentopencc = "open"; 
		}

		$ext_haveaquest = get_option('ext_haveaquest');	
		if($ext_haveaquest !='') {
			$ext_haveaquest = get_option('ext_haveaquest');	
		}else {
			$ext_haveaquest = __('Have A Question', 'extendons_faq_domain'); 
		}

		$havfaqenable = get_option('ext_haveafaqdisable');	
		if($havfaqenable !='') {
			$havfaqenable = get_option('ext_haveafaqdisable');	
		}else {
			$havfaqenable = "show"; 
		}


		$faq_settings = array();

		$faq_settings['blocktitle'] = $blocktitle; 
		$faq_settings['faq_perpage'] = $faq_perpage;
		$faq_settings['faqapprovedun'] = $faqapprovedun;      
		$faq_settings['commentapp'] = $commentapp;  
		$faq_settings['ask_queston'] = $ask_queston;   
		$faq_settings['likes_endis'] = $likes_endis;  
		$faq_settings['captcha_sitek'] = $captcha_sitek;  
		$faq_settings['captcha_secretk'] = $captcha_secretk;
		$faq_settings['commentopencc'] = $commentopencc;  
		$faq_settings['ext_haveaquest'] = $ext_haveaquest;
		$faq_settings['havfaqenable'] = $havfaqenable;  

		$faq_settings['myaccountT'] = $myaccountT;  
		$faq_settings['myaccounttab'] = $myaccounttab;  
		$faq_settings['singlepagetabtitle'] = $singlepagetabtitle;  

		$faq_settings['emailsender'] = $emailsender;  
		$faq_settings['emailclient_subject'] = $emailclient_subject;  
		$faq_settings['email_content_publish'] = $email_content_publish;   
		$faq_settings['email_notify'] = $email_notify;  
		$faq_settings['commen_notify'] = $commen_notify;            

		return $faq_settings;
	}


	//insering comment
	function inserting_commentdate() {

		global $wpdb, $post;

		$setings = $this->extendons_settings();

		$current_user = wp_get_current_user();
		$commappr = $setings['commentapp'];
		$date = date('Y-m-d h:i:s');
		
		if(isset($_POST['mode']) && $_POST['mode'] == "add_comment") {
			
			$wpdb->insert( 
				$wpdb->prefix.'comments', 
				array( 
					'comment_post_ID' => ($_POST["id"]),
					'comment_author' => $current_user->user_login,
					'comment_author_email' => $current_user->user_email, 
					'comment_date' => $date,
					'comment_content' => esc_textarea($_POST["comment"]),
					'comment_approved' => $commappr,
					'user_id' => $current_user->ID 	
				), 
				array( 
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s',
					'%s',
					'%d' 
				));
		}

		if(isset($_POST['comemail']) && $_POST['comemail'] == "emailcomm_settingyes") { 


			$post_id = $_POST["id"];
			$author_email = get_post_meta($post_id, '_product_email_value_key', true);
			$admin_email = $setings['emailsender'];
			$email_subject = $setings['emailclient_subject'];
			$email_q_contnet = $setings['email_content_publish'];
			$to = $author_email;
			$subject = $email_subject;

			$message = "
			<html>
			<head>
			<title>Comment Notification Woocommerece.</title>
			</head>
			<body>
			<table>
			<tr>
			<td>$email_q_contnet</td>
			</table>
			</body>
			</html>
			";

			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\n";
			$headers .= 'From: '.$admin_email.'' . "\r\n";
			$headers .= 'Cc: '.$admin_email.'' . "\r\n";

			mail($to,$subject,$message,$headers);

		}
		
		$lastid = $wpdb->insert_id;
		$coments = get_comment( $lastid ); 	

		echo '<div class="add_thread1">

		<div class="anss-info">
		<p>'.$coments->comment_content.'</p>

		<div class="answered_by">Comment by : '. $coments->comment_author.' on '.date('M j, Y h:i:s A',strtotime( $coments->comment_date)).'</div>
		</div> 

		</div>';

		die();		
	}


	//addind new question ajax callback
	function sumit_question_front() {		
		
		global $wpdb, $post;

		$setings = $this->extendons_settings();	

		if(isset($_POST['mode']) && $_POST['mode'] == "add_question") {

			if(isset($_POST['capt']) && !empty($_POST['capt'])) {
				
				$secret = $setings['captcha_secretk'];

				$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['capt']);

				$responseData = json_decode($verifyResponse);



        		//if($responseData->success) {

				$date = date('Y-m-d h:i:s');

				$wpdb->insert( 
					$wpdb->prefix.'posts', 
					array( 
						'post_title' =>  sanitize_text_field($_POST["question"]), 
						'post_status' =>  sanitize_text_field($setings['faqapprovedun']),
						'post_type'	=> sanitize_text_field('product_review_post'),
						'post_author' => sanitize_text_field($_POST["user"]),
						'post_date'=>  $date
					), 
					array( 
						'%s', 
						'%s', 
						'%s',
						'%d', 
						'%s'
					));

				$lastid = $wpdb->insert_id;
				add_post_meta( $lastid, '_product_email_value_key', sanitize_email($_POST['email']), $unique = false );
				add_post_meta( $lastid, '_product_name_value_key', sanitize_text_field($_POST['name']), $unique = false );
				add_post_meta( $lastid, '_product_id_value_key', sanitize_text_field($_POST['curpid']), $unique = false );
				add_post_meta( $lastid, '_private_question_key', sanitize_text_field($_POST['pri_pub']), $unique = false );
        		//}
			}
			
		}

		if(isset($_POST['email_send']) && $_POST['email_send'] == "send_questionmailyes") {

			$admin_email = $setings['emailsender'];
			$email_subject = $setings['emailclient_subject'];
			$email_q_contnet = $setings['email_content_publish'];

			$questioner_email = $_POST['email'];	

			$to = $questioner_email;
			$subject = $email_subject;

			$message = "
			<html>
			<head>
			<title>Question Woocommerece.</title>
			</head>
			<bodyquestion_dlikedb>
			<table>
			<tr>
			<td>$email_q_contnet</td>
			</table>
			</body>
			</html>
			";

			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\n";
			$headers .= 'From: '.$admin_email.'' . "\r\n";
			$headers .= 'Cc: '.$admin_email.'' . "\r\n";

			mail($to,$subject,$message,$headers);
		}		

		die();
	}

	//ajax question sorting
	function sorting_thequestion() {

		global $wpdb, $post;

		$setings = $this->extendons_settings();
		
		if(is_user_logged_in()) {

			$user = wp_get_current_user();
		} 

		if(isset($_POST['condition']) && $_POST['condition'] == "sorting") {
			
			$sorttting = $_POST['sortval'];
			
			$exploded_val = explode(":",$sorttting); ?>
			
			<!-- Accordion section starts -->
			
			<div class="extendons-accordion">

				<?php 

				$questions = $wpdb->get_results("Select *, m.post_id, m.meta_key, m.meta_value, m1.post_id, m1.meta_key, m1.meta_value from ".$wpdb->prefix."posts p LEFT JOIN ".$wpdb->prefix."postmeta m on (p.ID = m.post_id) LEFT JOIN ".$wpdb->prefix."postmeta m1 on ( p.ID = m1.post_id ) where m.meta_key='_private_question_key' AND m.meta_value != 1 AND m1.meta_key ='_product_id_value_key' AND m1.meta_value LIKE '%".$exploded_val[2]."%' AND p.post_type ='product_review_post' AND p.post_status='publish' ORDER BY  ".$exploded_val[0]." ".$exploded_val[1]); 
				?>

				<ul id="accordion" class="accordion">

					<?php 

					if(count($questions) > 0) {

						foreach ($questions as $question ) {

							$_likescount = get_post_meta($question->ID, '_faq_likes', true);
							if($_likescount > 0){
								$_likescount = get_post_meta($question->ID, '_faq_likes', true);	
							} else{
								$_likescount = "0";
							}

							$_dislikecount = get_post_meta($question->ID, '_faq_dislike', true);
							if($_dislikecount > 0){
								$_dislikecount = get_post_meta($question->ID, '_faq_dislike', true);	
							} else{
								$_dislikecount = "0";
							}

							?>

							<li class="faq-ext">

								<div class="qtoggle">
									<?php echo $question->post_title; ?>
								</div>

								<div class="under">

									<p>
										<?php echo $question->post_content; ?>
									</p>

									<div class="<?php echo ($setings['likes_endis'] == "enabled") ? 'ask_enable' : 'ask_disable'; ?> faq faq_like" id="faq_like<?php echo $question->ID;?>'">


										<button type="button" onclick="Likequestion('<?php echo $question->ID;?>');" class="fa fa-lg fa-thumbs-up likes_extendon">
											<span id="likecount<?php echo $question->ID;?>">(<?php echo $_likescount; ?>)</span>
										</button>

										<button type="button" onclick="Dislikequestion('<?php echo $question->ID;?>');" class="fa fa-lg fa-thumbs-down likes_extendon">
											<span id="discount<?php echo $question->ID;?>">(<?php echo $_dislikecount; ?>)</span>
										</button>

									</div>	

									<div class="answered_by">
										<span><?php _e('Answer by :', 'extendons_faq_domain'); ?> <?php echo get_post_meta($question->ID, "_product_name_value_key", true);?> <?php _e('on', 'extendons_faq_domain'); ?> <?php echo date('M j, Y h:i:s A',strtotime($question->post_date)); ?></span>
									</div>

									<div class="comment-section" id="comment-section<?php echo $question->ID; ?>">

										<?php 	
										$args = array('orderby' => 'comment_post_ID', 'order' => 'DESC', 'post_id' => $question->ID, 'status' => 'approve' ); 
										$comments = get_comments( $args );
										foreach ( $comments as $comment ) { ?>

											<div class="add_thread1">

												<div class="anss-info">
													<p><?php echo $comment->comment_content; ?></p>

													<div class="answered_by"><?php _e('Comment by:', 'extendons_faq_domain'); ?> <?php echo $comment->comment_author; ?> <?php _e('on', 'extendons_faq_domain'); ?> <?php echo date('M j, Y h:i:s A',strtotime( $comment->comment_date)); ?></div>
												</div> 

											</div>

										<?php } ?>

									</div>

									<div class="<?php echo ($setings['commentopencc'] == "open") ? 'ask_enable' : 'ask_disable'; ?> class="add_comments">

										<form class="<?php if($user->ID == 0){ echo "user_not_login"; } ?> comment_form" id="comment_form<?php echo $question->ID;?>" method="post">

											<textarea rows="4" cols="4" data-parsley-required class="faq_comentarea" name="comment" id="comment<?php echo $question->ID;?>"></textarea>

											<input type="hidden" name="postid" id="postid" value="<?php echo $question->ID; ?>" >

											<input type="hidden" name="mode_email_comment" id="emailcomm_setting" value="emailcomm_setting">
											<input type="hidden" name="mode" id="add_comment" value="add_comment" />

											<input id="id_submit_coment" class="field"  onclick="ajaxcommentvali('<?php echo $question->ID;?>')" type="button" value="<?php _e('Add Answer', 'extendons_faq_domain'); ?>" /> 

										</form>	

										<a class="<?php echo ($user->ID > 0) ? 'userlogin' : 'notlogin'; ?>" href="<?php echo wp_login_url(); ?>" target="_blank"><?php _e('Add Answer', 'extendons_faq_domain'); ?></a>

									</div>

								</div>  

							</li>

						<?php } } else { ?>

							<li class="no-faq">

								<p><?php _e('This Product have no Question..!', 'extendons_faq_domain'); ?></p>

							</li>

						<?php } ?>

					</ul>
					<div class="holder"></div>

				</div>

			<?php } ?>
			<!-- Accordion section end -->

			<script type="text/javascript">

				//pagination 
				jQuery(function() {
					jQuery("div.holder").jPages({
						containerID: "accordion",
						previous : "«",
						next : "»",
						perPage:<?php echo $setings['faq_perpage']; ?>,
						minHeight : false,
					});
				});

				//accordion
				jQuery('.qtoggle').click(function(e) {
					e.preventDefault();

					var $this = jQuery(this);

					if ($this.next().hasClass('show')) {
						$this.parent().removeClass('minus');
						$this.next().removeClass('show');
						$this.next().slideUp(350);
					} else {
						$this.parent().parent().find('li .under').removeClass('show');
						$this.parent().parent().parent().find('li.faq-ext').removeClass('minus');
						$this.parent().parent().find('li .under').slideUp(350);
						$this.next().toggleClass('show');
						$this.parent().toggleClass('minus');
						$this.next().slideToggle(350);
					}
				});


				//Comment submission
				function ajaxcommentvali(id) { 

					jQuery('#comment_form'+id).parsley().validate();
					
					var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
					var comment = jQuery('#comment'+id).val(); 
					var mode = jQuery('#add_comment').val();
					var comemail = jQuery('#emailcomm_setting').val();
					if (comment == '' ) {
						return false;
					} else {
						jQuery.ajax({
							url : ajaxurl,
							type : 'post',
							data : {
								action : 'form_comments',
								comment : comment,
								mode : mode,
								comemail : comemail,
								id : id,
							},
							success : function( response ) {
								jQuery('#comment_form'+id ).each(function(){this.reset(); 
								});
								jQuery('#comment-section'+id).append(response);
							}
						});
					}
				}

			</script>

			<?php die();
		}	



	//creating custom post type
		function extendons_post_type_faq() {

			$labels = array(
				'name' => __('Product Question', 'extendons_faq_domain'),
				'singular_name' => __('Product Question', 'extendons_faq_domain'),
				'add_new' => __('Add New Question', 'extendons_faq_domain'),
				'add_new_item' => __('Add New Question', 'extendons_faq_domain'),
				'edit_item' => __('Edit Question', 'extendons_faq_domain'),
				'new_item' => __('Add New Question', 'extendons_faq_domain'),
				'view_item' => __('View Question', 'extendons_faq_domain'),
				'search_items' => __('Search Question', 'extendons_faq_domain'),
				'not_found' =>  __('Nothing found', 'extendons_faq_domain'),
				'not_found_in_trash' => __('Nothing found in Trash', 'extendons_faq_domain'),
				'parent_item_colon' => ''
			);

			$args = array(
				'label'               => __( 'Product Question', 'extendons_faq_domain' ),
				'description'         => __( 'Product Question and reviews', 'extendons_faq_domain' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'comments', 'editor',  'revisions', ),
				'taxonomies'          => array( 'genres' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'menu_icon'           => product_question_url.'img/extendons-24x24.png',
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);

			register_post_type( 'product_review_post', $args );
			flush_rewrite_rules();
		}


	//adding sub menu for question post type	
		function extendons_product_review_post_setting_option() {

			add_submenu_page('edit.php?post_type=product_review_post',
				'Support Faq',
				__('Support', 'extendons_faq_domain'),
				'manage_options',
				'extendons-support',
				array($this, 'extendons_support_team' ));
		}

	//extendons support function
		function extendons_support_team() { ?>

			<div class="wrap extendons-support">

				<h3><?php _e('Welcome to Extendons Support – We are here to help', 'extendons_faq_domain'); ?></h3>

				<div class="about-text"><?php _e('Our customer support team is powered with enthusiasm to serve you the best in solving a technical issue or answering your queries in time. If you have got a question, please do not hesitate to ask us in this easy to fill form, and we assure you a prompt reply.', 'extendons_faq_domain'); ?> 
			</div>
			
			<?php 

			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
			}

			$a = "";	
			foreach ( $active_plugins as $plugin ) {

				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );


				if ( in_array('Extendons', $plugin_data)) { 

					$outpluing = $plugin_data;
					$ext_size = count($plugin_data);
					$total_out_pluing = $ext_size/12;	
				}
				
				if ($plugin_data['AuthorName'] == 'Extendons') { 
					
					$a++;
				}
			}

			?>

			<div class="extendons-logo">
				<img src="<?php echo product_question_url.'img/logo-extendon.png'; ?>" >
				<span class="extendons-faq-version">Version <?php echo $outpluing['Version']; ?></span>
			</div>

			<div class="extendons-support-active">

				<table class="widefat" cellspacing="0" id="status">

					<thead>
						<tr>
							<th><?php _e('Extendon Active Plugin', 'extendons_faq_domain'); ?> (<?php echo $a; ?>)</th>
							<th><?php _e('Version', 'extendons_faq_domain'); ?></th>
							<th><?php _e('Company', 'extendons_faq_domain'); ?> </th>
						</tr>
					</thead>

					<tbody>
						<?php
						foreach ( $active_plugins as $plugin ) {

							$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
							$dirname        = dirname( $plugin );
							$version_string = '';
							$network_string = '';

							if ( in_array('Extendons', $plugin_data)) {

								// Link the plugin name to the plugin url if available.
								if ( ! empty( $plugin_data['PluginURI'] ) ) {
									$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'extendons_faq_domain' ) . '">' . esc_html( $plugin_data['Name'] ) . '</a>';
								} else {
									$plugin_name = esc_html( $plugin_data['Name'] );
								}
								?>
								<tr>
									<td>
										<?php echo $plugin_name; ?>
									</td>
									<td>
										<?php echo $plugin_data['Version']; ?>
									</td>
									<td>
										<?php printf( esc_attr__( 'by %s', 'extendons_faq_domain' ), '<a href="' . esc_url( $plugin_data['AuthorURI'] ) . '" target="_blank">' . esc_html( $plugin_data['AuthorName'] ) . '</a>' ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?>
									</td>
								</tr>
								<?php
							}
						} ?>
					</tbody>

				</table>

			</div>


			<div class="extendons-support-form">

				<h4><?php _e('Contact Extendon Support Team', 'extendons_faq_domain'); ?></h4>
				
				<h5 id="extendon_sup_success"><?php _e('Your message has been successfully sent. We will contact you very soon!', 'extendons_faq_domain'); ?></h5>

				<div class="content">
					<small> For any plugin related issue or Custom Development Project please contact at <b>info@extendons.com</b>.<br>
						<div class="content2">The support timings are Mon - Fri, 9am - 6pm (UTC +5) </div>
					</small>
				</div>

				<div class="extendon-socials">
					<ul class="extend-social-left">
						<li>
							<a target="_blank" href="https://www.facebook.com/extendons/">
								<img src="<?php echo product_question_url.'img/fb.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="https://plus.google.com/u/8/114047538741272702397">
								<img src="<?php echo product_question_url.'img/google_plus.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="http://extendons.com/">
								<img src="<?php echo product_question_url.'img/avatar-80x80.png'; ?>" >
							</a>
						</li>
						<li>
							<a target="_blank" href="https://www.linkedin.com/company/extendons">
								<img src="<?php echo product_question_url.'img/linkedin.png'; ?>">
							</a>
						</li>
						<li>
							<a target="_blank" href="https://twitter.com/extendons">
								<img src="<?php echo product_question_url.'img/twitter.png'; ?>">
							</a>
						</li>
					</ul>
				</div>

			</div>

		</div>

		<!-- <script type="text/javascript">
			
			// support email
			function extendsupport() { 
				
				jQuery('#extendon-form-support').parsley().validate();	
				var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
				var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
				var condition = 'extendons_support_contact';
				var suppextfname = jQuery('#ex_customer_fname').val();
				var suppextlname = jQuery('#ex_customer_lname').val();
				var suppextemail = jQuery('#ex_customer_email').val();
				var suppextnumber = jQuery('#ex_customer_number').val();
				var suppextsubj = jQuery('#ex_customer_subject').val();
				var suppextmasg = jQuery('#ex_customer_message').val();
				if(suppextfname == '' && suppextlname == '' && suppextemail == '' && suppextmasg == '') {
					return false;
				}else if (suppextname == '') { 
					return false;
				}else if (suppextemail == '') {
					return false;
				}else if (!pattern.test(suppextemail)) {
					return false;
				}else if (suppextmasg == '' ) {
					return false;
				}else {

					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'support_extend_contact',
							condition : condition,
							suppextfname : suppextfname,
							suppextlname : suppextlname,
							suppextemail : suppextemail,
							suppextnumber : suppextnumber,
							suppextsubj : suppextsubj,
							suppextmasg : suppextmasg,		

						},
						success : function(response) {
							jQuery('#extendon_sup_success').show().delay(3000).fadeOut();
							jQuery('#extendon-form-support').each(function() {
								this.reset(); 
							});
						}
					});
				}
			}

		</script> -->

	<?php }


	// support email/contact function
	function support_extendon_callback () {
		
		if(isset($_POST['condition']) && $_POST['condition'] == "extendons_support_contact") {

			$suppextfname = $_POST['suppextfname'];
			$suppextlname = $_POST['suppextlname'];
			$support_email = $_POST['suppextemail'];
			$support_number = $_POST['suppextnumber'];
			$support_subject = $_POST['suppextsubj'];
			$support_message = $_POST['suppextmasg'];	

			$to = "support@extendon.com";
			$subject = $support_subject;

			$message = "
			<html>
			<head>
			<title>Question Woocommerece.</title>
			</head>
			<body>
			<table>
			<tr>
			<td>Support Message</td>
			<td>$support_message</td>
			</tr>
			</table>
			</body>
			</html>
			";
			
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\n";
			
			mail($to,$subject,$message,$headers);
			
		}

		die();
	}


	//function for adding setting page layout
	function extendons_product_review_question_setting() { ?>
		
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h3><?php _e('Product Questions Setting Options.', 'extendons_faq_domain'); ?></h3>
			<p><?php _e('Please do not forget to save option.','extendons_faq_domain'); ?></p> 
			<?php settings_errors(); ?>
			
			<?php
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_options';
			?>

			<h2 class="nav-tab-wrapper">

				<a href="edit.php?post_type=product_review_post&page=extendons-faq-setting&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php _e('General Options','extendons_faq_domain'); ?></a>

				<a href="edit.php?post_type=product_review_post&page=extendons-faq-setting&tab=email_option" class="nav-tab <?php echo $active_tab == 'email_option' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Options', 'extendons_faq_domain'); ?></a>
				<a href="edit.php?post_type=product_review_post&page=extendons-faq-setting&tab=captcha_option" class="nav-tab <?php echo $active_tab == 'captcha_option' ? 'nav-tab-active' : ''; ?>"><?php _e('Google Captcha Options','extendons_faq_domain'); ?></a>
			</h2>

			<form method="post" action="options.php">
				<?php
				if( $active_tab == 'general_options' ) { 
					settings_fields("section");
					do_settings_sections("product_review_setting_section");  
				}elseif ( $active_tab == 'email_option'){
					settings_fields("esection");
					do_settings_sections("emial_product_review_setting_section");
				}elseif ($active_tab == 'captcha_option' ) {			         
					settings_fields("gsection");
					do_settings_sections("google_product_review_setting_section");
				}
				submit_button(); ?>          
			</form>
		</div>
	<?php }	


	//page option all fields
	function page_title_faqfunction() {
		?>
		<input type="text" name="page_title_setting" id="page_title_setting" value="<?php echo get_option('page_title_setting'); ?>" />
		<p class="description"><?php _e('Block Title on top of Questions Single Page','extendons_faq_domain'); ?></p>
		<?php
	}
	//show per page question
	function perpage_faqfunction() {
		?>
		<input type="number" min="0" name="post_perpage_setting" id="post_perpage_setting" value="<?php echo get_option('post_perpage_setting'); ?>" />
		<p class="description"><?php _e('How many Question you want to display in Product Single page.<br />Default Post per Page is <strong>10</strong>', 'extendons_faq_domain'); ?></p>
		<?php
	}

	//approval for question pending publish
	function qustionapproval_settingfunc() {
		?>
		<select name="qustiondisabele_enabled">
			<option value="publish"<?php echo selected( get_option('qustiondisabele_enabled'), 'publish') ?>>Publish</option>
			<option value="pending"<?php echo selected( get_option('qustiondisabele_enabled'), 'pending') ?>>Pending</option>
		</select> 
		<p class="description"><?php _e('Status of question Pending or Publish.<br />Default is <strong>Publish</strong>', 'extendons_faq_domain'); ?></p>
		<?php
	}
	//asked question or not
	function qask_question_settingfunc() {
		?>
		<select name="ask_queston">
			<option value="disabled"<?php echo selected( get_option('ask_queston'), 'disabled') ?>>Disabled</option>
			<option value="enabled"<?php echo selected( get_option('ask_queston'), 'enabled') ?>>Enabled</option>
		</select> 
		<p class="description"><?php _e('Hide the Asked Question section or Show.<br />Default Option is <strong>Enabled</strong>','extendons_faq_domain'); ?></p>
		<?php
	}

	//likes enable disable
	function likes_settingfunc() {
		?>
		<select name="likes_queston">
			<option value="disabled"<?php echo selected( get_option('likes_queston'), 'disabled') ?>>Disabled</option>
			<option value="enabled"<?php echo selected( get_option('likes_queston'), 'enabled') ?>>Enabled</option>
		</select> 
		<p class="description"><?php _e('Likes Dislike<br>Default is <strong>Enable</strong>','extendons_faq_domain'); ?></p>
		<?php
	}
	//sender email
	function senderemail_faqfunction() {
		?>
		<input type="text" name="email_sender_setting" id="email_sender_setting" value="<?php echo get_option('email_sender_setting'); ?>" />
		<p class="description"><?php _e('To clear this you simply empty the box and click save changes.<br />IF not set then Email is from <strong>Web Site Admin</strong>','extendons_faq_domain'); ?></p>
		<?php
	}

	//client notificatoin
	function clientnotification_faqfunction() {
		?>
		<select name="notificationclient_email">
			<option value="yes"<?php echo selected( get_option('notificationclient_email'), 'yes') ?>>Yes</option>
			<option value="no"<?php echo selected( get_option('notificationclient_email'), 'no') ?>>No</option>
		</select> 
		<p class="description"><?php _e('For Client Notificaiton about comment in his post.<br />Default is <strong>Enabled</strong>','extendons_faq_domain'); ?></p>
		<?php
	}
	//email subject
	function emailsubject_faqfunction() {
		?>
		<input type="text" name="emailclient_subject" id="emailclient_subject" value="<?php echo get_option('emailclient_subject'); ?>" />
		<p class="description"><?php _e('To clear this you simply empty the box and click save changes.<br />IF not set then default subject is <strong>Woocommerece FAQs</strong>', 'extendons_faq_domain'); ?></p>
		<?php
	}

	//email contnet on question publish
	function emailcontent_faqfunction() {
		?>
		<textarea name="email_content_publish" id="email_content_publish" ><?php echo get_option('email_content_publish'); ?></textarea>
		<p class="description"><?php _e('Content For Email When a Question is Publish.', 'extendons_faq_domain'); ?></p>
		<?php
	}

	//email contnet on comment
	function emailcontentcomment_faqfunction() {

		?>
		<!-- <textarea name="comment_content_publish" id="comment_content_publish"></textarea> -->
		<!-- <p></p> -->
		<?php
	}

	//secret key
	function secretkey_faqfunction() {
		?>
		<input type="text" name="your_secret_key" id="your_secret_key" value="<?php echo get_option('your_secret_key'); ?>" />
		<p>Go to Google recaptcha site <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Google Recaptcha</a> then click on top right reCaptcha button and follow the Instructions.<br>
			Register you site by giving url path and get the secret key.
		</p>
		<?php
	}

	//Site key
	function sitekey_faqfunction() {
		?>
		<input type="text" name="your_site_key" id="your_site_key" value="<?php echo get_option('your_site_key'); ?>" />
		<p>Go to Google recaptcha site <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Google Recaptcha</a> then click on top right reCaptcha button and follow the Instructions.<br>
			Register you site by giving url path and get the site key.
		</p>
		<?php
	}

	//show questions perpage in my account
	function myaccountquestion_function() {
		?>
		<input type="number" min="0" name="myaccount_perpage_setting" id="myaccount_perpage_setting" value="<?php echo get_option('myaccount_perpage_setting'); ?>" />
		<p class="description"><?php _e('How many Question you want to disply in My Account.<br />Default listing is <strong>10</strong>', 'extendons_faq_domain'); ?></p>
		<?php
	}

	//show answrs perpage in my account
	function myaccountanswers_function() {
		?>
		<input type="number" min="0" name="myaccountans_perpage_setting" id="myaccountans_perpage_setting" value="<?php echo get_option('myaccountans_perpage_setting'); ?>" />
		<p class="description"<?php _e('How many Answers you want to disply in My Account.<br />Default Listing is <strong>10</strong>','extendons_faq_domain'); ?></p>
		<?php
	}


	//single page tab title
	function singlepagetab_title() {
		?>
		<input type="text" name="extendons_singlepage_tab" id="extendons_singlepage_tab" value="<?php echo get_option('extendons_singlepage_tab'); ?>" />
		<p class="description"><?php _e('Product Single Tab Title','extendons_faq_domain'); ?></p>
		<?php
	}


	//myaccout block title
	function myaccountblock_title() {
		?>
		<input type="text" name="extendons_myaccount_blocktitle" id="extendons_myaccount_blocktitle" value="<?php echo get_option('extendons_myaccount_blocktitle'); ?>" />
		<p class="description"><?php _e('Title display on MyAccount Page','extendons_faq_domain'); ?></p>
		<?php
	}


	//myaccount tab title
	function myaccounttab_title() {
		?>
		<input type="text" name="extendons_myaccount_blocktab" id="extendons_myaccount_blocktab" value="<?php echo get_option('extendons_myaccount_blocktab'); ?>" />
		<p class="description"><?php _e('MY Account Tab Title','extendons_faq_domain'); ?></p>
		<?php
	}



	//comment approved unapproved
	function commentapprove_faqfunction() {
		?>
		<select name="comment_approv">
			<option value="1"<?php echo selected( get_option('comment_approv'), '1') ?>>Approved</option>
			<option value="0"<?php echo selected( get_option('comment_approv'), '0') ?>>Unapproved</option>
		</select> 
		<p class="description"><?php _e('Comments Approved/Unapproved<br />Default is <strong>Apprved</strong>','extendons_faq_domain'); ?></p>
		<?php
	}

	//register settion fields and options
	function extendons_display_faqsetting_fields() {

		add_settings_section('section', 'Extendons Woocommerece FAQ General Setting Section', null, 'product_review_setting_section');
		add_settings_section('esection', 'Extendons Woocommerece FAQ Email Setting Section', null, 'emial_product_review_setting_section');
		add_settings_section('gsection', 'Extendons Woocommerece FAQ Google Captcha Setting Section', null, 'google_product_review_setting_section');

		add_settings_field('page_title_setting', 'Block Title', array($this,'page_title_faqfunction'), 'product_review_setting_section', 'section');

		add_settings_field('extendons_singlepage_tab', 'Product Single Tab Title', array($this,'singlepagetab_title'), 'product_review_setting_section', 'section');
		
		add_settings_field('post_perpage_setting', 'Show Total Questions', array($this,'perpage_faqfunction'), 'product_review_setting_section', 'section');

		add_settings_field('qustiondisabele_enabled', 'Question Approval', array($this,'qustionapproval_settingfunc'), 'product_review_setting_section', 'section');

		add_settings_field('ask_queston', 'Ask Question enable or disable', array($this,'qask_question_settingfunc'), 'product_review_setting_section', 'section');

		add_settings_field('likes_queston', 'Likes Dislike', array($this,'likes_settingfunc'), 'product_review_setting_section', 'section');

		add_settings_field('myaccount_perpage_setting', 'Question on My Account Page', array($this,'myaccountquestion_function'), 'product_review_setting_section', 'section');

		add_settings_field('myaccountans_perpage_setting', 'Answers on My Account Page', array($this,'myaccountanswers_function'), 'product_review_setting_section', 'section');

		add_settings_field('comment_approv', 'Comments Approved/Unapproved', array($this,'commentapprove_faqfunction'), 'product_review_setting_section', 'section');

		add_settings_field('extendons_myaccount_blocktitle', 'My Account Title', array($this,'myaccountblock_title'), 'product_review_setting_section', 'section');

		add_settings_field('extendons_myaccount_blocktab', 'My Account Tab Tilte', array($this,'myaccounttab_title'), 'product_review_setting_section', 'section');



		add_settings_field('email_sender_setting', 'Sender Email', array($this,'senderemail_faqfunction'), 'emial_product_review_setting_section', 'esection');

		add_settings_field('notificationclient_email', 'Notification for Client', array($this,'clientnotification_faqfunction'), 'emial_product_review_setting_section', 'esection');

		add_settings_field('emailclient_subject', 'Client Email Subject', array($this,'emailsubject_faqfunction'), 'emial_product_review_setting_section', 'esection');

		add_settings_field('email_content_publish', 'Write Email Content On Question Publish', array($this,'emailcontent_faqfunction'), 'emial_product_review_setting_section', 'esection');

		// add_settings_field('comment_content_publish', 'Write Email Content On Comment Post.', array($this,'emailcontentcomment_faqfunction'), 'emial_product_review_setting_section', 'esection');


		add_settings_field('your_secret_key', 'Your recaptcha Secret Key', array($this,'secretkey_faqfunction'), 'google_product_review_setting_section', 'gsection');

		add_settings_field('your_site_key', 'Your recaptcha Site Key', array($this,'sitekey_faqfunction'), 'google_product_review_setting_section', 'gsection');
		
		register_setting('section', 'page_title_setting');
		register_setting('section', 'post_perpage_setting');
		register_setting('section', 'qustiondisabele_enabled');
		register_setting('section', 'ask_queston');
		register_setting('section', 'likes_queston');
		register_setting('section', 'myaccount_perpage_setting');
		register_setting('section', 'myaccountans_perpage_setting');
		register_setting('section', 'comment_approv');

		register_setting('section', 'extendons_myaccount_blocktitle');
		register_setting('section', 'extendons_myaccount_blocktab');
		register_setting('section', 'extendons_singlepage_tab');

		register_setting('esection', 'email_sender_setting');
		register_setting('esection', 'notificationclient_email');
		register_setting('esection', 'emailclient_subject');
		register_setting('esection', 'email_content_publish');
	    // register_setting('esection', 'comment_content_publish');

		register_setting('gsection', 'your_secret_key');
		register_setting('gsection', 'your_site_key');
	}


	//module constant 
	function module_constant() {

		if ( !defined( 'product_question_url' ) )
			define( 'product_question_url', plugin_dir_url( __FILE__ ) );

		if ( !defined( 'product_question_basename' ) )
			define( 'product_question_basename', plugin_basename( __FILE__ ) );

		if ( ! defined( 'product_question_plguin_dir' ) )
			define( 'product_question_plguin_dir', plugin_dir_path( __FILE__ ) );
	}
	

	//enqueue the scripts and style
	function extendons_scripts_style_textdomain_init() { 

		wp_enqueue_script('jquery');

		wp_enqueue_script('jquery-ui-tabs');

		wp_enqueue_script('parsley-js', plugins_url( 'Scripts/parsley.min.js', __FILE__ ), false ); 
		wp_enqueue_script('ajax2.min-js', plugins_url( 'Scripts/ajax2.min.js', __FILE__ ), false );
		wp_enqueue_script('ajax.min-js', plugins_url( 'Scripts/ajax2.min.js', __FILE__ ), false );


		wp_enqueue_style('parsley-css', plugins_url( 'Styles/parsley.css', __FILE__ ), false );

		//wp_enqueue_style('bootstrap-css', plugins_url( 'Styles/bootstrap.css', __FILE__ ), false );

		wp_enqueue_script('modilate-js', plugins_url( 'Scripts/modalite.min.js', __FILE__ ), false );

		wp_enqueue_style('modilate-css', plugins_url( 'Styles/modalite.min.css', __FILE__ ), false );

		wp_enqueue_style( 'select2', plugins_url( 'Styles/select2.min.css', __FILE__ ), false );
		
		wp_enqueue_style( 'font-awesome', plugins_url( 'Styles/font-awesome.min.css', __FILE__ ), false );
		wp_enqueue_style( 'backend-style', plugins_url( 'Styles/back-style.css', __FILE__ ), false );
		wp_enqueue_script( 'select2', plugins_url( 'Scripts/select2.min.js', __FILE__ ), false );
		
		if ( function_exists( 'load_plugin_textdomain' ) )
			load_plugin_textdomain( 'extendons_faq_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	} 	

}
new EXTENDONS_FAQ_MAIN_CLASS();