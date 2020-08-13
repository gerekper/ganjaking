<?php

if ( apply_filters( 'ct_ultimate_gdpr_op_load', true ) ) {

	if ( apply_filters( 'ct_ultimate_gdpr_op_tgmpa_load', true ) ) {
		require_once __DIR__ . '/class-tgm-plugin-activation.php';
	}

	require_once __DIR__ . '/settings.php';
	require_once __DIR__ . '/class-ct-optimus-prime-plugin-update.php';

}
