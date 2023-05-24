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

// Check that the zip file only contains `.html` files.
if ( ! class_exists( 'ZipArchive' ) ) {
	show_message( __( 'ZipArchive is required to check template files.', 'follow_up_emails' ) );
	return;
}

$zip_file = isset( $_FILES['template']['tmp_name'] ) ? wp_unslash( $_FILES['template']['tmp_name'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$zip      = new ZipArchive();

$zip->open( $zip_file );

$template_name = '';
for ( $i = 0; $i < $zip->numFiles; $i++ ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	$template_name = $zip->getNameIndex( $i );

	if ( '__MACOSX/' === $template_name ) { // Skip the OS X-created __MACOSX directory.
		continue;
	}

	if ( ! preg_match( '/\.html$/', $template_name ) ) {
		show_message( __( 'Only HTML template files are allowed.', 'follow_up_emails' ) );
		return;
	}
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

$unzipped = unzip_file( $zip_file, $emails_dir );

if ( is_wp_error( $unzipped ) ) {
	show_message( $unzipped );
	return;
}

/* translators: %s template name */
show_message( sprintf( __( 'Unzipped template %s', 'follow_up_emails' ), $template_name ) );
show_message( '<a href="admin.php?page=followup-emails-templates">' . __( 'Go back', 'follow_up_emails' ) . '</a>' );
