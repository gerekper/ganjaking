<?php

function wcbo_update_110() {
	$updater = WCBO()->components->updater;

	$updater->install_my_ticket_page();
	$updater->install_default_settings();
	$updater->update_version( '1.1.0' );
}
