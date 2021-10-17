<?php
/**
 * Admin
 * @version 0.1
 */
class evovo_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));		
	}

	function admin_init(){
		include_once('class-post_meta.php');

		global $pagenow, $typenow, $wpdb, $post;	
			
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) && $post){
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $typenow = get_post_type( $_GET['post'] );
	    }

		if ( $typenow == '' || $typenow == "ajde_events") {
			// Event Post Only
			$print_css_on = array( 'post-new.php', 'post.php' );
			foreach ( $print_css_on as $page ){
				add_action( 'admin_print_styles-'. $page, array($this,'event_post_styles' ));		
			}
		}

		if(defined('DOING_AJAX')){	include_once( 'class-admin-ajax.php' );		}	

		add_filter('woocommerce_hidden_order_itemmeta', array($this, 'hidden_order_items'),10,1);

		// include in addons list
		add_filter('evo_addons_details_list', array($this, 'addons_list_inclusion'), 10, 1);

		// when duplicating an event
		add_action('eventon_duplicate_product', array($this,'duplicate_event'),10,2);	
		
	}

	// include in addons list
		function addons_list_inclusion($array){
			$array['eventon-ticket-variations-options']= array(
				'id'=>'EVOVO',
				'name'=>'Variations & Options',
				'link'=>'http://www.myeventon.com/addons/variaions-options',
				'download'=>'http://www.myeventon.com/addons/variaions-options',
				'desc'=>'Extend tickets with variations and options'
			);

			return $array;
		}

	// Woocommerce Related
		function hidden_order_items($array){
			$array[]= '_ticket_var_index';
			return $array;
		}

	// styles and scripts
		function event_post_styles(){
			
			wp_enqueue_style( 'evovo_admin_styles',EVOVO()->assets_path.'evovo_admin_styles.css', '', 
				EVOVO()->version);
			wp_enqueue_script( 'evovo_admin_post_script',EVOVO()->assets_path.'evovo_admin_script.js',
				array('jquery','jquery-ui-draggable','jquery-ui-sortable'), EVOVO()->version);
			wp_localize_script( 
				'evovo_admin_post_script', 
				'evovo_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonto_nonce' )
				)
			);
		}

	// duplicating event
		function duplicate_event($new_id, $post){

			$EVENT = new EVO_Event($new_id);

			// if variations are not enabled
			if(!$EVENT->check_yn('_evovo_activate')) return false;



			foreach(array(
				'variation_type','variation','option'
			) as $method){

				// check if values saved
				if(!$EVENT->get_prop('_evovo_'.$method)) continue;

				$vo_data = $EVENT->get_prop('_evovo_'.$method);

				if(!is_array($vo_data)) continue;

				// for each variation methods
				foreach($vo_data as $index=>$data){
					// check if parent ID is old parent
					if( !isset($data['parent_id'])) continue;
					if($data['parent_id'] != $post->ID) continue;
					if( !isset($data['parent_type'])) continue;
					if( $data['parent_type'] != 'event') continue;

					$vo_data[$index]['parent_id'] = $new_id;

				}

				// save updated value
				$EVENT->set_prop('_evovo_'.$method, $vo_data);
				

			}
		}
}