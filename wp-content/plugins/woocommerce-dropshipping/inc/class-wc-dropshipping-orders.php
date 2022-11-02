<?php
class WC_Dropshipping_Orders {
	
	public function __construct() {
		$this->init();
	}
	public function init() {
		// order processing
		add_filter('wc_dropship_manager_send_order_email_html',array($this,'send_order_email_html'));
		add_filter('wc_dropship_manager_send_order_attachments',array($this,'send_order_attach_packingslip'),10,3);
		add_filter( 'woocommerce_product_get_price', array($this,'round_price_product'));
		add_action('woocommerce_order_actions',array( $this,'add_order_meta_box_order_processing'));
		add_action('woocommerce_order_status_processing',array($this,'order_processing'));
		add_action('woocommerce_order_status_completed',array($this,'order_complete'));
		//add_action( 'woocommerce_order_status_changed', array($this,'grab_order_old_status'), 10, 4 );
		add_action('woocommerce_order_status_cancelled',array($this,'order_cancelled'));
		add_action('woocommerce_order_action_resend_dropship_supplier_notifications',array($this,'order_processing'));
		add_action('wc_dropship_manager_send_order',array($this,'send_order'),10, 2);
		add_filter( 'wp_mail_content_type',array($this,'wpse27856_set_content_type') );
		add_action( 'woocommerce_email_order_meta', array($this, 'add_tracking_info_customer_email'), 10, 3 );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data', array($this,'order_item_get_formatted_meta_data'), 10, 1 );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'store_product_meta' ) );
		/* Filter by Supplier */ 
		add_action( 'restrict_manage_posts', array( $this, 'admin_shop_order_by_supplier_meta_filter' ) );
		add_action( 'pre_get_posts', array( $this, 'process_admin_shop_order_supplier_by_filter' ), 99, 1);
	}
	
	public function order_item_get_formatted_meta_data($formatted_meta){
		$options = get_option( 'wc_dropship_manager' );
		if(isset($options['hideorderdetail_suppliername'])) {
			$hideorderdetail_suppliername = $options['hideorderdetail_suppliername'];
		}
		else {
			$hideorderdetail_suppliername = '';
		}
		if($hideorderdetail_suppliername == '1'){
			$temp_metas = [];
			foreach($formatted_meta as $key => $meta) {
				if ( isset( $meta->key ) && ! in_array( $meta->key, ['supplier'] ) ) {
					$temp_metas[ $key ] = $meta;
				}
			}
			return $temp_metas;
		} else {
			return $formatted_meta;
		}
	}
	function wpse27856_set_content_type(){
	    return "text/html";
	}
    function round_price_product( $price ){
    // Return rounded price
	$price = floatval($price);
      return round( $price,2);
    }
	public function add_order_meta_box_order_processing( $actions ) {
		$actions['resend_dropship_supplier_notifications'] = 'Resend Notifications to Dropshipping Suppliers';
		return $actions;
	}
	public function order_complete( $order_id ) {
		$dropship_data = get_option( 'wc_dropship_manager' );
		$complete_email = $dropship_data['complete_email'];
		$fullinfo = $dropship_data['full_information'];
		if($fullinfo == '1' && $complete_email == '1' ) {
			$order = new WC_Order( $order_id ); // load the order from woocommerce
			$this->notify_warehouse($order); // notify the warehouse to ship the order
		}
	}


	// Below function to send an email when order status cancelled.
	
	/*public function grab_order_old_status( $order_id, $status_from, $status_to, $order ) {
		$order = new WC_Order( $order_id );
		
		if($status_from == "completed" && $status_to == "cancelled"){
           $this->notify_warehouse($order);
	   }elseif($status_from == "processing" && $status_to == "cancelled"){
			$this->notify_warehouse($order);
		}else{
			
		}
	}*/
		
	
	public function order_cancelled( $order_id ) { 
        $order = new WC_Order( $order_id ); // load the order from woocommerce
        $this->notify_warehouse($order);
    }
	/* Notify Suppliers */
	// perform all tasks that happen once an order is set to processing
	public function order_processing( $order_id ) {
		$order = new WC_Order( $order_id ); // load the order from woocommerce
		$this->notify_warehouse($order); // notify the warehouse to ship the order
	}
	public function get_dropship_option() {
		$dOptions = get_option('opmc_dropshipping_options');
		if( $dOptions !== false && is_array($dOptions) && !empty($dOptions) ) {
			return $dOptions;
		} else {
			return array();
		}
	}
	public function update_dropship_option($dOptions) {
		if(is_array($dOptions)) {
			update_option('opmc_dropshipping_options', $dOptions);
		}
	}
	// parse the order, build pdfs, and send orders to the correct suppliers
	public function notify_warehouse( $order ) {
		$order_info = $this->get_order_info($order);
		$supplier_codes = $order_info['suppliers'];
		$dKey = 'order_'.$order_info['id'];
		$dOptions = $this->get_dropship_option();
		$dOptions[$dKey]['shipping_status'] = 'processing';
		// for each supplier code, loop and send email with product info
		foreach($supplier_codes as $code => $supplier_info) {
			$dOptions[$dKey][$supplier_info['id']] = 'processing';
			do_action('wc_dropship_manager_send_order',$order_info,$supplier_info);
		}
		$this->update_dropship_option($dOptions);
	}
	public function get_order_shipping_info($order) {
		$keys = explode(',','shipping_first_name,shipping_last_name,shipping_address_1,shipping_address_2,shipping_city,shipping_state,shipping_postcode,shipping_country,billing_phone,shipping_company');
		$info =  array();
        $info['name'] = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
        $info['phone'] = $this->formatPhone($order->get_billing_phone());
		$info['shipping_method'] = $order->get_shipping_method();
		foreach($keys as $key) {
			if ( is_callable( array( $order, "get_{$key}" ) ) ) {
			$info[$key] = $order->{'get_'.$key}();
			}else{
				$info[$key] = '';
			}
		}
		return $info;
	}
	/**
	 * @param $order WC_Order
	 *
	 * @return array
	 */
	public function get_order_billing_info($order) {
		$keys = explode(',','billing_first_name,billing_last_name,billing_address_1,billing_address_2,billing_city,billing_state,billing_postcode,billing_country,billing_phone,billing_email,billing_company');
		$info =  array();
                $info['name'] = $order->get_billing_first_name().' '.$order->get_billing_last_name();
                $info['phone'] = $this->formatPhone($order->get_billing_phone());
		foreach($keys as $key) {
			if ( is_callable( array( $order, "get_{$key}" ) ) ) {
				$info[$key] = $order->{'get_'.$key}();
			}else{
				$info[$key] = '';
			}
		}
		return $info;
	}


	public function get_order_product_info($item,$product) {
		global $woocommerce;
		$woocommerce_price_decimal_sep = get_option( 'woocommerce_price_decimal_sep' );
        $woocommerce_price_thousand_sep = get_option( 'woocommerce_price_thousand_sep' );
		$info = array();
		$shipping_total = 0;
		$shipping_tax = 0;
		$info['sku'] = $product->get_sku();
		$info['id'] = $product->get_id();
		$order = wc_get_order($item['order_id']);
		$order_data = $order->get_data();
		$order_currency = get_post_meta( $item['order_id'], '_order_currency', true );
		$shipping_total = $order_data['shipping_total'];
		$shipping_tax = $order_data['shipping_tax'];
		$get_tax_total = $order->get_tax_totals();                
		$taxAmt = 0;
		foreach ($get_tax_total as $get_tax) {
			$taxAmt += $get_tax->amount;
		}
		$info['get_shipping_total'] = $shipping_total;
        $info['get_tax_total'] = $taxAmt;
        $info['qty'] = $item['qty'];
        $info['subtotal_tax'] = $item['subtotal_tax'];
        $info['total'] = $item['total'];
		$info['name'] = $item['name'];
		$product = wc_get_product( $product->get_id() );
		$product_get = wc_get_product( $item->get_product_id() );
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ) );
 		$ali_image = wp_get_attachment_url( $product->get_image_id() );
		$info['ali_image'] = $ali_image;
		if (!empty($thumbnail)){
			$info['imgurl'] = $thumbnail['0'];
		}else{
			$info['imgurl'] = plugin_dir_url( __FILE__ ).'no.png';
		}
		$currency_symbol = get_woocommerce_currency_symbol();
		$costofgoods = get_post_meta($product->get_id(), 'custom_field', true);
		$typeofpackage = get_post_meta($product->get_id(), 'custom_field_description', true);
		$dropship_options = get_option( 'wc_dropship_manager' );
		if($product_get->is_type('variable'))
		{
			$typeofpackage = get_post_meta($product->get_id(), 'custom_field_description', true);
		} else {
			$typeofpackage = get_post_meta($product->get_id(), '_custom_product_text_field_description', true);
		}
		if(empty($typeofpackage))
		{
			$info['typeofpackage'] = '';
		} else {
			$info['typeofpackage'] = $typeofpackage;
		}
		if($product_get->is_type('variable'))
		{
			$costofgoods = get_post_meta($product->get_id(), 'custom_field', true);
		}
		else
		{
			$costofgoods = get_post_meta($product->get_id(), '_cost_of_goods', true);
		}
		if(empty($costofgoods) || $dropship_options['cost_of_goods'] == '0')
		{
			$product_subtotal = $item->get_subtotal();
			$info['price'] = wc_price( $product_subtotal, array(  'currency' => $order_currency, 'decimal_separator' => $woocommerce_price_decimal_sep, 'thousand_separator' => $woocommerce_price_thousand_sep ) );
		}
		else
		{
			$totalprice = $costofgoods*$item['qty'];
			$info['price'] = wc_price( $totalprice, array( 'currency' => $order_currency, 'decimal_separator' => $woocommerce_price_decimal_sep, 'thousand_separator' => $woocommerce_price_thousand_sep ) );
		}
		$product_attributes = maybe_unserialize( get_post_meta( $product->get_id(), '_product_attributes', true ) );
		$info['product_attribute_keys'] = array();
		if(is_array($product_attributes)) {
			$info['product_attribute_keys'] = array_keys($product_attributes);
			foreach($product_attributes as $key=>$data) {
				$info[$key] = $data['value'];
			}
		}

		// Product Variations
		$info['variation_data'] = [];
		$info['variation_labels'] = [];
		$info['variation_name'] = [];
		$variation_attributes = [];
		if($product->is_type('variable'))
		{
			$info['variation_data'] = $product->get_variation_attributes();
			$variation = wc_get_product($item['variation_id']);
			$variation_att = $variation->get_variation_attributes();
			foreach ($variation_att as $key => $value) {
				$variation_attributes[] =$key.':'. $value;
			}
			$var_lab = str_replace("attribute_pa_"," ",$variation_attributes);
			$variation_label = implode(',',$var_lab);
			$info['variation_labels'] = $variation_label;
			$v_name = explode('- ', $info['name']);
			$info['variation_name'] = $v_name[0];

		}
		else
		{
			$info['variation_name'] =  $info['name'];
			$info['variation_labels'] ='';
		}
		// Product Add-Ons Plugin
		$info['order_item_meta'] = $item->get_formatted_meta_data();
        if(function_exists('get_product_addons')) {
			$info['product_addons'] = get_product_addons($product);
			foreach($info['order_item_meta'] as $key=>$item_meta)
            {
				$info['order_item_meta'][$key]->display_label = $this->get_addon_display_label($info['order_item_meta'][$key]);
			}
		}
		return $info;
	}

	private function get_addon_display_label($item_meta)
	{
		$d = $item_meta->display_key;
		// remove the price from the meta display name
		return trim(preg_replace('/\(\$\d.*\)/','',$d));
	}

	public function get_order_info($order) {
		$options = get_option( 'wc_dropship_manager' );
	    $hideorderdetail_suppliername = $options['hideorderdetail_suppliername'];
		$woocommerce_price_decimal_sep = get_option( 'woocommerce_price_decimal_sep' );
		$woocommerce_price_thousand_sep = get_option( 'woocommerce_price_thousand_sep' );
		// gather some of the basic order info
		$order_info = array();
		$order_info['custom_order_number'] = $order->get_order_number();
		$get_shipping_total = $order->get_shipping_total();
		$order_info['get_shipping_total'] = $get_shipping_total;
		$order_info['id'] = $order->get_id();
		$order_info['number'] = $order->get_order_number();
		$order_info['options'] = get_option( 'wc_dropship_manager' );
		$order_info['shipping_info'] = $this->get_order_shipping_info($order);
		$order_info['billing_info'] = $this->get_order_billing_info($order);
		$order_info['order'] = $order;
		$order_info['customer_note'] = $order->get_customer_note();
		// for each item determine what products go to what suppliers.
		// Build product/supplier lists so we can send send out our order emails
		$order_info['suppliers'] = array();
		$items = $order->get_items();
		if ( count( $items ) > 0 ) {
			foreach( $items as $item_id => $item ) {
               //if($hideorderdetail_suppliername != '1'){
				 $sup_name = get_post_meta($item['product_id'], 'supplier', true);
				 if($sup_name != "" || !empty($sup_name) || !is_null($sup_name)){
					wc_update_order_item_meta($item_id,'supplier',$sup_name);
				}
			   //}
				$supid = get_post_meta($item['product_id'], 'supplierid', true);
				if($supid != "" || !is_null($supid)){
					update_post_meta($item_id,'supplierid', $supid);
					update_post_meta($order->get_id(),'supplier_'.$supid,$sup_name);
				}
				$ds = wc_dropshipping_get_dropship_supplier_by_product_id( intval( $item['product_id'] ) );
				if ( isset($ds['id']) && $ds['id'] > 0 ) {
					$product = $item->get_product(); // get the product obj
					$prod_info = $this->get_order_product_info($item,$product);
					//Add tax label on order_info
					if (class_exists('WC_Tax')){
						$order_tax = new WC_Tax();
						foreach($order_tax::get_rates() as $order_tax_key => $order_tax_value){
							$order_info['tax_label'] = $order_tax_value['label'];
						}
					}
					if(!array_key_exists($ds['slug'],$order_info['suppliers']))
					{
						$order_info['suppliers'][$ds['slug']] = $ds;  // ...add newly found dropship_supplier to the supplier array
						$order_info[$ds['slug']] = array(); // ... and create an empty array to store product info in
					}
					$order_info[$ds['slug']][] = $prod_info;
					//$order_info[$ds['slug'].'_raw'][] = $product;
				}
			}
		} else {
			// how did we get here?
			//$this->sendAlert('No Products found for order #'.$order_info['id'].'!');
			//die;
		}
		return $order_info;
	}
	public function formatPhone($pnum) {
		return preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $pnum);
	}
	public function get_from_name() {
		return wp_specialchars_decode(get_option( 'woocommerce_email_from_name' ));
	}
	public function get_from_address() {
		return get_option( 'woocommerce_email_from_address' );
	}
	public function get_content_type() {
		return " text/html";
	}

	// for sending failure notifications
	public function sendAlert($text) {
		wp_mail( get_bloginfo('admin_email'), 'Alert from '.get_bloginfo('name'), $text );
	}
	public function make_directory( $path ) {
		$upload_dir = wp_upload_dir();
		$order_dir = $upload_dir['basedir'].'/'.$path;
		if( ! file_exists( $order_dir ) )
    			wp_mkdir_p( $order_dir );
		return $order_dir;
	}

	// generate packingslip PDF
	public function make_pdf($order_info,$supplier_info,$html,$file_name) {
		// Include TCPDF library
		if (!class_exists('TCPDF')) {
			require_once( wc_dropshipping_get_base_path() . '/lib/tcpdf_min/tcpdf.php' );
		}
		$options = get_option( 'wc_dropship_manager' );
		$logourl = $options['packing_slip_url_to_logo'];
		$fullinfo = $options['full_information'];
		$show_logo = $options['show_logo'];
		$bill = $options['billing_phone'];
		if ( isset($order_info['options']['packing_slip_header'] ) ){
			if ( '' !== $order_info['options']['packing_slip_header'] ){
				$from_name = $order_info['options']['packing_slip_header']. ' ' .$options['from_name'];
			}else{
				$from_name = $options['from_name'];
			}
		}else{
			$from_name = $options['from_name'];
		}
		$from_email = $options['from_email'];
		if(trim($from_name) == "")
		{
			$from_name = get_option( 'woocommerce_email_from_name' );
		}
		if(trim($from_email) == "")
		{
			$from_email = get_option( 'woocommerce_email_from_address' );
		}
		// make a directory for the current order (if it doesn't already exist)
		$pdf_path = $this->make_directory($order_info['custom_order_number']);
		// generate a pdf for the current order and the current supplier
		$file = $pdf_path.'/'.$file_name;
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$logo_image_width = $options['packing_slip_url_to_logo_width'];
		$str = $logo_image_width;
		$arr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$str);
		$logo_width  = $arr['0'];
		$logo_size_final = ( ($logo_width > 38 || empty($logo_width)) ? 38 .'px' : $options['packing_slip_url_to_logo_width'] );
		if($fullinfo == '1' && $logourl != '' && $show_logo == '1' ) {
			// set default header data
			$pdf->SetHeaderData($options['packing_slip_url_to_logo'], $logo_size_final, $from_name.' '.date('Y-m-d'));
		} elseif(($fullinfo == '1' && $logourl != '' && $show_logo == '1' )){
           $pdf->SetHeaderData($options['packing_slip_url_to_logo'], $logo_size_final, $from_name.' '.date('Y-m-d'));
		}
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// remove default header/footer
		//$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);  // set default monospaced font
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);  // set margins
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		if(get_bloginfo("language") === "zh-CN"){
		    $pdf->SetFont('kozminproregular', '', '');
		}
		//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM); // set auto page breaks
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);  // set image scale factor
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output($file, 'F'); // save PDF
		return $file;
	}
	
	// generate packing csv
	public function make_csv($order_info,$supplier_info,$html,$file_name) {

		$options = get_option( 'wc_dropship_manager' );
		if($options['csv_inmail'] == '1') {
			$order = new WC_Order( $order_info['id'] );
			$csv_path = $this->make_directory($order_info['custom_order_number']);
			$filepath = $csv_path.'/'.$file_name;
			$file = fopen($filepath, 'w+');

			//Store name
			if ($order_info['options']['store_name'] == 1){

				$blog_title = get_bloginfo( 'name' );
				fputcsv($file, array('Store Name: '.$blog_title) );
			}

			if ($order_info['options']['store_address'] == 1){

				$store_address     = get_option( 'woocommerce_store_address' );
				$store_address_2   = get_option( 'woocommerce_store_address_2' );
				$store_city        = get_option( 'woocommerce_store_city' );
				$store_postcode    = get_option( 'woocommerce_store_postcode' );

				// The country/state
				$store_raw_country = get_option( 'woocommerce_default_country' );

				// Split the country/state
				$split_country = explode( ":", $store_raw_country );

				// Country and state separated:
				$store_country = $split_country[0];
				$store_state   = $split_country[1];


				fputcsv($file, array('Website Address: '.$store_address . $store_address_2 . ', ' . $store_city . ', ' . $store_state . ' ' . $store_postcode . ', ' . $store_country));
			}
                        
                        $order_data = $order->get_data();
                        
                        $order_billing_first_name = $order_data['billing']['first_name'];
                        $order_billing_last_name = $order_data['billing']['last_name'];
                        $order_billing_company = $order_data['billing']['company'];
                        $order_billing_address_1 = $order_data['billing']['address_1'];
                        $order_billing_address_2 = $order_data['billing']['address_2'];
                        $order_billing_city = $order_data['billing']['city'];
                        $order_billing_state = $order_data['billing']['state'];
                        $order_billing_postcode = $order_data['billing']['postcode'];
                        $order_billing_country = $order_data['billing']['country'];
                        $order_billing_email = $order_data['billing']['email'];
                        $order_billing_phone = $order_data['billing']['phone'];

                        /**********************SHIPPING INFORMATION********************/

                        $order_shipping_first_name = $order_data['shipping']['first_name'];
                        $order_shipping_last_name = $order_data['shipping']['last_name'];
                        $order_shipping_company = $order_data['shipping']['company'];
                        $order_shipping_address_1 = $order_data['shipping']['address_1'];
                        $order_shipping_address_2 = $order_data['shipping']['address_2'];
                        $order_shipping_city = $order_data['shipping']['city'];
                        $order_shipping_state = $order_data['shipping']['state'];
                        $order_shipping_postcode = $order_data['shipping']['postcode'];
                        $order_shipping_country = $order_data['shipping']['country'];  

                        if (!empty($order_billing_address_2)) {
                            $fullBillAddress = $order_billing_address_1 . ', ' . $order_billing_address_2 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_shipping_postcode . ', ' . $order_billing_country;
                        } else {
                            $fullBillAddress = $order_billing_address_1 . ', ' . $order_billing_city . ', ' . $order_billing_state . ', ' . $order_billing_postcode . ', ' . $order_billing_country;
                        }
                        
                        if (!empty($order_shipping_address_2)) {
                            $fullShipAddress = $order_shipping_address_1 . ', ' . $order_shipping_address_2 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;
                        } else {
                            $fullShipAddress = $order_shipping_address_1 . ', ' . $order_shipping_city . ', ' . $order_shipping_state . ', ' . $order_shipping_postcode . ', ' . $order_shipping_country;
                        }

			//CSV headers
			$headers=array( 'Product Name', 'Product SKU', 'Product Quantity', 'Product Price', 'Product Description', 'Customer name', 'Customer Billing Address', 'Customer Shipping Address', 'Customer Email', 'Customer Tel Number');
			fputcsv( $file, $headers );
			foreach($order_info[$supplier_info['slug']] as $prod_info)
			{

                $product_description = get_post($prod_info['id'])->post_content;
				fputcsv($file, array( $prod_info['name'], $prod_info['sku'], $prod_info['qty'],html_entity_decode(strip_tags($prod_info['price'])),$product_description,$order_billing_first_name.' '.$order_billing_last_name,$fullBillAddress,$fullShipAddress,$order_billing_email,$order_billing_phone));

			}

			fclose($file);
			return $filepath;
		}
	}
	// get HTML packingslip
	public function get_packingslip_html($order_info,$supplier_info,$callfrom = false) {
		$html = '';
		$dropship_data = get_option( 'wc_dropship_manager' );
		$complete_email = $dropship_data['complete_email'];
		$order = wc_get_order($order_info['id']);
		if($order->get_status() == 'completed') {
                    if($complete_email == '1' ) {
                        $filename = 'complete.html';
                    }
                } else if($order->get_status() == 'cancelled') {
                    $filename = 'cancelled.html';
                } else {
                    $filename = 'packingslip.html';
		}
		/* if($callfrom === true){
			$filename = 'packingslip.html';
		} else {
			$filename = 'packingslip.html';
		} */
		if (file_exists(get_stylesheet_directory().'/woocommerce-dropshipping/'.$supplier_info['slug'].'_'.$filename))
		{
			/* 	User can create a custom supplier packingslip PDF by creating a "woocommerce-dropshipping" directory
				inside their theme's directory and placing a custom SUPPLIERCODE_packingslip.html there */
			$templatepath = get_stylesheet_directory().'/woocommerce-dropshipping/'.$supplier_info['slug'].'_'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/wc_dropship_manager/'.$supplier_info['slug'].'_'.$filename))
		{
			/* 	User can create a custom supplier packingslip PDF by creating a "dropship_manager" directory
				inside their theme's directory and placing a custom SUPPLIERCODE_packingslip.html there */
			$templatepath = get_stylesheet_directory().'/wc_dropship_manager/'.$supplier_info['slug'].'_'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/woocommerce-dropshipping/'.$filename))
		{
			/* 	User can override the default packingslip PDF by creating a "woocommerce-dropshipping" directory
				inside their theme's directory and placing a custom packingslip.html there */
			$templatepath = get_stylesheet_directory().'/woocommerce-dropshipping/'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/wc_dropship_manager/'.$filename))
		{
			/* 	User can override the default packingslip PDF by creating a "dropship_manager" directory
				inside their theme's directory and placing a custom packingslip.html there */
			$templatepath = get_stylesheet_directory().'/wc_dropship_manager/'.$filename;
		}
		else
		{
			if ( class_exists( 'WC_DS_Settings_Pro' ) ) {
			    $data ="";
			    $templatepath = apply_filters("add_packingslip_file_dropshipping_pro",$data);
			}
		    else{
			    $templatepath = wc_dropshipping_get_base_path() . $filename;
		        }
		}
		return $this->get_template_html($templatepath,$order_info,$supplier_info);
	}
	// get HTML packingslip
	public function get_packingslip_text($order_info,$supplier_info) {
		$html = '';
		$dropship_data = get_option( 'wc_dropship_manager' );
		$complete_email = $dropship_data['complete_email'];
		$order = wc_get_order($order_info['id']);
		if($order->get_status() == 'completed') {
                    if($complete_email == '1' ) {
                        $filename = 'complete.html';
                    }
		} else if($order->get_status() == 'cancelled') {
                    $filename = 'cancelled.html';
                } else {
                    $filename = 'packingslip_text.html';
		}
		if (file_exists(get_stylesheet_directory().'/woocommerce-dropshipping/'.$supplier_info['slug'].'_'.$filename))
		{
			/* 	User can create a custom supplier packingslip PDF by creating a "woocommerce-dropshipping" directory
				inside their theme's directory and placing a custom SUPPLIERCODE_packingslip.html there */
			$templatepath = get_stylesheet_directory().'/woocommerce-dropshipping/'.$supplier_info['slug'].'_'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/wc_dropship_manager/'.$supplier_info['slug'].'_'.$filename))
		{
			/* 	User can create a custom supplier packingslip PDF by creating a "dropship_manager" directory
				inside their theme's directory and placing a custom SUPPLIERCODE_packingslip.html there */
			$templatepath = get_stylesheet_directory().'/wc_dropship_manager/'.$supplier_info['slug'].'_'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/woocommerce-dropshipping/'.$filename))
		{
			/* 	User can override the default packingslip PDF by creating a "woocommerce-dropshipping" directory
				inside their theme's directory and placing a custom packingslip.html there */
			$templatepath = get_stylesheet_directory().'/woocommerce-dropshipping/'.$filename;
		}
		else if (file_exists(get_stylesheet_directory().'/wc_dropship_manager/'.$filename))
		{
			/* 	User can override the default packingslip PDF by creating a "dropship_manager" directory
				inside their theme's directory and placing a custom packingslip.html there */
			$templatepath = get_stylesheet_directory().'/wc_dropship_manager/'.$filename;
		}
		else
		{
			if ( class_exists( 'WC_DS_Settings_Pro' ) ) {
			  $data ="";
			  $templatepath = apply_filters("add_packingslip_file_dropshipping_pro",$data);
			}
		    else{
			  $templatepath = wc_dropshipping_get_base_path() . $filename;
		        }
		}
		return $this->get_template_html($templatepath,$order_info,$supplier_info);
	}
	public function get_template_html($templatepath,$order_info,$supplier_info) {
		$html = '';
		ob_start();
		if (file_exists($templatepath)){
			include($templatepath);
		} else {
			echo '<b>Template '.$templatepath.' not found!</b>';
		}
		$html = ob_get_clean();
		return $html;
	}
	// send the pdf to the supplier
	public function send_order($order_info,$supplier_info) {
            $options = get_option( 'wc_dropship_manager' );
            $order = wc_get_order($order_info['id']);
            $billing_address = $order->get_formatted_billing_address();
            $shipping_address = $order->get_formatted_shipping_address();
            update_post_meta($order_info['id'],'_billing_address',$billing_address);
            update_post_meta($order_info['id'],'_shipping_address',$shipping_address);
            if(function_exists('wcs_get_subscriptions_for_renewal_order')) {
                $renewal_order = wcs_get_subscriptions_for_renewal_order( $order_info['id'] );
                if( empty( $renewal_order ) ) {
                    $this->_send_order($order_info,$supplier_info);
                } else {
                    if ( isset( $options['renewal_email'] ) ) { 
                        if ( $options['renewal_email'] != 1 ) {
                            $this->_send_order($order_info,$supplier_info);
                        }
                    }
                }	
            } else {
                $this->_send_order($order_info,$supplier_info);
            }
	}
    public function _send_order($order_info,$supplier_info) {
		$order = wc_get_order($order_info['id']);
		$options = get_option( 'wc_dropship_manager' );
		$smtp_check = $options['smtp_check'];
		$std_mail 	= $options['std_mail'];
		$from_name = $options['from_name'];
		$from_email = $options['from_email'];
		$cc_email = $options['cc_mail'];
		$complete_url = $options['order_complete_link'];
		$order_email = $options['order_button_email'];
		$cnf_mail = $options['cnf_mail'];
		if(trim($from_name) == "")
		{
			$from_name = get_option( 'woocommerce_email_from_name' );
		}
		if(trim($from_email) == "")
		{
			$from_email = get_option( 'woocommerce_email_from_address' );
		}
		$fullinfo = $order_info['options']['full_information'];
		if($smtp_check == '1' || $std_mail == '1' || $std_mail == ''){
			$attachments = array();
			$attachments = apply_filters('wc_dropship_manager_send_order_attachments',$attachments,$order_info,$supplier_info);  // create a pdf packing slip file
				//$bill = $order_info['options']['billing_phone'];
				//array_push($attachments, $attachments['pdf_packingslip'] );
				//array_push($attachments, $attachments['csv_packingslip'] );
			$hdrs = array();
			$hdrs['From'] = $from_email;
			$hdrs['To'] = $supplier_info['order_email_addresses'];
			if($cc_email == '0'){
				$hdrs['CC'] = $from_email;
			}
			$textPlain = $this->get_packingslip_text($order_info,$supplier_info);
			$text = $this->get_packingslip_html($order_info,$supplier_info, false);
			$skipStatus = array('completed','cancelled');
			if ( !in_array($order->get_status(), $skipStatus) ) {
                $text = '<img style="max-width:150px;" class="email_store_logo" src="'.$options['packing_slip_url_to_logo'].'" /><br/>' . $options['email_order_note'] . $text;
			}
			$html = apply_filters('wc_dropship_manager_send_order_email_html',$text);
		 	if ($order->get_status() == 'completed') {
		  		$hdrs['Subject'] = 'Order #'.$order_info['number'].' is completed ';
			} else if ($order->get_status() == 'cancelled') {
		  		$hdrs['Subject'] = 'Order #'.$order_info['number'].' is cancelled ';
			} else {
				$hdrs['Subject'] = sprintf( __('New Order #%1$s From %2$s', 'woocommerce-dropshipping' ),$order_info['id'],	$from_name  );
			}
			//Mail Subject
			/*if ($order_status == 'completed') {
				$hdrs['Subject'] = 'Order #'.$order_info['id'].' is completed ';
			}else {
				$hdrs['Subject'] = 'New Order #'.$order_info['id'].' From '.$from_name;
			}*/
			$message = '';
			if ($order->get_status() != 'completed' || $order->get_status() != 'cancelled') {
				if($cnf_mail == '1') {
					$message .= '<img width="1" height="1" src="'.plugin_dir_url(__FILE__ ).'mail-track.php?orderid='.$order_info['id'].'&suppid='.$supplier_info['id'] . '&from='.$supplier_info['order_email_addresses'] .'&sup_name='.$supplier_info['name'] . '&random_value='.rand().'">';
				}
			}
			$message .= '<div style="background-color: '.( !empty(trim(get_option('woocommerce_email_background_color'))) ? get_option('woocommerce_email_background_color') : '#ccc' ).';">'.$html.'</div>';
			if($order->get_status() == 'processing') {
				if($complete_url == '1') {
					$message .= '<table cellpadding="8" cellspacing="0" style="width:100%;" >
			    	<tr><td style="text-align: center;">'.sprintf( __( 'To mark this order as shipped please click the following link:', 'woocommerce-dropshipping'  ),"" ).'<br/><a href="'.get_home_url().'/wp-admin/admin-ajax.php?action=woocommerce_dropshippers_mark_as_shipped&orderid='.$order_info["custom_order_number"].'&supplierid='.$supplier_info["id"].'">'.sprintf( __( 'Mark as shipped', 'woocommerce-dropshipping'  ),"" ).'</a></td></tr></table>';		
				}
				if($order_email == '1') {
					$message .= '<table cellpadding="8" cellspacing="0" style="width:100%;" >
			    	<tr><td style="text-align: center;"><a href="'.get_home_url().'/wp-admin/admin.php?page=dropshipper-order-list">'.sprintf( __('View order in dashboard', 'woocommerce-dropshipping' ),"" ).'</a></td></tr></table>';
		
				}
			}
			$headers  = "From: ".wp_specialchars_decode($from_name)." <".$from_email.">\r\n";
			if($cc_email == '0'){
				$headers .= "CC: ".$from_email."\r\n";
			}
			if ( isset( $options['supp_notification'] ) ) {
				if ( $options['supp_notification'] == 1 ) {
					error_log( print_r( $order->get_order_number(), true ) );
					error_log( 'Order notification disabled - will not send notification to supplier' );
					return;
				}
			}
			if($order->get_status() == 'completed' || $order->get_status() == 'cancelled') { error_log('Order Status '.$order->get_status());
				wp_mail($hdrs['To'], $hdrs['Subject'], $message, $headers);
			} else {  error_log('Order Status '.$order->get_status());
				wp_mail($hdrs['To'], $hdrs['Subject'], $message, $headers, $attachments);
			}
		} else {
			$fullinfo = $order_info['options']['full_information'];
			//$bill = $order_info['options']['billing_phone'];
			//$attachments = array();
			$attachments = apply_filters('wc_dropship_manager_send_order_attachments',$attachments,$order_info,$supplier_info);  // create a pdf packing slip file
			$options = get_option( 'wc_dropship_manager' );
			$text = '';
			if(isset($attachments['pdf_packingslip'])) {
				$encoded_attachment = chunk_split(base64_encode(file_get_contents($attachments['pdf_packingslip'])));
			}
			if(isset($attachments['csv_packingslip'])) {
				$encoded_attachment_csv = chunk_split(base64_encode(file_get_contents($attachments['csv_packingslip'])));
			}
			$hdrs = array();
			$hdrs['From'] = $from_email;
			$hdrs['To'] = $supplier_info['order_email_addresses'];
			if($cc_email == '0'){
				$hdrs['CC'] = $from_email;
			}
			$order_status = $order->get_status();
			if ($order_status == 'completed') {
				$hdrs['Subject'] = 'Order #'.$order_info['custom_order_number'].' is completed ';
			}else if ($order_status == 'cancelled') {
				$hdrs['Subject'] = 'Order #'.$order_info['custom_order_number'].' is cancelled ';
			} else {
				$hdrs['Subject'] = sprintf( __('New Order #%1$s From %2$s', 'woocommerce-dropshipping' ),$order_info['id'],	$from_name  );
			}
			$semi_rand = md5(time());
			$semi_rand_mixed = $semi_rand."11";
			$mime_boundary_alt = "{$semi_rand}";
			$mime_boundary_mixed = "{$semi_rand_mixed}";
			$headers  = "From: ".wp_specialchars_decode($from_name)." <".$from_email.">\r\n";
			$headers .= "MIME-Version: 1.0\n";
			if($cc_email == '0'){
				$headers .= "CC: ".$from_email."\r\n";
			}
			$headers .= "Content-Type: multipart/mixed;\n";
			$headers .= " boundary=\"{$mime_boundary_mixed}\"";
			if (strlen($supplier_info['account_number']) > 0)
			{
				$text .= $from_name.'Account Number: '.$supplier_info['account_number'].'<br/>';
			}
			$textPlain = $this->get_packingslip_text($order_info,$supplier_info);
			$text = $this->get_packingslip_html($order_info,$supplier_info,false);
                        $skipStatus = array('completed','cancelled');
			if ( !in_array($order->get_status(), $skipStatus) ) {
                $text = '<img style="max-width:150px;" class="email_store_logo" src="'.$options['packing_slip_url_to_logo'].'" /><br/>' . $options['email_order_note'] . $text;
            }
			$html = apply_filters('wc_dropship_manager_send_order_email_html',$text);
			$message = "This is a multi-part message in MIME format.\n\n";
			$message .=  "--{$mime_boundary_mixed}\n";
			$message .= "Content-Type: multipart/alternative;\n";
			$message .= " boundary=\"{$mime_boundary_alt}\"\n\n";
			// The space in front of boundary is crucial.
			$email_message_text  = strip_tags($html);
			if ($options['order_button_email'] == '1') { 
				 $order_link = '<div style="text-align: center; margin-top: -15px;padding-bottom: 10px;"><a href="'.get_site_url().'/wp-admin/admin.php?page=dropshipper-order-list" style="display: inline-block; font-size:16px; font-weight: bold; color: green; text-decoration: none;">'.sprintf( __('View order in dashboard', 'woocommerce-dropshipping' ),"" ).'</a></div>';
			 }
			 else{
				$order_link = '';
			    }
			$email_message_html = '<div style="background-color: '.( !empty(trim(get_option('woocommerce_email_background_color'))) ? get_option('woocommerce_email_background_color') : '#ccc' ).';">'.$html.$order_link.'</div>';
			$attachment_name = $order_info['id'].'_'.$supplier_info['slug'].'.pdf';
			// Add a multipart boundary above the plain message
			$message .= "--{$mime_boundary_alt}\n" .
	          "Content-Type: text/html; charset=\"UTF-8\"\n" .
	          "Content-Transfer-Encoding: 8bit\n\n" .
	          $textPlain."\n\n" ."--{$mime_boundary_alt}--\n";
	            /*"Content-Type: application/pdf; name=".$attachment_name."\n" .
			          "Content-Transfer-Encoding: base64\n\n" .
					  "Content-Disposition: attachment".
			          $encoded_attachment . "\n\n".
	                Must have 2 hyphens at the end.*/
			$fullinfo = $order_info['options']['full_information'];
			$bill = $order_info['options']['billing_phone'];
			$csv_inmail = $order_info['options']['csv_inmail'];
			$sup_companyname = $order_info['options']['store_name'];
			$sup_address = $order_info['options']['store_address'];
			$pack_company = $order_info['options']['packing_slip_company_name'];
			$pack_address = $order_info['options']['packing_slip_address'];
			if( $fullinfo == '1' && $sup_companyname == '1' && $sup_address == '1') {
				 /*$csv_name = $order_info['id'].'_'.$supplier_info['slug'].'_'.$pack_company.'_'.$pack_address.'.csv';*/
				 $csv_name = $order_info['number'].'_'.$supplier_info['slug'].'_'.$pack_company.'_'.$pack_address.'.csv';
			}else if( $fullinfo == '1' && $sup_address == '1' ) {
				/*$csv_name = $order_info['id'].'_'.$supplier_info['slug'].'_'.$pack_address.'.csv';*/
				$csv_name = $order_info['number'].'_'.$supplier_info['slug'].'_'.$pack_address.'.csv';
			}else if( $fullinfo == '1' && $sup_companyname == '1' ){
				/*$csv_name = $order_info['id'].'_'.$supplier_info['slug'].'_'.$pack_company.'.csv';*/
				$csv_name = $order_info['number'].'_'.$supplier_info['slug'].'_'.$pack_company.'.csv';
			}else{
				/*$csv_name = $order_info['id'].'_'.$supplier_info['slug'].'.csv';*/
				$csv_name = $order_info['number'].'_'.$supplier_info['slug'].'.csv';
			}
			$fullinfo = $options['full_information'];
			//$bill = $order_info['options']['billing_phone'];
			//$message .= "--{$mime_boundary_mixed}\n";
			if($order_status != 'completed' || $order_status != 'cancelled') {
				if($fullinfo == '1'){
		        $message .= "--{$mime_boundary_mixed}\n".
		        			"Content-Type: application/pdf; name=".$attachment_name."\n" .
				          "Content-Transfer-Encoding: base64"."\r\n" .
						  "Content-Disposition: attachment; filename=\"".$attachment_name."\""."\r\n"."\r\n".
				          $encoded_attachment. "\r\n";
		      	}
		    }
		    if($order_status != 'completed' || $order_status != 'cancelled') {
				if($csv_inmail == '1') {
					$message .= "--{$mime_boundary_mixed}\n".
							"Content-Type: application/octet-stream; name=\"".$csv_name."\""."\r\n" .
				          "Content-Transfer-Encoding: base64"."\r\n" .
						  "Content-Disposition: attachment; filename=\"".$csv_name."\""."\r\n"."\r\n".
				          $encoded_attachment_csv. "\r\n";
				}
			}
			$message .= "--{$mime_boundary_mixed}--";			// Must have 2 hyphens at the end
			//wp_mail($hdrs['To'], $hdrs['Subject'] , $email_message_html, $headers);
			if ( isset( $options['supp_notification'] ) ) {
				if ( $options['supp_notification'] == 1 ) {
					error_log( print_r( $order->get_order_number(), true ) );
					error_log( 'Order notification disabled â€” will not send notification to supplier' );
					return;
				}
			}
			wp_mail($hdrs['To'], $hdrs['Subject'], $email_message_html, $headers, $attachments);
			//mail($hdrs['To'], $hdrs['Subject'], $message, $headers);
		}
	}
	public function send_order_email_html( $text ) {
		return '<b>'.$text.'</b>';
	}
	public function send_order_attach_packingslip($attachments,$order_info,$supplier_info) {
		$html = $this->get_packingslip_html($order_info,$supplier_info,true);
		/*$file_name = $order_info['id'].'_'.$supplier_info['slug'].'.pdf';
		$csv_name = $order_info['id'].'_'.$supplier_info['slug'].'.csv';*/
		$options = get_option( 'wc_dropship_manager' );
		$fullinfo = $options['full_information'];
		//$bill = $options['billing_phone'];
		$file_name = $order_info['number'].'_'.$supplier_info['slug'].'.pdf';
		$csv_name = $order_info['number'].'_'.$supplier_info['slug'].'.csv';
		if($fullinfo == '1') {
			$attachments['pdf_packingslip'] = $this->make_pdf($order_info,$supplier_info,$html,$file_name);  // create a pdf packing slip file
		}
		if($options['csv_inmail'] == '1') {
			$attachments['csv_packingslip'] = $this->make_csv($order_info,$supplier_info,$html,$csv_name);
		}
		return $attachments;
	}
	public function add_tracking_info_customer_email( $order, $sent_to_admin, $plain_text ) {
		global $post;
		if(isset($_GET['orderid'])){
	   		$order_id = $_GET['orderid'];
			$order_val = wc_get_order( $order_id );
			if(!empty($order_val)){
				$status = $order_val->get_status();
				if(isset($status) && $status == 'completed'){
					$items = $order_val->get_items();
					$arrayuser = array();
					foreach ( $items as $item_id => $item ) {
					    $product_name = $item->get_name();
					    $product_id = $item->get_product_id();
					    $quantity = $item['qty'];
					    $supplier_id = get_post_meta($item_id,'supplierid',true);
						$arg = array(
									'meta_key'	  =>	'supplier_id',
									'meta_value'	=>	$supplier_id
								);
						$user_query = new WP_User_Query($arg);
						$authors = $user_query->get_results();
						foreach ($authors as $author)  {
							$arrayuser[] = $author->ID;
					    }
					}
					$postid = $order_id;
					$uniqe_userid = array_unique($arrayuser);
					foreach ($uniqe_userid as $key => $value) {
					 	$dropshipper_shipping_info = get_post_meta($postid, 'dropshipper_shipping_info_'.$value, true);
					 	$track_no = $dropshipper_shipping_info['tracking_number'];
					 	$supplier_id = get_user_meta($value, 'supplier_id', true);
					 	$term = get_term_by('id', $supplier_id, 'dropship_supplier');
						if($dropshipper_shipping_info){
							echo 'Tracking Number(s): '.$track_no;
						} else {
							echo '';
						}
			 		}
			 	}
		 	}
		}
	}
	public function store_product_meta($order_id) {
		$order = wc_get_order($order_id);
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$terms = get_the_terms( $product_id, 'dropship_supplier' );
			if ( !empty( $terms ) ) {				
				foreach( $terms as $term ) {
					$term_id = $term->term_id;
					update_post_meta($order_id,'dropship_supplier_'.$term_id,$term_id);
				}
			}
		}
	}

	// Custom function where metakeys / labels pairs are defined
	public function get_supplier_taxonomy_terms(){
	    $taxonomy = 'dropship_supplier';
	    $options  = array();

	    foreach ( get_terms( array('taxonomy' => 'dropship_supplier' ) ) as $term ) {
	        $options[$term->name] = $term->name;
	    }
	    return $options;
	}

	// Add a dropdown to filter orders by Supplier
	public function admin_shop_order_by_supplier_meta_filter(){
	    global $pagenow, $typenow;

	    if( 'shop_order' === $typenow && 'edit.php' === $pagenow ) {
	        $domain    = 'woocommerce-dropshipping';
	        $filter_id = 'by_supplier';
	        $current   = isset($_GET[$filter_id])? $_GET[$filter_id] : '';

	        echo '<select name="'.$filter_id.'">
	        <option value="">' . __('Filter by Supplier', $domain) . '</option>';

	        $options = $this->get_supplier_taxonomy_terms();

	        foreach ( $options as $key => $label ) {
	            printf( '<option value="%s"%s>%s</option>', $key,
	                $key === $current ? '" selected="selected"' : '', $label );
	        }
	        echo '</select>';
	    }
	}
	
	public function process_admin_shop_order_supplier_by_filter( $query ) {
	    global $pagenow, $post_type, $wpdb;

	    $filter_id = 'by_supplier';
	    $taxonomy  = 'dropship_supplier';

	    // Get all orderIDs in which the product name occurs
	    if ( $query->is_admin && $pagenow === 'edit.php' && $post_type === 'shop_order' && isset( $_GET[$filter_id] ) && $_GET[$filter_id] != '' ) {
	    	
	    	$order_ids = $wpdb->get_col("
	        SELECT order_items.order_id
	        FROM {$wpdb->prefix}woocommerce_order_items as order_items
	        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
	        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
	        WHERE posts.post_type = 'shop_order'
	        AND order_items.order_item_type = 'line_item'
	        AND order_item_meta.meta_value='{$_GET[$filter_id]}'" );
	   
	       if (!empty($order_ids)) {
   			// Set the new "meta query"
		        $query->set( 'post__in', $order_ids );

		        // Set "posts per page"
		        $query->set( 'posts_per_page', -1 );

		        // Set "paged"
		        $query->set( 'paged', ( get_query_var('paged') ? get_query_var('paged') : 1 ) );

		   	} else {
		   		
		   			$query->set( 'post__in', array(0));
		   	}
	    }
	}
}
