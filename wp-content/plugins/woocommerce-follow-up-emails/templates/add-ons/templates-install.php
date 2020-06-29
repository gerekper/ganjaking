<?php

check_admin_referer( 'template_install' );
$template_id = isset( $_GET['template'] ) ? sanitize_text_field( wp_unslash( $_GET['template'] ) ) : '';
$url = wp_nonce_url( 'admin.php?page=followup-emails-templates&action=install_template&template='. $template_id, 'template_install');
$target_directory = trailingslashit( get_stylesheet_directory() );
$_dir = $target_directory .'follow-up-emails/emails';

if (false === ($creds = request_filesystem_credentials($url, '', false, $target_directory ) ) ) {
	return true; // stop the normal page form from displaying
}

if ( ! WP_Filesystem($creds) ) {
	// our credentials were no good, ask the user for them again
	request_filesystem_credentials($url, '', true, $target_directory );
	return true;
}

global $wp_filesystem;

// check if we need to first create the follow-up-emails/emails directory
$fue_dir = $target_directory . 'follow-up-emails';
$emails_dir = trailingslashit( $fue_dir ) . 'emails';
if ( !file_exists( $fue_dir ) ) {
	// attemp to create the directories
	if ( ! $wp_filesystem->mkdir($fue_dir) && ! $wp_filesystem->is_dir( $fue_dir ) ) {
		wp_mkdir_p( $fue_dir );

		if ( !file_exists( $fue_dir ) ) {
			show_message( sprintf( __( 'Could not create directory: %s.', 'follow_up_emails' ), $fue_dir ) );
			return;
		}

	}
}

if ( !file_exists( $emails_dir ) ) {
	if ( ! $wp_filesystem->mkdir($emails_dir, FS_CHMOD_DIR) && ! $wp_filesystem->is_dir( $emails_dir ) ) {

		wp_mkdir_p( $emails_dir );

		if ( !file_exists( $emails_dir ) ) {
			show_message( sprintf( __( 'Could not create directory.', 'follow_up_emails' ), $emails_dir ) );
			return;
		}

	}
}

show_message( __('Downlading template...', 'follow_up_emails' ) );

$fue_tpl     = new FUE_Templates( Follow_Up_Emails::instance() );
$templates   = $fue_tpl->get_templates();

if ( !isset( $templates->$template_id ) ) {
	show_message( __( 'Could not find the template requested.', 'follow_up_emails' ) );
	return;
}

$template = $templates->$template_id;
$response = wp_remote_get( $template->url );

if ( is_wp_error( $response ) ) {
	show_message( $response->get_error_message() );
	return;
}

if ( $response['response']['code'] != 200 ) {
	show_message( $response['body'] );
	return;
}

$contents = wp_remote_retrieve_body( $response );

$template_file = $template_id . '.html';

if ( !$wp_filesystem->put_contents( $emails_dir .'/'. $template_file, $contents, FS_CHMOD_FILE ) ) {
	show_message( sprintf( __('Error saving file to %s', 'follow_up_emails' ), $emails_dir .'/'. $template_file ) );
	return;
}

show_message( sprintf( __('Template %s installed!', 'follow_up_emails'), $template_file ) );
show_message( '<a href="admin.php?page=followup-emails-templates">'. __('Go back', 'follow_up_emails') .'</a>');