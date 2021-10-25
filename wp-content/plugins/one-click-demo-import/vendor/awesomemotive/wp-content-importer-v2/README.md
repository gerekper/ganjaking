# WP content importer used in OCDI

List of files from the [original repo](https://github.com/humanmade/WordPress-Importer/):

- class-logger-cli.php,
- class-logger.php,
- class-wxr-importer.php


One click demo import plugin page: https://wordpress.org/plugins/one-click-demo-import/

One click demo import github page: https://github.com/awesomemotive/one-click-demo-import

## Changelog

*February 12th 2021*
- Replaced deprecated WP function `wp_slash_strings_only` with `wp_slash`.

*July 21st 2020*
- Fixed incorrect post meta import.
- Fixed Elementor import after `wp_slash` updates in this repo.

*July 14th 2020*
- Fixed incorrect post and post meta import (unicode and other special characters were not escaped properly).

*February 7th 2018*
- Clean up the WXRImporter code
- Created a "wrapper" class `Importer.php` with additional functionality (importing by smaller parts -> users, categories, tags, terms and posts)
- tagging version 2.0

*October 29th 2016*

- Cleaned up this forked repo, to only include the thing we need in the OCDI plugin.
- Changed the class names and use psr-4 autoloading in composer.json

*October 26th 2016*

- made a fork from the original repo
- merged a pull request for "term meta data" from the original repo: https://github.com/humanmade/WordPress-Importer/pull/18
