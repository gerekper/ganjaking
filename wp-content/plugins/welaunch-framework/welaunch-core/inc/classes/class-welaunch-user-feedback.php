<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by CoBlocks
 * https://github.com/coblocks/coblocks/blob/master/includes/admin/class-coblocks-feedback.php
 *
 * @package   weLaunch
 * @author    Jeffrey Carandang
 * @link      https://welaunch.io
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Feedback Notice Class
 */
class weLaunch_User_Feedback {

	/**
	 * Slug.
	 *
	 * @var string $slug
	 */
	private $slug;

	/**
	 * Name.
	 *
	 * @var string $name
	 */
	private $name;

	/**
	 * Time limit.
	 *
	 * @var string $time_limit
	 */
	private $time_limit;

	/**
	 * No Bug Option.
	 *
	 * @var string $nobug_option
	 */
	public $nobug_option;

	/**
	 * Activation Date Option.
	 *
	 * @var string $date_option
	 */
	public $date_option;

	/**
	 * Class constructor.
	 *
	 * @param string $args Arguments.
	 */
	public function __construct( $args ) {

		$this->slug = $args['slug'];
		$this->name = $args['name'];

		$this->date_option  = $this->slug . '_activation_date';
		$this->nobug_option = $this->slug . '_no_bug';

		if ( isset( $args['time_limit'] ) ) {
			$this->time_limit = $args['time_limit'];
		} else {
			$this->time_limit = WEEK_IN_SECONDS;
		}

		if ( ! class_exists( 'weLaunch_Framework_Plugin' ) || ( class_exists( 'weLaunch_Framework_Plugin' ) && false === weLaunch_Framework_Plugin::$crash ) ) {
			// Add actions.
			add_action( 'admin_init', array( $this, 'check_installation_date' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}
	}

	/**
	 * Seconds to words.
	 *
	 * @param string $seconds Seconds in time.
	 * @return string
	 */
	public function seconds_to_words( $seconds ) {

		// Get the years.
		$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
		if ( $years > 1 ) {
			/* translators: Number of years */
			return sprintf( __( '%s years', 'welaunch-framework' ), $years );
		} elseif ( $years > 0 ) {
			return __( 'a year', 'welaunch-framework' );
		}

		// Get the weeks.
		$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
		if ( $weeks > 1 ) {
			/* translators: Number of weeks */
			return sprintf( __( '%s weeks', 'welaunch-framework' ), $weeks );
		} elseif ( $weeks > 0 ) {
			return __( 'a week', 'welaunch-framework' );
		}

		// Get the days.
		$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
		if ( $days > 1 ) {
			/* translators: Number of days */
			return sprintf( __( '%s days', 'welaunch-framework' ), $days );
		} elseif ( $days > 0 ) {
			return __( 'a day', 'welaunch-framework' );
		}

		// Get the hours.
		$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
		if ( $hours > 1 ) {
			/* translators: Number of hours */
			return sprintf( __( '%s hours', 'welaunch-framework' ), $hours );
		} elseif ( $hours > 0 ) {
			return __( 'an hour', 'welaunch-framework' );
		}

		// Get the minutes.
		$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
		if ( $minutes > 1 ) {
			/* translators: Number of minutes */
			return sprintf( __( '%s minutes', 'welaunch-framework' ), $minutes );
		} elseif ( $minutes > 0 ) {
			return __( 'a minute', 'welaunch-framework' );
		}

		// Get the seconds.
		$seconds = intval( $seconds ) % 60;
		if ( $seconds > 1 ) {
			/* translators: Number of seconds */
			return sprintf( __( '%s seconds', 'welaunch-framework' ), $seconds );
		} elseif ( $seconds > 0 ) {
			return __( 'a second', 'welaunch-framework' );
		}
	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		if ( ! get_site_option( $this->nobug_option ) || false === get_site_option( $this->nobug_option ) ) {

			add_site_option( $this->date_option, time() );

			// Retrieve the activation date.
			$install_date = get_site_option( $this->date_option );

			// If difference between install date and now is greater than time limit, then display notice.
			if ( ( time() - $install_date ) > $this->time_limit ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}
		}
	}

	/**
	 * Display the admin notice.
	 */
	public function display_admin_notice() {

		$screen = get_current_screen();

		if ( isset( $screen->base ) && 'plugins' === $screen->base ) {
			$no_bug_url = wp_nonce_url( admin_url( 'plugins.php?' . $this->nobug_option . '=true' ), 'welaunch-feedback-nounce' );
			$time       = $this->seconds_to_words( time() - get_site_option( $this->date_option ) );
			?>
			<style>
				.notice.welaunch-notice {
					border-left-color: #24b0a6 !important;
					padding: 20px;
				}
				.rtl .notice.welaunch-notice {
					border-right-color: #19837c !important;
				}
				.notice.notice.welaunch-notice .welaunch-notice-inner {
					display: table;
					width: 100%;
				}
				.notice.welaunch-notice .welaunch-notice-inner .welaunch-notice-icon,
				.notice.welaunch-notice .welaunch-notice-inner .welaunch-notice-content,
				.notice.welaunch-notice .welaunch-notice-inner .welaunch-install-now {
					display: table-cell;
					vertical-align: middle;
				}
				.notice.welaunch-notice .welaunch-notice-icon {
					color: #509ed2;
					font-size: 13px;
					width: 60px;
				}
				.notice.welaunch-notice .welaunch-notice-icon img {
					width: 64px;
				}
				.notice.welaunch-notice .welaunch-notice-content {
					padding: 0 40px 0 20px;
				}
				.notice.welaunch-notice p {
					padding: 0;
					margin: 0;
				}
				.notice.welaunch-notice h3 {
					margin: 0 0 5px;
				}
				.notice.welaunch-notice .welaunch-install-now {
					text-align: center;
				}
				.notice.welaunch-notice .welaunch-install-now .welaunch-install-button {
					padding: 6px 50px;
					height: auto;
					line-height: 20px;
					background: #24b0a6;
					border-color: transparent;
					font-weight: bold;
				}
				.notice.welaunch-notice .welaunch-install-now .welaunch-install-button:hover {
					background: #19837c;
				}
				.notice.welaunch-notice a.no-thanks {
					display: block;
					margin-top: 10px;
					color: #72777c;
					text-decoration: none;
				}

				.notice.welaunch-notice a.no-thanks:hover {
					color: #444;
				}

				@media (max-width: 767px) {

					.notice.notice.welaunch-notice .welaunch-notice-inner {
						display: block;
					}
					.notice.welaunch-notice {
						padding: 20px !important;
					}
					.notice.welaunch-noticee .welaunch-notice-inner {
						display: block;
					}
					.notice.welaunch-notice .welaunch-notice-inner .welaunch-notice-content {
						display: block;
						padding: 0;
					}
					.notice.welaunch-notice .welaunch-notice-inner .welaunch-notice-icon {
						display: none;
					}

					.notice.welaunch-notice .welaunch-notice-inner .welaunch-install-now {
						margin-top: 20px;
						display: block;
						text-align: left;
					}

					.notice.welaunch-notice .welaunch-notice-inner .no-thanks {
						display: inline-block;
						margin-left: 15px;
					}
				}
			</style>
			<div class="notice updated welaunch-notice">
				<div class="welaunch-notice-inner">
					<div class="welaunch-notice-icon">
						<?php /* translators: 1. Name */ ?>
						<img src="<?php echo esc_url( weLaunch_Core::$url . '/assets/img/icon--color.svg' ); ?>" alt="<?php printf( esc_attr__( '%s WordPress Plugin', 'welaunch-framework' ), esc_attr( $this->name ) ); ?>" />
					</div>
					<div class="welaunch-notice-content">
						<?php /* translators: 1. Name */ ?>
						<h3><?php printf( esc_html__( 'Are you enjoying %s?', 'welaunch-framework' ), esc_html( $this->name ) ); ?></h3>
						<p>
							<?php /* translators: 1. Name, 2. Time */ ?>
							<?php printf( esc_html__( 'You have been using %1$s for %2$s now. Would you mind leaving a review to let us know know what you think? We\'d really appreciate it!', 'welaunch-framework' ), esc_html( $this->name ), esc_html( $time ) ); ?>
						</p>
					</div>
					<div class="welaunch-install-now">
						<?php printf( '<a href="%1$s" class="button button-primary welaunch-install-button" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/welaunch-framework/reviews/?filter=5#new-post' ), esc_html__( 'Leave a Review', 'welaunch-framework' ) ); ?>
						<a href="<?php echo esc_url( $no_bug_url ); ?>" class="no-thanks"><?php echo esc_html__( 'No thanks / I already have', 'welaunch-framework' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page.
		// phpcs:ignore
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'welaunch-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		add_site_option( $this->nobug_option, true );
	}
}

/*
 * Instantiate the weLaunch_User_Feedback class.
 */
new weLaunch_User_Feedback(
	array(
		'slug'       => 'weLaunch_plugin_feedback',
		'name'       => __( 'weLaunch', 'welaunch-framework' ),
		'time_limit' => WEEK_IN_SECONDS,
	)
);
