# Gravity Forms Request

Utility to make WordPress REST API requests. It's a wrapper around window.fetch

## Installation

Install the module

```bash
npm install @gravityforms/request
```

**Note**: This package requires `node` 14.15.0 or later, and `npm` 6.14.8 or later. If needing support for IE11 make sure to include the fetch polyfill for that browser.

## Overview

The request module abstracts away most of the pain of dealing with raw fetch.

Basic principles:
* When a base_path needs variables in it, use the template function as seen in path map and pass those as "restParams" in your options
* When you need query args appended for a get, pass them as "params" in your options object
* When you want to POST set method: 'POST' and if passing json body set json: { data } in your options

## Usage

Required in `gravityforms.config.js` in your project root:

```js
module.exports = {
	requestConfig: {
		endpoints: {
			get_something: {
				path       : '/wp-json/gf/v2/get_something',
				rest_params: '',
				nonce      : null,
			},
		},
	}
	// other configs
}
```

Using the module:

```js
import request from '@gravityforms/request';

const getSomething = async ( id ) => {
	const other_stuff = getSomeStuff( id );
	const response = await request( 'get_something', {
		method: 'POST',
		body: {
			some_data: { stuff: 'here', },
			other_stuff,
		},
	} );

	console.log( response.data );
};
```


