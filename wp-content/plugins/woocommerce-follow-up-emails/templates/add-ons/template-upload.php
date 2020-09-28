<?php

check_admin_referer( 'fue_upload_template' );
$url = wp_nonce_url( 'admin.php?page=followup-emails-templates&action=new', 'template_install');
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

$status = unzip_file( isset( $_FILES['template']['tmp_name'] ) ? wp_unslash( $_FILES['template']['tmp_name'] ) : '', $emails_dir );

if ( is_wp_error( $status ) ) {
	show_message( $status );
	return;
}

/* translators: %s template name */
show_message( sprintf( __( 'Unzipped template', 'follow_up_emails' ), $template_name ) );
show_message( '<a href="admin.php?page=followup-emails-templates">' . __( 'Go back', 'follow_up_emails' ) . '</a>' );
