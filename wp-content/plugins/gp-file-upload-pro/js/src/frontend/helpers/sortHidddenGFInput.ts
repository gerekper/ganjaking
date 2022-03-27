/**
 * When files get added to Gravity Form's multi file hidden input, they are unshifted (added to the beginning) rather than
 * the end.
 *
 * This function syncs the Gravity Forms hidden input with the file order in the Vuex state to maintain consistent
 * ordering and put GPFUP in control of ordering. This will also enable sorting down the road.
 */
export default function(formId: string, fieldId: string, fileOrder: string[], orderChanged: boolean = false) {
	const $ = window.jQuery;
	const $uploadedFiles = $('#gform_uploaded_files_' + formId);
	const filesJson = $uploadedFiles.val();

	if(filesJson){
		// @ts-ignore
		var files = $.secureEvalJSON(filesJson);

		if(files) {
			var inputName = 'input_' + fieldId;

			files[inputName] = files[inputName].sort((a: any, b: any) => {
				/*
				 * The Gravity Forms multifile hidden input has a structure that looks something like the following:
				 *
				 * {
				 * 	"input_2": [
				 * 		{"temp_filename":"6f1d253e_input_2_o_1eo03uu85o25pt71qke1osn1n63m.jpg","uploaded_filename":"a.jpg"},
				 * 		{"temp_filename":"6f1d253e_input_2_o_1eo03uu85o5u1d1n1cikj1dp56n.jpg","uploaded_filename":"b.jpg"},
				 *   ]
				 * }
				 *
				 * The pattern below plucks the file ID out of temp_filename.
				 */
				const idPattern = /o_[a-z0-9]+(?=\.)/;
				let aId = a.temp_filename?.match(idPattern)?.[0];
				let bId = b.temp_filename?.match(idPattern)?.[0];

				/***
				 * In the context of editing entries or with rehydration from GFUR, we won't have the temp_filename.
				 * However, in these cases we will typically have uploaded_filename which we can sort by.
				 *
				 * Another note: GF_User_Registration::is_new_file_upload requires that new files are prepended to the
				 * files list instead of appended.
				 */
				if (!a.temp_filename) {
					aId = a.uploaded_filename;
				}

				if (!b.temp_filename) {
					bId = b.uploaded_filename;
				}

				return fileOrder.indexOf(aId) - fileOrder.indexOf(bId);
			});

			/**
			 * Set key on files if a sort happened. This helps signal to GFUR that the submitted value should be
			 * used rather than the existing meta values.
			 */
			if (orderChanged) {
				files[inputName] = files[inputName].map((file: { [key: string]: string | boolean }) => {
					file.gpfup_order_changed = true;
					return file;
				});
			}

			// @ts-ignore
			$uploadedFiles.val($.toJSON(files));
		}
	}
}
