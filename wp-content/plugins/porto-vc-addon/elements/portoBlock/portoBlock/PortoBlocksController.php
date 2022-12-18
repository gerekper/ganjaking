<?php
namespace porto\portoBlock;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
class PortoBlocksController extends Container implements Module {
	use EventsFilters;

	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_BLOCKS_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoBlock',
				'getBlocks'
			);
			define( 'VCV_PORTO_BLOCKS_CONTROLLER', true );
		}
	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getBlocks( $variables, $payload ) {
		if ( function_exists( 'porto_get_post_type_items' ) ) {
			$blocks = porto_get_post_type_items(
				'porto_builder',
				array(
					'meta_query' => array(
						array(
							'key'     => 'porto_builder_type',
							'value'   => 'block',
						),
					)
				),
				false
			);
			$result = array(
				array(
					'label' => '',
					'value' => '',
				),
			);
			if ( ! empty( $blocks ) ) {
				foreach ( $blocks as $value => $key ) {
					$result[] = array(
						'label' => str_replace( '&amp;', '&', esc_js( $key ) ),
						'value' => esc_js( $value ),
					);
				}
				$variables[] = array(
					'key'   => 'portoBlocks',
					'value' => $result,
				);
			}
		}

		return $variables;
	}
}
