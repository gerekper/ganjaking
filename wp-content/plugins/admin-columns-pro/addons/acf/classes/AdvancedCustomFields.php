<?php

namespace ACA\ACF;

use AC;
use AC\Registerable;
use AC\Request;
use ACA\ACF\ConditionalFormatting\FieldFormattableFactory;
use ACA\ACF\FieldGroup;
use ACA\ACF\RequestHandler\MapLegacyListScreen;
use ACA\ACF\Search;
use ACA\ACF\Service\AddColumns;
use ACA\ACF\Service\ColumnSettings;
use ACA\ACF\Service\InitColumn;
use ACA\ACF\Service\Scripts;
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
			new Service\EditingFix(),
			new Service\LegacyColumnMapper(),
			new Service\RemoveDeprecatedColumnFromTypeSelector(),
			new AddColumns(
				new FieldRepository( new FieldGroup\QueryFactory() ),
				new FieldsFactory(),
				new ColumnFactory( $column_initiator )
			),
			new Scripts( $this->location ),
			new InitColumn( $column_initiator ),
			new ColumnSettings(),
			new RequestParser( $request_handler_factory ),
		];

		array_map( function ( Registerable $service ) {
			$service->register();
		}, $services );
	}

}