<?php
/**
 * Dashboard template: Support Functions
 *
 * Manage support tickets, grant support-staff access and view System
 * configuration.
 *
 * Following variables are passed into the template:
 *   $data (projects data)
 *   $profile (user profile data)
 *   $urls (urls of all dashboard menu items)
 *   $staff_login (remote access status/details)
 *   $notes (notes for support staff)
 *   $access_logs (list of all support-staff logins)
 *
 * @since   4.0.0
 * @package WPMUDEV_Dashboard
 */

global $wp_version;

// Render the page header section.
$page_title = __( 'Support', 'wpmudev' );
$page_slug  = 'support';
$this->render_sui_header( $page_title, $page_slug );

/** @var WPMUDEV_Dashboard_Sui $this */
/** @var WPMUDEV_Dashboard_Sui_Page_Urls $urls */
/** @var stdClass $staff_login */
/** @var string $notes */

$url_grant       = wp_nonce_url( add_query_arg( 'action', 'remote-grant', $urls->support_url . '#access' ), 'remote-grant', 'hash' );
$url_revoke      = wp_nonce_url( add_query_arg( 'action', 'remote-revoke', $urls->support_url . '#access' ), 'remote-revoke', 'hash' );
$url_extend      = wp_nonce_url( add_query_arg( 'action', 'remote-extend', $urls->support_url . '#access' ), 'remote-extend', 'hash' );
$url_all_tickets = $urls->remote_site . 'hub/support/';
$url_search      = $urls->remote_site . 'forums/search.php';
$url_open_ticket = 'https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal';
$hub_url         = $urls->hub_url;

if ( $notes && ! empty( $_COOKIE['wpmudev_is_staff'] ) || ! empty( $_GET['staff'] ) ) {// wpcs csrf ok.
	$notes_class = 'active';
} else {
	$notes_class = '';
}

$threads      = $profile['forum']['support_threads'];
$open_threads = array(
	'all'      => array(),
	'open'     => array(),
	'resolved' => array(),
	'feedback' => array(),
);
foreach ( $threads as $thread ) {
	if ( empty( $thread['title'] ) ) {
		continue;
	}
	if ( empty( $thread['status'] ) ) {
		continue;
	}

	if ( 'resolved' === $thread['status'] ) {
		$thread['ui_status']        = array(
			'class' => 'sui-tag',
			'text'  => __( 'Resolved', 'wpmudev' ),
		);
		$open_threads['resolved'][] = $thread;
	} else {
		if ( isset( $thread['unread'] ) && $thread['unread'] ) {
			$thread['ui_status']        = array(
				'class' => 'sui-tag sui-tag-yellow',
				'text'  => __( 'Feedback', 'wpmudev' ),
			);
			$open_threads['feedback'][] = $thread;
		} else {
			$thread['ui_status']    = array(
				'class' => 'sui-tag sui-tag-blue',
				'text'  => __( 'Open', 'wpmudev' ),
			);
			$open_threads['open'][] = $thread;
		}
	}

	$open_threads['all'][] = $thread;

}

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
?>

<?php if ( isset( $_GET['success-action'] ) ) : // wpcs csrf ok. ?>

	<?php switch ( $_GET['success-action'] ) { // wpcs csrf ok.

		case 'remote-grant' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php printf( esc_html__('Support access granted. Please let support staff know you have granted access via your %s support ticket %s.', 'wpmudev' ), '<a href="' . esc_url( $urls->support_url ) . '">', '</a>' );//phpcs:ignore ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'remote-revoke' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Support session ended. You can grant access again at any time.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'remote-extend' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Support session extended. You can end session at any time.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'staff-note' : ?>

			<div class="sui-notice-top sui-notice-success sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php printf( esc_html__('Your note has been saved. Please let support staff know you have granted access via your %s support ticket %s.', 'wpmudev' ), '<a href="' . esc_url( '$urls->support_url' ) . '">', '</a>' );//phpcs:ignore ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;
		default:
			break;
	} ?>

<?php endif; ?>

<?php if ( isset( $_GET['failed-action'] ) ) : // wpcs csrf ok. ?>

	<?php switch ( $_GET['failed-action'] ) { // wpcs csrf ok.

		case 'remote-grant' : ?>

			<div class="sui-notice-top sui-notice-error sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Failed to grant support access.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'remote-revoke' : ?>

			<div class="sui-notice-top sui-notice-error sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Failed to end support session.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;

		case 'remote-extend' : ?>

			<div class="sui-notice-top sui-notice-error sui-can-dismiss">

				<div class="sui-notice-content">
					<p><?php esc_html_e( 'Failed to extend support session.', 'wpmudev' ); ?></p>
				</div>

				<span class="sui-notice-dismiss">
					<a role="button" aria-label="Dismiss" class="sui-icon-check"></a>
				</span>

			</div>

			<?php break;
		default:
			break;
	} ?>

<?php endif; ?>

<div class="sui-row-with-sidenav">

	<div class="sui-sidenav">

		<ul class="sui-vertical-tabs sui-sidenav-hide-md">

			<li class="sui-vertical-tab">

				<a href="#ticket"><?php esc_html_e( 'My Tickets', 'wpmudev' ); ?></a>

				<?php if ( ! empty( $open_threads['all'] ) ) : ?>
					<span class="sui-tag sui-tag-blue"><?php echo esc_html( count( $open_threads['all'] ) ); ?></span>
				<?php endif; ?>

			</li>

			<li class="sui-vertical-tab">

				<a href="#access"><?php esc_html_e( 'Support Access', 'wpmudev' ); ?></a>

				<?php if ( $staff_login->enabled ) : ?>
					<i class="sui-icon-lock sui-blue" aria-hidden="true"></i>
				<?php endif; ?>

			</li>

			<li class="sui-vertical-tab">
				<a href="#system"><?php esc_html_e( 'System Information', 'wpmudev' ); ?></a>
			</li>

		</ul>

		<div class="sui-sidenav-hide-lg">

			<select class="sui-mobile-nav" style="display: none;">

				<option value="#ticket" selected="selected"><?php esc_html_e( 'My Tickets', 'wpmudev' ); ?></option>

				<option value="#access"><?php esc_html_e( 'Support Access', 'wpmudev' ); ?></option>

				<option value="#system"><?php esc_html_e( 'System Information', 'wpmudev' ); ?></option>

			</select>
		</div>

	</div>

	<div class="sui-box js-sidenav-content" id="ticket" style="display: none;">

		<div class="sui-box-header">

			<h2 class="sui-box-title"><?php esc_html_e( 'My Tickets', 'wpmudev' ); ?></h2>

			<div class="sui-actions-right">

				<?php if ( ! empty( $open_threads['all'] ) ) : ?>

					<a
						href="<?php echo esc_url( $url_open_ticket ); ?>"
						target="_blank"
						class="sui-button sui-button-blue"
						<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>
					>
						<i class="sui-icon-plus" aria-hidden="true"></i>
						<?php esc_html_e( 'New Ticket', 'wpmudev' ); ?>
					</a>

				<?php endif; ?>

				<a
					href="<?php echo esc_url( $hub_url ); ?>"
					target="_blank"
					class="sui-button sui-button-ghost"
				>
					<i class="sui-icon-hub" aria-hidden="true"></i>
					<?php esc_html_e( 'The Hub', 'wpmudev' ); ?>
				</a>

			</div>

		</div>

		<?php if ( empty( $open_threads['all'] ) ) : ?>

			<div class="sui-message sui-message-lg">

				<img
					src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new.png' ); ?>"
					srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new@2x.png' ); ?> 2x"
					alt="Support"
					aria-hidden="true"
				/>

				<div class="sui-message-content">

					<p>
						<?php echo wp_kses_post(
							sprintf(
								__( 'You don’t have any active support tickets. When you create a support ticket, it will appear here. You can also access this in %1$sThe Hub%2$s.', 'wpmudev' ),
								'<a href="' . esc_url( $url_all_tickets ) . '" target="_blank">',
								'</a>'
							)
						); ?>
					</p>

					<p>
						<a href="<?php echo esc_url( $url_open_ticket ); ?>"
						   target="_blank"
						   class="sui-button sui-button-blue"
							<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>
						<i class="sui-icon-plus" aria-hidden="true"></i>
						<?php esc_html_e( 'New Ticket', 'wpmudev' ); ?>
					</a></p>

				</div>

			</div>

		<?php else : ?>

			<div class="sui-box-body">

				<div class="sui-side-tabs">

					<div class="sui-tabs-menu js-filter-ticket">

						<div class="sui-tab-item active" data-filter="all" tabindex="1"><?php esc_html_e( 'All', 'wpmudev' ); ?></div>
						<div class="sui-tab-item" data-filter="open" tabindex="2"><?php esc_html_e( 'Open', 'wpmudev' ); ?></div>
						<div class="sui-tab-item" data-filter="resolved" tabindex="3"><?php esc_html_e( 'Resolved', 'wpmudev' ); ?></div>
						<div class="sui-tab-item" data-filter="feedback" tabindex="4"><?php esc_html_e( 'Feedback', 'wpmudev' ); ?></div>

					</div>

				</div>

				<?php foreach ( $open_threads as $key => $thread_list ) : ?>

					<div class="dashui-table-tickets js-filter-ticket-content" data-filter="<?php echo esc_attr( $key ); ?>" style="display: none;">

						<table class="sui-table sui-table-flushed">

							<thead>
								<tr>
									<th class="dashui-column-topic"><?php esc_html_e( 'Topic', 'wpmudev' ); ?></th>
									<th class="dashui-column-replies"><?php esc_html_e( 'Replies', 'wpmudev' ); ?></th>
									<th class="dashui-column-status"><?php esc_html_e( 'Status', 'wpmudev' ); ?></th>
								</tr>
							</thead>

							<tbody>

								<?php if ( empty( $thread_list ) ) { ?>

									<tr>
										<td colspan="3">
											<?php esc_html_e( 'No tickets are available.', 'wpmudev' ); ?>
										</td>
									</tr>

								<?php } else { ?>

									<?php foreach ( $thread_list as $item ) : ?>

										<tr>

											<td class="dashui-column-topic">
												<a href="<?php echo esc_url( $item['link'] ); ?>" target="_blank"><?php echo esc_html( $item['title'] ); ?></a>
											</td>

											<td class="dashui-column-replies">
												<?php echo esc_html( intval( $item['posts'] ) ); ?>
											</td>

											<td class="dashui-column-status">

												<div class="dashui-status-row">

													<span class="<?php echo esc_attr( $item['ui_status']['class'] ); ?>"><?php echo esc_html( $item['ui_status']['text'] ); ?></span>

													<a class="sui-button-icon" href="<?php echo esc_url( $item['link'] ); ?>" target="_blank">
														<i class="sui-icon-chevron-right" aria-hidden="true"></i>
													</a>

												</div>

											</td>

										</tr>

									<?php endforeach; ?>

								<?php } ?>

							</tbody>

						</table>

						<?php if ( empty( $thread_list ) ) { ?>

							<div class="dashui-ticket">
								<span class="dashui-ticket-notice"><?php esc_html_e( 'No tickets are available.', 'wpmudev' ); ?></span>
							</div>

						<?php } else { ?>

							<?php foreach ( $thread_list as $item ) : ?>

								<div class="dashui-ticket">

									<span class="dashui-ticket-status <?php echo esc_attr( $item['ui_status']['class'] ); ?>"><?php echo esc_html( $item['ui_status']['text'] ); ?></span>

									<div class="dashui-ticket-topic">

										<span><?php echo esc_html( $item['title'] ); ?></span>

										<a
											href="<?php echo esc_url( $item['link'] ); ?>"
											target="_blank"
											class="sui-button-icon sui-button-icon-right"
										>
											<i class="sui-icon-chevron-right" aria-hidden="true"></i>
										</a>

									</div>

								</div>

							<?php endforeach; ?>

						<?php } ?>

					</div>

				<?php endforeach; ?>

				<p class="sui-block-content-center"><small>
					<?php echo wp_kses_post(
						sprintf(
							__( 'You can view and reply to your support tickets in %1$sThe Hub%2$s.', 'wpmudev' ),
							'<a href="' . esc_url( $url_all_tickets ) . '" target="_blank">',
							'</a>'
						)
					); ?>
				</small></p>

			</div>

		<?php endif; ?>

	</div>

	<div class="sui-box js-sidenav-content" id="access" style="display: none;">

		<div class="sui-box-header">

			<h2 class="sui-box-title"><?php esc_html_e( 'Support Access', 'wpmudev' ); ?></h2>

			<div class="sui-actions-left">

				<?php if ( $staff_login->enabled ) : ?>

					<span class="sui-tag sui-tag-blue"><?php esc_html_e( 'Active', 'wpmudev' ); ?></span>

				<?php else : ?>

					<?php if ( ! empty( $access_logs ) ) { ?>

						<span class="sui-tag"><?php esc_html_e( 'Inactive', 'wpmudev' ); ?></span>

					<?php } ?>

				<?php endif; ?>

			</div>


			<div class="sui-actions-right">

				<?php if ( ! empty( $access_logs ) && ! $staff_login->enabled ) : ?>

					<a href="<?php echo esc_url( $url_grant ); ?>"
					class="sui-button sui-button-blue js-loading-link"
						<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>
						<span class="sui-loading-text">
							<i class="sui-icon-key" aria-hidden="true"></i>
							<?php esc_html_e( 'Grant Support Access', 'wpmudev' ); ?>
						</span>
						<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
					</a>

				<?php endif; ?>

				<a class="sui-button sui-button-ghost js-modal-security" href="javascript:;">
					<i class="sui-icon-question" aria-hidden="true"></i>
					<?php _e( 'SECURITY INFO', 'wpmudev' ); ?>
				</a>

			</div>


		</div>

		<div class="sui-box-body">

			<?php if ( ! $staff_login->enabled ) { ?>

				<?php if ( empty( $access_logs ) ) : ?>

					<div class="sui-message">

						<img
							src="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new.png' ); ?>"
							srcset="<?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new.png' ); ?> 1x, <?php echo esc_url( WPMUDEV_Dashboard::$site->plugin_url . 'assets/images/graphic-support-new@2x.png' ); ?> 2x"
							alt="dev-man"
							class="sui-image"
						/>

						<p><?php esc_html_e( 'Need help? Grant support access so our WPMU DEV Support Staff are able to log in and help troubleshoot issues with you. This is completely secure and only active for a time period of your choice.', 'wpmudev' ); ?></p>

						<a href="<?php echo esc_url( $url_grant ); ?>"
						   class="sui-button sui-button-blue js-loading-link"
							<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>
							<span class="sui-loading-text">
								<i class="sui-icon-key" aria-hidden="true"></i>
								<?php esc_html_e( 'Grant Support Access', 'wpmudev' ); ?>
							</span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</a>

					</div>

					<div class="sui-box-body">
						<p class="sui-block-content-center sui-p-small" style="width: 100%">
							<?php
							$learnmore_url = 'https://premium.wpmudev.org/docs/getting-started/getting-support/#chapter-5';
							printf(
								esc_html__( 'Want to know more about the security of support access? %1$sLearn more%2$s', 'wpmudev' ),
								'<a target="_blank" class="js-modal-security" style="cursor:pointer">',
								'</a>'
							);
							?>
						</p>
					</div>

				<?php endif; ?>

			<?php } ?>

			<?php if ( $staff_login->enabled || ( ! $staff_login->enabled && ! empty( $access_logs ) ) ) : ?>

				<p><?php esc_html_e( 'Need help? Grant support access so our WPMU DEV Support Staff are able to log in and help troubleshoot issues with you. This is completely secure and only active for a time period of your choice.', 'wpmudev' ); ?></p>

			<?php endif; ?>

			<?php if ( $staff_login->enabled ) { ?>

				<div class="sui-notice dashui-notice-support">
					<p><?php echo esc_html( sprintf( __( "You have an active support session. If you haven't already, please let support staff know you have granted access. It will remain active for another %s.", 'wpmudev' ), human_time_diff( $staff_login->expires ) ) ); ?></p>

					<div class="sui-notice-buttons">

						<a
							href="<?php echo esc_url( $url_revoke ); ?>"
							class="sui-button js-loading-link"
						>
							<span class="sui-loading-text">
								<?php esc_html_e( 'END SESSION', 'wpmudev' ); ?>
							</span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</a>

						<a href="<?php echo esc_url( $url_extend ); ?>"
						   class="sui-button sui-button-ghost sui-tooltip js-loading-link"
						   data-tooltip="<?php esc_attr_e( 'Add another 3 days of support access', 'wpmudev' ); ?>"
							<?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>
							<span class="sui-loading-text">
								<?php esc_html_e( 'EXTEND', 'wpmudev' ); ?>
							</span>
							<i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
						</a>

					</div>

				</div>

				<form
					method="POST"
					action="<?php echo esc_url( $urls->support_url . '#access' ); ?>"
					class="sui-form-field staff-notes <?php echo esc_attr( $notes_class ); ?>"
				>

					<input
						type="hidden"
						name="action"
						value="staff-note"
					/>

					<?php wp_nonce_field( 'staff-note', 'hash' ); ?>

					<label for="support-staff-notes-id" class="sui-description" style="margin-bottom: 10px;"><?php esc_html_e( 'If you think it would help, leave our support heroes a quick message to let them know the details of your issue.', 'wpmudev' ); ?></label>

					<textarea
						name="notes"
						rows="5"
						placeholder="<?php esc_html_e( 'E.g. The issue occurs on Chrome when on smaller screens...', 'wpmudev' ); ?>"
						id="support-staff-notes-id"
						class="sui-form-control"
					><?php echo esc_textarea( $notes ); ?></textarea>

					<div style="display: block; margin-top: 10px; text-align: right;">

						<button type="submit" class="sui-button sui-button-blue">
							<span class="sui-loading-text"><?php esc_html_e( 'Save Message', 'wpmudev' ); ?></span>
							<i class="sui-icon-loader sui-loading"></i>
						</button>

					</div>

				</form>

				<?php if ( empty( $access_logs ) ) : ?>

					<div class="sui-box-settings-row sui-flushed" style="padding-bottom: 5px;">

						<div class="sui-box-settings-col-2">

							<label class="sui-table-title"><?php esc_html_e( 'Recent Sessions', 'wpmudev' ); ?></label>

						</div>

					</div>

					<div class="sui-notice">

						<p><?php if ( $staff_login->enabled ) : ?>
							<?php echo esc_html(
								sprintf(
									__( 'No one from Support has logged in yet. Sit tight, help is coming.', 'wpmudev' ),
									human_time_diff( $staff_login->expires )
								)
							); ?>
						<?php else : ?>
							<?php echo esc_html( sprintf( __( 'No one from Support has logged in.', 'wpmudev' ), human_time_diff( $staff_login->expires ) ) ); ?>
						<?php endif; ?></p>

					</div>

				<?php endif ; ?>

			<?php } ?>

		</div>

		<?php if ( ! empty( $access_logs ) ) : ?>

			<table class="sui-table sui-table-flushed dashui-table-sessions">

				<thead>

					<tr>
						<th colspan="2"><?php esc_html_e( 'Recent Sessions', 'wpmudev' ); ?></th>
					</tr>

				</thead>

				<tbody>

					<?php foreach ( $access_logs as $time => $user ) : ?>
						<?php
							$time = WPMUDEV_Dashboard::$site->to_localtime( $time );

							//backward compat
						 	$name = isset( $user['name'] ) ? $user['name'] : $user;
						 	$img = isset( $user['image'] ) ? 'https://www.gravatar.com/avatar/' . $user['image'] : '';
						?>
						<tr>

							<td class="sui-table-item-title">
								<div class="dashui-staff-info">
									<span class="dashui-avatar" style="background-image: url( <?php echo esc_url( $img ); ?> );" aria-hidden="true"></span>
									<span class="dashui-name"><?php echo esc_html( $name ); ?></span>
									<span class="sui-tag"><?php esc_html_e( 'Staff', 'wpmudev' ); ?></span>
								</div>
							</td>

							<td>
								<?php esc_html_e( 'Last seen', 'wpmudev' ); ?>
								<?php echo esc_html( date_i18n( $date_format, $time ) ); ?>
								@ <?php echo esc_html( date_i18n( $time_format, $time ) ); ?>
							</td>

						</tr>

					<?php endforeach; ?>


				</tbody>

			</table>

		<?php endif; ?>

		<?php if (
			$staff_login->enabled ||
			( ! $staff_login->enabled && ! empty( $access_logs ) )
		) : ?>

			<div class="sui-box-footer">

				<p class="sui-block-content-center sui-p-small" style="width: 100%">
					<?php
					$learnmore_url = 'https://premium.wpmudev.org/docs/getting-started/getting-support/#chapter-5';
					printf(
						esc_html__( 'Want to know more about the security of support access? %1$sLearn more%2$s', 'wpmudev' ),
						'<a href="' . esc_url( $learnmore_url ) . '" target="_blank">',
						'</a>'
					);
					?>
				</p>

			</div>

		<?php endif; ?>

	</div>

	<div class="sui-box js-sidenav-content" id="system" style="display: none;">

		<div class="sui-box-header">
			<h2 class="sui-box-title"><?php esc_html_e( 'System Information', 'wpmudev' ); ?></h2>
		</div>

		<div class="sui-box-body">

			<p><?php esc_html_e( 'Use this detailed overview of your system stack to debug issues with your WordPress installation.', 'wpmudev' ); ?></p>

			<ul class="dashui-list-sysinfo">

				<li>
					<strong><?php esc_html_e( 'WordPress', 'wpmudev' ); ?></strong>
					<span class="sui-tag"><?php echo esc_html( $wp_version ); ?></span>
				</li>

				<li>
					<strong><?php esc_html_e( 'WPMU DEV Dashboard', 'wpmudev' ); ?></strong>
					<span class="sui-tag"><?php echo esc_html( WPMUDEV_Dashboard::$version ); ?></span>
				</li>

			</ul>

		</div>

		<div class="sui-box-body">
			<?php $this->load_sui_template( 'part-system-info', array(), true ); ?>
		</div>

	</div>

</div>

<?php $this->load_sui_template( 'element-last-refresh', array(), true ); ?>

<?php $this->load_sui_template( 'footer', array(), true ); ?>

<div class="sui-dialog" aria-hidden="true" tabindex="-1" id="security-details">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

	<div class="sui-dialog-content" aria-labelledby="dialogTitle" aria-describedby="dialogDescription" role="dialog">

		<div class="sui-box" role="document">

			<div class="sui-box-header">

				<h3 class="sui-box-title" id="dialogTitle"><?php esc_html_e( 'How secure is support access?', 'wpmudev' ); ?></h3>

				<div class="sui-actions-right">
					<a data-a11y-dialog-hide class="sui-dialog-close" aria-label="<?php esc_html_e( 'Close this dialog window', 'wpmudev' ); ?>"></a>
				</div>

			</div>

			<div class="sui-box-body">

				<p class="sui-p-small"><?php esc_html_e( 'In short, our support access feature is bullet-proof secure and closed off to current WPMU DEV support staff only. We have never had any security issues with it, however you can disable it if you wish to.', 'wpmudev' ); ?></p>

				<h4 class="dashui-modal-header"><?php esc_html_e( 'How it works', 'wpmudev' ); ?></h4>
				<p id="dialogDescription" class="sui-p-small"><?php esc_html_e( 'When you click the "Grant Access" button a random 64 character access token is generated that is only good for 96 hours (5 days) and saved in your Database. This token is sent to the WPMU DEV API over an SSL encrypted connection to prevent eavesdropping, and stored on our secure servers. This access token is in no way related to your password, and can only be used from our closed WPMU DEV API system for temporary access to this site.', 'wpmudev' ); ?></p>

				<h4 class="dashui-modal-header"><?php esc_html_e( 'Who has access?', 'wpmudev' ); ?></h4>
				<p class="sui-p-small"><?php echo wp_kses_post( __( 'Only current WPMU DEV support staff can use this token to login as your user account by submitting a special form that only they have access to. This will give them 1 hour of admin access to this site before their login cookie expires. Every support staff login during the 5 day period is logged locally and you can view the details on this page.', 'wpmudev' ) ); ?></p>

				<h4 class="dashui-modal-header"><?php esc_html_e( 'Revoke access', 'wpmudev' ); ?></h4>
				<p class="sui-p-small"><?php echo wp_kses_post( __( 'You may at any time revoke this access which invalidates the token and it will no longer be usable. If you have special security concerns and you would like to disable the support access tab and functionality completely and permanently for whatever reason, you may do so by adding this line to your wp-config.php file:', 'wpmudev' ) ); ?></p>

				<pre class="sui-code-snippet sui-no-copy">define('WPMUDEV_DISABLE_REMOTE_ACCESS', true);</pre>

			</div>

			<div class="sui-box-footer">

				<a class="sui-button sui-button-ghost" data-a11y-dialog-hide="ftp-details"><?php esc_html_e( 'Close', 'wpmudev' ); ?></a>

				<div class="sui-actions-right">
					<a class="sui-button" href="<?php echo esc_url('https://premium.wpmudev.org/docs/getting-started/getting-support/'); ?>"><?php esc_html_e( 'Support Docs', 'wpmudev' ); ?></a>
				</div>

			</div>

		</div>

	</div>

</div>
