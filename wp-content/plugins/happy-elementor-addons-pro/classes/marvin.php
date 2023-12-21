<?php
namespace Happy_Addons_Pro;

use Elementor\Controls_Stack;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Marvin {

	/**
	 * Ajax action
	 */
	const ACTION = 'ha_process_ixport';

	/**
	 * Initialize actions
	 */
	public static function init() {
		if ( hapro_is_elementor_version( '>=', '2.8.0' ) ) {
			add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue' ] );
		} else {
			add_action( 'elementor/preview/enqueue_scripts', [ __CLASS__, 'enqueue' ] );
		}

		add_action( 'elementor/preview/enqueue_scripts', [ __CLASS__, 'enqueue_preview' ] );

		add_action( 'wp_ajax_' . self::ACTION, [ __CLASS__, 'process_ixport' ] );
	}

	/**
	 * Process image import request
	 *
	 * @return void
	 */
	public static function process_ixport() {
		$nonce   = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		$type    = isset( $_POST['type'] ) ? sanitize_text_field($_POST['type']) : 'import';
		$content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';

		if ( ! wp_verify_nonce( $nonce, self::ACTION ) ||
			! current_user_can( 'edit_posts' ) ||
			! hapro_get_appsero()->license()->is_valid()
		) {
			wp_send_json_error(
				__( 'You are not allowed to complete this task, thank you.', 'happy-addons-pro' ),
				403
			);
		}

		if ( empty( $content ) ) {
			wp_send_json_error( __( 'Sorry, cannot process empty content!', 'happy-addons-pro' ) );
		}

		if ( $type === 'export' ) {
			$content = self::process_export_content( $content );
		} else {
			$content = self::process_import_content( $content );
		}

		wp_send_json_success( $content );
	}

	protected static function process_export_content( $content = '' ) {
		// Need to be an array to process through elementor db iterator
		$content = [ json_decode( $content, true ) ];

		$xporter = new IXPorter();
		$content = $xporter->replace_elements_ids( $content );
		$content = $xporter->process_export_import_content( $content, 'on_export' );

		return $content;
	}

	protected static function process_import_content( $content = '' ) {
		// Enable svg support
		add_filter( 'upload_mimes', [ __CLASS__, 'add_svg_support' ] );

		// Need to be an array to process through elementor db iterator
		$content = [ json_decode( $content, true ) ];

		$xporter = new IXPorter();
		$content = $xporter->replace_elements_ids( $content );
		$content = $xporter->process_export_import_content( $content, 'on_import' );

		// Disable svg support
		remove_filter( 'upload_mimes', [ __CLASS__, 'add_svg_support' ] );

		return $content;
	}

	public static function add_svg_support( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	public static function enqueue_preview() {
		$_file = HAPPY_ADDONS_PRO_DIR_PATH . 'assets/admin/css/preview.min.css';

		if ( is_readable( $_file ) ) {
			$content = file_get_contents( $_file );
			wp_add_inline_style( 'happy-addons-pro', $content );
		}
	}

	/**
	 * Enqueue assets.
	 *
	 * @return void
	 */
	public static function enqueue() {
		if ( apply_filters( 'happyaddons_marvin_active', true ) && hapro_get_appsero()->license()->is_valid() ) {

			if ( hapro_is_elementor_version( '>=', '2.8.0' ) ) {
				$src = HAPPY_ADDONS_PRO_ASSETS . 'admin/js/marvin-new.min.js';
				$dependencies = [ 'elementor-editor' ];
			} else {
				$src = HAPPY_ADDONS_PRO_ASSETS . 'admin/js/marvin.min.js';
				$dependencies = [ 'elementor-editor' ];
			}

			wp_enqueue_script(
				'marvin',
				$src,
				$dependencies,
				HAPPY_ADDONS_PRO_VERSION,
				true
			);

			wp_localize_script(
				'marvin',
				'marvin',
				[
					'storageKey' => md5( 'LICENSE KEY' ),
					'ajaxURL'    => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( self::ACTION ),
				]
			);
		}
	}
}

Marvin::init();
