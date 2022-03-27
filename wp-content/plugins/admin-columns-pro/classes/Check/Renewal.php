<?php

namespace ACP\Check;

use AC\Admin\Page\Columns;
use AC\Admin\Page\Settings;
use AC\Ajax;
use AC\Capabilities;
use AC\Message;
use AC\Registrable;
use AC\Screen;
use AC\Storage;
use AC\Type\Url\Site;
use AC\Type\Url\UtmTags;
use ACP\Access\ActivationStorage;
use ACP\ActivationTokenFactory;
use ACP\Entity;
use ACP\Type\Activation\ExpiryDate;
use ACP\Type\SiteUrl;
use DateTime;
use Exception;

class Renewal
	implements Registrable {

	/**
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * @var ActivationTokenFactory
	 */
	private $activation_token_factory;

	/**
	 * @var ActivationStorage
	 */
	private $activation_storage;

	/**
	 * @var array
	 */
	private $intervals = [ 1, 7, 21 ];

	/**
	 * @var SiteUrl
	 */
	private $site_url;

	public function __construct( $plugin_basename, ActivationTokenFactory $activation_token_factory, ActivationStorage $activation_storage, SiteUrl $site_url ) {
		$this->plugin_basename = (string) $plugin_basename;
		$this->activation_token_factory = $activation_token_factory;
		$this->activation_storage = $activation_storage;
		$this->site_url = $site_url;
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
			exit;
		}

		// 90 days
		$this->get_dismiss_option( $interval )->save( time() + ( MONTH_IN_SECONDS * 3 ) );

		exit;
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

	private function get_activation() {
		$token = $this->activation_token_factory->create();

		return $token
			? $this->activation_storage->find( $token )
			: null;
	}

	private function is_activation_up_for_renewal( Entity\Activation $activation ) {
		return ! $activation->is_auto_renewal()
		       && ! $activation->is_expired()
		       && ! $activation->is_cancelled()
		       && ! $activation->is_lifetime()
		       && $activation->get_expiry_date()->exists();
	}

	/**
	 * @param Screen $screen
	 *
	 * @throws Exception
	 */
	public function display( Screen $screen ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( true === apply_filters( 'acp/hide_renewal_notice', false ) ) {
			return;
		}

		switch ( true ) {

			// Inline message on plugin page
			case $screen->is_plugin_screen():
				$activation = $this->get_activation();

				if ( $activation && $this->is_activation_up_for_renewal( $activation ) && $activation->get_expiry_date()->is_expiring_within_seconds( DAY_IN_SECONDS * 21 ) ) {
					$notice = new Message\Plugin(
						$this->get_message( $activation->get_expiry_date() ),
						$this->plugin_basename
					);
					$notice->register();
				}

				return;

			// Permanent displayed on settings page
			case $screen->is_admin_screen( Settings::NAME ):
				$activation = $this->get_activation();

				if ( $activation && $this->is_activation_up_for_renewal( $activation ) && $activation->get_expiry_date()->is_expiring_within_seconds( DAY_IN_SECONDS * 21 ) ) {
					$notice = new Message\Notice( $this->get_message( $activation->get_expiry_date() ) );
					$notice
						->set_type( $notice::WARNING )
						->register();
				}

				return;

			// Dismissible
			case ( $screen->is_list_screen() || $screen->is_admin_screen( Columns::NAME ) ):
				$activation = $this->get_activation();

				if ( ! $activation || ! $this->is_activation_up_for_renewal( $activation ) ) {
					return;
				}

				$days_remaining = $activation->get_expiry_date()->get_remaining_days();

				$interval = $days_remaining > 0
					? $this->get_current_interval( (int) floor( $days_remaining ) )
					: null;

				if ( $interval && $this->get_dismiss_option( $interval )->is_expired() ) {
					$notice = new Message\Notice\Dismissible( $this->get_message( $activation->get_expiry_date() ), $this->get_ajax_handler_interval( $interval ) );
					$notice
						->set_type( $notice::WARNING )
						->register();
				}

				return;
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
	 *
	 * @return string
	 */
	private function localize_date( DateTime $date ) {
		return (string) ac_format_date( get_option( 'date_format' ), $date->getTimestamp() );
	}

	/**
	 * @param ExpiryDate $expiry_date
	 *
	 * @return string
	 */
	protected function get_message( ExpiryDate $expiry_date ) {
		$url = new UtmTags( new Site( Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'renewal' );
		$activation_token = $this->activation_token_factory->create();

		if ( $activation_token ) {
			$url->add( [
				$activation_token->get_type() => $activation_token->get_token(),
				'site_url'                    => $this->site_url->get_url(),
			] );
		}

		$renewal_link = sprintf( '<a href="%s">%s</a>', $url->get_url(), __( 'Renew your license', 'codepress-admin-columns' ) );
		$remaining_time = sprintf( '<strong>%s</strong>', $expiry_date->get_human_time_diff() );
		$localize_date = sprintf( '<strong>%s</strong>', $this->localize_date( $expiry_date->get_value() ) );

		return sprintf(
			__( "Your Admin Columns Pro license will expire in %s. In order get access to new features and receive security updates, please %s before %s.", 'codepress-admin-columns' ),
			$remaining_time,
			strtolower( $renewal_link ),
			$localize_date
		);
	}

}