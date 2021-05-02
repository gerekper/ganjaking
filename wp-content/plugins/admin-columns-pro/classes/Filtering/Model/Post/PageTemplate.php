<?php

namespace ACP\Filtering\Model\Post;

use AC;
use ACP\Filtering\Model;

/**
 * @property AC\Column\Post\PageTemplate $column
 */
class PageTemplate extends Model\Meta {

	public function __construct( AC\Column\Post\PageTemplate $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = [
			'key'   => $this->column->get_meta_key(),
			'value' => $this->get_filter_value(),
		];

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		if ( $values = $this->get_meta_values() ) {
			$page_templates = $this->column->get_page_templates();

			foreach ( $values as $page_template ) {
				$label = array_search( $page_template, $page_templates );

				if ( $page_template === 'default' ) {
					$data['options'][ $page_template ] = apply_filters( 'default_page_template_title', __( 'Default Template' ), 'acp-filtering' );

					continue;
				}

				$data['options'][ $page_template ] = $label ?: $page_template;
			}
		}

		return $data;
	}
}