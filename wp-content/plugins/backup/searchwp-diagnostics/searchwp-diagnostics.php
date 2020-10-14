<?php
/*
Plugin Name: SearchWP Diagnostics
Plugin URI: https://searchwp.com/
Description: Retrieve detailed information about the inner workings of SearchWP index
Version: 1.5.1
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2014-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_DIAGNOSTICS_VERSION' ) ) {
	define( 'SEARCHWP_DIAGNOSTICS_VERSION', '1.5.1' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Diagnostics_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_diagnostics_update_check() {

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
	$searchwp_diagnostics_updater = new SWP_Diagnostics_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33682,
			'version'   => SEARCHWP_DIAGNOSTICS_VERSION,
			'license'   => $license_key,
			'item_name' => 'Diagnostics',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_diagnostics_updater;
}

add_action( 'admin_init', 'searchwp_diagnostics_update_check' );

class SearchWPDiagnostics {

	// required for all SearchWP extensions
	public $public                = true;           // should be shown in Extensions menu on SearchWP Settings screen
	public $slug                  = 'diagnostics';  // slug used for settings screen(s)
	public $name                  = 'Diagnostics';  // name used in various places
	public $min_searchwp_version  = '2.5.7';        // used in min version check

	// unique to this extension
	private $url;
	private $prefix     = 'swp_diagnostics_';

	function __construct() {
		$this->url = plugins_url( 'searchwp-diagnostics' );

		// SearchWP Hooks
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );

		add_action( 'init', array( $this, 'maybe_validate_indexer' ) );

		// SearchWP 4.0 compat.
		add_filter( 'searchwp\extensions', array( $this, 'register' ), 10 );
		add_action( 'wp_ajax_searchwp_get_indexed_tokens', array( $this, 'get_indexed_tokens' ) );
		add_action( 'wp_ajax_searchwp_get_unindexed_entries', array( $this, 'get_unindexed_entries' ) );
	}

	/**
	 * AJAX callback to retrieve indexed tokens for the submitted Source/ID pair.
	 */
	function get_indexed_tokens() {
		check_ajax_referer( 'searchwp-diagnostics' );

		$source = isset( $_REQUEST['source'] ) ? $_REQUEST['source'] : '';
		$id     = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : 0;

		$index = new \SearchWP\Index\Controller();
		$entry = new \SearchWP\Entry( $index->get_source_by_name( $source ), $id );

		wp_send_json_success( $index->get_tokens_for_entry( $entry ) );
	}

	/**
	 * AJAX callback to retrieve indexed tokens for the submitted Source/ID pair.
	 */
	function get_unindexed_entries() {
		check_ajax_referer( 'searchwp-diagnostics' );

		$source = isset( $_REQUEST['source'] ) ? $_REQUEST['source'] : '';

		$index   = new \SearchWP\Index\Controller();
		$source  = $index->get_source_by_name( $source );
		$entries = $source->get_unindexed_entries( 100 );

		$entry_ids = [];
		foreach ( $entries->get() as $entry ) {
			$entry_ids[] = $entry->get_id();
		}

		wp_send_json_success( $entry_ids );
	}

	/**
	 * Outputs the view in SearchWP 4.0+
	 * @return void
	 */
	function view_updated() {
		if ( ! class_exists( 'SearchWP\Index\Controller' ) || ! class_exists( 'SearchWP\Entry' ) ) {
			wp_die( 'SearchWP 4.0+ must be active' );
		}
		$nonce = wp_create_nonce( 'searchwp-diagnostics' );
		$index = new \SearchWP\Index\Controller();
		?>
			<p class="description">The following tools are available:</p>

			<div class="searchwp-diagnostics-tool searchwp-diagnostics-indexed-tokens-tool">
				<h2>View Indexed Tokens for Source Entry</h2>
				<form class="searchwp-diagnostics-indexed-tokens" action="" data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<div>
						<select name="searchwp_source">
							<?php foreach ( $index->get_sources() as $source ) : ?>
								<option value="<?php echo esc_attr( $source->get_name() ); ?>">
									<?php echo esc_html( $source->get_label( 'singular' ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<div>
							<label for="searchwp_source_entry_id">ID</label>
							<input name="searchwp_source_entry_id" id="searchwp_source_entry_id" type="text" value="">
						</div>
						<button class="button button-primary">View Tokens</button>
					</div>
				</form>
				<div class="searchwp-diagnostics-results-display searchwp-diagnostics-indexed-tokens-display">
					<p class="description">Please choose the applicable Source and enter an Entry ID</p>
				</div>
			</div>

			<div class="searchwp-diagnostics-tool searchwp-diagnostics-unindexed-entries-tool">
				<h2>List Unindexed Entries</h2>
				<form class="searchwp-diagnostics-unindexed-entries" action="" data-nonce="<?php echo esc_attr( $nonce ); ?>">
					<div>
						<select name="searchwp_source">
							<?php foreach ( $index->get_sources() as $source ) : ?>
								<option value="<?php echo esc_attr( $source->get_name() ); ?>">
									<?php echo esc_html( $source->get_label() ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<button class="button button-primary">View List</button>
					</div>
				</form>
				<div class="searchwp-diagnostics-results-display searchwp-diagnostics-unindexed-entries-display">
					<p class="description">Please choose the applicable Source. Results limited to 100.</p>
				</div>
			</div>
		<?php

		wp_enqueue_script(
			$this->prefix . 'style',
			plugin_dir_url( __FILE__ ) . '/script.js',
			array( 'jquery' ),
			SEARCHWP_DIAGNOSTICS_VERSION,
			true
		);

		wp_enqueue_style(
			$this->prefix . 'style',
			plugin_dir_url( __FILE__ ) . '/style.css',
			array(),
			SEARCHWP_DIAGNOSTICS_VERSION
		);
	}

	function register( $extensions ) {
		$extensions['Diagnostics'] = __FILE__;

		return $extensions;
	}

	function get_post_terms( $post_id = 0 ) {
		global $wpdb;

		if ( ! is_integer( $post_id ) || ! defined( 'SEARCHWP_DBPREFIX' ) ) {
			return 0;
		}

		$post_id = absint( $post_id );

		$searchwp_index_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'index';
		$searchwp_terms_table = $wpdb->prefix . SEARCHWP_DBPREFIX . 'terms';

		$sql = "
			SELECT      {$searchwp_terms_table}.term
			FROM        {$searchwp_terms_table}
			LEFT JOIN   {$searchwp_index_table}
			            ON {$searchwp_terms_table}.id = {$searchwp_index_table}.term
			WHERE       {$searchwp_index_table}.post_id = %d
			ORDER BY    {$searchwp_terms_table}.term ASC
			";

		$terms = $wpdb->get_col( $wpdb->prepare( trim( $sql ), $post_id ) );

		return $terms;
	}


	function maybe_validate_indexer() {
		if ( isset( $_REQUEST['searchwp_diagnostics_action'] ) && 'verify_indexer_environment' == $_REQUEST['searchwp_diagnostics_action']
		     && isset( $_REQUEST['searchwp_diagnostics_nonce'] ) && wp_verify_nonce( $_REQUEST['searchwp_diagnostics_nonce'], 'searchwp_diagnostics' ) ) {
			// we want to validate the indexer
			$flag = get_option( 'searchwp_diagnostics_indexer_check' );
			if ( empty( $flag ) ) {
				// this is the original submission (the first step in the check) so we'll set our flag
				$hash = md5( uniqid( 'searchwp_diagnostics' ) );

				add_option( 'searchwp_diagnostics_indexer_check', $hash, '', 'no' );

				$args = array(
					'body'        => array(
						'searchwp_diagnostics_action'   => 'verify_indexer_environment',
						'searchwp_diagnostics_nonce'    => sanitize_text_field( $_REQUEST['searchwp_diagnostics_nonce'] ),
						'searchwp_diagnostics_hash'     => $hash,
					),
					'blocking'    => false,
					'user-agent'  => 'SearchWP',
					'timeout'     => 1,
					'sslverify'   => false,
				);
				$args = apply_filters( 'searchwp_indexer_loopback_args', $args );

				// fire off the request
				wp_remote_post(
					trailingslashit( site_url() ),
					$args
				);

				// redirect to this same page now that the request has been (in)validated
				$redirect_url = trailingslashit( get_admin_url() );
				$redirect_url .= 'options-general.php?page=searchwp&extension=diagnostics&nonce=';
				$redirect_url .= isset( $_REQUEST['nonce'] ) ? esc_attr( $_REQUEST['nonce'] ) : '';
				$redirect_url .= isset( $_REQUEST['searchwp_diagnostics_action'] ) ? '&searchwp_diagnostics_action=' . esc_attr( $_REQUEST['searchwp_diagnostics_action'] ) : '';
				$redirect_url .= isset( $_REQUEST['searchwp_diagnostics_nonce'] ) ? '&searchwp_diagnostics_nonce=' . esc_attr( $_REQUEST['searchwp_diagnostics_nonce'] ) : '';
				$redirect_url .= '&searchwp_diagnostics_hash=' . $hash;

				wp_redirect( esc_url( $redirect_url ) );

				die();
			} else {
				if ( isset( $_REQUEST['searchwp_diagnostics_hash'] ) ) {
					// validate the hash
					$submitted_hash = sanitize_text_field( $_REQUEST['searchwp_diagnostics_hash'] );
					$actual_hash = get_option( 'searchwp_diagnostics_indexer_check' );
					if ( $submitted_hash == $actual_hash ) {
						// indexer environment is valid
						add_option( 'searchwp_diagnostics_indexer_valid', true, '', 'no' );
					}
					delete_option( 'searchwp_diagnostics_indexer_check' );
				}
			}
		}
	}

	function view() {
		if ( defined( 'SEARCHWP_VERSION' ) && version_compare( SEARCHWP_VERSION, '3.99.0', '>=' ) ) {
			$this->view_updated();
			return;
		}

		// all possible actions that
		$valid_actions = array( 'verify_indexer_environment', 'get_post_terms', 'find_unindexed_posts' );

		if ( isset( $_GET['searchwp_diagnostics_action'] ) ) {

			// make sure the requested action is valid
			if ( ! in_array( $_GET['searchwp_diagnostics_action'], $valid_actions ) ) {
				wp_die( esc_attr( __( 'Invalid request' ) ) );
			}

			// make sure it's an authorized action request
			if ( ! isset( $_GET['searchwp_diagnostics_nonce'] ) || ( isset( $_GET['searchwp_diagnostics_nonce'] ) && ! wp_verify_nonce( $_GET['searchwp_diagnostics_nonce'], 'searchwp_diagnostics' ) ) ) {
				wp_die( esc_attr( __( 'Invalid request' ) ) );
			}

			switch ( $_GET['searchwp_diagnostics_action'] ) {

				/**
				 * get_post_terms
				 */
				case 'get_post_terms';
					$post_id = isset( $_GET['swp_diagnostics_post_id'] ) ? absint( $_GET['swp_diagnostics_post_id'] ) : 0;
					if ( $post_id ) :
						$terms = $this->get_post_terms( $post_id );
						?>
						<h3><?php _e( 'Indexed terms for post', 'searchwp_diagnostics' ); ?> <?php echo esc_html( $post_id ); ?></h3>
						<p><?php _e( 'The following <em>unique terms</em> are currently in the index (in alphabetical order):', 'searchwp_diagnostics' ); ?></p>
						<p><?php _e( 'Total unique terms', 'searchwp_diagnostics' ); ?>: <strong><?php echo count( $terms ); ?></strong></p>
						<ul>
							<?php foreach ( $terms as $term ) : ?>
								<li style="display:inline-block;width:23%;padding-right:1%;"><?php echo esc_html( $term ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p><?php _e( 'Invalid Post ID', 'searchwp_diagnostics' ); ?></p>
					<?php endif;
					break;

				/**
				 * verify_indexer_environment
				 */
				case 'verify_indexer_environment':
					$valid = get_option( 'searchwp_diagnostics_indexer_valid' );
					delete_option( 'searchwp_diagnostics_indexer_valid' );
					delete_option( 'searchwp_diagnostics_indexer_check' );
					if ( $valid ) { ?>
						<div class="updated">
							<p><?php _e( 'The indexer environment is <strong>valid</strong>', 'searchwp_diagnostics' ); ?></p>
						</div>
					<?php } else { ?>
						<div class="error">
							<p><?php _e( 'The indexer environment is <strong>invalid</strong>', 'searchwp_diagnostics' ); ?></p>
						</div>
					<?php }
					// clean up

					break;

				/**
				 * find_unindexed_posts
				 */
				case 'find_unindexed_posts':
					$unindexed_posts = $this->get_unindexed_posts();
					?>
					<h3><?php _e( 'Unindexed posts', 'searchwp_diagnostics' ); ?></h3>
					<?php if ( ! empty( $unindexed_posts ) ) : ?>
					<p><?php _e( 'The following posts have not been indexed:', 'searchwp_diagnostics' ); ?></p>
					<?php if ( 100 <= count( $unindexed_posts ) ) : ?>
						<p class="description"><?php _e( 'List limited to 100 posts', 'searchwp_diagnostics' ); ?></p>
					<?php endif; ?>
					<ul style="padding:1em 0 2em;">
						<?php foreach ( $unindexed_posts as $unindexed_post ) : ?>
							<li><code><?php echo absint( $unindexed_post->ID ); ?></code> — 
								<a href="<?php echo get_permalink( $unindexed_post->ID ); ?>">
									<?php echo get_the_title( absint( $unindexed_post->ID ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php _e( 'All posts have been indexed', 'searchwp_diagnostics' ); ?></p>
				<?php endif; ?>
					<?php break;
			}

			?>
			<?php
			$link = add_query_arg(
				array(
					'page'      => 'searchwp',
					'extension' => 'diagnostics',
					'nonce'     => isset( $_GET['nonce'] ) ? esc_attr( $_GET['nonce'] ) : '',
				),
				admin_url( 'options-general.php' )
			);
			?>
			<p><a href="<?php echo esc_url( $link ); ?>" class="button"><?php _e( 'Back to SearchWP Diagnostics', 'searchwp_diagnostics' ); ?></a></p>
		<?php

		} else {
			$this->actions_form();
		}

		?>
	<?php
	}

	function posts_per_page() {
		return 100;
	}

	function get_unindexed_posts() {

		if ( ! class_exists( 'SearchWPIndexer' ) ) {
			return array();
		}

		add_filter( 'searchwp_index_chunk_size', array( $this, 'posts_per_page' ) );

		$indexer = new SearchWPIndexer();
		$unindexed_posts = $indexer->find_unindexed_posts();

		remove_filter( 'searchwp_index_chunk_size', array( $this, 'posts_per_page' ) );

		return $unindexed_posts;
	}

	function actions_form() {
		$nonce = wp_create_nonce( 'searchwp_diagnostics' );
		// $this->actions_form_verify_indexer_environment( $nonce );
		$this->actions_form_indexed_terms_per_post( $nonce );
		$this->actions_form_find_unindexed_posts( $nonce );
	}

	function actions_form_verify_indexer_environment( $nonce ) {
		?>
		<h3><?php _e( 'Verify Indexer Environment', 'searchwp_diagnostics' ); ?></h3>
		<p><?php _e( "If you are seeing consistent issues with the indexer stalling (not progressing) you can verify it's environment", 'searchwp_diagnostics' ); ?></p>
		<!--suppress HtmlUnknownTarget -->
		<form action="options-general.php" method="get" id="swp-diagnostics-verify-indexer-environment">
			<input type="hidden" name="page" value="searchwp" />
			<input type="hidden" name="extension" value="diagnostics" />
			<input type="hidden" name="nonce" value="<?php echo isset( $_GET['nonce'] ) ? esc_attr( $_GET['nonce'] ) : ''; ?>" />
			<input type="hidden" name="searchwp_diagnostics_action" value="verify_indexer_environment" />
			<input type="hidden" name="searchwp_diagnostics_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
			<p class="submit">
				<button class="button" type="submit"><?php _e( 'Verify Indexer Environment', 'searchwp_diagnostics' ); ?></button>
			</p>
		</form>
	<?php
	}

	function actions_form_find_unindexed_posts( $nonce ) {
		?>
		<h3><?php _e( 'List Unindexed Posts', 'searchwp_diagnostics' ); ?></h3>
		<p><?php _e( 'If the indexer appears to be stuck you can find out which posts it may be having trouble with', 'searchwp_diagnostics' ); ?></p>
		<!--suppress HtmlUnknownTarget -->
		<form action="options-general.php" method="get" id="swp-diagnostics-find-unindexed_posts">
			<input type="hidden" name="page" value="searchwp" />
			<input type="hidden" name="extension" value="diagnostics" />
			<input type="hidden" name="nonce" value="<?php echo isset( $_GET['nonce'] ) ? esc_attr( $_GET['nonce'] ) : ''; ?>" />
			<input type="hidden" name="searchwp_diagnostics_action" value="find_unindexed_posts" />
			<input type="hidden" name="searchwp_diagnostics_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
			<p class="submit">
				<button class="button" type="submit"><?php _e( 'Find Posts', 'searchwp_diagnostics' ); ?></button>
			</p>
		</form>
	<?php
	}

	function actions_form_indexed_terms_per_post( $nonce ) {
		?>
		<h3><?php _e( 'Retrieve Terms for Post', 'searchwp_diagnostics' ); ?></h3>
		<p><?php _e( 'Enter a single post ID to list unique terms in the SearchWP index for that post', 'searchwp_diagnostics' ); ?></p>
		<!--suppress HtmlUnknownTarget -->
		<form action="options-general.php" method="get" id="swp-diagnostics-terms-per-post">
			<input type="hidden" name="page" value="searchwp" />
			<input type="hidden" name="extension" value="diagnostics" />
			<input type="hidden" name="nonce" value="<?php echo isset( $_GET['nonce'] ) ? esc_attr( $_GET['nonce'] ) : ''; ?>" />
			<input type="hidden" name="searchwp_diagnostics_action" value="get_post_terms" />
			<input type="hidden" name="searchwp_diagnostics_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row"><label for="swp_diagnostics_post_id"><?php _e( 'Post ID', 'searchwp_diagnostics' ); ?></label></th>
					<td><input name="swp_diagnostics_post_id" type="text" id="swp_diagnostics_post_id" value="" class="medium-text"></td>
				</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button" value="<?php _e( 'Retrieve Details', 'searchwp_diagnostics' ); ?>">
			</p>
		</form>
	<?php
	}

}

new SearchWPDiagnostics();
