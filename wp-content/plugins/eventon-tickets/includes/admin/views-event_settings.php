<?php
/** 
 * Ticket Event Settings
 * @version 2.1.1
 */

$settings = new EVO_Settings();

global $evotx_admin;


// initial values
	$woo_product_id = $EVENT->get_wcid();
	// if woocommerce ticket has been created
		$the_product = $woometa = '';
		if($woo_product_id){
			$woometa =  get_post_custom($woo_product_id);
			$the_product = wc_get_product($woo_product_id);
		}
		$__woo_currencySYM = get_woocommerce_currency_symbol();

	$product_type = 'simple';

	// product type
	$product_type = $evotx_admin->get_product_type($woo_product_id);
	$product_type = (!empty($product_type))? $product_type: 'simple';

	$repeat_intervals = $EVENT->get_repeats();


$fields = array();

// price
	$fields['price_type'] = array(
		'id'=>'price_type',
		'type'=>'notice',
		'name'=> __('Product Type','evotx') .': <b>'. $product_type .'</b>',
	);
	$fields['wcid'] = array(
		'id'=>'wcid',
		'type'=>'notice',
		'name'=> __('Associated Woocommerce Product ID','evotx') .': <b>'. $woo_product_id .'</b>',
	);
	if(!empty($product_type) && !empty($the_product) && $product_type=='variable'):

		ob_start();
			echo $the_product->get_price_html() ? $the_product->get_price_html() : '&ndash;';
		$price_html = ob_get_clean();

		$fields['price'] = array(
			'id'=>'price',
			'type'=>'notice',
			'name'=>  __('Ticket Price', 'evotx').' '.__('(Required*)', 'evotx') .' ('.$__woo_currencySYM .'): ' . $price_html ,
		);
	else:
		$fields['_regular_price'] = array(
			'id'=>'_regular_price',
			'type'=>'input',
			'name'=> __('Ticket Price', 'evotx').' '.__('(Required*)', 'evotx') .' ('.$__woo_currencySYM .')',
			'value'=> evo_meta($woometa, '_regular_price'),
			'tooltip'=> __('Ticket price is required for tickets product to add to cart otherwise it will return an undefined error.','evotx')
		);$fields['_sale_price'] = array(
			'id'=>'_sale_price',
			'type'=>'input',
			'name'=> __('Sale Price', 'evotx').' ('.$__woo_currencySYM .')',
			'value'=> evo_meta($woometa, '_sale_price'),
		);
	endif;

// SKU & description
	$fields['_sku'] = array(
		'id'=>'_sku',
		'type'=>'input',
		'name'=> __('Ticket SKU', 'evotx').' '.__('(Required*)', 'evotx'),
		'value'=> $EVENT->get_wc_prop('_sku'),
		'tooltip'=> __('SKU refers to a Stock-keeping unit, a unique identifier for each distinct menu item that can be ordered. You must enter a SKU or else the tickets might not function correct.','evotx')
	);
	$fields['_tx_desc'] =array(
		'id'=>'_tx_desc',
		'type'=>'input',
		'name'=> __('Short Ticket Detail', 'evotx'),
		'value'=> $EVENT->get_wc_prop('_tx_desc'),
	);

// name your price
	if(empty($product_type) || $product_type == 'simple'):
		$fields['_name_yprice'] = array(
			'id'=>'_name_yprice',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_name_yprice'),
			'name'=> __('Enable name your price','evotx'),
			'tooltip'=> __('When name your price is enabled, customer will be able to set his own price and the ticket price will be ignored','evotx'),
			'afterstatement'=>'_evotx_nyp_min'
		);

		$fields['_evotx_nyp_min1'] = array(
			'type'=>'begin_afterstatement','id'=>'_evotx_nyp_min','value'=> $EVENT->get_prop('_name_yprice'),
		);
		$fields['_evotx_nyp_min'] =array(
			'id'=>'_evotx_nyp_min',
			'type'=>'input',
			'name'=> __('Minimum allowed price','evotx'),
			'value'=> $EVENT->get_prop('_evotx_nyp_min'),
			'tooltip'=>__('This will make sure customers can not name a price below this value.','evotx')
		);
		$fields['_evotx_nyp_min2'] = array(	'type'=>'end_afterstatement');
	endif;

// Manage your stock
	$fields['_manage_stock'] = array(
		'id'=>'_manage_stock',
		'type'=> 'yesno',
		'value'=> $EVENT->get_wc_prop('_manage_stock'),
		'name'=> __('Manage Ticket Stock','evotx'),
		'afterstatement'=>'_manage_stock1'
	);
	$fields['_manage_stock1'] = array(
			'type'=>'begin_afterstatement','id'=>'_manage_stock1','value'=> $EVENT->get_wc_prop('_manage_stock'),
	);
	$fields['_stock'] =array(
		'id'=>'_stock',
		'type'=>'input',
		'name'=> __('Total Tickets in Stock','evotx'),
		'value'=> $EVENT->get_wc_prop('_stock'),
		'tooltip'=>__('This is how many tickets you have currently in stock.','evotx')
	);

	// if repeating
	if( $EVENT->check_yn('evcal_repeat') && $product_type=='simple' && $repeat_intervals && count($repeat_intervals)>0 ):
		$manage_repeat_cap = $EVENT->check_yn('_manage_repeat_cap');
		$manage_repeat_cap = $manage_repeat_cap ? 'yes':'no';

		$fields['_manage_repeat_cap'] = array(
			'id'=>'_manage_repeat_cap',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_manage_repeat_cap'),
			'name'=> __('Manage capacity seperate for each repeating event','evotx'),
			'tooltip'=> __('This will show remaining tickets for this event on front-end','evotx'),
			'afterstatement'=>'evotx_ri_cap'
		);
		$fields['evotx_ri_cap'] = array(
			'type'=>'begin_afterstatement','id'=>'evotx_ri_cap','value'=> $EVENT->get_prop('_manage_repeat_cap') );

		ob_start();
		?>	
			<div class=''>
			<p><em style='opacity:0.6'><?php _e('NOTE: The capacity above should match the total number of capacity for each repeat occurance below for this event. Capacity is not supported for repeating events that have variations.','evotx');?></em></p>
			<?php
				$count =0;

				// get saved capacities for repeats
				$ri_capacity = $EVENT->get_prop('ri_capacity');

				//print_r($ri_capacity);
				//print_r($repeat_intervals);

				echo "<div class='evotx_ri_cap_inputs'>";

				// for each repeat interval
				$date_time = EVO()->calendar->date_format.' '.EVO()->calendar->time_format;

				foreach($repeat_intervals as $index=>$interval){
					$TIME  = $evotx_admin->get_format_time($interval[0]);
					$TIME = date($date_time, $interval[0] );

					echo "<p style=''><input type='text' name='ri_capacity[]' value='". (($ri_capacity && !empty($ri_capacity[$count]))? $ri_capacity[$count]:'0') . "'/> <span>#" . $index.' - '.$TIME . "</span></p>";
					$count++;
				}

				echo "</div>";

				echo (count($repeat_intervals)>5)? 
					"<p class='evotx_ri_view_more'><a class='button_evo'>Click here</a> to view the rest of repeat occurances.</p>":null;

				echo "</div>";
			
		$repeat_html = ob_get_clean();
		$fields['ri_capacity'] = array(
			'type'=>'code',
			'id'=>'ri_capacity',
			'content'=> $repeat_html
		);

		$fields['evotx_ri_cap2'] = array(	'type'=>'end_afterstatement');


	endif;

	$fields['_manage_stock2'] = array(	'type'=>'end_afterstatement');

// show remaining
	$fields['_show_remain_tix'] = array(
		'id'=>'_show_remain_tix',
		'type'=> 'yesno',
		'value'=> $EVENT->get_prop('_show_remain_tix'),
		'name'=> __('Show remaining tickets (Only for Woocommerce simple tickets)','evotx'),
		'tooltip'=> __('This will show remaining tickets for this event on front-end, ONLY if ticket stock is set.','evotx'),
		'afterstatement'=>'remaining_count'
	);
	$fields['remaining_count1'] = array(
			'type'=>'begin_afterstatement','id'=>'remaining_count','value'=> $EVENT->get_prop('_show_remain_tix'),
	);
	$fields['remaining_count'] =array(
		'id'=>'remaining_count',
		'type'=>'input',
		'name'=> __('Show remaining count at','evotx'),
		'value'=> $EVENT->get_prop('remaining_count'),
		'tooltip'=>__('Show remaining count when remaining count go below this number.','evotx')
	);
	$fields['remaining_count2'] = array(	'type'=>'end_afterstatement');

$fields['_tx_show_guest_list'] = array(
	'id'=>'_tx_show_guest_list',
	'type'=> 'yesno',
	'value'=> $EVENT->get_prop('_tx_show_guest_list'),
	'name'=> __('Show guest list for event on eventCard','evotx'),
);

// stock status
	$_stock_status = $EVENT->get_wc_prop('_stock_status','instock');
	$_stock_status_yesno = ( $_stock_status=='outofstock')? 'yes':'no';
	$fields['_stock_status'] = array(
		'id'=>'_stock_status',
		'type'=> 'yesno',
		'value'=> $_stock_status_yesno,
		'name'=> __('Place ticket on out of stock','evotx'),
		'tooltip'=> __('Set stock status of tickets. Setting this to yes would make tickets not available for sale anymore. This will also add sold out tag into event top, if not disabled in eventon settings.','evotx')
	);

// already purchased
	if($EVENT->wc_is_type('simple')):
	
	$fields['_already_purchased'] = array(
		'id'=>'_already_purchased',
		'type'=> 'yesno',
		'value'=> $EVENT->check_yn('_already_purchased'),
		'name'=> __('Show a message if a loggedin customer has purchased a ticket already','evotx'),
		'tooltip'=> __('If a logged in customer has purchased this event ticket it will show a message under ticket purchase section. The message text can be customized via eventON > language settings.','evotx')
	);
	endif;

// sold individually
	$fields['_sold_individually'] = array(
		'id'=>'_sold_individually',
		'type'=> 'yesno',
		'value'=> $EVENT->get_wc_prop('_sold_individually'),
		'name'=> __('Sold Individually','evotx'),
		'tooltip'=> __('Enable this to only allow one ticket per person','evotx')
	);

// show next available ticket on repeat
	if($EVENT->is_repeating_event() && $EVENT->is_ri_count_active()):
		$fields['_evotx_show_next_avai_event'] = array(
			'id'=>'_evotx_show_next_avai_event',
			'type'=> 'yesno',
			'value'=> $EVENT->get_prop('_evotx_show_next_avai_event'),
			'name'=> __('Show next available repeating instance of event','evotx'),
			'tooltip'=> __('This will allow a visitor to see the next available event in the repeating events series, if current repeating event is past and not available for sale. Only available if capacity managed separate for repeating events.','evotx')
		);
	endif;

// stop selling before x minutes
	EVO()->cal->set_cur('evcal_tx');
	$_tx_set = EVO()->cal->get_prop('evotx_stop_selling_tickets');
	$_txt = ($_tx_set =='start'|| !$_tx_set) ? __('start','evotx'): __('end','evotx');
	$fields['_xmin_stopsell'] = array(
		'id'=>'_xmin_stopsell',
		'type'=> 'input',
		'value'=> $EVENT->get_prop('_xmin_stopsell'),
		'name'=> __(sprintf('Stop selling tickets X minutes before event %s', $_txt),'evotx'),
		'tooltip'=> __(sprintf('This will hide selling tickets options X minutes before the event %s.',$_txt),'evotx')
	);

// subtitle
	$fields['_tx_text'] = array(
		'id'=>'_tx_text',
		'type'=> 'input',
		'value'=> $EVENT->get_wc_prop('_tx_text'),
		'name'=> __('Ticket Section Subtitle','evotx'),
		'tooltip'=> __('This text will appear right under the ticket section title in eventcard','evotx')
	);
	$fields['_tx_subtiltle_text'] = array(
		'id'=>'_tx_subtiltle_text',
		'type'=> 'textarea',
		'value'=> $EVENT->get_wc_prop('_tx_subtiltle_text'),
		'name'=> __('Ticket Field description','evotx'),
		'tooltip'=> __('Use this to type instruction text that will appear above add to cart section on calendar.','evotx')
	);

// image
	$fields['_tix_image_id'] = array(
		'id'=>'_tix_image_id',
		'type'=> 'image',
		'value'=> $EVENT->get_prop('_tix_image_id'),
		'name'=> __('Ticket Image','evotx'),
		'tooltip'=> __('NOTE: Ticket image added here will show next to add to cart section on event card. This image will also go in the WC ticket product as featured image. DO NOT set featured images for WC Ticket product, as that will get removed and replaced with this image.','evotx')
	);
	$fields['_tx_img_text'] = array(
		'id'=>'_tx_img_text',
		'type'=> 'input',
		'value'=> $EVENT->get_prop('_tx_img_text'),
		'name'=> __('Ticket Image Title','evotx'),
		'tooltip'=> __('This text will appear under the event image','evotx')
	);
// additional information
	$fields['_tx_add_info'] = array(
		'id'=>'_tx_add_info',
		'type'=> 'textarea',
		'value'=> $EVENT->get_prop('_tx_add_info'),
		'name'=> __('Additional Information visible to customer after ticket purchase.','evotx'),
		'tooltip'=> __('Details typed in here will be sent to customers vis confirmation email. This will only be sent once ticket purchase order is confirmed.','evotx')
	);

// inquire before buy
	$_tx_inq_email =  $EVENT->get_prop('_tx_inq_email' )? $EVENT->get_prop('_tx_inq_email' ): 
		( !empty($evoOpt['evotx_tix_inquiries_def_email'])? $evoOpt['evotx_tix_inquiries_def_email']: get_option('admin_email') );
	
	$fields['_allow_inquire'] = array(
		'id'=>'_allow_inquire',
		'type'=> 'yesno',
		'value'=> $EVENT->get_prop('_allow_inquire'),
		'name'=> __('Allow customers to submit inquiries.','evotx'),
		'tooltip'=> __('With this customers can submit inquiries via this form before buying tickets on front-end.','evotx'),
		'afterstatement'=>'_tx_inq_email'
	);
	$fields['_allow_inquire1'] = array(
			'type'=>'begin_afterstatement','id'=>'_tx_inq_email','value'=> $EVENT->get_prop('_allow_inquire'),
	);
		$fields['_tx_inq_email'] =array(
			'id'=>'_tx_inq_email',
			'type'=>'input',
			'name'=> __('Override Default Email Address to receive Inquiries','evotx'),
			'value'=> $EVENT->get_prop('_tx_inq_email'),
			'tooltip'=>__('Show remaining count when remaining count go below this number.','evotx'),
			'default'=>EVO()->cal->get_prop("evotx_tix_inquiries_def_email",'evcal_tx')
		);
		$fields['_tx_inq_subject'] =array(
			'id'=>'_tx_inq_subject',
			'type'=>'input',
			'name'=> __('Override Default Subject to receive Inquiries','evotx'),
			'value'=> $EVENT->get_prop('_tx_inq_subject'),
			'tooltip'=>__('Show remaining count when remaining count go below this number.','evotx'),
			'default'=>EVO()->cal->get_prop("evotx_tix_inquiries_def_subject",'evcal_tx')
		);
		$fields['_tx_inq_note'] =array(
			'id'=>'_tx_inq_note',
			'type'=>'notice',
			'name'=> __('NOTE: Front-end fields for Inquiries form can be customized from','evotx') . " <a style='color:#B3DDEC' href='". admin_url() ."admin.php?page=eventon&tab=evcal_2'>".__('EventON Languages','evotx') ."</a>",
			
		);
	$fields['_allow_inquire2'] = array(	'type'=>'end_afterstatement');


// full settings
$data_array =  array(
	'form_class'=>'evo_tix_event_settings',
	'container_class'=>'evo_tix pad20',
	'hidden_fields'=>array(
		'event_id'=>$EVENT->ID,
		'action'=>'evotx_save_event_tix_settings',
		'tx_product_type'=> $product_type,
		'tx_woocommerce_product_id'=> $woo_product_id,
	),
	'footer_btns'=> array(
		'save_changes'=> array(
			'label'=> __('Save Ticket Settings','eventon'),
			'data'=> array(
				'uid'=>'evotix_save_eventedit_settings',
				'lightbox_key'=>'config_tix_data',
				'hide_lightbox'=> 2000,
			),
			'class'=> 'evo_btn evolb_trigger_save'
		)
	),
	'fields'=> $fields
);

echo $settings->get_event_edit_settings( apply_filters('evotx_eventedit_fields_array', $data_array, $EVENT, $settings ) );