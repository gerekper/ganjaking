interface MOxieFile extends File {
	id: string
	getNative: () => File
	addedDate: Date
	percent: number
	status: number
	size: number
	cropped?: boolean
}
