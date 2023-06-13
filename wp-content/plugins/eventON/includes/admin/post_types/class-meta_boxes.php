<?php
/**
 * Meta boxes for ajde_events
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/ajde_events
 * @version     4.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_event_metaboxes{
	public $EVENT = false;
	public $event_data = array();
	private $helper;

	public function __construct(){
		add_action( 'add_meta_boxes', array($this,'metabox_init') );
		add_action( 'save_post', array($this,'eventon_save_meta_data'), 1, 2 );
		//add_action( 'post_submitbox_misc_actions', array($this,'ajde_events_settings_per_post' ));

		add_filter('evo_eventedit_pageload_data',array($this, 'eventedit_pageload_data'), 10, 4);
		add_filter('evo_eventedit_pageload_dom_ids', array($this, 'eventedit_domids'), 12,3);
	}

	// INIT meta boxes
		function metabox_init(){

			global $post;

			// get post type
			$postType = !empty($_GET['post_type'])? sanitize_text_field($_GET['post_type']): false;	   
	   		if(!$postType && !empty($_GET['post']))   	$postType = get_post_type( sanitize_text_field($_GET['post']));

	   		if( !$postType) return false;
	   		if( $postType != 'ajde_events' ) return false;

	   		// Custom editor // 2.8.5
	   		wp_enqueue_style('evo_wyg_editor');
	   		wp_enqueue_script('evo_wyg_editor');
			
			// initiate a event object
	   		$this->EVENT = $this->EVENT ? $this->EVENT: new EVO_Event($post->ID);
	   		$this->event_data = $this->EVENT->get_data();

	   		$GLOBALS['EVO_Event'] = $this->EVENT;

			$evcal_opt1= get_option('evcal_options_evcal_1');

			// ajde_events meta boxes
			add_meta_box('ajdeevcal_mb1', __('Main Event Details','eventon'), array($this,'ajde_evcal_show_box'),'ajde_events', 'normal', 'high');

			add_meta_box('ajdeevcal_mb1_cmf', __('Event Custom Meta Fields','eventon'), array($this,'ajde_evcal_show_box_cmf'),'ajde_events', 'normal', 'high');	

			add_meta_box('ajdeevcal_mb3jd',__('Event Options','eventon'), 
				array($this,'meta_box_event_options'),'ajde_events', 'side', 'low');
			
			add_meta_box('ajdeevcal_mb2',__('Event Colors','eventon'), 
				array($this,'meta_box_event_color'),'ajde_events', 'side', 'core');
			
			add_meta_box('ajdeevcal_mb_ei',__('Event Extra Images','eventon'), 
				array($this,'metabox_event_extra_images'),'ajde_events', 'side', 'low');
			
			
			// if third party is enabled
			if( EVO()->cal->check_yn('evcal_paypal_pay','evcal_1')){
				add_meta_box('ajdeevcal_mb3',__('Third Party Settings','eventon'), array($this,'ajde_evcal_show_box_3'),'ajde_events', 'normal', 'high');
			}


			// @updated 2.6.7 to pass event object
			do_action('eventon_add_meta_boxes', $this->EVENT);
		}

	// event edit ajax load
		function eventedit_domids($array){
			$array['evo']= 'evo_pageload_data';
			$array['evo_color']= 'evo_mb_color';
			return $array;
		}
		function eventedit_pageload_data($array, $postdata, $EVENT, $id){

			if( $id && $id != 'evo') return $array;
			
			ob_start();
			include_once 'class-meta_box_all.php';
			$items = ob_get_clean();

			$array['evo'] = $items;

			ob_start();
			include_once 'class-meta_boxes-color.php';
			$items_color = ob_get_clean();

			$array['evo_color'] = $items_color;
			return $array;
		}

	// extra event images
		function metabox_event_extra_images(){
			include_once 'class-meta_boxes-extraimages.php';			
		}

	// EXTRA event settings for the page
		function meta_box_event_options(){
			// Global Event Props will be set initially right here
				$event = $this->EVENT;
			?>	<div class='evo_event_opts evo_edit_field_box'>		<?php

			
			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'id'=>'evo_exclude_ev', 
						'type'=>'yesno_btn',
						'value'=> $event->get_prop('evo_exclude_ev'),
						'input'=>true,
						'label'=>__('Exclude from calendar','eventon'),
						'tooltip'=>__('Set this to Yes to hide event from showing in all calendars','eventon'),
						'tooltip_position'=>'L'
					),
					array(
						'id'=>'_featured', 'type'=>'yesno_btn',
						'value'=> $event->get_prop('_featured'),
						'input'=>true,
						'label'=>__('Featured Event','eventon'),
						'tooltip'=>__('Make this event a featured event','eventon'),
						'tooltip_position'=>'L'
					),
					array(
						'id'=>'_completed', 'type'=>'yesno_btn',
						'value'=> $event->get_prop('_completed'),
						'input'=>true,
						'label'=>__('Event Completed','eventon'),
						'tooltip'=>__('Mark this event as completed','eventon'),
						'tooltip_position'=>'L'
					),
					array(
						'id'=>'_onlyloggedin', 'type'=>'yesno_btn',
						'value'=> $event->get_prop('_onlyloggedin'),
						'input'=>true,
						'label'=>__('Loggedin Users Only','eventon'),
						'tooltip'=>__('This will make this event only visible if the users are loggedin to this site','eventon'),
						'tooltip_position'=>'L',
					)
				)
			);

			// export event data as CSV file
				$exportURL = add_query_arg(array(
				    'action' => 'eventon_export_events',
				    'eid'	=> $event->ID,
				    'nonce'=> wp_create_nonce('eventon_download_events')
				), admin_url('admin-ajax.php'));

			echo "<p><a class='evo_btn' href='{$exportURL}'>" . __('Download CSV') .'</a>'. EVO()->elements->tooltips( __('Download a CSV file format of event data from this event.','eventon'), 'L') .'</p>';
				// @since 2.2.28
				do_action('eventon_event_submitbox_misc_actions',$event);
			?>
		</div>
		<?php
		}
	
	// Event Color Meta Box	
		function meta_box_event_color(){
			?>
			<div id='evo_mb_color'>
				<div class='evo_loading_bar_holder h100'>
					<div class="evo_loading_bar hi_50"></div>
					<div class="evo_loading_bar hi_50"></div>
				</div>
			</div>
			<?php
		}

	// MAIN META BOX CONTENT
		function ajde_evcal_show_box(){
			$p_id = get_the_ID();
			$closedmeta = eventon_get_collapse_metaboxes($p_id);
			$closedmeta = is_array($closedmeta)? implode(',', $closedmeta):'';
		?>	
			
			<div id='evo_mb' class='eventon_mb'>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename' ); ?>
				<input type='hidden' id='evo_collapse_meta_boxes' name='evo_collapse_meta_boxes' value='<?php echo $closedmeta;?>'/>
				<input type='hidden' id='evo_event_id' name='' value='<?php echo $p_id;?>'/>
				
				<div id='evo_pageload_data'>
					<div class='evo_loading_bar_holder h100'>
						<div class="evo_loading_bar wid_40 hi_50"></div>
						<div class="evo_loading_bar hi_50"></div>
					</div>
				</div>
			</div>
		<?php  
		}

		// for custom meta boxes
		function ajde_evcal_show_box_cmf(){
			?>
			<div id='evo_mb' class='eventon_mb'>
				<?php include_once( 'class-meta_boxes-cmf.php');?>
			</div>
			<?php
		}

	// THIRD PARTY event related settings 
		function ajde_evcal_show_box_3(){	
			
			
			$evcal_opt1= get_option('evcal_options_evcal_1');
				$evcal_opt2= get_option('evcal_options_evcal_2');
				
				// Use nonce for verification
				//wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename_mb3' );
				
				// The actual fields for data entry
				$ev_vals = $this->event_data;
			
			?>
			<table id="meta_tb" class="form-table meta_tb evoThirdparty_meta" >
				<?php
					// (---) hook for addons
					if(has_action('eventon_post_settings_metabox_table'))
						do_action('eventon_post_settings_metabox_table');
				
					if(has_action('eventon_post_time_settings'))
						do_action('eventon_post_time_settings');

				// PAYPAL
					if($evcal_opt1['evcal_paypal_pay']=='yes'):
					?>
					<tr>
						<td colspan='2' class='evo_thirdparty_table_td'>
							<div class='evo3rdp_header'>
								<span class='evo3rdp_icon'><i class='fa fa-paypal'></i></span>
								<p><?php _e('Paypal "BUY NOW" button','eventon');?></p>
							</div>	
							<div class='evo_3rdp_inside'>
								<p class='evo_thirdparty'>
									<label for='evcal_paypal_text'><?php _e('Text to show above buy now button','eventon')?></label><br/>			
									<input type='text' id='evcal_paypal_text' name='evcal_paypal_text' value='<?php echo (!empty($ev_vals["evcal_paypal_text"]) )? $ev_vals["evcal_paypal_text"][0]:null?>' style='width:100%'/>
								</p>
								<p class='evo_thirdparty'><label for='evcal_paypal_item_price'><?php _e('Enter the price for paypal buy now button <i>eg. 23.99</i> (WITHOUT currency symbol)')?><?php EVO()->elements->tooltips(__('Type the price without currency symbol to create a buy now button for this event. This will show on front-end calendar for this event','eventon'),'',true);?></label><br/>			
									<input placeholder='eg. 29.99' type='text' id='evcal_paypal_item_price' name='evcal_paypal_item_price' value='<?php echo (!empty($ev_vals["evcal_paypal_item_price"]) )? $ev_vals["evcal_paypal_item_price"][0]:null?>' style='width:100%'/>
								</p>
								<p class='evo_thirdparty'>
									<label for='evcal_paypal_email'><?php _e('Custom Email address to receive payments','eventon')?><?php EVO()->elements->tooltips('This email address will override the email saved under eventON settings for paypal to accept payments to this email instead of paypal email saved in eventon settings.','',true);?></label><br/>			
									<input type='text' id='evcal_paypal_email' name='evcal_paypal_email' value='<?php echo (!empty($ev_vals["evcal_paypal_email"]) )? $ev_vals["evcal_paypal_email"][0]:null?>' style='width:100%'/>
								</p>
							</div>		
						</td>			
					</tr>
					<?php endif; ?>
				</table>
			<?php
		}
		
	// Save the Event data meta box
		function eventon_save_meta_data($post_id, $post){
			if($post->post_type!='ajde_events')
				return;
				
			// Stop WP from clearing custom fields on autosave
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;

			// Prevent quick edit from clearing custom fields
			if (defined('DOING_AJAX') && DOING_AJAX)
				return;

			
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if( isset($_POST['evo_noncename']) ){
				if ( !wp_verify_nonce( $_POST['evo_noncename'], plugin_basename( __FILE__ ) ) ){
					return;
				}
			}
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;	

			global $pagenow;
			$_allowed = array( 'post-new.php', 'post.php' );
			if(!in_array($pagenow, $_allowed)) return;

			$this->EVENT = $EVENT = new EVO_Event($post_id);

			$HELP = new evo_helper();
			$post_data = $HELP->recursive_sanitize_array_fields( $_POST );
						
			// $_POST FIELDS array
				$fields_ar =apply_filters('eventon_event_metafields', array(
					'evcal_allday','evcal_event_color','evcal_event_color_n',
					'evcal_exlink','evcal_lmlink','evcal_subtitle',
					'evcal_hide_locname','evcal_gmap_gen','evcal_name_over_img', 'evo_access_control_location',
					'evcal_mu_id','evcal_paypal_item_price','evcal_paypal_text','evcal_paypal_email',
					'evcal_repeat','_evcal_rep_series','_evcal_rep_endt','_evcal_rep_series_clickable','evcal_rep_freq','evcal_rep_gap','evcal_rep_num',
					'evp_repeat_rb','evo_repeat_wom','evo_rep_WK','evp_repeat_rb_wk','evo_rep_WKwk',
					'evcal_lmlink_target','_evcal_exlink_target','_evcal_exlink_option',
					'evo_hide_endtime','evo_span_hidden_end','evo_year_long','_evo_month_long',
					'evo_evcrd_field_org','evo_event_org_as_perf','evo_event_timezone',
					'_evo_virtual_endtime',

					'evo_exclude_ev',				
					'ev_releated',				
				), $post_id);

			// append custom fields based on activated number
				$evcal_opt1= get_option('evcal_options_evcal_1');
				$num = evo_calculate_cmd_count($evcal_opt1);
				for($x =1; $x<=$num; $x++){	
					if(eventon_is_custom_meta_field_good($x)){
						$fields_ar[]= '_evcal_ec_f'.$x.'a1_cus';
						$fields_ar[]= '_evcal_ec_f'.$x.'a1_cusL';
						$fields_ar[]= '_evcal_ec_f'.$x.'_onw';
					}
				}

			// fields allowed to pass HTML
				$fields_with_html = apply_filters('evo_event_metafields_htmlcontent',
					array('evcal_subtitle')
				);

			// array of post meta fields that should be deleted from event post meta
				foreach(array(
					'evo_location_tax_id','evo_organizer_tax_id','_cancel'
				) as $ff){
					delete_post_meta($post_id, $ff);
				}

			// Backward compatible cancel event u4.2.3
				if(!isset($post_data['_status']) && isset($post_data['_cancel']) && $post_data['_cancel'] == 'yes'){
					$post_data['_status'] = 'cancelled';
				}

			// Add _ event meta values
				foreach($post_data as $F=>$V){
					if(substr($F, 0,1) === '_'){
						$fields_ar[] = $F;
					}
				}

			// remove duplicate field keys
				$fields_ar = array_unique($fields_ar);

			$proper_time = 	evoadmin_get_unix_time_fromt_post($post_id);

			// if Repeating event save repeating intervals
				if( eventon_is_good_repeat_data()  ){

					if(!empty($proper_time['unix_start'])){

						$unix_E = $end_range = (!empty($proper_time['unix_end']))? $proper_time['unix_end']: $proper_time['unix_start'];
						$repeat_intervals = eventon_get_repeat_intervals($proper_time['unix_start'], $unix_E);

						// save repeat interval array as post meta
						if ( !empty($repeat_intervals) ){

							$E = end($repeat_intervals);
							$end_range = $E[1];

							update_post_meta( $post_id, 'repeat_intervals', $repeat_intervals);
						}else{
							delete_post_meta( $post_id, 'repeat_intervals');
						}
					}
				}

			// save virtual end time
				if( isset($proper_time['unix_vir_end']) && !empty($proper_time['unix_vir_end'])){
					$EVENT->set_meta( '_evo_virtual_erow', $proper_time['unix_vir_end']);
				}

			// save previous start date for reschedule events
				if( isset($post_data['_status']) && $post_data['_status'] == 'rescheduled' && isset($post_data['event_prev_date_x'])
				){
					$date = $post_data['event_prev_date_x'];
				}

			// event images processing
				if(!empty($post_data['_evo_images'])){
					$imgs = explode(',', $post_data['_evo_images']);
					$imgs = array_filter($imgs); 
					$str = ''; $x = 1;
					foreach($imgs as $IM){		
						//if( $x > apply_filters('evo_event_images_max',3)) continue;				
						$str .= $IM .','; $x++;
					}
					update_post_meta( $post_id, '_evo_images',$str);
				}else{
					delete_post_meta($post_id, '_evo_images');
				}
				

			// run through all the custom meta fields
				foreach($fields_ar as $f_val){
					
					// make sure values are not empty at $_POST level
					if(!empty($_POST[$f_val])){

						$post_value = ( $post_data[$f_val]);

						// for fields with HTML content @since 4.3.3
						if( in_array($f_val, $fields_with_html)){

							$EVENT->set_prop($f_val, $HELP->sanitize_html( $_POST[ $f_val ] ));
							continue;
						}

						// for saving custom meta fields @since 4.3.3
						if( strpos($f_val, '_evcal_ec_f') !== false ){
							$post_value = $HELP->sanitize_html( $_POST[$f_val]);				
						}
						
						$EVENT->set_prop( $f_val , $post_value);

						// ux val for single events linking to event page	
						if($f_val=='evcal_exlink' && $post_data['_evcal_exlink_option']=='4'){
							$EVENT->set_prop( 'evcal_exlink' , get_permalink($post_id) );
						}

					}else{
						//if(defined('DOING_AUTOSAVE') && !DOING_AUTOSAVE){						
						delete_post_meta($post_id, $f_val);
					}					
				}

			// Save all event data values
				if( isset($post_data['_edata']) ){
					$this->EVENT->set_prop('_edata', $post_data['_edata']);
				}							
			
			// Other data	
				// full time converted to unix time stamp
					if ( !empty($proper_time['unix_start']) )
						update_post_meta( $post_id, 'evcal_srow', $proper_time['unix_start']);
					
					if ( !empty($proper_time['unix_end']) )
						update_post_meta( $post_id, 'evcal_erow', $proper_time['unix_end']);

				
						
				//set event color code to 1 for none select colors
					if ( !isset( $post_data['evcal_event_color_n'] ) )
						update_post_meta( $post_id, 'evcal_event_color_n',1);
									
				// save featured event data default value no
					$_featured = get_post_meta($post_id, '_featured',true);
					if(empty( $_featured) )
						update_post_meta( $post_id, '_featured','no');

				// language corresponding
					if(empty($post_data['_evo_lang']))
						update_post_meta( $post_id, '_evo_lang','L1');
			
						
			// (---) hook for addons
			do_action('eventon_save_meta', $fields_ar, $post_id, $this->EVENT, $post_data);

			// save user closed meta field boxes
			if(!empty($post_data['evo_collapse_meta_boxes']))
				eventon_save_collapse_metaboxes($post_id, $post_data['evo_collapse_meta_boxes'],true );
				
		}

	// Process metabox content
	// @since 4.2.3
		function process_content($array){
			$output = '';

			$visibility_types = array('all'=>__('Everyone','eventon'),'admin'=>__('Admin Only','eventon'),'loggedin'=>__('Loggedin Users Only','eventon'));

			ob_start();

			foreach($array as $mBOX):

				if( empty($mBOX['content'])) continue;

				$closed = isset($mBOX['close']) && $mBOX['close'] ? 'closed' : '';

				// initials
					$icon_style = (!empty($mBOX['iconURL']))?
						'background-image:url('.$mBOX['iconURL'].')'
						:'background-position:'.$mBOX['iconPOS'];
					$icon_class = (!empty($mBOX['iconPOS']))? 'evIcons':'evII';
					
					$guide = (!empty($mBOX['guide']))? 
						EVO()->elements->tooltips($mBOX['guide']):null;
					
					$hiddenVal = (!empty($mBOX['hiddenVal']))?
						'<span class="hiddenVal">'.$mBOX['hiddenVal'].'</span>':null;

					// visibility type ONLY for custom meta fields
						$visibility_type = (!empty($mBOX['visibility_type']))? "<span class='visibility_type'>".__('Visibility Type:','eventon').' '.$visibility_types[$mBOX['visibility_type']] .'</span>': false;
				
				?>
				<div class='evomb_section' id='<?php echo $mBOX['id'];?>'>			
					<div class='evomb_header <?php echo $closed;?>'>
						<?php // custom field with icons
							if(!empty($mBOX['variation']) && $mBOX['variation']	=='customfield'):?>	
							<span class='evomb_icon <?php echo $icon_class;?>'><i class='fa <?php echo $mBOX['iconURL']; ?>'></i></span>
							
						<?php else:	?>
							<span class='evomb_icon <?php echo $icon_class;?>' style='<?php echo $icon_style?>'></span>
						<?php endif; ?>
						<p><?php echo $mBOX['name'];?><?php echo $hiddenVal;?><?php echo $guide;?><?php echo $visibility_type;?></p>
					</div>
					<div class='evomb_body <?php echo $closed;?>' box_id='<?php echo $mBOX['id'];?>'>
						<?php	 echo $mBOX['content'];?>
					</div>
				</div>
			<?php 
			endforeach;

			return ob_get_clean();
		}
}
