<?php

namespace ACP\Editing\Tooltip;

use AC\Admin\Tooltip;

class BulkEditing extends Tooltip {

	public function __construct( $id ) {
		parent::__construct(
			"bulk-editing-" . $id,
			array(
				'title'         => __( 'Bulk Editing', 'codepress-admin-columns' ),
				'content'       => $this->get_tooltip_content(),
				'link_label'    => '<img src="' . AC()->get_url() . 'assets/images/question.svg" alt="?" class="ac-setting-input__info">',
				'position'      => 'right',
			)
		);
	}

	/**
	 * @return string
	 */
	private function get_tooltip_content() {
		ob_start();
		?>
		<p>
			<?php _e( 'Bulk Edit allows you to update multiple values at once.', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			1. <?php _e( 'Select more than one row from the overview to show the bulk edit buttons.', 'codepress-admin-columns' ); ?>
		</p>
		<img src="<?php echo esc_url( ACP()->get_url() ) . 'assets/editing/'; ?>images/bulk-edit-selection.png" alt="Bulk Edit" style="border:1px solid #ddd;">
		<p>
			2. <?php _e( 'Clicking that button will display a popup that allows you to add or change the current value of all selected items.', 'codepress-admin-columns' ); ?>
		</p>
		<img src="<?php echo esc_url( ACP()->get_url() ) . 'assets/editing/'; ?>images/bulk-edit.png" alt="Bulk Edit" style="border:1px solid #ddd;">
		<?php

		return ob_get_clean();
	}

}