<template>
	<div class="searchwp-checkbox">
		<div class="searchwp-checkbox-input">
			<input
				type="checkbox"
				:disabled="disabled"
				:id="id"
				v-model="val"/>
			<label :for="id"><slot></slot></label>
		</div>
		<slot name="description"></slot>
	</div>
</template>

<script>
export default {
	name: 'Checkbox',
	model: {
		prop: 'checked',
		event: 'change'
	},
	props: {
		checked: {
			type: Boolean
		},
		id: {
			type: String,
			required: true
		},
		disabled: {
			type: Boolean,
			default: false
		}
	},
	watch: {
		val(value) {
			this.$emit('change', value);
		}
	},
	data() {
		return {
			val: this.checked,
		};
	}
}
</script>

<style lang="scss">
	.searchwp-checkbox-input {
		display: flex;

		input {
			display: block;
			margin-top: 0.1em;
		}

		label {
			display: block;
			padding-bottom: 0.2em;
			padding-left: 3px;
			line-height: 1.4;
		}

		.dashicons.has-tooltip {
			width: 16px;
			height: 16px;
			font-size: 18px;
		}
	}
</style>
