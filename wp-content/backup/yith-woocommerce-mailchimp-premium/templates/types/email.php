<?php
/**
 * Subscription form template email input (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */
?>

<input type="email" name="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>" id="<?php echo esc_attr( $mailchimp_data['tag'] ) ?>_<?php echo esc_attr( $id ) ?>" value="<?php echo isset( $_REQUEST[ $mailchimp_data['tag'] ] ) ? $_REQUEST[ $mailchimp_data['tag'] ] : '' ?>" <?php echo ( $mailchimp_data['required'] ) ? 'required="required"' : '' ?> placeholder="<?php echo $placeholder ?>" />