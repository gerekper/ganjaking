<?php

namespace ACP\Transient;

use AC\Transient;
use ACP\Type;

class PlatformCheckTransient extends Transient {

	public function __construct( Type\SiteId $site_id, $network_only ) {
		parent::__construct( 'acp_platform_check_' . $site_id->get_hash(), $network_only );
	}

}