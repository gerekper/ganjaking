<?php

namespace GFML\Compatibility\FeedAddon;

class Value {
	/** @var string */
	private $value;

	/** @var string */
	private $name;

	/** @var string */
	private $title;

	/** @var string */
	private $kind;

	/**
	 * A value that should be registered for translation
	 *
	 * @param string $value
	 * @param string $name
	 * @param string $title
	 * @param string $kind
	 */
	public function __construct( $value, $name, $title, $kind ) {
		$this->value = $value;
		$this->name  = $name;
		$this->title = $title;
		$this->kind  = $kind;
	}

	/** @return string */
	public function getStringValue() {
		return $this->value;
	}

	/**
	 * @param string $prefix
	 * @return string
	 */
	public function getStringName( $prefix ) {
		return "$prefix-$this->name";
	}

	/** @return string */
	public function getStringTitle() {
		return $this->title;
	}

	/** @return string */
	public function getStringKind() {
		return $this->kind;
	}
}
