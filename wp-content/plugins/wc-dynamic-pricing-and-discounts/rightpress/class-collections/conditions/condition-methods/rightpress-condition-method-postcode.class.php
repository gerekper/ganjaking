<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Postcode
 *
 * @class RightPress_Condition_Method_Postcode
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Postcode extends RightPress_Condition_Method
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
     * Get method options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        return array(
            'matches'           => esc_html__('matches', 'rightpress'),
            'does_not_match'    => esc_html__('does not match', 'rightpress'),
        );
    }

    /**
     * Check against condition method
     *
     * @access public
     * @param string $option_key
     * @param mixed $value
     * @param mixed $condition_value
     * @return bool
     */
    public function check($option_key, $value, $condition_value)
    {

        // Check if postcode matches condition
        $postcode_matches = RightPress_Conditions::check_postcode($value, $condition_value);

        // Matches
        if ($option_key === 'matches' && $postcode_matches) {
            return true;
        }
        // Does not match
        else if ($option_key === 'does_not_match' && !$postcode_matches) {
            return true;
        }

        return false;
    }





}
