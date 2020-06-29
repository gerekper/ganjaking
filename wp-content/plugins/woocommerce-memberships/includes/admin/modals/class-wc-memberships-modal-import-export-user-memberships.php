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
class WC_Memberships_Modal_Import_Export_User_Memberships extends \WC_Memberships_Batch_Job_Modal {


	/**
	 * Constructs the modal.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id = 'wc-memberships-modal-import-export-user-memberships';
	}


	/**
	 * Returns information of the ongoing job in progress.
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

		<# if ( 'import' == data.action.id ) { #>
			<p><?php /* translators: Placeholders: %1$s - opening <em> tag, %2$s - opening <strong> tag, %3$s - closing </strong> tag, %4$s - closing </em> tag */
				printf( __( '%1$s%2$sImportant!%3$s Do not leave this window until the import completes or it will be cancelled. Imported memberships will not be deleted automatically if the import is interrupted.%4$s', 'woocommerce-memberships' ), '<em>', '<strong>', '</strong>', '</em>' ); ?></p>
		<# } #>

		<# if ( 'export' == data.action.id ) { #>
			<p><?php /* translators: Placeholders: %1$s - opening <em> tag, %2$s - opening <strong> tag, %3$s - closing </strong> tag, %4$s - closing </em> tag */
				printf( __( '%1$s%2$sImportant!%3$s Do not leave this window until the export has completed or it will be stopped.%4$s', 'woocommerce-memberships' ), '<em>', '<strong>', '</strong>', '</em>' ); ?></p>
		<# } #>

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

		<# if ( 'import' == data.action.id ) { #>

			<small>
				<# if ( data.job.total == 1 ) { #>
					<?php /* translators: Placeholders: %s - current progress (number), either 0 or 1 */
					printf( __( 'Processed %s out of 1 row.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
				<# } else { #>
					<?php /* translators: Placeholders: %1$s - current progress (number), %2$s - total amount of rows processed during an import */
					printf( __( 'Processed %1$s out of %2$s rows.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
				<# } #>
			</small>

		<# } #>

		<# if ( 'export' == data.action.id ) { #>

			<small>
				<# if ( data.job.total == 1 ) { #>
					<?php /* translators: Placeholders: %s - current progress (number) */
					printf( __( 'Processed %s out of 1 membership.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
				<# } else { #>
					<?php /* translators: Placeholders: %1$s - current progress (number), %2$s - total amount of user memberships to process (number) */
					printf( __( 'Processed %1$s out of %2$s memberships.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?>
				<# } #>
			</small>

		<# } #>

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

		<# if ( 'import' == data.action.id ) { #>
			<strong><?php esc_html_e( 'Import complete.', 'woocommerce-memberships' ); ?></strong>
		<# } #>

		<# if ( 'export' == data.action.id ) { #>
			<strong><?php esc_html_e( 'Export complete.', 'woocommerce-memberships' ); ?></strong>
		<# } #>

		<?php echo $this->get_progress_bar(); ?>

		{{{data.job.results.html}}}

		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the stop import/export button.
	 *
	 * Overrides parent method: changes the label according to process.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	protected function get_stop_button() {

		ob_start();

		?>

		<# if ( 'import' == data.action.id ) { #>

			<button
				id="btn-stop"
				class="button button-large <?php echo sanitize_html_class( $this->stop_button_class ); ?>"
				data-job-name="{{data.job.name}}"
				data-job-id="{{data.job.id}}"><?php
				esc_html_e( 'Stop Import', 'woocommerce-memberships' ); ?></button>

		<# } #>

		<# if ( 'export' == data.action.id ) { #>

			<button
				id="btn-stop"
				class="button button-large <?php echo sanitize_html_class( $this->stop_button_class ); ?>"
				data-job-name="{{data.job.name}}"
				data-job-id="{{data.job.id}}"><?php
				esc_html_e( 'Stop Export', 'woocommerce-memberships' ); ?></button>

		<# } #>

		<?php

		return ob_get_clean();
	}


}

