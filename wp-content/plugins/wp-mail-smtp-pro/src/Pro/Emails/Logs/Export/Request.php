<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WPMailSMTP\Pro\Emails\Logs\EmailsCollection;

/**
 * Export request.
 *
 * @since 2.8.0
 */
class Request {

	/**
	 * Request method.
	 *
	 * @since 2.8.0
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Request arguments.
	 *
	 * @since 2.8.0
	 *
	 * @var array
	 */
	protected $args;

	/**
	 * Request data.
	 *
	 * @since 2.8.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param string $method    Request method.
	 * @param bool   $init_data Should request data be initialized.
	 */
	public function __construct( $method = 'GET', $init_data = true ) {

		$this->method = $method;
		$this->args   = $this->get_args();

		if ( $init_data ) {
			$this->populate_request_data();
		}
	}

	/**
	 * Initialize new request or get existing by request id.
	 *
	 * @since 2.8.0
	 */
	protected function populate_request_data() {

		if ( empty( $this->args['request_id'] ) ) {
			$this->data = $this->get_initial_request_data();
		} else {
			$data = get_transient( 'wp_mail_smtp_tools_export_email_logs_request_' . $this->args['request_id'] );

			if ( $this->get_arg( 'step' ) > 1 ) {
				$data['db_args']['offset'] = $data['db_args']['per_page'] * ( $this->get_arg( 'step' ) - 1 );
			}

			$this->data = $data;
		}
	}

	/**
	 * Save request data to transient option with defined time to live.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function persist() {

		if ( empty( $this->get_request_id() ) ) {
			return false;
		}

		return set_transient(
			'wp_mail_smtp_tools_export_email_logs_request_' . $this->get_request_id(),
			$this->data,
			Export::get_config( 'export', 'request_data_ttl' )
		);
	}

	/**
	 * Get request data.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $key Data key if we need particular option.
	 *
	 * @return mixed|null
	 */
	public function get_data( $key = false ) {

		if ( $key !== false ) {
			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}

		return $this->data;
	}

	/**
	 * Get argument value by key.
	 *
	 * @since 2.8.0
	 *
	 * @param bool $key Argument key if we need particular argument value.
	 *
	 * @return mixed|null
	 */
	public function get_arg( $key ) {

		return isset( $this->args[ $key ] ) ? $this->args[ $key ] : null;
	}

	/**
	 * Get request id helper.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_request_id() {

		return $this->get_data( 'request_id' );
	}

	/**
	 * Get request GET or POST args.
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function get_args() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		$args = [];

		$method = 'GET' === $this->method ? 'GET' : 'POST';
		$req    = 'GET' === $method ? $_GET : $_POST; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended

		// Action.
		$args['action'] = '';
		if ( ! empty( $req['action'] ) ) {
			$args['action'] = sanitize_text_field( wp_unslash( $req['action'] ) );
		}

		// Nonce.
		$args['nonce'] = '';
		if ( ! empty( $req['nonce'] ) ) {
			$args['nonce'] = sanitize_text_field( wp_unslash( $req['nonce'] ) );
		}

		// Step.
		$args['step'] = 1;
		if ( ! empty( $req['step'] ) ) {
			$args['step'] = intval( $req['step'] );
		}

		// Email ID.
		$args['email_id'] = 0;
		if ( ! empty( $req['email_id'] ) ) {
			$args['email_id'] = (int) $req['email_id'];
		}

		// Common Fields.
		$args['common_fields'] = [];
		if ( ! empty( $req['common_fields'] ) ) {
			$args['common_fields'] = array_map( 'sanitize_text_field', wp_unslash( $req['common_fields'] ) );
		}

		// Additional Fields.
		$args['additional_fields'] = [];
		if ( ! empty( $req['additional_fields'] ) ) {
			$args['additional_fields'] = array_map( 'sanitize_text_field', wp_unslash( $req['additional_fields'] ) );
		}

		// Export Type.
		$args['export_type'] = 'csv';
		if ( ! empty( $req['export_type'] ) ) {
			$args['export_type'] = sanitize_text_field( wp_unslash( $req['export_type'] ) );
		}

		// Date range.
		$args['date'] = [];
		if ( ! empty( $req['date'] ) ) {
			$dates = explode( ' - ', sanitize_text_field( wp_unslash( $req['date'] ) ) );

			switch ( count( $dates ) ) {
				case 1:
					$args['date'] = sanitize_text_field( $dates[0] );
					break;

				case 2:
					$args['date'] = array_map( 'sanitize_text_field', $dates );
					break;
			}
		}

		// Search.
		$args['search'] = [
			'place' => 'people',
			'term'  => '',
		];
		if ( isset( $req['search'] ) ) {
			if ( isset( $req['search']['place'] ) ) {
				$args['search']['place'] = sanitize_key( $req['search']['place'] );
			}
			if ( ! empty( $req['search']['term'] ) ) {
				$args['search']['term'] = sanitize_text_field( $req['search']['term'] );
			}
		}

		// Request id.
		$args['request_id'] = '';
		if ( ! empty( $req['request_id'] ) ) {
			$args['request_id'] = sanitize_text_field( wp_unslash( $req['request_id'] ) );
		}

		return $args;
	}

	/**
	 * Get initial request data at first step.
	 *
	 * @since 2.8.0
	 *
	 * @return array Request data.
	 */
	protected function get_initial_request_data() {

		$args = $this->args;

		// Prepare arguments.
		$db_args = [
			'offset'   => 0,
			'per_page' => Export::get_config( 'export', 'email_logs_per_step' ),
			'id'       => $args['email_id'],
			'date'     => $args['date'],
		];

		if ( $args['search']['term'] !== '' ) {
			$db_args['search']['place'] = $args['search']['place'];
			$db_args['search']['term']  = $args['search']['term'];
		}

		// Count total email logs.
		$count = ( new EmailsCollection( $db_args ) )->get_count();

		// Prepare `request data` for saving.
		$request_data = [
			'request_id'        => md5( wp_json_encode( $db_args ) . microtime() ),
			'db_args'           => $db_args,
			'common_fields'     => empty( $args['email_id'] ) ? $args['common_fields'] : array_keys( Export::get_common_fields() ),
			'additional_fields' => empty( $args['email_id'] ) ? $args['additional_fields'] : array_keys( Export::get_additional_fields() ),
			'count'             => $count,
			'total_steps'       => (int) ceil( $count / Export::get_config( 'export', 'email_logs_per_step' ) ),
			'type'              => $args['export_type'],
		];

		return $request_data;
	}

	/**
	 * Get notices.
	 *
	 * @since 2.9.0
	 *
	 * @param string $type Error type.
	 *
	 * @return array Notices.
	 */
	public function get_notices( $type = null ) {

		$notices = isset( $this->data['notices'] ) ? $this->data['notices'] : [];

		if ( $type !== null ) {
			return array_filter(
				$notices,
				function ( $notice ) use ( $type ) {
					return $notice['type'] === $type;
				}
			);
		}

		$order = [ 'error', 'warning', 'info' ];

		usort(
			$notices,
			function ( $a, $b ) use ( $order ) {
				$pos_a = array_search( $a['type'], $order, true );
				$pos_b = array_search( $b['type'], $order, true );

				return $pos_a - $pos_b;
			}
		);

		return $notices;
	}

	/**
	 * Add notice.
	 *
	 * @since 2.9.0
	 *
	 * @param string $msg  Notice message.
	 * @param string $type Error type. Valid types: info, warning, error.
	 */
	public function add_notice( $msg, $type = 'info' ) {

		$this->data['notices'][] = [
			'type'    => $type,
			'message' => $msg,
		];
	}
}
