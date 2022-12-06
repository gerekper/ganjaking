<?php

namespace ACA\WC\Helper\Select\Formatter;

use AC;
use WP_Post;

class ProductIDTitleAndSKU extends AC\Helper\Select\Formatter {

	/**
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_label( $post ) {
		$label = '#' . $post->ID;
		$title = $post->post_title;
		$sku = get_post_meta( $post->ID, '_sku', true );

		if ( $title ) {
			$label .= ' ' . $title;
		}

		if ( $sku ) {
			$label .= sprintf( ' (%s)', $sku );
		}

		return $label;
	}

}