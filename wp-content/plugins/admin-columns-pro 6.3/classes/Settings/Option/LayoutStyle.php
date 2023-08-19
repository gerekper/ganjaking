<?php

namespace ACP\Settings\Option;

use AC\Settings\Option;

class LayoutStyle extends Option {

	public const OPTION_TABS = 'tabs';
	public const OPTION_DROPDOWN = 'dropdown';

	public function __construct() {
		parent::__construct( 'layout_style' );
	}

}