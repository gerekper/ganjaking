import * as Plupload from 'plupload';
import Vue from 'vue';
import GPFUP from './GPFUP.vue';
import GPFUPStore from './stores/GPFUPStore';
import isImage from "./helpers/isImage";
import Storage from './classes/Storage';
import debounce from 'debounce';
import sortHidddenGFInput from "./helpers/sortHidddenGFInput";
import loadWithBlueimp from "./helpers/loadWithBlueimp";
import replaceFile from "./helpers/replaceFile";
import triggerUpload from "./helpers/triggerUpload";
import getImageSize from "./helpers/getImageSize";
import type loadImage from "blueimp-load-image";
import exifr from 'exifr';

const { jQuery: $, gform } = window;

/**
 * @class Class to enhance a Gravity Forms multi-file uploader
 */
export default class GPFUPField {

	public vm: Vue | undefined;

	public $store: any;

	public fieldId: string;

	public formId: string;

	public enableCrop: boolean;

	public enableSorting: boolean;

	public cropRequired: boolean;

	public aspectRatio: number | undefined;

	public maxWidth: number | undefined;

	public maxHeight: number | undefined;

	public minWidth: number | undefined;

	public minHeight: number | undefined;

	public exactWidth: number | undefined;

	public exactHeight: number | undefined;

	// @ts-ignore - set in constructor during gform_post_render
	public $field: HTMLElement | null;

	// @ts-ignore - set in constructor during gform_post_render
	public $preview: HTMLElement | null;

	public cropperContainerSelector: string;

	// @ts-ignore - set in constructor during gform_post_render
	public storage: Storage;

	get strings(): { [key: string]: string } {
		/**
		 * Filter the localized strings used on the File Uploader.
		 *
		 * @since 1.0.2
		 *
		 * @param object  		strings 		Localized strings.
		 * @param int           formId 			The current form ID
		 * @param int           fieldId   		The current uploader field ID
		 * @param {GPFUPField}	gpfupInstance 	Current File Upload Pro class instance
		 */
		return window.gform.applyFilters('gpfup_strings', {
			...window.gform_gravityforms.strings,
			...window.GPFUP_CONSTANTS.STRINGS
		}, this.formId, this.fieldId, this);
	}

	get Uploader(): Plupload.Uploader {
		const uploaderKey = `gform_multifile_upload_${this.formId}_${this.fieldId}`;

		return window.gfMultiFileUploader.uploaders[uploaderKey];
	}

	constructor(initSettings: GPFUPFieldInitSettings) {
		this.formId = initSettings.formId;
		this.fieldId = initSettings.fieldId;
		this.enableCrop = initSettings.enableCrop;
		this.enableSorting = initSettings.enableSorting;
		this.cropRequired = initSettings.cropRequired && initSettings.enableCrop; // Ensure cropping is enabled too (HS#:27331)
		this.aspectRatio = initSettings.aspectRatio;
		this.maxWidth = initSettings.maxWidth ? initSettings.maxWidth : undefined;
		this.maxHeight = initSettings.maxHeight ? initSettings.maxHeight : undefined;
		this.minWidth = initSettings.minWidth ? initSettings.minWidth : undefined;
		this.minHeight = initSettings.minHeight ? initSettings.minHeight : undefined;
		this.exactWidth = initSettings.exactWidth ? initSettings.exactWidth : undefined;
		this.exactHeight = initSettings.exactHeight ? initSettings.exactHeight : undefined;

		this.cropperContainerSelector = '.gform_heading';

		this.removeGFPreview();

		$(document).on('gform_post_render', async (e, formId) => {
			if (formId != this.formId) {
				return;
			}

			/**
			 * Add portal for cropper. JavaScript is used to improve compatibility with AJAX on multi page forms.
			 */
			if (this.enableCrop) {
				const portalId = `gpfup-cropper-portal-${this.formId}-${this.fieldId}`;

				$(`#${portalId}`).remove();
				$('body').append(`<div id="${portalId}"></div>`);
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

			// This needs to be awaited on as this is when we repopulate Plupload's file array.
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
				/**
				 * Do something after the uploader has been initialized.
				 *
				 * @since 1.1.12
				 *
				 * @param \GPFUPField gpfupField The instance of the GPFUPField class that has been initialized.
				 */
				gform.doAction( 'gpfup_uploader_ready', this );
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
	 *
	 * It's important that this happens prior to the VM mounting as the mounted() lifecycle method relies on the files
	 * being present.
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
			file.id = uploadedFile.temp_filename?.match(/o_[a-z0-9]+(?=\.)/)?.[0];

			if (!file.id && uploadedFile.temp_filename) {
				file.id = uploadedFile.temp_filename;
			}

			if (!file.id) {
				file.id = uploadedFile.uploaded_filename;
			}

			file.size = undefined;
			file.percent = 100;
			file.status = plupload.DONE;
			file.rehydrated = true;

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
						strings: this.strings,
						enableCrop: this.enableCrop,
						enableSorting: this.enableSorting,
						cropRequired: this.cropRequired,
						aspectRatio: this.aspectRatio,
						maxWidth: this.maxWidth,
						maxHeight: this.maxHeight,
						minWidth: this.minWidth,
						minHeight: this.minHeight,
						exactWidth: this.exactWidth,
						exactHeight: this.exactHeight,
					},
					ref: 'root',
				});
			},
			data: () => ({
				formId: this.formId,
				fieldId: this.fieldId,
				strings: this.strings,
				enableCrop: this.enableCrop,
				enableSorting: this.enableSorting,
				cropRequired: this.cropRequired,
				aspectRatio: this.aspectRatio,
				maxWidth: this.maxWidth,
				maxHeight: this.maxHeight,
				minWidth: this.minWidth,
				minHeight: this.minHeight,
				exactWidth: this.exactWidth,
				exactHeight: this.exactHeight,
				/**
				* not 100% sure why this happens but by adding this.Uploader here to the data, it makes the properties
				* Observables
				*/
				up: this.Uploader,
			}),
			/**
			* Progressively rehydrate the field from server and localforage.
			*/
			mounted: async function() {
				/* Fetch previews */
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

				/* Fetch from server */
				const rehydrationInfo: null | { [fileId: string]: { url?: string, size: number, type: string }} = (window as any)[`gpfup_rehydration_${this.formId}_${this.fieldId}`];

				if (rehydrationInfo) {
					// Loop over files, set size and type, and set loading flag if URL is present to show spinner.
					for (const file of this.up.files) {
						const id = file.id ?? file.name;
						const previewBase64 = await parent.storage.getPreview(file.id);

						file.size = rehydrationInfo[id].size;
						file.type = rehydrationInfo[id].type;

						if (previewBase64 || !rehydrationInfo[id].url) {
							continue;
						}

						file.loading = true;
					}

					// Fetch previews/originals from server if not present in localforage
					for (const file of this.up.files) {
						const id = file.id ?? file.name;
						const previewBase64 = await parent.storage.getPreview(file.id);
						const url = rehydrationInfo[id]?.url;

						if (previewBase64 || !url) {
							file.loading = false;
							continue;
						}

						const request = new Request(url);
						const response = await fetch(request);
						const blob = await response.blob();

						file.getNative = () => blob;
						file.loading = false;

						await this.$store.dispatch('storeOriginal', file);
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
		const { strings } = this;

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
			/**
			 * Action to process/validate file prior to upload.
			 *
			 * @since 1.1.1
			 *
			 * @param int           		formId 			The current form ID
			 * @param int           		fieldId   		The current uploader field ID
			 * @param {MOxieFile}			file			File being uploaded. MOxieFile extends File/Blob.
			 * @param {Plupload.Uploader}	up 				Current Plupload instance
			 * @param {GPFUPField}			gpfupInstance 	Current File Upload Pro class instance
			 */
			window.gform.doAction('gpfup_before_upload', this.formId, this.fieldId, file, up, this);

			if (isImage(file)) {
				/**
				 * Only run this logic if constraints are set.
				 */
				if (!file.processed) {
					(async () => {
						const blob = file.getNative();

						const imageSize = await getImageSize(blob);

						/**
						 * Orientation check can throw exceptions (e.g. mime-type not supported by exifr. See: HS#26994.)
						 */
						let orientation;
						try {
							orientation = await exifr.orientation(blob);
						} catch ( e ) {
							orientation = 0;
							console.debug( 'Orientation check failed. File type is probably not supported. Message:', e );
						}

						/**
						 * If orientation is 0, 1, or undefined and cropping is disabled, skip running the image
						 * through Blueimp.
						 */
						const requiresRotation = !([0, 1, undefined].includes(orientation));

						/**
						 * Filter whether or not the image loader (Blueimp) should be skipped. This can be advantageous
						 * if large images are frequently uploaded.
						 *
						 * @since 1.0.2
						 *
						 * @param boolean  		skipLoader 		Whether or not to skip sending the image to the loader (Blueimp).
						 * @param int           formId 			The current form ID
						 * @param int           fieldId   		The current uploader field ID
						 * @param {GPFUPField}	gpfupInstance 	Current File Upload Pro class instance
						 */
						const skipLoader = window.gform.applyFilters('gpfup_skip_image_loader', !requiresRotation && !this.enableCrop, this.formId, this.fieldId, this);

						if (skipLoader) {
							file.processed = true;
							this.$store.dispatch('storeOriginal', file);

							up.stop();
							up.start();

							return;
						}

						/**
						 * Minimum is only enforced if cropping is enabled.
						 */
						if (this.enableCrop) {
							let meetsMinimum = true;

							if (
								(this.minWidth && this.minHeight) &&
								(this.minWidth > imageSize.width || this.minHeight > imageSize.height)
							) {
								this.handleFileError(up, file, {
									code: 'does_not_meet_minimum_dimensions',
									message: this.strings.does_not_meet_minimum_dimensions
										.replace('{minWidth}', this.minWidth.toString())
										.replace('{minHeight}', this.minHeight.toString()),
								});

								meetsMinimum = false;
							} else if (this.minWidth && this.minWidth > imageSize.width) {
								this.handleFileError(up, file, {
									code: 'does_not_meet_minimum_width',
									message: this.strings.does_not_meet_minimum_width
										.replace('{minWidth}', this.minWidth.toString()),
								});

								meetsMinimum = false;
							}  else if (this.minHeight && this.minHeight > imageSize.height) {
								this.handleFileError(up, file, {
									code: 'does_not_meet_minimum_height',
									message: this.strings.does_not_meet_minimum_height
										.replace('{minHeight}', this.minHeight.toString()),
								});

								meetsMinimum = false;
							}

							if (!meetsMinimum) {
								/**
								 * When returning out and stopping an upload in some situations, the uploader can get into
								 * an odd state and not process subsequent uploads.
								 *
								 * This resolves that.
								 */
								up.stop();
								up.start();

								return;
							}
						}

						if (!file.cropped) {
							await this.$store.dispatch('storeOriginal', file);
						}

						let imageLoaderOptions: loadImage.LoadImageOptions = {
							maxWidth: undefined,
							maxHeight: undefined,
							minWidth: undefined,
							minHeight: undefined,
							aspectRatio: undefined,
							crop: undefined,
						};

						if (this.enableCrop) {
							imageLoaderOptions = {
								maxWidth: (this.exactWidth || this.maxWidth) ?? undefined,
								maxHeight: (this.exactHeight || this.maxHeight) ?? undefined,
								minWidth: this.exactWidth ?? this.minWidth ?? undefined,
								minHeight: this.exactHeight ?? this.minHeight ?? undefined,
								/*
								 * If aspectRatio is used, it takes precedence over max widht/height
								 */
								aspectRatio: (!this.exactWidth && !this.exactHeight ? this.aspectRatio : undefined) ?? undefined,
								crop: (this.enableCrop && !file.cropped) ? true : undefined,
							};
						}

						const processedImage = await loadWithBlueimp({
							image: file,
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
							stripMetadata: window.gform.applyFilters('gpfup_strip_image_metadata', true, this.formId, this.fieldId),
							/**
							 * Filter the options that are sent to
							 * [blueimp-load-image](https://www.npmjs.com/package/blueimp-load-image).
							 *
							 * @since 1.0-beta-2.0
							 *
							 * @param object 			options     	Options to send to blueimp-load-image
							 * @param int           	formId 			The current form ID
							 * @param int             	fieldId   		The current uploader field ID
							 * @param {GPFUPField}		gpfupInstance 	Current File Upload Pro class instance
							 */
							loadImageOptions: window.gform.applyFilters('gpfup_image_loader_options', imageLoaderOptions, this.formId, this.fieldId, this),
							/**
							 * Filter the quality used for JPEGs when auto-cropping and manual cropping.
							 *
							 * @since 1.0.4
							 *
							 * @param float 			jpegQuality     0-1 representation of JPEG quality. Defaults to 0.92.
							 * @param int           	formId 			The current form ID
							 * @param int             	fieldId   		The current uploader field ID
							 * @param {GPFUPField}		gpfupInstance 	Current File Upload Pro class instance
							 */
							jpegQuality: window.gform.applyFilters('gpfup_jpeg_quality', 0.92, this.formId, this.fieldId, this),
						});

						const image = replaceFile({
							up,
							$store: this.$store,
							fieldId: this.fieldId,
							formId: this.formId,
							newFile: processedImage,
							existingFile: file,
						});

						image.processed = true;

						triggerUpload(up, image);
					})();

					return false;
				}

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
					clearPrevious: this.$store.getters.currentAddedFiles <= 1,
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
			if (this.cropRequired) {
				if (!(up as any).replacingFile) {
					this.$store.commit('SET_CURRENT_ADDED_FILES', files);
				}
			}

			for ( const file of files ) {
				this.$store.commit('ADD_FILE', file);
			}

			if ('replacingFile' in up) {
				delete (up as any).replacingFile;
			}
		});

		this.Uploader.bind('FileUploaded', (up: Plupload.Uploader, file: any, result: any) => {
			var response = JSON.parse(result.response);

			if(response.status == 'error'){
				this.handleFileError(up, file, response.error);
			}

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

		/**
		 * Re-sort the Gravity Forms hidden input any time the file order changes.
		 */
		this.$store.subscribe((mutation: { payload: any, type: string }, state: any) => {
			if (mutation?.type !== 'SET_FILE_ORDER') {
				return;
			}

			sortHidddenGFInput(this.formId, this.fieldId, state.fileOrder, true);
		});
	}
}
