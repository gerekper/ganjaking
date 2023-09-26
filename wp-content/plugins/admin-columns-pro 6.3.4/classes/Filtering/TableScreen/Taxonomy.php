<?php

namespace ACP\Filtering\TableScreen;

use ACP\Filtering\TableScreen;
use ACP\Helper\FilterButtonFactory;

class Taxonomy extends TableScreen {

	public function __construct( array $models, $assets ) {
		parent::__construct( $models, $assets );

		$filter_button = FilterButtonFactory::create( FilterButtonFactory::SCREEN_TAXONOMY );
		$filter_button->register();

		add_action( 'in_admin_footer', [ $this, 'render_markup' ], 1 );
	}

	public function render_markup() {
		if ( ! $this->get_active_filters() ) {
			return;
		}

		// We set the orderby, because filtering will only work for hierarchical taxonomies when there is no default sorting
		$orderby = ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'name';

		?>
		<div class="acp_tax_filters">

			<?php parent::render_markup(); ?>

			<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
		</div>
		<?php
	}

	private function get_active_filters() {
		$filters = [];

		foreach ( $this->models as $model ) {
			if ( $model->is_active() ) {
				$filters[] = $model;
			}
		}

		return $filters;
	}

}