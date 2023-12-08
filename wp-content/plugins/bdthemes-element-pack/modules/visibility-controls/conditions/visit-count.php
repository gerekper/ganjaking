<?php

namespace ElementPack\Modules\VisibilityControls\Conditions;

use Elementor\Controls_Manager;
use ElementPack\Base\Condition;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('template_redirect', function () {
    ob_start();
});

class Visit_Count extends Condition
{

    /**
     * Get the name of condition
     * @return string as per our condition control name
     * @since  6.4.2
     */
    public function get_name()
    {
        return 'visit_count';
    }

    /**
     * Get the title of condition
     * @return string as per condition control title
     * @since  6.4.2
     */
    public function get_title()
    {
        return esc_html__('Visit Count', 'bdthemes-element-pack');
    }

    /**
     * Get the group of condition
     * @return string as per our condition control name
     * @since  6.11.3
     */
    public function get_group() {
        return 'user';
    }

    /**
     * Get the visitor
     * @return array of different languages
     * @since  6.4.2
     */
    public function get_control_value()
    {
        return array(
            'label' => __('Up to time(s)', 'bdthemes-element-pack'),
            'type' => Controls_Manager::NUMBER,
            'default' => '1',
        );
    }

    /**
     * Check the condition
     *
     * @param string $relation Comparison operator for compare function
     * @param mixed $val will check the control value as per condition needs
     *
     * @since 6.4.2
     */
    public function check($relation, $val)
    {

        $bdt_cookie_id = "bdt-visit-count-" . $this->get_element_id();

        $total_count = 0;
        if (isset($_COOKIE[$bdt_cookie_id])) {
            $total_count = $_COOKIE[$bdt_cookie_id];
            $total_count++;
        }

        setcookie($bdt_cookie_id, $total_count);

        // compare
        $show = is_array($val) && !empty($val) ? in_array($total_count, $val, true) : ($val > $total_count);

        return self::compare($show, true, $relation);
    }

}
