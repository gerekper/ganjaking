<?php
/**
 * Progress Bar HTML.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="rs_progress_bar_wrapper">
	<h1><?php esc_html_e( 'SUMO Reward Points', 'rewardsystem' ); ?></h1>
	<div id="rs_progress_bar_label">
		<h2><?php echo esc_html( $action_scheduler->get_progress_bar_label() ); ?></h2>
	</div>
	<div class="rs_progress_bar_outer">
		<div class="rs_progress_bar_inner" style="width: <?php echo esc_attr( $action_scheduler->get_progress_count() ); ?>%">

		</div>
	</div>
	<div id="rs_progress_bar_status">
		<span id="rs_progress_bar_current_status"><?php echo esc_html( $action_scheduler->get_progress_count() ); ?></span>
		<?php esc_html_e( '% Completed', 'rewardsystem' ); ?>
	</div>

	<p class="rs-action-scheduler-info">
		<span>
			<?php
			$scheduler_url = add_query_arg( array( 'page' => 'action-scheduler', 'status' => 'pending' ), admin_url( 'tools.php' ) );
			/* translators: %s : Scheduled Action URL */
			echo wp_kses_post( __( sprintf( '<b>Note:</b> <br/> 1. Please check and run the <b>Action Scheduler</b> name <b>"%s"</b> in <b>"Tools -> <a href="%s" target="_blank">Scheduled Actions</a>"</b> if you find any difficulties. <br/> 2. If you wish to check the Bulk Update process once the action is scheduled, then please check the Action Scheduler name <b>"%s"</b>.', $action_scheduler->get_action_scheduler_name(), esc_url( $scheduler_url ), $action_scheduler->get_chunked_action_scheduler_name() ), 'rewardsystem' ) );
			?>
		</span>
	</p>

	<?php if ( $action_scheduler->get_settings_url() ) : ?>    
		<p class="rs-settings-url" style="display:none;">
			<a href="<?php echo esc_url( $action_scheduler->get_settings_url() ); ?>"><b><?php esc_html_e( 'Go to Settings', 'rewardsystem' ); ?></b></a>
		</p>
	<?php endif; ?>

	<input type="hidden" class="rs-action-scheduler-action-id" value="<?php echo esc_attr( $action_scheduler->get_id() ); ?>">
</div>
<?php
