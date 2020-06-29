<?php
/**
 * Upgrader module.
 * Handles all plugin updates and installations.
 *
 * @since  4.1.0
 * @package WPMUDEV_Dashboard
 */

/**
 * The update/installation handler.
 */
class WPMUDEV_Dashboard_Upgrader {

	/**
	 * Stores the last error that happened during any upgrade/install process.
	 *
	 * @var array With elements 'code' and 'message'.
	 */
	protected $error = false;

	/**
	 * Stores the log from any upgrade/install process.
	 *
	 * @var array
	 */
	protected $log = false;

	/**
	 * Stores the new version after from any upgrade process.
	 *
	 * @var array
	 */
	protected $new_version = false;

	/**
	 * Tracks core update results during processing.
	 *
	 * @var array
	 * @access protected
	 */
	protected $update_results = array();

	/**
	 * Set up actions for the Upgrader module.
	 *
	 * @since 4.1.0
	 * @internal
	 */
	public function __construct() {
		// Enable auto updates for enabled projects
		add_filter( 'auto_update_plugin', array( $this, 'maybe_auto_update' ), 10, 2 );
		add_filter( 'auto_update_theme', array( $this, 'maybe_auto_update' ), 10, 2 );

		// Apply FTP credentials to install/update plugins and themes.
		add_action(
			'plugins_loaded',
			array( $this, 'apply_credentials' )
		);
	}

	/**
	 * Captures core update results from hook, only way to get them
	 *
	 * @param $results
	 */
	public function capture_core_update_results( $results ) {
		$this->update_results = $results;
	}

	/**
	 * Checks if an installed project is the latest version or if an update
	 * is available.
	 *
	 * @since  4.0.0
	 * @param  int $project_id The project-ID.
	 * @return bool True means there is an update (local project is outdated)
	 */
	public function is_update_available( $project_id ) {
		if ( ! $this->is_project_installed( $project_id ) ) {
			return false;
		}

		$local = WPMUDEV_Dashboard::$site->get_cached_projects( $project_id );
		$local_version = $local['version'];

		$remote = WPMUDEV_Dashboard::$api->get_project_data( $project_id );
		$remote_version = $remote['version'];

		return version_compare( $local_version, $remote_version, 'lt' );
	}

	/**
	 * Checks if a certain project is localy installed.
	 *
	 * @since  4.0.0
	 * @param  int $project_id The project to check.
	 * @return bool True if the project is installed.
	 */
	public function is_project_installed( $project_id ) {
		$data = WPMUDEV_Dashboard::$site->get_cached_projects( $project_id );
		return ( ! empty( $data ));
	}

	/**
	 * Get the nonced admin url for installing a given project.
	 *
	 * @since 1.0.0
	 * @param  int $project_id The project to install.
	 * @return string|bool Generated admin url for installing the project.
	 */
	public function auto_install_url( $project_id ) {
		// Download possible?
		if ( ! WPMUDEV_Dashboard::$api->has_key() ) { return false; }

		$data = WPMUDEV_Dashboard::$api->get_projects_data();
		$project = WPMUDEV_Dashboard::$api->get_project_data( $project_id );

		// Valid project ID?
		if ( empty( $project ) ) { return false; }

		// Already installed?
		if ( $this->is_project_installed( $project_id ) ) { return false; }

		// Auto-update possible for this project?
		if ( empty( $project['autoupdate'] ) ) { return false; }
		if ( 1 != $project['autoupdate'] ) { return false; }

		// User can install the project (license and tech requirements)?
		if ( ! $this->user_can_install( $project_id ) ) { return false; }
		if ( ! $this->is_project_compatible( $project_id ) ) { return false; }

		// All good, create the download URL.
		$url = false;
		if ( 'plugin' == $project['type'] ) {
			$url = wp_nonce_url(
				self_admin_url( "update.php?action=install-plugin&plugin=wpmudev_install-$project_id" ),
				"install-plugin_wpmudev_install-$project_id"
			);
		} elseif ( 'theme' == $project['type'] ) {
			$url = wp_nonce_url(
				self_admin_url( "update.php?action=install-theme&theme=wpmudev_install-$project_id" ),
				"install-theme_wpmudev_install-$project_id"
			);
		}

		return $url;
	}

	/**
	 * Get the nonced admin url for updating a given project.
	 *
	 * @since 1.0.0
	 * @param  int $project_id The project to install.
	 * @return string|bool Generated admin url for updating the project.
	 */
	public function auto_update_url( $project_id ) {
		// Download possible?
		if ( ! WPMUDEV_Dashboard::$api->has_key() ) { return false; }

		$project = WPMUDEV_Dashboard::$api->get_project_data( $project_id );

		// Valid project ID?
		if ( empty( $project ) ) { return false; }

		// Already installed?
		if ( ! $this->is_project_installed( $project_id ) ) { return false; }

		$local = WPMUDEV_Dashboard::$site->get_cached_projects( $project_id );
		if ( empty( $local ) ) { return false; }

		// Auto-update possible for this project?
		if ( empty( $project['autoupdate'] ) ) { return false; }
		if ( 1 != $project['autoupdate'] ) { return false; }

		// User can install the project (license and tech requirements)?
		if ( ! $this->user_can_install( $project_id ) ) { return false; }
		if ( ! $this->is_project_compatible( $project_id ) ) { return false; }

		// All good, create the update URL.
		$url = false;
		if ( 'plugin' == $project['type'] ) {
			$update_file = $local['filename'];
			$url = wp_nonce_url(
				self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $update_file ),
				'upgrade-plugin_' . $update_file
			);
		} elseif ( 'theme' == $project['type'] ) {
			$update_file = $local['slug'];
			$url = wp_nonce_url(
				self_admin_url( 'update.php?action=upgrade-theme&theme=' . $update_file ),
				'upgrade-theme_' . $update_file
			);
		}

		return $url;
	}

	/**
	 * Check user permissions to see if we can install this project.
	 *
	 * @since  1.0.0
	 * @param  int  $project_id The project to check.
	 * @param  bool $only_license Skip permission check, only validate license.
	 * @return bool
	 */
	public function user_can_install( $project_id, $only_license = false ) {
		$data            = WPMUDEV_Dashboard::$api->get_projects_data();
		$membership_type = WPMUDEV_Dashboard::$api->get_membership_type( $license_for );

		// Basic check if we have valid data.
		if ( empty( $data['projects'] ) ) {
			return false;
		}
		if ( empty( $data['projects'][ $project_id ] ) ) {
			return false;
		}

		$project = $data['projects'][ $project_id ];

		if ( ! $only_license ) {
			if ( ! WPMUDEV_Dashboard::$site->allowed_user() && ! current_user_can( 'edit_plugins' ) ) {
				return false;
			}
			//if ( ! $this->can_auto_install( $project['type'] ) ) { return false; }
		}

		$is_upfront = WPMUDEV_Dashboard::$site->id_upfront == $project_id;
		$package    = isset( $project['package'] ) ? $project['package'] : '';
		$access     = false;

		if ( 'full' == $membership_type ) {
			// User has full membership.
			$access = true;
		} else if ( 'single' == $membership_type && $license_for == $project_id ) {
			// User has single membership for the requested project.
			$access = true;
		} else if ( 'free' == $project['paid'] ) {
			// It's a free project. All users can install this.
			$access = true;
		} else if ( 'lite' == $project['paid'] ) {
			// It's a lite project. All users can install this.
			$access = true;
		} else if ( 'single' == $membership_type && $package && $package == $license_for ) {
			// A packaged project that the user bought.
			$access = true;
		} else if ( $is_upfront && 'single' == $membership_type ) {
			// User wants to get Upfront parent theme.
			$access = true;
		}

		return $access;
	}

	/**
	 * Check whether this project is compatible with the current install based
	 * on requirements from API.
	 *
	 * @since  1.0.0
	 * @param  int    $project_id The project to check.
	 * @param  string $reason If incompatible the reason is stored in this
	 *         output-parameter.
	 * @return bool True if the project is compatible with current site.
	 */
	public function is_project_compatible( $project_id, &$reason = '' ) {
		$data = WPMUDEV_Dashboard::$api->get_projects_data();
		$reason = '';

		if ( empty( $data['projects'][ $project_id ] ) ) {
			return false;
		}

		$project = $data['projects'][ $project_id ];
		if ( empty( $project['requires'] ) ) {
			$reason = 'unknown requirements';
			return false;
		}

		// Skip multisite only products if not compatible.
		if ( 'ms' == $project['requires'] && ! is_multisite() ) {
			$reason = 'multisite';
			return false;
		}

		// Skip BuddyPress only products if not active.
		if ( 'bp' == $project['requires'] && ! defined( 'BP_VERSION' ) ) {
			$reason = 'buddypress';
			return false;
		}

		return true;
	}

	/**
	 * Can plugins be automatically installed? Checks filesystem permissions
	 * and WP configuration to determine.
	 *
	 * @since  1.0.0
	 * @param  string $type Either plugin or theme.
	 * @return bool True means that projects can be downloaded automatically.
	 */
	public function can_auto_install( $type ) {
		$writable = false;

		if ( ! function_exists( 'get_filesystem_method' ) ) {
			include_once ABSPATH . '/wp-admin/includes/file.php';
		}

		// Are we dealing with direct access FS?
		if ( 'direct' == get_filesystem_method() ) {
			if ( 'plugin' == $type ) {
				$root = WP_PLUGIN_DIR;
			} else {
				$root = WP_CONTENT_DIR . '/themes';
			}

			$writable = is_writable( $root );
		}

		// If we don't have write permissions, do we have FTP settings?
		if ( ! $writable ) {
			$writable = defined( 'FTP_USER' )
				&& defined( 'FTP_PASS' )
				&& defined( 'FTP_HOST' );
		}

		// Lastly, if no other option worked, do we have SSH settings?
		if ( ! $writable ) {
			$writable = defined( 'FTP_USER' )
				&& defined( 'FTP_PUBKEY' )
				&& defined( 'FTP_PRIKEY' );
		}

		return $writable;
	}

	/**
	 * Read FTP credentials from the POST data and store them in a httponly
	 * cookie, with expiration 15 mintues.
	 *
	 * @since  1.0.0
	 * @return bool True on success.
	 */
	public function remember_credentials() {
		if ( ! isset( $_POST['ftp_user'] ) ) { return false; }
		if ( ! isset( $_POST['ftp_pass'] ) ) { return false; }
		if ( ! isset( $_POST['ftp_host'] ) ) { return false; }

		// Store user + host in DB so we have correct default values next time.
		$credentials = (array) get_option( 'ftp_credentials', array( 'hostname' => '', 'username' => '' ) );
		$credentials['hostname'] = $_POST['ftp_host'];
		$credentials['username'] = $_POST['ftp_user'];
		update_option( 'ftp_credentials', $credentials );

		// Prepare and set the httponly cookie for next 15 minutes.
		$cookie_data = array(
			urlencode( $_POST['ftp_user'] ),
			urlencode( $_POST['ftp_pass'] ),
			urlencode( $_POST['ftp_host'] ),
		);
		$expire = time() + 900; // 15minutes * 60seconds.

		$secure_cookie = 'https' === wp_parse_url( get_option( 'home' ), PHP_URL_SCHEME );

		return setcookie(
			COOKIEHASH . '-dev_ftp_data',
			implode( '&', $cookie_data ),
			$expire,
			COOKIEPATH,
			COOKIE_DOMAIN,
			$secure_cookie,
			true
		);
	}

	/**
	 * If we have a cookie with FTP credentials we will apply them here so
	 * WordPress can use them to install/update plugins.
	 *
	 * @since  1.0.0
	 */
	public function apply_credentials() {
		$secure_cookie = 'https' === wp_parse_url( get_option( 'home' ), PHP_URL_SCHEME );
		$cookie_name   = COOKIEHASH . '-dev_ftp_data';
		if ( empty( $_COOKIE[ $cookie_name ] ) ) { return; }

		$cookie_data = explode( '&', $_COOKIE[ $cookie_name ] );
		if ( 3 != count( $cookie_data ) ) {
			// Clear invalid cookie!
			setcookie(
				$cookie_name,
				'',
				1,
				COOKIEPATH,
				COOKIE_DOMAIN,
				$secure_cookie,
				true
			);
			return;
		}

		// Set the const values so WP can use them.
		if ( ! defined( 'FTP_USER' ) ) {
			define( 'FTP_USER', urldecode( $cookie_data[0] ) );
		}
		if ( ! defined( 'FTP_PASS' ) ) {
			define( 'FTP_PASS', urldecode( $cookie_data[1] ) );
		}
		if ( ! defined( 'FTP_HOST' ) ) {
			define( 'FTP_HOST', urldecode( $cookie_data[2] ) );
		}
	}

	/**
	 * Checks requirements, install-status, etc before upgrading the specific
	 * WPMU DEV project. Returns the project slug for upgrader.
	 *
	 * @since  1.0.0
	 * @param  int $pid Project ID.
	 * @return array Details about the project needed by upgrade().
	 */
	protected function prepare_dev_upgrade( $pid ) {
		$resp = array(
			'slug' => 'wpmudev_install-' . $pid,
			'filename' => '',
			'type' => '',
		);

		// Refresh local project cache before the update starts.
		WPMUDEV_Dashboard::$site->refresh_local_projects('local' );
		$local_projects = WPMUDEV_Dashboard::$site->get_cached_projects();

		// Now make sure that the project is updated, no matter what!
		WPMUDEV_Dashboard::$api->calculate_upgrades( $local_projects, $pid );

		if ( ! $this->is_project_installed( $pid ) ) {
			$this->set_error( $pid, 'UPG.01', __( 'Project not installed', 'wpmudev' ) );
			return false;
		}

		$project = WPMUDEV_Dashboard::$site->get_project_infos( $pid );
		$resp['type'] = $project->type;
		$resp['filename'] = $project->filename;

		// Upfront special: If updating a child theme or upfront dependant first update parent.
		if ( $project->need_upfront ) {
			$upfront = WPMUDEV_Dashboard::$site->get_project_infos( WPMUDEV_Dashboard::$site->id_upfront );

			// Time condition to avoid repeated UF checks if there was an error.
			$check = (int) WPMUDEV_Dashboard::$site->get_option( 'last_check_upfront' );

			if ( ! $upfront->is_installed ) {
				if ( time() > $check + (3 * MINUTE_IN_SECONDS) ) {
					WPMUDEV_Dashboard::$site->set_option( 'last_check_upfront', time() );
					$this->install( $upfront->pid );
				}
			} elseif ( $upfront->version_installed != $upfront->version_latest ) {
				if ( time() > $check + (3 * MINUTE_IN_SECONDS) ) {
					WPMUDEV_Dashboard::$site->set_option( 'last_check_upfront', time() );
					$this->upgrade( $upfront->pid, false );
				}
			}
		}

		return $resp;
	}

	/**
	 * Download and install a single plugin/theme update.
	 *
	 * A lot of logic is borrowed from ajax-actions.php
	 *
	 * @since  4.0.0
	 * @param  int/string $pid The project ID or a plugin slug.
	 * @return bool True on success.
	 */
	public function upgrade( $pid ) {
		$this->clear_error();
		$this->clear_log();
		$this->clear_version();


		// Is a WPMU DEV project?
		$is_dev = is_numeric( $pid );

		if ( $is_dev ) {
			$pid = (int) $pid;
			$infos = $this->prepare_dev_upgrade( $pid );
			if ( ! $infos ) { return false; }

			$filename = ( 'theme' == $infos['type'] ) ? dirname( $infos['filename'] ) : $infos['filename'];
			$slug = $infos['slug'];
			$type = $infos['type'];
		} elseif ( is_string( $pid ) ) {
			// No need to check if the plugin exists/is installed. WP will check it.
			list( $type, $filename ) = explode( ':', $pid );
			$slug = ( 'plugin' == $type && false !== strpos( $filename, '/' ) ) ? dirname( $filename ) : $filename; //TODO can't update hello dolly "hello.php"
		} else {
			$this->set_error( $pid, 'UPG.07', __( 'Invalid upgrade call', 'wpmudev' ) );
			return false;
		}

		if( ! $this->can_auto_install( $type ) ){
			$this->set_error( $pid, 'UPG.10', __( 'Insufficient filesystem permissions', 'wpmudev' ) );
			return false;
		}

		// For plugins_api/themes_api..
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );
		include_once( ABSPATH . 'wp-admin/includes/file.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$result = false;
		$success = false;

		/*
		 * Set before the update:
		 * WP will refresh local cache via action-hook before the install()
		 * method is finished. That refresh call must scan the FS again.
		 */
		if ( $is_dev ) {
			WPMUDEV_Dashboard::$site->clear_local_file_cache();
		}

		switch ( $type ) {
			case 'plugin':

				wp_update_plugins();

				$active_blog = is_plugin_active( $filename );
				$active_network = is_multisite() && is_plugin_active_for_network( $filename );

				$upgrader = new Plugin_Upgrader( $skin );
				$result = $upgrader->upgrade( $filename );

				/*
				 * Note: The following plugin activation is an intended and
				 * needed step. During upgrade() WordPress deactivates the
				 * plugin network- and site-wide. By default the user would
				 * see a upgrade-results page with the option to activate the
				 * plugin again. We skip that screen and restore original state.
				 */
				if ( $active_blog ) {
					activate_plugin( $filename, false, false, true );
				}
				if ( $active_network ) {
					activate_plugin( $filename, false, true, true );
				}
				break;

			case 'theme':

				wp_update_themes();

				$upgrader = new Theme_Upgrader( $skin );
				$result = $upgrader->upgrade( $filename );
				break;

			default:
				$this->set_error( $pid, 'UPG.08', __( 'Invalid upgrade call', 'wpmudev' ) );
				return false;
		}

		$this->log = $skin->get_upgrade_messages();
		if ( is_wp_error( $skin->result ) ) {
			if ( in_array( $skin->result->get_error_code() , array( 'remove_old_failed', 'mkdir_failed_ziparchive' ) ) ) {
				$this->set_error( $pid, 'UPG.10', $skin->get_error_messages() );
			} else {
				$this->set_error( $pid, 'UPG.04', $skin->result->get_error_message() );
			}
			return false;
		} elseif ( in_array( $skin->get_errors()->get_error_code() , array( 'remove_old_failed', 'mkdir_failed_ziparchive' ) ) ) {
			$this->set_error( $pid, 'UPG.10', $skin->get_error_messages() );
			return false;
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$this->set_error( $pid, 'UPG.09', $skin->get_error_messages() );
			return false;
		} elseif ( false === $result ) {
			global $wp_filesystem;

			$error = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$error = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			$this->set_error( $pid, 'UPG.05', $error );
			return false;
		} elseif ( true === $result ) { //this is success!
			if ( 'plugin' == $type ) {
				$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $filename );
				$this->new_version = $plugin_data['Version'];
			} else {
				$theme = wp_get_theme( $filename );
				$this->new_version = $theme->get( 'Version' );
			}

			// API call to inform wpmudev site about the change, as it's a single we can let it do that at the end to avoid multiple pings
			WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
			return true;
		}

		// An unhandled error occurred.
		$this->set_error( $pid, 'UPG.06', __( 'Update failed for an unknown reason.', 'wpmudev' ) );
		return false;
	}

	/**
	 * Install a new plugin or theme.
	 *
	 * A lot of logic is borrowed from ajax-actions.php
	 *
	 * @since  4.0.0
	 * @param  int $pid The project ID.
	 * @return bool True on success.
	 */
	public function install( $pid ) {
		$this->clear_error();
		$this->clear_log();

		// Is a WPMU DEV project?
		$is_dev = is_numeric( $pid );

		if ( $is_dev ) {
			$pid = (int) $pid;

			if ( $this->is_project_installed( $pid ) ) {
				$this->set_error( $pid, 'INS.01', __( 'Already installed', 'wpmudev' ) );
				return false;
			}

			$project = WPMUDEV_Dashboard::$site->get_project_infos( $pid );
			if ( ! $project ) {
				$this->set_error( $pid, 'INS.04', __( 'Invalid project', 'wpmudev' ) );
				return false;
			}

			$slug = 'wpmudev_install-' . $pid;
			$type = $project->type;

			if( ! $this->can_auto_install( $type ) ){
				$this->set_error( $pid, 'INS.09', __( 'Insufficient filesystem permissions', 'wpmudev' ) );
				return false;
			}

			// Make sure Upfront is available before an upfront theme or plugin is installed.
			if ( $project->need_upfront && ! WPMUDEV_Dashboard::$site->is_upfront_installed() ) {
				$this->install( WPMUDEV_Dashboard::$site->id_upfront );
			}
		} elseif ( is_string( $pid ) ) {
			// No need to check if the plugin exists/is installed. WP will check it.
			list( $type, $filename ) = explode( ':', $pid );
			$slug = ( 'plugin' == $type && false !== strpos( $filename, '/' ) ) ? dirname( $filename ) : $filename; //TODO can't update hello dolly "hello.php"
		} else {
			$this->set_error( $pid, 'INS.07', __( 'Invalid upgrade call', 'wpmudev' ) );
			return false;
		}

		// For plugins_api/themes_api..
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );
		include_once( ABSPATH . 'wp-admin/includes/file.php' );

		$skin = new WP_Ajax_Upgrader_Skin();

		/*
		 * Set before the update:
		 * WP will refresh local cache via action-hook before the install()
		 * method is finished. That refresh call must scan the FS again.
		 */
		if ( $is_dev ) {
			WPMUDEV_Dashboard::$site->clear_local_file_cache();
		}

		switch ( $type ) {
			case 'plugin':

				// Save on a bit of bandwidth.
				$api = plugins_api(
					'plugin_information',
					array(
						'slug' => sanitize_key( $slug ),
						'fields' => array( 'sections' => false ),
					)
				);

				if ( is_wp_error( $api ) ) {
					$this->set_error( $pid, 'INS.02', $api->get_error_message() );
					return false;
				}

				$upgrader = new Plugin_Upgrader( $skin );
				$result   = $upgrader->install( $api->download_link );
				break;

			case 'theme':

				// Save on a bit of bandwidth.
				$api = themes_api(
					'theme_information',
					array(
						'slug' => sanitize_key( $slug ),
						'fields' => array( 'sections' => false ),
					)
				);

				if ( is_wp_error( $api ) ) {
					$this->set_error( $pid, 'INS.02', $api->get_error_message() );
					return false;
				}

				$upgrader = new Theme_Upgrader( $skin );
				$result   = $upgrader->install( $api->download_link );
				break;

			default:
				$this->set_error( $pid, 'INS.08', __( 'Invalid upgrade call', 'wpmudev' ) );
				return false;
		}

		$this->log = $skin->get_upgrade_messages();
		if ( is_wp_error( $result ) ) {
			if ( 'mkdir_failed_ziparchive' === $skin->$result->get_error_code() ) {
				$this->set_error( $pid, 'INS.09', $skin->get_error_messages() );
			} else {
				$this->set_error( $pid, 'INS.05', $result->get_error_message() );
			}
			return false;
		} elseif ( is_wp_error( $skin->result ) ) {
			$this->set_error( $pid, 'INS.03', $skin->result->get_error_message() );
			return false;
		} elseif ( 'mkdir_failed_ziparchive' === $skin->get_errors()->get_error_code() ) {
			$this->set_error( $pid, 'INS.09', $skin->get_error_messages() );
			return false;
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$this->set_error( $pid, 'INS.06', $skin->get_error_messages() );
			return false;
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$error = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$error = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			$this->set_error( $pid, 'INS.08', $error );
			return false;
		}

		// API call to inform wpmudev site about the change, as it's a single we can let it do that at the end to avoid multiple pings
		WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();

		return true;
	}

	/**
	 * Upgrade WP Core to latest version
	 *
	 * A lot of logic is borrowed from WP_Automatic_Updater
	 *
	 * @since  4.4
	 * @return bool True on success.
	 */
	public function upgrade_core() {
		global $wp_version, $wpdb;

		$this->clear_error();
		$this->clear_log();
		$this->clear_version();

		/**
		 * mimic @see wp_maybe_auto_update()
		 */
		include_once( ABSPATH . 'wp-admin/includes/admin.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		add_action( 'automatic_updates_complete', array( $this, 'capture_core_update_results' ) );

		add_filter( "auto_update_core", '__return_true', 99999 ); //temporarily allow core autoupdates
		add_filter( "allow_major_auto_core_updates", '__return_true', 99999 ); //temporarily allow core autoupdates
		add_filter( "allow_minor_auto_core_updates", '__return_true', 99999 ); //temporarily allow core autoupdates
		add_filter( "auto_update_core", '__return_true', 99999 ); //temporarily allow core autoupdates
		add_filter( "auto_update_theme", '__return_false', 99999 );
		add_filter( "auto_update_plugin", '__return_false', 99999 );

		//TODO don't send email for successful updates
		//apply_filters( 'auto_core_update_send_email', true, $type, $core_update, $result )

		$upgrader = new WP_Automatic_Updater;

		/* ---- these checks are already run later, but we run them now so we can capture detailed errors --- */

		if ( $upgrader->is_disabled() || ( defined( 'WP_AUTO_UPDATE_CORE' ) && false === WP_AUTO_UPDATE_CORE ) ) {
			$this->set_error( 'core',
			                  'autoupdates_disabled',
			                  sprintf( __( 'You have disabled automatic core updates via define( \'WP_AUTO_UPDATE_CORE\', false ); in your wp-config.php or a filter. Remove that code to allow updating core by Automate or disable "WordPress Core" in your Automate settings. %1$sContact support%2$s if you need further assistance.',
			                               'wpmudev' ),
				                  '<a href="https://premium.wpmudev.org/hub/support/#get-support">',
				                  '</a>') );
			return false;
		}

		// Used to see if WP_Filesystem is set up to allow unattended updates.
		$skin = new Automatic_Upgrader_Skin;
		if ( ! $skin->request_filesystem_credentials( false, ABSPATH, false ) ) {
			$this->set_error( 'core', 'fs_unavailable', __( 'Could not access filesystem.' ) ); //this string is from core translation
			return false;
		}

		if ( $upgrader->is_vcs_checkout( ABSPATH ) ) {
			$this->set_error( 'core', 'is_vcs_checkout', __( 'Automatic core updates are disabled when WordPress is checked out from version control.', 'wpmudev' ) );
			return false;
		}

		wp_version_check(); // Check for Core updates
		$updates = get_site_transient( 'update_core' );
		if ( ! $updates || empty( $updates->updates ) )
			return false;

		$auto_update = false;
		foreach ( $updates->updates as $update ) {
			if ( 'autoupdate' != $update->response )
				continue;

			if ( ! $auto_update || version_compare( $update->current, $auto_update->current, '>' ) )
				$auto_update = $update;
		}

		if ( ! $auto_update ) {
			$this->set_error( 'core', 'update_unavailable', __( 'No WordPress core updates appear available.', 'wpmudev' ) );
			return false;
		}

		//compatiblity
		$php_compat = version_compare( phpversion(), $auto_update->php_version, '>=' );
		if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) ) {
			$mysql_compat = true;
		} else {
			$mysql_compat = version_compare( $wpdb->db_version(), $auto_update->mysql_version, '>=' );
		}

		if ( ! $php_compat || ! $mysql_compat ) {
			$this->set_error( 'core', 'incompatible', __( 'The new version of WordPress is incompatible with your PHP or MySQL version.', 'wpmudev' ) );
			return false;
		}

		// If this was a critical update failure last try, cannot update.
		$skip = false;
		$failure_data = get_site_option( 'auto_core_update_failed' );
		if ( $failure_data ) {
			if ( ! empty( $failure_data['critical'] ) )
				$skip = true;

			// Don't claim we can update on update-core.php if we have a non-critical failure logged.
			if ( $wp_version == $failure_data['current'] && false !== strpos( $auto_update->current, '.1.next.minor' ) )
				$skip = true;

			// Cannot update if we're retrying the same A to B update that caused a non-critical failure.
			// Some non-critical failures do allow retries, like download_failed.
			if ( empty( $failure_data['retry'] ) && $wp_version == $failure_data['current'] && $auto_update->current == $failure_data['attempted'] )
				$skip = true;

			if ( $skip ) {
				$this->set_error( 'core', 'previous_failure', __( 'There was a previous failure with this update. Please update manually instead.', 'wpmudev' ) );
				return false;
			}
		}

		//this is the only reason left this would fail
		if ( ! Core_Upgrader::should_update_to_version( $auto_update->current ) ) {
			$this->set_error( 'core',
			                  'autoupdates_disabled',
			                  sprintf( __( 'You have disabled automatic core updates via define( \'WP_AUTO_UPDATE_CORE\', false ); in your wp-config.php or a filter. Remove that code to allow updating core by Automate or disable "WordPress Core" in your Automate settings. %1$sContact support%2$s if you need further assistance.',
			                               'wpmudev' ),
			                           '<a href="https://premium.wpmudev.org/hub/support/#get-support">',
			                           '</a>') );
			return false;
		}

		/* -------------------------- */

		//ok we are good to give it a try
		$upgrader->run();

		//check populated var from hook
		if ( ! empty( $this->update_results['core'] ) ) {
			$update_result = $this->update_results['core'][0];

			$result      = $update_result->result;
			$this->log   = $update_result->messages;

			//yay we did it!
			if ( ! is_wp_error( $result ) ) {
				$this->new_version = $result;

				// API call to inform wpmudev site about the change, as it's a single we can let it do that at the end to avoid multiple pings
				WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
				return true;
			}

			$error_code = $result->get_error_code();
			$error_msg = $result->get_error_message();

			//if a rollback was run and errored append that to message.
			if ( $error_code === 'rollback_was_required' && is_wp_error( $result->get_error_data()->rollback ) ) {
				$rollback_result = $result->get_error_data()->rollback;
				$error_msg .= " Rollback: " . $rollback_result->get_error_message();
			}

			$this->set_error( 'core', $error_code, $error_msg );
			return false;
		}


		// An unhandled error occurred.
		$this->set_error( 'core', 'unknown_failure', __( 'Update failed for an unknown reason.', 'wpmudev' ) );
		return false;
	}

	/**
	 * This function checks if the specified project is configured for automatic
	 * upgrade in the background (without telling the user about the upgrade).
	 *
	 * If auto-upgrade is enabled then we enable it in the filter
	 *
	 * For dashboard it respects the setting "Enable
	 * automatic updates of WPMU DEV plugin" on the Manage page is enabled.
	 *
	 * @since  4.4
	 *
	 * @param  bool  $should_update Whether this item should be autoupdated
	 * @param object $item          Plugin or Theme object
	 *
	 * @return boolean $should_update
	 */
	public function maybe_auto_update( $should_update, $item ) {

		if ( isset( $item->pid ) ) { //DEV themes have this set
			$project_id = $item->pid;
		} else if ( ! empty( $item->slug ) && false !== strpos( $item->slug, 'wpmudev_install-' ) ) {
			//get the project_id
			list( , $project_id ) = explode( '-', $item->slug );
		} else {
			// Do nothing, not a DEV project
			return $should_update;
		}

		/*
		 * List of projects that will be automatically upgraded when the above
		 * flag is enabled.
		 */
		$auto_update_projects = apply_filters(
			'wpmudev_project_auto_update_projects',
			array(
				119, // WPMUDEV dashboard.
			)
		);

		if ( 119 == $project_id && ! WPMUDEV_Dashboard::$site->get_option( 'autoupdate_dashboard' ) ) {
			// Do nothing, auto-update is disabled for Dashboard plugin!
			return $should_update;
		}

		if ( in_array( $project_id, $auto_update_projects ) ) {
			return true;
		}

		return $should_update;
	}

	/**
	 * Stores the specific error details.
	 *
	 * @since 4.1.0
	 *
	 * @param string $pid     The PID that was installed/updated.
	 * @param string $code    Error code.
	 * @param string $message Error message.
	 */
	public function set_error( $pid, $code, $message ) {
		$this->error = array(
			'pid'     => $pid,
			'code'    => $code,
			'message' => $message,
		);

		error_log(
			sprintf( 'WPMU DEV Upgrader error: %s - %s.', $code, $message )
		);
	}

	/**
	 * Clears the current error flag.
	 *
	 * @since  4.1.0
	 */
	public function clear_error() {
		$this->error = false;
	}

	/**
	 * Returns the current error details, or false if no error is set.
	 *
	 * @since  4.1.0
	 * @return false|array Either the error details or false (no error).
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Clears the current log.
	 *
	 * @since  4.3.0
	 */
	public function clear_log() {
		$this->log = false;
	}

	/**
	 * Returns the current log details, or false if no log is set.
	 *
	 * @since  4.3.0
	 * @return false|array Either the log details or false (no error).
	 */
	public function get_log() {
		return $this->log;
	}

	/**
	 * Clears the last updated version.
	 *
	 * @since  4.3.0
	 */
	public function clear_version() {
		$this->new_version = false;
	}

	/**
	 * Returns the current log details, or false if no log is set.
	 *
	 * @since  4.3.0
	 * @return false|array Either the log details or false (no error).
	 */
	public function get_version() {
		return $this->new_version;
	}

	/**
	 * Delete Plugin, used internally
	 *
	 * @since  4.7
	 *
	 * @param  int|string $pid                 The project ID or plugin filename.
	 * @param  bool       $skip_uninstall_hook to avoid data deleted on uninstall
	 *
	 * @return bool True on success.
	 */
	public function delete_plugin( $pid, $skip_uninstall_hook = false ) {
		$this->clear_error();
		$this->clear_log();

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		include_once ABSPATH . 'wp-admin/includes/file.php';

		// Is a WPMU DEV project?
		$is_dev = is_numeric( $pid );

		if ( $is_dev ) {
			$pid = (int) $pid;

			if ( ! $this->is_project_installed( $pid ) ) {
				$this->set_error( $pid, 'DEL.01', __( 'Plugin not installed', 'wpmudev' ) );

				return false;
			}
			$local    = WPMUDEV_Dashboard::$site->get_cached_projects( $pid );
			$filename = $local['filename'];
		} else {
			$filename = $pid;
		}

		$filename = plugin_basename( sanitize_text_field( $filename ) );

		//Check that it's a valid plugin
		$valid = validate_plugin( $filename );
		if ( is_wp_error( $valid ) ) {
			$this->set_error( $pid, 'DEL.09', $valid->get_error_message() );

			return false;
		}

		// recheck
		$active_blog    = is_plugin_active( $filename );
		$active_network = is_multisite() && is_plugin_active_for_network( $filename );

		if ( $active_blog || $active_network ) {
			$this->set_error( $pid, 'DEL.02', __( 'You cannot delete a plugin while it is active.', 'wpmudev' ) );

			return false;
		}

		// Check filesystem credentials. `delete_plugins()` will bail otherwise.
		$url = wp_nonce_url( 'plugins.php?action=delete-selected&verify-delete=1&checked[]=' . $filename, 'bulk-plugins' );
		ob_start();
		$credentials = request_filesystem_credentials( $url );
		ob_end_clean();
		if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
			global $wp_filesystem;

			$error_code = 'DEL.03';
			$error      = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'wpmudev' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$error_code = $wp_filesystem->errors->get_error_code();
				$error      = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			$this->set_error( $pid, $error_code, $error );

			return false;
		}

		// skip uninstall hook if asked to
		if ( $skip_uninstall_hook ) {
			// uninstall hook available
			if ( is_uninstallable_plugin( $filename ) ) {
				/**
				 * @see is_uninstallable_plugin()
				 */
				$uninstallable_plugins = (array) get_option( 'uninstall_plugins' );
				if ( isset( $uninstallable_plugins[ $filename ] ) ) {
					unset( $uninstallable_plugins[ $filename ] );
					update_option( 'uninstall_plugins', $uninstallable_plugins );
				}

				if ( file_exists( WP_PLUGIN_DIR . '/' . dirname( $filename ) . '/uninstall.php' ) ) {
					/** @var WP_Filesystem_Base $wp_filesystem */
					global $wp_filesystem;
					if ( $wp_filesystem instanceof WP_Filesystem_Base ) {
						$wp_filesystem->delete( WP_PLUGIN_DIR . '/' . dirname( $filename ) . '/uninstall.php', false, 'f' );
					}
				}
			}

			// one recheck
			if ( is_uninstallable_plugin( $filename ) ) {
				$this->set_error( $pid, 'DEL.07', __( 'Plugin Uninstall hook could not be removed.', 'wpmudev' ) );
			}
		}

		/*
		 * Set before the update:
		 * WP will refresh local cache via action-hook before the install()
		 * method is finished. That refresh call must scan the FS again.
		 */
		WPMUDEV_Dashboard::$site->clear_local_file_cache();
		$result = delete_plugins( array( $filename ) );

		if ( true === $result ) {
			wp_clean_plugins_cache( false );
			WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();

			return true;
		} elseif ( is_wp_error( $result ) ) {
			if ( 'could_not_remove_plugin' === $skin->$result->get_error_code() ) {
				$this->set_error( $pid, 'DEL.10', $skin->get_error_messages() );
			} else {
				$this->set_error( $pid, $result->get_error_code(), $result->get_error_message() );
			}

			return false;
		} else {
			$this->set_error( $pid, 'DEL.05', __( 'Plugin could not be deleted.', 'wpmudev' ) );

			return false;
		}
	}
}
