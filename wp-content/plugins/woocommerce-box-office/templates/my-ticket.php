<?php
/**
 * The Template for displaying [my_ticket] shortcode.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/my-ticket.php.
 *
 * HOWEVER, on occasion WooCommerce Box Office will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @author  WooThemes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * woocommerce_box_office_before_edit_ticket hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 */
do_action( 'woocommerce_box_office_before_edit_ticket' );

?>
<p><?php echo esc_html( $ticket_description ); ?></p>

<?php
wc_get_template( 'ticket/form.php', $ticket_form_params, 'woocommerce-box-office', WCBO()->dir . 'templates/' );

/**
 * woocommerce_box_office_after_edit_ticket hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_box_office_after_edit_ticket' );
