<?php
declare( strict_types=1 );

namespace ACP\Service;

use AC\IntegrationRepository;
use AC\Message;
use AC\Registerable;
use AC\Type\Url\Documentation;

class PluginNotice implements Registerable {

	/**
	 * @var IntegrationRepository
	 */
	private $integration_repository;

	public function __construct( IntegrationRepository $integration_repository ) {
		$this->integration_repository = $integration_repository;
	}

	public function register() {
		$integrations = $this->integration_repository->find_all();

		$message = sprintf( __( 'This integration add-on is no longer required by %s and can be safely removed.', 'codepress-admin-columns' ), 'Admin Columns Pro' );
		$message .= sprintf( ' <a target="_blank" href="%s">%s</a>', Documentation::create_with_path( Documentation::ARTICLE_RELEASE_6 ), 'Learn more &raquo;' );

		foreach ( $integrations as $integration ) {
			$notice = new Message\Plugin(
				$message,
				$integration->get_basename(),
				Message::INFO
			);
			$notice->register();
		}
	}

}