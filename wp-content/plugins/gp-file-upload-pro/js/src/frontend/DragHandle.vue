<template>
	<div class="gpfup__drag-handle" :class="{ 'gpfup__drag-handle--disabled': disabled }"></div>
</template>

<script lang="ts">
import Vue from "vue";

export default Vue.extend({
	name: "DragHandle",
	props: [
		'disabled',
	],
});
</script>

<style>
.gpfup__drag-handle {
	display: flex;
	width: 1rem;
	margin-right: 0.8rem;
	margin-left: -0.2rem;
}

.gpfup--dragging .gpfup__drag-handle {
	pointer-events: none;
}

.gpfup__drag-handle:before {
	content: "";
	width: 1rem;
	height: 1rem;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' width='25' height='25'%3E%3Crect y='10' x='25' width='12' height='10' rx='6'/%3E%3Crect y='10' x='60' width='12' height='12' rx='6'/%3E%3Crect y='45' x='25' width='12' height='12' rx='6'/%3E%3Crect y='45' x='60' width='12' height='12' rx='6'/%3E%3Crect y='80' x='25' width='12' height='12' rx='6'/%3E%3Crect y='80' x='60' width='12' height='12' rx='6'/%3E%3C/svg%3E%0A");
	background-size: contain;
	speak: never;
	font-style: normal;
	font-weight: normal;
	font-feature-settings: normal;
	font-variant: normal;
	text-transform: none;
	line-height: 1;
	-webkit-font-smoothing: antialiased;
	font-size: 1.4rem;
	margin: auto 0;
	cursor: grab;
}

.gpfup__drag-handle--disabled {
	pointer-events: none;
	opacity: .15;
}
</style>
