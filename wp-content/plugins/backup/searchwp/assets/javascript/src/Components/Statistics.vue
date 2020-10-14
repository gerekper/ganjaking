<template>
	<div :class="['searchwp-settings', 'searchwp-settings-statistics' ]">
		<div class="searchwp-settings-view-header">
			<h1>{{ 'SearchWP Statistics' | i18n }}</h1>
			<ul class="searchwp-actions searchwp-settings-statistics-actions">
				<li>
					<button class="button" @click.stop="reset">
						{{ 'Reset' | i18n }}
					</button>
				</li>
				<li>
					<button class="button" @click.stop="$modal.show('manageIgnored')">
						{{ 'Manage Ignored' | i18n }}
					</button>
				</li>
			</ul>
		</div>

		<Modal :name="'manageIgnored'"
			:label="'Manage Ignored Queries' | i18n"
			:actionIsPrimary="false"
			:actionLabel="'Close' | i18n">
			<p>{{ '_manage_ignored_note' | i18n }}</p>
			<p class="description" v-if="!ignored.length">{{ '_manage_ignored_note_none' | i18n }}</p>
			<table v-else class="searchwp-data-table">
				<thead>
					<tr>
						<th>{{ 'Query' | i18n }}</th>
						<th>{{ 'Unignore' | i18n }}</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="query in ignored" :key="query">
						<td>{{ query }}</td>
						<td>
							<button
								:disabled="updating"
								class="button"
								@click="apiRequest('unignore_query', query)">
								<span>{{ 'Unignore' | i18n }} </span>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</Modal>

		<div :class="['searchwp-settings-statistics-charts', updating ? 'searchwp-settings-statistics-charts-updating' : '']">
			<vue-tabs
				class="postbox"
				direction="vertical"
				:activeTextColor="activeTabColor"
				:disabledTextColor="inactiveTabColor">
				<v-tab v-for="chart in stats" :key="chart.engine + ignored.length + chart.data.counts.reduce((a, b) => a + b)" :title="chart.label">
					<h3>{{ 'Searches over the past 30 days' | i18n }}</h3>
					<div class="searchwp-settings-statistics-chart">
						<LineChart
							class="searchwp-settings-statistics-chart-line"
							:labels="chart.data.labels"
							:datasets="[{
								label: ' Searches' | i18n,
								data: chart.data.counts,
								borderWidth: 2,
								fill: true,
								pointRadius: 3,
								pointHoverRadius: 5,
								pointBorderWidth: 2,
								borderColor: borderColor,
								backgroundColor: backgroundColor,
								pointBackgroundColor: pointBackgroundColor,
								pointBorderColor: pointBorderColor
							}]"></LineChart>
					</div>
					<div class="searchwp-settings-statistics-chart-details">
						<div class="searchwp-settings-statistics-chart-detail"
							v-for="detail in chart.details" :key="detail.label">
							<h3>{{ detail.label }}</h3>
							<table>
								<colgroup>
									<col class="searchwp-search-query">
									<col class="searchwp-search-count">
								</colgroup>
								<thead>
									<th>{{ 'Query' | i18n }}</th>
									<th>{{ 'Searches' | i18n }}</th>
								</thead>
								<tbody>
									<tr v-for="(search, index) in detail.data" :key="index + ignored.length">
										<td>
											<span>
												<button
													@click="apiRequest('ignore_query', search.query)"
													title="Ignore this">
													<span class="dashicons dashicons-no-alt"></span>
													<span class="screen-reader-text">Ignore</span>
												</button>
												<span :title="search.query">{{ search.query }}</span>
											</span>
										</td>
										<td><span>{{ search.searches }}</span></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</v-tab>
			</vue-tabs>
		</div>
	</div>
</template>

<script>
import Color from 'color';
import Modal from './Modal.vue';
import { __ } from './../helpers.js';
import LineChart from '../LineChart.js';
import cloneDeep from 'lodash.clonedeep';

export default {
	name: 'Statistics',
	components: {
		LineChart,
		Modal
	},
	methods: {
		reset: function() {
			if (confirm(__('_confirm_statistics_reset'))) {
				this.apiRequest( 'reset_statistics', null );
			}
		},
		apiRequest: function(action, query) {
			let vm = this;

			// If we're unignoring we can render that right away.
			if ('unignore_query'===action) {
				vm.ignored.splice(vm.ignored.indexOf(query), 1);
			}

			vm.updating = true;
			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + action,
				query: JSON.stringify(query)
			}, function(response) {
				vm.updating = false;

				if (response.success) {
					vm.stats = cloneDeep(response.data.engines);
					vm.ignored = cloneDeep(response.data.ignored);
				} else {
					alert(__('Update FAILED!'));
				}
			});
		}
	},
	data() {
		return {
			updating: false,
			activeTabColor: _SEARCHWP.misc.colors.hover,
			inactiveTabColor: _SEARCHWP.misc.colors.base,
			borderColor: _SEARCHWP.misc.colors.hover,
			backgroundColor: Color(_SEARCHWP.misc.colors.hover).hsl().lighten(0.6).desaturate(0.3).alpha(0.1).string(),
			pointBackgroundColor: _SEARCHWP.misc.colors.hover,
			pointBorderColor: _SEARCHWP.misc.colors.hover,
			stats: cloneDeep(_SEARCHWP.stats.engines),
			ignored: cloneDeep(_SEARCHWP.stats.ignored)
		}
	}
}
</script>

<style lang="scss">
	@import './../global.scss';

	.searchwp-settings-statistics {

		.searchwp-settings-view-header {
			width: 100%;
		}

		.searchwp-settings-statistics-actions {
			margin: 0 0 0 auto;
		}
	}

	.searchwp-settings-statistics-charts {
		width: 100%;
		position: relative;

		&.searchwp-settings-statistics-charts-updating {
			opacity: 0.4;

			&:after {
				content: '';
				position: absolute;
				z-index: 99999;
				background: transparent;
				content: '';
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
			}
		}

		.postbox {
			margin: 0;
		}

		.vue-tabs.stacked {
			display: flex;

			.nav-tabs-wrapper {
				height: 100%;
				display: flex;
				flex-direction: row;
			}

			.nav-tabs {
				flex-direction: column;
				background-color: #fafafa;
				display: flex;
				padding-top: 0.55em;
				padding-right: 0.5em;

				> li {
					min-width: 7em;

					a {
						display: inline-block;
					}
				}
			}

			.tab-content {
				flex: 1;
				display: flex;
			}

			.tab-container {
				position: relative;
				display: flex;
				flex-wrap: wrap;
				width: 100%;
				height: 100%;
				flex-direction: column;

				> h3 {
					padding-left: 20px; // Needs to match the offset of the chart.
				}
			}
		}
	}

	.searchwp-settings-statistics-chart {
		position: relative;
		height: 0;
		overflow: hidden;
		padding-top: 300px; // This is the height of the rendered chart.

		.searchwp-settings-statistics-chart-line {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
	}

	.searchwp-settings-statistics-chart-details {
		width: 100%;
		padding: 1em 20px;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;

		> * {
			width: 21%;
			margin-bottom: 1.5em;
		}
	}

	.searchwp-settings-view .searchwp-settings-statistics-chart-detail {

		table {
			margin: 1em 0;

			thead th:nth-child(2) {
				text-align: right;
			}

			button {
				display: inline-block;
				margin: 0 4px 0 0;
				padding: 0;
				border: 0;
				cursor: pointer;
				background: transparent;
			}

			.searchwp-search-count {
				text-align: right;
			}

			td {
				vertical-align: top;
				padding: 0.5em 0;

				&:nth-child(2) span {
					display: block;
					text-align: right;
					padding-top: 0.25em;
					padding-right: 2px;
				}

				span {
					display: flex;
					align-items: flex-start;

					> span {
						display: block;
						padding-top: 0.05em;
						line-height: 1.4;
					}
				}
			}
		}
	}

	@media screen and (max-width:1024px) {
		.searchwp-settings-statistics-chart-details > * {
			width: 48%;
		}
	}

	@media screen and (max-width:800px) {
		.searchwp-settings-statistics-chart-details > * {
			width: 100%;
		}
	}
</style>
