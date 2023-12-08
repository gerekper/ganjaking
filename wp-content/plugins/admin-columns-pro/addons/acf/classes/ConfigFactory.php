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

	private function is_clone( string $column_type ): bool {
		return 0 === strpos( $column_type, CloneColumnFactory::CLONE_PREFIX );
	}

	private function is_group( string $column_type ): bool {
		return 0 === strpos( $column_type, GroupColumnFactory::GROUP_PREFIX );
	}

	public function create( string $column_type ): ?array {

		if ( $this->is_clone( $column_type ) ) {
			return $this->clone_prefixed_config->create( $column_type );
		}

		if ( $this->is_group( $column_type ) ) {
			return $this->group_config->create( $column_type );
		}

		return $this->default_config->create( $column_type );
	}

}