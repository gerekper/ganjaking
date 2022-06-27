<?php

defined('ABSPATH') || exit;

class UP_UserPosts
{
    /**
     * UserPro user Object.
     * @since 4.9.31
     * @var UP_User
     */
    protected $user;

    /**
     * UP_UserPosts constructor.
     * @param UP_User $user
     */
    public function __construct(UP_User $user)
    {
        $this->user = $user;
    }

    /**
     * Get User posted posts, default posts per page 5.
     *
     * @param null $page
     * @since 4.9.31
     * @return WP_Query
     */
    public function getPosts($page = null)
    {
        $paged = (isset($page)) ? $page : 1;
        $args = array(
            'author' => $this->user->getUserId(),
            'orderby' => 'post_date',
            'order' => 'ASC',
            'posts_per_page' => 5,
            'paged' => $paged,
            'post_status' => array('publish', 'pending')
        );
        $query = new WP_Query($args);

        return $query;
    }

    /**
     * User Post pagination.
     * @param $pages
     * @since 4.9.31
     * @return string
     */
    public function pagination($pages)
    {
        $pagination = '<ul data-user-id="' . $this->user->getUserId() . '" class="up-pagination">';
        for ($i = 1; $pages >= $i; $i++) {
            if ($i === 1) {
                $class = 'active';
            } else {
                $class = '';
            }
            $pagination .= '<li class="' . $class . '" data-page="' . $i . '">' . $i . '</li>';
        }
        $pagination .= '</ul>';

        return $pagination;
    }
}