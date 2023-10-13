<?php
/** 
 * AJAX for only backend of the tickets
 * @version 2.2
 */
class evotx_admin_ajax{
	public $help, $post_data;
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
			'evotx_sync_with_order'=>'evotx_sync_with_order',
			'evotx_emailing_form'=>'evotx_emailing_form',

			'evotx_get_event_tix_settings'=>'get_event_settings',
			'evotx_save_event_tix_settings'=>'save_event_settings',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );

			$nopriv_class = method_exists($this, 'nopriv_'. $class )? 'nopriv_'. $class: $class;
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $nopriv_class ) );
		}

		$this->help = new evo_helper();
		$this->post_data = $this->help->sanitize_array( $_POST );
	}

	// event settings
		function get_event_settings(){

			$EVENT = new evotx_event( $this->post_data['eid'] );

			ob_start();

			include_once('views-event_settings.php');

			echo json_encode(array(
				'status'=>'good',
				'content'=> ob_get_clean()
			));exit;

		}

		function save_event_settings(){

			global $evotx_admin;

			$post_data = $this->help->sanitize_array( $_POST);
			$EVENT = new evotx_event( $post_data['event_id'] );

			$woo_data = array('_manage_stock','_stock','_stock_status','_sold_individually','_tx_text','_tx_subtiltle_text','tx_woocommerce_product_id');

			// woo product update
				$create_new = false;

				// check if woocommerce product id exist
				if(isset($post_data['tx_woocommerce_product_id']) && !empty($post_data['tx_woocommerce_product_id'])){
					
					$wcid = (int)$post_data['tx_woocommerce_product_id'];

					$post_exists = $evotx_admin->post_exist($wcid);

					// make sure woocommerce stock management is turned on
						update_option('woocommerce_manage_stock','yes');								
					
					if($post_exists){
						$evotx_admin->update_woocommerce_product($wcid, $EVENT->ID);
					}else{
						$create_new = true;	
					}						
					
				}else{					

					// check if wc prod association already made
						$post_wc = $EVENT->get_wcid(); 
						
						if($post_wc){
							$wcid = (int)$post_wc;
							$post_exists = $evotx_admin->post_exist($wcid);

							if($post_exists){								
								$evotx_admin->update_woocommerce_product($wcid, $EVENT->ID);	
							}else{	$create_new = true;	}

						}else{	$create_new = true;	}					
				}


				// create new wc associate post
				if($create_new){				
					$wcid = EVOTX()->functions->add_new_woocommerce_product($EVENT->ID);
					$_stock_status = (!empty($post_data['_stock_status']) && $post_data['_stock_status']=='yes')? 'outofstock': 'instock';
					update_post_meta($wcid, '_stock_status', $_stock_status);
				}


			// save all values to event
			foreach($post_data as $key=>$val){
				if( in_array($key, $woo_data)) continue;
				$EVENT->set_prop( $key, $val);
			}
			// save woo product values
			foreach($woo_data as $key){
				if( !isset( $post_data[ $key ])) continue;

				$value = $post_data[$key];
				if( $key == '_stock_status' ){
					$value = (!empty($post_data['_stock_status']) && $post_data['_stock_status']=='yes')? 'outofstock': 'instock';
				}
				
				update_post_meta($wcid, $key , $value );
			}
			

			// Save repeat capacity
				if(!empty($post_data['ri_capacity']) && evo_settings_check_yn($post_data, '_manage_repeat_cap')){

					// get total
					$count = 0; 
					foreach($post_data['ri_capacity'] as $cap){
						$count = $count + ( (int)$cap);
					}
					// update product capacity
					update_post_meta( $wcid, '_stock',$count);
					$EVENT->set_prop('ri_capacity',$post_data['ri_capacity']);
				}

			echo json_encode(array(
				'status'=>'good',
				'content'=> '',
				'msg'=> __('Event Ticket Values Saved Successfully!')
			));exit;

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

		?><div class='evopad20' style=''><?php

		if($wc_prods->have_posts()):
			?>
			<form class='evotx_assign_wc_form'>
				<?php
					EVO()->elements->print_hidden_inputs( array(
						'eid'=> $this->post_data['eid'],
						'action'=>'evotx_save_assign_wc_products',
					));
				?>
				<p><?php _e('Select a WC Product to assign this event ticket, instead of the already assigned WC Product','evotx');?><br/><br/>
				<i><?php _e('This event ticket is currently assigned to the below WC Product!','eventon');?></i><br/><code> (ID: <?php echo $_POST['wcid'];?>) <?php echo get_the_title($_POST['wcid']);?></code></p>

				<select class='field' name='wcid'><?php

					while($wc_prods->have_posts()): $wc_prods->the_post();

						$selected = (!empty($_POST['wcid']) && $wc_prods->post->ID == $_POST['wcid'])? 'selected="selected"':'';

						?><option <?php echo $selected;?> value="<?php echo $wc_prods->post->ID;?>">(ID: <?php echo $wc_prods->post->ID;?>) <?php the_title();?></option><?php
					endwhile;

				?></select>

				<br/><br/><p><i><?php _e('NOTE: When selecting a new WC Product be sure the product is published and can be assessible on frontend of your website','evotx');?></i></p>
				
				<p><?php
				// send emails button
					EVO()->elements->print_trigger_element(array(
						'title'=>__('Save Changes','evotx'),
						'uid'=>'evotx_assign_wc_submit',
						'lb_class' =>'evotx_manual_wc_product',
					), 'trig_form_submit');
				?></p>
				
			</form>

			<?php
			wp_reset_postdata();

		else:
			?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
		endif;

		echo "</div>";

		echo json_encode(array('content'=>ob_get_clean(), 'status'=>'good')); exit;

	}

	function save_assign_wc_products(){
		$wcid = (int)$this->post_data['wcid'];
		$eid = (int)$this->post_data['eid'];

		$EVENT = new evotx_event( $eid );

		$current_wcid = $EVENT->get_wcid();

		if( $current_wcid == $wcid ){
			echo json_encode(array('msg'=> __('Already assigned to this Product','evotx'), 'status'=>'good')); 
			exit;
		}

		$EVENT->set_prop('tx_woocommerce_product_id', $wcid);

		EVOTX()->functions->save_product_meta_values($wcid, $eid);
		EVOTX()->functions->assign_woo_cat($wcid);

		$msg = __('Successfully Assigned New WC Product to Event Ticket!','evotx');

		echo json_encode(array('msg'=> $msg, 'status'=>'good')); exit;
	}

// GET attendee list view for event
		function evotx_get_attendees(){	
			global $evotx;

			$status = 0;
			$message = $content = $json = '';
			$filter_vals = array();

			$postdata = $this->post_data;

			ob_start();

			$source = isset($postdata['source'])? $postdata['source']: false;

			$event_id = sanitize_text_field($postdata['eid']);
			$ri = (isset($postdata['ri']) )? $postdata['ri']:'all'; // repeat interval

			$EA = new EVOTX_Attendees();
			$json = $EA->get_tickets_for_event( $event_id);


			if(!count($json)>0){
				echo "<div class='evotx'>";
				echo "<p class='header nada'>".__('Could not find attendees with completed orders.','evotx')."</p>";	
				echo "</div>";
			}else{

				/// get sorted event time list
				$event_start_time = array();
				foreach($json as $tidx=>$td){
					if(!isset($td['oD'])) continue;
					if(!isset($td['oD']['event_time'])) continue;
					

					$ET = $td['oD']['event_time'];
					if( strpos($ET, '-') !== false )$ET = explode(' - ', $ET);
					if( strpos($ET[0], '(') !== false ) $ET = explode(' (', $ET[0]);

					if( in_array($ET[0], $event_start_time)) continue;

					$event_start_time[ $td['oD']['event_time'] ] = $ET[0];
				}

				uasort($event_start_time, array($this, "compareByTimeStamp") );

				$filter_vals['event_time'] = $event_start_time;
			}
			
			$content = ob_get_clean();

			$return_content = array(
				'attendees'=> array(
					'tickets'=>$json, 
					'od_gc'=>$EA->_user_can_check(),
					'source' =>$source, 
				),
				'filter_vals'=> $filter_vals,
				'temp'=> EVO()->temp->get('evotx_view_attendees'),
				'message'=> $message,
				'status'=>$status,
				'content'=>$content,
			);		
			
			
			echo json_encode($return_content);		
			exit;
		}

		function compareByTimeStamp($time1, $time2){
		    if (strtotime($time1) < strtotime($time2))
		        return 1;
		    else if (strtotime($time1) > strtotime($time2)) 
		        return -1;
		    else
		        return 0;
		}

// Download csv list of attendees
	function nopriv_generate_csv(){
		echo "You do not have permission!";exit;
	}
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
	function evotx_emailing_form(){

		$post_data = $this->post_data;
		$EVENT = new evotx_event($post_data['e_id']);

		ob_start();?>
		<div id='evotx_emailing' class='' style=''>
			<form class='evotx_emailing_form'>
			<?php
				EVO()->elements->print_hidden_inputs( array(
					'eid'=>$EVENT->ID,
					'wcid'=>$EVENT->get_wcid(),
					'action'=>'the_ajax_evotx_a8',
				));
			?>
			<p><label><?php _e('Select emailing option','evotx');?></label>
				<select name="type" id="evotx_emailing_options">
					<option value="someone"><?php _e('Email Attendees List to someone','evotx');?></option>
					<option value="completed"><?php _e('Email only to completed order guests','evotx');?></option>
					<option value="pending"><?php _e('Email only to pending order guests','evotx');?></option>
				</select>
			</p>
			<?php
				// if repeat interval count separatly						
				if($EVENT->is_ri_count_active() && $EVENT->get_repeats() ){
					$repeat_intervals = $EVENT->get_repeats();
					if(count($repeat_intervals)>0){

						$datetime = new evo_datetime();
						$wp_date_format = get_option('date_format');
						$pmv = $EVENT->get_data();	

						echo "<p><label>". __('Select Event Repeat Instance','evotx')."</label> ";
						echo "<select name='repeat_interval' id='evotx_emailing_repeat_interval'>
							<option value='all'>".__('All','evotx')."</option>";																
						$x=0;								
						foreach($repeat_intervals as $interval){
							$time = $datetime->get_correct_formatted_event_repeat_time($pmv,$x, $wp_date_format);
							echo "<option value='".$x."'>".$time['start']."</option>"; $x++;
						}
						echo "</select>";
						echo EVO()->throw_guide("Select which instance of repeating events of this event you want to use for this emailing action.", '',false);
						echo "</p>";
					}
				}
			?>
			<p style='' class='text'>
				<label for=""><?php _e('Email Addresses (separated by commas)','evotx');?></label>
				<input name='emails' style='width:100%' type="text"></p>
			<p style='' class='subject'>
				<label for=""><?php _e('Subject for email','evotx');?> *</label>
				<input name='subject' style='width:100%' type="text"></p>
			<p style='' class='textarea'>
				<label for=""><?php _e('Message for the email','evotx');?></label>
				<textarea name='message' id='evotx_emailing_message' cols="30" rows="5" style='width:100%'></textarea>
				
			</p>
			<p><?php
			// send emails button
				EVO()->elements->print_trigger_element(array(
					'title'=>__('Send Email','evotx'),
					'uid'=>'evotx_send_emails',
					'lb_class' =>'evotx_emailing',
					'lb_loader'=>true
				), 'trig_form_submit');
			?></p>

		</form>
		</div>
		
		<?php $emailing_content = ob_get_clean();
		$return_content = array(
			'status'=> 'good',
			'content'=>$emailing_content,
		);
		
		echo json_encode($return_content);		
		exit;

	}
	function emailing_attendees_admin(){
		global $evotx, $eventon;

		$eid = $_POST['eid'];
		$wcid = $_POST['wcid'];
		$type = $_POST['type'];		
		$EMAILED = $_message_addition = false;
		$emails = array();

		// repeat interval
		$RI = !empty($_POST['repeat_interval'])? $_POST['repeat_interval']:'all'; 
		if( isset($_POST['repeat_interval']) && $_POST['repeat_interval'] == 0) $RI = '0'; 

		$TA = new EVOTX_Attendees();

		// email attendees list to someone
		if($type=='someone'){

			// get the emails to send the email to
			$emails = explode(',', str_replace(' ', '', htmlspecialchars_decode($_POST['emails'])));

			$TH = $TA->_get_tickets_for_event($eid,'order_status');

			
			//order completed tickets
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

				// create the attenee list
				foreach($TH['completed'] as $tn=>$guest){

					// repeat interval filter
					if( $RI != 'all' && $guest['ri'] != $RI) continue;

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

						// repeat interval filter
						if( $RI != 'all' && $guest['ri'] != $RI) continue;

						$emails[] = $guest['e'];
					}
				}
			}
		}elseif($type=='pending'){
			$TH = $TA->_get_tickets_for_event($eid,'order_status');
			foreach(array('pending','on-hold') as $order_status){
				if(is_array($TH) && isset($TH[$order_status]) && count($TH[$order_status])>0){
					foreach($TH[$order_status] as $guest){

						// repeat interval filter
						if( $RI != 'all' && $guest['ri'] != $RI) continue;

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
			'status'=> ($EMAILED?'good':'bad'),	
			'msg'=> $EMAILED ? __('Email Sent Successfully') : __('Could not send email'),
			'other'=>$args
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

// SYNC with order
	public function evotx_sync_with_order(){
		$order_id = sanitize_text_field( $_POST['oid']);

		$MM = '';

		$order = new WC_Order( $order_id );	

		$TIXS = new evotx_tix();

		$MM = 'Sync Completed';

		$TIXS->re_process_order_items( $order_id, $order);
		echo json_encode(array('message'=> $MM, 'status'=>'good')); exit;

	}

	
}
new evotx_admin_ajax();