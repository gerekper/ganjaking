<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use DateTime as PhpDateTime;

class DateTime implements Service {

	const FORMAT = 'Y-m-d H:i:s';

	/**
	 * @var View
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

	public function __construct( View\DateTime $view, Storage $storage, $date_format = self::FORMAT ) {
		$this->view = $view;
		$this->storage = $storage;
		$this->date_format = (string) $date_format;
	}

	public function get_view( $context ) {
		return $this->view;
	}

	public function get_value( $id ) {
		$value = $this->storage->get( $id );

		if ( ! $value ) {
			return false;
		}

		$date = PhpDateTime::createFromFormat( $this->date_format, $value );

		return $date ? $date->format( self::FORMAT ) : false;
	}

	public function update( Request $request ) {
		$value = $request->get( 'value' );

		$formattedValue = $value
			? PhpDateTime::createFromFormat( self::FORMAT, $value )->format( $this->date_format )
			: $value;

		return $this->storage->update( $request->get( 'id' ), $formattedValue );
	}

}