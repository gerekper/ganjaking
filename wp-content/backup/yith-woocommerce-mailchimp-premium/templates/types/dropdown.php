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

<select name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>"<?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?>>
	<?php foreach( $mailchimp_data['options']['choices'] as $id => $name ): ?>
		<option value="<?php echo esc_attr( isset( $mailchimp_data['use_id_instead_of_name'] ) ? $id : $name )?>" <?php selected( $id, $selected )?> ><?php echo esc_html( $name )?></option>
	<?php endforeach; ?>
</select>

<?php endif; ?>