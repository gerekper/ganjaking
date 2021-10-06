<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer-value.class.php';

/**
 * Condition: Customer Value - Review Count
 *
 * @class RightPress_Condition_Customer_Value_Review_Count
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Value_Review_Count extends RightPress_Condition_Customer_Value
{

    protected $key          = 'review_count';
    protected $method       = 'numeric';
    protected $fields       = array(
        'after' => array('number'),
    );
    protected $position     = 60;

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

        return esc_html__('Customer review count', 'rightpress');
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

        $count = 0;

        // Get user id
        $user_id = isset($params['customer_id']) ? $params['customer_id'] : ((RightPress_Help::is_request('frontend') && is_user_logged_in()) ? get_current_user_id() : null);

        // Get by customer id
        if ($user_id) {
            $reviews = get_comments(array('fields' => 'ids', 'post_type' => 'product', 'user_id' => $user_id));
            $count += count($reviews);
        }

        // Get billing email address
        $billing_email = RightPress_Conditions::get_checkout_billing_email();

        // Get by billing email (but only those made by guest user so that we don't count the same ones we found when querying by user id)
        if ($billing_email) {
            $reviews = get_comments(array('fields' => 'ids', 'post_type' => 'product', 'user_id' => 0, 'author_email' => $billing_email));
            $count += count($reviews);
        }

        return $count;
    }





}
