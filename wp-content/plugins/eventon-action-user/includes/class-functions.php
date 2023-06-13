<?php
/**
 * ActionUser front and back end functions
 * @version  2.2.5
 */
class evoau_functions{
	public function __construct(){
		EVO()->cal->load_more('evoau_1');
	}

	// event manager related		
		// Can a event be editable
			function can_edit_event($eventid, $epmv=''){

				$opt = get_option('evcal_options_evoau_1');
				
				// does settings allow editing
				if( evo_settings_check_yn($opt,'evo_auem_editing') ){
					// check if editing allowed per each event post
					if(!empty($epmv)){						
						return evo_check_yn($epmv, 'evoau_disableEditing')? false: true;
					}else{
						$editable = get_post_meta($eventid, 'evoau_disableEditing', true);
						return (!empty($editable) && $editable=='yes')? false:true;
					}

				}else{
					return false;
				}

			}
		// can user edit event
			function can_currentuser_edit_event($event_id, $epmv=''){
				
				// if editing disabled via event
				if( !$this->can_edit_event($event_id, $epmv) ) return false;

				// if editting disabled from settings
				if( !EVO()->cal->check_yn('evo_auem_editing','evoau_1')) return false;


				global $current_user;
				$event_author = get_post_field ('post_author', $event_id);
				$event_post_statud = get_post_field ('post_status', $event_id);
				
				// user made event
				if( $event_author == $current_user->ID ){

					// user has permission to edit his published events
					return current_user_can('edit_published_eventons')? true: false;

				// others event	
				}else{

					//user assigned to event
					if( $this->user_assigned_toevent($event_id, $current_user->ID ) ){
						if( !EVO()->cal->check_yn('evoau_assigned_editing','evoau_1')) return false;
						return true;
					}else{

						// if he can edit others events via permissions
						return ( current_user_can('edit_others_eventons', $event_id) ) ? true: false;
					}
				}


				// if the user is admin// override everything
				if(current_user_can('manage_eventon')){
					return true;
				}
				return false;
			}

		// can user delete events in event manager
			function can_currentuser_delete_event($EVENT){
				$opt = get_option('evcal_options_evoau_1');
				
				// if deleting disabled via settings
				if( !evo_settings_check_yn($opt,'evo_auem_deleting') ) return false;

				global $current_user;
				$event_author = get_post_field ('post_author', $EVENT->ID);

				// user made event
				if( $event_author == $current_user->ID){

					// user has permission to delete his published events
					return current_user_can('delete_published_eventons')? true: false;

				// others event	
				}else{

					//user assigned to event
					if( $this->user_assigned_toevent($EVENT->ID, $current_user->ID ) ){
						return ( !EVO()->cal->check_yn('evoau_assigned_deleting','evoau_1')) ? false:true;
					}else{

						// if he can edit others events via permissions
						return ( current_user_can('delete_others_eventons', $EVENT->ID) ) ? true: false;
					}
				}

				
				
			}

		// check if the user ID is assigned to event
			function user_assigned_toevent($event_id, $userid){
				$r = is_object_in_term( $event_id, 'event_users', $userid );

				$saved_users = wp_get_object_terms( $event_id, 'event_users', array( 'fields'=>'slugs') );
				if(is_array($saved_users)  && !empty($saved_users)){
					if( in_array('all', $saved_users) ){
						return true;
					}else{
						$all_users = get_users();	
						foreach($all_users as $uu){
							if( in_array($uu->ID, $saved_users)){
								return true;
							}
						}
					}
				}
				return false;
			}

		// trash event
			function trash_event($eid){
				return wp_trash_post($eid);
			}
		// get url with variables added
			public function get_custom_url($baseurl, $args){
				$str = '';
				foreach($args as $f=>$v){ $str .= $f.'='.$v. '&'; }
				if(strpos($baseurl, '?')!== false){
					return $baseurl.'&'.$str;
				}else{
					return $baseurl.'?'.$str;
				}
			}

		// Get the back to event manager link
			function get_backlink($current_page_link){
				$parsed_url = parse_url($current_page_link);
				
				if( array_key_exists( 'query', $parsed_url )) {
			        $query_portion = $parsed_url['query'];
			    }else{
			    	return $current_page_link;
			    }

			    parse_str( $query_portion, $query_array );

			    $evoau_vars = apply_filters('evoau_event_manager_backlink_vars', array('action','eid'));
			    foreach($query_array as $key=>$value){
			    	if(in_array($key, $evoau_vars)){
			    		unset($query_array[$key]);
			    	}
			    }

			    $q = ( count( $query_array ) === 0 ) ? '' : '?';
			    $url =  $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'] . $q . http_build_query( $query_array );

			    return $url;
			}

// GET EVENTS
	// get paged events
		function get_paged_events($events, $atts){

			if(!$atts || empty($atts)) $atts = array();
			
			$page = (int)$atts['page'];
			$paginate = false;
			$count_start = $count_end = false;

			// if pagination
			if(isset($atts['pagination']) && $atts['pagination'] == 'yes'){
								
				$count_start = ($page==0)? 1: $atts['events_per_page'] * ($page-1)+1;
				$count_end = ($page==0)? $atts['events_per_page']:  $atts['events_per_page'] *$page;
				$paginate = true;
			}

			$count =1;
			ob_start();

			foreach($events as $event_id=>$evv){
				
				if($paginate && ($count < $count_start || $count >$count_end ) ){
					$count++;  continue;
				}

				echo $this->gethtml_event_row_event($event_id, $evv);
				$count ++;			
			}

			return ob_get_clean();
		}

		function get_next_pagination_page($atts){
			$current_page = isset($atts['page'])? $atts['page']: 1;			
			$direction = $atts['direction'];

			// if no direction
			if($direction == 'none') return $current_page;

			$all_events = $atts['total_events'];
			$events_per_page = $atts['events_per_page'];

			$max_pages = ceil($all_events/$events_per_page);

			$next_page = ($direction=='next')? $current_page+1: $current_page -1;
			$next_page = ($next_page<1)? 1: $next_page;

			if( $next_page > $max_pages) $next_page = $current_page;

			
			return $next_page;
		}

		// get event row HTML for event manager
			function gethtml_event_row_event($event_id, $data){
				ob_start();

				$evoDateTime = new evo_datetime();

				// initial values
				$DateTime = '';

				$EVENT = new EVO_Event( $event_id);

				$ePmv = $EVENT->get_data();
				$can_user_edit_event = $this->can_currentuser_edit_event($event_id, $ePmv);

				$EVENT_SUBMISSION_FORMAT = 'unpaid_submission';
				if(!empty($ePmv['_evoaup_event_type'])) $EVENT_SUBMISSION_FORMAT = 'regular';
				if(!empty($ePmv['_evoaup_submission_level'])) $EVENT_SUBMISSION_FORMAT = 'level_based';
				

				// edit button html
					$edit_html = (!$can_user_edit_event)? '':
						"<a class='fa fa-pencil editEvent' data-eid='{$event_id}' data-sformat='{$EVENT_SUBMISSION_FORMAT}'></a>";

					$edit_html = apply_filters('evoau_event_manager_edit_btn', $edit_html, $EVENT);
				
				
				// delete button html
					$delete_html = (!$this->can_currentuser_delete_event($EVENT))?
						'':"<a class='fa fa-trash deleteEvent' data-eid='{$event_id}'></a>";
					$delete_html = apply_filters('evoau_event_manager_delete_btn', $delete_html, $EVENT);

				
				$DateTime = $EVENT->get_formatted_smart_time();
				
				// if event is featured
					$feature_event_tag = evo_check_yn($ePmv, '_featured')? "<i class='fa fa-star' title='". evo_lang('Featured Event') ."'></i>":'';

				echo "<div class='evoau_manager_row evoau_em_{$event_id} ". ( $EVENT->is_past_event() ?'past':'')."' >";
				echo "<p class='event_name'>". $feature_event_tag. "<subtitle><a href='". $EVENT->get_permalink()."'>".$data[0]."</a></subtitle>";
					
				echo "</p>";
				
				echo "{$edit_html} {$delete_html}
					<p class='event_date_time'><i>".evo_lang('Date')."</i><span>{$DateTime}</span></p>
					<span class='event_info_tags'>";
						
						$ES = $EVENT->get_event_status();
						if( $ES != 'scheduled'){
							echo "<em class='event_status {$ES}'>". $EVENT->get_event_status_l18n( $ES ) ."</em>";
						}

						echo "<em class='event_poststatus status_{$data[1]}' title='".evo_lang('Status')."'>". evo_lang($data[1])."</em>";
						echo ($EVENT->is_repeating_event() ? "<em class='tag'>". evo_lang('Repeating Event')."</em>":""); 
						do_action('evoau_manager_row_title', $EVENT );
				echo "</span>";

				// pluggable
				do_action('evoau_manager_row', $EVENT,  $can_user_edit_event);

				echo "</div>";
				return ob_get_clean();
			}

		// language
			function get_lang($text, $lang='L1'){
				$lang = !empty( EVOAU()->frontend->lang)? EVOAU()->frontend->lang: $lang ;
				return evo_lang($text, $lang, '');
			}
}