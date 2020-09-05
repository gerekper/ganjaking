<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Snippets_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var string $snippet_id
 */
$snippet_id = $this->arguments[0];

$post_id = absint( Snippets_Model::get_post_id_by_snippet_id( $snippet_id ) );

if ( ! is_string( get_post_status( $post_id ) ) ) {
	return;
}

$link = sprintf(
	'<a class="wpb-rs-modalwindow-menu-item" href="%s" data-post_id="%d">%s</a>',
	get_edit_post_link( $post_id ),
	$post_id,
	get_the_title( $post_id )
);

printf(
	_x(
		'You have selected this property as overridable. However it references to another global snippet and cannot be edited here. Please click the following link in order to edit it now: %s',
		'%s is the link (with post title) to the global snippet',
		'rich-snippets-schema'
	),
	$link
);
