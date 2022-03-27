export default function  getImageSize(source: string | Blob) : Promise<{ width: number, height: number }>{
	const img = new Image;

	return new Promise((resolve) => {
		img.onload = function() {
			resolve({
				width: img.width,
				height: img.height,
			})
		};

		if (typeof source !== 'string')  {
			img.src = URL.createObjectURL(source);
		} else {
			img.src = source;
		}
	});
}
