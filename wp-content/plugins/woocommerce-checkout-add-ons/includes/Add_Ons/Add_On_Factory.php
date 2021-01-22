<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;


/**
 * Add-On Factory Class
 *
 * @since 2.0.0
 */
class Add_On_Factory {


	/** @var string namespace for built-in add-ons */
	const ADD_ON_NAMESPACE = 'SkyVerge\\WooCommerce\\Checkout_Add_Ons\\Add_Ons\\';


	/**
	 * Gets the registered add-on types and their translated front-end values.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_add_on_types() {

		/**
		 * Filters the valid add-on types.
		 *
		 * @since 1.0
		 *
		 * @param array $add_on_types The valid add-on types.
		 */
		return apply_filters( 'wc_checkout_add_ons_add_on_types', array(
			'text'          => __( 'Text', 'woocommerce-checkout-add-ons' ),
			'textarea'      => __( 'Text Area', 'woocommerce-checkout-add-ons' ),
			'select'        => __( 'Select', 'woocommerce-checkout-add-ons' ),
			'multiselect'   => __( 'Multiselect', 'woocommerce-checkout-add-ons' ),
			'radio'         => __( 'Radio', 'woocommerce-checkout-add-ons' ),
			'checkbox'      => __( 'Checkbox', 'woocommerce-checkout-add-ons' ),
			'multicheckbox' => __( 'Multi-checkbox', 'woocommerce-checkout-add-ons' ),
			'file'          => __( 'File', 'woocommerce-checkout-add-ons' ),
		) );
	}


	/**
	 * Gets the registered add-on classnames.
	 *
	 * @since 2.0.1
	 *
	 * @return array
	 */
	public static function get_add_on_classnames() {

		/**
		 * Filters the classnames for each add-on type.
		 *
		 * @since 2.0.1
		 *
		 * @param array $add_on_classnames the add-on classnames
		 */
		return apply_filters( 'wc_checkout_add_ons_add_on_classnames', array(
			'text'          => 'Text',
			'textarea'      => 'Text_Area',
			'select'        => 'Select',
			'multiselect'   => 'Multiselect',
			'radio'         => 'Radio',
			'checkbox'      => 'Checkbox',
			'multicheckbox' => 'Multi_Checkbox',
			'file'          => 'File',
		) );
	}


	/**
	 * Gets the add-on types that support options.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_add_on_types_with_options() {

		$add_on_types              = array_keys( self::get_add_on_types() );
		$add_on_types_with_options = array();

		foreach ( $add_on_types as $add_on_type ) {

			$classname = self::get_add_on_classname( $add_on_type );

			if ( class_exists( $classname ) ) {

				$class_parents = class_parents( $classname );

				// check if the class inherits the `Add_On_With_Options` class
				if ( is_array( $class_parents ) && in_array( self::ADD_ON_NAMESPACE . 'Add_On_With_Options', $class_parents, true ) ) {

					$add_on_types_with_options[ $add_on_type ] = array(
						'multiple_defaults' => $classname::has_multiple_defaults()
					);
				}
			}
		}

		return $add_on_types_with_options;
	}


	/**
	 * Gets an array of add-on types with their supported attributes.
	 *
	 * @since 2.0.0
	 *
	 * @return array add_on_type => string[] supported attributes
	 */
	public static function get_add_on_supported_attributes() {

		$add_on_types          = self::get_add_on_types();
		$types_with_attributes = array();

		foreach ( $add_on_types as $type => $value ) {

			$classname = self::get_add_on_classname( $type );

			if ( class_exists( $classname ) ) {

				$supported_attributes = $classname::get_supported_attributes();

				if ( is_array( $supported_attributes ) ) {

					$types_with_attributes[ $type ] = $supported_attributes;
				}
			}
		}

		return $types_with_attributes;
	}


	/**
	 * Instantiates and returns an add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param string|array|Add_On $add_on_type the add-on type string, or an Add_On object, or an add-on data array containing a `type` key
	 * @param int|array|Add_On $params (optional) param to pass into the new Add-On's constructor -- @see Add_On::__construct() for more info
	 * @return Add_On|bool false on failure
	 */
	public static function create_add_on( $add_on_type, $params = array() ) {

		// passed Add_On object
		if ( $add_on_type instanceof Add_On ) {
			$params      = $add_on_type;
			$add_on_type = $add_on_type->get_type();
		}

		// passed add-on data array
		if ( is_array( $add_on_type ) && isset( $add_on_type['type'] ) ) {
			$params      = $add_on_type;
			$add_on_type = $add_on_type['type'];
		}

		$classname = self::get_add_on_classname( $add_on_type );
		$add_on    = class_exists( $classname ) ? new $classname( $params ) : false;

		return $add_on instanceof Add_On ? $add_on : false;
	}


	/**
	 * Gets the fully-qualified classname for an add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param string $add_on_type the add-on type string
	 * @return string
	 */
	public static function get_add_on_classname( $add_on_type ) {

		$add_on_classnames = self::get_add_on_classnames();
		$classname         = isset( $add_on_classnames[ $add_on_type ] ) ? $add_on_classnames[ $add_on_type ] : '';

		if ( '' !== $classname ) {

			$classname = self::ADD_ON_NAMESPACE . implode( '_', array_map( 'ucfirst', explode( '_', str_replace( array( '-', ' ' ), '_', $classname ) ) ) );
		}

		/**
		 * Filters the classname used to instantiate a given add-on type.
		 *
		 * @since 2.0.0
		 *
		 * @param string $classname the fully-qualified classname to instantiate
		 * @param string $add_on_type the add-on type string
		 */
		$classname = apply_filters(
			'wc_checkout_add_ons_get_add_on_classname',
			$classname,
			$add_on_type
		);

		$class_parents = class_exists( $classname ) ? class_parents( $classname ) : [];

		// make sure the class extends our abstract Add_On class
		return is_array( $class_parents ) && in_array( self::ADD_ON_NAMESPACE . 'Add_On', $class_parents, true ) ? $classname : '';
	}


	/**
	 * Gets an instantiated add-on object without having to know which subclass to instantiate.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|array|string $lookup the Add_On object, object data array, or Add_On ID
	 * @return Add_On|bool false on failure
	 */
	public static function get_add_on( $lookup = '' ) {

		$add_on = false;

		if ( $add_on_id = self::get_add_on_id( $lookup ) ) {

			$data_store  = new Data_Store_Options();
			$object_info = $data_store->get_object_info( $add_on_id );
			$add_on      = isset( $object_info['type'] ) ? self::create_add_on( $object_info['type'], $add_on_id ) : false;
		}

		return $add_on instanceof Add_On ? $add_on : false;
	}


	/**
	 * Gets the ID for an add-on.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|array|string $add_on the Add_On object, object data array, or Add_On ID
	 * @return int|bool false on failure
	 */
	public static function get_add_on_id( $add_on = '' ) {

		$id = '';

		if ( is_string( $add_on ) ) {

			$id = $add_on;

		} elseif ( $add_on instanceof Add_On ) {

			$id = $add_on->get_id();

		} elseif ( isset( $add_on['id'] ) ) {

			$id = $add_on['id'];
		}

		return $id !== '' ? $id : false;
	}


	/**
	 * Gets an array of all of the add-ons.
	 *
	 * @since 2.0.0
	 *
	 * @return Add_On[]
	 */
	public static function get_add_ons() {

		$data_store  = new Data_Store_Options();
		$add_on_data = $data_store->get_add_ons_data();
		$add_ons     = array();

		foreach ( $add_on_data as $add_on_datum ) {

			$add_ons[] = self::create_add_on( $add_on_datum );
		}

		return $add_ons;
	}


}
