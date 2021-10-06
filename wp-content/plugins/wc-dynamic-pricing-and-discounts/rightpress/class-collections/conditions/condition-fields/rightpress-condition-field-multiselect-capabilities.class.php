<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-multiselect.class.php';

/**
 * Condition Field: Multiselect - Capabilities
 *
 * @class RightPress_Condition_Field_Multiselect_Capabilities
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Multiselect_Capabilities extends RightPress_Condition_Field_Multiselect
{

    protected $key = 'capabilities';

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
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {

        $all_capabilities = RightPress_Conditions::get_all_capabilities($ids, $query);
        return apply_filters('rightpress_all_site_capabilities', $all_capabilities);
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {

        return esc_html__('Select user capabilities', 'rightpress');
    }





}
