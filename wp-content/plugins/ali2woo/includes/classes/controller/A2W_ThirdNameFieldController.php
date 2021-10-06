<?php

/**
 * Description of A2W_ThirdNameFieldController
 *
 * @author Mikhail
 * 
 * @autoload: a2w_init
 */
if (!class_exists('A2W_ThirdNameFieldController')) {

    class A2W_ThirdNameFieldController {
        
        private $third_name_key = 'third_name';

        public function __construct() {
            if (a2w_get_setting('order_third_name')){
                
                add_filter( 'woocommerce_default_address_fields' , array($this, 'add_field_to_checkout') );
             
                add_filter( 'woocommerce_formatted_address_replacements', array($this, 'formatted_address_replacements'), 99, 2 );
              
          
             
                add_filter('woocommerce_admin_billing_fields', array($this, 'add_extra_customer_field'));
                add_filter('woocommerce_admin_shipping_fields', array($this, 'add_extra_customer_field'));
                
                add_filter( 'woocommerce_order_formatted_billing_address',  array($this,'update_formatted_billing_address'), 99, 2);
                
                add_filter( 'woocommerce_order_formatted_shipping_address', array($this,'update_formatted_shipping_address'), 99, 2);
            
            }
        }
        
        public function add_extra_customer_field($fields){
            $fields = $this->add_field_to_checkout($fields);
            return $fields;    
        }

        public function add_field_to_checkout($address_fields) {
          
            $new_address_fields = array();
            
            $thrid_name_field =  array(
                'label'     => __('Middle name', 'ali2woo'),
                'required'  => true,
                'class'     =>  array('form-row-wide', 'address-field'),
                'type'  => 'text',
              
            );
            
            $last_key = false; 
            
            foreach($address_fields as $key => $val){
                if ($last_key === "last_name"){
                   $new_address_fields[$this->third_name_key] = $thrid_name_field;
                   
                }
                
                $new_address_fields[$key] = $address_fields[$key];
                
                $last_key = $key;
            }
            
            return $new_address_fields;
           
        }
        
        public function formatted_address_replacements($address, $args ){
        
            if (isset($args[$this->third_name_key])){
                $address['{name}'] = $args[$this->third_name_key]." ".$args['first_name']." ".$args['last_name'];    
            }

            return $address;
        }
        
        public function update_formatted_billing_address($address, $obj){
            if (isset($address[$this->third_name_key])){
                $address[$this->third_name_key] = $obj->get_meta('_billing_third_name');    
            }
            
            return $address;
        }
        
        public function update_formatted_shipping_address($address, $obj){
            if (isset($address[$this->third_name_key])){
               $address[$this->third_name_key] = $obj->get_meta('_shipping_third_name');    
            }
               
            return $address;
        }
        
        
        
      
   

    }

}
