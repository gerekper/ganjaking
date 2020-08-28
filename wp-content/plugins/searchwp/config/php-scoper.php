<?php

declare(strict_types=1);

/**
 * PHP SCOPER BASE CONFIGURATION
 *
 * This configuration defines the way PHP Scoper @link https://github.com/humbug/php-scoper
 * will apply a custom prefix to all namespaces in the Composer packages we're using.
 *
 * This is necessary because WordPress does not have a proper method of handling dependencies
 * and as a result if multiple Plugins (or Themes, or custom code) are using the same Composer
 * packages, we'll end up in a race condition regarding which version is loaded. This is not ideal.
 *
 * PHP Scoper applies a custom namespace to all of the Composer packages in use by SearchWP
 * and relocates the prefixed versions into the `lib` directory. PHP Scoper also generates its
 * own `scoper-autoload.php` to handle additional class handling. Instead of SearchWP relying on
 * Composer's generated `autoload.php` we are instead fully relying on PHP Scoper's generated packages.
 *
 * That said, any time a Composer does something, PHP Scoper is run to generate an up-to-date autoloader.
 *
 * To manually regenerate a new `lib` and autoloader, fire this command:
 *     `composer php-scoper`
 *
 */

use Isolated\Symfony\Component\Finder\Finder;

return [
	'whitelist-global-classes' => false,
	'finders' => [
		Finder::create()
			->files()
			->ignoreVCS(true)
			->notName('/LICENSE|.*\\.md|.*\\.dist|psalm.xml|CHANGELOG\\.TXT|VERSION|Makefile|composer\\.json|composer\\.lock/')
			->exclude([
				'doc',
				'bin',
				'test',
				'Test',
				'test_old',
				'tests',
				'Tests',
				'vendor-bin',
				'node_modules',
				'examples',
				'fonts',
				'tools',
				'samples',
				'Samples',
			])
			->in( __DIR__ . '/../vendor' ),
	],
];