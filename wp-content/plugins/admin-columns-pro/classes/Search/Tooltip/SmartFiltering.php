<?php

namespace ACP\Search\Tooltip;

use AC\Admin\Tooltip;

class SmartFiltering extends Tooltip {

	public function __construct( $id ) {
		parent::__construct(
			"smart-filtering-" . $id,
			array(
				'title'      => __( 'Smart Filtering', 'codepress-admin-columns' ),
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
		<p><?php echo _x( 'Smart filtering allows you to segment your data by different criteria.', 'smart filtering help', 'codepress-admin-columns' ); ?></p>
		<p><?php echo _x( 'Click on the <strong>Add Filter</strong> button and select a column and the criteria you want to filter on. You can add as many filters as you like. ', 'smart filtering help', 'codepress-admin-columns' ); ?></p>
		<img src="<?php echo esc_url( ACP()->get_url() ) . 'assets/core/'; ?>images/smart-filtering.png" alt="Smart filtering">
		<?php

		return ob_get_clean();
	}

}