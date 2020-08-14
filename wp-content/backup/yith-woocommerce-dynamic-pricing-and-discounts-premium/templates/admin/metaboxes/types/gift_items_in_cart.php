<?php
/**
 * Gift item in cart.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
extract( $args );
$value = get_post_meta( $post->ID, $id, true );

if ( empty( $value ) ) {
	$value = array(
		'condition' => '>',
		'n_items'   => 1,
	);
}
?>
<div id="<?php echo esc_attr( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); //phpcs:ignore ?>
	 class="yith-plugin-fw-metabox-field-row">
	<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
	<?php
	$args = array(
		'id'      => $id,
		'name'    => $name . '[condition]',
		'type'    => 'select',
		'class'   => 'wc-enhanced-select ywdpd_condition',
		'options' => array(
			'>'   => __( 'is greater than', 'ywdpd' ),
			'<'   => __( 'is less than', 'ywdpd' ),
			'=='  => __( 'is equal to', 'ywdpd' ),
			'!==' => __( 'is not equal to ', 'ywdpd' ),
		),
		'default' => '>',
		'value'   => $value['condition'],

	);
	echo yith_plugin_fw_get_field( $args ); //phpcs:ignore

	$args2 = array(
		'id'                => $id,
		'name'              => $name . '[n_items]',
		'type'              => 'number',
		'value'             => $value['n_items'],
		'custom_attributes' => 'min = "0"  step="1"',
	);

	echo yith_plugin_fw_get_field( $args2 ); //phpcs:ignore
	?>
	<span class="description"><?php echo wp_kses_post( $desc ); ?></span>
</div>
