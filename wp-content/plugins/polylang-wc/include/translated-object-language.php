<?php
/**
 * @package Polylang-WC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract class to use for object types that support languages and translations.
 *
 * @since 1.0
 * @since 1.9 Renamed from `PLLWC_Translated_Object_Language_CPT` to `PLLWC_Translated_Object_Language`.
 */
abstract class PLLWC_Translated_Object_Language extends PLLWC_Object_Language {

	/**
	 * Instance of `PLL_Translated_Object`.
	 *
	 * @var PLL_Translated_Object
	 */
	protected $object;

	/**
	 * Returns the translations group taxonomy name.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @return string
	 *
	 * @phpstan-return non-empty-string
	 */
	public function get_tax_translations(): string {
		return $this->object->get_tax_translations();
	}

	/**
	 * Saves the object's translations.
	 *
	 * @since 1.0
	 * @since 1.9 Returns the translations.
	 * @since 1.9 Type-hinted.
	 *
	 * @param int[] $translations An associative array of translations with language code as key and translation ID as value.
	 * @return int[] An associative array with language codes as key and object IDs as values.
	 *
	 * @phpstan-param non-empty-array<non-empty-string, positive-int> $translations
	 * @phpstan-return array<non-empty-string, positive-int>
	 */
	public function save_translations( array $translations ): array {
		return $this->object->save_translations( reset( $translations ), $translations );
	}

	/**
	 * Returns an array of translations of an object.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param int $id Object ID.
	 * @return int[] An associative array of translations with language code as key and translation ID as value.
	 *
	 * @phpstan-return array<non-empty-string, positive-int>
	 */
	public function get_translations( int $id ): array {
		return $this->object->get_translations( $id );
	}

	/**
	 * Among the object and its translations, returns the ID of the object which is in `$lang`.
	 *
	 * @since 1.0
	 * @since 1.9 Type-hinted.
	 *
	 * @param int                      $id   Object ID.
	 * @param PLL_Language|string|null $lang Optional language (object or slug), defaults to the current language.
	 * @return int|null The translation object ID if exists, `0` otherwise. `null` if the language is not defined yet.
	 *
	 * @phpstan-return int<0, max>|null
	 */
	public function get( int $id, $lang = null ) {
		$lang = ! empty( $lang ) ? $lang : pll_current_language( \OBJECT );

		if ( empty( $lang ) ) {
			return null;
		}

		return $this->object->get( $id, $lang );
	}
}
