<?php
/**
 * Dashboard analytics widget: Displayed on site dashboards with stats.
 *
 * @since  4.6
 * @package WPMUDEV_Dashboard
 */
$days_ago = ( isset( $_REQUEST['analytics_range'] ) && in_array( $_REQUEST['analytics_range'], array( 1, 7, 30, 90 ) ) ) ? absint( $_REQUEST['analytics_range'] ) : 7;
if ( is_network_admin() || ! is_multisite() ) {
	$data = WPMUDEV_Dashboard::$api->analytics_stats_overall( $days_ago );
} else {
	$data = WPMUDEV_Dashboard::$api->analytics_stats_overall( $days_ago, get_current_blog_id() );
}
$metrics = WPMUDEV_Dashboard::$site->get_metrics_on_analytics();
?>
<div class="wpmudui-analytics">

	<div class="wpmudui-tabs">

		<?php if ( ! $data ) { ?>
		<div class="wpmudui-notice wpmudui-notice-error">
			<p><?php esc_html_e( 'There was a temporary issue fetching analytics data. Please try again later.', 'wpmudev' ); ?></p>
			<div class="wpmudui-notice-buttons">
				<a onClick="window.location.reload()" class="wpmudui-button"><?php esc_html_e( 'Try again', 'wpmudev' ); ?></a>
			</div>
		</div>
		<?php } else { //not an api error ?>

		<div class="wpmudui-analytics-tabs" data-tabs>
			<a data-tab="overview"<?php echo ( ( ! isset( $_GET['tab'] ) || 'overview' === $_GET['tab'] ) ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Overview', 'wpmudev' ); ?></a>
			<?php if ( isset( $data['pages'] ) && count( $data['pages'] ) ) { ?>
			<a data-tab="posts"<?php echo ( ( isset( $_GET['tab'] ) && 'posts' === $_GET['tab'] ) ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Top Pages & Posts', 'wpmudev' ); ?></a>
			<?php } ?>
			<?php if ( isset( $data['authors'] ) && count( $data['authors'] ) ) { ?>
			<a data-tab="authors"<?php echo ( ( isset( $_GET['tab'] ) && 'authors' === $_GET['tab'] ) ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Authors', 'wpmudev' ); ?></a>
			<?php } ?>
			<?php if ( isset( $data['sites'] ) && count( $data['sites'] ) ) { ?>
				<a data-tab="sites"<?php echo ( ( isset( $_GET['tab'] ) && 'sites' === $_GET['tab'] ) ? ' class="wpmudui-current"' : '' ); ?>><?php esc_html_e( 'Top Sites', 'wpmudev' ); ?></a>
			<?php } ?>
		</div>

		<div class="wpmudui-analytics-content" data-panes>

			<!-- TAB: Overview -->
			<div data-pane="overview" class="wpmudui-tab-content"<?php echo ! isset( $_GET['tab'] ) ? ' style="display: block;"' : ''; ?>>

				<div class="wpmudui-search-form">
					<label class="wpmudui-label" for="wpmudui-analytics-range"><?php esc_html_e( 'Show', 'wpmudev' ); ?></label>
					<select id="wpmudui-analytics-range" class="wpmudui-select wpmudui-analytics-range">
						<option value="1"<?php selected( 1, $days_ago ); ?>><?php esc_html_e( 'Yesterday', 'wpmudev' ); ?></option>
						<option value="7"<?php selected( 7, $days_ago ); ?>><?php esc_html_e( 'Last 7 days', 'wpmudev' ); ?></option>
						<option value="30"<?php selected( 30, $days_ago ); ?>><?php esc_html_e( 'Last 30 days', 'wpmudev' ); ?></option>
						<option value="90"<?php selected( 90, $days_ago ); ?>><?php esc_html_e( 'Last 90 days', 'wpmudev' ); ?></option>
					</select>
					<label class="wpmudui-label" for="wpmudui-analytics-search"><?php esc_html_e( 'data for', 'wpmudev' ); ?></label>
					<input type="search"
						size="1"
						placeholder="<?php esc_attr_e( 'Full Site', 'wpmudev' ); ?>"
						id="wpmudui-analytics-search"
						class="wpmudui-input wpmudui-autocomplete" />
				</div>

				<div class="wpmudui-analytics-chart">
					<div class="wpmudui-analytics-chart-empty">
						<p class="wpmudui-analytics-chart-title"><?php esc_html_e( "No data recorded for this metric", 'wpmudev' ); ?></p>
						<p><?php esc_html_e( "Analytics data is updated every 24 hours and does not include the current day's activity. Check back later to view these stats.", 'wpmudev' ); ?></p>
					</div>
					<canvas id="wpmudui-analytics-graph"></canvas>
				</div>

				<div class="wpmudui-chart-options">

					<button data-type="visits" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'If a visitor comes to your website for the first time or if they visit a page more than 30 minutes after their last page view, this will be recorded as a new visit.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Visits', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="pageviews" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Total number of pages viewed.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="page_time" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'The average amount of time visitors spent on a page.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Page Time', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="visit_time" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'The average amount of time visitors spent on the site.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Visit Time', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="bounce_rate" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'The percentage of visits that only had a single pageview. This means, that the visitor left the website directly from the entrance page.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_attr_e( 'Bounce Rate', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="exit_rate" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'Number of exits divided by page views. Indicates percentage of exits from a specified page or average across your site.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

					<button data-type="gen_time" class="wpmudui-none wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
					        data-tooltip="<?php esc_attr_e( 'The average time it took to generate a page. Includes server generation time + visitors time to download the page from the server.', 'wpmudev' ); ?>">
						<span class="wpmudui-chart-option-title"><?php esc_html_e( 'Gen. Time', 'wpmudev' ); ?></span>
						<span class="wpmudui-chart-option-value">-</span>
						<span class="wpmudui-chart-option-trend">0%</span>
					</button>

				</div>

			</div>

			<?php if ( isset( $data['pages'] ) && count( $data['pages'] ) ) { ?>
			<!-- TAB: Top Pages & Posts -->
			<div data-pane="posts" class="wpmudui-tab-content">

				<div class="wpmudui-search-form">
					<label class="wpmudui-label" for="wpmudui-analytics-posts-range"><?php esc_html_e( 'Show', 'wpmudev' ); ?></label>
					<select id="wpmudui-analytics-posts-range" class="wpmudui-select wpmudui-analytics-range">
						<option value="1"<?php selected( 1, $days_ago ); ?>><?php esc_html_e( 'Yesterday', 'wpmudev' ); ?></option>
						<option value="7"<?php selected( 7, $days_ago ); ?>><?php esc_html_e( 'Last 7 days', 'wpmudev' ); ?></option>
						<option value="30"<?php selected( 30, $days_ago ); ?>><?php esc_html_e( 'Last 30 days', 'wpmudev' ); ?></option>
						<option value="90"<?php selected( 90, $days_ago ); ?>><?php esc_html_e( 'Last 90 days', 'wpmudev' ); ?></option>
					</select>
					<label class="wpmudui-label" for="wpmudui-analytics-posts-type"><?php esc_html_e( 'data for', 'wpmudev' ); ?></label>
					<select id="wpmudui-analytics-posts-type" class="wpmudui-select wpmudui-analytics-column-filter">
						<?php if ( in_array( 'pageviews', $metrics, true ) ) : ?>
							<option value="pageviews"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></option>
						<?php endif; ?>
						<?php if ( in_array( 'unique_pageviews', $metrics, true ) ) : ?>
							<option value="unique_pageviews" selected><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></option>
						<?php endif; ?>
						<?php if ( in_array( 'bounce_rate', $metrics, true ) ) : ?>
							<option value="bounce_rate"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></option>
						<?php endif; ?>
						<?php if ( in_array( 'exit_rate', $metrics, true ) ) : ?>
							<option value="exit_rate"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></option>
						<?php endif; ?>
						<?php if ( in_array( 'page_time', $metrics, true ) ) : ?>
							<option value="page_time"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></option>
						<?php endif; ?>
						<?php if ( in_array( 'gen_time', $metrics, true ) ) : ?>
							<option value="gen_time"><?php esc_html_e( 'Generation Time', 'wpmudev' ); ?></option>
						<?php endif; ?>
					</select>
				</div>

				<div class="wpmudui-table-flushed">

					<table class="wpmudui-table" data-rows="<?php echo esc_attr( count( $data['pages'] ) ); ?>">

						<thead>
							<tr>
								<th><?php esc_html_e( 'Page/Post title', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of times this page was visited.', 'wpmudev' ); ?>"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-unique_pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of visits that included this page. If a page was viewed multiple times during one visit, it is only counted once.', 'wpmudev' ); ?>"><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-bounce_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The percentage of visits that only had a single pageview. This means, that the visitor left the website directly from the entrance page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-exit_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'Number of exits divided by page views. Indicates percentage of exits from a specified page or average across your site.', 'wpmudev' ); ?>"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-page_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average amount of time visitors spent on a page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-gen_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average time it took to generate a page. Includes server generation time + visitors time to download the page from the server.', 'wpmudev' ); ?>"><?php esc_html_e( 'Gen. Time', 'wpmudev' ); ?></th>
							</tr>
						</thead>

						<tbody class="wpmudui-table-sortable">

						<?php foreach( $data['pages'] as $site_page ) { ?>
							<tr class="wpmudui-table-item wpmudui-tracking" data-filter-type="page" data-filter="<?php echo esc_attr( $site_page['filter'] ); ?>" data-label="<?php esc_attr_e( 'Page:', 'wpmudev' ); ?>">
								<td><span><?php echo esc_html( $site_page['name'] ); ?></span></td>
								<?php if ( isset( $site_page['pageviews'] ) ) : ?>
									<td class="wpmudui-table-views data-pageviews" data-sort="<?php echo esc_attr( $site_page['pageviews']['sort'] ); ?>"><?php echo esc_html( $site_page['pageviews']['value'] ); ?></td>
								<?php endif; ?>
								<?php if ( isset( $site_page['unique_pageviews'] ) ) : ?>
									<td class="wpmudui-table-views data-unique_pageviews" data-sort="<?php echo esc_attr( $site_page['unique_pageviews']['sort'] ); ?>"><?php echo esc_html( $site_page['unique_pageviews']['value'] ); ?></td>
								<?php endif; ?>
								<?php if ( isset( $site_page['bounce_rate']) ) : ?>
									<td class="wpmudui-table-views data-bounce_rate" data-sort="<?php echo esc_attr( $site_page['bounce_rate']['sort'] ); ?>"><?php echo esc_html( $site_page['bounce_rate']['value'] ); ?></td>
								<?php endif; ?>
								<?php if ( isset( $site_page['exit_rate'] ) ) : ?>
									<td class="wpmudui-table-views data-exit_rate" data-sort="<?php echo esc_attr( $site_page['exit_rate']['sort'] ); ?>"><?php echo esc_html( $site_page['exit_rate']['value'] ); ?></td>
								<?php endif; ?>
								<?php if ( isset( $site_page['page_time'] ) ) : ?>
									<td class="wpmudui-table-views data-page_time" data-sort="<?php echo esc_attr( $site_page['page_time']['sort'] ); ?>"><?php echo esc_html( $site_page['page_time']['value'] ); ?></td>
								<?php endif; ?>
								<?php if ( isset( $site_page['gen_time'] ) ) : ?>
									<td class="wpmudui-table-views data-gen_time" data-sort="<?php echo esc_attr( $site_page['gen_time']['sort'] ); ?>"><?php echo esc_html( $site_page['gen_time']['value'] ); ?></td>
								<?php endif; ?>
							</tr>
						<?php } ?>

						</tbody>

					</table>

					<div class="wpmudui-pagination wpmudui-pagination-wrapper" data-current-page="1">

						<label for="wpmdui-pagination-search-pages" class="wpmudui-label"><?php esc_html_e( 'Go to', 'wpmudev' ); ?></label>

						<input type="number"
						       placeholder="1"
						       id="wpmdui-pagination-search-pages"
						       class="wpmudui-input wpmudui-goto-page">

						<div class="wpmudui-navigation">

							<label class="wpmudui-label">
								<span class="wpmudui-start-row">1</span>&nbsp;-&nbsp;
								<span class="wpmudui-end-row">10</span>&nbsp;
								<?php echo sprintf( __( 'of %s', 'wpmudev' ), number_format_i18n( count( $data['pages'] ) ) ); ?>
							</label>

							<button class="wpmudui-button wpmudui-button-icon wpmudui-page-prev"><i class="wpmudui-icon-chevron-left" aria-hidden="true"></i></button>

							<button class="wpmudui-button wpmudui-button-icon wpmudui-page-next"><i class="wpmudui-icon-chevron-right" aria-hidden="true"></i>
							</button>

						</div>

					</div>

				</div>

			</div>
			<?php } // end if pages ?>

			<?php if ( isset( $data['authors'] ) && count( $data['authors'] ) ) { ?>
				<!-- TAB: Top authors -->
				<div data-pane="authors" class="wpmudui-tab-content">

					<div class="wpmudui-search-form">
						<label class="wpmudui-label" for="wpmudui-analytics-authors-range"><?php esc_html_e( 'Show', 'wpmudev' ); ?></label>
						<select id="wpmudui-analytics-authors-range" class="wpmudui-select wpmudui-analytics-range">
							<option value="1"<?php selected( 1, $days_ago ); ?>><?php esc_html_e( 'Yesterday', 'wpmudev' ); ?></option>
							<option value="7"<?php selected( 7, $days_ago ); ?>><?php esc_html_e( 'Last 7 days', 'wpmudev' ); ?></option>
							<option value="30"<?php selected( 30, $days_ago ); ?>><?php esc_html_e( 'Last 30 days', 'wpmudev' ); ?></option>
							<option value="90"<?php selected( 90, $days_ago ); ?>><?php esc_html_e( 'Last 90 days', 'wpmudev' ); ?></option>
						</select>
						<label class="wpmudui-label" for="wpmudui-analytics-sites-type"><?php esc_html_e( 'data for', 'wpmudev' ); ?></label>
						<select id="wpmudui-analytics-authors-type" class="wpmudui-select wpmudui-analytics-column-filter">
							<?php if ( in_array( 'pageviews', $metrics, true ) ) : ?>
								<option value="pageviews"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'unique_pageviews', $metrics, true ) ) : ?>
								<option value="unique_pageviews" selected><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'bounce_rate', $metrics, true ) ) : ?>
								<option value="bounce_rate"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'exit_rate', $metrics, true ) ) : ?>
								<option value="exit_rate"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'page_time', $metrics, true ) ) : ?>
								<option value="page_time"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'gen_time', $metrics, true ) ) : ?>
								<option value="gen_time"><?php esc_html_e( 'Generation Time', 'wpmudev' ); ?></option>
							<?php endif; ?>
						</select>
					</div>

					<div class="wpmudui-table-flushed">

						<table class="wpmudui-table" data-rows="<?php echo esc_attr( count( $data['authors'] ) ); ?>">

							<thead>
							<tr>
								<th><?php esc_html_e( 'Author', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of times this page was visited.', 'wpmudev' ); ?>"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-unique_pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of visits that included this page. If a page was viewed multiple times during one visit, it is only counted once.', 'wpmudev' ); ?>"><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-bounce_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The percentage of visits that only had a single pageview. This means, that the visitor left the website directly from the entrance page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-exit_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'Number of exits divided by page views. Indicates percentage of exits from a specified page or average across your site.', 'wpmudev' ); ?>"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-page_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average amount of time visitors spent on a page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-gen_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average time it took to generate a page. Includes server generation time + visitors time to download the page from the server.', 'wpmudev' ); ?>"><?php esc_html_e( 'Gen. Time', 'wpmudev' ); ?></th>
							</tr>
							</thead>

							<tbody class="wpmudui-table-sortable">

							<?php foreach( $data['authors'] as $author ) { ?>
								<tr class="wpmudui-table-item wpmudui-tracking" data-filter-type="author" data-filter="<?php echo esc_attr( $author['filter'] ); ?>" data-label="<?php esc_attr_e( 'Author:', 'wpmudev' ); ?>">
									<td><img src="<?php echo esc_url( $author['gravatar'] ); ?>" width="25" height="25"><span><?php echo esc_html( $author['name'] ); ?></span></td>
									<?php if ( isset( $author['pageviews'] ) ) : ?>
										<td class="wpmudui-table-views data-pageviews" data-sort="<?php echo esc_attr( $author['pageviews']['sort'] ); ?>"><?php echo esc_html( $author['pageviews']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $author['unique_pageviews'] ) ) : ?>
										<td class="wpmudui-table-views data-unique_pageviews" data-sort="<?php echo esc_attr( $author['unique_pageviews']['sort'] ); ?>"><?php echo esc_html( $author['unique_pageviews']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $author['bounce_rate'] ) ) : ?>
										<td class="wpmudui-table-views data-bounce_rate" data-sort="<?php echo esc_attr( $author['bounce_rate']['sort'] ); ?>"><?php echo esc_html( $author['bounce_rate']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $author['exit_rate'] ) ) : ?>
										<td class="wpmudui-table-views data-exit_rate" data-sort="<?php echo esc_attr( $author['exit_rate']['sort'] ); ?>"><?php echo esc_html( $author['exit_rate']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $author['page_time'] ) ) : ?>
										<td class="wpmudui-table-views data-page_time" data-sort="<?php echo esc_attr( $author['page_time']['sort'] ); ?>"><?php echo esc_html( $author['page_time']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $author['gen_time'] ) ) : ?>
										<td class="wpmudui-table-views data-gen_time" data-sort="<?php echo esc_attr( $author['gen_time']['sort'] ); ?>"><?php echo esc_html( $author['gen_time']['value'] ); ?></td>
									<?php endif; ?>
								</tr>
							<?php } ?>

							</tbody>

						</table>

						<div class="wpmudui-pagination wpmudui-pagination-wrapper" data-current-page="1">

							<label for="wpmdui-pagination-search-pages" class="wpmudui-label"><?php esc_html_e( 'Go to', 'wpmudev' ); ?></label>

							<input type="number"
							       placeholder="1"
							       id="wpmdui-pagination-search-authors"
							       class="wpmudui-input wpmudui-goto-page">

							<div class="wpmudui-navigation">

								<label class="wpmudui-label">
									<span class="wpmudui-start-row">1</span>&nbsp;-&nbsp;
									<span class="wpmudui-end-row">10</span>&nbsp;
									<?php echo sprintf( __( 'of %s', 'wpmudev' ), number_format_i18n( count( $data['pages'] ) ) ); ?>
								</label>

								<button class="wpmudui-button wpmudui-button-icon wpmudui-page-prev"><i class="wpmudui-icon-chevron-left" aria-hidden="true"></i></button>

								<button class="wpmudui-button wpmudui-button-icon wpmudui-page-next"><i class="wpmudui-icon-chevron-right" aria-hidden="true"></i>
								</button>

							</div>

						</div>

					</div>

				</div>
			<?php } // end if authors ?>

			<?php if ( isset( $data['sites'] ) && count( $data['sites'] ) ) { ?>
				<!-- TAB: Top Sites -->
				<div data-pane="sites" class="wpmudui-tab-content">

					<div class="wpmudui-search-form">
						<label class="wpmudui-label" for="wpmudui-analytics-sites-range"><?php esc_html_e( 'Show', 'wpmudev' ); ?></label>
						<select id="wpmudui-analytics-sites-range" class="wpmudui-select wpmudui-analytics-range">
							<option value="1"<?php selected( 1, $days_ago ); ?>><?php esc_html_e( 'Yesterday', 'wpmudev' ); ?></option>
							<option value="7"<?php selected( 7, $days_ago ); ?>><?php esc_html_e( 'Last 7 days', 'wpmudev' ); ?></option>
							<option value="30"<?php selected( 30, $days_ago ); ?>><?php esc_html_e( 'Last 30 days', 'wpmudev' ); ?></option>
							<option value="90"<?php selected( 90, $days_ago ); ?>><?php esc_html_e( 'Last 90 days', 'wpmudev' ); ?></option>
						</select>
						<label class="wpmudui-label" for="wpmudui-analytics-sites-type"><?php esc_html_e( 'data for', 'wpmudev' ); ?></label>
						<select id="wpmudui-analytics-sites-type" class="wpmudui-select wpmudui-analytics-column-filter">
							<?php if ( in_array( 'pageviews', $metrics, true ) ) : ?>
								<option value="pageviews"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'unique_pageviews', $metrics, true ) ) : ?>
								<option value="unique_pageviews" selected><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'bounce_rate', $metrics, true ) ) : ?>
								<option value="bounce_rate"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'exit_rate', $metrics, true ) ) : ?>
								<option value="exit_rate"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'page_time', $metrics, true ) ) : ?>
								<option value="page_time"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></option>
							<?php endif; ?>
							<?php if ( in_array( 'gen_time', $metrics, true ) ) : ?>
								<option value="gen_time"><?php esc_html_e( 'Generation Time', 'wpmudev' ); ?></option>
							<?php endif; ?>
						</select>
					</div>

					<div class="wpmudui-table-flushed">

						<table class="wpmudui-table" data-rows="<?php echo esc_attr( count( $data['sites'] ) ); ?>">

							<thead>
							<tr>
								<th><?php esc_html_e( 'Site domain/name', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of times this page was visited.', 'wpmudev' ); ?>"><?php esc_html_e( 'Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-unique_pageviews wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The number of visits that included this page. If a page was viewed multiple times during one visit, it is only counted once.', 'wpmudev' ); ?>"><?php esc_html_e( 'Unique Pageviews', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-bounce_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The percentage of visits that only had a single pageview. This means, that the visitor left the website directly from the entrance page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Bounce Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-exit_rate wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'Number of exits divided by page views. Indicates percentage of exits from a specified page or average across your site.', 'wpmudev' ); ?>"><?php esc_html_e( 'Exit Rate', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-page_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average amount of time visitors spent on a page.', 'wpmudev' ); ?>"><?php esc_html_e( 'Time on Page', 'wpmudev' ); ?></th>
								<th class="wpmudui-table-views data-gen_time wpmudui-tooltip wpmudui-tooltip-top wpmudui-tooltip-top-right wpmudui-tooltip-constrained"
								    data-tooltip="<?php esc_attr_e( 'The average time it took to generate a page. Includes server generation time + visitors time to download the page from the server.', 'wpmudev' ); ?>"><?php esc_html_e( 'Gen. Time', 'wpmudev' ); ?></th>
							</tr>
							</thead>

							<tbody class="wpmudui-table-sortable">

							<?php foreach( $data['sites'] as $site ) { ?>
								<tr class="wpmudui-table-item wpmudui-tracking" data-filter-type="subsite" data-filter="<?php echo esc_attr( $site['filter'] ); ?>" data-label="<?php esc_attr_e( 'Site:', 'wpmudev' ); ?>">
									<td><span><?php echo esc_html( $site['name'] ); ?></span></td>
									<?php if ( isset( $site['pageviews'] ) ) : ?>
										<td class="wpmudui-table-views data-pageviews" data-sort="<?php echo esc_attr( $site['pageviews']['sort'] ); ?>"><?php echo esc_html( $site['pageviews']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $site['unique_pageviews'] ) ) : ?>
										<td class="wpmudui-table-views data-unique_pageviews" data-sort="<?php echo esc_attr( $site['unique_pageviews']['sort'] ); ?>"><?php echo esc_html( $site['unique_pageviews']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $site['bounce_rate'] ) ) : ?>
										<td class="wpmudui-table-views data-bounce_rate" data-sort="<?php echo esc_attr( $site['bounce_rate']['sort'] ); ?>"><?php echo esc_html( $site['bounce_rate']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $site['exit_rate'] ) ) : ?>
										<td class="wpmudui-table-views data-exit_rate" data-sort="<?php echo esc_attr( $site['exit_rate']['sort'] ); ?>"><?php echo esc_html( $site['exit_rate']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $site['page_time'] ) ) : ?>
										<td class="wpmudui-table-views data-page_time" data-sort="<?php echo esc_attr( $site['page_time']['sort'] ); ?>"><?php echo esc_html( $site['page_time']['value'] ); ?></td>
									<?php endif; ?>
									<?php if ( isset( $site['gen_time'] ) ) : ?>
										<td class="wpmudui-table-views data-gen_time" data-sort="<?php echo esc_attr( $site['gen_time']['sort'] ); ?>"><?php echo esc_html( $site['gen_time']['value'] ); ?></td>
									<?php endif; ?>
								</tr>
							<?php } ?>

							</tbody>

						</table>

						<div class="wpmudui-pagination wpmudui-pagination-wrapper" data-current-page="1">

							<label for="wpmdui-pagination-search-sites" class="wpmudui-label"><?php esc_html_e( 'Go to', 'wpmudev' ); ?></label>

							<input type="number"
							       placeholder="1"
							       id="wpmdui-pagination-search-sites"
							       class="wpmudui-input wpmudui-goto-page">

							<div class="wpmudui-navigation">

								<label class="wpmudui-label">
									<span class="wpmudui-start-row">1</span>&nbsp;-&nbsp;
									<span class="wpmudui-end-row">10</span>&nbsp;
									<?php echo sprintf( __( 'of %s', 'wpmudev' ), number_format_i18n( count( $data['pages'] ) ) ); ?>
								</label>

								<button class="wpmudui-button wpmudui-button-icon wpmudui-page-prev"><i class="wpmudui-icon-chevron-left" aria-hidden="true"></i></button>

								<button class="wpmudui-button wpmudui-button-icon wpmudui-page-next"><i class="wpmudui-icon-chevron-right" aria-hidden="true"></i>
								</button>

							</div>

						</div>

					</div>

				</div>
			<?php } // end if sites ?>

		</div>

		<?php } //end not an api error ?>

	</div>

</div>
