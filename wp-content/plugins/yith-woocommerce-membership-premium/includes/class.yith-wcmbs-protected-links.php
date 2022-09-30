<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Protected_Links
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Protected_Links {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Protected_Links
	 * @since 1.0.0
	 */
	protected static $_instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Protected_Links
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
	private function __construct() {
		if ( isset( $_REQUEST['protected_link'] ) ) {
			add_action( 'get_header', array( $this, 'download' ), 999 );
		}

		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
	}

	/**
	 * Check if user has access to media. If user have access forces the file download
	 *
	 * @since 1.0.0
	 */
	public function download() {
		if ( ! isset( $_REQUEST['protected_link'] ) || empty( $_REQUEST['of_post'] ) ) {
			return;
		}

		$protected_id = $_REQUEST['protected_link'];
		$post_id      = $_REQUEST['of_post'];
		$user_id      = get_current_user_id();

		$protected_links = yith_wcmbs_get_protected_links( $post_id );

		if ( ! ! $protected_links && is_array( $protected_links ) && isset( $protected_links[ $protected_id ] ) ) {
			$the_protected_link = $protected_links[ $protected_id ];
			$has_access         = yith_wcmbs_has_full_access( $user_id );
			$membership         = $the_protected_link['membership'];


			if ( ! $has_access ) {
				if ( ! ! $membership && is_array( $membership ) ) {
					$has_access = yith_wcmbs_user_has_membership( $user_id, $membership );
				} else {
					$has_access = yith_wcmbs_user_has_membership( $user_id );
				}
			}

			if ( $has_access ) {
				$file_path = $the_protected_link['link'];
				$filename  = basename( $file_path );
				do_action( 'woocommerce_download_file_force', $file_path, $filename );
			} else {
				wp_die( __( 'You can\'t access to this content.', 'yith-woocommerce-membership' ), __( 'Restricted Access.', 'yith-woocommerce-membership' ) );
			}

		}
	}

	/**
	 * Change upload dir for protected links.
	 *
	 * @param array $pathdata Array of paths.
	 *
	 * @return array
	 * @since 1.4.0
	 */
	public function upload_dir( $pathdata ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['type'] ) && 'membership_protected_link' === $_POST['type'] ) {

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url'] . '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}