<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
	name: 'InputUnit',
	props: [
		'id',
		'value',
		'disabled',
		'unit',
	],
});
</script>

<template>
	<div class="gpfup-input-unit">
		<input
			type="text"
			:id="id"
			:disabled="disabled"
			@input="$emit('input', $event.target.value)"
			:value="value" />

		<span class="gpfup-input-unit__unit">{{ unit }}</span>
	</div>
</template>

<style>
.gpfup-input-unit {
	width: 100%;
	position: relative;
	display: inline-block;
	margin-top: 0.5rem !important;
}

.gpfup-input-unit input {
	width: 100%;
	padding-right: 26px !important;
	text-align: right;
}

.gpfup-input-unit__unit {
	position: absolute;
	right: 10px;
	top: 9px;
	color: #999;
}
</style>
