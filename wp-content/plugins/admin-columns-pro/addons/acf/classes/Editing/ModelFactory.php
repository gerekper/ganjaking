<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACP;
use ACP\Editing\PaginatedOptions;

class ModelFactory implements EditingModelFactory {

	/**
	 * @var ModelViewFactory
	 */
	private $view_factory;

	/**
	 * @var StorageFactory
	 */
	private $storage_factory;

	public function __construct() {
		$this->view_factory = new ModelViewFactory();
		$this->storage_factory = new StorageFactory();
	}

	public function create( Field $field, Column $column ) {

		switch ( $field->get_type() ) {
			case FieldType::TYPE_BOOLEAN:
			case FieldType::TYPE_BUTTON_GROUP:
			case FieldType::TYPE_CHECKBOX:
			case FieldType::TYPE_COLOR_PICKER:
			case FieldType::TYPE_EMAIL:
			case FieldType::TYPE_LINK:
			case FieldType::TYPE_IMAGE:
			case FieldType::TYPE_FILE:
			case FieldType::TYPE_NUMBER:
			case FieldType::TYPE_PASSWORD:
			case FieldType::TYPE_RADIO:
			case FieldType::TYPE_RANGE:
			case FieldType::TYPE_TEXT:
			case FieldType::TYPE_TIME_PICKER:
			case FieldType::TYPE_TEXTAREA:
			case FieldType::TYPE_URL:
			case FieldType::TYPE_OEMBED:
			case FieldType::TYPE_GALLERY:
			case FieldType::TYPE_WYSIWYG:
				return new ACP\Editing\Service\Basic(
					$this->view_factory->create( $field ),
					$this->storage_factory->create( $column )
				);

			case FieldType::TYPE_SELECT:
				$view = $this->view_factory->create( $field );
				$storage = $this->storage_factory->create( $column );

				return $field instanceof Field\Multiple && $field->is_multiple()
					? new Service\MultipleSelect( $view, $storage )
					: new ACP\Editing\Service\Basic( $view, $storage );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return new ACP\Editing\Service\DateTime(
					$this->view_factory->create( $field ),
					$this->storage_factory->create( $column )
				);

			case FieldType::TYPE_DATE_PICKER:
				return new ACP\Editing\Service\Date(
					$this->view_factory->create( $field ),
					$this->storage_factory->create( $column ),
					$field instanceof Field\SaveFormat ? $field->get_save_format() : 'Ymd'
				);

			case FieldType::TYPE_USER:
				$args = [];

				if ( $field instanceof Field\RoleFilterable && $field->has_roles() ) {
					$args['role__in'] = $field->get_roles();
				}

				$view = $this->view_factory->create( $field );
				$storage = $this->storage_factory->create( $column );
				$paginated = new PaginatedOptions\Users( $args );

				return $field instanceof Field\Multiple && $field->is_multiple()
					? new ACP\Editing\Service\Users( $view, $storage, $paginated )
					: new ACP\Editing\Service\User( $view, $storage, $paginated );

			case FieldType::TYPE_RELATIONSHIP:
				$tax_query = $field instanceof Field\TaxonomyFilterable
					? $this->get_related_tax_query( $field->get_taxonomies() )
					: [];

				return new ACP\Editing\Service\Posts(
					$this->view_factory->create( $field ),
					$this->storage_factory->create( $column ),
					new PaginatedOptions\Posts( $field instanceof Field\PostTypeFilterable ? $field->get_post_type() : [ 'any' ], [ 'tax_query' => $tax_query ] )
				);

			case FieldType::TYPE_POST:
			case FieldType::TYPE_PAGE_LINK:
				$tax_query = $field instanceof Field\TaxonomyFilterable
					? $this->get_related_tax_query( $field->get_taxonomies() )
					: [];

				$paginated = new PaginatedOptions\Posts( $field instanceof Field\PostTypeFilterable ? $field->get_post_type() : [ 'any' ], [ 'tax_query' => $tax_query ] );
				$view = $this->view_factory->create( $field );
				$storage = $this->storage_factory->create( $column );

				return $field instanceof Field\Multiple && $field->is_multiple()
					? new ACP\Editing\Service\Posts( $view, $storage, $paginated )
					: new ACP\Editing\Service\Post( $view, $storage, $paginated );

			case FieldType::TYPE_TAXONOMY:
				if ( ! $field instanceof Field\Type\Taxonomy ) {
					return false;
				}

				return $field->is_multiple()
					? new Service\Taxonomies( $field->get_taxonomy(), $this->storage_factory->create( $column ) )
					: new Service\Taxonomy( $field->get_taxonomy(), $this->storage_factory->create( $column ) );
		}

		return false;
	}

	private function get_related_tax_query( array $terms ): array {
		$tax_query = [ 'relation' => 'OR' ];

		foreach ( $terms as $term ) {
			$tax_query[] = [
				'taxonomy' => $term->taxonomy,
				'field'    => 'slug',
				'terms'    => $term->slug,
			];
		}

		return $tax_query;
	}

}