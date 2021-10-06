<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class MenuBlockImportHelper
 */
class MenuBlockExportAssets {

	private $exist_assets = array();

	private $regexp_strings_default = array(

		// ct_bg_image="3631"
		'ct_bg_image'              => '@ct_bg_image="(\d*?)"@im',

		//[ult_content_box bg_type="bg_image" bg_repeat="no-repeat" bg_image="8307" box_shadow="horizontal:px|vertical:px|blur:px|spread:px|style:none|" min_height="619" hover_box_shadow="horizontal:px|vertical:px|blur:px|spread:px|style:none|" bg_position="center"]
		'ult_content_box_bg_image' => '@\[ult_content_box (.*?)bg_image="(\d*?)"@im',

		// [vc_single_image image="2928".
		'vc_single_image'          => '@\[vc_single_image image="(\d*?)"@im',

		// banner_image="2928|  // icon_img="637|  // btn_img="641|
		'attr_image_id'            => '@(main|thumb|icon|btn|banner|bg)_(image|img)="(id\^)*?(\d*?)\|@im',

		// banner_image="11|http://test.local/wp-content/uploads/2020/07/splash1400x1000.png"
		// icon_img="637|http://test.local/wp-content/uploads/2020/07/splash84x84.png"
		// btn_img="641|http://test.local/wp-content/uploads/2020/07/splash300x200.png"
		'attr_image_url'           => '@(main|thumb|icon|btn|banner|bg)_(image|img)="(id\^)*?([^\"]*?)\|([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)"@im',

		// banner_image="id^2928|
		'attr_banner_image_id'     => '@(banner_image|icon_img)="id\^(\d*?)\|@im',
		// banner_image="id^8319|/wp-content/uploads/2020/07/splash600x600.png|caption^null|alt^null|title^splash600x600|description^null"
		'attr_banner_image_url'    => '@(banner_image|icon_img)="id\^([^\"]*?)\|([^\"]*?)(\/wp-content\/uploads\/)([^\|\"]+?)\|@im',

		// [ultimate_google_map height="680" lat="51.50284" lng="-0.14010" zoom="12" scrollwheel="disable" marker_icon="custom" icon_img="id^8304|url^http://test.local/wp-content/uploads/2020/07/splash84x84.png|caption^null|alt^null|title^splash84x84|description^null" top_margin="none"
		'attr_img_ua_id'           => '@(bg_image_new|icon_img|img_separator)="id\^(\d*?)\|([^\"]*?)url\^([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)\|([^\"]*?)"@im',
		'attr_img_ua_url'          => '@(bg_image_new|icon_img|img_separator)="id\^([^\"]*?)\|([^\"]*?)url\^([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)\|([^\"]*?)"@im',

		// "portfolio-image":"4445,4420"
		'portfolio_image'          => '@\{"override_global"(.+?)"portfolio-image":"([^\"]*?)"([^\}]+?)\}@im',

		// [gallery columns="4" size="full" ids="4756,4755,4754,4752,4753,4751,4750,4749"]
		'gallery'                  => '@\[gallery([^\]]+?)ids="([^\]]*?)"([^\]]*?)\]@im',

		// [vc_images_carousel images="6425,6428,6450,6424,6423,6426,6421"
		'vc_images_carousel'       => '@\[vc_images_carousel([^\]]+?)images="([^\"]*?)"([^\]]*?)\]@im',

		// [vc_masonry_media_grid include="6425,6428,6450"
		'vc_masonry_media_grid'    => '@\[vc_masonry_media_grid([^\]]+?)include="([^\"]*?)"([^\]]*?)\]@im',

		// [et_pb_image src="http://test.local/wp-content/uploads/2020/04/image.jpg"]
		'et_pb_image'              => '@\[et_pb_image([^\]]+?)src="([^\"]*?)"([^\]]*?)\]@im',

		// [et_pb_blurb image="http://test.local/wp-content/uploads/2020/04/image.jpg"]
		'et_pb_blurb'              => '@\[et_pb_blurb([^\]]+?)image="([^\"]*?)"([^\]]*?)\]@im',

		// [et_pb_gallery gallery_ids="77,74,64"]
		'et_pb_gallery'            => '@\[et_pb_gallery([^\]]+?)gallery_ids="([^\"]*?)"([^\]]*?)\]@im',

		// background-image: url(/wp-content/uploads/2020/07/splash1400x1000.png)
		'css_url_in_brackets'  => '@url\(([^\)]*?)(\/wp-content\/uploads\/)([^\)]+?)\)@im',

		// <img class="size-full" src="http://test.local/wp-content/uploads/2020/07/splash1920x1200.png" alt="" width="1920" height="1280" />
		'url_in_img_and_a_tag' => '@<(a|img)([^\>]+?)(href|src)=[\'\"]([^\"\']*?)(\/wp-content\/uploads\/)([^\"\']+?)[\'\"]([^\>]*?)>@im',

		// srcset="http://test.local/wp-content/uploads/2020/05/color-psychology-300x165.png 300w, http://gm.local/wp-content/uploads/2021/05/color-psychology-768x422.png 768w"
		'clear_srcset'         => '@<img([^\>]+?)srcset="([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)"([^\>]*?)>@im',

		// [vc_column width="1/3" css=".vc_custom_1519716145703{background-image: url(/wp-content/uploads/2020/07/splash600x600.png?id=8319) !important;background-position: center !important;background-repeat: no-repeat !important;background-size: cover !important;}"]
		'url_in_css_tag'       => '@url\([\'"]?([^\)]*?)(\/wp-content\/uploads\/)([^\)]+?)(\?id\=\d*?)*?[\'"]?\)@im',

		//{"url":"http://test.local/wp-content/uploads/2021/05/color-psychology.png","id":10044}
		'url_in_json_param'    => '@[\'\"]{1}url[\'\"]{1}:[\'\"]{1}([^\'\"]*?)(\/wp-content\/uploads\/)([^\'\"]+?)[\'\"]{1}([^\}]*?)\}@im',

	);


	public function __construct() {
		// ...
	}


	public function get_pattern_id( $id ) {

		if ( ! empty( intval( $id ) ) ) {
			$this->set_exist_asset_by_id( intval( $id ) );
		}

		// Default params
		$return_asset = array(
			'id'      => $id,
			'type'    => 'ASSET_ID',
			'pattern' => $id,
			'url'     => '',
		);

		if ( ! empty( $this->exist_assets[ $id ] ) ) {

			$return_asset['id']      = $id;
			$return_asset['pattern'] = '%%%:' . $return_asset['type'] . ':' . $id . '%%%';
			$return_asset['url']     = $this->exist_assets[ $id ]['url'];

		}

		return $return_asset;
	}

	/**
	 * Return assets data such as ID, patter and original url
	 *
	 * @param string $url Part of URL or URI of asset
	 *
	 * @return array
	 */
	public function get_pattern_url( $url ) {

		if ( ! empty( $url ) ) {
			$this->set_exist_asset_by_url( $url );
		}

		// Default params.
		$return_asset = array(
			'id'      => $url,
			'type'    => 'ASSET_URL',
			'pattern' => $url,
			'url'     => $url,
		);

		foreach ( $this->exist_assets as $asset_id => $asset_data ) {
			if ( ! empty( $asset_data['url'] ) ) {
				if ( substr_count( $asset_data['url'], $url ) ) {

					$return_asset['id']      = strval( $asset_data['id'] );
					$return_asset['pattern'] = '%%%:' . $return_asset['type'] . ':' . strval( $asset_data['id'] ) . '%%%';
					$return_asset['url']     = $asset_data['url'];

					break;
				}
			}
		}

		return $return_asset;
	}

	public function set_exist_asset_by_id( int $asset_id ) {
		if ( empty( $asset_id ) ) {
			return false;
		}

		if ( ! empty( $this->exist_assets[ $asset_id ] ) ) {
			return true;
		}

		$asset_url = esc_url( wp_get_attachment_url( $asset_id ) );


		if ( empty( $asset_url ) ) {
			return false;
		}

		$this->exist_assets[ $asset_id ] = array(
			'id'            => $asset_id,
			'url'           => $asset_url,
			'media_details' => wp_get_attachment_image_src( $asset_id, 'full' ),
		);

		return true;
	}

	public function set_exist_asset_by_url( string $asset_url ) {
		if ( empty( $asset_url ) ) {
			return false;
		}

		foreach ( $this->exist_assets as $index => $exist_asset ) {
			if ( $asset_url === $exist_asset['url'] ) {
				return true;
			}
		}

		$asset_id = $this->attachment_url_to_post_id( $asset_url );


		if ( empty( $asset_id ) ) {
			return false;
		}

		$this->exist_assets[ $asset_id ] = array(
			'id'            => $asset_id,
			'url'           => $asset_url,
			'media_details' => wp_get_attachment_image_src( $asset_id, 'full' ),
		);

		return true;
	}

	/**
	 * Gets attachment ID by passed URL.
	 *
	 * @version 1.1
	 * @author  Kama (wp-kama.ru)
	 *
	 * @param null $url File URI in any format. Even `image.jpg`.
	 *
	 * @return bool|int Attachment id.
	 */
	function attachment_url_to_post_id( $url = null ) {
		global $wpdb;

		if ( ! $url ) {
			return false;
		}

		$name = basename( $url ); // имя файла

		// удалим размер миниатюры (-80x80)
		$name = preg_replace( '~-(?:\d+x\d+|scaled|rotated)~', '', $name );

		// удалим расширение
		$name = preg_replace( '~\.[^.]+$~', '', $name );

		$post_name = sanitize_title( $name );

		// фильтруем по индексному полю post_name
		$sql = $wpdb->prepare(
			"SELECT ID, guid FROM $wpdb->posts WHERE post_name LIKE %s AND post_title = %s AND post_type = 'attachment'",
			$wpdb->esc_like( $post_name ) . '%', $name
		);

		$attaches = $wpdb->get_results( $sql );

		if ( ! $attaches ) {
			return false;
		}

		$attachment_id = reset( $attaches )->ID;

		// найдено несколько, определимся какую точно брать
		if ( count( $attaches ) > 1 ) {

			$url_path = parse_url( $url, PHP_URL_PATH );

			foreach ( $attaches as $attach ) {

				if ( false !== strpos( $attach->guid, $url_path ) ) {
					$attachment_id = $attach->ID;
					break;
				}
			}
		}

		return (int) $attachment_id;
	}

	/**
	 * Main walker for set assets placeholders for content.
	 * Return new contend and array with assts id.
	 *
	 * @param string $content
	 * @param array  $regexp_strings
	 * @param array  $preset_need_image
	 *
	 * @return array
	 */
	public function set_placeholders( $content, $regexp_strings = array(), $preset_need_image = array() ) {

		$do_regexp = $this->regexp_strings_default;

		if ( ! empty( $regexp_strings ) ) {
			foreach ( $regexp_strings as $index => $pattern ) {
				$do_regexp[ $index ] = $pattern;
			}
		}

		// Prepare default empty array.
		$work_data = array(
			'content_images' => $preset_need_image,
			'content'        => $content,
		);

		foreach ( $do_regexp as $type => $pattern ) {

			if ( empty( $pattern ) ) {
				continue;
			}

			$class_method = 'replace_' . $type;

			if ( ! method_exists( $this, $class_method ) ) {
				continue;
			}

			$raw_data = $this->{$class_method}( $pattern, $work_data['content'] );

			$work_data['content'] = $raw_data['content'];
			foreach ( $raw_data['content_images'] as $index => $content_image ) {

				$work_data['content_images'][ $index ]['data']['url'] = $content_image['url'];
				if ( is_array( $content_image['pattern'] ) ) {
					foreach ( $content_image['pattern'] as $pattern ) {
						$work_data['content_images'][ $index ]['pattern'][ $content_image['type'] ][] = $pattern;
					}
				} else {
					$work_data['content_images'][ $index ]['pattern'][ $content_image['type'] ][] = $content_image['pattern'];
				}

			}

		}

		return $work_data;


	}


	/**
	 * Find and replace for vc_single_image type
	 * search example: [vc_single_image image="2928"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_vc_single_image( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}


			if ( empty( $matches[1][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[1][0] );

			$work_data['content_images'][ $matches[1][0] ] = $returned_asset;

			$old_param = '[vc_single_image image="' . $matches[1][0] . '"';
			$new_param = '[vc_single_image image="' . $returned_asset['pattern'] . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_vc_single_image().


	/**
	 * Find and replace for ult_content_box_bg_image type
	 * search example: //[ult_content_box bg_type="bg_image" bg_repeat="no-repeat" bg_image="8307" box_shadow="horizontal:px|vertical:px|blur:px|spread:px|style:none|" min_height="619" hover_box_shadow="horizontal:px|vertical:px|blur:px|spread:px|style:none|" bg_position="center"]
	 * #\[ult_content_box (.*?)bg_image="(\d*?)"#
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_ult_content_box_bg_image( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}


			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[2][0] );

			$work_data['content_images'][ $matches[2][0] ] = $returned_asset;

			$old_param = '[ult_content_box ' . $matches[1][0] . 'bg_image="' . $matches[2][0] . '"';
			$new_param = '[ult_content_box ' . $matches[1][0] . 'bg_image="' . $returned_asset['pattern'] . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_ult_content_box_bg_image().


	/**
	 * Find and replace for ct_bg_image type
	 * search example: // ct_bg_image="3631"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_ct_bg_image( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}


			if ( empty( $matches[1][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[1][0] );

			$work_data['content_images'][ $matches[1][0] ] = $returned_asset;

			$old_param = 'ct_bg_image="' . $matches[1][0] . '"';
			$new_param = 'ct_bg_image="' . $returned_asset['pattern'] . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_ct_bg_image().


	/**
	 * Find and replace for attr_image type
	 * search example: // banner_image="2928| // btn_img="998 // icon_img="9954
	 * (icon|btn|banner)_(image|img)="(\d*?)\|
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_image_id( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}
			//              %1                       %2      %3     %4
			// (main|thumb|icon|btn|banner|bg)_(image|img)="(id\^)*?(\d*?)\|

			if ( empty( $matches[4][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[4][0] );

			$work_data['content_images'][ $matches[4][0] ] = $returned_asset;

			$old_param = $matches[1][0] . '_' . $matches[2][0] . '="' . $matches[3][0] . $matches[4][0] . '|';
			$new_param = $matches[1][0] . '_' . $matches[2][0] . '="' . $matches[3][0] . $returned_asset['pattern'] . '|';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_image_id().


	/**
	 * Find and replace for replace_attr_image_url type
	 * search example:
	 *        // banner_image="11|http://test.local/wp-content/uploads/2020/07/splash1400x1000.png"
	 *        // icon_img="637|http://test.local/wp-content/uploads/2020/07/splash84x84.png"
	 *        // btn_img="641|http://test.local/wp-content/uploads/2020/07/splash300x200.png"
	 * (icon|btn|banner)_(image|img)="(.*?)\|(.*?)(\/wp-content\/uploads\/)(.+?)"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_image_url( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			//       %1              %2        %3     %4            %5              %6
			//(icon|btn|banner)_(image|img)="(.*?)\|(.*?)(\/wp-content\/uploads\/)(.+?)"

			if ( empty( $matches[6][0] ) ) {
				continue;
			}
			$returned_asset = $this->get_pattern_url( $matches[5][0] . $matches[6][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[4][0] . $matches[5][0] . $matches[6][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_image_url().


	/**
	 * Find and replace for attr_banner_image_id type
	 * search example: // // banner_image="id^2928|
	 * (banner_image|icon_img)="id\^(\d*?)\|
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_banner_image_id( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}
			//           %1                   %2
			// (banner_image|icon_img)="id\^(\d*?)\|

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[2][0] );

			$work_data['content_images'][ $matches[2][0] ] = $returned_asset;

			$old_param = $matches[1][0] . '="id^' . $matches[2][0] . '|';
			$new_param = $matches[1][0] . '="id^' . $returned_asset['pattern'] . '|';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_banner_image_id().


	/**
	 * Find and replace for replace_attr_image_url type
	 * search example:  // banner_image="id^8319|/wp-content/uploads/2020/07/splash600x600.png|caption^null|alt^null|title^splash600x600|description^null"
	 * (banner_image|icon_img)="id\^([^\"]*?)\|([^\"]*?)(\/wp-content\/uploads\/)([^\|\"]+?)\|
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_banner_image_url( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			//            %1                 %2          %3              %4                  %5
			//(banner_image|icon_img)="id\^([^\"]*?)\|([^\"]*?)(\/wp-content\/uploads\/)([^\|\"]+?)\|

			if ( empty( $matches[4][0] ) ) {
				continue;
			}
			$returned_asset = $this->get_pattern_url( $matches[4][0] . $matches[5][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[3][0] . $matches[4][0] . $matches[5][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_banner_image_url().


	/**
	 * Find and replace for attr_img_ua_id type
	 * search example:        // [ultimate_google_map height="680" lat="51.50284" lng="-0.14010" zoom="12" scrollwheel="disable" marker_icon="custom" icon_img="id^8304|url^http://test.local/wp-content/uploads/2020/07/splash84x84.png|caption^null|alt^null|title^splash84x84|description^null" top_margin="none"
	 * (icon_img|img_separator)="id\^(\d*?)\|(.*?)url\^(.*?)(\/wp-content\/uploads\/)(.+?)\|(.*?)"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_img_ua_id( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}
			//          %1                     %2      %3        %4           %5               %6     %7
			// (icon_img|img_separator)="id\^(\d*?)\|(.*?)url\^(.*?)(\/wp-content\/uploads\/)(.+?)\|(.*?)"

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_id( $matches[2][0] );

			$work_data['content_images'][ $matches[2][0] ] = $returned_asset;

			$old_param = $matches[1][0] . '="id^' . $matches[2][0] . '|';
			$new_param = $matches[1][0] . '="id^' . $returned_asset['pattern'] . '|';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_img_ua_id().


	/**
	 * Find and replace for attr_img_ua_url type
	 * search example:        // [ultimate_google_map height="680" lat="51.50284" lng="-0.14010" zoom="12" scrollwheel="disable" marker_icon="custom" icon_img="id^8304|url^http://test.local/wp-content/uploads/2020/07/splash84x84.png|caption^null|alt^null|title^splash84x84|description^null" top_margin="none"
	 * (icon_img|img_separator)="id\^(.*?)\|(.*?)url\^(.*?)(\/wp-content\/uploads\/)(.+?)\|(.*?)"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_attr_img_ua_url( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			//          %1                     %2     %3        %4           %5               %6     %7
			// (icon_img|img_separator)="id\^(.*?)\|(.*?)url\^(.*?)(\/wp-content\/uploads\/)(.+?)\|(.*?)"

			if ( empty( $matches[6][0] ) ) {
				continue;
			}
			$returned_asset = $this->get_pattern_url( $matches[5][0] . $matches[6][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[4][0] . $matches[5][0] . $matches[6][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while

		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_attr_img_ua_url().


	/**
	 * Find and replace for portfolio_image type
	 * search example: //  "portfolio-image":"4445,4420"
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_portfolio_image( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$pieces             = explode( ',', $matches[2][0] );
			$pieces_new_pattern = array();

			foreach ( $pieces as $piece ) {
				$returned_asset                        = $this->get_pattern_id( $piece );
				$pieces_new_pattern[]                  = $returned_asset['pattern'];
				$work_data['content_images'][ $piece ] = $returned_asset;
			}

			$old_param = '"portfolio-image":"' . $matches[2][0] . '"';
			$new_param = '"portfolio-image":"' . implode( ',', $pieces_new_pattern ) . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_portfolio_image().


	/**
	 * Find and replace for gallery type
	 * search example: //  [gallery columns="4" size="full" ids="4756,4755,4754,4752,4753,4751,4750,4749"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_gallery( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$pieces             = explode( ',', $matches[2][0] );
			$pieces_new_pattern = array();

			foreach ( $pieces as $piece ) {
				$returned_asset                        = $this->get_pattern_id( $piece );
				$pieces_new_pattern[]                  = $returned_asset['pattern'];
				$work_data['content_images'][ $piece ] = $returned_asset;
			}

			$old_param = 'ids="' . $matches[2][0] . '"';
			$new_param = 'ids="' . implode( ',', $pieces_new_pattern ) . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_gallery().


	/**
	 * Find and replace for vc_images_carousel type
	 * search example: //  [vc_images_carousel images="6425,6428,6450,6424,6423,6426,6421" img_size="full" speed="3000" autoplay="yes" hide_prev_next_buttons="yes" wrap="yes"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_vc_images_carousel( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$pieces             = explode( ',', $matches[2][0] );
			$pieces_new_pattern = array();

			foreach ( $pieces as $piece ) {
				$returned_asset                        = $this->get_pattern_id( $piece );
				$pieces_new_pattern[]                  = $returned_asset['pattern'];
				$work_data['content_images'][ $piece ] = $returned_asset;
			}

			$old_param = 'images="' . $matches[2][0] . '"';
			$new_param = 'images="' . implode( ',', $pieces_new_pattern ) . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_vc_images_carousel().


	/**
	 * Find and replace for vc_masonry_media_grid type
	 * search example: //  [vc_masonry_media_grid style="lazy" element_width="3" item="masonryMedia_SolidBlurOut" grid_id="vc_gid:1536058450406-66b7aa60-cc61-9" include="4750,4744,4740,4482,4505,4732,4736,4453,4445,4440,4433,3640,4448,4426,3663,3441,3667,3664,3660,3423,3443,3429,3241,2881,4752,3634,3512,3437,2902,2904,3433,3435,3449"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_vc_masonry_media_grid( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$pieces             = explode( ',', $matches[2][0] );
			$pieces_new_pattern = array();

			foreach ( $pieces as $piece ) {
				$returned_asset                        = $this->get_pattern_id( $piece );
				$pieces_new_pattern[]                  = $returned_asset['pattern'];
				$work_data['content_images'][ $piece ] = $returned_asset;
			}

			$old_param = 'include="' . $matches[2][0] . '"';
			$new_param = 'include="' . implode( ',', $pieces_new_pattern ) . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_vc_masonry_media_grid().


	/**
	 * Find and replace for css_url_in_brackets type
	 * search example: //  // background-image: url(/wp-content/uploads/2020/07/splash1400x1000.png)
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_css_url_in_brackets( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[3][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_url( $matches[1][0] . $matches[2][0] . $matches[3][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = 'url(' . $matches[1][0] . $matches[2][0] . $matches[3][0] . ')';
			$new_param = 'url(' . $returned_asset['pattern'] . ')';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_vc_images_carousel().

	/**
	 * Find and replace for url_in_img_and_a_tag type
	 * search example: //  // <img class="alignnone size-full wp-image-6421" src="http://test.local/wp-content/uploads/2020/07/splash1920x1200.png" alt="" width="1920" height="1280" />
	 * <(a|img)([^\>]+?)(href|src)="([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)"([^\>]*?)>
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_url_in_img_and_a_tag( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			//      1     2      3         4                5            6     7
			//  <(a|img)(.+?)(src|href)="(.*?)(\/wp-content\/uploads\/)(.+?)"(.*?)>

			if ( empty( $matches[3][0] ) || empty( $matches[6][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_url( $matches[4][0] . $matches[5][0] . $matches[6][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[4][0] . $matches[5][0] . $matches[6][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_url_in_img_and_a_tag().


	/**
	 * Find and replace for replace_url_in_css_tag type
	 * search example: //  // [vc_column width="1/3" css=".vc_custom_1519716145703{background-image: url(/wp-content/uploads/2020/07/splash600x600.png?id=8319) !important;background-position: center !important;background-repeat: no-repeat !important;background-size: cover !important;}"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_url_in_css_tag( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[3][0] ) ) {
				continue;
			}

			if ( empty( $matches[4][0] ) ) {
				$matches[4][0] = '';
			}

			$returned_asset = $this->get_pattern_url( $matches[2][0] . $matches[3][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[0][0];
			$new_param = 'url(' . $returned_asset['pattern'] . ')';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_url_in_css_tag().


	/**
	 * Find and replace for et_pb_image type
	 * search example: //  [et_pb_image src="http://gm.local/wp-content/uploads/2020/04/samee-anderson-qa6dwVxR2h0-unsplash.jpeg"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_et_pb_image( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_url( $matches[2][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[2][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_et_pb_image().


	/**
	 * Find and replace for et_pb_blurb type
	 * search example: //  [et_pb_blurb image="http://gm.local/wp-content/uploads/2020/04/image.jpg"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_et_pb_blurb( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_url( $matches[2][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[2][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_et_pb_blurb().


	/**
	 * Find and replace for et_pb_gallery type
	 * search example: //  [et_pb_gallery gallery_ids="77,74,64"]
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_et_pb_gallery( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) ) {
				continue;
			}

			$pieces             = explode( ',', $matches[2][0] );
			$pieces_new_pattern = array();

			foreach ( $pieces as $piece ) {
				$returned_asset                        = $this->get_pattern_id( $piece );
				$pieces_new_pattern[]                  = $returned_asset['pattern'];
				$work_data['content_images'][ $piece ] = $returned_asset;
			}

			$old_param = 'gallery_ids="' . $matches[2][0] . '"';
			$new_param = 'gallery_ids="' . implode( ',', $pieces_new_pattern ) . '"';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_et_pb_gallery().


	/**
	 * Find and replace for clear_srcset type
	 * search example: //        // srcset="http://test.local/wp-content/uploads/2020/05/color-psychology-300x165.png 300w, http://gm.local/wp-content/uploads/2021/05/color-psychology-768x422.png 768w"
	 *  <img([^\>]+?)srcset="([^\"]*?)(\/wp-content\/uploads\/)([^\"]+?)"([^\>]*?)>
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_clear_srcset( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) || empty( $matches[4][0] ) ) {
				continue;
			}

			$old_param = 'srcset="' . $matches[2][0] . $matches[3][0] . $matches[4][0] . '"';
			$new_param = '';

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );

			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_clear_srcset().

	/**
	 * Find and replace for url_in_json_param type
	 * search example: //                // {"url":"http://test.local/wp-content/uploads/2021/05/color-psychology.png","id":10044}
	 * 'url_in_json_param'    => '@[\'\"]{1}url[\'\"]{1}:[\'\"]{1}([^\'\"]*?)(\/wp-content\/uploads\/)([^\'\"]+?)[\'\"]{1}([^\}]*?)\}@im',
	 *
	 *
	 * @param $pattern
	 * @param $content
	 *
	 * @return array
	 */
	public function replace_url_in_json_param( $pattern, $content ) {

		$work_data = array(
			'content_images' => array(),
			'content'        => $content,
		);

		$preg_offset = 0;
		while ( $preg_offset >= 0 ) {

			preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE, $preg_offset );

			if ( ! $matches ) {
				break;
			}

			if ( ! isset( $matches[0] ) || empty( $matches[0][0] ) ) {
				break;
			}

			$preg_offset = end( $matches );
			if ( is_array( $preg_offset ) && isset( $preg_offset[1] ) ) {
				$preg_offset = $preg_offset[1];
			} else {
				break;
			}

			if ( empty( $matches[2][0] ) || empty( $matches[3][0] ) ) {
				continue;
			}

			$returned_asset = $this->get_pattern_url( $matches[1][0] . $matches[2][0] . $matches[3][0] );

			$work_data['content_images'][ $returned_asset['id'] ] = $returned_asset;

			$old_param = $matches[1][0] . $matches[2][0] . $matches[3][0];
			$new_param = $returned_asset['pattern'];

			$new_shortcode = str_replace( $old_param, $new_param, $matches[0][0] );

			$content = str_replace( $matches[0][0], $new_shortcode, $content );

			$len_before = mb_strlen( $matches[0][0] );
			$len_after  = mb_strlen( $new_shortcode );


			if ( $len_before > $len_after ) {
				$preg_offset = $preg_offset - ( $len_before - $len_after );
				if ( $preg_offset < 0 ) {
					$preg_offset = 0;
				}
			}

		} // while.


		$work_data['content'] = $content;

		return $work_data;


	} // public function replace_url_in_json_param().


}
