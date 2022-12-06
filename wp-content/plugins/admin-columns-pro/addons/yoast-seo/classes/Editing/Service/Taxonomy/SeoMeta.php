<?php

namespace ACA\YoastSeo\Editing\Service\Taxonomy;

use ACP\Editing;
use ACP\Editing\View;

class SeoMeta implements Editing\Service {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * @var Editing\View
	 */
	private $view;

	public function __construct( $taxonomy, $meta_key, Editing\View $view = null ) {
		$this->meta_key = $meta_key;
		$this->taxonomy = $taxonomy;
		$this->view = $view ?: new Editing\View\Text();
	}

	public function get_view( string $context ): ?View {
		return $this->view;
	}

	public function get_value( $id ) {
		$meta = get_option( 'wpseo_taxonomy_meta' );

		if ( ! is_array( $meta ) ) {
			return false;
		}

		return $meta[ $this->taxonomy ][ $id ][ $this->meta_key ] ?? false;
	}

	public function update( int $id, $data ): void {
		$meta = get_option( 'wpseo_taxonomy_meta' );

		if ( ! isset( $meta[ $this->taxonomy ] ) ) {
			$meta[ $this->taxonomy ] = [];
		}

		if ( ! isset( $meta[ $this->taxonomy ][ $id ] ) ) {
			$meta[ $this->taxonomy ][ $id ] = [];
		}

		$meta[ $this->taxonomy ][ $id ][ $this->meta_key ] = $data;

		update_option( 'wpseo_taxonomy_meta', $meta );
	}

}