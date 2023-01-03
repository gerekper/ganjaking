# WPMUDEV Black Friday Banner #

This is a submodule which can be used on our free plugins.

See more details on [this task](https://incsub.atlassian.net/browse/PM-304).

See the list of UTM URLs for all plugins [here](https://incsub.atlassian.net/browse/SID-3190).

# How to use it #

1. Insert this repository as **sub-module** into the existing project

2. Include the file `banner.php` in your plugin.

3. Create new instance of `WPMUDEV\BlackFriday\Banner` with your plugin data (see example below).

## IMPORTANT:

DO NOT include this submodule in Pro plugins. These notices are only for wp.org versions.


## Code Example (from SmartCrawl) ##

```
#!php

<?php
new \WPMUDEV\BlackFriday\Banner(
	array(
		'close'       => __( 'Close', 'wds' ),
		'get_deal'    => __( 'Get deal', 'wds' ),
		'intro'       => __( 'Black Friday offer for WP businesses and agencies', 'wds' ),
		'off'         => __( 'Off', 'wds' ),
		'title'       => __( 'Everything you need to run your WP business for', 'wds' ),
		'discount'    => __( '83.5', 'wds' ),
		'price'       => __( '3000', 'wds' ),
		'description' => __( 'From the creators of SmartCrawl, WPMU DEV’s all-in-one platform gives you all the Pro tools and support you need to run and grow a web development business. Trusted by over 50,000 web developers. Limited deals available.', 'wds' ),
	),
	'https://wpmudev.com/black-friday/?coupon=BFP-2022&utm_source=smartcrawl&utm_medium=plugin&utm_campaign=BFP-2022-smartcrawl&utm_id=BFP-2022&utm_term=BF-2022-plugin-SmartCrawl&utm_content=BF-2022',
	\WPMUDEV\BlackFriday\Banner::SMARTCRAWL
);
```

> IMPORTANT: Make sure to initialize this on a hook which is executed in admin-ajax requests too.

To s


## Testing Banners

To see the banners before the due time, you can fake the current date by using the filter.

```
<?php
// Set current date as 22nd Nov.
add_filter(
	'wpmudev_blackfriday_current_date',
	function() {
		return '22-11-2022';
	}
);
```

# Development

Do not commit anything directly to `master` branch. The `master` branch should always be production ready. All plugins will be using it as a submodule.

## Build Tasks (npm)

Everything should be handled by npm. Note that you don't need to interact with Gulp in a direct way.

| Command              | Action                                                 |
|----------------------|--------------------------------------------------------|
| `npm run watch`      | Compiles and watch for changes.                        |
| `npm run compile`    | Compile production ready assets.                       |
| `npm run build`  | Build production ready submodule inside `/build/` folder |

## Git Workflow

- Create a new branch from `dev` branch: `git checkout -b branch-name`. Try to give it a descriptive name. For example:
    -   `release/X.X.X` for next releases
    -   `new/some-feature` for new features
    -   `enhance/some-enhancement` for enhancements
    -   `fix/some-bug` for bug fixing
- Make your commits and push the new branch: `git push -u origin branch-name`
- File the new Pull Request against `dev` branch
- Assign somebody to review your code.
- Once the PR is approved and finished, merge it in `dev` branch.
- Checkout `dev` branch.
- Run `npm run build` and copy all files and folders from the `build` folder.
- Checkout `master` branch and replace all files and folders with copied content from the build folder.
- Commit and push the `master` branch changes.
- Inform all devs to update the submodule.