<?php
/**
 * Woocommerce my account integration	
 * @version 2.0
 */


class EVOTX_WC_my_account{

	private $page_slug = 'evo_tickets';

	public function __construct(){
		add_action( 'init', array($this,'custom_EP') );
		add_filter( 'query_vars', array($this,'_query_vars'), 0 );

		// flush reqrite tules
		add_action('evotx_activate', array($this, 'flush_rewrites'));
		add_action('evotx_deactivate', array($this, 'flush_rewrites'));

		add_filter ( 'woocommerce_account_menu_items', array($this,'account_menu_items') );	
		add_action('woocommerce_account_evo_tickets_endpoint', array($this, 'tickets_content'));
		add_filter( 'the_title', array($this, 'tickets_title') );
		

	}

	public function flush_rewrites(){
		flush_rewrite_rules();
	}

	public function custom_EP(){
		add_rewrite_endpoint( $this->page_slug, EP_ROOT | EP_PAGES );
		
	}
	public function _query_vars($vars){
		$vars[] = $this->page_slug;
	 	return $vars;
	}

	public function account_menu_items($menu_links){
		$menu_links[ $this->page_slug ] = __('Event Tickets','evotx');

		return $menu_links;
	}

	public function tickets_content(){
		

		$customer_id = get_current_user_id();

		if( !$customer_id){
			echo __('Could not find user id','evotx');
		}else{
			$EA = new EVOTX_Attendees();

			$tickets = $EA->get_tickets_by_customer_id( $customer_id);

			if( !$tickets){
				echo __('You do not have any event tickets','evotx');
			}else{
				
				$tickets = $EA->sort_tickets_by_event($tickets);	

				$upcoming = $past = '';


				foreach( $tickets as $event_id=>$events){				

					foreach($events as $ri=>$ticket_list){

						$html = $row = '';

						$is_past = true;
						
						
						$count = 1;
						foreach( $ticket_list as $ticket_number=> $TD){

							if(!isset( $TD['o'] )) continue;

							if( get_post_type( $TD['o'] ) != 'shop_order') continue;
							
							$order = new WC_Order($TD['o']);
							$TIX = new EVO_Evo_Tix_CPT($ticket_number);

							if( $count == 1){

								if( EVO()->calendar->current_time <= $TD['oDD']['event_end_unix'] )
									$is_past = false;

								$head = "<div class='evotx_event ". ($is_past? 'past':'') ."'>
								<h4>{$TD['oD']['event_title']}</h4>
								<p><b>". __('Event Time','evotx'). "</b>: ". (isset($TD['oD']['event_time']) ? $TD['oD']['event_time'] :'-' ) ."</p>

								<div class='evotx_event_tickets'>
								<p class='evotx_tb evotx_tb_head'>
									<span>". __('Ticket','evotx') ."</span>
									<span>". __('Ticket Status','evotx') ."</span>
									<span>". __('Order','evotx') ."</span>
									
									". do_action('evotx_wc_myaccount_tickettb_header'). "						
								</p>";
							}


							$row .= "<p class='evotx_tb evotx_ticket'>";

							if( $is_past){ $row .= "<span>". $TD['name'] ."</span>";}
							else{
								$row .= "<span><span class='evotx_view_ticket evcal_btn' data-tn='". ( $ticket_number )."'><i class='fa fa-eye' title='". __('View Ticket','evotx'). "'></i> View Ticket</span> ". $TD['name'] ."</span>";
							}
							
							//$row .= "<span>#". $TIX->get_enc_ticket_number( $ticket_number ) .'</span>';
							$row.= "<span class='tx_status'>". $TIX->get_status() . "</span>";
							$row.= "<span class='tx_o_status'><a href='". esc_url( $order->get_view_order_url() ) ."'>#". $TD['o']. "</a> - ". esc_html( $order->get_status() ) . "</span>";
							
							$row .= apply_filters('evotx_wc_myaccount_tickettb_row','', $TIX, $TD, $order, $EA);

							$row.= "</p>";

							$count ++;
						}

						
						$row .= "</div></div>";

						$html = $head . $row;

						if( $is_past ) $past .= $html;
						if( !$is_past ) $upcoming .= $html;
						
					}
				} // foreach $tickets

				echo "<h4>". __('Upcoming Events','evotx') ."</h4>". $upcoming ;
				echo "<h4>". __('Past Events','evotx') ."</h4>" . $past;

			}
		}
	}

	public function tickets_title($title){
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ $this->page_slug ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'My Event Tickets', 'evotx' );
			remove_filter( 'the_title', array($this,'tickets_title') );
		}

		return $title;
	}	
}

