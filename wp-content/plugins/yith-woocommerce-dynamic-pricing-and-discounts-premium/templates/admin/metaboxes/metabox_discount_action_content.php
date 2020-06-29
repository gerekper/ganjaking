<?php
/**
 * Metabox discount.
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

$posted = $_REQUEST;
if ( isset( $posted['ywdpd_discount_type'] ) ) {
	$discount_type = $posted['ywdpd_discount_type'];
} elseif ( isset( $posted['yit_metaboxes']['_discount_type'] ) ) {
	$discount_type = $posted['yit_metaboxes']['_discount_type'];
} elseif ( isset( $posted['post'] ) ) {
	$discount_type = get_post_meta( $post->ID, '_discount_type', true );
}
?>
<div class="submitbox ywdpd_submit_box" id="submitpost">
	<div id="major-publishing-actions">

		<div id="publishing-action">
			<div id="delete-action">
				<?php
				printf(
					'<a href="%s" class="submitdelete deletion" aria-label="%s">%s</a>',
					get_delete_post_link( $post->ID, '', true ) . '&ywdpd_discount_type=' . esc_attr( $discount_type ),
					esc_attr( __( 'Delete', 'ywdpd' ) ),
					esc_html( __( 'Delete', 'ywdpd' ) )
				);
				?>
				</div>

			<span class="spinner"></span>
			<input name="original_publish" type="hidden" id="original_publish" value="Publish">
			<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php esc_attr_e('Save', 'ywdpd'); ?>"></div>
		<div class="clear"></div>
	</div>
</div>
