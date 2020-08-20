<?php
/**
 * Extra Product Options Widgets
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Widgets {

	/**
	 * Register all of the plugin widgets
	 *
	 * @since 4.8
	 */
	public static function register() {

		require_once THEMECOMPLETE_EPO_PLUGIN_PATH . '/include/widgets/class-tm-epo-widget-action.php';

		register_widget( 'THEMECOMPLETE_EPO_Widget_Action' );

	}

}
