export default function  getImageSize(sourceUrl: string) : Promise<{ width: number, height: number }>{
	const img = new Image;

	return new Promise((resolve) => {
		img.onload = function() {
			resolve({
				width: img.width,
				height: img.height,
			})
		};

		img.src = sourceUrl;
	});
}
