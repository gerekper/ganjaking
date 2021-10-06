<?php

namespace ACP\Check;

use AC\Admin\Page\Addons;
use AC\Capabilities;
use AC\Integration;
use AC\Integration\Filter;
use AC\IntegrationRepository;
use AC\Integrations;
use AC\Message;
use AC\Registrable;
use AC\Screen;

class RecommendedAddons
	implements Registrable {

	/**
	 * @var IntegrationRepository
	 */
	private $integration_repository;

	public function __construct( IntegrationRepository $integration_repository ) {
		$this->integration_repository = $integration_repository;
	}

	public function register() {
		add_action( 'ac/screen', [ $this, 'register_notice' ] );
	}

	private function get_recommended_addons() {
		return $this->integration_repository->find_all( [
			IntegrationRepository::ARG_FILTER => [
				new Filter\IsNotActive( is_multisite(), is_network_admin() ),
				new Filter\IsPluginActive(),
			],
		] );
	}

	/**
	 * @param Screen $screen
	 */
	public function register_notice( Screen $screen ) {
		if ( ! $screen->has_screen() || ! current_user_can( Capabilities::MANAGE ) || ! $screen->is_admin_screen( Addons::NAME ) ) {
			return;
		}

		$recommended_addons = $this->get_recommended_addons();

		if ( ! $recommended_addons->exists() ) {
			return;
		}

		$notice = new Message\Notice( $this->get_message( $recommended_addons ) );
		$notice
			->set_type( Message::INFO )
			->register();
	}

	/**
	 * @return string
	 */
	private function get_message( Integrations $integrations ) {

		$titles = array_map( static function ( Integration $integration ) {
			return sprintf( '<strong>%s</strong>', $integration->get_title() );
		}, $integrations->all() );

		return sprintf(
			'%s %s',
			_n( 'We recommend installing this integration add-on:', 'We recommend installing these integration add-ons:', $integrations->count(), 'codepress-admin-columns' ),
			ac_helper()->string->enumeration_list( $titles, 'and' )
		);
	}

}