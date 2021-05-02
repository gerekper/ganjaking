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
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;

class Activation
	implements Registrable {

	/**
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * @var LicenseRepository
	 */
	private $license_repository;

	/**
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	public function __construct( $plugin_basename, LicenseRepository $license_repository, LicenseKeyRepository $license_key_repository ) {
		$this->plugin_basename = $plugin_basename;
		$this->license_repository = $license_repository;
		$this->license_key_repository = $license_key_repository;
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'register_notice' ] );

		$this->get_ajax_handler()->register();
	}

	/**
	 * @return bool
	 */
	private function show_message() {
		$license_key = $this->license_key_repository->find();

		if ( ! $license_key ) {
			return true;
		}

		$license = $this->license_repository->find( $license_key );

		if ( ! $license ) {
			return true;
		}

		// An expired license has it's own message
		if ( $license->is_expired() ) {
			return false;
		}

		return ! $license->is_active();
	}

	/**
	 * @param Screen $screen
	 */
	public function register_notice( Screen $screen ) {
		if ( ! $screen->has_screen() || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! $this->show_message() ) {
			return;
		}

		// Inline message on plugin page
		if ( $screen->is_plugin_screen() ) {
			$notice = new Message\Plugin( $this->get_message(), $this->plugin_basename );
			$notice
				->set_type( Message::INFO )
				->register();
		}

		// Permanent message on admin page
		if ( $screen->is_admin_screen() ) {
			$notice = new Message\Notice( $this->get_message() );
			$notice
				->set_type( Message::INFO )
				->register();
		}

		// Dismissible on list tables
		if ( $screen->get_list_screen() && $this->get_dismiss_option()->is_expired() ) {
			$notice = new Message\Notice\Dismissible( $this->get_message(), $this->get_ajax_handler() );
			$notice
				->set_type( Message::INFO )
				->register();
		}
	}

	/**
	 * @return string
	 */
	private function get_message() {
		$message = sprintf(
			__( "To enable automatic updates for %s, <a href='%s'>enter your license key</a>.", 'codepress_admin_columns' ),
			'Admin Columns Pro',
			acp_get_license_page_url()
		);

		$message .= ' ' . sprintf(
				__( "If you don't have a license key, please see %s.", 'codepress_admin_columns' ),
				sprintf(
					"<a href='%s' target='_blank'>%s</a>",
					( new UtmTags( new Site( Site::PAGE_PRICING ), 'plugins' ) )->get_url(),
					__( 'details & pricing', 'codepress-admin-columns' )
				)
			);

		return $message;
	}

	/**
	 * @return Ajax\Handler
	 */
	private function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler
			->set_action( 'ac_notice_dismiss_activation' )
			->set_callback( [ $this, 'ajax_dismiss_notice' ] );

		return $handler;
	}

	/**
	 * @return Storage\Timestamp
	 */
	private function get_dismiss_option() {
		return new Storage\Timestamp(
			new Storage\UserMeta( 'ac_notice_dismiss_activation' )
		);
	}

	public function ajax_dismiss_notice() {
		$this->get_ajax_handler()->verify_request();
		$this->get_dismiss_option()->save( time() + ( MONTH_IN_SECONDS * 2 ) );

		wp_die( 1 );
	}

}