<?php

/**
 * evo-tix post html
 */

global $post, $evotx, $ajde;

wp_nonce_field( 'evotx_edit_post', 'evo_noncename_tix' );

$evotx_tix = $ET = new evotx_tix();
$HELPER = new evotx_helper();
$ET->evo_tix_id = $post->ID;

$TIX_CPT = new EVO_Evo_Tix_CPT( $post->ID );

$ticketItem_meta = $evotix_meta = $TIX_CPT->get_props();
$event_id = $TIX_CPT->get_event_id();	
$repeat_interval = 	$TIX_CPT->get_repeat_interval();
$event_meta = get_post_meta($event_id);
$tn = $ticket_number = $TIX_CPT->get_ticket_number();

//print_r($ticketItem_meta);
//print_r( get_post_custom(2059));


// Order data
$order_id = $ET->get_prop('_orderid');	
$order = new WC_Order( $order_id );	
$order_status = $order->get_status();

$EA = new EVOTX_Attendees();
$TH = $EA->_get_tickets_for_order($order_id);


//print_r($TH);

//print_r(get_post_meta($order_id,'_tixholders',true));
//print_r(get_post_meta($post->ID,'xx',true));


// new ticket number method in 1.7
	if( $ticket_number){
		if( isset($TH[$event_id][$tn]) ){
			$_TH = array();
			$_TH[$event_id][$tn] = $TH[$event_id][$tn];
			$TH = $_TH;
		} 
	}

// Debug email templates
	if(isset($_GET['debug']) && $_GET['debug']):
		
		$order_id = $TIX_CPT->get_order_id();
		$order = new WC_Order( $order_id);
		$tickets = $order->get_items();

		$order_tickets = $evotx_tix->get_ticket_numbers_for_order($order_id);

		$email_body_arguments = array(
			'orderid'=>$order_id,
			'tickets'=>$order_tickets, 
			'customer'=>'Ashan Jay',
			'email'=>'yes'
		);

		$email = new evotx_email();
		$tt = $email->get_ticket_email_body($email_body_arguments);
		print_r($tt);


	endif;

// get event times			
	$event_time = $evotx->functions->get_event_time($event_meta, $repeat_interval );

?>	
<div class='eventon_mb' style='margin:-6px -12px -12px'>
<div style='background-color:#ECECEC; padding:15px;'>
	<div style='background-color:#fff; border-radius:8px;'>
	<table width='100%' class='evo_metatable' cellspacing="" style='vertical-align:top' valign='top'>
		<tr><td><?php _e('Woocommerce Order ID','evotx');?> #: </td><td><?php 
			echo '<a class="button" href="'.get_edit_post_link($order_id).'">'.$order_id.'</a> <span class="evotx_wcorderstatus '.$order_status.'" style="line-height: 20px; padding: 5px 20px;">'.$order_status.'</span>';
		?></td></tr>
		<?php
		foreach( array(
			'type'=>__('Ticket Type','evotx'),
			'email'=>__('Order Email','evotx'),
			'qty'=>__('Quantity','evotx'),
			'cost'=>__('Cost for ticket(s)','evotx'),

		) as $k=>$v){
			$d = $ET->get_prop($k);	
			$d = !$d? '--': $d;

			if( $k=='cost') $d = $HELPER->convert_to_currency($d);
			?>
			<tr><td><?php echo $v;?>: </td><td><?php echo $d;?></td></tr><?php
		}
		?>
		<tr><td><?php _e('Event','evotx');?>: </td>
		<td><?php echo '<a class="button" href="'.get_edit_post_link($event_id).'">'.get_the_title($ticketItem_meta['_eventid'][0]).'</a>';?> 
			<?php
				// if this is a repeat event show repeat information						
				if(!empty($event_meta['evcal_repeat']) && $event_meta['evcal_repeat'][0]=='yes'){
					echo "<p>".__('This is a repeating event. Repeat Instance Index','evotx').': '. $ticketItem_meta['repeat_interval'][0]."</p>";
				}
			?>
		</td></tr>

		<?php if($ticket_number):?>
			<tr><td><?php _e('Ticket Number','evotx');?>: </td>
				<td><?php 	echo $ticket_number;	?></td>
			</tr>
		<?php endif;?>
		<tr><td><?php _e('Ticket Time','evotx');?>: </td><td><?php echo $event_time;?></td></tr>
		<?php
		// get translated checkin status
			$st_count = $evotx_tix->checked_count($post->ID);
			$status = $evotx_tix->get_checkin_status_text('checked');
			$__count = ': '.(!empty($st_count['checked'])? $st_count['checked']:'0').' out of '.$ticketItem_meta['qty'][0];
		?>				
		<tr><td><?php _e('Ticket Checked-in Status','evotx');?>: </td><td><?php echo $status.$__count; ?></td></tr>
		<?php
			// ticket purchased by
			$purchaser_id = $ET->get_prop('_customerid');
			$purchaser = get_userdata($purchaser_id);

			if($purchaser):					
		?>
			<tr><td><?php _e('Ticket Purchased by','evotx');?>: </td>
				<td><?php 	echo $purchaser->last_name.' '.$purchaser->first_name;	?></td>
			</tr>
		<?php endif;?>
		<?php
		// Ticket number instance
			$_ticket_number_instance = $ET->get_prop('_ticket_number_instance');
			//if($_ticket_number_instance):
		?>
			<tr><td><?php _e('Ticket Instance Index in Order','evotx');?>: <?php echo $ajde->wp_admin->tooltips('This is the event ticket instance index in the order. Changing this will alter ticket holder values. Edit with cautions!');?></td>
				<td class='evotx_edittable'><input style='width:100%' type='text' name='_ticket_number_instance' value='<?php 	echo $_ticket_number_instance;	?>'/>
				</td>
			</tr>
		<?php ?>
		<?php if($TH):

			$ticket_number_index = $ET->get_prop('_ticket_number_index');
			$ticket_number_index = $ticket_number_index? $ticket_number_index: '0';
			
			foreach(array(
				'order_id'=> $order_id,
				'event_id'=> $event_id,
				'ri'=> $repeat_interval,
				'Q'=>$ticket_number_index,
				'event_instance'=>$_ticket_number_instance
			) as $F=>$V){
				echo "<input type='hidden' name='{$F}' value='{$V}'/>";
			}

			//print_r($TH);
		?>
			<tr><td colspan='2'><b><?php _e('Additional Ticket Holder Information','evotx');?></b></td></tr>
			<tr><td><?php _e('Name','evotx');?>: </td>
				<td data-d=''>
					<input style='width:100%' type='text' name="_ticket_holder[name]" value='<?php 	echo $TH[$event_id][$ticket_number]['name'];	?>'/>
				</td>
			</tr>

			<?php if( isset($TH[$event_id][$ticket_number]['th']) && is_array($TH[$event_id][$ticket_number]['th']) && isset($TH[$event_id][$ticket_number]['th']['name']) ):

				unset($TH[$event_id][$ticket_number]['th']['name']);

				foreach($TH[$event_id][$ticket_number]['th'] as $f=>$v){
					?>
					<tr><td><?php echo $f;?>: </td>
						<td data-d=''>
							<input style='width:100%' type='text' name='_ticket_holder[<?php echo $f;?>]' value='<?php 	echo $v;	?>'/>
						</td>
					</tr>
					<?php
				}					


			endif;?>
		<?php endif;?>
		<?php						
			do_action('eventontx_tix_post_table',$post->ID, $ticketItem_meta, $event_id, $ET);
		?>
		<tr><td colspan='2'><b><?php _e('Other Information','evotx');?></b>					
			<div id='evotx_ticketItem_tickets' >
				<?php 
					if($TH):
						//print_r($TH);
						foreach($TH[$event_id] as $ticket_number=>$td):
							echo $EA->__display_one_ticket_data($ticket_number, $td, array(
								'orderStatus'=> $order_status,
								'showStatus'=>true,
								'guestsCheckable'=>$EA->_user_can_check(),	
							));
						endforeach;
					endif;
				?>
			</div>
		</td></tr>
		
	</table>
	</div>
</div>
</div>