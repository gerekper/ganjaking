<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_links extends WYSIJA_object {

    /**
     * Render a typical link
     * @param type $html_content
     * @param type $link
     * @param string $max_link_length if the link exceeds to max_link_length, cut it off
     * @param string $sub_string_length if the link exceeds to max_link_length, cut it off and keep a substring of $sub_string_length characters
     * @param boolean $is_full_link if true, show full link, if no, show truncated link
     * @param string $default_html if this is defined, we will use this value instead.
     * @return < a > rendered tag
     */
    public function render_link($html_content, $link, $max_link_length = 50, $sub_string_length = 15, $is_full_link = false, $default_html = NULL){
	if (!empty($default_html) && is_string($default_html))
	    return $default_html;
        $link = $link; // not in use
        $helper_licence = WYSIJA::get('licence','helper');
        $url_checkout = $helper_licence->get_url_checkout('count_click_stats');
        $html_content.= str_replace(
                array('[link]','[/link]'),
                array('<a title="'.__('Get Premium now',WYSIJA).'" target="_blank" href="'.$url_checkout.'">','</a>'),
                __("Get [link]MailPoet Premium[/link] to see the link.",WYSIJA));
        return $html_content;
    }

    /**
     * render a link to detailed subsubscriber page, useful for edit or stats
     * @param int $user_id
     * @return full link to detailed subscriber page
     */
    public function detailed_subscriber($user_id){
        return 'admin.php?page=wysija_subscribers&id='.(int)$user_id.'&action=edit';
    }
}