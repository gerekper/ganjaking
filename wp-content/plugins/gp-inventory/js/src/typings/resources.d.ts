interface Resource {
	id: number
	name: string
	inventory_limit: number
	choice_based: boolean
	properties: ResourceProperty[]
}

interface ResourceProperty {
	id: string
	name: string
}
