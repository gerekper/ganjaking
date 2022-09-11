<?php
/**
 * Function ajax for backend
 * @version   2.6.15
 */
class EVO_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'deactivate_product'	=>'deactivate_product',	
			'validate_license'		=>'validate_license',					
			'revalidate_license'	=>'revalidate_license',					
			'export_events'			=>'export_events',			
			'get_addons_list'		=>'get_addons_list',
			'export_settings'		=>'export_settings',
			'import_settings'		=>'import_settings',
			'get_event_tax_term_section'=>'get_event_tax_term_section',
			'event_tax_list'		=>'event_tax_list',
			'event_tax_save_changes'=>'event_tax_save_changes',
			'event_tax_remove'		=>'event_tax_remove',
			'eventpost_update_meta'	=>'evo_eventpost_update_meta',
			'admin_test_email'		=>'admin_test_email',
			'admin_get_environment'		=>'admin_get_environment',
			'admin_system_log'		=>'admin_system_log',
			'admin_get_views'		=>'admin_get_views',
			'rel_event_list'		=>'rel_event_list',
			'get_latlng'				=>'get_latlng',

			'config_virtual_event'	=>'config_virtual_event',
			'select_virtual_moderator'	=>'select_virtual_moderator',
			'get_virtual_users'	=>'get_virtual_users',
			'save_virtual_mod_settings'	=>'save_virtual_mod_settings',
			'save_virtual_event_settings'	=>'save_virtual_event_settings',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {

			$prepend = 'eventon_';
			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		add_action('wp_ajax_eventon-feature-event', array($this, 'eventon_feature_event'));

		$this->helper = new evo_helper();
	}

	// virtual events
		public function config_virtual_event(){
			$post_data = $this->helper->sanitize_array( $_POST);

			$EVENT = new EVO_Event( $post_data['eid'] );

			ob_start();

			include_once('views/virtual_event_settings.php');

			echo json_encode(array(
				'status'=>'good','content'=> ob_get_clean()
			));exit;
		}
		public function select_virtual_moderator(){
			
			ob_start();

			$eid = sanitize_text_field( $_POST['eid'] );

			$EVENT = new EVO_Event( $eid);
			
			$set_user_role = $EVENT->get_prop('_evo_user_role');
			$set_mod = $EVENT->get_prop('_mod');

			global $wp_roles;
			?>
			<div style="padding:20px">
				<form class='evo_vir_select_mod'>
					<input type="hidden" name="action" value='eventon_save_virtual_mod_settings'>
					<input type="hidden" name="eid" value='<?php echo $eid;?>'>
					
					<p class='row'>
						<label><?php _e('Select a user role to find users');?></label>
						<select class='evo_select_more_field evo_virtual_moderator_role' name='_user_role' data-eid='<?php echo $eid;?>'>
							<option value=''> -- </option>
							<?php 
							
							foreach($wp_roles->roles as $role_slug=>$rr){
								$select = $set_user_role == $role_slug ? 'selected="selected"' :'';
								echo "<option value='". $role_slug. "' {$select}>". $rr['name'] .'</option>';
							}

						?></select>
					</p>
					<p class='row evo_select_more_field_2'>
						<label><?php _e('Select a user for above role');?></label>
						<select name='_mod' class='evo_virtual_moderator_users'>
							<?php
							if( $set_user_role ):
								echo $this->get_virtual_users_select_options($set_user_role, $set_mod );
							else:
							?>
								<option value=''>--</option>
							<?php endif;?>
						</select>
					</p>
					<p class='evo_save_changes' ><span class='evo_btn save_virtual_event_mod_config ' data-eid='<?php echo $eid;?>' style='margin-right: 10px'><?php _e('Save Changes','eventon');?></span></p>
				</form>
			</div>

			<?php

			echo json_encode(array(
				'status'=>'good','content'=> ob_get_clean()
			));exit;
		}
		public function get_virtual_users_select_options($role_slug, $set_user_id=''){
			
			$users = get_users( array( 
				'role' => $role_slug,
				'fields'=> array('ID','user_email', 'display_name') 
			) );
			$output = false;
			
			if($users){
				foreach($users as $user){
					$select = ( !empty($set_user_id) && $set_user_id == $user->ID) ? "selected='selected'":'';
					$output .= "<option value='{$user->ID}' {$select}>{$user->display_name} ({$user->user_email})</option>";
				}
			}
			return $output;
		}
		public function get_virtual_users(){

			$user_role = sanitize_text_field( $_POST['_user_role']);

			echo json_encode(array(
				'status'=>'good',
				'content'=> empty($user_role) ? 
					"<option value=''>--</option>" : 
					$this->get_virtual_users_select_options($user_role)
			));exit;

			
		}
		public function save_virtual_event_settings(){
			$post_data = $this->helper->sanitize_array( $_POST);

			$EVENT = new EVO_Event( $post_data['event_id']);


			foreach($post_data as $key=>$val){

				if( in_array($key, array( '_vir_url','_vir_after_content','_vir_pre_content','_vir_embed'))){
					$val = $post_data[$key] = $_POST[ $key ];
				}

				$EVENT->save_meta($key, $val);
			}

			echo json_encode(array(
				'status'=>'good','msg'=> __('Virtual Event Data Saved Successfully','eventon')
			));exit;
		}
		public function save_virtual_mod_settings(){
			$post_data = $this->helper->sanitize_array( $_POST);

			$EVENT = new EVO_Event( $post_data['eid']);

			$EVENT->save_meta('_evo_user_role', $post_data['_user_role']);
			$EVENT->save_meta('_mod', $post_data['_mod']);

			echo json_encode(array(
				'status'=>'good','msg'=> __('Moderator Data Saved Successfully','eventon')
			));exit;
			
		}

	// Related Events
		function rel_event_list(){
			$post_data = $this->helper->sanitize_array( $_POST);


			$event_id = (int)$post_data['eventid'];
			$EVs = json_decode( stripslashes($post_data['EVs']), true );

			$events = get_posts(
				array(
					'posts_per_page'=>-1,
					'post_type'=>'ajde_events',
					'exclude'=> $event_id,
					'post_status'=>'publish'
				)
			);


			ob_start();

			echo "<div class='evo_rel_events_form' data-eventid='{$event_id}'>";

			if(count($events)>0){				
				?><div class='evo_rel_events_list'><?php
				
				foreach ( $events as $post ) {		

					$event_id = $post->ID;
					$EV = new EVO_Event($event_id);

					$time = $EV->get_formatted_smart_time();
					?><span class='rel_event<?php echo (is_array($EVs) && array_key_exists($event_id.'-0', $EVs))?' select':'';?>' data-id="<?php echo $event_id.'-0';?>" data-n="<?php echo htmlentities($post->post_title, ENT_QUOTES)?>" data-t='<?php echo $time;?>'><b></b>
						<span class='o'>
							<span class='t'><?php echo $time;?></span>
							<span class='n'><?php echo $post->post_title;?></span>
						</span>
					</span><?php

					$repeats = $EV->get_repeats_count();
					if($repeats){
						for($x=1; $x<=$repeats; $x++){
							$time = $EV->get_formatted_smart_time($x);

							$select = (is_array($EVs) && array_key_exists($event_id.'-'.$x, $EVs) ) ?' select':'';
							
							?><span class='rel_event<?php echo $select;?>' data-id="<?php echo $event_id.'-'.$x;?>" data-n="<?php echo htmlentities($post->post_title, ENT_QUOTES)?>" data-t='<?php echo $time;?>'><b></b>
								<span class='o'>
									<span class='t'><?php echo $time;?></span>
									<span class='n'><?php echo $post->post_title;?></span>
								</span>
							</span><?php
						}
					}
				}
				
				?></div>
				<p style='text-align:center; padding-top:10px;'><span class='evo_btn evo_save_rel_events'><?php _e('Save Changes','eventon');?></span></p>
				<?php
			}else{
				?><p><?php _e('You must create events first!','eventon');?></p><?php
			}

			echo "</div>";

			echo json_encode(array(
				'status'=>'good',
				'content'=>ob_get_clean()
			)); exit;
		}


	// Get Location Cordinates
		public function get_latlng(){
			$gmap_api = EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1');

			if( !isset($_POST['address'])){
				echo json_encode(array(
				'status'=>'bad','m'=> __('Address Missing','eventon'))); exit;
			}

			$address = sanitize_text_field($_POST['address']);
			
			$address = str_replace(" ", "+", $address);
			$address = urlencode($address);
			
			$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$gmap_api;

			$response = wp_remote_get($url);

			$response = wp_remote_retrieve_body( $response );
			if(!$response){ 
				echo json_encode(array(
				'status'=>'bad','m'=> __('Could not connect to google maps api','eventon'))); exit;
			}

			$RR = json_decode($response);

		    echo json_encode(array(
				'status'=>'good',
				'lat' => $RR->results[0]->geometry->location->lat,
		        'lng' => $RR->results[0]->geometry->location->lng,
			)); exit;
		}

	// get HTML views
		function admin_get_views(){

			$post_data = $this->helper->sanitize_array( $_POST);

			if(!isset($_POST['type'])){
				echo 'failed'; exit;
			} 

			$type = $_POST['type'];
			$data = isset($_POST['data'])? $_POST['data']: array();

			$views = new EVO_Views();

			echo json_encode(array(
				'status'=>'good','html'=>$views->get_html($type, $data)
			)); exit;
		}

	// update event post meta
		function evo_eventpost_update_meta(){
			if(isset($_POST['eid']) && isset($_POST['values']) ){
				
				$post_data = $this->helper->sanitize_array( $_POST);

				foreach($post_data['values'] as $key=>$val){
					update_post_meta($post_data['eid'], $key, $val);

					do_action('eventon_saved_event_metadata', $post_data['eid'], $key, $val);
				}
				echo json_encode(array(
					'status'=>	'good',
					'msg'=>	__('Successfully saved event meta data!','eventon')
				)); exit;
			}else{
				echo 'Event ID not available!'; exit;
			}
		}

	// get event singular tax term form or list
		function get_event_tax_term_section(){			
			echo json_encode(array(
				'status'=>'good',
				'content'=> EVO()->evo_admin->metaboxes->get_tax_form()
			)); exit;
		}

		// tax term list
		function event_tax_list(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$terms = get_terms(
				$post_data['tax'],
				array(
					'orderby'           => 'name', 
				    'order'             => 'ASC',
				    'hide_empty'=>false
				) 
			);

			ob_start();
			echo "<div class='evo_tax_entry' data-eventid='{$post_data['eventid']}' data-tax='{$post_data['tax']}' data-type='list'>";

			if(count($terms)>0){				
				?><select class='field' name='event_tax_termid'><?php
				if(empty($post_data['termid'])){
					?><option value=""><?php _e('Select from the list','eventon');?></option><?php
				}
				foreach ( $terms as $term ) {

					if( empty($term->name)) continue;

					$selected = (!empty($post_data['termid']) && $term->term_id == $post_data['termid'])? 'selected="selected"':'';
					?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
				}
				?></select>
				<p style='text-align:center; padding-top:10px;'><span class='evo_btn evo_term_submit'><?php _e('Save Changes','eventon');?></span></p>
				<?php
			}else{
				?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
			}

			echo "</div>";

			echo json_encode(array(
				'status'=>'good',
				'content'=>ob_get_clean()
			)); exit;
		}

		// save changes
		function event_tax_save_changes(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$status = 'bad';
			$content = '';
			$tax = $post_data['tax'];

			switch($post_data['type']){
			case 'list':
				if(!empty($post_data['event_tax_termid'])){
					$event_id = (int)$post_data['eventid'];
					wp_set_object_terms( $event_id, (int)$post_data['event_tax_termid'], $tax , false);
					$status = 'good';
					$content = __('Changes successfully saved!','eventon');	
				}else{
					$content = __('Term ID was not passed!','eventon');	
				}
			break;
			case 'new':
			case 'edit':
				
				if(!isset($post_data[ 'term_name' ])) break;

				$term_name = esc_attr(stripslashes($post_data[ 'term_name' ]));
				$term = term_exists( $term_name, $tax );
				
				if($term !== 0 && $term !== null){
					$taxtermID = (int)$term['term_id'];
					wp_set_object_terms( $post_data['eventid'], $taxtermID, $tax );
				}else{
					// create slug from term name
						$trans = array(" "=>'-', ","=>'');
						$term_slug= strtr($term_name, $trans);

					// create wp term
					$new_term_ = wp_insert_term( $term_name, $tax , array('slug'=>$term_slug) );

					if(!is_wp_error($new_term_)){
						$taxtermID = (int)$new_term_['term_id'];
					}	
				}

				$fields = EVO()->taxonomies->get_event_tax_fields_array($post_data['tax'],'');

				
				// if a term ID is present
				if($taxtermID){
					$term_meta = array();

					// save description
					$term_description = isset($post_data['description'])? sanitize_text_field($post_data['description']):'';
					$tt = wp_update_term($taxtermID, $tax, array( 'description'=>$term_description ));
					
					// lat and lon values saved in the form
						if(isset($post_data['location_lon'])) $term_meta['location_lon'] = str_replace('"', "'", $post_data['location_lon']); 
						if(isset($post_data['location_lat'])) $term_meta['location_lat'] = str_replace('"', "'", $post_data['location_lat']); 

					foreach($fields as $key=>$value){
						if(in_array($key, array('description', 'submit','term_name','evcal_lat','evcal_lon'))) continue;

						if(isset($post_data[$value['var']])){

							do_action('evo_tax_save_each_field',$value['var'], $post_data[$value['var']]);

							
							if($value['var']=='location_address'){
								if(isset($post_data['location_address']))
									$latlon = eventon_get_latlon_from_address($post_data['location_address']);

								// longitude
								$term_meta['location_lon'] = isset($term_meta['location_lon']) ? $term_meta['location_lon']:
									(!empty($latlon['lng'])? floatval($latlon['lng']): null);

								// latitude
								$term_meta['location_lat'] = isset($term_meta['location_lat']) ? $term_meta['location_lat']:
									(!empty($latlon['lat'])? floatval($latlon['lat']): null);

								$term_meta['location_address' ] = (isset($post_data[ 'location_address' ]))? $post_data[ 'location_address' ]:null;

								continue;
							}


							$term_meta[ $value['var'] ] = str_replace('"', "'", $post_data[$value['var']]); 

						}else{
							$term_meta[ $value['var'] ] = ''; 
						}
					}

					// save meta values
						evo_save_term_metas($tax, $taxtermID, $term_meta);
					// assign term to event & replace
						wp_set_object_terms( $post_data['eventid'], $taxtermID, $tax , false);	

					$status = 'good';
					$content = __('Changes successfully saved!','eventon');	
				}

			break;
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($tax , $post_data['eventid'] )
			)); exit;
		}
		// remove a taxonomy term
		function event_tax_remove(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$status = 'bad';
			$content = '';
			
			if(!empty($post_data['termid'])){
				$event_id = (int)$post_data['eventid'];
				wp_remove_object_terms( $event_id, (int)$post_data['termid'], $post_data['tax'] , false);
				$status = 'good';
				$content = __('Changes successfully saved!','eventon');	
			}else{
				$content = __('Term ID was not passed!','eventon');	
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($post_data['tax'] , $post_data['eventid'] )
			)); exit;
		}

	// export eventon settings
		function export_settings(){
			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'evo_export_settings')) die('Security Check Failed!');

			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=Evo_settings__".date("d-m-y").".json");
			
			$json = array();
			$evo_options = get_option('evcal_options_evcal_1');
			foreach($evo_options as $field=>$option){
				// skip fields
				if(in_array($field, array('option_page','action','_wpnonce','_wp_http_referer'))) continue;
				$json[$field] = $option;
			}

			echo json_encode($json);
			exit;
		}
	// import settings
		function import_settings(){
			$output = array('status'=>'','msg'=>'');
			// verify nonce
				$output['success'] =wp_create_nonce('eventon_admin_nonce');
				if(!wp_verify_nonce($_POST['nonce'], 'eventon_admin_nonce')) $output['msg'] = __('Security Check Failed!','eventon');

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) $output['msg'] = __('User not loggedin!','eventon');

			$post_data = $this->helper->sanitize_array( $_POST);
			$JSON_data = $post_data['jsondata'];

			// check if json array present
			if(!is_array($JSON_data))  $output['msg'] = __('Not correct json format!','eventon');

			// if all good
			if( empty($output['msg'])){
				update_option('evcal_options_evcal_1', $JSON_data);
				$output['success'] = 'good';
				$output['msg'] = 'Successfully updated settings!';
			}
			
			echo json_encode($output);
			exit;

		}

	// export events as CSV
	// @version 2.2.30
		function export_events(){

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Security Check Failed!');

			header('Content-Encoding: UTF-8');
        	header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename=Eventon_events_".date("d-m-y").".csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			
			$evo_opt = get_option('evcal_options_evcal_1');
			$event_type_count = evo_get_ett_count($evo_opt);
			$cmd_count = evo_calculate_cmd_count($evo_opt);

			$fields = apply_filters('evo_csv_export_fields',array(
				'publish_status',	
				'event_id',			
				'evcal_event_color'=>'color',
				'event_name',				
				'event_description','event_start_date','event_start_time','event_end_date','event_end_time',

				'evcal_allday'=>'all_day',
				'evo_hide_endtime'=>'hide_end_time',
				'evcal_gmap_gen'=>'event_gmap',
				'evo_year_long'=>'yearlong',
				'_featured'=>'featured',

				'evo_location_id'=>'evo_location_id',
				'evcal_location_name'=>'location_name',	// location name			
				'evcal_location'=>'event_location',	// address		
				'location_desc'=>'location_description',	
				'location_lat'=>'location_latitude',	
				'location_lon'=>'location_longitude',	
				'location_link'=>'location_link',	
				'location_img'=>'location_img',	
				
				'evo_organizer_id'=>'evo_organizer_id',
				'evcal_organizer'=>'event_organizer',
				'organizer_description'=>'organizer_description',
				'organizer_contact'=>'evcal_org_contact',
				'organizer_address'=>'evcal_org_address',
				'organizer_link'=>'evcal_org_exlink',
				'organizer_img'=>'evo_org_img',

				'evcal_subtitle'=>'evcal_subtitle',
				'evcal_lmlink'=>'learnmore link',
				'image_url',

				'evcal_repeat'=>'repeatevent',
				'evcal_rep_freq'=>'frequency',
				'evcal_rep_num'=>'repeats',
				'evp_repeat_rb'=>'repeatby',
			));
			
			// Print out the CSV file header
				$csvHeader = '';
				foreach($fields as $var=>$val){	$csvHeader.= $val.',';	}

				// event types
					for($y=1; $y<=$event_type_count;  $y++){
						$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
						$csvHeader.= $_ett_name.',';
						$csvHeader.= $_ett_name.'_slug,';
					}
				// for event custom meta data
					for($z=1; $z<=$cmd_count;  $z++){
						$_cmd_name = 'cmd_'.$z;
						$csvHeader.= $_cmd_name.",";
					}

				$csvHeader = apply_filters('evo_export_events_csv_header',$csvHeader);
				$csvHeader.= "\n";
				
				echo (function_exists('iconv'))? iconv("UTF-8", "ISO-8859-2", $csvHeader): $csvHeader;
 	
 			// events
			$events = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'ajde_events',
				'post_status'=>'any'			
			));

			if($events->have_posts()):

				$DD = new DateTime('now', EVO()->calendar->timezone0);
				
				// allow processing content for html readability
				$process_html_content = true;

				// for each event
				while($events->have_posts()): $events->the_post();
					$__id = get_the_ID();
					$pmv = get_post_meta($__id);

					$csvRow = '';
					$csvRow.= get_post_status($__id).",";
					$csvRow.= $__id.",";
					$loctaxid = $orgtaxid = '';
					$loctaxname = $orgtaxname = '';

					//echo (!empty($pmv['_featured'])?$pmv['_featured'][0]:'no').",";
					$csvRow.= (!empty($pmv['evcal_event_color'])? $pmv['evcal_event_color'][0]:'').",";

					// location for this event
						$_event_location_term = wp_get_object_terms( $__id, 'event_location' );
						$location_term_meta = $event_location_term_id = false;
						if ( $_event_location_term && ! is_wp_error( $_event_location_term ) ){
							$event_location_term_id = $_event_location_term[0]->term_id;
							$location_term_meta = evo_get_term_meta('event_location',$event_location_term_id, '', true);
						}

					// Organizer for this event
						$_event_organizer_term = wp_get_object_terms( $__id, 'event_organizer' );
						$organizer_term_meta = $organizer_term_id = false;
						if( $_event_organizer_term && !is_wp_error($_event_organizer_term)){
							$organizer_term_id = $_event_organizer_term[0]->term_id;
							$organizer_term_meta = evo_get_term_meta('event_organizer',$organizer_term_id, '', true);
						}

					// Event Initial
						// event name
							$eventName = get_the_title();
							$eventName = $this->html_process_content($eventName, $process_html_content);
							//$eventName = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $eventName);
							//$eventName =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $output); 
							$eventName = str_replace('&amp;#8217;', "'", $eventName);
							$csvRow.= '"'. $eventName.'",';

						$event_content = get_the_content();
							$event_content = str_replace('"', "'", $event_content);
							$event_content = str_replace(',', "\,", $event_content);
							$event_content = $this->html_process_content( $event_content, $process_html_content);
						$csvRow.= '"'.$event_content.'",';

						// start time
							$start = (!empty($pmv['evcal_srow'])?$pmv['evcal_srow'][0]:'');
							if(!empty($start)){
								$DD->setTimestamp( $start);
								// date and time as separate columns
								$csvRow.= '"'. $DD->format( apply_filters('evo_csv_export_dateformat','m/d/Y') ) .'",';
								$csvRow.= '"'. $DD->format( apply_filters('evo_csv_export_timeformat','h:i:A') ) .'",';
							}else{ $csvRow.= "'','',";	}

						// end time
							$end = (!empty($pmv['evcal_erow'])?$pmv['evcal_erow'][0]:'');
							if(!empty($end)){
								$DD->setTimestamp( $end);
								// date and time as separate columns
								$csvRow.= '"'. $DD->format( apply_filters('evo_csv_export_dateformat','m/d/Y') ) .'",';
								$csvRow.= '"'. $DD->format( apply_filters('evo_csv_export_timeformat','h:i:A') ) .'",';
							}else{ $csvRow.= "'','',";	}

						
					// FOR EACH field
					
					foreach($fields as $var=>$val){
						// skip already added fields
							if(in_array($val, array('publish_status',	
								'event_id',			
								'color',
								'event_name',				
								'event_description','event_start_date','event_start_time','event_end_date','event_end_time',))){
								continue;
							}
						
						// yes no values
							if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent'))){

								$csvRow.= ( (!empty($pmv[$var]) && $pmv[$var][0]=='yes') ? 'yes': 'no').',';
								continue;
							}

						// organizer field
							$continue = false;
							switch($val){
								case 'evo_organizer_id':
									if($organizer_term_id){
										$csvRow .= '"'. $organizer_term_id .'",';
									}else{
										$csvRow.= ",";
									}
									$continue = true;
								break;
								case 'event_organizer':
									if($organizer_term_id){
										$csvRow.= '"'. $this->html_process_content($_event_organizer_term[0]->name, $process_html_content) . '",';	
									}elseif(!empty($pmv[$var]) ){
										$value = $this->html_process_content($pmv[$var][0], $process_html_content);
										$csvRow.= '"'.$value.'"';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'organizer_description':
									if($organizer_term_id){
										$csvRow.= '"'. $this->html_process_content($_event_organizer_term[0]->description) . '",';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'evcal_org_contact':
									$csvRow.= ($organizer_term_meta && !empty($organizer_term_meta['evcal_org_contact'])) ? '"'. $this->html_process_content($organizer_term_meta['evcal_org_contact']) .'",':
										","; $continue = true;
								break;
								case 'evcal_org_address':
									$csvRow.= ($organizer_term_meta && !empty($organizer_term_meta['evcal_org_address'])) ? '"'. $this->html_process_content($organizer_term_meta['evcal_org_address']) .'",':
										","; $continue = true;
								break;
								case 'evcal_org_exlink':
									$csvRow.= ($organizer_term_meta && !empty($organizer_term_meta['evcal_org_exlink'])) ? '"'. $this->html_process_content($organizer_term_meta['evcal_org_exlink']) .'",':
										","; $continue = true;
								break;
								case 'evo_org_img':
									$csvRow.= ($organizer_term_meta && !empty($organizer_term_meta['evo_org_img'])) ? '"'. $organizer_term_meta['evo_org_img'] .'",':","; $continue = true;
								break;
							}
							if($continue) continue;

						// location tax field
							$continue = false;
							switch ($val){
								case 'location_description':
									if ( $event_location_term_id ){
										$csvRow.= '"'. $this->html_process_content( $_event_location_term[0]->description) . '",';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'evo_location_id':
									if ( $event_location_term_id ){
										$csvRow.= '"'.$event_location_term_id . '",';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'location_name':
									if($event_location_term_id){
										$csvRow.= '"'. $this->html_process_content( $_event_location_term[0]->name, $process_html_content) . '",';									
									}elseif(!empty($pmv[$var]) ){
										$value = $this->html_process_content($pmv[$var][0], $process_html_content);
										$csvRow.= '"'.$value.'"';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'event_location':
									if($location_term_meta){
										$csvRow.= !empty($location_term_meta['location_address'])? 
											'"'. $this->html_process_content($location_term_meta['location_address'], $process_html_content) . '",':
											",";									
									}elseif(!empty($pmv[$var]) ){
										$value = $this->html_process_content($pmv[$var][0], $process_html_content);
										$csvRow.= '"'.$value.'"';
									}else{	$csvRow.= ",";	}
									$continue = true;
								break;
								case 'location_latitude':
									$csvRow.= ($location_term_meta && !empty($location_term_meta['location_lat'])) ? '"'. $location_term_meta['location_lat'] .'",':
										","; $continue = true;									
								break;
								case 'location_longitude':
									$csvRow.= ($location_term_meta && !empty($location_term_meta['location_lon'])) ? '"'. $location_term_meta['location_lon'] .'",':
										","; $continue = true;									
								break;
								case 'location_link':
									$csvRow.= ($location_term_meta && !empty($location_term_meta['evcal_location_link'])) ? '"'. $location_term_meta['evcal_location_link'] .'",':
										","; $continue = true;									
								break;
								case 'location_img':
									$csvRow.= ($location_term_meta && !empty($location_term_meta['evo_loc_img'])) ? '"'. $location_term_meta['evo_loc_img'] .'",':
										","; $continue = true;									
								break;
							}

							if($continue) continue;

						// skip fields
						if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent','color','publish_status','event_name','event_description','event_start_date','event_start_time','event_end_date','event_end_time','evo_organizer_id', 'evo_location_id'
							)
						)) continue;

						// image
							if($val =='image_url'){
								$img_id =get_post_thumbnail_id($__id);
								if($img_id!=''){
									
									$img_src = wp_get_attachment_image_src($img_id,'full');
									if($img_src){
										$csvRow.= $img_src[0].",";
									}else{
										$csvRow.= ",";
									}
									
								}else{ $csvRow.= ",";}
							}else{
								if(!empty($pmv[$var])){
									$value = $this->html_process_content($pmv[$var][0], $process_html_content);
									$csvRow.= '"'.$value.'"';
								}else{ $csvRow.= '';}
								$csvRow.= ',';
							}
					}
					
					// event types
						for($y=1; $y<=$event_type_count;  $y++){
							$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
							$terms = get_the_terms( $__id, $_ett_name );

							if ( $terms && ! is_wp_error( $terms ) ){
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->term_id.',';
									//$csvRow.= $term->name.',';
								}
								$csvRow.= '",';

								// slug version
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->slug.',';
								}
								$csvRow.= '",';
							}else{ $csvRow.= ",";}
						}
					// for event custom meta data
						for($z=1; $z<=$cmd_count;  $z++){
							$cmd_name = '_evcal_ec_f'.$z.'a1_cus';
							$csvRow.= (!empty($pmv[$cmd_name])? 
								'"'.str_replace('"', "'", $this->html_process_content($pmv[$cmd_name][0], $process_html_content) ) .'"'
								:'');
							$csvRow.= ",";
						}

					$csvRow = apply_filters('evo_export_events_csv_row',$csvRow, $__id, $pmv);
					$csvRow.= "\n";

					if( EVO()->cal->check_yn('evo_disable_csv_formatting','evcal_1')){
						echo $csvRow;
					}else{
						echo (function_exists('iconv'))? iconv("UTF-8", "ISO-8859-2", $csvRow): $csvRow;
					}
				

				endwhile;
			endif;

			wp_reset_postdata();
		}

		function html_process_content($content, $process = true){
			//$content = iconv('UTF-8', 'Windows-1252', $content);
			return ($process)? htmlentities($content, ENT_QUOTES): $content;
		}

	// Validation of eventon products
		function validate_license(){
			
			$post_data = $this->helper->sanitize_array( $_POST);

			$status = 'bad'; 
			$error_code = 11; 
			$error_msg_add = $html = $email = $msg = '';
			
			// check for required information
			if(empty($post_data['type']) && isset($post_data['key']) && isset($post_data['slug']) ){ 
				echo json_encode(array('status'=>'bad','error_msg'=> EVO_Error()->error_code(14) ));		
				exit;
			}

			// Initial values
			$type = $post_data['type'];
			$license_key = $post_data['key'];
			$slug = $post_data['slug'];

			$PROD = new EVO_Product_Lic($slug);
			
			// check for key format validation
			$verifyformat = $PROD->purchase_key_format($license_key );
			if(!$verifyformat) $error_code = '02';	

			// check if email provided for eventon addons
			if( $post_data['slug'] != 'eventon'){
				if(empty($post_data['email'])){
					$status = 'bad';
					$msg = 'Email address not provided!';
					$verifyformat = false;
				}else{
					$email = str_replace(' ','',$post_data['email']);
				}
			}
			
			// if license key format is validated
			if($verifyformat){

				// save eventon data
				if($type=='main') $PROD->save_license_data();

				$status = 'good';
				$msg = ($slug=='eventon')?
					'Excellent! Purchase key verified and saved. Thank you for activating EventON!':
					'Excellent! License key verified and saved. Thank you for activating EventON addon!';

				$data_args = array(
					'type'		=>(!empty($post_data['type'])?$post_data['type']:'main'),
					'key'		=> addslashes( str_replace(' ','',$license_key) ),
					'email'		=> $email,
					'product_id'=>(!empty($post_data['product_id'])?$post_data['product_id']:''),
				);
				$validation = $PROD->remote_validation($data_args);

				// Other update tasks
				if($type=='addon'){	
					// update other addon fields
					foreach(array(
						'email','product_id','instance','key'
					) as $field){
						if(!empty($post_data[$field])){
							$PROD->set_prop( $field, $post_data[$field], false);
						}
					}
					$PROD->save();
				}

				$results = $this->get_remote_validation_results($validation, $PROD, $type);
			
				if(isset($results['error_code'])) $error_code = $results['error_code'];

				$status = $results['status'];
					
				if($error_code != 11){
					$msg = EVO_Error()->error_code( $error_code);
				}

				if($results['status'] == 'bad' && in_array( $error_code, array(11,21,23) )){
					$msg = EVO_Error()->error_code( 120 );
				}

			}else{
				// Invalid license key format
				$status = 'bad';
				if(empty($msg)) $msg = 'License Key format is not a valid format!';
			}

			$return_content = array(
				'status'=>	$status,
				'msg'=> 	$msg,				
				'code'=> 	$error_code,
				'html'=>	$this->get_html_view( $type,$slug),
				'debug'=> $validation
			);
			echo json_encode($return_content);		
			exit;
		}

		// RE-VALIDATE
			function revalidate_license(){
				$post_data = $this->helper->sanitize_array( $_POST);
				$slug = $post_data['slug'];

				$PROD = new EVO_Product_Lic($slug);

				//echo $PROD->get_prop('key');

				if( !$PROD->get_prop('key') || !$PROD->get_prop('email')){
					echo json_encode(array(
						'status'=>'bad',
						'msg'=>'Required fields for remote validation are missing! try deactivating and reactivating again.'
					));		
					exit;
				}else{

					$ERR = new EVO_Error();
					$ERR->record_gen_log('Re-activating', $slug,'','',false);

					$data_args = array(
						'type'		=>(!empty($post_data['type'])?$post_data['type']:'main'),
						'key'		=> $PROD->get_prop('key'),
						'email'		=> $PROD->get_prop('email'),
						'product_id'=>(!empty($post_data['product_id'])?$post_data['product_id']:''),						
						'instance'	=> md5(get_site_url()),
					);
					$validation = $PROD->remote_validation($data_args);
					
					$results = $this->get_remote_validation_results( $validation, $PROD , $post_data['type']);
					$output_error_code = isset($results['error_code'])? (int) $results['error_code']: false;

					if($results['status'] == 'bad'){
						$ERR->record_gen_log('Re-activating failed', $slug, $results['error_code'],'',false);
					}
					
					$ERR->save();

					// Message intepretation
					$msg = ($results['status']=='bad'? EVO_Error()->error_code(15): EVO_Error()->error_code(16));
					if($results['status'] == 'bad' && $output_error_code && in_array( $output_error_code, array(11,21,23) )){
						$msg = EVO_Error()->error_code( 121 );
					}

					if($output_error_code && in_array( $output_error_code, array(100,101,102,103)) ){
						$msg = EVO_Error()->error_code( $output_error_code );

						if( $output_error_code == 103){
							$msg = EVO_Error()->error_code( '103r' );
							EVO_Error()->record_deactivation_loc($slug);
							$PROD->deactivate();
						}
					}

					echo json_encode(array(
						'status'=> $results['status'],
						'msg'=> $msg,
						//'error_msg'=> EVO_Error()->error_code( $results['error_code']),
						'html'=> $this->get_html_view( 'addon',$slug),					
					));		
					exit;
				}
			}

	// REMOTE RESULTS
		function get_remote_validation_results($validation, $PROD, $type){
			// validation contain // status, error_remote_msg, error_code, api_url
			// invalid remote validation
			$output = array();
			$error_code = false;
			if($validation['status'] =='good'){
				$output['status'] = 'good';
				EVO_Prods()->get_remote_prods_data();
				$PROD->evo_kriyathmaka_karanna();
				EVO_Error()->record_activation_rem();

			}else{
				$output['status'] = 'bad';	
				if(!empty($validation['error_code'])) $error_code =  (int)$validation['error_code'];
				
				$output['error_code'] = $error_code;
				$output['error_msg'] = isset($validation['error_remote_msg'])? $validation['error_remote_msg']: '';

				// local kriyathmaka karala nehe
				if(!$PROD->kriyathmaka_localda() && $error_code && in_array( $error_code, array(11,21,23) ) ){
					$PROD->evo_kriyathmaka_karanna_athulen();
					EVO_Error()->record_activation_loc($error_code);
				}
			}

			return $output;
		}

	// Deactivate EventON Products
		function deactivate_product(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$error_msg = $status = $html = '';
			$error_code = '00';
			
			if($post_data['type'] == 'main'){
				$PROD = new EVO_Product_Lic('eventon');
				$status = $PROD->deactivate();

				$slug = 'eventon';

				// not able to deactivate
				if(!$status){
				 	$error_code = '07';	
				}else{ // deactivated
					EVO_Error()->record_deactivation_loc($slug);
					$html = $this->get_html_view('main',$slug);
					$error_code = 32;
				}
				
			}else{// for addons

				if(!isset($post_data['slug'])){
					echo json_encode(array(
						'status'=>'bad',
						'error_msg'=> EVO_Error()->error_code(14)
					)); exit;
				}

				$PROD = new EVO_Product_Lic($post_data['slug']);
			
				// passing data
					$remote_data = array(
						'key'		=> addslashes( str_replace(' ','',$post_data['key']) ),
						'email'		=>(!empty($post_data['email'])? $post_data['email']: null),
						'product_id'=>(!empty($post_data['product_id'])? $post_data['product_id']: null),
					);

				// deactivate addon from remote server
					$deactive_remotely = $PROD->remote_deactivate($remote_data);

					$returned_error_code = isset($deactive_remotely['error_code'])? (int)$deactive_remotely['error_code']:false;

					if($returned_error_code && in_array( $returned_error_code, array(30,31) ) ){
						
						EVO_Error()->record_deactivation_fail($returned_error_code);
						EVO_Error()->record_deactivation_loc($post_data['slug']);
						$PROD->deactivate();
						$error_code = 32;
					}else{
						$error_code = 33;
						EVO_Error()->record_deactivation_rem();
						$PROD->deactivate();
					}


					$html = $this->get_html_view('addon',$post_data['slug']);
					$status = 'success';
			}

			$return_content = array(
				'status'=> ($status?'success':'bad'),
				'msg'=>EVO_Error()->error_code($error_code),
				'html'=> $html,					
			);
			echo json_encode($return_content);		
			exit;
		}

		function get_html_view($type,$slug){
			$views = new EVO_Views();
			$var = ($type=='main')? 'evo_box': 'evo_addon_box';
			return $views->get_html(	$var,array('slug'	=>$slug) );
		}
		
	// get all addon details
		public function get_addons_list(){

			// verifications
			if(!is_admin()) return false;

			$active_plugins = get_option( 'active_plugins' );

			ob_start();
			// installed addons		

				$addons_list = new EVO_Addons_List();

				$count=1;
				// EACH ADDON
				foreach($addons_list->get_list() as $slug=>$product){

					if($slug=='eventon') continue; // skip for eventon
					$_has_addon = false;

					$views = new EVO_Views();

					echo $views->get_html(
						'evo_addon_box',
						array(
							'slug'				=>$slug,
							'product'			=>$product,
							'active_plugins'	=>$active_plugins
						)
					);
					
					$count++;
				} //endforeach

			$content = ob_get_clean();

			$return_content = array(
				'content'=> $content,
				'status'=>true
			);			
			echo json_encode($return_content);	exit;	
		}

	/** Feature an event from admin */
		function eventon_feature_event() {

			if ( ! is_admin() ) wp_die( __( 'Only available in admin side.', 'eventon' ) );

			if ( ! current_user_can('edit_eventons') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'eventon' ) );

			if ( ! check_admin_referer('eventon-feature-event')) wp_die( __( 'You have taken too long. Please go back and retry.', 'eventon' ) );

			$post_id = isset( $_GET['eventID'] ) && (int) $_GET['eventID'] ? (int) $_GET['eventID'] : '';

			if (!$post_id) wp_die( __( 'Event id is missing!', 'eventon' ) );

			$post = get_post($post_id);

			if(!$post) wp_die( __( 'Event post doesnt exists!'),'eventon');
			if( $post->post_type !== 'ajde_events' ) wp_die( __('Post type is not an event', 'eventon' ) );

			$featured = get_post_meta( $post->ID, '_featured', true );

			wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
			
			if( $featured == 'yes' )
				update_post_meta($post->ID, '_featured', 'no');
			else
				update_post_meta($post->ID, '_featured', 'yes'); 

			wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
			exit;
		}
	
	// Diagnose
		// send test email
		function admin_test_email(){
			$post_data = $this->helper->sanitize_array( $_POST);
			$email_address = $post_data['email'];

			$result = wp_mail($email_address, 'This is a Test Email', 'Test Email Body', array('Content-Type: text/html; charset=UTF-8') );
			
			$ts_mail_errors = array();
			if(!$result){
				global $ts_mail_errors;
				global $phpmailer;

				if (!isset($ts_mail_errors)) $ts_mail_errors = array();

				if (isset($phpmailer)) {
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
			}

			echo json_encode(array(
				'msg'=> ($result?'Email Sent': 'Email was not sent'),
				'error'=>$ts_mail_errors
			));		
			exit;
		}

		// system log
		function admin_system_log(){
			if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_admin_nonce')) die('Security Check Failed!');

			$html = '';
			ob_start();

			echo "<div style='padding:20px; font-family:courier; line-height:1.5; max-height:500px; overflow-y:auto;background-color: #5f5f5f;color: #fff;'>";
			$log = EVO_Error()->get_log('general');

			if($log){
				$log = array_filter($log);

				if(sizeof($log)==0){ 
					echo 'No Log!'; 
				}else{
					echo "<b>Data Log</b><br/><i>This data is stored in your website wp_optinos table and is not shared with anyone.</i><br/>";

					echo "<br/><br/>";
					foreach($log as $time=>$data){
						$time = explode('-', $time);
						echo date('Y-m-d h:i:s',$time[0]).": ". $data."<br/>";
					}
				}				
			}else{
				echo 'No Log!';
			}		
			echo "</div>";


			$html = ob_get_clean();

			echo json_encode(array(
				'html'=> $html
			));exit;
		}

		// environment
		function admin_get_environment(){
			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_admin_nonce')) die('Security Check Failed!');

			$data = array(); $html = ''; global $wpdb;

			// event count
			$event_posts_r = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE post_type='ajde_events'" );
			$events_count = ($event_posts_r && is_array($event_posts_r) )? count($event_posts_r):0;

			// event post meta count
			$pm_cunt_r = $wpdb->get_results( "SELECT pm.meta_id FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id WHERE p.post_type = 'ajde_events'" );
			$pm_count = ($pm_cunt_r && is_array($pm_cunt_r) )? count($pm_cunt_r):0;

			$data['EventON_version'] = EVO()->version;
			$data['WordPress_version'] = get_bloginfo( 'version' );
			$data['is_multisite'] = is_multisite()?'Yes':'No';
			$data['WordPress_memory_limit'] = WP_MEMORY_LIMIT;
			$data['PHP_version'] = phpversion();
			$data['Maximum_update_size'] = wp_max_upload_size();
			$data['CURL_enabled'] = in_array  ('curl', get_loaded_extensions() ) ? 'Yes':'No';
			$data['Events_count'] = $events_count;
			$data['Total_event_postmeta_DB_entries'] = $pm_count;

			foreach($data as $D=>$V){
				$D = str_replace('_', ' ', $D);
				$html .= "<p><span>".$D."</span><span class='data'>". $V ."</span></p>";
			}

			$html .="<em class='hr_line'></em>";
				
			echo json_encode(array(
				'html'=> $html,
			)); exit;
		}
}
new EVO_admin_ajax();