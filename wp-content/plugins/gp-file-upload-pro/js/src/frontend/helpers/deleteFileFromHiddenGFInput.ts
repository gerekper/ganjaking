/**
 * This is a fork of Gravity Form's gformDeleteUploadedFile to enable us to pass the file directly and not worry about
 * indexes.
 */
export default function removeFileFromGFUploadedMeta(formId: string, fieldId: string, file: MOxieFile) {
	const $ = window.jQuery;

	var filesJson = $('#gform_uploaded_files_' + formId).val();

	if(filesJson){
		// @ts-ignore
		var files = $.secureEvalJSON(filesJson);
		if(files) {
			var inputName = "input_" + fieldId;
			var $multfile = $("#gform_multifile_upload_" + formId + "_" + fieldId );

			if( $multfile.length > 0 && files?.[inputName] ) {
				for ( const [index, hiddenFileMeta] of Object.entries(files[inputName]) ) {
					if (
						hiddenFileMeta?.temp_filename?.indexOf(file.id) > 0
						|| hiddenFileMeta?.uploaded_filename === file.id
					) {
						delete files[inputName][index];
					}
				}

				files[inputName] = files[inputName].filter(Boolean);

				var settings = $multfile.data('settings');
				var max = settings.gf_vars.max_files;
				$("#" + settings.gf_vars.message_id).html('');
				if(files[inputName].length < max)
					window.gfMultiFileUploader.toggleDisabled(settings, false);

			} else {
				files[inputName] = [];
			}

			// @ts-ignore
			$('#gform_uploaded_files_' + formId).val($.toJSON(files));
		}
	}
}
