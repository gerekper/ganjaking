<?php

/**
 * Description of A2W_WooCommerceOrderItem
 *
 * @author Mikhail
 */
if (!class_exists('A2W_WooCommerceOrderItem')):

	class A2W_WooCommerceOrderItem {
		private $orderItem;
		private $has_changes; //use it for Woocommerce CRUD
		
		function __construct($order_item){
			$this->orderItem  = $order_item;
			$this->has_changes = false;
		}
		
		public function getName(){
			if (is_array($this->orderItem)) return $this->orderItem['name'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_name();  
		}
		
		public function getProductID(){
			if (is_array($this->orderItem)) return $this->orderItem['product_id'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_product_id();   
		}
		
		public function getVariationID(){
			if (is_array($this->orderItem)) return $this->orderItem['variation_id'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_variation_id();   
		}
		
		public function getQuantity(){
			if (is_array($this->orderItem)) return $this->orderItem['qty'];
			if (get_class($this->orderItem) == 'WC_Order_Item_Product') return $this->orderItem->get_quantity();     
		}

		public function isDelivered(){
				
			$tracking_status = $this->getTrackingStatus();

			if ($tracking_status) {
				if ( in_array( strtolower( trim( $tracking_status ) ), array(
					'delivery successful',
					'delivered'
				), true ) ) {
	
					return true;
				}
			}

			return false;	

		}

		public function isShipped(){
			
			$tracking_status = $this->getTrackingStatus();

			if ($tracking_status) {
				if ( in_array( strtolower( trim( $tracking_status ) ), array(
					'seller_shipped',
				), true ) ) {
	
					return true;
				}
			}

			return false;	
		}

		public function getExternalProductId(){
			$product_id = $this->getProductID();
			return get_post_meta( $product_id,A2W_Constants::product_external_meta(), true );
		}

		public function getExternalOrderId(){
			$external_order_id = $this->orderItem->get_meta(A2W_Constants::order_item_external_order_meta());

            $external_order_id = is_array($external_order_id) ? $external_order_id[0] : '';

			return $external_order_id;
		}

		private function getTrackingData(){
			$tracking_data =  $this->orderItem->get_meta(A2W_Constants::order_item_tracking_data_meta());

			if (!$tracking_data){
				$tracking_data = array();
				$tracking_data = array("tracking_codes" => array(), "carrier_name"=>'', "carrier_url" =>'', "tracking_status"=>'');    
			} 

			if (!isset($tracking_data['tracking_codes'])){
				$tracking_data['tracking_codes'] = array();	
			}

			if (!isset($tracking_data['carrier_name'])){
				$tracking_data['carrier_name'] = '' ;	
			}

			if (!isset($tracking_data['carrier_url'])){
				$tracking_data['carrier_url'] = '';	
			}

			if (!isset($tracking_data['tracking_status'])){
				$tracking_data['tracking_status'] = '';	
			}

			return $tracking_data;
		}

		public function getTrackingCodes(){
			
			$tracking_data = $this->getTrackingData();

			return ($tracking_data && isset($tracking_data['tracking_codes'])) ? $tracking_data['tracking_codes'] : array();
			
			return array();
		}

		public function getTrackingStatus(){
			$tracking_data = $this->getTrackingData();	
			$tracking_status = $tracking_data['tracking_status'];
			return 	$tracking_status;
		}

		public function getCarrierName(){
			$tracking_data = $this->getTrackingData();	
			if ($tracking_data && isset($tracking_data['tracking_codes'])) {
				return $tracking_data['carrier_name'];
			}
			return 	"";
		}


		public function getFormatedCarrierLink(){
			$tracking_data = $this->getTrackingData();	

			if ($tracking_data && isset($tracking_data['tracking_codes'])) {

				$tracking_url = $tracking_data['carrier_url'];
				$carrier_name = $tracking_data['carrier_name'];

				if ( $tracking_url && $carrier_name){
					return "<a target='_blank' href='{$tracking_url}'>" . $carrier_name . "</a>";
				} else if ($carrier_name){
					return $carrier_name;	
				}
				
			}

			return "";
		}



		public function getFormatedTrackingCodes($plain=false){
		
			$tracking_numbers  = $this->getTrackingCodes();

			$tracking_numbers_formated = array();

			if (!$plain){

				$tracking_url_template = "https://global.cainiao.com/detail.htm?mailNoList={tracking_number}";

				$tracking_url_template = apply_filters('a2w_get_tracking_url_template', $tracking_url_template);

				foreach( $tracking_numbers  as $tracking_number ){
					$tracking_url = str_replace( '{tracking_number}', $tracking_number, $tracking_url_template );
					$link_title = __('Click to see the tracking information', 'ali2woo');
					$tracking_numbers_formated[] = "<a target='_blank' title='{$link_title}' href='{$tracking_url}'>" . $tracking_number. "</a>";
				}

			} else {
				$tracking_numbers_formated = $tracking_numbers;	
			}

			return !empty( $tracking_numbers_formated ) ? implode( ",", $tracking_numbers_formated ) : "";

		}

	

        public function get_A2W_ShippingCode(){
            $shipping_code= '';

            $shipping_meta = $this->orderItem->get_meta(A2W_Shipping::get_order_item_shipping_meta_key());

            $legacy_shipping_meta = $this->orderItem->get_meta(A2W_Shipping::get_order_item_legacy_shipping_meta_key());

            if ( $shipping_meta ){
                $shipping_info = json_decode($shipping_meta , true);
                $shipping_code = $shipping_info['service_name'];
            } else 

            if ($legacy_shipping_meta) {
                $shipping_code = $legacy_shipping_meta;
            }

            return $shipping_code;
        }

		public function crud_update_tracking_data($tracking_codes, $carrier_name, $carrier_url, $tracking_status){
		
			foreach ($tracking_codes as $tracking_number) {
				$order_id = $this->orderItem->get_order_id();
				//	$order_item_id =  $this->orderItem->get_id();
				do_action('wcae_after_add_tracking_code', $order_id, $tracking_number);
			}

			$this->orderItem->update_meta_data(A2W_Constants::order_item_tracking_data_meta(),  array("tracking_codes" => $tracking_codes, "carrier_name" => $carrier_name, "carrier_url" => $carrier_url, "tracking_status" => $tracking_status ));
			
			$this->has_changes = true;
		}

		public function crud_update_tracking_codes($tracking_codes){
	
			foreach ($tracking_codes as $tracking_number) {
				$order_id = $this->orderItem->get_order_id();
				//	$order_item_id =  $this->orderItem->get_id();
				do_action('wcae_after_add_tracking_code', $order_id, $tracking_number);
			}
			
			$tracking_data = $this->getTrackingData();
	
			$tracking_data["tracking_codes"] = $tracking_codes;

			$this->orderItem->update_meta_data(A2W_Constants::order_item_tracking_data_meta(), 	$tracking_data);

			$this->has_changes = true;
		}

		public function crud_update_external_order($external_order_id){

			$this->orderItem->update_meta_data( A2W_Constants::order_item_external_order_meta(), array($external_order_id) );

			$this->has_changes = true;	
		}


		public function save(){
			if ($this->has_changes){
				$this->orderItem->save();	
				return true;
			}

			return false;
		}
		
	}


endif;

