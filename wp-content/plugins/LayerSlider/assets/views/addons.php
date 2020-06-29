<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$section = ! empty( $_GET['section'] ) ? $_GET['section'] : false;

switch( $section ) {

	case 'revisions':
		include LS_ROOT_PATH . '/views/revisions.php';
		break;

	default:
		include LS_ROOT_PATH . '/templates/tmpl-addons.php';
		break;
}