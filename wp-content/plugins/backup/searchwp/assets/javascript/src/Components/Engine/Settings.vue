<template>
	<div class="searchwp-engine-settings">

		<div class="searchwp-engine-settings-sources">
			<h3>{{ 'Sources' | i18n }}</h3>
			<p>{{ 'Search these sources:' | i18n }}</p>
			<ul>
				<li v-for="(source, sourceName) in models" :key="sourceName">
					<div class="searchwp-engine-settings-source">
						<div class="searchwp-engine-settings-source-toggle">
							<div>
								<Checkbox
									:disabled="isInvalidSource(sourceName)"
									:id="name + '-' + sourceName"
									:value="sourceName"
									:checked="-1!==sourceIndex(sourceName)"
									@change="function(value) { toggleSource(sourceIndex(sourceName), sourceName, value) }"
								>{{ source.labels.plural }}</Checkbox>
							</div>
							<ul class="searchwp-engine-settings-source-notices"
								v-if="source.notices.length || isInvalidSource(sourceName)">
								<li v-for="(notice, noticeIndex) in source.notices"
									:key="noticeIndex">
									<Notice
										:type="notice.type"
										:icon="notice.icon"
										:message="notice.message"
										:tooltip="notice.tooltip"
										:more="notice.more"
									></Notice>
								</li>
								<li v-if="isInvalidSource(sourceName)">
									<Notice :tooltip="'_invalid_default_engine_source_note' | i18n "></Notice>
								</li>
							</ul>
						</div>
					</div>
				</li>
			</ul>
		</div>

		<div class="searchwp-engine-settings-options">
			<h3>{{ 'Options' | i18n }}</h3>
			<div v-if="'default'!==name" class="searchwp-engine-settings-option">
				<label for="searchwp-engine-settings-option-engine-label">{{ 'Engine Label' | i18n }}</label>
				<div class="searchwp-engine-settings-option-input">
					<input
						v-bind:value="label"
						v-on:input="updateName($event.target.value)"
						class="searchwp-input-mimic"
						type="text"
						id="searchwp-engine-settings-option-engine-label" />
				</div>
			</div>
			<div v-if="'default'!==name" class="searchwp-engine-settings-option">
				<label for="searchwp-engine-settings-option-engine-name">{{ 'Engine Name' | i18n }}</label>
				<div class="searchwp-engine-settings-option-input">
					<input
						v-model="name"
						disabled
						class="searchwp-input-mimic"
						style="font-family: monospace;"
						type="text"
						id="searchwp-engine-settings-option-engine-name" />
				</div>
			</div>
			<div class="searchwp-engine-settings-option">
				<label for="searchwp-engine-settings-option-keyword-stemming">
					{{ 'Keyword Stems' | i18n }}
				</label>
				<div class="searchwp-engine-settings-option-input">
					<Checkbox
						:id="'searchwp-engine-settings-option-keyword-stemming'"
						:value="stemming"
						:checked="stemming"
						@change="function(value) { stemming = value }"
					><Tooltip :content="'_keyword_stems_note' | i18n">{{ 'Use keyword stems' | i18n }}</Tooltip></Checkbox>
				</div>
			</div>
			<div class="searchwp-engine-settings-option">
				<label for="searchwp-engine-settings-option-admin-engine">
					{{ 'Admin Engine' | i18n }}
				</label>
				<div class="searchwp-engine-settings-option-input">
					<Checkbox
						v-if="false === currentAdminEngine || currentAdminEngine.name === name"
						:id="'searchwp-engine-settings-option-admin-engine'"
						:value="adminengine"
						:checked="adminengine"
						@change="function(value) { adminengine = value }"
					><Tooltip :content="'_admin_engine_tooltip' | i18n">{{ 'Use for Admin searches' | i18n }}</Tooltip></Checkbox>
					<Notice v-else
						:type="'info'"
						:message="'_admin_engine_defined_note' | i18n([currentAdminEngine.label])"
					></Notice>
				</div>
			</div>
		</div>

		<ul class="searchwp-actions">
			<li>
				<button
					v-if="'default'!==name"
					class="button button-link-delete"
					@click="remove">
					{{ 'Delete Engine' | i18n }}
				</button>
			</li>
			<li><button class="button button-primary" @click="save">{{ 'Done' | i18n }}</button></li>
		</ul>
	</div>
</template>

<script>
import slugify from 'slugify';
import Notice from './../Notice.vue';
import Tooltip from './../Tooltip.vue';
import { __ } from './../../helpers.js';
import cloneDeep from 'lodash.clonedeep';
import Checkbox from './../Inputs/Checkbox.vue';
import { normalizeSource } from './../../helpers.js';
import { EngineUtils } from './../../Mixins/EngineUtils.js';

export default {
	name: 'EngineSettings',
	props: {
		engine: {
			type: String,
			required: true,
		}
	},
	components: {
		Checkbox,
		Notice,
		Tooltip
	},
	mixins: [EngineUtils],
	computed: {
		wpPostSources: function() {
			let sources = [];
			const flag = 'post' + _SEARCHWP.separator;

			for (const source in this.models) {
				if (flag === source.substring(0, flag.length)) {
					sources.push(source);
				}
			}

			return sources;
		},
		currentAdminEngine: function() {
			return this.$store.getters.adminEngine;
		}
	},
	methods: {
		isInvalidSource: function(sourceName) {
			// The only case where a Source is invalid is if it's the Default Engine
			// and the Source is not representing a WP_Post content type.
			return 'default' === this.engine && -1 === this.wpPostSources.indexOf(sourceName);
		},
		remove: function() {
			if (confirm(__('Are you sure you want to delete this engine?'))) {
				this.$emit('delete', this.engine);
			}
		},
		updateName: function(label) {
			this.label = label;

			let name = slugify(label.replace(/ /gi, '_'), {
				replacement: '_',
				remove: /[^a-z0-9_]/gi,
				lower: true
			});

			while (-1!==this.engines.indexOf(name)) {
				name += '_copy';
			}

			this.name = name;
		},
		sourceIndex: function(sourceName) {
			let sourceIndex = -1;

			for (const index in this.sources) {
				if (sourceName===this.sources[index].name) {
					sourceIndex = index;
					break;
				}
			}

			return sourceIndex;
		},
		toggleSource: function(sourceIndex, sourceName, enabled) {
			if (!enabled && -1!==sourceIndex) {
				// Remove an added Source.
				this.sources.splice(sourceIndex, 1);

				// Flag this Source as removed for save routine.
				this.removed.push(sourceName);
			} else if (enabled) {
				// Add a new Source from the models.
				let source = normalizeSource(cloneDeep(this.models), sourceName);

				this.sources.push(source);

				// UNflag this Source as removed for save routine.
				if (-1 !== this.removed.indexOf(sourceName)) {
					this.removed.splice(this.removed.indexOf(sourceName), 1);
				}
			}
		},
		save: function() {
			let sources = {};

			this.sources.map(source => sources[source.name] = source);

			for (const removedSource in this.removed) {
				this.$store.commit('engineSourceRemoved', {
					engine: this.engine,
					source: this.removed[removedSource]
				});
			}

			this.$emit('save', {
				sources: sources,
				originalEngineName: this.engine,
				newEngineName: this.name,
				newEngineLabel: this.label,
				stemming: this.stemming,
				adminengine: this.adminengine
			});
		}
	},
	created() {
		this.label       = this.getEngineProperty(this.engine, 'label');
		this.name        = this.getEngineProperty(this.engine, 'name');
		this.stemming    = this.getEngineProperty(this.engine, 'settings').stemming;
		this.adminengine = this.getEngineProperty(this.engine, 'settings').adminengine;

		const sources = this.getEngineProperty(this.engine, 'sources');
		for (const source in sources) {
			this.sources.push(cloneDeep(sources[source]));
		}
	},
	data () {
		return {
			sources: [],
			removed: [],
			models: cloneDeep(_SEARCHWP.sources),
			label: '',
			name: '',
			stemming: false,
			adminengine: false,
			engines: Object.keys(this.$store.state.engines)
		}
	}
}
</script>

<style lang="scss">
	.searchwp-settings-view .searchwp-engine-settings {

		> .searchwp-actions {
			margin: 2em 0 0;

			> * {
				margin-left: auto;

				&:nth-child(1) {
					margin-left: 0;
				}
			}
		}
	}

	.searchwp-engine-settings-sources {

		ul {
			margin: 0;
			padding: 0;
			list-style: none;
			display: flex;
			flex-wrap: wrap;

			> li {
				width: 33.33333%;
				padding-right: 1em;
			}
		}
	}

	.searchwp-engine-settings-source {
		margin-bottom: 0.3em;
	}

	.searchwp-engine-settings-source-toggle {
		min-height: 2.1em;
		display: flex;
		align-items: center;

		> *,
		.searchwp-notice,
		.searchwp-notice p,
		ul li {
			display: inline-block;
		}

		.searchwp-notice p {
			margin: 0 0 0.3em;
		}
	}

	.searchwp-engine-settings-options {
		margin-top: 2em;
	}

	.searchwp-engine-settings-option {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 0.75em;

		> label {
			display: block;
			width: 35%;
			font-weight: 500;
		}
	}

	.searchwp-engine-settings-option-input {
		width: 62%;

		input[type="text"] {
			display: block;
			width: 100%;
		}

		// Implement visual offset.
		.searchwp-checkbox {
			padding-top: 0.8em;
			padding-bottom: 0.4em;
		}

		.notice {
			margin: 0;
		}
	}
</style>
