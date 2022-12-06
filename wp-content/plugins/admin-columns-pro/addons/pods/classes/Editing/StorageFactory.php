<?php

namespace ACA\Pods\Editing;

use ACA\Pods\Editing;
use ACA\Pods\Field;

final class StorageFactory {

	public function create_by_field( Field $field ) {
		switch ( true ) {
			case $field instanceof Field\Date:
			case $field instanceof Field\Datetime:
				return new Editing\Storage\Date( $field->get_pod(), $field->get_field_name(), new Editing\Storage\Read\PodsRaw( $field->get_pod(), $field->get_field_name() ), $field->get_option( 'date_format' ) );

			case $field instanceof Field\Pick\NavMenu:
			case $field instanceof Field\Pick\PostFormat:
				return new Editing\Storage\Field( $field->get_pod(), $field->get_field_name(), new Editing\Storage\Read\DbRaw( $field->get_field_name(), $field->get_meta_type() ) );
			default:
				return new Editing\Storage\Field( $field->get_pod(), $field->get_field_name(), new Editing\Storage\Read\PodsRaw( $field->get_pod(), $field->get_field_name() ) );
		}

	}

}