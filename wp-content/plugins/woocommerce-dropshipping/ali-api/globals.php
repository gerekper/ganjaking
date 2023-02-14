<?php
$inputParams = array(
	'title' => 'My Product 03',
	'type' => 'simple',
	'sku' => 'SSSSST45V',
	'price' => '51.00',
	'regular_price' => '46.00',
	'sale_price' => '41.00',
	'description' => 'Trying it out for realqqqqqqq',
	'short_description' => 'Pellentesque habitanqqqqq',
	'manage_stock' => true,
	'stock_quantity' => '45',
	'weight' => '1.30',
	'dimensions' => array(
		'length' => '12.00',
		'width' => '8.00',
		'height' => '3.00',

	),
	'tags' => array(
		array(
			'id' => '18',
		),
		array(
			'id' => '19',
		),
	),
	'categories' => array(
		array(
			'id' => '16',
		),
		array(
			'id' => '17',
		),
	),
	'images' => array(
		array(
			'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_4_front.jpg',
			'position' => 0,
		),
		array(
			'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg',
			'position' => 0,
		),
		array(
			'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg',
			'position' => 0,
		),
		array(
			'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg',
			'position' => 0,
		),

	),
	'attributes' => array(
		array(
			'name' => 'Color',
			'position' => 0,
			'visible' => true,
			'variation' => true,
			'options' => array(
				'Black',
				'Green',
			),
		),
		array(
			'name' => 'Size1',
			'position' => 0,
			'visible' => true,
			'variation' => true,
			'options' => array(
				'S',
				'M',
			),
		),
		array(
			'name' => 'Size2',
			'position' => 0,
			'visible' => true,
			'variation' => true,
			'options' => array(
				'S',
				'M',
			),
		),
		array(
			'name' => 'Size3',
			'position' => 0,
			'visible' => true,
			'variation' => true,
			'options' => array(
				'S',
				'M',
			),
		),
	),
	'default_attributes' => array(
		array(
			'name' => 'Color',
			'option' => 'Black',
		),
		array(
			'name' => 'Size1',
			'option' => 'S',
		),
	),
	'variations' => array(
		array(
			'regular_price' => '29.98',
			'attributes' => array(
				array(
					'name' => 'Color',
					'options' => 'Black',
				),
			),
		),
		array(
			'regular_price' => '29.98',
			'attributes' => array(
				array(
					'name' => 'color',
					'options' => 'Green',
				),
			),
		),
	),
);

/*
Sample Input JSON received from CBE (this includes all product details scraped from AliExpress site):

{
  "title": "My Product 03",
  "type": "simple",
  "sku": "SSSSST45V",
  "price": "51.00",
  "regular_price": "46.00",
  "sale_price": "41.00",
  "description": "Trying it out for realqqqqqqq",
  "short_description": "Pellentesque habitanqqqqq",
  "manage_stock": true,
  "stock_quantity": "45",
  "weight": "1.30",
  "dimensions": {
	"length": "12.00",
	"width": "8.00",
	"height": "3.00"
  },
  "tags": [
	{
	  "id": "18"
	},
	{
	  "id": "19"
	}
  ],
  "categories": [
	{
	  "id": "16"
	},
	{
	  "id": "17"
	}
  ],
  "images": [
	{
	  "src": "http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_4_front.jpg",
	  "position": 0
	},
	{
	  "src": "http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg",
	  "position": 0
	},
	{
	  "src": "http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg",
	  "position": 0
	},
	{
	  "src": "http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_3_front.jpg",
	  "position": 0
	}
  ],
  "attributes": [
	{
	  "name": "Color",
	  "position": 0,
	  "visible": true,
	  "variation": true,
	  "options": [
		"Black",
		"Green"
	  ]
	},
	{
	  "name": "Size1",
	  "position": 0,
	  "visible": true,
	  "variation": true,
	  "options": [
		"S",
		"M"
	  ]
	},
	{
	  "name": "Size2",
	  "position": 0,
	  "visible": true,
	  "variation": true,
	  "options": [
		"S",
		"M"
	  ]
	},
	{
	  "name": "Size3",
	  "position": 0,
	  "visible": true,
	  "variation": true,
	  "options": [
		"S",
		"M"
	  ]
	}
  ],
  "default_attributes": [
	{
	  "name": "Color",
	  "option": "Black"
	},
	{
	  "name": "Size1",
	  "option": "S"
	}
  ],
  "variations": [
	{
	  "regular_price": "29.98",
	  "attributes": [
		{
		  "name": "Color",
		  "options": "Black"
		}
	  ]
	},
	{
	  "regular_price": "29.98",
	  "attributes": [
		{
		  "name": "color",
		  "options": "Green"
		}
	  ]
	}
  ]
}

*/
