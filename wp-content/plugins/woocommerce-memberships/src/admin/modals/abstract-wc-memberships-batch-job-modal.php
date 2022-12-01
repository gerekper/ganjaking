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
 * Abstract background process modal class.
 *
 * This class acts as a base, abstracted modal view for background process jobs.
 *
 * @since 1.10.0
 */
abstract class WC_Memberships_Batch_Job_Modal extends \WC_Memberships_Modal {


	/** @var string stop button label */
	protected $stop_button_label = '';

	/** @var string stop button CSS class */
	protected $stop_button_class = '';

	/** @var string cancel button label */
	protected $cancel_button_label = '';

	/** @var string cancel button CSS class */
	protected $cancel_button_class = '';

	/** @var string close button label */
	protected $close_button_label = '';

	/** @var string close button CSS class */
	protected $close_button_class = '';


	/**
	 * Modal constructor.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		parent::__construct();

		$this->cancel_button_label = __( 'Cancel', 'woocommerce-memberships' );
		$this->close_button_label  = __( 'Close', 'woocommerce-memberships' );
		$this->can_be_closed       = false;
	}


	/**
	 * Returns the description of the job.
	 *
	 * Child modals that do not trigger immediately a batch process as they open can use this state to instruct the user on the process or provide additional options.
	 *
	 * This is displayed as the modal opens and the admin is asked to confirm starting the job.
	 * It will display errors if the process cannot be started at this point.
	 *
	 * @since 1.10.0
	 *
	 * @return string may contain HTML
	 */
	protected function get_job_description() {
		return '';
	}


	/**
	 * Returns information on an ongoing job.
	 *
	 * This is displayed as soon as a job started, and refreshed while it's ongoing.
	 *
	 * Child modals should extend this customizing the progress bar.
	 *
	 * @see \WC_Memberships_Batch_Job_Modal::get_progress_bar()
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_job_progress() {

		ob_start();

		?>
		<strong><?php /* translators: Placeholder: %s - percentage completed */
			printf( __( 'Current progress: %s', 'woocommerce-memberships' ), '{{data.job.percentage}}%' ); ?></strong>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the current job progress bar.
	 *
	 * Child modals could customize the progress bar with the item types being processed.
	 *
	 * @see \WC_Memberships_Batch_Job_Modal::get_job_progress()
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_progress_bar() {

		ob_start();

		?>
		<progress
			id="wc-memberships-batch-job-progress"
			value="{{data.job.progress}}"
			max="{{data.job.total}}"></progress>
		<div style="float:left; margin: 0;"
		     class="spinner <# if ( 'completed' != data.job.status ) { #>is-active<# } #>"></div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns information upon job completion.
	 *
	 * This is displayed once a job has finished running and results are displayed to the admin.
	 *
	 * @since 1.10.0
	 *
	 * @return string may contain HTML
	 */
	abstract protected function get_job_completed();


	/**
	 * Returns errors information.
	 *
	 * This is displayed in case of job errors (the job is normally halted).
	 *
	 * @since 1.10.0
	 *
	 * @return string may contain HTML
	 */
	protected function get_job_error() {

		ob_start();

		?>
		<# if ( data.error ) { #>

			<h4><?php esc_html_e( 'Error', 'woocommerce-memberships' ) ?></h4>

			{{{data.error}}}

		<# } #>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the cancel modal button to display upon job completion.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_cancel_button() {

		ob_start();

		?>
		<button
			<?php // button ID is not an error: the cancel button action is equivalent to close in JS ?>
			id="btn-close"
			class="button button-large <?php echo sanitize_html_class( $this->cancel_button_class ); ?>"
			data-job-name="<# if ( data.job ) { #>{{data.job.name}}<# } #>"
			data-job-id="<# if ( data.job ) { #>{{data.job.id}}<# } #>"><?php
			echo esc_html( $this->cancel_button_label ); ?></button>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the close modal button to display upon job completion.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_close_button() {

		ob_start();

		?>
		<button
			id="btn-close"
			class="button button-large <?php echo sanitize_html_class( $this->close_button_class ); ?>"
			data-job-name="<# if ( data.job ) { #>{{data.job.name}}<# } #>"
			data-job-id="<# if ( data.job ) { #>{{data.job.id}}<# } #>"><?php
			echo esc_html( $this->close_button_label ); ?></button>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the stop batch process button.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_stop_button() {

		ob_start();

		?>
		<button
			id="btn-stop"
			class="button button-large <?php echo sanitize_html_class( $this->stop_button_class ); ?>"
			data-job-name="{{data.job.name}}"
			data-job-id="{{data.job.id}}"><?php
			echo esc_html( $this->stop_button_label ); ?></button>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the button to start the batch process.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_start_button() {

		ob_start();

		?>
		<button
			id="btn-start"
			class="button button-large <?php echo sanitize_html_class( $this->action_button_class ); ?>"><?php
			echo esc_html( $this->action_button_label ); ?></button>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the modal body template.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_template_body() {

		ob_start();

		?>
		<article>

			<# if ( data.job ) { #>

				<div class="wc-memberships-background-process-modal-progress">

					<input
						type="hidden"
						class="wc-memberships-background-job-id"
						id="<?php echo esc_attr( $this->get_id() . '-job' ); ?>"
						data-name="{{data.job.name}}"
						data-id="{{data.job.id}}"
						data-status="{{data.job.status}}"
						value="{{data.job.id}}"
					/>

					<# if ( 'completed' == data.job.status || 100 == data.job.percentage ) { #>
						<?php echo $this->get_job_completed(); ?>
					<# } else { #>
						<# if ( data.error ) { #>
							<?php echo $this->get_job_error(); ?>
						<# } else { #>
							<?php echo $this->get_job_progress(); ?>
						<# } #>
					<# } #>

				</div>

			<# } else { #>

				<div class="wc-memberships-background-process-modal-description">
					<?php echo $this->get_job_description(); ?>
				</div>

			<# } #>

		</article>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the modal footer template.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_template_footer() {

		ob_start();

		?>
		<footer>
			<div class="inner">

				<# if ( data.error ) { #>

					<?php echo $this->get_close_button(); ?>

				<# } else if ( data.job ) { #>

					<# if ( 'completed' == data.job.status ) { #>
						<?php echo $this->get_close_button(); ?>
					<# } else { #>
						<?php echo $this->get_stop_button(); ?>
					<# } #>

				<# } else { #>

					<?php echo $this->get_cancel_button(); ?>
					<?php echo $this->get_start_button(); ?>

				<# } #>

			</div>
		</footer>
		<?php

		return ob_get_clean();
	}


}
