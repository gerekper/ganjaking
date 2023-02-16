<?php
/**
 * 
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-rsvp/classes
 * @version     2.5.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evorsvp_admin{
	
	public $optRS;
	function __construct(){
		add_action('admin_init', array($this, '_admin_init'));
		include_once('class-admin-evo-rsvp.php');
		include_once('evo-rsvp_meta_boxes.php');

		add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this, 'evoRS_dynamic_styles') , 10, 1);
		add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
		add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);

		// eventtop
		//add_action('eventon_eventop_fields', array($this,'eventtop_option'), 10, 1);
		add_action( 'admin_menu', array( $this, 'menu' ),9);

		// delete rsvp
		add_action('wp_trash_post',array($this,'trash_rsvp'),1,1);
		add_action('publish_to_trash',array($this,'trash_rsvp'),1,1);
		add_action('draft_to_trash',array($this,'trash_rsvp'),1,1);
		//add_action('trash_post',array($this,'trash_rsvp'),1,1);

		// duplicating event
		add_action('eventon_duplicate_product',array($this,'duplicate_event'), 10, 2);
		add_action('eventon_duplicate_event_exclude_meta',array($this,'exclude_duplicate_fields'), 10, 1);

		// troubleshooting info
		add_filter('eventon_troubleshooter', array($this,'troubleshooting'), 10, 1);
	}

	// INITIATE
		function _admin_init(){

			// icon
			add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);

			// eventCard inclusion
			add_filter( 'eventon_eventcard_boxes',array($this,'add_toeventcard_order') , 10, 1);


			global $pagenow, $typenow, $wpdb, $post;	
			
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) && $post){
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $typenow = get_post_type( $_GET['post'] );
		    }
			
			if ( $typenow == '' || $typenow == "ajde_events" || $typenow =='evo-rsvp') {

				// Event Post Only
				$print_css_on = array( 'post-new.php', 'post.php' );

				foreach ( $print_css_on as $page ){
					add_action( 'admin_print_styles-'. $page, array($this,'evoRS_event_post_styles' ));		
				}
			}

			// include rsvp id in the search
			if($typenow =='' || $typenow == 'evo-rsvp'){
				// Filter the search page
				add_filter('pre_get_posts', array($this, 'evors_search_pre_get_posts'));		
			}

			if($pagenow == 'edit.php' && $typenow == 'evo-rsvp'){
				add_action( 'admin_print_styles-edit.php', array($this, 'evoRS_event_post_styles' ));	
			}

			
		}

	// other hooks
		function evors_search_pre_get_posts($query){
		    // Verify that we are on the search page that that this came from the event search form
		    if($query->query_vars['s'] != '' && is_search())
		    {
		        // If "s" is a positive integer, assume post id search and change the search variables
		        if(absint($query->query_vars['s']) ){
		            // Set the post id value
		            $query->set('p', $query->query_vars['s']);

		            // Reset the search value
		            $query->set('s', '');
		        }
		    }
		}		

		function evoRS_event_post_styles(){
			global $eventon_rs;
			wp_enqueue_style( 'evors_admin_post',$eventon_rs->assets_path.'admin_evors_post.css','',$eventon_rs->version);
			wp_enqueue_script( 'evors_admin_post_script',$eventon_rs->assets_path.'RS_admin_script.js',array(), $eventon_rs->version);
			wp_localize_script( 
				'evors_admin_post_script', 
				'evors_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( AJDE_EVCAL_BASENAME )
				)
			);

			do_action('evors_enqueue_admin_scripts');
		}
		function add_toeventcard_order($array){
			$array['evorsvp']= array('evorsvp',__('RSVP Event Box','evors'));
			return $array;
		}

		function custom_icons($array){
			$array[] = array('id'=>'evcal__evors_001','type'=>'icon','name'=>'RSVP Event Icon','default'=>'fa-envelope');
			return $array;
		}
		// event top option for RSVP
		function eventtop_option($array){
			$array['rsvp_options'] = __('RSVP Info (Remaing Spaces & eventtop RSVP)','evors');
			return $array;
		}
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'RSVP', __('RSVP','evors'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_rs', '' );
		}
	// appearance
		function appearance_settings($array){
			
			$new[] = array('id'=>'evors','type'=>'hiddensection_open','name'=>'RSVP Styles', 'display'=>'none');
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'RSVP Buttons',
				'variations'=>array(
					array('id'=>'evoRS_1', 'name'=>'Border Color','type'=>'color', 'default'=>'cdcdcd'),
					array('id'=>'evoRS_2', 'name'=>'Background Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRS_2t', 'name'=>'Text Color','type'=>'color', 'default'=>'808080'),
					array('id'=>'evoRS_3', 'name'=>'Background Color (Hover)','type'=>'color', 'default'=>'888888'),
					array('id'=>'evoRS_3t', 'name'=>'Text Color (Hover)','type'=>'color', 'default'=>'ffffff')	
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Count Number Circles',
				'variations'=>array(
					array('id'=>'evors_cn_1', 'name'=>'Font Color (Attending)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evors_cn_2', 'name'=>'Background Color (Attending)','type'=>'color', 'default'=>'adadad'),
					array('id'=>'evors_cn_3', 'name'=>'Font Color (Not Attending)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evors_cn_4', 'name'=>'Background Color (Not Attending)','type'=>'color', 'default'=>'adadad'),
					array('id'=>'evors_cn_5', 'name'=>'Font Color (Spots Remaining)','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evors_cn_6', 'name'=>'Background Color (Spots Remaining)','type'=>'color', 'default'=>'6dc56b'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'RSVP Form',
				'variations'=>array(
					array('id'=>'evoRS_4', 'name'=>'Background Color','type'=>'color', 'default'=>'6dc56b'),
					array('id'=>'evoRS_5', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRS_7', 'name'=>'Button Color','type'=>'color', 'default'=>'ffffff'),	
					array('id'=>'evoRS_8', 'name'=>'Button Text Color','type'=>'color', 'default'=>'6dc56b'),		
					array('id'=>'evoRS_8z', 'name'=>'Selected RSVP option button font color','type'=>'color', 'default'=>'6dc56b'),		
					array('id'=>'evoRS_8y', 'name'=>'Terms & conditions text color','type'=>'color', 'default'=>'ffffff'),		
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'RSVP Form Fields',
				'variations'=>array(
					array('id'=>'evoRS_ff', 'name'=>'Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRS_ff2', 'name'=>'Placeholder Text Color','type'=>'color', 'default'=>'d5e4c5'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'RSVP Form Submit Button',
				'variations'=>array(
					array('id'=>'evoRS_12', 'name'=>'Font Color','type'=>'color', 'default'=>'6dc56b'),
					array('id'=>'evoRS_12H', 'name'=>'Background Color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evors','type'=>'fontation','name'=>'Guest List',
				'variations'=>array(
					array('id'=>'evoRS_9', 'name'=>'Guest Buble Background Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoRS_10', 'name'=>'Guest Buble Font Color','type'=>'color', 'default'=>'6b6b6b'),
					array('id'=>'evoRS_11', 'name'=>'Section Background Color (Attending)','type'=>'color', 'default'=>'ececec'),						
					array('id'=>'evoRS_11a', 'name'=>'Section Background Color (Not Attending)','type'=>'color', 'default'=>'e0e0e0'),						
				)
			);

			$new = apply_filters('evors_appearance_settings', $new);

			
			$new[] = array('id'=>'evors','type'=>'hiddensection_close',);

			return array_merge($array, $new);
		}

		function evoRS_dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'#evorsvp_form #submit_rsvp_form',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoRS_12',	'default'=>'6dc56b'),
						array('css'=>'background-color:#$', 'var'=>'evoRS_12H',	'default'=>'ffffff'),
					)
				),
				array(
					'item'=>'.evcal_desc .evcal_desc3 .evors_eventtop_section_data .evors_eventtop_data.attending em',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evors_cn_1',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evors_cn_2',	'default'=>'adadad'),
					)
				),
				array(
					'item'=>'.evcal_desc .evcal_desc3 .evors_eventtop_section_data .evors_eventtop_data.notattending em',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evors_cn_3',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evors_cn_4',	'default'=>'adadad'),
					)
				),
				array(
					'item'=>'.evcal_desc .evcal_desc3 .evors_eventtop_section_data .evors_eventtop_data.remaining_count em, .evcal_evdata_row .evors_stat_data .remaining_count em',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evors_cn_5',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evors_cn_6',	'default'=>'6dc56b'),
					)
				),
				array(
					'item'=>'.evors_whos_coming span',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRS_9',	'default'=>'ffffff'),
						array('css'=>'color:#$', 'var'=>'evoRS_10',	'default'=>'6b6b6b'),						
					)
				),
				array('item'=>'.evcal_evdata_row .evors_section.evors_guests_list','css'=>'background-color:#$', 'var'=>'evoRS_11',	'default'=>'ececec'),
				array('item'=>'.evcal_evdata_row .evors_section.evors_guests_list.evors_notcoming_list','css'=>'background-color:#$', 'var'=>'evoRS_11a',	'default'=>'e0e0e0'),
				array(
					'item'=>'#evorsvp_form a.submit_rsvp_form',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoRS_7',	'default'=>'ffffff'),
						array('css'=>'color:#$', 'var'=>'evoRS_8',	'default'=>'6dc56b'),
					)
				),array(
					'item'=>'.evo_lightbox_body #evorsvp_form .rsvp_status span.set',
					'css'=>'color:#$', 'var'=>'evoRS_8z',	'default'=>'6dc56b'
				),array(
					'item'=>'#evorsvp_form p.terms a',
					'css'=>'color:#$', 'var'=>'evoRS_8y',	'default'=>'ffffff'
				),array(
					'item'=>'.evors_lightbox_body #evorsvp_form .form_row select, 
					.evors_lightbox_body #evorsvp_form .form_row input,
					.evors_incard_form #evorsvp_form .form_row input,
					#evorsvp_form .form_row textarea',
					'css'=>'color:#$', 'var'=>'evoRS_ff',	'default'=>'ffffff'
				),
				
				array('item'=>'
					.evors_lightbox_body #evorsvp_form .form_row input::placeholder, 
					.evors_incard_form #evorsvp_form .form_row input::placeholder,
					.evors_lightbox_body #evorsvp_form .form_row textarea::placeholder, 
					.evors_incard_form #evorsvp_form .form_row textarea::placeholder',
					'css'=>'color:#$', 'var'=>'evoRS_ff2',	'default'=>'88b077'),
				array('item'=>'.evors_lightbox_body #evorsvp_form .form_row input:-moz-input-placeholder,
					.evors_incard_form #evorsvp_form .form_row input:-moz-input-placeholder,
					.evors_lightbox_body #evorsvp_form .form_row textarea:-moz-input-placeholder,
					.evors_incard_form #evorsvp_form .form_row textarea:-moz-input-placeholder',
					'css'=>'color:#$', 'var'=>'evoRS_ff2',	'default'=>'88b077'),
				array('item'=>'.evors_lightbox_body #evorsvp_form .form_row input:-ms-input-placeholder,
					.evors_incard_form #evorsvp_form .form_row input:-ms-input-placeholder,
					.evors_lightbox_body #evorsvp_form .form_row textarea:-ms-input-placeholder,
					.evors_incard_form #evorsvp_form .form_row textarea:-ms-input-placeholder',
					'css'=>'color:#$', 'var'=>'evoRS_ff2',	'default'=>'88b077'),
				array(
					'item'=>'.evors_submission_form, .evors_lightbox_body #evorsvp_form h3',
					'css'=>'color:#$', 'var'=>'evoRS_5',	'default'=>'ffffff'
				),array(
					'item'=>'.evors_lightbox .evo_lightbox_body.evo_lightbox_body, .evors_incard_form',
					'css'=>'background-color:#$', 'var'=>'evoRS_4',	'default'=>'6dc56b'
				),array(
					'item'=>'.evoRS_status_option_selection span:hover, body .eventon_list_event .evcal_list_a .evors_eventtop_rsvp span:hover',
					'css'=>'background-color:#$', 'var'=>'evoRS_3',	'default'=>'ffffff'
				),array(
					'item'=>'.evoRS_status_option_selection span, 
						.evors_rsvped_status_user, 
						.evors_change_rsvp span.change',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoRS_2t','default'=>'808080'),
						array('css'=>'border-color:#$', 'var'=>'evoRS_1','default'=>'cdcdcd'),
						array('css'=>'background-color:#$', 'var'=>'evoRS_2','default'=>'ffffff')
					)	
				),array(
					'item'=>'.evoRS_status_option_selection span:hover, 
						.evoRS_status_option_selection span.set, 
						.evors_change_rsvp span.change:hover',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'evoRS_3t','default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'evoRS_3','default'=>'888888')
					)	
				),				
			);			

			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
		// Font families
		function primary_font($str){
			$str .= ',.evcal_evdata_row .evors_stat_data p em,
			.evors_submission_form, .evors_lightbox_body #evorsvp_form h3,
			.evcal_desc .evcal_desc3 .evors_eventtop_data em,
			.eventon_rsvp_rsvplist p em.event_data span a,
			.eventon_rsvp_rsvplist p span.rsvpstatus,
			.eventon_rsvp_rsvplist p a.update_rsvp';
			return $str;
		}
		function secondary_font($str){
			return $str.',.evors_change_rsvp span.change,
			.evo_popin .evcal_eventcard p.evors_whos_coming_title,
			.eventon_list_event .evcal_evdata_row p.evors_whos_coming_title';
		}

	
	// TABS SETTINGS
		
	
	// duplicate event
		function duplicate_event($new_event_id, $old_event){

			$RSVP = new EVORS_Event($new_event_id);

			$RSVP->sync_rsvp_count();
			delete_post_meta($new_event_id, 'ri_count_rs');// clear ri count
		}
		// exclude event meta fields from duplication
			function exclude_duplicate_fields($fields){
				$fields[] = 'evors_data';
				return $fields;
			}

	// trash rsvp
		public function trash_rsvp($post_id){
			if( empty($post_id)) return;
			
			$post = get_post($post_id);

			if ( 'evo-rsvp' != $post->post_type)	return;
			
       		$data = '';

       		$RR = new EVO_RSVP_CPT($post_id);
       		$PMV = $RR->pmv;

       		$data .= '2';

       		$event_id = !empty($PMV['e_id'])? $PMV['e_id'][0]: false;
       		$repeat_interval = !empty($PMV['repeat_interval'])? $PMV['repeat_interval'][0]:0;

       		if(empty($event_id) || !$event_id) return;

       		$RSVP_Event = new EVORS_Event($event_id, $repeat_interval);
       		
       		$rsvp_status = !empty($PMV['rsvp'])? $PMV['rsvp'][0]:0;
       		
       		// if the userid is present for this RSVP
       		if(!empty($PMV['userid']) && !empty($PMV['e_id'])){
	       		$RSVP_Event->trash_user_rsvp($PMV['userid'][0]);
	       	}

	       	// if repeating event - sync remainging repeat count
	       		if($repeat_interval){
	       			$RSVP_Event->adjust_ri_count(	$rsvp_status, 'reduce'	);
	       		}

	       	// sync count
	       	if($event_id){
	       		$data .= '1 '.$event_id;
	       		$RSVP_Event->sync_rsvp_count();
	       	}

	       	//update_post_meta(1,'aa',$data);
		}

	// troubleshooting
		function troubleshooting($array){
			$newarray['RSVP Addon'] = array(
				'RSVP is not showing on eventcard'=>'Once you have activated RSVP for an event go to <b>myEventON Settings > EventCard > Re-arrange event data boxes</b> and make sure RSVP Event Box is checked and positioned correct. You can also move it up and down to make sure its registered. <b>Save Changes</b> This should make the RSVP box show up on eventCard.'
			);
			return array_merge($array, $newarray);
		}

}

new evorsvp_admin();