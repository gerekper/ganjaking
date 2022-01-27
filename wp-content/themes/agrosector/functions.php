<?php
function gt3_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'gt3_content_width', 940 );
}
add_action( 'after_setup_theme', 'gt3_content_width', 0 );
add_image_size('gt3theme_notebook',1366,0,false);
add_image_size('gt3theme_fhd',1920,0,false);

function gt3theme_max_srcset_image_width() {
    return 1920;
}
add_filter( 'max_srcset_image_width', 'gt3theme_max_srcset_image_width', 100);

add_filter('body_class','gt3theme_class_names');
if (!function_exists('gt3theme_class_names')) {
	function gt3theme_class_names($class) {
		if (post_password_required()) {
			$class[] = 'body_pp';
		}

		return $class;
	}
}

add_action( 'wbc_importer_dir_path', 'gt3_get_demo_data_path' );
function gt3_get_demo_data_path(){
    return trailingslashit( str_replace( '\\', '/', get_template_directory() ) ) . "/core/demo-data/";
}

add_action( 'gt3_homepage_importer_filter', 'gt3_homepage_importer_filter' );
function gt3_homepage_importer_filter(){
    return array(
       'demo' => 'Home',
    );
}

add_action( 'gt3_homepage_importer_slider_name', 'gt3_homepage_importer_slider_name' );
function gt3_homepage_importer_slider_name(){
    return array(
       'demo' => 'home_01.zip',
    );
}

add_filter('wp_get_attachment_image_attributes', 'gt3theme_attachment_image_attributes', 20, 3);
function gt3theme_attachment_image_attributes ($attr, $attachment, $size) {
    if (!key_exists('title',$attr)) {
        /* @var \WP_Post $attachment */
        if ($attachment instanceof \WP_Post) {
            $attr['title'] = $attachment->post_title;
        }
    }
    return $attr;
}

if (!function_exists('gt3_get_theme_option')) {
    function gt3_get_theme_option($optionname, $defaultValue = null){
        $gt3_options = get_option("agrosector_gt3_options");
        if (isset($gt3_options[$optionname])) {
            if (gettype($gt3_options[$optionname]) == "string") {
                return stripslashes($gt3_options[$optionname]);
            } else {
                return $gt3_options[$optionname];
            }
        } else {
            return $defaultValue;
        }
    }
}

if(!function_exists('gt3_option')) {
    function gt3_option($name) {
        if (  class_exists( 'Redux' ) ) {
            $theme_options = get_option( 'agrosector' );
            if (empty($theme_options)) {
                $theme_options = get_option( 'agrosector_default_options' );
            }
            return isset($theme_options[$name]) ? $theme_options[$name] : null;
        }else{
            $default_option = get_option( 'agrosector_default_options' );
            return isset($default_option[$name]) ? $default_option[$name] : null;
        }
    }
}

function gt3_activate_theme() {
	if ( current_user_can( 'manage_options' ) && !get_option( 'gt3_first_activation' ) ) {
		update_option( 'gt3_first_activation', 'true' );
        // need for 1170px grid
        if (!get_option( 'elementor_container_width' )) {
            update_option( 'elementor_container_width', '1190' );
        }

        update_option( 'elementor_disable_color_schemes', 'yes' );
        update_option( 'elementor_disable_typography_schemes', 'yes' );

		update_option( 'yith_woocompare_compare_button_in_products_list', 'yes' ); // YITH Compare
		update_option( 'woocommerce_catalog_columns', '3' );
		update_option( 'woocommerce_catalog_rows', '3' );
		update_option( 'woocommerce_single_image_width', 1200 );
		update_option( 'woocommerce_thumbnail_image_width', 800 );
		update_option( 'gallery_thumbnail_image_width', 800 );
	}
}
add_action( 'after_switch_theme', 'gt3_activate_theme' );

if(!function_exists('wp_body_classes')) {
    function wp_body_classes( $classes ) {
        $classes[] = gt3_option("add_default_typography_sapcing") == '1' ? 'gt3_default_typography_sapcing' : '';
        if (gt3_option("disable_right_click")) {
            $classes[] = 'disable_right_click';
            wp_localize_script('gt3-theme','gt3_rcg',array('alert'=>(gt3_option("disable_right_click_text"))));
        }

        return $classes;
    }
}
add_filter( 'body_class','wp_body_classes' );

if (!function_exists('gt3_theme_comment')) {
    function gt3_theme_comment($comment, $args, $depth){
        $max_depth_comment = ($args['max_depth'] > 4 ? 4 : $args['max_depth']);

        $GLOBALS['comment'] = $comment; ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
        <div id="comment-<?php comment_ID(); ?>" class="stand_comment">
            <div class="thiscommentbody">
                <div class="commentava">
                    <?php echo get_avatar($comment, 120); ?>
                </div>
                <div class="comment_info">
                    <div class="comment_author_says"><?php printf('%s', get_comment_author_link()) ?> <span><?php esc_html_e('says:', 'agrosector'); ?></span></div>
                    <div class="listing_meta">
                        <span><?php printf('%1$s', get_comment_date()) ?></span>
                        <?php edit_comment_link('<span>('.esc_html__('Edit', 'agrosector').')</span>', '  ', '') ?>
                    </div>
                </div>
                <div class="comment_content">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p><?php esc_html_e('Your comment is awaiting moderation.', 'agrosector'); ?></p>
                    <?php endif; ?>
                    <?php comment_text() ?>
                </div>
                <?php
                $icon_post_comments = '<span class="post_comments_icon"><span class="theme_icon-arrows-right"></span></span>';
                comment_reply_link(array_merge($args, array('depth' => $depth, 'reply_text' => esc_html__('Reply', 'agrosector') . ($icon_post_comments), 'max_depth' => $max_depth_comment)))
                ?>
            </div>
        </div>
        <?php
    }
}

#Custom paging
if (!function_exists('gt3_get_theme_pagination')) {
    function gt3_get_theme_pagination($range = 5, $type = "", $max_page = false, $paged_arg = false){
        if ($type == "show_in_shortcodes") {
            global $paged, $my_wp_query;
        } else {
            global $paged, $my_wp_query, $wp_query;
            if (is_null($my_wp_query)) $my_wp_query = $wp_query;
        }

        if (empty($paged) || !$paged_arg) {
            $paged = get_query_var('page') ? get_query_var('page') : get_query_var('paged') ? get_query_var('paged') : 1;
        }

        $compile = '';
        if (!$max_page) {
	        $max_page = $my_wp_query->max_num_pages;
        }

        if ($max_page > 1) {
            $compile .= '<ul class="pagerblock">';
        }
        if($paged > 1) $compile .= '<li class="prev_page"><a href="' .esc_url(get_pagenum_link($paged - 1)) . '"><i class="fa fa-angle-left"></i></a></li>';
        if ($max_page > 1) {
            if (!$paged) {
                $paged = 1;
            }
            if ($max_page > $range) {
                if ($paged < $range) {
                    for ($i = 1; $i <= ($range + 1); $i++) {
                        $compile .= "<li><a href='" . esc_url(get_pagenum_link($i)) . "'";
                        if ($i == $paged) $compile .= " class='current'";
                        $compile .= ">$i</a></li>";
                    }
                } elseif ($paged >= ($max_page - ceil(($range / 2)))) {
                    for ($i = $max_page - $range; $i <= $max_page; $i++) {
                        $compile .= "<li><a href='" . esc_url(get_pagenum_link($i)) . "'";
                        if ($i == $paged) $compile .= " class='current'";
                        $compile .= ">$i</a></li>";
                    }
                } elseif ($paged >= $range && $paged < ($max_page - ceil(($range / 2)))) {
                    for ($i = ($paged - ceil($range / 2)); $i <= ($paged + ceil(($range / 2))); $i++) {
                        $compile .= "<li><a href='" . esc_url(get_pagenum_link($i)) . "'";
                        if ($i == $paged) $compile .= " class='current'";
                        $compile .= ">$i</a></li>";
                    }
                }
            } else {
                for ($i = 1; $i <= $max_page; $i++) {
                    $compile .= "<li><a href='" . esc_url(get_pagenum_link($i)) . "'";
                    if ($i == $paged) $compile .= " class='current'";
                    $compile .= ">$i</a></li>";
                }
            }
        }
        if ($paged < $max_page) $compile .= '<li class="next_page"><a href="' . esc_url(get_pagenum_link($paged + 1)) . '"><i class="fa fa-angle-right"></i></a></li>';
        if ($max_page > 1) {
            $compile .= '</ul>';
        }

        return $compile;
    }
}

if (!function_exists('gt3_HexToRGB')) {
    function gt3_HexToRGB($hex = "#ffffff"){
        $color = array();
        if (strlen($hex) < 1) {
            $hex = "#ffffff";
        }

        $color['r'] = hexdec(substr($hex, 1, 2));
        $color['g'] = hexdec(substr($hex, 3, 2));
        $color['b'] = hexdec(substr($hex, 5, 2));

        return $color['r'] . "," . $color['g'] . "," . $color['b'];
    }
}

if (!function_exists('gt3_smarty_modifier_truncate')) {
    function gt3_smarty_modifier_truncate($string, $length = 80, $etc = '... ', $break_words = false) {
        if ($length == 0)
            return '';

        if (mb_strlen($string, 'utf8') > $length) {
            $length -= mb_strlen($etc, 'utf8');
            if (!$break_words) {
                $string = preg_replace('/\s+\S+\s*$/su', '', mb_substr($string, 0, $length + 1, 'utf8'));
            }
            return mb_substr($string, 0, $length, 'utf8') . $etc;
        } else {
            return $string;
        }
    }
}

if (!function_exists('gt3_get_pf_type_output')) {
    function gt3_get_pf_type_output($pf, $width, $height, $featured_image){
        $compile = "";
        $id = get_the_ID();
        $alt_text = get_post_meta($id, '_wp_attachment_image_alt', true);

        if (gt3_option('blog_post_fimage_animation')) {
            $featured_standard = '<div class="blog_post_media"><div class="blog_post_media-animate"><img src="' . esc_url(aq_resize($featured_image[0], $width, $height, true, true, true)) . '" alt="'.esc_attr($alt_text).'" /></div></div>';
        }else{
	        $featured_standard = '<div class="blog_post_media"><div class="blog_post_media-wrapper_img"><img src="' . esc_url(aq_resize($featured_image[0], $width, $height, false, true, true)) . '" alt="'.esc_attr($alt_text).'" /></div></div>';
        }

        if (class_exists( 'RWMB_Loader' )) {

            $pf_post_content = $quote_author = $quote_text = $link = $link_text = $pf_post_meta ='';

            switch($pf) {
                case 'gallery':
                    $pf_post_content = rwmb_meta('post_format_gallery_images');
                    $pf_post_meta = get_post_meta($id, 'post_format_gallery_images');
                    break;

                case 'video':
                    $pf_post_content = rwmb_meta('post_format_video_oEmbed', 'type=oembed');
                    $pf_post_meta = get_post_meta($id, 'post_format_video_oEmbed');
                    break;

                case 'audio':
                    $pf_post_content = rwmb_meta('post_format_audio_oEmbed', 'type=oembed');
                    $pf_post_meta = get_post_meta($id, 'post_format_audio_oEmbed');
                    break;

                case 'quote':
                    $quote_author = rwmb_meta('post_format_qoute_author');
                    $quote_author_image = rwmb_meta('post_format_qoute_author_image');
                    if (!empty($quote_author_image)) {
                        $quote_author_image = array_values($quote_author_image);
                        $quote_author_image = $quote_author_image[0];
                        $quote_author_image = $quote_author_image['url'];
                    }else{
                        $quote_author_image = '';
                    }
                    $quote_text = rwmb_meta('post_format_qoute_text');
                    $pf_post_content = $quote_author . $quote_text;
                    break;

                case 'link':
                    $link = rwmb_meta('post_format_link');
                    $link_text = rwmb_meta('post_format_link_text');
                    $pf_post_content = $link . $link_text;
                    break;
            }

            /* Gallery */
            if ($pf == 'gallery' && !empty($pf_post_meta)) {
                if (!empty($pf_post_content)) {
                    if (count($pf_post_content) == 1) {
                        $onlyOneImage = "oneImage";
                    } else {
                        $onlyOneImage = "";
                    }
                    $compile .= '
                    <div class="blog_post_media">
                        <div class="slider-wrapper theme-default ' . $onlyOneImage . '">
                            <div class="slides slick_wrapper">';

                    foreach ($pf_post_content as $image) {
                        $img_url = $image["full_url"];
                        $compile .= "<img src='" . esc_url(aq_resize($img_url, $width, $height, true, true, true)) . "' alt='".esc_attr($alt_text)."' />";
                    }

                    $compile .= '
                            </div>
                        </div>
                    </div>';
                    wp_enqueue_script('jquery-slick');
                }
            /* Video */
            } else if ($pf == 'video' && !empty($pf_post_meta)) {
                $video_autoplay_string = $video_class = $compile_image = '';
                if (strlen($featured_image[0])){
                    $video_class .= ' has_post_thumb';
                    if (is_array($pf_post_meta) && !empty($pf_post_meta[0])) {
                        $video_src = $pf_post_meta[0];
                        if (strpos($pf_post_meta[0], 'vimeo') !== false) {
                            $video_class .= ' vimeo_video';
                            $video_autoplay_string = '?autoplay=1';
                        }elseif(strpos($pf_post_meta[0], 'youtube') !== false){
                            $video_class .= ' youtube_video';
                            $video_autoplay_string = '&autoplay=1';
                        }
                    }

                    $compile_image .= '<div class="gt3_video_wrapper__thumb">';

                        $compile_image .= '<div class="gt3_video__play_image"><img src="' . esc_url($featured_image[0]) . '" alt="'.esc_attr($alt_text).'" /></div>';
                        $compile_image .= '<div class="gt3_video__play_button" data-video-autoplay="'.$video_autoplay_string.'">';
                            $compile_image .= '<svg viewBox="0 0 13 18" width="23" height="30">
                                                   <polygon points="1,1 1,16 11,9" stroke-width="2" />
                                               </svg>';
                        $compile_image .= '</div>';

                    $compile_image .= '</div>';
                }
                $compile .= '<div class="blog_post_media'.esc_attr($video_class).'">' . $compile_image;
                $compile .= strlen($featured_image[0]) ? '<div class="gt3_video__play_iframe">'.$pf_post_content.'</div>' : $pf_post_content;
                $compile .= '</div>';

            /* Audio */
            } else if ($pf == 'audio' && !empty($pf_post_meta)) {
                $compile .= '<div class="blog_post_media">' . $pf_post_content . '</div>';
            /* Quote */
            } else if ($pf == 'quote' && strlen($pf_post_content) > 0) {
                $compile .= '<div class="blog_post_media blog_post_media--quote">' . (strlen($quote_author) && !empty($quote_author_image) ? '<div class="post_media_info">' . (!empty($quote_author_image) ? '<img src="'.esc_url($quote_author_image).'"  class="quote_image" alt="'.esc_attr($alt_text).'" >' : '') . '</div>' : '') . (strlen($quote_text) ? '<div class="quote_text"><a href="' . esc_url(get_permalink()) . '">' . esc_attr($quote_text) . '</a></div>' : '') . '' . (strlen($quote_author) ? '<div class="quote_author">' . esc_attr($quote_author) . '</div>' : '') . '</div>';
            /* Link */
            } else if ($pf == 'link' && strlen($pf_post_content) > 0) {
                $compile .= '<div class="blog_post_media blog_post_media--link"><div class="blog_post_media__link_text blogpost_title">';
                    $compile .= '<a href="' . esc_url(get_permalink()) . '">';
                    if (strlen($link_text) > 0) {
                        $compile .= esc_attr($link_text);
                    } else {
                        $compile .= esc_attr($link);
                    }
                    $compile .= '</a>';
                    if (strlen($link) > 0) {
                        $compile .= '<p><a href="' . esc_url($link) . '">'.esc_attr($link).'</a></p>';
                    }
                $compile .= '</div></div>';
            /* Standard */
            } else {
                $pf = 'standard';
                if (strlen($featured_image[0]) > 0) {
                    $compile .= '' . $featured_standard . '';
                    $pf = 'standard-image';
                }
            }
        } else {
            $pf = 'standard';
            if (strlen($featured_image[0]) > 0) {
                $compile .= '' . $featured_standard . '';
                $pf = 'standard-image';
            }
        }

        $compile = array(
            'content' => $compile,
            'pf' => $pf
        );

        return $compile;
    }
}


if (!function_exists('gt3_get_field_media_and_attach_id')) {
    function gt3_get_field_media_and_attach_id($name, $attach_id, $previewW = "200px", $previewH = null, $classname = ""){
        return "<div class='select_image_root " . $classname . "'>
        <input type='hidden' name='" . esc_attr($name) . "' value='" . esc_attr($attach_id) . "' class='select_img_attachid'>
        <div class='select_img_preview'><img src='" . esc_url(($attach_id > 0 ? aq_resize(wp_get_attachment_url($attach_id), $previewW, $previewH, true, true, true) : "")) . "' alt='" . esc_attr($name) . "'></div>
        <input type='button' class='button button-secondary button-large select_attach_id_from_media_library' value='Select'>
    </div>";
    }
}

function gt3_setup_theme(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('revisions');
    add_theme_support('post-formats', array('gallery', 'video', 'quote', 'audio', 'link'));
    add_theme_support( 'custom-background' );
    add_theme_support( 'align-wide' );
}
add_action('after_setup_theme', 'gt3_setup_theme');

require_once(get_template_directory() . "/core/loader.php");

add_action('init', 'gt3_page_init');
if (!function_exists('gt3_page_init')) {
    function gt3_page_init(){
        add_post_type_support('page', 'excerpt');
    }
}

/// Post Page Settings //

/*Work with options*/
if (!function_exists('gt3pb_get_option')) {
    function gt3pb_get_option($optionname, $defaultValue = ""){
        $returnedValue = get_option("gt3pb_" . $optionname, $defaultValue);

        if (gettype($returnedValue) == "string") {
            return stripslashes($returnedValue);
        } else {
            return $returnedValue;
        }
    }
}

if (!function_exists('gt3pb_delete_option')) {
    function gt3pb_delete_option($optionname){
        return delete_option("gt3pb_" . $optionname);
    }
}

if (!function_exists('gt3pb_update_option')) {
    function gt3pb_update_option($optionname, $optionvalue){
        if (update_option("gt3pb_" . $optionname, $optionvalue)) {
            return true;
        }
    }
}

add_action('wp_footer','gt3_wp_footer');
function gt3_wp_footer() {
    echo gt3_get_theme_option("code_before_body");
}


if (!function_exists('gt3_get_image_bg')) {
    function gt3_get_image_bg($gt3_img_src, $gt3_is_grid) {
        if (isset($gt3_is_grid) && $gt3_is_grid == 'yes') {
            echo "<div class='fullscreen_block fw_background bg_image grid_background image_video_bg_block' data-bg='" . esc_url($gt3_img_src) . "'></div>";
        } else {
            echo "<div class='fullscreen_block fw_background bg_image image_video_bg_block' data-bg='" . esc_url($gt3_img_src) . "'></div>";
        }
    }
}
if (!function_exists('gt3_get_color_bg')) {
    function gt3_get_color_bg($gt3_bg_color) {
        echo "<div class='fullscreen_block fw_background bg_color grid_background' data-bgcolor='" . esc_attr($gt3_bg_color) . "'></div>";
    }
}

if (!function_exists('gt3_page_title')) {
    function gt3_page_title(){
        $title = '';

	    if ( class_exists( 'WooCommerce' ) && is_product() ) {
		    $title = wp_kses_post( get_the_title() );
	    } elseif ( class_exists( 'WooCommerce' ) && is_product_category() ) {
		    $title = single_cat_title( '', false );
	    } elseif ( class_exists( 'WooCommerce' ) && is_product_tag() ) {
		    $title = single_term_title( "", false );
	    } elseif ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
		    $title = woocommerce_page_title( false );
	    } elseif (is_category()) {
            $title = single_cat_title('', false);
        }elseif (is_tag()) {
            $title = single_term_title("", false).esc_html__(' Tag', 'agrosector');
        }elseif (is_date()) {
            $title = get_the_time('F Y');
        }elseif(is_author()){
            $title = esc_html__('Author:', 'agrosector') . " " . esc_html(get_the_author());
        }elseif (is_search()) {
            $title = esc_html__( 'Search Results for: ', 'agrosector' ). '<span>' . esc_html(get_search_query()) . '</span>';
        }elseif (is_404()) {
            $title = '';
        }elseif (is_archive()) {
            $title = esc_html__('Archive','agrosector');
        }elseif(is_home() || is_front_page()){
            $gt3_ID = gt3_get_queried_object_id();
            $title = get_the_title($gt3_ID);
        }else{
            global $post;
            if (!empty($post)) {
                $id = $post->ID;
                if ( is_sticky() ) {
                    $title = '<i class="fa fa-thumb-tack"></i>'.get_the_title($id);
                }else{
                    $title = get_the_title($id);
                }
            }else{
                $title = esc_html__('No Posts','agrosector');
            }
        }

        return $title;
    }
}


function gt3_the_breadcrumb(){
    $delimiter = '<span class="gt3_pagination_delimiter"></span>';
    $home = esc_html__('Home', 'agrosector');
    $showCurrent = 1;
    $before = '<span class="current">';
    $after = '</span>';
    global $post;
    $homeLink = esc_url(home_url('/'));
    if(is_front_page() && !is_home()) {
        echo '<div class="breadcrumbs">' . $home . '</div>';
    } elseif ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
	    echo '<div class="breadcrumbs">';
	    woocommerce_breadcrumb();
	    echo '</div>';
    } else {
        echo '<div class="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a>' . $delimiter . '';
        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
            echo wp_kses_post($before) . esc_html__('Archive','agrosector').' "' . single_cat_title('', false) . '"' . wp_kses_post($after);

        } elseif (get_post_type() == 'port') {
            the_terms($post->ID, 'portcat', '', '', '');
            if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

        } elseif (is_search()) {
            echo  wp_kses_post($before) . esc_html__('Search for','agrosector').' "' . esc_html(get_search_query()) . '"' . wp_kses_post($after);

        } elseif (is_day()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . $delimiter . ' ';
            echo '<a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a> ' . $delimiter . ' ';
            echo  wp_kses_post($before) . esc_html(get_the_time('d')) . wp_kses_post($after);

        } elseif (is_month()) {
            echo '<a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a> ' . $delimiter . ' ';
            echo  wp_kses_post($before) . esc_html(get_the_time('F')) . wp_kses_post($after);

        } elseif (is_year()) {
            echo  wp_kses_post($before) . esc_html(get_the_time('Y')) . wp_kses_post($after);

        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $parent_id = $post->post_parent;
                if ($parent_id > 0) {
                    $breadcrumbs = array();
                    while ($parent_id) {
                        $page = get_page($parent_id);
                        $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a>';
                        $parent_id = $page->post_parent;
                    }
                    $breadcrumbs = array_reverse($breadcrumbs);
                    for ($i = 0; $i < count($breadcrumbs); $i++) {
                        echo (($breadcrumbs[$i]));
                        if ($i != count($breadcrumbs) - 1) echo ' ' . $delimiter . ' ';
                    }
                    if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                } else {
                    echo wp_kses_post($before) . get_the_title() . wp_kses_post($after);
                }

            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                echo (($cats));
                if ($showCurrent == 1) echo  wp_kses_post($before) . get_the_title() . wp_kses_post($after);
            }

        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            echo  wp_kses_post($before) . esc_html($post_type->labels->singular_name) . wp_kses_post($after);
        } elseif (is_attachment()) {
            if ($showCurrent == 1) echo ' ' . $before . get_the_title() . $after;

        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1) echo  wp_kses_post($before) . get_the_title() . wp_kses_post($after);

        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo (($breadcrumbs[$i]));
                if ($i != count($breadcrumbs) - 1) echo ' ' . $delimiter . ' ';
            }
            if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

        } elseif (is_tag()) {
            echo  wp_kses_post($before) . esc_html__('Tag','agrosector').' "' . single_tag_title('', false) . '"' . wp_kses_post($after);

        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo  wp_kses_post($before) . esc_html__('Author','agrosector').' ' . esc_html($userdata->display_name) . wp_kses_post($after);

        } elseif (is_404()) {
            echo  wp_kses_post($before) . esc_html__('Error 404','agrosector') . wp_kses_post($after);

        } elseif ( is_home() && is_front_page() ) {
            $title = esc_html__('Blog', 'agrosector');
            echo  wp_kses_post($before) . $title . wp_kses_post($after);

        } elseif (is_home() || is_front_page()) {
            $gt3_ID = gt3_get_queried_object_id();
            $title = esc_html(get_the_title($gt3_ID));
            echo  wp_kses_post($before) . $title . wp_kses_post($after);
        }

        echo '</div>';
    }
}

if (!function_exists('gt3_preloader')) {
    function gt3_preloader(){
	    $id = gt3_get_queried_object_id();
    	$post_loader =  (class_exists( 'RWMB_Loader' ) && $id !== 0);
    	$mb_preloader = $post_loader ? rwmb_meta('mb_preloader', array(), $id) : false;
        if ($mb_preloader == 'none') return;
        if (gt3_option('preloader') == '1' || gt3_option('preloader') == true || $mb_preloader == 'custom') {
            $preloader_type        = gt3_option('preloader_type');
            $preloader_background  = gt3_option('preloader_background');
            $preloader_item_color  = gt3_option('preloader_item_color');
            $preloader_item_color2 = gt3_option('preloader_item_color2');
            $preloader_logo        = gt3_option('preloader_item_logo');
            $preloader_logo_cont_w = gt3_option('preloader_item_logo_width');
            $preloader_item_width  = gt3_option('preloader_item_width');
            $preloader_item_stroke = gt3_option('preloader_item_stroke');
            $preloader_full        = gt3_option('preloader_full');

            $preloader_logo_url = $preloader_logo['url'];
            $preloader_logo_width = $preloader_logo['width'];

            if ($post_loader && $mb_preloader == 'custom' ) {
            	$preloader_type         = rwmb_meta('mb_preloader_type', array(), $id);
                $preloader_background   = rwmb_meta('mb_preloader_background', array(), $id);
                $preloader_item_color   = rwmb_meta('mb_preloader_item_color', array(), $id);
                $preloader_item_color2  = rwmb_meta('mb_preloader_item_color2', array(), $id);
                $mb_preloader_item_logo = rwmb_meta('mb_preloader_item_logo', 'size=full', $id);
                if (!empty($mb_preloader_item_logo)) {
                    $preloader_logo_src   = array_values($mb_preloader_item_logo);
                    $preloader_logo_url   = $preloader_logo_src[0]['full_url'];
                    $preloader_logo_width = $preloader_logo_src[0]['width'];
                }else{
                    $preloader_logo_url = '';
                }
                $preloader_logo_cont_w['width'] = rwmb_meta('mb_preloader_item_logo_width', array(), $id).'px';
                $preloader_item_width['width'] = rwmb_meta('mb_preloader_item_width', array(), $id);
                $preloader_item_stroke['width'] = rwmb_meta('mb_preloader_item_stroke', array(), $id);
                $preloader_full = rwmb_meta('mb_preloader_full', array(), $id);
            }

            $preloader_background  = !empty($preloader_background)  ? $preloader_background  : '#ffffff';
            $preloader_item_color  = !empty($preloader_item_color)  ? $preloader_item_color  : '#808080';
            $preloader_item_color2 = !empty($preloader_item_color2) ? $preloader_item_color2 : '#e94e76';

            $preloader_class = $preloader_full == '1' ? ' gt3_preloader_full' : '';
            $preloader_class .= !empty($preloader_logo_url) ? ' gt3_preloader_image_on' : '';

            if ( $preloader_type == 'linear' ) {
                $preldr_linear_style = 'background-color:'.$preloader_item_color.';color:'.$preloader_item_color2.';';

                echo '<div class="gt3_preloader gt3_linear-loading'.esc_attr($preloader_class).'" style="background-color:'.esc_attr($preloader_background).';" data-loading_type="linear">';
                    echo '<div class="gt3_linear-loading-center">';
                        echo '<div class="gt3_linear-loading-center-absolute">';
                            if (!empty($preloader_logo_url)) {
                                echo '<img style="width:'.esc_attr((int)$preloader_logo_width/2).'px;height: auto;" src="'.esc_url($preloader_logo_url).'" alt="'.esc_attr__('preloader', 'agrosector').'">';
                            }
                            echo '<div class="gt3_linear-object gt3_linear-object_one" style="'.esc_attr($preldr_linear_style).'"></div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }else if ($preloader_type == 'circle') {
                $preldr_width     = !empty($preloader_item_width['width']) ? (int)$preloader_item_width['width'] : '200';
                $preldr_str_width = !empty($preloader_item_stroke['width']) ? (int)$preloader_item_stroke['width'] : '2';
                $preldr_circle_hp = $preldr_width / 2;
                $preldr_circle_xy = $preldr_circle_hp * 0.9;
                $preldr_circle_r  = $preldr_circle_hp * 0.8;
                $preldr_circle_l  = 2* pi() * $preldr_circle_r;

                $preldr_circle_style  = 'stroke:'.$preloader_item_color.'; stroke-dasharray: '.(float)$preldr_circle_l.'; stroke-width: '.(int)$preldr_str_width;
                $preldr_circle_style2 = 'stroke:'.$preloader_item_color2.'; stroke-dasharray: '.(float)$preldr_circle_l.'; stroke-width: '.(int)$preldr_str_width;

                $preldr_circle_logo_cont_style = 'width:'.$preloader_logo_cont_w['width'].';';

                echo '<div class="gt3_preloader gt3_circle-overlay'.esc_attr($preloader_class).'" style="background-color:'.esc_attr($preloader_background).';" data-loading_type="circle" data-circle_l="'.(int)$preldr_circle_l.'">';
                    echo '<div>';
                        echo '<div class="gt3_circle-preloader" style="width:'.(int)$preldr_width.'px; height:'.(int)$preldr_width.'px;">';
                            echo '<svg width="'.(int)$preldr_width.'" height="'.(int)$preldr_width.'">';
                                echo '<circle class="gt3_circle-background" cx="'.(int)$preldr_circle_xy.'" cy="'.(int)$preldr_circle_xy.'" r="'.(int)$preldr_circle_r.'" transform="rotate(-90, '.(int)$preldr_circle_hp.', '.(int)$preldr_circle_xy.')" style="'.esc_attr($preldr_circle_style).'" />';
                                echo '<circle class="gt3_circle-outer" cx="'.(int)$preldr_circle_xy.'" cy="'.(int)$preldr_circle_xy.'" r="'.(int)$preldr_circle_r.'" transform="rotate(-90, '.(int)$preldr_circle_hp.', '.(int)$preldr_circle_xy.')" style="'.esc_attr($preldr_circle_style2).'"/>';
                            echo '</svg>';
                            echo '<span class="gt3_circle-background"></span>';
                            echo '<span class="gt3_circle-logo gt3_circle-animated gt3_circle-fade_in" style="'.esc_attr($preldr_circle_logo_cont_style).'">';

                            if (!empty($preloader_logo_url)) {
                                echo '<img style="width:'.esc_attr((int)$preloader_logo_width/2).'px;height: auto;" src="'.esc_url($preloader_logo_url).'" alt="'.esc_attr__('preloader', 'agrosector').'">';
                            }
                            echo '</span>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            } else {
                $preldr_width     = !empty($preloader_item_width['width']) ? (int)$preloader_item_width['width'] : '200';
                $preldr_str_width = !empty($preloader_item_stroke['width']) ? (int)$preloader_item_stroke['width'] : '2';
                $preldr_circle_hp = $preldr_width / 2;
                $preldr_circle_l  = round(2* pi() * $preldr_circle_hp,0);
                $preldr_dashoffset = round($preldr_circle_l/4, 0);

                $preldr_circle_style  = 'stroke:'.$preloader_item_color2.'; stroke-dasharray: '.(float)$preldr_circle_l.'; stroke-width: '.(int)$preldr_str_width.'; stroke-dashoffset: '.$preldr_dashoffset.';';

                $preldr_circle_logo_cont_style = 'width:'.$preloader_logo_cont_w['width'].';';

                echo '<div class="gt3_preloader gt3_theme_prl-loading gt3_theme_prl-overlay'.esc_attr($preloader_class).'" style="background-color:'.esc_attr($preloader_background).';" data-loading_type="theme" data-circle_l="'.(int)$preldr_circle_l.'">';
                    echo '<div>';
                        echo '<div class="gt3_theme_prl-preloader" style="width:'.(int)$preldr_width.'px; height:'.(int)$preldr_width.'px;">';
                            echo '<svg width="'.(int)$preldr_width.'" height="'.(int)$preldr_width.'">';
                                echo '<circle class="gt3_theme_prl-background" cx="'.(int)$preldr_circle_hp.'" cy="'.(int)$preldr_circle_hp.'" r="'.(int)$preldr_circle_hp.'" transform="rotate(-90, '.(int)$preldr_circle_hp.', '.(int)$preldr_circle_hp.')" style="'.esc_attr($preldr_circle_style).'" />';

                            echo '</svg>';
                            echo '<span class="gt3_circle-background"></span>';
                            echo '<span class="gt3_theme_prl-logo gt3_theme_prl-animated gt3_theme_prl-fade_in" style="'.esc_attr($preldr_circle_logo_cont_style).'">';

                                if (!empty($preloader_logo_url)) {
                                    echo '<img style="width:'.esc_attr((int)$preloader_logo_width/2).'px;height: auto;" src="'.esc_url($preloader_logo_url).'" alt="'.esc_attr__('preloader', 'agrosector').'">';
                                }
                            echo '</span>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';

            }
        }
    }
}


if (!function_exists('gt3_get_page_title')) {
    function gt3_get_page_title($id) {
        $page_title_top_border = gt3_option("page_title_top_border");
        $page_title_top_border_color = gt3_option("page_title_top_border_color");

        $page_title_bottom_border = gt3_option("page_title_bottom_border");
        $page_title_bottom_border_color = gt3_option("page_title_bottom_border_color");

        $page_title_conditional = ((gt3_option('page_title_conditional') == '1' || gt3_option('page_title_conditional') == true)) ? 'yes' : 'no';
        $blog_title_conditional = ((gt3_option('blog_title_conditional') == '1' || gt3_option('blog_title_conditional') == true)) ? 'yes' : 'no';
        $team_title_conditional = ((gt3_option('team_title_conditional') == '1' || gt3_option('team_title_conditional') == true)) ? 'yes' : 'no';
        $portfolio_title_conditional = ((gt3_option('portfolio_title_conditional') == '1' || gt3_option('portfolio_title_conditional') == true)) ? 'yes' : 'no';
        $project_title_conditional = ((gt3_option('project_title_conditional') == '1' || gt3_option('project_title_conditional') == true)) ? 'yes' : 'no';

        $product_title_conditional = ((gt3_option('product_title_conditional') == '1' || gt3_option('product_title_conditional') == true)) ? 'yes' : 'no';
        $shop_cat_title_conditional = ((gt3_option('shop_cat_title_conditional') == '1' || gt3_option('shop_cat_title_conditional') == true)) ? 'yes' : 'no';

        if (is_singular('post') && $page_title_conditional == 'yes' && $blog_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        if (is_singular('team') && $page_title_conditional == 'yes' && $team_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        if (is_singular('portfolio') && $page_title_conditional == 'yes' && $portfolio_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        if (is_singular('project') && $page_title_conditional == 'yes' && $project_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        if (is_singular('product') && $page_title_conditional == 'yes' && $product_title_conditional == 'no') {
            $page_title_conditional = 'no';
        } elseif(is_singular('product') && $page_title_conditional == 'yes' && $product_title_conditional == 'yes'){
	        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
        }
        if ( class_exists('WooCommerce') && is_product_category() && $page_title_conditional == 'yes' && $shop_cat_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        $page_title_bottom_margin = gt3_option("page_title_bottom_margin");
        $page_title_bottom_margin = !empty($page_title_bottom_margin['margin-bottom']) ? (int)$page_title_bottom_margin['margin-bottom'] : '';

	    $page_title_svg_line = gt3_option("page_title_svg_line");
	    $page_title_svg_line_top_color = gt3_option("page_title_svg_line_top_color");
	    $page_title_svg_line_bottom_color = gt3_option("page_title_svg_line_bottom_color");


        if ($page_title_conditional == 'yes') {
            $page_title_breadcrumbs_conditional = gt3_option("page_title_breadcrumbs_conditional") == '1' ? 'yes' : 'no';
            $page_title_vert_align = gt3_option("page_title_vert_align");
            $page_title_horiz_align = gt3_option("page_title_horiz_align");
            $page_title_font_color = gt3_option("page_title_font_color");
            $page_title_bg_color = gt3_option("page_title_bg_color");
            $page_title_bg_image_array = gt3_option("page_title_bg_image");
            $page_title_height = gt3_option("page_title_height");
            $page_title_height = $page_title_height['height'];
            $header_height = gt3_option('header_height');
            $header_height = $header_height['height'];

            if (gt3_option('header_on_bg') == '1') {
                if (gt3_option('top_header_bar_left') == '1' || gt3_option('top_header_bar_right') == '1') {
                    $top_header_height = 40;
                }else{
                    $top_header_height = 0;
                }
                $header_height = gt3_option("header_height");
                $page_title_top_padding = !empty($header_height['height']) ? 20 + (int)$header_height['height'] + (int)$top_header_height : '';
            }else{
                $page_title_top_padding = '';
            }
        }

        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $page_sub_title = rwmb_meta('mb_page_sub_title', array(), $id);
            $mb_page_title_conditional = rwmb_meta('mb_page_title_conditional', array(), $id);
            $mb_page_title_use_feature_image = rwmb_meta('mb_page_title_use_feature_image', array(), $id);
            if ($mb_page_title_conditional == 'yes') {
                $page_title_conditional = 'yes';
                $page_title_breadcrumbs_conditional = rwmb_meta('mb_show_breadcrumbs', array(), $id) == '1' ? 'yes' : 'no';
                $page_title_vert_align = rwmb_meta('mb_page_title_vertical_align', array(), $id);
                $page_title_horiz_align = rwmb_meta('mb_page_title_horizontal_align', array(), $id);
                $page_title_font_color = rwmb_meta('mb_page_title_font_color', array(), $id);
                $page_title_bg_color = rwmb_meta('mb_page_title_bg_color', array(), $id);
                $page_title_height = rwmb_meta('mb_page_title_height', array(), $id);

                $page_title_top_border = rwmb_meta("mb_page_title_top_border", array(), $id);
                $mb_page_title_top_border_color = rwmb_meta("mb_page_title_top_border_color", array(), $id);
                $mb_page_title_top_border_color_opacity = rwmb_meta("mb_page_title_top_border_color_opacity", array(), $id);

                if (!empty($mb_page_title_top_border_color) && $page_title_top_border == '1') {
                    $page_title_top_border_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_page_title_top_border_color)).','.$mb_page_title_top_border_color_opacity.')';
                }else{
                    $page_title_top_border_color = '';
                }

                $page_title_bottom_border = rwmb_meta("mb_page_title_bottom_border", array(), $id);
                $mb_page_title_bottom_border_color = rwmb_meta("mb_page_title_bottom_border_color", array(), $id);
                $mb_page_title_bottom_border_color_opacity = rwmb_meta("mb_page_title_bottom_border_color_opacity", array(), $id);

                if (!empty($mb_page_title_bottom_border_color) && $page_title_bottom_border == '1') {
                    $page_title_bottom_border_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_page_title_bottom_border_color)).','.$mb_page_title_bottom_border_color_opacity.')';
                }else{
                    $page_title_bottom_border_color = '';
                }

                $page_title_bottom_margin = rwmb_meta("mb_page_title_bottom_margin", array(), $id);

                $page_title_svg_line = rwmb_meta("mb_page_title_svg_line", array(), $id);
	            $mb_page_title_svg_line_top_color = rwmb_meta("mb_page_title_svg_line_top_color", array(), $id);
	            $mb_page_title_svg_line_top_color_opacity = rwmb_meta("mb_page_title_svg_line_top_color_opacity", array(), $id);
	            $mb_page_title_svg_line_bottom_color = rwmb_meta("mb_page_title_svg_line_bottom_color", array(), $id);
	            $mb_page_title_svg_line_bottom_color_opacity = rwmb_meta("mb_page_title_svg_line_bottom_color_opacity", array(), $id);
	            if ( !empty($mb_page_title_svg_line_top_color) && ($page_title_svg_line == 'svg_line_top' || $page_title_svg_line == 'svg_line_both') ) {
		            $page_title_svg_line_top_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_page_title_svg_line_top_color)).','.$mb_page_title_svg_line_top_color_opacity.')';
	            }
	            if ( !empty($mb_page_title_svg_line_bottom_color) && ($page_title_svg_line == 'svg_line_bottom' || $page_title_svg_line == 'svg_line_both') ) {
		            $page_title_svg_line_bottom_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_page_title_svg_line_bottom_color)).','.$mb_page_title_svg_line_bottom_color_opacity.')';
	            }

            }elseif($mb_page_title_conditional == 'no'){
                $page_title_conditional = 'no';
            }
        }

	    $gt3_page_title = is_home() && is_front_page() ? esc_html__('Blog', 'agrosector') : gt3_page_title();

        if ($page_title_conditional == 'yes' && !empty($gt3_page_title)) {

            $page_title_classes = !empty($page_title_horiz_align) ? ' gt3-page-title_horiz_align_'.esc_attr($page_title_horiz_align) : ' gt3-page-title_horiz_align_left';
            $page_title_classes .= !empty($page_title_vert_align) ? ' gt3-page-title_vert_align_'.esc_attr($page_title_vert_align) : ' gt3-page-title_vert_align_middle';
            $page_title_classes .= !empty($page_title_height) && (int)$page_title_height < 80 ? ' gt3-page-title_small_header' : '';

            $page_title_styles = !empty($page_title_bg_color) ? 'background-color:'.esc_attr($page_title_bg_color).';' : '';
            $page_title_styles .= !empty($page_title_top_padding) ? 'padding-top:'.esc_attr($page_title_top_padding).'px;' : '';
            $page_title_styles .= !empty($page_title_height) ? 'height:'.esc_attr($page_title_height).'px;' : '';
            $page_title_styles .= !empty($page_title_font_color) ? 'color:'.esc_attr($page_title_font_color).';' : '';
            $page_title_styles .= !empty($page_title_bottom_margin) ? 'margin-bottom:'.esc_attr($page_title_bottom_margin).'px;' : '';

            if ($page_title_top_border == '1') {
                $page_title_styles .= !empty($page_title_top_border_color['rgba']) ? 'border-top: 1px solid '.esc_attr($page_title_top_border_color['rgba']).';' : '';
            }

            if ($page_title_bottom_border == '1') {
                $page_title_styles .= !empty($page_title_bottom_border_color['rgba']) ? 'border-bottom: 1px solid '.esc_attr($page_title_bottom_border_color['rgba']).';' : '';
            }

            if( $page_title_svg_line !== 'svg_none' ){
	            $svg = '<svg version="1.1" width="100%" height="6.214396685655101" preserveAspectRatio="none" viewBox="0 0 400 6.214396685655101" xmlns="http://www.w3.org/2000/svg"><path d="M0.000 1.417 L 0.000 2.833 1.300 2.833 C 2.015 2.833,2.671 2.898,2.758 2.977 C 3.332 3.499,10.962 3.531,11.135 3.012 C 11.217 2.764,11.790 2.787,11.875 3.042 C 11.977 3.349,18.863 3.321,19.236 3.012 C 19.447 2.836,19.550 2.834,19.750 3.000 C 19.937 3.155,20.057 3.160,20.226 3.020 C 20.529 2.769,38.312 2.746,38.563 2.996 C 38.784 3.218,42.280 3.221,42.417 3.000 C 42.473 2.908,42.961 2.833,43.500 2.833 C 44.039 2.833,44.527 2.908,44.583 3.000 C 44.710 3.205,47.372 3.223,47.592 3.021 C 47.890 2.745,52.957 2.664,53.179 2.931 C 53.396 3.194,55.086 3.266,55.238 3.019 C 55.349 2.840,56.165 2.869,56.473 3.064 C 56.604 3.146,56.772 3.128,56.881 3.019 C 57.112 2.788,58.667 2.772,58.667 3.000 C 58.667 3.220,60.051 3.216,60.272 2.995 C 60.635 2.631,63.899 2.513,68.417 2.701 C 69.975 2.766,71.381 2.822,71.542 2.826 C 71.740 2.831,71.833 2.967,71.833 3.250 C 71.833 3.600,71.912 3.667,72.323 3.667 C 72.593 3.667,72.860 3.742,72.917 3.833 C 73.043 4.038,75.206 4.056,75.425 3.854 C 75.725 3.578,81.288 3.495,81.511 3.763 C 81.736 4.035,83.092 4.089,83.250 3.833 C 83.387 3.612,86.833 3.612,86.833 3.833 C 86.833 4.050,88.216 4.050,88.433 3.833 C 88.791 3.476,91.119 3.370,96.417 3.471 C 100.882 3.557,101.337 3.536,101.227 3.251 C 101.084 2.878,101.596 2.765,102.034 3.073 C 102.452 3.368,107.710 3.285,108.101 2.977 C 108.382 2.757,111.826 2.759,112.047 2.980 C 112.289 3.222,113.545 3.222,113.787 2.979 C 114.102 2.665,116.443 2.699,116.700 3.022 C 116.874 3.241,117.468 3.327,119.744 3.463 C 121.516 3.569,122.595 3.701,122.633 3.816 C 122.725 4.092,128.864 4.069,129.143 3.792 C 129.431 3.505,132.426 3.565,132.789 3.865 C 132.998 4.037,133.069 4.039,133.124 3.875 C 133.208 3.627,133.936 3.595,134.083 3.833 C 134.212 4.041,138.610 4.059,138.738 3.852 C 138.960 3.494,146.657 3.395,160.167 3.576 C 162.688 3.610,166.430 3.570,168.484 3.487 C 170.538 3.404,172.301 3.399,172.401 3.477 C 172.601 3.632,179.150 3.567,180.750 3.394 C 182.029 3.256,184.216 3.238,187.616 3.337 C 190.086 3.410,190.323 3.392,190.393 3.125 C 190.478 2.798,191.894 2.693,192.083 3.000 C 192.218 3.217,194.949 3.217,195.083 3.000 C 195.225 2.772,196.056 2.790,196.293 3.026 C 196.428 3.161,196.551 3.167,196.701 3.046 C 197.120 2.710,202.614 2.594,203.098 2.911 C 203.431 3.129,203.574 3.146,203.761 2.991 C 203.946 2.837,204.057 2.840,204.254 3.003 C 204.642 3.325,212.167 3.382,212.167 3.063 C 212.167 2.767,213.514 2.747,213.805 3.039 C 214.062 3.296,233.043 3.303,233.376 3.046 C 233.601 2.872,234.265 2.876,234.667 3.053 C 234.804 3.114,237.279 3.202,240.167 3.250 C 243.054 3.297,246.729 3.369,248.333 3.409 C 249.938 3.450,251.911 3.410,252.720 3.322 C 253.684 3.216,254.543 3.218,255.220 3.328 C 256.735 3.574,261.768 3.665,261.983 3.451 C 262.079 3.354,262.987 3.241,263.999 3.199 C 265.012 3.158,265.881 3.058,265.930 2.978 C 265.979 2.899,266.423 2.833,266.917 2.833 C 267.410 2.833,267.856 2.764,267.908 2.680 C 268.036 2.473,273.719 2.482,278.310 2.696 C 281.233 2.833,282.120 2.826,282.424 2.663 C 282.833 2.444,283.422 2.555,283.258 2.821 C 283.059 3.143,283.631 3.216,286.803 3.272 C 288.595 3.304,290.478 3.400,290.989 3.487 C 292.214 3.695,295.784 3.598,296.417 3.340 C 296.763 3.198,297.250 3.176,298.000 3.268 C 298.596 3.341,299.440 3.395,299.875 3.388 C 300.310 3.381,301.360 3.362,302.208 3.346 C 303.056 3.330,304.961 3.392,306.441 3.485 C 308.266 3.599,309.472 3.599,310.188 3.484 C 310.822 3.382,311.264 3.375,311.295 3.467 C 311.325 3.558,312.879 3.579,315.131 3.518 C 321.308 3.353,323.836 3.436,324.219 3.819 C 324.446 4.046,325.833 4.059,325.833 3.833 C 325.833 3.611,329.446 3.611,329.583 3.833 C 329.742 4.089,331.097 4.035,331.322 3.763 C 331.545 3.495,337.109 3.578,337.408 3.854 C 337.627 4.056,339.623 4.038,339.750 3.833 C 339.884 3.616,342.282 3.616,342.417 3.833 C 342.561 4.067,345.553 4.047,345.789 3.811 C 346.077 3.523,364.113 3.582,364.464 3.872 C 364.791 4.142,372.286 4.114,372.559 3.841 C 372.800 3.600,373.722 3.622,373.975 3.875 C 374.319 4.220,380.836 4.273,381.511 3.936 C 381.871 3.757,382.788 3.642,384.474 3.564 C 386.461 3.473,386.999 3.395,387.359 3.143 C 387.865 2.789,388.834 2.734,389.141 3.041 C 389.432 3.332,392.546 3.308,392.903 3.011 C 393.086 2.860,393.231 2.845,393.375 2.962 C 393.490 3.056,394.633 3.179,395.917 3.237 C 397.200 3.295,398.644 3.383,399.125 3.433 L 400.000 3.524 400.000 1.762 L 400.000 0.000 200.000 0.000 L 0.000 0.000 0.000 1.417 " stroke="none" fill-rule="evenodd" fill="currentColor"></path></svg>';

	            $svg_top = '<div class="page_title_svg_line top" style="color:'.(!empty($page_title_svg_line_top_color['rgba']) ? esc_attr($page_title_svg_line_top_color['rgba']) : '#ffffff').'">'.apply_filters('page_title_svg_line_top', $svg).'</div>';
	            $svg_bottom = '<div class="page_title_svg_line bottom" style="color:'.(!empty($page_title_svg_line_bottom_color['rgba']) ? esc_attr($page_title_svg_line_bottom_color['rgba']) : '#ffffff').'">'.apply_filters('page_title_svg_line_bottom', $svg).'</div>';
            }

            $title_background = gt3_background_render('page_title','mb_page_title_conditional','yes',true);
            $bg_src = !empty($image_array['background-image']) ? $image_array['background-image'] : '';
            if (!empty($title_background) && is_array($title_background) && gt3_get_queried_object_id() !== 0 && !empty($mb_page_title_use_feature_image) && (bool)$mb_page_title_use_feature_image) {

                if (!empty($mb_page_title_conditional) && $mb_page_title_conditional == 'yes') {
                    $title_background = gt3_background_render('page_title','mb_page_title_use_feature_image','1',true, true);
                }

                $bg_src = get_the_post_thumbnail_url(gt3_get_queried_object_id(),'full');
                $title_background['background-image'] = 'background-image:url('.esc_url($bg_src).');';
            }
            $title_background = implode('', $title_background);

            $page_title_classes .= !empty($title_background) ? ' gt3-page-title_has_img_bg' : '';

            $page_title_styles .= $title_background;

            $page_title_classes .= ($page_title_bg_color == '#fff' || $page_title_bg_color == '#ffffff') && empty($title_background) ? ' gt3-page-title_default_color_a' : '';

            $image_array = gt3_option("page_title_bg_image");

            if (class_exists( 'RWMB_Loader' ) && gt3_get_queried_object_id() !== 0) {
                if ('mb_page_title_conditional' != false) {
                    $mb_conditional = rwmb_meta('mb_page_title_conditional', array(), $id);
                    if ($mb_conditional == 'yes') {
                        $bg_src = rwmb_meta('mb_page_title_bg_image', array(), $id);
                        $bg_src = !empty($bg_src) ? $bg_src : '';
                        if (!empty($bg_src)) {
                            $bg_src = array_values($bg_src);
                            $bg_src = $bg_src[0]['url'];
                        }
                    }
                }
            }
            $page_title_fill = $page_fill_inner_class = '';
            if (!empty($bg_src)) {
                $page_title_fill_color = getSolidColorFromImage(esc_url($bg_src));
                $page_title_fill = "<div class='gt3-page-title-fill' style='background-color:#".esc_attr($page_title_fill_color).";'></div>";
                $page_fill_inner_class = 'has_fill_inner';
            }

            echo '<div class="gt3-page-title_wrapper">';
                echo "<div class='gt3-page-title". (!empty($page_title_classes) ? esc_attr($page_title_classes) : '' ) ."'".( !empty($page_title_styles) ? ' style="'.esc_attr($page_title_styles).'"' : '').">";
                    if ( ! empty( $svg_top ) && ( $page_title_svg_line == 'svg_line_top' || $page_title_svg_line == 'svg_line_both' ) ) {
                        echo '' . $svg_top;
                    }
                    echo (($page_title_fill))."<div class='gt3-page-title__inner ".esc_attr($page_fill_inner_class)."'>";
                        echo "<div class='container'>";
                            echo "<div class='gt3-page-title__content'>";
                                echo "<div class='page_title'>";

                                if (!empty($page_sub_title) && $page_title_horiz_align != 'center') {
                                    echo "<div class='page_sub_title'><div>";
                                    echo esc_attr( $page_sub_title );
                                    echo "</div></div>";
                                }

                                echo "<h1>";
                                    echo $gt3_page_title;
                                echo "</h1>";

                                echo "</div>";
                                if (!empty($page_sub_title) && $page_title_horiz_align == 'center') {
                                    echo "<div class='page_sub_title'><div>";
                                        echo esc_attr( $page_sub_title );
                                    echo "</div></div>";
                                }
                                if ($page_title_breadcrumbs_conditional == 'yes') {
                                    echo "<div class='gt3_breadcrumb'>";
                                    gt3_the_breadcrumb();
                                    echo "</div>";
                                }
                                if (is_single() && get_post_type() == 'post') {
	                                $comments_text = get_comments_number( get_the_ID() ) == 1 ? esc_html__( 'comment', 'agrosector' ) : esc_html__( 'comments', 'agrosector' );
                                    $post = get_post(get_the_ID());
                                    ?>
                                        <div class='page_title_meta'>
                                            <span class='post_date'><?php echo esc_html(get_the_time(get_option( 'date_format' ))); ?></span>
                                            <span class='post_author'><?php esc_html_e('by', 'agrosector'); ?> <a href='<?php echo esc_url(get_author_posts_url($post->post_author)); ?>'><?php echo (get_the_author_meta('display_name', $post->post_author)); ?></a></span>

                                            <?php if (is_single() && get_post_type() == 'post') { ?>
                                                <span class='post_cats'>
                                                <?php the_category(', '); ?>
                                                </span>
                                            <?php }; ?>

                                            <?php if((int)get_comments_number(get_the_ID()) != 0 ){ ?>
                                                <span class='post_comments'><a href='<?php echo esc_url(get_comments_link()); ?>'><?php echo esc_html(get_comments_number(get_the_ID())) . ' <span>'.$comments_text.'</span>'; ?></a></span>
                                            <?php }; ?>
                                        </div>
                                        <?php
                                } else if (is_single() && get_post_type() == 'portfolio') {
                                    $item_category = '';
                                    $categories = get_the_terms(get_the_ID(), 'portfolio_category');
                                    if(!$categories || is_wp_error($categories)) {
                                        $categories = array();
                                    }
                                    if(count($categories)) {
                                        $item_category = array();
                                        foreach($categories as $category) {
                                            $item_category[] = '<span>'.$category->name.'</span>';
                                        }
                                        $item_category = implode(' ', $item_category);
                                    }
                                    echo "<div class='page_title_meta cpt_portf'>".$item_category."</div>";
                                } else if (is_single() && get_post_type() == 'project') {
                                    $item_category = '';
                                    $categories = get_the_terms(get_the_ID(), 'project_category');
                                    if(!$categories || is_wp_error($categories)) {
                                        $categories = array();
                                    }
                                    if(count($categories)) {
                                        $item_category = array();
                                        foreach($categories as $category) {
                                            $item_category[] = '<span>'.$category->name.'</span>';
                                        }
                                        $item_category = implode(' ', $item_category);
                                    }
                                    echo "<div class='page_title_meta cpt_portf'>".$item_category."</div>";
                                }

                            echo "</div>";

                        echo "</div>";
                    echo "</div>";

                    if ( ! empty( $svg_bottom ) && ( $page_title_svg_line == 'svg_line_bottom' || $page_title_svg_line == 'svg_line_both' ) ) {
                        echo '' . $svg_bottom;
                    }
                    // Page title Post (Portfolio) Links
	                $blog_title_prev_next = gt3_option( "blog_title_prev_next" );
	                $portfolio_title_prev_next = gt3_option( "portfolio_title_prev_next" );
	                $project_title_prev_next = gt3_option( "project_title_prev_next" );
                    if (is_single() && ((get_post_type() == 'post' && $blog_title_prev_next) || (get_post_type() == 'portfolio' && $portfolio_title_prev_next ) || (get_post_type() == 'project' && $project_title_prev_next ))) {
                        $prev_post = get_adjacent_post(false, '', true);
                        $next_post = get_adjacent_post(false, '', false);
                        if ($prev_post) {
                            $post_url_prev = get_permalink($prev_post->ID);
                            echo "<a href='" . esc_url($post_url_prev) . "' title='" . esc_attr($prev_post->post_title) . "' class='page_title_post_link prev_link'><span>" . esc_html__('Prev', 'agrosector') . "<i class='theme_icon-arrows-left'></i></span></a>";
                        }
                        if ($next_post) {
                            $post_url_next = get_permalink($next_post->ID);
                            echo "<a href='" . esc_url($post_url_next) . "' title='" . esc_attr($next_post->post_title) . "' class='page_title_post_link next_link'><span><i class='theme_icon-arrows-right'></i>" . esc_html__('Next', 'agrosector') . "</span></a>";
                        }
                    }
                    // Page title Post (Portfolio) Links End
                echo "</div>";
            echo '</div>';
        }
    }
}


if (!function_exists('gt3_get_logo')) {
    function gt3_get_logo() {

	    $header_logo_src = gt3_option( "header_logo" );
	    $logo_sticky_src = gt3_option( "logo_sticky" );
        $logo_mobile_src = gt3_option( "logo_mobile" );
	    $logo_tablet_src = gt3_option( "logo_tablet" );

	    $logo_height_custom = gt3_option( 'logo_height_custom' );
	    $logo_height        = gt3_option( 'logo_height' );
	    $logo_max_height    = gt3_option( 'logo_max_height' );
	    $sticky_logo_height = gt3_option( 'sticky_logo_height' );

	    // height for logo
	    $header_height = gt3_option( 'header_height' );

	    $id = gt3_get_queried_object_id();
	    if ( class_exists( 'RWMB_Loader' ) && $id !== 0 ) {
		    $mb_header_presets = rwmb_meta( 'mb_header_presets', array(), $id );
		    $presets           = gt3_option( 'gt3_header_builder_presets' );
		    if ( $mb_header_presets != 'default' && isset( $mb_header_presets ) && ! empty( $presets[ $mb_header_presets ] ) && ! empty( $presets[ $mb_header_presets ]['preset'] ) ) {
			    $preset = $presets[ $mb_header_presets ]['preset'];
			    $preset = json_decode( $preset, true );

			    $mb_header_logo_src = gt3_option_presets( $preset, 'header_logo' );
			    $mb_logo_sticky_src = gt3_option_presets( $preset, 'logo_sticky' );
			    $mb_logo_mobile_src = gt3_option_presets( $preset, 'logo_mobile' );
                $mb_logo_tablet_src = gt3_option_presets( $preset, "logo_tablet" );

			    $header_logo_src = ! empty( $mb_header_logo_src ) ? $mb_header_logo_src : $header_logo_src;
			    $logo_sticky_src = ! empty( $mb_logo_sticky_src ) ? $mb_logo_sticky_src : $logo_sticky_src;
			    $logo_mobile_src = ! empty( $mb_logo_mobile_src ) ? $mb_logo_mobile_src : $logo_mobile_src;
                $logo_tablet_src = ! empty( $mb_logo_tablet_src ) ? $mb_logo_tablet_src : $logo_tablet_src;

			    $logo_height_custom = gt3_option_presets( $preset, 'logo_height_custom' );
			    $logo_height        = gt3_option_presets( $preset, 'logo_height' );
			    $logo_max_height    = gt3_option_presets( $preset, 'logo_max_height' );
			    $sticky_logo_height = gt3_option_presets( $preset, 'sticky_logo_height' );
			    $header_height      = gt3_option_presets( $preset, 'header_height' );
		    }
	    }
	    $header_logo_src = ! empty( $header_logo_src ) ? $header_logo_src['url'] : '';
	    $logo_sticky_src = ! empty( $logo_sticky_src ) ? $logo_sticky_src['url'] : '';
	    $logo_mobile_src = ! empty( $logo_mobile_src ) ? $logo_mobile_src['url'] : '';
        $logo_tablet_src = ! empty( $logo_tablet_src ) ? $logo_tablet_src['url'] : '';

	    $logo_height        = $logo_height['height'];
	    $sticky_logo_height = $sticky_logo_height['height'];
	    $header_height      = $header_height['height'];

	    if (!empty($header_height) && $logo_max_height != '1') {
            $header_height_css = ' style="max-height:'.esc_attr($header_height*0.9).'px;"';
        }else{
            $header_height_css = '';
        }

        $logo_height_style = '';
        if (!empty($logo_height) && $logo_height_custom == '1') {
            $logo_height_style .= 'height:'.(esc_attr($logo_height)).'px;';
        }
        if (!empty($header_height) && $logo_max_height != '1') {
            $logo_height_style .= 'max-height:'.esc_attr($header_height*0.9).'px;';
        }
        $logo_height_style = !empty($logo_height_style) ? ' style="'.$logo_height_style.'"' : '';

        $sticky_logo_height_style = '';
        if (!empty($sticky_logo_height) && $logo_height_custom == '1') {
            $sticky_logo_height_style .= 'height:'.(esc_attr($sticky_logo_height)).'px;';
        }elseif(!empty($logo_height) && $logo_height_custom == '1'){
            $sticky_logo_height_style .= 'height:'.(esc_attr($logo_height)).'px;';
        }
        $sticky_logo_height_style = !empty($sticky_logo_height_style) ? ' style="'.$sticky_logo_height_style.'"' : '';

        $logo_class = '';
        if ($logo_height_custom == '1' && $logo_max_height == '1' ) {
            $logo_class .= ' no_height_limit';
        }

        $logo = "";
        $logo .= "<div class='logo_container".$logo_class.
        (!empty($logo_sticky_src) ? ' sticky_logo_enable' : '').
        (!empty($logo_tablet_src) ? ' tablet_logo_enable' : '').
        (!empty($logo_mobile_src) ? ' mobile_logo_enable' : '')."'>";
        $logo .= "<a href='".esc_url(home_url('/'))."'".$header_height_css.">";
        if (!empty($header_logo_src)) {
            $logo .= '<img class="default_logo" src="'.esc_url($header_logo_src).'" alt="'.esc_attr__('logo', 'agrosector').'"'.$logo_height_style.'>';
        }else{
            $logo .= '<h1 class="site-title">';
            $logo .= get_bloginfo( 'name' );
            $logo .= '</h1>';
        }
        if (!empty($logo_sticky_src)) {
            $logo .= '<img class="sticky_logo" src="'.esc_url($logo_sticky_src).'" alt="'.esc_attr__('logo', 'agrosector').'"'.$sticky_logo_height_style.'>';
        }
        if (!empty($logo_tablet_src)) {
            $logo .= '<img class="tablet_logo" src="'.esc_url($logo_tablet_src).'" alt="'.esc_attr__('logo', 'agrosector').'">';
        }
        if (!empty($logo_mobile_src)) {
            $logo .= '<img class="mobile_logo" src="'.esc_url($logo_mobile_src).'" alt="'.esc_attr__('logo', 'agrosector').'">';
        }
        $logo .= "</a>";
        $logo .= "</div>";
        return $logo;
    }
}

if (!function_exists('gt3_get_header_builder_text_component')) {
    function gt3_get_header_builder_text_component($index){
        $text_editor_content = '';
        $hide_class = '';

        $id = gt3_get_queried_object_id();
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $mb_header_presets = rwmb_meta('mb_header_presets', array(), $id);
            $presets = gt3_option('gt3_header_builder_presets');
            if ($mb_header_presets != 'default' && isset($mb_header_presets) && !empty($presets[$mb_header_presets]) && !empty($presets[$mb_header_presets]['preset'])) {
                $preset = $presets[$mb_header_presets]['preset'];
                $preset = json_decode($preset,true);
                $text_editor_content = gt3_option_presets($preset,"text".$index."_editor");
                $text_hide_on_desktop = gt3_option_presets($preset,"text".$index."_hide_on_desktop");
                $text_hide_on_tablet = gt3_option_presets($preset,"text".$index."_hide_on_tablet");
                $text_hide_on_mobile = gt3_option_presets($preset,"text".$index."_hide_on_mobile");
            }else{
                $text_editor_content = gt3_option("text".$index."_editor");
                $text_hide_on_desktop = gt3_option("text".$index."_hide_on_desktop");
                $text_hide_on_tablet = gt3_option("text".$index."_hide_on_tablet");
                $text_hide_on_mobile = gt3_option("text".$index."_hide_on_mobile");
            }
        }else{
            $text_editor_content = gt3_option("text".$index."_editor");
            $text_hide_on_desktop = gt3_option("text".$index."_hide_on_desktop");
            $text_hide_on_tablet = gt3_option("text".$index."_hide_on_tablet");
            $text_hide_on_mobile = gt3_option("text".$index."_hide_on_mobile");
        }

        if (!empty($text_hide_on_desktop)) {
            $hide_class .= ' gt3_hide_on_desktop';
        }
        if (!empty($text_hide_on_tablet)) {
            $hide_class .= ' gt3_hide_on_tablet';
        }
        if (!empty($text_hide_on_mobile)) {
            $hide_class .= ' gt3_hide_on_mobile';
        }

        $out = '';
        $out .= '<div class="gt3_header_builder_component gt3_header_builder_text_component'.esc_attr($hide_class).'">';
        $out .= $text_editor_content;
        $out .= '</div>';
        return $out;
    }
}

if (!function_exists('gt3_get_header_builder')) {
    function gt3_get_header_builder($id){
        $gt3_header_builder_array = gt3_option("gt3_header_builder_id");
        $preset = '';
        //check if preset set in metabox
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $mb_header_presets = rwmb_meta('mb_header_presets', array(), $id);
            $presets = gt3_option('gt3_header_builder_presets');
            if ($mb_header_presets != 'default' && isset($mb_header_presets) && !empty($presets[$mb_header_presets]) && !empty($presets[$mb_header_presets]['preset'])) {
                $preset = $presets[$mb_header_presets]['preset'];
                $preset = json_decode($preset,true);
                $gt3_header_builder_array = gt3_option_presets($preset,'gt3_header_builder_id');
            }
        }
        if (!empty($gt3_header_builder_array)) {

            $header_sections = array();

            /* header builder main settings */
            $header_full_width = (bool)gt3_option('header_full_width');
            $header_sticky = (bool)gt3_option('header_sticky');
            $tablet_header_sticky = (bool)gt3_option('tablet_header_sticky');
            $mobile_header_sticky = (bool)gt3_option('mobile_header_sticky');
            $header_on_bg = (bool)gt3_option('header_on_bg');
            $tablet_header_on_bg = (bool)gt3_option('tablet_header_on_bg');
            $mobile_header_on_bg = (bool)gt3_option('mobile_header_on_bg');
            /* end header builder main settings */

            /* header builder component */

            // LOGO
            $logo = gt3_get_logo();

            //MENU
            $menu_slug = gt3_option("menu_select");
            $menu2_slug = gt3_option("menu2_select");
            if ( !class_exists( 'GT3_Core_Elementor' ) ) {
                $menu_array = get_nav_menu_locations();
                if (!empty($menu_array) && is_array($menu_array) && !empty($menu_array['main_menu'])) {
                    $menu_slug = $menu_array['main_menu'];
                }else{
                    $menu_slug = '';
                }
            }
            $menu_active_top_line = gt3_option('menu_active_top_line');

            // Burger sidebar
            $is_burger_sidebar = false;
            $sidebar = gt3_option("burger_sidebar_select");
            //login
            $is_login = false;

            /* end header builder component */

            /* sticky */
            if ($header_sticky) {
                $options['header_sticky'] = $header_sticky;
                $header_sticky_appearance_style = gt3_option('header_sticky_appearance_style');
                $header_sticky_appearance_number = gt3_option('header_sticky_appearance_number');
                $header_sticky_appearance_number = (gt3_option('header_sticky_appearance_from_top') == 'custom') && !empty($header_sticky_appearance_number) ? $header_sticky_appearance_number['height'] : '';
                $header_sticky_shadow = gt3_option('header_sticky_shadow');
            }
            /* end sticky */

            // change option by option from metabox
            if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
                if ($mb_header_presets != 'default' && !empty($mb_header_presets) && !empty($presets[$mb_header_presets]) && !empty($presets[$mb_header_presets]['preset'])) {
                    $header_full_width = (bool)gt3_option_presets($preset,'header_full_width');
                    $header_sticky = (bool)gt3_option_presets($preset,'header_sticky');
                    $tablet_header_sticky = (bool)gt3_option('tablet_header_sticky');
                    $mobile_header_sticky = (bool)gt3_option('mobile_header_sticky');
                    $header_on_bg = (bool)gt3_option_presets($preset,'header_on_bg');
                    $tablet_header_on_bg = (bool)gt3_option_presets($preset,'tablet_header_on_bg');
                    $mobile_header_on_bg = (bool)gt3_option_presets($preset,'mobile_header_on_bg');
                    $menu_slug = gt3_option_presets($preset,"menu_select");
                    $menu_active_top_line = gt3_option_presets($preset,'menu_active_top_line');
                    $sidebar = gt3_option_presets($preset,"burger_sidebar_select");
                }

                $mb_customize_header_layout = rwmb_meta('mb_customize_header_layout', array(), $id);
                if ($mb_customize_header_layout == 'none') {
                    return false;
                }
            }

            $responsive_header_over_bg = '';
            $responsive_header_over_bg .= $tablet_header_on_bg ? ' header_over_bg--tablet' : ' header_over_bg--tablet-off';
            $responsive_header_over_bg .= $mobile_header_on_bg ? ' header_over_bg--mobile' : ' header_over_bg--mobile-off';

            echo "<div class='gt3_header_builder".$responsive_header_over_bg.((bool)$header_on_bg ? ' header_over_bg' : '')."'>";

                $options = array('gt3_header_builder_array' => $gt3_header_builder_array );
                $options['header_full_width'] = $header_full_width;
                $options['logo'] = $logo;
                $options['menu_slug'] = $menu_slug;
                $options['menu2_slug'] = $menu2_slug;
                $options['menu_active_top_line'] = $menu_active_top_line;
                $options['header_sticky'] = false;
                if (!empty($preset)) {
                    $options['preset'] = $preset;
                }else{
                    $options['preset'] = '';
                }

                $gt3_header_builder_out_array = gt3_header_builder__container($options);
                $is_burger_sidebar = $gt3_header_builder_out_array['is_burger_sidebar'];
                $is_login = $gt3_header_builder_out_array['is_login'];
                $is_header_menu = $gt3_header_builder_out_array['is_header_menu'];

                $desktop_height = $gt3_header_builder_out_array['desktop_height'];
                $tablet_height = $gt3_header_builder_out_array['tablet_height'];
                $mobile_height = $gt3_header_builder_out_array['mobile_height'];

                echo  (($gt3_header_builder_out_array['header_out']));

                if ($header_sticky) {
                    $sticky_responsive_class = '';
                    $sticky_responsive_class .= (bool)$tablet_header_sticky ? ' sticky_header--tablet' : '';
                    $sticky_responsive_class .= (bool)$mobile_header_sticky ? ' sticky_header--mobile' : '';
                    $options['header_sticky'] = (bool)$header_sticky;
                    echo "<div class='sticky_header".$sticky_responsive_class.($header_sticky_shadow == '1' ? ' header_sticky_shadow' : '')."'".(!empty($sticky_styles) ? $sticky_styles : '').(!empty($header_sticky_appearance_style) ? ' data-sticky-type="'.esc_attr($header_sticky_appearance_style).'"' : '').(!empty($header_sticky_appearance_number) ? ' data-sticky-number="'.((int)$header_sticky_appearance_number).'"' : '').">";

                    $gt3_header_builder_out_array = gt3_header_builder__container($options);
                    echo  (($gt3_header_builder_out_array['header_out']));
                    echo "</div>";
                }

                if ($is_header_menu) {
                    ob_start();
                        if (!empty($menu_slug)) {
                            gt3_header_builder_menu($menu_slug);
                        }else{
                            if (has_nav_menu( 'main_menu' )) {
                                gt3_main_menu();
                            }
                        }
                    $menu = ob_get_clean();
                    if (!empty($menu)) {
                        echo '<div class="mobile_menu_container">';
                            echo (($header_full_width)) ? "<div class='fullwidth-wrapper'>":"<div class='container'>";
                                echo "<div class='gt3_header_builder_component gt3_header_builder_menu_component'><nav class='main-menu main_menu_container".($menu_active_top_line == '1' ? ' menu_line_enable' : '')."'>";
                                echo  (($menu));
                                echo "</nav>";
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }
                }

            echo "</div>";
            if ($is_burger_sidebar) {
                echo '<div class="gt3_header_builder__burger_sidebar">';
                    echo '<div class="gt3_header_builder__burger_sidebar-cover"></div>';
                    echo '<div class="gt3_burger_sidebar_container">';
                        if (is_active_sidebar( $sidebar )) {
                            echo "<aside class='sidebar'>";
                            dynamic_sidebar( $sidebar );
                            echo "</aside>";
                        }
                    echo '</div>';
                echo '</div>';
            }
            if ($is_login) {
                echo "<div class='gt3_header_builder__login-modal".(get_option('woocommerce_enable_myaccount_registration') !='yes' ? ' without_register' : '').(is_user_logged_in() ? ' user_logged_in' : '')."'>";
                    echo "<div class='gt3_header_builder__login-modal-cover'></div>";
                    echo "<div class='gt3_header_builder__login-modal_container container'>";
                        echo "<div class='gt3_header_builder__login-modal-close'></div>";
                        if ( is_user_logged_in() ) {
                            wc_get_template('myaccount/navigation.php');
                        }
                        if (!is_user_logged_in()) {
                            $is_nextend_facebook = in_array( 'nextend-facebook-connect/nextend-facebook-connect.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
                            $is_nextend_google = in_array( 'nextend-google-connect/nextend-google-connect.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
                            $is_nextend_twitter = in_array( 'nextend-twitter-connect/nextend-twitter-connect.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
                            echo wc_get_template('gt3-templates/gt3-form-login.php');
                            if (($is_nextend_facebook || $is_nextend_google || $is_nextend_twitter) && get_option('woocommerce_enable_myaccount_registration')=='yes') {
                                echo "<div class='gt3_header_builder__login-modal_footer'>";
                                if ($is_nextend_facebook) {
                                    echo "<div class='gt3_module_button button_alignment_inline'>";
                                        echo "<a href='" . esc_url(wp_login_url() . "?loginSocial=facebook&redirect=" . get_permalink()) . "' class='button_size_normal gt3_facebook_login' data-plugin='nsl' data-action='connect' data-redirect='current' data-provider='facebook' data-popupwidth='475' data-popupheight='175'>";
                                        echo '<i class="fa fa-facebook" aria-hidden="true"></i>';
                                        echo '<span>'.esc_html__( 'Login with Facebook', 'agrosector' ).'</span>';
                                        echo "</a>";
                                    echo "</div>";
                                }
                                if ($is_nextend_google) {
                                    echo "<div class='gt3_module_button button_alignment_inline'>";
                                        echo "<a href='" . esc_url(wp_login_url() . "?loginSocial=google&redirect=" . get_permalink()) . "' class='button_size_normal gt3_google_login' data-plugin='nsl' data-action='connect' data-redirect='current' data-provider='google' data-popupwidth='475' data-popupheight='175'>";
                                        echo '<i class="fa fa-google" aria-hidden="true"></i>';
                                        echo '<span>'.esc_html__( 'Login with Google', 'agrosector' ).'</span>';
                                        echo "</a>";
                                    echo "</div>";
                                }
                                if ($is_nextend_twitter) {
                                    echo "<div class='gt3_module_button button_alignment_inline'>";
                                        echo "<a href='" . esc_url(wp_login_url() . "?loginSocial=twitter&redirect=" . get_permalink()) . "' class='button_size_normal gt3_twitter_login' title='".esc_attr__( 'Login with Twitter', 'agrosector' )."' data-plugin='nsl' data-action='connect' data-redirect='current' data-provider='twitter' data-popupwidth='475' data-popupheight='175'>";
                                        echo '<i class="fa fa-twitter" aria-hidden="true"></i>';
                                        echo '<span>'.esc_html__( 'Login with Twitter', 'agrosector' ).'</span>';
                                        echo "</a>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            }
                        }
                    echo "</div>";

                echo "</div>";
            }

            if (!empty($desktop_height)) {
                $responsive_header_height = array(
                    'desktop_height' => $desktop_height,
                    'tablet_height' => $tablet_height,
                    'mobile_height' => $mobile_height
                );

                if (function_exists('gt3_get_top_offset_for_page_title')) {
                    gt3_get_top_offset_for_page_title($header_on_bg,$tablet_header_on_bg,$mobile_header_on_bg,$responsive_header_height);
                }
            }

        }
    }
}


if (!function_exists('gt3_header_builder__container')) {
    function gt3_header_builder__container($options = null) {
        extract($options);
        $header_sections = array();
        $is_burger_sidebar = false;
        $is_login = false;
        $is_header_menu = false;
	    $id = gt3_get_queried_object_id();
        ob_start();
        echo "<div class='gt3_header_builder__container'>";

        unset(
            $gt3_header_builder_array['all_item'] ,
            $gt3_header_builder_array['all_item__tablet'],
            $gt3_header_builder_array['all_item__mobile']
        );

            foreach ($gt3_header_builder_array as $side => $value) {


                if (!empty($gt3_header_builder_array[$side]['content']) && $side != 'all_item' ) {

                    $side_out = '';

                    if (count($gt3_header_builder_array[$side]['content']) == 1 && empty($gt3_header_builder_array[$side]['content']['placebo']) || count($gt3_header_builder_array[$side]['content']) > 1) {
                        //get level and position of menu part
                        $side_filterred = str_replace('__', '_', $side);
                        $full_position = explode('_', $side_filterred, 3);
                        $level         = !empty($full_position[0]) ? $full_position[0] : '';
                        $position      = !empty($full_position[1]) ? $full_position[1] : '';
                        $responsive    = !empty($full_position[2]) ? $full_position[2] : '';

                        if ($header_sticky) {
                            if (!empty($preset)) {
                                $enable_section_in_sticky = (bool)gt3_option_presets($preset,"side_".$level."_sticky");
                            }else{
                                $enable_section_in_sticky = (bool)gt3_option("side_".$level."_sticky");
                            }

                        }else{
                            $enable_section_in_sticky = true;
                        }

                        if ($enable_section_in_sticky) {

                            $side_class = '';
                            $side_class .= sanitize_html_class($side);
                            $side_class .= !empty($position) ? ' '.sanitize_html_class($position) : '';

                            if (!empty($preset)) {
                                $side_align = gt3_option_presets($preset,$side."-align");
                            }else{
                                $side_align = gt3_option($side."-align");
                            }

                            if ($side_align != $position) {
                                $side_class .= ' header_side--custom-align header_side--'.$side_align.'-align';
                            }

                                $side_content_out = '';
                                ob_start();
                                foreach ($gt3_header_builder_array[$side]['content'] as $key => $value) {
                                    if ($key != 'placebo' && $key != 'undefined') {
                                        switch ($key) {
                                            case 'left_bar':
                                                echo !empty($bottom_header_left) ? $bottom_header_left  : '';
                                                break;
                                            case 'logo':
                                                echo !empty($logo) ? $logo : '';
                                                break;
                                            case 'menu':
                                                if (!empty($menu_slug)) {
                                                    $is_header_menu = true;
                                                    echo '<div class="gt3_header_builder_component gt3_header_builder_menu_component"><nav class="main-menu main_menu_container'.($menu_active_top_line == '1' ? ' menu_line_enable' : '').'">';
                                                    if ( class_exists( 'GT3_Core_Elementor' ) ) {
                                                        gt3_header_builder_menu($menu_slug);
                                                    }else{
                                                        if (has_nav_menu( 'main_menu' )) {
                                                            gt3_main_menu();
                                                        }
                                                    }

                                                    echo '</nav>';
                                                    echo '<div class="mobile-navigation-toggle"><div class="toggle-box"><div class="toggle-inner"></div></div></div></div>';
                                                }else{
                                                    if (has_nav_menu( 'main_menu' )) {
                                                        $is_header_menu = true;
                                                        echo "<div class='gt3_header_builder_component gt3_header_builder_menu_component'><nav class='main-menu main_menu_container".($menu_active_top_line == '1' ? ' menu_line_enable' : '')."'>";
                                                        gt3_main_menu();
                                                        echo  "</nav>";
                                                        echo '<div class="mobile-navigation-toggle"><div class="toggle-box"><div class="toggle-inner"></div></div></div></div>';
                                                    }
                                                }
                                                break;
                                            case 'menu2':
                                                if ( !empty($menu2_slug) ) {
                                                    $is_header_menu2 = true;
                                                    $menu2_open = gt3_option('menu2_open');
                                                    $menu2_mobile = gt3_option('menu2_mobile');

                                                    if (class_exists( 'RWMB_Loader' ) && $id !== 0 && rwmb_meta('mb_customize_column_menu', array(), $id) == 'custom'){
                                                        $menu2_open = rwmb_meta('mb_customize_column_menu_open', array(), $id);
                                                        $menu2_mobile = rwmb_meta('mb_customize_column_menu_mobile', array(), $id);
                                                    }

                                                    $menu2_class = $menu_active_top_line == '1' ? ' menu_line_enable' : '';
                                                    $menu2_class .= $menu2_open ? ' open' : '';
                                                    $menu2_class .= $menu2_mobile ? ' mobile_allowed' : '';

                                                    echo '<div class="gt3_header_builder_component gt3_header_builder_menu_component"><nav class="column_menu column_menu_container '.esc_attr($menu2_class).'">';
                                                    $menu2_title = gt3_option('menu2_title');
                                                    if ( !empty($menu2_title) ) {
                                                        echo '<div class="gt3-menu-categories-title">'.esc_attr($menu2_title).'<i class="fa fa-angle-down" aria-hidden="true"></i></div>';
                                                    }
                                                    if ( class_exists( 'GT3_Core_Elementor' ) ) {
                                                        gt3_header_builder_menu($menu2_slug);
                                                    }
                                                    echo '</nav></div>';
                                                }
                                                break;
                                            case 'search':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_search_component">'.do_shortcode('[gt3_search]').'</div>';
                                                break;
                                            case 'search_cat':
                                                if ( class_exists('WooCommerce') ) {
                                                    echo '<div class="gt3_header_builder_component gt3_header_builder_search_cat_component">'.gt3_search_category().'</div>';
                                                }
                                                break;
                                            case 'login':
                                                $is_login = true;
                                                if ( !class_exists('WooCommerce') ) {
                                                    $is_login = false;
                                                }
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_login_component"><p>'.esc_html__('login / register', 'agrosector').'</p></div>';
                                                break;
                                            case 'cart':
                                                if ( class_exists('WooCommerce') ) {
                                                    ob_start();
                                                    woocommerce_mini_cart();
                                                    $woo_mini_cart = ob_get_clean();
                                                    ob_start();
                                                    ?>
                                                    <a class="woo_icon" href="<?php echo wc_get_cart_url(); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'agrosector' ); ?>">
                                                        <i class='woo_mini-count'>
                                                        <?php if(WC()->cart->cart_contents_count > 0){ ?>
                                                            <span><?php echo esc_html( WC()->cart->cart_contents_count ); ?></span>
                                                        <?php }; ?>
                                                        </i>
                                                    </a>
                                                    <?php
                                                    $woo_mini_icon = ob_get_clean(); ?>

                                                    <div class="gt3_header_builder_component gt3_header_builder_cart_component">
                                                        <?php echo '' . $woo_mini_icon; ?>
                                                        <div class="gt3_header_builder_cart_component__cart woocommerce">
                                                            <div class="gt3_header_builder_cart_component__cart-container">
                                                                <?php echo '' . $woo_mini_cart; ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php }
                                                break;
                                            case 'burger_sidebar':
                                                $is_burger_sidebar = true;
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_burger_sidebar_component"><i class="burger_sidebar_icon"><span class="first"></span><span class="second"></span><span class="third"></span></i></div>';
                                                break;
                                            case 'text1':
                                                $text1_out = gt3_get_header_builder_text_component(1);
                                                echo !empty($text1_out) ? do_shortcode( $text1_out )  : '';
                                                break;
                                            case 'text2':
                                                $text2_out = gt3_get_header_builder_text_component(2);
                                                echo !empty($text2_out) ? do_shortcode($text2_out)  : '';
                                                break;
                                            case 'text3':
                                                $text3_out = gt3_get_header_builder_text_component(3);
                                                echo !empty($text3_out) ? do_shortcode($text3_out)  : '';
                                                break;
                                            case 'text4':
                                                $text4_out = gt3_get_header_builder_text_component(4);
                                                echo !empty($text4_out) ? do_shortcode($text4_out)  : '';
                                                break;
                                            case 'text5':
                                                $text5_out = gt3_get_header_builder_text_component(5);
                                                echo !empty($text5_out) ? do_shortcode($text5_out)  : '';
                                                break;
                                            case 'text6':
                                                $text6_out = gt3_get_header_builder_text_component(6);
                                                echo !empty($text6_out) ? do_shortcode($text6_out)  : '';
                                                break;
                                            case 'delimiter1':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter1"></div>';
                                                break;
                                            case 'delimiter2':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter2"></div>';
                                                break;
                                            case 'delimiter3':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter3"></div>';
                                                break;
                                            case 'delimiter4':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter4"></div>';
                                                break;
                                            case 'delimiter5':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter5"></div>';
                                                break;
                                            case 'delimiter6':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_delimiter_component gt3_delimiter6"></div>';
                                                break;

                                            case 'empty_space1':
                                            case 'empty_space2':
                                            case 'empty_space3':
                                            case 'empty_space4':
                                            case 'empty_space5':
                                                echo '<div class="gt3_header_builder_component gt3_header_builder_empty_space_component"></div>';
                                                break;

                                        }
                                    }
                                }
                                $side_content_out = ob_get_clean();
                                if (!empty($side_content_out)) {
                                    $side_out .= "<div class='".$side_class." header_side'>";
                                        $side_out .=  "<div class='header_side_container'>";
                                            $side_out .= $side_content_out;
                                        $side_out .=  "</div>";//header side container end
                                    $side_out .=  "</div>";//header side end
                                }

                            if (!empty($side_out)) {
                                if (!empty($responsive)) {
                                    $level = $level .'_'.$responsive;
                                }
                                $header_sections[$level][$position] = $side_out;
                            }
                        }
                    }
                }
            }
            $is_tablet = false;
            $is_mobile = false;
            $all_header_section = array_keys($header_sections);
            $desktop_height = 0;
            $tablet_height = 0;
            $mobile_height = 0;
            foreach ($all_header_section as $header_section) {
                if (strpos($header_section, 'tablet')) {
                    $is_tablet = true;
                }
                if (strpos($header_section, 'mobile')) {
                    $is_mobile = true;
                }
            }
            foreach ($header_sections as $header_section => $header_section_content) {
                $responsive_class = $header_mobile_class = '';
                if (!empty($preset)) {
                    $header_show_on_mobile = gt3_option_presets($preset,"side_".$header_section."_mobile");
                    $header_mobile_class = isset($header_show_on_mobile) && !(bool)$header_show_on_mobile ? ' gt3_header_builder__section--hide_on_mobile' : '';
                }else{
                    $header_show_on_mobile = gt3_option("side_" . $header_section . "_mobile");
                    $header_mobile_class = isset($header_show_on_mobile) && !(bool)$header_show_on_mobile ? ' gt3_header_builder__section--hide_on_mobile' : '';
                }


                if ($is_tablet && !strpos($header_section, 'tablet')) {
                    $responsive_class .= ' gt3_header_builder__section--hide_on_tablet';
                }elseif($is_tablet && strpos($header_section, 'tablet')){
                    $responsive_class .= ' gt3_header_builder__section--show_on_tablet';
                }

                if ($is_mobile && !strpos($header_section, 'mobile') && $header_mobile_class != ' gt3_header_builder__section--hide_on_mobile') {
                    $responsive_class .= ' gt3_header_builder__section--hide_on_mobile';
                }elseif($is_mobile && strpos($header_section, 'mobile')){
                    $responsive_class .= ' gt3_header_builder__section--show_on_mobile';
                }

                $header_section = str_replace('_', '__', $header_section);

                if (!empty($preset)) {
                    ${'side_' . $header_section . '_custom'} = gt3_option_presets($preset,'side_'.$header_section.'_custom');
                    ${'side_' . $header_section . '_height'} = gt3_option_presets($preset,'side_'.$header_section.'_height');
                }else{
                    ${'side_' . $header_section . '_custom'} = gt3_option('side_'.$header_section.'_custom');
                    ${'side_' . $header_section . '_height'} = gt3_option('side_'.$header_section.'_height');
                }
                ${'side_' . $header_section . '_height'} = ${'side_' . $header_section . '_height'}['height'];

                if (!${'side_' . $header_section . '_custom'}) {
                    $responsive_res = explode('__',$header_section);
                    if (is_array($responsive_res) && !empty($responsive_res[0]) && !empty($responsive_res[1])) {
                        if ($responsive_res[1] == 'tablet') {
                            ${'side_' . $header_section . '_height'} = ${'side_' . $responsive_res[0] . '_height'};
                        }
                        if ($responsive_res[1] == 'mobile') {
                            ${'side_' . $header_section . '_height'} = isset(${'side_' . $responsive_res[0] . '__tablet_height'}) ? ${'side_' . $responsive_res[0] . '__tablet_height'} : ${'side_' . $responsive_res[0] . '_height'};
                        }
                    }
                }


                if (!strpos($header_section, 'tablet') && !strpos($header_section, 'mobile')) {
                    $desktop_height += (int)${'side_' . $header_section . '_height'};
                }

                if ($is_tablet) {
                    if (!strpos($header_section, 'tablet')) {
                    }elseif(strpos($header_section, 'tablet')){
                        $tablet_height += (int)${'side_' . $header_section . '_height'};
                    }
                }

                if ($is_mobile) {
                    if (!strpos($header_section, 'mobile') && $header_mobile_class != ' gt3_header_builder__section--hide_on_mobile') {
                    }elseif(strpos($header_section, 'mobile')){
                        $mobile_height += (int)${'side_' . $header_section . '_height'};
                    }
                }


                echo "<div class='gt3_header_builder__section gt3_header_builder__section--" . esc_attr($header_section) . $responsive_class . (!empty($header_section_content['center']) ? ' not_empty_center_side' : '') . $header_mobile_class . "'>";
                echo "<div class='gt3_header_builder__section-container" . (!$header_full_width ? ' container' : ' container_full') . "'>";
                if (empty($header_section_content['left'])) {
                    echo "<div class='" . esc_attr($header_section) . "_left left header_side'></div>";
                }
                foreach ($header_section_content as $side => $side_content) {
                    echo '' . $side_content;
                }
                if (empty($header_section_content['right'])) {
                    echo "<div class='" . esc_attr($header_section) . "_right right header_side'></div>";
                }
                echo "</div>";
                echo "</div>";
            }
        echo "</div>";

        if ($tablet_height == 0) {
            $tablet_height = $desktop_height;
        }
        if ($mobile_height == 0) {
            $mobile_height = $tablet_height;
        }

        $gt3_header_builder__container = ob_get_clean();
        $output_array = array();
        $output_array['header_out'] = $gt3_header_builder__container;
        $output_array['is_login'] = $is_login;
        $output_array['is_burger_sidebar'] = $is_burger_sidebar;
        $output_array['is_header_menu'] = $is_header_menu;

        $output_array['desktop_height'] = $desktop_height;
        $output_array['tablet_height'] = $tablet_height;
        $output_array['mobile_height'] = $mobile_height;
        return $output_array;
    }
}

if (!function_exists('gt3_option_presets')) {
    function gt3_option_presets($preset = '',$name = ''){
        return isset($preset[$name]) ? $preset[$name] : null;
    }
}

if (!function_exists('gt3_main_menu')) {
    function gt3_main_menu (){
        wp_nav_menu( array(
            'theme_location'  => 'main_menu',
            'container' => '',
            'container_class' => '',
            'after' => '',
            'link_before'     => '<span>',
            'link_after'      => '</span>',
            'walker' => new GT3_Walker_Nav_Menu (),
        ) );
    }
}

if (!function_exists('gt3_header_builder_menu')) {
    function gt3_header_builder_menu ($menu_slug){
        wp_nav_menu( array(
            'menu'            => $menu_slug,
            'container'       => '',
            'container_class' => '',
            'after'           => '',
            'link_before'     => '<span>',
            'link_after'      => '</span>',
            'walker' => new GT3_Walker_Nav_Menu (),
        ) );
    }
}

// need for vertical view of header in theme options (admin)
if (!function_exists('gt3_add_admin_class_menu_order')) {
    add_filter('admin_body_class', 'gt3_add_admin_class_menu_order');
    function gt3_add_admin_class_menu_order($classes) {
        if (gt3_option('bottom_header_vertical_order')) {
            $classes .= ' bottom_header_vertical_order';
        }
        return $classes;
    }
}

if (!function_exists('gt3_footer_area')) {
    function gt3_footer_area(){
        // footer option
        $footer_switch = gt3_option('footer_switch');
        $footer_spacing = gt3_option('footer_spacing');
        $footer_column = gt3_option_compare('footer_column','mb_footer_switch','yes');
        $footer_column2 = gt3_option_compare('footer_column2','mb_footer_switch','yes');
        $footer_column3 = gt3_option_compare('footer_column3','mb_footer_switch','yes');
        $footer_column5 = gt3_option_compare('footer_column5','mb_footer_switch','yes');
        $footer_align = gt3_option_compare('footer_align','mb_footer_switch','yes');
        $footer_full_width = gt3_option_compare('footer_full_width','mb_footer_switch','yes');
        $footer_bg_color = gt3_option_compare('footer_bg_color','mb_footer_switch','yes');

        // copyright option
        $copyright_switch = gt3_option('copyright_switch');
        $copyright_spacing = gt3_option('copyright_spacing');
        $copyright_editor = gt3_option_compare('copyright_editor','mb_copyright_switch','1','mb_footer_switch','yes');
        $copyright_align = gt3_option_compare('copyright_align','mb_copyright_switch','1','mb_footer_switch','yes');
        $copyright_bg_color = gt3_option_compare('copyright_bg_color','mb_copyright_switch','1','mb_footer_switch','yes');
        $copyright_top_border = gt3_option("copyright_top_border");
        $copyright_top_border_color = gt3_option("copyright_top_border_color");

        // Pre Footer option
        $pre_footer_switch = gt3_option('pre_footer_switch');
        $pre_footer_spacing = gt3_option('pre_footer_spacing');
        $pre_footer_editor = gt3_option_compare('pre_footer_editor','mb_pre_footer_switch','1','mb_footer_switch','yes');
        $pre_footer_align = gt3_option_compare('pre_footer_align','mb_pre_footer_switch','1','mb_footer_switch','yes');
        $pre_footer_bottom_border = gt3_option("pre_footer_bottom_border");
        $pre_footer_bottom_border_color = gt3_option("pre_footer_bottom_border_color");

        // METABOX VAR
	    $id = gt3_get_queried_object_id();
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $mb_footer_switch = rwmb_meta('mb_footer_switch', array(), $id);
            if ($mb_footer_switch == 'yes') {
                $footer_switch = true;

                $footer_spacing = array();
                $mb_padding_top = rwmb_meta('mb_padding_top', array(), $id);
                $mb_padding_bottom = rwmb_meta('mb_padding_bottom', array(), $id);
                $mb_padding_left = rwmb_meta('mb_padding_left', array(), $id);
                $mb_padding_right = rwmb_meta('mb_padding_right', array(), $id);
                $footer_spacing['padding-top'] = !empty($mb_padding_top) ? $mb_padding_top : '';
                $footer_spacing['padding-bottom'] = !empty($mb_padding_bottom) ? $mb_padding_bottom : '';
                $footer_spacing['padding-left'] = !empty($mb_padding_left) ? $mb_padding_left : '';
                $footer_spacing['padding-right'] = !empty($mb_padding_right) ? $mb_padding_right : '';

                $mb_footer_sidebar_1 = rwmb_meta('mb_footer_sidebar_1', array(), $id);
                $mb_footer_sidebar_2 = rwmb_meta('mb_footer_sidebar_2', array(), $id);
                $mb_footer_sidebar_3 = rwmb_meta('mb_footer_sidebar_3', array(), $id);
                $mb_footer_sidebar_4 = rwmb_meta('mb_footer_sidebar_4', array(), $id);
                $mb_footer_sidebar_5 = rwmb_meta('mb_footer_sidebar_5', array(), $id);
            }elseif (rwmb_meta('mb_footer_switch', array(), $id) == 'no') {
                $footer_switch = false;
            }

            if ($mb_footer_switch == 'yes') {
                $mb_copyright_switch = rwmb_meta('mb_copyright_switch', array(), $id);
                if ($mb_copyright_switch == '1') {
                    $copyright_switch = true;
                    $mb_copyright_padding_top = rwmb_meta('mb_copyright_padding_top', array(), $id);
                    $mb_copyright_padding_bottom = rwmb_meta('mb_copyright_padding_bottom', array(), $id);
                    $mb_copyright_padding_left = rwmb_meta('mb_copyright_padding_left', array(), $id);
                    $mb_copyright_padding_right = rwmb_meta('mb_copyright_padding_right', array(), $id);
                    $copyright_spacing['padding-top'] = !empty($mb_copyright_padding_top) ? $mb_copyright_padding_top : '';
                    $copyright_spacing['padding-bottom'] = !empty($mb_copyright_padding_bottom) ? $mb_copyright_padding_bottom : '';
                    $copyright_spacing['padding-left'] = !empty($mb_copyright_padding_left) ? $mb_copyright_padding_left : '';
                    $copyright_spacing['padding-right'] = !empty($mb_copyright_padding_right) ? $mb_copyright_padding_right : '';

                    $copyright_top_border = rwmb_meta("mb_copyright_top_border", array(), $id);
                    $mb_copyright_top_border_color = rwmb_meta("mb_copyright_top_border_color", array(), $id);
                    $mb_copyright_top_border_color_opacity = rwmb_meta("mb_copyright_top_border_color_opacity", array(), $id);

                    if (!empty($mb_copyright_top_border_color) && $copyright_top_border == '1') {
                        $copyright_top_border_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_copyright_top_border_color)).','.$mb_copyright_top_border_color_opacity.')';
                    }else{
                        $copyright_top_border_color = '';
                    }

                }else{
                    $copyright_switch = false;
                }


                $mb_pre_footer_switch = rwmb_meta('mb_pre_footer_switch', array(), $id);
                if ($mb_pre_footer_switch == '1') {
                    $pre_footer_switch = true;
                    $mb_pre_footer_padding_top = rwmb_meta('mb_pre_footer_padding_top', array(), $id);
                    $mb_pre_footer_padding_bottom = rwmb_meta('mb_pre_footer_padding_bottom', array(), $id);
                    $mb_pre_footer_padding_left = rwmb_meta('mb_pre_footer_padding_left', array(), $id);
                    $mb_pre_footer_padding_right = rwmb_meta('mb_pre_footer_padding_right', array(), $id);
                    $pre_footer_spacing['padding-top'] = !empty($mb_pre_footer_padding_top) ? $mb_pre_footer_padding_top : '';
                    $pre_footer_spacing['padding-bottom'] = !empty($mb_pre_footer_padding_bottom) ? $mb_pre_footer_padding_bottom : '';
                    $pre_footer_spacing['padding-left'] = !empty($mb_pre_footer_padding_left) ? $mb_pre_footer_padding_left : '';
                    $pre_footer_spacing['padding-right'] = !empty($mb_pre_footer_padding_right) ? $mb_pre_footer_padding_right : '';

                    $pre_footer_bottom_border = rwmb_meta("mb_pre_footer_bottom_border", array(), $id);
                    $mb_pre_footer_bottom_border_color = rwmb_meta("mb_pre_footer_bottom_border_color", array(), $id);
                    $mb_pre_footer_bottom_border_color_opacity = rwmb_meta("mb_pre_footer_bottom_border_color_opacity", array(), $id);

                    if (!empty($mb_pre_footer_bottom_border_color) && $pre_footer_bottom_border == '1') {
                        $pre_footer_bottom_border_color['rgba'] = 'rgba('.(gt3_HexToRGB($mb_pre_footer_bottom_border_color)).','.$mb_pre_footer_bottom_border_color_opacity.')';
                    }else{
                        $pre_footer_bottom_border_color = '';
                    }

                }else{
                    $pre_footer_switch = false;
                }

            }elseif (rwmb_meta('mb_footer_switch', array(), $id) == 'no') {
                $copyright_switch = false;
                $pre_footer_switch = false;
            }

        }else{
            $mb_footer_switch = false;
        }

        //footer container style
        $footer_cont_style = !empty($footer_bg_color) ? ' background-color :'.esc_attr($footer_bg_color).';' : '';
        $footer_cont_style .= gt3_background_render('footer','mb_footer_switch','yes');

        $footer_cont_style = !empty($footer_cont_style) ? ' style="'.$footer_cont_style.'"' : '' ;

        //footer container class
        $footer_class = '';
        $footer_class .= ' align-'.esc_attr($footer_align);

        //footer padding
        $footer_top_padding = !empty($footer_spacing['padding-top']) ? $footer_spacing['padding-top'] : '';
        $footer_bottom_padding = !empty($footer_spacing['padding-bottom']) ? $footer_spacing['padding-bottom'] : '';
        $footer_left_padding = !empty($footer_spacing['padding-left']) ? $footer_spacing['padding-left'] : '';
        $footer_right_padding = !empty($footer_spacing['padding-right']) ? $footer_spacing['padding-right'] : '';

        //footer style
        $footer_style = '';
        $footer_style .= !empty($footer_top_padding) ? 'padding-top:'.esc_attr($footer_top_padding).'px;' : '' ;
        $footer_style .= !empty($footer_bottom_padding) ? 'padding-bottom:'.esc_attr($footer_bottom_padding).'px;' : '' ;
        $footer_style .= !empty($footer_left_padding) ? 'padding-left:'.esc_attr($footer_left_padding).'px;' : '' ;
        $footer_style .= !empty($footer_right_padding) ? 'padding-right:'.esc_attr($footer_right_padding).'px;' : '' ;
        $footer_style = !empty($footer_style) ? ' style="'.$footer_style.'"' : '';

        // COPYRIGHT CODE
        // copyright class
        $copyright_class = '';
        $copyright_class .= ' align-'.esc_attr($copyright_align);

        // copyright container style
        $copyright_cont_style = '';
        $copyright_cont_style .= !empty($copyright_bg_color) ? 'background-color:'.esc_attr($copyright_bg_color).';' : '';

        if ($copyright_top_border == '1') {
            $copyright_cont_border_style = !empty($copyright_top_border_color['rgba']) ? ' style="border-top: 1px solid '.esc_attr($copyright_top_border_color['rgba']).';"' : '';
            if ($footer_full_width !== 'default') {
                $copyright_cont_style .= !empty($copyright_top_border_color['rgba']) ? 'border-top: 1px solid '.esc_attr($copyright_top_border_color['rgba']).';' : '';
            }
        }else{
            $copyright_cont_border_style = '';
        }
        $copyright_cont_style = !empty($copyright_cont_style) ? ' style="'.$copyright_cont_style.'"' : '';

        // copyright padding
        $copyright_top_padding = !empty($copyright_spacing['padding-top']) ? $copyright_spacing['padding-top'] : '';
        $copyright_bottom_padding = !empty($copyright_spacing['padding-bottom']) ? $copyright_spacing['padding-bottom'] : '';
        $copyright_left_padding = !empty($copyright_spacing['padding-left']) ? $copyright_spacing['padding-left'] : '';
        $copyright_right_padding = !empty($copyright_spacing['padding-right']) ? $copyright_spacing['padding-right'] : '';
        // copyright style
        $copyright_style = '';
        $copyright_style .= !empty($copyright_top_padding) ? 'padding-top:'.esc_attr($copyright_top_padding).'px;' : '' ;
        $copyright_style .= !empty($copyright_bottom_padding) ? 'padding-bottom:'.esc_attr($copyright_bottom_padding).'px;' : '' ;
        $copyright_style .= !empty($copyright_left_padding) ? 'padding-left:'.esc_attr($copyright_left_padding).'px;' : '' ;
        $copyright_style .= !empty($copyright_right_padding) ? 'padding-right:'.esc_attr($copyright_right_padding).'px;' : '' ;
        $copyright_style = !empty($copyright_style) ? ' style="'.$copyright_style.'"' : '';

        // copyright class
        $pre_footer_class = '';
        $pre_footer_class .= ' align-'.esc_attr($pre_footer_align);

        // copyright container style
        $pre_footer_cont_style = '';
        if ($pre_footer_bottom_border == '1') {
            $pre_footer_cont_style .= !empty($pre_footer_bottom_border_color['rgba']) ? 'border-bottom: 1px solid '.esc_attr($pre_footer_bottom_border_color['rgba']).';border-top: 1px solid '.esc_attr($pre_footer_bottom_border_color['rgba']).';' : '';
        }
        $pre_footer_cont_style = !empty($pre_footer_cont_style) ? ' style="'.$pre_footer_cont_style.'"' : '';

        // copyright padding
        $pre_footer_top_padding = !empty($pre_footer_spacing['padding-top']) ? $pre_footer_spacing['padding-top'] : '';
        $pre_footer_bottom_padding = !empty($pre_footer_spacing['padding-bottom']) ? $pre_footer_spacing['padding-bottom'] : '';
        $pre_footer_left_padding = !empty($pre_footer_spacing['padding-left']) ? $pre_footer_spacing['padding-left'] : '';
        $pre_footer_right_padding = !empty($pre_footer_spacing['padding-right']) ? $pre_footer_spacing['padding-right'] : '';
        // copyright style
        $pre_footer_style = '';
        $pre_footer_style .= !empty($pre_footer_top_padding) ? 'padding-top:'.esc_attr($pre_footer_top_padding).'px;' : '' ;
        $pre_footer_style .= !empty($pre_footer_bottom_padding) ? 'padding-bottom:'.esc_attr($pre_footer_bottom_padding).'px;' : '' ;
        $pre_footer_style .= !empty($pre_footer_left_padding) ? 'padding-left:'.esc_attr($pre_footer_left_padding).'px;' : '' ;
        $pre_footer_style .= !empty($pre_footer_right_padding) ? 'padding-right:'.esc_attr($pre_footer_right_padding).'px;' : '' ;
        $pre_footer_style = !empty($pre_footer_style) ? ' style="'.$pre_footer_style.'"' : '';

        // COLUMN BUILD
        $column_sizes = array();
        switch ($footer_column) {
            case 1:
                $column_sizes = array('12');
                break;
            case 2:
                $column_sizes = explode("-", $footer_column2);
                break;
            case 3:
                $column_sizes = explode("-", $footer_column3);
                break;
            case 4:
                $column_sizes = array('3','3','3','3');
                break;
            case 4.5:
                $column_sizes = array('3','2','3','4');
                break;
            case 5:
                $column_sizes = explode("-", $footer_column5);
                break;
            default:
                $column_sizes = array('3','3','3','3');
                break;
        }

        // PREFOOTER MAP START
        $map_prefooter_default = gt3_option('map_prefooter_default');
        if (class_exists( 'RWMB_Loader' )) {
            $mb_map_prefooter = rwmb_meta('mb_map_prefooter', array(), $id);
            if (!empty($mb_map_prefooter) && $mb_map_prefooter != 'default') {
                $map_prefooter_default = $mb_map_prefooter;
            }
        }
        if ($map_prefooter_default == '1' || $map_prefooter_default == 'show') {
            // Args from Theme Options
            $zoom_map = gt3_option("zoom_map");
            $custom_map_style = gt3_option("custom_map_style");
            $custom_map_code = gt3_option("custom_map_code");
            $google_map_latitude = gt3_option("google_map_latitude");
            $google_map_longitude = gt3_option("google_map_longitude");
            $map_marker_info = gt3_option("map_marker_info");
            $map_marker_info_street_number = gt3_option("map_marker_info_street_number");
            $map_marker_info_street = gt3_option("map_marker_info_street");
            $map_marker_info_descr = gt3_option("map_marker_info_descr");

            $info_street_number = $info_street = $info_descr = $info_divider = '';
            if (!empty($map_marker_info_street_number) && strlen($map_marker_info_street_number) > 0) {
                $info_street_number = '<div class="marker_info_street_number">' . esc_html($map_marker_info_street_number) . '</div>';
            }
            if (!empty($map_marker_info_street) && strlen($map_marker_info_street) > 0) {
                $info_street = '<div class="marker_info_street">' . esc_html($map_marker_info_street) . '</div>';
            }
            if (!empty($map_marker_info_descr) && strlen($map_marker_info_descr) > 0) {
                $info_descr = '<div class="marker_info_desc">' . esc_html($map_marker_info_descr) . '</div>';
            }
            if (!empty($info_descr) && (!empty($info_street_number) || !empty($info_street))) {
                $info_divider = '<div class="marker_info_divider"></div>';
            }
            $marker_content = $info_street_number . $info_street . $info_divider . $info_descr;
            $rand = substr(md5(mt_rand(1000,9999999)),0,8);
            $custom_map_code = json_decode($custom_map_code);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $custom_map_code = array();
            }
            wp_enqueue_script('google-maps-api');
            ?>
            <div class="gt3_core_elementor_map">
                <div class="map-core-canvas map-id-<?php echo esc_attr($rand); ?>"></div>
                <?php if ($map_marker_info == true) { ?>
                    <div class="content_core_popup">
                        <div class="map_info_marker">
                            <div class="map_info_marker_content"><?php echo (($marker_content)); ?></div>
                        </div>
                    </div>
                <?php } ?>
                <script>
                    function gt3_core_initialize_map_<?php echo esc_attr($rand); ?>() {
                        <?php if ($custom_map_style == true && count($custom_map_code)) { ?>
                        var styleArray = <?php echo json_encode($custom_map_code); ?>;
                        <?php } else { ?>
                        var styleArray = [
                            {
                                "featureType": "water",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#e9e9e9"
                                    },
                                    {
                                        "lightness": 17
                                    }
                                ]
                            },
                            {
                                "featureType": "landscape",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f5f5f5"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 17
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 29
                                    },
                                    {
                                        "weight": 0.2
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 18
                                    }
                                ]
                            },
                            {
                                "featureType": "road.local",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "featureType": "poi",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f5f5f5"
                                    },
                                    {
                                        "lightness": 21
                                    }
                                ]
                            },
                            {
                                "featureType": "poi.park",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dedede"
                                    },
                                    {
                                        "lightness": 21
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "visibility": "on"
                                    },
                                    {
                                        "color": "#ffffff"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "saturation": 36
                                    },
                                    {
                                        "color": "#333333"
                                    },
                                    {
                                        "lightness": 40
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.icon",
                                "stylers": [
                                    {
                                        "visibility": "off"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f2f2f2"
                                    },
                                    {
                                        "lightness": 19
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#fefefe"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#fefefe"
                                    },
                                    {
                                        "lightness": 17
                                    },
                                    {
                                        "weight": 1.2
                                    }
                                ]
                            }
                        ];
                        <?php } ?>

                        definePopupClass();

                        var myLatlng = new google.maps.LatLng(<?php echo esc_attr($google_map_latitude); ?>, <?php echo esc_attr($google_map_longitude); ?>);

                        var mapOptions = {
                            zoom: <?php echo esc_attr($zoom_map); ?>,
                            scrollwheel: false,
                            center: myLatlng,
                            mapTypeId: google.maps.MapTypeId.ROADMAP,
                            styles: styleArray
                        };

                        var map = new google.maps.Map(document.getElementsByClassName('map-id-<?php echo esc_attr($rand); ?>')[0], mapOptions);

                        var marker = new google.maps.Marker({
                            position: myLatlng,
                            map: map,
                            icon: '<?php echo esc_url(gt3_option("custom_map_marker")); ?>'
                        });

                        <?php if ($map_marker_info == true) { ?>
                        popup = new Popup(
                            myLatlng,
                            document.getElementsByClassName('content_core_popup')[0]);
                        popup.setMap(map);
                        <?php } ?>

                    }

                    function definePopupClass() {
                        Popup = function(position, content) {
                            this.position = position;

                            content.classList.add('popup-bubble-content');

                            var pixelOffset = document.createElement('div');
                            pixelOffset.classList.add('popup-bubble-anchor');
                            pixelOffset.appendChild(content);

                            this.anchor = document.createElement('div');
                            this.anchor.classList.add('popup-tip-anchor');
                            this.anchor.appendChild(pixelOffset);

                            // Optionally stop clicks, etc., from bubbling up to the map.
                            this.stopEventPropagation();
                        };
                        // NOTE: google.maps.OverlayView is only defined once the Maps API has
                        // loaded. That is why Popup is defined inside initMap().
                        Popup.prototype = Object.create(google.maps.OverlayView.prototype);

                        /** Called when the popup is added to the map. */
                        Popup.prototype.onAdd = function() {
                            this.getPanes().floatPane.appendChild(this.anchor);
                        };

                        /** Called when the popup is removed from the map. */
                        Popup.prototype.onRemove = function() {
                            if (this.anchor.parentElement) {
                                this.anchor.parentElement.removeChild(this.anchor);
                            }
                        };

                        /** Called when the popup needs to draw itself. */
                        Popup.prototype.draw = function() {
                            var divPosition = this.getProjection().fromLatLngToDivPixel(this.position);
                            // Hide the popup when it is far out of view.
                            var display =
                                Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000 ?
                                    'block' :
                                    'none';

                            if (display === 'block') {
                                this.anchor.style.left = divPosition.x + 'px';
                                this.anchor.style.top = divPosition.y + 'px';
                            }
                            if (this.anchor.style.display !== display) {
                                this.anchor.style.display = display;
                            }
                        };

                        /** Stops clicks/drags from bubbling up to the map. */
                        Popup.prototype.stopEventPropagation = function() {
                            var anchor = this.anchor;
                            anchor.style.cursor = 'auto';
                            ['click', 'dblclick', 'contextmenu', 'wheel', 'mousedown', 'touchstart', 'pointerdown'].forEach(function(event) {
                                anchor.addEventListener(event, function(e) {
                                    e.stopPropagation();
                                });
                            });
                        };
                    }
                    jQuery(document).ready(function(){
                        if ('google' in window) {
                            gt3_core_initialize_map_<?php echo esc_attr($rand); ?>();
                        } else {
                            setTimeout(function () {
                                if ('google' in window) {
                                    gt3_core_initialize_map_<?php echo esc_attr($rand); ?>();
                                }
                            }, 3000);
                        }
                    });
                </script>
            </div>
        <?php }
        // PREFOOTER MAP END
        // FOOTER OUT
        if ($footer_switch || $copyright_switch || $pre_footer_switch) {
            echo "<footer class='main_footer fadeOnLoad clearfix'".$footer_cont_style." id='footer'>";
                // Footer Map
                // Back2Top
                $mb_footer_switch = class_exists('RWMB_Loader') ? rwmb_meta('mb_footer_switch', array(), $id) : '';

                if ($pre_footer_switch && !empty($pre_footer_editor)) {
                    echo "<div class='pre_footer".$pre_footer_class."'".($footer_full_width !== 'default' ? $pre_footer_cont_style : '').">";
                        echo (($footer_full_width)) !== 'default' ? "" : "<div class='container'".$pre_footer_cont_style.">";
                            echo "<div class='row'".$pre_footer_style.">";
                                echo (($footer_full_width)) == 'stretch_footer' ? "<div class='container'>" : "";
                                    echo "<div class='span12'>";
                                    echo do_shortcode( $pre_footer_editor );
                                    echo "</div>";
                                echo  (($footer_full_width)) !== 'stretch_footer' ? "</div>" : "";
                            echo "</div>";
                        echo  (($footer_full_width)) !== 'default' ? "" : "</div>";
                    echo "</div>";
                }

                if ($footer_switch) {
                    $is_any_footer_widget = false;
                    for ($i=0; $i < (int)$footer_column; $i++) {
                        if ($mb_footer_switch == 'yes') {
                            if (is_active_sidebar( ${'mb_footer_sidebar_'.($i+1)} )) {
                                $is_any_footer_widget = is_dynamic_sidebar( ${'mb_footer_sidebar_'.($i+1)} );
                            }
                        }else{
                            if (is_active_sidebar( 'footer_column_' . ($i+1) )) {
                                $is_any_footer_widget = is_dynamic_sidebar( 'footer_column_' . ($i+1) );
                            }
                        }
                    }
                }

                if ($footer_switch && $is_any_footer_widget) {
                    echo "<div class='top_footer column_".(int)$footer_column.$footer_class."'>";
                        echo (($footer_full_width)) !== 'default' ? "" : "<div class='container'>";
                            echo (($footer_full_width)) == 'stretch_footer' ? "<div class='container'>" : "";
                                echo "<div class='row'".$footer_style.">";
                                    for ($i=0; $i < (int)$footer_column; $i++) {
                                        echo "<div class='span".$column_sizes[$i]."'>";
                                            if ($mb_footer_switch == 'yes') {
                                                if (is_active_sidebar( ${'mb_footer_sidebar_'.($i+1)} )) {
                                                    dynamic_sidebar( ${'mb_footer_sidebar_'.($i+1)} );
                                                }
                                            }else{
                                                if (is_active_sidebar( 'footer_column_' . ($i+1) )) {
                                                    dynamic_sidebar( 'footer_column_' . ($i+1) );
                                                }
                                            }
                                        echo "</div>";
                                    }
                                echo "</div>";
                            echo  (($footer_full_width)) == 'stretch_footer' ? "</div>" : "";
                        echo  (($footer_full_width)) !== 'default' ? "" : "</div>";
                    echo "</div>";
                }

                if ($copyright_switch && !empty($copyright_editor)) {
                    echo "<div class='copyright".$copyright_class."'".$copyright_cont_style.">";
                        echo  (($footer_full_width)) !== 'default' ? "" : "<div class='container'".$copyright_cont_border_style.">";
                            echo  (($footer_full_width)) == 'stretch_footer' ? "<div class='container'>" : "";
                                echo "<div class='row'".$copyright_style.">";
                                    echo "<div class='span12'>";
                                    echo do_shortcode( $copyright_editor );
                                    echo "</div>";
                                echo "</div>";
                            echo  (($footer_full_width)) == 'stretch_footer' ? "</div>" : "";
                        echo  (($footer_full_width)) !== 'default' ? "" : "</div>";
                    echo "</div>";
                }

            echo "</footer>";
        }
    }
}

if (!function_exists('gt3_option_compare')) {
    function gt3_option_compare($opt_name,$meta_conditional = false,$meta_value = false,$meta_conditional2 = false,$meta_value2 = false){
        $option = gt3_option($opt_name);
	    $id = gt3_get_queried_object_id();
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            if ($meta_conditional != false) {
                if ($meta_conditional2 != false) {
                    if (rwmb_meta($meta_conditional) == $meta_value &&rwmb_meta($meta_conditional2, array(), $id) == $meta_value2) {
                        $option = rwmb_meta('mb_'.$opt_name, array(), $id);
                    }
                }else{
                    if (rwmb_meta($meta_conditional, array(), $id) == $meta_value ) {
                        $option = rwmb_meta('mb_'.$opt_name, array(), $id);
                    }
                }
            }else{
                $option = rwmb_meta('mb_'.$opt_name, array(), $id);
            }
        }
        return $option;
    }
}

// need for comparing (theme_options or metabox) and out html with background settings
if (!function_exists('gt3_background_render')) {
    function gt3_background_render($opt_name,$meta_conditional = false,$meta_value = false,$return_array = false,$force_bg_style = false){
        $image_array = gt3_option($opt_name."_bg_image");
        $bg_src = !empty($image_array['background-image']) ? $image_array['background-image'] : '';
        $bg_repeat = !empty($image_array['background-repeat']) ? $image_array['background-repeat'] : '';
        $bg_size = !empty($image_array['background-size']) ? $image_array['background-size'] : '';
        $attachment = !empty($image_array['background-attachment']) ? $image_array['background-attachment'] : '';
        $position = !empty($image_array['background-position']) ? $image_array['background-position'] : '';


	    $id = gt3_get_queried_object_id();
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            if ($meta_conditional != false) {
                $mb_conditional = rwmb_meta($meta_conditional, array(), $id);

                if ($mb_conditional == $meta_value) {
                    $bg_src = rwmb_meta('mb_'.$opt_name.'_bg_image', array(), $id);
                    $bg_src = !empty($bg_src) ? $bg_src : '';
                    if (!empty($bg_src)) {
                        $bg_src = array_values($bg_src);
                        $bg_src = $bg_src[0]['url'];
                    }

                    if (!empty($bg_src) || $force_bg_style) {
                        $bg_repeat = rwmb_meta('mb_'.$opt_name.'_bg_repeat', array(), $id);
                        $bg_repeat = !empty($bg_repeat) ? $bg_repeat : '';
                        $bg_size = rwmb_meta('mb_'.$opt_name.'_bg_size', array(), $id);
                        $bg_size = !empty($bg_size) ? $bg_size : '';
                        $attachment = rwmb_meta('mb_'.$opt_name.'_bg_attachment', array(), $id);
                        $attachment = !empty($attachment) ? $attachment : '';
                        $position = rwmb_meta('mb_'.$opt_name.'_bg_position', array(), $id);
                        $position = !empty($position) ? $position : '';
                    }else{
                        $bg_repeat = '';
                        $bg_size = '';
                        $attachment = '';
                        $position = '';
                    }
                }
            }
        }
        $bg_styles = array();

        $bg_styles['background-image'] = !empty($bg_src) ? 'background-image:url('.esc_url($bg_src).');' : '';

        if (!empty($bg_src) || $force_bg_style) {
            $bg_styles['background-size'] = !empty($bg_size) ? 'background-size:'.esc_attr($bg_size).';' : '';
            $bg_styles['background-repeat'] = !empty($bg_repeat) ? 'background-repeat:'.esc_attr($bg_repeat).';' : '';
            $bg_styles['background-attachment'] = !empty($attachment) ? 'background-attachment:'.esc_attr($attachment).';' : '';
            $bg_styles['background-position'] = !empty($position) ? 'background-position:'.esc_attr($position).';' : '';
        }

        if ($return_array) {
            return $bg_styles;
        }

        return implode('',$bg_styles);
    }
}

// return all sidebars
if (!function_exists('gt3_get_all_sidebar')) {
    function gt3_get_all_sidebar() {
        global $wp_registered_sidebars;
        $out = array('' => '' );
        if ( empty( $wp_registered_sidebars ) )
            return;
         foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar) :
            $out[$sidebar_id] = $sidebar['name'];
         endforeach;
         return $out;
    }
}

function gt3_get_attachment( $attachment_id ) {
    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}

// GT3 Featured Post CSS
add_action('wp_enqueue_scripts', 'gt3_blog_custom_styles_js');
function gt3_blog_custom_styles_js($custom_blog_css) {
    echo '
        <script type="text/javascript">
            var custom_blog_css = "' . $custom_blog_css . '";
            if (document.getElementById("custom_blog_styles")) {
                document.getElementById("custom_blog_styles").innerHTML += custom_blog_css;
            } else if (custom_blog_css !== "") {
                document.head.innerHTML += \'<style id="custom_blog_styles" type="text/css">\'+custom_blog_css+\'</style>\';
            }
        </script>
    ';
}

if (!function_exists('gt3_showJSInFooter')) {
    function gt3_showJSInFooter()
    {
        if (isset($GLOBALS['showOnlyOneTimeJS']) && is_array($GLOBALS['showOnlyOneTimeJS'])) {
            foreach ($GLOBALS['showOnlyOneTimeJS'] as $id => $js) {
                echo esc_js($js);
            }
        }
    }
}
add_action('wp_footer', 'gt3_showJSInFooter');

//* Tiny mce adding *//
function gt3_mce_buttons() {
    if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
    add_filter('mce_external_plugins', 'gt3_add_external_plugins','11');
    add_filter('mce_buttons_3', 'gt3_mce_buttons_register_button','11');
    add_filter('mce_buttons_2', 'gt3_mce_buttons_2','11');
    }
}
add_action('init', 'gt3_mce_buttons');

function gt3_add_external_plugins($plugin_array) {
$plugin_array['gt3_external_tinymce_plugins'] = get_template_directory_uri() . '/core/admin/js/tinymce-button.js';
return $plugin_array;
}
function gt3_mce_buttons_register_button($buttons) {
array_push($buttons, 'SocialIcon', 'DropCaps', 'Highlighter', 'TitleLine', 'LinkStyling', 'ListStyle', 'Columns', 'ToolTip');
return $buttons;
}
function gt3_mce_buttons_2($buttons) {
    array_unshift($buttons, 'styleselect');
    return $buttons;
}

function gt3_tiny_mce_before_init($settings) {
$settings['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';
$style_formats = array(
array(
'title' => esc_html__('Font Weight', 'agrosector'), 'items' => array(
array('title' => esc_html__('Default', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => 'inherit')),
array('title' => esc_html__('Lightest (100)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '100')),
array('title' => esc_html__('Lighter (200)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '200')),
array('title' => esc_html__('Light (300)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '300')),
array('title' => esc_html__('Normal (400)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '400')),
array('title' => esc_html__('Medium (500)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '500')),
array('title' => esc_html__('Semi-Bold (600)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '600')),
array('title' => esc_html__('Bold (700)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '700')),
array('title' => esc_html__('Bolder (800)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '800')),
array('title' => esc_html__('Extra Bold (900)', 'agrosector'), 'inline' => 'span', 'classes' => 'gt3_font-weight', 'styles' => array('font-weight' => '900'))
),
),
);

$settings['style_formats']           = str_replace('"', "'", json_encode($style_formats));
$settings['extended_valid_elements'] = 'span[*],a[*],i[*]';
return $settings;
}
add_filter('tiny_mce_before_init', 'gt3_tiny_mce_before_init');

function gt3_theme_add_editor_styles() {
add_editor_style('css/font-awesome.min.css');
add_editor_style('css/tiny_mce.css');
}
add_action('current_screen', 'gt3_theme_add_editor_styles');


function wpdocs_theme_add_editor_styles() {
    add_editor_style( 'css/font-awesome.min.css' );
    add_editor_style( 'css/tiny_mce.css' );
}
add_action( 'current_screen', 'wpdocs_theme_add_editor_styles' );
// end


function gt3_categories_postcount_filter ($variable) {
    preg_match('/(class="count")/', $variable, $matches);
    if (empty($matches)) {
        $variable = str_replace('</a> (', '</a> <span class="post_count">', $variable);
        $variable = str_replace('</a>&nbsp;(', '</a>&nbsp;<span class="post_count">', $variable);
        $variable = str_replace(')', '</span>', $variable);
    }
    return $variable;
}
add_filter('get_archives_link','gt3_categories_postcount_filter');
add_filter('wp_list_categories','gt3_categories_postcount_filter');

if (!function_exists('gt3_open_graph_meta')) {
    add_action( 'wp_head', 'gt3_open_graph_meta', 5 );
    function gt3_open_graph_meta(){
        global $post;
        if ( !is_singular()) //if it is not a post or a page
            return;
            echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '"/>';
            echo '<meta property="og:type" content="article"/>';
            echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '"/>';
            echo '<meta property="og:site_name" content="'.esc_html(get_bloginfo('name')).'"/>';
        if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
            $header_logo_src = gt3_option("header_logo");
	        $id = gt3_get_queried_object_id();
	        if ( class_exists( 'RWMB_Loader' ) && $id !== 0 ) {
		        $mb_header_presets = rwmb_meta( 'mb_header_presets', array(), $id );
		        $presets           = gt3_option( 'gt3_header_builder_presets' );
		        if ( $mb_header_presets != 'default' && isset( $mb_header_presets ) && ! empty( $presets[ $mb_header_presets ] ) && ! empty( $presets[ $mb_header_presets ]['preset'] ) ) {
			        $preset             = $presets[ $mb_header_presets ]['preset'];
			        $preset             = json_decode( $preset, true );
			        $mb_header_logo_src = gt3_option_presets( $preset, 'header_logo' );
			        $header_logo_src    = ! empty( $mb_header_logo_src ) ? $mb_header_logo_src : $header_logo_src;
		        }
	        }
            $header_logo_src = !empty($header_logo_src) ? $header_logo_src['url'] : '';
            echo '<meta property="og:image" content="' . esc_url($header_logo_src) . '"/>';
        }
        else{
            $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );
            echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
        }
    }
}

if (!function_exists('gt3_translateColumnWidthToSpan')) {
    function gt3_translateColumnWidthToSpan( $gt3_width ) {
        preg_match( '/(\d+)\/(\d+)/', $gt3_width, $matches );

        if ( ! empty( $matches ) ) {
            $part_x = (int) $matches[1];
            $part_y = (int) $matches[2];
            if ( $part_x > 0 && $part_y > 0 ) {
                $value = ceil( $part_x / $part_y * 12 );
                $value2 = ceil( ( 1 - $part_x / $part_y ) * 12 );
                if ( $value > 0 && $value <= 12 ) {
                    $gt3_width = array();
                    $gt3_width[] = $value;
                    $gt3_width[] = $value2;
                }
            }
        }
        return $gt3_width;
    }
}

function gt3_get_queried_object_id(){
    $id = get_queried_object_id();
    if ( $id == 0 && class_exists('WooCommerce') ) {
        if (is_shop()) {
            $id = get_option('woocommerce_shop_page_id');
        }else if (is_cart()) {
            $id = get_option('woocommerce_cart_page_id');
        }else if (is_checkout()) {
            $id = get_option('woocommerce_checkout_page_id');
        }
    }
    return $id;
}

if ( class_exists('WooCommerce') ) {
    require_once( get_template_directory() . '/woocommerce/wooinit.php' ); // Woocommerce init file
}

if (!function_exists('getSolidColorFromImage')) {
    function getSolidColorFromImage($filepath) {
        $attach_id = gt3_get_image_id($filepath);
        if (!empty($attach_id)) {
            $solid_color = get_post_meta( $attach_id, 'solid_color', true);
            if (!empty($solid_color)) {
                return $solid_color;
            }
        }

        $type = wp_check_filetype($filepath);
        if (!empty($type) && is_array($type) && !empty($type['ext'])) {
            $type = $type['ext'];
        }else{
            return '#D3D3D3';
        }
        $allowedTypes = array(
            'gif',  // [] gif
            'jpg',  // [] jpg
            'png',  // [] png
            'bmp'   // [] bmp
        );
        if (!in_array($type, $allowedTypes)) {
            return '#D3D3D3';
        }
        $im = false;
        switch ($type) {
            case 'gif' :
                $im = imageCreateFromGif($filepath);
            break;
            case 'jpg' :
                $im = imageCreateFromJpeg($filepath);
            break;
            case 'png' :
                $im = imageCreateFromPng($filepath);
            break;
            case 'bmp' :
                $im = imageCreateFromBmp($filepath);
            break;
        }

        if ($im) {
            $thumb=imagecreatetruecolor(1,1);
            imagecopyresampled($thumb,$im,0,0,0,0,1,1,imagesx($im),imagesy($im));
            $mainColor=strtoupper(dechex(imagecolorat($thumb,0,0)));
            update_post_meta( $attach_id, 'solid_color', $mainColor );
            return $mainColor;
        }else{
            return '#D3D3D3';
        }
    }
}


function gt3_get_image_id($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
        return $attachment[0];
}

if ( class_exists( 'GT3_Core_Elementor' ) ) {
    require_once( get_template_directory().'/elementor/init.php' ); // Theme elementor init file
}

add_filter('gt3/elementor/core/cpt/register', function(){
    return array(
        'team',
        'portfolio',
        'project',
    );
});
