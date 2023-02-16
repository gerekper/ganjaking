<?php
/**
 * Event Subscriber Custom Post class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventON/Admin/evo-subscriber
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosb_subscriber{	
	// Constructor
		function __construct(){
			add_filter( 'manage_edit-evo-subscriber_sortable_columns', array($this,'subscriber_sort') );
			add_action('manage_evo-subscriber_posts_custom_column', array($this,'evo_sub_custom_event_columns'), 2 );
			add_filter( 'manage_edit-evo-subscriber_columns', array($this,'evo_sub_edit_event_columns') );
			add_filter( 'request', array($this,'editorder_column') );	

			//add_action( 'admin_head-edit.php', array($this,'custom_data') );
			//add_filter( 'views_edit-evo-subscriber', array($this,'custom_data_view') );
		}
	
	// Columns
		function evo_sub_edit_event_columns( $existing_columns ) {
			global $eventon;
			
			// GET event type custom names
			
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";	
			$columns['title'] = __( 'Title', 'eventon' );
			$columns['subscribed'] = __( 'Subscribe Status', 'eventon' );
			$columns['verified'] = __( 'Verified Status', 'eventon' );
			$columns["date"] = __( 'Date', 'eventon' );	

			return array_merge( $columns, $existing_columns );
		}
		
	// field values
		function evo_sub_custom_event_columns( $column ) {
			global $post, $eventon;

			$subpmv = get_post_custom($post->ID); 
			
			switch ($column) {	
				case "subscribed":
					$link = wp_nonce_url( admin_url( 'admin-ajax.php?action=evosb_tog_activation&subscriberID=' . $post->ID ), 'evosb_tog_activation' );
					
					echo (!empty($subpmv['status']) && $subpmv['status'][0]=='yes')? 
						'<a href="'.$link.'"><span class="ss_active">'.__('Active','eventon') . '</span></a>':
						'<a href="'.$link.'"><span class="ss_inactive">'.__('Inactive','eventon') . '</span></a>';					
				break;
				case "verified":
					echo (!empty($subpmv['verified']) && $subpmv['verified'][0]=='yes')? 
						'<span class="vs_active">'.__('Verified','eventon') . '</span>':
						'<span class="vs_inactive">'.__('Not-Verified','eventon') . '</span>';					
				break;					
			}
		}

	// make ticket columns sortable
		function subscriber_sort($columns) {
			$custom = array(
				'subscribed'		=> 'subscribed',
				'verified'		=> 'verified',
			);
			return wp_parse_args( $custom, $columns );
		}
		function editorder_column($vars){
			if($vars['post_type']!='evo-subscriber')
				return $vars;

			if (isset( $vars['orderby'] )) :
				if ( 'subscribed' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'subscribed',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				if ( 'verified' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'verified',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				
			endif;

			return $vars;
		}
	
	// custom data on subscribers page
		function custom_data_view($views){
			$views['evosb-button'] = '<a href="'.get_admin_url().'/admin-ajax.php?action=evosb_generate_csv" id="evosb_download_csv" title="Download all subscribers as CSV file" style="margin-left:5px;padding:3px 7px; top:-3px" class="evo_admin_btn btn_secondary">Download CSV</a>';
    		return $views;
		}
		function custom_data(){
			global $current_screen;
		    // Not our post type, exit earlier
		    if( 'evo-subscriber' != $current_screen->post_type )
		        return;
		    ?>
		    <script type="text/javascript">
		        jQuery(document).ready( function($) {
		            $('#evosb_download_csv').appendTo('h2');    
		        });     
		    </script>
		    <?php
		}
}
new evosb_subscriber();
