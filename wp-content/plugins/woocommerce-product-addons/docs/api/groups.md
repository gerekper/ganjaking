# Product Add-ons REST API

## Global Group and Product Endpoints

* Endpoints are provided to create, read, update and delete add-on groups, fields and add-ons.
* A global add-on group or product contains zero or more add-on fields.
* Add-on fields contain options of a particular type (e.g. checkboxes).
* An add-on field contains zero or more options.
* Merchants can set up one or more global add-on groups and can also set up add-ons directly on individual products
* Global add-on groups can optionally be limited to certain product category IDs
* Products also inherit add-ons from their parent product, if any.
* Each global add-on group is a custom post and has a unique integer ID.

### Create a New Global Add-on Group

`POST /wp-json/wc-product-add-ons/v1/product-add-ons`

**Capability Required**

* `manage_woocommerce` or `manage_options`

**Request Body**

- name: (string, optional) the global group name
- priority: (integer, optional) the priority of the group
- restrict_to_category_ids: (array of integers, optional) the product categories this group applies to or an empty array if it applies to all products

```
{
	"name": "Personalization Options",
	"priority": 9,
	"restrict_to_category_ids": [
		11,
		12,
		13,
		14
	],
	"fields": [
	]
}
```

**Success Response (200)**

On success, the complete newly created group object is returned. The returned add-ons array will always be empty.

```
{
	"id": 10,
	"name": 'Personalization Options',
	"priority": 9,
	"restrict_to_category_ids": [
		11,
		12,
		13
	],
	"fields": []
}
```


### Get a single Global Add-on Group or Product Add-ons

`GET /wp-json/wc-product-add-ons/v1/product-add-ons/$group_or_product_ID`

**Capability Required**

* `manage_woocommerce` or `manage_options`

**Request Body**

```
(none)
```

**Success Response (200)**

- `id`: the global group ID OR product ID
- `name`: (string)
    - the global group name
    - always empty for product add-ons
- `priority`: (integer)
    - for global groups, the priority of the group
    - always 10 for product add-ons
- `restrict_to_category_ids`: (array of integers)
    - for global groups, these are the product categories this group applies to or an empty array if it applies to all products
    - always an empty array for product add-ons
- `fields`: (array of field items) the fields containing the add-ons and their options in the group or product

```
{
	"id": 10,
	"name": 'Personalization Options',
	"priority": 10,
	"restrict_to_category_ids": [
		11,
		12,
		13
	],
	"fields": [
		{
			"name": "Addon name",
			"title_format": "label",
			"description_enable": 1,
			"description": "Addon description",
			"type": "custom_text",
			"display": "select",
			"position": 0,
			"required": 1,
			"restrictions": 0,
			"restrictions_type": "any_text",
			"adjust_price": 1,
			"price_type": "flat_fee",
			"price": "10",
			"min": 0,
			"max": 0,
			"options": []
		}
	]
}
```

#### Get all the Global Add-on Groups

`GET /wp-json/wc-product-add-ons/v1/product-add-ons`

**Capability Required**

* `manage_woocommerce` or `manage_options`

**Request Body**

```
(none)
```

**Success Response (200)**

```
{
	[
		{
			"id": 10,
			"name": "Personalization Options",
			"priority": 10,
			"restrict_to_category_ids": [
				11,
				12,
				13
			],
			"fields": [
			]
		},
		{
			"id": 14,
			"name": "Moar Options",
			"priority": 15,
			"restrict_to_category_ids": [
				11,
				12,
				13
			],
			"fields": [
			]
		}
	]
}
```


### Update a Global Add-on Group or Product Add-ons

`PATCH /wp-json/wc-product-add-ons/v1/product-add-ons/$group_or_product_ID`

**Capability Required**

* `manage_woocommerce` or `manage_options`

**Request Body**

- name: (string, optional) the global group name; always empty for product add-ons
- priority: (integer, optional) for global groups, the priority of the group; always 10 for product add-ons
- restrict_to_category_ids: (array of integers, optional) for global groups, the product categories this group applies to or an empty array if it applies to all products; also an empty array for products 
- fields: (array of field items) the fields containing the add-ons and their options in the group or product

```
{
	"name": "Personalization Options",
	"priority": 9,
	"restrict_to_category_ids": [
		11,
		12,
		13,
		14
	],
	"fields": [
	]
}
```

**Success Response (200)**

On success, the entire group object is returned including any changes.

```
{
	"id": 10,
	"name": 'Personalization Options',
	"priority": 9,
	"restrict_to_category_ids": [
		11,
		12,
		13
	],
	"fields": [
	]
}
```


### Delete a Global Add-on Group

`DELETE /wp-json/wc-product-add-ons/v1/product-add-ons/$group_ID`

**Capability Required**

* `manage_woocommerce` or `manage_options`

**Request Body**

NOTE: Only works for global add-on groups.

```
(none)
```

**Success Response (200)**

```
(empty)
```

## Global Group and Product Fields

* The `fields` argument in creation and update requests must contain an array of field items (or an empty array). Each field item contains the following:

```
- type: (string, required) one of the following
    - `multiple_choice` : Multiple choice
    - `checkbox` : Checkboxes 
    - `custom_text` : Custom input (text) - Any Text
    - `custom_textarea` : Custom input (textarea)
    - `file_upload` : File upload
	- `custom_price`: Custom price
    - `input_multiplier` : Additional price multiplier
	- `Heading` : Heading
- display: (string, required, relevant for multiple_choice type ) one of the following
    - `select` : Dropdowns
    - `radiobutton` : Radio buttons 
    - `images` : Images
- name: (string, required) the name to display on the front-end for this add-on
- title_format: (string, required) one of the following
    - `label` : Default display 
    - `Heading` : Heading
    - `hide` : Hide addon name
- description_enabled: (boolean, required) whether or not the description is displayed
- description: (string, required - can be empty) the description, if any, to display on the front-end; defaults to empty string
- required: (boolean, required) whether or not the customer must choose/complete at least one option from the add-on; defaults to false
- position: (integer, required - can be 0) display position of the addon in the group
- restrictions: (boolean, required, relevant for custom_text) whether or not input text is restricted,
- restrictions_type": (string, required) restrictions on input text. One of the following
    - `any_text` 
    - `only_letters`
    - `only_numbers`
    - `only_letters_numbers`
    - `email`
- adjust_price: (boolean, required) whether or not price is adjusted
- price_type: (string, required) one of the following
    - `flat_fee` 
    - `quantity_based`
    - `percentage_based`
- price: (string with numeric value, required - can be empty, not relevant for heading, checkbox and multiple_choice) Addon price
- min: (integer, relevant for custom_text, custom_textarea, custom_price and input_multiplier) Minimun range on customer input
- max: (integer, relevant for custom_text, custom_textarea, custom_price and input_multiplier) Maximum range on customer input
- options: (array, required)
```

For example:

```
"fields": [
	{
		"name": "Add a text to engrave",
		"title_format": "label",
		"description_enable": 1,
		"description": "Text will be engraved on the back",
		"type": "custom_text",
		"display": "select",
		"position": 0,
		"required": 1,
		"restrictions": 0,
		"restrictions_type": "any_text",
		"adjust_price": 1,
		"price_type": "flat_fee",
		"price": "10",
		"min": 0,
		"max": 0,
		"options": []
	}
]
```

## Global Group and Product Options

* The `options` with the `fields` argument in creation and update requests must contain an array of options (or an empty array).
* The structure of an option varies based on the `fields` type.

### Checkboxes (checkbox type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option is selected
- price_type: (string, required) one of the following
    - `flat_fee` 
    - `quantity_based`
    - `percentage_based`


**Sample Option**
```
{
    "label": "option1",
    "price": "5",
    "price_type": "flat_fee"
}
```

### Images (multiple_choice type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option is selected
- `image` (image_id, optional)
- price_type: (string, required) one of the following
    - `flat_fee` 
    - `quantity_based`
    - `percentage_based`


**Sample Options**
```
{
    "label": "option image 1",
    "price": "5",
	"image": 2040,
    "price_type": "flat_fee"
}
{
    "label": "option image 2",
    "price": "10",
	"image": 2033,
    "price_type": "flat_fee"
}
```
