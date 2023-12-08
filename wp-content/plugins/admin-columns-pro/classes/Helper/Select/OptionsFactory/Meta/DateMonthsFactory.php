<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\OptionsFactory\Meta;

use AC\Helper\Select\Options;
use AC\Meta\Query;
use ACP\Helper\Select\OptionsFactory\DateOptionsFactory;
use DateTime;

class DateMonthsFactory implements DateOptionsFactory {

	private $date_format;

	private $query;

	public function __construct( string $date_format, Query $query ) {
		$this->date_format = $date_format;
		$this->query = $query;
	}

	public function create_label( string $value ): string {
		$date = DateTime::createFromFormat( 'Ym', $value );

		return $date ? $date->format( 'F Y' ) : '';
	}

	public function create_options( string $db_column ): Options {
		$options = [];

		foreach ( $this->query->get() as $date_string ) {
			$date = DateTime::createFromFormat( $this->date_format, $date_string );

			if ( ! $date ) {
				continue;
			}

			$options[ $date->format( 'Ym' ) ] = $date->format( 'F Y' );
		}

		krsort( $options );

		return Options::create_from_array( $options );
	}

}