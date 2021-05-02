<template>
	<div :class="{ 'gppa-results': true, 'gppa-results-loading': loading }"
		 v-if="field && objectTypeInstance.fieldValueObject !== true">
		<div :id="'gppa-results-' + populate + '-thickbox'" style="display: none;" v-if="results && results.length">
			<div class="gppa-results-preview-contents">
				<div class="notice notice-warning notice-alt"
					 style="margin: 0 0 15px;"
					 v-if="results.length >= limit * .8">
					<p>For optimal performance, only the first {{ limit }} results will be populated.
						Increase this limit using the <a href="https://gravitywiz.com/documentation/gppa_query_limit/">gppa_query_limit</a> filter.</p>
				</div>

				<table class="wp-list-table widefat fixed striped">
					<thead>
					<th v-for="column in resultColumns">{{ column }}</th>
					</thead>
					<tbody>
					<tr v-for="row in results">
						<!-- v-html is used since columnValue will be escaped from the AJAX request. -->
						<td v-for="columnValue in row" v-html="columnValue"></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

		<template v-if="hasFilterFieldValue">
			<strong>Heads-up!</strong> Cannot preview results when filtering by Form&nbsp;Field&nbsp;Value.
		</template>
		<template v-else-if="missingTemplates.length">
			Select
			<span v-for="(missingTemplate, index) in missingTemplates">
						<strong>{{missingTemplate}}</strong><span
				v-if="index + 1 < missingTemplates.length"> and </span>
					</span>
			{{ missingTemplates.length > 1 ? 'templates' : 'template' }} to preview results.
		</template>
		<template v-else-if="results && 'error' in results">
			<strong>Error Loading Results:</strong> <code>{{ results.error }}</code>
		</template>
		<template v-else-if="loading">
			<img :src="spinnerUrl"/> Loading Results
		</template>
		<template v-else-if="results && results.length === 0">
			<strong>{{ results.length }}</strong> results found
		</template>
		<template v-else-if="results && results.length > 0">
			<a class="thickbox" title="Results Preview"
			   :href="'#TB_inline?width=600&height=450&inlineId=gppa-results-' + populate + '-thickbox'"
			   onClick="tb_click.call(this);">
				<strong>{{ results.length }}{{ results.length >= limit ? '+' : '' }}</strong> {{ results.length ===
				1 ? 'result' : 'results' }}</a>
			found.
		</template>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';
	import debounce from 'lodash/debounce';

	const $ = window.jQuery;

	const updateResults = function () {
		var vm = this;

		if (this.missingTemplates.length || this.hasFilterFieldValue) {
			return;
		}

		this.loading = true;

		this.getPreviewResults().done(function (results) {
			vm.loading = false;
			vm.limit = results.limit;
			vm.results = results.results;
		}).fail(function () {
			vm.limit = null;
			vm.results = null;
			vm.loading = false;
		});
	};

	export default Vue.extend({
		data: function () {
			return {
				loading: false,
				previewResultsPromise: null,
				results: null,
				limit: null,
				gfBaseUrl: window.GPPA_ADMIN.gfBaseUrl,
				spinnerUrl: window.gf_global.spinnerUrl,
			};
		},
		created: function () {
			this.updateResults();
		},
		props: [
			'populate',
			'enabled',
			'field',
			'objectTypeInstance',
			'filterGroups',
			'templates',
			'templateRows',
			'orderingMethod',
			'orderingProperty',
			'uniqueResults',
		],
		watch: {
			filterGroups: {
				handler: function () {
					this.updateResultsDebounced();
				},
				deep: true,
			},
			templates: {
				handler: function () {
					this.updateResultsDebounced();
				},
				deep: true,
			},
			orderingProperty: function () {
				this.updateResults();
			},
			orderingMethod: function () {
				this.updateResults();
			},
			uniqueResults: function () {
				this.updateResults();
			},
			objectTypeInstance: function () {
				this.results = null;
			},
		},
		computed: {
			resultColumns: function () {
				if (!this.results || !this.results.length) {
					return [];
				}

				return Object.keys(this.results[0]);
			},
			hasFilterFieldValue: function () {

				var hasFilterFieldValue = false;

				this.filterGroups.forEach(function (filterGroup) {
					filterGroup.forEach(function (filter) {
						if (typeof filter.value === 'string' && filter.value.indexOf('gf_field') === 0) {
							hasFilterFieldValue = true;
						}
					});
				});

				return hasFilterFieldValue;

			},
			missingTemplates: function () {
				var vm = this;
				var missingTemplates = [];

				this.templateRows.forEach(function (templateRow) {
					if (!vm.templates?.[templateRow.id] && templateRow.required) {
						missingTemplates.push(templateRow.label);
					}
				});

				return missingTemplates;
			},
		},
		methods: {
			updateResults: updateResults,
			updateResultsDebounced: debounce(updateResults, 500),
			getPreviewResults: function () {
				if (this.previewResultsPromise && this.previewResultsPromise.state() !== 'resolved') {
					this.previewResultsPromise.abort();
				}

				this.previewResultsPromise = $.post(window.ajaxurl, {
					action: 'gppa_get_query_results',
					templateRows: this.templateRows,
					gppaPopulate: this.populate,
					fieldSettings: JSON.stringify(window.field),
					security: window.GPPA_ADMIN.nonce,
				}, null, 'json');

				return this.previewResultsPromise;
			},
		}
	});
</script>
