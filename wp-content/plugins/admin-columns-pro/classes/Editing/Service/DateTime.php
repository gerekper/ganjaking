<?php

namespace ACP\Editing\Service;

use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\Value\Data;
use ACP\Editing\View;
use DateTime as PhpDateTime;

class DateTime implements Service {

	const FORMAT = 'Y-m-d H:i:s';

	/**
	 * @var View\DateTime
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

	public function get_view( string $context ): ?View {
		return $this->view;
	}

	public function update( int $id, $data ): void {
		if ( $data ) {
			$data = PhpDateTime::createFromFormat( self::FORMAT, $data )->format( $this->date_format );
		}

		$this->storage->update( $id, $data );
	}

	public function get_value( int $id ) {
		$value = $this->storage->get( $id );

		if ( ! $value ) {
			return false;
		}

		$date = PhpDateTime::createFromFormat( $this->date_format, $value );

		return $date
			? $date->format( self::FORMAT )
			: false;
	}

}