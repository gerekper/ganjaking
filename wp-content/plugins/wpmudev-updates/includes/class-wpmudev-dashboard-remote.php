<?php
/**
 * Remote module.
 * Manages all remote access from Hub to the local WordPress site;
 *
 * @since   4.3.0
 * @package WPMUDEV_Dashboard
 */

/**
 * The remote-module class.
 */
class WPMUDEV_Dashboard_Remote {

	/**
	 * Stores request timing information for debug logging
	 *
	 * @var int
	 */
	protected $timer = 0;

	/**
	 * Stores current action being processed
	 *
	 * @var string
	 */
	protected $current_action = '';

	/**
	 * Stores registered remote access actions and their callbacks.
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Set up the Remote module. Here we load and initialize the settings.
	 *
	 * @internal
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'run_request' ) );
	}

	/**
	 * Return success results for API to the hub
	 *
	 * @param mixed $data        Data to encode as JSON, then print and die.
	 * @param int   $status_code The HTTP status code to output, defaults to 200.
	 */
	public function send_json_success( $data = null, $status_code = null ) {
		//log it if turned on
		if ( WPMUDEV_API_DEBUG ) {
			$req_time   = round( ( microtime( true ) - $this->timer ), 4 ) . "s";
			$req_status = is_null( $status_code ) ? 200 : $status_code;
			$log        = '[Hub API call response] %s %s %s %s';
			$log        .= "\n   Response: (success) %s\n";
			$msg        = sprintf(
				$log,
				$_GET['wpmudev-hub'],
				$this->current_action,
				$req_status,
				$req_time,
				json_encode( $data, JSON_PRETTY_PRINT )
			);
			error_log( $msg );
		}

		wp_send_json_success( $data, $status_code );
	}

	/**
	 * Return error results for API to the hub
	 *
	 * @param mixed $data                   Data to encode as JSON, then print and die. Expected to be an array error
	 *                                      or array of error arrays array(  'code'    => 'error_code',
	 *                                      'message' => 'Error message.',
	 *                                      'data'    => mixed
	 *                                      )
	 * @param int   $status_code            The HTTP status code to output, defaults to 200.
	 */
	public function send_json_error( $data = null, $status_code = null ) {
		//log it if turned on
		if ( WPMUDEV_API_DEBUG ) {
			$req_time   = round( ( microtime( true ) - $this->timer ), 4 ) . "s";
			$req_status = is_null( $status_code ) ? 200 : $status_code;
			$log        = '[Hub API call response] %s %s %s %s';
			$log        .= "\n   Response: (error) %s\n";
			$msg        = sprintf(
				$log,
				$_GET['wpmudev-hub'],
				$this->current_action,
				$req_status,
				$req_time,
				json_encode( $data, JSON_PRETTY_PRINT )
			);
			error_log( $msg );
		}

		wp_send_json_error( $data, $status_code );
	}

	/**
	 * Check signature hash of
	 *
	 * @since  4.0.0
	 *
	 * @param  string $req_id         The request id as passed by Hub
	 * @param  string $json           The full json body that hash was created on
	 * @param  bool   $die_on_failure If set to false the function returns a bool.
	 *
	 * @return bool    True on success.
	 */
	public function validate_hash( $req_id, $json, $die_on_failure = true ) {
		if ( defined( 'WPMUDEV_IS_REMOTE' ) && ! WPMUDEV_IS_REMOTE ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array(
						'code'    => 'remote_disabled',
						'message' => 'Remote calls are disabled in wp-config.php'
					)
				);
			} else {
				return false;
			}
		}

		if ( empty( $_SERVER['HTTP_WDP_AUTH'] ) ) {
			if ( $die_on_failure ) {
				wp_send_json_error(
					array( 'code' => 'missing_auth_header', 'message' => 'Missing authentication header' )
				);
			} else {
				return false;
			}
		}

		$hash    = $_SERVER['HTTP_WDP_AUTH'];
		$api_key = WPMUDEV_Dashboard::$api->get_key();

		$hash_string = $req_id . $json;

		$valid = hash_hmac( 'sha256', $hash_string, $api_key );

		$is_valid = hash_equals( $valid, $hash ); //Timing attack safe string comparison, PHP <5.6 compat added in WP 3.9.2
		if ( ! $is_valid && $die_on_failure ) {
			wp_send_json_error(
				array( 'code' => 'incorrect_auth', 'message' => 'Incorrect authentication' )
			);
		}

		//check nonce to prevent replay attacks
		list( $req_id, $timestamp ) = explode( '-', $req_id );
		$nonce = WPMUDEV_Dashboard::$site->get_option( 'hub_nonce' );
		if ( floatval( $timestamp ) > $nonce ) {
			WPMUDEV_Dashboard::$site->set_option( 'hub_nonce', floatval( $timestamp ) );
		} else {
			wp_send_json_error(
				array( 'code' => 'nonce_failed', 'message' => 'Nonce check failed' )
			);
		}


		if ( ! defined( 'WPMUDEV_IS_REMOTE' ) ) {
			define( 'WPMUDEV_IS_REMOTE', $is_valid );
		}

		return $is_valid;
	}

	/**
	 * Registers a Hub api action and callback for it
	 *
	 * @param          $action
	 * @param callable $callback The name of the function you wish to be called.
	 */
	public function register_action( $action, $callback ) {
		$this->actions[ $action ] = $callback;
	}

	/**
	 * Entry point for all Hub cloud requests to the plugin
	 *
	 * @internal
	 */
	public function run_request() {
		// Do nothing if we don't
		if ( empty( $_GET['wpmudev-hub'] ) ) {
			return;
		}

		$this->register_internal_actions();
		$this->register_plugin_actions();

		//get the json
		$raw_json = file_get_contents( 'php://input' );

		$this->validate_hash( $_GET['wpmudev-hub'], $raw_json );

		$body = json_decode( stripslashes( $raw_json ) );
		if ( ! isset( $body->action ) ) {
			wp_send_json_error( array( 'code' => 'invalid_params', 'message' => 'The "action" parameter is missing' ) );
		}
		if ( ! isset( $body->params ) ) {
			wp_send_json_error( array( 'code' => 'invalid_params', 'message' => 'The "params" object is missing' ) );
		}

		if ( isset( $this->actions[ $body->action ] ) ) {
			$this->current_action = $body->action;

			//log it if turned on
			if ( WPMUDEV_API_DEBUG ) {
				$this->timer = microtime( true ); //start the timer
				$log         = '[Hub API call] %s %s';
				$log         .= "\n   Request params: %s\n";

				$msg = sprintf(
					$log,
					$_GET['wpmudev-hub'],
					$body->action,
					json_encode( $body->params, JSON_PRETTY_PRINT )
				);
				error_log( $msg );
			}

			call_user_func( $this->actions[ $body->action ], $body->params, $body->action, $this );

			$this->send_json_success(); //send success in case the callback didn't respond
		}

		// When the callback function did not send a response assume error.
		wp_send_json_error( array(
			'code'    => 'unregistered_action', 'message' => 'This action is not registered. The required plugin is not installed, updated, or configured properly.'
		) );
	}

	/**
	 * Register actions that are used by the Dashboard plugin
	 */
	protected function register_internal_actions() {
		$this->register_action( 'registered_actions', array( $this, 'action_registered' ) );
		$this->register_action( 'sync', array( $this, 'action_sync' ) );
		$this->register_action( 'status', array( $this, 'action_status' ) );
		$this->register_action( 'logout', array( $this, 'action_logout' ) );
		$this->register_action( 'activate', array( $this, 'action_activate' ) );
		$this->register_action( 'deactivate', array( $this, 'action_deactivate' ) );
		$this->register_action( 'install', array( $this, 'action_install' ) );
		$this->register_action( 'upgrade', array( $this, 'action_upgrade' ) );
		$this->register_action( 'delete', array( $this, 'action_delete' ) );
		$this->register_action( 'core_upgrade', array( $this, 'action_core_upgrade' ) );
		$this->register_action( 'analytics', array( $this, 'action_analytics' ) );
	}

	/**
	 * Registers custom Hub actions from other DEV plugins
	 *
	 * Other plugins should use the wdp_register_hub_action filter to add an item to
	 *  the associative array as 'action_name' => 'callback'
	 */
	protected function register_plugin_actions() {
		/**
		 * Registers a Hub api action and callback for it
		 *
		 * @param          $action
		 * @param callable $callback The name of the function you wish to be called.
		 */
		$actions = apply_filters( 'wdp_register_hub_action', array() );
		foreach ( $actions as $action => $callback ) {
			//check action is not already registered and valid
			if ( ! isset( $this->actions[ $action ] ) && is_callable( $callback ) ) {
				$this->register_action( $action, $callback );
			}
		}
	}

	/*
	 * *********************************************************************** *
	 * *     INTERNAL ACTION METHODS
	 * *********************************************************************** *
	 */

	/**
	 * Get a list of registered Hub actions that can be called
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_registered( $params, $action ) {
		$actions = $this->actions;

		//make class names human readable
		foreach ( $actions as $action => $callback ) {
			if ( is_array( $callback ) ) {
				$actions[ $action ] = array( get_class( $callback[0] ), $callback[1] );
			} else if ( is_object( $callback ) ) {
				$actions[ $action ] = 'Closure';
			} else {
				$actions[ $action ] = trim( $callback ); //cleans up lambda function names
			}
		}

		$this->send_json_success( $actions );
	}

	/**
	 * Force a ping of the latest site status (plugins, themes, etc)
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_sync( $params, $action ) {
		// Simply refresh the membership details.
		WPMUDEV_Dashboard::$api->hub_sync();
		$this->send_json_success();
	}

	/**
	 * Get the latest site status (plugins, themes, etc)
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_status( $params, $action ) {
		$this->send_json_success( WPMUDEV_Dashboard::$api->build_api_data( false ) );
	}

	/**
	 * Logout of this site, removing it from the Hub
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_logout( $params, $action ) {
		WPMUDEV_Dashboard::$site->logout( false );
		$this->send_json_success();
	}

	/**
	 * Activates a list of plugins and themes by pid or slug. Handles multiple, but should normally
	 * be called with only one package at a time.
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_activate( $params, $action, $object ) {

		define( 'WPMUDEV_REMOTE_SKIP_SYNC', true ); //skip sync, hub remote calls are recorded locally

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$activated = $errors = array(); //init

		//do plugins
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				if ( is_numeric( $plugin ) ) {
					$local    = WPMUDEV_Dashboard::$site->get_cached_projects( $plugin );
					$filename = $local['filename'];
				} else {
					$filename = $plugin;
				}

				//this checks if it's valid already
				$result = activate_plugin( $filename, '', is_multisite() );
				if ( is_wp_error( $result ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $result->get_error_code(),
						'message' => $result->get_error_message()
					);
				} else {
					WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
					$activated[] = array( 'file' => $plugin );
				}
			}
		}

		//do themes
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( is_numeric( $theme ) ) {
					$local = WPMUDEV_Dashboard::$site->get_cached_projects( $theme );
					$slug  = $local['slug'];
				} else {
					$slug = $theme;
				}

				//wp_get_theme does not return an error for empty slugs
				if ( empty( $slug ) ) {
					$slug = "wpmudev_theme_$theme";
				}

				//check that this is a valid theme
				$check_theme = wp_get_theme( $slug );
				if ( ! $check_theme->exists() ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $check_theme->errors()->get_error_code(),
						'message' => $check_theme->errors()->get_error_message()
					);
					continue;
				}

				if ( is_multisite() ) {
					// Allow theme network wide.
					$allowed_themes          = get_site_option( 'allowedthemes' );
					$allowed_themes[ $slug ] = true;
					update_site_option( 'allowedthemes', $allowed_themes );
				} else {
					switch_theme( $slug );
				}
				WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
				$activated[] = array( 'file' => $theme );
			}
		}

		if ( count( $activated ) ) {
			$this->send_json_success( compact( 'activated', 'errors' ) );
		} else {
			$this->send_json_error( compact( 'activated', 'errors' ) );
		}
	}

	/**
	 * Deactivates a list of plugins and themes by pid or slug. Handles multiple, but should normally
	 * be called with only one package at a time.
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_deactivate( $params, $action ) {

		define( 'WPMUDEV_REMOTE_SKIP_SYNC', true ); //skip sync, hub remote calls are recorded locally

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$deactivated = $errors = array(); //init

		//do plugins
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				if ( is_numeric( $plugin ) ) {
					$local    = WPMUDEV_Dashboard::$site->get_cached_projects( $plugin );
					$filename = $local['filename'];
				} else {
					$filename = $plugin;
				}

				//Check that it's a valid plugin
				$valid = validate_plugin( $filename );
				if ( is_wp_error( $valid ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $valid->get_error_code(),
						'message' => $valid->get_error_message()
					);
					continue;
				}

				deactivate_plugins( $filename, false, is_multisite() );
				//there is no return so we always call it a success
				WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
				$deactivated[] = array( 'file' => $plugin );
			}
		}

		//do themes
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( is_numeric( $theme ) ) {
					$local = WPMUDEV_Dashboard::$site->get_cached_projects( $theme );
					$slug  = $local['slug'];
				} else {
					$slug = $theme;
				}

				//wp_get_theme does not return an error for empty slugs
				if ( empty( $slug ) ) {
					$slug = "wpmudev_theme_$theme";
				}

				//check that this is a valid theme
				$check_theme = wp_get_theme( $slug );
				if ( ! $check_theme->exists() ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $check_theme->errors()->get_error_code(),
						'message' => $check_theme->errors()->get_error_message()
					);
					continue;
				}

				if ( is_multisite() ) {
					// Disallow theme network wide.
					$allowed_themes = get_site_option( 'allowedthemes' );
					unset( $allowed_themes[ $slug ] );
					update_site_option( 'allowedthemes', $allowed_themes );

					WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
					$deactivated[] = array( 'file' => $theme );
				}
			}
		}

		if ( count( $deactivated ) ) {
			$this->send_json_success( compact( 'deactivated', 'errors' ) );
		} else {
			$this->send_json_error( compact( 'deactivated', 'errors' ) );
		}
	}

	/**
	 * Installs a list of plugins and themes by pid or slug. Handles multiple, but should normally
	 * be called with only one package at a time.
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_install( $params, $action ) {

		$only_wpmudev = true;
		$installed    = $errors = array(); //init

		//do plugins
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				if ( is_numeric( $plugin ) ) {
					$pid = $plugin;
				} else {
					$pid          = "plugin:{$plugin}";
					$only_wpmudev = false;
				}
				$success = WPMUDEV_Dashboard::$upgrader->install( $pid );
				if ( $success ) {
					$installed[] = array( 'file' => $plugin, 'log' => WPMUDEV_Dashboard::$upgrader->get_log() );
				} else {
					$error    = WPMUDEV_Dashboard::$upgrader->get_error();
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $error['code'],
						'message' => $error['message'],
						'log'     => WPMUDEV_Dashboard::$upgrader->get_log()
					);
				}
			}
		}

		//do themes
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( is_numeric( $theme ) ) {
					$pid = $theme;
				} else {
					$pid          = "theme:{$theme}";
					$only_wpmudev = false;
				}
				$success = WPMUDEV_Dashboard::$upgrader->install( $pid );
				if ( $success ) {
					$installed[] = array( 'file' => $theme, 'log' => WPMUDEV_Dashboard::$upgrader->get_log() );
				} else {
					$error    = WPMUDEV_Dashboard::$upgrader->get_error();
					$errors[] = array(
						'file'    => $theme,
						'code'    => $error['code'],
						'message' => $error['message'],
						'log'     => WPMUDEV_Dashboard::$upgrader->get_log()
					);
				}
			}
		}

		if ( $only_wpmudev ) { //if there is a non-dev product we need to sync still as those can't be recorded locally
			define( 'WPMUDEV_REMOTE_SKIP_SYNC', true ); //skip sync, hub remote calls are recorded locally
		}

		if ( count( $installed ) ) {
			$this->send_json_success( compact( 'installed', 'errors' ) );
		} else {
			$this->send_json_error( compact( 'installed', 'errors' ) );
		}
	}

	/**
	 * Upgrades a list of plugins and themes by pid or slug. Handles multiple, but should normally
	 * be called with only one package at a time.
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_upgrade( $params, $action ) {

		define( 'WPMUDEV_REMOTE_SKIP_SYNC', true ); //skip sync, hub remote calls are recorded locally

		$upgraded = $errors = array(); //init

		//do plugins
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				$pid     = is_numeric( $plugin ) ? $plugin : "plugin:{$plugin}";
				$success = WPMUDEV_Dashboard::$upgrader->upgrade( $pid );
				if ( $success ) {
					$upgraded[] = array(
						'file'        => $plugin,
						'log'         => WPMUDEV_Dashboard::$upgrader->get_log(),
						'new_version' => WPMUDEV_Dashboard::$upgrader->get_version()
					);
				} else {
					$error    = WPMUDEV_Dashboard::$upgrader->get_error();
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $error['code'],
						'message' => $error['message'],
						'log'     => WPMUDEV_Dashboard::$upgrader->get_log()
					);
				}
			}
		}

		//do themes
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				$pid     = is_numeric( $theme ) ? $theme : "theme:{$theme}";
				$success = WPMUDEV_Dashboard::$upgrader->upgrade( $pid );
				if ( $success ) {
					$upgraded[] = array(
						'file'        => $theme,
						'log'         => WPMUDEV_Dashboard::$upgrader->get_log(),
						'new_version' => WPMUDEV_Dashboard::$upgrader->get_version()
					);
				} else {
					$error    = WPMUDEV_Dashboard::$upgrader->get_error();
					$errors[] = array(
						'file'    => $theme,
						'code'    => $error['code'],
						'message' => $error['message'],
						'log'     => WPMUDEV_Dashboard::$upgrader->get_log()
					);
				}
			}
		}

		if ( count( $upgraded ) ) {
			$this->send_json_success( compact( 'upgraded', 'errors' ) );
		} else {
			$this->send_json_error( compact( 'upgraded', 'errors' ) );
		}
	}

	/**
	 * Deletes a list of plugins and themes by pid or slug. Handles multiple, but should normally
	 * be called with only one package at a time. Logic copied from ajax-actions.php
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 */
	public function action_delete( $params, $action ) {

		define( 'WPMUDEV_REMOTE_SKIP_SYNC', true ); //skip sync, hub remote calls are recorded locally

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );
		include_once( ABSPATH . 'wp-admin/includes/file.php' );

		$deleted = $errors = array(); //init

		//do plugins
		if ( isset( $params->plugins ) && is_array( $params->plugins ) ) {
			foreach ( $params->plugins as $plugin ) {
				if ( is_numeric( $plugin ) ) {
					$local    = WPMUDEV_Dashboard::$site->get_cached_projects( $plugin );
					$filename = $local['filename'];
				} else {
					$filename = $plugin;
				}

				$filename = plugin_basename( sanitize_text_field( $filename ) );

				//Check that it's a valid plugin
				$valid = validate_plugin( $filename );
				if ( is_wp_error( $valid ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $valid->get_error_code(),
						'message' => $valid->get_error_message()
					);
					continue;
				}

				if ( is_plugin_active( $filename ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => 'main_site_active',
						'message' => __( 'You cannot delete a plugin while it is active on the main site.' )
					);
					continue;
				}

				// Check filesystem credentials. `delete_plugins()` will bail otherwise.
				$url = wp_nonce_url( 'plugins.php?action=delete-selected&verify-delete=1&checked[]=' . $filename, 'bulk-plugins' );
				ob_start();
				$credentials = request_filesystem_credentials( $url );
				ob_end_clean();
				if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
					global $wp_filesystem;

					$error_code = 'fs_unavailable';
					$error      = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

					// Pass through the error from WP_Filesystem if one was raised.
					if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
						$error_code = $wp_filesystem->errors->get_error_code();
						$error      = esc_html( $wp_filesystem->errors->get_error_message() );
					}

					$errors[] = array(
						'file'    => $plugin,
						'code'    => $error_code,
						'message' => $error
					);
					continue;
				}

				$result = delete_plugins( array( $filename ) );

				if ( true === $result ) {
					wp_clean_plugins_cache( false );
					WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
					$deleted[] = array( 'file' => $plugin );
				} elseif ( is_wp_error( $result ) ) {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => $result->get_error_code(),
						'message' => $result->get_error_message()
					);
					continue;
				} else {
					$errors[] = array(
						'file'    => $plugin,
						'code'    => 'unknown_error',
						'message' => __( 'Plugin could not be deleted.' )
					);
					continue;
				}
			}
		}

		//do themes
		if ( isset( $params->themes ) && is_array( $params->themes ) ) {
			foreach ( $params->themes as $theme ) {
				if ( is_numeric( $theme ) ) {
					$local = WPMUDEV_Dashboard::$site->get_cached_projects( $theme );
					$slug  = $local['slug'];
				} else {
					$slug = $theme;
				}

				//wp_get_theme does not return an error for empty slugs
				if ( empty( $slug ) ) {
					$slug = "wpmudev_theme_$theme";
				}

				//check that this is a valid theme
				$check_theme = wp_get_theme( $slug );
				if ( ! $check_theme->exists() ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $check_theme->errors()->get_error_code(),
						'message' => $check_theme->errors()->get_error_message()
					);
					continue;
				}

				// Check filesystem credentials. `delete_theme()` will bail otherwise.
				$url = wp_nonce_url( 'themes.php?action=delete&stylesheet=' . urlencode( $slug ), 'delete-theme_' . $slug );
				ob_start();
				$credentials = request_filesystem_credentials( $url );
				ob_end_clean();
				if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
					global $wp_filesystem;

					$error_code = 'fs_unavailable';
					$error      = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

					// Pass through the error from WP_Filesystem if one was raised.
					if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
						$error_code = $wp_filesystem->errors->get_error_code();
						$error      = esc_html( $wp_filesystem->errors->get_error_message() );
					}

					$errors[] = array(
						'file'    => $theme,
						'code'    => $error_code,
						'message' => $error
					);
					continue;
				}

				$result = delete_theme( $slug );

				if ( is_wp_error( $result ) ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => $result->get_error_code(),
						'message' => $result->get_error_message()
					);
					continue;
				} elseif ( false === $result ) {
					$errors[] = array(
						'file'    => $theme,
						'code'    => 'unknown_error',
						'message' => __( 'Theme could not be deleted.' )
					);
					continue;
				}

				WPMUDEV_Dashboard::$site->schedule_shutdown_refresh();
				$deleted[] = array( 'file' => $theme );
			}
		}

		if ( count( $deleted ) ) {
			$this->send_json_success( compact( 'deleted', 'errors' ) );
		} else {
			$this->send_json_error( compact( 'deleted', 'errors' ) );
		}
	}

	/**
	 * Upgrades to the latest WP core version, major or minor
	 *
	 * @param object $params Parameters passed in json body
	 * @param string $action The action name that was called
	 *
	 * @since 4.4
	 */
	public function action_core_upgrade( $params, $action ) {

		$success = WPMUDEV_Dashboard::$upgrader->upgrade_core();
		if ( $success ) {
			$this->send_json_success( array(
				'log'         => WPMUDEV_Dashboard::$upgrader->get_log(),
				'new_version' => WPMUDEV_Dashboard::$upgrader->get_version()
			) );
		} else {
			$error    = WPMUDEV_Dashboard::$upgrader->get_error();
			$this->send_json_error( array(
				'code'    => $error['code'],
				'message' => $error['message'],
				'data'    => array( 'log' => WPMUDEV_Dashboard::$upgrader->get_log() )
			) );
		}
	}

	/**
	 * Enable/Disable Analytics.
	 *
	 * @since 4.6.1
	 *
	 * @param object $params list of args
	 * @param string $action name of action
	 */
	public function action_analytics( $params, $action ) {

		if ( ! isset( $params->status ) ) {
			$this->send_json_error( array(
				'code'    => 'invalid_params',
				'message' => __( 'The "status" param is missing.', 'wpmudev' )
			) );
		}

		switch ( $params->status ) {
			case 'enabled':
				/** @var WP_Error|object $result Enable analytics */
				$result = WPMUDEV_Dashboard::$api->analytics_enable();
				break;
			case 'disabled':
				/** @var WP_Error|object $result Disable Analytics */
				$result = WPMUDEV_Dashboard::$api->analytics_disable();
				break;
			default:
				// send error.
				$this->send_json_error( array(
					'code'    => 'invalid_params',
					'message' => __( 'Passed invalid value for param "status", it must be either "enabled" or "disabled"', 'wpmudev' )
				) );
		}

		if ( isset( $result ) && is_wp_error( $result ) ) {
			$this->send_json_error( array(
				'code'    => $result->get_error_code(),
				'message' => $result->get_error_message()
			) );
		}

		// set analytics status.
		WPMUDEV_Dashboard::$site->set_option( 'analytics_enabled', ( 'enabled' === $params->status ) );

		// success
		$this->send_json_success();
	}
}