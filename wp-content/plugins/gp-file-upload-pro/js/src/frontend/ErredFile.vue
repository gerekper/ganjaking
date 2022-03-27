<template>
	<li :data-file-id="file.id" class="gpfup__file gpfup__file--has-error">
		<ImagePreview :file="file" :error="true" />

		<div class="gpfup__file-info">
			<div class="gpfup__filename">{{ file.name }}</div>
			<div class="gpfup__filesize">{{ getSize(file) }}</div>
			<div class="gpfup__file-error">{{ errorMessage }}</div>
		</div>

		<div class="gpfup__file-actions">
			<button @click.prevent="deleteFile(file, $event)" class="gpfup__delete">
				<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 16 16" role="img" aria-hidden="true" focusable="false"><path d="M11.55,1.65,7.42,5.78,11.55,9.9,9.9,11.55,5.77,7.43,1.66,11.55,0,9.89,4.12,5.78,0,1.66,1.66,0,5.77,4.12,9.9,0Z"/></svg>
			</button>
		</div>
	</li>
</template>

<script lang="ts">
	import Vue from 'vue';
	import ImagePreview from "./ImagePreview.vue";
	import bytes from "bytes";

	export default Vue.extend({
		name: "ErredFile",
		props: [
			'file',
			'fileIndex',
			'errorMessage',
		],
		components: {
			ImagePreview,
		},
		methods: {
			getSize: function (file: MOxieFile) : string {
				return bytes(file.size);
			},
			deleteFile: function () : void {
				this.$store.commit('REMOVE_ERRED_FILE', this.file);
			},
		}
	});
</script>

<style>
	.gpfup__file-error {
		color: #790000;
		white-space: initial;
	}
	.gpfup--sortable .gpfup__file--has-error {
		padding-left: 2.6rem;
	}
</style>
