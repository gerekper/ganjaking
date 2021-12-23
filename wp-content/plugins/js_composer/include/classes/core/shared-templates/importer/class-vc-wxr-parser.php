<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/class-vc-wxr-parser-regex.php';
require_once dirname( __FILE__ ) . '/class-vc-wxr-parser-simplexml.php';
require_once dirname( __FILE__ ) . '/class-vc-wxr-parser-xml.php';
/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class Vc_WXR_Parser {
	/**
	 * @param $file
	 * @return array|\WP_Error
	 */
	public function parse( $file ) {
		// Attempt to use proper XML parsers first
		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new Vc_WXR_Parser_SimpleXML();
			$result = $parser->parse( $file );

			// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' !== $result->get_error_code() ) {
				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {
			$parser = new Vc_WXR_Parser_XML();
			$result = $parser->parse( $file );

			// If XMLParser succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'XML_parse_error' !== $result->get_error_code() ) {
				return $result;
			}
		}

		// We have a malformed XML file, so display the error and fallthrough to regex
		if ( isset( $result ) && defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
			echo '<pre>';
			if ( 'SimpleXML_parse_error' === $result->get_error_code() ) {
				foreach ( $result->get_error_data() as $error ) {
					echo esc_html( $error->line . ':' . $error->column ) . ' ' . esc_html( $error->message ) . "\n";
				}
			} elseif ( 'XML_parse_error' === $result->get_error_code() ) {
				$error = $result->get_error_data();
				echo esc_html( $error[0] . ':' . $error[1] ) . ' ' . esc_html( $error[2] );
			}
			echo '</pre>';
			echo '<p><strong>' . esc_html__( 'There was an error when reading this WXR file', 'js_composer' ) . '</strong><br />';
			echo esc_html__( 'Details are shown above. The importer will now try again with a different parser...', 'js_composer' ) . '</p>';
		}

		// use regular expressions if nothing else available or this is bad XML
		$parser = new Vc_WXR_Parser_Regex();

		return $parser->parse( $file );
	}
}
