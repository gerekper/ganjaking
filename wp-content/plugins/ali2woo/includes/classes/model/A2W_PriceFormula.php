<?php

/**
 * Description of A2W_PriceFormula
 *
 * @author Andrey
 */
class A2W_PriceFormula
{

    public $id = 0;
    public $category = '';
    public $category_name = '';
    public $min_price = '';
    public $max_price = '';
    public $sign = '*';
    public $value = '';
    public $compared_sign = '*';
    public $compared_value = '';
    public $discount1 = '';
    public $discount2 = '';

    public static function pricing_rules_types()
    {
        return array(
            'sale_price_and_discount' => array(
                'value' => 'sale_price_and_discount',
                'name' => __('Use sale price and discount', 'ali2woo'),
                'description' => __('Ali2Woo gets your <strong>sale price</strong> by applying a pricing formula to the AliExpress sale price. Your <strong>regular price</strong> is calculated using the <i>Regular Price</i> part of the formula.<br/> If you disable that part, the <strong>regular price</strong> is formed from your calculated sale price increased by the original AliExpress discount.<br/> To learn more check out <a href="https://ali2woo.com/codex/pricing-markup-formula/">this article</a>', 'ali2woo'),
            ),
            'sale_price_as_base' => array(
                'value' => 'sale_price_as_base',
                'name' => __('Use sale price as base', 'ali2woo'),
                'description' => __('Your pricing formula is applied to the AliExpress sale price. To learn more check out <a href="https://ali2woo.com/codex/pricing-markup-formula/">this article</a>', 'ali2woo'),
            ),
            'regular_price_as_base' => array(
                'value' => 'regular_price_as_base',
                'name' => __('Use regular price as base', 'ali2woo'),
                'description' => __('Your pricing formula is applied to the AliExpress regular price. To learn more check out <a href="https://ali2woo.com/codex/pricing-markup-formula/">this article</a>', 'ali2woo'),
            ),
        );
    }

    public function __construct($data = 0)
    {
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

    public function load($id = false)
    {
        $load_id = $id ? $id : ($this->id ? $this->id : 0);
        if ($load_id) {
            $formula_list = A2W_PriceFormula::load_formulas_list(false);
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

    public function save()
    {
        $formula_list = A2W_PriceFormula::load_formulas_list(false);

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

        a2w_set_setting('formula_list', array_values($formula_list));
        return $this;
    }

    public function delete()
    {
        $formula_list = A2W_PriceFormula::load_formulas_list(false);
        foreach ($formula_list as $key => $formula) {
            if (intval($formula['id']) === intval($this->id)) {
                unset($formula_list[$key]);
                a2w_set_setting('formula_list', array_values($formula_list));
            }
        }
    }

    public static function deleteAll()
    {
        a2w_del_setting('formula_list');
    }

    public static function normalize_product_price($product)
    {
        $price = $regular_price = 0;
        if (isset($product['price']) && floatval($product['price'])) {
            $price = $regular_price = floatval($product['price']);
            if (isset($product['regular_price']) && floatval($product['regular_price'])) {
                $regular_price = floatval($product['regular_price']);
            }
        } else if (isset($product['price_min']) && floatval($product['price_min'])) {
            $price = $regular_price = floatval($product['price_min']);
            if (isset($product['regular_price_min']) && floatval($product['regular_price_min'])) {
                $regular_price = floatval($product['regular_price_min']);
            }
        } else if (isset($product['price_max']) && floatval($product['price_max'])) {
            $price = $regular_price = floatval($product['price_max']);
            if (isset($product['regular_price_max']) && floatval($product['regular_price_max'])) {
                $regular_price = floatval($product['regular_price_max']);
            }
        }
        return array('price' => $price, 'regular_price' => $regular_price);
    }

    public static function apply_formula($product, $round = 2, $type = 'all')
    {
        $pricing_rules_type = a2w_get_setting('pricing_rules_type');

        $apply_price_rules_after_shipping_cost = a2w_get_setting('apply_price_rules_after_shipping_cost');
        $shipping_cost = a2w_get_setting('add_shipping_to_price') && !empty($product['shipping_cost']) ? round($product['shipping_cost'], $round) : 0;

        if (a2w_check_defined('A2W_USE_SEPARATE_FORMULA')) {
            $formula = A2W_PriceFormula::get_formula_by_product($product, "price");
            $formula_regular = A2W_PriceFormula::get_formula_by_product($product, "regular_price");
        } else {
            $price_type = $pricing_rules_type == 'regular_price_as_base' ? "regular_price" : "price";
            $formula = A2W_PriceFormula::get_formula_by_product($product, $price_type);
            $formula_regular = A2W_PriceFormula::get_formula_by_product($product, $price_type);
        }

        $product_price = A2W_PriceFormula::normalize_product_price($product);

        if ($formula && $formula_regular && $product_price['price']) {
            $use_compared_price_markup = a2w_get_setting('use_compared_price_markup');
            $price_cents = a2w_get_setting('price_cents');
            $price_compared_cents = a2w_get_setting('price_compared_cents');

            if ($type === 'all' || $type === 'price' || !isset($product['calc_price'])) {
                // calculate price
                $price = $pricing_rules_type == 'regular_price_as_base' ? $product_price['regular_price'] : $product_price['price'];

                if ($formula->sign == "=") {
                    $product['calc_price'] = round(floatval($formula->value) + $shipping_cost, $round);
                } else if ($formula->sign == "*") {
                    if ($apply_price_rules_after_shipping_cost) {
                        $product['calc_price'] = round((floatval($price) + $shipping_cost) * floatval($formula->value), $round);
                    } else {
                        $product['calc_price'] = round(floatval($price) * floatval($formula->value) + $shipping_cost, $round);
                    }
                } else if ($formula->sign == "+") {
                    $product['calc_price'] = round(floatval($price) + $shipping_cost + floatval($formula->value), $round);
                }

                if (!empty($product['calc_price']) && $price_cents > -1) {
                    $product['calc_price'] = round(floor($product['calc_price']) + ($price_cents / 100), 2);
                }
            }

            if ($type === 'all' || $type === 'regular_price' || !isset($product['calc_regular_price'])) {
                // calculate regular_price
                if ($pricing_rules_type == 'sale_price_and_discount') {
                    // use source discount
                    if (isset($product['discount']) && isset($product['calc_price'])) {
                        $product['calc_regular_price'] = round($product['calc_price'] * 100 / (100 - min(99.9, floatval($product['discount']))), $round);
                    }

                    if ($use_compared_price_markup) {
                        if ($formula_regular->compared_sign == "=") {
                            $product['calc_regular_price'] = round(floatval($formula_regular->compared_value) + $shipping_cost, $round);
                        } else if ($formula_regular->compared_sign == "*") {
                            if ($apply_price_rules_after_shipping_cost) {
                                $product['calc_regular_price'] = round((floatval($product_price['price']) + $shipping_cost) * floatval($formula_regular->compared_value), $round);
                            } else {
                                $product['calc_regular_price'] = round(floatval($product_price['price']) * floatval($formula_regular->compared_value) + $shipping_cost, $round);
                            }
                        } else if ($formula_regular->compared_sign == "+") {
                            $product['calc_regular_price'] = round(floatval($product_price['price']) + $shipping_cost + floatval($formula_regular->compared_value), $round);
                        }
                    }

                } else {
                    $price = $pricing_rules_type == 'regular_price_as_base' ? $product_price['regular_price'] : $product_price['price'];

                    if ($use_compared_price_markup) {
                        if ($formula_regular->compared_sign == "=") {
                            $product['calc_regular_price'] = round(floatval($formula_regular->compared_value) + $shipping_cost, $round);
                        } else if ($formula_regular->compared_sign == "*") {
                            if ($apply_price_rules_after_shipping_cost) {
                                $product['calc_regular_price'] = round((floatval($price) + $shipping_cost) * floatval($formula_regular->compared_value), $round);
                            } else {
                                $product['calc_regular_price'] = round(floatval($price) * floatval($formula_regular->compared_value) + $shipping_cost, $round);
                            }
                        } else if ($formula_regular->compared_sign == "+") {
                            $product['calc_regular_price'] = round(floatval($price) + $shipping_cost + floatval($formula_regular->compared_value), $round);
                        }
                    } else {
                        if ($formula_regular->sign == "=") {
                            $product['calc_regular_price'] = round(floatval($formula_regular->value) + $shipping_cost, $round);
                        } else if ($formula_regular->sign == "*") {
                            if ($apply_price_rules_after_shipping_cost) {
                                $product['calc_regular_price'] = round((floatval($price) + $shipping_cost) * floatval($formula_regular->value), $round);
                            } else {
                                $product['calc_regular_price'] = round(floatval($price) * floatval($formula_regular->value) + $shipping_cost, $round);
                            }
                        } else if ($formula_regular->sign == "+") {
                            $product['calc_regular_price'] = round(floatval($price) + $shipping_cost + floatval($formula_regular->value), $round);
                        }
                    }
                }

                if (!empty($product['calc_regular_price']) && $price_compared_cents > -1) {
                    $product['calc_regular_price'] = round(floor($product['calc_regular_price']) + ($price_compared_cents / 100), 2);
                }

                if (!empty($product['calc_regular_price']) && !empty($product['calc_price']) && $product['calc_regular_price'] < $product['calc_price']) {
                    $product['calc_regular_price'] = $product['calc_price'];
                }
            }

            if (isset($product['sku_products']['variations']) && $product['sku_products']['variations']) {
                foreach ($product['sku_products']['variations'] as &$var) {
                    if (a2w_check_defined('A2W_USE_SEPARATE_FORMULA')) {
                        $formula = A2W_PriceFormula::get_formula_by_product($var, "price");
                        $formula_regular = A2W_PriceFormula::get_formula_by_product($var, "regular_price");
                    } else {
                        $price_type = $pricing_rules_type == 'regular_price_as_base' ? "regular_price" : "price";
                        $formula = A2W_PriceFormula::get_formula_by_product($var, $price_type);
                        $formula_regular = A2W_PriceFormula::get_formula_by_product($var, $price_type);
                    }

                    if ($formula && $formula_regular) {
                        if ($type === 'all' || $type === 'price' || !isset($var['calc_price'])) {
                            // calculate price
                            $price = $pricing_rules_type == 'regular_price_as_base' ? $var['regular_price'] : $var['price'];

                            if ($formula->sign == "=") {
                                $var['calc_price'] = round(floatval($formula->value) + $shipping_cost, $round);
                            } else if ($formula->sign == "*") {
                                if ($apply_price_rules_after_shipping_cost) {
                                    $var['calc_price'] = round((floatval($price) + $shipping_cost) * floatval($formula->value), $round);
                                } else {
                                    $var['calc_price'] = round(floatval($price) * floatval($formula->value) + $shipping_cost, $round);
                                }
                            } else if ($formula->sign == "+") {
                                $var['calc_price'] = round(floatval($price) + $shipping_cost + floatval($formula->value), $round);
                            }

                            if (!empty($var['calc_price']) && $price_cents > -1) {
                                $var['calc_price'] = round(floor($var['calc_price']) + ($price_cents / 100), 2);
                            }
                        }

                        if ($type === 'all' || $type === 'regular_price' || !isset($var['calc_regular_price'])) {
                            // calculate regular_price
                            if ($pricing_rules_type == 'sale_price_and_discount') {
                                // use source discount
                                if (isset($var['discount']) && isset($var['calc_price'])) {
                                    $var['calc_regular_price'] = round($var['calc_price'] * 100 / (100 - min(99.9, floatval($var['discount']))), $round);
                                }
                                if ($use_compared_price_markup) {
                                    if ($formula_regular->compared_sign == "=") {
                                        $var['calc_regular_price'] = round(floatval($formula_regular->compared_value) + $shipping_cost, $round);
                                    } else if ($formula_regular->compared_sign == "*") {
                                        if ($apply_price_rules_after_shipping_cost) {
                                            $var['calc_regular_price'] = round((floatval($var['price']) + $shipping_cost) * floatval($formula_regular->compared_value), $round);
                                        } else {
                                            $var['calc_regular_price'] = round(floatval($var['price']) * floatval($formula_regular->compared_value) + $shipping_cost, $round);
                                        }
                                    } else if ($formula_regular->compared_sign == "+") {
                                        $var['calc_regular_price'] = round(floatval($var['price']) + $shipping_cost + floatval($formula_regular->compared_value), $round);
                                    }
                                }

                            } else {
                                $price = $pricing_rules_type == 'regular_price_as_base' ? $var['regular_price'] : $var['price'];

                                if ($use_compared_price_markup) {
                                    if ($formula_regular->compared_sign == "=") {
                                        $var['calc_regular_price'] = round(floatval($formula_regular->compared_value) + $shipping_cost, $round);
                                    } else if ($formula_regular->compared_sign == "*") {
                                        if ($apply_price_rules_after_shipping_cost) {
                                            $var['calc_regular_price'] = round((floatval($price) + $shipping_cost) * floatval($formula_regular->compared_value), $round);
                                        } else {
                                            $var['calc_regular_price'] = round(floatval($price) * floatval($formula_regular->compared_value) + $shipping_cost, $round);
                                        }
                                    } else if ($formula_regular->compared_sign == "+") {
                                        $var['calc_regular_price'] = round(floatval($price) + $shipping_cost + floatval($formula_regular->compared_value), $round);
                                    }
                                } else {
                                    if ($formula_regular->sign == "=") {
                                        $var['calc_regular_price'] = round(floatval($formula_regular->value) + $shipping_cost, $round);
                                    } else if ($formula_regular->sign == "*") {
                                        if ($apply_price_rules_after_shipping_cost) {
                                            $var['calc_regular_price'] = round((floatval($price) + $shipping_cost) * floatval($formula_regular->value), $round);
                                        } else {
                                            $var['calc_regular_price'] = round(floatval($price) * floatval($formula_regular->value) + $shipping_cost, $round);
                                        }
                                    } else if ($formula_regular->sign == "+") {
                                        $var['calc_regular_price'] = round(floatval($price) + $shipping_cost + floatval($formula_regular->value), $round);
                                    }
                                }
                            }

                            $regular_price_cents = $use_compared_price_markup ? $price_compared_cents : $price_cents;
                            if (!empty($var['calc_regular_price']) && $regular_price_cents > -1) {
                                $var['calc_regular_price'] = round(floor($var['calc_regular_price']) + ($regular_price_cents / 100), 2);
                            }

                            if (!empty($var['calc_regular_price']) && !empty($var['calc_price']) && $var['calc_regular_price'] < $var['calc_price']) {
                                $var['calc_regular_price'] = $var['calc_price'];
                            }
                        }
                    }
                }
            }
        }

        return $product;
    }

    public static function load_formulas()
    {
        return A2W_PriceFormula::load_formulas_list(true);
    }

    private static function load_formulas_list($asObject = true)
    {
        $result = array();
        $formula_list = a2w_get_setting('formula_list');
        $formula_list = $formula_list && is_array($formula_list) ? $formula_list : array();
        if ($asObject) {
            foreach ($formula_list as $formula) {
                $fo = new A2W_PriceFormula();
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

    public static function get_default_formula()
    {
        $formula = a2w_get_setting('default_formula');
        return new A2W_PriceFormula($formula && is_array($formula) ? $formula : array('value' => 1, 'sign' => '*', 'compared_value' => 1, 'compared_sign' => '*'));
    }

    public static function set_default_formula($formula)
    {
        a2w_set_setting('default_formula', get_object_vars($formula));
    }

    public static function get_default_formulas()
    {
        $f1 = new A2W_PriceFormula();
        $f1->id = 1;
        $f1->min_price = 0;
        $f1->max_price = 10;
        $f1->sign = "*";
        $f1->value = 1;

        $f2 = new A2W_PriceFormula();
        $f2->id = 1;
        $f2->min_price = 10.01;
        $f2->max_price = '';
        $f2->sign = "*";
        $f2->value = '';

        return array($f1, $f2);
    }

    public static function get_formula_by_product($product, $base_price_type = "price")
    {
        $res_formula = false;
        $use_extended_price_markup = a2w_get_setting('use_extended_price_markup');
        $base_price_type = in_array($base_price_type, array('price', 'regular_price')) ? $base_price_type : 'price';

        if ($use_extended_price_markup) {
            $product_price = A2W_PriceFormula::normalize_product_price($product);
            $product_price = $product_price[$base_price_type];
            if ($product_price) {
                $formula_list = A2W_PriceFormula::load_formulas_list();
                foreach ($formula_list as $formula) {
                    $check = true;

                    if (isset($formula->min_price) && $formula->min_price && floatval($formula->min_price) > $product_price) {
                        $check = false;
                    }

                    if (isset($formula->max_price) && $formula->max_price && floatval($formula->max_price) < $product_price) {
                        $check = false;
                    }

                    if (isset($formula->category) && $formula->category && intval($formula->category) != intval($product['category_id'])) {
                        $check = false;
                    }

                    if ($check) {
                        $res_formula = $formula;
                        break;
                    }
                }
            } else {
                a2w_error_log("can't find normalize_product_price for " . $product['id']);
            }
        }

        return $res_formula ? $res_formula : A2W_PriceFormula::get_default_formula();
    }
}
