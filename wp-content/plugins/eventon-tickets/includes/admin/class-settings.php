<?php
/**
 * Tickets admin settings
 * @version 2.2
 */

class evotx_settings{

function content(){
	global $eventon;

	$eventon->load_ajde_backender();
			
	?>
		<form method="post" action=""><?php settings_fields('evoau_field_group'); 
				wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
		<div id="evcal_tx" class="evcal_admin_meta">	
			<div class="evo_inside">
			<?php

				$site_name = get_bloginfo('name');
				$site_email = get_bloginfo('admin_email');

				$cutomization_pg_array = apply_filters('evotix_settings_page_content', array(
				
					array(
						'id'=>'evotx','display'=>'show',
						'name'=>'General Ticket Settings',
						'tab_name'=>'General',
						'fields'=>array(
							array('id'=>'evotx_loggedinuser',
								'type'=>'yesno',
								'name'=>'Show ticket purchase only for loggedin users',
							),
								
							array('id'=>'evotx_cart_newwin',
								'type'=>'yesno',
								'name'=>'Open Checkout & View Cart buttons in new tab/window'
							),									
							array('id'=>'evotx_hide_thankyou_page_ticket',
								'type'=>'yesno',
								'name'=>'Hide ticket information on order completion thank you page.'
							),array('id'=>'evotx_hide_orderpage_ticket',
								'type'=>'yesno',
								'name'=>'Hide ticket information on order details page.'
							),
							array('id'=>'evotx_eventop_soldout_hide',
								'type'=>'yesno',
								'name'=>'Do NOT show eventtop "sold out" tag above event title, when tickets sold out.'
							),
							array('id'=>'evotx_eventop_eventover_hide',
								'type'=>'yesno',
								'name'=>'Do NOT show eventtop "Event Over" tag above event title, when events are past.'
							),
							array(
								'id'=>'evotx_restock',
								'type'=>'yesno',
								'name'=>'Auto re-stock tickets when orders are refunded or cancelled',
								'legend'=>'This will auto increase the event tickets quantity when orders are refunded or cancelled.',
								'afterstatement'=>'evotx_restock'
							),
							array('id'=>'evotx_restock','type'=>'begin_afterstatement'),
								
								array(
									'id'=>'evotx_restock_failed',
									'type'=>'yesno',
									'name'=>'Auto re-stock failed ticket orders',
									'legend'=>'This will auto increase the event tickets quantity when orders are failed.',
								),
								array('type'=>'notice','name'=>'NOTE: When refunding orders, you must NOT restock refunded items in woocommerce order, which will result in duplicate restocking. <a href="https://docs.myeventon.com/documentations/auto-re-stocking-refunded-cancelled-or-failed-orders/" target="_blank">Learn More >></a>'),
							
							array('id'=>'evotx_restock','type'=>'end_afterstatement'),		
							
							array(
								'id'=>'evotx_wc_prod_redirect',
								'type'=>'yesno',
								'name'=>'Always redirect individual WC ticket Product page to event page on frontend',
								'legend'=> 'Once activated this will always redirect ticket product pages to event pages on front-end (only)'
							),
							array(
								'id'=>'evotx_wc_addcart_redirect',
								'type'=>'dropdown',
								'options'=>array(
									'none'=>'Do not redirect',
									'nonemore'=>'Do not redirect, allow adding more tickets to cart',
									//'noneopen'=>'Do not redirect, leave success message open',
									'cart'=>'Cart Page',
									'checkout'=>'Checkout Page'
								),
								'name'=>'Upon add to cart redirect customer to',
								'legend'=> 'Select your customer experience after adding a ticket to cart.'
							),
							array(
								'id'=>'evotx_stop_selling_tickets',
								'type'=>'dropdown',
								'options'=>array(
									'start'=>'When Event Start',
									'end'=>'When Event Ends'
								),
								'name'=>'Default event ticket stop selling time base',
								'legend'=> 'This will set the default event ticket stop selling time base.'
							),

							array('id'=>'subheader','type'=>'sub_section_open',
								'name'=>__('Ticket WC Product Title','evotx')
							),
								array(
									'id'=>'evotx_wc_prodname_update',
									'type'=>'yesno',
									'name'=>'Update WC ticket product name upon event update from now on',
									'legend'=>'If this is enabled, when you change the event name it will reflect on ticket product title'
								),
								array(
									'id'=>'evotx_wc_prodname_structure',
									'type'=>'text',
									'name'=>'Structure of ticket product title -- Supports: <code>{sku}</code>, <code>{event_name}</code>, <code>{event_start_date}</code>, <code>{event_end_date}</code>',
									'legend'=>'When creating custom ticket product titles, please use {} so proper value will replace those fields. Changes made in here will apply new products created after the changes.',
									'default'=>'Ticket: {event_name} {event_start_date} - {event_end_date}'
								),
							array('type'=>'sub_section_close'),

							
							
							array('id'=>'evotx_tix_inquiries',
								'type'=>'sub_section_open',
								'name'=>'Ticket Inquiries Settings'
							),
								array('id'=>'evotx_tix_inquiries_def_email',
									'type'=>'text',
									'name'=>'Default Email Address to <b>Receive</b> Ticket Inquiries. eg. YourName &#60;you@mail.com&#62;','default'=>get_option('admin_email'), 
								),
								array('id'=>'evotx_tix_inquiries_def_subject','type'=>'text','name'=>'Default Subject for Ticket Inquiries Email','default'=>'New Ticket Sale Inquery'),

							array('type'=>'sub_section_close'),

							array('id'=>'subheader','type'=>'sub_section_open',
								'name'=>__('Event Manager (<a href="http://www.myeventon.com/addons/action-user/" target="_blank">ActionUser Addon</a> required)','evotx')
							),
								array('id'=>'evotx_checkin_guests',
									'type'=>'yesno',
									'name'=>'Allow users with permission to check-in guests via event manager',
									'legend'=>__('This will allow users who have permission within actionUser to edit events, also be able to check in guests for tickets','evotx')
								),
							array('type'=>'sub_section_close'),

							
							
					)),
					array(
						'id'=>'evotx2a',
						'name'=>'Checkout Additional Data Settings',
						'tab_name'=>'Checkout','icon'=>'cart-plus',
						'fields'=>array(
							array('id'=>'evotx_hideadditional_guest_names',
								'type'=>'yesno',
								'name'=>'Hide additional guest names',
								'legend'=> __('Setting this will hide additional guest name fields at checkout','evotx')
							),
							array(
								'id'=>'evotx_reqadditional_guest_names',
								'type'=>'yesno',
								'name'=>'Make additional guest names required in checkout'
							),
							array(
								'id'=>'evotx_add_fields', 
								'type'=>'checkboxes',
								'name'=>__('Additional checkout guest fields','evotx'),
								'options'=> apply_filters('evotx_additional_checkout_fields_settings', $this->_supportive_additional_checkout_fields())
							),	
							array('id'=>'evcal_fcx','type'=>'note','name'=>'Learn how to add more fields at checkout <a href="http://docs.myeventon.com/documentations/how-to-add-additional-fields-at-checkout/" target="_blank">http://docs.myeventon.com/documentations/how-to-add-additional-fields-at-checkout/</a>'),
					)),

					array(
						'id'=>'evotx2',
						'name'=>'Ticket Email Settings',
						'tab_name'=>'Emails','icon'=>'envelope',
						'fields'=>array(

							array(
								'id'=>'evotx_tix_email',
								'type'=>'yesno',
								'name'=>'Stop auto sending ticket confirmation email to customers',
								'legend'=>__('This will stop auto sending ticket email to customers upon their purchase of tickets. However it will still send out WC order complete and other WC auto emails.','evotx')
							),

							array('type'=>'sub_section_open','name'=>'Event Ticket Confirmation Email'),	
								array('id'=>'evotx_notfiemailfromN','type'=>'text','name'=>'"From" Name','default'=>$site_name),
								array('id'=>'evotx_notfiemailfrom','type'=>'text','name'=>'"From" Email Address' ,'default'=>$site_email),
								
								array('id'=>'evotx_notfiesubjest','type'=>'text','name'=>'Email Subject line','default'=>'Event Ticket'),

								array('id'=>'evotx_termsc','type'=>'text','name'=>'Terms & Conditions statement on bottom of ticket','default'=>'Terms and condition statement for the ticket','legend'=>'This text will go in the bottom of the ticket email ticket itself as terms and conditions.'),

								array('id'=>'evotx_conlink','type'=>'text','name'=>'Contact Us for questions Link URL in ticket email','default'=>site_url(),'legend'=>'This is the link used in ticket email footer for contact us for questions text. If left blank will use your website link.'),

								/*
								array('id'=>'evcal_fcx','type'=>'subheader','name'=>'Supported Dynamic Tags'),
								array('id'=>'evcal_fcx','type'=>'note',
									'name'=>'<code>{event-name}</code>'),
								*/

								array('id'=>'evcal_fcx','type'=>'subheader','name'=>'HTML Template'),
								array('id'=>'evcal_fcx','type'=>'note','name'=>'To override and edit the email template copy "eventon-tickets/templates/email/ticket_confirmation_email.php" to  "yourtheme/eventon/templates/email/tickets/ticket_confirmation_email.php.'),
							array('type'=>'sub_section_close'),
					)),

					array(
						'id'=>'evotx3',
						'name'=>'Ticket Extras',
						'tab_name'=>'Ticket Extra','icon'=>'ticket',
						'fields'=>array(
							array('id'=>'evcal_fcx','type'=>'subheader','name'=>'Search for ticket information by ticket number & Check-in those tickets.'),
							array('type'=>'customcode','code'=>$this->searchcustomcode() ),	
							array('id'=>'evcal_additional','type'=>'note',
								'name'=>__('Check out how your ticket sales are doing. - On this page, under Category select <b>Tickets</b>','evotx') . '<br/>
									<a href="'. get_admin_url(). 'admin.php?page=wc-reports&tab=orders&report=sales_by_category'. '" style="margin-top:5px;" class="evo_admin_btn btn_triad">'. __('Ticket Sales Report','evotx'). "</a>",
							),
						)
					)
				));
					
				$eventon->load_ajde_backender();		
				
				$evcal_opt = get_option('evcal_options_evcal_tx');

				print_ajde_customization_form($cutomization_pg_array, $evcal_opt);
			?>
		</div>
		</div>
		<div class='evo_diag'>
			<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" />
		</div>
		
		</form>	
	<?php
}

	// supportive
		private function _supportive_additional_checkout_fields(){
			$arr = array(
				'phone'=>__('Phone Number','evotx'),
				'email'=>__('Email Address','evotx'),			
			);

			return apply_filters('evotx_checkout_additional_fields_settings', $arr);
		}

// custom code for searching the ticket information by ticket number
	function searchcustomcode(){
		ob_start();

		echo "<div class='evotx_searchtix_section'>";
		echo "<p class='evotx_searchtix_box'><input type='text' placeholder='Type ticket ID'/><span id='evotx_find_tix'>Find Ticket</span></p>";

		echo "<p class='evotx_searchtix_msg' style='display:none'></p>";
		echo "<div class='evotx_searchtix'></div></div>";

		return ob_get_clean();
	}
}
