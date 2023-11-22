<?php
/**
 * Admin class for csv importer plugin
 *
 * @version 	1.1.5
 * @author  	AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evocsv_admin{
	var $log= array();
	public $evo_opt;

	function __construct(){
		global $eventon_csv;
		add_action('admin_init', array($this, 'admin_scripts'));
		
		// settings link in plugins page
		add_filter("plugin_action_links_".$eventon_csv->plugin_slug, array($this,'eventon_plugin_links' ));
		add_action( 'admin_menu', array( $this, 'menu' ),9);

		$evo_opt = get_option('evcal_options_evcal_1');
	}
	/**	Add the tab to settings page on myeventon	 */
		function tab_array($evcal_tabs){
			$evcal_tabs['evcal_csv']='CSV Import';
			return $evcal_tabs;
		}
	// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'CSV Import', __('CSV Import','eventon'), 'manage_eventon', 'evocsv', array($this, 'page_content') );
		}

	/**	CSV settings content	 */
		function page_content(){
			require_once('class-settings.php');
		}

	// Styles and scripts for the page
		public function admin_scripts(){
			global $eventon_csv, $pagenow;

			if( (!empty($pagenow) && $pagenow=='admin.php')
			 && (!empty($_GET['page']) && $_GET['page']=='evocsv') 
			){
				wp_enqueue_style( 'csv_import_styles',$eventon_csv->assets_path.'csv_import_styles.css');
				wp_enqueue_script('csv_import_script',$eventon_csv->assets_path.'script.js', array('jquery'), 1.0, true );
				wp_localize_script( 
					'csv_import_script', 
					'evocsv_ajax_script', 
					array( 
						'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
						'postnonce' => wp_create_nonce( 'evocsv_nonce' )
					)
				);
			}
		}
	
	// Supported fields 
		function get_all_fields(){
			$fields =  array(
				0=>'publish_status',
				'event_id',
				'_featured'=>'featured',
				'evcal_event_color'=>'color',
				'event_name',
				'evcal_subtitle'=>'evcal_subtitle',
				'evcal_location_name'=>'location_name',
				'evcal_location'=>'event_location',
				'evcal_location_link',
				'evcal_gmap_gen'=>'event_gmap',
				'evcal_organizer'=>'event_organizer',
				'evcal_org_contact',
				'evcal_org_address',
				'evcal_org_exlink',
				'evcal_allday'=>'all_day',
				'evo_hide_endtime'=>'hide_end_time',
				'event_start_date',
				'event_start_time',
				'event_end_date','event_end_time',
				'event_description',
				'image_url','image_id',
				'evcal_lmlink',
				'evcal_lmlink_target',
				'_evcal_exlink_option','evcal_exlink','_evcal_exlink_target',
				'evo_location_id',
				'evo_organizer_id'
			);

			$event_type_count = evo_get_ett_count($this->evo_opt);
			$cmd_count = evo_calculate_cmd_count($this->evo_opt);
			
			// include taxonomies
				for($y=1; $y<=$event_type_count;  $y++){
					$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
					$fields[$_ett_name] = $_ett_name;
				}

			// custom meta fields
				for($z=1; $z<=$cmd_count;  $z++){
					$_cmd_name = 'cmd_'.$z;
					$fields[$_cmd_name] = $_cmd_name;
					$fields[$_cmd_name.'L'] = $_cmd_name.'L'; // button
				}

			// addon additions
				$fields['evoau_assignu'] = 'evoau_assignu';

			// pluggable hook for additional fields
				$fields = apply_filters('evocsv_additional_csv_fields', $fields);

			return $fields;
		}

	// IMPORTING EVENT
		function import_event($event){

			if(empty($event['status']) || $event['status']=='ns' )
				$status = 'failed';		

			$create_event = true;

			if(!empty($event['event_id'])){
				$event_id = (int)$event['event_id'];

				$result = $this->event_exists($event_id);

				if($result){
					$this->update_event_postdata( $event_id, $event);
					$this->save_event_post_data($event_id, $event);
					$status = $event_id;	
					$create_event = false;
				}
			}

			if($create_event){
				if($post_id = $this->create_post($event) ){
					$this->save_event_post_data($post_id, $event);
					$status = $post_id;				
				}else{
					$status = 'failed';
				}
			}			
			
			return $status;
		}

		function event_exists($event_id){
			global $wpdb;

			$post_id = (int)$event_id;
			$post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
			return $post_exists;
		}	

	// update existing event's post information
		function update_event_postdata( $post_id, $post_data){
			$event_post = array(
				'ID'=> $post_id,
				'post_title'=> (isset($post_data['event_name'])? $post_data['event_name']: $post_id),
				'post_content'=> (isset($post_data['event_description'])? $post_data['event_description']:''),
				'post_status'=> (isset($post_data['publish_status'])? $post_data['publish_status']:'publish')
			);
			$res = wp_update_post( $event_post );

			if(is_wp_error($res)) return false;

			return true;
		}

	// save custom meta fields
		function save_event_post_data($post_id,$post_data){
			global $eventon_csv;

		 	$fields = 'TEST:';

		 	$location_saved = $organizer_saved = false;

		 	// for all fields
		 	foreach($this->get_all_fields() as $fieldvar=>$field){
		 		// adjust array field value
		 		$fieldvar = (is_numeric($fieldvar))? $field: $fieldvar;
		 		
		 		// yes no fields
			 		if(in_array($field, array('featured','event_gmap','all_day','hide_end_time','evcal_lmlink_target'))){

			 			$value = empty($post_data[$field])? 'no': strtolower($post_data[$field]);
			 			$this->create_custom_fields($post_id, $fieldvar, $value);	
						$fieldSaved = true;
						continue;
			 		}
		 		
		 		// for empty values
		 		if(empty($post_data[$field])) continue;
		 		
		 		//$value = addslashes(htmlspecialchars_decode($post_data[$field]) );	
		 		$value = addslashes(html_entity_decode($post_data[$field]) );	

		 		$fieldSaved = false;		 		

		 		// skip fields
			 		if(in_array($field, array('event_description','publish_status','event_name','event_start_date','event_start_time','event_end_date','event_end_time', 'evcal_location_name','evcal_location','evcal_location_link','evcal_organizer', 'evcal_org_contact','evcal_org_address','evcal_org_exlink',
			 			'image_id',
			 			'evo_location_id',
						'evo_organizer_id'
			 		) 
			 		)) continue;

		 		// parsing for event color incorrect format submittions
		 			if($fieldvar =='evcal_event_color'){
		 				if(strpos($value, '#') !== false ){
		 					$value = substr($value, 1);
		 				}
		 			}		
		 		
		 		// each taxonomy field
			 		if( strpos($field, 'event_type') !== false){
			 			$ett = explode(',', $value);

			 			// for each term
			 			foreach($ett as $et_term_val){
			 				
			 				$type = 'num';
			 				if( is_numeric($et_term_val)){
			 					$the_term = (int)$et_term_val;			 					
			 				}else{
			 					$the_term = esc_attr(stripslashes($et_term_val));
			 					$type = 'str';
			 				}
			 				
			 				$term = term_exists( $the_term, $field);


			 				// if the term exists
			 				if($term !== 0 && $term !== null){
		 						$result = wp_set_object_terms($post_id, $the_term, $field , true);
		 					}else{

		 						// if term doesnt exists and tem value is string create new term
		 						if($type == 'str'){ 
		 							$term_name = esc_attr(stripslashes($et_term_val));
									$term_slug = str_replace(" ", "-", $term_name);
									$new_term_ = wp_insert_term( $term_name, $field, array('slug'=>$term_slug) );
									if(!is_wp_error($new_term_)){
										$termID = (int)$new_term_['term_id'];
										$result = wp_set_object_terms($post_id, $termID, $field , true);
									}
		 						}else{ // NUM
		 							$result = wp_set_object_terms($post_id, $the_term, $field , true);
		 						}	 						
		 					}
			 			}

						$fieldSaved = true;
			 		}

		 		// custom fields
			 		if( strpos($field, 'cmd_') !== false){
			 			// custom field with L
			 			if( strpos($field, 'L') !== false){
			 				$num = substr($field, 4, -1);
			 				$this->create_custom_fields($post_id, '_evcal_ec_f'.$num.'a1_cusL', $value);
			 			}else{
			 				$num = substr($field, 4);
			 				$this->create_custom_fields($post_id, '_evcal_ec_f'.$num.'a1_cus', $value);
			 			}		 			
			 		}

		 		// image
			 		if($field =='image_url' && empty( $post_data['image_id'] )){
			 			$img = $this->upload_image($post_data['image_url'], $post_data['event_name']);
						if($img && is_array($img)){
							$thumbnail = set_post_thumbnail($post_id, $img[0]);
						}
			 			$fieldSaved = true;			 			
			 		}

		 		// save non saved fields as post type meta
			 		if(!$fieldSaved){
			 			$this->create_custom_fields($post_id, $fieldvar, $value);

			 		}

		 		// pluggable hook
		 		do_action('evocsv_save_event_custom_data', $post_id, $post_data, $field);

		 		$fields = $fields.' '.$field;
		 	} // endforeach

		 	// save event date and time information
		 		if(isset($post_data['event_start_date'])&& isset($post_data['event_end_date']) ){
					$start_time = !empty($post_data['event_start_time'])?
						explode(":",$post_data['event_start_time']): false;
					$end_time = !empty($post_data['event_end_time'])?
						explode(":",$post_data['event_end_time']):false;
					
					$date_array = array(
						'evcal_start_date'=>$post_data['event_start_date'],
						'evcal_start_time_hour'=>( $start_time? $start_time[0]: ''),
						'evcal_start_time_min'=>( $start_time? $start_time[1]: ''),
						'evcal_st_ampm'=> ( $start_time? $start_time[2]: ''),
						'evcal_end_date'=>$post_data['event_end_date'], 										
						'evcal_end_time_hour'=>( $end_time? $end_time[0]:''),
						'evcal_end_time_min'=>( $end_time? $end_time[1]:''),
						'evcal_et_ampm'=>( $end_time? $end_time[2]:''),

						'evcal_allday'=>( !empty($post_data['all_day'])? $post_data['all_day']:'no'),
					);

					// check start date format
						$_date_format = apply_filters('evocsv_import_date_format',$this->_date_format_val($post_data['event_start_date']) );
					
					$proper_time = eventon_get_unix_time($date_array, $_date_format );
					
					// save required start time variables
					$this->create_custom_fields($post_id, 'evcal_srow', $proper_time['unix_start']);
					$this->create_custom_fields($post_id, 'evcal_erow', $proper_time['unix_end']);		
				}
				
		 	// event location fields		 		
		 		if(!empty($post_data['evo_location_id'])){
		 			$term_loc = (int)$post_data['evo_location_id'];
	 				$term = term_exists($term_loc, 'event_location');
	 				
	 				if($term !== 0 && $term !== null){
		 				wp_set_object_terms( $post_id, $term_loc, 'event_location');
		 				$location_saved = true;
		 			}
		 		}

		 		// if location name present and location id was not saved
		 		if( !empty($post_data['location_name']) && !$location_saved){
		 			$term = term_exists($post_data['location_name'], 'event_location');
		 			if($term !== 0 && $term !== null){ // term exist
		 				// assign location term to the event		 			
		 				wp_set_object_terms( $post_id, array(esc_attr(stripslashes($post_data['location_name']) ) ), 'event_location');		
		 			}else{
		 				$term_name = esc_attr(stripslashes($post_data['location_name']));
						$term_slug = str_replace(" ", "-", $term_name);

						// create wp term
						$new_term_ = wp_insert_term( $term_name, 'event_location', array('slug'=>$term_slug) );

						if(!is_wp_error($new_term_)){
							$term_meta = array();
							$termID = (int)$new_term_['term_id'];

							// generate latLon
							if(isset($post_data['event_location']))
								$latlon = eventon_get_latlon_from_address($post_data['event_location']);

							// latitude and longitude
							$term_meta['location_lon'] = (!empty($post_data['evcal_lon']))? $post_data['evcal_lon']:
								(!empty($latlon['lng'])? floatval($latlon['lng']): null);
							$term_meta['location_lat'] = (!empty($post_data['evcal_lat']))? $post_data['evcal_lat']:
								(!empty($latlon['lat'])? floatval($latlon['lat']): null);

							$term_meta['evcal_location_link'] = (isset($post_data['evcal_location_link']))?$post_data['evcal_location_link']:null;
							$term_meta['location_address'] = (isset($post_data['event_location']))?$post_data['event_location']:null;
							$term_meta['evo_loc_img'] = (isset($post_data['evo_loc_img']))?$post_data['evo_loc_img']:null;
							
							//update_option("taxonomy_".$new_term_['term_id'], $term_meta);
							evo_save_term_metas('event_location', $termID, $term_meta);
							
							wp_set_object_terms( $post_id, $termID, 'event_location', false);

						}	
		 			}
		 		}

		 	// event organizer fields
		 		if($field =='evo_organizer_id' && !empty($post_data['evo_organizer_id'])){
	 				$term_org_id = (int)$post_data['evo_organizer_id'];
	 				$term = term_exists($term_org_id, 'event_organizer');
	 				
	 				if($term !== 0 && $term !== null){
		 				wp_set_object_terms( $post_id, $term_org_id, 'event_organizer');
		 				$organizer_saved = true;
		 			}	
	 			}
		 		if( !empty($post_data['event_organizer']) && !$organizer_saved ){
		 			$term = term_exists($post_data['event_organizer'], 'event_organizer');
		 			if($term !== 0 && $term !== null){
		 				// assign organizer term to the event
		 				wp_set_object_terms( $post_id, array(esc_attr(stripslashes($post_data['event_organizer']))), 'event_organizer');	
		 			}else{
		 				$term_name = esc_attr(stripslashes($post_data['event_organizer']));
						$term_slug = str_replace(" ", "-", $term_name);

						// create wp term
						$new_term_ = wp_insert_term( $term_name, 'event_organizer', array('slug'=>$term_slug) );

						if(!is_wp_error($new_term_)){
							$term_meta = array();
							$termID = (int)$new_term_['term_id'];

							$term_meta['evcal_org_contact'] = (isset($post_data['evcal_org_contact']))?
								str_replace('"', "'", $post_data['evcal_org_contact']):null;
							$term_meta['evcal_org_address'] = (isset($post_data['evcal_org_address']))?
								str_replace('"', "'", $post_data['evcal_org_address']):null;
							$term_meta['evo_org_img'] = (isset($post_data['evo_org_img']))?$post_data['evo_org_img']:null;
							$term_meta['evcal_org_exlink'] = (isset($post_data['evcal_org_exlink']))?$post_data['evcal_org_exlink']:null;

							//update_option("taxonomy_".$new_term_['term_id'], $term_meta);
							evo_save_term_metas('event_organizer', $termID, $term_meta);

							wp_set_object_terms( $post_id, $termID, 'event_organizer', false);				
						}	
		 			}
		 		}
			
			// Featured image as ID
				if(!empty($post_data['image_id'])){
					$passed_img_id = (int)$post_data['image_id'];
					$img = wp_get_attachment_image( $passed_img_id );
					if($img){
						set_post_thumbnail($post_id, $passed_img_id );
					}elseif( !empty($post_data['image_url']) ){
						$img = $this->upload_image($post_data['image_url'], $post_data['event_name']);
						if($img && is_array($img)){
							$thumbnail = set_post_thumbnail($post_id, $img[0]);
						}
					}
				}
			
			// ActionUser fields
				if(!empty($post_data['evoau_assignu'])){
					$users = explode(',', $post_data['evoau_assignu']);
					wp_set_object_terms( $post_id, $users, 'event_users' );
				}

			// pluggable
				do_action('evocsv_save_additional_event_metadata', $post_id, $post_data);
		}

		// sub helpers
			private function _date_format_val($D){
				$d = explode('/', $D);
				return strlen($d[2]) == 2? 'm/d/y':'m/d/Y';
			}
	
	/** Create the event post */
		function create_post($data) {

			$evoHelper = new evo_helper();

			// content for the event
			$content = (!empty($data['event_description'])?$data['event_description']:null );
			$content = str_replace('\,', ",", $content);

			$publishStatus = (!empty($data['publish_status']) && strtolower($data['publish_status'])=='publish')?
				'publish':'draft';

			return $evoHelper->create_posts(array(
				'post_status'=>$publishStatus,
				'post_type'=>'ajde_events',
				'post_title'=>convert_chars(stripslashes($data['event_name'])),
				'post_content'=>$content
			));
	    }
		function create_custom_fields($post_id, $field, $value) {       
	        update_post_meta($post_id, $field, $value);
	    }
		function get_author_id() {
			$current_user = wp_get_current_user();
	        return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
	    }
	    // upload and return event featured image
		    function upload_image($url, $event_name){
		    	if(empty($url))
		    		return false;

		    	// Download file to temp location
			      $tmp = download_url( $url );

			      // Set variables for storage
			      // fix file filename for query strings
			      preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches );
			      $file_array['name'] = basename($matches[0]);
			      $file_array['tmp_name'] = $tmp;

			      // If error storing temporarily, unlink
			      if ( is_wp_error( $tmp ) ) {
			         @unlink($file_array['tmp_name']);
			         $file_array['tmp_name'] = '';
			      }

			      // do the validation and storage stuff
			      $post_id=0;
			      $desc="Featured image for '$event_name'";
			      $id = media_handle_sideload( $file_array, $post_id, $desc );
			      // If error storing permanently, unlink
			      if ( is_wp_error($id) ) {
			         @unlink($file_array['tmp_name']);
			         return false;
			      }

			      $src = wp_get_attachment_url( $id );
			      return array(0=>$id,1=>$src);

		    }
		
	// SECONDARY FUNCTIONS
    	function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=evocsv">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
}
