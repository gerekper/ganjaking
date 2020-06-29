<?php
/**
 * Subscription form content template (used in shortcode, widget and register)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php foreach ( $fields as $id => $field ) : ?>
	<p class="yith_wcac_field_item">
		<?php if ( ! empty( $field['name'] ) && ! $use_placeholders ) : ?>
			<label for="yith_wcac_shortcode_items_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
		<?php endif; ?>
		<?php YITH_WCAC()->print_field( $id, $field, $fields_data[ $id ], $context ); ?>
	</p>

<?php endforeach; ?>

<p class="yith_wcac_field_item">
	<?php if ( ! empty( $show_tags ) && ! empty( $tags_label ) ) : ?>
		<label for="yith_wcac_shortcode_items_show_tags"><?php echo esc_html( $tags_label ); ?></label>
	<?php endif; ?>
	<?php
	$tags_data = array(
		'title'   => $tags_label,
		'type'    => 'tags',
		'options' => $show_tags,
	);
	YITH_WCAC()->print_field( 'show_tags', array(), $tags_data, $context );
	?>
</p>

<?php if ( $show_privacy_field ) : ?>
	<p>
		<label for="privacy_agreement">
			<input type="checkbox" value="yes" name="privacy_agreement" id="privacy_agreement"/>
			<?php echo $privacy_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</label>
	</p>
<?php endif; ?>

<input type="hidden" name="yith_wcac_shortcode_items[hidden][status]" value="<?php echo esc_attr( $status ); ?>"/>
<input type="hidden" name="yith_wcac_shortcode_items[hidden][list]" value="<?php echo esc_attr( $list ); ?>"/>
<input type="hidden" name="yith_wcac_shortcode_items[hidden][context]" value="<?php echo esc_attr( $context ); ?>"/>
<input type="hidden" name="success_message" value="<?php echo esc_attr( $success_message ); ?>"/>
<input type="hidden" name="show_privacy_field" value="<?php echo $show_privacy_field ? 'yes' : 'no'; ?>"/>
<?php

if ( 'register' == $context ) {
	$show_checkbox = 'yes' == get_option( 'yith_wcac_register_subscription_checkbox' );
	if ( $show_checkbox ) {
		YITH_WCAC()->print_subscription_checkbox( 'register' );
	}
}

wp_nonce_field( 'yith_wcac_subscribe', 'yith_wcac_subscribe_nonce' );
