<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\Tests\Core\Triggers;

use WP_UnitTestCase;

class NewPublishPostTest extends WP_UnitTestCase
{
    public $insert_data;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testNewPublishPost()
    {
        // Create post object
        $post_id = wp_insert_post(
            array(
                'post_title' => 'sample post',
                'post_content' => 'sample post content',
                'post_status' => 'publish',
                'post_author' => 1,
            )
        );
    }

//    function test_single() {
//        $this->go_to( get_permalink( 1 ) );
//        $this->assertTrue( is_single(), 'This is not a single post page.' );
//        $this->assertTrue( have_posts() );
//    }

    function test_404()
    {
        $this->go_to(site_url('?p=100'));
        $this->assertTrue(is_404());
    }

}