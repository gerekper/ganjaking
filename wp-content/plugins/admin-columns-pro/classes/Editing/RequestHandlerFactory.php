<?php

namespace ACP\Editing;

use AC\ListScreenRepository\Storage;
use AC\Request;
use ACP\Editing\RequestHandler\BulkSave;
use ACP\Editing\RequestHandler\EditState;
use ACP\Editing\RequestHandler\InlineSave;
use ACP\Editing\RequestHandler\InlineValues;
use ACP\Editing\RequestHandler\SelectValues;
use LogicException;

class RequestHandlerFactory {

	const METHOD_BULK_SAVE = 'bulk-save';
	const METHOD_INLINE_SAVE = 'inline-save';
	const METHOD_INLINE_VALUES = 'inline-values';
	const METHOD_EDIT_STATE = 'edit-state';
	const METHOD_INLINE_SELECT_VALUES = 'inline-select-values';
	const METHOD_BULK_SELECT_VALUES = 'bulk-select-values';

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	/**
	 * @param Request $request
	 *
	 * @return RequestHandler
	 */
	public function create( Request $request ) {
		switch ( $request->get( 'method' ) ) {
			case self::METHOD_BULK_SAVE :
				return new BulkSave( $this->storage );
			case self::METHOD_INLINE_SAVE :
				return new InlineSave( $this->storage );
			case self::METHOD_INLINE_VALUES :
				return new InlineValues( $this->storage );
			case self::METHOD_EDIT_STATE :
				return new EditState( new Preference\EditState() );
			case self::METHOD_INLINE_SELECT_VALUES :
			case self::METHOD_BULK_SELECT_VALUES :
				return new SelectValues( $this->storage );
		}

		throw new LogicException( 'Invalid request.' );
	}

}