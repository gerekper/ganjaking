export default function isImage(file: File) : boolean {
	return file.type.indexOf('image/') === 0;
}
