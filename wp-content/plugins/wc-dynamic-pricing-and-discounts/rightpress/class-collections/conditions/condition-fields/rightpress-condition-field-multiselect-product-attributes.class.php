<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-multiselect.class.php';

/**
 * Condition Field: Multiselect - Product Attributes
 *
 * @class RightPress_Condition_Field_Multiselect_Product_Attributes
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Multiselect_Product_Attributes extends RightPress_Condition_Field_Multiselect
{

    protected $key                  = 'product_attributes';
    protected $supports_hierarchy   = true;

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
     * Get child ids for fields that support hierarchy
     *
     * @access public
     * @param array $values
     * @return array
     */
    public function get_children($values)
    {

        global $wpdb;

        $values_with_children = array();

        foreach ($values as $value) {

            // Determine taxonomy
            $taxonomy = $wpdb->get_var('SELECT taxonomy FROM ' . $wpdb->term_taxonomy . ' WHERE term_id = ' . absint($value));

            // Taxonomy was found
            if ($taxonomy !== null) {
                $values_with_children[$value] = RightPress_Help::get_term_with_children($value, $taxonomy);
            }
            // Taxonomy was not found
            else {
                $values_with_children[$value] = array($value);
            }
        }

        return $values_with_children;
    }

    /**
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {

        return RightPress_Conditions::get_all_product_attributes($ids, $query);
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {

        return esc_html__('Select product attributes', 'rightpress');
    }





}
