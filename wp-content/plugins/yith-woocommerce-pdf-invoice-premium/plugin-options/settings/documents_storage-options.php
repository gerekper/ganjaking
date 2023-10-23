<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Documents storage options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$gd_auth_url  = isset( $_GET['tab'], $_GET['sub_tab'] ) && 'settings' === sanitize_key( wp_unslash( $_GET['tab'] ) ) && 'settings-documents_storage' === sanitize_key( wp_unslash( $_GET['sub_tab'] ) ) ? YITH_PDF_Invoice_Google_Drive::get_instance()->client->createAuthUrl() : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$redirect_uri = admin_url( 'admin.php' . yith_ywpi_get_panel_url( 'settings', 'settings-documents_storage' ) );

$storage_options = array(
	'settings-documents_storage' => array(
		'storage_settings'                    => array(
			'name' => __( 'Documents storage', 'yith-woocommerce-pdf-invoice' ),
			'type' => 'title',
		),
		'invoice_folder_format'               => array(
			'name'      => __( 'Invoice sub-folder name', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_invoice_folder_format',
			// translators: all the placeholders are some HTML tags.
			'desc'      => sprintf( __( 'Enter a name to identify the folder where you want to store documents (root is %1$s%2$s wp-content / uploads / ywpi-pdf-invoice%3$s%4$s). Use [year], [month] and [day] as placeholders. Example: "Invoices/[year]/[month]" for invoices stored by year and month. %5$sLeave empty to store documents in the root folder.', 'yith-woocommerce-pdf-invoice' ), '<b>', '<i>', '</b>', '</i>', '<br>' ),
			'default'   => 'Invoices',
		),
		'dropbox_access'                      => array(
			'name'      => __( 'Automatically upload documents to Dropbox', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_dropbox_allow_upload',
			'desc'      => __( 'Enable if you want to automatically save all documents to a Dropbox folder.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'dropbox_folder'                      => array(
			'name'      => __( 'Dropbox folder name', 'yith-woocommerce-pdf-invoice' ),
			'desc'      => __( 'Name the Dropbox folder where you want to store the documents.', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_dropbox_folder',
			'default'   => __( 'Invoices', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_dropbox_allow_upload',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'dropbox'                             => array(
			'name'    => __( 'Dropbox authorization code', 'yith-woocommerce-pdf-invoice' ),
			'desc'    => __( 'Set automatic document backup to Dropbox.', 'yith-woocommerce-pdf-invoice' ),
			'type'    => 'ywpi_dropbox',
			'id'      => 'ywpi_dropbox_key',
			'default' => 'yes',
		),
		'google_drive_access'                 => array(
			'name'      => __( 'Automatically upload documents to Google Drive', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'id'        => 'ywpi_google_drive_allow_upload',
			'desc'      => __( 'Enable if you want to automatically save all documents to a Google Drive folder.', 'yith-woocommerce-pdf-invoice' ),
			'default'   => 'no',
		),
		'google_drive_folder'                 => array(
			'name'      => __( 'Google Drive folder name', 'yith-woocommerce-pdf-invoice' ),
			'desc'      => __( 'Name the Google Drive folder where you want to store the documents.', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_google_drive_folder',
			'default'   => __( 'Invoices', 'yith-woocommerce-pdf-invoice' ),
			'deps'      => array(
				'id'    => 'ywpi_google_drive_allow_upload',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'google_drive_client_id'              => array(
			'name'      => __( 'Google Drive Client ID', 'yith-woocommerce-pdf-invoice' ),
			'desc'      => __( 'Copy and paste the Google Drive Client ID here.', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_google_drive_client_id',
			'deps'      => array(
				'id'    => 'ywpi_google_drive_allow_upload',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'google_drive_client_password'        => array(
			'name'      => __( 'Google Drive Client Password', 'yith-woocommerce-pdf-invoice' ),
			'desc'      => __( 'Copy and paste the Google Drive Client Password here.', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'id'        => 'ywpi_google_drive_client_password',
			'deps'      => array(
				'id'    => 'ywpi_google_drive_allow_upload',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'google_drive_api_authorization_code' => array(
			'id'        => 'ywpi_authorization_code',
			// translators: 1. A break line. 2. The Google Drive OAuth link.
			'desc'      => sprintf( __( 'Copy and paste the Google Drive authorization code here. %1$s <a href="%2$s" target="_blank">Get your Google Drive authorization code ></a>%3$s<b>Your Redirect URI</b>: <i> %4$s </i>', 'yith-woocommerce-pdf-invoice' ), '<br>', $gd_auth_url, '<br>', $redirect_uri ),
			'name'      => __( 'Google Drive Authorization Code', 'yith-woocommerce-pdf-invoice' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'deps'      => array(
				'id'    => 'ywpi_google_drive_allow_upload',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
		),
		'storage_settings_end'                => array(
			'type' => 'sectionend',
		),
	),
);

if ( is_dir( YITH_YWPI_DOCUMENT_SAVE_DIR ) && ! is_writable( YITH_YWPI_DOCUMENT_SAVE_DIR ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable
	// translators: 1. italic html code. 2. The folder path. 3. end of italic html code.
	$storage_options['settings-documents_storage']['storage_settings']['desc'] = sprintf( __( 'The documents folder %1$s %2$s %3$s is not writable. Please, check the folder permissions before starting to create documents!', 'yith-woocommerce-pdf-invoice' ), '<i>', YITH_YWPI_DOCUMENT_SAVE_DIR, '</i>' );
}

return apply_filters( 'ywpi_storage_options', $storage_options );
