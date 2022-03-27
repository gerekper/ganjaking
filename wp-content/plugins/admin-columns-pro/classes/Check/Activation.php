<?php

namespace ACP\Check;

use AC\Admin\Page\Addons;
use AC\Admin\Page\Columns;
use AC\Admin\Page\Settings;
use AC\Ajax;
use AC\Capabilities;
use AC\Message;
use AC\Registrable;
use AC\Screen;
use AC\Storage;
use AC\Type\Url;
use ACP\Access\ActivationStorage;
use ACP\Access\Permissions;
use ACP\Access\PermissionsStorage;
use ACP\ActivationTokenFactory;
use ACP\Admin\Page\License;
use ACP\Admin\Page\Tools;

class Activation
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
	 * @var PermissionsStorage
	 */
	private $permission_storage;

	/**
	 * @var bool
	 */
	private $is_network_active;

	public function __construct( $plugin_basename, ActivationTokenFactory $activation_token_factory, ActivationStorage $activation_storage, PermissionsStorage $permission_storage, $is_network_active ) {
		$this->plugin_basename = (string) $plugin_basename;
		$this->activation_token_factory = $activation_token_factory;
		$this->activation_storage = $activation_storage;
		$this->permission_storage = $permission_storage;
		$this->is_network_active = (bool) $is_network_active;
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'register_notice' ] );

		$this->get_ajax_handler()->register();
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
	 * @param Screen $screen
	 */
	public function register_notice( Screen $screen ) {
		if ( ! $screen->has_screen() || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		switch ( true ) {
			case $screen->is_plugin_screen() && $this->show_message() :
				$notice = new Message\Plugin(
					$this->get_message(),
					$this->plugin_basename,
					Message::INFO
				);
				$notice->register();
				break;
			case ( $screen->is_admin_screen( Settings::NAME ) || $screen->is_admin_screen( Columns::NAME ) || $screen->is_admin_screen( Tools::NAME ) || $screen->is_admin_screen( License::NAME ) ) && $this->show_message() :
				$notice = new Message\Notice( $this->get_message() );
				$notice
					->set_type( Message::INFO )
					->register();
				break;
			case $screen->is_admin_screen( Addons::NAME ) && $this->show_message() :
				$notice = new Message\Notice( $this->get_message_addon() );
				$notice
					->set_type( Message::INFO )
					->register();
				break;
			case $screen->get_list_screen() && $this->get_dismiss_option()->is_expired() && $this->show_message() :

				// Dismissible message on list table
				$notice = new Message\Notice\Dismissible( $this->get_message(), $this->get_ajax_handler() );
				$notice
					->set_type( Message::INFO )
					->register();
				break;
		}
	}

	/**
	 * @return bool
	 */
	private function show_message() {
		// We send a different (locked) message when a use has no usage permissions
		$has_usage = $this->permission_storage->retrieve()->has_permission( Permissions::USAGE );

		if ( ! $has_usage ) {
			return false;
		}

		$token = $this->activation_token_factory->create();
		$activation = $token ? $this->activation_storage->find( $token ) : null;

		if ( ! $activation ) {
			return true;
		}

		// An expired license has its own message
		if ( $activation->is_expired() ) {
			return false;
		}

		return ! $activation->is_active();
	}

	/**
	 * @return Url
	 */
	private function get_license_page_url() {
		return $this->is_network_active
			? new Url\EditorNetwork( 'license' )
			: new Url\Editor( 'license' );
	}

	/**
	 * @return Url
	 */
	private function get_account_url() {
		return new Url\UtmTags( new Url\Site( Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'license-activation' );
	}

	/**
	 * @return string
	 */
	private function get_message_addon() {
		return sprintf(
			'%s %s',
			sprintf(
				__( "To enable add-ons, %s.", 'codepress_admin_columns' ),
				sprintf(
					"<a href='%s'>%s</a>",
					esc_url( $this->get_license_page_url()->get_url() ),
					__( 'enter your license key', 'codepress-admin-columns' )
				)
			),
			sprintf(
				__( 'You can find your license key on your %s.', 'codepress-admin-columns' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( $this->get_account_url()->get_url() ),
					__( 'account page', 'codepress-admin-columns' )
				)
			)
		);
	}

	/**
	 * @return string
	 */
	private function get_message() {
		return sprintf(
			'%s %s',
			sprintf(
				__( "To enable automatic updates for %s, <a href='%s'>enter your license key</a>.", 'codepress_admin_columns' ),
				'Admin Columns Pro',
				esc_url( $this->get_license_page_url()->get_url() )
			), sprintf(
				__( 'You can find your license key on your %s.', 'codepress-admin-columns' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( $this->get_account_url()->get_url() ),
					__( 'account page', 'codepress-admin-columns' )
				)
			)
		);
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

		exit;
	}

}