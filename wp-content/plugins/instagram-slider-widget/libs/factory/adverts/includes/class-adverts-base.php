<?php

namespace WBCR\Factory_Adverts_102;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for adverts module.
 *
 * Contains methods for retrieving banner data for a specific position.
 * With this class user cat get advert content for a specific position.
 * This class use functional design pattern.
 * It used in the main plugin file. Also some methods may used in any place of plugin.
 *
 * Example (main plugin file):
 *  // FRAMEWORK MODULES
 *  'load_factory_modules' => array(
 *      ...
 *      array( 'libs/factory/adverts', 'factory_adverts_102', 'admin' ),
 *  ),
 *
 *  if ( is_admin() ) {
 *      global $wbcr_PLUGIN_NAME_adinserter;
 *
 *      $wbcr_PLUGIN_NAME_adinserter = new WBCR\Factory_Adverts_102\Base(
 *          __FILE__,
 *          array_merge(
 *              $plugin_info,
 *              array(
 *                  'dashboard_widget' => true, // show dashboard widget (default: false)
 *                  'right_sidebar'    => true, // show adverts sidebar (default: false)
 *                  'notice'           => true, // show notice message (default: false)
 *              )
 *          )
 *      );
 *  }
 *
 * Example (in any place):
 *  <?php
 *      global $wbcr_PLUGIN_NAME_adinserter;
 *      echo $wbcr_PLUGIN_NAME_adinserter->get_adverts( 'right_sidebar' );
 *  ?>
 *
 * Replace in the variable the phrase PLUGIN_NAME with the current plugin name!
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Base {

	/*
	 * Contain array data with the plugin information and the module settings.
	 * Mainly used to get the name of the plugin and how to get the adverts blocks.
	 *
	 * @since 1.0.0 Added
	 *
	 * @var array   Example: array(
	 * 	    'prefix'                => 'wbcr_inp_',
	 *      'plugin_name'           => 'wbcr_insert_php',
	 *      'plugin_title'          => 'Woody ad snippets',
	 *      'plugin_text_domain'    => 'insert-php',
	 *      'dashboard_widget'      => true,
	 *      'right_sidebar'         => true,
	 *      'notice'                => true,
	 *      ...
	 * )
	 *
	 */
	private $data = [];

	/**
	 * Wbcr_Factory_Adinserter constructor.
	 *
	 * - Store plugin information and settings.
	 * - Add filter and actions.
	 * - Include dashboard widget.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param string $plugin_path   Path to plugin base file
	 * @param array  $data          Array data with plugin information and settings (@see $data property example)
	 */
	public function __construct( $plugin_path, $data ) {
		$this->data = $data;

		add_filter( 'wbcr/factory/pages/impressive/widgets', [ $this, 'register_widgets' ], 10, 3 );

		add_action( 'init', [ $this, 'add_notices' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_wbcr_advt_mark_notice', [ $this, 'mark_notice' ] );

		$this->include_dashboard();
	}

	/**
	 * Include dashboard widget
	 *
	 * Include functionality the output of the widget on the dashboard.
	 * Only one dashboard widget must be shown for some plugins with this setting (dashboard_widget).
	 *
	 * @since 1.0.0 Added
	 */
	private function include_dashboard() {
		if ( isset( $this->data['dashboard_widget'] ) && $this->data['dashboard_widget'] && ! defined( 'FACTORY_ADVERTS_DASHBOARD_WIDGET' ) ) {
			/**
			 * Dashboard widget is displays.
			 *
			 * Used only in this function.
			 *
			 * @since 1.0.0
			 * @var boolean Notes that the dashboard widget already displays.
			 */
			define( 'FACTORY_ADVERTS_DASHBOARD_WIDGET', true );
			require_once FACTORY_ADVERTS_102_DIR . '/includes/class-adverts-dashboard-widget.php';

			new Dashboard_Widget( $this->data['plugin_name'] );
		}
	}

	/**
	 * Get advert content for selected position.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param string $position   The position for advert
	 *
	 * @return string
	 */
	private function get_content( $position ) {
		$request = new Rest_Request( $this->data['plugin_name'], $position );

		return $request->get_content();
	}

	/**
	 * Register widgets.
	 *
	 * Depending on the settings, register new widgets.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param array  $widgets    Already existing registered widgets
	 * @param string $position   Position for the widget
	 * @param string $plugin     Plugin object for which the hook is run
	 *
	 * @return array array(
	 *  'adverts_widget'     => '<p></p>',
	 *  'businnes_suggetion' => '<p></p>',
	 *  'support'            => '<p></p>',
	 *  ...
	 * )
	 */
	public function register_widgets( $widgets, $position, $plugin ) {
		if ( $plugin->getPluginName() == $this->data['plugin_name'] && ! empty( $this->data ) && 'right' == $position ) {
			if ( isset( $this->data['right_sidebar'] ) && $this->data['right_sidebar'] ) {
				$content = $this->get_content( 'right_sidebar' );

				$widgets['adverts_widget'] = $content;
			}

			if ( isset( $this->data['businnes_suggetion'] ) && $this->data['businnes_suggetion'] ) {
				$content = $this->get_content( 'businnes_suggetion' );

				$widgets['businnes_suggetion'] = $content;
			}

			if ( isset( $this->data['support'] ) && $this->data['support'] ) {
				$content = $this->get_content( 'support' );

				$widgets['support'] = $content;
			}
		}

		return $widgets;
	}

	/**
	 * Add notice message.
	 *
	 * Only one notice must be shown for some plugins with this setting (notice).
	 *
	 * @since 1.0.0 Added
	 */
	public function add_notices() {
		if ( isset( $this->data['notice'] ) && $this->data['notice'] && ! defined( 'FACTORY_ADINSERTER_NOTICE' ) ) {
			/**
			 * Notice is displays.
			 *
			 * Used only in this function.
			 *
			 * @since 1.0.0
			 * @var boolean Notes that the notice already displays.
			 */
			define( 'FACTORY_ADINSERTER_NOTICE', true );

			$content = $this->get_content( 'notice' );
			$hash    = md5( $content );

			/* If the notice has not been closed by the user or the content of the notice has changed,
			   then we show the notice. */
			if ( ! get_option( 'wbcr-advt-notice-' . $this->data['plugin_name'] ) || get_option( 'wbcr-advt-notice-hash-' . $this->data['plugin_name'] ) != $hash ) {
				update_option( 'wbcr-advt-notice-' . $this->data['plugin_name'], false );
				update_option( 'wbcr-advt-notice-hash-' . $this->data['plugin_name'], $hash );
				add_action( 'admin_notices', function () {
					echo $this->get_content( 'notice' );
				} );
			}
		}
	}

	/**
	 * Add javascript file.
	 *
	 * File contains code for intercept the click event and post ajax request.
	 *
	 * @since 1.0.0 Added
	 */
	public function enqueue_scripts() {
		if ( isset( $this->data['notice'] ) && $this->data['notice'] && ! get_option( 'wbcr-advt-notice-' . $this->data['plugin_name'] ) ) {
			wp_enqueue_script( 'factory-adverts-notice', FACTORY_ADVERTS_102_URL . '/assets/js/script.js' );
		}
	}

	/**
	 * Mark notice closed for this plugin.
	 *
	 * Callback for ajax action. Execute when user close the notice.
	 *
	 * @since 1.0.0 Added
	 * @see   enqueue_scripts()
	 */
	public function mark_notice() {
		update_option( 'wbcr-advt-notice-' . $this->data['plugin_name'], true );
		exit();
	}

	/**
	 * Directly get advert content for selected position.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param string $position   Custom position name
	 *
	 * @return string
	 */
	public function get_adverts( $position ) {
		$content = '';

		if ( $position ) {
			$content = $this->get_content( $position );
		}

		return $content;
	}

}
