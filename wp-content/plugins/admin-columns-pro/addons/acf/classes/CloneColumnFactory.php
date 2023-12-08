<?php

namespace ACA\ACF;

class CloneColumnFactory {

	public const CLONE_PREFIX = 'acfclone__';

	/**
	 * @var ColumnFactory
	 */
	private $column_factory;

	public function __construct( ColumnFactory $column_factory ) {
		$this->column_factory = $column_factory;
	}

	public function create( array $settings ): ?Column {
		$clone_setting = acf_get_field( $settings['__key'] );

		if ( $clone_setting['type'] === 'group' ) {
			return null;
		}

		// Seamless without prefix
		if ( $clone_setting['name'] === $settings['name'] ) {
			return $this->create_seamless_clone( $clone_setting, $settings['label'] );
		}

		$explode = explode( '_', $settings['key'] );

		// Grouped prefixed
		if ( count( $explode ) === 2 ) {
			$settings['key'] = $settings['_clone'] . '_' . $settings['key'];
		}

		return $this->create_prefixed_clone( $settings );
	}

	private function create_seamless_clone( array $clone_setting, $label ): ?Column {
		$clone_setting['key'] = $clone_setting['name'];
		$clone_setting['label'] = $label;

		return $this->column_factory->create( $clone_setting );
	}

	private function create_prefixed_clone( array $settings ): ?Column {
		$settings['key'] = self::CLONE_PREFIX . $settings['key'];

		foreach ( [ '_clone', '_name', '_valid', '__name', '__label', '__key' ] as $key ) {
			unset( $settings[ $key ] );
		}

		return $this->column_factory->create( $settings );
	}

}