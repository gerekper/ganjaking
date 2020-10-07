<?php

namespace ACP\Check;

use AC\Ajax;
use AC\Capabilities;
use AC\Message;
use AC\Registrable;
use AC\Screen;
use AC\Storage;
use AC\Type\Url\Site;
use AC\Type\Url\UtmTags;
use ACP\Entity\License;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Type\SiteUrl;
use DateTime;
use Exception;

class Renewal
	implements Registrable {

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	/**
	 * @var int[] Intervals to check in ascending order with a max of 90 days
	 */
	protected $intervals;

	public function __construct( LicenseRepository $license_repository, LicenseKeyRepository $license_key_repository, $plugin_basename, SiteUrl $site_url ) {
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->plugin_basename = $plugin_basename;
		$this->site_url = $site_url;
		$this->intervals = [ 1, 7, 21 ];
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'display' ] );

		$this->get_ajax_handler()->register();
	}

	/**
	 * @throws Exception
	 */
	public function ajax_dismiss_notice() {
		$this->get_ajax_handler()->verify_request();

		$interval = (int) filter_input( INPUT_POST, 'interval', FILTER_SANITIZE_NUMBER_INT );

		if ( ! array_key_exists( $interval, $this->intervals ) ) {
			wp_die();
		}

		// 90 days
		$result = $this->get_dismiss_option( $interval )->save( time() + ( MONTH_IN_SECONDS * 3 ) );

		wp_die( $result );
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'ac_notice_dismiss_renewal' )
		        ->set_callback( [ $this, 'ajax_dismiss_notice' ] );

		return $handler;
	}

	/**
	 * @param int $interval
	 *
	 * @return Storage\Timestamp
	 * @throws Exception
	 */
	protected function get_dismiss_option( $interval ) {
		return new Storage\Timestamp(
			new Storage\UserMeta( 'ac_notice_dismiss_renewal_' . $interval )
		);
	}

	/**
	 * @param Screen $screen
	 *
	 * @throws Exception
	 */
	public function display( Screen $screen ) {
		if ( ! $screen->has_screen() || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( true === apply_filters( 'acp/hide_renewal_notice', false ) ) {
			return;
		}

		$license_key = $this->license_key_repository->find();

		if ( ! $license_key ) {
			return;
		}

		$license = $this->license_repository->find( $license_key );

		if ( ! $license
		     || $license->is_auto_renewal()
		     || $license->is_expired()
		     || $license->is_cancelled()
		     || $license->is_lifetime()
		     || ! $license->get_expiry_date()->exists()
		) {
			return;
		}

		$days_remaining = $license->get_expiry_date()->get_remaining_days();

		$interval = $days_remaining > 0
			? $this->get_current_interval( (int) floor( $days_remaining ) )
			: null;

		$notice = null;

		if ( $screen->is_plugin_screen() && $license->get_expiry_date()->is_expiring_within_seconds( DAY_IN_SECONDS * 21 ) ) {
			// Inline message on plugin page
			$notice = new Message\Plugin( $this->get_message( $license ), $this->plugin_basename );
		} else if ( $screen->is_admin_screen( 'settings' ) && $license->get_expiry_date()->is_expiring_within_seconds( DAY_IN_SECONDS * 21 ) ) {
			// Permanent displayed on settings page
			$notice = new Message\Notice( $this->get_message( $license ) );
		} else if ( ( $screen->is_list_screen() || $screen->is_admin_screen( 'columns' ) ) && $interval && $this->get_dismiss_option( $interval )->is_expired() ) {
			// Dismissible
			$notice = new Message\Notice\Dismissible( $this->get_message( $license ), $this->get_ajax_handler_interval( $interval ) );
		}

		if ( $notice instanceof Message ) {
			$notice
				->set_type( $notice::WARNING )
				->register();
		}
	}

	/**
	 * @param int $interval
	 *
	 * @return Ajax\Handler
	 */
	private function get_ajax_handler_interval( $interval ) {
		$ajax_handler = $this->get_ajax_handler();
		$ajax_handler->set_param( 'interval', $interval );

		return $ajax_handler;
	}

	/**
	 * Get the current interval compared to the license state. Returns false when no interval matches
	 *
	 * @param int $remaining_days
	 *
	 * @return false|int
	 */
	protected function get_current_interval( $remaining_days ) {
		foreach ( $this->intervals as $k => $interval ) {
			if ( $interval >= $remaining_days ) {
				return $k;
			}
		}

		return false;
	}

	/**
	 * @param DateTime $date
	 * @param string   $format
	 *
	 * @return string
	 */
	private function localize_date( DateTime $date, $format = null ) {
		if ( null === $format ) {
			$format = get_option( 'date_format' );
		}

		return ac_format_date( $format, $date->getTimestamp() );
	}

	/**
	 * @param License $license
	 *
	 * @return string
	 */
	protected function get_message( License $license ) {
		$url = new UtmTags( new Site( Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'renewal' );

		$url->add( [
			'subscription_key' => $license->get_key()->get_value(),
			'site_url'         => $this->site_url->get_url(),
		] );

		$renewal_link = sprintf( '<a href="%s">%s</a>', $url->get_url(), __( 'Renew your license', 'codepress-admin-columns' ) );
		$remaining_time = sprintf( '<strong>%s</strong>', $license->get_expiry_date()->get_human_time_diff() );
		$expiry_date = sprintf( '<strong>%s</strong>', $this->localize_date( $license->get_expiry_date()->get_value() ) );

		if ( $license->get_renewal_discount()->get_value() ) {
			return sprintf(
				__( "Your Admin Columns Pro license will expire in %s. %s before %s to get a %d%% discount!", 'codepress-admin-columns' ),
				$remaining_time,
				$renewal_link,
				$expiry_date,
				$license->get_renewal_discount()->get_value()
			);
		}

		return sprintf(
			__( "Your Admin Columns Pro license will expire in %s. In order get access to new features and receive security updates, please %s before %s.", 'codepress-admin-columns' ),
			$remaining_time,
			strtolower( $renewal_link ),
			$expiry_date
		);
	}

}