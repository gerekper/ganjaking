<div class="templates-new">
	<div class="row">
		<form action="" method="post" enctype="multipart/form-data">
			<?php esc_html_e('Upload your ZIP file', 'follow_up_emails'); ?>

			<input type="file" name="template" value="Select file" class="upload" />

			<input type="hidden" name="action" value="template_upload" />
			<?php wp_nonce_field( 'fue_upload_template' ); ?>
			<input type="submit" class="button big button-primary" value="<?php esc_attr_e('Install Template', 'follow_up_emails'); ?>" />
		</form>
	</div>
	<div class="row">
		<?php esc_html_e('Create a template from scratch', 'follow_up_emails'); ?>

		<input type="button" class="button big button-primary create-template" value="<?php esc_attr_e('Create a Template', 'follow_up_emails'); ?>" />
	</div>
	<div class="clear"></div>
</div>
<div class="template-form" style="display: none;">
	<form method="post" class="create-template-form">
		<p class="form-field">
			<label for="template_name"><?php esc_html_e('Template Name', 'follow_up_emails'); ?></label>
			<input type="text" name="template_name" id="template_name" />
		</p>

		<p class="form-field">
			<label for="template_source"><?php esc_html_e('Template Source', 'follow_up_emails'); ?></label>
			<textarea rows="20" cols="80" id="template_source" name="template_source"></textarea>
		</p>

		<p class="submit">
			<input type="hidden" name="action" value="template_create">
			<?php wp_nonce_field( 'fue_create_template' ); ?>
			<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Template', 'follow_up_emails'); ?>" />
		</p>
	</form>
</div>
