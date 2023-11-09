<?php

namespace Smush\Core\Webp;

use Smush\Core\Stats\Media_Item_Optimization_Global_Stats_Persistable;

class Webp_Optimization_Global_Stats_Persistable extends Media_Item_Optimization_Global_Stats_Persistable {
	const GLOBAL_STATS_OPTION_ID = 'wp-smush-webp-global-stats';

	public function __construct() {
		parent::__construct( self::GLOBAL_STATS_OPTION_ID );
	}

	public function save() {
		// Doing nothing. Since we don't keep stats for individual media items we can't
	}
}