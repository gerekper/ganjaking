# Gravity Forms Gulp Tasks

Configurable Gulp tasks for use in Gravity Forms projects. Note: Full extendability still being worked on. This is currently internal to Gravity Forms core/add ons but will soon be usable by any add on author or plugin wishing to use our tooling.

## Installation

Install the module:

```bash
npm install @gravityforms/gulp-tasks --save-dev
```

**Note**: This package requires `node` 14.15.0 or later, and `npm` 6.14.8 or later.

## Overview

This module encapsulates all of our gulp tasks and makes them reusable by add ons in the Gravity Forms ecosystem. It has specialized tasks like extracting and injecting icon kits from Icomoon into postcss systems, browsersync enabled dev modes and more.

## Usage

After installing, in a `gravityforms.config.js` in the root directory add the following and tweak as needed in regards to paths or adding additional tasks.

```javascript
const { resolve } = require( 'path' );

module.export = {
	gulpConfig: {
		browserSync: {
			defaultUrl: 'gravity-forms.local',
			serverName: 'Gravity Forms Dev',
		},
		icons: {
			admin: {
				replaceName: /'gform-icons-admin' !important/g, // regex for the icomoon generated name to replace
				replaceScss: /\$icomoon-font-family: "gform-icons-admin" !default;\n/g, // regex for scss file replace
				varName: 'var(--t-font-family-admin-icons) !important', // the css variable name to replace replaceName with
			},
			theme: {
				replaceName: /'gform-icons-theme' !important/g,
				replaceScss: /\$icomoon-font-family: "gform-icons-theme" !default;\n/g,
				varName: 'var(--t-font-family-theme-icons) !important',
			}
		},
		paths: {
			css_dist: resolve( __dirname, 'css' ),
			css_src: resolve( __dirname, 'src/css' ),
			dev: resolve( __dirname, 'dev' ),
			fonts: resolve( __dirname, 'fonts' ),
			images: resolve( __dirname, 'images' ),
			js_dist: resolve( __dirname, 'assets/js/dist' ),
			js_src: resolve( __dirname, 'assets/js/src' ),
			legacy_css: resolve( __dirname, 'legacy/css' ),
			npm: resolve( __dirname, 'node_modules' ),
			postcss_assets_base_url: resolve( __dirname, '../' ),
			root: resolve( __dirname, '' ),
			settings_css_dist: resolve( __dirname, 'includes/settings/css' ),
		},
		tasks: [],
		tasksDir: resolve( __dirname, 'gulp-tasks' ),
		webpack: {
			alias: {
				common: resolve( __dirname, 'assets/js/src/common' ),
			},
			overrides: {
				externals: {
					admin: {
						'gform-admin-config': 'gform_admin_config',
						'gform-admin-i18n': 'gform_admin_i18n',
					},
					theme: {
						'gform-theme-config': 'gform_theme_config',
						'gform-theme-i18n': 'gform_theme_i18n',
					},
				},
				output: {
					uniqueName: 'gravityforms',
				},
			},
		}
	}
}
```
Then add this to your scripts block in your root package.json to enable all available tasks:

```json
{
  "scripts": {
    "start": "npm install && cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- dev",
    "dev": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- dev",
    "dist": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- dist",
    "icons:admin": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- icons:admin",
    "icons:theme": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- icons:theme",
    "validate": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- lint",
    "watch": "cd node_modules/@gravityforms/gulp-tasks && npm run gulp -- watch"
  }
}
```

You can now run any of those tasks with `npm run dev` etc.
