<?php
/**
 * EventON Admin Functions
 *
 * Hooked-in functions for EventON related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     4.0.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// @since 2.2.24
// check if repeat post data are good to go
	function eventon_is_good_repeat_data(){
		return ( isset($_POST['evcal_rep_freq'])
			&& isset($_POST['evcal_repeat']) 
			&& $_POST['evcal_repeat']=='yes')? 	true: false;
	}


// SAVE: closed meta field boxes
	function eventon_save_collapse_metaboxes( $page, $post_value ) {
		
		if(empty($post_value)) return;
		
		$user_id = get_current_user_id();
		$option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		
		$meta_box_ids = array_unique(array_filter(explode(',',$post_value)));
		
		$meta_box_id_ar =serialize($meta_box_ids);
		
		update_user_option( $user_id, $option_name,  $meta_box_id_ar , true );
		
	}

	function eventon_get_collapse_metaboxes($page){
		
		$user_id = get_current_user_id();
	    $option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		$option_arr = get_user_option( $option_name, $user_id );
		
		if(empty($option_arr)) return;
		
		return unserialize($option_arr);
		//return ($option_arr);		
	}



// create backend pages
// @updated 2.6.1
	function eventon_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ){
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 && $p = get_post( $option_value ) ){
			return $p->ID;
		}

		$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
		if ( $page_found ) {
			if ( ! $option_value )
				update_option( $option, $page_found );
			return $page_found;
		}

		$page_data = array(
	        'post_status' 		=> 'publish',
	        'post_type' 		=> 'page',
	        'post_author' 		=> 1,
	        'post_name' 		=> $slug,
	        'post_title' 		=> $page_title,
	        'post_content' 		=> $page_content,
	        'post_parent' 		=> $post_parent,
	        'comment_status' 	=> 'closed'
	    );
	    $page_id = wp_insert_post( $page_data );
	    update_option( $option, $page_id );

	    return $page_id;
	}

// get converted unix time for saving event date time using $_POST
	function evoadmin_get_unix_time_fromt_post($post_id=''){
		// field names that pertains only to event date information
			$fields_sub_ar = apply_filters('eventon_event_date_metafields', array(
				'evcal_start_date',
				'evcal_end_date', 
				'evcal_start_time_hour',
				'evcal_start_time_min',
				'evcal_st_ampm',
				'evcal_end_time_hour',
				'evcal_end_time_min',
				'evcal_et_ampm',
				'evcal_allday',
				'event_vir_date_x',
				'_vir_hour',
				'_vir_minute',
				'_vir_ampm'
				)
			);

		// post values conversion
			$D = array(
				'event_start_date_x'=>'evcal_start_date',
				'event_end_date_x'=>'evcal_end_date',
				'_start_hour'=>'evcal_start_time_hour',
				'_start_minute'=>'evcal_start_time_min',
				'_start_ampm'=>'evcal_st_ampm',
				'_end_hour'=>'evcal_end_time_hour',
				'_end_minute'=>'evcal_end_time_min',
				'_end_ampm'=>'evcal_et_ampm',
				
				'event_vir_date_x'=>'event_vir_date_x',
				'_vir_hour'=> '_vir_hour',
				'_vir_minute'=>'_vir_minute',
				'_vir_ampm'=> '_vir_ampm'
			);

			foreach($D as $ff=>$vv){
				if(!isset( $_POST[ $ff ])) continue;

				$_POST[ $vv ] = $_POST[ $ff ];
			}

		// DATE and TIME data
			$date_POST_values = array();
			foreach($fields_sub_ar as $ff){
				
				if(empty($_POST[$ff])) continue;
				$date_POST_values[$ff]=$_POST[$ff];

				// remove these values from previously saved
				if(!empty($post_id)) delete_post_meta($post_id, $ff);
			}

		// hide end time filtering of data values
			if( !empty($_POST['evo_hide_endtime']) && $_POST['evo_hide_endtime']=='yes'){

				if(evo_settings_check_yn($_POST,'evo_span_hidden_end')){
					$date_POST_values['evcal_end_date']=$_POST['evcal_end_date'];
				}else{
					$date_POST_values['evcal_end_date']=$_POST['evcal_start_date'];
					$date_POST_values['evcal_end_time_hour'] = '11';
					$date_POST_values['evcal_end_time_min'] = '50';
					$date_POST_values['evcal_et_ampm'] = 'pm';
				}				
			}
		
		// convert the post times into proper unix time stamps
			$date_format = !empty($_POST['_evo_date_format']) ? $_POST['_evo_date_format']: get_option('date_format');
			$time_format = !empty($_POST['_evo_time_format']) ? $_POST['_evo_time_format']: get_option('time_format');

			return eventon_get_unix_time($date_POST_values, $date_format, $time_format);
	}

// LEGACY
	function print_ajde_customization_form($cutomization_pg_array, $evcal_opt=''){
		EVO()->evo_admin->settings->print_ajde_customization_form($cutomization_pg_array, $evcal_opt);
	}

?>