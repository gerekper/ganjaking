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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handles rescheduling of user memberships events.
 *
 * @since 1.10.0
 */
class WC_Memberships_Modal_Reschedule_User_Memberships_Events extends \WC_Memberships_Batch_Job_Modal {


	/**
	 * Constructs the modal.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-reschedule-user-memberships-events';
		$this->title               = __( 'Reschedule Membership Emails', 'woocommerce-memberships' );
		$this->action_button_class = 'button-primary';
		$this->action_button_label = __( 'Reschedule', 'woocommerce-memberships' );
		$this->stop_button_label   = __( 'Stop Rescheduling', 'woocommerce-memberships' );
	}


	/**
	 * Returns the task description when there's no job ongoing.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_job_description() {

		ob_start();

		?>
		<p><?php esc_html_e( 'You can reschedule emails for existing members if you have updated your sending schedule. If you do not reschedule emails, members will still receive emails on the previous schedule.', 'woocommerce-memberships' ); ?></p>
		<p><?php /* translators: Placeholders: %1$s - opening <em> tag, %2$s - opening <strong> tag, %3$s - closing </strong> tag, %4$s - opening <u> underline tag, %5$s, closing </u> underline tag, %6$s - closing </em> tag */
			printf( __( '%1$s%2$sImportant!%3$s Please be sure to %4$ssave your email settings%5$s before running this tool.%6$s', 'woocommerce-memberships' ), '<em>', '<strong>', '</strong>', '<u>', '</u>', '</em>' ); ?></p><?php

		return ob_get_clean();
	}


	/**
	 * Returns information if there's an ongoing job in progress.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_job_progress() {

		ob_start();

		echo parent::get_job_progress();
		echo $this->get_progress_bar();

		?>
		<p><?php esc_html_e( 'You can choose to stop the current process and start again later. Please be aware that any memberships that have been processed already will receive emails using your new schedule.', 'woocommerce-memberships' ); ?></p>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the current job progress bar.
	 *
	 * @see \WC_Memberships_Batch_Job_Modal::get_job_progress()
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_progress_bar() {

		ob_start();

		echo parent::get_progress_bar();

		?>
		<small>
			<# if ( data.job.total == 1 ) { #>
				<?php /* translators: Placeholders: %s - current progress (number), either 0 or 1 */
				printf( __( 'Processed %s out of 1 membership.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
			<# } else { #>
				<?php /* translators: Placeholders: %1$s - current progress (number), %2$s - total amount of user memberships to process (number) */
				printf( __( 'Processed %1$s out of %2$s memberships.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
			<# } #>
		</small>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns information about the current completed job.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_job_completed() {

		ob_start();

		?>
		<strong><?php esc_html_e( 'Done!', 'woocommerce-memberships' ); ?></strong>
		<?php echo $this->get_progress_bar(); ?>
		<p>
			<# if ( data.job.total == 0 ) { #>

				<span class="dashicons dashicons-no"></span>

				<?php esc_html_e( 'No memberships found to reschedule.', 'woocommerce-memberships' ); ?>

			<# } else { #>

				<span class="dashicons dashicons-yes"></span>

				<# if ( data.job.total > 1 ) { #>
					<?php /* translators: Placeholder: %s - processed user memberships */
					printf( esc_html__( 'Processed %s memberships.', 'woocommerce-memberships' ), '{{data.job.total}}' ); ?>
				<# } else { #>
					<?php esc_html_e( 'Processed 1 membership.', 'woocommerce-memberships' ); ?>
				<# } #>

			<# } #>
		</p>
		<?php

		return ob_get_clean();
	}


}

