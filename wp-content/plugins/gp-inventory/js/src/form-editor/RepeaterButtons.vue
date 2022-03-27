<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
	name: 'RepeaterButtons',
	props: {
		labelAddItem: {
			type: String,
			required: true,
		},
		labelRemoveItem: {
			type: String,
			required: true,
		},
		showAdd: {
			type: Boolean,
			default: true,
		},
		showRemove: {
			type: Boolean,
			default: true,
		}
	},
	computed: {
		isGF24: function() : boolean {
			return jQuery('body').hasClass('gf-legacy-ui');
		}
	}
});
</script>

<template>
	<div class="gpi-repeater-buttons">
		<button
			v-if="showAdd"
			class="field-choice-button field-choice-button--insert gform-st-icon gform-st-icon--circle-plus"
			:aria-label="labelAddItem"
			@click="$emit('add')"/>

		<button
			v-if="showRemove"
			class="field-choice-button field-choice-button--delete gform-st-icon gform-st-icon--circle-minus"
			:aria-label="labelRemoveItem"
			@click="$emit('remove')">
			<i class="gficon-subtract" aria-hidden="true" v-if="isGF24"></i>
		</button>
	</div>
</template>

<style>
.gpi-repeater-buttons {
	display: flex;
	align-items: center;
	margin-left: .25rem;
}

.gpi-repeater-buttons > button {
	margin-left: .25rem;
}
</style>
