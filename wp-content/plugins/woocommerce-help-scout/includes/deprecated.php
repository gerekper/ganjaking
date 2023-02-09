<?php
/**
 * Deprecated functions/actions/filters.
 *
 * @package  deprecated
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
	'woocommerce_help_scount_conversation_admin_form_end'    => 'woocommerce_help_scout_conversation_admin_form_end',
);

foreach ( $wc_map_deprecated_filters as $new => $old ) {
	add_filter( $new, 'wc_help_scout_deprecated_filter_mapping' );
}
/**
 * Wc_help_scout_deprecated_filter_mapping
 *
 * @param array  $data  data.
 * @param string $arg_1  arg_1.
 * @param string $arg_2  arg_2.
 * @param string $arg_3  arg_3.
 */
function wc_help_scout_deprecated_filter_mapping( $data, $arg_1 = '', $arg_2 = '', $arg_3 = '' ) {
	global $wc_map_deprecated_filters;

	$filter = current_filter();

	if ( isset( $wc_map_deprecated_filters[ $filter ] ) ) {
		if ( has_filter( $wc_map_deprecated_filters[ $filter ] ) ) {
			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			$data = apply_filters( $wc_map_deprecated_filters[ $filter ], $data, $arg_1, $arg_2, $arg_3 );
			if ( ! is_ajax() ) {
				_deprecated_function( esc_html_e( 'The {wc_map_deprecated_filters[ $filter ]} filter' ), '', esc_html_e( '{$filter}' ) );
			}
		}
	}

	return $data;
}
