<?php
/**
 * Admin
 * @version 0.1
 */
class evodp_admin{
	
	public function __construct(){\
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
		
	}

	// styles and scripts
		function event_post_styles(){
			
			wp_enqueue_style( 'evodp_admin_styles', EVODP()->assets_path.'DP_admin_styles.css');
			wp_enqueue_script( 'evodp_admin_post_script', EVODP()->assets_path.'DP_admin_script.js',array('jquery','jquery-ui-draggable'),  EVODP()->version);
			wp_localize_script( 
				'evodp_admin_post_script', 
				'evodp_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventondp_nonce' )
				)
			);
		}

	
}