<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue, {PropType} from 'vue';
import RepeaterButtons from "./RepeaterButtons.vue";

export default Vue.extend({
	name: 'Repeater',
	components: {RepeaterButtons},
	props: {
		value: {
			required: true,
			type: Array as PropType<any[]>
		},
		labelAddItem: {
			type: String,
			required: true,
		},
		labelRemoveItem: {
			type: String,
			required: true,
		},
		// Callback to do things like show a confirmation before deleting if desired
		beforeRemoveItem: {
			type: Function,
		},
		// Callback to create items when Add Item button is clicked
		itemFactory: {
			type: Function,
		},
	},
	computed: {},
	methods: {
		add() {
			this.$emit('input', this.value.concat([this?.itemFactory() ?? {}]));
		},
		remove(index: number) {
			const value = [...this.value];

			if (this?.beforeRemoveItem(index, value) === false) {
				return;
			}

			value.splice(index, 1);

			this.$emit('input', value);
		}
	}
});
</script>

<template>
	<ul class="gpi-repeater" v-if="value && value.length">
		<li v-for="(item, index) of value">
			<slot :item="item" :item-index="index"></slot>

			<RepeaterButtons
				:label-add-item="labelAddItem"
				:label-remove-item="labelRemoveItem"
				:show-add="false"
				@add="add"
				@remove="remove(index)"/>
		</li>

		<button class="button-secondary gpi-repeater-add" @click="add">{{ labelAddItem }}</button>
	</ul>
	<div v-else style="display: flex;">
		<button class="button-secondary gpi-repeater-add" @click="add">{{ labelAddItem }}</button>
	</div>
</template>

<style>
.gpi-repeater li {
	display: flex;
}
</style>
