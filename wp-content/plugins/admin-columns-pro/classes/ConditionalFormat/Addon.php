<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat;

use AC\Asset\Location;
use AC\Registerable;
use ACP\ConditionalFormat\Service;
use ACP\ConditionalFormat\Settings\ListScreen\HideOnScreenFactory;

final class Addon implements Registerable {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var RulesRepositoryFactory
	 */
	private $rules_repository_factory;

	/**
	 * @var HideOnScreenFactory
	 */
	private $hide_on_screen_factory;

	public function __construct(
		Location\Absolute $location,
		RulesRepositoryFactory $rules_repository_factory,
		HideOnScreenFactory $hide_on_screen_factory
	) {
		$this->location = $location;
		$this->rules_repository_factory = $rules_repository_factory;
		$this->hide_on_screen_factory = $hide_on_screen_factory;
	}

	public function register(): void {
		$operators = new Operators();

		$services = [
			new Service\Assets(
				$this->location,
				$operators,
				$this->rules_repository_factory,
				$this->hide_on_screen_factory
			),
			new Service\Formatter( $operators, $this->rules_repository_factory ),
			new Service\ListScreenSettings( $this->hide_on_screen_factory ),
			new Service\Storage( $this->rules_repository_factory ),
		];

		foreach ( $services as $service ) {
			$service->register();
		}
	}

}