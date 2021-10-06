# Gravity Forms Components

The Gravity Forms component library, for both vanilla js and React components.

## Installation

Install the module

```bash
npm install @gravityforms/components
```

**Note**: This package requires `node` 14.15.0 or later, and `npm` 6.14.8 or later. 

## Overview

This component library houses the JavaScript for both our plain js and React based components in the Gravity Forms ui. 

## Components

### Vanilla JavaScript

#### Flyout

A flyout component that can be attached to any element or the body, with options for flyout direction and many other settings.

Options:

```js
animationDelay: 215, // total runtime of close animation. must be synced with css
closeButtonClasses: 'gform-flyout__close', // classes for the close button
content: '', // the html content
description: '', // the optional description for the flyout
direction: 'right', // direction to fly in from, left or right
id: uniqueId( 'flyout' ), // id for the flyout
insertPosition: 'beforeend', // insert position relative to target
lockBody: false, // whether to lock body scroll when open
onClose: () => {}, // function to fire when closed
onOpen: () => {}, // function to fire when opened
position: 'fixed', // fixed or absolute positioning
renderOnInit: true, // render on initialization?
target: 'body', // the selector to append the flyout to
title: '', // the optional title for the flyout
triggers: '[data-js="gform-trigger-flyout"]', // the selector[s] of the trigger that shows it
wrapperClasses: 'gform-flyout', // additional classes for the wrapper
```

## Usage

```js
import Flyout from '@gravityforms/components/js/flyout';

const init = () => {
	const flyoutInstance = new Flyout( {
		content: 'Hello',
		position: 'absolute',
		target: '.gflow-inbox.gflow-grid',
		title: 'Inbox Settings',
		triggers: '[data-js="inbox-settings"]',
		wrapperClasses: 'gform-flyout gform-flyout--inbox-settings',
	} );

	console.log( flyoutInstance );
};
```


