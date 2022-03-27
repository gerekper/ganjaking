<?php

namespace ACP\Admin\Page;

use AC\Admin\RenderableHead;
use AC\Asset;
use AC\Asset\Assets;
use AC\Asset\Location;
use AC\Asset\Style;
use AC\PluginInformation;
use AC\Renderable;
use AC\Type\Url;
use AC\View;
use ACP;
use ACP\Access\ActivationStorage;
use ACP\Access\PermissionsStorage;
use ACP\ActivationTokenFactory;
use ACP\LicenseKeyRepository;
use ACP\PluginRepository;
use ACP\Type\LicenseKey;
use ACP\Type\SiteUrl;

class License implements Asset\Enqueueables, Renderable, RenderableHead {

	const NAME = 'license';

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var Renderable
	 */
	private $head;

	/**
	 * @var SiteUrl
	 */
	private $site_url;

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
	 * @var LicenseKeyRepository
	 */
	private $license_key_repository;

	/**
	 * @var PluginRepository
	 */
	private $plugin_repository;

	/**
	 * @var bool
	 */
	private $network_active;

	public function __construct( Location\Absolute $location, Renderable $head, SiteUrl $site_url, ActivationTokenFactory $activation_token_factory, ActivationStorage $activation_storage, PermissionsStorage $permission_storage, LicenseKeyRepository $license_key_repository, PluginRepository $plugin_repository, $network_active ) {
		$this->location = $location;
		$this->head = $head;
		$this->site_url = $site_url;
		$this->activation_token_factory = $activation_token_factory;
		$this->activation_storage = $activation_storage;
		$this->permission_storage = $permission_storage;
		$this->license_key_repository = $license_key_repository;
		$this->plugin_repository = $plugin_repository;
		$this->network_active = (bool) $network_active;
	}

	public function render_head() {
		return $this->head;
	}

	public function get_assets() {
		return new Assets( [
			new Style( 'acp-license-manager', $this->location->with_suffix( 'assets/core/css/license-manager.css' ) ),
			new ACP\Asset\Script\LicenseManager( $this->location->with_suffix( 'assets/core/js/license-manager.js' ) ),
		] );
	}

	/**
	 * @param string $plugin_name
	 *
	 * @return ACP\Type\Url\Changelog
	 */
	private function get_changelog_url( $plugin_name ) {
		return new ACP\Type\Url\Changelog( $this->network_active, $plugin_name );
	}

	private function show_render_section_updates() {

		// update section is hidden on subsites
		if ( is_multisite() && ! is_network_admin() ) {
			return false;
		}

		return true;
	}

	public function render() {
		if ( $this->network_active && ! is_network_admin() ) {
			return $this->render_network_message();
		}

		$view = new View( [
			'section_license' => $this->render_license_section(),
			'section_updates' => $this->show_render_section_updates() ? $this->render_section_updates() : '',
		] );

		return $view->set_template( 'admin/page/license' );
	}

	private function render_section_updates() {
		$content = '';

		$updates_available = false;
		$updates_available_with_package = false;

		foreach ( $this->plugin_repository->find_all()->all() as $plugin ) {
			$content .= $this->render_section_update( $plugin )->render();

			if ( $plugin->has_update() ) {
				$updates_available = true;

				if ( $plugin->get_update()->has_package() ) {
					$updates_available_with_package = true;
				}
			}
		}

		$has_token = null !== $this->activation_token_factory->create();
		$has_update_permission = $this->permission_storage->retrieve()->has_updates_permission();
		$show_update_now_button = $has_token && $updates_available_with_package && $has_update_permission;

		$view = new View( [
			'title'                      => __( 'Updates', 'codepress-admin-columns' ),
			'content'                    => $content,
			'button_update_now'          => $show_update_now_button,
			'button_update_now_disabled' => ! $show_update_now_button && $updates_available,
			'button_check_for_updates'   => ! $updates_available,
		] );

		return $view->set_template( 'admin/section-updates' );
	}

	private function get_update_link( $basename ) {
		$url = add_query_arg(
			[
				'action' => 'upgrade-plugin',
				'plugin' => $basename,
			],
			self_admin_url( 'update.php' )
		);

		return wp_nonce_url( $url, sprintf( 'upgrade-plugin_%s', $basename ) );
	}

	private function render_section_update( PluginInformation $plugin ) {
		$can_be_updated = $plugin->has_update() && $plugin->get_update()->has_package() && current_user_can( 'update_plugins' );

		$view = new View( [
			'plugin_update_link' => $can_be_updated ? $this->get_update_link( $plugin->get_basename() ) : null,
			'plugin_label'       => $plugin->get_name(),
			'current_version'    => $plugin->get_version()->get_value(),
			'available_version'  => $plugin->has_update() ? $plugin->get_update()->get_version()->get_value() : null,
			'changelog_link'     => $this->get_changelog_url( $plugin->get_dirname() )->get_url(),
		] );

		return $view->set_template( 'admin/section-update' );
	}

	private function get_inline_notice() {
		$permissions = $this->permission_storage->retrieve();

		$description = null;

		if ( ! $permissions->has_usage_permission() ) {
			$description = __( 'Enter your license code to receive automatic updates.', 'codepress-admin-columns' );
		}

		if ( ! $permissions->has_updates_permission() ) {
			$description = sprintf(
				__( 'Enter your license key to %s.', 'codepress-admin-columns' ),
				sprintf(
					'<strong>%s</strong>',
					__( 'unlock all settings', 'codepress-admin-columns' )
				)
			);
		}

		return $description
			? sprintf( '%s %s', ac_helper()->icon->dashicon( [ 'icon' => 'info-outline', 'class' => 'orange' ] ), $description )
			: null;
	}

	private function render_network_message() {
		$page = __( 'network settings page', 'codepress-admin-columns' );

		if ( current_user_can( 'manage_network_options' ) ) {
			$url = new Url\EditorNetwork( 'license' );

			$page = sprintf( '<a href="%s">%s</a>', $url->get_url(), $page );
		}

		$content = sprintf(
			__( 'The license can be managed on the %s.', 'codepress-admin-columns' ),
			$page
		);

		$inline_notice = $this->get_inline_notice();

		if ( $inline_notice ) {
			$content = sprintf( '%s %s', $inline_notice, $content );
		}

		$view = new View( [
			'title'   => __( 'License', 'codepress-admin-columns' ),
			'content' => sprintf( '<p>%s</p>', $content ),
			'class'   => '-license',
		] );

		return $view->set_template( 'admin/page/settings-section' );
	}

	private function render_license_section() {
		$account_url = new Url\UtmTags( new Url\Site( Url\Site::PAGE_ACCOUNT_SUBSCRIPTIONS ), 'license-activation' );

		$license_key = $this->license_key_repository->find();

		if ( $license_key ) {
			$account_url->add( [
				'subscription_key' => $license_key->get_token(),
				'site_url'         => $this->site_url->get_url(),
			] );
		}

		$activation_token = $this->activation_token_factory->create();
		$activation = $activation_token
			? $this->activation_storage->find( $activation_token )
			: null;

		$permissions = $this->permission_storage->retrieve();
		$is_expired = $activation && $activation->is_expired();

		// Give auto-renewal 2 extra days before marked as expired
		if ( $is_expired && $activation->is_auto_renewal() && $activation->get_expiry_date()->get_expired_seconds() < ( 2 * DAY_IN_SECONDS ) ) {
			$is_expired = false;
		}

		$updates_enabled = $activation && $activation->is_active() && ! $is_expired && $permissions->has_updates_permission();

		$license_info = new View( [
			'nonce_field'                     => ( new ACP\Nonce\LicenseNonce() )->create_field(),
			'updates_disabled'                => ! $updates_enabled,
			'updates_enabled'                 => $updates_enabled,
			'is_expired'                      => $is_expired,
			'expiry_date'                     => $activation && $activation->get_expiry_date()->exists() ? ac_format_date( 'F j, Y', $activation->get_expiry_date()->get_value()->getTimestamp() ) : false,
			'is_cancelled'                    => $activation && $activation->is_cancelled(),
			'is_active'                       => $activation && $activation->is_active(),
			'is_license_defined'              => $license_key && LicenseKey::SOURCE_CODE === $license_key->get_source(),
			'license_key'                     => $license_key ? $license_key->get_token() : false,
			'has_activation'                  => null !== $activation,
			'has_usage_permission'            => $permissions->has_usage_permission(),
			'my_account_link'                 => $account_url->get_url(),
			'subscription_documentation_link' => ( new Url\Documentation( Url\Documentation::ARTICLE_SUBSCRIPTION_QUESTIONS ) ),
		] );

		$view = new View( [
			'title'   => __( 'License', 'codepress-admin-columns' ),
			'content' => $license_info->set_template( 'admin/section-license' ),
			'class'   => '-license',
		] );

		return $view->set_template( 'admin/page/settings-section' );
	}

}