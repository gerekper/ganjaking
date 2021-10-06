<?php

/**
 * Description of A2W_ShippingPriceFormula
 *
 * @author Mikhail
 */
class A2W_ShippingPriceFormula {

    public $id = 0;
    public $sign = '*';
    public $value = '';

    public function __construct($data = 0) {
        if (is_int($data) && $data) {
            $this->id = $data;
            $this->load($this->id);
        } else if (is_array($data)) {
            foreach ($data as $field => $value) {
                if (property_exists(get_class($this), $field)) {
                    $this->$field = esc_attr($value);
                }
            }
        }
    }

    public function load($id = false) {
        $load_id = $id ? $id : ($this->id ? $this->id : 0);
        if ($load_id) {
            $formula_list = A2W_ShippingPriceFormula::load_formulas_list(false);
            foreach ($formula_list as $formula) {
                if (intval($formula['id']) === intval($load_id)) {
                    foreach ($formula as $field => $value) {
                        if (property_exists(get_class($this), $field)) {
                            $this->$field = esc_attr($value);
                        }
                    }
                    break;
                }
            }
        }
        return $this;
    }

    public function save() {
        $formula_list = A2W_ShippingPriceFormula::load_formulas_list(false);

        if (!intval($this->id)) {
            $this->id = 1;
            foreach ($formula_list as $key => $formula) {
                if (intval($formula['id']) >= $this->id) {
                    $this->id = intval($formula['id']) + 1;
                }
            }
            $formula_list[] = get_object_vars($this);
        } else {
            $boolean = false;
            foreach ($formula_list as $key => $formula) {
                if (intval($formula['id']) === intval($this->id)) {
                    $formula_list[$key] = get_object_vars($this);
                    $boolean = true;
                }
            }
            if (!$boolean) {
                $formula_list[] = get_object_vars($this);
            }
        }

        a2w_set_setting('shipping_formula_list', array_values($formula_list));
        return $this;
    }

    public function delete() {
        $formula_list = A2W_ShippingPriceFormula::load_formulas_list(false);
        foreach ($formula_list as $key => $formula) {
            if (intval($formula['id']) === intval($this->id)) {
                unset($formula_list[$key]);
                a2w_set_setting('shipping_formula_list', array_values($formula_list));
            }
        }
    }

    public static function deleteAll() {
        a2w_del_setting('shipping_formula_list');
    }


    public static function load_formulas() {
        return A2W_ShippingPriceFormula::load_formulas_list(true);
    }

    private static function load_formulas_list($asObject = true) {
        $result = array();
        $formula_list = a2w_get_setting('shipping_formula_list');
        $formula_list = $formula_list && is_array($formula_list) ? $formula_list : array();
        if ($asObject) {
            foreach ($formula_list as $formula) {
                $fo = new A2W_ShippingPriceFormula();
                foreach ($formula as $name => $value) {
                    if (property_exists(get_class($fo), $name)) {
                        $fo->$name = $value;
                    }
                }
                $result[] = $fo;
            }
        } else {
            $result = $formula_list;
        }

        return $result;
    }

    public static function get_default_formula() {
        $formula = a2w_get_setting('shipping_default_formula');
        return new A2W_ShippingPriceFormula($formula && is_array($formula) ? $formula : array('value' => 1, 'sign' => '*'));
    }

    public static function set_default_formula($formula) {
        
        a2w_set_setting('shipping_default_formula', get_object_vars($formula));
    }
    
    public static function normalize_shipping_price($shipping) {
        $price = 0;

        if (isset($shipping['previewFreightAmount']) && isset($shipping['previewFreightAmount']['value']))
            $price = floatval($shipping['previewFreightAmount']['value']);    
        
        else if (isset($shipping['freightAmount']) && isset($shipping['freightAmount']['value']))
            $price = floatval($shipping['freightAmount']['value']);    
        
        return $price;
           
    }
    
    public static function apply_formula($shipping, $local_options, $round = 2){

        $shipping_price = self::normalize_shipping_price($shipping);

        if ($local_options['use_price_rule']) {
            $formula = self::get_default_formula();            
            if ($formula && $formula->value){
                if ($formula->sign == "=") {
                    $shipping_price = floatval($formula->value);
                } else if ($formula->sign == "*") {
                    $shipping_price = floatval($shipping_price) * floatval($formula->value);
                } else if ($formula->sign == "+") {
                    $shipping_price = floatval($shipping_price) + floatval($formula->value);
                }    
            }    
        }
        return round($shipping_price, $round);   
    }

    public static function allow_price_rule(){
        return a2w_get_setting('aliship_frontend', false);
    }
    
    public static function allow_post_price_rule($post_id){
         $use_price_rule = get_post_meta($post_id, 'a2w_use_price_rule', true);       
         return $use_price_rule==="" || $use_price_rule === "1" ? true : false;    
    }
   
}
