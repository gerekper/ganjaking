<?php
/**
 * Admin View: Send Store Credit page.
 *
 * @package WC_Store_Credit/Admin/Views
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap woocommerce">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php WC_Store_Credit_Admin_Send_Credit_Page::show_messages(); ?>

	<form id="mainform" method="post" action="" enctype="multipart/form-data">
		<?php
		if ( ! empty( $fields ) ) :
			WC_Admin_Settings::output_fields( $fields );
		endif;
		?>

		<p class="submit">
			<button name="save" class="button-primary woocommerce-save-button" type="submit" value="<?php echo esc_attr_x( 'Send credit', 'send credit: submit button', 'woocommerce-store-credit' ); ?>"><?php esc_html_e( 'Send credit', 'woocommerce-store-credit' ); ?></button>
			<?php wp_nonce_field( 'wc_send_store_credit' ); ?>
		</p>
	</form>
</div>
