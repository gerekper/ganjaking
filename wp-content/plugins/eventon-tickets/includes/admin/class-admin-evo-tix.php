<?php
/**
 * Event Ticket Custom Post class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventON/Admin/evo-tix
 * @version     1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evotx_tix_cpt{	
	// Constructor
		function __construct(){
			add_filter( 'request', array($this,'ticket_order') );

			add_filter( 'manage_edit-evo-tix_sortable_columns', array($this,'ticket_sort') );
			add_action('manage_evo-tix_posts_custom_column', array($this,'evo_tx_custom_event_columns'), 2 );
			add_filter( 'manage_edit-evo-tix_columns', array($this,'evo_tx_edit_event_columns') );
			add_action("admin_init", array($this,"_evo_tx_remove_box"));

			// custom filters
			add_action('restrict_manage_posts',array($this,'evo_restrict_manage_posts'));
			add_filter('query_vars', array($this,'register_query_vars' ));
			add_filter('months_dropdown_results', array($this,'remove_date_filter' ),10,2);
			add_action( 'pre_get_posts', array($this,'pre_get_posts' ));

			// woocommerce Orders columns
		    $posttype = "shop_order";
		    add_filter( "manage_edit-{$posttype}_columns", array($this, 'SO_edit_columns'), 20, 1 );
		    add_action( "manage_{$posttype}_posts_custom_column", array($this, 'column_display_so_22237380'), 20, 2 ); 
		    add_filter( "manage_edit-{$posttype}_sortable_columns", array($this, 'column_sort_so_22237380') );
			
			// add woo into event CPT columns 
			add_filter('evo_event_columns', array($this, 'add_column_title'), 10, 1);
			add_filter('evo_column_type_woo', array($this, 'column_content'), 10, 1);

			// evo-tix extending the search field
			add_action('pre_get_posts', array($this, 'extend_admin_search'));
		}

	// admin search
		public function extend_admin_search( $query){
			$post_type = 'evo-tix';
			
			// Custom fields to search for
			$custom_fields = array(
		        "_eventid",
		    );
		 
		    if( ! is_admin() ) 	return;
		    
		  	if( isset($query->query['post_type']) && $query->query['post_type'] != $post_type )	return;
		 
		    $search_term = $query->query_vars['s'];
		 
		    // Set to empty, otherwise it won't find anything
		    $query->query_vars['s'] = '';
		 
		    if ( $search_term != '' ) {

		    	// if searching by ticket ID
		    	if( strpos($search_term, '-')){
		    		$ticket_no = explode('-', $search_term);
		    		$query->query_vars['p'] = (int)$ticket_no[0];
		    	}

		    	// if searching by event name
		    	else{

		    		// searching by attendee name
		    		if( is_numeric($search_term)){
		    			$query->query_vars['p'] = $search_term;
		    		}else{

		    			$meta_query = array( 
			    			'relation' => 'OR',
			    			array(
			    				'key'=> 'name',
			    				'value'=> $search_term,
			    				'compare'=> 'IN'
			    			)
			    		);

		    			/*
		    			$event_ids = $this->_get_eventid_by_name( $search_term );
		    				    		
			    		$meta_query = array( 
			    			'relation' => 'OR',
			    			array(
			    				'key'=> '_eventid',
			    				'value'=> $event_ids,
			    				'compare'=> 'IN'
			    			)
			    		);
			    		*/
			 
				        $query->set( 'meta_query', $meta_query );
		    		}		    		
		    	}


		    };
		}

		private function _get_eventid_by_name($event_name){
			global $wpdb;
		    $post = $wpdb->get_results( $wpdb->prepare( 
		    	"SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_type='ajde_events'", '%' . $event_name . '%')
			);
		    
		    if ( $post ){
		    	$p = array();

		    	foreach($post as $pp){ $p[] = $pp->ID; }
		    	return $p;
		    } 

		    return null;
		}

	// add order type columns
		function SO_edit_columns( $columns ){
		    $columns['order_type'] = "Type";
		    return $columns;
		}
		function column_display_so_22237380( $column_name, $post_id ) {
		    if ( 'order_type' != $column_name )
		        return;

		    $order_type_ = get_post_meta($post_id, '_order_type', true);
		    $order_type = (!empty($order_type_) && $order_type_== 'evotix')? __('Ticket Order','evotx'):__('Non-Ticket Order','evotx');
		    if ( $order_type ){
		        echo $order_type;
		    }
		}
		function column_sort_so_22237380( $columns ) {
		    $columns['order_type'] = 'order_type';
		    return $columns;
		}

	// remove the main editor box
		function _evo_tx_remove_box(){
			remove_post_type_support('evo-tix', 'title');
			remove_post_type_support('evo-tix', 'editor');
		}

	// add new column to menu items
			function add_column_title($columns){
				$columns['woo']= '<i title="Connected to woocommerce">'.__('TIX','evotx').'</i>';
				return $columns;
			}
			function column_content($post_id){				
				$evotx_tix = get_post_meta($post_id, 'evotx_tix', true);

				if(!empty($evotx_tix) && $evotx_tix=='yes'){
					global $evotx_admin;

					$__woo = get_post_meta($post_id, 'tx_woocommerce_product_id', true);
					//$__wo_perma = (!empty($__woo))? get_edit_post_link($__woo):null;
					
					
					$product_type = 'simple';
					$product_type = $evotx_admin->get_product_type($__woo);

					$_stock = "<i title='".__('Tickets are active','evotx')."'><b></b></i>";
					if($product_type == 'simple'){
						$_stockC = (int)get_post_meta($__woo, '_stock',true);
						if($_stockC) $_stock =  "<i title='".__('Tickets in Stock','evotx')."'>". $_stockC."</i>";
					}

					return (!empty($__woo))?
						"<span class='yeswootix' title='".apply_filters('evotx_admin_events_column_title',$product_type, $post_id)."'>".$_stock."</span>":
						"<span class='nowootix'>".__('No','evotx') . "</span>";
				}else{
					return "<span class='nowootix'>".__('No','evotx') . '</span>';
				}
			}

	/**
	 * Define custom columns for evo-tix
	 * @param  array $existing_columns
	 * @return array
	 */
		function evo_tx_edit_event_columns( $existing_columns ) {
			global $eventon;
			
			// GET event type custom names
			
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();
			if($_GET['post_type']!='evo-tix')
				return;

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";	

			$columns['tix'] = __( 'Event Ticket(s)', 'evotx' );
			$columns['tix_status'] = __( 'Status', 'evotx' );
			$columns['tix_wcid'] = __( 'Order ID', 'evotx' );
			
			$columns["tix_event"] = __( 'Event', 'evotx' );
			$columns["tix_type"] = __( 'Ticket Type', 'evotx' );
			$columns["date"] = __( 'Date', 'evotx' );				
			

			return array_merge( $columns, $existing_columns );
		}		

	// field values
		function evo_tx_custom_event_columns( $column ) {
			global $post, $eventon, $evotx;

			$meta = get_post_meta($post->ID); // ticket item meta
			
			$evotx_tix = $ET = new evotx_tix();
			$ET->evo_tix_id = $post->ID;



			switch ($column) {	
				case 'tix_wcid':
					$wcid = $ET->get_prop('_orderid');
					echo '<a class="row-title" href="'.get_edit_post_link( $wcid ).'">' . $wcid.'</a>';
				break;
				case "tix":
					// new method 1.7
					if( $ET->get_prop('_ticket_number') ){

						$ticket_number = $ET->get_prop('_ticket_number');

						$EA = new EVOTX_Attendees($ET);
						$attendee = $EA->get_attendee_by_ticket_number($ticket_number);
						$event_instance = $ET->get_prop('_ticket_number_instance');

						$name = isset($attendee['name']) ? $attendee['name']:$ET->get_prop('name'); 
						
						//$ticket_holder

						echo "<strong><a class='row-title evotix_admin_tixnum' href='". get_edit_post_link( $post->ID ) ."'>#".$ET->get_prop('_ticket_number')."</a></strong>
						<span> by ".$name." ".$ET->get_prop('email');
						echo "</span>";
					// legacy
					}else{
						$edit_link = get_edit_post_link( $post->ID );
						$cost = $ET->get_prop('cost');

						echo "<strong><a class='row-title' href='".$edit_link."'>#{$post->ID}</a></strong> by ".$meta['name'][0]." ".$meta['email'][0];

						// get ticket ids
						$tix_id_ar = $evotx_tix->get_ticket_numbers_by_evotix($post->ID, 'string');

						echo '<br/><em class="lite">Ticket ID(s):</em> <i>'.$tix_id_ar.'</i>';

						echo '<br/><span class="evotx_intrim">'. $ET->get_prop('qty') .' <em class="lite">(Qty)</em> - '. ((!empty($cost))? get_woocommerce_currency_symbol().apply_filters('woocommerce_get_price', $cost): '-').'<em class="lite"> (Total)</em></span>';
					}
					
				break;
				case "tix_event":
					$e_id = (!empty($meta['_eventid']))? $meta['_eventid'][0]: null;

					if($e_id){
						echo '<strong><a class="row-title" href="'.get_edit_post_link( $e_id ).'">' . get_the_title($e_id).'</a></strong>';
					}else{ echo '--';}

				break;
				case "tix_type":
					$type = get_post_meta($post->ID, 'type', true);						
					echo (!empty($type))? $type: '-';
				break;
				
				case "tix_status":
					// order
						$order_id = $ET->get_prop('_orderid');
						$order_status = 'n/a';	
						$_o_status = get_post_status($order_id);						
						if($order_id && $_o_status){	
							$order = new WC_Order( $order_id );
							$order_status = $order->get_status();
						}

					// new method 1.7
					if( $tn= $ET->get_prop('_ticket_number') ){
						$tickets = $ET->get_prop('ticket_ids');
						$this_ticket_status = isset($tickets[$tn])? $tickets[$tn]: $ET->get_prop('status');

						$display = $_checked_class = $this_ticket_status;
					}else{
						$checked_count = $evotx_tix->checked_count($post->ID);
						$status = 'checked';

						$checked_count_ = !empty($checked_count['checked'])? $checked_count['checked']:'0';
						
						// if all checked 
							$_checked_class = ($checked_count_ == $checked_count['qty'])? 'checked':'check-in';

						// different state on checked tickets
							if($checked_count['qty'] == '1' && $checked_count_=='0' ){
								$display = $evotx_tix->get_checkin_status_text('check-in');
							}elseif(($checked_count['qty'] == '1' && $checked_count_=='1')|| ($checked_count['qty']>1 && $checked_count['qty'] == $checked_count_)){
								$display = $evotx_tix->get_checkin_status_text('checked');
							}else{
								$display = $evotx_tix->get_checkin_status_text($status).' '.$checked_count_.'/'.$checked_count['qty'];
							}						
					}					

					echo "<p class='evotx_status_list {$order_status}'><span class='evotx_wcorderstatus {$order_status}' title='".__('Order Status','evotx')."'>".$order_status ."</span></p>";

					if( $order_status == 'completed'){
						echo "<p class='evotx_status_list {$_checked_class}'><span class='evotx_status {$_checked_class}' title='".__('Ticket Status','evotx')."'>".$display."</span></p>";	
					}	

				break;
			}
		}
	
	// make ticket columns sortable
		function ticket_sort($columns) {
			$custom = array(
				'event'		=> 'event',
			);
			return wp_parse_args( $custom, $columns );
		}
		function ticket_order( $vars ) {
			if (isset( $vars['orderby'] )) :
				if ( 'event' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_eventid',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				
			endif;

			return $vars;
		}

	// custom filter
		function remove_date_filter($A, $post_type){
			//if($post_type == 'evo-tix')	return $A;
			return $A;
		}
		function evo_restrict_manage_posts() {
			global $typenow;

			if ($typenow=='evo-tix'){
	           	$event_id = (isset($_GET['event_id'])? sanitize_text_field($_GET['event_id']):null);
				?>
				<select name="event_id">
					<option value="all"><?php _e('All Events','evotx');?></option>
					<?php 
					// get all events with tickets on
					$ev = new WP_Query(array(
						'posts_per_page'=>-1,
						'post_type'=>'ajde_events',
						'meta_query'=>array(
							'key'=>'evotx_tix',
							'value'=>'yes'
						)
					));
					if( $ev->have_posts()):
						foreach( $ev->posts as $post){
							$selected = $event_id == $post->ID ? 'selected="selected"' : null;
							echo "<option value='{$post->ID}' {$selected}>(#{$post->ID}) {$post->post_title}</option>";
						}

					endif;
					?>
				</select>
				<?php
	        }
		}
		function register_query_vars( $Q ){
		    //Add these query variables
		    $Q[] = 'event_id';
		    return $Q;
		}
		function pre_get_posts( $query ) {

		    //Only alter query if custom variable is set.
		    $event_id = $query->get('event_id');
		    if( !empty($event_id) && $event_id != 'all'){

		        $meta_query = $query->get('meta_query');
		        if( empty($meta_query) )    $meta_query = array();

		        // add event id to query
		        	$meta_query[] = array(
			            'key' => '_eventid',
			            'value' => $event_id,
			        );
		        
		        $query->set('meta_query',$meta_query);
		    }
		}
}
new evotx_tix_cpt();
