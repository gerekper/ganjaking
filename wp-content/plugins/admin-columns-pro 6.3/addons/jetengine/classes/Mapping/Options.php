<?php

namespace ACA\JetEngine\Mapping;

final class Options {

	public static function from_glossary_options( array $options ) {
		$result = [];

		foreach ( $options as $option ) {
			$result[ $option['value'] ] = $option['label'];
		}

		return $result;
	}

	public static function from_field_options( array $options ) {
		$result = [];

		foreach ( $options as $option ) {
			$result[ $option['key'] ] = $option['value'];
		}

		return $result;
	}

}