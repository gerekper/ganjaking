<?php

namespace ACP\Export\Tooltip;

use AC\Admin\Tooltip;

class ExportDisabled extends Tooltip {

	public function __construct( $id ) {
		parent::__construct(
			"export-" . $id,
			array(
				'title'      => __( 'Export Unavailable', 'codepress-admin-columns' ),
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
		<p><?php _e( 'Unfortunately not every column can be exported.', 'codepress-admin-columns' ); ?></p>
		<p><?php _e( 'Third-party columns and some custom columns cannot be exported unless there is build-in support for that specific column.', 'codepress-admin-columns' ); ?></p>

		<?php

		return ob_get_clean();
	}

}