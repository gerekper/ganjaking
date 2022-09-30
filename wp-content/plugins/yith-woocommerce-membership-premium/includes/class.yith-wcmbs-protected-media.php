<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Protected_Media
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Protected_Media {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Protected_Media
	 * @since 1.0.0
	 */
	protected static $_instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Protected_Media
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct() {

		if ( is_admin() ) {
			add_filter( 'media_send_to_editor', array( $this, 'media_send_to_editor' ), 10, 3 );
			add_filter( 'attachment_fields_to_edit', array( $this, 'add_restrict_access_in_media_uploader' ), 10, 2 );
		}
		if ( isset( $_GET['protected_media'] ) ) {
			add_action( 'init', array( $this, 'download_protected_media' ), 999 );
		}

	}


	public function add_restrict_access_in_media_uploader( $form_fields, $post ) {
		// hide in Attachment Edit Post page
		$screen = get_current_screen();
		if ( $screen && $screen->id == 'attachment' ) {
			return $form_fields;
		}

		ob_start();
		$restrict_access_plan       = yith_wcmbs_get_plans_meta_for_post( $post->ID );
		$restrict_access_plan_delay = get_post_meta( $post->ID, '_yith_wcmbs_plan_delay', true );

		yith_wcmbs_get_view( '/media/restrict_access.php', array(
			'post'                 => $post,
			'restrict_access_plan' => $restrict_access_plan,
			'plan_delay'           => $restrict_access_plan_delay,
		) );

		$html = ob_get_clean();

		$form_fields['yith_wcmbs_restrict_access'] = array(
			'label' => __( 'Allow access', 'yith-woocommerce-membership' ),
			'input' => 'html',
			'html'  => $html,
		);

		return $form_fields;
	}


	/**
	 * Filter the HTML markup for a media item sent to the editor.
	 *
	 * @param string $html       HTML markup for a media item sent to the editor.
	 * @param int    $send_id    The first key from the $_POST['send'] data.
	 * @param array  $attachment Array of attachment metadata.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function media_send_to_editor( $html, $send_id, $attachment ) {
		$restrict_access_plan = yith_wcmbs_get_plans_meta_for_post( $send_id );

		if ( ! empty( $restrict_access_plan ) ) {
			$title = get_the_title( $send_id );

			if ( ! empty( $attachment['image_alt'] ) ) {
				$title = $attachment['image_alt'];
			}

			if ( ! empty( $attachment['post_title'] ) ) {
				$title = $attachment['post_title'];
			}

			return "[protected_media id={$send_id}]{$title}[/protected_media]";
		}

		return $html;
	}


	/**
	 * Check if user has access to media. If user have access forces the file download
	 *
	 * @since 1.0.0
	 */
	public function download_protected_media() {
		$media_id = $_GET['protected_media'];
		$user_id  = get_current_user_id();
		$manager  = YITH_WCMBS_Manager();

		if ( $manager->user_has_access_to_post( $user_id, $media_id ) ) {
			$file_path = wp_get_attachment_url( $media_id );
			$filename  = basename( $file_path );

			header( "X-Robots-Tag: noindex, nofollow", true );
			header( "Content-Type: " . $this->get_download_content_type( $file_path ) );
			header( "Content-Description: File Transfer" );
			header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
			header( "Content-Transfer-Encoding: binary" );
			if ( $size = @filesize( $file_path ) ) {
				header( "Content-Length: " . $size );
			}

			self::readfile_chunked( $file_path );
			exit;
		} else {
			wp_die( __( 'You can\'t access to this content.', 'yith-woocommerce-membership' ), __( 'Restricted Access.', 'yith-woocommerce-membership' ) );
		}
	}

	/**
	 * Read file chunked.
	 *
	 * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/.
	 *
	 * @param string $file File.
	 *
	 * @return bool Success or fail
	 */
	public static function readfile_chunked( $file ) {
		if ( ! defined( 'YITH_WCMBS_CHUNK_SIZE' ) ) {
			define( 'YITH_WCMBS_CHUNK_SIZE', 1024 * 1024 );
		}
		$handle = @fopen( $file, 'r' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen

		if ( false === $handle ) {
			return false;
		}

		$read_length = (int) YITH_WCMBS_CHUNK_SIZE;

		while ( ! @feof( $handle ) ) { // @codingStandardsIgnoreLine.
			echo @fread( $handle, $read_length ); // @codingStandardsIgnoreLine.
			if ( ob_get_length() ) {
				ob_flush();
				flush();
			}
		}

		return @fclose( $handle ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fclose
	}


	/**
	 * Get content type of a download
	 *
	 * @param string $file_path
	 *
	 * @return string
	 * @access private
	 */
	private static function get_download_content_type( $file_path ) {
		$file_extension = strtolower( substr( strrchr( $file_path, "." ), 1 ) );
		$ctype          = "application/force-download";

		foreach ( get_allowed_mime_types() as $mime => $type ) {
			$mimes = explode( '|', $mime );
			if ( in_array( $file_extension, $mimes ) ) {
				$ctype = $type;
				break;
			}
		}

		return $ctype;
	}
}

/**
 * Unique access to instance of YITH_WCMBS_Protected_Media class
 *
 * @return YITH_WCMBS_Protected_Media
 * @since 1.0.0
 */
function YITH_WCMBS_Protected_Media() {

	return YITH_WCMBS_Protected_Media::get_instance();
}