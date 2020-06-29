<?php
/** @var array $posts data */
/** @var array $template template  */
/** @var string $images_link link */
/** @var string $account account */
/** @var string $orderby order */

$account = isset($args['account']) ? $args['account'] : array();
$template = isset($args['template']) ? $args['template'] : '';
$images_link = isset($args['images_link']) ? $args['images_link'] : '';
$posts = isset($args['posts']) ? $args['posts'] : array();

foreach ( $posts as $post ) {
    echo "<img src='{$post['full_picture']}' alt=''><br>";
}
