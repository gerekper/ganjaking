<template>
	<transition name="gpfup__cropper--transition">
		<div v-if="open" class="cropper__lightbox" style="z-index: 99999999;">
			<cropper
				v-if="imgSrc"
				class="cropper"
				ref="cropper"
				v-bind="cropperOptions"
			/>

			<div class="gpfup__cropper__topbar">
				<button
					class="gpfup__cancel"
					@click.prevent="cancel"
					v-shortkey="['esc']"
					@shortkey.prevent="cancel()"
				>
					{{ strings.cancel }}
				</button>

				<span class="gpfup__cropper_count"
					  v-if="currentImageIndex && totalNumberOfFiles && totalNumberOfFiles > 1">
					{{ strings.cropping }} {{ currentImageIndex }} {{ strings.croppingOf }} {{ totalNumberOfFiles }}
				</span>

				<button class="gpfup__crop" @click.prevent="crop" :disabled="!imgSrc">
					{{ strings.crop }}
				</button>
			</div>
		</div>
	</transition>
</template>

<script lang="ts">
// @ts-ignore
import {Cropper} from 'vue-advanced-cropper';
import Vue from 'vue';
import {canvasToBlob} from "blob-util";
import loadImage from 'blueimp-load-image';
import {mapState, mapGetters} from "vuex";
import deleteFileFromHiddenGFInput from "./helpers/deleteFileFromHiddenGFInput";
import replaceFile from "./helpers/replaceFile";
import triggerUpload from "./helpers/triggerUpload";

const $ = window.jQuery;

Vue.use(require('vue-shortkey'));

export default Vue.extend({
	name: "GPFUPCropper",
	props: [
		'open',
		'file',
		'strings',
		'up',
		'formId',
		'fieldId',
		'cropRequired',
		'aspectRatio',
		'minWidth',
		'minHeight',
	],
	components: {
		Cropper
	},
	computed: {
		cropperOptions: function (): { [key: string]: any } {
			/**
			 * Filter the options/properties that are sent to
			 * [vue-advanced-cropper](https://www.npmjs.com/package/vue-advanced-cropper).
			 *
			 * @since 1.0-beta-2.0
			 *
			 * @param object 			options     Options to send to vue-advanced-cropper
			 * @param int           	formId 		The current form ID
			 * @param int             	fieldId   	The uploader field ID
			 */
			return window.gform.applyFilters('gpfup_cropper_options', {
				src: this.imgSrc,
				defaultBoundaries: 'fit',
				imageRestriction: 'fill-area',
				minWidth: this.minWidth,
				minHeight: this.minHeight,
				defaultSize: this.defaultSize(),
				defaultPosition: this.defaultPosition(),
				stencilProps: this.stencilProps,
			}, this.formId, this.fieldId);
		},
		stripMetadata: function (): boolean {
			/**
			 * Filter whether or not image metadata (EXIF) should be stripped from the image when uploaded.
			 *
			 * Disabling metadata is useful if you need to maintain original metadata such as DPI, camera
			 * settings, etc.
			 *
			 * @since 1.0.4
			 *
			 * @param boolean 			stripMetadata   Whether or not to strip metadata/EXIF of the image. Defaults to true.
			 * @param int           	formId 			The current form ID
			 * @param int             	fieldId   		The current uploader field ID
			 */
			return window.gform.applyFilters('gpfup_strip_image_metadata', true, this.formId, this.fieldId);
		},
		imgSrc: function (): string {
			return this.$store.getters.imgSrc;
		},
		...mapState({
			currentFile: state => state.editor.currentFile,
		}),
		...mapGetters([
			'currentImageIndex',
		]),
		totalNumberOfFiles: function (): number {
			return this.$store.getters.currentAddedFiles.length;
		},
		stencilProps: function (): { [prop: string]: any } {
			const props = {};

			if (this.aspectRatio) {
				props.aspectRatio = this.aspectRatio;
			}

			return props;
		}
	},
	methods: {
		defaultSize: function (): { width: number, height: number } {
			return this.$store.getters.imgSize;
		},
		defaultPosition: function (): { top: number, left: number } {
			return this.$store.getters.imgPos;
		},
		crop: function (): void {
			const {coordinates, canvas}: { coordinates: Coords, canvas: HTMLCanvasElement } =
				(this.$refs.cropper as any).getResult();

			if (!canvas) {
				return;
			}

			let blobImageType = 'image/png';

			if (['image/jpg', 'image/jpeg'].includes(this.currentFile?.type)) {
				blobImageType = 'image/jpeg';
			}

			const jpegQuality = window.gform.applyFilters('gpfup_jpeg_quality', 0.92, this.formId, this.fieldId, (window as any)[`GPFUP_${this.formId}_${this.fieldId}`]);

			canvasToBlob(canvas, blobImageType, jpegQuality).then((blob) => {
				/* Create new file object for Plupload using blob and update file name */
				let file = new window.mOxie.File(null, blob);
				file.name = this.currentFile?.name;

				loadImage.parseMetaData(
					blob,
					async (data) => {
						if (data.imageHead && !this.stripMetadata) {
							file = await loadImage.replaceHead(blob, data.imageHead);
							file.name = this.currentFile?.name;
						}

						const newFile = replaceFile({
							up: this.up,
							$store: this.$store,
							formId: this.formId,
							fieldId: this.fieldId,
							existingFile: this.currentFile,
							newFile: file,
						})

						/* Set cropped flag to prevent infinite loop */
						newFile.cropped = true;

						/*
						 * Trigger upload if cropping required. If cropping not automatically required this trigger
						 * will actually cause conflicts with the status.
						 */
						if (this.cropRequired) {
							triggerUpload(this.up, newFile);
						}

						this.$store.dispatch('storeCroppedCoords', {
							fileId: newFile.id,
							coords: coordinates,
						});

						this.$store.dispatch('storeImagePreview', {
							fileId: newFile.id,
							blob
						});
					}
				)
			});

			if (
				!this.totalNumberOfFiles // not in forced cropping flow
				|| (this.currentImageIndex && this.currentImageIndex >= this.totalNumberOfFiles) // cropped all files
			) {
				this.$store.dispatch('closeEditor');
			}
		},
		cancel: function () {
			if (!this.currentFile.cropped && this.cropRequired) {
				for (const addedFile of this.$store.getters.currentAddedFiles) {
					this.up.removeFile(addedFile);
					const fileEl = $(`[data-file-id="${addedFile?.id}"]`);

					if (fileEl.length) {
						deleteFileFromHiddenGFInput(this.formId, this.fieldId, addedFile);
					}
				}

				for (const erredFile of this.$store.getters.currentAddedErredFiles) {
					this.$store.commit('REMOVE_ERRED_FILE', erredFile);
				}

				/**
				 * Needed so the queue doesn't get in a weird state and stop accepting new files.
				 */
				this.up.stop();
				this.up.start();
			}

			this.$store.dispatch('closeEditor');
		},
	}
});
</script>

<style>
@import url(~vue-advanced-cropper/dist/style.css);
@import url(~vue-advanced-cropper/dist/theme.classic.css);

.cropper__lightbox {
	background: rgba(0, 0, 0, .90);
	position: fixed;
	z-index: 10000;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	padding: 10vh;
}

.gpfup__cropper__topbar {
	display: flex;
	position: absolute;
	top: 20px;
	width: 100%;
	left: 0;
	padding: 0 20px;
	box-sizing: border-box;
}

.gpfup__cropper_count {
	color: #fff;
	text-align: center;
	line-height: 35px;
	flex: 1;
}

.vue-advanced-cropper__background {
	background: transparent;
}

.cropper {
	width: 100%;
	height: 100%;
}

.cropper__lightbox button {
	font-size: 16px;
	background: #56488f;
	padding: 10px 30px;
	border: 0;
	border-radius: 2px;
	color: white;
}

button.gpfup__crop {
	right: 20px;
	margin-left: auto
}

button.gpfup__cancel {
	background: #666;
	left: 20px;
}

.vue-handler-wrapper--south,
.vue-handler-wrapper--north {
	cursor: ns-resize;
}

.vue-handler-wrapper--west,
.vue-handler-wrapper--east {
	cursor: ew-resize;
}

.vue-handler-wrapper--east-north,
.vue-handler-wrapper--west-south {
	cursor: nesw-resize;
}

.vue-handler-wrapper--east-south,
.vue-handler-wrapper--west-north {
	cursor: nwse-resize;
}

/*.vue-advanced-cropper__area[style] {*/
/*    transition: all .3s ease-in-out !important;*/
/*}*/

.gpfup__cropper--transition-enter {
	opacity: 0;
}

.gpfup__cropper--transition-enter-active {
	transition: opacity .3s ease-out;
}

.gpfup__cropper--transition-enter-to {
	opacity: 1;
}

.gpfup__cropper--transition-leave-active {
	transition: opacity .3s ease-out;
}

.gpfup__cropper--transition-leave-to {
	opacity: 0;
}


</style>
