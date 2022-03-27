<!-- @todo Figure out a way to DRY up the repetition with files. Passthrough wrapper component? -->
<template>
	<div class="gpfup gpfup--strict" v-bind:class="containerClasses">
		<MountingPortal v-if="enableCrop" :mountTo="portalMountTo" :name="portalName" target-slim>
			<GPFUPCropper
				v-if="enableCrop"
				:strings="strings"
				:crop-required="cropRequired"
				:min-width="minWidth || exactWidth"
				:min-height="minHeight || exactHeight"
				:aspect-ratio="aspectRatio"
				:up="up"
				ref="app"
				:open="editorOpen"
				:file="currentFile"
				:field-id="fieldId"
				:form-id="formId"
			/>
		</MountingPortal>

		<ul class="gpfup__files" v-if="allFiles.length">
			<draggable
				v-if="enableSorting"
				v-bind="dragOptions"
				v-model="allFiles"
				:force-fallback="true"
				@start="onDragStart"
				@end="onDragEnd"
			>
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
						:enable-crop="enableCrop"
						:enable-sorting="enableSorting"
					/>

					<ErredFile
						v-else
						:key="`error-${file.id}`"
						:file="file"
						:error-message="file.error" />
				</template>
			</draggable>
			<template v-else v-for="file in allFiles">
				<File
					v-if="!file.error"
					:key="file.id"
					:file="file"
					:crop-required="cropRequired"
					:only-images-allowed="onlyImagesAllowed"
					:form-id="formId"
					:field-id="fieldId"
					:up="up"
					:enable-crop="enableCrop"
					:enable-sorting="enableSorting"
				/>

				<ErredFile
					v-else
					:key="`error-${file.id}`"
					:file="file"
					:error-message="file.error" />
			</template>
		</ul>

		<Droparea :strings="strings" :on-drop="drop" :on-browse="browse" :files="files" :reached-max-files="reachedMaxFiles" />
	</div>
</template>

<script lang="ts">
	import GPFUPCropper from './GPFUPCropper.vue';
	import File from "./File.vue";
	import ErredFile from "./ErredFile.vue";
	import Droparea from "./Droparea.vue";
	import {mapState} from 'vuex';
	import Vue from "vue";
	import draggable from 'vuedraggable';
	import PortalVue from "portal-vue";

	const $ = window.jQuery;

	Vue.use(PortalVue);

	const setDragCursor = (value: boolean) => {
		const html = document.getElementsByTagName('html').item(0);
		html?.classList.toggle('gpfup--dragging', value)
	}

	export default Vue.extend({
		name: "GPFUP",
		props: [
			'enableCrop',
			'enableSorting',
			'formId',
			'fieldId',
			'up',
			'strings',
			'cropRequired',
			'aspectRatio',
			'maxWidth',
			'maxHeight',
			'minWidth',
			'minHeight',
			'exactWidth',
			'exactHeight',
		],
		data: function() {
			return {
				drag: false,
			};
		},
		components: {
			File,
			ErredFile,
			Droparea,
			GPFUPCropper,
			draggable,
		},
		computed: {
			portalName: function() : string {
				return `gpfup-cropper-${this.formId}-${this.fieldId}`;
			},
			portalMountTo: function() : string {
				return `#gpfup-cropper-portal-${this.formId}-${this.fieldId}`;
			},
			...mapState({
				files: (state: any) => state.files,
				erredFiles: (state: any) => state.erredFiles,
				currentFile: (state: any) => state.editor.currentFile,
				editorOpen: (state: any) => state.editor.open,
			}),
			allFiles: {
				get() {
					return this.$store.getters.allFiles;
				},
				set(value: MOxieFile[]) {
					this.$store.commit('SET_FILE_ORDER', value.map((file) => file.id));
				}
			},
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

				/**
				 * Filter image extensions that will be used to determine if the uploader is only accepting images.
				 *
				 * @since 1.0-alpha-3.0
				 *
				 * @param string[] 	extensions  Allowed image extensions
				 */
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
			containerClasses: function() {
				const classes: { [className: string]: unknown } = {
					'gpfup--images-only': this.onlyImagesAllowed,
					'gpfup--has-error': this.erredFiles.length,
					'gpfup--maxed': this.reachedMaxFiles,
					'gpfup--sortable': this.enableSorting,
					'gpfup--has-files': this.files.length
				};

				/* Stub in cropper options to extract stencilComponent if present */
				const cropperOptions = window.gform.applyFilters('gpfup_cropper_options', {});

				if (cropperOptions?.stencilComponent) {
					classes[`gpfup--${cropperOptions?.stencilComponent}`] = true;
				}

				return classes;
			},
			dragOptions() {
				return {
					animation: 200,
					handle: '.gpfup__drag-handle',
					group: `gpfup-files-${this.formId}-${this.fieldId}`,
					disabled: false,
					ghostClass: "ghost",
				};
			}
		},
		methods: {
			browse: function () : void {
				$(`#field_${this.formId}_${this.fieldId} div.moxie-shim input[type=file]`).trigger('click');
			},
			drop: function (event: DragEvent) : void {
				this.up.addFile(Array.from(event.dataTransfer!.files));
			},
			onDragStart() {
				setDragCursor(true);
			},
			onDragEnd() {
				setDragCursor(false);
			},
		},
	});
</script>

<style>
	.gpfup * {
		box-sizing: border-box;
	}

	/* https://github.com/SortableJS/Vue.Draggable/issues/815#issuecomment-600637288 */
	.gpfup--dragging * {
		cursor: grabbing !important;
	}

	.gpfup__files {
		margin: 0;
		list-style: none;
		border-radius: 2px;
		border: 1px solid rgba( 0, 0, 0, 0.10 );
		position: relative;
		box-shadow: 0 2px 2px rgba( 0, 0, 0, 0.05 );
		z-index: 2;
		margin-bottom: -1px;
		padding: 0;
	}
</style>
