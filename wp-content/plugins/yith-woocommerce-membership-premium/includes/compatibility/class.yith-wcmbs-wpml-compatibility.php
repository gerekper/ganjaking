<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCMBS_Wpml_Compatibility
 * @since   1.3.5
 */
class YITH_WCMBS_Wpml_Compatibility {

	/** @var \YITH_WCMBS_Wpml_Compatibility */
	private static $_instance;

	private $sitepress;
	private $current_language;
	private $default_language;

	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	private function __construct() {
		global $sitepress;
		if ( $sitepress ) {
			$this->_init();

			// add translated it to restricted items
			add_filter( 'yith_wcmbs_non_allowed_post_ids_for_user', array( $this, 'add_translations' ), 99 );

			// Translate plan titles
			add_filter( 'yith_wcmbs_membership_get_plan_title', array( $this, 'translate_plan_title' ), 10, 2 );
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
			add_action( 'save_post', array( $this, 'save_metabox' ) );

			// Show Membership products in all languages when setting them in plans
			add_action( 'wp_ajax_woocommerce_json_search_products_and_variations', array( $this, 'show_membership_products_in_all_languages' ), 9 );

			// Set language before send email
			add_action( 'yith_wcms_before_send_notification', array( $this, 'set_language' ) );
		}
	}

	private function _init() {
		global $sitepress;
		$this->sitepress        = $sitepress;
		$this->current_language = $this->sitepress->get_current_language();
		$this->default_language = $this->sitepress->get_default_language();
	}

	public function add_translations( $items ) {
		if ( ! ! $items && is_array( $items ) ) {
			foreach ( $items as $item ) {
				$trid = $this->sitepress->get_element_trid( $item );
				if ( $trid ) {
					$element_translations = $this->sitepress->get_element_translations( $trid );
					if ( $element_translations && is_array( $element_translations ) ) {
						foreach ( $element_translations as $code_lang => $translation ) {
							$items[] = absint( $translation->element_id );
						}
					}
				}
			}
		}

		return array_unique( $items );
	}

	public function translate_plan_title( $title, $membership ) {
		$plan_id            = $membership->plan_id;
		$title_translations = get_post_meta( $plan_id, '_yith_wcmbs_wpml_title_translations', true );
		if ( ! ! $title_translations && ! empty( $title_translations[ $this->current_language ] ) ) {
			$title = $title_translations[ $this->current_language ];
		}

		return $title;
	}

	public function add_metabox() {
		add_meta_box( 'yith-wcmbs-wpml-translations',
					  __( 'WPML Traslations', 'yith-woocommerce-membership' ),
					  array( $this, 'show_title_translations_metabox' ),
					  'yith-wcmbs-plan',
					  'side',
					  'default' );
	}

	public function show_title_translations_metabox( $post ) {
		$languages = $this->sitepress->get_active_languages();
		if ( isset( $languages[ $this->default_language ] ) ) {
			unset( $languages[ $this->default_language ] );
		}

		$title_translations = get_post_meta( $post->ID, '_yith_wcmbs_wpml_title_translations', true );

		foreach ( $languages as $language_code => $language ) {
			$language_name = isset( $language['display_name'] ) ? $language['display_name'] : $language_code;
			$name          = "_yith_wcmbs_wpml_title_translations[{$language_code}]";
			$value         = isset( $title_translations[ $language_code ] ) ? $title_translations[ $language_code ] : '';
			?>
			<p>
				<label for="yith_wcmbs_wpml_title_translations_<?php echo esc_attr( $language_code ); ?>"><?php echo esc_html( sprintf( __( 'Title (%s)', 'yith-woocommerce-membership' ), $language_name ) ); ?></label>
				<input type="text" name="<?php echo esc_attr( $name ); ?>"
						id="yith_wcmbs_wpml_title_translations_<?php echo esc_attr( $language_code ); ?>"
						value="<?php echo esc_attr( $value ); ?>"/>
			</p>
			<?php
		}
	}

	public function save_metabox( $post_id ) {
		if ( isset( $_POST['_yith_wcmbs_wpml_title_translations'] ) ) {
			update_post_meta( $post_id, '_yith_wcmbs_wpml_title_translations', $_POST['_yith_wcmbs_wpml_title_translations'] );
		}
	}

	/**
	 * Show Membership products in all languages when setting them in plans
	 *
	 * @since 1.3.21
	 */
	public function show_membership_products_in_all_languages() {
		global $woocommerce_wpml;
		if ( ! empty( $_REQUEST['yith_wcmbs_search_for_membership_products'] ) && $woocommerce_wpml ) {
			remove_filter( 'woocommerce_json_search_found_products', array( $woocommerce_wpml->products, 'filter_wc_searched_products_on_admin' ) );
		}
	}

	/**
	 * Set language for email notitifications relative Membership
	 *
	 * @param [type] $membership
	 *
	 * @return void
	 */
	public function set_language( $membership ) {
		$language = get_post_meta( $membership->order_id, 'wpml_language', true );
		global $sitepress;
		$sitepress->switch_lang( $language, true );
	}

}

/**
 * Unique access to instance of YITH_WCMBS_Wpml_Compatibility class
 *
 * @return YITH_WCMBS_Wpml_Compatibility
 */
function YITH_WCMBS_Wpml_Compatibility() {
	return YITH_WCMBS_Wpml_Compatibility::get_instance();
}