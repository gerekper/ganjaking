<?php
/** 
 * Attendees
 */

class EVOTX_Attendees{

	private $evotix_id;
	public function __construct($evotx_tix = ''){
		$this->ETX = empty($evotx_tix)? new evotx_tix() : $evotx_tix;
	}

// PRIMARY GETTERS
	// get attendee by ticket number
		function get_attendee_by_ticket_number($tn){
			$tt = explode('-', $tn);
			$order_id = $tt[1];

			$t = $this->get_tickets_for_order($order_id);

			if( count($t)>0){
				return isset($t[$tn])? $t[$tn]: false;
			}
			return false;
		}

// SUPPORTIVE GETTERS
	// get tickets for an event
		function get_tickets_for_event($event_id){

			$EVENT = new EVO_Event( $event_id);

			$ticket_numbers = array();
			$meta_query = array(
				array('key' => '_eventid','value' => $event_id,'compare' => '=')
			);

			global $post;
			$_post = $post;

			$event_tickets = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type'=>'evo-tix',
				'meta_query' => $meta_query
			));

			if($event_tickets->have_posts()):
				while($event_tickets->have_posts()): $event_tickets->the_post();
					$this->evotix_id = $event_tickets->post->ID;
					
					$thA = $this->__return_th_array($EVENT);
					if(count($thA)>0){
						$ticket_numbers = array_merge($ticket_numbers, $thA);
					}			

				endwhile;
				wp_reset_postdata();
			endif;

			$GLOBALS['post'] = $_post;

			return $ticket_numbers;
		}

			// child functions
			function _get_tickets_for_event($event_id, $sortby='order_status'){
				$t = $this->get_tickets_for_event($event_id);
				if(!$t) return false;

				$_t = array();
				switch($sortby){
					case 'order_status':
						foreach( $t as $ti=>$td){
							$_t[ $td['oS'] ][$ti] = $td;
						}
					break;
					case 'order_status_tally':
						foreach( $t as $ti=>$td){

							$os = ($td['oS'] == 'completed')? $td['s']: $td['oS'];

							$_t[ $os ] = isset($_t[ $os ])? (int)$_t[ $os ]+1: 1;
							$_t['total'] = isset($_t['total'])? (int)$_t['total']+1: 1;
						}
					break;
				}

				return $_t;
			}

	// get tickets along with ticket holder data for a order
		function get_tickets_for_order($order_id){
			$ticket_numbers = array();

			//$TH = get_post_meta($order_id, '_tixholders', true);
			//$TH = $this->_process_ticket_holders( $TH);

			$purchaser = $this->get_ticket_purchaser($order_id);

			global $post;
			$_post = $post;

			$event_tickets = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type'=>'evo-tix',
				'meta_key'=>'_orderid',
				'meta_value'=>$order_id
			));

			if($event_tickets->have_posts()):
				while($event_tickets->have_posts()): $event_tickets->the_post();
					$this->evotix_id = $event_tickets->post->ID;
					
					$thA = $this->__return_th_array();
					if(count($thA)>0){
						$ticket_numbers = array_merge($ticket_numbers,$thA);
					}

				endwhile;				
			endif;
			wp_reset_postdata();

			$GLOBALS['post'] = $_post;

			return $ticket_numbers;
		}
			// child sorted versions
				function _get_tickets_for_order($order_id, $sortby='event'){
					$t = $this->get_tickets_for_order($order_id);
					if(!$t) return false;

					$_t = array();
					switch($sortby){
						case 'event':
							foreach( $t as $ti=>$td){
								$_t[ $td['event_id'] ][$ti] = $td;
							}
						break;case 'order_status':
							foreach( $t as $ti=>$td){
								$_t[ $td['oS'] ][$ti] = $td;
							}
						break;
					}

					return $_t;
				}

	// return ticket holder array content for evo-tix post
		function __return_th_array($EVENT=''){
			$event_id = $this->get_prop('_eventid');
			$order_id = $this->get_prop( '_orderid');
			
			if(empty($EVENT)) $EVENT = new EVO_Event($event_id);

			$_ri = $this->_get_ri();
			$ET = $EVENT->get_formatted_smart_time($_ri);

			// get ticket holders from order post
			$TH = get_post_meta($order_id, '_tixholders', true);
			//print_r($TH);
			$TH = $this->_process_ticket_holders( $TH);

			//print_r($TH);

			// make sure order still exists
			if(!get_post($order_id)){
				return array();
			}

			$purchaser = $this->get_ticket_purchaser($order_id);
			
			$ticket_ids = $this->get_prop( 'ticket_ids');

			//print_r($TH);
			$ticket_numbers = array();
			$index = 0;
			foreach($ticket_ids as $ticket_id=>$status){
				// event instance
					$_ticket_number_instance = $this->get_prop('_ticket_number_instance');
					if(!$_ticket_number_instance) $_ticket_number_instance = null;

				// get ticket holder for this ticket
				$ticket_number_index = $this->get_prop('_ticket_number_index');
				$ticket_number_index = $ticket_number_index? $ticket_number_index: $index;
				$_th = $this->__filter_ticket_holder($TH, $event_id, $_ri, $ticket_number_index , $_ticket_number_instance);
		

				// name & email
				$N = !empty($_th['name']) ? $_th['name']: $purchaser['name'];
				$E = !empty($_th['email']) ? $_th['email']: $purchaser['email'];


				$ticket_numbers[$ticket_id] = apply_filters('evotx_get_attendees_for_event',array(
					's'=> $status,
					'n'=> $N,
					'name'=> $N,
					'e'=> $E,
					'email'=> $E,
					'event_id'=>$event_id,
					'ri'=> $_ri,
					'o'=> $order_id,
					'd'=>get_the_date('Y-m-d'),
					'id'=> $this->evotix_id,
					'type'=> $this->get_prop('type'),
					'oD'=> array(
						'ordered_date'=>get_the_date('Y-m-d'),
						'email'=>$E,
						'event_time'=>$ET,
					),
					'oDD'=>array( // other data that are not shown by default
						'_order_item_id' => $this->get_prop('_order_item_id'),
						'order_id' => $order_id,
						'event_instance'=> ( empty($_ticket_number_instance)? 1:$_ticket_number_instance),
						'signin'=> $this->get_prop('signin'),
					),
					'eU'=> get_edit_post_link($order_id), // edit order post link
					'th'=> $_th,
				),$event_id, $_th);



				// other purchaser data
				foreach(array('company','phone','oS','aD','payment_method') as $F){
					if(!isset($purchaser[$F])) continue;
					$ticket_numbers[$ticket_id][$F] = $purchaser[$F];
				}
				$index ++;
			}

			//print_r($ticket_numbers);

			return $ticket_numbers;
		}

	// return an array of ticket purchaser name and email
		function get_ticket_purchaser($order){
			if( is_numeric($order)){
				if( !get_post_status($order)) return false;
				$order = new WC_Order( $order);
			} 

			if(!$order) return false;

			$order_id = $order->get_id();
			
			$bil_fn = get_post_meta($order_id, '_billing_first_name',true);
			$bil_ln = get_post_meta($order_id, '_billing_last_name',true);
			$bil_em = get_post_meta($order_id, '_billing_email',true);

			// payment method
			if ( WC()->payment_gateways() ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
			} else {
				$payment_gateways = array();
			}
			$payment_method = $order->get_payment_method();
			$payment_method = ($payment_method)? (($payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method) 
				:'';

			$aD = '"'.$order->get_billing_address_1().' '.
					$order->get_billing_address_2().' '.
					$order->get_billing_city().' '.
					$order->get_billing_state().' '.
					$order->get_billing_postcode().' '.
					$order->get_billing_country().'"';

			// if billing information is not there
			if(empty($bil_fn)){
				$user_id = $order->get_customer_id();
				$usermeta = get_userdata( $user_id );
				if($usermeta) $bil_fn = $usermeta->first_name;
				if($usermeta) $bil_ln = $usermeta->last_name;
			}

			return array(
				'customer_id'=> $order->get_customer_id(),
				'name'=> $bil_fn.' '.$bil_ln,
				'email'=> $bil_em,
				'company'=>$order->get_billing_company(),
				'phone'=>$order->get_billing_phone(),
				'oS'=>$order->get_status(),
				'aD'=> $aD,
				'payment_method'=>$payment_method
			);
		}

// VERIFICATIONS
	// if the current user can check in attendees
		function _user_can_check(){
			if(current_user_can('manage_eventon')) return true;
			return false;
		}

// DISPLAY
	function __display_one_ticket_data($ticket_number, $td, $args = array()){
		if(!is_array($td)) return false;

		$status = $td['s'];
		ob_start();

		$def = array(
			'orderStatus'=> $td['oS'],
			'showOrderStatus'=>false,
			'inlineStyles'=> false,
			'showStatus'=>false,
			'linkTicketNumber'=>false,
			'flex'=>true,
			'showExtra'=>true,
			'show_signin'=>false,
			'guestsCheckable'=>false
		);

		extract( array_merge($def, $args));

		// ticket status change to refunded if order is refunded
		if($orderStatus == 'refunded') $status = $orderStatus;

		?>
			<?php if($inlineStyles):?>
			<style type="text/css">
				.evotxVA_ticket{display:<?php echo $flex? 'flex':'block';?>;}
				.evotxVA_data{padding-left:20px;}
				.etxva_main{display:block;}
				.etxva_other span{display:block; opacity:0.7; font-size:12px;}
				.etxva_other em{text-transform: capitalize;}
				.evotxVA_tn{font-weight: bold; opacity: 0.7;}
			</style>
			<?php endif;?>
			<span class='evotxVA_ticket <?php echo $status;?>'>
				<span class='evotxVA_tn'><?php if($linkTicketNumber ): echo "<a href='". get_edit_post_link($td['id'])."' class='evo_admin_btn btn_triad'>"; endif;?><?php echo  apply_filters('evotx_tixPost_tixid', $ticket_number, $td);?><?php if($linkTicketNumber ): echo "</a>"; endif;?></span>
				<span class='evotxVA_data'>
					<span class='etxva_main'>
						<b><?php echo $td['name'];?></b> 
						<?php
						// signin status
						if($show_signin && isset($td['oDD']) && isset($td['oDD']['signin']) && $td['oDD']['signin'] == 'y'){
							?>
							<i class='signin fa fa-check'></i>
							<?php
						}

						?>
						<?php if($orderStatus == 'completed' && $showStatus || $status=='refunded'): ?>
							<span class='etxva_tag evotx_status <?php echo $status;?> <?php echo $guestsCheckable?'checkable':'';?>' data-tiid='<?php echo $td['id'];?>' data-gc='<?php echo $guestsCheckable?'true':'false';?>' data-tid='<?php echo $ticket_number;?>' data-status='<?php echo $status;?>'><?php echo $this->ETX->get_checkin_status_text($status);?></span>
						<?php endif;?>
						<?php if($showOrderStatus):?>
							<span class='etxva_tag evotx_wcorderstatus <?php echo $orderStatus;?>' ><?php echo $orderStatus;?></span>
						<?php endif;?>
					</span>												
					<span class='etxva_other'>
						<?php
						foreach( $td['oD'] as $kk=>$k){
							if(in_array($kk, array('ordered_date'))) continue;
							$_kk = str_replace('_', ' ', $kk);
							// capitalize event time
							if($kk == 'event_time') $k = ucfirst($k);
								
								$translated = evo_lang($_kk);

							?><span class='<?php echo $kk;?>'><em><?php echo $translated;?></em>: <?php echo $k;?> </span><?php 
						}
						?>
					</span>
					<?php
						$JD = array(
							'eid'=> $td['event_id'],
							'ri'=> $td['ri'],
							'ei'=> isset($td['oDD']['event_instance'])? $td['oDD']['event_instance']: '0'
						);
					?>
					<span style='display:none' class='evotxva_jdata' data-j='<?php echo json_encode($JD);?>'></span>
				</span>
				<?php if($showExtra && $orderStatus != 'refunded'):?>
					<span class='evotxVA_extra'>
					<?php do_action('evotx_one_ticket_extra',$ticket_number, $td);?>
					</span>
				<?php endif;?>
			</span>			
		<?php 

		return ob_get_clean();
	}

// PROCESS
	// process order ticket holders for old and new methods for multidimensional array
		function _process_ticket_holders($ticket_holders_array){
			$data = $ticket_holders_array;
			if(!is_array($data)) return false;

			$o = array();

			foreach($data as $e => $ris){

				// new method
				if( is_array($ris)){
					foreach($ris as $ri=>$indexes){
						if(!is_array($indexes)) continue;
						foreach($indexes as $Q=>$data){ // ticket number index
							if(!is_array($data)) continue;
							foreach($data as $_event_instance=>$value){ // event instance
								// if event instance is saved
								if(is_array($value)){
									foreach($value as $field=>$v){
										$o[$e][$ri]['names'][$Q][$_event_instance][$field] = $v;
									}
								// if event instance is not saved
								}else{
									$o[$e][$ri]['names'][$Q][1][$_event_instance] = $value;
								}								
							}
							
						}
					}
				}else{ // for old method
					array_filter($ris,'strlen');
	    			if(sizeof($ris)>0){
	    				foreach($ris as $i=>$n){
	    					$o[$e]['all']['names'][$i][1]['name'] = $n;
	    				}        					        				
	    			}			        			
				}			
			}

			return $o;
		}

	// secondary function to get ticket holder name for event and ri
		function __filter_ticket_holder($TH, $event_id, $ri, $index, $instance=''){
			if( !isset($TH[$event_id])) return false;

			if(empty($instance)) $instance = 1;

			$R = !isset($TH[$event_id][$ri])? (isset($TH[$event_id]['all'])? $TH[$event_id]['all']: false) : $TH[$event_id][$ri];
			if( !$R) return false;

			if( !isset($R['names'])) return false;

			if( !isset($R['names'][$index])) return false;
			if( !isset($R['names'][$index][$instance])) return false;

			return $R['names'][$index][$instance];
		}


	// update attendee information
		function _update_ticket_holder($ticket_data_json, $THD){
			$TD = $ticket_data_json;
			
			if(!($TD)) return false;

			//update_post_meta(640,'aa', $TD);
			if(!isset($TD->event_instance)) return false;

			$OTH = get_post_meta($TD->order_id, '_tixholders', true);
			
			if(!$OTH && !is_array($OTH)) return false;

			foreach($OTH as $e=>$ris){
				if( $e != $TD->event_id) continue;

				// new method
				if(is_array($ris)){
					
					if( is_array($OTH[$TD->event_id][$TD->ri][$TD->Q][$TD->event_instance])){
						foreach($THD as $F=>$V){
							$OTH[$TD->event_id][$TD->ri][$TD->Q][$TD->event_instance][$F] = $V;
						}
					}else{
						//$OTH[$TD->event_id][$TD->ri][$TD->Q][$TD->event_instance] = $ticket_holder_name;
						$OTH[$TD->event_id][$TD->ri][$TD->Q][$TD->event_instance] = 
							array('name' => $THD['name'] );
					}
					
				}			
			}

			update_post_meta( $TD->order_id, '_tixholders', $OTH);
			return true;

		}

// SUPPORTIVE
	private function get_prop($field){
		return get_post_meta($this->evotix_id, $field, true);
	}
	private function _get_ri(){
		$ri = $this->get_prop('repeat_interval');
		return ($ri)? $ri:0;
	}
}