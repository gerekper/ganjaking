<?php
namespace memberpress\courses\lib;
use memberpress\courses\helpers as helpers;
use memberpress\courses\models as models;

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

global $wp_rewrite;
$lessons = '/' . helpers\Courses::get_permalink_base() . '/%course_slug%/' . models\Lesson::$permalink_slug;
$wp_rewrite->add_rewrite_tag( "%course_slug%", '([^/]+)', "course=" );
$wp_rewrite->add_permastruct( models\Lesson::$permalink_slug, $lessons, false );
delete_option( 'mepr_courses_flushed_rewrite_rules' );
