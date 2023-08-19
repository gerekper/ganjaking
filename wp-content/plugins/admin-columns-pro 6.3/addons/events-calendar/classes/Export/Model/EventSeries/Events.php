<?php

namespace ACA\EC\Export\Model\EventSeries;

use ACP;
use TEC;

/**
 * Export Model for AllDayEvent column
 * @since 1.0.2
 */
class Events implements ACP\Export\Service {

	public function get_value( $id ): string {
		if ( ! class_exists( TEC\Events_Pro\Custom_Tables\V1\Repository\Events::class ) || ! function_exists( 'tribe' ) ) {
			return '';
		}

		return tribe( TEC\Events_Pro\Custom_Tables\V1\Repository\Events::class )->get_occurrence_count_for_series( $id );
	}

}