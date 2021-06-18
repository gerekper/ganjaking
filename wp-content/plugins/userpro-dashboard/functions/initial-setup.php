<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UPDBIntialSetup' ) ) :
	class UPDBIntialSetup{
		
		function __construct(){
			
			$this->setup_pages();
		}
		
		function setup_pages(){
			global $userpro;
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			$pages = get_option('userpro_pages');
			$slug_dashboard = $updb_default_options->updb_get_option( 'slug_dashboard' );
			$slug = userpro_get_option('slug');
			if ( !isset( $pages['dashboard'] ) ){

				$dashboard = array(
				  'post_title'  		=> __('Dashboard','userpro-dashboard'),
				  'post_content' 		=> '[userpro_dashboard]',
				  'post_name'			=> $slug_dashboard,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
				);
				
				$dashboard = wp_insert_post( $dashboard );
				$pages['dashboard'] = $dashboard;
				$post = get_post( $dashboard, ARRAY_A );
				$updb_default_options->updb_set_option( 'slug_dashboard', $post['post_name'] );
				update_option('userpro_pages', $pages);
			}
		}
	}
	new UPDBIntialSetup();
endif;
