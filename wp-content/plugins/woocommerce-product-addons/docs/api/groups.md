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
			"name": "Custom Text",
			"description": "Custom Text Description",
			"type": "custom",
			"position": 0,
			"required": 1,
			"options": [
				{
				"label": "Custom Text 1",
				"price": 9.95
				}
			]
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
    - `checkbox` : Checkboxes 
    - `custom` : Custom input (text) - Any Text
    - `custom_textarea` : Custom input (textarea)
    - `custom_price` : Additional custom price input
    - `custom_letters_only` : Custom input (text) - Only Letters
    - `custom_digits_only` : Custom input (text) - Only Numbers
    - `custom_letters_or_digits` Custom input (text) - Only Letters and Numbers
    - `custom_email` : Custom input (text) - Email Address
    - `file_upload` : File upload
    - `input_multiplier` : Additional price multiplier
    - `radiobutton` : Radio buttons
    - `select` : Select box
- name: (string, required) the name to display on the front-end for this add-on
- description: (string, required - can be empty) the description, if any, to display on the front-end; defaults to empty string
- required: (boolean, required) whether or not the customer must choose/complete at least one option from the add-on; defaults to false
- options: (array, required)
```

For example:

```
[
	{
		"type": "checkbox",
		"name": "Special Engraving Font',
		"description": "Upgrade from the standard font (Arial) to a special one.",
		"required": false,
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

**Sample Option**
```
{
	label: 'Comic Sans Font',
	price: 1.00,
}
```

### Custom Input - Text (custom type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has text entered
- `min` (integer, optional)
    - set to minimum number of characters required
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum number of characters allowed
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Enter a custom message to be printed on your shirt, up to 40 characters',
	price: 10.00,
	min: 0,
	max: 40
}
```

### Custom Input - Text area (custom_textarea type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has text entered
- `min` (integer, optional)
    - set to minimum number of characters required
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum number of characters allowed
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Enter a custom message to be printed on your shirt, up to 40 characters',
	price: 10.00,
	min: 0,
	max: 40
}
```

### Additional Custom Price Input (custom_price type)

- `label` (string, optional)
- `min` (price, optional)
    - set to minimum price customer can input (e.g. 1.00), or
    - set to '' to have no minimum
- `max` (price, optional)
    - set to maximum price customer can input (e.g. 10.00), or
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Add $1 for each additional blank card you'd like up to ten cards',
	min: 0,
	max: 10
}
```

#### Custom Input - Only Letters (custom_letters_only type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has text entered
- `min` (integer, optional)
    - set to minimum number of characters required
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum number of characters allowed
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Enter a custom message to be printed on your shirt, up to 40 characters',
	price: 10.00,
	min: 0,
	max: 40
}
```

### Custom Input - Only Numbers (custom_digits_only type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has text entered
- `min` (integer, optional)
    - set to minimum number of characters required
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum number of characters allowed
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Enter a zip code for special monitoring for $10 a month',
	price: 10.00,
	min: 6,
	max: 6
}
```

### Custom Input - Only Numbers and Letters (custom_letters_or_digits type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has text entered
- `min` (integer, optional)
    - set to minimum number of characters required
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum number of characters allowed
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Enter a custom message to be printed on your shirt, up to 40 characters',
	price: 10.00,
	min: 0,
	max: 40
}
```

### Custom Input - Email Address (custom_email type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option has an email address entered

**Sample Option**
```
{
	label: 'Enter an email address to get notifications automatically for just $10',
	price: 10.00,
}
```

### File Upload (file_upload type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when a file is attached

**Sample Option**
```
{
	label: 'Add a custom photo to your coffee mug for just $5',
	price: 5.00
}
```

### Additional Price Multiplier (input_multiplier type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when the option is selected
- `min` (integer, optional)
    - set to minimum multiplier the customer can input (e.g. 1), or
    - set to '' to have no minimum
- `max` (integer, optional)
    - set to maximum multiplier the customer can input (e.g. 10), or
    - set to '' to have no maximum

**Sample Option**
```
{
	label: 'Add up to ten additional cards at $1 each',
	price: 1.00,
	min: 0,
	max: 10
}
```

### Radio Buttons (radiobutton type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when that radio button is selected

NOTE: Usually you will have two or more radiobutton options in an addon

**Sample Option**
```
{
	label: 'Comic Sans',
	price: 5.00
}
```

### Select Box (select type)

- `label` (string, optional)
- `price` (price, optional)
    - the price to charge when that option is selected

NOTE: Usually you will have two or more selectbox options in an addon

**Sample Option**
```
{
	label: 'Comic Sans',
	price: 5.00
}
```
