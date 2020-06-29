<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Roles') ) :

/**
 *
 */
class Mega_Menu_Roles {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'megamenu_tabs', array( $this, 'add_roles_tab'), 10, 5 );
		add_filter( 'megamenu_nav_menu_objects_before', array( $this, 'apply_roles_to_menu_items'), 10, 2);

	}


	/**
	 * Go through menu items and strip out any that dont match our current role or logged in status
	 *
	 * @param array $items (before widgets have been added)
	 * @param array $args
	 */
	public function apply_roles_to_menu_items( $items, $args ) {

		$hidden_items = array();
		$hidden_items_parents = array();

		foreach ( $items as $key => $item ) {

			// remove children of hidden items
			if ( in_array( $item->menu_item_parent, $hidden_items ) ) {
				$hidden_items[] = $item->ID;
				$hidden_items_parents[] = $item->menu_item_parent;
				unset( $items[$key] );
				continue;
			}

			if ( isset( $item->megamenu_settings['roles'] ) ) {

				// disabled
				if ( $item->megamenu_settings['roles']['display_mode'] == 'disabled' ) {
					continue;
				}

				// check logged in
				if ( $item->megamenu_settings['roles']['display_mode'] == 'logged_in' && ! is_user_logged_in() ) {
					$hidden_items[] = $item->ID;
					$hidden_items_parents[] = $item->menu_item_parent;
					unset( $items[$key] );
					continue;
				}

				// check logged out
				if ( $item->megamenu_settings['roles']['display_mode'] == 'logged_out' && is_user_logged_in() ) {
					$hidden_items[] = $item->ID;
					$hidden_items_parents[] = $item->menu_item_parent;
					unset( $items[$key] );
					continue;
				}

				// check roles
				if ( $item->megamenu_settings['roles']['display_mode'] == 'by_role' && isset($item->megamenu_settings['roles']['roles']) && is_array( $item->megamenu_settings['roles']['roles'] ) ) {

					$user_has_a_valid_role = false;

					foreach ( $item->megamenu_settings['roles']['roles'] as $role ) {
						if ( current_user_can( $role ) ) {
							$user_has_a_valid_role = true;
						}
					}

					if ( ! $user_has_a_valid_role ) {
						$hidden_items[] = $item->ID;
						$hidden_items_parents[] = $item->menu_item_parent;
						unset( $items[$key] );
						continue;
					}

				}

			}

		}

		foreach ( $items as $key => $item ) {
			if ( in_array( $item->ID, $hidden_items_parents ) ) {
				if ( ! $this->menu_item_has_children( $item->ID, $items ) ) {
					// unset menu-item-has-children class
					$item->classes = array_diff( $item->classes, array( 'menu-item-has-children' ) );
				}
			}
		}

		return $items;

	}


	/**
	 * Ensure a menu item still has children assigned to it in the menu structure.
	 *
	 * @param int $item_id
	 * @param array $items
	 */
	public function menu_item_has_children( $item_id, $items ) {

		foreach ( $items as $key => $item ) {
			if ( $item->menu_item_parent == $item_id ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Add the roles tab to the menu item options
	 *
	 * @since 1.0
	 * @param array $tabs
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function add_roles_tab( $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$display_mode = isset( $menu_item_meta['roles']['display_mode'] ) ? $menu_item_meta['roles']['display_mode'] : 'disabled';
		$checked_roles = isset( $menu_item_meta['roles']['roles'] ) ? $menu_item_meta['roles']['roles'] : array();
		$disabled = $display_mode != 'by_role' ? 'disabled=disabled' : '';

		$html  = "<form id='mm_roles'>";
		$html .= "    <input type='hidden' name='_wpnonce' value='" . wp_create_nonce('megamenu_edit') . "' />";
		$html .= "    <input type='hidden' name='menu_item_id' value='{$menu_item_id}' />";
		$html .= "    <input type='hidden' name='action' value='mm_save_menu_item_settings' />";
		$html .= "    <h4 class='first'>" . __("Roles & Restrictions", "megamenupro") . "</h4>";
		$html .= "    <p class='tab-description'>" . __("Restrict the display of this menu item to selected user roles", "megamenu_pro") . "</p>";
		$html .= "    <table>";
		$html .= "        <tr>";
		$html .= "            <td class='mega-name'>" . __("Display Mode", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <select name='settings[roles][display_mode]'>";
		$html .= "                    <option value='disabled' " . selected( $display_mode, 'disabled', false ) . ">" . __("All Users", "megamenupro") . "</option>";
		$html .= "                    <option value='logged_out' " . selected( $display_mode, 'logged_out', false ) . ">" . __("Logged Out Users", "megamenupro") . "</option>";
		$html .= "                    <option value='logged_in' " . selected( $display_mode, 'logged_in', false ) . ">" . __("Logged In Users", "megamenupro") . "</option>";
		$html .= "                    <option value='by_role' " . selected( $display_mode, 'by_role', false ) . ">" . __("By Role", "megamenupro") . "</option>";
		$html .= "                </select>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr>";
		$html .= "            <td class='mega-name'>" . __("Role", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .= "                <ul>";

		$editable_roles = $this->get_editable_roles();

		if ( count( $editable_roles ) ) {
			foreach ( $editable_roles as $key => $value ) {
				$html .= "<li><label><input name='settings[roles][roles][]' type='checkbox' value='" . $key . "' " . checked( in_array( $key, $checked_roles ), true, false ) . " " . $disabled . "/>" . $value['name'] . "</label></li>";
			}
		}

		$html .= "                </ul>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "    </table>";
		$html .= get_submit_button();
		$html .= "</form>";

		$tabs['roles'] = array(
			'title' => __("Roles", "megamenupro"),
			'content' => $html
		);

		return $tabs;
	}


	/**
	 *
	 * @since 1.0
	 */
	public function get_editable_roles() {
	    global $wp_roles;

	    $all_roles = $wp_roles->roles;
	    $editable_roles = apply_filters('editable_roles', $all_roles);

	    return $editable_roles;
	}


}

endif;