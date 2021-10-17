<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-st/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evost_admin{
	
	public $optSL;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
	}

	// INITIATE
		function admin_init(){

			// include the meta box connections
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

			// settings
			add_filter( 'evotix_settings_page_content', array( $this, 'settings_tix' ),10,1);

			// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'language_additions'), 10, 1);
			//add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
			//add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
			
			add_filter('woocommerce_hidden_order_itemmeta', array($this, 'hidden_order_items'),10,1);

			// all events column for tickets
			add_filter('evotx_admin_events_column_title', array($this, 'events_column'),10,2);

			// duplicating event
			add_action('evotx_after_duplicate_ticket_event', array($this,'after_duplicate_event'),10,3);
			add_action('eventon_duplicate_event_exclude_meta', array($this, 'exclude_duplicate_field'), 10, 1);	

		}

	// settings
		function settings_tix($array){
			$array[] = array(
				'id'=>'evotxst',
				'name'=>'Seat Settings For EventON Ticket',
				'tab_name'=>'Seat Settings',
				'icon'=>'coffee',
				'fields'=>array(
					array(
						'id'=>'evost_session_time',
						'type'=>'text',
						'name'=>'Cart session timeout duration (time in minutes)',
						'legend'=>'How much time of user inactivity allowed until the cart content is emptied and stock made available for sale for other users.',
						'default'=> ( 10)
					),array(
						'id'=>'evost_session_time',
						'type'=>'note',
						'name'=>'NOTE: By default the shopping cart with seats idling for 10 minutes will make those seats available back again for other customers. Smaller minute value you set above will result in smaller window for customers to checkout the seats before their seats will be available for others to purchase.',
					),
					array(
						'id'=>'_evost_hide_cart_exp',
						'type'=>'yesno',
						'name'=>'Hide seat expiration time in all cart pages',
						'legend'=>'With this you can hide the seat expiration timers from showing on cart pages. Otherwise, it will show by default.',
					),array(
						'id'=>'evost_restock_note',
						'type'=>'note',
						'name'=>'<b>Restock Seats</b>: If you would like the cancelled and refunded orders to auto restock seats and make them available for customers, make sure to enable Auto restock for tickets in Tickets > General settings.',
					)
			));
			return $array;
		}
	
	// Woocommerce Related
		function hidden_order_items($array){
			$array[]= '_seat_id';
			$array[]= '_seat_num';
			return $array;
		}
	// column text update for events with seats
		function events_column($text, $event_id){
			$seats = get_post_meta($event_id, '_enable_seat_chart', true);
			if($seats =='yes'){
				return $text.' - '. __('With Seats','eventon');
			}
			return $text;
		}

	// styles and scripts
		function event_post_styles(){
			global $evost;

			wp_enqueue_style( 'evost_admin_styles',$evost->plugin_url.'/assets/ST_admin_styles.css');
			
			wp_enqueue_script( 'evost_handlebars',EVOST()->assets_path.'handlebars.js',array('jquery'), EVOST()->version, true);
			wp_enqueue_script( 'evost_draw',EVOST()->assets_path.'evost_map_draw.js',array('jquery'), EVOST()->version, true);
			wp_enqueue_script( 'evost_admin_post_script',$evost->plugin_url.'/assets/ST_admin_script.js',array('jquery','jquery-ui-resizable','jquery-ui-draggable','jquery-ui-tooltip'), $evost->version);
			wp_localize_script( 
				'evost_admin_post_script', 
				'evost_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonst_nonce' )
				)
			);

			wp_enqueue_script('evcal_functions');
		}

	// Duplication event
		function after_duplicate_event($EVENT, $wc_id, $post){

			$SEATS = new EVOST_Seats_Json($EVENT, $wc_id);

			$SEATS->update_all_seats('status','av');

		}
		function exclude_duplicate_field($array){
			//$array[] = '_evost_sections';
			return $array;
		}

	// Language & appearance		
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: SEATS'),
					array('label'=>'Select Seats', 'var'=>1),				
					array('label'=>'Find Seats', 'var'=>1),				
					array('label'=>'SEC', 'var'=>1),				
					array('label'=>'ROW', 'var'=>1),				
					array('label'=>'SEAT', 'var'=>1),					
					array('label'=>'Seats', 'var'=>1),				
					array('label'=>'Seat Number', 'var'=>1),				
					array('label'=>'Ticket Price', 'var'=>1),				
					array('label'=>'Your Tickets', 'var'=>1),			
					array('label'=>'Number of Seats', 'var'=>1),			
					array('label'=>'Buy Now', 'var'=>1),			
					array('label'=>'Seats available', 'var'=>1),			
					array('label'=>'Available', 'var'=>1),			
					array('label'=>'Unavailable', 'var'=>1),			
					array('label'=>'Your selected seats', 'var'=>1),			
					array('label'=>'Reserved', 'var'=>1),			
					array('label'=>'Handicap Accessible', 'var'=>1),			
					array('label'=>'Preview Seat', 'var'=>1),			
					array('label'=>'Seat Legends', 'var'=>1),			
					array('label'=>'Unavailable (Sold Out)', 'var'=>1),			
					array('label'=>"In someone's cart", 'var'=>1),			
					array('label'=>"Your Tickets In Cart", 'var'=>1),			
					array('label'=>"Seats in your cart", 'var'=>1),			
					array('label'=>'Seat not available at the moment', 'var'=>1),			
					array('label'=>'Your seats will expire in', 'var'=>1),			
					array('label'=>'Seats added to cart will expire in [time] minutes of inactivity in cart.', 'var'=>1),			
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	
}