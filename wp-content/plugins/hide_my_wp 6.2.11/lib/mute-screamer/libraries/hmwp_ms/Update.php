<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Mute Screamer update class
 *
 * Updates PHPIDS with the latest default_filter.xml
 * and Converter.php
 */
class HMWP_MS_Update {

	/**
	 * An instance of this class
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * Update cache
	 *
	 * @var array
	 */
	private $updates = array();

	/**
	 * The current file to check for a newer version
	 *
	 * @var string
	 */
	private $file = '';

	/**
	 * The files to check for updates
	 *
	 * @var array
	 */
	private $files = array( 'default_filter.xml', 'Converter.php' );

	/**
	 * Update check interval
	 *
	 * @var int
	 */
	private $timeout = 86400;

	/**
	 * JSON update data
	 *
	 * @var string
	 */
	private $json_data = '';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {
		$this->updates = get_site_transient( 'hmwp_ms_update' );
	}

	/**
	 * Get the HMWP_MS Update instance
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! self::$instance )
			self::$instance = new HMWP_MS_Update;

		return self::$instance;
	}

	/**
	 * Check for updates to Converter.php and default_filter.xml
	 *
	 * 1. Fetch remote sha1 of each file
	 * 2. Check if the sha1's are different
	 * 3. Fetch the latest RSS
	 * 4. Parse RSS data
	 *
	 * @return bool
	 */
	public function update_check() {
		// TODO: Make this more efficient/responsive so it doesn't
		// TODO: look like Wordpress is really slow

		if ( ! $this->can_update() )
			return false;

		// Is it time to check for updates?
		if ( $this->updates !== false )
			return false;

		// Suppress libxml parsing errors
		$libxml_use_errors = libxml_use_internal_errors( true );

		// Initialise the update cache
		$this->updates = array();
		$this->updates['updates'] = array();

		// Delete requests cache if any are hanging around
		delete_site_transient( 'hmwp_ms_requests_cache' );

		// Fetch the remote sha1's
		$this->sha1_fetch();

		foreach ( $this->files as $file ) {
			$this->file = $file;
			$this->updates['updates'][$this->file] = new stdClass;

			// Is the sha1 different?
			if ( ! $this->sha1_compare() ) {
				// File doesn't need updating remove from update array
				unset( $this->updates['updates'][$file] );
				continue;
			}
		}

		// Are there any files to update?
		if ( empty( $this->updates['updates'] ) ) {
			$this->abort();
			return false;
		}

		// Fetch RSS for latest revision
		$this->rss_fetch();

		// Load up the RSS
		$rss = simplexml_load_string( $this->updates['rss'] );
		if ( ! $rss ) {
			$this->abort();
			return false;
		}

		// Does the feed have what we are looking for?
		if ( ! isset( $rss->entry ) ) {
			$this->abort();
			return false;
		}

		// Make sure the elements we are going to use are set
		if ( ! isset( $rss->entry->id ) OR ! isset( $rss->entry->title ) OR ! isset( $rss->entry->updated ) OR ! isset( $rss->entry->link ) ) {
			$this->abort();
			return false;
		}

		// Revision number
		$id = (string) $rss->entry->id;
		$x = explode( '/', $id );
		$revision_number = end( $x );

		// Add update information to each file
		foreach ( $this->files as $file ) {
			// Simple XML elements can't be serialized so cast them to strings
			$this->updates['updates'][$file]->title = (string) $rss->entry->title;
			$this->updates['updates'][$file]->revision = $revision_number;
			$this->updates['updates'][$file]->date = (string) $rss->entry->updated;
			$this->updates['updates'][$file]->revision_url = (string) $rss->entry->link->attributes()->href;
			$this->updates['updates'][$file]->revision_file_url = "http://dev.itratos.de/projects/php-ids/repository/revisions/{$revision_number}/raw/trunk/lib/IDS/{$file}";
		}

		// Clear libxml errors
		libxml_clear_errors();

		// Restore libxml errors
		libxml_use_internal_errors( $libxml_use_errors );

		return set_site_transient( 'hmwp_ms_update', $this->updates, $this->timeout );
	}


	/**
	 * Is it a good time to check for updates?
	 *
	 * @return bool
	 */
	private function can_update() {
		// Don't check for updates on wp-login.php, this happens when you request
		// an admin page but are not logged in and then redirected to wp-login.php
		if ( false === wp_validate_auth_cookie() )
			return false;

		// Don't run on plugin activation/deactivation, request will seem slow
		foreach ( array( 'activate', 'deactivate', 'activate-multi', 'deactivate-multi' ) as $key ) {
			if ( array_key_exists( $key, $_REQUEST ) ) {
				return false;
			}
		}

		// Don't check for updates on the following actions
		$actions = array(
			'hmwp_ms_upgrade_diff',
			'hmwp_ms_upgrade',
			'hmwp_ms_upgrade_run',
			'activate',
			'deactivate',
			'activate-selected',
			'deactivate-selected',
		);
		if ( in_array( HMWP_MS_Utils::get( 'action' ), $actions ) )
			return false;

		return true;
	}

	/**
	 * Fetch the remote sha1 and cache the result
	 *
	 * @return void
	 */
	private function sha1_fetch() {
		$url = 'http://ampt.github.com/mute-screamer/update.json';
		$response = $this->remote_get( $url );

		// Did the request fail?
		if ( $response['body'] == '' ) {
			$this->abort();
			return;
		}

		$this->json_data = json_decode( $response['body'], true );
	}

	/**
	 * Fetch the latest rss revision and cache the result
	 *
	 * @return void
	 */
	private function rss_fetch() {
		$url = 'http://dev.itratos.de/projects/php-ids/repository/revisions/1/revisions/trunk/?format=atom';
		$response = $this->remote_get( $url );

		// Did the request fail?
		if ( $response['body'] == '' ) {
			$this->abort();
			return;
		}

		$this->updates['rss'] = $response['body'];
	}

	/**
	 * Compare the sha1 of the local and remote files
	 *
	 * @return bool true if the sha1's are different
	 */
	private function sha1_compare() {
		// Get the current sha1
		$local_file = HMWP_MS_PATH."/libraries/IDS/{$this->file}";

		if ( ! file_exists( $local_file ) )
			return false;

		// Problem fetching json data?
		if ( ! isset( $this->json_data[$this->file] ) )
			return false;

		$local_sha1  = sha1_file( $local_file );
		$remote_sha1 = $this->json_data[$this->file];

		if ( $local_sha1 == $remote_sha1 )
			return false;

		return true;
	}

	/**
	 * A wrapper function to wp_remote_get. On error return
	 * an empty body so we can fail gracefully.
	 *
	 * @param string
	 * @param array
	 * @return array
	 */
	private function remote_get( $url = '', $options = array() ) {
		$cache = get_site_transient( 'hmwp_ms_requests_cache' );

		// Is it in the cache?
		$hash = md5( $url );
		if ( isset( $cache[$hash] ) )
			return $cache[$hash];

		// Default options
		if ( empty( $options ) ) {
			$options = array( 'sslverify' => false );
		}

		$response = wp_remote_get( $url, $options );

		if ( is_wp_error( $response ) )
			return array( 'body' => '' );

		if ( 200 != $response['response']['code'] )
			return array( 'body' => '' );

		if ( ! is_array( $cache ) )
			$cache = array();

		$cache[$hash] = $response;
		set_site_transient( 'hmwp_ms_requests_cache', $cache, $this->timeout );

		return $response;
	}

	/**
	 * Abort the update process.
	 *
	 * @return void
	 */
	private function abort() {
		// Set error flag and try again when the transient expires next
		$this->updates            = array();
		$this->updates['updates'] = array();
		$this->updates['status']  = 'Failed';
		set_site_transient( 'hmwp_ms_update', $this->updates, $this->timeout );
	}

	/**
	 * Display update notices on the update page
	 *
	 * @return void
	 */
	public function list_hmwp_ms_updates() {
		if ( empty( $this->updates['updates'] ) ) {
			echo '<h3>' . __( 'Mute Screamer', 'mute-screamer' ) . '</h3>';
			echo '<p>' . __( 'All files are up to date.', 'mute-screamer' ) . '</p>';
			return;
		}

		// TODO: Fix current revision number
		HMWP_MS_Utils::view( 'admin_update', array( 'files' => $this->updates['updates'] ) );
	}

	/**
	 * Display diff of files to be upgraded
	 *
	 * @return void
	 */
	public function do_upgrade_diff() {
		$diff_files = array();

		if ( ! current_user_can( 'update_plugins' ) )
			wp_die( __( 'You do not have sufficient permissions to update Mute Screamer for this site.', 'mute-screamer' ) );

		check_admin_referer( 'upgrade-core' );

		$files = (array) HMWP_MS_Utils::post( 'checked' );

		// Valid files to upgrade?
		foreach ( $files as $file ) {
			if ( ! isset( $this->updates['updates'][$file] ) )
				continue;

			// Get local file
			$local = HMWP_MS_PATH.'/libraries/IDS/'.$file;

			if ( ! file_exists( $local ) ) {
				wp_die( new WP_Error( 'hmwp_ms_upgrade_file_missing', sprintf( __( '%s does not exist.', 'mute-screamer' ), esc_html( $file ) ) ) );
			}

			if ( ! @is_readable( $local ) ) {
				wp_die( new WP_Error( 'hmwp_ms_upgrade_file_read_error', sprintf( __( 'Can not read file %s.', 'mute-screamer' ), esc_html( $file ) ) ) );
			}

			$local = file_get_contents( $local );

			// Fetch remote file
			$remote = $this->remote_get( $this->updates['updates'][$file]->revision_file_url );

			if ( $remote['body'] == '' )
				wp_die( new WP_Error( 'hmwp_ms_upgrade_error', __( 'Could not connect to phpids.org, please try again later.', 'mute-screamer' ) ) );

			$remote = $remote['body'];

			$diff_files[$file] = new stdClass;
			$diff_files[$file]->name = $file;
			$diff_files[$file]->diff = HMWP_MS_Utils::text_diff( $local, $remote );
		}

		if ( empty( $diff_files ) ) {
			wp_redirect( admin_url( 'update-core.php' ) );
			exit;
		}

		$url = 'update.php?action=hmwp_ms_upgrade_run&files=' . urlencode( implode( ',', $files ) );
		$url = wp_nonce_url( $url, 'bulk-update-hmwp_ms' );

		$this->admin_header( __( 'Update Mute Screamer', 'mute-screamer' ) );

		$data['url'] = $url;
		$data['diff_files'] = $diff_files;

		HMWP_MS_Utils::view( 'admin_update_diff', $data );
		include(ABSPATH . 'wp-admin/admin-footer.php');
	}

	/**
	 * Admin header, because we are firing our own action
	 * in /wp-admin/update.php which does not set this up
	 * for us.
	 *
	 * @param string
	 * @return void
	 */
	private function admin_header( $title ) {
		// Admin header requires these variables to be in scope
		// TODO: Test for multisite variables that need to be in scope
		global $hook_suffix, $pagenow, $is_iphone, $current_screen, $user_identity, $wp_locale, $wp_version;
		require_once(ABSPATH . 'wp-admin/admin-header.php');
	}

	/**
	 * Display upgrade page, setup the iframe to run the upgrade
	 *
	 * @return void
	 */
	public function do_upgrade() {
		if ( ! current_user_can( 'update_plugins' ) )
			wp_die( __( 'You do not have sufficient permissions to update Mute Screamer for this site.', 'mute-screamer' ) );

		check_admin_referer( 'hmwp_ms-upgrade-diff' );

		$url = HMWP_MS_Utils::post( 'url' );
		$this->admin_header( __( 'Update Mute Screamer', 'mute-screamer' ) );

		// The $url below will invoke do_upgrade_run
		echo '<div class="wrap">';
		screen_icon( 'plugins' );
		echo '<h2>' . __( 'Update Mute Screamer', 'mute-screamer' ) . '</h2>';
		echo "<iframe src='$url' style='width: 100%; height: 100%; min-height: 750px;' frameborder='0'></iframe>";
		echo '</div>';

		include(ABSPATH . 'wp-admin/admin-footer.php');
	}

	/**
	 * This is in an iframe
	 *
	 * @return void
	 */
	public function do_upgrade_run() {
		$upgrade_files = array(
			'default_filter.xml',
			'Converter.php',
		);
		$files = HMWP_MS_Utils::get( 'files' );
		$files = explode( ',', $files );

		if ( ! current_user_can( 'update_plugins' ) )
			wp_die( __( 'You do not have sufficient permissions to update Mute Screamer for this site.', 'mute-screamer' ) );

		check_admin_referer( 'bulk-update-hmwp_ms' );

		// Valid files to upgrade?
		foreach ( $files as $key => $val ) {
			if ( ! in_array( $val, $upgrade_files ) )
				wp_die( sprintf( __( "%s can't be upgraded.", 'mute-screamer' ), esc_html( $val ) ) );

			// Fetch file contents from cache
			$files[$val] = $this->remote_get( $this->updates['updates'][$val]->revision_file_url );
			unset( $files[$key] ); // Remove existing integer based index
		}

		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once 'hmwp_ms/Upgrader.php';
		wp_enqueue_script( 'jquery' );
		iframe_header();

		$upgrader = new HMWP_MS_Upgrader();
		$res = $upgrader->upgrade( $files );

		// All good? Clear the update array, reset transients
		if ( $res ) {
			// Remove the files we updated from the update array
			foreach ( $files as $key => $file ) {
				unset( $this->updates['updates'][$key] );
			}

			// Did we update everything?
			// Only clear the update array and cache if there are no files left to update
			if ( empty( $this->updates['updates'] ) ) {
				$this->updates['updates'] = array();
				delete_site_transient( 'hmwp_ms_requests_cache' );
			}

			set_site_transient( 'hmwp_ms_update', $this->updates, $this->timeout );
		}

		iframe_footer();
	}
}
