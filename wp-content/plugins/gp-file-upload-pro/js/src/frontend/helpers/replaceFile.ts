import deleteFileFromHiddenGFInput from "./deleteFileFromHiddenGFInput";
import type {Store} from "vuex";

const $ = window.jQuery;

// @ts-ignore
export default function replaceFile({ up, $store, formId, fieldId, existingFile, newFile } : { up: any, $store: Store<any>, formId: string, fieldId: string, existingFile: MOxieFile, newFile: MOxieFile }) : MOxieFile {
	up.replacingFile = true; // Set flag to prevent certain portions of FilesAdded from firing,
							// this flag is removed in the callback of FilesAdded as addFile is async.

	up.addFile(newFile);

	/* Remove original file from GF */
	const currentFileEl = $(`[data-file-id="${existingFile?.id}"]`);

	if (currentFileEl.length) {
		deleteFileFromHiddenGFInput(formId, fieldId, existingFile);
	}

	newFile = up.files[up.files.length - 1];
	newFile.cropped = existingFile?.cropped;
	newFile.addedDate = new Date(existingFile?.addedDate?.getTime() ?? Date.now());

	$store.commit('REPLACE_FILE', {
		replacedFile: existingFile,
		newFile,
	});

	/* Remove original file. Ordering is important here as we need to do it after REPLACE_FILE */
	up.removeFile(existingFile?.id);

	/* Transfer original image and coords */
	$store.dispatch('transferCoords', {
		currentFileId: existingFile?.id,
		newFileId: newFile.id,
	})

	$store.dispatch('transferOriginal', {
		currentFileId: existingFile?.id,
		newFileId: newFile.id,
	})

	/* Remove original file from Gravity Forms UI */
	const fileIndexUI = $(`#${existingFile?.id}`).index();
	const $field = $("#field_" + formId + "_" + fieldId);

	$field.find(".ginput_preview").eq(fileIndexUI).remove();

	return newFile;
}
