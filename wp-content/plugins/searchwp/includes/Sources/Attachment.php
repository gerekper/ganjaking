<?php

namespace SearchWP\Sources;

use SearchWP\Utils;
use SearchWP\Option;
use SearchWP\Document;

/**
 * Class Attachment is a customized Post Source.
 *
 * @since 4.0
 */
final class Attachment extends Post {

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		global $wpdb;

		parent::__construct( 'attachment' );

		// Extend Post Attributes with Attachment Attributes.
		$this->attributes = array_merge( [
			[	// Document Content.
				'name'    => 'document_content',
				'label'   => __( 'Document Content', 'searchwp' ),
				'default' => Utils::get_min_engine_weight(),
				'data'    => function( $post_id ) {
					$post = get_post( $post_id );

					return is_null( $post ) ? '' : Document::get_content( $post );
				},
				'phrases' => [ [
					'table'  => $wpdb->postmeta,
					'column' => 'meta_value',
					'id'     => 'post_id'
				] ],
			],
			[	// PDF Metadata.
				'name'    => 'pdf_metadata',
				'label'   => __( 'PDF Metadata', 'searchwp' ),
				'default' => false,
				'data'    => function( $post_id ) {
					$post = get_post( $post_id );

					return is_null( $post ) || 'application/pdf' !== $post->post_mime_type
						? '' : Document::get_pdf_metadata( $post );
				},
				'phrases' => [ [
					'table'  => $wpdb->postmeta,
					'column' => 'meta_value',
					'id'     => 'post_id'
				] ],
			],
			[	// Image EXIF.
				'name'    => 'image_exif',
				'label'   => __( 'Image EXIF', 'searchwp' ),
				'default' => false,
				'data'    => function( $post_id ) {
					$post = get_post( $post_id );

					if ( is_null( $post ) || 'image/' !== substr( $post->post_mime_type, 0, 6 ) ) {
						return null;
					}

					$exif = get_post_meta( $post_id, SEARCHWP_PREFIX . 'image_exif', true );
					if ( empty( $exif ) ) {
						$exif = self::get_image_metadata( get_attached_file( $post_id ) );
						update_post_meta( $post_id, SEARCHWP_PREFIX . 'image_exif', $exif );
					}

					return apply_filters( 'searchwp\source\attachment\attribute\image_exif', $exif, [
						'post' => $post,
					] );
				},
				'phrases' => [ [
					'table'  => $wpdb->postmeta,
					'column' => 'meta_value',
					'id'     => 'post_id'
				] ],
			],
		], $this->attributes );

		// Extend Post Rules with Attachment Rules.
		$this->rules = array_merge( [
				$this->filetype_rule(),
				$this->filename_rule(),
			], $this->rules
		);
	}

	/**
	 * Overrides parent determination because Attachments are unique in that they can't be their own parent.
	 *
	 * @since 4.1
	 * @param array $args The arguments for the weight transfer option.
	 * @return string[] Post type names.
	 */
	public function get_potential_post_parent_types( $args, $child_post_type = '' ) {
		return array_filter(
			parent::get_potential_post_parent_types( $args, $child_post_type ),
			function( $source_name ) {
				return 'post' . SEARCHWP_SEPARATOR . $this->get_post_type() !== $source_name;
			}
		);
	}

	/**
	 * Restrict available Posts to this post type with the proper post stati and exclusions.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function db_where() {
		return [
			'relation' => 'AND',
			[ 	// Only include applicable post type.
				'column'  => 'post_type',
				'value'   => 'attachment',
			],
			[ 	// Attachments always have a post_status of 'inherit'.
				'column'  => 'post_status',
				'value'   => 'inherit',
				'compare' => '=',
			],
			[ 	// ID-based limiter.
				'column'  => 'ID',
				'value'   => Utils::get_filtered_post__in(),
				'compare' => 'IN',
				'type'    => 'NUMERIC',
			],
			[ 	// ID-based exclusions.
				'column'  => 'ID',
				'value'   => Utils::get_filtered_post__not_in(),
				'compare' => 'NOT IN',
				'type'    => 'NUMERIC',
			],
		];
	}

	/**
	 * Defines Rule based on file type.
	 *
	 * @since 4.0
	 * @return array
	 */
	protected function filetype_rule() {
		return [
			'name'        => 'filetype',
			'label'       => __( 'File Type', 'searchwp' ),
			'options'     => false,
			'conditions'  => [ 'IN', 'NOT IN' ],
			'values'      => [
				new Option( 'documents', __( 'All Documents', 'searchwp' ) ),
				new Option( 'pdf', __( 'PDFs', 'searchwp' ) ),
				new Option( 'text', __( 'Plain Text', 'searchwp' ) ),
				new Option( 'image', __( 'Images', 'searchwp' ) ),
				new Option( 'video', __( 'Videos', 'searchwp' ) ),
				new Option( 'audio', __( 'Audio', 'searchwp' ) ),
				new Option( 'office', __( 'Office Documents', 'searchwp' ) ),
				new Option( 'openoffice', __( 'OpenOffice Documents', 'searchwp' ) ),
				new Option( 'iwork', __( 'iWork Documents', 'searchwp' ) ),
			],
			'application' => function( $properties ) {
				$mimes = call_user_func_array( 'array_merge', array_map( function( $mime_group ) {
					switch ( $mime_group ) {
						case 'documents':
							$mimes = array_merge(
								$this->mimes['text'],
								$this->mimes['application'],
								$this->mimes['msoffice'],
								$this->mimes['openoffice'],
								$this->mimes['wordperfect'],
								$this->mimes['iwork']
							);
							break;

						case 'pdf':
							$mimes = [ 'application/pdf' ];
							break;

						case 'text':
							$mimes = $this->mimes['text'];
							break;

						case 'image':
							$mimes = $this->mimes['image'];
							break;

						case 'video':
							$mimes = $this->mimes['video'];
							break;

						case 'audio':
							$mimes = $this->mimes['audio'];
							break;

						case 'office':
							$mimes = $this->mimes['msoffice'];
							break;

						case 'openoffice':
							$mimes = $this->mimes['openoffice'];
							break;

						case 'iwork':
							$mimes = $this->mimes['iwork'];
							break;
					}

					return $mimes;
				}, $properties['value'] ) );

				$file_type_wp_query = new \WP_Query( array_merge(
					$this->get_base_wp_query_args(), [
						'post_mime_type' => $mimes,
						'post_status'    => 'inherit',
					]
				) );

				// Return the IDs we already did the work to find if there aren't too many.
				if ( empty( $file_type_wp_query->posts ) ) {
					return [ 0 ];
				} else if ( ! empty( $file_type_wp_query->posts ) && $file_type_wp_query->found_posts < 20 ) {
					return $file_type_wp_query->posts;
				} else {
					return $file_type_wp_query->request;
				}
			},
		];
	}

	/**
	 * Defines Rule based on filename.
	 *
	 * @since 4.1.1.4
	 * @return array
	 */
	protected function filename_rule() {
		return [
			'name'        => 'filename',
			'label'       => __( 'Filename', 'searchwp' ),
			'options'     => false,
			'conditions'  => [ 'LIKE', 'NOT LIKE' ],
			'tooltip'     => __( 'Rule will apply if ANY part of the filename matches (includes upload path, excluding upload base, and is case-insensitive)', 'searchwp' ),
			'application' => function( $properties ) {
				$filename_wp_query = new \WP_Query( [
					'post_type'        => 'attachment',
					'post_status'      => 'inherit',
					'orderby'          => 'none',
					'fields'           => 'ids',
					'nopaging'         => true,
					'suppress_filters' => true,
					'meta_query'       => [ [
						'key'     => '_wp_attached_file',
						'compare' => $properties['condition'],
						'value'   => $properties['value'],
					] ],
				] );

				// Return the IDs we already did the work to find if there aren't too many.
				if ( empty( $filename_wp_query->posts ) ) {
					return [ 0 ];
				} else if ( $filename_wp_query->found_posts < 20 ) {
					return $filename_wp_query->posts;
				} else {
					return $filename_wp_query->request;
				}
			},
		];
	}

	/**
	 * Add class hooks.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function add_hooks( array $params = [] ) {
		parent::add_hooks( $params );

		if ( ! has_action( 'add_meta_boxes', [ $this, 'document_content_meta_box' ] ) ) {
			add_action( 'add_meta_boxes', [ $this, 'document_content_meta_box' ] );
		}

		if ( ! has_action( 'searchwp\source\post\drop', [ $this, 'drop_attachment' ] ) ) {
			add_action( 'searchwp\source\post\drop', [ $this, 'drop_attachment' ], 999 );
		}

		if ( ! has_filter( 'searchwp\indexer\batch_size', [ $this, 'set_batch_size' ] ) ) {
			add_filter( 'searchwp\indexer\batch_size\\' . $this->get_name(), [ $this, 'set_batch_size' ], 99 );
		}

		if ( ! has_action( 'searchwp\index\rebuild', [ $this, 'index_rebuild' ] ) ) {
			add_action( 'searchwp\index\rebuild', [ $this, 'index_rebuild' ] );
		}

		// If this Source is not active we can bail out early.
		if ( isset( $params['active'] ) && ! $params['active'] ) {
			return;
		}

		if ( ! has_action( 'edit_attachment', [ $this, 'document_content_save' ] ) ) {
			add_action( 'edit_attachment', [ $this, 'document_content_save' ], 999 );
		}

		if ( ! has_action( 'add_attachment', [ $this, 'drop_post' ] ) ) {
			add_action( 'add_attachment', [ $this, 'drop_post' ], 999 );
		}

		if ( ! has_action( 'edit_attachment', [ $this, 'drop_post' ] ) ) {
			add_action( 'edit_attachment', [ $this, 'drop_post' ], 999 );
		}

		if ( ! has_action( 'delete_attachment', [ $this, 'drop_post' ] ) ) {
			add_action( 'delete_attachment', [ $this, 'drop_post' ], 999 );
		}
	}

	/**
	 * Callback when Attachment is dropped to also drop the content and skipped flag.
	 *
	 * @param array $args Incoming arguments.
	 *
	 * @since 4.0.32
	 * @return void
	 */
	public function drop_attachment( $args ) {
		if ( apply_filters( 'searchwp\source\attachment\skipped\reset\strict', false ) ) {
			return;
		}

		if ( $args['source']->get_post_type() !== 'attachment' ) {
            return;
		}

		$skipped  = get_post_meta( $args['post_id'], Document::$meta_key . '_skipped', true );

        // If the stored content was not manually edited, remove it and flag the attachment for a new extraction.
        if ( ! $skipped ) {
	        delete_post_meta( $args['post_id'], Document::$meta_key );
        }

		delete_post_meta( $args['post_id'], Document::$meta_key . '_skipped' );
	}

	/**
	 * Callback when index is rebuilt. Removes stored document content and PDF metadata.
	 *
	 * @since 4.0.26
	 * @return void
	 */
	public function index_rebuild() {
		global $wpdb;

		$default = \SearchWP\Settings::get_single( 'document_content_reset', 'boolean' );
		$skipped = apply_filters( 'searchwp\source\attachment\skipped\reset', $default );
		$content = apply_filters( 'searchwp\source\attachment\attribute\document_content\reset', $default );
		$meta    = apply_filters( 'searchwp\source\attachment\attribute\pdf_metadata\reset', $default );
		$exif    = apply_filters( 'searchwp\source\attachment\attribute\image_exif\reset', $default );

		if ( $skipped ) {
			$wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => Document::$meta_key . '_skipped' ] );
		}

		if ( $content ) {
			$wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => SEARCHWP_PREFIX . 'content' ] );
		}

		if ( $meta ) {
			$wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => SEARCHWP_PREFIX . 'content_pdf_metadata' ] );
		}

		if ( $exif ) {
			$wpdb->delete( $wpdb->prefix . 'postmeta', [ 'meta_key' => SEARCHWP_PREFIX . 'image_exif' ] );
		}
	}

	/**
	 * Callback to change the indexer batch size to 1 for this Source.
	 *
	 * @since 4.0.23
	 * @param mixed $size The incoming batch size.
	 * @return int The adjusted batch size.
	 */
	public function set_batch_size( $size ) {
		return absint( $size ) > 3 ? 3 : $size;
	}

	/**
	 * Callback to add Meta Box to facilitate editing parsed document content.
	 *
	 * @since 4.0
	 */
	public function document_content_meta_box( string $post_type ) {
		global $post;

		if ( ! $post instanceof \WP_Post
			|| 'attachment' !== $post_type
			|| ! in_array( $post->post_mime_type, array_merge(
				$this->mimes['text'],
				$this->mimes['msoffice'],
				$this->mimes['openoffice'],
				$this->mimes['wordperfect'],
				$this->mimes['iwork'],
				[ 'application/rtf', 'application/pdf', ]
			) )
		) {
			return;
		}

		// If we're not working with Document Content or PDF Metadata, bail out.
		if (
			! Utils::any_engine_has_source_attribute( $this->attributes['document_content'], $this )
			&& ! Utils::any_engine_has_source_attribute( $this->attributes['pdf_metadata'], $this )
		) {
			return;
		}

		$existing_content = $this->get_existing_document_content( $post );

		add_meta_box(
			SEARCHWP_PREFIX . 'document_content',
			__( 'SearchWP Document Content', 'searchwp' ),
			function( \WP_Post $the_post, array $meta ) use ( $existing_content, $post ) {
				// If there's no existing content, it may be in the queue.
				$skipped = false;
				if ( empty( trim( $existing_content ) ) ) {
					$status  = \SearchWP::$index->get_source_id_status( $this->get_name(), $post->ID );
					$skipped = get_post_meta( $post->ID, Document::$meta_key . '_skipped', true );

					if ( ! empty( $status->queued ) ) {
						?>
						<p><?php esc_html_e( 'This document is currently in the index queue, but has not yet been processed by SearchWP.', 'searchwp' ); ?></p>
						<?php

						return;
					} else if ( ! $skipped ) {
						?>
						<p class="description" style="padding-top: 0.5em;"><em><?php esc_html_e( 'Note: SearchWP was unable to extract content from this document.', 'searchwp' ); ?></em></p>
						<?php
					}
				}

				$existing = $meta['args']['existing_content'];
				$limit    = absint( apply_filters( 'searchwp\source\attachment\attribute\document_content\display_limit', 1000000 ) );

				do_action( 'searchwp\attachment\meta_box\content\before', $the_post );

				if ( $limit > strlen( $existing ) ) {
					wp_nonce_field( SEARCHWP_PREFIX . 'document_content_edit', SEARCHWP_PREFIX . 'document_content_nonce' );
					?>
						<p><?php esc_html_e( 'The content below will be indexed for this file. If you are experiencing unexpected search results, ensure accuracy here.', 'searchwp' ); ?></p>
						<p class="description"><?php esc_html_e( 'If you edit this content and update this Attachment, the changes will persist and be indexed.', 'searchwp' ); ?></p>
						<textarea style="display: block; width: 100%; height: 300px;" name="searchwp_document_content"<?php if ( $skipped ) : ?> placeholder="<?php echo esc_attr( 'SearchWP was unable to parse this file', 'searchwp' ); ?>"<?php endif; ?>><?php if ( $existing ) { echo esc_textarea( $existing ); } ?></textarea>
						<div style="display:none !important;overflow:hidden !important;">
							<textarea style="display: block; width: 100%; height: 300px;" name="searchwp_document_content_original"><?php if ( $existing ) { echo esc_textarea( $existing ); } ?></textarea>
						</div>
					<?php
				} else {
					$size   = function_exists( 'mb_strlen' ) ? mb_strlen( $existing, '8bit' ) : strlen( $existing );
					$sample = wordwrap( $existing, 1000 );
					$sample = explode( "\n", $sample );
					$sample = array_slice( $sample, 0, 100 );
					$sample = implode( ' ', $sample );
					unset( $existing );
					?>
					<p>
						<?php
						echo wp_kses(
							sprintf(
								__( '<strong>NOTE:</strong> This content is too long to display (%s). Here is a <strong>sample from the indexed content</strong>:', 'searchwp' ),
								size_format( $size, 2 )
							),
							[ 'strong' => [] ]
						);
						?>
					</p>
					<textarea style="display: block; width: 100%; height: 9em;" disabled="disabled"><?php echo esc_textarea( $sample ); ?></textarea>
					<p>
						<?php
						echo wp_kses(
							__( "To override this limit you must add the following to your theme's <code>functions.php</code> which will lift this limit:", 'searchwp' ),
							[ 'code' => [] ]
							);
						?>
					</p>
					<textarea style="display: block; width: 100%; height: 5em; font-family: monospace;"><?php
					echo "add_filter( 'searchwp\source\attachment\attribute\document_content\display_limit', function( \$limit ) {\n\treturn " . absint( $size + 100 ) . ";\n} );";
					?></textarea>
					<?php
				}

				if (
					'application/pdf' === $the_post->post_mime_type
					&& Utils::any_engine_has_source_attribute( $this->attributes['pdf_metadata'], $this )
				) {
					$pdf_metadata = Document::get_pdf_metadata( $the_post );

					if ( ! empty( $pdf_metadata ) ) {
						self::output_pdf_metadata( $pdf_metadata );
					}
				}

				do_action( 'searchwp\attachment\meta_box\content\after', $the_post );
			},
			'attachment',
			'advanced',
			'default',
			[ 'existing_content' => $existing_content ]
		);
	}

	/**
	 * Output PDF metadata.
	 *
	 * @since 4.0
	 * @param mixed $pdf_metadata
	 * @return void
	 */
	public static function output_pdf_metadata( $pdf_metadata ) {
		?>
			<div class="searchwp-indexed-pdf-metadata">
				<h3 class="searchwp-indexed-pdf-metadata-title"><?php esc_html_e( 'PDF Metadata', 'searchwp' ); ?></h3>
				<table>
					<thead>
					<tr>
						<th><?php _e( 'Key', 'searchwp' ); ?></th>
						<th><?php _e( 'Value', 'searchwp' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $pdf_metadata as $key => $val ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $key ); ?></strong></td>
							<td>
								<?php
								if ( is_array( $val ) ) {
									$val = array_map( 'esc_html', $val );
									echo implode( '<br />', $val );
								} else {
									echo esc_html( $val );
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<style type="text/css">
				.searchwp-indexed-pdf-metadata {
					padding-top: 1em;
					opacity: 0.7;
				}

				.searchwp-indexed-pdf-metadata-title {
					margin: 0;
				}

				#poststuff .searchwp-indexed-pdf-metadata h3,
				.searchwp-indexed-pdf-metadata h3 {
					padding-left: 0;
					padding-bottom: 0.5em;
				}

				.searchwp-indexed-pdf-metadata table {
					width: 100%;
					border-collapse: collapse;
				}

				.searchwp-indexed-pdf-metadata td {
					padding: 0.5em 0;
					border-top: 1px solid #eee;
				}

				.searchwp-indexed-pdf-metadata table thead {
					display: none;
				}
			</style>
		<?php
	}

	/**
	 * Callback fired when saving documents, saves document content.
	 *
	 * @since 4.0
	 * @param $post_id
	 */
	function document_content_save( $post_id ) {
		if ( ! isset( $_REQUEST['post_type'] ) ) {
			return;
		}

		if ( 'attachment' == $_REQUEST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		}
		else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		if (
			! isset( $_POST[ SEARCHWP_PREFIX . 'document_content_nonce' ] )
			|| ! wp_verify_nonce( $_POST[ SEARCHWP_PREFIX . 'document_content_nonce' ], SEARCHWP_PREFIX . 'document_content_edit' )
		) {
			return;
		}

		$original = isset( $_POST['searchwp_document_content_original'] ) ? sanitize_text_field( $_POST['searchwp_document_content_original'] ) : '';
		$edited   = isset( $_POST['searchwp_document_content'] ) ? sanitize_text_field( $_POST['searchwp_document_content'] ) : '';
		$skipped  = get_post_meta( $post_id, Document::$meta_key . '_skipped', true );

		// If the content was edited, save it.
		if ( $skipped || ( md5( $original ) != md5( $edited ) ) ) {
			do_action( 'searchwp\debug\log', 'Manual content update for attachment ' . $post_id, 'source' );
			update_post_meta( $post_id, Document::$meta_key . '_skipped', true );
			update_post_meta( $post_id, Document::$meta_key, $edited );
		}
	}

	/**
	 * Retrieve existing Document content taking into consideration the limitations of displaying it in browser.
	 *
	 * @since 4.0
	 * @param WP_Post $the_post
	 * @return string
	 */
	public function get_existing_document_content( \WP_Post $the_post ) {
		// Applies only to a limited set of mime types.
		if ( ! in_array( $the_post->post_mime_type, array_merge(
			$this->mimes['text'],
			$this->mimes['msoffice'],
			$this->mimes['openoffice'],
			$this->mimes['wordperfect'],
			$this->mimes['iwork'],
			[ 'application/rtf', 'application/pdf', ]
		) ) ) {
			return '';
		}

		// If Document Content is not added to Media for any Engine, bail out.
		if ( ! Utils::any_engine_has_source_attribute(
			$this->attributes['document_content'],
			$this
		) ) {
			return;
		}

		// If this ID is excluded, bail out.
		if ( ! in_array( $the_post->ID, $this->get_entry_db_records() ) ) {
			return;
		}

		return Document::get_stored_content( $the_post );
	}

	/**
	 * Retrieves image metadata from a file. Heavily based on wp_read_image_metadata() but exists
	 * because wp_read_image_metadata() attempts to normalize the return data, and the checks
	 * performed to do so exclude some common image formats (e.g. made on iPhone) so this function
	 * extracts the metadata retrieval and returns the raw data instead of normalizing it.
	 *
	 * @since 4.0
	 * @param string $file The file to examine.
	 * @return array The extracted metadata.
	 */
	public static function get_image_metadata( string $file ) {
		list( , , $image_type ) = @getimagesize( $file );
		$meta = [];
		$iptc = [];
		$exif = [];

		if ( is_callable( 'iptcparse' ) ) {
			@getimagesize( $file, $iptc );
		}

		$exif_image_types = apply_filters( 'wp_read_image_metadata_types', [ IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ] );

		if ( is_callable( 'exif_read_data' ) && in_array( $image_type, $exif_image_types, true ) ) {
			$exif = @exif_read_data( $file );
		}

		$iptc = wp_kses_post_deep( $iptc );
		$exif = wp_kses_post_deep( $exif );

		$meta = [
			'iptc' => $iptc,
			'exif' => $exif,
		];

		return apply_filters( 'searchwp\source\attachment\attribute\image_exif\read', $meta, $file, $image_type, $iptc, $exif );
	}

	/**
	 * Mime types categorized into something usable.
	 *
	 * @since 4.0
	 * @var string[][]
	 */
	private $mimes = [
		'image' => [
			'image/jpeg',
			'image/gif',
			'image/png',
			'image/bmp',
			'image/tiff',
			'image/x-icon',
		],
		'video' => [
			'video/x-ms-asf',
			'video/x-ms-wmv',
			'video/x-ms-wmx',
			'video/x-ms-wm',
			'video/avi',
			'video/divx',
			'video/x-flv',
			'video/quicktime',
			'video/mpeg',
			'video/mp4',
			'video/ogg',
			'video/webm',
			'video/x-matroska',
		],
		'text' => [
			'text/plain',
			'text/csv',
			'text/tab-separated-values',
			'text/calendar',
			'text/richtext',
			'text/css',
			'text/html',
		],
		'audio' => [
			'audio/mpeg',
			'audio/x-realaudio',
			'audio/wav',
			'audio/ogg',
			'audio/midi',
			'audio/x-ms-wma',
			'audio/x-ms-wax',
			'audio/x-matroska',
		],
		'application' => [
			'application/rtf',
			'application/javascript',
			'application/pdf',
			'application/x-shockwave-flash',
			'application/java',
			'application/x-tar',
			'application/zip',
			'application/x-gzip',
			'application/rar',
			'application/x-7z-compressed',
			'application/x-msdownload',
		],
		'msoffice' => [
			'application/msword',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-write',
			'application/vnd.ms-excel',
			'application/vnd.ms-access',
			'application/vnd.ms-project',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-word.document.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'application/vnd.ms-word.template.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.template',
			'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.presentationml.slide',
			'application/vnd.ms-powerpoint.slide.macroEnabled.12',
			'application/onenote',
		],
		'openoffice' => [
			'application/vnd.oasis.opendocument.text',
			'application/vnd.oasis.opendocument.presentation',
			'application/vnd.oasis.opendocument.spreadsheet',
			'application/vnd.oasis.opendocument.graphics',
			'application/vnd.oasis.opendocument.chart',
			'application/vnd.oasis.opendocument.database',
			'application/vnd.oasis.opendocument.formula',
		],
		'wordperfect' => [
			'application/wordperfect',
		],
		'iwork' => [
			'application/vnd.apple.keynote',
			'application/vnd.apple.numbers',
			'application/vnd.apple.pages',
		],
	];
}
