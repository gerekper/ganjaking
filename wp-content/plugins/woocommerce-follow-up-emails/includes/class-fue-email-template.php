<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_Email_Template {

	/**
	 * @var string The name of the template
	 */
	public $name;

	/**
	 * @var string Contents of the template file
	 */
	private $contents;

	/**
	 * @var array The sections found in the template file
	 */
	private $sections = array();

	/**
	 * @var string Path to the template file
	 */
	private $path;

	/**
	 * Load a template
	 * @param string $file Name of the template file
	 */
	public function __construct( $file = null ) {
		if ( ! is_null( $file ) ) {
			$this->load_template( $file );
		}
	}

	/**
	 * Get the path of the loaded template
	 */
	public function get_path() {
		return $this->path;
	}

	/**
	 * Load the template and parse the content to get the placeholders
	 *
	 * @param string $file Template filename
	 * @return bool|WP_Error
	 */
	public function load_template( $file ) {

		$file = fue_locate_email_template( $file );

		if ( ! $file || ! is_readable( $file ) ) {
			return new WP_Error(
				'fue_email_template',
				sprintf( __( 'The template (%s) could not be found or is not accessible', 'follow_up_emails' ), $file )
			);
		}

		$this->path     = $file;
		$this->contents = file_get_contents( $file );
		$this->name     = $this->get_template_name();
		$this->extract_sections();
		return true;
	}

	/**
	 * Get the contents of the loaded template file.
	 * @return string
	 */
	public function get_contents() {
		return $this->contents;
	}

	/**
	 * Get the sections found in the loaded template file.
	 * @return array
	 */
	public function get_sections() {
		return $this->sections;
	}

	/**
	 * DEAD CODE
	 * Look for the template in the current theme first, then in the FUE directory
	 * and return the absolute path to the file.
	 *
	 * @param string $filename
	 * @return string
	 */
	private function locate_template( $filename ) {

		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'follow-up-emails/emails/' . $filename ) ) {
			return trailingslashit( get_stylesheet_directory() ) . 'follow-up-emails/emails/' . $filename;
		}

		if ( file_exists( trailingslashit( FUE_TEMPLATES_DIR ) . 'emails/' . $filename ) ) {
			return trailingslashit( FUE_TEMPLATES_DIR ) . 'emails/' . $filename;
		}

		// not found
		return false;
	}

	/**
	 * Parse the template name from the template contents.
	 */
	private function get_template_name() {
		if ( file_exists( $this->path ) && is_file( $this->path ) ) {
			$template_data = implode( '', file( $this->path ) );
			if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
				return _cleanup_header_comment( $name[1] );
			}
		}

		return basename( $this->path );
	}

	/**
	 * Look for sections in self.contents and store them in self.sections.
	 */
	private function extract_sections() {
		$sections = fue_str_search( '{section:', '}', $this->contents );

		if ( is_array( $sections ) ) {
			$sections = array_filter( array_map( 'trim', $sections ) );
		} else {
			$sections = array();
		}

		$this->sections = $sections;
	}

}
