<template>
	<div class="gpfup" v-bind:class="{ 'gpfup--images-only': onlyImagesAllowed, 'gpfup--has-error': erredFiles.length, 'gpfup--maxed': reachedMaxFiles }">
		<GPFUPCropper
				v-if="enableCrop"
				:crop-required="cropRequired"
				:up="up"
				ref="app"
				:open="editorOpen"
				:file="currentFile"
				:field-id="fieldId"
				:form-id="formId"
		/>

		<ul class="gpfup__files" v-if="allFiles.length">
			<template v-for="file in allFiles">
				<File
						v-if="!file.error"
						:key="file.id"
						:file="file"
						:crop-required="cropRequired"
						:only-images-allowed="onlyImagesAllowed"
						:form-id="formId"
						:field-id="fieldId"
						:up="up"
						:enable-crop="enableCrop" />

				<ErredFile
						v-else
						:key="`error-${file.id}`"
						:file="file"
						:error-message="file.error" />
			</template>
		</ul>

		<Droparea :on-drop="drop" :on-browse="browse" :files="files" :reached-max-files="reachedMaxFiles" />
	</div>
</template>

<script lang="ts">
	import GPFUPCropper from './GPFUPCropper.vue';
	import File from "./File.vue";
	import ErredFile from "./ErredFile.vue";
	import Droparea from "./Droparea.vue";
	import {mapState, mapGetters} from 'vuex';
	import Vue from "vue";

	const $ = window.jQuery;

	export default Vue.extend({
		name: "GPFUP",
		props: ['enableCrop', 'formId', 'fieldId', 'up', 'cropRequired'],
		components: {
			File,
			ErredFile,
			Droparea,
			GPFUPCropper
		},
		computed: {
			...mapState({
				files: (state: any) => state.files,
				erredFiles: (state: any) => state.erredFiles,
				currentFile: (state: any) => state.editor.currentFile,
				editorOpen: (state: any) => state.editor.open,
			}),
			...mapGetters(['allFiles']),
			maxFiles: function() {
				const maxFiles = parseInt(this.up.settings.gf_vars.max_files, 10);

				return maxFiles > 0 ? maxFiles : Infinity;
			},
			reachedMaxFiles: function() {
				// @ts-ignore
				return this.files.length >= this.maxFiles;
			},
			onlyImagesAllowed: function() {
				const extensions = this.up?.settings?.filters?.mime_types[0]?.extensions.split(',');
				const imageExtensionWhitelist = window.gform.applyFilters(
					'gpfup_image_extension_whitelist',
					['jpg', 'jpeg', 'png', 'gif']
				);

				let onlyImages = true;

				if (extensions && extensions.length) {
					for ( let extension of extensions ) {
						extension = extension.trim();

						if (extension && imageExtensionWhitelist.indexOf(extension) === -1) {
							onlyImages = false;
							break;
						}
					}
				}

				return onlyImages;
			},
		},
		methods: {
			browse: function () : void {
				$(`#field_${this.formId}_${this.fieldId} div.moxie-shim input[type=file]`).trigger('click');
			},
			drop: function (event: DragEvent) : void {
				this.up.addFile(Array.from(event.dataTransfer!.files));
			},
		},
	});
</script>

<style>
	.gpfup * {
		box-sizing: border-box;
	}

	.gpfup__files {
		margin: 0;
		list-style: none;
		border-radius: 2px;
		border: 1px solid #ddd;
		position: relative;
		box-shadow: 0 2px 2px rgba( 0, 0, 0, 0.05 );
		z-index: 2;
		margin-bottom: -1px;
		padding: 0;
	}
</style>
