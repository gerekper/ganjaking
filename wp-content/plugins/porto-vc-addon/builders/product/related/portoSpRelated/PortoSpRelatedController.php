<?php
namespace porto\portoSpRelated;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoSpRelatedController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	protected $edit_post    = null;
	protected $edit_product = null;

	public function __construct() {
		if ( defined( 'VCV_PORTO_SP_RELATED_CONTROLLER' ) ) {
			return null;
		}
		$this->addFilter(
			'vcv:editor:variables vcv:editor:variables/portoSpRelated',
			'getRelatedProducts'
		);
		define( 'VCV_PORTO_SP_RELATED_CONTROLLER', true );
	}
	private function restore_global_product_variable() {

		if ( ! $this->edit_product && ( is_singular( \PortoBuilders::BUILDER_SLUG ) || ( isset( $_REQUEST['vcv-action'] ) && 'frontend' == $_REQUEST['vcv-action'] ) ) ) {
			$query = new \WP_Query(
				array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'posts_per_page'      => 1,
					'numberposts'         => 1,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $query->have_posts() ) {
				$the_post           = $query->next_post();
				$this->edit_post    = $the_post;
				$this->edit_product = wc_get_product( $the_post );
			}
		}

		if ( $this->edit_product ) {
			global $post, $product;
			$post = $this->edit_post;
			setup_postdata( $this->edit_post );
			$product = $this->edit_product;
			return true;
		}

		return false;
	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getRelatedProducts( $variables, $payload ) {
		if ( ! is_product() && ! $this->restore_global_product_variable() && defined( 'VCV_PORTO_SP_RELATED_CONTROLLER' ) ) {
			return null;
		}

		global $product, $porto_settings;
		$related = wc_get_related_products( $product->get_id(), $porto_settings['product-related-count'] );

		if ( in_array( $product->get_id(), $related ) ) {
			$related = array_diff( $related, array( $product->get_id() ) );
		}
		$upsells = $product->get_upsell_ids();
		if ( in_array( $product->get_id(), $upsells ) ) {
			$upsells = array_diff( $upsells, array( $product->get_id() ) );
		}
		$variables[] = array(
			'key'   => 'portoRelated',
			'value' => $related,
		);
		$variables[] = array(
			'key'   => 'portoUpsell',
			'value' => $upsells,
		);
		if ( $this->edit_product ) {
			wp_reset_postdata();
		}
		return $variables;
	}
}
