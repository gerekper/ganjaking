import loadImage from 'blueimp-load-image';

export default async function loadWithBlueimp({image, jpegQuality, loadImageOptions, stripMetadata}: {
	image: MOxieFile,
	loadImageOptions: loadImage.LoadImageOptions,
	jpegQuality: number,
	stripMetadata: boolean,
}): Promise<MOxieFile> {
	const img = await loadImage(image.getNative(), {
		canvas: true,
		orientation: true,
		meta: true,
		...loadImageOptions,
	});

	let blobImageType = 'image/png';

	if (['image/jpg', 'image/jpeg'].includes(image?.type)) {
		blobImageType = 'image/jpeg';
	}

	let processedBlob = await new Promise<Blob | null>((resolve) => {
		(img.image as unknown as HTMLCanvasElement).toBlob(function (blob) {
			resolve(blob);
		}, blobImageType, jpegQuality);
	});

	if (!stripMetadata) {
		processedBlob = await loadImage.replaceHead(processedBlob, img.imageHead);
	}

	/* Create new file object for Plupload using blob and update file name */
	const newFile = new window.mOxie.File(null, processedBlob);
	newFile.name = image.name;

	return newFile;
}
