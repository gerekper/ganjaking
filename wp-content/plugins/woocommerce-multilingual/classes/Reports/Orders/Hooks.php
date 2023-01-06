<?php

namespace WCML\Reports\Orders;

use WCML\Utilities\Resources;
use WCML\Rest\Functions;

class Hooks implements \IWPML_Backend_Action {

	public function add_hooks() {
		if ( Functions::isAnalyticsPage() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
		}
	}

	public function enqueueAssets() {
		$enqueue = Resources::enqueueApp( 'reportsOrders' );

		$enqueue(
			[
				'name' => 'wcmlReports',
				'data' => [
					'strings' => [
						'languageLabel' => __( 'Language', 'woocommerce-multilingual' ),
					],
				],
			]
		);
	}

}
