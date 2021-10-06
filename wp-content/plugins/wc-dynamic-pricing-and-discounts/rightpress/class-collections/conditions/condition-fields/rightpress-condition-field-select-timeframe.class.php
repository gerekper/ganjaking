<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-select.class.php';

/**
 * Condition Field Group: Select - Timeframe
 *
 * @class RightPress_Condition_Field_Select_Timeframe
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Select_Timeframe extends RightPress_Condition_Field_Select
{

    protected $is_grouped = true;

    protected $timeframes_for_display = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Get options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        // Check if timeframes for display are defined
        if ($this->timeframes_for_display === null) {

            // Get timeframes
            $timeframes = RightPress_Conditions_Timeframes::get_timeframes();

            // Iterate over timeframes
            foreach ($timeframes as $timeframe_group_key => $timeframe_group) {

                // Add timeframe group
                $this->timeframes_for_display[$timeframe_group_key] = array(
                    'label'     => $timeframe_group['label'],
                    'options'   => array(),
                );

                // Add timeframe
                foreach ($timeframe_group['children'] as $timeframe_key => $timeframe) {
                    $this->timeframes_for_display[$timeframe_group_key]['options'][$timeframe_key] = $timeframe['label'];
                }
            }
        }

        // Return timeframes with all_time prepended
        if ($this->key === 'timeframe_span') {
            return array_merge(array(
                'all_time' => array(
                    'label'     => esc_html__('All time', 'rightpress'),
                    'options'   => array(
                        'all_time' => esc_html__('all time', 'rightpress'),
                    ),
                ),
            ), $this->timeframes_for_display);
        }

        // Return timeframes
        return $this->timeframes_for_display;
    }

    /**
     * Validate field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return bool
     */
    public function validate($posted, $condition, $method_option_key)
    {

        if (isset($posted[$this->key])) {
            foreach ($this->get_options() as $timeframe_group) {
                if (isset($timeframe_group['options'][$posted[$this->key]])) {
                    return true;
                }
            }
        }

        return false;
    }





}
