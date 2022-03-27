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
use ACP\Access\ActivationStorage;
use ACP\ActivationTokenFactory;
use ACP\Entity;
use ACP\Type\SiteUrl;
use DateTime;
use Exception;

class Expired implements Registrable {

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

	private function is_activation_expired( Entity\Activation $activation ) {
		if ( ! $activation->is_expired()
		     || ! $activation->get_expiry_date()->exists() ) {
			return false;
		}

		// Prevent overlap with auto renewal payments and message
		if ( $activation->is_auto_renewal() && $activation->is_expired() && $activation->get_expiry_date()->get_expired_seconds() < ( 2 * DAY_IN_SECONDS ) ) {
			return false;
		}

		return true;
	}

	private function get_activation() {
		$token = $this->activation_token_factory->create();

		return $token
			? $this->activation_storage->find( $token )
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

		switch ( true ) {

			// Inline message on plugin page
			case $screen->is_plugin_screen() :
				$activation = $this->get_activation();

				if ( $activation && $this->is_activation_expired( $activation ) ) {

					$this->register_notice(
						new Message\Plugin(
							$this->get_message( $activation->get_expiry_date()->get_value() ),
							$this->plugin_basename
						)
					);
				}

				return;

			// Permanent displayed on settings page
			case $screen->is_admin_screen() :
				$activation = $this->get_activation();

				if ( $activation && $this->is_activation_expired( $activation ) ) {

					$this->register_notice(
						new Message\Notice(
							$this->get_message( $activation->get_expiry_date()->get_value() )
						)
					);
				}

				return;

			// Dismissible on list table
			case $screen->is_list_screen() && $this->get_dismiss_option()->is_expired() :
				$activation = $this->get_activation();

				if ( $activation && $this->is_activation_expired( $activation ) ) {

					$this->register_notice(
						new Message\Notice\Dismissible( $this->get_message( $activation->get_expiry_date()->get_value() ), $this->get_ajax_handler() )
					);
				}

				return;
		}
	}

	private function register_notice( Message $notice ) {
		$notice
			->set_type( Message::WARNING )
			->register();
	}

	/**
	 * @param DateTime $expiration_date
	 *
	 * @return string
	 */
	private function get_message( DateTime $expiration_date ) {
		$expired_on = ac_format_date( get_option( 'date_format' ), $expiration_date->getTimestamp() );

		$activation_token = $this->activation_token_factory->create();
		$url = new UtmTags( new Site( Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'expired' );

		if ( $activation_token ) {
			$url->add( [
				$activation_token->get_type() => $activation_token->get_token(),
				'site_url'                    => $this->site_url->get_url(),
			] );
		}

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