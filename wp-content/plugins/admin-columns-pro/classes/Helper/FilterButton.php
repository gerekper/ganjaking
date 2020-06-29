<?php

namespace ACP\Helper;

use AC\Registrable;

abstract class FilterButton
	implements Registrable {

	/**
	 * @var string
	 */
	protected $screen;

	/**
	 * @var string
	 */
	protected $has_run = false;

	/**
	 * @param string $screen
	 */
	public function __construct( $screen ) {
		$this->screen = $screen;
	}

	/**
	 * Display filter button
	 */
	public function display_button() {
		if ( $this->has_run ) {
			return;
		}

		?>

		<input type="submit" name="acp_filter_action" class="button" value="<?php echo esc_attr( __( 'Filter', 'codepress-admin-columns' ) ); ?>">

		<?php

		$this->has_run = true;
	}

	/**
	 * @return callable
	 */
	protected function get_callback() {
		return [ $this, 'display_button' ];
	}

}