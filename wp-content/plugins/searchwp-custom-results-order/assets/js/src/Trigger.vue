<template>

	<div :class="['searchwp-cro-trigger', loadingResults || waiting ? 'searchwp-cro--is-loading' : '']">

		<h3 class="searchwp-cro-trigger__heading">
			<span @click="toggleDetails()" class="searchwp-cro-trigger__label">
				<span v-if="!showingDetails" class="dashicons dashicons-arrow-right"></span>
				<span v-else class="dashicons dashicons-arrow-down"></span>
				<span class="searchwp-cro-trigger__label-details">
					{{ query }}
					<span>
						<span v-if="exact">Exact match</span>
						<span v-else>Partial match</span>,
						{{ engine.label }} engine
					</span>
				</span>
			</span>
			<button class="searchwp-cro-trigger__remove" @click="remove()">Remove</button>
		</h3>

		<div v-if="showingDetails" class="searchwp-cro-trigger__details">

			<div class="searchwp-cro-trigger__results">

				<div
					v-if="loadingResults || waiting"
					class="searchwp-cro-trigger__results--loading-indicator">
					<spinner
						line-bg-color="#dfdfdf"
						:spacing="5"
						message="Loading search results"
						:lineSize="4" />
				</div>

				<p class="description" v-if="!exact && !loadingResults && !waiting">
					<span class="dashicons dashicons-info"></span>
					Note: These search results <strong>can not</strong> reflect partial match queries
				</p>

				<p v-if="!searchResults.length && !loadingResults && !waiting" class="searchwp-cro-error">
					<span>There are no search results to customize</span>
				</p>
				<ol v-else class="searchwp-cro__results-list">
					<li
						v-for="(result, resultIndex) in searchResults"
						:key="'result' + resultIndex"
						class="searchwp-cro__result">
						<result
							:promoted="isPromoted(result.ID)"
							:title="result.post_title"
							:id="result.ID"
							:rank="resultIndex + 1"
							@release="release($event)"
							@promote="promote($event)"
						></result>
					</li>
				</ol>

				<div v-if="hasMoreResults && searchResults.length && !loadingResults && !waiting"
					:class="[ 'searchwp-cro-loading-paged-container', loadingPagedResults ? 'searchwp-cro--is-loading-paged' : 'searchwp-cro--not-loading-paged']"
				>
					<button class="button" @click="loadNextResults()">Load More Results</button>
					<div v-if="loadingPagedResults" class="searchwp-cro-loading-paged">
						<spinner size="small" line-bg-color="#dfdfdf" />
					</div>
				</div>

				<p v-if="!searchResults.length && errorGettingSearchResults && !loadingResults && !waiting" class="searchwp-cro-error"><span>There was an error retrieving search results</span></p>

			</div>
		</div>

	</div>

</template>

<script>
import Vue from 'vue';

export default {
	name: 'Trigger',
	components: {},
	props: {
		query: {
			type: String,
			default: '',
			required: true
		},
		exact: { // There is no attached functionality to this yet.
			type: Boolean,
			default: true
		},
		engine: Object,
		expanded: {
			type: Boolean,
			default: false
		}
	},
	components: {},
	methods: {
		toggleDetails() {
			this.showingDetails = !this.showingDetails;
			this.loadingResults = this.showingDetails;
		},
		remove() {
			this.$emit('remove', {
				query: this.query,
				engine: this.engine.name
			});
		},
		getSearchResults() {
			const data = {
				_ajax_nonce: _SEARCHWP_CRO_VARS.nonce,
				action: 'searchwp_cro_get_search_results',
				query: this.query,
				engine: this.engine.name,
				page: this.paged
			};

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
		triggerRefreshResultsUpdate() {
			// There's a watcher on loadingResults so we just need to toggle that.
			this.loadingResults = true;
		},
		refreshSearchResults() {
			let self = this;
			this.paged = 1;

			this.getSearchResults()
				.then(response => {
					self.loadingResults = false;
					if (response.success) {
						self.searchResults = response.data.results;
						self.id = response.data.id;
						self.promoted = response.data.promoted;
						self.paged = response.data.paged;
						self.maxNumPages = response.data.max_num_pages;
					} else {
						self.errorGettingSearchResults = true;
					}
				});
		},
		loadNextResults() {
			this.loadingPagedResults = true;
			this.paged = this.paged + 1;

			let self = this;

			this.getSearchResults()
				.then(response => {
					self.loadingPagedResults = false;
					if (response.success) {
						self.searchResults = [...self.searchResults, ...response.data.results];
						self.paged = response.data.paged;
						self.max_num_pages = response.data.maxNumPages;
					} else {
						self.errorGettingSearchResults = true;
					}
				});
		},
		isPromoted(postID) {
			return this.promoted.indexOf(postID) > -1;
		},
		promote(postID) {
			this.waiting = true;

			const data = {
				_ajax_nonce: _SEARCHWP_CRO_VARS.nonce,
				action: 'searchwp_cro_promote_result',
				postId: postID,
				query: this.query,
				engine: this.engine.name
			};

			let self = this;

			jQuery.post(ajaxurl, data, function(response) {}).always(function() {
				self.triggerRefreshResultsUpdate();
				self.waiting = false;
			});
		},
		release(postID) {
			this.waiting = true;

			const data = {
				_ajax_nonce: _SEARCHWP_CRO_VARS.nonce,
				action: 'searchwp_cro_release_result',
				postId: postID,
				query: this.query,
				engine: this.engine.name
			};

			let self = this;

			jQuery.post(ajaxurl, data, function(response) {}).always(function() {
				self.triggerRefreshResultsUpdate();
				self.waiting = false;
			});
		}
	},
	created() {
		if (this.expanded) {
			this.showingDetails = true;
			this.loadingResults = true;
		}
	},
	computed: {
		hasMoreResults() {
			return this.maxNumPages > this.paged;
		}
	},
	watch: { // Observer for when we need to update search results.
		loadingResults: function(loading, oldVal) {
			if (this.showingDetails && loading) {
				this.refreshSearchResults();
			}
		},
		showingDetails: function(showing, oldVal) {
			if (this.showingDetails && showing) {
				this.refreshSearchResults();
			}
		}
	},
	data() {
		return {
			id: '',
			showingDetails: false,
			loadingResults: false,
			loadingPagedResults: false,
			waiting: false,
			searchResults: [],
			promoted: [],
			errorGettingSearchResults: false,
			paged: 1,
			maxNumPages: 1
		}
	}
}
</script>

<style lang="scss">
	.searchwp-cro-trigger {
		background-color: #fafafa;
		border: 1px solid #ddd;
		border-radius: 2px;
		margin-top: 1em;
	}

	.searchwp-cro-trigger__heading {
		margin: 0;
		padding: 8px 1em 8px 4px; // From .hndle mostly, but left padding accommodates extra space from dashicon
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 14px;
		line-height: 1.4;
	}

	.searchwp-cro-trigger__label {
		display: flex;
		align-items: center;
		flex: 1;
		cursor: pointer;

		.dashicons:before {
			color: #BCBCBC;
		}
	}

	.searchwp-cro-trigger__label-details {
		display: flex;
		align-items: center;

		> span {
			display: inline-block;
			border-radius: 2px;
			line-height: 1;
			padding: 0.3em 0.5em;
			font-size: 0.8em;
			margin-left: 1em;
			background: #e6e6e6;
			font-weight: normal;
		}
	}

	.searchwp-cro-trigger__remove {
		display: block;
		border: 0;
		padding: 0;
		line-height: 1;
		background: transparent;
		margin-left: 1em;
		font-weight: normal;
		font-size: 0.9em;
		cursor: pointer;
		color: #aa0000;
		text-decoration: underline;

		&:hover {
			text-decoration: none;
		}
	}

	.searchwp-cro-trigger__details {
		padding: 0.5em;
		border-top: 1px solid #ddd;

		> p {
			margin-top: 0;
		}

		p.description {
			padding: 0.5em 0 0.5em 0.25em;
		}
	}

	.searchwp-cro-trigger__results {
		position: relative;
		min-height: 120px;
	}

	.searchwp-cro__results-list {
		margin: 0;
		padding: 0;
		list-style: none;
	}

	.searchwp-cro--is-loading {

		.searchwp-cro__results-list {
			opacity: 0;
		}
	}

	.searchwp-cro-error {
		margin: 0;
		padding: 0 1em;
		text-align: center;
		color: #aa0000;
		font-weight: bold;
		font-style: italic;
		line-height: 1.5;
		font-size: 1.2em;

		span {
			display: block;
		}
	}

	.searchwp-cro-error,
	.searchwp-cro-trigger__results--loading-indicator {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.searchwp-cro-loading-paged-container {
		position: relative;
		padding: 1em 0 1em 0.5em;
	}

	.searchwp-cro-loading-paged {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		align-items: center;

		.vue-simple-spinner {
			margin: 1em 0.5em !important;
		}
	}

	.searchwp-cro--is-loading-paged {

		button {
			visibility: hidden;
		}
	}

	.searchwp-cro--not-loading-paged {

	}
</style>
