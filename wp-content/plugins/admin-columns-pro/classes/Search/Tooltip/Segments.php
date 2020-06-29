<?php
namespace ACP\Search\Tooltip;

use AC\Admin\Tooltip;

class Segments extends Tooltip {

	public function __construct() {
		parent::__construct(
			'segments',
			array(
				'content' => $this->get_content()
			)
		);
	}

	private function get_content() {
		ob_start();
		?>

		<p>
			<?php _e( 'Save a set of custom smart filters for later use.', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<?php _e( 'This can be useful to group your WordPress content based on different criteria. Click on a segment in the list to load the segmented list.', 'codepress-admin-columns' ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

}