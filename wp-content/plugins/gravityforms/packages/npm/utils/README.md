# Gravity Forms Utils

Custom JavaScript utilities for Gravity Forms development.

## Installation

Install the module

```bash
npm install @gravityforms/utils --save-dev
```

**Note**: This package requires `node` 14.15.0 or later, and `npm` 6.14.8 or later.

## Overview

A collection of JavaScript utilities that we use in our day to day JavaScript. Function docs to come, but for now, functions by name:

- applyBrowserClasses
- arrayToInt
- bodyLock
- browsers
- convertElements
- debounce
- focusLoop
- getChildren
- getClosest
- getCoords
- getFocusable
- getHiddenHeight
- getNodes
- hasClassFromArray
- hasScrollbar
- insertAfter
- insertBefore
- isExternalLink
- isFileLink
- isFunction
- isImageLink
- isJson
- localStorage
- objectAssign
- objectToFormData
- parseUrl
- popup
- queryToJson
- ready
- removeClassThatContains
- scrollHorizontal
- scrollSpy
- scrollTo
- sessionStorage
- setAttributes
- shouldLoadChunk
- slide
- smoothAnchors
- trigger
- uniqueId
- updateQueryVar
- visible
- winPosition

## Usage

Example:

```js
import { isJson } from '@gravityforms/utils';

const doSomethingWithJson = ( data ) => {
	if ( ! isJson( data ) ) {
		return;
    }
}
```


