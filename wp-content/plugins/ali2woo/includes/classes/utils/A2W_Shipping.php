<?php

/**
 * Description of A2W_Shipping
 *
 * @author Mikhail
 */
if (!class_exists('A2W_Shipping')) {

    class A2W_Shipping {

        public static function get_fake_method_id(){
            return "a2w-fake-shipping";
        }

        public static function get_order_item_shipping_meta_key(){
            return "_a2w_customer_chosen_shipping";
        }

        //compatibility with Ali2Woo before 1.18.2
        public static function get_order_item_legacy_shipping_meta_key(){
            return "a2w_shipping_code";
        }

        public static function get_countries() {
            $countries = array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());
            return $countries;
        }

        public static function get_not_available_shipping_message($country){

            $not_available_message = a2w_get_setting( 'aliship_not_available_message' );
            $remove_cart_item      = a2w_get_setting( 'aliship_not_available_remove' );

            if ( $remove_cart_item ) {
                $default_shipping_message = str_replace( array(
                    '{shipping_cost}',
                    '{delivery_time}',
                    '{country}'
                ), '', $not_available_message );
            } else {
                $not_available_cost = a2w_get_setting( 'aliship_not_available_cost' );
                if ( ! $not_available_cost ) {
                    $not_available_cost = esc_html__( 'free', 'ali2woo' );
                } else {
                    $not_available_cost = wc_price(  $not_available_cost );
                }
                $default_shipping_message = str_replace( array(
                    '{shipping_cost}',
                    '{delivery_time}'
                ), array(
                    $not_available_cost,
                    self::process_delivery_time( a2w_get_setting( 'aliship_not_available_time_min' ) . '-' . a2w_get_setting( 'aliship_not_available_time_max' ))
                ), $not_available_message );

                $default_shipping_message = str_replace('{country}', $country, $default_shipping_message);
            }

            return $default_shipping_message;

        }

        public static function get_selection_types(){
            return array('popup','select');
        }

        public static function get_shipping_types(){
            return array(
                'none' => _x('Do not calculate item shipping, only save customer`s shipping option', 'Setting title', 'ali2woo'),
                'new' => _x('Create a new shipping method and add it to currently available shipping options', 'Setting title', 'ali2woo'),
                'new_only' => _x('Create a new shipping method and make it the only available shipping option', 'Setting title', 'ali2woo'),
                'add' => _x('Calculate AliExpress shipping cost of all items in cart and add the cost to all currently available shipping options', 'Setting title', 'ali2woo'),
            );     
        }

        public static function get_selection_position_types(){
            return array('before_cart' => _x('Before add-to-cart button', 'Setting title', 'ali2woo'),
                         'after_cart' => _x('After  add-to-cart button', 'Setting title', 'ali2woo'));
        }

        /**
         * Get local method by original method company
         * retun false if this method is disabled in settings
         */
        public static function get_local_method_by_company($company){
            return A2W_ShippingPostType::get_item($company);
        }

        /**
         * Add method to the local methods and return its local data
         */
        public static function add_local_method($method){
            A2W_ShippingPostType::add_item($method['company'], $method['serviceName']);
        }

        /**
        * Add additional data to the method properties depending on the plugin settings
        */
        public static function get_normalized($method, $country, $type = "select"){

                    $countries = A2W_Shipping::get_countries();

                    $country_label =  $countries[$country];

                    $local_values = self::get_local_method_by_company($method['company']);

                    //this method is disabled in the shipping list
                    if ( $local_values === false) return false;

                    //if no such method there, add it
                    if (!$local_values) {
                        self::add_local_method($method); 
                        $local_values = self::get_local_method_by_company($method['company']);
                    }

                    $ship_price = A2W_ShippingPriceFormula::apply_formula($method, $local_values);

                    $method['company'] = $local_values['title'];
                            
                //    $method['serviceName'] = $local_values['serviceName'];

                    $method['price'] = $ship_price;
                    $method['formated_price'] = strip_tags(wc_price($method['price']));

                    $method['formated_delivery_time'] = self::process_delivery_time($method['time']);
                    
                    $method_price_html = $method['price'] ? $method['formated_price'] : esc_html__('free', 'ali2woo');

                   /* if ($type == "select"){
                        $method['label'] = $method['company'] . ", " . $method['formated_delivery_time'] . ", " . ($method['price'] ? 
                        $method['formated_price'] : 
                            esc_html__('free', 'ali2woo'));
                    }*/

                      //1) for visual info (in popup view) or select label
       
                       $shipping_option_text = a2w_get_setting('aliship_shipping_option_text');

                       //do not replace {country}, it will be replaced in js
                       $shipping_info = str_replace( array(
                        '{shipping_cost}',
                        '{shipping_company}',
                        '{delivery_time}',
                        '{country}'
                        ), array( $method_price_html, $method['company'], $method['formated_delivery_time'], $country_label), $shipping_option_text );

                        $method['label'] = $shipping_info;

                    if ($type == "popup"){                
                        //2) for modal table
                        $html = '<div class="a2w-div-table-row">' .
                        '<div class="a2w-div-table-col small-col">' .
                           '<input type="radio" class="select_method" value= "' . $method['serviceName'] . '" name="a2w_shipping_method_popup_field_{item_id}" id="a2w_shipping_method_popup_field_{item_id}_' . $method['serviceName'] . '">' .
                        '</div>' .
                       '<div class="a2w-div-table-col">' . $method['formated_delivery_time'] . '</div>' .
                       '<div class="a2w-div-table-col">' . $method_price_html . '</div>' .
                       '<div class="a2w-div-table-col">' . ($method['tracking'] ? 'yes' : 'no') . '</div>' .
                       '<div class="a2w-div-table-col">' . $method['company'] . '</div>' .
                       '</div>';

                       $method['html_row'] = $html;

                    }

                    return $method;

        }


        public static function process_delivery_time( $time ) {
            $time_arr = explode( '-', $time );
            if ( count( $time_arr ) === 2 ) {
                $min = intval( $time_arr[0] );
                if ( $min === intval( $time_arr[1] ) ) {
                    $return = sprintf( _n( '%s day', '%s days', $min, 'ali2woo' ), $min );
                } else {
                    $return = sprintf( esc_html__( '%s days', 'ali2woo' ), $time );
                }
            } else {
                $return = sprintf( _n( '%s day', '%s days', $time, 'ali2woo' ), $time );
            }

            return $return;
        }

        public static function table_of_placeholders( $args ) {
            if ( count( $args ) ) {
                ?>
                <div class="table-responsive">
                <table class="table table-bordered a2w-table-of-placeholders">
                    <thead>
                    <tr class="active">
                        <th scope="col" style="width: 50%"><?php esc_html_e( 'Placeholder', 'ali2woo' ) ?></th>
                        <th scope="col" style="width: 50%"><?php esc_html_e( 'Purpose', 'ali2woo' ) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ( $args as $key => $value ) {
                        ?>
                        <tr>
                            <td class="a2w-placeholder-value-container">
                                <div class="form-inline" role="form">
                                    <div class="form-group has-success has-feedback testttt">  
                                        <input
                                                class="a2w-placeholder-value form-control" type="text"
                                                readonly value="<?php echo esc_attr( "{{$key}}" ); ?>">
                                                <span class="glyphicon glyphicon-copy form-control-feedback a2w-placeholder-value-copy"></span>
                                    </div> 
                    </div>   
                            </td>
                            <td><?php echo esc_html( "{$value}" ); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                </div>
                <?php
            }
        }

        public static function get_formated_shipping_info_meta($order_id, $meta_value){

            $shipping_info = json_decode($meta_value, true);

            $delivery_time = self::process_delivery_time( $shipping_info['delivery_time'] );
            $shipping_cost = floatval( ( $shipping_info['shipping_cost'] ) );
            if ( $shipping_cost ) {
                $shipping_cost    = wc_price( $shipping_cost, array( 'currency' => get_post_meta( $order_id, '_order_currency', true ) ) );
                $display_value  = "[{$shipping_cost}] {$shipping_info['company']} ({$delivery_time})";
            } else {
                $display_value  = "[" . esc_html__( 'Free', 'ali2woo' ) . "] {$shipping_info['company']} ({$delivery_time})";
            }

            return $display_value;
        }
    }
}
