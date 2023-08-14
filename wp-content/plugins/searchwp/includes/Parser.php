<?php

/**
 * SearchWP Parser.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

include_once ABSPATH . 'wp-admin/includes/file.php';

/**
 * Class Parser is responsible for extracting text from files.
 *
 * @since 4.0
 */
class Parser {

	/**
	 * Extract text from this document.
	 *
	 * @since 4.0
	 * @param string $file      The full file path.
	 * @param string $mime_type The file's mime type. Will be detected if not provided.
	 * @param mixed  $data      Additional data.
	 * @return string
	 */
	public static function extract_text( string $file, string $mime_type = '', $data = null ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( empty( $mime_type ) && function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $file );
		}

		$mime_class = false;
		foreach ( self::$mimes as $mimes_class => $mimes_class_mimes ) {
			if ( in_array( $mime_type, $mimes_class_mimes ) ) {
				$mime_class = $mimes_class;
				break;
			}
		}

		if ( empty( $mime_class ) ) {
			return false;
		}

		switch ( $mime_class ) {
			case 'pdf':
				$content = self::extract_pdf_content( $file, $data );
				break;

			case 'text':
				$content = self::extract_text_content( $file, $data );
				break;

			case 'richtext':
				$content = self::extract_rich_text_content( $file, $data );
				break;

			case 'msoffice_word':
				// .doc is not supported.
				if ( 'application/msword' !== $mime_type ) {
					$content = self::extract_msoffice_docx_text( $file, $data, $mime_type );
				}
				break;

			case 'msoffice_excel':
				$content = self::extract_msoffice_excel_text( $file, $data, $mime_type );
				break;

			case 'msoffice_powerpoint':
				$content = self::extract_msoffice_powerpoint_text( $file, $data, $mime_type );
				break;

			default:
				$content = '';
		}

		return $content;
	}

	/**
	 * Extract PDF content from the file.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @return string|boolean
	 */
	private static function extract_pdf_content( string $file, $data = null ) {
		$content = apply_filters( 'searchwp\parser\pdf', '', [ 'file' => $file, 'data' => $data ] );

		// If the content was populated externally, bail out.
		if ( ! file_exists( $file ) || ! empty( $content ) ) {
			return $content;
		}

		$pdf_parser = new \SearchWP\Dependencies\Smalot\PdfParser\Parser();
		try {
			$content = (string) $pdf_parser->parseFile( $file )->getText();
		} catch (\Exception $e) {
			do_action(
				'searchwp\debug\log',
				'PDF text extraction failed: ' . sanitize_text_field( $e->getMessage() ),
				'parser'
			);

			$content = false;
		}

		return $content;
	}

	/**
	 * Extract plaintext content from the file.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @return string|boolean
	 */
	private static function extract_text_content( string $file, $data = null) {
		$content = apply_filters( 'searchwp\parser\text', '', [ 'file' => $file, 'data' => $data ] );

		// If the content was populated externally, bail out.
		if ( ! file_exists( $file ) || ! empty( $content ) ) {
			return $content;
		}

		return self::wp_filesystem_get_contents( $file );
	}

	/**
	 * Extract Rich text content from the file.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @return string|boolean
	 */
	private static function extract_rich_text_content( string $file, $data = null) {
		$content = apply_filters( 'searchwp\parser\richtext', '', [ 'file' => $file, 'data' => $data ] );

		// If the content was populated externally, bail out.
		if ( ! file_exists( $file ) || ! empty( $content ) ) {
			return $content;
		}

		$rtf_content = self::wp_filesystem_get_contents( $file );
		$document    = new \SearchWP\Dependencies\RtfHtmlPhp\Document( $rtf_content );

		// Reduce the document to Text Objects.
		$reduce_rtf_to_text = function( $group, $content ) use ( &$reduce_rtf_to_text ) {
			if ( ! empty( $group->children ) && is_array( $group->children ) ) {
				foreach ( $group->children as $entry ) {
					if ( $entry instanceof \SearchWP\Dependencies\RtfHtmlPhp\Text ) {
						$content .= htmlspecialchars( $entry->text, ENT_NOQUOTES, 'UTF-8' );
					}
					if ( $entry instanceof \SearchWP\Dependencies\RtfHtmlPhp\Group && ! empty( $entry->children ) ) {
						$content .= $reduce_rtf_to_text( $entry->children, $content );
					}
				}
			}

			return $content;
		};

		return $reduce_rtf_to_text( $document->root, '' );
	}

	/**
	 * Extract text content from the file using WP_Filesystem.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @return string|boolean
	 */
	private static function wp_filesystem_get_contents( string $file ) {
		global $wp_filesystem;

		WP_Filesystem();

		$contents = '';

		if ( method_exists( $wp_filesystem, 'exists' ) && method_exists( $wp_filesystem, 'get_contents' ) ) {
			$contents = $wp_filesystem->exists( $file ) ? $wp_filesystem->get_contents( $file ) : '';
		}

		return $contents;
	}

	/**
	 * Extract text from this .docx.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @param string $mime_type File mime type.
	 * @return bool|mixed|string
	 */
	private static function extract_msoffice_docx_text( string $file, $data, string $mime_type ) {
		if ( false !== strpos( $mime_type, 'opendocument' ) ) {
			$content = self::get_file_content_from_package( $file, 'content.xml' );
		} else {
			$content = self::get_file_content_from_package( $file, 'word/document.xml' ); // Stored in the .docx zip.
		}

		return $content;
	}

	/**
	 * Retrieve embedded file content from MSOffice package.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param string $mime_type File mime type.
	 * @return string
	 */
	private static function get_file_content_from_package( string $file, string $stored_xml_filename ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			do_action( 'searchwp\debug\log', 'Document parsing failed: ZipArchive not available', 'parser' );
			return '';
		}

		$output_text = '';
		$zip_handle  = new \ZipArchive;

		if ( true === $zip_handle->open( $file ) ) {
			if ( false !== ( $xml_index = $zip_handle->locateName( $stored_xml_filename ) ) ) {
				$xml_datas   = $zip_handle->getFromIndex( $xml_index );
				$output_text = self::get_xml_content( $xml_datas );
			}

			$zip_handle->close();
		} else {
			do_action( 'searchwp\debug\log', 'Document parsing failed: unable to open file', 'parser' );
		}

		return $output_text;
	}

	/**
	 * Use DOMDocument to get partially cleaned XML content
	 *
	 * @since 4.0
	 * @param string $data
	 * @return mixed|string
	 */
	private static function get_xml_content( $data = '' ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			do_action( 'searchwp\debug\log', 'Document parsing ERROR: DOMDocument not found', 'parser' );
			return '';
		}

		$xml_handle = new \DOMDocument();
		$xml_handle->loadXML( $data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING );

		return wp_strip_all_tags( str_replace( '<', ' <', $xml_handle->saveXML() ) );
	}

	/**
	 * Extract text from Excel document
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @param string $mime_type File mime type.
	 * @return string
	 */
	private static function extract_msoffice_excel_text( string $file, $data, string $mime_type ) {
		if ( false !== strpos( $mime_type, 'opendocument' ) ) {
			$content = self::get_file_content_from_package( $file, 'content.xml' );
		} else {
			$content = self::get_file_content_from_package( $file, 'xl/sharedStrings.xml' ); // Stored in the .xlsx zip.
		}

		return $content;
	}

	/**
	 * Extract text from PowerPoint file.
	 *
	 * @since 4.0
	 * @param string $file The full path to the file.
	 * @param mixed  $data Additional data for this call.
	 * @param string $mime_type File mime type.
	 * @return string
	 */
	private static function extract_msoffice_powerpoint_text( string $file, $data, string $mime_type ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			do_action( 'searchwp\debug\log', 'Document parsing failed: ZipArchive not available', 'parser' );
			return '';
		}

		$zip_handle  = new \ZipArchive;
		$output_text = '';

		if ( false !== strpos( $mime_type, 'opendocument' ) ) {
			$output_text = self::get_file_content_from_package( $file, 'content.xml' );
		} else {
			if ( true === $zip_handle->open( $file ) ) {

				$slide_number = 1; // Loop through slide files.

				while ( false !== (
					$xml_index = $zip_handle->locateName( 'ppt/slides/slide' . absint( $slide_number ) . '.xml' ) ) ) {
					$xml_datas = $zip_handle->getFromIndex( $xml_index );
					$output_text .= ' ' . self::get_xml_content( $xml_datas );
					$slide_number++;
				}

				$zip_handle->close();
			}
		}

		return $output_text;
	}

	/**
	 * Collection of supported MIME types.
	 *
	 * @since 4.0
	 * @var string[][]
	 */
	protected static $mimes = [
		'pdf' => [
			'application/pdf',
		],
		'text' => [
			'text/plain',
			'text/csv',
			'text/tab-separated-values',
			'text/calendar',
			'text/css',
			'text/html',
		],
		'richtext' => [
			'text/richtext',
			'application/rtf',
		],
		'msoffice_word' => [
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.oasis.opendocument.text',
		],
		'msoffice_excel' => [
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.oasis.opendocument.spreadsheet',
			'application/vnd.oasis.opendocument.chart',
			'application/vnd.oasis.opendocument.database',
			'application/vnd.oasis.opendocument.formula',
		],
		'msoffice_powerpoint' => [
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'application/vnd.oasis.opendocument.presentation',
			'application/vnd.oasis.opendocument.graphics',
		],
	];
}
