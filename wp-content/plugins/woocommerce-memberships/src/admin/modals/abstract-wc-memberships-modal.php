<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract modal class.
 *
 * Child implementations should, at a minimum, assign a new ID and implement the `get_template_body()`.
 * The base modal will automatically output the modal HTML template in the admin footer when instantiated.
 *
 * @since 1.9.0
 */
abstract class WC_Memberships_Modal {


	/** @var string modal identifier */
	protected $id = 'wc-memberships-modal';

	/** @var string the modal main title */
	protected $title = '';

	/** @var string the modal main button label */
	protected $action_button_label = '';

	/** @var string the modal main button CSS class */
	protected $action_button_class = 'button-primary';

	/** @var string legacy property, should use $action_button_label */
	protected $button_label = '';

	/** @var string legacy property, should use $action_button_class */
	protected $button_class = 'button-primary';

	/** @var bool whether the modal could be closed early and have a close button in the top right corner */
	protected $can_be_closed = true;


	/**
	 * Constructs the modal.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		add_action( 'admin_footer', array( $this, 'output' ) );
	}


	/**
	 * Returns the modal ID.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Returns the modal header template.
	 *
	 * By default, this will display the modal's title and a close button.
	 *
	 * @since 1.9.0
	 *
	 * @return string HTML
	 */
	protected function get_template_header() {

		ob_start();

		?>
		<header class="wc-backbone-modal-header">
			<h1><?php echo esc_html( $this->title ); ?></h1>
			<?php if ( $this->can_be_closed ) : ?>
				<button class="modal-close modal-close-link dashicons dashicons-no-alt">
					<span class="screen-reader-text"><?php esc_html_e( 'Close modal window', 'woocommerce-memberships' ); ?></span>
				</button>
			<?php endif; ?>
		</header>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the modal body template.
	 *
	 * @since 1.9.0
	 *
	 * @return string HTML
	 */
	protected function get_template_body() {
		return '';
	}


	/**
	 * Returns the modal footer template.
	 *
	 * By default, this will be the area for the modal action buttons.
	 *
	 * @since 1.9.0
	 *
	 * @return string HTML
	 */
	protected function get_template_footer() {

		ob_start();

		// legacy handling
		$button_label = ! empty( $this->button_label ) ? $this->button_label : $this->action_button_label;

		?>
		<footer>
			<div class="inner">
				<button id="btn-ok" class="button button-large <?php echo sanitize_html_class( $this->action_button_class ); ?>"><?php echo esc_html( $button_label ); ?></button>
			</div>
		</footer>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the modal template HTML.
	 *
	 * @since 1.9.0
	 *
	 * @return string HTML
	 */
	protected function get_template() {

		ob_start();

		?>
		<div class="wc-backbone-modal">
			<div class="wc-backbone-modal-content">
				<section class="wc-backbone-modal-main" role="main">
					<?php echo $this->get_template_header(); ?>
					<?php echo $this->get_template_body(); ?>
					<?php echo $this->get_template_footer(); ?>
				</section>
			</div>
		</div>
		<div class="wc-backbone-modal-backdrop modal-close"></div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Passes the modal HTML into a filter and wraps into <script> tags for output.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function get_html() {

		/**
		 * Filter the modal template HTML used in Memberships.
		 *
		 * @since 1.9.0
		 *
		 * @param string $html HTML template (without script tags)
		 * @param string $id the modal ID
		 */
		$html = apply_filters( 'wc_memberships_modal_html', $this->get_template(), $this->id );

		ob_start();

		?>
		<script type="text/template" id="tmpl-<?php echo esc_attr( $this->id ); ?>">
			<div id="<?php echo esc_attr( $this->id ); ?>" class="wc-memberships-modal">
				<?php echo $html; ?>
			</div>
		</script>
		<?php

		return ob_get_clean();
	}


	/**
	 * Outputs the modal template HTML.
	 *
	 * @since 1.9.0
	 */
	public function output() {

		echo $this->get_html();
	}


}
