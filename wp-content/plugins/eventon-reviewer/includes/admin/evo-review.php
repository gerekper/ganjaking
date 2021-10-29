<?php
/**
 * Admin functions for the evo-review post type
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventON/Admin/evo-review
 * @version     1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function _evore_remove_box(){
	remove_post_type_support('evo-review', 'title');
    remove_post_type_support('evo-review', 'editor');
}
add_action("admin_init", "_evore_remove_box");

// columns for evo-review
	function evoRE_edit_event_columns( $existing_columns ) {
		// GET event type custom names
		
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
			$existing_columns = array();

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns = array();
		$columns["cb"] = "<input type=\"checkbox\" />";	
		$columns['rating'] = __( 'Rating', 'eventon' );
		$columns['review'] = __( 'Review', 'eventon' );;
		$columns["event"] = __( 'Event', 'eventon' );
		$columns["status"] = __( 'Status', 'eventon' );
		
		//$columns["date"] = __( 'Date', 'eventon' );

		return array_merge( $columns, $existing_columns );
	}
	add_filter( 'manage_edit-evo-review_columns', 'evoRE_edit_event_columns' );

// field values
	function evoRE_custom_event_columns( $column ) {
		global $post, $eventon, $eventon_rs;

		//if ( empty( $ajde_events ) || $ajde_events->id != $post->ID )
			//$ajde_events = get_product( $post );

		$opt = get_option('evcal_options_evcal_2');

		$meta = get_post_meta($post->ID);

		switch ($column) {		
			case "rating":
				global $eventon_re;

				$edit_link = get_edit_post_link( $post->ID );
				
				$rating =get_post_meta($post->ID, 'rating', true);				
				echo "<strong><a class='row-title' href='".$edit_link."'>".$eventon_re->frontend->functions->get_star_rating_html($rating). "</a>";						
			break;
			case "review":
				$review = get_post_meta($post->ID, 'review', true);


				echo !empty($review)? "<p>".$review. "<br/><i class='at'>at ".$post->post_date."</i></p>":'';
					
			break;	
			case "event":
				$e_id = (!empty($meta['e_id']))? $meta['e_id'][0]: null;

				if($e_id){
					$edit_link = get_edit_post_link( $e_id );
					$title = get_the_title($e_id);
					
					echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
				}else{ echo '--';}
			break;
			case "status":
				
				$status = get_post_status($post->ID);				
				if($status!='publish'){
					echo "<p class='review_post_status nobueno'>";
					echo __('Need Approved','eventon');
				}else{
					echo "<p class='review_post_status'>";
					echo __('Approved','eventon');
				}
				echo "</p>";
			break;		
		}
	}
	add_action('manage_evo-review_posts_custom_column', 'evoRE_custom_event_columns', 2 );


// COLUMN Sorting
	/** Make events columns sortable */
	function evore_review_sort($columns) {
		$custom = array(
			'rating'		=> 'rating',
			'event'		=> 'event',
		);
		return wp_parse_args( $custom, $columns );
	}
	add_filter( 'manage_edit-evo-review_sortable_columns', 'evore_review_sort');


/** Event column orderby */
	function evore_editorder_columns( $vars ) {
		if (isset( $vars['orderby'] )) :
			if ( 'rating' == $vars['orderby'] ) :
				$vars = array_merge( $vars, array(
					'meta_key' 	=> 'rating',
					'orderby' 	=> 'meta_value'
				) );
			endif;
			if ( 'event' == $vars['orderby'] ) :
				$vars = array_merge( $vars, array(
					'meta_key' 	=> 'e_id',
					'orderby' 	=> 'meta_value'
				) );
			endif;			
		endif;
		return $vars;
	}
	add_filter( 'request', 'evore_editorder_columns' );