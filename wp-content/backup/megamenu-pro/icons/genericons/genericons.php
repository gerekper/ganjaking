<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Genericons') ) :

/**
 *
 */
class Mega_Menu_Genericons {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_genericons_scss'), 10 );
		add_filter( 'megamenu_icon_tabs', array( $this, 'genericons_selector'), 10, 5 );
		add_action( 'megamenu_enqueue_public_scripts', array ( $this, 'enqueue_public_scripts'), 10 );

	}

	/**
	 * Add the CSS required to display genericons icons to the main SCSS file
	 *
	 * @since 1.0
	 * @param string $scss
	 * @return string
	 */
	public function append_genericons_scss( $scss ) {

		$path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/genericons.scss';

		$contents = file_get_contents( $path );

 		return $scss . $contents;

	}


	/**
	 * Enqueue cenericon CSS
	 *
	 * @since 1.0
	 */
	public function enqueue_public_scripts() {
		$settings = get_option("megamenu_settings");

        if ( ! is_array( $settings ) ) {
        	return;
        }

        if ( is_array( $settings ) && isset( $settings['enqueue_genericons'] ) && $settings['enqueue_genericons'] == 'disabled' ) {
        	return;
        }

        wp_enqueue_style( 'megamenu-genericons', plugins_url( 'genericons/genericons.css' , __FILE__ ), false, MEGAMENU_PRO_VERSION );
	}


	/**
	 * Generate HTML for the genericons selector
	 *
	 * @since 1.0
	 * @param array $tabs
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return array
	 */
	public function genericons_selector( $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {
		$settings = get_option("megamenu_settings");
        
        if ( is_array( $settings ) && isset( $settings['enqueue_genericons'] ) && $settings['enqueue_genericons'] == 'disabled' ) {
        	$html = "<div class='notice notice-warning'>" . _("Genericons has been dequeued under Mega Menu > General Settings.") . "</div>";
        } else {
        	$html = "";
        }
		
        foreach ( $this->icons() as $code => $class ) {

            $bits = explode( "-", $code );
            $code = "&#x" . $bits[1] . "";
            $type = $bits[0];

            $html .= "<div class='{$type}'>";
            $html .= "    <input class='radio' id='{$class}' type='radio' rel='{$code}' name='settings[icon]' value='{$class}' " . checked( $menu_item_meta['icon'], $class, false ) . " />";
            $html .= "    <label rel='{$code}' for='{$class}' title='{$class}'></label>";
            $html .= "</div>";
        
        }
    
		$tabs['genericons'] = array(
			'title' => __("Genericons", "megamenupro"),
			'active' => isset( $menu_item_meta['icon'] ) && substr( $menu_item_meta['icon'], 0, strlen("genericon") ) === "genericon",
			'content' => $html
		);

		return $tabs;

	}


	/**
	 * Return an array of genericons
	 *
	 * @since 1.0
	 * @param array $icons
	 */
	private function icons() {
		
		$icons = array(  
			"genericon-f423" => "genericon-404",
			"genericon-f508" => "genericon-activity",
			"genericon-f509" => "genericon-anchor",
			"genericon-f101" => "genericon-aside",
			"genericon-f416" => "genericon-attachment",
			"genericon-f109" => "genericon-audio",
			"genericon-f471" => "genericon-bold",
			"genericon-f444" => "genericon-book",
			"genericon-f50a" => "genericon-bug",
			"genericon-f447" => "genericon-cart",
			"genericon-f301" => "genericon-category",
			"genericon-f108" => "genericon-chat",
			"genericon-f418" => "genericon-checkmark",
			"genericon-f405" => "genericon-close",
			"genericon-f406" => "genericon-close-alt",
			"genericon-f426" => "genericon-cloud",
			"genericon-f440" => "genericon-cloud-download",
			"genericon-f441" => "genericon-cloud-upload",
			"genericon-f462" => "genericon-code",
			"genericon-f216" => "genericon-codepen",
			"genericon-f445" => "genericon-cog",
			"genericon-f432" => "genericon-collapse",
			"genericon-f300" => "genericon-comment",
			"genericon-f305" => "genericon-day",
			"genericon-f221" => "genericon-digg",
			"genericon-f443" => "genericon-document",
			"genericon-f428" => "genericon-dot",
			"genericon-f502" => "genericon-downarrow",
			"genericon-f50b" => "genericon-download",
			"genericon-f436" => "genericon-draggable",
			"genericon-f201" => "genericon-dribbble",
			"genericon-f225" => "genericon-dropbox",
			"genericon-f433" => "genericon-dropdown",
			"genericon-f434" => "genericon-dropdown-left",
			"genericon-f411" => "genericon-edit",
			"genericon-f476" => "genericon-ellipsis",
			"genericon-f431" => "genericon-expand",
			"genericon-f442" => "genericon-external",
			"genericon-f203" => "genericon-facebook",
			"genericon-f204" => "genericon-facebook-alt",
			"genericon-f458" => "genericon-fastforward",
			"genericon-f413" => "genericon-feed",
			"genericon-f468" => "genericon-flag",
			"genericon-f211" => "genericon-flickr",
			"genericon-f226" => "genericon-foursquare",
			"genericon-f474" => "genericon-fullscreen",
			"genericon-f103" => "genericon-gallery",
			"genericon-f200" => "genericon-github",
			"genericon-f206" => "genericon-googleplus",
			"genericon-f218" => "genericon-googleplus-alt",
			"genericon-f50c" => "genericon-handset",
			"genericon-f461" => "genericon-heart",
			"genericon-f457" => "genericon-help",
			"genericon-f404" => "genericon-hide",
			"genericon-f505" => "genericon-hierarchy",
			"genericon-f409" => "genericon-home",
			"genericon-f102" => "genericon-image",
			"genericon-f455" => "genericon-info",
			"genericon-f215" => "genericon-instagram",
			"genericon-f472" => "genericon-italic",
			"genericon-f427" => "genericon-key",
			"genericon-f503" => "genericon-leftarrow",
			"genericon-f107" => "genericon-link",
			"genericon-f207" => "genericon-linkedin",
			"genericon-f208" => "genericon-linkedin-alt",
			"genericon-f417" => "genericon-location",
			"genericon-f470" => "genericon-lock",
			"genericon-f410" => "genericon-mail",
			"genericon-f422" => "genericon-maximize",
			"genericon-f419" => "genericon-menu",
			"genericon-f50d" => "genericon-microphone",
			"genericon-f421" => "genericon-minimize",
			"genericon-f50e" => "genericon-minus",
			"genericon-f307" => "genericon-month",
			"genericon-f50f" => "genericon-move",
			"genericon-f429" => "genericon-next",
			"genericon-f456" => "genericon-notice",
			"genericon-f506" => "genericon-paintbrush",
			"genericon-f219" => "genericon-path",
			"genericon-f448" => "genericon-pause",
			"genericon-f437" => "genericon-phone",
			"genericon-f473" => "genericon-picture",
			"genericon-f308" => "genericon-pinned",
			"genericon-f209" => "genericon-pinterest",
			"genericon-f210" => "genericon-pinterest-alt",
			"genericon-f452" => "genericon-play",
			"genericon-f439" => "genericon-plugin",
			"genericon-f510" => "genericon-plus",
			"genericon-f224" => "genericon-pocket",
			"genericon-f217" => "genericon-polldaddy",
			"genericon-f460" => "genericon-portfolio",
			"genericon-f430" => "genericon-previous",
			"genericon-f469" => "genericon-print",
			"genericon-f106" => "genericon-quote",
			"genericon-f511" => "genericon-rating-empty",
			"genericon-f512" => "genericon-rating-full",
			"genericon-f513" => "genericon-rating-half",
			"genericon-f222" => "genericon-reddit",
			"genericon-f420" => "genericon-refresh",
			"genericon-f412" => "genericon-reply",
			"genericon-f466" => "genericon-reply-alt",
			"genericon-f467" => "genericon-reply-single",
			"genericon-f459" => "genericon-rewind",
			"genericon-f501" => "genericon-rightarrow",
			"genericon-f400" => "genericon-search",
			"genericon-f438" => "genericon-send-to-phone",
			"genericon-f454" => "genericon-send-to-tablet",
			"genericon-f415" => "genericon-share",
			"genericon-f403" => "genericon-show",
			"genericon-f514" => "genericon-shuffle",
			"genericon-f507" => "genericon-sitemap",
			"genericon-f451" => "genericon-skip-ahead",
			"genericon-f450" => "genericon-skip-back",
			"genericon-f220" => "genericon-skype",
			"genericon-f424" => "genericon-spam",
			"genericon-f515" => "genericon-spotify",
			"genericon-f100" => "genericon-standard",
			"genericon-f408" => "genericon-star",
			"genericon-f105" => "genericon-status",
			"genericon-f449" => "genericon-stop",
			"genericon-f223" => "genericon-stumbleupon",
			"genericon-f463" => "genericon-subscribe",
			"genericon-f465" => "genericon-subscribed",
			"genericon-f425" => "genericon-summary",
			"genericon-f453" => "genericon-tablet",
			"genericon-f302" => "genericon-tag",
			"genericon-f303" => "genericon-time",
			"genericon-f435" => "genericon-top",
			"genericon-f407" => "genericon-trash",
			"genericon-f214" => "genericon-tumblr",
			"genericon-f516" => "genericon-twitch",
			"genericon-f202" => "genericon-twitter",
			"genericon-f446" => "genericon-unapprove",
			"genericon-f464" => "genericon-unsubscribe",
			"genericon-f401" => "genericon-unzoom",
			"genericon-f500" => "genericon-uparrow",
			"genericon-f304" => "genericon-user",
			"genericon-f104" => "genericon-video",
			"genericon-f517" => "genericon-videocamera",
			"genericon-f212" => "genericon-vimeo",
			"genericon-f414" => "genericon-warning",
			"genericon-f475" => "genericon-website",
			"genericon-f306" => "genericon-week",
			"genericon-f205" => "genericon-wordpress",
			"genericon-f504" => "genericon-xpost",
			"genericon-f213" => "genericon-youtube",
			"genericon-f402" => "genericon-zoom"
		);

		$icons = apply_filters( "megamenu_genericons_icons", $icons );

		return $icons;

	}

}

endif;