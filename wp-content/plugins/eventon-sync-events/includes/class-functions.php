<?php
/**
 * Functions for both front and backend
 * @version   1.0.2
 */
class evosy_functions{
	function __construct(){
		$this->options = get_option('evcal_options_evosy_1');
	}

    // External source abbreviated text
        function source_abbre_name($source){
            $abbr = $this->sources();
            return !empty($abbr[$source])? $abbr[$source]: false;
        }
        function sources(){
            return apply_filters('evosy_sources', array(
                'facebook'=>'fb',
                'google'=>'gg'
            ));
        }

    // GET Sources
        function get_sources($source){
            $output = array();
            $status = 'bad';
            if($source == 'facebook'){
                // pages
                if( !empty($this->options['evosy_fb_uids'])){
                    $pages = str_replace(' ', '', $this->options['evosy_fb_uids']);
                    foreach( explode(',', $pages) as $page){
                        $output[] = array(
                            'id'=>$page,    'type'=>'page'
                        );
                    }
                    $status = 'good';
                }

                // event ids
                if( !empty($this->options['evosy_fb_eventids'])){
                    $pages = str_replace(' ', '', $this->options['evosy_fb_eventids']);
                    foreach( explode(',', $pages) as $page){
                        $output[] = array(
                            'id'=>$page,    'type'=>'id'
                        );
                    }
                    $status = 'good';
                }
            }elseif($source=='google'){
                for($x =1; $x <= apply_filters('evosy_google_profiles',5); $x++){
                    if(empty($this->options['evosy_gg_calid'.$x])) continue;

                    $profile = str_replace(' ', '', $this->options['evosy_gg_calid'.$x]);

                    $output[] = array(
                        'id'=>$profile, 'type'=>'profile'
                    );
                }

                if(sizeof($output)>0) $status = 'good';
            }

            return array(
                'output'=>$output,
                'status'=>$status
            );
        } 

    // Fetch events from stream
    // @stream array
        function fetch_events_stream($source, $stream){
            $fetched_events = array();
            $status = 'good';
            $message = '';

            // FACEBOOK
            if($source == 'facebook'){
                global $eventon_sy;

                include_once($eventon_sy->plugin_path.'/includes/admin/class-facebook.php');
                //require_once($eventon_sy->plugin_path.'/includes/admin/facebook-loader.php');

                $facebook = new evosy_facebook();

                $options = get_option('evcal_options_evosy_1');

                $fb_app_id = $options['evosy_fb_appid'];
                $fb_secret = $options['evosy_fb_secret'];

                // facebook pages
                if($stream['type'] == 'page'){
                    if(!empty($stream['id'])){
                        $fetched_events = $facebook->get_fb_events($fb_app_id, $fb_secret, array($stream['id']) );
                        if(!is_array($fetched_events)){ 
                            $status = 'bad';
                            $message = $fetched_events;
                        }
                    }else{
                        $message = __('Missing source item ID!','eventon');
                        $status = 'bad';
                    }
                }else{ // ID
                    if(!empty($stream['id'])){
                        $fetched_events = $facebook->get_fb_event($fb_app_id, $fb_secret, $stream['id'] );
                        if(!is_array($fetched_events)){ 
                            $status = 'bad';
                            $message = $fetched_events;
                        }
                    }else{
                        $message = __('Missing source item ID!','eventon');
                        $status = 'bad';
                    }
                }
            }

            // GOOGLE
            if($source == 'google'){
                if(!empty($stream['id'])){
                    $google = new evosy_google();
                    $fetched_events = $google->get_events( $stream['id'] );
                    if(!is_array($fetched_events)){ 
                        $status = 'bad';
                        $message = $fetched_events;
                    }
                }else{
                    $message = __('Missing source item ID!','eventon');
                    $status = 'bad';
                }                
            }

            return array(
                'events'=>$fetched_events,
                'message'=>$message,
                'status'=>$status
            );
        }

    // Fetched events table
    function fetched_events_table_fields(){
        return array(
            'status'=>__('Status','eventon'),
            'name'=>__('Event Name','eventon'),
            'start_time'=>__('Start Time','eventon'),
            'end_time'=>__('End Time','eventon'),
            'event_times'=>__('Repeat Times','eventon'),
            'description'=>__('Description','eventon'),
            'location'=>__('Location','eventon'),
            'other'=>__('Other Data','eventon')
        );
    }

    // pre process the fetched events from external source to eventon accepted data array
    function pre_process_fetched_events($events_data, $source, $when = 'cron'){
        $processed_events = array();

        $saved_events = $this->get_imported_event_ids($source);

        //print_r($events_data);

        foreach($events_data as $index=>$event_data){
            $ext_event_id = $event_data['id'];

            $processed_events[$ext_event_id]['source'] = $source;            

            foreach($this->fetched_events_table_fields() as $key=>$name){  
                switch ($key){
                    case 'status':
                        $processed_events[$ext_event_id]['status'] = (in_array($ext_event_id, $saved_events))? 'as':'ss';
                        if(in_array($ext_event_id, $saved_events))
                            $processed_events[$ext_event_id]['importedid'] = array_search($ext_event_id, $saved_events);
                    break;
                    case 'location':
                        if(!empty($event_data['location']) || !empty($event_data['location_address'])){
                            $base_location_value = (!empty($event_data['location'])? $event_data['location']:
                                ( !empty($event_data['location_address'])? $event_data['location_address']: 'na') );

                            $processed_events[$ext_event_id]['location'] = htmlentities($base_location_value);

                            foreach(array(
                                'location_address','location_lat','location_lon'
                            ) as $key){
                                if(empty($event_data[$key])) continue;
                                $processed_events[$ext_event_id][$key] =$event_data[$key];
                            }
                        }
                    break;
                    case 'description':
                        if(!empty($event_data['description']) ){
                            $short_desc = eventon_get_normal_excerpt($event_data['description'], 10);
                            $processed_events[$ext_event_id]['description'] = $event_data['description'];
                            $processed_events[$ext_event_id]['short_description'] = $short_desc .'..';
                        }
                    break;
                    case 'event_times':
                        if( !isset($event_data['event_times'])) break;
                        if( is_array($event_data['event_times'])){
                            $html = '';
                            $index = 1;

                            $UTC = new DateTimeZone("UTC");
                            $event_times = array_reverse($event_data['event_times']);

                            foreach($event_times as $times){
                                if(is_object($times ) && $times->start_time && $times->end_time){

                                    $DATEFORMAT = ($times->start_time>10)? 'Y-m-d\TH:i:sO': 'Y-m-d';
                                    $start = DateTime::createFromFormat($DATEFORMAT, $times->start_time);
                                        $start_unix = $start->format('U');
                                    $end = DateTime::createFromFormat($DATEFORMAT, $times->end_time);
                                        $end_unix = $end->format('U');

                                    $processed_events[$ext_event_id]['event_meta']['repeat_intervals'][$index][0] = $start_unix;
                                    
                                    $processed_events[$ext_event_id]['event_meta']['repeat_intervals'][$index][1] = $end_unix;
                                    

                                    if( $when != 'cron'){
                                        $processed_events[$ext_event_id]['event_meta']['repeat_intervals'][$index]['start'] = $times->start_time;
                                        $processed_events[$ext_event_id]['event_meta']['repeat_intervals'][$index]['end'] = $times->end_time;
                                    }
                                    $index++;
                                }                                
                            }

                            // enable repeat for this event
                            $processed_events[$ext_event_id]['event_meta']['evcal_repeat'] = 'yes';
                            $processed_events[$ext_event_id]['event_meta']['evcal_rep_freq'] = 'custom';
                        }
                    break;
                    default:
                        if(!empty($event_data[$key]) ){
                            $processed_events[$ext_event_id][$key] = $event_data[$key];
                        }
                    break;
                }
            }

            // other fetched event data
            foreach($event_data as $edk=>$edv){
                if( in_array($edk, array('event_times'))) continue;
                if( !array_key_exists($edk, $processed_events[$ext_event_id])){
                    $processed_events[$ext_event_id][$edk] = $edv;
                }
            }
        }

        return $processed_events;
    }

    function process_fetched_events_data_array($events, $source = 'facebook', $source_id){
        if(sizeof($events)==0 || !$events || !is_array($events)) return false;

        $saved_events = $this->get_imported_event_ids($source);

        //print_r($events);
        $_events = $this->pre_process_fetched_events($events, $source,'manual');
        //print_r($_events);

        global $ajde;

        ob_start();
        $ajde->wp_admin->table_row(array(), array(
            'colspan'=>'all',
            'colspan_count'=>8,
            'content'=> "<span class='evosy_row_caption'><b>".__('Events for Item','eventon').":</b> {$source_id}</span>"
        ));

        foreach($_events as $event_id=>$event_data){

            //print_r($event_data);

            $data = array();
            foreach($this->fetched_events_table_fields() as $key=>$name){  
                
                $event_id = $event_data['id'];
                
                switch($key){
                    case 'status':
                        if( isset($event_data['status']) && $event_data['status'] == 'as' && isset($event_data['importedid']) ){
                            $data[$key] = "<input type='hidden' name='status' value='as'/>
                                <input type='hidden' name='importedid' value='".$event_data['importedid'] ."'/><span class='imported'><a href='".get_edit_post_link($event_data['importedid'])."' target='_blank'>".__('Imported','eventon'). "</a></span>";
                        }else{
                             $data[$key] = "<input type='hidden' name='status' value='ss'/><span class='selected status_check'></span>";
                        }
                    break;
                    case 'location':
                        if(!empty($event_data['location']) || !empty($event_data['location_address'])){

                            $data_string = $event_data['location'] . "<input type='hidden' name='location' value=\"". htmlentities( $event_data['location'] ) ."\"/>";

                            foreach(array(
                                'location_address','location_lat','location_lon'
                            ) as $key){
                                $data_string .= (!empty($event_data[$key])? 
                                    "<input type='hidden' name='{$key}' value='{$event_data[$key]}'/>":'' );
                            }

                            $data['location'] = $data_string;
                        }else{
                            $data[$key] = 'na';
                        }
                    break;

                    case 'description':
                        if(!empty($event_data[ 'description' ]) ){
                            $data[$key] = (isset($event_data[ 'short_description' ] ) ? ($event_data[ 'short_description' ] .'..') :'') . 
                                "<textarea style='display:none' type='hidden' name='{$key}'>{$event_data[ 'description' ]}</textarea>";
                        }else{$data[$key] = 'na';}
                    break;

                    case 'event_times':
                        if( !isset($event_data[ 'event_meta'])){ $data[$key] = 'na'; break;}
                        if( !isset($event_data[ 'event_meta']['repeat_intervals'])){ $data[$key] = 'na'; break;}
                        if( !is_array($event_data[ 'event_meta']['repeat_intervals'])){ $data[$key] = 'na'; break;}
                        
                        // repeat event with other repeat event dates
                        $html = '';
                        foreach($event_data[ 'event_meta']['repeat_intervals'] as $ri=>$ri_data){
                            $html .= $ri_data['start'] . "<input type='hidden' name='event_meta][repeat_intervals][{$ri}][0' value='{$ri_data[0]}'/>";
                            $html .= ' - '.$ri_data['end'] . "<input type='hidden' name='event_meta][repeat_intervals][{$ri}][1' value='{$ri_data[1]}'/><br/>";
                        }

                        // enable repeat for this event
                        $html .= "<input type='hidden' name='event_meta][evcal_repeat' value='yes'/>";
                        $html .= "<input type='hidden' name='event_meta][evcal_rep_freq' value='custom'/>";

                        $data[$key] = $html;

                    break;

                    default:
                        if(!empty($event_data[$key]) ){
                            $html = $event_data[$key];
                            $html .= '<input type="hidden" name="'. $key .'"  value="'. htmlentities($event_data[$key]) .'"/>';                            
                            $data[$key] = $html;
                        }else{    $data[$key] = 'na';    }
                    break;
                }               
            }   

            $other_data = '';
            foreach($event_data as $key=>$value){
                if(array_key_exists($key, $data)) continue;

                if( is_array($value)) continue;

                if(in_array($key, array('location_address','location_lat','location_lon'))) continue;
                if(is_object($value )) continue;

                $other_data .= "<input type='hidden' name='{$key}' value='{$value}'/>";
                $other_data .= "<span><b title='{$value}'>".strtoupper($key)."</b></span> ";
            }

            // other additions
            $other_data .= "<input type='hidden' name='source' value='{$source}'/>";
            $other_data .= "<input type='hidden' name='stream_id' value='{$source_id}'/>";
                

            if(!empty($other_data)) $data['other'] = $other_data;
            $ajde->wp_admin->table_row($data);
        }

        return ob_get_clean();
    }

    // create new event post
        function create_post($data){
            $options = $this->options;

            $source = $data['source']=='facebook'? 'fb':'gg';

            $opt_draft = (!empty($options['evosy_post_status_'.$source]))?$options['evosy_post_status_'.$source]:'draft';        
            $type = 'ajde_events';
            $valid_type = (function_exists('post_type_exists') &&  post_type_exists($type));

            if (!$valid_type) {
                $this->log['error']["type-{$type}"] = sprintf(
                    'Unknown post type "%s".', $type);
            }

            // check if same event name exists already
            $event_name_check = !empty($options['evosy_disnamecheck_'.$source]) && $options['evosy_disnamecheck_'.$source] =='yes' ? false: true;
            if($event_name_check){
                $event_exists = $this->event_exists(stripslashes($data['name']));
                if( $event_exists && $event_exists >0) return $event_exists;
            }
                

            $new_post = array(
                'post_title'   => convert_chars(stripslashes($data['name'])),
                'post_content' => (!empty($data['description'])? wpautop(convert_chars(stripslashes($data['description']))): ''),
                'post_status'  => $opt_draft,
                'post_type'    => $type,
                'post_name'    => sanitize_title($data['name']),
                'post_author'  => $this->get_author_id(),
            );
           
            // create!
            $id = wp_insert_post($new_post);
           
            return $id;
        }

        function get_author_id() {
            $current_user = wp_get_current_user();
            return (($current_user instanceof WP_User)) ? $current_user->ID : 0;
        }
        function create_custom_fields($post_id, $field, $value, $type='add') {       
            if($type=='add')
                add_post_meta($post_id, $field, $value);
            else
                update_post_meta($post_id, $field, $value);
        }
        function event_exists($event_name){
            global $wpdb;

            $query = "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_status='publish' ";
            $args = array();
            $post_title = wp_unslash( sanitize_post_field( 'post_title', $event_name, 0, 'db' ) );

            if ( !empty ( $event_name ) ) {
                $query .= ' AND post_title = %s';
                $args[] = $post_title;
            }
            if ( !empty ( $args ) )
                return (int) $wpdb->get_var( $wpdb->prepare($query, $args) );
         
            return 0;
        }

    // save event custom post meta data
        function save_event_post_data($post_id, $eventdata, $type='add'){

            if(empty($post_id)) return false;

            //print_r($eventdata);

            // foreach passed direct event_meta values
            if( isset($eventdata['event_meta']) && is_array($eventdata['event_meta'])){
                foreach($eventdata['event_meta'] as $field=>$value){
                    if(empty($value)) continue;
                    $this->create_custom_fields($post_id, $field, $value, $type);
                }
            }

            // Source attribute 
                $s_attr = $this->source_abbre_name($eventdata['source']);

            // save event time
                $event_time = $this->get_event_dates($eventdata, $post_id);
                if(!empty($event_time)){                        
                    // save required start time variables
                    $this->create_custom_fields($post_id, 'evcal_srow', $event_time['unix_start'], $type);
                    $this->create_custom_fields($post_id, 'evcal_erow', $event_time['unix_end'], $type);
                    if($event_time['allday']=='yes')
                        $this->create_custom_fields($post_id, 'evcal_allday', 'yes', $type);
                }                   

            // event image - check if image is set to import
                if(!empty($eventdata['event_picture_url']) && 
                    !evo_settings_check_yn($this->options, 'evosy_img_'.$s_attr)
                ){
                    $img = $this->upload_image($eventdata['event_picture_url'], $eventdata['name'], $post_id);
                    if($img && is_array($img)){
                        $thumbnail = set_post_thumbnail($post_id, $img[0]);
                    }
                }

            // event location
                if(isset($eventdata['location']) || isset($eventdata['location_address'])){
                    $slug = isset($eventdata['location'])? $eventdata['location']: (isset($eventdata['location_address'])? $eventdata['location_address']: false);
                    if($slug){
                        $this->assign_event_taxonomies('event_location',$post_id, $eventdata, $slug);
                        // set to generate map
                        $this->create_custom_fields($post_id, 'evcal_gmap_gen','yes');
                    }
                }

            // Organizer
                if(!empty($eventdata['organizer'])){
                    $this->assign_event_taxonomies('event_organizer',$post_id, $eventdata,$eventdata['organizer']);
                }

            // learn more link
                if(isset($eventdata['link'])){
                    $this->create_custom_fields($post_id, 'evcal_lmlink', $eventdata['link'], $type);
                }

                // override learn more link with ticket uri, if available
                if( evo_settings_check_yn($this->options,'evosy_tix_uri_override_fb') && isset($eventdata['ticket_uri']) ){
                    $this->create_custom_fields($post_id, 'evcal_lmlink', $eventdata['ticket_uri'], $type);
                }

            // Assign event type default if set
                for($x=1; $x<=2; $x++){
                    if(evo_settings_check_yn($this->options, "evosy_default_ett{$x}_{$s_attr}") && !empty($this->options["evosy_val_ett{$x}_{$s_attr}"])
                    ){
                        $ab = ($x==1)? '':'_'.$x;
                        $tax = 'event_type'.$ab;
                        $this->assign_event_taxonomies($tax, $post_id, $eventdata, $this->options["evosy_val_ett{$x}_{$s_attr}"]);
                    }
                }
        }
    // Update event description
        function update_event_description($event){
            if(!empty($event['importedid']) && get_post_status( $event['importedid'] )  ){
                $description = !empty($event['description'])? $event['description']:'';
                $my_post = array(
                    'ID'           => $event['importedid'],
                    'post_content' => $description,
                    'post_title'   => $event['name']
                );
                wp_update_post( $my_post );
            }
        }

    // assign taxonomy to event
        function assign_event_taxonomies($taxonomy, $event_id, $event_data, $slug){
            // base name
            if(empty($slug)) return false;

            $base_name = esc_attr(stripslashes($slug));
            $term = term_exists($base_name, $taxonomy);

            // if term doesnt exist
            if($term !== 0 && $term !== null){
                wp_set_object_terms($event_id, $base_name, $taxonomy);  
            }else{
                $slug = str_replace(' ', '-', $base_name);
                $newterm = wp_insert_term(
                    $base_name, // the term 
                    $taxonomy, // the taxonomy
                    array(  'slug'=>$slug   )
                );
                if(!is_wp_error($newterm)){
                    $termID = (int)$newterm['term_id'];

                    if($taxonomy =='event_location'){
                        $term_meta = $latlon = $cord = array();
                        // generate coordinates for address
                            $address = !empty($event_data['location_address'])?
                                $event_data['location_address']: ( !empty($event_data['location'])? $event_data['location']: false);
                            if($address){
                                $latlon = eventon_get_latlon_from_address($address);
                                $term_meta['location_address'] = $address;
                            }
                
                        // longitude
                        $term_meta['location_lon'] = (!empty($event_data['location_lon']))? $event_data['location_lon']:
                            (!empty($latlon['lng'])? floatval($latlon['lng']): null);

                        // latitude
                        $term_meta['location_lat'] = (!empty($event_data['location_lat']))? $event_data['location_lat']:
                            (!empty($latlon['lat'])? floatval($latlon['lat']): null);

                        evo_save_term_metas('event_location', $termID, $term_meta);
                    }

                    wp_set_object_terms($event_id, $termID, $taxonomy, true);
                }
            }
        }

    // Get an array of imported event post IDS for given source
        function get_imported_event_ids($source='facebook'){
            $sourceindex = $this->source_abbre_name($source);

            $events = new WP_Query(array(
                'post_type'=>'ajde_events',
                'posts_per_page'=>-1,
                'meta_key'=>'evosy_'.$sourceindex,
                'post_status' => array(
                    'publish', 
                    'pending', 
                    'draft', 
                    'auto-draft'
                ) 
            ));

            $imported = array();
            if(!$events->have_posts())  return $imported;

            while($events->have_posts()): $events->the_post();              
                $sy_fb = get_post_meta($events->post->ID,'evosy_'.$sourceindex,true);
                if(!empty( $sy_fb))
                    $imported[$events->post->ID] = $sy_fb;
            endwhile;
            wp_reset_postdata();

            return $imported;           
        }

    // delete already synced events if deleted on the source
        function delete_synced_events($events){
            $already_imported = $this->get_imported_event_ids('google');

            $deleted = 0;
            foreach($already_imported as $event_id=>$data){
                // if the already imported event is not in events array from fetched events from source
                if( !in_array($event_id, $events)){
                    $event_id = (int) $event_id;

                    $results = wp_trash_post( $event_id );

                    if($results) $deleted++;
                }
            }

            // return the deleted events count
            return $deleted;
        }

    // return UNIX start and end times in array
        function get_event_dates($eventdata, $post_id=''){

            if(empty($eventdata['start_time'])) return false;

            global $eventon_sy;
            $debug = '';
            
            // START TIME
                $Stime = $this->process_time($eventdata['start_time'],'start', $eventdata['source']);

            // END TIME
                if(!empty($eventdata['end_time'])){
                    $Etime = $this->process_time($eventdata['end_time'],'end',$eventdata['source']);
                }else{
                    $Etime = $Stime;
                }
                        
            return array(
                'unix_start'=>$Stime['unix'],
                'unix_end'=>$Etime['unix'],
                'allday'=>$Etime['allday'],
            );
            //return eventon_get_unix_time($date_array, 'Y-m-d');
        }

    // upload and return event featured image
        function upload_image($url, $event_name, $event_id){
            if(empty($url))   return false;

            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');

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
              
              $desc="Featured image for '$event_name'";
              $id = media_handle_sideload( $file_array, $event_id, $desc );
              // If error storing permanently, unlink
              if ( is_wp_error($id) ) {
                 @unlink($file_array['tmp_name']);
                 return false;
              }

              $src = wp_get_attachment_url( $id );
              return array(0=>$id,1=>$src);

        }


	// properly split and return unix time
    	function process_time($timestring, $type='start', $source='facebook'){
    		// has time
    		$output = array(
    			'unix'=>'','allday'=>'no'
    		);

            // Source attribute 
                $s_attr = ($source =='facebook') ? 'fb':'gg';

    		// time string found
    		if(strpos($timestring, 'T') !== false){
    			$time = explode('T', $timestring);

    			// timezone found
    			if(strpos($time[1], '-') !== false){
    				$timeString = explode('-', $time[1]);
    				$timezone = $timeString[1];
    				$clock = $timeString[0];
    			}else{// no timezone
    				$clock = $time[1];
    			}
    			$clock = explode(':', $clock);
                //print_r($clock);

    			$date = $time[0];
    			$day = explode('-', $date);

    			// set to adjust time based on timezone provided
    			if(evo_settings_check_yn($this->options, 'evosy_adj_timezone_'.$s_attr) ){
    				$timezone = explode(':', $timezone);
    				$output['unix'] = mktime($clock[0] +$timezone[0], $clock[1]+$timezone[1], 0, $day[1], $day[2], $day[0]);
    			}else{
    				date_default_timezone_set('UTC');
    				$output['unix'] = mktime($clock[0], $clock[1], 0, $day[1], $day[2], $day[0]);
    			}

                // adjust custom offset for time
                if(evo_settings_check_yn($this->options, 'evosy_offset_'.$s_attr) && !empty($this->options['evosy_offset_time_'.$s_attr])){
                    $unix_adjust = 60 * (int)$this->options['evosy_offset_time_'.$s_attr];
                    $output['unix'] = $output['unix']+$unix_adjust;
                }

    		}else{
    			date_default_timezone_set('UTC');
    			$day = explode('-', $timestring);
    			$output['unix'] = ($type == 'end')?
    				 mktime(23, 59, 59, $day[1], $day[2], $day[0]):
    				 mktime(0, 0, 0, $day[1], $day[2], $day[0]);
    			$output['allday']='yes';
    		}

    		return $output;
    	}

	//process string ids into an array
		function process_ids($ids){
			if(empty($ids))
				return false;

			$uids = str_replace(' ', '', $ids);
			if(strpos($uids, ',')=== false){
				$uids = array($uids);
			}else{
				$uids = explode(',', $uids);
			}
			return $uids;
		}
    
    // verify nonce
        function verify_nonce_post($post_field){
            global $_POST, $eventon_sy;

            if(isset( $_POST ) && !empty($_POST[$post_field]) && $_POST[$post_field]  ){
                if ( wp_verify_nonce( $_POST[$post_field],  $eventon_sy->plugin_path )){
                    return true;
                }else{  
                    $this->log['error'][] =__("Could not verify nonce. Please try again.",'eventon');
                    $this->print_messages();
                    return false;   }
            }else{  
                $this->log['error'][] =__("Could not verify nonce. Please try again.",'eventon');
                $this->print_messages();
                return false;   
            }
        } 

    // time calculations
        function time_since($old_time, $new_time){
            $since = $new_time - $old_time;
            // array of time period chunks
            $chunks = array(
                /* translators: 1: The number of years in an interval of time. */
                array( 60 * 60 * 24 * 365, _n_noop( '%s year', '%s years', 'wp-crontrol' ) ),
                /* translators: 1: The number of months in an interval of time. */
                array( 60 * 60 * 24 * 30, _n_noop( '%s month', '%s months', 'wp-crontrol' ) ),
                /* translators: 1: The number of weeks in an interval of time. */
                array( 60 * 60 * 24 * 7, _n_noop( '%s week', '%s weeks', 'wp-crontrol' ) ),
                /* translators: 1: The number of days in an interval of time. */
                array( 60 * 60 * 24, _n_noop( '%s day', '%s days', 'wp-crontrol' ) ),
                /* translators: 1: The number of hours in an interval of time. */
                array( 60 * 60, _n_noop( '%s hour', '%s hours', 'wp-crontrol' ) ),
                /* translators: 1: The number of minutes in an interval of time. */
                array( 60, _n_noop( '%s minute', '%s minutes', 'wp-crontrol' ) ),
                /* translators: 1: The number of seconds in an interval of time. */
                array( 1, _n_noop( '%s second', '%s seconds', 'wp-crontrol' ) ),
            );

            if ( $since <= 0 ) {
                return __( 'now', 'wp-crontrol' );
            }

            // we only want to output two chunks of time here, eg:
            // x years, xx months
            // x days, xx hours
            // so there's only two bits of calculation below:

            // step one: the first chunk
            for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
                $seconds = $chunks[ $i ][0];
                $name = $chunks[ $i ][1];

                // finding the biggest chunk (if the chunk fits, break)
                if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
                    break;
                }
            }

            // set output var
            $output = sprintf( translate_nooped_plural( $name, $count, 'wp-crontrol' ), $count );

            // step two: the second chunk
            if ( $i + 1 < $j ) {
                $seconds2 = $chunks[ $i + 1 ][0];
                $name2 = $chunks[ $i + 1 ][1];

                if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
                    // add to output var
                    $output .= ' ' . sprintf( translate_nooped_plural( $name2, $count2, 'wp-crontrol' ), $count2 );
                }
            }

            return $output;
        }
}