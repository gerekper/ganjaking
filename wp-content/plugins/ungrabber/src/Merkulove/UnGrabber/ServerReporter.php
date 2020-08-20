<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

use Merkulove\UnGrabber;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Used to implement System report handler class
 * responsible for generating a report for the server environment.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky ( info@alexander.khmelnitskiy.ua )
 **/
final class ServerReporter {

	/**
	 * The one true ServerReporter.
	 *
	 * @var ServerReporter
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new ServerReporter instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Get server environment reporter title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Report title.
	 **/
	public function get_title() {
		return 'Server Environment';
	}

	/**
	 * Retrieve the required fields for the server environment report.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 **/
	public function get_fields() {
		return [
			'os'                    => esc_html__( 'Operating System', 'ungrabber' ),
			'software'              => esc_html__( 'Software','ungrabber' ),
			'mysql_version'         => esc_html__( 'MySQL version','ungrabber' ),
			'php_version'           => esc_html__( 'PHP Version','ungrabber' ),
			'write_permissions'     => esc_html__( 'Write Permissions','ungrabber' ),
			'zip_installed'         => esc_html__( 'ZIP Installed','ungrabber' ),
			'curl_installed'        => esc_html__( 'cURL Installed','ungrabber' ),
			'dom_installed'         => esc_html__( 'DOM Installed','ungrabber' ),
			'xml_installed'         => esc_html__( 'XML Installed','ungrabber' ),
			/** 'bcmath_installed'      => esc_html__( 'BCMath Installed','ungrabber' ), */
			'allow_url_fopen'       => esc_html__( 'allow_url_fopen','ungrabber' ),
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get server operating system.
	 * Retrieve the server operating system.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server operating system.
	 * }
	 **/
	public function get_os() {
		return [
			'value' => PHP_OS,
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get server software.
	 * Retrieve the server software.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server software.
	 * }
	 **/
	public function get_software() {
		return [
			'value' => $_SERVER['SERVER_SOFTWARE'],
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get PHP version.
	 * Retrieve the PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value          PHP version.
	 *    @type string $recommendation Minimum PHP version recommendation.
	 *    @type bool   $warning        Whether to display a warning.
	 * }
	 **/
	public function get_php_version() {
		$result = [
			'value' => PHP_VERSION,
		];

		if ( version_compare( $result['value'], '5.6', '<' ) ) {
			$result['recommendation'] = esc_html__( 'We recommend to use php 5.6 or higher', 'ungrabber' );

			$result['warning'] = true;
		}

		return $result;
	}

	/** @noinspection PhpUnused */
	/**
	 * Get ZIP installed.
	 * Whether the ZIP extension is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   Yes if the ZIP extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the ZIP extension is installed, False otherwise.
	 * }
	 **/
	public function get_zip_installed() {
		$zip_installed = extension_loaded( 'zip' );

		return [
			'value' => $zip_installed ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $zip_installed,
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get cURL installed.
	 * Whether the cURL extension is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the cURL extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the cURL extension is installed, False otherwise.
	 * }
	 **/
	public function get_curl_installed() {

		$curl_installed = extension_loaded( 'curl' );

		return [
			'value' => $curl_installed ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $curl_installed,
			'recommendation' => esc_html__('You must enable CURL (Client URL Library) in PHP. Contact the support service of your hosting provider. They know what to do.', 'ungrabber' )
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get DOM installed.
	 * Whether the DOM extension is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the DOM extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the DOM extension is installed, False otherwise.
	 * }
	 **/
	public function get_dom_installed() {

		$dom_installed = extension_loaded( 'dom' );

		return [
			'value' => $dom_installed ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $dom_installed,
			'recommendation' => esc_html__('You must enable DOM extension (Document Object Model) in PHP. It\'s used for HTML processing. Contact the support service of your hosting provider. They know what to do.', 'ungrabber' )
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get XML installed.
	 * Whether the XML extension is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the XML extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the XML extension is installed, False otherwise.
	 * }
	 **/
	public function get_xml_installed() {

		$xml_installed = extension_loaded( 'xml' );

		return [
			'value' => $xml_installed ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $xml_installed,
			'recommendation' => esc_html__('You must enable XML extension in PHP. It\'s used for XML processing. Contact the support service of your hosting provider. They know what to do.', 'ungrabber' )
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get allow_url_fopen status.
	 * Whether the allow_url_fopen directive is enabled.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if allow_url_fopen is enabled, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if allow_url_fopen is enabled, False otherwise.
	 * }
	 **/
	public function get_allow_url_fopen() {

		$allow_url_fopen = ini_get( 'allow_url_fopen' );

		return [
			'value' => $allow_url_fopen ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $allow_url_fopen,
			'recommendation' => esc_html__('You need to enable allow_url_fopen directive in PHP. It\'s used for download updates and other data from our server. Contact the support service of your hosting provider. They know what to do.', 'ungrabber' )
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get BCMath installed.
	 * Whether the BCMath extension is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   YES if the BCMath extension is installed, NO otherwise.
	 *    @type bool   $warning Whether to display a warning. True if the BCMath extension is installed, False otherwise.
	 * }
	 **/
	public function get_bcmath_installed() {

		$bcmath_installed = extension_loaded( 'bcmath' );

		return [
			'value' => $bcmath_installed ? '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('YES', 'ungrabber') : '<i class="material-icons mdc-system-no">error</i>' . esc_html__('NO', 'ungrabber' ),
			'warning' => ! $bcmath_installed,
			'recommendation' => esc_html__('You must enable BCMath extension (Arbitrary Precision Mathematics) in PHP. Contact the support service of your hosting provider. They know what to do.', 'ungrabber' )
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get MySQL version.
	 * Retrieve the MySQL version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value MySQL version.
	 * }
	 **/
	public function get_mysql_version() {
		global $wpdb;

		$db_server_version = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );

		return [
			'value' => $db_server_version['version_comment']->Value . ' v' . $db_server_version['innodb_version']->Value,
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get write permissions.
	 * Check whether the required folders has writing permissions.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   Writing permissions status.
	 *    @type bool   $warning Whether to display a warning. True if some required
	 *                          folders don't have writing permissions, False otherwise.
	 * }
	 **/
	public function get_write_permissions() {

		$paths_to_check = [
			ABSPATH => esc_html__('WordPress root directory', 'ungrabber' )
		];

		$write_problems = [];

		$wp_upload_dir = wp_upload_dir();

		if ( $wp_upload_dir['error'] ) {
			$write_problems[] = esc_html__('WordPress root uploads directory', 'ungrabber' );
		}

		$ungrabber_uploads_path = $wp_upload_dir['basedir'] . '/ungrabber';

		if ( is_dir( $ungrabber_uploads_path ) ) {
			$paths_to_check[ $ungrabber_uploads_path ] = esc_html__('UnGrabber uploads directory', 'ungrabber' );
		}

		$htaccess_file = ABSPATH . '/.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			$paths_to_check[ $htaccess_file ] = esc_html__('.htaccess file', 'ungrabber' );
		}

		foreach ( $paths_to_check as $dir => $description ) {

			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}

		if ( $write_problems ) {
			$value = '<i class="material-icons mdc-system-no">error</i>' . esc_html__('There are some writing permissions issues with the following directories/files:', 'ungrabber' ) . "<br> &nbsp;&nbsp;&nbsp;&nbsp;– ";

			$value .= implode( "<br> &nbsp;&nbsp;&nbsp;&nbsp;– ", $write_problems );
		} else {
			$value = '<i class="material-icons mdc-system-yes">check_circle</i>' . esc_html__('All right', 'ungrabber' );
		}

		return [
			'value' => $value,
			'warning' => ! ! $write_problems,
		];
	}

	/** @noinspection PhpUnused */
	/**
	 * Get report.
	 * Retrieve the report with all it's containing fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array {
	 *    Report fields.
	 *
	 *    @type string $name Field name.
	 *    @type string $label Field label.
	 * }
	 **/
	final public function get_report() {

		$result = [];

		foreach ( $this->get_fields() as $field_name => $field_label ) {
			$method = 'get_' . $field_name;

			$reporter_field = [
				'name' => $field_name,
				'label' => $field_label,
			];

			$reporter_field = array_merge( $reporter_field, $this->$method() );
			$result[ $field_name ] = $reporter_field;
		}

		return $result;
	}

	/**
	 * Main ServerReporter Instance.
	 *
	 * Insures that only one instance of ServerReporter exists in memory at any one time.
	 *
	 * @static
	 * @return ServerReporter
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ServerReporter ) ) {
			self::$instance = new ServerReporter;
		}

		return self::$instance;
	}

} // End Class ServerReporter.