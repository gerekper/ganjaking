interface MOxieFile extends File {
	id: string
	getNative: () => File
	addedDate: Date
	percent: number
	status: number
	size: number
	cropped?: boolean
	processed?: boolean; /* If the image has been loaded with Blueimp if rotation/cropping required or checked and Blueimp not required. */
	rehydrated?: boolean; /* Shortcut around any logic requiring cropped or processed */
}
