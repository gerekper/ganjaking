<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-decimal.class.php';

/**
 * Condition Field: Decimal - Decimal
 *
 * @class RightPress_Condition_Field_Decimal_Decimal
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Decimal_Decimal extends RightPress_Condition_Field_Decimal
{

    protected $key = 'decimal';

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





}
