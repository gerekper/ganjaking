<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
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
 * Unity active plugins report.
 * Report handler class responsible for generating a report for active plugins.
 *
 * @since 1.0.0
 **/
final class ReporterPlugins {

    /**
     * The one true ReporterPlugins.
     *
     * @var ReporterPlugins
     **/
    private static $instance;

	/**
	 * Active plugins.
	 *
	 * Holds the sites active plugins list.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $plugins;

	/**
	 * Get active plugins.
	 *
	 * Retrieve the active plugins from the list of all the installed plugins.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array Active plugins.
	 */
	private function get_plugins() {

		if ( ! $this->plugins ) {

			/** Ensure get_plugins function is loaded. */
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$active_plugins = get_option( 'active_plugins' );
			$this->plugins  = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );

		}

		return $this->plugins;

	}

	/**
	 * Get active plugins reporter title.
	 *
	 * Retrieve active plugins reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {

		return esc_html__( 'Active Plugins', 'ungrabber' );

	}

	/**
	 * Is enabled.
	 *
	 * Whether there are active plugins or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool True if the site has active plugins, False otherwise.
	 */
	public function is_enabled() {

		return (bool) $this->get_plugins();

	}

	/**
	 * Get active plugins report fields.
	 *
	 * Retrieve the required fields for the active plugins report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {

		return [
			'active_plugins' => esc_html__( 'Active Plugins', 'ungrabber' ),
		];

	}

	/**
	 * Get active plugins.
	 *
	 * Retrieve the sites active plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The active plugins list.
	 * }
	 */
	public function get_active_plugins() {

		return [
			'value' => $this->get_plugins(),
		];

	}

    /**
     * Get report.
     * Retrieve the report with all it's containing fields.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array {
     *    Report fields.
     *
     *    @type string $name Field name.
     *    @type string $label Field label.
     * }
     **/
    public function get_report() {

        $result = [];

        foreach ( $this->get_fields() as $field_name => $field_label ) {

            $method = 'get_' . $field_name;

            $reporter_field = [
                'name' => $field_name,
                'label' => $field_label,
            ];



            /** @noinspection SlowArrayOperationsInLoopInspection */
            $reporter_field = array_merge( $reporter_field, $this->$method() );

            $result[ $field_name ] = $reporter_field;

        }

        return $result;

    }

    /**
     * Main ReporterPlugins Instance.
     *
     * Insures that only one instance of ReporterPlugins exists in memory at any one time.
     *
     * @static
     * @since 1.0.0
     * @return ReporterPlugins
     **/
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

            self::$instance = new self;

        }

        return self::$instance;

    }

}
