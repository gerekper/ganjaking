<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-product-property.class.php';

/**
 * Condition: Product Property - Meta
 *
 * @class RightPress_Condition_Product_Property_Meta
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Product_Property_Meta extends RightPress_Condition_Product_Property
{

    protected $key          = 'meta';
    protected $method       = 'meta';
    protected $fields       = array(
        'before'    => array('meta_key'),
        'after'     => array('text'),
    );
    protected $main_field   = 'text';
    protected $position     = 90;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return esc_html__('Product meta field', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        // Check if at least one id is set
        if (empty($params['item_id']) && empty($params['child_id'])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Product is not defined.');
        }

        $meta = array();

        $meta_key = $params['condition']['meta_key'];

        // Get values for both product and variation (in case of variable product)
        foreach (array('item_id', 'child_id') as $identifier) {

            // Check if identifier is set
            if (!empty($params[$identifier])) {

                // Load product
                if ($product = wc_get_product($params[$identifier])) {

                    // Handle meta as product property
                    if (RightPress_WC::is_internal_meta($product, $meta_key, true)) {

                        // Visibility
                        if ($meta_key === '_visibility') {
                            $meta[] = $product->get_catalog_visibility();
                        }
                        // Price
                        else if ($meta_key === '_price') {
                            $meta[] = $product->get_price('edit');
                        }
                        // Regular price
                        else if ($meta_key === '_regular_price') {
                            $meta[] = $product->get_regular_price('edit');
                        }
                        // Sale price
                        else if ($meta_key === '_sale_price') {
                            $meta[] = $product->get_sale_price('edit');
                        }
                        // Rating count
                        else if ($meta_key === '_wc_rating_count') {
                            $meta[] = $product->get_rating_count();
                        }
                        // Virtual
                        else if ($meta_key === '_virtual') {
                            $meta[] = $product->is_virtual() ? 'yes' : 'no';
                        }
                        // Downloadable
                        else if ($meta_key === '_downloadable') {
                            $meta[] = $product->is_downloadable() ? 'yes' : 'no';
                        }
                        // Average rating
                        else if ($meta_key === '_wc_average_rating') {
                            $meta[] = $product->get_average_rating();
                        }
                        // Review count
                        else if ($meta_key === '_wc_review_count') {
                            $meta[] = $product->get_review_count();
                        }
                        // Regular getter
                        else {

                            // Getter method name
                            $getter = 'get_' . ltrim($meta_key, '_');

                            // Check if getter exists
                            if (method_exists($product, $getter)) {

                                // Set property value
                                $meta[] = $product->$getter();
                            }
                        }
                    }
                    // Regular meta handling
                    else {

                        // Get product meta
                        $product_meta = RightPress_WC::product_get_meta($product, $meta_key, false, 'edit');
                        $meta = array_merge($meta, RightPress_WC::normalize_meta_data($product_meta));
                    }
                }
            }
        }

        // Return meta
        return RightPress_Help::unwrap_post_meta($meta);
    }





}
