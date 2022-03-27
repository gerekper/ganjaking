import localforage from 'localforage';

interface FileInfo {
	size?: number
	type?: string
	addedDate?: Date
}

interface Dimensions {
	width: number
	height: number
}

const $ = window.jQuery;

/**
 * @classdesc Stores various bits of data including image previews and originals to client-side storage (via localforage)
 *   so the uploader can function on subsequent page loads such as failed validation.
 */
export default class Storage {

	constructor(private formId: string, private fieldId: string) {};

	get prefix() : string {
		const { formId, fieldId } = this;

		return `gpfup-${formId}-${fieldId}`;
	}

	getFileInfo(fileId: string) : Promise<FileInfo | null> {
		return localforage.getItem(`${this.prefix}-${fileId}-info`);
	}

	getPreview(fileId: string) : Promise<string | null> {
		return localforage.getItem(`${this.prefix}-${fileId}-preview`);
	}

	getPreviewDimensions(fileId: string) : Promise<Dimensions | null> {
		return localforage.getItem(`${this.prefix}-${fileId}-preview-dimensions`);
	}

	getOriginal(fileId: string) : Promise<{ src: File, size: Dimensions } | null> {
		return localforage.getItem(`${this.prefix}-${fileId}-original`);
	}

	getCoords(fileId: string) : Promise<Coords | null> {
		return localforage.getItem(`${this.prefix}-${fileId}-coords`);
	}

	async storeFileInfo(files: MOxieFile[]) : Promise<void> {
		for (const file of files) {
			const key = `${this.prefix}-${file.id}-info`;
			const fileInfo: FileInfo = await localforage.getItem(key) ?? {};

			if (file.size) {
				fileInfo.size = file.size;
			}

			if (file.type) {
				fileInfo.type = file.type;
			}

			if (file.addedDate) {
				fileInfo.addedDate = file.addedDate;
			}

			localforage.setItem(key, fileInfo);
		}
	}

	storeOriginal(fileId: string, file: File) : void {
		localforage.setItem(`${this.prefix}-${fileId}-original`, file);
	}

	async transferOriginal(newFileId: string, currentFileId: string) : Promise<void> {
		const existingKey = `${this.prefix}-${currentFileId}-original`;
		const existingOriginal = await localforage.getItem(existingKey);

		await localforage.setItem(`${this.prefix}-${newFileId}-original`, existingOriginal);
		await localforage.removeItem(existingKey);
	}

	storePreview(fileId: string, dataUrl: string) : void {
		localforage.setItem(`${this.prefix}-${fileId}-preview`, dataUrl);
	}

	storePreviewDimensions(fileId: string, dimensions : Dimensions) : void {
		localforage.setItem(`${this.prefix}-${fileId}-preview-dimensions`, dimensions);
	}

	storeCoords(fileId: string, coords: Coords) : void {
		localforage.setItem(`${this.prefix}-${fileId}-coords`, coords);
	}

	async transferCoords(newFileId: string, currentFileId: string) : Promise<void> {
		const existingKey = `${this.prefix}-${currentFileId}-coords`;
		const existingCoords = await localforage.getItem(existingKey);

		if (!existingCoords) {
			return;
		}

		await localforage.setItem(`${this.prefix}-${newFileId}-coords`, existingCoords);
		await localforage.removeItem(existingKey);
	}

	purgeFile(file: MOxieFile) : Promise<void> {
		return localforage.iterate((value, key) => {
			const fileIdPrefix = `${this.prefix}-${file.id}-`;
			const fileNamePrefix = `${this.prefix}-${file.name}-`;

			if (key.indexOf(fileIdPrefix) === 0 || key.indexOf(fileNamePrefix) === 0) {
				localforage.removeItem(key);
			}
		});
	}

	/**
	 * Purge localforage/IndexedDB for the current field
	 */
	purge() : Promise<void> {
		return localforage.iterate((value, key) => {
			if (key.indexOf(`${this.prefix}-`) === 0) {
				localforage.removeItem(key);
			}
		});
	}
}
