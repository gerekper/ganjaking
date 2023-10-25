<?php

/**
 * Get data.
 *
 *
 * @return array Localized data.
 */
function seedprod_pro_fetch_inline_help_data() {
	return array(
		'category_articles' => seedprod_pro_fetch_articles(),
	);
}

// Action 
add_action( 'seedprod_pro_fetch_help_docs', 'seedprod_pro_update_help_articles' );

/**
 * Get docs from the cache.
 *
 * @since 1.6.3
 *
 * @return array Docs data.
 */
function seedprod_pro_fetch_articles() {
	// Get cache file.
	$upload_dir = wp_upload_dir();
	$path       = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-help-docs/'; // target directory.
	$cache_file = wp_normalize_path( trailingslashit( $path ) . 'articles.json' );

	if ( is_file( $cache_file ) && is_readable( $cache_file ) ) {
		$articles = json_decode( file_get_contents( $cache_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	clearstatcache();

	if ( empty( $articles ) ) {
		// This code should only when there are no articles available.
		// seedprod_pro_update_help_articles() should be triggered by schedule.
		$articles = seedprod_pro_update_help_articles();
	}

	// Store in class private variable for further use.
	$articles = ! empty( $articles ) ? $articles : array();

	return $articles;
}

/**
 * Update docs cache with actual data retrieved from the remote source.
 *
 * @since 1.6.3
 *
 * @return array|boolean Updated docs data. Or false on error.
 */
function seedprod_pro_update_help_articles() {
	// Fetch categories.
	$categories = seedprod_pro_fetch_categories();
	$articles   = array();

	// Loop over categories.
	if ( ! is_array( $categories ) ) {
		$categories = array();
	}

	if ( ! empty( $categories ) ) {
		// Loop over categories and get articles.
		$categories_length = count( $categories );

		for ( $i = 0; $i < $categories_length; $i++ ) {
			$current_category = $categories[ $i ];

			$request = wp_remote_get(
				add_query_arg( 'ht-kb-category', $current_category['id'], 'https://www.seedprod.com/wp-json/wp/v2/ht-kb' ),
				array()
			);

			if ( is_wp_error( $request ) ) {
				return false;
			}

			$content = wp_remote_retrieve_body( $request );

			// Attempt to decode the json data.
			$fetched_articles = json_decode( $content, true );

			$articles_array = array(
				'category_details' => array(),
			);

			// If the data successfully decoded to array we caching the content.
			if ( is_array( $fetched_articles ) ) {
				$articles_array['category_details'] = array(
					'id'            => $current_category['id'],
					'name'          => $current_category['name'],
					'slug'          => $current_category['slug'],
					'article_count' => $current_category['count'],
				);

				$fetched_articles_count   = count( $fetched_articles );
				$fetched_articles_cleaned = array();

				// Process fetched articles.
				for ( $a = 0; $a < $fetched_articles_count; $a++ ) {
					array_push(
						$fetched_articles_cleaned,
						array(
							'id'    => $fetched_articles[ $a ]['id'],
							'date'  => $fetched_articles[ $a ]['date'],
							'slug'  => $fetched_articles[ $a ]['slug'],
							'link'  => $fetched_articles[ $a ]['link'],
							'title' => $fetched_articles[ $a ]['title'],
						)
					);
				}

				// Set articles.
				$articles_array['articles'] = $fetched_articles_cleaned;

				// Push docs array to the main articles array.
				array_push( $articles, $articles_array );
			} else {
				$fetched_articles = array();
			}
		}
	} else {
		$articles = array();
	}

	// Add content to file & cache.
	if ( ! empty( $articles ) && is_array( $articles ) ) {
		// Set up upload file.
		$upload_dir = wp_upload_dir();
		$path       = trailingslashit( $upload_dir['basedir'] ) . 'seedprod-help-docs/'; // target directory.
		$cache_file = wp_normalize_path( trailingslashit( $path ) . 'articles.json' );

		// Add fresh contents to cache file.
		if ( true === seedprod_pro_set_up_upload_dir( $path, $cache_file ) ) {
			file_put_contents( $cache_file, wp_json_encode( $articles ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		}
	}

	return $articles;
}

/**
 * Create articles upload directory & create index file.
 *
 * @param string $path
 * @param string $cache_file
 * @return boolean|string
 */
function seedprod_pro_set_up_upload_dir( $path, $cache_file ) {
	try {
		// Check if directory exists. Create if it doesn't.
		if ( ! is_dir( $path ) ) {
			wp_mkdir_p( dirname( $cache_file ) );
		}

		// Create index file.
		$index_file = wp_normalize_path( trailingslashit( $path ) . 'index.html' );

		// Create empty index.html.
		if ( ! file_exists( $index_file ) ) {
			file_put_contents( $index_file, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		}

		return true;
	} catch( \Exception $ex ) {
		return $ex->getMessage();
	}
}

/**
 * Get categories.
 *
 * @since 1.6.3
 *
 * @return array Categories data.
 */
function seedprod_pro_fetch_categories() {
	// Fetch categories.
	$request = wp_remote_get( 'https://www.seedprod.com/wp-json/wp/v2/ht-kb-category' );

	if ( is_wp_error( $request ) ) {
		return false;
	}

	$content = wp_remote_retrieve_body( $request );

	// Attempt to decode the json data.
	$categories = json_decode( $content, true );

	// If the data successfully decoded to array we caching the content.
	if ( ! is_array( $categories ) ) {
		$categories = array();
	}

	return $categories;
}
