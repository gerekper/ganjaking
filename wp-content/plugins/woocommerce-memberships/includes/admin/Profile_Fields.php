<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Admin;

use SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields\Edit_Screen;
use SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields\List_Screen;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The main handler for the profile fields admin component and UI.
 *
 * @since 1.19.0
 */
class Profile_Fields {


	/** @var string the add new profile field action action identifier */
	const SCREEN_ACTION_NEW = 'new';

	/** @var string the edit profile field screen action identifier */
	const SCREEN_ACTION_EDIT = 'edit';

	/** @var string the delete profile field screen action identifier */
	const SCREEN_ACTION_DELETE = 'delete';


	/** @var null|Profile_Field_Definition the profile field definition in context, if any */
	private $profile_field_definition;


	/**
	 * Profile fields admin handler constructor.
	 *
	 * @since 1.19.0
	 */
	public function __construct() {

		require_once( wc_memberships()->get_plugin_path() . '/includes/admin/Views/Meta_Boxes/Profile_Field/Meta_Box.php' );
		require_once( wc_memberships()->get_plugin_path() . '/includes/admin/Profile_Fields/Edit_Screen.php' );
		require_once( wc_memberships()->get_plugin_path() . '/includes/admin/Profile_Fields/List_Screen.php' );
		require_once( wc_memberships()->get_plugin_path() . '/includes/admin/Profile_Fields/List_Table.php' );

		// make sure that the Memberships menu item is set to currently active
		add_filter( 'parent_file', [ $this, 'set_current_admin_menu_item' ] );

		// set the admin page title
		add_filter( 'admin_title', [ $this, 'set_admin_page_title' ] );

		// render profile field admin screens content
		add_action( 'wc_memberships_render_profile_fields_page', [ $this, 'render_screen' ] );
	}


	/**
	 * Determines whether the current screen is a profile fields screen.
	 *
	 * @since 1.19.0
	 *
	 * @param string|string[] $which the current action or possible actions (empty implies list screen)
	 * @return bool
	 */
	public function is_profile_fields_screen( $which = '' ) {

		$is_screen = false;

		if ( wc_memberships()->get_admin_instance()->is_memberships_profile_fields_admin_screen() ) {

			if ( '' === $which ) {
				$is_screen = true;
			} elseif ( is_string( $which ) && $which === $this->get_admin_screen_action() ) {
				$is_screen = true;
			} elseif ( is_array( $which ) && in_array( $this->get_admin_screen_action(), $which, true ) ) {
				$is_screen = true;
			}
		}

		return $is_screen;
	}


	/**
	 * Gets the admin action from the current screen request.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_admin_screen_action() {

		$action = isset( $_GET['action'] ) ? $_GET['action'] : '';

		return in_array( $action, [ self::SCREEN_ACTION_NEW, self::SCREEN_ACTION_EDIT, self::SCREEN_ACTION_DELETE ], true ) && $this->is_profile_fields_screen() ? $action : '';
	}


	/**
	 * Gets a profile field definition object from the current screen request.
	 *
	 * @since 1.19.0
	 *
	 * @return Profile_Field_Definition|null
	 */
	public function get_admin_screen_profile_field_definition() {

		if ( null === $this->profile_field_definition && isset( $_GET['profile_field'] ) && ( $profile_field_id = trim( (string) $_GET['profile_field'] ) ) ) {

			foreach ( \SkyVerge\WooCommerce\Memberships\Profile_Fields::get_profile_field_definitions() as $profile_field_definition ) {

				if ( $profile_field_id === $profile_field_definition->get_id() ) {

					$this->profile_field_definition = $profile_field_definition;
					break;
				}
			}
		}

		return $this->profile_field_definition;
	}


	/**
	 * Determines whether the current request is for creating a new profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_new_profile_field_definition_screen() {

		return self::SCREEN_ACTION_NEW === $this->get_admin_screen_action() && null === $this->get_admin_screen_profile_field_definition();
	}


	/**
	 * Determines whether the current request is for editing an existing profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_edit_profile_field_definition_screen() {

		return self::SCREEN_ACTION_EDIT === $this->get_admin_screen_action() && null !== $this->get_admin_screen_profile_field_definition();
	}


	/**
	 * Determines whether the current request is for deleting an existing profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @return bool
	 */
	public function is_delete_profile_field_definition_screen() {

		return self::SCREEN_ACTION_DELETE === $this->get_admin_screen_action() && null !== $this->get_admin_screen_profile_field_definition();
	}


	/**
	 * Gets the admin screen URL for listing profile field definitions.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_profile_field_definitions_list_screen_url() {

		return add_query_arg( [
			'page' => 'wc_memberships_profile_fields',
		], admin_url( 'admin.php' ) );
	}


	/**
	 * Gets the admin screen URL for creating a new profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @return string
	 */
	public function get_new_profile_field_definition_screen_url() {

		return add_query_arg( [
			'action' => self::SCREEN_ACTION_NEW,
		], $this->get_profile_field_definitions_list_screen_url() );
	}


	/**
	 * Gets the admin screen URL for editing a profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @return string
	 */
	public function get_edit_profile_field_definition_screen_url( Profile_Field_Definition $profile_field_definition ) {

		return add_query_arg( [
			'action'        => self::SCREEN_ACTION_EDIT,
			'profile_field' => $profile_field_definition->get_id(),
		], $this->get_profile_field_definitions_list_screen_url() );
	}


	/**
	 * Gets the admin screen URL for deleting a profile field definition.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @return string
	 */
	public function get_delete_add_on_url( Profile_Field_Definition $profile_field_definition ) {

		return add_query_arg( [
			'action'        => self::SCREEN_ACTION_DELETE,
			'profile_field' => $profile_field_definition->get_id(),
			'security'      => wp_create_nonce( 'delete_profile_field_definition_' . str_replace( '-', '_', $profile_field_definition->get_id() ) ),
		], $this->get_profile_field_definitions_list_screen_url() );
	}


	/**
	 * Sets the Memberships admin menu item as active while viewing the Profile Fields admin screens.
	 *
	 * @internal
	 * @see \WC_Memberships_Admin_Import_Export_Handler::set_current_admin_menu_item()
	 *
	 * @since 1.19.0
	 *
	 * @param string $parent_file
	 * @return string
	 */
	public function set_current_admin_menu_item( $parent_file ) {
		global $menu, $submenu_file;

		if ( isset( $_GET['page'] ) && 'wc_memberships_profile_fields' === $_GET['page'] ) {

			$submenu_file = 'edit.php?post_type=wc_user_membership';

			if ( ! empty( $menu ) ) {

				foreach ( $menu as $key => $value ) {

					if ( isset( $value[2], $menu[ $key ][4] ) && 'woocommerce' === $value[2] ) {
						$menu[ $key ][4] .= ' wp-has-current-submenu wp-menu-open';
					}
				}
			}
		}

		return $parent_file;
	}


	/**
	 * Sets the admin page title for profile field admin screens.
	 *
	 * @internal
	 * @see \WC_Memberships_Admin_Import_Export_Handler::set_admin_page_title()
	 *
	 * @since 1.19.0
	 *
	 * @param string $admin_title the page title, with extra context added
	 * @return string
	 */
	public function set_admin_page_title( $admin_title ) {

		if ( $this->is_profile_fields_screen() ) {

			switch ( $this->get_admin_screen_action() ) {

				case self::SCREEN_ACTION_NEW :
					$admin_title = __( 'Add New Member Profile Field', 'woocommerce-memberships' ) . ' ' . $admin_title;
				break;

				case self::SCREEN_ACTION_EDIT :
					$admin_title = __( 'Edit Member Profile Field', 'woocommerce-memberships' ) . ' ' . $admin_title;
				break;

				default :
					$admin_title = __( 'Member Profile Fields', 'woocommerce-memberships' ) . ' ' . $admin_title;
				break;
			}
		}

		return $admin_title;
	}


	/**
	 * Renders HTML content of the profile fields admin screens.
	 *
	 * @internal
	 *
	 * @since 1.19.0
	 */
	public function render_screen() {

		if ( ! $this->is_profile_fields_screen() ) {
			return;
		}

		if ( $this->is_new_profile_field_definition_screen() || $this->is_edit_profile_field_definition_screen() ) {
			$screen = new Edit_Screen( $this );
		} else {
			$screen = new List_Screen( $this );
		}

		$screen->render();
	}


}
