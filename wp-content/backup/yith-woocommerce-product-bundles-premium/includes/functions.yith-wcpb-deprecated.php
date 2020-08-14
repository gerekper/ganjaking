<?php

$deprecated_filders_map = array(
	'yith_wcpb_quantity_input_step' => array(
		'since'  => '1.3.9',
		'use'    => 'yith_wcpb_bundled_item_quantity_input_step',
		'params' => 3,
	),
	'yith_wcpb_quantity_input_min'  => array(
		'since'  => '1.3.9',
		'use'    => 'yith_wcpb_bundled_item_quantity_input_min',
		'params' => 3,
	),
	'yith_wcpb_quantity_input_max'  => array(
		'since'  => '1.3.9',
		'use'    => 'yith_wcpb_bundled_item_quantity_input_max',
		'params' => 3,
	),
);

foreach ( $deprecated_filders_map as $deprecated_filter => $options ) {
	$new_filter = $options['use'];
	$params     = $options['params'];
	$since      = $options['since'];
	add_filter(
		$new_filter,
		function() use ( $deprecated_filter, $since, $new_filter ) {
            $args = func_get_args();
			$r    = $args[0];

			if ( has_filter( $deprecated_filter ) ) {
                error_log( sprintf( 'Deprecated filter: %s since %s. Use %s instead!', $deprecated_filter, $since, $new_filter ) );
            
				$r = call_user_func_array( 'apply_filters', array_merge(array( $deprecated_filter ), $args ) );
			}

			return $r;
		},
		10,
		$params
	);
}
