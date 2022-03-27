import Vue from 'vue';
import Vuex from 'vuex';
import editor from './modules/editor';
import { blobToDataURL } from 'blob-util';
import type Storage from '../classes/Storage';
import delay from 'delay';

Vue.use(Vuex);

export default function GPFUPStoreFactory({ storage } : { storage: Storage }) : any {
	return new Vuex.Store({
		modules: {
			editor: editor(),
		},
		state: {
			/**
			* Instance of Storage to be accessible by modules
			*/
			storage,

			/**
			* Files synced from Plupload
			*/
			files: [],

			/**
			 * Keep track of removed files to filter them out of other arrays such as fileOrder, currentAddedFiles, etc.
			 *
			 * The primary reason why we need this is due to how Gravity Forms limits the number of files. It uses
			 * Plupload's remove file method which fires before the FilesAdded event.
			 */
			removedFiles: [],

			/**
			* File order to handle re-ordering the Plupload files and Gravity Forms hidden input after cropping and
			* to prepare for sortables.
			*/
			fileOrder: [],

			/**
			* Files that are added in a batch. Needed to cancel the entire batch at once.
			*/
			currentAddedFiles: [],

			/**
			* Erred files not in Plupload to provide a way to show upload errors
			*/
			erredFiles: [],

			/**
			* Image previews for file list
			*/
			imgPreviews: {},
		} as {
			storage: Storage,
			removedFiles: MOxieFile['id'][],
			files: MOxieFile[],
			fileOrder: string[],
			currentAddedFiles: MOxieFile[],
			erredFiles: MOxieFile[],
			imgPreviews: { [key: string]: any },
		},
		actions: {
			async storeImagePreview (context, { fileId, blob, dataUrl } : { fileId: string, blob?: Blob, dataUrl?: string }) {
				let base64: string = dataUrl!;

				if (!dataUrl) {
					base64 = await blobToDataURL(blob!);
				}

				if (!base64) {
					throw new Error('Unable to save image preview.');
				}

				context.commit('ADD_IMAGE_PREVIEW', Object.freeze({
					fileId,
					base64,
				}));

				storage.storePreview(fileId, base64);
			},
			async setFiles (context, files) {
				context.commit('SET_FILES', files);

				storage.storeFileInfo(files);
			},
			async setCurrentFileAndOpenEditor (
				context,
				{ file, clearPrevious = true, delayMs } : { file: MOxieFile, clearPrevious?: boolean, delayMs?: number },
			) {
				await context.dispatch('setCurrentFile', { file, clearPrevious });

				if (delayMs) {
					await delay(delayMs);
				}

				context.commit('OPEN_EDITOR');
			},
			closeEditor (context) {
				context.commit('CLOSE_EDITOR');
				context.commit('CLEAR_CURRENT_ADDED_FILES');
			},
		},
		getters: {
			currentAddedFiles: (state) => {
				return state.currentAddedFiles.filter((file: MOxieFile) => {
					return  !state.removedFiles.includes(file.id)
						&& !state.erredFiles.find((erredFile) => file.id === erredFile.id)
				})
			},
			currentAddedErredFiles: (state) => {
				return state.currentAddedFiles.filter((file: MOxieFile) => {
					return state.erredFiles.find((erredFile) => file.id === erredFile.id)
				})
			},
			allFiles: (state) => {
				return [
					...state.files,
					...state.erredFiles,
				].sort((a: any, b: any) => {
					return state.fileOrder.indexOf(a.id) - state.fileOrder.indexOf(b.id);
				});
			}
		},
		mutations: {
			SET_FILES (state, files) {
				state.files = files;
			},
			ADD_FILE (state, file) {
				const existingIndex = state.fileOrder.indexOf(file.id);

				if (existingIndex !== -1) {
					return;
				}

				state.fileOrder.push(file.id);
			},
			SET_CURRENT_ADDED_FILES (state, files) {
				state.currentAddedFiles = files;
			},
			CLEAR_CURRENT_ADDED_FILES (state) {
				state.currentAddedFiles = [];
			},
			REMOVE_FILE (state, file) {
				const index = state.fileOrder.indexOf(file.id);

				state.removedFiles.push(file.id);

				if (index === -1) {
					return;
				}

				state.fileOrder.splice(index, 1);
			},
			REPLACE_FILE (state, { replacedFile, newFile }) {
				const fileOrderIndex = state.fileOrder.indexOf(replacedFile.id);

				if (fileOrderIndex !== -1) {
					state.fileOrder[fileOrderIndex] = newFile.id;
				}

				const currentAddedFilesIndex = state.currentAddedFiles.findIndex((file) => {
					return file.id === replacedFile.id;
				});

				if (currentAddedFilesIndex !== -1) {
					state.currentAddedFiles[currentAddedFilesIndex] = newFile;
				}
			},
			PUSH_ERRED_FILE (state, { file, error }) {
				state.erredFiles.push({
					...file,
					error,
				});
			},
			REMOVE_ERRED_FILE (state, file) {
				state.erredFiles.splice(state.erredFiles.indexOf(file.id), 1);
			},
			ADD_IMAGE_PREVIEW (state, { fileId, base64 } : { fileId: string, base64: string }) {
				Vue.set(state.imgPreviews, fileId, base64);
			},
			SET_FILE_ORDER (state, order) {
				state.fileOrder = order;
			},
		},
	});
}
