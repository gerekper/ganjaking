<template>

	<div class="searchwp-metrics wrap">

		<div class="searchwp-metrics__title">
			<h1>SearchWP Metrics</h1>
			<v-popover v-if="'1' === canEditSettings"
				:popover-wrapper-class="'searchwp-metrics-popover'"
				:placement="'bottom'"
			>
				<button class="button"><span class="dashicons dashicons-menu"></span></button>
				<template slot="popover">
					<ul>
						<li>
							<button
								class="searchwp-metrics-nonbutton"
								v-close-popover
								@click.prevent="showingClearMetricsData = true"
							>
								{{ i18n.clearMetricsData }}
							</button>
						</li>
						<li v-if="ignoredQueries && ignoredQueries.length">
							<button
								class="searchwp-metrics-nonbutton"
								v-close-popover
								@click.prevent="showingClearIgnoredQueries = true"
							>
								{{ i18n.removeAllIgnoredQueries }}
							</button>
						</li>
						<li>
							<button
								class="searchwp-metrics-nonbutton"
								v-close-popover
								@click.prevent="showingModifyLoggingRules = true"
							>
								{{ i18n.modifyLoggingRules }}
							</button>
						</li>
						<li>
							<button
								class="searchwp-metrics-nonbutton"
								v-close-popover
								@click.prevent="showingSettings = true"
							>
								{{ i18n.settings }}
							</button>
						</li>
					</ul>
				</template>
			</v-popover>

			<vue-modaltor
				:visible="showingClearMetricsData"
				@hide="hideClearMetricsData"
				:default-width="'300px'">
				<div class="searchwp-metrics__modal searchwp-metrics__modal-confirmation">
					<h4><span class="dashicons dashicons-arrow-right"></span> {{ i18n.areYouSure }} <span class="dashicons dashicons-arrow-left"></span></h4>
					<component :is="translatedClearMetricsDataNote"></component>
					<ul class="searchwp-metrics__modal-confirmation--actions">
						<li>
							<button @click.prevent="clearMetricsData" class="button">
								{{ i18n.clearData }}
							</button>
						</li>
						<li>
							<button @click.prevent="hideClearMetricsData" class="searchwp-metrics-nonbutton">
								{{ i18n.cancel }}
							</button>
						</li>
					</ul>
				</div>
			</vue-modaltor>

			<vue-modaltor
				:visible="showingClearIgnoredQueries"
				@hide="hideClearIgnoredQueries"
				:default-width="'300px'">
				<div class="searchwp-metrics__modal searchwp-metrics__modal-confirmation">
					<h4><span class="dashicons dashicons-arrow-right"></span> {{ i18n.areYouSure }} <span class="dashicons dashicons-arrow-left"></span></h4>
					<component :is="translatedClearIgnoredQueriesNote"></component>
					<ul class="searchwp-metrics__modal-confirmation--actions">
						<li>
							<button @click.prevent="clearIgnoredQueries" class="button">
								{{ i18n.clearData }}
							</button>
						</li>
						<li>
							<button @click.prevent="hideClearIgnoredQueries" class="searchwp-metrics-nonbutton">
								{{ i18n.cancel }}
							</button>
						</li>
					</ul>
				</div>
			</vue-modaltor>

			<vue-modaltor
				:visible="showingModifyLoggingRules"
				@hide="hideModifyLoggingRules"
				:default-width="'400px'">
				<div class="searchwp-metrics__modal searchwp-metrics__modal-logging">
					<h4>{{ i18n.loggingRules }}</h4>
					<p>{{ i18n.loggingRulesNote }}</p>
					<p><span class="dashicons dashicons-info"></span> <component :is="translatedLoggingRulesNoteDetails"></component></p>
					<div class="searchwp-metrics__textarea">
						<label for="searchwp_metrics_ip_blocklist">{{ i18n.userIdRoleBlocklist }}</label>
						<textarea v-model="ignoredRoles" name="searchwp_metrics_role_blocklist" id="searchwp_metrics_role_blocklist" cols="30" rows="10"></textarea>
						<p class="description">{{ i18n.userIdRoleBlocklistNote }}</p>
					</div>
					<div class="searchwp-metrics__textarea">
						<label for="searchwp_metrics_ip_blocklist">{{ i18n.ipBlocklist }}</label>
						<textarea v-model="ignoredIps" name="searchwp_metrics_ip_blocklist" id="searchwp_metrics_ip_blocklist" cols="30" rows="10"></textarea>
						<p class="description">{{ i18n.ipBlocklistNote }}</p>
					</div>
					<ul class="searchwp-metrics__modal-confirmation--actions">
						<li>
							<button @click.prevent="hideModifyLoggingRules" class="button">
								{{ i18n.saveClose }}
							</button>
						</li>
					</ul>
				</div>
			</vue-modaltor>

			<vue-modaltor
				:visible="showingSettings"
				@hide="hideSettings"
				:default-width="'400px'">
				<div class="searchwp-metrics__modal searchwp-metrics__modal-settings">
					<h4>{{ i18n.settings }}</h4>
					<div
						v-if="clickTrackingBuoyApplicable"
						class="searchwp-metrics__checkbox">
						<input
							v-model="clickTrackingBuoy"
							type="checkbox"
							name="searchwp_metrics_click_track_buoy"
							id="searchwp_metrics_click_track_buoy"
						/>
						<div class="searchwp-metrics__checkbox-label">
							<label for="searchwp_metrics_click_track_buoy">{{ i18n.clickTrackingBuoy }}</label>
							<p class="description">{{ i18n.clickTrackingBuoyLabelNote }}</p>
						</div>
					</div>
					<div v-else
						class="searchwp-metrics__note">
						<span class="dashicons dashicons-info"></span>
						<div>
							<p>{{ i18n.clickTrackingBuoyUnavailable }}</p>
						</div>
					</div>
					<div class="searchwp-metrics__checkbox">
						<input
							v-model="clearDataOnUninstall"
							type="checkbox"
							name="searchwp_metrics_clear_data_on_uninstall"
							id="searchwp_metrics_clear_data_on_uninstall"
						/>
						<div class="searchwp-metrics__checkbox-label">
							<label for="searchwp_metrics_clear_data_on_uninstall">{{ i18n.removeOnUninstallation }}</label>
							<p class="description">{{ i18n.removeOnUninstallationLabelNote }}</p>
						</div>
					</div>
					<ul class="searchwp-metrics__modal-confirmation--actions">
						<li>
							<button @click.prevent="hideSettings" class="button">
								{{ i18n.saveClose }}
							</button>
						</li>
					</ul>
				</div>
			</vue-modaltor>

		</div>

		<div class="searchwp-metrics__controls">

			<div class="searchwp-metrics__control">
				<h4>{{ i18n.dateRange }}</h4>
				<vue-datepicker-local
					v-model="dateRange"
					:range-separator="i18n.to"
					:local="i18n.datePicker"
					show-buttons
					@confirm="update"
				></vue-datepicker-local>
			</div>

			<div class="searchwp-metrics__control">
				<h4>{{ i18n.searchQueryControls }}</h4>
				<div class="searchwp-metrics__control-queries">
					<multiselect
						v-model="selectedSearchQueries"
						id="searchwp-metrics-query-limiter"
						label="query"
						track-by="id"
						:placeholder="i18n.limitMetricsToQueries"
						open-direction="bottom"
						:options="searchQueries"
						:multiple="true"
						:searchable="true"
						:loading="isLoadingSearchSearches"
						:internal-search="false"
						:clear-on-select="false"
						:close-on-select="false"
						:options-limit="300"
						:limit="3"
						:max-height="300"
						:show-no-results="false"
						:hide-selected="true"
						@search-change="searchSearchQueries"
						:taggable="true"
						:tag-placeholder="i18n.addAsPartialMatch"
						@tag="addSelectedSearchQuery"
						@input="update"
					></multiselect>
					<button class="button" @click="showingIgnoredSearches = true">{{ i18n.ignored }}: {{ Object.keys(ignoredQueries).length }}</button>
					<vue-modaltor
						:visible="showingIgnoredSearches"
						@hide="hideIgnoredSearches"
						:default-width="'600px'">
						<div class="searchwp-metrics__modal searchwp-metrics__ignored-searches-details">
							<h4>{{ i18n.ignoredSearches }}</h4>
							<p>{{ i18n.ignoredMessage }}</p>
							<table v-if="ignoredQueries && ignoredQueries.length">
								<thead>
									<tr>
										<th>{{ i18n.ignoredSearchQuery }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(ignoredQuery, ignoredQueryIndex) in ignoredQueries"
										:key="'ignored' + ignoredQueryIndex"
										v-if="!ignoredQuery.unignored">
										<td>
											<delete :title="i18n.stopIgnoringQuery" v-on:onclick="unIgnoreQuery(ignoredQuery.hash)"></delete>
											{{ ignoredQuery.query }}
										</td>
									</tr>
								</tbody>
							</table>
							<div v-else
								class="searchwp-metrics__note">
								<span class="dashicons dashicons-info"></span>
								<div>
									<p>{{ i18n.noIgnoredQueries }}</p>
								</div>
							</div>
						</div>
					</vue-modaltor>
				</div>
			</div>

			<div class="searchwp-metrics__control">
				<h4>{{ i18n.enginesToDisplay }}</h4>
				<multiselect
					v-model="multiselect.engines.value"
					:options="multiselect.engines.options"
					:multiple="true"
					:close-on-select="true"
					:hide-selected="true"
					:placeholder="i18n.chooseEngine"
					label="label"
					track-by="name"
					:searchable="false"
					:allow-empty="false"
					@input="update"
				></multiselect>
			</div>

		</div>

		<div class="searchwp-metrics__details">

			<div
				v-if="!sameDate"
				v-bind:class="['searchwp-metrics__searches-over-time', searchesOverTime ? '' : 'searchwp-metrics__is-loading']">
				<line-chart
					:datacollection="searchesOverTime"
					:options="searchesOverTimeOptions"
					:height="'300px'"
				></line-chart>
			</div>

			<div class="searchwp-metrics__engine-details"
				v-if="engines && !loading"
				v-for="(engine, engineIndex) in engines" :key="engine.name">

				<div class="searchwp-metrics__engine-details-heading-group">
					<h3 class="searchwp-metrics__engine-details-heading">
						<span class="searchwp-metrics__engine-details-legend" v-bind:style="{ backgroundColor: engine.color }"></span>
						<component :is="translatedEngineDetailsHeading" :props="engine"></component>
					</h3>
					<v-popover
						:popover-wrapper-class="'searchwp-metrics-popover'"
						:placement="'left'"
					>
						<button class="button"><span class="dashicons dashicons-menu"></span></button>
						<template slot="popover">
							<ul>
								<li>
									<json-csv
										:data   = "formattedSearchesOverTimeForCsv(engineIndex)"
										:fields = "searchesOverTimeJsonFields"
										type    = "csv"
										:name    = "appendTimestamp('SearchesOverTime_' + engine.name) + '.csv'">
										{{ i18n.exportSearchesOverTime }}
									</json-csv>
								</li>
								<li>
									<json-csv
										:data   = "formattedEngineStatisticForCsv(engineIndex, engine)"
										:fields = "engineStatisticsJsonFields"
										type    = "csv"
										:name    = "appendTimestamp('EngineStatistics_' + engine.name) + '.csv'">
										{{ i18n.exportEngineStatistics }}
									</json-csv>
								</li>
								<li>
									<json-csv
										:data   = "formattedPopularSearchesForCsv(engineIndex)"
										:fields = "popularSearchesJsonFields"
										type    = "csv"
										:name    = "appendTimestamp('PopularSearches') + '.csv'">
										{{ i18n.exportPopularSearches }}
									</json-csv>
								</li>
							</ul>
						</template>
					</v-popover>
				</div>

				<div v-if="0 == getTotalSearchesCount( engineIndex )" class="searchwp-metrics__no-data">
					<p>{{ i18n.notEnoughData }}</p>
				</div>
				<div v-else class="searchwp-metrics__engine-details-hook">
					<div class="searchwp-metrics__engine-details-alpha">
						<div class="searchwp-metrics__engine-details--heading">
							<h4>{{ i18n.engineStatistics }}</h4>
						</div>
						<div class="searchwp-metrics__stats-grid">
							<div>
								<dl>
									<dt>{{ i18n.totalSearches }}</dt>
									<dd>{{ getTotalSearchesCount( engineIndex ) }}</dd>
								</dl>
							</div>
							<div>
								<dl>
									<dt>{{ i18n.noResultsSearches }}</dt>
									<dd>
										<div class="searchwp-metrics__flex">
											<div>{{ getFailedSearchesCount( engineIndex ) }}</div>
											<div>
												<button
													@click="showingFailedSearches = engine.name"
													class="searchwp-trigger__external">
													<span class="dashicons dashicons-external"></span>
													<span class="screen-reader-text">{{ i18n.viewNoResultsSearches }}</span>
												</button>
											</div>
										</div>
									</dd>
								</dl>
								<vue-modaltor
									:visible="showingFailedSearches === engine.name"
									@hide="hideFailedSearches"
									:default-width="'600px'">
									<div class="searchwp-metrics__modal searchwp-metrics__failed-searches-details">
										<component :is="translatedNoResultsSearchesEngineHeading" :props="engine"></component>
										<div class="searchwp-metrics__engine-details--heading"
										v-if="getFailedSearches(engine.name) && getFailedSearches(engine.name).length"
										>
											<component :is="translatedNoResultsSearchesEngineNote"></component>
											<json-csv class="button"
												:data   = "noResultsDetailsForExport"
												:fields = "noResultsDetailsJsonFields"
												type    = "csv"
												:name    = "appendTimestamp('NoResultsSearches') + '.csv'">
												<span class="dashicons dashicons-download"></span>
											</json-csv>
										</div>
										<table v-if="getFailedSearches(engine.name) && getFailedSearches(engine.name).length">
											<thead>
												<tr>
													<th>{{ i18n.searchQuery }}</th>
													<th>{{ i18n.searches }}</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="(failedSearch, failedSearchIndex) in getFailedSearches(engine.name)"
													:key="'failed' + failedSearchIndex + engine.name"
													v-bind:id="'searchwp-metrics--hook-failed-' + failedSearchIndex">
													<td>
														<delete title="Ignore this query" v-on:onclick="ignoreFailedSearch(failedSearch.query, engine.name, failedSearchIndex)"></delete>
														{{ failedSearch.query }}
													</td>
													<td>{{ failedSearch.count }}</td>
												</tr>
											</tbody>
										</table>
										<div v-else
											class="searchwp-metrics__note">
											<span class="dashicons dashicons-info"></span>
											<div>
												<p>{{ i18n.noFailedSearches }}</p>
											</div>
										</div>
									</div>
								</vue-modaltor>
							</div>
							<div>
								<dl>
									<dt>{{ i18n.totalResultsViewed }}</dt>
									<dd>{{ outputMetric( totalClicks[ engine.name ].statistic ) }}</dd>
								</dl>
							</div>
							<div>
								<dl>
									<dt>
										<tooltip :content="i18n.searchesPerUserNote">{{ i18n.searchesPerUser }}</tooltip>
									</dt>
									<dd>{{ outputMetric( averageSearchesPerUser[ engine.name ].statistic ) }}</dd>
								</dl>
							</div>
							<div>
								<dl>
									<dt>{{ i18n.clicksPerSearch }}</dt>
									<dd>{{ outputMetric( averageClicksPerSearch[ engine.name ].statistic ) }}</dd>
								</dl>
							</div>
							<div>
								<dl>
									<dt>{{ i18n.averageClickRank }}</dt>
									<dd>{{ outputMetric( averageClickRank[ engine.name ].statistic ) }}</dd>
								</dl>
							</div>
						</div>
						<div class="searchwp-metrics__note" v-if="engineHasNoTracking( engine.name )">
							<span class="dashicons dashicons-info"></span>
							<div>
								<component :is="translatedClickTrackingNote"></component>
							</div>
						</div>
					</div>
					<div class="searchwp-metrics__engine-details-beta searchwp-metrics__engine-popular-searches">
						<div class="searchwp-metrics__engine-details--heading">
							<h4>{{ i18n.popularSearches }}</h4>
							<div>
								<button class="button" @click.prevent="showPopularSearchDetails(engine)">{{ i18n.viewMore }}</button>
							</div>
						</div>
						<div v-if="getPopularSearches(engine.name)" class="searchwp-metrics__chart-donut-wrapper">
							<div>
								<table class="searchwp-metrics-table">
									<tbody>
										<tr v-for="(popularSearch, popularSearchIndex) in getPopularSearches(engine.name).labels" :key="'popularSearch' + engine.name + popularSearchIndex">
											<td>
												<legend-indicator
													:index="popularSearchIndex"
													:text="popularSearch"
													v-on:onclick="showPopularSearchDetails(engine, popularSearch)"
												></legend-indicator>
											</td>
											<td><delete v-on:onclick="ignoreSearch(popularSearch)"></delete></td>
											<td>{{ getPopularSearches(engine.name).datasets[0].data[ popularSearchIndex ] }}</td>
										</tr>
									</tbody>
								</table>
								<vue-modaltor
									:visible="showingPopularSearchDetails && showingPopularSearchDetails.engine.name === engine.name"
									@hide="hidePopularSearchDetails"
									:default-width="'800px'">
									<div v-bind:class="['searchwp-metrics__modal searchwp-metrics__popular-search-details', loadingPopularSearchDetails ? 'searchwp-metrics__is-loading-details' : '']"
										v-if="showingPopularSearchDetails"
									>
										<div class="searchwp-metrics__loading-details-container">
											<component :is="translatedPopularSearchDetailsHeading" :props="engine"></component>
											<div class="searchwp-metrics__engine-details--heading">
												<p>{{ i18n.popularSearchDetailsNote }}</p>
												<div>
													<input type="text"
														v-model.number="popularSearchesCount"
														@keyup.enter="updatePopularSearchDetails(showingPopularSearchDetails.engine.name)">
													<button class="button"
														@click.prevent="updatePopularSearchDetails(showingPopularSearchDetails.engine.name)">
														<span class="dashicons dashicons-update"></span>
													</button>
													<json-csv class="button"
														:data   = "popularSearchesDetailsForExport"
														:fields = "popularSearchesDetailsJsonFields"
														type    = "csv"
														:name    = "appendTimestamp('PopularSearchesDetails') + '.csv'">
														<span class="dashicons dashicons-download"></span>
													</json-csv>
												</div>
											</div>
											<v-collapse-group>
												<div class="searchwp-metrics__split">
													<p class="searchwp-metrics__guide">{{ i18n.searchQuery }}</p>
													<p class="searchwp-metrics__guide">{{ i18n.searches }}</p>
												</div>
												<div class="searchwp-metrics__popular-search-details searchwp-metrics__accordion">
													<v-collapse-wrapper
														v-for="(popularSearchDetail, popularSearchDetailIndex) in popularSearchesDetails"
														v-bind:active="popularSearchDetail.query.query === showingPopularSearchDetails.query"
														v-bind:id="'searchwp-metrics--hook-popular-' + popularSearchDetailIndex"
														:key="'popularSearchDetail' + showingPopularSearchDetails.engine.name + popularSearchDetailIndex">
														<div class="searchwp-metrics__accordion--header">
															<div class="searchwp-metrics__accordion--trigger" v-collapse-toggle>
																<div class="searchwp-metrics__accordion--header-icon">
																	<span class="dashicons dashicons-arrow-right"></span>
																</div>
																<h5 class="searchwp-metrics__accordion--header-title">
																	{{ popularSearchDetail.query.query }}
																</h5>
																<div class="searchwp-metrics__accordion--header-figure">
																	{{ popularSearchDetail.query.searchcount }}
																</div>
															</div>
															<div class="searchwp-metrics__accordion--actions">
																<delete v-on:onclick="ignorePopularSearch(popularSearchDetail.query.query, popularSearchDetailIndex)"></delete>
															</div>
														</div>
														<div class="searchwp-metrics__accordion--content" v-collapse-content>
															<div class="searchwp-metrics__inner">
																<div v-if="popularSearchDetail.clicks.length">
																	<bar-chart
																		:datacollection="buildChartDataset(popularSearchDetail.clicks)"
																		:options="popularSearchDetailsClicksOptions"
																		:height="'30px'"
																	></bar-chart>
																	<table>
																		<thead>
																			<tr>
																				<th class="searchwp-metrics--primary-col">{{ i18n.clickedResult }}</th>
																				<th class="searchwp-metrics--secondary-col">{{ i18n.clicks }}</th>
																				<th class="searchwp-metrics--secondary-col">{{ i18n.conversionRate }}</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr v-for="(popularSearchDetailClicks, popularSearchDetailClicksIndex) in popularSearchDetail.clicks"
																				:key="'popularSearchDetailClicks' + showingPopularSearchDetails.engine.name + popularSearchDetailClicksIndex">
																				<td class="searchwp-metrics--primary-col">
																					<a :href="popularSearchDetailClicks.permalink" target="_blank">
																						<legend-indicator
																							:index="popularSearchDetailClicksIndex"
																							:text="popularSearchDetailClicks.post_title"
																						></legend-indicator>
																					</a>
																				</td>
																				<td class="searchwp-metrics--secondary-col">{{ popularSearchDetailClicks.clicks }}</td>
																				<td class="searchwp-metrics--secondary-col">{{ (( popularSearchDetailClicks.clicks * 100 ) / popularSearchDetail.query.searchcount).toFixed(2) }}%</td>
																			</tr>
																		</tbody>
																	</table>
																</div>
																<div v-else class="searchwp-metrics__no-data">
																	<p>{{ i18n.noClicks }}</p>
																</div>
															</div>
														</div>
													</v-collapse-wrapper>
												</div>
											</v-collapse-group>
										</div>
										<div class="searchwp-metrics__loading-details-loader">
											<spinner
												:size="55"
												:line-size="6"
											></spinner>
										</div>
									</div>
								</vue-modaltor>
							</div>
							<div>
								<doughnut-chart
									:datacollection="getPopularSearches(engine.name)"
									:options="popularSearchesOptions"
								></doughnut-chart>
								<div class="searchwp-metrics__engine-popular-searches-coverage">
									<span>{{ getPopularSearchesPercentage(engine.name, engineIndex) }}%</span> {{ i18n.ofAllSearches }}
								</div>
							</div>
						</div>
					</div>
					<div class="searchwp-metrics__engine-details-omega searchwp-metrics__engine-suggestions">
						<div v-if="getPopularClicks(engine.name)">
							<div class="searchwp-metrics__engine-details--heading">
								<h4>{{ i18n.insights }}</h4>
							</div>
							<div class="searchwp-metrics__engine-suggestions-insights">
								<div v-if="getInsights(engine.name) && getInsights(engine.name).length">
									<ul>
										<li class="searchwp-metrics__engine-suggestions-insight"
											v-for="(insight, insightIndex) in getInsights(engine.name)"
											:key="'insight' + engine.name + insightIndex">
											<insight-underdog
												v-if="insight.type === 'underdog'"
												:post-count="insight.postCount"
												v-on:onclick="showInsightDetails(engine, insightIndex)"
											></insight-underdog>
											<insight-popular
												v-if="insight.type === 'popular'"
												:post-count="insight.postCount"
												v-on:onclick="showInsightDetails(engine, insightIndex)"
											></insight-popular>
											<insight-analysis
												v-if="insight.type === 'analysis'"
												:query="insight.query"
												:click-count="insight.clickCount"
												:post-count="insight.postCount"
												v-on:onclick="showInsightDetails(engine, insightIndex)"
											></insight-analysis>
										</li>
									</ul>
									<div class="searchwp-metrics__engine-suggestions-insights-actions"
										v-if="getInsightsCount(engine.name) > 5"
									>
										<button class="button" @click.prevent="showInsightDetails(engine)">{{ i18n.viewAll }} ({{ getInsightsCount(engine.name) }})</button>
									</div>
								</div>
								<div class="searchwp-metrics__note" v-else>
									<span class="dashicons dashicons-info"></span>
									<div>
										<p>{{ i18n.noInsights }}</p>
									</div>
								</div>
							</div>
							<vue-modaltor
								:visible="showingInsights && showingInsights.engine.name === engine.name"
								@hide="hideInsights"
								:default-width="'600px'">
								<div v-if="showingInsights && showingInsights.engine.name === engine.name" class="searchwp-metrics__modal searchwp-metrics__engine-suggestions-insights-details">
									<component :is="translatedInsightsEngineHeading" :props="engine"></component>
									<v-collapse-group>
										<div class="searchwp-metrics__accordion">
											<v-collapse-wrapper
												v-for="(insightDetail, insightDetailIndex) in getInsights(engine.name, -1)"
												v-bind:active="insightDetailIndex === showingInsights.insightIndex"
												:key="'insightDetail' + engine.name + insightDetailIndex">
												<div class="searchwp-metrics__accordion--header">
													<div class="searchwp-metrics__accordion--trigger" v-collapse-toggle>
														<div class="searchwp-metrics__accordion--header-icon">
															<span v-if="insightDetail.type === 'underdog'"
																class="dashicons dashicons-sos"></span>
															<span v-if="insightDetail.type === 'popular'"
																class="dashicons dashicons-awards"></span>
															<span v-if="insightDetail.type === 'analysis'"
																class="dashicons dashicons-arrow-right"></span>
														</div>
														<div class="searchwp-metrics__accordion--header-title">
															<insight-underdog
																v-if="insightDetail.type === 'underdog'"
																:post-count="insightDetail.postCount"
																:icon="''"
															></insight-underdog>
															<insight-popular
																v-if="insightDetail.type === 'popular'"
																:post-count="insightDetail.postCount"
																:icon="''"
															></insight-popular>
															<insight-analysis
																v-if="insightDetail.type === 'analysis'"
																:query="insightDetail.query"
																:click-count="insightDetail.clickCount"
																:post-count="insightDetail.postCount"
																:icon="''"
															></insight-analysis>
														</div>
													</div>
												</div>
												<div class="searchwp-metrics__accordion--content" v-collapse-content>
													<div class="searchwp-metrics__inner">
														<div v-if="insightDetail.type === 'underdog'">
															<table>
																<thead>
																	<tr>
																		<th class="searchwp-metrics--primary-col">{{ i18n.entry }}</th>
																		<th class="searchwp-metrics--secondary-col">{{ i18n.clicks }}</th>
																		<th class="searchwp-metrics--secondary-col">{{ i18n.averageRank }}</th>
																	</tr>
																</thead>
																<tbody>
																	<tr
																		v-for="(insightPost, insightPostIndex) in insightDetail.posts"
																		:key="'insightPost' + engine.name + insightPostIndex"
																	>
																		<td class="searchwp-metrics--primary-col">
																			<a :href="insightPost.permalink" target="_blank">
																				{{ insightPost.post_title }}
																			</a>
																		</td>
																		<td class="searchwp-metrics--secondary-col">{{ insightPost.click_count }}</td>
																		<td class="searchwp-metrics--secondary-col">{{ insightPost.avg_rank }}</td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div v-if="insightDetail.type === 'popular'">
															<table>
																<thead>
																	<tr>
																		<th class="searchwp-metrics--primary-col">{{ i18n.entry }}</th>
																		<th class="searchwp-metrics--secondary-col">{{ i18n.clicks }}</th>
																	</tr>
																</thead>
																<tbody>
																	<tr
																		v-for="(insightPost, insightPostIndex) in insightDetail.posts"
																		:key="'insightPost' + engine.name + insightPostIndex"
																	>
																		<td class="searchwp-metrics--primary-col">
																			<a :href="insightPost.permalink" target="_blank">
																				{{ insightPost.post_title }}
																			</a>
																		</td>
																		<td class="searchwp-metrics--secondary-col">{{ insightPost.clicks }}</td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div v-if="insightDetail.type === 'analysis'">
															<bar-chart
																:datacollection="buildChartDataset(insightDetail.posts, insightDetail.clickCount)"
																:options="popularSearchDetailsClicksOptions"
																:height="'30px'"
															></bar-chart>
															<table>
																<thead>
																	<tr>
																		<th class="searchwp-metrics--primary-col">{{ i18n.entry }}</th>
																		<th class="searchwp-metrics--secondary-col">{{ i18n.clicks }}</th>
																	</tr>
																</thead>
																<tbody>
																	<tr v-for="(insightDetailInfo, insightDetailIndex) in insightDetail.posts"
																		:key="'insightDetail' + engine.name + insightDetailIndex">
																		<td class="searchwp-metrics--primary-col">
																			<a :href="insightDetailInfo.permalink" target="_blank">
																				<legend-indicator
																					:index="insightDetailIndex"
																					:text="insightDetailInfo.post_title"
																				></legend-indicator>
																			</a>
																		</td>
																		<td class="searchwp-metrics--secondary-col">{{ insightDetailInfo.clicks }}</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</v-collapse-wrapper>
										</div>
									</v-collapse-group>
								</div>
							</vue-modaltor>
						</div>
					</div>
				</div>
			</div>

		</div>

		<transition name="fade">
			<div class="searchwp-metrics__loading"
				v-if="loading"
				v-bind:style="{ top: loaderPositionTop + 'px', left: loaderPositionLeft + 'px' }">
				<spinner
					:size="55"
					:line-size="6"
				></spinner>
			</div>
		</transition>

	</div>

</template>

<script>
import Vue from 'vue';
import Debounce from "debounce";
import Keyfinder from "keyfinder";
import Multiselect from 'vue-multiselect';
import VueDatepickerLocal from 'vue-datepicker-local';
import Tooltip from './Tooltip.vue';
import LineChart from './LineChart.vue';
import Delete from './Delete.vue';
import DoughnutChart from './DoughnutChart.vue';
import BarChart from './BarChart.vue';
import LegendIndicator from './LegendIndicator.vue';
import InsightAnalysis from './InsightAnalysis.vue';
import InsightPopular from './InsightPopular.vue';
import InsightUnderdog from './InsightUnderdog.vue';
import Spinner from 'vue-simple-spinner';
import JsonCsv from 'vue-json-excel';

export default {
	name: 'SearchwpMetrics',
	components: {
		Multiselect,
		VueDatepickerLocal,
		LineChart,
		Tooltip,
		DoughnutChart,
		BarChart,
		Delete,
		LegendIndicator,
		InsightAnalysis,
		InsightPopular,
		InsightUnderdog,
		Spinner,
		JsonCsv
	},
	methods: {
		clearMetricsData: function() {
			let self = this;
			let payload = {
				action: 'searchwp_metrics_clear_metrics_data',
			};

			// We want the loading state to be triggered right away
			this.loading = true;
			this.hideClearMetricsData();

			// Request the deletion and then update when it's done
			self.apiRequest(payload).then((response) => {
				this.update();
			});
		},
		hideClearMetricsData: function(){
			this.showingClearMetricsData = false;
		},
		clearIgnoredQueries: function() {
			let self = this;
			let payload = {
				action: 'searchwp_metrics_clear_ignored_queries',
			};

			// We want the loading state to be triggered right away
			this.loading = true;
			this.hideClearIgnoredQueries();

			// Request the deletion and then update when it's done
			self.apiRequest(payload).then((response) => {
				this.update();
			});
		},
		hideClearIgnoredQueries: function() {
			this.showingClearIgnoredQueries = false;
		},
		hideModifyLoggingRules: function() {
			this.showingModifyLoggingRules = false;

			let self = this;
			let payload = {
				action: 'searchwp_metrics_update_logging_rules',
				ips: this.ignoredIps,
				roles: this.ignoredRoles
			};

			self.apiRequest(payload).then((response) => {});
		},
		hideSettings: function() {
			this.showingSettings = false;

			let self = this;
			let payload = {
				action: 'searchwp_metrics_update_settings',
				clear_data_on_uninstall: this.clearDataOnUninstall,
				click_tracking_buoy: this.clickTrackingBuoy
			};

			self.apiRequest(payload).then((response) => {});
		},
		formattedPopularSearchesForCsv: function(engineIndex){
			if (!this.popularSearches || !this.popularSearches[ engineIndex ]) {
				return [];
			}
			let data = this.popularSearches[ engineIndex ].datasets[0].data;
			let labels = this.popularSearches[ engineIndex ].labels;

			let formatted = [];

			for (let i = 0; i < labels.length; i++) {
				// The output will be nested in a query object so as to utilize the existing Popular Search Details format
				formatted.push({
					query: {
						query: labels[i],
						searchcount: data[i],
					}
				});
			}

			return formatted;
		},
		formattedSearchesOverTimeForCsv: function(engineIndex){
			if (!this.searchesOverTime || !this.searchesOverTime.datasets[ engineIndex ]) {
				return [];
			}
			let data = this.searchesOverTime.datasets[ engineIndex ].data;
			let labels = this.searchesOverTime.labels;

			let formatted = [];

			for (let i = 0; i < labels.length; i++) {
				formatted.push({
					label: labels[i],
					searches: data[i],
				});
			}

			return formatted;
		},
		formattedEngineStatisticForCsv: function(engineIndex, engine = 'default') {
			return [
				{ statistic: 'Total Searches', value: this.getTotalSearchesCount( engineIndex ) },
				{ statistic: 'No Results Searches', value: this.getFailedSearchesCount( engineIndex ) },
				{ statistic: 'Total Results Viewed', value: this.outputMetric( this.totalClicks[ engine.name ].statistic ) },
				{ statistic: 'Searches Per User', value: this.outputMetric( this.averageSearchesPerUser[ engine.name ].statistic ) },
				{ statistic: 'Clicks Per Search', value: this.outputMetric( this.averageClicksPerSearch[ engine.name ].statistic ) },
				{ statistic: 'Average Click Rank', value: this.outputMetric( this.averageClickRank[ engine.name ].statistic ) }
			];
		},
		appendTimestamp: function(string = 'export'){
			return string + '_' + this.formatDate(this.dateRange[0]) + '_' + this.formatDate(this.dateRange[1]);
		},
		// TODO: the params here are terrible
		showPopularSearchDetails: function(engine = 'default', query = ''){
			this.showingPopularSearchDetails = { engine: engine, query: query };
			this.updatePopularSearchDetails(engine.name);
		},
		// TODO: the params here are terrible
		showInsightDetails: function(engine = 'default', insightIndex = -1){
			this.showingInsights = { engine: engine, insightIndex: insightIndex };
		},
		buildChartDataset: function(data, total = 0){
			let labels = ['Clicks'];
			let datasets = [];

			// This is kind of wacky because it's being used for Popular Searches and Insights and the data
			// structure is a bit different; Analysis Insights already know the total number of clicks
			// so in those cases that param is populated, otherwise this figures it out
			let totalClicks = total === 0 ? Keyfinder(data,'clicks').reduce((a, b) => a + b, 0) : total;

			for (let i = 0; i < data.length; i++) {
				let value = (( data[i].clicks * 100 ) / totalClicks).toFixed(2)
				datasets.push({
					type: 'horizontalBar',
					backgroundColor: Vue.SearchwpMetricsGetColor(i),
					label: data[i].post_title,
					data: [ value ]
				});
			}

			return {
				labels: labels,
				datasets: datasets
			}
		},
		updatePopularSearchDetails: function(engine) {
			let self = this;
			self.loadingPopularSearchDetails = true;

			let payload = {
				action: 'searchwp_metrics_popular_search_details',
				engine: engine,
				limit: this.popularSearchesCount,
			};

			self.apiRequest(payload).then((response) => {
				// We need to 'reset' the rules of any inline ignores
				if (this.inlineIgnores.length) {
					this.inlineIgnores.forEach(function(index){
						document.getElementById('searchwp-metrics--hook-popular-' + index).removeAttribute('style');
					});

					// Empty out the array
					this.inlineIgnores = [];
				}

				self.popularSearchesDetails = response.data;
				self.loadingPopularSearchDetails = false;
			});
		},
		unIgnoreQuery: function(hash) {
			// Remove from display (we're not refreshing until modal is closed)
			let newIgnoredQueries = this.ignoredQueries;
			for (let i = 0; i < this.ignoredQueries.length; i++) {
				if (hash === this.ignoredQueries[i].hash) {
					Vue.set(this.ignoredQueries[i], 'unignored', true);
					break;
				}
			}

			this.unIgnoreSearch(hash, false);
			this.needsRefresh = true;
		},
		unIgnoreSearch: function(hash, refresh = true) {
			let self = this;
			let payload = {
				action: 'searchwp_metrics_unignore_query',
				hash: hash
			};
			self.apiRequest(payload).then((response) => {
				if (refresh) {
					self.update();
				}
			});
		},
		hideInsights: function(){
			this.showingInsights = false;
		},
		searchSearchQueries: Debounce(function(query) {
			let self = this;

			if (query.length<3) {
				return;
			}

			self.isLoadingSearchSearches = true;

			let payload = {
				action: 'searchwp_metrics_search_queries',
				searchquery: query
			};
			self.apiRequest(payload).then((response) => {
				self.searchQueries = response.data;
				self.isLoadingSearchSearches = false;
			});
		}, 500),
		addSelectedSearchQuery: function(query){
			// In this case we've added a partial match so we're going to use the query as the ID
			// and the server will consider it for a partial match
			let tag = {
				id: query,
				query: query
			};

			this.searchQueries.push(tag);
			this.selectedSearchQueries.push(tag);
			this.update();
		},
		limit: function(data, limit = 10) {
			// As it stands this causes a Vue Warning about a potential infinite render loop, but
			// I'm not sure why yet, so we're going to hide the data with CSS while other tests are run
			let limited;
			let i = 0;

			// Is it an object?
			if (data.length === undefined) {
				limited = {};
				for (let property in data) {
					if (data.hasOwnProperty(property)) {
						i++;
						limited[property] = data[property];

						if (Object.keys(limited).length >= limit) {
							break;
						}
					}
				}
			} else {
				limited = data.splice(0,limit);
			}

			return limited;
		},
		engineHasNoTracking: function(engine) {
			return this.outputMetric( this.totalClicks[ engine ].statistic ) === '--' || this.outputMetric( this.averageClicksPerSearch[ engine ].statistic ) === '--' || this.outputMetric( this.averageClickRank[ engine ].statistic ) === '--';
		},
		outputMetric: function(n) {
			if ('0' === n.toString() || '0.000' === n.toString()) {
				return '--';
			} else {
				return n;
			}
		},
		hidePopularSearchDetails: function() {
			this.showingPopularSearchDetails = false;

			// We only need to update stats on close if new ignored searches were added
			if (this.needsRefresh) {
				this.update();
				this.needsRefresh = false;
			}
		},
		hideFailedSearches: function() {
			this.showingFailedSearches = false;

			// We need to 'reset' the rules of any inline ignores
			if (this.inlineIgnores.length) {
				let self = this;
				// Persist the ignore in the data model
				self.inlineIgnores.forEach(function(ignore){
					for (let i = 0; i < self.failedSearches.length; i++) {
						if (self.failedSearches[i].engine === ignore.engine) {
							for (let y = 0; y < self.failedSearches[i].data.length; y++) {
								if (ignore.ignoredSearch === self.failedSearches[i].data[y].query) {
									Vue.delete(self.failedSearches[i].data, y);
									break;
								}
							}
							break;
						}
					}
				});

				// Remove the inline style in case of reload
				this.inlineIgnores.forEach(function(ignore){
					document.getElementById('searchwp-metrics--hook-failed-' + ignore.index).removeAttribute('style');
				});

				// Empty out the array
				this.inlineIgnores = [];
			}

			// We only need to update stats on close if new ignored searches were added
			if (this.needsRefresh) {
				this.update();
				this.needsRefresh = false;
			}
		},
		hideIgnoredSearches: function() {
			this.showingIgnoredSearches = false;

			// We only need to update stats on close if ignored searches were removed
			if (this.needsRefresh) {
				this.update();
				this.needsRefresh = false;
			}
		},
		getTotalSearchesCount: function( engineIndex ) {
			if (!this.searchesOverTime || !this.searchesOverTime.datasets[ engineIndex ]) {
				return 0;
			}
			let data = this.searchesOverTime.datasets[ engineIndex ].data;
			return data.reduce((a, b) => a + b, 0);
		},
		getFailedSearchesCount: function( engineIndex ) {
			if (!this.failedSearches || !this.failedSearches[ engineIndex ]) {
				return 0;
			}
			let data = this.failedSearches[ engineIndex ].data;
			return data.reduce((a, b) => a + b.count, 0);
		},
		ignoreSearch: function(ignoredSearch, refresh = true) {
			let self = this;
			let payload = {
				action: 'searchwp_metrics_ignore_query',
				query: ignoredSearch
			};
			self.apiRequest(payload).then((response) => {
				if (refresh) {
					self.update();
				}
			});
		},
		ignorePopularSearch: function(ignoredSearch, index) {
			// Remove from display right away because there's a delay when there's a lot of data
			document.getElementById('searchwp-metrics--hook-popular-' + index).style.display = 'none';

			// We are also going to persist the ID so we can un-hide it later (because the whole point of
			// this is to avoid mutation and in doing so avoid the perf hit)
			this.inlineIgnores.push(index);

			// Also remove from the model in the 'background' because we can export from here without refresh
			for (let i = 0; i < this.popularSearchesDetails.length; i++) {
				if (ignoredSearch === this.popularSearchesDetails[i].query.query) {
					Vue.delete(this.popularSearchesDetails, i);
					break;
				}
			}

			// Persist the ignore
			this.ignoreSearch(ignoredSearch, false);
			this.needsRefresh = true;
		},
		ignoreFailedSearch: function(ignoredSearch, engine, index) {
			// Remove from display right away because there's a delay when there's a lot of data
			document.getElementById('searchwp-metrics--hook-failed-' + index).style.display = 'none';

			// We are also going to persist the ID so we can un-hide it later (because the whole point of
			// this is to avoid mutation and in doing so avoid the perf hit)
			this.inlineIgnores.push({
				engine: engine,
				ignoredSearch: ignoredSearch,
				index: index
			});

			// The data removal will be offloaded to the hideFailedSearches()

			// Persist the ignore
			this.ignoreSearch(ignoredSearch, false);
			this.needsRefresh = true;
		},
		getFailedSearches: function(engine) {
			for (let i = 0; i < this.failedSearches.length; i++) {
				if (this.failedSearches[i].engine === engine) {
					return this.failedSearches[i].data;
				}
			}

			return [];
		},
		getPopularSearches: function(engine) {
			for (let i = 0; i < this.popularSearches.length; i++) {
				if (this.popularSearches[i].engine === engine) {
					return this.popularSearches[i];
				}
			}

			return [];
		},
		getPopularSearchesPercentage: function(engine, engineIndex) {
			let totalSearches = this.getTotalSearchesCount( engineIndex );
			let totalPopularSearches = this.getPopularSearches(engine).datasets[0].data.reduce((a, b) => a + b, 0);

			return Math.round(( totalPopularSearches * 100 ) / totalSearches);
		},
		getPopularClicks: function(engine) {
			for (let i = 0; i < this.popularClicks.length; i++) {
				if (this.popularClicks[i].engine === engine) {
					return this.popularClicks[i];
				}
			}

			return [];
		},
		shuffle: function(a){
			for (let i = a.length - 1; i > 0; i--) {
				const j = Math.floor(Math.random() * (i + 1));
				[a[i], a[j]] = [a[j], a[i]];
			}

			return a;
		},
		getInsightsCount: function(engine) {
			if (!this.getPopularClicks(engine)){
				return 0;
			}

			if (!this.getPopularClicks(engine).insights){
				return 0;
			}

			let popular = this.getPopularClicks(engine).insights.popular;
			let underdogs = this.getPopularClicks(engine).insights.underdogs;
			let analysis = this.getPopularClicks(engine).insights.analysis;

			let total = 0;

			if (underdogs && underdogs.length) {
				total++;
			}

			if (popular && popular.length) {
				total++;
			}

			total += Object.keys(analysis).length;

			return total;
		},
		getInsights: function(engine, limit = 3){
			if (!this.getPopularClicks(engine)){
				return [];
			}

			if (!this.getPopularClicks(engine).insights){
				return [];
			}

			let insights = [];
			let popular = this.getPopularClicks(engine).insights.popular;
			let underdogs = this.getPopularClicks(engine).insights.underdogs;
			let analysis = this.getPopularClicks(engine).insights.analysis;

			if (underdogs && underdogs.length) {
				insights.push({
					type: 'underdog',
					postCount: underdogs.length,
					posts: underdogs
				});
			}

			if (popular && popular.length) {
				insights.push({
					type: 'popular',
					postCount: popular.length,
					posts: popular
				});
			}

			for (let insight in analysis) {
				if (analysis.hasOwnProperty(insight)) {
					insights.push({
						type: 'analysis',
						query: analysis[insight].query,
						clickCount: analysis[insight].clicks,
						postCount: analysis[insight].posts.length,
						posts: analysis[insight].posts
					});
				}

				// We only want a maximum of 3 entries in Insights just so the UI is balanced
				if (limit > 0 && insights.length > (limit - 1)) {
					break;
				}
			}

			return insights;
		},
		formatDate: function(date) {
			let year = date.getFullYear().toString();

			let month = date.getMonth() + 1; // getMonth() is zero based ¯\_(ツ)_/¯
			month = month < 10 ? '0' + month.toString() : month.toString();

			let day = date.getDate() < 10 ? '0' + date.getDate().toString() : date.getDate().toString();

			return year + '-' + month + '-' + day;
		},
		updateSearchesOverTime: function(data) {
			this.searchesOverTime = Vue.SearchwpMetricsFormatForChart(data, {
				type: 'line',
				borderWidth: 2,
				fill: true
			});
		},
		updatePopularQueriesOverTime: function(data) {
			let popularSearches = [];
			if (data) {
				data.forEach(function(dataset){
					popularSearches.push({
						engine: dataset.engine,
						engineLabel: dataset.engineLabel,
						labels: dataset.labels,
						datasets: [{
							type: 'doughnut',
							data: dataset.dataset,
							backgroundColor: dataset.dataset.map((el, index) =>{
								return Vue.SearchwpMetricsGetColor(index);
							})
						}]
					});
				});
			}

			this.popularSearches = popularSearches;
		},
		updatePopularClicksOverTime: function(data) {
			let popularClicks = [];

			if (data) {
				data.forEach(function(dataset){

					popularClicks.push({
						engine: dataset.engine,
						engineLabel: dataset.engineLabel,
						type: 'bar',
						insights: dataset.insights,
						datasets: dataset.dataset,
						labels: dataset.labels
					});
				});
			}

			this.popularClicks = popularClicks;
		},
		updateFailedSearchesOverTime: function(data) {
			let failedSearches = [];

			if (data) {
				data.forEach(function(dataset){
					let engineFailedSearches = [];

					for (let i = 0; i < dataset.labels.length; i++) {
						engineFailedSearches.push({
							query: dataset.labels[i],
							count: dataset.dataset[i]
						});
					}

					failedSearches.push({
						engine: dataset.engine,
						engineLabel: dataset.engineLabel,
						data: engineFailedSearches
					});
				});
			}

			this.failedSearches = failedSearches;
		},
		apiRequest: function(data = {}) {

			// Requests for metrics share a lot of properties
			if (!Object.keys(data).length) {
				data.action = 'searchwp_metrics';
				data.limit = 10;
				data.searches = Keyfinder(this.selectedSearchQueries, 'id');
			}

			data.engines = [];
			if (this.multiselect.engines.value.length) {
				this.multiselect.engines.value.forEach(function(engine){
					data.engines.push(engine.name);
				});
			}

			data.after = this.formatDate(this.dateRange[0]);
			data.before = this.formatDate(this.dateRange[1]);

			data._ajax_nonce = _SEARCHWP_METRICS_VARS.nonce;

			return new Promise(function(resolve, reject) {
				jQuery.post(ajaxurl, data, function(response) {
					if (response.success) {
						resolve(response);
					} else {
						reject(response);
					}
				});
			});
		},
		update: function() {
			this.loading = true;
			this.apiRequest().then((response) => {
				this.updateSearchesOverTime(response.data.searches_over_time);
				this.updatePopularQueriesOverTime(response.data.popular_queries_over_time);
				this.updatePopularClicksOverTime(response.data.popular_clicks_over_time);
				this.updateFailedSearchesOverTime(response.data.failed_searches_over_time);
				this.ignoredQueries = response.data.ignored_queries;
				this.averageSearchesPerUser = response.data.average_searches_per_user;
				this.averageClicksPerSearch = response.data.average_clicks_per_search;
				this.averageClickRank = response.data.average_click_rank;
				this.totalClicks = response.data.total_clicks;

				let engines = [];

				for (let i = 0; i < this.multiselect.engines.value.length; i++) {
					engines.push({
						name: this.multiselect.engines.value[i].name,
						label: this.multiselect.engines.value[i].label,
						color: Vue.SearchwpMetricsGetColor(i)
					});
				}

				this.sameDate = this.formatDate(this.dateRange[0]) === this.formatDate(this.dateRange[1]);
				this.engines = engines;
				this.loading = false;
			});
		},
		updateLoaderPosition: function() {
			let topEl = document.getElementById('wpadminbar');
			let leftEl = document.getElementById('adminmenuback');
			this.loaderPositionTop = topEl ? topEl.offsetHeight : 0;
			this.loaderPositionLeft = leftEl ? leftEl.offsetWidth : 0;
		}
	},
	mounted () {
		this.updateLoaderPosition();

		window.addEventListener('resize', this.updateLoaderPosition);

		this.update();
	},
	computed: {
		noResultsDetailsForExport() {
			return this.getFailedSearches(this.showingFailedSearches);
		},
		popularSearchesDetailsForExport() {
			// To effectively populate a CSV-compatible display of popular searches, we need to
			// enforce the data structure to accommodate the display of click data.
			const source = this.popularSearchesDetails;
			let formatted = [];

			if (!source.length) {
				return formatted;
			}

			source.forEach(function(popularSearch) {
				// Add a row in the spreadsheet for the search, acting as a heading.
				formatted.push({
					query: popularSearch.query.query,
					searches: popularSearch.query.searchcount,
					clickId: '',
					clickTitle: '',
					clickCount: '',
				});

				// For each click, we need to make space in the spreadsheet, so we
				// blank out the search query info and instead fill in click details.
				if (popularSearch.clicks.length) {
					popularSearch.clicks.forEach(function(click) {
						formatted.push({
							query: '',
							searches: '',
							clickId: click.post_id,
							clickTitle: click.post_title,
							clickCount: click.clicks,
						});
					});
				}
			});

			return formatted;
		},
		translatedEngineDetailsHeading() {
			return {
				template: '<span>' + this.i18n.engineDetailsForTimeline + '</span>',
				props: ['props'],
				data () {
					return {
						engine: this.props
					}
				}
			}
		},
		translatedNoResultsSearchesEngineHeading() {
			return {
				template: '<h4>' + this.i18n.noResultsSearchesEngine + '</h4>',
				props: ['props'],
				data () {
					return {
						engine: this.props
					}
				}
			}
		},
		translatedNoResultsSearchesEngineNote() {
			return {
				template: '<p>' + this.i18n.noResultsSearchesEngineNote + '</p>'
			}
		},
		translatedClickTrackingNote() {
			return {
				template: '<p>' + this.i18n.clickTrackingNote + '</p>'
			}
		},
		translatedPopularSearchDetailsHeading() {
			return {
				template: '<h4>' + this.i18n.popularSearchDetailsEngine + '</h4>',
				props: ['props'],
				data () {
					return {
						engine: this.props
					}
				}
			}
		},
		translatedInsightsEngineHeading() {
			return {
				template: '<h4>' + this.i18n.insightsEngine + '</h4>',
				props: ['props'],
				data () {
					return {
						engine: this.props
					}
				}
			}
		},
		translatedClearMetricsDataNote() {
			return {
				template: '<p>' + this.i18n.clearMetricsDataNote + '</p>'
			}
		},
		translatedClearIgnoredQueriesNote() {
			return {
				template: '<p>' + this.i18n.clearIgnoredQueriesNote + '</p>'
			}
		},
		translatedLoggingRulesNoteDetails() {
			return {
				template: '<span>' + this.i18n.loggingRulesNoteDetails + '</span>'
			}
		}
	},
	data () {
		const that = this;

		return {
			canEditSettings: _SEARCHWP_METRICS_VARS.can_edit_settings,
			clearDataOnUninstall: _SEARCHWP_METRICS_VARS.settings.clear_data_on_uninstall,
			clickTrackingBuoy: _SEARCHWP_METRICS_VARS.settings.click_tracking_buoy,
			clickTrackingBuoyApplicable: _SEARCHWP_METRICS_VARS.settings.click_tracking_buoy_applicable,
			inlineIgnores: [],
			ignoredIps: _SEARCHWP_METRICS_VARS.settings.blocklists.ips,
			ignoredRoles: _SEARCHWP_METRICS_VARS.settings.blocklists.roles,
			sameDate: false,
			showingSettings: false,
			showingClearMetricsData: false,
			showingClearIgnoredQueries: false,
			showingModifyLoggingRules: false,
			noResultsDetailsJsonFields: {
				'Query': 'query',
				'No Results Search Count': 'count'
			},
			searchesOverTimeJsonFields: {
				'Date': 'label',
				'Searches': 'searches'
			},
			engineStatisticsJsonFields: {
				'Statistic': 'statistic',
				'Value': 'value'
			},
			popularSearchesJsonFields: {
				'Search Query': 'query.query',
				'Searches': 'query.searchcount'
			},
			popularSearchesDetailsJsonFields: {
				'Search Query': 'query',
				'Searches': 'searches',
				'Clicked Title': {
					callback: function(value) {
						return !value.clickId ? '' : value.clickTitle + ' (' + value.clickId + ')';
					}
				},
				'Clicks': 'clickCount'
			},
			popularSearchesCount: 10,
			popularSearchesDetails: [],
			loading: true,
			loaderPositionTop: 0,
			loaderPositionLeft: 0,
			showingInsights: false,
			showingIgnoredSearches: false,
			showingFailedSearches: false,
			showingPopularSearchDetails: false,
			loadingPopularSearchDetails: true,
			needsRefresh: false,
			engines: [],
			ignoredQueries: [],
			isLoadingSearchSearches: false,
			searchQueries: [],
			selectedSearchQueries: [],
			multiselect: {
				engines: {
					value: _SEARCHWP_METRICS_VARS.engine_default,
					options: _SEARCHWP_METRICS_VARS.engines,
				}
			},
			averageSearchesPerUser: [],
			averageClicksPerSearch: [],
			averageClickRank: [],
			totalClicks: [],
			// Metrics are grouped by chart type because some will combine engines while others will not
			searchesOverTime: null,
			searchesOverTimeOptions: {
				maintainAspectRatio: false,
				legend: {},
				tooltips: {
					cornerRadius: 2,
					titleMarginBottom: 10,
					xPadding: 16,
					yPadding: 9,
					displayColors: false // We want the fill color to be semi-transparent but that doesn't translate well here
				}
			},
			popularSearchDetailsClicksOptions: {
				maintainAspectRatio: false,
				height: '40px',
				legend: {
					display: false
				},
				tooltips: {
					enabled: false
				},
				hover: {
					mode: null
				},
				scales: {
					xAxes: [{
						stacked: true,
						display: false,
						ticks: {
							max: 100
						}
					}],
					yAxes: [{
						stacked: true,
						display: false
					}]
				}
			},
			popularSearches: [],
			popularSearchesOptions: {
				maintainAspectRatio: true,
				legend: {
					display: false
				},
				tooltips: {
					cornerRadius: 2,
					titleMarginBottom: 10,
					xPadding: 11,
					yPadding: 9,
				}// ,
				// onClick: function(e,i){
				// 	that.showPopularSearchDetails('default', i[0]['_index']); // In progress, needs to pass engine
				// }
			},
			popularClicks: [],
			popularClicksOptions: {
				maintainAspectRatio: false,
				legend: {
					display: false,
					position: 'bottom'
				},
				tooltips: {
					cornerRadius: 2,
					titleMarginBottom: 10,
					xPadding: 11,
					yPadding: 9,
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true
					}]
				}
			},
			failedSearches: [],
			dateRange: [
				new Date(_SEARCHWP_METRICS_VARS.options.default_start),
				new Date(_SEARCHWP_METRICS_VARS.options.default_end)
			],
			i18n: {
				addAsPartialMatch: _SEARCHWP_METRICS_VARS.i18n.add_as_partial_match,
				areYouSure: _SEARCHWP_METRICS_VARS.i18n.are_you_sure,
				averageClickRank: _SEARCHWP_METRICS_VARS.i18n.average_click_rank,
				averageRank: _SEARCHWP_METRICS_VARS.i18n.average_rank,
				cancel: _SEARCHWP_METRICS_VARS.i18n.cancel,
				chooseEngine: _SEARCHWP_METRICS_VARS.i18n.choose_engine,
				clearData: _SEARCHWP_METRICS_VARS.i18n.clear_data,
				clearIgnoredQueriesNote: _SEARCHWP_METRICS_VARS.i18n.clear_ignored_queries_note,
				clearMetricsData: _SEARCHWP_METRICS_VARS.i18n.clear_metrics_data,
				clearMetricsDataNote: _SEARCHWP_METRICS_VARS.i18n.clear_metrics_data_note,
				clickedResult: _SEARCHWP_METRICS_VARS.i18n.clicked_result,
				clicks: _SEARCHWP_METRICS_VARS.i18n.clicks,
				clicksPerSearch: _SEARCHWP_METRICS_VARS.i18n.clicks_per_search,
				clickTrackingNote: _SEARCHWP_METRICS_VARS.i18n.click_tracking_note,
				clickTrackingBuoy: _SEARCHWP_METRICS_VARS.i18n.click_tracking_buoy,
				clickTrackingBuoyLabelNote: _SEARCHWP_METRICS_VARS.i18n.click_tracking_buoy_label_note,
				clickTrackingBuoyUnavailable: _SEARCHWP_METRICS_VARS.i18n.click_tracking_buoy_unavailable,
				conversionRate: _SEARCHWP_METRICS_VARS.i18n.conversion_rate,
				datePicker: {
					dow: _SEARCHWP_METRICS_VARS.options.first_day_of_week,
					hourTip: _SEARCHWP_METRICS_VARS.i18n.select_hour,
					minuteTip: _SEARCHWP_METRICS_VARS.i18n.select_minute,
					secondTip: _SEARCHWP_METRICS_VARS.i18n.select_second,
					yearSuffix: _SEARCHWP_METRICS_VARS.options.year_suffix,
					monthsHead: _SEARCHWP_METRICS_VARS.i18n.months.split('_'),
					months: _SEARCHWP_METRICS_VARS.i18n.months_abbr.split('_'),
					weeks: _SEARCHWP_METRICS_VARS.i18n.days_abbr.split('_'),
					cancelTip: _SEARCHWP_METRICS_VARS.i18n.close,
					submitTip: _SEARCHWP_METRICS_VARS.i18n.update
				},
				dateRange: _SEARCHWP_METRICS_VARS.i18n.date_range,
				engineDetailsForTimeline: _SEARCHWP_METRICS_VARS.i18n.engine_details_for_timeline,
				engineStatistics: _SEARCHWP_METRICS_VARS.i18n.engine_statistics,
				enginesToDisplay: _SEARCHWP_METRICS_VARS.i18n.engines_to_display,
				entry: _SEARCHWP_METRICS_VARS.i18n.entry,
				exportEngineStatistics: _SEARCHWP_METRICS_VARS.i18n.export_engine_statistics,
				exportPopularSearches: _SEARCHWP_METRICS_VARS.i18n.export_popular_searches,
				exportSearchesOverTime: _SEARCHWP_METRICS_VARS.i18n.export_searches_over_time,
				ignored: _SEARCHWP_METRICS_VARS.i18n.ignored,
				ignoredSearches: _SEARCHWP_METRICS_VARS.i18n.ignored_searches,
				ignoredSearchQuery: _SEARCHWP_METRICS_VARS.i18n.ignored_search_query,
				ignoredMessage: _SEARCHWP_METRICS_VARS.i18n.ignored_message,
				insights: _SEARCHWP_METRICS_VARS.i18n.insights,
				insightsEngine: _SEARCHWP_METRICS_VARS.i18n.insights_engine,
				ipBlocklist: _SEARCHWP_METRICS_VARS.i18n.ip_blocklist,
				ipBlocklistNote: _SEARCHWP_METRICS_VARS.i18n.ip_blocklist_note,
				limitMetricsToQueries: _SEARCHWP_METRICS_VARS.i18n.limit_metrics_to_queries,
				loggingRules: _SEARCHWP_METRICS_VARS.i18n.logging_rules,
				loggingRulesNote: _SEARCHWP_METRICS_VARS.i18n.logging_rules_note,
				loggingRulesNoteDetails: _SEARCHWP_METRICS_VARS.i18n.logging_rules_note_details,
				modifyLoggingRules: _SEARCHWP_METRICS_VARS.i18n.modify_logging_rules,
				noClicks: _SEARCHWP_METRICS_VARS.i18n.no_clicks,
				noFailedSearches: _SEARCHWP_METRICS_VARS.i18n.no_failed_searches,
				noIgnoredQueries: _SEARCHWP_METRICS_VARS.i18n.no_ignored_queries,
				noInsights: _SEARCHWP_METRICS_VARS.i18n.no_insights,
				noResultsSearches: _SEARCHWP_METRICS_VARS.i18n.no_results_searches,
				noResultsSearchesEngine: _SEARCHWP_METRICS_VARS.i18n.no_results_searches_engine,
				noResultsSearchesEngineNote: _SEARCHWP_METRICS_VARS.i18n.no_results_searches_engine_note,
				notEnoughData: _SEARCHWP_METRICS_VARS.i18n.not_enough_data,
				ofAllSearches: _SEARCHWP_METRICS_VARS.i18n.of_all_searches,
				popularSearchDetailsEngine: _SEARCHWP_METRICS_VARS.i18n.popular_search_details_engine,
				popularSearchDetailsNote: _SEARCHWP_METRICS_VARS.i18n.popular_search_details_note,
				popularSearches: _SEARCHWP_METRICS_VARS.i18n.popular_searches,
				removeAllIgnoredQueries: _SEARCHWP_METRICS_VARS.i18n.remove_all_ignored_queries,
				removeOnUninstallation: _SEARCHWP_METRICS_VARS.i18n.remove_on_uninstallation,
				removeOnUninstallationLabelNote: _SEARCHWP_METRICS_VARS.i18n.remove_on_uninstallation_label_note,
				saveClose: _SEARCHWP_METRICS_VARS.i18n.save_close,
				searches: _SEARCHWP_METRICS_VARS.i18n.searches,
				searchesPerUser: _SEARCHWP_METRICS_VARS.i18n.searches_per_user,
				searchesPerUserNote: _SEARCHWP_METRICS_VARS.i18n.searches_per_user_note,
				// searchMetrics: _SEARCHWP_METRICS_VARS.i18n.search_metrics,
				searchQuery: _SEARCHWP_METRICS_VARS.i18n.search_query,
				searchQueryControls: _SEARCHWP_METRICS_VARS.i18n.search_query_controls,
				settings: _SEARCHWP_METRICS_VARS.i18n.settings,
				stopIgnoringQuery: _SEARCHWP_METRICS_VARS.i18n.stop_ignoring_query,
				to: _SEARCHWP_METRICS_VARS.i18n.to,
				totalResultsViewed: _SEARCHWP_METRICS_VARS.i18n.total_results_viewed,
				totalSearches: _SEARCHWP_METRICS_VARS.i18n.total_searches,
				userIdRoleBlocklist: _SEARCHWP_METRICS_VARS.i18n.user_id_role_blocklist,
				userIdRoleBlocklistNote: _SEARCHWP_METRICS_VARS.i18n.user_id_role_blocklist_note,
				viewAll: _SEARCHWP_METRICS_VARS.i18n.view_all,
				viewMore: _SEARCHWP_METRICS_VARS.i18n.view_more,
				viewNoResultsSearches: _SEARCHWP_METRICS_VARS.i18n.view_no_results_searches
			}
		}
	}
}
</script>

<style lang="css">
	@import '../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css';
</style>

<style lang="scss">
	.fade-enter-active, .fade-leave-active {
		transition: opacity 135ms;
	}

	.fade-enter, .fade-leave-to {
		opacity: 0;
	}

	.searchwp-metrics {
		margin-right: 20px; // Match the gap from the Admin Menu

		* {
			box-sizing: border-box;
		}

		// Date picker hard codes a width of 403px but in range mode
		// with buttons it prevents them from sitting next to each other
		.datepicker-range {
			width: 100%;
			min-width: 1px;

			// These styles aim to match this input with the multiselect inputs
			> input {
				background: #fff;
				border-radius: 5px;
				border: 1px solid #e8e8e8;
				font-size: 16px;
				line-height: 20px;
				padding: 8px 10px;
				margin-top: -1px;
				height: 40px;
			}

			.datepicker-popup{
				width: 100vw;
				max-width: 415px;
			}
		}

		.modal-vue-wrapper {
			z-index: 999999 !important;

			.modal-vue-overlay {
				background: rgba( 30, 30, 30, 0.5 ) !important;
			}

			.modal-vue-panel {
				max-width: 600px;
				overflow-y: scroll;

				.modal-vue-content {
					padding-right: 1em;
				}

				// also need to reset the webkit customizations
				// &::-webkit-scrollbar,
				// &::-webkit-scrollbar-track,
				// &::-webkit-scrollbar-thumb {
				// 	all: initial !important;
				// }
			}
		}

		.searchwp-metrics__engine-popular-searches {

			.searchwp-metrics__modal,
			.modal-vue-panel {
				max-width: 800px;
			}
		}
	}

	.searchwp-metrics__loading {
		position: fixed;
		top: 0;
		right: 0;
		left: 0;
		bottom: 0;
		z-index: 99999;
		display: flex;
		align-items: center;
		justify-content: center;
		background: rgba(255, 255, 255, 0.85);
	}

	.searchwp-is-clickable {
		cursor: pointer;
	}

	.searchwp-metrics__controls {
		width: 100%;
		display: flex;
		justify-content: space-between;
		flex-direction: row;
		margin: 1% 0 2% 0;

		.searchwp-metrics__control {
			width: 32%;
		}
	}

	.searchwp-metrics__control {
		padding: 1.5em;
		background: #fff;

		h4 {
			font-size: 1.1em;
			margin: 0 0 0.7em 1px;
		}
	}

	.searchwp-metrics__searches-over-time {

		.searchwp-metrics-chart,
		.searchwp-metrics-chart canvas {
			height: 300px !important; // Manually setting this isn't working
		}
	}

	.searchwp-metrics__searches-over-time,
	.searchwp-metrics__engine-details {
		width: 100%;
		padding: 1.5em;
		background: #fff;
		margin: 2% 0;
	}

	.searchwp-metrics__engine-details {

		> div {
			display: flex;

			&.searchwp-metrics__engine-details-hook > div {
				padding-right: 2%;
				padding-left: 2%;

				&:first-child {
					padding-left: 0;
				}

				&:last-child {
					padding-right: 0;
				}
			}
		}
	}

	.searchwp-metrics__engine-details-heading-group {
		display: flex;
		justify-content: space-between;
		margin-bottom: 1.6em;
		align-items: center;

		button.button {
			padding: 0 0.3em;
			line-height: 1;
			display: inline-block;

			span {
				opacity: 0.5;
			}
		}
	}

	.searchwp-metrics__engine-details-heading {
		margin-top: 0;
		margin-bottom: 0;
		font-size: 1.7em;
		color: #444;
		display: flex;
		align-items: center;
		width: 100%;
	}

	.searchwp-metrics__engine-details-legend {
		display: inline-block;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		margin-right: 0.25em;
	}

	.searchwp-metrics__engine-popular-searches {
		width: 35%;
	}

	.searchwp-metrics__engine-top-clicks {
		flex: 1;

		.searchwp-metrics-table {
			margin-top: 2em;
		}
	}

	.searchwp-metrics__chart-donut-wrapper {
		display: flex;

		> * {
			display: flex;
			flex-direction: column;

			&:first-child {
				flex: 1;
				padding-right: 3em;
			}

			&:last-child {
				align-items: center;
				padding-top: 1em;
			}
		}
	}

	.searchwp-metrics__engine-popular-searches-coverage {
		padding-top: 1.5em;
		text-align: center;

		span {
			font-size: 2.5em;
			font-weight: bold;
			letter-spacing: -1px;
			display: block;
			line-height: 1;
		}
	}

	.searchwp-delete {
		opacity: 0.5;
		color: #aa0000;
		transform: scale(0.8);
	}

	.searchwp-metrics-table {
		width: 100%;
		border-collapse: collapse;

		td {
			padding: 0.2em 0;
		}

		td:first-of-type {
			width: 100%;
			position: relative;
		}

		.searchwp-delete {
			opacity: 0;
		}

		tr:hover .searchwp-delete {
			opacity: 0.5;
		}
	}

	.searchwp-metrics__control-queries,
	.searchwp-metrics__heading-button {
		display: flex;
		justify-content: space-between;
		align-items: center;

		> .button {
			margin-left: 1em;
			cursor: pointer;
		}
	}

	.searchwp-popular-clicks-suggestions {
		overflow: auto;
		padding: 1.5em;

		ul > li + li {
			border-top: 2px solid #efefef;
			margin-top: 2em;
			padding-top: 0.5em;
		}

		h6 {
			font-size: 14px;
			margin-top: 1em;
			margin-bottom: 0.25em;
		}
	}

	.searchwp-metrics__engine-details-alpha {
		width: 30%;
		position: relative;
		padding-left: 2px !important; // visual illusion of alignment with the legend indicator
	}

	.searchwp-metrics__engine-details-beta {
		width: 36%;
		position: relative;
	}

	.searchwp-metrics__engine-details-beta {
		padding-left: 3%;
		padding-right: 3%;

		&:before,
		&:after {
			top: 0;
			bottom: 0;
			background: #dcdcdc;
			content: '';
			display: block;
			position: absolute;
			width: 1px;
		}

		&:before {
			left: -1.5%;
		}

		&:after {
			right: 0;
		}
	}

	.searchwp-metrics__engine-details-omega {
		width: auto;
		flex: 1;
	}

	.searchwp-metrics__stats-grid {
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;

		> * {
			width: 48%;
		}

		dl, dt, dd {
			margin: 0;
			padding: 0;
			line-height: 1.5;
		}

		dd {
			font-weight: bold;
			font-size: 2.8em;
			letter-spacing: -1px;
			line-height: 1;
			padding-top: 0.15em;
			margin-bottom: 0.6em;
		}
	}

	.searchwp-trigger__external {
		border: 0;
		padding: 0;
		margin: 0;
		background: transparent;
		color: #00a0d2;
		cursor: pointer;
		display: flex;

		* {
			display: flex;
			justify-content: center;
			align-items: center;
		}
	}

	.searchwp-metrics__flex {
		display: flex;
		align-items: center;

		> * {
			padding-left: 0.25em;

			&:first-child {
				padding-left: 0;
			}
		}
	}

	.searchwp-metrics__modal {
		max-width: 600px;
		text-align: left;

		table {
			width: 100%;
			border-collapse: collapse;
			font-size: 0.9em;
			margin-top: 1em;

			th {
				padding-bottom: 0.7em;
			}

			td {
				border-top: 1px solid #eaeaea;
				padding: 0.5em 0 0.4em;
			}

			.searchwp-delete {
				position: relative;
				top: 1px;
			}
		}
	}

	.searchwp-metrics__modal-confirmation {
		text-align: center;

		h4 {
			margin-top: 0;
		}

		.dashicons {
			color: #e15759;
			transform: scale(1.2);
			line-height: 1.2;
		}
	}

	.searchwp-metrics__modal-confirmation--actions {
		list-style: none;
		margin: 0;
		padding: 1em 0 0;

		.button {
			margin-left: 0 !important; // TODO: specificity with .searchwp-metrics__title
			margin-bottom: 1em;
		}

		.searchwp-metrics-nonbutton {
			font-size: 0.83em;
		}
	}

	.searchwp-metrics__engine-popular-searches {

		.searchwp-metrics-legend-label {

			span:hover {
				cursor: pointer;
				color: #0073aa;
				text-decoration: underline;
			}
		}
	}

	.searchwp-metrics__engine-details--heading {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-top: 0.4em;
		margin-bottom: 1.6em;

		> h4 {
			margin: 0.3em 0;
			font-size: 1.2em;
		}

		> div {

			> input {
				display: inline-block;
				width: 3em;
			}
		}
	}

	.searchwp-metrics__no-data {

		p {
			font-style: italic;
			width: 100%;
			text-align: center;
			font-size: 1.5em;
			padding: 2em 0;
		}
	}

	.searchwp-metrics__note {
		display: flex;
		padding: 0.8em;
		border: 1px solid #dcdcdc;
		border-radius: 2px;
		background: #f3f3f3;

		> span {
			width: 1em;
			height: 1em;
		}

		> div {
			flex: 1;
			padding-left: 0.5em;

			> *:first-child {
				margin-top: 0;
				padding-top: 0;
			}

			> *:last-child {
				margin-bottom: 0;
				padding-bottom: 0;
			}
		}
	}

	.searchwp-metrics-nonbutton {
		border: 0;
		margin: 0;
		display: inline-block;
		text-decoration: underline;
		color: #0073aa;
		padding: 0;
		background: transparent;
		cursor: pointer;

		&:hover {
			color: #00a0d2;
		}
	}

	.searchwp-metrics__engine-suggestions-insights {

		ul {
			margin: -0.5em 0 0;
			padding: 0;
			list-style: none;
		}
	}

	.searchwp-metrics__engine-suggestions-insight {
		margin: 1em 0 1.5em;
	}

	// Currently not in use...
	.searchwp-metrics__engine-suggestions-insights-actions {
		display: flex;
		padding-left: 1.5em;
	}

	.tooltip.popover .searchwp-metrics-popover {

		.popover-arrow {
			border-color: #414141;
		}

		.popover-inner {
			background: #414141;
			color: #fff;
			border-radius: 3px;
			padding: 0;
			box-shadow: none;

			ul {
				text-align: left;
				margin: 0;
				padding: 0.6em 0;
				list-style: none;
				min-width: 100px;
			}

			li {
				padding: 0.3em 0.7em;
				line-height: 1.5;
				margin: 0;
				cursor: pointer;

				&:hover {
					background: #159FD2;
				}
			}
		}
	}

	.searchwp-metrics__insight {
		display: flex;
		justify-content: space-between;

		> span {
			display: inline-block;
			width: 1em;
			height: 1em;
			position: relative;
			left: -0.25em;
		}
	}

	.searchwp-metrics__engine-suggestions-insight-content {
		flex: 1;

		p {
			margin: 0;
		}
	}

	.v-collapse-content{
		max-height: 0;
		transition: max-height 0.3s ease-out;
		overflow: hidden;
		padding: 0;
	}

	.v-collapse-content-end {
		transition: max-height 0.3s ease-in;
		max-height: 1500px;
	}

	.searchwp-metrics__accordion {

		.vc-collapse {
			padding: 0.4em 1em 0.1em 0.2em;
			border: 1px solid #dcdcdc;
			border-radius: 2px;
			background: #fafafa;
			margin-bottom: 0.7em;
		}
	}

	.searchwp-metrics__accordion--header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		text-align: left;
	}

	.searchwp-metrics__accordion--trigger {
		cursor: pointer;
		flex: 1;
		display: flex;
		justify-content: space-between;
	}

	.searchwp-metrics__accordion--actions {
		width: 1.5em;
		text-align: right;
	}

	.searchwp-metrics__accordion--header-icon {
		width: 1em;

		// span:before {
		// 	color: #BCBCBC; // Taken from SearchWP core
		// }

		.dashicons-sos,
		.dashicons-awards {
			transform: scale(0.8);
		}
	}

	.searchwp-metrics__accordion--header-title {
		flex: 1;
		padding: 0 1em 0 0.4em;
		margin: 0;
		font-size: 0.83em;

		.searchwp-metrics__insight {
			padding-bottom: 0.4em;
		}
	}

	.searchwp-metrics__accordion--header-figure {
		width: 4em;
		font-size: 0.83em; // match the title above
		text-align: right;
	}

	.searchwp-metrics__accordion--content {

		a {
			display: block;
			position: relative;
		}

		.searchwp-metrics-chart,
		.searchwp-metrics-chart canvas {
			height: 30px !important;
		}

		.searchwp-metrics__no-data {

			p {
				font-size: 1.05em;
				margin-top: 0;
				padding: 0;
			}
		}
	}

	.searchwp-metrics--primary-col {
		width: 50%;
	}

	.searchwp-metrics--secondary-col {
		width: 25%;
		text-align: center;
	}

	.searchwp-metrics__inner {
		padding: 0.5em 0 0.4em 1.4em;
	}

	.searchwp-metrics__popular-search-details {
		position: relative;

		.vc-collapse,
		.searchwp-metrics__accordion--content {
			padding-right: 0.5em;
		}
	}

	.searchwp-metrics__engine-details--heading {
		margin-bottom: 0;

		> div {
			padding-left: 1em;
			display: flex;
			align-items: center;
		}

		input {
			border-radius: 2px;
		}

		.button {
			padding: 0 0.3em;
			line-height: 1;
			display: inline-block;
			margin-left: 0.6em;
			// height: 27px; // Firefox issue with shadow render having an offset
			line-height: 1;

			span {
				opacity: 0.5;
			}
		}

		div.button {
			display: flex;
			align-items: center;
			align-content: center;
		}
	}

	.searchwp-metrics__loading-details {
		position: relative;
	}

	.searchwp-metrics__loading-details-container {

		> * {
			opacity: 1;
		}
	}
	.searchwp-metrics__loading-details-loader {
		opacity: 0;
		position: absolute;
		width: 0;
		height: 0;
		overflow: hidden;
	}

	.searchwp-metrics__is-loading-details {

		.searchwp-metrics__loading-details-container {
			opacity: 0;
		}

		.searchwp-metrics__loading-details-loader {
			opacity: 1;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			padding-top: 15vh;
		}
	}

	.searchwp-metrics__split {
		display: flex;
		justify-content: space-between;

		> * {
			width: 50%;

			&:last-child {
				text-align: right;
			}
		}
	}

	.searchwp-metrics__guide {
		font-weight: bold;
		margin: 0;
		font-size: 1em;
	}

	.searchwp-metrics__popular-search-details {
		min-height: 40vh;

		h4 {
			margin-bottom: 0;
		}

		.searchwp-metrics__guide {
			margin: 1em 0 0.6em;
		}

		table {

			tr {

				> *:last-child {
					text-align: right;
				}
			}
		}
	}

	.searchwp-metrics__engine-suggestions-insights-details {

		.searchwp-metrics__inner {
			padding-top: 0;
		}

		&.searchwp-metrics__modal table {
			margin-top: 0.5em;

			tr {

				> *:last-child {
					text-align: right;
				}
			}

			tbody {
				font-size: 0.95em;
			}
		}

		.searchwp-metrics-chart--bar {
			margin-top: 0.5em;
		}
	}

	.searchwp-metrics__title {
		display: flex;
		justify-content: space-between;
		align-items: center;

		h1 {
			flex: 1;
		}

		.v-popover .button {
			padding: 0 0.3em;
			line-height: 1;
			display: inline-block;
			margin-left: 0.6em;

			span {
				opacity: 0.5;
			}
		}

		.modal-vue-wrapper .modal-vue-panel {
			overflow-y: inherit;
		}

		.modal-vue-wrapper .modal-vue-panel .modal-vue-actions {
			display: none;
		}
	}

	.tooltip-inner .searchwp-metrics-nonbutton {
		text-decoration: none;
		color: #fff;
	}

	.searchwp-metrics--hidden {
		display: none !important;
	}

	.searchwp-metrics__modal-logging {

		h4 {
			margin-top: 0;
		}

		.button {
			margin: 0;
		}

		.searchwp-metrics__modal-confirmation--actions {
			padding: 0;
		}
	}

	.searchwp-metrics__textarea {
		margin: 1em 0;

		textarea {
			resize: none;
			display: block;
			width: 100%;
			font: 1em monospace;
			height: 5em;
		}

		p {
			margin: 0;
			font-size: 0.8em;
		}
	}

	.searchwp-metrics__modal-logging,
	.searchwp-metrics__modal-settings {

		.searchwp-metrics__modal-confirmation--actions {
			margin-top: 1em;
			text-align: center;
		}
	}

	.searchwp-metrics__modal-settings {

		.searchwp-metrics__note {
			margin-bottom: 1em;
		}
	}

	.searchwp-metrics__checkbox {
		display: flex;
		margin: 0.5em 0;

		input {
			display: inline-block;
			margin: 5px 6px 0 0;
		}
	}

	.searchwp-metrics__checkbox-label {

		label {
			font-size: 0.83em;
			cursor: pointer;
		}
	}

	@media screen and (max-width:1300px) {
		.searchwp-metrics__engine-details {

			.searchwp-metrics__engine-details-hook {
				flex-wrap: wrap;
			}

			.searchwp-metrics__engine-details-alpha,
			.searchwp-metrics__engine-details-beta {
				width: 50%;
			}

			.searchwp-metrics__engine-details-beta {
				padding-right: 0;

				&:after {
					display: none;
				}
			}

			.searchwp-metrics__engine-details-omega {
				width: 100%;
				padding-top: 1em;
				margin-top: 2em;
				padding-left: 0;
				border-top: 1px solid #dcdcdc;
			}
		}
	}

	@media screen and (max-width:1000px) {
		.searchwp-metrics__controls {
			flex-direction: column;

			.searchwp-metrics__control {
				width: 100%;

				+ .searchwp-metrics__control {
					padding-top: 0;
				}
			}
		}
	}

	@media screen and (max-width:750px) {

		.searchwp-metrics__engine-details {

			.searchwp-metrics__engine-details-alpha,
			.searchwp-metrics__engine-details-beta {
				width: 100%;
			}

			.searchwp-metrics__engine-details-beta {
				padding-top: 1em;
				margin-top: 2em;
				padding-left: 0;
				border-top: 1px solid #dcdcdc;

				&:before {
					display: none;
				}
			}
		}
	}

	@media screen and (max-width:440px) {
		.searchwp-metrics__engine-details-heading-group {
			display: block !important;

			.searchwp-metrics__engine-details-heading {
				line-height: 1.2;
				margin-bottom: 0.5em;
			}
		}

		.searchwp-metrics__stats-grid,
		.searchwp-metrics__chart-donut-wrapper {
			display: block;
		}
	}

</style>
