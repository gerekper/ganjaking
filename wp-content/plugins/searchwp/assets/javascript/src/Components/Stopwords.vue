<template>
	<MetaBox
		class="searchwp-stopwords"
		:active="true"
		:label="'Stopwords'">
		<template v-slot:heading>
			<span>{{ 'Stopwords' | i18n }}</span>
			<Menu :items="menuItems">
				<button class="button">{{ 'Actions' | i18n }}</button>
			</Menu>
			<button
				class="button button-primary"
				:disabled="!updated"
				@click.stop="save()">
				{{ 'Save' | i18n }}
			</button>
		</template>
		<template v-slot:content>
			<div class="inside searchwp-stopwords-input" @click="focusInput">
				<component :is="i18nStopwordsNote"></component>
				<p class="description" v-if="!stopwords.length">{{ '_no_stopwords_note' | i18n }}</p>
				<v-select
					:class="'searchwp-input-tags'"
					ref="stopwordsInput"
					v-model="stopwords"
					:multiple="true"
					:noDrop="true"
					:searchable="true"
					:filterable="false"
					:taggable="true"
					@input="normalize"></v-select>
			</div>

			<Modal :name="'suggestions'"
				:label="'Suggested Stopwords' | i18n"
				:actionIsPrimary="true"
				:actionLabel="'Done' | i18n">
				<p>{{ '_suggested_stopwords_note' | i18n }}</p>
				<p class="description" v-if="!availableStopwordsSuggestions.length">{{ '_no_suggested_stopwords_note' | i18n }}</p>
				<table v-else class="searchwp-data-table">
					<thead>
						<tr>
							<th>{{ 'Term' | i18n }}</th>
							<th>{{ 'Prevalence' | i18n }}</th>
							<th>{{ 'Add Stopword' | i18n }}</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(suggestion, index) in availableStopwordsSuggestions" :key="index">
							<td>{{ suggestion.token }}</td>
							<td>{{ suggestion.prevalence }}%</td>
							<td>
								<button class="button" @click="add(suggestion.token)">
									<span>{{ 'Add Stopword' | i18n }}</span>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</Modal>
		</template>
	</MetaBox>
</template>

<script>
import Menu from './Menu.vue';
import Modal from './Modal.vue';
import MetaBox from './MetaBox.vue';
import { __ } from './../helpers.js';
import isEqual from 'lodash.isequal';
import cloneDeep from 'lodash.clonedeep';

export default {
	name: 'Stopwords',
	components: {
		Menu,
		Modal,
		MetaBox
	},
	computed: {
		i18nStopwordsNote: function() {
			return {
				template: '<p>' + __('_stopwords_note') + '</p>'
			};
		},
		updated: function() {
			return !isEqual(this.original, this.stopwords);
		},
		availableStopwordsSuggestions: function() {
			return this.stopwordsSuggestions.filter(stopword => -1 === this.stopwords.indexOf(stopword.token));
		},
		menuItems: function() {
			let vm = this;

			let items = [
				{
					text: __('Sort Alphabetically'),
					click: function() { vm.sort() }
				},
				{
					text: __('Restore Defaults'),
					click: function() { vm.stopwords = vm.defaults; }
				},
				{
					text: __('Clear Stopwords'),
					click: function() { vm.clear() }
				}
			];

			if (this.stopwordsSuggestions) {
				items.unshift({
					text: __('View Suggestions'),
					click: function() { vm.$modal.show('suggestions'); }
				});
			}

			return items;
		}
	},
	methods: {
		focusInput: function(el) {
			this.$refs.stopwordsInput.$refs.search.focus();
		},
		add: function(stopword) {
			this.stopwords.push(stopword);
		},
		clear: function() {
			this.stopwords = [];
		},
		sort: function() {
			this.stopwords = this.stopwords.sort(function(a, b) {
				if (a.toLowerCase() > b.toLowerCase()) {
					return 1;
				} else if (b.toLowerCase() > a.toLowerCase()) {
					return -1;
				} else {
					return 0;
				}
			});
		},
		save: function() {
			let vm = this;

			jQuery.post(ajaxurl, {
				_ajax_nonce: _SEARCHWP.nonce,
				action: _SEARCHWP.prefix + 'stopwords_update',
				stopwords: JSON.stringify(vm.stopwords)
			}, function(response) {
				vm.stopwords = cloneDeep(response.data);
				vm.original = cloneDeep(response.data);
			});
		},
		normalize: function(stopwords) {
			this.stopwords = stopwords
				.reduce((arr, stopword) => arr.concat(
					stopword.split(',').map(stopword => stopword.trim().toLowerCase())
				), [])
				.filter((stopword, idx, array) => array.indexOf(stopword) === idx);
		},
		checkForUnsaved: function(event) {
			if (this.updated) {
				event.preventDefault();
				// Chrome requires returnValue to be set.
				event.returnValue = '';
			}
		}
	},
	created() {
		let vm = this;

		if (!_SEARCHWP.stopwords.suggest) {
			return;
		}

		jQuery.post(ajaxurl, {
			_ajax_nonce: _SEARCHWP.nonce,
			action: _SEARCHWP.prefix + 'stopwords_suggestions'
		}, function(response) {
			if (response.success) {
				vm.stopwordsSuggestions = response.data;
				vm.loadingSuggestions = false;
			}
		});
	},
	beforeMount() {
		window.addEventListener('beforeunload', this.checkForUnsaved);
	},
	beforeDestroy() {
		window.removeEventListener('beforeunload', this.checkForUnsaved);
	},
	data() {
		return {
			loadingSuggestions: true,
			original: cloneDeep(_SEARCHWP.stopwords.list),
			stopwords: cloneDeep(_SEARCHWP.stopwords.list),
			defaults: cloneDeep(_SEARCHWP.stopwords.defaults),
			stopwordsSuggestions: []
		}
	}
}
</script>

<style lang="scss">
	.searchwp-stopwords {

		table {
			margin-top: 2em;
		}
	}

	.searchwp-stopwords-input {
		min-height: 50vh;
	}

	.searchwp-settings-view .v-select .vs__search {
		min-width: 10em;
	}
</style>
