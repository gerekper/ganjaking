<?php

namespace ACA\YoastSeo\Service;

use AC;
use AC\Registerable;
use ACA\YoastSeo\Column;
use ACP;

final class Columns implements Registerable {

	public function register(): void {
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
	}

	public function add_columns( AC\ListScreen $list_screen ): void {

		switch ( true ) {
			case $list_screen instanceof AC\ListScreen\Post:
				$this->register_post_columns( $list_screen );

				break;
			case $list_screen instanceof ACP\ListScreen\Taxonomy:
				$this->register_taxonomy_columns( $list_screen );

				break;

			case $list_screen instanceof AC\ListScreen\User:
				$this->register_user_columns( $list_screen );

				break;
		}

	}

	private function register_post_columns( AC\ListScreen\Post $list_screen ): void {
		$classes = [
			new Column\Post\FacebookDescription(),
			new Column\Post\FacebookImage(),
			new Column\Post\FacebookTitle(),
			new Column\Post\FocusKeywordCount(),
			new Column\Post\FocusKW(),
			new Column\Post\IsIndexed(),
			new Column\Post\Linked(),
			new Column\Post\Links(),
			new Column\Post\MetaDesc(),
			new Column\Post\PrimaryTaxonomy(),
			new Column\Post\Readability(),
			new Column\Post\Score(),
			new Column\Post\Title(),
			new Column\Post\TwitterDescription(),
			new Column\Post\TwitterImage(),
			new Column\Post\TwitterTitle(),
			new Column\Post\TwitterTitle(),
		];

		foreach ( $classes as $class ) {
			$list_screen->register_column_type( new $class );
		}
	}

	private function register_taxonomy_columns( ACP\ListScreen\Taxonomy $list_screen ): void {
		$classes = [
			new Column\Taxonomy\CanonicalUrl(),
			new Column\Taxonomy\FocusKeyword(),
			new Column\Taxonomy\IncludeInSitemap(),
			new Column\Taxonomy\MetaDesc(),
			new Column\Taxonomy\MetaTitle(),
			new Column\Taxonomy\NoIndex(),
		];

		foreach ( $classes as $class ) {
			$list_screen->register_column_type( new $class );
		}
	}

	private function register_user_columns( AC\ListScreen\User $list_screen ): void {
		$classes = [
			new Column\User\AuthorPageMetaDesc(),
			new Column\User\AuthorPageTitle(),
			new Column\User\DisableReadabilityAnalysis(),
			new Column\User\DisableSeoAnalysis(),
			new Column\User\NoIndexAuthor(),
		];

		foreach ( $classes as $class ) {
			$list_screen->register_column_type( new $class );
		}
	}

}