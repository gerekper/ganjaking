<?php

namespace ACP\Editing;

use AC\ListScreenRepository\Storage;
use AC\Request;
use ACP\Editing\RequestHandler\BulkDelete;
use ACP\Editing\RequestHandler\BulkSave;
use ACP\Editing\RequestHandler\DeleteUserSelectValues;
use ACP\Editing\RequestHandler\EditState;
use ACP\Editing\RequestHandler\InlineSave;
use ACP\Editing\RequestHandler\InlineValues;
use ACP\Editing\RequestHandler\SelectValues;
use LogicException;

class RequestHandlerAjaxFactory {

	private const METHOD_BULK_DELETE = 'bulk-delete';
	private const METHOD_BULK_SAVE = 'bulk-save';
	private const METHOD_INLINE_SAVE = 'inline-save';
	private const METHOD_INLINE_VALUES = 'inline-values';
	private const METHOD_EDIT_STATE = 'edit-state';
	private const METHOD_INLINE_SELECT_VALUES = 'inline-select-values';
	private const METHOD_BULK_SELECT_VALUES = 'bulk-select-values';
	private const METHOD_USER_SELECT_VALUES = 'delete-user-select-values';

	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function create( Request $request ): RequestHandler {
		switch ( $request->get( 'method' ) ) {
			case self::METHOD_BULK_DELETE :
				return new BulkDelete( $this->storage );
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
			case self::METHOD_USER_SELECT_VALUES :
				return new DeleteUserSelectValues();
		}

		throw new LogicException( 'Invalid request.' );
	}

}