<?php

namespace ACA\ACF;

use AC;
use AC\Registerable;
use AC\Request;
use ACA\ACF\ConditionalFormatting\FieldFormattableFactory;
use ACA\ACF\FieldGroup;
use ACA\ACF\ListScreenFactory\FieldGroupFactory;
use ACA\ACF\RequestHandler\MapLegacyListScreen;
use ACA\ACF\Search;
use ACA\ACF\Service;
use ACA\ACF\Sorting;
use ACP;
use ACP\RequestHandlerFactory;
use ACP\RequestParser;

final class AdvancedCustomFields implements Registerable {

	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		if ( ! class_exists( 'acf', false ) ) {
			return;
		}

		AC\ListScreenFactory::add( new FieldGroupFactory() );

		$column_initiator = new ColumnInstantiator(
			new ConfigFactory( new FieldFactory() ),
			new Search\ComparisonFactory(),
			new Sorting\ModelFactory(),
			new Editing\ModelFactory(),
			new Filtering\ModelFactory(),
			new FieldFormattableFactory()
		);

		$request_handler_factory = new RequestHandlerFactory( new Request() );
		$request_handler_factory->add( 'aca-acf-map-legacy-list-screen', new MapLegacyListScreen( AC()->get_storage() ) );

		$services = [
			new ACP\Service\IntegrationStatus( 'ac-addon-acf' ),
			new ColumnGroup(),
			new Service\ColumnSettings(),
			new Service\EditingFix(),
			new Service\LegacyColumnMapper(),
			new Service\ListScreens(),
			new Service\RemoveDeprecatedColumnFromTypeSelector(),
			new Service\AddColumns(
				new FieldRepository( new FieldGroup\QueryFactory() ),
				new FieldsFactory(),
				new ColumnFactory( $column_initiator )
			),
			new Service\Scripts( $this->location ),
			new Service\InitColumn( $column_initiator ),
			new RequestParser( $request_handler_factory ),
		];

		array_map( static function ( Registerable $service ) {
			$service->register();
		}, $services );
	}

}