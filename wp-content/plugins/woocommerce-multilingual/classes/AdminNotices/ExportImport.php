<?php

namespace WCML\AdminNotices;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Obj;
use WPML\FP\Str;
use function WPML\FP\pipe;

class ExportImport implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	// Cascade of priorities before 10.
	// 7: WPML.
	// 8: WCML.
	// 9: WPML Export and Import.
	const PRIORITY = 8;
	const GROUP    = 'wpml-import-notices';

	const WPML_IMPORT_URL = 'https://wpml.org/documentation/related-projects/wpml-export-and-import/?utm_source=plugin&utm_medium=gui&utm_campaign=wpml-export-import&utm_term=admin-notice';
	const WCML_URL        = 'https://wpml.org/documentation/related-projects/woocommerce-multilingual/?utm_source=plugin&utm_medium=gui&utm_campaign=wcml&utm_term=admin-notice';

	const NOTICE_CLASSES = [
		'wpml-import-notice',
		'wpml-import-notice-from-wcml',
	];

	/** @var \WPML_Notices $notices */
	private $wpmlNotices;

	public function __construct( \WPML_Notices $wpmlNotices ) {
		$this->wpmlNotices = $wpmlNotices;
	}

	public function add_hooks() {
		if ( defined( 'WPML_IMPORT_VERSION' ) ) {
			// WPML Export and Import will take care of this.
			return;
		}

		add_action( 'admin_init', [ $this, 'manageNotice' ], self::PRIORITY );
	}

	public function manageNotice() {
		if ( ! self::isOnMigrationPages() ) {
			return;
		}
		$exportNotices = self::getExportNotices();
		$importNotices = self::getImportNotices();
		$noticeIds     = array_keys( array_merge( $exportNotices, $importNotices ) );

		array_walk( $exportNotices, function( $path, $id ) {
			$this->maybeAddNotice(
				$id,
				$path,
				$this->getExportMessage()
			);
		} );
		array_walk( $importNotices, function( $path, $id ) {
			$this->maybeAddNotice(
				$id,
				$path,
				$this->getImportMessage()
			);
		} );
	}

	/**
	 * @param string $id
	 * @param string $path
	 * @param string $message
	 */
	private function maybeAddNotice( $id, $path, $message ) {
		if ( ! self::isOnPage( $path ) ) {
			return;
		}
		$notice = $this->wpmlNotices->get_new_notice(
			$id,
			$message,
			self::GROUP
		);
		$notice->set_css_class_types( 'info' );
		$notice->set_css_classes( self::NOTICE_CLASSES );
		$notice->add_display_callback( [ \WCML\AdminNotices\ExportImport::class, 'isOnMigrationPages' ] );
		$notice->set_dismissible( true );
		$this->wpmlNotices->add_notice( $notice, true );
	}

	/**
	 * @return array
	 */
	private static function getExportNotices() {
		$notices = [
			'woocommerce-export' => '/edit.php?post_type=product&page=product_exporter',
		];
		if ( defined( 'PMWE_VERSION' ) ) {
			$notices['wp-all-export'] = '/admin.php?page=pmxe-admin-export';
		}
		return $notices;
	}

	/**
	 * @return array
	 */
	private static function getImportNotices() {
		$notices = [
			'woocommerce-import' => '/edit.php?post_type=product&page=product_importer',
		];
		if ( defined( 'PMWI_VERSION' ) ) {
			$notices['wp-all-import'] = '/admin.php?page=pmxi-admin-import';
		}
		return $notices;
	}

	/**
	 * @param  string $path
	 *
	 * @return bool
	 */
	private static function isOnPage( $path ) {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
		) {
			return false;
		}

		if ( false !== strpos( $_SERVER['REQUEST_URI'], $path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isOnMigrationPages() {
		$exportNotices  = self::getExportNotices();
		$importNotices  = self::getImportNotices();
		$migrationPaths = array_values( array_merge( $exportNotices, $importNotices ) );
		foreach ( $migrationPaths as $path ) {
			if ( false !== self::isOnPage( $path ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string
	 */
	private function getExportMessage() {
		return sprintf(
			/* translators: %s is a link. */
			__( 'Migrating your multilingual shop? With %s you can transfer your translated content to a new site, including cross-sells, up-sells, and product attributes.', 'woocommerce-multilingual' ),
			$this->getWpmlImportLink()
		);
	}

	/**
	 * @return string
	 */
	private function getImportMessage() {
		return sprintf(
			/* translators: %1$s and %2$s are both links. */
			__( 'Looking to import your multilingual shop? With %1$s and %2$s in both your original and new site, you can export and import your translations automatically.', 'woocommerce-multilingual' ),
			$this->getWcmlLink(),
			$this->getWpmlImportLink()
		);
	}

	/**
	 * @return string
	 */
	private function getWpmlImportLink() {
		$url   = self::WPML_IMPORT_URL;
		$title = __( 'WPML Export and Import', 'woocommerce-multilingual' );
		return '<a class="wpml-external-link" href="' . esc_url( $url ) . '" title="' . esc_attr( $title ) . '" target="_blank">'
			. esc_html( $title )
			. '</a>';
	}

	/**
	 * @return string
	 */
	private function getWcmlLink() {
		$url   = self::WCML_URL;
		$title = __( 'WooCommerce Multilingual', 'woocommerce-multilingual' );
		return '<a class="wpml-external-link" href="' . esc_url( $url ) . '" title="' . esc_attr( $title ) . '" target="_blank">'
			. esc_html( $title )
			. '</a>';
	}

}