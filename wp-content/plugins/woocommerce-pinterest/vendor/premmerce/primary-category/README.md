# Premmerce Primary Category
A set of classes to add primary category functionalily to WordPress and Woocommerce.
For now it works only with Woocommerce products. WordPress posts and custom post types with categories can be added later.


## Getting Started

### Prerequisites

This instruction assumes you are already have an installed WordPress and plugin you are working on.
It also assumes you already configured access to this repository and can clone it.

### Installing

Easiest way to install is to require this package with composer inside your plugin project.

__First__, you need to add this repository to your composer.json.

You can do it from command line inside your composer project

```
composer config repositories.premmerce-primary-category vcs git@github.com:Premmerce/primary-category.git
```

... or by adding this to your composer.json

```
"repositories": {
        "premmerce-primary-category": {
            "type": "vcs",
            "url": "git@github.com:Premmerce/primary-category.git"
        }
    }
```

__Then__ require this package

```
composer require premmerce/primary-category
```

You will be asked for oAuth token from GitHub. 
Read the message and follow the link to create and copy your token. Paste it and hit Enter.


__Finally__, instantiate PrimaryCategory class in your plugin.

```
<?php
use Premmerce\PrimaryCategory\PrimaryCategory;
...

$primaryCategoryServiceContainer = \Premmerce\PrimaryCategory\ServiceContainer::getInstance();

$primaryCategoryServiceContainer->initPrimaryCategory($mainPluginFile);
```


If all works fine, you'll see 'Make primary' links next to your categories list on product/post edit pages.

## Authors

* **[Premmerce](https://premmerce.com)**

## License

This project is licensed under the [GPL-2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)+


