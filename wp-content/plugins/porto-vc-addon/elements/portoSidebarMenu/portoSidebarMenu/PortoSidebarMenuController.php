<?php
namespace porto\portoBlock;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
class PortoSidebarMenuController extends Container implements Module {
	use EventsFilters;

	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_SIDEBAR_MENU_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoSidebarMenu',
				'getMenus'
			);
			define( 'VCV_PORTO_SIDEBAR_MENU_CONTROLLER', true );
		}
	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getMenus( $variables, $payload ) {
		if ( function_exists( 'porto_get_post_type_items' ) ) {
			$custom_menus = array(
				array(
					'label' => '',
					'value' => '',
				),
			);
			$menus        = get_terms(
				array(
					'taxonomy'   => 'nav_menu',
					'hide_empty' => false,
				)
			);
			if ( is_array( $menus ) && ! empty( $menus ) ) {
				foreach ( $menus as $single_menu ) {
					if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
						$custom_menus[] = array(
							'label' => str_replace( '&amp;', '&', esc_js( $single_menu->name ) ),
							'value' => (int) $single_menu->term_id,
						);
					}
				}
				$variables[] = array(
					'key'   => 'portoMenus',
					'value' => $custom_menus,
				);
			}
		}

		return $variables;
	}
}
