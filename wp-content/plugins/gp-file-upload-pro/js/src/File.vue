<template>
	<li :data-file-id="file.id" class="gpfup__file">
		<ImagePreview :file="file" :enable-crop="enableCrop" :crop-required="cropRequired" :only-images-allowed="onlyImagesAllowed" />

		<div class="gpfup__file-info">
			{{ file.name }}
			<div class="gpfup__filesize">{{ getSize(file) }}</div>

			<template v-if="!existingFile">
				<LoadingBar :file="file" />
			</template>
		</div>

		<div class="gpfup__file-actions">
			<button @click.prevent="deleteFile(file, $event)" class="gpfup__delete" :data-gpfup-filename="file.name">
				<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 16 16" role="img" aria-hidden="true" focusable="false"><path d="M11.55,1.65,7.42,5.78,11.55,9.9,9.9,11.55,5.77,7.43,1.66,11.55,0,9.89,4.12,5.78,0,1.66,1.66,0,5.77,4.12,9.9,0Z"/></svg>
			</button>
		</div>

	</li>
</template>

<script lang="ts">
	import Vue from 'vue';
	import ImagePreview from "./ImagePreview.vue";
	import bytes from "bytes";
	import isImage from "./helpers/isImage";
	import LoadingBar from "./LoadingBar.vue";
	import removeFileFromGFUploadedMeta from "./helpers/deleteFileFromHiddenGFInput";

	export default Vue.extend({
		name: "File",
		props: [
			'file',
			'formId',
			'fieldId',
			'up',
			'enableCrop',
			'cropRequired',
			'onlyImagesAllowed',
		],
		data: function() {
			return {
				/**
				* Flag for whether or not a file has already been uploaded and this is a subsequent visit/reload.
				*
				* This is required so we can bypass the CSS animation.
				*/
				existingFile: false,
			}
		},
		beforeMount() {
			/**
			* If this component is mounted and the file percent is immediately 100%, we know that it's an existing file.
			*/
			if (this.file.percent === 100) {
				this.existingFile = true;
			}
		},
		components: {
			LoadingBar,
			ImagePreview,
		},
		methods: {
			getSize: function (file: MOxieFile) : string {
				return bytes(file.size);
			},
			isImage: function (file: MOxieFile) : boolean {
				return isImage(file);
			},
			deleteFile: function (file: MOxieFile, event: Event) : void {
			try {
				removeFileFromGFUploadedMeta(this.formId, this.fieldId, file);
				this.up.removeFile(file.id);
			} catch (e) {
				console.error('Error deleting file.', e);
			}
			},
		}
	});
</script>

<style>
	.gpfup__file {
		padding: 1rem;
		display: flex;
		position: relative;
	}

	.gpfup__file:not(:last-of-type) {
		border-bottom: 1px solid #ddd;
	}

	.gpfup__filesize {
		color: #999;
	}

	.gpfup__file-actions {
		position: absolute;
		top: 1rem;
		right: 1rem;
		display: flex;
	}

	.gpfup__delete {
		display: inline-block;
		width: 1.5rem;
		height: 1.5rem;
		margin-left: auto;
		cursor: pointer;
		padding: 0.2rem;
		background-color: transparent;
		border: 0;
		border-radius: 50%;
	}

	.gpfup__file-info {
		text-overflow: ellipsis;
		white-space: nowrap;
		flex: 1;
		overflow: hidden;
		margin-right: 24px;
	}
</style>
