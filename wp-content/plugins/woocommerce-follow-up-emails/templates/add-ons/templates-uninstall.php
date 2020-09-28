<?php

check_admin_referer( 'template_uninstall' );
$template = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';
$url = wp_nonce_url( 'admin.php?page=followup-emails-templates&action=uninstall_template&template='. $template, 'template_uninstall');
$target_directory = trailingslashit( get_stylesheet_directory() );
$_dir = $target_directory .'follow-up-emails/emails';
$file = sanitize_file_name( basename( $template ) );

if (false === ($creds = request_filesystem_credentials($url, '', false, $target_directory ) ) ) {
	return true; // stop the normal page form from displaying
}

if ( ! WP_Filesystem($creds) ) {
	// our credentials were no good, ask the user for them again
	request_filesystem_credentials($url, '', true, $target_directory );
	return true;
}

global $wp_filesystem;

// check that the template actually exists
if ( !file_exists( $_dir .'/'. $file ) ) {
	show_message( sprintf( __('The template %s does not exist', 'follow_up_emails'), $_dir .'/'. $file ) );
	return;
}

$tpl = new FUE_Email_Template( $_dir .'/'. $file );

show_message( __('Removing template...', 'follow_up_emails' ) );

if ( !$wp_filesystem->delete( $_dir .'/'. $file ) ) {
	show_message( sprintf( __('Error removing the file: %s', 'follow_up_emails' ), $_dir .'/'. $file ) );
	return;
}

/* translators: %s template name */
show_message( sprintf( __( 'Removed template %s', 'follow_up_emails' ), $tpl->name ) );
show_message( '<a href="admin.php?page=followup-emails-templates">' . __( 'Go back', 'follow_up_emails' ) . '</a>' );
