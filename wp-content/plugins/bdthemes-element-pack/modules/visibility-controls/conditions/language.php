<?php
	
	namespace ElementPack\Modules\VisibilityControls\Conditions;
	
	use ElementPack\Base\Condition;
	use Elementor\Controls_Manager;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	
	class Language extends Condition {
		
		/**
		 * Get the name of condition
		 * @return string as per our condition control name
		 */
		public function get_name() {
			return 'language';
		}
		
		/**
		 * Get the title of condition
		 * @return string as per condition control title
		 */
		public function get_title() {
			return esc_html__( 'Site Language', 'bdthemes-element-pack' );
		}

		/**
		 * Get the group of condition
		 * @return string as per our condition control name
		 */
		public function get_group() {
			return 'system';
		}
		
		/**
		 * Get the control value
		 * @return array as per condition control value
		 */
		public function get_control_value() {
			$lang_locales = array();

			$langs=array('af'=>array('name'=>'Afrikaans','code'=>'af','wp_locale'=>'af',),'sq'=>array('name'=>'Albanian','code'=>'sq','wp_locale'=>'sq',),'ar'=>array('name'=>'Arabic','code'=>'ar','wp_locale'=>'ar',),'bel'=>array('name'=>'Belarusian','code'=>'bel','wp_locale'=>'bel',),'bn_BD'=>array('name'=>'Bengali','code'=>'bn','wp_locale'=>'bn_BD',),'bs_BA'=>array('name'=>'Bosnian','code'=>'bs','wp_locale'=>'bs_BA',),'bg_BG'=>array('name'=>'Bulgarian','code'=>'bg','wp_locale'=>'bg_BG',),'ca'=>array('name'=>'Catalan','code'=>'ca','wp_locale'=>'ca',),'zh_CN'=>array('name'=>'Chinese (China)','code'=>'zh-cn','wp_locale'=>'zh_CN',),'zh_HK'=>array('name'=>'Chinese (Hong Kong)','code'=>'zh-hk','wp_locale'=>'zh_HK',),'hr'=>array('name'=>'Croatian','code'=>'hr','wp_locale'=>'hr',),'cs_CZ'=>array('name'=>'Czech','code'=>'cs','wp_locale'=>'cs_CZ',),'da_DK'=>array('name'=>'Danish','code'=>'da','wp_locale'=>'da_DK',),'nl_NL'=>array('name'=>'Dutch','code'=>'nl','wp_locale'=>'nl_NL',),'nl_BE'=>array('name'=>'Dutch (Belgium)','code'=>'nl-be','wp_locale'=>'nl_BE',),'en_US'=>array('name'=>'English','code'=>'en','wp_locale'=>'en_US',),'en_AU'=>array('name'=>'English (Australia)','code'=>'en-au','wp_locale'=>'en_AU',),'en_CA'=>array('name'=>'English (Canada)','code'=>'en-ca','wp_locale'=>'en_CA',),'en_GB'=>array('name'=>'English (UK)','code'=>'en-gb','wp_locale'=>'en_GB',),'et'=>array('name'=>'Estonian','code'=>'et','wp_locale'=>'et',),'fi'=>array('name'=>'Finnish','code'=>'fi','wp_locale'=>'fi',),'fr_BE'=>array('name'=>'French (Belgium)','code'=>'fr-be','wp_locale'=>'fr_BE',),'fr_FR'=>array('name'=>'French (France)','code'=>'fr','wp_locale'=>'fr_FR',),'ka_GE'=>array('name'=>'Georgian','code'=>'ka','wp_locale'=>'ka_GE',),'de_DE'=>array('name'=>'German','code'=>'de','wp_locale'=>'de_DE',),'de_CH'=>array('name'=>'German (Switzerland)','code'=>'de-ch','wp_locale'=>'de_CH',),'el'=>array('name'=>'Greek','code'=>'el','wp_locale'=>'el',),'he_IL'=>array('name'=>'Hebrew','code'=>'he','wp_locale'=>'he_IL',),'hi_IN'=>array('name'=>'Hindi','code'=>'hi','wp_locale'=>'hi_IN',),'hu_HU'=>array('name'=>'Hungarian','code'=>'hu','wp_locale'=>'hu_HU',),'is_IS'=>array('name'=>'Icelandic','code'=>'is','wp_locale'=>'is_IS',),'id_ID'=>array('name'=>'Indonesian','code'=>'id','wp_locale'=>'id_ID',),'ga'=>array('name'=>'Irish','code'=>'ga','wp_locale'=>'ga',),'it_IT'=>array('name'=>'Italian','code'=>'it','wp_locale'=>'it_IT',),'ja'=>array('name'=>'Japanese','code'=>'ja','wp_locale'=>'ja',),'kn'=>array('name'=>'Kannada','code'=>'kn','wp_locale'=>'kn',),'ko_KR'=>array('name'=>'Korean','code'=>'ko','wp_locale'=>'ko_KR',),'lt_LT'=>array('name'=>'Lithuanian','code'=>'lt','wp_locale'=>'lt_LT',),'lb_LU'=>array('name'=>'Luxembourgish','code'=>'lb','wp_locale'=>'lb_LU',),'ml_IN'=>array('name'=>'Malayalam','code'=>'ml','wp_locale'=>'ml_IN',),'ne_NP'=>array('name'=>'Nepali','code'=>'ne','wp_locale'=>'ne_NP',),'nb_NO'=>array('name'=>'Norwegian (Bokmål)','code'=>'no','wp_locale'=>'nb_NO',),'fa_IR'=>array('name'=>'Persian','code'=>'fa','wp_locale'=>'fa_IR',),'pl_PL'=>array('name'=>'Polish','code'=>'pl','wp_locale'=>'pl_PL',),'pt_BR'=>array('name'=>'Portuguese (Brazil)','code'=>'pt-br','wp_locale'=>'pt_BR',),'pt_PT'=>array('name'=>'Portuguese (Portugal)','code'=>'pt','wp_locale'=>'pt_PT',),'ro_RO'=>array('name'=>'Romanian','code'=>'ro','wp_locale'=>'ro_RO',),'ru_RU'=>array('name'=>'Russian','code'=>'ru','wp_locale'=>'ru_RU',),'ru_UA'=>array('name'=>'Russian (Ukraine)','code'=>'ru-ua','wp_locale'=>'ru_UA',),'sr_RS'=>array('name'=>'Serbian','code'=>'sr','wp_locale'=>'sr_RS',),'sk_SK'=>array('name'=>'Slovak','code'=>'sk','wp_locale'=>'sk_SK',),'sl_SI'=>array('name'=>'Slovenian','code'=>'sl','wp_locale'=>'sl_SI',),'azb'=>array('name'=>'South Azerbaijani','code'=>'azb','wp_locale'=>'azb',),'es_ES'=>array('name'=>'Spanish (Spain)','code'=>'es','wp_locale'=>'es_ES',),'sv_SE'=>array('name'=>'Swedish','code'=>'sv','wp_locale'=>'sv_SE',),'th'=>array('name'=>'Thai','code'=>'th','wp_locale'=>'th',),'tr_TR'=>array('name'=>'Turkish','code'=>'tr','wp_locale'=>'tr_TR',),'uk'=>array('name'=>'Ukrainian','code'=>'uk','wp_locale'=>'uk',),'vi'=>array('name'=>'Vietnamese','code'=>'vi','wp_locale'=>'vi',),);

			foreach ( $langs as $lang => $props ) {
				/* translators: %s: Language Name */
				$val                         = ucwords( $props['name'] );
				$lang_locales[ $lang ] = $val;
			}

			return array(
				'label'       => __( 'Value', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => array(),
				'options'     => $lang_locales,
				'multiple'    => true,
			);
		}
		
		/**
		 * Check the condition
		 *
		 * @param string $relation Comparison operator for compare function
		 * @param mixed $val will check the control value as per condition needs
		 *
		 */
		public function check( $relation, $val ) {
			$current_language = function_exists( 'get_locale' ) ? get_locale() : false;

			if ( ! $current_language || empty( $val ) ) {
				return;
			}

			$show = in_array( $current_language, (array) $val, true ) ? true : false;
			
			return $this->compare( $show, true, $relation );
		}
	}
