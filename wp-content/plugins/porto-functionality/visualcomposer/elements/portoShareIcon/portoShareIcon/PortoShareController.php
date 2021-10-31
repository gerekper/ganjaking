<?php
namespace porto\portoShareIcon;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoShareController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'VCV_PORTO_SHARE_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoShareIcon',
				'getShareUrl'
			);
			define( 'VCV_PORTO_SHARE_CONTROLLER', true );
		}
	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getShareUrl( $variables, $payload ) {
		$permalink   = get_the_permalink();
		$title       = get_the_title();
		$excerpt     = get_the_excerpt();
		$image       = wp_get_attachment_url( get_post_thumbnail_id() );
		$share_icons = array(
			'facebook'  => 'https://www.facebook.com/sharer.php?u=' . $permalink,
			'twitter'   => 'https://twitter.com/intent/tweet?text=' . $title . '&amp;url=' . $permalink,
			'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . $permalink . '&amp;title=' . $title,
			'email'     => 'mailto:?subject=$title&amp;body=' . $permalink,
			'google'    => 'https://plus.google.com/share?url=' . $permalink,
			'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . $permalink . '&amp;media=' . $image,
			'reddit'    => 'http://www.reddit.com/submit?url=' . $permalink . '&amp;title=' . $title,
			'tumblr'    => 'http://www.tumblr.com/share/link?url=' . $permalink . '&amp;name=' . $title . '&amp;description=' . $excerpt,
			'vk'        => 'https://vk.com/share.php?url=' . $permalink . '&amp;title=' . $title . '&amp;image=' . $image . '&amp;noparse=true',
			'whatsapp'  => 'whatsapp://send?text=$title - ' . $permalink,
			'xing'      => 'https://www.xing-share.com/app/user?op=share;sc_p=xing-share;url=' . $permalink,
		);
		$variables[] = array(
			'key'   => 'portoShareUrl',
			'value' => $share_icons,
		);

		return $variables;
	}
}
