import type Plupload from "plupload";

export default function triggerUpload(up: Plupload.Uploader, file: MOxieFile) : void {
	file.status = plupload.UPLOADING;
	up.trigger('UploadFile', file);

	/*
	 * Help Plupload not get stuck in a funky state. Without this, crops after the first will
	 * not upload
	 */
	up.stop();
	up.start();
}
