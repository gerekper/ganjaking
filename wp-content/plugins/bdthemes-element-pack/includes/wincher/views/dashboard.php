<div id="bdt-ep-wincher">

	<div id="bdt-wincher-login" class="bdt-wincher-connect-wrap bdt-hidden">
		<div class="bdt-wincher-connect-content">
			<h2 class="bdt-wincher-connect-title bdt-text-large bdt-text-secondary bdt-margin-small-bottom bdt-text-bold">
				SEO Performance</h2>
			<p class="bdt-wincher-connect-desc">Connect your Wincher account to get more data and insights</p>
			<button id="wincher-auth-save" type="button" class="bdt-wincher-connect-button bdt-border-rounded">
				Connect to Wincher
			</button>
		</div>
	</div>


	<!-- New Code -->
	<div id="bdt-wincher-data">

		<div class="bdt-margin-bottom" class="bdt-form-select">
			<select id="bdt-wincher-domains">
				<option value="0">Select Domain</option>
			</select>
		</div>
		<!-- Header area -->
		<div class="bdt-seo-dashboard-header-area">
			<div class="bdt-grid-item">
				<div class="bdt-card bdt-card-default bdt-border-rounded bdt-flex bdt-flex-between bdt-flex-wrap bdt-padding-25 bdt-keywords-item">
					<div class="bdt-keywords-info-item">
						<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
							Tracked Keywords
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
						<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
							<span id="bdt_keyword_count"></span>
							<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small  bdt-border-rounded">
								<span bdt-icon="icon: plus; ratio: 0.7"></span>
								1k
							</span> -->
						</span>
					</div>

					<div class="bdt-keywords-label-item-wrap">
						<div class="bdt-keywords-label-item bdt-flex bdt-flex-between">
							<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary">
								Ranking Keywords
								<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
							</span>
							<span class="bdt-performance bdt-margin-left  bdt-text-bold bdt-text-warning">
								<span id="bdt-wincher-ranking-keywords-count">0</span>
							</span>
						</div>
						<div class="bdt-keywords-label-item bdt-flex bdt-flex-between bdt-margin-small-top">
							<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary">
								Competition label
								<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
							</span>
							<span class="bdt-performance bdt-margin-left  bdt-text-bold ">
								<span id="bdt-wincher-competition-level"></span>
							</span>
						</div>
					</div>
					<div class="w-100" style="height: 120px; width:100%;">
						<canvas id="bdt-ep-wincher-keyword-count-history"></canvas>
					</div>
				</div>
			</div>

			<!-- <div class="bdt-grid-item">
				<div class="bdt-card bdt-card-default  bdt-border-rounded bdt-flex bdt-flex-between bdt-padding-25 bdt-position-change-item">
					<div class="bdt-keywords-info-item">
						<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
							<s>Tracked Keywords</s>
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
						<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
							2.2k
							<span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary  bdt-border-rounded">
								<span bdt-icon="icon: chevron-up; ratio: 1"></span>
								1k
							</span>
						</span>
					</div>

				</div>
			</div> -->

			<div class="bdt-grid-item">
				<div class="bdt-card bdt-card-default bdt-border-rounded bdt-padding-25">
					<div class="bdt-keywords-info-item bdt-flex bdt-flex-between">
						<div>
							<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
								Pages
								<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
							</span>
							<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
								<span id="bdt_ranking_pages"></span>
								<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small bdt-border-rounded">
									<span bdt-icon="icon: chevron-up; ratio: 1"></span>
									112
								</span> -->
							</span>
						</div>
						<div>
							<a class="bdt-text-warning bdt-external-link-btn" href="https://app.wincher.com/websites" target="_blank">View all
								<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
								</svg>
							</a>
						</div>
					</div>
					<div class="w-100" style="height: 120px; width:100%;">
						<canvas id="bdt-ep-wincher-ranking-pages-history"></canvas>
					</div>
				</div>
			</div>
		</div>

		<div class="bdt-ep-graph-tabs bdt-seo-tabs-wrap bdt-margin-top bdt-flex">
			<div class="bdt-ep-tab-switcher bdt-grid-item bdt-card bdt-card-default bdt-padding-25 bdt-border-rounded bdt-flex bdt-flex-between bdt-active" data-tab-index="0">
				<div class="bdt-estimated-traffic-info-item">
					<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
						Estimated Traffic
						<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
					</span>
					<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
						<span id="bdt_estimated_traffic"></span>
						<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small bdt-border-rounded">
							<span bdt-icon="icon: chevron-up; ratio: 1"></span>
							640
						</span> -->
					</span>
				</div>
			</div>

			<div class="bdt-ep-tab-switcher bdt-grid-item bdt-card bdt-card-default bdt-padding-25 bdt-border-rounded bdt-flex bdt-flex-between" data-tab-index="1">
				<div class="bdt-traffic-value-info-item">
					<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
						Traffic Value
						<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
					</span>
					<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
						$<span id="bdt_traffic_value"></span>
						<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small bdt-border-rounded">
							<span bdt-icon="icon: chevron-up; ratio: 1"></span>
							776
						</span> -->
					</span>
				</div>
			</div>

			<div class="bdt-ep-tab-switcher bdt-grid-item bdt-card bdt-card-default bdt-padding-25 bdt-border-rounded bdt-flex bdt-flex-between" data-tab-index="2">
				<div class="bdt-taverage-position-info-item">
					<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
						Average Position
						<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
					</span>
					<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
						<span id="bdt_avg_position"></span>
						<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small bdt-border-rounded">
							<span bdt-icon="icon: chevron-up; ratio: 1"></span>
							56.9
						</span> -->
					</span>
				</div>
			</div>

			<div class="bdt-ep-tab-switcher bdt-grid-item bdt-card bdt-card-default bdt-padding-25 bdt-border-rounded bdt-flex bdt-flex-between" data-tab-index="3">
				<div class="bdt-share-info-item">
					<span class="bdt-title  bdt-flex bdt-flex-middle bdt-text-secondary bdt-margin-small-bottom">
						Share of voice
						<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
					</span>
					<span class="bdt-performance-wrap bdt-flex bdt-flex-middle bdt-text-secondary bdt-text-large bdt-text-bold">
						<span id="bdt_share_of_voice"></span>%
						<!-- <span class="bdt-badge-count bdt-flex bdt-flex-middle bdt-margin-small-left bdt-background-primary bdt-text-small bdt-border-rounded">
							<span bdt-icon="icon: chevron-up; ratio: 1"></span>
							0.02
						</span> -->
					</span>
				</div>
			</div>

		</div>

		<div class="bdt-seo-table-wrap bdt-seo-middle-table bdt-margin-top bdt-ep-graph-tabs-container-wrap">
			<div id="bdt-ep-graph-tabs-container" class="bdt-ep-graph-wrapper bdt-padding-25">
				<!-- Graph View -->
				<div class="bdt-ep-tab-container bdt-graph-item" data-tab-index="0">
					<div class="w-100" style="height: 400px; width:100%;">
						<canvas id="bdt-ep-wincher-traffic-graph"></canvas>
					</div>
				</div>
				<div class="bdt-ep-tab-container bdt-graph-item bdt-hidden" data-tab-index="1">
					<div class="w-100" style="height: 400px; width:100%;">
						<canvas id="bdt-ep-wincher-traffic-value-graph"></canvas>
					</div>
				</div>
				<div class="bdt-ep-tab-container bdt-graph-item bdt-hidden" data-tab-index="2">
					<div class="w-100" style="height: 400px; width:100%;">
						<canvas id="bdt-ep-wincher-traffic-average-position-graph"></canvas>
					</div>
				</div>
				<div class="bdt-ep-tab-container bdt-graph-item bdt-hidden" data-tab-index="3">
					<div class="w-100" style="height: 400px; width:100%;">
						<canvas id="bdt-ep-wincher-share-voice-graph"></canvas>
					</div>
				</div>
				<!-- /Graph View -->
			</div>

			<div class="bdt-seo-table-item bdt-wincher-top-cp-item">
				<!-- Top Competitors -->

				<div class="bdt-competitors-wrap bdt-card bdt-card-default bdt-padding-25 bdt-border-rounded" style="height:100%;">

					<div class="bdt-flex bdt-flex-between">
						<div>
							<span class="bdt-title bdt-flex bdt-flex-middle bdt-text-secondary">Top Competitors</span>
						</div>
						<!-- <div>
								<a class="bdt-text-warning" href="#">Add Competitors</a>
							</div> -->
					</div>

					<div id="bdt-wincher-competitor-list">
						<div class="bdt-flex bdt-flex-middle bdt-flex-between bdt-competitors-name-wrap bdt-margin-top">
							<div class="bdt-flex bdt-flex-middle bdt-border-rounded bdt-competitors-name bdt-text-secondary bdt-background-75" style="width:100%;">
								<div class="bdt-wincher-tc-logo"></div>
								<span class="">Data Not Found!</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Table area -->

		<div class="bdt-seo-table-wrap bdt-seo-middle-table bdt-margin-top">
			<div class="bdt-grid-item bdt-seo-table-item">
				<div class="bdt-table-header-content bdt-flex bdt-flex-between">
					<div>
						<span class="bdt-title bdt-flex bdt-flex-middle bdt-text-secondary">
							Ranking Keywords
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
					</div>
					<div>
						<a class="bdt-text-warning bdt-external-link-btn" href="https://app.wincher.com/websites" target="_blank">View all
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
							</svg>
						</a>
					</div>
				</div>
				<div class="bdt-overflow-auto">
					<table class="bdt-table bdt-table-striped">
						<thead>
							<tr>
								<th class="bdt-text-secondary bdt-text-capitalize">Keyword</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-center">Position</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-right">Est. traffic</th>
							</tr>
						</thead>
						<tbody id="bdt-wincher-ranking-keywords-list">
							<tr>
								<td colspan="3" class="bdt-text-center bdt-text-secondary">No data found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- <div class="bdt-grid-item bdt-seo-table-item">
				<div class="bdt-table-header-content bdt-flex bdt-flex-between">
					<div>
						<span class="bdt-title bdt-flex bdt-flex-middle bdt-text-secondary">
							Traffic growth
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
					</div>
					<div>
						<a class="bdt-text-warning bdt-external-link-btn" href="#">View all
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
							</svg>
						</a>
					</div>
				</div>
				<div class="bdt-overflow-auto">
					<table class="bdt-table bdt-table-striped">
						<thead>
							<tr>
								<th class="bdt-text-secondary bdt-text-capitalize">Keyword</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-center">Position</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-right">Est. traffic</th>
							</tr>
						</thead>
						<tbody id="bdt-wincher-traffic-growth-list">
						</tbody>
					</table>
				</div>
			</div> -->

			<div class="bdt-grid-item bdt-seo-table-item">
				<div class="bdt-table-header-content bdt-flex bdt-flex-between">
					<div>
						<span class="bdt-title bdt-flex bdt-flex-middle bdt-text-secondary">
							Traffic loss
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
					</div>
					<div>
						<a class="bdt-text-warning bdt-external-link-btn" href="https://app.wincher.com/websites" target="_blank">View all
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
							</svg>
						</a>
					</div>
				</div>
				<div class="bdt-overflow-auto">
					<table class="bdt-table bdt-table-striped">
						<thead>
							<tr>
								<th class="bdt-text-secondary bdt-text-capitalize">Keyword</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-center">Position</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-right">Est. traffic</th>
							</tr>
						</thead>
						<tbody id="bdt-wincher-traffic-loss-list">
							<tr>
								<td colspan="3" class="bdt-text-center bdt-text-secondary">No data found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="bdt-grid-item bdt-seo-table-item">
				<div class="bdt-table-header-content bdt-flex bdt-flex-between">
					<div>
						<span class="bdt-title bdt-flex bdt-flex-middle bdt-text-secondary">
							Traffic Opportunities
							<span class="bdt-margin-small-left bdt-text-warning" bdt-icon="icon: question; ratio: 0.8"></span>
						</span>
					</div>
					<div>
						<a class="bdt-text-warning bdt-external-link-btn" href="https://app.wincher.com/websites" target="_blank">View all
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
							</svg>
						</a>
					</div>
				</div>
				<div class="bdt-overflow-auto">
					<table class="bdt-table bdt-table-striped">
						<thead>
							<tr>
								<th class="bdt-text-secondary bdt-text-capitalize">Keyword</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-center">Position</th>
								<th class="bdt-text-secondary bdt-text-capitalize bdt-text-right">Volume</th>
							</tr>
						</thead>
						<tbody id="bdt-wincher-traffic-opportunities-list">
							<tr>
								<td colspan="3" class="bdt-text-center bdt-text-secondary">No data found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>

		<!-- Graph area -->
		<div class="bdt-seo-table-wrap bdt-seo-footer-table bdt-margin-top">
			<div class="bdt-keyword-table-header-content bdt-flex bdt-flex-between">
				<!-- <div>
					<span class="bdt-table-title bdt-flex bdt-flex-middle bdt-text-secondary">
						<strong id="bdt-wincher-keywords-count" class="bdt-margin-small-right"></strong>Keywords
					</span>
				</div> -->
				<!-- <div class="bdt-flex bdt-flex-middle">
					<a class="bdt-text-secondary bdt-seo-d-s-btn bdt-border-rounded" href="#">
						<span bdt-icon="icon: download; ratio: 1"></span>
					</a>
					<a class="bdt-text-secondary bdt-seo-d-s-btn bdt-border-rounded bdt-margin-small-left" href="#">
						<span bdt-icon="icon: cog; ratio: 1"></span>
					</a>
				</div> -->
			</div>
			<div class="bdt-overflow-auto bdt-border-rounded">
				<table id="bdt-wincher-keywords" class="bdt-table bdt-table-striped">
					<thead>
						<tr>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title">
								Keyword
								(<strong id="bdt-wincher-keywords-count"></strong>)
								<span class="bdt-text-muted" bdt-icon="icon: triangle-up; ratio: 1"></span>
							</th>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title bdt-text-center">
								<span class="bdt-text-warning bdt-warning-icon" bdt-icon="icon: question; ratio: 0.8"></span>
								Intent
							</th>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title bdt-text-center">
								<span class="bdt-text-warning bdt-warning-icon" bdt-icon="icon: question; ratio: 0.8"></span>
								Position
							</th>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title bdt-text-center">
								<span class="bdt-text-warning bdt-warning-icon" bdt-icon="icon: question; ratio: 0.8"></span>
								CPC
								<span class="bdt-text-muted" bdt-icon="icon: triangle-down; ratio: 1"></span>
							</th>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title bdt-text-center">
								Volume
								<span class="bdt-text-secondary" bdt-icon="icon: triangle-down; ratio: 1"></span>
							</th>
							<th class="bdt-text-secondary bdt-text-capitalize bdt-table-head-title bdt-text-right">
								Traffic
								<span class="bdt-text-secondary" bdt-icon="icon: triangle-down; ratio: 1"></span>
							</th>
						</tr>
					</thead>
					<tbody id="bdt-keywords-list">
						<tr>
							<td colspan="6" class="bdt-text-center bdt-text-secondary">No data found</td>
						</tr>
					</tbody>
				</table>
				<div id="bdt-wk-pagination-container"></div>
			</div>
		</div>
	</div>
	<!-- /New Code -->

</div>