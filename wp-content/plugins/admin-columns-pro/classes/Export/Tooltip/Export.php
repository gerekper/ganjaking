<?php

namespace ACP\Export\Tooltip;

use AC\Admin\Tooltip;

class Export extends Tooltip {

	public function __construct( $id ) {
		parent::__construct(
			"export-" . $id,
			array(
				'title'      => __( 'Export', 'codepress-admin-columns' ),
				'content'    => $this->get_tooltip_content(),
				'link_label' => '<img src="' . AC()->get_url() . 'assets/images/question.svg" alt="?" class="ac-setting-input__info">',
				'position'   => 'right',
			)
		);
	}

	/**
	 * @return string
	 */
	private function get_tooltip_content() {
		ob_start();
		?>
		<p><?php _e( 'Export for Admin Columns Pro, allows you to export your column content to CSV.', 'codepress-admin-columns' ); ?></p>
		<p><?php _e( 'On the overview, click on the export button and the columns on the overview will be exported to CSV.', 'codepress-admin-columns' ); ?></p>

		<?php

		return ob_get_clean();
	}

}