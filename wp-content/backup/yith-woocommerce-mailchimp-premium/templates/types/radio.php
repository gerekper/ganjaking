<?php
/**
 * Subscription form template dropdown input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

$selected = isset( $_REQUEST[ $mailchimp_data['tag'] ] ) ? $_REQUEST[ $mailchimp_data['tag'] ] : '';
?>

<?php if( ! empty( $mailchimp_data['options']['choices'] ) ): ?>
	<?php foreach( $mailchimp_data['options']['choices'] as $id => $name ): ?>
		<input type="radio" value="<?php echo esc_attr( isset( $mailchimp_data['use_id_instead_of_name'] ) ? $id : $name ) ?>" id="<?php echo esc_attr( $mailchimp_data['tag'] . '_' . $id ) ?>_<?php echo esc_attr( $id ) ?>" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?>/>
		<label for="<?php echo esc_attr( $mailchimp_data['tag'] . '_' . $id ) ?>"><?php echo esc_html( $name ) ?></label><br/>
	<?php endforeach; ?>
<?php endif; ?>