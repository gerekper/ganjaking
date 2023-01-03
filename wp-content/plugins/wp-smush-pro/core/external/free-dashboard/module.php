<?php
/**
 * WPMU DEV Free Notices - notification module.
 * Used by WordPress.org hosted plugins.
 *
 * @version 2.0.0
 * @author  Incsub (Philipp Stracker, Anton Vanyukov)
 * @package wpmu_free_notice
 */

if ( ! class_exists( 'WPMU_Free_Notice' ) ) {
	/**
	 * Class WPMU_Free_Notice
	 */
	class WPMU_Free_Notice {

		/**
		 * List of all registered plugins.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $plugins = array();

		/**
		 * Module options that are stored in database.
		 * Timestamps are stored here.
		 *
		 * Note that this option is stored in site-meta for multisite installations.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		protected $stored = array();

		/**
		 * User id /API Key for Mailchimp subscriber list
		 *
		 * @since 1.2
		 *
		 * @var string
		 */
		private $mc_user_id = '53a1e972a043d1264ed082a5b';

		/**
		 * Initializes and returns the singleton instance.
		 *
		 * @since  1.0.0
		 */
		public static function instance() {
			static $instance = null;

			if ( null === $instance ) {
				$instance = new WPMU_Free_Notice();
			}

			return $instance;
		}

		/**
		 * Set up the WPMU_Free_Notice module. Private singleton constructor.
		 *
		 * @since  1.0.0
		 */
		private function __construct() {
			$this->read_stored_data();

			add_action( 'wdev_register_plugin', array( $this, 'wdev_register_plugin' ), 5, 5 );
			add_action( 'load-index.php', array( $this, 'load_index_php' ), 5 );
			add_action( 'wp_ajax_frash_act', array( $this, 'wp_ajax_frash_act' ), 5 );
			add_action( 'wp_ajax_frash_dismiss', array( $this, 'wp_ajax_frash_dismiss' ), 5 );
		}

		/**
		 * Load persistent module-data from the WP Database.
		 *
		 * @since  1.0.0
		 */
		protected function read_stored_data() {
			$data = get_site_option( 'wdev-frash', false, false );

			if ( ! is_array( $data ) ) {
				$data = array();
			}

			// A list of all plugins with timestamp of first registration.
			if ( ! isset( $data['plugins'] ) || ! is_array( $data['plugins'] ) ) {
				$data['plugins'] = array();
			}

			// A list with pending messages and earliest timestamp for display.
			if ( ! isset( $data['queue'] ) || ! is_array( $data['queue'] ) ) {
				$data['queue'] = array();
			}

			// A list with all messages that were handles already.
			if ( ! isset( $data['done'] ) || ! is_array( $data['done'] ) ) {
				$data['done'] = array();
			}

			$this->stored = $data;
		}

		/**
		 * Save persistent module-data to the WP database.
		 *
		 * @since  1.0.0
		 */
		protected function store_data() {
			update_site_option( 'wdev-frash', $this->stored );
		}

		/**
		 * Action handler for 'wdev-register-plugin'
		 * Register an active plugin.
		 *
		 * @since  1.0.0
		 * @param  string $plugin_id   WordPress plugin-ID (see: plugin_basename).
		 * @param  string $title       Plugin name for display.
		 * @param  string $url_wp      URL to the plugin on wp.org (domain not needed).
		 * @param  string $cta_email   Title of the Email CTA button.
		 * @param  string $mc_list_id  Required. Mailchimp mailing list id for the plugin.
		 */
		public function wdev_register_plugin( $plugin_id, $title, $url_wp, $cta_email = '', $mc_list_id = '' ) {
			// Ignore incorrectly registered plugins to avoid errors later.
			if ( empty( $plugin_id ) || empty( $title ) || empty( $url_wp ) ) {
				return;
			}

			if ( false === strpos( $url_wp, '://' ) ) {
				$url_wp = 'https://wordpress.org/' . trim( $url_wp, '/' );
			}

			$this->plugins[ $plugin_id ] = (object) array(
				'id'         => $plugin_id,
				'title'      => $title,
				'url_wp'     => $url_wp,
				'cta_email'  => $cta_email,
				'mc_list_id' => $mc_list_id,
			);

			/*
			 * When the plugin is registered the first time we store some infos
			 * in the persistent module-data that help us later to find out
			 * if/which message should be displayed.
			 */
			if ( empty( $this->stored['plugins'][ $plugin_id ] ) ) {
				// First register the plugin permanently.
				$this->stored['plugins'][ $plugin_id ] = time();

				$hash = md5( $plugin_id . '-email' );

				// Second schedule the messages to display.
				$this->stored['queue'][ $hash ] = array(
					'plugin'  => $plugin_id,
					'type'    => 'email',
					'show_at' => time(),  // Earliest time to display note.
				);

				$hash = md5( $plugin_id . '-rate' );

				$this->stored['queue'][ $hash ] = array(
					'plugin'  => $plugin_id,
					'type'    => 'rate',
					'show_at' => time() + WEEK_IN_SECONDS,
				);

				// Finally, save the details.
				$this->store_data();
			}
		}

		/**
		 * Ajax handler called when the user chooses the CTA button.
		 *
		 * @since  1.0.0
		 */
		public function wp_ajax_frash_act() {
			$plugin = filter_input( INPUT_POST, 'plugin_id', FILTER_SANITIZE_STRING );
			$type   = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );

			$this->mark_as_done( $plugin, $type, 'ok' );

			wp_die();
		}

		/**
		 * Ajax handler called when the user chooses the dismiss button.
		 *
		 * @since  1.0.0
		 */
		public function wp_ajax_frash_dismiss() {
			$plugin = filter_input( INPUT_POST, 'plugin_id', FILTER_SANITIZE_STRING );
			$type   = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );

			$this->mark_as_done( $plugin, $type, 'ignore' );

			wp_die();
		}

		/**
		 * Action handler for 'load-index.php'
		 * Set-up the Dashboard notification.
		 *
		 * @since  1.0.0
		 */
		public function load_index_php() {
			if ( is_super_admin() ) {
				add_action( 'all_admin_notices', array( $this, 'all_admin_notices' ), 5 );
			}
		}

		/**
		 * Action handler for 'admin_notices'
		 * Display the Dashboard notification.
		 *
		 * @since  1.0.0
		 */
		public function all_admin_notices() {
			$info = $this->choose_message();
			if ( ! $info ) {
				return;
			}

			$this->render_message( $info );
		}

		/**
		 * Check to see if there is a pending message to display and returns
		 * the message details if there is.
		 *
		 * Note that this function is only called on the main Dashboard screen
		 * and only when logged in as super-admin.
		 *
		 * @since  1.0.0
		 * @return object|false
		 *         string $type   [rate|email] Which message type?
		 *         string $plugin WordPress plugin ID?
		 */
		protected function choose_message() {
			$obj      = false;
			$chosen   = false;
			$earliest = false;

			$now = time();

			// The "current" time can be changed via $_GET to test the module.
			$custom_time = filter_input( INPUT_GET, 'time', FILTER_SANITIZE_STRING );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && ! empty( $custom_time ) ) {
				if ( ' ' === $custom_time[0] ) {
					$custom_time[0] = '+';
				}

				if ( $custom_time ) {
					$now = strtotime( $custom_time );
				}

				if ( ! $now ) {
					$now = time();
				}
			}

			$tomorrow = $now + DAY_IN_SECONDS;

			foreach ( $this->stored['queue'] as $hash => $item ) {
				$show_at   = (int) $item['show_at'];
				$is_sticky = ! empty( $item['sticky'] );

				if ( ! isset( $this->plugins[ $item['plugin'] ] ) ) {
					// Deactivated plugin before the message was displayed.
					continue;
				}
				$plugin = $this->plugins[ $item['plugin'] ];

				$can_display = true;
				if ( wp_is_mobile() ) {
					// Do not display rating message on mobile devices.
					if ( 'rate' === $item['type'] ) {
						$can_display = false;
					}
				}
				if ( 'email' === $item['type'] ) {
					// If we don't have mailchimp list id.
					if ( ! $plugin->mc_list_id || ! $plugin->cta_email ) {
						// Do not display email message with missing email params.
						$can_display = false;
					}
				}
				if ( $now < $show_at ) {
					// Do not display messages that are not due yet.
					$can_display = false;
				}

				if ( ! $can_display ) {
					continue;
				}

				if ( $is_sticky ) {
					// If sticky item is present then choose it!
					$chosen = $hash;
					break;
				} elseif ( ! $earliest || $earliest < $show_at ) {
					$earliest = $show_at;
					$chosen   = $hash;
					// Don't use `break` because a sticky item might follow...
					// Find the item with the earliest schedule.
				}
			}

			if ( $chosen ) {
				// Make the chosen item sticky.
				$this->stored['queue'][ $chosen ]['sticky'] = true;

				// Re-schedule other messages that are due today.
				foreach ( $this->stored['queue'] as $hash => $item ) {
					$show_at = (int) $item['show_at'];

					if ( empty( $item['sticky'] ) && $tomorrow > $show_at ) {
						$this->stored['queue'][ $hash ]['show_at'] = $tomorrow;
					}
				}

				// Save the changes.
				$this->store_data();

				$obj = (object) $this->stored['queue'][ $chosen ];
			}

			return $obj;
		}

		/**
		 * Moves a message from the queue to the done list.
		 *
		 * @since  1.0.0
		 * @param  string $plugin  Plugin ID.
		 * @param  string $type    Message type [rate|email].
		 * @param  string $state   Button clicked [ok|ignore].
		 */
		protected function mark_as_done( $plugin, $type, $state ) {
			$done_item = false;

			foreach ( $this->stored['queue'] as $hash => $item ) {
				unset( $this->stored['queue'][ $hash ]['sticky'] );

				if ( $item['plugin'] === $plugin && $item['type'] === $type ) {
					$done_item = $item;
					unset( $this->stored['queue'][ $hash ] );
				}
			}

			if ( $done_item ) {
				$done_item['state']      = $state;
				$done_item['hash']       = $hash;
				$done_item['handled_at'] = time();
				unset( $done_item['sticky'] );

				$this->stored['done'][] = $done_item;
				$this->store_data();
			}
		}

		/**
		 * Renders the actual Notification message.
		 *
		 * @since  1.0.0
		 *
		 * @param object $info  Plugin info.
		 */
		protected function render_message( $info ) {
			$plugin  = $this->plugins[ $info->plugin ];
			$css_url = plugin_dir_url( __FILE__ ) . 'assets/admin.css';
			$js_url  = plugin_dir_url( __FILE__ ) . 'assets/admin.js';

			wp_enqueue_style( 'wdev-frash-css', $css_url, array(), '1.3.0' );
			wp_enqueue_script( 'wpev-frash-js', $js_url, array(), '1.3.0', true );
			?>
			<div class="notice notice-info frash-notice frash-notice-<?php echo esc_attr( $info->type ); ?>">
				<input type="hidden" name="type" value="<?php echo esc_attr( $info->type ); ?>" />
				<input type="hidden" name="plugin_id" value="<?php echo esc_attr( $info->plugin ); ?>" />
				<input type="hidden" name="url_wp" value="<?php echo esc_attr( $plugin->url_wp ); ?>" />
				<div class="frash-notice-logo <?php echo esc_attr( strtolower( $plugin->title ) ); ?>"><span></span></div>
				<div class="frash-notice-message">
					<?php
					if ( 'email' === $info->type ) {
						$this->render_email_message( $plugin );
					} elseif ( 'rate' === $info->type ) {
						$this->render_rate_message( $plugin );
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Output the contents of the email message.
		 * No return value. The code is directly output.
		 *
		 * @since  1.0.0
		 *
		 * @param object $plugin  Plugin info.
		 */
		protected function render_email_message( $plugin ) {
			$admin_email = get_site_option( 'admin_email' );

			$action = "https://edublogs.us1.list-manage.com/subscribe/post-json?u={$this->mc_user_id}&id={$plugin->mc_list_id}&c=?";

			/* translators: %s - plugin name */
			$title = __( "We're happy that you've chosen to install %s!", 'wdev_frash' );
			$title = apply_filters( 'wdev_email_title_' . $plugin->id, $title );

			/* translators: %s - plugin name */
			$message = __( 'Are you interested in how to make the most of this plugin? How would you like a quick 5 day email crash course with actionable advice on building your membership site? Only the info you want, no subscription!', 'wdev_frash' );
			$message = apply_filters( 'wdev_email_message_' . $plugin->id, $message );

			$mc_list_id = $plugin->mc_list_id;
			?>
			<p class="notice-title"><?php printf( esc_html( $title ), esc_html( $plugin->title ) ); ?></p>
			<p><?php printf( esc_html( $message ), esc_html( $plugin->title ) ); ?></p>
			<div class="frash-notice-cta">
				<?php
				/**
				 * Fires before subscribe form renders.
				 *
				 * @since 1.3
				 *
				 * @param int $mc_list_id  Mailchimp list ID.
				 */
				do_action( 'frash_before_subscribe_form_render', $mc_list_id );
				?>
				<form action="<?php echo esc_attr( $action ); ?>" method="get" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
					<label for="mce-EMAIL" class="hidden"><?php esc_html_e( 'Email', 'wdev_frash' ); ?></label>
					<input type="email" name="EMAIL" class="email" id="mce-EMAIL" value="<?php echo esc_attr( $admin_email ); ?>" required="required"/>
					<button class="frash-notice-act button-primary" data-msg="<?php esc_attr_e( 'Thanks :)', 'wdev_frash' ); ?>" type="submit">
						<?php echo esc_html( $plugin->cta_email ); ?>
					</button>
					<span class="frash-notice-cta-divider">|</span>
					<a href="#" class="frash-notice-dismiss" data-msg="<?php esc_attr_e( 'Saving', 'wdev_frash' ); ?>">
						<?php esc_html_e( 'No thanks', 'wdev_frash' ); ?>
					</a>
					<?php
					/**
					 * Fires after subscribe form fields are rendered.
					 * Use this hook to add additional fields for on the sub form.
					 *
					 * Make sure that the additional field has is also present on the
					 * actual MC subscribe form.
					 *
					 * @since 1.3
					 *
					 * @param int $mc_list_id  Mailchimp list ID.
					 */
					do_action( 'frash_subscribe_form_fields', $mc_list_id );
					?>
				</form>
				<?php
				/**
				 * Fires after subscribe form is rendered
				 *
				 * @since 1.3
				 *
				 * @param int $mc_list_id  Mailchimp list ID.
				 */
				do_action( 'frash_before_subscribe_form_render', $mc_list_id );
				?>
			</div>
			<?php
		}

		/**
		 * Output the contents of the rate-this-plugin message.
		 * No return value. The code is directly output.
		 *
		 * @since  1.0.0
		 *
		 * @param object $plugin  Plugin info.
		 */
		protected function render_rate_message( $plugin ) {
			/* translators: %s - plugin name */
			$title = __( 'Enjoying %s? We’d love to hear your feedback!', 'wdev_frash' );
			$title = apply_filters( 'wdev_rating_title_' . $plugin->id, $title );

			/* translators: %s - plugin name */
			$message = __( 'You’ve been using %s for over a week now, and we’d love to hear about your experience! We’ve spent countless hours developing it for you, and your feedback is important to us. We’d really appreciate your rating.', 'wdev_frash' );
			$message = apply_filters( 'wdev_rating_message_' . $plugin->id, $message );
			?>
			<p class="notice-title"><?php printf( esc_html( $title ), esc_html( $plugin->title ) ); ?></p>
			<p><?php printf( esc_html( $message ), esc_html( $plugin->title ) ); ?></p>
			<div class="frash-notice-actions">
				<a href="#" class="frash-notice-act frash-stars" data-msg="<?php esc_attr_e( 'Thanks :)', 'wdev_frash' ); ?>">
					<span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
				</a>
				<span class="frash-notice-cta-divider">|</span>
				<a href="#" class="frash-notice-dismiss" data-msg="<?php esc_attr_e( 'Saving', 'wdev_frash' ); ?>">
					<?php esc_html_e( 'Dismiss', 'wdev_frash' ); ?>
				</a>
			</div>
			<?php
		}
	}

	// Initialize the module.
	WPMU_Free_Notice::instance();
}
