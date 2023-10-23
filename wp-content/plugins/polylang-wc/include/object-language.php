<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract class to use for object types that support at least one language.
 *
 * @since 1.9
 */
abstract class PLLWC_Object_Language {

	/**
	 * Instance of `PLL_Translatable_Object`.
	 *
	 * @var PLL_Translatable_Object
	 */
	protected $object;

	/**
	 * Adds hooks.
	 *
	 * @since 1.9
	 *
	 * @return self
	 */
	public function init() {
		return $this;
	}

	/**
	 * Returns the language taxonomy name.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_tax_language(): string {
		return $this->object->get_tax_language();
	}

	/**
	 * Stores the object's language in the database.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param int                     $id   Object ID.
	 * @param PLL_Language|string|int $lang Language (object, slug, or term ID).
	 * @return bool True when successfully assigned. False otherwise (or if the given language is already assigned to
	 *              the object).
	 */
	public function set_language( int $id, $lang ): bool {
		return $this->object->set_language( $id, $lang );
	}

	/**
	 * Returns the language of an object.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param int    $id    Object ID.
	 * @param string $field Optional, the language field to return (@see PLL_Language), defaults to `'slug'`.
	 *                      A composite value can be used for language term property values, in the form of
	 *                      `{language_taxonomy_name}:{property_name}` (see {@see PLL_Language::get_tax_prop()} for
	 *                      the possible values). Ex: `term_language:term_taxonomy_id`.
	 * @return string|int|bool|string[] The requested field value of the object language, `false` if no language is
	 *                                  associated to that object.
	 *
	 * @phpstan-param non-falsy-string $field
	 * @phpstan-return (
	 *     $field is 'slug' ? non-empty-string : string|int|bool|list<non-empty-string>
	 * )|false
	 */
	public function get_language( int $id, string $field = 'slug' ) {
		$lang = $this->object->get_language( $id );
		return ! empty( $lang ) ? $lang->get_prop( $field ) : false;
	}

	/**
	 * A JOIN clause to add to sql queries when filtering by language is needed directly in query.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param string $alias Optional alias for object table.
	 * @return string The JOIN clause.
	 *
	 * @phpstan-return non-empty-string
	 */
	public function join_clause( string $alias = '' ): string {
		return $this->object->join_clause( $alias );
	}

	/**
	 * A WHERE clause to add to sql queries when filtering by language is needed directly in query.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param PLL_Language|PLL_Language[]|string|string[] $lang A `PLL_Language` object, or a comma separated list of
	 *                                                          language slugs, or an array of language slugs or objects.
	 * @return string The WHERE clause.
	 *
	 * @phpstan-param PLL_Language|PLL_Language[]|non-empty-string|non-empty-string[] $lang
	 */
	public function where_clause( $lang ): string {
		return $this->object->where_clause( $lang );
	}
}
