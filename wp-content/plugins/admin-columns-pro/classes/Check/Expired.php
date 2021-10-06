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
use ACP\Type\License\Key;
use ACP\Type\SiteUrl;
use DateTime;
use Exception;

class Expired implements Registrable {

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

	public function __construct( LicenseRepository $license_repository, LicenseKeyRepository $license_key_repository, $plugin_basename, SiteUrl $site_url ) {
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
		$this->plugin_basename = $plugin_basename;
		$this->site_url = $site_url;
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'display' ] );

		$this->get_ajax_handler()->register();
	}

	/**
	 * @return bool
	 */
	private function show_message() {
		$license_key = $this->license_key_repository->find();

		if ( ! $license_key ) {
			return false;
		}

		$license = $this->license_repository->find( $license_key );

		if ( ! $license
		     || ! $license->is_expired()
		     || ! $license->get_expiry_date()->exists() ) {
			return false;
		}

		// Prevent overlap with auto renewal payments and message
		if ( $license->is_auto_renewal() && $license->is_expired() && $license->get_expiry_date()->get_expired_seconds() < ( 2 * DAY_IN_SECONDS ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return License|null
	 */
	private function get_license() {
		$license_key = $this->license_key_repository->find();

		return $license_key
			? $this->license_repository->find( $license_key )
			: null;
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

		// Inline message on plugin page
		if ( $screen->is_plugin_screen() && $this->show_message() ) {
			$license = $this->get_license();

			if ( ! $license ) {
				return;
			}

			$this->register_notice(
				new Message\Plugin(
					$this->get_message( $license->get_expiry_date()->get_value(), $license->get_key() ),
					$this->plugin_basename
				)
			);

			return;
		}

		// Permanent displayed on settings page
		if ( $screen->is_admin_screen() && $this->show_message() ) {
			$license = $this->get_license();

			if ( ! $license ) {
				return;
			}

			$this->register_notice(
				new Message\Notice( $this->get_message( $license->get_expiry_date()->get_value(), $license->get_key() ) )
			);

			return;
		}

		// Dismissible on list table
		if ( $screen->is_list_screen() && $this->get_dismiss_option()->is_expired() && $this->show_message() ) {
			$license = $this->get_license();

			if ( ! $license ) {
				return;
			}

			$this->register_notice(
				new Message\Notice\Dismissible( $this->get_message( $license->get_expiry_date()->get_value(), $license->get_key() ), $this->get_ajax_handler() )
			);
		}
	}

	private function register_notice( Message $notice ) {
		$notice
			->set_type( Message::WARNING )
			->register();
	}

	/**
	 * @param DateTime $expiration_date
	 * @param Key      $license_key
	 *
	 * @return string
	 */
	private function get_message( DateTime $expiration_date, Key $license_key ) {
		$expired_on = ac_format_date( get_option( 'date_format' ), $expiration_date->getTimestamp() );

		$url = new UtmTags( new Site( Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'expired' );

		$url->add( [
			'subscription_key' => $license_key->get_value(),
			'site_url'         => $this->site_url->get_url(),
		] );

		return sprintf(
			__( 'Your Admin Columns Pro license has expired on %s. To receive updates, renew your license on the %s.', 'codepress-admin-columns' ),
			'<strong>' . $expired_on . '</strong>',
			sprintf( '<a href="%s">%s</a>', $url->get_url(), __( 'My Account Page', 'codepress-admin-columns' ) )
		);
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler
			->set_action( 'ac_notice_dismiss_expired' )
			->set_callback( [ $this, 'ajax_dismiss_notice' ] );

		return $handler;
	}

	/**
	 * @return Storage\Timestamp
	 */
	protected function get_dismiss_option() {
		return new Storage\Timestamp(
			new Storage\UserMeta( 'ac_notice_dismiss_expired' )
		);
	}

	public function ajax_dismiss_notice() {
		$this->get_ajax_handler()->verify_request();
		$this->get_dismiss_option()->save( time() + MONTH_IN_SECONDS );
	}

}