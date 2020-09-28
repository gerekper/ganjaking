<div id="email-actions-box" class="submitbox">

	<div id="minor-publishing">

		<div style="display:none;">
			<p class="submit"><input type="submit" value="Save" class="button" id="save" name="save"></p></div>

		<div id="minor-publishing-actions">
			<div id="save-action">
				<input type="submit" class="button" value="Save Draft" id="save-post" name="save">
				<span class="spinner"></span>
			</div>
			<div class="clear"></div>
		</div><!-- #minor-publishing-actions -->

		<div id="misc-publishing-actions">

			<div class="misc-pub-section misc-pub-email-status"><label for="post_status"><?php esc_html_e('Status:') ?></label>
			<span id="post-status-display">
			<?php
			switch ( $email->status ) {
				case 'draft':
				case 'auto-draft':
				case FUE_Email::STATUS_ACTIVE:
					esc_html_e('Active', 'follow_up_emails');
					break;
				case FUE_Email::STATUS_INACTIVE:
					esc_html_e('Inactive', 'follow_up_emails');
					break;
				case FUE_Email::STATUS_ARCHIVED:
					esc_html_e('Archived');
					break;
			}
			?>
			</span>

				<a href="#post_status" class="edit-post-status hide-if-no-js"><span aria-hidden="true"><?php esc_html_e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php esc_html_e( 'Edit status' ); ?></span></a>

				<div id="post-status-select" class="hide-if-js">
					<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $email->status ) ? 'draft' : $email->status); ?>" />
					<select name='post_status' id='post_status'>
						<option<?php selected( in_array( $email->status, array( 'draft', 'auto-draft', FUE_Email::STATUS_ACTIVE ) ) ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_ACTIVE); ?>'><?php esc_html_e('Active', 'follow_up_emails'); ?></option>
						<option<?php selected( $email->status, FUE_Email::STATUS_INACTIVE ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_INACTIVE); ?>'><?php esc_html_e('Inactive', 'follow_up_emails') ?></option>
						<option<?php selected( $email->status, FUE_Email::STATUS_ARCHIVED ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_ARCHIVED); ?>'><?php esc_html_e('Archived', 'follow_up_emails') ?></option>
					</select>
					<a href="#post_status" class="save-post-status hide-if-no-js button"><?php esc_html_e('OK', 'follow_up_emails'); ?></a>
					<a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php esc_html_e('Cancel', 'follow_up_emails'); ?></a>
				</div>

			</div><!-- .misc-pub-section -->
		</div>
		<div class="clear"></div>
	</div>

	<div id="publishing-actions">
		<div id="fue-delete-action">
			<a class="submitdelete deletion" onclick="return confirm('Really delete this email?');" href="<?php echo esc_url( wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. esc_attr( $post->ID ), 'delete-email') ); ?>"><?php esc_html_e('Delete', 'follow_up_email'); ?></a>
		</div>

		<div id="publishing-action">
			<span class="spinner"></span>
			<input type="submit" class="button save_email button-primary" name="save" value="<?php esc_attr_e( 'Save Email', 'follow_up_emails' ); ?>" <?php if ( empty( $email->type ) ) echo 'disabled' ?> />
			<?php wp_nonce_field( 'fue_save_data', 'fue_meta_nonce' ); ?>
		</div>
		<div class="clear"></div>
	</div>
</div>
