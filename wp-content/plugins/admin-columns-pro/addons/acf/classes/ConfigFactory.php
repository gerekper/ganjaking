<?php

namespace ACA\ACF;

class ConfigFactory {

	/**
	 * @var Configurable\ClonePrefixedField
	 */
	private $clone_prefixed_config;

	/**
	 * @var Configurable\Group
	 */
	private $group_config;

	/**
	 * @var Configurable\Column
	 */
	private $default_config;

	public function __construct( FieldFactory $field_factory ) {
		$this->clone_prefixed_config = new Configurable\ClonePrefixedField( $field_factory );
		$this->group_config = new Configurable\Group( $field_factory );
		$this->default_config = new Configurable\Column( $field_factory );
	}

	/**
	 * @param string $column
	 *
	 * @return bool
	 */
	private function is_clone( $column_type ) {
		return 0 === strpos( $column_type, CloneColumnFactory::CLONE_PREFIX );
	}

	/**
	 * @param string $column
	 *
	 * @return bool
	 */
	private function is_group( $column_type ) {
		return 0 === strpos( $column_type, GroupColumnFactory::GROUP_PREFIX );
	}

	/**
	 * @param string $column_type
	 *
	 * @return array|null
	 */
	public function create( $column_type ) {

		if ( $this->is_clone( $column_type ) ) {
			return $this->clone_prefixed_config->create( $column_type );
		}

		if ( $this->is_group( $column_type ) ) {
			return $this->group_config->create( $column_type );
		}

		return $this->default_config->create( $column_type );
	}

}