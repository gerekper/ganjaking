<?php
/**
 * Deprecated functions/actions/filters.
 */

/**
 * Handle renamed filters
 */
global $wc_map_deprecated_filters;

$wc_map_deprecated_filters = array(
	'woocommerce_help_scount_conversation_form_description'  => 'woocommerce_help_scout_conversation_form_description',
	'woocommerce_help_scount_conversation_form_start'        => 'woocommerce_help_scout_conversation_form_start',
	'woocommerce_help_scount_conversation_form'              => 'woocommerce_help_scout_conversation_form',
	'woocommerce_help_scount_conversation_form_end'          => 'woocommerce_help_scout_conversation_form_end',
	'woocommerce_help_scount_conversation_admin_form_start'  => 'woocommerce_help_scout_conversation_admin_form_start',
	'woocommerce_help_scount_conversation_admin_form'        => 'woocommerce_help_scout_conversation_admin_form',
	'woocommerce_help_scount_conversation_admin_form_end'    => 'woocommerce_help_scout_conversation_admin_form_end'
);

foreach ( $wc_map_deprecated_filters as $new => $old ) {
	add_filter( $new, 'wc_help_scout_deprecated_filter_mapping' );
}

function wc_help_scout_deprecated_filter_mapping( $data, $arg_1 = '', $arg_2 = '', $arg_3 = '' ) {
	global $wc_map_deprecated_filters;

	$filter = current_filter();

	if ( isset( $wc_map_deprecated_filters[ $filter ] ) ) {
		if ( has_filter( $wc_map_deprecated_filters[ $filter ] ) ) {
			$data = apply_filters( $wc_map_deprecated_filters[ $filter ], $data, $arg_1, $arg_2, $arg_3 );
			if ( ! is_ajax() ) {
				_deprecated_function( 'The ' . $wc_map_deprecated_filters[ $filter ] . ' filter', '', $filter );
			}
		}
	}

	return $data;
}
