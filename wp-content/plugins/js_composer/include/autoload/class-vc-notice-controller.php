<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Controller for plugin notice system.
 *
 * @description: We use it to notify users about,
 * emergency updates, new features and some promotions stuff etc.
 *
 * @since 7.0
 */
class Vc_Notice_Controller {
	/**
	 * Notification API URL.
	 * @version 7.0
	 * @var string
	 */
	protected $notification_api_url = 'https://support.wpbakery.com/api/external/notifications';

	/**
	 * Vc_Notice_Controller constructor.
	 *
	 * @since 7.0
	 */
	public function __construct() {
		add_action( 'admin_init', [
			$this,
			'init',
		] );
	}

	/**
	 * Init notice system.
	 *
	 * @since 7.0
	 */
	public function init() {
		$notice_list = $this->get_notice_list();

		$this->show_notices( $notice_list );
	}

	/**
	 * Show notices to user.
	 *
	 * @since 7.0
	 * @param mixed $notice_list
	 */
	public function show_notices( $notice_list ) {
		if ( empty( $notice_list ) || ! is_array( $notice_list ) ) {
			return;
		}

		// last api request was empty or failed.
		if ( $this->is_api_response_empty( $notice_list ) ) {
			return;
		}

		$is_show_at_least_one_notice = false;
		foreach ( $notice_list as $notice ) {
			if ( ! $this->is_notice_valid( $notice ) ) {
				continue;
			}

			if ( ! $this->is_show_notice( $notice['id'] ) ) {
				continue;
			}

			$this->output_notice( $notice );
			$is_show_at_least_one_notice = true;
		}

		if ( $is_show_at_least_one_notice ) {
			add_action(
				'admin_notices',
				function () use ( $notice ) {
					vc_include_template( 'params/notice/notice-assets.php' );
				}
			);
		}
	}

	/**
	 * Check if notice is valid.
	 *
	 * @since 7.0
	 * @param mixed $notice
	 * @return bool
	 */
	public function is_notice_valid( $notice ) {

		if ( empty( $notice ) || ! is_array( $notice ) ) {
			return false;
		}

		if ( ! $this->is_notice_version_valid( $notice, WPB_VC_VERSION ) ) {
			return false;
		}

		if ( ! $this->is_notice_date_valid( $notice, current_time( 'timestamp' ) ) ) {
			return false;
		}

		if ( ! $this->is_notice_content_valid( $notice ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if we should show notice.
	 *
	 * @note we don't show notice if user already closed it.
	 *
	 * @since 7.0
	 * @param int $notice_id
	 * @return bool
	 */
	public function is_show_notice( $notice_id ) {
		return empty( $_COOKIE[ 'wpb-notice-' . $notice_id ] );
	}

	/**
	 * Check if notice can be displayed in current plugin version.
	 *
	 * @since 7.0
	 * @param array $notice
	 * @param string $current_version
	 * @return bool
	 */
	public function is_notice_version_valid( $notice, $current_version ) {

		$result = false;

		if ( empty( $notice['version_from'] ) || empty( $notice['version_to'] ) ) {
			return $result;
		}

		$is_versions_value_valid =
			version_compare( $notice['version_from'], '0.0.1', '>=' ) &&
			version_compare( $notice['version_to'], '0.0.1', '>=' );

		if ( ! $is_versions_value_valid ) {
			return $result;
		}

		$is_version_inside_diapason =
			version_compare( $notice['version_from'], $current_version, '<=' ) &&
			version_compare( $current_version, $notice['version_to'], '<=' );

		if ( $is_version_inside_diapason ) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Check if notice can be displayed in current time.
	 *
	 * @since 7.0
	 * @param array $notice
	 * @param int $current_time
	 * @return bool
	 */
	public function is_notice_date_valid( $notice, $current_time ) {
		$result = false;

		if ( empty( $notice['date_from'] ) || empty( $notice['date_to'] ) ) {
			return $result;
		}

		$is_date_inside_diapason =
			($current_time >= strtotime( $notice['date_from'] ) ) &&
			($current_time <= strtotime( $notice['date_to'] ) );

		if ( $is_date_inside_diapason ) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Check if notice has content that we can show to user.
	 *
	 * @since 7.0
	 * @param $notice
	 * @return bool
	 */
	public function is_notice_content_valid( $notice ) {
		// notice always should have id
		if ( empty( $notice['id'] ) ) {
			return false;
		}

		// notice should have at least one element to show
		$is_notice_content_empty =
			empty( $notice['title'] ) &&
			empty( $notice['description'] ) &&
			empty( $notice['image'] ) &&
			empty( $notice['button_text'] );

		return $is_notice_content_empty ? false : true;
	}

	/**
	 * Output notice to user.
	 *
	 * @since 7.0
	 * @param array $notice
	 */
	public function output_notice( $notice ) {
		add_action(
			'admin_notices',
			function () use ( $notice ) {
				vc_include_template( 'params/notice/notice.php', [ 'notice' => $notice ] );
			}
		);
	}

	/**
	 * Get notices that should be displayed.
	 *
	 * @since 7.0
	 * @return array
	 */
	public function get_notice_list() {
		$notice_list = get_transient( 'wpb_notice_list' );

		if ( $this->is_api_response_empty( $notice_list ) ) {
			return [];
		}

		if ( ! $this->is_notice_list_valid( $notice_list ) ) {
			$notice_list = $this->get_notice_list_from_api_request();

			$this->save_notice_list_to_transient( $notice_list );
		}

		return json_decode( $notice_list, true );
	}

	/**
	 * Check if api response is empty.
	 */
	public function is_api_response_empty( $notice_list ) {
		return is_array( $notice_list ) && isset( $notice_list['empty_api_response'] );
	}
	/**
	 * Save notice list to transient.
	 *
	 * @since 7.0
	 * @param string $notice_list
	 */
	public function save_notice_list_to_transient( $notice_list ) {
		if ( ! $this->is_notice_list_valid( $notice_list ) ) {
			// in case if we have invalid notice list we save false value
			// to transient to prevent requests to our API more than 12 hours
			$empty = [ 'empty_api_response' => true ];
			set_transient( 'wpb_notice_list', $empty, 12 * HOUR_IN_SECONDS );
			return;
		}

		set_transient( 'wpb_notice_list', $notice_list, 12 * HOUR_IN_SECONDS );
	}

	/**
	 * Check if notice list is valid.
	 *
	 * @since 7.0
	 * @param mixed $notice_list
	 * @return bool
	 */
	public function is_notice_list_valid( $notice_list ) {
		if ( empty( $notice_list ) ) {
			return false;
		}

		if ( ! is_string( $notice_list ) ) {
			return false;
		}

		json_decode( $notice_list );
		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Get notices from notice API.
	 * @note we fire up request to our API once per 12 hours.
	 *
	 * @since 7.0
	 * @return string
	 */
	public function get_notice_list_from_api_request() {
		$empty_notice_list = '';

		$response = wp_remote_get( $this->notification_api_url, [ 'timeout' => 30 ] );

		if ( is_wp_error( $response ) ) {
			return $empty_notice_list;
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return $empty_notice_list;
		}

		$response_body = wp_remote_retrieve_body( $response );

		if ( $this->is_notice_list_valid( $response_body ) ) {
			$notice_list = $response_body;
		} else {
			$notice_list = $empty_notice_list;
		}

		return $notice_list;
	}
}

new Vc_Notice_Controller();
