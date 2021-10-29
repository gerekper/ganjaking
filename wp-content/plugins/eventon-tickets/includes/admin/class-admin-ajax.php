<?php
/** 
 * AJAX for only backend of the tickets
 * @version 1.3.10
 */
class evotx_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'the_ajax_evotx_a1'=>'evotx_get_attendees',			
			'the_ajax_evotx_a3'=>'generate_csv',
			'the_ajax_evotx_a55'=>'admin_resend_confirmation',
			'evoTX_ajax_07'=>'get_ticektinfo_by_ID',
			'the_ajax_evotx_a8'=>'emailing_attendees_admin',
			'evotx_assign_wc_products'=>'assign_wc_products',
			'evotx_save_assign_wc_products'=>'save_assign_wc_products',
			'evotx_sales_insight'=>'evotx_sales_insight',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

// assign WC Product to event ticket
	function assign_wc_products(){
		$wc_prods = new WP_Query(array(
				'post_type'=>'product', 
				'posts_per_page'=>-1,
				'tax_query' => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => 'ticket',
					),
				),
			)
		);

		ob_start();

		?><div style='text-align:center;padding:15px'><?php

		if($wc_prods->have_posts()):
			?>
			<p><?php _e('Select a WC Product to assign this event ticket, instead of the already assigned WC Product','evotx');?><br/><br/>
			<i><?php _e('This event ticket is currently assigned to the below WC Product!','eventon');?></i><br/><code> (ID: <?php echo $_POST['wcid'];?>) <?php echo get_the_title($_POST['wcid']);?></code></p>

			<select class='field' name='evotx_wcid'><?php

			while($wc_prods->have_posts()): $wc_prods->the_post();

				$selected = (!empty($_POST['wcid']) && $wc_prods->post->ID == $_POST['wcid'])? 'selected="selected"':'';

				?><option <?php echo $selected;?> value="<?php echo $wc_prods->post->ID;?>">(ID: <?php echo $wc_prods->post->ID;?>) <?php the_title();?></option><?php
			endwhile;

			?></select>

				<br/><br/><p><i><?php _e('NOTE: When selecting a new WC Product be sure the product is published and can be assessible on frontend of your website','evotx');?></i></p>
				<p style='text-align:center; padding-top:10px;'>
					<span class='evo_btn evotx_submit_manual_wc_prod' data-eid="<?php echo $_POST['eid'];?>"><?php _e('Save Changes','eventon');?></span>
				</p>
				<?php

			wp_reset_postdata();
		else:
			?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
		endif;

		echo "</div>";

		echo json_encode(array('content'=>ob_get_clean(), 'status'=>'good')); exit;

	}

	function save_assign_wc_products(){
		$wcid = (int)$_POST['wcid'];
		$eid = (int)$_POST['eid'];

		update_post_meta( $eid, 'tx_woocommerce_product_id', $wcid);

		EVOTX()->functions->save_product_meta_values($wcid, $eid);
		EVOTX()->functions->assign_woo_cat($wcid);

		$msg = __('Successfully Assigned New WC Product to Event Ticket!','evotx');

		echo json_encode(array('msg'=> $msg, 'status'=>'good')); exit;
	}

// GET attendee list view for event
		function evotx_get_attendees(){	
			global $evotx;

			$nonce = $_POST['postnonce'];
			$status = 0;
			$message = $content = $json = '';

			if(! wp_verify_nonce( $nonce, 'evotx_nonce' ) ){
				$status = 1;	$message ='Invalid Nonce';
			}else{

				ob_start();

				$source = isset($_POST['source'])? $_POST['source']: false;
				$ri = (!empty($_POST['ri']) || $_POST['ri']=='0')? $_POST['ri']:'all'; // repeat interval

				$EA = new EVOTX_Attendees();
				$json = $EA->get_tickets_for_event($_POST['eid'], $source);


				if(!count($json)>0){
					echo "<div class='evotx'>";
					echo "<p class='header nada'>".__('Could not find attendees with completed orders.','evotx')."</p>";	
					echo "</div>";
				}
				
				$content = ob_get_clean();
			}

					
			$return_content = array(
				'attendees'=> array(
					'tickets'=>$json, 
					'od_gc'=>$EA->_user_can_check(),
					'source' =>$source, 
				),
				'temp'=> EVO()->temp->get('evotx_view_attendees'),
				'message'=> $message,
				'status'=>$status,
				'content'=>$content,
			);
			
			echo json_encode($return_content);		
			exit;
		}

// Download csv list of attendees
	function generate_csv(){

		$e_id = (int)$_REQUEST['e_id'];
		$EVENT = new EVO_Event($e_id);
		$EVENT->get_event_post();


		header('Content-Encoding: UTF-8');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$EVENT->post_name."_".date("d-m-y").".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo "xEFxBBxBF"; // UTF-8 BOM


		$EA = new EVOTX_Attendees();
		$TN = $EA->get_tickets_for_event($e_id);
		
		if($TN){

			//$fp = fopen('file.csv', 'w');
			$csv_header = apply_filters('evotx_csv_headers',array(
				'Name',
				//'Ticket Holder Name',
				'Email Address',
				'Company',
				'Address',
				'Phone',
				'Ticket IDs',
				'Quantity',
				'Ticket Type',
				'Event Time',
				'Order Status',
				'Ordered Date'
			), $EVENT);
			$csv_head = implode(',', $csv_header);
			echo $csv_head."\n";

			$index = 1;				
			
			// each customer
			foreach($TN as $tn=>$td){					
									
				$csv_data = apply_filters('evotx_csv_row',array(
					'name'=>	$td['n'],
					'email'=>	$td['e'],
					'company'=> '"'. isset($td['company'])? $td['company']:''.'"',
					'address'=> $td['aD'],
					'phone'=>	isset($td['phone'])? $td['phone']:'',
					'ticket_number'=>	$tn,
					'qty'=>				'1',
					'ticket_type'=> 	$td['type'],
					'event_time'=>		'"'.$td['oD']['event_time'].'"',
					'order_status'=>	$td['oS'],
					'ordered_date'=> '"'. isset($td['oD']['ordered_date'])? $td['oD']['ordered_date']:''.'"',
				), $tn, $td, $EVENT);

				// process each data row
				foreach($csv_data as $field=>$val){	echo $val . ",";	}

				echo "\n";					
			}
		}

	}

// Email attendee list to someone
	function emailing_attendees_admin(){
		global $evotx, $eventon;

		$eid = $_POST['eid'];
		$wcid = $_POST['wcid'];
		$type = $_POST['type'];
		$RI = !empty($_POST['repeat_interval'])? $_POST['repeat_interval']:'all'; // repeat interval
		$EMAILED = $_message_addition = false;
		$emails = array();
		$TA = new EVOTX_Attendees();

		// email attendees list to someone
		if($type=='someone'){

			// get the emails to send the email to
			$emails = explode(',', str_replace(' ', '', htmlspecialchars_decode($_POST['emails'])));

			$TH = $TA->_get_tickets_for_event($eid,'order_status');
			
			if(is_array($TH) && isset($TH['completed']) && count($TH['completed'])>0){
				ob_start();
				
				// get event date time
					$datetime = new evo_datetime();
					$epmv = get_post_custom($eid);
					$eventdate = $datetime->get_correct_formatted_event_repeat_time($epmv, ($RI=='all'?'0':$RI));

				echo "<p>Confirmed Guests for ".get_the_title($eid)." on ".$eventdate['start']."</p>";
				echo "<table style='padding-top:15px; width:100%;text-align:left'><thead><tr>
					<th>Ticket Holder</th>
					<th>Email Address</th>
					<th>Phone</th>
					<th>Ticket Number</th>
				</tr></thead>
				<tbody>";
				foreach($TH['completed'] as $tn=>$guest){
					echo "<tr><td>".$guest['n'] ."</td><td>".$guest['e']."</td><td>".$guest['phone']. "</td>
					<td>".$tn. "</td></tr>";
				}
				echo "</tbody></table>";
				$_message_addition = ob_get_clean();
			}else{
				ob_start();
				echo "<p>".__('There are no completed orders!','evotx')."</p>";
				$_message_addition = ob_get_clean();
			}

			//print_r($_message_addition);

		}elseif($type=='completed'){
			$TH = $TA->_get_tickets_for_event($eid,'order_status');
			foreach(array('completed') as $order_status){
				if(is_array($TH) && isset($TH[$order_status]) && count($TH[$order_status])>0){
					foreach($TH[$order_status] as $guest){
						$emails[] = $guest['e'];
					}
				}
			}
		}elseif($type=='pending'){
			$TH = $TA->_get_tickets_for_event($eid,'order_status');
			foreach(array('pending','on-hold') as $order_status){
				if(is_array($TH) && isset($TH[$order_status]) && count($TH[$order_status])>0){
					foreach($TH[$order_status] as $guest){
						$emails[] = $guest['e'];
					}
				}
			}
		}

		// emaling
		if($emails){	
			$email = new evotx_email();			
			$messageBODY = "<div style='padding:15px'>".
				(!empty($_POST['message'])? html_entity_decode(stripslashes($_POST['message']) ).'<br/><br/>':'' ).
				($_message_addition?$_message_addition:'') . "</div>";
				
			$messageBODY = $email->get_evo_email_body($messageBODY);
			$from_email = $email->get_from_email_address();

			$emails = array_unique($emails);

			$args = array(
				'html'=>'yes',
				'to'=> $emails,
				'type'=> ($type=='someone'? '':'bcc'),
				'subject'=>$_POST['subject'],
				'from'=>$from_email,
				'from_name'=> $email->get_from_email_name(),
				'from_email'=> $from_email,
				'message'=>$messageBODY,
			);

			//print_r($args);

			$helper = new evo_helper();
			$EMAILED = $helper->send_email($args);

		}			

		$return_content = array(
			'status'=> ($EMAILED?'0':'did not go'),	'other'=>$args
		);		
		echo json_encode($return_content);		
		exit;
	}

// Resend Ticket Email
// Used in both evo-tix and order post page
		function admin_resend_confirmation(){
			$order_id = false;
			$status = 'bad';
			$email = '';	

			// get order ID
			$order_id = (!empty($_POST['orderid']))? $_POST['orderid']:false;			
			$ts_mail_errors = array();

			if($order_id){

				// use custom email if passed or else get email to send ticket from order information
				$email = !empty($_POST['email'])? 
					$_POST['email']: 
					get_post_meta($order_id, '_billing_email',true);

				//print_r($email);

				if(!empty($email)){
					$evoemail = new evotx_email();
					$send_mail = $evoemail->send_ticket_email($order_id, false, false, $email);

					if($send_mail) $status = 'good';

					if(!$send_mail){
						global $ts_mail_errors;
						global $phpmailer;

						if (!isset($ts_mail_errors)) $ts_mail_errors = array();

						if (isset($phpmailer)) {
							$ts_mail_errors[] = $phpmailer->ErrorInfo;
						}
					}
				}				
			}	

			// return the results
			$return_content = array(
				'status'=> $status,
				'email'=>$email,
				'errors'=>$ts_mail_errors,
			);
			
			echo json_encode($return_content);		
			exit;
		}

// get information for a ticket number
	function get_ticektinfo_by_ID(){

		$tickernumber = $_POST['tickernumber'];

		// decode base 64
		if( $this->_is_base64encoded($tickernumber) ){
			$tickernumber = base64_decode( $tickernumber );
		}
		
		$content = $this->get_ticket_info($tickernumber);

		$return_content = array(
			'content'=>$content,
			'status'=> ($content? 'good':'bad'),
		);
		
		echo json_encode($return_content);		
		exit;

	}

		function _is_base64encoded($data){
			if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
		       return TRUE;
		    } else {
		       return FALSE;
		    }
		}

		function get_ticket_info($ticket_number){
			if(strpos($ticket_number, '-') === false) return false;

			$tixNum = explode('-', $ticket_number);

			if(!get_post_status($tixNum[0])) return false;

			$tixPMV = get_post_custom($tixNum[0]);

			ob_start();

			$evotx_tix = new evotx_tix();
			$EA = new EVOTX_Attendees();

			$tixPOST = get_post($tixNum[0]);
			$orderStatus = get_post_status($tixPMV['_orderid'][0]);
				$orderStatus = str_replace('wc-', '', $orderStatus);

			$ticket_holder = $EA->get_attendee_by_ticket_number($ticket_number);
			$ticket_status = isset($ticket_holder['s'])? $ticket_holder['s']: 'check-in';

			echo "<p><em>".__('Ticket Purchased By','evotx').":</em> {$tixPMV['name'][0]}</p>";

			// additional ticket holder associated names
				if(!empty($ticket_holder))
					echo "<p><em>".__('Ticket Holder','evotx').":</em> {$ticket_holder['n']}</p>";

			echo "<p><em>".__('Email Address','evotx').":</em> {$tixPMV['email'][0]}</p>
				<p><em>".__('Event','evotx').":</em> ".get_the_title($tixPMV['_eventid'][0])."</p>
				<p><em>".__('Purchase Date','evotx').":</em> ".$tixPOST->post_date."</p>
				<p><em>".__('Ticket Status','evotx').":</em> <span class='tix_status {$ticket_status}' data-tiid='{$tixNum[0]}' data-tid='{$ticket_number}' data-status='{$ticket_status}'>{$ticket_status}</span></p>
				<p><em>".__('Payment Status','evotx').":</em> {$orderStatus}</p>";

				// other tickets in the same order
				$otherTickets = $evotx_tix->get_other_tix_order($ticket_number);

				if(is_array($otherTickets) && count($otherTickets)>0){
					echo "<div class='evotx_other_tickets'>";
					echo "<p >".__('Other Tickets in the same Order','evotx')."</p>";
					foreach($otherTickets as $num=>$status){
						echo "<p><em>".__('Ticekt Number','evotx').":</em> ".$num."</p>";
						echo "<p style='padding-bottom:10px;'><em>".__('Ticekt Status','evotx').":</em> <span class='tix_status {$status}' data-tiid='{$tixNum[0]}' data-tid='{$num}' data-status='{$status}'>{$status}</span></p>";
					}
					echo "</div>";
				}

			return ob_get_clean();
		}

// Sales Insight
	function evotx_sales_insight(){

		include_once('class-admin_sales_insight.php');

		$insight = new EVOTX_Sales_Insight();

		$content =  $insight->get_insight();

		echo json_encode(array('content'=> $content, 'status'=>'good')); exit;
	}

	
}
new evotx_admin_ajax();