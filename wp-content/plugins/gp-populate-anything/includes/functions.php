<?php
if ( ! function_exists( 'gppa_is_assoc_array' ) ) {
	function gppa_is_assoc_array( array $array ) {
		return ( array_values( $array ) !== $array );
	}
}
