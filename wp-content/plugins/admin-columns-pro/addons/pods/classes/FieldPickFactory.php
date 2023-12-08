<?php

namespace ACA\Pods;

use ACA\Pods\Field\Pick;
use ACA\Pods\Field\Pick\Capability;
use ACA\Pods\Field\Pick\Comment;
use ACA\Pods\Field\Pick\Country;
use ACA\Pods\Field\Pick\CustomSimple;
use ACA\Pods\Field\Pick\DaysOfWeek;
use ACA\Pods\Field\Pick\ImageSize;
use ACA\Pods\Field\Pick\Media;
use ACA\Pods\Field\Pick\MonthsOfYear;
use ACA\Pods\Field\Pick\NavMenu;
use ACA\Pods\Field\Pick\PostFormat;
use ACA\Pods\Field\Pick\PostStatus;
use ACA\Pods\Field\Pick\PostType;
use ACA\Pods\Field\Pick\Role;
use ACA\Pods\Field\Pick\Taxonomy;
use ACA\Pods\Field\Pick\User;
use ACA\Pods\Field\Pick\UsState;

class FieldPickFactory
{

    public function create(string $type, Column $column): Field\Pick
    {
        switch ($type) {
            case 'capability' :
                return new Capability($column);
            case 'comment' :
                return new Comment($column);
            case 'country' :
                return new Country($column);
            case 'custom-simple' :
                return new CustomSimple($column);
            case 'days_of_week' :
                return new DaysOfWeek($column);
            case 'image-size' :
                return new ImageSize($column);
            case 'media' :
                return new Media($column);
            case 'months_of_year' :
                return new MonthsOfYear($column);
            case 'nav_menu' :
                return new NavMenu($column);
            case 'post_format' :
                return new PostFormat($column);
            case 'post-status' :
                return new PostStatus($column);
            case 'post_type' :
                return new PostType($column);
            case 'role' :
                return new Role($column);
            case 'taxonomy' :
                return new Taxonomy($column);
            case 'user' :
                return new User($column);
            case 'us_state' :
                return new UsState($column);
            default :
                return new Pick($column);
        }
    }

}