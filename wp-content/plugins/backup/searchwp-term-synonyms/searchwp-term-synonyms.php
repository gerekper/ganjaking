<?php
/*
Plugin Name: SearchWP Term Synonyms
Plugin URI: https://searchwp.com/
Description: Manually define term synonyms for search queries
Version: 2.4.14
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2013-2018 Jonathan Christopher

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

if ( ! defined( 'SEARCHWP_TERM_SYNONYMS_VERSION' ) ) {
	define( 'SEARCHWP_TERM_SYNONYMS_VERSION', '2.4.14' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Term_Synonyms_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_term_synonyms_update_check() {

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

	if ( ! defined( 'SEARCHWP_TERM_SYNONYMS_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_term_synonyms_updater = new SWP_Term_Synonyms_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33675,
			'version'   => SEARCHWP_TERM_SYNONYMS_VERSION,
			'license'   => $license_key,
			'item_name' => 'Term Synonyms',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_term_synonyms_updater;
}

add_action( 'admin_init', 'searchwp_term_synonyms_update_check' );

class SearchWPTermSynonyms {

	// required for all SearchWP extensions
	public $public                = true;               // should be shown in Extensions menu on SearchWP Settings screen
	public $slug                  = 'term-synonyms';    // slug used for settings screen(s)
	public $name                  = 'Term Synonyms';    // name used in various places
	public $min_searchwp_version  = '2.4.10';           // used in min version check

	// unique to this extension
	private $url;
	private $version    = SEARCHWP_TERM_SYNONYMS_VERSION;
	private $prefix     = 'swp_termsyn_';
	private $settings;

	/**
	 * SearchWPTermSynonyms constructor.
	 */
	function __construct() {
		$this->url      = plugins_url( 'searchwp-term-synonyms' );
		$this->settings = get_option( $this->prefix . 'settings' );

		// WordPress hooks
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 11 );

		add_action( 'admin_enqueue_scripts',        array( $this, 'assets' ) );
		add_action( 'admin_init',                   array( $this, 'init_settings' ) );

		// SearchWP Hooks
		add_filter( 'searchwp_extensions',          array( $this, 'register' ), 10 );
		add_filter( 'searchwp_pre_search_terms',    array( $this, 'find_synonyms' ), 10, 2 );
	}

	/**
	 * Register Term Synonyms with SearchWP
	 *
	 * @param $extensions
	 *
	 * @return mixed
	 */
	function register( $extensions ) {
		$extensions['TermSynonyms'] = __FILE__;

		return $extensions;
	}

	/**
	 * Output the settings view
	 */
	function view() {
		$synonyms = get_option( $this->prefix . 'settings' );
		?>
		<p><?php echo wp_kses( __( 'Manage term synonyms by manually defining what should be considered during searches. These synonyms will be considered for <strong>all searches</strong>.', 'searchwptermsyn' ), array( 'strong' => array() ) ); ?></p>
		<form action="options.php" method="post" id="swp-term-synonyms-wrapper">
			<div class="swp-wp-settings-api">
				<?php do_settings_sections( $this->prefix ); ?>
				<?php settings_fields( $this->prefix . 'settings' ); ?>
			</div>
			<table class="swp-term-synonyms">
				<colgroup>
					<col id="searchwp-col-source" />
					<col id="searchwp-col-synonyms" />
				</colgroup>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Search Term', 'searchwptermsyn' ); ?> <a class="swp-tooltip" href="#swp-tooltip-term-source">?</a>
						<div class="swp-tooltip-content" id="swp-tooltip-term-source">
							<?php echo wp_kses( __( '<strong>Simple terms only</strong>: single words, lowercase, no punctuation', 'searchwptermsyn' ), array( 'strong' => array() ) ); ?>
						</div>
					</th>
					<th><?php esc_html_e( 'Search Term Synonyms (to include when Search Term is used)', 'searchwptermsyn' ); ?> <a class="swp-tooltip" href="#swp-tooltip-replace">?</a>
						<div class="swp-tooltip-content" id="swp-tooltip-replace">
							<?php echo wp_kses( __( 'If you choose to <strong>Replace</strong> a term the synonyms will take its place, removing it from the search query', 'searchwptermsyn' ), array( 'strong' => array() ) ); ?>
						</div>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $synonyms ) && is_array( $synonyms ) ) : ?>
					<?php foreach ( $synonyms as $synonym ) : $arrayFlag = uniqid( 'swpts' ); ?>
					<tr>
						<td>
							<label for="swp_term_<?php echo esc_attr( $arrayFlag ); ?>"></label>
							<input type="text" class="swp-term-input" id="swp_term_<?php echo esc_attr( $arrayFlag ); ?>" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][<?php echo esc_attr( $arrayFlag ); ?>][term]" value="<?php echo esc_attr( $synonym['term'] ); ?>" />
							<a href="#" class="swp-term-delete">x</a>
						</td>
						<td>
							<input type="text" class="swp-synonyms-input" id="swp_term_<?php echo esc_attr( $arrayFlag ); ?>" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][<?php echo esc_attr( $arrayFlag ); ?>][synonyms]" value="<?php echo esc_attr( implode( ', ', $synonym['synonyms'] ) ); ?>" placeholder="<?php esc_attr( 'Comma separated synonyms', 'searchwptermsyn' ); ?>" />
							<input type="checkbox" id="swp_term_<?php echo esc_attr( $arrayFlag ); ?>_replace" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][<?php echo esc_attr( $arrayFlag ); ?>][replace]" value="1"<?php if ( $synonym['replace'] ) : ?> checked="checked"<?php endif; ?> />
							<label for="swp_term_<?php echo esc_attr( $arrayFlag ); ?>_replace"><?php esc_html_e( 'Replace', 'searchwptermsyn' ); ?></label>
						</td>
					</tr>
				<?php endforeach; endif; ?></tbody>
			</table>
			<p>
				<a class="button swp-add-synonym" href="#"><?php esc_html_e( 'Add Synonym', 'searchwptermsyn' ); ?></a>
			</p>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'searchwptermsyn' ); ?>" />
			</p>
		</form>
		<script type="text/html" id="tmpl-searchwp-term-synonym">
			<tr>
				<td>
					<label for="swp_term_{{ swp.arrayFlag }}"></label>
					<input type="text" class="swp-term-input" id="swp_term_{{ swp.arrayFlag }}" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][{{ swp.arrayFlag }}][term]" value="" />
					<a href="#" class="swp-term-delete">x</a>
				</td>
				<td>
					<input type="text" class="swp-synonyms-input" id="swp_term_{{ swp.arrayFlag }}" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][{{ swp.arrayFlag }}][synonyms]" value="" placeholder="<?php esc_attr( 'Comma separated synonyms', 'searchwptermsyn' ); ?>" />
					<input type="checkbox" id="swp_term_{{ swp.arrayFlag }}_replace" name="<?php echo esc_attr( $this->prefix ); ?>settings[def][{{ swp.arrayFlag }}][replace]" value="1" />
					<label for="swp_term_{{ swp.arrayFlag }}_replace"><?php esc_html_e( 'Replace', 'searchwptermsyn' ); ?></label>
				</td>
			</tr>
		</script>
		<style type="text/css">
			#searchwp-term-synonyms-wrapper table {
				width: 100%;
			}

			#searchwp-term-synonyms-wrapper th {
				text-align: left;
			}

			#searchwp-term-synonyms-wrapper td {
				padding: 5px 0;
			}

			#searchwp-col-source {
				width: 25%;
			}

			#searchwp-col-synonyms {
				width: 75%;
			}
		</style>
	<?php
	}

	/**
	 * Retrieve synonyms
	 *
	 * @param $term
	 * @param $engine
	 *
	 * @return array
	 */
	function find_synonyms( $term, $engine ) {
		global $searchwp;

		if ( empty( $term ) ) {
			return $term;
		}

		if ( isset( $engine ) ) {
			$engine = null;
		}

		if ( ! class_exists( 'SearchWP' ) || version_compare( $searchwp->version, $this->min_searchwp_version, '<' ) ) {
			return $term;
		}

		$synonyms = get_option( $this->prefix . 'settings' );

		if ( ! is_array( $synonyms ) ) {
			return $term;
		}

		// convert everything to lowercase
		if ( ! empty( $synonyms ) ) {
			foreach ( $synonyms as $synonym_id => $synonym ) {
				if ( ! empty( $synonyms[ $synonym_id ]['term'] ) ) {
					if ( function_exists( 'mb_strtolower' ) ) {
						$synonyms[ $synonym_id ]['term'] = mb_strtolower( $synonyms[ $synonym_id ]['term'] );
					} else {
						$synonyms[ $synonym_id ]['term'] = strtolower( $synonyms[ $synonym_id ]['term'] );
					}
				}

				if ( is_array( $synonyms[ $synonym_id ]['synonyms'] ) && ! empty( $synonyms[ $synonym_id ]['synonyms'] ) ) {
					if ( function_exists( 'mb_strtolower' ) ) {
						array_map( 'mb_strtolower', $synonyms[ $synonym_id ]['synonyms'] );
					} else {
						array_map( 'strtolower', $synonyms[ $synonym_id ]['synonyms'] );
					}
				}
			}
		}

		// we expect $term to be an array
		if ( is_string( $term ) ) {
			$term = array( $term );
		}

		if ( is_array( $term ) && is_array( $synonyms ) && ! empty( $synonyms ) ) {
			foreach ( $synonyms as $synonym ) {
				if ( in_array( $synonym['term'], $term ) ) {

					// there is a match, handle it

					// break out where applicable
					if ( is_array( $synonym['synonyms'] ) && ! empty( $synonym['synonyms'] ) ) {
						foreach ( $synonym['synonyms'] as $maybe_synonym ) {
							if ( false !== strpos( $maybe_synonym, ' ' ) ) {
								$maybe_synonym = explode( ' ', $maybe_synonym );
								$synonym['synonyms'] = $maybe_synonym;
							}
						}
					}

					// if the term was stemmed that means stemming is enabled so we need to stem the synonym(s) too...
//					if ( $stemming_enabled ) {
//						if ( is_array( $synonym['synonyms'] ) && class_exists( 'SearchWPStemmer' ) ) {
//							foreach ( $synonym['synonyms'] as $key => $unstemmed_synonym ) {
//								$unstemmed = $unstemmed_synonym;
//								$maybeStemmed = apply_filters( 'searchwp_custom_stemmer', $unstemmed );
//								$stemmer = new SearchWPStemmer();
//								// if the term was stemmed via the filter use it, else generate our own
//								$stemmed_term = ( $unstemmed == $maybeStemmed ) ? $stemmer->stem( $unstemmed_synonym ) : $maybeStemmed;
//								$synonym['synonyms'][ $key ] = $stemmed_term;
//							}
//						}
//					}

					// merge everything together
					$term = array_merge( $term, $synonym['synonyms'] );
				}
			}
		}

		// LASTLY handle any Replacements
		if ( is_array( $term ) && ! empty( $term ) && is_array( $synonyms ) && ! empty( $synonyms ) ) {
			foreach ( $term as $key => $potential_replacement ) {
				foreach ( $synonyms as $synonym ) {
					if ( ! empty( $synonym['replace'] ) && $synonym['term'] == $potential_replacement ) {
						unset( $term[ $key ] );
					}
				}
			}
		}

		$term = array_values( array_unique( $term ) );
		$term = array_map( 'sanitize_text_field', $term );

		if ( function_exists( 'mb_strtolower' ) ) {
			$term = array_map( 'mb_strtolower', $term );
		} else {
			$term = array_map( 'strtolower', $term );
		}

		return $term;
	}

	/**
	 * Settings initialization callback
	 */
	function init_settings() {
		add_settings_section(
			$this->prefix . 'settings',
			'SearchWP Settings',
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
	 * Settings callback
	 */
	function settings_callback() {}

	/**
	 * Settings field callback
	 */
	function settings_field_callback() { ?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<input type="text" name="<?php echo esc_attr( $this->prefix ); ?>settings" id="<?php echo esc_attr( $this->prefix ); ?>settings" value="SearchWP Term Synonyms" />
	<?php
	}

	/**
	 * Settings validation callback
	 *
	 * @param $input
	 *
	 * @return null
	 */
	function validate_settings( $input ) {

		if ( isset( $input['def'] ) && is_array( $input['def'] ) ) {

			if ( ! class_exists( 'SearchWP' ) ) {
				return null;
			}

			$synonyms = $input['def'];

			foreach ( $synonyms as $key => $synonymDefinition ) {

				// prepare the term
				$synonyms[ $key ]['term'] = trim( sanitize_text_field( $synonymDefinition['term'] ) );

				if ( empty( $synonymDefinition['synonyms'] ) ) {
					// no synonyms? kill it
					unset( $synonyms[ $key ] );
				} else {
					// sanitize the synonyms
					$synonyms_synonyms = explode( ',', trim( sanitize_text_field( $synonymDefinition['synonyms'] ) ) );
					$synonyms_synonyms = array_map( 'trim', $synonyms_synonyms );
					foreach ( $synonyms_synonyms as $synonyms_synonym_key => $synonyms_synonym ) {
						$synonyms_synonyms[ $synonyms_synonym_key ] = sanitize_text_field( $synonyms_synonym );
					}

					$synonyms[ $key ]['synonyms'] = $synonyms_synonyms;

					// make sure there isn't synonymception
					if ( $synonyms[ $key ]['term'] == $synonyms[ $key ]['synonyms'] ) {
						unset( $synonyms[ $key ] );
					} else {
						// finalize the replace bool
						if ( isset( $synonyms[ $key ]['replace'] ) ) {
							$synonyms[ $key ]['replace'] = true;
						} else {
							$synonyms[ $key ]['replace'] = false;
						}
					}
				}
			}

			// deliver sanitized results
			$input = $synonyms;
		}

		return $input;
	}

	/**
	 * Enqueue assets callback
	 *
	 * @param $hook
	 */
	function assets( $hook ) {
		wp_register_script( 'swp_term_synonyms_js', trailingslashit( $this->url ) . 'assets/js/searchwp-term-synonyms.js', array( 'jquery', 'underscore' ), $this->version );

		wp_register_style( 'swp_term_synonyms_css', trailingslashit( $this->url ) . 'assets/css/searchwp-term-synonyms.css', false, $this->version );

		if ( 'settings_page_searchwp' == $hook && isset( $_GET['extension'] ) && $_GET['extension'] == $this->slug ) {
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'swp_term_synonyms_js' );

			wp_enqueue_style( 'swp_term_synonyms_css' );
		}
	}

	/**
	 * Plugin row output (checks for minimum SearchWP version)
	 */
	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) ) {
			return;
		}

		$searchwp = SWP();
		if ( version_compare( $searchwp->version, $this->min_searchwp_version, '<' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP Term Synonyms requires a newer version of SearchWP', 'searchwptermsyn' ); ?>
					</div>
				</td>
			</tr>
		<?php }
	}

}

new SearchWPTermSynonyms();
