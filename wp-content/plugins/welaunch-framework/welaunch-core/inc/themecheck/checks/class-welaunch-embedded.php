<?php
/**
 * weLaunch Embedded Class
 *
 * @class weLaunch_Embedded
 * @version 3.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class weLaunch_Embedded
 */
class weLaunch_Embedded implements themecheck {

	/**
	 * Error array.
	 *
	 * @var array
	 */
	protected $error = array();

	/**
	 * Run checker.
	 *
	 * @param array $php_files Files to check.
	 * @param array $css_files Files to check.
	 * @param array $other_files Files to check.
	 *
	 * @return bool
	 */
	public function check( $php_files, $css_files, $other_files ) {

		$ret   = true;
		$check = weLaunch_ThemeCheck::get_instance();
		$welaunch = $check::get_welaunch_details( $php_files );

		if ( $welaunch ) {
			if ( ! isset( $_POST['welaunch_wporg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				checkcount();
				$this->error[] = '<div class="welaunch-error">' . sprintf( '<span class="tc-lead tc-recommended">' . esc_html__( 'RECOMMENDED', 'welaunch-framework' ) . '</span>: ' . esc_html__( 'If you are submitting to WordPress.org Theme Repository, it is', 'welaunch-framework' ) . ' <strong>' . esc_html__( 'strongly', 'welaunch-framework' ) . '</strong> ' . esc_html__( 'suggested that you read', 'welaunch-framework' ) . ' <a href="%s" target="_blank">' . esc_html__( 'this document', 'welaunch-framework' ) . '</a>, ' . esc_html__( 'or your theme will be rejected because of weLaunch.', 'welaunch-framework' ), '//docs.welaunch.io/core/wordpress-org-submissions/' ) . '</div>';
				$ret           = false;
			} else {
				// TODO Granular WP.org tests!!!
				// Check for Tracking.
				checkcount();
				$tracking = $welaunch['dir'] . 'inc/tracking.php';
				if ( file_exists( $tracking ) ) {
					$this->error[] = '<div class="welaunch-error">' . sprintf( '<span class="tc-lead tc-required">' . esc_html__( 'REQUIRED', 'welaunch-framework' ) . '</span>: ' . esc_html__( 'You MUST delete', 'welaunch-framework' ) . ' <strong> %s </strong>, ' . esc_html__( 'or your theme will be rejected by WP.org theme submission because of weLaunch.', 'welaunch-framework' ), $tracking ) . '</div>';
					$ret           = false;
				}

				// Embedded CDN package
				// use_cdn
				// Arguments.
				checkcount();
				$args          = '<ol>';
				$args         .= "<li><code>'save_defaults' => false</code></li>";
				$args         .= "<li><code>'use_cdn' => false</code></li>";
				$args         .= "<li><code>'customizer_only' => true</code> Non-Customizer Based Panels are Prohibited within WP.org Themes</li>";
				$args         .= "<li><code>'database' => 'theme_mods'</code> (' . esc_html__( 'Optional', 'welaunch-framework' ) . ')</li>";
				$args         .= '</ol>';
				$this->error[] = '<div class="welaunch-error"><span class="tc-lead tc-recommended">' . esc_html__( 'RECOMMENDED', 'welaunch-framework' ) . '</span>: ' . esc_html__( 'The following arguments MUST be used for WP.org submissions, or you will be rejected because of your weLaunch configuration.', 'welaunch-framework' ) . $args . '</div>';
			}
		}

		return $ret;
	}

	/**
	 * Return error array.
	 *
	 * @return array
	 */
	public function getError() {
		return $this->error;
	}

}

$themechecks[] = new weLaunch_Embedded();
