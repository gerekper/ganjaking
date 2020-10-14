<?php
/*
Plugin Name: SearchWP Xpdf Integration
Plugin URI: https://searchwp.com/
Description: Uses Xpdf (pdftotext) to extract content from PDF files during indexing
Version: 1.2.0
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2013-2020 SearchWP

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_XPDF_VERSION' ) ) {
	define( 'SEARCHWP_XPDF_VERSION', '1.2.0' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Xpdf_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}


/**
 * Set up the updater
 *
 * @return bool|SWP_Xpdf_Updater
 */
function searchwp_xpdf_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// instantiate the updater to prep the environment
	$searchwp_xpdf_updater = new SWP_Xpdf_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33650,
			'version'   => SEARCHWP_XPDF_VERSION,
			'license'   => $license_key,
			'item_name' => 'Xpdf Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_xpdf_updater;
}

add_action( 'admin_init', 'searchwp_xpdf_update_check' );

/**
 * Class SearchWPXpdf
 */
class SearchWPXpdf {

	// required for all SearchWP extensions
	public $public                = true;                // should be shown in Extensions menu on SearchWP Settings screen
	public $slug                  = 'xpdf-integration';  // slug used for settings screen(s)
	public $name                  = 'Xpdf Integration';  // name used in various places
	public $min_searchwp_version  = '2.5.7';             // used in min version check

	private $xpdfPath = '';
	private $version = SEARCHWP_XPDF_VERSION;
	private $prefix = 'swp_xpdf_';
	private $settings;

    /**
     * SearchWPXpdf constructor.
     */
    function __construct() {
		$this->url      = plugins_url( 'searchwp-xpdf' );
		$this->settings = get_option( $this->prefix . 'settings' );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 99 );
		add_action( 'admin_init', array( $this, 'init_settings' ) );

		add_filter( 'searchwp\extensions', array( $this, 'register' ), 10 );
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );
	}

    /**
     * Initialize the settings
     */
	function init_settings() {
		add_settings_section(
			$this->prefix . 'settings',
			'SearchWP Xpdf Integration Settings',
			array( $this, 'settings_callback' ),
			$this->prefix
		);

		add_settings_field(
			$this->prefix . 'settings_field',
			'Settings',
			array( $this, 'settings_field_callback' ),
			$this->prefix,
			$this->prefix . 'settings'
		);

		register_setting(
			$this->prefix . 'settings',
			$this->prefix . 'settings',
			array( $this, 'validate_settings' )
		);
	}

    /**
     * Callback for the settings
     */
	function settings_callback() {}

    /**
     * Callback for settings field
     */
	function settings_field_callback() {
		?><!--suppress HtmlFormInputWithoutLabel -->
		<input type="text" name="<?php echo esc_attr( $this->prefix ); ?>settings" id="<?php echo esc_attr( $this->prefix ); ?>settings" value="SearchWP Term Synonyms" /><?php
	}

    /**
     * Settings validation callback
     *
     * @param $input
     *
     * @return mixed
     */
	function validate_settings( $input ) {
		if ( isset( $input['pdfid'] ) ) {
			$input['pdfid'] = absint( $input['pdfid'] );
		}

		return $input;
	}

    /**
     * Register as a SearchWP Extension
     *
     * @param $extensions
     *
     * @return mixed
     */
	function register( $extensions ) {
		$extensions['Xpdf'] = __FILE__;

		return $extensions;
	}

	/**
	 * Retrieve the actual command to execute pdftotext
	 *
	 * @return string
	 */
	function getXpdfCommand( $filename ) {
		// The Xpdf project was seemingly superceded by XpdfReader, which changed the command
		if ( apply_filters( 'searchwp_xpdf_legacy_command', false ) ) {
			$cmd = $this->xpdfPath . ' "' . $filename . '" - -enc UTF-8';
		} else {
			// Since the options are now nestled in the middle of the command, we need filters for encrypted PDFs
			$owner_password = apply_filters( 'searchwp_xpdf_owner_password', '',  $filename );
			$user_password = apply_filters( 'searchwp_xpdf_user_password', '',  $filename );
			$encoding = apply_filters( 'searchwp_xpdf_encoding', 'UTF-8',  $filename );

			$cmd_owner_password = '';
			if ( ! empty( $owner_password ) ) {
				$cmd_owner_password = ' -opw ' . $owner_password;
			}

			$cmd_user_password = '';
			if ( ! empty( $user_password ) ) {
				$cmd_user_password = ' -upw ' . $user_password;
			}

			$cmd_enc = '';
			if ( ! empty( $encoding ) ) {
				$cmd_enc = ' -enc ' . $encoding;
			}

			$cmd = $this->xpdfPath . $cmd_owner_password . $cmd_user_password . $cmd_enc . ' "' . $filename . '" -';
		}

		$cmd = apply_filters( 'searchwp_xpdf_command', $cmd, $filename );

		return $cmd;
	}

    /**
     * The settings view
     */
	function view() {
		$this->xpdfPath = apply_filters( 'searchwp_xpdf_path', '' ); ?>
		<?php if ( isset( $this->settings['pdfid'] ) && absint( $this->settings['pdfid'] ) > 0 ) : ?>
            <?php $pdfID = absint( $this->settings['pdfid'] ); ?>
			<div class="searchwp-xpdf-integration-results">
				<h3><?php esc_html_e( 'Results', 'searchwp' ); ?></h3>
				<p><?php esc_html_e( 'Attempted PDF text extraction via Xpdf on post ', 'searchwp' ); ?> <strong><?php echo esc_html( $pdfID ); ?></strong></p>
				<h3><?php esc_html_e( 'Log', 'searchwp' ); ?></h3>
				<div class="searchwp-xpdf-integration-log">
					<?php
					$continue = true;
					$content = '';

					$filename = get_attached_file( $pdfID );
					echo 'Attempting text extraction of ' . esc_html( $pdfID ) . '<br />';

					if ( $filename ) {
						echo 'File: ' . esc_html( $filename ) . '<br />';
					} else {
						echo 'Submitted ID was not for a file, aborting<br />';
						$continue = false;
					}

					// see if Xpdf exists
					if ( $continue && file_exists( $this->xpdfPath ) ) {
						echo 'Xpdf (pdftotext) was found, continuing<br />';
					} elseif ( $continue ) {
						echo 'Xpdf (pdftotext) was not found, aborting<br />';
						$continue = false;
					}

					// see if the file exists
					if ( $continue && file_exists( $filename ) ) {
						echo 'File was found, continuing<br />';
					} elseif ( $continue ) {
						echo 'File was not found, aborting<br />';
						$continue = false;
					}

					// make sure it's a PDF
					$checkFileType = wp_check_filetype( $filename );
					if ( $continue && 'pdf' === strtolower( $checkFileType['ext'] ) ) {
						echo 'File is a PDF, continuing<br />';
					} elseif ( $continue ) {
						echo 'File is not a PDF, aborting<br />';
						$continue = false;
					}

					if ( $continue ) {
						// generate the full command to Xpdf's pdftotext binary
						if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
							$filename = str_replace( '/', '\\', $filename );
						}
						$cmd = $this->getXpdfCommand( $filename );

						echo 'Executing command ' . esc_html( $cmd ) . '<br />';

						// @codingStandardsIgnoreStart
						// fire Xpdf
						@exec( $cmd, $output, $exitCode );
						// @codingStandardsIgnoreEnd

						if ( isset( $exitCode ) ) {
							echo 'Command exited with code ' . esc_html( $exitCode ) . '<br />';
						} else {
							echo 'Command exited with NO exit code<br />';
						}

						// grab the content
						$content = isset( $exitCode ) && 0 === $exitCode ? implode( ' ', $output ) : '';

						// clean up a little bit
						$content = trim( str_replace( "\n", ' ', $content ) );
						$content = sanitize_text_field( $content );

						echo 'Found characters: ' . esc_html( strlen( $content ) ) . '<br />';
					} ?>
				</div>
				<h3><?php esc_html_e( 'Extracted Text', 'searchwp' ); ?></h3>
				<div class="searchwp-xpdf-integration-extracted-content">
					<?php echo esc_html( $content ); ?>
				</div>
			</div>
			<?php
			// this test gets run ONCE only, kill the flag
			update_option( $this->prefix . 'settings', array(
                'pdfid' => false
            ) );
			?>
			<!--suppress CssUnusedSymbol -->
            <style type="text/css">
				#setting-error-settings_updated {
                    display:none !important;
                }

                .searchwp-xpdf-integration-results {
					border:1px solid #ccc;
					background:#fff;
					border-radius:3px;
					padding:10px;
					-moz-box-sizing:border-box;
					box-sizing:border-box;
				}

                .searchwp-xpdf-integration-log,
				.searchwp-xpdf-integration-extracted-content {
					font-family:monospace;
					padding-bottom:10px;
				}
			</style>
		<?php endif; ?>
		<h3><?php esc_html_e( 'Test Xpdf Integration', 'searchwp' ); ?></h3>
		<p><?php esc_html_e( 'After uploading a PDF to your Media library, run a manual test with Xpdf to view what it extracts from your PDF.', 'searchwp' ); ?></p>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post" id="swp-xpdf-test-wrapper">
			<div style="display:none;">
				<?php do_settings_sections( $this->prefix ); ?>
				<?php settings_fields( $this->prefix . 'settings' ); ?>
			</div>
			<p>
				<label for="<?php echo esc_attr( $this->prefix ); ?>settings[pdfid]"><?php esc_html_e( 'PDF Post ID', 'searchwp' ); ?></label>
				<input type="number" class="small" id="<?php echo esc_attr( $this->prefix ); ?>settings[pdfid]" name="<?php echo esc_attr( $this->prefix ); ?>settings[pdfid]" value="" />
			</p>
			<input type="submit" name="submit" id="submit" class="button" value="<?php esc_html_e( 'Test Text Extraction', 'searchwp' ); ?>" />
		</form>
	<?php }

    /**
     * Initializer
     */
	function init() {
		$this->xpdfPath = apply_filters( 'searchwp_xpdf_path', '' );

		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_filter( 'searchwp_external_pdf_processing', array( $this, 'extract_pdf_content' ), 10, 2 );

		add_filter( 'searchwp\parser\pdf', array( $this, 'extract_pdf_content' ), 10, 2 );
	}

    /**
     * Use pdftotext to extract content
     *
     * @param $content
     * @param $filename
     *
     * @return string
     */
	function extract_pdf_content( $content, $filename ) {
		if ( is_array( $filename ) ) {
			// SearchWP 4.0 compat.
			$filename = $filename['file'];
		}

		// make sure the file exists and the Xpdf path was provided
		if ( ! file_exists( $filename ) && ! file_exists( $this->xpdfPath ) ) {
			return $content;
		}

		// make sure it's a PDF
		$checkFileType = wp_check_filetype( $filename );
		if ( 'pdf' !== strtolower( $checkFileType['ext'] ) ) {
			return $content;
		}

		// generate the full command to Xpdf's pdftotext binary
		if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) ) ) {
			$filename = str_replace( '/', '\\', $filename );
		}
		$cmd = $this->getXpdfCommand( $filename );

		// @codingStandardsIgnoreStart
		// fire Xpdf
		@exec( $cmd, $output, $exitCode );
		// @codingStandardsIgnoreEnd

		// grab the content
		$content = isset( $exitCode ) && 0 === $exitCode ? implode( ' ', $output ) : '';

		// clean up a little bit
		$content = trim( str_replace( "\n", ' ', $content ) );
		$content = sanitize_text_field( $content );

		return $content;
	}

    /**
     * Output compat info if necessary
     */
	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP must be active to use this Extension' ); ?>
					</div>
				</td>
			</tr>
		<?php } else if ( function_exists( 'SWP' ) ) { ?>
			<?php $searchwp = SearchWP::instance(); ?>
			<?php if ( version_compare( $searchwp->version, '1.3.3', '<' ) ) { ?>
				<tr class="plugin-update-tr searchwp">
					<td colspan="3" class="plugin-update">
						<div class="update-message">
							<?php esc_html_e( 'SearchWP Xpdf Integration requires SearchWP 2.5.7 or greater', 'searchwp' ); ?>
						</div>
					</td>
				</tr>
			<?php } ?>
		<?php }
	}

	/**
	 * Output an admin notice if the path to pdftotext can't be resolved
	 */
	public function admin_notice() {
		$exec_enabled = function_exists( 'exec' ) && ! in_array( 'exec', array_map( 'trim', explode( ', ', ini_get( 'disable_functions' ) ) ), true ) && ( empty( ini_get( 'safe_mode' ) ) || ! ( strtolower( ini_get( 'safe_mode' ) ) !== 'off' ) );
		if ( ! $exec_enabled ) {
			?>
			<div class="updated">
				<p>
					<strong><?php esc_html_e( 'NOTE', 'searchwp' ); ?>: </strong>
					<?php
					echo wp_kses(
						__(
						'SearchWP Xpdf Integration requires access to <code>exec()</code> in order to run <code>pdftotext</code>. Please confirm with your host that it is available.', 'searchwp' ),
						array(
							'code' => array(),
						)
					);
					?>
				</p>
			</div>
			<?php
		}
		if ( class_exists( 'SearchWP' ) && false === strpos( $this->xpdfPath, 'pdftotext' ) ) { ?>
			<div class="updated">
				<p>
					<strong><?php esc_html_e( 'NOTE', 'searchwp' ); ?>: </strong>
					<?php
					echo wp_kses(
						__(
						'SearchWP Xpdf Integration depends on the use of <code>pdftotext</code>. Please ensure you are using that binary (it should be in the <code>searchwp_xpdf_path</code> filter file path).', 'searchwp' ),
						array(
							'code' => array(),
						)
					); ?> <a href="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>INSTALL.md"><?php esc_html_e( 'View installation instructions', 'searchwp' ); ?></a></p>
			</div>
		<?php } elseif ( class_exists( 'SearchWP' ) && ! file_exists( $this->xpdfPath ) ) { ?>
			<div class="updated">
				<p><strong><?php esc_html_e( 'NOTE', 'searchwp' ); ?>: </strong> <?php esc_html_e( 'SearchWP Xpdf Integration requires you to download and install Xpdf. Text extraction is disabled until your path to Xpdf has been defined.', 'searchwp' ); ?> <a href="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>INSTALL.md"><?php esc_html_e( 'View installation instructions', 'searchwp' ); ?></a></p>
			</div>
		<?php }
	}
}

new SearchWPXpdf();
