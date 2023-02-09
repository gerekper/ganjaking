<?php

namespace GFML\Compatibility\FeedAddon;

use GFAPI;
use GFML_TM_API;

/**
 * Base class for translating feed addons.
 */
abstract class FeedAddon implements \IWPML_Action {

	const PRIORITY_AFTER_ADDON_COMPLETES = 11;

	/** @var GFML_TM_API */
	private $gfmlTmApi;

	/** @var string */
	private $prefix;

	public function __construct( GFML_TM_API $gfmlTmApi, $prefix ) {
		$this->gfmlTmApi = $gfmlTmApi;
		$this->prefix    = $prefix;
	}

	public function add_hooks() {
		add_action( 'gform_post_save_feed_settings', [ $this, 'registerOnSave' ], 10, 3 );
		add_action( 'gform_forms_post_import', [ $this, 'registerOnImport' ], static::PRIORITY_AFTER_ADDON_COMPLETES );
		add_action( 'gform_post_form_duplicated', [ $this, 'registerOnDuplicate' ], static::PRIORITY_AFTER_ADDON_COMPLETES, 2 );
	}

	/**
	 * Register feed strings when feed is created / updated.
	 *
	 * @param int   $feedId
	 * @param int   $formId
	 * @param array $feedMeta
	 */
	public function registerOnSave( $feedId, $formId, $feedMeta ) {
		$form = GFAPI::get_form( $formId );

		if ( $form ) {
			$this->registerFeedStrings( $feedId, $form, $feedMeta );
		}
	}

	/**
	 * Register feed strings when a form is duplicated.
	 *
	 * @param int $oldId
	 * @param int $newId
	 */
	public function registerOnDuplicate( $oldId, $newId ) {
		$form = GFAPI::get_form( $newId );

		if ( $form ) {
			$this->registerOnImport( [ $form ] );
		}
	}

	/**
	 * Register feed strings when forms are imported.
	 *
	 * @param array $forms
	 */
	public function registerOnImport( $forms ) {
		if ( is_array( $forms ) ) {
			foreach ( $forms as $form ) {
				foreach ( $this->getImportedFeeds( $form['id'] ) as $feed ) {
					$this->registerFeedStrings( $feed['id'], $form, $feed['meta'] );
				}
			}
		}
	}

	/**
	 * Register strings from $feedMeta for each of the keys returned by getTranslatableKeys().
	 *
	 * @param int   $feedId
	 * @param array $form
	 * @param array $feedMeta
	 */
	private function registerFeedStrings( $feedId, $form, $feedMeta ) {
		$formPackage = $this->gfmlTmApi->get_form_package( $form );

		foreach ( $this->getTranslatableKeys() as $translatable_key ) {
			foreach ( $translatable_key->getValues( $feedMeta, $feedId ) as $value ) {
				$this->gfmlTmApi->register_gf_string(
					$value->getStringValue(),
					$value->getStringName( $this->prefix ),
					$formPackage,
					$value->getStringTitle(),
					$value->getStringKind()
				);
			}
		}
	}

	/**
	 * The list of imported feeds to register.
	 *
	 * @param int $formId
	 * @return array
	 */
	abstract protected function getImportedFeeds( $formId );

	/**
	 * The list of keys to translate.
	 *
	 * @return TranslatableKey[]
	 */
	abstract protected function getTranslatableKeys();
}
