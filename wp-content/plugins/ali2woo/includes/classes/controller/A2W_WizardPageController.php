<?php

/**
 * Description of A2W_WizardPageController
 *
 * @author Mikhail
 * 
 * @autoload: a2w_admin_init 
 */
if (!class_exists('A2W_WizardPageController')) {


    class A2W_WizardPageController extends A2W_AbstractAdminPage {

      

        public function __construct() {
            parent::__construct(__('Wizard', 'ali2woo'), __('Wizard', 'ali2woo'), 'import', 'a2w_wizard', 30, 2);
        }
  
        public function render($params = array()) {
           $errors = array();

           if (isset($_POST['wizard_form'])) {
                a2w_settings()->auto_commit(false);
      
                
                if (isset($_POST['a2w_item_purchase_code']) && trim($_POST['a2w_item_purchase_code'])){
                    a2w_set_setting('item_purchase_code', isset($_POST['a2w_item_purchase_code']) ? wp_unslash($_POST['a2w_item_purchase_code']) : '');
                } else {
                    $errors['a2w_item_purchase_code'] = esc_html__('required field', 'ali2woo'); 
                }
                

                if (isset($_POST['a2w_import_language'])){
                    a2w_set_setting('import_language', isset($_POST['a2w_import_language']) ? wp_unslash($_POST['a2w_import_language']) : 'en');
                }

                if (isset($_POST['a2w_local_currency'])){
                    $currency = isset($_POST['a2w_local_currency']) ? wp_unslash($_POST['a2w_local_currency']) : 'USD';
                    a2w_set_setting('local_currency', $currency);
                    update_option( 'woocommerce_currency',  $currency );
                } 


                $a2w_description_import_mode = isset($_POST['a2w_description_import_mode']) ? $_POST['a2w_description_import_mode'] :  "use_spec";
            
                a2w_set_setting('not_import_attributes', false);

                if ($a2w_description_import_mode == "use_spec"){

                    a2w_set_setting('not_import_description', true);
                    a2w_set_setting('not_import_description_images', true);

                } else {
                    a2w_set_setting('not_import_description', false);
                    a2w_set_setting('not_import_description_images', false);    
                }

                //pricing rules setup

                $a2w_pricing_rules = isset($_POST['a2w_pricing_rules']) ? $_POST['a2w_pricing_rules'] :  "low-ticket-fixed-3000";
                $a2w_add_shipping_to_product =  isset($_POST['a2w_add_shipping_to_product']);

                a2w_set_setting('pricing_rules_type', 'sale_price_as_base');
                a2w_set_setting('use_extended_price_markup', false);
                a2w_set_setting('use_compared_price_markup', false);
                a2w_set_setting('price_cents', -1);
                a2w_set_setting('price_compared_cents', -1);
                a2w_set_setting('default_formula', false);

                A2W_PriceFormula::deleteAll();

                if ($a2w_pricing_rules == "low-ticket-fixed-3000"){

                    $default_rule = array( 'value' => 3, 'sign' => '*', 'compared_value' => 1, 'compared_sign' => '*');
                    A2W_PriceFormula::set_default_formula(new A2W_PriceFormula($default_rule));         

                }

                if ($a2w_pricing_rules != "no" && $a2w_add_shipping_to_product){
                    a2w_set_setting('add_shipping_to_price', true);
                    a2w_set_setting('apply_price_rules_after_shipping_cost', true);
                } else {
                    a2w_set_setting('add_shipping_to_price', false);
                    a2w_set_setting('apply_price_rules_after_shipping_cost', false);
                }

                //phrase rules setup        
                if (isset($_POST['a2w_remove_unwanted_phrases'])){

                    A2W_PhraseFilter::deleteAll();

                    $phrases = array();
                    $phrases[] = array('phrase'=>'China', 'phrase_replace'=>'');
                    $phrases[] = array('phrase'=>'china', 'phrase_replace'=>'');
                    $phrases[] = array('phrase'=>'Aliexpress', 'phrase_replace'=>'');
                    $phrases[] = array('phrase'=>'AliExpress', 'phrase_replace'=>'');

                    foreach ($phrases as $phrase) {
                        $filter = new A2W_PhraseFilter($phrase);
                        $filter->save();
                    }
            
                }


                if (isset($_POST['a2w_fulfillment_phone_code']) && trim($_POST['a2w_fulfillment_phone_code']) 
                    && isset($_POST['a2w_fulfillment_phone_number']) && trim($_POST['a2w_fulfillment_phone_number']))
                {
                    a2w_set_setting('fulfillment_phone_code',  wp_unslash($_POST['a2w_fulfillment_phone_code']));
                    a2w_set_setting('fulfillment_phone_number', wp_unslash($_POST['a2w_fulfillment_phone_number']));

                } else {
                    $errors['a2w_fulfillment_phone_block'] = esc_html__('required fields', 'ali2woo'); 
                }

                if (isset($_POST['a2w_import_reviews'])){

                    a2w_set_setting('load_review', true);
                    a2w_set_setting('review_status', true);
                    a2w_set_setting('review_translated', true);
                    
                    a2w_set_setting('review_min_per_product', 10);
                    a2w_set_setting('review_max_per_product', 20);
                   
                    a2w_set_setting('review_raiting_from', 4);
                    a2w_set_setting('review_raiting_to', 5);

                    a2w_set_setting('review_thumb_width', 30);   

                    a2w_set_setting('review_load_attributes', false);

                    a2w_set_setting('review_show_image_list', true);

                    a2w_set_setting('review_skip_keywords', '');

                    a2w_set_setting('review_skip_empty', true);

                    a2w_set_setting('review_country', array()); 

                    a2w_set_setting('moderation_reviews', false);
          

                }

                a2w_settings()->commit();
                a2w_settings()->auto_commit(true);

                $redirect = add_query_arg( 'setup_wizard', 'success', admin_url('admin.php?page=a2w_dashboard') );

                wp_redirect($redirect);

           }

            $localizator = A2W_AliexpressLocalizator::getInstance();

            $language_model = new A2W_Language();

            $description_import_modes = array(
                "use_spec" => esc_html_x('Use product specifications instead of description (recommended)', 'Wizard', 'ali2woo'), 
                "import_desc" => esc_html_x('Import description from AliExpress', 'Wizard', 'ali2woo'),
            );

            $pricing_rule_sets = array(
                "no" => esc_html_x('No, i will set up prices myself later', 'Wizard', 'ali2woo'), 
                "low-ticket-fixed-3000" => esc_html_x('Set 300% fixed markup (if you sell only low-ticket products only)', 'Wizard', 'ali2woo'), 
            );

            $close_link = admin_url( 'admin.php?page=a2w_dashboard' );
    
            $this->model_put("currencies", $localizator->getCurrencies(false));
            $this->model_put("custom_currencies", $localizator->getCurrencies(true));
            $this->model_put("description_import_modes", $description_import_modes);
            $this->model_put("pricing_rule_sets", $pricing_rule_sets);
            $this->model_put("errors", $errors);
            $this->model_put("languages", $language_model->get_languages());
            $this->model_put("close_link", $close_link);

            $this->include_view("wizard.php");
        }

    

   
    }

}
