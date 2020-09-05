<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<div class="rss-widget">
	<?php
	@wp_widget_rss_output(
		'https://rich-snippets.io/category/news/feed/',
		array(
			'show_author'  => 0,
			'show_date'    => true,
			'show_summary' => false,
			'items'        => 3,
		)
	);
	?>
</div>
<p>
	<a href="<?php echo esc_url( Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/category/news/', 'global-snippets-metabox' ) ); ?>"
	   class="button" target="_blank"><?php _e( 'More news', 'rich-snippets-schema' ); ?></a>
</p>
