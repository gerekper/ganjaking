<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuActions
 */
class GroovyMenuActions {

	/**
	 * Compile shortcodes from preset options
	 *
	 * @param \GroovyMenuStyle $styles
	 */
	public static function do_preset_shortcodes( \GroovyMenuStyle $styles ) {
		global $groovyMenuSettings;
		global $groovyMenuActions;

		if ( ! isset( $groovyMenuSettings['_preset_shortcodes_added'] ) || ! $groovyMenuSettings['_preset_shortcodes_added'] ) {

			$list = array(
				'action__gm_toolbar_left_first'             => 'gm_toolbar_left_first',
				'action__gm_toolbar_left_last'              => 'gm_toolbar_left_last',
				'action__gm_toolbar_right_first'            => 'gm_toolbar_right_first',
				'action__gm_toolbar_right_last'             => 'gm_toolbar_right_last',
				'action__gm_before_logo'                    => 'gm_before_logo',
				'action__gm_after_logo'                     => 'gm_after_logo',
				'action__gm_before_main_header'             => 'gm_before_main_header',
				'action__gm_after_main_header'              => 'gm_after_main_header',
				'action__gm_after_main_menu_nav'            => 'gm_after_main_menu_nav',
				'action__gm_main_menu_nav_first'            => 'gm_main_menu_nav_first',
				'action__gm_main_menu_nav_last'             => 'gm_main_menu_nav_last',
				'action__gm_main_menu_actions_button_first' => 'gm_main_menu_actions_button_first',
				'action__gm_main_menu_actions_button_last'  => 'gm_main_menu_actions_button_last',
				'action__gm_mobile_main_menu_top'           => 'gm_mobile_main_menu_top',
				'action__gm_mobile_main_menu_nav_first'     => 'gm_mobile_main_menu_nav_first',
				'action__gm_mobile_main_menu_nav_last'      => 'gm_mobile_main_menu_nav_last',
				'action__gm_mobile_after_main_menu_nav'     => 'gm_mobile_after_main_menu_nav',
				'action__gm_mobile_before_search_icon'      => 'gm_mobile_before_search_icon',
				'action__gm_mobile_before_minicart'         => 'gm_mobile_before_minicart',
				'action__gm_mobile_toolbar_end'             => 'gm_mobile_toolbar_end',
			);

			$settings = $styles->serialize( true, false, false, true );


			foreach ( $list as $setting_index => $action_name ) {
				if ( ! empty( $settings[ $setting_index ] ) ) {
					$groovyMenuActions['custom_preset'][ $action_name ][] = wp_unslash( $settings[ $setting_index ] );
					add_action( $action_name, [ self::class, $action_name ], 10 );
				}
			}

			$groovyMenuSettings['_preset_shortcodes_added'] = true;
		}
	}


	/**
	 * Compile shortcodes for action
	 */
	public static function __callStatic( $method, $arguments ) {
		global $groovyMenuActions;
		if ( ! empty( $groovyMenuActions['custom_preset'][ $method ] ) ) {
			foreach ( $groovyMenuActions['custom_preset'][ $method ] as $action_content ) {
				echo do_shortcode( $action_content );
			}
		}
	}


}
