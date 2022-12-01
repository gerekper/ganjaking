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

namespace SkyVerge\WooCommerce\Memberships\Data_Stores\Profile_Field;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;
use SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field;

defined( 'ABSPATH' ) or exit;

/**
 * User meta data store for profile fields.
 *
 * @since 1.19.0
 */
class User_Meta implements \WC_Object_Data_Store_Interface {


	/** @var string user meta key prefix */
	protected $meta_key_prefix = '_wc_memberships_profile_field_';


	/**
	 * Parses the user meta key from a profile field object.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field $profile_field object
	 * @return string
	 */
	public function get_meta_key( Profile_Field $profile_field ) {

		return $this->meta_key_prefix . str_replace( '-', '_', $profile_field->get_slug() );
	}


	/**
	 * Creates a profile field in database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field $profile_field object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function create( &$profile_field ) {

		if ( ! $profile_field instanceof Profile_Field ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field.', 'woocommerce-memberships' ) );
		}

		$value = $profile_field->get_value( 'edit' );

		add_user_meta( $profile_field->get_user_id( 'edit' ), $this->get_meta_key( $profile_field ), is_bool( $value ) ? wc_bool_to_string( $value ) : $value );

		/**
		 * Fires when a profile field object is created in database.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field $profile_field object
		 */
		do_action( 'wc_memberships_create_profile_field', $profile_field );
	}


	/**
	 * Reads a profile field from database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field $profile_field object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function read( &$profile_field ) {

		if ( ! $profile_field instanceof Profile_Field ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field.', 'woocommerce-memberships' ) );
		}

		$user_id  = $profile_field->get_user_id( 'edit' );
		$slug     = $profile_field->get_slug();
		$meta_key = '' !== $slug ? $this->get_meta_key( $profile_field ) : '';

		if ( empty( $user_id ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field user ID.', 'woocommerce-memberships' ) );
		}

		if ( empty( $meta_key ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field slug.', 'woocommerce-memberships' ) );
		}

		if ( ! metadata_exists( 'user', $user_id, $meta_key ) ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Profile field not found.', 'woocommerce-memberships' ) );
		}

		$value = get_user_meta( $user_id, $meta_key, true );

		$profile_field->set_defaults();
		$profile_field->set_props( [
			'user_id' => $user_id,
			'slug'    => $slug,
			'value'   => $value || is_numeric( $value ) || is_array( $value ) ? $value : '',
		] );
		$profile_field->set_object_read( true );
	}


	/**
	 * Updates a profile field in database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field $profile_field object
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function update( &$profile_field ) {

		if ( ! $profile_field instanceof Profile_Field ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field.', 'woocommerce-memberships' ) );
		}

		$value = $profile_field->get_value( 'edit' );

		update_user_meta( $profile_field->get_user_id( 'edit' ), $this->get_meta_key( $profile_field ), is_bool( $value ) ? wc_bool_to_string( $value ) : $value );

		/**
		 * Fires when a profile field object is updated in database.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field $profile_field object
		 */
		do_action( 'wc_memberships_update_profile_field', $profile_field );
	}


	/**
	 * Deletes a profile field from database.
	 *
	 * @since 1.19.0
	 *
	 * @param Profile_Field $profile_field object
	 * @param array $args unused (from interface)
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function delete( &$profile_field, $args = [] ) {

		if ( ! $profile_field instanceof Profile_Field ) {
			throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid profile field.', 'woocommerce-memberships' ) );
		}

		delete_user_meta( $profile_field->get_user_id( 'edit' ), $this->get_meta_key( $profile_field ) );

		/**
		 * Fires when a profile field object is deleted from database.
		 *
		 * @since 1.19.0
		 *
		 * @param Profile_Field $profile_field object
		 */
		do_action( 'wc_memberships_delete_profile_field', $profile_field );
	}


	/**
	 * Reads meta data (interface method).
	 *
	 * No op: profile fields have no user meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 */
	public function read_meta( &$data ) { }


	/**
	 * Updates meta data (interface method).
	 *
	 * No op: profile fields have no user meta at this time.
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
	 * No op: profile fields have no user meta at this time.
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
	 * No op: profile fields have no user meta at this time.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Data $data
	 * @param object $meta
	 */
	public function update_meta( &$data, $meta ) { }


}
