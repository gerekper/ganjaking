import * as Plupload from 'plupload';
import Vue from 'vue';
import GPFUP from './GPFUP.vue';
import GPFUPStore from './stores/GPFUPStore';
import isImage from "./helpers/isImage";
import Storage from './classes/Storage';
import { debounce } from 'lodash';
import sortHidddenGFInput from "./helpers/sortHidddenGFInput";

const $ = window.jQuery;

/**
 * @class Class to enhance a Gravity Forms multi-file uploader
 */
export default class GPFUPField {

	public vm: Vue | undefined;

	public $store: any;

	public fieldId: number;

	public formId: number;

	public enableCrop: boolean;

	public cropRequired: boolean;

	public $field: HTMLElement | null;

	public $preview: HTMLElement | null;

	public cropperContainerSelector: string;

	public storage: Storage;

	get Uploader(): Plupload.Uploader {
		const uploaderKey = `gform_multifile_upload_${this.formId}_${this.fieldId}`;

		return window.gfMultiFileUploader.uploaders[uploaderKey];
	}

	constructor(initSettings: GPFUPFieldInitSettings) {
		this.formId = initSettings.formId;
		this.fieldId = initSettings.fieldId;
		this.enableCrop = initSettings.enableCrop;
		this.cropRequired = initSettings.cropRequired;
		this.cropperContainerSelector = '.gform_heading';

		this.removeGFPreview();

		$(document).on('gform_post_render', async (e, formId) => {
			if (formId != this.formId) {
				return;
			}

			/**
			* Ensure selectors for $field and $preview are still valid after post render. This is typically needed
			* for things like GPNF.
			*/
			this.$field = document.querySelector(`#field_${this.formId}_${this.fieldId} .ginput_container_fileupload`);
			this.$preview = document.querySelector(`#gform_preview_${this.formId}_${this.fieldId} `);

			this.storage = new Storage(this.formId, this.fieldId);

			this.$store = GPFUPStore({
				storage: this.storage,
			});

			/* Remove GF preview again in case it still exists. Needed for GPNF. */
			this.removeGFPreview();

			this.addStoreSubscriptions();

			$(this.$field!).find('.validation_message').remove();

			await this.rehydrateFiles();

			// Wait for this.Uploader before calling addVM and addPluploadFilters
			// The specific case here occurs in a WC product page. See #23362.
			// The source is WC `$.fn.wc_gravity_form running` and failing `gfMultiFileUploader.setup(this);`
			// See explanation in #9#issuecomment-808988375
			let uploaderRetry = 0;
			let initMaxRetry = 10;
			const waitForUploader = ()=>{
				if ( ! this.Uploader ) {
					if ( uploaderRetry < initMaxRetry ) {
						uploaderRetry++;
						return window.setTimeout( waitForUploader, 10 );
					}
					return;
				}

				this.addVM();
				this.addPluploadFilters();
				return;
			};
			waitForUploader();
		});
	}

	/**
	* Rehydrate Plupload using the Gravity Forms uploaded files hidden input.
	*
	* In addition to adding the files from the hidden input, we pull from localforage to add in other info such as
	* the file type, size, preview size, etc.
	*/
	async rehydrateFiles() {
		const uploadedFilesInput:HTMLInputElement|null = document.querySelector(`#gform_uploaded_files_${this.formId}`);

		if (!uploadedFilesInput) {
			return;
		}

		let uploadedFilesInForm;

		try {
			uploadedFilesInForm = JSON.parse(uploadedFilesInput.value);
		} catch (e) {
			this.storage.purge();
			return;
		}

		const uploadedFilesInField:GFFile[] = uploadedFilesInForm?.[`input_${this.fieldId}`];

		if (!uploadedFilesInField?.length) {
			this.storage.purge();
			return;
		}

		for ( const uploadedFile of uploadedFilesInField ) {
			const file = new window.mOxie.File(null, new Blob(['asdf']));
			file.name = uploadedFile.uploaded_filename;
			file.id = uploadedFile.temp_filename.match(/o_[a-z0-9]+(?=\.)/)?.[0];

			if (!file.id) {
				file.id = uploadedFile.temp_filename;
			}

			file.size = undefined;
			file.percent = 100;
			file.status = plupload.DONE;

			const fileInfo = await this.storage.getFileInfo(file.id);
			const dimensions = await this.storage.getPreviewDimensions(file.id);

			if (fileInfo) {
				const { size, type, addedDate} = fileInfo;

				if (size) {
					file.size = size;
				}

				if (type) {
					file.type = type;
				}

				if (addedDate) {
					file.addedDate = addedDate;
				}
			}

			if (dimensions) {
				file.previewWidth = dimensions.width;
				file.previewHeight = dimensions.height;
			}

			this.Uploader.files.push(file);
			this.$store.commit('ADD_FILE', file);
		}

		this.$store.commit('SET_FILES', this.Uploader.files);
	}

	addVM() {
		if (!this.Uploader) {
			console.debug('Plupload not ready yet. Cannot add Vue.');
			return;
		}

		const parent = this;

		if (!jQuery(`#gform_drag_drop_area_${this.formId}_${this.fieldId}`).length) {
			return;
		}

		if (this.vm) {
			this.vm.$destroy();
		}

		this.vm = new Vue({
			el: `#gform_drag_drop_area_${this.formId}_${this.fieldId}`,
			store: this.$store,
			render: function(h) : Vue.VNode {
				return h(GPFUP, {
					props: {
						fieldId: this.fieldId,
						formId: this.formId,
						up: this.up,
						enableCrop: this.enableCrop,
						cropRequired: this.cropRequired,
					},
					ref: 'root',
				});
			},
			data: () => ({
				formId: this.formId,
				fieldId: this.fieldId,
				enableCrop: this.enableCrop,
				cropRequired: this.cropRequired,
				/**
				* not 100% sure why this happens but by adding this.Uploader here to the data, it makes the properties
				* Observables
				*/
				up: this.Uploader,
			}),
			/**
			* Progressively rehydrate the field from localforage.
			*/
			mounted: async function() {
				for (const file of this.up.files) {
					const previewBase64 = await parent.storage.getPreview(file.id);

					if (previewBase64) {
						parent.$store.commit('ADD_IMAGE_PREVIEW', Object.freeze({
							fileId: file.id,
							base64: previewBase64,
						}));
					}
				}

				/* Add originals (only applies if cropping is enabled) */
				for (const file of this.up.files) {
					const original = await parent.storage.getOriginal(file.id);

					if (original?.size && original?.src) {
						const { size, src } = original;

						parent.$store.commit('STORE_ORIGINAL', {
							fileId: file.id,
							src,
							size,
						});
					}
				}

				/* Add coords (only applies if cropping is enabled) */
				for (const file of this.up.files) {
					const coords = await parent.storage.getCoords(file.id);

					if (coords) {
						parent.$store.commit('STORE_CROPPED_COORDS', {
							fileId: file.id,
							coords: coords,
						});
					}
				}
			},
			watch: {
				/**
				* Debounce is used here to prevent a flash of files that exceed the max file limit.
				*/
				'up.files': debounce((newValue: any) => {
					/**
					* Automatically copy files from Plupload when it changes so we can import/export Vuex state to
					* rapidly develop UI.
					*/
					this.$store.dispatch('setFiles', newValue);
				}, 10),
			},
			components: {
				GPFUP
			}
		});

	}

	removeGFPreview() {
		$(`#gform_preview_${this.formId}_${this.fieldId}`).remove();
	}

	handleFileError(up: Plupload.Uploader, file: any, err: { code: string | number, message: string }) {
		const strings = window.gform_gravityforms.strings;

		up.removeFile(file);

		if(err.code === plupload.FILE_EXTENSION_ERROR){
			var extensions = typeof up.settings.filters.mime_types != 'undefined'
				? up.settings.filters.mime_types[0].extensions /* plupoad 2 */
				: up.settings.filters[0].extensions;

			this.$store.commit('PUSH_ERRED_FILE', {
				file: file,
				error: `${strings.invalid_file_extension} ${extensions}`
			});
		} else if (err.code === plupload.FILE_SIZE_ERROR) {
			this.$store.commit('PUSH_ERRED_FILE', {
				file,
				error: strings.file_exceeds_limit,
			});
		} else {
			this.$store.commit('PUSH_ERRED_FILE', {
				file,
				error: err.message,
			});
		}
	}

	addPluploadFilters() {
		if (!this.Uploader) {
			console.debug('Plupload not ready yet. Cannot add filters.');
			return;
		}

		this.Uploader.bind('BeforeUpload', (up: Plupload.Uploader, file: any) => {
			if (isImage(file)) {
				this.$store.dispatch('storeImagePreview', {
					fileId: file.id,
					blob: file.getNative(),
				});
			}

			/**
			* Add a date that we can sort using. This is primarily for consolidated erred files with uploaded files.
			*/
			if (!file.addedDate) {
				file.addedDate = new Date();
			}

			if (!file.cropped && this.cropRequired && isImage(file)) {
				this.$store.dispatch('setCurrentFileAndOpenEditor', {
					file,
					/*
					* Only clear out the previous image if uploading 1 image at a time. If we clear out the image when
					* cropping a set, it can create jank and make things like the "Cropping X of X" to hide then reshow
					*/
					clearPrevious: this.$store.state.currentAddedFiles <= 1,
					/*
					* Use a slight delay when opening the first image (or single image) when cropping is required.
					*
					* Minor detail, but it smooths out the UX.
					*/
					delayMs: this.$store.getters.currentImageIndex === undefined ? 300 : undefined,
				});

				// Prevent uploading until the file is cropped.
				return false;
			}

			return true;
		});

		this.Uploader.bind('FilesAdded', (up: Plupload.Uploader, files: MOxieFile[]) => {
			/**
			* When replacing files, we use addFile in Plupload which triggers FilesAdded. Without this conditional,
			* it will cause the SET_CURRENT_ADDED_FILES to run with only the file that was cropped which hinders the
			* required cropping flow.
			*/
			if (!(up as any).replacingFile) {
				this.$store.commit('SET_CURRENT_ADDED_FILES', files);
			} else {
				delete (up as any).replacingFile;
			}

			for ( const file of files ) {
				this.$store.commit('ADD_FILE', file);
			}
		});

		this.Uploader.bind('FileUploaded', (up: Plupload.Uploader, file: any, result: any) => {
			var response = JSON.parse(result.response);

			if(response.status == 'error'){
				this.handleFileError(up, file, response.error);
			}

			this.$store.commit('ADD_FILE', file);

			sortHidddenGFInput(this.formId, this.fieldId, this.$store.state.fileOrder);
		});

		this.Uploader.bind('FilesRemoved', (up: Plupload.Uploader, files: MOxieFile[]) => {
			for ( const file of files ) {
				this.storage.purgeFile(file);
				this.$store.commit('REMOVE_FILE', file);
			}
		});

		this.Uploader.bind('Error', (up: Plupload.Uploader, err: plupload_error) => {
			this.$store.commit('ADD_FILE', err.file);

			this.handleFileError(up, err.file, err);
		});
	}

	/**
	* Add various subscriptions on the store
	*/
	addStoreSubscriptions() {
		this.$store.subscribe((mutation: any, state: any) => {
			/**
			* If we're in a GPNF Tingle modal, we need to check the overflow to recalculate the height of the modal
			* so scrolling remains possible.
			*/
			const $gpnfModal = $(this.$field!).closest('.gpnf-modal');
			if ($gpnfModal.length) {
				let nestedFormParentID: string;
				let nestedFormFieldID: string;

				$gpnfModal[0].classList.forEach((value) => {
					const match = /gpnf-modal-(\d+)-(\d+)/.exec(value);

					if (!match) {
						return;
					}

					nestedFormParentID = match[1];
					nestedFormFieldID = match[2];
				});

				// @ts-ignore
				window?.[`GPNestedForms_${nestedFormParentID!}_${nestedFormFieldID!}`].modal?.checkOverflow();
			}
		});
	}
}
