<ul class="fue-templates">
	<?php
	$installed_templates = array_map( 'fue_template_basename', fue_get_installed_templates() );

	$all_templates = array();

	foreach ( $installed_templates as $idx => $basename ) {
		if ( $basename == 'WooCommerce' ) {
			unset( $installed_templates[ $idx ] );
			continue;
		}

		$slug = str_replace( '.html', '', $basename );

		if ( isset( $templates->$slug ) ) {
			$templates->$slug->file = $basename;
			$templates->$slug->installed = true;
			$installed_templates[ $slug ] = $templates->$slug;
		} else {
			$tpl_file = fue_locate_email_template( $basename );

			if ( !$tpl_file ) {
				continue;
			}

			$data   = get_file_data( $tpl_file, array('name' => 'Template Name') );

			$template               = new stdClass();
			$template->file         = $basename;
			$template->name         = (!empty($data['name'])) ? $data['name'] : $basename;
			$template->version      = '';
			$template->description  = '';
			$template->thumbnail    = '';
			$template->image        = '';
			$template->downloads    = '';
			$template->url          = '';
			$template->installed    = true;

			$installed_templates[ $slug ] = $template;
		}
		unset ( $installed_templates[ $idx ] );
	}

	if ( empty( $installed_templates ) ) {
		echo '<li>'. esc_html__('No templates available', 'follow_up_emails') .'</li>';
	} else {
		foreach ( $installed_templates as $id => $template ) {
			?>
			<li><?php include FUE_TEMPLATES_DIR .'/add-ons/template-item.php'; ?></li>
			<?php
		}
	}
	?>
</ul>
<div id="template_editor">
	<p class="submit">
		<input type="button" class="button-primary edit-html-save" value="<?php esc_attr_e( 'Save', 'follow_up_emails' ); ?>" />
		<input type="button" class="button edit-html-close" value="<?php esc_attr_e( 'Close', 'follow_up_emails' ); ?>" />
		<span class="edit-html-spinner spinner"></span>
		<span class="edit-html-status"></span>
		<input type="hidden" id="current_template" />
	</p>

	<p><textarea id="editor" name="editor" rows="20" cols="80"></textarea></p>

	<p class="submit">
		<input type="button" class="button-primary edit-html-save" value="<?php esc_attr_e( 'Save', 'follow_up_emails' ); ?>" />
		<input type="button" class="button edit-html-close" value="<?php esc_attr_e( 'Close', 'follow_up_emails' ); ?>" />
		<span class="edit-html-spinner spinner"></span>
		<span class="edit-html-status"></span>
	</p>
</div>
