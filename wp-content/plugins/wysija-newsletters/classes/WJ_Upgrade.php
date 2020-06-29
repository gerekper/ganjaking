<?php
defined('WYSIJA') or die('Restricted access');
class WJ_Upgrade extends WYSIJA_object {
	/**
	* A static variable that holds a dinamic instance of the class
	* @var [object||null]
	*/
	public static $instance = null;

	public static $plugins = array( 'wysija-newsletters/index.php', 'wysija-newsletters-premium/index.php' );

	public static $baseurl = array(
		'core' => 'https://downloads.wordpress.org/plugin/',
		'packager' => 'http://packager.mailpoet.com/release/',
	);

	public static function hook(){
		null === self::$instance and self::$instance = new self;

		if ( ! is_admin() ) {
			return;
		}

		self::$baseurl = (object) self::$baseurl;

		add_action( 'current_screen', array( self::$instance, 'setup_bulk_screen' ) );
		add_action( 'shutdown', array( self::$instance, 'setup_bulk_screen_footer' ) );
		add_action( 'current_screen', array( self::$instance, 'iframe_intercept' ) );

		add_action( 'init', array( self::$instance, 'update_warning' ) );

		add_filter( 'pre_set_site_transient_update_plugins', array( self::$instance, 'pre_set_site_transient_update_plugins' ), 100 );
	}

	public function update_warning() {
		if ( ! is_admin() ){
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! ( ( is_multisite() && current_user_can( 'manage_network' ) ) || current_user_can( 'update_plugins' ) ) ){
			return;
		}

		if ( ! function_exists( 'get_plugin_data' ) ){
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$current = get_site_transient( 'update_plugins' );

		foreach ( self::$plugins as $plugin ){
			if ( isset( $current->response[ $plugin ] ) ){
				$data = self::get_plugin_data( $plugin );

				if ( version_compare( $current->response[ $plugin ]->new_version, $data->info->Version, '<=' ) ){
					continue;
				}

				$this->notice(
					sprintf(
						__( 'Hey! %1$s has an update (version %2$s), <a href="%3$s">click here to update</a>.', WYSIJA ),
						'<strong>' . esc_attr( $data->info->Name ) . '</strong>',
						$current->response[ $plugin ]->new_version,
						wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $plugin, 'upgrade-plugin_' . $plugin )
					),
					true,
					true
				);
			}
		}
	}

  //$titlelink=str_replace(array('[link]','[\link]'), array('<a href="">','</a>'),'');



  public function update_plugin_complete_actions( $update_actions, $mixed = null, $plugin = null ){
		$actions = array(
			'refresh_page' => '<a href="#" onclick="window.parent.location.reload(true);return false;" title="' . esc_attr__( 'Refresh the page you current are!', WYSIJA ) . '" target="_parent">' . __( 'Return to MailPoet', WYSIJA ) . '</a>'
		);

		return $actions;
	}

	public function iframe_intercept( $current_screen ) {
        $is_mailpoet_page = preg_match('/^mailpoet.*?_page_wysija_config$/i', $current_screen->base);
        $is_packager_switcher = (isset( $_GET['action'] ) && $_GET['action'] === 'packager-switch');

        if(!$is_mailpoet_page || !$is_packager_switcher) {
			return;
        }

        if (!(wp_verify_nonce( $_GET['_wpnonce'], $_GET['action'] ) === 1)) {
            return;
        }

		// Require the Updater classes
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$to = (isset($_GET['stable']) && $_GET['stable']?'stable':'beta');

		add_filter( 'pre_site_transient_update_plugins', array( $this, 'pre_site_transient_update_plugins' ) );

		$plugins = array();

		// Check for the action, it might be upgrading or installing
		$action = 'upgrade';
		if ( isset( $_GET['_mp_action'] ) && in_array( $_GET['_mp_action'], array( 'upgrade', 'install' ) ) ){
			$action = strtolower( $_GET['_mp_action'] );
		}

		foreach ( self::$plugins as $k => $plugin ) {
			if ( is_plugin_active( $plugin ) ){
				$plugins[] = $plugin;
			}
		}

		// Ajust the Padding/margin of the iFrame
		define( 'IFRAME_REQUEST', true );
		echo "<div style='margin: 0 20px;'>";

		// Thats how WordPress calls for an iFrame page
		wp_enqueue_script( 'jquery' );
		iframe_header();

		if ( $action === 'upgrade' ) {
			add_filter( 'update_bulk_plugins_complete_actions', array( $this, 'update_plugin_complete_actions' ), 10, 2 );
			$upgrader = new Plugin_Upgrader( new Bulk_Plugin_Upgrader_Skin( compact( 'nonce', 'url' ) ) );
			$upgrader->bulk_upgrade( $plugins );
		} elseif ( $action === 'install' ) {
			// If the action is install, it will only happen if it's the Premium
			add_filter( 'install_plugin_complete_actions', array( $this, 'update_plugin_complete_actions' ), 10, 3 );
			$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin() );
			$result   = $upgrader->install( self::get_url( self::$plugins[1], WYSIJA::is_beta(), 'zip' ) );
		}

		iframe_footer();
		echo '</div>';

		remove_filter( 'pre_site_transient_update_plugins', array( $this, 'pre_site_transient_update_plugins' ) );

		$model_config = WYSIJA::get( 'config', 'model' );
		$model_config->save( array( 'beta_mode' => ( $to === 'stable' ? false : true ) ) );
		set_site_transient( 'update_plugins', '' );

		exit();
	}

	/**
	 * A static method to grab the url from the packager to grab the ZIP
	 * or the version of the plugin there.
	 *
	 * @uses bool_from_yn
	 * @uses esc_url
	 * @uses add_query_arg
	 *
	 * @param  string			$package 	Which package you want to grab
	 * @param  boolean|string	$beta		Beta URL or not
	 * @param  string  			$action		Which kind of URL you need? [zip|check]
	 * @param  string  			$version	The version you want the URL to be related to
	 * @return string|null      It will return the URL from the packager related to the asked action
	 */
	public static function get_url( $package = null, $beta = false, $action = 'zip', $version = null ){
		if ( is_string( $beta ) ){
			if ( $beta === 'beta' ) {
				$beta = true;
			} else {
				$beta = bool_from_yn( $beta );
			}
		} else {
			$beta = (bool) $beta;
		}

		if ( ! in_array( $action, array( 'zip', 'check' ) ) ) {
			return null;
		}

		$slug = self::get_slug( $package );

		if ( true === $beta || 'wysija-newsletters-premium' === $slug ) {
			$url = self::$baseurl->packager . $action;

			$params = array(
				'key' => self::get_slug( $package ),
			);

			if ( $beta === true ){
				$params['beta'] = 'true';
			}

			$url = add_query_arg( $params, $url );

			return (string) $url;
		} else {
			return (string) self::$baseurl->core . $slug . '.zip';
		}
	}

	public static function get_version( $package = null, $beta = false ){
		$request = wp_remote_get( self::get_url( $package, $beta, 'check' ) );

		if ( is_wp_error( $request ) ){
			return false;
		}

		$version = wp_remote_retrieve_body( $request );

		return $version;
	}

	public static function get_slug( $package = null ){
		switch ( $package ) {
			case self::$plugins[1]:
			case 'premium':
			case 'wysija-newsletters-premium':
				return 'wysija-newsletters-premium';
				break;

			case self::$plugins[0]:
			case 'base':
			case 'wysija-newsletters':
			default:
				return 'wysija-newsletters';
				break;
		}
	}

	public static function get_plugin_data( $package = null, $beta = false, $new_version = false ){
		$data = (object) array();
		if ( is_null( $package ) ){
			return $data;
		}

		$data->id      = 27505;
		$data->slug    = self::get_slug( $package );
		$data->package = self::get_url( $package, $beta, 'zip' );

		if ( function_exists( 'get_plugin_data' ) ){
			$data->info = (object) get_plugin_data( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . $package );
		}

		if ( $data->slug === 'wysija-newsletters' ){
			$data->url = "https://wordpress.org/plugins/{$data->slug}/";
		} else {
			$data->url = 'http://www.mailpoet.com/wordpress-newsletter-plugin-premium/';
		}
		$data->url = esc_url( $data->url );

		if ( $new_version !== false ){
			$data->new_version = (string) $new_version;
		}

		return $data;
	}

	public function pre_set_site_transient_update_plugins( $update_data ){

		if ( ! function_exists( 'get_plugin_data' ) ){
			return (object) array();
		}

		if ( ! is_object( $update_data ) && strlen( trim( $update_data ) ) === 0 ){
			return (object) array();
		}

		if ( ! isset( $update_data->last_checked ) ){
			$update_data->last_checked = 0;
		}

		if ( ( time() - ( 60 * 60 * 12 ) ) > ( $update_data->last_checked ) ) { // Just check once every 12 hours
			return $update_data;
		}

		foreach ( self::$plugins as $plugin ){
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( ! is_plugin_active( $plugin ) ){
				continue;
			}

			if ( ! WYSIJA::is_beta() && $plugin === 'wysija-newsletters/index.php' ) {
				continue;
			}

			$version = self::get_version( $plugin, WYSIJA::is_beta() );
			$update_data->last_checked = time();

			if ( version_compare( WYSIJA::get_version( $plugin ), $version, '>=' ) ){
				continue;
			}

			$update_data->response[ $plugin ] = self::get_plugin_data( $plugin, WYSIJA::is_beta(), $version );
		}

		return $update_data;
	}

	public function pre_site_transient_update_plugins( $transient ) {
		$update_data = (object) array(
			'last_checked' => time() - 10,
			'response' => array()
		);
		$to = (isset($_GET['stable']) && $_GET['stable']?'stable':'beta');

		foreach ( self::$plugins as $plugin ) {
			if ( ! function_exists( 'is_plugin_active' ) ){
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if ( ! is_plugin_active( $plugin ) ) {
				continue;
			}

			$update_data->response[ $plugin ] = self::get_plugin_data( $plugin, $to, self::get_version( $plugin, $to ) );
		}

		return $update_data;
	}

	public function setup_bulk_screen( $current_screen ) {
		global $title, $parent_file, $submenu_file;

		if ( strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST' && in_array( $current_screen->id, array( 'update-core', 'plugins' ) ) ) {
			if ( ! isset( $_POST['checked'] ) ){
				return;
			}

			$plugins = (array) $_POST['checked'];
			$plugins = array_map( 'urldecode', $plugins );

			$__intersection = array_intersect( $plugins, self::$plugins );

			if ( empty( $__intersection ) ){
				return;
			}

			$action = (isset($_POST['action']) ? $_POST['action'] : null);

			switch($action) {
				case 'delete-selected':

				break;

				case 'deactivate-selected':
					if ( in_array( self::$plugins[0], $plugins ) && ! in_array( self::$plugins[1], $plugins ) && is_plugin_active( self::$plugins[1] ) ){
						$plugins[] = self::$plugins[1];
					}
				break;

				case 'update-selected':
				case 'activate-selected':
					if ( in_array( self::$plugins[1], $plugins ) && ! in_array( self::$plugins[0], $plugins ) ){
						$plugins[] = self::$plugins[0];
					}
				break;
			}

			$_POST['checked'] = $plugins;

			return;
		}

		if ( $current_screen->id !== 'update' ){
			return;
		}

		if ( $_GET['action'] !== 'upgrade-plugin' ){
			return;
		}

		if ( $_GET['action'] === 'upgrade-plugin' && ! in_array( $_GET['plugin'], self::$plugins ) ){
			return;
		}

		foreach ( self::$plugins as $plugin ) {
			if ( ! is_plugin_active( $plugin ) ) {
				return;
			}
		}

		$_GET['action'] = $_REQUEST['action'] = 'update-selected';
		$_GET['plugins'] = $_REQUEST['plugins'] = implode( ',', array_map( 'urlencode', self::$plugins ) );
		$_GET['_wpnonce'] = $_REQUEST['_wpnonce'] = wp_create_nonce( 'bulk-update-plugins' );
		$_GET['_wysija_bulk_screen'] = $_REQUEST['_wysija_bulk_screen'] = true;

		$title        = esc_attr__( 'Update Plugin' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugins.php';
		require_once(ABSPATH . 'wp-admin/admin-header.php');
		echo
			"<div class='wrap'>" .
				'<h2>' . esc_attr( $title ) . '</h2>';

	}

	public function setup_bulk_screen_footer(){
		if ( ! isset( $_GET['_wysija_bulk_screen'] ) ){
			return;
		}
		echo '</div>';
		include(ABSPATH . 'wp-admin/admin-footer.php');
	}
}