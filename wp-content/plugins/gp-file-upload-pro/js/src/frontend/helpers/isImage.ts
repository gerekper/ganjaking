export default function isImage(file: File) : boolean {
	const unsupportedImageTypes = [
		'image/tiff',
		'image/psd',
		'image/vnd.adobe.photoshop',
	];

	return file.type.indexOf('image/') === 0 && !unsupportedImageTypes.includes(file.type);
}
