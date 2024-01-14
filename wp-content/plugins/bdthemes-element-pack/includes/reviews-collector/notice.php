<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RC_Reviews_Collector' ) ) {
	class RC_Reviews_Collector {

		public $version = '1.0.0';

		public $rc_name;
		public $rc_allow_name;
		public $rc_date_name;
		public $rc_count_name;
		public $nonce;
		public $params;
		public $review_url;

		private static $instance = null;

		/**
		 * Get Instance
		 * 
		 * @since 0.0.0
		 */
		public static function get_instance( $params ) {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self( $params );
			}

			return self::$instance;
		}

		/**
		 * Insights SDK Version
		 * param array $params
		 * @return void
		 */
		public function __construct( $params ) {
			$this->params     = $params;
			$this->review_url = isset( $params['review_url'] ) ? $params['review_url'] : false;

			add_action( 'admin_enqueue_scripts', array( $this, 'rc_enqueue_scripts' ) );
			add_action( 'wp_ajax_rc_sdk_insights', array( $this, 'rc_sdk_insights' ) );
			add_action( 'wp_ajax_rc_sdk_dismiss_notice', array( $this, 'rc_sdk_dismiss_notice' ) );

			$security_key        = md5( $params['menu_slug'] );
			$this->rc_name       = str_replace( '-', '_', sanitize_title( $params['plugin_name'] ) . $security_key );
			$this->rc_allow_name = 'rc_allow_' . $security_key;
			$this->rc_date_name  = 'rc_date_' . $security_key;
			$rc_count_name       = 'rc_attempt_count_' . $security_key;
			$rc_status_db        = get_option( $this->rc_allow_name, false );

			$this->nonce = wp_create_nonce( $this->rc_allow_name );

			/**
			 * Show Notice after 3 days
			 * Now 5 minutes
			 */
			$installed = get_option( $this->rc_date_name . '_installed', false );

			if ( ! $installed ) {
				update_option( $this->rc_date_name . '_installed', time() );
			}

			$installed = get_option( $this->rc_date_name . '_installed', false );

			if ( $installed && ( time() - $installed ) < 5 * MINUTE_IN_SECONDS ) {
				// if ( $installed && ( time() - $installed ) < 3 * DAY_IN_SECONDS ) {
				return;
			}

			/**
			 * Show Notice
			 */
			if ( ! $rc_status_db ) {
				$this->display_notice();
				return;
			}

			/**
			 * Skip & Date Not Expired
			 * Show Notice
			 */
			if ( 'skip' == $rc_status_db && true == $this->check_date() ) {
				$this->display_notice();
				return;
			}

			/**
			 * Allowed & Date not Expired
			 * No need send data to server
			 * Else Send Data to Server
			 */
			if ( ! $this->check_date() ) {
				return;
			}

			/**
			 * Count attempt every time
			 */
			$rc_attempt = get_option( $rc_count_name, 0 );

			if ( ! $rc_attempt ) {
				update_option( $rc_count_name, 1 );
			}
			update_option( $rc_count_name, $rc_attempt + 1 );

		}

		/**
		 * Notice Modal
		 *
		 * @return void
		 */
		public function display_notice() {
			if ( ! get_transient( 'dismissed_notice_' . $this->rc_name ) ) {
				add_action( 'admin_notices', array( $this, 'display_global_notice' ) );
			}
		}

		/**
		 * If date is expired immidiate action
		 *
		 * @return boolean
		 */
		public function check_date() {
			$current_date   = strtotime( gmdate( 'Y-m-d' ) );
			$rc_status_date = strtotime( get_option( $this->rc_date_name, false ) );

			if ( ! $rc_status_date ) {
				return true;
			}

			if ( $rc_status_date && $current_date >= $rc_status_date ) {
				return true;
			}
			return false;
		}

		/**
		 * Reset Options Settings
		 * @return void
		 */
		public function reset_settings() {
			delete_option( $this->rc_allow_name );
			delete_option( $this->rc_date_name );
		}

		/**
		 * Ajax callback
		 */
		public function rc_sdk_insights() {
			$sanitized_status = isset( $_POST['button_val'] ) ? sanitize_text_field( $_POST['button_val'] ) : '';
			$nonce            = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			$allow_name       = isset( $_POST['allow_name'] ) ? sanitize_text_field( $_POST['allow_name'] ) : '';
			$date_name        = isset( $_POST['date_name'] ) ? sanitize_text_field( $_POST['date_name'] ) : '';

			if ( ! wp_verify_nonce( $nonce, 'rc_sdk' ) ) {
				wp_send_json( array(
					'status'  => 'error',
					'title'   => 'Error',
					'message' => 'Nonce verification failed',
				) );
				wp_die();
			}

			if ( $sanitized_status == 'skip' ) {
				update_option( $allow_name, 'skip' );
				/**
				 * Next schedule date for attempt
				 */
				update_option( $date_name, gmdate( 'Y-m-d', strtotime( "+1 month" ) ) );
			} elseif ( $sanitized_status == 'yes' ) {
				update_option( $allow_name, 'yes' );
			}

			wp_send_json( array(
				'status'  => 'success',
				'title'   => 'Success',
				'message' => 'Success.',
				'action'  => $sanitized_status,
			) );
			wp_die();
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.0.0
		 */
		public function rc_enqueue_scripts() {
			wp_enqueue_style( 'rc-sdk', plugins_url( 'assets/rc.css', __FILE__ ), array(), '1.0.0' );
			wp_enqueue_script( 'rc-sdk', plugins_url( 'assets/rc.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		}

		/**
		 * Display Global Notice
		 *
		 * @return void
		 */
		public function display_global_notice() {
			?>
			<div class="rc-global-notice notice notice-success is-dismissible">
				<h3>
					<?php printf( $this->params['plugin_title'] ); ?>
				</h3>
				<?php printf( $this->params['plugin_msg'] ); ?>
				<input type="hidden" name="rc_name" value="<?php echo esc_html( $this->rc_name ); ?>">
				<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'rc_sdk' ) ); ?>">
				<p>
					<button data-rc_name="<?php echo esc_html( $this->rc_name ); ?>"
						data-date_name="<?php echo esc_html( $this->rc_date_name ); ?>"
						data-allow_name="<?php echo esc_html( $this->rc_allow_name ); ?>"
						data-nonce="<?php echo esc_html( wp_create_nonce( 'rc_sdk' ) ); ?>"
						data-review_url="<?php echo esc_html( $this->review_url ); ?>" name="rc_allow_status" value="yes"
						class="button button-primary rc-button-allow">
						<span class="dashicons dashicons-star-filled" style="margin-top: 3px;"></span> Give us your Review
					</button>
					<button data-rc_name="<?php echo esc_html( $this->rc_name ); ?>"
						data-date_name="<?php echo esc_html( $this->rc_date_name ); ?>"
						data-allow_name="<?php echo esc_html( $this->rc_allow_name ); ?>"
						data-nonce="<?php echo esc_html( wp_create_nonce( 'rc_sdk' ) ); ?>"
						data-review_url="<?php echo esc_html( $this->review_url ); ?>" name="rc_allow_status" value="skip"
						class="button rc-button-skip button-secondary">
						I'll skip for now
					</button>
				</p>
			</div>
			<?php
		}

		/**
		 * Dismiss Notice
		 *
		 * @return void
		 */
		public function rc_sdk_dismiss_notice() {
			$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			$rc_name = isset( $_POST['rc_name'] ) ? sanitize_text_field( $_POST['rc_name'] ) : '';

			if ( ! wp_verify_nonce( $nonce, 'rc_sdk' ) ) {
				wp_send_json( array(
					'status'  => 'error',
					'title'   => 'Error',
					'message' => 'Nonce verification failed',
				) );
				wp_die();
			}

			set_transient( 'dismissed_notice_' . $rc_name, true, 30 * DAY_IN_SECONDS );

			wp_send_json( array(
				'status'  => 'success',
				'title'   => 'Success',
				'message' => 'Success.',
			) );
			wp_die();
		}
	}

}

/**
 * Main Insights Function
 */
if ( ! function_exists( 'rc_sdk_automate' ) ) {
	function rc_sdk_automate( $params ) {
		if ( class_exists( 'RC_Reviews_Collector' ) ) {
			new RC_Reviews_Collector( $params );
		}
	}
}
