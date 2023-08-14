<?php

namespace SearchWP\Admin\Extensions;

use SearchWP\License;

/**
 * Extensions manages SearchWP extensions functionality.
 *
 * @since 4.2.2
 */
class Extensions {

	/**
	 * Extensions data storage.
	 *
	 * @since 4.2.2
	 *
	 * @var array
	 */
	private static $storage;

	/**
	 * Fetch extensions data from the remote source.
	 * Temporarily mocks the remote with a hardcoded array.
	 *
	 * @since 4.2.2
	 *
	 * @return array
	 */
	private static function fetch(): array {

		return [
			'searchwp-woocommerce'                       =>
				[
					'title'     => 'WooCommerce Integration',
					'slug'      => 'searchwp-woocommerce',
					'file_name' => 'searchwp-woocommerce/searchwp-woocommerce.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/woocommerce-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/woocommerce.png',
					'excerpt'   => 'WooCommerce is one of the leading e-commerce platforms for WordPress. SearchWP makes searching your WooCommerce store much more powerful than native WordPress search.',
					'id'        => 33339,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-metrics'                           =>
				[
					'title'     => 'Metrics',
					'slug'      => 'searchwp-metrics',
					'file_name' => 'searchwp-metrics/searchwp-metrics.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/metrics/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/metrics.png',
					'excerpt'   => 'Metrics collects comprehensive data for on-site search, and provides you with actionable advice about refinements you can make to your content.',
					'id'        => 86386,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-related'                           =>
				[
					'title'     => 'Related',
					'slug'      => 'searchwp-related',
					'file_name' => 'searchwp-related/searchwp-related.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/related/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/related.jpeg',
					'excerpt'   => 'Use SearchWP to power related content on your site! One click integration combined with an awesome template loader makes implementation easy!',
					'id'        => 85457,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-custom-results-order'              =>
				[
					'title'     => 'Custom Results Order',
					'slug'      => 'searchwp-custom-results-order',
					'file_name' => 'searchwp-custom-results-order/searchwp-custom-results-order.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/custom-results-order/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/custom-results-order.png',
					'excerpt'   => 'Customize the order of search results for particular queries, ensuring that certain entries appear first!',
					'id'        => 177762,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-redirects'                         =>
				[
					'title'     => 'Redirects',
					'slug'      => 'searchwp-redirects',
					'file_name' => 'searchwp-redirects/searchwp-redirects.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/redirects/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/redirects.png',
					'excerpt'   => 'Automatically redirect to a specific page when certain searches are performed!',
					'id'        => 89175,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-source-gravity-forms'              =>
				[
					'title'     => 'Source - Gravity Forms',
					'slug'      => 'searchwp-source-gravity-forms',
					'file_name' => 'searchwp-source-gravity-forms/searchwp-source-gravity-forms.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/source-gravity-forms/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/gravity-forms.png',
					'excerpt'   => 'Quickly make Gravity Forms Entries searchable on your site using SearchWP!',
					'id'        => 248821,
					'license'   =>
						[
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-live-ajax-search'                  =>
				[
					'title'     => 'Live Search',
					'slug'      => 'searchwp-live-ajax-search',
					'file_name' => 'searchwp-live-ajax-search/searchwp-live-ajax-search.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/live-search/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/live-ajax-search.png',
					'excerpt'   => 'Automatically (or manually) enhance your existing search forms with no configuration necessary. You can also fully configure just about everything, right down to the loading spinner and results template.',
					'id'        => 0,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-modal-search-form'                 =>
				[
					'title'     => 'Modal Search Form',
					'slug'      => 'searchwp-modal-search-form',
					'file_name' => 'searchwp-modal-search-form/searchwp-modal-form.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/modal-form/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/modal-search-form.png',
					'excerpt'   => 'Use Modal Form to quickly and easily add a lightweight, accessible modal search form (that matches your active theme) to your site!',
					'id'        => 0,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-shortcodes'                        =>
				[
					'title'     => 'Shortcodes',
					'slug'      => 'searchwp-shortcodes',
					'file_name' => 'searchwp-shortcodes/searchwp-shortcodes.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/shortcodes/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/shortcodes.png',
					'excerpt'   => 'If you’re not comfortable (or able) to edit template files to implement search forms and results, this extension provides Shortcodes that generate both search forms and results loops for SearchWP search engines.',
					'id'        => 33253,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-exclude-ui'                        =>
				[
					'title'     => 'Exclude UI',
					'slug'      => 'searchwp-exclude-ui',
					'file_name' => 'searchwp-exclude-ui/searchwp-exclude-ui.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/exclude-ui/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/exclude-ui.png',
					'excerpt'   => 'Add a checkbox to every Publish meta box allowing you to easily exclude an entry from search',
					'id'        => 36614,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-xpdf'                              =>
				[
					'title'     => 'Xpdf Integration',
					'slug'      => 'searchwp-xpdf',
					'file_name' => 'searchwp-xpdf/searchwp-xpdf.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/xpdf-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/xpdf.png',
					'excerpt'   => 'While SearchWP does the best it can to extract content from your PDFs out of the box using PHP only, Xpdf does an even better job.',
					'id'        => 33650,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-boolean'                           =>
				[
					'title'     => 'Boolean Search',
					'slug'      => 'searchwp-boolean',
					'file_name' => 'searchwp-boolean/searchwp-boolean.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/boolean-search/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/boolean-search.png',
					'excerpt'   => 'By default SearchWP pulls results using all keywords in a search and weights them accordingly, highest weight wins. This extension allows users to exclude keywords from search results, e.g. “cats -dogs” would return search results including “cats” but not “dogs”.',
					'id'        => 33684,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-diagnostics'                       =>
				[
					'title'     => 'Diagnostics',
					'slug'      => 'searchwp-diagnostics',
					'file_name' => 'searchwp-diagnostics/searchwp-diagnostics.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/diagnostics/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/diagnostics.png',
					'excerpt'   => 'With this Extension you can easily retrieve various data and information from under the hood of the SearchWP index. New features being added regularly.',
					'id'        => 33682,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-term-priority'                     =>
				[
					'title'     => 'Term Archive Priority',
					'slug'      => 'searchwp-term-priority',
					'file_name' => 'searchwp-term-priority/searchwp-term-priority.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/term-archive-priority/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/term-archive-priority.png',
					'excerpt'   => 'The Term Archive Priority Extension will force term archive pages to bubble to the top of search results when a match is found. This Extension only applies to supplemental search engines.',
					'id'        => 33679,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-edd-integration'                   =>
				[
					'title'     => 'EDD Integration',
					'slug'      => 'searchwp-edd-integration',
					'file_name' => 'searchwp-edd-integration/searchwp-edd-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/easy-digital-downloads-edd-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/easy-digital-downloads.svg',
					'excerpt'   => 'Automatically integrate with EDD’s Shortcodes and more',
					'id'        => 41570,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-wpml'                              =>
				[
					'title'     => 'WPML Integration',
					'slug'      => 'searchwp-wpml',
					'file_name' => 'searchwp-wpml/searchwp-wpml.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/wpml-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/wpml.png',
					'excerpt'   => 'WPML is a fantastic WordPress plugin that allows your content to go multilingual. The WPML Integration Extension shows SearchWP how to limit search results to the active language at the time of searching.',
					'id'        => 33645,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-meta-box-integration'              =>
				[
					'title'     => 'Meta Box Integration',
					'slug'      => 'searchwp-meta-box-integration',
					'file_name' => 'searchwp-meta-box-integration/searchwp-meta-box-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/meta-box-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/meta-box.png',
					'excerpt'   => 'With SearchWP’s Meta Box Integration, you’ll be able to easily add your Meta Box fields to your search engines.',
					'id'        => 193494,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-wp-job-manager-integration'        =>
				[
					'title'     => 'WP Job Manager Integration',
					'slug'      => 'searchwp-wp-job-manager-integration',
					'file_name' => 'searchwp-wp-job-manager-integration/searchwp-wp-job-manager-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/wp-job-manager-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/wp-job-manager.png',
					'excerpt'   => 'WP Job Manager is a great plugin that allows you to implement a job board on your site. It comes with a feature-rich search but if you’d like to further enhance the keyword implementation you can integrate SearchWP with it directly.',
					'id'        => 33362,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-polylang'                          =>
				[
					'title'     => 'Polylang Integration',
					'slug'      => 'searchwp-polylang',
					'file_name' => 'searchwp-polylang/searchwp-polylang.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/polylang-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/polylang.png',
					'excerpt'   => 'Polylang is an awesome WordPress plugin that allows your content to go multilingual. The Polylang Integration Extension shows SearchWP how to limit search results to the active language at the time of searching.',
					'id'        => 33648,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-bbpress'                           =>
				[
					'title'     => 'bbPress Integration',
					'slug'      => 'searchwp-bbpress',
					'file_name' => 'searchwp-bbpress/searchwp-bbpress.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/bbpress-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/bbpress.png',
					'excerpt'   => 'bbPress normally prevents searching by excluding it’s own post type. The bbPress Integration Extension allows SearchWP to index forum content and include it in search results. Best of all you can have Reply weight count toward parent Topics!',
					'id'        => 33686,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-give'                              =>
				[
					'title'     => 'Give Integration',
					'slug'      => 'searchwp-give',
					'file_name' => 'searchwp-give/searchwp-give.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/give-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/give.jpeg',
					'excerpt'   => 'Integrate SearchWP and Give allowing you to effectively search your forms by title or metadata!',
					'id'        => 152587,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-enable-media-replace'              =>
				[
					'title'     => 'Enable Media Replace Integration',
					'slug'      => 'searchwp-enable-media-replace',
					'file_name' => 'searchwp-enable-media-replace/searchwp-enable-media-replace.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/enable-media-replace-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/enable-media-replace.png',
					'excerpt'   => 'Integrate SearchWP with Enable Media Replace to automatically update file content when changes are made',
					'id'        => 88188,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-privatecontent'                    =>
				[
					'title'     => 'PrivateContent Integration',
					'slug'      => 'searchwp-privatecontent',
					'file_name' => 'searchwp-privatecontent/searchwp-privatecontent.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/privatecontent-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/privatecontent.svg',
					'excerpt'   => 'PrivateContent is a popular plugin that allows you to restrict your WordPress content based on user groups you have defined. This integration ensures that results are limited to the appropriate group(s).',
					'id'        => 33250,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-co-authors-plus'                   =>
				[
					'title'     => 'Co-Authors Plus Integration',
					'slug'      => 'searchwp-co-authors-plus',
					'file_name' => 'searchwp-co-authors-plus/searchwp-co-authors-plus.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/co-authors-plus-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/co-authors-plus.png',
					'excerpt'   => 'Integrate with Automattic’s Co-Authors Plus plugin and include Author details in searches',
					'id'        => 54834,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-wp-document-revisions-integration' =>
				[
					'title'     => 'WP Document Revisions Integration',
					'slug'      => 'searchwp-wp-document-revisions-integration',
					'file_name' => 'searchwp-wp-document-revisions-integration/searchwp-wp-document-revisions-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/wp-document-revisions-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/wp-document-revisions.png',
					'excerpt'   => 'Integrate SearchWP with WP Document Revisions',
					'id'        => 38053,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-herothemes-integration'            =>
				[
					'title'     => 'HeroThemes Integration',
					'slug'      => 'searchwp-herothemes-integration',
					'file_name' => 'searchwp-herothemes-integration/searchwp-herothemes-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/herothemes-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/herothemes.png',
					'excerpt'   => 'Integrate SearchWP results in HeroThemes products',
					'id'        => 63360,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-directorypress'                    =>
				[
					'title'     => 'DirectoryPress Integration',
					'slug'      => 'searchwp-directorypress',
					'file_name' => 'searchwp-directorypress/searchwp-directorypress.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/directorypress-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/directorypress.png',
					'excerpt'   => 'DirectoryPress is a popular theme that facilitates creating directories of many kinds. It offers an advanced search with a number of filters specific to DirectoryPress. This extension integrates SearchWP into the text search in DirectoryPress.',
					'id'        => 31026,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-bigcommerce-integration'           =>
				[
					'title'     => 'BigCommerce Integration',
					'slug'      => 'searchwp-bigcommerce-integration',
					'file_name' => 'searchwp-bigcommerce-integration/searchwp-bigcommerce-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/bigcommerce-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/bigcommerce.png',
					'excerpt'   => 'BigCommerce is a popular e-commerce platform that includes powerful features. SearchWP’s BigCommerce Integration Extension makes your online store even more powerful by directly integrating BigCommerce product attributes into your on-site search.',
					'id'        => 193520,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-flatsome'                          =>
				[
					'title'     => 'Flatsome Integration',
					'slug'      => 'searchwp-flatsome',
					'file_name' => 'searchwp-flatsome/searchwp-flatsome.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/flatsome-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/flatsome.png',
					'excerpt'   => 'Flatsome is a very popular WooCommerce & Business theme that has a great looking live search feature. Using the Flatsome Integration Extension for SearchWP you can directly utilize SearchWP for searches performed using that feature of Flatsome.',
					'id'        => 245715,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-mylisting-integration'             =>
				[
					'title'     => 'MyListing Integration',
					'slug'      => 'searchwp-mylisting-integration',
					'file_name' => 'searchwp-mylisting-integration/searchwp-mylisting-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/mylisting-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/mylisting.png',
					'excerpt'   => 'Integrate MyListing and SearchWP to improve keyword search!',
					'id'        => 226103,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-betterdocs'                        =>
				[
					'title'     => 'BetterDocs Integration',
					'slug'      => 'searchwp-betterdocs',
					'file_name' => 'searchwp-betterdocs/searchwp-betterdocs-integration.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/betterdocs-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/betterdocs.png',
					'excerpt'   => 'Use SearchWP to power BetterDocs’ live search functionality!',
					'id'        => 254291,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
			'searchwp-woocommerce-product-table'         =>
				[
					'title'     => 'WooCommerce Product Table Integration',
					'slug'      => 'searchwp-woocommerce-product-table',
					'file_name' => 'searchwp-woocommerce-product-table/searchwp-woocommerce-product-table.php',
					'url'       => SEARCHWP_EDD_STORE_URL . '/extensions/woocommerce-product-table-integration/',
					'image'     => SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions/woocommerce-product-table.png',
					'excerpt'   => 'The SearchWP WooCommerce Product Table Integration provides integration between SearchWP and WooCommerce Product Table, the easy way to speed up shopping by listing your products in a quick order form. It upgrades the basic search box which comes with WooCommerce Product Table so that your customers can benefit from SearchWP’s advanced search features.',
					'id'        => 282718,
					'license'   =>
						[
							0 => 'standard',
							1 => 'pro',
							2 => 'agency',
						],
				],
		];
	}

	/**
	 * Set activation/installation statuses for every extension.
	 *
	 * @since 4.2.2
	 *
	 * @param array $extensions Extensions data list.
	 *
	 * @return array
	 */
	private static function set_statuses( array $extensions ) {

		$license_type = License::get_type();

		$installed = array_keys( get_plugins() );
		$active    = get_option( 'active_plugins', [] );

		foreach ( $extensions as $key => $extension ) {

			$extensions[ $key ]['plugin_allowed']   = in_array( $license_type, $extension['license'], true );
			$extensions[ $key ]['plugin_installed'] = in_array( $extension['file_name'], $installed, true );
			$extensions[ $key ]['plugin_status']    = 'disallowed';

			if ( $extensions[ $key ]['plugin_allowed'] && $extensions[ $key ]['plugin_installed'] ) {
				$extensions[ $key ]['plugin_status'] = in_array( $extension['file_name'], $active, true ) ? 'active' : 'inactive';
			}

			if ( $extensions[ $key ]['plugin_allowed'] && ! $extensions[ $key ]['plugin_installed'] ) {
				$extensions[ $key ]['plugin_status'] = 'missing';
			}
		}

		return $extensions;
	}

	/**
	 * Get data for all extensions or one specific extension.
	 *
	 * @since 4.2.2
	 *
	 * @param string $slug Extension slug to get data for a single extension.
	 */
	public static function get( string $slug = '' ) {

		if ( empty( self::$storage ) ) {
			self::$storage = self::set_statuses( self::fetch() );
		}

		if ( ! empty( $slug ) ) {
			return self::$storage[ $slug ] ?? null;
		}

		return self::$storage;
	}

	/**
	 * Get data for all extensions allowed by the current license.
	 *
	 * @since 4.2.2
	 *
	 * @return array
	 */
	public static function get_allowed(): array {

		return wp_list_filter( self::get(), [ 'plugin_allowed' => true ] );
	}

	/**
	 * Get data for all extensions disallowed by the current license.
	 *
	 * @since 4.2.2
	 *
	 * @return array
	 */
	public static function get_disallowed(): array {

		return wp_list_filter( self::get(), [ 'plugin_allowed' => false ] );
	}

	/**
	 * Determine if the extension installations are allowed.
	 *
	 * @since 4.2.2
	 *
	 * @return bool
	 */
	public static function current_user_can_install() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		// Determine whether file modifications are allowed for SearchWP.
		if ( ! wp_is_file_mod_allowed( 'searchwp_can_install' ) ) {
			return false;
		}

		// Allow extensions installation if license is not expired, enabled and valid.
		return License::is_active();
	}

	/**
	 * Get download URL for the extension.
	 * Works with both WP.org and EDD extensions.
	 *
	 * @since 4.2.2
	 *
	 * @param array $extension Extension data.
	 */
	public static function get_download_url( array $extension ) {

		if ( ! isset( $extension['id'], $extension['slug'] ) ) {
			return '';
		}

		$url  = SEARCHWP_EDD_STORE_URL;
		$args = [
			'edd_action' => 'get_version',
			'item_id'    => $extension['id'],
			'license'    => License::is_active() ? sanitize_key( License::get_key() ) : '',
		];

		if ( self::is_wporg_extension( $extension ) ) {
			$url  = 'https://api.wordpress.org/plugins/info/1.2/';
			$args = [
				'action'  => 'plugin_information',
				'request' => [
					'slug' => $extension['slug'],
				],
			];
		}

		$response = wp_remote_get( $url, [ 'body' => $args ] );
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );

		return ! empty( $body['download_link'] ) ? $body['download_link'] : '';
	}

	/**
	 * Check if the extension is WP.org extension.
	 *
	 * @since 4.2.2
	 *
	 * @param array $extension Extension data.
	 *
	 * @return bool
	 */
	private static function is_wporg_extension( array $extension ) {

		return isset( $extension['id'] ) && $extension['id'] === 0;
	}
}
