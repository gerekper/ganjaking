<form name="edit-ticket" action="" method="post" class="edit-ticket">
	<?php $ticket_form->render(); ?>

	<div class="clear"></div>

	<p class="buttons">
		<input type="submit" class="button" value="<?php _e( 'Update', 'woocommerce-box-office' ); ?>" />

		<?php if ( $print_ticket_enabled ) : ?>
			<a href="<?php echo esc_url( $print_ticket_url ); ?>" target="_blank" class="button">
				<?php _e( 'Print ticket', 'woocommerce-box-office' ); ?>
			</a>
		<?php endif; ?>
	</p>

	<input type="hidden" name="action" value="update_ticket" />
	<?php wp_nonce_field( 'woocommerce-box-office_update_ticket' ); ?>
</form>

<?php do_action( 'woocommerce_box_office_after_edit_ticket_form', $ticket_id ); ?>
