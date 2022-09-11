<?php

class WPML_Media_Duplication_Setup {

	const MEDIA_SETTINGS_OPTION_KEY = '_wpml_media';

	public static function initialize_settings() {
		if ( ! get_option( self::MEDIA_SETTINGS_OPTION_KEY, [] ) ) {
			$settings = [
				'new_content_settings' => [
					'always_translate_media' => true,
					'duplicate_media'        => true,
					'duplicate_featured'     => true,
				],
				'translate_media_library_texts' => false,
			];
			update_option( self::MEDIA_SETTINGS_OPTION_KEY, $settings );
		}
	}

	public static function isTranslateMediaLibraryTextsEnabled() {
		$settings = get_option( self::MEDIA_SETTINGS_OPTION_KEY, [] );
		return \WPML\FP\Obj::propOr(false, 'translate_media_library_texts', $settings);
	}
}
