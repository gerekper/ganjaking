<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-text.class.php';

/**
 * Condition Field: Text - Meta Key
 *
 * @class RightPress_Condition_Field_Text_Meta_Key
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text_Meta_Key extends RightPress_Condition_Field_Text
{

    protected $key = 'meta_key';

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
        return esc_html__('meta field key', 'rightpress');
    }





}
