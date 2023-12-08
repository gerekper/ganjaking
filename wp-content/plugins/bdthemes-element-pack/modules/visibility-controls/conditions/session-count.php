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

class Session_Count extends Condition
{

    /**
     * Get the name of condition
     * @return string as per our condition control name
     * @since  6.4.2
     */
    public function get_name()
    {
        return 'session_count';
    }

    /**
     * Get the title of condition
     * @return string as per condition control title
     * @since  6.4.2
     */
    public function get_title()
    {
        return esc_html__('Session Count', 'bdthemes-element-pack');
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
            'label' => __('Up to times', 'bdthemes-element-pack'),
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
        $total_count = false;

        if (!isset($_SESSION)) {
            session_start();
        }

        $user_id = session_id();
        $element_name = '_ep_' . md5($_SERVER['HTTP_HOST']) . $this->get_element_id();

        $arr = [
            'user_id' => $user_id,
            'count' => 1,
        ];

        if (isset($_COOKIE[$element_name])) {
            $co = $_COOKIE[$element_name];

            $co = json_decode(stripslashes($co), true, JSON_UNESCAPED_SLASHES);

            if (isset($co['user_id']) && $user_id != $co['user_id']) {
                $countInc = $co['count'] += 1;
                $arr = [
                    'user_id' => $user_id,
                    'count' => $countInc,
                ];

                setcookie($element_name, wp_json_encode($arr), time() + (3660 * 24 * 365), '/'); // expires after 365 days

                $total_count = $countInc;
            }

            $total_count = $co['count'];

        } else {
            setcookie($element_name, wp_json_encode($arr), time() + (3660 * 24 * 365), '/'); // expires after 365 days
        }

        // compare
        $show = is_array($val) && !empty($val) ? in_array($total_count, $val, true) : ($val >= $total_count);

        return self::compare($show, true, $relation);
    }
}
