<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var \WP_Post     $post
 * @var Rich_Snippet $snippet
 */
$post    = $this->arguments[0];
$snippet = $this->arguments[1];

?>

<div class="wpb-rs-single-snippet">
	<button class="button button-small wpb-rs-toggle-snippet">
		<span class="dashicons dashicons-arrow-up-alt2"></span>
	</button>
	<button class="button button-small wpb-rs-remove-snippet">
		<span class="dashicons dashicons-trash"></span>
	</button>
	<?php View::admin_snippets_metabox_snippet( $snippet, $post ); ?>
</div>
