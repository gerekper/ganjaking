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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field_Definition;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;
use \SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field_Definition;

defined( 'ABSPATH' ) or exit;

/**
 * Blocks handler for the Gutenberg editor.
 *
 * @since 1.19.0
 */
class Option implements \WC_Object_Data_Store_Interface {


	/** @var string the name of the option used to store definitions */
	const OPTION_NAME = 'wc_memberships_profile_fields';


	/**
	 * Creates a profile field definition in database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function create( &$profile_field_definition ) {

		if ( ! $profile_field_definition instanceof Profile_Field_Definition ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field definition.', 'woocommerce-memberships' ) );
		}

		if ( empty( $profile_field_definition->get_id() ) ) {
			$profile_field_definition->set_id( uniqid( '', false ) );
		}

		$this->add( $profile_field_definition );

		/**
		 * Fires when a profile field definition is created in database.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field_Definition $profile_field_definition object
		 */
		do_action( 'wc_memberships_create_profile_field_definition', $profile_field_definition );
	}


	/**
	 * Reads a profile field definition from database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function read( &$profile_field_definition ) {

		if ( ! $profile_field_definition instanceof Profile_Field_Definition ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field definition.', 'woocommerce-memberships' ) );
		}

		$profile_field_definitions = $this->get_profile_field_definitions_data();
		$profile_field_slug        = $profile_field_definition->get_slug( 'edit' );

		if ( ! isset( $profile_field_definitions[ $profile_field_slug ] ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Profile field definition not found.', 'woocommerce-memberships' ) );
		}

		$data = $profile_field_definitions[ $profile_field_slug ];

		if ( empty( $data['id'] ) ) {
			$data['id'] = uniqid( '', false );
		}

		$profile_field_definition->set_defaults();
		$profile_field_definition->set_id( $data['id'] );
		$profile_field_definition->set_props( $data );
		$profile_field_definition->set_object_read( true );
	}


	/**
	 * Updates a profile field definition in database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function update( &$profile_field_definition ) {

		if ( ! $profile_field_definition instanceof Profile_Field_Definition ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field definition.', 'woocommerce-memberships' ) );
		}

		if ( empty( $profile_field_definition->get_id() ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Profile field definition ID invalid or not found while updating.', 'woocommerce-memberships' ) );
		}

		$this->add( $profile_field_definition );

		/**
		 * Fires when a profile field definition is updated in database.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field_Definition $profile_field_definition object
		 */
		do_action( 'wc_memberships_update_profile_field_definition', $profile_field_definition );
	}


	/**
	 * Deletes a profile field definition from database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @param array $args unused (implemented from interface)
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function delete( &$profile_field_definition, $args = [] ) {

		if ( ! $profile_field_definition instanceof Profile_Field_Definition ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field definition.', 'woocommerce-memberships' ) );
		}

		$profile_field_definitions = $this->get_profile_field_definitions_data();

		if ( isset( $profile_field_definitions[ $profile_field_definition->get_slug() ] )) {

			unset( $profile_field_definitions[ $profile_field_definition->get_slug() ] );

			/**
			 * Fires when a profile field definition is delete from database.
			 *
			 * @since 1.19.0
			 *
			 * @param Profile_Field_Definition $profile_field_definition object
			 */
			do_action( 'wc_memberships_delete_profile_field_definition', $profile_field_definition );

			$this->store( $profile_field_definitions );
		}
	}


	/**
	 * Gets the profile field definitions raw data.
	 *
	 * @since 1.19.0
	 *
	 * @return array
	 */
	public function get_profile_field_definitions_data() {

		return get_option( self::OPTION_NAME, [] );
	}


	/**
	 * Gets profile field definition array data.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition object
	 * @return array
	 */
	private function get_profile_field_definition_data( Profile_Field_Definition $profile_field_definition ) {

		return [
			'id'                  => $profile_field_definition->get_id(),
			'slug'                => $profile_field_definition->get_slug( 'edit' ),
			'name'                => $profile_field_definition->get_name( 'edit' ),
			'type'                => $profile_field_definition->get_type( 'edit' ),
			'label'               => $profile_field_definition->get_label( 'edit' ),
			'description'         => $profile_field_definition->get_description( 'edit' ),
			'editable_by'         => $profile_field_definition->get_editable_by( 'edit' ),
			'visibility'          => $profile_field_definition->get_visibility( 'edit' ),
			'required'            => $profile_field_definition->get_required( 'edit' ),
			'default_value'       => $profile_field_definition->get_default_value( 'edit' ),
			'options'             => $profile_field_definition->get_options( 'edit' ),
			'membership_plan_ids' => $profile_field_definition->get_membership_plan_ids( 'edit' ),
		];
	}


	/**
	 * Adds a profile field definition to the database option.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field_Definition $profile_field_definition
	 */
	private function add( Profile_Field_Definition $profile_field_definition ) {

		$profile_field_definitions     = $this->get_profile_field_definitions_data();
		$new_profile_field_data        = $this->get_profile_field_definition_data( $profile_field_definition );		$updated_old_definition    = false;
		$new_profile_field_definitions = [];
		$updated_old_definition        = false;

		// prevents duplicates when the slug is being updated by overwriting old data
		foreach ( $profile_field_definitions as $old_profile_field_slug => $old_profile_field_definition ) {

			if ( isset( $old_profile_field_definition['id'] ) && $profile_field_definition->get_id() === $old_profile_field_definition['id'] ) {

				$new_profile_field_definitions[ $profile_field_definition->get_slug( 'edit' ) ] = $new_profile_field_data;

				$updated_old_definition = true;

			} else {

				$new_profile_field_definitions[ $old_profile_field_slug ] = $old_profile_field_definition;
			}
		}

		// otherwise, append at the end of the fields array
		if ( $updated_old_definition ) {
			$profile_field_definitions = $new_profile_field_definitions;
		} else {
			$profile_field_definitions[ $profile_field_definition->get_slug() ] = $new_profile_field_data;
		}

		$this->store( $profile_field_definitions );
	}


	/**
	 * Stores an array of profile field definitions data.
	 *
	 * @since 1.19.0-dev,1
	 *
	 * @param array $profile_field_definitions array of raw profile field definition items
	 */
	private function store( array $profile_field_definitions ) {

		update_option( self::OPTION_NAME, $profile_field_definitions );
	}


	/**
	 * Sorts the profile field definitions.
	 *
	 * @since 1.19.0
	 *
	 * @param string[] $profile_field_definition_slugs
	 */
	public function sort( array $profile_field_definition_slugs ) {

		$profile_field_definitions     = $this->get_profile_field_definitions_data();
		$new_profile_field_definitions = [];

		foreach ( $profile_field_definition_slugs as $slug ) {

			if ( isset( $profile_field_definitions[ $slug ] ) ) {

				$new_profile_field_definitions[ $slug ] = $profile_field_definitions[ $slug ];

				unset( $profile_field_definitions[ $slug ] );
			}
		}

		$this->store( array_merge( $new_profile_field_definitions, $profile_field_definitions ) );
	}


	/**
	 * Reads meta data (interface method).
	 *
	 * No op: profile field definitions have no meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 */
	public function read_meta( &$data ) { }


	/**
	 * Updates meta data (interface method).
	 *
	 * No op: profile field definitions have no meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 * @param object $meta
	 */
	public function delete_meta( &$data, $meta ) { }


	/**
	 * Adds meta data (interface method).
	 *
	 * No op: profile field definitions have no meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 * @param object $meta
	 */
	public function add_meta( &$data, $meta ) {	}


	/**
	 * Updates meta data (interface method).
	 *
	 * No op: profile field definitions have no meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 * @param object $meta
	 */
	public function update_meta( &$data, $meta ) { }


}
