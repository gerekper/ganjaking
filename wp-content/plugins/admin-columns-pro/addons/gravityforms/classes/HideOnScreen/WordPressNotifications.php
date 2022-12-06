<?php

namespace ACA\GravityForms\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class WordPressNotifications extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_gf_wordpress_notices', __( 'WordPress Notifications', 'codepress-admin-columns' ) );
	}

}