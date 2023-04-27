<?php

function ACP(): ACP\AdminColumnsPro {
	return ACP\AdminColumnsPro::instance();
}

function acp_support_email(): string {
	return 'support@admincolumns.com';
}

/**
 * @deprecated 6.0
 * @since      5.1
 */
function acp_sorting_show_all_results(): bool {
	_deprecated_function( __FUNCTION__, '6.0' );

	return true;
}