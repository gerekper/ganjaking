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
				'action__gm_custom_mobile_hamburger'        => 'gm_custom_mobile_hamburger',
				'action__gm_before_mobile_hamburger'        => 'gm_before_mobile_hamburger',
				'action__gm_after_mobile_hamburger'         => 'gm_after_mobile_hamburger',
				'action__gm_mobile_main_menu_top'           => 'gm_mobile_main_menu_top',
				'action__gm_mobile_main_menu_nav_first'     => 'gm_mobile_main_menu_nav_first',
				'action__gm_mobile_main_menu_nav_last'      => 'gm_mobile_main_menu_nav_last',
				'action__gm_mobile_after_main_menu_nav'     => 'gm_mobile_after_main_menu_nav',
				'action__gm_mobile_before_search_icon'      => 'gm_mobile_before_search_icon',
				'action__gm_mobile_before_minicart'         => 'gm_mobile_before_minicart',
				'action__gm_mobile_toolbar_end'             => 'gm_mobile_toolbar_end',
			);

			$wrappered_actions = array(
				'gm_before_mobile_hamburger',
				'gm_after_mobile_hamburger',
			);

			$settings = $styles->serialize( true, false, false, true );


			foreach ( $list as $setting_index => $action_name ) {
				if ( ! empty( $settings[ $setting_index ] ) ) {

					// Default wrapper must be Empty.
					$wrapper = array(
						'before' => '',
						'after'  => '',
					);

					// Check needle for wrapper.
					if ( in_array( $action_name, $wrappered_actions, true ) ) {
						$wrapper = array(
							'before' => '<div class="gm-action__' . esc_attr( $action_name ) . '">',
							'after'  => '</div>',
						);
					}

					// Prepare content.
					$action_content = wp_unslash( self::prepare_string_for_unslash( $settings[ $setting_index ] ) );

					// Wrap content.
					$groovyMenuActions['custom_preset'][ $action_name ][] = $wrapper['before'] . $action_content . $wrapper['after'];

					// Add action with content.
					add_action( $action_name, [ self::class, $action_name ], 10 );
				}
			}

			$groovyMenuSettings['_preset_shortcodes_added'] = true;
		}
	}


	/**
	 * Prepare string for unslash.
	 *
	 * @param $value string value for work with.
	 *
	 * @return string
	 */
	public static function prepare_string_for_unslash( $value ) {

		// list from www.json.org: (\b backspace, \f formfeed).
		$escapers     = array( "\\n", "\\r", "\\t", "\\f", "\\b" );
		$replacements = array( "\n", "\r", "\t", "\x08", "\x0c" );

		$result = str_replace( $escapers, $replacements, $value );

		return $result;
	}

	/**
	 * Compile menu block for actions from preset options
	 *
	 * @param \GroovyMenuStyle $styles
	 *
	 * @return void;
	 */
	public static function check_menu_block_for_actions( \GroovyMenuStyle $styles ) {
		global $groovyMenuSettings;
		global $groovyMenuActions;

		if ( ! isset( $groovyMenuSettings['_preset_mb_action_added'] ) || ! $groovyMenuSettings['_preset_mb_action_added'] ) {

			$list = array(
				'action__gm_before_main_header__custom_id' => 'gm_before_main_header',
				'action__gm_after_main_header__custom_id'  => 'gm_after_main_header',
			);

			$settings = $styles->serialize( true, false, false, true );

			foreach ( $list as $setting_index => $action_name ) {
				if ( ! empty( $settings[ $setting_index ] ) ) {
					$groovyMenuActions['custom_mb_actions'][ $action_name ][] = $settings[ $setting_index ];
					add_action( $action_name, [ self::class, $action_name ], 10 );
				}
			}

			$groovyMenuSettings['_preset_mb_action_added'] = true;
		}
	}


	/**
	 * Compile toolbar menu from preset options
	 *
	 * @param \GroovyMenuStyle $styles
	 *
	 * @return void;
	 */
	public static function check_toolbar_menu( \GroovyMenuStyle $styles ) {
		global $groovyMenuSettings;

		$allowed_actions_list = array(
			'gm_toolbar_left_first',
			'gm_toolbar_left_last',
			'gm_toolbar_right_first',
			'gm_toolbar_right_last',
		);

		if ( ! isset( $groovyMenuSettings['_preset_toolbar_menu_added'] ) || ! $groovyMenuSettings['_preset_toolbar_menu_added'] ) {

			$settings = $styles->serialize( true, false, false, true );

			$toolbar_menu_position = empty( $settings['toolbar_menu_position'] ) ? '' : $settings['toolbar_menu_position'];
			$toolbar_menu_id       = empty( $settings['toolbar_menu_id'] ) ? 0 : intval( $settings['toolbar_menu_id'] );

			if ( ! in_array( $toolbar_menu_position, $allowed_actions_list, true ) || $toolbar_menu_id < 1 ) {
				return;
			}

			add_action( $toolbar_menu_position, [ self::class, 'show_toolbar_menu' ], 10 );

			$groovyMenuSettings['_preset_toolbar_menu_added'] = $toolbar_menu_id;
		}
	}


	/**
	 * Compile shortcodes for action
	 */
	public static function show_toolbar_menu() {
		global $groovyMenuSettings;

		$toolbar_menu_id = empty( $groovyMenuSettings['_preset_toolbar_menu_added'] ) ? 0 : intval( $groovyMenuSettings['_preset_toolbar_menu_added'] );

		if ( $toolbar_menu_id < 0 ) {
			return;
		}

		$args = array(
			'theme_location'  => 'gm_toolbar_menu',
			'menu'            => $toolbar_menu_id,
			'container'       => 'div',
			'container_class' => 'gm-toolbar-nav-container',
			'container_id'    => '',
			'menu_class'      => 'gm-toolbar-nav',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => 'wp_page_menu',
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'depth'           => 0,
			'walker'          => '',
		);

		wp_nav_menu( $args );
	}


	/**
	 * Compile shortcodes and menu block for action
	 */
	public static function __callStatic( $method, $arguments ) {
		global $groovyMenuActions;
		if ( ! empty( $groovyMenuActions['custom_preset'][ $method ] ) ) {
			foreach ( $groovyMenuActions['custom_preset'][ $method ] as $action_content ) {
				echo do_shortcode( $action_content );
			}
		}

		if ( ! empty( $groovyMenuActions['custom_mb_actions'][ $method ] ) ) {
			foreach ( $groovyMenuActions['custom_mb_actions'][ $method ] as $mm_id ) {
				$menu_block_helper = new \GroovyMenu\WalkerNavMenu();

				echo $menu_block_helper->getMenuBlockPostContent( $mm_id );

				if ( function_exists( 'groovy_menu_add_custom_styles' ) ) {
					groovy_menu_add_custom_styles( $mm_id );
				}
				if ( function_exists( 'groovy_menu_add_custom_styles_support' ) ) {
					groovy_menu_add_custom_styles_support( $mm_id );
				}
			}
		}
	}


}
