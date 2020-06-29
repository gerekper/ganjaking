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
 * Handles retroactive grants access action to a membership plan
 *
 * This modal is used when an admin wants to grant existing users access to a plan.
 *
 * @since 1.10.0
 */
class WC_Memberships_Modal_Grant_Access_Membership_Plan extends \WC_Memberships_Batch_Job_Modal {


	/**
	 * Constructs the modal.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		parent::__construct();

		$this->id                  = 'wc-memberships-modal-grant-access-membership-plan';
		$this->title               = __( 'Grant Access Retroactively', 'woocommerce-memberships' );
		$this->action_button_label = __( 'Grant Access', 'woocommerce-memberships' );
		$this->stop_button_label   = __( 'Stop Processing', 'woocommerce-memberships' );
	}


	/**
	 * Returns the task description when there's no job ongoing.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	protected function get_job_description() {

		/* translators: Placeholder: %s - the plan name */
		$signup_label    = sprintf( __( 'This action will create a membership for registered users who are not yet members of the %s plan.', 'woocommerce-memberships' ),'<strong>{{data.plan.name}}</strong>' );
		/* translators: Placeholder: %s - the plan name */
		$purchase_label  = sprintf( __( 'This action will create a membership for customers who have previously purchased one of the products that grant access to the %s plan.', 'woocommerce-memberships' ), '<strong>{{data.plan.name}}</strong>' );
		$purchase_label .= '<br /><br />' . esc_html__( 'If a user already has access to this plan, the original membership status and dates are preserved.', 'woocommerce-memberships' );

		if ( wc_memberships()->get_integrations_instance()->is_subscriptions_active() ) {

			$purchase_label .= '<br />' . esc_html__( 'For subscription-tied plans: only active subscribers will gain a membership.', 'woocommerce-memberships' );
		}

		ob_start();

		?>
		<# if ( 'signup' == data.plan.access ) { #>
			<p><span class="grant-access-signup"><?php echo $signup_label; ?></span></p>
		<# } #>
		<# if ( 'purchase' == data.plan.access ) { #>
			<p><span class="grant-access-purchase"><?php echo $purchase_label; ?></span></p>
		<# } #>
		<p><?php /* translators: Placeholders: %1$s - opening <em> tag, %2$s - opening <strong> tag, %3$s - closing </strong> tag, %4$s - opening <u> tag, %5$s - closing </u> tag, %6$s - opening <u> tag, %7$s - closing </u> tag, %8$s - closing </em> tag */
			printf( __( '%1$s%2$sImportant!%3$s The process is %4$snot%5$s reversible. Once memberships have been created, you must manually delete them. Please be sure you have %6$ssaved changes to your plan%7$s before starting this process.%8$s', 'woocommerce-memberships' ), '<em>', '<strong>', '</strong>', '<u>', '</u>', '<u>', '</u>', '</em>' ); ?></p><?php

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
		<p><?php esc_html_e( 'You can choose to stop the current process early. Please be aware that any newly created memberships will not be deleted automatically.', 'woocommerce-memberships' ); ?></p>
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
		<div>
			<# if ( data.job.total == 1 ) { #>
				<p><small><?php /* translators: Placeholders: %s - current progress (number), either 0 or 1 */
				printf( __( 'Processed %s out of 1 user.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?></small></p>
			<# } else { #>
				<p><small><?php /* translators: Placeholders: %1$s - current progress (number), %2$s - total amount of users to process (number) */
				printf( __( 'Processed %1$s out of %2$s users.', 'woocommerce-memberships' ), '<span class="job-progress-current">{{data.job.progress}}</span>', '<span class="job-progress-total">{{data.job.total}}</span>' ); ?></small></p>
			<# } #>
		</div>
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
		<div style="margin-top: 12px;">
			{{{data.job.results.html}}}
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the batch process start button.
	 *
	 * Overrides abstract method: we need to pass the plan ID in the button.
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
			data-plan-id="{{data.plan.id}}"
			class="button button-large <?php echo sanitize_html_class( $this->action_button_class ); ?>"><?php
			echo esc_html( $this->action_button_label ); ?></button>
		<?php

		return ob_get_clean();
	}


}
