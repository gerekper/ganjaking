<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-text.class.php';

/**
 * Condition Field: Text - Postcode
 *
 * @class RightPress_Condition_Field_Text_Postcode
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text_Postcode extends RightPress_Condition_Field_Text
{

    protected $key = 'postcode';

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
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return esc_html__('e.g. 90210, 902**, 90200-90299, SW1A 1AA, NSW 2001', 'rightpress');
    }





}
