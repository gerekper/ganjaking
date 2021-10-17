<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use DateTime;

class Date implements Service {

	const FORMAT = 'Y-m-d';

	/**
	 * @var View\Date
	 */
	private $view;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var string
	 */
	protected $date_format;

	public function __construct( View\Date $view, Storage $storage, $date_format = self::FORMAT ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->date_format = (string) $date_format;
	}

	public function get_view( $context ) {
		return $this->view;
	}

	public function get_value( $id ) {
		$value = DateTime::createFromFormat( $this->date_format, $this->storage->get( $id ) );

		return $value
			? $value->format( self::FORMAT )
			: false;
	}

	public function update( Request $request ) {
		$value = $request->get( 'value' );

		if ( $value ) {
			$date_time = DateTime::createFromFormat( 'U', ac_helper()->date->strtotime( $value ) );

			$value = $date_time
				? $date_time->format( $this->date_format )
				: false;
		}

		return $this->storage->update( $request->get( 'id' ), $value );
	}

}