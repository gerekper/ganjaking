<?php

namespace WeDevs\PM_Pro\Core\WP;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


class Integrations {

    /**
     * Init integrations.
     */
    public static function init() {

       self::slack();

    }

    /**
     * integration Wrapper.
     *
     * @param string[] $function
     * @param array $atts (default: array())
     * @return string
     */
    public static function integration_wrapper(
        $function,
        $atts = []
    ) {

        call_user_func( $function, $atts );
    }

    /**
     * integration.
     *
     * @param mixed $atts
     *
     * @return string
     */
    public static function slack( $atts = [] ) {
        return self::integration_wrapper(
            array( 'WeDevs\\PM_Pro\\Core\\Integrations\\Slack', 'getInstance' ),
            $atts
        );
    }
}
