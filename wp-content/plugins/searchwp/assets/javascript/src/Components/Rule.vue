<template>
	<p :class="[ 'searchwp-engine-source-rules-overview-rule', disabled ? 'searchwp-engine-source-rules-overview-rule-disabled' : '' ]">
		<span class="searchwp-engine-source-rules-overview-rule-name">{{ rules[name].label }}</span>
		<span v-if="Array.isArray(rules[name].options) && rules[name].options.length"
				class="searchwp-engine-source-rules-overview-rule-option">
			{{ rules[name].options.filter(optionsOption => optionsOption.value === option)[0].label }}
		</span>
		<span class="searchwp-engine-source-rules-overview-rule-condition"><code>{{ condition }}</code></span>
		<span class="searchwp-engine-source-rules-overview-rule-value">{{ valueDisplay }}</span>
	</p>
</template>

<script>
export default {
	name: 'Rule',
	props: {
		rules: {
			type: Object,
			required: true
		},
		name: {
			type: String,
			required: true
		},
		option: {
			type: String|Boolean,
			required: false
		},
		condition: {
			type: String,
			required: false
		},
		value: {
			type: String|Array|Boolean,
			required: true
		},
		source: {
			type: String,
			required: true
		}
	},
	methods: {
		cycleValue: function() {
			let self = this;

			if (this.option && Array.isArray(this.value) && this.rules[this.name].options && this.rules[this.name].options.length) {
				// Get the labels for the chosen Options.
				this.valueDisplay = this.value.map(value => value.label).join(', ');
			} else if (Array.isArray(this.value)) {
				// Concat the value array into CSV.
				if (typeof this.value[0] === 'string' || typeof this.value[0] === 'number') {
					this.valueDisplay = this.value.join(', ');
				} else {
					this.valueDisplay = this.value.map(value => value.label).join(', ');
				}
			} else {
				// It's a string.
				this.valueDisplay = this.value;
			}
		}
	},
	created() {
		this.cycleValue();
	},
	watch: {
		value: function (newOption, oldOption) {
			this.cycleValue();
		}
	},
	data () {
		return {
			disabled: false,
			valueDisplay: null
		}
	}
}
</script>

<style lang="scss">

</style>
