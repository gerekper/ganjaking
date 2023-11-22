<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-reviwer/classes
 * @version     0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_ra_admin{
	
	public $optRS;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
		include_once('evo-review.php');
		include_once('evo-review_meta_boxes.php');

		add_filter( 'eventon_appearance_add', array($this, 'evoRE_appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'evoRE_dynamic_styles') , 10, 1);

		add_action( 'admin_menu', array( $this, 'menu' ),10);

		// delete review
		add_action('wp_trash_post',array($this,'trash_review'),1,1);

		// change review from draft to publish
		add_action('transition_post_status',array($this,'change_review_status'), 10, 3);
	}

	// INITIATE
		function admin_init(){

			// settings
			add_filter('eventon_settings_tabs',array($this, 'evoRE_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evcal_re',array($this, 'evoRE_tab_content' ));	

			// icon
			add_filter( 'eventon_custom_icons',array($this, 'evoRE_custom_icons') , 10, 1);

			// eventCard inclusion
			add_filter( 'eventon_eventcard_boxes',array($this,'evoRE_add_toeventcard_order') , 10, 1);

			// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'evoRE_language_additions'), 10, 1);

			global $pagenow, $typenow, $wpdb, $post;	
			
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) && $post ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $typenow = get_post_type( $_GET['post'] );
		    }
			
			if ( $typenow == '' || $typenow == "ajde_events" || $typenow =='evo-review') {
				// Event Post Only
				$print_css_on = array( 'post-new.php', 'post.php' );
				foreach ( $print_css_on as $page ){
					add_action( 'admin_print_styles-'. $page, array($this,'evoRE_event_post_styles' ));		
				}
			}

			if($pagenow == 'edit.php' && $typenow == 'evo-review'){
				add_action( 'admin_print_styles-edit.php', array($this, 'evoRE_event_post_styles' ));	
			}

				
		}

	// other hooks
		function evoRE_event_post_styles(){
			global $eventon_re;
			wp_enqueue_style( 'evore_admin_post',$eventon_re->plugin_url.'/assets/admin_evore_post.css');
			wp_enqueue_script( 'evore_admin_post_script',$eventon_re->plugin_url.'/assets/RE_admin_script.js',array(), $eventon_re->version);
			wp_localize_script( 
				'evore_admin_post_script', 
				'evore_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonre_nonce' )
				)
			);
		}
		function evoRE_add_toeventcard_order($array){
			$array['evore']= array('evore',__('Event Review Box','eventon'));
			return $array;
		}

		function evoRE_custom_icons($array){
			$array[] = array('id'=>'evcal__evore_001','type'=>'icon','name'=>'Event Review Icon','default'=>'fa-star');
			return $array;
		}
		
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Reviewer', __('Reviewer','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_re', '' );
		}
	// appearance
		function evoRE_appearance_settings($array){
			
			$new[] = array('id'=>'evore','type'=>'hiddensection_open','name'=>'Reviewer Styles','display'=>'none');
			$new[] = array('id'=>'evore','type'=>'fontation','name'=>'Overall Rating Section',
				'variations'=>array(
					array('id'=>'evoRE_2', 'name'=>'Stars Color','type'=>'color', 'default'=>'6B6B6B'),
					array('id'=>'evoRE_3', 'name'=>'Ratings Count Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRE_4', 'name'=>'Ratings Count Background Color','type'=>'color', 'default'=>'6B6B6B'),
					array('id'=>'evoRE_5', 'name'=>'Data Button Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRE_6', 'name'=>'Data Button Background Color','type'=>'color', 'default'=>'6B6B6B'),
				)
			);
			$new[] = array('id'=>'evore','type'=>'fontation','name'=>'Rating Data Section',
				'variations'=>array(
					array('id'=>'evoRE_d1', 'name'=>'Star Color','type'=>'color', 'default'=>'656565'),
					array('id'=>'evoRE_d2', 'name'=>'Percentage Bar Color','type'=>'color', 'default'=>'4DA5E2'),
					array('id'=>'evoRE_d3', 'name'=>'Rating Count Color','type'=>'color', 'default'=>'656565'),		
				)
			);$new[] = array('id'=>'evore','type'=>'fontation','name'=>'Review Section',
				'variations'=>array(
					array('id'=>'evoRE_R', 'name'=>'Star Color','type'=>'color', 'default'=>'656565'),
					array('id'=>'evoRE_R2', 'name'=>'Review Background Color','type'=>'color', 'default'=>'DEDEDE'),
					array('id'=>'evoRE_R3', 'name'=>'Review Font Color','type'=>'color', 'default'=>'656565'),
					array('id'=>'evoRE_R4', 'name'=>'Reviewer Font Color','type'=>'color', 'default'=>'656565'),
					array('id'=>'evoRE_R5', 'name'=>'Review Arrow Color','type'=>'color', 'default'=>'141412'),
				)
			);$new[] = array('id'=>'evore','type'=>'fontation','name'=>'Review Lightbox Form',
				'variations'=>array(
					array('id'=>'evoRE_L_1', 'name'=>'Background Color','type'=>'color', 'default'=>'99c379'),
					array('id'=>'evoRE_L_2', 'name'=>'Header Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRE_L_3', 'name'=>'Button Background Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRE_L_4', 'name'=>'Button Text Color','type'=>'color', 'default'=>'9ab37f'),						
				)
			);
			
			$new[] = array('id'=>'evore','type'=>'hiddensection_close',);

			return array_merge($array, $new);
		}

		function evoRE_dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.evcal_evdata_cell h3.orating .orating_stars',
					'css'=>'color:#$', 'var'=>'evoRE_2',	'default'=>'6B6B6B'
				),
				array(
					'item'=>'.evcal_evdata_cell h3.orating .orating_data',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRE_4',	'default'=>'6B6B6B'),
						array('css'=>'color:#$', 'var'=>'evoRE_3',	'default'=>'ffffff'),
					)
				),
				array(
					'item'=>'.evcal_evdata_cell h3.orating .extra_data',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRE_6',	'default'=>'6B6B6B'),
						array('css'=>'color:#$', 'var'=>'evoRE_5',	'default'=>'ffffff'),
					)
				),
				array(
					'item'=>'.evcal_evdata_cell .rating_data .rating',
					'css'=>'color:#$', 'var'=>'evoRE_d1',	'default'=>'656565'
				),array(
					'item'=>'.evcal_evdata_cell .rating_data .bar em',
					'css'=>'background-color:#$', 'var'=>'evoRE_d2',	'default'=>'4DA5E2'
				),array(
					'item'=>'.evcal_evdata_cell .rating_data .count',
					'css'=>'color:#$', 'var'=>'evoRE_d3',	'default'=>'656565'
				),

				array(
					'item'=>'.evcal_evdata_cell .review_list .review.show .rating',
					'css'=>'color:#$', 'var'=>'evoRE_R',	'default'=>'656565'
				),array(
					'item'=>'.evcal_evdata_cell .review_list .review.show .description',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRE_R2',	'default'=>'DEDEDE'),
						array('css'=>'color:#$', 'var'=>'evoRE_R3',	'default'=>'656565'),
					)
				),array(
					'item'=>'.evcal_evdata_cell .review_list .reviewer',
					'css'=>'color:#$', 'var'=>'evoRE_R4',	'default'=>'656565'
				),array(
					'item'=>'.evcal_evdata_cell .review_list_control span',
					'css'=>'color:#$', 'var'=>'evoRE_R4',	'default'=>'141412'
				)
				
				,array(
					'item'=>'.evore_form_section',
					'css'=>'background-color:#$', 'var'=>'evoRE_L_1',	'default'=>'9AB37F'
				),array(
					'item'=>'.evore_form_section, #evore_form h3',
					'css'=>'color:#$', 'var'=>'evoRE_L_2',	'default'=>'ffffff'
				),array(
					'item'=>'#submit_review_form',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRE_L_3',	'default'=>'ffffff'),
						array('css'=>'color:#$', 'var'=>'evoRE_L_4',	'default'=>'9ab37f'),
					)
				)
				
			);		

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
	// language settings additinos
		function evoRE_language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Event Reviewer'),
					array('label'=>'Event Reviews','var'=>'1'),
					array('label'=>'Overall Rating:','var'=>'1'),
					array('label'=>'Ratings','var'=>'1'),
					array('label'=>'Data','var'=>'1'),
					array('label'=>'There are no reviews for this event','var'=>'1'),
					array('label'=>'Write a Review','var'=>'1'),

					array('label'=>'Frontend Form Fields','type'=>'subheader'),
						array('label'=>'Write a review for [event-name]','name'=>'evoREL_x8'),
						array('label'=>'Your Name','name'=>'evoREL_x9'),
						array('label'=>'Your Email Address','name'=>'evoREL_x10'),
						array('label'=>'Event Review Text','name'=>'evoREL_x11'),
						array('label'=>'Verify you are a human:','name'=>'evoREL_x12'),
						array('label'=>'Terms & Conditions','name'=>'evoREL_x12a'),
						array('label'=>'Submit','name'=>'evoREL_x13'),
					array('type'=>'togend'),

					array('label'=>'Form Messages','type'=>'subheader'),
						array('label'=>'Required fields missing','var'=>'1'),
						array('label'=>'Invalid nonce, try again later!','var'=>'1'),
						array('label'=>'Invalid email address','var'=>'1'),
						array('label'=>'Invalid Validation code.','var'=>'1'),
						array('label'=>'Could not save review please try later.','var'=>'1'),
						array('label'=>'You can only submit once for this event.','var'=>'1'),
						array('label'=>'Thank you for submitting your review','var'=>'1'),
					array('type'=>'togend'),
					array('type'=>'subheader','label'=>'EMAIL Body'),
						array('label'=>'Rating','name'=>'evoRE_e_001'),
						array('label'=>'Reviewer','name'=>'evoRE_e_002'),
						array('label'=>'Email Address','name'=>'evoRE_e_003'),
						array('label'=>'Review','name'=>'evoRE_e_004'),

						array('label'=>'Spaces','name'=>'evoRSLX_003','legend'=>'','placeholder'=>''),
						array('label'=>'Receive Updates','name'=>'evoRSLX_003a'),
						array('label'=>'Location','name'=>'evoRSLX_003x',),
						array('label'=>'Thank you for RSVPing to our event','name'=>'evoRSLX_004','legend'=>'','placeholder'=>''),
						array('label'=>'We look forward to seeing you!','name'=>'evoRSLX_005','legend'=>'','placeholder'=>'We look forward to seeing you!'),
						array('label'=>'Contact us for quesitons and concerns.',
							'name'=>'evoRSLX_006','legend'=>'','placeholder'=>''),
						array('label'=>'New RSVP for event.',
							'name'=>'evoRSLX_007','legend'=>'','placeholder'=>''),
						array('label'=>'Event Time.','name'=>'evoRSLX_008','legend'=>'','placeholder'=>''),
						array('label'=>'Confirmation email subheader.','name'=>'evoRSLX_009','legend'=>'','placeholder'=>'You have RSVP-ed for'),
						array('label'=>'Notification email subheader.','name'=>'evoRSLX_010','legend'=>'','placeholder'=>'You have received a new RSVP for'),
					array('type'=>'togend'),				
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}
	
	// TABS SETTINGS
		function evoRE_tab_array($evcal_tabs){
			$evcal_tabs['evcal_re']='Reviewer';		
			return $evcal_tabs;
		}
		function evoRE_tab_content(){
			EVO()->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evore_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_re" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php

					$site_name = get_bloginfo('name');
					$site_email = get_bloginfo('admin_email');

					$cutomization_pg_array = array(
						array(
							'id'=>'evoRE1','display'=>'show',
							'name'=>'General Reviewer Settings',
							'tab_name'=>'General',
							'fields'=>array(
								array('id'=>'evore_prefil','type'=>'yesno','name'=>'Pre-fill fields  if user is already logged-in (eg. name, email)','legend'=>'If this option is activated, form will pre-fill fields (name & email) for logged-in users.'),
								array('id'=>'evore_draft','type'=>'yesno','name'=>'Require review approval before publishing'),	
								array('id'=>'evore_only_logged','type'=>'yesno','name'=>'Allow only logged-in users to submit reviews'),	

						)),array(
							'id'=>'evoRE2','display'=>'',
							'name'=>'Email Settings',
							'tab_name'=>'Emails','icon'=>'envelope',
							'fields'=>array(
								array('id'=>'evore_notif','type'=>'yesno','name'=>'Receive email notifications upon new Review submission','afterstatement'=>'evore_notif'),
								array('id'=>'evore_notif','type'=>'begin_afterstatement'),	

									array('id'=>'evore_notfiemailfromN','type'=>'text','name'=>'"From" Name','default'=>$site_name),
									array('id'=>'evore_notfiemailfrom','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
									array('id'=>'evore_notfiemailto','type'=>'text','name'=>'"To" Email Address (You can set multiple email addresses separated by commas)' ,'default'=>$site_email),

									array('id'=>'evore_notfiesubjest','type'=>'text','name'=>'Email Subject line','default'=>'New Review Notification'),
									array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
									array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email template copy "eventon-rsvp/templates/review_notification_email.php" to  "yourtheme/eventon/templates/email/reviewer/notification_email.php.'),
								array('id'=>'evore_notif','type'=>'end_afterstatement'),								

						)),array(
							'id'=>'evoRE3','display'=>'',
							'name'=>'Review form fields',
							'tab_name'=>'Review Form','icon'=>'inbox',
							'fields'=>array(
								array('id'=>'evore_fields', 'type'=>'rearrange',
									'fields_array'=>$this->fields_array(),
									'order_var'=> 'evore_fieldorder',
									'selected_var'=> 'evore_fields',
									'title'=>__('Fields for the review form','eventon'),								
								),	
								array('id'=>'evore_termscond_text','type'=>'text','name'=>'Terms & Conditions URL','legend'=>'Terms & Conditions link text can be changed via language > Event Review'),
								array('id'=>'evore_email_req','type'=>'yesno','name'=>'Require email address for review submission'),
								array('id'=>'evore_review_req','type'=>'yesno','name'=>'Require review text for review submission'),
						))
					);							
					EVO()->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_re'); 

					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
				<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
			</div>			
			</form>	
		<?php
		}

		function fields_array(){
			return array(
				'name'=>__('Name','eventon'),
				'review'=>__('Review Text','eventon'),
				'validation'=>__('Human Validation Field','eventon'),
				'terms'=>__('Terms & Conditions','eventon'),
			);
		}
		function _custom_field_types(){
			return array('text'=>'Single Line Input Text Field', 'dropdown'=>'Drop Down Options', 'html'=>'Basic Text Line');
		}
		function __evore_settings_part_preview_email(){
			ob_start();
			echo "<a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_re&action=confirmation#evoRe2' class='evo_admin_btn btn_triad'>Confirmation Email</a> <a href='".get_admin_url()."admin.php?page=eventon&tab=evcal_re&action=notification#evoRe2' class='evo_admin_btn btn_triad'>Notification Email</a>";
			
			if(!empty($_GET['action'])){
				echo $this->get_email_preview($_GET['action']);
			}
			return ob_get_clean();
		}
		// preview of emails that are sent out
		function get_email_preview( $type){
			global $eventon_re;

			$email = $eventon_re->frontend->get_email_data(array(
					'e_id'=>'934',
					'email'=>'test@msn.com',
					'review_id'=>'100'
				),$type
			);		
			$email['preview']= 'yes';	
			return $eventon_re->helper->send_email($email);
		}
	// trash review
		public function trash_review($post_id){
			if ( 'evo-review' != get_post_type( $post_id ))
       			return;

       		global $eventon_re;
       		$PMV = get_post_custom($post_id);
       		
       		// sync count
	       	if(!empty($PMV['e_id']))
	       		$eventon_re->frontend->functions->sync_ratings($PMV['e_id'][0]);
		}
	// change review post status to publish
		function change_review_status($new_status, $old_status, $post){
			if($post->post_type !== 'evo-review') return;

			if( 
				(('draft'=== $old_status || 'auto-draft'===$old_status) && $new_status === 'publish')||
				('publish'=== $old_status && $new_status === 'draft')
			){
				global $eventon_re;

				$rpmv = get_post_custom($post->ID);
				$rating = (!empty($rpmv['rating'])? $rpmv['rating'][0]:1);
				$ri = (!empty($rpmv['repeat_interval'])? $rpmv['repeat_interval'][0]:0);
				$e_id = (!empty($rpmv['e_id'])? $rpmv['e_id'][0]:0);

				// sync ratings for this review
				$eventon_re->frontend->functions->add_new_rating($rating, $e_id, $ri);
				$eventon_re->frontend->functions->sync_ratings($e_id, $ri);
			}
		}

}

new evo_ra_admin();