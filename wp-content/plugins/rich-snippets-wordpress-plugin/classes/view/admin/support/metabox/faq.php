<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$faq_posts = WPBuddy_Model::request(
	'/wp/v2/posts/?categories=10&per_page=5'
);

?>

<a href="<?php echo esc_url( admin_url( 'admin.php?page=rich-snippets-schema&tab=training#new_to_snip' ) ); ?>"
   class="button button-primary button-hero"><span
			class="dashicons dashicons-welcome-learn-more"></span><?php _e( 'Take the Structured Data Training', 'rich-snippets-schema' ); ?>
</a>

<a href="<?php echo esc_url( admin_url( 'admin.php?page=rich-snippets-schema&tab=training#work_with_snip' ) ); ?>"
   class="button button-primary button-hero"><span
			class="dashicons dashicons-admin-plugins"></span><?php _e( 'Learn how to work with SNIP', 'rich-snippets-schema' ); ?>
</a>

<hr/>

<form class="wpb-rs-support-faq-search">
	<input class="wpb-rs-support-faq-search-input" type="search"
		   placeholder="<?php _e( 'Start typing to search ...', 'rich-snippets-schema' ); ?>"/>
	<a class="button button-hero wpb-rs-support-faq-search-button"
	   href="#"><?php esc_attr_e( 'Search', 'rich-snippets-schema' ); ?></a>
</form>
<ul class="wpb-rs-support-faq-results">
	<?php
	if ( is_wp_error( $faq_posts ) ) {
		_e( 'Could not fetch latest FAQ entries from rich-snippets.io', 'rich-snippets-schema' );
	} else {
		foreach ( $faq_posts as $faq_post ) {
			printf(
				'<li><a href="%s" target="_blank">%s</a><p class="description">%s</p></li>',
				esc_url( $faq_post->link ),
				strip_tags( $faq_post->title->rendered ),
				wp_trim_words( strip_tags( $faq_post->excerpt->rendered ) )
			);
		}
	}
	?>
</ul>
