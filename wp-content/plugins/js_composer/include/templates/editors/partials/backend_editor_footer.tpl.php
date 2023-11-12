<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var Vc_Backend_Editor $editor */

vc_include_template( 'editors/partials/footer.tpl.php',
	[
		'editor' => $editor,
	]
);

// [shortcode edit layout]
vc_include_template( 'editors/partials/backend-shortcodes-templates.tpl.php' );
