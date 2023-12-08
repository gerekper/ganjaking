<?php
/**
 * Extra Product Options Widgets
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Widgets
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_Widgets {

	/**
	 * Register all of the plugin widgets
	 *
	 * @return void
	 * @since 4.8
	 */
	public static function register() {
		require_once THEMECOMPLETE_EPO_INCLUDES_PATH . 'widgets/class-themecomplete-epo-widget-action.php';
		register_widget( 'THEMECOMPLETE_EPO_Widget_Action' );
	}
}
