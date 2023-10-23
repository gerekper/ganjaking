<?php // phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
/**
 * Options for YITH Plugin Panel WooCommerce
 *
 * @package YITH Plugin Framework
 */

$prefix  = str_replace( '-options.php', '', basename( __FILE__ ) ) . '-';
$options = YITH_Plugin_FW_Panels_Helper::get_fixture( 'all-options' );
$fields  = array();

$fields[ $prefix . 'general-options' ] = array(
	'title' => 'General',
	'type'  => 'title',
	'id'    => 'general-options',
);

foreach ( $options as $key => $values ) {
	$type         = $values['type'];
	$prefixed_key = $prefix . $key;

	$id_title = array(
		'id'    => $prefixed_key,
		'title' => $prefixed_key,
	);

	$fields[ $prefixed_key ]              = wp_parse_args( $values, $id_title );
	$fields[ $prefixed_key ]['type']      = 'yith-field';
	$fields[ $prefixed_key ]['yith-type'] = $type;
	if ( isset( $fields[ $prefixed_key ]['value'] ) ) {
		unset( $fields[ $prefixed_key ]['value'] );
	}
}


$fields[ $prefix . 'general-options-end' ] = array(
	'type' => 'sectionend',
	'id'   => 'general-options',
);

return array(
	'wc-panel' => $fields,
);
