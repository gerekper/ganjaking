<?php
/*
	Sales Insight for Tickets
*/

class EVOTX_Sales_Insight{
	function get_insight(){
		ob_start();

		$event_id = $_POST['event_id'];

		date_default_timezone_set('UTC');

		$EVENT = new evotx_event($event_id);
		$curSYM = get_woocommerce_currency_symbol();

		// event time
			if( !$EVENT->is_repeating_event()){
				?>
				<div class='evotxsi_row timetoevent'>
					<?php if( $EVENT->is_current_event('start')):
	
						$timenow = current_time( 'timestamp' );

						$start = $EVENT->get_prop('evcal_srow');

						$dif = $start - $timenow;

					?>
						<p><?php _e('Time left till event start','evotx');?> <span class='static_field'><?php echo $this->get_human_time($dif);?></span></p>
					<?php else:?>
						<p><?php _e('Event has already started!','evotx');?></p>
					<?php endif;?>				
				</div>
				<?php
			}

		// sales by ticekt order
			$remainging_tickets = is_bool( $EVENT->has_tickets() )? 0: $EVENT->has_tickets();
			$orders = new WP_Query(array(
				'post_type'=>'evo-tix',
				'posts_per_page'=>-1,
				'meta_query'=>array(
					array(
						'key'=>'_eventid',
						'value'=>$event_id
					)
				)
			));

			$sales_data = array();
			$total_tickets_sold = 0;
			$total_tickets_sold_ready = 0;
			$checked_count = 0;

			$processed_order_ids = array();

			if($orders->have_posts()):
				while($orders->have_posts()): $orders->the_post();

					$order_id = get_post_meta($orders->post->ID, '_orderid', true);

					// checked count
						$status = get_post_meta($orders->post->ID, 'status', true);
						if( $status == 'checked') $checked_count ++;

					// check if order post exists
					$order_status = get_post_status($order_id);
					if(!$order_status) continue;

					// Process 1 order once only
						if(in_array($order_id, $processed_order_ids)) continue;
						$order = new WC_Order( $order_id );	
						if(sizeof( $order->get_items() ) <= 0) continue;
				
					// for each ticket item in the order
						$_order_qty = $_order_cost = 0;

					// order information
						$order_time = get_the_date('U', $order_id);
						$billing_country = get_post_meta($order_id, '_billing_country',true);
						$order_status = $order->get_status();

					// foreach order item  ticket sold
					foreach($order->get_items() as $item_id=>$item){
						$_order_event_id = ( isset($item['_event_id']) )? $item['_event_id']:'';
						$_order_event_id = !empty($_order_event_id)? $_order_event_id: get_post_meta( $item['product_id'], '_eventid', true);				    		
				    	if(empty($_order_event_id)) continue; // skip non ticket items

				    	if($_order_event_id != $event_id) continue;


				    	$_order_qty += (int)$item['qty'];
				    	$_order_cost += floatval($item['subtotal']);

				    	$sales_data[$item_id] = apply_filters('evotx_sales_insight_data_item',
				    		array(
					    		'qty'=> (int)$item['qty'],
					    		'cost'=> floatval($item['subtotal']),
					    		'order_id'=> $orders->post->ID,
					    		'time'=> $order_time,
					    		'country'=>$billing_country,
					    		'order_status'=> $order_status,
				    	), $item_id, $item, $EVENT, $order);

					}

					// completed & ready to go orders
					if( $order_status == 'completed'){
						$total_tickets_sold_ready += $_order_qty ;
					}

					$total_tickets_sold += $_order_qty;
					$processed_order_ids[] = $order_id;				


				endwhile;
				wp_reset_postdata();
			endif;


		//print_r($sales_data);

		// sales by order status
		if(sizeof($sales_data)>0){

			?>
			<div class='evotxsi_row sales_by_status'>
				<h2 style='margin:10px 0 30px; font-weight:bold'><?php _e('Ticket sales by ticket order status','evotx');?></h2>				
				<p>
				<span>
					<b><?php echo $total_tickets_sold + $remainging_tickets ;?></b>
					<em><?php echo $remainging_tickets==0? __('No capacity limit','evotx'):'';?></em>
					<?php _e('Total Event Capacity','evotx');?>
				</span>
				
				<?php foreach(array(
					'wc-completed'=> __('Tickets Sold','evotx'),
					'wc-onhold'=> __('Pending','evotx'),
					'wc-cancelled'=> __('Cancelled','evotx'),
					'wc-refunded'=> __('Refunded','evotx'),

				) as $type=>$name):?>
				<span class='<?php echo $type;?>'>
					<?php
						$_qty = $_cost = 0;
						foreach($sales_data as $oiid=>$d){

							if( $type == 'wc-onhold'){
								if(!in_array('wc-'.$d['order_status'], array('wc-on-hold','wc-pending','wc-processing','wc-failed')) ) continue; 
							}else{
								if('wc-'.$d['order_status'] != $type) continue;
							}
							

							$_qty += (int)$d['qty'];
							$_cost += floatval($d['cost']);
						}
					?>
					<b><?php echo $_qty;?></b><em><?php echo $curSYM.number_format($_cost,2,'.','');?></em>
					<i><?php echo $name;?></i>
				</span>
				<?php endforeach;?>
				</p>
			</div>

			<div class='evotxsi_row sales_by_status'>
				<h2 style='margin:10px 0 30px; font-weight:bold'><?php _e('Guest Attendance Data','evotx');?></h2>				
				<p>				
				
				<?php 
				$attendance_perc = 0;
				if( $total_tickets_sold_ready >0 )
					$attendance_perc = round( ($checked_count/$total_tickets_sold_ready) *100 , 2) ;

				foreach(array(
					'completed'=> array( __('Tickets Sold','evotx'), $total_tickets_sold_ready),
					'checked'=> array( __('Checked in Count','evotx'), $checked_count),
					'perc'=> array( __('Attendance %','evotx'), $attendance_perc.'%'),
				) as $FF=>$VV):?>
				<span class='<?php echo $type;?>'>					
					<b><?php echo $VV[1];?></b></em>
					<i><?php echo $VV[0];?></i>
				</span>
				<?php endforeach;?>
				</p>

			</div>
			<div class='evotxsi_row sales_by_time'>
				<h2 style='font-weight:bold'><?php _e('Ticket sales based on the time of ticket sale','evotx');?></h2>	
				<h3><?php _e('* Time in relation to current time','evotx');?></h3>			
				<p style='padding:30px 50px'>
				<?php		

					// time adjust markup
					//$event_start = $EVENT->get_event_time('start');	

					$time_adjust = current_time('timestamp');

					foreach(array(
						array(4838400,10000000,__('2+ Month ago','evotx')),
						array(2419200,4838400,__('1-2 Month ago','evotx')),
						array(1209600,2419200,__('2-4 Weeks ago','evotx')),
						array(604800,1209600,__('1-2 Weeks ago','evotx')),
						array(259200,604800,__('3-7 Days Ago','evotx')),
						array(86400,259200,__('1-3 Days Ago','evotx')),
						array(0,86400,__('Within 1 Day','evotx')),
					) as $val){

						$_qty = $_cost = 0;

						$index = 0;
						foreach( $sales_data as $oiid=>$d){
							$order_time = $time_adjust - $d['time'] ;


							// if order start is equal or greater and order end if less than
							if( $order_time >= $val[0] && $order_time < $val[1] ){
								$_qty += $d['qty'];
								$_cost += $d['cost'];
							}
							$index++;
						}

					$total = $total_tickets_sold + $remainging_tickets;
					$width = ($total_tickets_sold==0)? 0: number_format( (($_qty/$total) *100), 2);

				?>
					<span><b><?php echo $val[2];?></b>
					<em><b style='width:<?php echo $width;?>%'></b></em>
					<i><b><?php echo $_qty;?></b> <?php echo $curSYM.number_format($_cost,2,'.','');?></i>
					</span>
				<?php
					}
				?>
				</p>
			</div>
			<div class='evotxsi_row sales_by_country'>
				<h2 style='font-weight:bold'><?php _e('Sales by customer location','evotx');?></h2>	
				<h3><?php _e('Top 3 countries where customers have placed orders from','evotx');?></h3>			
				<p style='padding-top:10px'>
				<?php	
										
					$_country_data = array();
					
					foreach( $sales_data as $oiid=>$d){

						if(!isset($d['country'])) continue;

						$_country_data[ $d['country']]['qty'] = isset($_country_data[ $d['country']]['qty'])?
							$_country_data[ $d['country']]['qty'] + $d['qty'] : $d['qty'];

						$_country_data[ $d['country']]['cost'] = isset($_country_data[ $d['country']]['cost'])?
							$_country_data[ $d['country']]['cost'] + $d['cost'] : $d['cost'];
						
					}

					//$_country_data['CA']= array('qty'=>'3','cost'=>'70');
					//$_country_data['SL']= array('qty'=>'12','cost'=>'120');

					$country_qty = array();
					foreach($_country_data as $key=>$row){
						$country_qty[ $key] = $row['qty'];
					}

					array_multisort( $country_qty, SORT_DESC,$_country_data );
					
					$index = 0;
					foreach($_country_data as $country=>$data){
					?>
					<span style='opacity:<?php echo 1- ($index*0.3);?>'>
						<em><?php echo empty($country)? 'n/a': $country;?></em>
						<b><?php echo $data['qty'];?></b>
						<i><?php echo $curSYM. number_format($data['cost'], 2, '.','');?></i>
					</span>
					<?php
					$index++;
					}

				?>
				</p>
			</div>

			<div class='evotxsi_row sales_msg'><p><?php _e('NOTE: Sales insight data is calculated using evo-tix posts as primary measure count.','evotx');?></p></div>
			<?php
		}

		do_action('evotx_sales_insight_after', $EVENT, $orders, $sales_data);

		return ob_get_clean();
	}

	// return time difference in d/h/m
		function get_human_time($time){

			$output = '';
			$day = $time/(60*60*24); // in day
			$dayFix = floor($day);
			$dayPen = $day - $dayFix;
			if($dayPen > 0)
			{
				$hour = $dayPen*(24); // in hour (1 day = 24 hour)
				$hourFix = floor($hour);
				$hourPen = $hour - $hourFix;
				if($hourPen > 0)
				{
					$min = $hourPen*(60); // in hour (1 hour = 60 min)
					$minFix = floor($min);
					$minPen = $min - $minFix;
					if($minPen > 0)
					{
						$sec = $minPen*(60); // in sec (1 min = 60 sec)
						$secFix = floor($sec);
					}
				}
			}
			$str = "";
			if($dayFix > 0)
				$str.= $dayFix." day ";
			if($hourFix > 0)
				$str.= $hourFix." hour ";
			if($minFix > 0)
				$str.= $minFix." min ";
			//if($secFix > 0)	$str.= $secFix." sec ";
			return $str;
		}

}