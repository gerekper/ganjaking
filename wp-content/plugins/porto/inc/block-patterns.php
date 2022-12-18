<?php
/**
 * Porto: Block Patterns
 *
 * @since 6.5.0
 */

/**
 * Registers block patterns and categories.
 *
 * @since 6.5.0
 *
 * @return void
 */
function porto_register_block_patterns() {
	$block_pattern_categories = array(
		'footer' => array( 'label' => __( 'Footers', 'porto' ) ),
	);

	/**
	 * Filters the block pattern categories.
	 *
	 * @since 6.5.0
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'porto_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

	$block_patterns = array(
		'footer',
		'header',
	);

	/**
	 * Filters the theme block patterns.
	 *
	 * @since 6.5.0
	 *
	 * @param array $block_patterns List of block patterns by name.
	 */
	$block_patterns = apply_filters( 'porto_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		$pattern_file = get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' );

		register_block_pattern(
			'porto/' . $block_pattern,
			require $pattern_file
		);
	}
}
add_action( 'init', 'porto_register_block_patterns', 9 );
