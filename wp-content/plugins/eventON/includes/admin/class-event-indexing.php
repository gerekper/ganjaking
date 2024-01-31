<?php
/**
 *	EventON Event Indexing - in progress
 * @version 4.5.5
 */

class EVO_Event_Indexing{
	public function __construct(){

		// ajax calls
		$ajax_events = array(
			'eventon_admin_index_log'=>'index_log',	
			'eventon_admin_index_run'=>'index_run',	
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_' . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $class ) );
		}
	}

	function get_events_in_para($atts=''){
		$log = get_option('eventon_indexed_events');

		EVO()->calendar->process_arguments(array('number_of_months'=>1));
		$SC = (object) EVO()->calendar->shortcode_args;

		EVO()->calendar->shell->set_calendar_range();

		if( empty($log)) return false;

		unset( $log[0]);

		// ids of events in date range
		$in_events = array();

		$all_event_tax = EVO()->calendar->shell->get_all_event_tax();

		print_r($SC);

		foreach($log as $event_id => $data){
			// check if in date range
			$in_range = EVO()->calendar->shell->is_in_range( 
				$SC->focus_start_date_range, $SC->focus_end_date_range,
				$data[0], $data[1]
			);

			// if event is not in date range
			if( !$in_range) continue;


			$event_has_tax = count($data)> 3 ? true : false;

			$event_data = $data;

			$parent_event_id = $event_id;

			// if repeat instance, use tax data from parent event
			if( strpos($event_id, '-') !== false){
				$ee = explode('-', $event_id);

				// if parent event id of the repeat is not set in index skip this event
				if( !isset( $log[ $ee[0] ] )) continue;

				$parent_event_id = $ee[0];

				$event_has_tax = count( $log[ $parent_event_id ] )> 3 ? true: false;

				$event_data = $log[ $parent_event_id ];
			}

			// add event id to list
			$in_events[] = $parent_event_id;


			// check for tax filter
			// each tax
			foreach($all_event_tax as $id=>$taxonomy){

				// skip tax filring for value = '' and all
				if( !isset( $SC->{$taxonomy} ) ) continue;
				if( $SC->{$taxonomy} == 'all' ) continue;

				$set_terms = explode(',', $SC->{$taxonomy} );

				// parent event does not have tax data
				if( !isset($event_data[ $taxonomy ]){
					unset( $in_events[ $parent_event_id ] );
				}

				// parent event term is not equal to SC term
				//$matchin_terms = array_intersect($set_terms, array2)
				//if(  )
			}
		}


	}

	function index_log(){
		ob_start();

		$log = get_option('eventon_indexed_events');

		$manual_index_btn_data = array(
			'title'=>__('Run Manual Event Indexing','eventon'),
			'class_attr'=>'evo_admin_btn evo_trigger_ajax_run',
			'dom_element'=> 'span',
			'uid'=>'evo_admin_index_run',
			'lb_class' =>'evoadmin_index_log',
			'lb_loader'=> true,
			'lb_load_new_content'=> true,
			'load_new_content_id'=> 'evo_indexing_results',
			'ajax_data' =>array(
				'action'=>'eventon_admin_index_run'
			));

		print_r($this->get_events_in_para());

		// no logs
		if( empty($log) ){
			echo "<div>";
			echo "<p>".__('Events have not been indexed yet') . ".</p>";
			
			EVO()->elements->print_trigger_element( $manual_index_btn_data , 'trig_ajax');

			echo "</div>";
			echo "<div id='evo_indexing_results'></div>";
		}else{	

			echo "<div class='evopadb10'>";
			EVO()->elements->print_trigger_element( $manual_index_btn_data , 'trig_ajax');
			echo "</div>";

			echo "<div id='evo_indexing_results'>";

			if( isset( $log[0] ) )
				echo "<p class=''>" . __('Last indexed at: '). $log[0] . ' '.  __('Local Time') .'</p>';

			echo "<p class=''>" . __('Number of entries: '). count($log) - 1 . '</p>';

			//print_r($log);
			
			EVO()->elements->start_table_header('evo_index', array('Event ID', 'Start', 'End','Tax Data'));

			foreach( $log as $event_id=> $data){		

				if( !is_array($data)) continue;

				$taxs = count($data);	

				EVO()->elements->table_row(array(
					$event_id, $data[0], $data[1], ( $taxs>2 ? $taxs - 2 :'No')
				));
			}

			EVO()->elements->table_footer();

			echo "</div>";
		}

		//print_r($log);

		wp_send_json(array(
			'status'=>'good',
			'content'=> ob_get_clean()
		)); wp_die();
	}

	function index_run(){
		ob_start();

		echo "<div id='evo_indexing_results' class='evopadt10'>";

		$this->get_all_event_indexes();

		echo '</div>';

		wp_send_json(array(
			'status'=>'good',
			'content'=> ob_get_clean()
		)); wp_die();
	}

	function get_all_event_indexes($print_view = true){

		$time_now = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
		$data = array(0 => $time_now);

		$wp_arguments = array (
			'post_type' 		=>'ajde_events' ,
			'post_status'		=>'publish',
			'posts_per_page'	=> -1 ,
			'order'				=>'ASC',
			'orderby' 			=> 'menu_order',
			'has_password'		=> FALSE
		);

		$events = new WP_Query( $wp_arguments);	

		$count = 1;
		if ( $events->have_posts() ) :

			if($print_view) echo "<p class=''>". __('Total Events Processed') .': '. count( $events->posts) ."</p>";
			if($print_view) echo "<p class=''>".  __('Last indexed at: '). $time_now . ' '.  __('Local Time')  ."</p>";


			if($print_view) EVO()->elements->start_table_header('evo_index', array('Event ID', 'Start', 'End','Tax Data'));

			while( $events->have_posts()): $events->the_post();
				$EVENT = new EVO_Event( $events->post->ID ,'','',true, $events->post);

				if( $count == 1){
					$EVENT->get_all_taxonomies();
				}
				$count ++;

				$repeats = $EVENT->is_repeating_event(true);
				if( $repeats != false ){
					foreach($repeats as $index=>$R){
						$data[ $EVENT->ID .'-'. $index] =  array( (int)$R[0] , (int)$R[1]);
					}
				}

				$data[ $EVENT->ID ] = array( $EVENT->start_unix, $EVENT->end_unix);

				// all event tax
				if( $taxs = $EVENT->get_all_taxonomies() ){
					foreach( $taxs as $tax=>$terms){
						$data[ $EVENT->ID ][ $tax ] = $terms;
					}
				}

				if($print_view){
					EVO()->elements->table_row(array(
						$EVENT->ID, $EVENT->start_unix, $EVENT->end_unix, ( $taxs ? count($taxs) :'No')
					));
				}
				

			endwhile;

			if($print_view) EVO()->elements->table_footer();

		endif;
		wp_reset_postdata();

		update_option('eventon_indexed_events', $data);

		//print_r($data);
	}
}

new EVO_Event_Indexing();