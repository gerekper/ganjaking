<div class="subscribers-container">
	<?php if (isset($_GET['opt-out-restored']) && $_GET['opt-out-restored'] > 0): // phpcs:ignore WordPress.Security.NonceVerification ?>
		<div id="message" class="updated"><p><?php echo esc_html( sprintf( _n('1 email has been restored', '%d emails have been restored', intval($_GET['opt-out-restored']), 'follow_up_emails'), intval($_GET['opt-out-restored']))); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
	<?php endif; ?>

	<?php if (isset($_GET['opt-out-added'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
		<div id="message" class="updated"><p><?php echo wp_kses_post( sprintf(__('<em>%s</em> has been added to the opt-out list', 'follow_up_emails'), strip_tags( sanitize_text_field( wp_unslash( $_GET['opt-out-added'] ) ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
	<?php endif; ?>

	<?php if (isset($_GET['opt-out-error']) ): // phpcs:ignore WordPress.Security.NonceVerification ?>
		<div id="message" class="error"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['opt-out-error'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
	<?php endif; ?>

	<div class="subscribers-col1">
		<?php
		$list_table = new FUE_Subscribers_Optouts_List_Table();
		$list_table->prepare_items();
		$list_table->display();
		?>
	</div>
	<div class="subscribers-col2">
		<form action="admin-post.php" method="post">
			<input type="hidden" name="action" value="fue_optout_manage" />
			<?php wp_nonce_field( 'fue-optout-manage' ); ?>

			<div class="meta-box no-padding">
				<h3 class="handle"><?php esc_html_e('Add Email to Opt-out', 'follow_up_emails'); ?></h3>
				<div class="inside">
					<p>
						<input type="email" name="email" placeholder="Email address" />
					</p>

					<div class="meta-box-actions">
						<input type="submit" name="button_add" class="button button-primary" value="<?php esc_attr_e('Add Email', 'follow_up_emails'); ?>">
						<div class="clear"></div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
