<?php

namespace Ademti\DismissibleWpNotices;

use Ademti\DismissibleWpNotices\DismissibleWpNotice;
use Exception;

class DismissibleWpNoticeManager {

	/**
	 * @var DismissibleWpNoticeManager
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private $uri_path = '';

	/**
	 * @var bool
	 */
	private $ajaxAttached = false;

	/**
	 * @var array
	 */
	private $notices = [];

	/**
	 *
	 */
	const JS_VERSION = '1.0';

	/**
	 * Constructor. Attaches AJAX callback, and enqueues JS.
	 */
	private function __construct( string $uri_path ) {
		$this->uri_path = $uri_path;
		add_action( 'wp_ajax_ademti_dismissible_wp_notices_dismiss', [ $this, 'handle_dismiss' ] );
		add_action( 'wp_ajax_ademti_dismissible_wp_notices_snooze', [ $this, 'handle_snooze' ] );
		add_action( 'init', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * @param $uri_path
	 *
	 * @return DismissibleWpNoticeManager
	 */
	public static function get_instance( $uri_path ) {
		if ( self::$instance == null ) {
			self::$instance = new DismissibleWpNoticeManager( $uri_path );
		}
		// Note $uri_path ignored if we already have an instance.
		// We will get an instance with it's already configured URI path.

		return self::$instance;
	}

	/**
	 * @param string $slug
	 * @param bool $per_user
	 * @param bool $per_site
	 *
	 * @return void
	 */
	public function register_notice(
		string $slug,
		bool $per_user = false,
		int $snooze_duration = WEEK_IN_SECONDS,
		bool $per_site = false
	) {
		$notice                 = new DismissibleWpNotice( $slug, $per_user, $snooze_duration, $per_site );
		$this->notices[ $slug ] = $notice;
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		$js_uri = $this->uri_path . 'js/dismissible-wp-notices.js';
		wp_enqueue_script( 'dismissible-wp-notices', $js_uri, [ 'jquery' ], self::JS_VERSION, true );
		wp_localize_script(
			'dismissible-wp-notices',
			'dismissibleWpNotices',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'dismissible_wp_notices' )
			]
		);
	}

	/**
	 * Handle an AJAX dismiss request.
	 *
	 * Extract and validate info before making the call to actually do the dismiss.
	 *
	 * @return void
	 */
	public function handle_dismiss() {
		// Extract info from the POST request.
		$slug  = $_POST['slug'] ?? '';
		$nonce = $_POST['nonce'] ?? '';
		wp_verify_nonce( $nonce, 'dismissible_wp_notices' ) || die( 'Invalid authorisation' );

		return $this->dismiss( $slug );
	}

	/**
	 * @param string $slug
	 *
	 * @return void
	 */
	public function dismiss( string $slug ) {
		$notice = $this->notices[ $slug ] ?? null;
		if ( $notice === null ) {
			die( 'Unknown notice. Aborting' );
		}
		$key     = $this->get_key_for_notice( $notice, 'dismiss' );
		$user    = wp_get_current_user();
		$user_id = $user->ID ?? null;

		if ( ! $notice->per_site && ! $notice->per_user ) {
			update_option( $key, 1 );
		} elseif ( $notice->per_site && ! $notice->per_user ) {
			update_site_option( $key, 1 );
		} elseif ( ! $notice->per_site && $notice->per_user ) {
			update_user_meta( $user_id, $key, 1 );
		} else {
			update_user_option( $user_id, $key, 1 );
		}
	}

	/**
	 * Handle an AJAX dismiss request.
	 *
	 * Extract and validate info before making the call to actually do the dismiss.
	 *
	 * @return void
	 */
	public function handle_snooze() {
		// Extract info from the POST request.
		$slug  = $_POST['slug'] ?? '';
		$nonce = $_POST['nonce'] ?? '';
		wp_verify_nonce( $nonce, 'dismissible_wp_notices' ) || die( 'Invalid authorisation' );

		return $this->snooze( $slug );
	}

	/**
	 * @param string $slug
	 *
	 * @return void
	 */
	public function snooze( string $slug ) {
		$notice = $this->notices[ $slug ] ?? null;
		if ( $notice === null ) {
			die( 'Unknown notice. Aborting' );
		}
		$key           = $this->get_key_for_notice( $notice, 'snooze' );
		$snoozed_until = time() + $notice->snooze_duration;
		$user          = wp_get_current_user();
		$user_id       = $user->ID ?? null;

		if ( ! $notice->per_site && ! $notice->per_user ) {
			update_option( $key, $snoozed_until );
		} elseif ( $notice->per_site && ! $notice->per_user ) {
			update_site_option( $key, $snoozed_until );
		} elseif ( ! $notice->per_site && $notice->per_user ) {
			update_user_meta( $user_id, $key, $snoozed_until );
		} else {
			update_user_option( $user_id, $key, $snoozed_until );
		}
	}

	/**
	 * @param \Ademti\DismissibleWpNotices\DismissibleWpNotice $notice
	 * @param string $action
	 *
	 * @return string
	 * @throws Exception
	 */
	private function get_key_for_notice( DismissibleWpNotice $notice, string $action ) {
		switch ( $action ) {
			case 'snooze':
				return 'adwn_' . $notice->slug . '_snoozed_until';
				break;
			case 'dismiss':
				return 'adwn_' . $notice->slug . '_dismissed';
				break;
			default;
				throw new Exception( 'Invalid request' );
				break;
		}
	}

	/**
	 * @param string $noticeSlug
	 *
	 * @return bool
	 */
	public function is_notice_visible( string $slug ): bool {
		$notice = $this->notices[ $slug ] ?? null;
		if ( $notice === null ) {
			die( 'Unknown notice. Aborting' );
		}

		return ! $this->is_notice_snoozed( $notice ) &&
			   ! $this->is_notice_dismissed( $notice );
	}

	/**
	 * @param DismissibleWpNotice $notice
	 *
	 * @return bool
	 */
	private function is_notice_dismissed( DismissibleWpNotice $notice ) {
		$key     = $this->get_key_for_notice( $notice, 'dismiss' );
		$user    = wp_get_current_user();
		$user_id = $user->ID ?? null;

		if ( ! $notice->per_site && ! $notice->per_user ) {
			$dismissed = get_option( $key, false );
		} elseif ( $notice->per_site && ! $notice->per_user ) {
			$dismissed = get_site_option( $key, false );
		} elseif ( ! $notice->per_site && $notice->per_user ) {
			$dismissed = get_user_meta( $user_id, $key, true );
		} else {
			$dismissed = get_user_option( $key, $user_id );
		}

		return (bool) $dismissed;
	}

	/**
	 * @param DismissibleWpNotice $notice
	 *
	 * @return bool
	 */
	private function is_notice_snoozed( DismissibleWpNotice $notice ) {

		$key     = $this->get_key_for_notice( $notice, 'snooze' );
		$user    = wp_get_current_user();
		$user_id = $user->ID ?? null;

		if ( ! $notice->per_site && ! $notice->per_user ) {
			$snoozed_until = get_option( $key, 0 );
		} elseif ( $notice->per_site && ! $notice->per_user ) {
			$snoozed_until = get_site_option( $key, 0 );
		} elseif ( ! $notice->per_site && $notice->per_user ) {
			$snoozed_until = get_user_meta( $user_id, $key, true );
			if ( $snoozed_until === '' ) {
				$snoozed_until = 0;
			}
		} else {
			$snoozed_until = get_user_option( $key, $user_id ) || 0;
		}

		return $snoozed_until > time();
	}
}
