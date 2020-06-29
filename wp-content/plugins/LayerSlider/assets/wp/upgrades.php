<?php

$upgrades = array(

	// !!!!!!!!!
	// IMPORTANT: Add new entries at the *END* of this array.
	// !!!!!!!!!

	'6.11.0' => function() {
		update_option( 'ls_gsap_sandboxing', 1 );
	}
);