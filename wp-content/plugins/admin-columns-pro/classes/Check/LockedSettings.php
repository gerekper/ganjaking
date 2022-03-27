<?php

namespace ACP\Check;

use AC;
use AC\Capabilities;
use AC\Message;
use AC\Message\Notice;
use AC\Registrable;
use AC\Screen;
use AC\Type\Url;
use ACP\Access\PermissionsStorage;
use ACP\Admin\Page;

class LockedSettings implements Registrable {

	/**
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * @var PermissionsStorage
	 */
	private $permission_storage;

	/**
	 * @var bool
	 */
	private $is_network_active;

	public function __construct( $plugin_basename, PermissionsStorage $permission_storage, $is_network_active ) {
		$this->plugin_basename = (string) $plugin_basename;
		$this->permission_storage = $permission_storage;
		$this->is_network_active = (bool) $is_network_active;
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'register_notice' ] );
	}

	/**
	 * @return Url
	 */
	private function get_license_page_url() {
		return $this->is_network_active
			? new Url\EditorNetwork( 'license' )
			: new Url\Editor( 'license' );
	}

	private function get_message() {
		return sprintf(
			'%s %s',
			sprintf( '%s is not yet activated.', 'Admin Columns Pro' ),
			sprintf(
				__( "Go to the %s and activate Admin Columns Pro to start using the plugin.", 'codepress_admin_columns' ),
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( $this->get_license_page_url()->get_url() ),
					__( 'license page', 'codepress_admin_columns' )
				)
			)
		);
	}

	private function get_inline_plugin_message() {
		return sprintf( '%s %s',
			sprintf(
				__( '%s is not yet activated, please %s.', 'codepress_admin_columns' ),
				'Admin Columns Pro',
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( $this->get_license_page_url()->get_url() ),
					__( 'enter your license key', 'codepress_admin_columns' )
				)
			),
			$this->get_message_account_page()
		);
	}

	private function missing_usage_permission() {
		return ! $this->permission_storage->retrieve()->has_usage_permission();
	}

	private function get_account_url() {
		return new Url\UtmTags( new Url\Site( Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'license-activation' );
	}

	private function get_message_account_page() {
		return sprintf(
			__( 'You can find your license key on your %s.', 'codepress-admin-columns' ),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->get_account_url()->get_url() ),
				__( 'account page', 'codepress-admin-columns' )
			)
		);
	}

	private function get_license_page_message() {
		$documentation = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			( new Url\Documentation( Url\Documentation::ARTICLE_SUBSCRIPTION_QUESTIONS ) )->get_url(),
			sprintf( __( 'activating %s', 'codepress-admin-columns' ), 'Admin Columns Pro' )
		);

		$parts = [
			__( 'To start using Admin Columns Pro, fill in your license key below.', 'codepress-admin-columns' ),
			sprintf( __( 'Read more about %s.' ), $documentation ),
		];

		return implode( ' ', $parts );
	}

	private function get_addons_page_message() {
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
			$this->get_message_account_page()
		);
	}

	public function register_notice( Screen $screen ) {
		if ( ! current_user_can( Capabilities::MANAGE ) || ! $screen->has_screen() ) {
			return;
		}

		switch ( true ) {
			case $screen->is_plugin_screen() && $this->missing_usage_permission() :
				$notice = new Message\Plugin(
					$this->get_inline_plugin_message(),
					$this->plugin_basename,
					Message::WARNING
				);

				$notice->register();
				break;
			case $screen->is_admin_screen( Page\License::NAME ) && $this->missing_usage_permission() :
				$notice = new Notice(
					$this->get_license_page_message(),
					Message::ERROR
				);

				$notice->register();
				break;
			case $screen->is_admin_screen( Page\Addons::NAME ) && $this->missing_usage_permission() :
				$notice = new Notice(
					$this->get_addons_page_message(),
					Message::ERROR
				);

				$notice->register();
				break;
			case ( $screen->is_admin_screen( AC\Admin\Page\Columns::NAME ) || $screen->is_admin_screen( Page\Tools::NAME ) || $screen->is_admin_screen( AC\Admin\Page\Settings::NAME ) ) && $this->missing_usage_permission() :
				$notice = new Notice(
					$this->get_message(),
					Message::ERROR
				);

				$notice->register();
				break;
		}
	}

}