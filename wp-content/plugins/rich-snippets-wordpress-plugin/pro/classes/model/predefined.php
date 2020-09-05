<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Predefined.
 *
 * Functions to install/update predefined snippets.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Predefined_Model {

	/**
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function article() {
		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f34dd4994dcf",
                "context": "http://schema.org",
                "type": "Article",
                "dateModified-prop-599be31c45e07": {
                    "0": "current_post_modified_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "author-prop-599be31c45e2d": {
                    "0": "http://schema.org/Person",
                    "1": {
                        "id": "snip-5f34dd4994c32",
                        "context": "http://schema.org",
                        "type": "Person",
                        "url-prop-599be31c45d55": {
                            "0": "current_post_author_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "name-prop-599be31c45d7d": {
                            "0": "current_post_author_name",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "mainEntityOfPage-prop-599be31c45e4f": {
                    "0": "http://schema.org/WebPage",
                    "1": {
                        "id": "snip-5f34dd4994c95",
                        "context": "http://schema.org",
                        "type": "WebPage",
                        "@id-prop-5d5e6759a925e": {
                            "0": "textfield",
                            "1": "#webpage",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "description-prop-599be31c45e70": {
                    "0": "current_post_excerpt",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "datePublished-prop-599be31c45e97": {
                    "0": "current_post_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "headline-prop-599be31c45f10": {
                    "0": "current_post_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "publisher-prop-5bb1d64111e07": {
                    "0": "http://schema.org/Organization",
                    "1": {
                        "id": "snip-5f34dd4994d7a",
                        "context": "http://schema.org",
                        "type": "Organization",
                        "@id-prop-5f34dd144ada3": {
                            "0": "textfield",
                            "1": "#organization",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "image-prop-599be31c45dba": {
                    "0": "current_post_thumbnail_url",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "image-prop-5d568b16e2fbc": {
                    "0": "textfield",
                    "1": "https://please-enter-a-fallback-url.here",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "post"
                        },
                        {
                            "param": "page_type",
                            "operator": "!=",
                            "value": "front_page"
                        }
                    ],
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "page"
                        },
                        {
                            "param": "page_type",
                            "operator": "!=",
                            "value": "front_page"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Article', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}

	/**
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public static function organization() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-global-organization",
                "context": "http:\/\/schema.org",
                "type": "Organization",
                "url-prop-599be6521135e": {
                    "0": "blog_url",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "name-prop-599be652113e9": {
                    "0": "blog_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "logo-prop-599be65211453": {
                    "0": "http:\/\/schema.org\/ImageObject",
                    "1": {
                        "id": "snip-599be6401df71",
                        "context": "http:\/\/schema.org",
                        "type": "ImageObject",
                        "url-prop-599be652112f6": {
                            "0": "site_icon_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "width-prop-599be65211337": {
                            "0": "site_icon_width",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "@id-prop-5d83322ce0854": {
                    "0": "textfield",
                    "1": "#organization",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "all"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Organization', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}

	/**
	 * @return array
	 * @since 2.3.1
	 *
	 */
	public static function review_of_product() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5d88a349d0c9b",
                "context": "http:\/\/schema.org",
                "type": "Review",
                "publisher-prop-5d88a349d8488": {
                    "0": "http:\/\/schema.org\/Organization",
                    "1": {
                        "id": "snip-5d88a3585c10c",
                        "context": "http:\/\/schema.org",
                        "type": "Organization",
                        "@id-prop-5d88a394268d1": {
                            "0": "textfield",
                            "1": "#organization",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "itemReviewed-prop-5d88a349dfcfc": {
                    "0": "http:\/\/schema.org\/Product",
                    "1": {
                        "id": "snip-5d88a3ac53d84",
                        "context": "http:\/\/schema.org",
                        "type": "Product",
                        "image-prop-5d88a3ac6d26a": {
                            "0": "current_post_thumbnail_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "name-prop-5d88a3ac72e08": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": false
                        },
                        "description-prop-5d88a3ac99942": {
                            "0": "current_post_excerpt",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "brand-prop-5d88a3aca0514": {
                            "0": "http:\/\/schema.org\/Brand",
                            "1": {
                                "id": "snip-5d88b78604591",
                                "context": "http:\/\/schema.org",
                                "type": "Brand",
                                "name-prop-5d88b78665e59": {
                                    "0": "textfield",
                                    "1": "",
                                    "overridable": true,
                                    "overridable_multiple": false
                                }
                            },
                            "overridable": true,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": false
                },
                "dateModified-prop-5d88a349e7c13": {
                    "0": "current_post_modified_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "author-prop-5d88a349eea1e": {
                    "0": "http:\/\/schema.org\/Person",
                    "1": {
                        "id": "snip-5d88b7995fdeb",
                        "context": "http:\/\/schema.org",
                        "type": "Person",
                        "url-prop-5d88b799651f3": {
                            "0": "current_post_author_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "name-prop-5d88b7996a37d": {
                            "0": "current_post_author_name",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "reviewRating-prop-5d88a34a00eb3": {
                    "0": "misc_rating_5_star",
                    "1": 5,
                    "overridable": true,
                    "overridable_multiple": false
                },
                "description-prop-5d88a34a084fd": {
                    "0": "textfield",
                    "1": "",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "datePublished-prop-5d88a34a15a56": {
                    "0": "current_post_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "post"
                        },
                        {
                            "param": "post_category",
                            "operator": "==",
                            "value": "999"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title'  => __( 'Review of Product', 'rich-snippets-schema' ),
			'status' => 'draft',
			'json'   => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.3.1
	 *
	 */
	public static function product_woocommerce() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f3a4ae6e53f0",
                "context": "http://schema.org",
                "type": "Product",
                "sku-prop-5a90e4c190ec5": {
                    "0": "woocommerce_sku",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "mpn-prop-5a90e4c1a707e": {
                    "0": "textfield_woocommerce_product_attribute",
                    "1": "mpn",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "gtin8-prop-5a90e4c1bc50c": {
                    "0": "textfield_woocommerce_product_attribute",
                    "1": "gtin8",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "image-prop-5a90e4c1cf2ec": {
                    "0": "http://schema.org/ImageObject",
                    "1": {
                        "id": "snip-5f3a4ae6e52c0",
                        "context": "http://schema.org",
                        "type": "ImageObject",
                        "height-prop-5a97cc064cf42": {
                            "0": "current_post_thumbnail_height",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "width-prop-5a97cc065a42e": {
                            "0": "current_post_thumbnail_width",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "url-prop-5a97cc066871e": {
                            "0": "current_post_thumbnail_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "name-prop-5a90e4c1e3c44": {
                    "0": "current_post_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "aggregateRating-prop-5a90e4c20773c": {
                    "0": "woocommerce_review_rating",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "offers-prop-5a90e4c21d251": {
                    "0": "woocommerce_offers",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "gtin14-prop-5a90e4c23b1a4": {
                    "0": "textfield_woocommerce_product_attribute",
                    "1": "gtin14",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "gtin13-prop-5a90e4c261b5e": {
                    "0": "textfield_woocommerce_product_attribute",
                    "1": "gtin13",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "description-prop-5a90e4c297536": {
                    "0": "current_post_excerpt",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "brand-prop-5a90e4c2b5e88": {
                    "0": "http://schema.org/Brand",
                    "1": {
                        "id": "snip-5f3a4ae6e53ae",
                        "context": "http://schema.org",
                        "type": "Brand",
                        "name-prop-5a97cc563c122": {
                            "0": "textfield_woocommerce_product_attribute",
                            "1": "brand",
                            "overridable": true,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": false
                },
                "gtin12-prop-5c0f9ac960039": {
                    "0": "textfield_woocommerce_product_attribute",
                    "1": "gtin12",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "review-prop-5c0f9ae8cd0bc": {
                    "0": "woocommerce_reviews",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "mainEntityOfPage-prop-5f3b743653ca5": {
                    "0": "http://schema.org/WebPage",
                    "1": {
                        "id": "snip-5f3b743d040fe",
                        "context": "http://schema.org",
                        "type": "WebPage",
                        "@id-prop-5f3b7442f06b8": {
                            "0": "textfield",
                            "1": "#webpage",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "product"
                        }
                    ]
                ]
            }
        </script>
		<?php
		return array(
			'title'  => __( 'Product (WooCommerce)', 'rich-snippets-schema' ),
			'status' => function_exists( 'WC' ) ? 'publish' : 'draft',
			'json'   => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.7.0
	 *
	 */
	public static function recipe() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f34db77b74e8",
                "context": "http://schema.org",
                "type": "Recipe",
                "recipeYield-prop-5c0d1a90b6931": {
                    "0": "textfield",
                    "1": "",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "recipeCategory-prop-5c0d1a90be968": {
                    "0": "current_category",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "image-prop-5c0d1a90c6e69": {
                    "0": "http://schema.org/ImageObject",
                    "1": {
                        "id": "snip-5f34db77b7106",
                        "context": "http://schema.org",
                        "type": "ImageObject",
                        "height-prop-5c0d2014d6457": {
                            "0": "current_post_thumbnail_width",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "width-prop-5c0d2014df612": {
                            "0": "current_post_thumbnail_height",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "url-prop-5c0d2014e7fa0": {
                            "0": "current_post_thumbnail_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "name-prop-5c0d1a90cf5bf": {
                    "0": "current_post_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "aggregateRating-prop-5c0d1a90d7f15": {
                    "0": "misc_rating_5_star",
                    "1": 0,
                    "overridable": true,
                    "overridable_multiple": false
                },
                "recipeIngredient-prop-5c0d1a90e07c8": {
                    "0": "textfield",
                    "1": "",
                    "overridable": true,
                    "overridable_multiple": true
                },
                "recipeInstructions-prop-5c0d1a90e9dae": {
                    "0": "http://schema.org/HowToStep",
                    "1": {
                        "id": "snip-5f34db77b71b7",
                        "context": "http://schema.org",
                        "type": "HowToStep",
                        "text-prop-5c0d1e7c0dffc": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": true
                },
                "nutrition-prop-5c0d1a910fedc": {
                    "0": "http://schema.org/NutritionInformation",
                    "1": {
                        "id": "snip-5f34db77b7212",
                        "context": "http://schema.org",
                        "type": "NutritionInformation",
                        "calories-prop-5c0d1ba6e056f": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": false
                },
                "prepTime-prop-5c0d1a91234c0": {
                    "0": "misc_duration_minutes",
                    "1": 30,
                    "overridable": true,
                    "overridable_multiple": false
                },
                "dateModified-prop-5c0d1a912eac1": {
                    "0": "current_post_modified_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "author-prop-5c0d1a913d071": {
                    "0": "http://schema.org/Person",
                    "1": {
                        "id": "snip-5f34db77b72a7",
                        "context": "http://schema.org",
                        "type": "Person",
                        "name-prop-5c0d1ade06d98": {
                            "0": "current_post_author_name",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "url-prop-5c0d1ade10859": {
                            "0": "current_post_author_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "cookTime-prop-5c0d1a914d109": {
                    "0": "misc_duration_minutes",
                    "1": 30,
                    "overridable": true,
                    "overridable_multiple": false
                },
                "totalTime-prop-5c0d1a9157585": {
                    "0": "misc_duration_minutes",
                    "1": 30,
                    "overridable": true,
                    "overridable_multiple": false
                },
                "review-prop-5c0d1a915fef0": {
                    "0": "http://schema.org/Review",
                    "1": {
                        "id": "snip-5f34db77b7372",
                        "context": "http://schema.org",
                        "type": "Review",
                        "reviewRating-prop-5c0d1eb18d4cb": {
                            "0": "misc_rating_5_star",
                            "1": 0,
                            "overridable": true,
                            "overridable_multiple": false
                        },
                        "publisher-prop-5c0d1eb19e2e5": {
                            "0": "http://schema.org/Organization",
                            "1": {
                                "id": "snip-5f34e572b82d9",
                                "context": "http://schema.org",
                                "type": "Organization",
                                "@id-prop-5f34e57aae227": {
                                    "0": "textfield",
                                    "1": "#organization",
                                    "overridable": false,
                                    "overridable_multiple": false
                                }
                            },
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "reviewBody-prop-5c0d1eb1c5246": {
                            "0": "textfield",
                            "1": "A review of the dish.",
                            "overridable": true,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": false
                },
                "description-prop-5c0d1a9166ba8": {
                    "0": "textfield",
                    "1": "A short summary describing the dish.",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "datePublished-prop-5c0d1a916c158": {
                    "0": "current_post_date",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "keywords-prop-5c0d1b851ceb7": {
                    "0": "textfield",
                    "1": "",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "recipeCuisine-prop-5c0d1e1e1b043": {
                    "0": "textfield",
                    "1": "",
                    "overridable": true,
                    "overridable_multiple": false
                },
                "video-prop-5c0d1f2e823a4": {
                    "0": "http://schema.org/VideoObject",
                    "1": {
                        "id": "snip-5f34db77b74d7",
                        "context": "http://schema.org",
                        "type": "VideoObject",
                        "name-prop-5c0d1f4a3448e": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "thumbnailUrl-prop-5c0d1f4a41fb8": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": true
                        },
                        "description-prop-5c0d1f4a46ad5": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": false
                        },
                        "contentUrl-prop-5c0d1f87990e9": {
                            "0": "textfield",
                            "1": "",
                            "overridable": true,
                            "overridable_multiple": false
                        },
                        "embedUrl-prop-5c0d1f96a595a": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": true,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "recipe"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title'  => __( 'Recipe', 'rich-snippets-schema' ),
			'status' => 'draft',
			'json'   => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function sitelink_serachbox() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5d83339c65c30",
                "context": "http:\/\/schema.org",
                "type": "WebSite",
                "potentialAction-prop-5c616e94f2263": {
                    "0": "http:\/\/schema.org\/SearchAction",
                    "1": {
                        "id": "snip-5d83339c65b77",
                        "context": "http:\/\/schema.org",
                        "type": "SearchAction",
                        "target-prop-5c616ea8abfc2": {
                            "0": "search_url_search_term",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "query-input-prop-5c6171f30aebf": {
                            "0": "textfield",
                            "1": "required name=search_term_string",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "url-prop-5c6a7b2f9ca8e": {
                    "0": "blog_url",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "name-prop-5cd1b2a0ecb55": {
                    "0": "blog_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "description-prop-5cd1b2ad6bde1": {
                    "0": "blog_description",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "publisher-prop-5cd1b2c8901e5": {
                    "0": "http:\/\/schema.org\/Organization",
                    "1": {
                        "id": "snip-5d83339c65c09",
                        "context": "http:\/\/schema.org",
                        "type": "Organization",
                        "@id-prop-5cd1b2df66972": {
                            "0": "textfield",
                            "1": "#organization",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "@id-prop-5cd1b32b41cfe": {
                    "0": "textfield",
                    "1": "#website",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "post"
                        }
                    ],
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "page"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Website with Sitelink Searchbox', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function carousel_articles() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f3a4ae6f2d6c",
                "context": "http://schema.org",
                "type": "ItemList",
                "itemListElement-prop-5c641bfb50334": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f3a4ae6f2d5f",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "position-prop-5c641c1b47139": {
                            "0": "textfield_sequential_number",
                            "1": "carousel_category_posts",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "url-prop-5c641cf01b059": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "name-prop-5f3b6f897646b": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "loop": "main_query"
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "archive_category"
                        }
                    ],
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "archive_post_tag"
                        }
                    ],
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "search"
                        }
                    ],
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "front_page"
                        }
                    ],
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "posts_page"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Carousel for Frontpage, Posts-Page, Search-Page & Archive pages', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function breadcrumbs_posts() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f34e267330fb",
                "context": "http://schema.org",
                "type": "BreadcrumbList",
                "itemListElement-prop-5c63f68381ee5": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f34e26732e40",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "name-prop-5c64155761709": {
                            "0": "term_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "position-prop-5c6415576a168": {
                            "0": "textfield_sequential_number",
                            "1": "breadcrumbs_posts",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "item-prop-5c64155778310": {
                            "0": "term_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "loop": "taxonomy_category"
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "itemListElement-prop-5c6404b73450b": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f34e267330d0",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "name-prop-5c6404be5f27d": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "position-prop-5c6404be6c368": {
                            "0": "textfield_sequential_number",
                            "1": "breadcrumbs_posts",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "item-prop-5c6404be79508": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "@id-prop-5f34e2703274c": {
                    "0": "textfield",
                    "1": "#breadcrumbs",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "post"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Breadcrumbs for posts using categories', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function breadcrumbs_pages() {

		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f34e205235d3",
                "context": "http://schema.org",
                "type": "BreadcrumbList",
                "itemListElement-prop-5c62a14111e9c": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f34e20522ec5",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "name-prop-5c62b1dca84e0": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "position-prop-5c62b1dcb4b23": {
                            "0": "textfield_sequential_number",
                            "1": "breadcrumbs_pages",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "item-prop-5c62b1dcc6fe1": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "loop": "page_parents"
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "itemListElement-prop-5c64053c41463": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f34e2052358c",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "name-prop-5c6405420a089": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "position-prop-5c64054214640": {
                            "0": "textfield_sequential_number",
                            "1": "breadcrumbs_pages",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "item-prop-5c64054220672": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "@id-prop-5f34e20d45bfb": {
                    "0": "textfield",
                    "1": "#breadcrumbs",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "page"
                        }
                    ]
                ]
            }
        </script>
		<?php

		return array(
			'title' => __( 'Breadcrumbs for pages using page hierarchy', 'rich-snippets-schema' ),
			'json'  => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
		);
	}


	/**
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function carousel_products() {
		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f34e4a1a6e71",
                "context": "http://schema.org",
                "type": "ItemList",
                "itemListElement-prop-5c6529c924e4a": {
                    "0": "http://schema.org/ListItem",
                    "1": {
                        "id": "snip-5f34e4a1a6e63",
                        "context": "http://schema.org",
                        "type": "ListItem",
                        "position-prop-5c6529d776f58": {
                            "0": "textfield_sequential_number",
                            "1": "carousel_woocommerce_products",
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "url-prop-5c652a11c053b": {
                            "0": "current_post_url",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "name-prop-5f34e4bb2d036": {
                            "0": "current_post_title",
                            "1": null,
                            "overridable": false,
                            "overridable_multiple": false
                        },
                        "loop": "main_query"
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "archive_product_cat"
                        }
                    ],
                    [
                        {
                            "param": "page_type",
                            "operator": "==",
                            "value": "archive_product_tag"
                        }
                    ]
                ]
            }
        </script>
		<?php
		return array(
			'title'  => __( 'Carousel for Product Archive pages (WooCommerce)', 'rich-snippets-schema' ),
			'json'   => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
			'status' => function_exists( 'WC' ) ? 'publish' : 'draft',
		);
	}


	/**
	 * @return array
	 * @since 2.19.0
	 */
	public static function webpage_posts() {
		ob_start();
		?>
        <script type="application/ld+json">
            {
                "id": "snip-5f3a4ae7106f6",
                "context": "http://schema.org",
                "type": "WebPage",
                "isPartOf-prop-5ceba164c6786": {
                    "0": "http://schema.org/WebSite",
                    "1": {
                        "id": "snip-5f3a4ae7106b3",
                        "context": "http://schema.org",
                        "type": "WebSite",
                        "@id-prop-5ceba1707e49b": {
                            "0": "textfield",
                            "1": "#website",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "name-prop-5ceba1817040f": {
                    "0": "current_post_title",
                    "1": null,
                    "overridable": false,
                    "overridable_multiple": false
                },
                "@id-prop-5ceba19c4b47a": {
                    "0": "textfield",
                    "1": "#webpage",
                    "overridable": false,
                    "overridable_multiple": false
                },
                "breadcrumb-prop-5f3a4d632e08a": {
                    "0": "http://schema.org/BreadcrumbList",
                    "1": {
                        "id": "snip-5f3a4d71bc9ce",
                        "context": "http://schema.org",
                        "type": "BreadcrumbList",
                        "@id-prop-5f3a4d7fc0680": {
                            "0": "textfield",
                            "1": "#breadcrumbs",
                            "overridable": false,
                            "overridable_multiple": false
                        }
                    },
                    "overridable": false,
                    "overridable_multiple": false
                },
                "_is_export": true,
                "@ruleset": [
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "post"
                        }
                    ],
                    [
                        {
                            "param": "post_type",
                            "operator": "==",
                            "value": "page"
                        }
                    ]
                ]
            }
        </script>
		<?php
		return array(
			'title'  => __( 'WebPage for Pages and Posts', 'rich-snippets-schema' ),
			'json'   => str_replace( [ '<script type="application/ld+json">', '</script>' ], '', ob_get_clean() ),
			'status' => 'publish',
		);
	}
}
