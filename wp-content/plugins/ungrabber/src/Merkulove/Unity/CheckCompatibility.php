<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.3
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * SINGLETON: Class used for check plugin compatibility on early phase.
 *
 * @since 1.0.0
 *
 **/
final class CheckCompatibility {

    /**
     * Array of messages to show in admin area if some checks fails.
     *
     * @var array
     **/
    private $admin_messages = [];

    /**
     * The one true CheckCompatibility.
     *
     * @since 1.0.0
     * @var CheckCompatibility
     **/
    private static $instance;

    /**
     * Do initial hosting environment check: PHP version and critical extensions.
     *
     * @param array $checks - List of critical initial checks to run. List of available checks: 'php56', 'curl'
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool - True if all checks passed, false otherwise.
     **/
    public function do_initial_checks( $checks, $show_message = true ) {

        /** Flag to indicate failed checks. */
        $pass = true;

        /** Plugin require PHP 5.6 or higher. */
        if ( in_array( 'php56', $checks, true ) ) {

            /** @noinspection NestedPositiveIfStatementsInspection */
            if ( false === $this->check_php56_version( $show_message ) ) { $pass = false; }

        }

        /** Plugin require cURL extension. */
        if ( in_array( 'curl', $checks, true ) ) {

            /** @noinspection NestedPositiveIfStatementsInspection */
            if ( false === $this->check_curl( $show_message ) ) { $pass = false; }

        }

        /** Add handler to show admin messages. */
        $this->admin_notices( $show_message );

        return $pass;

    }

    /**
     * Do environment checks for required extensions on plugin page, before show any settings.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return bool - true if all checks passed, false otherwise.
     **/
    public function do_settings_checks( $show_message = true ) {

        /** Flag to indicate failed checks. */
        $pass = true;

        /** Plugin require cURL extension. */
        $curl = $this->check_curl( $show_message );
        if ( false ===  $curl ) { $pass = false; }

        /** Plugin require DOM extension. */
        $dom = $this->check_dom( $show_message );
        if ( false ===  $dom ) { $pass = false; }

        /** Plugin require XML extension. */
        $xml = $this->check_xml( $show_message );
        if ( false ===  $xml ) { $pass = false; }

        /** Add handler to show admin messages. */
        $this->admin_notices( $show_message );

        return $pass;

    }

    /**
     * Add handler to show admin messages.
     *
     * @param $show_message
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function admin_notices( $show_message ) {

        /** Do we need to show message in admin area. */
        if ( ! $show_message ) { return; }

        /** Too early to call get_current_screen(). */
        if ( ! function_exists( 'get_current_screen' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/screen.php' );
        }

        /** Detect Plugin Settings Page. */
        $screen = get_current_screen(); // Get current screen.

        if ( null !== $screen && in_array( $screen->base, Plugin::get_menu_bases(), true ) ) {

            /** Show messages as snackbars. */
            foreach ( $this->admin_messages as $message ) {
                $this->render_snackbar_message( $message, 'error' );
            }

        } else {

            /** Show messages as WordPress admin messages. */
            add_action( 'admin_notices', [ $this, 'show_admin_messages' ] );

        }

    }

    /**
     * Show messages in Admin area.
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    public function show_admin_messages() {

        /** Show messages as WordPress admin messages. */
        foreach ( $this->admin_messages as $message ) {
            $this->render_classic_message( $message, 'error' );
        }

    }

    /**
     * Render message in snackbar style.
     *
     * @param string $message - Message to show.
     * @param string $type - Type of message: info|error|warning
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function render_snackbar_message( $message, $type = 'warning' ) {

        /** Render message in snackbar style. */
        UI::get_instance()->render_snackbar(
            $message,
            $type,
            -1,
            true
        );

    }

    /**
     * Render message in classic WordPress style.
     *
     * @param string $message - Message to show
     * @param string $type - Type of message: info|error|warning
     *
     * @since 1.0.0
     * @access public
     *
     * @return void
     **/
    private function render_classic_message( $message, $type = 'warning' ) {

        /** Render message in old fashion style. */
        ?>
        <div class="settings-error notice notice-<?php esc_attr_e( $type ); ?>">
            <h4><?php esc_html_e( 'UnGrabber', 'ungrabber' ); ?></h4>
            <p><?php esc_html_e( $message ); ?></p>
        </div>
        <?php

    }

    /**
     * Check minimal required php version.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if php version is 5.6 or higher, false otherwise.
     **/
    private function check_php56_version( $show_message = true ) {

        /** Plugin require PHP 5.6 or higher. */
        $res = ! ( ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < 50600 );

        /** If we need to show message in admin area. */
        if ( false === $res && $show_message ) {

            $this->admin_messages[] = esc_html__( 'The minimum PHP version required for UnGrabber plugin is 5.6.0.', 'ungrabber' );

        }

        return $res;

    }

    /**
     * Check whether the cURL extension is installed.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if curl extension is loaded, false otherwise.
     **/
    private function check_curl( $show_message = true ) {

        /** Whether the cURL extension is installed. */
        $curl = ReporterServer::get_instance()->get_curl_installed();
        $check = ! $curl['warning'];

        /** If we need to show message in admin area. */
        if ( false === $check && $show_message ) {
            $this->admin_messages[] = $curl['recommendation'];
        }

        return $check;

    }

    /**
     * Check whether the DOM extension is installed.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if DOM extension is loaded, false otherwise.
     **/
    private function check_dom( $show_message = true ) {

        /** Whether the DOM extension is installed. */
        $dom = ReporterServer::get_instance()->get_dom_installed();
        $check = ! $dom['warning'];

        /** If we need to show message in admin area. */
        if ( false === $check && $show_message ) {
            $this->admin_messages[] = $dom['recommendation'];
        }

        return $check;

    }

    /**
     * Check whether the xml extension is installed.
     *
     * @param bool $show_message - Show or hide messages in admin area.
     *
     * @since 1.0.0
     * @access private
     *
     * @return bool - true if xml extension is loaded, false otherwise.
     **/
    private function check_xml( $show_message = true ) {

        /** Whether the XML extension is installed. */
        $xml = ReporterServer::get_instance()->get_xml_installed();
        $check = ! $xml['warning'];

        /** If we need to show message in admin area. */
        if ( false === $check && $show_message ) {
            $this->admin_messages[] = $xml['recommendation'];
        }

        return $check;

    }

    /**
     * Main CheckCompatibility Instance.
     * Insures that only one instance of CheckCompatibility exists in memory at any one time.
     *
     * @since 1.0.0
     * @static
     *
     * @return CheckCompatibility
     **/
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

            self::$instance = new self;

        }

        return self::$instance;

    }

} // End Class CheckCompatibility.
