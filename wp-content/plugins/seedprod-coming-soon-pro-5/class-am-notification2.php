<?php

if ( ! class_exists( 'AMNotification2', false ) ) {
	/**
	 * Awesome Motive Notifications
	 *
	 * This creates a custom post type (if it doesn't exist) and calls the API to
	 * retrieve notifications for this product.
	 *
	 * @package    AwesomeMotive
	 * @author     AwesomeMotive Team
	 * @license    GPL-2.0+
	 * @copyright  Copyright (c) 2018, Awesome Motive LLC
	 * @version    2.0.0
	 */
	class AMNotification2 {

		/**
		 * The api url we are calling.
		 *
		 * @since 2.0.0
		 *
		 * @var string
		 */
		public $apiUrl = 'https://api.awesomemotive.com/v2/notification/';

		/**
		 * The notifications url we are calling.
		 *
		 * @since 2.0.0
		 *
		 * @var string
		 */
		public $notificationsUrl = 'https://awesomemotive.com';

		/**
		 * Any additional settings set on construction.
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		public $settings = array();

		/**
		 * All the notifications that will be shown to the user.
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		public static $notifications = array();

		/**
		 * An array of data to output at the top of the list to show who is sending the notifications.
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		public static $notification_list = array();

		/**
		 * Flag if the styles have been registered.
		 *
		 * @since 2.0.0
		 *
		 * @var bool
		 */
		public static $registered_styles = false;

		/**
		 * Flag if a notice has been registered.
		 *
		 * @since 2.0.0
		 *
		 * @var bool
		 */
		public static $registered = false;

		/**
		 * Flag if the html has been registered.
		 *
		 * @since 2.0.0
		 *
		 * @var bool
		 */
		public static $registered_html = false;

		/**
		 * Construct.
		 *
		 * @since 2.0.0
		 *
		 * @param string $plugin The plugin slug.
		 * @param mixed $version The version of the plugin.
		 */
		public function __construct( array $settings ) {
			$this->settings = $this->validateSettings( $settings );

			add_action( 'init',                       array( $this, 'customPostType' ) );
			add_action( 'admin_init',                 array( $this, 'getRemoteNotifications' ), 100 );
			add_action( 'wp_ajax_amn2_dismiss',       array( $this, 'dismissNotification' ) );
			add_action( 'admin_enqueue_scripts',      array( $this, 'adminEnqueueScripts' ) );
			add_action( 'admin_head',                 array( $this, 'notificationsPanelStyles' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'adminBarNotifications' ) );
			add_action( 'admin_footer',               array( $this, 'notificationsPanelHtml' ) );
		}

		/**
		 * Validate settings passed in on construction.
		 *
		 * @since 2.0.0
		 *
		 * @param  array $settings The settings array to validate.
		 * @return void
		 */
		protected function validateSettings( $settings ) {
			// These are the initial required keys.
			// If any are missing, we will throw an error so the developers can fix.
			$keys = array(
				'name',
				'slug',
				'version',
				'logo'
			);

			$missing = array_diff( $keys, array_keys( $settings ) );

			if ( ! empty( $missing ) ) {
				throw new Exception( 'Missing required keys: ' . json_encode( array_values( $missing ) ) );
			}

			return $settings;
		}

		/**
		 * Registers a custom post type.
		 *
		 * @since 2.0.0
		 */
		public function customPostType() {
			if ( post_type_exists( '_amn2' ) ) {
				return;
			}

			register_post_type( '_amn2', array(
				'label'           => 'Awesome Motive Notifications',
				'can_export'      => false,
				'supports'        => false,
				'capability_type' => 'manage_options',
			) );
		}

		/**
		 * Retrieve the remote notifications if the time has expired.
		 *
		 * @since 2.0.0
		 */
		public function getRemoteNotifications() {
			if ( ! apply_filters( 'amn2_display', is_super_admin() ) ) {
				return;
			}

			$last_checked = get_option( '_amn2_last_checked', strtotime( '-1 week' ) );

			if ( $last_checked < strtotime( 'today midnight' ) ) {
				$pluginNotifications = $this->getPluginNotifications( 1 );
				$notificationId      = null;

				if ( ! empty( $pluginNotifications ) && ! $skip_id ) {
					// Unset it from the array.
					$notification    = $pluginNotifications[0];
					$notificationId = get_post_meta( $notification->ID, 'notification_id', true );
				}

				$response = wp_remote_retrieve_body( wp_remote_post( $this->apiUrl, array(
					'body' => array(
						'slug'              => $this->settings['slug'],
						'version'           => $this->settings['version'],
						'last_notification' => $notificationId,
					),
					'sslverify' => false
				) ) );

				$data = json_decode( $response );
				if ( ! empty( $data->notifications ) ) {
					foreach ( $data->notifications as $notification ) {
						$existing = (array) get_posts(
							array(
								'post_type'   => '_amn2',
								'post_status' => 'all',
								'meta_key'    => 'notification_id',
								'meta_value'  => $notification->id,
							)
						);

						// We've already got this notification.
						if ( ! empty( $existing ) ) {
							continue;
						}

						$newNotificationId = wp_insert_post(
							array(
								'post_content' => wp_kses_post( $notification->content ),
								'post_type'    => '_amn2',
							)
						);

						update_post_meta( $newNotificationId, 'notification_id', absint( $notification->id ) );
						update_post_meta( $newNotificationId, 'title', sanitize_text_field( trim( $notification->title ) ) );
						update_post_meta( $newNotificationId, 'slugs', function_exists( 'wp_json_encode' ) ? wp_json_encode( $notification->slugs ) : json_encode( $notification->slugs ) );
						update_post_meta( $newNotificationId, 'type', sanitize_text_field( trim( $notification->type ) ) );
						update_post_meta( $newNotificationId, 'hero_image', sanitize_text_field( trim( $notification->hero_image ) ) );
						update_post_meta( $newNotificationId, 'dismissable', (bool) $notification->dismissible ? 1 : 0 );
						update_post_meta( $newNotificationId, 'location', function_exists( 'wp_json_encode' ) ? wp_json_encode( $notification->location ) : json_encode( $notification->location ) );
						update_post_meta( $newNotificationId, 'version', sanitize_text_field( trim( $notification->version ) ) );
						update_post_meta( $newNotificationId, 'viewed', 0 );
						update_post_meta( $newNotificationId, 'expiration', $notification->expiration ? absint( $notification->expiration ) : false );
						update_post_meta( $newNotificationId, 'created', $notification->created ? absint( $notification->created ) : false );
						update_post_meta( $newNotificationId, 'plans', function_exists( 'wp_json_encode' ) ? wp_json_encode( $notification->plans ) : json_encode( $notification->plans ) );
					}
				}

				// Possibly revoke notifications.
				if ( ! empty( $data->revoked ) ) {
					$this->revokeNotifications( $data->revoked );
				}

				// Set the option now so we can't run this again until after 24 hours.
				update_option( '_amn2_last_checked', strtotime( 'today midnight' ) );

				// Delete our other options so we can make sure we have the latest data.
				delete_option( '_amn2_notifications' );
				delete_option( '_amn2_notifications_refreshed' );
			}

			// Set all our notifications on the global notifications array, starting with the cache if its set.
			$this->setPluginNotifications();
		}

		/**
		 * Get local plugin notifications that have already been set.
		 *
		 * @since 2.0.0
		 *
		 * @param  integer $limit Set the limit for how many posts to retrieve.
		 * @param  array $args Any top-level arguments to add to the array.
		 *
		 * @return WP_Post[] WP_Post that match the query.
		 */
		protected function getPluginNotifications( $limit = - 1, $args = array() ) {
			$notifications = new WP_Query(
				array(
					'post_type'      => '_amn2',
					'posts_per_page' => $limit
				) + $args
			);

			return $notifications->posts;
		}

		/**
		 * Set local plugin notifications on a global array so we can access them later.
		 *
		 * @since 2.0.0
		 *
		 * @param integer $limit Set the limit for how many posts to retrieve.
		 * @param array   $args  Any top-level arguments to add to the array.
		 */
		protected function setPluginNotifications( $limit = - 1, $args = array() ) {
			$notifications = json_decode( get_option( '_amn2_notifications', null ) );
			$refreshed     = get_option( '_amn2_notifications_refreshed', strtotime( '-1 week' ) );

			if ( ! $notifications || $refreshed < strtotime( 'today midnight' ) ) {
				$notifications = $this->getPluginNotifications( - 1, array(
					'post_status' => 'all',
					'meta_key'    => 'viewed',
					'meta_value'  => '0',
					'meta_query' => array(
						array(
							'key'     => 'slugs',
							'value'   => '"' . $this->settings['slug'] . '"',
							'compare' => 'LIKE',
						),
					),
				) );

				// Set the option now so we can't run this again until after 24 hours.
				$saved = array();
				foreach ( $notifications as $notification ) {
					$saved[] = (object) array(
						'ID'           => $notification->ID,
						'post_content' => $notification->post_content
					);
				}

				update_option( '_amn2_notifications', json_encode( $saved ) );
				update_option( '_amn2_notifications_refreshed', strtotime( 'today midnight' ) );
			}

			$notifications = $this->validateNotifications( $notifications );
			if ( count( $notifications ) ) {
				foreach ( $notifications as $notification ) {
					// Attach the settings to the notification.
					$notification->settings = $this->settings;

					$notificationId = get_post_meta( $notification->ID, 'notification_id', true );
					self::$notifications[ $notificationId ] = $notification;
				}
			}

			// Add this plugin to the notification list.
			$this->setNotificationList();
		}

		/**
		 * Set a list of plugins using the notification center.
		 *
		 * @since 2.0.0
		 */
		protected function setNotificationList() {
			self::$notification_list[ $this->settings['slug'] ] = $this->settings;
		}

		/**
		 * Get the list of plugins utilizing the notification center.
		 *
		 * @since 2.0.0
		 */
		protected function getNotificationList() {
			// First, we want to sort the list in a specific order.
			$new     = array();
			$list    = self::$notification_list;
			$default = array(
				'om',
				'wpforms',
				'wpforms-lite',
				'mi',
				'mi-lite',
				'seedprod',
				'smtp'
			);

			foreach ( $default as $plugin ) {
				if ( ! empty( $list[ $plugin ] ) ) {
					$new[ $plugin ] = $list[ $plugin ];
					unset( $list[ $plugin ] );
				}
			}

			return array_values( $new + $list );
		}

		/**
		 * Validate the notifications before displaying them.
		 *
		 * @since 2.0.0
		 *
		 * @param  array $notifications An array of plugin notifications.
		 * @return array                A filtered array of plugin notifications.
		 */
		protected function validateNotifications( $notifications ) {
			global $pagenow;

			foreach ( $notifications as $key => $notification ) {
				// Location validation.
				$location = (array) json_decode( get_post_meta( $notification->ID, 'location', true ) );
				$continue = false;
				if ( ! in_array( 'everywhere', $location, true ) ) {
					if ( in_array( 'index.php', $location, true ) && 'index.php' === $pagenow ) {
						$continue = true;
					}

					if ( in_array( 'plugins.php', $location, true ) && 'plugins.php' === $pagenow ) {
						$continue = true;
					}

					if ( ! $continue ) {
						unset( $notifications[ $key ] );
					}
				}

				// Plugin validation (OR conditional).
				$plugins  = (array) json_decode( get_post_meta( $notification->ID, 'plugins', true ) );
				$continue = false;
				if ( ! empty( $plugins ) ) {
					foreach ( $plugins as $plugin ) {
						if ( is_plugin_active( $plugin ) ) {
							$continue = true;
						}
					}

					if ( ! $continue ) {
						unset( $notifications[ $key ] );
					}
				}

				// Theme validation.
				$theme    = get_post_meta( $notification->ID, 'theme', true );
				$continue = (string) wp_get_theme() === $theme;

				if ( ! empty( $theme ) && ! $continue ) {
					unset( $notifications[ $key ] );
				}

				// Version validation.
				$version  = get_post_meta( $notification->ID, 'version', true );
				$continue = false;
				if ( ! empty( $version ) ) {
					if ( version_compare( $this->settings['version'], $version, '<=' ) ) {
						$continue = true;
					}

					if ( ! $continue ) {
						unset( $notifications[ $key ] );
					}
				}

				// Expiration validation.
				$expiration = get_post_meta( $notification->ID, 'expiration', true );
				$continue   = false;
				if ( ! empty( $expiration ) ) {
					if ( $expiration > time() ) {
						$continue = true;
					}

					if ( ! $continue ) {
						unset( $notifications[ $key ] );
					}
				}

				// Plan validation.
				$plans    = (array) json_decode( get_post_meta( $notification->ID, 'plans', true ) );
				$continue = false;
				if ( ! empty( $plans ) ) {
					$level = $this->getPlanLevel();
					if ( in_array( $level, $plans, true ) ) {
						$continue = true;
					}

					if ( ! $continue ) {
						unset( $notifications[ $key ] );
					}
				}
			}

			return $notifications;
		}

		/**
		 * Grab the current plan level.
		 *
		 * @since 2.0.0
		 *
		 * @return string The current plan level.
		 */
		public function getPlanLevel() {
			// Prepare variables.
			$key   = '';
			$level = '';

			switch ( $this->settings['slug'] ) {
				case 'wpforms':
					$option = get_option( 'wpforms_license' );
					$key    = is_array( $option ) && isset( $option['key'] ) ? $option['key'] : '';
					$level  = is_array( $option ) && isset( $option['type'] ) ? $option['type'] : '';

					// Possibly check for a constant.
					if ( empty( $key ) && defined( 'WPFORMS_LICENSE_KEY' ) ) {
						$key = WPFORMS_LICENSE_KEY;
					}
					break;
				case 'mi-lite':
				case 'mi':
					if ( version_compare( MONSTERINSIGHTS_VERSION, '6.9.0', '>=' ) ) {
						if ( MonsterInsights()->license->get_site_license_type() ) {
							$key  = MonsterInsights()->license->get_site_license_key();
							$type = MonsterInsights()->license->get_site_license_type();
						} else if ( MonsterInsights()->license->get_network_license_type() ) {
							$key  = MonsterInsights()->license->get_network_license_key();
							$type = MonsterInsights()->license->get_network_license_type();
						}

						// Check key fallbacks
						if ( empty( $key ) ) {
							$key = MonsterInsights()->license->get_license_key();
						}
					} else {
						$option = get_option( 'monsterinsights_license' );
						$key    = is_array( $option ) && isset( $option['key'] ) ? $option['key'] : '';
						$level  = is_array( $option ) && isset( $option['type'] ) ? $option['type'] : '';

						// Possibly check for a constant.
						if ( empty( $key ) && defined( 'MONSTERINSIGHTS_LICENSE_KEY' ) && is_string( MONSTERINSIGHTS_LICENSE_KEY ) && strlen( MONSTERINSIGHTS_LICENSE_KEY ) > 10 ) {
							$key = MONSTERINSIGHTS_LICENSE_KEY;
						}
					}
					break;
				case 'om':
					$option = get_option( 'optin_monster_api' );
					$key    = is_array( $option ) && isset( $option['api']['apikey'] ) ? $option['api']['apikey'] : '';

					// Possibly check for a constant.
					if ( empty( $key ) && defined( 'OPTINMONSTER_REST_API_LICENSE_KEY' ) ) {
						$key = OPTINMONSTER_REST_API_LICENSE_KEY;
					}

					// If the key is still empty, check for the old legacy key.
					if ( empty( $key ) ) {
						$key = is_array( $option ) && isset( $option['api']['key'] ) ? $option['api']['key'] : '';
					}
					break;
			}

			// Possibly set the level to 'none' if the key is empty and no level has been set.
			if ( empty( $key ) && empty( $level ) ) {
				$level = 'none';
			}

			// Possibly set the level to 'unknown' if a key is entered, but no level can be determined (such as manually entered key)
			if ( ! empty( $key ) && empty( $level ) ) {
				$level = 'unknown';
			}

			// Normalize the level.
			switch ( $level ) {
				case 'bronze':
				case 'personal':
					$level = 'basic';
					break;
				case 'silver':
				case 'multi':
					$level = 'plus';
					break;
				case 'gold':
				case 'developer':
					$level = 'pro';
					break;
				case 'platinum':
				case 'master':
					$level = 'ultimate';
					break;
			}

			// Return the plan level.
			return $level;
		}

		/**
		 * Dismiss the notification via AJAX.
		 *
		 * @since 2.0.0
		 */
		public function dismissNotification() {
			if ( ! apply_filters( 'amn2_display', is_super_admin() ) ) {
				die;
			}

			if ( 'notifications' === $_POST['notification_id'] ) {
				$user = wp_get_current_user();
				update_user_meta( $user->ID, '_amn2_updates_dismissed', true );
			} else {
				$this->revokeNotifications( array( intval( $_POST['notification_id'] ) ) );
			}
			die;
		}

		/**
		 * Revokes notifications.
		 *
		 * @since 2.0.0
		 *
		 * @param array $ids An array of notification IDs to revoke.
		 */
		public function revokeNotifications( $ids ) {
			// Loop through each of the IDs and find the post that has it as meta.
			foreach ( (array) $ids as $id ) {
				$notifications = $this->getPluginNotifications( - 1,
					array(
						'post_status' => 'all',
						'meta_key'    => 'notification_id',
						'meta_value'  => $id
					)
				);
				if ( $notifications ) {
					foreach ( $notifications as $notification ) {
						update_post_meta( $notification->ID, 'viewed', 1 );
					}
				}
			}
		}

		/**
		 * Enqueue styles needed for the admin bar.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public function adminEnqueueScripts() {
			wp_enqueue_style( 'font-awesome-free-solid', '//use.fontawesome.com/releases/v5.7.0/css/solid.css' );
			wp_enqueue_style( 'font-awesome-free-all', '//use.fontawesome.com/releases/v5.7.0/css/fontawesome.css' );
		}

		/**
		 * Enqueue styles needed for the admin bar.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public function notificationsPanelStyles() {
			if ( ! apply_filters( 'amn2_display', is_super_admin() ) ) {
				return;
			}

			if ( self::$registered_styles ) {
				return;
			}

			self::$registered_styles = true;

			$count   = count( self::$notifications );
			$user    = wp_get_current_user();
			$updates = get_user_meta( $user->ID, '_amn2_updates_dismissed', true );

			// If the user has not dismissed the updates, let's add another item to the count.
			if ( ! $updates ) {
				$count++;
			}

			if ( ! $count ) {
				return;
			}

			ob_start();
			?>
			<style>
				#wpadminbar #wp-admin-bar-am-notifications > div {
					cursor: pointer;
				}
				#wpadminbar #wp-admin-bar-am-notifications > div > i:before {
					top: 3px;
				}
				#wpadminbar #wp-admin-bar-am-notifications > div > i {
					font-family:'Font Awesome 5 Free' !important;
					font-size: 18px;
					margin:0;
				}

				#wpadminbar #wp-admin-bar-am-notifications .notification-indicator {
					display: inline-block;
					animation: bounce 1.5s linear infinite;
					vertical-align: top;
					position: relative;
					top: 5px;
					margin: 1px 0 0 2px;
					padding: 0 5px 0 4px;
					min-width: 7px;
					height: 17px;
					border-radius: 11px;
					background-color: #ca4a1f;
					color: #fff;
					font-size: 9px;
					line-height: 17px;
					text-align: center;
				}

				.am-notification-center {
					position: fixed;
					top: 32px;
					right: 0;
					bottom: 0;
					min-width: 400px;
					-webkit-transition: all .15s cubic-bezier(.075,.82,.165,1);
					transition: all .15s cubic-bezier(.075,.82,.165,1);
					-webkit-box-shadow: -3px 1px 10px -2px rgba(61, 65, 69,.075);
					box-shadow: -3px 1px 10px -2px rgba(61, 65, 69,.075);
					z-index: 999999999;
				}
				.am-notification-center.open {
					right: 0;
					opacity: 1;
					pointer-events: auto;
					visibility: visible;
					color: #3e3e3e;
					background: #f6f6f6;
					cursor: default;
				}
				.am-notification-center.closed {
					visibility: hidden;
					right: -400px;
					opacity: 0;
					pointer-events: none;
				}

				.am-notification-center .header {
					height: 60px;
					color: #fff;
					background: #1074a8;
					font-size: 18px;
					line-height: 56px;
					position: relative;
					padding: 0 20px;
				}

				.am-notification-center .header .close-panel {
					cursor: pointer;
					position: absolute;
					right: 15px;
					font-family:'Font Awesome 5 Free';
					font-size: 20px;
					top: 50%;
					margin-top: -10px;
				}

				.am-notification-center .content {
					padding: 15px;
					overflow: auto;
					position: absolute;
					bottom: 0;
					top: 60px;
					right: 0;
					left: 0;
				}

				.am-notification-center .notification-card {
					border: 0;
					border-left: 4px solid #fff;
					background: #fff;
					margin-bottom: 15px;
					min-width: 100%;
					-webkit-box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					-moz-box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					opacity: 1;
					max-height: 1500px;
					transition: all 0.3s ease-out;
					overflow: hidden;
					font-size: 16px;
					line-height: 25px;
					color: #3e3e3e;
				}

				.am-notification-center .notification-card.no-notifications {
					display: none;
				}

				.am-notification-center .notification-card.remove {
					opacity: 0;
				}

				.am-notification-center .notification-card.remove-height {
					max-height: 0;
					margin: 0;
				}

				.am-notification-center .notification-card-image {
					max-height: 150px;
					overflow: hidden;
				}

				.am-notification-center .notification-card-image img {
					width: 100%;
					height: auto;
				}

				.am-notification-center .notification-card-content {
					padding: 15px 15px 15px 11px;
					display: flex;
				}

				.am-notification-center .notification-card.notice-none .notification-card-content {
					padding: 15px;
				}

				.am-notification-center .notification-card.notice-none {
					border: 0;
				}

				.am-notification-center .notification-card.notice-success {
					border-left-color: #46b450;
				}

				.am-notification-center .notification-card.notice-warning {
					border-left-color: #ffb900;
				}

				.am-notification-center .notification-card.notice-warning {
					border-left-color: #dc3232;
				}

				.am-notification-center .notification-card.notice-info {
					border-left-color: #00a0d2;
				}

				.am-notification-center .notification-plugin-list {
					display: flex;
					margin-bottom: 15px;
				}

				.am-notification-center .notification-card-content .notification-card-icon,
				.am-notification-center .notification-plugin-list-icon {
					min-width: 60px;
					width: 60px;
					height: 60px;
					display: inline-block;
					border: 1px solid #e5e5e5;
					margin-right: 15px;
					position: relative;
					box-sizing: border-box;
					padding: 5px;
				}

				.am-notification-center .notification-plugin-list-text {
					display: flex;
					flex-grow: 1;
					align-items: center;
					margin-left: 5px;
					position: relative;
				}

				.am-notification-center .notification-plugin-list-text i {
					color: #b4b4b4;
					font-size: 18px;
					position: absolute;
					right: 0;
					cursor: pointer;
				}

				.am-notification-center .notification-plugin-list-text i span {
					position: absolute;
					width: 120px;
					color: #FFFFFF;
					background: #1074a8;
					height: auto;
					line-height: 20px;
					text-align: center;
					visibility: hidden;
					border-radius: 0px;
					-webkit-box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					-moz-box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					box-shadow: 0px 0px 5px 0px rgba(196,196,196,0.45);
					font-family: sans-serif;
					font-size: 12px;
					font-weight: normal;
					opacity: 0;
					transition: opacity 0.5s;
					padding: 10px;
					margin-left: -121px;
				}
				.am-notification-center .notification-plugin-list-text i span:after {
					content: '';
					position: absolute;
					bottom: 100%;
					right: 10px;
					margin-left: -8px;
					width: 0; height: 0;
					border-bottom: 8px solid #1074a8;
					border-right: 8px solid transparent;
					border-left: 8px solid transparent;
				}
				.am-notification-center .notification-plugin-list-text i:hover span {
					visibility: visible;
					opacity: 1;
					top: 30px;
					left: 50%;
					margin-left: -121px;
					z-index: 999;
				}

				.am-notification-center .notification-plugin-list-icon {
					min-width: 40px;
					width: 40px;
					height: 40px;
					margin-right: 5px;
				}

				.am-notification-center .notification-card-content .notification-card-icon.round,
				.am-notification-center .notification-plugin-list-icon {
					border-radius: 50%;
				}

				.am-notification-center .notification-card-content .notification-card-icon img,
				.am-notification-center .notification-plugin-list-icon img {
					width: 100%;
					height: auto;
				}
				.am-notification-center .notification-card-content .notification-card-icon i {
					font-family:'Font Awesome 5 Free';
					font-size: 40px;
					position: absolute;
					top: 10px;
					left: 11.5px;
					color: #f36f2c;
				}
				.am-notification-center .notification-card-content .notification-card-text {
					flex-grow: 1;
					position: relative;
				}

				.am-notification-center .notification-card-content .notification-card-text .notification-card-message p:first-of-type {
					margin-top: 0.5em;
				}

				.am-notification-center .notification-card-content .notification-card-text .notification-card-created {
					font-size: 14px;
				}

				.am-notification-center .notification-card-content .notification-card-text .notification-card-title {
					font-size: 16px;
					font-weight: bold;
					display: block;
				}

				.am-notification-center .notification-card-content .notification-card-text .button {
					display: block;
					margin: 5px 0;
					color: #c4c4c4;
					font-size: 14px;
					padding: 5px 18px;
					line-height: 28px;
					text-decoration: none;
					border-radius: 0;
					text-shadow: none;
					box-shadow: none;
					height: auto;
					text-transform: uppercase;
				}

				.am-notification-center .notification-card-content .notification-card-text .button-primary {
					background: #1074a8;
					color: #fff;
					border: none;
				}

				.am-notification-center .notification-card-content .notification-card-text .button-secondary {
					color: #3e3e3e;
				}

				.am-notification-center .notification-card-content .notification-card-text .button-primary:hover {
					background: #008ec2;
				}

				.am-notification-center .notification-card-content .notification-card-text i.close-card {
					position: absolute;
					top: 0;
					right: 0;
					cursor: pointer;
					color: #b4b4b4;
				}

				.am-notification-center .notification-card-content .notification-card-text i.close-card:hover:before {
					color: #c00;
				}

				.am-notification-center .notification-card-actions {
					padding: 15px;
					border-top: 1px solid #e5e5e5;
					text-align: right;
				}

				.am-notification-center .notification-card-actions a {
					display: inline-block;
					color: #c4c4c4;
					font-size: 16px;
					padding: 5px 24px;
					line-height: 32px;
					text-decoration: none;
				}

				.am-notification-center .notification-card-actions a:focus {
					box-shadow: none;
				}

				.am-notification-center .notification-card-actions a:hover {
					color: #3e3e3e;
				}

				.am-notification-center .notification-card-actions a.notification-button {
					background: #1074a8;
					color: #fff;
				}

				.am-notification-center .notification-card-actions a.notification-button:hover {
					background: #008ec2;
				}


				@keyframes bounce {
					0%,20%,40%,60%,80%,100% {
						-webkit-transform: translateY(0);
						transform: translateY(0)
					}

					50% {
						-webkit-transform: translateY(-5px);
						transform: translateY(-5px)
					}
				}

				@media (max-width: 660px) {
					.am-notification-center {
						width: 100%;
						min-width: 0;
					}

					body.am-notification-center-open {
						overflow: hidden;
					}
				}

				@media screen and (max-width: 782px) {
					#wpadminbar #wp-admin-bar-am-notifications {
						display: block;
					}
					#wpadminbar #wp-admin-bar-am-notifications i:before {
						top: 0;
					}
					#wpadminbar #wp-admin-bar-am-notifications i {
						font-family:'Font Awesome 5 Free' !important;
						line-height: 48px !important;
						font-size: 28px !important;
					}
					#wpadminbar #wp-admin-bar-am-notifications .notification-indicator {
						position: absolute;
						right: 0;
					}
					.am-notification-center {
						top: 46px;
					}
				}
			</style>
			<?php
			echo ob_get_clean();
		}

		/**
		 * Add Switch back to account button to WP Admin Bar.
		 *
		 * @since 2.0.0
		 */
		public function adminBarNotifications() {
			if ( ! apply_filters( 'amn2_display', is_super_admin() ) ) {
				return;
			}

			if ( self::$registered ) {
				return;
			}

			self::$registered = true;

			$count   = count( self::$notifications );
			$user    = wp_get_current_user();
			$updates = get_user_meta( $user->ID, '_amn2_updates_dismissed', true );

			// If the user has not dismissed the updates, let's add another item to the count.
			if ( ! $updates ) {
				$count++;
			}

			if ( ! $count ) {
				return;
			}

			$countHtml = $count ? '<span class="notification-indicator">' . $count . '</span>' : '';

			global $wp_admin_bar;

			// Add the customer-lookup form to the admin bar.
			$wp_admin_bar->add_menu( [
				'parent' => 'top-secondary',
				'id'     => 'am-notifications',
				'title'  => '<i class="fas fa-bell ab-icon"></i>' . $countHtml,
			] );
		}

		/**
		 * Add HTML needed for the admin bar.
		 *
		 * @since 2.0.0
		 *
		 * @return void
		 */
		public function notificationsPanelHtml() {
			if ( ! apply_filters( 'amn2_display', is_super_admin() ) ) {
				return;
			}

			if ( self::$registered_html ) {
				return;
			}

			self::$registered_html = true;

			$count   = count( self::$notifications );
			$user    = wp_get_current_user();
			$updates = get_user_meta( $user->ID, '_amn2_updates_dismissed', true );

			// If the user has not dismissed the updates, let's add another item to the count.
			if ( ! $updates ) {
				$count++;
			}

			if ( ! $count ) {
				return;
			}

			ob_start();
			?>
			<div class="am-notification-center closed">
				<div class="header">
					New Features and Updates
					<i class="fas fa-times close-panel"></i>
				</div>
				<div class="content">
					<?php echo $this->getListHtml(); ?>
					<div class="notification-card no-notifications">
						<div class="notification-card-content bottom-border">
							<div class="notification-card-text">
								You have no notifications!
							</div>
						</div>
					</div>
					<?php if ( ! $updates ) : ?>
						<div class="notification-card" data-notification-id="notifications">
							<div class="notification-card-content bottom-border">
								<div class="notification-card-icon">
									<i class="fas fa-bell"></i>
								</div>
								<div class="notification-card-text">
									We'd like to show you notifications for the latest news and updates.
								</div>
							</div>
							<div class="notification-card-actions">
								<a href="#" class="close-card" data-notification-id="notifications">NO THANKS</a>
								<a href="#" class="notification-button">ALLOW</a>
							</div>
						</div>
					<?php endif; ?>
					<?php echo $this->getCardHtml(); ?>
				</div>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function($){
					$(document).on('click', function(event) {
						var element = $(event.target),
							link    = $('#wpadminbar #wp-admin-bar-am-notifications'),
							parents = element.parents('.am-notification-center');

						if ((parents.length && ! $(event.target).hasClass('close-panel')) || link[0] === event.target) {
							return;
						}

						var panel = $('.am-notification-center');
						if (panel.hasClass('open')) {
							panel.removeClass('open').addClass('closed');
							$('body').removeClass('am-notification-center-open');
						}
					});

					$(document).on('click', '#wpadminbar #wp-admin-bar-am-notifications', function(event) {
						event.stopPropagation();
						var panel = $('.am-notification-center');

						if (panel.hasClass('open')) {
							panel.removeClass('open').addClass('closed');
							$('body').removeClass('am-notification-center-open');
						} else {
							panel.removeClass('closed').addClass('open');
							$('body').addClass('am-notification-center-open');
						}
					});

					$(document).on('click', '.am-notification-center .close-card', function(event) {
						event.stopPropagation();
						event.preventDefault();

						// Set up our variables.
						var close = $(event.target),
							card  = $('.notification-card[data-notification-id="' + close.data('notification-id') + '"]');

						// Hide the panel with an effect.
						if (card.length) {
							card.addClass('remove remove-height')
						}

						setTimeout(function() {
							// Once the effect has finished, let's actually hide it so our indicator can have the correct number.
							card.hide();

							updateCount();
						}, 500);

						dismissNotification(close.data('notification-id'));
					});

					$(document).on('click', '.notification-card-actions .notification-button', function(event) {
						event.preventDefault();
						var card = $(event.target).parents('.notification-card');

						var windowName    = 'AwesomeMotiveNotifications',
							windowOptions = 'location=1,status=0,width=800,height=700,left=50,top=50',
							oauthWindow = window.open('<?php echo $this->notificationsUrl; ?>/notifications', windowName, windowOptions);

						window.addEventListener('message', function(event) {
							if (~event.origin.indexOf('<?php echo $this->notificationsUrl; ?>')) {
								oauthWindow.close();
								if ('undefined' !== typeof event.data.currentPermission) {
									if ('granted' === event.data.currentPermission) {
										$('.notification-card-text', card).text('Thanks! Latest news and updates are now enabled for these plugins.');
									} else {
										$('.notification-card-text', card).text('Latest news and updates have been blocked for these plugins.');
									}
								}

								$('.notification-card-actions', card).hide();
								dismissNotification('notifications');
								updateCount();
							}
						}, oauthWindow);
					});

					function dismissNotification(notification_id) {
						$.post(ajaxurl, {
							action: 'amn2_dismiss',
							notification_id: notification_id
						});
					}

					function updateCount() {
						// Reduce the number in the notification indicator.
						var count = $('.am-notification-center .notification-card:not(.no-notifications):visible').length;
						if (count) {
							$('#wpadminbar .notification-indicator').text(count);
						} else {
							$('#wpadminbar .notification-indicator').remove();

							$('.no-notifications').show();
						}
					}
				});
			</script>
			<?php
			echo ob_get_clean();
		}

		/**
		 * Gets the HTML for the notification list at the top of the panel.
		 *
		 * @since 2.0.0
		 *
		 * @return string The HTML as a string.
		 */
		protected function getListHtml() {
			$list = $this->getNotificationList();
			if ( ! empty( $list ) ) {
				ob_start();
				$count = 0;
				?>
				<div class="notification-plugin-list">
					<?php foreach ( $list as $settings ) : $count++ ?>
						<?php if ( 5 == $count ) { break; } ?>
						<div class="notification-plugin-list-icon">
							<img src="<?php echo $settings['logo']; ?>">
						</div>
					<?php endforeach; ?>

					<div class="notification-plugin-list-text">
						<?php if ( count( $list ) > 4 ) : ?>
							and <?php echo count( $list ) - 4; ?> others &hellip;
						<?php endif; ?>
						<i class="fas fa-info-circle">
							<span>
								<?php foreach ( $list as $settings ) : ?>
									<?php echo $settings['name']; ?><br>
								<?php endforeach; ?>
							</span>
						</i>
					</div>
				</div>
				<?php

				return ob_get_clean();
			}

			return '';
		}

		/**
		 * Gets the HTML for the notifications themselves.
		 *
		 * @since 2.0.0
		 *
		 * @return string The HTML as a string.
		 */
		protected function getCardHtml() {
			if ( ! empty( self::$notifications ) ) {
				ob_start();

				foreach ( self::$notifications as $notification ) {
					$notificationId = get_post_meta( $notification->ID, 'notification_id', true );
					$dismissable     = get_post_meta( $notification->ID, 'dismissable', true );
					$type            = get_post_meta( $notification->ID, 'type', true );
					$heroImage       = get_post_meta( $notification->ID, 'hero_image', true );
					$title           = get_post_meta( $notification->ID, 'title', true );
					$created         = get_post_meta( $notification->ID, 'created', true );
					$created         = ! empty( $created ) ? date( 'F j, Y', $created ) : date( 'F j, Y', strtotime( $notification->post_date ) );
					?>
					<div class="notification-card notice-<?php echo $type; ?>" data-notification-id="<?php echo absint( $notificationId ); ?>">
						<?php if ( $heroImage ) : ?>
							<div class="notification-card-image">
								<img src="<?php echo $heroImage; ?>">
							</div>
						<?php endif; ?>
						<div class="notification-card-content">
							<?php if ( ! empty( $notification->settings['logo'] ) ) : ?>
								<div class="notification-card-icon round">
									<img src="<?php echo $notification->settings['logo']; ?>">
								</div>
							<?php endif; ?>
							<div class="notification-card-text">
								<span class="notification-card-created"><?php echo $created; ?></span>
								<?php if ( $title ) : ?>
									<span class="notification-card-title"><?php echo $title; ?></span>
								<?php endif; ?>
								<?php if ( $dismissable ) : ?>
									<i class="fas fa-times-circle close-card" data-notification-id="<?php echo absint( $notificationId ); ?>"></i>
								<?php endif; ?>
								<div class="notification-card-message">
									<?php echo wp_kses_post( $notification->post_content ); ?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}

				return ob_get_clean();
			}

			return '';
		}
	}
}