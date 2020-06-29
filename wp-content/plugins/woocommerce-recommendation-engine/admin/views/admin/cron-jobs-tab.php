
<div id="wc-recommender-cron-jobs">
	<form method="POST">

		<table class="wp-list-table widefat fixed crons" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="hook" class="manage-column column-hook"><span><?php _e( 'Hook', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="schedule" class="manage-column column-schedule"><span><?php _e( 'Schedule', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="args" class="manage-column column-args"><span><?php _e( 'Previous execution', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="next" class="manage-column column-next"><span><?php _e( 'Next execution', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="next" class="manage-column column-next"><span><?php _e( 'Action', 'wc_recommender' ); ?></span></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th scope="col" id="hook" class="manage-column column-hook"><span><?php _e( 'Hook', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="schedule" class="manage-column column-schedule"><span><?php _e( 'Schedule', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="args" class="manage-column column-args"><span><?php _e( 'Previous execution', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="next" class="manage-column column-next"><span><?php _e( 'Next execution', 'wc_recommender' ); ?></span></th>
					<th scope="col" id="next" class="manage-column column-next"><span><?php _e( 'Action', 'wc_recommender' ); ?></span></th>
				</tr>
			</tfoot>

			<tbody>
				<tr>
					<td></td>
					<td></td>
					<td>
						<ul>
							<li>
								<?php echo date( 'Y-m-d h:i:s', get_option( 'woocommerce_recommender_cron_start' ) ); ?>
							</li>
							<li>
								<?php echo date( 'Y-m-d h:i:s', get_option( 'woocommerce_recommender_cron_end' ) ); ?>
							</li>
						</ul>

					</td>
					<td>
						
					</td>
					<td>
						<a class="do_execute_cron_job button-secondary"><?php _e( 'Execute', 'wc_recommender' ); ?></a>
					</td>
				</tr>


			</tbody>

		</table>

	</form>
</div>