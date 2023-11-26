<?php

/**
 * The public-specific functionality of the plugin.
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/public
 */

/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/public
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $rtwwdpd_plugin_name    The ID of this plugin.
     */
    private $rtwwdpd_plugin_name;
    public $rtwwdpd_modules = array();

    public $bundle_product_id;
    public $rtwwdpd_set_rules;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $rtwwdpd_version    The current version of this plugin.
     */
    private $rtwwdpd_version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $rtwwdpd_plugin_name       The name of this plugin.
     * @param      string    $rtwwdpd_version    The version of this plugin.
     */
    public function __construct( $rtwwdpd_plugin_name, $rtwwdpd_version ) {
        $sabcd = 'verification_done';


        $rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) )
        { 
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-module-base.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-base.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-product.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-category.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-total.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-attr.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-payment.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-tier.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-bogo.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-simple-base.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-simple-product.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-product-tag.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/modules/rtwwdpd-class-adv-product-var.php';

            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-cart-query.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-class-adj-set.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-class-adj-set-product.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-class-adj-set-category.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-class-adj-set-total.php';
            include plugin_dir_path( dirname( __FILE__ ) ) . 'public/classes/rtwwdpd-class-compatibility.php';

            $this->rtwwdpd_plugin_name = $rtwwdpd_plugin_name;
            $this->rtwwdpd_version = $rtwwdpd_version;

            $rtwwdpd_modules['advanced_bogo']   = RTWWDPD_Advance_Bogo::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_product']  = RTWWDPD_Advance_Product::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_category'] = RTWWDPD_Advance_Category::rtwwdpd_instance();
            $rtwwdpd_modules['simple_product']    = RTWWDPD_Simple_Product::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_attribute']   = RTWWDPD_Advance_Attribute::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_tier']   = RTWWDPD_Advance_Tier::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_payment']   = RTWWDPD_Advance_Payment::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_pro_tag']   = RTWWDPD_Advance_Product_Tag::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_pro_var']   = RTWWDPD_Advance_Product_Variation::rtwwdpd_instance();
            $rtwwdpd_modules['advanced_totals'] = RTWWDPD_Advance_Total::rtwwdpd_instance();
            $this->rtwwdpd_modules = $rtwwdpd_modules;
            $this->bundle_product_id = array();
            
           
        }
        // do_shortcode('rtwwdpd_discnt_products','rtwwdpd_dscnt_pdt_callback');

    }


    /**
     * Register the stylesheets for the public area.
     *
     * @since    1.0.0
     */
    public function rtwwdpd_enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the rtwwdpd_run() function
         * defined in Woo_Dynamic_Pricing_Discounts_With_Ai_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Dynamic_Pricing_Discounts_With_Ai_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( $this->rtwwdpd_plugin_name, plugin_dir_url( __FILE__ ) . 'css/rtwwdpd-woo-dynamic-pricing-discounts-with-ai-public.css', array(), $this->rtwwdpd_version, 'all' );
    }

    /**
     * Register the JavaScript for the public area.
     *
     * @since    1.0.0
     */
    public function rtwwdpd_enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the rtwwdpd_run() function
         * defined in Woo_Dynamic_Pricing_Discounts_With_Ai_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Dynamic_Pricing_Discounts_With_Ai_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        // wp_enqueue_script( 'wp-api' );
        $sabcd = 'ification_done';
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_ver'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) {
            $rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
            $rtwwdpd_tier_cat_rule_name = get_option('rtwwdpd_tiered_cat');
            wp_enqueue_script( $this->rtwwdpd_plugin_name, plugin_dir_url( __FILE__ ) . 'js/rtwwdpd-woo-dynamic-pricing-discounts-with-ai-public.js', array( 'jquery' ), $this->rtwwdpd_version, false );

            $rtwwdpd_ajax_nonce = wp_create_nonce( "rtwwdpd-ajax-seurity" );
            wp_localize_script($this->rtwwdpd_plugin_name, 'rtwwdpd_ajax', array( 'ajax_url' => esc_url(admin_url('admin-ajax.php')),
                'rtwwdpd_nonce' => $rtwwdpd_ajax_nonce,
                'rtwwdpd_timer_time' => $this->rtwwdpd_time_get_time(),
                'rtwwdpd_without_ajax' => $this->rtw_sale_timer_html(),
                'rtwwdpd_rule_name'    => $rtwwdpd_rule_name,
                'rtwwdpd_tier_cat_rule_name' => $rtwwdpd_tier_cat_rule_name
            ));
            wp_enqueue_script( $this->rtwwdpd_plugin_name );
        }
        
    }
    /**
     * Calculating discount on payment method change.
     *
     * @since    1.0.0
     */
    function rtwwdpd_discnt_on_pay_select(){
        global $woocommerce;
    
        WC()->cart;
    }

    ///
    // Discounted product listing Shortcode

    // function rtwwdpd_dscnt_pdt_callback()
    // {

    // }


    function rtw_sale_timer_htm()
    {
        $rtwwdpd_html = "";
        $rtwwdpd_html .='<div class="rtweo_sale_message">
       <div class="rtweo_msg_sec">
       <p style="color:red" id="test1" ></p>

       </div>
       <div class="rtwwdpd_timer_sec">
        <div class="rtwwdpd-timing-box">
            <span style="color:red" id="days" class="rtwsame" ></span>
            <div class="rtwmsg"> Days </div>
        </div>
        <div class="rtwwdpd-timing-box">
            <span style="color:red" id="hours"  class="rtwsame"></span>  
            <div class="rtwmsg"> hours </div>
        </div>
        <div class="rtwwdpd-timing-box">
            <span style="color:red" id="minutes" class="rtwsame" ></span>   
            <div class="rtwmsg"> minutes </div>
        </div>
        <div class="rtwwdpd-timing-box">
            <span style="color:red" id="second" class="rtwsame" ></span>
            <div class="rtwmsg"> seconds </div>
        </div>
      
       </div>
       
        </div>';
        echo $rtwwdpd_html;
    }
    function rtwwdpd_time_get_time()
    {
        $rtw_priority_option = get_option( 'rtwwdpd_setting_priority' );
        $rtw_end_date=$rtw_priority_option['rtwwdpd_end_sale_date'];
        $end_date=strtotime($rtw_end_date);
        $rtw_cur_date=date("Y-m-d ");
        $cur_date= strtotime($rtw_cur_date);
        $end_time=$rtw_priority_option['end_time'];
        return array("end_date"=>$end_date,"cur_date"=>$cur_date,"end_time"=>$end_time);
    }
    /**
     * Set Timer for Sale.
     *
     * @since    1.1.0
     */
    function rtw_sale_timer_html()
    {  
         $rtw_priority_opt = get_option( 'rtwwdpd_setting_priority' );
         date_default_timezone_set('Asia/Kolkata');
         $sale_end_date= $rtw_priority_opt['rtwwdpd_end_sale_date'];
         $cur_date=date("Y-m-d");
         $c_date=strtotime($cur_date);
         $e_date=strtotime($sale_end_date);
         $end_time= $rtw_priority_opt['end_time'];
         $e_time=strtotime($end_time);
         $curnt_time=date("H:i");
         $c_time=strtotime($curnt_time);
         $seconds = strtotime($sale_end_date. $end_time)-time();
         $days = floor($seconds / 86400);
         $seconds %= 86400;
         $hours = floor($seconds / 3600);
         $seconds %= 3600;
         $minutes = floor($seconds / 60);
         $seconds %= 60;
            if($c_date > $e_date)
            {   
                
                return; 
            }
             else
            {
             
                if( $c_date == $e_date )
                {   
                    
                    if($curnt_time >= $end_time)
                    {
                        return ;
                    }
                }
                 $rtw_priority_opt['rtwwdpd_offr_msg']." : ".$days." :".$hours.":".$minutes." :". $seconds;
              
            }
            return array("msg" =>$rtw_priority_opt['rtwwdpd_offr_msg'],"end_date" =>$sale_end_date,"end_time" => $end_time,"curnt_date"=>$cur_date,"curnt_time"=>$c_time);
            
        
    }
    /**
     * Function to give discount according to shipping method rule.
     *
     * @since    1.0.0
     */
    function rtwwdpd_shipping_dscnt($rtwwdpd_rates, $rtwwdpd_package)
    {   
        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        $sabcd = 'verification_done';
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 

        global $woocommerce;
        $rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');
        $rtwwdpd_option = get_option('rtwwdpd_shipping_discount_on', 'rtwwdpd_shipping');

        $rtwwdpd_enable = get_option('rtwwdpd_apply_shipping_discount_on', 'message');
        $rtwwdpd_today_date = current_time('Y-m-d');
        if((isset($rtwwdpd_get_settings['ship_rule']) && $rtwwdpd_get_settings['ship_rule'] == 1) && $rtwwdpd_enable == 'applied')
        {  
            $rtwwdpd_get_ship_opt = get_option('rtwwdpd_ship_method');
            // !isset in version 1.5.2
            if(isset($rtwwdpd_get_ship_opt) && is_array($rtwwdpd_get_ship_opt) && !empty($rtwwdpd_get_ship_opt))
            {
                foreach ($rtwwdpd_get_ship_opt as $keys => $values) {

                    if( $values['rtwwdpd_ship_from_date'] > $rtwwdpd_today_date || $values['rtwwdpd_ship_to_date'] < $rtwwdpd_today_date )
                    {
                        continue 1;
                    }

                    foreach($rtwwdpd_rates as $key => $rate ) {
                        $rtwwdpd_pos = stripos($key, $values['allowed_shipping_methods']);
                        if($rtwwdpd_pos !== false)
                        {
                            $rtwwdpd_cart_prod_count = WC()->cart->get_cart_contents_count();
                            $rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();

                            if(isset($values['rtwwdpd_min_prod_cont']) && !empty($values['rtwwdpd_min_prod_cont']) )
                            {
                                if($values['rtwwdpd_min_prod_cont'] > $rtwwdpd_cart_prod_count )
                                {
                                    continue 1;
                                }
                            }

                            $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                            $chosen_shipping = $chosen_methods[0];

                            if( stripos( $chosen_shipping, $values['allowed_shipping_methods'] ) === false )
                            {
                                continue 1;
                            }

                            $shipping_packages =  WC()->cart->get_shipping_packages();
                            // // Get the WC_Shipping_Zones instance object for the first package
                            $shipping_zone = wc_get_shipping_zone( reset( $shipping_packages ) );
                            $zone_id   = $shipping_zone->get_id(); // Get the zone ID
                            // $zone_name = $shipping_zone->get_zone_name(); // To get the zone name
                            
                            $abcd = 1;
                            if(isset($values['allowed_shipping_zones']) && !empty($values['allowed_shipping_zones']) && is_array($values['allowed_shipping_zones']))
                            {
                                if(!in_array('all', $values['allowed_shipping_zones']))
                                {
                                    if(!in_array($zone_id, $values['allowed_shipping_zones']))
                                    {  
                                        $abcd = 3;
                                        continue 1;
                                    }else{
                                        $abcd = 2;
                                    }
                                }else{
                                    $abcd = 2;
                                }
                            }
                            
                            if(isset($values['rtwwdpd_min_spend']) && !empty($values['rtwwdpd_min_spend']) )
                            {
                                if($values['rtwwdpd_min_spend'] > $rtwwdpd_cart_total )
                                {
                                    continue 1;
                                }
                            }
                            if($values['rtwwdpd_ship_discount_type'] == 'rtwwdpd_discount_percentage')
                            {
                                $rtwwdpd_dscnt_val = $values['rtwwdpd_ship_discount_value'];
                                if($rtwwdpd_rates[$key]->cost != 0)
                                {
                                    $rtwwdpd_new_cost = $rtwwdpd_rates[$key]->cost - ($rtwwdpd_rates[$key]->cost * ($rtwwdpd_dscnt_val/100));

                                    if($values['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                    {
                                        $rtwwdpd_new_cost = $values['rtwwdpd_ship_max_discount'];
                                    }

                                    if($rtwwdpd_option == 'rtwwdpd_shipping' && $abcd == 2)
                                    {
                                        $rtwwdpd_rates[$key]->cost = $rtwwdpd_new_cost;
                                    }else{
                                        $rtwwdpd_rates[$key]->cost = $rtwwdpd_rates[$key]->cost;
                                    }

                                }
                            }
                            else{
                                $rtwwdpd_dscnt_val = $values['rtwwdpd_ship_discount_value'];
                                if($rtwwdpd_rates[$key]->cost != 0 && $rtwwdpd_rates[$key]->cost >= $rtwwdpd_dscnt_val)
                                {
                                    $rtwwdpd_new_cost = $rtwwdpd_rates[$key]->cost - $rtwwdpd_dscnt_val;

                                    if($values['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                    {
                                        $rtwwdpd_new_cost = $values['rtwwdpd_ship_max_discount'];
                                    }

                                    if($rtwwdpd_option == 'rtwwdpd_shipping' && $abcd == 2)
                                    {
                                        $rtwwdpd_rates[$key]->cost = $rtwwdpd_new_cost;
                                    }else{
                                        $rtwwdpd_rates[$key]->cost = $rtwwdpd_rates[$key]->cost;
                                    }
                                }
                            }
                        }
                        else{
                            $rtwwdpd_rates[$key]->cost = $rtwwdpd_rates[$key]->cost;
                        }
                    }
                }
            }
        }
        return $rtwwdpd_rates;
        }
    }
        /**
     * Function to add offer list on product/shop page before add to cart button.
     *
     * @since    1.0.0
     */
    function rtwwdpd_on_product_page(){
        global $post;
        $rtwwdpd_offers = get_option('rtwwdpd_setting_priority');
        $rtwwdpd_priority = array();
        $rtwwdpd_select_offer = '';
        $rtwwdpd_rule_per_page = '';
        $rtwwdpd_i = 1;
        $sabcd = 'ification_done';
        /////// to get product category ///////
        $rtwwdpd_terms = array();
        if(isset($post->ID) && !empty($post->ID))
        {
            $rtwwdpd_terms = get_the_terms( $post->ID, 'product_cat' );
        }
        if(is_array($rtwwdpd_terms) && !empty($rtwwdpd_terms))
        {
            foreach ($rtwwdpd_terms  as $term  ) {
                $rtwwdpd_product_cat_id = $term->term_id;
                $rtwwdpd_product_cat_name = $term->name;
                break;
            }
        }
        
        //////// to get product tag /////////
        if(has_term('', 'product_tag'))
        {
            $rtwwdpd_nterms = array();
            if(isset($post->ID) && !empty($post->ID))
            {
                $rtwwdpd_nterms = get_the_terms( $post->ID, 'product_tag' );
            }

            if(!empty($rtwwdpd_nterms))
            {
                foreach ($rtwwdpd_nterms  as $term  ) {
                    $rtwwdpd_product_tag_id = $term->term_id;
                    $rtwwdpd_product_tag_name = $term->name;
                    break;
                }
            }
        }

        $rtwwdpd_today_date = current_time('Y-m-d');

        ////// get category name thorugh category id ///////
        $rtwwdpd_cat = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
        $rtwwdpd_products = array();
        if(is_array($rtwwdpd_cat) && !empty($rtwwdpd_cat))
        {
            foreach ($rtwwdpd_cat as $value) {
                $rtwwdpd_products[$value->term_id] = $value->name;
            }
        }
        $rtwwdpd_product = wc_get_product();

        $rtwwdpd_prod_id = 0;
        if(is_object( $rtwwdpd_product ))
        {
            $rtwwdpd_prod_id = $rtwwdpd_product->get_id();
        }
        // $rtwwdpd_prod_var_id = $rtwwdpd_product['data']->get_variation_id();
        if(is_array($rtwwdpd_offers) && !empty($rtwwdpd_offers))
        {
            foreach ($rtwwdpd_offers as $key => $value) {
                if($key == 'pro_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'tier_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'pro_com_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'cat_com_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'tier_cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }	
                elseif($key == 'var_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_tag_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'attr_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'prod_tag_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif ($key == 'rtw_offer_select') {
                    $rtwwdpd_select_offer = $value;
                }
                elseif ($key == 'rtwwdpd_rule_per_page') {
                    $rtwwdpd_rule_per_page = $value;
                }
            }
        }
        
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_ver'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
            ///////////////// applying rule settings //////////////////
            if( $rtwwdpd_select_offer == 'rtw_first_match' )
            {	
                include( RTWWDPD_DIR . 'public/partials/rtwwdpd_applied_method/rtwwdpd_all_match_rule.php' );
                
            }
            elseif( $rtwwdpd_select_offer == 'rtw_best_discount' )
            {
                include( RTWWDPD_DIR . 'public/partials/rtwwdpd_applied_method/rtwwdpd_best_dscnt_rule.php' );
            }
            elseif( $rtwwdpd_select_offer == 'rtw_all_mtch' )
            {
                include( RTWWDPD_DIR . 'public/partials/rtwwdpd_applied_method/rtwwdpd_all_match_rule.php' );
                
            }
        }
    }

    /**
     * Function to add offer list on cart page.
     *
     * @since    1.0.0
     */
    function rtwwdpd_on_cart_page(){
        $rtwwdpd_offers = get_option('rtwwdpd_setting_priority');
        $rtwwdpd_today_date = current_time('Y-m-d');
    
        if( isset($rtwwdpd_offers['cart_rule']) || (  isset( $rtwwdpd_offers['rtw_tier_offer_on_cart'] ) && $rtwwdpd_offers['rtw_tier_offer_on_cart'] == 'rtw_price_yes' ) )
        {
            include( RTWWDPD_DIR . 'public/partials/rtwwdpd_applied_method/rtwwdpd_cart_setting.php' );	
        }
    }

    /**
     * Function to display discounted price on cart page.
     *
     * @since    1.0.0
     */
    function rtwwdpd_on_display_cart_item_price_html($rtwwdpd_html, $rtwwdpd_cart_item, $rtwwdpd_cart_item_key )
    {      
        // .rtwwdpd_show_offer span{
        //     color: #fff;
        //     background-color:#ffbf08;
        // }
        $sabcd = 'ification_done';
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_ver'.$sabcd, array() );
        
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 

        if ( $this->rtwwdpd_is_cart_item_discounted( $rtwwdpd_cart_item ) ) {
            $_product = $rtwwdpd_cart_item['data'];

            if ( function_exists( 'get_product' ) ) { 
                if (isset($rtwwdpd_cart_item['is_deposit']) && $rtwwdpd_cart_item['is_deposit']) {
                    $rtwwdpd_price_to_calculate = isset( $rtwwdpd_cart_item['discounts'] ) ? $rtwwdpd_cart_item['discounts']['price_adjusted'] : $rtwwdpd_cart_item['data']->get_price();
                } else { 
                    $rtwwdpd_price_to_calculate = $rtwwdpd_cart_item['data']->get_price(); 
                }

                $rtwwdpd_price_adjusted = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax($_product, array('price' => $rtwwdpd_price_to_calculate, 'qty' => 1)) : wc_get_price_including_tax($_product, array('price' => $rtwwdpd_price_to_calculate, 'qty' => 1));
                $rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];

            } else {
                if ( get_option( 'rtwwdpd_display_cart_prices_excluding_tax' ) == 'yes' ) {
                    $rtwwdpd_price_adjusted = wc_get_price_excluding_tax($rtwwdpd_cart_item['data']);
                    $rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];}
                else {
                    $rtwwdpd_price_adjusted = $rtwwdpd_cart_item['data']->get_price();
                    $rtwwdpd_price_base = $rtwwdpd_cart_item['discounts']['display_price'];}
                // endif;
                
            }
            
            if(!isset($rtwwdpd_price_adjusted) || !isset($rtwwdpd_price_base))
            {
                $prod = $rtwwdpd_cart_item['data'];
                if($prod)
                {
                    return $prod->get_price_html();
                }
            }
            
            if($rtwwdpd_price_adjusted != $rtwwdpd_price_base){
        
                if ( !empty( $rtwwdpd_price_adjusted ) || $rtwwdpd_price_adjusted === 0 || $rtwwdpd_price_adjusted === 0.00 ) {
                    
                    if ( apply_filters( 'rtwwdpd_use_discount_format', true ) ) {
                        
                        $rtwwdpd_html = '<del>' . RTWWDPD_Compatibility::rtw_wc_price( $rtwwdpd_price_base ) . '</del><ins> ' . RTWWDPD_Compatibility::rtw_wc_price( $rtwwdpd_price_adjusted ) . '</ins>';
                    } else {
                        $rtwwdpd_html = '<span class="amount">' . RTWWDPD_Compatibility::rtw_wc_price( $rtwwdpd_price_adjusted ) . '</span>';
                    }
                }
            }
        }
        else{
            $prod = $rtwwdpd_cart_item['data'];
            if($prod)
            {
                return $prod->get_price_html();
            }
        }
        
        return $rtwwdpd_html;
        }
    }
    
    /**
     * Function to check if product is already discounted.
     *
     * @since    1.0.0
     */
    public function rtwwdpd_is_cart_item_discounted( $rtwwdpd_cart_item ) {
        return isset( $rtwwdpd_cart_item['discounts'] );
    }

    /**
     * Function to add offer banner page.
     *
     * @since    1.0.0
     */
    function rtwwdpd_show_offer_banner_page(){
        ob_start(); 
        wp_enqueue_script( 'design-js', plugin_dir_url( __FILE__ ) . 'js/rtwwdpd-owlcarosel.js', array( 'jquery','OwlCarousel' ), $this->rtwwdpd_version, false );

        wp_enqueue_script( 'OwlCarousel', RTWWDPD_URL. 'assets/OwlCarousel/dist/owl.carousel.min.js', array(), $this->rtwwdpd_version, 'all'  );

        wp_enqueue_style( 'OwlCarousel', RTWWDPD_URL. 'assets/OwlCarousel/dist/assets/owl.carousel.min.css', array(), $this->rtwwdpd_version, 'all'  );
        
        wp_enqueue_style( 'OwlCarouseltheme', RTWWDPD_URL. 'assets/OwlCarousel/dist/assets/owl.theme.default.min.css', array(), $this->rtwwdpd_version, 'all'  );

        include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/show-offer/rtwwdpd-show-offer-banner-page.php';
        return ob_get_clean();
    }
        /**
     * Function to calculate discounts on cart page.
     *
     * @since    1.0.0
     */
    function rtwwdpd_cart_loaded_from_session($cart){

        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_verification_done', array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
        global $woocommerce;
        global $wpdb;
        $rtwwdpd_sorted_cart = array();
        if ( sizeof( $cart->cart_contents ) > 0 ) {
            foreach ( $cart->cart_contents as $cart_item_key => &$values ) {
                if ( $values === null ) {
                    continue;
                }

                if ( isset( $cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {
                    unset( $cart->cart_contents[ $cart_item_key ]['discounts'] );
                }
                $rtwwdpd_sorted_cart[ $cart_item_key ] = &$values;
            }
        }

        if ( empty( $rtwwdpd_sorted_cart ) ) {
            return;
        }
        //Sort the cart so that the lowest priced item is discounted when using block rules.
        uasort( $rtwwdpd_sorted_cart, 'RTWWDPD_Cart_Query::rtw_sort_by_price' );
        $rtwwdpd_modules = apply_filters( 'rtwwdpd_load_modules', $this->rtwwdpd_modules );
        
        foreach ( $rtwwdpd_modules as $module ) {
            
            $module->rtwwdpd_adjust_cart( $rtwwdpd_sorted_cart);
        }

        $rtwwdpd_today_date = current_time('Y-m-d');
        $rtwwdpd_cart_total = $cart->get_subtotal();

        $rtwwdpd_cart_prod_count = $cart->cart_contents;
        
        $rtwwdpd_prod_count = 0;
        if(is_array($rtwwdpd_cart_prod_count) && !empty($rtwwdpd_cart_prod_count))
        {
            foreach ($rtwwdpd_cart_prod_count as $key => $value) {
                
                $rtwwdpd_prod_count += (int)$value['quantity'];
              
            }
        }
        
        $rtwwdpd_enable = get_option('rtwwdpd_specific_enable');
        $rtwwdpd_spec_cus_opt = array();
        if(isset($rtwwdpd_enable) && $rtwwdpd_enable == 'enable'){
            $rtwwdpd_spec_cus_opt = get_option('rtwwdpd_specific_c');
        }

        $rtwwdpd_user = wp_get_current_user();

        $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
        $rtwwdpd_user_id = get_current_user_id();
        $rtwwdpd_args = array(
            'customer_id' => get_current_user_id(),
            'post_status' => 'cancelled',
            'post_type' => 'shop_order',
            'return' => 'ids',
        );

        $rtwwdpd_numordr_cancld = 0;
        $rtwwdpd_numordr_cancld = count( wc_get_orders( $rtwwdpd_args ) );
        $rtwwdpd_no_oforders = $rtwwdpd_no_oforders - $rtwwdpd_numordr_cancld;
        $rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
        
        $rtwwdpd_rslt = $this->current_customer_month_count($rtwwdpd_user_id);
        $rtwwdpd_pro_in_mnth = isset($rtwwdpd_rslt['count']) ? $rtwwdpd_rslt['count'] : 0;
        $rtwwdpd_pur_amt_in_mnth = isset($rtwwdpd_rslt['order_totl']) ? $rtwwdpd_rslt['order_totl'] : 0;
        

        if( is_array($rtwwdpd_spec_cus_opt ) && !empty( $rtwwdpd_spec_cus_opt ) )
        {
            foreach ($rtwwdpd_spec_cus_opt as $key => $value) {

                if($value['rtwwdpd_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                {
                    continue 1;
                }

                $rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
                $rtwwdpd_role_matched = false;
                if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                {
                    foreach ($rtwwdpd_user_role as $rol => $role) {
                        if($role == 'all'){
                            $rtwwdpd_role_matched = true;
                        }
                        if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                            $rtwwdpd_role_matched = true;
                        }
                    }
                }
                if($rtwwdpd_role_matched == false)
                {
                    continue;
                }
                // $rtwemails = explode(",", $value['rtwwdpd_user_emails']);
                $rtwemails = isset( $value['rtwwdpd_user_emails']) ? $value['rtwwdpd_user_emails'] : '';
                $emails = array();
                if(!empty($rtwemails) && is_array($rtwemails))
                {
                    foreach ($rtwemails as $eid => $email) {
                        $emails[$eid] = trim($email);
                    }
                    $rtw = wp_get_current_user();
                    $curr_email = $rtw->user_email;
                    if(!in_array($curr_email, $emails))
                    {
                        continue;
                    }
                }

                if($value['rtwwdpd_rule_for'] == 'rtwwdpd_min_purchase')
                {
                    if($rtwwdpd_ordrtotal < $value['rtwwdpd_min'])
                    {
                        continue 1;
                    }
                }
                elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_min_prod')
                {
                    $rtwwdpd_units_bought = $wpdb->get_var( "
                        SELECT SUM(woim2.meta_value)
                        FROM {$wpdb->prefix}woocommerce_order_items AS woi
                    -- INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim ON woi.order_item_id = woim.order_item_id
                    INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta woim2 ON woi.order_item_id = woim2.order_item_id
                    INNER JOIN {$wpdb->prefix}postmeta pm ON woi.order_id = pm.post_id
                    INNER JOIN {$wpdb->prefix}posts AS p ON woi.order_id = p.ID
                    WHERE woi.order_item_type LIKE 'line_item'
                    AND p.post_type LIKE 'shop_order'
                    AND p.post_status IN ('wc-completed','wc-processing')
                    AND pm.meta_key = '_customer_user'
                    AND pm.meta_value = $rtwwdpd_user_id
                    AND woim2.meta_key = '_qty'
                    ");

                    if($rtwwdpd_units_bought < $value['rtwwdpd_min'])
                    {
                        continue 1;
                    }
                }
                elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_mntly_pur_pro')
                {
                    if($rtwwdpd_pro_in_mnth < $value['rtwwdpd_min'])
                    {
                        continue 1;
                    }
                }
                elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_mntly_pur_amt')
                {
                    if($rtwwdpd_pur_amt_in_mnth < $value['rtwwdpd_min'])
                    {
                        continue 1;
                    }
                }
                elseif($value['rtwwdpd_rule_for'] == 'rtwwdpd_mntly_visit')
                {
                    $rtwwdpd_meta_key = 'rtwwdpd_user_visit_count';
                    $rtwwdpd_array = get_user_meta($rtwwdpd_user_id, $key = '', $single = false);
                    $rtwwdpd_visit_count = $rtwwdpd_array['rtwwdpd_user_visit_count'][0];
                    
                    if($rtwwdpd_visit_count < $value['rtwwdpd_min'])
                    {
                        continue 1;
                    }
                }

                if($value['rtwwdpd_rule_on'] == 'rtw_amt')
                {
                    if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'])
                    {
                        continue 1;
                    }
                }
                elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
                {
                    if($rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                    {
                        continue 1;
                    }
                }
                else{
                    if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'] && $rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                    {
                        continue 1;
                    }
                }
                foreach ($cart->cart_contents as $cart_item_key => $cart_item) {
                    $rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
                    // $rtwwdpd_original_price = $cart_item['data']->get_price();

                    $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];
                    
                    $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];
                    
                    $rtwwdpd_dscnted_price = 0;

                    if( $value['rtwwdpd_dsnt_type'] == 'rtwwdpd_discount_percentage' )
                    {
                        $rtwwdpd_dscnted_price = $rtwwdpd_original_price - ($rtwwdpd_original_price * ($rtwwdpd_dis_val/100));
                    }
                    else{
                        if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                        {
                            $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                        }
                        $rtwwdpd_dscnted_price = $rtwwdpd_original_price - $rtwwdpd_dis_val;
                        if($rtwwdpd_original_price < $rtwwdpd_dis_val)
                        {
                            $rtwwdpd_dscnted_price = 0;
                        }
                    }
                    if( isset($value['rtwwdpd_combi_exclude_sale']) )
                    {
                        if( $cart_item['data']->is_on_sale() )
                        {
                            continue;
                        }
                    }
                    $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_dscnted_price, 'rtw_spec_cus', 'rtw_spec_cus');
                }
            }
        }

        $rtwwdpd_plus_mem_opt = get_option('rtwwdpd_plus_member');
        $rtwwdpd_enable = get_option('rtwwdpd_plus_enable');
        if(is_array($rtwwdpd_plus_mem_opt) && !empty($rtwwdpd_plus_mem_opt))
        {
            
            if($rtwwdpd_enable == 'enable')
            {
                $rtwwdpd_user_id = get_current_user_id();
                $rtwwdpd_user_meta = get_user_meta($rtwwdpd_user_id, 'rtwwdpd_plus_member');
                ///////// checking if user is a plus member ///////////
                if($rtwwdpd_user_meta && $rtwwdpd_user_meta[0]['check'] == 'checked')
                {
                    foreach ($rtwwdpd_plus_mem_opt as $key => $value)
                    {	
                        $matched = true;
                        if($value['rtwwdpd_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                        {
                            continue 1;
                        }
                        $rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
                        $rtwwdpd_role_matched = false;
                        if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                        {
                            foreach ($rtwwdpd_user_role as $rol => $role) {
                                if($role == 'all'){
                                    $rtwwdpd_role_matched = true;
                                }
                                if($role == 'guest')
                                {
                                    if(!is_user_logged_in())
                                    {
                                        $rtwwdpd_role_matched = true;
                                    }
                                }
                                if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                    $rtwwdpd_role_matched = true;
                                }
                            }
                        }
                        if($rtwwdpd_role_matched == false)
                        {
                            continue 1;
                        }

                        if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product')
                        {
                            if(isset($value['product_ids']) && is_array($value['product_ids']) && !empty($value['product_ids']))
                            {
                                foreach ($value['product_ids'] as $pids => $pid) {

                                    foreach ($cart->cart_contents as $cart_item_key => $cart_item) {

                                        if($cart_item['data']->get_id() == $pid)
                                        {
                                            if( isset($value['rtwwdpd_combi_exclude_sale']) )
                                            {
                                                if( $cart_item['data']->is_on_sale() )
                                                {
                                                    continue;
                                                }
                                            }

                                            if($value['rtwwdpd_rule_on'] == 'rtw_quant')
                                            {
                                                if( $cart_item['quantity'] < $value['rtwwdpd_min_purchase_quant'] || $cart_item['quantity'] > $value['rtwwdpd_max_purchase_quant'])
                                                {
                                                    continue;
                                                }

                                            }
                                            if($value['rtwwdpd_rule_on'] == 'rtw_amt')
                                            {
                                                if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'])
                                                {
                                                    continue 1;
                                                }
                                                if($rtwwdpd_cart_total > $value['rtwwdpd_max_purchase_of'])
                                                {
                                                    continue 1;
                                                }
                                            }
                                            elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
                                            {
                                                if( $cart_item['quantity'] < $value['rtwwdpd_min_purchase_quant'] || $cart_item['quantity'] > $value['rtwwdpd_max_purchase_quant'])
                                                {
                                                    continue;
                                                }
                                            }
                                            else{
                                                if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'] && $rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                                if($rtwwdpd_cart_total > $value['rtwwdpd_max_purchase_of'] && $rtwwdpd_prod_count > $value['rtwwdpd_max_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                            }

                                            $rtwwdpd_original_price = $cart_item['data']->get_price();

                                            $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];

                                            $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];

                                            $rtwwdpd_dscnted_price = 0;

                                            if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_dis_percent')
                                            {
                                                $rtwwdpd_dscnted_price = $rtwwdpd_original_price - ($rtwwdpd_original_price * ($rtwwdpd_dis_val/100));
                                            }
                                            else{
                                                if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                                                {
                                                    $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                                                }
                                                $rtwwdpd_dscnted_price = $rtwwdpd_original_price - $rtwwdpd_dis_val;
                                                if($rtwwdpd_original_price < $rtwwdpd_dis_val)
                                                {
                                                    $rtwwdpd_dscnted_price = 0;
                                                }
                                            }
                                            
                                            $vip_val = get_option('rtwwdpd_plus_member_discount_value', array());

                                            if(empty($vip_val))
                                            {
                                                $vip_val = array( $cart_item_key => array(
                                                    'quantity' => $cart_item['quantity'],
                                                    'discount' => $rtwwdpd_dscnted_price )
                                                );
                                            }else{
                                                $vip_val[$cart_item_key] = array(
                                                    'quantity' => $cart_item['quantity'],
                                                    'discount' => $rtwwdpd_dscnted_price );
                                            }

                                            update_option('rtwwdpd_plus_member_discount_value', $vip_val);

                                            $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_dscnted_price, 'rtwwdpd_plus_member', 'rtwwdpd_plus_member');
                                        }
                                    }
                                }
                            }
                        }
                        elseif($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category')
                        {
                            $rtwwdpd_catids = array();
                            if(isset($value['category_ids']) && is_array($value['category_ids']) && !empty($value['category_ids']))
                            {
                                $rtwwdpd_prod_count = 0;
                                foreach ($value['category_ids'] as $cids => $cid) 
                                {
                                    foreach ($cart->cart_contents as $cart_item_key => $cart_item)
                                    {
                                        
                                        if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                                        {
                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                        }else{
                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                        }

                                        if( in_array( $cid, $rtwwdpd_catids ) )
                                        {
                                            $rtwwdpd_prod_count += $cart_item['quantity'];
                                        }
                                    }
                                }
                                foreach ($value['category_ids'] as $cids => $cid) 
                                {
                                    foreach ($cart->cart_contents as $cart_item_key => $cart_item) 
                                    {
                                        $rtwwdpd_catids = array();
                                        if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                                        {
                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                        }else{
                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                        }

                                        if( in_array( $cid, $rtwwdpd_catids ) )
                                        {
                                            if( isset($value['rtwwdpd_combi_exclude_sale']) )
                                            {
                                                if( $cart_item['data']->is_on_sale() )
                                                {
                                                    continue;
                                                }
                                            }

                                            if($value['rtwwdpd_rule_on'] == 'rtw_amt')
                                            {
                                                if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'])
                                                {
                                                    continue 1;
                                                }
                                                if($rtwwdpd_cart_total > $value['rtwwdpd_max_purchase_of'])
                                                {
                                                    continue 1;
                                                }
                                            }
                                            elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
                                            {
                                                if($rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                                if($rtwwdpd_prod_count > $value['rtwwdpd_max_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                            }
                                            else{
                                                if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'] && $rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                                if($rtwwdpd_cart_total > $value['rtwwdpd_max_purchase_of'] && $rtwwdpd_prod_count > $value['rtwwdpd_max_purchase_quant'])
                                                {
                                                    continue 1;
                                                }
                                            }

                                            $rtwwdpd_original_price = $cart_item['data']->get_price();

                                            $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];

                                            $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];

                                            $rtwwdpd_dscnted_price = 0;

                                            if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_dis_percent')
                                            {
                                                $rtwwdpd_dscnted_price = $rtwwdpd_original_price - ($rtwwdpd_original_price * ($rtwwdpd_dis_val/100));
                                            }
                                            else{
                                                if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                                                {
                                                    $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                                                }
                                                $rtwwdpd_dscnted_price = $rtwwdpd_original_price - $rtwwdpd_dis_val;
                                                if($rtwwdpd_original_price < $rtwwdpd_dis_val)
                                                {
                                                    $rtwwdpd_dscnted_price = 0;
                                                }
                                            }
                                            
                                            $vip_val = get_option('rtwwdpd_plus_member_discount_value', array());

                                            if(empty($vip_val))
                                            {
                                                $vip_val = array( $cart_item_key => array(
                                                    'quantity' => $cart_item['quantity'],
                                                    'discount' => $rtwwdpd_dscnted_price )
                                                );
                                            }else{
                                                $vip_val[$cart_item_key] = array(
                                                    'quantity' => $cart_item['quantity'],
                                                    'discount' => $rtwwdpd_dscnted_price );
                                            }

                                            update_option('rtwwdpd_plus_member_discount_value', $vip_val);

                                            $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_dscnted_price, 'rtwwdpd_plus_member', 'rtwwdpd_plus_member');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        
        ////////////////// upcoming sale ////////////////////
        $rtwwdpd_temp_cart = $cart->cart_contents;
        if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
        { 
            $rtwwdpd_get_pro_option = get_option('rtwwdpd_coming_sale');
        
            $i = 0;
            $rtwwdpd_user = wp_get_current_user();
            $rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
            $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
            $rtwwdpd_today_date = current_time('Y-m-d');
            $rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
            $set_id = 1;
            if( is_array($rtwwdpd_get_pro_option) && !empty($rtwwdpd_get_pro_option) )
            {   
                $pi = 0;
                $ci = 0;
                foreach ($rtwwdpd_get_pro_option as $prod => $pro_rul) {

                    if($pro_rul['rtwwdpd_sale_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_sale_to_date'] < $rtwwdpd_today_date)
                    {
                        continue 1;
                    }

                    $rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles'] ;
                    $rtwwdpd_role_matched = false;
                    if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role) )
                    {
                        foreach ($rtwwdpd_user_role as $rol => $role) {
                            if($role == 'all'){
                                $rtwwdpd_role_matched = true;
                            }
                            if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                $rtwwdpd_role_matched = true;
                            }
                        }
                    }

                    if($rtwwdpd_role_matched == false)
                    {
                        continue 1;
                    }
                    if(isset($pro_rul['rtwwdpd_sale_min_orders']) && $pro_rul['rtwwdpd_sale_min_orders'] > $rtwwdpd_no_oforders)
                    {
                        continue 1;
                    }
                    if(isset($pro_rul['rtwwdpd_sale_min_spend']) && $pro_rul['rtwwdpd_sale_min_spend'] > $rtwwdpd_ordrtotal)
                    {
                        continue 1;
                    }

                    foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
                        $rtwwdpd_category_id = $cart_item['data']->get_category_ids();
                        $rtwwdpd_original_price = $cart_item['data']->get_price();

                        if ( $rtwwdpd_original_price ) {
                            $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_sale_discount_value'], $pro_rul, $cart_item, $this );
                            if( isset( $pro_rul['product_id'] ) )
                            {
                                foreach ($pro_rul['product_id'] as $prod => $p_id) 
                                {
                                    if( $p_id == $cart_item['product_id'] )
                                    {
                                        if( $pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_quantity')
                                        {
                                            if( $cart_item['quantity'] < $pro_rul['quant_pro'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                        elseif ( $pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_price' )
                                        {
                                            if( $cart_item['data']->get_price() < $pro_rul['quant_pro'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                        elseif($pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_weight')
                                        {
                                            if($cart_item['data']->get_weight() < $pro_rul['quant_pro'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                    }
                                }
                            }
                            else{
                                foreach ($pro_rul['category_id'] as $prod => $c_id) 
                                {
                                    $cate_ids_arr = $cart_item['data']->get_category_ids();
                                    if( in_array( $c_id, $cate_ids_arr ) )
                                    {
                                        if( $pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_quantity')
                                        {
                                            if( $cart_item['quantity'] < $pro_rul['quant_cat'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                        elseif ( $pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_price' )
                                        {
                                            if($cart_item['data']->get_price() < $pro_rul['quant_cat'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                        elseif($pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_weight')
                                        {
                                            if($cart_item['data']->get_weight() < $pro_rul['quant_cat'][$prod] )
                                            {
                                                continue 2;
                                            }
                                        }
                                    }
                                }
                            }

                            if($pro_rul['rtwwdpd_sale_discount_type'] == 'rtwwdpd_discount_percentage')
                            {
                                $rtwwdpd_amount = $rtwwdpd_amount / 100;
                                $rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
                                if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_sale_max_discount'])
                                {
                                    $rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_sale_max_discount'];
                                }
                                $rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );

                                if( isset($pro_rul['product_id'] ) )
                                {							
                                    foreach ( $pro_rul['product_id'] as $k => $v ) 
                                    {
                                        if($v == $cart_item['product_id'])
                                        {
                                            if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                                Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                                $set_id++;
                                                break;
                                            }
                                        }
                                    }
                                }
                                if(isset($pro_rul['category_id']))
                                {	
                                    if( in_array( $pro_rul['category_id'][0], $rtwwdpd_category_id ) )
                                    {	
                                        if(isset($pro_rul['rtwwdpd_exclude_sale']))
                                        {
                                            if( !$cart_item['data']->is_on_sale() )
                                            {
                                                Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                            }
                                        }
                                        else{
                                            Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if($rtwwdpd_amount > $pro_rul['rtwwdpd_sale_max_discount'])
                                {
                                    $rtwwdpd_amount = $pro_rul['rtwwdpd_sale_max_discount'];
                                }

                                $rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
                                if(isset($pro_rul['product_id']))
                                {	
                                    $rtwwdpd_get_pro_id[] = $cart_item['product_id'];
                               
                                    if( in_array($pro_rul['product_id'][0],$rtwwdpd_get_pro_id) )
                                    {  
                                        if(isset($pro_rul['rtwwdpd_exclude_sale']))
                                        {
                                            if( !$cart_item['data']->is_on_sale() )
                                            {
                                                if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                                    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                                    $set_id++;
                                                }
                                            }
                                        }
                                        else{
                                            if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                                Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                                $set_id++;
                                            }
                                        }
                                    }
                                }
                                if(isset($pro_rul['category_id']))
                                {	
                                    
                                    if( in_array( $pro_rul['category_id'][0], $rtwwdpd_category_id ) )
                                    {	
                                        if(isset($pro_rul['rtwwdpd_exclude_sale']))
                                        {
                                            if( !$cart_item['data']->is_on_sale() )
                                            {
                                                Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                            }
                                        }
                                        else{
                                            Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        ////////////////// Next Buy Bonus ////////////////////
        $rtwwdpd_next_buy_option = array();
        $rtwwdpd_next_enable = get_option('rtwwdpd_next_buy');
        if( isset( $rtwwdpd_next_enable ) && $rtwwdpd_next_enable == 'enable' )
        {
            $rtwwdpd_next_buy_option = get_option( 'rtwwdpd_next_buy_rule' );
        }

        if( is_array( $rtwwdpd_next_buy_option ) && !empty( $rtwwdpd_next_buy_option ) )
        {   
            $rtwwdpd_user_id = get_current_user_id();
            $rtwwdpd_user_meta = get_user_meta( $rtwwdpd_user_id , 'rtwwdpd_next_buy_eligible' );
            $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
            $rtwwdpd_today_date = current_time('Y-m-d');
            $rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
            if(is_array($rtwwdpd_user_meta) && !empty($rtwwdpd_user_meta))
            {   
                if( $rtwwdpd_user_meta[0] == 'rtwwdpd_eligible' )
                {
                    foreach ( $rtwwdpd_next_buy_option as $next_buy => $opt ) {

                        if( $opt['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $opt['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date )
                        {
                            continue 1;
                        }
                        foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

                            $rtwwdpd_category_ids = $cart_item['data']->get_category_ids();
                            if( isset( $opt['category_exe_id'] ) )
                            {
                                $rtwwdpd_exe_cats = $opt['category_exe_id'];
                                $rtw_cat_match = false;

                                foreach ($rtwwdpd_exe_cats as $idsss) {
                                    if( in_array( $idsss, $rtwwdpd_category_ids ) )
                                    {
                                        $rtw_cat_match = true;
                                    }
                                }
                                if( $rtw_cat_match == true )
                                {
                                    continue 1;
                                }
                            }
                            if( isset( $opt['product_exe_id'] ) )
                            {
                                $rtwwdpd_exe_prods = $opt['product_exe_id'];
                                if( in_array( $cart_item['product_id'], $rtwwdpd_exe_prods ) )
                                {
                                    continue 1;
                                }
                            }

                            $rtwwdpd_amount = isset( $opt['rtwwdpd_discount_value'] ) ? $opt['rtwwdpd_discount_value'] : 0;
                            $rtwwdpd_original_price = $cart_item['data']->get_price();

                            if($opt['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
                            {
                                $rtwwdpd_amount = $rtwwdpd_amount / 100;
                                $rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
                                if($rtwwdpd_dscnted_val > $opt['rtwwdpd_max_discount'])
                                {
                                    $rtwwdpd_dscnted_val = $opt['rtwwdpd_max_discount'];
                                }
                                $rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );
                
                                if( isset( $opt['rtwwdpd_exclude_sale'] ) )
                                {
                                    if( !$cart_item['data']->is_on_sale() )
                                    { 
                                        Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                    }
                                }
                                else{

                                    if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                        Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                    }
                                }

                            }
                            else
                            {
                                if($rtwwdpd_amount > $opt['rtwwdpd_max_discount'])
                                {
                                    $rtwwdpd_amount = $opt['rtwwdpd_max_discount'];
                                }

                                $rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
                                if(isset($opt['rtwwdpd_exclude_sale']))
                                {
                                    if( !$cart_item['data']->is_on_sale() )
                                    {
                                        if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                            $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                        }
                                    }
                                }
                                else{
                                    if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
                                        $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                    }
                                }
                                if(isset($opt['rtwwdpd_exclude_sale']))
                                {
                                    if( !$cart_item['data']->is_on_sale() )
                                    {
                                        Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                    }
                                }
                                else{
                                    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'rtwwdpd_next_buy', '_next_buy' );
                                }
                            }
                        }
                    }
                }
            }
            
        }
        ////////////////// Next Buy Bonus ////////////////////

        ////////////////// Nth Order Discount ////////////////////
        
        $rtwwdpd_nth_order_option = array();
        $rtwwdpd_nth_enable = get_option('rtwwdpd_enable_nth_order');
        if( isset( $rtwwdpd_nth_enable ) && $rtwwdpd_nth_enable == 'enable' )
        {
            $rtwwdpd_nth_order_option = get_option( 'rtwwdpd_nth_order' );
        }

        if( is_array( $rtwwdpd_nth_order_option ) && !empty( $rtwwdpd_nth_order_option ) )
        {
            if($rtwwdpd_nth_enable == 'enable')
            {
                $rtwwdpd_user_id = get_current_user_id();
                $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
                $rtwwdpd_current_order_no = ( $rtwwdpd_no_oforders + 1 );

                foreach ($rtwwdpd_nth_order_option as $key => $value)
                {	
                    $matched = true;
                    if($value['rtwwdpd_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                    {
                        continue 1;
                    }
                   
                    if(isset($value['rtwwdpd_repeat_discount']) && $value['rtwwdpd_repeat_discount'] == 'yes')
                    {
                        if( $rtwwdpd_current_order_no < $value['rtwwdpd_nth_value'] )
                        {
                            continue 1;
                        }

                        if( isset( $value['rtwwdpd_nth_upto_value'] ) && !empty( $value['rtwwdpd_nth_upto_value'] ) )
                        {
                            if($value['rtwwdpd_nth_upto_value'] < $rtwwdpd_current_order_no)
                            {
                                continue 1;
                            }
                        }
                    }
                    else
                    {
                       if(isset($value['rtwwdpd_nth_value']) && !empty($value['rtwwdpd_nth_value']))
                       {
                            if( $rtwwdpd_current_order_no != $value['rtwwdpd_nth_value'] )
                            {
                                continue 1;
                            }
                        }
                    }
                    
                    $rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
                    $rtwwdpd_role_matched = false;
                    if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                    {
                        foreach ($rtwwdpd_user_role as $rol => $role) {
                            if($role == 'all'){
                                $rtwwdpd_role_matched = true;
                            }
                            if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                $rtwwdpd_role_matched = true;
                            }
                        }
                    }
                    if($rtwwdpd_role_matched == false)
                    {
                        continue 1;
                    }

                    if($value['rtwwdpd_rule_on'] == 'rtw_amt')
                    {
                        
                        if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'])
                        {
                            continue 1;
                        }
                    }
                    elseif($value['rtwwdpd_rule_on'] == 'rtw_quant')
                    {   
                       
                        if($rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                        { 
                            continue 1;
                        }
                    }
                    else{
                        if($rtwwdpd_cart_total < $value['rtwwdpd_min_purchase_of'] && $rtwwdpd_prod_count < $value['rtwwdpd_min_purchase_quant'])
                        {
                            continue 1;
                        }
                    }
                    
                    if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product')
                    {
                        if(is_array($value['product_ids']) && !empty($value['product_ids']))
                        {
                            foreach ($value['product_ids'] as $pids => $pid) {

                                foreach ($cart->cart_contents as $cart_item_key => $cart_item) {

                                    if($cart_item['product_id'] == $pid)
                                    {
                                        $rtwwdpd_original_price = $cart_item['data']->get_price();

                                        $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];

                                        $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];
                                        
                                        $rtwwdpd_dscnted_price = 0;

                                        if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_dis_percent')
                                        {
                                            $rtwwdpd_dscnted_price = $rtwwdpd_original_price - ($rtwwdpd_original_price * ($rtwwdpd_dis_val/100));
                                        }
                                        else{
                                            if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                                            {
                                                $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                                            }
                                            $rtwwdpd_dscnted_price = $rtwwdpd_original_price - $rtwwdpd_dis_val;
                                            if($rtwwdpd_original_price < $rtwwdpd_dis_val)
                                            {
                                                $rtwwdpd_dscnted_price = 0;
                                            }
                                        }

                                        $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_dscnted_price, 'rtwwdpd_nth_order', 'rtwwdpd_nth_order');
                                    }
                                }
                            }
                        }
                    }
                    elseif($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category')
                    {
                        if(is_array($value['category_ids']) && !empty($value['category_ids']))
                        {
                            foreach ($value['category_ids'] as $cids => $cid) 
                            {
                                foreach ($cart->cart_contents as $cart_item_key => $cart_item) 
                                {
                                    $rtwwdpd_catids = array();

                                    if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                                    {
                                        $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                    }else{
                                        $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                    }
                                    
                                    if( is_array($rtwwdpd_catids) && in_array($cid, $rtwwdpd_catids)  )
                                    {
                                        $rtwwdpd_original_price = $cart_item['data']->get_price();

                                        $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];

                                        $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];
                                        
                                        $rtwwdpd_dscnted_price = 0;

                                        if($value['rtwwdpd_dsnt_type'] == 'rtwwdpd_dis_percent')
                                        {
                                            $rtwwdpd_dscnted_price = $rtwwdpd_original_price - ($rtwwdpd_original_price * ($rtwwdpd_dis_val/100));
                                        }
                                        else{
                                            if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                                            {
                                                $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                                            }
                                            $rtwwdpd_dscnted_price = $rtwwdpd_original_price - $rtwwdpd_dis_val;
                                            if($rtwwdpd_original_price < $rtwwdpd_dis_val)
                                            {
                                                $rtwwdpd_dscnted_price = 0;
                                            }
                                        }
                                        
                                        $this->rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_dscnted_price, 'rtwwdpd_nth_order', 'rtwwdpd_nth_order' );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            ////////////////// Nth Order Discount ////////////////////

        }

    }

    /**
     * Function to check if current customer month count.
     *
     * @since    1.0.0
     */
    function current_customer_month_count( $rtwwdpd_user_id=null ) {
        if ( empty($rtwwdpd_user_id) ){
            $rtwwdpd_user_id = get_current_user_id();
        }
        // Date calculations to limit the query
        $tywwdpd_today_year = date( 'Y' );
        $rtwwdpd_today_month = date( 'm' );
        $rtwwdpd_day = date( 'd' );
        if ($rtwwdpd_today_month == '01') {
            $rtwwdpd_month = '12';
            $rtwwdpd_year = $tywwdpd_today_year - 1;
        } else{
            $rtwwdpd_month = $rtwwdpd_today_month - 1;
            $rtwwdpd_month = sprintf("%02d", $rtwwdpd_month);
            $rtwwdpd_year = $tywwdpd_today_year - 1;
        }

        // ORDERS FOR LAST 30 DAYS (Time calculations)
        $rtwwdpd_now = strtotime('now');
        // Set the gap time (here 30 days)
        $rtwwdpd_gap_days = 30;
        $rtwwdpd_gap_days_in_seconds = 60*60*24*$rtwwdpd_gap_days;
        $rtwwdpd_gap_time = $rtwwdpd_now - $rtwwdpd_gap_days_in_seconds;

        $rtwwdpd_args = array(
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed,wc-processing', 
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $rtwwdpd_user_id,
            'date_query' => array(
                'relation' => 'OR',
                array(
                    'year' => $tywwdpd_today_year,
                    'month' => $rtwwdpd_today_month,
                ),
                array(
                    'year' => $rtwwdpd_year,
                    'month' => $rtwwdpd_month,
                ),
            ),
        );

        $rtwwdpd_customer_orders = get_posts( $rtwwdpd_args );

        $rtwwdpd_count = 0;
        if (!empty($rtwwdpd_customer_orders)) {
            $rtwwdpd_order_totl = 0;
            $rtwwdpd_customer_orders_date = array();
            foreach ( $rtwwdpd_customer_orders as $customer_order ){
                $rtwwdpd_customer_order_date = strtotime($customer_order->post_date);

                if ( $rtwwdpd_customer_order_date > $rtwwdpd_gap_time ) {
                    $rtwwdpd_customer_order_date;
                    $rtwwdpd_order = new WC_Order( $customer_order->ID );
                    $rtwwdpd_order_items = $rtwwdpd_order->get_items();

                // Going through each current customer items in the order
                    if(is_array($rtwwdpd_order_items) && !empty($rtwwdpd_order_items))
                    {
                        foreach ( $rtwwdpd_order_items as $order_item ){
                     
                            $rtwwdpd_order_totl += $order_item->get_total();
                            $rtwwdpd_count++;
                        }
                    }
                }
            }

            $rtwwdpd_result = array('count' => $rtwwdpd_count,
                'order_totl' => $rtwwdpd_order_totl);
            return $rtwwdpd_result;
        }
    }

    /**
     * Function to calculate discount for other discount rule.
     *
     * @since    1.0.0
     */
    public static function rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id ){

        global $woocommerce;
        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        if ( $rtwwdpd_adjusted_price === false ) {
            return;
        }
        $rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
        if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

            $_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
            $rtwwdpd_display_price = wc_get_price_including_tax( $_product );
            if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
            {
                $rtwwdpd_display_price = $_product->get_price();
                $rtwwdpd_display_price = wc_get_price_including_tax($_product);//->get_price();

                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $rtwwdpd_original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $rtwwdpd_original_price ) );
            }
            else{
                $rtwwdpd_display_price = $_product->get_regular_price();
                $rtwwdpd_display_price = wc_get_price_including_tax($_product);
                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $rtwwdpd_original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $rtwwdpd_original_price ) );
            }

            WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $rtwwdpd_adjusted_price );

            if ( $_product->get_type() == 'composite' ) {
                WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $rtwwdpd_adjusted_price;
            }

            if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

                $rtwwdpd_discount_data                                           = array(
                    'by'                => array( $module ),
                    'set_id'            => $set_id,
                    'price_base'        => $rtwwdpd_original_price,
                    'display_price'     => $rtwwdpd_display_price,
                    'price_adjusted'    => $rtwwdpd_adjusted_price,
                    'applied_discounts' => array(
                        array(
                            'by'             => $module,
                            'set_id'         => $set_id,
                            'price_base'     => $rtwwdpd_original_price,
                            'price_adjusted' => $rtwwdpd_adjusted_price
                        )
                    )
                );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;
                
            } else {

                $rtwwdpd_existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];

                $rtwwdpd_discount_data = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $set_id,
                    'price_base'     => $rtwwdpd_original_price,
                    'display_price'  => $rtwwdpd_existing['display_price'],
                    'price_adjusted' => $rtwwdpd_adjusted_price
                );

                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;

                $history = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $rtwwdpd_existing['set_id'],
                    'price_base'     => $rtwwdpd_existing['price_base'],
                    'price_adjusted' => $rtwwdpd_existing['price_adjusted']
                );

                array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
                
                WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = $history;
            }
        }
    }

    /**
     * Function to calculate discount for cart discount rule.
     *
     * @since    1.0.0
     */
    public static function rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id ) {

        $rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
        do_action( 'rtwwdpd_memberships_discounts_disable_price_adjustments' );
        $rtwwdpd_adjusted_price = apply_filters( 'rtwwdpd_dynamic_pricing_apply_cart_item_adjustment', $rtwwdpd_adjusted_price, $cart_item_key, $rtwwdpd_original_price, $module );

        //Allow extensions to stop processing of applying the discount.  Added for subscriptions signup fee compatibility
        if ( $rtwwdpd_adjusted_price === false ) {
            return;
        }

        if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {


            $_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
            
            if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $_product ) ) {
                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product ) : wc_get_price_including_tax( $_product );
            } else {
                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $rtwwdpd_original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $rtwwdpd_original_price ) );
            }
            if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
            {
                $rtwwdpd_display_price = $_product->get_price();
            }
            else{
                $rtwwdpd_display_price = $_product->get_regular_price();
            }
            
            WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $rtwwdpd_adjusted_price );

            if ( $_product->get_type() == 'composite' ) {
                WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $rtwwdpd_adjusted_price;
            }

            if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

                $rtwwdpd_discount_data                                           = array(
                    'by'                => array( $module ),
                    'set_id'            => $set_id,
                    'price_base'        => $rtwwdpd_original_price,
                    'display_price'     => $rtwwdpd_display_price,
                    'price_adjusted'    => $rtwwdpd_adjusted_price,
                    'applied_discounts' => array(
                        array(
                            'by'             => $module,
                            'set_id'         => $set_id,
                            'price_base'     => $rtwwdpd_original_price,
                            'price_adjusted' => $rtwwdpd_adjusted_price
                        )
                    )
                );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;
            } else {

                $rtwwdpd_existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];

                $rtwwdpd_discount_data = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $set_id,
                    'price_base'     => $rtwwdpd_original_price,
                    'display_price'  => $rtwwdpd_existing['display_price'],
                    'price_adjusted' => $rtwwdpd_adjusted_price
                );

                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;

                $history = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $rtwwdpd_existing['set_id'],
                    'price_base'     => $rtwwdpd_existing['price_base'],
                    'price_adjusted' => $rtwwdpd_existing['price_adjusted']
                );
                array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = $history;
            }
        }
        
        do_action( 'rtwwdpd_memberships_discounts_enable_price_adjustments' );
        do_action( 'rtwwdpd_dynamic_pricing_apply_cartitem_adjustment', $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id );
    }

    /**
     * Function to calculate discount for cart discount rule.
     *
     * @since    1.0.0
     */
    public static function rtw_apply_carts_items_adjust( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id ) {

        $sabcd = 'ification_done';
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_ver'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
        $rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
        do_action( 'rtwwdpd_memberships_discounts_disable_price_adjustments' );
        $rtwwdpd_adjusted_price = apply_filters( 'rtwwdpd_dynamic_pricing_apply_cart_item_adjustment', $rtwwdpd_adjusted_price, $cart_item_key, $rtwwdpd_original_price, $module );

        //Allow extensions to stop processing of applying the discount.  Added for subscriptions signup fee compatibility
        if ( $rtwwdpd_adjusted_price === false ) {
            return;
        }

        if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {


            $_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
            
            if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $_product ) ) {
                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product ) : wc_get_price_including_tax( $_product );
            } else {
                $rtwwdpd_display_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $_product, array( 'price' => $rtwwdpd_original_price ) ) : wc_get_price_including_tax( $_product, array( 'price' => $rtwwdpd_original_price ) );
            }
            
            WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $rtwwdpd_adjusted_price );

            if ( $_product->get_type() == 'composite' ) {
                WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $rtwwdpd_adjusted_price;
            }

            if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

                $rtwwdpd_discount_data                                           = array(
                    'by'                => array( $module ),
                    'set_id'            => $set_id,
                    'price_base'        => $rtwwdpd_original_price,
                    'display_price'     => $rtwwdpd_display_price,
                    'price_adjusted'    => $rtwwdpd_adjusted_price,
                    'applied_discounts' => array(
                        array(
                            'by'             => $module,
                            'set_id'         => $set_id,
                            'price_base'     => $rtwwdpd_original_price,
                            'price_adjusted' => $rtwwdpd_adjusted_price
                        )
                    )
                );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;
            } else {

                $rtwwdpd_existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];

                $rtwwdpd_discount_data = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $set_id,
                    'price_base'     => $rtwwdpd_original_price,
                    'display_price'  => $rtwwdpd_existing['display_price'],
                    'price_adjusted' => $rtwwdpd_adjusted_price
                );

                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;

                $history = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $rtwwdpd_existing['set_id'],
                    'price_base'     => $rtwwdpd_existing['price_base'],
                    'price_adjusted' => $rtwwdpd_existing['price_adjusted']
                );
                array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = $history;
            }
        }
        do_action( 'rtwwdpd_memberships_discounts_enable_price_adjustments' );
        do_action( 'rtwwdpd_dynamic_pricing_apply_cartitem_adjustment', $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id );
        }
    }

    /**
     * Function to calculate discounts.
     *
     * @since    1.0.0
     */
    function rtwwdpd_before_calculate_totals($cart)
    {

        global $woocommerce;
        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        $sabcd = 'verification_done';
        $rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
        global $wpdb;
        $subtotal = 0;
        foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
        {
            $_product = $val['data'];
            if ( $_product->get_id() == 60 )
            {
                $found = true;
                $__price = $val['data']->get_price();
                $quantt = $val['quantity'];
                $pos = stripos($cart_item_k, 'rtwwdpd_free_prod');
                if($pos !== false)
                {
                    $val['data']->set_price(0);
                }
            }
            
            $pos = stripos($cart_item_k, 'rtw_free_prod');
            if($pos === false)
            {
                $subtotal += wc_get_price_excluding_tax($val['data'])* $val['quantity'];//$val['data']->wc_get_price_excluding_tax('edit') * $val['quantity'];
              
            }
        }
        
        $rtwwdpd_today_date = current_time('Y-m-d');
        $rtwwdpd_get_option = get_option('rtwwdpd_bogo_rule');
        $rtwwdpd_get_cat_option = get_option('rtwwdpd_bogo_cat_rule');
        $rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');
        $rtwwdpd_cat_ids = array();
        $rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
        $ii = 0;
        if(is_array($cart->cart_contents) && !empty($cart->cart_contents))
        {
            $rtwwdpd_product_ids = array();
            foreach ( $cart->cart_contents as $cart_item_key => $values ) 
            {
                $rtwwdpd_product_ids[] = $values['data']->get_id();
                if(!empty($values['data']->get_parent_id()))
                {
                    $rtwwdpd_product_ids[] = $values['data']->get_parent_id();
                }
            }
            foreach ($cart->cart_contents as $cart_item_key => $cart_item) 
            {
                $rtwwdpd_cat_ids[] = $cart_item['data']->get_category_ids();
                if(isset($rtwwdpd_get_settings['bogo_rule']) && $rtwwdpd_get_settings['bogo_rule'] == 1 && is_array($rtwwdpd_get_option) && !empty($rtwwdpd_get_option))
                {
                    if(is_array($rtwwdpd_get_option) && !empty($rtwwdpd_get_option))
                    {
                        $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
                        foreach ($rtwwdpd_get_option as $key => $value) 
                        {
                            if($active_dayss == 'yes')
                            {
                                $active_days = isset($value['rtwwwdpd_bogo_day']) ? $value['rtwwwdpd_bogo_day'] : array();
                                $current_day = date('N');
        
                                if(!in_array($current_day, $active_days))
                                {
                                    continue;
                                }
                            }
                            $pur_quantity = 0;
                            foreach ($value['combi_quant'] as $ke => $valu) 
                            {
                                $rtwwdpd_p_c[] = $valu;
                                if(!isset($valu) || !is_numeric($valu))
                                {
                                    $valu = 0;
                                }
                                $pur_quantity += $valu;
                            }
                            $rtwwdpd_purchased_quantity = 0;
                            foreach ( $cart->cart_contents as $cart_item_key => $cart_item )
                            {
                                
                                if( stripos($cart_item_key , 'rtw_free_prod') === false )
                                {	 
                                   
        
                                    if( isset($value['product_id']) && !empty($value['product_id']) && is_array($value['product_id']) && (in_array($cart_item['data']->get_id(), $value['product_id']) || in_array($cart_item['data']->get_parent_id(), $value['product_id']) ))
                                    {
                                        $rtwwdpd_purchased_quantity += $cart_item['quantity'];
                                    }
                                }
                            }
                            $rtwwdpd_user = wp_get_current_user();
                            $rtwwdpd_user_role = $value['rtwwdpd_select_roles'];
                            $rtwwdpd_role_matched = false;
                            if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                            {
                                foreach ($rtwwdpd_user_role as $rol => $role) 
                                {
                                    if($role == 'all')
                                    {
                                        $rtwwdpd_role_matched = true;
                                    }
                                    if (in_array( $role, (array) $rtwwdpd_user->roles ) ) 
                                    {
                                        $rtwwdpd_role_matched = true;
                                    }
                                }
                            }
                            if($rtwwdpd_role_matched == false)
                            {
                                continue;
                            }

                            $rtw_curnt_dayname = date("N");
                            $rtwwdpd_day_waise_rule = false;
                            if(isset($value['rtwwdpd_enable_day_bogo']) && $value['rtwwdpd_enable_day_bogo'] == 'yes')
                            {
                                
                                if(isset($value['rtwwdpd_select_day_bogo']) && !empty($value['rtwwdpd_select_day_bogo']))
                                {
                                    if($value['rtwwdpd_select_day_bogo'] == $rtw_curnt_dayname)
                                    {
                                        $rtwwdpd_day_waise_rule = true;
                                    }
                                }
                                if($rtwwdpd_day_waise_rule == false)
                                {
                                    continue;
                                }
                            }
                            $rtwwdpd_restricted_mails = isset( $value['rtwwdpd_select_emails'] ) ? $value['rtwwdpd_select_emails'] : array();
        
                            $rtwwdpd_cur_user_mail = get_current_user_id();
                            if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
                            {
                                continue 1;
                            }
                            if($subtotal < $value['rtwwdpd_bogo_min_spend'])
                            {
                                continue;
                            }
                            if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
                            {
                                if(is_array( $value['rtwbogo'] ) && !empty( $value['rtwbogo'] ))
                                {
                                    foreach( $value['rtwbogo'] as $free_id )
                                    {
                                        $rtwwdpd_free_p_id = $free_id;
                                        $rtwwdpd_p_quant = $value['combi_quant'][$ii];
                                        $value_pro_id = array();
                                        if(isset($value['product_id']) && is_array($value['product_id']) && !empty($value['product_id']))
                                        {
                                            $value_pro_id = $value['product_id'];
                                        }
                                        $rtwwdpd_free_qunt = $value['bogo_quant_free'][$ii];
                                        
                                        // $rtwwdpd_rule_on = apply_filters('rtwwdpd_rule_applied_on_bogo', $key );
            
                                        $rtwwdpd_rule_on = isset($value['rtwwdpd_bogo_rule_on']) ? $value['rtwwdpd_bogo_rule_on'] : 'product';
            
                                        // if($rtwwdpd_rule_on == $key)
                                        // {
                                        // 	$rtwwdpd_rule_on = 'product';
                                        // }
                                        $result_array = array_diff( $value_pro_id, $rtwwdpd_product_ids );
                                        
                                        if( empty($result_array) && $rtwwdpd_rule_on == 'product' )
                                        {
                                            if($pur_quantity <= $rtwwdpd_purchased_quantity)
                                            {
                                                if ( sizeof( WC()->cart->get_cart() ) > 0 ) 
                                                {
                                                    if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                                    {
                                                        foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) {
                                                            $_product = $val['data'];
                                                            if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                            {
                                                                $found = true;
            
                                                                $__price = $val['data']->get_price();
                                                                $quantt = $val['quantity'];
                                                            
                                                            
                                                                $pos = stripos($cart_item_k, 'rtw_free_prod');
                                                                if($pos !== false)
                                                                {
                                                                    $val['data']->set_price(0);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                                        {
                                                            $_product = $val['data'];
                                                            if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                            {
                                                                $found = true;
                                                                $rtwwdpd_free_p_id = $val['product_id'];
                                                                $rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                                if($rtwwdpd_free_p_id==$val['data']->get_id())
                                                                {
                                                                    $__price =  $rtwwdpd_product_data->get_price();
                                                                    $quantt = $val['quantity'];
                                                                    // $pos = stripos($cart_item_k, 'rtw_free_prod
                                                                    
                                                                    if($found !== false)
                                                                    {   
                                                                        $this->rtw_product_rule_bogo( $cart_item_k, $__price, 0, 'bogo', '', '','' );
                                                                        
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } 
                                            }
                                            else
                                            {
                                                foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) {
                                                    $_product = $val['data'];
                                                    if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                    {
                                                        $found = true;
                                                        
                                                        $prod_id = $val['data']->get_id();
                                                        $product = wc_get_product($prod_id);
                                                        $__price = $product->get_price();
                                                        $quantt = $val['quantity'];
            
                                                        $pos = stripos($cart_item_k, 'rtw_free_prod');
            
                                                        if($pos !== false)
                                                        {
                                                            $val['data']->set_price($__price);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        elseif($rtwwdpd_rule_on == 'min_purchase'){
                                            if( $subtotal < $value['rtwwdpd_min_purchase'] )
                                            {
                                            continue;
                                            }
                                            {
                                                if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                                {
                                                    foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) {
                                                        $_product = $val['data'];
                                                        if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                        {
                                                            $found = true;
                                                            $__price = $val['data']->get_price();
                                                            $quantt = $val['quantity'];
            
                                                            $pos = stripos($cart_item_k, 'rtw_free_prod');
            
                                                            if($pos !== false)
                                                            {
                                                                $val['data']->set_price(0);
                                                            }
                                                        }
                                                    }
                                                }
                                            } 
                                        }
                                    }
                                }
                            }
                        
                          ///////////////////// start extra addition in bogo rule for discount on free product  
                            else
                            {      
                                if(isset($value['rtwwdpd_dscnt_cat_val']) && !empty($value['rtwwdpd_dscnt_cat_val']))
                                {
                                    $value['rtwwdpd_dscnt_cat_val'];
                                } 
                                else
                                {
                                    $value['rtwwdpd_dscnt_cat_val']=100;
                                }
                                if(is_array( $value['rtwbogo'] ) && !empty( $value['rtwbogo'] ))
                                {
                                    foreach( $value['rtwbogo'] as $free_id )
                                    {
                                        $rtwwdpd_free_p_id = $free_id;
                                        $rtwwdpd_p_quant = $value['combi_quant'][$ii];
                                        $value_pro_id = array();
                                        if(isset($value['product_id']) && is_array($value['product_id']) && !empty($value['product_id']))
                                        {
                                            $value_pro_id = $value['product_id'];
                                        }
                                        $rtwwdpd_free_qunt = $value['bogo_quant_free'][$ii];
                                        $rtwwdpd_rule_on = isset($value['rtwwdpd_bogo_rule_on']) ? $value['rtwwdpd_bogo_rule_on'] : 'product';
                                        $result_array = array_diff( $value_pro_id, $rtwwdpd_product_ids );
                                        if( empty($result_array) && $rtwwdpd_rule_on == 'product' )
                                        {
                                            if($pur_quantity <= $rtwwdpd_purchased_quantity)
                                            {
                                                if ( sizeof( WC()->cart->get_cart() ) > 0 ) 
                                                {
                                                    if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                                    {
                                                        foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                                        {
                                                            
                                                            $_product = $val['data'];
                                                            if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                            {
                                                                $found = true;
                                                                $rtwwdpd_free_p_id = $val['product_id'];
                                                                $rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                                $__price =  $rtwwdpd_product_data->get_price();
                                                                $quantt = $val['quantity'];
                                                                $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                                if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                                {
                                                                    $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                                    $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price );
                                                                    $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                                }
                                                                $pos = stripos($cart_item_k, 'rtw_free_prod');
                                                                if($pos !== false)
                                                                {    
                                                                    $this->rtw_product_rule_bogo( $cart_item_k, $__price, $rtwwdpd_price_adjusted, 'bogo', '', '','' );
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                                        {
                                                            $_product = $val['data'];
                                                            if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                            {
                                                                $found = true;
                                                                $rtwwdpd_free_p_id = $val['product_id'];
                                                                $rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                                if($rtwwdpd_free_p_id==$val['data']->get_id())
                                                                {
                                                                    $__price =  $rtwwdpd_product_data->get_price();
                                                                    $quantt = $val['quantity'];
                                                                    // $pos = stripos($cart_item_k, 'rtw_free_prod');
                                                                    $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                                    
                                                                        if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                                        {
                                                                            $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                                            $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price );
                                                                            $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                                        }
                                                                    
                                                                    if($found !== false)
                                                                    {   
                                                                        $this->rtw_product_rule_bogo( $cart_item_k, $__price, $rtwwdpd_price_adjusted, 'bogo', '', '','' );
                                                                        
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } 
                                            }
                                            else
                                            {
                                                foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) {
                                                    $_product = $val['data'];
                                                    if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                    {
                                                        $found = true;
                                                        
                                                        $prod_id = $val['data']->get_id();
                                                        $product = wc_get_product($prod_id);
                                                        $__price = $product->get_price();
                                                        $quantt = $val['quantity'];
        
                                                        $pos = stripos($cart_item_k, 'rtw_free_prod');
        
                                                        if($pos !== false)
                                                        {
                                                            $val['data']->set_price($__price);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        elseif($rtwwdpd_rule_on == 'min_purchase'){
                                            if( $subtotal < $value['rtwwdpd_min_purchase'] )
                                            {
                                            continue;
                                            }
                                            {
                                                if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                                {
                                                    foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                                    {
                                                        $_product = $val['data'];
                                                        if ( $_product->get_id() == $rtwwdpd_free_p_id )
                                                        {
                                                            $found = true;
                                                            $__price = $val['data']->get_price();
                                                            $quantt = $val['quantity'];
        
                                                            $pos = stripos($cart_item_k, 'rtw_free_prod');
        
                                                            if($pos !== false)
                                                            {
                                                                $val['data']->set_price(0);
                                                            }
                                                        }
                                                    }
                                                }
                                            } 
                                        }
                                    }
                                }
                            }
                         //////////////////////// End extra addition in bogo rule for discount on free product  
                        }
                    }
                }
                if(isset($rtwwdpd_get_settings['bogo_cat_rule']) && $rtwwdpd_get_settings['bogo_cat_rule'] == 1 && is_array($rtwwdpd_get_cat_option) && !empty($rtwwdpd_get_cat_option))
                { 
                    foreach ( $rtwwdpd_get_cat_option as $key => $value ) 
                    {
                        $rtw_cat_id = $value['category_id'][$ii];
                        $rtwwdpd_free_p_id = array();
                        $rtwwdpd_free_quant = '';
                        if( !isset( $value['rtwbogo'][$ii] ) )
                        {
                            continue 1;
                        }
                        else{
                            $rtwwdpd_free_p_id[] = $value['rtwbogo'][$ii];
                        }
                        if(!isset($value['bogo_quant_free'][$ii]))
                        {
                            continue 1;
                        }
                        else
                        {
                            $rtwwdpd_free_quant = $value['bogo_quant_free'][$ii];
                        }
                       
                        $quant_to_purchased = 0;
                        $category_to_purchase = array();
                        $category_in_cart = array();
                        $quantity_in_cart = 0;
                        foreach ($value['combi_quant'] as $ke => $valu) 
                        {
                            $quant_to_purchased += $valu;
                        }
                        foreach($value['category_id'] as $pro => $proid)
                        {
                            $category_to_purchase[] = $proid;
                        }
                        foreach ( WC()->cart->get_cart() as $items => $item )
                        {
                            if( stripos( $items , '_free') !== false )
                            {
                            }
                            else
                            {
                                if( isset($item['variation_id']) && !empty($item['variation_id']) )
								{
									$rtwwdpd_catids = wp_get_post_terms( $item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );

									if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) )
									{
										foreach ($rtwwdpd_catids as $cc => $c) 
                                        {
											$category_in_cart[] = $c;
										}
									}
									else
									{
										$categorys = $cart_item['data']->get_category_ids();
										foreach ($categorys as $cc => $c) 
                                        {
											$category_in_cart[] = $c;
										}
									}
									if(array_intersect($rtwwdpd_catids, $category_to_purchase))
									{
										$quantity_in_cart += $item['quantity'];
									}
								}
                                else
                                {
                                    $categorys = $item['data']->get_category_ids();
                                    if( is_array($categorys) && !empty($categorys) )
                                    {
                                        foreach ($categorys as $cc => $c) 
                                        {
                                            $category_in_cart[] = $c;
                                        }
                                    }
                                    if(array_intersect($categorys, $category_to_purchase))
                                    {
                                        $quantity_in_cart += $item['quantity'];
                                    }
                                }
                            }
                        }
                        $intersect_array = array_intersect($category_in_cart, $category_to_purchase );
                        if( empty( $intersect_array ) )
                        {
                            continue 1;
                        }
                        
                        if( $quantity_in_cart < $quant_to_purchased )
                        {
                            continue 1;
                        }
                        $matched = false;
                        $rtwwdpd_catids = array();

                        if(isset($value['rtwwdpd_dscnt_cat_val']) && !empty($value['rtwwdpd_dscnt_cat_val']))
                        {
                            $value['rtwwdpd_dscnt_cat_val'];
                        } 
                        else
                        {
                            $value['rtwwdpd_dscnt_cat_val']=100;
                        }
                        if ( sizeof( WC()->cart->get_cart() ) > 0 ) 
                        {   
                            foreach($rtwwdpd_free_p_id as $f_val)
                            {
                                foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                {
                                    $rtwwdpd_catids = $val['data']->get_category_ids();
                                    if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                    {
                                        /////////////////////////////////////////
                                        $rtwwdpd_f_quant = 0;
                                        $rtwwdpd_free_qunt = 0;
                                        $rtwwdpd_f_quant = floor( $quantity_in_cart / $value['combi_quant'][0] );
                                        if( $rtwwdpd_f_quant >= 2 )
                                        {
                                            $rtwwdpd_free_qunt = ((isset( $value['bogo_quant_free'][0] ) ? $value['bogo_quant_free'][0] : 0 )* $rtwwdpd_f_quant);
                                        }
                                        else 
                                        {
                                            $rtwwdpd_free_qunt = isset( $value['bogo_quant_free'][0] ) ? $value['bogo_quant_free'][0] : 0;
                                        }
                                        // if(in_array($value['category_id'][0], $rtwwdpd_catids))
                                        {
                                            $_product = $val['data'];
                                            
                                            
                                            if ($_product->get_id() == $f_val)
                                            {
                                                $found = true;
                                                $rtwwdpd_free_p_id = $val['product_id'];
                                                
                                                $rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                $__price = $rtwwdpd_product_data->get_price();
                                                
                                                $pos = stripos($cart_item_key, 'rtw_free_prod_bogoc');
                                                $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                
                                                if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                {
                                                    $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                    $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price);
                                                    ;
                                                    $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                    
                                                }
                                                if( $pos !== false )
                                                {
                                                    //  $val['data']->set_price($rtwwdpd_price_adjusted)
                                                   
                                                    $this->rtw_product_rule_bogo( $cart_item_k, $__price,$rtwwdpd_price_adjusted, 'bogo_cat', '', '','' );
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                            
                                        $_product = $cart_item['data'];
                                        foreach($rtwwdpd_free_p_id as $val)
                                        {
                                            if ( $val == $_product->get_id() )
                                            {
                                                $found = true;
                                                $rtwwdpd_product_data = wc_get_product($val);
                                                $__price = $rtwwdpd_product_data->get_price();
                                                $pos = stripos($cart_item_key, 'rtw_free_prod_bogoc');
                                                $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                    
                                                if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                {
                                                    $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                    $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price);
                                                    ;
                                                    $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                }
                                                if( $found !== false )
                                                {
                                                    $this->rtw_product_rule_bogo( $cart_item_key, $__price, $rtwwdpd_price_adjusted, 'bogo_c'.$key, '','' , '');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
        
                }

                $rtwwdpd_get_tag_option = get_option('rtwwdpd_bogo_tag_rule');

                if(isset($rtwwdpd_get_settings['bogo_tag_rule']) && $rtwwdpd_get_settings['bogo_tag_rule'] == 1 && is_array($rtwwdpd_get_tag_option) && !empty($rtwwdpd_get_tag_option))
                { 
                    
                    foreach ( $rtwwdpd_get_tag_option as $key => $value ) 
                    {
                        
                        $rtw_cat_id = $value['tag_id'][$ii];
                        $rtwwdpd_free_p_id = array();
                        $rtwwdpd_free_quant = '';
                        if( !isset( $value['rtwbogo'][$ii] ) )
                        {
                            continue 1;
                        }
                        else{
                            $rtwwdpd_free_p_id[] = $value['rtwbogo'][$ii];
                        }
                        if(!isset($value['bogo_quant_free'][$ii]))
                        {
                            continue 1;
                        }
                        else
                        {
                            $rtwwdpd_free_quant = $value['bogo_quant_free'][$ii];
                        }
                       
                        $quant_to_purchased = 0;
                        $tag_to_purchase = array();
                        $tags_in_cart = array();
                        $quantity_in_cart = 0;
                        foreach ($value['combi_quant'] as $ke => $valu) 
                        {
                            $quant_to_purchased += $valu;
                        }
                        foreach($value['tag_id'] as $pro => $proid)
                        {
                            $tag_to_purchase[] = $proid;
                        }
                        foreach ( WC()->cart->get_cart() as $items => $item )
                        {
                            if( stripos( $items , '_free') !== false )
                            {
                            }
                            else
                            {
                                if( isset($item['variation_id']) && !empty($item['variation_id']) )
								{
									$rtwwdpd_tagids = wp_get_post_terms( $item['data']->get_parent_id(), 'product_tag', array( 'fields' => 'ids' ) );

									if( is_array($rtwwdpd_tagids) && !empty($rtwwdpd_tagids) )
									{
										foreach ($rtwwdpd_tagids as $cc => $c) 
                                        {
											$tags_in_cart[] = $c;
										}
									}
									else
									{
										$tags = $cart_item['data']->get_tag_ids();
										foreach ($tags as $cc => $c) 
                                        {
											$tags_in_cart[] = $c;
										}
									}
									if(array_intersect($rtwwdpd_tagids, $tag_to_purchase))
									{
										$quantity_in_cart += $item['quantity'];
									}
								}
                                else
                                {
                                    $tags = $item['data']->get_tag_ids();
                                   
                                    if( is_array($tags) && !empty($tags) )
                                    {
                                        foreach ($tags as $cc => $c) 
                                        {
                                            $tags_in_cart[] = $c;
                                        }
                                    }
                                    if(array_intersect($tags, $tag_to_purchase))
                                    {
                                        $quantity_in_cart += $item['quantity'];
                                    }
                                }
                            }
                        }
                      
                        $intersect_array = array_intersect($tags_in_cart, $tag_to_purchase );
                        if( empty( $intersect_array ) )
                        {
                            continue 1;
                        }
                        
                        if( $quantity_in_cart < $quant_to_purchased )
                        {
                            continue 1;
                        }
                        $matched = false;
                        $rtwwdpd_tagids = array();


                        if ( sizeof( WC()->cart->get_cart() ) > 0 ) 
                        {   
                           
                            foreach($rtwwdpd_free_p_id as $f_val)
                            {
                                foreach ( WC()->cart->get_cart() as $cart_item_k => $val ) 
                                {
                                    $rtwwdpd_tagids = $val['data']->get_tag_ids();
                                    
                                    if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
                                    {
                                        /////////////////////////////////////////
                                        $rtwwdpd_f_quant = 0;
                                        $rtwwdpd_free_qunt = 0;
                                        $rtwwdpd_f_quant = floor( $quantity_in_cart / $value['combi_quant'][0] );
                                        if( $rtwwdpd_f_quant >= 2 )
                                        {
                                            $rtwwdpd_free_qunt = ((isset( $value['bogo_quant_free'][0] ) ? $value['bogo_quant_free'][0] : 0 )* $rtwwdpd_f_quant);
                                        }
                                        else 
                                        {
                                            $rtwwdpd_free_qunt = isset( $value['bogo_quant_free'][0] ) ? $value['bogo_quant_free'][0] : 0;
                                        }
                                        // if(in_array($value['category_id'][0], $rtwwdpd_tagids))
                                        {
                                            $_product = $val['data'];
                                            
                                            
                                            if ($_product->get_id() == $f_val)
                                            {
                                               
                                                $found = true;
                                                $rtwwdpd_free_p_id = $val['product_id'];
                                               
                                                $rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                $__price = $rtwwdpd_product_data->get_price();
                                                
                                                $pos = stripos($cart_item_key, 'rtw_free_prod_bogot');
                                                
                                                // $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                
                                                // if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                // {
                                                //     $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                //     $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price);
                                                //     ;
                                                //     $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                    
                                                // }
                                                
                                                if( $found )
                                                {
                                                   
                                                    //  $val['data']->set_price($rtwwdpd_price_adjusted)
                                                   
                                                    $this->rtw_product_rule_bogo( $cart_item_k, $__price,0, 'bogo_t', '', '','' );
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                            
                                        $_product = $cart_item['data'];
                                        foreach($rtwwdpd_free_p_id as $val)
                                        {
                                            if ( $val == $_product->get_id() )
                                            {
                                                $found = true;
                                                $rtwwdpd_product_data = wc_get_product($val);
                                                $__price = $rtwwdpd_product_data->get_price();
                                                $pos = stripos($cart_item_key, 'rtw_free_prod_bogot');
                                                $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $value['rtwwdpd_dscnt_cat_val'], 'product_id', $cart_item, $this );	
                                                    
                                                if(isset($rtwwdpd_amount) && !empty($rtwwdpd_amount))
                                                {
                                                    $rtwwdpd_amount = ($rtwwdpd_amount / 100);
                                                    $rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $__price);
                                                    ;
                                                    $rtwwdpd_price_adjusted = ( floatval( $__price ) - $rtwwdpd_discnted_val );
                                                }
                                                if( $found !== false )
                                                {
                                                    $this->rtw_product_rule_bogo( $cart_item_key, $__price, $rtwwdpd_price_adjusted, 'bogo_t'.$key, '','' , '');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $ii++;
                    }
        
                }
            }
        }
    }
        
        $rtwwdpd_today_date = current_time('Y-m-d');
        $rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');
        $rtwwdpd_i = 0;
        if(is_array($rtwwdpd_get_settings) && !empty($rtwwdpd_get_settings)){
            foreach ($rtwwdpd_get_settings as $key => $value) {
                if($key == 'cart_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'pro_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'tier_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'pro_com_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'cat_com_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'tier_cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }	
                elseif($key == 'var_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_cat_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'bogo_tag_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'attr_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif($key == 'prod_tag_rule_row')
                {
                    $rtwwdpd_priority[$rtwwdpd_i] = $key;
                    $rtwwdpd_i++;
                }
                elseif ($key == 'rtw_offer_select') {
                    $rtwwdpd_select_offer = $value;
                }
                elseif ($key == 'rtwwdpd_rule_per_page') {
                    $rtwwdpd_rule_per_page = $value;
                }
            }
        }
        $this->rtwwdpd_set_rules = $rtwwdpd_priority;
            // ////////////////// Least amount product discount ////////////////////
            $rtwwdpd_least_option = array();
            $rtwwdpd_temp_cart = WC()->cart->get_cart(); 
            $rtwwdpd_least_enable = get_option( 'rtwwdpd_enable_least_free' );
            if( isset( $rtwwdpd_least_enable ) && $rtwwdpd_least_enable == 'enable' )
            {
                $rtwwdpd_least_option = get_option( 'rtwwdpd_get_least_free' );
            }
    
            if( isset($rtwwdpd_least_option) && is_array($rtwwdpd_least_option) && !empty($rtwwdpd_least_option) )
            {
                $i = 0;
                $rtwwdpd_user = wp_get_current_user();
                $rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
                $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id() );
                $rtwwdpd_today_date = current_time('Y-m-d');
                $rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
                $set_id = 1;
                
                foreach ($rtwwdpd_least_option as $catt => $pro_rul)
                { 
                    if ( is_page( 'cart' ) || is_cart() ) {
                        wc_clear_notices();
                        wc_add_notice( ( isset( $pro_rul['rtwwdpd_offer_msg'] ) ? $pro_rul['rtwwdpd_offer_msg'] : '' ), 'notice');
                    }

                    $rtwwdpd_total_quantity = 0;
                    $least_price_pro_id = '';
                    $least_price = 9999999;
                    $rtwwdpd_catids = '';
                    
                    foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
                    {
                        $rtwwdpd_catids = wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
                          
                        if( isset($pro_rul['rtwwdpd_discount_on']) && $pro_rul['rtwwdpd_discount_on'] == 2 )
                        {   
                            $arr = array_intersect( $pro_rul['category_id'], $rtwwdpd_catids );
                            if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && !empty($arr) )
                            {   
                                if( isset( $pro_rul['product_exe_id'] ) && !empty( $pro_rul['product_exe_id'] ) )
                                {

                                    if( !empty( $cart_item['data']->get_id() ) && in_array( $cart_item['data']->get_id() ,$pro_rul['product_exe_id']) )
                                    {
                                        continue 1;
                                    }
                                }

                                if(isset($pro_rul['rtwwdpd_exclude_sale']))
                                {
                                    if( $cart_item['data']->is_on_sale() )
                                    {
                                        continue;
                                    }
                                }
                                if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
                                {
                                    if(array_intersect( $pro_rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids()))
                                    {
                                        continue;
                                    }
                                }
                               
                                $rtwwdpd_total_quantity += $cart_item['quantity'];
                             
                            }
    
                            if( $cart_item['data']->get_price() < $least_price )
                            {   
                                $least_price = $cart_item['data']->get_price();
                                $least_price_pro_id = $cart_item['data']->get_id();
                            }
                        }
                        else
                        {   
                            if( isset( $pro_rul['product_exe_id'] ) && !empty( $pro_rul['product_exe_id'] ) )
                            {      
                                if( !empty( $cart_item['data']->get_id() ) && in_array( $cart_item['data']->get_id() ,$pro_rul['product_exe_id']) )
                                {
                                    continue 1;
                                }
                            }

                            if(isset($pro_rul['rtwwdpd_exclude_sale']))
                            {
                                if( $cart_item['data']->is_on_sale() )
                                {
                                    continue;
                                }
                            }

                            if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
                            {
                                if(array_intersect( $pro_rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids()))
                                {
                                    continue;
                                }
                            }

                            $rtwwdpd_total_quantity += $cart_item['quantity'];
    
                            if( $cart_item['data']->get_price() < $least_price )
                            {
                                $least_price = $cart_item['data']->get_price();
                                $least_price_pro_id = $cart_item['data']->get_id();
                            }
                        }
                    }                
                    if( isset( $pro_rul['product_exe_id'] ) && !empty( $pro_rul['product_exe_id'] ) )
                    {    
                        if( !empty( $least_price_pro_id ) && in_array( $least_price_pro_id ,$pro_rul['product_exe_id']) )
                        {   
                            continue 1;
                        }
                    }                                                                  
                    if( isset( $pro_rul['rtwwdpd_min_cat'] ) && $rtwwdpd_total_quantity < $pro_rul['rtwwdpd_min_cat'] )
                    {  
                        continue 1;
                    }
                    $rtwwdpd_matched = true;   // unwanted line.
                    if( $pro_rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_to_date'] < $rtwwdpd_today_date )
                    {
                        continue 1;
                    }
                    $rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles'] ;
                    $rtwwdpd_role_matched = false;
                    
                    if(isset($rtwwdpd_user_role) && is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                    {
                        foreach ($rtwwdpd_user_role as $rol => $role) 
                        {
                            if($role == 'all'){
                                $rtwwdpd_role_matched = true;
                            }
                            if ( in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                $rtwwdpd_role_matched = true;
                            }
                        }
                    }
                    if($rtwwdpd_role_matched == false)
                    {
                        continue 1;
                    }
                  
                    if(isset($pro_rul['rtwwdpd_min_orders']) && $pro_rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
                    {
                        continue 1;
                    }
                
                    if(isset($pro_rul['rtwwdpd_min_spend']) && $pro_rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
                    {
                        continue 1;
                    }
    
                    $sabcd = 'fication_done';
                    $rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
                    if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
                        if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
                        {
                            foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
                                
                                if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
                                {
                                    if( ( !empty( $least_price_pro_id )) && ( $cart_item['product_id'] == $least_price_pro_id || $cart_item['variation_id'] == $least_price_pro_id ) )
                                    {
                                        $rtw_matched = array_intersect( $pro_rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids());
                                        
                                    }
                                }
                                
                                if( $cart_item['product_id'] == $least_price_pro_id || $least_price_pro_id == $cart_item['data']->get_id() )
                                {
                                    if ( $least_price !== false  )
                                    {
                                        $original_price = $cart_item['data']->get_price();

                                        if($cart_item['quantity'] == 1)
                                        {
                                            $discounted_price = ($original_price - (( $original_price * $pro_rul['rtwwdpd_dscnt_cat_val'] ) / 100));
                                            $this->rtw_apply_cart_item_adjustment( $cart_item_key, $original_price, $discounted_price, 'least_amount_product', '' );
                                        }else{

                                            $discounted_price = (( $original_price * $pro_rul['rtwwdpd_dscnt_cat_val'] ) / 100);

                                            $discounted_price = ($original_price - ($discounted_price/$cart_item['quantity']));
                                            $this->rtw_apply_cart_item_adjustment( $cart_item_key, $original_price, $discounted_price, 'least_amount_product', '' );
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
            // ////////////////// lease amount product discount ////////////////////
    }

    /**
     * Function to calculate discount for bogo discount rule.
     *
     * @since    1.0.0
     */
    public function rtw_product_rule_bogo($cart_item_key, $rtwwdpd_original_price, $rtwwdpd_adjusted_price, $module, $set_id, $quant, $key )
    {
        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        if ( $rtwwdpd_adjusted_price === false ) 
        {
            return;
        }
        if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) 
        {
            $_product = WC()->cart->cart_contents[ $cart_item_key ]['data'];
            $rtwwdpd_display_price = wc_get_price_including_tax( $_product );
            WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $rtwwdpd_adjusted_price);
            if ( $_product->get_type() == 'composite' ) 
            {
                WC()->cart->cart_contents[ $cart_item_key ]['data']->base_price = $rtwwdpd_adjusted_price;
            }
            if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] ) ) {

                $rtwwdpd_discount_data                                           = array(
                    'by'                => array( $module ),
                    'set_id'            => $set_id,
                    'price_base'        => $rtwwdpd_original_price,
                    'display_price'     => $rtwwdpd_display_price,
                    'price_adjusted'    => $rtwwdpd_adjusted_price,
                    'applied_discounts' => array(
                        array(
                            'by'             => $module,
                            'set_id'         => $set_id,
                            'price_base'     => $rtwwdpd_original_price,
                            'price_adjusted' => $rtwwdpd_adjusted_price
                        )
                    )
                );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;
                
            } 
            else 
            {
                $rtwwdpd_existing = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
                $rtwwdpd_discount_data = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $set_id,
                    'price_base'     => $rtwwdpd_original_price,
                    'display_price'  => $rtwwdpd_existing['display_price'],
                    'price_adjusted' => $rtwwdpd_adjusted_price
                );
                WC()->cart->cart_contents[ $cart_item_key ]['discounts'] = $rtwwdpd_discount_data;

                $history = array(
                    'by'             => $rtwwdpd_existing['by'],
                    'set_id'         => $rtwwdpd_existing['set_id'],
                    'price_base'     => $rtwwdpd_existing['price_base'],
                    'price_adjusted' => $rtwwdpd_existing['price_adjusted']
                );
               
                array_push( WC()->cart->cart_contents[ $cart_item_key ]['discounts']['by'], $module );
                
                WC()->cart->cart_contents[ $cart_item_key ]['discounts']['applied_discounts'][] = $history;
            }
        }
    }

    /**
     * Function to give discount based on cart rule.
     *
     * @since    1.0.0
     */
    function rtwwdpd_sale_custom_price($cart_object) 
    {   
        // Calculate discount amount and return $discount
        global $woocommerce;
        WC()->session;
        if (!empty($woocommerce->cart->applied_coupons))
        {
            $active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
            if($active == 'no')
            {
                return;
            }
        }
        WC()->cart;
        $cart_object_main = $cart_object;
        $rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
        $rtwwdpd_cart_subtotl = $woocommerce->cart->get_subtotal();
        $cart_object = $cart_object->cart_contents;
        $rtwwdpd_total_weig = 0;
        $rtwwdpd_prod_count = 0;
        $rtwwdpd_temp_cart = $cart_object;
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = isset($chosen_methods[0]) && !empty($chosen_methods[0]);
    
        $rate_table = array();

        $shipping_methods = WC()->shipping->get_shipping_methods();
        foreach($shipping_methods as $shipping_method){
            foreach($shipping_method->rates as $key=>$val)
            {   
                $rate_table[$key] = $val->cost;
            }
        }  
        
        
        foreach ( WC()->cart->get_shipping_packages() as $package_id => $package ) {
            // Check if a shipping for the current package exist
            if ( WC()->session->__isset( 'shipping_for_package_'.$package_id ) ) {
                // Loop through shipping rates for the current package
                foreach ( WC()->session->get( 'shipping_for_package_'.$package_id )['rates'] as $shipping_rate_id => $shipping_rate ) {
                    // $rate_id     = $shipping_rate->get_id(); // same thing that $shipping_rate_id variable (combination of the shipping method and instance ID)
                    // $method_id   = $shipping_rate->get_method_id(); // The shipping method slug
                    // $instance_id = $shipping_rate->get_instance_id(); // The instance ID
                    // $label_name  = $shipping_rate->get_label(); // The label name of the method
                    // $cost        = $shipping_rate->get_cost(); // The cost without tax
                    // $tax_cost    = $shipping_rate->get_shipping_tax(); // The tax cost
                    // $taxes       = $shipping_rate->get_taxes(); // The taxes details (array)
                    $rate_table[$shipping_rate_id] = $shipping_rate->get_cost();
                    
                }
            }
        }
        $rtwwdpd_cart_prod_count = $woocommerce->cart->get_cart_contents_count();

        if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
        {
            foreach ( $rtwwdpd_temp_cart as $cart_item_key => $values ) {
                
                if( isset( $values['quantity'] ) &&  $values['quantity'] != '' )
                {
                    $rtwwdpd_prod_count += $values['quantity'];
                }
                if( $values['data']->get_weight() != '' )
                {
                    $rtwwdpd_total_weig += ( $values['data']->get_weight() * $values['quantity'] );
                }
            }
        }

        if( is_array($cart_object) && !empty($cart_object) )
        {
            $rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
            $rtwwdpd_option = get_option('rtwwdpd_shipping_discount_on', 'rtwwdpd_shipping');
            
            $ship_customize = get_option('rtwwdpd_apply_shipping_discount_on', 'message' );
            $rtwwdpd_today_date = current_time('Y-m-d');
            if((isset($rtwwdpd_setting_pri['ship_rule']) && $rtwwdpd_setting_pri['ship_rule'] == 1) && $ship_customize == 'message')
            {   
                $rtwwdpd_get_ship_opt = get_option('rtwwdpd_ship_method');
                if(isset($rtwwdpd_get_ship_opt) && is_array($rtwwdpd_get_ship_opt) && !empty($rtwwdpd_get_ship_opt))
                {  
                    foreach ($rtwwdpd_get_ship_opt as $keys => $rulval) {
                        // if($rtwwdpd_option == 'rtwwdpd_subtotal')
                        {   
                            $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                            $chosen_shipping = isset($chosen_methods[0]) ? $chosen_methods[0] : '';
                            
                            if( $rulval['rtwwdpd_ship_from_date'] > $rtwwdpd_today_date || $rulval['rtwwdpd_ship_to_date'] < $rtwwdpd_today_date )
                            {   
                                continue 1;
                            }
                            if(empty($rate_table))
                            {
                                continue;
                            }
                            $chosen_shipping_method_price = $rate_table[$chosen_shipping];
                            
                            if( stripos( $chosen_shipping, $rulval['allowed_shipping_methods'] ) === false )
                            {
                                continue 1;
                            }
                            
                            $shipping_packages =  WC()->cart->get_shipping_packages();
                            // // Get the WC_Shipping_Zones instance object for the first package
                            $shipping_zone = wc_get_shipping_zone( reset( $shipping_packages ) );
                            $zone_id   = $shipping_zone->get_id(); // Get the zone ID
                            // $zone_name = $shipping_zone->get_zone_name(); // To get the zone name
                           
                            if(isset($rulval['allowed_shipping_zones']) && !empty($rulval['allowed_shipping_zones']) && is_array($rulval['allowed_shipping_zones']))
                            {
                                if(!in_array('all', $rulval['allowed_shipping_zones']))
                                {
                                    if(!in_array($zone_id, $rulval['allowed_shipping_zones']))
                                    {
                                        continue;
                                    }
                                }
                            }
                           
                            if(isset($rulval['rtwwdpd_min_prod_cont']) && !empty($rulval['rtwwdpd_min_prod_cont']) )
                            {
                                if($rulval['rtwwdpd_min_prod_cont'] > $rtwwdpd_cart_prod_count )
                                {
                                    continue 1;
                                }
                            }
                            
                            if(isset($rulval['rtwwdpd_min_spend']) && !empty($rulval['rtwwdpd_min_spend']) )
                            {
                                if($rulval['rtwwdpd_min_spend'] > $rtwwdpd_cart_subtotl )
                                {
                                    continue 1;
                                }
                            }
                            $rtwwdpd_chkout = get_option('rtwwdpd_show_ship_on_chkout', 'both');
                            if($rtwwdpd_chkout == 'both')
                            {
                                if($rulval['rtwwdpd_ship_discount_type'] == 'rtwwdpd_discount_percentage')
                                {
                                    $rtwwdpd_dscnt_val = $rulval['rtwwdpd_ship_discount_value'];
                                    
                                    $rtwwdpd_new_cost = ($rtwwdpd_cart_subtotl * ($rtwwdpd_dscnt_val/100));

                                    if($rulval['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                    {
                                        $rtwwdpd_new_cost = $rulval['rtwwdpd_ship_max_discount'];
                                    }
                                    
                                    if($chosen_shipping_method_price < $rtwwdpd_new_cost)
                                    {
                                        $rtwwdpd_new_cost = $chosen_shipping_method_price;
                                    }

                                    $cart_object_main->add_fee( __('Shipping Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_new_cost, true, '');

                                }
                                else
                                {       
                                    
                                    $rtwwdpd_dscnt_val = $rulval['rtwwdpd_ship_discount_value'];
                                   
                                    if($rtwwdpd_cart_subtotl != 0 && $rtwwdpd_cart_subtotl >= $rtwwdpd_dscnt_val)
                                    {   
                                        $rtwwdpd_new_cost = $rtwwdpd_dscnt_val;

                                        if($rulval['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                        {
                                            $rtwwdpd_new_cost = $rulval['rtwwdpd_ship_max_discount'];
                                        }
                                        
                                        if($chosen_shipping_method_price < $rtwwdpd_new_cost)
                                        {
                                            $rtwwdpd_new_cost = $chosen_shipping_method_price;
                                        }

                                        $cart_object_main->add_fee( __('Shipping Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_new_cost, true, '');
                                    }
                                }
                            }
                            elseif($rtwwdpd_chkout == 'checkout')
                            {
                                if(is_checkout())
                                {
                                    if($rulval['rtwwdpd_ship_discount_type'] == 'rtwwdpd_discount_percentage')
                                    {
                                        $rtwwdpd_dscnt_val = $rulval['rtwwdpd_ship_discount_value'];
                                        
                                        $rtwwdpd_new_cost = ($rtwwdpd_cart_subtotl * ($rtwwdpd_dscnt_val/100));

                                        if($rulval['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                        {
                                            $rtwwdpd_new_cost = $rulval['rtwwdpd_ship_max_discount'];
                                        }

                                        
                                        if($chosen_shipping_method_price < $rtwwdpd_new_cost)
                                        {
                                            $rtwwdpd_new_cost = $chosen_shipping_method_price;
                                        }

                                        $cart_object_main->add_fee( __('Shipping Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_new_cost, true, '');

                                    }
                                    else{
                                        $rtwwdpd_dscnt_val = $rulval['rtwwdpd_ship_discount_value'];
                                        
                                        if($rtwwdpd_cart_subtotl != 0 && $rtwwdpd_cart_subtotl >= $rtwwdpd_dscnt_val)
                                        {
                                            $rtwwdpd_new_cost = $rtwwdpd_dscnt_val;

                                            if($rulval['rtwwdpd_ship_max_discount'] < $rtwwdpd_new_cost)
                                            {
                                                $rtwwdpd_new_cost = $rulval['rtwwdpd_ship_max_discount'];
                                            }
                                            
                                            if($chosen_shipping_method_price < $rtwwdpd_new_cost)
                                            {
                                                $rtwwdpd_new_cost = $chosen_shipping_method_price;
                                            }

                                            $cart_object_main->add_fee( __('Shipping Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_new_cost, true, '');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if( isset( $rtwwdpd_setting_pri['cart_rule'] ) && $rtwwdpd_setting_pri['cart_rule'] == 1 )
            {   
                $rtwwdpd_today_date = current_time('Y-m-d');	
                $rtwwdpd_user = wp_get_current_user();
                $rtwwdpd_car_rul = get_option('rtwwdpd_cart_rule');

                $rtwwdpd_dis_array_fixed = array();
                $cart_subtotal = $woocommerce->cart->get_subtotal();
                $rtw_cart_count = count(WC()->cart->get_cart());
                
                $rtwwdpd_dis_val = 0;
                if( is_array( $rtwwdpd_car_rul ) && !empty( $rtwwdpd_car_rul ) )
                {	

                    foreach ( $rtwwdpd_car_rul as $car => $rul ) 
                    {
                        if( isset( $rul['allowed_shipping_method'][0] ) && $rul['allowed_shipping_method'][0] != '0' )
                        {
                            $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                            $chosen_shipping = $chosen_methods[0];

                            if( stripos( $chosen_shipping, $rul['allowed_shipping_method'][0] ) === false )
                            {
                                continue 1;
                            }
                        }
                       
                        if( isset( $rul['allowed_payment_method'][0] ) && $rul['allowed_payment_method'][0] != '0' )
                        {
                            $chosen_payment_method = WC()->session->get('chosen_payment_method');
                            if( $chosen_payment_method == $rul['allowed_payment_method'][0] )
                            {
                                continue 1;
                            }
                        }
                        
                        if($rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                        {
                            continue 1;
                        }
                        $rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;

                        $rtwwdpd_role_matched = false;
                        if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                        {
                            foreach ($rtwwdpd_user_role as $rol => $role) {
                                if($role == 'all'){
                                    $rtwwdpd_role_matched = true;
                                }
                                if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                    $rtwwdpd_role_matched = true;
                                }
                            }
                        }
                        if( $rtwwdpd_role_matched == false )
                        {
                            continue 1;
                        }
                        
                        $rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_emails'] ) ? $rul['rtwwdpd_select_emails'] : array();

                        $rtwwdpd_cur_user_mail = get_current_user_id();
                        
                        if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
                        {
                            continue 1;
                        }

                        if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
                        {
                            foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
                            {
                                if( $rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_best_discount' )
                                {
                                    
                                    if ( isset( $cart_item['discounts'] ) && !empty($cart_item['discounts'])) {
                                     
                                        continue 2;
                                    }
                                }

                                if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
                                {
                                    if($rtwwdpd_prod_count < $rul['rtwwdpd_min'])
                                    {
                                        continue 2;
                                    }
                                    if(isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '')
                                    {
                                        if($rul['rtwwdpd_max'] < $rtwwdpd_prod_count)
                                        {
                                            continue 2;
                                        }
                                    }
                                }
                                elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
                                {
                                    if($rtwwdpd_cart_total < $rul['rtwwdpd_min'])
                                    {
                                        continue 2;
                                    }
                                    if(isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '')
                                    {
                                        if($rul['rtwwdpd_max'] < $rtwwdpd_cart_total)
                                        {
                                            continue 2;
                                        }
                                    }
                                }
                                elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_subtotal')
                                {
                                    if($cart_subtotal < $rul['rtwwdpd_min'])
                                    {
                                        continue 2;
                                    }
                                    if(isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '')
                                    {
                                        if($rul['rtwwdpd_max'] < $cart_subtotal)
                                        {
                                            continue 2;
                                        }
                                    }
                                }
                                elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_cart_count')
                                {
                                    if($rtw_cart_count < $rul['rtwwdpd_min'])
                                    {
                                        continue 2;
                                    }
                                    if(isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '')
                                    {
                                        if($rul['rtwwdpd_max'] < $rtw_cart_count)
                                        {
                                            continue 2;
                                        }
                                    }
                                }
                                else{
                                    $rtwwdpd_total_weigs = 0;
                                    if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
                                    {
                                        
                                        foreach ( $rtwwdpd_temp_cart as $cart_item_key => $values ) {
                                            
                                            if( $values['data']->get_weight() != '' )
                                            {
                                                $rtwwdpd_total_weigs += ( $values['data']->get_weight() * $values['quantity'] );
                                            }
                                        }
                                    }
                                
                                    if(isset($rul['rtwwdpd_min']) && $rul['rtwwdpd_min'] != '')
                                    {
                                        if($rtwwdpd_total_weigs < $rul['rtwwdpd_min'])
                                        {
                                            continue 2;
                                        }
                                    }
                                    if(isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '')
                                    {
                                        if($rul['rtwwdpd_max'] < $rtwwdpd_total_weigs)
                                        {
                                            continue 2;
                                        }
                                    }
                                    
                                }
                            }
                        }
                       
                        //----------------------------------------------------------------- Updated By Anoop Saxena ----------------------------------------------------------------------//
                        if(isset($rul['rtwwdpd_select_product']) && is_array($rul['rtwwdpd_select_product']) && !empty($rul['rtwwdpd_select_product']))
                        {
                            $selected_cart_pro = $rul['rtwwdpd_select_product'];
                            $cart_pro = array();

                            if(sizeof(WC()->cart->get_cart())>0)
                            {
                                foreach(WC()->cart->get_cart() as $cart_item_key => $value)
                                {
                                    $_product = $value['data'];
                                    $cart_pro[] = $_product->get_id();
                                    
                                   
                                }
                            }
                            if(!empty(array_diff($selected_cart_pro,$cart_pro)))
                            {
                                continue;
                            }
                        }
                        //--------------------------------------------------------------------------------------------------------------------------------------//

                        $rtwwdpd_amount = $rul['rtwwdpd_discount_value'];
                        
                        if( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price' )
                        {
                            if( $rtwwdpd_amount > $rul['rtwwdpd_max_discount'] )
                            {
                                $rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
                            }
                            $cart_object_main->add_fee( __('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_amount, true, '');
                            
                        }
                        elseif( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
                        {
                            
                            $discount_value = ( ( $rtwwdpd_amount / 100 ) * $rtwwdpd_cart_total );
                            if( $discount_value > $rul['rtwwdpd_max_discount'] )
                            {
                                $discount_value = $rul['rtwwdpd_max_discount'];
                            }

                            $cart_object_main->add_fee( __('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$discount_value, true, '');
                        }
                        elseif( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount' )
                        { 
                            $discount_value = ( $rtwwdpd_amount * $rtwwdpd_prod_count );
                            if( $discount_value > $rul['rtwwdpd_max_discount'] )
                            {
                                $discount_value = $rul['rtwwdpd_max_discount'];
                            }
                            $cart_object_main->add_fee( __('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$discount_value, true, '');
                        }
                    }
                }
            }

            if( isset( $rtwwdpd_setting_pri['pro_com_rule'] ) && $rtwwdpd_setting_pri['pro_com_rule'] == 1 )
            {
                $rtwwdpd_get_pro_option = get_option('rtwwdpd_combi_prod_rule');
                $i = 0;
                $rtwwdpd_user = wp_get_current_user();
                $rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
                $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
                $rtwwdpd_today_date = current_time('Y-m-d');
                $rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());                
                $set_id = 1;
                if ( is_array($rtwwdpd_get_pro_option) && !empty($rtwwdpd_get_pro_option) ) {

                    foreach ( $rtwwdpd_get_pro_option as $prod => $pro_rul ) {

                        if($pro_rul['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date)
                        {
                            continue 1;
                        }
                        $rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles_com'] ;
                        $rtwwdpd_role_matched = false;
                        if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role) )
                        {
                            foreach ($rtwwdpd_user_role as $rol => $role) {
                                if($role == 'all'){
                                    $rtwwdpd_role_matched = true;
                                }
                                if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                    $rtwwdpd_role_matched = true;
                                }
                            }
                        }

                        if($rtwwdpd_role_matched == false)
                        {
                            continue 1;
                        }
                        if(isset($pro_rul['rtwwdpd_combi_min_orders']) && $pro_rul['rtwwdpd_combi_min_orders'] > $rtwwdpd_no_oforders)
                        {
                            continue 1;
                        }
                        if(isset($pro_rul['rtwwdpd_combi_min_spend']) && $pro_rul['rtwwdpd_combi_min_spend'] > $rtwwdpd_ordrtotal)
                        {
                            continue 1;
                        }

                        $both_quantity = 0;
                        $both_ids 	=	array();

                        $ids_quantity_in_cart = array();
                        $ids_quantity_in_rule = array();
                        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
                            foreach ( $rtwwdpd_temp_cart as $cart_item_k => $valid ) {
                                foreach($pro_rul['product_id'] as $na => $kid )
                                { 
                                    if($kid == $valid['data']->get_parent_id() || $kid == $valid['data']->get_id())
                                    {
                                        if( $valid['data']->get_parent_id() != 0 )
                                        {
                                            $both_ids[] = $valid['data']->get_parent_id();
                                        }
                                        else{
                                            $both_ids[] = $valid['data']->get_id();
                                        }
                                        
                                        $both_quantity += $valid['quantity'];
                                        $ids_quantity_in_cart[$kid] = $valid['quantity'];
                                    }
                                }
                            }
                        }

                        $givn_quanty = 0;
                        foreach ($pro_rul['combi_quant'] as $quants) {
                            $givn_quanty += $quants;
                        }

                        $rslt = array();
    
                        $rslt = array_diff($pro_rul['product_id'], $both_ids );
                        if( !empty($rslt) )
                        {
                            continue 1;
                        }

                        if( $givn_quanty > $both_quantity )
                        {
                            continue 1;
                        }

                        ///////////////////////////////////////
                        foreach($pro_rul['product_id'] as $na => $kid )
                        {
                            $ids_quantity_in_rule[$kid] = $pro_rul['combi_quant'][$na];
                        } 
                        foreach($ids_quantity_in_rule as $na => $kid )
                        {
                            if($ids_quantity_in_cart[$na] < $kid )
                            {
                                continue 2;
                            }
                        } 

                        ///////////////////////////////////////

                        $sabcd = 'fication_done';
                        $rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
                        if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
                            foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

                                $rtwwdpd_original_price = $cart_item['data']->get_price();

                                if ( $rtwwdpd_original_price ) {
                                    $rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_combi_discount_value'], $pro_rul, $cart_item, $this );

                        
                                    if($pro_rul['rtwwdpd_combi_discount_type'] == 'rtwwdpd_fixed_price')
                                    {
                                        if($rtwwdpd_amount > $pro_rul['rtwwdpd_combi_max_discount'])
                                        {
                                            $rtwwdpd_amount = $pro_rul['rtwwdpd_combi_max_discount'];
                                        }

                                        if(isset($pro_rul['product_id']) && is_array($pro_rul['product_id']))
                                        {
                                            foreach ($pro_rul['product_id'] as $k => $v) {
                                                if($v == $cart_item['data']->get_id() || $v == $cart_item['data']->get_parent_id())
                                                {
                                                    if(isset($pro_rul['rtwwdpd_combi_exclude_sale']))
                                                    {
                                                        if( !$cart_item['data']->is_on_sale() )
                                                        {
                                                            if ( $rtwwdpd_amount !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_amount ) ) {
                                                                
                                                                $cart_object_main->add_fee( __('Discount ', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_amount, true, '');
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        if ( $rtwwdpd_amount !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_amount ) ) 
                                                        {
                                                            $cart_object_main->add_fee( __('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai'), -$rtwwdpd_amount, true, 'Zero rate');
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }
    }

    /**
     * Function to check if a product is already discounted by the same rule.
     *
     * @since    1.0.0
     */
    protected function rtwwdpd_is_cumulative_cart( $cart_item, $cart_item_key, $default = false ) {
        global $woocommerce;
        $rtwwdpd_cumulative = null;
        if ( isset( WC()->cart->cart_contents[$cart_item_key]['discounts'] ) ) {
            if ( in_array( 'advance_cart', WC()->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) {
                
                return false;
            } elseif ( count( array_intersect( array('simple_category', 'simple_membership', 'simple_group'), WC()->cart->cart_contents[$cart_item_key]['discounts']['by'] ) ) > 0 ) {
                $rtwwdpd_cumulative = true;
            }
        } else {
            $rtwwdpd_cumulative = $default;
        }

        return apply_filters( 'rtwwdpd_dynamic_pricing_is_cumulative', $rtwwdpd_cumulative, 'advance_cart', $cart_item, $cart_item_key );
    }
    
    /**
     * Function to check if a product is discounted.
     *
     * @since    1.0.0
     */
    protected function rtwwdpd_is_item_discounted_cart( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) {
        global $woocommerce;
        $discounted_array = isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by']) ? WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by'] : array() ;
        
        if( in_array( 'advance_cart', $discounted_array ) )
        {
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Function to change title of browser.
     *
     * @since    1.0.0
     */
    function rtwwdpd_change_titles( $title ){
        
        $rtwwdpd_get_setting = get_option( 'rtwwdpd_setting_priority' );
        
        if( is_array( $rtwwdpd_get_setting ) && !empty( $rtwwdpd_get_setting ) )
        {
            if( isset( $rtwwdpd_get_setting['rtw_show_combck'] ) && $rtwwdpd_get_setting['rtw_show_combck'] == 'rtw_price_yes' && isset( $rtwwdpd_get_setting['rtw_show_pages'] ) && is_array( $rtwwdpd_get_setting['rtw_show_pages'] ) && !empty( $rtwwdpd_get_setting['rtw_show_pages'] ) ) 
            {
                if( in_array( get_the_ID(), $rtwwdpd_get_setting['rtw_show_pages'] ) )
                {
                    $title = $rtwwdpd_get_setting['rtwwdpd_text_combck'];
                }
            }
        }
        return $title;
    }


    /*
    * function to check variation of the product have discounts
    */
    function rtwwdpd_variation_id_callback(){
        if (!wp_verify_nonce($_POST['security_check'], 'rtwwdpd-ajax-seurity')){
            return;
        }
        global $woocommerce;
        $rtwwdpd_var_id = sanitize_text_field($_POST['rtwwdpd_var_id']);
        $rtwwdpd_prod_id = sanitize_text_field($_POST['rtwwdpd_prod_id']);
        $rtwwdpd_offers = get_option('rtwwdpd_setting_priority');
        
        if( isset( $rtwwdpd_offers['tier_rule'] ) )
        {
            $rtwwdpd_match = false;
            $rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
            if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
            {
                $temp_cart = $woocommerce->cart->cart_contents;
                $prods_quant = 0;
                $rtwwdpd_total_weig = 0;
                $rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
                foreach ( $temp_cart as $cart_item_key => $cart_item ) {
                    $prods_quant += $cart_item['quantity'];
                    if( $cart_item['data']->get_weight() != '' )
                    {
                        $rtwwdpd_total_weig += $cart_item['data']->get_weight();
                    }
                }
                foreach ( $rtwwdpd_rule_name as $name ) {
                    if( $name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] ){
                        if( isset($name['products'] ) && is_array( $name['products'] ) && !empty( $name['products'] ) )
                        {
                            foreach ( $name['products'] as $keys => $vals )
                            {

                                if( $vals == $rtwwdpd_var_id && $rtwwdpd_match == false )
                                {
                                    if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
                                    {
                                        if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
                                                
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) 
                                            {
                                                $table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
                                                
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        break 2;
                                    }
                                    else
                                    {
                                        if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.wc_price($name['discount_val'][$k]).'</td>';
                                                
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.wc_price($name['discount_val'][$k]).'</td>';
                                                
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
                                        {
                                            $table_html = '<table id="tier_offer_table">
                                                <tr><th>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
                                            }
                                            $table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
                                            foreach ($name['quant_min'] as $k => $va) {
                                                
                                                $table_html .= '<td>'.wc_price($name['discount_val'][$k]).'</td>';
                                                
                                            }
                                            $table_html .= '</tr></table>';
                                            echo $table_html;
                                        }
                                        $rtwwdpd_match = true;
                                        break 2;
                                    }
                                }
                                else{
                                    echo json_encode('');
                                    die;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // compatible with woocommerce bundle product
    function rtwwdpd_bundled_product_detials( $bundled_items, $bundle_obj )
    {
        $bundle_item_id = array();
        foreach ($bundled_items as $key => $value) {
            //$this->bundle_product_id[] = $value->product->get_id();
            array_push($this->bundle_product_id,$value->product->get_id());
        }
        return $bundled_items;
    }

    /**
     * Function to show total discount in cart and checkout page.
     *
     * @since    1.3.0
     */
    function rtwwdpd_cart_totals_before_order_total( $rtwwdpd_price )
    {
        global $woocommerce;
        $discount_total = 0;
        $vip_val = get_option('rtwwdpd_plus_member_discount_value', array());  
        $total_vip_discount = 0;
        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values) 
        {
            if(array_key_exists($cart_item_key, $vip_val))
            {
                $total_vip_discount += $vip_val[$cart_item_key]['quantity'] * $vip_val[$cart_item_key]['discount'];
            }
            $original_product = wc_get_product($values['data']->get_id());  

            /// updated code 2.5.0  compatible with woocommerce product bandal

            
            if( in_array('woocommerce-product-bundles/woocommerce-product-bundles.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
            {
                
                $product = $original_product->get_data();
                if( $original_product->is_type('bundle') )
                {
                    $bundle_data = $original_product->get_data();
                    if( isset( $bundle_data['group_mode'] ) && $bundle_data['group_mode'] == 'parent' )
                    {
                        $sale_price = (float)($values['data']->get_price());
                        if ( $original_product->is_on_sale() ) 
                        {
                            $regular_price = $original_product->get_sale_price();
                        }
                        else
                        {
                            $regular_price = $original_product->get_regular_price();
                        }
                        $discount = ( (float)$regular_price - (float)$sale_price ) * (int)$values['quantity'];
                        $discount_total += $discount;
                    }
                }
                else
                {
                    
                    if( !in_array($values['data']->get_id(),$this->bundle_product_id) )
                    {
                        $_product = $values['data'];
                        if ( is_object($original_product) ) 
                        {
                            if( !empty($original_product->get_regular_price()) )
                            {
                                $regular_price = $original_product->get_regular_price();
                            }
                            else
                            {
                                $regular_price = $original_product->get_price();
                            }
                            $sale_price = (float)($values['data']->get_price());
                            $regular_discount = (float)(($regular_price - $sale_price) * $values['quantity']);
                        
                            $discount_total += $regular_discount;
                        }
                    }
                }
            }
            else
            {
                
                $_product = $values['data'];
                if ( is_object($original_product) ) 
                {
                    if( !empty($original_product->get_regular_price()) )
                    {
                        $regular_price = $original_product->get_regular_price();
                    }
                    else
                    {
                        $regular_price = $original_product->get_price();
                    }
                    $sale_price = (float)($values['data']->get_price());
                    
                    $discount = (float)(($regular_price - $sale_price) * $values['quantity']);
                    // $discount_total += $sale_price * $values['quantity'];
                    $discount_total += $discount;
                }
            }

        }
        $fee_applied = $woocommerce->cart->get_fee_total();
        
        $rtwwdpd_text = get_option( 'rtwwdpd_plus_member_text', 'VIP Customer Discount' );

        if ( $discount_total > 0 ) {
            $activated = false;
            $activated = apply_filters('rtwwdpd_plus_member_customize',  $activated);
            if($activated)
            {
                if( $total_vip_discount != 0 || $total_vip_discount != ( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ))
                {
                    if( $total_vip_discount > ( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) )
                    {
                        echo '<tr class="cart-discount">
                        <th>'. esc_html__( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</th>
                        <td data-title=" '. esc_attr( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .' ">'
                        . wc_price( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) .'</td>
                        </tr>';
                    }
                    else
                    {
                       
                        $you_saved = ( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) - $total_vip_discount ;
                        echo '<tr class="cart-discount">
                        <th>'. esc_html__( 'You Saved', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</th>
                        <td data-title=" '. esc_attr( 'You Saved', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .' ">'
                        . wc_price( $you_saved ) .'</td>
                        </tr>';

                        echo '<tr class="cart-discount">
                        <th>'. esc_html__( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</th>
                        <td data-title=" '. esc_attr( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .' ">'
                        . wc_price( $total_vip_discount ) .'</td>
                        </tr>';
                    }
                }
                elseif( $total_vip_discount == ( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) )
                {
                    
                    echo '<tr class="cart-discount">
                    <th>'. esc_html__( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</th>
                    <td data-title=" '. esc_attr( $rtwwdpd_text, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .' ">'
                    . wc_price( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) .'</td>
                    </tr>';
                }
            }else{

                echo '<tr class="cart-discount">
                        <th>'. esc_html__( 'You Saved', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .'</th>
                        <td data-title=" '. esc_attr( 'You Saved', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai' ) .' ">'
                        . wc_price( $discount_total + $woocommerce->cart->discount_cart - $fee_applied ) .'</td>
                        </tr>';
            }
        }
    }

    /**
     * Function to update order meta.
     *
     * @since    1.3.0
     */
    function rtwwdpd_woocommerce_checkout_create_order( $order, $data )
    {
        global $woocommerce;
      
        $discount_total = 0;
          
        foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values) {
            $original_product = wc_get_product($values['product_id']);  
            
               $_product = $values['data'];
            
            if ( is_object($original_product) ) {
            $regular_price = $original_product->get_price();
            $sale_price = $values['data']->get_price();
            $discount = ($regular_price - $sale_price) * $values['quantity'];
            $discount_total += $discount;
            }
      
        }
        $order->update_meta_data( 'total_discount', $discount_total );
    }

    /**
     * Function to get total discount on order.
     *
     * @since    1.3.0
     */
    function rtwwdpd_order_get_total_discount( $order, $data )
    {
        $total_discount = 0;
        $meta_data_order = $data->get_meta_data();
        foreach( $meta_data_order as $meta => $dis )
        {
            $key = $dis->get_data()['key'];
            if( $key == 'total_discount' )
            {
                $total_discount = $dis->get_data()['value'];
            }
        }

        if( !empty( $total_discount ) )
        {
            return $total_discount;
        }
        else{
            return 0;
        }
        
    }


    function rtwwdpd_offers_message()
    {
        if( !is_user_logged_in() )
        {
            $message_settings = get_option('rtwwdpd_message_settings', array());
            if( isset( $message_settings['rtwwdpd_message_text'] ) && !empty( $message_settings['rtwwdpd_message_text'] ) )
            {
                echo stripcslashes($message_settings['rtwwdpd_message_text']);
            }
        }
    }


    function clear_notices_on_cart_update(){
        wc_clear_notices();
    }

    function woocommerce_payment_complete_order_status($status, $abc)
    {
        
    }
    

    function rtwwdpd_after_cart_item_quantity_update( $rtwwdpd_cart_item_key, $rtwwdpd_new_quantity, $rtwwdpd_prev_quantity ) {
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        $this->rtwwdpd_cart_loaded_from_session($cart);
    }

    function rtwwdpd_change_product_html( $price_html, $product ) {
        $rtwwdpd_gnrl_set = get_option('rtwwdpd_setting_priority'); 
        
        global $woocommerce;
        if( empty($woocommerce->cart) )
        {
            delete_option('rtwwdpd_plus_member_discount_value');
        }
        if( !isset($_GET['post_type']) && isset($rtwwdpd_gnrl_set['rtwwdpd_discounted_price']) && $rtwwdpd_gnrl_set['rtwwdpd_discounted_price'] == 1 )
        {
            global $post;
            global $woocommerce;
            $rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id() );
            // $rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
            
            $rtwwdpd_offers = get_option('rtwwdpd_setting_priority');
            $rtwwdpd_terms = get_the_terms( $product->get_id(), 'product_cat' );
            $rtwwdpd_product_cat_id = array();
            $rtwwdpd_user = wp_get_current_user();

            // $rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

            // $original_price = wc_get_price_including_tax($product); 
            $original_price = $product->get_regular_price();
            if($product->is_type('variable'))
            {
                $original_price     =  $product->get_variation_sale_price( 'min', true );
                // $regular_price  =  $product->get_variation_regular_price( 'max', true );
            }
            //$product->get_price_including_tax();
            if( !isset( $original_price ) || empty($original_price) )
            {
                $original_price = $product->get_regular_price();
                if( !isset( $original_price ) || empty($original_price) )
                {
                    $original_price = $product->get_price();
                }
            }
            
            if( is_array($rtwwdpd_terms) && !empty($rtwwdpd_terms) )
            {
                foreach ($rtwwdpd_terms  as $term  ) {
                    $rtwwdpd_product_cat_id[] = $term->term_id;
                }
            }
        
            $rtwwdpd_priority = array();
            if(is_array($rtwwdpd_offers) && !empty($rtwwdpd_offers))
            { 
                $rtwwdpd_i = 1;
                foreach ($rtwwdpd_offers as $key => $value) {
                    if($key == 'pro_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'bogo_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'tier_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'pro_com_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'cat_com_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'tier_cat_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }	
                    elseif($key == 'var_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'cat_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'bogo_cat_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'bogo_tag_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'attr_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif($key == 'prod_tag_rule_row')
                    {
                        $rtwwdpd_priority[$rtwwdpd_i] = $key;
                        $rtwwdpd_i++;
                    }
                    elseif ($key == 'rtw_offer_select') {
                        $rtwwdpd_select_offer = $value;
                    }
                    elseif ($key == 'rtwwdpd_rule_per_page') {
                        $rtwwdpd_rule_per_page = $value;
                    }
                }
            }
            $rtwwdpd_today_date = current_time('Y-m-d');
        
            $rtwwdpd_rule_name = array();
            $rtwwdpd_catids = array();
            if(is_array($rtwwdpd_priority) && !empty($rtwwdpd_priority))
            {
                foreach ($rtwwdpd_priority as $rule => $rule_name) 
                {
                    if($rtwwdpd_select_offer != 'rtw_best_discount')
                    {
                        if($rule_name == 'cat_rule_row')
                        {
                            if(isset($rtwwdpd_offers['cat_rule']))
                            {
                                $rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');
                                if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
                                {
                                    $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
                                    foreach ($rtwwdpd_rule_name as $name) {
                                        
                                        if($active_dayss == 'yes')
                                        {
                                            $active_days = isset($name['rtwwwdpd_cat_day']) ? $name['rtwwwdpd_cat_day'] : array();
                                            $current_day = date('N');

                                            if(!in_array($current_day, $active_days))
                                            {
                                                continue;
                                            }
                                        }
                                        if(isset($name['rtwwdpd_category_on_update']) && $name['rtwwdpd_category_on_update']== 'rtwwdpd_category_update')
							            {
							
                                            $rtwwdpd_total_weight = 0;
                                            $rtwwdpd_total_price = 0;
                                            $rtwwdpd_total_quantity = 0;
                                            $rtwdpd_pdt_price = '';
                                            foreach ( $woocommerce->cart as $cart_item_key => $cart_it ) 
                                            {  
                                                foreach($cart_it as $cart_item_k  => $cart_item)
                                                {  
                                                    if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                                                    {   
                                                        if(isset($cart_item['data']) && !empty($cart_item['data']))
                                                        {
                                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if(isset($cart_item['product_id']) && !empty($cart_item['product_id']))
                                                        {
                                                            $rtwwdpd_catids = wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );	
                                                        }
                                                       
                                                    }
                                                    if(isset($name['category_id']) &&  is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($name['category_id'], $rtwwdpd_catids))
                                                    {   
                                                        if(isset($cart_item['discounts']['price_adjusted']) && !empty($cart_item['discounts']['price_adjusted']))
                                                        {
                                                            $rtwdpd_pdt_price = isset($cart_item['discounts'])? $cart_item['discounts']['price_adjusted'] : '';
                                                        }
                                                        else
                                                        {
                                                            $rtwdpd_pdt_price =  isset($cart_item['discounts']) ?  $cart_item['discounts']['display_price'] : '';
                                                        }
                                                        if(isset($cart_item['quantity']))
                                                        {
                                                        //  $rtwwdpd_total_price += $cart_item['quantity'] * $cart_item['data']->get_price();
                                                        $rtwwdpd_total_price += (int)$cart_item['quantity'] * (int)$rtwdpd_pdt_price;
                                                        }
                                                        if(isset($cart_item['quantity']))
                                                        {
                                                            $rtwwdpd_total_quantity += $cart_item['quantity'];
                                                        }
                                                    }
                                                }
                                                
                                            }
                                            
                                           

                                            if($name['rtwwdpd_from_date'] > $rtwwdpd_today_date || $name['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                                            {
                                                continue;
                                            }

                                            if(isset($name['category_id']))
                                            {
                                                $rtwwdpd_cat = $name['category_id'];
                                                
                                                if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
                                                {
                                                    $rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $product->get_tag_ids());

                                                    if( !empty( $rtw_matched ) )
                                                    {
                                                        continue 1;
                                                    }
                                                }

                                                if(isset($name['rtwwdpd_exclude_sale']))
                                                {
                                                    if( $product->is_on_sale() )
                                                    {
                                                        continue;
                                                    }
                                                }

                                                if($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
                                                {
                                                    $rtwwdpd_on_single_or_whole = 'whole';
                                                    $rtwwdpd_on_single_or_whole = apply_filters('rtwwdpd_on_single_or_whole', $rtwwdpd_on_single_or_whole);

                                                    if($rtwwdpd_on_single_or_whole == 'whole')
                                                    {
                                                        if($rtwwdpd_total_quantity < $name['rtwwdpd_min_cat'])
                                                        {
                                                            continue 1;
                                                        }
                                                    }
                                                    elseif($rtwwdpd_on_single_or_whole == 'single')
                                                    {
                                                        if($cart_item['quantity'] < $name['rtwwdpd_min_cat'])
                                                        {
                                                            continue 1;
                                                        }
                                                    }
                                                    if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                    {
                                                        if($rtwwdpd_on_single_or_whole == 'whole')
                                                        {
                                                            if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_quantity)
                                                            {
                                                                continue 1;
                                                            }
                                                        }elseif($rtwwdpd_on_single_or_whole == 'single')
                                                        {	
                                                            if($name['rtwwdpd_max_cat'] < $cart_item['quantity'])
                                                            {
                                                                continue 1;
                                                            }
                                                        }
                                                    }
                                                }
                                                elseif($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
                                                {
                                                   
                                                    if($rtwwdpd_total_price < $name['rtwwdpd_min_cat'])
                                                    {
                                                        continue 1;
                                                    }
                                                    if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                    {
                                                        if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_price)
                                                        {
                                                            continue 1;
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    if($rtwwdpd_total_weight < $name['rtwwdpd_min_cat'])
                                                    {
                                                        continue 1;
                                                    }
                                                    if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                    {
                                                        if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_weight)
                                                        {
                                                            continue 1;
                                                        }
                                                    }
                                                }
                                                if( in_array( $rtwwdpd_cat, $rtwwdpd_product_cat_id ))
                                                {
                                                    if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
                                                    {
                                                        if( isset($original_price) && !empty($original_price))
                                                        {
                                                            $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_dscnt_cat_val"]/100));
            
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if( isset($original_price) && !empty($original_price))
                                                        {
                                                            $discounted_price = ( $original_price - $name["rtwwdpd_dscnt_cat_val"] );
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if(isset($name['rtwwdpd_category_on_update']) && $name['rtwwdpd_category_on_update']== 'rtwwdpd_multiple_cat_update')
								            {
                                                if(isset($name['multiple_cat_ids']))
                                                {
                                                    $rtwwdpd_mul_comn_id = array_intersect($rtwwdpd_product_cat_id,$name['multiple_cat_ids']);
                                                    
                                                    if($rtwwdpd_mul_comn_id )
                                                    {
                                                        $rtwwdpd_total_weight = 0;
                                                        $rtwwdpd_total_price = 0;
                                                        $rtwwdpd_total_quantity = 0;
                                                        foreach ( $woocommerce->cart as $cart_item_key => $cart_it ) 
                                                        {
                                                            foreach($cart_it as $cart_item_k  => $cart_item)
                                                            {
                                                                if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                                                                {
                                                                    $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                                                                }
                                                                else
                                                                {
                                                                    if(isset($cart_item['product_id']) && !empty($cart_item['product_id']))
                                                                    {
                                                                        $rtwwdpd_catids = wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => 'ids' ) );	
                                                                    }
                                                                   
                                                                }
                                                                if(isset($name['category_id']) &&  is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($name['category_id'], $rtwwdpd_catids))
                                                                {
                                                                    if(isset($cart_item['quantity']))
                                                                    {
                                                                     $rtwwdpd_total_price += $cart_item['quantity'] * $cart_item['data']->get_price();
                                                                    }
                                                                    if(isset($cart_item['quantity']))
                                                                    {
                                                                        $rtwwdpd_total_quantity += $cart_item['quantity'];
                                                                    }
                                                                }
                                                            }
                                                            
                                                        }
                                                        if($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
                                                        {
                                                            $rtwwdpd_on_single_or_whole = 'whole';
                                                            $rtwwdpd_on_single_or_whole = apply_filters('rtwwdpd_on_single_or_whole', $rtwwdpd_on_single_or_whole);
        
                                                            if($rtwwdpd_on_single_or_whole == 'whole')
                                                            {
                                                                if($rtwwdpd_total_quantity < $name['rtwwdpd_min_cat'])
                                                                {
                                                                    continue 1;
                                                                }
                                                            }
                                                            elseif($rtwwdpd_on_single_or_whole == 'single')
                                                            {
                                                                if($cart_item['quantity'] < $name['rtwwdpd_min_cat'])
                                                                {
                                                                    continue 1;
                                                                }
                                                            }
                                                            if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                            {
                                                                if($rtwwdpd_on_single_or_whole == 'whole')
                                                                {
                                                                    if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_quantity)
                                                                    {
                                                                        continue 1;
                                                                    }
                                                                }elseif($rtwwdpd_on_single_or_whole == 'single')
                                                                {	
                                                                    if($name['rtwwdpd_max_cat'] < $cart_item['quantity'])
                                                                    {
                                                                        continue 1;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        elseif($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
                                                        {
                                                           
                                                            if($rtwwdpd_total_price < $name['rtwwdpd_min_cat'])
                                                            {
                                                                continue 1;
                                                            }
                                                            if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                            {
                                                                if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_price)
                                                                {
                                                                    continue 1;
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                            if($rtwwdpd_total_weight < $name['rtwwdpd_min_cat'])
                                                            {
                                                                continue 1;
                                                            }
                                                            if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
                                                            {
                                                                if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_weight)
                                                                {
                                                                    continue 1;
                                                                }
                                                            }
                                                        }
                                                        if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_dscnt_cat_val"]/100));
                
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = ( $original_price - $name["rtwwdpd_dscnt_cat_val"] );
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                        }	

                                                    
                                                    
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        elseif($rule_name == 'pro_rule_row')
                        {
                            
                            if(isset($rtwwdpd_offers['pro_rule']))
                            {
                                $rtwwdpd_rule_name = get_option('rtwwdpd_single_prod_rule');
                                
                                if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
                                {
                                    
                                    $active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

                                    foreach ($rtwwdpd_rule_name as $name) 
                                    {
                                        if($active_dayss == 'yes')
                                        {
                                            $active_days = isset($pro_rul['rtwwwdpd_prod_day']) ? $pro_rul['rtwwwdpd_prod_day'] : array();
                                            $current_day = date('N');

                                            if(!in_array($current_day, $active_days))
                                            {
                                                continue;
                                            }
                                        }

                                        $rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
                                        $rtwwdpd_role_matched = false;
                                        if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role) )
                                        {
                                            foreach ($rtwwdpd_user_role as $rol => $role) {
                                                if($role == 'all'){
                                                    $rtwwdpd_role_matched = true;
                                                }
                                                if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                                    $rtwwdpd_role_matched = true;
                                                }
                                            }
                                        }

                                        if($rtwwdpd_role_matched == false)
                                        {
                                            continue;
                                        }

                                        $rtw_curnt_dayname = date("N");
                                        $rtwwdpd_day_waise_rule = false;
                                        if(isset($name['rtwwdpd_enable_day']) && $name['rtwwdpd_enable_day'] == 'yes')
                                        {
                                            
                                            if(isset($name['rtwwdpd_select_day']) && !empty($name['rtwwdpd_select_day']))
                                            {
                                                if($name['rtwwdpd_select_day'] == $rtw_curnt_dayname)
                                                {
                                                    $rtwwdpd_day_waise_rule = true;
                                                }
                                            }
                                            if($rtwwdpd_day_waise_rule == false)
                                            {
                                                
                                                continue;
                                            }
                                        }

                                        if(isset($name['rtwwdpd_exclude_sale']))
                                        {
                                            if( $product->is_on_sale() )
                                            {
                                                continue;
                                            }
                                        }

                                        if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
                                        {
                                            if( $name['rtwwdpd_min'] > 1)
                                            {
                                                continue;
                                            }
                                        }

                                        if($name['rtwwdpd_single_from_date'] > $rtwwdpd_today_date || $name['rtwwdpd_single_to_date'] < $rtwwdpd_today_date)
                                        {
                                            continue;
                                        }
                                        
                                        if(isset($name['rtwwdpd_rule_on']) && ($name['rtwwdpd_rule_on'] == 'rtwwdpd_products' || $name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products'))
                                        // if(isset($name['product_id']))
                                        {
                                            if( $name['rtwwdpd_rule_on'] == 'rtwwdpd_products' )
                                            {
                                                $rtw_id = $name['product_id'];
                                                if($rtw_id == $product->get_id())
                                                {
                                                    if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
                                                    {
                                                        if( isset($original_price) && !empty($original_price))
                                                        {
                                                            $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_discount_value"]/100));
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                        else{
                                                            $original_price = $product->get_price();
                                                            $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_discount_value"]/100));
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if( isset($original_price) && !empty($original_price))
                                                        {
                                                            $discounted_price = ( $original_price - $name["rtwwdpd_discount_value"] );
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                        else{
                                                            $original_price = $product->get_price();
                                                            $discounted_price = ( $original_price - $name["rtwwdpd_discount_value"] );
                                                            $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                            return $price_html;
                                                        }
                                                    }
                                                }
                                            }elseif(  $name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products' )
                                            {
                                                if($name['rtwwdpd_condition'] != 'rtwwdpd_and')
                                                {
                                                    $rtw_id = $name['multiple_product_ids'];
                                                    if(in_array($product->get_id(), $rtw_id))
                                                    {
                                                        if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_discount_value"]/100));
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                            else{
                                                                $original_price = $product->get_price();
                                                                $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_discount_value"]/100));
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = ( $original_price - $name["rtwwdpd_discount_value"] );
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                            else{
                                                                $original_price = $product->get_price();
                                                                $discounted_price = ( $original_price - $name["rtwwdpd_discount_value"] );
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
                                            {
                                                $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_discount_value"]/100));
                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                return $price_html;
                                            }
                                            else
                                            {
                                                $discounted_price = ( $original_price - $name["rtwwdpd_discount_value"] );
                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                return $price_html;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        elseif($rule_name == 'prod_tag_rule_row')
                        {
                            $rtwwdpd_tag = wp_get_post_terms( get_the_id(), 'product_tag' );
                            if(!empty($rtwwdpd_tag))
                            {
                                if(isset($rtwwdpd_offers['prod_tag_rule']))
                                {
                                    $rtwwdpd_rule_name = get_option('rtwwdpd_tag_method');
                                    if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
                                    {
                                        foreach ($rtwwdpd_rule_name as $ke => $name) 
                                        {
                                            $rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
                                            $rtwwdpd_role_matched = false;
                                            if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role) )
                                            {
                                                foreach ($rtwwdpd_user_role as $rol => $role) {
                                                    if($role == 'all'){
                                                        $rtwwdpd_role_matched = true;
                                                    }
                                                    if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                                        $rtwwdpd_role_matched = true;
                                                    }
                                                }
                                            }
                                            
                                            if($rtwwdpd_role_matched == false)
                                            {
                                                continue;
                                            }
                                            
                                            if(isset($name['rtwwdpd_tag_exclude_sale']))
                                            {
                                                if( $product->is_on_sale() )
                                                {
                                                    continue;
                                                }
                                            }

                                            if($name['rtwwdpd_tag_from_date'] > $rtwwdpd_today_date || $name['rtwwdpd_tag_to_date'] < $rtwwdpd_today_date)
                                            {
                                                continue;
                                            }

                                            if(isset($name['rtw_product_tags']) && is_array($name['rtw_product_tags']) && !empty($name['rtw_product_tags']))
                                            {
                                                foreach ($name['rtw_product_tags'] as $tag => $tags) 
                                                {	
                                                    if(in_array($tags, array_column($rtwwdpd_tag, 'term_id')))
                                                    { 
                                                        if($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_discount_percentage')
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_tag_discount_value"]/100));
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                            
                                                        }
                                                        else
                                                        {
                                                            if( isset($original_price) && !empty($original_price))
                                                            {
                                                                $discounted_price = ( $original_price - $name["rtwwdpd_tag_discount_value"] );
                                                                $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                                return $price_html;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        elseif($rule_name == 'attr_rule_row')
                        {
                            global $post;
                            if(isset($rtwwdpd_offers['attr_rule']))
                            {
                                $rtwwdpd_rule_name = get_option('rtwwdpd_att_rule');
                                $max_quant = 0;
                                $this_price = 0;
                                $max_price = 0;
                                $max_weight = 0;
                                $total_weight = 0;
                                
                                if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
                                {
                                    foreach ($rtwwdpd_rule_name as $ke => $name) 
                                    {  
                                        $ai = 1;
                                        $rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
                                        $rtwwdpd_role_matched = false;
                                        if(!empty($rtwwdpd_user_role))
                                        {
                                            foreach ($rtwwdpd_user_role as $rol => $role) {
                                                if($role == 'all'){
                                                    $rtwwdpd_role_matched = true;
                                                }
                                                if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                                    $rtwwdpd_role_matched = true;
                                                }
                                            }
                                            if($rtwwdpd_role_matched == false)
                                            {
                                                continue 1;
                                            }
                                        }

                                        if(isset($name['rtwwdpd_att_exclude_sale']))
                                        {
                                            if( $product->is_on_sale() )
                                            {
                                                continue;
                                            }
                                        }

                                        if($name['rtwwdpd_att_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_att_from_date'] )
                                        {
                                            $rtwwdpd_attr = array();
                                            if( !empty($product->get_parent_id()) )
                                            {
                                                $rtwwdpd_attr = wc_get_product($product->get_parent_id())->get_attributes();
                                            }
                                            else{
                                                $rtwwdpd_attr = $product->get_attributes();
                                            }
                    
                                            $attr_ids = array();
                    
                                            foreach ($rtwwdpd_attr as $attrr => $att) 
                                            {
                                                if(is_object($att))
                                                {
                                                    foreach ($att->get_options() as $kopt => $opt) 
                                                    {
                                                        $attr_ids[] = $opt;
                                                    }
                                                }
                                            }
                                            if ( sizeof( $woocommerce->cart->get_cart() ) > 0) 
                                            {
                                                
                                                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item )
                                                {
                                                    if( isset($name['rtwwdpd_max']) && !empty($name['rtwwdpd_max']) )
                                                    {
                                                        
                                                        $product_id = isset($name['rtwwdpd_attribute_val'])? $name['rtwwdpd_attribute_val'] : '';
                                                        foreach($name['rtwwdpd_attribute_val'] as $key => $val)								
                                                        {
                                                            
                                                            if(in_array($val,$attr_ids))
                                                            {
                                                              
                                                                if($ai == 1)
                                                                {
                                                                   
                                                                    $this_price = $cart_item['data']->get_price();
                                                                   
                                                                    $ai++;
                                                                }
                                                                $max_price += $cart_item['quantity']*$cart_item['data']->get_price();
                                                                
                                                                $max_quant += $cart_item['quantity'];
                                                                
                                                                if( !empty($cart_item['data']->get_weight()) )
                                                                {
                                                                    $max_weight += $cart_item['quantity']*$cart_item['data']->get_weight();
                                                                }
                                                            }
                                                        }										
                                                    }
                                                }
                                            }
                                            
                                            $attribut_val = isset($name['rtwwdpd_attribute_val']) ? $name['rtwwdpd_attribute_val'] : array();
                                            $rtwwdpd_arr = array_intersect( $attr_ids, $attribut_val );
                                            if(is_array($rtwwdpd_arr) && empty($rtwwdpd_arr))
                                            {
                                                continue 1;
                                            }
                                            if(isset($name['product_exe_id']) && (in_array($product->get_id(),$name['product_exe_id']) ||  in_array($product->get_parent_id(),$name['product_exe_id'])))
                                            {
                                                continue 1;
                                            }
                                            if(isset($name['rtwwdpd_att_exclude_sale']) && $name['rtwwdpd_att_exclude_sale'] == 'yes' )
                                            {
                                                if( $product->is_on_sale() )
                                                {
                                                    continue 1;
                                                }
                                            }
                                            
                                            if($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
                                            {
                                                if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
                                                {	
                                                    
                                                    if( $max_quant < $name['rtwwdpd_min'] )
                                                    {
                                                        continue 1;
                                                    }
                                                    if( isset($name['rtwwdpd_max']) && $name['rtwwdpd_max'] != '' && $max_quant > $name['rtwwdpd_max'] )
                                                    {
                                                        continue 1;
                                                    }
                                                }
                                                elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
                                                {
                                                    if( $max_price < $name['rtwwdpd_min'] )
                                                    {
                                                        continue 1;
                                                    }
                                                    $total_cost = ( $cart_item['data']->get_price() * $cart_item['quantity'] );
                                                    if( isset($name['rtwwdpd_max']) && !empty($name['rtwwdpd_max']) && $total_cost > $name['rtwwdpd_max'] )
                                                    {
                                                        continue 1;
                                                    }
                                                }
                                                elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
                                                {
                                                    
                                                    if( ($cart_item['quantity']*$cart_item['data']->get_weight()) < $name['rtwwdpd_min'] )
                                                    {
                                                        continue 1;
                                                    }
                                                    if( isset($name['rtwwdpd_max']) && $name['rtwwdpd_max'] != '' && ($cart_item['quantity']*$cart_item['data']->get_weight()) > $name['rtwwdpd_max'] )
                                                    {
                                                        continue 1;
                                                
                                                    }
                                                }
                                                if( isset($original_price) && !empty($original_price))
                                                {
                                                    $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_att_discount_value"]/100));
                                                    $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                    return $price_html;
                                                }
                                                else{
                                                    $original_price = $product->get_price();
                                                    $discounted_price = $original_price - ($original_price * ( $name["rtwwdpd_att_discount_value"]/100));
                                                    $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                    return $price_html;
                                                }
                                            }
                                            else
                                            {
                                                if( isset($original_price) && !empty($original_price))
                                                {
                                                    $discounted_price = ( $original_price - $name["rtwwdpd_att_discount_value"] );
                                                    $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                    return $price_html;
                                                }
                                                else{
                                                    $original_price = $product->get_price();
                                                    $discounted_price = ( $original_price - $name["rtwwdpd_att_discount_value"] );
                                                    $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($discounted_price);
                                                    return $price_html;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            };

            $rtwwdpd_enable = get_option('rtwwdpd_specific_enable');
            $rtwwdpd_spec_cus_opt = array();
            if(isset($rtwwdpd_enable) && $rtwwdpd_enable == 'enable'){
                $rtwwdpd_spec_cus_opt = get_option('rtwwdpd_specific_c');
            }
            if( is_array($rtwwdpd_spec_cus_opt ) && !empty( $rtwwdpd_spec_cus_opt ) )
            {
                foreach ($rtwwdpd_spec_cus_opt as $key => $value) {

                    if($value['rtwwdpd_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_to_date'] < $rtwwdpd_today_date)
                    {
                        continue 1;
                    }
                    $rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
                    $rtwwdpd_role_matched = false;
                    if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
                    {
                        foreach ($rtwwdpd_user_role as $rol => $role) {
                            if($role == 'all'){
                                $rtwwdpd_role_matched = true;
                            }
                            if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
                                $rtwwdpd_role_matched = true;
                            }
                        }
                    }
                    if($rtwwdpd_role_matched == false)
                    {
                        continue;
                    }
                    // $rtwemails = explode(",", $value['rtwwdpd_user_emails']);
                    $rtwemails = isset($value['rtwwdpd_user_emails']) ? $value['rtwwdpd_user_emails'] : array();
                    $emails = array();
                    if(!empty($rtwemails) && is_array($rtwemails))
                    {
                        
                        foreach ($rtwemails as $eid => $email) {
                            $emails[$eid] = trim($email);
                        }
                        $rtw = wp_get_current_user();
                        $curr_email = $rtw->user_email;
                        if(!in_array($curr_email, $emails))
                        {
                            continue;
                        }
                    }

                    if( isset($value['rtwwdpd_combi_exclude_sale']) )
                    {
                        if( $product->is_on_sale() )
                        {
                            continue;
                        }
                    }
                    $rtwwdpd_dis_val = $value['rtwwdpd_dscnt_val'];
                    
                    $rtwwdpd_max_dscnt = $value['rtwwdpd_max_discount'];
                    
                    $rtwwdpd_dscnted_price = 0;

                    if(isset($original_price) && !empty($original_price) && $product->get_type() != 'grouped')
                    {
                        if( $value['rtwwdpd_dsnt_type'] == 'rtwwdpd_discount_percentage' )
                        {
                            $rtwwdpd_dscnted_price = $original_price - ($original_price * ($rtwwdpd_dis_val/100));
                        }
                        else{
                            if($rtwwdpd_max_dscnt < $rtwwdpd_dis_val)
                            {
                                $rtwwdpd_dis_val = $rtwwdpd_max_dscnt;
                            }
                            $rtwwdpd_dscnted_price = $original_price - $rtwwdpd_dis_val;
                            if($original_price < $rtwwdpd_dis_val)
                            {
                                $rtwwdpd_dscnted_price = 0;
                            }
                        }
                    }

                    // $price_html = '<del>'.wc_price($original_price).'</del>'.' ' .wc_price($rtwwdpd_dscnted_price);
                    $price_html = wc_price($rtwwdpd_dscnted_price);
                    return $price_html;
                }
            }
        }
        return $price_html;
    }

    public function rtw_get_price_to_discount( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtw_stack_rules = false ) {
        
		$sabcd = 'verification_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
			global $woocommerce;
			$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
			$rtwwdpd_result = false;
			do_action( 'rtwwdpd_memberships_discounts_disable_price_adjustments' );

			$rtwwdpd_filter_cart_item = $rtwwdpd_cart_item;
			if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ] ) ) {
				$rtwwdpd_filter_cart_item = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ];

				if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] ) ) {
					if ( $this->rtwwdpd_is_cumulative( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) || $rtw_stack_rules ) {
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_adjusted'];
					} else {
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_base'];
					}
				} else {
					if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
					{
						if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $rtwwdpd_filter_cart_item['data'] ) ) {
							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_price('edit');
						} 
						else {
							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
						}
					}
					else{
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
					}
				}
			}
			
			return $rtwwdpd_result;
		}
	}

}