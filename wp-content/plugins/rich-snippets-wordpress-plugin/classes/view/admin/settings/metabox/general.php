<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

printf( '<form method="post" action="%s">', esc_url( admin_url( 'options.php' ) ) );

settings_fields( 'rich-snippets-settings' );

do_settings_sections( Admin_Controller::instance()->menu_settings_hook );

submit_button( null, 'primary', 'submit', false );

echo '</form>';
