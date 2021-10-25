<?php

namespace ACP\Export;

/**
 * Handles the exposure of a downloadable file. Receives a string or a file pointer as its input,
 * and outputs the file to the browser, generally (depending on the browser) prompting a
 * "Save as..." dialog box
 * @since 1.0
 */
class DownloadableFile {

	/**
	 * String content to be made downloadable. If set (via the `load_content_string` method) this
	 * content overrides the passed file handle
	 * @since 1.0
	 * @var string
	 */
	private $content_string = '';

	/**
	 * Pointer to file to be made downloadable
	 * @since 1.0
	 * @var resource
	 */
	private $content_fh;

	/**
	 * Load the string content to be made downloadable
	 *
	 * @param string $content String content to be made downloadable
	 *
	 * @since 1.0
	 */
	public function load_content_string( $content ) {
		$this->content_string = $content;
	}

	/**
	 * Get the string content to be made downloadable
	 * @return string String content to be made downloadable
	 * @since 1.0
	 */
	public function get_content_string() {
		return $this->content_string;
	}

	/**
	 * Load the file to be made downloadable
	 *
	 * @param string $fh File pointer to be made downloadable
	 *
	 * @since 1.0
	 */
	public function load_content_fh( $fh ) {
		$this->content_fh = $fh;
	}

	/**
	 * Get the pointer to the file to be made downloadable
	 * @return resource File pointer to be made downloadable
	 * @since 1.0
	 */
	public function get_content_fh() {
		return $this->content_fh;
	}

	/**
	 * Run the actual exporting of the file, i.e., pass it as a downloadable file to the client
	 *
	 * @param string $filename Filename of the downloadable. This is the name of the file as the
	 *                         client downloads it
	 *
	 * @since 1.0
	 */
	public function export( $filename ) {
		// Headers
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Transfer-Encoding: Binary' );
		header( 'Content-disposition: attachment; filename="' . strtolower( sanitize_file_name( $filename ) ) . '"' );

		// Content
		if ( $this->get_content_fh() ) {
			$this->export_from_file();
		} else {
			$this->export_from_string();
		}

		// Stop further execution
		exit;
	}

	/**
	 * Output the downloadable file content from the stored file pointer
	 * @since 1.0
	 */
	protected function export_from_file() {
		// Get file pointer
		$fh = $this->get_content_fh();

		// Pass file contents to output
		rewind( $fh );
		fpassthru( $fh );
		fclose( $fh );
	}

	/**
	 * Output the downloadable file content from the stored content string
	 * @since 1.0
	 */
	protected function export_from_string() {
		echo $this->get_content_string();
	}

}