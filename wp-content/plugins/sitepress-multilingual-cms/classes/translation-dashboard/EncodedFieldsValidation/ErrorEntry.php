<?php

namespace WPML\TM\TranslationDashboard\EncodedFieldsValidation;

class ErrorEntry {
	/** @var int ID of post or package */
	public $elementId;

	/** @var string */
	public $elementTitle;

	/** @var array{title: string, content: string} */
	public $fields;

	/**
	 * @param int $elementId
	 * @param string $elementTitle
	 * @param array{title: string, content: string} $fields
	 */
	public function __construct( $elementId, $elementTitle, $fields ) {
		$this->elementId    = (int) $elementId;
		$this->elementTitle = $elementTitle;
		$this->fields       = $fields;
	}
}