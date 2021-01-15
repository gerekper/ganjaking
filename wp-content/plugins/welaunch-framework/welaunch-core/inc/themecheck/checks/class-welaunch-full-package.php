<?php
/**
 * weLaunch Full_Pakage Class
 *
 * @class weLaunch_Full_Package
 * @version 3.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class weLaunch_Full_Package
 */
class weLaunch_Full_Package implements themecheck {

	/**
	 * Themecheck error array.
	 *
	 * @var array $error Error storage.
	 */
	protected $error = array();

	/**
	 * Check files.
	 *
	 * @param array $php_files File to check.
	 * @param array $css_files Files to check.
	 * @param array $other_files Files to check.
	 *
	 * @return bool
	 */
	public function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$check = weLaunch_ThemeCheck::get_instance();
		$welaunch = $check::get_welaunch_details( $php_files );

		if ( $welaunch ) {

			$blacklist = array(
				'.tx'                              => esc_html__( 'weLaunch localization utilities', 'welaunch-framework' ),
				'bin'                              => esc_html__( 'weLaunch Resting Diles', 'welaunch-framework' ),
				'codestyles'                       => esc_html__( 'weLaunch Code Styles', 'welaunch-framework' ),
				'tests'                            => esc_html__( 'weLaunch Unit Testing', 'welaunch-framework' ),
				'class-welaunch-framework-plugin.php' => esc_html__( 'weLaunch Plugin File', 'welaunch-framework' ),
				'bootstrap_tests.php'              => esc_html__( 'weLaunch Boostrap Tests', 'welaunch-framework' ),
				'.travis.yml'                      => esc_html__( 'CI Testing FIle', 'welaunch-framework' ),
				'phpunit.xml'                      => esc_html__( 'PHP Unit Testing', 'welaunch-framework' ),
			);

			$errors = array();

			foreach ( $blacklist as $file => $reason ) {
				checkcount();
				if ( file_exists( $welaunch['parent_dir'] . $file ) ) {
					$errors[ $welaunch['parent_dir'] . $file ] = $reason;
				}
			}

			if ( ! empty( $errors ) ) {
				$error  = '<span class="tc-lead tc-required">REQUIRED</span> ' . esc_html__( 'It appears that you have embedded the full weLaunch package inside your theme. You need only embed the', 'welaunch-framework' ) . ' <strong>weLaunch_Core</strong> ' . esc_html__( 'folder. Embedding anything else will get your rejected from theme submission. Suspected weLaunch package file(s):', 'welaunch-framework' );
				$error .= '<ol>';

				foreach ( $errors as $key => $e ) {
					$error .= '<li><strong>' . $e . '</strong>: ' . $key . '</li>';
				}

				$error        .= '</ol>';
				$this->error[] = '<div class="welaunch-error">' . $error . '</div>';
				$ret           = false;
			}
		}

		return $ret;
	}

	/**
	 * Retrieve errors.
	 *
	 * @return array
	 */
	public function getError() {
		return $this->error;
	}
}

$themechecks = array();

$themechecks[] = new weLaunch_Full_Package();
