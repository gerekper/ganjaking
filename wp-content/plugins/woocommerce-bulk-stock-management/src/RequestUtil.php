<?php

namespace Automattic\WooCommerce\BulkStockManagement;

/**
 * Class with utility methods to deal with the HTTP request.
 *
 * @since x.x.x
 */
class RequestUtil {
	/**
	 * Get the value of a given request variable,
	 * or a default value if the variable is not present.
	 */
    public static function get_request_variable( $name, $default = '' ) {
		return empty( $_REQUEST[ $name ] ) ? $default : wc_clean( wp_unslash( $_REQUEST[ $name ] ) );
	}

	/**
	 * Get the value of a given query string variable,
	 * or a default value if the variable is not present.
	 */
    public static function get_query_string_variable( $name, $default = '' ) {
		return empty( $_GET[ $name ] ) ? $default : wc_clean( wp_unslash( $_GET[ $name ] ) );
	}

	/**
	 * Get the value of a given POST variable,
	 * or a default value if the variable is not present.
	 */
    public static function get_post_variable( $name, $default = '' ) {
		return empty( $_POST[ $name ] ) ? $default : wc_clean( wp_unslash( $_POST[ $name ] ) );
	}
}
