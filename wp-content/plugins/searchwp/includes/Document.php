<?php

/**
 * SearchWP Document.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Parser;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Document is responsible for modeling an attachment WP_Post.
 *
 * @since 4.0
 */
class Document extends Parser {

	/**
	 * Meta key that stores parsed Document content.
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $meta_key = SEARCHWP_PREFIX . 'content';

	/**
	 * Retrieve the file content from a Media entry.
	 *
	 * @since 4.0
	 * @param WP_Post $post The Media entry.
	 * @return string
	 */
	public static function get_content( \WP_Post $post ) {
		$mime_type = $post->post_mime_type;
		$filename  = get_attached_file( $post->ID );

		// It's possible that this fires too early to return the file. See #226.
		// Returning here will cause the indexing process to try again.
		if ( empty( $filename ) || ! file_exists( $filename ) ) {
			return '';
		}

		$content = apply_filters( 'searchwp\document\content\stored', self::get_stored_content( $post ), $post );
		$skipped = apply_filters( 'searchwp\document\skip',
			get_post_meta( $post->ID, self::$meta_key . '_skipped', true ), $post );

		if (
			empty( $content )
			&& ! $skipped
			&& ( did_action( 'searchwp\indexer\batch' ) || did_action( 'searchwp\indexer\init' ) )
		) {
			$extracted = self::extract_text( $filename, $mime_type, $post );
			$content   = apply_filters( 'searchwp\document\content\extracted', $extracted, $post );

			if ( empty( $content ) ) {
				// There was an actual error or there's no content.
				// Flag this to omit skip from further extraction attempts.
				update_post_meta( $post->ID, self::$meta_key . '_skipped', true );
			}

			$content = Utils::get_string_from( $content );

			// Prevent sanitization malfunction due to angle brackets.
			$content = str_replace( '<', '&lt;', $content );
			$content = str_replace( '>', '&gt;', $content );

			update_post_meta( $post->ID, self::$meta_key, $content );
		}

		return (string) apply_filters( 'searchwp\document\content', $content, $post );
	}

	/**
	 * Retrieve stored document content.
	 *
	 * @since 4.0
	 * @return mixed|string
	 */
	public static function get_stored_content( \WP_Post $post ) {
		return get_post_meta( $post->ID, self::$meta_key, true );
	}

	/**
	 * Retrieve PDF metadata from a Media entry.
	 *
	 * @since 4.0
	 * @param WP_Post $post The Media entry.
	 * @return string
	 */
	public static function get_pdf_metadata( \WP_Post $post ) {
		$filename = get_attached_file( $post->ID );

		$skipped = apply_filters(
			'searchwp\document\pdf_metadata\skip',
			get_post_meta( $post->ID, self::$meta_key . '_skipped_pdf_metadata', true ),
			$post
		);

		if ( $skipped || ! file_exists( $filename ) || 'application/pdf' !== $post->post_mime_type ) {
			return null;
		}

		// TODO: If an external process is extracting this data, that external process is
		// run each time which adds overhead.
		$metadata = apply_filters( 'searchwp\document\pdf_metadata',
			get_post_meta( $post->ID, self::$meta_key . '_pdf_metadata', true ), $post );

		if ( ! empty( $metadata ) ) {
			return $metadata;
		}

		try {
			if ( did_action( 'searchwp\indexer\batch' ) || did_action( 'searchwp\indexer\init' ) ) {
				$pdf_parser = new \SearchWP\Dependencies\Smalot\PdfParser\Parser();

				$metadata = apply_filters(
					'searchwp\document\pdf_metadata\parsed',
					$pdf_parser->parseFile( $filename )->getDetails(),
					[ 'post_id' => $post->ID ]
				);

				$result = update_post_meta( $post->ID, self::$meta_key . '_pdf_metadata', $metadata );

				if ( ! $result ) {
					// Something went wrong in trying to save the metadata, likely invalid characters.
					do_action( 'searchwp\debug\log', 'PDF metadata saving failed, attempting re-save after parsing', 'parser' );

					// Clean it up and try again.
					$metadata = array_map( function( $value ) {
						return Utils::get_string_from( $value );
					}, $metadata );

					$result = update_post_meta( $post->ID, self::$meta_key . '_pdf_metadata', $metadata );

					if ( ! $result ) {
						do_action( 'searchwp\debug\log', 'PDF metadata parsing failed for ' . $post->ID, 'parser' );
					}
				}
			}
		} catch (\Exception $e) {
			do_action(
				'searchwp\debug\log',
				'PDF metadata extraction failed: ' . sanitize_text_field( $e->getMessage() ),
				'parser'
			);

			update_post_meta( $post->ID, self::$meta_key . '_skipped_pdf_metadata', true );

			$metadata = null;
		}

		return $metadata;
	}
}
