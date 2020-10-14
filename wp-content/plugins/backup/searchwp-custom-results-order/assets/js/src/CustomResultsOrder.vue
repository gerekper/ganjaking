<template>

	<div class="searchwp-cro metabox-holder">

		<div v-if="triggers.length" class="searchwp-cro-triggers postbox">

			<div class="inside">

				<div class="searchwp-cro-triggers-container">
					<Trigger
						v-for="(trigger, triggerIndex) in triggers"
						:key="'trigger' + triggerIndex"
						:query="trigger.query"
						:engine="trigger.engine"
						:exact="trigger.exact"
						:expanded="trigger.expanded"
						@remove="removeTrigger(triggerIndex, trigger.query, trigger.engine)"
					/>
				</div>

			</div>

		</div>

		<ul class="searchwp-cro-triggers-actions">
			<li class="searchwp-cro-trigger-query">
				<input
					type="text"
					v-model="formattedQuery"
					placeholder="Enter search query"
					@keyup.enter="addTrigger()"
				/>
			</li>
			<li class="searchwp-cro-trigger-exact">
				<input type="checkbox" id="searchwp-cro-trigger-exact" v-model="exact">
				<label for="searchwp-cro-trigger-exact">
					<tooltip :content="'Partial matches are not recommended'">Exact</tooltip>
				</label>
			</li>
			<li class="searchwp-cro-trigger-engine">
				<select v-model="triggerEngine">
					<option
						v-for="(engine, engineIndex) in engineOptions"
						:value="engineIndex"
						:key="'triggerEngine' + engineIndex">
							{{ engine.label }} Engine
					</option>
				</select>
			</li>
			<li class="searchwp-cro-add-trigger">
				<button
					class="button button-primary"
					@click="addTrigger()"
				>Add Search Query</button>
			</li>
		</ul>

	</div>

</template>

<script>
import Vue from 'vue';
import Trigger from './Trigger';

export default {
	name: 'CustomResultsOrder',
	components: {
		Trigger
	},
	methods: {
		addTrigger() {
			if (this.newQuery.length < 1) {
				alert('Query cannot be empty!');
				return;
			}

			let trigger = new Vue.Factory('trigger');

			// Enforce our defaults.
			trigger.query = this.newQuery;
			trigger.engine = this.engineOptions[this.triggerEngine];
			trigger.expanded = true;
			trigger.exact = this.exact;

			// Add this trigger to the list.
			this.triggers.push(trigger);

			// Reset UI.
			this.newQuery = '';

			this.saveTriggers();
		},
		saveTriggers() {
			const triggers = this.triggers.map(trigger => {
				return {
					query: trigger.query,
					engine: trigger.engine,
					exact: trigger.exact
				};
			});

			const data = {
				_ajax_nonce: _SEARCHWP_CRO_VARS.nonce,
				action: 'searchwp_cro_save_triggers',
				triggers: JSON.stringify(triggers)
			};

			jQuery.post(ajaxurl, data, function(response) {});
		},
		removeTrigger(triggerIndex, query, engine) {
			this.triggers.splice(triggerIndex, 1);
			this.saveTriggers();

			const data = {
				_ajax_nonce: _SEARCHWP_CRO_VARS.nonce,
				action: 'searchwp_cro_clear_buoys',
				query: query,
				engine: engine.name,
			};

			jQuery.post(ajaxurl, data, function(response) {});
		}
	},
	created() {
		// We only know about engine names at this point as that's all that's saved
		// in the database, so we need to cycle through and retrieve labels too.
		let triggers = [];

		for (var trigger in this.$root.$data.triggers) {
			if (this.$root.$data.triggers.hasOwnProperty(trigger)) {
				const thisTrigger = this.$root.$data.triggers[trigger];

				let triggerEngineLabel = 'Default';

				for (var engine in this.$root.$data.engines) {
					if (thisTrigger.engine==engine && this.$root.$data.engines.hasOwnProperty(engine)) {
						const thisEngine = this.$root.$data.engines[engine];
						triggers.push({
							query: thisTrigger.query,
							exact: thisTrigger.exact,
							engine: {
								name: engine,
								label: thisEngine.hasOwnProperty('searchwp_engine_label') ? thisEngine.searchwp_engine_label : 'Default'
							}
						});
					}
				}
			}
		}

		this.triggers = triggers;
	},
	computed: {
		formattedQuery: {
			get: function() {
				return this.newQuery;
			},
			set: function(value) {
				this.newQuery = value.toLowerCase();
			}
		},
		engineOptions() {
			let engines = [];

			for (var engine in this.$root.$data.engines) {
				if (this.$root.$data.engines.hasOwnProperty(engine)) {
					const thisEngine = this.$root.$data.engines[engine];
					engines.push({
						name: engine,
						label: thisEngine.hasOwnProperty('searchwp_engine_label') ? thisEngine.searchwp_engine_label : 'Default'
					});
				}
			}

			return engines;
		},
	},
	data() {
		return {
			newQuery: '',
			exact: true,
			triggerEngine: 0,
			triggers: []
		}
	}
}
</script>

<style lang="scss">
	#wpbody-content .metabox-holder.searchwp-cro {
		padding: 0;
	}

	.searchwp-cro * {
		box-sizing: border-box;
	}

	.searchwp-cro .inside {
		padding-bottom: 0;
	}

	.searchwp-extension-back {
		display: none;
	}

	.searchwp-cro-triggers-actions {
		display: flex;
		align-items: center;
		margin: 0 0 0.6em;
		padding: 0;
		list-style: none;
	}

	.searchwp-cro-trigger-query,
	.searchwp-cro-trigger-engine {
		padding-right: 0.7em;
		width: 100%;
		max-width: 200px;
	}

	.searchwp-cro-trigger-exact {
		padding-top: 4px;
		padding-right: 2em;
		display: flex;
		align-items: center;

		label {
			padding-bottom: 2px;
		}
	}

	.searchwp-cro-trigger-query input {
		padding: 5px 7px;
		display: block;
		width: 100%;
	}

	.searchwp-cro-trigger-engine select {
		display: block;
		width: 100%;
	}
</style>
