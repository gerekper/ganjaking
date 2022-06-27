<?php

/*
 * Adds or controls pointers for notice purpose
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UP_Admin_pointers{
	
	public function __construct(){
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_pointers' ) );
	}
	
	function setup_pointers( $screen ){
		if ( ! $screen = get_current_screen() ) {
				return;
			}
	
			switch ( $screen->id ) {
				case 'toplevel_page_userpro' :
					$this->create_pointers();
				break;
		}
	}
	
	function create_pointers(){
		$pointers = array(
			'pointers' => array(
				'up_addons' => array(
					'target'       => 'a[href="admin.php?page=userpro-addons"]',
					'options'      => array(
						'content'  => 	'<h3>' . esc_html__( 'More Features', 'woocommerce' ) . '</h3>' .
										'<p>' . __( 'Add more features to your site by installing addons . Check out <a href="admin.php?page=userpro-addons">available addons</a> for UserPro', 'userpro' ) . '</p>',
						'position' => array(
							'edge'  => 'left',
							'align' => 'left'
						)
					)
				)
			)
		);	
		
		$this->enqueue_pointers( $pointers );
	}
	
	public function enqueue_pointers( $pointers ) {
		$dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		if( !empty($dismissed) ){
			if(strstr($dismissed,'up_addons')!==false){
				unset($pointers['pointers']['up_addons']);
			}
		}
		//$pointers = wp_json_encode( $pointers );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_register_script( 'up_pointers_js', userpro_url.'admin/scripts/up_pointer_js.js','','',true );
		wp_localize_script( 'up_pointers_js', 'up_pointer_data', array('up_pointers'=>$pointers ) );
		wp_enqueue_script('up_pointers_js');
	}
}

new UP_Admin_pointers();