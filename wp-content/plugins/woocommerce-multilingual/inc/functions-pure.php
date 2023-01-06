<?php
/**
 * This file is automatically loaded by composer,
 * and it should only contain pure functions.
 *
 * Impure functions should go to the main functions.php
 * manually included so we can mock it in our unit tests.
 */
namespace WCML\functions;

use WPML\FP\Obj;

use function WPML\FP\curryN;

if ( ! function_exists( 'WCML\functions\getId' ) ) {
	/**
	 * Returns the object id.
	 *
	 * @param \stdClass|\WP_Post|\WC_Order|\WC_Data|array|null $object The object to get id.
	 * @return int|callable|null
	 */
	function getId( $object = null ) {
		$getId = function ( $object ) {
			return is_object( $object ) && method_exists( $object, 'get_id' )
				? $object->get_id()
				: Obj::prop( 'ID', $object );
		};
		return call_user_func_array( curryN( 1, $getId ), func_get_args() );
	}
}
