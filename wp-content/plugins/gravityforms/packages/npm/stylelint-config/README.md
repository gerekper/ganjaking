# Gravity Forms Stylelint Config

Stylelint config for use in Gravity Forms projects.

## Installation

Install the module and required dependencies:

```bash
npm install @gravityforms/stylelint-config stylelint stylelint-order --save-dev
```

**Note**: This package requires `node` 14.15.0 or later, and `npm` 6.14.8 or later.

## Overview

An extension of the [WordPress Stylelint](https://github.com/WordPress/gutenberg/tree/trunk/packages/stylelint-config) config with additional rules used at Gravity Forms, which includes:

* Sort order: alphabetical
* BEM formatting as warnings
* Some small tweaks to the WordPress rules that better suit our working environment.

## Usage

After installing, create a `.stylelintrc.json` file in the root of your project, and then add:

```json
{
  "extends": "@gravityforms/stylelint-config",
  "rules": {}
}
```

The rules object is your optional overrides of the config for your project.


