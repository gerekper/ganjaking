<?php
/** 
 * ICS functions
 * @version 2.0
 */

class EVOICS_Fnc{
	function __construct(){		
		$this->options = get_option('evcal_options_evoics_1');
		EVO()->cal->load_more('evoics_1', 'evcal_options_', $this->options);
	}

// ICS event functions
	function get_events_from_ics($file){
		require_once 'lib/IcalParser.php';
		require_once 'lib/Recurrence.php';
		require_once 'lib/WindowsTimezones.php';
		require_once 'lib/Freq.php';
		$cal = new \om\IcalParser();
		$results = $cal->parseFile($file);
		$A = $cal->getSortedEvents();

		return $cal->getSortedEvents();
		
	}

	
// IMPORTING EVENT
	function import_event($event){
		if(empty($event['status']) || $event['status']=='ns' ) return false;	
		
		// alredy imported events
		if(!empty($event['imported_event_id'])){

			// if sync already imported set to yes
			if(evo_settings_check_yn($this->options, 'evoics_sync_fetched') ){

				$imported_event_id = (int)$event['imported_event_id'];

				// update description
				$description = !empty($event['event_description'])? $event['event_description']:'';
	            $my_post = array(
	                'ID'           => $imported_event_id,
	                'post_title'    => (isset($event['event_name'])? $event['event_name']:$imported_event_id),
	                'post_content' => $description,
	            );
	            wp_update_post( $my_post );

				$this->save_event_post_data($event['imported_event_id'], $event);
				return $imported_event_id;
			}

			return false;

		}else{
			// if a new event is created
			if($post_id = $this->create_post($event) ){
				$this->save_event_post_data($post_id, $event);				
				return $post_id;		
			}

			return false;
		}		
	}


// Create the event post */
	function create_post($data) {
		
		// if duplication check enabled check for existing events with same name
		if(evo_settings_check_yn($this->options,'EVOICS_dupli_check')){
			if( $R = $this->event_exists( 'name', $data['event_name'] ) ){
				return $R['ID'];
			}
		}

		$evoHelper = new evo_helper();

		// content for the event
		$content = (!empty($data['event_description'])?$data['event_description']:null );
		$content = str_replace('\,', ",", stripslashes($content) );

		$publishStatus = evo_settings_check_yn($this->options,'EVOICS_status_publish')? 'publish': 'draft';

		return $evoHelper->create_posts(array(
			'post_status'=>$publishStatus,
			'post_type'=>'ajde_events',
			'post_title'=>convert_chars(stripslashes($data['event_name'])),
			'post_content'=>$content
		));
    }

    // check if event exists by event id or name
	    function event_exists($type = 'id', $val){
			global $wpdb;

			if($type == 'id'){
				$post_id = (int)$val;
				$post_exists = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE id = '" . $post_id . "'", 'ARRAY_A');
				return $post_exists;
			}

			if($type == 'name'){
				$val = sanitize_text_field($val);
				$post_exists = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $val . "'", 'ARRAY_A');
				return $post_exists;
			}

			return false;		
		}


	// other event functions
		function create_custom_fields($post_id, $field, $value) {       
	        update_post_meta($post_id, $field, $value);
	    }
// save custom meta fields
	function save_event_post_data($post_id,$post_data){
		global $evoics;
	 	
	 	// for all fields
	 	foreach($this->get_all_fields() as $fieldvar=>$field){

	 		// for empty values
	 		if(empty($post_data[$field])) continue;

	 		// adjust array field value
	 		$fieldvar = (is_numeric($fieldvar))? $field: $fieldvar;
	 		//$value = addslashes(htmlspecialchars_decode($post_data[$field]) );	
	 		$value = addslashes(html_entity_decode($post_data[$field]) );	

	 		$fieldSaved = false;		 		

	 		// skip fields
	 		if(in_array($field, apply_filters('evoics_skipped_save_fields',array(
	 			'event_description',
	 			'event_name',
	 			'event_start_date',
	 			'event_start_time',
	 			'event_end_date',
	 			'event_end_time', 
	 			'evcal_location_name',
	 			'UID'
	 		) 
	 		))) continue;

	 		// yes no fields
	 		if(in_array($field, array('all_day'))){
	 			$value = strtolower($value);
	 			$this->create_custom_fields($post_id, $fieldvar, $value);	
				$fieldSaved = true;
	 		}

	 		// save non saved fields as post type meta
	 		if(!$fieldSaved){
	 			$this->create_custom_fields($post_id, $fieldvar, $value);
	 		}

	 		// pluggable hook
	 		do_action('evoics_save_event_custom_data', $post_id, $post_data, $field);

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
				
				$proper_time = eventon_get_unix_time($date_array, 'm/d/Y');
				
				// save required start time variables
				$this->create_custom_fields($post_id, 'evcal_srow', $proper_time['unix_start']);
				$this->create_custom_fields($post_id, 'evcal_erow', $proper_time['unix_end']);		
			}

		// event repeat information
			if(isset( $post_data['repeat_freq'])){
				$this->create_custom_fields($post_id, 'evcal_rep_freq', $post_data['repeat_freq'] );	
				$this->create_custom_fields($post_id, 'evcal_repeat', 'yes');	
			}
			if(isset( $post_data['repeat_gap'])){
				$this->create_custom_fields($post_id, 'evcal_rep_gap', (int)$post_data['repeat_gap'] );
			}
			if(isset( $post_data['repeat_num'])){
				$this->create_custom_fields($post_id, 'evcal_rep_num', (int)$post_data['repeat_num'] );
			}


	
	 	// event location fields
	 		if( !empty($post_data['evcal_location_name']) ){

	 			$termName = esc_attr(stripslashes($post_data['evcal_location_name']));

	 			$term = term_exists( $termName, 'event_location');
	 			if($term !== 0 && $term !== null){
	 				// assign location term to the event		 			
	 				wp_set_object_terms( $post_id, $termName, 'event_location');		
	 			}else{
	 				$term_slug = str_replace(" ", "-", $termName);

					// create wp term
					$newterm = wp_insert_term( $termName, 'event_location', array('slug'=>$term_slug) );

					if(!is_wp_error($newterm)){
						$term_meta = array();
						$termID = (int)$newterm['term_id'];

						// generate latLon
						if(isset($post_data['evcal_location_name']))
							$latlon = eventon_get_latlon_from_address($post_data['evcal_location_name']);

						// latitude and longitude
						$term_meta['location_lon'] = (!empty($_POST['evcal_lon']))? $_POST['evcal_lon']:
							(!empty($latlon['lng'])? floatval($latlon['lng']): null);
						$term_meta['location_lat'] = (!empty($_POST['evcal_lat']))? $_POST['evcal_lat']:
							(!empty($latlon['lat'])? floatval($latlon['lat']): null);
						
						$term_meta['location_address'] = $termName;

						//update_option("taxonomy_".$termID, $term_meta);
						evo_save_term_metas('event_location', $termID, $term_meta);
												
						wp_set_object_terms( $post_id,  $termID , 'event_location', false);					
					}
	 			}

	 			// set location generation to yes
	 			$this->create_custom_fields( $post_id, 'evcal_gmap_gen', 'yes');
	 		}
		
		// UID field if passed
			if(!empty($post_data['UID']) ){
				$this->create_custom_fields($post_id, '_evoics_uid', $post_data['UID']);	
			}

	 	// Pluggable filter
	 		do_action('evoics_save_additional_data', $post_id, $post_data);
	}



// process fetched event data
	function process_fetched_events($events_array){
		if(sizeof($events_array)==0) return false;

		$imported_events = $this->get_imported_event_ids();

		$import_past = EVO()->cal->get_prop('evoics_import_past','evoics_1');

		$data = array();
		foreach($events_array as $index=>$event){

			$data[$index] = $this->process_fetched_data($event);

			// event Unique ID
				$event_uid = false;
				if( isset($event['UID'])) $event_uid = $event['UID'];

				if(!$event_uid){
					$event_uid = rand(100000,999999);
				}

			// discard past events
				if( $import_past != 'yes' && isset($data[$index]['event_start_unix'])){
					if( $data[$index]['event_start_unix'] < EVO()->calendar->current_time)
						continue;
				}

			// status
			if(is_array($imported_events) && in_array($event_uid, $imported_events)){
				$site_event_id = array_search($event_uid, $imported_events);
				$data[$index]['status'] = 'as';
				$data[$index]['imported_event_id'] = $site_event_id;
			}else{
				$data[$index]['status'] = 'ss';
			}

			// validate required fields
			if( empty($data[$index]['event_start_date'])){
				$data[$index]['log'] = 'Start date missing';
				$data[$index]['status'] = 'err';
			}
		}

		return $data;
	}

	function process_fetched_data($ics_data){

		//print_r($ics_data);
		// defaults
			$ics_data['evcal_allday'] ='no';
			
			$timezone_method = evo_settings_check_yn($this->options,'evoics_timezone_method');
			$alldayADJ = evo_settings_check_yn($this->options,'EVOICS_auto_allday_dis');
			$WPtimezone = get_option( 'timezone_string');
				$WPtimezone = (empty($WPtimezone)? false:$WPtimezone );

		// event date validation
			if(!empty($ics_data['DTSTART'])){

				$dt = $ics_data['DTSTART'];

				// alternate timezones
				if($timezone_method =='wp' && $WPtimezone){
					$dt->setTimeZone( new DateTimezone($WPtimezone) );				
				}elseif($timezone_method =='utc' ){
					$dt->setTimeZone( new DateTimezone( 'UTC') );
				}

				
				$event_start_date_val= $dt->format('m/d/Y');
				$event_start_time_val= $dt->format('g:i:a');

				$ics_data['event_start_unix'] = $dt->format('U');
			}else{ 
				$event_start_date_val =null;	
				$event_start_time_val =null;
			}
		
		// End time
			if(!empty($ics_data['DTEND'])){
				$dt = $ics_data['DTEND'];
				
				// alternate timezones
				if($timezone_method =='wp' && $WPtimezone){
					$dt->setTimeZone( new DateTimezone($WPtimezone) );
				}elseif($timezone_method =='utc' ){
					$dt->setTimeZone( new DateTimezone( 'UTC') );
				}

				$event_end_date_val = $dt->format('m/d/Y');
				$event_end_time_val = $dt->format('g:i:a');
			}else{ 
				$event_end_time_val =$event_start_time_val;	
				$event_end_date_val = $event_start_date_val;
			}		

		// repeating data
			if( isset($ics_data['RRULE'])){
				if( isset($ics_data['RRULE']['FREQ'] )) $ics_data['repeat_freq'] = strtolower($ics_data['RRULE']['FREQ']);

				if( isset($ics_data['RRULE']['INTERVAL'] ))
					$ics_data['repeat_gap'] = $ics_data['RRULE']['INTERVAL'];

				if( isset($ics_data['RRULE']['COUNT'] ))
					$ics_data['repeat_num'] = $ics_data['RRULE']['COUNT'];

				if( isset($ics_data['RRULE']['UNTIL'] )){
					$dt = $ics_data['RRULE']['UNTIL'];

					// alternate timezones
					if($timezone_method =='wp' && $WPtimezone){
						$dt->setTimeZone( new DateTimezone($WPtimezone) );
					}elseif($timezone_method =='utc' ){
						$dt->setTimeZone( new DateTimezone( 'UTC') );
					}

					$ics_data['repeat_until'] = $dt->format('U');
				}
			}
							
		// description
			$event_description = (!empty($ics_data['DESCRIPTION']))? 
				html_entity_decode(convert_chars(addslashes($ics_data['DESCRIPTION'] ))): 
				null;
			// /$event_description = $ics_data['DESCRIPTION'];

		// Auto detect all day event
			if($event_start_time_val == '12:00:am' && $event_end_time_val =='12:00:am' && !$alldayADJ)
				$ics_data['evcal_allday'] = 'yes';


		// adjust all day event end date back once date
			if($ics_data['evcal_allday'] == 'yes'){
				$dt->modify('-1 day');

				$event_end_date_val = $dt->format('m/d/Y');
			}
		
		$ics_data['event_start_date'] = $event_start_date_val;
		$ics_data['event_start_time'] = $event_start_time_val;
		$ics_data['event_end_date'] = $event_end_date_val;
		$ics_data['event_end_time'] = $event_end_time_val;
		$ics_data['event_description'] = $event_description;

		// event name
			$eventName = (!empty($ics_data['SUMMARY']))?
				html_entity_decode($ics_data['SUMMARY']):	$event_start_date_val;
			$ics_data['event_name'] = $eventName;

		// Location 
			if(!empty($ics_data['LOCATION']))
				$ics_data['evcal_location_name'] = $ics_data['LOCATION'];

		// pluggable
			$ics_data = apply_filters('evoics_additional_data_validation', $ics_data);


		return $ics_data;
	}

// upload a file
	function upload_file($url, $event_name='', $file_type='image'){
    	if($file_type =='image'){
    		$preg = '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i';
    		$desc="Featured image for '$event_name'";
    	}else{
    		$preg = '/[^\?]+\.(ics)\b/i';
    		$desc="ICS File for importing events on ". date('Y-m-d', time());
    	}

		if(empty($url))	return false;

    	// Download file to temp location
	      $tmp = download_url( $url );

	      // Set variables for storage
	      $preg = preg_match( $preg, $url, $matches );

	      if(!$preg) return false;

	      $file_array['name'] = basename($matches[0]);
	      $file_array['tmp_name'] = $tmp;

	      // If error storing temporarily, unlink
	      if ( is_wp_error( $tmp ) ) {
	         @unlink($file_array['tmp_name']);
	         $file_array['tmp_name'] = '';
	      }

	      // do the validation and storage stuff
	      $post_id=0;
	      
	      $id = media_handle_sideload( $file_array, $post_id, $desc );
	      
	      // If error storing permanently, unlink
	      if ( is_wp_error($id) ) {
	         @unlink($file_array['tmp_name']);
	         return false;
	      }

	      $src = wp_get_attachment_url( $id );
	      return array(0=>$id,1=>$src);
    	
    }

// get list of already imported events
    function get_imported_event_ids(){

        $events = new WP_Query(array(
            'post_type'=>'ajde_events',
            'posts_per_page'=>-1,
            'meta_key'=>'_evoics_uid',
            'post_status' => array(
                'publish', 
                'pending', 
                'draft', 
                'auto-draft'
            ) 
        ));

        $imported = array();
        if(!$events->have_posts())  return false;

        while($events->have_posts()): $events->the_post();              
            $UID = get_post_meta($events->post->ID,'_evoics_uid',true);
            if(!empty( $UID))  $imported[$events->post->ID] = $UID;
        endwhile;
        wp_reset_postdata();

        return $imported;           
    }

// Supported variable names for event post meta values 
	function get_all_fields(){
		$fields =  array(
			'event_name',
			'evcal_location_name',
			'evcal_allday',
			'event_start_date',
			'event_start_time',
			'event_end_date',
			'event_end_time',
			'event_description',
			'UID',
			'imported_event_id'
		);
		
		// pluggable hook for additional fields
			$fields = apply_filters('evoics_additional_ics_fields', $fields);

		return $fields;
	}

// guidelines for ICS file
	function print_guidelines(){
		global $eventon, $evoics;
		
		ob_start();
		
		require_once($evoics->plugin_path.'/guide.php');
		
		$content = ob_get_clean();
		
		echo $eventon->output_eventon_pop_window( 
			array('content'=>$content, 'title'=>'How to use ICS Importer', 'type'=>'padded')
		);
		?>					
			<h3><?php _e('**ICS file guidelines','eventon')?></h3>
			<p><?php _e('Please read the below guide for proper .ICS file that is acceptable with this addon. Along with this addon, in the main addon file folder you should find a <b>sample.ics</b> file that can be used to help guide for creation of ics file.','eventon');?></p>
			<a type='submit' name='' id='eventon_ics_guide_trig' class=' ajde_popup_trig btn_secondary evo_admin_btn'><?php _e('Guide for ICS File','eventon');?></a>

		<?php
	}
}